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
        Schema::create('ilp_dewasa', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat', 17);
            $table->dateTime('tanggal');
            $table->date('tgl_lahir')->nullable();
            $table->enum('stts_nikah', ['BELUM MENIKAH', 'MENIKAH', 'JANDA', 'DUDHA', 'TIDAK MENIKAH'])->nullable();
            $table->enum('jk', ['L', 'P'])->nullable();
            $table->string('no_kk', 20)->nullable();
            $table->string('no_tlp', 40)->nullable();
            $table->string('pekerjaan', 60)->nullable();
            $table->enum('riwayat_diri_sendiri', ['Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis'])->nullable();
            $table->enum('riwayat_keluarga', ['Hipertensi', 'Diabetes militus', 'Stroke', 'Jantung', 'Asma', 'Kanker', 'Kolesterol', 'Hepatitis'])->nullable();
            $table->enum('merokok', ['Ya', 'Tidak'])->nullable();
            $table->string('konsumsi_tinggi', 25)->nullable();
            $table->string('berat_badan', 5)->nullable();
            $table->string('tinggi_badan', 5)->nullable();
            $table->string('imt', 5)->nullable();
            $table->string('lp', 4)->nullable();
            $table->string('td', 8)->nullable();
            $table->string('gula_darah', 4)->nullable();
            $table->enum('metode_mata', ['hitungjari', 'visus', 'pinhole', 'snelen card'])->nullable();
            $table->enum('hasil_mata', ['normal', 'tidak normal'])->nullable();
            $table->enum('tes_berbisik', ['normal', 'tidak normal'])->nullable();
            $table->enum('gigi', ['normal', 'caries', 'jaringan Periodental', 'goyang'])->nullable();
            $table->enum('kesehatan_jiwa', ['normal', 'gangguan emosional', 'gangguan perilaku'])->nullable();
            $table->string('tbc', 50)->nullable();
            $table->enum('fungsi_hari', ['Normal', 'Hepatitis B', 'Hepatitis C', 'Sirosis'])->nullable();
            $table->enum('status_tt', ['-', '1', '2', '3', '4', '5'])->nullable();
            $table->enum('penyakit_lain_catin', ['Normal', 'Anemia', 'HIV', 'Sifilis', 'Napza'])->nullable();
            $table->enum('kanker_payudara', ['Normal', 'ada benjolan'])->nullable();
            $table->enum('iva_test', ['Negatif', 'Positif'])->nullable();
            $table->enum('resiko_jantung', ['Ya', 'Tidak'])->nullable();
            $table->string('gds', 5)->nullable();
            $table->string('asam_urat', 5)->nullable();
            $table->string('kolesterol', 5)->nullable();
            $table->string('trigliserida', 5)->nullable();
            $table->enum('charta', ['<10%', '10% - 20%', '20% - 30%', '30% - 40%', '> 40%'])->nullable();
            $table->string('ureum', 6)->nullable();
            $table->string('kreatinin', 6)->nullable();
            $table->enum('resiko_kanker_usus', ['Ya', 'Tidak'])->nullable();
            $table->enum('skor_puma', ['< 6', '> 6'])->nullable();
            $table->string('skilas', 100)->nullable();
            $table->timestamps();
            
            // Menambahkan indeks
            $table->index('no_rawat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ilp_dewasa');
    }
};
