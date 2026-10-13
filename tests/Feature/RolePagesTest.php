<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RolePagesTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $role, string $email): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test '.ucfirst($role),
            'email' => $email,
            'password' => 'rahasia123',
            'nohp' => '081200001'.random_int(100, 999),
            'role' => $role,
        ]);
    }

    public function test_petugas_dashboard_renders(): void
    {
        $petugas = $this->pengguna('petugas', 'test-petugas@jiwanta.test');

        $this->actingAs($petugas)->get('/petugas/dashboard')->assertOk();
    }

    public function test_pengunjung_pages_render(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'test-pengunjung@jiwanta.test');

        foreach (['/dashboard', '/tiket', '/reservasi', '/transaksi'] as $uri) {
            $this->actingAs($pengunjung)->get($uri)->assertOk();
        }
    }

    public function test_home_redirects_by_role(): void
    {
        $admin = $this->pengguna('admin', 'test-admin-home@jiwanta.test');
        $petugas = $this->pengguna('petugas', 'test-petugas-home@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'test-pengunjung-home@jiwanta.test');

        $this->actingAs($admin)->get('/')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($petugas)->get('/')->assertRedirect(route('petugas.dashboard'));
        $this->actingAs($pengunjung)->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_reservasi_checkout_stores_order_in_session(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'test-checkout@jiwanta.test');

        $this->actingAs($pengunjung)
            ->post('/reservasi/checkout', [
                'tipe' => 'tiket',
                'kategori' => 'premier',
                'jumlah' => 2,
                'total' => 150000,
                'tanggal' => now()->toDateString(),
            ])
            ->assertRedirect(route('transaksi'));

        $order = session('order');
        $this->assertNotNull($order);
        $this->assertSame('tiket', $order['tipe']);
        $this->assertSame(150000, $order['total']);
    }

    public function test_transaksi_kirim_marks_order_as_terkirim(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'test-kirim@jiwanta.test');

        $this->actingAs($pengunjung)
            ->withSession(['order' => ['tipe' => 'tiket', 'total' => 150000, 'status' => 'belum']])
            ->postJson('/transaksi/kirim', ['bukti' => 'bukti.jpg'])
            ->assertOk()
            ->assertJson(['url' => route('tiket')]);

        $this->assertSame('terkirim', session('order.status'));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/petugas/dashboard')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_wrong_role_is_forbidden(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'test-forbidden@jiwanta.test');

        $this->actingAs($pengunjung)->get('/petugas/dashboard')->assertForbidden();
    }
}
