<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Cabin;
use App\Models\Detailtiket;
use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Renang;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Database\Seeders\BankSeeder;
use Database\Seeders\CabinSeeder;
use Database\Seeders\DetailTiketSeeder;
use Database\Seeders\DetailTransaksiSeeder;
use Database\Seeders\PenggunaSeeder;
use Database\Seeders\RenangSeeder;
use Database\Seeders\ReservasiSeeder;
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
        RenangSeeder::class,
        BankSeeder::class,
        TransaksiSeeder::class,
        DetailTiketSeeder::class,
        ReservasiSeeder::class,
        DetailTransaksiSeeder::class,
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
            'renang' => Renang::count(),
            'bank' => Bank::count(),
            'transaksi' => Transaksi::count(),
            'detailtiket' => Detailtiket::count(),
            'reservasi' => Reservasi::count(),
            'detailtransaksi' => Detailtransaksi::count(),
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
        $this->assertDatabaseHas('penggunas', ['email' => 'rian@jiwanta.com', 'role' => 'petugas']);
        $this->assertDatabaseHas('tikets', ['kategori' => 'dewasa']);
        $this->assertDatabaseHas('cabins', ['status' => 'terisi']);
        $this->assertDatabaseHas('renangs', ['nama_kolam' => 'Kolam Air Panas Utama']);
        $this->assertDatabaseHas('banks', ['nama_bank' => 'Bank Central Asia (BCA)']);
        $this->assertDatabaseHas('transaksis', ['kode_transaksi' => 'TRX-20261009-001', 'status' => 'pending']);
        $this->assertDatabaseHas('reservasis', ['status' => 'confirmed']);
        $this->assertDatabaseHas('detailtikets', ['jumlah' => 2]);
    }

    public function test_seeded_transaksi_links_to_seeded_pengguna(): void
    {
        $this->seedAll();

        $transaksi = Transaksi::where('kode_transaksi', 'TRX-20261009-001')->first();

        $this->assertNotNull($transaksi);
        $this->assertNotNull($transaksi->pengguna, 'Relasi transaksi->pengguna tidak valid.');
        $this->assertSame('siti@gmail.com', $transaksi->pengguna->email);
    }

    public function test_seeded_reservasi_links_to_pengguna_and_cabin_or_renang(): void
    {
        $this->seedAll();

        $reservasi = Reservasi::with('pengguna')->first();

        $this->assertNotNull($reservasi);
        $this->assertNotNull($reservasi->pengguna, 'Relasi reservasi->pengguna tidak valid.');
        $this->assertTrue(
            $reservasi->cabin_id !== null || $reservasi->renang_id !== null,
            'Reservasi harus terkait cabin atau renang.'
        );
    }

    public function test_seeded_detail_transaksi_links_to_transaksi(): void
    {
        $this->seedAll();

        $detail = Detailtransaksi::first();

        $this->assertNotNull($detail);
        $this->assertNotNull($detail->transaksi, 'Relasi detailtransaksi->transaksi tidak valid.');
    }
}
