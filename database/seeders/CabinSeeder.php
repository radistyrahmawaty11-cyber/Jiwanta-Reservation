<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabin;

class CabinSeeder extends Seeder
{
    public function run(): void
    {
        Cabin::create([
            'nama_cabin' => 'Cabin Suite Mawar',
            'jenis_cabin' => 'suite',
            'kapasitas' => 4,
            'harga_per_malam' => 1250000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin suite dengan pemandangan hutan dan hot spring pribadi.',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Shorts Melati',
            'jenis_cabin' => 'shorts',
            'kapasitas' => 2,
            'harga_per_malam' => 750000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin cozy untuk pasangan dengan dek pribadi.',
        ]);
    }
}
