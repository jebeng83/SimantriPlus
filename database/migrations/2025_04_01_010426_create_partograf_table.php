<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartografTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('partograf')) {
            Schema::create('partograf', function (Blueprint $table) {
                // Primary keys dan foreign keys
                $table->string('id_partograf', 20)->primary();
                $table->string('no_rawat', 20)->index();
                $table->string('no_rkm_medis', 20)->index();
                $table->string('id_hamil', 20)->index();
                
                // Informasi umum
                $table->dateTime('tanggal_partograf');
                $table->string('diperiksa_oleh', 100);
                
                // Bagian 1: Informasi Persalinan Awal
                $table->string('paritas', 50)->nullable();
                $table->string('onset_persalinan', 50)->nullable();
                $table->dateTime('waktu_pecah_ketuban')->nullable();
                $table->json('faktor_risiko')->nullable();
                
                // Bagian 2: Supportive Care
                $table->string('pendamping', 10)->nullable();
                $table->string('mobilitas', 10)->nullable();
                $table->string('manajemen_nyeri', 50)->nullable();
                $table->string('intake_cairan', 50)->nullable();
                
                // Bagian 3: Informasi Janin
                $table->integer('denyut_jantung_janin')->nullable();
                $table->string('kondisi_cairan_ketuban', 10)->nullable();
                $table->string('presentasi_janin', 10)->nullable();
                $table->string('bentuk_kepala_janin', 10)->nullable();
                $table->string('caput_succedaneum', 10)->nullable();
                
                // Bagian 4: Informasi Ibu
                $table->integer('nadi')->nullable();
                $table->integer('tekanan_darah_sistole')->nullable();
                $table->integer('tekanan_darah_diastole')->nullable();
                $table->decimal('suhu', 4, 1)->nullable();
                $table->integer('urine_output')->nullable();
                
                // Bagian 5: Proses Persalinan
                $table->integer('frekuensi_kontraksi')->nullable();
                $table->integer('durasi_kontraksi')->nullable();
                $table->decimal('dilatasi_serviks', 3, 1)->nullable();
                $table->string('penurunan_posisi_janin', 10)->nullable();
                
                // Bagian 6: Pengobatan
                $table->text('obat_dan_dosis')->nullable();
                $table->text('cairan_infus')->nullable();
                
                // Bagian 7: Perencanaan
                $table->text('tindakan_yang_direncanakan')->nullable();
                $table->text('hasil_tindakan')->nullable();
                $table->text('keputusan_bersama')->nullable();
                
                // Data grafik
                $table->json('grafik_kemajuan_persalinan_json')->nullable();
                
                // Timestamps
                $table->timestamps();
                
                // Tidak menggunakan foreign key constraint karena mungkin ada masalah dengan struktur tabel yang direferensikan
                // Sebagai gantinya, kita menggunakan index untuk performa query
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partograf');
    }
}
