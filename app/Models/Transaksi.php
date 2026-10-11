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
        'tanggal_transaksi',
        'total_amount',
        'total_bayar',
        'kembalian',
        'metode_pembayaran',
        'status',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function detailtransaksis()
    {
        return $this->hasMany(Detailtransaksi::class);
    }
}
