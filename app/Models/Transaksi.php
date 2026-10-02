<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'pengguna_id',
        'tgl_transaksi',
        'total_bayar',
        'jumlah_bayar',
        'kembalian',
        'metode',
        'jenis_transaksi',
        'status',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
