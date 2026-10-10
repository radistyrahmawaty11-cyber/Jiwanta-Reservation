<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;

class VerifikasiController extends Controller
{
    public function index()
    {
        $pending = Transaksi::with('pengguna')
            ->where('status', 'pending')
            ->orderBy('tgl_transaksi')
            ->get();

        $riwayat = Transaksi::with('pengguna')
            ->whereIn('status', ['lunas', 'gagal'])
            ->orderByDesc('tgl_transaksi')
            ->take(10)
            ->get();

        return view('admin.verifikasi', compact('pending', 'riwayat'));
    }

    public function approve(Transaksi $transaksi)
    {
        $transaksi->status = 'lunas';
        $transaksi->save();

        return response()->json(['ok' => true, 'status' => $transaksi->status]);
    }

    public function reject(Transaksi $transaksi)
    {
        $transaksi->status = 'gagal';
        $transaksi->save();

        return response()->json(['ok' => true, 'status' => $transaksi->status]);
    }
}
