<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChangeCard extends Component
{
    use LivewireAlert;
    public $no_card, $noRm;
    protected $listeners = ['setRmCard' => 'setCard'];

    public function render()
    {
        return view('livewire.component.change-card');
    }

    public function setCard($noRm, $noCard)
    {
        $this->noRm = $noRm;
        $this->no_card = $noCard;
    }

    public function simpan()
    {
        $this->validate([
            'no_card' => 'required|numeric|min:13'
        ],[
            'no_card.required' => 'No Kartu tidak boleh kosong',
            'no_card.numeric' => 'No Kartu harus berupa angka',
            'no_card.min' => 'No Kartu harus 13 digit'
        ]);

        try{

            DB::table('pasien')->where('no_rkm_medis', $this->noRm)->update([
                'no_peserta' => $this->no_card
            ]);

            $this->alert('success', 'No Kartu berhasil diubah');
            $this->emit('refreshCard', $this->no_card);
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
