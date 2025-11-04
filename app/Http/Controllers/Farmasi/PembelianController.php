<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PembelianController extends Controller
{
    /**
     * Get dropdown data for akun bayar
     */
    public function getAkunBayar()
    {
        try {
            $akunBayar = DB::table('akun_bayar')
                ->leftJoin('rekening', 'akun_bayar.kd_rek', '=', 'rekening.kd_rek')
                ->select(
                    'akun_bayar.kd_rek', 
                    'akun_bayar.nama_bayar',
                    'rekening.nm_rek'
                )
                ->orderBy('akun_bayar.nama_bayar')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $akunBayar
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching akun bayar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown data for supplier
     */
    public function getSupplier()
    {
        try {
            $suppliers = DB::table('datasuplier')
                ->select('kode_suplier', 'nama_suplier')
                ->orderBy('nama_suplier')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown data for petugas
     */
    public function getPetugas()
    {
        try {
            Log::info('Getting petugas data...');
            $petugas = DB::table('petugas')
                ->select('nip', 'nama')
                ->orderBy('nama')
                ->get();

            Log::info('Petugas count: ' . $petugas->count());
            Log::info('Petugas data: ' . $petugas->toJson());

            return response()->json([
                'success' => true,
                'data' => $petugas
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
        } catch (\Exception $e) {
            Log::error('Error fetching petugas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching petugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dropdown data for lokasi/bangsal
     */
    public function getLokasi()
    {
        try {
            $lokasi = DB::table('bangsal')
                ->select('kd_bangsal', 'nm_bangsal')
                ->orderBy('nm_bangsal')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $lokasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lokasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store pembelian data
     */
    public function store(Request $request)
    {
        try {
            Log::info('Pembelian store method called');
            Log::info('Request data: ' . json_encode($request->all()));
            
            DB::beginTransaction();

            // Validate request (no_faktur tidak wajib karena akan di-generate)
            $validated = $request->validate([
                'no_faktur' => 'nullable|string|max:50',
                'tgl_beli' => 'required|date',
                'kd_rek' => 'required|string',
                'kode_suplier' => 'required|string',
                'nip' => 'required|string',
                'kd_bangsal' => 'required|string',
                'total1' => 'required|numeric|min:0',
                'potongan' => 'required|numeric|min:0',
                'total2' => 'required|numeric|min:0',
                'ppn' => 'required|numeric|min:0',
                'tagihan' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.kode_brng' => 'required|string',
                'items.*.kode_sat' => 'nullable|string|max:10',
                'items.*.no_batch' => 'nullable|string|max:20',
                'items.*.kadaluarsa' => 'nullable|date',
                'items.*.jumlah' => 'required|numeric|min:1',
                'items.*.harga' => 'required|numeric|min:0',
                'items.*.subtotal' => 'required|numeric|min:0',
                'items.*.dis' => 'nullable|numeric|min:0|max:100',
                'items.*.besardis' => 'nullable|numeric|min:0',
                'items.*.total' => 'required|numeric|min:0'
            ]);

            // Generate nomor faktur baru di dalam transaksi untuk menghindari duplicate
            $todayFormatted = now()->format('Ymd');
            $prefix = 'PB-' . $todayFormatted . '-';
            
            // Cari nomor faktur terakhir untuk hari ini dengan locking
            $lastFaktur = DB::table('pembelian')
                ->where('no_faktur', 'LIKE', $prefix . '%')
                ->orderBy('no_faktur', 'desc')
                ->lockForUpdate()
                ->first();
            
            $nextNumber = 1;
            
            if ($lastFaktur) {
                // Extract nomor urut dari no_faktur terakhir
                $lastNumber = (int) substr($lastFaktur->no_faktur, -3);
                $nextNumber = $lastNumber + 1;
            }
            
            // Format nomor faktur: PB-YYYYMMDD-XXX
            $noFaktur = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Double check untuk memastikan nomor belum ada
            while (DB::table('pembelian')->where('no_faktur', $noFaktur)->exists()) {
                $nextNumber++;
                $noFaktur = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            // Insert to pembelian table dengan nomor faktur yang baru di-generate
            $pembelianId = DB::table('pembelian')->insertGetId([
                'no_faktur' => $noFaktur,
                'tgl_beli' => $validated['tgl_beli'],
                'kd_rek' => $validated['kd_rek'],
                'kode_suplier' => $validated['kode_suplier'],
                'nip' => $validated['nip'],
                'kd_bangsal' => $validated['kd_bangsal'],
                'total1' => $validated['total1'],
                'potongan' => $validated['potongan'],
                'total2' => $validated['total2'],
                'ppn' => $validated['ppn'],
                'tagihan' => $validated['tagihan']
            ]);

            // Insert items to detailbeli table
            foreach ($validated['items'] as $item) {
                DB::table('detailbeli')->insert([
                    'no_faktur' => $noFaktur,
                    'kode_brng' => $item['kode_brng'],
                    'kode_sat' => $item['kode_sat'] ?? 'TAB',
                    'jumlah' => $item['jumlah'],
                    'h_beli' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'dis' => $item['dis'] ?? 0,
                    'besardis' => $item['besardis'] ?? 0,
                    'total' => $item['total'],
                    'no_batch' => $item['no_batch'] ?? 'BATCH001',
                    'jumlah2' => $item['jumlah'],
                    'kadaluarsa' => $item['kadaluarsa'] ?? '2026-12-31',
                    'created_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pembelian berhasil disimpan',
                'data' => [
                    'id' => $pembelianId,
                    'no_faktur' => $noFaktur
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving pembelian: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error saving pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nomor faktur dengan format PB-YYYYMMDD-XXX
     * XXX adalah nomor urut berdasarkan nomor terakhir yang ada
     */
    public function generateNoFaktur()
    {
        try {
            $todayFormatted = now()->format('Ymd');
            $prefix = 'PB-' . $todayFormatted . '-';
            
            // Gunakan database locking untuk menghindari race condition
            return DB::transaction(function () use ($prefix, $todayFormatted) {
                // Cari nomor faktur terakhir untuk hari ini dengan locking
                $lastFaktur = DB::table('pembelian')
                    ->where('no_faktur', 'LIKE', $prefix . '%')
                    ->orderBy('no_faktur', 'desc')
                    ->lockForUpdate()
                    ->first();
                
                $nextNumber = 1;
                
                if ($lastFaktur) {
                    // Extract nomor urut dari no_faktur terakhir
                    $lastNumber = (int) substr($lastFaktur->no_faktur, -3);
                    $nextNumber = $lastNumber + 1;
                }
                
                // Format nomor faktur: PB-YYYYMMDD-XXX
                $noFaktur = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                
                // Double check untuk memastikan nomor belum ada
                while (DB::table('pembelian')->where('no_faktur', $noFaktur)->exists()) {
                    $nextNumber++;
                    $noFaktur = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                }
                
                return response()->json([
                    'success' => true,
                    'no_faktur' => $noFaktur
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating no faktur: ' . $e->getMessage()
            ], 500);
        }
    }
}