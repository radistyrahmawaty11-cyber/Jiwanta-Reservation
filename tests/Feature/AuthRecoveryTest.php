<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthRecoveryTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $email = 'recovery@jiwanta.test'): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test Recovery',
            'email' => $email,
            'password' => 'rahasia123',
            'nohp' => '081700001'.random_int(100, 999),
            'role' => 'pengunjung',
        ]);
    }

    public function test_forgot_password_page_renders(): void
    {
        $this->get('/forgot-password')->assertOk()->assertSee('LUPA SANDI');
    }

    public function test_registration_persists_user_and_redirects_to_dashboard(): void
    {
        $this->post('/register', [
            'nama_pengguna' => 'Pendaftar Baru',
            'email' => 'pendaftar-baru@jiwanta.test',
            'nohp' => '081299998888',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $user = Pengguna::where('email', 'pendaftar-baru@jiwanta.test')->first();

        $this->assertNotNull($user);
        $this->assertSame('pengunjung', $user->role);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $user = $this->pengguna('duplikat@jiwanta.test');

        $this->post('/register', [
            'nama_pengguna' => 'Pendaftar Kedua',
            'email' => $user->email,
            'nohp' => '081299997777',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_with_remember_me_succeeds(): void
    {
        $user = $this->pengguna('remember@jiwanta.test');

        $this->post('/login', [
            'whatsapp' => $user->email,
            'password' => 'rahasia123',
            'remember' => 'on',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_send_reset_link_creates_token_for_known_account(): void
    {
        $user = $this->pengguna('reset-known@jiwanta.test');

        $this->post('/forgot-password', ['identifier' => $user->email])
            ->assertSessionHas('reset_url')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_send_reset_link_rejects_unknown_account(): void
    {
        $this->post('/forgot-password', ['identifier' => 'tidak-ada@jiwanta.test'])
            ->assertSessionHasErrors('identifier');
    }

    public function test_reset_password_updates_password(): void
    {
        $user = $this->pengguna('reset-update@jiwanta.test');

        $token = Password::broker()->createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'katasandi-baru',
            'password_confirmation' => 'katasandi-baru',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('katasandi-baru', $user->fresh()->password));
    }

    public function test_otp_flow_logs_the_matching_user_in(): void
    {
        $user = $this->pengguna('otp-flow@jiwanta.test');

        $send = $this->postJson('/otp/send', ['whatsapp' => $user->nohp])->assertOk();
        $code = $send->json('dev_code');

        $this->assertNotNull($code);

        $this->postJson('/otp/verify', ['code' => $code])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_otp_verify_rejects_wrong_code(): void
    {
        $user = $this->pengguna('otp-wrong@jiwanta.test');

        $this->postJson('/otp/send', ['whatsapp' => $user->email])->assertOk();

        $this->postJson('/otp/verify', ['code' => '000000'])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);

        $this->assertGuest();
    }

    public function test_google_login_creates_and_authenticates_demo_account(): void
    {
        $this->post('/auth/google')->assertRedirect();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('penggunas', ['email' => 'google@jiwanta.com']);
    }
}
