var script = document.getElementById('pemeriksaanjs');
var encryptNoRawat = script.getAttribute('data-encryptNoRawat');
var token = script.getAttribute('data-token');

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
    let imun = $("select[name=imun]").val();
    let gcs = $("input[name=gcs]").val();
    let _token   = token;
    // alert("{{request()->get('no_rawat')}}");
    $.ajax({
      url: "/ralan/pemeriksaan/submit",
      type:"POST",
      data:{
        no_rawat:encryptNoRawat,
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
      }
     });
});