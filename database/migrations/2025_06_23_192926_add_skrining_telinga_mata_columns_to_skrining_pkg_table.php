<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            // Add columns for Skrining Telinga dan Mata - Balita dan Anak Prasekolah
            if (!Schema::hasColumn('skrining_pkg', 'hasil_tes_dengar')) {
                $table->string('hasil_tes_dengar', 100)->nullable()->after('penglihatan');
            }
            if (!Schema::hasColumn('skrining_pkg', 'hasil_tes_lihat')) {
                $table->string('hasil_tes_lihat', 100)->nullable()->after('hasil_tes_dengar');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            // Drop columns for Skrining Telinga dan Mata - Balita dan Anak Prasekolah
            if (Schema::hasColumn('skrining_pkg', 'hasil_tes_dengar')) {
                $table->dropColumn('hasil_tes_dengar');
            }
            if (Schema::hasColumn('skrining_pkg', 'hasil_tes_lihat')) {
                $table->dropColumn('hasil_tes_lihat');
            }
        });
    }
};
