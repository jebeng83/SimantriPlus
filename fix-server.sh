#!/bin/bash

# Script perbaikan server untuk masalah error 500
# Jalankan dengan: sudo bash fix-server.sh

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Banner
echo -e "${YELLOW}============================================${NC}"
echo -e "${YELLOW}     SCRIPT PERBAIKAN SERVER ERROR 500     ${NC}"
echo -e "${YELLOW}============================================${NC}"
echo

# Verifikasi root
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Error: Script ini harus dijalankan sebagai root (gunakan sudo)${NC}"
  exit 1
fi

# Fungsi untuk menampilkan status
function echo_status() {
    case $1 in
        "info")
            echo -e "[${YELLOW}INFO${NC}] $2"
            ;;
        "success")
            echo -e "[${GREEN}SUKSES${NC}] $2"
            ;;
        "error")
            echo -e "[${RED}ERROR${NC}] $2"
            ;;
        *)
            echo -e "$2"
            ;;
    esac
}

# Deteksi OS dan web server
OS=""
WEB_SERVER=""

if [ -f /etc/redhat-release ]; then
    OS="centos"
    if systemctl is-active --quiet httpd; then
        WEB_SERVER="httpd"
    fi
elif [ -f /etc/lsb-release ]; then
    OS="ubuntu"
    if systemctl is-active --quiet apache2; then
        WEB_SERVER="apache2"
    fi
elif [ -f /etc/debian_version ]; then
    OS="debian"
    if systemctl is-active --quiet apache2; then
        WEB_SERVER="apache2"
    fi
elif [ -d /Applications/XAMPP ]; then
    OS="macos"
    WEB_SERVER="xampp"
fi

if [ -z "$OS" ]; then
    OS="unknown"
    echo_status "warning" "Sistem operasi tidak terdeteksi, melanjutkan dalam mode generic"
else
    echo_status "info" "Sistem operasi terdeteksi: $OS"
fi

if [ -z "$WEB_SERVER" ]; then
    if systemctl is-active --quiet nginx; then
        WEB_SERVER="nginx"
    else
        WEB_SERVER="unknown"
        echo_status "warning" "Web server tidak terdeteksi, beberapa perbaikan mungkin tidak bekerja"
    fi
else
    echo_status "info" "Web server terdeteksi: $WEB_SERVER"
fi

# Determine PHP version and path
PHP_VERSION=$(php -r 'echo PHP_VERSION;' | cut -c1-3)
echo_status "info" "PHP version: $PHP_VERSION"

# Set path aplikasi - ubah jika berbeda
APP_PATH="$(pwd)"
cd "$APP_PATH" || {
    echo_status "error" "Tidak dapat mengakses direktori aplikasi: $APP_PATH"
    exit 1
}
echo_status "info" "Menggunakan direktori aplikasi: $APP_PATH"

# LANGKAH 1: Verifikasi dan perbaiki file .env
echo_status "info" "Memeriksa file .env..."
if [ ! -f "$APP_PATH/.env" ]; then
    if [ -f "$APP_PATH/.env.example" ]; then
        echo_status "warning" "File .env tidak ditemukan, menyalin dari .env.example"
        cp "$APP_PATH/.env.example" "$APP_PATH/.env"
        echo_status "success" "File .env dibuat dari template .env.example"
    else
        echo_status "error" "File .env dan .env.example tidak ditemukan!"
        exit 1
    fi
else
    echo_status "success" "File .env ditemukan"
fi

# Periksa APP_KEY di .env
APP_KEY=$(grep "^APP_KEY=" "$APP_PATH/.env" | cut -d= -f2-)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" == "base64:" ]; then
    echo_status "warning" "APP_KEY tidak valid di .env"
    
    # Buat backup .env sebelum mengubah
    cp "$APP_PATH/.env" "$APP_PATH/.env.backup-$(date +%Y%m%d%H%M%S)"
    echo_status "info" "Backup .env dibuat"
    
    # Coba generate key baru dengan artisan
    if php "$APP_PATH/artisan" key:generate; then
        echo_status "success" "APP_KEY baru dihasilkan dengan artisan"
    else
        # Jika artisan gagal, set key secara manual
        echo_status "warning" "Gagal menghasilkan key dengan artisan, menetapkan key default"
        sed -i'' -e "s/^APP_KEY=.*/APP_KEY=base64:HNU+Nb2vC44ablVRvqG6bls7tdpBmPYSOJLU+4rR4sE=/" "$APP_PATH/.env"
        echo_status "success" "APP_KEY default ditetapkan di .env"
    fi
else
    echo_status "success" "APP_KEY sudah ditetapkan: $APP_KEY"
fi

# Set APP_DEBUG untuk debugging
echo_status "info" "Mengaktifkan mode debug sementara untuk diagnosa masalah..."
sed -i'' -e "s/^APP_DEBUG=.*/APP_DEBUG=true/" "$APP_PATH/.env"
echo_status "success" "APP_DEBUG diaktifkan di .env"

# LANGKAH 2: Perbaiki permissions
echo_status "info" "Memperbaiki permissions file dan direktori..."

# Mendapatkan web server user
WEB_USER="www-data"  # Default untuk Debian/Ubuntu
if [ "$OS" == "centos" ]; then
    WEB_USER="apache"
elif [ "$OS" == "macos" ]; then
    WEB_USER="_www"  # MacOS default
fi

# Fix permissions recursively
find "$APP_PATH" -type f -exec chmod 644 {} \;
find "$APP_PATH" -type d -exec chmod 755 {} \;

# Fix specific directory permissions
chmod -R 775 "$APP_PATH/storage"
chmod -R 775 "$APP_PATH/bootstrap/cache"

# Jika root, set ownership
if [ "$EUID" -eq 0 ]; then
    chown -R "$WEB_USER":"$WEB_USER" "$APP_PATH/storage"
    chown -R "$WEB_USER":"$WEB_USER" "$APP_PATH/bootstrap/cache"
    echo_status "success" "Ownership direktori diperbarui ke $WEB_USER"
fi

echo_status "success" "Permissions direktori diperbarui"

# LANGKAH 3: Rewrite .htaccess
echo_status "info" "Memperbarui file .htaccess di root..."
cat > "$APP_PATH/.htaccess" << 'EOL'
# Redirect HTTP to HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Explicitly handle favicon.ico
    RewriteRule ^favicon\.ico$ public/favicon.ico [L]
    
    # Redirect all requests to public folder
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# PHP settings
<IfModule mod_php8.c>
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
    php_flag display_errors Off
    php_flag log_errors On
    php_value error_log storage/logs/php_error.log
</IfModule>

# Custom error pages dengan path yang benar
ErrorDocument 500 /500.php
ErrorDocument 404 /404.html
ErrorDocument 403 /403.html

# Set timeout values
<IfModule mod_reqtimeout.c>
    RequestReadTimeout header=60 body=300
</IfModule>

# Enable keep-alive connections
<IfModule mod_headers.c>
    Header set Connection keep-alive
</IfModule>

# Disable directory browsing
Options -Indexes

# Set default character set
AddDefaultCharset UTF-8

# Disable server signature
ServerSignature Off
EOL
echo_status "success" "File .htaccess root diperbarui"

# Perbarui .htaccess di public
echo_status "info" "Memperbarui file .htaccess di public..."
cat > "$APP_PATH/public/.htaccess" << 'EOL'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
    
    # Directly serve error pages without going through PHP
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^(500\.php|404\.html|403\.html|error\.html)$ - [L]
</IfModule>

# PHP settings for this directory
<IfModule mod_php8.c>
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
    php_flag display_errors Off
    php_flag log_errors On
</IfModule>

# Custom error documents
ErrorDocument 500 /500.php
ErrorDocument 404 /404.html
ErrorDocument 403 /403.html

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript application/json
</IfModule>

# Set browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresDefault "access plus 2 days"
</IfModule>

# Prevent direct access to sensitive file types
<FilesMatch "^\.">
    Order deny,allow
    Deny from all
</FilesMatch>

<FilesMatch "\.(env|log|yml|yaml|json|lock|md|sql|sh)$">
    Order deny,allow
    Deny from all
</FilesMatch>
EOL
echo_status "success" "File .htaccess public diperbarui"

# LANGKAH 4: Clear cache aplikasi
echo_status "info" "Membersihkan cache aplikasi..."
php "$APP_PATH/artisan" cache:clear
php "$APP_PATH/artisan" config:clear
php "$APP_PATH/artisan" route:clear
php "$APP_PATH/artisan" view:clear
echo_status "success" "Cache aplikasi dibersihkan"

# LANGKAH 5: Dump autoload composer
echo_status "info" "Mengoptimalkan autoload..."
if command -v composer &> /dev/null; then
    composer dump-autoload -o
    echo_status "success" "Autoload dioptimalkan"
else
    echo_status "warning" "Composer tidak ditemukan, lewati optimasi autoload"
fi

# LANGKAH 6: Restart web server
echo_status "info" "Mencoba restart web server..."
if [ "$WEB_SERVER" == "apache2" ]; then
    systemctl restart apache2
    echo_status "success" "Apache2 di-restart"
elif [ "$WEB_SERVER" == "httpd" ]; then
    systemctl restart httpd
    echo_status "success" "Apache (httpd) di-restart"
elif [ "$WEB_SERVER" == "nginx" ]; then
    systemctl restart nginx
    systemctl restart php$PHP_VERSION-fpm
    echo_status "success" "Nginx dan PHP-FPM di-restart"
elif [ "$WEB_SERVER" == "xampp" ]; then
    /Applications/XAMPP/xamppfiles/xampp restart
    echo_status "success" "XAMPP di-restart"
else
    echo_status "warning" "Web server tidak dikenali, restart manual diperlukan"
fi

# LANGKAH 7: Pesan tentang langkah selanjutnya
echo
echo -e "${GREEN}=== PERBAIKAN SERVER SELESAI ===${NC}"
echo
echo -e "${YELLOW}Langkah-langkah selanjutnya:${NC}"
echo -e "1. Coba akses aplikasi Anda untuk melihat apakah error 500 sudah teratasi"
echo -e "2. Periksa file log berikut untuk informasi error lebih lanjut:"
echo -e "   - ${YELLOW}$APP_PATH/storage/logs/laravel.log${NC}"
echo -e "   - ${YELLOW}$APP_PATH/storage/logs/php_error.log${NC}"
echo -e "3. Jika masih ada masalah, jalankan script diagnostik:"
echo -e "   ${YELLOW}php $APP_PATH/check-app.php${NC}"
echo -e "4. Untuk debug masalah enkripsi, jalankan:"
echo -e "   ${YELLOW}php $APP_PATH/check-decrypt.php${NC}"
echo -e "5. CATATAN: JANGAN LUPA mengembalikan APP_DEBUG=false di .env"
echo -e "   setelah masalah terselesaikan untuk keamanan"
echo

# Selesai
echo -e "${GREEN}Script perbaikan selesai.${NC}" 