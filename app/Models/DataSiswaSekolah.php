<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DataSiswaSekolah extends Model
{
    use HasFactory;

    protected $table = 'data_siswa_sekolah';
    
    protected $fillable = [
        'nis',
        'nisn',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_ibu',
        'id_sekolah',
        'id_kelas',
        'tanggal_masuk',
        'status',
        'status_siswa',
        'no_rkm_medis',
        'nama_ortu',
        'jenis_disabilitas',
        'nik_ortu',
        'no_tlp', // Legacy field - consider for removal
        'no_whatsapp', // Primary phone field - mapped from form field 'no_telepon_ortu'
        'no_telepon_ortu' // Virtual field that maps to no_whatsapp via mutator
    ];

    protected $dates = [
        'tanggal_lahir',
        'tanggal_masuk'
    ];

    /**
     * Relasi dengan sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(DataSekolah::class, 'id_sekolah', 'id_sekolah');
    }

    /**
     * Relasi dengan kelas
     */
    public function kelas()
    {
        return $this->belongsTo(DataKelas::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Accessor untuk sekolah_id (backward compatibility)
     */
    public function getSekolahIdAttribute()
    {
        return $this->id_sekolah;
    }

    /**
     * Accessor untuk kelas_id (backward compatibility)
     */
    public function getKelasIdAttribute()
    {
        return $this->id_kelas;
    }

    /**
     * Relasi dengan skrining siswa SD
     */
    public function skriningSiswaSD()
    {
        return $this->hasMany(SkriningSiswaSD::class, 'siswa_id');
    }

    /**
     * Relasi dengan skrining pkg (jika ada)
     */
    public function skriningPkg()
    {
        return $this->hasMany(SkriningPkg::class, 'siswa_id');
    }

    /**
     * Relasi dengan pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    /**
     * Scope untuk siswa aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_siswa', 'Aktif');
    }

    /**
     * Accessor untuk umur
     */
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    /**
     * Accessor untuk jenis kelamin lengkap
     */
    public function getJenisKelaminLengkapAttribute()
    {
        return $this->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Accessor untuk nomor telepon orang tua (virtual field)
     * Maps to no_whatsapp field in database
     */
    public function getNoTeleponOrtuAttribute()
    {
        return $this->no_whatsapp;
    }

    /**
     * Mutator untuk nomor telepon orang tua (virtual field)
     * Maps to no_whatsapp field in database
     */
    public function setNoTeleponOrtuAttribute($value)
    {
        $this->attributes['no_whatsapp'] = $value;
    }

    /**
     * Accessor untuk tempat tanggal lahir
     */
    public function getTempatTanggalLahirAttribute()
    {
        return $this->tempat_lahir . ', ' . Carbon::parse($this->tanggal_lahir)->format('d-m-Y');
    }
}