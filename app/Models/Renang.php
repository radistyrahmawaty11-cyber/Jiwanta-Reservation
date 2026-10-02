<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_renang',
        'jenis_renang',
        'kapasitas',
        'harga_per_jam',
        'status',
        'deskripsi',
    ];

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}
