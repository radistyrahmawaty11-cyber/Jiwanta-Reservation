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
        Schema::create('detailtikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tikets')->onDelete('cascade');
            $table->foreignId('pengguna_id')->constrained('penggunas')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('total_harga', 10, 2);
            $table->date('tgl_pembelian');
            $table->enum('status', ['belum_bayar', 'lunas', 'dibatalkan'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailtikets');
    }
};
