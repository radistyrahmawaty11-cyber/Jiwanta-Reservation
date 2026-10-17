<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CabinController extends Controller
{
    // Tampilkan daftar semua cabin
    public function index()
    {
        $cabins = Cabin::with(['reservasis' => function ($q) {
            $q->where('status', 'confirmed')->with('pengguna');
        }])->orderBy('created_at', 'desc')->get();

        return view('admin.cabin', compact('cabins'));
    }

    // Tampilkan form tambah cabin
    public function create()
    {
        return view('admin.cabin-form', ['cabin' => null]);
    }

    // Simpan cabin baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_cabin' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_per_malam' => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:tersedia,terisi',
        ]);

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('cabins', 'public');
        }

        Cabin::create($validated);

        return redirect()->route('admin.cabin.index')->with('success', 'Cabin berhasil ditambahkan!');
    }

    // Tampilkan form edit cabin
    public function edit($id)
    {
        $cabin = Cabin::findOrFail($id);

        return view('admin.cabin-form', compact('cabin'));
    }

    // Update cabin
    public function update(Request $request, $id)
    {
        $cabin = Cabin::findOrFail($id);

        $validated = $request->validate([
            'nama_cabin' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga_per_malam' => 'required|numeric|min:0',
            'kapasitas' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:tersedia,terisi',
        ]);

        // Handle upload gambar baru (hapus yang lama)
        if ($request->hasFile('gambar')) {
            if ($cabin->gambar) {
                Storage::disk('public')->delete($cabin->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('cabins', 'public');
        }

        $cabin->update($validated);

        return redirect()->route('admin.cabin.index')->with('success', 'Cabin berhasil diperbarui!');
    }

    // Hapus cabin
    public function destroy($id)
    {
        $cabin = Cabin::findOrFail($id);

        // Hapus gambar jika ada
        if ($cabin->gambar) {
            Storage::disk('public')->delete($cabin->gambar);
        }

        $cabin->delete();

        return redirect()->route('admin.cabin.index')->with('success', 'Cabin berhasil dihapus!');
    }
}
