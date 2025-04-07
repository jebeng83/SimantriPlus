<?php
/**
 * Script uji coba untuk menyimpan data ke tabel pcare_pendaftaran
 * Gunakan: php test_pcare_pendaftaran.php dari terminal
 */

// Konfigurasi database (diambil dari .env)
$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'kerjo';
$db_user = 'root';
$db_pass = '';

// Data yang akan disimpan (dari contoh yang diberikan)
$data = [
    'no_rawat' => '2025/04/07/000003',
    'no_rkm_medis' => '003669.8',
    'noUrut' => 'A94',
    'jenis_rawat' => 'Ralan' // Data tambahan yang tidak disimpan ke tabel
];

try {
    // Buat koneksi ke database
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Koneksi database berhasil\n";
    
    // Siapkan data lengkap berdasarkan struktur tabel
    $tglDaftar = date('Y-m-d'); // Tanggal hari ini
    
    // Query untuk mendapatkan data pasien berdasarkan no_rkm_medis
    $queryPasien = "SELECT nm_pasien FROM pasien WHERE no_rkm_medis = :no_rkm_medis LIMIT 1";
    $stmtPasien = $pdo->prepare($queryPasien);
    $stmtPasien->execute(['no_rkm_medis' => $data['no_rkm_medis']]);
    $pasien = $stmtPasien->fetch(PDO::FETCH_ASSOC);
    
    $namaPasien = $pasien ? $pasien['nm_pasien'] : 'PASIEN TEST';
    
    // Ambil data poli dari tabel maping_poliklinik_pcare
    $queryPoli = "SELECT kd_poli_pcare, nm_poli_pcare FROM maping_poliklinik_pcare LIMIT 1";
    $stmtPoli = $pdo->prepare($queryPoli);
    $stmtPoli->execute();
    $poli = $stmtPoli->fetch(PDO::FETCH_ASSOC);
    
    // Gunakan data poli dari database atau default jika tidak ditemukan
    $kdPoli = $poli ? $poli['kd_poli_pcare'] : 'UMU';
    $nmPoli = $poli ? $poli['nm_poli_pcare'] : 'POLI UMUM';
    
    echo "Data poli dari database: kode=$kdPoli, nama=$nmPoli\n";
    
    // Data lengkap untuk tabel pcare_pendaftaran
    $dataPcarePendaftaran = [
        'no_rawat' => $data['no_rawat'],
        'tglDaftar' => $tglDaftar,
        'no_rkm_medis' => $data['no_rkm_medis'],
        'nm_pasien' => $namaPasien,
        'kdProviderPeserta' => '11251616', // Sesuaikan dengan config
        'noKartu' => '0001234567890', // Contoh nomor kartu
        'kdPoli' => $kdPoli, // Diambil dari tabel maping_poliklinik_pcare
        'nmPoli' => $nmPoli, // Diambil dari tabel maping_poliklinik_pcare
        'keluhan' => 'Demam, Batuk, Pilek', // Contoh keluhan
        'kunjSakit' => 'Kunjungan Sakit',
        'sistole' => '120',
        'diastole' => '80',
        'beratBadan' => '60',
        'tinggiBadan' => '170',
        'respRate' => '20',
        'lingkar_perut' => '80',
        'heartRate' => '80',
        'rujukBalik' => '0',
        'kdTkp' => '10 Rawat Jalan',
        'noUrut' => $data['noUrut'],
        'status' => 'Terkirim'
    ];
    
    // Periksa apakah data dengan no_rawat yang sama sudah ada
    $checkQuery = "SELECT COUNT(*) as total FROM pcare_pendaftaran WHERE no_rawat = :no_rawat";
    $stmtCheck = $pdo->prepare($checkQuery);
    $stmtCheck->execute(['no_rawat' => $data['no_rawat']]);
    $count = $stmtCheck->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($count > 0) {
        // Update data yang sudah ada
        $updateColumns = [];
        $updateParams = [];
        
        foreach ($dataPcarePendaftaran as $key => $value) {
            if ($key !== 'no_rawat') { // no_rawat adalah primary key, jadi tidak diupdate
                $updateColumns[] = "$key = :$key";
                $updateParams[$key] = $value;
            }
        }
        
        $updateParams['no_rawat'] = $data['no_rawat']; // Tambahkan no_rawat untuk WHERE clause
        
        $updateQuery = "UPDATE pcare_pendaftaran SET " . implode(", ", $updateColumns) . " WHERE no_rawat = :no_rawat";
        $stmtUpdate = $pdo->prepare($updateQuery);
        $stmtUpdate->execute($updateParams);
        
        echo "Data berhasil diupdate di tabel pcare_pendaftaran\n";
    } else {
        // Insert data baru
        $columns = implode(", ", array_keys($dataPcarePendaftaran));
        $placeholders = ":" . implode(", :", array_keys($dataPcarePendaftaran));
        
        $insertQuery = "INSERT INTO pcare_pendaftaran ($columns) VALUES ($placeholders)";
        $stmtInsert = $pdo->prepare($insertQuery);
        $stmtInsert->execute($dataPcarePendaftaran);
        
        echo "Data berhasil disimpan ke tabel pcare_pendaftaran\n";
    }
    
    // Tampilkan data yang disimpan
    echo "\nData yang disimpan/diupdate:\n";
    echo "==========================\n";
    foreach ($dataPcarePendaftaran as $key => $value) {
        echo "$key: $value\n";
    }
    
    // Verifikasi data yang disimpan
    $selectQuery = "SELECT * FROM pcare_pendaftaran WHERE no_rawat = :no_rawat";
    $stmtSelect = $pdo->prepare($selectQuery);
    $stmtSelect->execute(['no_rawat' => $data['no_rawat']]);
    $result = $stmtSelect->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "\nVerifikasi data di database:\n";
        echo "==========================\n";
        foreach ($result as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "\nGagal mendapatkan data dari database untuk verifikasi\n";
    }
    
} catch (PDOException $e) {
    echo "Error database: " . $e->getMessage() . "\n";
    echo "Periksa kembali konfigurasi database dan pastikan tabel pcare_pendaftaran sudah dibuat\n";
} catch (Exception $e) {
    echo "Error umum: " . $e->getMessage() . "\n";
} 