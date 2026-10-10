<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
=======
<<<<<<< HEAD
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
=======
use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
>>>>>>> cda8ee55bbea2f98005fe193b9416e649bb5bb95

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> cda8ee55bbea2f98005fe193b9416e649bb5bb95
        $rows = [
            ['nama_pengguna' => 'Bagas Dananjaya', 'email' => 'admin@jiwanta.id', 'nohp' => '081200000001', 'role' => 'admin'],
            ['nama_pengguna' => 'Rian Hidayat', 'email' => 'petugas@jiwanta.id', 'nohp' => '081200000002', 'role' => 'petugas'],
            ['nama_pengguna' => 'Siti Nurhaliza', 'email' => 'siti@jiwanta.id', 'nohp' => '081200000003', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Ahmad Fauzi', 'email' => 'ahmad@jiwanta.id', 'nohp' => '081200000004', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Dewi Lestari', 'email' => 'dewi@jiwanta.id', 'nohp' => '081200000005', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Rizky Pratama', 'email' => 'rizky@jiwanta.id', 'nohp' => '081200000006', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Putri Amelia', 'email' => 'putri@jiwanta.id', 'nohp' => '081200000007', 'role' => 'pengunjung'],
            ['nama_pengguna' => 'Bayu Setiawan', 'email' => 'bayu@jiwanta.id', 'nohp' => '081200000008', 'role' => 'pengunjung'],
        ];

        foreach ($rows as $row) {
            Pengguna::create($row + ['password' => 'password']);
        }
<<<<<<< HEAD
=======
=======
        // 1. Buat Akun ADMIN
        Pengguna::create([
            'nama' => 'Admin Jiwanta',
            'email' => 'admin@jiwanta.com',
            'password' => Hash::make('123456'), // Password di-hash otomatis
            'nohp' => '081234567890',
            'role' => 'admin',
        ]);

        // 2. Buat Akun PETUGAS
        Pengguna::create([
            'nama' => 'Petugas Jiwanta',
            'email' => 'petugas@jiwanta.com',
            'password' => Hash::make('123456'),
            'nohp' => '081234567891',
            'role' => 'petugas',
        ]);

        // 3. Buat Akun PENGUNJUNG
        Pengguna::create([
            'nama' => 'Budi Pengunjung',
            'email' => 'pengunjung@jiwanta.com',
            'password' => Hash::make('123456'),
            'nohp' => '081234567892',
            'role' => 'pengunjung',
        ]);
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
>>>>>>> cda8ee55bbea2f98005fe193b9416e649bb5bb95
    }
}
