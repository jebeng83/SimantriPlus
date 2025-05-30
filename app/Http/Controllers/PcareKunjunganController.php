<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BpjsService;
use Yajra\DataTables\Facades\DataTables;

class PcareKunjunganController extends Controller
{
    protected $bpjsService;

    public function __construct(BpjsService $bpjsService)
    {
        $this->bpjsService = $bpjsService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('pcare_kunjungan_umum as pku')
                ->join('reg_periksa as rp', 'pku.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->join('dokter as d', 'pku.kdDokter', '=', 'd.kd_dokter')
                ->select([
                    'pku.no_rawat',
                    'pku.noKunjungan',
                    'rp.tgl_registrasi as tglDaftar',
                    'pku.no_rkm_medis',
                    'p.nm_pasien',
                    'pku.noKartu',
                    'pku.kdPoli',
                    'pku.nmPoli',
                    'pku.keluhan',
                    'pku.kdSadar',
                    'pku.nmSadar',
                    'pku.sistole',
                    'pku.diastole',
                    'pku.beratBadan',
                    'pku.tinggiBadan',
                    'pku.respRate',
                    'pku.heartRate',
                    'pku.lingkarPerut',
                    'pku.terapi',
                    'pku.kdStatusPulang',
                    'pku.nmStatusPulang',
                    'pku.tglPulang',
                    'pku.kdDokter',
                    'd.nm_dokter as nmDokter',
                    'pku.kdDiag1',
                    'pku.nmDiag1',
                    'pku.kdDiag2',
                    'pku.nmDiag2',
                    'pku.kdDiag3',
                    'pku.nmDiag3',
                    'pku.status',
                    'pku.KdAlergiMakanan',
                    'pku.NmAlergiMakanan',
                    'pku.KdAlergiUdara',
                    'pku.NmAlergiUdara',
                    'pku.KdAlergiObat',
                    'pku.NmAlergiObat',
                    'pku.KdPrognosa',
                    'pku.NmPrognosa',
                    'pku.terapi_non_obat',
                    'pku.bmhp'
                ]);

            return DataTables::of($query)
                ->editColumn('tglDaftar', function($row) {
                    return date('d-m-Y', strtotime($row->tglDaftar));
                })
                ->editColumn('tglPulang', function($row) {
                    return $row->tglPulang ? date('d-m-Y', strtotime($row->tglPulang)) : '-';
                })
                ->editColumn('status', function($row) {
                    if ($row->status == 'Terkirim') {
                        return '<span class="badge badge-success">Terkirim</span>';
                    } else {
                        return '<span class="badge badge-danger">Gagal</span>';
                    }
                })
                ->addColumn('action', function($row) {
                    return '<button class="btn btn-sm btn-primary btn-kirim-ulang" data-id="' . $row->no_rawat . '">' .
                           '<i class="fas fa-sync-alt"></i> Kirim Ulang</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('Pcare.data-kunjungan-pcare');
    }

    public function show($noRawat)
    {
        $kunjungan = DB::table('pcare_kunjungan_umum')
            ->where('no_rawat', $noRawat)
            ->first();

        return response()->json($kunjungan);
    }

    public function kirimUlang($noRawat)
    {
        try {
            $kunjungan = DB::table('pcare_kunjungan_umum')
                ->where('no_rawat', $noRawat)
                ->first();

            if (!$kunjungan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data kunjungan tidak ditemukan'
                ], 404);
            }

            // Konversi stdClass object ke array
            $kunjunganArray = json_decode(json_encode($kunjungan), true);

            // Implementasi logika kirim ulang ke PCare
            $response = $this->bpjsService->kirimKunjungan($kunjunganArray);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mengirim ulang data kunjungan',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim ulang data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kirimUlangBatch(Request $request)
    {
        try {
            $noRawatList = $request->input('no_rawat', []);
            $results = [];

            foreach ($noRawatList as $noRawat) {
                $kunjungan = DB::table('pcare_kunjungan_umum')
                    ->where('no_rawat', $noRawat)
                    ->first();

                if ($kunjungan) {
                    // Konversi stdClass object ke array
                    $kunjunganArray = json_decode(json_encode($kunjungan), true);
                    
                    // Implementasi logika kirim ulang ke PCare
                    $response = $this->bpjsService->kirimKunjungan($kunjunganArray);
                    $results[$noRawat] = [
                        'status' => 'success',
                        'message' => 'Berhasil dikirim',
                        'response' => $response
                    ];
                } else {
                    $results[$noRawat] = [
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan'
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Proses kirim ulang batch selesai',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim ulang data batch: ' . $e->getMessage()
            ], 500);
        }
    }
}