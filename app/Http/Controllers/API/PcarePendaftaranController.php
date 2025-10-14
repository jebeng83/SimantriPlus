<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PcarePendaftaranExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PcarePendaftaranController extends Controller
{
    /**
     * Mendapatkan data pendaftaran PCare untuk DataTables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            $query = DB::table('pcare_pendaftaran');

            // Filter berdasarkan tanggal
            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $query->whereDate('tglDaftar', $request->tanggal);
            }

            // Filter berdasarkan status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $data = $query->get();

            // Format tanggal untuk kebutuhan delete action
            foreach ($data as $row) {
                // Format tanggal dari YYYY-MM-DD menjadi DD-MM-YYYY
                $parts = explode('-', $row->tglDaftar);
                if (count($parts) === 3) {
                    $row->tglDaftar_formatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                } else {
                    $row->tglDaftar_formatted = $row->tglDaftar;
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="btn-group">
                        <a href="javascript:void(0)" class="btn btn-sm btn-info btn-detail" data-id="'.$row->no_rawat.'">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-delete" 
                            data-nokartu="'.$row->noKartu.'" 
                            data-tgldaftar="'.$row->tglDaftar_formatted.'" 
                            data-nourut="'.$row->noUrut.'">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data pendaftaran PCare', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pendaftaran PCare'
            ], 500);
        }
    }

    /**
     * Mendapatkan detail pendaftaran PCare berdasarkan no_rawat
     *
     * @param string $no_rawat
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetail($no_rawat)
    {
        try {
            $pendaftaran = DB::table('pcare_pendaftaran')
                ->where('no_rawat', $no_rawat)
                ->first();

            if (!$pendaftaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pendaftaran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pendaftaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil detail pendaftaran PCare', [
                'no_rawat' => $no_rawat,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail pendaftaran PCare'
            ], 500);
        }
    }

    /**
     * Export data pendaftaran PCare ke Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        try {
            $tanggal = $request->input('tanggal');
            $status = $request->input('status');
            
            $export = new PcarePendaftaranExport($tanggal, $status);
            $filename = 'data_pendaftaran_pcare_' . date('YmdHis') . '.xlsx';
            
            return Excel::download($export, $filename);
        } catch (\Exception $e) {
            Log::error('Error saat export Excel pendaftaran PCare', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat export data ke Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export data pendaftaran PCare ke PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            $tanggal = $request->input('tanggal');
            $status = $request->input('status');
            
            // Query data
            $query = DB::table('pcare_pendaftaran');
            
            if (!empty($tanggal)) {
                $query->whereDate('tglDaftar', $tanggal);
            }
            
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            $data = $query->get();
            
            // Memformat data tanggal untuk tampilan
            foreach ($data as $row) {
                $parts = explode('-', $row->tglDaftar);
                if (count($parts) === 3) {
                    $row->tglDaftar_formatted = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                } else {
                    $row->tglDaftar_formatted = $row->tglDaftar;
                }
                
                // Format kunjSakit
                $row->kunjSakit_formatted = ($row->kunjSakit === 'true') ? 'Ya' : 'Tidak';
                
                // Format tempat kunjungan
                switch ($row->kdTkp) {
                    case '10':
                        $row->tkp_formatted = 'Rawat Jalan (RJTP)';
                        break;
                    case '20':
                        $row->tkp_formatted = 'Rawat Inap (RITP)';
                        break;
                    case '50':
                        $row->tkp_formatted = 'Promotif Preventif';
                        break;
                    default:
                        $row->tkp_formatted = $row->kdTkp;
                        break;
                }
            }
            
            $filename = 'data_pendaftaran_pcare_' . date('YmdHis') . '.pdf';
            
            // Generate PDF
            $pdf = Pdf::loadView('exports.pcare-pendaftaran-pdf', [
                'data' => $data,
                'tanggal' => $tanggal ? date('d-m-Y', strtotime($tanggal)) : 'Semua',
                'status' => $status ?: 'Semua',
                'tanggal_cetak' => date('d-m-Y H:i:s')
            ]);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error saat export PDF pendaftaran PCare', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat export data ke PDF: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan daftar registrasi (reg_periksa) dengan status pendaftaran PCare,
     * sekaligus ringkasan perbandingan total registrasi vs yang sukses terkirim.
     *
     * Filter:
     * - start_date (YYYY-MM-DD) opsional
     * - end_date (YYYY-MM-DD) opsional
     * - status (Terkirim|Batal|Belum) opsional
     */
    public function getStatusRegistrations(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $statusFilter = $request->input('status');

            // Subquery pemeriksaan_ralan (mengambil baris terakhir per no_rawat)
            $latestRalan = DB::table('pemeriksaan_ralan as pr')
                ->join(DB::raw('(SELECT no_rawat, MAX(CONCAT(tgl_perawatan, " ", IFNULL(jam_rawat, "00:00:00"))) AS maxdt FROM pemeriksaan_ralan GROUP BY no_rawat) AS mx'), function ($join) {
                    $join->on('mx.no_rawat', '=', 'pr.no_rawat');
                })
                ->whereRaw('CONCAT(pr.tgl_perawatan, " ", IFNULL(pr.jam_rawat, "00:00:00")) = mx.maxdt')
                ->select([
                    'pr.no_rawat',
                    'pr.keluhan',
                    'pr.tinggi',
                    'pr.berat',
                    'pr.lingkar_perut',
                    'pr.tensi',
                    'pr.nadi',
                    'pr.respirasi',
                    'pr.suhu_tubuh',
                    'pr.instruksi',
                ]);

            // Subquery diagnosa pasien (gabungkan semua kd_penyakit per no_rawat)
            $diagnosaSub = DB::table('diagnosa_pasien')
                ->select('no_rawat', DB::raw('GROUP_CONCAT(kd_penyakit ORDER BY prioritas SEPARATOR ", ") AS kode_diagnosa'))
                ->groupBy('no_rawat');

            // Query dasar dari reg_periksa
            $baseQuery = DB::table('reg_periksa as r')
                ->leftJoin('pcare_pendaftaran as p', 'p.no_rawat', '=', 'r.no_rawat')
                ->leftJoin('pasien as ps', 'ps.no_rkm_medis', '=', 'r.no_rkm_medis')
                ->leftJoin('poliklinik as pl', 'pl.kd_poli', '=', 'r.kd_poli')
                ->leftJoin('penjab as pj', 'pj.kd_pj', '=', 'r.kd_pj')
                ->leftJoin('pcare_kunjungan_umum as ku', 'ku.no_rawat', '=', 'r.no_rawat')
                ->leftJoinSub($latestRalan, 'prl', function ($join) {
                    $join->on('prl.no_rawat', '=', 'r.no_rawat');
                })
                ->leftJoinSub($diagnosaSub, 'dp', function ($join) {
                    $join->on('dp.no_rawat', '=', 'r.no_rawat');
                })
                ->select([
                    'r.no_rawat',
                    'r.tgl_registrasi',
                    'r.jam_reg',
                    'r.no_rkm_medis',
                    DB::raw('COALESCE(ps.nm_pasien, "-") as nm_pasien'),
                    'r.kd_poli',
                    DB::raw('COALESCE(pl.nm_poli, r.kd_poli) as nm_poli'),
                    DB::raw('COALESCE(pj.png_jawab, r.kd_pj) as penjamin'),
                    DB::raw('COALESCE(p.status, "Belum") as status_pcare'),
                    // Kunjungan PCare
                    DB::raw('COALESCE(ku.noKunjungan, "") as no_kunjungan'),
                    DB::raw('COALESCE(ku.status, "") as status_kunjungan'),
                    // Pemeriksaan ralan
                    DB::raw('COALESCE(prl.keluhan, "") as keluhan'),
                    DB::raw('COALESCE(prl.tinggi, "") as tinggi'),
                    DB::raw('COALESCE(prl.berat, "") as berat'),
                    DB::raw('COALESCE(prl.lingkar_perut, "") as lingkar_perut'),
                    DB::raw('COALESCE(prl.tensi, "") as tensi'),
                    DB::raw('COALESCE(prl.nadi, "") as nadi'),
                    DB::raw('COALESCE(prl.respirasi, "") as respirasi'),
                    DB::raw('COALESCE(prl.suhu_tubuh, "") as suhu_tubuh'),
                    DB::raw('COALESCE(prl.instruksi, "") as instruksi'),
                    // Diagnosa
                    DB::raw('COALESCE(dp.kode_diagnosa, "") as kode_diagnosa'),
                ])
                ->whereIn('r.kd_pj', ['BPJ', 'NON', 'PBI']);

            if (!empty($startDate)) {
                $baseQuery->whereDate('r.tgl_registrasi', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $baseQuery->whereDate('r.tgl_registrasi', '<=', $endDate);
            }

            if (!empty($statusFilter)) {
                if (strtolower($statusFilter) === 'belum') {
                    $baseQuery->where(function ($q) {
                        $q->whereNull('p.status')->orWhere('p.status', 'Belum');
                    });
                } else {
                    $baseQuery->where('p.status', $statusFilter);
                }
            }

            $rows = $baseQuery->orderBy('r.tgl_registrasi', 'desc')->orderBy('r.jam_reg', 'desc')->get();

            $total = $rows->count();
            $sukses = $rows->where('status_pcare', 'Terkirim')->count();
            $batal = $rows->where('status_pcare', 'Batal')->count();
            $belum = $total - $sukses - $batal;
            $persentase = $total > 0 ? round(($sukses / $total) * 100, 2) : 0;

            // Sukses Kunjungan PCare: gunakan indikator no_kunjungan terisi
            $suksesKunjungan = $rows->filter(function ($r) {
                return !empty($r->no_kunjungan);
            })->count();

            // Gap perhitungan
            $gapRegVsDaftar = max($total - $sukses, 0);
            $gapDaftarVsKunjungan = max($sukses - $suksesKunjungan, 0);
            $gapRegVsKunjungan = max($total - $suksesKunjungan, 0);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total' => $total,
                    'terkirim' => $sukses,
                    'batal' => $batal,
                    'belum' => $belum,
                    'persentase' => $persentase,
                    // tambahan untuk kartu ringkasan
                    'sukses_kunjungan' => $suksesKunjungan,
                    'gap_reg_vs_pcare' => $gapRegVsDaftar,
                    'gap_pcare_vs_kunjungan' => $gapDaftarVsKunjungan,
                    'gap_reg_vs_kunjungan' => $gapRegVsKunjungan,
                ],
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil status registrasi PCare', [
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil status registrasi PCare',
            ], 500);
        }
    }
}