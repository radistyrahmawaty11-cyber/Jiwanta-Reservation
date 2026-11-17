<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'penggunas'; // Pastikan ini sesuai nama tabel

    protected $fillable = [
        'nama_pengguna',
        'email',
        'nohp',
        'foto',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/'.$this->foto)
            : asset('images/profil.jpg');
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function canReviewCategory(string $kategori): bool
    {
        return $this->transaksis()
            ->where('status', 'lunas')
            ->whereNotNull('verified_at')
            ->whereHas('detailtransaksis', function ($query) use ($kategori) {
                if ($kategori === Ulasan::KATEGORI_CABIN) {
                    $query->whereHas('reservasi', fn ($reservasi) => $reservasi->whereNotNull('cabin_id'));
                } else {
                    $query->whereNotNull('tiket_id');
                }
            })
            ->exists();
    }

    /**
     * @return list<string>
     */
    public function reviewableCategories(): array
    {
        return array_values(array_filter(
            array_keys(Ulasan::KATEGORI),
            fn (string $kategori) => $this->canReviewCategory($kategori)
        ));
    }
}
