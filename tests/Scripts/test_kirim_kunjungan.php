<?php

require_once __DIR__.'/../../vendor/autoload.php';

use AamDsam\Bpjs\PCare;
use Illuminate\Support\Facades\Log;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

// Setup configuration
$config = [
    'base_url' => 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest', // URL Production
    'service_name' => 'kunjungan',
    'username' => '0159B0001', // Username PCare Production
    'password' => 'Puskesmas123#', // Password PCare Production  
    'cons_id' => '27999', // Consumer ID Production
    'secret_key' => '1aE95A11CB' // Secret Key Production
];

// Prepare test data
$dataKunjungan = [
    'noKunjungan' => null,
    'noKartu' => '0000043678034',
    'tglDaftar' => date('d-m-Y'), // Menggunakan tanggal hari ini
    'kdPoli' => 'UMU',
    'keluhan' => 'Demam dan batuk',
    'kdSadar' => '01',
    'sistole' => 120,
    'diastole' => 80,
    'beratBadan' => 65,
    'tinggiBadan' => 170,
    'respRate' => 20,
    'heartRate' => 80,
    'lingkarPerut' => 80,
    'kdStatusPulang' => '3',
    'tglPulang' => date('d-m-Y'), // Menggunakan tanggal hari ini
    'kdDokter' => '123456',
    'kdDiag1' => 'A01.0',
    'kdDiag2' => null,
    'kdDiag3' => null,
    'kdPoliRujukInternal' => null,
    'rujukLanjut' => null,
    'kdTacc' => 0,
    'alasanTacc' => null,
    'suhu' => '36.5',
    'alergiMakan' => '00',
    'alergiUdara' => '00',
    'alergiObat' => '00',
    'kdPrognosa' => '01',
    'anamnesa' => 'Pasien mengeluh demam dan batuk',
    'terapiObat' => 'Paracetamol 3x1',
    'terapiNonObat' => 'Istirahat cukup',
    'bmhp' => '-'
];

try {
    echo "=== Test Pengiriman Kunjungan PCare PRODUCTION ===\n\n";
    
    // 1. Validasi konfigurasi
    echo "Checking configuration...\n";
    foreach (['base_url', 'username', 'password', 'cons_id', 'secret_key'] as $key) {
        if (empty($config[$key])) {
            throw new Exception("Configuration '$key' is missing or empty");
        }
        // Mask sensitive data when printing
        $value = $config[$key];
        if (in_array($key, ['password', 'secret_key'])) {
            $value = str_repeat('*', strlen($value));
        }
        echo "$key: $value\n";
    }
    echo "Configuration OK\n\n";
    
    // 2. Validasi data kunjungan
    echo "Validating data...\n";
    $requiredFields = ['noKartu', 'tglDaftar', 'kdPoli', 'keluhan', 'kdSadar', 'kdDiag1'];
    foreach ($requiredFields as $field) {
        if (empty($dataKunjungan[$field])) {
            throw new Exception("Required field '$field' is missing or empty");
        }
        echo "$field: " . $dataKunjungan[$field] . "\n";
    }
    echo "Data validation OK\n\n";
    
    // 3. Initialize PCare client with error handling
    echo "Initializing PCare client...\n";
    try {
        $kunjungan = new PCare\Kunjungan($config);
        echo "Client initialized successfully\n\n";
    } catch (Exception $e) {
        throw new Exception("Failed to initialize PCare client: " . $e->getMessage());
    }
    
    // 4. Send data with retry mechanism
    echo "Sending data to BPJS Production...\n";
    echo "Request data:\n";
    print_r($dataKunjungan);
    
    $maxRetries = 3;
    $attempt = 1;
    $success = false;
    
    while ($attempt <= $maxRetries && !$success) {
        try {
            echo "\nAttempt $attempt of $maxRetries...\n";
            $response = $kunjungan->store($dataKunjungan);
            $success = true;
        } catch (Exception $e) {
            if ($attempt == $maxRetries) {
                throw new Exception("Failed after $maxRetries attempts: " . $e->getMessage());
            }
            echo "Attempt failed, retrying in 2 seconds...\n";
            sleep(2);
            $attempt++;
        }
    }
    
    echo "\nBPJS Response:\n";
    print_r($response);
    
    // 5. Analyze response
    if (isset($response['metaData']['code'])) {
        $code = $response['metaData']['code'];
        $message = $response['metaData']['message'] ?? 'No message';
        
        echo "\nResponse Analysis:\n";
        echo "Code: $code\n";
        echo "Message: $message\n";
        
        if ($code == '201') {
            echo "\n✅ SUCCESS: Kunjungan berhasil dikirim ke Production!\n";
            if (isset($response['response']['message'])) {
                echo "No Kunjungan: " . $response['response']['message'] . "\n";
            }
        } else {
            echo "\n❌ ERROR: Gagal mengirim kunjungan\n";
            echo "Error details: $message\n";
        }
    } else {
        echo "\n❌ ERROR: Invalid response format from BPJS\n";
        print_r($response);
    }

} catch (Exception $e) {
    echo "\n❌ ERROR OCCURRED:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
} 