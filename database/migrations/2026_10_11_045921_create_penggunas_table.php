<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengguna', 100);
            $table->string('email', 50)->unique();
            $table->string('password', 255);
            $table->string('nohp', 15);
            $table->enum('role', ['admin', 'pengunjung', 'petugas'])->default('pengunjung');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
