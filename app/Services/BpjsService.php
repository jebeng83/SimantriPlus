<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class BpjsService
{
    protected $baseUrl;
    protected $consId;
    protected $secretKey;
    protected $userKey;
    protected $username;
    protected $password;
    protected $kdAplikasi;
    protected $serviceName;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('BPJS_PCARE_BASE_URL'), '/');
        $this->serviceName = 'pcare-rest';
        $this->consId = env('BPJS_PCARE_CONS_ID');
        $this->secretKey = env('BPJS_PCARE_CONS_PWD');
        $this->userKey = env('BPJS_PCARE_USER_KEY');
        $this->username = env('BPJS_PCARE_USER');
        $this->password = env('BPJS_PCARE_PASS');
        $this->kdAplikasi = env('BPJS_PCARE_APP_CODE', '095');
    }

    public function generateSignature($data = null)
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', $this->consId . "&" . $timestamp, $this->secretKey);
        
        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    public function kirimKunjungan($data)
    {
        try {
            // 1. Validasi data wajib
            $this->validateRequiredFields($data);

            // 2. Generate timestamp & signature
            $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            $signature = hash_hmac('sha256', $this->consId . "&" . $timestamp, $this->secretKey, true);
            $encodedSignature = base64_encode($signature);

            // 3. Set headers sesuai standar BPJS
            $headers = [
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'X-authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password . ':' . $this->kdAplikasi),
                'user_key' => $this->userKey,
                'Content-Type' => 'text/plain',
                'Accept' => 'application/json'
            ];

            // 4. Format data sesuai spesifikasi BPJS
            $requestData = [
                'noKunjungan' => null,
                'noKartu' => $data['noKartu'],
                'tglDaftar' => date('d-m-Y', strtotime($data['tglDaftar'])),
                'kdPoli' => $data['kdPoli'],
                'keluhan' => $data['keluhan'] ?: 'Tidak Ada',
                'kdSadar' => $data['kdSadar'] ?? '01',
                'sistole' => (int)$data['sistole'],
                'diastole' => (int)$data['diastole'],
                'beratBadan' => (int)$data['beratBadan'],
                'tinggiBadan' => (int)$data['tinggiBadan'],
                'respRate' => (int)$data['respRate'],
                'heartRate' => (int)$data['heartRate'],
                'lingkarPerut' => (int)($data['lingkarPerut'] ?? 0),
                'kdStatusPulang' => $data['kdStatusPulang'] ?? '3',
                'tglPulang' => date('d-m-Y', strtotime($data['tglPulang'] ?? $data['tglDaftar'])),
                'kdDokter' => $data['kdDokter'],
                'kdDiag1' => $data['kdDiag1'],
                'kdDiag2' => !empty($data['kdDiag2']) ? $data['kdDiag2'] : null,
                'kdDiag3' => !empty($data['kdDiag3']) ? $data['kdDiag3'] : null,
                'kdPoliRujukInternal' => null,
                'rujukLanjut' => null,
                'kdTacc' => -1,
                'alasanTacc' => null,
                'anamnesa' => $data['keluhan'] ?: 'Tidak Ada',
                'alergiMakan' => $data['KdAlergiMakanan'] ?? '00',
                'alergiUdara' => $data['KdAlergiUdara'] ?? '00',
                'alergiObat' => $data['KdAlergiObat'] ?? '00',
                'kdPrognosa' => $data['KdPrognosa'] ?? '01',
                'terapiObat' => $data['terapi'] ?: ($data['terapiObat'] ?? 'Tidak Ada'),
                'terapiNonObat' => $data['terapi_non_obat'] ?? 'Edukasi Kesehatan',
                'bmhp' => $data['bmhp'] ?? '',
                'suhu' => str_replace('.', ',', $data['suhu'] ?? '36,5')
            ];
            
            // 5. Construct URL
            $url = $this->baseUrl . '/' . $this->serviceName . '/kunjungan/v1';

            // 6. Log request
            \Log::info('Request Kunjungan PCare', [
                'url' => $url,
                'timestamp' => $timestamp,
                'headers' => array_merge(
                    $headers,
                    ['X-signature' => '***', 'X-authorization' => '***', 'user_key' => '***']
                ),
                'data' => $requestData
            ]);

            // 7. Kirim request
            $client = new Client(['verify' => false]);
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => json_encode($requestData)
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            // 8. Log response
            \Log::info('Response Kunjungan PCare', [
                'status_code' => $statusCode,
                'response' => $responseData
            ]);
            
            // 9. Handle response
            if (isset($responseData['metaData']['code']) && $responseData['metaData']['code'] == '201') {
                if (isset($responseData['response'])) {
                    // Decrypt response jika perlu
                    $decrypted = $this->decrypt($responseData['response'], $timestamp);
                    if ($decrypted) {
                        $responseData['response'] = json_decode($decrypted, true);
                    }
                }
                
                // Update status kunjungan
                DB::table('pcare_kunjungan_umum')
                    ->where('no_rawat', $data['no_rawat'])
                    ->update(['status' => 'Terkirim']);
            }

            return $responseData;

        } catch (\Exception $e) {
            \Log::error('Error kirim kunjungan PCare: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    private function validateRequiredFields($data)
    {
        $required = [
            'noKartu', 'tglDaftar', 'kdPoli', 'kdDokter', 'kdDiag1',
            'sistole', 'diastole', 'beratBadan', 'tinggiBadan',
            'respRate', 'heartRate'
        ];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Field {$field} wajib diisi");
            }
        }
    }

    private function saveKunjungan($data, $response)
    {
        try {
            DB::table('pcare_kunjungan_umum')->insert([
                'no_rawat' => $data['no_rawat'],
                'noKunjungan' => $response['response']['message'] ?? null,
                'tglDaftar' => date('Y-m-d', strtotime($data['tglDaftar'])),
                'no_rkm_medis' => $data['no_rkm_medis'],
                'nm_pasien' => $data['nm_pasien'],
                'noKartu' => $data['noKartu'],
                'kdPoli' => $data['kdPoli'],
                'nmPoli' => $data['nmPoli'],
                'keluhan' => $data['keluhan'],
                'kdSadar' => $data['kdSadar'],
                'nmSadar' => 'Compos Mentis',
                'sistole' => $data['sistole'],
                'diastole' => $data['diastole'],
                'beratBadan' => $data['beratBadan'],
                'tinggiBadan' => $data['tinggiBadan'],
                'respRate' => $data['respRate'],
                'heartRate' => $data['heartRate'],
                'terapi' => $data['terapiObat'],
                'status_pulang' => '3',
                'tglPulang' => date('Y-m-d'),
                'kdDokter' => $data['kdDokter'],
                'nmDokter' => $data['nmDokter'],
                'status' => 'Terkirim'
            ]);
        } catch (\Exception $e) {
            Log::error('Error menyimpan kunjungan: ' . $e->getMessage());
            throw $e;
        }
    }
}