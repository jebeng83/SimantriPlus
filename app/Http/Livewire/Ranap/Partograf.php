<?php

namespace App\Http\Livewire\Ranap;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\DataIbuHamil;
use App\Models\Pasien;
use App\Models\RegPeriksa;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Partograf extends Component
{
    public $noRawat;
    public $dataIbuHamil;
    public $partograf = [
        // Bagian 1: Informasi Persalinan Awal
        'paritas' => '',
        'onset_persalinan' => '',
        'waktu_pecah_ketuban' => '',
        
        // Bagian 2: Supportive Care
        'pendamping' => '',
        'mobilitas' => '',
        'manajemen_nyeri' => '',
        'intake_cairan' => '',
        
        // Bagian 3: Informasi Janin
        'denyut_jantung_janin' => '',
        'kondisi_cairan_ketuban' => '',
        'presentasi_janin' => '',
        'bentuk_kepala_janin' => '',
        'caput_succedaneum' => '',
        
        // Bagian 4: Informasi Ibu
        'nadi' => '',
        'tekanan_darah_sistole' => '',
        'tekanan_darah_diastole' => '',
        'suhu' => '',
        'urine_output' => '',
        
        // Bagian 5: Proses Persalinan
        'frekuensi_kontraksi' => '',
        'durasi_kontraksi' => '',
        'dilatasi_serviks' => '',
        'penurunan_posisi_janin' => '',
        
        // Bagian 6: Pengobatan
        'obat_dan_dosis' => '',
        'cairan_infus' => '',
        
        // Bagian 7: Perencanaan
        'tindakan_yang_direncanakan' => '',
        'hasil_tindakan' => '',
        'keputusan_bersama' => ''
    ];
    
    public $faktorRisiko = [
        'hipertensi' => false,
        'preeklampsia' => false,
        'diabetes' => false
    ];
    
    public $riwayatPartograf = [];
    public $chartData = [];
    public $currentPartografId = null;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->loadDataIbuHamil();
        $this->loadRiwayatPartograf();
        $this->loadChartData();
    }

    public function render()
    {
        return view('livewire.ranap.partograf');
    }
    
    protected function loadDataIbuHamil()
    {
        try {
            // Mendapatkan data pasien berdasarkan no_rawat
            $regPeriksa = RegPeriksa::where('no_rawat', $this->noRawat)->first();
            
            if ($regPeriksa) {
                // Mencari data ibu hamil berdasarkan no_rkm_medis dengan status 'Aktif'
                $this->dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $regPeriksa->no_rkm_medis)
                    ->where('status', 'Aktif')
                    ->first();
                
                // Jika tidak menemukan dengan status 'Aktif', coba cari dengan status 'Hamil'    
                if (!$this->dataIbuHamil) {
                    $this->dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $regPeriksa->no_rkm_medis)
                        ->where('status', 'Hamil')
                        ->first();
                }
            }
        } catch (\Exception $e) {
            $this->dataIbuHamil = null;
        }
    }
    
    protected function loadRiwayatPartograf()
    {
        if (!$this->dataIbuHamil) {
            $this->riwayatPartograf = [];
            return;
        }
        
        $data = DB::table('partograf')
            ->where('id_hamil', $this->dataIbuHamil->id_hamil)
            ->orderBy('tanggal_partograf', 'desc')
            ->get();
            
        // Memastikan data dikembalikan sebagai array objek, bukan array asosiatif
        $this->riwayatPartograf = json_decode(json_encode($data), false);
    }
    
    protected function loadChartData()
    {
        try {
            // Inisialisasi data kosong
            $chartData = array_fill(0, 12, null);
            
            if (empty($this->riwayatPartograf)) {
                \Log::info('No partograf data available for chart');
                $this->chartData = $chartData;
                $this->emit('partografDataUpdated', $this->chartData);
                return;
            }
            
            // Ambil data dan urutkan berdasarkan waktu
            $sortedRecords = collect($this->riwayatPartograf)->sortBy(function($record) {
                return is_object($record) ? $record->tanggal_partograf : $record['tanggal_partograf'];
            })->values()->all();
            
            \Log::info('Sorted records count: ' . count($sortedRecords));
            
            if (count($sortedRecords) > 0) {
                // Ambil waktu awal 
                $firstRecord = $sortedRecords[0];
                $startTime = Carbon::parse(is_object($firstRecord) ? $firstRecord->tanggal_partograf : $firstRecord['tanggal_partograf']);
                
                \Log::info('Start time for chart: ' . $startTime->toDateTimeString());
                
                // Proses setiap record
                foreach ($sortedRecords as $record) {
                    $recordTime = Carbon::parse(is_object($record) ? $record->tanggal_partograf : $record['tanggal_partograf']);
                    $hourDiff = (int)$startTime->diffInHours($recordTime);
                    
                    $dilatasi = is_object($record) ? $record->dilatasi_serviks : $record['dilatasi_serviks'];
                    
                    // Pastikan hourDiff valid dan dilatasi memiliki nilai 
                    if ($dilatasi !== null && $dilatasi !== '' && is_numeric($dilatasi) && $hourDiff >= 0 && $hourDiff < 12) {
                        $chartData[$hourDiff] = (float)$dilatasi;
                        \Log::info("Adding point at hour {$hourDiff}: dilatasi {$dilatasi}");
                    }
                }
                
                $this->chartData = $chartData;
                \Log::info('Chart data prepared', ['chartData' => $this->chartData]);
            } else {
                \Log::warning('No valid records available for chart');
                $this->chartData = $chartData;
            }
            
            // Emit event untuk update chart
            $this->emit('partografDataUpdated', $this->chartData);
            
        } catch (\Exception $e) {
            \Log::error('Error loading chart data: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            
            // Emit event dengan data kosong
            $this->chartData = array_fill(0, 12, null);
            $this->emit('partografDataUpdated', $this->chartData);
        }
    }
    
    public function savePartograf()
    {
        if (!$this->dataIbuHamil) {
            session()->flash('error', 'Data ibu hamil tidak ditemukan. Partograf tidak dapat disimpan.');
            return;
        }
        
        // Validasi input
        $this->validate([
            'partograf.dilatasi_serviks' => 'required|numeric|min:0|max:10',
            'partograf.denyut_jantung_janin' => 'required|numeric|min:100|max:200',
            'partograf.tekanan_darah_sistole' => 'required|numeric|min:80|max:200',
            'partograf.tekanan_darah_diastole' => 'required|numeric|min:40|max:120',
        ], [
            'partograf.dilatasi_serviks.required' => 'Dilatasi serviks harus diisi',
            'partograf.denyut_jantung_janin.required' => 'Denyut jantung janin harus diisi',
            'partograf.tekanan_darah_sistole.required' => 'Tekanan darah sistole harus diisi',
            'partograf.tekanan_darah_diastole.required' => 'Tekanan darah diastole harus diisi',
        ]);
        
        try {
            // Mendapatkan data reg_periksa
            $regPeriksa = RegPeriksa::where('no_rawat', $this->noRawat)->first();
            
            if (!$regPeriksa) {
                session()->flash('error', 'Data registrasi tidak ditemukan');
                return;
            }
            
            // Generate ID partograf
            $idPartograf = $this->generateIdPartograf();
            
            // Konversi faktor risiko ke JSON
            $faktorRisikoJson = json_encode($this->faktorRisiko);
            
            // Data untuk disimpan ke database
            $dataPartograf = [
                'id_partograf' => $idPartograf,
                'no_rawat' => $this->noRawat,
                'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                'id_hamil' => $this->dataIbuHamil->id_hamil,
                'tanggal_partograf' => now(),
                'diperiksa_oleh' => Auth::user()->name ?? 'Petugas',
                
                // Bagian 1: Informasi Persalinan Awal
                'paritas' => $this->partograf['paritas'],
                'onset_persalinan' => $this->partograf['onset_persalinan'],
                'waktu_pecah_ketuban' => $this->partograf['waktu_pecah_ketuban'],
                'faktor_risiko' => $faktorRisikoJson,
                
                // Bagian 2: Supportive Care
                'pendamping' => $this->partograf['pendamping'],
                'mobilitas' => $this->partograf['mobilitas'],
                'manajemen_nyeri' => $this->partograf['manajemen_nyeri'],
                'intake_cairan' => $this->partograf['intake_cairan'],
                
                // Bagian 3: Informasi Janin
                'denyut_jantung_janin' => $this->partograf['denyut_jantung_janin'],
                'kondisi_cairan_ketuban' => $this->partograf['kondisi_cairan_ketuban'],
                'presentasi_janin' => $this->partograf['presentasi_janin'],
                'bentuk_kepala_janin' => $this->partograf['bentuk_kepala_janin'],
                'caput_succedaneum' => $this->partograf['caput_succedaneum'],
                
                // Bagian 4: Informasi Ibu
                'nadi' => $this->partograf['nadi'],
                'tekanan_darah_sistole' => $this->partograf['tekanan_darah_sistole'],
                'tekanan_darah_diastole' => $this->partograf['tekanan_darah_diastole'],
                'suhu' => $this->partograf['suhu'],
                'urine_output' => $this->partograf['urine_output'],
                
                // Bagian 5: Proses Persalinan
                'frekuensi_kontraksi' => $this->partograf['frekuensi_kontraksi'],
                'durasi_kontraksi' => $this->partograf['durasi_kontraksi'],
                'dilatasi_serviks' => $this->partograf['dilatasi_serviks'],
                'penurunan_posisi_janin' => $this->partograf['penurunan_posisi_janin'],
                
                // Bagian 6: Pengobatan
                'obat_dan_dosis' => $this->partograf['obat_dan_dosis'],
                'cairan_infus' => $this->partograf['cairan_infus'],
                
                // Bagian 7: Perencanaan
                'tindakan_yang_direncanakan' => $this->partograf['tindakan_yang_direncanakan'],
                'hasil_tindakan' => $this->partograf['hasil_tindakan'],
                'keputusan_bersama' => $this->partograf['keputusan_bersama'],
                
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Simpan data partograf ke database
            DB::table('partograf')->insert($dataPartograf);
            
            // Perbarui data grafik
            $this->loadRiwayatPartograf();
            $this->loadChartData();
            
            // Reset form partograf untuk entri baru
            $this->resetPartografForm();
            
            // Tampilkan pesan sukses
            session()->flash('success', 'Data partograf berhasil disimpan');

            // Pindah ke tab grafik
            $this->dispatchBrowserEvent('show-grafik-tab');
        } catch (\Exception $e) {
            // Tampilkan pesan error
            session()->flash('error', 'Gagal menyimpan data partograf: ' . $e->getMessage());
        }
    }
    
    protected function generateIdPartograf()
    {
        $lastId = DB::table('partograf')
            ->where('id_partograf', 'like', 'PART%')
            ->orderBy('id_partograf', 'desc')
            ->value('id_partograf');
            
        if ($lastId) {
            $lastNumber = (int) substr($lastId, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'PART' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
    
    public function viewPartograf($idPartograf)
    {
        $this->currentPartografId = $idPartograf;
        
        // Ambil data partograf berdasarkan ID
        $partografData = DB::table('partograf')
            ->where('id_partograf', $idPartograf)
            ->first();
            
        if ($partografData) {
            // Pastikan data dikembalikan sebagai objek, bukan array
            $partografData = (object)$partografData;
            
            // Isi form dengan data partograf yang dipilih
            $this->partograf = [
                // Bagian 1: Informasi Persalinan Awal
                'paritas' => $partografData->paritas,
                'onset_persalinan' => $partografData->onset_persalinan,
                'waktu_pecah_ketuban' => $partografData->waktu_pecah_ketuban,
                
                // Bagian 2: Supportive Care
                'pendamping' => $partografData->pendamping,
                'mobilitas' => $partografData->mobilitas,
                'manajemen_nyeri' => $partografData->manajemen_nyeri,
                'intake_cairan' => $partografData->intake_cairan,
                
                // Bagian 3: Informasi Janin
                'denyut_jantung_janin' => $partografData->denyut_jantung_janin,
                'kondisi_cairan_ketuban' => $partografData->kondisi_cairan_ketuban,
                'presentasi_janin' => $partografData->presentasi_janin,
                'bentuk_kepala_janin' => $partografData->bentuk_kepala_janin,
                'caput_succedaneum' => $partografData->caput_succedaneum,
                
                // Bagian 4: Informasi Ibu
                'nadi' => $partografData->nadi,
                'tekanan_darah_sistole' => $partografData->tekanan_darah_sistole,
                'tekanan_darah_diastole' => $partografData->tekanan_darah_diastole,
                'suhu' => $partografData->suhu,
                'urine_output' => $partografData->urine_output,
                
                // Bagian 5: Proses Persalinan
                'frekuensi_kontraksi' => $partografData->frekuensi_kontraksi,
                'durasi_kontraksi' => $partografData->durasi_kontraksi,
                'dilatasi_serviks' => $partografData->dilatasi_serviks,
                'penurunan_posisi_janin' => $partografData->penurunan_posisi_janin,
                
                // Bagian 6: Pengobatan
                'obat_dan_dosis' => $partografData->obat_dan_dosis,
                'cairan_infus' => $partografData->cairan_infus,
                
                // Bagian 7: Perencanaan
                'tindakan_yang_direncanakan' => $partografData->tindakan_yang_direncanakan,
                'hasil_tindakan' => $partografData->hasil_tindakan,
                'keputusan_bersama' => $partografData->keputusan_bersama
            ];
            
            // Konversi faktor risiko dari JSON
            if ($partografData->faktor_risiko) {
                $this->faktorRisiko = json_decode($partografData->faktor_risiko, true);
            }
            
            // Pindah ke tab data partograf
            $this->dispatchBrowserEvent('show-data-tab');
        }
    }
    
    public function resetPartografForm()
    {
        $this->partograf = [
            // Bagian 1: Informasi Persalinan Awal
            'paritas' => '',
            'onset_persalinan' => '',
            'waktu_pecah_ketuban' => '',
            
            // Bagian 2: Supportive Care
            'pendamping' => '',
            'mobilitas' => '',
            'manajemen_nyeri' => '',
            'intake_cairan' => '',
            
            // Bagian 3: Informasi Janin
            'denyut_jantung_janin' => '',
            'kondisi_cairan_ketuban' => '',
            'presentasi_janin' => '',
            'bentuk_kepala_janin' => '',
            'caput_succedaneum' => '',
            
            // Bagian 4: Informasi Ibu
            'nadi' => '',
            'tekanan_darah_sistole' => '',
            'tekanan_darah_diastole' => '',
            'suhu' => '',
            'urine_output' => '',
            
            // Bagian 5: Proses Persalinan
            'frekuensi_kontraksi' => '',
            'durasi_kontraksi' => '',
            'dilatasi_serviks' => '',
            'penurunan_posisi_janin' => '',
            
            // Bagian 6: Pengobatan
            'obat_dan_dosis' => '',
            'cairan_infus' => '',
            
            // Bagian 7: Perencanaan
            'tindakan_yang_direncanakan' => '',
            'hasil_tindakan' => '',
            'keputusan_bersama' => ''
        ];
        
        $this->faktorRisiko = [
            'hipertensi' => false,
            'preeklampsia' => false,
            'diabetes' => false
        ];
        
        $this->currentPartografId = null;
    }
    
    public function exportPartograf()
    {
        $this->dispatchBrowserEvent('export-partograf-pdf', [
            'title' => 'Partograf - ' . ($this->dataIbuHamil->nama ?? 'Pasien'),
            'data' => $this->chartData
        ]);
        
        session()->flash('info', 'Partograf sedang diproses untuk diunduh');
    }

    public function reloadChartData()
    {
        $this->loadRiwayatPartograf();
        $this->loadChartData();
    }

    protected $listeners = [
        'chartDataRequest' => 'handleChartDataRequest'
    ];
    
    public function handleChartDataRequest()
    {
        \Log::info('Menerima event chartDataRequest');
        $this->loadChartData();
    }
} 