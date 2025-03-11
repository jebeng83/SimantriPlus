<?php

namespace App\View\Components\Ranap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;

class PemeriksaanRanap extends Component
{
    public $noRawat, $noRM, $heads, $riwayat, $pemeriksaan, $encryptNoRawat;
    public $countTodayExams;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRM = Request::get('no_rm');
        $this->encryptNoRawat = $this->encryptData($noRawat);
        $this->noRawat = $noRawat;
        $this->heads = [
            'Tanggal',
            'Jam',
            'Keluhan',
            'Suhu',
            'Tensi',
            'Nadi',
            'Pilihan',
        ];
        $allRiwayat = $this->getRiwayat($this->noRM, false);
        $this->countTodayExams = $allRiwayat->filter(function($item) {
            return $item->tgl_perawatan == date('Y-m-d');
        })->count();
        $this->riwayat = $this->getRiwayat($this->noRM, true);
        // $this->pemeriksaan = DB::table('pemeriksaan_ranap')->where('no_rawat', $this->noRawat)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ranap.pemeriksaan-ranap', [
            'no_rawat' => $this->noRawat,
            'heads' => $this->heads,
            'riwayat' => $this->riwayat,
            'no_rm' => $this->noRM,
            'encryptNoRawat' => $this->encryptNoRawat,
        ]);
    }

    public function getRiwayat($noRM, $onlyToday = true)
    {
        try {
            // Buat query dasar
            $query = DB::table('pemeriksaan_ranap')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ranap.no_rawat')
                ->where('reg_periksa.no_rkm_medis', $noRM);
            
            // Filter hanya data hari ini jika parameter onlyToday true
            if ($onlyToday) {
                $query->where('pemeriksaan_ranap.tgl_perawatan', date('Y-m-d'));
            }
            
            // Ambil data dengan select dan urutkan
            $data = $query->select(
                'pemeriksaan_ranap.no_rawat',
                'pemeriksaan_ranap.tgl_perawatan',
                'pemeriksaan_ranap.jam_rawat',
                'pemeriksaan_ranap.keluhan',
                'pemeriksaan_ranap.pemeriksaan',
                'pemeriksaan_ranap.suhu_tubuh',
                'pemeriksaan_ranap.tensi',
                'pemeriksaan_ranap.nadi',
                'pemeriksaan_ranap.respirasi',
                'pemeriksaan_ranap.tinggi',
                'pemeriksaan_ranap.berat',
                'pemeriksaan_ranap.gcs',
                'pemeriksaan_ranap.kesadaran',
                'pemeriksaan_ranap.alergi',
                'pemeriksaan_ranap.penilaian',
                'pemeriksaan_ranap.rtl',
                'pemeriksaan_ranap.instruksi',
                'pemeriksaan_ranap.evaluasi',
                'pemeriksaan_ranap.spo2'
            )
            ->orderByDesc(DB::raw('CONCAT(pemeriksaan_ranap.tgl_perawatan, " ", pemeriksaan_ranap.jam_rawat)'))
            ->get();
            
            // Tambahkan timestamp untuk menghindari cache
            $data = $data->map(function($item) {
                $item->_timestamp = now()->timestamp;
                return $item;
            });
            
            return $data;
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Illuminate\Support\Facades\Log::error('Error di PemeriksaanRanap::getRiwayat: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'noRM' => $noRM
            ]);
            
            // Jika terjadi error, kembalikan koleksi kosong
            return collect();
        }
    }

    public function getPetugas()
    {
        return DB::table('pegawai')->get();
    }

    public function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}
