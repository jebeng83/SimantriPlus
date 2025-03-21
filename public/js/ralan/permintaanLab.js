// Inisialisasi variabel global
var script = document.getElementById('permintaanLab');
var encrypNoRawat = script.getAttribute("data-encrypNoRawat");
var token = script.getAttribute("data-token");
var baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
var noRawat = encrypNoRawat; // Menggunakan nilai terenkripsi untuk API calls

// Gunakan cache untuk menyimpan data dan mengurangi permintaan server
var permintaanLabCache = null;
var permintaanLabCacheTime = 0;
var templateCache = {};
var CACHE_VALIDITY_PERIOD = 30000; // 30 detik

function getValue(name) {
    // Coba cari berdasarkan ID
    const elem = document.getElementById(name);
    if (elem) {
        return elem.value || '';
    }
    
    // Jika tidak ditemukan ID, coba cari berdasarkan nama
    const elements = document.getElementsByName(name);
    if (elements.length > 0) {
        return elements[0].value || '';
    }
    
    return '';
}

function formatData (data) {
    var $data = $(
        '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
    );
    return $data;
};

// Inisialisasi komponen dengan lazy loading
$(document).ready(function() {
    // Lazy initialization untuk Select2 - hanya inisialisasi saat panel terbuka
    initializeLabComponents();
});

// Inisialisasi komponen lab hanya saat diperlukan untuk menghemat resource
function initializeLabComponents() {
    // Cek apakah komponen sudah diinisialisasi
    if ($('.jenis').data('initialized')) {
        return;
    }
    
    // Load permintaan lab hanya jika komponen lab terlihat
    if ($('#permintaan-lab-table-body').is(':visible')) {
        loadPermintaanLab();
    }
    
    // Inisialisasi Select2 jika belum
    if (!$('.jenis').data('select2')) {
        $('.jenis').select2({
            placeholder: 'Pilih Jenis',
            ajax: {
                url: '/api/jns_perawatan_lab',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
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
            handleJenisPemeriksaanChange();
        });
        
        $('.jenis').data('initialized', true);
    }
}

// Fungsi untuk menangani perubahan jenis pemeriksaan
function handleJenisPemeriksaanChange() {
    // Hapus container template sebelumnya
    $('.template-container').remove();
    
    const selectedValues = $('#jenis').val();
    if (!selectedValues || selectedValues.length === 0) {
        return;
    }
    
    let promises = [];
    
    // Untuk setiap nilai yang dipilih, dapatkan teks (nama) dan proses
    selectedValues.forEach(function(value) {
        const optionElement = $('#jenis option[value="' + value + '"]');
        const namaPemeriksaan = optionElement.text() || 'Pemeriksaan';
        
        // Periksa cache terlebih dahulu
        if (templateCache[value]) {
            const templateHtml = renderTemplateCheckboxes(templateCache[value], value, namaPemeriksaan);
            $('#template-area').append(templateHtml);
        } else {
            // Ambil template untuk setiap jenis pemeriksaan
            const promise = getTemplateLab(value)
                .then(response => {
                    if (response.status === 'sukses' && response.data && response.data.length > 0) {
                        // Simpan ke cache
                        templateCache[value] = response.data;
                        
                        const templateHtml = renderTemplateCheckboxes(response.data, value, namaPemeriksaan);
                        $('#template-area').append(templateHtml);
                    }
                })
                .catch(error => {
                    // Kesalahan silenced pada production
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.error('Gagal mengambil template:', error);
                    }
                });
                
            promises.push(promise);
        }
    });
}

// Fungsi untuk mengambil template berdasarkan jenis pemeriksaan
function getTemplateLab(kdJenisPrw) {
    return new Promise((resolve, reject) => {
        // Gunakan cache jika tersedia
        if (templateCache[kdJenisPrw]) {
            console.log(`Menggunakan cache template untuk kd_jenis_prw: ${kdJenisPrw}, ${templateCache[kdJenisPrw].length} item`);
            return resolve({
                status: 'sukses',
                data: templateCache[kdJenisPrw]
            });
        }
        
        console.log(`Memuat template untuk kd_jenis_prw: ${kdJenisPrw}`);
        $.ajax({
            url: '/api/template-lab/' + kdJenisPrw,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'sukses' && response.data) {
                    // Simpan ke cache
                    templateCache[kdJenisPrw] = response.data;
                    console.log(`Template berhasil dimuat: ${response.data.length} item`);
                    
                    resolve({
                        status: 'sukses',
                        data: response.data
                    });
                } else {
                    console.warn(`Tidak ada template ditemukan untuk kd_jenis_prw: ${kdJenisPrw}`);
                    resolve({
                        status: 'sukses',
                        data: []
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(`Error memuat template: ${error}`, xhr);
                reject(error);
            }
        });
    });
}

// Fungsi untuk menampilkan template dalam bentuk checkbox - optimized
function renderTemplateCheckboxes(templates, kdJenisPrw, namaPemeriksaan) {
    if (!templates || templates.length === 0) {
        return '';
    }
    
    // Gunakan array concatenation untuk performa lebih baik dengan string panjang
    let htmlParts = [
        `<div class="template-container mt-2" data-jenis="${kdJenisPrw}">
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
                        <tbody>`
    ];
    
    templates.forEach(template => {
        htmlParts.push(`
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
        `);
    });
    
    htmlParts.push(`
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `);
    
    return htmlParts.join('');
}

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
                    if(response.status == 'sukses'){
                        Swal.fire({
                            title: "Sukses",
                            text: response.message ?? "Data berhasil dihapus",
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then(() => {
                            // Gunakan fungsi removeRowFromTable jika tersedia
                            if (typeof window.removeRowFromTable === 'function') {
                                window.removeRowFromTable(noOrder);
                                
                                // Hapus dari cache jika ada
                                if (permintaanLabCache) {
                                    permintaanLabCache = permintaanLabCache.filter(item => item.noorder !== noOrder);
                                }
                            } else {
                                // Fallback ke reload halaman jika fungsi tidak tersedia
                                window.location.reload(true);
                            }
                        });
                    }else{
                        Swal.fire({
                            title: "Gagal",
                            text: response.message ?? "Data gagal dihapus",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function(xhr, status, error){
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
    // Event untuk mendeteksi saat tab permintaan lab dibuka
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const targetId = $(e.target).attr('href');
        if (targetId === '#permintaanLab' || targetId.indexOf('permintaan-lab') !== -1) {
            initializeLabComponents();
        }
    });
    
    // Event untuk kartu yang collapsible
    $('.card-collapse').on('shown.bs.collapse', function() {
        if ($(this).find('#permintaan-lab-table-body').length) {
            initializeLabComponents();
        }
    });
    
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
                // Invalidate cache
                permintaanLabCache = null;
                permintaanLabCacheTime = 0;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Permintaan lab berhasil disimpan',
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Reload halaman dengan hard refresh
                    window.location.reload(true);
                });
            } else {
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

// Fungsi untuk memuat data permintaan lab dari server - optimized
function loadPermintaanLab() {
    const encrypNoRawat = document.getElementById('permintaanLab').getAttribute('data-encrypNoRawat');
    
    // Gunakan cache jika masih valid
    const now = Date.now();
    if (permintaanLabCache && (now - permintaanLabCacheTime < CACHE_VALIDITY_PERIOD)) {
        renderPermintaanLabTable(permintaanLabCache);
        return;
    }
    
    // Tambahkan indikator loading di tbody
    $('#permintaan-lab-table-body').html(`
        <tr>
            <td colspan="6" class="text-center">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <p class="mt-2">Memuat data permintaan lab...</p>
            </td>
        </tr>
    `);
    
    // Validasi parameter
    if (!encrypNoRawat || encrypNoRawat === 'undefined' || encrypNoRawat === 'null') {
        $('#permintaan-lab-table-body').html(`
            <tr>
                <td colspan="6" class="text-center">
                    <div class="alert alert-danger">
                        <p>Error: Parameter tidak valid</p>
                        <small class="d-block mt-1">
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="window.location.reload(true)">
                                <i class="fas fa-sync-alt"></i> Reload Halaman
                            </button>
                        </small>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    // Kirim permintaan AJAX
    $.ajax({
        url: '/api/get-permintaan-lab/' + encrypNoRawat,
        type: 'GET',
        dataType: 'json',
        timeout: 15000, // 15 detik timeout
        success: function(response) {
            if (response.status === 'sukses' && response.data && response.data.length > 0) {
                // Simpan ke cache
                permintaanLabCache = response.data;
                permintaanLabCacheTime = now;
                
                renderPermintaanLabTable(response.data);
            } else {
                // Coba fallback dengan memuat dari DOM yang sudah ada
                const existingRows = document.querySelectorAll('#permintaan-lab-table-body tr[id^="row-"]');
                
                if (existingRows.length > 0) {
                    // Data sudah ada di DOM, tidak perlu melakukan apa-apa
                    $('#permintaan-lab-table-body').html('');
                    existingRows.forEach(row => {
                        $('#permintaan-lab-table-body').append(row.outerHTML);
                    });
                } else {
                    // Tampilkan pesan jika tidak ada data
                    $('#permintaan-lab-table-body').html(`
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-info">
                                    <p>Belum ada permintaan laboratorium. Silakan tambahkan permintaan baru.</p>
                                    <button class="btn btn-sm btn-secondary mt-2" onclick="window.location.reload(true)">
                                        <i class="fas fa-sync-alt"></i> Muat Ulang Data
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            }
        },
        error: function(xhr, status, error) {
            // Coba fallback dengan memuat dari DOM yang sudah ada
            const existingRows = document.querySelectorAll('#permintaan-lab-table-body tr[id^="row-"]');
            
            if (existingRows.length > 0) {
                // Data sudah ada di DOM, tidak perlu melakukan apa-apa
                $('#permintaan-lab-table-body').html('');
                existingRows.forEach(row => {
                    $('#permintaan-lab-table-body').append(row.outerHTML);
                });
            } else {
                // Tampilkan pesan error
                $('#permintaan-lab-table-body').html(`
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-danger">
                                <p>Terjadi kesalahan saat memuat data permintaan lab</p>
                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadPermintaanLab()">
                                    <i class="fas fa-sync-alt"></i> Coba Lagi
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        }
    });
}

// Fungsi untuk merender tabel permintaan lab - optimized
function renderPermintaanLabTable(data) {
    // Kosongkan tabel
    $('#permintaan-lab-table-body').empty();
    
    // Jika tidak ada data, tampilkan pesan
    if (!data || data.length === 0) {
        $('#permintaan-lab-table-body').html(`
            <tr>
                <td colspan="6" class="text-center">
                    <div class="alert alert-info">
                        <p>Belum ada permintaan laboratorium. Silakan tambahkan permintaan baru.</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    // Render HTML dengan strategi satu string (lebih efisien)
    let html = '';
    
    data.forEach(item => {
        html += `
            <tr id="row-${item.noorder}">
                <td scope="row">${item.noorder || 'N/A'}</td>
                <td>${item.tgl_permintaan || 'N/A'} ${item.jam_permintaan || ''}</td>
                <td>${item.informasi_tambahan || 'N/A'}</td>
                <td>${item.diagnosa_klinis || 'N/A'}</td>
                <td id="detail-${item.noorder}">
                    <span class="text-muted">Memuat detail...</span>
                </td>
                <td>
                    <button class="btn btn-danger btn-sm hapus-lab-btn" data-noorder="${item.noorder}"
                        onclick='hapusPermintaanLab("${item.noorder}", event)'>Hapus</button>
                </td>
            </tr>
        `;
    });
    
    $('#permintaan-lab-table-body').html(html);
    
    // Lazy load detail pemeriksaan hanya untuk item yang terlihat
    lazyLoadVisibleDetails(data);
}

// Fungsi untuk load detail secara lazy hanya untuk baris yang terlihat
function lazyLoadVisibleDetails(data) {
    // Fungsi untuk mengecek apakah elemen terlihat
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Fungsi untuk load detail pemeriksaan yang terlihat
    function loadVisibleDetails() {
        data.forEach(item => {
            const detailElement = document.getElementById(`detail-${item.noorder}`);
            if (detailElement && isElementInViewport(detailElement) && !detailElement.dataset.loaded) {
                detailElement.dataset.loaded = true;
                loadPemeriksaanDetail(item.noorder);
            }
        });
    }
    
    // Load detail untuk baris yang terlihat saat ini
    loadVisibleDetails();
    
    // Load tambahan ketika user melakukan scroll
    $(window).on('scroll', loadVisibleDetails);
}

// Fungsi untuk memuat detail pemeriksaan - optimized
function loadPemeriksaanDetail(noOrder) {
    // Gunakan cache jika tersedia
    const cacheKey = `detail_${noOrder}`;
    if (templateCache[cacheKey]) {
        renderDetailPemeriksaan(noOrder, templateCache[cacheKey]);
        return;
    }
    
    $.ajax({
        url: '/api/get-detail-pemeriksaan/' + noOrder,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'sukses' && response.data && response.data.length > 0) {
                // Simpan ke cache
                templateCache[cacheKey] = response.data;
                renderDetailPemeriksaan(noOrder, response.data);
            } else {
                $(`#detail-${noOrder}`).html('<span class="text-muted">Tidak ada detail pemeriksaan</span>');
            }
        },
        error: function(error) {
            $(`#detail-${noOrder}`).html('<span class="text-danger">Gagal memuat detail</span>');
        }
    });
}

// Fungsi untuk render detail pemeriksaan
function renderDetailPemeriksaan(noOrder, data) {
    if (!data || data.length === 0) {
        $(`#detail-${noOrder}`).html('<span class="text-muted">Tidak ada detail pemeriksaan</span>');
        return;
    }
    
    let detailHtml = '<ul class="mb-0 pl-3">';
    
    data.forEach(item => {
        detailHtml += `<li>${item.nm_perawatan || 'Pemeriksaan'}</li>`;
    });
    
    detailHtml += '</ul>';
    
    $(`#detail-${noOrder}`).html(detailHtml);
}

// Fungsi untuk reset form
function resetForm() {
    // Reset input fields
    $('#klinis').val('');
    $('#info-tambahan').val('');
    
    // Hapus semua checkbox yang dipilih
    $('.jenis-lab-checkbox').prop('checked', false);
    
    // Hapus semua container template
    $('.template-container').remove();
}

// Fungsi untuk memuat ulang data lab request
function loadLabRequest() {
    // Invalidate cache
    permintaanLabCache = null;
    permintaanLabCacheTime = 0;
    
    // Muat ulang data permintaan lab
    loadPermintaanLab();
}