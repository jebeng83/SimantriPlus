<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use App\Traits\PcareTrait;
use App\Traits\BpjsTraits as MainBpjsTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class ReferensiPoliController extends Controller
{
    use PcareTrait, MainBpjsTraits {
        PcareTrait::stringDecrypt insteadof MainBpjsTraits;
        MainBpjsTraits::requestGetBpjs as requestGetBpjsMain;
    }

    /**
     * Display the poli reference page
     */
    public function index()
    {
        return view('pcare.referensi.refrensi-poli');
    }

    /**
     * Get poli data from BPJS PCare API
     */
    public function getPoli(Request $request, $tanggal = null)
    {
        try {
            // Get tanggal from route parameter, query parameter, or use today's date
            if (!$tanggal) {
                $tanggal = $request->query('tanggal', date('d-m-Y'));
            }
            
            // Validate and format date for BPJS MobileJKN API (requires YYYY-MM-DD format)
            if ($tanggal) {
                // Try to parse different date formats and convert to YYYY-MM-DD
                try {
                    // If already in YYYY-MM-DD format, keep it
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                        // Already in correct format
                    }
                    // If in DD-MM-YYYY format, convert to YYYY-MM-DD
                    elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggal)) {
                        $dateObj = \DateTime::createFromFormat('d-m-Y', $tanggal);
                        if ($dateObj) {
                            $tanggal = $dateObj->format('Y-m-d');
                        } else {
                            throw new \Exception('Invalid date format');
                        }
                    }
                    // If in other formats, try to parse
                    else {
                        $dateObj = \DateTime::createFromFormat('Y-m-d', $tanggal);
                        if (!$dateObj) {
                            $dateObj = \DateTime::createFromFormat('d-m-Y', $tanggal);
                        }
                        if ($dateObj) {
                            $tanggal = $dateObj->format('Y-m-d');
                        } else {
                            throw new \Exception('Invalid date format');
                        }
                    }
                } catch (\Exception $e) {
                    // If parsing fails, use today's date in YYYY-MM-DD format
                    $tanggal = date('Y-m-d');
                }
            } else {
                // Use today's date in YYYY-MM-DD format
                $tanggal = date('Y-m-d');
            }
            
            $cacheKey = 'mobilejkn_ref_poli_' . str_replace('-', '_', $tanggal);
            
            // Check cache first
            $cachedData = Cache::get($cacheKey);
            if ($cachedData) {
                return response()->json([
                    'success' => true,
                    'data' => $cachedData,
                    'source' => 'cache'
                ]);
            }

            // Call BPJS Mobile JKN API for poli reference
            $endpoint = "ref/poli/tanggal/{$tanggal}";
            $response = $this->requestGetBpjsMain($endpoint, 'mobilejkn');

            // Handle JsonResponse object
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
                
                if ($responseData && isset($responseData['response'])) {
                    // Cache the response for 1 hour
                    Cache::put($cacheKey, $responseData['response'], 3600);
                    
                    return response()->json([
                        'success' => true,
                        'data' => $responseData['response'],
                        'metadata' => $responseData['metadata'] ?? $responseData['metaData'] ?? null,
                        'source' => 'api'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'No data found',
                'data' => null
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error getting poli reference: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Export poli data to Excel
     */
    public function exportExcel()
    {
        try {
            $tanggal = date('d-m-Y');
            $endpoint = "ref/poli/tanggal/{$tanggal}";
            $response = $this->requestGetBpjsMain($endpoint, 'mobilejkn');
            
            // Handle JsonResponse object
            $responseData = null;
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
            }
            
            if (!$responseData || !isset($responseData['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data to export'
                ], 404);
            }

            $data = $responseData['response'];
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $sheet->setCellValue('A1', 'Kode Poli');
            $sheet->setCellValue('B1', 'Nama Poli');
            $sheet->setCellValue('C1', 'Poliklinik');
            
            // Add data
            $row = 2;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $item['kdPoli'] ?? '');
                $sheet->setCellValue('B' . $row, $item['nmPoli'] ?? '');
                $sheet->setCellValue('C' . $row, $item['poliklinik'] ?? '');
                $row++;
            }
            
            $writer = new Xlsx($spreadsheet);
            $filename = 'referensi_poli_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Response::streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting poli to Excel: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export poli data to PDF
     */
    public function exportPdf()
    {
        try {
            $tanggal = date('d-m-Y');
            $endpoint = "ref/poli/tanggal/{$tanggal}";
            $response = $this->requestGetBpjsMain($endpoint, 'mobilejkn');
            
            // Handle JsonResponse object
            $responseData = null;
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
            }
            
            if (!$responseData || !isset($responseData['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data to export'
                ], 404);
            }

            $data = $responseData['response'];
            
            $pdf = Pdf::loadView('pcare.exports.poli', compact('data'));
            $filename = 'referensi_poli_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting poli to PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Data Poli FKTP with pagination
     * Endpoint: poli/fktp/{start}/{limit}
     * 
     * @param int $start Row data awal yang akan ditampilkan
     * @param int $limit Limit jumlah data yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPoliFktp($start, $limit)
    {
        try {
            // Validasi parameter
            if (!is_numeric($start) || !is_numeric($limit)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Parameter start dan limit harus berupa angka'
                    ],
                    'response' => null
                ], 400);
            }

            $start = (int) $start;
            $limit = (int) $limit;

            // Validasi range parameter
            if ($start < 0) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Parameter start tidak boleh kurang dari 0'
                    ],
                    'response' => null
                ], 400);
            }

            if ($limit <= 0 || $limit > 100) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Parameter limit harus antara 1-100'
                    ],
                    'response' => null
                ], 400);
            }

            // Log request
            Log::info('PCare Get Poli FKTP Request', [
                'start' => $start,
                'limit' => $limit
            ]);

            // Buat cache key
            $cacheKey = 'pcare_poli_fktp_' . $start . '_' . $limit;
            
            // Cek cache dulu (cache 30 menit)
            if (Cache::has($cacheKey)) {
                Log::info('PCare Get Poli FKTP From Cache');
                return response()->json(Cache::get($cacheKey));
            }

            // Format endpoint
            $endpoint = "poli/fktp/{$start}/{$limit}";

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Log response
            Log::info('PCare Get Poli FKTP Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'count' => isset($response['response']['count']) ? $response['response']['count'] : 0
            ]);

            // Normalize response structure to match frontend expectations
            if (isset($response['metaData'])) {
                $response['metadata'] = $response['metaData'];
                unset($response['metaData']);
            }

            // Simpan ke cache jika berhasil (30 menit)
            if (isset($response['metadata']) && $response['metadata']['code'] == 200) {
                Cache::put($cacheKey, $response, now()->addMinutes(30));
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Poli FKTP Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        }
    }
}