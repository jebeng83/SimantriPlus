<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Terjadi Kesalahan - Simantri PLUS</title>

   <!-- Favicon -->
   <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
   <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

   <style>
      body {
         font-family: 'Arial', sans-serif;
         background-color: #f8f9fa;
         color: #333;
         margin: 0;
         padding: 0;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         min-height: 100vh;
         text-align: center;
      }

      .container {
         max-width: 600px;
         padding: 20px;
         background-color: white;
         border-radius: 8px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         margin: 20px;
      }

      h1 {
         color: #dc3545;
         margin-bottom: 20px;
      }

      p {
         margin-bottom: 15px;
         line-height: 1.5;
      }

      .icon {
         font-size: 64px;
         margin-bottom: 20px;
         color: #dc3545;
      }

      .btn {
         display: inline-block;
         background-color: #0056b3;
         color: white;
         padding: 10px 20px;
         border-radius: 4px;
         text-decoration: none;
         margin-top: 20px;
         transition: background-color 0.3s;
      }

      .btn:hover {
         background-color: #003d7a;
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="icon">⚠️</div>
      <h1>Terjadi Kesalahan</h1>
      <p>Maaf, sistem sedang mengalami gangguan teknis.</p>
      <p>Tim kami sedang bekerja untuk memperbaiki masalah ini. Silakan coba lagi nanti.</p>
      <a href="{{ url('/') }}" class="btn">Kembali ke Beranda</a>
   </div>
   <script>
      // Coba reload halaman setelah 30 detik
        setTimeout(function() {
            window.location.reload();
        }, 30000);
   </script>
</body>

</html>