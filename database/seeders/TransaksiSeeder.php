<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $id = fn (string $email) => Pengguna::where('email', $email)->value('id');

        $rows = [
            ['kode_transaksi' => 'TRX-20261009-001', 'email' => 'siti@jiwanta.id', 'tanggal_transaksi' => now()->subMinutes(18), 'total_amount' => 180000, 'total_bayar' => 180000, 'kembalian' => 0, 'metode_pembayaran' => 'BCA', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-002', 'email' => 'ahmad@jiwanta.id', 'tanggal_transaksi' => now()->subMinutes(42), 'total_amount' => 1040000, 'total_bayar' => 1040000, 'kembalian' => 0, 'metode_pembayaran' => 'Mandiri', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-003', 'email' => 'dewi@jiwanta.id', 'tanggal_transaksi' => now()->subHours(2), 'total_amount' => 340000, 'total_bayar' => 340000, 'kembalian' => 0, 'metode_pembayaran' => 'BCA', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-004', 'email' => 'rizky@jiwanta.id', 'tanggal_transaksi' => now()->subHours(3), 'total_amount' => 225000, 'total_bayar' => 225000, 'kembalian' => 0, 'metode_pembayaran' => 'Mandiri', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261009-005', 'email' => 'putri@jiwanta.id', 'tanggal_transaksi' => now()->subHours(4), 'total_amount' => 75000, 'total_bayar' => 100000, 'kembalian' => 25000, 'metode_pembayaran' => 'BCA', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261008-006', 'email' => 'bayu@jiwanta.id', 'tanggal_transaksi' => now()->subDay(), 'total_amount' => 2080000, 'total_bayar' => 2080000, 'kembalian' => 0, 'metode_pembayaran' => 'Mandiri', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261008-007', 'email' => 'siti@jiwanta.id', 'tanggal_transaksi' => now()->subDay()->subHours(2), 'total_amount' => 60000, 'total_bayar' => 60000, 'kembalian' => 0, 'metode_pembayaran' => 'BCA', 'status' => 'gagal'],
            ['kode_transaksi' => 'TRX-20261008-008', 'email' => 'ahmad@jiwanta.id', 'tanggal_transaksi' => now()->subDay()->subHours(5), 'total_amount' => 750000, 'total_bayar' => 750000, 'kembalian' => 0, 'metode_pembayaran' => 'Mandiri', 'status' => 'lunas'],
        ];

        foreach ($rows as $row) {
            $penggunaId = $id($row['email']);

            if (! $penggunaId) {
                continue;
            }

            Transaksi::updateOrCreate(
                ['kode_transaksi' => $row['kode_transaksi']],
                [
                    'pengguna_id'       => $penggunaId,
                    'tanggal_transaksi' => $row['tanggal_transaksi'],
                    'total_amount'      => $row['total_amount'],
                    'total_bayar'       => $row['total_bayar'],
                    'kembalian'         => $row['kembalian'],
                    'metode_pembayaran' => $row['metode_pembayaran'],
                    'status'            => $row['status'],
                ]
            );
        }
    }
}
