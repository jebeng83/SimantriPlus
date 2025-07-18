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
        'rencana_menikah',
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