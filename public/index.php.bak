<?php

// Aktifkan display errors untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Menetapkan zona waktu
date_default_timezone_set('Asia/Jakarta');

// Menetapkan batas waktu eksekusi
set_time_limit(300);

// Menetapkan batas memori
ini_set('memory_limit', '256M');

// Pastikan folder logs ada
$logDir = __DIR__ . '/../storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}

// Fungsi penanganan error kustom
function handleError($errno, $errstr, $errfile, $errline) {
    $logFile = __DIR__ . '/../storage/logs/php-error.log';
    $message = date('Y-m-d H:i:s') . " - Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($message, 3, $logFile);
    
    // Jangan tampilkan error di produksi
    if (getenv('APP_DEBUG') !== 'true') {
        return true;
    }
    
    return false;
}

// Daftarkan penanganan error kustom
set_error_handler('handleError');

// Fungsi penanganan exception kustom
function handleException($exception) {
    $logFile = __DIR__ . '/../storage/logs/php-error.log';
    $message = date('Y-m-d H:i:s') . " - Exception: " . $exception->getMessage() . 
               " in " . $exception->getFile() . " on line " . $exception->getLine() . 
               "\nStack trace: " . $exception->getTraceAsString() . "\n";
    error_log($message, 3, $logFile);
    
    // Jangan tampilkan error di produksi
    if (getenv('APP_DEBUG') !== 'true') {
        http_response_code(500);
        include __DIR__ . '/error.html';
        exit;
    }
}

// Daftarkan penanganan exception kustom
set_exception_handler('handleException');

// Penanganan fatal error
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $logFile = __DIR__ . '/../storage/logs/php-error.log';
        $message = date('Y-m-d H:i:s') . " - Fatal Error [{$error['type']}]: {$error['message']} in {$error['file']} on line {$error['line']}\n";
        error_log($message, 3, $logFile);
        
        if (getenv('APP_DEBUG') !== 'true') {
            http_response_code(500);
            include __DIR__ . '/error.html';
            exit;
        }
    }
});

// Penanganan khusus untuk favicon.ico
if (strpos($_SERVER['REQUEST_URI'], 'favicon.ico') !== false) {
    header('Content-Type: image/x-icon');
    readfile(__DIR__ . '/favicon.ico');
    exit;
}

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
