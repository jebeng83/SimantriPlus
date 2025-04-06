<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Logout - Simantri PLUS</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <style>
      :root {
         --primary-color: #233292;
         --secondary-color: #1a2570;
         --accent-color: #4f5bda;
         --text-color: #ffffff;
         --card-bg: rgba(255, 255, 255, 0.15);
      }

      body {
         font-family: 'Poppins', sans-serif;
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         margin: 0;
         color: var(--text-color);
         text-align: center;
         background: transparent;
         position: relative;
         overflow: hidden;
      }

      body::before {
         content: "";
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: transparent;
         z-index: 0;
      }

      .container {
         position: relative;
         padding: 40px;
         border-radius: 16px;
         background-color: rgba(87, 11, 238, 0.955);
         max-width: 500px;
         backdrop-filter: blur(15px);
         -webkit-backdrop-filter: blur(15px);
         box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
         border: 1px solid rgba(255, 255, 255, 0.2);
         z-index: 1;
         animation: fadeIn 0.6s ease-out;
      }

      @keyframes fadeIn {
         from {
            opacity: 0;
            transform: translateY(20px);
         }

         to {
            opacity: 1;
            transform: translateY(0);
         }
      }

      h1 {
         margin-bottom: 20px;
         font-weight: 600;
         font-size: 2.2rem;
         letter-spacing: 0.5px;
         color: var(--text-color);
         text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      }

      p {
         font-size: 1.1rem;
         line-height: 1.6;
         margin-bottom: 25px;
         font-weight: 300;
         opacity: 0.95;
      }

      .loading {
         position: relative;
         width: 70px;
         height: 70px;
         margin: 25px auto;
      }

      .loading::before,
      .loading::after {
         content: "";
         position: absolute;
         border-radius: 50%;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
      }

      .loading::before {
         border: 3px solid rgba(255, 255, 255, 0.2);
      }

      .loading::after {
         border: 3px solid transparent;
         border-top-color: #ffffff;
         animation: spin 1.2s linear infinite;
      }

      @keyframes spin {
         0% {
            transform: rotate(0deg);
         }

         100% {
            transform: rotate(360deg);
         }
      }

      .logo {
         margin-bottom: 20px;
         font-size: 1.5rem;
         font-weight: 700;
         letter-spacing: 1px;
         color: var(--text-color);
      }

      .footer {
         margin-top: 30px;
         font-size: 0.8rem;
         opacity: 0.7;
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="logo">SIMANTRI PLUS</div>
      <h1>Sedang Keluar</h1>
      <p>Mohon tunggu sebentar, sistem sedang membersihkan data sesi Anda untuk menjaga keamanan akun.</p>
      <div class="loading"></div>
      <div class="footer">© {{ date('Y') }} Simantri PLUS - Semua Hak Dilindungi</div>
   </div>

   <script>
      document.addEventListener('DOMContentLoaded', function() {
            // Hapus cookies
            function deleteAllCookies() {
                const cookies = document.cookie.split(";");
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i];
                    const eqPos = cookie.indexOf("=");
                    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                }
            }

            // Hapus cache
            function clearCache() {
                window.caches && caches.keys().then(function(names) {
                    for (let name of names) caches.delete(name);
                });
            }

            // Hapus localStorage
            function clearLocalStorage() {
                localStorage.clear();
            }

            // Hapus sessionStorage
            function clearSessionStorage() {
                sessionStorage.clear();
            }

            // Menjalankan semua fungsi pembersihan
            deleteAllCookies();
            clearCache();
            clearLocalStorage();
            clearSessionStorage();

            // Hapus riwayat (tidak bisa dilakukan langsung karena batasan keamanan browser)
            // Sebagai gantinya, kita bisa menggunakan teknik replacingState
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Melakukan redirect setelah pembersihan (beri jeda 2 detik)
            setTimeout(function() {
                window.location.href = '{{ route('login') }}';
            }, 2000);
        });
   </script>
</body>

</html>