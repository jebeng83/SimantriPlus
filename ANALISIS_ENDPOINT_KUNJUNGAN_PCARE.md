# ANALISIS ENDPOINT KUNJUNGAN PCARE

## RINGKASAN MASALAH

Setelah membandingkan implementasi PHP dengan implementasi Java yang sukses, ditemukan perbedaan penting dalam endpoint yang digunakan:

- **PHP (Current)**: `kunjungan` → Error 404 "Unauthorized! You are not registered for this service!"
- **Java (Working)**: `kunjungan/v1` → Error 412 "PRECONDITION_FAILED" dengan pesan lebih spesifik

## TEMUAN PENTING

### 1. Perbedaan Endpoint

| Aspek | PHP Implementation | Java Implementation |
|-------|-------------------|--------------------|
| Endpoint | `kunjungan` | `kunjungan/v1` |
| URL Final | `{base_url}/pcare-rest/kunjungan` | `{base_url}/pcare-rest/kunjungan/v1` |
| Error Code | 404 | 412 |
| Error Message | "Unauthorized! You are not registered for this service!" | "PRECONDITION_FAILED" |

### 2. Analisis Error Messages

#### Error 404 (Endpoint `kunjungan`)
```json
{
    "response": null,
    "metaData": {
        "message": "Unauthorized! You are not registered for this service!",
        "code": 404
    }
}
```
**Interpretasi**: Layanan tidak tersedia atau tidak terdaftar.

#### Error 412 (Endpoint `kunjungan/v1`)
```json
{
    "response": [
        {
            "field": "noKartu",
            "message": "Belum melakukan pendaftaran pada poli tersebut."
        },
        {
            "field": "noKartu",
            "message": "noKartu, belum melakukan pendaftaran pada poli tersebut."
        }
    ],
    "metaData": {
        "message": "PRECONDITION_FAILED",
        "code": 412
    }
}
```
**Interpretasi**: Endpoint dapat diakses, tetapi data tidak memenuhi prasyarat (belum ada pendaftaran).

## KESIMPULAN

### 1. Endpoint Version Matters
- Endpoint `kunjungan/v1` memberikan respons yang lebih informatif
- Error 412 menunjukkan bahwa layanan dapat diakses tetapi ada masalah data
- Error 404 menunjukkan layanan tidak tersedia sama sekali

### 2. Root Cause Analysis
- **Primary Issue**: Aplikasi menggunakan endpoint versi lama (`kunjungan`)
- **Secondary Issue**: Data test tidak memiliki pendaftaran yang valid

## SOLUSI

### 1. Update Endpoint (PRIORITAS TINGGI)

Update aplikasi untuk menggunakan endpoint `kunjungan/v1` seperti implementasi Java:

#### File yang perlu diubah:

**1. `app/Http/Livewire/Ralan/Pemeriksaan.php`**
```php
// Ganti baris ini:
$responseData = $this->requestPcare('kunjungan', 'POST', $kunjunganData, 'text/plain');

// Menjadi:
$responseData = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');
```

**2. `app/Http/Controllers/PcareKunjunganController.php`**
```php
// Ganti baris ini:
$endpoint = 'kunjungan';

// Menjadi:
$endpoint = 'kunjungan/v1';
```

### 2. Validasi Data Pendaftaran

Pastikan data kunjungan menggunakan:
- `noKartu` yang sudah terdaftar di PCare
- `kdPoli` yang valid dan sudah didaftarkan
- `tglDaftar` yang sesuai dengan tanggal pendaftaran

### 3. Testing Workflow

1. **Test dengan data real** (bukan data dummy)
2. **Pastikan ada pendaftaran PCare** sebelum membuat kunjungan
3. **Gunakan endpoint `/kunjungan/v1`**

## IMPLEMENTASI LANGSUNG

### Script Test untuk Validasi

Gunakan script berikut untuk memvalidasi perubahan:

```bash
php test_kunjungan_v1_endpoint.php
```

### Expected Result Setelah Update

Setelah menggunakan endpoint `/kunjungan/v1`, error seharusnya berubah dari:
- ❌ 404 "Unauthorized! You are not registered for this service!"

Menjadi:
- ⚠️ 412 "PRECONDITION_FAILED" (yang menunjukkan endpoint dapat diakses)

## LANGKAH SELANJUTNYA

1. ✅ **Update endpoint ke `kunjungan/v1`** (dapat dilakukan segera)
2. ✅ **Test dengan data pendaftaran yang valid**
3. ⚠️ **Jika masih error 412**: Pastikan ada pendaftaran PCare yang valid
4. ⚠️ **Jika masih error 404**: Kontak BPJS untuk aktivasi layanan

## PRIORITAS TINDAKAN

| Prioritas | Tindakan | Estimasi Waktu | Dampak |
|-----------|----------|----------------|--------|
| 🔴 HIGH | Update endpoint ke `/kunjungan/v1` | 15 menit | Menyelesaikan masalah utama |
| 🟡 MEDIUM | Test dengan data real | 30 menit | Validasi solusi |
| 🟢 LOW | Dokumentasi perubahan | 15 menit | Maintenance |

---

**Catatan**: Perubahan endpoint ini kemungkinan besar akan menyelesaikan masalah "Unauthorized" karena menggunakan versi API yang sama dengan implementasi Java yang sudah terbukti berhasil.