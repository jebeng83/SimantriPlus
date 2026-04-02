Skrining CKG — Ringkasan Mapping Input → Controller → Model

- Identitas (semua form)
  - Controller: berbagai metode simpan* menyisipkan identitas (nik, nama_lengkap, tanggal_lahir, jenis_kelamin, no_handphone, no_rkm_medis).
  - Model: SkriningPkg → nik, nama_lengkap, tanggal_lahir, jenis_kelamin, no_handphone, no_rkm_medis, tanggal_skrining.
  - Catatan: Validasi tahunan via validasiNikTahunan membatasi 1 skrining/nik/tahun.

- Demografi (dewasa)
  - Route: api.skrining.demografi → SkriningController@simpanDemografi
  - Input → Model:
    - status_perkawinan → status_perkawinan
    - status_hamil (opsional) → status_hamil
    - status_disabilitas (opsional) → status_disabilitas
    - kode_posyandu/petugas_entri/status_petugas (opsional, jika kolom ada) → kolom sejenis
  - Catatan: Field rencana_menikah telah dihapus dari UI/JS/Model.

- Kesehatan Jiwa
  - Route: api.skrining.kesehatan-jiwa → SkriningController@simpanKesehatanJiwa
  - Input → Model: minat, sedih, cemas, khawatir → minat, sedih, cemas, khawatir

- Aktivitas Fisik
  - Route: api.skrining.aktivitas-fisik → SkriningController@simpanAktivitasFisik
  - Input → Model:
    - frekuensi_olahraga → frekuensi_olahraga
    - frekuensi_olahraga_1, frekuensi_olahraga_2 (jika Ya) → frekuensi_olahraga_1, frekuensi_olahraga_2
    - aktivitas_fisik_2..6 → aktivitas_fisik_2..6
    - aktivitas_fisik_2..6_hari/menit (jika Ya) → aktivitas_fisik_2..6_hari, aktivitas_fisik_2..6_menit
  - Catatan: Q1 memakai prefix frekuensi_olahraga_*, Q2–Q6 memakai aktivitas_fisik_*.

- Perilaku Merokok
  - Route: api.skrining.perilaku-merokok → SkriningController@simpanPerilakuMerokok
  - Input → Model:
    - status_merokok → status_merokok
    - lama_merokok, jumlah_rokok (required_if status_merokok=Ya) → lama_merokok, jumlah_rokok (null jika Tidak)
    - paparan_asap → paparan_asap
    - riwayat_merokok (opsional; juga dipakai pada PUMA) → riwayat_merokok

- Hati
  - Route: api.skrining.hati → SkriningController@simpanHati
  - Input → Model:
    - riwayat_hepatitis → riwayat_hepatitis
    - riwayat_kuning → riwayat_kuning
    - riwayat_transfusi → riwayat_transfusi
    - riwayat_tindik → riwayat_tindik
    - narkoba_suntik → narkoba_suntik
    - odhiv → odhiv
    - riwayat_tattoo → riwayat_tattoo
    - kolesterol → kolesterol
    - hubungan_intim → hubungan_intim
  - Catatan: Label pertanyaan No.2 menyebut riwayat Hepatitis keluarga namun name saat ini riwayat_kuning.

- Tuberkulosis (dewasa)
  - Route: api.skrining.tuberkulosis → SkriningController@simpanTuberkulosis
  - Input → Model:
    - riwayat_tbc → riwayat_tbc
    - jenis_tbc (required_if riwayat kontak) → jenis_tbc
    - batuk_berdahak → batuk (model)
    - demam → demam

- Antropometri & Laboratorium
  - Route: api.skrining.antropometri-lab → SkriningController@simpanAntropometriLab
  - Input → Model:
    - riwayat_dm, riwayat_ht → riwayat_dm, riwayat_ht
    - tinggi_badan, berat_badan, lingkar_perut → kolom sejenis
    - tekanan_sistolik/diastolik (+_2) → tekanan_sistolik, tekanan_diastolik, tekanan_sistolik_2, tekanan_diastolik_2
    - gds, gdp, kolesterol_lab, trigliserida → bernilai 0 bila kosong

- Skrining Indra
  - Route: api.skrining.skrining-indra → SkriningController@simpanSkriningIndra
  - Input → Model: pendengaran, penglihatan → pendengaran, penglihatan

- Skrining Gigi
  - Route: api.skrining.skrining-gigi → SkriningController@simpanSkriningGigi
  - Input → Model: karies, hilang, goyang → karies, hilang, goyang

- Gangguan Fungsional (Barthel Index)
  - Route: api.skrining.gangguan-fungsional → SkriningController@simpanGangguanFungsional
  - Input → Model:
    - bab, bak, membersihkan_diri, penggunaan_jamban, makan_minum, berubah_sikap, berpindah, memakai_baju, naik_tangga, mandi → kolom sejenis
    - total_skor_barthel, tingkat_ketergantungan → kolom sejenis

- Skrining PUMA
  - Route: api.skrining.skrining-puma → SkriningController@simpanSkriningPuma
  - Input → Model: riwayat_merokok, napas_pendek, dahak, batuk, spirometri → kolom sejenis

- Anak — Gejala DM
  - Route: api.skrining.gejala-dm-anak
  - Input: sering_lapar, sering_haus, sering_pipis, sering_mengompol, berat_turun, riwayat_diabetes_ortu
  - Model: kolom belum dimapping eksplisit pada SkriningPkg (disimpan via endpoint terpisah atau perlu penambahan kolom).

- Anak — Perkembangan 3–6 Tahun
  - Route: api.skrining.perkembangan-3-6-tahun
  - Input: gangguan_emosi, hiperaktif
  - Model: kolom belum dimapping eksplisit pada SkriningPkg.

- Anak — TBC Bayi/Anak
  - Route: api.skrining.tuberkulosis-bayi-anak
  - Input: batuk_lama, berat_turun_tbc, berat_tidak_naik, nafsu_makan_berkurang, kontak_tbc
  - Model: kolom belum dimapping eksplisit pada SkriningPkg.

- Anak — Skrining Pertumbuhan
  - Route: api.skrining.skrining-pertumbuhan
  - Input: berat_badan, tinggi_badan, status_gizi_bb_u, status_gizi_pb_u, status_gizi_bb_pb, hasil_imt_u, status_lingkar_kepala
  - Model: sebagian memanfaatkan kolom numerik di SkriningPkg (berat_badan/tinggi_badan); kolom status gizi/hasil IMT belum ada.

- Anak — KPSP
  - Route: api.skrining.kpsp
  - Input: hasil_kpsp
  - Model: belum ada kolom eksplisit pada SkriningPkg.

- Anak — Telinga & Mata
  - Route: api.skrining.telinga-mata
  - Input: hasil_tes_dengar, hasil_tes_lihat
  - Model: belum ada kolom eksplisit pada SkriningPkg.

Catatan Konsistensi
- rencana_menikah dihilangkan dari UI/JS/Model.
- Form Hati: name riwayat_kuning untuk pertanyaan keluarga Hepatitis (dipertimbangkan untuk penamaan lebih tepat).
- Aktivitas Fisik: Q1 memakai frekuensi_olahraga_*; Q2–Q6 memakai aktivitas_fisik_*.
