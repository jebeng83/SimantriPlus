<?php

namespace App\Models\ANC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pasien;

class DataRematri extends Model
{
    use HasFactory;

    protected $table = 'data_rematri';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'lingkar_lengan' => 'float',
        'hemoglobin' => 'float',
        'pemberian_tablet_tambah_darah' => 'boolean',
        'konseling_gizi' => 'boolean'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }
} 