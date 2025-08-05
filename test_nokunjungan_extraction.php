<?php

// Test script to verify noKunjungan extraction logic

echo "=== TEST NOKUNJUNGAN EXTRACTION ===\n";

// Simulate the actual response format from PCare
$responseData = [
    'response' => [
        [
            'field' => 'noKunjungan',
            'message' => '112516160825Y000381'
        ]
    ],
    'metaData' => [
        'message' => 'Created',
        'code' => 201
    ]
];

echo "\nResponse data structure:\n";
echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";

// Test the extraction logic
$responseCode = $responseData['metaData']['code'];
$noKunjungan = null;

if ($responseCode == '201' || $responseCode == 201) {
    echo "\n✓ Response code 201 detected\n";
    
    // Extract noKunjungan from response array in new format
    if (isset($responseData['response']) && is_array($responseData['response'])) {
        echo "✓ Response array found\n";
        
        foreach ($responseData['response'] as $item) {
            echo "  - Checking item: " . json_encode($item) . "\n";
            
            if (isset($item['field']) && $item['field'] === 'noKunjungan') {
                $noKunjungan = $item['message'] ?? null;
                echo "  ✓ Found noKunjungan field!\n";
                break;
            }
        }
    }
}

echo "\n=== RESULT ===\n";
echo "Extracted noKunjungan: " . ($noKunjungan ?? 'NULL') . "\n";

if ($noKunjungan === '112516160825Y000381') {
    echo "✅ SUCCESS: noKunjungan extracted correctly!\n";
} else {
    echo "❌ FAILED: noKunjungan extraction failed!\n";
}

echo "\n=== TEST COMPLETED ===\n";