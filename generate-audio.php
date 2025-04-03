<?php

require 'vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

// Fungsi untuk generate audio file
function generateAudio($text, $outputFile) {
    try {
        // Inisialisasi client
        $client = new TextToSpeechClient([
            'credentials' => json_decode(file_get_contents('google-credentials.json'), true)
        ]);

        // Set input text
        $input = new SynthesisInput();
        $input->setText($text);

        // Set voice parameters
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('id-ID');
        $voice->setName('id-ID-Standard-A');

        // Set audio config
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);
        $audioConfig->setSpeakingRate(0.9);
        $audioConfig->setPitch(0);
        $audioConfig->setVolumeGainDb(0);

        // Perform text-to-speech request
        $response = $client->synthesizeSpeech($input, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();

        // Save to file
        file_put_contents($outputFile, $audioContent);
        echo "Audio berhasil dibuat: $outputFile\n";

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Buat direktori jika belum ada
$directories = [
    'public/assets/audio',
    'public/assets/audio/antrian',
    'public/assets/audio/poli'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Generate file audio umum
$commonAudio = [
    'bell' => 'ding dong',
    'nomor-antrian' => 'Nomor Antrian',
    'menuju' => 'Menuju'
];

foreach ($commonAudio as $filename => $text) {
    generateAudio($text, "public/assets/audio/$filename.mp3");
}

// Generate file audio nomor antrian (001-100)
for ($i = 1; $i <= 100; $i++) {
    $number = str_pad($i, 3, '0', STR_PAD_LEFT);
    generateAudio($number, "public/assets/audio/antrian/$number.mp3");
}

// Generate file audio poli
$poli = [
    'umum' => 'Poli Umum',
    'gigi' => 'Poli Gigi',
    'kia' => 'Poli KIA',
    'mtbs' => 'Poli MTBS',
    'lansia' => 'Poli Lansia',
    'kb' => 'Poli KB'
];

foreach ($poli as $filename => $text) {
    generateAudio($text, "public/assets/audio/poli/$filename.mp3");
}

echo "Proses generate audio selesai!\n"; 