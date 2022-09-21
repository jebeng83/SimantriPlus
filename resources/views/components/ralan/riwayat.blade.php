<div>
    <x-adminlte-modal id="modalRiwayatPemeriksaanRalan" title="Riwayat Pemeriksaan" size="xl" theme="info"
    icon="fas fa-book-medical" v-centered static-backdrop scrollable>
        <x-adminlte-callout theme="info">
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatPemeriksaan" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                @foreach($data as $row)
                    @php
                        $pemriksaanRalan = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat, $row->status_lanjut);
                        $diagnosa = App\Http\Controllers\Ralan\PemeriksaanRalanController::getDiagnosa($row->no_rawat);
                    @endphp
                    <tr>
                        <td>{{ $row->no_rawat }}</td>
                        <td>{{ $row->nm_dokter}}</td>
                        <td>
                            <ul>
                                <li>{{ $pemriksaanRalan->keluhan ?? '-' }}</li>
                                <li>{{$pemriksaanRalan->pemeriksaan ?? '-'}}</li>
                                @if(!empty($pemriksaanRalan->tinggi))
                                    <li>Tinggi  :  {{$pemriksaanRalan->tinggi}}</li>
                                @endif
                                @if(!empty($pemriksaanRalan->berat))
                                    <li>Berat  :  {{$pemriksaanRalan->berat}}</li>
                                @endif
                                @if(!empty($pemriksaanRalan->tensi))
                                    <li>Tensi  :  {{$pemriksaanRalan->tensi}}</li>
                                @endif
                                @if(!empty($pemriksaanRalan->nadi))
                                    <li>Nadi  :  {{$pemriksaanRalan->nadi}}</li>
                                @endif
                                @if(!empty($pemriksaanRalan->suhu))
                                    <li>Suhu  :  {{$pemriksaanRalan->suhu}}</li>
                                @endif
                                @if(!empty($pemriksaanRalan->respirasi))
                                    <li>RR  :  {{$pemriksaanRalan->respirasi}}</li>
                                @endif
                                <li>Alergi  :  {{$pemriksaanRalan->alergi ?? '-'}}</li>
                                @if(!empty($pemriksaanRalan->rtl))
                                    <li>Tindak Lanjut  :  {{$pemriksaanRalan->rtl}}</li>
                                @endif
                            </ul>
                        </td>
                        <td>
                            <ul">
                                @foreach($diagnosa as $diagnosa)
                                    <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
                    
            </x-adminlte-datatable>
        </x-adminlte-callout>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
</div>