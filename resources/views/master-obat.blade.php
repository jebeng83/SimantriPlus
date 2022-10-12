@extends('adminlte::page')

@section('title', 'Master Obat')

@section('content_header')
    <h1>Master Obat</h1>
@stop

@section('content')
    @php
        $config["responsive"] = true;
    @endphp
    <x-adminlte-datatable id="tableMasterObat" :heads="$heads" head-theme="dark" striped hoverable bordered compressed>
        @foreach($obat as $row)
            <tr>
                <td>{{$row->kode_brng}}</td>
                <td>{{$row->nama_brng}}</td>
                <td>{{$row->stok}}</td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    
@stop
