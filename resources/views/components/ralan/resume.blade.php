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

        <div class="row justify-content-end">
            <x-adminlte-button id="resumeButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
        </div>
    </x-adminlte-card>
</div>

@push('js')
    <script>
        $('#resumeButton').click(function(){
            axios.post("{{ url('/ralan/simpan/copyresep/$encrypNoRawat') }}", {
                '_token': "{{ csrf_token() }}",
                'keluhan_utama': $('#keluhanUtama').val(),
                'terapi': $('#terapi').val(),
                'prosedur_utama': $('#prosedurUtama').val(),
                'diagnosa_utama': $('#diagnosaUtama').val(),
            })
            .then(function (response) {
                console.log(response);
                $('#resumeButton').html('<i class="fas fa-check"></i> Berhasil');
                $('#resumeButton').attr('disabled', true);
            })
            .catch(function (error) {
                console.log(error);
            });
        });
    </script>
@endpush