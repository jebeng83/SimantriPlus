var script = document.getElementById("resume");
var encrypNoRawat = script.getAttribute("data-encrypNoRawat");
var token = script.getAttribute("data-token");

$('#oklab').click(function(event){
    event.preventDefault();
    var lab = [];
    $.each($("input[name='hasil-lab-cek[]']:checked"), function(){
        lab.push($(this).val());
    });
    $('#lab-form').val(lab);
    $('#modalLab').modal('hide');
});

$('#okrad').click(function(event){
    event.preventDefault();
    var rad = [];
    $.each($("input[name='hasil-rad-cek[]']:checked"), function(){
        rad.push($(this).val());
    });
    $('#rad-form').val(rad);
    $('#modalRad').modal('hide');
});

$('#kelButton').click(function(event){
    event.preventDefault();
    $.ajax({
        url: '/api/hasil/kel/'+encrypNoRawat,
        type: 'GET',
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
            Swal.close();
            var keluhan = response.data;
            $('#keluhanUtama').val(keluhan);
        },
        error: function(response){
            Swal.close();
            Swal.fire({
                title: "Error",
                text: response.message ?? "Terjadi kesalahan",
                icon: "error",
                button: "OK",
            });
        }
    });
});

$('#radButton').click(function(event){
    event.preventDefault();
    var radHTML = '';
    $('.bodyrad').remove();
    $.ajax({
        url: '/api/hasil/rad/'+encrypNoRawat,
        type: 'GET',
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
            if(response.data.length == 0){
                Swal.fire({
                    title: "Data Pemeriksaan Radiologi Tidak Ditemukan",
                    icon: "info",
                    button: "OK",
                });
            }else{
                Swal.close();
                $.each(response.data, function(i, item){
                    var value = item.hasil;
                    radHTML += '<div class="bodylab">'+
                                '<div class="form-check mb-2">'+
                                    '<input class="form-check-input" type="checkbox" value="'+value+'" id="rad'+i+'" name="hasil-rad-cek[]">'+
                                    '<label class="form-check-label" for="lab'+i+'">'+
                                    value+
                                    '</label>'+
                                '</div>'+
                                '</div>';

                });
                $('.container-rad').append(labHTML);
                $('#modalRad').modal('show');
            }
        },
        error: function(response){
            Swal.fire({
                title: "Error",
                text: response.pesan ?? "Terjadi kesalahan",
                icon: "error",
                button: "OK",
            });
        }
    });     
});

$('#labButton').click(function(event){
    event.preventDefault();
    var labHTML = '';
    $('.bodylab').remove();
    $.ajax({
        url: '/api/hasil/lab/'+encrypNoRawat,
        type: 'GET',
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
            if(response.data.length == 0){
                Swal.fire({
                    title: "Data Pemeriksaan Lab tidak ditemukan",
                    icon: "info",
                    button: "OK",
                });
            }else{
                Swal.close();
                $.each(response.data, function(i, item){
                    var value = item.Pemeriksaan+' : '+item.nilai+' ';
                    labHTML += '<div class="bodylab">'+
                                '<div class="form-check mb-2">'+
                                    '<input class="form-check-input" type="checkbox" value="'+value+'" id="lab'+i+'" name="hasil-lab-cek[]">'+
                                    '<label class="form-check-label" for="lab'+i+'">'+
                                    item.Pemeriksaan+' : '+item.nilai+
                                    '</label>'+
                                '</div>'+
                                '</div>';

                });
                $('.container-lab').append(labHTML);
                $('#modalLab').modal('show');
            }
        },
        error: function(response){
            console.log(response);
            Swal.fire({
                title: "Error",
                text: response.pesan ?? "Terjadi kesalahan",
                icon: "error",
                button: "OK",
            });
        }
    });   
});

$('#resumeSubmitButton').click(function(event){
    event.preventDefault();
    // alert('test');
    $.ajax({
        url: '/api/resumemedis/'+encrypNoRawat,
        type: 'POST',
        data: {
            keluhan_utama: $('#keluhanUtama').val(),
            prosedur_utama: $('#prosedurUtama').val(),
            diagnosa_utama: $('#diagnosaUtama').val(),
            terapi: $('#terapi').val(),
            jalannya_penyakit: $('#jalan').val(),
            pemeriksaan_penunjang: $('#rad-form').val(),
            hasil_laborat: $('#lab-form').val(),
            kondisi_pulang: $('#kondisiPasien').find(":selected").val(),
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
                    text: response.pesan ?? "Data berhasil disimpan",
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
            if(response.status == 422){
                Swal.fire({
                    title: "Error",
                    text: response.responseJSON.message,
                    icon: "error",
                    button: "OK",
                });
            }else{
                Swal.fire({
                    title: "Error",
                    text: response.pesan ?? "Terjadi kesalahan",
                    icon: "error",
                    button: "OK",
                });
            }
        }
    });
});