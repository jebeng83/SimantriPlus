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
        if (!Schema::hasColumn('pemeriksaan_anc', 'pemberian_mt')) {
            Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                // Tambahkan kolom untuk form Makanan Tambahan Ibu Hamil
                $table->enum('pemberian_mt', ['MT Lokal', 'MT Pabrikan'])->nullable()->after('tatalaksana_lainnya');
                $table->integer('jumlah_mt')->nullable()->default(0)->after('pemberian_mt');
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
            // Drop kolom jika ada
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_mt')) {
                $table->dropColumn('pemberian_mt');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'jumlah_mt')) {
                $table->dropColumn('jumlah_mt');
            }
        });
    }
};
