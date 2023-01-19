$('#resumeButton').click(function(){
    var script = document.getElementById("resume");
    var encrypNoRawat = script.getAttribute("data-encrypNoRawat");
    var token = script.getAttribute("data-token");
    $.ajax({
        url: '/ralan/simpan/resumemedis/'+encrypNoRawat,
        type: 'POST',
        data: {
            keluhanUtama: $('#keluhanUtama').val(),
            prosedurUtama: $('#prosedurUtama').val(),
            diagnosaUtama: $('#diagnosaUtama').val(),
            terapi: $('#terapi').val(),
            _token: token
        },
        format: 'json',
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
        success: function(response){
            // console.log(response);
            if(response.status == 'sukses'){
                Swal.fire({
                    title: "Sukses",
                    text: "Data berhasil disimpan",
                    icon: "success",
                    button: "OK",
                });
            }else{
                Swal.fire({
                    title: "Gagal",
                    text: response.pesan ?? "Data gagal disimpan",
                    icon: "error",
                    button: "OK",
                });
            }
        },
        error: function(response){
            // console.log(response);
            Swal.fire({
                title: "Error",
                text: response.pesan ?? "Terjadi kesalahan",
                icon: "error",
                button: "OK",
            });
        }
    });
});