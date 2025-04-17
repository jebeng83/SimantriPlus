# SimantriPlus

Sistem Informasi untuk Puskesmas dan klinik berbasis web untuk ILP Puskesmas dan Layanan Klinik yang Gratis

# E-Dokter

## Perubahan Terbaru
- Fix kapitalisasi namespace di routes/api.php untuk controller API yang menggunakan kapitalisasi yang berbeda antara sistem operasi macOS (case-insensitive) dan Linux (case-sensitive) di server produksi.

  ```php
  // Perubahan dari:
  Route::get('/ranap/{bangsal}/obat', [App\Http\Controllers\Api\ResepRanapController::class, 'getObatRanap']);
  
  // Menjadi:
  Route::get('/ranap/{bangsal}/obat', [App\Http\Controllers\API\ResepRanapController::class, 'getObatRanap']);
  ```

## Catatan Penting Tentang Case-Sensitivity
- macOS memiliki sistem file yang **case-insensitive**, sehingga `app/Http/Controllers/Api` dan `app/Http/Controllers/API` dianggap direktori yang sama.
- Linux (server produksi) memiliki sistem file yang **case-sensitive**, sehingga `app/Http/Controllers/Api` dan `app/Http/Controllers/API` dianggap sebagai direktori yang berbeda.
- Pastikan selalu menggunakan kapitalisasi yang konsisten ketika mendefinisikan namespace dan jalur direktori.

## Setelah Perubahan Kode
Setelah melakukan perubahan kode, jalankan perintah berikut pada server produksi:
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

