<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabin;

class CabinController extends Controller
{
    /**
     * 1. Menampilkan semua data cabin (Read)
     */
    public function index()
    {
        $cabins = Cabin::all();

        return view('admin.cabin', compact('cabins'));
    }

    /**
     * 2. Menampilkan form tambah cabin (Create)
     * (Bisa dikosongkan jika form tambah menggunakan Modal/Popup di halaman index)
     */
    public function create()
    {
        //
    }

    /**
     * 3. Menyimpan data cabin baru ke database (Store)
     */
    public function store(Request $request)
    {
        // A. VALIDASI DATA (Satpam Data)
        $request->validate([
            'nama_cabin'      => 'required|string|max:100',
            'jenis_cabin'     => 'required|in:suite,shorts',
            'kapasitas'       => 'required|integer|min:1',
            'harga_per_malam' => 'required|numeric|min:0',
            'status'          => 'required|in:tersedia,penuh,maintenance',
            'deskripsi'       => 'nullable|string',
        ], [
            // Pesan error custom agar user tahu apa yang salah
            'nama_cabin.required'      => 'Nama cabin wajib diisi.',
            'jenis_cabin.required'     => 'Jenis cabin wajib dipilih.',
            'kapasitas.required'       => 'Kapasitas wajib diisi.',
            'kapasitas.integer'        => 'Kapasitas harus berupa angka.',
            'harga_per_malam.required' => 'Harga per malam wajib diisi.',
            'harga_per_malam.numeric'  => 'Harga harus berupa angka.',
            'status.required'          => 'Status wajib dipilih.',
        ]);

        // B. SIMPAN KE DATABASE
        Cabin::create($request->all());

        // C. KEMBALI KE HALAMAN CABIN DENGAN PESAN SUKSES
        return redirect()->route('cabin')->with('success', 'Cabin berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan detail 1 cabin (Show)
     */
    public function show($id)
    {
        //
    }

    /**
     * 5. Menampilkan form edit cabin (Edit)
     */
    public function edit($id)
    {
        //
    }

    /**
     * 6. Memperbarui data cabin (Update)
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 7. Menghapus data cabin (Destroy)
     */
    public function destroy($id)
    {
        // 1. Cari cabin berdasarkan ID. Jika tidak ada, lempar error 404
        $cabin = Cabin::findOrFail($id);

        // 2. Hapus data dari database
        $cabin->delete();

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Cabin berhasil dihapus!');
    }
}
