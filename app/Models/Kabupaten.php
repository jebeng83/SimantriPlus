<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    protected $primaryKey = 'kd_kab';
    public $timestamps = false;

    protected $fillable = [
        'kd_kab',
        'nm_kab',
        'kd_prop'
    ];

    public function propinsi()
    {
        return $this->belongsTo(Propinsi::class, 'kd_prop', 'kd_prop');
    }
} 