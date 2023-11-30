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
                                <td>
                                    <div class="dropdown">
                                        <button id="my-dropdown" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{$row->no_rawat}}</button>
                                        <div class="dropdown-menu" aria-labelledby="my-dropdown">
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-medis" wire:click="$emit('awalMedis')">Penilaian Awal Medis Umum</button>
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-tht">Penilaian Awal Medis THT</button>
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-anak">Penilaian Awal Medis Bayi/Anak</button>
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-kandungan">Penilaian Awal Medis Kandungan</button>
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-dalam">Penilaian Awal Medis Penyakit Dalam</button>
                                            <button id="{{$row->no_rawat}}" class="dropdown-item btn-awal-psikiatri">Penilaian Awal Medis Psikiatri</button>
                                        </div>
                                    </div>
                                </td>
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
    
    <x-adminlte-modal wire:ignore.self id="modal-awal-keperawatan" title="Penilaian Awal Medis Umum" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-ralan.form />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-tht" title="Penilaian Awal Medis THT" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-tht.form  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-anak" title="Penilaian Awal Medis Anak" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-anak.form-anak  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-kandungan" title="Penilaian Awal Medis Kandungan" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-kandungan.form-kandungan  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-dalam" title="Penilaian Awal Medis Penyakit Dalam" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-dalam.form-dalam  />
    </x-adminlte-modal>

    <x-adminlte-modal wire:ignore.self id="modal-awal-medis-psikiatri" title="Penilaian Awal Medis Psikiatri" size="xl" v-centered static-backdrop scrollable>
        <livewire:component.awal-psikiatri.form-psikiatri  />
    </x-adminlte-modal>
@stop

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
    
</script>
@endpush
