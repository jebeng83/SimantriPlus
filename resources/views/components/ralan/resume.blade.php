<div>
    <x-adminlte-card title="Resume Medis" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed" maximizable>
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-textarea name="keluhanUtama" label="Keluhan Utama" rows=5>
                    {{ $kel->keluhan ?? '' }}
                </x-adminlte-textarea>
            </div>
            <div class="col-md-6">
                <x-adminlte-textarea name="terapi" label="Terapi" rows=5>
                    {{ $getTerapi->nama_brng ?? '' }}
                </x-adminlte-textarea>
            </div>
        </div>
        <div class="row">
            <x-adminlte-input name="prosedurUtama" label="Prosedur Utama" value="{{$prosedur->deskripsi_panjang ?? ''}}"  fgroup-class="col-md-6" />
            <x-adminlte-input name="diagnosaUtama" label="Diagnosa Utama" value="{{$diagnosa->diagnosa_utama ?? ''}}"  fgroup-class="col-md-6" />
        </div>

        <div class="row justify-content-end">
            <x-adminlte-button id="resumeButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
        </div>
    </x-adminlte-card>
</div>

@push('js')
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        $('#resumeButton').click(function(){
            $.ajax({
                url: '/ralan/simpan/resumemedis/'+"{{$encrypNoRawat}}",
                type: 'POST',
                data: {
                    keluhanUtama: $('#keluhanUtama').val(),
                    prosedurUtama: $('#prosedurUtama').val(),
                    diagnosaUtama: $('#diagnosaUtama').val(),
                    terapi: $('#terapi').val(),
                    _token: '{{ csrf_token() }}'
                },
                format: 'json',
                beforeSend:function() {
                Swal.fire({
                    title: 'Loading....',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        }
                    });
                },
                success: function(response){
                    // console.log(response);
                    if(response.status == 'sukses'){
                        Swal.fire({
                            title: "Sukses",
                            text: "Data berhasil disimpan",
                            icon: "success",
                            button: "OK",
                        });
                    }else{
                        Swal.fire({
                            title: "Gagal",
                            text: response.pesan ?? "Data gagal disimpan",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function(response){
                    // console.log(response);
                    Swal.fire({
                        title: "Error",
                        text: response.pesan ?? "Terjadi kesalahan",
                        icon: "error",
                        button: "OK",
                    });
                }
            });
        });
    </script>
@endpush