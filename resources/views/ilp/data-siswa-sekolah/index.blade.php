@extends('adminlte::page')

@section('title', 'Data Siswa Sekolah')

@section('content_header')
<div class="d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Data Siswa Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data Siswa Sekolah</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <p class="text-muted m-0"><i class="fas fa-calendar-day mr-1"></i> {{ date('d F Y') }}</p>
   </div>
</div>
@stop

@section('content')
<div class="preloader">
   <div class="loader">
      <div class="spinner-border text-primary" role="status">
         <span class="sr-only">Loading...</span>
      </div>
      <p class="mt-2">Memuat data...</p>
   </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible animate__animated animate__fadeInDown">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   <h5><i class="icon fas fa-check"></i> Sukses!</h5>
   {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible animate__animated animate__fadeInDown">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   <h5><i class="icon fas fa-ban"></i> Error!</h5>
   {{ session('error') }}
</div>
@endif

<!-- Statistik Cards -->
<div class="row mb-4">
   <div class="col-lg-4 col-6">
      <div class="small-box bg-info">
         <div class="inner">
            <h3>{{ $totalSiswa }}</h3>
            <p>Total Siswa</p>
         </div>
         <div class="icon">
            <i class="fas fa-graduation-cap"></i>
         </div>
      </div>
   </div>
   <div class="col-lg-4 col-6">
      <div class="small-box bg-success">
         <div class="inner">
            <h3>{{ $totalSekolah }}</h3>
            <p>Total Sekolah</p>
         </div>
         <div class="icon">
            <i class="fas fa-school"></i>
         </div>
      </div>
   </div>
   <div class="col-lg-4 col-6">
      <div class="small-box bg-warning">
         <div class="inner">
            <h3>{{ $totalKelas }}</h3>
            <p>Total Kelas</p>
         </div>
         <div class="icon">
            <i class="fas fa-chalkboard"></i>
         </div>
      </div>
   </div>
</div>

<!-- Filter & Search -->
<div class="row mb-3">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h6 class="text-primary mb-0 font-weight-bold"><i class="fas fa-filter mr-2"></i>Filter & Pencarian</h6>
               <div>
                  <a href="{{ route('ilp.data-siswa-sekolah.create') }}" class="btn btn-sm btn-primary mr-2">
                     <i class="fas fa-plus mr-1"></i> Tambah Siswa
                  </a>
                  <button type="button" class="btn btn-sm btn-success mr-2" id="exportExcel">
                     <i class="fas fa-file-excel mr-1"></i> Export Excel
                  </button>
                  <button type="button" class="btn btn-sm btn-danger mr-2" id="exportPdf">
                     <i class="fas fa-file-pdf mr-1"></i> Export PDF
                  </button>
                  <a href="#" class="btn btn-sm btn-outline-secondary" id="resetAllFilters">
                     <i class="fas fa-sync mr-1"></i> Reset Filter
                  </a>
               </div>
            </div>
            <form method="GET" action="{{ route('ilp.data-siswa-sekolah.index') }}" id="filterForm">
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-search mr-1"></i> Cari Siswa</label>
                        <input type="text" class="form-control" name="search" value="{{ $filters['search'] }}" 
                               placeholder="Nama Pasien, No RM, NISN, atau Sekolah...">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-school mr-1"></i> Sekolah</label>
                        <select class="form-control" name="sekolah" id="sekolahFilter">
                           <option value="">Semua Sekolah</option>
                           @foreach($daftarSekolah as $sekolah)
                           <option value="{{ $sekolah->id_sekolah }}" {{ $filters['sekolah'] == $sekolah->id_sekolah ? 'selected' : '' }}>
                              {{ $sekolah->nama_sekolah }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-chalkboard mr-1"></i> Kelas</label>
                        <select class="form-control" name="kelas" id="kelasFilter">
                           <option value="">Semua Kelas</option>
                           @foreach($daftarKelas as $kelas)
                           <option value="{{ $kelas->getKey() }}" {{ $filters['kelas'] == $kelas->getKey() ? 'selected' : '' }}>
                              {{ $kelas->kelas }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-info-circle mr-1"></i> Status</label>
                        <select class="form-control" name="status">
                           <option value="">Semua Status</option>
                           <option value="Aktif" {{ $filters['status'] == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                           <option value="Pindah" {{ $filters['status'] == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                           <option value="Lulus" {{ $filters['status'] == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                           <option value="Drop Out" {{ $filters['status'] == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col-md-12 text-right">
                     <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> Cari
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Data Table -->
<div class="row">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-primary text-white">
            <h3 class="card-title m-0">
               <i class="fas fa-table mr-2"></i>Daftar Siswa Sekolah
            </h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-striped table-hover mb-0">
                  <thead class="bg-light">
                     <tr>
                        <th width="3%">No</th>
                        <th width="7%">No KTP</th>
                        <th width="10%">Nama Siswa</th>
                        <th width="5%">JK</th>
                        <th width="8%">TTL</th>
                        <th width="5%">Umur</th>
                        <th width="10%">Sekolah</th>
                        <th width="6%">Kelas</th>
                        <th width="8%">Nama Ortu</th>
                        <th width="7%">NIK Ortu</th>
                        <th width="8%">No WhatsApp</th>
                        <th width="7%">Disabilitas</th>
                        <th width="6%">Status</th>
                        <th width="10%">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($dataSiswa as $siswa)
                     <tr>
                        <td>{{ $siswa['no'] }}</td>
                        <td><span class="badge badge-info">{{ $siswa['no_ktp'] }}</span></td>
                        <td>
                           <strong>{{ $siswa['nama_siswa'] }}</strong>
                           @if($siswa['nisn'] != '-')
                           <br><small class="text-muted">NISN: {{ $siswa['nisn'] }}</small>
                           @endif
                        </td>
                        <td>
                           @if($siswa['jenis_kelamin'] == 'Laki-laki')
                           <span class="badge badge-primary">L</span>
                           @else
                           <span class="badge badge-pink">P</span>
                           @endif
                        </td>
                        <td><small>{{ $siswa['tempat_tanggal_lahir'] }}</small></td>
                        <td>{{ $siswa['umur'] }}</td>
                        <td>
                           <strong>{{ $siswa['nama_sekolah'] }}</strong>
                           <br><small class="text-muted">{{ $siswa['jenis_sekolah'] }}</small>
                        </td>
                        <td>{{ $siswa['kelas'] }}</td>
                        <td>{{ $siswa['nama_ortu'] }}</td>
                        <td><span class="badge badge-secondary">{{ $siswa['nik_ortu'] }}</span></td>
                        <td>
                           @if($siswa['no_whatsapp'] != '-')
                           <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siswa['no_whatsapp']) }}" target="_blank" class="text-success">
                              <i class="fab fa-whatsapp"></i> {{ $siswa['no_whatsapp'] }}
                           </a>
                           @else
                           <span class="text-muted">-</span>
                           @endif
                        </td>
                        <td>
                           @if($siswa['jenis_disabilitas'] == 'Non Disabilitas')
                           <span class="badge badge-success">{{ $siswa['jenis_disabilitas'] }}</span>
                           @else
                           <span class="badge badge-warning">{{ $siswa['jenis_disabilitas'] }}</span>
                           @endif
                        </td>
                        <td>
                           @if($siswa['status'] == 'Aktif')
                           <span class="badge badge-success">{{ $siswa['status'] }}</span>
                           @elseif($siswa['status'] == 'Lulus')
                           <span class="badge badge-primary">{{ $siswa['status'] }}</span>
                           @elseif($siswa['status'] == 'Pindah')
                           <span class="badge badge-warning">{{ $siswa['status'] }}</span>
                           @else
                           <span class="badge badge-danger">{{ $siswa['status'] }}</span>
                           @endif
                        </td>
                        <td>
                           <div class="btn-group" role="group">
                              <a href="{{ route('ilp.data-siswa-sekolah.show', $siswa['id']) }}" 
                                 class="btn btn-info btn-sm" title="Detail">
                                 <i class="fas fa-eye"></i>
                              </a>
                              <a href="{{ route('ilp.data-siswa-sekolah.edit', $siswa['id']) }}" 
                                 class="btn btn-warning btn-sm" title="Edit">
                                 <i class="fas fa-edit"></i>
                              </a>
                              <button type="button" class="btn btn-danger btn-sm" 
                                      onclick="confirmDelete({{ $siswa['id'] }})" title="Hapus">
                                 <i class="fas fa-trash"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="14" class="text-center py-4">
                           <i class="fas fa-search fa-3x text-muted mb-3"></i>
                           <h5 class="text-muted">Tidak ada data siswa</h5>
                           <p class="text-muted">Silakan tambah data siswa atau ubah filter pencarian</p>
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
         @if($dataSiswa->hasPages())
         <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
               <div class="mb-2 mb-md-0">
                  <small class="text-muted">
                     <i class="fas fa-info-circle mr-1"></i>
                     Menampilkan {{ $dataSiswa->firstItem() }} - {{ $dataSiswa->lastItem() }} 
                     dari {{ number_format($dataSiswa->total()) }} data
                  </small>
               </div>
               <div class="pagination-wrapper">
                  {{ $dataSiswa->appends(request()->query())->links('pagination::bootstrap-4') }}
               </div>
            </div>
         </div>
         @endif
      </div>
   </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header bg-danger text-white">
            <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus data siswa ini?</p>
            <p class="text-danger"><strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!</p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
               @csrf
               @method('DELETE')
               <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<style>
.preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    flex-direction: column;
}

.loader {
    text-align: center;
}

.badge-pink {
    background-color: #e83e8c;
    color: white;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.animate__animated {
    animation-duration: 0.5s;
}

.animate__fadeIn {
    animation-name: fadeIn;
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -100%, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* Pagination Styling */
.pagination-wrapper .pagination {
    margin-bottom: 0;
    justify-content: center;
}

.pagination-wrapper .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    padding: 0.375rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
    transition: all 0.2s ease-in-out;
}

.pagination-wrapper .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #adb5bd;
    transform: translateY(-1px);
}

.pagination-wrapper .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.pagination-wrapper .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .pagination-wrapper .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-wrapper .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin: 1px;
    }
    
    .card-footer .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .card-footer .mb-2 {
        margin-bottom: 1rem !important;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Hide preloader after page load
    $('.preloader').fadeOut();
    
    // Reset filters
    $('#resetAllFilters').click(function(e) {
        e.preventDefault();
        window.location.href = '{{ route("ilp.data-siswa-sekolah.index") }}';
    });
    
    // Auto submit form on filter change
    $('#filterForm select, #filterForm input[name="search"]').change(function() {
        $('#filterForm').submit();
    });
    
    // Search on enter key
    $('#filterForm input[name="search"]').keypress(function(e) {
        if (e.which == 13) {
            $('#filterForm').submit();
        }
    });
    
    // Export Excel with current filters
    $('#exportExcel').click(function(e) {
        e.preventDefault();
        
        // Get current filter values
        var search = $('input[name="search"]').val();
        var sekolah = $('select[name="sekolah"]').val();
        var kelas = $('select[name="kelas"]').val();
        var status = $('select[name="status"]').val();
        
        // Build export URL with parameters
        var exportUrl = '{{ route("ilp.data-siswa-sekolah.export.excel") }}';
        var params = [];
        
        if (search) params.push('search=' + encodeURIComponent(search));
        if (sekolah) params.push('sekolah=' + encodeURIComponent(sekolah));
        if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
        if (status) params.push('status=' + encodeURIComponent(status));
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        // Show loading state
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...');
        $(this).prop('disabled', true);
        
        // Open export URL
        window.location.href = exportUrl;
        
        // Reset button after delay
        setTimeout(function() {
            $('#exportExcel').html(originalText);
            $('#exportExcel').prop('disabled', false);
        }, 2000);
    });
    
    // Export PDF with current filters
    $('#exportPdf').click(function(e) {
        e.preventDefault();
        
        // Get current filter values
        var search = $('input[name="search"]').val();
        var sekolah = $('select[name="sekolah"]').val();
        var kelas = $('select[name="kelas"]').val();
        var status = $('select[name="status"]').val();
        
        // Build export URL with parameters
        var exportUrl = '{{ route("ilp.data-siswa-sekolah.export.pdf") }}';
        var params = [];
        
        if (search) params.push('search=' + encodeURIComponent(search));
        if (sekolah) params.push('sekolah=' + encodeURIComponent(sekolah));
        if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
        if (status) params.push('status=' + encodeURIComponent(status));
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        // Show loading state
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...');
        $(this).prop('disabled', true);
        
        // Open export URL
        window.location.href = exportUrl;
        
        // Reset button after delay
        setTimeout(function() {
            $('#exportPdf').html(originalText);
            $('#exportPdf').prop('disabled', false);
        }, 2000);
    });
    
    // Pagination smooth scroll
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        // Show loading state
        $('.preloader').fadeIn();
        
        // Navigate to the URL
        window.location.href = url;
    });
    
    // Scroll to top when pagination is used
    if (window.location.search.includes('page=')) {
        $('html, body').animate({
            scrollTop: $('.card-header').offset().top - 100
        }, 500);
    }
});

function confirmDelete(id) {
    $('#deleteForm').attr('action', '/ilp/data-siswa-sekolah/' + id);
    $('#deleteModal').modal('show');
}
</script>
@stop