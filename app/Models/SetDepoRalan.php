<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\Poliklinik;
use App\Models\Bangsal;
use App\Models\SetLokasi;

class SetDepoRalan extends Model
{
    protected $table = 'set_depo_ralan';
    protected $primaryKey = ['kd_poli', 'kd_bangsal'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_poli',
        'kd_bangsal'
    ];

    // Override getKeyName untuk composite key
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    // Override getKey untuk composite key
    public function getKey()
    {
        $key = [];
        $keyNames = is_array($this->getKeyName()) ? $this->getKeyName() : [$this->getKeyName()];
        foreach ($keyNames as $keyName) {
            $key[$keyName] = $this->getAttribute($keyName);
        }
        return $key;
    }

    // Override setKeysForSaveQuery untuk composite key
    protected function setKeysForSaveQuery($query)
    {
        $keyNames = is_array($this->getKeyName()) ? $this->getKeyName() : [$this->getKeyName()];
        foreach ($keyNames as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }
        return $query;
    }

    // Relasi ke poliklinik
    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    // Relasi ke bangsal (jika ada model Bangsal)
    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    // Scope untuk filter berdasarkan poli
    public function scopeByPoli($query, $kdPoli)
    {
        return $query->where('kd_poli', $kdPoli);
    }

    /**
     * Get bangsal codes by poli code
     * If no bangsal found in set_depo_ralan, fallback to set_lokasi
     */
    public static function getBangsalByPoli(string $kdPoli)
    {
        $bangsalCodes = self::where('kd_poli', $kdPoli)
            ->pluck('kd_bangsal')
            ->toArray();
        
        // If no bangsal found in set_depo_ralan, fallback to set_lokasi
        if (empty($bangsalCodes)) {
            $bangsalCodes = SetLokasi::getAllBangsal();
        }
        
        return $bangsalCodes;
    }

    /**
     * Get first bangsal code by poli code
     * If no bangsal found in set_depo_ralan, fallback to set_lokasi
     */
    public static function getFirstBangsalByPoli(string $kdPoli)
    {
        $bangsalCode = self::where('kd_poli', $kdPoli)
            ->value('kd_bangsal');
        
        // If no bangsal found in set_depo_ralan, fallback to set_lokasi
        if (!$bangsalCode) {
            $bangsalCode = SetLokasi::getFirstBangsal();
        }
        
        return $bangsalCode;
    }
}