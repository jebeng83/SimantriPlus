# Panduan Deployment Simantri PLUS

File ini berisi langkah-langkah untuk mem-deploy aplikasi Simantri PLUS ke server produksi. Ikuti langkah-langkah berikut dengan teliti untuk memastikan aplikasi berjalan dengan baik di lingkungan produksi.

## Prasyarat

Pastikan server memenuhi spesifikasi minimum berikut:

* PHP 8.0 atau lebih tinggi
* MySQL 5.7 atau lebih tinggi (atau MariaDB 10.4+)
* Ekstensi PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
* Composer 2.x
* Apache atau Nginx dengan mod_rewrite diaktifkan
* Git (opsional, untuk update)

## Langkah-langkah Deployment

### 1. Persiapan Server

Siapkan server dengan menginstal semua prasyarat di atas. Contoh pada server Ubuntu:

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Instal PHP dan ekstensi yang diperlukan
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-fileinfo

# Instal MySQL
sudo apt install mysql-server

# Instal Apache
sudo apt install apache2 libapache2-mod-php8.1

# Aktifkan mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Instal Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 2. Transfer Kode Aplikasi

Ada beberapa cara untuk mentransfer kode aplikasi ke server:

#### Opsi 1: Menggunakan Git

```bash
cd /var/www/html
git clone [URL_REPOSITORI] simantri-plus
cd simantri-plus
```

#### Opsi 2: Upload Manual (FTP/SFTP)

Upload semua file aplikasi ke direktori `/var/www/html/simantri-plus` atau lokasi sesuai konfigurasi web server.

### 3. Konfigurasi Aplikasi

1. **Buat file .env dari contoh**:

   ```bash
   cd /var/www/html/simantri-plus
   cp .env.example .env
   ```

2. **Edit file .env** dengan konfigurasi yang sesuai:

   ```bash
   nano .env
   ```

   Perhatikan terutama bagian berikut:
   
   ```
   APP_NAME="Simantri PLUS"
   APP_ENV=production
   APP_KEY=base64:HNU+Nb2vC44ablVRvqG6bls7tdpBmPYSOJLU+4rR4sE=
   APP_DEBUG=false
   APP_URL=https://[domain-anda]
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=username_db
   DB_PASSWORD=password_db
   ```

3. **Instal dependensi dengan Composer**:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. **Generate Application Key** (jika belum ada di file .env):

   ```bash
   php artisan key:generate
   ```

5. **Setup Database**:

   ```bash
   # Buat database
   mysql -u root -p
   CREATE DATABASE nama_database;
   GRANT ALL PRIVILEGES ON nama_database.* TO 'username_db'@'localhost' IDENTIFIED BY 'password_db';
   FLUSH PRIVILEGES;
   EXIT;
   
   # Migrasi database
   php artisan migrate
   
   # Jalankan seeder jika diperlukan
   php artisan db:seed
   ```

6. **Setel izin file**:

   ```bash
   sudo chown -R www-data:www-data /var/www/html/simantri-plus
   sudo chmod -R 755 /var/www/html/simantri-plus
   sudo chmod -R 775 /var/www/html/simantri-plus/storage /var/www/html/simantri-plus/bootstrap/cache
   ```

### 4. Konfigurasi Web Server

#### Apache

Buat virtual host baru:

```bash
sudo nano /etc/apache2/sites-available/simantri-plus.conf
```

Isi dengan konfigurasi berikut:

```apache
<VirtualHost *:80>
    ServerName domain-anda.com
    ServerAlias www.domain-anda.com
    DocumentRoot /var/www/html/simantri-plus/public
    
    <Directory /var/www/html/simantri-plus/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    
    # Pengaturan PHP untuk aplikasi ini
    <FilesMatch \.php$>
        SetHandler application/x-httpd-php
        php_value max_execution_time 300
        php_value post_max_size 20M
        php_value upload_max_filesize 20M
    </FilesMatch>
</VirtualHost>
```

Aktifkan site dan restart Apache:

```bash
sudo a2ensite simantri-plus.conf
sudo systemctl restart apache2
```

#### Nginx

Buat file konfigurasi:

```bash
sudo nano /etc/nginx/sites-available/simantri-plus
```

Isi dengan konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/html/simantri-plus/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "
            max_execution_time = 300
            memory_limit = 256M
            upload_max_filesize = 20M
            post_max_size = 20M
        ";
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site dan restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/simantri-plus /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

### 5. Optimasi Aplikasi

Optimalkan aplikasi untuk performa terbaik:

```bash
# Bersihkan dan cache konfigurasi
php artisan config:cache

# Cache rute
php artisan route:cache

# Optimasi autoload
composer dump-autoload -o

# Cache tampilan
php artisan view:cache
```

### 6. Setup SSL (HTTPS)

Sangat disarankan untuk mengamankan aplikasi dengan SSL:

```bash
# Untuk Apache dengan Let's Encrypt
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d domain-anda.com -d www.domain-anda.com

# Untuk Nginx dengan Let's Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

### 7. Penanganan Error 500

1. **File error 500 kustom**:

   Aplikasi ini dilengkapi dengan file `public/500.php` untuk menampilkan halaman error yang lebih user-friendly. Verifikasikan file itu ada dan dapat diakses.

2. **Aktivasi Log Error**:

   Pastikan log error diaktifkan untuk debugging:
   
   ```bash
   # Periksa log Laravel
   tail -f storage/logs/laravel.log
   
   # Periksa log Apache
   tail -f /var/log/apache2/error.log
   
   # Periksa log Nginx
   tail -f /var/log/nginx/error.log
   ```

## Diagnosa dan Pemeliharaan

### Script Diagnostik

Aplikasi menyertakan file `check-app.php` untuk memeriksa kesehatan aplikasi:

```bash
php check-app.php
```

### Script Restart Aplikasi

Gunakan `restart-app.sh` untuk melakukan restart aplikasi dan membersihkan cache:

```bash
# Tanpa restart web server
bash restart-app.sh

# Dengan restart web server (memerlukan sudo)
sudo bash restart-app.sh --restart-web

# Dengan generate APP_KEY baru (jika diperlukan)
bash restart-app.sh --generate-key
```

## Penanganan Masalah Umum

### Masalah Dekripsi Data

Jika terjadi error saat dekripsi data, kemungkinan ada masalah dengan APP_KEY:

1. Verifikasi bahwa APP_KEY sama antara sistem development dan produksi
2. Jika key berbeda, data yang sudah dienkripsi dengan key lama tidak dapat didekripsi
3. Dalam kasus data yang terenkripsi, jangan mengubah APP_KEY

### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/html/simantri-plus
sudo chmod -R 755 /var/www/html/simantri-plus
sudo chmod -R 775 /var/www/html/simantri-plus/storage /var/www/html/simantri-plus/bootstrap/cache
```

### Error 500

1. Periksa log aplikasi di `storage/logs/laravel.log`
2. Periksa log server web (Apache/Nginx)
3. Verifikasi APP_KEY benar dan konsisten
4. Pastikan semua direktori memiliki permissions yang benar

## Backup dan Pemeliharaan

### Backup Reguler

Lakukan backup reguler pada:

1. Database
2. File .env
3. Data yang di-upload (storage/app)

Contoh script backup sederhana:

```bash
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/path/to/backups/$TIMESTAMP"
APP_DIR="/var/www/html/simantri-plus"
DB_NAME="nama_database"
DB_USER="username_db"
DB_PASS="password_db"

# Buat direktori backup
mkdir -p "$BACKUP_DIR"

# Backup database
mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/database_backup.sql"

# Backup file .env
cp "$APP_DIR/.env" "$BACKUP_DIR/.env"

# Backup data upload (jika ada)
tar -czf "$BACKUP_DIR/storage_app.tar.gz" -C "$APP_DIR/storage/app" .

echo "Backup selesai: $BACKUP_DIR"
```

### Update Aplikasi

Untuk memperbarui aplikasi:

```bash
cd /var/www/html/simantri-plus

# Ambil perubahan terbaru (jika menggunakan git)
git pull

# Update dependensi
composer install --no-dev --optimize-autoloader

# Migrasi database jika ada perubahan
php artisan migrate

# Bersihkan dan cache ulang
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

## Kontak Support

Jika Anda mengalami masalah selama deployment, silakan hubungi tim teknis kami di [email-support@example.com]. 