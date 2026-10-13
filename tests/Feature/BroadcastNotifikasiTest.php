<?php

namespace Tests\Feature;

use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BroadcastNotifikasiTest extends TestCase
{
    use DatabaseTransactions;

    private function pengunjung(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Notif Tester',
            'email' => 'notif-'.random_int(1000, 9999).'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200002'.random_int(100, 999),
            'role' => 'pengunjung',
        ]);
    }

    private function petugas(): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => 'Notif Petugas',
            'email' => 'petugas-'.random_int(1000, 9999).'@jiwanta.test',
            'password' => 'rahasia123',
            'nohp' => '081200003'.random_int(100, 999),
            'role' => 'petugas',
        ]);
    }

    public function test_kirim_transaksi_broadcasts_new_order_event(): void
    {
        Event::fake([NewOrderPlaced::class]);

        $pengunjung = $this->pengunjung();

        $this->actingAs($pengunjung)
            ->withSession(['order' => [
                'tipe' => 'tiket',
                'judul' => 'Tiket Renang Premier',
                'total' => 150000,
                'kode' => 'JW-20261013-001',
                'status' => 'belum',
            ]])
            ->postJson('/transaksi/kirim', ['bukti' => 'bukti.jpg'])
            ->assertOk();

        Event::assertDispatched(NewOrderPlaced::class, function (NewOrderPlaced $event) use ($pengunjung) {
            return $event->order['pemesan'] === $pengunjung->nama_pengguna
                && $event->order['kode'] === 'JW-20261013-001';
        });
    }

    public function test_event_broadcasts_on_public_admin_channel_with_payload(): void
    {
        $event = new NewOrderPlaced([
            'tipe' => 'cabin',
            'judul' => 'Cabin Deluxe',
            'total' => 2000000,
            'tamu' => '4 Tamu Dewasa',
            'kode' => 'JW-20261013-777',
            'pemesan' => 'Budi Santoso',
        ]);

        $channel = $event->broadcastOn()[0];

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertSame('admin.orders', $channel->name);
        $this->assertSame('order.placed', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertSame('JW-20261013-777', $payload['kode']);
        $this->assertSame('Budi Santoso', $payload['pemesan']);
        $this->assertSame('Rp 2.000.000', $payload['total_label']);
    }

    public function test_verify_payment_broadcasts_order_status_to_pemesan(): void
    {
        Event::fake([OrderStatusUpdated::class]);

        $pengunjung = $this->pengunjung();
        $petugas = $this->petugas();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-20261013-'.random_int(100, 999),
            'pengguna_id' => $pengunjung->id,
            'tanggal_transaksi' => now(),
            'total_amount' => 60000,
            'total_bayar' => 60000,
            'kembalian' => 0,
            'metode_pembayaran' => 'BCA',
            'status' => 'pending',
        ]);

        $this->actingAs($petugas)
            ->postJson("/petugas/transaksi/{$trx->id}/verify")
            ->assertOk();

        Event::assertDispatched(OrderStatusUpdated::class, function (OrderStatusUpdated $event) use ($pengunjung, $trx) {
            return $event->penggunaId === $pengunjung->id
                && $event->kode === $trx->kode_transaksi
                && $event->status === 'lunas';
        });
    }

    public function test_order_status_event_broadcasts_on_user_channel(): void
    {
        $event = new OrderStatusUpdated(42, 'TRX-20261013-001', 'lunas');

        $channel = $event->broadcastOn()[0];

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertSame('user.42', $channel->name);
        $this->assertSame('order.status', $event->broadcastAs());
        $this->assertSame(['kode' => 'TRX-20261013-001', 'status' => 'lunas'], $event->broadcastWith());
    }
}
