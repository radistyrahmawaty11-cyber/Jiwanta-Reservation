<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status dari ENUM ke VARCHAR(50) agar bebas menerima nilai apapun
        DB::statement('ALTER TABLE transaksis MODIFY COLUMN status VARCHAR(50) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transaksis MODIFY COLUMN status ENUM("lunas", "pending", "gagal") NOT NULL');
    }
};
