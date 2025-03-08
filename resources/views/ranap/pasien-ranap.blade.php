@extends('adminlte::page')

@section('title', 'Pasien Ranap')

@section('content_header')
<div class="ranap-header premium-header">
    <div class="header-content">
        <h1 class="ranap-title">Pasien Rawat Inap</h1>
        <p class="ranap-subtitle">Daftar Pasien yang Sedang Dirawat</p>
    </div>
    <div class="header-actions">
        <div class="quick-stats">
            <div class="stat-item">
                <span class="stat-value">{{ count($data) }}</span>
                <span class="stat-label">Total Pasien</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ collect($data)->where('nm_bangsal', 'MELATI')->count() }}</span>
                <span class="stat-label">Bangsal Melati</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ collect($data)->where('nm_bangsal', '!=', 'MELATI')->count() }}</span>
                <span class="stat-label">Bangsal Lain</span>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="ranap-container premium-container">
    <div class="ranap-card premium-card">
        <div class="card-header-gradient"></div>
        <div class="ranap-card-body p-0">
            <div class="ranap-table-wrapper">
                <table id="tablePasienRanap" class="ranap-table table-hover">
                    <thead>
                        <tr>
                            @foreach($heads as $head)
                            <th>{{ $head }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        @php
                        $noRawat = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rawat);
                        $noRM = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rkm_medis);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{route('ranap.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM, 'bangsal' => $row->kd_bangsal])}}"
                                    class="patient-name">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    <span class="patient-fullname">{{$row->nm_pasien}}</span>
                                </a>
                            </td>
                            <td>
                                <span class="medical-record">{{$row->no_rkm_medis}}</span>
                            </td>
                            <td>
                                <div class="ward-badge">
                                    <i class="fas fa-bed mr-1"></i>
                                    {{$row->nm_bangsal}}
                                </div>
                            </td>
                            <td>
                                <span class="room-number">{{$row->kd_kamar}}</span>
                            </td>
                            <td>
                                <span class="date-badge">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    {{$row->tgl_masuk}}
                                </span>
                            </td>
                            <td>
                                <span class="insurance-badge 
                                        @if($row->png_jawab == 'BPJS') bpjs 
                                        @elseif($row->png_jawab == 'UMUM') umum 
                                        @else other @endif">
                                    {{$row->png_jawab}}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap"
    rel="stylesheet">
<style>
    :root {
        --premium-gradient: linear-gradient(135deg, #233292 0%, #4F5BDA 100%);
        --premium-shadow: 0 10px 30px rgba(35, 50, 146, 0.15);
        --premium-border-radius: 12px;
        --premium-font-heading: 'Playfair Display', serif;
    }

    body {
        background-color: #f7f9fc;
    }

    /* Premium Header */
    .premium-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        margin-bottom: 2rem;
        position: relative;
    }

    .premium-header::before {
        content: '';
        position: absolute;
        left: -1000px;
        top: 0;
        width: 3000px;
        height: 100%;
        background: var(--premium-gradient);
        opacity: 0.05;
        z-index: -1;
    }

    .premium-header .header-content {
        position: relative;
    }

    .ranap-title {
        font-family: var(--premium-font-heading);
        font-size: 2.2rem;
        font-weight: 600;
        color: #233292;
        margin-bottom: 0.3rem;
        letter-spacing: -0.5px;
        position: relative;
    }

    .ranap-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 80px;
        height: 4px;
        background: var(--premium-gradient);
        border-radius: 10px;
    }

    .ranap-subtitle {
        font-size: 1.1rem;
        color: #4a5568;
        font-weight: 400;
    }

    /* Quick Stats */
    .quick-stats {
        display: flex;
        gap: 1.5rem;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        min-width: 120px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3748;
        font-family: var(--premium-font-heading);
    }

    .stat-label {
        font-size: 0.85rem;
        color: #718096;
        font-weight: 500;
    }

    /* Premium Container */
    .premium-container {
        padding: 0 1.5rem 2rem;
    }

    /* Premium Card */
    .premium-card {
        border-radius: var(--premium-border-radius);
        box-shadow: var(--premium-shadow);
        border: none;
        position: relative;
        overflow: hidden;
        background: white;
        margin-bottom: 2rem;
    }

    .card-header-gradient {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 8px;
        background: var(--premium-gradient);
        z-index: 1;
    }

    .ranap-card-body {
        padding: 2rem;
    }

    /* Table Styles */
    .ranap-table-wrapper {
        padding: 1.5rem;
    }

    .ranap-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .ranap-table thead th {
        background-color: rgba(247, 250, 252, 0.8);
        color: #233292;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .ranap-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, #4F5BDA 0%, rgba(79, 91, 218, 0) 100%);
        opacity: 0.3;
    }

    .ranap-table tbody tr {
        transition: all 0.3s ease;
    }

    .ranap-table tbody tr:hover {
        background-color: rgba(79, 91, 218, 0.03);
        transform: scale(1.003);
    }

    .ranap-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        color: #4a5568;
        font-size: 0.95rem;
    }

    .ranap-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Patient Name Styling */
    .patient-name {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #2d3748;
        font-weight: 500;
    }

    .patient-name:hover {
        background: rgba(79, 91, 218, 0.05);
        transform: translateY(-2px);
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(79, 91, 218, 0.1);
        color: #233292;
    }

    .patient-name i {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edf2f7;
        border-radius: 50%;
        margin-right: 0.75rem;
        color: #4F5BDA;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .patient-name:hover i {
        transform: scale(1.1);
        background: #4F5BDA;
        color: white;
    }

    .patient-fullname {
        font-weight: 500;
        transition: all 0.2s ease;
    }

    /* Medical Record Styling */
    .medical-record {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #4a5568;
        padding: 0.25rem 0.5rem;
        background: #f7fafc;
        border-radius: 4px;
        border: 1px solid #edf2f7;
    }

    /* Ward Badge Styling */
    .ward-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        background: rgba(66, 153, 225, 0.1);
        color: #3182ce;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Room Number Styling */
    .room-number {
        font-weight: 600;
        color: #4a5568;
    }

    /* Date Badge Styling */
    .date-badge {
        display: inline-flex;
        align-items: center;
        color: #718096;
        font-size: 0.9rem;
    }

    .date-badge i {
        color: #4F5BDA;
        margin-right: 0.35rem;
    }

    /* Insurance Badge Styling */
    .insurance-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        text-align: center;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .insurance-badge.bpjs {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .insurance-badge.umum {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        color: white;
    }

    .insurance-badge.other {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .premium-card {
        animation: fadeInUp 0.6s ease forwards;
    }

    .stat-item:nth-child(1) {
        animation: fadeInUp 0.4s ease forwards;
    }

    .stat-item:nth-child(2) {
        animation: fadeInUp 0.5s ease forwards;
    }

    .stat-item:nth-child(3) {
        animation: fadeInUp 0.6s ease forwards;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .premium-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .quick-stats {
            margin-top: 1.5rem;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .stat-item {
            min-width: 110px;
        }

        .ranap-table thead th,
        .ranap-table tbody td {
            padding: 0.75rem;
        }
    }
</style>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
<script>
    $(document).ready(function() {
        // DataTable initialization
        if ($.fn.DataTable.isDataTable('#tablePasienRanap')) {
            $('#tablePasienRanap').DataTable().destroy();
        }
        
        // Initialize DataTable with premium settings
        $('#tablePasienRanap').DataTable({
            responsive: true,
            language: {
                search: "Cari pasien:",
                lengthMenu: "Tampilkan _MENU_ pasien",
                info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ pasien",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ditemukan pasien yang sesuai",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            },
            dom: '<"d-flex justify-content-between align-items-center mb-4"fl>t<"d-flex justify-content-between align-items-center mt-4"ip>',
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            drawCallback: function() {
                // Animate table rows
                animateTableRows();
            },
            initComplete: function() {
                // Custom styling for inputs
                $('.dataTables_filter input').attr('placeholder', 'Cari pasien...');
                $('.dataTables_filter input').css({
                    'border': '1px solid #e2e8f0',
                    'border-radius': '8px',
                    'padding': '0.5rem 1rem',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.05)'
                });
                $('.dataTables_length select').css({
                    'border-radius': '8px',
                    'padding': '0.35rem'
                });
            }
        });
        
        // Function to animate table rows
        function animateTableRows() {
            $('.ranap-table tbody tr').each(function(index) {
                $(this).css({
                    'opacity': 0,
                    'transform': 'translateY(10px)'
                });
                
                setTimeout(function() {
                    $(this).css({
                        'opacity': 1,
                        'transform': 'translateY(0)',
                        'transition': 'all 0.3s ease'
                    });
                }.bind(this), 50 * (index + 1));
            });
        }
        
        // Call once when page loads
        animateTableRows();
        
        // Auto refresh data secara berkala
        function setupAutoRefresh() {
            // Refresh data setiap 30 detik
            setInterval(function() {
                refreshData();
            }, 30000); // 30 detik
            
            // Listen untuk event pasien-saved dari halaman lain
            window.addEventListener('pasien-saved', function(event) {
                console.log('Mendeteksi pasien baru disimpan, memperbarui daftar...');
                refreshData();
            });
        }
        
        // Fungsi untuk memperbarui data tabel
        function refreshData() {
            // Simpan pencarian dan pagination saat ini
            const currentSearch = $('.dataTables_filter input').val();
            const currentPage = $('#tablePasienRanap').DataTable().page.info().page;
            
            // Lakukan AJAX request untuk mendapatkan data terbaru
            $.ajax({
                url: window.location.href,
                method: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Ekstrak tbody dari respons
                    const newHtml = $(response).find('#tablePasienRanap tbody').html();
                    
                    // Update tbody tanpa merender ulang seluruh tabel
                    $('#tablePasienRanap tbody').html(newHtml);
                    
                    // Terapkan kembali pencarian dan pagination
                    const dataTable = $('#tablePasienRanap').DataTable();
                    dataTable.search(currentSearch).draw();
                    dataTable.page(currentPage).draw('page');
                    
                    // Terapkan kembali animasi
                    animateTableRows();
                    
                    console.log('Data telah diperbarui');
                },
                error: function(error) {
                    console.error('Gagal memperbarui data:', error);
                }
            });
        }
        
        // Inisialisasi auto refresh
        setupAutoRefresh();
        
        // Add hover effects to patient rows
        $('.ranap-table tbody tr').hover(
            function() {
                $(this).find('.patient-name i').css('transform', 'scale(1.1)');
            },
            function() {
                $(this).find('.patient-name i').css('transform', 'scale(1)');
            }
        );
    });
</script>
@stop