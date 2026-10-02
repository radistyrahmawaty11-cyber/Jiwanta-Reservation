<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'nohp',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
