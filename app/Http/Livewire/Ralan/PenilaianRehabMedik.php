<?php

namespace App\Http\Livewire\Ralan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PenilaianRehabMedik extends Component
{
    public $data = [], $noRawat, $dokter;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->dokter = session()->get('username');
    }

    public function render()
    {
        return view('livewire.ralan.penilaian-rehab-medik');
    }

    public function simpan()
    {
        try{

            DB::table('penilaian_medis_ralan_rehab_medik')
                ->insert([
                    'no_rawat' => $this->noRawat,
                    'kd_dokter' => $this->dokter,
                    'tanggal' => date('Y-m-d H:i:s'),
                    'anamnesis' => $this->data['anamnesis'],
                    'hubungan' => $this->data['hubungan'],
                    'keluhan_utama' => $this->data['keluhan_utama'],
                    'rps'   =>  $this->data['rps'],
                    'kkf'   =>  $this->data['kkf'],
                    'rpor'  =>  $this->data['rpor'],
                    'rpd'   =>  $this->data['rpd'],
                    'rhp'   =>  $this->data['rhp'],
                    'harapan_pasien'    =>  $this->data['harapan_pasien'],
                    'gcs'   =>  $this->data['gcs'],
                    'td'    =>  $this->data['td'],
                    'hr'    =>  $this->data['hr'],
                    'rr'    =>  $this->data['rr'],
                    't'    =>  $this->data['t'],
                    'spo2'    =>  $this->data['spo2'],
                    'bb'    =>  $this->data['bb'],
                    'tb'    =>  $this->data['tb'],
                    'imt'    =>  $this->data['imt'],
                    'lk'    =>  $this->data['lk'],
                    'postur'    =>  $this->data['postur'],
                    'ambulasi'  =>  $this->data['ambulasi'],
                    'ekstremitas_do'    =>  $this->data['ekstremitas_do'],
                    'kepala_leher'  =>  $this->data['kepala_leher'],
                    'thorax_paru'   =>  $this->data['thorax_paru'],
                    'thorax_jantung'    =>  $this->data['thorax_jantung'],
                    'abdomen'   =>  $this->data['abdomen'],
                    'ekstremitas'   =>  $this->data['ekstremitas'],
                    'kep_look'  =>  $this->data['kep_look'],
                    'kep_feel'  =>  $this->data['kep_feel'],
                    'kep_rom_mmt'   =>  $this->data['kep_rom_mmt'],
                    'kep_special_test'  =>  $this->data['kep_special_test'],
                    'kep_saraf_crani'   =>  $this->data['kep_saraf_crani'],
                    'kep_sensorik'  =>  $this->data['kep_sensorik'],
                    'trunk_look'  =>  $this->data['trunk_look'],
                    'trunk_feel'  =>  $this->data['trunk_feel'],
                    'trunk_rom_mmt'   =>  $this->data['trunk_rom_mmt'],
                    'trunk_special_test'  =>  $this->data['kep_special_test'],
                    'kep_sensorik'  =>  $this->data['kep_sensorik'],
                    'aga_look'  =>  $this->data['aga_look'],
                    'aga_feel'  =>  $this->data['aga_feel'],
                    'aga_room_mmt'  =>  $this->data['aga_room_mmt'],
                    'aga_room_mmt_ket'  =>  $this->data['aga_room_mmt_ket'],
                    'aga_special_test'  =>  $this->data['aga_special_test'],
                    'aga_sensorik'  =>  $this->data['aga_sensorik'],
                    'aga_dtr'   =>  $this->data['aga_dtr'],
                    'aga_spastisitas'   =>  $this->data['aga_spastisitas'],
                    'aga_reflek_patologis'  =>  $this->data['aga_reflek_patologis'],
                    'agb_look'  =>  $this->data['agb_look'],
                    'agb_feel'  =>  $this->data['agb_feel'],
                    'agb_room_mmt'  =>  $this->data['agb_room_mmt'],
                    'agb_room_mmt_ket'  =>  $this->data['agb_room_mmt_ket'],
                    'agb_special_test'  =>  $this->data['agb_special_test'],
                    'agb_sensorik'  =>  $this->data['agb_sensorik'],
                    'agb_dtr'   =>  $this->data['agb_dtr'],
                    'agb_spastisitas'   =>  $this->data['agb_spastisitas'],
                    'agb_reflek_patologis'  =>  $this->data['agb_reflek_patologis'],
                    'pemeriksaan_fisik' =>  $this->data['pemeriksaan_fisik'],
                    'basic_adl' =>  $this->data['basic_adl'],
                    'kognisi'   =>  $this->data['kognisi'],
                    'kognisi_ket'   =>  $this->data['kognisi_ket'],
                    'count_test'    =>  $this->data['count_test'],
                    'chest_expansion'   =>  $this->data['chest_expansion'],
                    'balance'   =>  $this->data['balance'],
                    'transfer'  =>  $this->data['transfer'],
                    'coordination'  =>  $this->data['coordination'],
                    'fungsional_lainnya'    =>  $this->data['fungsional_lainnya'],
                    'pemeriksaan_penunjang' =>  $this->data['pemeriksaan_penunjang'],
                    'diag_klinis'   =>  $this->data['diag_klinis'],
                    'diag_anatomis' =>  $this->data['diag_anatomis'],
                    'diag_etiologis'    =>  $this->data['diag_etiologis'],
                    'icf_function'  =>  $this->data['icf_function'],
                    'icf_activity'  =>  $this->data['icf_activity'],
                    'icf_enviromental'  =>  $this->data['icf_enviromental'],
                    'icf_personal_factor'   =>  $this->data['icf_personal_factor'],
                    'progno_vitam'  =>  $this->data['progno_vitam'],
                    'progno_sanactionam'    =>  $this->data['progno_sanactionam'],
                    'progno_functionam' =>  $this->data['progno_functionam'],
                    'progno_ket'    =>  $this->data['progno_ket'],
                    'goal_pendek'   =>  $this->data['goal_pendek'],
                    'goal_panjang'  =>  $this->data['goal_panjang'],
                    'masalah'   =>  $this->data['masalah'],
                    'rencana'   =>  $this->data['rencana'],
                ]);

        }catch(\Exception $e){
            dd($e->getMessage());
        }
    }
}
