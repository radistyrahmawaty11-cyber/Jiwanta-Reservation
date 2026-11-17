<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $this->kategoriDari($request);
        $sort = $request->query('sort') === 'rating' ? 'rating' : 'terbaru';
        $ulasans = $this->listQuery($kategori, $sort)
            ->get()
            ->map(fn (Ulasan $ulasan) => $this->format($ulasan))
            ->values();
        $ringkasan = Ulasan::ringkasan($kategori);
        $ringkasanKategori = [
            Ulasan::KATEGORI_TIKET => Ulasan::ringkasan(Ulasan::KATEGORI_TIKET),
            Ulasan::KATEGORI_CABIN => Ulasan::ringkasan(Ulasan::KATEGORI_CABIN),
        ];
        $kategoriBisaDinilai = $request->user()->reviewableCategories();
        $bisaMenilai = $kategoriBisaDinilai !== [];
        $ulasanSaya = $request->user()
            ->ulasans()
            ->where('kategori', $kategori)
            ->first();
        $ulasanForm = $kategoriBisaDinilai === []
            ? null
            : $request->user()
                ->ulasans()
                ->whereIn('kategori', $kategoriBisaDinilai)
                ->latest('updated_at')
                ->first();

        if ($request->boolean('partial')) {
            return response()->view('pengunjung.ulasan._list', [
                'ulasans' => $ulasans,
                'kategori' => $kategori,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'kategori' => $kategori,
                'label_kategori' => Ulasan::labelKategori($kategori),
                'data' => $ulasans,
                'ringkasan' => $ringkasan,
                'ringkasan_kategori' => $ringkasanKategori,
                'bisa_menilai' => $bisaMenilai,
                'kategori_bisa_dinilai' => $kategoriBisaDinilai,
                'ulasan_saya' => $ulasanSaya ? $this->format($ulasanSaya) : null,
                'sort' => $sort,
            ]);
        }

        return view('pengunjung.ulasan.index', [
            'ulasans' => $ulasans,
            'ringkasan' => $ringkasan,
            'ringkasanKategori' => $ringkasanKategori,
            'sort' => $sort,
            'kategori' => $kategori,
            'bisaMenilai' => $bisaMenilai,
            'kategoriBisaDinilai' => $kategoriBisaDinilai,
            'ulasanSaya' => $ulasanSaya,
            'ulasanForm' => $ulasanForm,
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

        $kategoriDinilai = $request->user()->reviewableCategories();

        if ($kategoriDinilai === []) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda hanya dapat memberi rating setelah memiliki transaksi yang sudah diverifikasi petugas.',
            ], 403);
        }

        $ulasans = [];

        foreach ($kategoriDinilai as $kategori) {
            $ulasans[$kategori] = Ulasan::updateOrCreate(
                [
                    'pengguna_id' => $request->user()->id,
                    'kategori' => $kategori,
                ],
                [
                    'rating' => $data['rating'],
                    'komentar' => $data['komentar'],
                ]
            );
        }

        $kategoriUtama = $kategoriDinilai[0];

        return response()->json([
            'ok' => true,
            'kategori' => $kategoriUtama,
            'kategori_dinilai' => $kategoriDinilai,
            'data' => collect($ulasans)
                ->map(fn (Ulasan $ulasan) => $this->format($ulasan->load('pengguna')))
                ->values(),
            'ringkasan' => Ulasan::ringkasan($kategoriUtama),
            'ringkasan_kategori' => [
                Ulasan::KATEGORI_TIKET => Ulasan::ringkasan(Ulasan::KATEGORI_TIKET),
                Ulasan::KATEGORI_CABIN => Ulasan::ringkasan(Ulasan::KATEGORI_CABIN),
            ],
        ]);
    }

    private function kategoriDari(Request $request): string
    {
        return $request->query('kategori') === Ulasan::KATEGORI_CABIN
            ? Ulasan::KATEGORI_CABIN
            : Ulasan::KATEGORI_TIKET;
    }

    private function listQuery(string $kategori, string $sort)
    {
        return Ulasan::with('pengguna')
            ->where('kategori', $kategori)
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
            'kategori' => $ulasan->kategori,
            'nama_pengguna' => $ulasan->pengguna?->nama_pengguna ?? 'Pengguna',
            'foto' => $ulasan->pengguna?->fotoUrl(),
            'rating' => (int) $ulasan->rating,
            'komentar' => $ulasan->komentar,
            'created_at' => $ulasan->created_at?->toIso8601String(),
            'updated_at' => $ulasan->updated_at?->toIso8601String(),
        ];
    }
}
