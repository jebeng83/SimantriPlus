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
        Schema::table('partograf_catatan', function (Blueprint $table) {
            // Menambahkan kolom ID catatan dan informasi pasien
            $table->string('id_catatan', 20)->after('id')->nullable();
            $table->string('no_rawat', 20)->after('id_hamil')->nullable();
            $table->string('no_rkm_medis', 20)->after('no_rawat')->nullable();
            
            // Kolom untuk KALA I
            $table->string('kala1_garis_waspada', 10)->nullable()->default('Tidak');
            $table->string('kala1_masalah_lain')->nullable();
            $table->text('kala1_penatalaksanaan')->nullable();
            $table->text('kala1_hasil')->nullable();
            
            // Kolom untuk KALA II
            $table->string('kala2_episiotomi', 10)->nullable()->default('Tidak');
            $table->string('kala2_pendamping')->nullable();
            $table->string('kala2_gawat_janin', 10)->nullable()->default('Tidak');
            $table->string('kala2_distosia_bahu', 10)->nullable()->default('Tidak');
            
            // Kolom untuk KALA III
            $table->string('kala3_lama')->nullable();
            $table->string('kala3_oksitosin', 10)->nullable()->default('Tidak');
            $table->string('kala3_oks_2x', 10)->nullable()->default('Tidak');
            $table->string('kala3_penegangan_tali_pusat', 10)->nullable()->default('Tidak');
            $table->string('kala3_plasenta_lengkap', 10)->nullable()->default('Ya');
            $table->string('kala3_plasenta_lebih_30', 10)->nullable()->default('Tidak');
            
            // Kolom untuk BAYI BARU LAHIR
            $table->string('bayi_berat_badan')->nullable();
            $table->string('bayi_panjang')->nullable();
            $table->string('bayi_jenis_kelamin', 20)->nullable();
            $table->string('bayi_penilaian_bbl', 20)->nullable()->default('Baik');
            $table->string('bayi_pemberian_asi', 10)->nullable()->default('Ya');
            
            // Kolom untuk KALA IV
            $table->text('kala4_masalah')->nullable();
            $table->text('kala4_penatalaksanaan')->nullable();
            $table->text('kala4_hasil')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partograf_catatan', function (Blueprint $table) {
            // Menghapus kolom KALA I
            $table->dropColumn([
                'id_catatan',
                'no_rawat',
                'no_rkm_medis',
                'kala1_garis_waspada',
                'kala1_masalah_lain',
                'kala1_penatalaksanaan',
                'kala1_hasil',
                'kala2_episiotomi',
                'kala2_pendamping',
                'kala2_gawat_janin',
                'kala2_distosia_bahu',
                'kala3_lama',
                'kala3_oksitosin',
                'kala3_oks_2x',
                'kala3_penegangan_tali_pusat',
                'kala3_plasenta_lengkap',
                'kala3_plasenta_lebih_30',
                'bayi_berat_badan',
                'bayi_panjang',
                'bayi_jenis_kelamin',
                'bayi_penilaian_bbl',
                'bayi_pemberian_asi',
                'kala4_masalah',
                'kala4_penatalaksanaan',
                'kala4_hasil'
            ]);
        });
    }
};
