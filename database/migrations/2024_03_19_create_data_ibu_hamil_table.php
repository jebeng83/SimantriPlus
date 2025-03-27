<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('data_ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->nullable();
            $table->string('kehamilan_ke')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nomor_kk')->nullable();
            $table->string('nama')->nullable();
            $table->decimal('berat_badan_sebelum_hamil', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('lila', 5, 2)->nullable();
            $table->decimal('imt_sebelum_hamil', 5, 2)->nullable();
            $table->string('status_gizi')->nullable();
            $table->string('jumlah_janin')->nullable();
            $table->string('jarak_kehamilan_tahun')->nullable();
            $table->string('jarak_kehamilan_bulan')->nullable();
            $table->date('hari_pertama_haid')->nullable();
            $table->date('hari_perkiraan_lahir')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->boolean('kepemilikan_buku_kia')->default(false);
            $table->string('jaminan_kesehatan')->nullable();
            $table->string('no_jaminan_kesehatan')->nullable();
            $table->string('faskes_tk1')->nullable();
            $table->string('faskes_rujukan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_suami')->nullable();
            $table->string('nik_suami')->nullable();
            $table->string('telp_suami')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('puskesmas')->nullable();
            $table->string('desa')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_ibu_hamil');
    }
}; 