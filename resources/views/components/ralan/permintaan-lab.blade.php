<x-adminlte-card title="Permintaan Lab" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
    <form id="formPermintaanLab"></form>
    <div class="form-group row">
        <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="klinis" name="klinis" />
        </div>
    </div>
    <div class="form-group row">
        <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="info" name="info" />
        </div>
    </div>
    <div class="form-group row">
        <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
        <div class="col-sm-8">
          <select class="form-control jenis" id="jenis" name="jenis[]" multiple="multiple" ></select>
        </div>
    </div>
    <div class="d-flex flex-row-reverse">
        <x-adminlte-button id="simpanPermintaanLab" class="ml-1" theme="primary" type="submit" label="Simpan" />
    </div>
</x-adminlte-card>

@push('js')
<script>
    function getValue(name) {
            var data = [];
            var doc = document.getElementsByName(name);
            for (var i = 0; i < doc.length; i++) {
                    var a = doc[i].value;
                    data.push(a);
                }

            return data;
        }

    function formatData (data) {
            var $data = $(
                '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
            );
            return $data;
    };

    $('.jenis').select2({
        placeholder: 'Pilih Jenis',
        ajax: {
            url: '/api/jns_perawatan_lab',
            dataType: 'json',
            delay: 250,
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            cache: true
            },
            templateResult: formatData,
            minimumInputLength: 3
    });

    $('#simpanPermintaanLab').click(function(){
        $.ajax({
            url: '/api/ralan/simpan/permintaanlab/'+"{{$encrypNoRawat}}",
            type: 'POST',
            data: {
                klinis: $('#klinis').val(),
                info: $('#info').val(),
                jns_pemeriksaan: getValue('jenis[]'),
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
                        text: "Data gagal disimpan",
                        icon: "error",
                        button: "OK",
                    });
                }
            },
            error: function(response){
                Swal.fire({
                    title: "Gagal",
                    text: "Data gagal disimpan",
                    icon: "error",
                    button: "OK",
                });
            }
        });
    });
</script>
@endpush