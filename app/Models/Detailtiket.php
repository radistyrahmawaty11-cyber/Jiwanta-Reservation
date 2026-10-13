<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailtiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiket_id',
        'jumlah',
        'subtotal',
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}
