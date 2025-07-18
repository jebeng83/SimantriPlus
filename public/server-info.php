<?php
// File untuk memeriksa kondisi server
// Akses melalui: http://kerjo.faskesku.com/server-info.php

// Cek keamanan - hanya akses dari IP yang diizinkan
$allowed_ips = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>403 Akses Ditolak</h1>";
    exit;
}

// Informasi dasar PHP
echo "<h1>Server Info</h1>";
echo "<h2>Versi PHP: " . phpversion() . "</h2>";
echo "<h2>Ekstensi PHP yang Terinstall:</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Cek konfigurasi PHP
echo "<h2>Konfigurasi PHP:</h2>";
echo "<ul>";
echo "<li>display_errors: " . ini_get('display_errors') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "</ul>";

// Cek folder permission
echo "<h2>Folder Permission:</h2>";
echo "<ul>";
$folders = [
    '../storage',
    '../storage/logs',
    '../storage/framework',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/framework/cache',
    '../bootstrap/cache',
];

foreach ($folders as $folder) {
    $is_writable = is_writable($folder) ? 'Ya' : 'Tidak';
    $is_readable = is_readable($folder) ? 'Ya' : 'Tidak';
    $permissions = substr(sprintf('%o', fileperms($folder)), -4);
    
    echo "<li>$folder - Writable: $is_writable, Readable: $is_readable, Permissions: $permissions</li>";
}
echo "</ul>";

// Cek koneksi database
echo "<h2>Koneksi Database:</h2>";
try {
    require_once '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    $db_host = $_ENV['DB_HOST'];
    $db_name = $_ENV['DB_DATABASE'];
    $db_user = $_ENV['DB_USERNAME'];
    $db_pass = $_ENV['DB_PASSWORD'];
    
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Koneksi database berhasil!</p>";
    
    // Cek versi MySQL
    $stmt = $conn->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "<p>Versi MySQL: $version</p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>Koneksi database gagal: " . $e->getMessage() . "</p>";
}

// Test akses ke file penting
echo "<h2>Akses File Kritis:</h2>";
$files = [
    '../.env',
    '../config/app.php',
    '../routes/web.php',
    '../public/index.php',
];

foreach ($files as $file) {
    $is_readable = is_readable($file) ? 'Ya' : 'Tidak';
    echo "<li>$file - Readable: $is_readable</li>";
}

// Cek Log Laravel Terakhir
echo "<h2>Log Laravel Terakhir:</h2>";
$log_file = '../storage/logs/laravel-' . date('Y-m-d') . '.log';
if (file_exists($log_file) && is_readable($log_file)) {
    $log_content = file_get_contents($log_file);
    $log_lines = explode("\n", $log_content);
    $last_lines = array_slice($log_lines, -20);
    echo "<pre>";
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line) . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>File log tidak ditemukan atau tidak dapat dibaca.</p>";
}

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
print_r($_ENV);
echo "</pre>"; 