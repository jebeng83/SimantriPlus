<?php
$jsonData = "{\"malaria\":{\"nilai\":\"Positif\",\"checked\":true},\"gula_darah\":{\"nilai\":\"100\",\"checked\":true},\"hbsag\":{\"nilai\":\"Non Reaktif\",\"checked\":true},\"protein_urin\":{\"nilai\":\"Negatif\",\"checked\":true},\"sifilis\":{\"nilai\":\"Non Reaktif\",\"checked\":true},\"goldar\":{\"nilai\":\"B\",\"checked\":true},\"hiv\":{\"nilai\":\"Non Reaktif\",\"checked\":true},\"hb\":{\"nilai\":\"15\",\"checked\":true}}";

echo "Data di database: " . $jsonData . "\n\n";

$decoded = json_decode($jsonData, true);
echo "Data setelah decode: \n";
var_export($decoded);
echo "\n\n";

$expected = [
    'malaria' => ['nilai' => 'Positif', 'checked' => true],
    'gula_darah' => ['nilai' => '100', 'checked' => true],
    'hbsag' => ['nilai' => 'Non Reaktif', 'checked' => true],
    'protein_urin' => ['nilai' => 'Negatif', 'checked' => true],
    'sifilis' => ['nilai' => 'Non Reaktif', 'checked' => true],
    'goldar' => ['nilai' => 'B', 'checked' => true],
    'hiv' => ['nilai' => 'Non Reaktif', 'checked' => true],
    'hb' => ['nilai' => '15', 'checked' => true],
];

echo "Perbandingan dengan data yang diharapkan:\n";
echo "Sama: " . (json_encode($decoded) === json_encode($expected) ? "Ya" : "Tidak") . "\n"; 