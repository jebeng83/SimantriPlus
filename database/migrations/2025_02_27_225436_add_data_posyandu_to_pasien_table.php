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
        Schema::table('pasien', function (Blueprint $table) {
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('pasien', 'data_posyandu')) {
                $table->string('data_posyandu', 100)->nullable()->after('no_ktp');
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
        Schema::table('pasien', function (Blueprint $table) {
            // Cek apakah kolom ada sebelum dihapus
            if (Schema::hasColumn('pasien', 'data_posyandu')) {
                $table->dropColumn('data_posyandu');
            }
        });
    }
};
