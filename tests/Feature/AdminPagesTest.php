<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_pages_render_successfully(): void
    {
        $admin = Pengguna::create([
            'nama_pengguna' => 'Test Admin',
            'email' => 'test-admin@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200009999',
            'role' => 'admin',
        ]);

        $pages = [
            '/admin/dashboard',
            '/admin/transaksi',
            '/admin/tiket-renang',
            '/admin/cabin',
            '/admin/verifikasi',
            '/admin/manajemen-pengguna',
            '/admin/laporan',
            '/admin/pengaturan',
        ];

        foreach ($pages as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }
}
