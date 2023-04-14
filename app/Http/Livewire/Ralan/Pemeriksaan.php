<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pemeriksaan extends Component
{
    use SwalResponse;
    public $isCollapsed = false, $noRawat, $isMaximized = true, $keluhan, $pemeriksaan, $penilaian, $instruksi, $rtl, $alergi, $suhu, $berat, $tinggi, $tensi, $nadi, $respirasi, $evaluasi, $gcs, $kesadaran = 'Compos Mentis', $lingkar, $spo2;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        if(!$this->isCollapsed){
            $this->getPemeriksaan();
        }
    }

    public function render()
    {
        return view('livewire.ralan.pemeriksaan');
    }

    public function hydrate()
    {
        $this->getPemeriksaan();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function expanded()
    {
        $this->isMaximized = !$this->isMaximized;
    }

    public function getPemeriksaan()
    {
        $pemeriksaan = DB::table('pemeriksaan_ralan')
                            ->where('no_rawat', $this->noRawat)
                            ->first();
        if($pemeriksaan){
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
        }
    }

    public function simpanPemeriksaan()
    {
        try{
            DB::beginTransaction();
            $pemeriksaan = DB::table('pemeriksaan_ralan')
                                ->where('no_rawat', $this->noRawat)
                                ->first();
            if($pemeriksaan){
                DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $this->noRawat)
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
                        'kesadaran' => $this->kesadaran,
                        'gcs' => $this->gcs,
                        'lingkar_perut' => $this->lingkar,
                        'spo2' => $this->spo2,
                    ]);
            }else{
                DB::table('pemeriksaan_ralan')
                    ->insert([
                        'no_rawat' => $this->noRawat,
                        'keluhan' => $this->keluhan,
                        'pemeriksaan' => $this->pemeriksaan,
                        'penilaian' => $this->penilaian ?? '-',
                        'instruksi' => $this->instruksi ?? '-',
                        'rtl' => $this->rtl ?? '-',
                        'alergi' => $this->alergi,
                        'suhu_tubuh' => $this->suhu,
                        'berat' => $this->berat,
                        'tinggi' => $this->tinggi,
                        'tensi' => $this->tensi ?? '-',
                        'nadi' => $this->nadi,
                        'respirasi' => $this->respirasi,
                        'gcs' => $this->gcs,
                        'kesadaran' => $this->kesadaran,
                        'lingkar_perut' => $this->lingkar,
                        'spo2' => $this->spo2 ?? '-',
                        'evaluasi' => $this->evaluasi ?? '-',
                        'tgl_perawatan' => date('Y-m-d'),
                        'jam_rawat' => date('H:i:s'),
                        'nip' => session()->get('username'),
                    ]);
            }
            
            DB::commit();
            $this->getPemeriksaan();
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse('Pemeriksaan berhasil ditambahkan'));

        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollback();
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        }
    }
}
