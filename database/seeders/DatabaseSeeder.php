<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            CabinSeeder::class,
            TiketSeeder::class,
            RenangSeeder::class,
            BankSeeder::class,
            TransaksiSeeder::class,
            DetailTiketSeeder::class,
            ReservasiSeeder::class,
            DetailTransaksiSeeder::class,
        ]);

        $pengunjungIds = Pengguna::where('role', 'pengunjung')->pluck('id')->toArray();

        if (empty($pengunjungIds)) {
            $this->command->warn('Tidak ada pengguna dengan role pengunjung, transaksi dummy dilewati.');

            return;
        }

        $metodes = ['BCA', 'Mandiri', 'Tunai'];
        $statuses = ['lunas', 'lunas', 'lunas', 'pending', 'gagal'];

        for ($i = 1; $i <= 25; $i++) {
            $tanggal = Carbon::now()->subDays($i % 15)->subHours($i % 12)->subMinutes($i % 60);
            $total = (3 + ($i % 13)) * 50000;
            $bayar = $total + (($i % 2) * 50000);

            Transaksi::updateOrCreate(
                ['kode_transaksi' => 'TRX-GEN-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'pengguna_id'       => $pengunjungIds[array_rand($pengunjungIds)],
                    'tanggal_transaksi' => $tanggal,
                    'total_amount'      => $total,
                    'total_bayar'       => $bayar,
                    'kembalian'         => max(0, $bayar - $total),
                    'metode_pembayaran' => $metodes[array_rand($metodes)],
                    'status'            => $statuses[array_rand($statuses)],
                ]
            );
        }

        $this->command->info('Data seeder (Pengguna, Cabin, Tiket, Renang, Bank, Transaksi, DetailTiket, Reservasi & DetailTransaksi) berhasil di-generate!');
    }
}
