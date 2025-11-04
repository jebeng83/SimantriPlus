<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RegPeriksa;
use App\Models\AntriPoli;

class AntriPoliController extends Controller
{
    /**
     * Tampilkan halaman React Antri Poli
     */
    public function index()
    {
        try {
            $setting = DB::table('setting')->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email', 'logo')->first();
        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil setting untuk antri-poli: ' . $e->getMessage());
            $setting = null;
        }
        return view('react.antri-poli', compact('setting'));
    }

    /**
     * API: Data display untuk Antri Poli
     * - pasien yang sedang dipanggil
     * - agregasi per poliklinik dan per dokter untuk hari ini
     */
    public function getDisplayData(Request $request)
    {
        try {
            $tanggal = date('Y-m-d');

            // Ambil seluruh data registrasi hari ini dengan join yang diperlukan
            $rows = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('antripoli', 'reg_periksa.no_rawat', '=', 'antripoli.no_rawat')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.stts',
                    'reg_periksa.jam_reg',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'antripoli.status as status_poli'
                )
                ->orderBy('poliklinik.nm_poli', 'asc')
                ->orderBy('reg_periksa.no_reg', 'asc')
                ->get();

            // Pasien sedang dipanggil: ambil dari antripoli.status = '1' dan join sesuai kebutuhan
            $dipanggil = DB::table('antripoli')
                ->join('reg_periksa', 'antripoli.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('dokter', 'antripoli.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('poliklinik', 'antripoli.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->where('antripoli.status', '1')
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'antripoli.kd_dokter as kd_dokter',
                    'antripoli.kd_poli as kd_poli'
                )
                ->orderBy('reg_periksa.no_reg', 'desc')
                ->first();

            // Jika belum ada yang dipanggil, fallback ke status 'Dipanggil' di reg_periksa atau yang pertama 'Belum'
            if (!$dipanggil) {
                $dipanggil = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->where('reg_periksa.tgl_registrasi', $tanggal)
                    ->whereIn('reg_periksa.stts', ['Dipanggil', 'Belum'])
                    ->select(
                        'reg_periksa.no_reg',
                        'reg_periksa.no_rawat',
                        'pasien.no_rkm_medis',
                        'pasien.nm_pasien',
                        'poliklinik.nm_poli',
                        'dokter.nm_dokter',
                        'reg_periksa.stts'
                    )
                    ->orderByRaw("FIELD(reg_periksa.stts, 'Dipanggil', 'Belum')")
                    ->orderBy('poliklinik.nm_poli', 'asc')
                    ->orderBy('reg_periksa.no_reg', 'asc')
                    ->first();
                if ($dipanggil) {
                    $dipanggil->status = 'Dipanggil';
                }
            } else {
                $dipanggil->status = 'Dipanggil';
            }

            // Agregasi per poliklinik dan per dokter
            $poliGroups = [];
            foreach ($rows as $row) {
                $poliKey = $row->kd_poli;
                if (!isset($poliGroups[$poliKey])) {
                    $poliGroups[$poliKey] = [
                        'kd_poli' => $row->kd_poli,
                        'nm_poli' => $row->nm_poli,
                        'total' => 0,
                        'menunggu' => 0,
                        'selesai' => 0,
                        'dipanggil' => 0,
                        'next_no_reg' => null,
                        'dokters' => []
                    ];
                }

                $poliGroups[$poliKey]['total']++;
                if ($row->status_poli === '1' || $row->stts === 'Dipanggil') {
                    $poliGroups[$poliKey]['dipanggil']++;
                } elseif ($row->stts === 'Sudah') {
                    $poliGroups[$poliKey]['selesai']++;
                } elseif ($row->stts === 'Belum') {
                    $poliGroups[$poliKey]['menunggu']++;
                    // Tentukan next nomor (Belum paling kecil)
                    if ($poliGroups[$poliKey]['next_no_reg'] === null || (int)$row->no_reg < (int)$poliGroups[$poliKey]['next_no_reg']) {
                        $poliGroups[$poliKey]['next_no_reg'] = $row->no_reg;
                    }
                }

                // Dokter grouping
                $dokKey = $row->kd_dokter;
                if (!isset($poliGroups[$poliKey]['dokters'][$dokKey])) {
                    $poliGroups[$poliKey]['dokters'][$dokKey] = [
                        'kd_dokter' => $row->kd_dokter,
                        'nm_dokter' => $row->nm_dokter,
                        'total' => 0,
                        'menunggu' => 0,
                        'selesai' => 0,
                        'dipanggil' => 0,
                        'next_no_reg' => null
                    ];
                }
                $poliGroups[$poliKey]['dokters'][$dokKey]['total']++;
                if ($row->status_poli === '1' || $row->stts === 'Dipanggil') {
                    $poliGroups[$poliKey]['dokters'][$dokKey]['dipanggil']++;
                } elseif ($row->stts === 'Sudah') {
                    $poliGroups[$poliKey]['dokters'][$dokKey]['selesai']++;
                } elseif ($row->stts === 'Belum') {
                    $poliGroups[$poliKey]['dokters'][$dokKey]['menunggu']++;
                    if ($poliGroups[$poliKey]['dokters'][$dokKey]['next_no_reg'] === null || (int)$row->no_reg < (int)$poliGroups[$poliKey]['dokters'][$dokKey]['next_no_reg']) {
                        $poliGroups[$poliKey]['dokters'][$dokKey]['next_no_reg'] = $row->no_reg;
                    }
                }
            }

            // Normalisasi dokters menjadi array index
            $poliGroupsOut = array_values(array_map(function ($grp) {
                $grp['dokters'] = array_values($grp['dokters']);
                return $grp;
            }, $poliGroups));

            return response()->json([
                'success' => true,
                'dipanggil' => $dipanggil,
                'groups' => $poliGroupsOut,
                'count' => count($rows),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            Log::error('Error getDisplayData AntriPoli: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}