<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $table = 'data_posyandu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_posyandu',
        'alamat',
        'keterangan'
    ];
} 