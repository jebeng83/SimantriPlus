@extends('adminlte::page')

@section('title', 'Tambah Pasien Baru')

@section('content_header')
    <h1>Tambah Pasien Baru</h1>
@stop

@section('content')
    <x-adminlte-card title="Form Pendaftaran Pasien" theme="primary" icon="fas fa-user-plus">
        <p class="text-muted mb-3">Silakan lengkapi data di bawah ini untuk menambahkan pasien baru.</p>
        <livewire:pasien.form-pendaftaran />
        <div class="mt-3">
            <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Kembali ke Data Pasien</a>
        </div>
    </x-adminlte-card>
@stop

@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)

@section('css')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@stop

@section('js')
<script>
    // Optional: Focus first input when page loads
    window.addEventListener('load', function() {
        const input = document.querySelector('input, select, textarea');
        if (input) {
            input.focus();
        }
    });
</script>
@stop