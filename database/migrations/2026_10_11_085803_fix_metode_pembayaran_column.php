<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita pakai Raw SQL langsung biar gak butuh doctrine/dbal
        DB::statement('ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran VARCHAR(50) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE transaksis MODIFY COLUMN metode_pembayaran ENUM("BCA", "Mandiri", "Tunai") NOT NULL');
    }
};
