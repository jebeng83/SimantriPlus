<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSekolah extends Model
{
    use HasFactory;

    protected $table = 'data_sekolah';
    protected $primaryKey = 'id_sekolah';
    
    protected $fillable = [
        'nama_sekolah',
        'id_jenis_sekolah',
        'kd_kel'
    ];

    /**
     * Relasi dengan jenis sekolah
     */
    public function jenisSekolah()
    {
        return $this->belongsTo(JenisSekolah::class, 'id_jenis_sekolah');
    }

    /**
     * Relasi dengan kelas
     */
    public function dataKelas()
    {
        return $this->hasMany(DataKelas::class, 'kelas', 'id_sekolah');
    }

    /**
     * Relasi dengan siswa
     */
    public function siswa()
    {
        return $this->hasMany(DataSiswaSekolah::class, 'id_sekolah', 'id_sekolah');
    }

    /**
     * Relasi dengan kelurahan
     */
    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kd_kel', 'kd_kel');
    }
}