<?php

namespace App\Http\Controllers;

use App\Events\NewOrderPlaced;
use App\Models\Bank;
use App\Models\Cabin;
use App\Models\Detailtransaksi;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengunjungController extends Controller
{
    public function dashboard()
    {
        return view('pengunjung.dashboard', [
            'tikets' => Tiket::orderBy('kategori')->orderBy('id')->get(),
            'cabins' => Cabin::orderBy('id')->get(),
        ]);
    }

    public function feed()
    {
        return response()->json([
            'ok' => true,
            'data' => [
                'tikets' => Tiket::orderBy('id')->get(['id', 'stok'])->map(fn ($t) => [
                    'id' => (int) $t->id,
                    'stok' => (int) $t->stok,
                ]),
                'cabins' => Cabin::orderBy('id')->get(['id', 'status'])->map(fn ($c) => [
                    'id' => (int) $c->id,
                    'status' => $c->status,
                ]),
            ],
        ]);
    }

    public function reservasi(Request $request)
    {
        return view('pengunjung.reservasi', [
            'tab' => $request->input('tab') === 'cabin' ? 'cabin' : 'tiket',
            'tikets' => Tiket::orderBy('id')->get(),
            'cabins' => Cabin::orderBy('id')->get(),
        ]);
    }

    public function availability()
    {
        $cabins = Cabin::orderBy('id')->get()->map(fn ($c) => [
            'key' => $c->id,
            'sold' => $c->status === 'terisi',
        ])->values();

        return response()->json(['ok' => true, 'data' => $cabins]);
    }

    public function transaksi(Request $request)
    {
        $order = $request->session()->get('order');

        return view('pengunjung.transaksi', [
            'order' => $order,
            'expiredAt' => $order ? now()->addHours(2) : null,
            'user' => $request->user(),
            'banks' => Bank::where('status', true)->orderBy('nama_bank')->get(),
        ]);
    }

    public function tiket(Request $request)
    {
        $order = $request->session()->get('order');

        $transaksis = Transaksi::with([
            'detailtransaksis.tiket',
            'detailtransaksis.reservasi.cabin',
            'detailtransaksis.reservasi.renang',
        ])
            ->where('pengguna_id', $request->user()->id)
            ->orderByDesc('tanggal_transaksi')
            ->get();

        return view('pengunjung.tiket', [
            'order' => $order,
            'transaksis' => $transaksis,
        ]);
    }

    public function checkout(Request $request)
    {
        $items = $this->readItems($request);

        if ($items !== []) {
            return $this->checkoutItems($request, $items);
        }

        return $this->checkoutSingle($request);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function checkoutItems(Request $request, array $items)
    {
        $user = $request->user();
        $kode = $this->kodeTransaksi();
        $checkin = Carbon::parse($request->input('tanggal', now()->toDateString()));

        $lines = [];
        $total = 0;
        $pax = 0;
        $titles = [];
        $hasTiket = false;
        $hasCabin = false;

        foreach ($items as $item) {
            if ($item['tipe'] === 'cabin') {
                $cabin = Cabin::find($item['cabin_id']);
                if (! $cabin) {
                    continue;
                }

                $malam = (int) $item['malam'];
                $harga = $item['harga_satuan'] > 0 ? (int) $item['harga_satuan'] : (int) $cabin->harga_per_malam;
                $subtotal = $harga * $malam;

                $lines[] = ['tipe' => 'cabin', 'cabin' => $cabin, 'jumlah' => $malam, 'harga' => $harga, 'subtotal' => $subtotal];
                $total += $subtotal;
                $pax += (int) $cabin->kapasitas;
                $titles[] = 'Cabin '.$cabin->nama_cabin;
                $hasCabin = true;
            } else {
                $tiket = Tiket::find($item['tiket_id']);
                if (! $tiket) {
                    continue;
                }

                $jumlah = (int) $item['jumlah'];
                $harga = $item['harga_satuan'] > 0 ? (int) $item['harga_satuan'] : (int) $tiket->harga;
                $subtotal = $harga * $jumlah;

                $lines[] = ['tipe' => 'tiket', 'tiket' => $tiket, 'jumlah' => $jumlah, 'harga' => $harga, 'subtotal' => $subtotal];
                $total += $subtotal;
                $pax += $jumlah;
                $titles[] = $tiket->nama_tiket;
                $hasTiket = true;
            }
        }

        if ($lines === []) {
            return redirect()->route('reservasi');
        }

        $transaksi = Transaksi::create([
            'pengguna_id' => $user->id,
            'kode_transaksi' => $kode,
            'total_amount' => $total,
            'total_bayar' => $total,
            'kembalian' => 0,
            'metode_pembayaran' => 'Transfer',
            'status' => 'pending',
            'tanggal_transaksi' => now(),
        ]);

        $orderItems = [];

        foreach ($lines as $line) {
            if ($line['tipe'] === 'cabin') {
                $cabin = $line['cabin'];

                $reservasi = Reservasi::create([
                    'pengguna_id' => $user->id,
                    'cabin_id' => $cabin->id,
                    'renang_id' => null,
                    'tanggal_checkin' => $checkin->toDateString(),
                    'tanggal_checkout' => $checkin->copy()->addDays($line['jumlah'])->toDateString(),
                    'jumlah_tamu' => $cabin->kapasitas ?? 4,
                    'total_harga' => $line['subtotal'],
                    'status' => 'pending',
                ]);

                Detailtransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'reservasi_id' => $reservasi->id,
                    'jumlah' => $line['jumlah'],
                    'harga_satuan' => $line['harga'],
                    'subtotal' => $line['subtotal'],
                ]);

                $orderItems[] = [
                    'tipe' => 'cabin',
                    'nama' => 'Cabin '.$cabin->nama_cabin,
                    'jumlah' => $line['jumlah'],
                    'satuan' => $line['harga'],
                    'subtotal' => $line['subtotal'],
                    'keterangan' => $line['jumlah'].' malam',
                ];
            } else {
                $tiket = $line['tiket'];

                Detailtransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'tiket_id' => $tiket->id,
                    'jumlah' => $line['jumlah'],
                    'harga_satuan' => $line['harga'],
                    'subtotal' => $line['subtotal'],
                ]);

                $orderItems[] = [
                    'tipe' => 'tiket',
                    'nama' => $tiket->nama_tiket,
                    'jumlah' => $line['jumlah'],
                    'satuan' => $line['harga'],
                    'subtotal' => $line['subtotal'],
                    'keterangan' => $line['jumlah'].' tiket',
                ];
            }
        }

        $tipe = $hasCabin && $hasTiket ? 'mixed' : ($hasCabin ? 'cabin' : 'tiket');

        $order = [
            'tipe' => $tipe,
            'judul' => count($titles) === 1 ? $titles[0] : count($titles).' Item Reservasi',
            'badge' => match ($tipe) {
                'cabin' => 'Cabin Suite',
                'tiket' => 'Tiket Masuk & Kolam',
                default => 'Pesanan Gabungan',
            },
            'tamu' => $pax.' Pengunjung',
            'harga_label' => 'Rincian Item',
            'harga_value' => count($orderItems).' item',
            'subtotal_label' => 'Subtotal',
            'total' => $total,
            'tanggal' => $checkin->toDateString(),
            'jumlah' => $pax,
            'kode' => $kode,
            'status' => 'belum',
            'items' => $orderItems,
            'transaksi_id' => $transaksi->id,
        ];

        $request->session()->put('order', $order);

        return redirect()->route('transaksi');
    }

    private function checkoutSingle(Request $request)
    {
        $tipe = $request->input('tipe', 'tiket') === 'cabin' ? 'cabin' : 'tiket';
        $total = (int) $request->input('total', 0);
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kode = $this->kodeTransaksi();

        if ($tipe === 'cabin') {
            $cabin = Cabin::find($request->input('unit'));
            $malam = max(1, (int) $request->input('malam', 1));
            $harga = $malam > 0 ? (int) round($total / $malam) : $total;

            $order = [
                'tipe' => 'cabin',
                'judul' => 'Cabin '.($cabin?->nama_cabin ?? ucfirst((string) $request->input('unit'))),
                'badge' => 'Cabin Suite',
                'tamu' => ($cabin?->kapasitas ?? 4).' Tamu Dewasa',
                'harga_label' => 'Harga per Malam',
                'harga_value' => 'Rp '.number_format($harga, 0, ',', '.'),
                'subtotal_label' => 'Subtotal Cabin',
                'total' => $total,
                'tanggal' => $tanggal,
                'unit' => $cabin?->id ?? $request->input('unit'),
                'malam' => $malam,
                'kode' => $kode,
                'status' => 'belum',
            ];

            $checkin = Carbon::parse($tanggal);

            $reservasi = Reservasi::create([
                'pengguna_id' => $request->user()->id,
                'cabin_id' => $cabin?->id,
                'renang_id' => null,
                'tanggal_checkin' => $checkin->toDateString(),
                'tanggal_checkout' => $checkin->copy()->addDays($malam)->toDateString(),
                'jumlah_tamu' => $cabin?->kapasitas ?? 4,
                'total_harga' => $total,
                'status' => 'pending',
            ]);

            $detail = [
                'reservasi_id' => $reservasi->id,
                'harga_satuan' => $harga,
                'jumlah' => $malam,
                'subtotal' => $total,
            ];
        } else {
            $tiket = $this->resolveTiket($request);
            $jumlah = max(1, (int) $request->input('jumlah', 1));
            $harga = $tiket?->harga ? (int) $tiket->harga : ($jumlah > 0 ? (int) round($total / $jumlah) : $total);

            $order = [
                'tipe' => 'tiket',
                'judul' => $tiket?->nama_tiket ?? 'Tiket Renang',
                'badge' => 'Tiket Masuk & Kolam',
                'tamu' => $jumlah.' Pengunjung Dewasa',
                'harga_label' => 'Harga Satuan (x'.$jumlah.')',
                'harga_value' => 'Rp '.number_format($harga, 0, ',', '.').' x '.$jumlah,
                'subtotal_label' => 'Subtotal Tiket',
                'total' => $total,
                'tanggal' => $tanggal,
                'kategori' => $tiket?->id ?? $request->input('kategori'),
                'jumlah' => $jumlah,
                'kode' => $kode,
                'status' => 'belum',
            ];

            $detail = [
                'tiket_id' => $tiket?->id,
                'harga_satuan' => $harga,
                'jumlah' => $jumlah,
                'subtotal' => $total,
            ];
        }

        $transaksi = Transaksi::create([
            'pengguna_id' => $request->user()->id,
            'kode_transaksi' => $kode,
            'total_amount' => $total,
            'total_bayar' => $total,
            'kembalian' => 0,
            'metode_pembayaran' => 'Transfer',
            'status' => 'pending',
            'tanggal_transaksi' => now(),
        ]);

        Detailtransaksi::create($detail + ['transaksi_id' => $transaksi->id]);

        $order['transaksi_id'] = $transaksi->id;

        $request->session()->put('order', $order);

        return redirect()->route('transaksi');
    }

    public function kirim(Request $request)
    {
        $order = $request->session()->get('order', []);

        $buktiPath = null;
        $buktiNama = $request->input('bukti') ?: null;

        if ($request->hasFile('bukti')) {
            $request->validate([
                'bukti' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $file = $request->file('bukti');
            $buktiNama = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
            $base = Str::slug(pathinfo($buktiNama, PATHINFO_FILENAME)) ?: 'bukti';
            $buktiPath = $file->storeAs('bukti-transfer', Str::random(8).'_'.$base.'.'.$ext, 'public');
        }

        $order['status'] = 'terkirim';
        $order['bukti_nama'] = $buktiNama ?: 'bukti_transfer.jpg';
        if ($buktiPath) {
            $order['bukti_path'] = $buktiPath;
            $order['bukti_url'] = Storage::url($buktiPath);
        }
        $order['dikirim_at'] = now()->toDateTimeString();
        $order['pemesan'] = $request->user()->nama_pengguna;

        $request->session()->put('order', $order);

        if ($buktiPath) {
            $query = Transaksi::query();
            if (! empty($order['transaksi_id'])) {
                $query->where('id', $order['transaksi_id']);
            } elseif (! empty($order['kode'])) {
                $query->where('kode_transaksi', $order['kode']);
            } else {
                $query->whereRaw('1 = 0');
            }
            $query->update(['bukti_transfer' => $buktiPath]);
        }

        try {
            NewOrderPlaced::dispatch($order);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json(['url' => route('tiket')]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readItems(Request $request): array
    {
        $raw = $request->input('items');
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        if (! is_array($decoded)) {
            return [];
        }

        $items = [];

        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }

            if (($row['tipe'] ?? '') === 'cabin') {
                $cabinId = (int) ($row['cabin_id'] ?? 0);
                if ($cabinId <= 0) {
                    continue;
                }

                $items[] = [
                    'tipe' => 'cabin',
                    'cabin_id' => $cabinId,
                    'malam' => max(1, (int) ($row['malam'] ?? 1)),
                    'harga_satuan' => max(0, (int) ($row['harga_satuan'] ?? 0)),
                ];

                continue;
            }

            $tiketId = (int) ($row['tiket_id'] ?? 0);
            if ($tiketId <= 0) {
                continue;
            }

            $items[] = [
                'tipe' => 'tiket',
                'tiket_id' => $tiketId,
                'jumlah' => max(1, (int) ($row['jumlah'] ?? 1)),
                'harga_satuan' => max(0, (int) ($row['harga_satuan'] ?? 0)),
            ];
        }

        return $items;
    }

    private function resolveTiket(Request $request): ?Tiket
    {
        $key = $request->input('kategori');

        if (is_numeric($key)) {
            $tiket = Tiket::find((int) $key);
            if ($tiket) {
                return $tiket;
            }
        }

        if (is_string($key) && $key !== '') {
            $tiket = Tiket::where('nama_tiket', 'like', '%'.$key.'%')->first();
            if ($tiket) {
                return $tiket;
            }
        }

        return Tiket::orderBy('id')->first();
    }

    private function kodeTransaksi(): string
    {
        do {
            $kode = 'TRX-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Transaksi::where('kode_transaksi', $kode)->exists());

        return $kode;
    }
}
