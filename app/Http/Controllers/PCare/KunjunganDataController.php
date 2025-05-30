<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class KunjunganDataController extends Controller
{
    public function getAdditionalData($tahun, $bulan, $tanggal, $nomor)
    {
        try {
            $noRawat = sprintf('%s/%s/%s/%06d', $tahun, $bulan, $tanggal, $nomor);
            
            Log::info('Mencari data tambahan untuk no_rawat:', [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'tanggal' => $tanggal,
                'nomor' => $nomor,
                'formatted_no_rawat' => $noRawat
            ]);

            // Ambil data poli dari reg_periksa
            $regPeriksa = DB::table('reg_periksa as rp')
                ->join('poliklinik as p', 'rp.kd_poli', '=', 'p.kd_poli')
                ->where('rp.no_rawat', $noRawat)
                ->select('rp.kd_poli', 'p.nm_poli')
                ->first();

            Log::info('Data reg_periksa:', ['reg_periksa' => $regPeriksa]);

            // Cari mapping poli jika ada
            $poli = null;
            if ($regPeriksa) {
                $poli = DB::table('maping_poliklinik_pcare')
                    ->where('kd_poli_rs', $regPeriksa->kd_poli)
                    ->select('kd_poli_pcare')
                    ->first();

                Log::info('Data mapping poli:', ['mapping' => $poli]);
            }

            // Ambil data diagnosa (hanya diagnosa utama)
            $diagnosa = DB::table('diagnosa_pasien')
                ->where('no_rawat', $noRawat)
                ->select('kd_penyakit as kd_penyakit1')
                ->first();

            Log::info('Data diagnosa yang ditemukan:', ['diagnosa' => $diagnosa]);

            // Ambil data resep dengan detail obat
            $resepDetail = DB::table('resep_obat as ro')
                ->join('resep_dokter as rd', 'ro.no_resep', '=', 'rd.no_resep')
                ->leftJoin('databarang as db', 'rd.kode_brng', '=', 'db.kode_brng')
                ->where('ro.no_rawat', $noRawat)
                ->select(
                    'ro.no_resep',
                    'rd.kode_brng',
                    'db.nama_brng',
                    'rd.jml',
                    'rd.aturan_pakai'
                )
                ->get();

            Log::info('Detail resep dan obat:', ['resep_detail' => $resepDetail]);

            // Format resep untuk response
            $resep = null;
            if ($resepDetail->isNotEmpty()) {
                $terapi = $resepDetail->map(function($item) {
                    $namaObat = $item->nama_brng ?? $item->kode_brng;
                    return "{$namaObat} {$item->jml}x{$item->aturan_pakai}";
                })->implode(', ');

                $resep = (object)['terapi' => $terapi];
            }

            return response()->json([
                'success' => true,
                'poli' => $poli ?? (object)['kd_poli_pcare' => null],
                'diagnosa' => [
                    'kd_penyakit1' => $diagnosa->kd_penyakit1 ?? null,
                    'kd_penyakit2' => null,
                    'kd_penyakit3' => null
                ],
                'resep' => $resep ?? (object)['terapi' => null]
            ]);

        } catch (\Exception $e) {
            Log::error('Error saat mengambil data tambahan:', [
                'error' => $e->getMessage(),
                'no_rawat' => $noRawat ?? 'undefined',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}
 