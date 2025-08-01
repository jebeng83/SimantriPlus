@extends('adminlte::page')

@section('title', 'Dashboard Sekolah')

@section('content_header')
<div class="d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Dashboard Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard Sekolah</li>
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
      <p class="mt-2">Memuat dashboard...</p>
   </div>
</div>

<!-- Filter Section -->
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h6 class="text-primary mb-0 font-weight-bold"><i class="fas fa-filter mr-2"></i>Filter Dashboard</h6>
               <div>
                  <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-sm btn-outline-primary mr-2">
                     <i class="fas fa-table mr-1"></i> Lihat Data Siswa
                  </a>
                  <button type="button" class="btn btn-sm btn-success mr-2" id="exportExcel">
                     <i class="fas fa-file-excel mr-1"></i> Export Excel
                  </button>
                  <button type="button" class="btn btn-sm btn-danger mr-2" id="exportPdf">
                     <i class="fas fa-file-pdf mr-1"></i> Export PDF
                  </button>
                  <a href="{{ route('ilp.dashboard-sekolah') }}" class="btn btn-sm btn-outline-secondary" id="resetFilter">
                     <i class="fas fa-sync mr-1"></i> Reset Filter
                  </a>
               </div>
            </div>
            <form method="GET" action="{{ route('ilp.dashboard-sekolah') }}" id="filterForm">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-school mr-1"></i> Sekolah</label>
                        <select class="form-control" name="sekolah" id="sekolahFilter">
                           <option value="">Semua Sekolah</option>
                           @foreach($daftarSekolah as $sekolah)
                           <option value="{{ $sekolah->id_sekolah }}" {{ $sekolahId == $sekolah->id_sekolah ? 'selected' : '' }}>
                              {{ $sekolah->nama_sekolah }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-graduation-cap mr-1"></i> Jenis Sekolah</label>
                        <select class="form-control" name="jenis_sekolah" id="jenisSekolahFilter">
                           <option value="">Semua Jenis</option>
                           @foreach($daftarJenisSekolah as $jenis)
                           <option value="{{ $jenis->id }}" {{ $jenisSekolahId == $jenis->id ? 'selected' : '' }}>
                              {{ $jenis->nama }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-chalkboard mr-1"></i> Kelas</label>
                        <select class="form-control" name="kelas" id="kelasFilter">
                           <option value="">Semua Kelas</option>
                           @foreach($daftarKelas as $kelas)
                           <option value="{{ $kelas->id_kelas }}" {{ $kelasId == $kelas->id_kelas ? 'selected' : '' }}>
                              {{ $kelas->kelas }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col-md-12 text-right">
                     <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> Filter
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
   <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
         <div class="inner">
            <h3>{{ number_format($totalSiswa) }}</h3>
            <p>Total Siswa</p>
         </div>
         <div class="icon">
            <i class="fas fa-users"></i>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-6">
      <div class="small-box bg-primary">
         <div class="inner">
            <h3>{{ number_format($siswaLakiLaki) }}</h3>
            <p>Siswa Laki-laki</p>
         </div>
         <div class="icon">
            <i class="fas fa-male"></i>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-6">
      <div class="small-box bg-pink">
         <div class="inner">
            <h3>{{ number_format($siswaPerempuan) }}</h3>
            <p>Siswa Perempuan</p>
         </div>
         <div class="icon">
            <i class="fas fa-female"></i>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
         <div class="inner">
            <h3>{{ number_format($siswaAktif) }}</h3>
            <p>Siswa Aktif</p>
         </div>
         <div class="icon">
            <i class="fas fa-check-circle"></i>
         </div>
      </div>
   </div>
</div>

<!-- Status & Disability Statistics -->
<div class="row mb-4">
   <div class="col-md-6">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title m-0">
               <i class="fas fa-chart-pie mr-2"></i>Status Siswa
            </h3>
         </div>
         <div class="card-body">
            <div style="height: 250px; position: relative;">
               <canvas id="statusChart"></canvas>
            </div>
            <div class="mt-3">
               <div class="row text-center">
                  <div class="col-3">
                     <div class="description-block">
                        <span class="description-percentage text-success">{{ $siswaAktif }}</span>
                        <h5 class="description-header">Aktif</h5>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="description-block">
                        <span class="description-percentage text-warning">{{ $siswaPindah }}</span>
                        <h5 class="description-header">Pindah</h5>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="description-block">
                        <span class="description-percentage text-primary">{{ $siswaLulus }}</span>
                        <h5 class="description-header">Lulus</h5>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="description-block">
                        <span class="description-percentage text-danger">{{ $siswaDropOut }}</span>
                        <h5 class="description-header">Drop Out</h5>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-6">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-info text-white">
            <h3 class="card-title m-0">
               <i class="fas fa-chart-bar mr-2"></i>Distribusi Umur
            </h3>
         </div>
         <div class="card-body">
            <div style="height: 300px; position: relative;">
               <canvas id="ageChart"></canvas>
            </div>
            <div class="mt-3">
               @foreach($distribusiUmur as $umur)
               <div class="progress-group">
                  {{ $umur->kelompok_umur }}
                  <span class="float-right"><b>{{ $umur->jumlah }}</b>/{{ $totalSiswa }}</span>
                  <div class="progress progress-sm">
                     <div class="progress-bar bg-info" style="width: {{ $totalSiswa > 0 ? ($umur->jumlah / $totalSiswa) * 100 : 0 }}%"></div>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
</div>

<!-- School Statistics Table -->
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-success text-white">
            <h3 class="card-title m-0">
               <i class="fas fa-school mr-2"></i>Statistik Per Sekolah
            </h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-striped table-hover mb-0">
                  <thead class="bg-light">
                     <tr>
                        <th>No</th>
                        <th>Nama Sekolah</th>
                        <th>Jenis Sekolah</th>
                        <th>Total Siswa</th>
                        <th>Laki-laki</th>
                        <th>Perempuan</th>
                        <th>Siswa Aktif</th>
                        <th>Disabilitas</th>
                        <th>Persentase</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($statistikSekolah as $index => $sekolah)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                           <strong>{{ $sekolah->nama_sekolah }}</strong>
                        </td>
                        <td>
                           <span class="badge badge-info">{{ $sekolah->nama }}</span>
                        </td>
                        <td>
                           <span class="badge badge-primary">{{ number_format($sekolah->total_siswa) }}</span>
                        </td>
                        <td>{{ number_format($sekolah->siswa_laki) }}</td>
                        <td>{{ number_format($sekolah->siswa_perempuan) }}</td>
                        <td>
                           <span class="badge badge-success">{{ number_format($sekolah->siswa_aktif) }}</span>
                        </td>
                        <td>
                           @if($sekolah->siswa_disabilitas > 0)
                           <span class="badge badge-warning">{{ number_format($sekolah->siswa_disabilitas) }}</span>
                           @else
                           <span class="text-muted">0</span>
                           @endif
                        </td>
                        <td>
                           <div class="progress progress-xs">
                              <div class="progress-bar bg-success" style="width: {{ $totalSiswa > 0 ? ($sekolah->total_siswa / $totalSiswa) * 100 : 0 }}%"></div>
                           </div>
                           <span class="badge badge-light">{{ $totalSiswa > 0 ? number_format(($sekolah->total_siswa / $totalSiswa) * 100, 1) : 0 }}%</span>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="9" class="text-center py-4">
                           <i class="fas fa-school fa-3x text-muted mb-3"></i>
                           <h5 class="text-muted">Tidak ada data sekolah</h5>
                           <p class="text-muted">Silakan ubah filter untuk melihat data</p>
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Class Statistics Table -->
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-warning text-white">
            <h3 class="card-title m-0">
               <i class="fas fa-chalkboard mr-2"></i>Statistik Per Kelas
            </h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-striped table-hover mb-0">
                  <thead class="bg-light">
                     <tr>
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Sekolah</th>
                        <th>Total Siswa</th>
                        <th>Laki-laki</th>
                        <th>Perempuan</th>
                        <th>Grafik</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($statistikKelas as $index => $kelas)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                           <span class="badge badge-primary">{{ $kelas->kelas }}</span>
                        </td>
                        <td>
                           <strong>{{ $kelas->nama_sekolah }}</strong>
                        </td>
                        <td>
                           <span class="badge badge-info">{{ number_format($kelas->total_siswa) }}</span>
                        </td>
                        <td>{{ number_format($kelas->siswa_laki) }}</td>
                        <td>{{ number_format($kelas->siswa_perempuan) }}</td>
                        <td>
                           <div class="progress progress-xs">
                              <div class="progress-bar bg-primary" style="width: {{ $kelas->total_siswa > 0 ? ($kelas->siswa_laki / $kelas->total_siswa) * 100 : 0 }}%"></div>
                              <div class="progress-bar bg-pink" style="width: {{ $kelas->total_siswa > 0 ? ($kelas->siswa_perempuan / $kelas->total_siswa) * 100 : 0 }}%"></div>
                           </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="7" class="text-center py-4">
                           <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                           <h5 class="text-muted">Tidak ada data kelas</h5>
                           <p class="text-muted">Silakan ubah filter untuk melihat data</p>
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
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

.bg-pink {
    background-color: #e83e8c !important;
}

.progress-bar.bg-pink {
    background-color: #e83e8c;
}

.animate__animated {
    animation-duration: 0.5s;
}

.animate__fadeIn {
    animation-name: fadeIn;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.description-block {
    margin: 0;
}

.description-header {
    font-size: 16px;
    margin: 0;
    padding: 0;
    font-weight: 600;
}

.description-percentage {
    font-size: 20px;
    font-weight: bold;
}

.progress-group {
    margin-bottom: 15px;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.small-box {
    transition: transform 0.2s;
}

.small-box:hover {
    transform: translateY(-3px);
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Hide preloader after page load
    $('.preloader').fadeOut();
    
    // Auto submit form on filter change
    $('#filterForm select').change(function() {
        $('#filterForm').submit();
    });
    
    // Reset filter functionality
    $('#resetFilter').click(function(e) {
        e.preventDefault();
        
        // Redirect to dashboard without any parameters to reset all filters
        window.location.href = '{{ route("ilp.dashboard-sekolah") }}';
    });
    
    // Status Chart
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Pindah', 'Lulus', 'Drop Out'],
            datasets: [{
                data: [{{ $siswaAktif }}, {{ $siswaPindah }}, {{ $siswaLulus }}, {{ $siswaDropOut }}],
                backgroundColor: [
                    '#28a745',
                    '#ffc107', 
                    '#007bff',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            }
        }
    });
    
    // Age Distribution Chart
    var ageCtx = document.getElementById('ageChart').getContext('2d');
    var ageChart = new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($distribusiUmur->pluck('kelompok_umur')->toArray()) !!},
            datasets: [{
                label: 'Jumlah Siswa',
                data: {!! json_encode($distribusiUmur->pluck('jumlah')->toArray()) !!},
                backgroundColor: '#17a2b8',
                borderColor: '#138496',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        maxTicksLimit: 10
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            }
        }
    });
    
    // Export Excel
    $('#exportExcel').click(function(e) {
        e.preventDefault();
        
        var sekolah = $('select[name="sekolah"]').val();
        var jenisSekolah = $('select[name="jenis_sekolah"]').val();
        var kelas = $('select[name="kelas"]').val();
        
        var exportUrl = '{{ url("ilp/dashboard-sekolah/export/excel") }}';
        var params = [];
        
        if (sekolah) params.push('sekolah=' + encodeURIComponent(sekolah));
        if (jenisSekolah) params.push('jenis_sekolah=' + encodeURIComponent(jenisSekolah));
        if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...');
        $(this).prop('disabled', true);
        
        window.location.href = exportUrl;
        
        setTimeout(function() {
            $('#exportExcel').html(originalText);
            $('#exportExcel').prop('disabled', false);
        }, 2000);
    });
    
    // Export PDF
    $('#exportPdf').click(function(e) {
        e.preventDefault();
        
        var sekolah = $('select[name="sekolah"]').val();
        var jenisSekolah = $('select[name="jenis_sekolah"]').val();
        var kelas = $('select[name="kelas"]').val();
        
        var exportUrl = '{{ url("ilp/dashboard-sekolah/export/pdf") }}';
        var params = [];
        
        if (sekolah) params.push('sekolah=' + encodeURIComponent(sekolah));
        if (jenisSekolah) params.push('jenis_sekolah=' + encodeURIComponent(jenisSekolah));
        if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...');
        $(this).prop('disabled', true);
        
        window.location.href = exportUrl;
        
        setTimeout(function() {
            $('#exportPdf').html(originalText);
            $('#exportPdf').prop('disabled', false);
        }, 2000);
    });
});
</script>
@stop