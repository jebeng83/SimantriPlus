<?php

namespace App\Http\Livewire\Ralan;

use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Diagnosa extends Component
{
    use LivewireAlert;
    public $noRawat, $noRM, $diagnosa, $prioritas;
    public $selectDiagnosa, $selectPrioritas;
    protected $listeners = ['refreshDiagnosa' => '$refresh', 'deleteDiagnosa' => 'delete'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRM = $noRm;
    }

    public function render()
    {
        return view('livewire.ralan.diagnosa', [
            'diagnosas' => DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $this->noRawat)
                ->get(),
        ]);
    }

    public function simpan()
    {
        $this->validate([
            'diagnosa' => 'required',
            'prioritas' => 'required',
        ], [
            'diagnosa.required' => 'Diagnosa tidak boleh kosong',
            'prioritas.required' => 'Prioritas tidak boleh kosong',
        ]);

        try {
            $cek_status = DB::table('diagnosa_pasien')
                ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('diagnosa_pasien.kd_penyakit', $this->diagnosa)
                ->where('reg_periksa.no_rkm_medis', $this->noRM)
                ->select('diagnosa_pasien.kd_penyakit')
                ->first();
            if ($cek_status) {
                $status = 'Lama';
            } else {
                $status = 'Baru';
            }
            $cek = DB::table('diagnosa_pasien')
                ->where('kd_penyakit', $this->diagnosa)
                ->where('no_rawat', $this->noRawat)->count();
            if ($cek > 0) {
                $this->alert('warning', 'Diagnosa sudah ada');
            } else {
                DB::table('diagnosa_pasien')->insert([
                    'no_rawat' => $this->noRawat,
                    'kd_penyakit' => $this->diagnosa,
                    'status' => 'Ralan',
                    'prioritas' => $this->prioritas,
                    'status_penyakit' => $status,
                ]);
                $this->dispatchBrowserEvent('resetSelect2');
                $this->reset(['diagnosa', 'prioritas']);
                $this->emit('refreshDiagnosa');
                $this->alert('success', 'Diagnosa berhasil ditambahkan');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Diagnosa gagal ditambahkan');
        }
    }

    public function confirmDelete($diagnosa, $prioritas)
    {
        $this->selectDiagnosa = $diagnosa;
        $this->selectPrioritas = $prioritas;
        $this->confirm('Yakin ingin menghapus diagnosa ini?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'deleteDiagnosa',
        ]);
    }

    public function delete()
    {
        try {
            DB::table('diagnosa_pasien')
                ->where('kd_penyakit', $this->selectDiagnosa)
                ->where('prioritas', $this->selectPrioritas)
                ->where('no_rawat', $this->noRawat)
                ->delete();
            $this->dispatchBrowserEvent('resetSelect2');
            $this->reset(['diagnosa', 'prioritas']);
            $this->alert('success', 'Diagnosa berhasil dihapus');
            $this->emit('refreshDiagnosa');
        } catch (\Exception $e) {
            $this->alert('error', 'Diagnosa gagal dihapus');
        }
    }
}
