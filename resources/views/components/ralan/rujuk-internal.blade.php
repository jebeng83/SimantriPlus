<div>
    <x-adminlte-card title="Rujuk Internal" theme="info" icon="fas fa-lg fa-wheelchair" collapsible="collapsed" >
        <div class="mb-3 row">
            <label for="poli" class="col-sm-2 col-form-label">Poli Tujuan</label>
            <div class="col-sm-10">
                <select name="poli" class="form-control poli" id="poli" data-placeholder="Pilih Poli">
                </select>
            </div>
        </div>
        <div id="dokter-container" class="mb-3 row">
        </div>
        <div class="mb-3 row">
            <label for="catatan" class="col-sm-2 col-form-label">Catatan Konsul</label>
            <div class="col-sm-10">
                <textarea name="catatan" class="form-control catatan" id="catatan" placeholder="Tulis Catatan Konsul" rows="4"></textarea>
            </div>
        </div>
        <x-adminlte-button class="d-flex ml-auto" id="rujukButton" theme="primary" label="Simpan" onclick="postRujukan()" icon="fas fa-sign-in"/>
        @foreach($data as $data)
            <x-adminlte-card title="Konsul / Rujukan Internal" theme="dark" theme-mode="outline" class="mt-4">
                <table class="table table-bordered table-striped mb-4">
                    <tr>
                        <th>Poli Tujuan</th>
                        <th>{{$data->nm_poli}}</th>
                    </tr>
                    <tr>
                        <th>Dokter Tujuan</th>
                        <th>{{$data->nm_dokter}}</th>
                    </tr>
                    <tr>
                        <th>Konsul</th>
                        <th>{{$data->konsul}}</th>
                    </tr>
                    <tr>
                        <th>Pemeriksaan</th>
                        <th>{{$data->pemeriksaan}}</th>
                    </tr>
                    <tr>
                        <th>Diagnosa</th>
                        <th>{{$data->diagnosa}}</th>
                    </tr>
                    <tr>
                        <th>Saran</th>
                        <th>{{$data->saran}}</th>
                    </tr>
                </table>
                <x-adminlte-button class="d-flex ml-auto" id="rujukButtonHapus" theme="danger" label="Hapus" onclick="deleteRujukan()" />
            </x-adminlte-card>
        @endforeach
    </x-adminlte-card>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $('.poli').select2({
                placeholder: 'Pilih Poli Tujuan',
                ajax: {
                    url: '/ralan/poli',
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

            function formatData (data) {
                    var $data = $(
                        '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
                    );
                    return $data;
            };
        });

        $('.poli').on('select2:select', function (e) {
            var data = e.params.data.id;
            $.ajax({
                url: '/ralan/dokter/'+data,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    var options = '';
                    response.forEach(function(item) {
                        options += '<option value="'+item.kd_dokter+'">'+item.nm_dokter+'</option>';
                    });
                    var input = '' + 
                            '<label for="dokter" class="col-sm-2 col-form-label">Dokter Tujuan</label>' + 
                            '<div class="col-sm-10">' + 
                            '    <select name="dokter" class="form-control dokter w-100" id="dokter" data-placeholder="Pilih Dokter">' + options + 
                            '    </select>' + 
                            '</div>' + 
                            '';
                    $('#dokter-container').html(input);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        function postRujukan() {
            var poli = $('.poli').val();
            var dokter = $('.dokter').val();
            var catatan = $('.catatan').val();
            var _token   = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "/ralan/rujuk-internal/submit",
                type:"POST",
                data:{
                    no_rawat:"{{$encryptNoRawat}}",
                    kd_poli:poli,
                    kd_dokter:dokter,
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
        }

        function deleteRujukan() {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.value) {
                    var _token   = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: "/ralan/rujuk-internal/delete/{{$encryptNoRawat}}",
                        type:"DELETE",
                        data:{
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
                }
            });
        }
    </script>
@endpush