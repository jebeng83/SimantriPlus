# Panduan Mengatasi Masalah Akses Server

Dokumen ini berisi langkah-langkah untuk mengatasi masalah umum terkait akses aplikasi dari server.

## Masalah Umum dan Solusinya

### 1. Halaman Error 500 (Internal Server Error)

Error 500 biasanya menunjukkan ada masalah dengan konfigurasi server atau izin file.

**Solusi:**

1. Jalankan script `fix-permissions.sh` untuk memperbaiki izin file:
   ```bash
   sudo bash fix-permissions.sh
   ```

2. Periksa log error Apache untuk mengetahui masalah spesifik:
   ```bash
   sudo tail -f /var/log/apache2/error.log    # Untuk Debian/Ubuntu
   sudo tail -f /var/log/httpd/error_log      # Untuk CentOS/RHEL
   ```

3. Periksa log Laravel untuk error aplikasi:
   ```bash
   tail -f storage/logs/laravel-*.log
   ```

### 2. Halaman Error 404 (Not Found)

Error 404 sering disebabkan oleh masalah rewrite URL atau konfigurasi virtual host.

**Solusi:**

1. Pastikan mod_rewrite diaktifkan:
   ```bash
   # Untuk Debian/Ubuntu
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   
   # Untuk CentOS/RHEL
   sudo sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /etc/httpd/conf/httpd.conf
   sudo systemctl restart httpd
   ```

2. Periksa file `.htaccess` di direktori root dan `public/` memiliki konfigurasi yang benar.

3. Periksa konfigurasi virtualhost Apache. Pastikan `AllowOverride All` diatur di direktori aplikasi.

### 3. Masalah Izin File

Masalah izin file sering menyebabkan aplikasi tidak dapat membaca atau menulis file yang diperlukan.

**Solusi:**

1. Jalankan script `fix-permissions.sh` untuk memperbaiki izin file:
   ```bash
   sudo bash fix-permissions.sh
   ```

2. Jika tetap mengalami masalah, atur izin secara manual:
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

### 4. Masalah URL

Masalah URL dapat menyebabkan pengalihan yang salah atau asset (CSS/JS) tidak dimuat.

**Solusi:**

1. Periksa konfigurasi URL di file `.env`:
   ```
   APP_URL=http://domain-anda.com
   ```

2. Akses `check-url.php` untuk memeriksa konfigurasi URL saat ini.

### 5. Masalah Session atau Cache

Masalah session atau cache dapat menyebabkan error atau perilaku tidak konsisten.

**Solusi:**

1. Bersihkan cache aplikasi:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   php artisan route:clear
   ```

2. Periksa izin direktori session:
   ```bash
   sudo chmod -R 775 storage/framework/sessions
   sudo chown -R www-data:www-data storage/framework/sessions
   ```

### 6. Masalah Database

Koneksi database yang gagal dapat menyebabkan error.

**Solusi:**

1. Periksa konfigurasi database di file `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username_db
   DB_PASSWORD=password_db
   ```

2. Pastikan database ada dan user memiliki akses:
   ```bash
   mysql -u root -p
   SHOW DATABASES;
   GRANT ALL PRIVILEGES ON nama_database.* TO 'username_db'@'localhost';
   FLUSH PRIVILEGES;
   ```

### 7. Mod PHP atau Ekstensi Tidak Tersedia

Kekurangan modul PHP yang diperlukan dapat menyebabkan error.

**Solusi:**

1. Periksa ekstensi yang dibutuhkan:
   ```bash
   # Untuk Debian/Ubuntu
   sudo apt install php8.1-mbstring php8.1-xml php8.1-zip php8.1-mysql php8.1-curl
   
   # Untuk CentOS/RHEL
   sudo yum install php-mbstring php-xml php-zip php-mysql php-curl
   ```

2. Restart web server:
   ```bash
   sudo systemctl restart apache2    # Untuk Debian/Ubuntu
   sudo systemctl restart httpd      # Untuk CentOS/RHEL
   ```

## Tools Diagnosa

Gunakan tools berikut untuk mendiagnosa masalah:

1. `check-server-config.php` - Memeriksa konfigurasi server
2. `check-url.php` - Memeriksa konfigurasi URL
3. `public/server-info.php` - Menampilkan informasi server (hanya dapat diakses dari localhost)

## Panduan Khusus untuk XAMPP

Jika menggunakan XAMPP:

1. Pastikan modul Apache dan MySQL berjalan.
2. Periksa file `httpd-vhosts.conf` di direktori `xampp/apache/conf/extra/`.
3. Pastikan Document Root menunjuk ke direktori `public` aplikasi:

```apache
<VirtualHost *:80>
    ServerName edokter.local
    DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/edokter/public"
    <Directory "/Applications/XAMPP/xamppfiles/htdocs/edokter/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

4. Tambahkan entri di file hosts (`/etc/hosts` di Linux/Mac, `C:\Windows\System32\drivers\etc\hosts` di Windows):
```
127.0.0.1 edokter.local
```

## Panduan Khusus untuk Shared Hosting

Untuk shared hosting:

1. Pastikan PHP ≥ 8.0 tersedia di hosting.
2. Upload semua file, pastikan dokumen root diarahkan ke folder `public`.
3. Buat `.htaccess` di root domain yang mengarahkan ke subdirektori `public`:
```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
``` 