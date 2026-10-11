<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama_pengguna' => 'Admin Jiwanta',
                'email' => 'admin@jiwanta.com',
                'password' => Hash::make('123456'),
                'nohp' => '081234567890',
                'role' => 'admin',
            ],
            [
                'nama_pengguna' => 'Budi Pengunjung',
                'email' => 'budi@jiwanta.com',
                'password' => Hash::make('123456'),
                'nohp' => '081234567891',
                'role' => 'pengunjung',
            ],
            [
                'nama_pengguna' => 'Siti Petugas',
                'email' => 'siti@jiwanta.com',
                'password' => Hash::make('123456'),
                'nohp' => '081234567892',
                'role' => 'petugas',
            ],
        ];

        // updateOrCreate akan mengecek email. Jika ada, di-update. Jika tidak, dibuat baru.
        foreach ($users as $user) {
            Pengguna::updateOrCreate(
                ['email' => $user['email']], // Kondisi pencarian
                $user                        // Data yang diisi
            );
        }
    }
}
