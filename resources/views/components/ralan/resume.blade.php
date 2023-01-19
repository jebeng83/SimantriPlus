<div>
    <x-adminlte-card title="Resume Medis" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed" maximizable>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="keluhanUtama" label="Keluhan Utama" rows=4>
                    {{ $kel->keluhan ?? '' }}
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="jalan" label="Jalannya Penyakit Selama Perawatan" rows=4>
                </x-adminlte-textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="radiologi" label="Pemeriksaan Penunjang yang Positif" rows=4>
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" icon="fas fa-paperclip" data-toggle="modal" data-target="#modalRad" />
                    </x-slot>
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="lab" label="Hasil Laboratorium yang Positif" rows=4>
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" icon="fas fa-paperclip" data-toggle="modal" data-target="#modalLab" />
                    </x-slot>
                </x-adminlte-textarea>
            </div>
        </div>
        <div class="col-md-12">
            <x-adminlte-textarea name="terapi" label="Terapi" rows=4>
                {{ $getTerapi->nama_brng ?? '' }}
            </x-adminlte-textarea>
        </div>
        <div class="row">
            <x-adminlte-input name="diagnosaUtama" label="Diagnosa Utama" value="{{$diagnosa->diagnosa_utama ?? ''}}"  fgroup-class="col-md-6" />
            <x-adminlte-input name="prosedurUtama" label="Prosedur Utama" value="{{$prosedur->deskripsi_panjang ?? ''}}"  fgroup-class="col-md-6" />
        </div>

        <div class="row justify-content-end">
            <x-adminlte-button id="resumeButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
        </div>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="modalLab" title="Pemeriksaan Lab" size="lg" theme="primary"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <x-slot name="footerSlot">
        <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>

<x-adminlte-modal id="modalRad" title="Pemeriksaan Radiologi" size="lg" theme="primary"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    .container-
    <x-slot name="footerSlot">
        <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>


@push('js')
    <script 
        id="resume" 
        src="{{ asset('js/ralan/resume.js') }}" 
        data-encrypNoRawat="{{ $encrypNoRawat }}" 
        data-token="{{ csrf_token() }}">
    </script>
@endpush