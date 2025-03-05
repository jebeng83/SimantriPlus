@extends('adminlte::page')

@section('title', 'Register Pasien')

@section('content_header')
<h1></h1>
@stop

@section('content')
<x-adminlte-card>
    <div class="d-flex flex-row-reverse mb-3">
        <x-adminlte-button label="Register" theme="primary" icon="fas fa-user-plus" class="btn-register"
            data-toggle="modal" data-target="#modalPendaftaran" />
    </div>
    <div class="dropdown-divider mb-3"></div>
    <div id="loading-container" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 text-primary font-weight-bold">Memuat data pasien...</p>
    </div>
    <div id="table-container">
        <livewire:reg-periksa-table />
    </div>
</x-adminlte-card>
<x-adminlte-modal id="modalPendaftaran" title="Pendaftaran" v-centered static-backdrop>
    <div id="modal-loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3 text-primary font-weight-bold">Mempersiapkan formulir pendaftaran...</p>
    </div>
    <div id="form-container">
        <livewire:registrasi.form-pendaftaran />
    </div>
    <x-slot name="footerSlot">
        {{--
        <x-adminlte-button theme="primary" label="Simpan" /> --}}
    </x-slot>
</x-adminlte-modal>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)
@section('plugins.Sweetalert2', true)

@section('css')
<style>
    /* Animasi loading yang lebih ringan */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Perbaikan tampilan tabel */
    .table {
        border-radius: 5px;
        overflow: hidden;
    }

    .table thead th {
        background-color: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4e73df !important;
        color: white !important;
        border: none !important;
        border-radius: 5px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):hover {
        background: rgba(78, 115, 223, 0.1) !important;
        color: #4e73df !important;
        border: none !important;
    }

    /* Spinner loading yang lebih ringan */
    .spinner-border {
        color: #4e73df;
    }

    /* Preloader overlay yang lebih ringan */
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.98);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.3s ease;
    }

    .preloader.fade-out {
        opacity: 0;
    }

    .btn-register {
        transition: all 0.2s ease;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 0.5rem 1.5rem;
        border-radius: 5px;
    }

    .btn-register:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .modal-content {
        animation: fadeIn 0.2s ease-out;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .modal-header {
        background-color: #4e73df;
        color: white;
        border-bottom: 0;
        padding: 1.25rem 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .modal-body {
        padding: 0;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1) !important;
    }

    /* Efek hover untuk baris tabel */
    .row-hover {
        background-color: rgba(78, 115, 223, 0.05) !important;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
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
        
        // Add preloader to page
        $('body').append('<div class="preloader"><div class="text-center"><div class="spinner-border text-primary mb-3" style="width: 2rem; height: 2rem;" role="status"><span class="sr-only">Loading...</span></div><h5 class="text-primary font-weight-bold">Memuat Aplikasi</h5></div></div>');
        
        // Remove preloader after page loads
        $(window).on('load', function() {
            setTimeout(function() {
                $('.preloader').addClass('fade-out');
                setTimeout(function() {
                    $('.preloader').remove();
                }, 300);
            }, 300);
        });
        
        // Optimize modal loading
        $('.btn-register').on('click', function() {
            $('#form-container').hide();
            $('#modal-loading').show();
            
            setTimeout(function() {
                $('#modal-loading').fadeOut(200, function() {
                    $('#form-container').fadeIn(200);
                });
            }, 500);
        });
        
        // Initialize Livewire hooks for smoother transitions
        document.addEventListener('livewire:load', function() {
            Livewire.hook('message.sent', () => {
                $('#table-container').hide();
                $('#loading-container').show();
            });
            
            Livewire.hook('message.processed', () => {
                $('#loading-container').hide();
                $('#table-container').show();
            });
        });
        
        // Preload resources in background
        setTimeout(preloadResources, 500);
        
        // Add confirmation for actions with SweetAlert2
        $(document).on('click', '.btn-action', function(e) {
            e.preventDefault();
            const action = $(this).data('action');
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin melakukan tindakan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#e74a3b',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with action
                    if (action === 'delete') {
                        Livewire.emit('deleteRecord', id);
                    } else if (action === 'edit') {
                        Livewire.emit('editRecord', id);
                    }
                }
            });
        });
        
        // Optimize table rendering with IntersectionObserver
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.card, .table').forEach(el => {
                observer.observe(el);
            });
        }
        
        // Tambahkan efek hover pada baris tabel
        $(document).on('mouseenter', '.table tbody tr', function() {
            $(this).addClass('row-hover');
        }).on('mouseleave', '.table tbody tr', function() {
            $(this).removeClass('row-hover');
        });
    });
</script>
@stop