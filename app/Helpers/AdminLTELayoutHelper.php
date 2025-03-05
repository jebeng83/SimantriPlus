<?php

namespace App\Helpers;

use JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper as BaseLayoutHelper;

class AdminLTELayoutHelper extends BaseLayoutHelper
{
    /**
     * Check if the preloader animation is enabled.
     *
     * @return bool
     */
    public static function isPreloaderEnabled()
    {
        return config('adminlte.preloader.enabled', false);
    }
} 