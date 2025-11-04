//=========================================================================
// Menampilkan data rumah sakit
$(document).ready(function () {
  $.ajax({
    url: "app/antrian.php?p=pengaturan",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var namars = $("#namars");
      namars.html(data.nama_instansi);
      var text = $("#text");
      text.html(data.text || "PERHATIAN: Pendaftaran Poli tutup jam 11:00 WIB. Terima kasih.");
    },
    error: function() {
      console.log("Gagal memuat pengaturan");
    }
  });
});

// //==================pengaturan video ===============
const videoPlayer = document.getElementById("myVideo");
// ganti path video , bisa juga menggunakan url video
const videos = [
  "video/vid-1.mp4",
  "video/vid-2.mp4",
  "video/vid-3.mp4",
  "video/vid-4.mp4",
  "video/vid-5.mp4",
  "video/vid-6.mp4",
  "video/vid-7.mp4",
  "video/vid-8.mp4",
  "video/vid-9.mp4"
];
// Ganti dengan daftar video yang Anda inginkan
let currentVideoIndex = 0;
// Fungsi untuk mengatur video pertama sebagai sumber awal
function setInitialVideo() {
  videoPlayer.src = videos[currentVideoIndex];
}

// Event listener saat video selesai diputar
videoPlayer.addEventListener("ended", () => {
  try {
    // Dapatkan indeks berikutnya
    currentVideoIndex = (currentVideoIndex + 1) % videos.length;
    
    // Set source baru
    videoPlayer.src = videos[currentVideoIndex];
    videoPlayer.load(); // Tambahkan ini untuk memastikan video dimuat ulang
    
    // Pastikan selalu muted agar bisa autoplay
    videoPlayer.muted = true;
    
    // Tangani play dengan Promise dan tangkap error
    const playPromise = videoPlayer.play();
    
    // Cek apakah promise tersedia (di browser modern tersedia)
    if (playPromise !== undefined) {
      playPromise.catch(error => {
        console.log("Gagal memutar video:", error);
        
        if (error.name === "AbortError" || error.name === "NotAllowedError") {
          // Jika ada AbortError, coba lagi dengan delay
          setTimeout(() => {
            videoPlayer.muted = true; // Pastikan muted diaktifkan
            videoPlayer.play().catch(e => console.error("Masih gagal memutar video setelah retry:", e));
          }, 500);
        }
      });
    }
  } catch (error) {
    console.error("Error saat mengganti video:", error);
  }
});
// Mengatur volume ke 20%
videoPlayer.volume = 0.1;
// Mengatur Mute Video
videoPlayer.muted = true;
videoPlayer.playsInline = true;
videoPlayer.poster = "video/cover.jpg";

// Panggil fungsi untuk mengatur video pertama saat halaman dimuat
setInitialVideo();

// Tambahkan event listener untuk membuat user mendengar audio lagi setelah interaksi
document.addEventListener('click', function() {
  // Jika user mengklik halaman, coba putar video jika tertunda
  if (videoPlayer.paused) {
    videoPlayer.play().catch(function(e) {
      console.log("Masih gagal memutar video setelah klik:", e);
    });
  }
});

// Format nomor menjadi pola dengan prefix loket, contoh: A-001, B-002
function formatQueueNumber(loket, number) {
  // Tentukan prefix berdasarkan loket
  let prefix = 'A'; // default
  
  if (loket && typeof loket === 'string') {
    const loketUpper = loket.toUpperCase();
    if (loketUpper.includes('1')) {
      prefix = 'A';
    } else if (loketUpper.includes('2')) {
      prefix = 'B';
    } else if (loketUpper.includes('3')) {
      prefix = 'C';
    } else if (loketUpper.includes('4')) {
      prefix = 'D';
    }
  }
  
  // Pad angka dengan leading zeros dan tambahkan prefix
  return prefix + '-' + String(number).padStart(3, '0');
}

//=========================================================================
// Variabel untuk mencegah pemutaran berulang
let lastCalledNumber = localStorage.getItem('lastCalledNumber') || null;
let lastCalledLoket = localStorage.getItem('lastCalledLoket') || null;
let isCurrentlyPlaying = false;

// Fungsi pemanggil
function Suara() {
  $.ajax({
    url: "app/antrian.php?p=panggil",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var nomorAntrian = $("#suara");
      nomorAntrian.empty();

      $.each(data, function (index, item) {
        // Update tampilan nomor yang aktif dipanggil (tampilkan terakhir jika tidak ada yang aktif)
        if (item.nomor !== '0') {
          $("#current-loket").text(item.loket);
          $("#current-number").text(formatQueueNumber(item.loket, item.nomor));
          // Simpan sebagai terakhir dipanggil
          lastCalledNumber = item.nomor;
          lastCalledLoket = item.loket;
          try {
            localStorage.setItem('lastCalledNumber', String(item.nomor));
            localStorage.setItem('lastCalledLoket', item.loket);
          } catch(e) { /* ignore */ }
        } else {
          // Jika tidak ada nomor aktif, tampilkan panggilan terakhir bila ada
          if (lastCalledNumber && lastCalledLoket) {
            $("#current-loket").text(lastCalledLoket);
            $("#current-number").text(formatQueueNumber(lastCalledLoket, lastCalledNumber));
          } else {
            $("#current-loket").text('-');
            $("#current-number").text('000');
          }
        }
        
        // Cek apakah ini nomor baru atau sedang memutar
        const currentNumber = item.nomor;
        const currentLoket = item.loket;
        
        console.log("Data dari server - Nomor: " + currentNumber + ", Loket: " + currentLoket);
        console.log("Status - lastCalledNumber: " + lastCalledNumber + ", lastCalledLoket: " + lastCalledLoket + ", isCurrentlyPlaying: " + isCurrentlyPlaying);
        
        if (currentNumber !== '0' && !isCurrentlyPlaying && 
            (lastCalledNumber !== currentNumber || lastCalledLoket !== currentLoket)) {
          
          console.log("Nomor baru terdeteksi: " + currentNumber + " di " + currentLoket);
          lastCalledNumber = currentNumber;
          lastCalledLoket = currentLoket;
          isCurrentlyPlaying = true;
          
          try {
            // Panggil playNomor untuk pemutaran berurutan
            playNomor(item.nomor, item.loket).then(() => {
              // Setelah selesai memutar, ubah status menjadi 3 (selesai)
              console.log("Pemutaran selesai, mengubah status ke 3 untuk nomor: " + currentNumber);
              $.ajax({
                url: "app/antrian.php?p=selesai&nomor=" + currentNumber,
                type: "GET",
                success: function(response) {
                  console.log("Status berhasil diubah ke 3:", response);
                  isCurrentlyPlaying = false;
                },
                error: function(xhr, status, error) {
                  console.error("Error mengubah status ke 3:", error);
                  isCurrentlyPlaying = false;
                }
              });
            }).catch((error) => {
              console.error("Error saat memutar audio:", error);
              isCurrentlyPlaying = false;
            });
          } catch (error) {
            console.error("Error saat memanggil suara antrian:", error);
            isCurrentlyPlaying = false;
            // Fallback ke responsiveVoice jika gagal
            if (typeof responsiveVoice !== 'undefined' && (window.rvReady === true || window.rvReady === undefined)) {
              console.log("Fallback ke responsiveVoice untuk nomor " + item.nomor);
              
              // Buat teks lebih jelas dengan jeda
              const teksAntrian = "nomor antrian "+item.nomor+", silahkan ke " +item.loket.toLowerCase();
              
              responsiveVoice.speak(
                 teksAntrian,
                 "Indonesian Female",
                 {
                   pitch: 1,
                   rate: 0.9,
                   volume: 1,
                   onend: function() {
                     console.log("Responsivevoice selesai untuk nomor " + currentNumber);
                     // Ubah status menjadi 3 setelah selesai
                     $.ajax({
                       url: "app/antrian.php?p=selesai&nomor=" + currentNumber,
                       type: "GET",
                       success: function(response) {
                         console.log("Status berhasil diubah ke 3 (responsivevoice):", response);
                         isCurrentlyPlaying = false;
                       },
                       error: function(xhr, status, error) {
                         console.error("Error mengubah status ke 3 (responsivevoice):", error);
                         isCurrentlyPlaying = false;
                       }
                     });
                   }
                 }
               );
             }
           }
         } else if (currentNumber === '0') {
          // Tidak ada nomor aktif. Tetap pertahankan tampilan terakhir dipanggil.
          // Jangan reset lastCalled* agar layar besar tetap menampilkan panggilan terakhir.
        }
       });
     },
     error: function(xhr, status, error) {
       console.error("Error mengambil data antrian:", error);
     }
   });
 }

  //=======================================================================

function updateDisplayData() {
  //=======================================================================
  // Update hidden divs for compatibility
  $.ajax({
    url: "app/antrian.php?p=panggil",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var nomorAntrian = $("#nomor");
      nomorAntrian.empty();
      
      $.each(data, function (index, item) {
        var antrian = $(
            "<div class='loket-title'>" + item.loket + "</div><div class='main-number'>" + item.nomor + "</div>"
        );
        nomorAntrian.append(antrian);
        
        // Update tampilan utama dengan data terbaru juga (hanya jika bukan nomor 0)
        if (item.nomor !== '0') {
          $("#current-loket").text(item.loket);
          $("#current-number").text(formatQueueNumber(item.loket, item.nomor));
          // Simpan sebagai terakhir dipanggil
          lastCalledNumber = item.nomor;
          lastCalledLoket = item.loket;
        } else {
          // Jika tidak ada nomor aktif, tampilkan panggilan terakhir bila ada
          if (lastCalledNumber && lastCalledLoket) {
            $("#current-loket").text(lastCalledLoket);
            $("#current-number").text(formatQueueNumber(lastCalledLoket, lastCalledNumber));
          } else {
            $("#current-loket").text('-');
            $("#current-number").text('000');
          }
        }
      });
    },
    error: function() {
      console.log("Gagal memuat data nomor");
    }
  });
  
  //=======================================================================
  // Update loket list di footer
  $.ajax({
    url: "app/antrian.php?p=loket1",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var dummyContainer = $("#datapoli"); // Untuk kompatibilitas
      dummyContainer.empty();
      
      // Hapus semua loket list yang ada
      $("#loket-list").empty();
      
      // Generate array untuk 4 loket, dengan default '---'
      var loketArray = [
        {loket: 'Loket 1', nomor: '---'},
        {loket: 'Loket 2', nomor: '---'},
        {loket: 'Loket 3', nomor: '---'},
        {loket: 'Loket 4', nomor: '---'}
      ];
      
      // Update array dengan data yang ada
      $.each(data, function(index, item) {
        // Ambil nomor loket dari string "LOKET X"
        var loketNumber = item.loket.match(/\d+/);
        if (loketNumber && loketNumber[0] > 0 && loketNumber[0] <= 4) {
          loketArray[loketNumber[0]-1] = {
            loket: 'Loket ' + loketNumber[0],
            nomor: formatQueueNumber('Loket ' + loketNumber[0], item.nomor)
          };
        }
        
        // Untuk kompatibilitas, tambahkan juga ke datapoli
        var varpoli = $("<div class='p-3 border-bottom'>" +
          "<div class='text-primary small mb-1'>" + item.loket + "</div>" +
          "<div class='sidebar-number text-danger'>" + item.nomor + "</div>" +
          "</div>");
        dummyContainer.append(varpoli);
      });
      
      // Render loket list
      $.each(loketArray, function(index, item) {
        var loketItem = $(
          "<div class='loket-item'>" +
            "<div class='loket-name'>" + item.loket + "</div>" +
            "<div class='loket-number'>" + item.nomor + "</div>" +
          "</div>"
        );
        $("#loket-list").append(loketItem);
      });
      
      // Fallback pusat layar: jika panggil selalu 0 dan belum ada lastCalled,
      // gunakan data loket1 untuk menampilkan nomor terakhir yang tersedia.
      try {
        var candidate = null;
        $.each(data, function(index, item) {
          var nomorStr = String(item.nomor || '').trim();
          if (nomorStr !== '' && nomorStr !== '0' && nomorStr !== '-') {
            if (!candidate || Number(item.nomor) > Number(candidate.nomor)) {
              candidate = item;
            }
          }
        });
        if (candidate && (!lastCalledNumber || !lastCalledLoket)) {
          lastCalledNumber = candidate.nomor;
          lastCalledLoket = candidate.loket;
          try {
            localStorage.setItem('lastCalledNumber', String(lastCalledNumber));
            localStorage.setItem('lastCalledLoket', lastCalledLoket);
          } catch(e) { /* ignore */ }
          $("#current-loket").text(lastCalledLoket);
          $("#current-number").text(formatQueueNumber(lastCalledLoket, lastCalledNumber));
          console.log("Fallback dari loket1 -> pusat layar:", lastCalledLoket, lastCalledNumber);
        }
      } catch (e) {
        console.log("Gagal menentukan fallback dari loket1:", e.message);
      }
    },
    error: function() {
      console.log("Gagal memuat data loket");
    }
  });
}

//=========================================================================
// Fungsi untuk memastikan video berjalan
function checkVideoPlayback() {
  try {
    if (videoPlayer && videoPlayer.paused && !videoPlayer.ended) {
      console.log("Video tertunda, mencoba memutar ulang");
      
      // Pastikan video tetap dimute untuk menghindari blocking autoplay
      videoPlayer.muted = true;
      
      // Jika video belum dimuai sama sekali
      if (videoPlayer.currentTime === 0) {
        setInitialVideo();
      }
      
      // Putar dengan promise handling
      const playPromise = videoPlayer.play();
      if (playPromise !== undefined) {
        playPromise.catch(error => {
          console.log("Gagal memutar ulang video:", error);
          
          // Jika mendapat AbortError, coba video lain
          if (error.name === "AbortError" || error.name === "NotAllowedError") {
            console.log("AbortError/NotAllowedError terdeteksi, mengganti video");
            currentVideoIndex = (currentVideoIndex + 1) % videos.length;
            videoPlayer.src = videos[currentVideoIndex];
            
            // Pastikan muted untuk lolos kebijakan autoplay
            videoPlayer.muted = true;
            
            // Beri sedikit delay sebelum mencoba lagi
            setTimeout(() => {
              videoPlayer.play().catch(e => console.log("Masih gagal dengan video baru:", e));
            }, 100);
          }
        });
      }
    }
  } catch (error) {
    console.error("Error dalam checkVideoPlayback:", error);
  }
}

//refresh otomatis setiap 3 detik, bukan 0.75 untuk mengurangi beban server
setInterval(Suara, 3000);
setInterval(updateDisplayData, 5000); // Update display data every 5 seconds

// Interval untuk memeriksa status video secara berkala - 3 detik
let videoCheckInterval;

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  // Panggil fungsi Suara pertama kali
  Suara();
  
  // Update display data
  updateDisplayData();
  
  // Tampilkan dari localStorage jika tersedia (agar tidak kosong saat awal muat)
  if (lastCalledNumber && lastCalledLoket) {
    $("#current-loket").text(lastCalledLoket);
    $("#current-number").text(formatQueueNumber(lastCalledLoket, lastCalledNumber));
    console.log("Init dari localStorage:", lastCalledLoket, lastCalledNumber);
  }
  
  // Mulai interval pemeriksaan video
  videoCheckInterval = setInterval(checkVideoPlayback, 3000);
  
  // Coba putar video pertama kali
  setTimeout(function() {
    if (videoPlayer && videoPlayer.paused) {
      checkVideoPlayback();
    }
  }, 1000);
});

//=======================================================================

//==========membuat jam=============
function updateClock() {
  var currentTime = new Date();
  var hours = currentTime.getHours();
  var minutes = currentTime.getMinutes();
  var seconds = currentTime.getSeconds();

  // Format waktu dengan tambahkan "0" di depan angka jika kurang dari 10
  hours = (hours < 10 ? "0" : "") + hours;
  minutes = (minutes < 10 ? "0" : "") + minutes;
  seconds = (seconds < 10 ? "0" : "") + seconds;

  var timeString = "" + hours + ":" + minutes + ":" + seconds;

  // Update elemen HTML dengan waktu yang telah diformat
  document.getElementById("clock").innerHTML = timeString;
}

// Panggil fungsi updateClock setiap detik
setInterval(updateClock, 1000);

// Fungsi untuk memutar file MP3 secara berurutan
function playNomor(nomor, loket) {
  console.log("=== MULAI PEMANGGILAN NOMOR (AUDIO LOKAL) ===");
  console.log("Nomor:", nomor, "Loket:", loket);

  // Pastikan nomor valid
  if (!nomor || nomor === '0' || nomor === 0) {
    console.error("Nomor tidak valid:", nomor);
    return Promise.resolve();
  }

  // Konversi ke integer untuk memudahkan logika
  const n = parseInt(nomor, 10);
  const audioQueue = [];

  // Helper untuk menambahkan audio ke antrian
  function push(path) {
    const a = new Audio(path);
    a.preload = 'auto';
    a.volume = 1;
    audioQueue.push(a);
  }

  // Helper untuk mengeja angka 1..9999 menggunakan aset lokal
  function speakNumber(num) {
    if (num <= 0) return;
    if (num < 10) {
      push(`assets/nomor/${num}.mp3`);
      return;
    }
    if (num === 10) {
      push('assets/nomor/10.mp3');
      return;
    }
    if (num === 11) {
      push('assets/nomor/sebelas.mp3');
      return;
    }
    if (num < 20) {
      push(`assets/nomor/${num - 10}.mp3`);
      push('assets/nomor/belas.mp3');
      return;
    }
    if (num < 100) {
      const puluh = Math.floor(num / 10);
      const satu = num % 10;
      // Bentuk: <digit> + puluh
      push(`assets/nomor/${puluh}.mp3`);
      push('assets/nomor/puluh.mp3');
      if (satu > 0) push(`assets/nomor/${satu}.mp3`);
      return;
    }
    if (num < 1000) {
      const ratus = Math.floor(num / 100);
      const sisa = num % 100;
      if (ratus === 1) {
        // seratus
        push('assets/nomor/ratus.mp3');
      } else {
        // <digit> ratus
        push(`assets/nomor/${ratus}.mp3`);
        push('assets/nomor/ratus.mp3');
      }
      if (sisa > 0) speakNumber(sisa);
      return;
    }
    if (num < 10000) {
      const ribu = Math.floor(num / 1000);
      const sisa = num % 1000;
      push(`assets/nomor/${ribu}.mp3`);
      push('assets/nomor/ribu.mp3');
      if (sisa > 0) speakNumber(sisa);
      return;
    }
    // Di luar cakupan, tetap coba ucapkan digit per digit
    String(num).split('').forEach(d => push(`assets/nomor/${d}.mp3`));
  }

  // Susun antrian audio sesuai urutan yang diinginkan
  push('assets/notifbell.mp3');
  // Gunakan path yang benar untuk "nomor antrian"
  push('assets/nomor antrian.mp3');
  speakNumber(n);
  push('assets/menuju/Silahkan ke.mp3');

  // Loket audio dinonaktifkan sesuai permintaan, tidak memutar file loket
  // (dulu menentukan file loket dan push ke assets/loket)


  // Log antrian
  console.log('Antrian audio lokal:', audioQueue.map(a => a.src.replace(/^.*\//, '')).join(' -> '));

  // Putar berurutan
  function playAudioSequentially(idx) {
    if (idx >= audioQueue.length) {
      console.log('=== SELESAI MEMUTAR AUDIO LOKAL ===');
      return Promise.resolve();
    }
    return new Promise(resolve => {
      const audio = audioQueue[idx];
      const fileName = audio.src.split('/').pop();
      console.log(`[${idx+1}/${audioQueue.length}] Play: ${fileName}`);

      // Fail-safe timeout (10 detik per file)
      const timeoutId = setTimeout(() => {
        console.warn(`Timeout: ${fileName}`);
        resolve(playAudioSequentially(idx + 1));
      }, 10000);

      audio.onended = function() {
        clearTimeout(timeoutId);
        console.log(`Selesai: ${fileName}`);
        resolve(playAudioSequentially(idx + 1));
      };
      audio.onerror = function(e) {
        clearTimeout(timeoutId);
        console.error(`Error: ${fileName}`, e);
        resolve(playAudioSequentially(idx + 1));
      };

      const p = audio.play();
      if (p && typeof p.catch === 'function') {
        p.catch(err => {
          clearTimeout(timeoutId);
          console.error(`Play() error: ${fileName}`, err);
          resolve(playAudioSequentially(idx + 1));
        });
      }
    });
  }

  // Jangan fallback ke responsiveVoice; gunakan aset lokal saja
  return playAudioSequentially(0);
}
