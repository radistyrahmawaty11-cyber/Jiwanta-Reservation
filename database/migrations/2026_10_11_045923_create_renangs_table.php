<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kolam', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_tiket', 10, 2);
            $table->string('kedalaman', 50)->nullable();
            $table->integer('kapasitas');
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renangs');
    }
};
