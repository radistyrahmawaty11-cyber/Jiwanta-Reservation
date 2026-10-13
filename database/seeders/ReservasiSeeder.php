<?php

namespace Database\Seeders;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Renang;
use App\Models\Reservasi;
use Illuminate\Database\Seeder;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = fn (string $email) => Pengguna::where('email', $email)->value('id');
        $cabin = fn (string $nama) => Cabin::where('nama_cabin', $nama)->value('id');
        $renang = fn (string $nama) => Renang::where('nama_kolam', $nama)->value('id');

        $rows = [
            ['email' => 'budi@gmail.com', 'cabin' => 'Cabin Suite Pinus', 'renang' => null, 'checkin' => 3, 'checkout' => 5, 'jumlah_tamu' => 2, 'total_harga' => 2080000, 'status' => 'confirmed'],
            ['email' => 'ani@gmail.com', 'cabin' => 'Cabin Shorts Melati', 'renang' => null, 'checkin' => 1, 'checkout' => 3, 'jumlah_tamu' => 2, 'total_harga' => 680000, 'status' => 'pending'],
            ['email' => 'siti@gmail.com', 'cabin' => null, 'renang' => 'Kolam Private Hot Spring', 'checkin' => 2, 'checkout' => 2, 'jumlah_tamu' => 4, 'total_harga' => 600000, 'status' => 'confirmed'],
            ['email' => 'ahmad@gmail.com', 'cabin' => 'Cabin Suite Magnolia', 'renang' => null, 'checkin' => 7, 'checkout' => 9, 'jumlah_tamu' => 4, 'total_harga' => 2080000, 'status' => 'pending'],
            ['email' => 'dewi@gmail.com', 'cabin' => null, 'renang' => 'Kolam Air Panas Utama', 'checkin' => 2, 'checkout' => 2, 'jumlah_tamu' => 3, 'total_harga' => 180000, 'status' => 'cancelled'],
            ['email' => 'rizky@gmail.com', 'cabin' => null, 'renang' => 'Kolam Infinity View', 'checkin' => 4, 'checkout' => 4, 'jumlah_tamu' => 2, 'total_harga' => 180000, 'status' => 'confirmed'],
        ];

        foreach ($rows as $row) {
            $penggunaId = $pengguna($row['email']);
            $cabinId = $row['cabin'] ? $cabin($row['cabin']) : null;
            $renangId = $row['renang'] ? $renang($row['renang']) : null;

            if (! $penggunaId || ($row['cabin'] && ! $cabinId) || ($row['renang'] && ! $renangId)) {
                continue;
            }

            Reservasi::updateOrCreate(
                [
                    'pengguna_id' => $penggunaId,
                    'cabin_id' => $cabinId,
                    'renang_id' => $renangId,
                ],
                [
                    'tanggal_checkin' => now()->addDays($row['checkin'])->toDateString(),
                    'tanggal_checkout' => now()->addDays($row['checkout'])->toDateString(),
                    'jumlah_tamu' => $row['jumlah_tamu'],
                    'total_harga' => $row['total_harga'],
                    'status' => $row['status'],
                ]
            );
        }
    }
}
