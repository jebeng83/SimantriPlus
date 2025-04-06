<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class DphoController extends Controller
{
    use PcareTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Mendapatkan data DPHO (Daftar Plafon Harga Obat) PCare
     * 
     * @param string $keyword Kode atau nama DPHO
     * @param int $start Row data awal yang ditampilkan (default 0)
     * @param int $limit Limit jumlah data yang ditampilkan (default 10)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDpho($keyword, $start = 0, $limit = 10)
    {
        try {
            // Validasi parameter
            if (!is_numeric($start) || $start < 0) {
                $start = 0;
            }
            
            if (!is_numeric($limit) || $limit < 1) {
                $limit = 10;
            }
            
            // Format endpoint sesuai katalog BPJS: obat/dpho/{keyword}/{start}/{limit}
            $endpoint = 'obat/dpho/' . urlencode($keyword) . '/' . $start . '/' . $limit;
            
            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);
            
            // Log response
            Log::info('PCare Get DPHO Response', [
                'keyword' => $keyword,
                'start' => $start,
                'limit' => $limit,
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get DPHO Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }

    /**
     * Mendapatkan data obat berdasarkan nomor kunjungan
     * 
     * @param string $noKunjungan Nomor Kunjungan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getObatByKunjungan($noKunjungan)
    {
        try {
            // Validasi format nomor kunjungan
            if (empty($noKunjungan)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kunjungan tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: obat/kunjungan/{noKunjungan}
            $endpoint = 'obat/kunjungan/' . $noKunjungan;
            
            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);
            
            // Log response
            Log::info('PCare Get Obat By Kunjungan Response', [
                'noKunjungan' => $noKunjungan,
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Obat By Kunjungan Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'noKunjungan' => $noKunjungan
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }

    /**
     * Menambahkan data obat ke kunjungan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addObat(Request $request)
    {
        try {
            // Validasi data obat
            $validatedData = $request->validate([
                'kdObatSK' => 'required|numeric',
                'noKunjungan' => 'required|string',
                'racikan' => 'required|boolean',
                'kdRacikan' => 'nullable|string',
                'obatDPHO' => 'required|boolean',
                'kdObat' => 'required|string',
                'signa1' => 'required|numeric',
                'signa2' => 'required|numeric',
                'jmlObat' => 'required|numeric',
                'jmlPermintaan' => 'required|numeric',
                'nmObatNonDPHO' => 'nullable|string',
            ]);
            
            // Format endpoint sesuai katalog BPJS: obat/kunjungan (POST)
            $endpoint = 'obat/kunjungan';
            
            // Log request
            Log::info('PCare Add Obat Request', [
                'noKunjungan' => $validatedData['noKunjungan'],
                'racikan' => $validatedData['racikan'] ? 'Ya' : 'Tidak',
                'obatDPHO' => $validatedData['obatDPHO'] ? 'Ya' : 'Tidak',
                'kdObat' => $validatedData['kdObat']
            ]);
            
            // Kirim request ke PCare dengan content-type: text/plain
            $response = $this->requestPcare($endpoint, 'POST', $validatedData, 'text/plain');
            
            // Log response
            Log::info('PCare Add Obat Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown'
            ]);
            
            // Cek jika response menunjukkan kesalahan
            if (isset($response['metaData']) && $response['metaData']['code'] != 200 && $response['metaData']['code'] != 201) {
                Log::warning('PCare Add Obat Failed', [
                    'response' => $response,
                    'noKunjungan' => $validatedData['noKunjungan']
                ]);
            }

            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PCare Add Obat Validation Error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 422,
                    'message' => 'Validation Error',
                ],
                'response' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PCare Add Obat Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }

    /**
     * Menghapus data obat dari kunjungan
     * 
     * @param string $kdObatSK Kode Obat SK
     * @param string $noKunjungan Nomor Kunjungan
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteObat($kdObatSK, $noKunjungan)
    {
        try {
            // Validasi parameter
            if (empty($kdObatSK) || !is_numeric($kdObatSK)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode Obat SK tidak valid'
                    ],
                    'response' => null
                ], 400);
            }
            
            if (empty($noKunjungan)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kunjungan tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: obat/{kdObatSK}/kunjungan/{noKunjungan}
            $endpoint = 'obat/' . $kdObatSK . '/kunjungan/' . $noKunjungan;
            
            // Log request
            Log::info('PCare Delete Obat Request', [
                'kdObatSK' => $kdObatSK,
                'noKunjungan' => $noKunjungan
            ]);
            
            // Kirim request DELETE ke PCare
            $response = $this->requestPcare($endpoint, 'DELETE');
            
            // Log response
            Log::info('PCare Delete Obat Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown'
            ]);
            
            // Cek jika response menunjukkan kesalahan
            if (isset($response['metaData']) && $response['metaData']['code'] != 200) {
                Log::warning('PCare Delete Obat Failed', [
                    'response' => $response,
                    'kdObatSK' => $kdObatSK,
                    'noKunjungan' => $noKunjungan
                ]);
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Delete Obat Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'kdObatSK' => $kdObatSK,
                'noKunjungan' => $noKunjungan
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }
}
