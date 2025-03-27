<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class WilayahController extends Controller
{
    public function getPropinsi()
    {
        try {
            $path = public_path('assets/propinsi.iyem');
            if (!File::exists($path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File propinsi.iyem tidak ditemukan'
                ], 404);
            }

            $content = File::get($path);
            $data = json_decode($content, true);
            
            if (!isset($data['propinsi'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format file propinsi.iyem tidak valid'
                ], 400);
            }

            // Transformasi data ke format yang dibutuhkan
            $result = [];
            foreach ($data['propinsi'] as $row) {
                $result[] = [
                    'kd_prop' => $row['id'],
                    'nm_prop' => $row['nama']
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data propinsi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data propinsi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKabupaten(Request $request)
    {
        try {
            $kdProp = $request->query('kd_prop');
            if (!$kdProp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode propinsi tidak valid'
                ], 400);
            }

            $path = public_path('assets/kabupaten.iyem');
            if (!File::exists($path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File kabupaten.iyem tidak ditemukan'
                ], 404);
            }

            $content = File::get($path);
            $data = json_decode($content, true);
            
            if (!isset($data['kabupaten'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format file kabupaten.iyem tidak valid'
                ], 400);
            }

            // Filter kabupaten berdasarkan kode propinsi
            $result = [];
            foreach ($data['kabupaten'] as $row) {
                if ($row['id_propinsi'] == $kdProp) {
                    $result[] = [
                        'kd_kab' => $row['id'],
                        'nm_kab' => $row['nama']
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data kabupaten: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kabupaten: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKecamatan(Request $request)
    {
        try {
            $kdKab = $request->query('kd_kab');
            if (!$kdKab) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode kabupaten tidak valid'
                ], 400);
            }

            $path = public_path('assets/kecamatan.iyem');
            if (!File::exists($path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File kecamatan.iyem tidak ditemukan'
                ], 404);
            }

            $content = File::get($path);
            $data = json_decode($content, true);
            
            if (!isset($data['kecamatan'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format file kecamatan.iyem tidak valid'
                ], 400);
            }

            // Filter kecamatan berdasarkan kode kabupaten
            $result = [];
            foreach ($data['kecamatan'] as $row) {
                if ($row['id_kabupaten'] == $kdKab) {
                    $result[] = [
                        'kd_kec' => $row['id'],
                        'nm_kec' => $row['nama']
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data kecamatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kecamatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKelurahan(Request $request)
    {
        try {
            $kdKec = $request->query('kd_kec');
            if (!$kdKec) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode kecamatan tidak valid'
                ], 400);
            }

            $path = public_path('assets/kelurahan.iyem');
            if (!File::exists($path)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File kelurahan.iyem tidak ditemukan'
                ], 404);
            }

            $content = File::get($path);
            $data = json_decode($content, true);
            
            if (!isset($data['kelurahan'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format file kelurahan.iyem tidak valid'
                ], 400);
            }

            // Filter kelurahan berdasarkan kode kecamatan
            $result = [];
            foreach ($data['kelurahan'] as $row) {
                if ($row['id_kecamatan'] == $kdKec) {
                    $result[] = [
                        'kd_kel' => $row['id'],
                        'nm_kel' => $row['nama']
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data kelurahan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kelurahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 