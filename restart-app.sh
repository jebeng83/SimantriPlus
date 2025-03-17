#!/bin/bash

# Script untuk memulai ulang dan memperbaiki aplikasi Laravel
echo "Memulai proses restart aplikasi Laravel..."

# Deteksi OS dan web server
if [ -d /Applications/XAMPP ]; then
    # macOS dengan XAMPP
    echo "Terdeteksi macOS dengan XAMPP"
    WEB_USER="_www"
    
    # Restart MySQL jika perlu
    if ! /Applications/XAMPP/xamppfiles/xampp status | grep -q "MySQL is running"; then
        echo "MySQL tidak berjalan, memulai MySQL..."
        sudo /Applications/XAMPP/xamppfiles/xampp startmysql
    else
        echo "MySQL sudah berjalan"
    fi
    
    # Restart Apache jika perlu
    if ! /Applications/XAMPP/xamppfiles/xampp status | grep -q "Apache is running"; then
        echo "Apache tidak berjalan, memulai Apache..."
        sudo /Applications/XAMPP/xamppfiles/xampp startapache
    else
        echo "Apache sudah berjalan"
    fi
elif [ -f /etc/debian_version ]; then
    # Debian/Ubuntu
    echo "Terdeteksi Debian/Ubuntu"
    WEB_USER="www-data"
    
    # Restart MySQL
    echo "Memulai ulang MySQL..."
    sudo systemctl restart mysql
    
    # Restart Apache
    echo "Memulai ulang Apache..."
    sudo systemctl restart apache2
elif [ -f /etc/redhat-release ]; then
    # CentOS/RHEL
    echo "Terdeteksi CentOS/RHEL"
    WEB_USER="apache"
    
    # Restart MySQL/MariaDB
    echo "Memulai ulang MariaDB/MySQL..."
    sudo systemctl restart mariadb
    
    # Restart Apache
    echo "Memulai ulang Apache..."
    sudo systemctl restart httpd
else
    echo "Sistem operasi tidak terdeteksi, menggunakan nilai default"
    WEB_USER="www-data"
fi

# Perbaiki izin file
echo "Memperbaiki izin file..."
if [ "$EUID" -eq 0 ]; then
    chmod -R 755 .
    chmod -R 777 storage
    chmod -R 777 bootstrap/cache
    chown -R $WEB_USER:$WEB_USER storage bootstrap/cache
    echo "Izin file diperbaiki"
else
    echo "PERINGATAN: Script tidak dijalankan sebagai root (sudo), beberapa operasi izin mungkin gagal"
    chmod -R 755 .
    chmod -R 777 storage
    chmod -R 777 bootstrap/cache
fi

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear

# Periksa koneksi database
echo "Memeriksa koneksi database..."
if php -r "try { new PDO('mysql:host=127.0.0.1;dbname=kerjo', 'root', ''); echo 'OK'; } catch(PDOException \$e) { echo \$e->getMessage(); exit(1); }"; then
    echo "Koneksi database berhasil"
else
    echo "PERINGATAN: Koneksi database gagal, periksa konfigurasi database di .env"
fi

# Jalankan migrasi jika diperlukan (uncomment jika diperlukan)
# echo "Menjalankan migrasi database..."
# php artisan migrate --force

echo "Aplikasi telah dimulai ulang dan diperbaiki"
echo "Silakan akses aplikasi melalui browser" 