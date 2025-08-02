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
                    <div class="stat-number" id="total-pasien">0</div>
                    <div class="stat-label">Total Pasien</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number" id="belum-periksa">0</div>
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
    <div class="info-cards-section mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Informasi:</strong> Data yang ditampilkan adalah registrasi pasien untuk hari ini ({{ date('d F Y') }}). 
                    Gunakan filter tanggal untuk melihat data hari lain.
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
        <livewire:reg-periksa-table />
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
    .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-bottom: 0;
        padding: 1.25rem 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
    }

    .modal-body {
        padding: 1.5rem;
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
                    updateStatistics();
                }, 100);
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
        function updateStatistics() {
            // Count total rows in table
            const totalRows = $('.table tbody tr:visible').length;
            $('#total-pasien').text(totalRows);
            
            // Count pending patients (status 'Belum')
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
        
        // Initial statistics update
        setTimeout(updateStatistics, 1000);
        
        // Update statistics when search is performed
        $('.search-input').on('keyup', function() {
            setTimeout(updateStatistics, 100);
        });
    });
</script>
@stop