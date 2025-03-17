#!/bin/bash

# Script untuk memperbaiki izin file agar aplikasi dapat diakses dari server
echo "Memperbaiki izin file untuk akses server universal..."

# Tentukan web server user berdasarkan sistem operasi
if [ -f /etc/debian_version ]; then
    WEB_USER="www-data"
    WEB_GROUP="www-data"
    echo "Terdeteksi sistem Debian/Ubuntu"
elif [ -f /etc/redhat-release ]; then
    WEB_USER="apache"
    WEB_GROUP="apache"
    echo "Terdeteksi sistem CentOS/RHEL"
elif [ -d /Applications/XAMPP ]; then
    WEB_USER="_www"
    WEB_GROUP="_www"
    echo "Terdeteksi macOS dengan XAMPP"
elif [ -d /usr/local/cpanel ]; then
    WEB_USER="nobody"
    WEB_GROUP="nobody"
    echo "Terdeteksi server cPanel"
else
    # Default untuk shared hosting atau server tidak dikenal
    WEB_USER="nobody"
    WEB_GROUP="nobody"
    echo "Sistem tidak dikenal, menggunakan pengguna web server default: nobody"
fi

echo "Menggunakan pengguna web server: $WEB_USER:$WEB_GROUP"

# Langkah 1: Atur izin dasar untuk semua file dan direktori
echo "Langkah 1: Mengatur izin dasar untuk semua file dan direktori..."
find . -type f -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./storage/*" -exec chmod 644 {} \;
find . -type d -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./storage/*" -exec chmod 755 {} \;

# Langkah 2: Atur izin untuk direktori penting yang memerlukan akses tulis
echo "Langkah 2: Mengatur izin untuk direktori yang memerlukan akses tulis..."

# Buat direktori yang diperlukan jika belum ada
echo "Memastikan semua direktori yang diperlukan ada..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/storage

# Atur izin yang lebih permisif untuk direktori kritis
echo "Mengatur izin untuk direktori kritis..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod -R 777 public/storage

# Langkah 3: Atur kepemilikan file jika dijalankan sebagai root
if [ "$EUID" -eq 0 ]; then
    echo "Langkah 3: Mengubah kepemilikan direktori ke $WEB_USER:$WEB_GROUP..."
    chown -R $WEB_USER:$WEB_GROUP .
    echo "Memberikan izin khusus untuk pengguna saat ini agar dapat melakukan edit..."
    current_user=$(logname 2>/dev/null || echo $SUDO_USER)
    if [ -n "$current_user" ]; then
        echo "Memberikan akses tambahan untuk pengguna: $current_user"
        setfacl -R -m u:$current_user:rwx storage bootstrap/cache public/storage 2>/dev/null || echo "setfacl tidak tersedia, melanjutkan tanpa ACL"
    fi
    echo "Kepemilikan direktori diubah ke $WEB_USER:$WEB_GROUP"
else
    echo "PERINGATAN: Script tidak dijalankan sebagai root, tidak dapat mengubah kepemilikan direktori"
    echo "Disarankan untuk menjalankan script ini dengan sudo untuk hasil terbaik"
fi

# Langkah 4: Izin tambahan untuk direktori spesifik yang sering menyebabkan masalah
echo "Langkah 4: Memberikan izin tambahan untuk direktori spesifik..."
chmod -R 777 storage/framework/views
chmod -R 777 storage/logs
chmod -R 777 storage/framework/sessions
chmod -R 777 storage/framework/cache
chmod -R 777 bootstrap/cache

# Langkah 5: Membuat symlink storage jika belum ada
echo "Langkah 5: Memeriksa symlink storage..."
if [ ! -L public/storage ] && [ -d storage/app/public ]; then
    echo "Membuat symlink storage..."
    ln -sf ../storage/app/public public/storage
fi

# Langkah 6: Verifikasi izin file .env dan artisan
echo "Langkah 6: Memverifikasi izin file kunci..."
if [ -f .env ]; then
    chmod 644 .env
    echo "Izin .env disetel ke 644"
else
    echo "PERINGATAN: File .env tidak ditemukan!"
fi

if [ -f artisan ]; then
    chmod 755 artisan
    echo "Izin artisan disetel ke 755"
fi

# Langkah 7: Cek konfigurasi .env
if [ -f .env ]; then
    if grep -q "APP_DEBUG=true" .env; then
        echo "PERINGATAN: APP_DEBUG masih diaktifkan di .env, ini tidak disarankan untuk produksi"
        echo "Ubah APP_DEBUG=true menjadi APP_DEBUG=false di file .env"
    fi
    
    if grep -q "APP_ENV=local" .env; then
        echo "PERINGATAN: APP_ENV masih diatur ke 'local', disarankan menggunakan 'production' di server"
    fi
fi

echo "Izin file diperbaiki untuk akses universal"
echo "Pastikan web server memiliki akses ke direktori aplikasi"
echo "Selesai" 