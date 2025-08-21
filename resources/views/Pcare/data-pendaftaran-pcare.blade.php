@extends('adminlte::page')

@section('title', 'Data Pendaftaran PCare BPJS')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-clipboard-list text-primary"></i> Data Pendaftaran PCare BPJS</h1>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Data Pendaftaran PCare</li>
   </ol>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card card-primary card-outline">
         <div class="card-header">
            <h3 class="card-title">Data Pendaftaran PCare</h3>
            <div class="card-tools">
               <a href="{{ route('pcare.form-pendaftaran') }}" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus-circle"></i> Tambah Pendaftaran
               </a>
            </div>
         </div>
         <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-4">
               <div class="col-md-8">
                  <form id="filter-form" class="form-inline">
                     <div class="form-group mr-2">
                        <label for="tanggal" class="mr-2">Tanggal:</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}">
                     </div>
                     <div class="form-group mr-2">
                        <label for="status" class="mr-2">Status:</label>
                        <select class="form-control" id="status" name="status">
                           <option value="">Semua</option>
                           <option value="Terkirim">Terkirim</option>
                           <option value="Batal">Batal</option>
                        </select>
                     </div>
                     <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                     </button>
                     <button type="button" id="reset-filter" class="btn btn-secondary ml-2">
                        <i class="fas fa-sync"></i> Reset
                     </button>
                  </form>
               </div>
               <div class="col-md-4 text-right">
                  <div class="btn-group">
                     <button type="button" id="export-excel" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                     </button>
                     <button type="button" id="export-pdf" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                     </button>
                  </div>
               </div>
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
               <table id="tabel-pcare-pendaftaran" class="table table-bordered table-striped">
                  <thead>
                     <tr>
                        <th>No</th>
                        <th>Tanggal Daftar</th>
                        <th>No. Rawat</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>No. Kartu BPJS</th>
                        <th>Poli</th>
                        <th>No. Urut</th>
                        <th>Status</th>
                        <th>Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <!-- Data will be loaded via AJAX -->
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal Detail Pendaftaran -->
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail-label"
   aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modal-detail-label">Detail Pendaftaran PCare</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-6">
                  <h5 class="border-bottom pb-2">Data Pasien</h5>
                  <table class="table table-sm table-borderless">
                     <tr>
                        <td width="40%">No. Rawat</td>
                        <td width="60%" id="detail-no-rawat">-</td>
                     </tr>
                     <tr>
                        <td>No. RM</td>
                        <td id="detail-no-rm">-</td>
                     </tr>
                     <tr>
                        <td>Nama Pasien</td>
                        <td id="detail-nama-pasien">-</td>
                     </tr>
                     <tr>
                        <td>No. Kartu BPJS</td>
                        <td id="detail-no-kartu">-</td>
                     </tr>
                     <tr>
                        <td>Provider Peserta</td>
                        <td id="detail-provider">-</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <h5 class="border-bottom pb-2">Informasi Kunjungan</h5>
                  <table class="table table-sm table-borderless">
                     <tr>
                        <td width="40%">Tanggal Daftar</td>
                        <td width="60%" id="detail-tgl-daftar">-</td>
                     </tr>
                     <tr>
                        <td>Poli</td>
                        <td id="detail-poli">-</td>
                     </tr>
                     <tr>
                        <td>Tempat Kunjungan</td>
                        <td id="detail-tkp">-</td>
                     </tr>
                     <tr>
                        <td>Kunjungan Sakit</td>
                        <td id="detail-kunj-sakit">-</td>
                     </tr>
                     <tr>
                        <td>No. Urut</td>
                        <td id="detail-no-urut">-</td>
                     </tr>
                     <tr>
                        <td>Status</td>
                        <td id="detail-status">-</td>
                     </tr>
                  </table>
               </div>
            </div>
            <div class="row mt-3">
               <div class="col-md-12">
                  <h5 class="border-bottom pb-2">Pemeriksaan</h5>
                  <div class="row">
                     <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                           <tr>
                              <td width="40%">Keluhan</td>
                              <td width="60%" id="detail-keluhan">-</td>
                           </tr>
                           <tr>
                              <td>Tinggi Badan</td>
                              <td id="detail-tinggi">-</td>
                           </tr>
                           <tr>
                              <td>Berat Badan</td>
                              <td id="detail-berat">-</td>
                           </tr>
                        </table>
                     </div>
                     <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                           <tr>
                              <td width="40%">Tekanan Darah</td>
                              <td width="60%" id="detail-tekanan-darah">-</td>
                           </tr>
                           <tr>
                              <td>Respiratory Rate</td>
                              <td id="detail-resp-rate">-</td>
                           </tr>
                           <tr>
                              <td>Heart Rate</td>
                              <td id="detail-heart-rate">-</td>
                           </tr>
                           <tr>
                              <td>Lingkar Perut</td>
                              <td id="detail-lingkar-perut">-</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-danger" id="btn-hapus-detail">Hapus Pendaftaran</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modal-confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Konfirmasi Hapus Pendaftaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p>Anda yakin ingin menghapus pendaftaran ini dari PCare?</p>
            <p>Data yang sudah dihapus tidak dapat dikembalikan.</p>
            <form id="form-delete">
               <input type="hidden" id="delete-no-kartu" name="noKartu">
               <input type="hidden" id="delete-tgl-daftar" name="tglDaftar">
               <input type="hidden" id="delete-no-urut" name="noUrut">
               <input type="hidden" id="delete-kd-poli" name="kdPoli">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger" id="btn-confirm-delete">Hapus</button>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/extensions/responsive/css/responsive.dataTables.min.css') }}">
<style>
   .badge-terkirim {
      background-color: #28a745;
      color: white;
   }

   .badge-batal {
      background-color: #dc3545;
      color: white;
   }
</style>
@stop

@section('js')
<script src="{{ asset('epasien/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
   $(function() {
      // Initialize DataTable
      const table = $('#tabel-pcare-pendaftaran').DataTable({
         processing: true,
         serverSide: true,
         responsive: true,
         ajax: {
            url: '/api/pcare/pendaftaran/data',
            data: function(d) {
               d.tanggal = $('#tanggal').val();
               d.status = $('#status').val();
            }
         },
         columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { 
               data: 'tglDaftar', 
               name: 'tglDaftar',
               render: function(data) {
                  // Format tanggal dari YYYY-MM-DD menjadi DD-MM-YYYY
                  const parts = data.split('-');
                  return parts[2] + '-' + parts[1] + '-' + parts[0];
               }
            },
            { data: 'no_rawat', name: 'no_rawat' },
            { data: 'no_rkm_medis', name: 'no_rkm_medis' },
            { data: 'nm_pasien', name: 'nm_pasien' },
            { data: 'noKartu', name: 'noKartu' },
            { 
               data: 'nmPoli', 
               name: 'nmPoli',
               render: function(data, type, row) {
                  return data + ' (' + row.kdPoli + ')';
               }
            },
            { data: 'noUrut', name: 'noUrut' },
            { 
               data: 'status', 
               name: 'status',
               render: function(data) {
                  let badge = '';
                  if (data === 'Terkirim') {
                     badge = '<span class="badge badge-terkirim">Terkirim</span>';
                  } else if (data === 'Batal') {
                     badge = '<span class="badge badge-batal">Batal</span>';
                  } else {
                     badge = '<span class="badge badge-secondary">' + data + '</span>';
                  }
                  return badge;
               }
            },
            {
               data: 'action',
               name: 'action',
               orderable: false,
               searchable: false,
               render: function(data, type, row) {
                  return `
                     <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info btn-detail" data-id="${row.no_rawat}">
                           <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${row.no_rawat}" 
                           data-nokartu="${row.noKartu}" data-tgldaftar="${row.tglDaftar_formatted}" 
                           data-nourut="${row.noUrut}" data-kdpoli="${row.kdPoli}">
                           <i class="fas fa-trash"></i>
                        </button>
                     </div>
                  `;
               }
            }
         ],
         order: [[1, 'desc']]
      });

      // Filter form submission
      $('#filter-form').on('submit', function(e) {
         e.preventDefault();
         table.ajax.reload();
      });

      // Reset filter
      $('#reset-filter').on('click', function() {
         $('#tanggal').val(moment().format('YYYY-MM-DD'));
         $('#status').val('');
         table.ajax.reload();
      });

      // Handle detail button click
      $(document).on('click', '.btn-detail', function() {
         const noRawat = $(this).data('id');
         
         // Show loading
         Swal.fire({
            title: 'Memuat Data',
            html: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
               Swal.showLoading();
            }
         });
         
         // Fetch detail data
         $.ajax({
            url: `/api/pcare/pendaftaran/detail/${noRawat}`,
            method: 'GET',
            success: function(response) {
               Swal.close();
               
               if (response.success) {
                  const data = response.data;
                  
                  // Format tanggal dari YYYY-MM-DD menjadi DD-MM-YYYY
                  const tglParts = data.tglDaftar.split('-');
                  const tglDaftar = tglParts[2] + '-' + tglParts[1] + '-' + tglParts[0];
                  
                  // Set data to modal
                  $('#detail-no-rawat').text(data.no_rawat);
                  $('#detail-no-rm').text(data.no_rkm_medis);
                  $('#detail-nama-pasien').text(data.nm_pasien);
                  $('#detail-no-kartu').text(data.noKartu);
                  $('#detail-provider').text(data.kdProviderPeserta);
                  $('#detail-tgl-daftar').text(tglDaftar);
                  $('#detail-poli').text(`${data.nmPoli} (${data.kdPoli})`);
                  
                  // Set TKP
                  let tkpText = '';
                  switch(data.kdTkp) {
                     case '10': tkpText = 'Rawat Jalan (RJTP)'; break;
                     case '20': tkpText = 'Rawat Inap (RITP)'; break;
                     case '50': tkpText = 'Promotif Preventif'; break;
                     default: tkpText = data.kdTkp;
                  }
                  $('#detail-tkp').text(tkpText);
                  
                  // Set kunjungan sakit
                  const kunjSakit = data.kunjSakit === "true" ? "Ya" : "Tidak";
                  $('#detail-kunj-sakit').text(kunjSakit);
                  
                  $('#detail-no-urut').text(data.noUrut);
                  $('#detail-status').text(data.status);
                  $('#detail-keluhan').text(data.keluhan || '-');
                  $('#detail-tinggi').text(data.tinggiBadan + ' cm');
                  $('#detail-berat').text(data.beratBadan + ' kg');
                  $('#detail-tekanan-darah').text(data.sistole + '/' + data.diastole + ' mmHg');
                  $('#detail-resp-rate').text(data.respRate + ' /menit');
                  $('#detail-heart-rate').text(data.heartRate + ' /menit');
                  $('#detail-lingkar-perut').text(data.lingkar_perut + ' cm');
                  
                  // Set hapus button data
                  $('#btn-hapus-detail').data('nokartu', data.noKartu);
                  $('#btn-hapus-detail').data('tgldaftar', tglDaftar);
                  $('#btn-hapus-detail').data('nourut', data.noUrut);
                  $('#btn-hapus-detail').data('kdpoli', data.kdPoli);
                  
                  // Show modal
                  $('#modal-detail').modal('show');
               } else {
                  Swal.fire({
                     icon: 'error',
                     title: 'Gagal',
                     text: response.message || 'Terjadi kesalahan saat memuat data'
                  });
               }
            },
            error: function(xhr) {
               Swal.close();
               Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Terjadi kesalahan saat memuat data'
               });
            }
         });
      });
      
      // Handle delete button click from detail modal
      $('#btn-hapus-detail').on('click', function() {
         const noKartu = $(this).data('nokartu');
         const tglDaftar = $(this).data('tgldaftar');
         const noUrut = $(this).data('nourut');
         const kdPoli = $(this).data('kdpoli');
         
         // Set data to confirmation modal
         $('#delete-no-kartu').val(noKartu);
         $('#delete-tgl-daftar').val(tglDaftar);
         $('#delete-no-urut').val(noUrut);
         $('#delete-kd-poli').val(kdPoli);
         
         // Close detail modal and show confirmation modal
         $('#modal-detail').modal('hide');
         $('#modal-confirm-delete').modal('show');
      });
      
      // Handle delete button click from table
      $(document).on('click', '.btn-delete', function() {
         const noKartu = $(this).data('nokartu');
         const tglDaftar = $(this).data('tgldaftar');
         const noUrut = $(this).data('nourut');
         const kdPoli = $(this).data('kdpoli');
         
         // Set data to confirmation modal
         $('#delete-no-kartu').val(noKartu);
         $('#delete-tgl-daftar').val(tglDaftar);
         $('#delete-no-urut').val(noUrut);
         $('#delete-kd-poli').val(kdPoli);
         
         // Show confirmation modal
         $('#modal-confirm-delete').modal('show');
      });
      
      // Handle confirm delete button click
      $('#btn-confirm-delete').on('click', function() {
         const noKartu = $('#delete-no-kartu').val();
         const tglDaftar = $('#delete-tgl-daftar').val();
         const noUrut = $('#delete-no-urut').val();
         const kdPoli = $('#delete-kd-poli').val();
         
         // Show loading
         $(this).attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
         
         // Send delete request
         $.ajax({
            url: `/api/pcare/pendaftaran/peserta/${noKartu}/tglDaftar/${tglDaftar}/noUrut/${noUrut}/kdPoli/${kdPoli}`,
            method: 'DELETE',
            success: function(response) {
               $('#btn-confirm-delete').attr('disabled', false).html('Hapus');
               $('#modal-confirm-delete').modal('hide');
               
               if (response.metaData && response.metaData.code === 200) {
                  Swal.fire({
                     icon: 'success',
                     title: 'Berhasil',
                     text: 'Pendaftaran PCare berhasil dihapus'
                  }).then(() => {
                     table.ajax.reload();
                  });
               } else {
                  // Tampilkan pesan error dari API
                  let errorMsg = 'Terjadi kesalahan saat menghapus data';
                  if (response.metaData && response.metaData.message) {
                     errorMsg = response.metaData.message;
                  }
                  
                  Swal.fire({
                     icon: 'error',
                     title: 'Gagal',
                     text: errorMsg
                  });
               }
            },
            error: function(xhr) {
               $('#btn-confirm-delete').attr('disabled', false).html('Hapus');
               $('#modal-confirm-delete').modal('hide');
               
               let errorMsg = 'Terjadi kesalahan saat menghapus data';
               if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                  errorMsg = xhr.responseJSON.metaData.message;
               }
               
               Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: errorMsg
               });
            }
         });
      });
      
      // Export Excel
      $('#export-excel').on('click', function() {
         const tanggal = $('#tanggal').val();
         const status = $('#status').val();
         
         window.location.href = `/api/pcare/pendaftaran/export/excel?tanggal=${tanggal}&status=${status}`;
      });
      
      // Export PDF
      $('#export-pdf').on('click', function() {
         const tanggal = $('#tanggal').val();
         const status = $('#status').val();
         
         window.location.href = `/api/pcare/pendaftaran/export/pdf?tanggal=${tanggal}&status=${status}`;
      });
   });
</script>
@stop