<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChangeKtp extends Component
{
    use LivewireAlert;
    public $no_ktp, $noRm;
    protected $listeners = ['setRmKtp' => 'setKtp'];

    public function render()
    {
        return view('livewire.component.change-ktp');
    }

    public function setKtp($noRm, $noKtp)
    {
        $this->noRm = $noRm;
        $this->no_ktp = $noKtp;
    }

    public function simpan()
    {
        $this->validate([
            'no_ktp' => 'required|numeric|min:16'
        ],[
            'no_ktp.required' => 'No KTP tidak boleh kosong',
            'no_ktp.numeric' => 'No KTP harus berupa angka',
            'no_ktp.min' => 'No KTP harus 16 digit'
        ]);

        try{

            DB::table('pasien')->where('no_rkm_medis', $this->noRm)->update([
                'no_ktp' => $this->no_ktp
            ]);

            $this->alert('success', 'No KTP berhasil diubah');
            $this->emit('refreshKtp', $this->no_ktp);
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
