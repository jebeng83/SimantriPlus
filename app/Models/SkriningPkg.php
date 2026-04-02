<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkriningPkg extends Model
{
    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'skrining_pkg';

    /**
     * Primary key yang digunakan
     */
    protected $primaryKey = 'id_pkg';

    /**
     * Mengindikasikan bahwa primary key adalah auto increment
     */
    public $incrementing = true;

    /**
     * Mengindikasikan bahwa model menggunakan timestamps (created_at dan updated_at)
     */
    public $timestamps = true;

    /**
     * Kolom yang dapat diisi (fillable)
     */
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'tanggal_lahir',
        'umur',
        'jenis_kelamin',
        'no_handphone',
        'kode_posyandu',
        'petugas_entri',
        'status_petugas',
        'no_rkm_medis',
        'tanggal_skrining',
        
        // Data Wali (untuk anak di bawah 5 tahun)
        'nik_wali',
        'nama_wali',
        'tanggal_lahir_wali',
        'jenis_kelamin_wali',
        
        // Demografi
        'status_perkawinan',
        'status_hamil',
        'status_disabilitas',
        
        // Kesehatan Jiwa
        'minat',
        'sedih',
        'cemas',
        'khawatir',
        
        // Aktivitas Fisik
        'frekuensi_olahraga',
        'durasi_olahraga',
        
        // Perilaku Merokok
        'status_merokok',
        'lama_merokok',
        'jumlah_rokok',
        'paparan_asap',
        
        // Tekanan Darah & Gula Darah
        'riwayat_hipertensi',
        'riwayat_diabetes',
        
        // Hati
        'riwayat_hepatitis',
        'riwayat_kuning',
        'riwayat_transfusi',
        'riwayat_tattoo',
        'riwayat_tindik',
        'narkoba_suntik',
        'odhiv',
        'kolesterol',
        
        // Kanker Leher Rahim
        'hubungan_intim',
        
        // Tuberkulosis
        'riwayat_merokok',
        'napas_pendek',
        'dahak',
        'batuk',
        'spirometri',
        
        // Antropometri dan Laboratorium
        'tinggi_badan',
        'berat_badan',
        'berat_badan_balita',
        'berat_lahir',
        'pjb_tangan_kanan',
        'pjb_kaki',
        'darah_tumit',
        'shk',
        'G6PD',
        'hak',
        'konfirmasi_shk',
        'konfirmasi_g6pd',
        'konfirmasi_hak',
        'edukasi_warna_kulit',
        'hasil_kreamer',
        'lingkar_perut',
        'tekanan_sistolik',
        'tekanan_diastolik',
        'gds',
        'gdp',
        'kolesterol_lab',
        'trigliserida',
        
        // Skrining Indra
        'pendengaran',
        'penglihatan',
        
        // Skrining Gigi
        'karies',
        'hilang',
        'goyang',
        'status',
        'jumlah_karies',
        
        // Gangguan Fungsional / Barthel Index
        'bab',
        'bak',
        'membersihkan_diri',
        'penggunaan_jamban',
        'makan_minum',
        'berubah_sikap',
        'berpindah',
        'memakai_baju',
        'naik_tangga',
        'mandi',
        'total_skor_barthel',
        'tingkat_ketergantungan',
        
        // Field lain yang mungkin diperlukan
        'umur_tahun',
        'demam',

        // Gejala DM Anak (baru)
        'pernah_dm_oleh_dokter',
        'lama_anak_dm',

        // Riwayat Imunisasi Rutin Balita
        'imunisasi_inti',
        'imunisasi_lanjutan',
        'imunisasi_lanjutan_1',
        'imunisasi_lanjutan_2',
        'imunisasi_lanjutan_3',
        'imunisasi_lanjutan_4',
        'imunisasi_lanjutan_5',
        'imunisasi_lanjutan_6',
        'imunisasi_lanjutan_7',
        'imunisasi_lanjutan_8',
        'imunisasi_lanjutan_9',
        'imunisasi_lanjutan_10',
        'imunisasi_lanjutan_11',
        'imunisasi_lanjutan_12',
        'imunisasi_lanjutan_13',
        'imunisasi_lanjutan_14',
        'imunisasi_lanjutan_15',
        'imunisasi_lanjutan_16',
        'imunisasi_lanjutan_17',
        'imunisasi_lanjutan_18',

        // Skrining pertumbuhan balita
        'posisi_ukur',

        // Skrining telinga & mata (detail)
        'hasil_serumen',
        'hasil_infeksi_telinga',
        'selaput_mata',
        'pupil',
    ];

    /**
     * Field yang perlu dikonversi ke tipe data tertentu
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_skrining' => 'date',
        'umur' => 'integer',
        'lama_merokok' => 'integer',
        'jumlah_rokok' => 'integer',
        'berat_badan' => 'decimal:1',
        'berat_badan_balita' => 'decimal:2',
        'berat_lahir' => 'integer',
        'pjb_tangan_kanan' => 'integer',
        'pjb_kaki' => 'integer',
        'tinggi_badan' => 'decimal:1',
        'lingkar_perut' => 'decimal:1',
        'tekanan_sistolik' => 'integer',
        'tekanan_diastolik' => 'integer',
        'gds' => 'decimal:1',
        'gdp' => 'decimal:1',
        'kolesterol_lab' => 'decimal:1',
        'trigliserida' => 'decimal:1',
    ];
    
    /**
     * Menghitung umur berdasarkan tanggal lahir
     */
    public function hitungUmur()
    {
        if ($this->tanggal_lahir) {
            $tanggalLahir = new \DateTime($this->tanggal_lahir);
            $today = new \DateTime('today');
            $umur = $tanggalLahir->diff($today)->y;
            $this->umur = $umur;
        }
    }
    
    /**
     * Override method save untuk menghitung umur sebelum menyimpan
     */
    public function save(array $options = [])
    {
        $this->hitungUmur();
        
        // Set tanggal skrining jika belum ada
        if (!$this->tanggal_skrining) {
            $this->tanggal_skrining = date('Y-m-d');
        }
        
        return parent::save($options);
    }
}
