<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
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

    private function transaksi(Pengguna $pengguna, string $status = 'pending', ?Carbon $tanggal = null): Transaksi
    {
        return Transaksi::create([
            'kode_transaksi' => 'TRX-20261013-'.random_int(100, 999),
            'pengguna_id' => $pengguna->id,
            'tanggal_transaksi' => $tanggal ?? now(),
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

    public function test_scan_menerima_kode_barcode_tanpa_prefiks_trx(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-scan-barcode@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-scan-barcode-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas');

        // Isi barcode pada tiket = kode booking tanpa prefiks "TRX-".
        $payload = preg_replace('/^TRX-/', '', $transaksi->kode_transaksi);

        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => $payload])
            ->assertOk()
            ->assertJson(['ok' => true])
            ->assertJsonPath('transaksi.code', $transaksi->kode_transaksi);
    }

    public function test_pengunjung_cannot_verify_payment(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'gate-forbidden@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($pengunjung)
            ->postJson("/petugas/transaksi/{$transaksi->id}/verify")
            ->assertForbidden();
    }

    public function test_check_in_berhasil_lalu_tiket_tidak_bisa_dipakai_lagi(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-checkin@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-checkin-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas');

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/check-in")
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $this->assertNotNull($transaksi->fresh()->checked_in_at);

        // Scan ulang: tiket sudah dipakai.
        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => $transaksi->kode_transaksi])
            ->assertOk()
            ->assertJson(['ok' => false, 'reason' => 'used']);

        // Check-in kedua: ditolak.
        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/check-in")
            ->assertStatus(422)
            ->assertJson(['ok' => false, 'reason' => 'used']);
    }

    public function test_scan_menolak_tiket_kedaluwarsa(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-expired@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-expired-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas', now()->subDay());

        $this->actingAs($petugas)
            ->postJson('/petugas/scan', ['code' => $transaksi->kode_transaksi])
            ->assertOk()
            ->assertJson(['ok' => false, 'reason' => 'expired'])
            ->assertJsonPath('transaksi.visit_date', now()->subDay()->toDateString());
    }

    public function test_check_in_menolak_tiket_kedaluwarsa(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-expired-checkin@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-expired-checkin-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas', now()->subDays(3));

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/check-in")
            ->assertStatus(422)
            ->assertJson(['ok' => false, 'reason' => 'expired']);

        $this->assertNull($transaksi->fresh()->checked_in_at);
    }

    public function test_check_in_menolak_pembayaran_yang_belum_lunas(): void
    {
        $petugas = $this->pengguna('petugas', 'gate-pending-checkin@jiwanta.test');
        $pengunjung = $this->pengguna('pengunjung', 'gate-pending-checkin-visitor@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending');

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$transaksi->id}/check-in")
            ->assertStatus(422)
            ->assertJson(['ok' => false, 'reason' => 'pending']);
    }
}
