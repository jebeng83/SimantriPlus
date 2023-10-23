<?php

namespace App\Http\Livewire\Ranap;

use App\Traits\SwalResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ResumePasien extends Component
{
    use SwalResponse, LivewireAlert;
    public $isCollapsed = true, $noRawat, $noRM, $listKeluhan = [], $listRadiologi = [], $listLab = [], $keluhan, $perawatan, $penunjang, $lab, $obat, $kondisi, $listResume = [];
    public $diagnosa_awal, $alasan, $fisik, $operasi;
    public $listPemeriksaan = [], $listTindakanOperasi = [], $listObat = [], $listDiet = [], $listLabPending = [];
    public $checkFisik = [], $checkKeluhan = [], $checkRadiologi = [], $checkLab = [], $checkObat = [], $checkTindakanOperasi = [], $checkDiet = [], $checkLabPending = [];
    public $diagnosa, $diagnosa1, $diagnosa2, $diagnosa3, $diagnosa4;
    public $kdDiagnosa, $kdDiagnosa1, $kdDiagnosa2, $kdDiagnosa3, $kdDiagnosa4;
    public $prosedur, $prosedur1, $prosedur2, $prosedur3;
    public $kdProsedur, $kdProsedur1, $kdProsedur2, $kdProsedur3;
    public $diet, $labPending, $alergi, $instruksi, $keadaanPulang, $keadaanPulangKet;
    public $caraKeluar, $caraKeluarKet, $dilanjutkan, $dilanjutkanKet, $obatPulang;
    public $loadDiagnosaAwal = false;

    // protected $rules = [
    //     'keluhan' => 'required',
    //     'perawatan' => 'required',
    //     'diagnosa' => 'required',
    //     'prosedur' => 'required',
    //     'kondisi' => 'required',
    //     'diagnosa_awal' => 'required',
    //     'alasan' => 'required',
    //     'fisik' => 'required',
    //     'operasi' => 'required',
    // ];

    // protected $messages = [
    //     'keluhan.required' => 'Keluhan tidak boleh kosong',
    //     'perawatan.required' => 'Perawatan tidak boleh kosong',
    //     'diagnosa.required' => 'Diagnosa Utama tidak boleh kosong',
    //     'prosedur.required' => 'Prosedur Utama tidak boleh kosong',
    //     'kondisi.required' => 'Kondisi Pulang tidak boleh kosong',
    //     'diagnosa_awal.required' => 'Diagnosa Awal tidak boleh kosong',
    //     'alasan.required' => 'Alasan tidak boleh kosong',
    //     'fisik.required' => 'Pemeriksaan Fisik tidak boleh kosong',
    //     'operasi.required' => 'Operasi tidak boleh kosong',
    // ];

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->kondisi = 'Hidup';
        $this->keadaanPulang = 'Membaik';
        $this->caraKeluar = 'Atas Izin Dokter';
        $this->dilanjutkan = 'Kembali Ke RS';
        $this->getProsedur();
        $this->getDiagnosa();
        // $this->getPerawatan();
        $this->getDiagnosaAwal();
    }

    public function hydrate()
    {
        $this->listResume = DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->get();
    }

    public function render()
    {
        return view('livewire.ranap.resume-pasien');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function edit()
    {
        try {
            $data = DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->first();
            if ($data) {
                $this->diagnosa_awal = $data->diagnosa_awal ?? '';
                $this->alasan = $data->alasan ?? '';
                $this->keluhan = $data->keluhan_utama ?? '';
                $this->fisik = $data->pemeriksaan_fisik ?? '';
                $this->perawatan = $data->jalannya_penyakit ?? '';
                $this->penunjang = $data->pemeriksaan_penunjang ?? '';
                $this->lab = $data->hasil_laborat ?? '';
                $this->obat = $data->obat_di_rs ?? '';
                $this->diagnosa = $data->diagnosa_utama ?? '';
                $this->diagnosa1 = $data->diagnosa_sekunder ?? '';
                $this->diagnosa2 = $data->diagnosa_sekunder2 ?? '';
                $this->diagnosa3 = $data->diagnosa_sekunder3 ?? '';
                $this->diagnosa4 = $data->diagnosa_sekunder4 ?? '';
                $this->kdDiagnosa = $data->kd_diagnosa_utama ?? '';
                $this->kdDiagnosa1 = $data->kd_diagnosa_sekunder ?? '';
                $this->kdDiagnosa2 = $data->kd_diagnosa_sekunder2 ?? '';
                $this->kdDiagnosa3 = $data->kd_diagnosa_sekunder3 ?? '';
                $this->kdDiagnosa4 = $data->kd_diagnosa_sekunder4 ?? '';
                $this->prosedur = $data->prosedur_utama ?? '';
                $this->prosedur1 = $data->prosedur_sekunder ?? '';
                $this->prosedur2 = $data->prosedur_sekunder2 ?? '';
                $this->prosedur3 = $data->prosedur_sekunder3 ?? '';
                $this->kdProsedur = $data->kd_prosedur_utama ?? '';
                $this->kdProsedur1 = $data->kd_prosedur_sekunder ?? '';
                $this->kdProsedur2 = $data->kd_prosedur_sekunder2 ?? '';
                $this->kdProsedur3 = $data->kd_prosedur_sekunder3 ?? '';
                $this->alergi = $data->alergi ?? '';
                $this->diet = $data->diet ?? '';
                $this->labPending = $data->lab_belum ?? '';
                $this->instruksi = $data->edukasi ?? '';
                $this->caraKeluar = $data->cara_keluar ?? '';
                $this->caraKeluarKet = $data->ket_keluar ?? '';
                $this->keadaanPulang = $data->keadaan ?? '';
                $this->keadaanPulangKet = $data->ket_keadaan ?? '';
                $this->dilanjutkan = $data->dilanjutkan ?? '';
                $this->dilanjutkanKet = $data->ket_dilanjutkan ?? '';
                $this->obatPulang = $data->obat_pulang ?? '';
            }
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'position' =>  'center',
                'toast' =>  false,
                'text' =>  '',
                'confirmButtonText' =>  'Ok',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }

    public function getProsedur()
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

    public function getDiagnosa()
    {
        $diagnosa = DB::table('diagnosa_pasien')
            ->join('icd9', 'diagnosa_pasien.kd_penyakit', '=', 'icd9.kode')
            ->where('diagnosa_pasien.no_rawat', $this->noRawat)
            ->where('diagnosa_pasien.prioritas', '1')
            ->where('diagnosa_pasien.status', 'Ralan')
            ->select('icd9.deskripsi_panjang', 'icd9.kode')
            ->first();

        $this->diagnosa = $diagnosa->deskripsi_panjang ?? '';
        $this->kdDiagnosa = $diagnosa->kode ?? '';
    }

    public function getDiagnosaAwal()
    {
        $data = DB::table('kamar_inap')
            ->where('no_rawat', $this->noRawat)
            ->select('diagnosa_awal')
            ->first();
        $this->diagnosa_awal = $data->diagnosa_awal ?? '';
        $this->loadDiagnosaAwal = true;
    }

    public function getTindakanOperasi()
    {
        $dataRalan = DB::table('rawat_jl_dr')
            ->join('jns_perawatan', 'rawat_jl_dr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->where('rawat_jl_dr.no_rawat', $this->noRawat)
            ->orderBy('rawat_jl_dr.tgl_perawatan', 'desc')
            ->select('rawat_jl_dr.tgl_perawatan', 'rawat_jl_dr.jam_rawat', 'jns_perawatan.nm_perawatan')
            ->get();

        $dataRanap = DB::table('rawat_inap_dr')
            ->join('jns_perawatan_inap', 'rawat_inap_dr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->where('rawat_inap_dr.no_rawat', $this->noRawat)
            ->orderBy('rawat_inap_dr.tgl_perawatan', 'desc')
            ->select('rawat_inap_dr.tgl_perawatan', 'rawat_inap_dr.jam_rawat', 'jns_perawatan_inap.nm_perawatan')
            ->get();

        $data = $dataRalan->merge($dataRanap);
        $this->listTindakanOperasi = $data;
        $this->dispatchBrowserEvent('openTindakanOperasiModal');
    }

    public function getObat()
    {
        // $data = DB::table('detail_pemberian_obat')
        //     ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
        //     ->join('resep_obat', 'resep_obat.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
        //     ->join('resep_dokter', 'resep_dokter.no_resep', '=', 'resep_obat.no_resep')
        //     ->where('detail_pemberian_obat.no_rawat', $this->noRawat)
        //     ->where('resep_dokter.aturan_pakai', '<>', '')
        //     ->orderBy('detail_pemberian_obat.tgl_perawatan', 'desc')
        //     ->orderBy('detail_pemberian_obat.jam', 'desc')
        //     ->groupBy('databarang.nama_brng')
        //     ->select('detail_pemberian_obat.tgl_perawatan', 'detail_pemberian_obat.jam', 'databarang.nama_brng', 'detail_pemberian_obat.jml', 'databarang.kode_sat', 'resep_dokter.aturan_pakai')
        //     ->get();

        $data = DB::table('resep_obat')
            ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_obat.no_rawat', $this->noRawat)
            ->select('databarang.nama_brng', 'databarang.kode_sat', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
            ->get();

        $this->listObat = $data;
        $this->dispatchBrowserEvent('openObatModal');
    }

    public function getKeluhanUtama()
    {
        $this->listKeluhan = DB::table('pemeriksaan_ranap')
            ->where('no_rawat', $this->noRawat)
            ->get();
        $this->emit('getKeluhanUtama');
    }

    public function getPemeriksaanFisik()
    {
        $this->listPemeriksaan = DB::table('pemeriksaan_ranap')
            ->where('no_rawat', $this->noRawat)
            ->get();
        $this->emit('getPemeriksaanFisik');
    }

    public function getPemeriksaanRadiologi()
    {
        $this->listRadiologi = DB::table('hasil_radiologi')
            ->where('no_rawat', $this->noRawat)
            ->get();
        $this->emit('getPemeriksaanRadiologi');
    }

    public function getPemeriksaanLab()
    {
        $this->listLab = DB::table('detail_periksa_lab')
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
            ->where('detail_periksa_lab.no_rawat', $this->noRawat)
            ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai')
            ->get();
        $this->emit('getPemeriksaanLab');
    }

    public function getDiet()
    {
        $this->listDiet = DB::table('detail_beri_diet')
            ->join('diet_jenis', 'detail_beri_diet.kd_jenis', '=', 'diet_jenis.kd_jenis')
            ->where('detail_beri_diet.no_rawat', $this->noRawat)
            ->orderBy('detail_beri_diet.tanggal', 'desc')
            ->orderBy('detail_beri_diet.waktu', 'desc')
            ->select('detail_beri_diet.tanggal', 'detail_beri_diet.waktu', 'diet_jenis.nama_jenis as nama_diet')
            ->get();
        $this->emit('openDietModal');
    }

    public function getLabPending()
    {
        $this->listLabPending = DB::table('permintaan_lab')
            ->join('permintaan_detail_permintaan_lab', 'permintaan_detail_permintaan_lab.noorder', '=', 'permintaan_lab.noorder')
            ->join('template_laboratorium', 'permintaan_detail_permintaan_lab.id_template', '=', 'template_laboratorium.id_template')
            ->where('permintaan_lab.tgl_hasil', '0000-00-00')
            ->where('permintaan_lab.no_rawat', $this->noRawat)
            ->orderBy('permintaan_lab.tgl_permintaan', 'desc')
            ->orderBy('permintaan_lab.jam_permintaan', 'desc')
            ->select('permintaan_lab.tgl_permintaan', 'permintaan_lab.jam_permintaan', 'template_laboratorium.Pemeriksaan')
            ->get();
        $this->emit('openLabPendingModal');
    }

    public function tambahPemeriksaanLabPending()
    {
        if (!empty($this->checkLabPending)) {
            $this->labPending = '';
            foreach ($this->checkLabPending as $key => $kel) {
                if ($key == array_key_last($this->checkLabPending)) {
                    $this->labPending .= $kel;
                } else {
                    $this->labPending .= $kel . ', ';
                }
            }
        } else {
            $this->labPending = '';
        }
        $this->reset('listLabPending', 'checkLabPending');
        $this->emit('closeLabPendingModal');
    }

    public function tambahDiet()
    {
        if (!empty($this->checkDiet)) {
            $this->diet = '';
            foreach ($this->checkDiet as $key => $kel) {
                if ($key == array_key_last($this->checkDiet)) {
                    $this->diet .= $kel;
                } else {
                    $this->diet .= $kel . ', ';
                }
            }
        } else {
            $this->diet = '';
        }
        $this->reset('listDiet', 'checkDiet');
        $this->emit('closeDietModal');
    }

    public function tambahObat()
    {
        if (!empty($this->checkObat)) {
            $this->obat = '';
            foreach ($this->checkObat as $key => $kel) {
                if ($key == array_key_last($this->checkObat)) {
                    $this->obat .= $kel;
                } else {
                    $this->obat .= $kel . ', ';
                }
            }
        } else {
            $this->obat = '';
        }
        $this->reset('listObat', 'checkObat');
        $this->dispatchBrowserEvent('closeObatModal');
    }

    public function tambahTindakanOperasi()
    {
        if (!empty($this->checkTindakanOperasi)) {
            $this->operasi = '';
            foreach ($this->checkTindakanOperasi as $key => $kel) {
                if ($key == array_key_last($this->checkTindakanOperasi)) {
                    $this->operasi .= $kel;
                } else {
                    $this->operasi .= $kel . ', ';
                }
            }
        } else {
            $this->operasi = '';
        }
        $this->reset('listTindakanOperasi', 'checkTindakanOperasi');
        $this->dispatchBrowserEvent('closeTindakanOperasiModal');
    }

    public function tambahPemeriksaanFisik()
    {
        // dd($this->checkFisik);
        if (!empty($this->checkFisik)) {
            $this->fisik = '';
            foreach ($this->checkFisik as $key => $kel) {
                if ($key == array_key_last($this->checkFisik)) {
                    $this->fisik .= $kel;
                } else {
                    $this->fisik .= $kel . ', ';
                }
            }
        } else {
            $this->fisik = '';
        }
        $this->reset('listPemeriksaan', 'checkFisik');
        $this->emit('closePemeriksaanFisikModal');
    }

    public function tambahKeluhan()
    {
        if (!empty($this->checkKeluhan)) {
            $this->keluhan = '';
            foreach ($this->checkKeluhan as $key => $kel) {
                if ($key == array_key_last($this->checkKeluhan)) {
                    $this->keluhan .= $kel;
                } else {
                    $this->keluhan .= $kel . ', ';
                }
            }
        } else {
            $this->keluhan = '';
        }
        $this->reset('listKeluhan', 'checkKeluhan');
        $this->emit('closeKeluhanModal');
    }

    public function tambahPemeriksaanRadiologi()
    {
        if (!empty($this->checkRadiologi)) {
            $this->penunjang = '';
            foreach ($this->checkRadiologi as $key => $kel) {
                if ($key == array_key_last($this->checkRadiologi)) {
                    $this->penunjang .= $kel;
                } else {
                    $this->penunjang .= $kel . ', ';
                }
            }
        } else {
            $this->penunjang = '';
        }
        $this->reset('listRadiologi', 'checkRadiologi');
        $this->emit('closePemeriksaanRadiologiModal');
    }

    public function tambahPemeriksaanLab()
    {
        if (!empty($this->checkLab)) {
            $this->lab = '';
            foreach ($this->checkLab as $key => $kel) {
                if ($key == array_key_last($this->checkLab)) {
                    $this->lab .= $kel;
                } else {
                    $this->lab .= $kel . ', ';
                }
            }
        } else {
            $this->lab = '';
        }
        $this->reset('listLab', 'checkLab');
        $this->emit('closePemeriksaanLabModal');
    }

    public function getPerawatan()
    {
        $data = DB::table('pemeriksaan_ranap')
            ->where('no_rawat', $this->noRawat)
            ->orderByDesc('jam_rawat')
            ->orderByDesc('tgl_perawatan')
            ->first();
        $this->perawatan = $data->pemeriksaan ?? '';
    }

    public function simpanResume()
    {

        $data = [
            'no_rawat' => $this->noRawat,
            'kd_dokter' => session()->get('username'),
            'diagnosa_awal' => $this->diagnosa_awal ?? '',
            'alasan' => $this->alasan ?? '',
            'keluhan_utama' => $this->keluhan ?? '',
            'pemeriksaan_fisik' => $this->fisik ?? '',
            'jalannya_penyakit' => $this->perawatan ?? '',
            'pemeriksaan_penunjang' => $this->penunjang ?? '',
            'hasil_laborat' => $this->lab ?? '',
            'tindakan_dan_operasi' => $this->operasi ?? '',
            'obat_di_rs' => $this->obat ?? '',
            'diagnosa_utama' => $this->diagnosa ?? '',
            'kd_diagnosa_utama' => $this->kdDiagnosa ?? '',
            'diagnosa_sekunder' => $this->diagnosa1 ?? '',
            'kd_diagnosa_sekunder' => $this->kdDiagnosa1 ?? '',
            'diagnosa_sekunder2' => $this->diagnosa2 ?? '',
            'kd_diagnosa_sekunder2' => $this->kdDiagnosa2 ?? '',
            'diagnosa_sekunder3' => $this->diagnosa3 ?? '',
            'kd_diagnosa_sekunder3' => $this->kdDiagnosa3 ?? '',
            'diagnosa_sekunder4' => $this->diagnosa4 ?? '',
            'kd_diagnosa_sekunder4' => $this->kdDiagnosa4 ?? '',
            'prosedur_utama' => $this->prosedur ?? '',
            'kd_prosedur_utama' => $this->kdProsedur ?? '',
            'prosedur_sekunder' => $this->prosedur1 ?? '',
            'kd_prosedur_sekunder' => $this->kdProsedur1 ?? '',
            'prosedur_sekunder2' => $this->prosedur2 ?? '',
            'kd_prosedur_sekunder2' => $this->kdProsedur2 ?? '',
            'prosedur_sekunder3' => $this->prosedur3 ?? '',
            'kd_prosedur_sekunder3' => $this->kdProsedur3 ?? '',
            'alergi' => $this->alergi ?? '',
            'diet' => $this->diet ?? '',
            'lab_belum' => $this->labPending ?? '',
            'edukasi' => $this->instruksi ?? '',
            'cara_keluar' => $this->caraKeluar ?? '',
            'ket_keluar' => $this->caraKeluarKet ?? '',
            'keadaan' => $this->keadaanPulang ?? '',
            'ket_keadaan' => $this->keadaanPulangKet ?? '',
            'dilanjutkan' => $this->dilanjutkan ?? '',
            'ket_dilanjutkan' => $this->dilanjutkanKet ?? '',
            'kontrol' => date('Y-m-d H:m:s', strtotime('+7 days')),
            'obat_pulang' => $this->obatPulang ?? '',
        ];

        try {
            DB::beginTransaction();
            $cek = DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->first();
            if ($cek) {
                $data = Arr::except($data, ['no_rawat']);
                DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->update($data);
            } else {
                DB::table('resume_pasien_ranap')->insert($data);
            }
            DB::commit();
            $this->listResume = DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->get();
            // $this->dispatchBrowserEvent('swal', $this->toastResponse("Resume pasien berhasil disimpan"));
            $this->reset('diagnosa_awal', 'alasan', 'keluhan', 'fisik', 'perawatan', 'penunjang', 'lab', 'obat', 'diagnosa', 'diagnosa1', 'diagnosa2', 'diagnosa3', 'diagnosa4', 'prosedur', 'prosedur1', 'prosedur2', 'prosedur3', 'alergi', 'diet', 'labPending', 'instruksi', 'caraKeluar', 'caraKeluarKet', 'keadaanPulang', 'keadaanPulangKet', 'dilanjutkan', 'dilanjutkanKet', 'obatPulang');
            $this->alert('success', 'Resume pasien berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $this->alert('error', $ex->getMessage() ?? "Resume pasien gagal disimpan", [
                'position' =>  'center',
                'toast' =>  false,
                'text' =>  '',
                'confirmButtonText' =>  'Ok',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }

    public function hapusResume($noRawat)
    {
        try {
            DB::beginTransaction();
            DB::table('resume_pasien_ranap')->where('no_rawat', $noRawat)->delete();
            DB::commit();
            $this->listResume = DB::table('resume_pasien_ranap')->where('no_rawat', $this->noRawat)->get();
            $this->alert('success', 'Resume pasien berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $this->alert('error', $ex->getMessage() ?? "Resume pasien gagal dihapus", [
                'position' =>  'center',
                'toast' =>  false,
                'text' =>  '',
                'confirmButtonText' =>  'Ok',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }
}
