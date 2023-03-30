@extends('adminlte::page')

@section('title', 'Master Obat')

@section('content_header')
    <h1>BOOKING {{ $nmPoli }}</h1>
@stop

@section('content')
    @php
        $config["responsive"] = true;
    @endphp
    <x-adminlte-card>
        <x-adminlte-datatable id="tableBooking" :heads="$heads" head-theme="dark" striped hoverable bordered compressed>
            @foreach($data as $row)
                <tr>
                    <td>{{$row->no_reg}}</td>
                    <td>{{$row->nm_pasien}}</td>
                    <td>{{$row->no_rkm_medis}}</td>
                    <td>{{ $row->tanggal_periksa }}</td>
                    <td>{{ $row->no_tlp }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ $row->png_jawab }}</td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
        <form action="{{ url('/booking') }}" method="GET">
        <div class="row mt-4">
            @php
                $config = ['format' => 'YYYY-MM-DD'];
            @endphp
            
                <div class="col-md-5">
                    <x-adminlte-input-date name="tglMulai" value="{{$tglMulai}}" :config="$config" placeholder="Pilih Tanggal...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-white">
                                <i class="far fa-lg fa-calendar-alt text-primary"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="col-md-6">
                    <x-adminlte-input-date name="tglAkhir" value="{{$tglAkhir}}" :config="$config" placeholder="Pilih Tanggal...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-white">
                                <i class="far fa-lg fa-calendar-alt text-danger"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                </div>
                <div class="col-md-1">
                    <x-adminlte-button theme="success" type="submit" icon="fas fa-lg fa-search" />
                </div>
            
        </div>
    </form>
    </x-adminlte-card>
@stop

@section('plugins.TempusDominusBs4', true)

