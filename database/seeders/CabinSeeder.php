<?php

namespace Database\Seeders;

use App\Models\Cabin;
use Illuminate\Database\Seeder;

class CabinSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_cabin' => 'Cabin Suite Pinus', 'deskripsi' => 'Cabin suite dengan pemandangan hutan pinus dan hot spring pribadi.', 'harga_per_malam' => 1040000, 'kapasitas' => 5, 'fasilitas' => 'Hot spring pribadi, AC, Sarapan, Wi-Fi', 'gambar' => null, 'status' => 'tersedia'],
            ['nama_cabin' => 'Cabin Suite Magnolia', 'deskripsi' => 'Cabin suite keluarga dengan balkon menghadap lembah Ciwidey.', 'harga_per_malam' => 1040000, 'kapasitas' => 4, 'fasilitas' => 'Balkon, AC, Sarapan, Wi-Fi', 'gambar' => null, 'status' => 'tersedia'],
            ['nama_cabin' => 'Cabin Suite Eukaliptus', 'deskripsi' => 'Cabin suite premium dengan area api unggun pribadi.', 'harga_per_malam' => 1040000, 'kapasitas' => 4, 'fasilitas' => 'Api unggun, AC, Sarapan, Wi-Fi', 'gambar' => null, 'status' => 'terisi'],
            ['nama_cabin' => 'Cabin Shorts Melati', 'deskripsi' => 'Cabin shorts untuk keluarga kecil dengan dek pribadi.', 'harga_per_malam' => 340000, 'kapasitas' => 4, 'fasilitas' => 'Dek pribadi, Kipas, Sarapan', 'gambar' => null, 'status' => 'tersedia'],
            ['nama_cabin' => 'Cabin Shorts Anggrek', 'deskripsi' => 'Cabin shorts nyaman dekat area kolam utama.', 'harga_per_malam' => 340000, 'kapasitas' => 4, 'fasilitas' => 'Dek pribadi, Kipas, Sarapan', 'gambar' => null, 'status' => 'tersedia'],
        ];

        foreach ($rows as $row) {
            Cabin::updateOrCreate(['nama_cabin' => $row['nama_cabin']], $row);
        }
    }
}
