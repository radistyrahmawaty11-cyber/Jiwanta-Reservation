<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TiketController extends Controller
{
    private function parseFacilities(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[,\n]/', $raw))));
    }

    private function formatTiket($tiket)
    {
        $short = str_replace(['tiket', 'kolam', 'renang'], '', strtolower($tiket->nama_tiket));
        $short = trim(preg_replace('/\s+/', ' ', $short));
        $short = ucwords($short) ?: 'Tiket';

        $sku = 'JWN-TKT-'.strtoupper(substr($tiket->nama_tiket, 0, 3)).'-'.str_pad($tiket->id, 2, '0', STR_PAD_LEFT);

        return [
            'id' => (int) $tiket->id,
            'name' => $tiket->nama_tiket,
            'price' => (int) $tiket->harga,
            'unit' => 'orang',
            'cap' => (int) $tiket->stok,
            'accent' => $tiket->kategori === 'lansia' ? 'vip' : 'public',
            'temp' => '39° - 41°C',
            'status' => 'active',
            'hours' => '08:00 - 18:00 WIB',
            'sessions' => ['Pagi 08-12', 'Sore 13-17'],
            'facilities' => $this->parseFacilities($tiket->fasilitas),
            'desc' => $tiket->deskripsi ?? 'Tiket masuk area kolam renang Jiwanta.',
            'kategori' => $tiket->kategori,
            'sku' => $sku,
            'short' => $short,
            'portfolio' => $tiket->nama_tiket,
            'sold' => (int) ($tiket->sold ?? 0),
            'safeMax' => (int) ($tiket->stok * 1.25),
            'image' => $tiket->gambar ? asset('storage/'.$tiket->gambar) : '',
            'updatedAt' => time() * 1000,
        ];
    }

    public function index()
    {
        $tikets = Tiket::withSum('detail_tikets as sold', 'jumlah')
            ->orderBy('kategori')->orderBy('nama_tiket')->get()
            ->map(function ($tiket) {
                return $this->formatTiket($tiket);
            });

        $revenue = (int) $tikets->sum(fn ($t) => ($t['sold'] ?? 0) * ($t['price'] ?? 0));

        return view('admin.tiket-renang', ['tickets' => $tikets, 'revenue' => $revenue]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cap' => 'required|integer|min:0',
            'desc' => 'nullable|string',
            'fasilitas' => 'nullable|string|max:500',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('tikets', 'public');
            ImageOptimizer::optimize(Storage::disk('public')->path($gambar));
        }

        $tiket = Tiket::create([
            'nama_tiket' => $validated['name'],
            'harga' => $validated['price'],
            'stok' => $validated['cap'],
            'deskripsi' => $validated['desc'] ?? null,
            'kategori' => 'dewasa',
            'fasilitas' => $validated['fasilitas'] ?? null,
            'gambar' => $gambar,
        ]);

        return response()->json(['ok' => true, 'data' => $this->formatTiket($tiket)]);
    }

    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cap' => 'required|integer|min:0',
            'desc' => 'nullable|string',
            'fasilitas' => 'nullable|string|max:500',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($tiket->gambar) {
                Storage::disk('public')->delete($tiket->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('tikets', 'public');
            ImageOptimizer::optimize(Storage::disk('public')->path($validated['gambar']));
        }

        $attributes = [
            'nama_tiket' => $validated['name'],
            'harga' => $validated['price'],
            'stok' => $validated['cap'],
            'deskripsi' => $validated['desc'] ?? null,
            'gambar' => $validated['gambar'] ?? $tiket->gambar,
        ];

        if ($request->has('fasilitas')) {
            $attributes['fasilitas'] = $validated['fasilitas'] ?? null;
        }

        $tiket->update($attributes);

        return response()->json(['ok' => true, 'data' => $this->formatTiket($tiket)]);
    }

    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);

        if ($tiket->gambar) {
            Storage::disk('public')->delete($tiket->gambar);
        }

        $tiket->delete();

        return response()->json(['ok' => true, 'message' => 'Tiket berhasil dihapus']);
    }

    public function updateQuota(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $validated = $request->validate(['cap' => 'required|integer|min:0|max:100000']);

        $tiket->update(['stok' => $validated['cap']]);

        return response()->json(['ok' => true, 'data' => $this->formatTiket($tiket)]);
    }

    public function openAll()
    {
        return response()->json(['ok' => true, 'message' => 'Semua tiket dibuka']);
    }
}
