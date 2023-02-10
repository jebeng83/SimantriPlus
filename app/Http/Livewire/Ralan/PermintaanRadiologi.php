<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\EnkripsiData;
use App\Traits\SwalResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PermintaanRadiologi extends Component
{
    use EnkripsiData, SwalResponse;
    public $noRawat, $klinis, $info, $jns_pemeriksaan = [], $permintaanRad = [], $isCollapsed = true;
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

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
    }

    public function hydrate()
    {
        $this->getPermintaanRadiologi();
    }

    public function render()
    {
        return view('livewire.ralan.permintaan-radiologi');
    }

    public function selectedJnsPerawatan($item)
    {
        $this->jns_pemeriksaan = $item;
    }

    public function savePermintaanRadiologi()
    {
        $this->validate();

        try{
            DB::beginTransaction();
            $getNumber = DB::table('permintaan_radiologi')
                            ->where('tgl_permintaan', date('Y-m-d'))
                            ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                            ->first();

            $lastNumber = substr($getNumber->no, 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PR'.date('Ymd').$getNextNumber;

            DB::table('permintaan_radiologi')
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

            foreach( $this->jns_pemeriksaan as $pemeriksaan){
                DB::table('permintaan_pemeriksaan_radiologi')
                        ->insert([
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $pemeriksaan,
                            'stts_bayar' => 'Belum'
                        ]);
            }
            DB::commit();
            $this->getPermintaanRadiologi();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Permintaan Radiologi berhasil ditambahkab"));
            $this->emit('select2Rad');
            $this->resetForm();

        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();

            $this->dispatchBrowserEvent('swal', $this->toastResponse("Permintaan Radiologi gagal ditambahkan", 'error'));
        }

    }

    public function getPermintaanRadiologi()
    {
        $this->permintaanRad = DB::table('permintaan_radiologi')
                                    ->where('no_rawat', $this->noRawat)
                                    ->get();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function resetForm()
    {
        $this->reset(['klinis', 'info', 'jns_pemeriksaan']);
        $this->dispatchBrowserEvent('select2Rad:reset');
    }

    public function getDetailPemeriksaan($noOrder)
    {
        return DB::table('permintaan_pemeriksaan_radiologi')
                    ->join('jns_perawatan_radiologi', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                    ->where('permintaan_pemeriksaan_radiologi.noorder', $noOrder)
                    ->select('jns_perawatan_radiologi.*')
                    ->get();
    }

    public function deletePermintaanRadiologi($noOrder)
    {
        try{
            DB::beginTransaction();
            DB::table('permintaan_radiologi')
                ->where('noorder', $noOrder)
                ->delete();

            $this->getPermintaanRadiologi();
            DB::commit();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Permintaan Radiologi berhasil dihapus"));
            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Permintaan Radiologi gagal dihapus", 'error'));
        }
    }
}
