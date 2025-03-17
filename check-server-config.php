<?php
// File untuk memeriksa konfigurasi server yang diperlukan

// Header untuk menampilkan konten sebagai plaintext
header('Content-Type: text/plain');

echo "=== PENGECEKAN KONFIGURASI SERVER ===\n\n";

// Cek versi PHP
echo "VERSI PHP: " . phpversion() . "\n";
echo "Direkomendasikan: PHP 8.0 atau lebih tinggi\n\n";

// Cek ekstensi PHP yang diperlukan
$required_extensions = [
    'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'
];

echo "EKSTENSI PHP YANG DIPERLUKAN:\n";
foreach ($required_extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "OK" : "TIDAK TERSEDIA") . "\n";
}
echo "\n";

// Cek konfigurasi PHP
echo "KONFIGURASI PHP:\n";
echo "memory_limit: " . ini_get('memory_limit') . " (Direkomendasikan: 256M)\n";
echo "max_execution_time: " . ini_get('max_execution_time') . " (Direkomendasikan: 300)\n";
echo "post_max_size: " . ini_get('post_max_size') . " (Direkomendasikan: 64M)\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . " (Direkomendasikan: 64M)\n";
echo "\n";

// Cek mod_rewrite
echo "PENGECEKAN MOD_REWRITE:\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? "TERSEDIA" : "TIDAK TERSEDIA") . "\n";
} else {
    echo "Tidak dapat memeriksa modul Apache melalui PHP - pastikan mod_rewrite diaktifkan di server\n";
}
echo "\n";

// Cek izin direktori
echo "PENGECEKAN IZIN DIREKTORI:\n";
$directories = [
    'storage' => '775 atau 755',
    'storage/logs' => '775 atau 755',
    'storage/framework' => '775 atau 755',
    'storage/framework/sessions' => '775 atau 755',
    'storage/framework/views' => '775 atau 755',
    'storage/framework/cache' => '775 atau 755',
    'bootstrap/cache' => '775 atau 755',
];

foreach ($directories as $dir => $recommended) {
    if (file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? "YA" : "TIDAK";
        echo "$dir: IZIN=$perms, DAPAT DITULIS=$writable (Direkomendasikan: $recommended)\n";
    } else {
        echo "$dir: TIDAK DITEMUKAN\n";
    }
}
echo "\n";

// Cek .htaccess
echo "PENGECEKAN .HTACCESS:\n";
$htaccess_files = [
    '.htaccess',
    'public/.htaccess'
];

foreach ($htaccess_files as $file) {
    if (file_exists($file)) {
        echo "$file: DITEMUKAN\n";
    } else {
        echo "$file: TIDAK DITEMUKAN\n";
    }
}
echo "\n";

// Cek koneksi database
echo "PENGECEKAN KONEKSI DATABASE:\n";
try {
    // Coba baca konfigurasi dari .env
    $env_file = '.env';
    if (file_exists($env_file)) {
        $env_content = file_get_contents($env_file);
        preg_match('/DB_HOST=(.*)/', $env_content, $host_matches);
        preg_match('/DB_DATABASE=(.*)/', $env_content, $db_matches);
        preg_match('/DB_USERNAME=(.*)/', $env_content, $user_matches);
        preg_match('/DB_PASSWORD=(.*)/', $env_content, $pass_matches);
        
        $db_host = isset($host_matches[1]) ? trim($host_matches[1]) : '';
        $db_name = isset($db_matches[1]) ? trim($db_matches[1]) : '';
        $db_user = isset($user_matches[1]) ? trim($user_matches[1]) : '';
        $db_pass = isset($pass_matches[1]) ? trim($pass_matches[1]) : '';
        
        if ($db_host && $db_name) {
            try {
                $dsn = "mysql:host=$db_host;dbname=$db_name";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5,
                ];
                $pdo = new PDO($dsn, $db_user, $db_pass, $options);
                echo "Koneksi ke database berhasil!\n";
                
                $stmt = $pdo->query("SELECT VERSION()");
                $version = $stmt->fetchColumn();
                echo "Versi MySQL: $version\n";
            } catch (PDOException $e) {
                echo "Koneksi ke database gagal: " . $e->getMessage() . "\n";
            }
        } else {
            echo "Konfigurasi database tidak ditemukan di .env\n";
        }
    } else {
        echo "File .env tidak ditemukan\n";
    }
} catch (Exception $e) {
    echo "Error saat memeriksa database: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== PENGECEKAN SELESAI ===\n";
echo "Jika ada masalah, perbaiki terlebih dahulu lalu coba akses aplikasi lagi.\n"; 