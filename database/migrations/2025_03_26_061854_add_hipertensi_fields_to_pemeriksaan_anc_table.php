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
        if (!Schema::hasColumn('pemeriksaan_anc', 'pantau_tekanan_darah')) {
            Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                // Tambahkan kolom untuk form Hipertensi
                $table->enum('pantau_tekanan_darah', ['Ya', 'Tidak'])->nullable()->after('jumlah_mt');
                $table->enum('pantau_protein_urine', ['Ya', 'Tidak'])->nullable()->after('pantau_tekanan_darah');
                $table->enum('pantau_kondisi_janin', ['Ya', 'Tidak'])->nullable()->after('pantau_protein_urine');
                $table->string('hipertensi_lainnya')->nullable()->after('pantau_kondisi_janin');
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
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_tekanan_darah')) {
                $table->dropColumn('pantau_tekanan_darah');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_protein_urine')) {
                $table->dropColumn('pantau_protein_urine');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_kondisi_janin')) {
                $table->dropColumn('pantau_kondisi_janin');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'hipertensi_lainnya')) {
                $table->dropColumn('hipertensi_lainnya');
            }
        });
    }
};
