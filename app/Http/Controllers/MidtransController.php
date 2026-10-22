<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Reservasi;
use App\Models\Transaksi;
use App\Services\MidtransClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function __construct(private readonly MidtransClient $midtrans) {}

    /**
     * Buat Snap token untuk pesanan yang sedang dibayar (dari session).
     */
    public function pay(Request $request): JsonResponse
    {
        if (! $this->midtrans->enabled()) {
            return response()->json([
                'ok' => false,
                'message' => 'Pembayaran online belum aktif. Silakan gunakan transfer bank manual.',
            ], 503);
        }

        $transaksi = $this->currentTransaksi($request);

        if (! $transaksi) {
            return response()->json(['ok' => false, 'message' => 'Pesanan tidak ditemukan. Silakan ulangi pemesanan.'], 404);
        }

        if ($transaksi->status === 'lunas') {
            return response()->json(['ok' => false, 'message' => 'Pesanan ini sudah lunas.'], 409);
        }

        if ($transaksi->snap_token && $transaksi->status === 'pending') {
            return response()->json([
                'ok' => true,
                'token' => $transaksi->snap_token,
                'order_id' => $transaksi->kode_transaksi,
            ]);
        }

        try {
            $result = $this->midtrans->createSnap($this->snapPayload($request, $transaksi));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage() ?: 'Gagal terhubung ke layanan pembayaran.',
            ], 502);
        }

        $token = (string) ($result['token'] ?? '');

        if ($token === '') {
            return response()->json(['ok' => false, 'message' => 'Layanan pembayaran tidak mengembalikan token.'], 502);
        }

        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->snap_token = $token;
        $transaksi->metode_pembayaran = 'midtrans';
        $transaksi->save();

        return response()->json([
            'ok' => true,
            'token' => $token,
            'redirect_url' => (string) ($result['redirect_url'] ?? ''),
            'order_id' => $transaksi->kode_transaksi,
        ]);
    }

    /**
     * Buat QRIS via Core API agar kode QR tampil langsung di halaman pembayaran.
     */
    public function qris(Request $request): JsonResponse
    {
        return $this->chargeMethod($request, 'qris');
    }

    /**
     * Buat pembayaran Core API Midtrans: BCA Virtual Account, Mandiri Bill, atau QRIS.
     */
    public function charge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'metode' => ['required', 'string', 'in:qris,bca,mandiri'],
        ]);

        return $this->chargeMethod($request, $data['metode']);
    }

    private function chargeMethod(Request $request, string $method): JsonResponse
    {
        if (! $this->midtrans->enabled()) {
            return response()->json([
                'ok' => false,
                'message' => 'Pembayaran online belum aktif.',
            ], 503);
        }

        $transaksi = $this->currentTransaksi($request);

        if (! $transaksi) {
            return response()->json(['ok' => false, 'message' => 'Pesanan tidak ditemukan. Silakan ulangi pemesanan.'], 404);
        }

        if ($transaksi->status === 'lunas') {
            return response()->json(['ok' => false, 'message' => 'Pesanan ini sudah lunas.'], 409);
        }

        $detail = is_array($transaksi->payment_detail) ? $transaksi->payment_detail : [];
        $cached = $this->cachedCharge($detail, $method);

        if ($cached && $transaksi->status === 'pending' && $this->expiryValid($detail['expiry_time'] ?? null)) {
            return response()->json($cached + ['order_id' => $transaksi->kode_transaksi]);
        }

        $gross = (int) round((float) $transaksi->total_amount);
        $order = $request->session()->get('order', []);
        $user = $request->user();
        $midtransOrderId = $this->nextOrderId($transaksi, $method);

        $base = array_filter([
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $gross,
            ],
            'item_details' => $this->itemDetails($order, $transaksi, $gross),
            'customer_details' => [
                'first_name' => (string) ($user?->nama_pengguna ?? 'Pengunjung'),
                'last_name' => '',
                'email' => (string) ($user?->email ?? ''),
                'phone' => (string) ($user?->nohp ?? ''),
            ],
        ], fn ($value) => $value !== null);

        $payload = match ($method) {
            'bca' => $base + [
                'payment_type' => 'bank_transfer',
                'bank_transfer' => ['bank' => 'bca'],
            ],
            'mandiri' => $base + [
                'payment_type' => 'echannel',
                'echannel' => [
                    'bill_info1' => 'Pembayaran',
                    'bill_info2' => 'Jiwanta Resort',
                ],
            ],
            default => $base + [
                'payment_type' => 'qris',
                'qris' => ['acquirer' => 'gopay'],
            ],
        };

        try {
            $result = $this->midtrans->charge($payload);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage() ?: 'Gagal membuat pembayaran.',
            ], 502);
        }

        $result['method'] = $method;
        $response = $this->chargeResponse($result, $method);

        if ($response === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Midtrans tidak mengembalikan data pembayaran. Coba lagi beberapa saat.',
            ], 502);
        }

        $previous = is_array($transaksi->payment_detail) ? $transaksi->payment_detail : [];
        $result['order_ids'] = array_values(array_unique(array_merge(
            (array) ($previous['order_ids'] ?? []),
            [$midtransOrderId]
        )));

        $transaksi->midtrans_order_id = $midtransOrderId;
        $transaksi->payment_type = (string) ($result['payment_type'] ?? $method);
        $transaksi->metode_pembayaran = match ($method) {
            'bca' => 'BCA',
            'mandiri' => 'Mandiri',
            default => 'qris',
        };
        $transaksi->payment_detail = $result;
        $transaksi->status = 'pending';
        $transaksi->save();

        $order['metode'] = $method;
        $request->session()->put('order', $order);

        return response()->json($response + [
            'order_id' => $transaksi->kode_transaksi,
            'metode' => $method,
        ]);
    }

    /**
     * @param  array<string, mixed>  $detail
     * @return array<string, mixed>|null
     */
    private function cachedCharge(array $detail, string $method): ?array
    {
        $stored = $detail['method'] ?? $this->methodFromPaymentType((string) ($detail['payment_type'] ?? ''));

        if ($stored !== $method) {
            return null;
        }

        return $this->chargeResponse($detail, $method);
    }

    private function methodFromPaymentType(string $type): ?string
    {
        return match ($type) {
            'qris' => 'qris',
            'bank_transfer' => 'bca',
            'echannel' => 'mandiri',
            default => null,
        };
    }

    /**
     * Midtrans menolak order_id yang sudah pernah dipakai, jadi setiap percobaan
     * charge (mis. pindah dari BCA ke Mandiri) harus memakai order_id baru.
     */
    private function nextOrderId(Transaksi $transaksi, string $method): string
    {
        return $transaksi->kode_transaksi.'-'.$method.'-'.now()->format('His').random_int(10, 99);
    }

    /**
     * @param  array<string, mixed>  $detail
     * @return array<string, mixed>|null
     */
    private function chargeResponse(array $detail, string $method): ?array
    {
        $base = [
            'ok' => true,
            'metode' => $method,
            'expiry_time' => $detail['expiry_time'] ?? null,
        ];

        return match ($method) {
            'qris' => ($qr = $this->qrUrl($detail)) ? $base + ['qr_url' => $qr] : null,
            'bca' => ($va = $this->vaNumber($detail)) ? $base + ['bank' => 'bca', 'va_number' => $va] : null,
            'mandiri' => ! empty($detail['bill_key'])
                ? $base + [
                    'bill_key' => (string) $detail['bill_key'],
                    'biller_code' => (string) ($detail['biller_code'] ?? '70012'),
                ]
                : null,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $detail
     */
    private function vaNumber(array $detail): ?string
    {
        foreach ((array) ($detail['va_numbers'] ?? []) as $va) {
            if (is_array($va) && ! empty($va['va_number'])) {
                return (string) $va['va_number'];
            }
        }

        return ! empty($detail['permata_va_number']) ? (string) $detail['permata_va_number'] : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function qrUrl(array $payload): ?string
    {
        foreach ((array) ($payload['actions'] ?? []) as $action) {
            if (! is_array($action)) {
                continue;
            }

            if (($action['name'] ?? '') === 'generate-qr-code' && ! empty($action['url'])) {
                return (string) $action['url'];
            }
        }

        return null;
    }

    private function expiryValid(mixed $expiry): bool
    {
        if (! is_string($expiry) || $expiry === '') {
            return true;
        }

        try {
            return Carbon::parse($expiry)->isFuture();
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * Notifikasi server-to-server dari Midtrans (tanpa login, diverifikasi signature).
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->post() ?: $request->json()->all();
        $required = ['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'];

        foreach ($required as $field) {
            if (! isset($payload[$field]) || $payload[$field] === '') {
                return response()->json(['ok' => false, 'reason' => 'invalid_payload'], 400);
            }
        }

        $serverKey = (string) config('services.midtrans.server_key', '');

        if ($serverKey === '' || ! MidtransClient::verifySignature($payload, $serverKey)) {
            return response()->json(['ok' => false, 'reason' => 'invalid_signature'], 403);
        }

        $orderId = (string) $payload['order_id'];
        $transaksi = Transaksi::query()
            ->where('midtrans_order_id', $orderId)
            ->orWhere('kode_transaksi', $orderId)
            ->first();

        if (! $transaksi) {
            $kode = preg_replace('/-(bca|mandiri|qris)-\d+$/', '', $orderId);

            if (is_string($kode) && $kode !== '' && $kode !== $orderId) {
                $transaksi = Transaksi::query()->where('kode_transaksi', $kode)->first();
            }
        }

        if (! $transaksi) {
            return response()->json(['ok' => true, 'reason' => 'unknown_order']);
        }

        if ((int) $payload['gross_amount'] !== (int) round((float) $transaksi->total_amount)) {
            return response()->json(['ok' => false, 'reason' => 'amount_mismatch'], 403);
        }

        $status = $this->persist($transaksi, $payload);

        return response()->json(['ok' => true, 'status' => $status]);
    }

    /**
     * Status pembayaran dari DB (dipanggil halaman transaksi setelah popup Snap ditutup).
     * Dengan ?sync=1, status juga ditarik langsung dari Midtrans (berguna saat webhook
     * belum bisa menjangkau server lokal).
     */
    public function status(Request $request): JsonResponse
    {
        $transaksi = $this->currentTransaksi($request);

        if (! $transaksi) {
            return response()->json(['ok' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($request->boolean('sync') && $transaksi->status === 'pending' && $transaksi->midtrans_order_id) {
            $detail = is_array($transaksi->payment_detail) ? $transaksi->payment_detail : [];
            $orderIds = (array) ($detail['order_ids'] ?? []);
            $orderIds[] = $transaksi->midtrans_order_id;
            $orderIds = array_values(array_unique(array_filter($orderIds)));

            foreach ($orderIds as $orderId) {
                try {
                    // Transaksi Core API (QRIS/VA/Bill) tidak punya snap_token -> pakai endpoint /v2.
                    $remote = $transaksi->snap_token
                        ? $this->midtrans->transactionStatus((string) $orderId)
                        : $this->midtrans->coreTransactionStatus((string) $orderId);

                    if ($remote
                        && isset($remote['transaction_status'], $remote['gross_amount'])
                        && (int) $remote['gross_amount'] === (int) round((float) $transaksi->total_amount)) {
                        $this->persist($transaksi, $remote);
                        $transaksi->refresh();

                        if ($transaksi->status !== 'pending') {
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return response()->json([
            'ok' => true,
            'kode' => $transaksi->kode_transaksi,
            'status' => $transaksi->status,
            'payment_type' => $transaksi->payment_type,
            'paid_at' => $transaksi->paid_at?->toDateTimeString(),
            'redirect_url' => $transaksi->status === 'lunas' ? route('tiket') : null,
        ]);
    }

    /**
     * Terapkan notifikasi/status Midtrans ke transaksi. Status tidak pernah diturunkan dari lunas.
     *
     * @param  array<string, mixed>  $payload
     */
    private function persist(Transaksi $transaksi, array $payload): string
    {
        if (! isset($payload['transaction_status'])) {
            return (string) $transaksi->status;
        }

        $mapped = MidtransClient::mapStatus(
            (string) $payload['transaction_status'],
            (string) ($payload['fraud_status'] ?? '')
        );

        $status = match (true) {
            $mapped['settlement'], $mapped['capture'] => 'lunas',
            $mapped['failed'] => 'gagal',
            default => 'pending',
        };

        if ($transaksi->status === 'lunas' && $status !== 'lunas') {
            return 'lunas';
        }

        $paymentType = (string) ($payload['payment_type'] ?? '');
        $previous = is_array($transaksi->payment_detail) ? $transaksi->payment_detail : [];
        $method = $this->methodFromPaymentType($paymentType) ?? ($previous['method'] ?? null);
        $detail = $payload;
        unset($detail['signature_key']);
        if ($method !== null) {
            $detail['method'] = $method;
        }
        $orderIds = array_values(array_unique(array_merge(
            (array) ($previous['order_ids'] ?? []),
            isset($payload['order_id']) ? [(string) $payload['order_id']] : []
        )));
        if ($orderIds !== []) {
            $detail['order_ids'] = $orderIds;
        }

        $transaksi->status = $status;
        if ($paymentType !== '') {
            $transaksi->payment_type = $paymentType;
        }
        $transaksi->metode_pembayaran = match ($method) {
            'bca' => 'BCA',
            'mandiri' => 'Mandiri',
            'qris' => 'qris',
            default => $paymentType !== '' ? $paymentType : $transaksi->metode_pembayaran,
        };
        $transaksi->payment_detail = $detail;
        $transaksi->total_bayar = $status === 'lunas' ? $transaksi->total_amount : $transaksi->total_bayar;
        $transaksi->paid_at = $status === 'lunas' ? ($transaksi->paid_at ?? now()) : $transaksi->paid_at;
        $transaksi->save();

        if ($status === 'lunas') {
            $this->confirmReservasi($transaksi);
        }

        $this->broadcastStatus($transaksi);

        return $status;
    }

    private function currentTransaksi(Request $request): ?Transaksi
    {
        $order = $request->session()->get('order', []);

        if (empty($order['transaksi_id']) && empty($order['kode'])) {
            return null;
        }

        $query = Transaksi::query();

        if (! empty($order['transaksi_id'])) {
            $query->where('id', $order['transaksi_id']);
        } else {
            $query->where('kode_transaksi', $order['kode']);
        }

        return $query->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function snapPayload(Request $request, Transaksi $transaksi): array
    {
        $user = $request->user();
        $gross = (int) round((float) $transaksi->total_amount);
        $order = $request->session()->get('order', []);
        $items = $this->itemDetails($order, $transaksi, $gross);

        return array_filter([
            'transaction_details' => [
                'order_id' => $transaksi->kode_transaksi,
                'gross_amount' => $gross,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => (string) ($user?->nama_pengguna ?? 'Pengunjung'),
                'last_name' => '',
                'email' => (string) ($user?->email ?? ''),
                'phone' => (string) ($user?->nohp ?? ''),
            ],
            'callbacks' => [
                // Setelah pembayaran selesai pengunjung langsung ke Tiket Saya
                'finish' => route('tiket'),
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 2,
            ],
        ], fn ($value) => $value !== null);
    }

    /**
     * item_details wajib berjumlah sama persis dengan gross_amount.
     *
     * @param  array<string, mixed>  $order
     * @return array<int, array<string, mixed>>|null
     */
    private function itemDetails(array $order, Transaksi $transaksi, int $gross): ?array
    {
        $raw = $order['items'] ?? null;

        if (! is_array($raw) || $raw === []) {
            $raw = [[
                'nama' => $transaksi->kode_transaksi,
                'jumlah' => 1,
                'satuan' => $gross,
            ]];
        }

        $items = [];
        $sum = 0;

        foreach ($raw as $row) {
            $qty = max(1, (int) ($row['jumlah'] ?? 1));
            $price = max(0, (int) round((float) ($row['satuan'] ?? 0)));

            $items[] = [
                'id' => Str::limit(Str::slug((string) ($row['nama'] ?? 'item')), 30, ''),
                'price' => $price,
                'quantity' => $qty,
                'name' => Str::limit((string) ($row['nama'] ?? 'Item'), 50),
            ];

            $sum += $price * $qty;
        }

        if ($sum !== $gross || $items === []) {
            return [[
                'id' => $transaksi->kode_transaksi,
                'price' => $gross,
                'quantity' => 1,
                'name' => $transaksi->kode_transaksi,
            ]];
        }

        return $items;
    }

    private function confirmReservasi(Transaksi $transaksi): void
    {
        $ids = $transaksi->detailtransaksis()->pluck('reservasi_id')->filter();

        if ($ids->isEmpty()) {
            return;
        }

        Reservasi::whereIn('id', $ids)->where('status', 'pending')->update(['status' => 'confirmed']);
    }

    private function broadcastStatus(Transaksi $transaksi): void
    {
        if (! $transaksi->pengguna_id) {
            return;
        }

        try {
            OrderStatusUpdated::dispatch(
                (int) $transaksi->pengguna_id,
                (string) $transaksi->kode_transaksi,
                (string) $transaksi->status,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
