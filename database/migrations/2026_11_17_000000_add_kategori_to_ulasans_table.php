<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ulasans', 'kategori')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->string('kategori', 20)->default('tiket')->after('pengguna_id');
            });
        }

        $ulasans = DB::table('ulasans')->select('id', 'pengguna_id')->get();

        foreach ($ulasans as $ulasan) {
            $hasTiket = DB::table('detailtransaksis')
                ->join('transaksis', 'transaksis.id', '=', 'detailtransaksis.transaksi_id')
                ->where('transaksis.pengguna_id', $ulasan->pengguna_id)
                ->whereNotNull('detailtransaksis.tiket_id')
                ->exists();

            $hasCabin = DB::table('detailtransaksis')
                ->join('transaksis', 'transaksis.id', '=', 'detailtransaksis.transaksi_id')
                ->join('reservasis', 'reservasis.id', '=', 'detailtransaksis.reservasi_id')
                ->where('transaksis.pengguna_id', $ulasan->pengguna_id)
                ->whereNotNull('reservasis.cabin_id')
                ->exists();

            if (! $hasTiket && $hasCabin) {
                DB::table('ulasans')->where('id', $ulasan->id)->update(['kategori' => 'cabin']);
            }
        }

        // Indeks unik lama juga dipakai sebagai indeks foreign key pengguna_id,
        // jadi buat indeks pengganti sebelum indeks unik tersebut dihapus.
        if (! Schema::hasIndex('ulasans', 'ulasans_pengguna_id_index')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->index('pengguna_id', 'ulasans_pengguna_id_index');
            });
        }

        if (Schema::hasIndex('ulasans', 'ulasans_pengguna_id_unique')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->dropUnique('ulasans_pengguna_id_unique');
            });
        }

        if (! Schema::hasIndex('ulasans', 'ulasans_pengguna_id_kategori_unique')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->unique(['pengguna_id', 'kategori']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('ulasans', 'ulasans_pengguna_id_kategori_unique')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->dropUnique('ulasans_pengguna_id_kategori_unique');
            });
        }

        $keepIds = DB::table('ulasans')
            ->selectRaw('MAX(id) as id')
            ->groupBy('pengguna_id')
            ->pluck('id');

        DB::table('ulasans')->whereNotIn('id', $keepIds)->delete();

        if (Schema::hasIndex('ulasans', 'ulasans_pengguna_id_index')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->dropIndex('ulasans_pengguna_id_index');
            });
        }

        if (! Schema::hasIndex('ulasans', 'ulasans_pengguna_id_unique')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->unique('pengguna_id');
            });
        }

        if (Schema::hasColumn('ulasans', 'kategori')) {
            Schema::table('ulasans', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }
};
