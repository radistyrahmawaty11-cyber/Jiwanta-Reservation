<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetugasDashboardTest extends TestCase
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

    public function test_dashboard_lists_pending_transactions(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-list@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-list-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($petugas)
            ->get('/petugas/dashboard')
            ->assertOk()
            ->assertSee($transaksi->kode_transaksi);
    }

    public function test_petugas_can_verify_payment(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-verify@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-verify-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/verify")
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $this->assertSame('lunas', $transaksi->fresh()->status);
    }

    public function test_petugas_can_reject_payment(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-reject@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-reject-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/reject")
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'gagal']);

        $this->assertSame('gagal', $transaksi->fresh()->status);
    }

    public function test_scan_returns_ticket_for_lunas_code(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-scan@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-scan-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas');

        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => $transaksi->kode_transaksi])
            ->assertOk()
            ->assertJson(['ok' => true])
            ->assertJsonPath('transaksi.code', $transaksi->kode_transaksi);
    }

    public function test_scan_flags_pending_payment(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-scan-pending@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-scan-pending-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => $transaksi->kode_transaksi])
            ->assertOk()
            ->assertJson(['ok' => false, 'reason' => 'pending']);
    }

    public function test_scan_rejects_unknown_code(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-scan-unknown@jiwanta.test');

        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => 'TRX-99999999-000'])
            ->assertOk()
            ->assertJson(['ok' => false, 'reason' => 'not_found']);
    }

    public function test_pengunjung_cannot_verify_payment(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'gate-forbidden@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($pengunjung)
            ->postJson("/petugas/transaksi/{$transaksi->id}/verify")
            ->assertForbidden();
    }
}
