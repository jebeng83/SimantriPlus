<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataIbuHamilDummy extends Model
{
    use HasFactory;
    
    protected $table = 'data_ibu_hamil';
    protected $primaryKey = 'id_hamil';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public static function testConnection()
    {
        // Periksa apakah ada data di tabel
        $count = self::count();
        
        // Periksa sequence counter
        $sequenceCount = DB::table('data_ibu_hamil_sequence')->count();
        
        // Hasil
        return [
            'data_count' => $count,
            'sequence_exists' => $sequenceCount > 0,
            'columns' => Schema::getColumnListing('data_ibu_hamil')
        ];
    }
}
