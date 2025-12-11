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
            // Normalisasi RM (menghapus spasi/NBSP) dan pilih RM tersimpan sebenarnya untuk update
            $noRmNormalizedParam = preg_replace('/\s+/', '', str_replace(chr(160), '', (string)$this->noRm));
            $storedPatient = DB::table('pasien')->select('no_rkm_medis')->where('no_rkm_medis', $this->noRm)->first();
            if (!$storedPatient) {
                $storedPatient = DB::table('pasien')
                    ->select('no_rkm_medis')
                    ->whereRaw("REPLACE(REPLACE(no_rkm_medis, CHAR(160), ''), ' ', '') = ?", [$noRmNormalizedParam])
                    ->first();
            }
            $noRmToUpdate = $storedPatient ? (string)$storedPatient->no_rkm_medis : (string)$this->noRm;

            DB::table('pasien')->where('no_rkm_medis', $noRmToUpdate)->update([
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
