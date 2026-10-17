<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenggunaManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Admin Uji',
            'email' => 'admin-uji-'.random_int(1000, 9999).'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '08120000'.random_int(1000, 9999),
            'role' => 'admin',
        ]);
    }

    public function test_manajemen_pengguna_lists_real_database_users(): void
    {
        $admin = $this->admin();
        $target = Pengguna::create([
            'nama_pengguna' => 'Budi Pengunjung',
            'email' => 'budi-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08139999'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);

        $this->actingAs($admin)
            ->get('/admin/manajemen-pengguna')
            ->assertOk()
            ->assertSee($target->nama_pengguna)
            ->assertSee($target->email);
    }

    public function test_admin_can_create_pengguna(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/admin/pengguna', [
            'nama_pengguna' => 'Petugas Baru',
            'email' => 'petugas-baru-'.random_int(1000, 9999).'@jiwanta.test',
            'nohp' => '08127777'.random_int(1000, 9999),
            'role' => 'petugas',
            'password' => 'rahasia123',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('penggunas', ['nama_pengguna' => 'Petugas Baru', 'role' => 'petugas']);
    }

    public function test_admin_can_update_pengguna(): void
    {
        $admin = $this->admin();
        $target = Pengguna::create([
            'nama_pengguna' => 'Nama Lama',
            'email' => 'lama-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08135555'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);

        $this->actingAs($admin)->putJson('/admin/pengguna/'.$target->id, [
            'nama_pengguna' => 'Nama Baru',
            'email' => $target->email,
            'nohp' => $target->nohp,
            'role' => 'petugas',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('penggunas', ['id' => $target->id, 'nama_pengguna' => 'Nama Baru', 'role' => 'petugas']);
    }

    public function test_admin_can_delete_pengguna(): void
    {
        $admin = $this->admin();
        $target = Pengguna::create([
            'nama_pengguna' => 'Akan Dihapus',
            'email' => 'hapus-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08136666'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);

        $this->actingAs($admin)
            ->deleteJson('/admin/pengguna/'.$target->id)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('penggunas', ['id' => $target->id]);
    }

    public function test_reservasi_availability_returns_real_cabin_status(): void
    {
        $pengunjung = Pengguna::create([
            'nama_pengguna' => 'Cek Ketersediaan',
            'email' => 'avail-'.random_int(1000, 9999).'@gmail.com',
            'password' => 'rahasia123',
            'nohp' => '08138888'.random_int(1000, 9999),
            'role' => 'pengunjung',
        ]);

        $cabin = Cabin::orderBy('id')->first();
        $cabin->update(['status' => 'terisi']);

        $this->actingAs($pengunjung)
            ->getJson('/reservasi/availability')
            ->assertOk()
            ->assertJsonFragment(['key' => $cabin->id, 'sold' => true]);
    }
}
