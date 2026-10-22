<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use DatabaseTransactions;

    private function aktifkanKredensial(): void
    {
        config([
            'services.google.client_id' => 'client-id-uji',
            'services.google.client_secret' => 'client-secret-uji',
            'services.google.redirect_uri' => 'http://127.0.0.1:8000/auth/google/callback',
        ]);
    }

    public function test_halaman_login_menampilkan_tautan_google(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee(route('auth.google'), false);
        $response->assertDontSee('id="googleForm"', false);
    }

    public function test_tombol_google_mengarah_ke_halaman_pilih_akun_google(): void
    {
        $this->aktifkanKredensial();

        $response = $this->get('/auth/google');

        $response->assertRedirectContains('https://accounts.google.com/o/oauth2/v2/auth');
        $location = $response->headers->get('Location');
        $this->assertStringContainsString('prompt=select_account', $location);
        $this->assertStringContainsString('client_id=client-id-uji', $location);
        $this->assertStringContainsString('scope=openid+email+profile', $location);
    }

    public function test_login_google_tanpa_kredensial_menampilkan_petunjuk(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get('/auth/google');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('whatsapp');
    }

    public function test_callback_google_membuat_pengguna_dengan_email_asli(): void
    {
        $this->aktifkanKredensial();

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'token-abc'], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'budi.google@gmail.com',
                'email_verified' => true,
                'name' => 'Budi Santoso',
                'picture' => 'https://lh3.googleusercontent.com/a/abc',
            ], 200),
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-uji'])
            ->get('/auth/google/callback?code=kode-uji&state=state-uji');

        $response->assertRedirect(route('home'));
        $this->get('/')->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = Pengguna::where('email', 'budi.google@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Budi Santoso', $user->nama_pengguna);
        $this->assertSame('pengunjung', $user->role);
        $this->assertNotSame('google@jiwanta.com', $user->email);
    }

    public function test_callback_google_menolak_state_yang_tidak_valid(): void
    {
        $this->aktifkanKredensial();

        Http::fake();

        $response = $this->withSession(['google_oauth_state' => 'state-asli'])
            ->get('/auth/google/callback?code=kode-uji&state=state-palsu');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('whatsapp');
        $this->assertGuest();
    }

    public function test_callback_google_menolak_email_yang_belum_terverifikasi(): void
    {
        $this->aktifkanKredensial();

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'token-abc'], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'belum.terverifikasi@gmail.com',
                'email_verified' => false,
                'name' => 'Belum Terverifikasi',
            ], 200),
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-uji'])
            ->get('/auth/google/callback?code=kode-uji&state=state-uji');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('whatsapp');
        $this->assertGuest();
        $this->assertDatabaseMissing('penggunas', ['email' => 'belum.terverifikasi@gmail.com']);
    }
}
