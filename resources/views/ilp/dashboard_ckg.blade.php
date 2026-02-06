@extends('adminlte::page')

@section('title', 'Dashboard CKG')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css') }}">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        background-color: #f4f6f9 !important;
    }

    .content-wrapper {
        background-color: #f4f6f9 !important;
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
    }

    /* Stat Cards */
    .card-modern {
        border-radius: 15px;
        padding: 20px;
        color: white !important;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin-bottom: 20px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .bg-grad-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .bg-grad-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-grad-amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-grad-rose { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }

    .card-modern .icon-bg {
        position: absolute;
        right: -10px;
        top: -10px;
        font-size: 60px;
        opacity: 0.15;
    }

    .card-modern .label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .card-modern .value {
        font-size: 32px;
        font-weight: 800;
        margin: 0;
    }

    /* Filter wrapper */
    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 15px 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .btn-period {
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        margin-right: 5px;
        transition: 0.2s;
    }

    .btn-period.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    /* Table Container */
    .table-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    #table-entri-pegawai thead th {
        background: #f8fafc !important;
        color: #64748b !important;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        border-bottom: 2px solid #f1f5f9 !important;
        padding: 12px !important;
    }

    .dataTables_wrapper {
        width: 100% !important;
        position: relative;
        clear: both;
    }

    .dataTables_wrapper .row {
        display: flex !important;
        flex-wrap: wrap !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .dataTables_wrapper .dataTables_length {
        float: left !important;
        text-align: left !important;
        margin-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_filter {
        float: right !important;
        text-align: right !important;
        margin-bottom: 20px;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-weight: 600;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 10px !important;
        width: 280px !important;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 12px;
        transition: all 0.2s;
        display: inline-block !important;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Custom Pagination */
    .dataTables_wrapper .dataTables_paginate {
        float: right !important;
        margin-top: 20px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        margin-left: 5px !important;
        padding: 6px 14px !important;
        font-weight: 600 !important;
        transition: all 0.2s !important;
        background: #ffffff !important;
        color: #4b5563 !important;
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f3f4f6 !important;
        color: #4f46e5 !important;
        border-color: #d1d5db !important;
        text-decoration: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
    }

    .dataTables_wrapper .dataTables_info {
        float: left !important;
        margin-top: 20px;
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">
    
    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="font-weight-bold mb-1">Monitor Entri Data CKG</h2>
                <p class="mb-0 opacity-80">Selamat datang kembali, <strong>{{$nm_dokter}}</strong></p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <div class="d-inline-block text-right">
                    <div id="tanggalHari" class="font-weight-bold small"></div>
                    <div id="jamDigital" class="h4 font-weight-bold text-warning mb-0"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-12 d-flex align-items-center mb-3 mb-lg-0">
                <div class="bg-primary p-2 rounded mr-3 text-white">
                    <i class="fas fa-filter"></i>
                </div>
                <h5 class="mb-0 font-weight-bold">Filter Laporan</h5>
            </div>
            <div class="col-lg-8 col-md-12 text-lg-right">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 15px;">
                    <!-- Dropdown Berdasarkan -->
                    <div class="d-flex align-items-center">
                        <span class="mr-2 font-weight-bold small text-muted text-uppercase">Sumber:</span>
                        <select id="filter-berdasarkan" class="form-control form-control-sm rounded-pill px-3" style="width: auto; min-width: 220px; font-weight: 600; border-color: #e2e8f0;">
                            <option value="asik" {{ $berdasarkan == 'asik' ? 'selected' : '' }}>Petugas Berdasarkan Entri Asik</option>
                            <option value="skrining" {{ $berdasarkan == 'skrining' ? 'selected' : '' }}>Petugas Berdasarkan Entri Skrining</option>
                        </select>
                    </div>

                    <!-- Button Group Periode -->
                    <div class="btn-group shadow-sm rounded-pill overflow-hidden" id="wrapper-periode">
                        @foreach(['hari' => 'Harian', 'minggu' => 'Mingguan', 'bulan' => 'Bulanan', 'tahun' => 'Tahunan'] as $key => $label)
                            <button type="button" data-periode="{{ $key }}" class="btn-period {{ $periode_filter == $key ? 'active' : '' }}" style="margin: 0; border: none; border-radius: 0;">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card-modern bg-grad-indigo">
                <i class="fas fa-users icon-bg"></i>
                <div class="label">Total Pegawai</div>
                <div class="value" id="total-pegawai">0</div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card-modern bg-grad-emerald">
                <i class="fas fa-file-alt icon-bg"></i>
                <div class="label">Total Entri</div>
                <div class="value" id="total-entri">0</div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card-modern bg-grad-amber">
                <i class="fas fa-chart-line icon-bg"></i>
                <div class="label">Rata-rata</div>
                <div class="value" id="rata-rata">0</div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card-modern bg-grad-rose">
                <i class="fas fa-trophy icon-bg"></i>
                <div class="label">Tertinggi</div>
                <div class="value" id="tertinggi">0</div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="font-weight-bold mb-0">Leaderboard Kontribusi</h5>
            <button class="btn btn-success btn-sm font-weight-bold rounded-pill px-3" id="btn-export-excel">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </button>
        </div>
        
        <table class="table table-hover" id="table-entri-pegawai" style="width: 100% !important;">
            <thead>
                <tr>
                    <th class="text-center" width="50">No</th>
                    <th>Nama Pegawai</th>
                    <th class="text-center">NIK</th>
                    <th class="text-center">Jumlah Entri</th>
                    <th class="text-center">Ranking</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data_entri_pegawai as $index => $pegawai)
                <tr>
                    <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded-xl mr-2 text-primary" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="font-weight-bold">{{ $pegawai->nama_pegawai }}</span>
                        </div>
                    </td>
                    <td class="text-center text-muted small">{{ $pegawai->nik }}</td>
                    <td class="text-center text-primary font-weight-bold">{{ $pegawai->jumlah_entri }}</td>
                    <td class="text-center">
                        @if($index == 0)
                            <span class="badge badge-warning" style="font-size: 11px;"><i class="fas fa-crown mr-1"></i>1st Rank</span>
                        @elseif($index == 1)
                            <span class="badge badge-secondary" style="font-size: 11px;">2nd Rank</span>
                        @elseif($index == 2)
                            <span class="badge" style="background:#cd7f32; color:white; font-size: 11px;">3rd Rank</span>
                        @else
                            <span class="text-muted small">{{ $index + 1 }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="dashboard-ckg-data" class="d-none"
         data-total-pegawai="{{ count($data_entri_pegawai) }}"
         data-total-entri="{{ array_sum(array_column($data_entri_pegawai, 'jumlah_entri')) }}"
         data-rata-rata="{{ count($data_entri_pegawai) > 0 ? floor(array_sum(array_column($data_entri_pegawai, 'jumlah_entri')) / count($data_entri_pegawai)) : 0 }}"
         data-tertinggi="{{ count($data_entri_pegawai) > 0 ? max(array_column($data_entri_pegawai, 'jumlah_entri')) : 0 }}"
    ></div>
</div>
@endsection

@section('js')
<script src="{{ asset('epasien/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.min.js') }}"></script>

<script>
$(document).ready(function() {
    // DataTables dengan koordinasi DOM yang lebih kuat
    var table = $('#table-entri-pegawai').DataTable({
        "order": [[3, "desc"]],
        "responsive": true,
        "autoWidth": false,
        "dom": "<'row mb-4'<'col-sm-6'l><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row mt-4'<'col-sm-5'i><'col-sm-7'p>>",
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data tidak ditemukan",
            "emptyTable": "Tidak ada data kontribusi untuk periode ini",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "paginate": {
                "first": "Awal",
                "last": "Akhir",
                "next": "Lanjut",
                "previous": "Balik"
            }
        },
        "drawCallback": function(settings) {
            $('.paginate_button').removeClass('btn-default');
        }
    });

    // Refresh layout table
    setTimeout(function() {
        table.columns.adjust().draw();
    }, 500);

    // Clock
    function clock() {
        const now = new Date();
        $('#tanggalHari').text(now.toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'}));
        $('#jamDigital').text(now.toLocaleTimeString('id-ID', {hour12:false}));
    }
    setInterval(clock, 1000); clock();

    // Counters
    function animate(id, end) {
        $({v:0}).animate({v:end}, {
            duration: 1000,
            step: function() { $('#'+id).text(Math.floor(this.v).toLocaleString('id-ID')); },
            complete: function() { $('#'+id).text(end.toLocaleString('id-ID')); }
        });
    }

    setTimeout(() => {
        const el = document.getElementById('dashboard-ckg-data');
        const d = el ? el.dataset : {};
        const totalPegawai = Number(d.totalPegawai || 0);
        const totalEntri = Number(d.totalEntri || 0);
        const rataRata = Number(d.rataRata || 0);
        const tertinggi = Number(d.tertinggi || 0);
        animate('total-pegawai', totalPegawai);
        animate('total-entri', totalEntri);
        animate('rata-rata', rataRata);
        animate('tertinggi', tertinggi);
    }, 500);

    $('.btn-period').click(function() {
        const periode = $(this).data('periode');
        const berdasarkan = $('#filter-berdasarkan').val();
        window.location.href = '{{ route("ilp.dashboard-ckg") }}?periode=' + periode + '&berdasarkan=' + berdasarkan;
    });

    $('#filter-berdasarkan').change(function() {
        const berdasarkan = $(this).val();
        const periode = $('.btn-period.active').data('periode') || 'bulan';
        window.location.href = '{{ route("ilp.dashboard-ckg") }}?periode=' + periode + '&berdasarkan=' + berdasarkan;
    });
});
</script>
@endsection
