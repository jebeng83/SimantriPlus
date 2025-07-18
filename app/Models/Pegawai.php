<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'pegawai';

    /**
     * Primary key yang digunakan
     */
    protected $primaryKey = 'nik';

    /**
     * Mengindikasikan bahwa primary key bukan auto increment
     */
    public $incrementing = false;

    /**
     * Tipe data primary key
     */
    protected $keyType = 'string';

    /**
     * Mengindikasikan bahwa model tidak menggunakan timestamps
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi (fillable)
     */
    protected $fillable = [
        'nik',
        'nama',
        'jbtn',
        'jenis_kelamin',
        'tmp_lahir',
        'tgl_lahir',
        'gol_darah',
        'agama',
        'stts_nikah',
        'alamat',
        'kd_jbtn',
        'no_telp',
        'stts_aktif',
    ];

    /**
     * Scope untuk pegawai aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('stts_aktif', 'Aktif');
    }

    /**
     * Relasi dengan skrining pkg sebagai petugas entry
     */
    public function skriningPkgAsEntry()
    {
        return $this->hasMany('App\Models\SkriningPkg', 'id_petugas_entri', 'nik');
    }
}