<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabin', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_per_malam', 10, 2);
            $table->integer('kapasitas');
            $table->text('fasilitas')->nullable();
            $table->string('gambar')->nullable();
            $table->enum('status', ['tersedia', 'terisi'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabins');
    }
};
