@extends('adminlte::page')

@section('title', 'Pendaftaran Pelayanan CKG')

@section('content_header')
<div class="container-fluid">
   <div class="row mb-2">
      <div class="col-sm-6">
         <h1 class="m-0">Pendaftaran Pelayanan CKG</h1>
      </div>
      <div class="col-sm-6">
         <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item active">Pendaftaran CKG</li>
         </ol>
      </div>
   </div>
</div>

<!-- Modal Detail CKG Sekolah -->
<div class="modal fade" id="detailSekolahModal" tabindex="-1" role="dialog" aria-labelledby="detailSekolahModalLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="detailSekolahModalLabel">Detail CKG Sekolah</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="detail-sekolah-content">
               <!-- Detail sekolah content will be loaded here -->
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-info" id="btn-kunjungan-sehat-sekolah" style="display: none;">
               <i class="fas fa-heart"></i> Jadikan Kunjungan Sehat
            </button>
            <button type="button" class="btn btn-success" id="btn-selesai-sekolah">Selesai</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>
@stop

@section('content')
<!-- Tambahkan CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Tambahkan Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<div class="container-fluid">
   <!-- Filter Card -->
   <div class="card mb-3">
      <div class="card-header">
         <h3 class="card-title">Filter Data</h3>
         <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
               <i class="fas fa-minus"></i>
            </button>
         </div>
      </div>
      <div class="card-body">
         <form id="filter-form" method="GET" action="{{ route('ilp.pendaftaran-ckg') }}">
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Tanggal Skrining Awal:</label>
                     <input type="date" class="form-control" name="tanggal_awal" id="tanggal_awal"
                        value="{{ request('tanggal_awal') }}">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Tanggal Skrining Akhir:</label>
                     <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Status:</label>
                     <select class="form-control" name="status" id="status">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Selesai</option>
                        <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Menunggu</option>
                        <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Usia Sekolah</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Nama Sekolah:</label>
                     <select class="form-control" name="nama_sekolah" id="nama_sekolah">
                        <option value="">Semua Sekolah</option>
                        @foreach($daftar_sekolah as $sekolah)
                        <option value="{{ $sekolah->id_sekolah }}" {{ request('nama_sekolah')==$sekolah->id_sekolah ? 'selected' : '' }}>{{ $sekolah->nama_sekolah }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group">
                     <label>Kelas:</label>
                     <select class="form-control" name="kelas" id="kelas">
                        <option value="">Semua Kelas</option>
                        @foreach($daftar_kelas as $kelas_item)
                        <option value="{{ $kelas_item->id_kelas }}" {{ request('kelas')==$kelas_item->id_kelas ? 'selected' : '' }}>{{ $kelas_item->kelas }}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-9">
                  <div class="form-group">
                     <label>&nbsp;</label>
                     <div class="d-flex">
                        <button type="submit" class="btn btn-primary mr-2">
                           <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('ilp.pendaftaran-ckg') }}" class="btn btn-default">
                           <i class="fas fa-sync"></i> Reset
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>

   <div class="card">
      <div class="card-header">
         <h3 class="card-title">Data Pendaftaran Pelayanan CKG</h3>
      </div>
      <div class="card-body">
         <div class="table-responsive">
            <table id="tabel-pendaftaran-ckg" class="table table-bordered table-striped table-hover w-100">
               <thead>
                  <tr>
                     <th>No.</th>
                     <th>NIK</th>
                     <th>Nama Lengkap</th>
                     <th>Tanggal Lahir</th>
                     <th>Umur</th>
                     <th>Jenis Kelamin</th>
                     <th>No. Handphone</th>
                     <th>No. Peserta BPJS</th>
                     <th>Nama Sekolah</th>
                     <th>Kelas</th>
                     <th>Tanggal Skrining</th>
                     <th>Kunjungan Sehat</th>
                     <th>Status</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($data_pendaftaran as $key => $pendaftaran)
                  <tr>
                     <td data-label="No.">{{ $key + 1 }}</td>
                     <td data-label="NIK">{{ $pendaftaran->nik }}</td>
                     <td data-label="Nama Lengkap">{{ $pendaftaran->nama_lengkap }}</td>
                     <td data-label="Tanggal Lahir">{{ date('d-m-Y', strtotime($pendaftaran->tanggal_lahir)) }}</td>
                     <td data-label="Umur">{{ $pendaftaran->umur }} tahun</td>
                     <td data-label="Jenis Kelamin">{{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                     </td>
                     <td data-label="No. Handphone">{{ $pendaftaran->no_handphone }}</td>
                     <td data-label="No. Peserta BPJS">{{ $pendaftaran->no_peserta ?? '-' }}</td>
                     <td data-label="Nama Sekolah">{{ $pendaftaran->nama_sekolah ?? '-' }}</td>
                     <td data-label="Kelas">{{ $pendaftaran->kelas ?? '-' }}</td>
                     <td data-label="Tanggal Skrining">{{ $pendaftaran->tanggal_skrining ? date('d-m-Y',
                        strtotime($pendaftaran->tanggal_skrining)) : '-' }}</td>
                     <td data-label="Kunjungan Sehat">
                        @if(isset($pendaftaran->kunjungan_sehat) && ($pendaftaran->kunjungan_sehat == '1' ||
                        $pendaftaran->kunjungan_sehat == 1))
                        <span class="badge badge-success">Sudah</span>
                        @else
                        <span class="badge badge-secondary">Belum</span>
                        @endif
                     </td>
                     <td data-label="Status">
                        <span
                           class="badge {{ $pendaftaran->status == '1' ? 'badge-success' : ($pendaftaran->status == '0' ? 'badge-warning' : 'badge-secondary') }}">
                           {{ $pendaftaran->status == '1' ? 'Selesai' : ($pendaftaran->status == '0' ? 'Menunggu' :
                           'Usia Sekolah') }}
                        </span>
                     </td>
                     <td data-label="Aksi">
                        <div class="btn-group" role="group">
                           <button type="button" class="btn btn-info btn-sm detail-btn" data-toggle="modal"
                              data-target="#detailModal" data-id="{{ $pendaftaran->id_pkg }}">
                              <i class="fas fa-eye"></i> Detail
                           </button>
                           <button type="button" class="btn btn-primary btn-sm detail-sekolah-btn" data-toggle="modal"
                              data-target="#detailSekolahModal" data-id="{{ $pendaftaran->id_pkg }}">
                              <i class="fas fa-school"></i> Detail CKG Sekolah
                           </button>
                           <button type="button" class="btn btn-success btn-sm set-status-btn"
                              data-id="{{ $pendaftaran->id_pkg }}" data-status="{{ $pendaftaran->status }}">
                              <i class="fas fa-tasks"></i> Set Status
                           </button>
                           <button type="button" class="btn btn-warning btn-sm kunjungan-sehat-btn"
                              data-id="{{ $pendaftaran->id_pkg }}" data-nokartu="{{ $pendaftaran->no_peserta ?? '' }}"
                              data-nama="{{ $pendaftaran->nama_lengkap }}">
                              <i class="fas fa-heartbeat"></i> Kunjungan Sehat
                           </button>
                        </div>
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">Detail Pendaftaran CKG</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="detail-content">
               <!-- Detail content will be loaded here -->
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-info" id="btn-kunjungan-sehat" style="display: none;">
               <i class="fas fa-heart"></i> Jadikan Kunjungan Sehat
            </button>
            <button type="button" class="btn btn-success" id="btn-selesai">Selesai</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Set Status -->
<div class="modal fade" id="setStatusModal" tabindex="-1" role="dialog" aria-labelledby="setStatusModalLabel"
   aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="setStatusModalLabel">Set Status Pendaftaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="set-status-form">
               <input type="hidden" id="pendaftaran-id" name="id">
               <div class="form-group">
                  <label>Pilih Status:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="status_option" id="status0" value="0">
                     <label class="form-check-label" for="status0">
                        <span class="badge badge-warning">Menunggu</span>
                     </label>
                  </div>
                  <div class="form-check mt-2">
                     <input class="form-check-input" type="radio" name="status_option" id="status1" value="1">
                     <label class="form-check-label" for="status1">
                        <span class="badge badge-success">Selesai</span>
                     </label>
                  </div>
                  <div class="form-check mt-2">
                     <input class="form-check-input" type="radio" name="status_option" id="status2" value="2">
                     <label class="form-check-label" for="status2">
                        <span class="badge badge-secondary">Usia Sekolah</span>
                     </label>
                  </div>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="submit-status">Simpan</button>
         </div>
      </div>
   </div>
</div>
@stop

@section('plugins.Sweetalert2', true)

@section('css')
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
   .modal-backdrop {
      opacity: 0.5 !important;
   }

   .badge {
      font-size: 100%;
   }

   /* Styling untuk button group */
   .btn-group .btn {
      margin-right: 2px;
   }

   .btn-group .btn:last-child {
      margin-right: 0;
   }

   /* Responsive button group */
   @media (max-width: 768px) {
      .btn-group {
         display: flex;
         flex-direction: column;
      }

      .btn-group .btn {
         margin-bottom: 2px;
         margin-right: 0;
      }

      .btn-group .btn:last-child {
         margin-bottom: 0;
      }
   }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
   // Pastikan jQuery dan Bootstrap sudah dimuat sebelum kode ini dijalankan
   if (typeof $ === 'undefined') {
      console.error('jQuery tidak ditemukan!');
   } else {
      console.log('jQuery tersedia:', $.fn.jquery);
   }
   
   if (typeof $.fn.modal === 'undefined') {
      console.error('Bootstrap Modal tidak ditemukan!');
   } else {
      console.log('Bootstrap Modal tersedia');
   }

   $(document).ready(function() {
        // Cetak log untuk debugging
        console.log('Document ready, initializing event handlers');
        
        // Initialize DataTable with state saving
        var table = $('#tabel-pendaftaran-ckg').DataTable({
            "responsive": false,
            "lengthChange": true,
            "autoWidth": false,
            "scrollX": true,
            "stateSave": true,
            "stateDuration": 60 * 60 * 24, // 24 hours
            "language": {
                "emptyTable": "Tidak ada data yang tersedia",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "thousands": ".",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
        
        // Release processing status when Detail CKG Sekolah modal is closed
        $('#detailSekolahModal').on('hidden.bs.modal', function () {
            // Find the currently processing record and release it
            $('.detail-sekolah-btn.currently-processing').each(function() {
                const button = $(this);
                const id = button.data('id');
                
                // Release processing status on server
                $.ajax({
                    url: "{{ route('ilp.ckg.release-processing') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        button.prop('disabled', false);
                        button.removeClass('btn-secondary currently-processing').addClass('btn-primary');
                        button.html('<i class="fas fa-school"></i> Detail CKG Sekolah');
                    },
                    error: function(xhr) {
                        console.error('Error releasing processing status:', xhr);
                    }
                });
            });
        });
        
        // Event handler untuk tombol "Jadikan Kunjungan Sehat" di modal sekolah
        $(document).on('click', '#btn-kunjungan-sehat-sekolah', function() {
            const currentId = $('#detailSekolahModal').data('current-id');
            const noPesertaBpjs = $('#no-peserta-bpjs-sekolah').text().trim();
            const namaLengkap = $('#nama-siswa').text().trim();
            
            console.log('Kunjungan Sehat button clicked. ID:', currentId, 'BPJS:', noPesertaBpjs);
            
            // Validasi nomor BPJS
            if (!noPesertaBpjs || noPesertaBpjs.length !== 13 || noPesertaBpjs === '-') {
                Swal.fire({
                    title: 'Nomor BPJS Tidak Valid',
                    text: 'Nomor peserta BPJS harus 13 digit untuk dapat didaftarkan kunjungan sehat.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Konfirmasi pendaftaran kunjungan sehat
            Swal.fire({
                title: 'Konfirmasi Kunjungan Sehat',
                text: `Daftarkan ${namaLengkap} (${noPesertaBpjs}) untuk kunjungan sehat?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Daftarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    daftarKunjunganSehat(currentId, noPesertaBpjs, namaLengkap);
                }
            });
        });
        
        // Event handler untuk tombol "Selesai" di modal sekolah
        $(document).on('click', '#btn-selesai-sekolah', function() {
            const currentId = $('#detailSekolahModal').data('current-id');
            
            console.log('Selesai button clicked (sekolah). ID:', currentId);
            
            // Konfirmasi sebelum menyimpan
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyelesaikan data ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses',
                        text: 'Menyimpan data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Kirim request untuk update status
                    $.ajax({
                        url: "{{ route('ilp.ckg.update-status') }}",
                        type: "POST",
                        data: {
                            id: currentId,
                            status: '1', // Set status menjadi selesai
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                $('#detailSekolahModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil diselesaikan',
                                    icon: 'success'
                                }).then(() => {
                                    // Save current page state before reload
                                    saveCurrentPageState();
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Gagal menyelesaikan data',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            console.error('Error completing data:', xhr);
                            
                            let errorMessage = 'Terjadi kesalahan saat menyelesaikan data';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 422) {
                                errorMessage = 'Data tidak valid. Silakan periksa kembali.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
        

        

        
        // Function to save current page state
        function saveCurrentPageState() {
            if (table) {
                var pageInfo = table.page.info();
                localStorage.setItem('ckg_current_page', pageInfo.page);
                localStorage.setItem('ckg_page_length', pageInfo.length);
                localStorage.setItem('ckg_search_value', table.search());
            }
        }
        
        // Function to restore page state
        function restorePageState() {
            var savedPage = localStorage.getItem('ckg_current_page');
            var savedLength = localStorage.getItem('ckg_page_length');
            var savedSearch = localStorage.getItem('ckg_search_value');
            
            if (savedPage !== null) {
                table.page(parseInt(savedPage));
            }
            if (savedLength !== null) {
                table.page.len(parseInt(savedLength));
            }
            if (savedSearch !== null) {
                table.search(savedSearch);
            }
            table.draw(false);
        }
        
        // Save state when page changes
        table.on('page.dt', function() {
            saveCurrentPageState();
        });
        
        // Save state when search changes
        table.on('search.dt', function() {
            saveCurrentPageState();
        });
        
        // Save state when page length changes
        table.on('length.dt', function() {
            saveCurrentPageState();
        });
        
        // Function to refresh table data without full page reload
        function refreshTableData() {
            // Save current state
            saveCurrentPageState();
            
            // Get current page info
            var pageInfo = table.page.info();
            var currentPage = pageInfo.page;
            var searchValue = table.search();
            
            // Reload the page but maintain state
            location.reload();
        }
        
        // Restore page state after page load
        setTimeout(function() {
            restorePageState();
        }, 100);
        
        // Check processing status on page load
        function checkProcessingStatus() {
            $.ajax({
                url: "{{ route('ilp.ckg.check-processing-status') }}",
                type: "GET",
                success: function(response) {
                    // Reset semua button ke state normal terlebih dahulu
                    $('.detail-btn').each(function() {
                        const button = $(this);
                        if (!button.hasClass('currently-processing')) {
                            button.prop('disabled', false);
                            button.removeClass('btn-secondary').addClass('btn-info');
                            button.html('<i class="fas fa-eye"></i> Detail');
                        }
                    });
                    
                    // Set button yang sedang diproses
                    if (response.processing_records && response.processing_records.length > 0) {
                        response.processing_records.forEach(function(id) {
                            const button = $(`.detail-btn[data-id="${id}"]`);
                            if (button.length > 0) {
                                button.prop('disabled', true);
                                button.removeClass('btn-info').addClass('btn-secondary');
                                button.html('<i class="fas fa-lock"></i> Sedang Diproses');
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error checking processing status:', xhr);
                }
            });
        }
        
        // Check processing status every 30 seconds
        setInterval(checkProcessingStatus, 30000);
        
        // Initial check
        checkProcessingStatus();
        
        // Detail button click - menggunakan event delegation untuk pagination
        $(document).on('click', '.detail-btn', function() {
            const id = $(this).data('id');
            const button = $(this);
            
            // Check if button is already disabled (being processed)
            if (button.prop('disabled')) {
                Swal.fire({
                    title: 'Maaf!',
                    text: 'Data sedang diproses oleh petugas lain',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Set processing status on server
            $.ajax({
                url: "{{ route('ilp.ckg.set-processing') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Disable button and change appearance
                        button.prop('disabled', true);
                        button.removeClass('btn-info').addClass('btn-secondary');
                        button.addClass('currently-processing');
                        button.html('<i class="fas fa-lock"></i> Sedang Diproses');
                        
                        // Load detail content
                        $.ajax({
                            url: "{{ route('ilp.ckg.detail') }}",
                            type: "GET",
                            data: {
                                id: id
                            },
                            beforeSend: function() {
                                $('#detail-content').html(`
                                    <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Sedang memuat data pasien...</p>
                                            <small class="text-muted">Mohon tunggu sebentar</small>
                                        </div>
                                    </div>
                                `);
                            },
                            success: function(response) {
                                $('#detail-content').html(response);
                                // Store current ID in modal data for Selesai button
                                $('#detailModal').data('current-id', id);
                                
                                // Show modal first
                                $('#detailModal').modal('show');
                                
                                // Check if BPJS number exists and show/hide kunjungan sehat button
                                setTimeout(function() {
                                    const noPesertaBpjs = $('#no-peserta-bpjs').text().trim();
                                    if (noPesertaBpjs && noPesertaBpjs.length === 13 && noPesertaBpjs !== '-') {
                                        $('#btn-kunjungan-sehat').show();
                                    } else {
                                        $('#btn-kunjungan-sehat').hide();
                                    }
                                }, 200);
                            },
                            error: function(xhr) {
                                // Release processing status on error
                                $.ajax({
                                    url: "{{ route('ilp.ckg.release-processing') }}",
                                    type: "POST",
                                    data: {
                                        id: id,
                                        _token: "{{ csrf_token() }}"
                                    }
                                });
                                
                                button.prop('disabled', false);
                                button.removeClass('btn-secondary currently-processing').addClass('btn-info');
                                button.html('<i class="fas fa-eye"></i> Detail');
                                
                                let errorMessage = 'Terjadi kesalahan saat mengambil data';
                                let errorTitle = 'Error!';
                                let suggestion = '';
                                
                                if (xhr.status === 404 && xhr.responseJSON) {
                                    errorTitle = xhr.responseJSON.error || 'Data Tidak Ditemukan';
                                    errorMessage = xhr.responseJSON.message || errorMessage;
                                    suggestion = xhr.responseJSON.suggestion || '';
                                } else if (xhr.status === 500) {
                                    errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.';
                                } else if (xhr.status === 400) {
                                    errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                                }
                                
                                let alertText = errorMessage;
                                if (suggestion) {
                                    alertText += '\n\n' + suggestion;
                                }
                                
                                Swal.fire({
                                    title: errorTitle,
                                    text: alertText,
                                    icon: xhr.status === 404 ? 'warning' : 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Maaf!',
                            text: response.message || 'Data sedang diproses oleh petugas lain',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengakses data';
                    let errorTitle = 'Error!';
                    let suggestion = '';
                    
                    if (xhr.status === 404 && xhr.responseJSON) {
                        errorTitle = xhr.responseJSON.error || 'Data Tidak Ditemukan';
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        suggestion = xhr.responseJSON.suggestion || '';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.';
                    } else if (xhr.status === 400) {
                        errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                    }
                    
                    let alertText = errorMessage;
                    if (suggestion) {
                        alertText += '\n\n' + suggestion;
                    }
                    
                    Swal.fire({
                        title: errorTitle,
                        text: alertText,
                        icon: xhr.status === 404 ? 'warning' : 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
        
        // Release processing status when modal is closed
        $('#detailModal').on('hidden.bs.modal', function () {
            // Find the currently processing record and release it
            $('.detail-btn.currently-processing').each(function() {
                const button = $(this);
                const id = button.data('id');
                
                // Release processing status on server
                $.ajax({
                    url: "{{ route('ilp.ckg.release-processing') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        button.prop('disabled', false);
                        button.removeClass('btn-secondary currently-processing').addClass('btn-info');
                        button.html('<i class="fas fa-eye"></i> Detail');
                    },
                    error: function(xhr) {
                        console.error('Error releasing processing status:', xhr);
                    }
                });
            });
        });

        // Detail CKG Sekolah button click - menggunakan event delegation untuk pagination
        $(document).on('click', '.detail-sekolah-btn', function() {
            const id = $(this).data('id');
            const button = $(this);
            
            // Check if button is already disabled (being processed)
            if (button.prop('disabled')) {
                Swal.fire({
                    title: 'Maaf!',
                    text: 'Data sedang diproses oleh petugas lain',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Set processing status on server
            $.ajax({
                url: "{{ route('ilp.ckg.set-processing') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Disable button and change appearance
                        button.prop('disabled', true);
                        button.removeClass('btn-primary').addClass('btn-secondary');
                        button.addClass('currently-processing');
                        button.html('<i class="fas fa-lock"></i> Sedang Diproses');
                        
                        // Load detail sekolah content
                        $.ajax({
                            url: "{{ route('ilp.ckg.detail-sekolah') }}",
                            type: "GET",
                            data: {
                                id: id
                            },
                beforeSend: function() {
                    $('#detail-sekolah-content').html(`
                        <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Sedang memuat data siswa...</p>
                                <small class="text-muted">Mohon tunggu sebentar</small>
                            </div>
                        </div>
                    `);
                },
                success: function(response) {
                    $('#detail-sekolah-content').html(response);
                    $('#detailSekolahModal').data('current-id', id);
                    $('#detailSekolahModal').modal('show');
                    
                    // Check if BPJS number exists and show/hide kunjungan sehat button
                    setTimeout(function() {
                        const noPesertaBpjs = $('#no-peserta-bpjs-sekolah').text().trim();
                        if (noPesertaBpjs && noPesertaBpjs.length === 13 && noPesertaBpjs !== '-') {
                            $('#btn-kunjungan-sehat-sekolah').show();
                        } else {
                            $('#btn-kunjungan-sehat-sekolah').hide();
                        }
                    }, 200);
                            },
                            error: function(xhr) {
                                // Release processing status on error
                                $.ajax({
                                    url: "{{ route('ilp.ckg.release-processing') }}",
                                    type: "POST",
                                    data: {
                                        id: id,
                                        _token: "{{ csrf_token() }}"
                                    }
                                });
                                
                                let errorMessage = 'Terjadi kesalahan saat mengambil data sekolah';
                                let errorTitle = 'Error!';
                                let suggestion = '';
                                let iconType = 'error';
                                
                                if ((xhr.status === 404 || xhr.status === 400) && xhr.responseJSON) {
                                    errorTitle = xhr.responseJSON.error || 'Data Tidak Ditemukan';
                                    errorMessage = xhr.responseJSON.message || errorMessage;
                                    suggestion = xhr.responseJSON.suggestion || '';
                                    
                                    // Untuk status 400 (bukan data siswa sekolah), gunakan icon info
                                    if (xhr.status === 400) {
                                        iconType = 'info';
                                    } else {
                                        iconType = 'warning';
                                    }
                                }
                                
                                let alertText = errorMessage;
                                if (suggestion) {
                                    alertText += '\n\n' + suggestion;
                                }
                                
                                Swal.fire({
                                    title: errorTitle,
                                    text: alertText,
                                    icon: iconType,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Gagal mengatur status processing',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengatur status processing',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Set status button click - menggunakan event delegation untuk pagination
        $(document).on('click', '.set-status-btn', function() {
            const id = $(this).data('id');
            const currentStatus = $(this).data('status');
            
            console.log('Set status clicked for ID:', id, 'Current status:', currentStatus);
            
            // Isi hidden input dengan ID pendaftaran
            $('#pendaftaran-id').val(id);
            
            // Set radio button sesuai status saat ini
            $(`#status${currentStatus}`).prop('checked', true);
            
            // Tampilkan modal set status
            $('#setStatusModal').modal('show');
        });
        
        // Handle submit status - menggunakan event delegation untuk modal yang dibuat dinamis
        // Submit status event handler dengan fitur kunjungan sehat
        $(document).on('click', '#submit-status', function() {
            const id = $('#pendaftaran-id').val();
            const newStatus = $('input[name="status_option"]:checked').val();
            
            console.log('Submit status clicked. ID:', id, 'New status:', newStatus);
            
            if (!newStatus) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Silakan pilih status terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }
            
            // Jika status yang dipilih adalah selesai (1), tanyakan apakah ingin mendaftarkan kunjungan sehat
            if (newStatus === '1') {
                const noKartu = $('#no-peserta-bpjs').text().trim();
                const nama = $('#nama-lengkap').text().trim();
                
                console.log('Checking kunjungan sehat conditions:', {
                    noKartu: noKartu,
                    nama: nama,
                    noKartuLength: noKartu ? noKartu.length : 0,
                    isValidCard: noKartu && noKartu !== '-' && noKartu.length === 13
                });
                
                if (noKartu && noKartu !== '-' && noKartu.length === 13) {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda juga ingin mendaftarkan kunjungan sehat untuk pasien ini?',
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonColor: '#28a745',
                        denyButtonColor: '#17a2b8',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, dengan Kunjungan Sehat',
                        denyButtonText: 'Tidak, Simpan Saja',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Daftarkan kunjungan sehat terlebih dahulu
                            daftarKunjunganSehat(id, noKartu, nama, function(success) {
                                if (success) {
                                    // Jika berhasil, update status dengan kunjungan_sehat = 1
                                    updateStatus(id, newStatus, 1);
                                }
                            });
                        } else if (result.isDenied) {
                            // Simpan tanpa kunjungan sehat
                            updateStatus(id, newStatus, 0);
                        }
                    });
                } else {
                    // Jika tidak ada nomor kartu BPJS yang valid, langsung simpan
                    updateStatus(id, newStatus, 0);
                }
            } else {
                // Jika bukan status selesai, langsung simpan
                updateStatus(id, newStatus, 0);
            }
        });
        
        // Function untuk mendaftarkan kunjungan sehat
    function daftarKunjunganSehat(id, noKartu, nama, callback) {
        if (!noKartu || noKartu.length !== 13) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Nomor kartu BPJS tidak valid (harus 13 digit)',
                confirmButtonColor: '#3085d6'
            });
            if (callback) callback(false);
            return;
        }
        
        // Tampilkan loading
        Swal.fire({
            title: 'Memproses',
            text: 'Mendaftarkan kunjungan sehat...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Kirim request ke server untuk mendaftarkan kunjungan sehat
        $.ajax({
            url: '/api/pcare/pendaftaran',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                noKartu: noKartu.toString(),
                tglDaftar: moment().format('DD-MM-YYYY'),
                kdPoli: '021',
                kunjSakit: false,
                keluhan: 'Konsultasi Kesehatan',
                sistole: 0,
                diastole: 0,
                beratBadan: 0,
                tinggiBadan: 0,
                respRate: 0,
                heartRate: 0,
                lingkarPerut: 0,
                rujukBalik: 0,
                kdTkp: '10',
                kdSadar: '01'
            }),
            success: function(response) {
                Swal.close();
                if (response.metaData && (response.metaData.code === 200 || response.metaData.code === 201)) {
                    // Update kunjungan_sehat di database lokal
                    $.ajax({
                        url: "{{ route('ilp.ckg.update-status') }}",
                        type: "POST",
                        data: {
                            id: id,
                            kunjungan_sehat: 1,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(updateResponse) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                confirmButtonColor: '#3085d6'
                            });
                            if (callback) callback(true);
                        },
                        error: function(xhr) {
                            console.error('Error updating kunjungan_sehat:', xhr);
                            // Tetap tampilkan sukses karena pendaftaran BPJS berhasil
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil') + '. Namun gagal update status lokal.',
                                confirmButtonColor: '#3085d6'
                            });
                            if (callback) callback(true);
                        }
                    });
                } else {
                    let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                    if (response.metaData && response.metaData.message) {
                        errorMessage = response.metaData.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6'
                    });
                    if (callback) callback(false);
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                
                if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                    errorMessage = xhr.responseJSON.metaData.message;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 400) {
                    errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage,
                    confirmButtonColor: '#3085d6'
                });
                if (callback) callback(false);
            }
        });
    }
    
    // Function untuk mendaftarkan kunjungan sehat (backup)
    function daftarKunjunganSehatBackup(id, noKartu, nama, callback) {
            if (!noKartu || noKartu.length !== 13) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Nomor kartu BPJS tidak valid (harus 13 digit)',
                    confirmButtonColor: '#3085d6'
                });
                if (callback) callback(false);
                return;
            }
            
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses',
                text: 'Mendaftarkan kunjungan sehat...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim request ke server untuk mendaftarkan kunjungan sehat
            $.ajax({
                url: '/api/pcare/pendaftaran',
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    noKartu: noKartu.toString(),
                    tglDaftar: moment().format('DD-MM-YYYY'),
                    kdPoli: '021',
                    kunjSakit: false,
                    keluhan: 'Konsultasi Kesehatan',
                    sistole: 0,
                    diastole: 0,
                    beratBadan: 0,
                    tinggiBadan: 0,
                    respRate: 0,
                    heartRate: 0,
                    lingkarPerut: 0,
                    rujukBalik: 0,
                    kdTkp: '10',
                    kdSadar: '01'
                }),
                success: function(response) {
                    Swal.close();
                    if (response.metaData && (response.metaData.code === 200 || response.metaData.code === 201)) {
                        // Cek status saat ini terlebih dahulu
                        $.ajax({
                            url: "{{ route('ilp.ckg.detail') }}",
                            type: "GET",
                            data: { id: id },
                            success: function(detailResponse) {
                                // Parse response untuk mendapatkan status
                                const tempDiv = $('<div>').html(detailResponse);
                                const statusBadge = tempDiv.find('.badge-success, .badge-warning, .badge-secondary');
                                const isCompleted = statusBadge.hasClass('badge-success');
                                
                                if (isCompleted) {
                                    // Jika sudah selesai, hanya update kunjungan_sehat
                                    $.ajax({
                                        url: "{{ route('ilp.ckg.update-status') }}",
                                        type: "POST",
                                        data: {
                                            id: id,
                                            status: '1', // Status tetap selesai
                                            kunjungan_sehat: 1, // Update kunjungan_sehat
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(updateResponse) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                                confirmButtonColor: '#3085d6'
                                            });
                                            if (callback) callback(true);
                                        },
                                        error: function(xhr) {
                                            console.error('Error updating kunjungan_sehat:', xhr);
                                            // Tetap tampilkan sukses karena pendaftaran BPJS berhasil
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil') + '. Namun gagal update status lokal.',
                                                confirmButtonColor: '#3085d6'
                                            });
                                            if (callback) callback(true);
                                        }
                                    });
                                } else {
                                    // Jika belum selesai, update status dan kunjungan_sehat
                                    $.ajax({
                                        url: "{{ route('ilp.ckg.update-status') }}",
                                        type: "POST",
                                        data: {
                                            id: id,
                                            status: '1', // Set status menjadi selesai
                                            kunjungan_sehat: 1,
                                            _token: "{{ csrf_token() }}"
                                        },
                                        success: function(updateResponse) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                                confirmButtonColor: '#3085d6'
                                            });
                                            if (callback) callback(true);
                                        },
                                        error: function(xhr) {
                                            console.error('Error updating status:', xhr);
                                            // Tetap tampilkan sukses karena pendaftaran BPJS berhasil
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil') + '. Namun gagal update status lokal.',
                                                confirmButtonColor: '#3085d6'
                                            });
                                            if (callback) callback(true);
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error checking status:', xhr);
                                // Fallback: coba update dengan asumsi belum selesai
                                $.ajax({
                                    url: "{{ route('ilp.ckg.update-status') }}",
                                    type: "POST",
                                    data: {
                                        id: id,
                                        status: '1',
                                        kunjungan_sehat: 1,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(updateResponse) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                            confirmButtonColor: '#3085d6'
                                        });
                                        if (callback) callback(true);
                                    },
                                    error: function(xhr) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                            confirmButtonColor: '#3085d6'
                                        });
                                        if (callback) callback(true);
                                    }
                                });
                            }
                        });
                    } else {
                        let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                        if (response.metaData && response.metaData.message) {
                            errorMessage = response.metaData.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage,
                            confirmButtonColor: '#3085d6'
                        });
                        if (callback) callback(false);
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                    
                    if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                        errorMessage = xhr.responseJSON.metaData.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 400) {
                        errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6'
                    });
                    if (callback) callback(false);
                }
            });
        }
        
        // Function untuk update status
        function updateStatus(id, status, kunjunganSehat) {
            // Prepare data to send
            const dataToSend = {
                id: id,
                status: status,
                _token: "{{ csrf_token() }}"
            };
            
            // Add kunjungan_sehat if applicable
            if (kunjunganSehat === 1) {
                dataToSend.kunjungan_sehat = 1;
            }
            
            $.ajax({
                url: "{{ route('ilp.ckg.update-status') }}",
                type: "POST",
                data: dataToSend,
                success: function(response) {
                    if (response.success) {
                        $('#setStatusModal').modal('hide');
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berhasil diperbarui',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Gagal memperbarui status',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error updating status:', xhr);
                    
                    let errorMessage = 'Terjadi kesalahan saat memperbarui status';
                    let errorTitle = 'Error!';
                    let suggestion = '';
                    
                    if (xhr.status === 404 && xhr.responseJSON) {
                        errorTitle = xhr.responseJSON.error || 'Data Tidak Ditemukan';
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        suggestion = xhr.responseJSON.suggestion || '';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Data tidak valid. Silakan periksa kembali.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.';
                    } else if (xhr.status === 400) {
                        errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                    }
                    
                    let alertText = errorMessage;
                    if (suggestion) {
                        alertText += '\n\n' + suggestion;
                    }
                    
                    Swal.fire({
                        title: errorTitle,
                        text: alertText,
                        icon: xhr.status === 404 ? 'warning' : 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        }
        
                // Debugging - cek apakah modal set status ada di DOM
        console.log('Modal set status exists:', $('#setStatusModal').length > 0);
        
        // Reset modal ketika disembunyikan
        $('#setStatusModal').on('hidden.bs.modal', function () {
            console.log('Modal hidden, resetting form');
            $('#set-status-form')[0].reset();
        });
        
        // Validasi input tanggal
        $('#tanggal_awal').on('change', function() {
            const tanggalAwal = $(this).val();
            const tanggalAkhir = $('#tanggal_akhir').val();
            
            if(tanggalAwal && tanggalAkhir && new Date(tanggalAwal) > new Date(tanggalAkhir)) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir',
                    icon: 'warning'
                });
                $(this).val('');
            }
        });
        
        $('#tanggal_akhir').on('change', function() {
            const tanggalAwal = $('#tanggal_awal').val();
            const tanggalAkhir = $(this).val();
            
            if(tanggalAwal && tanggalAkhir && new Date(tanggalAwal) > new Date(tanggalAkhir)) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal',
                    icon: 'warning'
                });
                $(this).val('');
            }
        });
        
        // Kunjungan sehat button click - menggunakan event delegation untuk pagination
        $(document).on('click', '.kunjungan-sehat-btn', function() {
            const id = $(this).data('id');
            const noKartu = $(this).data('nokartu');
            const nama = $(this).data('nama');
            
            if (!noKartu || noKartu.length !== 13) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Nomor kartu BPJS tidak valid (harus 13 digit)',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            console.log('Kunjungan sehat clicked for ID:', id, 'NIK:', noKartu, 'Nama:', nama);
            
            // Konfirmasi pendaftaran kunjungan sehat
            Swal.fire({
                title: 'Daftar Kunjungan Sehat',
                text: `Apakah Anda yakin ingin mendaftarkan kunjungan sehat untuk ${nama} (NIK: ${noKartu})?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Daftarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses',
                        text: 'Mendaftarkan kunjungan sehat...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Kirim request ke server untuk mendaftarkan kunjungan sehat
                    $.ajax({
                        url: '/api/pcare/pendaftaran',
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            noKartu: noKartu.toString(),
                            tglDaftar: moment().format('DD-MM-YYYY'),
                            kdPoli: '021',
                            kunjSakit: false,
                            keluhan: 'Konsultasi Kesehatan',
                            sistole: 0,
                            diastole: 0,
                            beratBadan: 0,
                            tinggiBadan: 0,
                            respRate: 0,
                            heartRate: 0,
                            lingkarPerut: 0,
                            rujukBalik: 0,
                            kdTkp: '10',
                            kdSadar: '01'
                        }),
                        success: function(response) {
                            Swal.close();
                            if (response.metaData && (response.metaData.code === 200 || response.metaData.code === 201)) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + (response.response ? response.response.message : 'Berhasil'),
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    // Cek status saat ini terlebih dahulu
                                    $.ajax({
                                        url: "{{ route('ilp.ckg.detail') }}",
                                        type: "GET",
                                        data: { id: id },
                                        success: function(detailResponse) {
                                            // Parse response untuk mendapatkan status
                                            const tempDiv = $('<div>').html(detailResponse);
                                            const statusBadge = tempDiv.find('.badge-success, .badge-warning, .badge-secondary');
                                            const isCompleted = statusBadge.hasClass('badge-success');
                                            
                                            if (isCompleted) {
                                                // Jika sudah selesai, hanya update kunjungan_sehat
                                                $.ajax({
                                                    url: "{{ route('ilp.ckg.update-status') }}",
                                                    type: "POST",
                                                    data: {
                                                        id: id,
                                                        status: '1', // Status tetap selesai
                                                        kunjungan_sehat: 1, // Update kunjungan_sehat
                                                        _token: "{{ csrf_token() }}"
                                                    },
                                                    success: function(updateResponse) {
                                                        if (updateResponse.success) {
                                                            // Save current page state before reload
                                                            saveCurrentPageState();
                                                            location.reload();
                                                        } else {
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Berhasil Daftar BPJS',
                                                                text: 'Kunjungan sehat berhasil didaftarkan di BPJS, namun gagal update status lokal: ' + (updateResponse.message || 'Unknown error'),
                                                                confirmButtonColor: '#3085d6'
                                                            });
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        console.error('Error updating kunjungan_sehat:', xhr);
                                                        let errorMessage = 'Kunjungan sehat berhasil didaftarkan di BPJS, namun gagal update status lokal.';
                                                        
                                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                                            errorMessage += ' Error: ' + xhr.responseJSON.message;
                                                        }
                                                        
                                                        Swal.fire({
                                                            icon: 'warning',
                                                            title: 'Berhasil Daftar BPJS',
                                                            text: errorMessage,
                                                            confirmButtonColor: '#3085d6'
                                                        });
                                                    }
                                                });
                                            } else {
                                                // Jika belum selesai, update status dan kunjungan_sehat
                                                $.ajax({
                                                    url: "{{ route('ilp.ckg.update-status') }}",
                                                    type: "POST",
                                                    data: {
                                                        id: id,
                                                        status: '1', // Set status menjadi selesai
                                                        kunjungan_sehat: 1,
                                                        _token: "{{ csrf_token() }}"
                                                    },
                                                    success: function(updateResponse) {
                                                        if (updateResponse.success) {
                                                            // Save current page state before reload
                                                            saveCurrentPageState();
                                                            location.reload();
                                                        } else {
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Berhasil Daftar BPJS',
                                                                text: 'Kunjungan sehat berhasil didaftarkan di BPJS, namun gagal update status lokal: ' + (updateResponse.message || 'Unknown error'),
                                                                confirmButtonColor: '#3085d6'
                                                            });
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        console.error('Error updating status:', xhr);
                                                        let errorMessage = 'Kunjungan sehat berhasil didaftarkan di BPJS, namun gagal update status lokal.';
                                                        
                                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                                            errorMessage += ' Error: ' + xhr.responseJSON.message;
                                                        }
                                                        
                                                        Swal.fire({
                                                            icon: 'warning',
                                                            title: 'Berhasil Daftar BPJS',
                                                            text: errorMessage,
                                                            confirmButtonColor: '#3085d6'
                                                        });
                                                    }
                                                });
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('Error checking status:', xhr);
                                            // Fallback: coba update dengan asumsi belum selesai
                                            
                                            $.ajax({
                                                url: "{{ route('ilp.ckg.update-status') }}",
                                                type: "POST",
                                                data: {
                                                    id: id,
                                                    status: '1',
                                                    kunjungan_sehat: 1,
                                                    _token: "{{ csrf_token() }}"
                                                },
                                                success: function(updateResponse) {
                                                    if (updateResponse.success) {
                                                        saveCurrentPageState();
                                                        location.reload();
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'warning',
                                                            title: 'Berhasil Daftar BPJS',
                                                            text: 'Kunjungan sehat berhasil didaftarkan di BPJS.',
                                                            confirmButtonColor: '#3085d6'
                                                        });
                                                    }
                                                },
                                                error: function(xhr) {
                                                    Swal.fire({
                                                        icon: 'warning',
                                                        title: 'Berhasil Daftar BPJS',
                                                        text: 'Kunjungan sehat berhasil didaftarkan di BPJS.',
                                                        confirmButtonColor: '#3085d6'
                                                    });
                                                }
                                            });
                                        }
                                    });
                                });
                            } else {
                                let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                                if (response.metaData && response.metaData.message) {
                                    errorMessage = response.metaData.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                            
                            console.log('Error response:', xhr.responseJSON);
                            console.log('Status:', xhr.status);
                            console.log('Status text:', xhr.statusText);
                            
                            if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                                errorMessage = xhr.responseJSON.metaData.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 400) {
                                errorMessage = 'Permintaan tidak valid. Periksa data yang dikirim.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMessage,
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });
        
        // Handle Jadikan Kunjungan Sehat button click
        $(document).on('click', '#btn-kunjungan-sehat', function() {
            const noPesertaBpjs = $('#no-peserta-bpjs').text().trim();
            const namaLengkap = $('#nama-lengkap').text().trim();
            const currentId = $('#detailModal').data('current-id');
            
            if (!noPesertaBpjs || noPesertaBpjs.length !== 13) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Nomor peserta BPJS tidak valid atau tidak ditemukan.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Konfirmasi pendaftaran kunjungan sehat
            Swal.fire({
                title: 'Konfirmasi Kunjungan Sehat',
                text: `Daftarkan ${namaLengkap} (${noPesertaBpjs}) untuk kunjungan sehat?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Daftarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    daftarKunjunganSehat(currentId, noPesertaBpjs, namaLengkap);
                }
            });
        });
        
        // Handle Selesai button click
        $(document).on('click', '#btn-selesai', function() {
            const currentId = $('#detailModal').data('current-id');
            
            console.log('Selesai button clicked. ID:', currentId);
            
            // Konfirmasi sebelum menyimpan
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyelesaikan data ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses',
                        text: 'Menyimpan data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Kirim request untuk update status dan petugas entry
                    $.ajax({
                        url: "{{ route('ilp.ckg.update-status') }}",
                        type: "POST",
                        data: {
                            id: currentId,
                            status: '1', // Set status menjadi selesai
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.success) {
                                $('#detailModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil diselesaikan',
                                    icon: 'success'
                                }).then(() => {
                                    // Save current page state before reload
                                    saveCurrentPageState();
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Gagal menyelesaikan data',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            console.error('Error completing data:', xhr);
                            
                            let errorMessage = 'Terjadi kesalahan saat menyelesaikan data';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 422) {
                                errorMessage = 'Data tidak valid. Silakan periksa kembali.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi.';
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@stop