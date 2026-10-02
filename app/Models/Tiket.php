<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tiket',
        'jenis_tiket',
        'kategori',
        'harga',
        'kuota',
        'status',
    ];

    public function detail_tikets()
    {
        return $this->hasMany(Detailtiket::class);
    }
}
