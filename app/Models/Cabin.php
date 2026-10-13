<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabin',
        'deskripsi',
        'harga_per_malam',
        'kapasitas',
        'fasilitas',
        'gambar',
        'status',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}
