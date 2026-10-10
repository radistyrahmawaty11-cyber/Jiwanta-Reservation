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
<<<<<<< HEAD
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengguna', 100);
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->string('nohp', 15);
            $table->enum('role', ['admin', 'pengunjung', 'petugas']);
            $table->timestamps();
        });
=======
    Schema::create('penggunas', function (Blueprint $table) {
        $table->id();
        $table->string('nama', 100);       // <-- Pastikan ini 'nama', bukan 'name'
        $table->string('email', 50)->unique();
        $table->string('password');
        $table->string('nohp', 15);
        $table->enum('role', ['admin', 'pengunjung', 'petugas']);
        $table->timestamps();
    });
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
