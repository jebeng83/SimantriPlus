@extends('adminlte::page')

@section('title', 'Terjadi Kesalahan - Simantri PLUS')

@section('content_header')
<h1>Terjadi Kesalahan</h1>
@stop

@section('content')
<div class="error-page">
   <div class="row">
      <div class="col-12 col-md-8 offset-md-2">
         <div class="card horror-card">
            <div class="card-body text-center p-5">
               <div id="ghost-container" style="position: relative; height: 300px; overflow: hidden;">
                  <img id="ghost"
                     src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMjU2IDhDMTE5IDggOCAxMTkgOCAyNTZzMTExIDI0OCAyNDggMjQ4IDI0OC0xMTEgMjQ4LTI0OFMzOTMgOCAyNTYgOHptMCA0NDhjLTExMC41IDAtMjAwLTg5LjUtMjAwLTIwMFMxNDUuNSA1NiAyNTYgNTZzMjAwIDg9LjUgMjAwIDIwMC04OS41IDIwMC0yMDAgMjAwem0tODAtMjE2YzE3LjcgMCAzMi0xNC4zIDMyLTMycy0xNC4zLTMyLTMyLTMyLTMyIDE0LjMtMzIgMzIgMTQuMyAzMiAzMiAzMnptMTYwLTY0YzE3LjcgMCAzMi0xNC4zIDMyLTMycy0xNC4zLTMyLTMyLTMyLTMyIDE0LjMtMzIgMzIgMTQuMyAzMiAzMiAzMnptLTQwIDMyYzAtMjIuMS0xNy45LTQwLTQwLTQwcy00MCAxNy45LTQwIDQwIDE3LjkgNDAgNDAgNDAgNDAtMTcuOSA0MC00MHoiLz48L3N2Zz4="
                     style="position: absolute; width: 150px; height: 150px; bottom: -200px; left: 50%; transform: translateX(-50%) scale(0.5); opacity: 0; filter: drop-shadow(0 0 15px rgba(255,0,0,0.7));">
               </div>

               <h2 class="text-danger mb-4" id="error-title">TERJADI KESALAHAN!</h2>
               <p class="lead mb-5" id="error-text">Sistem mendeteksi kehadiran asing...</p>
               <p class="mb-5" id="ghost-text" style="display:none; color:red; font-weight:bold; font-size:1.5rem;">
                  ARCHHHHHH... IKUT AKU!!!</p>

               <audio id="scream" preload="auto">
                  <source src="{{ asset('sounds/scream.mp3') }}" type="audio/mpeg">
               </audio>

               <a href="{{ route('home') }}" class="btn btn-danger btn-lg mt-4 pulse">
                  <i class="fas fa-running mr-2"></i> LARI SEKARANG!
               </a>
            </div>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<style>
   .error-page {
      margin-top: 2rem;
   }

   .horror-card {
      background-color: #000 !important;
      border: 2px solid #ff0000 !important;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(255, 0, 0, 0.5) !important;
   }

   .card-body {
      background-color: #111;
      position: relative;
      overflow: hidden;
   }

   .btn-danger {
      background-color: #ff0000;
      border-color: #ff0000;
      animation: pulse 1.5s infinite;
   }

   @keyframes pulse {
      0% {
         transform: scale(1);
      }

      50% {
         transform: scale(1.1);
      }

      100% {
         transform: scale(1);
      }
   }

   #ghost {
      transition: all 1s ease-in-out;
   }

   body {
      background-color: #000;
   }

   .text-danger {
      color: #ff0000 !important;
      text-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
   }
</style>
@stop

@section('js')
<script>
   document.addEventListener('DOMContentLoaded', function() {
      // Efek suara
      const screamSound = document.getElementById('scream');
      
      // Animasi hantu muncul
      setTimeout(function() {
         const ghost = document.getElementById('ghost');
         const errorText = document.getElementById('error-text');
         const errorTitle = document.getElementById('error-title');
         const ghostText = document.getElementById('ghost-text');
         
         // Hantu muncul
         ghost.style.bottom = '100px';
         ghost.style.opacity = '1';
         ghost.style.transform = 'translateX(-50%) scale(1.2)';
         
         // Efek bergetar dan suara
         setTimeout(function() {
            ghost.style.animation = 'shake 0.3s infinite';
            
            // Ganti teks dan mainkan suara
            setTimeout(function() {
               errorText.style.display = 'none';
               errorTitle.textContent = 'AWAS DI BELAKANGMU!';
               ghostText.style.display = 'block';
               screamSound.play();
               
               // Efek hantu membesar seolah menerkam
               ghost.style.transform = 'translateX(-50%) scale(2.5)';
               ghost.style.filter = 'drop-shadow(0 0 30px rgba(255,0,0,0.9))';
               
               // Flash merah
               document.body.style.backgroundColor = '#300';
               setTimeout(() => {
                  document.body.style.backgroundColor = '#000';
               }, 300);
               
            }, 1000);
         }, 800);
      }, 1000);
   });
</script>

<style>
   @keyframes shake {
      0% {
         transform: translateX(-50%) rotate(0deg) scale(1.2);
      }

      25% {
         transform: translateX(-50%) rotate(5deg) scale(1.3);
      }

      50% {
         transform: translateX(-50%) rotate(0deg) scale(1.2);
      }

      75% {
         transform: translateX(-50%) rotate(-5deg) scale(1.3);
      }

      100% {
         transform: translateX(-50%) rotate(0deg) scale(1.2);
      }
   }

   /* Efek darah di background */
   .card-body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><path fill="rgba(255,0,0,0.05)" d="M20,20 Q30,10 40,20 T60,20 T80,20 T100,20 T120,20" /></svg>');
      opacity: 0.3;
      pointer-events: none;
   }
</style>
@stop