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
                console.log('Data jns_perawatan_lab:', data);
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.text,
                        kd_jenis_prw: item.kd_jenis_prw
                    }))
                };
            },
            cache: true
        },
        templateResult: formatData,
        minimumInputLength: 3
    });

    // Pasang event handler untuk perubahan pilihan
    $('.jenis').on('select2:select select2:unselect', function(e) {
        console.log('Select2 event:', e);
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
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/api/template-lab/' + kdJenisPrw,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Response dari server:', response);
                if (response.status === 'sukses' && response.data) {
                    // Tidak perlu memproses ulang data karena sudah sesuai format dari backend
                    resolve({
                        status: 'sukses',
                        data: response.data
                    });
                } else {
                    resolve({
                        status: 'sukses',
                        data: []
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saat mengambil template:', error);
                reject(error);
            }
        });
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
        // Log untuk debugging
        console.log('Rendering template:', template);

        html += `
            <tr>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input template-checkbox" type="checkbox" 
                            id="template-${template.id_template}" 
                            value="${template.id_template}" 
                            data-kd-jenis="${template.kd_jenis_prw}"
                            data-pemeriksaan="${template.text}"
                            data-nilai-rujukan="${template.nilai_rujukan}"
                            data-satuan="${template.satuan}"
                            checked>
                    </div>
                </td>
                <td>
                    <label class="form-check-label" for="template-${template.id_template}">
                        ${template.text}
                    </label>
                </td>
                <td>${template.nilai_rujukan}</td>
                <td>${template.satuan}</td>
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
    
    // Tambahkan console log untuk debugging
    console.log('Menghapus permintaan lab dengan noOrder:', noOrder);
    
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
                dataType: 'json',
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
                    console.log('Respons hapus dari server:', response);
                    
                    if(response.status == 'sukses'){
                        Swal.fire({
                            title: "Sukses",
                            text: response.message ?? "Data berhasil dihapus",
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then(() => {
                            console.log('Menghapus baris dari tabel...');
                            
                            // Gunakan fungsi removeRowFromTable jika tersedia
                            if (typeof window.removeRowFromTable === 'function') {
                                window.removeRowFromTable(noOrder);
                            } else {
                                // Fallback ke reload halaman jika fungsi tidak tersedia
                                console.log('Fungsi removeRowFromTable tidak ditemukan, reload halaman...');
                                
                                // Hapus cache browser lalu refresh
                                const currentUrl = window.location.href.split('?')[0];
                                const refreshUrl = currentUrl + '?nocache=' + new Date().getTime();
                                window.location.href = refreshUrl;
                            }
                        });
                    }else{
                        console.error('Error hapus dari server:', response.message);
                        Swal.fire({
                            title: "Gagal",
                            text: response.message ?? "Data gagal dihapus",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function(xhr, status, error){
                    console.error('Error AJAX hapus:', error);
                    // Menampilkan informasi error yang lebih detail
                    let errorMessage = "Data gagal dihapus";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: "Gagal",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "OK",
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
    
    // Validasi data sebelum dikirim
    if (!data.jns_pemeriksaan || data.jns_pemeriksaan.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Silakan pilih minimal satu jenis pemeriksaan'
        });
        return;
    }
    
    // Tambahkan console log untuk debugging
    console.log('Mengirim permintaan lab dengan data:', data);
    console.log('Menggunakan no_rawat:', encrypNoRawat);
    
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
            console.log('Respons dari server:', response);
            
            if (response.status === 'sukses') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Permintaan lab berhasil disimpan',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Reload halaman dengan hard refresh
                    console.log('Melakukan refresh halaman...');
                    window.location.reload(true);
                });
            } else {
                console.error('Error dari server:', response.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Terjadi kesalahan saat menyimpan permintaan lab'
                });
                $('#btn-simpan').prop('disabled', false);
                $('#btn-simpan').html('Simpan');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', xhr.responseText);
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