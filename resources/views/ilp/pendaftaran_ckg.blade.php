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
@stop

@section('content')
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
            <table id="tabel-pendaftaran-ckg" class="table table-bordered table-striped">
               <thead>
                  <tr>
                     <th>No.</th>
                     <th>NIK</th>
                     <th>Nama Lengkap</th>
                     <th>Tanggal Lahir</th>
                     <th>Umur</th>
                     <th>Jenis Kelamin</th>
                     <th>No. Handphone</th>
                     <th>Tanggal Skrining</th>
                     <th>Status</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($data_pendaftaran as $key => $pendaftaran)
                  <tr>
                     <td>{{ $key + 1 }}</td>
                     <td>{{ $pendaftaran->nik }}</td>
                     <td>{{ $pendaftaran->nama_lengkap }}</td>
                     <td>{{ date('d-m-Y', strtotime($pendaftaran->tanggal_lahir)) }}</td>
                     <td>{{ $pendaftaran->umur }} tahun</td>
                     <td>{{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                     <td>{{ $pendaftaran->no_handphone }}</td>
                     <td>{{ $pendaftaran->tanggal_skrining ? date('d-m-Y', strtotime($pendaftaran->tanggal_skrining)) :
                        '-' }}</td>
                     <td>
                        <span
                           class="badge {{ $pendaftaran->status == '1' ? 'badge-success' : ($pendaftaran->status == '0' ? 'badge-warning' : 'badge-secondary') }}">
                           {{ $pendaftaran->status == '1' ? 'Selesai' : ($pendaftaran->status == '0' ? 'Menunggu' :
                           'Usia Sekolah') }}
                        </span>
                     </td>
                     <td>
                        <button type="button" class="btn btn-info btn-sm detail-btn" data-toggle="modal"
                           data-target="#detailModal" data-id="{{ $pendaftaran->id_pkg }}">
                           <i class="fas fa-eye"></i> Detail
                        </button>
                        <button type="button" class="btn btn-success btn-sm set-status-btn"
                           data-id="{{ $pendaftaran->id_pkg }}" data-status="{{ $pendaftaran->status }}">
                           <i class="fas fa-tasks"></i> Set Status
                        </button>
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

@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<style>
   .modal-backdrop {
      opacity: 0.5 !important;
   }

   .badge {
      font-size: 100%;
   }
</style>
@stop

@section('js')
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
        
        // Initialize DataTable
        $('#tabel-pendaftaran-ckg').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
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

        // Detail button click - menggunakan event delegation untuk pagination
        $(document).on('click', '.detail-btn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('ilp.ckg.detail') }}",
                type: "GET",
                data: {
                    id: id
                },
                beforeSend: function() {
                    $('#detail-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Memuat data...</p></div>');
                },
                success: function(response) {
                    $('#detail-content').html(response);
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengambil data',
                        icon: 'error'
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
            
            $.ajax({
                url: "{{ route('ilp.ckg.update-status') }}",
                type: "POST",
                data: {
                    id: id,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#setStatusModal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Status berhasil diperbarui',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.error('Error updating status:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memperbarui status',
                        icon: 'error'
                    });
                }
            });
        });
        
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
    });
</script>
@stop