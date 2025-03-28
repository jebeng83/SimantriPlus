<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaktorResikoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the Faktor Resiko page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Ambil filter posyandu jika ada
        $posyandu_filter = $request->input('posyandu');
        
        // Ambil filter desa jika ada
        $desa_filter = $request->input('desa');
        
        // Ambil filter periode (default: bulan)
        $periode_filter = $request->input('periode', 'bulan');
        
        // Ambil daftar desa/kelurahan dari database
        $daftar_desa = $this->getDaftarDesa();
        
        // Ambil daftar posyandu dari database berdasarkan filter desa jika ada
        $daftar_posyandu = $this->getDaftarPosyandu($desa_filter);
        
        // Ambil data hasil laboratorium
        $hasil_lab = $this->getHasilLaboratorium($posyandu_filter, $desa_filter, $periode_filter);
        
        return view('ilp.faktor_resiko', [
            'daftar_desa' => $daftar_desa,
            'daftar_posyandu' => $daftar_posyandu,
            'posyandu_filter' => $posyandu_filter,
            'desa_filter' => $desa_filter,
            'periode_filter' => $periode_filter,
            'hemoglobin' => $hasil_lab['hemoglobin'],
            'kolesterol' => $hasil_lab['kolesterol'],
            'asam_urat' => $hasil_lab['asam_urat'],
            'gula_darah' => $hasil_lab['gula_darah'],
            'trigliserida' => $hasil_lab['trigliserida'],
            'hba1c' => $hasil_lab['hba1c'],
            'imt' => $hasil_lab['imt'],
            'tekanan_darah' => $hasil_lab['tekanan_darah']
        ]);
    }
    
    /**
     * Get daftar posyandu berdasarkan desa
     * 
     * @param string|null $desa Filter berdasarkan desa
     * @return \Illuminate\Support\Collection
     */
    private function getDaftarPosyandu($desa = null)
    {
        $query = DB::table('data_posyandu')
            ->select('nama_posyandu')
            ->distinct();
            
        if ($desa) {
            $query->where('desa', $desa);
        }
        
        return $query->orderBy('nama_posyandu', 'asc')->get();
    }
    
    /**
     * Get daftar desa/kelurahan
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getDaftarDesa()
    {
        return DB::table('data_posyandu')
            ->select('desa')
            ->distinct()
            ->orderBy('desa', 'asc')
            ->get();
    }
    
    /**
     * Get hasil laboratorium
     * 
     * @param string|null $posyandu Filter berdasarkan posyandu
     * @param string|null $desa Filter berdasarkan desa
     * @param string $periode Filter berdasarkan periode (minggu, bulan, tahun)
     * @return array
     */
    private function getHasilLaboratorium($posyandu = null, $desa = null, $periode = 'bulan')
    {
        // Query dasar untuk mengambil data dari ilp_dewasa
        $query = DB::table('ilp_dewasa as id')
            ->join('pasien as p', 'id.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_posyandu as dp', 'id.data_posyandu', '=', 'dp.nama_posyandu')
            ->select(
                'id.gds',
                'id.asam_urat',
                'id.kolesterol',
                'id.trigliserida',
                'id.ureum',
                'id.kreatinin',
                'id.imt',
                'id.td'
            )
            ->whereNotNull('id.data_posyandu')
            ->where('id.data_posyandu', '!=', '')
            ->where('id.data_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('id.data_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Tentukan interval waktu berdasarkan periode
        switch ($periode) {
            case 'minggu':
                $interval = 12; // 12 minggu terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' WEEK)'));
                break;
                
            case 'tahun':
                $interval = 5; // 5 tahun terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' YEAR)'));
                break;
                
            case 'bulan':
            default:
                $interval = 6; // 6 bulan terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' MONTH)'));
                break;
        }
        
        $result = $query->get();
        
        // Inisialisasi data untuk kategori hasil lab
        $hemoglobin = [
            'rendah' => 0,
            'normal' => 0,
            'tinggi' => 0
        ];
        
        $kolesterol = [
            'normal' => 0,
            'batas_tinggi' => 0,
            'tinggi' => 0
        ];
        
        $asam_urat = [
            'rendah' => 0,
            'normal' => 0,
            'tinggi' => 0
        ];
        
        $gula_darah = [
            'normal' => 0,
            'prediabetes' => 0,
            'diabetes' => 0
        ];
        
        $trigliserida = [
            'normal' => 0,
            'batas_tinggi' => 0,
            'tinggi' => 0,
            'sangat_tinggi' => 0
        ];
        
        $hba1c = [
            'normal' => 0,
            'prediabetes' => 0,
            'diabetes' => 0
        ];
        
        // Tambahkan kategori untuk IMT
        $imt = [
            'kurus' => 0,
            'normal' => 0,
            'kelebihan_bb' => 0,
            'obesitas' => 0
        ];
        
        // Tambahkan kategori untuk Tekanan Darah
        $tekanan_darah = [
            'normal' => 0,
            'prahipertensi' => 0,
            'hipertensi1' => 0,
            'hipertensi2' => 0,
            'hst' => 0 // Hipertensi Sistolik Terisolasi
        ];
        
        // Hitung jumlah untuk setiap kategori
        foreach ($result as $row) {
            // Kategorisasi Hemoglobin (asumsi data hemoglobin tidak ada di tabel, ini hanya contoh)
            // Dalam implementasi nyata, perlu disesuaikan dengan data yang tersedia
            
            // Kategorisasi Kolesterol
            if ($row->kolesterol) {
                $kolesterol_value = (float) $row->kolesterol;
                if ($kolesterol_value < 200) {
                    $kolesterol['normal']++;
                } elseif ($kolesterol_value >= 200 && $kolesterol_value <= 239) {
                    $kolesterol['batas_tinggi']++;
                } elseif ($kolesterol_value >= 240) {
                    $kolesterol['tinggi']++;
                }
            }
            
            // Kategorisasi Asam Urat
            if ($row->asam_urat) {
                $asam_urat_value = (float) $row->asam_urat;
                if ($asam_urat_value < 3.5) {
                    $asam_urat['rendah']++;
                } elseif ($asam_urat_value >= 3.5 && $asam_urat_value <= 7.2) {
                    $asam_urat['normal']++;
                } elseif ($asam_urat_value > 7.2) {
                    $asam_urat['tinggi']++;
                }
            }
            
            // Kategorisasi Gula Darah
            if ($row->gds) {
                $gds_value = (float) $row->gds;
                if ($gds_value < 100) {
                    $gula_darah['normal']++;
                } elseif ($gds_value >= 100 && $gds_value <= 125) {
                    $gula_darah['prediabetes']++;
                } elseif ($gds_value >= 126) {
                    $gula_darah['diabetes']++;
                }
            }
            
            // Kategorisasi Trigliserida
            if ($row->trigliserida) {
                $trigliserida_value = (float) $row->trigliserida;
                if ($trigliserida_value < 150) {
                    $trigliserida['normal']++;
                } elseif ($trigliserida_value >= 150 && $trigliserida_value <= 199) {
                    $trigliserida['batas_tinggi']++;
                } elseif ($trigliserida_value >= 200 && $trigliserida_value <= 499) {
                    $trigliserida['tinggi']++;
                } elseif ($trigliserida_value >= 500) {
                    $trigliserida['sangat_tinggi']++;
                }
            }
            
            // HbA1c (asumsi data HbA1c tidak ada di tabel, ini hanya contoh)
            // Dalam implementasi nyata, perlu disesuaikan dengan data yang tersedia
            
            // Kategorisasi IMT
            if ($row->imt) {
                $imt_value = (float) $row->imt;
                
                if ($imt_value < 18.5) {
                    $imt['kurus']++;
                } elseif ($imt_value >= 18.5 && $imt_value <= 24.9) {
                    $imt['normal']++;
                } elseif ($imt_value >= 25 && $imt_value <= 29.9) {
                    $imt['kelebihan_bb']++;
                } elseif ($imt_value >= 30) {
                    $imt['obesitas']++;
                }
            }
            
            // Kategorisasi Tekanan Darah
            if ($row->td) {
                // Format tekanan darah biasanya "sistole/diastole", misalnya "120/80"
                $td_parts = explode('/', $row->td);
                
                if (count($td_parts) == 2) {
                    $sistole = (int) $td_parts[0];
                    $diastole = (int) $td_parts[1];
                    
                    if ($sistole < 120 && $diastole < 80) {
                        $tekanan_darah['normal']++;
                    } elseif (($sistole >= 120 && $sistole <= 139) || ($diastole >= 80 && $diastole <= 89)) {
                        $tekanan_darah['prahipertensi']++;
                    } elseif (($sistole >= 140 && $sistole <= 159) || ($diastole >= 90 && $diastole <= 99)) {
                        $tekanan_darah['hipertensi1']++;
                    } elseif ($sistole >= 160 || $diastole >= 100) {
                        $tekanan_darah['hipertensi2']++;
                    } elseif ($sistole > 140 && $diastole < 90) {
                        $tekanan_darah['hst']++;
                    }
                }
            }
        }
        
        return [
            'hemoglobin' => $hemoglobin,
            'kolesterol' => $kolesterol,
            'asam_urat' => $asam_urat,
            'gula_darah' => $gula_darah,
            'trigliserida' => $trigliserida,
            'hba1c' => $hba1c,
            'imt' => $imt,
            'tekanan_darah' => $tekanan_darah
        ];
    }
    
    /**
     * Get posyandu berdasarkan desa (untuk AJAX)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosyandu(Request $request)
    {
        // Debug informasi
        \Log::info('Request getPosyandu diterima', [
            'desa' => $request->input('desa'),
            'user_agent' => $request->header('User-Agent')
        ]);
        
        $desa = $request->input('desa');
        
        $query = DB::table('data_posyandu')
            ->select('nama_posyandu')
            ->distinct();
            
        if ($desa) {
            $query->where('desa', $desa);
        }
        
        $posyandu = $query->orderBy('nama_posyandu', 'asc')->get();
        
        \Log::info('Response getPosyandu', [
            'count' => count($posyandu),
            'data' => $posyandu
        ]);
        
        return response()->json($posyandu);
    }
} 