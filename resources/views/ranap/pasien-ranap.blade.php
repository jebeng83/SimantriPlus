@extends('adminlte::page')

@section('title', 'Pasien Ranap')

@section('content_header')
    <h1>Pasien Ranap</h1>
@stop

@section('content')
<form action="{{route('ralan.pasien')}}" method="GET">
    <x-adminlte-callout theme="info" >
        @php
            $config["responsive"] = true;
        @endphp
        {{-- Minimal example / fill data using the component slot --}}
        <x-adminlte-datatable id="tablePasienRanap" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
            @foreach($data as $row)
                @php
                    $noRawat = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rawat);
                    $noRM = App\Http\Controllers\Ranap\PasienRanapController::encryptData($row->no_rkm_medis);
                @endphp
                <tr>
                    <td> 
                        <a class="text-primary" href="{{route('ranap.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}}">
                            {{$row->nm_pasien}}
                        </a>
                    </td>
                    <td>{{$row->no_rkm_medis}}</td>
                    <td>{{$row->nm_bangsal}}</td>
                    <td>{{$row->kd_kamar}}</td>
                    <td>{{$row->tgl_masuk}}</td>
                    <td>{{$row->png_jawab}}</td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
        
    </x-adminlte-callout>
</form>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
@stop
