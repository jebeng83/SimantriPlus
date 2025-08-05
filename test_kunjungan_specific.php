<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
$host = $_ENV['DB_HOST'];
$database = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TEST KUNJUNGAN PCARE UNTUK NO_RAWAT: 2025/08/05/000006 ===\n\n";
    
    // 1. Ambil data registrasi
    $stmt = $pdo->prepare("
        SELECT r.*, p.no_peserta, p.nm_pasien, mdp.kd_dokter_pcare, pol.nm_poli
        FROM reg_periksa r
        JOIN pasien p ON r.no_rkm_medis = p.no_rkm_medis
        LEFT JOIN maping_dokter_pcare mdp ON r.kd_dokter = mdp.kd_dokter
        LEFT JOIN poliklinik pol ON r.kd_poli = pol.kd_poli
        WHERE r.no_rawat = ?
    ");
    $stmt->execute(['2025/08/05/000006']);
    $dataPasien = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$dataPasien) {
        echo "❌ Data registrasi tidak ditemukan untuk no_rawat: 2025/08/05/000006\n";
        exit(1);
    }
    
    echo "✅ Data Registrasi Ditemukan:\n";
    echo "   - No. Rawat: {$dataPasien->no_rawat}\n";
    echo "   - Pasien: {$dataPasien->nm_pasien}\n";
    echo "   - No. Peserta: {$dataPasien->no_peserta}\n";
    echo "   - Poli: {$dataPasien->nm_poli} ({$dataPasien->kd_poli})\n";
    echo "   - Dokter PCare: {$dataPasien->kd_dokter_pcare}\n";
    echo "   - Tanggal: {$dataPasien->tgl_registrasi}\n\n";
    
    // 2. Cek mapping poli PCare
    $stmt = $pdo->prepare("
        SELECT kd_poli_pcare, nm_poli_pcare 
        FROM maping_poliklinik_pcare 
        WHERE kd_poli_rs = ?
    ");
    $stmt->execute([$dataPasien->kd_poli]);
    $mappingPoli = $stmt->fetch(PDO::FETCH_OBJ);
    
    $kdPoliPcare = $mappingPoli ? $mappingPoli->kd_poli_pcare : '001';
    $nmPoliPcare = $mappingPoli ? $mappingPoli->nm_poli_pcare : 'POLI UMUM';
    
    echo "✅ Mapping Poli PCare:\n";
    echo "   - Kode RS: {$dataPasien->kd_poli}\n";
    echo "   - Kode PCare: {$kdPoliPcare}\n";
    echo "   - Nama PCare: {$nmPoliPcare}\n\n";
    
    // 3. Ambil data pemeriksaan
    $stmt = $pdo->prepare("
        SELECT * FROM pemeriksaan_ralan 
        WHERE no_rawat = ?
        ORDER BY tgl_perawatan DESC, jam_rawat DESC 
        LIMIT 1
    ");
    $stmt->execute(['2025/08/05/000006']);
    $pemeriksaanData = $stmt->fetch(PDO::FETCH_OBJ);
    
    echo "📋 Data Pemeriksaan:\n";
    if ($pemeriksaanData) {
        echo "   - Keluhan: {$pemeriksaanData->keluhan}\n";
        echo "   - Tensi: {$pemeriksaanData->tensi}\n";
        echo "   - Suhu: {$pemeriksaanData->suhu_tubuh}\n";
        echo "   - Nadi: {$pemeriksaanData->nadi}\n";
        echo "   - Respirasi: {$pemeriksaanData->respirasi}\n";
        echo "   - Berat: {$pemeriksaanData->berat}\n";
        echo "   - Tinggi: {$pemeriksaanData->tinggi}\n";
    } else {
        echo "   - Menggunakan data default\n";
    }
    echo "\n";
    
    // 4. Ambil data diagnosa
    $stmt = $pdo->prepare("
        SELECT dp.kd_penyakit, p.nm_penyakit 
        FROM diagnosa_pasien dp
        JOIN penyakit p ON dp.kd_penyakit = p.kd_penyakit
        WHERE dp.no_rawat = ? AND dp.prioritas = '1'
        LIMIT 1
    ");
    $stmt->execute(['2025/08/05/000006']);
    $diagnosaData = $stmt->fetch(PDO::FETCH_OBJ);
    
    echo "🩺 Data Diagnosa:\n";
    if ($diagnosaData) {
        echo "   - Kode: {$diagnosaData->kd_penyakit}\n";
        echo "   - Nama: {$diagnosaData->nm_penyakit}\n";
    } else {
        echo "   - Menggunakan diagnosa default: Z00.0\n";
    }
    echo "\n";
    
    // 5. Persiapkan data vital signs
    $sistole = 120;
    $diastole = 80;
    if ($pemeriksaanData && !empty($pemeriksaanData->tensi) && strpos($pemeriksaanData->tensi, '/') !== false) {
        $tensiParts = explode('/', $pemeriksaanData->tensi);
        $sistole = (int)trim($tensiParts[0]) ?: 120;
        $diastole = (int)trim($tensiParts[1]) ?: 80;
    }
    
    // 6. Ambil data resep obat
    $stmt = $pdo->prepare("
        SELECT db.nama_brng, rd.jml, rd.aturan_pakai
        FROM resep_obat ro
        JOIN resep_dokter rd ON ro.no_resep = rd.no_resep
        JOIN databarang db ON rd.kode_brng = db.kode_brng
        WHERE ro.no_rawat = ?
    ");
    $stmt->execute(['2025/08/05/000006']);
    $terapiObatData = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $terapiObatString = 'Edukasi Kesehatan';
    if (!empty($terapiObatData)) {
        $terapiObatArray = [];
        foreach ($terapiObatData as $obat) {
            $terapiObatArray[] = $obat->nama_brng . ' ' . $obat->jml . ' [' . $obat->aturan_pakai . ']';
        }
        $terapiObatString = implode(', ', $terapiObatArray);
    }
    
    echo "💊 Data Terapi Obat:\n";
    echo "   - {$terapiObatString}\n\n";
    
    // 7. Persiapkan data kunjungan sesuai format yang diperbaiki
    $kunjunganData = [
        'noKunjungan' => null,
        'noKartu' => $dataPasien->no_peserta,
        'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
        'kdPoli' => $kdPoliPcare,
        'keluhan' => $pemeriksaanData->keluhan ?? 'Kontrol rutin',
        'kunjSakit' => true,
        'kdSadar' => '04', // Compos Mentis
        'sistole' => $sistole,
        'diastole' => $diastole,
        'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
        'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 170),
        'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
        'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
        'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 0),
        'rujukBalik' => 0,
        'kdTkp' => '10', // Rawat Jalan
        'kdStatusPulang' => '4', // Sehat
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
        'kdPrognosa' => '01', // Baik
        'terapiObat' => $terapiObatString,
        'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Kontrol rutin',
        'bmhp' => 'Tidak ada',
        'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
    ];
    
    echo "📤 Data Kunjungan yang Akan Dikirim:\n";
    echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // 8. Setup PCare API
    $consId = $_ENV['BPJS_PCARE_CONS_ID'];
    $secretKey = $_ENV['BPJS_PCARE_CONS_PWD'];
    $username = $_ENV['BPJS_PCARE_USER'];
    $password = $_ENV['BPJS_PCARE_PASS'];
    $appCode = $_ENV['BPJS_PCARE_APP_CODE'];
    $userKey = $_ENV['BPJS_PCARE_USER_KEY'];
    $baseUrl = $_ENV['BPJS_PCARE_BASE_URL'];
    
    // Pastikan format password sesuai dengan PcareTrait
    if (strpos($password, '#') === false) {
        $password .= '#';
    }
    
    // 9. Generate signature dan headers
    $timestamp = time();
    $signature = hash_hmac('sha256', $consId . '&' . $timestamp, $secretKey, true);
    $encodedSignature = base64_encode($signature);
    
    // 10. Encrypt data
    $jsonData = json_encode($kunjunganData);
    $key = $consId . $secretKey . $timestamp;
    $encryptedData = base64_encode(openssl_encrypt($jsonData, 'AES-256-CBC', substr(hash('sha256', $key, true), 0, 32), OPENSSL_RAW_DATA, str_repeat("\0", 16)));
    
    // 11. Setup headers
    $headers = [
        'Content-Type: text/plain',
        'X-cons-id: ' . $consId,
        'X-timestamp: ' . $timestamp,
        'X-signature: ' . $encodedSignature,
        'X-authorization: Basic ' . base64_encode($username . ':' . $password . ':' . $appCode),
        'user_key: ' . $userKey
    ];
    
    // 12. Kirim request ke endpoint yang sudah diperbaiki
    $endpoint = 'kunjungan/v1';
    $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    
    echo "🚀 Mengirim Request ke PCare:\n";
    echo "   - URL: {$fullUrl}\n";
    echo "   - Method: POST\n";
    echo "   - Endpoint: {$endpoint}\n\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $encryptedData,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "📥 Response dari PCare:\n";
    echo "   - HTTP Code: {$httpCode}\n";
    
    if ($curlError) {
        echo "   - cURL Error: {$curlError}\n";
        exit(1);
    }
    
    if ($response) {
        // Decrypt response
        $decryptedResponse = openssl_decrypt(base64_decode($response), 'AES-256-CBC', substr(hash('sha256', $key, true), 0, 32), OPENSSL_RAW_DATA, str_repeat("\0", 16));
        
        if ($decryptedResponse) {
            $responseData = json_decode($decryptedResponse, true);
            echo "   - Response (Decrypted):\n";
            echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            // Analisis response
            if ($httpCode == 200 || $httpCode == 201) {
                echo "✅ SUKSES! Kunjungan PCare berhasil dikirim\n";
                if (isset($responseData['response']['message'])) {
                    echo "   - No Kunjungan: {$responseData['response']['message']}\n";
                } elseif (isset($responseData['response']['noKunjungan'])) {
                    echo "   - No Kunjungan: {$responseData['response']['noKunjungan']}\n";
                }
            } elseif ($httpCode == 412) {
                echo "⚠️  PRECONDITION FAILED - Ada masalah dengan data:\n";
                if (isset($responseData['response']) && is_array($responseData['response'])) {
                    foreach ($responseData['response'] as $error) {
                        echo "   - {$error['field']}: {$error['message']}\n";
                    }
                }
            } elseif ($httpCode == 500) {
                echo "❌ SERVER ERROR - Ada masalah dengan validasi data:\n";
                if (isset($responseData['response']['message'])) {
                    echo "   - {$responseData['response']['message']}\n";
                }
            } else {
                echo "❌ ERROR - HTTP {$httpCode}:\n";
                if (isset($responseData['metaData']['message'])) {
                    echo "   - {$responseData['metaData']['message']}\n";
                }
            }
        } else {
            echo "   - Response (Raw): {$response}\n";
        }
    } else {
        echo "   - No response received\n";
    }
    
    echo "\n=== SELESAI ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}