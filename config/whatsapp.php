<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp Gateway Service
    |
    */

    'gateway_url' => env('WHATSAPP_GATEWAY_URL', 'http://localhost:8100'),
    
    'api_key' => env('WHATSAPP_API_KEY', ''),
    
    'timeout' => env('WHATSAPP_TIMEOUT', 30),
    
    'default_session' => env('WHATSAPP_DEFAULT_SESSION', 'edokter_session'),
    
    /*
    |--------------------------------------------------------------------------
    | Node.js WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Node.js WhatsApp Gateway Service
    |
    */
    
    'node_api_url' => env('WHATSAPP_NODE_API_URL', 'http://localhost:8100'),
    
    'node_enabled' => env('WHATSAPP_NODE_ENABLED', true),
    
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Templates
    |--------------------------------------------------------------------------
    |
    | Template pesan WhatsApp yang dapat digunakan
    |
    */
    
    'templates' => [
        'hasil_pemeriksaan' => [
            'name' => 'hasil_pemeriksaan',
            'message' => "Halo {nama_pasien},\n\nBerikut adalah hasil pemeriksaan ILP Anda pada tanggal {tanggal_pemeriksaan}.\n\n{ringkasan_hasil}\n\nUntuk hasil lengkap, silakan lihat dokumen yang terlampir.\n\nTerima kasih telah menggunakan layanan kami.\n\n*{nama_instansi}*"
        ],
        'reminder_pemeriksaan' => [
            'name' => 'reminder_pemeriksaan',
            'message' => "Halo {nama_pasien},\n\nIni adalah pengingat untuk pemeriksaan ILP Anda yang dijadwalkan pada:\n\nTanggal: {tanggal_jadwal}\nWaktu: {waktu_jadwal}\nLokasi: {lokasi_pemeriksaan}\n\nMohon hadir tepat waktu.\n\nTerima kasih.\n\n*{nama_instansi}*"
        ],
        'konfirmasi_jadwal' => [
            'name' => 'konfirmasi_jadwal',
            'message' => "Halo {nama_pasien},\n\nJadwal pemeriksaan ILP Anda telah dikonfirmasi:\n\nTanggal: {tanggal_jadwal}\nWaktu: {waktu_jadwal}\nLokasi: {lokasi_pemeriksaan}\n\nSilakan hadir 15 menit sebelum waktu yang dijadwalkan.\n\nTerima kasih.\n\n*{nama_instansi}*"
        ],
        'hasil_lab' => [
            'name' => 'hasil_lab',
            'message' => "Halo {nama_pasien},\n\nHasil pemeriksaan laboratorium Anda sudah tersedia.\n\nTanggal Pemeriksaan: {tanggal_pemeriksaan}\nJenis Pemeriksaan: {jenis_pemeriksaan}\n\nSilakan ambil hasil di {lokasi_pengambilan} atau lihat dokumen terlampir.\n\nTerima kasih.\n\n*{nama_instansi}*"
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan umum untuk WhatsApp
    |
    */
    
    'settings' => [
        'auto_retry' => env('WHATSAPP_AUTO_RETRY', true),
        'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('WHATSAPP_RETRY_DELAY', 5), // dalam detik
        'queue_enabled' => env('WHATSAPP_QUEUE_ENABLED', false),
        'queue_name' => env('WHATSAPP_QUEUE_NAME', 'whatsapp'),
        'webhook_enabled' => env('WHATSAPP_WEBHOOK_ENABLED', false),
        'webhook_url' => env('WHATSAPP_WEBHOOK_URL', ''),
        'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET', ''),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk upload file melalui WhatsApp
    |
    */
    
    'file_upload' => [
        'max_size' => env('WHATSAPP_MAX_FILE_SIZE', 16), // dalam MB
        'allowed_types' => [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 
            'jpg', 'jpeg', 'png', 'gif',
            'mp4', 'avi', 'mov',
            'mp3', 'wav', 'ogg'
        ],
        'storage_path' => 'whatsapp/uploads',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan logging untuk WhatsApp Gateway
    |
    */
    
    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'level' => env('WHATSAPP_LOG_LEVEL', 'info'),
        'channel' => env('WHATSAPP_LOG_CHANNEL', 'daily'),
    ],

];