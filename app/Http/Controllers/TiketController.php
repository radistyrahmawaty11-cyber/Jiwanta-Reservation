<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index()
    {
        $tickets = Tiket::orderBy('kategori')->orderBy('id')->get()
            ->map(fn (Tiket $t) => $this->present($t))
            ->values()
            ->all();

        return view('admin.tiket-renang', compact('tickets'));
    }

    public function store(Request $request)
    {
        $d = $this->validated($request);

        $tiket = Tiket::create($this->columns($d));

        return response()->json(['ok' => true, 'data' => $this->present($tiket)]);
    }

    public function update(Request $request, Tiket $tiket)
    {
        $d = $this->validated($request);

        $tiket->update($this->columns($d, $tiket));

        return response()->json(['ok' => true, 'data' => $this->present($tiket)]);
    }

    public function destroy(Tiket $tiket)
    {
        $tiket->delete();

        return response()->json(['ok' => true]);
    }

    public function kuota(Request $request, Tiket $tiket)
    {
        $d = $request->validate([
            'cap' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $tiket->kuota = $d['cap'];
        $tiket->status = $d['cap'] > 0 ? 'tersedia' : 'habis';
        $tiket->save();

        return response()->json(['ok' => true, 'kuota' => $tiket->kuota, 'status' => $tiket->status]);
    }

    public function bukaSemua()
    {
        Tiket::query()->update(['status' => 'tersedia']);

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'cap' => ['required', 'integer', 'min:0', 'max:100000'],
            'status' => ['nullable', 'string', 'in:active,inactive,tersedia,habis'],
            'accent' => ['nullable', 'in:public,vip'],
            'jenis_tiket' => ['nullable', 'in:classic,premier'],
            'kategori' => ['nullable', 'in:cabin,renang'],
        ]);
    }

    private function columns(array $d, ?Tiket $existing = null): array
    {
        $accent = $d['accent'] ?? 'public';
        $status = $d['status'] ?? 'active';

        return [
            'nama_tiket' => $d['name'],
            'jenis_tiket' => $d['jenis_tiket'] ?? (($existing && ! isset($d['accent'])) ? $existing->jenis_tiket : ($accent === 'vip' ? 'premier' : 'classic')),
            'kategori' => $d['kategori'] ?? ($existing->kategori ?? 'renang'),
            'harga' => $d['price'],
            'kuota' => $d['cap'],
            'status' => in_array($status, ['active', 'tersedia'], true) ? 'tersedia' : 'habis',
        ];
    }

    private function present(Tiket $t): array
    {
        $vip = $t->jenis_tiket === 'premier';

        return [
            'id' => (string) $t->id,
            'sku' => 'JWN-TKT-'.strtoupper(substr($t->kategori, 0, 3)).'-'.str_pad((string) $t->id, 2, '0', STR_PAD_LEFT),
            'short' => $vip ? 'Premier Onsen' : 'Classic',
            'portfolio' => $t->nama_tiket,
            'name' => $t->nama_tiket,
            'accent' => $vip ? 'vip' : 'public',
            'temp' => '',
            'image' => $vip ? asset('images/tiket-premier.jpg') : asset('images/tiket-classic.jpg'),
            'price' => (int) $t->harga,
            'unit' => 'orang',
            'cap' => (int) $t->kuota,
            'sold' => 0,
            'safeMax' => (int) max($t->kuota + 100, (int) round($t->kuota * 1.25)),
            'hours' => '',
            'sessions' => [],
            'facilities' => [],
            'desc' => '',
            'status' => $t->status === 'tersedia' ? 'active' : 'inactive',
            'updatedMin' => 0,
            'jenis_tiket' => $t->jenis_tiket,
            'kategori' => $t->kategori,
        ];
    }
}
