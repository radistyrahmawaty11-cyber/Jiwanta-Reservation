<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->hasVerifiedTransaction()) {
            if ($request->boolean('partial') || $request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Rating hanya dapat diberikan setelah transaksi Anda diverifikasi oleh petugas.',
                ], 403);
            }

            return view('pengunjung.ulasan.locked', [
                'ringkasan' => Ulasan::ringkasan(),
            ]);
        }

        $sort = $request->query('sort') === 'rating' ? 'rating' : 'terbaru';
        $ulasans = $this->listQuery($sort)
            ->get()
            ->map(fn (Ulasan $ulasan) => $this->format($ulasan))
            ->values();
        $ringkasan = Ulasan::ringkasan();

        if ($request->boolean('partial')) {
            return response()->view('pengunjung.ulasan._list', [
                'ulasans' => $ulasans,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'data' => $ulasans,
                'ringkasan' => $ringkasan,
                'sort' => $sort,
            ]);
        }

        return view('pengunjung.ulasan.index', [
            'ulasans' => $ulasans,
            'ringkasan' => $ringkasan,
            'sort' => $sort,
            'ulasanSaya' => $request->user()->ulasan,
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->user()->hasVerifiedTransaction()) {
            return response()->json([
                'ok' => false,
                'message' => 'Rating hanya dapat diberikan setelah transaksi Anda diverifikasi oleh petugas.',
            ], 403);
        }

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

    private function listQuery(string $sort)
    {
        return Ulasan::with('pengguna')
            ->when($sort === 'rating', fn ($query) => $query->orderByDesc('rating'))
            ->latest();
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
