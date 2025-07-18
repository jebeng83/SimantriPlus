<?php
// Aktifkan tampilan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek koneksi ke database jika dibutuhkan
function testDbConnection() {
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // Coba baca .env file untuk mendapatkan konfigurasi database
        if (file_exists('../.env')) {
            $env = file_get_contents('../.env');
            
            // Parse DB settings
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
                $result['message'] = "Nama database tidak ditemukan di file .env";
                return $result;
            }
            
            $dsn = "{$connection}:host={$host};port={$port};dbname={$database}";
            $dbh = new PDO($dsn, $username, $password);
            $result['status'] = true;
            $result['message'] = "Berhasil terhubung ke database {$database}";
        } else {
            $result['message'] = "File .env tidak ditemukan";
        }
    } catch (PDOException $e) {
        $result['message'] = "Koneksi database gagal: " . $e->getMessage();
    } catch (Exception $e) {
        $result['message'] = "Error: " . $e->getMessage();
    }
    
    return $result;
}

// Cek permission direktori
function checkDirectoryPermissions() {
    $result = [];
    $directories = [
        '../storage' => 'Storage',
        '../storage/app' => 'Storage/App',
        '../storage/framework' => 'Storage/Framework',
        '../storage/logs' => 'Storage/Logs',
        '../bootstrap/cache' => 'Bootstrap/Cache'
    ];
    
    foreach ($directories as $dir => $name) {
        if (file_exists($dir)) {
            $result[$name] = is_writable($dir);
        } else {
            $result[$name] = false;
        }
    }
    
    return $result;
}

// Cek APP_KEY di .env
function checkAppKey() {
    $result = [
        'exists' => false,
        'valid' => false,
        'value' => ''
    ];
    
    if (file_exists('../.env')) {
        $result['exists'] = true;
        $env = file_get_contents('../.env');
        
        if (preg_match('/APP_KEY=(.*)/', $env, $matches)) {
            $appKey = trim($matches[1]);
            $result['value'] = $appKey;
            
            if (!empty($appKey) && $appKey !== 'base64:') {
                $result['valid'] = true;
            }
        }
    }
    
    return $result;
}
?>
// Cek Laravel log
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesalahan Server (500) - Simantri PLUS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .error-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 700px;
            width: 90%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #006bb4, #0095de);
        }
        .error-code {
            font-size: 72px;
            font-weight: 700;
            color: #006bb4;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin: 10px 0 20px;
            color: #333;
        }
        .error-message {
            margin-bottom: 30px;
            line-height: 1.6;
            color: #555;
        }
        .error-actions {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #006bb4;
            color: #fff;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 10px;
        }
        .btn:hover {
            background-color: #004d91;
            box-shadow: 0 5px 15px rgba(0, 107, 180, 0.3);
        }
        .btn-outline {
            background-color: transparent;
            border: 1px solid #006bb4;
            color: #006bb4;
        }
        .btn-outline:hover {
            background-color: #f0f7ff;
            box-shadow: 0 5px 15px rgba(0, 107, 180, 0.1);
        }
        .technical-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: left;
            font-size: 14px;
        }
        .technical-info h3 {
            font-size: 16px;
            color: #555;
        }
        .technical-info p {
            margin: 5px 0;
            color: #666;
        }
        .technical-info code {
            background-color: #f5f7fb;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 13px;
        }
        .logo {
            margin-bottom: 20px;
            max-width: 120px;
        }
        @media (max-width: 600px) {
            .error-container {
                padding: 30px 20px;
            }
            .error-code {
                font-size: 50px;
            }
            .error-title {
                font-size: 20px;
            }
            .error-actions .btn {
                display: block;
                margin: 10px auto;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="<?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http'; ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/img/logo/logo.png" alt="Logo Simantri PLUS" class="logo">
        
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Terjadi Kesalahan pada Server</h2>
        
        <div class="error-message">
            <p>Mohon maaf, sistem sedang mengalami kendala teknis. Tim IT kami telah diberitahu dan sedang bekerja untuk memperbaikinya.</p>
            <p>Silakan coba me-refresh halaman atau kembali beberapa saat lagi.</p>
        </div>
        
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn">Muat Ulang Halaman</a>
            <a href="<?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http'; ?>://<?php echo $_SERVER['HTTP_HOST']; ?>" class="btn btn-outline">Kembali ke Beranda</a>
        </div>
        
        <div class="technical-info">
            <h3>Informasi Teknis (untuk Administrator):</h3>
            <p>Error Time: <?php echo date('Y-m-d H:i:s'); ?></p>
            <p>Request URL: <?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></p>
            <p>Server: <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE']); ?></p>
            
            <?php if(file_exists(dirname(__DIR__) . '/storage/logs/laravel.log')): ?>
            <p>Periksa file log aplikasi di: <code>storage/logs/laravel.log</code></p>
            <?php endif; ?>
            
            <p>Jika Anda admin sistem, coba periksa:</p>
            <ol>
                <li>Konfigurasi <code>.env</code> (khususnya APP_KEY)</li>
                <li>Izin file dan direktori</li>
                <li>Koneksi database</li>
                <li>Error log PHP dan server</li>
            </ol>
            
            <p>Jika masalah berlanjut, hubungi tim dukungan teknis.</p>
        </div>
    </div>
</body>
</html> 