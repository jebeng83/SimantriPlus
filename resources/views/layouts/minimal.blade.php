<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>@yield('title')</title>

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

   <!-- SweetAlert2 -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">

   <!-- Custom CSS -->
   @yield('css')
</head>

<body>
   <div class="container-fluid py-4">
      @yield('content')
   </div>

   <!-- jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <!-- Bootstrap JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>

   <!-- Custom JS -->
   @yield('js')

   <script>
      $(document).ready(function() {
         console.log('jQuery loaded and document ready');
         
         // Debug: Log semua modals yang ada di DOM
         $('.modal').each(function() {
            console.log('Modal terdeteksi di DOM: #' + $(this).attr('id'));
         });
         
         // Debug: Log semua tombol modal
         $('[data-toggle="modal"]').each(function() {
            console.log('Tombol modal terdeteksi: target=' + $(this).data('target'));
         });
         
         // Menambahkan listener tambahan untuk tombol modal
         $('[data-toggle="modal"]').on('click', function() {
            console.log('Tombol modal diklik: target=' + $(this).data('target'));
         });

         // Mengaktifkan tampilan pertanyaan hamil saat jenis kelamin dipilih perempuan
         $('#jenis_kelamin').on('change', function() {
            if ($(this).val() == 'P') {
               $('#pertanyaan_hamil').show();
            } else {
               $('#pertanyaan_hamil').hide();
            }
         });

         // Mengaktifkan tampilan pertanyaan jumlah rokok
         $('input[name="status_merokok"]').on('change', function() {
            if ($(this).val() == 'merokok_aktif' || $(this).val() == 'pernah_merokok') {
               $('#divJumlahRokok').show();
            } else {
               $('#divJumlahRokok').hide();
            }
         });
         
         // Toggle collapse pada bagian skrining mandiri
         $('[data-toggle="collapse"]').on('click', function() {
            var targetCollapse = $(this).attr('href');
            var icon = $(this).find('i');
            
            if ($(targetCollapse).hasClass('show')) {
               icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            } else {
               icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            }
         });
         
         // Menangani event terbukanya modal
         $('.modal').on('shown.bs.modal', function() {
            console.log('Modal terbuka: #' + $(this).attr('id'));
         });
         
         // Fungsi pencarian data pasien
         function cariPasienByNIK() {
            var nik = $('#nik').val();
            if (nik.length > 0) {
               $.ajax({
                  url: "{{ route('pasien.get-by-nik') }}",
                  type: "GET",
                  data: {
                     nik: nik
                  },
                  dataType: "json",
                  beforeSend: function() {
                     // Tampilkan loading
                     Swal.fire({
                        title: 'Mencari data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                           Swal.showLoading();
                        }
                     });
                  },
                  success: function(response) {
                     // Tutup loading
                     Swal.close();
                     
                     if (response.status == 'success') {
                        var pasien = response.data;
                        $('#nama_lengkap').val(pasien.nm_pasien);
                        $('#tanggal_lahir').val(pasien.tgl_lahir);
                        $('#jenis_kelamin').val(pasien.jk).trigger('change');
                        $('#no_telepon').val(pasien.no_tlp);
                        
                        // Status perkawinan
                        var statusNikah = pasien.stts_nikah.toUpperCase();
                        if (statusNikah == 'MENIKAH') {
                           $('#status_perkawinan').val('Menikah');
                        } else if (statusNikah == 'BELUM MENIKAH') {
                           $('#status_perkawinan').val('Belum Menikah');
                        } else if (statusNikah == 'JANDA') {
                           $('#status_perkawinan').val('Cerai Mati');
                        } else if (statusNikah == 'DUDA') {
                           $('#status_perkawinan').val('Cerai Hidup');
                        }
                        
                        // Menampilkan notifikasi
                        Swal.fire({
                           icon: 'success',
                           title: 'Data ditemukan',
                           text: 'Data pasien berhasil ditemukan',
                           timer: 1500,
                           showConfirmButton: false
                        });
                     } else {
                        // Menampilkan notifikasi
                        Swal.fire({
                           icon: 'info',
                           title: 'Data tidak ditemukan',
                           text: 'Data pasien tidak ditemukan, silahkan isi form secara manual',
                           timer: 1500,
                           showConfirmButton: false
                        });
                     }
                  },
                  error: function(xhr, status, error) {
                     // Tutup loading
                     Swal.close();
                     
                     // Tampilkan pesan error
                     Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal menghubungi server: ' + error,
                        timer: 1500,
                        showConfirmButton: false
                     });
                  }
               });
            }
         }
         
         // Event saat tombol cari NIK diklik
         $('#cari-nik').on('click', function() {
            cariPasienByNIK();
         });
         
         // Event saat enter di input NIK
         $('#nik').on('keypress', function(e) {
            if (e.which == 13) {
               e.preventDefault();
               cariPasienByNIK();
            }
         });
         
         // Perhitungan IMT
         $('#berat_badan, #tinggi_badan').on('input', function() {
            var beratBadan = parseFloat($('#berat_badan').val()) || 0;
            var tinggiBadan = parseFloat($('#tinggi_badan').val()) || 0;
            
            if (beratBadan > 0 && tinggiBadan > 0) {
               var tinggiBadanMeter = tinggiBadan / 100;
               var imt = beratBadan / (tinggiBadanMeter * tinggiBadanMeter);
               
               $('#imt').val(imt.toFixed(2));
               
               var kategori = '';
               if (imt < 18.5) {
                  kategori = 'Berat badan kurang';
               } else if (imt >= 18.5 && imt < 25) {
                  kategori = 'Berat badan normal';
               } else if (imt >= 25 && imt < 30) {
                  kategori = 'Berat badan berlebih (pre-obesitas)';
               } else if (imt >= 30) {
                  kategori = 'Obesitas';
               }
               
               $('#kategoriBMI').text(kategori);
            }
         });
         
         // Interpretasi tekanan darah
         $('#tekanan_sistolik, #tekanan_diastolik').on('input', function() {
            var sistolik = parseInt($('#tekanan_sistolik').val()) || 0;
            var diastolik = parseInt($('#tekanan_diastolik').val()) || 0;
            
            if (sistolik > 0 && diastolik > 0) {
               var interpretasi = '';
               
               if (sistolik < 120 && diastolik < 80) {
                  interpretasi = 'Normal';
               } else if ((sistolik >= 120 && sistolik <= 139) || (diastolik >= 80 && diastolik <= 89)) {
                  interpretasi = 'Prehipertensi';
               } else if ((sistolik >= 140 && sistolik <= 159) || (diastolik >= 90 && diastolik <= 99)) {
                  interpretasi = 'Hipertensi derajat 1';
               } else if (sistolik >= 160 || diastolik >= 100) {
                  interpretasi = 'Hipertensi derajat 2';
               }
               
               $('#interpretasi_td').val(interpretasi);
            }
         });
      });
   </script>
</body>

</html>