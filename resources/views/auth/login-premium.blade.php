<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Login - Simantri PLUS</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <style>
      :root {
         --primary-color: #233292;
         --secondary-color: #1a2570;
         --accent-color: #4f5bda;
         --text-color: #ffffff;
         --text-dark: #333333;
         --card-bg: rgba(255, 255, 255, 0.95);
         --input-bg: #f8f9fa;
         --input-border: #e1e5eb;
         --input-focus: #d0d6e6;
         --btn-primary: #233292;
         --btn-hover: #1a2570;
         --error-color: #dc3545;
      }

      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }

      body {
         font-family: 'Poppins', sans-serif;
         height: 100vh;
         background-color: #f5f7fa;
         overflow: hidden;
         position: relative;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      /* Pastikan kontainer React/fallback selalu berada di tengah layar dan cukup ruang */
      #login-premium-react-root {
         position: relative;
         min-height: 100vh;
         width: 100%;
         display: flex;
         align-items: center;
         justify-content: center;
         padding: 16px;
      }

      .bg-wallpaper {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background-image: url('{{ $wallpaperUrl }}');
         background-size: cover;
         background-position: center center;
         background-repeat: no-repeat;
         z-index: -1;
      }

      .bg-pattern {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDYwIDYwIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIG9wYWNpdHk9Ii4yIj48ZyBmaWxsPSIjZmZmIiBmaWxsLXJ1bGU9Im5vbnplcm8iPjxwYXRoIGQ9Ik0zOS4yNSAxLjNsMi4xIDIuMS0yLjEgMi4xLTIuMS0yLjF6TTU2Ljk1IDE5LjA1bDIuMSAyLjEtMi4xIDIuMS0yLjEtMi4xek00OS4zNSAxMS40bDIuMSAyLjEtMi4xIDIuMS0yLjEtMi4xek0yOC43NSAxLjNsMi4xIDIuMS0yLjEgMi4xLTIuMS0yLjF6TTQ2LjIgMjAuMWwyLjEgMi4xLTIuMSAyLjEtMi4xLTIuMXoiLz48L2c+PC9nPjwvc3ZnPg==');
         opacity: 0.1;
         z-index: -1;
      }

      .logo {
         text-align: center;
         margin-bottom: 25px;
      }

      .logo img {
         height: 60px;
      }

      .login-container {
         width: 100%;
         max-width: 460px;
         /* sebelumnya 420px */
         padding: 32px 24px;
         /* sebelumnya 40px */
         background-color: var(--card-bg);
         border-radius: 18px;
         /* sebelumnya 16px */
         box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
         /* perkuat shadow agar jelas di wallpaper */
         z-index: 10;
         position: relative;
         backdrop-filter: saturate(140%) blur(8px);
         /* kurangi blur agar teks tidak tampak pudar */
         border: 1px solid rgba(31, 41, 55, 0.12);
         /* ring lebih halus */
         overflow: visible;
         /* hindari cropping efek dekorasi */
      }

      .login-container::before {
         content: none;
      }

      .login-header {
         text-align: center;
         margin-bottom: 30px;
      }

      .login-header h1 {
         color: var(--primary-color);
         font-size: 26px;
         font-weight: 600;
         margin-bottom: 10px;
         letter-spacing: 0.5px;
      }

      .login-header p {
         color: #6c757d;
         font-size: 15px;
         font-weight: 300;
      }

      .form-group {
         margin-bottom: 20px;
         position: relative;
      }

      .form-group label {
         display: block;
         margin-bottom: 8px;
         color: var(--text-dark);
         font-weight: 500;
         font-size: 14px;
      }

      .input-group {
         position: relative;
      }

      .input-group-prepend {
         position: absolute;
         top: 50%;
         left: 15px;
         transform: translateY(-50%);
         color: #adb5bd;
      }

      .form-control {
         width: 100%;
         padding: 14px 15px 14px 45px;
         border: 1px solid var(--input-border);
         border-radius: 10px;
         /* sedikit lebih bulat agar konsisten */
         background-color: var(--input-bg);
         color: var(--text-dark);
         font-size: 14px;
         font-weight: 400;
         transition: all 0.3s ease;
      }

      .form-control:focus {
         outline: none;
         border-color: var(--input-focus);
         box-shadow: 0 0 0 3px rgba(35, 50, 146, 0.1);
      }

      .form-control.is-invalid {
         border-color: var(--error-color);
      }

      .invalid-feedback {
         color: var(--error-color);
         font-size: 12px;
         margin-top: 5px;
         display: block;
      }

      .custom-select {
         appearance: none;
         -webkit-appearance: none;
         -moz-appearance: none;
         background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
         background-repeat: no-repeat;
         background-position: right 15px center;
         background-size: 16px 12px;
         padding-right: 40px;
      }

      .btn {
         display: inline-block;
         font-weight: 500;
         text-align: center;
         white-space: nowrap;
         vertical-align: middle;
         user-select: none;
         border: 1px solid transparent;
         padding: 14px 30px;
         font-size: 15px;
         line-height: 1.5;
         border-radius: 8px;
         transition: all 0.15s ease-in-out;
         cursor: pointer;
         width: 100%;
      }

      .btn-primary {
         color: #fff;
         background-color: var(--btn-primary);
         border-color: var(--btn-primary);
      }

      .btn-primary:hover {
         background-color: var(--btn-hover);
         border-color: var(--btn-hover);
      }

      .btn-icon {
         margin-right: 8px;
      }

      .login-footer {
         margin-top: 30px;
         text-align: center;
         border-top: 1px solid rgba(0, 0, 0, 0.05);
         padding-top: 20px;
         font-size: 13px;
         color: #6c757d;
      }

      .login-notes {
         margin-top: 25px;
         background-color: rgba(35, 50, 146, 0.05);
         border-radius: 8px;
         padding: 15px;
         font-size: 13px;
      }

      .login-notes h4 {
         font-size: 14px;
         color: var(--primary-color);
         margin-bottom: 10px;
         font-weight: 600;
      }

      .login-notes ol {
         padding-left: 20px;
         color: #6c757d;
      }

      .login-notes li {
         margin-bottom: 5px;
      }

      .alert {
         padding: 15px;
         margin-bottom: 20px;
         border-radius: 8px;
         color: #721c24;
         background-color: #f8d7da;
         border: 1px solid #f5c6cb;
      }

      @media (max-width: 576px) {
         .login-container {
            max-width: 92%;
            /* beri sedikit ruang lebih pada layar kecil */
            padding: 28px 18px;
            /* sesuaikan padding agar kompak */
         }
      }
   </style>
   @if(app()->environment('local', 'development'))
        @viteReactRefresh
    @endif
   @vite('resources/css/app.css')
   <script type="module">
      // Fallback preamble: ensure React dev runtime does not crash if the Vite React Refresh preamble is not injected
      // This must be placed BEFORE app.jsx so that dynamic imports do not throw
      if (!window.__vite_plugin_react_preamble_installed__) {
         window.__vite_plugin_react_preamble_installed__ = true;
         // Provide no-op refresh hooks so plugin runtime is satisfied in non-dev environments
         window.$RefreshReg$ = () => {};
         window.$RefreshSig$ = () => (type) => type;
      }
   </script>
   @vite('resources/js/app.jsx')
</head>

<body>
   <div class="bg-wallpaper"></div>

   <div id="login-premium-react-root" data-action-url="{{ route('customlogin') }}" data-csrf-token="{{ csrf_token() }}"
      data-logo-url="{{ $logoUrl }}"
      data-poli-b64="{{ base64_encode(json_encode($poli, JSON_UNESCAPED_UNICODE)) }}"
      data-error-message="{{ $errors->first('message') }}"
      data-old-username="{{ old('username') }}" data-old-poli="{{ old('poli') }}"
      data-error-username="{{ $errors->first('username') }}" data-error-password="{{ $errors->first('password') }}"
      data-error-poli="{{ $errors->first('poli') }}">
   </div>

   

   <noscript>
      <style>
         .noscript-box {
            max-width: 480px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            font-family: 'Poppins', sans-serif;
            color: #111827
         }
      </style>
      <div class="noscript-box">JavaScript dinonaktifkan atau gagal dimuat. Silakan aktifkan JavaScript untuk
         menggunakan halaman login.</div>
   </noscript>
</body>

</html>