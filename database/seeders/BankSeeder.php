<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['kode' => 'bca', 'nama' => 'Bank Central Asia (BCA)', 'nomor_rekening' => '4370-9008-21', 'atas_nama' => 'PT Jiwanta Resort', 'cabang' => 'KCU Kopo Bandung', 'is_utama' => true],
            ['kode' => 'mandiri', 'nama' => 'Bank Mandiri', 'nomor_rekening' => '1300-1234-5678', 'atas_nama' => 'PT Jiwanta Resort', 'cabang' => 'KCP Soreang', 'is_utama' => false],
        ];

        foreach ($rows as $row) {
            Bank::create($row);
        }
    }
}
