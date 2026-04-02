<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (!Schema::hasColumn('skrining_pkg', 'hasil_serumen')) {
                $table->string('hasil_serumen', 255)->nullable()->after('hasil_tes_lihat')
                    ->comment('Hasil pemeriksaan serumen impaksi');
            }

            if (!Schema::hasColumn('skrining_pkg', 'hasil_infeksi_telinga')) {
                $table->string('hasil_infeksi_telinga', 255)->nullable()->after('hasil_serumen')
                    ->comment('Hasil pemeriksaan infeksi telinga');
            }

            if (!Schema::hasColumn('skrining_pkg', 'selaput_mata')) {
                $table->string('selaput_mata', 255)->nullable()->after('hasil_infeksi_telinga')
                    ->comment('Hasil pemeriksaan selaput mata (red reflex/kornea/kelopak)');
            }
        });
    }

    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (Schema::hasColumn('skrining_pkg', 'selaput_mata')) {
                $table->dropColumn('selaput_mata');
            }
            if (Schema::hasColumn('skrining_pkg', 'hasil_infeksi_telinga')) {
                $table->dropColumn('hasil_infeksi_telinga');
            }
            if (Schema::hasColumn('skrining_pkg', 'hasil_serumen')) {
                $table->dropColumn('hasil_serumen');
            }
        });
    }
};

