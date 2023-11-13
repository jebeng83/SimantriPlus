@extends('adminlte::page')

@section('title', 'Master Laporan Operasi')

@section('content_header')
    <h1>Master Laporan Operasi</h1>
@stop

@section('content')
    <x-adminlte-card>
        <livewire:component.form-master-operasi />
    </x-adminlte-card>
    <x-adminlte-card>
        <livewire:component.table-master-operasi />
    </x-adminlte-card>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    
@stop
