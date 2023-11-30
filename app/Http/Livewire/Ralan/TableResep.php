<?php

namespace App\Http\Livewire\Ralan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class TableResep extends Component
{
    use WithPagination;
    public $selectedItem = [], $selectAll = false, $resep, $noRawat, $dokter;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->dokter = session()->get('username');
        $this->getResep();
    }

    public function render()
    {
        return view('livewire.ralan.table-resep');
    }

    public function hydrate()
    {
        $this->getResep();
    }

    public function getResep()
    {
        $this->resep = DB::table('resep_dokter')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->where('resep_obat.no_rawat', $this->noRawat)
            ->where('resep_obat.kd_dokter', $this->dokter)
            ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
            ->get();
    }

    public function checkAll()
    {
        $this->selectAll = !$this->selectAll;
    }

    public function selectResep($kode_obat)
    {
        array_push($this->selectedItem, $kode_obat);
        dd($this->selectedItem);
    }
}
