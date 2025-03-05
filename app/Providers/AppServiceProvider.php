<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\AdminLTELayoutHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Mendaftarkan custom LayoutHelper
        $this->app->bind('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper', function ($app) {
            return new AdminLTELayoutHelper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
