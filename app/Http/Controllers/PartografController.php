<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partograf;
use App\Models\IbuHamil;
use Illuminate\Support\Facades\Log;

class PartografController extends Controller
{
    /**
     * Menampilkan partograf klasik berdasarkan ID ibu hamil
     *
     * @param int $id_hamil
     * @return \Illuminate\View\View
     */
    public function showKlasik($id_hamil)
    {
        try {
            Log::info('Mencoba mengakses partograf dengan id_hamil: ' . $id_hamil);
            
            // Ambil data ibu hamil
            $ibuHamil = IbuHamil::where('id_hamil', $id_hamil)->first();
            
            if (!$ibuHamil) {
                Log::warning('Data ibu hamil tidak ditemukan untuk id_hamil: ' . $id_hamil);
                return view('errors.custom', [
                    'title' => 'Data Tidak Ditemukan',
                    'message' => 'Data ibu hamil tidak ditemukan.'
                ]);
            }
            
            // Ambil semua data partograf untuk pasien ini berdasarkan no_rawat
            // Urutkan berdasarkan tanggal_partograf
            $partografList = Partograf::where('id_hamil', $id_hamil)
                ->orderBy('tanggal_partograf', 'asc')
                ->get();
            
            if ($partografList->isEmpty()) {
                return view('errors.custom', [
                    'title' => 'Data Tidak Ditemukan',
                    'message' => 'Data partograf belum tersedia untuk pasien ini.'
                ]);
            }
            
            // Ekstrak data yang dibutuhkan untuk template partograf-klasik
            $nama = $ibuHamil->nama;
            $no_rkm_medis = $ibuHamil->no_rkm_medis;
            $hpht = $ibuHamil->hari_pertama_haid ? date('d-m-Y', strtotime($ibuHamil->hari_pertama_haid)) : 'N/A';
            
            // Inisialisasi array kosong untuk data partograf
            $djjData = [];
            $dilatasiData = [];
            $kontraksiData = [];
            $tensiData = [];
            $nadiData = [];
            $suhuData = [];
            $ketubanData = [];
            $volumeData = [];
            $obatData = [];
            
            // Siapkan data untuk grafik
            $waktuLabels = [];
            $pembukaanData = [];
            $penurunanData = [];
            
            // Loop semua data partograf dan gabungkan
            $jam = 0;
            foreach ($partografList as $partograf) {
                // Load data untuk grafik dari JSON
                $grafikData = json_decode($partograf->grafik_kemajuan_persalinan_json, true);
                
                // Ambil waktu pemeriksaan untuk label
                $waktuLabels[] = date('d/m H:i', strtotime($partograf->tanggal_partograf));
                
                // Ambil data pembukaan dan penurunan
                $pembukaanData[] = (float) $partograf->dilatasi_serviks;
                $penurunanData[] = (int) $partograf->penurunan_posisi_janin;
                
                // Jika ada data grafik_kemajuan_persalinan_json, gabungkan semua data
                if (!empty($grafikData)) {
                    // Ambil data DJJ langsung dari kolom tabel
                    if ($partograf->denyut_jantung_janin) {
                        $djjData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->denyut_jantung_janin
                        ];
                    }
                    
                    // Ambil data dilatasi
                    if (isset($grafikData['dilatasi']) && is_array($grafikData['dilatasi'])) {
                        foreach ($grafikData['dilatasi'] as $dilatasi) {
                            $dilatasi['jam'] = $jam;
                            $dilatasiData[] = $dilatasi;
                        }
                    } elseif ($partograf->dilatasi_serviks) {
                        $dilatasiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->dilatasi_serviks
                        ];
                    }
                    
                    // Ambil data kontraksi
                    if (isset($grafikData['kontraksi']) && is_array($grafikData['kontraksi'])) {
                        foreach ($grafikData['kontraksi'] as $kontraksi) {
                            $kontraksi['jam'] = $jam;
                            $kontraksiData[] = $kontraksi;
                        }
                    } elseif ($partograf->frekuensi_kontraksi) {
                        $kontraksiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->frekuensi_kontraksi,
                            'durasi' => $partograf->durasi_kontraksi
                        ];
                    }
                    
                    // Ambil data tensi
                    if (isset($grafikData['tensi']) && is_array($grafikData['tensi'])) {
                        foreach ($grafikData['tensi'] as $tensi) {
                            $tensi['jam'] = $jam;
                            $tensiData[] = $tensi;
                        }
                    } elseif ($partograf->tekanan_darah_sistole && $partograf->tekanan_darah_diastole) {
                        $tensiData[] = [
                            'jam' => $jam,
                            'sistole' => (int) $partograf->tekanan_darah_sistole,
                            'diastole' => (int) $partograf->tekanan_darah_diastole
                        ];
                    }
                    
                    // Ambil data nadi
                    if (isset($grafikData['nadi']) && is_array($grafikData['nadi'])) {
                        foreach ($grafikData['nadi'] as $nadi) {
                            $nadi['jam'] = $jam;
                            $nadiData[] = $nadi;
                        }
                    } elseif ($partograf->nadi) {
                        $nadiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->nadi
                        ];
                    }
                    
                    // Ambil data suhu
                    if (isset($grafikData['suhu']) && is_array($grafikData['suhu'])) {
                        foreach ($grafikData['suhu'] as $suhu) {
                            $suhu['jam'] = $jam;
                            $suhuData[] = $suhu;
                        }
                    } elseif ($partograf->suhu) {
                        $suhuData[] = [
                            'jam' => $jam,
                            'nilai' => (float) $partograf->suhu
                        ];
                    }
                    
                    // Ambil data ketuban
                    if (isset($grafikData['ketuban']) && is_array($grafikData['ketuban'])) {
                        foreach ($grafikData['ketuban'] as $ketuban) {
                            $ketuban['jam'] = $jam;
                            $ketubanData[] = $ketuban;
                        }
                    } elseif ($partograf->kondisi_cairan_ketuban) {
                        $ketubanData[] = [
                            'jam' => $jam,
                            'kode' => substr($partograf->kondisi_cairan_ketuban, 0, 1)
                        ];
                    }
                    
                    // Ambil data volume urine
                    if (isset($grafikData['volume']) && is_array($grafikData['volume'])) {
                        foreach ($grafikData['volume'] as $volume) {
                            $volume['jam'] = $jam;
                            $volumeData[] = $volume;
                        }
                    } elseif ($partograf->urine_output) {
                        $volumeData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->urine_output
                        ];
                    }
                    
                    // Ambil data obat
                    if (isset($grafikData['obat']) && is_array($grafikData['obat'])) {
                        foreach ($grafikData['obat'] as $obat) {
                            $obat['jam'] = $jam;
                            $obatData[] = $obat;
                        }
                    } elseif ($partograf->obat_dan_dosis) {
                        $obatData[] = [
                            'jam' => $jam,
                            'detail' => $partograf->obat_dan_dosis
                        ];
                    }
                } else {
                    // Jika tidak ada data JSON, ambil dari kolom tabel langsung
                    if ($partograf->denyut_jantung_janin) {
                        $djjData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->denyut_jantung_janin
                        ];
                    }
                    
                    if ($partograf->dilatasi_serviks) {
                        $dilatasiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->dilatasi_serviks
                        ];
                    }
                    
                    if ($partograf->frekuensi_kontraksi) {
                        $kontraksiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->frekuensi_kontraksi,
                            'durasi' => $partograf->durasi_kontraksi
                        ];
                    }
                    
                    if ($partograf->tekanan_darah_sistole && $partograf->tekanan_darah_diastole) {
                        $tensiData[] = [
                            'jam' => $jam,
                            'sistole' => (int) $partograf->tekanan_darah_sistole,
                            'diastole' => (int) $partograf->tekanan_darah_diastole
                        ];
                    }
                    
                    if ($partograf->nadi) {
                        $nadiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->nadi
                        ];
                    }
                    
                    if ($partograf->suhu) {
                        $suhuData[] = [
                            'jam' => $jam,
                            'nilai' => (float) $partograf->suhu
                        ];
                    }
                    
                    if ($partograf->kondisi_cairan_ketuban) {
                        $ketubanData[] = [
                            'jam' => $jam,
                            'kode' => substr($partograf->kondisi_cairan_ketuban, 0, 1)
                        ];
                    }
                    
                    if ($partograf->urine_output) {
                        $volumeData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->urine_output
                        ];
                    }
                    
                    if ($partograf->obat_dan_dosis) {
                        $obatData[] = [
                            'jam' => $jam,
                            'detail' => $partograf->obat_dan_dosis
                        ];
                    }
                }
                
                $jam++;
            }
            
            // Siapkan data grafik
            $grafikData = [
                'waktu' => $waktuLabels,
                'pembukaan' => $pembukaanData,
                'penurunan' => $penurunanData
            ];
            
            Log::info('Data grafik yang akan ditampilkan: ' . json_encode($grafikData));
            
            // Render view partograf-klasik dengan data yang disiapkan
            return view('partograf-klasik', compact(
                'nama', 
                'no_rkm_medis', 
                'hpht', 
                'grafikData',
                'djjData',
                'dilatasiData',
                'kontraksiData',
                'tensiData',
                'nadiData',
                'suhuData',
                'ketubanData',
                'volumeData',
                'obatData'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error pada PartografController@showKlasik: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return view('errors.custom', [
                'title' => 'Terjadi Kesalahan',
                'message' => 'Maaf, sistem sedang mengalami gangguan teknis: ' . $e->getMessage()
            ]);
        }
    }

    public function showByIdHamil($id_hamil)
    {
        try {
            // Ambil data ibu hamil
            $ibuHamil = IbuHamil::where('id_hamil', $id_hamil)->first();
            
            if (!$ibuHamil) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data ibu hamil tidak ditemukan'
                ], 404);
            }
            
            // Ambil data partograf
            $partograf = Partograf::where('id_hamil', $id_hamil)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$partograf) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data partograf tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ibu_hamil' => $ibuHamil,
                    'partograf' => $partograf
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error pada PartografController@showByIdHamil: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
} 