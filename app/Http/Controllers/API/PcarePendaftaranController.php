<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PcarePendaftaranExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

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
            $pdf = PDF::loadView('exports.pcare-pendaftaran-pdf', [
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
} 