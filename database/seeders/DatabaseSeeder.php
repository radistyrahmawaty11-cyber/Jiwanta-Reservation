<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Pengguna (Users) - Sudah pasti aman
        $users = [
            [
                'nama_pengguna' => 'Bagas Dananjaya',
                'email' => 'admin@jiwanta.com',
                'nohp' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('admin123')
            ],
            [
                'nama_pengguna' => 'Siti Rahma',
                'email' => 'petugas@jiwanta.com',
                'nohp' => '081298765432',
                'role' => 'petugas',
                'password' => Hash::make('petugas123')
            ],
            [
                'nama_pengguna' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'nohp' => '081311112222',
                'role' => 'pengunjung',
                'password' => Hash::make('123456')
            ],
            [
                'nama_pengguna' => 'Ani Wijaya',
                'email' => 'ani@gmail.com',
                'nohp' => '081333334444',
                'role' => 'pengunjung',
                'password' => Hash::make('123456')
            ],
        ];

        foreach ($users as $user) {
            Pengguna::updateOrCreate(['email' => $user['email']], $user);
        }

        // 2. Buat Transaksi Dummy (Agar Dashboard & Halaman Transaksi Terlihat Hidup)
        // Saya lewati Tiket & Cabin untuk menghindari error ENUM kategori yang ketat di database kamu.
        $pengunjungIds = Pengguna::where('role', 'pengunjung')->pluck('id')->toArray();
        $metodes = ['BCA', 'Mandiri', 'Tunai'];
        $statuses = ['lunas', 'lunas', 'lunas', 'pending', 'gagal'];

        for ($i = 1; $i <= 25; $i++) {
            $tanggal = Carbon::now()->subDays(rand(0, 14))->subHours(rand(0, 12))->subMinutes(rand(0, 59));
            $total = rand(3, 15) * 50000;
            $bayar = $total + (rand(0, 1) * 50000);
            $kembali = max(0, $bayar - $total);

            Transaksi::create([
                'kode_transaksi'    => 'TRX-' . $tanggal->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'pengguna_id'       => $pengunjungIds[array_rand($pengunjungIds)],
                'tanggal_transaksi' => $tanggal,
                'total_amount'      => $total,
                'total_bayar'       => $total,
                'kembalian'         => $kembali,
                'metode_pembayaran' => $metodes[array_rand($metodes)],
                'status'            => $statuses[array_rand($statuses)],
            ]);
        }

        $this->command->info('✅ Data dummy (Pengguna & Transaksi) berhasil di-generate! Dashboard sekarang terlihat hidup.');
    }
}
