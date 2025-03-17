<div>
    <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-pills" collapsible="collapsed"
        maximizable>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="resep-tab" data-toggle="tab" data-target="#resep" type="button"
                    role="tab" aria-controls="resep" aria-selected="true">Resep</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="copyresep-tab" data-toggle="tab" data-target="#copyresep" type="button"
                    role="tab" aria-controls="copyresep" aria-selected="false">Resep Racikan</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="resep" role="tabpanel" aria-labelledby="resep-tab">
                <x-adminlte-callout theme="info" title="Input Resep">
                    <form method="post" id="resepForm" action="{{url('/api/resep_ranap/'.$encryptNoRawat)}}">
                        @csrf
                        <div class="containerResep">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="visible-sm">Nama Obat</label>
                                        <select name="obat[]" class="form-control obat w-100" id="obat"
                                            data-placeholder="Pilih Obat">
                                        </select>
                                    </div>
                                </div>
                                <x-adminlte-input id="jumlah" label="Jumlah" name="jumlah[]" fgroup-class="col-md-2"
                                    placeholder="Jumlah" />
                                <x-adminlte-input id="aturan" label="Aturan Pakai" name="aturan[]"
                                    fgroup-class="col-md-5" placeholder="Aturan Pakai" />
                            </div>
                        </div>
                        <div class="row justify-content-end" style="gap: 10px">
                            <x-adminlte-select2 id="dokter" name="dokter" fgroup-class="col-md-6 col-sm-6 my-auto"
                                data-placeholder="Pilih Dokter">
                                <option value="">Pilih Dokter ......</option>
                                @foreach($dokters as $dokter)
                                <option value="{{$dokter->kd_dokter}}">{{$dokter->nm_dokter}}</option>
                                @endforeach
                            </x-adminlte-select2>
                            <x-adminlte-select2 id="depo" name="depo" fgroup-class="col-md-3 col-sm-5 my-auto"
                                data-placeholder="Pilih Depo">
                                <option value="">Pilih Depo ......</option>
                                @foreach($depos as $depo)
                                <option value="{{$depo->kd_bangsal}}" @if($depo->kd_bangsal == $setBangsal->kd_depo)
                                    selected @endif>{{$depo->nm_bangsal}}</option>
                                @endforeach
                            </x-adminlte-select2>
                            <x-adminlte-button id="addFormResep" class="md:col-md-1 sm:col-sm-6 add-form-resep"
                                theme="success" label="+" />
                            <x-adminlte-button id="resepButton" class="md:col-md-2 sm:col-sm-6 ml-1" theme="primary"
                                type="submit" label="Simpan" />
                        </div>
                    </form>
                </x-adminlte-callout>

                @if(count($resep) > 0)
                <x-adminlte-callout theme="info">
                    <div class="table-responsive">
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
                                        <button class="btn btn-danger btn-sm"
                                            onclick='hapusObat("{{$r->no_resep}}", "{{$r->kode_brng}}", event)'>Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-adminlte-callout>
                @endif
                <x-adminlte-callout theme="info" title="Riwayat Peresepan">
                    @php
                    $config["responsive"] = true;
                    $config['order'] = [[1, 'desc']];
                    $jumlahRiwayat = count($riwayatPeresepan);
                    @endphp

                    <x-adminlte-datatable id="tableRiwayatResep" :heads="$heads" :config="$config" head-theme="dark"
                        striped hoverable bordered compressed>
                        @if(count($riwayatPeresepan) > 0)
                        @foreach($riwayatPeresepan as $r)
                        <tr>
                            <td class="align-middle text-center">{{$r->no_resep}}</td>
                            <td class="align-middle text-center">{{$r->tgl_peresepan}}</td>
                            <td>
                                @php
                                $racikan = $resepRacikan->where('no_resep', $r->no_resep)->first();
                                $resepObat = $getResepObat($r->no_resep);
                                @endphp
                                <ul class="p-4">
                                    @if($racikan)
                                    <li>Racikan - {{$racikan->nama_racik ?? 'Tidak ada nama'}} - {{$racikan->jml_dr ??
                                        '0'}} -
                                        [{{$racikan->aturan_pakai ?? 'Tidak ada aturan'}}]</li>
                                    <ul>
                                        @foreach($getDetailRacikan($racikan->no_resep) as $ror)
                                        <li>{{$ror->nama_brng}} - {{$ror->p1}}/{{$ror->p2}} - {{$ror->kandungan}} -
                                            {{$ror->jml}}</li>
                                        @endforeach
                                    </ul>
                                    @endif

                                    @if(count($resepObat) > 0)
                                    @foreach($resepObat as $ro)
                                    <li>{{$ro->nama_brng}} - {{$ro->jml}} - [{{$ro->aturan_pakai}}]</li>
                                    @endforeach
                                    @else
                                    <li>Tidak ada data obat</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="align-middle text-center">
                                <x-adminlte-button onclick='getCopyResep({{$r->no_resep}}, event)'
                                    class="mx-auto btn-sm" theme="primary" icon="fa fa-sm fa-fw fa-pen" />
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data riwayat peresepan</td>
                        </tr>
                        @endif
                    </x-adminlte-datatable>
                </x-adminlte-callout>

            </div>
            <div class="tab-pane fade" id="copyresep" role="tabpanel" aria-labelledby="copyresep-tab">
                <x-adminlte-callout theme="info" title="Input Resep Racikan">
                    <form method="post" id="copyresepForm"
                        action="{{url('/api/ranap/resep/racikan/'.$encryptNoRawat)}}">
                        @csrf
                        <div class="containerCopyResep">
                            <div class="row">
                                <x-adminlte-input id="obat_racikan" label="Nama Racikan" name="nama_racikan"
                                    fgroup-class="col-md-12" />
                                <x-adminlte-select-bs id="metode_racikan" name="metode_racikan" label="Metode Racikan"
                                    fgroup-class="col-md-6" data-live-search data-live-search-placeholder="Cari..."
                                    data-show-tick>
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
                                        <select name="obatRacikan[]" class="form-control obat-racikan w-100"
                                            id="obatRacikan" data-placeholder="Pilih Obat">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="stok">Stok</label>
                                        <input id="stok" class="form-control stok p-1" type="text" name="stok[]"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="kps">Kps</label>
                                        <input id="kps" class="form-control kps text-black p-1" type="text" name="kps[]"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="p1">P1</label>
                                        <input id="p1" class="form-control p-1" oninput="hitungRacikan(0)" type="text"
                                            name="p1[]">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="p2">P2</label>
                                        <input id="p2" class="form-control p-2" oninput="hitungRacikan(0)" type="text"
                                            name="p2[]">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="kandungan">Kandungan</label>
                                        <input id="kandungan" oninput="hitungJml(0)"
                                            class="form-control p-1 kandungan-0" type="text" name="kandungan[]">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="jml">Jml</label>
                                        <input id="jml" class="form-control p-1 jml-0" type="text" name="jml[]">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <x-adminlte-button id="deleteRacikan" onclick="deleteRowRacikan()"
                                class="md:col-md-1 sm:col-sm-6 delete-form-racikan mr-1" theme="danger" label="-" />
                            <x-adminlte-button id="addRacikan" class="md:col-md-1 sm:col-sm-6 add-form-racikan"
                                theme="success" label="+" />
                            <x-adminlte-button id="resepRacikanButton" class="md:col-md-2 sm:col-sm-6 ml-1"
                                theme="primary" type="submit" label="Simpan" />
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
        </div>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="modalCopyResep" title="Copy Resep" size="lg" theme="teal" icon="fas fa-bell" v-centered
    static-backdrop scrollable>
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
        <x-adminlte-button class="mr-2" id="simpanCopyResep" theme="primary" label="Simpan" data-dismiss="modal" />
        <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
    </x-slot>
</x-adminlte-modal>

@push('css')
<style>
    .no-border {
        border: 0;
        box-shadow: none;
        /* You may want to include this as bootstrap applies these styles too */
    }
</style>
@endpush

@push('js')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    // Fungsi untuk me-refresh halaman dengan parameter yang benar
    function reloadPageWithParams() {
        console.log("Memanggil fungsi reloadPageWithParams...");
        var currentUrl = window.location.href;
        var baseUrl = currentUrl.split('?')[0]; // Ambil URL dasar tanpa parameter
        var params = new URLSearchParams(window.location.search);
        
        // Pastikan parameter yang diperlukan ada
        if (!params.has('no_rawat')) {
            params.set('no_rawat', "{{$noRawat}}");
        }
        if (!params.has('no_rm')) {
            params.set('no_rm', "{{$noRM}}");
        }
        if (!params.has('bangsal')) {
            params.set('bangsal', "{{$bangsal}}");
        }
        
        // Tambahkan timestamp untuk menghindari cache
        params.set('ts', new Date().getTime());
        
        // Buat URL baru dengan parameter yang benar
        var newUrl = baseUrl + '?' + params.toString();
        
        console.log("Memuat ulang halaman ke: " + newUrl);
        window.location.href = newUrl;
    }
    
    // Override fungsi reloadPage yang sudah ada
    function reloadPage() {
        reloadPageWithParams();
    }

    let bangsal = $('#depo').val() ?? '{{$setBangsal->kd_depo}}';
    $('#depo').on('change', function(e){
        bangsal = $(this).val();
        console.log(bangsal);
    });

    // Event listener untuk perubahan jumlah racikan
    $('#jumlah_racikan').on('change', function() {
        var jmlRacikan = $(this).val();
        console.log("Jumlah racikan diubah menjadi:", jmlRacikan);
        
        // Update jumlah untuk semua obat racikan
        for (var j = 0; j <= i; j++) {
            hitungRacikan(j);
        }
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })
    function getIndexValue(name, index) {
            var doc = document.getElementsByName(name);
            return doc[index].value;
        }

        var i = 0;
        $("#addRacikan").click(function(e){
            e.preventDefault();
            i++;
            var variable = '';
            var variable = '' + 
                            '<div class="row racikan-'+i+'">' + 
                            '                                <div class="col-md-5">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none">Obat</label>' + 
                            '                                        <select name="obatRacikan[]" class="form-control obat-racikan w-100" id="obatRacikan'+i+'" data-placeholder="Pilih Obat">' + 
                            '                                        </select>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none stok-'+i+'" for="stok'+i+'">Stok</label>' + 
                            '                                        <input id="stok'+i+'" class="form-control p-1 stok-'+i+'" type="text" name="stok[]" disabled>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="kps'+i+'">Kps</label>' + 
                            '                                        <input id="kps'+i+'" class="form-control p-1 kps-'+i+'" type="text" name="kps[]" disabled>' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="p1'+i+'">P1</label>' + 
                            '                                        <input id="p1'+i+'" class="form-control p-1 p1-'+i+'" oninput="hitungRacikan('+i+')" type="text" name="p1[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none"  for="p2'+i+'">P2</label>' + 
                            '                                        <input id="p2'+i+'" class="form-control p-1 p2-'+i+'" oninput="hitungRacikan('+i+')" type="text" name="p2[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-2">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="kandungan'+i+'">Kandungan</label>' + 
                            '                                        <input id="kandungan'+i+'" class="form-control p-1 kandungan-'+i+'" type="text" oninput="hitungJml('+i+')" name="kandungan[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                                <div class="col-md-1">' + 
                            '                                    <div class="form-group">' + 
                            '                                        <label class="d-sm-none" for="jml'+i+'">Jml</label>' + 
                            '                                        <input id="jml'+i+'" class="form-control p-1 jml-'+i+'" type="text" name="jml[]">' + 
                            '                                    </div>' + 
                            '                                </div>' + 
                            '                            </div>' + 
                            '';

            $(".containerRacikan").append(variable.trim());
            $('#'+'obatRacikan'+i, ".containerRacikan").select2({
                placeholder: 'Pilih obat',
                ajax: {
                    url: '/api/ranap/'+bangsal+'/obat',
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
            }).on("change", function(e){
                var data = $(this).select2('data');
                var id = $(this).attr('id').replace ( /[^\d.]/g, '' );
                var idRow = parseInt(id);
                var jmlRacikan = $('#jumlah_racikan').val();
                $.ajax({
                    url: '/api/obat/'+data[0].id,
                    data:{
                        status:'ranap',
                        kode: bangsal
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log("Data obat racikan:", data);
                        $('input[id="stok'+idRow+'"]').val(data.stok_akhir);
                        $('input[id="kps'+idRow+'"]').val(data.kapasitas);
                        $('input[id="p1'+idRow+'"]').val('1');
                        $('input[id="p2'+idRow+'"]').val('1');
                        $('input[id="kandungan'+idRow+'"]').val(data.kapasitas);
                        $('input[id="jml'+idRow+'"]').val(jmlRacikan);
                    }
                });
            });
        });

        function deleteRowRacikan(){
            $(".racikan-"+i).remove();
            if(i>=1){
                i--;
            }
        }

        function hitungRacikan(index){
            console.log("hitungRacikan dipanggil dengan index:", index);
            var p1 = getIndexValue('p1[]', index);
            var p2 = getIndexValue('p2[]', index);
            var jmlRacikan = $('#jumlah_racikan').val();
            var kps = getIndexValue('kps[]', index);
            
            console.log("p1:", p1, "p2:", p2, "jmlRacikan:", jmlRacikan, "kps:", kps);
            
            // Konversi ke float
            p1 = parseFloat(p1) || 0;
            p2 = parseFloat(p2) || 1; // Hindari pembagian dengan nol
            kps = parseFloat(kps) || 0;
            jmlRacikan = parseFloat(jmlRacikan) || 0;
            
            // Cek apakah p2 adalah nol untuk menghindari pembagian dengan nol
            if (p2 === 0) {
                p2 = 1;
                console.warn("p2 bernilai 0, diubah menjadi 1 untuk menghindari pembagian dengan nol");
            }
            
            var rasio = p1 / p2;
            var kandungan = rasio * kps;
            var jml = rasio * jmlRacikan;
            
            console.log("Rasio p1/p2:", rasio, "Hasil perhitungan - kandungan:", kandungan, "jml:", jml);
            
            // Update nilai di form
            $(".kandungan-"+index).val(kandungan.toFixed(2));
            $(".jml-"+index).val(jml.toFixed(2));
        }

        function hitungJml(index){
            console.log("hitungJml dipanggil dengan index:", index);
            var kps = parseFloat(getIndexValue('kps[]', index)) || 0;
            var jmlRacikan = parseFloat($('#jumlah_racikan').val()) || 0;
            var kandungan = parseFloat($(".kandungan-"+index).val()) || 0;
            var p1 = parseFloat(getIndexValue('p1[]', index)) || 0;
            var p2 = parseFloat(getIndexValue('p2[]', index)) || 1;
            
            console.log("kps:", kps, "jmlRacikan:", jmlRacikan, "kandungan:", kandungan, "p1:", p1, "p2:", p2);
            
            // Pastikan kandungan tidak nol untuk menghindari pembagian dengan nol
            if (kandungan === 0) {
                console.warn("Kandungan bernilai 0, tidak dapat menghitung jumlah");
                return;
            }
            
            // Hitung jumlah berdasarkan kandungan yang diinput
            var jml = 0;
            
            // Jika p1/p2 = 1/1, gunakan perhitungan langsung dari kandungan
            if (p1 === p2 && p1 !== 0) {
                jml = jmlRacikan * (kandungan / kps);
                console.log("Menggunakan perhitungan langsung: jmlRacikan * (kandungan / kps) =", jml);
            } else {
                // Jika tidak, gunakan perhitungan berdasarkan rasio p1/p2
                var rasio = p1 / p2;
                if (rasio !== 0) {
                    jml = jmlRacikan * (kandungan / (kps * rasio));
                    console.log("Menggunakan perhitungan dengan rasio:", jml);
                } else {
                    jml = 0;
                    console.warn("Rasio p1/p2 adalah 0, tidak dapat menghitung jumlah");
                }
            }
            
            // Periksa apakah nilai valid
            if(isNaN(jml) || !isFinite(jml)){
                jml = 0;
                console.warn("Hasil perhitungan tidak valid");
            }
            
            console.log("Hasil perhitungan jml final:", jml);
            
            // Update nilai di form
            $(".jml-"+index).val(jml.toFixed(2));
        }

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
            html += '<hr class="d-sm-none">';
            html += '   <div class="col-md-5">';
            html += '       <div class="form-group">';
            html += '            <label class="d-sm-none">Nama Obat</label>';
            html += '            <select name="obat[]" class="form-control obat-'+x+'" id="obat'+x+'" data-placeholder="Pilih Obat">';
            html += '            </select>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="col-md-2">';
            html += '        <div class="form-group">';
            html += '            <label class="d-sm-none">Jumlah</label>';
            html += '            <input type="text" name="jumlah[]" class="form-control" id="jumlah'+x+'" placeholder="Jumlah"/>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="col-md-5">';
            html += '        <div class="form-group">';
            html += '            <label class="d-sm-none">Aturan Pakai</label>';
            html += '            <div class="input-group">';
            html += '            <input name="aturan[]" id="aturan'+x+'" class="form-control" placeholder="Aturan Pakai">';
            html += '            <div class="input-group-append">';
            html += '                 <button class="btn btn-danger delete" value="row_resep'+x+'">-</button>';
            html += '            </div>';
            html += '            </div>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
            $(wrapper).append(html.trim()); 
            $('#'+'obat'+x, wrapper).select2({
                placeholder: 'Pilih obat',
                ajax: {
                    url: function (params) {
                        return '/api/ranap/'+bangsal+'/obat';
                    },
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

        $('.obat').select2({
            placeholder: 'Pilih obat',
            ajax: {
                url: function (params) {
                    return '/api/ranap/'+bangsal+'/obat';
                },
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

        $('.obat-racikan').select2({
            placeholder: 'Pilih obat racikan',
            ajax: {
                url: '/api/ranap/' + bangsal + '/obat',
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
            var jmlRacikan = $('#jumlah_racikan').val();
            console.log("Obat racikan pertama dipilih:", data);
            $.ajax({
                url: '/api/obat/'+data.id,
                data:{
                    status:'ranap',
                    kode: bangsal
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log("Data obat racikan pertama:", data);
                    $('#stok').val(data.stok_akhir);
                    $('#kps').val(data.kapasitas);
                    $('#p1').val('1');
                    $('#p2').val('1');
                    $('#kandungan').val(data.kapasitas);
                    $('#jml').val(jmlRacikan);
                }
            });
        });

        function formatData (data) {
                var $data = $(
                    '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
                );
                return $data;
        };

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
            
            console.log("Mengambil data resep dengan no_resep: " + no_resep);
            
            // Validasi nomor resep
            if (!no_resep) {
                Swal.fire({
                    title: 'Error',
                    text: 'Nomor resep tidak valid',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            
            // Tambahkan parameter bangsal ke URL API
            var apiUrl = '/api/ranap/resep-copy/' + no_resep;
            if (bangsal) {
                apiUrl += '?bangsal=' + bangsal;
            }
            
            console.log("URL API: " + apiUrl);
            
            $.ajax({
                url: apiUrl,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                beforeSend: function() {
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
                    console.log("Data resep diterima:", data);
                    Swal.close();
                    
                    if (data && data.length > 0) {
                        $.each(data, function (i, item) {
                            if (item.kode_brng && item.nama_brng && item.jml) {
                                trHTML += '<tr class="body"><td><input type="text" name="jml_copyresep[]" multiple="multiple" value="' + item.jml + '" size="5"></td>'
                                        + '<td><input type="hidden" name="kode_brng_copyresep[]" multiple="multiple" value="' + item.kode_brng +'" > ' + item.nama_brng + '</td>'
                                        + '<td><input type="text" name="aturan_copyresep[]" multiple="multiple" value="' + (item.aturan_pakai || '') + '"></td></tr>';
                            } else {
                                console.warn("Item data tidak lengkap:", item);
                            }
                        });
                        
                        if (trHTML) {
                            $('.tbBodyCopy').append(trHTML);
                            $('#modalCopyResep').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Peringatan',
                                text: 'Data resep tidak valid',
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                        }
                    } else if (data && data.status === 'gagal') {
                        Swal.fire({
                            title: 'Informasi',
                            text: data.message || 'Tidak ada data resep yang dapat disalin',
                            icon: 'info',
                            confirmButtonText: 'Ok'
                        });
                    } else {
                        Swal.fire({
                            title: 'Informasi',
                            text: 'Tidak ada data resep yang dapat disalin',
                            icon: 'info',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error saat mengambil data resep:", xhr.responseText);
                    let errorMessage = 'Gagal mengambil data resep. ';
                    
                    try {
                        const responseData = JSON.parse(xhr.responseText);
                        if (responseData && responseData.message) {
                            errorMessage += responseData.message;
                        } else if (xhr.status === 404) {
                            errorMessage += 'Resep tidak ditemukan.';
                        } else if (xhr.status === 500) {
                            errorMessage += 'Server Error.';
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        errorMessage += 'Status: ' + xhr.status + ' - ' + error;
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
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
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    let _token = $('meta[name="csrf-token"]').attr('content');
                    console.log("Menghapus obat dengan noResep: " + $noResep + " dan kode obat: " + $kdObat);
                    
                    $.ajax({
                        url: '/api/obat/' + $noResep + '/' + $kdObat,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: _token
                        },
                        beforeSend: function() {
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
                            console.log("Respons sukses:", data);
                            if (data && data.status === 'sukses') {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: data.pesan || 'Obat berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.value) {
                                        console.log("Memuat ulang halaman setelah hapus obat...");
                                        reloadPage();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: (data && data.pesan) ? data.pesan : 'Obat gagal dihapus. Silakan coba lagi.',
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.value) {
                                        console.log("Memuat ulang halaman setelah gagal hapus obat...");
                                        reloadPage();
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error saat menghapus obat:", xhr.responseText);
                            let errorMessage = 'Obat gagal dihapus. ';
                            
                            try {
                                const responseData = JSON.parse(xhr.responseText);
                                if (responseData && responseData.pesan) {
                                    errorMessage += responseData.pesan;
                                } else if (xhr.status === 404) {
                                    errorMessage += 'Obat tidak ditemukan.';
                                } else if (xhr.status === 500) {
                                    errorMessage += 'Terjadi kesalahan di server. Silakan hubungi administrator.';
                                    console.error("Detail error 500:", xhr.responseText);
                                } else if (xhr.status === 400) {
                                    errorMessage += 'Permintaan tidak valid. Obat mungkin sudah divalidasi.';
                                }
                            } catch (e) {
                                errorMessage += 'Status: ' + xhr.status + ' - ' + error;
                                console.error("Error parsing response:", e);
                            }
                            
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        }
                    });
                }
            });
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
                        url: '/api/resep/hapus-racikan',
                        type: 'POST',
                        dataType: 'json',
                        data:{
                            no_resep: $noResep,
                            no_racik: $noRacik,
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
                        success: function(data) {
                            console.log(data);
                            data.status == 'sukses' ? Swal.fire(
                                'Terhapus!',
                                data.pesan,
                                'success'
                            ).then((result) => {
                                if (result.value) {
                                    console.log("Memuat ulang halaman setelah hapus racikan...");
                                    reloadPage();
                                }
                            }) : Swal.fire(
                                'Gagal!',
                                data.pesan,
                                'error'
                            ).then((result) => {
                                if (result.value) {
                                    console.log("Memuat ulang halaman setelah hapus racikan...");
                                    reloadPage();
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
            let dokter = $('#dokter').val();
            
            // Validasi data sebelum dikirim
            if (obat.length === 0) {
                Swal.fire({
                    title: 'Error',
                    text: 'Tidak ada data obat yang akan disimpan',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            
            // Validasi dokter
            if (!dokter) {
                Swal.fire({
                    title: 'Error',
                    text: 'Pilih dokter terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            
            // Pastikan semua field terisi
            for (let i = 0; i < obat.length; i++) {
                if (!jumlah[i] || jumlah[i] <= 0) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Jumlah obat harus diisi dan lebih dari 0',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                
                if (!aturan[i]) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Aturan pakai harus diisi',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
            }
            
            var data = {
                obat: obat,
                jumlah: jumlah,
                aturan_pakai: aturan,
                status: 'ranap',
                kode: bangsal,
                dokter: dokter,
                _token: _token,
            };
            
            console.log("Data yang akan dikirim:", data);
            
            $.ajax({
                type: 'POST',
                url: '/api/resep_ranap/'+"{{$encryptNoRawat}}",
                data: data,
                dataType: 'json',
                beforeSend: function() {
                    $('#modalCopyResep').modal('hide');
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
                    console.log("Respons server:", response);
                    if(response.status == 'sukses'){
                        Swal.fire({
                            title: 'Sukses',
                            text: 'Data berhasil disimpan',
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.value) {
                                console.log("Memuat ulang halaman setelah copy resep...");
                                reloadPage();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: response.pesan || 'Gagal menyimpan data',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error saat menyimpan data:", xhr.responseText);
                    let errorMessage = 'Gagal menyimpan data. ';
                    
                    try {
                        const responseData = JSON.parse(xhr.responseText);
                        if (responseData && responseData.pesan) {
                            errorMessage += responseData.pesan;
                        } else if (xhr.status === 404) {
                            errorMessage += 'Endpoint API tidak ditemukan.';
                        } else if (xhr.status === 500) {
                            errorMessage += 'Server Error.';
                        }
                    } catch (e) {
                        errorMessage += 'Status: ' + xhr.status + ' - ' + error;
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });

        $("#resepButton").click(function(e) {
            e.preventDefault();
            let _token   = $('meta[name="csrf-token"]').attr('content');
            let obat = getValue('obat[]');
            let jumlah = getValue('jumlah[]');
            let aturan = getValue('aturan[]');
            let dokter = $('#dokter').val();
            
            // Validasi input
            if (obat.length === 0 || obat[0] === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Pilih obat terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            
            if (!dokter) {
                Swal.fire({
                    title: 'Error',
                    text: 'Pilih dokter terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
                return;
            }
            
            for (let i = 0; i < obat.length; i++) {
                if (!jumlah[i] || jumlah[i] <= 0) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Jumlah obat harus diisi dan lebih dari 0',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                
                if (!aturan[i]) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Aturan pakai harus diisi',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
            }
            
            var form = $("#resepForm");
            var data = {
                obat:obat,
                jumlah:jumlah,
                aturan_pakai:aturan,
                status:'Ranap',
                kode:bangsal,
                dokter:dokter,
                _token:_token,
            };
            var url = form.attr('action');
            var method = form.attr('method');
            console.log("Menyimpan resep dengan data:", data);

            $.ajax({
                type: method,
                url: url,
                data: data,
                dataType: 'json',
                beforeSend: function() {
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
                    console.log("Respon server:", response);
                    if(response.status == 'sukses'){
                        Swal.fire({
                        title: 'Sukses',
                        text: 'Data berhasil disimpan',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.value) {
                                console.log("Memuat ulang halaman...");
                                reloadPage();
                            }
                        })
                    }
                    else{
                        Swal.fire({
                        title: 'Gagal',
                        text: response.pesan || 'Gagal menyimpan resep',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                        })
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error saat menyimpan resep:", xhr.responseText);
                    let errorMessage = 'Terjadi kesalahan sistem';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response && response.pesan) {
                            errorMessage = response.pesan;
                        }
                    } catch (e) {
                        if (xhr.status === 404) {
                            errorMessage = 'API endpoint tidak ditemukan. Periksa konfigurasi route.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan di server. Silakan hubungi administrator.';
                        }
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
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
            let kdObat = getValue('obatRacikan[]');
            let p1 = getValue('p1[]');
            let p2 = getValue('p2[]');
            let kandungan = getValue('kandungan[]');
            let jml = getValue('jml[]');
            $.ajax({
                type: 'POST',
                url: '/api/ranap/resep/racikan/'+"{{$encryptNoRawat}}",
                data: {
                    nama_racikan:obat,
                    metode_racikan:metode,
                    jumlah_racikan:jumlah,
                    aturan_racikan:aturan,
                    keterangan_racikan:keterangan,
                    kd_obat:kdObat,
                    p1:p1,
                    p2:p2,
                    kandungan:kandungan,
                    jml:jml,
                    _token:_token,
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#modalRacikan').modal('hide')
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
                                console.log("Memuat ulang halaman setelah resep racikan...");
                                reloadPage();
                            }
                        })
                    }
                    else{
                        Swal.fire({
                        title: 'Gagal',
                        text: response.message,
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

        function hitung(){

        } 

</script>

@endpush