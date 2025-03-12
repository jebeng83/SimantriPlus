<?php

namespace App\Http\Livewire\Ranap;

use App\Traits\EnkripsiData;
use App\Traits\SwalResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanLab extends Component
{
    use EnkripsiData, SwalResponse;
    public $noRawat, $klinis, $info, $jns_pemeriksaan = [], $permintaanLab = [], $isCollapsed = true;
    public $selectedTemplates = [];

    protected $rules = [
        'klinis' => 'required',
        'info' => 'required',
        'jns_pemeriksaan' => 'required',
    ];

    protected $messages = [
        'klinis.required' => 'Klinis tidak boleh kosong',
        'info.required' => 'Informasi tambahan tidak boleh kosong',
        'jns_pemeriksaan.required' => 'Jenis pemeriksaan tidak boleh kosong',
    ];

    protected $listeners = ['deletePermintaanLab'];

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->klinis = $this->klinis ?? '-';
        $this->info = $this->info ?? '-';
    }

    public function hydrate()
    {
        $this->getPermintaanLab();
    }

    public function render()
    {
        return view('livewire.ranap.permintaan-lab');
    }

    public function selectedJnsPerawatan($item)
    {
        $this->jns_pemeriksaan = $item;
    }

    public function selectTemplate($templateId, $jenisPrw = null, $pemeriksaan = null, $satuan = null, $nilaiRujukanLd = null, $nilaiRujukanLa = null)
    {
        Log::info('Template dipilih', [
            'template_id' => $templateId,
            'jenis_prw' => $jenisPrw,
            'pemeriksaan' => $pemeriksaan,
            'satuan' => $satuan,
            'nilai_rujukan_ld' => $nilaiRujukanLd,
            'nilai_rujukan_la' => $nilaiRujukanLa
        ]);
        
        $isDummy = is_string($templateId) && strpos($templateId, 'dummy') === 0;
        
        if (!in_array($templateId, $this->selectedTemplates)) {
            $this->selectedTemplates[] = [
                'id' => $templateId,
                'jenis_prw' => $jenisPrw,
                'pemeriksaan' => $pemeriksaan,
                'satuan' => $satuan,
                'nilai_rujukan_ld' => $nilaiRujukanLd,
                'nilai_rujukan_la' => $nilaiRujukanLa,
                'is_dummy' => $isDummy
            ];
            
            if ($isDummy) {
                Log::info('Template dummy dipilih', ['template_id' => $templateId]);
            }
        }
    }

    public function unselectTemplate($templateId)
    {
        Log::info('Template dibatalkan', ['template_id' => $templateId]);
        
        $this->selectedTemplates = array_filter($this->selectedTemplates, function($template) use ($templateId) {
            return $template['id'] != $templateId;
        });
    }

    public function savePermintaanLab()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            
            Log::info('Menyimpan permintaan lab', [
                'no_rawat' => $this->noRawat,
                'jenis_pemeriksaan' => $this->jns_pemeriksaan,
                'template_terpilih' => $this->selectedTemplates
            ]);
            
            $getNumber = DB::table('permintaan_lab')
                ->where('tgl_permintaan', date('Y-m-d'))
                ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                ->first();

            $lastNumber = substr($getNumber->no ?? '0000', 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL' . date('Ymd') . $getNextNumber;

            DB::table('permintaan_lab')
                ->insert([
                    'noorder' => $noOrder,
                    'no_rawat' => $this->noRawat,
                    'tgl_permintaan' => date('Y-m-d'),
                    'jam_permintaan' => date('H:i:s'),
                    'dokter_perujuk' => session()->get('username'),
                    'diagnosa_klinis' =>  $this->klinis,
                    'informasi_tambahan' =>  $this->info,
                    'status' => 'ranap'
                ]);

            foreach ($this->jns_pemeriksaan as $pemeriksaan) {
                DB::table('permintaan_pemeriksaan_lab')
                    ->insert([
                        'noorder' => $noOrder,
                        'kd_jenis_prw' => $pemeriksaan,
                        'stts_bayar' => 'Belum'
                    ]);

                if (!empty($this->selectedTemplates)) {
                    // Filter template berdasarkan jenis pemeriksaan
                    $templatesForThisType = array_filter($this->selectedTemplates, function($template) use ($pemeriksaan) {
                        return $template['jenis_prw'] == $pemeriksaan;
                    });
                    
                    Log::info('Template yang akan disimpan', [
                        'pemeriksaan' => $pemeriksaan,
                        'jumlah_template' => count($templatesForThisType)
                    ]);
                    
                    if (!empty($templatesForThisType)) {
                        foreach ($templatesForThisType as $template) {
                            $templateId = $template['id'];
                            $isDummy = $template['is_dummy'] ?? false;
                            
                            if (!$isDummy) {
                                // Template normal dari database
                                DB::table('permintaan_detail_permintaan_lab')->insert([
                                    'noorder'   =>  $noOrder,
                                    'kd_jenis_prw'  =>  $pemeriksaan,
                                    'id_template'   =>  $templateId,
                                    'stts_bayar'    =>  'Belum'
                                ]);
                            } else {
                                // Template dummy, cek apakah perlu dibuat di database
                                $existingTemplate = DB::table('template_laboratorium')
                                    ->where('kd_jenis_prw', $pemeriksaan)
                                    ->where('Pemeriksaan', $template['pemeriksaan'])
                                    ->first();
                                
                                if ($existingTemplate) {
                                    // Gunakan template yang sudah ada
                                    DB::table('permintaan_detail_permintaan_lab')->insert([
                                        'noorder'   =>  $noOrder,
                                        'kd_jenis_prw'  =>  $pemeriksaan,
                                        'id_template'   =>  $existingTemplate->id_template,
                                        'stts_bayar'    =>  'Belum'
                                    ]);
                                } else {
                                    // Buat template baru di database
                                    $newTemplateId = DB::table('template_laboratorium')->insertGetId([
                                        'kd_jenis_prw' => $pemeriksaan,
                                        'Pemeriksaan' => $template['pemeriksaan'],
                                        'satuan' => $template['satuan'] ?? '',
                                        'nilai_rujukan_ld' => $template['nilai_rujukan_ld'] ?? '',
                                        'nilai_rujukan_la' => $template['nilai_rujukan_la'] ?? '',
                                        'bagian_rs' => 0,
                                        'bhp' => 0,
                                        'bagian_perujuk' => 0,
                                        'bagian_dokter' => 0,
                                        'bagian_laborat' => 0,
                                        'kso' => 0,
                                        'menejemen' => 0,
                                        'biaya_item' => 0,
                                        'urut' => 1
                                    ]);
                                    
                                    // Gunakan template yang baru dibuat
                                    DB::table('permintaan_detail_permintaan_lab')->insert([
                                        'noorder'   =>  $noOrder,
                                        'kd_jenis_prw'  =>  $pemeriksaan,
                                        'id_template'   =>  $newTemplateId,
                                        'stts_bayar'    =>  'Belum'
                                    ]);
                                }
                            }
                        }
                    } else {
                        // Tidak ada template yang dipilih untuk jenis pemeriksaan ini
                        // Gunakan semua template yang ada di database
                        $allTemplates = DB::table('template_laboratorium')
                            ->where('kd_jenis_prw', $pemeriksaan)
                            ->get();
                            
                        if ($allTemplates->isNotEmpty()) {
                            Log::info('Menggunakan semua template dari database', [
                                'pemeriksaan' => $pemeriksaan,
                                'jumlah_template' => $allTemplates->count()
                            ]);
                            
                            foreach ($allTemplates as $temp) {
                                DB::table('permintaan_detail_permintaan_lab')->insert([
                                    'noorder'   =>  $noOrder,
                                    'kd_jenis_prw'  =>  $pemeriksaan,
                                    'id_template'   =>  $temp->id_template,
                                    'stts_bayar'    =>  'Belum'
                                ]);
                            }
                        } else {
                            Log::info('Tidak ada template di database untuk jenis pemeriksaan ini', [
                                'pemeriksaan' => $pemeriksaan
                            ]);
                        }
                    }
                } else {
                    // Tidak ada template yang dipilih sama sekali
                    // Gunakan semua template yang ada di database
                    $allTemplates = DB::table('template_laboratorium')
                        ->where('kd_jenis_prw', $pemeriksaan)
                        ->get();
                        
                    if ($allTemplates->isNotEmpty()) {
                        Log::info('Menggunakan semua template dari database (tidak ada template yang dipilih)', [
                            'pemeriksaan' => $pemeriksaan,
                            'jumlah_template' => $allTemplates->count()
                        ]);
                        
                        foreach ($allTemplates as $temp) {
                            DB::table('permintaan_detail_permintaan_lab')->insert([
                                'noorder'   =>  $noOrder,
                                'kd_jenis_prw'  =>  $pemeriksaan,
                                'id_template'   =>  $temp->id_template,
                                'stts_bayar'    =>  'Belum'
                            ]);
                        }
                    } else {
                        Log::info('Tidak ada template di database untuk jenis pemeriksaan ini', [
                            'pemeriksaan' => $pemeriksaan
                        ]);
                    }
                }
            }
            
            DB::commit();
            $this->getPermintaanLab();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Permintaan Lab berhasil ditambahkan'));
            
            $this->reset(['klinis', 'info', 'jns_pemeriksaan', 'selectedTemplates']);
            $this->klinis = '-';
            $this->info = '-';
            
            $this->emit('resetForm');
            $this->emit('select2Lab');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan permintaan lab: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatchBrowserEvent('swal', $this->toastResponse($e->getMessage(), 'error'));
        }
    }

    public function getPermintaanLab()
    {
        $this->permintaanLab = DB::table('permintaan_lab')
            ->where('no_rawat', $this->noRawat)
            ->get();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function getDetailPemeriksaan($noOrder)
    {
        return DB::table('permintaan_pemeriksaan_lab')
            ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->where('permintaan_pemeriksaan_lab.noorder', $noOrder)
            ->select('jns_perawatan_lab.*')
            ->get();
    }

    public function konfirmasiHapus($id)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Konfirmasi Hapus Data',
            'text' => 'Apakah anda yakin ingin menghapus data ini?',
            'type' => 'warning',
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Tidak',
            'function' => 'deletePermintaanLab',
            'params' => [$id]
        ]);
    }

    public function deletePermintaanLab($noOrder)
    {
        try {
            DB::beginTransaction();
            
            DB::table('permintaan_detail_permintaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
                
            DB::table('permintaan_pemeriksaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
                
            DB::table('permintaan_lab')
                ->where('noorder', $noOrder)
                ->delete();

            $this->getPermintaanLab();
            DB::commit();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Permintaan Lab berhasil dihapus'));
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Log::error('Error saat menghapus permintaan lab', [
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ]);
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Permintaan Lab gagal dihapus', 'error'));
        }
    }
}
