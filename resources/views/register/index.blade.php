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
    <!-- Filter akan ditampilkan oleh Livewire Tables component -->

    <div id="loading-container" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 font-weight-bold">Memuat data registrasi pasien...</p>
    </div>

    <div id="table-container" class="datatable-wrapper">
        <!-- Filter Lock Toggle -->
        <div class="mb-3 d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="filter-lock-btn"
                onclick="toggleFilterLock()" title="Kunci filter untuk mempertahankan setelah refresh">
                <i class="fas fa-lock" id="lock-icon"></i>
                <span id="lock-text">Kunci Filter</span>
            </button>
        </div>

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
    /* Filter Lock Button Styling */
    #filter-lock-btn {
        border: 1px solid #6c757d;
        background-color: transparent;
        color: #6c757d;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }

    #filter-lock-btn:hover {
        background-color: #6c757d;
        color: white;
    }

    #filter-lock-btn.btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    #filter-lock-btn.btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        color: #212529;
    }

    #filter-lock-btn i {
        margin-right: 0.5rem;
    }
</style>
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

    /* Enhanced Filter Card Styles */
    .filter-card-enhanced {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        overflow: visible;
    }

    .filter-card-enhanced:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .filter-card-enhanced .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
        padding: 1rem 1.5rem;
    }

    .filter-card-enhanced .card-body {
        padding: 1.5rem;
    }

    .filter-card-enhanced .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-card-enhanced .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .filter-card-enhanced .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .poli-lock-section {
        border-top: 1px solid #e2e8f0;
        padding-top: 1rem;
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

    #apply-filters {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    #apply-filters:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }

    /* Enhanced Filter Responsive Styles */
    .filter-actions .btn {
        min-height: 38px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .filter-actions .flex-fill {
        min-width: 120px;
    }

    .poli-lock-section {
        border-color: #e2e8f0 !important;
    }

    .filter-card-enhanced .row.g-3>* {
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }



    /* Responsive */
    @media (max-width: 768px) {
        .filter-actions {
            flex-direction: column;
        }

        .filter-actions .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .filter-actions .btn:last-child {
            margin-bottom: 0;
        }

        .poli-lock-section .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .poli-lock-section #filter-status {
            width: 100%;
            margin-top: 0.5rem;
            margin-left: 0 !important;
        }


    }

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
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const alasan = result.value;
                        
                        // Emit event ke Livewire component
                        const tableElement = document.querySelector('[wire\\:id]');
                        if (tableElement && window.Livewire) {
                            const wireId = tableElement.getAttribute('wire:id');
                            const tableComponent = window.Livewire.find(wireId);
                            if (tableComponent) {
                                tableComponent.call('batalAntreanBPJS', noRawat, alasan);
                            } else {
                                Livewire.emit('batalAntreanBPJS', noRawat, alasan);
                            }
                        } else {
                            Livewire.emit('batalAntreanBPJS', noRawat, alasan);
                        }
                    }
                });
            } else {
                // Fallback jika SweetAlert tidak tersedia
                const alasan = prompt(`Masukkan alasan pembatalan antrean untuk ${namaPasien}:`);
                if (alasan && alasan.trim().length >= 10) {
                    const tableElement = document.querySelector('[wire\\:id]');
                    if (tableElement && window.Livewire) {
                        const wireId = tableElement.getAttribute('wire:id');
                        const tableComponent = window.Livewire.find(wireId);
                        if (tableComponent) {
                            tableComponent.call('batalAntreanBPJS', noRawat, alasan.trim());
                        } else {
                            Livewire.emit('batalAntreanBPJS', noRawat, alasan.trim());
                        }
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
                    updateStatistics();
                }, 100);
            });
            
            // Event listener untuk registrasi berhasil
            Livewire.on('registrationSuccess', (data) => {
                // Refresh Livewire component langsung
                const tableElement = document.querySelector('[wire\\:id]');
                if (tableElement && window.Livewire) {
                    const wireId = tableElement.getAttribute('wire:id');
                    const tableComponent = window.Livewire.find(wireId);
                    if (tableComponent) {
                        tableComponent.call('refreshData');
                    } else {
                        Livewire.emit('refreshDatatable');
                    }
                } else {
                    Livewire.emit('refreshDatatable');
                }
                
                // Refresh data table dan statistik
                setTimeout(() => {
                    updateStatistics();
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
                // Refresh Livewire component langsung
                const tableElement = document.querySelector('[wire\\:id]');
                if (tableElement && window.Livewire) {
                    const wireId = tableElement.getAttribute('wire:id');
                    const tableComponent = window.Livewire.find(wireId);
                    if (tableComponent) {
                        tableComponent.call('refreshData');
                    }
                }
                
                setTimeout(() => {
                    updateStatistics();
                }, 300);
            });
            
            // Handle session expired errors
            Livewire.hook('message.failed', (message, component) => {
                if (message.response && message.response.includes('This page has expired')) {
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
            if ($(this).attr('wire:click') || $(this).attr('href')) {
                const $button = $(this).closest('.btn-group').find('.dropdown-toggle');
                const originalText = $button.html();
                
                if (!$(this).attr('href')) {
                    $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                    
                    setTimeout(function() {
                        $button.html(originalText);
                    }, 1000);
                }
            }
        });
        
        // Function to update statistics
        function updateStatistics(kdPoli = null) {
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
            const dateFilter = $('input[type="date"]').val();
            return dateFilter || '{{ date('Y-m-d') }}';
        }
        
        // Initial statistics update
        setTimeout(updateStatistics, 1000);
        
        // Update statistics when search is performed
        $('.search-input').on('keyup', function() {
            setTimeout(updateStatistics, 100);
        });

        // Initialize Select2 for filters
        $('#filter-poli').select2({
            placeholder: 'Pilih Poliklinik',
            allowClear: true,
            width: '100%'
        });
        
        $('#filter-status').select2({
            placeholder: 'Pilih Status',
            allowClear: true,
            width: '100%'
        });
        
        // Set default date values
        const today = new Date().toISOString().split('T')[0];
        $('#filter-date-from').val(today);
        $('#filter-date-to').val(today);

        // Handle filter poli change
        $('#filter-poli').on('change', function() {
            const selectedValue = $(this).val();
            applyPoliFilter(selectedValue);
            updateStatistics(selectedValue);
        });

        // Handle reset filter
        $('#reset-filter').on('click', function() {
            $('#filter-poli').val('').trigger('change');
            
            const tableElement = document.querySelector('[wire\\:id]');
            if (tableElement && window.Livewire) {
                const wireId = tableElement.getAttribute('wire:id');
                const tableComponent = window.Livewire.find(wireId);
                if (tableComponent) {
                    tableComponent.call('resetFilters');
                } else {
                    Livewire.emit('resetFilters');
                }
            } else {
                Livewire.emit('resetFilters');
            }
            
            setTimeout(() => {
                applyPoliFilter('');
                updateStatistics('');
            }, 300);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Filter Direset',
                    text: 'Semua filter telah direset ke nilai default.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });

        // Function to apply poli filter
        function applyPoliFilter(poliValue) {
            const tableElement = document.querySelector('[wire\\:id]');
            
            if (tableElement && window.Livewire) {
                const wireId = tableElement.getAttribute('wire:id');
                const tableComponent = window.Livewire.find(wireId);
                
                if (tableComponent) {
                    tableComponent.call('filterByPoliklinik', poliValue);
                    
                    setTimeout(() => {
                        updateStatistics(poliValue);
                    }, 500);
                    
                    return;
                }
            }
            
            // Fallback: filter manual pada tabel yang sudah ada
            if (poliValue === '' || poliValue === null || poliValue === undefined) {
                $('.table tbody tr').show();
            } else {
                $('.table tbody tr').each(function() {
                    const poliCell = $(this).find('td:nth-child(8)');
                    const poliText = poliCell.text().trim();
                    
                    if (poliText.toLowerCase().includes(poliValue.toLowerCase()) || 
                        $(this).data('poli-code') === poliValue) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            
            updateStatistics(poliValue);
        }
        
        // Event listener untuk menangkap respons BPJS
        document.addEventListener('livewire:load', function () {
            // Event listener untuk menangkap respons detail BPJS
            Livewire.on('bpjsResponseReceived', (data) => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil dikirim ke BPJS',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.response_data && data.response_data.error_message ? 
                                  data.response_data.error_message : 
                                  'Terjadi kesalahan saat mengirim data ke BPJS.',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
            
            // Event listener untuk menangkap respons batal antrean BPJS
            Livewire.on('bpjsBatalAntreanResponse', (data) => {
                if (data.success) {
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

    // Simplified Filter Lock Functionality
    let filterLockInitialized = false;
    
    function toggleFilterLock() {
        Livewire.emit('toggleFilterLock');
    }

    // Listen for filter lock status updates
    Livewire.on('filterLockUpdated', function(isLocked) {
        updateFilterLockUI(isLocked);
    });
    
    // Listen for filters loaded event
    Livewire.on('filters-loaded', function(data) {
        if (typeof toastr !== 'undefined' && data.message) {
            toastr.success(data.message);
        }
    });
    
    // Listen for component mounted event
    Livewire.on('component-mounted', function(data) {
        updateFilterLockUI(data.isFilterLocked);
    });
    
    // Listen for filter lock toggle messages
    Livewire.on('filterLockToggled', function(data) {
        if (typeof toastr !== 'undefined' && data.message) {
            if (data.locked) {
                toastr.success(data.message);
            } else {
                toastr.info(data.message);
            }
        }
    });
    
    // Listen for filter lock active messages
    Livewire.on('filterLockActive', function(message) {
        if (typeof toastr !== 'undefined') {
            toastr.warning(message);
        }
    });

    function updateFilterLockUI(isLocked) {
        const btn = document.getElementById('filter-lock-btn');
        const icon = document.getElementById('lock-icon');
        const text = document.getElementById('lock-text');
        
        if (!btn || !icon || !text) {
            return;
        }
        
        if (isLocked) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-warning');
            icon.classList.remove('fa-unlock');
            icon.classList.add('fa-lock');
            text.textContent = 'Filter Terkunci';
            btn.title = 'Filter terkunci - Klik untuk membuka';
        } else {
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-secondary');
            icon.classList.remove('fa-lock');
            icon.classList.add('fa-unlock');
            text.textContent = 'Kunci Filter';
            btn.title = 'Kunci filter untuk mempertahankan setelah refresh';
        }
    }
    
    // Initialize filter lock when Livewire is ready
    function initializeFilterLock() {
        if (filterLockInitialized) {
            return;
        }
        
        const checkLivewire = setInterval(() => {
            const tableElement = document.querySelector('[wire\\:id]');
            if (window.Livewire && tableElement) {
                const wireId = tableElement.getAttribute('wire:id');
                const tableComponent = window.Livewire.find(wireId);
                if (tableComponent) {
                    clearInterval(checkLivewire);
                    
                    Livewire.emit('initializeComponent');
                    
                    setTimeout(() => {
                        Livewire.emit('getFilterLockStatus');
                    }, 300);
                    
                    filterLockInitialized = true;
                }
            }
        }, 100);
        
        setTimeout(() => {
            if (!filterLockInitialized) {
                clearInterval(checkLivewire);
            }
        }, 5000);
    }

    // Single initialization point
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            initializeFilterLock();
        }, 1000);
    });
    
    // Re-initialize when Livewire loads
    document.addEventListener('livewire:load', function() {
        filterLockInitialized = false;
        setTimeout(() => {
            initializeFilterLock();
        }, 500);
    });
</script>
@stop