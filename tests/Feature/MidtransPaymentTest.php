<?php

namespace Tests\Feature;

use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private const SERVER_KEY = 'Mid-server-test-key';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => self::SERVER_KEY,
            'services.midtrans.client_key' => 'Mid-client-test-key',
            'services.midtrans.merchant_id' => 'M-test',
            'services.midtrans.sandbox' => true,
        ]);
    }

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

    private function transaksi(Pengguna $pengguna, string $status = 'pending', array $extra = []): Transaksi
    {
        return Transaksi::create(array_merge([
            'kode_transaksi' => 'TRX-20261021-'.random_int(100000, 999999),
            'pengguna_id' => $pengguna->id,
            'tanggal_transaksi' => now(),
            'total_amount' => 75000,
            'total_bayar' => 75000,
            'kembalian' => 0,
            'metode_pembayaran' => 'transfer',
            'status' => $status,
        ], $extra));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function signedPayload(Transaksi $transaksi, array $overrides = []): array
    {
        $payload = array_merge([
            'order_id' => $transaksi->kode_transaksi,
            'status_code' => '200',
            'gross_amount' => number_format((float) $transaksi->total_amount, 2, '.', ''),
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
            'transaction_id' => 'mid-'.random_int(100000, 999999),
        ], $overrides);

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].self::SERVER_KEY
        );

        return $payload;
    }

    private function sessionOrder(Transaksi $transaksi): array
    {
        return [
            'kode' => $transaksi->kode_transaksi,
            'transaksi_id' => $transaksi->id,
            'total' => (int) $transaksi->total_amount,
            'judul' => 'Tiket Renang',
            'badge' => 'Tiket Masuk & Kolam',
            'tipe' => 'tiket',
            'tamu' => '1 Pengunjung',
            'harga_label' => 'Harga Satuan',
            'harga_value' => 'Rp '.number_format((float) $transaksi->total_amount, 0, ',', '.'),
            'subtotal_label' => 'Subtotal',
            'tanggal' => now()->toDateString(),
            'jumlah' => 1,
            'status' => 'belum',
            'items' => [
                ['nama' => 'Tiket Renang', 'jumlah' => 1, 'satuan' => (int) $transaksi->total_amount, 'subtotal' => (int) $transaksi->total_amount, 'keterangan' => '1 tiket'],
            ],
        ];
    }

    public function test_pay_creates_snap_token_and_saves_it(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-pay@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token-123',
            ], 201),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.pay'))
            ->assertOk()
            ->assertJson(['ok' => true, 'token' => 'snap-token-123']);

        $fresh = $transaksi->fresh();
        $this->assertSame('snap-token-123', $fresh->snap_token);
        $this->assertSame($transaksi->kode_transaksi, $fresh->midtrans_order_id);
        $this->assertSame('midtrans', $fresh->metode_pembayaran);
    }

    public function test_pay_sends_expected_snap_payload(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-payload@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response(['token' => 'tok'], 201),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.pay'))
            ->assertOk();

        Http::assertSent(function ($request) use ($transaksi) {
            return $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
                && $request['transaction_details']['order_id'] === $transaksi->kode_transaksi
                && $request['transaction_details']['gross_amount'] === (int) $transaksi->total_amount
                && $request['customer_details']['email'] === $transaksi->pengguna->email;
        });
    }

    public function test_callback_with_valid_signature_marks_transaksi_lunas(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-callback@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending', [
            'midtrans_order_id' => 'TRX-MID-'.random_int(1000, 9999),
        ]);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        $payload = $this->signedPayload($transaksi);

        $this->postJson('/midtrans/callback', $payload)
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $fresh = $transaksi->fresh();
        $this->assertSame('lunas', $fresh->status);
        $this->assertSame('qris', $fresh->payment_type);
        $this->assertSame('qris', $fresh->metode_pembayaran);
        $this->assertNotNull($fresh->paid_at);
        $this->assertSame($payload['transaction_id'], $fresh->payment_detail['transaction_id']);
        $this->assertArrayNotHasKey('signature_key', $fresh->payment_detail);
    }

    public function test_callback_confirms_reservasi_on_settlement(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-cabin@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        $reservasi = Reservasi::create([
            'pengguna_id' => $pengunjung->id,
            'cabin_id' => null,
            'renang_id' => null,
            'tanggal_checkin' => now()->toDateString(),
            'tanggal_checkout' => now()->addDay()->toDateString(),
            'jumlah_tamu' => 2,
            'total_harga' => 75000,
            'status' => 'pending',
        ]);
        Detailtransaksi::create([
            'transaksi_id' => $transaksi->id,
            'reservasi_id' => $reservasi->id,
            'jumlah' => 1,
            'harga_satuan' => 75000,
            'subtotal' => 75000,
        ]);

        $this->postJson('/midtrans/callback', $this->signedPayload($transaksi))
            ->assertOk();

        $this->assertSame('confirmed', $reservasi->fresh()->status);
    }

    public function test_callback_with_invalid_signature_is_rejected(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-badsig@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        $payload = $this->signedPayload($transaksi);
        $payload['signature_key'] = 'palsu';

        $this->postJson('/midtrans/callback', $payload)
            ->assertStatus(403)
            ->assertJson(['ok' => false, 'reason' => 'invalid_signature']);

        $this->assertSame('pending', $transaksi->fresh()->status);
    }

    public function test_callback_with_amount_mismatch_is_rejected(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-amount@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        $payload = $this->signedPayload($transaksi, ['gross_amount' => '1000.00']);

        $this->postJson('/midtrans/callback', $payload)
            ->assertStatus(403)
            ->assertJson(['ok' => false, 'reason' => 'amount_mismatch']);

        $this->assertSame('pending', $transaksi->fresh()->status);
    }

    public function test_callback_deny_marks_transaksi_gagal(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-deny@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        $this->postJson('/midtrans/callback', $this->signedPayload($transaksi, [
            'transaction_status' => 'deny',
            'status_code' => '202',
        ]))->assertOk()->assertJson(['ok' => true, 'status' => 'gagal']);

        $this->assertSame('gagal', $transaksi->fresh()->status);
    }

    public function test_status_sync_pulls_settlement_from_midtrans(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-sync@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->snap_token = 'snap-token-123';
        $transaksi->save();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/*/status' => Http::response([
                'order_id' => $transaksi->kode_transaksi,
                'status_code' => '200',
                'gross_amount' => '75000.00',
                'transaction_status' => 'settlement',
                'payment_type' => 'gopay',
                'fraud_status' => 'accept',
            ], 200),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->getJson(route('transaksi.midtrans.status', ['sync' => 1]))
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $fresh = $transaksi->fresh();
        $this->assertSame('lunas', $fresh->status);
        $this->assertSame('gopay', $fresh->payment_type);
        $this->assertNotNull($fresh->paid_at);
    }

    public function test_status_sync_does_not_downgrade_paid_transaksi(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-sync-safe@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas');
        $transaksi->midtrans_order_id = $transaksi->kode_transaksi;
        $transaksi->save();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/*/status' => Http::response([
                'order_id' => $transaksi->kode_transaksi,
                'status_code' => '200',
                'gross_amount' => '75000.00',
                'transaction_status' => 'expire',
            ], 200),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->getJson(route('transaksi.midtrans.status', ['sync' => 1]))
            ->assertOk()
            ->assertJson(['status' => 'lunas']);

        $this->assertSame('lunas', $transaksi->fresh()->status);
    }

    public function test_transaksi_page_hides_manual_transfer_when_lunas(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-view@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas', ['payment_type' => 'qris']);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->get('/transaksi')
            ->assertOk()
            ->assertSee('Pembayaran QRIS Lunas')
            ->assertSee('Buka Tiket Saya')
            ->assertDontSee('Transfer Bank Manual');
    }

    public function test_transaksi_page_shows_midtrans_methods_for_pending_order(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-view-pending@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->get('/transaksi')
            ->assertOk()
            ->assertSee('data-method="bca"', false)
            ->assertSee('data-method="mandiri"', false)
            ->assertSee('data-method="qris"', false)
            ->assertSee('transaksi\\/midtrans\\/charge', false)
            ->assertDontSee('Nomor Rekening')
            ->assertDontSee('id="proofCard"', false);
    }

    public function test_charge_bca_returns_virtual_account_and_saves_pending(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-bca@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake([
            'api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-bca',
                'order_id' => $transaksi->kode_transaksi,
                'gross_amount' => '75000.00',
                'payment_type' => 'bank_transfer',
                'transaction_status' => 'pending',
                'expiry_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'va_numbers' => [
                    ['bank' => 'bca', 'va_number' => '390012345678901'],
                ],
            ], 201),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.charge'), ['metode' => 'bca'])
            ->assertOk()
            ->assertJson(['ok' => true, 'metode' => 'bca', 'va_number' => '390012345678901']);

        $fresh = $transaksi->fresh();
        $this->assertSame('bank_transfer', $fresh->payment_type);
        $this->assertSame('BCA', $fresh->metode_pembayaran);
        $this->assertSame('pending', $fresh->status);
        $this->assertSame('bca', $fresh->payment_detail['method']);
    }

    public function test_charge_mandiri_returns_bill_key_and_biller_code(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-mandiri@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake([
            'api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-mandiri',
                'order_id' => $transaksi->kode_transaksi,
                'gross_amount' => '75000.00',
                'payment_type' => 'echannel',
                'transaction_status' => 'pending',
                'expiry_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'bill_key' => '990000000260',
                'biller_code' => '70012',
            ], 201),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.charge'), ['metode' => 'mandiri'])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'metode' => 'mandiri',
                'bill_key' => '990000000260',
                'biller_code' => '70012',
            ]);

        $fresh = $transaksi->fresh();
        $this->assertSame('echannel', $fresh->payment_type);
        $this->assertSame('Mandiri', $fresh->metode_pembayaran);
    }

    public function test_charge_rejects_unknown_method(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-unknown@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.charge'), ['metode' => 'bni'])
            ->assertStatus(422);
    }

    public function test_qris_returns_qr_url_and_saves_pending(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-qris@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);
        $qrUrl = 'https://api.sandbox.midtrans.com/v2/qris/trx-qris/qr-code';

        Http::fake([
            'api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-qris',
                'order_id' => $transaksi->kode_transaksi,
                'gross_amount' => '75000.00',
                'payment_type' => 'qris',
                'transaction_status' => 'pending',
                'expiry_time' => now()->addHour()->format('Y-m-d H:i:s'),
                'actions' => [
                    ['name' => 'generate-qr-code', 'method' => 'GET', 'url' => $qrUrl],
                ],
            ], 201),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.qris'))
            ->assertOk()
            ->assertJson(['ok' => true, 'qr_url' => $qrUrl]);

        $fresh = $transaksi->fresh();
        $this->assertSame('qris', $fresh->payment_type);
        $this->assertSame('qris', $fresh->metode_pembayaran);
        $this->assertSame('pending', $fresh->status);
        $this->assertSame('trx-qris', $fresh->payment_detail['transaction_id']);
    }

    public function test_qris_charge_uses_core_api_host_and_surfaces_midtrans_error(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-qris-error@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake([
            'api.sandbox.midtrans.com/v2/charge' => Http::response([
                'status_code' => '401',
                'status_message' => 'Unauthorized. Please check your Server Key',
            ], 401),
        ]);

        $response = $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.qris'))
            ->assertStatus(502);

        $this->assertStringContainsString('Unauthorized', (string) $response->json('message'));

        Http::assertSent(fn ($request) => $request->url() === 'https://api.sandbox.midtrans.com/v2/charge');
    }

    public function test_qris_rejects_paid_order(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-qris-paid@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'lunas');

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.qris'))
            ->assertStatus(409);
    }

    public function test_status_sync_uses_core_endpoint_for_qris(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-qris-sync@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung, 'pending', [
            'midtrans_order_id' => 'TRX-QRIS-SYNC-1',
            'payment_type' => 'qris',
            'metode_pembayaran' => 'qris',
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'order_id' => 'TRX-QRIS-SYNC-1',
                'status_code' => '200',
                'gross_amount' => '75000.00',
                'transaction_status' => 'settlement',
                'payment_type' => 'qris',
                'fraud_status' => 'accept',
            ], 200),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->getJson(route('transaksi.midtrans.status', ['sync' => 1]))
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'lunas']);

        $this->assertSame('lunas', $transaksi->fresh()->status);
    }

    public function test_switching_method_uses_fresh_midtrans_order_id(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-switch@jiwanta.test');
        $transaksi = $this->transaksi($pengunjung);

        Http::fake(function ($request) {
            $orderId = $request['transaction_details']['order_id'];
            $type = $request['payment_type'];

            return Http::response(array_merge([
                'status_code' => '201',
                'transaction_id' => 'trx-'.$type,
                'order_id' => $orderId,
                'gross_amount' => '75000.00',
                'payment_type' => $type,
                'transaction_status' => 'pending',
                'expiry_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
            ], $type === 'echannel'
                ? ['bill_key' => '990000000260', 'biller_code' => '70012']
                : ['va_numbers' => [['bank' => 'bca', 'va_number' => '390012345678901']]]), 201);
        });

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.charge'), ['metode' => 'bca'])
            ->assertOk();

        $firstOrderId = $transaksi->fresh()->midtrans_order_id;

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->postJson(route('transaksi.midtrans.charge'), ['metode' => 'mandiri'])
            ->assertOk();

        $fresh = $transaksi->fresh();

        $this->assertNotSame($firstOrderId, $fresh->midtrans_order_id);
        $this->assertStringContainsString('-mandiri-', (string) $fresh->midtrans_order_id);
        $this->assertSame('Mandiri', $fresh->metode_pembayaran);
        $this->assertSame('mandiri', $fresh->payment_detail['method']);
        $this->assertCount(2, $fresh->payment_detail['order_ids']);
    }

    public function test_status_sync_preserves_selected_method(): void
    {
        $pengunjung = $this->pengguna('pengunjung', 'midtrans-sync-method@jiwanta.test');
        $orderId = 'TRX-SYNC-MANDIRI-1';
        $transaksi = $this->transaksi($pengunjung, 'pending', [
            'midtrans_order_id' => $orderId,
            'payment_type' => 'echannel',
            'metode_pembayaran' => 'Mandiri',
            'payment_detail' => ['method' => 'mandiri', 'order_ids' => [$orderId]],
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/v2/*/status' => Http::response([
                'order_id' => $orderId,
                'status_code' => '200',
                'gross_amount' => '75000.00',
                'transaction_status' => 'pending',
                'payment_type' => 'echannel',
            ], 200),
        ]);

        $this->actingAs($pengunjung)
            ->withSession(['order' => $this->sessionOrder($transaksi)])
            ->getJson(route('transaksi.midtrans.status', ['sync' => 1]))
            ->assertOk()
            ->assertJson(['ok' => true, 'status' => 'pending']);

        $fresh = $transaksi->fresh();
        $this->assertSame('Mandiri', $fresh->metode_pembayaran);
        $this->assertSame('mandiri', $fresh->payment_detail['method']);
    }
}
