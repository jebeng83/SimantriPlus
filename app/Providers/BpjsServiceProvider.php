<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BpjsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('pcare_conf', function ($app) {
            return [
                'cons_id' => env('BPJS_PCARE_CONSID'),
                'secret_key' => env('BPJS_PCARE_SCREET_KEY'),
                'username' => env('BPJS_PCARE_USERNAME'),
                'password' => env('BPJS_PCARE_PASSWORD'),
                'app_code' => env('BPJS_PCARE_APP_CODE'),
                'base_url' => env('BPJS_PCARE_BASE_URL'),
                'service_name' => env('BPJS_PCARE_SERVICE_NAME'),
            ];
        });
    }
}