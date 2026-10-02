<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailtiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_id',
        'pengguna_id',
        'jumlah',
        'total_harga',
        'tgl_pembelian',
        'status',
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
