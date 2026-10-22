<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('paid_at');
        });

        DB::table('transaksis')
            ->where('status', 'lunas')
            ->whereNull('verified_at')
            ->update(['verified_at' => DB::raw('COALESCE(paid_at, updated_at)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};
