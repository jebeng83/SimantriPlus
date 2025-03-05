<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SukuBangsa extends Model
{
    protected $table = 'suku_bangsa';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama_suku_bangsa'
    ];
} 