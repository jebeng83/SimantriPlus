<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AntrianDisplayController extends Controller
{
    public function display()
    {
        // Ambil setting aplikasi
        $setting = DB::table('setting')->first();
        
        return view('antrian-display', [
            'setting' => $setting
        ]);
    }

    public function getDataDisplay()
    {
        try {
            // Aktifkan query logging
            DB::enableQueryLog();

            // Ambil data antrian untuk hari ini
            $tanggal = date('Y-m-d');
            
            Log::info('Fetching antrian data for date: ' . $tanggal);
            
            // Query untuk data antrian
            $antrian = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->leftJoin('antripoli', 'reg_periksa.no_rawat', '=', 'antripoli.no_rawat')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->select(
                    'reg_periksa.no_reg',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'reg_periksa.no_rawat',
                    DB::raw('CASE 
                        WHEN antripoli.status = "1" THEN "DIPANGGIL"
                        WHEN reg_periksa.stts = "Sudah" THEN "SELESAI"
                        WHEN reg_periksa.stts = "Batal" THEN "BATAL"
                        ELSE "MENUNGGU"
                    END as status')
                )
                ->orderBy('reg_periksa.no_reg', 'asc')
                ->get();

            // Log jumlah data yang ditemukan
            Log::info('Found ' . $antrian->count() . ' antrian records');
            
            // Debug: Log struktur data
            Log::debug('Sample antrian data:', [
                'first_record' => $antrian->first(),
                'data_structure' => array_keys((array) $antrian->first())
            ]);

            // Query untuk pasien yang sedang dipanggil
            $dipanggil = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('antripoli', 'reg_periksa.no_rawat', '=', 'antripoli.no_rawat')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->where('antripoli.status', '1')
                ->select(
                    'reg_periksa.no_reg',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'reg_periksa.no_rawat'
                )
                ->orderBy('reg_periksa.no_reg', 'desc')
                ->first();

            // Log queries yang dijalankan
            $queries = DB::getQueryLog();
            foreach ($queries as $query) {
                Log::debug('Executed query:', [
                    'sql' => $query['query'],
                    'bindings' => $query['bindings'],
                    'time' => $query['time']
                ]);
            }

            return response()->json([
                'success' => true,
                'count' => $antrian->count(),
                'antrian' => $antrian,
                'dipanggil' => $dipanggil,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getDataDisplay: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
} 