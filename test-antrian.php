<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Event;
use App\Events\AntrianDipanggil;

// Data test untuk panggilan
$testData = [
    'no_reg' => '7',
    'nama' => 'PASIEN TEST',
    'poli' => 'umum',
    'is_ulang' => false
];

// Broadcast event
event(new AntrianDipanggil($testData));

echo "Event antrian.dipanggil telah dikirim dengan data:\n";
print_r($testData); 