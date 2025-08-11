@extends('adminlte::page')

@section('title', 'Register Pasien')

@section('content_header')
<div class="registrasi-header">
    <div class="header-content">
        <div class="title-section">
            <h1 class="registrasi-title">Registrasi Pasien Hari Ini</h1>
            <p class="subtitle">{{ date('d F Y') }}</p>
        </div>
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="total-pasien">{{ $totalPasien ?? 0 }}</div>
                    <div class="stat-label">Total Pasien</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="belum-periksa">{{ $belumPeriksa ?? 0 }}</div>
                    <div class="stat-label">Belum Periksa</div>
                </div>
            </div>
        </div>
        <button class="registrasi-btn registrasi-btn-primary btn-register" data-toggle="modal"
            data-target="#modalPendaftaran">
            <i class="fas fa-user-plus registrasi-btn-icon"></i>Register Baru
        </button>
    </div>
</div>
@stop

@section('content')
<div class="registrasi-container">
    <!-- Filter Poli Section -->
    <div class="filter-section mb-3">
        <div class="card filter-card-horizontal">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <h6 class="filter-title mb-0">
                            <i class="fas fa-hospital mr-2"></i>Poli
                        </h6>
                    </div>
                    <div class="col-md-8">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <select id="filter-poli" class="form-control select2" style="width: 100%;">
                                    <option value="">Pilih Poliklinik</option>
                                    @foreach($poliklinik as $poli)
                                        <option value="{{ $poli->kd_poli }}">{{ $poli->nm_poli }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="filter-actions d-flex justify-content-start ml-3">
                                    <button id="lock-filter" class="btn btn-outline-primary btn-sm mr-2" title="Lock Filter">
                                        <i class="fas fa-unlock" id="lock-icon"></i>
                                    </button>
                                    <button id="reset-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <!-- Empty space for balance -->
                    </div>
                </div>
                <div id="filter-status" class="mt-2" style="display: none;">
                    <div class="alert alert-info alert-sm mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="filter-status-text">Filter tidak terkunci</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-container" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 font-weight-bold">Memuat data registrasi pasien...</p>
    </div>

    <div id="table-container" class="datatable-wrapper">
        <livewire:reg-periksa-table wire:id="reg-periksa-table" />
    </div>
</div>

<x-adminlte-modal id="modalPendaftaran" title="Pendaftaran Pasien Baru" v-centered static-backdrop>
    <div id="modal-loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 font-weight-bold">Mempersiapkan formulir pendaftaran...</p>
    </div>
    <div id="form-container">
        <livewire:registrasi.form-pendaftaran />
    </div>
    <x-slot name="footerSlot">
        {{-- Buttons controlled by Livewire component --}}
    </x-slot>
</x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)
@section('plugins.Sweetalert2', true)

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/adminlte-premium.css') }}">
<link rel="stylesheet" href="{{ asset('css/registrasi-premium.css') }}">
<style>
    /* Header Styles */
    .registrasi-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .title-section h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .subtitle {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .stats-section {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 150px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.pending {
        background: rgba(255, 193, 7, 0.3);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    .btn-register {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-register:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Info Cards */
    .info-cards-section .alert {
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #1565c0;
        border-left: 4px solid #2196f3;
    }

    /* Modal Styles */
    .modal {
        z-index: 1055 !important;
    }
    
    .modal-backdrop {
        z-index: 1050 !important;
    }
    
    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 90vw;
        width: auto;
    }
    
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-bottom: 0;
        padding: 1.25rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
    }

    .modal-body {
        padding: 1.5rem;
        max-height: calc(90vh - 120px);
        overflow-y: auto;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        height: 42px;
        padding: 0.3rem 0.5rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }

    .form-control {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(79, 91, 218, 0.15);
    }

    /* Table Badges */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 0.375rem;
    }

    .badge-success {
        background-color: #10b981;
        color: white;
    }

    .badge-primary {
        background-color: #3b82f6;
        color: white;
    }

    .badge-warning {
        background-color: #f59e0b;
        color: white;
    }

    /* Dropdown menu */
    .dropdown-menu {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        padding: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.6rem 1.25rem;
        color: #4a5568;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8fafc;
        color: var(--accent-color);
    }

    /* Filter Card Styles */
    .filter-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: visible;
    }

    .filter-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .filter-card .card-body {
        padding: 1.5rem;
    }

    /* Horizontal Filter Card Styles */
    .filter-card-horizontal {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: visible;
    }

    .filter-card-horizontal:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .filter-card-horizontal .card-body {
        padding: 1rem 1.5rem;
    }

    .filter-title {
        font-weight: 600;
        color: #4a5568;
        font-size: 1.1rem;
    }

    .filter-header {
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0.75rem;
    }

    .filter-body {
        padding-top: 0.5rem;
    }

    .filter-actions .btn {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .filter-actions .btn:hover {
        transform: translateY(-1px);
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .filter-actions .btn {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-actions .btn:hover {
        transform: translateY(-1px);
    }

    #filter-status {
        background: rgba(79, 91, 218, 0.1);
        border-radius: 6px;
        padding: 0.5rem;
    }

    .filter-locked {
        background: rgba(34, 197, 94, 0.1) !important;
        border-color: #22c55e !important;
    }

    .filter-locked #filter-status {
        background: rgba(34, 197, 94, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }
        
        .stats-section {
            justify-content: center;
        }
        
        .stat-card {
            min-width: 120px;
        }

        .filter-body .row {
            flex-direction: column;
        }

        .filter-body .col-md-9,
        .filter-body .col-md-3 {
            margin-bottom: 1rem;
        }

        .filter-actions {
            justify-content: center;
        }

        /* Horizontal filter responsive */
        .filter-card-horizontal .row {
            flex-direction: column;
        }

        .filter-card-horizontal .col-md-2,
        .filter-card-horizontal .col-md-8 {
            margin-bottom: 0.75rem;
        }

        .filter-card-horizontal .col-md-8 .row {
            flex-direction: column;
        }

        .filter-card-horizontal .col-md-8 .col-md-8,
        .filter-card-horizontal .col-md-8 .col-md-4 {
            margin-bottom: 0.5rem;
        }

        .filter-card-horizontal .col-md-8 .col-md-4 {
            margin-bottom: 0;
        }

        .filter-card-horizontal .filter-actions {
            justify-content: center;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .filter-card .card-body {
            padding: 1rem;
        }

        .filter-card-horizontal .card-body {
            padding: 0.75rem 1rem;
        }

        .filter-actions .btn {
            width: 36px;
            height: 36px;
        }

        .filter-title {
            font-size: 0.9rem;
        }
    }

    /* Patient Name Link Styles */
    .table tbody tr td a {
        transition: all 0.3s ease;
        border-radius: 4px;
        padding: 2px 6px;
        display: inline-block;
    }

    .table tbody tr td a:hover {
        background-color: rgba(79, 91, 218, 0.1);
        text-decoration: none !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(79, 91, 218, 0.2);
    }

    .table tbody tr:hover td a {
        color: #4f5bda !important;
        font-weight: 600;
    }
    
    /* Fix untuk modal ketika data sedikit */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }
    
    .registrasi-container {
        position: relative;
        z-index: 1;
    }
    
    /* Pastikan modal selalu di atas */
    .modal.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .modal.show .modal-dialog {
        transform: none;
        margin: auto;
    }
    
    /* Modal dengan scroll vertikal */
    .modal-dialog {
        max-width: 800px;
        width: 90%;
        margin: 1.75rem auto;
    }
    
    .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    
    .modal-header {
        flex-shrink: 0;
        position: sticky;
        top: 0;
        z-index: 1051;
        background: white;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(90vh - 120px);
        padding: 1.5rem;
    }
    
    .modal-footer {
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        z-index: 1051;
        background: white;
        border-top: 1px solid #dee2e6;
    }
    
    /* Custom scrollbar untuk modal */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsive modal */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100vw - 1rem);
            width: calc(100vw - 1rem);
        }
        
        .modal-content {
            max-height: 95vh;
        }
        
        .modal-body {
            max-height: calc(95vh - 120px);
            padding: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100vw - 0.5rem);
            width: calc(100vw - 0.5rem);
        }
        
        .modal-content {
            max-height: 98vh;
        }
        
        .modal-body {
            max-height: calc(98vh - 100px);
            padding: 0.75rem;
        }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inisialisasi tema premium pada tabel
        $('.table').addClass('registrasi-table');
        $('.table thead th').each(function() {
            const text = $(this).text();
            if (text.includes('▲') || text.includes('▼')) {
                const arrowChar = text.includes('▲') ? '▲' : '▼';
                const mainText = text.replace(arrowChar, '').trim();
                $(this).html(mainText + ' <span class="sort-icon">' + arrowChar + '</span>');
            }
        });
        
        // Global function untuk batal antrean BPJS
        window.batalAntrean = function(noRawat, namaPasien) {
            console.group('🚫 BPJS Batal Antrean Function Called');
            console.log('👤 Nama Pasien:', namaPasien);
            console.log('📋 No Rawat:', noRawat);
            console.log('⏰ Waktu:', new Date().toLocaleString());
            console.groupEnd();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batal Antrean BPJS',
                    text: `Apakah Anda yakin ingin membatalkan antrean BPJS untuk pasien ${namaPasien}?`,
                    input: 'textarea',
                    inputLabel: 'Alasan Pembatalan',
                    inputPlaceholder: 'Masukkan alasan pembatalan antrean...',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan pembatalan harus diisi!';
                        }
                        if (value.length < 10) {
                            return 'Alasan pembatalan minimal 10 karakter!';
                        }
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Batal',
                    preConfirm: (alasan) => {
                        console.log('💬 Alasan pembatalan:', alasan);
                        return alasan;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const alasan = result.value;
                        
                        console.group('🔄 Processing Batal Antrean BPJS');
                        console.log('👤 Nama Pasien:', namaPasien);
                        console.log('📋 No Rawat:', noRawat);
                        console.log('💬 Alasan:', alasan);
                        console.log('🔗 Mengirim request ke BPJS API...');
                        console.groupEnd();
                        
                        // Emit event ke Livewire component
                        const tableComponent = window.livewire.find('reg-periksa-table');
                        if (tableComponent) {
                            tableComponent.call('batalAntreanBPJS', noRawat, alasan);
                        } else {
                            // Fallback: emit global event
                            Livewire.emit('batalAntreanBPJS', noRawat, alasan);
                        }
                    } else {
                        console.log('🚫 Pembatalan antrean dibatalkan oleh user');
                    }
                });
            } else {
                // Fallback jika SweetAlert tidak tersedia
                const alasan = prompt(`Masukkan alasan pembatalan antrean untuk ${namaPasien}:`);
                if (alasan && alasan.trim().length >= 10) {
                    const tableComponent = window.livewire.find('reg-periksa-table');
                    if (tableComponent) {
                        tableComponent.call('batalAntreanBPJS', noRawat, alasan.trim());
                    } else {
                        Livewire.emit('batalAntreanBPJS', noRawat, alasan.trim());
                    }
                } else {
                    alert('Alasan pembatalan harus diisi minimal 10 karakter!');
                }
            }
        };

        // Format status bayar dengan badge
        $('.table tbody tr').each(function() {
            const jenisBayarCell = $(this).find('td:nth-child(10)');
            const jenisBayar = jenisBayarCell.text().trim();
            
            if (jenisBayar.toLowerCase().includes('bpjs')) {
                jenisBayarCell.html('<span class="status-badge bpjs">' + jenisBayar + '</span>');
            } else if (jenisBayar.toLowerCase().includes('umum')) {
                jenisBayarCell.html('<span class="status-badge umum">' + jenisBayar + '</span>');
            }
        });

        // Set CSRF token untuk semua permintaan AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Preload resources
        function preloadResources() {
            // Preload Select2 resources
            $.getScript('{{ asset('vendor/select2/js/select2.full.min.js') }}');
            $('<link>').attr({
                rel: 'stylesheet',
                type: 'text/css',
                href: '{{ asset('vendor/select2/css/select2.min.css') }}'
            }).appendTo('head');
            
            // Preload patient data for faster search
            $.ajax({
                url: '{{ url('/api/pasien') }}',
                data: { q: '', preload: true, limit: 20 },
                dataType: 'json',
                cache: true
            });
            
            // Preload doctor data
            $.ajax({
                url: '{{ route('dokter') }}',
                data: { q: '', preload: true, limit: 20 },
                dataType: 'json',
                cache: true
            });
        }
        
        // Call preload function
        preloadResources();
        
        // Handle modal loading
        $('.btn-register').on('click', function() {
            $('#form-container').hide();
            $('#modal-loading').show();
            
            setTimeout(function() {
                $('#modal-loading').fadeOut(200, function() {
                    $('#form-container').fadeIn(200);
                });
            }, 500);
        });
        
        // Livewire hooks untuk efek loading halus
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.sent', () => {
                $('#form-container').css('opacity', '0.5');
            });
            
            Livewire.hook('message.processed', () => {
                $('#form-container').css('opacity', '1');
                
                // Re-apply premium styling after table refreshes
                setTimeout(function() {
                    $('.table').addClass('registrasi-table');
                    
                    // Re-apply filter yang terkunci setelah table refresh
                     if (isFilterLocked && lockedPoliValue) {
                         console.log('Re-applying locked filter after Livewire refresh:', lockedPoliValue);
                         // Delay sedikit untuk memastikan table sudah sepenuhnya dimuat
                         setTimeout(() => {
                             applyPoliFilter(lockedPoliValue);
                         }, 200);
                     }
                    
                    updateStatistics();
                }, 100);
            });
            
            // Event listener untuk registrasi berhasil
            Livewire.on('registrationSuccess', (data) => {
                console.log('Registration success event received:', data);
                
                // Refresh Livewire component langsung
                const tableComponent = window.livewire.find('reg-periksa-table');
                if (tableComponent) {
                    tableComponent.call('refreshData');
                } else {
                    // Fallback: emit event untuk refresh
                    Livewire.emit('refreshDatatable');
                }
                
                // Refresh data table dan statistik
                setTimeout(() => {
                    updateStatistics();
                    
                    // Re-apply filter jika terkunci
                    if (isFilterLocked && lockedPoliValue) {
                        applyPoliFilter(lockedPoliValue);
                    }
                }, 300);
                
                // Show success notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil',
                        text: `Pasien berhasil didaftarkan dengan No. Rawat: ${data.no_rawat}`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
            
            // Event listener untuk refresh datatable
            Livewire.on('refreshDatatable', () => {
                console.log('Refresh datatable event received');
                
                // Refresh Livewire component langsung
                const tableComponent = window.livewire.find('reg-periksa-table');
                if (tableComponent) {
                    tableComponent.call('refreshData');
                }
                
                setTimeout(() => {
                    updateStatistics();
                    
                    // Re-apply filter jika terkunci
                    if (isFilterLocked && lockedPoliValue) {
                        applyPoliFilter(lockedPoliValue);
                    }
                }, 300);
            });
            
            // Handle session expired errors
            Livewire.hook('message.failed', (message, component) => {
                if (message.response && message.response.includes('This page has expired')) {
                    // Jika sesi berakhir, muat ulang halaman
                    Swal.fire({
                        title: 'Sesi Telah Berakhir',
                        text: 'Halaman akan dimuat ulang untuk memperbarui sesi.',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Muat Ulang'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        });

        // Implementasi pencarian
        $('.search-input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.table tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                if(text.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Handling dropdown menu in table
        $(document).on('click', '.action-dropdown .dropdown-item', function() {
            // Add loading animation for button clicks
            if ($(this).attr('wire:click') || $(this).attr('href')) {
                const $button = $(this).closest('.btn-group').find('.dropdown-toggle');
                const originalText = $button.html();
                
                if (!$(this).attr('href')) { // Only for wire:click, not for direct links
                    $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                    
                    setTimeout(function() {
                        $button.html(originalText);
                    }, 1000);
                }
            }
        });
        
        // Re-initialize dropdowns after Livewire updates
        Livewire.hook('message.processed', (message, component) => {
            // Apply premium styling to new dropdown menus
            $('.dropdown-toggle').dropdown();
            updateStatistics();
        });
        
        // Function to update statistics
        function updateStatistics(kdPoli = null) {
            // Gunakan AJAX untuk mendapatkan data statistik yang akurat dari server
            $.ajax({
                url: '{{ route('register.stats') }}',
                method: 'GET',
                data: {
                    date: getCurrentFilterDate(),
                    kd_poli: kdPoli || $('#filter-poli').val()
                },
                success: function(response) {
                    $('#total-pasien').text(response.totalPasien || 0);
                    $('#belum-periksa').text(response.belumPeriksa || 0);
                },
                error: function() {
                    // Fallback ke counting manual jika AJAX gagal
                    const totalRows = $('.table tbody tr:visible').length;
                    $('#total-pasien').text(totalRows);
                    
                    let belumPeriksa = 0;
                    $('.table tbody tr:visible').each(function() {
                        const statusCell = $(this).find('td').filter(function() {
                            return $(this).find('.badge-warning').length > 0;
                        });
                        if (statusCell.length > 0) {
                            belumPeriksa++;
                        }
                    });
                    $('#belum-periksa').text(belumPeriksa);
                }
            });
        }

        // Function to get current filter date
        function getCurrentFilterDate() {
            // Ambil tanggal dari filter Livewire jika ada
            const dateFilter = $('input[type="date"]').val();
            return dateFilter || '{{ date('Y-m-d') }}';
        }
        
        // Initial statistics update
        setTimeout(updateStatistics, 1000);
        
        // Update statistics when search is performed
        $('.search-input').on('keyup', function() {
            setTimeout(updateStatistics, 100);
        });
        
        // Function to initialize filter on page load
        function initializeFilter() {
            // Cek apakah ada filter yang tersimpan di localStorage
            const savedFilterState = localStorage.getItem('poliFilterState');
            if (savedFilterState) {
                const filterState = JSON.parse(savedFilterState);
                if (filterState.isLocked && filterState.value) {
                    // Restore locked filter state
                    isFilterLocked = true;
                    lockedPoliValue = filterState.value;
                    
                    // Update UI
                    $('#filter-poli').val(lockedPoliValue).trigger('change.select2');
                    $('#lock-icon').removeClass('fa-unlock').addClass('fa-lock');
                    $('#lock-filter').removeClass('btn-outline-primary').addClass('btn-success');
                    $('#lock-filter').attr('title', 'Unlock Filter');
                    $('.filter-card').addClass('filter-locked');
                    $('#filter-poli').prop('disabled', true);
                    $('#filter-status').show();
                    
                    const selectedText = $('#filter-poli').find('option:selected').text();
                    $('#filter-status-text').text(`Filter terkunci pada: ${selectedText}`);
                    
                    // Apply filter
                    setTimeout(() => {
                        applyPoliFilter(lockedPoliValue);
                    }, 500);
                }
            }
        }
        
        // Initialize filter after page load
        setTimeout(initializeFilter, 1500);

        // Filter Poli Functionality
        let isFilterLocked = false;
        let lockedPoliValue = '';

        // Initialize Select2 for filter poli (menggunakan data yang sudah ada)
        $('#filter-poli').select2({
            placeholder: 'Pilih Poliklinik',
            allowClear: true,
            width: '100%'
        });
        
        // Debug: Log when filter is initialized
        console.log('Filter poli initialized with Select2');

        // Handle filter poli change
        $('#filter-poli').on('change', function() {
            const selectedValue = $(this).val();
            
            if (!isFilterLocked) {
                applyPoliFilter(selectedValue);
                updateStatistics(selectedValue);
            } else {
                // Jika filter terkunci, kembalikan ke nilai yang terkunci
                $(this).val(lockedPoliValue).trigger('change.select2');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Terkunci',
                        text: 'Filter poliklinik sedang terkunci. Buka kunci terlebih dahulu untuk mengubah filter.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });

        // Handle lock/unlock filter
        $('#lock-filter').on('click', function() {
            const $button = $(this);
            const $icon = $('#lock-icon');
            const $card = $('.filter-card');
            const $status = $('#filter-status');
            const $statusText = $('#filter-status-text');
            const $select = $('#filter-poli');

            if (!isFilterLocked) {
                // Lock filter
                isFilterLocked = true;
                lockedPoliValue = $select.val();
                
                $icon.removeClass('fa-unlock').addClass('fa-lock');
                $button.removeClass('btn-outline-primary').addClass('btn-success');
                $button.attr('title', 'Unlock Filter');
                $card.addClass('filter-locked');
                $select.prop('disabled', true);
                $status.show();
                
                if (lockedPoliValue) {
                    const selectedText = $select.find('option:selected').text();
                    $statusText.text(`Filter terkunci pada: ${selectedText}`);
                } else {
                    $statusText.text('Filter terkunci pada: Semua Poliklinik');
                }
                
                // Apply filter immediately when locked
                applyPoliFilter(lockedPoliValue);
                
                // Save filter state to localStorage
                localStorage.setItem('poliFilterState', JSON.stringify({
                    isLocked: true,
                    value: lockedPoliValue
                }));
                
                // Show notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Filter Terkunci',
                        text: 'Filter poliklinik telah dikunci. Data akan tetap menampilkan poliklinik yang dipilih.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                // Unlock filter
                isFilterLocked = false;
                lockedPoliValue = '';
                
                $icon.removeClass('fa-lock').addClass('fa-unlock');
                $button.removeClass('btn-success').addClass('btn-outline-primary');
                $button.attr('title', 'Lock Filter');
                $card.removeClass('filter-locked');
                $select.prop('disabled', false);
                $status.hide();
                
                // Remove filter state from localStorage
                localStorage.removeItem('poliFilterState');
                
                // Show notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Filter Dibuka',
                        text: 'Filter poliklinik telah dibuka. Anda dapat mengubah filter kembali.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });

        // Handle reset filter
        $('#reset-filter').on('click', function() {
            if (!isFilterLocked) {
                $('#filter-poli').val('').trigger('change');
                
                // Refresh Livewire component langsung
                const tableComponent = window.livewire.find('reg-periksa-table');
                if (tableComponent) {
                    tableComponent.call('refreshData');
                } else {
                    // Fallback: emit event untuk refresh
                    Livewire.emit('refreshDatatable');
                }
                
                // Apply filter dan update statistik
                setTimeout(() => {
                    applyPoliFilter('');
                    updateStatistics('');
                }, 300);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Filter Direset',
                        text: 'Filter poliklinik telah direset ke "Semua Poliklinik".',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Terkunci',
                        text: 'Buka kunci filter terlebih dahulu untuk mereset.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });

        // Function to apply poli filter
        function applyPoliFilter(poliValue) {
            console.log('applyPoliFilter called with value:', poliValue);
            
            // Cari komponen Livewire table dengan cara yang lebih spesifik
            const tableElement = document.querySelector('[wire\\:id]');
            
            if (tableElement && window.Livewire) {
                const wireId = tableElement.getAttribute('wire:id');
                console.log('Found table element with wire:id:', wireId);
                const tableComponent = window.Livewire.find(wireId);
                
                if (tableComponent) {
                    console.log('Livewire component found, applying filter via Livewire:', poliValue);
                    
                    // Gunakan method filterByPoliklinik yang sudah ada di komponen
                    tableComponent.call('filterByPoliklinik', poliValue);
                    
                    // Update statistik dengan filter
                    setTimeout(() => {
                        updateStatistics(poliValue);
                    }, 500); // Delay untuk memastikan filter sudah diterapkan
                    
                    return;
                } else {
                    console.log('Livewire component not found for wireId:', wireId);
                }
            } else {
                console.log('Table element or Livewire not found, tableElement:', !!tableElement, 'Livewire:', !!window.Livewire);
            }
            
            // Fallback: filter manual pada tabel yang sudah ada
            console.log('Using fallback manual filter:', poliValue);
            
            if (poliValue === '') {
                $('.table tbody tr').show();
                console.log('Showing all rows (no filter)');
            } else {
                let visibleCount = 0;
                $('.table tbody tr').each(function() {
                    const poliCell = $(this).find('td:nth-child(8)'); // Kolom poliklinik
                    const poliText = poliCell.text().trim();
                    
                    // Cari berdasarkan nama poli atau kode poli
                    if (poliText.toLowerCase().includes(poliValue.toLowerCase()) || 
                        $(this).data('poli-code') === poliValue) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });
                console.log(`Manual filter applied. Visible rows: ${visibleCount}`);
            }
            
            // Update statistik setelah filter manual
            updateStatistics(poliValue);
        }
        
        // Event listener untuk menangkap respons BPJS saat klik menu aksi hadir/belum
        document.addEventListener('livewire:load', function () {
            // Tangkap event sebelum request dikirim
            Livewire.hook('message.sent', (message, component) => {
                // Cek apakah ini adalah call untuk updateStatusAntreanBPJS
                if (message.updateQueue && message.updateQueue.some(update => 
                    update.method === 'updateStatusAntreanBPJS'
                )) {
                    const updateCall = message.updateQueue.find(update => 
                        update.method === 'updateStatusAntreanBPJS'
                    );
                    const [noRawat, status] = updateCall.payload.params;
                    const statusText = status == 1 ? 'Hadir' : 'Tidak Hadir';
                    
                    console.group('🏥 BPJS API Request - Status Antrean');
                    console.log('📋 No Rawat:', noRawat);
                    console.log('📊 Status:', statusText + ' (' + status + ')');
                    console.log('⏰ Waktu Request:', new Date().toLocaleString());
                    console.groupEnd();
                }
            });
            
            // Tangkap event setelah response diterima
            Livewire.hook('message.processed', (message, component) => {
                // Cek apakah ada flash message yang mengindikasikan respons BPJS
                if (component.serverMemo.data.flashMessages) {
                    const flashMessages = component.serverMemo.data.flashMessages;
                    
                    // Cek untuk success message BPJS
                    if (flashMessages.success && flashMessages.success.includes('Status antrean BPJS')) {
                        console.group('✅ BPJS API Response - SUCCESS');
                        console.log('📝 Message:', flashMessages.success);
                        console.log('⏰ Waktu Response:', new Date().toLocaleString());
                        console.log('🎯 Status: Berhasil mengupdate status antrean BPJS');
                        console.groupEnd();
                    }
                    
                    // Cek untuk error message BPJS
                    if (flashMessages.error && flashMessages.error.includes('BPJS')) {
                        console.group('❌ BPJS API Response - ERROR');
                        console.log('📝 Error Message:', flashMessages.error);
                        console.log('⏰ Waktu Response:', new Date().toLocaleString());
                        console.log('🚨 Status: Gagal mengupdate status antrean BPJS');
                        console.groupEnd();
                    }
                }
            });
            
            // Event listener khusus untuk klik menu BPJS
             $(document).on('click', '[wire\\:click*="updateStatusAntreanBPJS"]', function() {
                 const wireClick = $(this).attr('wire:click');
                 const match = wireClick.match(/updateStatusAntreanBPJS\('([^']+)',\s*(\d+)\)/);
                 
                 if (match) {
                     const noRawat = match[1];
                     const status = match[2];
                     const statusText = status == '1' ? 'Hadir' : 'Tidak Hadir';
                     const patientName = $(this).closest('tr').find('td:nth-child(3)').text().trim();
                     
                     console.group('🔄 BPJS Menu Action Clicked');
                     console.log('👤 Nama Pasien:', patientName);
                     console.log('📋 No Rawat:', noRawat);
                     console.log('📊 Action:', 'Update Status ke ' + statusText);
                     console.log('⏰ Waktu Klik:', new Date().toLocaleString());
                     console.log('🔗 Mengirim request ke BPJS API...');
                     console.groupEnd();
                 }
             });
             
             // Event listener untuk menangkap klik tombol batal antrean
             $(document).on('click', '[onclick*="batalAntrean"]', function() {
                 const onclickAttr = $(this).attr('onclick');
                 const match = onclickAttr.match(/batalAntrean\('([^']+)',\s*'([^']+)'\)/);
                 
                 if (match) {
                     const noRawat = match[1];
                     const namaPasien = match[2];
                     
                     console.group('🚫 BPJS Batal Antrean Action Clicked');
                     console.log('👤 Nama Pasien:', namaPasien);
                     console.log('📋 No Rawat:', noRawat);
                     console.log('📊 Action:', 'Batal Antrean BPJS');
                     console.log('⏰ Waktu Klik:', new Date().toLocaleString());
                     console.log('🔗 Menunggu input alasan pembatalan...');
                     console.groupEnd();
                 }
             });
             
             // Event listener untuk menangkap respons detail BPJS
             Livewire.on('bpjsResponseReceived', (data) => {
                 if (data.success) {
                     console.group('🎉 BPJS API Response - DETAIL SUCCESS');
                     console.log('👤 Nama Pasien:', data.patient_name);
                     console.log('📋 No Rawat:', data.no_rawat);
                     console.log('📊 Status Update:', data.status_text);
                     console.log('⏰ Timestamp:', data.timestamp);
                     console.log('📤 Request Data:', data.request_data);
                     console.log('📥 Response Data:', data.response_data);
                     console.log('✅ Status: SUCCESS - Data berhasil dikirim ke BPJS');
                     console.groupEnd();
                 } else {
                     console.group('💥 BPJS API Response - DETAIL ERROR');
                     console.log('👤 Nama Pasien:', data.patient_name);
                     console.log('📋 No Rawat:', data.no_rawat);
                     console.log('📊 Status Update:', data.status_text);
                     console.log('⏰ Timestamp:', data.timestamp);
                     console.log('📤 Request Data:', data.request_data);
                     console.log('📥 Response Data:', data.response_data);
                     console.log('❌ Status: ERROR - Gagal mengirim data ke BPJS');
                     if (data.response_data.error_message) {
                         console.log('🚨 Error Message:', data.response_data.error_message);
                     }
                     console.groupEnd();
                 }
             });
             
             // Event listener untuk menangkap respons batal antrean BPJS
             Livewire.on('bpjsBatalAntreanResponse', (data) => {
                 if (data.success) {
                     console.group('🎉 BPJS Batal Antrean Response - SUCCESS');
                     console.log('👤 Nama Pasien:', data.patient_name);
                     console.log('📋 No Rawat:', data.no_rawat);
                     console.log('📊 Action:', 'Batal Antrean BPJS');
                     console.log('💬 Alasan:', data.alasan);
                     console.log('⏰ Timestamp:', data.timestamp);
                     console.log('📤 Request Data:', data.request_data);
                     console.log('📥 Response Data:', data.response_data);
                     console.log('✅ Status: SUCCESS - Antrean berhasil dibatalkan di BPJS');
                     console.groupEnd();
                     
                     // Show success notification
                     if (typeof Swal !== 'undefined') {
                         Swal.fire({
                             icon: 'success',
                             title: 'Antrean Dibatalkan',
                             text: `Antrean BPJS untuk ${data.patient_name} berhasil dibatalkan.`,
                             timer: 3000,
                             showConfirmButton: false
                         });
                     }
                 } else {
                     console.group('💥 BPJS Batal Antrean Response - ERROR');
                     console.log('👤 Nama Pasien:', data.patient_name);
                     console.log('📋 No Rawat:', data.no_rawat);
                     console.log('📊 Action:', 'Batal Antrean BPJS');
                     console.log('💬 Alasan:', data.alasan);
                     console.log('⏰ Timestamp:', data.timestamp);
                     console.log('📤 Request Data:', data.request_data);
                     console.log('📥 Response Data:', data.response_data);
                     console.log('❌ Status: ERROR - Gagal membatalkan antrean di BPJS');
                     if (data.response_data && data.response_data.error_message) {
                         console.log('🚨 Error Message:', data.response_data.error_message);
                     }
                     console.groupEnd();
                     
                     // Show error notification
                     if (typeof Swal !== 'undefined') {
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal Membatalkan Antrean',
                             text: data.response_data && data.response_data.error_message ? 
                                   data.response_data.error_message : 
                                   'Terjadi kesalahan saat membatalkan antrean BPJS.',
                             confirmButtonText: 'OK'
                         });
                     }
                 }
             });
        });
    });
</script>
@stop