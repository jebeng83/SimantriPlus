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
        Schema::create('skrining_siswa_sd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa_sekolah')->onDelete('cascade');
            $table->date('tanggal_skrining');
            $table->string('petugas_skrining', 100);
            
            // Antropometri
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('imt', 5, 2)->nullable();
            $table->string('status_gizi', 50)->nullable();
            
            // Pemeriksaan Fisik
            $table->string('tekanan_darah', 20)->nullable();
            $table->integer('denyut_nadi')->nullable();
            $table->decimal('suhu_tubuh', 4, 1)->nullable();
            
            // Pemeriksaan Mata
            $table->string('visus_od', 20)->nullable();
            $table->string('visus_os', 20)->nullable();
            $table->text('kelainan_mata')->nullable();
            
            // Pemeriksaan Telinga
            $table->string('pendengaran_kanan', 50)->nullable();
            $table->string('pendengaran_kiri', 50)->nullable();
            $table->text('kelainan_telinga')->nullable();
            
            // Pemeriksaan Gigi
            $table->integer('gigi_karies')->default(0);
            $table->integer('gigi_hilang')->default(0);
            $table->text('kelainan_gigi')->nullable();
            
            // Riwayat Kesehatan
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->text('obat_dikonsumsi')->nullable();
            
            // Imunisasi
            $table->json('status_imunisasi')->nullable();
            
            // Kesimpulan dan Tindak Lanjut
            $table->text('kesimpulan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->enum('status_skrining', ['Normal', 'Perlu Perhatian', 'Rujuk'])->default('Normal');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skrining_siswa_sd');
    }
};