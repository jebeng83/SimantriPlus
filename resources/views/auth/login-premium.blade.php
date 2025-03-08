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

      .bg-gradient {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
         clip-path: polygon(0 0, 100% 0, 100% 65%, 0 100%);
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
         max-width: 420px;
         padding: 40px;
         background-color: var(--card-bg);
         border-radius: 16px;
         box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
         z-index: 10;
         position: relative;
         backdrop-filter: blur(10px);
         border: 1px solid rgba(255, 255, 255, 0.2);
         overflow: hidden;
      }

      .login-container::before {
         content: "";
         position: absolute;
         top: -50%;
         left: -50%;
         width: 200%;
         height: 200%;
         background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
         z-index: -1;
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
         border-radius: 8px;
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
            max-width: 90%;
            padding: 30px 20px;
         }
      }
   </style>
</head>

<body>
   <div class="bg-gradient"></div>
   <div class="bg-pattern"></div>

   <div class="login-container">
      <div class="logo">
         <img src="{{ asset(config('adminlte.logo_img')) }}" alt="Simantri PLUS">
      </div>

      <div class="login-header">
         <h1>Selamat Datang</h1>
         <p>Silakan masuk untuk melanjutkan</p>
      </div>

      @error('message')
      <div class="alert">
         {{ $message }}
      </div>
      @enderror

      <form action="{{ route('customlogin') }}" method="post">
         @csrf
         <input type="hidden" name="_token" value="{{ csrf_token() }}">

         <div class="form-group">
            <label for="username">ID Khanza</label>
            <div class="input-group">
               <div class="input-group-prepend">
                  <i class="fas fa-user-md"></i>
               </div>
               <input type="text" id="username" name="username"
                  class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                  placeholder="Masukkan ID Khanza" autofocus>
               @error('username')
               <span class="invalid-feedback">
                  {{ $message }}
               </span>
               @enderror
            </div>
         </div>

         <div class="form-group">
            <label for="password">Kata Sandi</label>
            <div class="input-group">
               <div class="input-group-prepend">
                  <i class="fas fa-lock"></i>
               </div>
               <input type="password" id="password" name="password"
                  class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi">
               @error('password')
               <span class="invalid-feedback">
                  {{ $message }}
               </span>
               @enderror
            </div>
         </div>

         <div class="form-group">
            <label for="poli">Pilih Poliklinik</label>
            <div class="input-group">
               <div class="input-group-prepend">
                  <i class="fas fa-hospital"></i>
               </div>
               <select id="poli" name="poli" class="form-control custom-select">
                  @foreach($poli as $p)
                  <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
                  @endforeach
               </select>
            </div>
         </div>

         <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt btn-icon"></i>
            Masuk
         </button>

         <div class="login-notes">
            <h4>Catatan Penting:</h4>
            <ol>
               <li>Login menggunakan ID masing-masing</li>
               <li>Pilih POLIKLINIK pilihan Anda</li>
               <li>Sesi login akan berakhir dalam 30 menit jika tidak ada aktivitas</li>
            </ol>
         </div>
      </form>

      <div class="login-footer">
         &copy; {{ date('Y') }} Simantri PLUS - Semua Hak Dilindungi
      </div>
   </div>
</body>

</html>