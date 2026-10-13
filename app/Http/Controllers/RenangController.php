<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Renang; // Import Model Renang

class RenangController extends Controller
{
    // 1. Menampilkan semua data fasilitas renang
    public function index()
    {
        $renangs = Renang::all();

        return view('admin.renang', compact('renangs'));
    }

    // 2. Menyimpan data fasilitas renang baru
    public function store(Request $request)
    {
        // Validasi data (sesuai kolom di database renangs)
        $validated = $request->validate([
            'nama_kolam'    => 'required|string|max:100',
            'deskripsi'     => 'nullable|string',
            'harga_tiket'   => 'required|numeric|min:0',
            'kedalaman'     => 'nullable|string|max:50',
            'kapasitas'     => 'required|integer|min:1',
            'jam_buka'      => 'required|date_format:H:i',
            'jam_tutup'     => 'required|date_format:H:i',
            'gambar'        => 'nullable|string|max:255',
        ], [
            'nama_kolam.required'  => 'Nama fasilitas wajib diisi.',
            'harga_tiket.numeric'  => 'Harga harus berupa angka.',
        ]);

        // Simpan ke database
        Renang::create($validated);

        // Balik ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Fasilitas renang berhasil ditambahkan!');
    }

    // (Fungsi create, show, edit, update, destroy biarkan kosong dulu)
}
