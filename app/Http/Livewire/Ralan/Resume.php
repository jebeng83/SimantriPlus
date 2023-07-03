<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Resume extends Component
{
    use SwalResponse;
    public $isCollapsed = true, $noRawat, $noRm, $listKeluhan = [], $listRadiologi = [], $listLab = [], $listTerapi = [], $keluhan, $perawatan, $penunjang, $lab, $terapi, $diagnosa, $prosedur, $kondisi, $checkKeluhan = [], $checkRadiologi = [], $checkLab = [], $checkTerapi = [], $listResume = [];

    protected $rules = [
        'keluhan' => 'required',
        'perawatan' => 'required',
        'diagnosa' => 'required',
        'prosedur' => 'required',
        'kondisi' => 'required',
    ];

    protected $messages = [
        'keluhan.required' => 'Keluhan tidak boleh kosong',
        'perawatan.required' => 'Perawatan tidak boleh kosong',
        'diagnosa.required' => 'Diagnosa Utama tidak boleh kosong',
        'prosedur.required' => 'Prosedur Utama tidak boleh kosong',
        'kondisi.required' => 'Kondisi Pulang tidak boleh kosong',
    ];

    protected $listeners = ['hapusResume'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
        $this->kondisi = 'Hidup';
    }

    public function hydrate()
    {
        // $this->listKeluhan = DB::table('pemeriksaan_ralan')
        //                         ->where('no_rawat', $this->noRawat)
        //                         ->get();

        $this->listRadiologi = DB::table('hasil_radiologi')
                                ->where('no_rawat', $this->noRawat)
                                ->get();

        $this->listLab = DB::table('detail_periksa_lab')
                                ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                                ->where('detail_periksa_lab.no_rawat', $this->noRawat)
                                ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai')
                                ->get();

        // $this->listTerapi = DB::table('resep_obat')
        //                         ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
        //                         ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
        //                         ->where('resep_obat.no_rawat', $this->noRawat)
        //                         ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai', 'databarang.kode_sat')
        //                         ->get();

        $this->listResume = DB::table('resume_pasien')->where('no_rawat', $this->noRawat)->get();


    }

    public function render()
    {
        return view('livewire.ralan.resume');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
        $this->getKeluhanUtama();
        $this->getProsedurUtama();
        $this->getDiagnosaUtama();
        $this->getTerapi();
    }

    public function getKeluhanUtama()
    {
        // $this->emit('getKeluhanUtama');
        $data = DB::table('pemeriksaan_ralan')
                            ->where('no_rawat', $this->noRawat)
                            ->select('keluhan')
                            ->first();
        $this->keluhan = $data->keluhan ?? '';
    }

    public function getDiagnosaUtama()
    {
        $diagnosa = DB::table('resume_pasien')
                        ->join('reg_periksa', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                        ->where('pasien.no_rkm_medis',$this->noRm)
                        ->first();

        $this->diagnosa = $diagnosa->diagnosa_utama ?? '';
    }

    public function getProsedurUtama()
    {
        $prosedur = DB::table('prosedur_pasien')
                        ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                        ->where('prosedur_pasien.no_rawat', $this->noRawat)
                        ->where('prosedur_pasien.prioritas', '1')
                        ->where('prosedur_pasien.status', 'Ralan')
                        ->select('icd9.deskripsi_panjang')
                        ->first();

        $this->prosedur = $prosedur->deskripsi_panjang ?? '';
    }

    public function getPemeriksaanRadiologi()
    {
        $this->emit('getPemeriksaanRadiologi');
    }

    public function getPemeriksaanLab()
    {
        $this->emit('getPemeriksaanLab');
    }

    public function getTerapi()
    {
        // $this->emit('getTerapi');
        $terapi = DB::table('resep_dokter')
                            ->join('resep_obat', 'resep_dokter.no_resep', '=', 'resep_obat.no_resep')
                            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
                            ->where('resep_obat.no_rawat', $this->noRawat)
                            ->where('reg_periksa.status_lanjut', 'Ralan')
                            ->select(DB::raw("GROUP_CONCAT( databarang.nama_brng,'-', resep_dokter.jml SEPARATOR '\r\n') AS nama_brng"))
                            ->first();

        $this->terapi = $terapi->nama_brng ?? '';
    }

    public function hapusTerapi()
    {
        $this->terapi = '';
    }

    public function tambahKeluhan()
    {
        if(!empty($this->checkKeluhan)){
            $this->keluhan = '';
            foreach($this->checkKeluhan as $kel){
                $this->keluhan .= $kel.', ';
            }
        }else{
            $this->keluhan = '';
        }
        $this->emit('closeKeluhanModal');
    }

    public function tambahPemeriksaanRadiologi()
    {
        if(!empty($this->checkRadiologi)){
            $this->penunjang = '';
            foreach($this->checkRadiologi as $kel){
                $this->penunjang .= $kel.', ';
            }
        }else{
            $this->penunjang = '';
        }
        $this->emit('closePemeriksaanRadiologiModal');
    }

    public function tambahPemeriksaanLab()
    {
        if(!empty($this->checkLab)){
            $this->lab = '';
            foreach($this->checkLab as $kel){
                $this->lab .= $kel.', ';
            }
        }else{
            $this->lab = '';
        }
        $this->emit('closePemeriksaanLabModal');
    }

    public function tambahTerapi()
    {
        if(!empty($this->checkTerapi)){
            $this->terapi = '';
            foreach($this->checkTerapi as $kel){
                $this->terapi .= $kel.', ';
            }
        }else{
            $this->terapi = '';
        }
        $this->emit('closeTerapiModal');
    }

    public function simpanResume()
    {
        // $this->validate();

        $data = [
            'no_rawat' => $this->noRawat,
            'kd_dokter' => session()->get('username'),
            'keluhan_utama' => $this->keluhan,
            'jalannya_penyakit' => $this->perawatan ?? '',
            'pemeriksaan_penunjang' => $this->penunjang ?? '',
            'hasil_laborat' => $this->lab ?? '',
            'obat_pulang' => $this->terapi,
            'diagnosa_utama' => $this->diagnosa,
            'prosedur_utama' => $this->prosedur,
            'kondisi_pulang' => $this->kondisi,
        ];

        try{
            DB::beginTransaction();
            DB::table('resume_pasien')->insert($data);
            DB::commit();
            $this->listResume = DB::table('resume_pasien')->where('no_rawat', $this->noRawat)->get();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Resume pasien berhasil disimpan"));

        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse( $ex->getMessage() ?? "Resume pasien gagal disimpan", 'error'));
        }
    }

    public function konfirmasiHapus($id)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Konfirmasi Hapus Data',
            'text' => 'Apakah anda yakin ingin menghapus data ini?',
            'type' => 'warning',
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Tidak',
            'function' => 'hapusResume',
            'params' => [$id]
        ]);
    }

    public function hapusResume($noRawat)
    {
        try{
            DB::beginTransaction();
            DB::table('resume_pasien')->where('no_rawat', $noRawat)->delete();
            DB::commit();
            $this->listResume = DB::table('resume_pasien')->where('no_rawat', $this->noRawat)->get();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Resume pasien berhasil dihapus"));

        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse( $ex->getMessage() ?? "Resume pasien gagal dihapus", 'error'));
        }
    }
}
