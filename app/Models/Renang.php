<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kolam',
        'deskripsi',
        'harga_tiket',
        'kedalaman',
        'kapasitas',
        'jam_buka',
        'jam_tutup',
        'gambar',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}
