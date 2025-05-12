@extends('layouts.minimal')

@section('title', 'Form Skrining Kesehatan Sederhana')

@section('content')
<!-- Meta tag untuk CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Ganti include dengan konten yang diperlukan saja -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">FORM SKRINING KESEHATAN</h4>
    </div>
    <div class="card-body">
        <!-- DATA IDENTITAS DIRI -->
        <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">DATA IDENTITAS DIRI</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NIK</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="nik" id="nik" maxlength="25" required>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="cari-nik">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Masukkan NIK untuk mengisi data otomatis</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" maxlength="100"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Umur</label>
                        <input type="text" class="form-control" name="umur" id="umur" readonly>
                        <input type="hidden" name="umur_tahun" id="umur_tahun">
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Handphone</label>
                        <input type="text" class="form-control" name="no_handphone" id="no_handphone" maxlength="25">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pemeriksaan -->
        <div class="section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title m-0 font-weight-bold">Pemeriksaan Mandiri</h5>
                <small class="text-muted">Versi 1.0</small>
            </div>

            <div class="card">
                <div class="card-header bg-light p-2">
                    <h6 class="mb-0">
                        <button
                            class="btn btn-link btn-block text-left text-dark text-decoration-none d-flex justify-content-between align-items-center pl-2"
                            type="button">
                            <span><i class="fas fa-chevron-down mr-2"></i>Jumlah Pemeriksaan (8/8)</span>
                        </button>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Layanan</th>
                                    <th class="text-center" width="120">Status</th>
                                    <th class="text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-service="demografi">
                                    <td>Demografi Dewasa Perempuan</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalDemografi">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="hati">
                                    <td>Hati</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalHati">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kanker-leher-rahim">
                                    <td>Kanker Leher Rahim</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKankerLeherRahim">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kesehatan-jiwa">
                                    <td>Kesehatan Jiwa</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKesehatanJiwa">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="perilaku-merokok">
                                    <td>Perilaku Merokok</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalPerilakuMerokok">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="tekanan-darah">
                                    <td>Tekanan Darah & Gula Darah</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalTekananDarah">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="aktivitas-fisik">
                                    <td>Tingkat Aktivitas Fisik</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalAktivitasFisik">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="tuberkulosis">
                                    <td>Tuberkulosis</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalTuberkulosis">Input Data</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="bg-light text-primary font-weight-bold">ASSESMENT KESEHATAN
                                    </td>
                                </tr>
                                <tr data-service="antropometri-lab">
                                    <td>1. Antropometri dan Laboratorium</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalAntropometriLab">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-puma">
                                    <td>2. Skrining PUMA</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningPuma">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-indra">
                                    <td>3. Skrining Indra</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningIndra">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-gigi">
                                    <td>4. Skrining Gigi</td>
                                    <td class="text-center"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningGigi">Input Data</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk semua pemeriksaan seperti di skrining-ckg.blade.php -->
<!-- Modal Demografi -->
<div class="modal fade" id="modalDemografi" tabindex="-1" role="dialog" aria-labelledby="modalDemografiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDemografiLabel">Demografi Dewasa Perempuan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.demografi')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('demografi')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hati -->
<div class="modal fade" id="modalHati" tabindex="-1" role="dialog" aria-labelledby="modalHatiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalHatiLabel">Hati</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.hati')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('hati')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kanker Leher Rahim -->
<div class="modal fade" id="modalKankerLeherRahim" tabindex="-1" role="dialog"
    aria-labelledby="modalKankerLeherRahimLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKankerLeherRahimLabel">Kanker Leher Rahim</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.kanker-leher-rahim')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('kanker-leher-rahim')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kesehatan Jiwa -->
<div class="modal fade" id="modalKesehatanJiwa" tabindex="-1" role="dialog" aria-labelledby="modalKesehatanJiwaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKesehatanJiwaLabel">Kesehatan Jiwa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.kesehatan-jiwa')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('kesehatan-jiwa')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Perilaku Merokok -->
<div class="modal fade" id="modalPerilakuMerokok" tabindex="-1" role="dialog"
    aria-labelledby="modalPerilakuMerokokLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPerilakuMerokokLabel">Perilaku Merokok</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.perilaku-merokok')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('perilaku-merokok')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tekanan Darah -->
<div class="modal fade" id="modalTekananDarah" tabindex="-1" role="dialog" aria-labelledby="modalTekananDarahLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTekananDarahLabel">Tekanan Darah & Gula Darah</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.tekanan-darah')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('tekanan-darah')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aktivitas Fisik -->
<div class="modal fade" id="modalAktivitasFisik" tabindex="-1" role="dialog" aria-labelledby="modalAktivitasFisikLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAktivitasFisikLabel">Tingkat Aktivitas Fisik</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.aktivitas-fisik')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('aktivitas-fisik')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tuberkulosis -->
<div class="modal fade" id="modalTuberkulosis" tabindex="-1" role="dialog" aria-labelledby="modalTuberkulosisLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTuberkulosisLabel">Tuberkulosis</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.tuberkulosis')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('tuberkulosis')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Antropometri dan Laboratorium -->
<div class="modal fade" id="modalAntropometriLab" tabindex="-1" role="dialog"
    aria-labelledby="modalAntropometriLabLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAntropometriLabLabel">Antropometri dan Laboratorium</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.antropometri-lab')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('antropometri-lab')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Skrining PUMA -->
<div class="modal fade" id="modalSkriningPuma" tabindex="-1" role="dialog" aria-labelledby="modalSkriningPumaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSkriningPumaLabel">Skrining PUMA</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.skrining-puma')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('skrining-puma')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Skrining Indra -->
<div class="modal fade" id="modalSkriningIndra" tabindex="-1" role="dialog" aria-labelledby="modalSkriningIndraLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSkriningIndraLabel">Skrining Indra</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.skrining-indra')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('skrining-indra')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Skrining Gigi -->
<div class="modal fade" id="modalSkriningGigi" tabindex="-1" role="dialog" aria-labelledby="modalSkriningGigiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSkriningGigiLabel">Skrining Gigi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.skrining-gigi')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('skrining-gigi')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    /* Pastikan modal bisa tampil dengan benar */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    .section-title {
        color: #0069d9;
        font-weight: bold;
    }

    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }

    /* Perbaiki radio button yang tidak bisa diklik */
    .form-check {
        position: relative;
        padding-left: 1.25rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }

    .form-check-input {
        position: absolute;
        margin-top: 0.3rem;
        margin-left: -1.25rem;
        cursor: pointer;
        z-index: 2;
    }

    .form-check-label {
        cursor: pointer;
        margin-bottom: 0;
        padding: 0.25rem 0;
        display: inline-block;
        position: relative;
        z-index: 1;
    }

    /* Pastikan area klik label lebih luas */
    label {
        display: inline-block;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }

    /* Tambahkan efek hover untuk meningkatkan UX */
    .form-check:hover {
        background-color: rgba(0, 123, 255, 0.05);
        border-radius: 4px;
    }

    /* Gaya untuk radio button yang aktif */
    .form-check-input:checked+.form-check-label {
        color: #007bff;
        font-weight: 500;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Konfigurasi AJAX global untuk menambahkan CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Fungsi untuk menghitung umur
        function hitungUmur(tanggalLahir) {
            if (!tanggalLahir) return '';
            
            var today = new Date();
            var birthDate = new Date(tanggalLahir);
            
            // Memastikan tanggal lahir valid
            if (isNaN(birthDate.getTime())) return '';
            
            // Menghitung selisih dalam milisecond
            var diffMs = today - birthDate;
            
            // Konversi ke hari
            var diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            
            // Menghitung tahun
            var years = Math.floor(diffDays / 365.25);
            
            // Sisa hari setelah menghitung tahun
            var remainingDays = diffDays - Math.floor(years * 365.25);
            
            // Menghitung bulan (approx)
            var months = Math.floor(remainingDays / 30.4375);
            
            // Sisa hari setelah menghitung bulan
            var days = Math.floor(remainingDays - (months * 30.4375));
            
            // Format result
            var result = '';
            if (years > 0) {
                result += years + ' tahun ';
            }
            if (months > 0 || years > 0) {
                result += months + ' bulan ';
            }
            result += days + ' hari';
            
            // Simpan nilai tahun di hidden field untuk database
            $('#umur_tahun').val(years);
            
            return result.trim();
        }
        
        // Event handler untuk tanggal lahir - hitung umur otomatis
        $('#tanggal_lahir').on('change', function() {
            var tanggalLahir = $(this).val();
            var umur = hitungUmur(tanggalLahir);
            $('#umur').val(umur);
        });
        
        // Validasi input numerik - pastikan selalu valid
        $(document).on('blur', 'input[type="number"]', function() {
            // Jika kosong, atur nilai menjadi null (akan dihandle dengan nilai default di server)
            if ($(this).val() === '' || $(this).val() === null) {
                $(this).val('');
            } else {
                // Jika berisi nilai tetapi bukan angka valid, atur menjadi 0
                if (isNaN(parseFloat($(this).val()))) {
                    $(this).val(0);
                }
            }
        });
        
        // Perbaikan untuk radio button yang tidak bisa diklik
        $(document).on('click', '.form-check-label', function() {
            var radioId = $(this).attr('for');
            if (radioId) {
                $('#' + radioId).prop('checked', true).trigger('change');
            }
        });
        
        // Perbaikan untuk area klik radio button
        $(document).on('click', '.form-check', function(e) {
            if (e.target === this) {
                var radio = $(this).find('input[type="radio"]');
                if (radio.length > 0) {
                    radio.prop('checked', true).trigger('change');
                }
            }
        });
        
        // Menangani klik pada tombol di tabel
        $('.btn-modal-trigger').on('click', function(e) {
            e.preventDefault();
            var targetModal = $(this).data('target');
            console.log('Tombol tabel diklik, target modal: ' + targetModal);
            $(targetModal).modal('show');
        });
        
        // Mengubah status badge menjadi hijau saat modal disimpan
        $('.modal').on('hidden.bs.modal', function (e) {
            // Dapatkan ID modal yang baru saja ditutup
            var modalId = $(this).attr('id');
            console.log('Modal ditutup: ' + modalId);
            
            // Dapatkan data-service dari tombol yang memicu modal ini
            var serviceType = modalId.replace('modal', '');
            
            // Untuk nama modal dengan format camelCase, konversi kembali ke kebab-case
            if (serviceType === 'KankerLeherRahim') {
                serviceType = 'kanker-leher-rahim';
            } else if (serviceType === 'KesehatanJiwa') {
                serviceType = 'kesehatan-jiwa';
            } else if (serviceType === 'PerilakuMerokok') {
                serviceType = 'perilaku-merokok';
            } else if (serviceType === 'TekananDarah') {
                serviceType = 'tekanan-darah';
            } else if (serviceType === 'AktivitasFisik') {
                serviceType = 'aktivitas-fisik';
            } else {
                // Konversi camelCase ke kebab-case secara umum
                serviceType = serviceType.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
            }
            
            // Ubah status badge jadi hijau
            $('tr[data-service="' + serviceType + '"] .status-check').removeClass('badge-secondary').addClass('badge-success');
        });
        
        // Pemeriksaan kondisi form kanker leher rahim - hanya tampilkan untuk jenis kelamin wanita
        function checkKankerLeherRahimForm() {
            var jenisKelamin = $('#jenis_kelamin').val();
            if (jenisKelamin === 'P') {
                $('tr[data-service="kanker-leher-rahim"]').show();
            } else {
                $('tr[data-service="kanker-leher-rahim"]').hide();
            }
        }
        
        // Panggil fungsi saat halaman dimuat dan saat jenis kelamin berubah
        checkKankerLeherRahimForm();
        $('#jenis_kelamin').on('change', function() {
            checkKankerLeherRahimForm();
        });
        
        // Fungsi untuk mencari dan mengisi data pasien berdasarkan NIK
        function cariPasienByNIK() {
            var nik = $('#nik').val();
            if (nik.length > 0) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Mencari data...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Cek dulu apakah NIK sudah ada di database skrining
                $.ajax({
                    url: "{{ route('api.skrining.cek-nik') }}",
                    type: "POST",
                    data: {
                        nik: nik
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log('Hasil cek NIK:', response);
                        
                        if (response.status === 'warning' || response.status === 'info') {
                            // NIK sudah ada di database skrining, isi form dengan data yang ada
                            var data = response.data;
                            
                            // Isi data identitas
                            $('#nama_lengkap').val(data.nama_lengkap);
                            $('#tanggal_lahir').val(data.tanggal_lahir).trigger('change');
                            $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
                            $('#no_handphone').val(data.no_handphone);
                            
                            // Isi status badge berdasarkan data yang sudah ada
                            if (data.status_perkawinan) $('tr[data-service="demografi"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.riwayat_hipertensi) $('tr[data-service="tekanan-darah"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.status_merokok) $('tr[data-service="perilaku-merokok"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.riwayat_hepatitis) $('tr[data-service="hati"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.minat) $('tr[data-service="kesehatan-jiwa"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.hubungan_intim && data.jenis_kelamin === 'P') $('tr[data-service="kanker-leher-rahim"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.frekuensi_olahraga) $('tr[data-service="aktivitas-fisik"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.batuk || data.demam) $('tr[data-service="tuberkulosis"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.tinggi_badan || data.berat_badan) $('tr[data-service="antropometri-lab"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.pendengaran) $('tr[data-service="skrining-indra"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            if (data.karies) $('tr[data-service="skrining-gigi"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Data ditemukan',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            // NIK belum ada di database skrining, cari di database pasien
                            $.ajax({
                                url: "{{ route('pasien.get-by-nik') }}",
                                type: "GET",
                                data: {
                                    nik: nik
                                },
                                dataType: "json",
                                success: function(responsePasien) {
                                    if (responsePasien.status == 'success') {
                                        var pasien = responsePasien.data;
                                        $('#nama_lengkap').val(pasien.nm_pasien);
                                        $('#tanggal_lahir').val(pasien.tgl_lahir).trigger('change');
                                        $('#jenis_kelamin').val(pasien.jk).trigger('change');
                                        $('#no_handphone').val(pasien.no_tlp);
                                        
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Data ditemukan',
                                            text: 'Data pasien berhasil ditemukan',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Data tidak ditemukan',
                                            text: 'Data pasien tidak ditemukan, silahkan isi form secara manual',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Terjadi kesalahan',
                                        text: 'Gagal menghubungi server: ' + error,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tutup loading
                        Swal.close();
                        
                        // Tampilkan pesan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Gagal memeriksa NIK: ' + error,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }
        }
        
        // Event saat tombol cari NIK diklik
        $('#cari-nik').on('click', function() {
            cariPasienByNIK();
        });
        
        // Event saat enter di input NIK
        $('#nik').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                cariPasienByNIK();
            }
        });
        
        // Memeriksa NIK otomatis saat halaman dimuat jika ada NIK di URL
        var urlParams = new URLSearchParams(window.location.search);
        var nikFromUrl = urlParams.get('nik');
        if (nikFromUrl) {
            $('#nik').val(nikFromUrl);
            setTimeout(function() {
                cariPasienByNIK();
            }, 500);
        }
        
        // Hitung umur saat halaman dimuat jika tanggal lahir sudah ada
        if ($('#tanggal_lahir').val()) {
            var umur = hitungUmur($('#tanggal_lahir').val());
            $('#umur').val(umur);
        }
        
        // Fungsi untuk menyimpan data form ke database
        function simpanDataForm(formType) {
            var formData = {};
            var url = '';
            var nik = $('#nik').val();
            
            if (!nik) {
                Swal.fire({
                    icon: 'error',
                    title: 'NIK diperlukan',
                    text: 'Harap isi NIK terlebih dahulu sebelum menyimpan data',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Tambahkan NIK ke formData
            formData.nik = nik;
            
            // Tambahkan data lain berdasarkan tipe form
            switch(formType) {
                case 'demografi':
                    url = "{{ route('api.skrining.demografi') }}";
                    formData.no_rkm_medis = $('#no_rkm_medis').val();
                    formData.pendidikan = $('input[name="pendidikan"]:checked').val();
                    formData.pekerjaan = $('input[name="pekerjaan"]:checked').val();
                    formData.status_perkawinan = $('input[name="status_perkawinan"]:checked').val() || $('input[name="status_menikah"]:checked').val();
                    formData.rencana_menikah = $('input[name="rencana_menikah"]:checked').val();
                    formData.status_hamil = $('input[name="status_hamil"]:checked').val();
                    formData.status_disabilitas = $('input[name="status_disabilitas"]:checked').val();
                    formData.jumlah_anak = $('input[name="jumlah_anak"]').val();
                    formData.alamat = $('textarea[name="alamat"]').val();
                    break;
                
                case 'tekanan-darah':
                    url = "{{ route('api.skrining.tekanan-darah') }}";
                    formData.riwayat_hipertensi = $('input[name="riwayat_hipertensi"]:checked').val();
                    formData.riwayat_diabetes = $('input[name="riwayat_diabetes"]:checked').val();
                    break;
                
                case 'perilaku-merokok':
                    url = "{{ route('api.skrining.perilaku-merokok') }}";
                    formData.status_merokok = $('input[name="status_merokok"]:checked').val();
                    if (formData.status_merokok === 'Ya') {
                        formData.lama_merokok = $('input[name="lama_merokok"]').val();
                        formData.jumlah_rokok = $('input[name="jumlah_rokok"]').val();
                    }
                    formData.paparan_asap = $('input[name="paparan_asap"]:checked').val();
                    break;
                
                case 'hati':
                    url = "{{ route('api.skrining.hati') }}";
                    formData.riwayat_hepatitis = $('input[name="riwayat_hepatitis"]:checked').val();
                    formData.riwayat_kuning = $('input[name="riwayat_kuning"]:checked').val();
                    formData.hubungan_intim = $('input[name="hubungan_intim"]:checked').val();
                    formData.riwayat_transfusi = $('input[name="riwayat_transfusi"]:checked').val();
                    formData.riwayat_tindik = $('input[name="riwayat_tindik"]:checked').val();
                    formData.narkoba_suntik = $('input[name="narkoba_suntik"]:checked').val();
                    formData.odhiv = $('input[name="odhiv"]:checked').val();
                    formData.riwayat_tattoo = $('input[name="riwayat_tattoo"]:checked').val();
                    formData.kolesterol = $('input[name="kolesterol"]:checked').val();
                    break;
                
                case 'kesehatan-jiwa':
                    url = "{{ route('api.skrining.kesehatan-jiwa') }}";
                    // Ambil data minat, sedih, cemas, khawatir
                    formData.minat = $('input[name="minat"]:checked').val();
                    formData.sedih = $('input[name="sedih"]:checked').val();
                    formData.cemas = $('input[name="cemas"]:checked').val();
                    formData.khawatir = $('input[name="khawatir"]:checked').val();
                    break;
                
                case 'kanker-leher-rahim':
                    url = "{{ route('api.skrining.kanker-leher-rahim') }}";
                    formData.hubungan_intim = $('input[name="hubungan_intim"]:checked').val();
                    break;
                
                case 'aktivitas-fisik':
                    url = "{{ route('api.skrining.aktivitas-fisik') }}";
                    formData.frekuensi_olahraga = $('input[name="frekuensi_olahraga"]:checked').val();
                    formData.durasi_olahraga = $('input[name="durasi_olahraga"]:checked').val();
                    break;
                
                case 'tuberkulosis':
                    url = "{{ route('api.skrining.tuberkulosis') }}";
                    formData.batuk_berdahak = $('input[name="batuk_berdahak"]:checked').val();
                    formData.demam = $('input[name="demam"]:checked').val();
                    break;
                
                case 'antropometri-lab':
                    url = "{{ route('api.skrining.antropometri-lab') }}";
                    // Untuk nilai numerik, pastikan selalu bernilai valid (null atau angka)
                    formData.tinggi_badan = $('input[name="tinggi_badan"]').val() || null;
                    formData.berat_badan = $('input[name="berat_badan"]').val() || null;
                    formData.lingkar_perut = $('input[name="lingkar_perut"]').val() || null;
                    formData.tekanan_sistolik = $('input[name="tekanan_sistolik"]').val() || null;
                    formData.tekanan_diastolik = $('input[name="tekanan_diastolik"]').val() || null;
                    
                    // Untuk data laboratorium, nilai default 0 jika kosong
                    var gdsVal = $('input[name="gds"]').val();
                    formData.gds = (gdsVal === '' || gdsVal === null || isNaN(parseFloat(gdsVal))) ? 0 : parseFloat(gdsVal);
                    
                    var gdpVal = $('input[name="gdp"]').val();
                    formData.gdp = (gdpVal === '' || gdpVal === null || isNaN(parseFloat(gdpVal))) ? 0 : parseFloat(gdpVal);
                    
                    var kolesterolVal = $('input[name="kolesterol"]').val();
                    formData.kolesterol = (kolesterolVal === '' || kolesterolVal === null || isNaN(parseFloat(kolesterolVal))) ? 0 : parseFloat(kolesterolVal);
                    
                    var trigliseridaVal = $('input[name="trigliserida"]').val();
                    formData.trigliserida = (trigliseridaVal === '' || trigliseridaVal === null || isNaN(parseFloat(trigliseridaVal))) ? 0 : parseFloat(trigliseridaVal);
                    
                    console.log('Data antropometri yang akan dikirim:', formData);
                    break;
                
                case 'skrining-indra':
                    url = "{{ route('api.skrining.skrining-indra') }}";
                    formData.pendengaran = $('input[name="pendengaran"]:checked').val();
                    formData.penglihatan = $('input[name="penglihatan"]:checked').val();
                    break;
                
                case 'skrining-gigi':
                    url = "{{ route('api.skrining.skrining-gigi') }}";
                    formData.karies = $('input[name="karies"]:checked').val();
                    formData.hilang = $('input[name="hilang"]:checked').val();
                    formData.goyang = $('input[name="goyang"]:checked').val();
                    break;
                
                case 'skrining-puma':
                    url = "{{ route('api.skrining.simpan') }}";
                    formData.riwayat_merokok = $('input[name="riwayat_merokok"]:checked').val();
                    formData.napas_pendek = $('input[name="napas_pendek"]:checked').val();
                    formData.dahak = $('input[name="dahak"]:checked').val();
                    formData.batuk = $('input[name="batuk_puma"]:checked').val(); // Menggunakan batuk_puma tetapi menyimpan ke field batuk
                    formData.spirometri = $('input[name="spirometri"]:checked').val();
                    break;
                
                default:
                    // URL default untuk menyimpan data skrining secara umum
                    url = "{{ route('api.skrining.simpan') }}";
                    // Kumpulkan data dari form yang aktif sesuai id modal
                    $('#modal' + formType.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); }) + ' :input').each(function() {
                        var input = $(this);
                        if (input.attr('name') && input.attr('type') !== 'button' && input.attr('type') !== 'submit') {
                            if (input.attr('type') === 'radio') {
                                if (input.is(':checked')) {
                                    formData[input.attr('name')] = input.val();
                                }
                            } else {
                                formData[input.attr('name')] = input.val();
                            }
                        }
                    });
                    break;
            }
            
            // Tambahkan data identitas pasien
            formData.nama_lengkap = $('#nama_lengkap').val();
            formData.tanggal_lahir = $('#tanggal_lahir').val();
            formData.jenis_kelamin = $('#jenis_kelamin').val();
            formData.no_handphone = $('#no_handphone').val();
            formData.umur = $('#umur').val();
            formData.umur_tahun = $('#umur_tahun').val();
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan data...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim data ke server
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log('Data berhasil disimpan:', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Ubah status pemeriksaan menjadi selesai (ikon hijau)
                    $('tr[data-service="' + formType + '"] .status-check').removeClass('badge-secondary').addClass('badge-success');
                },
                error: function(xhr, status, error) {
                    console.error('Gagal menyimpan data:', error);
                    console.log('Status code:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    
                    var errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errorMessages = '';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMessages += value + '<br>';
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi gagal',
                            html: errorMessages,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan',
                            html: errorMessage,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            html: errorMessage + '<br>Kode: ' + xhr.status,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        // Expose simpanDataForm to global scope
        window.simpanDataForm = simpanDataForm;
    });
</script>
@endsection