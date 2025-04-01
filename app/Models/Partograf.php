<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partograf extends Model
{
    use HasFactory;

    protected $table = 'partograf';
    protected $primaryKey = 'id_partograf';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id_partograf',
        'no_rawat',
        'no_rkm_medis',
        'id_hamil',
        'tanggal_partograf',
        'diperiksa_oleh',
        
        // Bagian 1: Informasi Persalinan Awal
        'paritas',
        'onset_persalinan',
        'waktu_pecah_ketuban',
        'faktor_risiko',
        
        // Bagian 2: Supportive Care
        'pendamping', 
        'mobilitas',
        'manajemen_nyeri',
        'intake_cairan',
        
        // Bagian 3: Informasi Janin
        'denyut_jantung_janin',
        'kondisi_cairan_ketuban',
        'presentasi_janin',
        'bentuk_kepala_janin',
        'caput_succedaneum',
        
        // Bagian 4: Informasi Ibu
        'nadi',
        'tekanan_darah_sistole',
        'tekanan_darah_diastole',
        'suhu',
        'urine_output',
        
        // Bagian 5: Proses Persalinan
        'frekuensi_kontraksi',
        'durasi_kontraksi',
        'dilatasi_serviks',
        'penurunan_posisi_janin',
        
        // Bagian 6: Pengobatan
        'obat_dan_dosis',
        'cairan_infus',
        
        // Bagian 7: Perencanaan
        'tindakan_yang_direncanakan',
        'hasil_tindakan',
        'keputusan_bersama',
        
        // Data grafik
        'grafik_kemajuan_persalinan_json',
    ];
    
    protected $casts = [
        'faktor_risiko' => 'array',
        'grafik_kemajuan_persalinan_json' => 'array',
        'tanggal_partograf' => 'datetime',
        'waktu_pecah_ketuban' => 'datetime',
    ];
    
    public function ibuHamil()
    {
        return $this->belongsTo(IbuHamil::class, 'id_hamil', 'id_hamil');
    }
} 