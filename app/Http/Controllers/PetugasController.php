<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Bank;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PetugasController extends Controller
{
    private const PAX_CAP = 250;

    private const EAGER = [
        'pengguna',
        'detailtransaksis.tiket',
        'detailtransaksis.reservasi.cabin',
        'detailtransaksis.reservasi.renang',
    ];

    private ?Collection $banks = null;

    public function index()
    {
        return view('petugas.dashboard', [
            'payload' => $this->payload(),
        ]);
    }

    public function feed()
    {
        return response()->json($this->payload());
    }

    public function verify(Transaksi $transaksi)
    {
        $transaksi->status = 'lunas';
        $transaksi->verified_at ??= now();
        $transaksi->save();

        $this->broadcastStatus($transaksi);

        return response()->json(['ok' => true, 'status' => $transaksi->status]);
    }

    public function reject(Transaksi $transaksi)
    {
        $transaksi->status = 'gagal';
        $transaksi->save();

        $this->broadcastStatus($transaksi);

        return response()->json(['ok' => true, 'status' => $transaksi->status]);
    }

    public function checkIn(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'lunas') {
            return response()->json([
                'ok' => false,
                'reason' => $transaksi->status === 'gagal' ? 'rejected' : 'pending',
            ], 422);
        }

        if ($transaksi->checked_in_at !== null) {
            return response()->json([
                'ok' => false,
                'reason' => 'used',
                'checked_in_at' => $transaksi->checked_in_at->toIso8601String(),
            ], 422);
        }

        $visitDate = $this->visitDate($transaksi);

        if ($visitDate !== null && $visitDate->lt(Carbon::now()->startOfDay())) {
            return response()->json([
                'ok' => false,
                'reason' => 'expired',
                'visit_date' => $visitDate->toDateString(),
            ], 422);
        }

        $transaksi->checked_in_at = now();
        $transaksi->verified_at ??= now();
        $transaksi->save();

        $this->broadcastStatus($transaksi);

        return response()->json([
            'ok' => true,
            'status' => $transaksi->status,
            'checked_in_at' => $transaksi->checked_in_at->toIso8601String(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        $pending = Transaksi::with(self::EAGER)
            ->where('status', 'pending')
            ->orderBy('tanggal_transaksi')
            ->get()
            ->map(fn (Transaksi $t) => $this->present($t))
            ->values();

        $history = Transaksi::with('pengguna')
            ->whereIn('status', ['lunas', 'gagal'])
            ->orderByDesc('tanggal_transaksi')
            ->take(5)
            ->get()
            ->map(fn (Transaksi $t) => [
                't' => $t->tanggal_transaksi?->format('H:i:s') ?? '--:--:--',
                'name' => $t->pengguna?->nama_pengguna ?? 'Pengguna',
                'sub' => $t->kode_transaksi.' • '.$t->metode_pembayaran,
                'ok' => $t->status === 'lunas',
            ])
            ->values();

        $verifiedToday = Transaksi::where('status', 'lunas')
            ->whereDate('tanggal_transaksi', today())
            ->count();

        $gateToday = (int) Transaksi::with('detailtransaksis')
            ->where('status', 'lunas')
            ->whereDate('tanggal_transaksi', today())
            ->get()
            ->sum(fn (Transaksi $t) => $this->pax($t));

        return [
            'stats' => [
                'gate' => $gateToday,
                'cap' => self::PAX_CAP,
                'verified' => $verifiedToday,
            ],
            'pending' => $pending,
            'history' => $history,
            'scanUrl' => route('petugas.scan'),
            'actionUrl' => url('petugas/transaksi'),
            'feedUrl' => route('petugas.feed'),
        ];
    }

    private function broadcastStatus(Transaksi $transaksi): void
    {
        if (! $transaksi->pengguna_id) {
            return;
        }

        try {
            OrderStatusUpdated::dispatch(
                (int) $transaksi->pengguna_id,
                (string) $transaksi->kode_transaksi,
                (string) $transaksi->status,
                $transaksi->checked_in_at !== null,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function scan(Request $request)
    {
        $code = strtoupper(trim((string) $request->input('code')));

        $transaksi = Transaksi::with(self::EAGER)
            ->whereRaw('UPPER(kode_transaksi) = ?', [$code])
            ->first();

        // Barcode tiket memuat kode tanpa prefiks "TRX-" (lebih mudah discan).
        if (! $transaksi && preg_match('/^\d{8}-\d{3}$/', $code)) {
            $transaksi = Transaksi::with(self::EAGER)
                ->whereRaw('UPPER(kode_transaksi) = ?', ['TRX-'.$code])
                ->first();
        }

        if (! $transaksi) {
            return response()->json(['ok' => false, 'reason' => 'not_found']);
        }

        $tiket = $this->present($transaksi);

        $reason = match ($transaksi->status) {
            'pending' => 'pending',
            'gagal' => 'rejected',
            default => $transaksi->checked_in_at !== null
                ? 'used'
                : ($this->isExpired($transaksi) ? 'expired' : null),
        };

        return response()->json([
            'ok' => $reason === null,
            'reason' => $reason,
            'transaksi' => $tiket,
        ]);
    }

    /**
     * Tanggal kunjungan: reservasi (check-in) bila ada, else tanggal transaksi.
     */
    private function visitDate(Transaksi $t): ?Carbon
    {
        $detail = $t->detailtransaksis->first();
        $raw = $detail?->reservasi?->tanggal_checkin ?? $t->tanggal_transaksi?->toDateString();

        return $raw ? Carbon::parse($raw)->startOfDay() : null;
    }

    private function isExpired(Transaksi $t): bool
    {
        $visit = $this->visitDate($t);

        return $visit !== null && $visit->lt(Carbon::now()->startOfDay());
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Transaksi $t): array
    {
        $detail = $t->detailtransaksis->first();

        $pkg = 'Tiket Renang';

        if ($detail?->tiket) {
            $pkg = $detail->tiket->nama_tiket;
        } elseif ($detail?->reservasi) {
            $reservasi = $detail->reservasi;
            $pkg = $reservasi->cabin?->nama_cabin
                ? 'Cabin '.$reservasi->cabin->nama_cabin
                : ($reservasi->renang?->nama_kolam ?? 'Reservasi');
        }

        return [
            'id' => $t->id,
            'code' => $t->kode_transaksi,
            'name' => $t->pengguna?->nama_pengguna ?? 'Pengguna',
            'pax' => $this->pax($t),
            'pkg' => $pkg,
            'total' => (int) $t->total_amount,
            'paid' => (int) ($t->total_bayar ?: $t->total_amount),
            'bank' => $t->metode_pembayaran,
            'rek' => $this->rekening($t->metode_pembayaran),
            'uploaded' => $t->tanggal_transaksi?->getTimestampMs() ?? now()->getTimestampMs(),
            'date' => $this->visitDate($t)?->translatedFormat('l, d M Y') ?? '-',
            'visit_date' => $this->visitDate($t)?->toDateString(),
            'file' => 'bukti_'.$t->kode_transaksi.'.jpg',
            'status' => $t->status,
            'verified' => ! is_null($t->verified_at),
            'checked_in_at' => $t->checked_in_at?->toIso8601String(),
        ];
    }

    private function pax(Transaksi $t): int
    {
        return max(1, (int) $t->detailtransaksis->sum('jumlah'));
    }

    private function rekening(string $metode): string
    {
        $this->banks ??= Bank::all();

        $bank = $this->banks->first(
            fn ($bank) => stripos($bank->nama_bank, $metode) !== false
        );

        return $bank?->nomor_rekening ?? '-';
    }
}
