<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index()
    {
        $harian = Transaksi::selectRaw('DATE(tanggal_transaksi) as tanggal, COUNT(*) as jumlah, SUM(total_bayar) as total')
            ->where('status', 'lunas')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->orderByDesc('tanggal')
            ->get();

        $ringkasan = [
            'total_transaksi' => Transaksi::count(),
            'lunas' => Transaksi::where('status', 'lunas')->count(),
            'pending' => Transaksi::where('status', 'pending')->count(),
            'gagal' => Transaksi::where('status', 'gagal')->count(),
            'pendapatan' => (float) Transaksi::where('status', 'lunas')->sum('total_bayar'),
            'total' => (float) Transaksi::sum('total_bayar'),
        ];

        $laporan = Transaksi::with('pengguna')->orderByDesc('tanggal_transaksi')->get();

        return view('admin.laporan', compact('harian', 'ringkasan', 'laporan'));
    }

    public function export(): StreamedResponse
    {
        $rows = Transaksi::with('pengguna')->orderByDesc('tanggal_transaksi')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Tanggal', 'Kode', 'Nama', 'Metode', 'Total', 'Status']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    optional($r->tanggal_transaksi)->format('Y-m-d H:i'),
                    $r->kode_transaksi,
                    $r->pengguna?->nama_pengguna,
                    strtoupper($r->metode_pembayaran),
                    $r->total_bayar,
                    $r->status,
                ]);
            }
            fclose($out);
        }, 'laporan-operasional-'.date('Ymd').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
