@extends('adminlte::page')

@section('title', 'Register Pasien')

@section('content_header')
<div class="registrasi-header">
    <h1 class="registrasi-title">Registrasi Pasien</h1>
    <button class="registrasi-btn registrasi-btn-primary btn-register" data-toggle="modal"
        data-target="#modalPendaftaran">
        <i class="fas fa-user-plus registrasi-btn-icon"></i>Register
    </button>
</div>
@stop

@section('content')
<div class="registrasi-container">
    <div class="filter-section">
        <div class="search-box">
            <input type="text" class="search-input"
                placeholder="Cari pasien berdasarkan nama atau nomor rekam medis...">
        </div>
        <div class="filter-controls">
            <div class="filter-dropdown">
                <button class="filter-button">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
            <div class="length-dropdown">
                <select>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div id="loading-container" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 font-weight-bold">Memuat data pasien...</p>
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

    .table .status-badge.bpjs {
        background-color: rgba(79, 209, 197, 0.15);
        color: #2dd4bf;
        padding: 0.25rem 0.75rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .table .status-badge.umum {
        background-color: rgba(99, 102, 241, 0.15);
        color: #6366f1;
        padding: 0.25rem 0.75rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Peningkatan tampilan dropdown menu */
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
                    
                    $('.table tbody tr').each(function() {
                        const jenisBayarCell = $(this).find('td:nth-child(10)');
                        const jenisBayar = jenisBayarCell.text().trim();
                        
                        if (jenisBayar.toLowerCase().includes('bpjs')) {
                            jenisBayarCell.html('<span class="status-badge bpjs">' + jenisBayar + '</span>');
                        } else if (jenisBayar.toLowerCase().includes('umum')) {
                            jenisBayarCell.html('<span class="status-badge umum">' + jenisBayar + '</span>');
                        }
                    });
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
        });
    });
</script>
@stop