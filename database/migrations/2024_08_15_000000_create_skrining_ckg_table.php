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
        Schema::create('skrining_pkg', function (Blueprint $table) {
            $table->integer('id_pkg', true);
            
            // Data Identitas Diri
            $table->string('nik', 25);
            $table->string('nama_lengkap', 100);
            $table->date('tanggal_lahir');
            $table->integer('umur')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_handphone', 25)->nullable();
            
            // Demografi
            $table->enum('status_perkawinan', ['Belum Menikah', 'Menikah', 'Cerai Mati', 'Cerai Hidup'])->nullable();
            $table->enum('rencana_menikah', ['Ya', 'Tidak'])->nullable();
            $table->enum('status_hamil', ['Ya', 'Tidak'])->nullable();
            $table->enum('status_disabilitas', ['Non disabilitas', 'Penyandang disabilitas'])->nullable();
            
            // Kesehatan Jiwa
            $table->string('minat', 50)->nullable();
            $table->string('sedih', 50)->nullable();
            $table->string('cemas', 50)->nullable();
            $table->string('khawatir', 50)->nullable();
            
            // Aktivitas Fisik
            $table->string('frekuensi_olahraga', 50)->nullable();
            $table->string('durasi_olahraga', 50)->nullable();
            
            // Perilaku Merokok
            $table->enum('status_merokok', ['Ya', 'Tidak'])->nullable();
            $table->integer('lama_merokok')->nullable();
            $table->integer('jumlah_rokok')->nullable();
            $table->enum('paparan_asap', ['Ya', 'Tidak'])->nullable();
            
            // Tekanan Darah & Gula Darah
            $table->enum('riwayat_hipertensi', ['Ya', 'Tidak'])->nullable();
            $table->enum('riwayat_diabetes', ['Ya', 'Tidak'])->nullable();
            
            // Hati
            $table->enum('riwayat_hepatitis', ['Ya', 'Tidak'])->nullable();
            $table->enum('riwayat_kuning', ['Ya', 'Tidak'])->nullable();
            $table->enum('riwayat_transfusi', ['Ya', 'Tidak'])->nullable();
            $table->enum('riwayat_tattoo', ['Ya', 'Tidak'])->nullable();
            $table->enum('riwayat_tindik', ['Ya', 'Tidak'])->nullable();
            $table->enum('narkoba_suntik', ['Ya', 'Tidak'])->nullable();
            $table->enum('odhiv', ['Ya', 'Tidak'])->nullable();
            $table->enum('kolesterol', ['Ya', 'Tidak'])->nullable();
            
            // Kanker Leher Rahim
            $table->enum('hubungan_intim', ['Ya', 'Tidak'])->nullable();
            
            // Tuberkulosis
            $table->enum('riwayat_merokok', ['Ya', 'Tidak'])->nullable();
            $table->enum('napas_pendek', ['Ya', 'Tidak'])->nullable();
            $table->enum('dahak', ['Ya', 'Tidak'])->nullable();
            $table->enum('batuk', ['Ya', 'Tidak'])->nullable();
            $table->enum('spirometri', ['Ya', 'Tidak'])->nullable();
            
            // Antropometri dan Laboratorium
            $table->decimal('tinggi_badan', 5, 1)->nullable();
            $table->decimal('berat_badan', 5, 1)->nullable();
            $table->decimal('lingkar_perut', 5, 1)->nullable();
            $table->integer('tekanan_sistolik')->nullable();
            $table->integer('tekanan_diastolik')->nullable();
            $table->decimal('gds', 6, 1)->nullable();
            $table->decimal('gdp', 6, 1)->nullable();
            $table->decimal('kolesterol_lab', 6, 1)->nullable();
            $table->decimal('trigliserida', 6, 1)->nullable();
            
            // Skrining Indra
            $table->enum('pendengaran', ['Normal', 'Gangguan pendengaran'])->nullable();
            $table->enum('penglihatan', ['Normal', 'Menggunakan Kacamata'])->nullable();
            
            // Skrining Gigi
            $table->enum('karies', ['Ya', 'Tidak'])->nullable();
            $table->enum('hilang', ['Ya', 'Tidak'])->nullable();
            $table->enum('goyang', ['Ya', 'Tidak'])->nullable();
            $table->enum('status', ['0', '1'])->default('0');
            
            // Tanggal skrining
            $table->date('tanggal_skrining')->nullable();
            $table->string('no_rkm_medis', 50)->nullable();
            
            // Metadata
            $table->timestamps();
        });
        
        // Membuat sequence untuk auto_increment id_pkg
        DB::statement('ALTER TABLE skrining_pkg AUTO_INCREMENT = 1001');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skrining_pkg');
    }
}; 