<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransClient
{
    public const STATUS_SETTLEMENT = 'settlement';

    public const STATUS_CAPTURE = 'capture';

    public const STATUS_PENDING = 'pending';

    public const STATUS_DENY = 'deny';

    public const STATUS_CANCEL = 'cancel';

    public const STATUS_EXPIRE = 'expire';

    public function enabled(): bool
    {
        return $this->serverKey() !== '' && $this->clientKey() !== '';
    }

    public function baseUrl(): string
    {
        return config('services.midtrans.sandbox', true)
            ? 'https://app.sandbox.midtrans.com'
            : 'https://app.midtrans.com';
    }

    public function apiBaseUrl(): string
    {
        return config('services.midtrans.sandbox', true)
            ? 'https://api.sandbox.midtrans.com'
            : 'https://api.midtrans.com';
    }

    public function serverKey(): string
    {
        return (string) config('services.midtrans.server_key', '');
    }

    public function clientKey(): string
    {
        return (string) config('services.midtrans.client_key', '');
    }

    public function merchantId(): string
    {
        return (string) config('services.midtrans.merchant_id', '');
    }

    /**
     * Buat transaksi Snap, kembalikan token & redirect_url.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createSnap(array $payload): array
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Konfigurasi Midtrans belum lengkap (MIDTRANS_SERVER_KEY / MIDTRANS_CLIENT_KEY).');
        }

        $response = $this->request()
            ->timeout(20)
            ->asJson()
            ->post($this->baseUrl().'/snap/v1/transactions', $payload);

        return $this->handle($response, 'Gagal membuat transaksi Snap Midtrans.', 201);
    }

    /**
     * Charge Core API (mis. QRIS) agar QR bisa ditampilkan langsung di halaman.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function charge(array $payload): array
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Konfigurasi Midtrans belum lengkap (MIDTRANS_SERVER_KEY / MIDTRANS_CLIENT_KEY).');
        }

        $response = $this->request()
            ->timeout(20)
            ->asJson()
            ->post($this->apiBaseUrl().'/v2/charge', $payload);

        return $this->handle($response, 'Gagal membuat pembayaran Midtrans.', [200, 201]);
    }

    /**
     * @return array<string, mixed>|null null jika order tidak ditemukan
     */
    public function transactionStatus(string $orderId): ?array
    {
        $response = $this->request()
            ->timeout(15)
            ->asJson()
            ->get($this->baseUrl().'/snap/v1/'.rawurlencode($orderId).'/status');

        if ($response->status() === 404) {
            return null;
        }

        return $this->handle($response, 'Gagal membaca status transaksi Midtrans.', 200);
    }

    /**
     * Status Core API (/v2) untuk transaksi non-Snap seperti QRIS.
     *
     * @return array<string, mixed>|null null jika order tidak ditemukan
     */
    public function coreTransactionStatus(string $orderId): ?array
    {
        $response = $this->request()
            ->timeout(15)
            ->asJson()
            ->get($this->apiBaseUrl().'/v2/'.rawurlencode($orderId).'/status');

        if ($response->status() === 404) {
            return null;
        }

        return $this->handle($response, 'Gagal membaca status transaksi Midtrans.', 200);
    }

    /**
     * signature_key = sha512(order_id + status_code + gross_amount + server_key)
     */
    public static function verifySignature(array $payload, string $serverKey): bool
    {
        foreach (['order_id', 'status_code', 'gross_amount'] as $field) {
            if (! isset($payload[$field])) {
                return false;
            }
        }

        $expected = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey
        );

        return is_string($payload['signature_key'] ?? null)
            && hash_equals($expected, $payload['signature_key']);
    }

    /**
     * @return array{settlement: bool, capture: bool, pending: bool, failed: bool}
     */
    public static function mapStatus(string $transactionStatus, string $fraudStatus = ''): array
    {
        $failed = in_array($transactionStatus, [self::STATUS_DENY, self::STATUS_CANCEL, self::STATUS_EXPIRE], true);

        return [
            'settlement' => $transactionStatus === self::STATUS_SETTLEMENT,
            // kartu kredit: capture tapi fraud_status deny -> gagal
            'capture' => $transactionStatus === self::STATUS_CAPTURE && $fraudStatus !== 'deny',
            'pending' => $transactionStatus === self::STATUS_PENDING,
            'failed' => $failed || ($transactionStatus === self::STATUS_CAPTURE && $fraudStatus === 'deny'),
        ];
    }

    private function request(): PendingRequest
    {
        // Basic auth: server key sebagai username, password kosong
        return Http::withBasicAuth($this->serverKey(), '');
    }

    /**
     * @return array<string, mixed>
     */
    private function handle(Response $response, string $message, int|array $expected): array
    {
        $expected = (array) $expected;
        $body = $response->json() ?? [];
        $statusCode = (int) ($body['status_code'] ?? 0);

        if (! in_array($response->status(), $expected, true) || $statusCode >= 400) {
            $apiError = $body['error_messages'] ?? $body['error_message'] ?? $body['status_message'] ?? null;
            $detail = is_array($apiError) ? implode(' / ', $apiError) : $apiError;

            throw new RuntimeException(
                $message.($detail ? ' '.$detail : '').' (HTTP '.$response->status().')'
            );
        }

        return $body;
    }
}
