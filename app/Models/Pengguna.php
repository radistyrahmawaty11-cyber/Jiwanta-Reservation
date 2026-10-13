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
}
