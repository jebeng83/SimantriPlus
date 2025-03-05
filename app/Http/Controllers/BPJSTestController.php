<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BPJSTestController extends Controller
{
    public function testConnection()
    {
        try {
            $consId = "7925";
            $secretKey = "2eF2C8E837";
            $userKey = "403bf17ddf158790afcfe1e8dd682a67";
            
            // Format timestamp sesuai standar BPJS
            $timestamp = strtotime(date('Y-m-d H:i:s'));
            $signature = hash_hmac('sha256', $consId . "&" . $timestamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);

            $data = [
                "t_sep" => [
                    "noKartu" => "0001441909697",
                    "kodeDokter" => "510429",
                    "tglSep" => date('Y-m-d'),
                    "ppkPelayanan" => "095"
                ]
            ];

            $headers = [
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'user_key' => $userKey,
                'Content-Type' => 'application/json'
            ];

            Log::info('BPJS Request Headers', [
                'headers' => $headers,
                'data' => $data
            ]);

            $response = Http::withHeaders($headers)
                ->post('https://apijkn.bpjs-kesehatan.go.id/wsihs/api/pcare/validate', $data);

            Log::info('BPJS Test Connection Response', [
                'request' => [
                    'data' => $data,
                    'timestamp' => $timestamp,
                    'headers' => $headers
                ],
                'response' => $response->json()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil terhubung ke BPJS',
                'data' => $response->json()
            ]);

        } catch (Exception $e) {
            Log::error('BPJS Test Connection Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal koneksi ke BPJS: ' . $e->getMessage()
            ], 422);
        }
    }
}
