<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('pengguna_id')->constrained('penggunas')->onDelete('cascade');
            $table->datetime('tgl_transaksi');
            $table->decimal('total_bayar', 12, 2);
            $table->decimal('jumlah_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->enum('metode', ['cash', 'qr', 'tf']);
            $table->enum('jenis_transaksi', ['pembayaran_renang', 'pembayaran_cabin']);
            $table->enum('status', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
