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
        Schema::create('pemeriksaan_anc_new', function (Blueprint $table) {
            // ID dan Informasi Dasar
            $table->id();
            $table->string('id_anc', 7)->unique();
            $table->string('no_rawat', 20);
            $table->string('no_rkm_medis', 15);
            $table->string('id_hamil', 15)->nullable();
            $table->dateTime('tanggal_anc');
            $table->string('diperiksa_oleh', 255);
            
            // Informasi Kunjungan
            $table->integer('usia_kehamilan');
            $table->integer('trimester');
            $table->integer('kunjungan_ke')->nullable();
            $table->string('keadaan_pulang');
            
            // 1. Anamnesis
            $table->text('keluhan_utama')->nullable();
            $table->integer('gravida')->nullable();
            $table->integer('partus')->nullable();
            $table->integer('abortus')->nullable();
            $table->integer('hidup')->nullable();
            $table->text('riwayat_penyakit')->nullable(); // JSON
            
            // 2. Pemeriksaan Fisik - BB & TB (T1)
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->decimal('imt', 5, 2);
            $table->string('kategori_imt', 50);
            $table->string('jumlah_janin', 50)->nullable();
            
            // 3. Status Gizi (T3)
            $table->decimal('lila', 5, 2)->nullable(); // Lingkar Lengan Atas
            $table->string('status_gizi', 50)->nullable();
            
            // 4. Tekanan Darah (T2)
            $table->integer('td_sistole');
            $table->integer('td_diastole');
            
            // 5. Tinggi Fundus Uteri (T4)
            $table->decimal('tinggi_fundus', 5, 2)->nullable();
            $table->integer('taksiran_berat_janin')->nullable();
            
            // 6. DJJ dan Presentasi (T5)
            $table->integer('denyut_jantung_janin')->nullable();
            $table->string('presentasi', 50)->nullable();
            $table->string('presentasi_janin', 50)->nullable();
            
            // 7. Status Imunisasi TT (T6)
            $table->string('status_tt', 50)->nullable();
            $table->string('imunisasi_tt', 50)->nullable();
            $table->date('tanggal_imunisasi')->nullable();
            
            // 8. Tablet Fe (T7)
            $table->integer('jumlah_fe');
            $table->integer('dosis');
            
            // 9. Pemeriksaan Lab (T8)
            $table->date('tanggal_lab')->nullable();
            $table->text('lab')->nullable(); // JSON
            $table->decimal('hasil_pemeriksaan_hb', 5, 2)->nullable();
            $table->string('hasil_pemeriksaan_urine_protein', 50)->nullable();
            $table->string('hasil_pemeriksaan_urine_reduksi', 50)->nullable();
            $table->string('pemeriksaan_lab', 255)->nullable();
            $table->text('rujukan_ims')->nullable();
            $table->enum('perawatan_payudara', ['Ya', 'Tidak'])->nullable();
            
            // 10. Tatalaksana Kasus (T9)
            $table->string('jenis_tatalaksana', 255)->nullable();
            
            // Tatalaksana - Anemia
            $table->enum('diberikan_tablet_fe', ['Ya', 'Tidak'])->nullable();
            $table->integer('jumlah_tablet_dikonsumsi')->default(0);
            $table->integer('jumlah_tablet_ditambahkan')->default(0);
            $table->string('tatalaksana_lainnya')->nullable();
            
            // Tatalaksana - Makanan Tambahan
            $table->enum('pemberian_mt', ['MT Lokal', 'MT Pabrikan'])->nullable();
            $table->integer('jumlah_mt')->default(0);
            
            // Tatalaksana - Hipertensi
            $table->enum('pantau_tekanan_darah', ['Ya', 'Tidak'])->nullable();
            $table->enum('pantau_protein_urine', ['Ya', 'Tidak'])->nullable();
            $table->enum('pantau_kondisi_janin', ['Ya', 'Tidak'])->nullable();
            $table->string('hipertensi_lainnya')->nullable();
            
            // Tatalaksana - Eklampsia
            $table->enum('pantau_tekanan_darah_eklampsia', ['Ya', 'Tidak'])->nullable();
            $table->enum('pantau_protein_urine_eklampsia', ['Ya', 'Tidak'])->nullable();
            $table->enum('pantau_kondisi_janin_eklampsia', ['Ya', 'Tidak'])->nullable();
            $table->enum('pemberian_antihipertensi', ['Ya', 'Tidak'])->nullable();
            $table->enum('pemberian_mgso4', ['Ya', 'Tidak'])->nullable();
            $table->enum('pemberian_diazepam', ['Ya', 'Tidak'])->nullable();
            
            // Tatalaksana - KEK
            $table->enum('edukasi_gizi', ['Ya', 'Tidak'])->nullable();
            $table->string('kek_lainnya')->nullable();
            
            // Tatalaksana - Obesitas
            $table->enum('edukasi_gizi_obesitas', ['Ya', 'Tidak'])->nullable();
            $table->string('obesitas_lainnya')->nullable();
            
            // Tatalaksana - Infeksi
            $table->enum('pemberian_antipiretik', ['Ya', 'Tidak'])->nullable();
            $table->enum('pemberian_antibiotik', ['Ya', 'Tidak'])->nullable();
            $table->string('infeksi_lainnya')->nullable();
            
            // Tatalaksana - Penyakit Jantung
            $table->enum('edukasi', ['Ya', 'Tidak'])->nullable();
            $table->string('jantung_lainnya')->nullable();
            
            // Tatalaksana - HIV
            $table->enum('datang_dengan_hiv', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->enum('persalinan_pervaginam', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->enum('persalinan_perapdoinam', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->enum('ditawarkan_tes', ['Ya', 'Tidak'])->nullable();
            $table->enum('dilakukan_tes', ['Ya', 'Tidak'])->nullable();
            $table->enum('hasil_tes_hiv', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->enum('mendapatkan_art', ['Ya', 'Tidak'])->nullable();
            $table->enum('vct_pict', ['Ya', 'Tidak'])->nullable();
            $table->enum('periksa_darah', ['Ya', 'Tidak'])->nullable();
            $table->enum('serologi', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->string('arv_profilaksis')->nullable();
            $table->string('hiv_lainnya')->nullable();
            
            // Tatalaksana - TB
            $table->enum('diperiksa_dahak', ['Ya', 'Tidak'])->nullable();
            $table->enum('tbc', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->string('obat_tb')->nullable();
            $table->string('sisa_obat')->nullable();
            $table->string('tb_lainnya')->nullable();
            
            // Tatalaksana - Malaria
            $table->enum('diberikan_kelambu', ['Ya', 'Tidak'])->nullable();
            $table->enum('darah_malaria_rdt', ['Ya', 'Tidak'])->nullable();
            $table->enum('darah_malaria_mikroskopis', ['Ya', 'Tidak'])->nullable();
            $table->enum('ibu_hamil_malaria_rdt', ['Ya', 'Tidak'])->nullable();
            $table->enum('ibu_hamil_malaria_mikroskopis', ['Ya', 'Tidak'])->nullable();
            $table->enum('hasil_test_malaria', ['Negatif (-)', 'Positif (+)'])->nullable();
            $table->string('obat_malaria')->nullable();
            $table->string('malaria_lainnya')->nullable();
            
            // 11. Konseling / Temu Wicara (T10)
            $table->text('materi');
            $table->text('rekomendasi');
            $table->enum('konseling_menyusui', ['Ya', 'Tidak']);
            $table->enum('tanda_bahaya_kehamilan', ['Ya', 'Tidak']);
            $table->enum('tanda_bahaya_persalinan', ['Ya', 'Tidak']);
            $table->enum('konseling_phbs', ['Ya', 'Tidak']);
            $table->enum('konseling_gizi', ['Ya', 'Tidak']);
            $table->enum('konseling_ibu_hamil', ['Ya', 'Tidak']);
            $table->string('konseling_lainnya')->nullable();
            
            // 12. Tindak Lanjut
            $table->string('tindak_lanjut')->nullable();
            $table->text('detail_tindak_lanjut')->nullable();
            $table->date('tanggal_kunjungan_berikutnya')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes untuk pencarian cepat
            $table->index('no_rawat');
            $table->index('no_rkm_medis');
            $table->index('tanggal_anc');
            $table->index('id_hamil');
            $table->index('id_anc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemeriksaan_anc_new');
    }
};
