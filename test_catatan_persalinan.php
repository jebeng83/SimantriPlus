<?php
// Simulasi data catatan persalinan
$catatanPersalinan = [
    "kala1_garis_waspada" => "Ya",
    "kala1_masalah_lain" => "Kontraksi lemah",
    "kala3_plasenta_lengkap" => "Tidak",
    "kala3_plasenta_lebih_30" => "Ya",
    "bayi_jenis_kelamin" => "L",
    "bayi_penilaian_bbl" => "Ada penyulit"
];

// Simulasi data tambahan
$kondisiBayi = [
    "status" => "Asfiksia", 
    "keringkan" => true,
    "hangat" => true,
    "rangsang" => false,
    "bebaskan" => true,
    "bungkus" => true
];

$tindakanPlasenta = [
    "a" => "Pengecekan manual",
    "b" => "Ekstraksi plasenta",
    "c" => null
];

$tindakanPlasenta30 = [
    "a" => "Pemberian oksitosin tambahan",
    "b" => "Kateterisasi urin",
    "c" => "Rujuk"
];

// Gabungkan data ke dalam sebuah field catatan
$catatanTambahan = [];

// Tambahkan informasi kondisi bayi
if ($kondisiBayi["status"] !== "Normal") {
    $catatanTambahan[] = "Kondisi bayi: " . $kondisiBayi["status"];
    
    $tindakanBayi = [];
    foreach ($kondisiBayi as $key => $value) {
        if ($key !== "status" && $value) {
            $tindakanBayi[] = ucfirst($key);
        }
    }
    
    if (!empty($tindakanBayi)) {
        $catatanTambahan[] = "Tindakan bayi: " . implode(", ", $tindakanBayi);
    }
}

// Tambahkan informasi tindakan plasenta
if ($catatanPersalinan["kala3_plasenta_lengkap"] == "Tidak") {
    $filteredTindakanPlasenta = array_filter($tindakanPlasenta);
    if (!empty($filteredTindakanPlasenta)) {
        $tindakan = [];
        foreach ($filteredTindakanPlasenta as $key => $value) {
            $tindakan[] = "$key) $value";
        }
        $catatanTambahan[] = "Tindakan plasenta tidak lengkap: " . implode("; ", $tindakan);
    }
}

// Tambahkan informasi tindakan plasenta > 30 menit
if ($catatanPersalinan["kala3_plasenta_lebih_30"] == "Ya") {
    $filteredTindakanPlasenta30 = array_filter($tindakanPlasenta30);
    if (!empty($filteredTindakanPlasenta30)) {
        $tindakan = [];
        foreach ($filteredTindakanPlasenta30 as $key => $value) {
            $tindakan[] = "$key) $value";
        }
        $catatanTambahan[] = "Tindakan plasenta >30 menit: " . implode("; ", $tindakan);
    }
}

// Gabungkan semua catatan tambahan
$catatanText = implode("\n", $catatanTambahan);

// Tambahkan ke field catatan yang ada di database
$catatanPersalinan["catatan"] = $catatanText;

// Tampilkan hasil
echo "=== CATATAN PERSALINAN YANG DISIMPAN ===\n";
print_r($catatanPersalinan);
echo "\n=== DETAIL CATATAN ===\n";
echo $catatanText . "\n";

// Simulasi validasi data sebelum disimpan ke database
$validFields = [
    'id_catatan', 'id_hamil', 'no_rawat', 'no_rkm_medis', 'catatan', 'petugas',
    'kala1_garis_waspada', 'kala1_masalah_lain', 'kala1_penatalaksanaan', 'kala1_hasil',
    'kala2_episiotomi', 'kala2_pendamping', 'kala2_gawat_janin', 'kala2_distosia_bahu',
    'kala3_lama', 'kala3_oksitosin', 'kala3_oks_2x', 'kala3_penegangan_tali_pusat',
    'kala3_plasenta_lengkap', 'kala3_plasenta_lebih_30',
    'bayi_berat_badan', 'bayi_panjang', 'bayi_jenis_kelamin', 'bayi_penilaian_bbl', 'bayi_pemberian_asi',
    'kala4_masalah', 'kala4_penatalaksanaan', 'kala4_hasil', 'updated_at'
];

$filteredData = array_intersect_key($catatanPersalinan, array_flip($validFields));

echo "\n=== DATA YANG AKAN DISIMPAN KE DATABASE ===\n";
print_r($filteredData); 