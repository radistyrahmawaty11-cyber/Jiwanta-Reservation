<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index()
    {
        $ulasans = Ulasan::with('pengguna')
            ->latest()
            ->get()
            ->map(fn (Ulasan $ulasan) => $this->format($ulasan))
            ->values();

        return response()->json([
            'ok' => true,
            'data' => $ulasans,
            'ringkasan' => Ulasan::ringkasan(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'komentar' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rating.required' => 'Rating wajib diisi.',
            'rating.integer' => 'Rating harus berupa angka bulat.',
            'rating.between' => 'Rating harus antara 1 sampai 5.',
            'komentar.required' => 'Komentar wajib diisi.',
            'komentar.string' => 'Komentar harus berupa teks.',
            'komentar.min' => 'Komentar minimal 10 karakter.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        $ulasan = Ulasan::updateOrCreate(
            ['pengguna_id' => $request->user()->id],
            [
                'rating' => $data['rating'],
                'komentar' => $data['komentar'],
            ]
        );

        return response()->json([
            'ok' => true,
            'data' => $this->format($ulasan->load('pengguna')),
            'ringkasan' => Ulasan::ringkasan(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function format(Ulasan $ulasan): array
    {
        return [
            'id' => (int) $ulasan->id,
            'pengguna_id' => (int) $ulasan->pengguna_id,
            'nama_pengguna' => $ulasan->pengguna?->nama_pengguna ?? 'Pengguna',
            'foto' => $ulasan->pengguna?->fotoUrl(),
            'rating' => (int) $ulasan->rating,
            'komentar' => $ulasan->komentar,
            'created_at' => $ulasan->created_at?->toIso8601String(),
            'updated_at' => $ulasan->updated_at?->toIso8601String(),
        ];
    }
}
