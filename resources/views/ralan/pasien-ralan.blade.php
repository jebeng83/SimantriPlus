@extends('adminlte::page')

@section('title', 'Pasien Ralan')

@section('content_header')
    <h1>Pasien Ralan</h1>
@stop

@section('content')
<form action="{{route('ralan.pasien')}}" method="GET">
    <x-adminlte-callout theme="info" title="{{$nm_poli}}">
        @php
            $config["responsive"] = true;
        @endphp
        {{-- Minimal example / fill data using the component slot --}}
        <x-adminlte-datatable id="tablePasienRalan" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
            @foreach($data as $row)
                <tr @if($row->stts == 'Sudah') class="bg-success" @endif >
                    <td>{{$row->no_reg}}</td>
                    <td> 
                        @php
                        $noRawat = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat);
                        $noRM = App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis);
                        @endphp
                        <a class="text-black" href="{{route('ralan.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}}">
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
        <div class="row justify-content-end pt-5">
            <div class="md:col-3 sm:col-auto">
                @php
                $config = ['format' => 'YYYY-MM-DD'];
                @endphp
                <x-adminlte-input-date name="tanggal" value="{{$tanggal}}" :config="$config" placeholder="Pilih Tanggal...">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-primary">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-date>
            </div>
            <div class="col-auto">
                <x-adminlte-button class="btn-flat" type="submit" label="Cari" theme="success" icon="fas fa-lg fa-search"/>
            </div>
        </div>
    </x-adminlte-callout>
</form>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop
