<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partograf;
use App\Models\IbuHamil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            $hamilke = $ibuHamil->kehamilan_ke ?? '0';
            $anakhidup = $ibuHamil->jumlah_anak_hidup ?? '0';
            $keguguran = $ibuHamil->riwayat_keguguran ?? '0';
            $umur = $ibuHamil->usia_ibu ?? '0';
            
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
            $tanggal_partograf = null;
            
            foreach ($partografList as $partograf) {
                // Catat tanggal partograf dari item pertama (paling awal)
                if ($tanggal_partograf === null) {
                    $tanggal_partograf = $partograf->tanggal_partograf;
                }
                
                // Proses data partograf
                if ($partograf->dilatasi_serviks || $partograf->penurunan_posisi_janin) {
                    // Simpan data waktu untuk label pada grafik
                    $waktuLabels[] = $jam;
                    
                    // Tampilkan data dilatasi serviks jika ada
                    if ($partograf->dilatasi_serviks) {
                        // Ambil nilai pembukaan langsung dari database tanpa modifikasi
                        $nilaiPembukaan = (float) $partograf->dilatasi_serviks;
                        
                        // Log nilai pembukaan untuk debugging
                        Log::debug("Jam $jam: Nilai pembukaan: $nilaiPembukaan");
                        
                        // Simpan nilai pembukaan apa adanya
                        $pembukaanData[] = $nilaiPembukaan;
                        
                        $dilatasiData[] = [
                            'jam' => $jam,
                            'nilai' => (int) $partograf->dilatasi_serviks
                        ];
                    } else {
                        $pembukaanData[] = null;
                    }
                    
                    // Tampilkan data penurunan kepala jika ada
                    if ($partograf->penurunan_posisi_janin) {
                        // Ambil nilai stasiun penurunan kepala (-5 sampai +5)
                        // Format yang tersimpan bisa berupa angka (misal: "0") atau string (misal: "-3")
                        $posisiJaninValue = $partograf->penurunan_posisi_janin;
                        
                        // Konversi ke nilai integer dalam range -5 sampai +5
                        if (is_numeric($posisiJaninValue)) {
                            // Jika sudah numerik, gunakan langsung
                            $station = (int) $posisiJaninValue;
                            
                            // Pastikan nilainya dalam range -5 sampai +5
                            $station = max(-5, min(5, $station));
                            $penurunanData[] = $station;
                        } else {
                            // Jika format lain, coba ekstrak nilai numerik
                            Log::debug("Format penurunan_posisi_janin tidak standar: " . $posisiJaninValue);
                            
                            // Default ke 0 jika tidak bisa dikonversi
                            $penurunanData[] = 0;
                        }
                    } else {
                        $penurunanData[] = null;
                    }
                }
                
                // Data denyut jantung janin
                if ($partograf->denyut_jantung_janin) {
                    $djjData[] = [
                        'jam' => $jam,
                        'nilai' => (int) $partograf->denyut_jantung_janin
                    ];
                }
                
                // Data kontraksi
                if ($partograf->frekuensi_kontraksi) {
                    $kontraksiData[] = [
                        'jam' => $jam,
                        'nilai' => (int) $partograf->frekuensi_kontraksi,
                        'durasi' => (int) $partograf->durasi_kontraksi ?? 0
                    ];
                }
                
                // Data tekanan darah
                if ($partograf->tekanan_darah_sistole && $partograf->tekanan_darah_diastole) {
                    $tensiData[] = [
                        'jam' => $jam,
                        'sistole' => (int) $partograf->tekanan_darah_sistole,
                        'diastole' => (int) $partograf->tekanan_darah_diastole
                    ];
                }
                
                // Data nadi
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
                
                $jam++;
            }
            
            // Siapkan data grafik
            $grafikData = [
                'waktu' => $waktuLabels,
                'pembukaan' => $pembukaanData,
                'penurunan' => $penurunanData
            ];
            
            // Pastikan ada data grafik dan log untuk debugging
            if (empty($pembukaanData)) {
                Log::warning('Data pembukaan kosong untuk id_hamil: ' . $id_hamil);
            } else {
                Log::info('Data pembukaan: ' . json_encode($pembukaanData));
            }
            
            // Ambil catatan persalinan terbaru untuk pasien ini
            $catatanPersalinan = DB::table('partograf_catatan')
                ->where('id_hamil', $id_hamil)
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Ambil data pemantauan kala 4 jika ada catatan persalinan
            $pemantauanKala4 = [];
            if ($catatanPersalinan && isset($catatanPersalinan->id_catatan)) {
                $pemantauanKala4 = DB::table('partograf_pemantauan_kala4')
                    ->where('id_catatan', $catatanPersalinan->id_catatan)
                    ->orderBy('jam_ke', 'asc')
                    ->get();
                
                // Konversi ke array untuk memudahkan akses di view
                $pemantauanKala4 = json_decode(json_encode($pemantauanKala4), true);
            }
            
            // Konversi catatanPersalinan ke objek
            if ($catatanPersalinan) {
                $catatanPersalinan = json_decode(json_encode($catatanPersalinan));
            } else {
                // Jika tidak ada data catatan persalinan, buat objek kosong
                $catatanPersalinan = new \stdClass();
            }
            
            Log::info('Data grafik yang akan ditampilkan: ' . json_encode($grafikData));
            
            // Render view partograf-klasik dengan data yang disiapkan
            return view('partograf-klasik', compact(
                'nama', 
                'no_rkm_medis', 
                'hpht',
                'tanggal_partograf',
                'grafikData',
                'djjData',
                'dilatasiData',
                'kontraksiData',
                'tensiData',
                'nadiData',
                'suhuData',
                'ketubanData',
                'volumeData',
                'obatData',
                'catatanPersalinan',
                'pemantauanKala4',
                'hamilke',
                'anakhidup',
                'keguguran',
                'umur'
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