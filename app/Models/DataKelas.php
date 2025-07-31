<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKelas extends Model
{
    use HasFactory;

    protected $table = 'data_kelas';
    protected $primaryKey = 'id_kelas';
    
    protected $fillable = [
        'sekolah_id',
        'kelas',
        'tingkat',
        'wali_kelas',
        'jumlah_siswa',
        'status'
    ];

    /**
     * Relasi dengan sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'sekolah_id', 'id_sekolah');
    }

    /**
     * Relasi dengan siswa
     */
    public function siswa()
    {
        return $this->hasMany(DataSiswaSekolah::class, 'id_kelas', 'id_kelas');
    }



    /**
     * Accessor untuk nama lengkap kelas
     */
    public function getNamaLengkapAttribute()
    {
        return $this->kelas;
    }


}