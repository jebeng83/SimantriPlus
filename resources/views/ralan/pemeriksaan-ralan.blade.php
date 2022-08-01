@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
    <h1>Pemeriksaan Ralan</h1>
@stop

@section('content')
    @isset($error)
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ $error }}
        </x-adminlte-alert>    
    @endisset
    @isset($success)
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ $success }}
        </x-adminlte-alert>
    @endisset
    <x-adminlte-modal id="modalRiwayatPemeriksaanRalan" title="Riwayat Pemeriksaan" size="xl" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
        <x-adminlte-callout theme="info">
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            {{-- Minimal example / fill data using the component slot --}}
            <x-adminlte-datatable id="tableRiwayatPemeriksaan" :heads="$headsRiwayatPemeriksaan" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                @foreach($riwayatPemeriksaan as $row)
                    <tr>
                        @php
                            $i = 0;
                        @endphp
                        @foreach($row as $cell)
                        @php
                            $pemriksaanRalan = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat, $row->status_lanjut);
                            $diagnosa = App\Http\Controllers\Ralan\PemeriksaanRalanController::getDiagnosa($row->no_rawat);
                            
                        @endphp
                            @if ($i == 3)
                                <td>
                                    <ul>
                                        <li>{{ $pemriksaanRalan->keluhan ?? '-' }}</li>
                                        <li>{{$pemriksaanRalan->pemeriksaan ?? '-'}}</li>
                                        @if(!empty($pemriksaanRalan->tinggi))
                                            <li>Tinggi  :  {{$pemriksaanRalan->tinggi}}</li>
                                        @endif
                                        @if(!empty($pemriksaanRalan->berat))
                                            <li>Berat  :  {{$pemriksaanRalan->berat}}</li>
                                        @endif
                                        @if(!empty($pemriksaanRalan->tensi))
                                            <li>Tensi  :  {{$pemriksaanRalan->tensi}}</li>
                                        @endif
                                        @if(!empty($pemriksaanRalan->nadi))
                                            <li>Nadi  :  {{$pemriksaanRalan->nadi}}</li>
                                        @endif
                                        @if(!empty($pemriksaanRalan->suhu))
                                            <li>Suhu  :  {{$pemriksaanRalan->suhu}}</li>
                                        @endif
                                        @if(!empty($pemriksaanRalan->respirasi))
                                            <li>RR  :  {{$pemriksaanRalan->respirasi}}</li>
                                        @endif
                                        <li>Alergi  :  {{$pemriksaanRalan->alergi ?? '-'}}</li>
                                        @if(!empty($pemriksaanRalan->rtl))
                                            <li>Tindak Lanjut  :  {{$pemriksaanRalan->rtl}}</li>
                                        @endif
                                    </ul>
                                </td>
                            @elseif ($i == 4)
                                <td>
                                    <ul">
                                        @foreach($diagnosa as $diagnosa)
                                            <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                                        @endforeach
                                    </ul>
                                </td>
                            @elseif ($i == 5)
                                @if($row->status_lanjut == 'Ralan')
                                    @php
                                        $pemeriksaanObstetri = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanObstetri($row->no_rawat);
                                    @endphp
                                    <td>
                                        @if(!empty($pemeriksaanObstetri))
                                            <ul>
                                                <li>Tinggi Fundus : {{$pemeriksaanObstetri->tinggi_uteri}}</li>
                                                <li>Janin : {{$pemeriksaanObstetri->janin}}</li>
                                                <li>Letak : {{$pemeriksaanObstetri->letak}}</li>
                                                <li>Bawah Panggul : {{$pemeriksaanObstetri->panggul}}</li>
                                                <li>Denyut Jantung : {{$pemeriksaanObstetri->denyut}}</li>
                                                <li>Kontarksi : {{$pemeriksaanObstetri->kontraksi}}</li>
                                                <li>Kualitas Menit : {{$pemeriksaanObstetri->kualitas_mnt}}</li>
                                                <li>Kualitas Detik : {{$pemeriksaanObstetri->kualitas_dtk}}</li>
                                                <li>Fluksus : {{$pemeriksaanObstetri->fluksus}}</li>
                                                <li>Fluor Albus : {{$pemeriksaanObstetri->albus}}</li>
                                                <li>Selaput Ketuban : {{$pemeriksaanObstetri->ketuban}}</li>
                                                <li>Vulva/Vagina : {{$pemeriksaanObstetri->vulva}}</li>
                                                <li>Portio Inspekulo  : {{$pemeriksaanObstetri->portio}}</li>
                                                <li>Dalam  : {{$pemeriksaanObstetri->dalam}}</li>
                                                <li>Tebal  : {{$pemeriksaanObstetri->tebal}}</li>
                                                <li>Arah  : {{$pemeriksaanObstetri->arah}}</li>
                                                <li>Pembukaan  : {{$pemeriksaanObstetri->pembukaan}}</li>
                                                <li>Penurunan  : {{$pemeriksaanObstetri->penurunan}}</li>
                                                <li>Denominator  : {{$pemeriksaanObstetri->denominator}}</li>
                                                <li>Feto-Pelvik  : {{$pemeriksaanObstetri->feto}}</li>
                                            </ul>
                                        @endif
                                    </td>
                                @else
                                    <td> - </td>
                                @endif

                            @else
                                <td>{!! $cell !!}</td>
                            @endif
                        @php
                            $i++;
                        @endphp
                        @endforeach    
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-profile-widget name="{{$pasien->nm_pasien}}" desc="{{$pasien->no_rkm_medis}}" theme="lightblue"
            img="https://picsum.photos/id/1/100" layout-type="classic">
            <x-adminlte-profile-row-item icon="fas fa-fw fa-user-friends" title="No Rawat" text="{{$pasien->no_rawat}}" />
            <x-adminlte-profile-row-item icon="fas fa-fw fa-user-friends fa-flip-horizontal" title="Tgl Lahir" text="{{$pasien->tgl_lahir}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Umur" text="{{$pasien->umur}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Cara Bayar" text="{{$pasien->png_jawab}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="No Telp" text="{{$pasien->no_tlp}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Pekerjaan" text="{{$pasien->pekerjaan}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="No Peserta" text="{{$pasien->no_peserta}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Alamat" text="{{$pasien->alamat}}"/>
            <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan" text="{{$pasien->catatan ?? '-'}}"/>
            <span class="nav-link">
                <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal" data-target="#modalRiwayatPemeriksaanRalan" class="bg-primary justify-content-end"/>
            </span>
            </x-adminlte-profile-widget>
        </div>
        <div class="col-md-8">
            <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
                <form id="pemeriksaanForm">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-textarea name="keluhan" label="Keluhan">
                            {{$pemeriksaan->keluhan ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-textarea name="pemeriksaan" label="Pemeriksaan">
                            {{$pemeriksaan->pemeriksaan ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-textarea name="penilaian" label="Penilaian">
                            {{$pemeriksaan->penilaian ?? ''}}
                        </x-adminlte-textarea>
                    </div>
                </div>
                <div class="row">
                    <x-adminlte-input name="suhu" label="Suhu Badan (C)" value="{{$pemeriksaan->suhu_tubuh ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="berat" label="Berat (Kg)" value="{{$pemeriksaan->berat ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="tinggi" label="Tinggi Badan (Cm)" value="{{$pemeriksaan->tinggi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="tensi" label="Tensi" value="{{$pemeriksaan->tensi ?? ''}}" fgroup-class="col-md-3" />
                </div>
                <div class="row">
                    <x-adminlte-input name="nadi" label="Nadi (per Menit)" value="{{$pemeriksaan->nadi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="respirasi" label="Respirasi (per Menit)" value="{{$pemeriksaan->respirasi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="instruksi" label="Instruksi" value="{{$pemeriksaan->instruksi ?? ''}}" fgroup-class="col-md-3" />
                    <div class="col-md-3">
                        <x-adminlte-select-bs name="kesadaran" label="Kesadaran">
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
                </div>
                <div class="row">
                    <x-adminlte-input name="alergi" label="Alergi" value="{{$pemeriksaan->alergi ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="imun" label="Imun Ke" value="{{$pemeriksaan->imun_ke ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="gcs" label="GCS (E, V, M)" value="{{$pemeriksaan->gcs ?? ''}}" fgroup-class="col-md-3" />
                    <x-adminlte-input name="rtl" label="Tindak Lanjut" value="{{$pemeriksaan->rtl ?? ''}}" fgroup-class="col-md-3" />
                </div>
                <x-slot name="footerSlot">
                    <x-adminlte-button class="d-flex ml-auto" id="pemeriksaanButton" theme="primary" label="Simpan"
                        icon="fas fa-sign-in"/>
                </x-slot>
                </form>
            </x-adminlte-card>

            {{-- <x-adminlte-card title="Info Card" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
                An info theme card with all the tool buttons...
            </x-adminlte-card>
            <x-adminlte-card title="Info Card" theme="info" icon="fas fa-lg fa-bell" collapsible removable maximizable>
                An info theme card with all the tool buttons...
            </x-adminlte-card> --}}
        </div>
    </div>

    {{-- resep section --}}
    <x-adminlte-card title="Resep" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <x-slot name="toolsSlot">
            <select class="custom-select w-auto form-control-border bg-light">
                <option>Resep</option>
                <option>Resep Racikan</option>
            </select>
        </x-slot>
        <x-adminlte-callout theme="info" title="Riwayat Peresepan">
            <form  method="post" id="resepForm" action="{{route('ralan.simpan.resep', ['no_rawat' => $pasien->no_rawat])}}">
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
        <x-adminlte-callout theme="info" title="Riwayat Peresepan">
            @php
                $config["responsive"] = true;
                $config['order'] = [[1, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatResep" :heads="$headsRiwayatResep" :config="$config" head-theme="dark" striped hoverable bordered compressed>
                {{-- <x-slot name="bodySlot"> --}}
                    @foreach($riwayatPeresepan as $r)
                    @php
                    $resepObat = App\Http\Controllers\Ralan\PemeriksaanRalanController::getResepObat($r->no_resep);
                    @endphp
                        <tr>
                            <td class="align-middle text-center">{{$r->no_resep}}</td>
                            <td class="align-middle text-center">{{$r->tgl_peresepan}}</td>
                            <td>
                                <ul>
                                @foreach($resepObat as $ro)
                                    <li>{{$ro->nama_brng}} - {{$ro->jml}} - [{{$ro->aturan_pakai}}]</li>
                                @endforeach
                                </ul>
                            </td>
                            <td class="align-middle text-center"><x-adminlte-button label="Copy Resep" class="mx-auto" theme="primary" icon="fas fa-note"/></td>
                        </tr>
                    @endforeach
                {{-- </x-slot> --}}
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </x-adminlte-card>

    {{-- riwayat pemeriksaan --}}
@stop

@section('plugins.TempusDominusBs4', true)
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
        html += '                  <input name="aturan_pakai]" id="aturan_pakai'+x+'" class="form-control" placeholder="Aturan Pakai">';
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
    $("#resepButton").click(function(e) {
        e.preventDefault();
        var text = $("#obat").select2('data');
        // console.log(text);
        let _token   = $('meta[name="csrf-token"]').attr('content');
        // var select = document.getElementById('obat');
		// var option = select.options[select.selectedIndex];
        var form = $("#resepForm");
        // var data = form.serialize();
        var data = {
            obat:text,
            jumlah:$("input[name=jumlah]").val(),
            aturan_pakai:$("input[name=aturan_pakai]").val(),
            _token:_token,
        };
        var url = form.attr('action');
        var method = form.attr('method');
        var noRawat = "{{$pasien->no_rawat}}";
        $.ajax({
            type: method,
            url: url,
            data: data,
            success: function (response) {
                console.log(response);
                if(response.status == 'sukses'){
                    Swal.fire({
                    title: 'Sukses',
                    text: 'Data berhasil disimpan',
                    type: 'success',
                    confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.value) {
                            window.location.href = "/ralan/pemeriksaan?"+noRawat;
                        }
                    })
                }else{
                    Swal.fire({
                    title: 'Gagal',
                    text: response.pesan,
                    type: 'error',
                    confirmButtonText: 'Ok'
                    })
                }
            },
            error: function (response) {
                console.log(response);
                Swal.fire({
                    title: 'Gagal',
                    text: response.pesan ?? 'Terjadi kesalahan',
                    type: 'error',
                    confirmButtonText: 'Ok'
                })
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
        let instruksi = $("input[name=instruksi]").val();
        let alergi = $("input[name=alergi]").val();
        let rtl = $("input[name=rtl]").val();
        let imun = $("input[name=imun]").val();
        let gcs = $("input[name=gcs]").val();
        let _token   = $('meta[name="csrf-token"]').attr('content');
  
        $.ajax({
          url: "/ralan/pemeriksaan/submit",
          type:"POST",
          data:{
            no_rawat:"{{$pasien->no_rawat}}",
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
            imun:imun,
            gcs:gcs,
            _token: _token
          },
          beforeSend: function() {
            Swal.fire({
              title: 'Loading',
            //   text: 'Sedang memproses data',
              imageUrl: '{{asset("img/loading.gif")}}',
              showConfirmButton: false,
            //   allowOutsideClick: false,
            //   allowEscapeKey: false,
            //   allowEnterKey: false,
            })
            },
          success:function(response){
            console.log(response);
            // var res = $.parseJSON(response);
            Swal.fire({
                text: response.message,
                icon: 'success',
                confirmButtonText: 'Tutup'
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
           
            // $('#nameError').text(response.responseJSON.errors.name);
            // $('#emailError').text(response.responseJSON.errors.email);
            // $('#mobileError').text(response.responseJSON.errors.mobile);
            // $('#messageError').text(response.responseJSON.errors.message);
          }
         });
    });
    </script>
@stop
