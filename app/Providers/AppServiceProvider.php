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
        // Menambahkan CSS premium secara global
        \Illuminate\Support\Facades\View::composer('adminlte::page', function ($view) {
            $view->with('adminlte_css', 'layouts.global-styles');
        });
        
        // Kode boot yang sudah ada
    }
}
