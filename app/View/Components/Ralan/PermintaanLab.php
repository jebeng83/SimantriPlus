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
            
            // Ambil data dari permintaan_pemeriksaan_lab terlebih dahulu
            $pemeriksaanLab = DB::table('permintaan_pemeriksaan_lab')
                              ->where('noorder', $noOrder)
                              ->get();
                              
            // Log hasil query
            \Illuminate\Support\Facades\Log::info('Found ' . count($pemeriksaanLab) . ' records in permintaan_pemeriksaan_lab');
            
            // Jika ada data pemeriksaan, ambil informasi jenis perawatan
            if (count($pemeriksaanLab) > 0) {
                $results = collect();
                
                foreach ($pemeriksaanLab as $pemeriksaan) {
                    try {
                        // Ambil data nama perawatan dari tabel jns_perawatan_lab
                        $jenisPrw = DB::table('jns_perawatan_lab')
                                    ->where('kd_jenis_prw', $pemeriksaan->kd_jenis_prw)
                                    ->first();
                                    
                        if ($jenisPrw) {
                            $results->push($jenisPrw);
                        } else {
                            // Jika tidak ditemukan, buat objek manual dengan nama perawatan sesuai kode
                            $mockPrw = (object)[
                                'nm_perawatan' => 'Pemeriksaan Kode: ' . $pemeriksaan->kd_jenis_prw,
                                'kd_jenis_prw' => $pemeriksaan->kd_jenis_prw
                            ];
                            $results->push($mockPrw);
                        }
                    } catch (\Exception $e) {
                        // Log error tapi tidak detail
                        \Illuminate\Support\Facades\Log::error('Error ambil jenis perawatan untuk kode: ' . $pemeriksaan->kd_jenis_prw);
                    }
                }
                
                return $results;
            }
            
            // Alternatif: jika tidak ada data di permintaan_pemeriksaan_lab
            // Periksa di tabel permintaan_detail_permintaan_lab
            $detailLab = DB::table('permintaan_detail_permintaan_lab')
                          ->where('noorder', $noOrder)
                          ->get();
                          
            // Log hasil query alternatif
            \Illuminate\Support\Facades\Log::info('Found ' . count($detailLab) . ' records in permintaan_detail_permintaan_lab');
            
            if (count($detailLab) > 0) {
                $results = collect();
                
                foreach ($detailLab as $detail) {
                    try {
                        // Ambil data template
                        $template = DB::table('template_laboratorium')
                                    ->where('id_template', $detail->id_template)
                                    ->first();
                                    
                        if ($template) {
                            $templateObj = (object)[
                                'nm_perawatan' => $template->Pemeriksaan ?? ('Template ID: ' . $detail->id_template),
                                'kd_jenis_prw' => $detail->kd_jenis_prw
                            ];
                            $results->push($templateObj);
                        } else {
                            // Jika tidak ditemukan template, tampilkan info dasar
                            $mockTemplate = (object)[
                                'nm_perawatan' => 'Template ID: ' . $detail->id_template,
                                'kd_jenis_prw' => $detail->kd_jenis_prw
                            ];
                            $results->push($mockTemplate);
                        }
                    } catch (\Exception $e) {
                        // Log error tapi tidak detail
                        \Illuminate\Support\Facades\Log::error('Error ambil template untuk ID: ' . $detail->id_template);
                    }
                }
                
                return $results;
            }
            
            // Jika tidak ada data yang ditemukan, kembalikan collection kosong
            \Illuminate\Support\Facades\Log::info('No data found for order: ' . $noOrder);
            return collect();
            
        } catch (\Exception $e) {
            // Log error tapi tidak detail
            \Illuminate\Support\Facades\Log::error('Error pada getDetailPemeriksaan: ' . $e->getMessage());
            return collect();
        }
    }
}
