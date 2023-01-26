<div>
    <x-adminlte-card title="Resume Medis" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed" maximizable>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="keluhanUtama" id="keluhanUtama" label="Keluhan Utama" rows=4>
                    {{ $kel->keluhan ?? '' }}
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" id="kelButton" icon="fas fa-paperclip" />
                    </x-slot>
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="jalan" label="Jalannya Penyakit Selama Perawatan" rows=4>
                    {{ $diagnosa->jalannya_penyakit ?? '' }}
                </x-adminlte-textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="radiologi" id="rad-form" label="Pemeriksaan Penunjang yang Positif" rows=4>
                    {{ $diagnosa->pemeriksaan_penunjang ?? '' }}
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" id="radButton" icon="fas fa-paperclip" />
                    </x-slot>
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="lab" id="lab-form" label="Hasil Laboratorium yang Positif" rows=4>
                    {{ $diagnosa->hasil_laborat ?? '' }}
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" icon="fas fa-paperclip" id="labButton"/>
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
            <x-adminlte-input name="diagnosaUtama" label="Diagnosa Utama" value="{{$diagnosa->diagnosa_utama ?? ''}}"  fgroup-class="col-md-4" />
            <x-adminlte-input name="prosedurUtama" label="Prosedur Utama" value="{{$prosedur->deskripsi_panjang ?? ''}}"  fgroup-class="col-md-4" />
            <div class="col-md-4">
                <x-adminlte-select-bs name="kondisiPasien" id="kondisiPasien" label="Kondisi Pasien Pulang">
                    <option value="Hidup" selected>Hidup</option>
                    <option value="Meninggal">Meninggal</option>
                </x-adminlte-select-bs>                      
            </div>
        </div>

        <div class="row justify-content-end">
            <x-adminlte-button id="resumeSubmitButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" label="Simpan" />
        </div>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="modalLab" title="Pemeriksaan Lab" size="lg" theme="primary"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <div class="container-lab"></div>
    <x-slot name="footerSlot">
        <x-adminlte-button theme="primary" label="OK" id="oklab"/>
    </x-slot>
</x-adminlte-modal>

<x-adminlte-modal id="modalRad" title="Pemeriksaan Radiologi" size="lg" theme="primary"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <div class="container-radiologi"></div>
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