<?php

namespace Database\Seeders;

use App\Models\Detailtiket;
use App\Models\Tiket;
use Illuminate\Database\Seeder;

class DetailTiketSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_tiket' => 'Tiket Renang Classic (Dewasa)', 'jumlah' => 2, 'subtotal' => 120000],
            ['nama_tiket' => 'Tiket Renang Premier (Dewasa)', 'jumlah' => 1, 'subtotal' => 75000],
            ['nama_tiket' => 'Tiket Renang Anak', 'jumlah' => 3, 'subtotal' => 120000],
            ['nama_tiket' => 'Tiket Renang Lansia', 'jumlah' => 1, 'subtotal' => 45000],
        ];

        foreach ($rows as $row) {
            $tiketId = Tiket::where('nama_tiket', $row['nama_tiket'])->value('id');

            if (! $tiketId) {
                continue;
            }

            Detailtiket::updateOrCreate(
                ['tiket_id' => $tiketId],
                ['jumlah' => $row['jumlah'], 'subtotal' => $row['subtotal']]
            );
        }
    }
}
