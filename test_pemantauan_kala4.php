<?php
// Simulasi data pemantauan kala 4
$pemantauanKala4 = [
    [
        'extra_field' => 'Tidak boleh ada di database',
        'jam_ke' => 1,
        'waktu' => '14:00',
        'tekanan_darah' => '120/80',
        'nadi' => 80,
        'tinggi_fundus' => '2 jari dibawah pusat',
        'kontraksi' => 'Baik',
        'kandung_kemih' => 'Kosong',
        'perdarahan' => 'Minimal'
    ],
    [
        'extra_field' => 'Tidak boleh ada di database',
        'jam_ke' => 2,
        'waktu' => '15:00',
        'tekanan_darah' => '110/70',
        'nadi' => 76,
        'tinggi_fundus' => '2 jari dibawah pusat',
        'kontraksi' => 'Baik',
        'kandung_kemih' => 'Kosong',
        'perdarahan' => 'Minimal'
    ]
];

// Definisikan field yang valid
$validFields = [
    'id_catatan', 'id_hamil', 'jam_ke', 'waktu', 'tekanan_darah', 
    'nadi', 'tinggi_fundus', 'kontraksi', 'kandung_kemih', 'perdarahan',
    'created_at', 'updated_at'
];

echo "=== DATA PEMANTAUAN KALA 4 SEBELUM DIFILTER ===\n";
print_r($pemantauanKala4);

// Simulasi filter data untuk setiap baris
$filteredPemantauan = [];
foreach ($pemantauanKala4 as $index => $pemantauan) {
    $dataToSave = [
        'id_catatan' => 'CAT2308220001',
        'id_hamil' => 'HAM2308220001',
        'jam_ke' => $pemantauan['jam_ke'] ?? ($index + 1),
        'waktu' => $pemantauan['waktu'] ?? null,
        'tekanan_darah' => $pemantauan['tekanan_darah'] ?? null,
        'nadi' => $pemantauan['nadi'] ?? null,
        'tinggi_fundus' => $pemantauan['tinggi_fundus'] ?? null,
        'kontraksi' => $pemantauan['kontraksi'] ?? null,
        'kandung_kemih' => $pemantauan['kandung_kemih'] ?? null,
        'perdarahan' => $pemantauan['perdarahan'] ?? null,
        'created_at' => '2023-08-22 14:00:00',
        'updated_at' => '2023-08-22 14:00:00'
    ];
    
    // Filter hanya field yang valid
    $filteredData = array_intersect_key($dataToSave, array_flip($validFields));
    $filteredPemantauan[] = $filteredData;
}

echo "\n=== DATA PEMANTAUAN KALA 4 SETELAH DIFILTER ===\n";
print_r($filteredPemantauan); 