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
            // Tuberkulosis Bayi & Anak - Kolom untuk menyimpan data form TBC
            $table->enum('batuk_lama', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Batuk yang tidak sembuh lebih dari 2 minggu');
            $table->enum('berat_turun_tbc', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Berat badan turun tanpa alasan jelas');
            $table->enum('berat_tidak_naik', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Berat badan tidak naik dalam 2 bulan terakhir');
            $table->enum('nafsu_makan_berkurang', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Nafsu makan berkurang atau tidak ada');
            $table->enum('kontak_tbc', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Kontak dengan penderita TBC atau batuk berkepanjangan');
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
            // Hapus kolom tuberkulosis bayi anak
            $table->dropColumn([
                'batuk_lama',
                'berat_turun_tbc',
                'berat_tidak_naik',
                'nafsu_makan_berkurang',
                'kontak_tbc'
            ]);
        });
    }
};
