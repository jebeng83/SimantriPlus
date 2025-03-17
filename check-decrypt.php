<?php
/**
 * Script pengujian enkripsi dan dekripsi Laravel
 * 
 * Ini membantu menguji apakah APP_KEY Anda dapat mengenkripsi dan
 * mendekripsi data dengan benar. Berguna untuk debugging masalah
 * dekripsi pada aplikasi.
 * 
 * PERHATIAN: JANGAN tinggalkan script ini di server produksi setelah digunakan!
 */

// Memastikan kesalahan PHP ditampilkan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tampilkan header HTML jika dipanggil dari browser
$isCommandLine = php_sapi_name() === 'cli';
if (!$isCommandLine) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Test Enkripsi Laravel</title>
        <style>
            body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
            h1 { color: #006bb4; border-bottom: 2px solid #eee; padding-bottom: 10px; }
            .success { color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; }
            .error { color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; }
            .warning { color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; }
            code { background-color: #f5f7fb; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
            pre { background-color: #f5f7fb; padding: 15px; border-radius: 5px; overflow-x: auto; }
            .container { margin-bottom: 30px; }
        </style>
    </head>
    <body>
        <h1>Pengujian Enkripsi Laravel</h1>';
}

// Fungsi untuk output terformat berdasarkan mode (CLI atau browser)
function output($message, $type = 'info') {
    global $isCommandLine;
    
    if ($isCommandLine) {
        switch ($type) {
            case 'success':
                echo "[SUKSES] $message\n";
                break;
            case 'error':
                echo "[ERROR] $message\n";
                break;
            case 'warning':
                echo "[PERINGATAN] $message\n";
                break;
            default:
                echo "$message\n";
        }
    } else {
        echo "<div class=\"$type\">$message</div>";
    }
}

// Mulai pengujian
try {
    // Tentukan basepath
    $basePath = __DIR__; // direktori saat ini
    
    // 1. Periksa file .env
    output("Memeriksa file .env...");
    if (!file_exists($basePath . '/.env')) {
        output("File .env tidak ditemukan! Harap buat file .env terlebih dahulu.", "error");
        goto endTest;
    }
    output("File .env ditemukan.", "success");
    
    // 2. Baca kunci aplikasi dari .env
    output("\nMembaca APP_KEY dari .env...");
    $envContent = file_get_contents($basePath . '/.env');
    
    preg_match('/APP_KEY=(.*)/', $envContent, $matches);
    if (!isset($matches[1]) || empty(trim($matches[1]))) {
        output("APP_KEY tidak ditemukan di file .env!", "error");
        output("Harap jalankan 'php artisan key:generate' atau tetapkan APP_KEY secara manual di .env.", "warning");
        goto endTest;
    }
    
    $appKey = trim($matches[1]);
    output("APP_KEY ditemukan: " . $appKey, "success");
    
    // 3. Verifikasi format APP_KEY
    output("\nMemeriksa format APP_KEY...");
    $isValidFormat = strpos($appKey, 'base64:') === 0 && strlen($appKey) > 50;
    
    if (!$isValidFormat) {
        output("APP_KEY memiliki format yang tidak valid!", "error");
        output("APP_KEY harus dimulai dengan 'base64:' dan harus cukup panjang.", "warning");
        output("Contoh format yang benar: base64:HNU+Nb2vC44ablVRvqG6bls7tdpBmPYSOJLU+4rR4sE=", "info");
        goto endTest;
    }
    output("Format APP_KEY valid.", "success");
    
    // 4. Coba buat kelas serupa dengan EnkripsiData trait
    output("\nMencoba mengimplemen operasi enkripsi/dekripsi...");
    
    // Definisikan key dari APP_KEY
    $base64Key = substr($appKey, 7); // Hapus "base64:" prefix
    $key = base64_decode($base64Key);
    
    // Fungsi untuk enkripsi dan dekripsi manual
    function encryptManual($value, $key) {
        $iv = random_bytes(16);
        $value = openssl_encrypt(
            serialize($value),
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        
        if ($value === false) {
            throw new Exception('Tidak dapat mengenkripsi nilai');
        }
        
        $mac = hash_hmac('sha256', $iv.$value, $key);
        
        $json = json_encode([
            'iv' => base64_encode($iv),
            'value' => $value,
            'mac' => $mac,
        ]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Tidak dapat mengenkode payload JSON');
        }
        
        return base64_encode($json);
    }
    
    function decryptManual($payload, $key) {
        $payload = json_decode(base64_decode($payload), true);
        
        if (!$payload || !isset($payload['iv']) || !isset($payload['value']) || !isset($payload['mac'])) {
            throw new Exception('Payload tidak valid');
        }
        
        $iv = base64_decode($payload['iv']);
        $decrypted = openssl_decrypt(
            $payload['value'],
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        
        if ($decrypted === false) {
            throw new Exception('Tidak dapat mendekripsi nilai');
        }
        
        return unserialize($decrypted);
    }
    
    // 5. Test enkripsi dan dekripsi dengan key yang ditemukan
    output("\nUji enkripsi dan dekripsi dengan APP_KEY yang ditemukan...");
    
    $testData = [
        'simple_string' => 'Test string untuk enkripsi dan dekripsi',
        'object' => (object)['name' => 'John Doe', 'age' => 30],
        'array' => ['key1' => 'value1', 'key2' => 'value2'],
        'number' => 12345,
        'special_chars' => '!@#$%^&*()_+{}:"<>?[];\',./`~'
    ];
    
    $allPassed = true;
    
    foreach ($testData as $type => $original) {
        try {
            // Enkripsi
            $encrypted = encryptManual($original, $key);
            
            // Dekripsi
            $decrypted = decryptManual($encrypted, $key);
            
            // Bandingkan
            $isEqual = is_object($original) ? 
                       json_encode($original) === json_encode($decrypted) : 
                       $original === $decrypted;
            
            if ($isEqual) {
                output("Test enkripsi/dekripsi berhasil untuk tipe '$type'", "success");
            } else {
                output("Test enkripsi/dekripsi gagal untuk tipe '$type' - nilai berbeda", "error");
                $allPassed = false;
            }
        } catch (Exception $e) {
            output("Test enkripsi/dekripsi gagal untuk tipe '$type': " . $e->getMessage(), "error");
            $allPassed = false;
        }
    }
    
    // 6. Test dekripsi dengan data dari database (jika tersedia)
    output("\nCek koneksi database untuk pengujian data terenkripsi aktual...");
    
    // Ekstrak konfigurasi database dari .env
    preg_match('/DB_CONNECTION=(.*)/', $envContent, $matchesConn);
    preg_match('/DB_HOST=(.*)/', $envContent, $matchesHost);
    preg_match('/DB_PORT=(.*)/', $envContent, $matchesPort);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $matchesDB);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $matchesUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $matchesPass);
    
    $dbConnected = false;
    
    if (isset($matchesConn[1]) && isset($matchesHost[1]) && isset($matchesDB[1]) && isset($matchesUser[1])) {
        $dbConn = trim($matchesConn[1]);
        $dbHost = trim($matchesHost[1]);
        $dbPort = isset($matchesPort[1]) ? trim($matchesPort[1]) : '3306';
        $dbName = trim($matchesDB[1]);
        $dbUser = trim($matchesUser[1]);
        $dbPass = isset($matchesPass[1]) ? trim($matchesPass[1]) : '';
        
        try {
            // Coba koneksi ke database
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbConnected = true;
            
            output("Berhasil terhubung ke database.", "success");
            
            // Pengujian dekripsi data dari tabel (contoh: pasien)
            $testTable = 'pasien';
            $testColumn = 'no_rm';
            $stmt = $pdo->query("SHOW TABLES LIKE '{$testTable}'");
            
            if ($stmt->rowCount() > 0) {
                $stmt = $pdo->query("SELECT * FROM {$testTable} LIMIT 1");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row && isset($row[$testColumn])) {
                    $encryptedValue = $row[$testColumn];
                    
                    // Cek apakah nilai terlihat seperti terenkripsi
                    if (strpos($encryptedValue, 'eyJ') === 0) {
                        output("Ditemukan nilai potensial terenkripsi di kolom {$testColumn}.", "info");
                        
                        try {
                            $decryptedValue = decryptManual($encryptedValue, $key);
                            output("Berhasil mendekripsi nilai dari database: {$decryptedValue}", "success");
                        } catch (Exception $e) {
                            output("Gagal mendekripsi nilai dari database: " . $e->getMessage(), "error");
                            output("Ini bisa berarti data dienkripsi dengan APP_KEY yang berbeda dari yang sekarang digunakan!", "warning");
                        }
                    } else {
                        output("Nilai di kolom {$testColumn} tidak terlihat seperti terenkripsi.", "info");
                    }
                } else {
                    output("Tidak dapat menemukan kolom {$testColumn} di tabel {$testTable}.", "warning");
                }
            } else {
                output("Tabel {$testTable} tidak ditemukan dalam database.", "warning");
            }
        } catch (PDOException $e) {
            output("Gagal terhubung ke database: " . $e->getMessage(), "error");
        }
    } else {
        output("Konfigurasi database tidak lengkap di .env.", "warning");
    }
    
    // 7. Kesimpulan
    if ($allPassed) {
        output("\nSEMUA TEST ENKRIPSI/DEKRIPSI BERHASIL!", "success");
        output("APP_KEY Anda berfungsi dengan baik untuk enkripsi dan dekripsi.", "success");
        
        if ($dbConnected) {
            output("\nLangkah selanjutnya:", "info");
            output("1. Pastikan APP_KEY yang sama digunakan di lingkungan development dan production untuk data terenkripsi yang berbagi antar lingkungan.", "info");
            output("2. Jika Anda masih melihat kesalahan dekripsi, periksa apakah data dienkripsi dengan APP_KEY yang berbeda.", "info");
            output("3. Jika perlu menggunakan APP_KEY baru tetapi memiliki data terenkripsi dengan key lama, Anda perlu membuat script migrasi data.", "info");
        }
    } else {
        output("\nADA MASALAH DENGAN TEST ENKRIPSI/DEKRIPSI!", "error");
        output("Perlu penyelidikan lebih lanjut untuk masalah enkripsi/dekripsi.", "warning");
    }
    
} catch (Exception $e) {
    output("Terjadi kesalahan dalam pengujian: " . $e->getMessage(), "error");
}

// Label untuk goto statement
endTest:

// Tampilkan footer HTML jika dipanggil dari browser
if (!$isCommandLine) {
    echo '
    <div class="container">
        <h2>Rekomendasi untuk memperbaiki masalah:</h2>
        <ol>
            <li>Pastikan file .env memiliki APP_KEY yang valid dimulai dengan "base64:" dan cukup panjang</li>
            <li>Jalankan <code>php artisan key:generate</code> untuk menghasilkan kunci baru</li>
            <li>Jika menghasilkan kunci baru, perhatikan bahwa data terenkripsi sebelumnya tidak dapat didekripsi</li>
            <li>Pastikan .env memiliki APP_KEY yang sama antara lingkungan development dan production untuk data terenkripsi yang dibagi</li>
            <li>Periksa apakah <code>app/Traits/EnkripsiData.php</code> menggunakan metode enkripsi yang sama dengan Laravel bawaan</li>
        </ol>
        <p><strong>PERHATIAN:</strong> Hapus script ini setelah selesai digunakan di server produksi!</p>
    </div>
    </body>
    </html>';
}
?> 