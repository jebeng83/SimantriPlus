<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChangePhone extends Component
{
    use LivewireAlert;
    public $no_hp, $noRm;
    protected $listeners = ['setRmPhone' => 'setPhone'];

    public function render()
    {
        return view('livewire.component.change-phone');
    }

    public function setPhone($noRm, $noHp)
    {
        $this->noRm = $noRm;
        $this->no_hp = $noHp;
    }

    public function simpan()
    {
        $this->validate([
            'no_hp' => 'required|numeric|digits_between:10,13'
        ],[
            'no_hp.required' => 'No HP tidak boleh kosong',
            'no_hp.numeric' => 'No HP harus berupa angka',
            'no_hp.digits_between' => 'No HP harus 10 sampai 13 digit'
        ]);

        try{

            DB::table('pasien')->where('no_rkm_medis', $this->noRm)->update([
                'no_tlp' => $this->no_hp
            ]);

            $this->alert('success', 'No HP berhasil diubah');
            $this->emit('refreshPhone', $this->no_hp);
            $this->reset();

        }catch(\Exception $e){

            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan saat input data',
                'confirmButtonText' =>  'Tutup',
                'cancelButtonText' =>  'Batalkan',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }
}
