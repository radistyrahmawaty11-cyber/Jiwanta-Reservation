<?php

namespace Database\Seeders;

use App\Models\Detailtransaksi;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class DetailTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $transaksi = Transaksi::whereIn('kode_transaksi', [
            'TRX-20261009-001',
            'TRX-20261009-002',
            'TRX-20261009-003',
            'TRX-20261009-004',
            'TRX-20261009-005',
            'TRX-20261008-006',
        ])->orderBy('id')->get();

        if ($transaksi->isEmpty()) {
            return;
        }

        $tiketIds = Tiket::orderBy('id')->pluck('id')->toArray();
        $reservasiIds = Reservasi::orderBy('id')->pluck('id')->toArray();

        foreach ($transaksi as $i => $trx) {
            $useReservasi = $i % 2 === 1 && ! empty($reservasiIds);

            $tiketId = ! $useReservasi && ! empty($tiketIds) ? $tiketIds[$i % count($tiketIds)] : null;
            $reservasiId = $useReservasi ? $reservasiIds[$i % count($reservasiIds)] : null;

            if (! $tiketId && ! $reservasiId) {
                continue;
            }

            Detailtransaksi::updateOrCreate(
                [
                    'transaksi_id' => $trx->id,
                    'tiket_id' => $tiketId,
                    'reservasi_id' => $reservasiId,
                ],
                [
                    'jumlah' => 1,
                    'harga_satuan' => $trx->total_amount,
                    'subtotal' => $trx->total_amount,
                ]
            );
        }
    }
}
