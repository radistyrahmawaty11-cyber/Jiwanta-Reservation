<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Ulasan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UlasanTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $email, string $role = 'pengunjung'): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test '.ucfirst($role),
            'email' => $email,
            'password' => 'rahasia123',
            'nohp' => '08120000'.random_int(100, 999),
            'role' => $role,
        ]);
    }

    public function test_pengunjung_can_submit_ulasan_and_summary_is_dynamic(): void
    {
        $user = $this->pengguna('ulasan-valid@jiwanta.test');

        $response = $this->actingAs($user)->postJson('/ulasan', [
            'rating' => 5,
            'komentar' => 'Pelayanan sangat memuaskan dan tempatnya bersih.',
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'rating' => 5,
            'komentar' => 'Pelayanan sangat memuaskan dan tempatnya bersih.',
        ]);

        $ringkasan = $response->json('ringkasan');
        $this->assertSame(1, $ringkasan['jumlah_ulasan']);
        $this->assertEqualsWithDelta(5.0, $ringkasan['rata_rata'], 0.001);
    }

    public function test_rating_must_be_integer_between_1_and_5(): void
    {
        $user = $this->pengguna('ulasan-rating@jiwanta.test');

        foreach ([0, 6, 4.5, -1] as $rating) {
            $this->actingAs($user)
                ->postJson('/ulasan', [
                    'rating' => $rating,
                    'komentar' => 'Komentar yang cukup panjang.',
                ])
                ->assertStatus(422)
                ->assertJsonValidationErrors('rating');
        }

        $this->assertDatabaseMissing('ulasans', ['pengguna_id' => $user->id]);
    }

    public function test_komentar_must_be_at_least_10_characters(): void
    {
        $user = $this->pengguna('ulasan-komentar@jiwanta.test');

        $this->actingAs($user)
            ->postJson('/ulasan', [
                'rating' => 4,
                'komentar' => 'pendek',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('komentar');

        $this->assertDatabaseMissing('ulasans', ['pengguna_id' => $user->id]);
    }

    public function test_satu_pengunjung_hanya_punya_satu_ulasan(): void
    {
        $user = $this->pengguna('ulasan-update@jiwanta.test');

        $this->actingAs($user)->postJson('/ulasan', [
            'rating' => 3,
            'komentar' => 'Ulasan pertama yang cukup panjang.',
        ])->assertOk();

        $this->actingAs($user)->postJson('/ulasan', [
            'rating' => 5,
            'komentar' => 'Ulasan kedua setelah diperbarui.',
        ])->assertOk();

        $this->assertSame(1, Ulasan::where('pengguna_id', $user->id)->count());
        $this->assertDatabaseHas('ulasans', [
            'pengguna_id' => $user->id,
            'rating' => 5,
            'komentar' => 'Ulasan kedua setelah diperbarui.',
        ]);
    }

    public function test_ringkasan_dihitung_dari_database(): void
    {
        $ratings = [5, 4, 3];
        $viewer = $this->pengguna('ulasan-viewer@jiwanta.test');
        $sebelum = Ulasan::count();

        foreach ($ratings as $i => $rating) {
            $pengguna = $this->pengguna('ulasan-user-'.$i.'@jiwanta.test');
            Ulasan::create([
                'pengguna_id' => $pengguna->id,
                'rating' => $rating,
                'komentar' => 'Komentar ulasan nomor '.$i.' yang panjang.',
            ]);
        }

        $ringkasan = $this->actingAs($viewer)
            ->getJson('/ulasan')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->json('ringkasan');

        $expectedAvg = round((float) Ulasan::avg('rating'), 1);

        $this->assertSame($sebelum + 3, $ringkasan['jumlah_ulasan']);
        $this->assertEqualsWithDelta($expectedAvg, $ringkasan['rata_rata'], 0.001);
    }

    public function test_guest_cannot_access_ulasan_and_admin_cannot_submit(): void
    {
        $this->get('/ulasan')->assertRedirect('/login');

        $admin = $this->pengguna('ulasan-admin@jiwanta.test', 'admin');

        $this->actingAs($admin)
            ->postJson('/ulasan', [
                'rating' => 5,
                'komentar' => 'Admin tidak boleh mengirim ulasan.',
            ])
            ->assertForbidden();
    }
}
