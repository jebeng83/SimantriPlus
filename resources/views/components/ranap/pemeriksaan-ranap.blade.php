<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <x-adminlte-callout theme="info" title="Input Pemeriksaan" >
            <form id="pemeriksaanForm">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-textarea name="keluhan" label="Keluhan">
                            {{$pemeriksaan->keluhan ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-textarea name="pemeriksaan" label="Pemeriksaan">
                            {{$pemeriksaan->pemeriksaan ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-textarea name="penilaian" label="Penilaian">
                            {{$pemeriksaan->penilaian ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                </div>
                <div class="row">
                    <x-adminlte-input name="suhu" label="Suhu Badan (C)" value="{{$pemeriksaan->suhu_tubuh ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="berat" label="Berat (Kg)" value="{{$pemeriksaan->berat ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="tinggi" label="Tinggi Badan (Cm)" value="{{$pemeriksaan->tinggi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="tensi" label="Tensi" value="{{$pemeriksaan->tensi ?? ''}}" fgroup-class="col-md-3" />
                </div>
                <div class="row">
                    <x-adminlte-input name="nadi" label="Nadi (per Menit)" value="{{$pemeriksaan->nadi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="respirasi" label="Respirasi (per Menit)" value="{{$pemeriksaan->respirasi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="instruksi" label="Instruksi" value="{{$pemeriksaan->instruksi ?? ''}}" fgroup-class="col-md-3" />
                    <div class="col-md-3">
                        <x-adminlte-select-bs name="kesadaran" label="Kesadaran">
                            @if(!empty($pemeriksaan->kesadaran))
                                <option @php if($pemeriksaan->kesadaran == 'Compos Mentis') echo 'selected'; @endphp >Compos Mentis</option>
                                <option @php if($pemeriksaan->kesadaran == 'Somnolence') echo 'selected'; @endphp >Somnolence</option>
                                <option @php if($pemeriksaan->kesadaran == 'Sopor') echo 'selected'; @endphp >Sopor</option>
                                <option @php if($pemeriksaan->kesadaran == 'Coma') echo 'selected'; @endphp >Coma</option>
                            @else
                                <option>Compos Mentis</option>
                                <option>Somnolence</option>
                                <option>Sopor</option>
                                <option>Coma</option>
                            @endif
                        </x-adminlte-select-bs>
                    </div>                    
                </div>
                <div class="row">
                    <x-adminlte-input name="alergi" label="Alergi" value="{{$pemeriksaan->alergi ?? ''}}" fgroup-class="col-md-3" />
                    {{-- <x-adminlte-input name="imun" label="Imun Ke" value="{{$pemeriksaan->imun_ke ?? ''}}" fgroup-class="col-md-3" /> --}}
                    <div class="col-md-3">
                        <x-adminlte-select-bs name="imun" label="Imun Ke">
                            <option value="-">-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                        </x-adminlte-select-bs>
                    </div>
                    <x-adminlte-input name="gcs" label="GCS (E, V, M)" value="{{$pemeriksaan->gcs ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="rtl" label="Tindak Lanjut" value="{{$pemeriksaan->rtl ?? ''}}" fgroup-class="col-md-3" />
                </div>
                <x-adminlte-button class="d-flex ml-auto" id="pemeriksaanButton" theme="primary" label="Simpan" icon="fas fa-sign-in"/>
                </form>
        </x-adminlte-callout>
        <x-adminlte-callout theme="info" title="Riwayat" >
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatPemeriksaanRanap" :heads="$heads" head-theme="dark" :config="$config" striped hoverable bordered compressed>
                @foreach($riwayat as $row)
                    <tr>
                        <td>{{ $row->tgl_perawatan }}</td>
                        <td>{{ $row->jam_rawat }}</td>
                        <td>{{ $row->keluhan }}</td>
                        <td>{{ $row->pemeriksaan }}</td>
                        <td>{{ $row->penilaian }}</td>
                        <td>{{ $row->suhu_tubuh }}</td>
                        <td>{{ $row->tensi }}</td>
                        <td>{{ $row->nadi }}</td>
                        <td>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </x-adminlte-card>
</div>