<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    protected $table = 'penggunas';

    protected $fillable = [
        'nama_pengguna',
        'email',
        'password',
        'nohp',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Override karena kolom username bukan 'name'
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    // Relasi ke transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'pengguna_id');
    }

    // Relasi ke reservasi
    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'pengguna_id');
    }
}
