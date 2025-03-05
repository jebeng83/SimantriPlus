<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlpDewasa extends Model
{
    use HasFactory;
    
    protected $table = 'ilp_dewasa';
    
    protected $fillable = [
        'no_rawat',
        'no_rkm_medis',
        'no_ktp',
        'data_posyandu',
        'nip',
        'tanggal',
        'tgl_lahir',
        'stts_nikah',
        'jk',
        'no_kk',
        'no_tlp',
        'pekerjaan',
        'riwayat_diri_sendiri',
        'riwayat_keluarga',
        'merokok',
        'konsumsi_tinggi',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'lp',
        'td',
        'gula_darah',
        'metode_mata',
        'hasil_mata',
        'tes_berbisik',
        'gigi',
        'kesehatan_jiwa',
        'tbc',
        'fungsi_hari',
        'status_tt',
        'penyakit_lain_catin',
        'kanker_payudara',
        'iva_test',
        'resiko_jantung',
        'gds',
        'asam_urat',
        'kolesterol',
        'trigliserida',
        'charta',
        'ureum',
        'kreatinin',
        'resiko_kanker_usus',
        'skor_puma',
        'skilas'
    ];
    
    // Relasi dengan tabel reg_periksa
    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
    
    // Relasi dengan tabel pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
    
    // Relasi dengan tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }
}
