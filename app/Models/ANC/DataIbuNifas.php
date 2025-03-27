<?php

namespace App\Models\ANC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pasien;

class DataIbuNifas extends Model
{
    use HasFactory;

    protected $table = 'data_ibu_nifas';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'tanggal_persalinan' => 'date',
        'berat_bayi' => 'float',
        'tinggi_bayi' => 'float',
        'asi_eksklusif' => 'boolean',
        'kb_pasca_salin' => 'boolean'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }
} 