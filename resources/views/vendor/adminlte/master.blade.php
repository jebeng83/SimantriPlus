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
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

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

    {{-- Vite React Refresh (React HMR preamble) - Only when dev server is running (hot file present) --}}
    @php
        $hotPath = public_path('hot');
        $hotUrl = file_exists($hotPath) ? trim(file_get_contents($hotPath)) : null;
    @endphp
    @if(app()->environment('local', 'development') && !empty($hotUrl))
        @viteReactRefresh
        <script type="module">
            try {
                const devBase = {{ json_encode($hotUrl) }};
                await import(devBase + '/@@vite/client');
                await import(devBase + '/@react-refresh');
                window.__vite_plugin_react_preamble_installed__ = true;
                window.$RefreshReg$ = window.$RefreshReg$ || (() => {});
                window.$RefreshSig$ = window.$RefreshSig$ || (() => (type) => type);
                console.debug('[Blade] Injected Vite React refresh preamble manually from', devBase);
            } catch (e) {
                console.warn('Failed to inject Vite React refresh preamble', e);
            }
        </script>
    @endif

    {{-- Vite React & Tailwind --}}
    @vite(['resources/js/app.jsx'])

    {{-- Removing conflicting favicon settings --}}
    {{-- @laravelPWA --}}
</head>

<body class="@yield('classes_body') sidebar-mini dark-sidebar premium-route" @yield('body_data') data-route="{{ Request::path() }}">

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
    {{-- Nonaktifkan include bundle legacy Mix (public/js/app.js) untuk mencegah konflik dengan Vite
         Jika diperlukan, aktifkan kembali dengan env khusus atau setelah validasi. --}}
    {{-- @if(app()->environment('production'))
    <script src="{{ asset('js/app.js') }}"></script>
    @endif --}}
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
    @if(app()->environment('production'))
    <script>
        // Production: daftarkan Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/serviceworker.js')
                .then(function(registration) {
                    console.log('Service worker (production) scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Pendaftaran Service Worker gagal:', error);
                });
        }
    </script>
    @else
    <script>
        // Development: pastikan tidak ada Service Worker aktif dan bersihkan cache untuk menghindari konflik HMR
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for (let registration of registrations) {
                    registration.unregister();
                }
            });
        }
        if (window.caches && typeof window.caches.keys === 'function') {
            caches.keys().then(function(keys) { keys.forEach(function(key) { caches.delete(key); }); });
        }
    </script>
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    {{-- React Root --}}
    <div id="react-root" style="position: fixed; bottom: 16px; right: 16px; z-index: 9999;"></div>
</body>

</html>
