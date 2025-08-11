<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\PcareTrait;
use Illuminate\Validation\ValidationException;

class KelompokPcareController extends Controller
{
    use PcareTrait;

    /**
     * Mendapatkan data klub Prolanis PCare berdasarkan jenis kelompok
     * Format endpoint: {Base URL}/{Service Name}/kelompok/club/{Parameter 1}
     * 
     * @param string $kdKelompok Kode Jenis Kelompok (01: Diabetes Melitus, 02: Hipertensi)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClubProlanis($kdKelompok)
    {
        try {
            // Validasi parameter kode kelompok
            if (!in_array($kdKelompok, ['01', '02'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode jenis kelompok harus berupa 01 (Diabetes Melitus) atau 02 (Hipertensi)'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: kelompok/club/{kdKelompok}
            $endpoint = 'kelompok/club/' . $kdKelompok;
            
            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);
            
            // Log response
            Log::info('PCare Get Club Prolanis Response', [
                'kdKelompok' => $kdKelompok,
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Club Prolanis Error', [
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
     * Mendapatkan data kegiatan kelompok PCare berdasarkan bulan
     * Format endpoint: {Base URL}/{Service Name}/kelompok/kegiatan/{Parameter 1}
     * 
     * @param string $bulan Bulan dalam format dd-mm-yyyy
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKegiatanKelompok($bulan)
    {
        try {
            // Validasi format bulan (harus DD-MM-YYYY)
            if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $bulan)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format bulan harus DD-MM-YYYY'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: kelompok/kegiatan/{bulan}
            $endpoint = 'kelompok/kegiatan/' . $bulan;
            
            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);
            
            // Log response
            Log::info('PCare Get Kegiatan Kelompok Response', [
                'bulan' => $bulan,
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Kegiatan Kelompok Error', [
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
     * Mendapatkan data peserta kegiatan kelompok PCare berdasarkan eduId
     * Format endpoint: {Base URL}/{Service Name}/kelompok/peserta/{Parameter 1}
     * 
     * @param string $eduId ID kegiatan edukasi kelompok
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPesertaKelompok($eduId)
    {
        try {
            // Validasi parameter eduId (format asumsi eduId 11 digit numerik)
            if (!preg_match('/^\d+$/', $eduId)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format eduId tidak valid, harus berupa angka'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: kelompok/peserta/{eduId}
            $endpoint = 'kelompok/peserta/' . $eduId;
            
            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);
            
            // Log response
            Log::info('PCare Get Peserta Kelompok Response', [
                'eduId' => $eduId,
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown'
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Peserta Kelompok Error', [
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
     * Menambahkan data kegiatan kelompok PCare
     * Format endpoint: {Base URL}/{Service Name}/kelompok/kegiatan
     * 
     * @param Request $request Request berisi data kegiatan kelompok
     * @return \Illuminate\Http\JsonResponse
     */
    public function addKegiatanKelompok(Request $request)
    {
        try {
            // Debug input yang diterima
            Log::info('Input received', [
                'input' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);
            
            // Cek apakah request berupa JSON string (dari Content-Type: text/plain)
            $input = $request->all();
            if ($request->header('Content-Type') === 'text/plain' && empty($input)) {
                $jsonContent = $request->getContent();
                $input = json_decode($jsonContent, true);
                Log::info('Decoded content', ['decoded' => $input]);
            }
            
            // Validasi data input
            $rules = [
                'eduId' => 'nullable',
                'clubId' => 'required|numeric',
                'tglPelayanan' => 'required|string',
                'kdKegiatan' => 'required|string',
                'kdKelompok' => 'required|string',
                'materi' => 'required|string',
                'pembicara' => 'required|string',
                'lokasi' => 'required|string',
                'keterangan' => 'nullable|string',
                'biaya' => 'required|numeric'
            ];
            
            $validator = Validator::make($input, $rules);
            
            if ($validator->fails()) {
                Log::error('Validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'metaData' => [
                        'code' => 422,
                        'message' => 'Validation Error'
                    ],
                    'response' => $validator->errors()
                ], 422);
            }
            
            $validatedData = $validator->validated();

            // Validasi format tanggal
            if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $validatedData['tglPelayanan'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal pelayanan harus DD-MM-YYYY'
                    ],
                    'response' => null
                ], 400);
            }

            // Validasi kode kegiatan
            if (!in_array($validatedData['kdKegiatan'], ['01', '10', '11'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode kegiatan harus berupa 01 (Senam), 10 (Penyuluhan), atau 11 (Penyuluhan dan Senam)'
                    ],
                    'response' => null
                ], 400);
            }

            // Validasi kode kelompok
            if (!in_array($validatedData['kdKelompok'], ['01', '02', '03'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode kelompok harus berupa 01 (Diabetes Melitus), 02 (Hipertensi), atau 03 (Asthma)'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Format endpoint sesuai katalog BPJS: kelompok/kegiatan
            $endpoint = 'kelompok/kegiatan';
            
            // Siapkan data untuk dikirim
            $data = [
                'eduId' => $validatedData['eduId'],
                'clubId' => (int)$validatedData['clubId'],
                'tglPelayanan' => $validatedData['tglPelayanan'],
                'kdKegiatan' => $validatedData['kdKegiatan'],
                'kdKelompok' => $validatedData['kdKelompok'],
                'materi' => $validatedData['materi'],
                'pembicara' => $validatedData['pembicara'],
                'lokasi' => $validatedData['lokasi'],
                'keterangan' => $validatedData['keterangan'] ?? '',
                'biaya' => (int)$validatedData['biaya']
            ];
            
            // Log request
            Log::info('PCare Add Kegiatan Kelompok Request', [
                'data' => $data
            ]);
            
            // Kirim request ke PCare dengan content-type: text/plain
            $response = $this->requestPcare($endpoint, 'POST', $data, 'text/plain');
            
            // Log response
            Log::info('PCare Add Kegiatan Kelompok Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'response' => $response
            ]);

            return response()->json($response);

        } catch (ValidationException $e) {
            Log::error('PCare Add Kegiatan Kelompok Validation Error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 422,
                    'message' => 'Validation Error'
                ],
                'response' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PCare Add Kegiatan Kelompok Error', [
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
}
