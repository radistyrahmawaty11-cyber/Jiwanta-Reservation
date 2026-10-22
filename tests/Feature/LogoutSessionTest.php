<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogoutSessionTest extends TestCase
{
    use DatabaseTransactions;

    private function pengunjung(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Tester Logout',
            'email' => 'tester-logout@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200009'.random_int(100, 999),
            'role' => 'pengunjung',
        ]);
    }

    public function test_logout_mengakhiri_sesi_dan_kembali_ke_halaman_login(): void
    {
        $pengguna = $this->pengunjung();

        $this->actingAs($pengguna)->get('/dashboard')->assertOk();

        $response = $this->actingAs($pengguna)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();

        // Refresh / buka ulang halaman login tetap di halaman login.
        $this->get('/login')->assertOk()->assertSee('whatsapp', false);

        // Halaman terproteksi tidak lagi bisa dibuka.
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_halaman_aplikasi_tidak_disimpan_di_cache_browser(): void
    {
        $pengguna = $this->pengunjung();

        $response = $this->actingAs($pengguna)->get('/dashboard');

        $response->assertOk();
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_respons_logout_membersihkan_cache_browser(): void
    {
        $pengguna = $this->pengunjung();

        $response = $this->actingAs($pengguna)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('cache', (string) $response->headers->get('Clear-Site-Data'));
    }
}
