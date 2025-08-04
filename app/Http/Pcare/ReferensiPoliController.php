<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use LZCompressor\LZString;
use Illuminate\Support\Facades\DB;

class ReferensiPoliController extends Controller
{
    protected $config;
    protected $client;

    public function __construct()
    {
        $this->config = [
            'base_url' => 'https://apijkn.bpjs-kesehatan.go.id/antreanfktp',
            'cons_id' => env('MOBILEJKN_CONS_ID', '7925'),
            'secret_key' => env('MOBILEJKN_SECRET_KEY', '2eF2C8E837'),
            'user_key' => env('MOBILEJKN_USER_KEY', 'e0fc15a6c8f737a8c46d9072e63b6102')
        ];

        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => 30,
            'verify' => false
        ]);

        Log::info('ReferensiPoliController initialized', [
            'base_url' => $this->config['base_url']
        ]);
    }

    public function index()
    {
        return view('Pcare.referensi.referensi-poli');
    }

    protected function generateSignature($timestamp)
    {
        $data = $this->config['cons_id'] . "&" . $timestamp;
        $signature = hash_hmac('sha256', $data, $this->config['secret_key'], true);
        return base64_encode($signature);
    }

    protected function stringDecrypt($key, $string)
    {
        try {
            Log::info('Memulai proses dekripsi', [
                'key_length' => strlen($key),
                'string_length' => strlen($string)
            ]);

            $encrypt_method = 'AES-256-CBC';
            $key_hash = hex2bin(hash('sha256', $key));
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

            Log::info('Parameter dekripsi', [
                'method' => $encrypt_method,
                'key_hash_length' => strlen($key_hash),
                'iv_length' => strlen($iv)
            ]);

            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
            
            if ($output === false) {
                $error = openssl_error_string();
                Log::error('Gagal dekripsi', ['openssl_error' => $error]);
                throw new Exception("Gagal mendekripsi data: " . $error);
            }

            Log::info('Dekripsi berhasil', [
                'output_length' => strlen($output)
            ]);

            return $output;
        } catch (Exception $e) {
            Log::error('Error dalam proses dekripsi:', [
                'error' => $e->getMessage(),
                'key_length' => strlen($key),
                'string_length' => strlen($string)
            ]);
            throw $e;
        }
    }

    protected function decompress($string)
    {
        try {
            Log::info('Memulai proses dekompresi', [
                'input_length' => strlen($string)
            ]);

            $result = LZString::decompressFromEncodedURIComponent($string);
            
            if (!$result) {
                throw new Exception("Gagal mendekompresi data");
            }

            Log::info('Dekompresi berhasil', [
                'result_length' => strlen($result)
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('Error dalam proses dekompresi:', [
                'error' => $e->getMessage(),
                'string_length' => strlen($string)
            ]);
            throw $e;
        }
    }

    public function getPoli(Request $request)
    {
        try {
            $maxRetries = 3;
            $attempt = 0;
            $waitTime = 1;

            while ($attempt < $maxRetries) {
                try {
                    $tanggal = $request->input('tanggal', date('Y-m-d'));
                    $timestamp = time();
                    
                    Log::info('Memulai request getPoli:', [
                        'tanggal' => $tanggal,
                        'timestamp' => $timestamp,
                        'attempt' => $attempt + 1
                    ]);
                    
                    $signature = $this->generateSignature($timestamp);
                    
                    $headers = [
                        'X-cons-id' => $this->config['cons_id'],
                        'X-timestamp' => $timestamp,
                        'X-signature' => $signature,
                        'user_key' => $this->config['user_key']
                    ];

                    $endpoint = "/antreanfktp/ref/poli/tanggal/{$tanggal}";
                    
                    Log::info('Request detail:', [
                        'url' => $this->config['base_url'] . $endpoint,
                        'headers' => array_merge($headers, ['X-signature' => '******'])
                    ]);

                    $response = $this->client->request('GET', $endpoint, [
                        'headers' => $headers
                    ]);

                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody()->getContents();
                    
                    Log::info('Response from BPJS:', [
                        'status_code' => $statusCode,
                        'response_length' => strlen($responseBody)
                    ]);

                    $result = json_decode($responseBody, true);
                    
                    if (!isset($result['response'])) {
                        throw new Exception('Invalid response format: response field not found');
                    }

                    // Dekripsi response
                    if (isset($result['response']) && is_string($result['response'])) {
                        $key = $this->config['cons_id'] . $this->config['secret_key'] . $timestamp;
                        $decryptedResponse = $this->stringDecrypt($key, $result['response']);
                        $decompressedResponse = $this->decompress($decryptedResponse);
                        $decodedResponse = json_decode($decompressedResponse, true);
                        
                        Log::info('Decoded response:', [
                            'response' => $decodedResponse
                        ]);

                        // Format data sesuai kebutuhan frontend
                        $finalData = [];
                        if (is_array($decodedResponse)) {
                            foreach ($decodedResponse as $poli) {
                                $finalData[] = [
                                    'kdPoli' => $poli['kodepoli'] ?? null,
                                    'nmPoli' => $poli['namapoli'] ?? null
                                ];
                            }
                        }

                        return response()->json([
                            'metadata' => [
                                'code' => 200,
                                'message' => 'OK'
                            ],
                            'response' => [
                                'list' => $finalData
                            ]
                        ]);
                    }

                    throw new Exception('Invalid response format: encrypted response not found');

                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                    Log::warning("Kesalahan koneksi pada percobaan " . ($attempt + 1), [
                        'error' => $e->getMessage()
                    ]);
                    
                    if ($attempt == $maxRetries - 1) {
                        throw $e;
                    }
                    
                    sleep($waitTime);
                    $waitTime *= 2;
                    $attempt++;
                    continue;
                }
            }
        } catch (Exception $e) {
            Log::error('Error dalam getPoli:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => [
                    'list' => []
                ]
            ], 200); // Return 200 dengan list kosong
        }
    }

    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export
        return response()->json(['message' => 'Fitur export Excel akan segera tersedia']);
    }

    public function exportPdf(Request $request)
    {
        // TODO: Implement PDF export
        return response()->json(['message' => 'Fitur export PDF akan segera tersedia']);
    }
}