<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\WaOutbox;
use Exception;

class WhatsAppGatewayService
{
    private $baseUrl;
    private $apiKey;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.gateway_url', 'http://localhost:3000');
        $this->apiKey = config('whatsapp.api_key', '');
        $this->timeout = config('whatsapp.timeout', 30);
    }

    /**
     * Kirim pesan teks WhatsApp (langsung atau melalui antrian)
     *
     * @param string $phone
     * @param string $message
     * @param string $sessionId
     * @param bool $useQueue
     * @param array $options
     * @return array
     */
    public function sendTextMessage($phone, $message, $sessionId = 'default', $useQueue = true, $options = [])
    {
        try {
            // Jika menggunakan antrian, simpan ke wa_outbox
            if ($useQueue) {
                return $this->queueMessage($phone, $message, WaOutbox::TYPE_TEXT, $options);
            }

            // Kirim langsung tanpa antrian
            return $this->sendDirectMessage($phone, $message);
        } catch (Exception $e) {
            Log::error('WhatsApp service error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim file/dokumen WhatsApp (langsung atau melalui antrian)
     *
     * @param string $phone
     * @param string $filePath
     * @param string $caption
     * @param string $sessionId
     * @param bool $useQueue
     * @param array $options
     * @return array
     */
    public function sendDocument($phone, $filePath, $caption = '', $sessionId = 'default', $useQueue = true, $options = [])
    {
        try {
            // Cek apakah file ada
            if (!Storage::exists($filePath) && !file_exists($filePath)) {
                return [
                    'success' => false,
                    'error' => 'File tidak ditemukan'
                ];
            }

            // Jika menggunakan antrian, simpan ke wa_outbox
            if ($useQueue) {
                return $this->queueDocument($phone, $filePath, $caption, $options);
            }

            // Kirim langsung tanpa antrian
            return $this->sendDirectDocument($phone, $filePath, $caption);
        } catch (Exception $e) {
            Log::error('WhatsApp document service error', [
                'phone' => $phone,
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cek status koneksi WhatsApp
     *
     * @param string $sessionId
     * @return array
     */
    public function getSessionStatus($sessionId = 'default')
    {
        try {
            // Node.js server belum memiliki endpoint status, gunakan health check
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->get($this->baseUrl . '/');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => [
                        'status' => 'connected',
                        'message' => $data['message'] ?? 'WhatsApp Gateway is running'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal mendapatkan status session',
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan QR Code untuk koneksi WhatsApp
     *
     * @param string $sessionId
     * @return array
     */
    public function getQRCode($sessionId = 'default')
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/WA-QrCode');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => [
                        'qr' => $data['qrBarCode'] ?? null,
                        'status' => $data['status'] ?? false,
                        'message' => $data['message'] ?? 'QR Code tidak tersedia'
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal mendapatkan QR Code',
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Buat session WhatsApp baru
     *
     * @param string $sessionId
     * @return array
     */
    public function createSession($sessionId = 'default')
    {
        try {
            // Node.js server otomatis membuat session saat startup
            // Kita cek status dengan endpoint QR Code
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/WA-QrCode');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'message' => 'Session siap digunakan'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal membuat session: ' . $response->body(),
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hapus session WhatsApp
     *
     * @param string $sessionId
     * @return array
     */
    public function deleteSession($sessionId = 'default')
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/StopWAG');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Session berhasil dihentikan'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal menghentikan session',
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Dapatkan headers untuk request API
     *
     * @return array
     */
    private function getHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        if (!empty($this->apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        return $headers;
    }

    /**
     * Kirim pesan template WhatsApp
     *
     * @param string $phone
     * @param string $templateName
     * @param array $parameters
     * @param string $sessionId
     * @param bool $useQueue
     * @return array
     */
    public function sendTemplateMessage($phone, $templateName, $parameters = [], $sessionId = 'default', $useQueue = true)
    {
        try {
            // Node.js server tidak memiliki endpoint template, gunakan send-message biasa
            $templateConfig = config('whatsapp.templates.' . $templateName, []);
            $message = $templateConfig['content'] ?? 'Template tidak ditemukan';
            
            // Replace parameters dalam template
            foreach ($parameters as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }
            
            return $this->sendTextMessage($phone, $message, $sessionId, $useQueue, [
                'source' => 'TEMPLATE_' . strtoupper($templateName)
            ]);
        } catch (Exception $e) {
            Log::error('WhatsApp template service error', [
                'phone' => $phone,
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simpan pesan ke antrian wa_outbox
     *
     * @param string $phone
     * @param string $message
     * @param string $type
     * @param array $options
     * @return array
     */
    public function queueMessage($phone, $message, $type = WaOutbox::TYPE_TEXT, $options = [])
    {
        try {
            $waOutbox = WaOutbox::createMessage($phone, $message, array_merge([
                'type' => $type,
                'sender' => WaOutbox::SENDER_NODEJS
            ], $options));

            Log::info('Message queued successfully', [
                'nomor' => $waOutbox->nomor,
                'phone' => $phone,
                'type' => $type
            ]);

            return [
                'success' => true,
                'data' => [
                    'nomor' => $waOutbox->nomor,
                    'status' => $waOutbox->status
                ],
                'message' => 'Pesan berhasil ditambahkan ke antrian'
            ];
        } catch (Exception $e) {
            Log::error('Failed to queue message', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Gagal menambahkan pesan ke antrian: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simpan dokumen ke antrian wa_outbox
     *
     * @param string $phone
     * @param string $filePath
     * @param string $caption
     * @param array $options
     * @return array
     */
    public function queueDocument($phone, $filePath, $caption = '', $options = [])
    {
        try {
            $waOutbox = WaOutbox::createMessage($phone, $caption, array_merge([
                'type' => WaOutbox::TYPE_DOCUMENT,
                'file' => $filePath,
                'sender' => WaOutbox::SENDER_NODEJS
            ], $options));

            Log::info('Document queued successfully', [
                'nomor' => $waOutbox->nomor,
                'phone' => $phone,
                'file' => $filePath
            ]);

            return [
                'success' => true,
                'data' => [
                    'nomor' => $waOutbox->nomor,
                    'status' => $waOutbox->status
                ],
                'message' => 'Dokumen berhasil ditambahkan ke antrian'
            ];
        } catch (Exception $e) {
            Log::error('Failed to queue document', [
                'phone' => $phone,
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Gagal menambahkan dokumen ke antrian: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim pesan langsung tanpa antrian
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendDirectMessage($phone, $message)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . '/send-message', [
                    'number' => $this->formatPhoneNumber($phone),
                    'message' => $message
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phone,
                    'response' => $data
                ]);
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Pesan berhasil dikirim'
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Gagal mengirim pesan: ' . $response->body(),
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            Log::error('WhatsApp direct message error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim dokumen langsung tanpa antrian
     *
     * @param string $phone
     * @param string $filePath
     * @param string $caption
     * @return array
     */
    public function sendDirectDocument($phone, $filePath, $caption = '')
    {
        try {
            // Dapatkan path absolut file
            $absolutePath = Storage::exists($filePath) ? Storage::path($filePath) : $filePath;
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->attach('file', file_get_contents($absolutePath), basename($absolutePath))
                ->post($this->baseUrl . '/send-file', [
                    'number' => $this->formatPhoneNumber($phone),
                    'caption' => $caption
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('WhatsApp document sent successfully', [
                    'phone' => $phone,
                    'file' => $filePath,
                    'response' => $data
                ]);
                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Dokumen berhasil dikirim'
                ];
            } else {
                Log::error('Failed to send WhatsApp document', [
                    'phone' => $phone,
                    'file' => $filePath,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Gagal mengirim dokumen: ' . $response->body(),
                    'status_code' => $response->status()
                ];
            }
        } catch (Exception $e) {
            Log::error('WhatsApp direct document error', [
                'phone' => $phone,
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Proses antrian pesan dari wa_outbox
     *
     * @param int $limit
     * @return array
     */
    public function processQueue($limit = 10)
    {
        try {
            $messages = WaOutbox::antrian()
                ->bySender(WaOutbox::SENDER_NODEJS)
                ->orderBy('tanggal_jam', 'asc')
                ->limit($limit)
                ->get();

            $processed = 0;
            $success = 0;
            $failed = 0;

            foreach ($messages as $message) {
                // Tandai sebagai sedang diproses
                $message->markAsProcessing();

                $result = null;
                if ($message->type === WaOutbox::TYPE_TEXT) {
                    $result = $this->sendDirectMessage($message->nowa, $message->pesan);
                } elseif (in_array($message->type, [WaOutbox::TYPE_DOCUMENT, WaOutbox::TYPE_IMAGE, WaOutbox::TYPE_VIDEO])) {
                    if ($message->file) {
                        $result = $this->sendDirectDocument($message->nowa, $message->file, $message->pesan);
                    } else {
                        $result = ['success' => false, 'error' => 'File path tidak ditemukan'];
                    }
                }

                if ($result && $result['success']) {
                    $message->markAsSent(json_encode($result['data'] ?? []));
                    $success++;
                } else {
                    $message->markAsFailed(json_encode($result ?? ['error' => 'Unknown error']));
                    $failed++;
                }

                $processed++;
            }

            return [
                'success' => true,
                'data' => [
                    'processed' => $processed,
                    'success' => $success,
                    'failed' => $failed
                ],
                'message' => "Berhasil memproses {$processed} pesan ({$success} berhasil, {$failed} gagal)"
            ];
        } catch (Exception $e) {
            Log::error('Queue processing error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Gagal memproses antrian: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dapatkan statistik antrian
     *
     * @return array
     */
    public function getQueueStats()
    {
        try {
            return [
                'success' => true,
                'data' => WaOutbox::getStats()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Gagal mendapatkan statistik: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format nomor telepon untuk WhatsApp (tanpa @c.us)
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber($phone)
    {
        // Hapus karakter non-numerik
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Hapus awalan 0 dan ganti dengan kode negara Indonesia (62)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan kode negara Indonesia
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}