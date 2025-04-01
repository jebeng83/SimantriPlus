<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbuHamil extends Model
{
    use HasFactory;

    protected $table = 'data_ibu_hamil';
    protected $primaryKey = 'id_hamil';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'no_rkm_medis',
        'nama',
        'usia',
        'usia_kehamilan',
        'tanggal_lahir',
        'alamat',
        'status_kehamilan',
        'HPHT',
        'HPL',
        'catatan',
        'created_by',
        'updated_by'
    ];
    
    protected $dates = [
        'tanggal_lahir',
        'HPHT',
        'HPL',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'usia' => 'integer',
        'usia_kehamilan' => 'integer',
        'tanggal_lahir' => 'date',
        'HPHT' => 'date',
        'HPL' => 'date'
    ];
    
    public function partograf()
    {
        return $this->hasMany(Partograf::class, 'id_hamil', 'id_hamil');
    }
    
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
} 