<?php
// File untuk memeriksa konfigurasi URL pada aplikasi

// Mengatur header
header('Content-Type: text/html; charset=utf-8');

// Kumpulkan informasi server
$server_name = $_SERVER['SERVER_NAME'] ?? 'unknown';
$server_port = $_SERVER['SERVER_PORT'] ?? '80';
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$server_protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$script_filename = $_SERVER['SCRIPT_FILENAME'] ?? 'unknown';
$document_root = $_SERVER['DOCUMENT_ROOT'] ?? 'unknown';

// Deteksi URL saat ini
$current_url = $server_protocol . '://' . $server_name;
if (($server_protocol === 'http' && $server_port != '80') || ($server_protocol === 'https' && $server_port != '443')) {
    $current_url .= ':' . $server_port;
}
$current_url .= $request_uri;

// Ambil URL dari .env
$env_app_url = '';
if (file_exists('../.env')) {
    $env_content = file_get_contents('../.env');
    preg_match('/APP_URL=(.*)/', $env_content, $matches);
    if (isset($matches[1])) {
        $env_app_url = trim($matches[1]);
    }
}

// Cek konsistensi URL
$url_mismatch = false;
if ($env_app_url && strpos($current_url, $env_app_url) !== 0) {
    $url_mismatch = true;
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Konfigurasi URL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #0066cc;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .info-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-warning {
            background-color: #fcf8e3;
            border: 1px solid #faebcc;
            color: #8a6d3b;
        }
        .alert-success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cek Konfigurasi URL</h1>
        
        <?php if ($url_mismatch): ?>
        <div class="alert alert-warning">
            <strong>Peringatan!</strong> URL saat ini tidak cocok dengan URL yang dikonfigurasi di file .env. 
            Ini dapat menyebabkan masalah dengan path relatif, pengalihan, dan pengiriman aset.
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <strong>URL OK!</strong> Konfigurasi URL sudah sesuai.
        </div>
        <?php endif; ?>
        
        <div class="info-box">
            <h2>Informasi URL</h2>
            <table>
                <tr>
                    <th>Deskripsi</th>
                    <th>Nilai</th>
                </tr>
                <tr>
                    <td>URL Saat Ini</td>
                    <td><?php echo htmlspecialchars($current_url); ?></td>
                </tr>
                <tr>
                    <td>URL di .env (APP_URL)</td>
                    <td><?php echo htmlspecialchars($env_app_url ?: 'Tidak ditemukan'); ?></td>
                </tr>
                <tr>
                    <td>Protocol</td>
                    <td><?php echo htmlspecialchars($server_protocol); ?></td>
                </tr>
                <tr>
                    <td>Server Name</td>
                    <td><?php echo htmlspecialchars($server_name); ?></td>
                </tr>
                <tr>
                    <td>Server Port</td>
                    <td><?php echo htmlspecialchars($server_port); ?></td>
                </tr>
                <tr>
                    <td>Document Root</td>
                    <td><?php echo htmlspecialchars($document_root); ?></td>
                </tr>
                <tr>
                    <td>Script Filename</td>
                    <td><?php echo htmlspecialchars($script_filename); ?></td>
                </tr>
            </table>
        </div>
        
        <h2>Rekomendasi</h2>
        <p>Untuk memastikan aplikasi berfungsi dengan benar, pastikan konfigurasi berikut:</p>
        <ol>
            <li>APP_URL di file .env harus menunjuk ke URL dasar yang benar dari aplikasi Anda.</li>
            <li>Jika menggunakan HTTPS, pastikan APP_URL dimulai dengan "https://".</li>
            <li>Pastikan mod_rewrite diaktifkan di server Apache Anda.</li>
            <li>Pastikan file .htaccess Anda dikonfigurasi dengan benar.</li>
        </ol>
        
        <p>Jika masih mengalami masalah, coba jalankan script check-server-config.php untuk pemeriksaan lebih lanjut.</p>
    </div>
</body>
</html> 