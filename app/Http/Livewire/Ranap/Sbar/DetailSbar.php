<?php

namespace App\Http\Livewire\Ranap\Sbar;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DetailSbar extends Component
{
    use LivewireAlert;
    public $noSbar;
    public $situation;
    public $background;
    public $assesment;
    public $recommendation;
    public $advis;
    public $petugas;
    protected $listeners = [
        'pilihSbar' => 'pilihSbar'
    ];

    public function render()
    {
        return view('livewire.ranap.sbar.detail-sbar');
    }

    public function updatedPetugas()
    {
        if (empty($this->petugas)) {
            $this->emit('resetPetugas');
        }
    }

    public function pilihSbar($id)
    {
        $data = DB::table('catatan_sbar')
            ->leftJoin('validasi_catatan_sbar', 'catatan_sbar.no_sbar', '=', 'validasi_catatan_sbar.no_sbar')
            ->where('catatan_sbar.no_sbar', $id)
            ->select('catatan_sbar.*', 'validasi_catatan_sbar.advis')
            ->first();
        // dd($data);
        $this->noSbar = $data->no_sbar;
        $this->situation = $data->situation;
        $this->background = $data->background;
        $this->assesment = $data->assesment;
        $this->recommendation = $data->recommendation;
        $this->advis = $data->advis;
    }

    public function simpan()
    {
        $this->validate([
            'advis' => 'required',
            'petugas' => 'required'
        ], [
            'advis.required' => 'Advis harus diisi',
            'petugas.required' => 'Petugas harus diisi'
        ]);

        try {

            DB::table('validasi_catatan_sbar')
                ->upsert([
                    'no_sbar' => $this->noSbar,
                    'tanggal_validasi' => date('Y-m-d H:i:s'),
                    'advis' => $this->advis,
                    'nip' => $this->petugas
                ], ['no_sbar'], ['advis', 'nip']);

            $this->reset();
            $this->emit('loadSbar');
            $this->alert('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {

            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('production') ? 'Terjadi kesalahan' : $e->getMessage(),
                'confirmButtonText' =>  'Ok',
                'showConfirmButton' =>  true,
            ]);
        }
    }
}
