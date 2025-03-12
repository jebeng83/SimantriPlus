@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ranap')

@section('content_header')
<div class="content-header-container">
    <style>
        .content-header-container {
            padding: 15px 0;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px;
        }

        .header-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #2c3e50;
            position: relative;
            padding-left: 15px;
            margin: 0;
            font-size: 1.8rem;
        }

        .header-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 4px;
            background: linear-gradient(to bottom, #3498db, #2c3e50);
            border-radius: 2px;
        }

        .back-button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background: linear-gradient(135deg, #2980b9, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .back-button i {
            margin-right: 5px;
        }
    </style>
    <div class="header-wrapper">
        <h1 class="header-title">CPPT Pasien Rawat Inap</h1>
        <a class="back-button" href="{{ url('ranap/pasien') }}" role="button">
            <i class="fas fa-chevron-left"></i> Kembali ke Daftar Pasien
        </a>
    </div>
</div>
@stop

@section('content')
<x-ranap.riwayat-ranap :no-rawat="request()->get('no_rawat')" />
<div class="row">
    <div class="col-md-4">
        <x-ranap.pasien :no-rawat="request()->get('no_rawat')" />
    </div>
    <div class="col-md-8">
        <x-ranap.pemeriksaan-ranap :no-rawat="request()->get('no_rawat')" />
        <x-ranap.resep-ranap />
        <livewire:ranap.permintaan-lab :no-rawat="request()->get('no_rawat')" />
        <livewire:ranap.resume-pasien :no-rawat="request()->get('no_rawat')" />
        <livewire:ranap.catatan-pasien :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
        <!--<livewire:ranap.permintaan-radiologi :no-rawat="request()->get('no_rawat')" -->
        <!--<x-adminlte-card title="Laporan Operasi" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">-->
        <!--    <livewire:ranap.lap-operasi :no-rawat="request()->get('no_rawat')" -->
        <!--    <livewire:ranap.template-lap-operasi -->
        <!--</x-adminlte-card>-->
        <x-adminlte-card title="SBAR" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">
            <livewire:ranap.sbar.detail-sbar />
            <livewire:ranap.sbar.table-sbar :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card>
    </div>
</div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
<script>
    console.log('Hi!');
    
    // Menghapus semua elemen yang mungkin berisi teks debug
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus elemen dengan ID btn-toggle-debug (yang terlihat di pojok kanan bawah)
        var debugButton = document.getElementById('btn-toggle-debug');
        if (debugButton) {
            debugButton.remove();
        }
        
        // Hapus elemen dengan ID debug
        var debugElements = document.querySelectorAll('[id*="debug"],[class*="debug"],[class*="Debug"]');
        debugElements.forEach(function(el) {
            el.style.display = 'none';
        });
        
        // Cari semua elemen yang mengandung tulisan "Debug"
        var allElements = document.getElementsByTagName('*');
        for (var i = 0; i < allElements.length; i++) {
            var el = allElements[i];
            if (el.textContent === 'Debug') {
                el.style.display = 'none';
            }
        }
    });
</script>
@stop

@section('css')
<style>
    /* Sembunyikan elemen debug */
    .debug,
    .Debug,
    [class*="debug"] {
        display: none !important;
    }

    /* Sembunyikan tombol Debug yang terlihat di pojok kanan bawah */
    #btn-toggle-debug {
        display: none !important;
    }

    /* Tambahan untuk menargetkan elemen spesifik yang mungkin berisi teks Debug */
    .sbar-debug,
    .debug-panel,
    .position-fixed[style*="bottom: 10px"][style*="right: 10px"] {
        display: none !important;
    }
</style>
@stop