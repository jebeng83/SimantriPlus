@extends('adminlte::page')

@section('title', 'Kunjungan PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-user-injured text-primary"></i> Kunjungan PCare</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Kunjungan PCare</li>
    </ol>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Kunjungan</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Informasi</h5>
                    Halaman kunjungan PCare sedang dalam pengembangan.
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        console.log('Kunjungan PCare page loaded');
    });
</script>
@stop