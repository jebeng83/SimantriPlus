<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WilayahController extends Controller
{
    public function getPropinsi()
    {
        try {
            $rows = DB::table('propinsi')
                ->select('kd_prop', 'nm_prop')
                ->orderBy('nm_prop')
                ->get();

            return response()->json($rows);
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

            $query = DB::table('kabupaten')->select('kd_kab', 'nm_kab');

            // Some installations may not have kd_prop in kabupaten
            if ($kdProp && Schema::hasColumn('kabupaten', 'kd_prop')) {
                $query->where('kd_prop', $kdProp);
            }

            $rows = $query->orderBy('nm_kab')->get();

            return response()->json($rows);
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

            $query = DB::table('kecamatan')->select('kd_kec', 'nm_kec');
            if ($kdKab && Schema::hasColumn('kecamatan', 'kd_kab')) {
                $query->where('kd_kab', $kdKab);
            }
            $rows = $query->orderBy('nm_kec')->get();

            return response()->json($rows);
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

            $query = DB::table('kelurahan')->select('kd_kel', 'nm_kel');
            if ($kdKec && Schema::hasColumn('kelurahan', 'kd_kec')) {
                $query->where('kd_kec', $kdKec);
            }
            $rows = $query->orderBy('nm_kel')->get();

            return response()->json($rows);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data kelurahan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kelurahan: ' . $e->getMessage()
            ], 500);
        }
    }
}