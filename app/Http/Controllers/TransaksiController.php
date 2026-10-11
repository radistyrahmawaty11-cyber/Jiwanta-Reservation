<?php

namespace App\Http\Controllers;

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
}
