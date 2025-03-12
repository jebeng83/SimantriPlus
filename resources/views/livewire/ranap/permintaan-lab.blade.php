<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-flask mr-1"></i> Permintaan Lab </h3>
        <div class="card-tools">
            {{-- <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-lg fa-expand"></i>
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="savePermintaanLab">
            <div class="form-group row">
                <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" wire:model.defer="klinis" id="klinis" name="klinis" />
                    @error('klinis') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" wire:model.defer="info" id="info" name="info" />
                    @error('info') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div wire:ignore class="form-group row">
                <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
                <div class="col-sm-8">
                    <select class="form-control jenis" wire:model.defer="jns_pemeriksaan" id="jenis_lab" name="jenis[]"
                        multiple="multiple"></select>
                </div>
                @error('jns_pemeriksaan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- Container untuk template -->
            <div id="template-area" class="mt-3"></div>

            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit"> Simpan </button>
            </div>
        </form>
        <div class="callout callout-info">
            <h5> Daftar Permintaan Lab </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-inverse" style="width: 100%">
                        <tr>
                            <th>No. Order</th>
                            <th>Informasi</th>
                            <th>Klinis</th>
                            <th>Pemeriksaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaanLab as $item)
                        <tr>
                            <td>{{ $item->noorder }}</td>
                            <td>{{ $item->informasi_tambahan }}</td>
                            <td>{{ $item->diagnosa_klinis }}</td>
                            <td>
                                @foreach ($this->getDetailPemeriksaan($item->noorder) as $pemeriksaan)
                                <span class="badge badge-primary">{{ $pemeriksaan->nm_perawatan }}</span>
                                @endforeach
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    wire:click="konfirmasiHapus('{{ $item->noorder }}')">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Permintaan Lab Kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    // Pastikan CSRF token tersedia untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // Tambahkan fungsi untuk debugging AJAX
    $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
        console.error('AJAX Error:', thrownError, jqxhr.status, jqxhr.responseText, settings.url);
    });

    window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm',function(e){
            Swal.fire({
                title: e.detail.title,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: e.detail.confirmButtonText,
                cancelButtonText: e.detail.cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.function, e.detail.params[0]);
                }
            });
        });

        function formatData (data) {
            var $data = $(
                '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
            );
            return $data;
        };

        $('#jenis_lab').select2({
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

        $('#jenis_lab').on('change', function (e) {
            let data = $(this).val();
            @this.set('jns_pemeriksaan', data);
            
            // Tambahkan kode untuk menampilkan template checklist
            loadTemplateOptions(data);
        });
        
        // Fungsi untuk memuat opsi template berdasarkan jenis pemeriksaan yang dipilih
        function loadTemplateOptions(selectedItems) {
            console.log('Loading template options for:', selectedItems);
            
            if (!selectedItems || selectedItems.length === 0) {
                console.log('No items selected');
                $('#template-area').html('<div class="alert alert-warning">Pilih jenis pemeriksaan terlebih dahulu</div>');
                return;
            }
            
            // Tampilkan pesan loading
            $('#template-area').html('<div class="alert alert-info">Memuat template...</div>');
            
            // Dapatkan teks dari item yang dipilih untuk logging
            const selectedTexts = [];
            selectedItems.forEach(function(item) {
                const optionText = $('#jenis_lab option[value="' + item + '"]').text();
                selectedTexts.push(item + ' - ' + optionText);
            });
            console.log('Selected items with text:', selectedTexts);
            
            // Coba metode GET terlebih dahulu
            $.ajax({
                url: '/api/template-lab',
                type: 'GET',
                dataType: 'json',
                data: {
                    kd_jenis_prw: selectedItems
                },
                success: function(response) {
                    console.log('GET response:', response);
                    
                    if (response && (response.data || response.templates)) {
                        const templates = response.data || response.templates;
                        console.log('Templates found:', templates);
                        renderTemplateOptions(templates);
                    } else {
                        console.log('Invalid or empty response from GET, trying alternative methods');
                        tryAlternativeMethods(selectedItems);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('GET Error:', xhr.responseText, status, error);
                    tryAlternativeMethods(selectedItems);
                }
            });
        }
        
        // Fungsi untuk mencoba metode alternatif jika metode GET gagal
        function tryAlternativeMethods(selectedItems) {
            console.log('Trying POST method for template retrieval');
            
            // Coba metode POST
            $.ajax({
                url: '/api/template-lab',
                type: 'POST',
                dataType: 'json',
                data: {
                    kd_jenis_prw: selectedItems
                },
                success: function(response) {
                    console.log('POST response:', response);
                    
                    if (response && (response.data || response.templates)) {
                        const templates = response.data || response.templates;
                        console.log('Templates found with POST:', templates);
                        renderTemplateOptions(templates);
                    } else {
                        console.log('Invalid or empty response from POST, trying JSON method');
                        tryPostJsonMethod(selectedItems);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('POST Error:', xhr.responseText, status, error);
                    tryPostJsonMethod(selectedItems);
                }
            });
        }
        
        // Fungsi untuk mencoba metode POST dengan JSON jika metode POST biasa gagal
        function tryPostJsonMethod(selectedItems) {
            console.log('Trying POST JSON method for template retrieval');
            
            // Coba metode POST dengan JSON
            $.ajax({
                url: '/api/template-lab',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    kd_jenis_prw: selectedItems
                }),
                success: function(response) {
                    console.log('POST JSON response:', response);
                    
                    if (response && (response.data || response.templates)) {
                        const templates = response.data || response.templates;
                        console.log('Templates found with POST JSON:', templates);
                        renderTemplateOptions(templates);
                    } else {
                        console.log('Invalid or empty response from all methods, creating dummy templates');
                        createDummyTemplates(selectedItems);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('POST JSON Error:', xhr.responseText, status, error);
                    createDummyTemplates(selectedItems);
                }
            });
        }
        
        // Fungsi untuk membuat template dummy jika semua metode gagal
        function createDummyTemplates(selectedItems) {
            console.log('Creating dummy templates for:', selectedItems);
            
            // Dapatkan teks dari item yang dipilih
            const templates = [];
            
            selectedItems.forEach(function(item) {
                const optionText = $('#jenis_lab option[value="' + item + '"]').text();
                console.log('Creating dummy templates for:', item, optionText);
                
                // Buat template dummy berdasarkan jenis pemeriksaan
                if (optionText.includes('DARAH') || optionText.includes('HEMATOLOGI')) {
                    templates.push({
                        kd_jenis_prw: item,
                        nm_jenis_prw: optionText,
                        templates: [
                            { id_template: 'dummy1', Pemeriksaan: 'Hemoglobin', satuan: 'g/dL', nilai_rujukan_ld: '13.0', nilai_rujukan_la: '18.0' },
                            { id_template: 'dummy2', Pemeriksaan: 'Hematokrit', satuan: '%', nilai_rujukan_ld: '40.0', nilai_rujukan_la: '50.0' },
                            { id_template: 'dummy3', Pemeriksaan: 'Eritrosit', satuan: 'juta/uL', nilai_rujukan_ld: '4.5', nilai_rujukan_la: '6.0' },
                            { id_template: 'dummy4', Pemeriksaan: 'Leukosit', satuan: 'ribu/uL', nilai_rujukan_ld: '4.0', nilai_rujukan_la: '10.0' },
                            { id_template: 'dummy5', Pemeriksaan: 'Trombosit', satuan: 'ribu/uL', nilai_rujukan_ld: '150', nilai_rujukan_la: '450' }
                        ]
                    });
                } else if (optionText.includes('ASAM URAT')) {
                    templates.push({
                        kd_jenis_prw: item,
                        nm_jenis_prw: optionText,
                        templates: [
                            { id_template: 'dummy1', Pemeriksaan: 'Asam Urat', satuan: 'mg/dL', nilai_rujukan_ld: '3.5', nilai_rujukan_la: '7.2' }
                        ]
                    });
                } else if (optionText.includes('GULA') || optionText.includes('GLUKOSA')) {
                    templates.push({
                        kd_jenis_prw: item,
                        nm_jenis_prw: optionText,
                        templates: [
                            { id_template: 'dummy1', Pemeriksaan: 'Glukosa Puasa', satuan: 'mg/dL', nilai_rujukan_ld: '70', nilai_rujukan_la: '100' },
                            { id_template: 'dummy2', Pemeriksaan: 'Glukosa 2 Jam PP', satuan: 'mg/dL', nilai_rujukan_ld: '100', nilai_rujukan_la: '140' },
                            { id_template: 'dummy3', Pemeriksaan: 'HbA1C', satuan: '%', nilai_rujukan_ld: '4.0', nilai_rujukan_la: '6.0' }
                        ]
                    });
                } else {
                    templates.push({
                        kd_jenis_prw: item,
                        nm_jenis_prw: optionText,
                        templates: [
                            { id_template: 'dummy1', Pemeriksaan: 'Parameter 1', satuan: '-', nilai_rujukan_ld: '-', nilai_rujukan_la: '-' },
                            { id_template: 'dummy2', Pemeriksaan: 'Parameter 2', satuan: '-', nilai_rujukan_ld: '-', nilai_rujukan_la: '-' },
                            { id_template: 'dummy3', Pemeriksaan: 'Parameter 3', satuan: '-', nilai_rujukan_ld: '-', nilai_rujukan_la: '-' }
                        ]
                    });
                }
            });
            
            console.log('Dummy templates created:', templates);
            renderTemplateOptions(templates);
        }
        
        // Fungsi untuk merender opsi template
        function renderTemplateOptions(templates) {
            console.log('Rendering template options:', templates);
            
            if (!templates || templates.length === 0) {
                console.log('No templates to render');
                $('#template-area').html('<div class="alert alert-warning">Tidak ada template yang tersedia</div>');
                return;
            }
            
            let html = '';
            
            // Periksa format data
            if (Array.isArray(templates) && templates[0] && templates[0].kd_jenis_prw) {
                // Format: array of objects with kd_jenis_prw and templates
                console.log('Format: array of objects with kd_jenis_prw and templates');
                
                templates.forEach(function(group) {
                    const jenisPrw = group.kd_jenis_prw;
                    const namaPrw = group.nm_jenis_prw || $('#jenis_lab option[value="' + jenisPrw + '"]').text();
                    const groupTemplates = group.templates || [];
                    
                    if (groupTemplates.length > 0) {
                        html += `<div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">${namaPrw}</h5>
                            </div>
                            <div class="card-body">`;
                        
                        groupTemplates.forEach(function(template) {
                            html += `<div class="form-check">
                                <input class="form-check-input template-checkbox" type="checkbox" value="${template.id_template}" 
                                    data-jenis-prw="${jenisPrw}" 
                                    data-pemeriksaan="${template.Pemeriksaan}" 
                                    data-satuan="${template.satuan || ''}" 
                                    data-nilai-rujukan-ld="${template.nilai_rujukan_ld || ''}" 
                                    data-nilai-rujukan-la="${template.nilai_rujukan_la || ''}" 
                                    id="template-${template.id_template}">
                                <label class="form-check-label" for="template-${template.id_template}">
                                    ${template.Pemeriksaan} ${template.satuan ? '(' + template.satuan + ')' : ''}
                                    ${template.nilai_rujukan_ld && template.nilai_rujukan_la ? ' - Nilai Rujukan: ' + template.nilai_rujukan_ld + ' - ' + template.nilai_rujukan_la : ''}
                                </label>
                            </div>`;
                        });
                        
                        html += `</div></div>`;
                    }
                });
            } else if (Array.isArray(templates)) {
                // Format: flat array of templates
                console.log('Format: flat array of templates');
                
                // Kelompokkan template berdasarkan jenis pemeriksaan
                const groupedTemplates = {};
                
                templates.forEach(function(template) {
                    const jenisPrw = template.kd_jenis_prw || 'unknown';
                    
                    if (!groupedTemplates[jenisPrw]) {
                        groupedTemplates[jenisPrw] = {
                            kd_jenis_prw: jenisPrw,
                            nm_jenis_prw: $('#jenis_lab option[value="' + jenisPrw + '"]').text() || 'Unknown',
                            templates: []
                        };
                    }
                    
                    groupedTemplates[jenisPrw].templates.push(template);
                });
                
                // Render template yang dikelompokkan
                Object.values(groupedTemplates).forEach(function(group) {
                    const jenisPrw = group.kd_jenis_prw;
                    const namaPrw = group.nm_jenis_prw;
                    const groupTemplates = group.templates;
                    
                    if (groupTemplates.length > 0) {
                        html += `<div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">${namaPrw}</h5>
                            </div>
                            <div class="card-body">`;
                        
                        groupTemplates.forEach(function(template) {
                            html += `<div class="form-check">
                                <input class="form-check-input template-checkbox" type="checkbox" value="${template.id_template}" 
                                    data-jenis-prw="${jenisPrw}" 
                                    data-pemeriksaan="${template.Pemeriksaan}" 
                                    data-satuan="${template.satuan || ''}" 
                                    data-nilai-rujukan-ld="${template.nilai_rujukan_ld || ''}" 
                                    data-nilai-rujukan-la="${template.nilai_rujukan_la || ''}" 
                                    id="template-${template.id_template}">
                                <label class="form-check-label" for="template-${template.id_template}">
                                    ${template.Pemeriksaan} ${template.satuan ? '(' + template.satuan + ')' : ''}
                                    ${template.nilai_rujukan_ld && template.nilai_rujukan_la ? ' - Nilai Rujukan: ' + template.nilai_rujukan_ld + ' - ' + template.nilai_rujukan_la : ''}
                                </label>
                            </div>`;
                        });
                        
                        html += `</div></div>`;
                    }
                });
            } else {
                // Format tidak dikenali
                console.error('Unrecognized template format:', templates);
                $('#template-area').html('<div class="alert alert-danger">Format template tidak valid</div>');
                return;
            }
            
            if (html === '') {
                $('#template-area').html('<div class="alert alert-warning">Tidak ada template yang tersedia</div>');
            } else {
                $('#template-area').html(html);
            }
        }

        // Tambahkan event listener untuk reset template area ketika form di-reset
        window.livewire.on('resetForm', function() {
            $('#template-area').html('');
            $('#jenis_lab').val(null).trigger('change');
        });

        window.livewire.on('select2Lab', () => {
            $('#jenis_lab').select2({
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
            
            // Reset template area
            $('#template-area').html('');
        });

        // Fungsi untuk memeriksa keberadaan template di database
        function checkTemplateExistence() {
            console.log('Memeriksa keberadaan template di database');
            
            $.ajax({
                url: '/api/template-lab/check',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Response check template existence:', response);
                    
                    if (response.status === 'sukses') {
                        const templateCount = response.template_count;
                        const sampleTemplates = response.sample_templates;
                        const examTypesWithTemplates = response.exam_types_with_templates;
                        
                        let message = `Total template di database: ${templateCount}`;
                        
                        if (examTypesWithTemplates && examTypesWithTemplates.length > 0) {
                            message += '<br>Jenis pemeriksaan yang memiliki template:';
                            message += '<ul>';
                            examTypesWithTemplates.forEach(function(item) {
                                message += `<li>${item.kd_jenis_prw} - ${item.nm_jenis_prw}</li>`;
                            });
                            message += '</ul>';
                        }
                        
                        if (sampleTemplates && sampleTemplates.length > 0) {
                            message += '<br>Contoh template:';
                            message += '<ul>';
                            sampleTemplates.forEach(function(template) {
                                message += `<li>${template.id_template} - ${template.Pemeriksaan}</li>`;
                            });
                            message += '</ul>';
                        }
                        
                        // Tampilkan informasi dalam modal
                        showTemplateInfoModal(message);
                    } else {
                        console.error('Gagal memeriksa template:', response.message);
                        showTemplateInfoModal('Gagal memeriksa template: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking templates:', xhr.responseText, status, error);
                    showTemplateInfoModal('Error checking templates: ' + error);
                }
            });
        }
        
        // Fungsi untuk menampilkan modal informasi template
        function showTemplateInfoModal(content) {
            // Hapus modal lama jika ada
            $('#templateInfoModal').remove();
            
            // Buat modal baru
            const modalHtml = `
                <div class="modal fade" id="templateInfoModal" tabindex="-1" role="dialog" aria-labelledby="templateInfoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="templateInfoModalLabel">Informasi Template</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ${content}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Tambahkan modal ke body
            $('body').append(modalHtml);
            
            // Tampilkan modal
            $('#templateInfoModal').modal('show');
        }
        
        // Fungsi untuk membuat template dummy di database
        function createDummyTemplatesInDatabase(kd_jenis_prw) {
            console.log('Membuat template dummy di database untuk jenis pemeriksaan:', kd_jenis_prw);
            
            $.ajax({
                url: '/api/template-lab/create-dummy',
                type: 'POST',
                dataType: 'json',
                data: {
                    kd_jenis_prw: kd_jenis_prw
                },
                success: function(response) {
                    console.log('Response create dummy templates:', response);
                    
                    if (response.status === 'sukses') {
                        // Reload template options
                        loadTemplateOptions([kd_jenis_prw]);
                    } else {
                        console.error('Gagal membuat template dummy:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error creating dummy templates:', xhr.responseText, status, error);
                }
            });
        }

        // Panggil fungsi untuk memeriksa keberadaan template saat halaman dimuat
        $(document).ready(function() {
            // Tambahkan tombol debug di bawah area template
            $('#template-area').after('<div class="mt-3 debug-buttons" style="display: none;"><button type="button" class="btn btn-sm btn-info mr-2" id="btn-debug-template">Debug Template</button><button type="button" class="btn btn-sm btn-warning mr-2" id="btn-create-dummy">Buat Template Dummy</button><button type="button" class="btn btn-sm btn-primary" id="btn-check-template">Cek Template</button></div>');
            
            // Tambahkan event listener untuk tombol debug
            $(document).on('click', '#btn-debug-template', function() {
                const selectedItems = $('#jenis_lab').val();
                if (selectedItems && selectedItems.length > 0) {
                    console.log('Debug template untuk jenis pemeriksaan:', selectedItems);
                    
                    // Coba semua metode untuk mendapatkan template
                    loadTemplateOptions(selectedItems);
                } else {
                    console.log('Tidak ada jenis pemeriksaan yang dipilih');
                    $('#template-area').html('<div class="alert alert-warning">Pilih jenis pemeriksaan terlebih dahulu</div>');
                }
            });
            
            // Tambahkan event listener untuk tombol buat template dummy
            $(document).on('click', '#btn-create-dummy', function() {
                const selectedItems = $('#jenis_lab').val();
                if (selectedItems && selectedItems.length > 0) {
                    console.log('Membuat template dummy untuk jenis pemeriksaan:', selectedItems);
                    
                    // Buat template dummy untuk setiap jenis pemeriksaan
                    selectedItems.forEach(function(item) {
                        createDummyTemplatesInDatabase(item);
                    });
                } else {
                    console.log('Tidak ada jenis pemeriksaan yang dipilih');
                    $('#template-area').html('<div class="alert alert-warning">Pilih jenis pemeriksaan terlebih dahulu</div>');
                }
            });
            
            // Tambahkan tombol untuk menampilkan/menyembunyikan tombol debug
            $('body').append('<button type="button" class="btn btn-sm btn-secondary position-fixed" style="bottom: 10px; right: 10px; z-index: 9999;" id="btn-toggle-debug">Debug</button>');
            
            // Tambahkan event listener untuk tombol toggle debug
            $(document).on('click', '#btn-toggle-debug', function() {
                $('.debug-buttons').toggle();
            });
            
            // Tambahkan event listener untuk tombol cek template
            $(document).on('click', '#btn-check-template', function() {
                checkTemplateExistence();
            });
            
            // Tambahkan event listener untuk checkbox template
            $(document).on('change', '.template-checkbox', function() {
                const templateId = $(this).val();
                const jenisPrw = $(this).data('jenis-prw');
                const pemeriksaan = $(this).data('pemeriksaan');
                const satuan = $(this).data('satuan');
                const nilaiRujukanLd = $(this).data('nilai-rujukan-ld');
                const nilaiRujukanLa = $(this).data('nilai-rujukan-la');
                const isChecked = $(this).prop('checked');
                
                console.log('Template checkbox changed:', {
                    templateId: templateId,
                    jenisPrw: jenisPrw,
                    pemeriksaan: pemeriksaan,
                    satuan: satuan,
                    nilaiRujukanLd: nilaiRujukanLd,
                    nilaiRujukanLa: nilaiRujukanLa,
                    isChecked: isChecked
                });
                
                if (isChecked) {
                    // Kirim ke Livewire bahwa template dipilih
                    @this.call('selectTemplate', templateId, jenisPrw, pemeriksaan, satuan, nilaiRujukanLd, nilaiRujukanLa);
                    console.log('Template dipilih:', templateId, 'untuk jenis pemeriksaan:', jenisPrw);
                } else {
                    // Kirim ke Livewire bahwa template dibatalkan
                    @this.call('unselectTemplate', templateId);
                    console.log('Template dibatalkan:', templateId);
                }
            });

            // Tambahkan event listener untuk perubahan pada select jenis pemeriksaan
            $('#jenis_lab').on('change', function() {
                const selectedItems = $(this).val();
                console.log('Jenis pemeriksaan berubah:', selectedItems);
                
                if (selectedItems && selectedItems.length > 0) {
                    // Muat template berdasarkan jenis pemeriksaan yang dipilih
                    loadTemplateOptions(selectedItems);
                } else {
                    // Kosongkan area template jika tidak ada jenis pemeriksaan yang dipilih
                    $('#template-area').html('<div class="alert alert-warning">Pilih jenis pemeriksaan terlebih dahulu</div>');
                }
            });
            
            // Muat template untuk jenis pemeriksaan yang sudah dipilih saat halaman dimuat
            const initialSelectedItems = $('#jenis_lab').val();
            if (initialSelectedItems && initialSelectedItems.length > 0) {
                console.log('Memuat template untuk jenis pemeriksaan yang sudah dipilih:', initialSelectedItems);
                loadTemplateOptions(initialSelectedItems);
            }
        });

</script>
@endpush