<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';
    
    protected $fillable = [
        'nama_instansi',
        'alamat_instansi', 
        'kabupaten',
        'propinsi',
        'kontak',
        'email',
        'aktifkan',
        'kode_ppk',
        'kode_ppkinhealth',
        'kode_ppkemkemkes',
        'wallpaper',
        'logo'
    ];

    /**
     * Get setting value by field name
     *
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    public static function get($field, $default = null)
    {
        return Cache::remember("setting_{$field}", 3600, function () use ($field, $default) {
            $setting = self::first();
            return $setting && isset($setting->{$field}) ? $setting->{$field} : $default;
        });
    }

    /**
     * Set setting value
     *
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public static function set($field, $value)
    {
        $setting = self::first();
        if (!$setting) {
            $setting = new self();
        }
        
        $setting->{$field} = $value;
        $result = $setting->save();
        
        // Clear cache
        Cache::forget("setting_{$field}");
        
        return $result;
    }

    /**
     * Get all hospital settings
     *
     * @return array
     */
    public static function getHospitalInfo()
    {
        return [
            'name' => self::get('nama_instansi', 'RSUD GUNUNG TUA'),
            'address' => self::get('alamat_instansi', 'Jl. Raya Gunung Tua, Kabupaten Padang Lawas Utara, Sumatera Utara'),
            'phone' => self::get('kontak', '(0634) 123456'),
            'email' => self::get('email', 'info@rsudgunungua.go.id'),
            'kabupaten' => self::get('kabupaten', 'Padang Lawas Utara'),
            'propinsi' => self::get('propinsi', 'Sumatera Utara')
        ];
    }
}