<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerusahaanPasien extends Model
{
    protected $table = 'perusahaan_pasien';
    protected $primaryKey = 'kode_perusahaan';
    public $timestamps = false;

    protected $fillable = [
        'kode_perusahaan',
        'nama_perusahaan',
        'alamat',
        'kota',
        'no_telp'
    ];
} 