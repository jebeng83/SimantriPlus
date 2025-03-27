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
        // Hapus tabel data_ibu_hamil_new karena sudah tidak digunakan
        Schema::dropIfExists('data_ibu_hamil_new');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Buat ulang tabel data_ibu_hamil_new dengan struktur yang sama dengan data_ibu_hamil
        Schema::create('data_ibu_hamil_new', function (Blueprint $table) {
            $table->string('id_hamil', 7)->primary();
            $table->string('nik');
            $table->string('no_rkm_medis');
            $table->string('kehamilan_ke');
            $table->date('tgl_lahir');
            $table->string('nomor_kk');
            $table->string('nama');
            $table->double('berat_badan_sebelum_hamil')->nullable();
            $table->double('tinggi_badan')->nullable();
            $table->double('lila')->nullable();
            $table->double('imt_sebelum_hamil')->nullable();
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
            $table->boolean('kepemilikan_buku_kia');
            $table->string('jaminan_kesehatan')->nullable();
            $table->string('no_jaminan_kesehatan')->nullable();
            $table->string('faskes_tk1')->nullable();
            $table->string('faskes_rujukan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('status');
            $table->string('nama_suami')->nullable();
            $table->string('nik_suami')->nullable();
            $table->string('telp_suami')->nullable();
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('puskesmas');
            $table->string('desa');
            $table->string('data_posyandu')->nullable();
            $table->text('alamat_lengkap');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->timestamps();
        });
    }
};
