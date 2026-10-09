<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiket; // Jangan lupa import Model Tiket

class TiketController extends Controller
{
    // 1. Menampilkan semua data tiket
    public function index()
    {
        $tikets = Tiket::all();

        // Nanti kalau frontend udah siap, tinggal ubah 'admin.tiket' jadi nama view mereka
        return view('admin.tiket', compact('tikets'));
    }

    // 2. Menyimpan data tiket baru
    public function store(Request $request)
    {
        // Validasi data (sesuai kolom di database tiket)
        $request->validate([
            'nama_tiket'  => 'required|string|max:100',
            'jenis_tiket' => 'required|string',
            'kategori'    => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'kuota'       => 'required|integer|min:1',
            'status'      => 'required|in:tersedia,habis',
        ], [
            'nama_tiket.required' => 'Nama tiket wajib diisi.',
            'harga.numeric'       => 'Harga harus berupa angka.',
        ]);

        // Simpan ke database
        Tiket::create($request->all());

        // Balik ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Tiket berhasil ditambahkan!');
    }

    // (Fungsi create, show, edit, update, destroy biarkan kosong dulu)
}
