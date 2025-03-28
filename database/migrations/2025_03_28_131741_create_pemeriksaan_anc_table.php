<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemeriksaan_anc', function (Blueprint $table) {
            $table->id();
            
            // Data dasar pemeriksaan
            $table->string('no_rawat', 20);
            $table->string('no_rkm_medis', 15);
            $table->dateTime('tanggal_anc');
            $table->string('diperiksa_oleh', 255);
            $table->integer('usia_kehamilan');
            $table->integer('trimester');
            $table->integer('kunjungan_ke')->nullable();
            
            // Pemeriksaan Fisik (T1) - Berat Badan dan Tinggi Badan
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->decimal('lila', 5, 2)->nullable(); // Lingkar Lengan Atas
            $table->decimal('imt', 5, 2);
            $table->string('kategori_imt', 50);
            $table->string('jumlah_janin', 50)->nullable();
            
            // Pemeriksaan Fisik (T3) - Tinggi Fundus Uteri
            $table->decimal('tinggi_fundus', 5, 2)->nullable();
            
            // Pemeriksaan Fisik (T2) - Tekanan Darah
            $table->integer('td_sistole');
            $table->integer('td_diastole');
            
            // Pemberian Tablet Fe (T4)
            $table->integer('jumlah_fe');
            $table->integer('dosis');
            
            // Status Imunisasi TT (T5)
            $table->string('imunisasi_tt', 50)->nullable();
            
            // Pemeriksaan Lab (T6, T7, T8, T9)
            $table->decimal('hasil_pemeriksaan_hb', 5, 2)->nullable(); // T6
            $table->string('hasil_pemeriksaan_urine_protein', 50)->nullable(); // T8
            $table->string('hasil_pemeriksaan_urine_reduksi', 50)->nullable(); // T9
            $table->string('pemeriksaan_lab', 255)->nullable(); // Pemeriksaan lab lainnya termasuk T7 (VDRL/Sifilis)
            
            // Perawatan Payudara (T10)
            $table->enum('perawatan_payudara', ['Ya', 'Tidak'])->nullable();
            
            // Pemeriksaan Presentasi Janin dan DJJ (T11)
            $table->string('presentasi_janin', 50)->nullable();
            $table->integer('denyut_jantung_janin')->nullable();
            
            // Jenis Tatalaksana
            $table->string('jenis_tatalaksana', 255)->nullable();
            
            // Tatalaksana - Anemia
            $table->enum('diberikan_tablet_fe', ['Ya', 'Tidak'])->nullable();
            $table->integer('jumlah_tablet_dikonsumsi')->nullable()->default(0);
            $table->integer('jumlah_tablet_ditambahkan')->nullable()->default(0);
            $table->string('tatalaksana_lainnya')->nullable();
            
            // Tatalaksana - Makanan Tambahan Ibu Hamil
            $table->enum('pemberian_mt', ['MT Lokal', 'MT Pabrikan'])->nullable();
            $table->integer('jumlah_mt')->nullable()->default(0);
            
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
            
            // Konseling/Temu Wicara (T12)
            $table->text('materi');
            $table->text('rekomendasi');
            $table->enum('konseling_menyusui', ['Ya', 'Tidak']);
            $table->enum('tanda_bahaya_kehamilan', ['Ya', 'Tidak']);
            $table->enum('tanda_bahaya_persalinan', ['Ya', 'Tidak']);
            $table->enum('konseling_phbs', ['Ya', 'Tidak']);
            $table->enum('konseling_gizi', ['Ya', 'Tidak']);
            $table->enum('konseling_ibu_hamil', ['Ya', 'Tidak']);
            $table->string('konseling_lainnya')->nullable();
            
            // Status Pulang
            $table->string('keadaan_pulang');
            
            // Index untuk pencarian
            $table->index('no_rawat');
            $table->index('no_rkm_medis');
            $table->index('tanggal_anc');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_anc');
    }
};
