# PCare BPJS Integration - Pemeriksaan Ralan

## Overview
Fitur ini menambahkan integrasi otomatis dengan PCare BPJS ketika menyimpan data pemeriksaan rawat jalan. Sistem akan secara otomatis mendaftarkan pasien BPJS (BPJ, PBI, NON) ke PCare setelah pemeriksaan berhasil disimpan.

## Fitur yang Ditambahkan

### 1. Auto-Registration ke PCare
- Ketika dokter menyimpan pemeriksaan, sistem akan mengecek apakah pasien adalah peserta BPJS
- Jika ya, sistem akan otomatis mendaftarkan pasien ke PCare BPJS
- Proses ini berjalan di background dan tidak mengganggu workflow pemeriksaan

### 2. Validasi Otomatis
- Sistem mengecek apakah pasien memiliki kd_pj dalam ['BPJ', 'PBI', 'NON']
- Sistem mengecek apakah pasien memiliki nomor peserta BPJS
- Mengecek apakah pasien sudah terdaftar di PCare hari ini
- Hanya mendaftarkan jika belum terdaftar

### 3. Data yang Dikirim ke PCare (Sesuai Katalog BPJS)
- `kdProviderPeserta`: From `BPJS_PCARE_KODE_PPK` environment variable
- `tglDaftar`: Current date (d-m-Y format)
- `noKartu`: Patient's BPJS card number from `pasien.no_peserta`
- `kdPoli`: From `maping_poliklinik_pcare` table mapping
- `keluhan`: Patient's complaint (empty string if not provided)
- `kunjSakit`: true
- `sistole`: From examination data (tensi field, default 120)
- `diastole`: From examination data (tensi field, default 80)
- `beratBadan`: From examination data (berat field, default 0)
- `tinggiBadan`: From examination data (tinggi field, default 0)
- `respRate`: From examination data (respirasi field, default 0)
- `lingkarPerut`: From examination data (lingkar field, default 0)
- `heartRate`: From examination data (nadi field, default 0)
- `rujukBalik`: 0
- `kdTkp`: '10'

## File yang Dimodifikasi

### 1. `app/Http/Livewire/Ralan/Pemeriksaan.php`
- Menambahkan method `daftarPcareBpjs()` untuk handle registrasi PCare
- Menambahkan method `mapPoliToPcare()` untuk mapping kode poli
- Memodifikasi `simpanPemeriksaan()` untuk memanggil registrasi PCare

### 2. `resources/views/livewire/ralan/pemeriksaan.blade.php`
- Menambahkan informasi visual bahwa sistem akan mendaftarkan pasien BPJS ke PCare

### 3. `.env.example`
- Menambahkan konfigurasi `BPJS_PCARE_PROVIDER`

## Konfigurasi Environment

Pastikan environment variables berikut sudah dikonfigurasi:

```env
BPJS_PCARE_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/pcare-rest
BPJS_PCARE_CONS_ID=7925
BPJS_PCARE_CONS_PWD=2eF2C8E837
BPJS_PCARE_USER_KEY=403bf17ddf158790afcfe1e8dd682a67
BPJS_PCARE_USER=11251919
BPJS_PCARE_PASS=Pcare154#
BPJS_PCARE_KODE_PPK=11251919
BPJS_PCARE_APP_CODE=095
```

## Mapping Poli

Sistem menggunakan database table `maping_poliklinik_pcare` untuk mapping kode poli internal (`kd_poli_rs`) ke kode poli PCare (`kd_poli_pcare`). Jika tidak ditemukan mapping, sistem akan menggunakan default '001' (Poli Umum).

Pastikan table `maping_poliklinik_pcare` sudah dikonfigurasi dengan benar sesuai mapping yang diperlukan.

## Logging

Semua aktivitas PCare registration dicatat dalam log dengan level:
- **INFO**: Registrasi berhasil atau pasien bukan BPJS
- **WARNING**: Registrasi gagal dari sisi PCare
- **ERROR**: Error sistem atau koneksi

## Error Handling

- Jika registrasi PCare gagal, pemeriksaan tetap tersimpan
- Error tidak mengganggu workflow utama
- Semua error dicatat dalam log untuk debugging

## Testing

Untuk testing fitur ini:
1. Pastikan konfigurasi PCare sudah benar
2. Gunakan pasien dengan status BPJS (kd_pj = 'BPJ')
3. Pastikan pasien memiliki nomor peserta BPJS
4. Lakukan pemeriksaan dan simpan
5. Cek log untuk memastikan registrasi berjalan

## Troubleshooting

### Registrasi PCare Tidak Berjalan
1. Cek apakah pasien memiliki kd_pj = 'BPJ'
2. Cek apakah pasien memiliki nomor peserta BPJS
3. Cek konfigurasi environment PCare
4. Cek log aplikasi untuk error details

### Error Koneksi PCare
1. Pastikan URL PCare dapat diakses
2. Cek kredensial PCare
3. Cek timeout setting (default 30 detik)

## Catatan Penting

- Fitur ini hanya berjalan untuk pasien BPJS
- Registrasi hanya dilakukan sekali per hari per pasien
- Proses berjalan asynchronous untuk tidak mengganggu UX
- Semua data vital signs dari pemeriksaan akan dikirim ke PCare