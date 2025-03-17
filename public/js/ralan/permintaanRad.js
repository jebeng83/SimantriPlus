var script = document.getElementById("permintaanRad");
var encrypNoRawat = script.getAttribute("data-encrypNoRawat");
var token = script.getAttribute("data-token");

function getValue(name) {
    var data = [];
    var doc = document.getElementsByName(name);
    for (var i = 0; i < doc.length; i++) {
            var a = doc[i].value;
            data.push(a);
        }

    return data;
}

function formatData (data) {
    var $data = $(
        '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
    );
    return $data;
};

$('.jenisRad').select2({
    placeholder: 'Pilih Jenis',
    ajax: {
        url: '/api/jns_perawatan_rad',
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

$('#simpanPermintaanRad').click(function(event){
    event.preventDefault();
    $.ajax({
        url: '/api/permintaanrad/'+encrypNoRawat,
        type: 'POST',
        data: {
            klinis: $('#klinisRad').val(),
            info: $('#infoRad').val(),
            jns_pemeriksaan: $('#jenisRad').val(),
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
            console.log(response);
            if(response.status == 'sukses'){
                Swal.fire({
                    title: "Sukses",
                    text: response.pesan ?? "Data berhasil disimpan",
                    icon: "success",
                    button: "OK",
                }).then((result) => {
                    if (result.value) {
                        window.location.reload();
                    }
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
            console.log(response);
            Swal.fire({
                title: "Gagal",
                text: response.pesan ?? "Data gagal disimpan",
                icon: "error",
                button: "OK",
            });
        }
    });
});

function hapusPermintaanRad(noOrder, event){
    event.preventDefault();
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
        }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/api/hapus/permintaanrad/'+noOrder,
                type: 'POST',
                data: {
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
                            text: response.pesan ?? "Data berhasil dihapus",
                            icon: "success",
                            button: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }else{
                        Swal.fire({
                            title: "Gagal",
                            text: response.pesan ?? "Data gagal dihapus",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function(response){
                    // console.log(response);
                    Swal.fire({
                        title: "Gagal",
                        text: response.pesan ?? "Data gagal dihapus",
                        icon: "error",
                        button: "OK",
                    });
                }
            });
        }
    });
}