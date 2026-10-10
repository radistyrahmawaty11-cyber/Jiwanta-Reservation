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
            ['kode_transaksi' => 'TRX-20261009-001', 'pengguna_id' => $id('siti@jiwanta.id'), 'tgl_transaksi' => now()->subMinutes(18), 'total_bayar' => 180000, 'jumlah_bayar' => 180000, 'kembalian' => 0, 'metode' => 'bca', 'bukti' => 'struk_bca_siti.jpg', 'jenis_transaksi' => 'pembayaran_renang', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-002', 'pengguna_id' => $id('ahmad@jiwanta.id'), 'tgl_transaksi' => now()->subMinutes(42), 'total_bayar' => 1040000, 'jumlah_bayar' => 1040000, 'kembalian' => 0, 'metode' => 'mandiri', 'bukti' => 'struk_mandiri_ahmad.jpg', 'jenis_transaksi' => 'pembayaran_cabin', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-003', 'pengguna_id' => $id('dewi@jiwanta.id'), 'tgl_transaksi' => now()->subHours(2), 'total_bayar' => 340000, 'jumlah_bayar' => 340000, 'kembalian' => 0, 'metode' => 'bca', 'bukti' => 'transfer_bca_dewi.jpg', 'jenis_transaksi' => 'pembayaran_cabin', 'status' => 'pending'],
            ['kode_transaksi' => 'TRX-20261009-004', 'pengguna_id' => $id('rizky@jiwanta.id'), 'tgl_transaksi' => now()->subHours(3), 'total_bayar' => 225000, 'jumlah_bayar' => 225000, 'kembalian' => 0, 'metode' => 'mandiri', 'bukti' => 'struk_mandiri_rizky.jpg', 'jenis_transaksi' => 'pembayaran_renang', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261009-005', 'pengguna_id' => $id('putri@jiwanta.id'), 'tgl_transaksi' => now()->subHours(4), 'total_bayar' => 75000, 'jumlah_bayar' => 100000, 'kembalian' => 25000, 'metode' => 'bca', 'bukti' => 'struk_bca_putri.jpg', 'jenis_transaksi' => 'pembayaran_renang', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261008-006', 'pengguna_id' => $id('bayu@jiwanta.id'), 'tgl_transaksi' => now()->subDay(), 'total_bayar' => 2080000, 'jumlah_bayar' => 2080000, 'kembalian' => 0, 'metode' => 'mandiri', 'bukti' => 'transfer_mandiri_bayu.jpg', 'jenis_transaksi' => 'pembayaran_cabin', 'status' => 'lunas'],
            ['kode_transaksi' => 'TRX-20261008-007', 'pengguna_id' => $id('siti@jiwanta.id'), 'tgl_transaksi' => now()->subDay()->subHours(2), 'total_bayar' => 60000, 'jumlah_bayar' => 60000, 'kembalian' => 0, 'metode' => 'bca', 'bukti' => 'struk_bca_siti2.jpg', 'jenis_transaksi' => 'pembayaran_renang', 'status' => 'gagal'],
            ['kode_transaksi' => 'TRX-20261008-008', 'pengguna_id' => $id('ahmad@jiwanta.id'), 'tgl_transaksi' => now()->subDay()->subHours(5), 'total_bayar' => 750000, 'jumlah_bayar' => 750000, 'kembalian' => 0, 'metode' => 'mandiri', 'bukti' => 'struk_mandiri_ahmad2.jpg', 'jenis_transaksi' => 'pembayaran_renang', 'status' => 'lunas'],
        ];

        foreach ($rows as $row) {
            Transaksi::create($row);
        }
    }
}
