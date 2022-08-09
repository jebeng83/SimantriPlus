<div>
    <x-adminlte-card title="Resep" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <x-slot name="toolsSlot">
            <select class="custom-select w-auto form-control-border bg-light">
                <option>{{$no_rawat}}</option>
                <option>Resep Racikan</option>
            </select>
        </x-slot>
        <x-adminlte-callout theme="info" title="Riwayat Peresepan">
            <form  method="post" id="resepForm" action="{{route('ralan.simpan.resep')}}">
                @csrf
                <div class="containerResep">
                    <div class="row">
                        <x-adminlte-select2 id="obat" label="Nama Obat" class="obat" name="obat[]" fgroup-class="col-md-5" data-placeholder="Pilih Obat" />
                        <x-adminlte-input id="jumlah" label="Jumlah" name="jumlah[]" fgroup-class="col-md-2" placeholder="Jumlah"/>
                        <x-adminlte-input id="aturan" label="Aturan Pakai" name="aturan[]" fgroup-class="col-md-5" placeholder="Aturan Pakai"/>
                    </div>
                </div>
                <div class="row justify-content-end">
                    <x-adminlte-button id="addFormResep" class="md:col-md-1 sm:col-sm-6 add-form-resep" theme="success" label="+" />
                    <x-adminlte-button id="resepButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
                </div>
            </form>
        </x-adminlte-callout>
        @if(!isset($resep))
        <x-adminlte-callout theme="info">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Tanggal / Jam</th>
                        <th>Jumlah</th>
                        <th>Aturan Pakai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resep as $r)
                        <tr>
                            <td>{{$r->nama_brng}}</td>
                            <td>{{$r->tgl_peresepan}} {{$r->jam_peresepan}}</td>
                            <td>{{$r->jml}}</td>
                            <td>{{$r->aturan_pakai}}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick='hapusObat("{{$r->no_resep}}", "{{$r->kode_brng}}", event)'>Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>              
        </x-adminlte-callout>
        @endif
        <x-adminlte-callout theme="info" title="Riwayat Peresepan">
            @php
                $config["responsive"] = true;
                $config['order'] = [[1, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatResep" :heads="$heads" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                {{-- <x-slot name="bodySlot"> --}}
                    @foreach($riwayatPeresepan as $r)
                        <tr>
                            <td class="align-middle text-center">{{$r->no_resep}}</td>
                            <td class="align-middle text-center">{{$r->tgl_peresepan}}</td>
                            <td>
                                <ul>
                                @foreach($getResepObat($r->no_resep) as $ro)
                                    <li>{{$ro->nama_brng}} - {{$ro->jml}} - [{{$ro->aturan_pakai}}]</li>
                                @endforeach
                                </ul>
                            </td>
                            <td class="align-middle text-center"><x-adminlte-button label="Copy Resep" onclick='getCopyResep({{$r->no_resep}}, event)' class="mx-auto" theme="primary" icon="fas fa-note"/></td>
                        </tr>
                    @endforeach
                {{-- </x-slot> --}}
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="modalCopyResep" title="Copy Resep" size="lg" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <div style="height:800px;">Read the account policies...</div>
    <x-slot name="footerSlot">
        <x-adminlte-button class="mr-auto" theme="success" label="Accept"/>
        <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>


@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var wrapper = $(".containerResep");
    var add_button = $("#addFormResep");
    var x = 0;
    function formatData (data) {
                var $data = $(
                    '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
                );
                return $data;
        };
    $(add_button).click(function(e) {
        e.preventDefault();
        var html = '';
        html += '<div class="row clearfix">';
        html += '   <div class="col-md-5">';
        html += '       <div class="form-group">';
        // html += '            <label class="visible-sm">Nama Obat</label>';
        html += '            <select name="obat[]" class="form-control obat-'+x+'" id="obat'+x+'" data-placeholder="Pilih Obat">';
        html += '            </select>';
        html += '        </div>';
        html += '    </div>';
        html += '    <div class="col-md-2">';
        html += '        <div class="form-group">';
        // html += '            <label class="visible-sm">Jumlah</label>';
        html += '            <input type="text" name="jumlah[]" class="form-control" id="jumlah'+x+'" placeholder="Jumlah"/>';
        html += '        </div>';
        html += '    </div>';
        html += '    <div class="col-md-4">';
        html += '        <div class="form-group">';
        // html += '            <label class="visible-sm">Aturan Pakai</label>';
        html += '                  <input name="aturan[]" id="aturan'+x+'" class="form-control" placeholder="Aturan Pakai">';
        html += '        </div>';
        html += '    </div>';
        html += '    <div class="col-md-1">';
        html += '      <button class="btn btn-danger delete" value="row_resep'+x+'">-</button>';
        html += '    </div>';
        html += '</div>';
        $(wrapper).append(html.trim()); 
        $('#'+'obat'+x, wrapper).select2({
            placeholder: 'Pilih obat',
            ajax: {
                url: '/ralan/obat',
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
        x++;
    });

    $(wrapper).on("click", ".delete", function(e) {
        e.preventDefault();
        $(this).closest('.row').remove();
    })

    $(document).ready(function() {
        $('.obat').select2({
            placeholder: 'Pilih obat',
            ajax: {
                url: '/ralan/obat',
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
    </script>
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

    function getCopyResep(no_resep, e) {
        e.preventDefault();
        let _token   = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url:'ralan/copy/'+no_resep,
            type:'GET',
            dataType:'json',
            success:function(data){
                console.log(data);
                $('#modalCopyResep').modal('show');
            }
        });
    }

    function hapusObat($noResep, $kdObat, e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Obat?',
            text: "Yakin ingin menghapus obat ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.value) {
                let _token   = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/ralan/obat/'+$noResep+'/'+$kdObat,
                    type: 'DELETE',
                    dataType: 'json',
                    data:{_token: _token}, 
                    success: function(data) {
                        console.log(data);
                        Swal.fire(
                            data.status == 'success' ? 'Terhapus!' : 'Gagal!',
                            data.pesan,
                            data.status == 'success' ? 'success' : 'error'
                        ).then((result) => {
                            if (result.value) {
                                window.location.reload();
                            }
                        })
                    },
                    error: function(data) {
                        console.log(data);
                        Swal.fire(
                            'Gagal!',
                            data.pesan ?? 'Obat gagal dihapus.',
                            'error'
                        )
                    }
                })
            }
        })
    }

    $("#resepButton").click(function(e) {
        e.preventDefault();
        let _token   = $('meta[name="csrf-token"]').attr('content');
        let obat = getValue('obat[]');
        let jumlah = getValue('jumlah[]');
        let aturan = getValue('aturan[]');
        var noRawat = "{{request()->get('no_rawat')}}";
        var form = $("#resepForm");
        var data = {
            no_rawat:noRawat,
            obat:obat,
            jumlah:jumlah,
            aturan_pakai:aturan,
            _token:_token,
        };
        var url = form.attr('action');
        var method = form.attr('method');
        // console.log(data);
        $.ajax({
            type: method,
            url: url,
            data: data,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                title: 'Loading',
                imageUrl: '{{asset("img/loading.gif")}}',
                showConfirmButton: false,
                })
            },
            success: function (response) {
                console.log(response);
                if(response.status == 'sukses'){
                    Swal.fire({
                    title: 'Sukses',
                    text: 'Data berhasil disimpan',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.value) {
                            window.location.reload();
                        }
                    })
                }
                else{
                    Swal.fire({
                    title: 'Gagal',
                    text: response.pesan,
                    icon: 'error',
                    confirmButtonText: 'Ok'
                    })
                }
            },
            error: function (response) {
                console.log(response);
                Swal.fire({
                    title: 'Error',
                    text: response.pesan ?? 'Terjadi kesalahan',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                })
            }
        });
    });
    </script>
@endsection