<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

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

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class, 'id_pengguna');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
