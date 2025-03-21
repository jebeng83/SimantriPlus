<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000" />

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    {{-- Configured Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    {{-- Premium AdminLTE style for consistent look --}}
    <link rel="stylesheet" href="{{ asset('css/adminlte-premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/uniform-layout.css') }}">

    {{-- Script untuk menghapus tombol Debug --}}
    <script>
        (function() {
        // Fungsi untuk menghapus tombol Debug
        function removeDebugButton() {
            var debugButton = document.getElementById('btn-toggle-debug');
            if (debugButton) debugButton.remove();
            
            // Cari semua elemen dengan atribut ID atau kelas yang mengandung kata 'debug'
            var debugElements = document.querySelectorAll('[id*="debug"],[class*="debug"]');
            for (var i = 0; i < debugElements.length; i++) {
                debugElements[i].style.display = 'none';
            }
        }
        
        // Jalankan segera
        removeDebugButton();
        
        // Jalankan ketika DOM sudah siap
        document.addEventListener('DOMContentLoaded', removeDebugButton);
        
        // Jalankan secara periodik
        setInterval(removeDebugButton, 500);
    })();
    </script>

    @if(config('adminlte.google_fonts.allowed', true))
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    @endif
    @else
    <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
    @if(app()->version() >= 7)
    <livewire:styles />
    @else
    <livewire:styles />
    @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Removing conflicting favicon settings --}}
    {{-- @laravelPWA --}}
</head>

<body class="@yield('classes_body') dark-sidebar premium-route" @yield('body_data') data-route="{{ Request::path() }}">

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.1/dist/cdn.min.js"></script>

    {{-- Configured Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    {{-- Navigation Handler Script --}}
    <script src="{{ asset('js/navigation-handler.js') }}"></script>
    @else
    <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
    @if(app()->version() >= 7)
    <livewire:scripts />
    @else
    <livewire:scripts />
    @endif
    @endif
    <x-livewire-alert::scripts />

    <!-- Service Worker Script -->
    <script>
        // Hanya mendaftarkan service worker jika protokol HTTPS atau localhost
        if (('https:' === window.location.protocol || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') && 'serviceWorker' in navigator) {
            // Gunakan hanya satu service worker (dari LaravelPWA)
            navigator.serviceWorker.register('/serviceworker.js')
                .then(function(registration) {
                    console.log('Service worker berhasil didaftarkan dengan scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Pendaftaran Service Worker gagal:', error);
                });
        }
    </script>

    {{-- Custom Scripts --}}
    @yield('adminlte_js')
</body>

</html>