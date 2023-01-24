var script = document.getElementById("permintaanLab");
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

$('.jenis').select2({
    placeholder: 'Pilih Jenis',
    ajax: {
        url: '/api/jns_perawatan_lab',
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

$('#simpanPermintaanLab').click(function(){
    $.ajax({
        url: '/api/permintaanlab/'+encrypNoRawat,
        type: 'POST',
        data: {
            klinis: $('#klinis').val(),
            info: $('#info').val(),
            jns_pemeriksaan: $('#jenis').val(),
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

function hapusPermintaanLab(noOrder, event){
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
                url: '/api/permintaanlab/'+noOrder,
                type: 'DELETE',
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