<?php

namespace App\Models\ANC;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pasien;

class DataBalitaSakit extends Model
{
    use HasFactory;

    protected $table = 'data_balita_sakit';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'suhu_tubuh' => 'float',
        'lingkar_kepala' => 'float',
        'lingkar_lengan' => 'float'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rm', 'no_rm');
    }
} 