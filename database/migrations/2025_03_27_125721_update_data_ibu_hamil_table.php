<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop table jika sudah ada
        Schema::dropIfExists('data_ibu_hamil');

        // Buat ulang tabel dengan struktur baru
        Schema::create('data_ibu_hamil', function (Blueprint $table) {
            $table->string('id_hamil', 7)->primary();
            $table->string('nik', 20)->nullable();
            $table->string('no_rkm_medis', 15);
            $table->string('kehamilan_ke', 3)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nomor_kk', 20)->nullable();
            $table->string('nama', 40)->nullable();
            $table->decimal('berat_badan_sebelum_hamil', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('lila', 5, 2)->nullable();
            $table->decimal('imt_sebelum_hamil', 5, 2)->nullable();
            $table->string('status_gizi', 40)->nullable();
            $table->string('jumlah_janin', 50)->nullable();
            $table->char('jarak_kehamilan_tahun', 3)->nullable();
            $table->char('jarak_kehamilan_bulan', 3)->nullable();
            $table->date('hari_pertama_haid')->nullable();
            $table->date('hari_perkiraan_lahir')->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->boolean('kepemilikan_buku_kia')->default(0);
            $table->string('jaminan_kesehatan', 30)->nullable();
            $table->string('no_jaminan_kesehatan', 20)->nullable();
            $table->string('faskes_tk1', 50)->nullable();
            $table->string('faskes_rujukan', 40)->nullable();
            $table->string('pendidikan', 10)->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('status', 30)->default('Hamil');
            $table->string('nama_suami', 40)->nullable();
            $table->string('nik_suami', 20)->nullable();
            $table->string('telp_suami', 40)->nullable();
            $table->integer('provinsi')->nullable();
            $table->integer('kabupaten')->nullable();
            $table->integer('kecamatan')->nullable();
            $table->string('puskesmas', 255)->nullable();
            $table->string('desa', 60)->nullable();
            $table->string('data_posyandu', 50)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->char('rt', 3)->nullable();
            $table->char('rw', 4)->nullable();
            $table->timestamps();
        });

        // Set tabel engine ke InnoDB dengan charset latin1 dan collate latin1_swedish_ci
        DB::statement('ALTER TABLE data_ibu_hamil ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC');
        
        // Tambahkan foreign key ke tabel pasien
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            // Cek jika tabel pasien ada
            if (Schema::hasTable('pasien')) {
                $table->foreign('no_rkm_medis')
                      ->references('no_rkm_medis')
                      ->on('pasien')
                      ->onUpdate('cascade')
                      ->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_ibu_hamil');
    }
};
