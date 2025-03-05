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
        Schema::table('ilp_dewasa', function (Blueprint $table) {
            // Tambahkan kolom no_rkm_medis dan nip
            $table->string('no_rkm_medis', 15)->nullable()->after('no_rawat');
            $table->string('nip', 20)->nullable()->after('no_rkm_medis');
        });

        // Ubah tipe data kolom riwayat_diri_sendiri dan riwayat_keluarga untuk menambahkan nilai enum 'Normal'
        DB::statement("ALTER TABLE ilp_dewasa MODIFY COLUMN riwayat_diri_sendiri ENUM('Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis', 'Normal') NULL");
        
        DB::statement("ALTER TABLE ilp_dewasa MODIFY COLUMN riwayat_keluarga ENUM('Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis', 'Normal') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ilp_dewasa', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['no_rkm_medis', 'nip']);
        });

        // Kembalikan tipe data kolom riwayat_diri_sendiri dan riwayat_keluarga ke nilai semula
        DB::statement("ALTER TABLE ilp_dewasa MODIFY COLUMN riwayat_diri_sendiri ENUM('Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis') NULL");
        
        DB::statement("ALTER TABLE ilp_dewasa MODIFY COLUMN riwayat_keluarga ENUM('Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis') NULL");
    }
};
