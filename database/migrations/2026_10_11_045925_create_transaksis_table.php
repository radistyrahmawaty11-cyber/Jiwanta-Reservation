<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('penggunas')->onDelete('cascade');
            $table->string('kode_transaksi', 50)->unique();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_bayar', 10, 2)->nullable();
            $table->decimal('kembalian', 10, 2)->nullable();
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris'])->default('cash');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
