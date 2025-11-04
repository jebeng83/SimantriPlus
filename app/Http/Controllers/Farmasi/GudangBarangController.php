<?php

namespace App\Http\Controllers;

use App\Models\RawatJalan\Gudangbarang as GudangBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GudangBarangController extends Controller
{
    /**
     * Update atau create stok gudang barang
     */
    public function updateStok(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_brng' => 'required|string|max:15',
                'kd_bangsal' => 'required|string|max:5',
                'stok' => 'required|numeric|min:0',
                'no_batch' => 'required|string|max:20',
                'no_faktur' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kodeBarang = $request->kode_brng;
            $kdBangsal = $request->kd_bangsal;
            $stok = $request->stok;
            $noBatch = $request->no_batch;
            $noFaktur = $request->no_faktur;

            // Cari record existing berdasarkan composite key
            $record = GudangBarang::where('kode_brng', $kodeBarang)
                ->where('kd_bangsal', $kdBangsal)
                ->where('no_batch', $noBatch)
                ->where('no_faktur', $noFaktur)
                ->first();

            if ($record) {
                // Tambahkan stok
                $record->stok = ($record->stok ?? 0) + $stok;
                $record->save();
            } else {
                // Buat record baru
                $record = GudangBarang::create([
                    'kode_brng' => $kodeBarang,
                    'kd_bangsal' => $kdBangsal,
                    'stok' => $stok,
                    'no_batch' => $noBatch,
                    'no_faktur' => $noFaktur,
                ]);
            }

            if ($record) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stok gudang barang berhasil diupdate',
                    'data' => [
                        'kode_brng' => $kodeBarang,
                        'kd_bangsal' => $kdBangsal,
                        'stok_ditambahkan' => $stok,
                        'total_stok' => GudangBarang::getTotalStokByBarangBangsal($kodeBarang, $kdBangsal)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate stok gudang barang'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error updating gudang barang stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate stok gudang barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total stok untuk kode barang dan bangsal tertentu
     */
    public function getTotalStok(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_brng' => 'required|string|max:15',
                'kd_bangsal' => 'required|string|max:5'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kodeBarang = $request->kode_brng;
            $kdBangsal = $request->kd_bangsal;

            $totalStok = GudangBarang::getTotalStokByBarangBangsal($kodeBarang, $kdBangsal);

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_brng' => $kodeBarang,
                    'kd_bangsal' => $kdBangsal,
                    'total_stok' => $totalStok
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting total stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stok detail untuk kode barang dan bangsal tertentu
     */
    public function getStokDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_brng' => 'required|string|max:15',
                'kd_bangsal' => 'required|string|max:5'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kodeBarang = $request->kode_brng;
            $kdBangsal = $request->kd_bangsal;

            $stokDetail = GudangBarang::where('kode_brng', $kodeBarang)
                ->where('kd_bangsal', $kdBangsal)
                ->with('databarang')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stokDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting stok detail: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}