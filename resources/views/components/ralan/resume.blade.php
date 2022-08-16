<div>
    <x-adminlte-card title="Resume Medis" theme="info" icon="fas fa-lg fa-file-medical" collapsible maximizable>
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

        <div class="row justify-content-end">
            <x-adminlte-button id="resumeButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
        </div>
    </x-adminlte-card>
</div>

@push('js')
    <script>
        $('#resumeButton').click(function(){
            $.ajax({
                url: "{{ url('/ralan/simpan/copyresep/$encrypNoRawat') }}",
                type: 'POST',
                data: {
                    keluhanUtama: $('#keluhanUtama').val(),
                    prosedurUtama: $('#prosedurUtama').val(),
                    diagnosaUtama: $('#diagnosaUtama').val(),
                    terapi: $('#terapi').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response){
                    console.log(response);
                },
                error: function(response){
                    console.log(response);
                }
            });
        });
    </script>
@endpush