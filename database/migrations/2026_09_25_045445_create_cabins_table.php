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
        Schema::create('cabins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabin', 100);
            $table->enum('jenis_cabin', ['sweet', 'short']);
            $table->integer('kapasitas')->default(2);
            $table->decimal('harga_per_malam', 10, 2);
            $table->enum('status', ['tersedia', 'dipesan'])->default('tersedia');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabins');
    }
};
