<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class UrlHelper
{
    /**
     * Enkripsi parameter URL
     *
     * @param string $value
     * @return string
     */
    public static function encrypt($value)
    {
        return urlencode(base64_encode($value));
    }

    /**
     * Dekripsi parameter URL
     *
     * @param string $value
     * @return string|null
     */
    public static function decrypt($value)
    {
        try {
            return base64_decode(urldecode($value));
        } catch (\Exception $e) {
            Log::error('Gagal mendekripsi parameter URL: ' . $e->getMessage());
            return null;
        }
    }
}