<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabin extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabin',
        'jenis_cabin',
        'kapasitas',
        'harga_per_malam',
        'status',
        'deskripsi',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}
