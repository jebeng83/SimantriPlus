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
        Schema::create('tatalaksana_anc', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat', 17);
            $table->dateTime('tanggal');
            $table->string('jenis_tatalaksana');
            
            // Fields untuk semua jenis tatalaksana
            $table->text('tatalaksana_lainnya')->nullable();
            
            // Fields untuk Anemia
            $table->boolean('diberikan_tablet_fe')->nullable();
            $table->integer('jumlah_tablet_dikonsumsi')->nullable();
            $table->integer('jumlah_tablet_ditambahkan')->nullable();
            
            // Fields untuk Makanan Tambahan
            $table->boolean('pemberian_mt')->nullable();
            $table->integer('jumlah_mt')->nullable();
            
            // Fields untuk Hipertensi dan Eklampsia
            $table->boolean('pantau_tekanan_darah')->nullable();
            $table->boolean('pantau_protein_urine')->nullable();
            $table->boolean('pantau_kondisi_janin')->nullable();
            $table->boolean('pemberian_antihipertensi')->nullable();
            $table->boolean('pemberian_mgso4')->nullable();
            $table->boolean('pemberian_diazepam')->nullable();
            
            // Fields untuk KEK dan Obesitas
            $table->boolean('edukasi_gizi')->nullable();
            
            // Fields untuk Infeksi
            $table->boolean('pemberian_antipiretik')->nullable();
            $table->boolean('pemberian_antibiotik')->nullable();
            
            // Fields untuk Penyakit Jantung
            $table->boolean('edukasi')->nullable();
            
            // Fields untuk HIV
            $table->boolean('datang_dengan_hiv')->nullable();
            $table->boolean('persalinan_pervaginam')->nullable();
            $table->boolean('persalinan_perapdoinam')->nullable();
            $table->boolean('ditawarkan_tes')->nullable();
            $table->boolean('dilakukan_tes')->nullable();
            $table->string('hasil_tes_hiv')->nullable();
            $table->boolean('mendapatkan_art')->nullable();
            $table->boolean('vct_pict')->nullable();
            $table->boolean('periksa_darah')->nullable();
            $table->boolean('serologi')->nullable();
            $table->boolean('arv_profilaksis')->nullable();
            
            // Fields untuk TB
            $table->boolean('diperiksa_dahak')->nullable();
            $table->boolean('tbc')->nullable();
            $table->boolean('obat_tb')->nullable();
            $table->string('sisa_obat')->nullable();
            
            // Fields untuk Malaria
            $table->boolean('diberikan_kelambu')->nullable();
            $table->boolean('darah_malaria_rdt')->nullable();
            $table->boolean('darah_malaria_mikroskopis')->nullable();
            $table->boolean('ibu_hamil_malaria_rdt')->nullable();
            $table->boolean('ibu_hamil_malaria_mikroskopis')->nullable();
            $table->string('hasil_test_malaria')->nullable();
            $table->boolean('obat_malaria')->nullable();
            
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
        Schema::dropIfExists('tatalaksana_anc');
    }
};
