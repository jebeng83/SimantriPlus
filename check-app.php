<?php
/**
 * Script Diagnostik Aplikasi Simantri PLUS
 * 
 * Script ini membantu mengidentifikasi masalah umum pada instalasi aplikasi
 * dan memberikan petunjuk perbaikan. Jalankan di browser atau command line.
 * 
 * PERHATIAN: Jangan menempatkan script ini di folder yang dapat diakses publik
 * pada lingkungan produksi. Hapus setelah digunakan.
 */

// Memastikan kesalahan PHP ditampilkan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$results = [];
$isCommandLine = php_sapi_name() === 'cli';
$basePath = __DIR__;

// Header
if (!$isCommandLine) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Diagnostik Simantri PLUS</title>
        <style>
            body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; max-width: 1000px; margin: 0 auto; padding: 20px; }
            h1 { color: #006bb4; border-bottom: 2px solid #eee; padding-bottom: 10px; }
            h2 { margin-top: 30px; color: #333; }
            .status { display: inline-block; margin-left: 10px; padding: 3px 8px; border-radius: 3px; font-size: 14px; font-weight: bold; }
            .success { background-color: #d4edda; color: #155724; }
            .warning { background-color: #fff3cd; color: #856404; }
            .error { background-color: #f8d7da; color: #721c24; }
            .info { background-color: #d1ecf1; color: #0c5460; }
            pre { background-color: #f5f7fb; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 15px 0; }
            code { font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f5f7fb; }
            tr:hover { background-color: #f9f9f9; }
            ul { margin-top: 5px; }
            .accordion { background-color: #f5f7fb; color: #333; cursor: pointer; padding: 18px; width: 100%; text-align: left; border: none; outline: none; transition: 0.4s; margin-bottom: 1px; font-weight: bold; position: relative; }
            .accordion:after { content: "\\002B"; color: #777; font-weight: bold; float: right; margin-left: 5px; }
            .active:after { content: "\\2212"; }
            .panel { padding: 0 18px; background-color: white; max-height: 0; overflow: hidden; transition: max-height 0.2s ease-out; border-left: 1px solid #ddd; border-right: 1px solid #ddd; }
            .solution { margin-top: 10px; padding: 10px; background-color: #e8f4fd; border-radius: 5px; }
            .solution h4 { margin-top: 0; color: #0d6efd; }
        </style>
        <script>
            function toggleAccordion(id) {
                var element = document.getElementById(id);
                element.classList.toggle("active");
                var panel = element.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            }
        </script>
    </head>
    <body>
        <h1>Diagnostik Aplikasi Simantri PLUS</h1>
        <p>Script ini memeriksa konfigurasi dasar aplikasi dan mengidentifikasi masalah potensial.</p>';
}

// Fungsi pembantu
function check($condition, $message, $type = 'success', $solution = '') {
    global $results;
    
    $results[] = [
        'condition' => $condition,
        'message' => $message,
        'type' => $condition ? 'success' : ($type === 'warning' ? 'warning' : 'error'),
        'solution' => $condition ? '' : $solution
    ];
    
    return $condition;
}

function display_results() {
    global $results, $isCommandLine;
    
    if ($isCommandLine) {
        foreach ($results as $index => $result) {
            $symbol = $result['type'] === 'success' ? '[✓]' : ($result['type'] === 'warning' ? '[!]' : '[✗]');
            echo "{$symbol} {$result['message']}\n";
            
            if (!empty($result['solution'])) {
                echo "  → Solusi: {$result['solution']}\n";
            }
            
            if ($index < count($results) - 1) {
                echo "\n";
            }
        }
    } else {
        echo '<h2>Hasil Pemeriksaan</h2>';
        
        foreach ($results as $index => $result) {
            $statusClass = $result['type'];
            $statusText = $result['type'] === 'success' ? 'OK' : ($result['type'] === 'warning' ? 'Peringatan' : 'Error');
            
            echo '<button class="accordion" onclick="toggleAccordion(\'accordion-' . $index . '\')" id="accordion-' . $index . '">' .
                 $result['message'] . '<span class="status ' . $statusClass . '">' . $statusText . '</span></button>';
            echo '<div class="panel">';
            echo '<p>' . ($result['condition'] ? 'Pemeriksaan berhasil.' : 'Pemeriksaan gagal.') . '</p>';
            
            if (!empty($result['solution'])) {
                echo '<div class="solution">';
                echo '<h4>Solusi:</h4>';
                echo '<p>' . $result['solution'] . '</p>';
                echo '</div>';
            }
            
            echo '</div>';
        }
    }
}

// --------------------------------------
// Mulai Pemeriksaan
// --------------------------------------

// 1. Memeriksa PHP Version
$phpVersion = phpversion();
$phpVersionOK = version_compare($phpVersion, '8.0.0', '>=');
check($phpVersionOK, 
      "Versi PHP: {$phpVersion}", 
      'error',
      "Perbarui PHP ke minimal versi 8.0.0. Versi saat ini: {$phpVersion}");

// 2. Memeriksa file .env
$envFileExists = file_exists("{$basePath}/.env");
check($envFileExists, 
      "File .env tersedia", 
      'error',
      "Buat file .env berdasarkan .env.example dan isi dengan konfigurasi yang sesuai.");

// 3. Memeriksa APP_KEY di .env
$appKeySet = false;
$appKeyValid = false;

if ($envFileExists) {
    $envContent = file_get_contents("{$basePath}/.env");
    preg_match('/APP_KEY=(.*)/', $envContent, $matches);
    
    if (isset($matches[1]) && !empty(trim($matches[1]))) {
        $appKeySet = true;
        // Memeriksa format APP_KEY
        $appKeyValid = strpos(trim($matches[1]), 'base64:') === 0 && strlen(trim($matches[1])) > 50;
    }
}

check($appKeySet && $appKeyValid, 
      "APP_KEY terkonfigurasi dengan benar", 
      'error',
      "Jalankan 'php artisan key:generate' untuk menghasilkan APP_KEY baru, atau gunakan: base64:HNU+Nb2vC44ablVRvqG6bls7tdpBmPYSOJLU+4rR4sE=");

// 4. Memeriksa direktori storage
$storageWritable = is_writable("{$basePath}/storage");
check($storageWritable, 
      "Direktori storage dapat ditulis", 
      'error',
      "Ubah izin direktori storage: chmod -R 775 storage");

// 5. Memeriksa composer.lock dan vendor
$composerInstalled = file_exists("{$basePath}/vendor/autoload.php");
check($composerInstalled, 
      "Dependensi composer terinstall", 
      'error',
      "Jalankan 'composer install' untuk menginstall dependensi.");

// 6. Memeriksa file bootstrap/cache
$cacheDirectoryWritable = is_writable("{$basePath}/bootstrap/cache");
check($cacheDirectoryWritable, 
      "Direktori bootstrap/cache dapat ditulis", 
      'error',
      "Ubah izin direktori bootstrap/cache: chmod -R 775 bootstrap/cache");

// 7. Memeriksa ekstensi PHP yang diperlukan
$requiredExtensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$missingExtensions = [];

foreach ($requiredExtensions as $extension) {
    if (!extension_loaded($extension)) {
        $missingExtensions[] = $extension;
    }
}

check(empty($missingExtensions), 
      "Ekstensi PHP yang diperlukan terinstall", 
      'error',
      "Instal ekstensi PHP yang hilang: " . implode(', ', $missingExtensions));

// 8. Memeriksa koneksi database jika .env ada
$dbConfigured = false;
$dbConnected = false;

if ($envFileExists) {
    // Ekstrak konfigurasi DB dari .env
    preg_match('/DB_CONNECTION=(.*)/', $envContent, $matchesConn);
    preg_match('/DB_HOST=(.*)/', $envContent, $matchesHost);
    preg_match('/DB_PORT=(.*)/', $envContent, $matchesPort);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $matchesDB);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $matchesUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $matchesPass);
    
    if (isset($matchesConn[1]) && isset($matchesHost[1]) && isset($matchesDB[1]) && isset($matchesUser[1])) {
        $dbConfigured = true;
        
        $dbConn = trim($matchesConn[1]);
        $dbHost = trim($matchesHost[1]);
        $dbPort = isset($matchesPort[1]) ? trim($matchesPort[1]) : '3306';
        $dbName = trim($matchesDB[1]);
        $dbUser = trim($matchesUser[1]);
        $dbPass = isset($matchesPass[1]) ? trim($matchesPass[1]) : '';
        
        // Coba koneksi ke database
        try {
            if ($dbConn === 'mysql') {
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
                $pdo = new PDO($dsn, $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbConnected = true;
            }
        } catch (PDOException $e) {
            $dbError = $e->getMessage();
        }
    }
}

check($dbConfigured, 
      "Database terkonfigurasi di .env", 
      'error',
      "Konfigurasi database di file .env (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)");

if ($dbConfigured) {
    check($dbConnected, 
          "Koneksi database berhasil", 
          'error',
          "Periksa konfigurasi database. Error: " . (isset($dbError) ? $dbError : "Tidak dapat terhubung"));
}

// 9. Memeriksa file kunci untuk enkripsi/dekripsi
if ($envFileExists && $appKeySet) {
    $encryptionTraitExists = file_exists("{$basePath}/app/Traits/EnkripsiData.php");
    check($encryptionTraitExists, 
          "File app/Traits/EnkripsiData.php ditemukan", 
          'error',
          "Periksa file app/Traits/EnkripsiData.php. File ini penting untuk enkripsi/dekripsi data.");
}

// 10. Memeriksa .htaccess file di direktori public
$htaccessExists = file_exists("{$basePath}/public/.htaccess");
check($htaccessExists, 
      "File .htaccess ditemukan di direktori public", 
      'error',
      "Buat file .htaccess di direktori public. Ini penting untuk URL rewriting pada Apache.");

// 11. Memeriksa web server rewrite module
$rewriteEnabled = true; // Asumsi default untuk positif
if (function_exists('apache_get_modules')) {
    $rewriteEnabled = in_array('mod_rewrite', apache_get_modules());
}
check($rewriteEnabled, 
      "Apache mod_rewrite diaktifkan", 
      'warning',
      "Aktifkan mod_rewrite di konfigurasi Apache Anda.");

// 12. Memeriksa timeout settings di PHP
$maxExecutionTime = ini_get('max_execution_time');
check((int)$maxExecutionTime >= 60 || $maxExecutionTime == 0, 
      "PHP max_execution_time cukup ({$maxExecutionTime}s)", 
      'warning',
      "Tingkatkan nilai max_execution_time di php.ini atau .htaccess menjadi minimal 60s.");

// 13. Memeriksa izin pada direktori penting
$directoriesToCheck = [
    "{$basePath}/storage/app" => 775,
    "{$basePath}/storage/framework" => 775,
    "{$basePath}/storage/logs" => 775,
    "{$basePath}/bootstrap/cache" => 775
];

$directoryPermissionsOK = true;
$badPermissions = [];

foreach ($directoriesToCheck as $dir => $perm) {
    if (file_exists($dir)) {
        $actualPerm = substr(sprintf('%o', fileperms($dir)), -3);
        // Perbandingan sangat sederhana
        if ($actualPerm < $perm) {
            $directoryPermissionsOK = false;
            $badPermissions[] = "{$dir} (Saat ini: {$actualPerm}, Diharapkan: {$perm})";
        }
    } else {
        $directoryPermissionsOK = false;
        $badPermissions[] = "{$dir} (Direktori tidak ditemukan)";
    }
}

check($directoryPermissionsOK, 
      "Izin direktori sudah benar", 
      'warning', 
      "Perbarui izin direktori berikut:\n- " . implode("\n- ", $badPermissions));

// 14. Memeriksa dukungan SSL/HTTPS
$sslSupported = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
check($sslSupported, 
      "HTTPS/SSL diaktifkan", 
      'warning',
      "Pertimbangkan untuk mengaktifkan HTTPS untuk keamanan data.");

// Tampilkan Hasil
display_results();

// Footer
if (!$isCommandLine) {
    echo '
        <h2>Langkah Selanjutnya</h2>
        <p>Jika semua pemeriksaan "OK", aplikasi Anda dikonfigurasi dengan benar. Jika ada kesalahan, ikuti petunjuk solusi yang disediakan.</p>
        <p>Jika masalah terus berlanjut, periksa file log di <code>storage/logs/laravel.log</code> untuk informasi lebih lanjut.</p>
        <p><strong>Peringatan:</strong> Hapus file ini setelah digunakan pada lingkungan produksi.</p>
    </body>
    </html>';
} 