<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilPengunjungTest extends TestCase
{
    use DatabaseTransactions;

    private function pengunjung(string $email): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test Pengunjung',
            'email' => $email,
            'password' => 'rahasia123',
            'nohp' => '08120000123',
            'role' => 'pengunjung',
        ]);
    }

    public function test_profil_page_renders(): void
    {
        $user = $this->pengunjung('profil-render@jiwanta.test');

        $this->actingAs($user)
            ->get('/profil')
            ->assertOk()
            ->assertSee($user->nama_pengguna)
            ->assertSee($user->email);
    }

    public function test_pengunjung_can_update_account(): void
    {
        $user = $this->pengunjung('profil-update@jiwanta.test');

        $this->actingAs($user)
            ->post('/profil', [
                'nama_pengguna' => 'Nama Baru',
                'email' => 'profil-baru@jiwanta.test',
                'nohp' => '08129999888',
            ])
            ->assertRedirect(route('profil'));

        $this->assertDatabaseHas('penggunas', [
            'id' => $user->id,
            'nama_pengguna' => 'Nama Baru',
            'email' => 'profil-baru@jiwanta.test',
            'nohp' => '08129999888',
        ]);
    }

    public function test_pengunjung_can_change_password_with_correct_current(): void
    {
        $user = $this->pengunjung('profil-password@jiwanta.test');

        $this->actingAs($user)
            ->post('/profil', [
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'nohp' => $user->nohp,
                'password_lama' => 'rahasia123',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])
            ->assertRedirect(route('profil'));

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }

    public function test_password_change_fails_with_wrong_current(): void
    {
        $user = $this->pengunjung('profil-wrong@jiwanta.test');

        $this->actingAs($user)
            ->post('/profil', [
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'nohp' => $user->nohp,
                'password_lama' => 'salah-sekali',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])
            ->assertSessionHasErrors('password_lama');

        $this->assertTrue(Hash::check('rahasia123', $user->fresh()->password));
    }

    public function test_pengunjung_can_change_profile_photo(): void
    {
        Storage::fake('public');

        $user = $this->pengunjung('profil-foto@jiwanta.test');

        $this->actingAs($user)
            ->post('/profil', [
                'nama_pengguna' => $user->nama_pengguna,
                'email' => $user->email,
                'nohp' => $user->nohp,
                'foto' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertRedirect(route('profil'));

        $user->refresh();

        $this->assertNotNull($user->foto);
        Storage::disk('public')->assertExists($user->foto);
    }

    public function test_non_pengunjung_cannot_access_profil(): void
    {
        $admin = Pengguna::create([
            'nama_pengguna' => 'Test Admin',
            'email' => 'profil-admin@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '08120000999',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->get('/profil')->assertForbidden();
    }
}
