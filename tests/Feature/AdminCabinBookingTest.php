<?php

namespace Tests\Feature;

use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCabinBookingTest extends TestCase
{
    use DatabaseTransactions;

    private function pengguna(string $role, string $nama): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => $nama,
            'email' => $role.'-'.uniqid().'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200008888',
            'role' => $role,
        ]);
    }

    private function cabin(): Cabin
    {
        return Cabin::create([
            'nama_cabin' => 'Cabin Booking '.uniqid(),
            'deskripsi' => 'Cabin untuk pengujian detail booking.',
            'harga_per_malam' => 500000,
            'kapasitas' => 4,
            'fasilitas' => 'AC',
            'status' => 'tersedia',
        ]);
    }

    public function test_detail_booking_menampilkan_tamu_dan_status_unit(): void
    {
        $admin = $this->pengguna('admin', 'Admin Uji');
        $tamu = $this->pengguna('pengunjung', 'Budi Tamu');
        $cabin = $this->cabin();

        $this->actingAs($tamu)->post('/reservasi/checkout', [
            'tanggal' => now()->toDateString(),
            'items' => json_encode([
                ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 2, 'harga_satuan' => 500000],
            ]),
        ])->assertRedirect(route('transaksi'));

        $res = $this->actingAs($admin)->get('/admin/cabin');

        $res->assertOk();
        $res->assertSee('Detail Booking');
        $res->assertSee('Budi Tamu');
        $res->assertSee('081200008888');
        $res->assertSee('"bookings"', false);
        $this->assertSame('terisi', $cabin->fresh()->status);
    }

    public function test_booking_ditolak_hilang_dari_timeline_dan_unit_tersedia_lagi(): void
    {
        $admin = $this->pengguna('admin', 'Admin Uji');
        $tamu = $this->pengguna('pengunjung', 'Sari Ditolak');
        $cabin = $this->cabin();

        $this->actingAs($tamu)->post('/reservasi/checkout', [
            'tanggal' => now()->toDateString(),
            'items' => json_encode([
                ['tipe' => 'cabin', 'cabin_id' => $cabin->id, 'malam' => 1, 'harga_satuan' => 500000],
            ]),
        ])->assertRedirect(route('transaksi'));

        $transaksi = Transaksi::latest('id')->firstOrFail();

        $this->actingAs($admin)
            ->post("/admin/transaksi/{$transaksi->id}/reject")
            ->assertOk();

        $this->assertSame('tersedia', $cabin->fresh()->status);
        $this->assertSame('cancelled', Reservasi::where('cabin_id', $cabin->id)->latest('id')->firstOrFail()->status);

        $res = $this->actingAs($admin)->get('/admin/cabin');
        $res->assertOk();
        $res->assertDontSee('Sari Ditolak');
    }
}
