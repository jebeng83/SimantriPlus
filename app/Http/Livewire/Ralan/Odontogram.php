<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Odontogram extends Component
{
    use SwalResponse;
    public $isCollapsed = true, $gigi, $penyakit, $catatan, $listPemeriksaanOdontogram = [], $noRawat, $noRm, $swal = 'swal:odontogram';
    protected $rules = [
        'gigi' => 'required',
        'penyakit' => 'required',
        'catatan' => 'required',
    ];

    protected $messages = [
        'gigi.required' => 'Gigi tidak boleh kosong',
        'penyakit.required' => 'Penyakit tidak boleh kosong',
        'catatan.required' => 'Catatan tidak boleh kosong',
    ];

    protected $listeners = ['hapusOdontogram'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
    }

    public function hydrate()
    {
        $this->getPemeriksaanOdontogram();
    }

    public function render()
    {
        return view('livewire.ralan.odontogram');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function setGigi($value)
    {
        $this->gigi = $value;
    }

    public function setPenyakit($value)
    {
        $this->penyakit = $value;
    }

    public function getPemeriksaanOdontogram()
    {
        $this->listPemeriksaanOdontogram = DB::table('pemeriksaan_odontogram')
                                                ->where('no_rkm_medis', $this->noRm)
                                                ->get();
    }

    public function save()
    {
        $this->validate();

        try{
            DB::beginTransaction();
            DB::table('pemeriksaan_odontogram')
                ->insert([
                    'no_rawat' => $this->noRawat,
                    'no_rkm_medis' => $this->noRm,
                    'tgl_perawatan' => date('Y-m-d'),
                    'jam_rawat' => date('H:i:s'),
                    'gg_xx' => $this->gigi,
                    'value' => $this->penyakit,
                    'catatan' => $this->catatan,
                ]);
            $this->getPemeriksaanOdontogram();
            DB::commit();
            $this->dispatchBrowserEvent($this->swal, $this->toastResponse('Pemeriksaan Odontogram berhasil disimpan'));
            $this->reset(['gigi', 'penyakit', 'catatan']);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent($this->swal, $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan Odontogram gagal disimpan', 'error'));
        }
    }


    public function delete($id, $tgl)
    {
        $this->dispatchBrowserEvent($this->swal.':confirm', $this->swalConfirmDialog('hapusOdontogram', [$id, $tgl]));
    }

    public function hapusOdontogram($id)
    {
        try{
            DB::beginTransaction();
            DB::table('pemeriksaan_odontogram')
                ->where('no_rawat', $this->noRawat)
                ->where('gg_xx', $id)
                ->delete();

            $this->getPemeriksaanOdontogram();
            DB::commit();
            $this->dispatchBrowserEvent($this->swal, $this->toastResponse('Pemeriksaan Odontogram berhasil dihapus'));
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent($this->swal, $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan Odontogram gagal dihapus', 'error'));
        }
    }
}
