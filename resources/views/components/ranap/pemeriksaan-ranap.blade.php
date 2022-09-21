<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <x-adminlte-card theme="primary" title="Input Pemeriksaan" collapsible="collapsed">
            <form id="pemeriksaanForm">
                <div class="row">
                    <x-adminlte-textarea name="keluhan" label="Subjek" fgroup-class="col-md-6" rows="4">
                        {{$pemeriksaan->keluhan ?? ''}}
                    </x-adminlte-textarea>
                    <x-adminlte-textarea name="pemeriksaan" label="Objek" fgroup-class="col-md-6" rows="4">
                        {{$pemeriksaan->pemeriksaan ?? ''}}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-textarea name="penilaian" label="Asesmen" fgroup-class="col-md-6" rows="2">
                        {{$pemeriksaan->penilaian ?? ''}}
                    </x-adminlte-textarea>
                    <x-adminlte-textarea name="instruksi" label="Instruksi" fgroup-class="col-md-6" rows="2">
                        {{$pemeriksaan->instruksi ?? ''}}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-textarea name="rtl" label="Plan" fgroup-class="col-md-6" rows="2">
                        {{$pemeriksaan->rtl ?? ''}}
                    </x-adminlte-textarea>
                    <x-adminlte-textarea name="alergi" label="Alergi" fgroup-class="col-md-6" rows="2">
                        {{$alergi ?? ''}}
                    </x-adminlte-textarea>
                </div>
                <div class="row">
                    <x-adminlte-input name="suhu" label="Suhu Badan (C)" value="{{$pemeriksaan->suhu_tubuh ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="berat" label="Berat (Kg)" value="{{$pemeriksaan->berat ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="tinggi" label="Tinggi Badan (Cm)" value="{{$pemeriksaan->tinggi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="gcs" label="GCS (E, V, M)" value="{{$pemeriksaan->gcs ?? ''}}" fgroup-class="col-md-3" />
                </div>
                <div class="row">
                    <x-adminlte-input name="tensi" label="Tensi" value="{{$pemeriksaan->tensi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="nadi" label="Nadi (per Menit)" value="{{$pemeriksaan->nadi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="respirasi" label="Respirasi (per Menit)" value="{{$pemeriksaan->respirasi ?? ''}}" fgroup-class="col-md-3" />  
                    <x-adminlte-select-bs name="kesadaran" label="Kesadaran" fgroup-class="col-md-3">
                        @if(!empty($pemeriksaan->kesadaran))
                            <option @php if($pemeriksaan->kesadaran == 'Compos Mentis') echo 'selected'; @endphp >Compos Mentis</option>
                            <option @php if($pemeriksaan->kesadaran == 'Somnolence') echo 'selected'; @endphp >Somnolence</option>
                            <option @php if($pemeriksaan->kesadaran == 'Sopor') echo 'selected'; @endphp >Sopor</option>
                            <option @php if($pemeriksaan->kesadaran == 'Coma') echo 'selected'; @endphp >Coma</option>
                        @else
                            <option>Compos Mentis</option>
                            <option>Somnolence</option>
                            <option>Sopor</option>
                            <option>Coma</option>
                        @endif
                    </x-adminlte-select-bs>            
                </div>
                <x-adminlte-button class="d-flex ml-auto" id="pemeriksaanButton" theme="primary" label="Simpan" icon="fas fa-sign-in"/>
            </form>
        </x-adminlte-card>
        <x-adminlte-card theme="info" title="Riwayat" theme-mode="outline" header-class="rounded-bottom">
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatPemeriksaanRanap" :heads="$heads" head-theme="dark" :config="$config" striped hoverable bordered compressed>
                @foreach($riwayat as $row)
                    <tr>
                        <td>{{ $row->tgl_perawatan }}</td>
                        <td>{{ $row->jam_rawat }}</td>
                        <td>{{ $row->keluhan }}</td>
                        <td>{{ $row->pemeriksaan }}</td>
                        <td>{{ $row->penilaian }}</td>
                        <td>{{ $row->suhu_tubuh }}</td>
                        <td>{{ $row->tensi }}</td>
                        <td>{{ $row->nadi }}</td>
                        <td>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" data-toggle="modal" data-target="#editPemeriksaan" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            {{-- <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </button> --}}
                        </td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-card>
    </x-adminlte-card>
</div>

<x-adminlte-modal id="editPemeriksaan" title="Edit Pemeriksaan" theme="info" icon="fas fa-bolt" size='lg' v-centered static-backdrop scrollable>
    <div class="row">
        <x-adminlte-textarea name="keluhan" label="Subjek" fgroup-class="col-md-6" rows="4">
        </x-adminlte-textarea>
        <x-adminlte-textarea name="pemeriksaan" label="Objek" fgroup-class="col-md-6" rows="4">
        </x-adminlte-textarea>
    </div>
    <div class="row">
        <x-adminlte-textarea name="penilaian" label="Asesmen" fgroup-class="col-md-6" rows="2">
        </x-adminlte-textarea>
        <x-adminlte-textarea name="instruksi" label="Instruksi" fgroup-class="col-md-6" rows="2">
        </x-adminlte-textarea>
    </div>
    <div class="row">
        <x-adminlte-textarea name="rtl" label="Plan" fgroup-class="col-md-6" rows="2">
        </x-adminlte-textarea>
        <x-adminlte-textarea name="alergi" label="Alergi" fgroup-class="col-md-6" rows="2">
        </x-adminlte-textarea>
    </div>
    <div class="row">
        <x-adminlte-input name="suhu" label="Suhu Badan (C)" fgroup-class="col-md-3" />
        <x-adminlte-input name="berat" label="Berat (Kg)" fgroup-class="col-md-3" />
        <x-adminlte-input name="tinggi" label="Tinggi Badan (Cm)" fgroup-class="col-md-3" />
        <x-adminlte-input name="gcs" label="GCS (E, V, M)" fgroup-class="col-md-3" />
    </div>
    <div class="row">
        <x-adminlte-input name="tensi" label="Tensi" fgroup-class="col-md-3" />
        <x-adminlte-input name="nadi" label="Nadi (per Menit)" fgroup-class="col-md-3" />
        <x-adminlte-input name="respirasi" label="Respirasi (per Menit)" fgroup-class="col-md-3" />  
        <x-adminlte-select-bs name="kesadaran" label="Kesadaran" fgroup-class="col-md-3">
            @if(!empty($pemeriksaan->kesadaran))
                <option @php if($pemeriksaan->kesadaran == 'Compos Mentis') echo 'selected'; @endphp >Compos Mentis</option>
                <option @php if($pemeriksaan->kesadaran == 'Somnolence') echo 'selected'; @endphp >Somnolence</option>
                <option @php if($pemeriksaan->kesadaran == 'Sopor') echo 'selected'; @endphp >Sopor</option>
                <option @php if($pemeriksaan->kesadaran == 'Coma') echo 'selected'; @endphp >Coma</option>
            @else
                <option>Compos Mentis</option>
                <option>Somnolence</option>
                <option>Sopor</option>
                <option>Coma</option>
            @endif
        </x-adminlte-select-bs>            
    </div>
</x-adminlte-modal>

@push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $("#editPemeriksaan").click(function(event){
            event.preventDefault();
            $('#editPemeriksaan').modal('show');
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
            let instruksi = $("input[name=instruksi]").val();
            let alergi = $("input[name=alergi]").val();
            let rtl = $("input[name=rtl]").val();
            let gcs = $("input[name=gcs]").val();
            let _token   = $('meta[name="csrf-token"]').attr('content');
            // alert("{{request()->get('no_rawat')}}");
            $.ajax({
            url: "/ranap/pemeriksaan/submit",
            type:"POST",
            data:{
                no_rawat:"{{$encryptNoRawat}}",
                keluhan:keluhan,
                pemeriksaan:pemeriksaan,
                penilaian:penilaian,
                suhu:suhu,
                berat:berat,
                tinggi:tinggi,
                tensi:tensi,
                nadi:nadi,
                respirasi:respirasi,
                instruksi:instruksi,
                kesadaran:kesadaran,
                alergi:alergi,
                rtl:rtl,
                gcs:gcs,
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
                                    }});
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
        });
    </script>
@endpush