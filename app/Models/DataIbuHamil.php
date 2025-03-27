<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataIbuHamil extends Model
{
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
        'berat_badan_sebelum_hamil' => 'double',
        'tinggi_badan' => 'double',
        'lila' => 'double',
        'imt_sebelum_hamil' => 'double'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Gunakan tabel sequence untuk mendapatkan nomor urut berikutnya
            $sequenceRow = DB::table('data_ibu_hamil_sequence')->first();
            $nextNumber = $sequenceRow ? $sequenceRow->last_number + 1 : 1;
            
            // Format ID: H + 6 digit nomor (misal: H000001)
            $model->id_hamil = 'H' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            // Update nilai sequence
            if ($sequenceRow) {
                DB::table('data_ibu_hamil_sequence')
                    ->where('id', $sequenceRow->id)
                    ->update(['last_number' => $nextNumber]);
            } else {
                DB::table('data_ibu_hamil_sequence')->insert([
                    'last_number' => $nextNumber
                ]);
            }
        });
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
} 