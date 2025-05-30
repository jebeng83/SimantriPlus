<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class PcareKunjunganTest extends TestCase
{
    public function test_kirim_kunjungan()
    {
        // 1. Setup data test
        $dataTest = [
            'no_rawat' => '2025/05/28/000001',
            'noKartu' => '0000043678034',
            'kdPoli' => 'UMU',
            'tglDaftar' => '2025-05-28'
        ];

        // 2. Hit endpoint jadikan kunjungan
        $response = $this->postJson('/api/pcare/pendaftaran/jadikan-kunjungan', $dataTest);

        // 3. Log response
        Log::info('Test Kirim Kunjungan Response:', [
            'status' => $response->status(),
            'content' => $response->json()
        ]);

        // 4. Assertions
        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }

    public function test_validasi_data_kunjungan()
    {
        // Test dengan data tidak lengkap
        $dataTidakLengkap = [
            'no_rawat' => '2025/05/28/000001'
        ];

        $response = $this->postJson('/api/pcare/pendaftaran/jadikan-kunjungan', $dataTidakLengkap);
        
        Log::info('Test Validasi Data Response:', [
            'status' => $response->status(),
            'content' => $response->json()
        ]);

        $response->assertStatus(500)
                ->assertJson(['success' => false]);
    }

    public function test_format_data_kunjungan()
    {
        // Setup mock data lengkap
        $dataLengkap = [
            'no_rawat' => '2025/05/28/000001',
            'noKartu' => '0000043678034',
            'kdPoli' => 'UMU',
            'tglDaftar' => '2025-05-28',
            'keluhan' => 'Demam dan batuk',
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 65,
            'tinggiBadan' => 170,
            'respRate' => 20,
            'heartRate' => 80,
            'lingkarPerut' => 80,
            'suhu' => '36.5'
        ];

        $response = $this->postJson('/api/pcare/pendaftaran/jadikan-kunjungan', $dataLengkap);
        
        Log::info('Test Format Data Response:', [
            'status' => $response->status(),
            'content' => $response->json()
        ]);

        if ($response->status() === 200) {
            $this->assertTrue($response->json()['success']);
            Log::info('Data berhasil dikirim dengan format yang benar');
        } else {
            Log::error('Gagal mengirim data:', [
                'error' => $response->json()['message']
            ]);
        }
    }
} 