<?php

namespace Database\Seeders;

use App\Models\Tiket;
use Illuminate\Database\Seeder;

class TiketSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_tiket' => 'Tiket Renang Classic (Dewasa)', 'deskripsi' => 'Tiket masuk kolam air panas untuk pengunjung dewasa.', 'harga' => 60000, 'stok' => 350, 'kategori' => 'dewasa'],
            ['nama_tiket' => 'Tiket Renang Premier (Dewasa)', 'deskripsi' => 'Tiket masuk dengan akses kolam premier dan handuk.', 'harga' => 75000, 'stok' => 200, 'kategori' => 'dewasa'],
            ['nama_tiket' => 'Tiket Renang Anak', 'deskripsi' => 'Tiket masuk kolam anak dengan pengawasan lifeguard.', 'harga' => 40000, 'stok' => 150, 'kategori' => 'anak-anak'],
            ['nama_tiket' => 'Tiket Renang Lansia', 'deskripsi' => 'Tiket khusus lansia dengan akses jalur landai.', 'harga' => 45000, 'stok' => 100, 'kategori' => 'lansia'],
        ];

        foreach ($rows as $row) {
            Tiket::updateOrCreate(['nama_tiket' => $row['nama_tiket']], $row);
        }
    }
}
