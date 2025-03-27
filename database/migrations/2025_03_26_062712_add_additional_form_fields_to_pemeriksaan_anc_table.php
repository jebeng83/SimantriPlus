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
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Tambahkan kolom untuk form Eklampsia
            if (!Schema::hasColumn('pemeriksaan_anc', 'pantau_tekanan_darah_eklampsia')) {
                $table->enum('pantau_tekanan_darah_eklampsia', ['Ya', 'Tidak'])->nullable()->after('hipertensi_lainnya');
                $table->enum('pantau_protein_urine_eklampsia', ['Ya', 'Tidak'])->nullable()->after('pantau_tekanan_darah_eklampsia');
                $table->enum('pantau_kondisi_janin_eklampsia', ['Ya', 'Tidak'])->nullable()->after('pantau_protein_urine_eklampsia');
                $table->enum('pemberian_antihipertensi', ['Ya', 'Tidak'])->nullable()->after('pantau_kondisi_janin_eklampsia');
                $table->enum('pemberian_mgso4', ['Ya', 'Tidak'])->nullable()->after('pemberian_antihipertensi');
                $table->enum('pemberian_diazepam', ['Ya', 'Tidak'])->nullable()->after('pemberian_mgso4');
            }
            
            // Tambahkan kolom untuk form KEK
            if (!Schema::hasColumn('pemeriksaan_anc', 'edukasi_gizi')) {
                $table->enum('edukasi_gizi', ['Ya', 'Tidak'])->nullable()->after('pemberian_diazepam');
                $table->string('kek_lainnya')->nullable()->after('edukasi_gizi');
            }
            
            // Tambahkan kolom untuk form Obesitas
            if (!Schema::hasColumn('pemeriksaan_anc', 'edukasi_gizi_obesitas')) {
                $table->enum('edukasi_gizi_obesitas', ['Ya', 'Tidak'])->nullable()->after('kek_lainnya');
                $table->string('obesitas_lainnya')->nullable()->after('edukasi_gizi_obesitas');
            }
            
            // Tambahkan kolom untuk form Infeksi
            if (!Schema::hasColumn('pemeriksaan_anc', 'pemberian_antipiretik')) {
                $table->enum('pemberian_antipiretik', ['Ya', 'Tidak'])->nullable()->after('obesitas_lainnya');
                $table->enum('pemberian_antibiotik', ['Ya', 'Tidak'])->nullable()->after('pemberian_antipiretik');
                $table->string('infeksi_lainnya')->nullable()->after('pemberian_antibiotik');
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
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Drop kolom form Eklampsia jika ada
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_tekanan_darah_eklampsia')) {
                $table->dropColumn('pantau_tekanan_darah_eklampsia');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_protein_urine_eklampsia')) {
                $table->dropColumn('pantau_protein_urine_eklampsia');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pantau_kondisi_janin_eklampsia')) {
                $table->dropColumn('pantau_kondisi_janin_eklampsia');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_antihipertensi')) {
                $table->dropColumn('pemberian_antihipertensi');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_mgso4')) {
                $table->dropColumn('pemberian_mgso4');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_diazepam')) {
                $table->dropColumn('pemberian_diazepam');
            }
            
            // Drop kolom form KEK jika ada
            if (Schema::hasColumn('pemeriksaan_anc', 'edukasi_gizi')) {
                $table->dropColumn('edukasi_gizi');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'kek_lainnya')) {
                $table->dropColumn('kek_lainnya');
            }
            
            // Drop kolom form Obesitas jika ada
            if (Schema::hasColumn('pemeriksaan_anc', 'edukasi_gizi_obesitas')) {
                $table->dropColumn('edukasi_gizi_obesitas');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'obesitas_lainnya')) {
                $table->dropColumn('obesitas_lainnya');
            }
            
            // Drop kolom form Infeksi jika ada
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_antipiretik')) {
                $table->dropColumn('pemberian_antipiretik');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'pemberian_antibiotik')) {
                $table->dropColumn('pemberian_antibiotik');
            }
            if (Schema::hasColumn('pemeriksaan_anc', 'infeksi_lainnya')) {
                $table->dropColumn('infeksi_lainnya');
            }
        });
    }
};
