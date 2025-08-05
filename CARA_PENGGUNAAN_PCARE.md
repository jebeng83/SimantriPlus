# Cara Penggunaan Pendaftaran PCare

Dokumentasi ini menjelaskan cara menggunakan fitur pendaftaran PCare yang terintegrasi dengan sistem eDoktor.

## Daftar Isi
- [Persyaratan](#persyaratan)
- [Konfigurasi](#konfigurasi)
- [Cara Penggunaan](#cara-penggunaan)
- [Testing via Terminal](#testing-via-terminal)
- [Troubleshooting](#troubleshooting)
- [FAQ](#faq)

## Persyaratan

### 1. Pasien BPJS
- Pasien harus memiliki status penjamin BPJS (`kd_pj = 'BPJ'`)
- Pasien harus memiliki nomor peserta BPJS yang valid
- Nomor peserta tersimpan di field `no_peserta` pada tabel `pasien`

### 2. Konfigurasi Environment
Pastikan semua environment variables PCare sudah dikonfigurasi dengan benar:

```env
BPJS_PCARE_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/pcare-rest
BPJS_PCARE_CONS_ID=7925
BPJS_PCARE_CONS_PWD=2eF2C8E837
BPJS_PCARE_USER_KEY=403bf17ddf158790afcfe1e8dd682a67
BPJS_PCARE_USER=11251616
BPJS_PCARE_PASS=Pcare154#
BPJS_PCARE_KODE_PPK=11251616
BPJS_PCARE_APP_CODE=095
```

### 3. Mapping Poliklinik
Pastikan tabel `maping_poliklinik_pcare` sudah dikonfigurasi untuk mapping kode poli internal ke kode poli PCare.

## Konfigurasi

### 1. Environment Variables
Salin konfigurasi dari `.env.example` dan sesuaikan dengan kredensial PCare Anda:

```bash
cp .env.example .env
# Edit file .env dan tambahkan konfigurasi PCare
```

### 2. Mapping Poliklinik
Tambahkan mapping poliklinik di tabel `maping_poliklinik_pcare`:

```sql
INSERT INTO maping_poliklinik_pcare (kd_poli_rs, kd_poli_pcare, nm_poli_pcare) VALUES
('U0001', '001', 'Poli Umum'),
('U0002', '001', 'Poli Umum'),
('INT', '002', 'Poli Dalam'),
('ANA', '003', 'Anak'),
('OBG', '004', 'Kandungan'),
('BED', '005', 'Bedah');
```

## Cara Penggunaan

### 1. Pendaftaran Otomatis
Sistem akan secara otomatis mendaftarkan pasien BPJS ke PCare saat:
- Melakukan pemeriksaan pasien BPJS
- Menyimpan data pemeriksaan
- Pasien belum terdaftar di PCare pada hari yang sama

### 2. Proses Pendaftaran
1. **Buka halaman pemeriksaan** (`/ralan/pemeriksaan`)
2. **Pilih pasien BPJS** dari daftar registrasi
3. **Isi data pemeriksaan** seperti biasa:
   - Keluhan
   - Vital signs (tekanan darah, berat badan, dll)
   - Diagnosis
   - Terapi
4. **Klik tombol Simpan**
5. **Sistem otomatis** akan:
   - Menyimpan data pemeriksaan
   - Mengirim data pendaftaran ke PCare
   - Mencatat hasil di log

### 3. Data yang Dikirim ke PCare
Sistem akan mengirim data berikut ke PCare:

```json
{
  "kdProviderPeserta": "11251616",
  "tglDaftar": "05-08-2025",
  "noKartu": "0001441909697",
  "kdPoli": "003",
  "keluhan": "Kontrol rutin",
  "kunjSakit": true,
  "sistole": 120,
  "diastole": 80,
  "beratBadan": 70.0,
  "tinggiBadan": 170.0,
  "respRate": 20,
  "lingkarPerut": 80.0,
  "heartRate": 80,
  "rujukBalik": 0,
  "kdTkp": "10"
}
```

### 4. Response PCare
**Berhasil (Status 201):**
```json
{
  "metaData": {
    "code": "201",
    "message": "CREATED"
  },
  "response": {
    "field": "noUrut",
    "message": "C22"
  }
}
```

**Sudah Terdaftar (Status 401):**
```json
{
  "metaData": {
    "code": "401",
    "message": "Peserta sudah di-entri di poli yang sama pada hari yang sama."
  }
}
```

## Testing via Terminal

Untuk melakukan testing pendaftaran PCare via terminal:

### 1. Buat Script Test
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Traits\PcareTrait;

class TestPcare {
    use PcareTrait;
    
    public function testRegistrasi() {
        // Ambil data registrasi BPJS hari ini
        $registrasi = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
            ->where('penjab.png_jawab', 'LIKE', '%BPJS%')
            ->first();
            
        if (!$registrasi) {
            echo "Tidak ada registrasi BPJS hari ini\n";
            return;
        }
        
        // Siapkan data PCare
        $data = [
            'kdProviderPeserta' => env('BPJS_PCARE_KODE_PPK'),
            'tglDaftar' => date('d-m-Y'),
            'noKartu' => $registrasi->no_peserta,
            'kdPoli' => '001',
            'keluhan' => 'Test pendaftaran',
            'kunjSakit' => true,
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 70.0,
            'tinggiBadan' => 170.0,
            'respRate' => 20,
            'lingkarPerut' => 80.0,
            'heartRate' => 80,
            'rujukBalik' => 0,
            'kdTkp' => '10'
        ];
        
        // Kirim ke PCare
        $response = $this->requestPcare('pendaftaran', 'POST', $data, 'text/plain');
        
        echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    }
}

$test = new TestPcare();
$test->testRegistrasi();
?>
```

### 2. Jalankan Test
```bash
php test_pcare.php
```

## Troubleshooting

### 1. Error "body request option as an array is not supported"
**Penyebab:** Format data yang dikirim tidak sesuai dengan yang diharapkan PCare.

**Solusi:** Pastikan menggunakan `PcareTrait` yang sudah diperbaiki dengan content-type `text/plain`.

### 2. Error 401 "Peserta sudah di-entri"
**Penyebab:** Pasien sudah terdaftar di PCare pada hari yang sama.

**Solusi:** Ini adalah perilaku normal. Sistem akan mencatat di log dan melanjutkan proses.

### 3. Error Koneksi ke PCare
**Penyebab:** 
- Kredensial PCare salah
- URL PCare tidak dapat diakses
- Timeout koneksi

**Solusi:**
1. Periksa konfigurasi environment
2. Test koneksi ke URL PCare
3. Periksa log aplikasi untuk detail error

### 4. Mapping Poli Tidak Ditemukan
**Penyebab:** Kode poli tidak ada di tabel `maping_poliklinik_pcare`.

**Solusi:** Tambahkan mapping poli atau sistem akan menggunakan default '001' (Poli Umum).

### 5. Pasien Tidak Memiliki Nomor Peserta
**Penyebab:** Field `no_peserta` kosong di tabel `pasien`.

**Solusi:** Lengkapi data nomor peserta BPJS pasien.

## FAQ

### Q: Apakah semua pasien akan didaftarkan ke PCare?
**A:** Tidak. Hanya pasien dengan penjamin BPJS (`kd_pj = 'BPJ'`) yang akan didaftarkan.

### Q: Bagaimana jika pendaftaran PCare gagal?
**A:** Pemeriksaan tetap akan tersimpan. Error akan dicatat di log untuk debugging.

### Q: Apakah bisa mendaftarkan ulang pasien yang sama?
**A:** Tidak. PCare akan menolak pendaftaran duplikat pada hari yang sama dengan error 401.

### Q: Data apa saja yang dikirim ke PCare?
**A:** Data meliputi informasi pasien, poli, keluhan, dan vital signs dari pemeriksaan.

### Q: Bagaimana cara melihat log pendaftaran PCare?
**A:** Cek file log di `storage/logs/laravel-{tanggal}.log` dengan keyword "PCare".

### Q: Apakah pendaftaran PCare mempengaruhi kecepatan sistem?
**A:** Tidak. Proses pendaftaran berjalan asynchronous dan tidak mengganggu workflow utama.

### Q: Bagaimana cara mengubah mapping poliklinik?
**A:** Edit data di tabel `maping_poliklinik_pcare` sesuai dengan kode poli PCare yang benar.

### Q: Apakah bisa testing tanpa mengganggu data production?
**A:** Ya. Gunakan script test via terminal atau environment development terpisah.

## Monitoring dan Log

### 1. Log Aplikasi
Semua aktivitas PCare dicatat dalam log dengan format:

```
[2025-08-05 16:02:12] local.INFO: PCare registration berhasil {"no_rawat":"2025/08/05/000002","no_urut":"C22"}
[2025-08-05 16:02:13] local.WARNING: PCare registration gagal {"no_rawat":"2025/08/05/000001","error":"Peserta sudah di-entri"}
```

### 2. Database Tracking
Data pendaftaran PCare disimpan di tabel `pcare_pendaftaran` untuk tracking.

### 3. Monitoring Real-time
Gunakan command berikut untuk monitoring log real-time:

```bash
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log | grep PCare
```

---

**Catatan:** Dokumentasi ini dibuat berdasarkan implementasi sistem eDoktor dengan integrasi PCare. Pastikan selalu menggunakan environment development untuk testing sebelum implementasi di production.