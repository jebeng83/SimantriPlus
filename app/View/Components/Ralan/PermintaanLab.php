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
            
            // Format variasi noRawat untuk mencoba berbagai kemungkinan
            $noRawatVariations = [
                $noRawat,                                    // Format asli
                $decodedNoRawat,                             // Format yang sudah didekode
                urldecode($noRawat),                         // URL decoded
                str_replace('/', '', $noRawat),              // Tanpa slash
                str_replace('/', '', $decodedNoRawat),       // Decoded tanpa slash
                date('Y/m/d') . '/' . substr($noRawat, -6)   // Mencoba format tanggal+nomor
            ];
            
            // Pastikan cache tidak digunakan dalam query
            DB::connection()->disableQueryLog();
            
            // Coba satu persatu
            foreach ($noRawatVariations as $variation) {
                $query = DB::table('permintaan_lab')
                          ->where('no_rawat', $variation)
                          ->orderBy('tgl_permintaan', 'desc')
                          ->orderBy('jam_permintaan', 'desc');
                          
                $data = $query->get();
                
                if (count($data) > 0) {
                    \Illuminate\Support\Facades\Log::info('Berhasil mendapatkan data dengan variasi', [
                        'variation' => $variation,
                        'count' => count($data)
                    ]);
                    
                    return $data;
                }
            }
            
            // Jika semua variasi gagal, coba query alternatif dengan tanggal hari ini
            $today = date('Y-m-d');
            
            // Query data untuk hari ini tanpa menggunakan cache
            $queryToday = DB::table('permintaan_lab')
                          ->whereDate('tgl_permintaan', $today)
                          ->orderBy('tgl_permintaan', 'desc')
                          ->orderBy('jam_permintaan', 'desc');
            
            // Jalankan query dengan fresh data
            $dataToday = $queryToday->get();
                          
            // Log hasil query alternatif untuk debugging
            \Illuminate\Support\Facades\Log::debug('Data permintaan_lab dari query alternatif', [
                'jumlah_data' => count($dataToday),
                'data_sample' => $dataToday->take(3)
            ]);
            
            if (count($dataToday) > 0) {
                \Illuminate\Support\Facades\Log::info('Mendapatkan data hari ini sebagai alternatif', [
                    'count' => count($dataToday),
                    'first' => $dataToday->first()
                ]);
                
                return $dataToday;
            }
            
            // Jika masih belum ada data, cek total data di tabel
            $totalData = DB::table('permintaan_lab')->count();
            \Illuminate\Support\Facades\Log::info('Total data di tabel permintaan_lab: ' . $totalData);
            
            if ($totalData > 0) {
                // Ambil 5 data terbaru untuk diagnostik
                $latestData = DB::table('permintaan_lab')
                              ->orderBy('tgl_permintaan', 'desc')
                              ->orderBy('jam_permintaan', 'desc')
                              ->limit(5)
                              ->get();
                              
                \Illuminate\Support\Facades\Log::info('5 data terbaru di permintaan_lab', [
                    'data' => $latestData
                ]);
                
                // Kembalikan data terbaru sebagai fallback
                return $latestData;
            }
            
            // Tidak ada data yang ditemukan
            \Illuminate\Support\Facades\Log::warning('Tidak ada data permintaan lab ditemukan untuk semua variasi noRawat');
            return collect();
            
        } catch (\Exception $e) {
            // Log error
            \Illuminate\Support\Facades\Log::error('Error pada getPemeriksaanLab: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        } finally {
            // Aktifkan kembali query log
            DB::connection()->enableQueryLog();
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
