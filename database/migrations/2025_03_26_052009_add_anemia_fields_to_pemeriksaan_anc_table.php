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
        // Periksa apakah kolom sudah ada
        if (!Schema::hasColumn('pemeriksaan_anc', 'tatalaksana_lainnya')) {
            Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                // Tambahkan hanya kolom yang belum ada
                $table->string('tatalaksana_lainnya')->nullable()->after('jumlah_tablet_ditambahkan');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Drop hanya kolom yang kita tambahkan
            if (Schema::hasColumn('pemeriksaan_anc', 'tatalaksana_lainnya')) {
                $table->dropColumn('tatalaksana_lainnya');
            }
        });
    }
};
