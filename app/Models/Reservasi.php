<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengguna_id',
        'cabin_id',
        'renang_id',
        'tanggal_checkin',
        'tanggal_checkout',
        'jumlah_tamu',
        'total_harga',
        'status',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function cabin()
    {
        return $this->belongsTo(Cabin::class);
    }

    public function renang()
    {
        return $this->belongsTo(Renang::class);
    }
}
