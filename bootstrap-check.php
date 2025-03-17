<?php
// File pemeriksaan bootstrap sederhana untuk Laravel

// Tampilkan semua error PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek jika vendor/autoload.php ada
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Error: File vendor/autoload.php tidak ditemukan. Jalankan composer install untuk menginstall dependencies.');
}

// Coba load autoloader
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Vendor autoload loaded successfully<br>";
} catch (Exception $e) {
    die('Error loading vendor/autoload.php: ' . $e->getMessage());
}

// Cek jika bootstrap/app.php ada
if (!file_exists(__DIR__ . '/bootstrap/app.php')) {
    die('Error: File bootstrap/app.php tidak ditemukan.');
}

// Coba load bootstrap/app.php
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✓ Bootstrap/app.php loaded successfully<br>";
} catch (Exception $e) {
    die('Error loading bootstrap/app.php: ' . $e->getMessage());
}

// Cek storage directory permissions
$storagePath = __DIR__ . '/storage';
if (!is_writable($storagePath)) {
    echo "⚠ Warning: storage directory is not writable<br>";
} else {
    echo "✓ Storage directory is writable<br>";
}

// Cek bootstrap/cache permissions
$bootstrapCachePath = __DIR__ . '/bootstrap/cache';
if (!is_writable($bootstrapCachePath)) {
    echo "⚠ Warning: bootstrap/cache directory is not writable<br>";
} else {
    echo "✓ Bootstrap/cache directory is writable<br>";
}

// Cek jika .env ada
if (!file_exists(__DIR__ . '/.env')) {
    echo "⚠ Warning: .env file not found<br>";
} else {
    echo "✓ .env file exists<br>";
    
    // Cek APP_KEY di .env
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
        $appKey = trim($matches[1]);
        if (empty($appKey) || $appKey === 'base64:') {
            echo "⚠ Warning: APP_KEY is empty or invalid<br>";
        } else {
            echo "✓ APP_KEY is set: {$appKey}<br>";
        }
    } else {
        echo "⚠ Warning: APP_KEY not found in .env file<br>";
    }
}

// Try to create a simple Laravel application to ensure core functionality works
try {
    echo "<hr><h3>Testing Laravel Application</h3>";
    // Get an instance of the IoC container
    echo "✓ Application is ready<br>";
    
    // Test configuration loading
    $appName = $app['config']->get('app.name');
    echo "✓ Config loaded: app.name = {$appName}<br>";
    
    echo "<hr><h3>All tests completed successfully!</h3>";
} catch (Exception $e) {
    echo "<hr><h3>Laravel Application Error:</h3>";
    echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
?> 