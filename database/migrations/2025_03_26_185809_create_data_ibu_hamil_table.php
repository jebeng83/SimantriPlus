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
        Schema::create('data_ibu_hamil', function (Blueprint $table) {
            $table->id();
            
            // Data Wajib
            $table->string('nik')->nullable();
            $table->integer('kehamilan_ke');
            $table->date('tgl_lahir');
            $table->string('nomor_kk');
            $table->string('nama');
            
            // Data Kesehatan
            $table->decimal('berat_badan_sebelum_hamil', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('lila', 5, 2)->nullable();
            $table->decimal('imt_sebelum_hamil', 5, 2)->nullable();
            $table->string('status_gizi')->nullable();
            $table->string('jumlah_janin')->nullable();
            $table->integer('jarak_kehamilan_tahun')->nullable();
            $table->integer('jarak_kehamilan_bulan')->nullable();
            $table->date('hari_pertama_haid')->nullable();
            $table->date('hari_perkiraan_lahir')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            
            // Data Administrasi
            $table->boolean('kepemilikan_buku_kia');
            $table->string('jaminan_kesehatan')->nullable();
            $table->string('no_jaminan_kesehatan')->nullable();
            $table->string('faskes_tk1')->nullable();
            $table->string('faskes_rujukan')->nullable();
            
            // Data Pribadi
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_suami')->nullable();
            $table->string('nik_suami')->nullable();
            $table->string('telp_suami')->nullable();
            
            // Data Alamat
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('puskesmas');
            $table->text('alamat_lengkap');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_ibu_hamil');
    }
};
