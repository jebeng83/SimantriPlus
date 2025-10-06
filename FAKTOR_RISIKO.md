# Faktor Risiko PKG (PWS) – Definisi dan Implementasi

Dokumen ini merangkum definisi faktor risiko dan klasifikasi risiko yang digunakan pada Dashboard PWS (Pemantauan Wilayah Setempat) untuk skrining PKG, serta sumber data dan ambang batas klinis yang dipakai di backend dan ditampilkan pada tabel “Analisis per Posyandu”.

## Tujuan
- Menyeragamkan definisi Risiko Tinggi, Risiko Sedang, dan Risiko Rendah berbasis pengukuran klinis, riwayat faktor risiko, dan umur.
- Menjelaskan metrik definisi klinis yang ditampilkan sebagai kolom baru di tabel Analisis per Posyandu.
- Mencatat sumber kolom data dan pemetaan filter Desa/Posyandu agar hasil sesuai opsi filter.

## Sumber Data
- Tabel utama: `skrining_pkg (sp)`
  - Kolom yang digunakan: `tekanan_sistolik`, `tekanan_diastolik`, `gds`, `gdp`, `berat_badan`, `tinggi_badan`, `umur`, `riwayat_hipertensi`, `riwayat_diabetes`, `status_merokok`, `kode_posyandu`, `kd_kel`, `tanggal_skrining`.
- Tabel tambahan (join): `skrining_siswa_sd (ssd)`
  - Kolom yang digunakan: `id_pkg`, `sistole` (sistolik), `hasil_gds` (GDS), `imt` (BMI/IMT).
- Referensi lokasi: `data_posyandu (dp)`
  - Kolom yang digunakan: `kode_posyandu`, `nama_posyandu`, `desa`.
- Referensi kelurahan: `kelurahan (k)`
  - Kolom yang digunakan: `kd_kel`, `nm_kel` (tidak dipakai lagi untuk filter/penamaan desa pada tabel Analisis).

## Ambang Batas Klinis
- Hipertensi terdeteksi: sistolik ≥ 140 mmHg atau diastolik ≥ 90 mmHg.
- Diabetes terdeteksi: GDS ≥ 200 mg/dL atau GDP ≥ 126 mg/dL.
- Obesitas (BMI tinggi): BMI ≥ 30 kg/m².
- Overweight: 25 ≤ BMI < 30 kg/m².
- Underweight: BMI < 18.5 kg/m².

Rumus BMI: `BMI = berat_badan (kg) / (tinggi_badan (m))^2`.
- Jika data BMI/IMT tersedia di `ssd.imt`, nilai tersebut digunakan sebagai alternatif.

## Definisi Klasifikasi Risiko
- Risiko Tinggi: jika salah satu kondisi berikut terpenuhi:
  - Pengukuran klinis tinggi:
    - Sistolik ≥ 140 atau Diastolik ≥ 90
    - GDS ≥ 200 atau GDP ≥ 126
    - BMI ≥ 30
  - Kombinasi riwayat yang kuat:
    - Riwayat hipertensi = “Ya” DAN riwayat diabetes = “Ya”
    - Status merokok = “Ya” DAN riwayat hipertensi = “Ya”
    - Umur ≥ 60 DAN riwayat diabetes = “Ya”

- Risiko Sedang: jika memenuhi kondisi klinis sedang atau faktor tunggal, dan BUKAN termasuk Risiko Tinggi:
  - Sistolik 120–139 atau Diastolik 80–89, atau
  - 25 ≤ BMI < 30, atau
  - Merokok = “Ya”, atau
  - Riwayat hipertensi = “Ya”, atau
  - Riwayat diabetes = “Ya”.

- Risiko Rendah: bukan Risiko Tinggi dan bukan Risiko Sedang.

## Kolom Definisi Klinis di “Analisis per Posyandu”
Ditambahkan metrik agregat per posyandu untuk memudahkan interpretasi:
- `TD ≥ 140`: jumlah penduduk dengan sistolik ≥ 140 (menggunakan `COALESCE(sp.tekanan_sistolik, ssd.sistole)`).
- `GDS ≥ 200`: jumlah penduduk dengan GDS ≥ 200 (menggunakan `COALESCE(sp.gds, ssd.hasil_gds)`).
- `GDP ≥ 126`: jumlah penduduk dengan GDP ≥ 126 (menggunakan `sp.gdp`).
- `BMI ≥ 30`: jumlah penduduk dengan BMI ≥ 30 (menggunakan perhitungan dari `sp.berat_badan`/`sp.tinggi_badan` atau `ssd.imt`).

## Konsistensi Filter Desa dan Posyandu
- Desa yang digunakan pada tabel Analisis berasal dari `dp.desa` (bukan `k.nm_kel`).
- Filter Desa pada query menggunakan `dp.desa` secara konsisten.
- Pengelompokan data di Analisis: `GROUP BY dp.nama_posyandu, dp.desa` agar kombinasi Posyandu–Desa konsisten dengan data master posyandu.

## Lokasi Implementasi di Kode
- Controller: `app/Http/Controllers/ILP/DashboardController.php`
  - `getAnalisisPkgAjax`: agregasi per posyandu (join `ssd` dan penambahan metrik TD≥140, GDS≥200, GDP≥126, BMI≥30; filter desa/posyandu, periode).
  - `getAnalisisPkg`: agregasi non-AJAX (konsisten definisi risiko dan filter).
  - `getSummaryPkg`: kartu ringkasan (definisi risiko seragam, filter `dp.desa`).
  - `getFaktorRisikoPkg`: metrik faktor lain (underweight, kolesterol, aktivitas, dsb.).

## Catatan
- Unit ambang batas: mmHg (TD), mg/dL (GDS/GDP), kg/m² (BMI).
- Penamaan kolom output pada API Analisis menyesuaikan dengan frontend:
  - `risiko_tinggi`, `risiko_sedang`, `risiko_rendah`, `total_skrining`, `persen_tinggi`, dan kolom tambahan: `td_ge_140`, `gds_ge_200`, `gdp_ge_126`, `bmi_ge_30`.
- Untuk kasus penamaan Desa/Posyandu yang tidak sesuai hasil filter, pastikan data master `data_posyandu.desa` dan `data_posyandu.nama_posyandu` telah bersih dari tanda `-` dan konsisten.

## Pengembangan Lanjutan
- Menambahkan definisi diastolik ≥ 90 sebagai kolom agregat terpisah jika diperlukan.
- Menyediakan tooltip di UI yang menjelaskan setiap ambang batas dan sumber data.
- Audit data ganda antar `skrining_pkg` dan `skrining_siswa_sd` jika ditemukan ketidaksesuaian jumlah.