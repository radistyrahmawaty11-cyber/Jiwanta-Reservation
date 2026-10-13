<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_pengguna' => 'Bagas Dananjaya', 'email' => 'admin@jiwanta.com', 'nohp' => '081200000001', 'role' => 'admin', 'password' => 'admin123'],
            ['nama_pengguna' => 'Siti Rahma', 'email' => 'petugas@jiwanta.com', 'nohp' => '081200000002', 'role' => 'petugas', 'password' => 'petugas123'],
            ['nama_pengguna' => 'Siti Nurhaliza', 'email' => 'siti@jiwanta.id', 'nohp' => '081200000003', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Ahmad Fauzi', 'email' => 'ahmad@jiwanta.id', 'nohp' => '081200000004', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Dewi Lestari', 'email' => 'dewi@jiwanta.id', 'nohp' => '081200000005', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Rizky Pratama', 'email' => 'rizky@jiwanta.id', 'nohp' => '081200000006', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Putri Amelia', 'email' => 'putri@jiwanta.id', 'nohp' => '081200000007', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Bayu Setiawan', 'email' => 'bayu@jiwanta.id', 'nohp' => '081200000008', 'role' => 'pengunjung', 'password' => 'password'],
            ['nama_pengguna' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'nohp' => '081311112222', 'role' => 'pengunjung', 'password' => '123456'],
            ['nama_pengguna' => 'Ani Wijaya', 'email' => 'ani@gmail.com', 'nohp' => '081333334444', 'role' => 'pengunjung', 'password' => '123456'],
        ];

        foreach ($rows as $row) {
            Pengguna::updateOrCreate(
                ['email' => $row['email']],
                [
                    'nama_pengguna' => $row['nama_pengguna'],
                    'nohp' => $row['nohp'],
                    'role' => $row['role'],
                    'password' => Hash::make($row['password']),
                ]
            );
        }
    }
}
