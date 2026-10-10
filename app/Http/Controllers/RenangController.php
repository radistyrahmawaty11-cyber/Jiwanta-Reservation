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
        $request->validate([
            'nama_renang'   => 'required|string|max:100',
            'jenis_renang'  => 'required|string',
            'kapasitas'     => 'required|integer|min:1',
            'harga_per_jam' => 'required|numeric|min:0',
            'status'        => 'required|in:tersedia,penuh,maintenance',
            'deskripsi'     => 'nullable|string',
        ], [
            'nama_renang.required'   => 'Nama fasilitas wajib diisi.',
            'harga_per_jam.numeric'  => 'Harga harus berupa angka.',
        ]);

        // Simpan ke database
        Renang::create($request->all());

        // Balik ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Fasilitas renang berhasil ditambahkan!');
    }

    // (Fungsi create, show, edit, update, destroy biarkan kosong dulu)
}
