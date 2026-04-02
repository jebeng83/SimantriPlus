<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkriningCkg extends Model
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
     * Kolom yang dapat diisi (fillable)
     */
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'tanggal_lahir',
        'umur',
        'jenis_kelamin',
        'no_handphone',
        'no_rkm_medis',
        'tanggal_skrining',
        
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
        'durasi_olahraga', // Legacy field
        'frekuensi_olahraga_1',
        'frekuensi_olahraga_2',
        
        'aktivitas_fisik_2', 'aktivitas_fisik_2_hari', 'aktivitas_fisik_2_menit',
        'aktivitas_fisik_3', 'aktivitas_fisik_3_hari', 'aktivitas_fisik_3_menit',
        'aktivitas_fisik_4', 'aktivitas_fisik_4_hari', 'aktivitas_fisik_4_menit',
        'aktivitas_fisik_5', 'aktivitas_fisik_5_hari', 'aktivitas_fisik_5_menit',
        'aktivitas_fisik_6', 'aktivitas_fisik_6_hari', 'aktivitas_fisik_6_menit',
        
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
        
        // Kanker Usus/Kolorektal
        'kanker_usus_1',
        'kanker_usus_2',
        
        // Faktor Resiko TB
        'faktor_resiko_tb',
        
        // Penapisan Resiko Kanker Paru
        'kanker_paru_1',
        'kanker_paru_2',
        'kanker_paru_3',
        'kanker_paru_4',
        
        // Tuberkulosis
        'riwayat_tbc',
        'jenis_tbc',
        'riwayat_merokok',
        'napas_pendek',
        'dahak',
        'batuk',
        'spirometri',
        
        // Antropometri dan Laboratorium
        'riwayat_dm',
        'riwayat_ht',
        'tinggi_badan',
        'berat_badan',
        'lingkar_perut',
        'tekanan_sistolik',
        'tekanan_diastolik',
        'tekanan_sistolik_2',
        'tekanan_diastolik_2',
        'gds',
        'gdp',
        'kolesterol_lab',
        'trigliserida',
        
        // Skrining Indra
        'hasil_serumen',
        'hasil_infeksi_telinga',
        'pendengaran',
        'penglihatan',
        'pupil',
        
        // Skrining Gigi
        'karies',
        'hilang',
        'goyang',
        'status',
        
        // Penyakit Tropis
        'frambusia',
        'kusta',
        'skabies',
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
