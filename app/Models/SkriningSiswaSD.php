<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkriningSiswaSD extends Model
{
    use HasFactory;

    protected $table = 'skrining_siswa_sd';
    
    protected $fillable = [
        'siswa_id',
        'tanggal_skrining',
        'petugas_skrining',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'status_gizi',
        'tekanan_darah',
        'denyut_nadi',
        'suhu_tubuh',
        'visus_od',
        'visus_os',
        'kelainan_mata',
        'pendengaran_kanan',
        'pendengaran_kiri',
        'kelainan_telinga',
        'gigi_karies',
        'gigi_hilang',
        'kelainan_gigi',
        'riwayat_penyakit',
        'riwayat_alergi',
        'obat_dikonsumsi',
        'status_imunisasi',
        'kesimpulan',
        'tindak_lanjut',
        'status_skrining'
    ];

    protected $dates = [
        'tanggal_skrining'
    ];

    protected $casts = [
        'status_imunisasi' => 'array',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'imt' => 'decimal:2',
        'suhu_tubuh' => 'decimal:1'
    ];

    /**
     * Relasi dengan siswa
     */
    public function siswa()
    {
        return $this->belongsTo(DataSiswaSekolah::class, 'siswa_id');
    }

    /**
     * Scope untuk skrining normal
     */
    public function scopeNormal($query)
    {
        return $query->where('status_skrining', 'Normal');
    }

    /**
     * Scope untuk skrining perlu perhatian
     */
    public function scopePerluPerhatian($query)
    {
        return $query->where('status_skrining', 'Perlu Perhatian');
    }

    /**
     * Scope untuk skrining rujuk
     */
    public function scopeRujuk($query)
    {
        return $query->where('status_skrining', 'Rujuk');
    }

    /**
     * Accessor untuk status gizi warna
     */
    public function getStatusGiziWarnaAttribute()
    {
        switch ($this->status_gizi) {
            case 'Normal':
                return 'success';
            case 'Kurang':
            case 'Kurus':
                return 'warning';
            case 'Obesitas':
            case 'Sangat Kurus':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Accessor untuk status skrining warna
     */
    public function getStatusSkriningWarnaAttribute()
    {
        switch ($this->status_skrining) {
            case 'Normal':
                return 'success';
            case 'Perlu Perhatian':
                return 'warning';
            case 'Rujuk':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}