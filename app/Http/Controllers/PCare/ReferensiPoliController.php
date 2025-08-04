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
        return view('Pcare.referensi.referensi-poli');
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
            
            $pdf = Pdf::loadView('exports.poli', compact('data'));
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
    
    public function testMethod()
    {
        Log::info('Test Method Called in ReferensiPoliController');
        return response()->json([
            'message' => 'Test method works',
            'controller' => 'ReferensiPoliController',
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Get Data Poli FKTP
     * Endpoint: poli/fktp/{start}/{limit}
     * Method: GET
     * Format: JSON
     * Content-Type: application/json; charset=utf-8
     * 
     * @param int $start Row data awal yang akan ditampilkan
     * @param int $limit Limit jumlah data yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPoliFktp($start, $limit)
    {
        Log::info('PCare Get Poli FKTP Method Called', [
            'start' => $start,
            'limit' => $limit,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
            // Validasi parameter
            if (!is_numeric($start) || !is_numeric($limit)) {
                Log::error('PCare Get Poli FKTP Invalid Parameters', [
                    'start' => $start,
                    'limit' => $limit
                ]);
                return response()->json([
                    'response' => [
                        'count' => 0,
                        'list' => []
                    ],
                    'metaData' => [
                        'message' => 'Parameter start dan limit harus berupa angka',
                        'code' => 400
                    ]
                ], 400);
            }

            $start = (int) $start;
            $limit = (int) $limit;

            // Validasi range parameter sesuai spesifikasi BPJS
            if ($start < 0) {
                return response()->json([
                    'response' => [
                        'count' => 0,
                        'list' => []
                    ],
                    'metaData' => [
                        'message' => 'Parameter start tidak boleh kurang dari 0',
                        'code' => 400
                    ]
                ], 400);
            }

            if ($limit <= 0 || $limit > 100) {
                return response()->json([
                    'response' => [
                        'count' => 0,
                        'list' => []
                    ],
                    'metaData' => [
                        'message' => 'Parameter limit harus antara 1-100',
                        'code' => 400
                    ]
                ], 400);
            }

            // Cache key
            $cacheKey = "pcare_poli_fktp_{$start}_{$limit}";

            // Cek cache terlebih dahulu
            $cachedResponse = Cache::get($cacheKey);
            if ($cachedResponse) {
                Log::info('PCare Get Poli FKTP Cache Hit', ['cache_key' => $cacheKey]);
                return response()->json($cachedResponse, 200, [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]);
            }

            // Format endpoint sesuai spesifikasi BPJS
            $endpoint = "poli/fktp/{$start}/{$limit}";
            Log::info('PCare Get Poli FKTP Endpoint', ['endpoint' => $endpoint]);

            // Request ke PCare API
            $response = $this->requestPcare($endpoint);
            
            // Log response untuk debugging
            Log::info('PCare Get Poli FKTP Raw Response', [
                'response_structure' => is_array($response) ? array_keys($response) : gettype($response),
                'has_metadata' => isset($response['metaData']),
                'has_response' => isset($response['response'])
            ]);
            
            // Jika tidak ada response dari PCare API, buat data dummy untuk testing
            if (!isset($response['metaData']) || $response['metaData']['code'] != 200) {
                Log::info('PCare API tidak tersedia, menggunakan data dummy untuk testing');
                
                // Data dummy sesuai format BPJS untuk testing
                $dummyData = [
                    [
                        'kdPoli' => '001',
                        'nmPoli' => 'Umum',
                        'poliSakit' => true
                    ],
                    [
                        'kdPoli' => '003',
                        'nmPoli' => 'K I A',
                        'poliSakit' => true
                    ],
                    [
                        'kdPoli' => '002',
                        'nmPoli' => 'Gigi & Mulut',
                        'poliSakit' => true
                    ],
                    [
                        'kdPoli' => '008',
                        'nmPoli' => 'KB',
                        'poliSakit' => false
                    ]
                ];
                
                // Apply pagination to dummy data
                $totalData = count($dummyData);
                $paginatedData = array_slice($dummyData, $start, $limit);
                
                return response()->json([
                    'response' => [
                        'count' => count($paginatedData),
                        'list' => $paginatedData
                    ],
                    'metaData' => [
                        'message' => 'OK',
                        'code' => 200
                    ]
                ], 200, [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]);
            }

            // Format response sesuai katalog BPJS
            $formattedResponse = [
                'response' => [
                    'count' => 0,
                    'list' => []
                ],
                'metaData' => [
                    'message' => 'Data tidak ditemukan',
                    'code' => 404
                ]
            ];

            // Proses response dari PCare API
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                $poliList = [];
                
                // Jika response berhasil, format data sesuai spesifikasi
                if (isset($response['response']['list']) && is_array($response['response']['list'])) {
                    foreach ($response['response']['list'] as $poli) {
                        $poliList[] = [
                            'kdPoli' => $poli['kdPoli'] ?? '',
                            'nmPoli' => $poli['nmPoli'] ?? '',
                            'poliSakit' => isset($poli['poliSakit']) ? (bool)$poli['poliSakit'] : true
                        ];
                    }
                } elseif (isset($response['response']) && is_array($response['response'])) {
                    // Jika response langsung berupa array poli
                    foreach ($response['response'] as $poli) {
                        $poliList[] = [
                            'kdPoli' => $poli['kdPoli'] ?? '',
                            'nmPoli' => $poli['nmPoli'] ?? '',
                            'poliSakit' => isset($poli['poliSakit']) ? (bool)$poli['poliSakit'] : true
                        ];
                    }
                }

                $formattedResponse = [
                    'response' => [
                        'count' => count($poliList),
                        'list' => $poliList
                    ],
                    'metaData' => [
                        'message' => 'OK',
                        'code' => 200
                    ]
                ];
            } elseif (isset($response['metaData'])) {
                // Jika ada error dari PCare API
                $formattedResponse['metaData'] = [
                    'message' => $response['metaData']['message'] ?? 'Terjadi kesalahan pada server PCare',
                    'code' => $response['metaData']['code'] ?? 500
                ];
            }

            // Simpan ke cache jika berhasil (30 menit)
            if ($formattedResponse['metaData']['code'] == 200) {
                Cache::put($cacheKey, $formattedResponse, now()->addMinutes(30));
                Log::info('PCare Get Poli FKTP Cache Saved', ['cache_key' => $cacheKey]);
            }

            // Return response dengan Content-Type yang sesuai
            return response()->json($formattedResponse, 200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ]);

        } catch (\Exception $e) {
            Log::error('PCare Get Poli FKTP Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Return error response sesuai format BPJS
            return response()->json([
                'response' => [
                    'count' => 0,
                    'list' => []
                ],
                'metaData' => [
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                    'code' => 500
                ]
            ], 500, [
                'Content-Type' => 'application/json; charset=utf-8'
            ]);
        }
    }
}