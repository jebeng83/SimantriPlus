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
            if (!Schema::hasColumn('skrining_pkg', 'pernah_dm_oleh_dokter')) {
                $table->enum('pernah_dm_oleh_dokter', ['Ya', 'Tidak'])
                    ->nullable()
                    ->after('riwayat_diabetes_ortu')
                    ->comment('Apakah anak pernah dinyatakan diabetes / kencing manis oleh dokter');
            }

            if (!Schema::hasColumn('skrining_pkg', 'lama_anak_dm')) {
                $table->string('lama_anak_dm', 4)
                    ->nullable()
                    ->after('pernah_dm_oleh_dokter')
                    ->comment('Lama anak didiagnosis DM (bulan)');
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
            if (Schema::hasColumn('skrining_pkg', 'lama_anak_dm')) {
                $table->dropColumn('lama_anak_dm');
            }
            if (Schema::hasColumn('skrining_pkg', 'pernah_dm_oleh_dokter')) {
                $table->dropColumn('pernah_dm_oleh_dokter');
            }
        });
    }
};

