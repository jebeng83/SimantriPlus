<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-book-medical" collapsible maximizable>
        <!-- Tampilkan informasi tanggal dan waktu saat ini -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <span class="badge badge-info p-2">
                    <i class="fas fa-calendar-day"></i> Tanggal: {{ date('d-m-Y') }}
                </span>
                <span class="badge badge-primary p-2 ml-2">
                    <i class="fas fa-clock"></i> Jam: <span id="current-time">{{ date('H:i:s') }}</span>
                </span>
            </div>
            <div>
                <span class="badge badge-warning p-2">
                    <i class="fas fa-notes-medical"></i> Total Pemeriksaan Hari Ini: <span id="today-count">{{
                        $countTodayExams }}</span>
                </span>
            </div>
        </div>

        <x-adminlte-card theme="dark" title="Input Pemeriksaan" theme-mode="outline" maximizable
            collapsible="collapsed">
            <!-- Tambahkan template selector -->
            <div class="mb-3">
                <label for="template-selector">Template Pemeriksaan:</label>
                <select id="template-selector" class="form-control">
                    <option value="">-- Pilih Template --</option>
                    <option value="normal">Pemeriksaan Normal</option>
                    <option value="demam">Demam</option>
                    <option value="sakit-kepala">Sakit Kepala</option>
                    <option value="sesak">Sesak Napas</option>
                    <option value="nyeri-perut">Nyeri Perut</option>
                    <option value="diare">Diare</option>
                    <option value="hipertensi">Hipertensi</option>
                    <option value="diabetes">Diabetes Mellitus</option>
                    <option value="batuk">Batuk</option>
                    <option value="gatal">Gatal-gatal/Alergi</option>
                    <option value="jantung">Penyakit Jantung</option>
                    <option value="visite">Visite Dokter</option>
                    <option value="visite-hari1">Visite Hari 1</option>
                    <option value="visite-hari2">Visite Hari 2</option>
                    <option value="visite-hari3">Visite Hari 3</option>
                </select>
            </div>

            <form id="pemeriksaanForm">
                <div class="row">
                    <x-adminlte-textarea name="keluhan" label="Subjek" fgroup-class="col-md-6" rows="4">
                    </x-adminlte-textarea>
                    <x-adminlte-textarea name="pemeriksaan" label="Objek" fgroup-class="col-md-6" rows="4">
                        {{ old('pemeriksaan',
                        'KU : Composmentis, Baik
                        Thorax : Cor S1-2 intensitas normal, reguler, bising (-)
                        Pulmo : SDV +/+ ST -/-
                        Abdomen : Supel, NT(-), peristaltik (+) normal.
                        EXT : Oedem -/-') }}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-textarea name="penilaian" label="Asesmen" fgroup-class="col-md-6" rows="2">
                        {{ old('penilaian', '- ') }}
                    </x-adminlte-textarea>
                    <x-adminlte-textarea name="instruksi" label="Instruksi" fgroup-class="col-md-6" rows="2">
                        {{ old('instruksi', 'Istirahat Cukup, PHBS ') }}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-textarea name="rtl" label="Plan" fgroup-class="col-md-6" rows="2">
                        {{ old('rtl', 'Edukasi Kesehatan') }}
                    </x-adminlte-textarea>
                    <!--<x-adminlte-textarea name="alergi" label="Alergi" fgroup-class="col-md-6" rows="2" value="Tidak Ada">-->
                    <!--</x-adminlte-textarea>-->
                    <x-adminlte-textarea name="alergi" label="Alergi" fgroup-class="col-md-6" rows="2">
                        {{ old('alergi', 'Tidak Ada') }}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-input name="suhu" label="Suhu" fgroup-class="col-md-3" placeholder="36.5" />
                    <x-adminlte-input name="berat" label="Berat" fgroup-class="col-md-3" placeholder="60" />
                    <x-adminlte-input name="tinggi" label="Tinggi Badan" fgroup-class="col-md-3" placeholder="165" />
                    <x-adminlte-input name="gcs" label="GCS (E, V, M)" fgroup-class="col-md-3" placeholder="4,5,6" />
                </div>
                <div class="row">
                    <x-adminlte-input name="spo2" label="SP02" fgroup-class="col-md-2" placeholder="98" />
                    <x-adminlte-input name="tensi" label="Tensi" fgroup-class="col-md-2" placeholder="120/80" />
                    <x-adminlte-input name="nadi" label="Nadi" fgroup-class="col-md-2" placeholder="80" />
                    <x-adminlte-input name="respirasi" label="Respirasi" fgroup-class="col-md-3" placeholder="20" />
                    <x-adminlte-select-bs name="kesadaran" label="Kesadaran" fgroup-class="col-md-3">
                        <option value="Compos Mentis">Compos Mentis</option>
                        <option value="Apatis">Apatis</option>
                        <option value="Delirium">Delirium</option>
                        <option value="Somnolence">Somnolence</option>
                        <option value="Sopor">Sopor</option>
                        <option value="Coma">Coma</option>
                    </x-adminlte-select-bs>
                </div>
                <x-adminlte-textarea name="evaluasi" label="Evaluasi" fgroup-class="col-md-12" rows="2">
                    {{ old('evaluasi', 'Evaluasi Keadaan Umum Tiap 6 Jam') }}
                </x-adminlte-textarea>
                <div class="row justify-content-end">
                    <x-adminlte-button id="resetFormButton" class="col-2 mr-1" theme="secondary" label="Reset"
                        icon="fas fa-undo" />
                    <x-adminlte-button id="copyPemeriksaanButton" class="col-2 mr-1" theme="warning" label="Copy"
                        icon="fas fa-copy" />
                    <x-adminlte-button id="pemeriksaanButton" class="col-2 ml-1" theme="primary" label="Simpan"
                        icon="fas fa-save" />
                </div>
            </form>
        </x-adminlte-card>
        <x-adminlte-card theme="info" title="Riwayat" theme-mode="outline" header-class="rounded-bottom" collapsible>
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="toggleToday" checked>
                        <label class="custom-control-label" for="toggleToday">Hanya Tampilkan Data Hari Ini</label>
                    </div>
                </div>
                <div>
                    <button id="refreshTableBtn" class="btn btn-sm btn-primary">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                    <button id="expandAllBtn" class="btn btn-sm btn-info ml-2">
                        <i class="fas fa-expand-alt"></i> Perluas Semua
                    </button>
                </div>
            </div>
            @php
            $config["responsive"] = true;
            $config['order'] = [[0, 'desc'], [1, 'desc']];
            $config['language'] = [
            'emptyTable' => 'Tidak ada data riwayat',
            'zeroRecords' => 'Tidak ditemukan data yang sesuai',
            'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
            'infoEmpty' => 'Menampilkan 0 sampai 0 dari 0 entri',
            'infoFiltered' => '(difilter dari _MAX_ total entri)',
            'search' => 'Cari:',
            'paginate' => [
            'first' => 'Pertama',
            'last' => 'Terakhir',
            'next' => 'Selanjutnya',
            'previous' => 'Sebelumnya'
            ],
            ];
            $config['retrieve'] = true;
            @endphp
            <x-adminlte-datatable id="tableRiwayatPemeriksaanRanap" :heads="$heads" head-theme="dark" :config="$config"
                striped hoverable bordered compressed>
                @foreach($riwayat as $row)
                <tr>
                    <td>{{ $row->tgl_perawatan }}</td>
                    <td>{{ $row->jam_rawat }}</td>
                    <td>{{ $row->keluhan }}</td>
                    <td>{{ $row->suhu_tubuh }}</td>
                    <td>{{ $row->tensi }}</td>
                    <td>{{ $row->nadi }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow"
                                onclick="showModalEdit('{{$row->no_rawat}}' ,'{{$row->tgl_perawatan}}', '{{$row->jam_rawat}}')"
                                title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-info mx-1 shadow"
                                onclick="showDetailPemeriksaan('{{$row->no_rawat}}' ,'{{$row->tgl_perawatan}}', '{{$row->jam_rawat}}')"
                                title="Lihat Detail">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-card>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="editPemeriksaan" title="Edit Pemeriksaan" theme="info" size='lg' v-centered static-backdrop
    scrollable>
    <div></div>
    {{--
    <x-adminlte-button class="d-flex ml-auto" id="editPemeriksaanButton" theme="primary" label="Simpan"
        icon="fas fa-sign-in" /> --}}
</x-adminlte-modal>

<x-adminlte-modal id="detailPemeriksaan" title="Detail Pemeriksaan" theme="success" size='lg' v-centered static-backdrop
    scrollable>
    <div id="detailPemeriksaanContent"></div>
    <x-slot name="footerSlot">
        <x-adminlte-button class="mr-auto" theme="success" label="Tutup" data-dismiss="modal" />
        <x-adminlte-button id="printDetailBtn" theme="primary" label="Cetak" icon="fas fa-print" />
    </x-slot>
</x-adminlte-modal>

@push('js')
{{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    // Variabel untuk menyimpan instance DataTable
    var riwayatTable;
    
    // Fungsi untuk mengupdate jam saat ini
    function updateClock() {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var seconds = now.getSeconds().toString().padStart(2, '0');
        $('#current-time').text(hours + ':' + minutes + ':' + seconds);
        setTimeout(updateClock, 1000);
    }
    
    $(document).ready(function() {
        // Inisialisasi DataTable saat halaman dimuat
        if ($.fn.dataTable.isDataTable('#tableRiwayatPemeriksaanRanap')) {
            riwayatTable = $('#tableRiwayatPemeriksaanRanap').DataTable();
        } else {
            initDataTable();
        }
        
        // Mulai update jam
        updateClock();
        
        // Event listener untuk tombol toggle Today Only
        $('#toggleToday').change(function() {
            refreshRiwayatTable();
        });
        
        // Event listener untuk tombol refresh
        $('#refreshTableBtn').click(function() {
            refreshRiwayatTable();
        });
        
        // Event listener untuk tombol expand all
        $('#expandAllBtn').click(function() {
            $('[data-card-widget="collapse"]').map(function() {
                var $button = $(this);
                var $card = $button.closest('.card');
                if ($card.find('.card-body').is(':hidden')) {
                    $button.trigger('click');
                }
            });
        });
        
        // Template selector
        $('#template-selector').change(function() {
            var template = $(this).val();
            if (template !== '') {
                applyTemplate(template);
            }
        });
        
        // Reset form button
        $('#resetFormButton').click(function(e) {
            e.preventDefault();
            resetForm();
            Swal.fire({
                text: 'Form telah direset',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false
            });
        });
        
        // Print detail button
        $('#printDetailBtn').click(function() {
            printDetail();
        });
        
        // Atur interval refresh otomatis setiap 30 detik
        setInterval(function() {
            refreshRiwayatTable();
        }, 30000);
    });
    
    function showModalEdit(noRawat, tgl, jam){
        $.ajax({
            url: "{{url('/ranap/pemeriksaan')}}"+"/"+"{{$encryptNoRawat}}"+"/"+tgl+"/"+jam,
            type: "GET",
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
            success: function(response){
                Swal.close();
                var html = '' + 
                                '<input id="editjam" name="editjam" type="hidden" value="'+response.data.jam_rawat+'" class="form-control">' +                    
                                '<input id="edittgl" name="edittgl" type="hidden" value="'+response.data.tgl_perawatan+'" class="form-control">' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editkeluhan"> Subjek </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editkeluhan" name="editkeluhan" class="form-control" rows="4">'+response.data.keluhan+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editpemeriksaan"> Objek </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editpemeriksaan" name="editpemeriksaan" class="form-control" rows="4">'+response.data.pemeriksaan+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editpenilaian"> Asesmen </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editpenilaian" name="editpenilaian" class="form-control" rows="2">'+response.data.penilaian+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editinstruksi"> Instruksi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editinstruksi" name="editinstruksi" class="form-control" rows="2">'+response.data.instruksi+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editrtl"> Plan </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editrtl" name="editrtl" class="form-control" rows="2">'+response.data.rtl+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="editalergi"> Alergi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="editalergi" name="editalergi" class="form-control" rows="2">'+response.data.alergi+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="editsuhu"> Suhu Badan (C) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editsuhu" name="editsuhu" value="'+response.data.suhu_tubuh+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="editberat"> Berat (Kg) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editberat" name="editberat" value="'+response.data.berat+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="edittinggi"> Tinggi Badan (Cm) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="edittinggi" name="edittinggi" value="'+response.data.tinggi+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="editgcs"> GCS (E, V, M) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editgcs" name="editgcs" value="'+response.data.gcs+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="edittensi"> SP02 </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editspo2" name="editspo2" value="'+response.data.spo2+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="edittensi"> Tensi </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="edittensi" name="edittensi" value="'+response.data.tensi+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="editnadi"> Nadi (per Menit) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editnadi" name="editnadi" value="'+response.data.nadi+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="editrespirasi"> Respirasi (per Menit) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="editrespirasi" name="editrespirasi" value="'+response.data.respirasi+'" class="form-control"> </div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="editkesadaran"> Kesadaran </label>' + 
                                '		<div class="input-group">' + 
                                '			<div class="dropdown bootstrap-select form-control">' + 
                                '				<select id="editkesadaran" name="editkesadaran" class="form-control" tabindex="-98">' + 
                                '					<option>Compos Mentis</option>' + 
                                '					<option>Somnolence</option>' + 
                                '					<option>Sopor</option>' + 
                                '					<option>Coma</option>' + 
                                '				</select>' + 
                                '				<button type="button" class="btn dropdown-toggle btn-light" data-toggle="dropdown" role="combobox" aria-owns="bs-select-2" aria-haspopup="listbox" aria-expanded="false" data-id="editkesadaran" title="Compos Mentis">' + 
                                '					<div class="filter-option">' + 
                                '						<div class="filter-option-inner">' + 
                                '							<div class="filter-option-inner-inner">Compos Mentis</div>' + 
                                '						</div>' + 
                                '					</div>' + 
                                '				</button>' + 
                                '				<div class="dropdown-menu ">' + 
                                '					<div class="inner show" role="listbox" id="bs-select-2" tabindex="-1">' + 
                                '						<ul class="dropdown-menu inner show" role="presentation"></ul>' + 
                                '					</div>' + 
                                '				</div>' + 
                                '			</div>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="form-group col-md-12">' + 
                                '		<label for="editrtl"> Evaluasi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="edievaluasi" name="editevaluasi" class="form-control" rows="2">'+response.data.evaluasi+'</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '<button type="button" class="btn btn-primary d-flex ml-auto" onclick="edit(event)"> <i class="fas fa-sign-in"></i> Perbaharui </button>';
                $('#editPemeriksaan').find('.modal-body').html(html);
                $('#editPemeriksaan').modal('show');
            },
            error: function(error){
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data pemeriksaan',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        });
    }

    function edit(e){
        e.preventDefault();
        let tgl_perawatan = $("input[name=edittgl]").val();
        let jam_rawat = $("input[name=editjam]").val();

        var data = {
            kesadaran: $("select[name=editkesadaran]").val(),
            keluhan: $("textarea[name=editkeluhan]").val(),
            pemeriksaan: $("textarea[name=editpemeriksaan]").val(),
            penilaian: $("textarea[name=editpenilaian]").val(),
            suhu: $("input[name=editsuhu]").val(),
            berat: $("input[name=editberat]").val(),
            tinggi: $("input[name=edittinggi]").val(),
            tensi: $("input[name=edittensi]").val(),
            nadi: $("input[name=editnadi]").val(),
            respirasi: $("input[name=editrespirasi]").val(),
            instruksi: $("textarea[name=editinstruksi]").val(),
            alergi: $("textarea[name=editalergi]").val(),
            rtl: $("textarea[name=editrtl]").val(),
            gcs: $("input[name=editgcs]").val(),
            evaluasi: $("textarea[name=edievaluasi]").val(),
            spo2: $("input[name=editspo2]").val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
        };
        $.ajax({
            url: "{{url('/ranap/pemeriksaan/edit')}}"+"/"+"{{$encryptNoRawat}}"+"/"+tgl_perawatan+"/"+jam_rawat,
            method: "POST",
            data: data,
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
            success: function(response){
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
            error: function(error){
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal menyimpan perubahan',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        });
    }

    $('#copyPemeriksaanButton').click(function (e){
        e.preventDefault();
        $.ajax({
            url: "{{url('/api/pemeriksaan')}}"+"/"+"{{$encryptNoRawat}}",
            method: "GET",
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
            success: function(response){
                Swal.close();
                $("select[name=kesadaran]").val(response.data.kesadaran);
                $("textarea[name=keluhan]").val(response.data.keluhan);
                $("textarea[name=pemeriksaan]").val(response.data.pemeriksaan);
                $("textarea[name=penilaian]").val(response.data.penilaian);
                $("input[name=suhu]").val(response.data.suhu_tubuh);
                $("input[name=berat]").val(response.data.berat);
                $("input[name=tinggi]").val(response.data.tinggi);
                $("input[name=tensi]").val(response.data.tensi);
                $("input[name=nadi]").val(response.data.nadi);
                $("input[name=respirasi]").val(response.data.respirasi);
                $("textarea[name=instruksi]").val(response.data.instruksi);
                $("textarea[name=alergi]").val(response.data.alergi);
                $("textarea[name=rtl]").val(response.data.rtl);
                $("input[name=gcs]").val(response.data.gcs);
                $("input[name=spo2]").val(response.data.spo2);
                $("textarea[name=evaluasi]").val(response.data.evaluasi);
                
                Swal.fire({
                    text: 'Data berhasil disalin',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function(error){
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal menyalin data pemeriksaan',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        });
    });

    $("#pemeriksaanButton").click(function(event){
        event.preventDefault();
        var select = document.getElementById('kesadaran');
        var option = select.options[select.selectedIndex];
        let kesadaran = option.text;
        let keluhan = $("textarea[name=keluhan]").val();
        let pemeriksaan = $("textarea[name=pemeriksaan]").val();
        let penilaian = $("textarea[name=penilaian]").val();
        let suhu = $("input[name=suhu]").val();
        let berat = $("input[name=berat]").val();
        let tinggi = $("input[name=tinggi]").val();
        let tensi = $("input[name=tensi]").val();
        let nadi = $("input[name=nadi]").val();
        let respirasi = $("input[name=respirasi]").val();
        let instruksi = $("textarea[name=instruksi]").val();
        let alergi = $("textarea[name=alergi]").val();
        let rtl = $("textarea[name=rtl]").val();
        let gcs = $("input[name=gcs]").val();
        let spo2 = $("input[name=spo2]").val();
        let evaluasi = $("textarea[name=evaluasi]").val();
        let _token = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: "{{url('/ranap/pemeriksaan/submit')}}",
            type: "POST",
            data:{
                no_rawat: "{{$encryptNoRawat}}",
                keluhan: keluhan,
                pemeriksaan: pemeriksaan,
                penilaian: penilaian,
                suhu: suhu,
                berat: berat,
                tinggi: tinggi,
                tensi: tensi,
                nadi: nadi,
                respirasi: respirasi,
                instruksi: instruksi,
                kesadaran: kesadaran,
                alergi: alergi,
                rtl: rtl,
                gcs: gcs,
                spo2: spo2,
                evaluasi: evaluasi,
                _token: _token
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Menyimpan data...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response){
                Swal.close();
                
                // Bersihkan form setelah sukses
                resetForm();
                
                // Tampilkan pesan sukses
                Swal.fire({
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Perbarui tabel riwayat dengan data terbaru setelah pesan ditutup
                    refreshRiwayatTable();
                });
            },
            error: function(xhr, status, error) {
                Swal.close();
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse && errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Tangani error parsing
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        });
    });

    // Fungsi untuk memperbarui tabel riwayat
    function refreshRiwayatTable() {
        // Dapatkan status checkbox "Hanya Tampilkan Data Hari Ini"
        var showTodayOnly = $('#toggleToday').is(':checked');
        
        // Tambahkan indikator loading yang jelas
        $('#tableRiwayatPemeriksaanRanap_wrapper').prepend(
            '<div id="tableLoading" style="position: absolute; width: 100%; text-align: center; padding: 10px; background: rgba(255,255,255,0.8); z-index: 10;">' +
            '<i class="fas fa-spinner fa-spin"></i> Sedang memuat data terbaru...</div>'
        );
        
        // Tambahkan delay untuk memastikan database sudah diperbarui
        setTimeout(function() {
            $.ajax({
                url: "{{url('/api/riwayat-pemeriksaan')}}" + "/" + "{{$encryptNoRawat}}",
                type: "GET",
                dataType: 'json',
                data: {
                    today: showTodayOnly // Kirim parameter filter ke API
                },
                cache: false, // Tambahkan ini untuk memastikan data tidak di-cache
                success: function(response) {
                    // Hapus indikator loading
                    $('#tableLoading').remove();
                    
                    // Cek ketersediaan tabel
                    if ($.fn.dataTable.isDataTable('#tableRiwayatPemeriksaanRanap')) {
                        riwayatTable = $('#tableRiwayatPemeriksaanRanap').DataTable();
                        
                        // Kosongkan tabel terlebih dahulu
                        riwayatTable.clear();
                        
                        // Tambahkan data baru ke tabel
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                var actionButton = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" onclick="showModalEdit(\'' + 
                                    item.no_rawat + '\' ,\'' + item.tgl_perawatan + '\', \'' + item.jam_rawat + '\')" title="Edit">' +
                                    '<i class="fa fa-lg fa-fw fa-pen"></i></button>';
                                
                                riwayatTable.row.add([
                                    item.tgl_perawatan,
                                    item.jam_rawat,
                                    item.keluhan,
                                    item.suhu_tubuh,
                                    item.tensi,
                                    item.nadi,
                                    actionButton
                                ]);
                            });
                            
                            // Redraw tabel untuk menampilkan data baru
                            riwayatTable.draw();
                        }
                    } else {
                        // Jika tabel belum diinisialisasi, inisialisasi ulang dan coba lagi
                        console.log('Tabel belum diinisialisasi, mencoba ulang...');
                        initDataTable();
                    }
                },
                error: function(xhr, status, error) {
                    // Hapus indikator loading
                    $('#tableLoading').remove();
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data riwayat: ' + error,
                        icon: 'error',
                        confirmButtonText: 'Coba Lagi'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika user klik "Coba Lagi", refresh tabel lagi
                            refreshRiwayatTable();
                        }
                    });
                }
            });
        }, 1000); // Tambahkan delay 1000ms (1 detik) untuk memberi waktu database update
    }
    
    // Fungsi baru untuk inisialisasi DataTable
    function initDataTable() {
        if (!$.fn.dataTable.isDataTable('#tableRiwayatPemeriksaanRanap')) {
            riwayatTable = $('#tableRiwayatPemeriksaanRanap').DataTable({
                responsive: true,
                order: [[0, 'desc'], [1, 'desc']],
                language: {
                    emptyTable: 'Tidak ada data riwayat',
                    zeroRecords: 'Tidak ditemukan data yang sesuai',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
                    infoFiltered: '(difilter dari _MAX_ total entri)',
                    search: 'Cari:',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        }
        
        // Refresh tabel setelah inisialisasi
        setTimeout(function() {
            refreshRiwayatTable();
        }, 500);
    }
    
    // Fungsi untuk reset form setelah simpan
    function resetForm() {
        // Bersihkan nilai input
        $("textarea[name=keluhan]").val("");
        $("input[name=suhu]").val("");
        $("input[name=berat]").val("");
        $("input[name=tinggi]").val("");
        $("input[name=tensi]").val("");
        $("input[name=nadi]").val("");
        $("input[name=respirasi]").val("");
        $("input[name=gcs]").val("");
        $("input[name=spo2]").val("");
    }

    function showDetailPemeriksaan(noRawat, tgl, jam) {
        $.ajax({
            url: "{{url('/ranap/pemeriksaan')}}"+"/"+"{{$encryptNoRawat}}"+"/"+tgl+"/"+jam,
            type: "GET",
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
            success: function(response){
                Swal.close();
                var html = '' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailkeluhan"> Subjek </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailkeluhan" class="form-control" rows="4" readonly>' + response.data.keluhan + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailpemeriksaan"> Objek </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailpemeriksaan" class="form-control" rows="4" readonly>' + response.data.pemeriksaan + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailpenilaian"> Asesmen </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailpenilaian" class="form-control" rows="2" readonly>' + response.data.penilaian + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailinstruksi"> Instruksi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailinstruksi" class="form-control" rows="2" readonly>' + response.data.instruksi + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailrtl"> Plan </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailrtl" class="form-control" rows="2" readonly>' + response.data.rtl + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-6">' + 
                                '		<label for="detailalergi"> Alergi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailalergi" class="form-control" rows="2" readonly>' + response.data.alergi + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="detailsuhu"> Suhu Badan (C) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailsuhu" class="form-control" value="' + response.data.suhu_tubuh + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="detailberat"> Berat (Kg) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailberat" class="form-control" value="' + response.data.berat + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="detailtinggi"> Tinggi Badan (Cm) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailtinggi" class="form-control" value="' + response.data.tinggi + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="detailgcs"> GCS (E, V, M) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailgcs" class="form-control" value="' + response.data.gcs + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="row">' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="detailspo2"> SP02 </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailspo2" class="form-control" value="' + response.data.spo2 + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="detailtensi"> Tensi </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailtensi" class="form-control" value="' + response.data.tensi + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-2">' + 
                                '		<label for="detailnadi"> Nadi (per Menit) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailnadi" class="form-control" value="' + response.data.nadi + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '	<div class="form-group col-md-3">' + 
                                '		<label for="detailrespirasi"> Respirasi (per Menit) </label>' + 
                                '		<div class="input-group">' + 
                                '			<input id="detailrespirasi" class="form-control" value="' + response.data.respirasi + '" readonly>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="form-group col-md-3">' + 
                                '		<label for="detailkesadaran"> Kesadaran </label>' + 
                                '		<div class="input-group">' + 
                                '			<div class="dropdown bootstrap-select form-control">' + 
                                '				<select id="detailkesadaran" class="form-control" readonly>' + 
                                '					<option>' + response.data.kesadaran + '</option>' + 
                                '				</select>' + 
                                '			</div>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '</div>' + 
                                '<div class="form-group col-md-12">' + 
                                '		<label for="detailevaluasi"> Evaluasi </label>' + 
                                '		<div class="input-group">' + 
                                '			<textarea id="detailevaluasi" class="form-control" rows="2" readonly>' + response.data.evaluasi + '</textarea>' + 
                                '		</div>' + 
                                '	</div>' + 
                                '<button type="button" class="btn btn-primary d-flex ml-auto" onclick="closeDetail()"> <i class="fas fa-sign-in"></i> Tutup </button>';
                $('#detailPemeriksaanContent').html(html);
                $('#detailPemeriksaan').modal('show');
            },
            error: function(error){
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat detail pemeriksaan',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        });
    }

    function closeDetail() {
        $('#detailPemeriksaan').modal('hide');
    }

    // Fungsi untuk menerapkan template yang dipilih
    function applyTemplate(templateType) {
        // Template untuk pemeriksaan normal
        if (templateType === 'normal') {
            $("textarea[name=keluhan]").val("Pasien melakukan kontrol rutin.");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Kondisi pasien stabil");
            $("textarea[name=instruksi]").val("Istirahat Cukup, PHBS");
            $("textarea[name=rtl]").val("Edukasi Kesehatan");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Keadaan Umum Tiap 6 Jam");
            $("input[name=suhu]").val("36.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("80");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk demam
        else if (templateType === 'demam') {
            $("textarea[name=keluhan]").val("Pasien mengeluh demam sejak 2 hari yang lalu. Demam naik turun, kadang menggigil. Nyeri kepala (+), mual (-), muntah (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Demam");
            $("textarea[name=instruksi]").val("Istirahat cukup, kompres, minum banyak air putih");
            $("textarea[name=rtl]").val("Observasi suhu tiap 4 jam\nPemberian antipiretik");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Suhu Tiap 4 Jam");
            $("input[name=suhu]").val("38.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("110/70");
            $("input[name=nadi]").val("90");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("97");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk sakit kepala
        else if (templateType === 'sakit-kepala') {
            $("textarea[name=keluhan]").val("Pasien mengeluh sakit kepala berdenyut sejak 1 hari yang lalu. Nyeri skala 6/10. Mual (+), muntah (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Cephalgia");
            $("textarea[name=instruksi]").val("Istirahat yang cukup dalam ruangan yang tenang, hindari cahaya terang");
            $("textarea[name=rtl]").val("Pemberian analgetik dan anti mual\nObservasi nyeri skala");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Skala Nyeri Tiap 4 Jam");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("130/85");
            $("input[name=nadi]").val("85");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk sesak nafas
        else if (templateType === 'sesak') {
            $("textarea[name=keluhan]").val("Pasien mengeluh sesak napas sejak 4 jam yang lalu. Sesak memberat saat aktivitas. Batuk (+), dahak (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Sesak, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST +/+, Rhonki (+/+), Wheezing (+/+)\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Sesak Napas");
            $("textarea[name=instruksi]").val("Elevasi kepala 30 derajat, oksigenasi, posisi semi fowler");
            $("textarea[name=rtl]").val("Oksigenasi\nPemberian bronkodilator\nObservasi respirasi");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Status Respirasi Tiap 2 Jam");
            $("input[name=suhu]").val("37.2");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("140/90");
            $("input[name=nadi]").val("100");
            $("input[name=respirasi]").val("28");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("92");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk nyeri perut
        else if (templateType === 'nyeri-perut') {
            $("textarea[name=keluhan]").val("Pasien mengeluh nyeri perut sejak 1 hari yang lalu. Nyeri terutama di ulu hati/epigastrium. Mual (+), muntah (-), nafsu makan menurun. BAB normal.");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Nyeri tekan epigastrium (+), defans muskular (-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Dispepsia");
            $("textarea[name=instruksi]").val("Hindari makanan pedas, asam, dan bergas. Makan porsi kecil tapi sering.");
            $("textarea[name=rtl]").val("Pemberian antasida dan proton pump inhibitor\nObservasi nyeri perut");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Nyeri Perut Tiap 4 Jam");
            $("input[name=suhu]").val("36.7");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("88");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("99");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk diare
        else if (templateType === 'diare') {
            $("textarea[name=keluhan]").val("Pasien mengeluh diare cair sejak 2 hari yang lalu. BAB 5-6x/hari, konsistensi cair, tidak berdarah. Mual (+), muntah (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Bising usus meningkat, nyeri tekan (-), defans muskular (-).\nEXT : Turgor menurun, akral hangat.");
            $("textarea[name=penilaian]").val("Gastroenteritis Akut");
            $("textarea[name=instruksi]").val("Rehidrasi oral, minum air putih minimal 2L/hari, hindari makanan yang merangsang.");
            $("textarea[name=rtl]").val("Pemberian oralit dan probiotik\nMonitoring intake-output\nMonitoring frekuensi BAB");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Frekuensi BAB Tiap 4 Jam");
            $("input[name=suhu]").val("36.9");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("110/70");
            $("input[name=nadi]").val("95");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("97");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk hipertensi
        else if (templateType === 'hipertensi') {
            $("textarea[name=keluhan]").val("Pasien mengeluh pusing berputar dan tengkuk terasa berat. Nyeri kepala (+), mual (-), muntah (-), riwayat hipertensi (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Hipertensi Grade II");
            $("textarea[name=instruksi]").val("Diet rendah garam, hindari stress, istirahat cukup, pantau tekanan darah secara rutin.");
            $("textarea[name=rtl]").val("Pemberian antihipertensi\nMonitoring tekanan darah tiap 4 jam\nKontrol tekanan darah secara rutin");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Tekanan Darah Tiap 4 Jam");
            $("input[name=suhu]").val("36.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("170/100");
            $("input[name=nadi]").val("95");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk diabetes mellitus
        else if (templateType === 'diabetes') {
            $("textarea[name=keluhan]").val("Pasien mengeluh badan lemas, sering haus, sering BAK. Nafsu makan (+), penurunan berat badan (+), riwayat DM (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Akral hangat, CRT < 2 detik.");
            $("textarea[name=penilaian]").val("Diabetes Mellitus Tipe 2");
            $("textarea[name=instruksi]").val("Diet rendah gula, olahraga teratur, pantau kadar gula darah secara rutin.");
            $("textarea[name=rtl]").val("Pemeriksaan gula darah\nPemberian OHO/Insulin\nEdukasi diet dan aktivitas fisik");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Kadar Gula Darah Tiap 6 Jam");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("130/80");
            $("input[name=nadi]").val("88");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk batuk
        else if (templateType === 'batuk') {
            $("textarea[name=keluhan]").val("Pasien mengeluh batuk sejak 5 hari yang lalu. Batuk berdahak, warna dahak putih. Demam (+) ringan, mual (-), sesak (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("ISPA");
            $("textarea[name=instruksi]").val("Istirahat cukup, minum air hangat, hindari udara dingin.");
            $("textarea[name=rtl]").val("Pemberian ekspektoran dan mukolitik\nMonitoring frekuensi batuk\nPemberian antibiotik bila perlu");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Batuk Tiap 4 Jam");
            $("input[name=suhu]").val("37.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("85");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("97");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk gatal-gatal/alergi
        else if (templateType === 'gatal') {
            $("textarea[name=keluhan]").val("Pasien mengeluh gatal-gatal di seluruh tubuh sejak 2 hari yang lalu. Kemerahan pada kulit (+), bentol-bentol (+). Riwayat alergi makanan/obat (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Ruam eritematosa di ekstremitas dan punggung, urtikaria (+).");
            $("textarea[name=penilaian]").val("Dermatitis Alergi");
            $("textarea[name=instruksi]").val("Hindari garukan pada kulit, gunakan pakaian longgar dan berbahan katun, hindari pemicu alergi.");
            $("textarea[name=rtl]").val("Pemberian antihistamin\nKompres dingin area gatal\nPemberian salep kortikosteroid topikal bila perlu");
            $("textarea[name=alergi]").val("Dalam investigasi");
            $("textarea[name=evaluasi]").val("Evaluasi Luas Lesi Tiap 8 Jam");
            $("input[name=suhu]").val("36.7");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("82");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk penyakit jantung
        else if (templateType === 'jantung') {
            $("textarea[name=keluhan]").val("Pasien mengeluh nyeri dada seperti tertekan benda berat sejak 6 jam yang lalu. Nyeri menjalar ke lengan kiri, sesak napas (+), keringat dingin (+), mual (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Cemas, Composmentis\nThorax : Cor S1-2 ireguler, bising (+) grade 2/6 di apex\nPulmo : SDV +/+ ST -/-, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Akral dingin, CRT > 2 detik.");
            $("textarea[name=penilaian]").val("Angina Pektoris");
            $("textarea[name=instruksi]").val("Istirahat total, posisi semi fowler, hindari aktivitas berat, diet rendah garam dan lemak.");
            $("textarea[name=rtl]").val("EKG 12 lead\nPemeriksaan enzim jantung\nKonsultasi spesialis jantung\nPemberian anti angina");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("Evaluasi Nyeri Dada dan Tanda Vital Tiap 1 Jam");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("150/95");
            $("input[name=nadi]").val("110");
            $("input[name=respirasi]").val("26");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("94");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk visite dokter
        else if (templateType === 'visite') {
            $("textarea[name=keluhan]").val("Pasien menjalani perawatan hari ke-... untuk diagnosis... \n\nKeluhan saat ini: \n- Status keluhan utama: membaik/tetap/memburuk \n- Keluhan tambahan: ... \n- Riwayat pengobatan: ...");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\n\nVital Sign:\n- Tekanan darah: stabil/tidak stabil\n- Suhu: normal/febris\n- Respirasi: normal/tidak normal\n\nThorax : \n- Cor: S1-2 intensitas normal, reguler, bising (-)\n- Pulmo: SDV +/+ ST -/-\n\nAbdomen : Supel, NT(-), peristaltik (+) normal\n\nEXT : Akral hangat, CRT < 2 detik\n\nStatus Lokalis: \n- Luka: ...\n- Drain: ...\n- Kateter: ...");
            $("textarea[name=penilaian]").val("1. Diagnosis utama: ...\n2. Diagnosis sekunder: ...\n\nProgres: membaik/stabil/memburuk");
            $("textarea[name=instruksi]").val("1. Tirah baring: total/parsial/mobilisasi\n2. Diet: sesuai penyakit/normal\n3. Intake cairan: cukup (minimal 2L/hari)\n4. Kontrol nyeri: sesuai kebutuhan\n5. Perawatan luka: ...");
            $("textarea[name=rtl]").val("1. Pemeriksaan Laboratorium: ...\n2. Pemeriksaan Radiologi: ...\n3. Terapi:\n   - Obat 1: ...\n   - Obat 2: ...\n   - Obat 3: ...\n4. Rencana observasi: ...\n5. Rencana tindakan: ...\n6. Kemungkinan pulang: dalam ... hari");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("1. Evaluasi tanda vital tiap 6 jam\n2. Evaluasi keluhan tiap shift\n3. Evaluasi respons terhadap terapi\n4. Evaluasi kondisi luka bila ada");
            $("input[name=suhu]").val("36.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("80");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk visite dokter hari pertama
        else if (templateType === 'visite-hari1') {
            $("textarea[name=keluhan]").val("Pasien menjalani perawatan HARI PERTAMA untuk diagnosis... \n\nKeluhan utama saat masuk: \n- Onset: ... \n- Lokasi: ... \n- Kualitas: ... \n- Faktor yang memperberat/memperingan: ... \n\nRiwayat penyakit:\n- Riwayat penyakit serupa: ...\n- Riwayat penyakit dahulu: ...\n- Riwayat pengobatan sebelumnya: ...\n- Riwayat alergi: ...");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Sakit Sedang/Berat, Kesadaran Compos Mentis\n\nVital Sign:\n- Tekanan darah: .../... mmHg\n- Suhu: ... °C\n- Nadi: ... x/menit, reguler/ireguler\n- Respirasi: ... x/menit\n- SpO2: ...%\n\nThorax : \n- Cor: S1-2 normal/abnormal, bising: ada/tidak ada\n- Pulmo: Suara nafas vesikular +/+, ronkhi +/-, wheezing -/-\n\nAbdomen : Tampak datar/distensi, nyeri tekan ada/tidak ada, bising usus normal/tidak\n\nEXT : Akral hangat/dingin, CRT < 2 detik/> 2 detik, edema +/-\n\nStatus Lokalis:\n- Kelainan yang ditemukan: ...\n- Tanda-tanda patologis: ...");
            $("textarea[name=penilaian]").val("1. Diagnosis utama: ... (ICD-10: ...)\n2. Diagnosis banding: ...\n3. Diagnosis sekunder: ...\n\nDerajat penyakit: ringan/sedang/berat\nKomplikasi: ada/tidak ada");
            $("textarea[name=instruksi]").val("1. Tirah baring: total/parsial\n2. Diet: lunak/biasa/khusus ... \n3. Intake cairan: 2-2,5L/hari\n4. Terapi non-farmakologis: ...\n5. Pemantauan tanda vital tiap 4 jam\n6. Monitor ketat tanda-tanda perburukan");
            $("textarea[name=rtl]").val("1. Pemeriksaan Laboratorium:\n   - Darah lengkap\n   - Fungsi ginjal\n   - Elektrolit\n   - Lainnya: ...\n\n2. Pemeriksaan Radiologi:\n   - Rontgen ...\n   - USG ...\n   - Lainnya: ...\n\n3. Terapi:\n   - Cairan intravena: ...\n   - Antibiotik: ...\n   - Simptomatik: ...\n   - Lainnya: ...\n\n4. Konsultasi ke spesialis bila perlu\n\n5. Evaluasi respons terapi dalam 24 jam");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("textarea[name=evaluasi]").val("1. Evaluasi tanda vital tiap 4 jam\n2. Evaluasi respons terhadap terapi awal\n3. Pemantauan tanda kegawatan\n4. Evaluasi hasil pemeriksaan penunjang");
            $("input[name=suhu]").val("37.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("130/80");
            $("input[name=nadi]").val("88");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("96");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk visite dokter hari kedua
        else if (templateType === 'visite-hari2') {
            $("textarea[name=keluhan]").val("Pasien menjalani perawatan HARI KEDUA untuk diagnosis... \n\nPerkembangan keluhan: \n- Keluhan utama: membaik/tetap/memburuk \n- Gejala yang membaik: ... \n- Gejala yang menetap: ... \n- Gejala baru: ... \n\nRiwayat pengobatan hari pertama:\n- Terapi yang diberikan: ...\n- Respons terhadap terapi: ...");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Sakit Sedang/Ringan, Kesadaran Compos Mentis\n\nVital Sign:\n- Tekanan darah: .../... mmHg (↑/↓/tetap dibanding hari sebelumnya)\n- Suhu: ... °C (↑/↓/tetap dibanding hari sebelumnya)\n- Nadi: ... x/menit (↑/↓/tetap dibanding hari sebelumnya)\n- Respirasi: ... x/menit (↑/↓/tetap dibanding hari sebelumnya)\n- SpO2: ...% (↑/↓/tetap dibanding hari sebelumnya)\n\nThorax : \n- Cor: Perubahan dibanding hari sebelumnya: ...\n- Pulmo: Perubahan dibanding hari sebelumnya: ...\n\nAbdomen : Perubahan dibanding hari sebelumnya: ...\n\nEXT : Perubahan dibanding hari sebelumnya: ...\n\nStatus Lokalis:\n- Perubahan dibanding hari sebelumnya: ...\n- Perkembangan tanda patologis: ...");
            $("textarea[name=penilaian]").val("1. Diagnosis kerja: ... (ICD-10: ...)\n2. Diagnosis sekunder: ...\n\nProgres penyakit: membaik/stabil/memburuk\nKomplikasi: ada/tidak ada\n\nHasil pemeriksaan penunjang:\n- Lab: ...\n- Radiologi: ...");
            $("textarea[name=instruksi]").val("1. Tirah baring: parsial/mobilisasi terbatas\n2. Diet: lunak/biasa/khusus ... \n3. Intake cairan: tetap 2-2,5L/hari\n4. Aktivitas: mulai mobilisasi bertahap sesuai toleransi\n5. Terapi non-farmakologis: dilanjutkan/dimodifikasi");
            $("textarea[name=rtl]").val("1. Evaluasi pemeriksaan laboratorium:\n   - Pemeriksaan ulang: ...\n   - Pemeriksaan tambahan: ...\n\n2. Evaluasi pemeriksaan radiologi:\n   - Follow up jika diperlukan: ...\n\n3. Terapi:\n   - Terapi dilanjutkan: ...\n   - Terapi dimodifikasi: ...\n   - Terapi dihentikan: ...\n   - Terapi baru: ...\n\n4. Monitoring respons terapi\n\n5. Edukasi pasien dan keluarga\n\n6. Evaluasi kemungkinan pemulangan dalam 1-2 hari jika kondisi membaik");
            $("textarea[name=alergi]").val("Tidak Ada / Alergi terhadap: ...");
            $("textarea[name=evaluasi]").val("1. Evaluasi tanda vital tiap 6 jam\n2. Evaluasi respons terhadap terapi yang sudah dimodifikasi\n3. Evaluasi perkembangan kondisi pasien\n4. Persiapan pemulangan pasien jika kondisi membaik");
            $("input[name=suhu]").val("37.0");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("82");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("97");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        // Template untuk visite dokter hari ketiga
        else if (templateType === 'visite-hari3') {
            $("textarea[name=keluhan]").val("Pasien menjalani perawatan HARI KETIGA untuk diagnosis... \n\nPerkembangan kondisi: \n- Status keluhan utama: signifikan membaik/sedikit membaik/tetap/memburuk \n- Status keluhan lain: ... \n- Gejala baru: tidak ada/ada: ... \n\nRespons terhadap terapi:\n- Obat yang efektif: ...\n- Obat yang kurang efektif: ...\n- Efek samping obat: tidak ada/ada: ...");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Membaik, Tampak Sakit Ringan/Sedang, Kesadaran Compos Mentis\n\nVital Sign (Perkembangan selama 3 hari):\n- Tekanan darah: .../... mmHg (tren stabil/membaik)\n- Suhu: ... °C (tren stabil/membaik)\n- Nadi: ... x/menit (tren stabil/membaik)\n- Respirasi: ... x/menit (tren stabil/membaik)\n- SpO2: ...% (tren stabil/membaik)\n\nThorax : \n- Cor: Perkembangan: membaik/stabil/memburuk\n- Pulmo: Perkembangan: membaik/stabil/memburuk\n\nAbdomen : Perkembangan: membaik/stabil/memburuk\n\nEXT : Perkembangan: membaik/stabil/memburuk\n\nStatus Lokalis:\n- Perkembangan kondisi spesifik penyakit: membaik/stabil/memburuk\n- Status luka (jika ada): ...\n- Status pembengkakan (jika ada): ...");
            $("textarea[name=penilaian]").val("1. Diagnosis akhir: ... (ICD-10: ...)\n2. Diagnosis sekunder: ...\n\nKesimpulan hasil pengobatan selama 3 hari: signifikan membaik/membaik/stabil/memburuk\n\nHasil pemeriksaan penunjang terbaru:\n- Lab: ...\n- Radiologi: ...\n\nProgres penyakit sesuai dengan perkiraan/lebih cepat/lebih lambat dari perkiraan");
            $("textarea[name=instruksi]").val("1. Aktivitas: dapat mobilisasi normal/terbatas\n2. Diet: normal/khusus: ...\n3. Edukasi tanda kekambuhan dan kapan harus kembali ke RS\n4. Pentingnya kontrol rutin setelah pemulangan\n5. Tata cara penggunaan obat-obatan di rumah");
            $("textarea[name=rtl]").val("1. Rencana pemulangan:\n   - Pasien dapat dipulangkan hari ini/besok\n   - Pasien belum dapat dipulangkan karena: ...\n\n2. Terapi lanjutan:\n   - Obat yang dilanjutkan: ...\n   - Obat yang dihentikan: ...\n   - Obat baru: ...\n\n3. Edukasi:\n   - Diberikan edukasi tentang penyakit\n   - Diberikan edukasi tentang pengobatan\n   - Diberikan edukasi tentang pencegahan kekambuhan\n\n4. Rencana kontrol:\n   - Kontrol ke Poli ... tanggal ...\n   - Pemeriksaan lanjutan yang diperlukan: ...\n\n5. Jika kondisi belum membaik, rencana perawatan hari keempat:");
            $("textarea[name=alergi]").val("Tidak Ada / Alergi terhadap: ...");
            $("textarea[name=evaluasi]").val("1. Evaluasi keseluruhan kondisi pasien setelah 3 hari perawatan\n2. Evaluasi kepatuhan terhadap terapi\n3. Evaluasi kesiapan pasien untuk pemulangan\n4. Evaluasi pemahaman pasien dan keluarga tentang penyakit dan tata cara pengobatan di rumah");
            $("input[name=suhu]").val("36.7");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("80");
            $("input[name=respirasi]").val("18");
            $("input[name=gcs]").val("456");
            $("input[name=spo2]").val("98");
            $("select[name=kesadaran]").val("Compos Mentis").change();
        }
        
        Swal.fire({
            text: 'Template berhasil diterapkan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Fungsi untuk mencetak detail pemeriksaan
    function printDetail() {
        var printContents = document.getElementById('detailPemeriksaanContent').innerHTML;
        var originalContents = document.body.innerHTML;
        
        // Buat template cetak yang lebih rapi
        var printTemplate = `
            <html>
            <head>
                <title>Detail Pemeriksaan Pasien</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
                    .content { margin: 20px; }
                    .row { display: flex; margin-bottom: 15px; }
                    .col-6 { width: 50%; padding: 0 10px; }
                    .col-3 { width: 25%; padding: 0 10px; }
                    label { font-weight: bold; display: block; margin-bottom: 5px; }
                    input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; }
                    h2 { color: #2980b9; }
                    .footer { margin-top: 30px; text-align: center; font-size: 0.9em; color: #777; }
                    @media print {
                        button { display: none !important; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>DETAIL PEMERIKSAAN PASIEN</h2>
                    <p>Tanggal: ${new Date().toLocaleDateString('id-ID')} | Jam: ${new Date().toLocaleTimeString('id-ID')}</p>
                </div>
                <div class="content">
                    ${printContents}
                </div>
                <div class="footer">
                    <p>Dokumen dicetak dari Sistem Informasi Rumah Sakit</p>
                </div>
            </body>
            </html>
        `;
        
        document.body.innerHTML = printTemplate;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endpush