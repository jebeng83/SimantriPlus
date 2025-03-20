@php
if (!function_exists('maskKtp')) {
function maskKtp($ktp) {
if (!$ktp || $ktp === '-') return '-';
$ktpLength = strlen($ktp);
if ($ktpLength <= 5) return $ktp; $firstFour=substr($ktp, 0, 4); $lastOne=substr($ktp, -1); $masked=str_repeat('x',
    $ktpLength - 5); return $firstFour . $masked . $lastOne; } } @endphp @if(!$data) <div class="alert alert-warning">
    <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
    Data pasien tidak ditemukan atau terjadi kesalahan saat memuat data pasien.
    </div>
    @else
    <div>
        <x-adminlte-profile-widget name="{{$data->nm_pasien ?? '-'}}" desc="{{$data->no_rkm_medis ?? '-'}}"
            theme="lightblue" layout-type="classic"
            img="https://simrs.rsbhayangkaranganjuk.com/webapps/photopasien/{{$data->gambar ?? 'avatar.png'}}">
            <x-adminlte-profile-row-item icon="fas fa-fw fa-book-medical" title="No Rawat"
                text="{{$data->no_rawat ?? '-'}}" />
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No KTP" text="{{$data->no_ktp ?? '-'}}"-->
            <!--    button="btn-ktp" />-->
            <div class="p-0 col-12">
                <span class="nav-link">
                    <i class="fas fa-fw fa-id-card"></i>No KTP <span class="float-right"><span
                            id="data-ktp">{{maskKtp($data->no_ktp ?? '-')}}</span>
                        <button id="btn-ktp" class="btn btn-sm btn-success">
                            <i class="fas fa-edit"></i>
                        </button>
                    </span>
                </span>
            </div>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-user" title="Jns Kelamin"
                text="{{($data->jk ?? '') == 'L' ? 'Laki - Laki' : 'Perempuan' }}" />
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-calendar" title="Tempat, Tgl Lahir"-->
            <!--    text="{{$data->tmp_lahir ?? '-'}}, {{\Carbon\Carbon::parse($data->tgl_lahir)->isoFormat('LL')  ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-school" title="Pendidikan" text="{{$data->pnd ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item title="Nama Ibu" icon="fas fa-fw fa-user" text="{{$data->nm_ibu  ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat" text="{{$data->alamat ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item title="Nama Keluarga" icon="fas fa-fw fa-user"-->
            <!--    text="{{$data->namakeluarga  ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-briefcase" title="Pekerjaan PJ"-->
            <!--    text="{{$data->pekerjaanpj ?? '-'}}" -->
            <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat PJ" text="{{$data->alamatpj ?? '-'}}" />
            <!--<x-adminlte-profile-row-item title="Gol Darah" icon="fas fa-fw fa-droplet"-->
            <!--    text="{{$data->gol_darah  ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item title="Stts Nikah" icon="fas fa-fw fa-ring" text="{{$data->stts_nikah  ?? '-'}}" -->
            <!--<x-adminlte-profile-row-item title="Agama" icon="fas fa-fw fa-book" text="{{$data->agama  ?? '-'}}" -->
            <x-adminlte-profile-row-item icon="fas fa-fw fa-clock" title="Umur" text="{{$data->umur ?? '-'}}" />
            <x-adminlte-profile-row-item icon="fas fa-fw fa-wallet" title="Cara Bayar"
                text="{{$data->png_jawab ?? '-'}}" />
            {{--
            <x-adminlte-profile-row-item icon="fas fa-fw fa-phone" title="No Telp" text="{{$data->no_tlp ?? '-'}}"
                button="btn-phone" /> --}}
            <div class="p-0 col-12">
                <span class="nav-link">
                    <i class="fas fa-fw fa-phone"></i>No Telp <span class="float-right"><span
                            id="data-phone">{{$data->no_tlp ?? '-'}}</span>
                        <button id="btn-phone" class="btn btn-sm btn-success">
                            <i class="fas fa-edit"></i>
                        </button>
                    </span>
                </span>
            </div>
            <!--<x-adminlte-profile-row-item icon="fas fa-fw fa-building" title="Pekerjaan"-->
            <!--    text="{{$data->pekerjaan ?? '-'}}" -->
            <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No Peserta"
                text="{{$data->no_peserta ?? '-'}}" />
            <div class="p-0 col-12">
                <span class="nav-link">
                    <i class="fas fa-fw fa-id-card"></i>No Kartu <span class="float-right"><span
                            id="data-card">{{$data->no_peserta ?? '-'}}</span>
                        <button id="btn-card" class="btn btn-sm btn-success">
                            <i class="fas fa-edit"></i>
                        </button>
                    </span>
                </span>
            </div>

            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan"
                text="{{$data->catatan ?? '-'}}" />
            <!-- Kolom data_posyandu tidak tersedia di database -->
            <!-- <x-adminlte-profile-row-item icon="fas fa-fw fa-school" title="Posyandu"
                text="{{$data->data_posyandu ?? '-'}}" /> -->
            <div class="p-0 col-12">
                <span class="nav-link">
                    <div class="d-flex flex-row justify-content-between" style="gap:10px">
                        <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal"
                            data-target="#modalRiwayatPemeriksaanRalan" class="bg-info" />
                    </div>
                </span>
                <span class="nav-link">
                    <x-ralan.icare-bpjs :noPeserta="$data->no_peserta ?? ''" :kodeDokter="$dokter ?? '102'" />
                </span>
                <span class="nav-link">
                    <div class="d-flex flex-row justify-content-between" style="gap:10px">
                        <x-adminlte-button icon="fas fa-folder" id="btn-rm" data-rm="{{$data->no_rkm_medis ?? ''}}"
                            label="Berkas RM Digital" theme="success" />
                        <x-adminlte-button icon="fas fa-folder" label="Berkas RM Retensi" theme="secondary"
                            onclick="getBerkasRetensi()" />
                    </div>
                </span>
            </div>
            <span class="nav-link">
                <x-adminlte-input-file id="fileupload" name="fileupload" igroup-size="sm"
                    accept="image/*,application/pdf" placeholder="Berkas Digital" legend="Pilih">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="primary" onclick="uploadFile()" label="Upload" />
                    </x-slot>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-primary">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>
            </span>
        </x-adminlte-profile-widget>
    </div>
    @endif

    <x-adminlte-modal id="modalBerkasRM" class="modal-lg" title="Berkas RM" size="lg" theme="info" icon="fas fa-bell"
        v-centered static-backdrop scrollable>
        <div class="body-modal-berkasrm" style="gap:20px">
            {{-- <div class="row row-cols-auto body-modal-berkasrm" style="gap:20px">
                <div class="body-modal-berkasrm">
                </div>
            </div> --}}
        </div>
    </x-adminlte-modal>

    <x-adminlte-modal id="modal-rm" class="modal-lg" title="Berkas RM" size="lg" theme="info" icon="fas fa-bell"
        v-centered scrollable>
        <livewire:component.berkas-rm />
    </x-adminlte-modal>

    <x-adminlte-modal id="modalBerkasRetensi" title="Berkas Retensi" size="lg" theme="info" icon="fas fa-bell"
        v-centered static-backdrop scrollable>
        <div class="container-retensi" style="color:#0d2741">
        </div>
    </x-adminlte-modal>

    <livewire:component.change-phone />
    <livewire:component.change-ktp />
    <livewire:component.change-card />

    @push('css')
    <style>
        @media (min-width: 992px) {
            .modal-lg {
                max-width: 100%;
            }
        }
    </style>
    @endpush

    @push('js')
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        // Fungsi untuk menyembunyikan sebagian nomor KTP
        function maskKtp(ktp) {
            if (!ktp || ktp === '-') return '-';
            var ktpLength = ktp.length;
            if (ktpLength <= 5) return ktp;
            
            var firstFour = ktp.substring(0, 4);
            var lastOne = ktp.substring(ktpLength - 1);
            var masked = 'x'.repeat(ktpLength - 5);
            
            return firstFour + masked + lastOne;
        }
        
        @if($data)
        $('#btn-phone').on('click', function(event){
            event.preventDefault();
            var phone = $('#data-phone').text();
            Livewire.emit('setRmPhone', "{{$data->no_rkm_medis ?? ''}}", phone.trim());
            $('#change-phone').modal('show');
        });

        Livewire.on('refreshPhone', function(event){
            $('#change-phone').modal('hide');
            console.log(event);
            $('#data-phone').text(event);
        });
        
        $('#btn-ktp').on('click', function(event){
            event.preventDefault();
            var ktp = $('#data-ktp').text();
            Livewire.emit('setRmKtp', "{{$data->no_rkm_medis ?? ''}}", ktp.trim());
            $('#change-ktp').modal('show');
        });

        Livewire.on('refreshKtp', function(event){
            $('#change-ktp').modal('hide');
            console.log(event);
            $('#data-ktp').text(event);
        });
        
        $('#btn-card').on('click', function(event){
            event.preventDefault();
            var card = $('#data-card').text();
            Livewire.emit('setRmCard', "{{$data->no_rkm_medis ?? ''}}", card.trim());
            $('#change-card').modal('show');
        });

        Livewire.on('refreshCard', function(event){
            $('#change-card').modal('hide');
            console.log(event);
            $('#data-card').text(event);
        });

        $('#btn-rm').on('click', function(event){
            event.preventDefault();
            var rm = $(this).data('rm');
            $('#modal-rm').modal('show');
            Livewire.emit('setRm', rm);
        });

        function uploadFile() {
            var file_data = $('#fileupload').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('no_rawat', "{{$data->no_rawat ?? ''}}");
            form_data.append('url', '{{url()->current()}}');
            
            $.ajax({
                url: "{{url()->current()}}",
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    Swal.fire({
                        title: data.status ? 'Sukses' : 'Gagal',
                        text: data.message ?? 'Berkas berhasil diupload',
                        icon: data.status ? 'success' : 'error',
                        confirmButtonText: 'OK'
                    })
                },
                error: function (data) {
                    Swal.fire({
                        title: 'Gagal',
                        text: data.message ?? 'Berkas berhasil diupload',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                }
            });
        }

        function getBerkasRetensi(){
            $.ajax({
                url: "/berkas-retensi/{{$data->no_rkm_medis ?? ''}}",
                type: "GET",
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
                success: function (data) {
                    Swal.close();
                    if(data.status == 'success'){
                        let decode = decodeURIComponent(data.data.lokasi_pdf);
                        var html = '';
                        html += '<iframe></iframe>';
                        $('.container-retensi').html(html);
                        $('#modalBerkasRetensi').modal('show');
                    }else{
                        Swal.fire({
                            title: 'Kosong',
                            text: data.message,
                            icon: 'info',
                            confirmButtonText: 'OK'
                        })
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

        function getBerkasRM() {
            $.ajax({
                url: "/berkas/{{$data->no_rawat ?? ''}}/{{$data->no_rkm_medis ?? ''}}",
                type: "GET",
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
                success: function (data) {
                    Swal.close();
                    console.log(data);  
                    if(data.status == 'success'){
                        var html = '';
                        data.data.forEach(function(item){
                            let decoded = decodeURIComponent(item.lokasi_file);
                            html += '<iframe></iframe>';
                            
                        });
                        $('.body-modal-berkasrm').html(html);
                        $('#modalBerkasRM').modal('show');
                    }else{
                        Swal.fire({
                            title: 'Kosong',
                            text: data.message,
                            icon: 'info',
                            confirmButtonText: 'OK'
                        })
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
        @endif
    </script>
    {{-- <script>

    </script> --}}
    @endpush