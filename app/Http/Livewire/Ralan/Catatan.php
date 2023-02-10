<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Catatan extends Component
{
    use SwalResponse;
    public $noRawat, $catatan, $isCollapsed = true, $listCatatan = [], $noRm;
    protected $rules = [
        'catatan' => 'required',
    ];

    protected $messages = [
        'catatan.required' => 'Catatan tidak boleh kosong',
    ];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
    }

    public function hydrate()
    {
        $this->getCatatan();
    }

    public function render()
    {
        return view('livewire.ralan.catatan');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function getCatatan()
    {
        $this->listCatatan = DB::table('catatan_pasien')
                                ->where('no_rkm_medis', $this->noRm)
                                ->get();
    }

    public function simpanCatatan()
    {
        $this->validate();

        try{
            DB::beginTransaction();
            DB::table('catatan_pasien')
                ->insert([
                    'no_rkm_medis' => $this->noRm,
                    'catatan' => $this->catatan,
                ]);
            
            DB::commit();
            $this->getCatatan();
            $this->catatan = '';
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Catatan Pasien berhasil ditambahkan'));
            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Catatan Pasien gagal ditambahkan', 'error'));
        }
    }

    public function hapusCatatan($noRM)
    {
        try{
            DB::beginTransaction();
            DB::table('catatan_pasien')
                ->where('no_rkm_medis', $noRM)
                ->delete();
            
            DB::commit();
            $this->getCatatan();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Catatan Pasien berhasil dihapus'));
            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Catatan Pasien gagal dihapus', 'error'));
        }
    }
}
