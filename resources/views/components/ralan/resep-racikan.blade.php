<div>
    <x-adminlte-callout theme="info" title="Input Resep Racikan">
        <form method="post" id="copyresepForm" action="{{url('/ralan/simpan/copyresep/'.$encryptNoRawat)}}">
            @csrf
            <div class="containerCopyResep">
                <div class="row">
                    <x-adminlte-input id="obat_racikan" label="Nama Racikan" name="nama_racikan"
                        fgroup-class="col-md-12" />
                    <x-adminlte-select-bs id="metode_racikan" name="metode_racikan" label="Metode Racikan"
                        fgroup-class="col-md-6" data-live-search data-live-search-placeholder="Cari..." data-show-tick>
                        @foreach($dataMetodeRacik as $metode)
                        <option value="{{$metode->kd_racik}}">{{$metode->nm_racik}}</option>
                        @endforeach
                    </x-adminlte-select-bs>
                    <x-adminlte-input label="Jumlah" id="jumlah_racikan" value="10" name="jumlah_racikan"
                        fgroup-class="col-md-6" />
                    <x-adminlte-input label="Aturan Pakai" id="aturan_racikan" name="aturan_racikan"
                        fgroup-class="col-md-6" />
                    <x-adminlte-input label="Keterangan" id="keterangan_racikan" name="keterangan_racikan"
                        fgroup-class="col-md-6" />
                </div>
            </div>
            <div class="containerRacikan">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="d-block">Obat</label>
                            <select name="obatRacikan[]" class="form-control obat-racikan w-100" id="obatRacikan"
                                data-placeholder="Pilih Obat">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input id="stok" class="form-control p-1" type="text" name="stok[]" disabled>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="kps">Kps</label>
                            <input id="kps" class="form-control p-1 text-black" type="text" name="kps[]" disabled>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="p1">P1</label>
                            <input id="p1" class="form-control" type="text" name="p1[]">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="p2">P2</label>
                            <input id="p2" class="form-control" type="text" name="p2[]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="kandungan">Kandungan</label>
                            <input id="kandungan" onclick="hitungRacikan(0)" onchange="hitungJml(0)"
                                class="form-control kandungan0" type="text" name="kandungan[]">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="jml">Jml</label>
                            <input id="jml" class="form-control jml0" type="text" name="jml[]">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
                <x-adminlte-button id="deleteRacikan" onclick="deleteRowRacikan()"
                    class="md:col-md-1 sm:col-sm-6 delete-form-racikan mr-1" theme="danger" label="-" />
                <x-adminlte-button id="addRacikan" class="md:col-md-1 sm:col-sm-6 add-form-racikan" theme="success"
                    label="+" />
                <x-adminlte-button id="resepRacikanButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary"
                    type="submit" label="Simpan" />
            </div>
        </form>
    </x-adminlte-callout>

    @if(count($resepRacikan) > 0)
    <x-adminlte-callout theme="info">
        <div class="table-responsive">
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
                            <button class="btn btn-danger btn-sm"
                                onclick='hapusRacikan("{{$r->no_resep}}", "{{$r->no_racik}}", event)'>Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </x-adminlte-callout>
    @endif
</div>

@push('js')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    $('.obat-racikan').select2({
                placeholder: 'Pilih obat racikan',
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
            }).on("select2:select", function(e){
                var data = e.params.data;
                $.ajax({
                    url: '/api/obat/'+data.id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $('#stok').val(data.stok_akhir);
                        $('#kps').val(data.kapasitas);
                        $('#p1').val('1');
                        $('#p2').val('1');
                    }
                });
            });

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

        function getIndexValue(name, index) {
            var doc = document.getElementsByName(name);
            return doc[index].value;
        }

        var x = 1;
        $("#addRacikan").click(function(e){
            e.preventDefault();
            var variable = '';
            var variable = '' + 
                            '<div class="row racikan-'+x+'">' + 
                            '                                <div class="col-md-5">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none">Obat</label>' + 
                            '                                        <select name="obatRacikan[]" class="form-control obat-racikan w-100" id="obatRacikan'+x+'" data-placeholder="Pilih Obat">' + 
                            '                                        </select>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="stok'+x+'">Stok</label>' + 
                            '                                        <input id="stok'+x+'" class="form-control no-label" type="text" name="stok[]" disabled>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="kps'+x+'">Kps</label>' + 
                            '                                        <input id="kps'+x+'" class="form-control" type="text" name="kps[]" disabled>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="p1'+x+'">P1</label>' + 
                            '                                        <input id="p1'+x+'" class="form-control" type="text" name="p1[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none"  for="p2'+x+'">P2</label>' + 
                            '                                        <input id="p2'+x+'" class="form-control" type="text" name="p2[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-2">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="kandungan'+x+'">Kandungan</label>' + 
                            '                                        <input id="kandungan'+x+'" class="form-control" type="text" name="kandungan[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="jml'+x+'">Jml</label>' + 
                            '                                        <input id="jml'+x+'" class="form-control" type="text" name="jml[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                            </div>' + 
                            '';
            $(".containerRacikan").append(variable.trim());
            $('#'+'obatRacikan'+x, ".containerRacikan").select2({
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
            }).on();
            x++;
        });

        function deleteRowRacikan(){
            $(".racikan-"+x).remove();
            if(x>=1){
                x--;
            }
        }

        function hitungRacikan(index){
            var p1 = getIndexValue('p1[]', index);
            var p2 = getIndexValue('p2[]', index);
            var jmlRacikan = $('#jumlah_racikan').val();
            var kps = getIndexValue('kps[]', index);
            var kandungan = (p1/p2)*kps;
            var jml = (p1/p2)*jmlRacikan;
            $(".kandungan"+index).val(kandungan.toFixed(1));
        }

        function hitungJml(index){
            var jmlRacikan = $('#jumlah_racikan').val();
            var kandungan = getIndexValue('kandungan[]', index);
            var kps = getIndexValue('kps[]', index);
            var jml = (kandungan/kps)*jmlRacikan;
            $(".jml"+index).val(jml);
        }

</script>
@endpush