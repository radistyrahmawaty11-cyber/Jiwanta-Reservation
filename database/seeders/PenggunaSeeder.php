<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
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
    }
}
