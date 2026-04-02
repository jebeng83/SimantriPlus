<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Sistem Skrining Kesehatan">
   <meta name="theme-color" content="#2e5cb8">
   <meta name="mobile-web-app-capable" content="yes">
   <meta name="apple-mobile-web-app-capable" content="yes">
   <meta name="apple-mobile-web-app-status-bar-style" content="default">
   <meta name="apple-mobile-web-app-title" content="SkriningCKG">

   <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
   <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
   <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
   <link rel="manifest" href="{{ asset('manifest.json') }}">
   <title>@yield('title')</title>

   <!-- Google Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

   <!-- SweetAlert2 -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">

   <!-- Base CSS -->
   <style>
      :root {
         --primary: #2e5cb8;
         --primary-dark: #1a3c7e;
         --secondary: #6c757d;
         --success: #28a745;
         --info: #17a2b8;
         --warning: #ffc107;
         --danger: #dc3545;
         --light: #f8f9fa;
         --dark: #343a40;
         --body-bg: #f8f9fc;
         --body-color: #3a3a3a;
         --border-color: #eaecf0;
         --shadow-sm: 0 .125rem .25rem rgba(0, 0, 0, .075);
         --shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
         --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, .175);
         --font-sans-serif: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
         --transition: all 0.2s ease-in-out;
      }

      html {
         scroll-behavior: smooth;
      }

      body {
         font-family: var(--font-sans-serif);
         background-color: var(--body-bg);
         color: var(--body-color);
         line-height: 1.6;
         min-height: 100vh;
         display: flex;
         flex-direction: column;
      }

      .container-fluid {
         max-width: 1400px;
         flex: 1;
         padding-top: 1.5rem;
         padding-bottom: 1.5rem;
      }

      /* Custom Scrollbar */
      ::-webkit-scrollbar {
         width: 8px;
         height: 8px;
      }

      ::-webkit-scrollbar-track {
         background: #f1f1f1;
      }

      ::-webkit-scrollbar-thumb {
         background: #c1c1c1;
         border-radius: 4px;
      }

      ::-webkit-scrollbar-thumb:hover {
         background: #a8a8a8;
      }
   </style>

   <!-- Custom CSS -->
   @yield('css')
</head>

<body>
   <!-- Header Simple -->
   <header class="bg-white shadow-sm py-2 d-none d-print-none">
      <div class="container-fluid">
         <div class="d-flex justify-content-between align-items-center">
            <div>
               <img src="{{ asset('img/logo.png') }}" alt="Logo" height="40" class="mr-2">
               <span class="font-weight-bold text-primary">e-Dokter</span>
            </div>
            <div>
               <span class="text-muted small">Sistem Skrining Kesehatan</span>
            </div>
         </div>
      </div>
   </header>

   <!-- Main Content -->
   <div class="container-fluid py-4">
      @yield('content')
   </div>

   <!-- Footer -->
   <footer class="bg-white text-muted py-3 shadow-sm d-none d-print-none">
      <div class="container-fluid">
         <div class="text-center">
            <small>&copy; {{ date('Y') }} e-Dokter. All rights reserved.</small>
         </div>
      </div>
   </footer>

   <!-- jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
      if (typeof window.jQuery === 'undefined') {
         var s = document.createElement('script');
         s.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js';
         document.head.appendChild(s);
      }
   </script>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>

   <!-- Custom JS for every page -->
   <script>
      if (typeof window.jQuery !== 'undefined') {
         $(function () {
            $('[data-toggle="tooltip"]').tooltip();
         });
      }
      const Toast = Swal.mixin({
         toast: true,
         position: 'top-end',
         showConfirmButton: false,
         timer: 3000,
         timerProgressBar: true,
         didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
         }
      });
   </script>

   <script>
      // Registrasi service worker agar halaman minimal bisa di-install sebagai PWA di Android.
      if ('serviceWorker' in navigator) {
         window.addEventListener('load', function () {
            var swCandidates = ['/serviceworker.js', '/sw.js'];
            var registerSW = function (index) {
               if (index >= swCandidates.length) return;
               navigator.serviceWorker.register(swCandidates[index], {
                  scope: '/'
               }).catch(function () {
                  registerSW(index + 1);
               });
            };
            registerSW(0);
         });
      }
   </script>

   <!-- Custom JS -->
   @yield('js')
</body>

</html>
