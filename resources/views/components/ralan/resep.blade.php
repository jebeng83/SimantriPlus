<div>
    <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-pills" collapsible="collapsed" maximizable>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="resep-tab" data-toggle="tab" data-target="#resep" type="button" role="tab" aria-controls="resep" aria-selected="true">Resep</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="copyresep-tab" data-toggle="tab" data-target="#copyresep" type="button" role="tab" aria-controls="copyresep" aria-selected="false">Resep Racikan</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="resep" role="tabpanel" aria-labelledby="resep-tab">
                <x-adminlte-callout theme="info" title="Input Resep">
                    <form  method="post" id="resepForm" action="{{url('/ralan/simpan/resep/'.$encryptNoRawat)}}">
                        @csrf
                        <div class="containerResep">
                            <div class="row">
                                <x-adminlte-select2 id="obat" label="Nama Obat" class="obat" name="obat[]" fgroup-class="col-md-5"  data-placeholder="Pilih Obat" />
                                <x-adminlte-input id="jumlah" label="Jumlah" name="jumlah[]" fgroup-class="col-md-2" placeholder="Jumlah"/>
                                <x-adminlte-input id="aturan" label="Aturan Pakai" name="aturan[]" fgroup-class="col-md-5" placeholder="Aturan Pakai"/>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <x-adminlte-select-bs id="iter" name="iter" fgroup-class="col-md-4 my-auto" data-placeholder="Pilih Iter">
                                <option value="-">Pilih jumlah iter</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </x-adminlte-select>
                            <x-adminlte-button id="addFormResep" class="md:col-md-1 sm:col-sm-6 add-form-resep" theme="success" label="+" />
                            <x-adminlte-button id="resepButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
                        </div>
                    </form>
                </x-adminlte-callout>

                @if(count($resep) > 0)
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

            </div>
            <div class="tab-pane fade" id="copyresep" role="tabpanel" aria-labelledby="copyresep-tab">
                <x-adminlte-callout theme="info" title="Input Resep Racikan">
                    <form  method="post" id="copyresepForm" action="{{url('/ralan/simpan/copyresep/'.$encryptNoRawat)}}">
                        @csrf
                        <div class="containerCopyResep">
                            <div class="row">
                                <x-adminlte-input id="obat_racikan" label="Nama Racikan" name="nama_racikan" fgroup-class="col-md-12" />
                                <x-adminlte-select-bs id="metode_racikan" name="metode_racikan" label="Metode Racikan" fgroup-class="col-md-6" data-live-search data-live-search-placeholder="Cari..." data-show-tick>
                                    @foreach($dataMetodeRacik as $metode)
                                        <option value="{{$metode->kd_racik}}">{{$metode->nm_racik}}</option>
                                    @endforeach
                                </x-adminlte-select-bs>
                                <x-adminlte-input label="Jumlah" id="jumlah_racikan" value="10" name="jumlah_racikan" fgroup-class="col-md-6" />
                                <x-adminlte-input label="Aturan Pakai" id="aturan_racikan" name="aturan_racikan" fgroup-class="col-md-6" />
                                <x-adminlte-input label="Keterangan" id="keterangan_racikan" name="keterangan_racikan" fgroup-class="col-md-6" />
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <x-adminlte-button id="resepRacikanButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary" type="submit" label="Simpan" />
                        </div>
                    </form>
                </x-adminlte-callout>

                @if(count($resepRacikan) > 0)
                <x-adminlte-callout theme="info">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No Resep</th>
                                <th>Nama Racikan</th>
                                <th>Metode Racikan</th>
                                <th>Jumlah</th>
                                <th>Aturan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resepRacikan as $r)
                                <tr>
                                    <td>{{$r->no_resep}}</td>
                                    <td>{{$r->no_racik}}. {{$r->nama_racik}}</td>
                                    <td>{{$r->nm_racik}}</td>
                                    <td>{{$r->jml_dr}}</td>
                                    <td>{{$r->aturan_pakai}}</td>
                                    <td>{{$r->keterangan}}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick='hapusRacikan("{{$r->no_resep}}", "{{$r->no_racik}}", event)'>Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>              
                </x-adminlte-callout>
                @endif

                {{-- <x-adminlte-callout theme="info" title="Riwayat Resep Racikan">
                </x-adminlte-callout> --}}
            </div>
        </div>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="modalCopyResep" title="Copy Resep" size="lg" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <div class="table-responsive">
        <table class="table table-copy-resep">
            <thead class="thead-dark">
              <tr>
                <th scope="col">Jumlah</th>
                <th scope="col">Nama Obat</th>
                <th scope="col">Aturan Pakai</th>
              </tr>
            </thead>
            <tbody class="tbBodyCopy">
            </tbody>
        </table>
    </div>
    <x-slot name="footerSlot">
        <x-adminlte-button class="mr-2" id="simpanCopyResep"  theme="primary" label="Simpan" data-dismiss="modal"/>
        <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>


@push('js')
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
            html += '<div class="row">';
            html += '   <div class="col-md-5">';
            html += '       <div class="form-group">';
            html += '            <label class="visible-sm">Nama Obat</label>';
            html += '            <select name="obat[]" class="form-control obat-'+x+'" id="obat'+x+'" data-placeholder="Pilih Obat">';
            html += '            </select>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="col-md-2">';
            html += '        <div class="form-group">';
            html += '            <label class="visible-sm">Jumlah</label>';
            html += '            <input type="text" name="jumlah[]" class="form-control" id="jumlah'+x+'" placeholder="Jumlah"/>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="col-md-4">';
            html += '        <div class="form-group">';
            html += '            <label class="visible-sm">Aturan Pakai</label>';
            html += '            <input name="aturan[]" id="aturan'+x+'" class="form-control" placeholder="Aturan Pakai">';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="col-md-1 my-auto">';
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
            var trHTML = '';
            $(".table-copy-resep").find("tr.body").remove();
            $.ajax({
                url:'/ralan/copy/'+no_resep,
                type:'GET',
                dataType:'json',
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
                success: function(data){
                    // console.log(data);
                    Swal.close();
                    $.each(data, function (i, item) {
                        trHTML += '<tr class="body"><td><input type="text" name="jml_copyresep[]" multiple="multiple" value="' + item.jml + '" size="5"></td>'
                                + '<td><input type="hidden" name="kode_brng_copyresep[]" multiple="multiple" value="' + item.kode_brng +'" > ' + item.nama_brng + '</td>'
                                + '<td><input type="text" name="aturan_copyresep[]" multiple="multiple" value="' + item.aturan_pakai + '"></td></tr>';
                    });
                    $('.tbBodyCopy').append(trHTML);
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
                        success: function(data) {
                            console.log(data);
                            data.status == 'success' ? Swal.fire(
                                'Terhapus!',
                                data.pesan,
                                'success'
                            ).then((result) => {
                                if (result.value) {
                                    window.location.reload();
                                }
                            }) : Swal.fire(
                                'Gagal!',
                                data.pesan,
                                'error'
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

        function hapusRacikan($noResep, $noRacik, e) {
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
                        url: '/ralan/racikan/'+$noResep+'/'+$noRacik,
                        type: 'DELETE',
                        dataType: 'json',
                        data:{_token: _token}, 
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
                        success: function(data) {
                            console.log(data);
                            data.status == 'success' ? Swal.fire(
                                'Terhapus!',
                                data.pesan,
                                'success'
                            ).then((result) => {
                                if (result.value) {
                                    window.location.reload();
                                }
                            }) : Swal.fire(
                                'Gagal!',
                                data.pesan,
                                'error'
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

        $("#simpanCopyResep").click(function(e) {
            e.preventDefault();
            let _token   = $('meta[name="csrf-token"]').attr('content');
            let obat = getValue('kode_brng_copyresep[]');
            let jumlah = getValue('jml_copyresep[]');
            let aturan = getValue('aturan_copyresep[]');
            var data = {
                obat:obat,
                jumlah:jumlah,
                aturan_pakai:aturan,
                _token:_token,
            };
            // console.log(data);
            $.ajax({
                type: 'POST',
                url: '/ralan/simpan/copyresep/'+"{{$encryptNoRawat}}",
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    $('#modalCopyResep').modal('hide')
                    Swal.fire({
                    title: 'Loading',
                    imageUrl: '{{asset("img/loading.gif")}}',
                    showConfirmButton: false,
                    })
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

        $("#resepButton").click(function(e) {
            e.preventDefault();
            let _token   = $('meta[name="csrf-token"]').attr('content');
            let obat = getValue('obat[]');
            let jumlah = getValue('jumlah[]');
            let aturan = getValue('aturan[]');
            let iter = $('#iter').val();
            var form = $("#resepForm");
            var data = {
                obat:obat,
                jumlah:jumlah,
                aturan_pakai:aturan,
                iter:iter,
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

        $("#resepRacikanButton").click(function(e){
            e.preventDefault();
            let _token   = $('meta[name="csrf-token"]').attr('content');
            let obat = $('#obat_racikan').val();
            let metode = $('#metode_racikan').val();
            let jumlah = $('#jumlah_racikan').val();
            let aturan = $('#aturan_racikan').val();
            let keterangan = $('#keterangan_racikan').val();
            $.ajax({
                type: 'POST',
                url: '/ralan/simpan/racikan/'+"{{$encryptNoRawat}}",
                data: {
                    nama_racikan:obat,
                    metode_racikan:metode,
                    jumlah_racikan:jumlah,
                    aturan_racikan:aturan,
                    keterangan_racikan:keterangan,
                    _token:_token,
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#modalRacikan').modal('hide')
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
                    var errors = $.parseJSON(response.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: errors.message ?? 'Terjadi kesalahan',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    })
                }
            });
        });

    </script>
    
@endpush