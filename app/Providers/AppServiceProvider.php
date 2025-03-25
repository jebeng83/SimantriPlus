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
        
        // Register Livewire Components
        if (class_exists('Livewire')) {
            // Register components hanya sekali
            if (!app()->bound('livewire.ralan.pemeriksaan-anc')) {
                \Livewire::component('ralan.pemeriksaan-anc', \App\Http\Livewire\Ralan\PemeriksaanANC::class);
            }
        }
        
        // Handle session timeout
        if (auth()->check()) {
            $lastActivity = session('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            
            if ($lastActivity && time() - $lastActivity > $timeout) {
                auth()->logout();
                session()->flush();
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            
            session(['last_activity' => time()]);
        }
    }
}
