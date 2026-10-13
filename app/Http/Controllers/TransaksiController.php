<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use App\Models\Transaksi;
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

    private function stats(): array
    {
        $today = Carbon::today();

        return [
            'today'       => Transaksi::whereDate('tanggal_transaksi', $today)->count(),
            'omzet'       => (int) Transaksi::whereDate('tanggal_transaksi', $today)->where('status', 'lunas')->sum('total_bayar'),
            'pending'     => Transaksi::where('status', 'pending')->count(),
            'revenue'     => (int) Transaksi::where('status', 'lunas')->sum('total_bayar'),
            'sold'        => Transaksi::where('status', 'lunas')->count(),
            'cabin_used'  => Cabin::where('status', 'terisi')->count(),
            'cabin_total' => Cabin::count(),
        ];
    }

    public function index()
    {
        $transaksis = Transaksi::with('pengguna')->orderBy('tanggal_transaksi', 'desc')->get();

        $formattedTransaksis = $transaksis->map(function($trx) {
            return $this->formatTransaksi($trx);
        })->values();

        return view('admin.transaksi', [
            'stats' => $this->stats(),
            'transaksis' => $formattedTransaksis
        ]);
    }

    /**
     * Feed JSON untuk pembaruan realtime (websocket) di halaman admin.
     */
    public function feed()
    {
        $rows = Transaksi::with('pengguna')
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(100)
            ->get()
            ->map(fn ($trx) => $this->formatTransaksi($trx))
            ->values();

        return response()->json([
            'ok' => true,
            'data' => [
                'stats'        => $this->stats(),
                'rows'         => $rows,
                'pending_rows' => $rows->where('status', 'pending')->values(),
            ],
        ]);
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
