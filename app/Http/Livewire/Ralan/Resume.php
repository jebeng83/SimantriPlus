<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Resume extends Component
{
    use SwalResponse;
    public $isCollapsed = true, $noRawat, $noRm, $listKeluhan = [], $listRadiologi = [], $listLab = [], $listTerapi = [], $keluhan, $perawatan, $penunjang, $lab, $terapi, $diagnosa, $prosedur, $kondisi, $checkKeluhan = [], $checkRadiologi = [], $checkLab = [], $checkTerapi = [], $listResume = [];
    public $kdDiagnosa, $kdDiagnosa1, $kdDiagnosa2, $kdDiagnosa3, $kdDiagnosa4;
    public $diagnosa1, $diagnosa2, $diagnosa3, $diagnosa4;
    public $prosedur1, $prosedur2, $prosedur3;
    public $kdProsedur, $kdProsedur1, $kdProsedur2, $kdProsedur3;

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
        $this->getPerawatan();
    }

    public function getPerawatan()
    {
        $data = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->orderByDesc('jam_rawat')
            ->orderByDesc('tgl_perawatan')
            ->first();
        $this->perawatan = $data->pemeriksaan ?? '';
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
            ->where('pasien.no_rkm_medis', $this->noRm)
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
            ->select('icd9.deskripsi_panjang', 'icd9.kode')
            ->first();
        $this->prosedur = $prosedur->deskripsi_panjang ?? '';
        $this->kdProsedur = $prosedur->kode ?? '';
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

        $racikan = DB::table('resep_dokter_racikan_detail')
            ->join('resep_obat', 'resep_dokter_racikan_detail.no_resep', '=', 'resep_obat.no_resep')
            ->join('databarang', 'resep_dokter_racikan_detail.kode_brng', '=', 'databarang.kode_brng')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('resep_obat.no_rawat', $this->noRawat)
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->select(DB::raw("GROUP_CONCAT( databarang.nama_brng, ' - ', resep_dokter_racikan_detail.p1, '/', resep_dokter_racikan_detail.p2, ' - ', resep_dokter_racikan_detail.kandungan, ' - ', resep_dokter_racikan_detail.jml SEPARATOR '\r\n') AS nama_racik"))
            ->first();

        $terapi = $terapi->nama_brng . "\r\n" . $racikan->nama_racik ?? '';

        $this->terapi = $terapi;
    }

    public function hapusTerapi()
    {
        $this->terapi = '';
    }

    public function tambahKeluhan()
    {
        if (!empty($this->checkKeluhan)) {
            $this->keluhan = '';
            foreach ($this->checkKeluhan as $kel) {
                $this->keluhan .= $kel . ', ';
            }
        } else {
            $this->keluhan = '';
        }
        $this->emit('closeKeluhanModal');
    }

    public function tambahPemeriksaanRadiologi()
    {
        if (!empty($this->checkRadiologi)) {
            $this->penunjang = '';
            foreach ($this->checkRadiologi as $kel) {
                $this->penunjang .= $kel . ', ';
            }
        } else {
            $this->penunjang = '';
        }
        $this->emit('closePemeriksaanRadiologiModal');
    }

    public function tambahPemeriksaanLab()
    {
        if (!empty($this->checkLab)) {
            $this->lab = '';
            foreach ($this->checkLab as $kel) {
                $this->lab .= $kel . ', ';
            }
        } else {
            $this->lab = '';
        }
        $this->emit('closePemeriksaanLabModal');
    }

    public function tambahTerapi()
    {
        if (!empty($this->checkTerapi)) {
            $this->terapi = '';
            foreach ($this->checkTerapi as $kel) {
                $this->terapi .= $kel . ', ';
            }
        } else {
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
            'kd_diagnosa_utama' => $this->kdDiagnosa ?? '',
            'diagnosa_sekunder' => $this->diagnosa1 ?? '',
            'kd_diagnosa_sekunder' => $this->kdDiagnosa1 ?? '',
            'diagnosa_sekunder2' => $this->diagnosa2 ?? '',
            'kd_diagnosa_sekunder2' => $this->kdDiagnosa2 ?? '',
            'diagnosa_sekunder3' => $this->diagnosa3 ?? '',
            'kd_diagnosa_sekunder3' => $this->kdDiagnosa3 ?? '',
            'diagnosa_sekunder4' => $this->diagnosa4 ?? '',
            'kd_diagnosa_sekunder4' => $this->kdDiagnosa4 ?? '',
            'prosedur_utama' => $this->prosedur,
            'kd_prosedur_utama' => $this->kdProsedur ?? '',
            'prosedur_sekunder' => $this->prosedur1 ?? '',
            'kd_prosedur_sekunder' => $this->kdProsedur1 ?? '',
            'prosedur_sekunder2' => $this->prosedur2 ?? '',
            'kd_prosedur_sekunder2' => $this->kdProsedur2 ?? '',
            'prosedur_sekunder3' => $this->prosedur3 ?? '',
            'kd_prosedur_sekunder3' => $this->kdProsedur3 ?? '',
            'kondisi_pulang' => $this->kondisi,
        ];

        try {
            DB::beginTransaction();
            DB::table('resume_pasien')->insert($data);
            DB::commit();
            $this->listResume = DB::table('resume_pasien')->where('no_rawat', $this->noRawat)->get();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Resume pasien berhasil disimpan"));
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? "Resume pasien gagal disimpan", 'error'));
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
        try {
            DB::beginTransaction();
            DB::table('resume_pasien')->where('no_rawat', $noRawat)->delete();
            DB::commit();
            $this->listResume = DB::table('resume_pasien')->where('no_rawat', $this->noRawat)->get();
            $this->dispatchBrowserEvent('swal', $this->toastResponse("Resume pasien berhasil dihapus"));
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? "Resume pasien gagal dihapus", 'error'));
        }
    }
}
