<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    public function index()
    {
        $all = Transaksi::with('pengguna')->orderByDesc('tgl_transaksi')->get();

        $transaksis = $all->map(fn (Transaksi $t) => $this->present($t))->values()->all();

        $stats = [
            'today' => $all->filter(fn (Transaksi $t) => optional($t->tgl_transaksi)->isToday())->count(),
            'omzet' => (float) $all->filter(fn (Transaksi $t) => optional($t->tgl_transaksi)->isToday())->sum('total_bayar'),
            'pending' => $all->where('status', 'pending')->count(),
        ];

        $penggunas = Pengguna::orderBy('nama_pengguna')->get(['id', 'nama_pengguna', 'email']);

        return view('admin.transaksi', compact('transaksis', 'stats', 'penggunas'));
    }

    public function store(Request $request)
    {
        $transaksi = Transaksi::create($this->validated($request));

        return response()->json(['ok' => true, 'data' => $this->present($transaksi->fresh('pengguna'))]);
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $transaksi->update($this->validated($request, $transaksi));

        return response()->json(['ok' => true, 'data' => $this->present($transaksi->fresh('pengguna'))]);
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return response()->json(['ok' => true]);
    }

    public function export()
    {
        $rows = Transaksi::with('pengguna')->orderByDesc('tgl_transaksi')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Kode Transaksi', 'Pengguna', 'Tanggal', 'Total Bayar', 'Jumlah Bayar', 'Kembalian', 'Metode', 'Jenis', 'Status']);
            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->kode_transaksi,
                    $t->pengguna?->nama_pengguna,
                    optional($t->tgl_transaksi)->format('Y-m-d H:i'),
                    $t->total_bayar,
                    $t->jumlah_bayar,
                    $t->kembalian,
                    strtoupper($t->metode),
                    $t->jenis_transaksi,
                    $t->status,
                ]);
            }
            fclose($out);
        }, 'riwayat-transaksi-'.date('Ymd').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function validated(Request $request, ?Transaksi $transaksi = null): array
    {
        return $request->validate([
            'kode_transaksi' => ['required', 'string', 'max:50', Rule::unique('transaksis', 'kode_transaksi')->ignore($transaksi?->id)],
            'pengguna_id' => ['required', 'exists:penggunas,id'],
            'tgl_transaksi' => ['required', 'date'],
            'total_bayar' => ['required', 'numeric', 'min:0'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'kembalian' => ['nullable', 'numeric', 'min:0'],
            'metode' => ['required', Rule::in(['bca', 'mandiri'])],
            'jenis_transaksi' => ['required', Rule::in(['pembayaran_renang', 'pembayaran_cabin'])],
            'status' => ['required', Rule::in(['pending', 'lunas', 'gagal'])],
            'bukti' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function present(Transaksi $t): array
    {
        return [
            'id' => (string) $t->id,
            'kode' => $t->kode_transaksi,
            'pengguna_id' => (string) $t->pengguna_id,
            'name' => $t->pengguna?->nama_pengguna ?? '-',
            'unit' => $t->jenis_transaksi === 'pembayaran_cabin' ? 'Pembayaran Cabin' : 'Pembayaran Renang',
            'jenis' => $t->jenis_transaksi,
            'metode' => $t->metode === 'bca' ? 'BCA' : 'Mandiri',
            'metode_raw' => $t->metode,
            'amount' => (float) $t->total_bayar,
            'total_bayar' => (float) $t->total_bayar,
            'jumlah_bayar' => (float) $t->jumlah_bayar,
            'kembalian' => (float) $t->kembalian,
            'status' => $t->status,
            'bukti' => $t->bukti,
            'datetime' => optional($t->tgl_transaksi)->format('Y-m-d\TH:i'),
            't' => optional($t->tgl_transaksi)->format('H:i'),
            'date' => optional($t->tgl_transaksi)->format('d M Y'),
            'today' => (bool) optional($t->tgl_transaksi)->isToday(),
        ];
    }
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB untuk Database Transaction
use App\Models\Transaksi;
use App\Models\Detailtransaksi;
use App\Models\Tiket;

class TransaksiController extends Controller
{
    // 1. Menampilkan semua data transaksi
    public function index()
    {
        // Ambil data transaksi, beserta data pengguna dan detail tiketnya (Eager Loading)
        $transaksis = Transaksi::with(['pengguna', 'detailTransaksis.tiket'])->get();

        return view('admin.transaksi', compact('transaksis'));
    }

    // 2. Menyimpan Transaksi Baru (Induk + Detail + Hitung Total)
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'pengguna_id' => 'required|exists:penggunas,id',
            'metode'      => 'required|string',
            'items'       => 'required|array|min:1', // items adalah array berisi daftar tiket yang dibeli
            'items.*.tiket_id' => 'required|exists:tikets,id',
            'items.*.jumlah'   => 'required|integer|min:1',
        ]);

        // Mulai Database Transaction (Pro Tip: Biar data konsisten)
        DB::beginTransaction();
        try {
            $totalBayar = 0;

            // A. Buat Transaksi Induk (Total bayar diisi 0 dulu)
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . time(), // Buat kode unik sederhana
                'pengguna_id'    => $request->pengguna_id,
                'tgl_transaksi'  => now(),
                'total_bayar'    => 0,
                'jumlah_bayar'   => $request->jumlah_bayar ?? 0,
                'kembalian'      => 0,
                'metode'         => $request->metode,
                'jenis_transaksi'=> 'tiket',
                'status'         => 'berhasil',
            ]);

            // B. Looping untuk simpan Detail Transaksi & Hitung Subtotal
            foreach ($request->items as $item) {
                $tiket = Tiket::find($item['tiket_id']);
                $subtotal = $tiket->harga * $item['jumlah'];
                $totalBayar += $subtotal; // Tambahkan ke total

                Detailtransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'tiket_id'     => $item['tiket_id'],
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $tiket->harga,
                    'subtotal'     => $subtotal,
                ]);
            }

            // C. Update Total Bayar & Kembalian di Transaksi Induk
            $kembalian = ($request->jumlah_bayar ?? 0) - $totalBayar;
            $transaksi->update([
                'total_bayar' => $totalBayar,
                'kembalian'   => $kembalian
            ]);

            // Jika semua sukses, commit (simpan permanen ke database)
            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            // Jika ada error, rollBack (batalkan semua, database tetap bersih)
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    // (Fungsi create, show, edit, update, destroy biarkan kosong dulu)
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
}
