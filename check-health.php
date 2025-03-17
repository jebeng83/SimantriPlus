<?php
// Script untuk memeriksa kesehatan Laravel secara langsung

// Ambil versi Laravel
$composer_json = file_get_contents(__DIR__ . '/composer.json');
$composer_data = json_decode($composer_json, true);
$laravel_version = isset($composer_data['require']['laravel/framework']) ? $composer_data['require']['laravel/framework'] : 'Unknown';

// Ambil direktori aktif
$current_dir = __DIR__;

// Periksa izin file kritis
$critical_dirs = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/views',
    'storage/framework/sessions',
    'storage/framework/cache',
    'bootstrap/cache'
];

$dir_status = [];
foreach ($critical_dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    $dir_status[$dir] = [
        'exists' => file_exists($full_path),
        'writable' => is_writable($full_path),
        'permissions' => file_exists($full_path) ? substr(sprintf('%o', fileperms($full_path)), -4) : 'N/A'
    ];
}

// Periksa file konfigurasi
$env_exists = file_exists(__DIR__ . '/.env');

// Coba membuat file uji di direktori storage
$test_write = false;
$test_file = __DIR__ . '/storage/framework/views/test_' . time() . '.txt';
try {
    $test_write = file_put_contents($test_file, 'Test write at ' . date('Y-m-d H:i:s')) !== false;
    if (file_exists($test_file)) {
        unlink($test_file);
    }
} catch (Exception $e) {
    $test_write = false;
}

// Coba akses database
$db_connection = false;
$db_error = '';
if ($env_exists) {
    $env_content = file_get_contents(__DIR__ . '/.env');
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
            $db_connection = true;
        } catch (PDOException $e) {
            $db_error = $e->getMessage();
        }
    }
}

// Check Apache modules if available
$apache_modules = function_exists('apache_get_modules') ? apache_get_modules() : [];
$has_mod_rewrite = in_array('mod_rewrite', $apache_modules);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Health Check</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #3490dc;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        h2 {
            margin-top: 30px;
            color: #38a169;
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            border-top: 4px solid #3490dc;
        }
        .success {
            color: #38a169;
        }
        .warning {
            color: #e3a008;
        }
        .error {
            color: #e53e3e;
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
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-success {
            background-color: #38a169;
        }
        .badge-warning {
            background-color: #e3a008;
        }
        .badge-error {
            background-color: #e53e3e;
        }
    </style>
</head>
<body>
    <h1>Laravel Health Check</h1>
    
    <div class="card">
        <h2>Informasi Sistem</h2>
        <table>
            <tr>
                <th width="30%">Versi Laravel</th>
                <td><?php echo htmlspecialchars($laravel_version); ?></td>
            </tr>
            <tr>
                <th>Versi PHP</th>
                <td><?php echo htmlspecialchars(phpversion()); ?></td>
            </tr>
            <tr>
                <th>Direktori Aplikasi</th>
                <td><?php echo htmlspecialchars($current_dir); ?></td>
            </tr>
            <tr>
                <th>File .env</th>
                <td>
                    <?php if ($env_exists): ?>
                        <span class="badge badge-success">Ada</span>
                    <?php else: ?>
                        <span class="badge badge-error">Tidak Ada</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>mod_rewrite</th>
                <td>
                    <?php if ($has_mod_rewrite): ?>
                        <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Tidak Terdeteksi</span> (Tidak dapat mendeteksi atau tidak aktif)
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="card">
        <h2>Pemeriksaan Izin Direktori</h2>
        <table>
            <tr>
                <th>Direktori</th>
                <th>Ada</th>
                <th>Dapat Ditulis</th>
                <th>Izin</th>
            </tr>
            <?php foreach ($dir_status as $dir => $status): ?>
            <tr>
                <td><?php echo htmlspecialchars($dir); ?></td>
                <td>
                    <?php if ($status['exists']): ?>
                        <span class="badge badge-success">Ya</span>
                    <?php else: ?>
                        <span class="badge badge-error">Tidak</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($status['writable']): ?>
                        <span class="badge badge-success">Ya</span>
                    <?php else: ?>
                        <span class="badge badge-error">Tidak</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($status['permissions']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="card">
        <h2>Tes Tulis File</h2>
        <p>
            <?php if ($test_write): ?>
                <span class="success">✓ Uji tulis ke storage/framework/views berhasil</span>
            <?php else: ?>
                <span class="error">✗ Uji tulis ke storage/framework/views gagal</span>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="card">
        <h2>Koneksi Database</h2>
        <p>
            <?php if ($db_connection): ?>
                <span class="success">✓ Koneksi database berhasil</span>
            <?php else: ?>
                <span class="error">✗ Koneksi database gagal: <?php echo htmlspecialchars($db_error); ?></span>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="card">
        <h2>Konfigurasi PHP</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Nilai Saat Ini</th>
                <th>Nilai Direkomendasikan</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>memory_limit</td>
                <td><?php echo htmlspecialchars(ini_get('memory_limit')); ?></td>
                <td>256M</td>
                <td>
                    <?php if (intval(ini_get('memory_limit')) >= 256): ?>
                        <span class="badge badge-success">OK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Kurang</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>max_execution_time</td>
                <td><?php echo htmlspecialchars(ini_get('max_execution_time')); ?></td>
                <td>300</td>
                <td>
                    <?php if (intval(ini_get('max_execution_time')) >= 300 || intval(ini_get('max_execution_time')) == 0): ?>
                        <span class="badge badge-success">OK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Kurang</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>post_max_size</td>
                <td><?php echo htmlspecialchars(ini_get('post_max_size')); ?></td>
                <td>64M</td>
                <td>
                    <?php if (intval(ini_get('post_max_size')) >= 64): ?>
                        <span class="badge badge-success">OK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Kurang</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>upload_max_filesize</td>
                <td><?php echo htmlspecialchars(ini_get('upload_max_filesize')); ?></td>
                <td>64M</td>
                <td>
                    <?php if (intval(ini_get('upload_max_filesize')) >= 64): ?>
                        <span class="badge badge-success">OK</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Kurang</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Langkah Selanjutnya</h2>
        <?php if ($test_write && $db_connection && $env_exists): ?>
            <p class="success">✓ Semua pemeriksaan utama berhasil. Aplikasi siap dijalankan.</p>
            <p>Untuk memastikan aplikasi berjalan dengan optimal:</p>
            <ol>
                <li>Pastikan nilai konfigurasi PHP sudah ditingkatkan (memory_limit, max_execution_time, dll.)</li>
                <li>Bersihkan cache Laravel dengan perintah: <code>php artisan cache:clear</code></li>
                <li>Pastikan web server (Apache/Nginx) sudah dikonfigurasi dengan benar</li>
                <li>Pastikan .htaccess memiliki konfigurasi yang benar</li>
            </ol>
        <?php else: ?>
            <p class="error">✗ Ada beberapa masalah yang perlu diperbaiki:</p>
            <ul>
                <?php if (!$test_write): ?>
                    <li>Direktori storage dan bootstrap/cache memerlukan izin tulis (chmod -R 777 storage bootstrap/cache)</li>
                <?php endif; ?>
                <?php if (!$db_connection): ?>
                    <li>Koneksi database gagal - pastikan konfigurasi database sudah benar di file .env</li>
                <?php endif; ?>
                <?php if (!$env_exists): ?>
                    <li>File .env tidak ditemukan - salin dari .env.example dan sesuaikan konfigurasinya</li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html> 