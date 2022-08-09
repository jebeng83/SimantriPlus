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
    <x-ralan.riwayat :no-rawat="request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" />
            <x-ralan.resume />
        </div>
    </div>

    {{-- resep section --}}
    <x-ralan.resep />
    
    {{-- obat section --}}

    {{-- riwayat pemeriksaan --}}
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
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
  
        $.ajax({
          url: "/ralan/pemeriksaan/submit",
          type:"POST",
          data:{
            no_rawat:"{{request()->get('no_rawat')}}",
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
