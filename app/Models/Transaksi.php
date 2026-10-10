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
        'bukti',
        'jenis_transaksi',
        'status',
    ];

    protected $casts = [
        'tgl_transaksi' => 'datetime',
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
