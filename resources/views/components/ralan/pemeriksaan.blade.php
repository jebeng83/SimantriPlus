<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
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
            <x-adminlte-input name="imun" label="Imun Ke" value="{{$pemeriksaan->imun_ke ?? ''}}" fgroup-class="col-md-3" />
            <x-adminlte-input name="gcs" label="GCS (E, V, M)" value="{{$pemeriksaan->gcs ?? ''}}" fgroup-class="col-md-3" />
            <x-adminlte-input name="rtl" label="Tindak Lanjut" value="{{$pemeriksaan->rtl ?? ''}}" fgroup-class="col-md-3" />
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button class="d-flex ml-auto" id="pemeriksaanButton" theme="primary" label="Simpan"
                icon="fas fa-sign-in"/>
        </x-slot>
        </form>
    </x-adminlte-card>
</div>