<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengguna_id',
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

    /**
     * Rata-rata dan jumlah ulasan selalu dihitung langsung dari database.
     *
     * @return array{rata_rata: float, jumlah_ulasan: int}
     */
    public static function ringkasan(): array
    {
        $summary = static::query()
            ->selectRaw('AVG(rating) as rata_rata, COUNT(*) as jumlah_ulasan')
            ->first();

        return [
            'rata_rata' => round((float) ($summary?->rata_rata ?? 0), 1),
            'jumlah_ulasan' => (int) ($summary?->jumlah_ulasan ?? 0),
        ];
    }
}
