<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class PermintaanLab extends Component
{
    use EnkripsiData;
    public $noRawat, $encrypNoRawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        // Pastikan kita mendapatkan nilai yang valid
        if (empty($noRawat)) {
            \Illuminate\Support\Facades\Log::warning('PermintaanLab initialized with empty noRawat');
            $this->noRawat = date('Y/m/d') . '/000001'; // Default fallback
        } else {
            $this->noRawat = $noRawat;
        }
        
        // Coba dekripsi jika sudah terenkripsi
        try {
            $decodedNoRawat = $this->decryptData($this->noRawat);
            
            // Cek apakah hasil decode valid (harus memiliki format yang benar)
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedNoRawat)) {
                $this->noRawat = $decodedNoRawat;
                \Illuminate\Support\Facades\Log::info('Berhasil mendecode noRawat terenkripsi', [
                    'original' => $noRawat,
                    'decoded' => $decodedNoRawat
                ]);
            }
        } catch (\Exception $e) {
            // Jika gagal, gunakan nilai original
            \Illuminate\Support\Facades\Log::info('Menggunakan noRawat original (gagal decode): ' . $this->noRawat);
        }
        
        // Selalu enkripsi untuk kebutuhan form
        $this->encrypNoRawat = $this->encryptData($this->noRawat);
        
        // Log untuk tracking
        \Illuminate\Support\Facades\Log::info('PermintaanLab Component initialized with:', [
            'noRawat_input' => $noRawat,
            'noRawat_processed' => $this->noRawat,
            'noRawat_encrypted' => $this->encrypNoRawat
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.permintaan-lab',[
            'pemeriksaan' => $this->getPemeriksaanLab($this->noRawat),
            'encrypNoRawat' => $this->encrypNoRawat
        ]);
    }

    public function getPemeriksaanLab($noRawat)
    {
        try {
            // Log parameter input
            \Illuminate\Support\Facades\Log::info('getPemeriksaanLab input parameter', [
                'noRawat' => $noRawat,
                'type' => gettype($noRawat),
                'length' => strlen($noRawat)
            ]);
            
            // Cek apakah noRawat adalah base64 dan perlu didekode
            $decodedNoRawat = $noRawat;
            if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $noRawat)) {
                try {
                    $tempDecoded = base64_decode($noRawat, true);
                    if ($tempDecoded !== false && strpos($tempDecoded, '/') !== false) {
                        $decodedNoRawat = $tempDecoded;
                        \Illuminate\Support\Facades\Log::info('Berhasil decode base64 noRawat', [
                            'original' => $noRawat,
                            'decoded' => $decodedNoRawat
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Gagal decode base64 noRawat: ' . $e->getMessage());
                }
            }
            
            // Bersihkan format no_rawat
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable jika ada
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $decodedNoRawat);
            
            // Jika setelah dibersihkan kosong, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($decodedNoRawat)) {
                $cleanNoRawat = $decodedNoRawat;
            }
            
            // Array variasi pencarian no_rawat untuk meningkatkan kemungkinan menemukan data
            $variasi = [
                $cleanNoRawat, 
                urldecode($cleanNoRawat),
                str_replace(' ', '', $cleanNoRawat)
            ];
            
            // Cek apakah format no_rawat sesuai pola YYYY/MM/DD/XXXXXX
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                // Gunakan format yang sudah benar
                $noRawatFormatted = $cleanNoRawat;
            } else {
                // Coba ekstrak tanggal hari ini + 6 digit terakhir dari no_rawat
                $today = date('Y/m/d');
                if (preg_match('/\d{6}$/', $cleanNoRawat, $matches)) {
                    $lastSixDigits = $matches[0];
                    $noRawatFormatted = $today . '/' . $lastSixDigits;
                    $variasi[] = $noRawatFormatted;
                } else {
                    $noRawatFormatted = $cleanNoRawat;
                }
            }
            
            // Query untuk mendapatkan data permintaan lab dengan berbagai variasi no_rawat
            foreach ($variasi as $varNoRawat) {
                $queryResult = DB::table('permintaan_lab')
                    ->where('no_rawat', $varNoRawat)
                    ->orderBy('tgl_permintaan', 'desc')
                    ->orderBy('jam_permintaan', 'desc')
                    ->get();
                
                if ($queryResult->count() > 0) {
                    \Illuminate\Support\Facades\Log::info('Berhasil mendapatkan data dengan variasi no_rawat', [
                        'variasi' => $varNoRawat,
                        'jumlah' => $queryResult->count()
                    ]);
                    return $queryResult;
                }
            }
            
            // Jika tidak ditemukan, coba cari dengan LEFT JOIN pada reg_periksa untuk memastikan
            $queryByRM = DB::table('permintaan_lab')
                ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('reg_periksa.no_rkm_medis', $cleanNoRawat) // Coba cari berdasarkan no_rm
                ->orderBy('permintaan_lab.tgl_permintaan', 'desc')
                ->orderBy('permintaan_lab.jam_permintaan', 'desc')
                ->select('permintaan_lab.*')
                ->get();
                
            if ($queryByRM->count() > 0) {
                \Illuminate\Support\Facades\Log::info('Berhasil mendapatkan data dengan no_rkm_medis', [
                    'no_rkm_medis' => $cleanNoRawat,
                    'jumlah' => $queryByRM->count()
                ]);
                return $queryByRM;
            }
            
            // Log jika tidak menemukan data
            \Illuminate\Support\Facades\Log::warning('Tidak menemukan data permintaan lab untuk no_rawat', [
                'cleanNoRawat' => $cleanNoRawat,
                'variasi' => $variasi
            ]);
            
            return collect();
        } catch (\Exception $e) {
            // Log error
            \Illuminate\Support\Facades\Log::error('Error pada getPemeriksaanLab: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    public static function getDetailPemeriksaan($noOrder)
    {
        try {
            // Log untuk tracking
            \Illuminate\Support\Facades\Log::info('Getting detail pemeriksaan for noOrder: ' . $noOrder);
            
            // Buat query builder untuk mengambil data dari permintaan_pemeriksaan_lab dan jns_perawatan_lab
            $pemeriksaanLabQuery = DB::table('permintaan_pemeriksaan_lab')
                ->leftJoin('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('permintaan_pemeriksaan_lab.noorder', $noOrder)
                ->select(
                    'jns_perawatan_lab.nm_perawatan',
                    'permintaan_pemeriksaan_lab.kd_jenis_prw',
                    DB::raw("'jenis' as source")
                );
            
            // Buat query builder untuk mengambil data dari permintaan_detail_permintaan_lab dan template_laboratorium
            $templateLabQuery = DB::table('permintaan_detail_permintaan_lab')
                ->leftJoin('template_laboratorium', 'permintaan_detail_permintaan_lab.id_template', '=', 'template_laboratorium.id_template')
                ->where('permintaan_detail_permintaan_lab.noorder', $noOrder)
                ->select(
                    DB::raw('COALESCE(template_laboratorium.Pemeriksaan, CONCAT("Template ID: ", permintaan_detail_permintaan_lab.id_template)) as nm_perawatan'),
                    'permintaan_detail_permintaan_lab.kd_jenis_prw',
                    DB::raw("'template' as source")
                );
            
            // Gabungkan hasil kedua query dengan union
            $combinedQuery = $pemeriksaanLabQuery->union($templateLabQuery);
            
            // Eksekusi query gabungan
            $results = $combinedQuery->get();
            
            // Log hasil query
            \Illuminate\Support\Facades\Log::info('Found ' . count($results) . ' total records for noOrder: ' . $noOrder);
            
            // Jika tidak ada data, coba fallback dengan query langsung ke permintaan_lab
            if (count($results) === 0) {
                // Ambil informasi permintaan lab untuk fallback
                $permintaanLab = DB::table('permintaan_lab')
                    ->where('noorder', $noOrder)
                    ->first();
                
                if ($permintaanLab) {
                    $fallbackResult = collect([(object)[
                        'nm_perawatan' => $permintaanLab->diagnosa_klinis ?? 'Pemeriksaan Lab',
                        'kd_jenis_prw' => $noOrder,
                        'source' => 'fallback'
                    ]]);
                    
                    \Illuminate\Support\Facades\Log::info('Using fallback data for noOrder: ' . $noOrder);
                    return $fallbackResult;
                }
            }
            
            return $results;
        } catch (\Exception $e) {
            // Log error dengan detail untuk debugging
            \Illuminate\Support\Facades\Log::error('Error pada getDetailPemeriksaan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'noOrder' => $noOrder
            ]);
            return collect();
        }
    }
}
