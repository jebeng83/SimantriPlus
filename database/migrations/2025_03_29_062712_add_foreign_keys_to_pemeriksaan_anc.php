<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Periksa dulu apakah tabel pemeriksaan_anc ada
        if (Schema::hasTable('pemeriksaan_anc')) {
            try {
                // Tambahkan indeks pada kolom no_rawat, no_rkm_medis, dan id_hamil
                Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                    // Indeks untuk no_rawat
                    $table->index('no_rawat', 'pemeriksaan_anc_no_rawat_index');
                    
                    // Indeks untuk no_rkm_medis
                    $table->index('no_rkm_medis', 'pemeriksaan_anc_no_rkm_medis_index');
                    
                    // Indeks untuk id_hamil
                    $table->index('id_hamil', 'pemeriksaan_anc_id_hamil_index');
                });
            } catch (\Exception $e) {
                // Jika error duplikasi indeks, abaikan
                \Log::info('Indeks mungkin sudah ada: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('pemeriksaan_anc')) {
            try {
                Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                    // Hapus indeks
                    $table->dropIndex('pemeriksaan_anc_no_rawat_index');
                    $table->dropIndex('pemeriksaan_anc_no_rkm_medis_index');
                    $table->dropIndex('pemeriksaan_anc_id_hamil_index');
                });
            } catch (\Exception $e) {
                // Jika error indeks tidak ditemukan, abaikan
                \Log::info('Indeks mungkin tidak ada: ' . $e->getMessage());
            }
        }
    }
};
