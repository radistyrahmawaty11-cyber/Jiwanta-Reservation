<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    public const KATEGORI_TIKET = 'tiket';

    public const KATEGORI_CABIN = 'cabin';

    public const KATEGORI = [
        self::KATEGORI_TIKET => 'Tiket Renang',
        self::KATEGORI_CABIN => 'Cabin',
    ];

    protected $fillable = [
        'pengguna_id',
        'kategori',
        'rating',
        'komentar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public static function labelKategori(string $kategori): string
    {
        return self::KATEGORI[$kategori] ?? self::KATEGORI[self::KATEGORI_TIKET];
    }

    /**
     * Rata-rata dan jumlah ulasan selalu dihitung langsung dari database.
     *
     * @return array{rata_rata: float, jumlah_ulasan: int}
     */
    public static function ringkasan(?string $kategori = null): array
    {
        $summary = static::query()
            ->when($kategori !== null, fn ($query) => $query->where('kategori', $kategori))
            ->selectRaw('AVG(rating) as rata_rata, COUNT(*) as jumlah_ulasan')
            ->first();

        return [
            'rata_rata' => round((float) ($summary?->rata_rata ?? 0), 1),
            'jumlah_ulasan' => (int) ($summary?->jumlah_ulasan ?? 0),
        ];
    }
}
