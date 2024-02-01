<?php

namespace App\Http\Livewire\Ranap\Sbar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TableSbar extends Component
{
    public $noRawat;
    public $sbar = [];
    public $headers = [
        'Tanggal',
        'Situasion',
        'Background',
        'Assesment',
        'Recommendation',
        'Advis',
        'Petugas',
        'Aksi'
    ];
    protected $listeners = [
        'loadSbar' => 'load'
    ];

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
    }

    public function render()
    {
        return view('livewire.ranap.sbar.table-sbar');
    }

    public function load()
    {
        try {

            $this->sbar = DB::table('catatan_sbar')
                ->leftJoin('petugas', 'catatan_sbar.nip', '=', 'petugas.nip')
                ->leftJoin('validasi_catatan_sbar', 'catatan_sbar.no_sbar', '=', 'validasi_catatan_sbar.no_sbar')
                ->where('catatan_sbar.no_rawat', $this->noRawat)
                ->select('catatan_sbar.*', 'petugas.nama', 'validasi_catatan_sbar.advis')
                ->orderBy('catatan_sbar.tanggal', 'desc')
                ->get();

            // $this->emit('sbarLoaded', $this->sbar);

        } catch (\Exception $e) {
            // dd($e);
        }
    }
}
