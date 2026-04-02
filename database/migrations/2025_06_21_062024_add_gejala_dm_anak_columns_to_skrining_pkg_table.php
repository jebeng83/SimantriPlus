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
            // Gejala DM Anak - Kolom untuk menyimpan data form gejala diabetes mellitus pada anak
            $table->enum('sering_lapar', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak sering lapar atau banyak makan dalam 1 bulan terakhir');
            $table->enum('sering_haus', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak sering haus atau banyak minum dalam 1 bulan terakhir');
            $table->enum('sering_pipis', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak sering pipis dalam 1 bulan terakhir');
            $table->enum('sering_mengompol', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak sering mengompol dalam 1 bulan terakhir');
            $table->enum('berat_turun', ['Ya', 'Tidak'])->nullable()->comment('Apakah berat badan anak turun secara drastis');
            $table->enum('riwayat_diabetes_ortu', ['Ya', 'Tidak'])->nullable()->comment('Apakah orang tua memiliki riwayat penyakit diabetes');
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
            // Hapus kolom gejala DM anak
            $table->dropColumn([
                'sering_lapar',
                'sering_haus', 
                'sering_pipis',
                'sering_mengompol',
                'berat_turun',
                'riwayat_diabetes_ortu'
            ]);
        });
    }
};
