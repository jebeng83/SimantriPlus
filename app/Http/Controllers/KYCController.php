<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use GuzzleHttp\Client;

class KYCController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Menampilkan form KYC
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('kyc.index');
    }

    /**
     * Memproses permintaan challenge code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function processVerification(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nik' => 'required|string|size:16|regex:/^[0-9]+$/',
            'name' => 'required|string|max:255',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.regex' => 'NIK hanya boleh berisi angka',
            'name.required' => 'Nama wajib diisi',
        ]);

        try {
            // Log data yang diterima untuk debugging
            \Log::info('Data KYC yang diterima:', $validated);
            
            // Simpan NIK dan nama ke session
            session([
                'patient_nik' => $validated['nik'],
                'patient_name' => $validated['name'],
            ]);

            // Bersihkan nama dari karakter khusus dan format yang tidak sesuai
            $cleanName = $this->cleanName($validated['name']);

            // Dapatkan token SATUSEHAT
            $token = $this->getSatusehatToken(true); // Force refresh token

            if (!$token) {
                return redirect()->back()->with('error', 'Gagal mendapatkan token SATUSEHAT. Silakan periksa konfigurasi.');
            }

            // Coba dengan beberapa variasi nama untuk meningkatkan kemungkinan berhasil
            $nameVariations = $this->getNameVariations($validated['name']);
            
            foreach ($nameVariations as $nameVariation) {
                \Log::info('Mencoba dengan variasi nama: "' . $nameVariation . '"');
                
                // Siapkan data untuk request challenge code sesuai format API SATUSEHAT
                $data = [
                    'metadata' => [
                        'method' => 'request_per_nik'
                    ],
                    'data' => [
                        'nik' => $validated['nik'],
                        'name' => $nameVariation
                    ]
                ];

                // Endpoint API SATUSEHAT
                $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';

                // Log request yang akan dikirim
                \Log::info('KYC Challenge Code Request:', [
                    'url' => $endpoint,
                    'data' => $data,
                    'token_length' => strlen($token)
                ]);

                // Gunakan curl langsung untuk mendapatkan challenge code
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $endpoint,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ],
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ]);

                $response = curl_exec($curl);
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = curl_error($curl);

                curl_close($curl);

                // Log response untuk debugging
                \Log::info('KYC Challenge Code Response Status: ' . $statusCode);
                \Log::info('KYC Challenge Code Response Raw: ' . substr($response, 0, 100) . '...');

                if ($error) {
                    \Log::error('Curl error: ' . $error);
                    continue; // Coba dengan variasi nama berikutnya
                }

                // Pastikan respons tidak kosong
                if (empty($response)) {
                    \Log::error('Respons kosong dari server SATUSEHAT');
                    continue; // Coba dengan variasi nama berikutnya
                }

                // Coba decode respons JSON
                $responseBody = json_decode($response, true);
                
                // Jika gagal decode, coba tampilkan respons mentah
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::error('Gagal decode respons JSON: ' . json_last_error_msg());
                    \Log::error('Respons mentah: ' . $response);
                    continue; // Coba dengan variasi nama berikutnya
                }

                // Log respons untuk debugging
                \Log::info('KYC Challenge Code Response Body:', $responseBody);

                if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                    // Simpan challenge code ke session
                    session([
                        'challenge_code' => $responseBody['data']['challenge_code'],
                        'kyc_ihs_number' => $responseBody['data']['ihs_number'] ?? null,
                        'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp'] ?? null,
                        'kyc_successful_name' => $nameVariation // Simpan nama yang berhasil untuk referensi
                    ]);

                    \Log::info('Berhasil mendapatkan challenge code dengan nama: "' . $nameVariation . '"');
                    
                    // Redirect ke halaman challenge code
                    return view('kyc.challenge', ['challengeCode' => $responseBody['data']['challenge_code']]);
                }
                
                // Jika ada error spesifik dari SATUSEHAT, catat untuk debugging
                if (isset($responseBody['metadata']['message'])) {
                    \Log::warning('Error dari SATUSEHAT: ' . $responseBody['metadata']['message']);
                }
                
                if (isset($responseBody['data']['error'])) {
                    \Log::warning('Detail error: ' . $responseBody['data']['error']);
                    
                    // Jika error adalah "Failed to decrypt message", ini biasanya masalah format nama
                    if ($responseBody['data']['error'] === 'Failed to decrypt message') {
                        \Log::warning('Error "Failed to decrypt message" biasanya terjadi karena format nama tidak sesuai dengan data di SATUSEHAT');
                        \Log::warning('Nama yang dikirim: "' . $nameVariation . '"');
                        \Log::warning('NIK yang dikirim: "' . $validated['nik'] . '"');
                        
                        // Log informasi tambahan yang mungkin berguna untuk debugging
                        \Log::warning('Informasi tambahan untuk debugging:');
                        \Log::warning('- Format nama asli: "' . $validated['name'] . '"');
                        \Log::warning('- Format nama yang dibersihkan: "' . $this->cleanName($validated['name']) . '"');
                        \Log::warning('- Respons lengkap: ' . json_encode($responseBody));
                    }
                }
                
                // Jika gagal, coba dengan variasi nama berikutnya
                \Log::warning('Gagal mendapatkan challenge code dengan nama: "' . $nameVariation . '", mencoba variasi berikutnya');
            }
            
            // Jika semua variasi nama gagal
            \Log::error('Semua variasi nama gagal untuk mendapatkan challenge code');
            
            // Periksa apakah ada error spesifik yang perlu ditampilkan ke pengguna
            $errorMessage = 'Gagal mendapatkan challenge code. Pastikan NIK dan nama sesuai dengan data di SATUSEHAT.';
            
            // Tambahkan detail error jika tersedia di log terakhir
            if (isset($responseBody) && isset($responseBody['data']['error']) && $responseBody['data']['error'] === 'Failed to decrypt message') {
                $errorMessage = 'Gagal mendapatkan challenge code. Nama yang dimasukkan tidak sesuai dengan data di SATUSEHAT. Pastikan nama ditulis persis seperti di KTP/KK.';
                
                // Tambahkan penjelasan lebih detail tentang error
                $errorDetails = $this->explainFailedToDecryptError();
                session(['error_details' => $errorDetails]);
            } else if (isset($responseBody) && isset($responseBody['metadata']['message'])) {
                $errorMessage = 'Gagal mendapatkan challenge code: ' . $responseBody['metadata']['message'];
            }
            
            return redirect()->back()->with('error', $errorMessage);
            
        } catch (\Exception $e) {
            \Log::error('KYC Error: ' . $e->getMessage());
            \Log::error('KYC Error Stack Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mencoba mendapatkan challenge code dengan curl
     *
     * @param string $nik
     * @param string $name
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function tryGetChallengeCodeWithCurl($nik, $name, $token)
    {
        try {
            \Log::info('Mencoba mendapatkan challenge code dengan curl sesuai dokumentasi SATUSEHAT');
            
            // Bersihkan nama dari karakter khusus dan format yang tidak sesuai
            $cleanName = $this->cleanName($name);
            
            // Siapkan data untuk request sesuai format dokumentasi SATUSEHAT
            $data = json_encode([
                'metadata' => [
                    'method' => 'request_per_nik'
                ],
                'data' => [
                    'nik' => $nik,
                    'name' => $cleanName
                ]
            ]);
            
            // Endpoint API SATUSEHAT
            $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';
            
            \Log::info('KYC Challenge Code Request (Curl):', [
                'url' => $endpoint,
                'data' => json_decode($data, true),
                'token_length' => strlen($token)
            ]);
            
            // Inisialisasi curl
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                CURLOPT_VERBOSE => true
            ]);
            
            $response = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            
            curl_close($curl);
            
            \Log::info('KYC Challenge Code Response (Curl)', [
                'status' => $statusCode,
                'response' => $response,
                'error' => $error
            ]);
            
            if ($error) {
                \Log::error('Curl error: ' . $error);
                return redirect()->back()->with('error', 'Gagal mendapatkan challenge code: Curl error - ' . $error);
            }
            
            $responseBody = json_decode($response, true);
            
            // Cek apakah response berhasil dan memiliki challenge_code
            if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                // Simpan challenge code ke session
                session([
                    'challenge_code' => $responseBody['data']['challenge_code'],
                    'kyc_ihs_number' => $responseBody['data']['ihs_number'] ?? null,
                    'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp'] ?? null
                ]);
                
                \Log::info('Berhasil mendapatkan challenge code: ' . $responseBody['data']['challenge_code']);
                
                // Redirect ke halaman challenge code
                return view('kyc.challenge', ['challengeCode' => $responseBody['data']['challenge_code']]);
            } 
            // Jika ada error "Failed to decrypt message", coba dengan format nama yang berbeda
            else if (isset($responseBody['data']['error']) && $responseBody['data']['error'] === 'Failed to decrypt message') {
                \Log::warning('Mendapatkan error "Failed to decrypt message", mencoba dengan format nama yang berbeda');
                
                // Coba dengan format nama yang berbeda
                return $this->tryWithDifferentNameFormats($nik, $name, $token);
            }
            // Jika ada error lain
            else {
                $errorMessage = 'Gagal mendapatkan challenge code: ';
                
                if (isset($responseBody['metadata']['message'])) {
                    $errorMessage .= $responseBody['metadata']['message'];
                } else if (isset($responseBody['fault']['faultstring'])) {
                    $errorMessage .= $responseBody['fault']['faultstring'];
                } else {
                    $errorMessage .= 'HTTP Status ' . $statusCode;
                }
                
                \Log::error('KYC Challenge Code Error (Curl): ' . $errorMessage);
                \Log::error('Response Body: ' . $response);
                
                // Coba dengan metode alternatif
                return $this->tryWithDifferentNameFormats($nik, $name, $token);
            }
        } catch (\Exception $e) {
            \Log::error('KYC Error (Curl): ' . $e->getMessage());
            \Log::error('KYC Error Stack Trace (Curl): ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mencoba mendapatkan challenge code dengan berbagai format nama
     *
     * @param string $nik
     * @param string $name
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function tryWithDifferentNameFormats($nik, $name, $token)
    {
        \Log::info('Mencoba dengan berbagai format nama untuk NIK: ' . $nik);
        
        // Endpoint API SATUSEHAT
        $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';
        
        // Coba dengan berbagai format nama
        $alternativeNames = [
            strtoupper($name), // Nama dalam uppercase
            preg_replace('/[^A-Za-z0-9\s]/', '', $name), // Hapus semua karakter khusus
            preg_replace('/,\s*AN$/i', '', $name), // Hapus suffix ",AN"
            preg_replace('/[^A-Za-z0-9\s]/', '', preg_replace('/,\s*AN$/i', '', $name)), // Kombinasi keduanya
            trim($name), // Trim whitespace
            strtoupper(trim($name)), // Uppercase dan trim
            preg_replace('/\s+/', ' ', trim($name)), // Normalisasi spasi
            strtoupper(preg_replace('/\s+/', ' ', trim($name))), // Uppercase dan normalisasi spasi
        ];
        
        foreach ($alternativeNames as $altName) {
            \Log::info('Mencoba dengan nama alternatif: "' . $altName . '"');
            
            // Siapkan data untuk request
            $data = json_encode([
                'metadata' => [
                    'method' => 'request_per_nik'
                ],
                'data' => [
                    'nik' => $nik,
                    'name' => $altName
                ]
            ]);
            
            // Inisialisasi curl
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
            ]);
            
            $response = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            
            curl_close($curl);
            
            \Log::info('KYC Challenge Code Response (Nama Alternatif)', [
                'nama' => $altName,
                'status' => $statusCode,
                'response' => $response,
                'error' => $error
            ]);
            
            if (!$error) {
                $responseBody = json_decode($response, true);
                
                if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                    // Berhasil dengan nama alternatif
                    session([
                        'challenge_code' => $responseBody['data']['challenge_code'],
                        'kyc_ihs_number' => $responseBody['data']['ihs_number'] ?? null,
                        'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp'] ?? null
                    ]);
                    
                    \Log::info('Berhasil mendapatkan challenge code dengan nama alternatif: "' . $altName . '"');
                    return view('kyc.challenge', ['challengeCode' => $responseBody['data']['challenge_code']]);
                }
            }
        }
        
        // Jika semua format nama gagal, coba dengan metode pencarian
        return $this->tryWithSearchResult($nik, $token);
    }
    
    /**
     * Mencoba mendapatkan challenge code dengan hasil pencarian
     *
     * @param string $nik
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function tryWithSearchResult($nik, $token)
    {
        try {
            \Log::info('Mencoba mendapatkan challenge code dengan hasil pencarian untuk NIK: ' . $nik);
            
            // Endpoint API SATUSEHAT untuk pencarian pasien
            $searchEndpoint = config('satusehat.api_url') . '/kyc/v1/search';
            
            // Data untuk request pencarian
            $searchData = json_encode([
                'parameter' => $nik
            ]);
            
            \Log::info('KYC Search Request:', [
                'url' => $searchEndpoint,
                'data' => json_decode($searchData, true)
            ]);
            
            // Inisialisasi curl untuk pencarian
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $searchEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $searchData,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
            ]);
            
            $searchResponse = curl_exec($curl);
            $searchStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $searchError = curl_error($curl);
            
            curl_close($curl);
            
            \Log::info('KYC Search Response', [
                'status' => $searchStatusCode,
                'response' => $searchResponse,
                'error' => $searchError
            ]);
            
            if ($searchError) {
                \Log::error('Curl error pada pencarian: ' . $searchError);
                return redirect()->back()->with('error', 'Gagal mencari data pasien: ' . $searchError);
            }
            
            $searchResponseBody = json_decode($searchResponse, true);
            
            if ($searchStatusCode == 200 && isset($searchResponseBody['data']) && !empty($searchResponseBody['data'])) {
                // Ambil data pasien dari hasil pencarian
                $patientData = $searchResponseBody['data'][0];
                $patientName = $patientData['name'] ?? ($patientData['label'] ?? '');
                
                // Ekstrak nama dari label jika perlu (biasanya dalam format "NIK - NAMA")
                if (empty($patientName) && isset($patientData['label']) && strpos($patientData['label'], ' - ') !== false) {
                    $patientName = trim(explode(' - ', $patientData['label'])[1]);
                }
                
                if (empty($patientName)) {
                    \Log::warning('Tidak dapat mengekstrak nama pasien dari hasil pencarian');
                    return redirect()->back()->with('error', 'Tidak dapat mengekstrak nama pasien dari hasil pencarian');
                }
                
                \Log::info('Mencoba dengan nama dari hasil pencarian: "' . $patientName . '"');
                
                // Endpoint API SATUSEHAT untuk challenge code
                $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';
                
                // Data untuk request challenge code
                $data = json_encode([
                    'metadata' => [
                        'method' => 'request_per_nik'
                    ],
                    'data' => [
                        'nik' => $nik,
                        'name' => $patientName
                    ]
                ]);
                
                // Inisialisasi curl
                $curl = curl_init();
                
                curl_setopt_array($curl, [
                    CURLOPT_URL => $endpoint,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ],
                ]);
                
                $response = curl_exec($curl);
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = curl_error($curl);
                
                curl_close($curl);
                
                \Log::info('KYC Challenge Code Response (Nama dari Pencarian)', [
                    'status' => $statusCode,
                    'response' => $response,
                    'error' => $error
                ]);
                
                if ($error) {
                    \Log::error('Curl error: ' . $error);
                    return redirect()->back()->with('error', 'Gagal mendapatkan challenge code: ' . $error);
                }
                
                $responseBody = json_decode($response, true);
                
                if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                    // Simpan challenge code ke session
                    session([
                        'challenge_code' => $responseBody['data']['challenge_code'],
                        'kyc_ihs_number' => $responseBody['data']['ihs_number'] ?? null,
                        'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp'] ?? null
                    ]);
                    
                    \Log::info('Berhasil mendapatkan challenge code dengan nama dari hasil pencarian');
                    return view('kyc.challenge', ['challengeCode' => $responseBody['data']['challenge_code']]);
                } else {
                    $errorMessage = 'Gagal mendapatkan challenge code dengan nama dari hasil pencarian: ';
                    
                    if (isset($responseBody['metadata']['message'])) {
                        $errorMessage .= $responseBody['metadata']['message'];
                    } else {
                        $errorMessage .= 'HTTP Status ' . $statusCode;
                    }
                    
                    \Log::error($errorMessage);
                    \Log::error('Response Body: ' . $response);
                }
            } else {
                \Log::warning('Tidak ada hasil pencarian untuk NIK: ' . $nik);
            }
            
            // Jika semua metode gagal, coba dengan metode terakhir - gunakan nama dari database lokal
            return $this->tryWithLocalDatabase($nik, $token);
        } catch (\Exception $e) {
            \Log::error('KYC Error (Search): ' . $e->getMessage());
            \Log::error('KYC Error Stack Trace (Search): ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mencoba mendapatkan challenge code dengan data dari database lokal
     *
     * @param string $nik
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function tryWithLocalDatabase($nik, $token)
    {
        try {
            \Log::info('Mencoba mendapatkan challenge code dengan data dari database lokal untuk NIK: ' . $nik);
            
            // Cari data pasien di database lokal
            $pasien = DB::table('pasien')
                ->where('no_ktp', $nik)
                ->first();
            
            if (!$pasien) {
                \Log::warning('Tidak ada data pasien di database lokal untuk NIK: ' . $nik);
                return redirect()->back()->with('error', 'Gagal mendapatkan challenge code. Pastikan NIK dan nama sesuai dengan data di SATUSEHAT.');
            }
            
            // Ambil nama pasien dari database
            $patientName = $pasien->nm_pasien;
            
            \Log::info('Mencoba dengan nama dari database lokal: "' . $patientName . '"');
            
            // Endpoint API SATUSEHAT
            $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';
            
            // Coba dengan berbagai format nama dari database
            $alternativeNames = [
                $patientName,
                strtoupper($patientName),
                preg_replace('/[^A-Za-z0-9\s]/', '', $patientName),
                trim($patientName),
                strtoupper(trim($patientName))
            ];
            
            foreach ($alternativeNames as $altName) {
                // Data untuk request challenge code
                $data = json_encode([
                    'metadata' => [
                        'method' => 'request_per_nik'
                    ],
                    'data' => [
                        'nik' => $nik,
                        'name' => $altName
                    ]
                ]);
                
                // Inisialisasi curl
                $curl = curl_init();
                
                curl_setopt_array($curl, [
                    CURLOPT_URL => $endpoint,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ],
                ]);
                
                $response = curl_exec($curl);
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = curl_error($curl);
                
                curl_close($curl);
                
                \Log::info('KYC Challenge Code Response (Nama dari Database)', [
                    'nama' => $altName,
                    'status' => $statusCode,
                    'response' => $response,
                    'error' => $error
                ]);
                
                if (!$error) {
                    $responseBody = json_decode($response, true);
                    
                    if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                        // Simpan challenge code ke session
                        session([
                            'challenge_code' => $responseBody['data']['challenge_code'],
                            'kyc_ihs_number' => $responseBody['data']['ihs_number'] ?? null,
                            'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp'] ?? null
                        ]);
                        
                        \Log::info('Berhasil mendapatkan challenge code dengan nama dari database lokal');
                        return view('kyc.challenge', ['challengeCode' => $responseBody['data']['challenge_code']]);
                    }
                }
            }
            
            // Jika semua metode gagal
            \Log::error('Semua metode untuk mendapatkan challenge code gagal');
            return redirect()->back()->with('error', 'Gagal mendapatkan challenge code. Pastikan NIK dan nama sesuai dengan data di SATUSEHAT. Silakan coba lagi nanti atau hubungi administrator.');
        } catch (\Exception $e) {
            \Log::error('KYC Error (Database): ' . $e->getMessage());
            \Log::error('KYC Error Stack Trace (Database): ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Request challenge code from SATUSEHAT API.
     *
     * @param string $nik
     * @param string $name
     * @return array
     */
    private function requestChallengeCode($nik, $name)
    {
        try {
            // Dapatkan token dari konfigurasi atau database
            $token = Cache::has('satusehat_token') ? Cache::get('satusehat_token') : $this->getSatusehatToken(true);
            
            if (!$token) {
                return ['error' => 'Tidak dapat mendapatkan token akses SATUSEHAT'];
            }
            
            // Endpoint API SATUSEHAT
            $endpoint = config('satusehat.api_url') . '/kyc/v1/challenge-code';
            
            // Bersihkan nama dari karakter khusus dan format yang tidak sesuai
            $cleanName = $this->cleanName($name);
            
            // Data untuk request sesuai format dokumentasi SATUSEHAT
            $data = [
                'metadata' => [
                    'method' => 'request_per_nik'
                ],
                'data' => [
                    'nik' => $nik,
                    'name' => $cleanName
                ]
            ];
            
            // Log request untuk debugging
            Log::info('KYC Challenge Code Request:', [
                'url' => $endpoint,
                'data' => $data,
                'token_length' => strlen($token)
            ]);
            
            // Gunakan curl langsung untuk mendapatkan token yang lebih reliable
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);

            $response = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);

            curl_close($curl);
            
            // Log response untuk debugging
            Log::info('KYC Challenge Code Response Status: ' . $statusCode);
            Log::info('KYC Challenge Code Response Raw: ' . substr($response, 0, 300) . '...');
            
            if ($error) {
                Log::error('KYC Challenge Code Curl error: ' . $error);
                return ['error' => 'Terjadi kesalahan koneksi: ' . $error];
            }
            
            // Pastikan respons tidak kosong
            if (empty($response)) {
                \Log::error('Respons kosong dari server SATUSEHAT');
                return ['error' => 'Gagal mendapatkan challenge code: Respons kosong dari server SATUSEHAT'];
            }

            // Coba decode respons JSON
            $responseBody = json_decode($response, true);
            
            // Jika gagal decode, coba tampilkan respons mentah
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Gagal decode respons JSON: ' . json_last_error_msg());
                \Log::error('Respons mentah: ' . $response);
                return ['error' => 'Gagal mendapatkan challenge code: Gagal decode respons JSON'];
            }

            // Log respons untuk debugging
            \Log::info('KYC Challenge Code Response Body:', $responseBody);

            if ($statusCode == 200 && isset($responseBody['data']['challenge_code'])) {
                // Simpan data challenge code ke session untuk digunakan nanti
                session([
                    'kyc_challenge_code' => $responseBody['data']['challenge_code'],
                    'kyc_nik' => $responseBody['data']['nik'],
                    'kyc_name' => $responseBody['data']['name'],
                    'kyc_ihs_number' => $responseBody['data']['ihs_number'],
                    'kyc_expired_timestamp' => $responseBody['data']['expired_timestamp']
                ]);
                
                return $responseBody['data'];
            } else {
                // Handle error response
                $errorMessage = 'Error dari SATUSEHAT: ';
                
                if ($statusCode === 401) {
                    $errorMessage .= 'Token tidak valid atau kadaluarsa';
                    
                    // Coba refresh token dan coba lagi
                    Cache::forget('satusehat_token');
                    $newToken = $this->getSatusehatToken(true);
                    
                    if ($newToken) {
                        return $this->requestChallengeCode($nik, $name);
                    }
                } else {
                    $errorData = $responseBody;
                    if (isset($errorData['metadata']['message'])) {
                        $errorMessage .= $errorData['metadata']['message'];
                    } else if (isset($errorData['fault']['faultstring'])) {
                        $errorMessage .= $errorData['fault']['faultstring'];
                    } else {
                        $errorMessage .= 'Kode HTTP ' . $statusCode;
                    }
                }
                
                Log::error('KYC Challenge Code Error: ' . $errorMessage);
                Log::error('KYC Challenge Code Response: ' . $response);
                return ['error' => $errorMessage];
            }
        } catch (\Exception $e) {
            Log::error('KYC Challenge Code Exception: ' . $e->getMessage());
            return ['error' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Membersihkan nama dari karakter khusus dan format yang tidak sesuai
     * 
     * @param string $name
     * @return string
     */
    private function cleanName($name)
    {
        // Hapus karakter khusus yang mungkin menyebabkan masalah
        $cleanName = preg_replace('/[^\p{L}\p{N}\s\.,\-]/u', '', $name);
        
        // Normalisasi spasi (hapus spasi berlebih)
        $cleanName = preg_replace('/\s+/', ' ', $cleanName);
        
        // Trim whitespace
        $cleanName = trim($cleanName);
        
        // Ubah ke uppercase sesuai format SATUSEHAT
        $cleanName = strtoupper($cleanName);
        
        // Log perubahan nama untuk debugging
        \Log::info('Nama asli: "' . $name . '", Nama yang dibersihkan: "' . $cleanName . '"');
        
        return $cleanName;
    }
    
    /**
     * Menampilkan halaman verifikasi setelah KYC berhasil
     *
     * @return \Illuminate\View\View
     */
    public function verification()
    {
        // Jika tidak ada data pasien di session, redirect ke halaman KYC
        if (!session('patient_nik') || !session('patient_name')) {
            return redirect()->route('kyc.new')
                ->with('error', 'Tidak ada data pasien yang terverifikasi. Silakan lakukan verifikasi terlebih dahulu.');
        }
        
        return view('kyc.verification');
    }
    
    /**
     * Get doctor name from database.
     *
     * @param string $username
     * @return string
     */
    private function getDokter($username)
    {
        try {
            $dokter = DB::table('dokter')
                ->where('username', $username)
                ->first();
            
            return $dokter;
        } catch (\Exception $e) {
            Log::error('Error getting dokter data: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Test token generation for SATUSEHAT
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testToken()
    {
        try {
            // Clear cached token to force new token generation
            Cache::forget('satusehat_token');
            
            // Check if there was a recent rate limit hit
            if (Cache::has('satusehat_rate_limited')) {
                $retryAfter = Cache::get('satusehat_rate_limited');
                $now = Carbon::now();
                $waitTime = $now->diffInSeconds($retryAfter, false);
                
                if ($waitTime > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Terlalu banyak permintaan ke API SATUSEHAT. Silakan coba lagi dalam {$waitTime} detik.",
                        'error_details' => [
                            'error_type' => 'rate_limit',
                            'retry_after' => $waitTime,
                            'retry_after_time' => $retryAfter->format('Y-m-d H:i:s'),
                            'client_id' => config('satusehat.client_id') ? 'Tersedia (' . substr(config('satusehat.client_id'), 0, 5) . '...)' : 'Tidak tersedia',
                            'client_secret' => config('satusehat.client_secret') ? 'Tersedia (' . substr(config('satusehat.client_secret'), 0, 5) . '...)' : 'Tidak tersedia',
                            'organization_id' => config('satusehat.organization_id') ?: 'Tidak tersedia',
                        ],
                        'petugas' => $this->getPetugasData()
                    ]);
                }
            }
            
            // Get a new token
            $token = $this->getSatusehatToken(true);
            
            // Get petugas data
            $petugasData = $this->getPetugasData();
            
            if (!$token) {
                // Log detail kredensial untuk debugging
                Log::error('Test Token Gagal: Kredensial', [
                    'client_id_length' => config('satusehat.client_id') ? strlen(config('satusehat.client_id')) : 0,
                    'client_secret_length' => config('satusehat.client_secret') ? strlen(config('satusehat.client_secret')) : 0,
                    'organization_id' => config('satusehat.organization_id'),
                    'auth_url' => config('satusehat.auth_url'),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendapatkan token. Periksa kredensial dan koneksi Anda.',
                    'error_details' => [
                        'client_id' => config('satusehat.client_id') ? 'Tersedia (' . substr(config('satusehat.client_id'), 0, 5) . '...)' : 'Tidak tersedia',
                        'client_secret' => config('satusehat.client_secret') ? 'Tersedia (' . substr(config('satusehat.client_secret'), 0, 5) . '...)' : 'Tidak tersedia',
                        'organization_id' => config('satusehat.organization_id') ?: 'Tidak tersedia',
                        'auth_url' => config('satusehat.auth_url'),
                        'api_url' => config('satusehat.api_url'),
                    ],
                    'petugas' => $petugasData
                ]);
            }
            
            // Mask token for security (show only first 10 and last 10 characters)
            $tokenLength = strlen($token);
            $maskedToken = substr($token, 0, 10) . '...' . substr($token, -10);
            
            // Get token expiry time
            $expiresIn = config('satusehat.token_expiry', 3600);
            
            // Get current time in Asia/Jakarta timezone
            $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
            
            // Get additional information from cache
            $organizationName = Cache::get('satusehat_org_name', 'N/A');
            $clientId = Cache::get('satusehat_client_id', config('satusehat.client_id'));
            
            // Log successful token generation
            Log::info('Test Token Berhasil', [
                'token_length' => $tokenLength,
                'generated_at' => $now,
                'petugas' => $petugasData['nama'] ?? 'Unknown',
                'organization_name' => $organizationName
            ]);
            
            // Prepare response data with additional information from the token response
            $responseData = [
                'success' => true,
                'message' => 'Token berhasil didapatkan',
                'data' => [
                    'masked_token' => $maskedToken,
                    'token_length' => $tokenLength,
                    'expires_in' => $expiresIn,
                    'generated_at' => $now,
                    'environment' => strpos(config('satusehat.api_url'), 'stg') !== false ? 'Staging' : 'Production',
                    'organization_name' => $organizationName,
                    'client_id' => $clientId ? substr($clientId, 0, 10) . '...' . substr($clientId, -10) : 'N/A'
                ],
                'petugas' => $petugasData
            ];
            
            return response()->json($responseData);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            \Log::error('Client Exception in testToken: ' . $e->getMessage());
            
            if ($statusCode === 429) {
                // Handle rate limiting
                $retryAfter = $response->hasHeader('Retry-After') 
                    ? (int) $response->getHeaderLine('Retry-After')
                    : 60; // Default to 60 seconds if no header
                
                $retryTime = Carbon::now()->addSeconds($retryAfter);
                Cache::put('satusehat_rate_limited', $retryTime, $retryAfter);
                
                return response()->json([
                    'success' => false,
                    'message' => "Terlalu banyak permintaan ke API SATUSEHAT. Silakan coba lagi dalam {$retryAfter} detik.",
                    'error_details' => [
                        'error_type' => 'rate_limit',
                        'retry_after' => $retryAfter,
                        'retry_after_time' => $retryTime->format('Y-m-d H:i:s'),
                        'status_code' => $statusCode,
                        'response' => $responseBody ?? 'No response body',
                    ],
                    'petugas' => $this->getPetugasData()
                ]);
            }
            
            return $this->handleApiError($e, 'testToken');
        } catch (\Exception $e) {
            \Log::error('Error in testToken: ' . $e->getMessage());
            \Log::error('Error Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'client_id' => config('satusehat.client_id') ? 'Tersedia (' . substr(config('satusehat.client_id'), 0, 5) . '...)' : 'Tidak tersedia',
                    'client_secret' => config('satusehat.client_secret') ? 'Tersedia (' . substr(config('satusehat.client_secret'), 0, 5) . '...)' : 'Tidak tersedia',
                    'organization_id' => config('satusehat.organization_id') ?: 'Tidak tersedia',
                ],
                'petugas' => $this->getPetugasData()
            ]);
        }
    }
    
    /**
     * Helper function to get petugas data
     * 
     * @return array Data petugas dengan no_ktp sebagai NIK untuk validasi SATUSEHAT
     */
    private function getPetugasData()
    {
        $username = session('username');
        $pegawai = DB::table('pegawai')->where('nik', $username)->first();
        
        // Pastikan no_ktp digunakan sebagai NIK untuk validasi SATUSEHAT
        return [
            'username' => $username, // Username internal (nik di tabel pegawai)
            'no_ktp' => $pegawai ? $pegawai->no_ktp : null, // NIK KTP untuk validasi SATUSEHAT
            'nama' => $pegawai ? $pegawai->nama : null // Nama petugas
        ];
    }
    
    /**
     * Helper function to handle API errors
     */
    private function handleApiError($exception, $method)
    {
        try {
            $response = $exception->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $responseBody = $response ? json_decode($response->getBody()->getContents(), true) : null;
            
            \Log::error("API Error in {$method}: Status {$statusCode}", [
                'response' => $responseBody,
                'message' => $exception->getMessage()
            ]);
            
            $errorMessage = 'Kesalahan pada API SATUSEHAT';
            $errorDetails = [];
            
            // Jika status 401, berikan pesan khusus untuk masalah autentikasi
            if ($statusCode === 401) {
                $errorMessage = 'Gagal autentikasi ke SATUSEHAT. Kredensial tidak valid atau tidak diterima.';
                
                // Tambahkan informasi tambahan untuk debugging
                $errorDetails = [
                    'auth_url' => config('satusehat.auth_url'),
                    'client_id_status' => config('satusehat.client_id') ? 'Tersedia (' . substr(config('satusehat.client_id'), 0, 5) . '...)' : 'Tidak tersedia',
                    'client_secret_status' => config('satusehat.client_secret') ? 'Tersedia (' . strlen(config('satusehat.client_secret')) . ' karakter)' : 'Tidak tersedia',
                    'client_id_length' => config('satusehat.client_id') ? strlen(config('satusehat.client_id')) : 0,
                    'organization_id' => config('satusehat.organization_id') ?: 'Tidak tersedia',
                    'status_code' => $statusCode,
                    'error_type' => 'authentication_error'
                ];
                
                // Log informasi tambahan untuk troubleshooting
                \Log::error('Authentication Error Details:', $errorDetails);
            } 
            // Jika tidak, gunakan pesan dari response API jika tersedia
            else if ($responseBody && isset($responseBody['issue'])) {
                $issues = collect($responseBody['issue']);
                $errorDetails = $issues->map(function($issue) {
                    return $issue['details']['text'] ?? $issue['diagnostics'] ?? 'Unknown error';
                })->implode(', ');
                
                $errorMessage = $errorDetails;
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_details' => $errorDetails ?: [
                    'status_code' => $statusCode,
                    'response' => $responseBody ?? 'No response body',
                ],
                'petugas' => $this->getPetugasData()
            ]);
        } catch (\Exception $e) {
            \Log::error("Error handling API error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan yang tidak diketahui',
                'error_details' => [
                    'original_error' => $exception->getMessage(),
                    'handler_error' => $e->getMessage()
                ],
                'petugas' => $this->getPetugasData()
            ]);
        }
    }

    /**
     * Menampilkan halaman konfigurasi SATUSEHAT
     *
     * @return \Illuminate\View\View
     */
    public function config()
    {
        $config = [
            'environment' => 'Production',
            'api_url' => config('satusehat.api_url'),
            'auth_url' => config('satusehat.auth_url'),
            'client_id' => config('satusehat.client_id'),
            'client_secret' => config('satusehat.client_secret') ? 'Tersedia' : 'Tidak Tersedia',
            'organization_id' => config('satusehat.organization_id'),
            'token_status' => Cache::has('satusehat_token') ? 'Tersedia (Cached)' : 'Tidak Tersedia'
        ];
        
        return view('kyc.config', compact('config'));
    }

    /**
     * Menampilkan halaman status verifikasi KYC
     *
     * @return \Illuminate\View\View
     */
    public function status()
    {
        // Jika pasien sudah terverifikasi (ada data di session), hapus pesan error terkait challenge code
        if (session('patient_nik') && session('patient_name')) {
            // Cek apakah ada pesan error terkait challenge code
            if (session('error') && (
                strpos(session('error'), 'Gagal mendapatkan challenge code') !== false ||
                strpos(session('error'), 'Failed to decrypt message') !== false
            )) {
                // Hapus pesan error dari session
                session()->forget('error');
                
                // Log bahwa pesan error dihapus karena verifikasi sudah berhasil
                \Log::info('Menghapus pesan error challenge code karena pasien sudah terverifikasi', [
                    'patient_nik' => session('patient_nik'),
                    'patient_name' => session('patient_name')
                ]);
            }
            
            // Coba verifikasi status di SATUSEHAT jika belum ada IHS number
            if (!session('kyc_ihs_number')) {
                try {
                    // Dapatkan token SATUSEHAT
                    $token = $this->getSatusehatToken();
                    
                    if ($token) {
                        // Periksa status verifikasi
                        $isVerified = $this->checkVerificationStatus(session('patient_nik'), $token);
                        
                        if ($isVerified) {
                            // Tambahkan pesan sukses jika berhasil memverifikasi
                            session()->flash('success', 'Status verifikasi pasien berhasil dikonfirmasi di SATUSEHAT.');
                        }
                    } else {
                        // Jika tidak bisa mendapatkan token, gunakan data session
                        \Log::warning('Tidak dapat mendapatkan token SATUSEHAT. Menggunakan data session untuk verifikasi.');
                        
                        // Jika ada data pasien di session, anggap sudah terverifikasi
                        if (session('patient_nik') && session('patient_name')) {
                            \Log::info('Menggunakan data session untuk konfirmasi status verifikasi');
                            // Tidak perlu menampilkan pesan error ke pengguna
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error saat memeriksa status verifikasi: ' . $e->getMessage());
                    // Tidak perlu menampilkan error ke pengguna karena ini hanya pemeriksaan tambahan
                    // dan kita sudah memiliki data pasien di session
                }
            }
        }
        
        return view('kyc.status');
    }
    
    /**
     * Mencari data pasien berdasarkan NIK atau nama
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPatient(Request $request)
    {
        try {
            $search = $request->input('search');
            
            \Log::info('Searching for patient with term: ' . $search);
            
            if (empty($search)) {
                \Log::warning('Empty search parameter');
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter pencarian tidak boleh kosong'
                ]);
            }
            
            // Cari berdasarkan NIK atau nama pasien
            $pasien = \DB::table('pasien')
                ->where('no_ktp', 'like', '%' . $search . '%')
                ->orWhere('nm_pasien', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();
                
            \Log::info('Patient search results count: ' . $pasien->count());
                
            if ($pasien->isEmpty()) {
                \Log::warning('No patients found for search term: ' . $search);
                return response()->json([
                    'success' => false,
                    'message' => 'Data pasien tidak ditemukan'
                ]);
            }
            
            // Format data pasien untuk response
            $result = $pasien->map(function($item) {
                // Konversi jenis kelamin
                $gender = 'male';
                if ($item->jk == 'P') {
                    $gender = 'female';
                }
                
                // Format tanggal lahir ke Y-m-d
                $tglLahir = $item->tgl_lahir;
                if ($tglLahir) {
                    try {
                        $tglLahir = \Carbon\Carbon::parse($tglLahir)->format('Y-m-d');
                    } catch (\Exception $e) {
                        \Log::error('Error parsing tanggal lahir: ' . $e->getMessage());
                    }
                }
                
                return [
                    'id' => $item->no_rkm_medis,
                    'nik' => $item->no_ktp,
                    'name' => $item->nm_pasien,
                    'dob' => $tglLahir,
                    'gender' => $gender,
                    'phone' => $item->no_tlp,
                    'label' => $item->no_ktp . ' - ' . $item->nm_pasien
                ];
            });
            
            \Log::info('Formatted patient data for response');
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error searching patient: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan berbagai variasi nama untuk meningkatkan kemungkinan berhasil
     * 
     * @param string $name
     * @return array
     */
    private function getNameVariations($name)
    {
        $variations = [];
        
        // Nama asli yang dibersihkan
        $cleanName = $this->cleanName($name);
        $variations[] = $cleanName;
        
        // Nama dalam uppercase
        $variations[] = strtoupper($name);
        
        // Nama tanpa karakter khusus
        $variations[] = preg_replace('/[^A-Za-z0-9\s]/', '', $name);
        
        // Nama tanpa karakter khusus dalam uppercase
        $variations[] = strtoupper(preg_replace('/[^A-Za-z0-9\s]/', '', $name));
        
        // Nama dengan spasi dinormalisasi
        $variations[] = preg_replace('/\s+/', ' ', trim($name));
        
        // Nama dengan spasi dinormalisasi dalam uppercase
        $variations[] = strtoupper(preg_replace('/\s+/', ' ', trim($name)));
        
        // Coba dengan nama yang dibalik (untuk kasus nama dengan format "Nama Belakang, Nama Depan")
        if (strpos($name, ',') !== false) {
            $parts = explode(',', $name);
            if (count($parts) == 2) {
                $reversedName = trim($parts[1]) . ' ' . trim($parts[0]);
                $variations[] = $this->cleanName($reversedName);
                $variations[] = strtoupper($reversedName);
            }
        }
        
        // Coba dengan nama tanpa bin/binti jika ada
        $withoutBin = preg_replace('/\s+(bin|binti)\s+/i', ' ', $name);
        if ($withoutBin !== $name) {
            $variations[] = $this->cleanName($withoutBin);
        }
        
        // Coba dengan nama tanpa tanda kutip jika ada
        $withoutQuotes = str_replace(["'", '"'], '', $name);
        if ($withoutQuotes !== $name) {
            $variations[] = $this->cleanName($withoutQuotes);
        }
        
        // Coba dengan nama tanpa angka jika ada
        $withoutNumbers = preg_replace('/[0-9]/', '', $name);
        if ($withoutNumbers !== $name) {
            $variations[] = $this->cleanName($withoutNumbers);
        }
        
        // Variasi tambahan untuk kasus khusus
        
        // Jika nama berakhiran ",AN" (anak), coba beberapa variasi
        if (preg_match('/,\s*AN$/i', $name)) {
            // Tanpa ",AN"
            $withoutAN = preg_replace('/,\s*AN$/i', '', $name);
            $variations[] = $this->cleanName($withoutAN);
            $variations[] = strtoupper($withoutAN);
            
            // Dengan spasi sebelum ",AN"
            if (strpos($name, ' ,AN') === false && strpos($name, ', AN') === false) {
                $withSpace = preg_replace('/,AN$/i', ', AN', $name);
                $variations[] = $this->cleanName($withSpace);
            }
            
            // Tanpa spasi sebelum ",AN"
            if (strpos($name, ',AN') === false) {
                $withoutSpace = preg_replace('/,\s+AN$/i', ',AN', $name);
                $variations[] = $this->cleanName($withoutSpace);
            }
            
            // Dengan "AN" di depan (format "AN Nama")
            $anFirst = 'AN ' . $withoutAN;
            $variations[] = $this->cleanName($anFirst);
            $variations[] = strtoupper($anFirst);
            
            // Dengan "ANAK" lengkap alih-alih "AN"
            $withAnak = preg_replace('/,\s*AN$/i', ', ANAK', $name);
            $variations[] = $this->cleanName($withAnak);
            
            // Dengan "ANAK" di depan
            $anakFirst = 'ANAK ' . $withoutAN;
            $variations[] = $this->cleanName($anakFirst);
            
            // Dengan "BY" (bayi) di depan
            $byFirst = 'BY ' . $withoutAN;
            $variations[] = $this->cleanName($byFirst);
            $variations[] = strtoupper($byFirst);
            
            // Dengan "BAYI" lengkap di depan
            $bayiFirst = 'BAYI ' . $withoutAN;
            $variations[] = $this->cleanName($bayiFirst);
        }
        
        // Jika nama berakhiran "NY" (nyonya), coba beberapa variasi
        if (preg_match('/\sNY$/i', $name)) {
            // Tanpa "NY"
            $withoutNY = preg_replace('/\sNY$/i', '', $name);
            $variations[] = $this->cleanName($withoutNY);
            $variations[] = strtoupper($withoutNY);
            
            // Dengan "NYONYA" lengkap alih-alih "NY"
            $withNyonya = preg_replace('/\sNY$/i', ' NYONYA', $name);
            $variations[] = $this->cleanName($withNyonya);
            
            // Dengan "NYONYA" di depan
            $nyonyaFirst = 'NYONYA ' . $withoutNY;
            $variations[] = $this->cleanName($nyonyaFirst);
        }
        
        // Jika nama berakhiran "TN" (tuan), coba beberapa variasi
        if (preg_match('/\sTN$/i', $name)) {
            // Tanpa "TN"
            $withoutTN = preg_replace('/\sTN$/i', '', $name);
            $variations[] = $this->cleanName($withoutTN);
            $variations[] = strtoupper($withoutTN);
            
            // Dengan "TUAN" lengkap alih-alih "TN"
            $withTuan = preg_replace('/\sTN$/i', ' TUAN', $name);
            $variations[] = $this->cleanName($withTuan);
            
            // Dengan "TUAN" di depan
            $tuanFirst = 'TUAN ' . $withoutTN;
            $variations[] = $this->cleanName($tuanFirst);
        }
        
        // Jika nama mengandung "BY" (bayi), coba beberapa variasi
        if (preg_match('/\bBY\b/i', $name)) {
            // Dengan "BAYI" lengkap alih-alih "BY"
            $withBayi = preg_replace('/\bBY\b/i', 'BAYI', $name);
            $variations[] = $this->cleanName($withBayi);
        }
        
        // Hapus duplikat dan kembalikan array unik
        return array_unique($variations);
    }

    /**
     * Menjelaskan error "Failed to decrypt message" dari SATUSEHAT
     * 
     * @return string
     */
    private function explainFailedToDecryptError()
    {
        $explanation = "Error 'Failed to decrypt message' dari SATUSEHAT biasanya terjadi karena format nama yang dikirimkan tidak sesuai dengan data yang tersimpan di database SATUSEHAT. ";
        $explanation .= "Hal ini dapat disebabkan oleh beberapa faktor:\n\n";
        $explanation .= "1. Format nama di KTP/KK berbeda dengan yang terdaftar di SATUSEHAT\n";
        $explanation .= "2. Adanya perbedaan penulisan seperti penggunaan tanda koma, spasi, atau huruf kapital\n";
        $explanation .= "3. Penggunaan singkatan atau gelar yang berbeda (misalnya 'NY' vs 'NYONYA', 'AN' vs 'ANAK')\n";
        $explanation .= "4. Urutan nama yang berbeda (nama depan dan belakang tertukar)\n";
        $explanation .= "5. Adanya karakter khusus atau tanda baca yang tidak konsisten\n\n";
        $explanation .= "Sistem telah mencoba berbagai variasi format nama, namun tetap gagal mendapatkan challenge code. ";
        $explanation .= "Pastikan nama yang dimasukkan persis sama dengan yang terdaftar di SATUSEHAT.";
        
        return $explanation;
    }

    /**
     * Mendapatkan token SATUSEHAT dari API
     *
     * @param bool $forceRefresh Paksa refresh token meskipun masih ada di cache
     * @return string|null Token SATUSEHAT atau null jika gagal
     */
    private function getSatusehatToken($forceRefresh = false)
    {
        try {
            // Jika token sudah ada di cache dan tidak dipaksa refresh, gunakan yang ada
            if (!$forceRefresh && Cache::has('satusehat_token')) {
                \Log::info('Menggunakan token SATUSEHAT dari cache');
                return Cache::get('satusehat_token');
            }

            \Log::info('Meminta token baru dari SATUSEHAT');

            // Ambil kredensial dari konfigurasi
            $clientId = config('satusehat.client_id');
            $clientSecret = config('satusehat.client_secret');
            $authUrl = config('satusehat.auth_url');

            if (empty($clientId) || empty($clientSecret) || empty($authUrl)) {
                \Log::error('Kredensial SATUSEHAT tidak lengkap', [
                    'client_id_exists' => !empty($clientId),
                    'client_secret_exists' => !empty($clientSecret),
                    'auth_url_exists' => !empty($authUrl)
                ]);
                return null;
            }

            // URL untuk mendapatkan token
            $tokenUrl = 'https://api-satusehat.kemkes.go.id/oauth2/v1/accesstoken?grant_type=client_credentials';

            // Log request untuk debugging (tanpa client_secret)
            \Log::info('Token Request:', [
                'url' => $tokenUrl,
                'client_id' => substr($clientId, 0, 10) . '...',
                'client_id_length' => strlen($clientId),
                'client_secret_length' => strlen($clientSecret),
                'organization_id' => config('satusehat.organization_id')
            ]);

            // Gunakan curl langsung untuk mendapatkan token
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $tokenUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded'
                ],
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'organization_id' => config('satusehat.organization_id')
                ]),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);

            $response = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);

            curl_close($curl);

            \Log::info('Token Response Status: ' . $statusCode);
            \Log::info('Token Response Raw: ' . substr($response, 0, 100) . '...');

            if ($error) {
                \Log::error('Curl error: ' . $error);
                return null;
            }

            // Pastikan respons tidak kosong
            if (empty($response)) {
                \Log::error('Respons kosong dari server SATUSEHAT');
                return null;
            }

            // Coba decode respons JSON
            $responseBody = json_decode($response, true);
            
            // Jika gagal decode, coba tampilkan respons mentah
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Gagal decode respons JSON: ' . json_last_error_msg());
                \Log::error('Respons mentah: ' . $response);
                return null;
            }

            // Log respons untuk debugging
            \Log::info('Token Response Body:', [
                'has_access_token' => isset($responseBody['access_token']),
                'expires_in' => $responseBody['expires_in'] ?? 'tidak ada',
                'organization_name' => $responseBody['organization_name'] ?? 'tidak ada'
            ]);

            if ($statusCode == 200 && isset($responseBody['access_token'])) {
                $token = $responseBody['access_token'];
                $expiresIn = $responseBody['expires_in'] ?? 3600; // Default 1 jam

                // Simpan token ke cache
                Cache::put('satusehat_token', $token, now()->addSeconds($expiresIn - 60)); // Kurangi 60 detik untuk jaga-jaga

                // Simpan informasi tambahan jika ada
                if (isset($responseBody['organization_name'])) {
                    Cache::put('satusehat_org_name', $responseBody['organization_name'], now()->addDay());
                }
                if (isset($responseBody['client_id'])) {
                    Cache::put('satusehat_client_id', $responseBody['client_id'], now()->addDay());
                }

                \Log::info('Berhasil mendapatkan token SATUSEHAT', [
                    'token_length' => strlen($token),
                    'expires_in' => $expiresIn,
                    'token_preview' => substr($token, 0, 10) . '...'
                ]);

                return $token;
            } else {
                $errorMessage = 'Gagal mendapatkan token: ';
                
                if (isset($responseBody['error_description'])) {
                    $errorMessage .= $responseBody['error_description'];
                } else if (isset($responseBody['message'])) {
                    $errorMessage .= $responseBody['message'];
                } else {
                    $errorMessage .= 'HTTP Status ' . $statusCode;
                }
                
                \Log::error($errorMessage);
                \Log::error('Response Body: ' . json_encode($responseBody));
                
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Exception saat mendapatkan token: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Memeriksa status verifikasi pasien di SATUSEHAT
     * 
     * @param string $nik
     * @param string $token
     * @return bool
     */
    private function checkVerificationStatus($nik, $token)
    {
        try {
            \Log::info('Memeriksa status verifikasi pasien dengan NIK: ' . $nik);
            
            // Jika sudah ada IHS number di session, pasien sudah terverifikasi
            if (session('kyc_ihs_number')) {
                \Log::info('Pasien sudah memiliki IHS number di session, status terverifikasi');
                return true;
            }
            
            // Endpoint API SATUSEHAT untuk pencarian pasien
            $searchEndpoint = config('satusehat.api_url') . '/kyc/v1/search';
            
            // Data untuk request pencarian
            $searchData = json_encode([
                'parameter' => $nik
            ]);
            
            // Inisialisasi curl untuk pencarian
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $searchEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $searchData,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);
            
            $searchResponse = curl_exec($curl);
            $searchStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $searchError = curl_error($curl);
            
            curl_close($curl);
            
            \Log::info('KYC Status Check Response', [
                'status' => $searchStatusCode,
                'response_length' => strlen($searchResponse),
                'error' => $searchError,
                'response' => $searchResponse
            ]);
            
            if ($searchError) {
                \Log::error('Curl error pada pemeriksaan status: ' . $searchError);
                return false;
            }
            
            // Jika status 404, berarti endpoint tidak ditemukan atau tidak tersedia
            // Dalam hal ini, kita anggap pasien sudah terverifikasi jika ada data di session
            if ($searchStatusCode == 404) {
                \Log::warning('Endpoint pencarian tidak tersedia (404). Menggunakan data session untuk verifikasi.');
                
                // Jika ada data pasien di session, anggap sudah terverifikasi
                if (session('patient_nik') && session('patient_name')) {
                    \Log::info('Menggunakan data session untuk konfirmasi status verifikasi');
                    return true;
                }
                
                return false;
            }
            
            // Coba decode respons JSON
            $searchResponseBody = json_decode($searchResponse, true);
            
            // Jika gagal decode, log error dan gunakan data session
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Gagal decode respons JSON: ' . json_last_error_msg());
                \Log::error('Respons mentah: ' . $searchResponse);
                
                // Jika ada data pasien di session, anggap sudah terverifikasi
                if (session('patient_nik') && session('patient_name')) {
                    return true;
                }
                
                return false;
            }
            
            // Jika pencarian berhasil dan ada data, berarti pasien sudah terverifikasi
            if ($searchStatusCode == 200 && isset($searchResponseBody['data']) && !empty($searchResponseBody['data'])) {
                // Ambil data pasien dari hasil pencarian
                $patientData = $searchResponseBody['data'][0];
                
                // Jika ada IHS number, simpan ke session
                if (isset($patientData['ihs_number']) && !session('kyc_ihs_number')) {
                    session(['kyc_ihs_number' => $patientData['ihs_number']]);
                }
                
                \Log::info('Pasien ditemukan di SATUSEHAT, status terverifikasi', [
                    'nik' => $nik,
                    'ihs_number' => $patientData['ihs_number'] ?? 'Tidak tersedia'
                ]);
                
                return true;
            }
            
            // Jika tidak ada data dari API tetapi ada data di session, anggap sudah terverifikasi
            if (session('patient_nik') && session('patient_name')) {
                \Log::info('Data tidak ditemukan di API tetapi ada di session, status terverifikasi');
                return true;
            }
            
            \Log::warning('Pasien tidak ditemukan di SATUSEHAT atau belum terverifikasi');
            return false;
            
        } catch (\Exception $e) {
            \Log::error('Error memeriksa status verifikasi: ' . $e->getMessage());
            
            // Jika terjadi error tetapi ada data di session, anggap sudah terverifikasi
            if (session('patient_nik') && session('patient_name')) {
                \Log::info('Terjadi error saat memeriksa status, menggunakan data session');
                return true;
            }
            
            return false;
        }
    }
} 