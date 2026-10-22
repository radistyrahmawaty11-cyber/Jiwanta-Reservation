<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nama_bank' => 'Bank Central Asia (BCA)', 'nomor_rekening' => '4370-9008-21', 'atas_nama' => 'PT Jiwanta Resort', 'status' => true],
            ['nama_bank' => 'Bank Mandiri', 'nomor_rekening' => '1300-1234-5678', 'atas_nama' => 'PT Jiwanta Resort', 'status' => true],
        ];

        foreach ($rows as $row) {
            Bank::updateOrCreate(['nomor_rekening' => $row['nomor_rekening']], $row);
        }
    }
}
