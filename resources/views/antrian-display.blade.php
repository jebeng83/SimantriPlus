<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <meta http-equiv="refresh" content="3600"> <!-- Auto refresh setelah 1 jam -->
   <title>Antrian Poliklinik - {{ $setting->nama_instansi }}</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet">
   <style>
      :root {
         --primary-color: #1a5276;
         --secondary-color: #2874a6;
         --accent-color: #f39c12;
         --text-light: #ffffff;
         --text-dark: #333333;
         --bg-light: #f8f9fa;
         --bg-dark: #2c3e50;
         --border-color: #e0e0e0;
      }

      body {
         margin: 0;
         padding: 0;
         overflow: hidden;
         background-color: var(--bg-light);
         font-family: 'Poppins', sans-serif;
      }

      .header {
         background: linear-gradient(180deg, #000000, #1a1a1a);
         color: var(--text-light);
         padding: 15px 30px;
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 5px solid var(--accent-color);
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      }

      .logo-container {
         display: flex;
         align-items: center;
      }

      .logo {
         height: 70px;
         margin-right: 20px;
         filter: drop-shadow(2px 2px 2px rgba(0, 0, 0, 0.3));
      }

      .header-text {
         display: flex;
         flex-direction: column;
      }

      .header-title {
         font-size: 36px;
         font-weight: 700;
         margin: 0;
         font-family: 'Montserrat', sans-serif;
         text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.3);
      }

      .header-subtitle {
         font-size: 16px;
         margin-top: 4px;
         opacity: 0.9;
      }

      .datetime-container {
         display: flex;
         flex-direction: column;
         align-items: flex-start;
         flex: 1;
      }

      .datetime-row {
         display: flex;
         align-items: center;
         gap: 15px;
         width: 60%;
      }

      .datetime {
         font-size: 22px;
         text-align: left;
         background-color: rgba(255, 255, 255, 0.15);
         padding: 10px 15px;
         border-radius: 10px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
         flex: 1;
      }

      .test-sound-btn {
         background-color: rgba(255, 255, 255, 0.2);
         border: none;
         color: white;
         padding: 10px 20px;
         border-radius: 10px;
         cursor: pointer;
         transition: all 0.3s ease;
         font-size: 16px;
         display: flex;
         align-items: center;
         gap: 8px;
      }

      .test-sound-btn:hover {
         background-color: rgba(255, 255, 255, 0.3);
      }

      .test-sound-btn i {
         font-size: 18px;
      }

      .time {
         font-size: 26px;
         font-weight: 600;
         margin-top: 5px;
      }

      .refresh-btn {
         margin-top: 10px;
         background-color: rgba(255, 255, 255, 0.2);
         border: none;
         color: white;
         padding: 5px 15px;
         border-radius: 5px;
         cursor: pointer;
         transition: all 0.3s ease;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .refresh-btn:hover {
         background-color: rgba(255, 255, 255, 0.3);
      }

      .refresh-btn i {
         margin-right: 5px;
      }

      .spin {
         animation: spin 1s linear;
      }

      @keyframes spin {
         0% {
            transform: rotate(0deg);
         }

         100% {
            transform: rotate(360deg);
         }
      }

      .table-row-highlight {
         background-color: rgba(26, 82, 118, 0.1) !important;
         animation: highlight-row 2s infinite;
      }

      @keyframes highlight-row {
         0% {
            background-color: rgba(26, 82, 118, 0.1);
         }

         50% {
            background-color: rgba(26, 82, 118, 0.2);
         }

         100% {
            background-color: rgba(26, 82, 118, 0.1);
         }
      }

      #loading-indicator {
         font-size: 18px;
         color: white;
         background-color: rgba(255, 255, 255, 0.2);
         padding: 5px 10px;
         border-radius: 5px;
         display: inline-flex;
         align-items: center;
      }

      .fade-effect {
         transition: opacity 0.3s ease-in-out;
      }

      .update-highlight {
         animation: update-flash 1.5s ease-out;
      }

      @keyframes update-flash {
         0% {
            background-color: rgba(52, 152, 219, 0.3);
         }

         100% {
            background-color: transparent;
         }
      }

      .antrian-header {
         background: linear-gradient(to right, #000000, #1a1a1a);
         color: var(--text-light);
         padding: 20px;
         font-size: 36px;
         font-weight: 600;
         text-align: center;
         text-transform: uppercase;
         letter-spacing: 2px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 3px solid #f39c12;
      }

      .antrian-count {
         font-size: 24px;
         background-color: #f39c12;
         padding: 10px 25px;
         border-radius: 30px;
         margin-left: 15px;
         font-weight: 600;
         letter-spacing: 1px;
      }

      .content {
         display: flex;
         height: calc(100vh - 260px);
         padding: 0;
         margin: 0;
         margin-bottom: 110px;
         /* Tambahkan margin untuk footer */
      }

      .antrian-container {
         flex: 1;
         display: flex;
         flex-direction: column;
         padding: 0;
         background: var(--text-light);
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         margin: 15px 0 15px 15px;
         border-radius: 10px;
         overflow: hidden;
      }

      .antrian-table {
         flex: 1;
         margin: 0;
         width: 100%;
         border-collapse: separate;
         border-spacing: 0 12px;
         padding: 0 15px;
      }

      .antrian-table th {
         background: #000000;
         color: #ffffff;
         font-size: 28px;
         padding: 25px 15px;
         text-align: center;
         text-transform: uppercase;
         letter-spacing: 2px;
         border-bottom: 3px solid #f39c12;
         font-weight: 700;
      }

      .antrian-table td {
         font-size: 40px;
         padding: 25px 15px;
         text-align: center;
         vertical-align: middle;
         color: #ffffff;
         background: rgba(39, 8, 8, 0.7);
      }

      .antrian-nomor {
         font-family: 'Montserrat', sans-serif;
         font-weight: 800;
         font-size: 64px;
         color: #f39c12;
         text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
         letter-spacing: 3px;
         line-height: 1.2;
      }

      .antrian-table tr {
         margin-bottom: 15px;
         transition: transform 0.3s ease;
      }

      .antrian-table tr:hover {
         transform: scale(1.02);
      }

      .status-menunggu {
         font-weight: 600;
         color: #ffffff;
         padding: 15px 30px;
         border-radius: 30px;
         background-color: #f39c12;
         display: inline-block;
         font-size: 28px;
         letter-spacing: 1px;
         box-shadow: 0 4px 8px rgba(243, 156, 18, 0.3);
         text-transform: uppercase;
      }

      .status-dipanggil {
         font-weight: 600;
         color: #ffffff;
         padding: 15px 30px;
         border-radius: 30px;
         background-color: #e74c3c;
         display: inline-block;
         font-size: 28px;
         letter-spacing: 1px;
         box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
         animation: blink 1s infinite;
         text-transform: uppercase;
      }

      .status-dilayani {
         font-weight: 600;
         color: #ffffff;
         padding: 15px 30px;
         border-radius: 30px;
         background-color: #27ae60;
         display: inline-block;
         font-size: 28px;
         letter-spacing: 1px;
         box-shadow: 0 4px 8px rgba(39, 174, 96, 0.3);
         text-transform: uppercase;
      }

      .media-container {
         width: 40%;
         background: #5FBFBC;
         /* Warna teal/hijau seperti di gambar */
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         margin: 15px;
         border-radius: 10px;
         overflow: hidden;
         display: flex;
         flex-direction: column;
         position: relative;
      }

      .media-header {
         background: linear-gradient(to right, #000000, #1a1a1a);
         color: var(--text-light);
         padding: 15px;
         font-size: 24px;
         font-weight: 600;
         text-align: center;
         text-transform: uppercase;
         letter-spacing: 1px;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      }

      .media-content {
         flex: 1;
         overflow: hidden;
         position: relative;
         display: flex;
         flex-direction: column;
         justify-content: flex-start;
         align-items: center;
         padding-top: 30px;
      }

      .media-image {
         width: 100%;
         height: 55%;
         object-fit: cover;
         transition: opacity 1s ease-in-out;
      }

      .media-video {
         width: 100%;
         height: 55%;
         object-fit: cover;
      }

      .footer {
         position: fixed;
         bottom: 40px;
         left: 0;
         right: 0;
         background: linear-gradient(135deg, #000000, #1a1a1a);
         color: var(--text-light);
         font-size: 24px;
         font-weight: 600;
         text-align: center;
         padding: 10px 0;
         height: 50px;
         border-top: 3px solid var(--accent-color);
         box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.3);
         text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
         z-index: 100;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .marquee-container {
         position: fixed;
         bottom: 0;
         left: 0;
         right: 0;
         background: #000000;
         color: white;
         padding: 8px 0;
         height: 40px;
         border-top: 2px solid #f39c12;
         overflow: hidden;
         z-index: 100;
         display: flex;
         align-items: center;
      }

      .marquee-text {
         font-family: 'Montserrat', sans-serif;
         font-size: 20px;
         font-weight: 600;
         white-space: nowrap;
         display: inline-block;
         animation: marquee 30s linear infinite;
         text-transform: uppercase;
         letter-spacing: 2px;
         padding: 0 20px;
      }

      @keyframes marquee {
         0% {
            transform: translateX(100%);
         }

         100% {
            transform: translateX(-100%);
         }
      }

      .blink {
         animation: blinking 1s infinite;
      }

      @keyframes blinking {
         0% {
            opacity: 1;
         }

         50% {
            opacity: 0.5;
         }

         100% {
            opacity: 1;
         }
      }

      .status-batal {
         font-weight: 600;
         color: #ffffff;
         padding: 6px 12px;
         border-radius: 20px;
         background-color: #95a5a6;
         display: inline-block;
         box-shadow: 0 2px 5px rgba(149, 165, 166, 0.3);
      }

      .antrian-call {
         position: fixed;
         bottom: 90px;
         right: 20px;
         background-color: rgba(26, 82, 118, 0.9);
         color: white;
         padding: 15px 25px;
         border-radius: 10px;
         font-size: 24px;
         font-weight: 600;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
         display: none;
         z-index: 1000;
         animation: fadeInOut 5s ease-in-out;
      }

      @keyframes fadeInOut {
         0% {
            opacity: 0;
            transform: translateY(20px);
         }

         10% {
            opacity: 1;
            transform: translateY(0);
         }

         90% {
            opacity: 1;
            transform: translateY(0);
         }

         100% {
            opacity: 0;
            transform: translateY(20px);
         }
      }

      .info-panel {
         padding: 15px;
         text-align: center;
         background-color: #f5f5f5;
         margin-top: auto;
         width: 100%;
         position: absolute;
         bottom: 0;
         left: 0;
         right: 0;
      }

      .calling-title {
         font-size: 24px;
         font-weight: 700;
         color: #194B7A;
         margin-bottom: 12px;
         text-transform: uppercase;
         letter-spacing: 1px;
      }

      .calling-number {
         font-size: 105px !important;
         font-weight: 800;
         color: #f40808 !important;
         line-height: 1;
         margin-bottom: 5px;
         letter-spacing: 3px;
         max-width: 100%;
         word-break: break-word;
      }

      .calling-name {
         font-size: 28px;
         font-weight: 700;
         margin-bottom: 15px;
         max-width: 100%;
         overflow: hidden;
         text-overflow: ellipsis;
      }

      .calling-poli {
         background-color: #f40808;
         color: white;
         font-size: 24px;
         font-weight: 600;
         padding: 10px 25px;
         border-radius: 30px;
         margin-top: 5px;
         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
         letter-spacing: 1px;
         display: inline-block;
         max-width: 100%;
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
      }

      .patient-calling.active {
         animation: pulse 2s infinite;
      }

      @keyframes pulse {
         0% {
            transform: scale(1);
         }

         50% {
            transform: scale(1.05);
         }

         100% {
            transform: scale(1);
         }
      }

      .custom-hr {
         border: 0;
         height: 1px;
         background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
         margin: 10px 0;
      }

      .called-patient-panel {
         position: absolute;
         bottom: 150px;
         left: 50%;
         transform: translateX(-50%);
         z-index: 100;
         background-color: white;
         box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
         border-radius: 15px;
         padding: 20px 30px;
         text-align: center;
         width: 75%;
         max-width: 500px;
      }

      .video-container {
         width: 100%;
         height: 40%;
         overflow: hidden;
         margin-bottom: 20px;
         background-color: #000;
         display: flex;
         justify-content: center;
         align-items: center;
      }

      #media-video {
         width: 100%;
         height: 100%;
         object-fit: contain;
      }

      .static-container {
         background-color: white;
         border-radius: 10px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
         margin: 15px;
         padding: 20px;
         height: calc(100% - 30px);
         display: flex;
         flex-direction: column;
         justify-content: center;
      }

      .info-list {
         list-style: none;
         padding: 0;
         margin: 0;
      }

      .info-list li {
         margin-bottom: 10px;
         display: flex;
         align-items: center;
      }

      .info-list li i {
         margin-right: 10px;
         color: #2874a6;
      }
   </style>
   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <!-- Scripts -->
   <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = false;

      window.Laravel = {!! json_encode([
          'csrfToken' => csrf_token(),
          'pusherKey' => config('broadcasting.connections.pusher.key'),
          'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
      ]) !!};

      // Inisialisasi Pusher dengan konfigurasi yang benar
      const pusher = new Pusher(window.Laravel.pusherKey, {
          cluster: window.Laravel.pusherCluster,
          forceTLS: true
      });

      // Subscribe ke channel antrian
      const channel = pusher.subscribe('antrian');

      // Variabel untuk audio system
      let audioContext = null;
      let audioElement = null;
      let audioInitialized = false;
      let pendingAudioPlay = null;

      // Fungsi untuk menampilkan error
      function showError(message) {
          const errorDiv = document.createElement('div');
          errorDiv.style.cssText = `
              position: fixed;
              top: 20px;
              right: 20px;
              background: rgba(220, 53, 69, 0.95);
              color: white;
              padding: 15px 25px;
              border-radius: 8px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.15);
              z-index: 9999;
              font-family: 'Poppins', sans-serif;
          `;
          errorDiv.textContent = message;
          document.body.appendChild(errorDiv);
          setTimeout(() => errorDiv.remove(), 5000);
      }

      // Fungsi untuk menampilkan prompt aktivasi audio
      function showAudioActivationPrompt() {
          const promptDiv = document.createElement('div');
          promptDiv.style.cssText = `
              position: fixed;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              background: rgba(0, 0, 0, 0.9);
              color: white;
              padding: 20px;
              border-radius: 10px;
              text-align: center;
              z-index: 9999;
          `;
          
          promptDiv.innerHTML = `
              <h3>Aktivasi Sistem Suara</h3>
              <p>Klik tombol di bawah untuk mengaktifkan sistem suara antrian</p>
              <button onclick="initAudio()" style="
                  padding: 10px 20px;
                  background: #007bff;
                  color: white;
                  border: none;
                  border-radius: 5px;
                  cursor: pointer;
              ">Aktifkan Suara</button>
          `;
          
          document.body.appendChild(promptDiv);
      }

      // Fungsi untuk inisialisasi audio
      async function initAudio() {
          try {
              audioContext = new (window.AudioContext || window.webkitAudioContext)();
              audioElement = new Audio();
              const source = audioContext.createMediaElementSource(audioElement);
              source.connect(audioContext.destination);
              
              if (audioContext.state === 'suspended') {
                  await audioContext.resume();
              }
              
              audioInitialized = true;
              
              // Remove prompt if exists
              const prompt = document.querySelector('[onclick="initAudio()"]')?.parentElement;
              if (prompt) prompt.remove();
              
              // Play pending audio if exists
              if (pendingAudioPlay) {
                  const data = pendingAudioPlay;
                  pendingAudioPlay = null;
                  await playAntrianSound(data);
              }
              
              return true;
          } catch (error) {
              showError('Gagal mengaktifkan sistem suara');
              return false;
          }
      }

      // Fungsi untuk memainkan file audio
      function playAudioFile(filename) {
          return new Promise((resolve, reject) => {
              if (!audioElement) {
                  reject(new Error('Audio belum diinisialisasi'));
                  return;
              }

              const fullPath = filename.startsWith('/') ? filename : '/' + filename;
              audioElement.src = fullPath;
              
              audioElement.onloadeddata = () => {};
              audioElement.onended = () => setTimeout(resolve, 300);
              audioElement.onerror = () => reject(new Error(`Gagal memainkan ${filename}`));
              
              const playPromise = audioElement.play();
              if (playPromise !== undefined) {
                  playPromise.catch(error => {
                      if (error.name === 'NotAllowedError') {
                          showAudioActivationPrompt();
                          reject(new Error('Perlu interaksi pengguna'));
                      } else {
                          reject(error);
                      }
                  });
              }
          });
      }

      // Fungsi untuk memainkan nomor antrian
      async function playAntrianNumber(number) {
          const cleanNumber = String(number).replace(/\D/g, '');
          if (!cleanNumber) throw new Error('Nomor antrian tidak valid');
          
          for (const digit of cleanNumber) {
              await playAudioFile(`/assets/audio/antrian/${digit}.mp3`);
          }
      }

      // Fungsi untuk mendapatkan nama file poli
      function getPoliFileName(poliName) {
          const poliLower = poliName.toLowerCase();
          if (poliLower.includes('umum')) return 'umum';
          if (poliLower.includes('gigi')) return 'gigi';
          if (poliLower.includes('kia')) return 'kia';
          return 'umum';
      }

      // Fungsi utama untuk memainkan suara antrian
      async function playAntrianSound(data) {
          try {
              if (!audioInitialized) {
                  pendingAudioPlay = data;
                  showAudioActivationPrompt();
                  return;
              }

              await playAudioFile('assets/audio/bell.mp3');
              await playAudioFile('assets/audio/nomor-antrian.mp3');
              await playAntrianNumber(data.no_reg || data.nomor_antrian);
              await playAudioFile('assets/audio/menuju.mp3');
              await playAudioFile(`assets/audio/poli/${getPoliFileName(data.poli)}.mp3`);
              
          } catch (error) {
              showError(error.message);
          }
      }

      // Event listener saat dokumen dimuat
      document.addEventListener('DOMContentLoaded', () => {
          // Tampilkan prompt aktivasi audio
          showAudioActivationPrompt();
          
          // Setup event listener untuk antrian
          channel.bind('antrian.dipanggil', async (data) => {
              if (!data?.no_reg) {
                  return;
              }
              
              try {
                  await playAntrianSound(data);
                  updatePanggilanStatus(data);
              } catch (error) {
                  showError(error.message);
              }
          });
      });
   </script>

   <script>
      // Setup CSRF token untuk semua request AJAX
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      // Update tanggal dan waktu
      function updateDateTime() {
         const dateElement = document.getElementById('date');
         const timeElement = document.getElementById('time');
         
         if (dateElement && timeElement) {
             const now = new Date();
             const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
             
             dateElement.textContent = now.toLocaleDateString('id-ID', options);
             timeElement.textContent = now.toLocaleTimeString('id-ID');
         }
      }
        
      // Jalankan update datetime pertama kali
      document.addEventListener('DOMContentLoaded', function() {
         updateDateTime();
         // Update setiap detik
         setInterval(updateDateTime, 1000);
      });

      // Fungsi untuk update status secara langsung
      function updatePanggilanStatus(data) {
          // Update status di tabel
          $(`#antrian-body tr`).each(function() {
              const noReg = $(this).find('td:first').text();
              if (noReg === data.no_reg) {
                  $(this).addClass('table-row-highlight');
                  $(this).find('td:last span')
                      .removeClass()
                      .addClass('status-dipanggil')
                      .text('DIPANGGIL');
              }
          });
          
          // Update panel pasien dipanggil
          $('#dipanggil-noreg').text(data.no_reg);
          $('#dipanggil-nama').text(samarkanNama(data.nm_pasien || data.nama));
          $('#dipanggil-poli').text(data.nm_poli || data.poli);
          
          // Tambahkan efek highlight pada panel
          $('#panel-dipanggil').hide().fadeIn('fast');
          $('#patient-calling').addClass('active');
          
          // Hapus efek highlight setelah 5 detik
          setTimeout(() => {
              $('#patient-calling').removeClass('active');
          }, 5000);
      }

      // Fungsi untuk menangani video
      function setupVideo() {
         const videoContainer = document.getElementById('video-container');
         const staticContent = document.getElementById('static-content');
         const mediaVideo = document.getElementById('media-video');
         
         // Ambil daftar video dari server
         fetch('/get-videos')
            .then(response => response.json())
            .then(videos => {
               if (videos.length > 0) {
                  let currentVideoIndex = 0;
                  
                  function playNextVideo() {
                     mediaVideo.src = `/assets/vidio/${videos[currentVideoIndex]}`;
                     mediaVideo.load();
                     mediaVideo.play();
                     currentVideoIndex = (currentVideoIndex + 1) % videos.length;
                  }
                  
                  // Mainkan video pertama
                  playNextVideo();
                  
                  // Ganti video ketika selesai
                  mediaVideo.addEventListener('ended', playNextVideo);
                  
                  // Tampilkan container video
                  videoContainer.style.display = 'block';
                  staticContent.style.display = 'none';
               } else {
                  // Jika tidak ada video, tampilkan konten statis
                  videoContainer.style.display = 'none';
                  staticContent.style.display = 'flex';
               }
            })
            .catch(error => {
               videoContainer.style.display = 'none';
               staticContent.style.display = 'flex';
            });
      }

      // Fungsi untuk menangani konten
      function setupContent() {
         setupVideo(); // Panggil setupVideo saat konten diinisialisasi
      }

      // Fungsi untuk memuat data antrian
      function loadAntrianData() {
          $.ajax({
              url: '/antrian/display/data',
              method: 'GET',
              success: function(response) {
                  if (response.success) {
                      updateAntrianTable(response.antrian);
                      $('#data-count').text(response.count);
                      
                      // Update panel pasien dipanggil jika ada
                      if (response.dipanggil) {
                          $('#dipanggil-noreg').text(response.dipanggil.no_reg);
                          $('#dipanggil-nama').text(samarkanNama(response.dipanggil.nm_pasien));
                          $('#dipanggil-poli').text(response.dipanggil.nm_poli);
                          $('#panel-dipanggil').show();
                          $('#patient-calling').addClass('active');
                      }
                  } else {
                      showError('Gagal memuat data antrian');
                  }
              },
              error: function(xhr, status, error) {
                  showError('Gagal memuat data antrian');
              }
          });
      }

      // Fungsi untuk memperbarui tabel antrian
      function updateAntrianTable(data) {
          const tbody = $('#antrian-body');
          tbody.empty();

          if (!Array.isArray(data)) {
              showError('Format data tidak valid');
              return;
          }

          data.forEach((item, index) => {
              const row = $('<tr>');
              
              // Tambahkan highlight jika status dipanggil
              if (item.status === 'DIPANGGIL') {
                  row.addClass('table-row-highlight');
              }
              
              // Pastikan semua field yang diperlukan ada
              const no_reg = item.no_reg || '';
              const nm_pasien = item.nm_pasien || '';
              const nm_poli = item.nm_poli || '';
              const status = item.status || 'MENUNGGU';
              
              row.append(`
                  <td class="antrian-nomor">${no_reg}</td>
                  <td>${samarkanNama(nm_pasien)}</td>
                  <td>${nm_poli}</td>
                  <td><span class="status-${status.toLowerCase()}">${status}</span></td>
              `);
              
              tbody.append(row);
          });
      }

      // Event handler untuk tombol refresh
      $('#btn-refresh').on('click', function() {
          const icon = $(this).find('i');
          icon.addClass('spin');
          loadAntrianData();
          setTimeout(() => icon.removeClass('spin'), 1000);
      });

      // Fungsi untuk test suara
      async function testSuara() {
         try {
            // Data test
            const testData = {
               no_reg: '001',
               nama: 'PASIEN TEST',
               poli: 'POLI UMUM',
               is_ulang: false
            };
            
            // Mainkan suara
            await playAntrianSound(testData);
         } catch (error) {
            showError('Error: ' + error.message);
         }
      }

      // Panggil setupContent saat dokumen dimuat
      document.addEventListener('DOMContentLoaded', function() {
         // Inisialisasi audio context dan element
         try {
             initAudio();
             
             // Tambahkan event listener untuk user interaction
             document.body.addEventListener('click', function() {
                 if (audioContext.state === 'suspended') {
                     audioContext.resume().then(() => {
                         showError('Sistem suara siap digunakan');
                     });
                 }
             }, { once: true });
             
         } catch (error) {
             showError('Error inisialisasi sistem suara: ' + error.message);
         }
         
         setupContent();
         updateDateTime();
         loadAntrianData();
         
         // Auto refresh setiap 30 detik
         setInterval(function() {
             loadAntrianData();
         }, 30000);
         
         // Update waktu setiap detik
         setInterval(updateDateTime, 1000);
      });

      // Fungsi untuk menyamarkan nama pasien
      function samarkanNama(nama) {
         if (!nama) return '';
         
         // Pisahkan nama berdasarkan spasi
         var namaParts = nama.split(' ');
         var result = '';
         
         // Proses setiap bagian nama
         for (var i = 0; i < namaParts.length; i++) {
            var part = namaParts[i];
            
            if (part.length <= 0) continue;
            
            // Ambil huruf pertama
            var initial = part.charAt(0);
            
            // Buat string bintang sesuai panjang nama
            var stars = '';
            for (var j = 1; j < part.length; j++) {
               stars += '*';
            }
            
            // Gabungkan inisial dengan bintang
            result += initial + stars + ' ';
         }
         
         return result.trim();
      }
   </script>
</head>

<body>
   <!-- Header -->
   <div class="header">
      <div class="datetime-container">
         <div class="datetime-row">
            <div class="datetime">
               <div id="date"></div>
               <div id="time" class="time"></div>
            </div>
            <button class="test-sound-btn" onclick="testSuara()">
               <i class="fas fa-volume-up"></i> Test Suara
            </button>
         </div>
         <button id="btn-refresh" class="refresh-btn">
            <i class="fas fa-sync-alt"></i> Refresh Data
         </button>
      </div>
      <div class="logo-container">
         <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" alt="Logo" class="logo">
         <div class="header-text">
            <h1 class="header-title">{{ strtoupper($setting->nama_instansi) }}</h1>
            <div class="header-subtitle">{{ $setting->alamat_instansi }}, {{ $setting->kabupaten }}, {{
               $setting->propinsi }}</div>
         </div>
      </div>
   </div>

   <!-- Content -->
   <div class="content">
      <!-- Antrian Section -->
      <div class="antrian-container">
         <div class="antrian-header">
            <div>ANTRIAN POLIKLINIK</div>
            <div class="antrian-count">Jumlah: <span id="data-count">0</span></div>
         </div>
         <table class="antrian-table">
            <thead>
               <tr>
                  <th width="15%">NO. ANTRIAN</th>
                  <th width="35%">NAMA PASIEN</th>
                  <th width="35%">POLIKLINIK</th>
                  <th width="15%">STATUS</th>
               </tr>
            </thead>
            <tbody id="antrian-body">
               <!-- Data antrian akan diisi melalui JavaScript -->
            </tbody>
         </table>
      </div>

      <!-- Media Section -->
      <div class="media-container">
         <div class="media-header">
            INFORMASI
         </div>
         <div class="media-content">
            <!-- Static content -->
            <div id="static-content" class="static-container">
               <div style="text-align: center; width: 100%; padding: 20px;">
                  <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" alt="Logo"
                     style="max-width: 200px; margin-bottom: 20px;">
                  <h3 style="color: #194B7A; margin-bottom: 15px;">Selamat Datang di {{ $setting->nama_instansi }}</h3>
                  <div style="background-color: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
                     <h4 style="color: #2874a6; margin-bottom: 10px;">Informasi Pelayanan</h4>
                     <ul style="list-style: none; padding: 0; text-align: left;">
                        <li style="margin-bottom: 8px;"><i class="fas fa-clock"></i> Senin - Kamis: 07:30 - 14:00</li>
                        <li style="margin-bottom: 8px;"><i class="fas fa-clock"></i> Jumat: 07:30 - 11:00</li>
                        <li style="margin-bottom: 8px;"><i class="fas fa-phone"></i> Telp: {{ $setting->kontak }}</li>
                        <li><i class="fas fa-map-marker-alt"></i> {{ $setting->alamat_instansi }}</li>
                     </ul>
                  </div>
               </div>
            </div>

            <!-- Video container -->
            <div id="video-container" class="video-container">
               <video id="media-video" width="100%" height="100%" autoplay muted loop playsinline>
                  Peramban Anda tidak mendukung tag video.
               </video>
            </div>

            <!-- Panel Pasien Dipanggil -->
            <div class="called-patient-panel" id="panel-dipanggil">
               <div class="patient-calling" id="patient-calling">
                  <div class="calling-title">NOMOR ANTRIAN DIPANGGIL</div>
                  <div class="calling-number" id="dipanggil-noreg">001</div>
                  <div class="calling-name" id="dipanggil-nama">K****** N*</div>
                  <div class="calling-poli" id="dipanggil-poli">KIA ( UMUR 0 - 18 TAHUN )</div>
               </div>
            </div>

            <div class="info-panel">
               <h2 style="font-size: 24px; font-weight: 700; color: #194B7A; margin-bottom: 8px;">
                  {{ strtoupper($setting->nama_instansi) }}
               </h2>
               <p style="font-size: 16px; margin-bottom: 5px;">
                  {{ $setting->alamat_instansi }}, {{ $setting->kabupaten }}, {{ $setting->propinsi }}
               </p>
               <p style="font-size: 16px; margin-bottom: 5px;">Telp: {{ $setting->kontak }}</p>
            </div>
         </div>
      </div>
   </div>

   <!-- Notifikasi Panggilan -->
   <div class="antrian-call" id="antrian-call">
      <i class="fas fa-bell mr-2"></i> <span id="call-text"></span>
   </div>

   <!-- Footer -->
   <div class="footer">
      <i class="fas fa-hospital-alt mr-2"></i> Selamat Datang di {{ $setting->nama_instansi }}
   </div>

   <!-- Marquee -->
   <div class="marquee-container">
      <div class="marquee-text">
         &#x2605; We Serve Better &#x2605; Melayani Dengan Sepenuh Hati &#x2605; We Serve Better &#x2605; Kesehatan Anda
         Prioritas Kami &#x2605;
      </div>
   </div>

   <!-- Scripts -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>