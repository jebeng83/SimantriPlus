<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $primaryKey = 'kd_kec';
    public $timestamps = false;

    protected $fillable = [
        'kd_kec',
        'nm_kec',
        'kd_kab'
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kd_kab', 'kd_kab');
    }
} 