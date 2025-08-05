<?php

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Database connection
try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

// PCare configuration
$pcareConfig = [
    'base_url' => $_ENV['BPJS_PCARE_BASE_URL'] ?? 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest',
    'cons_id' => $_ENV['BPJS_PCARE_CONS_ID'] ?? '7925',
    'secret_key' => $_ENV['BPJS_PCARE_SECRET_KEY'] ?? $_ENV['BPJS_PCARE_CONS_PWD'] ?? '',
    'user_key' => $_ENV['BPJS_PCARE_USER_KEY'] ?? '',
    'username' => $_ENV['BPJS_PCARE_USERNAME'] ?? $_ENV['BPJS_PCARE_USER'] ?? '',
    'password' => $_ENV['BPJS_PCARE_PASSWORD'] ?? $_ENV['BPJS_PCARE_PASS'] ?? '',
    'app_code' => $_ENV['BPJS_PCARE_APP_CODE'] ?? '095'
];

echo "\n=== KONFIGURASI PCARE ===\n";
echo "Base URL: " . $pcareConfig['base_url'] . "\n";
echo "Cons ID: " . $pcareConfig['cons_id'] . "\n";
echo "Username: " . $pcareConfig['username'] . "\n";

// Function to generate timestamp
function generateTimestamp() {
    return time();
}

// Function to generate signature
function generateSignature($consId, $secretKey, $timestamp) {
    $data = $consId . '&' . $timestamp;
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

// Function to generate authorization
function generateAuth($username, $password, $appCode) {
    $data = $username . ':' . $password . ':' . $appCode;
    return base64_encode($data);
}

// Function to encrypt data
function encryptData($data, $key) {
    $key = substr(hash('sha256', $key), 0, 32);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Function to decrypt data
function decryptData($encryptedData, $key) {
    $key = substr(hash('sha256', $key), 0, 32);
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// Function to decompress data
function decompressData($data) {
    return gzinflate($data);
}

// Get sample data from database
echo "\n=== MENGAMBIL DATA SAMPLE ===\n";

// Get latest registration with BPJS patient and mapped doctor
$sql = "
    SELECT 
        rp.no_rawat,
        rp.no_rkm_medis,
        rp.tgl_registrasi,
        rp.jam_reg,
        rp.kd_poli,
        rp.kd_dokter,
        p.no_peserta,
        p.nm_pasien,
        p.tgl_lahir,
        p.jk,
        pol.nm_poli,
        d.nm_dokter,
        mdp.kd_dokter_pcare,
        mpp.kd_poli_pcare
    FROM reg_periksa rp
    JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
    JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
    JOIN dokter d ON rp.kd_dokter = d.kd_dokter
    LEFT JOIN maping_dokter_pcare mdp ON rp.kd_dokter = mdp.kd_dokter
    LEFT JOIN maping_poliklinik_pcare mpp ON rp.kd_poli = mpp.kd_poli_rs
    WHERE p.no_peserta IS NOT NULL 
    AND p.no_peserta != ''
    AND mdp.kd_dokter_pcare IS NOT NULL
    AND mpp.kd_poli_pcare IS NOT NULL
    ORDER BY rp.tgl_registrasi DESC, rp.jam_reg DESC
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$dataPasien = $stmt->fetch(PDO::FETCH_OBJ);

if (!$dataPasien) {
    die("❌ Tidak ada data pasien BPJS dengan dokter dan poli yang sudah dimapping\n");
}

echo "✓ Data pasien ditemukan:\n";
echo "  No. Rawat: {$dataPasien->no_rawat}\n";
echo "  Nama: {$dataPasien->nm_pasien}\n";
echo "  No. Peserta: {$dataPasien->no_peserta}\n";
echo "  Dokter: {$dataPasien->nm_dokter} (PCare: {$dataPasien->kd_dokter_pcare})\n";
echo "  Poli: {$dataPasien->nm_poli} (PCare: {$dataPasien->kd_poli_pcare})\n";

// Get examination data
$sqlPemeriksaan = "
    SELECT *
    FROM pemeriksaan_ralan
    WHERE no_rawat = ?
    ORDER BY tgl_perawatan DESC, jam_rawat DESC
    LIMIT 1
";

$stmt = $pdo->prepare($sqlPemeriksaan);
$stmt->execute([$dataPasien->no_rawat]);
$pemeriksaanData = $stmt->fetch(PDO::FETCH_OBJ);

// Get diagnosis data
$sqlDiagnosa = "
    SELECT dp.kd_penyakit, p.nm_penyakit
    FROM diagnosa_pasien dp
    JOIN penyakit p ON dp.kd_penyakit = p.kd_penyakit
    WHERE dp.no_rawat = ?
    AND dp.prioritas = '1'
    LIMIT 1
";

$stmt = $pdo->prepare($sqlDiagnosa);
$stmt->execute([$dataPasien->no_rawat]);
$diagnosaData = $stmt->fetch(PDO::FETCH_OBJ);

echo "\n=== MENYIAPKAN DATA KUNJUNGAN ===\n";

// Prepare kunjungan data
$kunjunganData = [
    'noKartu' => $dataPasien->no_peserta,
    'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
    'kdPoli' => $dataPasien->kd_poli_pcare,
    'keluhan' => $pemeriksaanData->keluhan ?? 'Kontrol rutin',
    'kunjSakit' => true,
    'sistole' => (int)($pemeriksaanData->sistole ?? 120),
    'diastole' => (int)($pemeriksaanData->diastole ?? 80),
    'beratBadan' => (int)($pemeriksaanData->berat ?? 60),
    'tinggiBadan' => (int)($pemeriksaanData->tinggi ?? 160),
    'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
    'lingkarPerut' => (int)($pemeriksaanData->lingkar_perut ?? 80),
    'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
    'rujukBalik' => 0,
    'kdTkp' => '10',
    'kdStatusPulang' => '4',
    'tglPulang' => date('d-m-Y'),
    'kdDokter' => $dataPasien->kd_dokter_pcare,
    'kdDiag1' => $diagnosaData->kd_penyakit ?? 'Z00.0',
    'kdDiag2' => null,
    'kdDiag3' => null,
    'kdPoliRujukInternal' => null,
    'rujukLanjut' => null,
    'kdTacc' => -1,
    'alasanTacc' => null,
    'anamnesa' => $pemeriksaanData->anamnesis ?? 'Pemeriksaan rutin',
    'alergiMakan' => '00',
    'alergiUdara' => '00',
    'alergiObat' => '00',
    'kdPrognosa' => '01',
    'terapiObat' => $pemeriksaanData->rtl ?? 'Sesuai indikasi',
    'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Kontrol rutin',
    'bmhp' => 'Tidak ada',
    'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
];

echo "✓ Data kunjungan disiapkan:\n";
echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n=== MENGIRIM DATA KE PCARE ===\n";

// Generate headers
$timestamp = generateTimestamp();
$signature = generateSignature($pcareConfig['cons_id'], $pcareConfig['secret_key'], $timestamp);
$authorization = generateAuth($pcareConfig['username'], $pcareConfig['password'], $pcareConfig['app_code']);

// Encrypt data
$jsonData = json_encode($kunjunganData);
$encryptedData = encryptData($jsonData, $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $timestamp);

echo "Data terenkripsi: " . substr($encryptedData, 0, 50) . "...\n";

// Prepare request
$url = $pcareConfig['base_url'] . '/kunjungan';
$headers = [
    'X-cons-id: ' . $pcareConfig['cons_id'],
    'X-timestamp: ' . $timestamp,
    'X-signature: ' . $signature,
    'X-authorization: ' . $authorization,
    'user_key: ' . $pcareConfig['user_key'],
    'Content-Type: application/json'
];

echo "\nURL: $url\n";
echo "Headers:\n";
foreach ($headers as $header) {
    echo "  $header\n";
}

// Send request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $encryptedData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "\n=== HASIL RESPONSE ===\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "✓ Response received\n";
    echo "Raw Response: " . substr($response, 0, 200) . "...\n";
    
    if ($httpCode == 200) {
        try {
            // Try to decrypt response
            $decryptedResponse = decryptData($response, $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $timestamp);
            echo "\nDecrypted Response: $decryptedResponse\n";
            
            // Try to decompress if needed
            $decompressed = @decompressData($decryptedResponse);
            if ($decompressed) {
                echo "\nDecompressed Response: $decompressed\n";
            }
            
            $responseData = json_decode($decryptedResponse, true);
            if ($responseData) {
                echo "\n✓ Response berhasil didekripsi:\n";
                echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            }
        } catch (Exception $e) {
            echo "❌ Error decrypting response: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ HTTP Error $httpCode\n";
        echo "Response: $response\n";
    }
}

echo "\n=== TEST SELESAI ===\n";