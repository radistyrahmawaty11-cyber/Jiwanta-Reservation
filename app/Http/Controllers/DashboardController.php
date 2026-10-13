<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tikets = Tiket::orderBy('kategori')->orderBy('id')->get();
        $pending = Transaksi::with('pengguna')->where('status', 'pending')->orderByDesc('tanggal_transaksi')->take(6)->get();
        $recent = Transaksi::with('pengguna')->orderByDesc('tanggal_transaksi')->take(6)->get();
        $cabins = Cabin::orderBy('nama_cabin')->orderBy('id')->get();

        $stats = [
            'total_transaksi' => Transaksi::count(),
            'pending' => Transaksi::where('status', 'pending')->count(),
            'lunas' => Transaksi::where('status', 'lunas')->count(),
            'gagal' => Transaksi::where('status', 'gagal')->count(),
            'pendapatan' => (float) Transaksi::where('status', 'lunas')->sum('total_bayar'),
            'pengguna' => Pengguna::count(),
            'kuota_renang' => (int) Tiket::sum('stok'),
            'cabin_tersedia' => Cabin::where('status', 'tersedia')->count(),
            'cabin_dipesan' => Cabin::where('status', 'terisi')->count(),
        ];

        return view('admin.dashboard', compact('tikets', 'pending', 'recent', 'cabins', 'stats'));
    }

    public function updateKuota(Request $request)
    {
        $d = $request->validate([
            'tiket_id' => ['required', 'exists:tikets,id'],
            'kuota' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $tiket = Tiket::findOrFail($d['tiket_id']);
        $tiket->stok = $d['kuota'];
        $tiket->save();

        return response()->json(['ok' => true, 'kuota' => $tiket->stok]);
    }
}
