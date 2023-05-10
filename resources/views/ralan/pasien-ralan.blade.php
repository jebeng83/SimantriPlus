@extends('adminlte::page')

@section('title', 'Pasien Ralan')

@section('content_header')
    <h1>Pasien Ralan</h1>
@stop

@section('content')
    <x-adminlte-callout theme="info" title="{{$nm_poli}}">
        <ul class="nav nav-tabs" id="tabRalan" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pasien-tab" data-toggle="tab" data-target="#pasien" type="button" role="tab" aria-controls="pasien" aria-selected="true">Pasien Ralan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rujuk-tab" data-toggle="tab" data-target="#rujuk" type="button" role="tab" aria-controls="rujuk" aria-selected="false">Rujuk Internal</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="pasien" role="tabpanel" aria-labelledby="pasien-tab">
                <x-adminlte-card theme="info">
                    @php
                        $config["responsive"] = true;
                    @endphp
                    {{-- Minimal example / fill data using the component slot --}}
                    <x-adminlte-datatable id="tablePasienRalan" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                        @foreach($data as $row)
                            <tr @if(!empty($row->diagnosa_utama)) class="bg-success" @endif >
                                <td>{{$row->no_reg}}</td>
                                <td> 
                                    @php
                                    $noRawat = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                                    $noRM = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                                    @endphp
                                    <a @if(!empty($row->diagnosa_utama)) class="text-white" @else class="text-primary" @endif href="{{route('ralan.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}} ">
                                        {{$row->nm_pasien}}
                                    </a>
                                </td>
                                <td>{{$row->no_rawat}}</td>
                                <td>{{$row->no_tlp}}</td>
                                <td>{{$row->nm_dokter}}</td>
                                <td>{{$row->stts}}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
            <div class="tab-pane fade" id="rujuk" role="tabpanel" aria-labelledby="rujuk-tab">
                <x-adminlte-card theme="info">
                    @php
                        $config["responsive"] = true;
                    @endphp
                    {{-- Minimal example / fill data using the component slot --}}
                    <x-adminlte-datatable id="tableRujuk" :heads="$headsInternal" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                        @foreach($dataInternal as $row)
                            <tr @if($row->stts == 'Sudah') class="bg-success" @endif >
                                <td>{{$row->no_reg}}</td>
                                <td>
                                    @php
                                    $noRawat = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                                    $noRM = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                                    @endphp
                                    <a @if($row->stts == 'Sudah') class="text-white" @else class="text-primary" @endif href="{{route('ralan.rujuk-internal', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}} ">{{$row->nm_pasien}}
                                    </a>
                                </td>
                                <td>{{$row->no_rkm_medis}}</td>
                                <td>{{$row->nm_dokter}}</td>
                                <td>{{$row->stts}}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        </div>
        <div class="row justify-content-end pr-2">
            <div class="md:col-3 sm:col-auto">
                @php
                $config = ['format' => 'YYYY-MM-DD'];
                @endphp
                <form action="{{route('ralan.pasien')}}" method="GET">
                <x-adminlte-input-date name="tanggal" value="{{$tanggal}}" :config="$config" placeholder="Pilih Tanggal...">
                    <x-slot name="appendSlot">
                        <x-adminlte-button class="btn-flat" type="submit" theme="primary" icon="fas fa-lg fa-search"/>
                    </x-slot>
                </x-adminlte-input-date>
                </form>
            </div>
        </div>
    </x-adminlte-callout>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
@stop
