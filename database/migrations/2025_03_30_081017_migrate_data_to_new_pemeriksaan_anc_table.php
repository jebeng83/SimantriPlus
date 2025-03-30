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
        // Migrasi data dari tabel lama ke tabel baru
        $oldRecords = DB::table('pemeriksaan_anc')->get();
        
        foreach ($oldRecords as $record) {
            DB::table('pemeriksaan_anc_new')->insert([
                // ID dan Informasi Dasar
                'id_anc' => $record->id_anc,
                'no_rawat' => $record->no_rawat,
                'no_rkm_medis' => $record->no_rkm_medis,
                'id_hamil' => $record->id_hamil,
                'tanggal_anc' => $record->tanggal_anc,
                'diperiksa_oleh' => $record->diperiksa_oleh,
                
                // Informasi Kunjungan
                'usia_kehamilan' => $record->usia_kehamilan,
                'trimester' => $record->trimester,
                'kunjungan_ke' => $record->kunjungan_ke,
                'keadaan_pulang' => $record->keadaan_pulang,
                
                // 1. Anamnesis
                'keluhan_utama' => $record->keluhan_utama,
                'gravida' => $record->gravida,
                'partus' => $record->partus,
                'abortus' => $record->abortus,
                'hidup' => $record->hidup,
                'riwayat_penyakit' => $record->riwayat_penyakit,
                
                // 2. Pemeriksaan Fisik - BB & TB (T1)
                'berat_badan' => $record->berat_badan,
                'tinggi_badan' => $record->tinggi_badan,
                'imt' => $record->imt,
                'kategori_imt' => $record->kategori_imt,
                'jumlah_janin' => $record->jumlah_janin,
                
                // 3. Status Gizi (T3)
                'lila' => $record->lila,
                'status_gizi' => $record->status_gizi,
                
                // 4. Tekanan Darah (T2)
                'td_sistole' => $record->td_sistole,
                'td_diastole' => $record->td_diastole,
                
                // 5. Tinggi Fundus Uteri (T4)
                'tinggi_fundus' => $record->tinggi_fundus,
                'taksiran_berat_janin' => $record->taksiran_berat_janin,
                
                // 6. DJJ dan Presentasi (T5)
                'denyut_jantung_janin' => $record->denyut_jantung_janin,
                'presentasi' => $record->presentasi,
                'presentasi_janin' => $record->presentasi_janin,
                
                // 7. Status Imunisasi TT (T6)
                'status_tt' => $record->status_tt,
                'imunisasi_tt' => $record->imunisasi_tt,
                'tanggal_imunisasi' => $record->tanggal_imunisasi,
                
                // 8. Tablet Fe (T7)
                'jumlah_fe' => $record->jumlah_fe,
                'dosis' => $record->dosis,
                
                // 9. Pemeriksaan Lab (T8)
                'tanggal_lab' => $record->tanggal_lab,
                'lab' => $record->lab,
                'hasil_pemeriksaan_hb' => $record->hasil_pemeriksaan_hb,
                'hasil_pemeriksaan_urine_protein' => $record->hasil_pemeriksaan_urine_protein,
                'hasil_pemeriksaan_urine_reduksi' => $record->hasil_pemeriksaan_urine_reduksi,
                'pemeriksaan_lab' => $record->pemeriksaan_lab,
                'rujukan_ims' => $record->rujukan_ims,
                'perawatan_payudara' => $record->perawatan_payudara,
                
                // 10. Tatalaksana Kasus (T9)
                'jenis_tatalaksana' => $record->jenis_tatalaksana,
                
                // Tatalaksana - Anemia
                'diberikan_tablet_fe' => $record->diberikan_tablet_fe,
                'jumlah_tablet_dikonsumsi' => $record->jumlah_tablet_dikonsumsi,
                'jumlah_tablet_ditambahkan' => $record->jumlah_tablet_ditambahkan,
                'tatalaksana_lainnya' => $record->tatalaksana_lainnya,
                
                // Tatalaksana - Makanan Tambahan
                'pemberian_mt' => $record->pemberian_mt,
                'jumlah_mt' => $record->jumlah_mt,
                
                // Tatalaksana - Hipertensi
                'pantau_tekanan_darah' => $record->pantau_tekanan_darah,
                'pantau_protein_urine' => $record->pantau_protein_urine,
                'pantau_kondisi_janin' => $record->pantau_kondisi_janin,
                'hipertensi_lainnya' => $record->hipertensi_lainnya,
                
                // Tatalaksana - Eklampsia
                'pantau_tekanan_darah_eklampsia' => $record->pantau_tekanan_darah_eklampsia,
                'pantau_protein_urine_eklampsia' => $record->pantau_protein_urine_eklampsia,
                'pantau_kondisi_janin_eklampsia' => $record->pantau_kondisi_janin_eklampsia,
                'pemberian_antihipertensi' => $record->pemberian_antihipertensi,
                'pemberian_mgso4' => $record->pemberian_mgso4,
                'pemberian_diazepam' => $record->pemberian_diazepam,
                
                // Tatalaksana - KEK
                'edukasi_gizi' => $record->edukasi_gizi,
                'kek_lainnya' => $record->kek_lainnya,
                
                // Tatalaksana - Obesitas
                'edukasi_gizi_obesitas' => $record->edukasi_gizi_obesitas,
                'obesitas_lainnya' => $record->obesitas_lainnya,
                
                // Tatalaksana - Infeksi
                'pemberian_antipiretik' => $record->pemberian_antipiretik,
                'pemberian_antibiotik' => $record->pemberian_antibiotik,
                'infeksi_lainnya' => $record->infeksi_lainnya,
                
                // Tatalaksana - Penyakit Jantung
                'edukasi' => $record->edukasi,
                'jantung_lainnya' => $record->jantung_lainnya,
                
                // Tatalaksana - HIV
                'datang_dengan_hiv' => $record->datang_dengan_hiv,
                'persalinan_pervaginam' => $record->persalinan_pervaginam,
                'persalinan_perapdoinam' => $record->persalinan_perapdoinam,
                'ditawarkan_tes' => $record->ditawarkan_tes,
                'dilakukan_tes' => $record->dilakukan_tes,
                'hasil_tes_hiv' => $record->hasil_tes_hiv,
                'mendapatkan_art' => $record->mendapatkan_art,
                'vct_pict' => $record->vct_pict,
                'periksa_darah' => $record->periksa_darah,
                'serologi' => $record->serologi,
                'arv_profilaksis' => $record->arv_profilaksis,
                'hiv_lainnya' => $record->hiv_lainnya,
                
                // Tatalaksana - TB
                'diperiksa_dahak' => $record->diperiksa_dahak,
                'tbc' => $record->tbc,
                'obat_tb' => $record->obat_tb,
                'sisa_obat' => $record->sisa_obat,
                'tb_lainnya' => $record->tb_lainnya,
                
                // Tatalaksana - Malaria
                'diberikan_kelambu' => $record->diberikan_kelambu,
                'darah_malaria_rdt' => $record->darah_malaria_rdt,
                'darah_malaria_mikroskopis' => $record->darah_malaria_mikroskopis,
                'ibu_hamil_malaria_rdt' => $record->ibu_hamil_malaria_rdt,
                'ibu_hamil_malaria_mikroskopis' => $record->ibu_hamil_malaria_mikroskopis,
                'hasil_test_malaria' => $record->hasil_test_malaria,
                'obat_malaria' => $record->obat_malaria,
                'malaria_lainnya' => $record->malaria_lainnya,
                
                // 11. Konseling / Temu Wicara (T10)
                'materi' => $record->materi,
                'rekomendasi' => $record->rekomendasi,
                'konseling_menyusui' => $record->konseling_menyusui,
                'tanda_bahaya_kehamilan' => $record->tanda_bahaya_kehamilan,
                'tanda_bahaya_persalinan' => $record->tanda_bahaya_persalinan,
                'konseling_phbs' => $record->konseling_phbs ?? 'Ya',
                'konseling_gizi' => $record->konseling_gizi ?? 'Ya',
                'konseling_ibu_hamil' => $record->konseling_ibu_hamil ?? 'Ya',
                'konseling_lainnya' => $record->konseling_lainnya,
                
                // 12. Tindak Lanjut
                'tindak_lanjut' => $record->tindak_lanjut,
                'detail_tindak_lanjut' => $record->detail_tindak_lanjut,
                'tanggal_kunjungan_berikutnya' => $record->tanggal_kunjungan_berikutnya,
                
                // Timestamps
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
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
        // Hapus semua data dari tabel baru (karena akan mengulang migrasi)
        DB::table('pemeriksaan_anc_new')->truncate();
    }
};
