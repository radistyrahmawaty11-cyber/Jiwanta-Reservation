<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengunjungFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function pengunjung(?string $email = null): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test Pengunjung',
            'email' => $email ?? 'flow-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08130000'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);
    }

    public function test_dashboard_shows_seeded_catalog(): void
    {
        $tiket = Tiket::orderBy('id')->first();
        $cabin = Cabin::orderBy('id')->first();

        $this->actingAs($this->pengunjung())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee($tiket->nama_tiket)
            ->assertSee($cabin->nama_cabin);
    }

    public function test_reservasi_shows_categories_and_units(): void
    {
        $tiket = Tiket::orderBy('id')->first();
        $cabin = Cabin::orderBy('id')->first();

        $this->actingAs($this->pengunjung())
            ->get('/reservasi')
            ->assertOk()
            ->assertSee($tiket->nama_tiket)
            ->assertSee($cabin->nama_cabin);
    }

    public function test_checkout_persists_transaksi_and_detail(): void
    {
        $user = $this->pengunjung();
        $tiket = Tiket::orderBy('id')->first();
        $total = (int) $tiket->harga * 2;

        $this->actingAs($user)
            ->post('/reservasi/checkout', [
                'tipe' => 'tiket',
                'kategori' => $tiket->id,
                'jumlah' => 2,
                'total' => $total,
                'tanggal' => now()->toDateString(),
            ])
            ->assertRedirect(route('transaksi'));

        $order = session('order');
        $this->assertNotNull($order['transaksi_id'] ?? null);

        $trx = Transaksi::find($order['transaksi_id']);
        $this->assertSame($user->id, $trx->pengguna_id);
        $this->assertSame($total, (int) $trx->total_amount);

        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $trx->id,
            'tiket_id' => $tiket->id,
            'jumlah' => 2,
            'subtotal' => $total,
        ]);
    }

    public function test_checkout_cabin_creates_reservasi(): void
    {
        $user = $this->pengunjung();
        $cabin = Cabin::where('status', 'tersedia')->orderBy('id')->first();
        $checkin = now()->toDateString();

        $this->actingAs($user)
            ->post('/reservasi/checkout', [
                'tipe' => 'cabin',
                'unit' => $cabin->id,
                'malam' => 2,
                'total' => $cabin->harga_per_malam * 2,
                'tanggal' => $checkin,
            ])
            ->assertRedirect(route('transaksi'));

        $reservasi = Reservasi::where('pengguna_id', $user->id)->latest('id')->first();
        $this->assertNotNull($reservasi);
        $this->assertSame($cabin->id, $reservasi->cabin_id);
        $this->assertSame('pending', $reservasi->status);
        $this->assertSame(
            Carbon::parse($checkin)->addDays(2)->toDateString(),
            (string) $reservasi->tanggal_checkout
        );

        $trx = Transaksi::find(session('order.transaksi_id'));
        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $trx->id,
            'reservasi_id' => $reservasi->id,
            'jumlah' => 2,
        ]);
    }

    public function test_checkout_multi_items_tiket_dan_cabin(): void
    {
        $user = $this->pengunjung();
        $tiket = Tiket::orderBy('id')->first();
        $cabin = Cabin::where('status', 'tersedia')->orderBy('id')->first();
        $checkin = now()->toDateString();
        $expected = (int) ($tiket->harga * 2) + (int) ($cabin->harga_per_malam * 3);

        $items = [
            ['tipe' => 'tiket', 'tiket_id' => $tiket->id, 'jumlah' => 2, 'harga_satuan' => $tiket->harga],
            ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 3, 'harga_satuan' => $cabin->harga_per_malam],
        ];

        $this->actingAs($user)
            ->post('/reservasi/checkout', [
                'tipe' => 'mixed',
                'items' => json_encode($items),
                'total' => $expected,
                'tanggal' => $checkin,
            ])
            ->assertRedirect(route('transaksi'));

        $order = session('order');
        $this->assertSame('mixed', $order['tipe']);
        $this->assertCount(2, $order['items']);

        $trx = Transaksi::find($order['transaksi_id']);
        $this->assertSame($expected, (int) $trx->total_amount);
        $this->assertSame(2, $trx->detailtransaksis()->count());

        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $trx->id,
            'tiket_id' => $tiket->id,
            'jumlah' => 2,
        ]);
        $this->assertDatabaseHas('detailtransaksis', [
            'transaksi_id' => $trx->id,
            'jumlah' => 3,
        ]);

        $reservasi = Reservasi::where('pengguna_id', $user->id)->latest('id')->first();
        $this->assertNotNull($reservasi);
        $this->assertSame($cabin->id, $reservasi->cabin_id);
        $this->assertSame(
            Carbon::parse($checkin)->addDays(3)->toDateString(),
            (string) $reservasi->tanggal_checkout
        );
    }

    public function test_tiket_lists_user_transaksi(): void
    {
        $user = $this->pengunjung();
        $tiket = Tiket::orderBy('id')->first();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-20261013-'.random_int(100, 999),
            'pengguna_id' => $user->id,
            'tanggal_transaksi' => now(),
            'total_amount' => $tiket->harga,
            'total_bayar' => $tiket->harga,
            'kembalian' => 0,
            'metode_pembayaran' => 'BCA',
            'status' => 'lunas',
        ]);

        Detailtransaksi::create([
            'transaksi_id' => $trx->id,
            'tiket_id' => $tiket->id,
            'jumlah' => 1,
            'harga_satuan' => $tiket->harga,
            'subtotal' => $tiket->harga,
        ]);

        $other = $this->pengunjung();
        $asing = Transaksi::create([
            'kode_transaksi' => 'TRX-20261013-'.random_int(100, 999),
            'pengguna_id' => $other->id,
            'tanggal_transaksi' => now(),
            'total_amount' => 50000,
            'total_bayar' => 50000,
            'kembalian' => 0,
            'metode_pembayaran' => 'BCA',
            'status' => 'lunas',
        ]);

        $this->actingAs($user)
            ->get('/tiket')
            ->assertOk()
            ->assertSee($trx->kode_transaksi)
            ->assertDontSee($asing->kode_transaksi);
    }

    public function test_kirim_marks_order_as_terkirim(): void
    {
        $user = $this->pengunjung();

        $this->actingAs($user)
            ->withSession(['order' => [
                'tipe' => 'tiket',
                'judul' => 'Tiket Renang Classic',
                'total' => 60000,
                'kode' => 'TRX-20261013-500',
                'status' => 'belum',
            ]])
            ->postJson('/transaksi/kirim', ['bukti' => 'bukti.png'])
            ->assertOk()
            ->assertJson(['url' => route('tiket')]);

        $this->assertSame('terkirim', session('order.status'));
    }

    public function test_kirim_stores_bukti_transfer_upload_to_transaksi(): void
    {
        Storage::fake('public');
        $user = $this->pengunjung();

        $trx = Transaksi::create([
            'pengguna_id' => $user->id,
            'kode_transaksi' => 'TRX-20261013-501',
            'total_amount' => 60000,
            'total_bayar' => 60000,
            'kembalian' => 0,
            'metode_pembayaran' => 'Transfer',
            'status' => 'pending',
            'tanggal_transaksi' => Carbon::now(),
        ]);

        $this->actingAs($user)
            ->withSession(['order' => [
                'tipe' => 'tiket',
                'total' => 60000,
                'kode' => $trx->kode_transaksi,
                'status' => 'belum',
                'transaksi_id' => $trx->id,
            ]])
            ->post('/transaksi/kirim', [
                'bukti' => UploadedFile::fake()->image('bukti.png', 120, 240),
            ])
            ->assertOk()
            ->assertJson(['url' => route('tiket')]);

        $trx->refresh();
        $this->assertNotNull($trx->bukti_transfer);
        Storage::disk('public')->assertExists($trx->bukti_transfer);
    }
}
