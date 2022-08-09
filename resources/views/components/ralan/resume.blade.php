<div>
    <x-adminlte-card title="Resume Medis" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="keluhanUtama" label="Keluhan Utama" rows=10>
                    {{ $kel->keluhan ?? '' }}
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="terapi" label="Terapi" rows=10>
                    {{ $getTerapi->nama_brng ?? '' }}
                </x-adminlte-textarea>
            </div>
        </div>
        <div class="row">
            <x-adminlte-input name="prosedurUtama" label="Prosedur Utama"  fgroup-class="col-md-6" />
            <x-adminlte-input name="diagnosaUtama" label="Diagnosa Utama"  fgroup-class="col-md-6" />
        </div>
    </x-adminlte-card>
</div>