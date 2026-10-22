<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'midtrans_order_id',
        'snap_token',
        'pengguna_id',
        'tanggal_transaksi',
        'total_amount',
        'total_bayar',
        'kembalian',
        'metode_pembayaran',
        'payment_type',
        'payment_detail',
        'paid_at',
        'verified_at',
        'checked_in_at',
        'bukti_transfer',
        'status',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'payment_detail' => 'array',
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
