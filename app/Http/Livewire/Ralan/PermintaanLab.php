<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\EnkripsiData;
use App\Traits\SwalResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PermintaanLab extends Component
{
    use EnkripsiData, SwalResponse;
    public $noRawat, $klinis, $info, $jns_pemeriksaan = [], $permintaanLab = [], $isCollapsed = true, $isExpand = false, $noRawatEncrypted;

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

    protected $listeners = ['refreshPermintaanLab' => 'getPermintaanLab', 'deletePermintaanLab'];

    public function mount($noRawat)
    {
        try {
            // Simpan no_rawat terenkripsi untuk referensi
            $this->noRawatEncrypted = $noRawat;
            $this->noRawat = $this->dekripsi($noRawat);
            
            \Illuminate\Support\Facades\Log::info('Livewire PermintaanLab - mount', [
                'no_rawat_terenkripsi' => $noRawat,
                'no_rawat_terdekripsi' => $this->noRawat,
                'class' => get_class($this)
            ]);
            
            if (empty($this->noRawat)) {
                throw new \Exception('No Rawat kosong setelah dekripsi');
            }

            // Cek data langsung setelah mount
            $initialCheck = DB::table('permintaan_lab')
                            ->where('no_rawat', $this->noRawat)
                            ->get();
                            
            \Illuminate\Support\Facades\Log::info('Initial check pada mount:', [
                'no_rawat' => $this->noRawat,
                'jumlah_data' => $initialCheck->count(),
                'data' => $initialCheck->toArray()
            ]);
            
            $this->getPermintaanLab();
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada mount PermintaanLab: ' . $e->getMessage(), [
                'no_rawat_terenkripsi' => $noRawat,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function hydrate()
    {
        $this->getPermintaanLab();
    }

    public function render()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Render PermintaanLab dipanggil', [
                'no_rawat' => $this->noRawat,
                'jumlah_data' => $this->permintaanLab ? $this->permintaanLab->count() : 0
            ]);
            
            return view('livewire.ralan.permintaan-lab');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada render PermintaanLab: ' . $e->getMessage(), [
                'no_rawat' => $this->noRawat,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return view('livewire.ralan.permintaan-lab');
        }
    }

    public function selectedJnsPerawatan($item)
    {
        $this->jns_pemeriksaan = $item;
    }

    public function savePermintaanLab()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            $getNumber = DB::table('permintaan_lab')
                            ->where('tgl_permintaan', date('Y-m-d'))
                            ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                            ->first();

            $lastNumber = substr($getNumber->no, 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL'.date('Ymd').$getNextNumber;

            \Illuminate\Support\Facades\Log::info('Livewire - Menyimpan permintaan lab baru', [
                'no_rawat' => $this->noRawat,
                'noorder' => $noOrder
            ]);

            DB::table('permintaan_lab')
                    ->insert([
                        'noorder' => $noOrder,
                        'no_rawat' => $this->noRawat,
                        'tgl_permintaan' => date('Y-m-d'),
                        'jam_permintaan' => date('H:i:s'),
                        'dokter_perujuk' => session()->get('username'),
                        'diagnosa_klinis' =>  $this->klinis,
                        'informasi_tambahan' =>  $this->info,
                        'status' => 'ralan'
                    ]);
            
            \Illuminate\Support\Facades\Log::info('Livewire - Data utama permintaan lab tersimpan');

            foreach( $this->jns_pemeriksaan as $pemeriksaan){
                DB::table('permintaan_pemeriksaan_lab')
                        ->insert([
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $pemeriksaan,
                            'stts_bayar' => 'Belum'
                        ]);

                // Ambil template berdasarkan jenis pemeriksaan
                try {
                    $template = DB::table('template_laboratorium')
                                ->where(DB::raw('kd_jenis_prw COLLATE utf8mb4_unicode_ci'), $pemeriksaan)
                                ->select('id_template')
                                ->get();
                                
                    \Illuminate\Support\Facades\Log::info('Template ditemukan untuk pemeriksaan', [
                        'kd_jenis_prw' => $pemeriksaan,
                        'jumlah_template' => count($template)
                    ]);
                                
                    foreach($template as $temp){
                        DB::table('permintaan_detail_permintaan_lab')->insert([
                            'noorder'   =>  $noOrder,
                            'kd_jenis_prw'  =>  $pemeriksaan,
                            'id_template'   =>  $temp->id_template,
                            'stts_bayar'    =>  'Belum'
                        ]);
                    }
                } catch (\Exception $templateError) {
                    \Illuminate\Support\Facades\Log::error('Error saat ambil template: ' . $templateError->getMessage(), [
                        'kd_jenis_prw' => $pemeriksaan
                    ]);
                    // Lanjutkan meskipun ada error di template, untuk menghindari rollback transaksi
                }
            }
            
            DB::commit();
            
            // Refresh data setelah simpan
            $this->getPermintaanLab();
            
            // Reset form dan emit events
            $this->reset(['klinis', 'info', 'jns_pemeriksaan']);
            $this->emit('refreshPermintaanLab');
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Permintaan Lab berhasil ditambahkan'));
            $this->emit('select2Lab');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat simpan permintaan lab: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal', $this->toastResponse($e->getMessage() ?? 'Permintaan Lab gagal ditambahkan', 'error'));
        }
    }

    public function getPermintaanLab()
    {
        try {
            if (empty($this->noRawat)) {
                throw new \Exception('No Rawat kosong');
            }

            \Illuminate\Support\Facades\Log::info('Mengambil data permintaan lab', [
                'no_rawat' => $this->noRawat,
                'waktu' => now()->format('Y-m-d H:i:s')
            ]);

            // Query untuk mengambil data
            $query = DB::table('permintaan_lab AS pl')
                      ->where('pl.no_rawat', '=', trim($this->noRawat))
                      ->orderBy('pl.tgl_permintaan', 'desc')
                      ->orderBy('pl.jam_permintaan', 'desc');

            // Log query yang akan dijalankan
            \Illuminate\Support\Facades\Log::info('Query permintaan lab: ' . $query->toSql(), [
                'bindings' => $query->getBindings(),
                'raw_no_rawat' => $this->noRawat,
                'query_string' => vsprintf(str_replace('?', '%s', $query->toSql()), $query->getBindings())
            ]);

            $this->permintaanLab = $query->get();

            // Log hasil query
            \Illuminate\Support\Facades\Log::info('Data permintaan lab berhasil diambil', [
                'no_rawat' => $this->noRawat,
                'jumlah_data' => $this->permintaanLab->count(),
                'raw_data' => $this->permintaanLab->toArray()
            ]);

            // Jika data kosong, coba cek langsung dengan raw query
            if ($this->permintaanLab->isEmpty()) {
                $rawCheck = DB::select("SELECT * FROM permintaan_lab WHERE no_rawat = ?", [trim($this->noRawat)]);
                \Illuminate\Support\Facades\Log::info('Raw check permintaan lab:', [
                    'no_rawat' => $this->noRawat,
                    'hasil' => $rawCheck
                ]);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat mengambil data permintaan lab: ' . $e->getMessage(), [
                'no_rawat' => $this->noRawat,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->permintaanLab = collect();
        }
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function expanded()
    {
        $this->isExpand = !$this->isExpand;
    }

    public function resetForm()
    {
        $this->reset(['klinis', 'info', 'jns_pemeriksaan']);
        $this->dispatchBrowserEvent('select2Lab:reset');
    }

    public function getDetailPemeriksaan($noOrder)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Mengambil detail pemeriksaan untuk noorder: ' . $noOrder);
            
            // Ambil data pemeriksaan dari permintaan_pemeriksaan_lab
            $pemeriksaan = DB::table('permintaan_pemeriksaan_lab AS ppl')
                    ->join('jns_perawatan_lab AS jpl', function($join) {
                        $join->on('ppl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
                             ->whereRaw('BINARY ppl.kd_jenis_prw = BINARY jpl.kd_jenis_prw');
                    })
                    ->where('ppl.noorder', $noOrder)
                    ->select('ppl.kd_jenis_prw', 'jpl.nm_perawatan')
                    ->distinct()
                    ->get();

            \Illuminate\Support\Facades\Log::info('Data pemeriksaan ditemukan', [
                'noorder' => $noOrder,
                'jumlah_pemeriksaan' => $pemeriksaan->count(),
                'detail_pemeriksaan' => $pemeriksaan->pluck('nm_perawatan')->toArray()
            ]);

            return $pemeriksaan;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat mengambil detail pemeriksaan: ' . $e->getMessage(), [
                'noorder' => $noOrder,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return collect();
        }
    }

    public function getDetailTemplate($noOrder, $kdJenisPrw)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Mengambil detail template untuk pemeriksaan', [
                'noorder' => $noOrder,
                'kd_jenis_prw' => $kdJenisPrw
            ]);
            
            $templates = DB::table('permintaan_detail_permintaan_lab AS pdpl')
                    ->join('template_laboratorium AS tl', function($join) {
                        $join->on('pdpl.id_template', '=', 'tl.id_template')
                             ->whereRaw('BINARY pdpl.kd_jenis_prw = BINARY tl.kd_jenis_prw');
                    })
                    ->where('pdpl.noorder', $noOrder)
                    ->where('pdpl.kd_jenis_prw', $kdJenisPrw)
                    ->select(
                        'tl.Pemeriksaan as nama_pemeriksaan',
                        'tl.nilai_rujukan',
                        'tl.satuan',
                        'tl.urut'
                    )
                    ->orderBy('tl.urut')
                    ->get();
                    
            \Illuminate\Support\Facades\Log::info('Detail template ditemukan', [
                'noorder' => $noOrder,
                'kd_jenis_prw' => $kdJenisPrw,
                'jumlah_template' => $templates->count()
            ]);
            
            return $templates;
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat mengambil detail template: ' . $e->getMessage(), [
                'noorder' => $noOrder,
                'kd_jenis_prw' => $kdJenisPrw
            ]);
            return collect();
        }
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
        try{
            DB::beginTransaction();
            
            // Hapus detail template terlebih dahulu
            DB::table('permintaan_detail_permintaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
            
            \Illuminate\Support\Facades\Log::info('Detail template berhasil dihapus', [
                'noorder' => $noOrder
            ]);
                
            // Hapus pemeriksaan lab
            DB::table('permintaan_pemeriksaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
            
            \Illuminate\Support\Facades\Log::info('Pemeriksaan lab berhasil dihapus', [
                'noorder' => $noOrder
            ]);
                
            // Hapus permintaan lab
            DB::table('permintaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
            
            \Illuminate\Support\Facades\Log::info('Permintaan lab berhasil dihapus', [
                'noorder' => $noOrder
            ]);

            $this->getPermintaanLab();
            DB::commit();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Permintaan Lab berhasil dihapus'));
            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat hapus permintaan lab: ' . $ex->getMessage(), [
                'noorder' => $noOrder,
                'code' => $ex->getCode()
            ]);
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Permintaan Lab gagal dihapus', 'error'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat hapus permintaan lab: ' . $e->getMessage(), [
                'noorder' => $noOrder
            ]);
            $this->dispatchBrowserEvent('swal', $this->toastResponse($e->getMessage() ?? 'Permintaan Lab gagal dihapus', 'error'));
        }
    }
}
