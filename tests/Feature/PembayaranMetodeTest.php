<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PembayaranMetodeTest extends TestCase
{
    use DatabaseTransactions;

    private function pengunjung(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test Metode',
            'email' => 'metode-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08130000'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);
    }

    private function transaksi(Pengguna $pengguna, string $status = 'pending'): Transaksi
    {
        $total = 75000;

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-20261021-'.random_int(100, 999),
            'pengguna_id' => $pengguna->id,
            'tanggal_transaksi' => now(),
            'total_amount' => $total,
            'total_bayar' => $total,
            'kembalian' => 0,
            'metode_pembayaran' => 'transfer',
            'status' => $status,
        ]);

        Detailtransaksi::create([
            'transaksi_id' => $trx->id,
            'tiket_id' => Tiket::orderBy('id')->value('id'),
            'jumlah' => 1,
            'harga_satuan' => $total,
            'subtotal' => $total,
        ]);

        return $trx;
    }

    /**
     * @return array<string, mixed>
     */
    private function sessionOrder(Transaksi $transaksi): array
    {
        return [
            'kode' => $transaksi->kode_transaksi,
            'transaksi_id' => $transaksi->id,
            'total' => (int) $transaksi->total_amount,
            'judul' => 'Tiket Renang',
            'badge' => 'Tiket Masuk & Kolam',
            'tipe' => 'tiket',
            'tamu' => '1 Pengunjung',
            'harga_label' => 'Harga Satuan',
            'harga_value' => 'Rp '.number_format((float) $transaksi->total_amount, 0, ',', '.'),
            'subtotal_label' => 'Subtotal',
            'tanggal' => now()->toDateString(),
            'jumlah' => 1,
            'status' => 'belum',
            'items' => [
                ['nama' => 'Tiket Renang', 'jumlah' => 1, 'satuan' => (int) $transaksi->total_amount, 'subtotal' => (int) $transaksi->total_amount, 'keterangan' => '1 tiket'],
            ],
        ];
    }

    public function test_metode_bank_disimpan_ke_transaksi_dan_session(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user);
        $bank = Bank::where('status', true)->orderBy('id')->firstOrFail();

        $this->actingAs($user)
            ->withSession(['order' => $this->sessionOrder($trx)])
            ->postJson(route('transaksi.metode'), ['metode' => $bank->nama_bank])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $nama = strtolower($bank->nama_bank);
        $label = str_contains($nama, 'bca') ? 'BCA' : (str_contains($nama, 'mandiri') ? 'Mandiri' : $bank->nama_bank);

        $this->assertSame($label, $trx->fresh()->metode_pembayaran);
        $this->assertSame($label, session('order.metode'));
    }

    public function test_metode_online_disimpan_sebagai_midtrans(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user);

        $this->actingAs($user)
            ->withSession(['order' => $this->sessionOrder($trx)])
            ->postJson(route('transaksi.metode'), ['metode' => 'online'])
            ->assertOk()
            ->assertJson(['ok' => true, 'metode' => 'online']);

        $this->assertSame('midtrans', $trx->fresh()->metode_pembayaran);
        $this->assertSame('online', session('order.metode'));
    }

    public function test_metode_tidak_dikenal_ditolak(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user);

        $this->actingAs($user)
            ->withSession(['order' => $this->sessionOrder($trx)])
            ->postJson(route('transaksi.metode'), ['metode' => 'Bank Antah Berantah'])
            ->assertStatus(422);

        $this->assertSame('transfer', $trx->fresh()->metode_pembayaran);
    }

    public function test_pilih_metode_mengembalikan_status_gagal_ke_pending(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user, 'gagal');
        $bank = Bank::where('status', true)->orderBy('id')->firstOrFail();

        $this->actingAs($user)
            ->withSession(['order' => $this->sessionOrder($trx)])
            ->postJson(route('transaksi.metode'), ['metode' => $bank->nama_bank])
            ->assertOk();

        $this->assertSame('pending', $trx->fresh()->status);
    }

    public function test_tiket_belum_lunas_tidak_menampilkan_barcode(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user, 'pending');

        $this->actingAs($user)
            ->get('/tiket')
            ->assertOk()
            ->assertSee('Daftar Booking')
            ->assertSee('Menunggu Pembayaran')
            ->assertDontSee('Secure Token Authenticated');

        $trx->update(['status' => 'lunas']);

        $this->actingAs($user)
            ->get('/tiket')
            ->assertOk()
            ->assertSee('Daftar Booking')
            ->assertSee('Menunggu Konfirmasi Petugas')
            ->assertSee('Secure Token Authenticated')
            ->assertSee('LUNAS')
            // Barcode Code 128 asli (SVG) berisi kode booking tanpa prefiks TRX-.
            ->assertSee('<desc>'.preg_replace('/^TRX-/', '', $trx->kode_transaksi).'</desc>', false);

        $trx->update(['verified_at' => now()]);

        // Terverifikasi = masih boleh discan (tiket belum dipakai di gerbang).
        $this->actingAs($user)
            ->get('/tiket')
            ->assertOk()
            ->assertSee('LUNAS &amp; DIKONFIRMASI', false)
            ->assertSee('Terkonfirmasi')
            ->assertDontSee('Berhasil');

        $trx->update(['checked_in_at' => now()]);

        // Sudah dipakai = stempel "Berhasil" + tiket tidak bisa dipakai lagi.
        $this->actingAs($user)
            ->get('/tiket')
            ->assertOk()
            ->assertSee('LUNAS &amp; SUDAH CHECK-IN', false)
            ->assertSee('Sudah Check-in')
            ->assertSee('Berhasil');
    }

    public function test_halaman_transaksi_menampilkan_metode_midtrans(): void
    {
        $user = $this->pengunjung();
        $trx = $this->transaksi($user);

        $this->actingAs($user)
            ->withSession(['order' => $this->sessionOrder($trx)])
            ->get('/transaksi')
            ->assertOk()
            ->assertSee('Metode Pembayaran')
            ->assertSee('data-method="bca"', false)
            ->assertSee('data-method="mandiri"', false)
            ->assertSee('data-method="qris"', false)
            ->assertSee('Virtual Account')
            ->assertSee('Bill Payment')
            ->assertSee('QR Code')
            ->assertDontSee('Nomor Rekening')
            ->assertDontSee('id="proofCard"', false)
            ->assertSee('aria-pressed="false"', false);
    }
}
