# Dokumentasi Relasi Tabel Data Siswa Sekolah

## Overview
Dokumentasi ini menjelaskan struktur tabel dan relasi yang digunakan dalam fungsi edit data siswa sekolah.

## Tabel yang Terlibat

### 1. data_siswa_sekolah (Tabel Utama)
**Primary Key:** `id`

**Fields:**
- `id` - Primary key
- `nis` - Nomor Induk Siswa
- `nisn` - Nomor Induk Siswa Nasional
- `nama_siswa` - Nama lengkap siswa
- `nik` - Nomor Induk Kependudukan (16 digit)
- `jenis_kelamin` - L/P
- `tanggal_lahir` - Tanggal lahir siswa
- `tempat_lahir` - Tempat lahir siswa
- `alamat` - Alamat siswa
- `nama_ayah` - Nama ayah
- `nama_ibu` - Nama ibu
- `no_telepon_ortu` - Nomor telepon orang tua
- `id_sekolah` - Foreign key ke data_sekolah
- `id_kelas` - Foreign key ke data_kelas
- `tanggal_masuk` - Tanggal masuk sekolah
- `status` - Status siswa (Aktif, Tidak Aktif, Lulus, Pindah, Keluar, Drop Out)
- `no_rkm_medis` - Foreign key ke pasien

### 2. pasien
**Primary Key:** `no_rkm_medis`

**Fields:**
- `no_rkm_medis` - Primary key
- `nm_pasien` - Nama pasien
- `no_ktp` - Nomor KTP/NIK
- `jk` - Jenis kelamin
- `tmp_lahir` - Tempat lahir
- `tgl_lahir` - Tanggal lahir
- `alamat` - Alamat

**Relasi:** 
- `data_siswa_sekolah.no_rkm_medis = pasien.no_rkm_medis`

### 3. data_sekolah
**Primary Key:** `id_sekolah`

**Fields:**
- `id_sekolah` - Primary key
- `nama_sekolah` - Nama sekolah
- `id_jenis_sekolah` - Foreign key ke jenis_sekolah
- `kd_kel` - Kode kelurahan

**Relasi:**
- `data_siswa_sekolah.id_sekolah = data_sekolah.id_sekolah`

### 4. jenis_sekolah
**Primary Key:** `id`

**Fields:**
- `id` - Primary key
- `nama_jenis_sekolah` - Nama jenis sekolah (SD, SMP, SMA, dll)
- `keterangan` - Keterangan tambahan

**Relasi:**
- `data_sekolah.id_jenis_sekolah = jenis_sekolah.id`

### 5. data_kelas
**Primary Key:** `id_kelas`

**Fields:**
- `id_kelas` - Primary key
- `sekolah_id` - Foreign key ke data_sekolah
- `kelas` - Nama kelas
- `tingkat` - Tingkat kelas
- `wali_kelas` - Nama wali kelas
- `jumlah_siswa` - Jumlah siswa dalam kelas
- `status` - Status kelas

**Relasi:**
- `data_siswa_sekolah.id_kelas = data_kelas.id_kelas`
- `data_kelas.sekolah_id = data_sekolah.id_sekolah`

## Diagram Relasi

```
data_siswa_sekolah (Tabel Utama)
├── pasien (via no_rkm_medis)
├── data_sekolah (via id_sekolah)
│   ├── jenis_sekolah (via id_jenis_sekolah)
│   └── kelurahan (via kd_kel)
└── data_kelas (via id_kelas)
    └── data_sekolah (via sekolah_id)
```

## Query Join yang Digunakan

### Di Controller (Method Edit)
```php
$siswa = DataSiswaSekolah::with([
    'sekolah.jenisSekolah',  // Join ke data_sekolah dan jenis_sekolah
    'sekolah.kelurahan',     // Join ke kelurahan
    'kelas',                 // Join ke data_kelas
    'pasien'                 // Join ke pasien
])->findOrFail($id);
```

### Relasi di Model DataSiswaSekolah
```php
// Relasi dengan sekolah
public function sekolah()
{
    return $this->belongsTo(DataSekolah::class, 'id_sekolah', 'id_sekolah');
}

// Relasi dengan kelas
public function kelas()
{
    return $this->belongsTo(DataKelas::class, 'id_kelas', 'id_kelas');
}

// Relasi dengan pasien
public function pasien()
{
    return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
}
```

## Perbaikan yang Dilakukan

### 1. Controller (DataSiswaSekolahController.php)
- **Method edit():** Ditambahkan eager loading untuk semua relasi
- **Method update():** Diperbaiki validasi dan update data ke tabel terkait
- Menambahkan update data pasien secara otomatis

### 2. View (edit.blade.php)
- **Form fields:** Diperbaiki nama field sesuai dengan database
- **Dropdown:** Menampilkan data dengan relasi yang benar
- **Informasi tabel:** Ditambahkan section untuk menampilkan data dari semua tabel
- **Validasi:** Diperbaiki JavaScript validation

### 3. Fitur Tambahan
- Menampilkan informasi lengkap dari semua tabel yang di-join
- Validasi form yang lebih komprehensif
- Update otomatis data pasien saat update data siswa
- Dropdown yang menampilkan relasi dengan jelas

## Cara Menggunakan

1. **Akses halaman edit:** `/ilp/data-siswa-sekolah/{id}/edit`
2. **Lihat informasi tabel:** Section biru di atas form menampilkan data dari semua tabel
3. **Edit data:** Form sudah terhubung dengan semua relasi
4. **Submit:** Data akan terupdate di tabel utama dan tabel pasien

## Catatan Penting

- Pastikan relasi foreign key sudah benar di database
- Data pasien akan ikut terupdate saat update data siswa
- Dropdown sekolah dan kelas menampilkan informasi relasi
- Validasi form mencakup semua field yang diperlukan