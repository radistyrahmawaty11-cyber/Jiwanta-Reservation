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
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_reservasi', 100);
            $table->datetime('tgl_reservasi' );
            $table->enum('jenis_reservasi', ['cabin', 'renang']);
            $table->foreignId('id_pengguna')->constrained('penggunas')->onDelete('cascade');
            $table->integer('jumlah_orang')->default(1);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};
