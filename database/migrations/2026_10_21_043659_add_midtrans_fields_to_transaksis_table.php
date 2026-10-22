<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // enum('cash','transfer','qris') -> varchar agar menerima metode Midtrans (gopay, qris, bank_transfer, ccard, ...)
        DB::statement("ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran VARCHAR(50) NOT NULL DEFAULT 'transfer'");

        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('midtrans_order_id', 64)->nullable()->unique()->after('kode_transaksi');
            $table->text('snap_token')->nullable()->after('midtrans_order_id');
            $table->string('payment_type', 50)->nullable()->after('metode_pembayaran');
            $table->json('payment_detail')->nullable()->after('payment_type');
            $table->timestamp('paid_at')->nullable()->after('tanggal_transaksi');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropUnique(['midtrans_order_id']);
            $table->dropColumn([
                'midtrans_order_id',
                'snap_token',
                'payment_type',
                'payment_detail',
                'paid_at',
            ]);
        });

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran ENUM('cash','transfer','qris') NOT NULL DEFAULT 'cash'");
    }
};
