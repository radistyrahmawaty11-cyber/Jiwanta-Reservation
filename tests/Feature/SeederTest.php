<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Tiket;
use App\Models\Transaksi;
use Database\Seeders\BankSeeder;
use Database\Seeders\CabinSeeder;
use Database\Seeders\PenggunaSeeder;
use Database\Seeders\TiketSeeder;
use Database\Seeders\TransaksiSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Seeder yang diuji, sudah dalam urutan dependency yang benar.
     */
    private array $seeders = [
        PenggunaSeeder::class,
        CabinSeeder::class,
        TiketSeeder::class,
        BankSeeder::class,
        TransaksiSeeder::class,
    ];

    private function seedAll(): void
    {
        foreach ($this->seeders as $seeder) {
            $this->seed($seeder);
        }
    }

    private function counts(): array
    {
        return [
            'pengguna' => Pengguna::count(),
            'cabin' => Cabin::count(),
            'tiket' => Tiket::count(),
            'bank' => Bank::count(),
            'transaksi' => Transaksi::count(),
        ];
    }

    public function test_seeders_are_idempotent(): void
    {
        $this->seedAll();
        $before = $this->counts();

        $this->seedAll();

        $this->assertSame($before, $this->counts(), 'Seeder menghasilkan baris duplikat saat dijalankan ulang.');
    }

    public function test_seeders_write_expected_columns(): void
    {
        $this->seedAll();

        $this->assertDatabaseHas('penggunas', ['email' => 'admin@jiwanta.com', 'role' => 'admin']);
        $this->assertDatabaseHas('tikets', ['kategori' => 'dewasa']);
        $this->assertDatabaseHas('cabins', ['status' => 'terisi']);
        $this->assertDatabaseHas('banks', ['nama_bank' => 'Bank Central Asia (BCA)']);
        $this->assertDatabaseHas('transaksis', ['kode_transaksi' => 'TRX-20261009-001', 'status' => 'pending']);
    }

    public function test_seeded_transaksi_links_to_seeded_pengguna(): void
    {
        $this->seedAll();

        $transaksi = Transaksi::where('kode_transaksi', 'TRX-20261009-001')->first();

        $this->assertNotNull($transaksi);
        $this->assertNotNull($transaksi->pengguna, 'Relasi transaksi->pengguna tidak valid.');
        $this->assertSame('siti@jiwanta.id', $transaksi->pengguna->email);
    }
}
