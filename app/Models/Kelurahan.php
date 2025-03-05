<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = 'kelurahan';
    protected $primaryKey = 'kd_kel';
    public $timestamps = false;

    protected $fillable = [
        'kd_kel',
        'nm_kel',
        'kd_kec'
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kec', 'kd_kec');
    }
} 