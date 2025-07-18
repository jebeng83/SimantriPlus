@extends('adminlte::page')

@section('title', 'Dashboard CKG')

@section('css')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
.card-stats {
    transition: transform 0.2s;
}
.card-stats:hover {
    transform: translateY(-2px);
}
.periode-filter .btn {
    border-radius: 20px;
    margin: 0 2px;
    transition: all 0.3s ease;
}
.periode-filter .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.table tbody tr {
    transition: all 0.3s ease;
}
.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}
.badge-rank {
    font-size: 0.9em;
    padding: 8px 12px;
    border-radius: 20px;
}
.badge-entri {
    font-size: 1.0em !important;
    font-weight: bold !important;
    padding: 12px 20px !important;
    border-radius: 25px !important;
    min-width: 80px;
    display: inline-block;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
.badge-entri:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}
.rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); color: #333; }
.rank-2 { background: linear-gradient(135deg, #C0C0C0, #A9A9A9); color: #333; }
.rank-3 { background: linear-gradient(135deg, #CD7F32, #B8860B); color: white; }
.rank-other { background: linear-gradient(135deg, #6c757d, #495057); color: white; }
</style>
@endsection

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
   <div>
      <h1 class="m-0 text-dark"><i class="fas fa-chart-bar mr-2"></i>Dashboard Monitoring Entri Data CKG</h1>
      <h5 class="text-muted"><i class="fas fa-user-md mr-1"></i>Selamat Datang, {{$nm_dokter}}</h5>
   </div>
   <div class="text-right">
      <h5 id="tanggalHari" class="text-muted"></h5>
      <h5 id="jamDigital" class="text-muted"></h5>
   </div>
</div>
@endsection

@section('content')
<!-- Filter Periode -->
<div class="row mb-4">
   <div class="col-12">
      <x-adminlte-card title="Filter Periode" theme="primary" theme-mode="outline" class="card-outline">
         <div class="periode-filter text-center">
            <div class="btn-group" role="group">
               <button type="button" data-periode="hari"
                  class="btn-periode btn {{ $periode_filter == 'hari' ? 'btn-success' : 'btn-outline-success' }}">
                  <i class="fas fa-calendar-day mr-1"></i> Harian
               </button>
               <button type="button" data-periode="minggu"
                  class="btn-periode btn {{ $periode_filter == 'minggu' ? 'btn-success' : 'btn-outline-success' }}">
                  <i class="fas fa-calendar-week mr-1"></i> Mingguan
               </button>
               <button type="button" data-periode="bulan"
                  class="btn-periode btn {{ $periode_filter == 'bulan' ? 'btn-success' : 'btn-outline-success' }}">
                  <i class="fas fa-calendar-alt mr-1"></i> Bulanan
               </button>
               <button type="button" data-periode="tahun"
                  class="btn-periode btn {{ $periode_filter == 'tahun' ? 'btn-success' : 'btn-outline-success' }}">
                  <i class="fas fa-calendar mr-1"></i> Tahunan
               </button>
            </div>
         </div>
         <div class="text-center mt-3">
            <small class="text-muted">
               <i class="fas fa-info-circle mr-1"></i>
               @if($periode_filter == 'hari')
               Data entri hari ini dari tabel skrining_pkg
               @elseif($periode_filter == 'minggu')
               Data entri minggu ini dari tabel skrining_pkg
               @elseif($periode_filter == 'tahun')
               Data entri tahun ini dari tabel skrining_pkg
               @else
               Data entri bulan ini dari tabel skrining_pkg
               @endif
            </small>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Statistik Ringkas -->
<div class="row mb-4">
   <div class="col-lg-3 col-md-6">
      <div class="card card-stats bg-gradient-primary text-white">
         <div class="card-body">
            <div class="row">
               <div class="col">
                  <h5 class="card-title text-uppercase text-white mb-0">Total Pegawai</h5>
                  <span class="h2 font-weight-bold mb-0" id="total-pegawai">{{ count($data_entri_pegawai) }}</span>
               </div>
               <div class="col-auto">
                  <div class="icon icon-shape bg-white text-primary rounded-circle shadow">
                     <i class="fas fa-users"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-md-6">
      <div class="card card-stats bg-gradient-success text-white">
         <div class="card-body">
            <div class="row">
               <div class="col">
                  <h5 class="card-title text-uppercase text-white mb-0">Total Entri</h5>
                  <span class="h2 font-weight-bold mb-0" id="total-entri">{{ array_sum(array_column($data_entri_pegawai, 'jumlah_entri')) }}</span>
               </div>
               <div class="col-auto">
                  <div class="icon icon-shape bg-white text-success rounded-circle shadow">
                     <i class="fas fa-clipboard-list"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-md-6">
      <div class="card card-stats bg-gradient-info text-white">
         <div class="card-body">
            <div class="row">
               <div class="col">
                  <h5 class="card-title text-uppercase text-white mb-0">Rata-rata</h5>
                  <span class="h2 font-weight-bold mb-0" id="rata-rata">
                     {{ count($data_entri_pegawai) > 0 ? number_format(array_sum(array_column($data_entri_pegawai, 'jumlah_entri')) / count($data_entri_pegawai), 1) : 0 }}
                  </span>
               </div>
               <div class="col-auto">
                  <div class="icon icon-shape bg-white text-info rounded-circle shadow">
                     <i class="fas fa-chart-line"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-3 col-md-6">
      <div class="card card-stats bg-gradient-warning text-white">
         <div class="card-body">
            <div class="row">
               <div class="col">
                  <h5 class="card-title text-uppercase text-white mb-0">Tertinggi</h5>
                  <span class="h2 font-weight-bold mb-0" id="tertinggi">
                     {{ count($data_entri_pegawai) > 0 ? max(array_column($data_entri_pegawai, 'jumlah_entri')) : 0 }}
                  </span>
               </div>
               <div class="col-auto">
                  <div class="icon icon-shape bg-white text-warning rounded-circle shadow">
                     <i class="fas fa-trophy"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Tabel Data Entri Per Pegawai -->
<div class="row">
   <div class="col-12">
      <x-adminlte-card title="Data Jumlah Entri Per Pegawai" theme="primary" theme-mode="outline">
         <div class="table-responsive">
            <table class="table table-striped table-hover" id="table-entri-pegawai">
               <thead>
                  <tr>
                     <th width="5%">No</th>
                     <th width="15%">NIK</th>
                     <th width="50%">Nama Pegawai</th>
                     <th width="20%">Jumlah Entri</th>
                     <th width="10%">Ranking</th>
                  </tr>
               </thead>
               <tbody id="tbody-entri-pegawai">
                  @forelse($data_entri_pegawai as $index => $pegawai)
                  <tr>
                     <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                     <td><code>{{ $pegawai->nik }}</code></td>
                     <td>
                        <div class="d-flex align-items-center">
                           <div class="avatar avatar-sm rounded-circle bg-gradient-primary text-white mr-2">
                              <i class="fas fa-user"></i>
                           </div>
                           <span class="font-weight-bold">{{ $pegawai->nama_pegawai }}</span>
                        </div>
                     </td>
                     <td>
                        <span class="badge badge-pill badge-primary badge-entri">
                           <i class="fas fa-clipboard-check mr-2"></i>{{ $pegawai->jumlah_entri }}
                        </span>
                     </td>
                     <td>
                        @if($index == 0)
                           <span class="badge badge-rank rank-1"><i class="fas fa-trophy mr-1"></i>1st</span>
                        @elseif($index == 1)
                           <span class="badge badge-rank rank-2"><i class="fas fa-medal mr-1"></i>2nd</span>
                        @elseif($index == 2)
                           <span class="badge badge-rank rank-3"><i class="fas fa-award mr-1"></i>3rd</span>
                        @else
                           <span class="badge badge-rank rank-other">{{ $index + 1 }}</span>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        <h5>Tidak ada data</h5>
                        <p>Belum ada data entri untuk periode yang dipilih</p>
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </x-adminlte-card>
   </div>
</div>
@endsection

@section('js')
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    $('#table-entri-pegawai').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "paging": true,
        "pageLength": 25,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        "order": [[3, "desc"]] // Urutkan berdasarkan jumlah entri (descending)
    });
    
    // Update jam digital
    function updateJam() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const tanggal = now.toLocaleDateString('id-ID', options);
        const jam = now.toLocaleTimeString('id-ID');
        
        $('#tanggalHari').text(tanggal);
        $('#jamDigital').text(jam);
    }
    
    // Update jam setiap detik
    updateJam();
    setInterval(updateJam, 1000);
    
    // Handle filter periode
    $('.btn-periode').on('click', function() {
        const periode = $(this).data('periode');
        
        // Update button states
        $('.btn-periode').removeClass('btn-success').addClass('btn-outline-success');
        $(this).removeClass('btn-outline-success').addClass('btn-success');
        
        // Show loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang mengambil data untuk periode ' + periode,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Reload halaman dengan parameter periode
        window.location.href = '{{ route("ilp.dashboard-ckg") }}?periode=' + periode;
    });
    
    // Animasi counter untuk statistik
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            $(element).text(Math.floor(current));
        }, 20);
    }
    
    // Jalankan animasi counter saat halaman dimuat
    setTimeout(() => {
        const totalPegawai = {{ count($data_entri_pegawai) }};
        const totalEntri = {{ array_sum(array_column($data_entri_pegawai, 'jumlah_entri')) }};
        const tertinggi = {{ count($data_entri_pegawai) > 0 ? max(array_column($data_entri_pegawai, 'jumlah_entri')) : 0 }};
        
        animateCounter('#total-pegawai', totalPegawai);
        animateCounter('#total-entri', totalEntri);
        animateCounter('#tertinggi', tertinggi);
    }, 500);
});
</script>
@endsection