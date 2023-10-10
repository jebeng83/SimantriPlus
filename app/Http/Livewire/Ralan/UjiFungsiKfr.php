<?php

namespace App\Http\Livewire\Ralan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UjiFungsiKfr extends Component
{
    use LivewireAlert;
    public $noRawat, $dokter;
    public $fungsional, $medis, $hasil, $kesimpulan, $rekomendasi, $idFkr;
    public $readyToLoad = false;
    protected $listeners = ['refreshData' => '$refresh', 'hapusKfr' => 'hapus'];

    public function loadDatas()
    {
        $this->readyToLoad = true;
    }

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->dokter = session()->get('username');
    }

    public function render()
    {
        return view('livewire.ralan.uji-fungsi-kfr', [
            'datas' => $this->readyToLoad
                ? DB::table('uji_fungsi_kfr')
                ->where('no_rawat', $this->noRawat)
                ->get() : []
        ]);
    }

    public function confirmDelete($id)
    {
        $this->idFkr = $id;
        $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
            'onConfirmed' => 'hapusKfr',
        ]);
    }

    public function hapus()
    {
        try {
            DB::table('uji_fungsi_kfr')
                ->where('no_rawat', $this->idFkr)
                ->delete();

            $this->emit('refreshData');
            $this->alert('success', 'Berhasil', [
                'position' =>  'top-end',
                'timer' =>  3000,
                'toast' =>  true,
                'text' =>  'Data berhasil dihapus',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  false
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' =>  'top-end',
                'timer' =>  3000,
                'toast' =>  true,
                'text' =>  $e->getMessage(),
                'showCancelButton' =>  false,
                'showConfirmButton' =>  false
            ]);
        }
    }

    public function simpan()
    {
        $this->validate([
            'fungsional' => 'required',
            'medis' => 'required',
            'hasil' => 'required',
            'kesimpulan' => 'required',
            'rekomendasi' => 'required',
        ], [
            'fungsional.required' => 'Fungsional tidak boleh kosong',
            'medis.required' => 'Medis tidak boleh kosong',
            'hasil.required' => 'Hasil tidak boleh kosong',
            'kesimpulan.required' => 'Kesimpulan tidak boleh kosong',
            'rekomendasi.required' => 'Rekomendasi tidak boleh kosong',
        ]);

        try {

            DB::table('uji_fungsi_kfr')
                ->insert([
                    'no_rawat' => $this->noRawat,
                    'kd_dokter' => $this->dokter,
                    'tanggal' => date('Y-m-d H:i:s'),
                    'diagnosis_fungsional' => $this->fungsional,
                    'diagnosis_medis' => $this->medis,
                    'hasil_didapat' => $this->hasil,
                    'kesimpulan' => $this->kesimpulan,
                    'rekomedasi' => $this->rekomendasi,
                ]);

            $this->emit('refreshData');
            $this->alert('success', 'Berhasil', [
                'position' =>  'top-end',
                'timer' =>  3000,
                'toast' =>  true,
                'text' =>  'Data berhasil disimpan',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  false
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' =>  'top-end',
                'timer' =>  3000,
                'toast' =>  true,
                'text' =>  $e->getMessage(),
                'showCancelButton' =>  false,
                'showConfirmButton' =>  false
            ]);
        }
    }
}
