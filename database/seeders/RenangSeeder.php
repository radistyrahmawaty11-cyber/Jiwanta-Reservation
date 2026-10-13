<?php

namespace Database\Seeders;

use App\Models\Renang;
use Illuminate\Database\Seeder;

class RenangSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_kolam' => 'Kolam Air Panas Utama', 'deskripsi' => 'Kolam hot spring utama dengan pemandangan hutan pinus Ciwidey.', 'harga_tiket' => 60000, 'kedalaman' => '120-150 cm', 'kapasitas' => 200, 'jam_buka' => '08:00', 'jam_tutup' => '18:00'],
            ['nama_kolam' => 'Kolam Anak & Balita', 'deskripsi' => 'Kolam dangkal dengan pengawasan lifeguard untuk anak dan balita.', 'harga_tiket' => 40000, 'kedalaman' => '30-60 cm', 'kapasitas' => 100, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            ['nama_kolam' => 'Kolam Private Hot Spring', 'deskripsi' => 'Kolam privat dengan air panas alami, cocok untuk keluarga.', 'harga_tiket' => 150000, 'kedalaman' => '100-120 cm', 'kapasitas' => 20, 'jam_buka' => '09:00', 'jam_tutup' => '21:00'],
            ['nama_kolam' => 'Kolam Infinity View', 'deskripsi' => 'Kolam infinity menghadap lembah dengan area lounge.', 'harga_tiket' => 90000, 'kedalaman' => '140 cm', 'kapasitas' => 80, 'jam_buka' => '07:00', 'jam_tutup' => '19:00'],
        ];

        foreach ($rows as $row) {
            Renang::updateOrCreate(['nama_kolam' => $row['nama_kolam']], $row + ['gambar' => null]);
        }
    }
}
