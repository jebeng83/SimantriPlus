<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-clipboard" collapsible="collapsed" maximizable>
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
        <x-adminlte-button class="d-flex ml-auto" id="pemeriksaanButton" theme="primary" label="Simpan" icon="fas fa-sign-in"/>
        </form>
    </x-adminlte-card>
</div>

@push('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
        // alert("{{request()->get('no_rawat')}}");
        $.ajax({
          url: "/ralan/pemeriksaan/submit",
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
@endpush