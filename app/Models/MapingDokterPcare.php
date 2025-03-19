<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapingDokterPcare extends Model
{
    protected $table = 'maping_dokter_pcare';
    public $timestamps = false;
    protected $primaryKey = 'kd_dokter';
    protected $keyType = 'string';
    
    // Relasi ke dokter jika diperlukan nantinya
    // public function dokter()
    // {
    //     return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    // }
} 