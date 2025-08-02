<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 text-center"><i class="fas fa-user-plus mr-2"></i>Formulir Pendaftaran Pasien</h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent='simpan'>
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                        <label for="no_rm" class="font-weight-bold text-primary">Cari Nama</label>
                        <div class="d-flex align-items-center">
                            <div class="icon-container mr-2">
                                <span class="icon-circle bg-primary">
                                    <i class="fas fa-id-card text-white"></i>
                                </span>
                            </div>
                            <select id="no_rm" class="form-control form-control-lg select2-rm" type="text" name="no_rm">
                            </select>
                        </div>
                        @error('no_rkm_medis') <span class="text-danger">{{ $message }}</span> @enderror
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
                            <select id="status" class="form-control form-control-lg" name="status" wire:model='status'>
                                <option value="">Pilih Status</option>
                                <option value="Baru">Baru</option>
                                <option value="Lama">Lama</option>
                            </select>
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
    document.addEventListener('livewire:load', function () {
        // Tangani error Livewire
        Livewire.hook('message.failed', (message, component) => {
            console.log('Message failed:', message);
            
            if (message.response && message.response.includes('This page has expired')) {
                // Jika session expired, refresh halaman
                Swal.fire({
                    title: 'Sesi Telah Berakhir',
                    text: 'Halaman akan dimuat ulang untuk memperbarui sesi.',
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Muat Ulang'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        // Inisialisasi Select2 untuk pencarian pasien dengan mode sederhana
        $('#no_rm').select2({
            placeholder: 'Cari Nama / No. KTP Pasien',
            ajax: {
                url: '{{ url('/api/pasien') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        limit: 5
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items || data,
                        pagination: {
                            more: (params.page * 5) < (data.total_count || data.length)
                        }
                    };
                },
                cache: true
            },
            theme: 'bootstrap4',
            allowClear: true,
            minimumInputLength: 3,
            dropdownParent: $('#modalPendaftaran'),
            templateResult: formatPasien,
            templateSelection: formatPasienSelection,
            width: '100%',
            escapeMarkup: function (markup) { return markup; },
            dropdownCssClass: 'select2-dropdown-custom'
        });
        
        // Inisialisasi Select2 untuk pencarian dokter dengan mode sederhana
        $('#dokter').select2({
            placeholder: 'Cari Nama Dokter',
            ajax: {
                url: '{{ route('dokter') }}',
                dataType: 'json',
                delay: 350,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                        limit: 5
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items || data,
                        pagination: {
                            more: (params.page * 5) < (data.total_count || data.length)
                        }
                    };
                },
                cache: true
            },
            theme: 'bootstrap4',
            allowClear: true,
            minimumInputLength: 3,
            dropdownParent: $('#modalPendaftaran'),
            templateResult: formatDokter,
            templateSelection: formatDokterSelection,
            width: '100%',
            escapeMarkup: function (markup) { return markup; },
            dropdownCssClass: 'select2-dropdown-custom'
        });
        
        // Event handlers dengan mode sederhana
        $('#no_rm').on('select2:select', function(e) {
            var data = e.params.data;
            @this.set('no_rkm_medis', data.id);
        });
        
        $('#dokter').on('select2:select', function(e) {
            var data = e.params.data;
            @this.set('dokter', data.id);
        });
        
        $('#modalPendaftaran').on('shown.bs.modal', function() {
            $(this).find('.modal-dialog').css({
                'max-width': '800px',
                'width': '95%'
            });
            
            var date = moment().format('YYYY-MM-DD HH:mm:ss');
            @this.set('tgl_registrasi', date);
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
        
        function formatPasien(pasien) {
            if (!pasien.id) {
                return pasien.text;
            }
            
            var $pasien = $(
                '<div class="select2-result-pasien animate__animated animate__fadeIn">' +
                '<div class="select2-result-pasien__icon"><i class="fas fa-user-circle"></i></div>' +
                '<div class="select2-result-pasien__meta">' +
                '<div class="select2-result-pasien__title">' + pasien.text + '</div>' +
                (pasien.kelurahanpj ? '<div class="select2-result-pasien__kelurahan"><i class="fas fa-map-marker-alt mr-1"></i> Kelurahan: ' + pasien.kelurahanpj + '</div>' : '') +
                '</div>' +
                '</div>'
            );
            
            return $pasien;
        }
        
        function formatPasienSelection(pasien) {
            return pasien.text || pasien.id;
        }
        
        function formatDokter(dokter) {
            if (!dokter.id) {
                return dokter.text;
            }
            
            var $dokter = $(
                '<div class="select2-result-dokter animate__animated animate__fadeIn">' +
                '<div class="select2-result-dokter__icon"><i class="fas fa-user-md"></i></div>' +
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
    });
</script>
@endpush

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
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

    .card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
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

    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
        border-color: #d1d3e2;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2.5 !important;
        padding-left: 0.75rem;
        color: #6e707e;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 2.5rem !important;
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

    /* Styling untuk dropdown hasil pencarian pasien */
    .select2-container--open .select2-dropdown {
        border-color: #4e73df;
    }

    /* Mengubah warna teks pada dropdown hasil pencarian menjadi putih */
    .select2-results__option--highlighted[aria-selected] {
        background-color: #4e73df !important;
        color: white !important;
    }

    /* Styling untuk item yang dipilih */
    .select2-results__option[aria-selected=true] {
        background-color: #4e73df !important;
        color: white !important;
    }

    /* Styling untuk teks pada dropdown dengan background biru */
    .select2-container--bootstrap4 .select2-results__option--highlighted,
    .select2-container--bootstrap4 .select2-results__option--highlighted.select2-results__option[aria-selected=true] {
        background-color: #4e73df;
        color: white !important;
    }

    /* Memastikan teks pada dropdown dengan background biru terlihat jelas */
    .select2-results__option {
        padding: 8px 12px;
    }

    /* Styling khusus untuk dropdown pasien dengan background biru */
    .select2-result-pasien__title,
    .select2-result-pasien__kelurahan,
    .select2-result-dokter__title {
        color: inherit;
    }

    /* Memastikan ikon pada dropdown dengan background biru juga terlihat */
    .select2-results__option--highlighted .select2-result-pasien__icon i,
    .select2-results__option--highlighted .select2-result-dokter__icon i,
    .select2-results__option[aria-selected=true] .select2-result-pasien__icon i,
    .select2-results__option[aria-selected=true] .select2-result-dokter__icon i {
        color: white !important;
    }

    /* Memastikan ikon pada dropdown dengan background biru juga terlihat */
    .select2-results__option--highlighted .fas,
    .select2-results__option[aria-selected=true] .fas {
        color: white !important;
    }

    /* Styling khusus untuk dropdown custom */
    .select2-dropdown-custom .select2-results__option {
        color: #333;
    }

    .select2-dropdown-custom .select2-results__option--highlighted[aria-selected],
    .select2-dropdown-custom .select2-results__option[aria-selected=true] {
        background-color: #4e73df !important;
        color: white !important;
    }

    .select2-dropdown-custom .select2-results__option--highlighted .select2-result-pasien__title,
    .select2-dropdown-custom .select2-results__option--highlighted .select2-result-pasien__kelurahan,
    .select2-dropdown-custom .select2-results__option--highlighted .select2-result-dokter__title,
    .select2-dropdown-custom .select2-results__option[aria-selected=true] .select2-result-pasien__title,
    .select2-dropdown-custom .select2-results__option[aria-selected=true] .select2-result-pasien__kelurahan,
    .select2-dropdown-custom .select2-results__option[aria-selected=true] .select2-result-dokter__title {
        color: white !important;
    }

    .select2-dropdown-custom .select2-results__option--highlighted .fas,
    .select2-dropdown-custom .select2-results__option[aria-selected=true] .fas {
        color: white !important;
    }

    /* Styling untuk dropdown yang sudah terbuka dengan background biru */
    .select2-container--bootstrap4 .select2-dropdown {
        border-radius: 5px;
        overflow: hidden;
    }

    /* Styling untuk hasil pencarian dengan background biru */
    .select2-container--bootstrap4 .select2-results {
        padding: 0;
    }

    /* Styling untuk item hasil pencarian dengan background biru */
    .select2-container--bootstrap4 .select2-results__options {
        max-height: 250px;
        overflow-y: auto;
    }

    /* Styling untuk item hasil pencarian dengan background biru saat di-hover */
    .select2-container--bootstrap4 .select2-results__option:hover {
        background-color: #eaecf4;
    }

    /* Styling untuk item hasil pencarian dengan background biru saat dipilih */
    .select2-container--bootstrap4 .select2-results__option[aria-selected=true]:hover {
        background-color: #4e73df !important;
        color: white !important;
    }

    /* Styling khusus untuk item aktif pada dropdown */
    .select2-results__option.select2-results__option--highlighted {
        background-color: #4e73df !important;
        color: white !important;
    }

    /* Styling untuk teks pada item aktif */
    .select2-results__option.select2-results__option--highlighted * {
        color: white !important;
    }

    /* Styling untuk dropdown dengan background biru */
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #4e73df !important;
    }

    /* Styling untuk teks pada dropdown dengan background biru */
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] * {
        color: white !important;
    }
</style>

<script>
    document.addEventListener('livewire:load', function () {
        // Handle form submission success with BPJS integration
        Livewire.on('registrationSuccess', (data) => {
            // Cek jika pasien menggunakan BPJS
            const kdPj = @this.penjab;
            const isBpjs = kdPj && (kdPj === 'A03' || kdPj === 'A14' || kdPj === 'A15' || kdPj === 'BPJ' || kdPj.toLowerCase().includes('bpjs'));
            
            let successTitle = 'Berhasil!';
            let successText = 'Registrasi berhasil disimpan dengan nomor: ' + (data.no_rawat || 'N/A');
            
            // Tambahkan informasi khusus untuk pasien BPJS
            if (isBpjs) {
                successText += '<br><br><strong>Data pasien BPJS telah dikirim ke sistem Antrian BPJS.</strong><br>';
                successText += 'Nomor antrian akan diproses secara otomatis.';
                
                // Pengiriman data ke BPJS sudah dilakukan di backend melalui metode kirimAntreanBPJS()
                console.log('BPJS patient registration completed with queue integration');
            }
            
            // Tampilkan notifikasi sukses
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: successTitle,
                    html: successText,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            } else {
                alert(successText.replace(/<br>/g, '\n').replace(/<[^>]*>/g, ''));
            }
        });
        
        // Handle form submission with BPJS check
        Livewire.on('beforeSubmit', () => {
            const kdPj = @this.penjab;
            const isBpjs = kdPj && (kdPj === 'A03' || kdPj === 'A14' || kdPj === 'A15' || kdPj === 'BPJ' || kdPj.toLowerCase().includes('bpjs'));
            
            if (isBpjs) {
                console.log('BPJS patient detected, will process queue integration');
                
                // Show loading notification for BPJS patients
                if (typeof toastr !== 'undefined') {
                    toastr.info('Pendaftaran pasien BPJS sedang diproses, mohon tunggu...');
                } else {
                    console.log('Processing BPJS patient registration...');
                }
            }
        });
    });
</script>

@endpush