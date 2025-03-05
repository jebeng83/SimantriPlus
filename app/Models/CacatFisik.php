<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacatFisik extends Model
{
    protected $table = 'cacat_fisik';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama_cacat'
    ];
} 