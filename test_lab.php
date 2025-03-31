<?php
// Fungsi displayLabResults yang diambil dari kelas PemeriksaanANC
function displayLabResults($jsonString) {
    $output = '<div class="table-responsive">';
    $output .= '<table class="table table-bordered table-striped">';
    $output .= '<thead class="thead-light">';
    $output .= '<tr>';
    $output .= '<th width="40%">Jenis Pemeriksaan</th>';
    $output .= '<th width="30%">Hasil</th>';
    $output .= '<th width="30%">Satuan/Keterangan</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    
    try {
        // Jika jsonString sudah dalam bentuk array/object
        if (is_array($jsonString) || is_object($jsonString)) {
            $labData = $jsonString;
        } else {
            // Coba decode JSON string
            if (empty($jsonString) || $jsonString === 'null' || $jsonString === '[]') {
                $labData = [];
            } else {
                $labData = json_decode($jsonString, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return '<div class="alert alert-warning">Format data lab tidak valid: ' . json_last_error_msg() . '</div>';
                }
            }
        }
        
        $hasResults = false;
        
        if (empty($labData)) {
            $output .= '<tr><td colspan="3" class="text-center text-muted">Tidak ada data lab yang tersedia</td></tr>';
        } else {
            foreach ($labData as $key => $item) {
                // Periksa apakah nilai tersedia dan tidak kosong, TERLEPAS dari status checked
                if (isset($item['nilai']) && $item['nilai'] !== '' && $item['nilai'] !== null) {
                    $hasResults = true;
                    $label = getLabLabel($key);
                    
                    // Format nilai berdasarkan jenis lab
                    $nilai = $item['nilai'];
                    
                    // Tentukan satuan berdasarkan jenis lab
                    $satuan = '';
                    if ($key === 'hb') {
                        $satuan = 'g/dL';
                    } elseif ($key === 'gula_darah') {
                        $satuan = 'mg/dL';
                    }
                    
                    // Tambahkan class warna berdasarkan hasil
                    $rowClass = '';
                    $badgeHtml = '';
                    
                    if (in_array($key, ['hiv', 'sifilis', 'hbsag']) && 
                        (strtolower($nilai) === 'reaktif' || strtolower($nilai) === 'positif')) {
                        $rowClass = 'table-danger';
                        $badgeHtml = '<span class="badge badge-danger">Perlu Perhatian</span>';
                    } elseif ($key === 'malaria' && strtolower($nilai) === 'positif') {
                        $rowClass = 'table-warning';
                        $badgeHtml = '<span class="badge badge-warning">Perlu Perhatian</span>';
                    } elseif ($key === 'protein_urin' && in_array(strtolower($nilai), ['positif', '+1', '+2', '+3', '+4'])) {
                        $rowClass = 'table-warning';
                        $badgeHtml = '<span class="badge badge-warning">Perlu Perhatian</span>';
                    }
                    
                    $output .= "<tr class=\"{$rowClass}\">";
                    $output .= "<td><strong>{$label}</strong></td>";
                    $output .= "<td>{$nilai}</td>";
                    $output .= "<td>{$satuan} {$badgeHtml}</td>";
                    $output .= '</tr>';
                }
            }
            
            if (!$hasResults) {
                $output .= '<tr><td colspan="3" class="text-center text-muted">Tidak ada hasil lab yang tercatat</td></tr>';
            }
        }
        
    } catch (Exception $e) {
        $output .= '<tr><td colspan="3" class="text-center text-danger">Error: ' . $e->getMessage() . '</td></tr>';
    }
    
    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>';
    
    return $output;
}

// Fungsi helper untuk mendapatkan label lab
function getLabLabel($key) {
    $labels = [
        'hb' => 'Hemoglobin (Hb)',
        'goldar' => 'Golongan Darah',
        'gula_darah' => 'Gula Darah',
        'protein_urin' => 'Protein Urin',
        'hiv' => 'HIV',
        'sifilis' => 'Sifilis',
        'hbsag' => 'HBsAg',
        'malaria' => 'Malaria'
    ];
    
    return isset($labels[$key]) ? $labels[$key] : ucfirst(str_replace('_', ' ', $key));
}

// Data test
$test = [
    "hb" => ["checked" => false, "nilai" => "12"],
    "goldar" => ["checked" => false, "nilai" => "AB"],
    "protein_urin" => ["checked" => false, "nilai" => "Negatif"],
    "hiv" => ["checked" => false, "nilai" => "Non-reaktif"]
];

echo displayLabResults($test); 