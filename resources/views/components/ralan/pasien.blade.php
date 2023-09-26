<div>
    <x-adminlte-profile-widget name="{{$data->nm_pasien ?? '-'}}" desc="{{$data->no_rkm_medis ?? '-'}}"
        theme="lightblue" layout-type="classic"
        img="https://simrs.rsbhayangkaranganjuk.com/webapps/photopasien/{{$data->gambar ?? 'avatar.png'}}">
        <x-adminlte-profile-row-item icon="fas fa-fw fa-book-medical" title="No Rawat"
            text="{{$data->no_rawat ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-calendar" title="Tgl Lahir"
            text="{{$data->tgl_lahir  ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-clock" title="Umur" text="{{$data->umur ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-wallet" title="Cara Bayar" text="{{$data->png_jawab ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-phone" title="No Telp" text="{{$data->no_tlp ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-building" title="Pekerjaan"
            text="{{$data->pekerjaan ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No Peserta"
            text="{{$data->no_peserta ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat" text="{{$data->alamat ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan" text="{{$data->catatan ?? '-'}}" />
        <div class="p-0 col-12">
            <span class="nav-link">
                <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal"
                    data-target="#modalRiwayatPemeriksaanRalan" class="bg-info justify-content-end" />
            </span>
            <span class="nav-link">
                <div class="d-flex flex-row justify-content-between" style="gap:10px">
                    <x-adminlte-button icon="fas fa-folder" label="Berkas RM Digital" onclick="getBerkasRM()"
                        theme="success" />
                    <x-adminlte-button icon="fas fa-folder" label="Berkas RM Retensi" theme="secondary"
                        onclick="getBerkasRetensi()" />
                </div>
            </span>
        </div>
        <span class="nav-link">
            <x-adminlte-input-file id="fileupload" name="fileupload" igroup-size="sm" accept="image/*,application/pdf"
                placeholder="Berkas Digital" legend="Pilih">
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

<x-adminlte-modal id="modalBerkasRM" class="modal-lg" title="Berkas RM" size="lg" theme="info" icon="fas fa-bell" v-centered
    static-backdrop scrollable>
    <div class="body-modal-berkasrm" style="gap:20px">
        {{-- <div class="row row-cols-auto body-modal-berkasrm" style="gap:20px">
            <div class="body-modal-berkasrm">
            </div>
        </div> --}}
    </div>
</x-adminlte-modal>

<x-adminlte-modal id="modalBerkasRetensi" title="Berkas Retensi" size="lg" theme="info" icon="fas fa-bell" v-centered
    static-backdrop scrollable>
    <div class="container-retensi" style="color:#0d2741">
    </div>
</x-adminlte-modal>

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
    function uploadFile() {
            var file_data = $('#fileupload').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('no_rawat', '{{$data->no_rawat}}');
            form_data.append('url', '{{url()->current()}}');
            $.ajax({
                url: "https://simrs.rsbhayangkaranganjuk.com/webapps/edokterfile.php",
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
                url: "/berkas-retensi/{{$data->no_rkm_medis}}",
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
                        html += '<iframe src="http://simrs.rsbhayangkaranganjuk.com/webapps/medrec/'+decode+'" frameborder="0" height="700px" width="100%"></iframe>';
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
                url: "/berkas/{{$data->no_rawat}}/{{$data->no_rkm_medis}}",
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
                        var html = '';
                        data.data.forEach(function(item){
                            let decoded = decodeURIComponent(item.lokasi_file);
                            html += '<iframe src="https://simrs.rsbhayangkaranganjuk.com/webapps/berkasrawat/'+decoded+'" frameborder="0" height="700px" width="100%"></iframe>';
                            
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
</script>
<script>
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
                $('#modalBerkasRM').modal('hide');
            });
</script>
@endpush