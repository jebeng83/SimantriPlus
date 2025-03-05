<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 text-center"><i class="fas fa-user-plus mr-2"></i>Formulir Pendaftaran Pasien</h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent='simpan'>
            <div class="row">
                <div class="col-md-6 animate__animated animate__fadeInLeft" style="animation-delay: 0.1s">
                    <div class="form-group">
                        <label for="tgl_registrasi" class="font-weight-bold text-primary">Tanggal Registrasi</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-calendar-alt text-white"></i>
                                </span>
                            </div>
                            <x-ui.input-datetime id="tgl_registrasi" model='tgl_registrasi' class="form-control-lg" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight" style="animation-delay: 0.1s">
                    <div wire:ignore class="form-group">
                        <label for="no_rm" class="font-weight-bold text-primary">No. KTP</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-id-card text-white"></i>
                                </span>
                            </div>
                            <select id="no_rm" class="form-control form-control-lg select2-rm" type="text" name="no_rm">
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s">
                    <div class="form-group">
                        <label for="pj" class="font-weight-bold text-primary">Penanggung Jawab</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-user text-white"></i>
                                </span>
                            </div>
                            <input id="pj" class="form-control form-control-lg" type="text" name="pj" wire:model='pj'>
                        </div>
                        @error('pj') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight" style="animation-delay: 0.2s">
                    <div class="form-group">
                        <label for="hubungan_pj" class="font-weight-bold text-primary">Hubungan PJ</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-people-arrows text-white"></i>
                                </span>
                            </div>
                            <input id="hubungan_pj" class="form-control form-control-lg" type="text" name="hubungan_pj"
                                wire:model='hubungan_pj'>
                        </div>
                        @error('hubungan_pj') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <div class="form-group">
                        <label for="alamat_pj" class="font-weight-bold text-primary">Alamat PJ</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-map-marker-alt text-white"></i>
                                </span>
                            </div>
                            <input id="alamat_pj" class="form-control form-control-lg" type="text" name="alamat_pj"
                                wire:model='alamat_pj'>
                        </div>
                        @error('alamat_pj') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 animate__animated animate__fadeInLeft" style="animation-delay: 0.4s">
                    <div class="form-group">
                        <label for="status" class="font-weight-bold text-primary">Status</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-info-circle text-white"></i>
                                </span>
                            </div>
                            <input id="status" class="form-control form-control-lg" type="text" name="status"
                                wire:model='status'>
                        </div>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight" style="animation-delay: 0.4s">
                    <div class="form-group">
                        <label for="penjab" class="font-weight-bold text-primary">Penjab</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-hand-holding-usd text-white"></i>
                                </span>
                            </div>
                            <select id="penjab" class="form-control form-control-lg" name="penjab" wire:model='penjab'>
                                <option value="">Pilih Penjab</option>
                                @foreach($listPenjab as $penjab)
                                <option value="{{$penjab->kd_pj}}">{{$penjab->png_jawab}}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('penjab') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 animate__animated animate__fadeInLeft" style="animation-delay: 0.5s">
                    <div wire:ignore class="form-group">
                        <label for="dokter" class="font-weight-bold text-primary">Dokter</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-user-md text-white"></i>
                                </span>
                            </div>
                            <select id="dokter" class="form-control form-control-lg" type="text" name="dokter">
                            </select>
                        </div>
                        @error('dokter') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight" style="animation-delay: 0.5s">
                    <div class="form-group">
                        <label for="kd_poli" class="font-weight-bold text-primary">Unit</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-hospital text-white"></i>
                                </span>
                            </div>
                            <select id="kd_poli" class="form-control form-control-lg" name="kd_poli"
                                wire:model='kd_poli'>
                                <option value="">Pilih Unit</option>
                                @foreach($poliklinik as $poli)
                                <option value="{{$poli->kd_poli}}">{{$poli->nm_poli}}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('kd_poli') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 text-right animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                    <button type="button" class="btn btn-secondary btn-lg mr-2"
                        wire:click="$emit('closeModalPendaftaran')">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
    // Fungsi untuk mengoptimalkan performa Select2
    function optimizeSelect2Performance() {
        // Batasi frekuensi event scroll untuk mengurangi beban CPU
        let scrollTimeout;
        $('.select2-results__options').on('scroll', function() {
            if (!scrollTimeout) {
                scrollTimeout = setTimeout(function() {
                    scrollTimeout = null;
                }, 100);
            }
        });
        
        // Gunakan IntersectionObserver untuk lazy-load item yang terlihat
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            setTimeout(() => {
                document.querySelectorAll('.select2-result-pasien, .select2-result-dokter').forEach(el => {
                    observer.observe(el);
                });
            }, 500);
        }
    }

    $(document).ready(function() {
        // Fungsi untuk animasi elemen form
        function animateFormElements() {
            // Reset animasi terlebih dahulu
            $('.animate__animated').removeClass('animate__fadeInLeft animate__fadeInRight animate__fadeInUp')
                .css('opacity', 0);
            
            // Terapkan animasi dengan delay bertahap
            setTimeout(function() {
                $('.animate__animated').each(function(index) {
                    const $this = $(this);
                    const delay = index * 100;
                    
                    setTimeout(function() {
                        if ($this.hasClass('col-md-6') && index % 2 === 0) {
                            $this.addClass('animate__fadeInLeft');
                        } else if ($this.hasClass('col-md-6')) {
                            $this.addClass('animate__fadeInRight');
                        } else {
                            $this.addClass('animate__fadeInUp');
                        }
                        $this.css('opacity', 1);
                    }, delay);
                });
            }, 100);
        }
        
        // Fungsi debounce untuk membatasi frekuensi permintaan AJAX
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Cache untuk menyimpan hasil pencarian
        const searchCache = {
            pasien: {},
            dokter: {}
        };
        
        // Preload data saat halaman dimuat
        function preloadData() {
            // Preload data pasien populer
            $.ajax({
                url: '{{ url('/api/pasien') }}',
                data: { q: '', preload: true, limit: 10 },
                dataType: 'json',
                cache: true
            });
            
            // Preload data dokter
            $.ajax({
                url: '{{ route('dokter') }}',
                data: { q: '', preload: true, limit: 10 },
                dataType: 'json',
                cache: true
            });
        }
        
        // Panggil preload data segera
        preloadData();

        // Inisialisasi Select2 untuk pencarian pasien dengan optimasi
        $('#no_rm').select2({
            placeholder: 'Cari Nama / No. KTP Pasien',
            ajax: {
                url: '{{ url('/api/pasien') }}',
                dataType: 'json',
                delay: 250,
                minimalTextLength: 3,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        limit: 5
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    
                    // Simpan hasil ke cache
                    if (params.term) {
                        searchCache.pasien[params.term] = data;
                    }
                    
                    return {
                        results: data.items || data,
                        pagination: {
                            more: (params.page * 5) < (data.total_count || data.length)
                        }
                    };
                },
                transport: function(params, success, failure) {
                    // Cek cache terlebih dahulu
                    const term = params.data.q;
                    if (term && searchCache.pasien[term]) {
                        return success(searchCache.pasien[term]);
                    }
                    
                    // Jika tidak ada di cache, lakukan request AJAX
                    const $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    
                    return $request;
                },
                cache: true,
                language: {
                    searching: function() {
                        return '<div class="d-flex align-items-center justify-content-center py-2"><div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div><span>Mencari data pasien...</span></div>';
                    },
                    inputTooShort: function() {
                        return '<div class="text-center py-2"><i class="fas fa-info-circle text-info mr-1"></i> Masukkan minimal 3 karakter untuk mencari</div>';
                    },
                    noResults: function() {
                        return '<div class="text-center py-3"><i class="fas fa-search text-muted mr-1"></i> Tidak ada hasil yang ditemukan</div>';
                    },
                    loadingMore: function() {
                        return '<div class="d-flex align-items-center justify-content-center py-2"><div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div><span>Memuat data tambahan...</span></div>';
                    }
                }
            },
            theme: 'bootstrap4',
            allowClear: true,
            minimumInputLength: 3,
            dropdownParent: $('#modalPendaftaran'),
            templateResult: formatPasien,
            templateSelection: formatPasienSelection,
            width: '100%',
            escapeMarkup: function (markup) { return markup; }
        });

        function formatPasien(pasien) {
            if (!pasien.id) {
                return pasien.text;
            }
            
            // Tambahkan animasi dan styling yang lebih baik
            var $pasien = $(
                '<div class="select2-result-pasien animate__animated animate__fadeIn">' +
                '<div class="select2-result-pasien__icon"><i class="fas fa-user-circle text-primary"></i></div>' +
                '<div class="select2-result-pasien__meta">' +
                '<div class="select2-result-pasien__title">' + pasien.text + '</div>' +
                (pasien.kelurahanpj ? '<div class="select2-result-pasien__kelurahan"><i class="fas fa-map-marker-alt text-info mr-1"></i> Kelurahan: ' + pasien.kelurahanpj + '</div>' : '') +
                '</div>' +
                '</div>'
            );
            
            return $pasien;
        }

        function formatPasienSelection(pasien) {
            return pasien.text || pasien.id;
        }

        $('#no_rm').on('select2:select', function(e) {
            var data = e.params.data;
            @this.set('no_rkm_medis', data.id);
        });
        
        // Event handler untuk optimasi saat dropdown dibuka
        $('#no_rm').on('select2:open', function() {
            setTimeout(optimizeSelect2Performance, 100);
        });

        // Inisialisasi Select2 untuk pencarian dokter dengan optimasi
        $('#dokter').select2({
            placeholder: 'Cari Nama Dokter',
            ajax: {
                url: '{{ route('dokter') }}',
                dataType: 'json',
                delay: 350, // Meningkatkan delay untuk mengurangi jumlah request
                minimalTextLength: 3,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        limit: 5
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    
                    // Simpan hasil ke cache
                    if (params.term) {
                        searchCache.dokter[params.term] = data;
                    }
                    
                    return {
                        results: data.items || data,
                        pagination: {
                            more: (params.page * 5) < (data.total_count || data.length)
                        }
                    };
                },
                transport: function(params, success, failure) {
                    // Cek cache terlebih dahulu
                    const term = params.data.q;
                    if (term && searchCache.dokter[term]) {
                        return success(searchCache.dokter[term]);
                    }
                    
                    // Jika tidak ada di cache, lakukan request AJAX
                    const $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    
                    return $request;
                },
                cache: true,
                language: {
                    searching: function() {
                        return '<div class="d-flex align-items-center justify-content-center py-2"><div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div><span>Mencari data dokter...</span></div>';
                    },
                    inputTooShort: function() {
                        return '<div class="text-center py-2"><i class="fas fa-info-circle text-info mr-1"></i> Masukkan minimal 3 karakter untuk mencari</div>';
                    },
                    noResults: function() {
                        return '<div class="text-center py-3"><i class="fas fa-search text-muted mr-1"></i> Tidak ada hasil yang ditemukan</div>';
                    },
                    loadingMore: function() {
                        return '<div class="d-flex align-items-center justify-content-center py-2"><div class="spinner-border spinner-border-sm text-primary mr-2" role="status"></div><span>Memuat data tambahan...</span></div>';
                    }
                }
            },
            theme: 'bootstrap4',
            allowClear: true,
            minimumInputLength: 3,
            dropdownParent: $('#modalPendaftaran'),
            templateResult: formatDokter,
            templateSelection: formatDokterSelection,
            width: '100%',
            escapeMarkup: function (markup) { return markup; }
        });

        function formatDokter(dokter) {
            if (!dokter.id) {
                return dokter.text;
            }
            
            var $dokter = $(
                '<div class="select2-result-dokter animate__animated animate__fadeIn">' +
                '<div class="select2-result-dokter__icon"><i class="fas fa-user-md text-primary"></i></div>' +
                '<div class="select2-result-dokter__meta">' +
                '<div class="select2-result-dokter__title">' + dokter.text + '</div>' +
                '</div>' +
                '</div>'
            );
            
            return $dokter;
        }

        function formatDokterSelection(dokter) {
            return dokter.text || dokter.id;
        }

        $('#dokter').on('select2:select', function(e) {
            var data = e.params.data;
            @this.set('dokter', data.id);
        });
        
        // Event handler untuk optimasi saat dropdown dibuka
        $('#dokter').on('select2:open', function() {
            setTimeout(optimizeSelect2Performance, 100);
        });

        // Preload data saat modal dibuka
        function preloadSelect2Data() {
            // Preload data pasien populer
            $.ajax({
                url: '{{ url('/api/pasien') }}',
                data: { q: '', preload: true, limit: 10 },
                dataType: 'json',
                cache: true
            });
            
            // Preload data dokter
            $.ajax({
                url: '{{ route('dokter') }}',
                data: { q: '', preload: true, limit: 10 },
                dataType: 'json',
                cache: true
            });
        }

        $('#modalPendaftaran').on('shown.bs.modal', function() {
            $(this).find('.modal-dialog').css({
                'max-width': '800px',
                'width': '95%'
            });
            
            var date = moment().format('YYYY-MM-DD HH:mm:ss');
            @this.set('tgl_registrasi', date);
            
            // Preload data untuk Select2
            preloadSelect2Data();
            
            // Inisialisasi Select2 dengan animasi
            setTimeout(function() {
                $('#no_rm').select2('open');
                $('#no_rm').select2('close');
                $('#dokter').select2('open');
                $('#dokter').select2('close');
            }, 100);
            
            // Animasi elemen form
            animateFormElements();
        });

        $('#modalPendaftaran').on('hidden.bs.modal', function() {
            $('#no_rm').val(null).trigger('change');
            $('#dokter').val(null).trigger('change');
        });

        Livewire.on('closeModalPendaftaran', () => {
            $('#modalPendaftaran').modal('hide');
        });

        Livewire.on('openModalPendaftaran', () => {
            $('#dokter').append(new Option(@this.nm_dokter, @this.dokter, true, true)).trigger('change');
            $('#no_rm').append(new Option(@this.nm_pasien, @this.no_rkm_medis, true, true)).trigger('change');
            $('#modalPendaftaran').modal('show');
        });

        // Styling untuk Select2
        $('.select2-container--bootstrap4 .select2-selection--single').css({
            'height': 'calc(2.875rem + 2px)',
            'border-radius': '5px',
            'border-color': '#d1d3e2',
            'transition': 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out'
        });
        
        $('.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered').css({
            'line-height': '2.5',
            'padding-left': '0.75rem',
            'color': '#6e707e'
        });
        
        $('.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow').css('height', '2.5rem');
        
        // Tambahkan event listener untuk fokus pada Select2
        $(document).on('focus', '.select2-selection', function() {
            $(this).css({
                'border-color': '#4e73df',
                'box-shadow': '0 0 0 0.2rem rgba(78, 115, 223, 0.25)'
            });
        });
        
        $(document).on('blur', '.select2-selection', function() {
            $(this).css({
                'border-color': '#d1d3e2',
                'box-shadow': 'none'
            });
        });
        
        // Optimasi untuk input lainnya
        $('input, select').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });
        
        // Tambahkan efek ripple pada tombol
        $('.btn').on('mousedown', function(e) {
            const $btn = $(this);
            const offset = $btn.offset();
            const x = e.pageX - offset.left;
            const y = e.pageY - offset.top;
            
            const $ripple = $('<span class="btn-ripple"></span>');
            $ripple.css({
                top: y,
                left: x
            });
            
            $btn.append($ripple);
            
            setTimeout(function() {
                $ripple.remove();
            }, 700);
        });
        
        // Tambahkan animasi hover pada form group
        $('.form-group').hover(
            function() {
                $(this).addClass('form-group-hover');
            },
            function() {
                $(this).removeClass('form-group-hover');
            }
        );
    });
</script>
@endpush

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* Animasi untuk hasil pencarian */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate__animated {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }

    .animate__fadeIn {
        animation-name: fadeIn;
    }

    .animate__fadeInLeft {
        animation-name: fadeInLeft;
    }

    .animate__fadeInRight {
        animation-name: fadeInRight;
    }

    .animate__fadeInUp {
        animation-name: fadeInUp;
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translate3d(-30px, 0, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translate3d(30px, 0, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 30px, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }

    /* Efek ripple untuk tombol */
    .btn {
        position: relative;
        overflow: hidden;
    }

    .btn-ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        width: 100px;
        height: 100px;
        margin-top: -50px;
        margin-left: -50px;
        animation: ripple 0.7s linear infinite;
        transform: scale(0);
        opacity: 1;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(3);
            opacity: 0;
        }
    }

    /* Efek fokus untuk form group */
    .form-group.focused label {
        color: #4e73df;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }

    /* Efek hover untuk form group */
    .form-group {
        transition: all 0.3s ease;
        border-radius: 5px;
    }

    .form-group-hover {
        background-color: rgba(78, 115, 223, 0.03);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    /* Animasi untuk icon */
    .icon-circle {
        transition: all 0.3s ease;
    }

    .form-group:hover .icon-circle {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        width: 100%;
    }

    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        border: none;
    }

    .modal-header {
        background-color: #4e73df;
        color: white;
        border-bottom: 0;
        padding: 1rem 1.5rem;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-body {
        padding: 0;
    }

    .card-header {
        border-bottom: 0;
        padding: 1rem 1.5rem;
        background-color: #4e73df !important;
    }

    .card-body {
        padding: 1.5rem;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #d1d3e2;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .form-control-lg {
        font-size: 1rem;
        height: calc(2.875rem + 2px);
    }

    .btn {
        border-radius: 5px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.15);
    }

    .bg-primary {
        background-color: #4e73df !important;
    }

    label.font-weight-bold {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
        border-color: #d1d3e2;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-container--bootstrap4 .select2-selection--single:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2.5 !important;
        padding-left: 0.75rem;
        color: #6e707e;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 2.5rem !important;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: rgba(78, 115, 223, 0.1) !important;
        color: #333 !important;
        border-left: 3px solid #4e73df;
        transition: all 0.2s ease;
    }

    .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background-color: rgba(78, 115, 223, 0.2) !important;
        color: #333 !important;
        border-left: 3px solid #4e73df;
    }

    .select2-result-pasien,
    .select2-result-dokter {
        display: flex;
        align-items: center;
        padding: 8px 0;
        transition: all 0.2s ease;
    }

    .select2-result-pasien__icon,
    .select2-result-dokter__icon {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .select2-result-pasien__meta,
    .select2-result-dokter__meta {
        flex: 1;
    }

    .select2-result-pasien__title,
    .select2-result-dokter__title {
        font-weight: bold;
        color: #4e73df;
        margin-bottom: 3px;
    }

    .select2-result-pasien__kelurahan {
        font-size: 0.8rem;
        color: #858796;
    }

    .select2-container--bootstrap4 .select2-dropdown {
        border-color: #d1d3e2;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 5px;
        overflow: hidden;
        animation: fadeIn 0.3s ease-out;
    }

    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border-radius: 5px;
        border: 1px solid #d1d3e2;
        padding: 0.5rem;
        margin: 5px;
        width: calc(100% - 10px);
        transition: all 0.2s ease;
    }

    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }

    .select2-container--bootstrap4 .select2-results>.select2-results__options {
        max-height: 250px;
        overflow-y: auto;
        padding: 5px;
        scrollbar-width: thin;
        scrollbar-color: #d1d3e2 #f8f9fc;
    }

    .select2-container--bootstrap4 .select2-results>.select2-results__options::-webkit-scrollbar {
        width: 6px;
    }

    .select2-container--bootstrap4 .select2-results>.select2-results__options::-webkit-scrollbar-track {
        background: #f8f9fc;
        border-radius: 10px;
    }

    .select2-container--bootstrap4 .select2-results>.select2-results__options::-webkit-scrollbar-thumb {
        background: #d1d3e2;
        border-radius: 10px;
    }

    .select2-container--bootstrap4 .select2-results>.select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #a8aab9;
    }

    .select2-container--bootstrap4 .select2-results__option {
        padding: 8px 10px;
        margin-bottom: 2px;
        border-radius: 3px;
        transition: all 0.2s ease;
    }

    .select2-container--bootstrap4 .select2-results__option:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }

    .select2-container--bootstrap4 .select2-results__message {
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 5px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .icon-circle:hover {
        transform: scale(1.05);
    }

    @media (min-width: 768px) {
        .modal-dialog {
            max-width: 800px;
            margin: 1.75rem auto;
        }
    }
</style>
@endpush