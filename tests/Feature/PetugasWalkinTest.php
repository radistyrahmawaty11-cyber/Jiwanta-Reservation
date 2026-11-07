<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PetugasWalkinTest extends TestCase
{
    use DatabaseTransactions;

    private function petugas(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Petugas Walkin',
            'email' => 'walkin-'.uniqid().'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '08130000'.random_int(1000, 9999),
            'role' => 'petugas',
        ]);
    }

    public function test_petugas_can_create_walkin_ticket(): void
    {
        $petugas = $this->petugas();
        $tiket = Tiket::create([
            'nama_tiket' => 'Walkin Tiket '.uniqid(),
            'harga' => 50000,
            'stok' => 10,
            'kategori' => 'dewasa',
        ]);

        $res = $this->actingAs($petugas)->postJson('/petugas/walkin', [
            'nama' => 'Tamu Walkin',
            'nohp' => '081234567890',
            'layanan' => 'tiket:'.$tiket->id,
            'jumlah' => 2,
        ]);

        $res->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('transaksi.pax', 2)
            ->assertJsonPath('transaksi.status', 'lunas');

        $kode = $res->json('transaksi.code');
        $transaksi = Transaksi::where('kode_transaksi', $kode)->firstOrFail();

        $this->assertSame('lunas', $transaksi->status);
        $this->assertSame('Tunai', $transaksi->metode_pembayaran);
        $this->assertSame(100000.0, (float) $transaksi->total_amount);
        $this->assertNotNull($transaksi->checked_in_at);
        $this->assertSame('pengunjung', $transaksi->pengguna->role);
        $this->assertSame('Tamu Walkin', $transaksi->pengguna->nama_pengguna);
        $this->assertSame(8, $tiket->fresh()->stok);
        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $transaksi->id,
            'tiket_id' => $tiket->id,
            'jumlah' => 2,
        ]);
    }

    public function test_petugas_can_create_walkin_cabin(): void
    {
        $petugas = $this->petugas();
        $cabin = Cabin::create([
            'nama_cabin' => 'Walkin Cabin '.uniqid(),
            'harga_per_malam' => 1000000,
            'kapasitas' => 4,
            'status' => 'tersedia',
        ]);

        $checkin = Carbon::today();

        $res = $this->actingAs($petugas)->postJson('/petugas/walkin', [
            'nama' => 'Tamu Cabin',
            'nohp' => '081234567891',
            'layanan' => 'cabin:'.$cabin->id,
            'tanggal' => $checkin->toDateString(),
            'malam' => 2,
        ]);

        $res->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('transaksi.pax', 4);

        $kode = $res->json('transaksi.code');
        $transaksi = Transaksi::where('kode_transaksi', $kode)->firstOrFail();

        $this->assertSame('lunas', $transaksi->status);
        $this->assertSame(2000000.0, (float) $transaksi->total_amount);
        $this->assertSame('terisi', $cabin->fresh()->status);

        $reservasi = Reservasi::where('pengguna_id', $transaksi->pengguna_id)->firstOrFail();
        $this->assertSame('confirmed', $reservasi->status);
        $this->assertSame($cabin->id, $reservasi->cabin_id);
        $this->assertSame($checkin->toDateString(), (string) $reservasi->tanggal_checkin);
        $this->assertSame($checkin->copy()->addDays(2)->toDateString(), (string) $reservasi->tanggal_checkout);
        $this->assertSame(4, (int) $reservasi->jumlah_tamu);

        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $transaksi->id,
            'reservasi_id' => $reservasi->id,
            'jumlah' => 2,
            'subtotal' => 2000000,
        ]);
    }

    public function test_walkin_ticket_rejects_insufficient_stock(): void
    {
        $petugas = $this->petugas();
        $tiket = Tiket::create([
            'nama_tiket' => 'Walkin Habis '.uniqid(),
            'harga' => 50000,
            'stok' => 1,
            'kategori' => 'dewasa',
        ]);

        $this->actingAs($petugas)->postJson('/petugas/walkin', [
            'nama' => 'Tamu Berlebih',
            'nohp' => '081234567892',
            'layanan' => 'tiket:'.$tiket->id,
            'jumlah' => 2,
        ])->assertStatus(422)->assertJsonPath('ok', false);

        $this->assertSame(1, $tiket->fresh()->stok);
        $this->assertSame(0, Pengguna::where('nama_pengguna', 'Tamu Berlebih')->count());
    }
}
