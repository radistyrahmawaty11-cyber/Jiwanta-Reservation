<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminKuotaTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Admin Kuota',
            'email' => 'kuota-'.uniqid().'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200009999',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_update_tiket_kuota(): void
    {
        $tiket = Tiket::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->putJson("/admin/tiket/{$tiket->id}/kuota", ['cap' => 500])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.cap', 500);

        $this->assertSame(500, (int) $tiket->fresh()->stok);
    }

    public function test_tiket_kuota_can_be_set_to_zero_to_close_sales(): void
    {
        $tiket = Tiket::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->putJson("/admin/tiket/{$tiket->id}/kuota", ['cap' => 0])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.cap', 0);

        $this->assertSame(0, (int) $tiket->fresh()->stok);
    }

    public function test_admin_can_update_cabin_kapasitas(): void
    {
        $cabin = Cabin::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->putJson("/admin/cabin/{$cabin->id}/kapasitas", ['kapasitas' => 6])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('kapasitas', 6);

        $this->assertSame(6, (int) $cabin->fresh()->kapasitas);
    }

    public function test_cabin_kapasitas_can_be_set_to_zero(): void
    {
        $cabin = Cabin::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->putJson("/admin/cabin/{$cabin->id}/kapasitas", ['kapasitas' => 0])
            ->assertOk()
            ->assertJsonPath('kapasitas', 0);

        $this->assertSame(0, (int) $cabin->fresh()->kapasitas);
    }

    public function test_cabin_kapasitas_rejects_invalid_value(): void
    {
        $cabin = Cabin::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->putJson("/admin/cabin/{$cabin->id}/kapasitas", ['kapasitas' => -1])
            ->assertStatus(422);
    }

    public function test_dashboard_quota_modal_lists_tickets_and_cabins(): void
    {
        $tiket = Tiket::orderBy('id')->firstOrFail();
        $cabin = Cabin::orderBy('id')->firstOrFail();

        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('data-quota-id="'.$tiket->id.'"', false)
            ->assertSee('data-cabin-id="'.$cabin->id.'"', false)
            ->assertSee('Simpan Kuota');
    }
}
