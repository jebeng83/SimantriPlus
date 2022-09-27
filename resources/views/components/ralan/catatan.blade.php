<div>
    <x-adminlte-card title="Catatan Pasien" theme="info" icon="fas fa-lg fa-notes-medical" collapsible="collapsed" maximizable>
        <x-adminlte-textarea name="catatan" label="Catatan" fgroup-class="col-md-12" rows="4">
            {{$data->catatan ?? ''}}
        </x-adminlte-textarea>
        <div class="d-flex flex-row-reverse">
            <x-adminlte-button class="btn-flat" type="submit" id="catatanButton" label="Simpan" theme="primary" icon="fas fa-lg fa-save" />
        </div>
    </x-adminlte-card>
</div>

@push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $("#catatanButton").click(function(event){
        event.preventDefault();
        let catatan = $("textarea[name=catatan]").val();
        let _token   = $('meta[name="csrf-token"]').attr('content');
        // alert("{{request()->get('no_rawat')}}");
        $.ajax({
          url: "/ralan/catatan/submit",
          type:"POST",
          data:{
            no_rawat:"{{$encryptNoRawat}}",
            catatan:catatan,
            _token: _token
          },
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
          success:function(response){
            console.log(response);
            // var res = $.parseJSON(response);
            Swal.fire({
                text: response.message,
                icon: 'success',
                confirmButtonText: 'Tutup'
            }).then((result) => {
                if (result.value) {
                    window.location.reload();
                }
            });
          },
          error: function(error) {
           console.log(error);
           var errors = $.parseJSON(error.responseText);
            Swal.fire({
                title: 'Error!',
                text: errors.message,
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
          }
         });
    });
    </script>
@endpush