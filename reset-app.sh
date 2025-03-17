#!/bin/bash

# Script reset aplikasi untuk mengatasi error 500
# Jalankan dengan: sudo bash reset-app.sh

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Banner
echo -e "${YELLOW}============================================${NC}"
echo -e "${YELLOW}     SCRIPT RESET APLIKASI LARAVEL 500     ${NC}"
echo -e "${YELLOW}============================================${NC}"
echo

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

# Pastikan script dijalankan dari direktori aplikasi
if [ ! -f "artisan" ]; then
    echo_status "error" "Script harus dijalankan dari direktori aplikasi Laravel (yang memiliki file artisan)"
    exit 1
fi

# 1. Hapus file konfigurasi cache
echo_status "info" "Menghapus file konfigurasi cache..."
rm -f bootstrap/cache/*.php
echo_status "success" "File konfigurasi cache dihapus"

# 2. Hapus direktori storage/framework/sessions, storage/framework/views, dan storage/framework/cache
echo_status "info" "Menghapus session dan cache..."
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf storage/framework/cache/*
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
touch storage/logs/laravel.log
echo_status "success" "Session dan cache dihapus"

# 3. Perbaiki permission
echo_status "info" "Memperbaiki permission direktori..."
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R _www:_www storage
chown -R _www:_www bootstrap/cache
echo_status "success" "Permission direktori telah diperbaiki"

# 4. Perbaiki file .htaccess
echo_status "info" "Memperbaiki file .htaccess..."
cat > .htaccess << 'EOL'
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
    php_flag display_errors On
    php_flag log_errors On
    php_value error_log storage/logs/php_error.log
</IfModule>

# Custom error pages
ErrorDocument 500 /500.php
ErrorDocument 404 /404.html
ErrorDocument 403 /403.html

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
echo_status "success" "File .htaccess telah diperbarui"

# 5. Perbaiki file .htaccess di public
echo_status "info" "Memperbaiki file .htaccess di public..."
cat > public/.htaccess << 'EOL'
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
</IfModule>

# PHP settings for this directory
<IfModule mod_php8.c>
    php_value upload_max_filesize 64M
    php_value post_max_size 64M
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 256M
    php_flag display_errors On
    php_flag log_errors On
</IfModule>

# Custom error documents
ErrorDocument 500 /500.php
ErrorDocument 404 /404.html
ErrorDocument 403 /403.html
EOL
echo_status "success" "File .htaccess di public telah diperbarui"

# 6. Modifikasi index.php untuk menampilkan error
echo_status "info" "Memodifikasi index.php untuk menampilkan error..."
sed -i'' -e '1a\
// Aktifkan display errors untuk debugging\
ini_set("display_errors", 1);\
ini_set("display_startup_errors", 1);\
error_reporting(E_ALL);
' public/index.php
echo_status "success" "File index.php telah dimodifikasi"

# 7. Jalankan artisan command untuk membersihkan cache
echo_status "info" "Membersihkan cache aplikasi Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo_status "success" "Cache aplikasi berhasil dibersihkan"

# 8. Membuat file diagnostik
echo_status "info" "Membuat file diagnostik..."
cat > public/diagnostik.php << 'EOL'
<?php
// File diagnostik sederhana

// Tampilkan semua error PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostik Laravel</h1>";

// Cek PHP version
echo "<h2>PHP Version</h2>";
echo "<p>Current PHP version: " . phpversion() . "</p>";

// Cek ekstensi PHP
echo "<h2>PHP Extensions</h2>";
$requiredExtensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'curl'];
echo "<ul>";
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<li>{$ext}: <span style='color:green'>Loaded</span></li>";
    } else {
        echo "<li>{$ext}: <span style='color:red'>Not Loaded</span></li>";
    }
}
echo "</ul>";

// Cek permissions direktori
echo "<h2>Directory Permissions</h2>";
$directories = [
    '../storage' => 'Storage',
    '../storage/app' => 'Storage/App',
    '../storage/framework' => 'Storage/Framework',
    '../storage/logs' => 'Storage/Logs',
    '../bootstrap/cache' => 'Bootstrap/Cache'
];

echo "<ul>";
foreach ($directories as $dir => $name) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            echo "<li>{$name}: <span style='color:green'>Writable</span></li>";
        } else {
            echo "<li>{$name}: <span style='color:red'>Not Writable</span></li>";
        }
    } else {
        echo "<li>{$name}: <span style='color:red'>Directory Not Found</span></li>";
    }
}
echo "</ul>";

// Cek environment file
echo "<h2>Environment File</h2>";
if (file_exists('../.env')) {
    echo "<p><span style='color:green'>.env file exists</span></p>";
    
    // Cek APP_KEY
    $env = file_get_contents('../.env');
    if (preg_match('/APP_KEY=(.*)/', $env, $matches)) {
        $appKey = trim($matches[1]);
        if (empty($appKey) || $appKey === 'base64:') {
            echo "<p>APP_KEY: <span style='color:red'>Not Set or Invalid</span></p>";
        } else {
            echo "<p>APP_KEY: <span style='color:green'>Set</span> ($appKey)</p>";
        }
    } else {
        echo "<p>APP_KEY: <span style='color:red'>Not Found</span></p>";
    }
    
    // Cek APP_DEBUG
    if (preg_match('/APP_DEBUG=(.*)/', $env, $matches)) {
        $appDebug = trim($matches[1]);
        echo "<p>APP_DEBUG: $appDebug</p>";
    }
} else {
    echo "<p><span style='color:red'>.env file does not exist</span></p>";
}

// Cek connection to database
echo "<h2>Database Connection</h2>";
try {
    // Parse database config from .env
    $env = file_exists('../.env') ? file_get_contents('../.env') : '';
    
    preg_match('/DB_CONNECTION=(.*)/', $env, $matchConn);
    preg_match('/DB_HOST=(.*)/', $env, $matchHost);
    preg_match('/DB_PORT=(.*)/', $env, $matchPort);
    preg_match('/DB_DATABASE=(.*)/', $env, $matchDB);
    preg_match('/DB_USERNAME=(.*)/', $env, $matchUser);
    preg_match('/DB_PASSWORD=(.*)/', $env, $matchPass);
    
    $connection = isset($matchConn[1]) ? trim($matchConn[1]) : 'mysql';
    $host = isset($matchHost[1]) ? trim($matchHost[1]) : 'localhost';
    $port = isset($matchPort[1]) ? trim($matchPort[1]) : '3306';
    $database = isset($matchDB[1]) ? trim($matchDB[1]) : '';
    $username = isset($matchUser[1]) ? trim($matchUser[1]) : '';
    $password = isset($matchPass[1]) ? trim($matchPass[1]) : '';
    
    if (empty($database)) {
        throw new Exception("Database name not found in .env");
    }
    
    $dsn = "{$connection}:host={$host};port={$port};dbname={$database}";
    $dbh = new PDO($dsn, $username, $password);
    echo "<p><span style='color:green'>Successfully connected to the database</span></p>";
    echo "<p>Connection: {$connection}, Host: {$host}, Database: {$database}</p>";
} catch (Exception $e) {
    echo "<p><span style='color:red'>Database connection failed: " . $e->getMessage() . "</span></p>";
}

// Server Information
echo "<h2>Server Information</h2>";
echo "<ul>";
echo "<li>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</li>";
echo "</ul>";

// PHP Info in collapsed section
echo "<details>";
echo "<summary>PHP Info (click to expand)</summary>";
ob_start();
phpinfo();
$phpinfo = ob_get_clean();

// Make phpinfo output more readable inside the page
$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
$phpinfo = str_replace('<table', '<table class="phpinfo"', $phpinfo);
echo $phpinfo;
echo "</details>";
EOL
echo_status "success" "File diagnostik dibuat di public/diagnostik.php"

# 9. Restart XAMPP jika tersedia
if [ -f "/Applications/XAMPP/xamppfiles/xampp" ]; then
    echo_status "info" "Me-restart XAMPP..."
    /Applications/XAMPP/xamppfiles/xampp restart
    echo_status "success" "XAMPP berhasil di-restart"
else
    echo_status "warning" "Tidak dapat menemukan XAMPP executable, silakan restart server web Anda secara manual"
fi

echo
echo_status "success" "Reset aplikasi selesai!"
echo_status "info" "Silakan akses aplikasi Anda melalui browser"
echo_status "info" "Jika masih mengalami error, cek file diagnostik di: http://localhost/edokter/diagnostik.php"
echo 