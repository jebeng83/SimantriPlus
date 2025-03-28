<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataIbuHamil extends Model
{
    use HasFactory;

    protected $table = 'data_ibu_hamil';
    protected $primaryKey = 'id_hamil';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_hamil',
        'nik',
        'no_rkm_medis',
        'kehamilan_ke',
        'tgl_lahir',
        'nomor_kk',
        'nama',
        'berat_badan_sebelum_hamil',
        'tinggi_badan',
        'lila',
        'imt_sebelum_hamil',
        'status_gizi',
        'jumlah_janin',
        'usia_ibu',
        'jumlah_anak_hidup',
        'riwayat_keguguran',
        'jarak_kehamilan_tahun',
        'jarak_kehamilan_bulan',
        'hari_pertama_haid',
        'hari_perkiraan_lahir',
        'golongan_darah',
        'rhesus',
        'riwayat_penyakit',
        'riwayat_alergi',
        'kepemilikan_buku_kia',
        'jaminan_kesehatan',
        'no_jaminan_kesehatan',
        'faskes_tk1',
        'faskes_rujukan',
        'pendidikan',
        'pekerjaan',
        'status',
        'nama_suami',
        'nik_suami',
        'telp_suami',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'puskesmas',
        'desa',
        'data_posyandu',
        'alamat_lengkap',
        'rt',
        'rw'
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'hari_pertama_haid' => 'date',
        'hari_perkiraan_lahir' => 'date',
        'kepemilikan_buku_kia' => 'boolean',
        'berat_badan_sebelum_hamil' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'lila' => 'decimal:2',
        'imt_sebelum_hamil' => 'decimal:2',
        'provinsi' => 'integer',
        'kabupaten' => 'integer',
        'kecamatan' => 'integer',
        'desa' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_hamil) {
                $lastId = static::orderBy('id_hamil', 'desc')->value('id_hamil');
                
                if ($lastId) {
                    $numericPart = (int)substr($lastId, 3);
                    $newNumericPart = $numericPart + 1;
                } else {
                    $newNumericPart = 1;
                }
                
                $model->id_hamil = 'IH-' . str_pad($newNumericPart, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
} 