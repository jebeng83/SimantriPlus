<div>
    <x-adminlte-modal id="modalRiwayatPemeriksaanRalan" title="Riwayat Pemeriksaan" size="xl" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
        <x-adminlte-callout theme="info">
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            {{-- Minimal example / fill data using the component slot --}}
            <x-adminlte-datatable id="tableRiwayatPemeriksaan" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                @foreach($data as $row)
                    <tr>
                        @php
                            $i = 0;
                        @endphp
                        @foreach($row as $cell)
                        @php
                            $pemriksaanRalan = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat, $row->status_lanjut);
                            $diagnosa = App\Http\Controllers\Ralan\PemeriksaanRalanController::getDiagnosa($row->no_rawat);
                            
                        @endphp
                            @if ($i == 3)
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
                            @elseif ($i == 4)
                                <td>
                                    <ul">
                                        @foreach($diagnosa as $diagnosa)
                                            <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                                        @endforeach
                                    </ul>
                                </td>
                            @elseif ($i == 5)
                                @if($row->status_lanjut == 'Ralan')
                                    @php
                                        $pemeriksaanObstetri = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanObstetri($row->no_rawat);
                                    @endphp
                                    <td>
                                        @if(!empty($pemeriksaanObstetri))
                                            <ul>
                                                <li>Tinggi Fundus : {{$pemeriksaanObstetri->tinggi_uteri}}</li>
                                                <li>Janin : {{$pemeriksaanObstetri->janin}}</li>
                                                <li>Letak : {{$pemeriksaanObstetri->letak}}</li>
                                                <li>Bawah Panggul : {{$pemeriksaanObstetri->panggul}}</li>
                                                <li>Denyut Jantung : {{$pemeriksaanObstetri->denyut}}</li>
                                                <li>Kontarksi : {{$pemeriksaanObstetri->kontraksi}}</li>
                                                <li>Kualitas Menit : {{$pemeriksaanObstetri->kualitas_mnt}}</li>
                                                <li>Kualitas Detik : {{$pemeriksaanObstetri->kualitas_dtk}}</li>
                                                <li>Fluksus : {{$pemeriksaanObstetri->fluksus}}</li>
                                                <li>Fluor Albus : {{$pemeriksaanObstetri->albus}}</li>
                                                <li>Selaput Ketuban : {{$pemeriksaanObstetri->ketuban}}</li>
                                                <li>Vulva/Vagina : {{$pemeriksaanObstetri->vulva}}</li>
                                                <li>Portio Inspekulo  : {{$pemeriksaanObstetri->portio}}</li>
                                                <li>Dalam  : {{$pemeriksaanObstetri->dalam}}</li>
                                                <li>Tebal  : {{$pemeriksaanObstetri->tebal}}</li>
                                                <li>Arah  : {{$pemeriksaanObstetri->arah}}</li>
                                                <li>Pembukaan  : {{$pemeriksaanObstetri->pembukaan}}</li>
                                                <li>Penurunan  : {{$pemeriksaanObstetri->penurunan}}</li>
                                                <li>Denominator  : {{$pemeriksaanObstetri->denominator}}</li>
                                                <li>Feto-Pelvik  : {{$pemeriksaanObstetri->feto}}</li>
                                            </ul>
                                        @endif
                                    </td>
                                @else
                                    <td> - </td>
                                @endif

                            @else
                                <td>{!! $cell !!}</td>
                            @endif
                        @php
                            $i++;
                        @endphp
                        @endforeach    
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
</div>