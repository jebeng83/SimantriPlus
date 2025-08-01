<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';
    protected $primaryKey = 'no_rkm_medis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rkm_medis',
        'no_ktp',
        'nm_pasien',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'alamat'
    ];

    // Relasi dengan tabel data_ibu_hamil
    public function dataIbuHamil()
    {
        return $this->hasMany(DataIbuHamil::class, 'no_rkm_medis', 'no_rkm_medis');
    }
}
