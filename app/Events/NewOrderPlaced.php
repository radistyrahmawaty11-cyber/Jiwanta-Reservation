<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $order
     */
    public function __construct(public array $order) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('admin.orders')];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $total = (int) ($this->order['total'] ?? 0);

        return [
            'transaksi_id' => (int) ($this->order['transaksi_id'] ?? 0),
            'kode' => (string) ($this->order['kode'] ?? '-'),
            'judul' => (string) ($this->order['judul'] ?? 'Pesanan Baru'),
            'tipe' => (string) ($this->order['tipe'] ?? 'tiket'),
            'tamu' => (string) ($this->order['tamu'] ?? '-'),
            'total' => $total,
            'total_label' => 'Rp '.number_format($total, 0, ',', '.'),
            'pemesan' => (string) ($this->order['pemesan'] ?? 'Pengunjung'),
            'waktu' => now()->format('H:i'),
        ];
    }
}
