<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    /**
     * Helper untuk memformat data agar cocok dengan frontend JS
     */
    private function formatTransaksi($trx)
    {
        $date = Carbon::parse($trx->tanggal_transaksi);
        $total = (int) ($trx->total_bayar ?? $trx->total_amount ?? 0);

        return [
            'id'            => (int) $trx->id,
            't'             => $date->format('H:i'),
            'date'          => $date->format('d M Y'),
            'datetime'      => $date->format('Y-m-d\TH:i'),
            'kode'          => $trx->kode_transaksi,
            'name'          => $trx->pengguna ? $trx->pengguna->nama_pengguna : 'Pengguna Umum',
            'unit'          => 'Transaksi',
            'metode'        => strtoupper($trx->metode_pembayaran ?? 'TUNAI'),
            'metode_raw'    => strtolower($trx->metode_pembayaran ?? 'tunai'),
            'amount'        => $total,
            'status'        => $trx->status,
            'pengguna_id'   => (int) $trx->pengguna_id,
            'total_bayar'   => $total,
            'jumlah_bayar'  => $total,
            'kembalian'     => (int) ($trx->kembalian ?? 0),
            'bukti'         => '-',
            'today'         => $date->isToday(),
        ];
    }

    public function index()
    {
        $penggunas = Pengguna::orderBy('nama_pengguna')->get();

        $transaksis = Transaksi::with('pengguna')->orderBy('tanggal_transaksi', 'desc')->get();

        $today = Carbon::today();
        $stats = [
            'today'   => Transaksi::whereDate('tanggal_transaksi', $today)->count(),
            'omzet'   => Transaksi::whereDate('tanggal_transaksi', $today)->where('status', 'lunas')->sum('total_bayar'),
            'pending' => Transaksi::where('status', 'pending')->count(),
        ];

        $formattedTransaksis = $transaksis->map(function($trx) {
            return $this->formatTransaksi($trx);
        })->values();

        return view('admin.transaksi', [
            'stats' => $stats,
            'penggunas' => $penggunas,
            'transaksis' => $formattedTransaksis
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_transaksi' => 'required|string|unique:transaksis,kode_transaksi',
            'pengguna_id'    => 'required|exists:penggunas,id',
            'tgl_transaksi'  => 'required|date',
            'total_bayar'    => 'required|numeric|min:0',
            'kembalian'      => 'required|numeric|min:0',
            'metode'         => 'required|string',
            'status'         => 'required|in:pending,lunas,gagal',
        ]);

        $trx = Transaksi::create([
            'kode_transaksi'    => $validated['kode_transaksi'],
            'pengguna_id'       => $validated['pengguna_id'],
            'tanggal_transaksi' => $validated['tgl_transaksi'],
            'total_amount'      => $validated['total_bayar'],
            'total_bayar'       => $validated['total_bayar'],
            'kembalian'         => $validated['kembalian'],
            'metode_pembayaran' => strtoupper($validated['metode']),
            'status'            => $validated['status'],
        ]);

        return response()->json([
            'ok' => true,
            'data' => $this->formatTransaksi($trx)
        ]);
    }

    public function update(Request $request, $id)
    {
        $trx = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'kode_transaksi' => 'required|string|unique:transaksis,kode_transaksi,' . $id,
            'pengguna_id'    => 'required|exists:penggunas,id',
            'tgl_transaksi'  => 'required|date',
            'total_bayar'    => 'required|numeric|min:0',
            'kembalian'      => 'required|numeric|min:0',
            'metode'         => 'required|string',
            'status'         => 'required|in:pending,lunas,gagal',
        ]);

        $trx->update([
            'kode_transaksi'    => $validated['kode_transaksi'],
            'pengguna_id'       => $validated['pengguna_id'],
            'tanggal_transaksi' => $validated['tgl_transaksi'],
            'total_amount'      => $validated['total_bayar'],
            'total_bayar'       => $validated['total_bayar'],
            'kembalian'         => $validated['kembalian'],
            'metode_pembayaran' => strtoupper($validated['metode']),
            'status'            => $validated['status'],
        ]);

        $trx->refresh();

        return response()->json([
            'ok' => true,
            'data' => $this->formatTransaksi($trx)
        ]);
    }

    public function destroy($id)
    {
        $trx = Transaksi::findOrFail($id);
        $trx->delete();

        return response()->json(['ok' => true, 'message' => 'Transaksi berhasil dihapus']);
    }

    public function export()
    {
        $transaksis = Transaksi::with('pengguna')->orderBy('tanggal_transaksi', 'desc')->get();

        $filename = "transaksi_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($transaksis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode', 'Tanggal', 'Pengguna', 'Metode', 'Total', 'Status']);

            foreach ($transaksis as $trx) {
                fputcsv($file, [
                    $trx->kode_transaksi,
                    $trx->tanggal_transaksi,
                    $trx->pengguna ? $trx->pengguna->nama_pengguna : 'Umum',
                    $trx->metode_pembayaran,
                    $trx->total_bayar ?? $trx->total_amount,
                    $trx->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
