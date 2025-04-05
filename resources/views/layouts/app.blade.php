<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="theme-color" content="#2e5cb8" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @include('layouts.global-styles')

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>

    @livewireStyles

    @stack('styles')

    <style>
        /* Alpine.js transitions */
        [x-cloak] {
            display: none !important;
        }

        .transition {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global styles */
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #3a3a3a;
            background-color: #f8f9fc;
        }

        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #0f7386 100%);
        }

        /* Card styling */
        .card {
            border-radius: 8px;
            overflow: hidden;
            border: none;
        }

        .card-header {
            border-bottom: 0;
            padding: 15px 20px;
        }

        /* Button styles */
        .btn {
            border-radius: 4px;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
            border: none;
        }

        /* Shadow effects */
        .shadow-lg {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
    </style>

    <!-- Page Specific CSS -->
    @yield('css')

    <script>
        // Fungsi untuk memperbarui CSRF token secara berkala
        function refreshCsrfToken() {
            fetch('/refresh-csrf')
                .then(response => response.text())
                .then(token => {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
                    console.log('CSRF token diperbarui');
                })
                .catch(error => console.error('Gagal memperbarui CSRF token:', error));
        }
        
        // Perbarui CSRF token setiap 5 menit
        setInterval(refreshCsrfToken, 300000);
    </script>

    @if(!isset($disableToastr) || !$disableToastr)
    <!-- Toastr CSS for Notifications -->
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    @endif

    @if(!isset($disableSweetAlert) || !$disableSweetAlert)
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
    @endif
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-gradient-primary shadow-lg">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">
                                <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-stethoscope mr-1"></i> Pelayanan
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ url('/ralan') }}">
                                    <i class="fas fa-stethoscope mr-1"></i> Rawat Jalan
                                </a>
                                <a class="dropdown-item" href="{{ url('/ranap') }}">
                                    <i class="fas fa-procedures mr-1"></i> Rawat Inap
                                </a>
                                <a class="dropdown-item" href="{{ route('kyc.index') }}">
                                    <i class="fas fa-id-card mr-1"></i> KYC SATUSEHAT
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/data-pasien') }}">
                                <i class="fas fa-user-injured mr-1"></i> Data Pasien
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">{{ __('Home') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <!-- Tombol register dihapus -->
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ session('username') ?? 'User' }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-1"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- CSRF Token for AJAX -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Page Specific JS -->
    @yield('js')

    <script>
        // Registrasi Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/serviceworker.js', {
                    scope: '/',
                    updateViaCache: 'none'
                }).then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        // Konfigurasi untuk memastikan grafik dimuat dengan benar
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan semua URL grafik menggunakan HTTPS
            const chartScripts = document.querySelectorAll('script[src*="chart"]');
            chartScripts.forEach(script => {
                if (script.src.startsWith('http://')) {
                    script.src = script.src.replace('http://', 'https://');
                }
            });
        });
    </script>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah Anda yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Keluar" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}">Keluar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menangani error service worker
    window.addEventListener('error', function(event) {
        if (event.message && (event.message.includes('Failed to fetch') || event.message.includes('Service Worker'))) {
            console.log('Service worker error ditangani');
            // Opsional: unregister service worker untuk menghindari masalah
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for (let registration of registrations) {
                        registration.unregister();
                    }
                });
            }
        }
    });

    // Menangani error CORS
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        if (jqxhr.status === 0 || jqxhr.status === 503) {
            console.log('CORS atau koneksi error ditangani pada: ' + settings.url);
        }
    });
    </script>

    @if(!isset($disableToastr) || !$disableToastr)
    <!-- Toastr JS for Notifications -->
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
    <script>
        // Initialize toastr settings
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    @endif

    @if(!isset($disableSweetAlert) || !$disableSweetAlert)
    <!-- SweetAlert2 JS -->
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.min.js') }}"></script>
    @endif
</body>

</html>