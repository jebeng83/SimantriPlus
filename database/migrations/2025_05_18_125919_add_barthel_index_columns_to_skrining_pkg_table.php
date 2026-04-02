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
            // Kolom untuk Barthel Index (Pemeriksaan Gangguan Fungsional Lansia)
            $table->integer('bab')->nullable();
            $table->integer('bak')->nullable();
            $table->integer('membersihkan_diri')->nullable();
            $table->integer('penggunaan_jamban')->nullable();
            $table->integer('makan_minum')->nullable();
            $table->integer('berubah_sikap')->nullable();
            $table->integer('berpindah')->nullable();
            $table->integer('memakai_baju')->nullable();
            $table->integer('naik_tangga')->nullable();
            $table->integer('mandi')->nullable();
            $table->integer('total_skor_barthel')->nullable();
            $table->string('tingkat_ketergantungan', 50)->nullable();
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
            // Hapus kolom Barthel Index
            $table->dropColumn([
                'bab', 'bak', 'membersihkan_diri', 'penggunaan_jamban', 
                'makan_minum', 'berubah_sikap', 'berpindah', 'memakai_baju',
                'naik_tangga', 'mandi', 'total_skor_barthel', 'tingkat_ketergantungan'
            ]);
        });
    }
};
