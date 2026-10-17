<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
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
            'facilities' => ['Gazebo Publik', 'Kamar Bilas Standar'],
            'desc' => $tiket->deskripsi ?? 'Tiket masuk area kolam renang Jiwanta.',
            'kategori' => $tiket->kategori,
            'sku' => $sku,
            'short' => $short,
            'portfolio' => $tiket->nama_tiket,
            'sold' => (int) ($tiket->sold ?? 0),
            'safeMax' => (int) ($tiket->stok * 1.25),
            'image' => 'https://via.placeholder.com/150x150/2d5a4a/ffffff?text='.urlencode($tiket->nama_tiket),
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
        ]);

        $tiket = Tiket::create([
            'nama_tiket' => $validated['name'],
            'harga' => $validated['price'],
            'stok' => $validated['cap'],
            'deskripsi' => $validated['desc'] ?? null,
            'kategori' => 'dewasa',
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
        ]);

        $tiket->update([
            'nama_tiket' => $validated['name'],
            'harga' => $validated['price'],
            'stok' => $validated['cap'],
            'deskripsi' => $validated['desc'] ?? null,
        ]);

        return response()->json(['ok' => true, 'data' => $this->formatTiket($tiket)]);
    }

    public function destroy($id)
    {
        $tiket = Tiket::findOrFail($id);
        $tiket->delete();

        return response()->json(['ok' => true, 'message' => 'Tiket berhasil dihapus']);
    }

    public function updateQuota(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $validated = $request->validate(['cap' => 'required|integer|min:0']);
        $tiket->update(['stok' => $validated['cap']]);

        return response()->json(['ok' => true, 'data' => $this->formatTiket($tiket)]);
    }

    public function openAll()
    {
        return response()->json(['ok' => true, 'message' => 'Semua tiket dibuka']);
    }
}
