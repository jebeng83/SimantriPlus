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
                    $config = [
                    "responsive" => true,
                    "order" => [[1, 'desc']],
                    "columnDefs" => [
                    ["className" => "text-center", "targets" => [0, 1, 3]]
                    ],
                    "processing" => true,
                    "language" => [
                    "processing" => "Memuat data...",
                    "info" => "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "emptyTable" => "Tidak ada data riwayat peresepan",
                    "zeroRecords" => "Tidak ditemukan data yang sesuai",
                    ],
                    ];
                    $jumlahRiwayat = count($riwayatPeresepan);
                    @endphp

                    <x-adminlte-datatable id="tableRiwayatResep" :heads="$heads" :config="$config" head-theme="dark"
                        striped hoverable bordered compressed>
                        @if($riwayatPeresepan && count($riwayatPeresepan) > 0)
                        @foreach($riwayatPeresepan as $r)
                        <tr>
                            <td>{{$r->no_resep}}</td>
                            <td>{{$r->tgl_peresepan}}</td>
                            <td>
                                @php
                                $racikan = $resepRacikan->where('no_resep', $r->no_resep)->first();
                                $resepObat = $getResepObat($r->no_resep);
                                @endphp
                                <ul class="p-4">
                                    @if($racikan)
                                    <li>Racikan - {{$racikan->nama_racik ?? 'Tidak ada nama'}} - {{$racikan->jml_dr
                                        ?? '0'}} -
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
                            <td>
                                <button onclick="getCopyResep('{{$r->no_resep}}', event)"
                                    class="btn btn-primary btn-sm"><i class="fa fa-sm fa-fw fa-pen"></i></button>
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
    /* CSS untuk memperbaiki dropdown select2 */
    .select2-container {
        width: 100% !important;
        z-index: 1050 !important;
        /* Mengurangi z-index dari 9999 */
    }

    .select2-container--open {
        z-index: 1051 !important;
        /* Mengurangi z-index dari 99999 */
    }

    .select2-container--open .select2-dropdown {
        z-index: 1051 !important;
        /* Mengurangi z-index dari 99999 */
    }

    /* Perbaikan spesifik untuk posisi dropdown dan z-index */
    .select2-dropdown {
        border: 1px solid #ddd !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        top: 0 !important;
    }

    /* Pastikan SweetAlert selalu di atas semua elemen */
    .swal2-container {
        z-index: 9999 !important;
    }

    .select2-results {
        max-height: 300px !important;
        overflow-y: auto !important;
    }

    .select2-search__field {
        width: 100% !important;
        font-size: 14px !important;
        padding: 8px !important;
    }

    .select2-selection {
        min-height: 38px !important;
        border: 1px solid #ced4da !important;
    }

    .select2-selection__rendered {
        line-height: 36px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container--default .select2-results>.select2-results__options {
        max-height: 300px !important;
    }

    .select2-search {
        padding: 8px !important;
    }

    .modal-dialog {
        z-index: 1050 !important;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 4px !important;
    }
</style>
@endpush

@push('js')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    // Deklarasi variabel
    var dataObat = [];
    var counter = 1;
    var rowCount = 1;
    var counterRacikan = 1;
    var bangsal = $('#depo').val();
    let activeIndex = 0;
    
    $(document).ready(function() {
        console.log("Document ready, initializing components...");
        
        // Pastikan bangsal terdefinisi
        bangsal = $('#depo').val() || "{{$setBangsal->kd_depo}}";
        console.log("Bangsal aktif:", bangsal);
        
        // Fix untuk select2 dropdown
        setTimeout(function() {
            initObatSelect2();
        }, 200);
        
        loadRiwayatPeresepan();
        
        // Tambahkan event listener khusus untuk select2
        $(document).on('select2:open', function() {
            console.log("Select2 opened");
            setTimeout(function() {
                // Fokuskan ke kolom pencarian
                document.querySelector('.select2-search__field').focus();
                
                // Atur z-index tinggi
                $('.select2-container--open').css('z-index', 99999);
                $('.select2-dropdown').css('z-index', 99999);
                $('.select2-results').css('z-index', 99999);
            }, 10);
        });
        
        // Event handler untuk tab switching
        $('button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            console.log("Tab changed");
            // Re-inisialisasi select2 pada tab yang aktif
            setTimeout(function() {
                initObatSelect2();
            }, 200);
        });
        
        // Event handler untuk perubahan bangsal/depo
        $('#depo').on('change', function() {
            bangsal = $(this).val();
            console.log("Bangsal changed to:", bangsal);
            // Reinisialisasi select2 setelah perubahan bangsal
            setTimeout(function() {
                initObatSelect2();
            }, 200);
        });
    });
    
    // Fungsi untuk inisialisasi select2 untuk obat
    function initObatSelect2() {
        console.log("Initializing Select2 for obat");
        
        // Destroy existing select2 instances
        $('.obat, .obat-racikan').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                console.log("Destroying existing Select2 instance for: " + this.id);
                $(this).select2('destroy');
            }
        });
        
        // Set bangsal jika belum ada
        bangsal = $('#depo').val() || "{{$setBangsal->kd_depo}}";
        console.log("Menggunakan bangsal: " + bangsal);
        
        // Inisialisasi untuk obat reguler
        $('.obat').each(function() {
            console.log("Initializing regular obat:", this.id);
            $(this).select2({
                placeholder: 'Pilih obat',
                allowClear: true,
                dropdownParent: $(document.body),
                width: '100%',
                ajax: {
                    url: '/api/ranap/'+bangsal+'/obat',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        console.log("Data obat diterima:", data.length, "item");
                        return { results: data };
                    },
                    cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return "Ketik minimal 3 karakter...";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    noResults: function() {
                        return "Tidak ada hasil";
                    }
                }
            }).on('select2:open', function() {
                console.log("Select2 opened for:", this.id);
                // Fokuskan ke kolom pencarian
                setTimeout(function() {
                    $('.select2-search__field').focus();
                }, 0);
                
                // Atur z-index yang lebih rendah untuk elemen dropdown
                $('.select2-container--open').css('z-index', 1051);
                $('.select2-dropdown').css('z-index', 1051);
                $('.select2-results').css('z-index', 1051);
            }).on('change', function(e) {
                var data = $(this).select2('data');
                var id = $(this).attr('id').replace('obat', '');
                
                if (data && data.length > 0) {
                    console.log("Obat dipilih:", data[0].id, "-", data[0].text);
                }
            });
        });
        
        // Inisialisasi untuk obat racikan
        $('.obat-racikan').each(function() {
            console.log("Initializing racikan obat:", this.id);
            $(this).select2({
            placeholder: 'Pilih obat racikan',
                allowClear: true,
                dropdownParent: $(document.body),
                width: '100%',
            ajax: {
                    url: '/api/ranap/'+bangsal+'/obat',
                dataType: 'json',
                delay: 250,
                    processResults: function(data) {
                        console.log("Data obat racikan diterima:", data.length, "item");
                        return { results: data };
                },
                cache: true
            },
            templateResult: formatData,
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return "Ketik minimal 3 karakter...";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    noResults: function() {
                        return "Tidak ada hasil";
                    }
                }
            }).on('select2:open', function() {
                console.log("Select2 racikan opened for:", this.id);
                // Fokuskan ke kolom pencarian
                setTimeout(function() {
                    $('.select2-search__field').focus();
                }, 0);
                
                // Atur z-index yang lebih rendah untuk elemen dropdown
                $('.select2-container--open').css('z-index', 1051);
                $('.select2-dropdown').css('z-index', 1051);
                $('.select2-results').css('z-index', 1051);
            }).on('change', function(e) {
                var data = $(this).select2('data');
                if (data && data.length > 0) {
                    var id = $(this).attr('id').replace(/[^\d.]/g, '');
                    var idRow = parseInt(id);
            var jmlRacikan = $('#jumlah_racikan').val();
                    
                    console.log("Obat racikan dipilih:", data[0].id, "-", data[0].text, "untuk baris", idRow);
                    
            $.ajax({
                        url: '/api/obat/'+data[0].id,
                        data: {
                            status: 'ranap',
                    kode: bangsal
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                            console.log("Obat data received:", data);
                            $('input[id="stok'+idRow+'"]').val(data.stok_akhir);
                            $('input[id="kps'+idRow+'"]').val(data.kapasitas);
                            $('input[id="p1'+idRow+'"]').val('1');
                            $('input[id="p2'+idRow+'"]').val('1');
                            $('input[id="kandungan'+idRow+'"]').val(data.kapasitas);
                            $('input[id="jml'+idRow+'"]').val(jmlRacikan);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error mengambil data obat:", error);
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    }
    
    // Fungsi format data untuk select2
    function formatData(data) {
        if (data.loading) return data.text;
        var $container = $(
            '<div class="select2-result-obat">' +
            '<div class="select2-result-obat__kode"><b>' + data.id + '</b></div>' +
            '<div class="select2-result-obat__nama"> - ' + data.text + '</div>' +
            '</div>'
        );
        return $container;
    }
    
    // Fungsi untuk memuat ulang halaman
    function reloadPage() {
        location.reload();
    }
    
    // Fungsi untuk mendapatkan array dari nilai input
    function getValue(selector) {
        var values = [];
        $('input[name="' + selector + '"], select[name="' + selector + '"]').each(function() {
            values.push($(this).val() || '');
        });
        return values;
    }

    // Fungsi untuk reload riwayat peresepan
    function loadRiwayatPeresepan() {
        let _token = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
            url: '/api/ranap/riwayat-peresepan/' + "{{$encryptNoRawat}}",
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                beforeSend: function() {
                $('#tableRiwayatResep_wrapper').addClass('d-none');
                $('#tableRiwayatResep').closest('.card-body').append('<div id="loading-resep" class="text-center py-3"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</div>');
            },
            success: function(response) {
                console.log("Data riwayat peresepan:", response);
                $('#loading-resep').remove();
                $('#tableRiwayatResep_wrapper').removeClass('d-none');
                
                if ($.fn.dataTable.isDataTable('#tableRiwayatResep')) {
                    $('#tableRiwayatResep').DataTable().destroy();
                }
                
                var table = $('#tableRiwayatResep').DataTable({
                    destroy: true,
                    responsive: true,
                    data: response.status === 'sukses' && response.data ? response.data : [],
                    columns: [
                        { data: 'no_resep' },
                        { data: 'tgl_peresepan' },
                        { 
                            data: null,
                            render: function(data, type, row) {
                                let html = '<ul class="p-4">';
                                if (data.detail && data.detail.length > 0) {
                                    $.each(data.detail, function(i, item) {
                                        if (item.racikan) {
                                            html += '<li>Racikan - ' + item.nama_racik + ' - ' + 
                                                  item.jml + ' - [' + item.aturan_pakai + ']</li>';
                        } else {
                                            html += '<li>' + item.nama_brng + ' - ' + 
                                                  item.jml + ' - [' + item.aturan_pakai + ']</li>';
                                        }
                        });
                    } else {
                                    html += '<li>Tidak ada data obat</li>';
                                }
                                html += '</ul>';
                                return html;
                            }
                        },
                        { 
                            data: null,
                            render: function(data, type, row) {
                                return '<button onclick="getCopyResep(\'' + data.no_resep + '\', event)" ' +
                                       'class="btn btn-primary btn-sm"><i class="fa fa-sm fa-fw fa-pen"></i></button>';
                            }
                        }
                    ],
                    language: {
                        processing: "Memuat data...",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "Tidak ada data riwayat peresepan",
                        zeroRecords: "Tidak ditemukan data yang sesuai"
                    },
                    order: [[1, 'desc']],
                    columnDefs: [
                        {className: "text-center", targets: [0, 1, 3]}
                    ]
                });
                },
                error: function(xhr, status, error) {
                console.error("Error saat mengambil riwayat peresepan:", xhr.responseText);
                $('#loading-resep').remove();
                $('#tableRiwayatResep_wrapper').removeClass('d-none');
                $('#tableRiwayatResep').closest('.card-body').append('<div class="alert alert-danger">Gagal memuat data: ' + error + '</div>');
            }
        });
    }

    // Fungsi untuk copy resep
    function getCopyResep(no_resep, e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        
        console.log("Copy resep untuk nomor:", no_resep);
        
        $.ajax({
            url: '/api/ranap/resep-copy/' + no_resep,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('.tbBodyCopy').html('<tr><td colspan="3" class="text-center">Memuat data...</td></tr>');
                $('#modalCopyResep').modal('show');
            },
            success: function(response) {
                console.log("Data copy resep:", response);
                
                $('.tbBodyCopy').empty();
                
                if (response && response.length > 0) {
                    $.each(response, function(i, item) {
                        let row = `<tr>
                            <td>${item.jml}</td>
                            <td>${item.nama_brng}</td>
                            <td>${item.aturan_pakai}</td>
                        </tr>`;
                        $('.tbBodyCopy').append(row);
                    });
                } else {
                    $('.tbBodyCopy').html('<tr><td colspan="3" class="text-center">Tidak ada data resep</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error saat mengambil data copy resep:", xhr.responseText);
                $('.tbBodyCopy').html('<tr><td colspan="3" class="text-center">Gagal memuat data: ' + error + '</td></tr>');
                }
            });
        }

    // Fungsi untuk hapus obat
    function hapusObat(noResep, kdObat, e) {
            e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        
            Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Anda yakin ingin menghapus obat ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
            if (result.isConfirmed) {
                    $.ajax({
                    url: '/api/ranap/resep/hapus-obat',
                        type: 'POST',
                        data: {
                        no_resep: noResep,
                        kode_brng: kdObat,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("Hapus obat berhasil:", response);
                        Swal.fire(
                            'Terhapus!',
                            'Obat berhasil dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                        console.error("Error hapus obat:", xhr.responseText);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus obat.',
                            'error'
                        );
                        }
                    });
                }
            });
        }

    // Fungsi untuk hapus racikan
    function hapusRacikan(noResep, noRacik, e) {
            e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        
            Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Anda yakin ingin menghapus racikan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
            }).then((result) => {
            if (result.isConfirmed) {
                    $.ajax({
                    url: '/api/ranap/resep/hapus-racikan',
                        type: 'POST',
                    data: {
                        no_resep: noResep,
                        no_racik: noRacik,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("Hapus racikan berhasil:", response);
                        Swal.fire(
                                'Terhapus!',
                            'Racikan berhasil dihapus.',
                                'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error hapus racikan:", xhr.responseText);
                            Swal.fire(
                                'Gagal!',
                            'Terjadi kesalahan saat menghapus racikan.',
                                'error'
                        );
                        }
                });
                }
        });
        }

    // Tambahkan fungsi untuk tab racikan
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
        
        // Inisialisasi select2 untuk elemen baru
        setTimeout(function() {
            var newSelect = $('#obatRacikan' + i);
            console.log("Initializing new racikan select:", newSelect.attr('id'));
            
            newSelect.select2({
                placeholder: 'Pilih obat racikan',
                allowClear: true,
                dropdownParent: $('body'),
                width: '100%',
                ajax: {
                    url: '/api/ranap/' + bangsal + '/obat',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return { results: data };
                    },
                    cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return "Ketik minimal 3 karakter...";
                    },
                    searching: function() {
                        return "Mencari...";
                    },
                    noResults: function() {
                        return "Tidak ada hasil";
                    }
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-search__field').focus();
                }, 0);
            }).on('change', function(e) {
                var data = $(this).select2('data');
                if (data && data.length > 0) {
                    var currentId = i;
                    
                    $.ajax({
                        url: '/api/obat/' + data[0].id,
                        data: {
                            status: 'ranap',
                            kode: bangsal
                        },
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log("Racikan obat data received:", data);
                            $('input[id="stok' + currentId + '"]').val(data.stok_akhir);
                            $('input[id="kps' + currentId + '"]').val(data.kapasitas);
                            $('input[id="p1' + currentId + '"]').val('1');
                            $('input[id="p2' + currentId + '"]').val('1');
                            $('input[id="kandungan' + currentId + '"]').val(data.kapasitas);
                            
                            var jmlRacikan = $('#jumlah_racikan').val() || 0;
                            $('input[id="jml' + currentId + '"]').val(jmlRacikan);
                        }
                    });
                }
            });
            
            // Buka dropdown secara otomatis
            setTimeout(function() {
                newSelect.select2('open');
            }, 100);
        }, 100);
    });

    function deleteRowRacikan(){
        $(".racikan-"+i).remove();
        if(i>=1){
            i--;
        }
    }
    
    // Fungsi hitung-hitung untuk racikan
    function hitungRacikan(index) {
        // Pastikan semua input dikonversi ke angka dengan benar
        var p1 = parseFloat($('#p1'+index).val()) || 0;
        var p2 = parseFloat($('#p2'+index).val()) || 1;
        var jmlRacikan = parseFloat($('#jumlah_racikan').val()) || 0;
        var kps = parseFloat($('#kps'+index).val()) || 0;
        
        // Hindari pembagian dengan nol
        if (p2 === 0) p2 = 1;
        
        var rasio = p1 / p2;
        var kandungan = rasio * kps;
        var jml = rasio * jmlRacikan;
        
        // Batasi nilai maksimal untuk menghindari angka yang terlalu besar
        if (jml > 1000) jml = 1000;
        if (kandungan > 1000) kandungan = 1000;
        
        // Validasi nilai yang dihasilkan
        if (isNaN(kandungan) || !isFinite(kandungan)) kandungan = 0;
        if (isNaN(jml) || !isFinite(jml)) jml = 0;
        
        // Gunakan presisi 2 desimal untuk semua nilai
        $('#kandungan'+index).val(kandungan.toFixed(2));
        $('#jml'+index).val(jml.toFixed(2));
        
        console.log("Racikan " + index + ": p1=" + p1 + ", p2=" + p2 + ", kps=" + kps + 
                   ", kandungan=" + kandungan.toFixed(2) + ", jml=" + jml.toFixed(2));
    }
    
    function hitungJml(index) {
        var kps = parseFloat($('#kps'+index).val()) || 0;
        var jmlRacikan = parseFloat($('#jumlah_racikan').val()) || 0;
        var kandungan = parseFloat($('#kandungan'+index).val()) || 0;
        
        if (kps === 0 || kandungan === 0) return;
        
        var jml = jmlRacikan * (kandungan / kps);
        
        if (isNaN(jml) || !isFinite(jml)) jml = 0;
        
        $('#jml'+index).val(jml.toFixed(2));
    }
    
    // Event listener untuk jumlah racikan
    $('#jumlah_racikan').on('change', function() {
        var jmlRacikan = $(this).val();
        for (var j = 0; j <= i; j++) {
            hitungRacikan(j);
        }
    });
    
    // Simpan resep
    $("#resepButton").click(function(e) {
        e.preventDefault();
        
        // Sembunyikan pesan error sebelumnya
        $('.alert-danger').hide();
        $('.text-danger').remove();
        
        console.log("Tombol simpan resep diklik");
        
        // Ambil data dari form
        var obat = getValue('obat[]');
        var jumlah = getValue('jumlah[]');
        var aturan = getValue('aturan[]');
        var dokter = $('#dokter').val();
        var depo = $('#depo').val();
        
        console.log("Nilai form yang diambil:", {
            obatLength: obat.length,
            jumlahLength: jumlah.length,
            aturanLength: aturan.length,
            dokter: dokter,
            depo: depo
        });
        
        // Validasi data
        if (obat.filter(Boolean).length === 0) {
                Swal.fire({
                    icon: 'error',
                title: 'Oops...',
                text: 'Pilih minimal satu obat!'
                });
            return false;
            }
            
            if (!dokter) {
            console.warn("Dokter tidak diisi");
                Swal.fire({
                    icon: 'error',
                title: 'Oops...',
                text: 'Pilih dokter terlebih dahulu!'
                });
            return false;
            }
            
        if (!depo) {
            console.warn("Depo tidak diisi");
                    Swal.fire({
                        icon: 'error',
                title: 'Oops...',
                text: 'Pilih depo terlebih dahulu!'
            });
            return false;
        }
        
        // Persiapkan data untuk dikirim sebagai objek key/value sederhana bukan FormData
        var formData = {};
        var obatValid = [];
        var jumlahValid = [];
        var aturanValid = [];
        
        for (var i = 0; i < obat.length; i++) {
            if (obat[i]) {
                obatValid.push(obat[i]);
                jumlahValid.push(jumlah[i] || 0);
                aturanValid.push(aturan[i] || '');
            }
        }
        
        if (obatValid.length === 0) {
            console.warn("Tidak ada data obat valid untuk dikirim");
                    Swal.fire({
                        icon: 'error',
                title: 'Oops...',
                text: 'Tidak ada obat valid untuk disimpan!'
            });
            return false;
        }
        
        formData.obat = obatValid;
        formData.jumlah = jumlahValid;
        formData.aturan_pakai = aturanValid;
        formData.dokter = dokter;
        formData.kode = depo;
        formData._token = $('meta[name="csrf-token"]').attr('content');
        
        // Log data untuk debugging
        console.log("Data yang akan dikirim:", formData);
        
        // Non-aktifkan tombol untuk mencegah klik ganda
        $("#resepButton").prop('disabled', true);
        
        // Tampilkan loading
                    Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                            Swal.showLoading();
                        }
                    });
        
        // Kirim request
        $.ajax({
            url: '/api/resep_ranap/{{$encryptNoRawat}}',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log("Simpan resep berhasil:", response);
                
                        Swal.fire({
                            icon: 'success',
                    title: 'Berhasil!',
                    text: 'Resep berhasil disimpan',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => {
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error simpan resep:", xhr.responseText);
                console.error("Status:", xhr.status);
                
                // Aktifkan kembali tombol
                $("#resepButton").prop('disabled', false);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan resep.';
                
                // Coba parse response error
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.pesan) {
                        errorMessage = response.pesan;
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                    
                    if (response.status === 'sukses' && response.no_resep) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Resep berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false,
                            willClose: () => {
                                location.reload();
                            }
                        });
                        return;
                        }
                    } catch (e) {
                    console.error("Error parsing response:", e);
                    }
                    
                    Swal.fire({
                        icon: 'error',
                    title: 'Oops...',
                    text: errorMessage
                    });
                }
            });
        
        return false; // Mencegah form submit normal
        });

    // Simpan resep racikan
    $("#resepRacikanButton").click(function(e) {
            e.preventDefault();
        
        // Sembunyikan pesan error sebelumnya
        $('.alert-danger').hide();
        $('.text-danger').remove();
        
        // Ambil data dari form
        var nama_racikan = $('#obat_racikan').val();
        var metode_racikan = $('#metode_racikan').val();
        var jumlah_racikan = $('#jumlah_racikan').val();
        var aturan_racikan = $('#aturan_racikan').val();
        var keterangan_racikan = $('#keterangan_racikan').val();
        var dokter = $('#dokter').val();
        var depo = $('#depo').val();
        
        var obatRacikan = getValue('obatRacikan[]');
        var p1 = getValue('p1[]');
        var p2 = getValue('p2[]');
        var kandungan = getValue('kandungan[]');
        var jml = getValue('jml[]');
        
        // Validasi data
        if (!nama_racikan) {
                Swal.fire({
                    icon: 'error',
                title: 'Oops...',
                text: 'Nama racikan harus diisi!'
                });
                return;
            }
            
        if (obatRacikan.filter(Boolean).length === 0) {
                Swal.fire({
                    icon: 'error',
                title: 'Oops...',
                text: 'Pilih minimal satu obat untuk racikan!'
                });
                return;
            }
            
        if (!jumlah_racikan || jumlah_racikan <= 0) {
                    Swal.fire({
                        icon: 'error',
                title: 'Oops...',
                text: 'Jumlah racikan harus diisi dengan angka lebih dari 0!'
                    });
                    return;
                }
                
        if (!dokter) {
                    Swal.fire({
                        icon: 'error',
                title: 'Oops...',
                text: 'Pilih dokter terlebih dahulu!'
                    });
                    return;
                }
        
        if (!depo) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih depo terlebih dahulu!'
            });
            return;
        }
        
        // Persiapkan data untuk dikirim
        var formData = new FormData();
        formData.append('nama_racikan', nama_racikan);
        formData.append('metode_racikan', metode_racikan);
        formData.append('jumlah_racikan', jumlah_racikan);
        formData.append('aturan_racikan', aturan_racikan);
        formData.append('keterangan_racikan', keterangan_racikan);
        formData.append('dokter', dokter);
        formData.append('kode', depo); // Ubah nama field menjadi 'kode' sesuai kebutuhan controller
        
        for (var i = 0; i < obatRacikan.length; i++) {
            if (obatRacikan[i]) {
                formData.append('kd_obat[]', obatRacikan[i]); // Ubah nama field menjadi kd_obat sesuai controller
                formData.append('p1[]', p1[i] || 0);
                formData.append('p2[]', p2[i] || 1);
                formData.append('kandungan[]', kandungan[i] || 0);
                formData.append('jml[]', jml[i] || 0);
            }
        }
        
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Non-aktifkan tombol untuk mencegah klik ganda
        $("#resepRacikanButton").prop('disabled', true);
        
        // Tampilkan loading
                    Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                        Swal.showLoading();
                    }
                    });
        
        // Set timeout untuk mencegah request terlalu cepat
        setTimeout(function() {
            // Kirim request
            $.ajax({
                url: '/api/ranap/resep/racikan/{{$encryptNoRawat}}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log("Simpan resep racikan berhasil:", response);
                    
                    // Tampilkan pesan sukses selama 1.5 detik, kemudian refresh halaman
                        Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Resep racikan berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => {
                            // Tambahkan effect fadeOut sebelum reload
                            $('.card').fadeOut(300, function() {
                                location.reload();
                            });
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error simpan resep racikan:", xhr.responseText);
                    console.error("Status:", xhr.status);
                    
                    // Aktifkan kembali tombol
                    $("#resepRacikanButton").prop('disabled', false);
                    
                    let errorMessage = 'Terjadi kesalahan saat menyimpan resep racikan.';
                    let shouldReload = false;
                    
                    // Coba parse response error
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.pesan) {
                            errorMessage = response.pesan;
                            // Periksa apakah pesan error menunjukkan masalah dengan tabel yang tidak ada
                            if (response.pesan.includes("permintaan_resep_ranap") && response.status === 'sukses') {
                                errorMessage = "Resep racikan berhasil disimpan, tetapi terdapat peringatan: " + response.pesan;
                                shouldReload = true;
                            }
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                        
                        // Jika status sukses tetapi ada error, tetap anggap berhasil
                        if (response.status === 'sukses' && response.no_resep) {
                            console.log("Data berhasil disimpan meskipun ada error:", response.no_resep);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Resep racikan berhasil disimpan dengan nomor: ' + response.no_resep,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                            return;
                        }
                    } catch (e) {
                        console.error("Error parsing response racikan:", e);
                        if (xhr.status === 400) {
                            errorMessage = 'Data resep racikan tidak lengkap atau tidak valid. Periksa kembali input Anda.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Data pasien tidak ditemukan. Refresh halaman dan coba lagi.';
                        } else if (xhr.status === 500) {
                            // Periksa apakah error 500 terkait permintaan_resep_ranap
                            if (xhr.responseText.includes("permintaan_resep_ranap")) {
                                errorMessage = "Resep racikan mungkin telah tersimpan, tetapi terjadi kesalahan terkait tabel permintaan_resep_ranap. Halaman akan dimuat ulang.";
                                shouldReload = true;
                            } else {
                                errorMessage = 'Terjadi kesalahan pada server. Hubungi administrator.';
                            }
                        }
                    }
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        icon: shouldReload ? 'warning' : 'error',
                        title: shouldReload ? 'Perhatian' : 'Oops...',
                        text: errorMessage,
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (shouldReload) {
                            location.reload();
                        }
                    });
                }
            });
        }, 300); // 300ms delay sebelum kirim request untuk UI yang lebih responsif
    });
    
    // Simpan copy resep
    $("#simpanCopyResep").click(function(e) {
        e.preventDefault();
        
        console.log("Menyimpan copy resep");
        
        // Ambil data dari modal
        var rows = $('.table-copy-resep tbody tr');
        var dataResep = [];
        
        if (rows.length === 0 || (rows.length === 1 && rows.find('td').length === 1)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tidak ada data resep untuk disimpan!'
            });
            return;
        }
        
        // Ambil dokter dan depo
        var dokter = $('#dokter').val();
        var depo = $('#depo').val();
        
        // Validasi dokter dan depo
        if (!dokter) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih dokter terlebih dahulu!'
            });
            return;
        }
        
        if (!depo) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih depo terlebih dahulu!'
            });
            return;
        }
        
        // Persiapkan array untuk menyimpan data yang akan dikirim
        var obatArray = [];
        var jumlahArray = [];
        var aturanArray = [];
        
        // Kumpulkan data dari tabel
        rows.each(function() {
            var tds = $(this).find('td');
            if (tds.length > 1) { // Pastikan baris berisi data valid (bukan pesan kosong)
                var jml = $(tds[0]).text();
                var namaObat = $(tds[1]).text();
                var aturanPakai = $(tds[2]).text();
                
                // Cari kode obat berdasarkan nama
                // Karena kode obat tidak ada di modal, kita perlu mencari obat ini dulu
                obatArray.push(namaObat);
                jumlahArray.push(jml);
                aturanArray.push(aturanPakai);
            }
        });
        
        // Validasi data sebelum dikirim
        if (obatArray.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tidak ada data valid untuk disimpan!'
            });
            return;
        }
        
        // Persiapkan data untuk dikirim dalam format yang sesuai dengan endpoint API
        var formData = {
            obat: obatArray,
            jumlah: jumlahArray,
            aturan_pakai: aturanArray,
            dokter: dokter,
            kode: depo,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        // Non-aktifkan tombol untuk mencegah klik ganda
        $("#simpanCopyResep").prop('disabled', true);
        
        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cari kode obat berdasarkan nama
        // Langkah 1: Cari kode obat dari database
        $.ajax({
            url: '/api/cari-kode-obat',
            type: 'POST',
            data: {
                nama_obat: obatArray,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(responseKode) {
                console.log("Hasil pencarian kode obat:", responseKode);
                
                // Jika berhasil mendapatkan kode obat
                if (responseKode.status === 'sukses' && responseKode.data && responseKode.data.length > 0) {
                    // Update array obat dengan kode_brng
                    formData.obat = responseKode.data;
                    
                    // Langkah 2: Kirim resep dengan kode obat yang sudah didapat
                    $.ajax({
                        url: '/api/resep_ranap/{{$encryptNoRawat}}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log("Simpan copy resep berhasil:", response);
                            
                            // Tutup modal
                            $('#modalCopyResep').modal('hide');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Resep berhasil disimpan',
                                timer: 1500,
                                showConfirmButton: false,
                                willClose: () => {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            handleResponseError(xhr, error);
                        }
                    });
                } else {
                    // Jika gagal mendapatkan kode obat
                    $("#simpanCopyResep").prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mendapatkan kode obat. ' + (responseKode.pesan || 'Silakan coba lagi')
                    });
                }
            },
            error: function(xhr, status, error) {
                handleResponseError(xhr, error);
            }
        });
        
        // Fungsi untuk menangani error response
        function handleResponseError(xhr, error) {
            console.error("Error simpan resep:", xhr.responseText);
            
            // Aktifkan kembali tombol
            $("#simpanCopyResep").prop('disabled', false);
            
            let errorMessage = 'Terjadi kesalahan saat menyimpan resep.';
            
            // Coba parse response error
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.pesan) {
                    errorMessage = response.pesan;
                } else if (response.message) {
                    errorMessage = response.message;
                }
                
                if (response.status === 'sukses' && response.no_resep) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Resep berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => {
                            location.reload();
                        }
                    });
                    return;
                }
            } catch (e) {
                console.error("Error parsing response:", e);
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessage
            });
        }
    });

    // Tambahkan config DataTables global yang lebih kuat
    $.extend(true, $.fn.dataTable.defaults, {
        destroy: true,
        retrieve: true,
        processing: true,
        language: {
            processing: "Memuat data...",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data",
            zeroRecords: "Tidak ditemukan data yang sesuai"
        }
    });

    // Tambahkan event handler untuk tombol tambah obat
    $("#addFormResep").click(function(e) {
        e.preventDefault();
        console.log("Tombol tambah obat diklik");
        
        counter++;
        var newRow = `
            <div class="row form-row-${counter}">
                <div class="col-md-5">
                    <div class="form-group">
                        <select name="obat[]" class="form-control obat" id="obat${counter}" data-placeholder="Pilih Obat">
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" class="form-control" name="jumlah[]" placeholder="Jumlah">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="aturan[]" placeholder="Aturan Pakai">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-row" data-row="${counter}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Tambahkan baris baru ke form
        $(".containerResep").append(newRow);
        
        // Inisialisasi select2 untuk baris baru
        setTimeout(function() {
            initObatSelect2();
            
            // Buka dropdown secara otomatis
            setTimeout(function() {
                $('#obat' + counter).select2('open');
            }, 100);
        }, 100);
    });
    
    // Event handler untuk tombol hapus baris
    $(document).on('click', '.remove-row', function() {
        var rowNum = $(this).data('row');
        $('.form-row-' + rowNum).remove();
    });

    // Tambahkan event listener untuk tab switching
    $('button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        console.log("Tab changed");
        // Re-inisialisasi select2 pada tab yang aktif
        setTimeout(function() {
            initObatSelect2();
        }, 200);
    });
</script>

@endpush