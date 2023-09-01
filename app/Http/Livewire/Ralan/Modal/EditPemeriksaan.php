<?php

namespace App\Http\Livewire\Ralan\Modal;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditPemeriksaan extends Component
{
    use LivewireAlert;
    public $keluhan, $pemeriksaan, $penilaian, $instruksi, $rtl, $alergi, $suhu, $berat, $tinggi, $tensi, $nadi, $respirasi, $evaluasi, $gcs, $kesadaran = 'Compos Mentis', $lingkar, $spo2;
    public $noRawat, $tgl, $jam;
    protected $listeners = ['openModalEditPemeriksaan' => 'openModal'];
    public function render()
    {
        return view('livewire.ralan.modal.edit-pemeriksaan');
    }

    public function openModal($noRawat, $tgl, $jam)
    {
        $this->noRawat = $noRawat;
        $this->tgl = $tgl;
        $this->jam = $jam;
        $pemeriksaan = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $noRawat)
            ->where('tgl_perawatan', $tgl)
            ->where('jam_rawat', $jam)
            ->first();
        $this->keluhan = $pemeriksaan->keluhan;
        $this->pemeriksaan = $pemeriksaan->pemeriksaan;
        $this->penilaian = $pemeriksaan->penilaian;
        $this->instruksi = $pemeriksaan->instruksi;
        $this->rtl = $pemeriksaan->rtl;
        $this->alergi = $pemeriksaan->alergi;
        $this->suhu = $pemeriksaan->suhu_tubuh;
        $this->berat = $pemeriksaan->berat;
        $this->tinggi = $pemeriksaan->tinggi;
        $this->tensi = $pemeriksaan->tensi;
        $this->nadi = $pemeriksaan->nadi;
        $this->respirasi = $pemeriksaan->respirasi;
        $this->evaluasi = $pemeriksaan->evaluasi;
        $this->gcs = $pemeriksaan->gcs;
        $this->kesadaran = $pemeriksaan->kesadaran;
        $this->lingkar = $pemeriksaan->lingkar_perut;
        $this->spo2 = $pemeriksaan->spo2;
        $this->dispatchBrowserEvent('openModalEditPemeriksaan');
    }

    public function simpan()
    {
        try {
            DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $this->noRawat)
                ->where('tgl_perawatan', $this->tgl)
                ->where('jam_rawat', $this->jam)
                ->update([
                    'keluhan' => $this->keluhan,
                    'pemeriksaan' => $this->pemeriksaan,
                    'penilaian' => $this->penilaian,
                    'instruksi' => $this->instruksi,
                    'rtl' => $this->rtl,
                    'alergi' => $this->alergi,
                    'suhu_tubuh' => $this->suhu,
                    'berat' => $this->berat,
                    'tinggi' => $this->tinggi,
                    'tensi' => $this->tensi,
                    'nadi' => $this->nadi,
                    'respirasi' => $this->respirasi,
                    'evaluasi' => $this->evaluasi,
                    'gcs' => $this->gcs,
                    'kesadaran' => $this->kesadaran,
                    'lingkar_perut' => $this->lingkar,
                    'spo2' => $this->spo2,
                ]);
            $this->emit('refreshData');
            $this->reset();
            $this->dispatchBrowserEvent('closeModalEditPemeriksaan');
            $this->alert('success', 'Data berhasil diubah', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
                'text' =>  $e->getMessage(),
                'confirmButtonText' =>  'Oke'
            ]);
        }
    }
}
