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
        // Buat tabel data_ibu_hamil dengan primary key id_hamil
        Schema::create('data_ibu_hamil', function (Blueprint $table) {
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
        
        // Buat tabel sequence jika belum ada
        if (!Schema::hasTable('data_ibu_hamil_sequence')) {
            Schema::create('data_ibu_hamil_sequence', function (Blueprint $table) {
                $table->id();
                $table->integer('last_number')->default(1);
                $table->timestamps();
            });
            
            // Inisialisasi sequence
            DB::table('data_ibu_hamil_sequence')->insert([
                'last_number' => 1, 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_ibu_hamil');
        Schema::dropIfExists('data_ibu_hamil_sequence');
    }
};
