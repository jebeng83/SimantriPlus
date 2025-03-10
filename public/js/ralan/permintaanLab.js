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

// Inisialisasi Select2 untuk jenis pemeriksaan
$(document).ready(function() {
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

    // Pasang event handler untuk perubahan pilihan
    $('.jenis').on('select2:select select2:unselect', function(e) {
        handleJenisPemeriksaanChange();
    });
});

// Fungsi untuk menangani perubahan jenis pemeriksaan
function handleJenisPemeriksaanChange() {
    console.log('Jenis pemeriksaan berubah:', $('#jenis').val());
    // Hapus container template sebelumnya
    $('.template-container').remove();
    
    const selectedValues = $('#jenis').val();
    if (!selectedValues || selectedValues.length === 0) {
        console.log('Tidak ada jenis pemeriksaan yang dipilih');
        return;
    }
    
    console.log('Jumlah jenis pemeriksaan dipilih:', selectedValues.length);
    
    let promises = [];
    
    // Untuk setiap nilai yang dipilih, dapatkan teks (nama) dan proses
    selectedValues.forEach(function(value) {
        const optionElement = $('#jenis option[value="' + value + '"]');
        const namaPemeriksaan = optionElement.text() || 'Pemeriksaan';
        
        console.log('Memproses:', value, namaPemeriksaan);
        
        // Ambil template untuk setiap jenis pemeriksaan
        const promise = getTemplateLab(value)
            .then(response => {
                console.log('Response template:', response);
                if (response.status === 'sukses' && response.data && response.data.length > 0) {
                    const templateHtml = renderTemplateCheckboxes(response.data, value, namaPemeriksaan);
                    $('#template-area').append(templateHtml);
                    console.log('Template berhasil ditambahkan ke #template-area');
                } else {
                    console.log('Tidak ada template untuk', namaPemeriksaan);
                }
            })
            .catch(error => {
                console.error('Gagal mengambil template:', error);
            });
            
        promises.push(promise);
    });
    
    // Tunggu semua request selesai
    Promise.all(promises).catch(error => {
        console.error('Ada kesalahan saat mengambil template:', error);
    });
}

// Fungsi untuk mengambil template berdasarkan jenis pemeriksaan
function getTemplateLab(kdJenisPrw) {
    console.log('Mengambil template untuk kode:', kdJenisPrw);
    return $.ajax({
        url: '/api/template-lab/' + kdJenisPrw,
        type: 'GET',
        dataType: 'json'
    });
}

// Fungsi untuk menampilkan template dalam bentuk checkbox
function renderTemplateCheckboxes(templates, kdJenisPrw, namaPemeriksaan) {
    console.log('Render template untuk', namaPemeriksaan, 'dengan data:', templates);
    
    if (!templates || templates.length === 0) {
        console.log('Tidak ada template untuk', namaPemeriksaan);
        return '';
    }
    
    let html = `
        <div class="template-container mt-2" data-jenis="${kdJenisPrw}">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Template untuk ${namaPemeriksaan}</h3>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 40px;">Pilih</th>
                                <th>Pemeriksaan</th>
                                <th>Nilai Rujukan</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    templates.forEach(template => {
        // Gabungkan nilai rujukan dari 4 kolom berbeda
        let nilaiRujukan = '';
        
        if (template.nilai_rujukan_ld) {
            nilaiRujukan += 'LD: ' + template.nilai_rujukan_ld;
        }
        if (template.nilai_rujukan_la) {
            nilaiRujukan += (nilaiRujukan ? ', ' : '') + 'LA: ' + template.nilai_rujukan_la;
        }
        if (template.nilai_rujukan_pd) {
            nilaiRujukan += (nilaiRujukan ? ', ' : '') + 'PD: ' + template.nilai_rujukan_pd;
        }
        if (template.nilai_rujukan_pa) {
            nilaiRujukan += (nilaiRujukan ? ', ' : '') + 'PA: ' + template.nilai_rujukan_pa;
        }
        
        html += `
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input template-checkbox" type="checkbox" 
                            id="template-${template.id_template}" 
                            value="${template.id_template}" 
                            data-kd-jenis="${kdJenisPrw}">
                    </div>
                </td>
                <td>
                    <label class="form-check-label" for="template-${template.id_template}">
                        ${template.Pemeriksaan}
                    </label>
                </td>
                <td>${nilaiRujukan || '-'}</td>
                <td>${template.satuan || '-'}</td>
            </tr>
        `;
    });
    
    html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
    
    return html;
}

// Hapus handler untuk tombol simpan lama jika ada
$('#simpanPermintaanLab').off('click');

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
                url: '/api/hapus/permintaan-lab/'+noOrder,
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

$(document).ready(function() {
    // Modifikasi fungsi simpan untuk menyertakan template yang dipilih
    $('#form-lab').on('submit', function(e) {
        e.preventDefault();
        
        // Dapatkan data form yang sudah ada
        const klinis = $('#klinis').val();
        const info = $('#info').val();
        const jenisPemeriksaan = $('#jenis').val();
        
        // Dapatkan template yang dipilih
        const templates = [];
        $('.template-checkbox:checked').each(function() {
            templates.push({
                id_template: $(this).val(),
                kd_jenis_prw: $(this).data('kd-jenis')
            });
        });
        
        // Siapkan data untuk dikirim
        const data = {
            klinis: klinis,
            info: info,
            jns_pemeriksaan: jenisPemeriksaan,
            templates: templates
        };
        
        // Kirim data ke server
        simpanPermintaanLab(data);
    });
});

// Fungsi untuk menyimpan permintaan lab
function simpanPermintaanLab(data) {
    const encrypNoRawat = document.getElementById('permintaanLab').getAttribute('data-encrypNoRawat');
    const token = document.getElementById('permintaanLab').getAttribute('data-token');
    
    $.ajax({
        url: '/api/permintaan-lab/' + encrypNoRawat,
        type: 'POST',
        dataType: 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': token
        },
        beforeSend: function() {
            $('#btn-simpan').prop('disabled', true);
            $('#btn-simpan').html('<i class="fas fa-spinner fa-spin"></i> Proses...');
        },
        success: function(response) {
            if (response.status === 'sukses') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Permintaan lab berhasil disimpan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message
                });
                $('#btn-simpan').prop('disabled', false);
                $('#btn-simpan').html('Simpan');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error
            });
            $('#btn-simpan').prop('disabled', false);
            $('#btn-simpan').html('Simpan');
        }
    });
}