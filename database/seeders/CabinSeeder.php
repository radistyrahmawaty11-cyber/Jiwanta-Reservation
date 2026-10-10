<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabin;

class CabinSeeder extends Seeder
{
    public function run(): void
    {
        Cabin::create([
            'nama_cabin' => 'Cabin Suite Pinus',
            'jenis_cabin' => 'suite',
            'kapasitas' => 5,
            'harga_per_malam' => 1040000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin suite dengan pemandangan hutan pinus dan hot spring pribadi.',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Suite Magnolia',
            'jenis_cabin' => 'suite',
            'kapasitas' => 4,
            'harga_per_malam' => 1040000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin suite keluarga dengan balkon menghadap lembah Ciwidey.',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Suite Eukaliptus',
            'jenis_cabin' => 'suite',
            'kapasitas' => 4,
            'harga_per_malam' => 1040000,
            'status' => 'dipesan',
            'deskripsi' => 'Cabin suite premium dengan area api unggun pribadi.',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Shorts Melati',
            'jenis_cabin' => 'shorts',
            'kapasitas' => 4,
            'harga_per_malam' => 340000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin shorts untuk keluarga kecil dengan dek pribadi.',
        ]);

        Cabin::create([
            'nama_cabin' => 'Cabin Shorts Anggrek',
            'jenis_cabin' => 'shorts',
            'kapasitas' => 4,
            'harga_per_malam' => 340000,
            'status' => 'tersedia',
            'deskripsi' => 'Cabin shorts nyaman dekat area kolam utama.',
        ]);
    }
}
