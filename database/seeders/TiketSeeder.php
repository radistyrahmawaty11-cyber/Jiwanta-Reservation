<?php

namespace Database\Seeders;

use App\Models\Tiket;
use Illuminate\Database\Seeder;

class TiketSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_tiket' => 'Tiket Renang Classic', 'jenis_tiket' => 'classic', 'kategori' => 'renang', 'harga' => 60000, 'kuota' => 350, 'status' => 'tersedia'],
            ['nama_tiket' => 'Tiket Renang Premier', 'jenis_tiket' => 'premier', 'kategori' => 'renang', 'harga' => 75000, 'kuota' => 200, 'status' => 'tersedia'],
            ['nama_tiket' => 'Paket Cabin Shorts', 'jenis_tiket' => 'classic', 'kategori' => 'cabin', 'harga' => 340000, 'kuota' => 12, 'status' => 'tersedia'],
            ['nama_tiket' => 'Paket Cabin Suite', 'jenis_tiket' => 'premier', 'kategori' => 'cabin', 'harga' => 1040000, 'kuota' => 6, 'status' => 'tersedia'],
        ];

        foreach ($rows as $row) {
            Tiket::create($row);
        }
    }
}
