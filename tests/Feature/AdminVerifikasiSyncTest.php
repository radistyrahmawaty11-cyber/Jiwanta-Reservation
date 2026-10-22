<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminVerifikasiSyncTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $role, string $email): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Test '.ucfirst($role),
            'email' => $email,
            'password' => 'rahasia123',
            'nohp' => '08130000'.random_int(1000, 9999),
            'role' => $role,
        ]);
    }

    private function transaksi(Pengguna $pengguna, string $status = 'pending'): Transaksi
    {
        return Transaksi::create([
            'kode_transaksi' => 'TRX-20261013-'.random_int(100, 999),
            'pengguna_id' => $pengguna->id,
            'tanggal_transaksi' => now(),
            'total_amount' => 75000,
            'total_bayar' => 75000,
            'kembalian' => 0,
            'metode_pembayaran' => 'BCA',
            'status' => $status,
        ]);
    }

    public function test_admin_verify_updates_database_status(): void
    {
        $admin = $this->pengguna('admin', 'admin-verif@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'admin-verif-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($admin)
            ->postJson("/admin/transaksi/{$transaksi->id}/verify")
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $this->assertSame('lunas', $transaksi->fresh()->status);
    }

    public function test_admin_reject_updates_database_status(): void
    {
        $admin = $this->pengguna('admin', 'admin-reject@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'admin-reject-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($admin)
            ->postJson("/admin/transaksi/{$transaksi->id}/reject")
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'gagal']);

        $this->assertSame('gagal', $transaksi->fresh()->status);
    }

    public function test_feed_drops_verified_transaction_from_pending_queue(): void
    {
        $admin = $this->pengguna('admin', 'admin-feed@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'admin-feed-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($admin)
            ->postJson("/admin/transaksi/{$transaksi->id}/verify")
            ->assertOk();

        $rows = $this->actingAs($admin)
            ->getJson('/admin/feed')
            ->assertOk()
            ->json('data.pending_rows');

        $kodes = array_column($rows, 'kode');
        $this->assertNotContains($transaksi->kode_transaksi, $kodes);
    }

    public function test_verified_transaction_shows_lunas_in_history_feed(): void
    {
        $admin = $this->pengguna('admin', 'admin-history@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'admin-history-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($admin)->postJson("/admin/transaksi/{$transaksi->id}/verify")->assertOk();

        $rows = $this->actingAs($admin)
            ->getJson('/admin/feed')
            ->assertOk()
            ->json('data.rows');

        $row = collect($rows)->firstWhere('kode', $transaksi->kode_transaksi);
        $this->assertNotNull($row);
        $this->assertSame('lunas', $row['status']);
    }
}
