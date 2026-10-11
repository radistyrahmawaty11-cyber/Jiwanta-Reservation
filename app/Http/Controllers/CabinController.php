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
     *
     * CATATAN: Karena kita menggunakan Modal JavaScript di halaman index,
     * method ini tidak benar-benar dipanggil saat klik tombol "Tambah".
     * Kita buat redirect ke index agar aman, atau bisa return view('admin.cabin.create')
     * jika nanti ingin membuat halaman khusus untuk menambah data.
     */
    public function create()
    {
        // Redirect ke halaman index (karena form tambah sudah ada di modal)
        return redirect()->route('cabin');

        // Jika nanti ingin pakai halaman terpisah, hapus baris di atas dan gunakan:
        // return view('admin.cabin.create');
    }

    /**
     * 3. Menyimpan data cabin baru ke database (Store)
     */
    public function store(Request $request)
    {
        $request->validate([

            'nama_cabin'      => 'required|string|max:100',
            'jenis_cabin'     => 'required|in:suite,shorts',
            'kapasitas'       => 'required
            |integer|min:1',
            'harga_per_malam' => 'required|numeric|min:0',
            'status'          => 'required|in:tersedia,penuh,maintenance',
            'deskripsi'       => 'nullable|string',
        ], [
            'nama_cabin.required'      => 'Nama cabin wajib diisi.',
            'jenis_cabin.required'     => 'Jenis cabin wajib dipilih.',
            'kapasitas.required'       => 'Kapasitas wajib diisi.',
            'kapasitas.integer'        => 'Kapasitas harus berupa angka.',
            'harga_per_malam.required' => 'Harga per malam wajib diisi.',
            'harga_per_malam.numeric'  => 'Harga harus berupa angka.',
            'status.required'          => 'Status wajib dipilih.',
        ]);

        Cabin::create($request->all());

        return redirect()->route('cabin')->with('success', 'Cabin berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan detail 1 cabin (Show) - Biasanya untuk API
     */
    public function show($id)
    {
        $cabin = Cabin::findOrFail($id);
        return response()->json($cabin);
    }

    /**
     * 5. Menampilkan data cabin untuk diedit (Edit) - Biasanya untuk API/JS
     */
    public function edit($id)
    {
        $cabin = Cabin::findOrFail($id);
        return response()->json($cabin);
    }

    /**
     * 6. Memperbarui data cabin (Update)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_cabin'      => 'required|string|max:100',
            'jenis_cabin'     => 'required|in:suite,shorts',
            'kapasitas'       => 'required|integer|min:1',
            'harga_per_malam' => 'required|numeric|min:0',
            'status'          => 'required|in:tersedia,penuh,maintenance',
            'deskripsi'       => 'nullable|string',
        ], [
            'nama_cabin.required'      => 'Nama cabin wajib diisi.',
            'jenis_cabin.required'     => 'Jenis cabin wajib dipilih.',
            'kapasitas.required'       => 'Kapasitas wajib diisi.',
            'kapasitas.integer'        => 'Kapasitas harus berupa angka.',
            'harga_per_malam.required' => 'Harga per malam wajib diisi.',
            'harga_per_malam.numeric'  => 'Harga harus berupa angka.',
            'status.required'          => 'Status wajib dipilih.',
        ]);

        $cabin = Cabin::findOrFail($id);
        $cabin->update($request->all());

        return redirect()->route('cabin')->with('success', 'Data cabin berhasil diperbarui!');
    }

    /**
     * 7. Menghapus data cabin (Destroy)
     */
    public function destroy($id)
    {
        $cabin = Cabin::findOrFail($id);
        $cabin->delete();

        return redirect()->route('cabin')->with('success', 'Cabin berhasil dihapus!');
    }
}
