<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $passwordByRole = [
            'admin' => 'admin123',
            'petugas' => 'petugas123',
            'pengunjung' => '123456',
        ];

        $rows = [
            // Admin
            ['nama_pengguna' => 'Bagas Dananjaya', 'email' => 'admin@jiwanta.com', 'nohp' => '081200000001', 'role' => 'admin'],
            ['nama_pengguna' => 'Maya Kartika', 'email' => 'maya@jiwanta.com', 'nohp' => '081200000011', 'role' => 'admin'],
            ['nama_pengguna' => 'Hendra Gunawan', 'email' => 'hendra@jiwanta.com', 'nohp' => '081200000012', 'role' => 'admin'],

            // Petugas
            ['nama_pengguna' => 'Siti Rahma', 'email' => 'petugas@jiwanta.com', 'nohp' => '081200000002', 'role' => 'petugas'],
            ['nama_pengguna' => 'Rian Hidayat', 'email' => 'rian@jiwanta.com', 'nohp' => '081200000013', 'role' => 'petugas'],
            ['nama_pengguna' => 'Lina Marlina', 'email' => 'lina@jiwanta.com', 'nohp' => '081200000014', 'role' => 'petugas'],
            ['nama_pengguna' => 'Eko Prasetyo', 'email' => 'eko@jiwanta.com', 'nohp' => '081200000015', 'role' => 'petugas'],

            // Pengunjung
            ['nama_pengguna' => 'Siti Nurhaliza', 'email' => 'siti@jiwanta.id', 'nohp' => '081200000003', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Ahmad Fauzi', 'email' => 'ahmad@jiwanta.id', 'nohp' => '081200000004', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Dewi Lestari', 'email' => 'dewi@jiwanta.id', 'nohp' => '081200000005', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Rizky Pratama', 'email' => 'rizky@jiwanta.id', 'nohp' => '081200000006', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Putri Amelia', 'email' => 'putri@jiwanta.id', 'nohp' => '081200000007', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Bayu Setiawan', 'email' => 'bayu@jiwanta.id', 'nohp' => '081200000008', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'nohp' => '081311112222', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Ani Wijaya', 'email' => 'ani@gmail.com', 'nohp' => '081333334444', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Dedi Kurniawan', 'email' => 'dedi@gmail.com', 'nohp' => '081355556666', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Rina Marlina', 'email' => 'rina@gmail.com', 'nohp' => '081377778888', 'role' => 'pengunjung'],
        ];

        foreach ($rows as $row) {
            Pengguna::updateOrCreate(
                ['email' => $row['email']],
                [
                    'nama_pengguna' => $row['nama_pengguna'],
                    'nohp' => $row['nohp'],
                    'role' => $row['role'],
                    'password' => Hash::make($passwordByRole[$row['role']]),
                ]
            );
        }
    }
}
