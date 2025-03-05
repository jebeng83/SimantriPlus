<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propinsi extends Model
{
    protected $table = 'propinsi';
    protected $primaryKey = 'kd_prop';
    public $timestamps = false;

    protected $fillable = [
        'kd_prop',
        'nm_prop'
    ];
} 