<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KuotaPemesananTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role = 'pengunjung'): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Tamu '.$role,
            'email' => $role.'-'.uniqid().'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200007777',
            'role' => $role,
        ]);
    }

    private function tiket(int $stok = 10): Tiket
    {
        return Tiket::create([
            'nama_tiket' => 'Tiket Uji '.uniqid(),
            'deskripsi' => 'Tiket untuk pengujian kuota.',
            'harga' => 50000,
            'stok' => $stok,
            'kategori' => 'dewasa',
        ]);
    }

    private function cabin(int $kapasitas = 4, string $status = 'tersedia'): Cabin
    {
        return Cabin::create([
            'nama_cabin' => 'Cabin Uji '.uniqid(),
            'deskripsi' => 'Cabin untuk pengujian kuota.',
            'harga_per_malam' => 500000,
            'kapasitas' => $kapasitas,
            'fasilitas' => 'AC',
            'status' => $status,
        ]);
    }

    public function test_pemesanan_tiket_mengurangi_kuota(): void
    {
        $tiket = $this->tiket(10);

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => now()->toDateString(),
                'items' => json_encode([
                    ['tipe' => 'tiket', 'tiket_id' => $tiket->id, 'jumlah' => 3, 'harga_satuan' => 50000],
                ]),
            ])
            ->assertRedirect(route('transaksi'));

        $this->assertSame(7, (int) $tiket->fresh()->stok);
    }

    public function test_pemesanan_cabin_menutup_unit(): void
    {
        $cabin = $this->cabin(4);

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => now()->toDateString(),
                'items' => json_encode([
                    ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 2, 'harga_satuan' => 500000],
                ]),
            ])
            ->assertRedirect(route('transaksi'));

        $this->assertSame('terisi', $cabin->fresh()->status);
    }

    public function test_tiket_dengan_kuota_nol_tidak_bisa_dipesan(): void
    {
        $tiket = $this->tiket(0);
        $before = Transaksi::count();

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => now()->toDateString(),
                'items' => json_encode([
                    ['tipe' => 'tiket', 'tiket_id' => $tiket->id, 'jumlah' => 1, 'harga_satuan' => 50000],
                ]),
            ])
            ->assertRedirect(route('reservasi'))
            ->assertSessionHas('error');

        $this->assertSame($before, Transaksi::count());
    }

    public function test_jumlah_pesanan_melebihi_kuota_ditolak(): void
    {
        $tiket = $this->tiket(2);
        $before = Transaksi::count();

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => now()->toDateString(),
                'items' => json_encode([
                    ['tipe' => 'tiket', 'tiket_id' => $tiket->id, 'jumlah' => 3, 'harga_satuan' => 50000],
                ]),
            ])
            ->assertRedirect(route('reservasi'))
            ->assertSessionHas('error');

        $this->assertSame($before, Transaksi::count());
        $this->assertSame(2, (int) $tiket->fresh()->stok);
    }

    public function test_cabin_dengan_kapasitas_nol_tidak_bisa_dipesan(): void
    {
        $cabin = $this->cabin(0);

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => now()->toDateString(),
                'items' => json_encode([
                    ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 1, 'harga_satuan' => 500000],
                ]),
            ])
            ->assertRedirect(route('reservasi', ['tab' => 'cabin']))
            ->assertSessionHas('error');
    }

    public function test_pemesanan_cabin_per_jam_checkout_hari_sama_dan_harga_tetap(): void
    {
        $cabin = $this->cabin(4);
        $tanggal = now()->toDateString();

        $this->actingAs($this->user())
            ->post('/reservasi/checkout', [
                'tanggal' => $tanggal,
                'items' => json_encode([
                    ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 1, 'durasi' => 3, 'harga_satuan' => 500000, 'subtotal' => 500000],
                ]),
            ])
            ->assertRedirect(route('transaksi'));

        $reservasi = Reservasi::where('cabin_id', $cabin->id)->latest('id')->firstOrFail();

        $this->assertSame($tanggal, (string) $reservasi->tanggal_checkin);
        $this->assertSame($tanggal, (string) $reservasi->tanggal_checkout);
        $this->assertSame(500000, (int) $reservasi->total_harga);
        $this->assertSame('1 paket (3 jam)', session('order.items.0.keterangan'));
    }

    public function test_transaksi_ditolak_mengembalikan_kuota(): void
    {
        $tiket = $this->tiket(10);
        $user = $this->user();

        $this->actingAs($user)->post('/reservasi/checkout', [
            'tanggal' => now()->toDateString(),
            'items' => json_encode([
                ['tipe' => 'tiket', 'tiket_id' => $tiket->id, 'jumlah' => 4, 'harga_satuan' => 50000],
            ]),
        ])->assertRedirect(route('transaksi'));

        $this->assertSame(6, (int) $tiket->fresh()->stok);

        $transaksi = Transaksi::latest('id')->firstOrFail();

        $this->actingAs($this->user('admin'))
            ->post("/admin/transaksi/{$transaksi->id}/reject")
            ->assertOk();

        $this->assertSame(10, (int) $tiket->fresh()->stok);
    }
}
