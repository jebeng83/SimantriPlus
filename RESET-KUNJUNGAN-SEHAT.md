# Reset Kunjungan Sehat Otomatis

Fitur ini akan secara otomatis mereset kolom `kunjungan_sehat` di tabel `skrining_pkg` menjadi `0` setiap awal bulan (tanggal 1) pada jam 00:00:00.

## Cara Kerja

1. **Command Laravel**: `ResetKunjunganSehat` command akan mengupdate semua record di tabel `skrining_pkg` yang memiliki `kunjungan_sehat = '1'` menjadi `'0'`
2. **Scheduler**: Laravel Task Scheduler akan menjalankan command ini secara otomatis setiap tanggal 1 jam 00:00:00
3. **Logging**: Semua aktivitas akan dicatat di file log `storage/logs/reset-kunjungan-sehat.log`

## File yang Terlibat

- `app/Console/Commands/ResetKunjunganSehat.php` - Command untuk reset kunjungan sehat
- `app/Console/Kernel.php` - Konfigurasi scheduler
- `storage/logs/reset-kunjungan-sehat.log` - File log aktivitas

## Menjalankan Manual

Untuk menjalankan reset secara manual (testing atau keperluan khusus):

```bash
php artisan ckg:reset-kunjungan-sehat
```

## Melihat Jadwal

Untuk melihat semua jadwal yang terdaftar:

```bash
php artisan schedule:list
```

## Testing Scheduler

Untuk testing scheduler (menjalankan semua jadwal yang seharusnya berjalan saat ini):

```bash
php artisan schedule:run
```

## Setup Cron Job (Production)

Di server production, tambahkan cron job berikut:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Monitoring

- Cek file log: `storage/logs/reset-kunjungan-sehat.log`
- Cek Laravel log: `storage/logs/laravel.log`
- Monitor database untuk memastikan reset berjalan dengan benar

## Troubleshooting

1. **Command tidak terdaftar**: Jalankan `php artisan list` untuk memastikan command `ckg:reset-kunjungan-sehat` terdaftar
2. **Scheduler tidak berjalan**: Pastikan cron job sudah disetup di server
3. **Error database**: Cek koneksi database dan permission
4. **Log tidak muncul**: Pastikan direktori `storage/logs` writable

## Konfigurasi Jadwal

Jadwal saat ini: **Setiap tanggal 1 jam 00:00:00**

Untuk mengubah jadwal, edit file `app/Console/Kernel.php` pada method `schedule()`:

```php
// Contoh jadwal lain:
$schedule->command('ckg:reset-kunjungan-sehat')
    ->monthlyOn(1, '00:00')  // Tanggal 1 jam 00:00
    ->monthlyOn(15, '12:00') // Tanggal 15 jam 12:00
    ->weekly()               // Setiap minggu
    ->daily()                // Setiap hari
```