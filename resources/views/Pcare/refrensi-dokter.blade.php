@extends('adminlte::page')

@section('title', 'Referensi Dokter PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-user-md text-primary"></i> Referensi Dokter PCare</h1>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Referensi Dokter PCare</li>
   </ol>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card card-primary card-outline">
         <div class="card-header">
            <h3 class="card-title">Data Jadwal Praktek Dokter</h3>
         </div>
         <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-4">
               <div class="col-md-8">
                  <form id="filter-form" class="form-inline">
                     <div class="form-group mr-2">
                        <label for="kodePoli" class="mr-2">Poli:</label>
                        <select class="form-control" id="kodePoli" name="kodePoli" required>
                           <option value="">Pilih Poli</option>
                           <!-- Opsi poli akan diisi melalui AJAX -->
                        </select>
                     </div>
                     <div class="form-group mr-2">
                        <label for="tanggal" class="mr-2">Tanggal:</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}"
                           required>
                     </div>
                     <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
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
               <table id="tabel-dokter" class="table table-bordered table-striped">
                  <thead>
                     <tr>
                        <th>No</th>
                        <th>Nama Dokter</th>
                        <th>Kode Dokter</th>
                        <th>Jam Praktek</th>
                        <th>Kapasitas</th>
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
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
   $(function() {
   // Load data poli saat halaman dimuat
   $.ajax({
      url: '/api/pcare/ref/poli',
      method: 'GET',
      success: function(response) {
         if (response.success) {
            const poliSelect = $('#kodePoli');
            response.data.forEach(function(poli) {
               poliSelect.append(new Option(poli.nmPoli + ' (' + poli.kdPoli + ')', poli.kdPoli));
            });
         }
      },
      error: function(xhr) {
         Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data poli'
         });
      }
   });

   // Initialize DataTable
   const table = $('#tabel-dokter').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      ajax: {
         url: '/api/pcare/ref/dokter',
         data: function(d) {
            return {
               kodePoli: $('#kodePoli').val() || '',
               tanggal: moment($('#tanggal').val()).format('DD-MM-YYYY')
            };
         },
         error: function(xhr, error, code) {
            console.error('DataTable AJAX Error:', xhr, error, code);
            let errorMessage = 'Gagal memuat data.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
               errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
               icon: 'error',
               title: 'Error',
               text: errorMessage
            });
         }
      },
      columns: [
         { 
            data: null,
            render: function (data, type, row, meta) {
               return meta.row + meta.settings._iDisplayStart + 1;
            }
         },
         { data: 'namadokter' },
         { data: 'kodedokter' },
         { data: 'jampraktek' },
         { data: 'kapasitas' }
      ],
      order: [[1, 'asc']]
   });

   // Filter form submission
   $('#filter-form').on('submit', function(e) {
      e.preventDefault();
      if (!$('#kodePoli').val()) {
         Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih Poli terlebih dahulu'
         });
         return;
      }
      table.ajax.reload();
   });

   // Reset filter
   $('#reset-filter').on('click', function() {
      $('#kodePoli').val('');
      $('#tanggal').val(moment().format('YYYY-MM-DD'));
      table.ajax.reload();
   });

   // Export Excel
   $('#export-excel').on('click', function() {
      const kodePoli = $('#kodePoli').val();
      const tanggal = moment($('#tanggal').val()).format('DD-MM-YYYY');
      if (!kodePoli) {
         Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih Poli terlebih dahulu'
         });
         return;
      }
      window.location.href = `/api/pcare/ref/dokter/export/excel?kodePoli=${kodePoli}&tanggal=${tanggal}`;
   });

   // Export PDF
   $('#export-pdf').on('click', function() {
      const kodePoli = $('#kodePoli').val();
      const tanggal = moment($('#tanggal').val()).format('DD-MM-YYYY');
      if (!kodePoli) {
         Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih Poli terlebih dahulu'
         });
         return;
      }
      window.location.href = `/api/pcare/ref/dokter/export/pdf?kodePoli=${kodePoli}&tanggal=${tanggal}`;
   });
});
</script>
@stop