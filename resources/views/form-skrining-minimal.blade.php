@extends('layouts.minimal')

@section('title', 'Form Skrining Kesehatan Sederhana')

@section('content')
<!-- Meta tag untuk CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Form Skrining dengan desain premium -->
<div class="card shadow-lg mb-5">
    <div class="card-header bg-gradient-primary text-white py-3">
        <h4 class="mb-0 font-weight-bold">FORM SKRINING KESEHATAN</h4>
    </div>
    <div class="card-body">
        <!-- DATA IDENTITAS DIRI -->
        <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2 text-primary">DATA IDENTITAS DIRI</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">NIK</label>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-left" name="nik" id="nik" maxlength="25"
                                required>
                        <div class="input-group-append">
                                <button class="btn btn-primary px-3" type="button" id="cari-nik">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <button class="btn btn-success px-3 ml-2" type="button" id="btn-pasien-baru" title="Pasien Baru" aria-label="Pasien Baru">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button class="btn btn-info px-3 ml-2" type="button" id="btn-edit-pasien" title="Edit Data Pasien" aria-label="Edit Data Pasien">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-warning px-3 ml-2" type="button" id="btn-ambil-data-sebelumnya" style="display: none;">
                                    <i class="fas fa-history"></i> Ambil Data Sebelumnya
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Masukkan NIK untuk mengisi data otomatis</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" maxlength="100"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">Umur</label>
                        <input type="text" class="form-control" name="umur" id="umur" readonly>
                        <input type="hidden" name="umur_tahun" id="umur_tahun">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Jenis Kelamin</label>
                        <select class="form-control custom-select" name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold" style="color: red;">No. Whatapps</label>
                        <input type="text" class="form-control" name="no_handphone" id="no_handphone" maxlength="25">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">Kelurahan</label>
                        <input type="text" class="form-control" name="kelurahan" id="kelurahan" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Posyandu</label>
                        <input type="text" class="form-control" name="nama_posyandu" id="nama_posyandu" readonly>
                        <input type="hidden" name="kode_posyandu" id="kode_posyandu">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-semibold mb-0">Petugas Entri</label>
                            <button type="button" class="btn btn-outline-success btn-sm" id="btn-manage-kader" style="display:none;">
                                <i class="fas fa-user-plus mr-1"></i>+Kader
                            </button>
                        </div>
                        <div class="input-group petugas-entri-group">
                            <select class="form-control custom-select" name="petugas_entri_tipe" id="petugas_entri_tipe">
                                <option value="Umum">Umum</option>
                                <option value="Pegawai Pusk">Pegawai Pusk</option>
                                <option value="Kader">Kader</option>
                            </select>
                            <input type="text" class="form-control petugas-entri-input" name="petugas_entri" id="petugas_entri" maxlength="100">
                            <select class="form-control custom-select petugas-entri-select" id="petugas_entri_select" style="display:none"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Status Kunjungan</label>
                        <select class="form-control custom-select" name="status_petugas" id="status_petugas">
                            <option value="">-- Pilih --</option>
                            <option value="CKG Umum" selected>CKG Umum</option>
                            <option value="Kunjungan Rumah">Kunjungan Rumah</option>
                            <option value="Tindak Lanjut Posyandu">Tindak Lanjut Posyandu</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalKaderCrud" tabindex="-1" role="dialog" aria-labelledby="modalKaderCrudLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content rounded-lg shadow">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title" id="modalKaderCrudLabel">Kelola Data Kader</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="font-weight-bold text-primary mb-3">Form Kader</h6>
                        <input type="hidden" id="kader_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-semibold">Nama Kader <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kader_nama" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-semibold">Nama Posyandu <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select kader-master-select" id="kader_kode_posyandu"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-semibold">Nama Kelurahan <span class="text-danger">*</span></label>
                                    <select class="form-control custom-select kader-master-select" id="kader_kd_kel"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-semibold">Status</label>
                                    <select class="form-control custom-select" id="kader_status">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 font-weight-bold text-primary">Daftar Kader</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-refresh-kader">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                        </div>
                        <div class="table-responsive border rounded mb-2">
                            <table class="table table-sm table-hover m-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Kader</th>
                                        <th>Nama Posyandu</th>
                                        <th>Nama Kelurahan</th>
                                        <th>Status</th>
                                        <th class="text-center" width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kader-table-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Memuat data kader...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="btn-reset-kader-form" style="display:none;">
                            <i class="fas fa-undo mr-1"></i>Batal Edit
                        </button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-success" id="btn-save-kader">
                                <i class="fas fa-save mr-1"></i>Simpan Kader
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATA WALI (untuk anak di bawah 6 tahun) -->
        <div class="section mb-4" id="data-wali" style="display: none;">
            <h5 class="section-title border-bottom pb-2 text-warning">DATA WALI (Untuk Anak di Bawah 6 Tahun)</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">NIK Wali</label>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-left" name="nik_wali" id="nik_wali"
                                maxlength="25">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-3" type="button" id="cari-nik-wali">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">NIK orang tua/wali yang bertanggung jawab</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Nama Wali</label>
                        <input type="text" class="form-control" name="nama_wali" id="nama_wali" maxlength="100">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">Tanggal Lahir Wali</label>
                        <input type="date" class="form-control" name="tanggal_lahir_wali" id="tanggal_lahir_wali">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-semibold">Jenis Kelamin Wali</label>
                        <select class="form-control custom-select" name="jenis_kelamin_wali" id="jenis_kelamin_wali">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>



        <!-- Tabel Pemeriksaan Anak (Untuk Anak di Bawah 6 Tahun) -->
        <div class="section mb-4" id="tabel-pemeriksaan-anak" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title m-0 font-weight-bold text-primary">Pemeriksaan Anak (Usia di Bawah 6 Tahun)
                </h5>
                <span class="badge badge-info px-3 py-2 rounded-pill">Khusus Anak</span>
            </div>

            <div class="card border-0 rounded-lg shadow-sm">
                <div class="card-header bg-light p-3">
                    <h6 class="mb-0">
                        <button
                            class="btn btn-link btn-block text-left text-dark text-decoration-none d-flex justify-content-between align-items-center pl-2 font-weight-bold"
                            type="button">
                            <span><i class="fas fa-chevron-down mr-2"></i>Jumlah Pemeriksaan Anak (10/10)</span>
                        </button>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Layanan</th>
                                    <th class="text-center border-0" width="120">Status</th>
                                    <th class="text-center border-0" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-service="gejala-dm-anak" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Gejala DM Anak</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalGejalaDMAnak">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="demografi-anak" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Demografi Anak</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalDemografiAnak">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="riwayat-imunisasi-balita" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Riwayat Imunisasi Rutin Balita</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalRiwayatImunisasiBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr class="bg-light pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td colspan="3" class="text-center font-weight-bold text-info py-2">
                                        <i class="fas fa-baby mr-2"></i>PERTANYAAN KHUSUS BAYI/BALITA &lt; 1 TAHUN
                                    </td>
                                </tr>
                                <tr data-service="hepatitis-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Riwayat Imunisasi Hepatitis B</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalHepatitisBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="berat-lahir-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Berat Lahir</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalBeratLahirBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="pjb-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Pemeriksaan Jantung Bawaan (PJB)</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalPjbBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="darah-tumit-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Pengambilan Darah Tumit</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalDarahTumitBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="shk-g6pd-hak-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Hasil Pemeriksaan SHK, G6PD, HAK</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalShkG6pdHakBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="konfirmasi-shk-g6pd-hak-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Tes Konfirmasi Pemeriksaan SHK, G6PD, HAK</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKonfirmasiShkG6pdHakBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="edukasi-warna-kulit-balita" class="pertanyaan-bayi-bawah-satu" style="display: none;">
                                    <td class="align-middle">Edukasi Warna Kulit dan Tinja Bayi</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalEdukasiWarnaKulitBalita">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="talasemia" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Talasemia</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalTalasemia">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="tuberkulosis-bayi-anak" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Tuberkulosis Bayi & Anak Pra Sekolah</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalTuberkulosisBayiAnak">Input Data</button>
                                    </td>
                                </tr>
                                <!-- Header Pelayanan Medis -->
                                <tr class="bg-light pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td colspan="3" class="text-center font-weight-bold text-primary py-3">
                                        <i class="fas fa-stethoscope mr-2"></i>PELAYANAN MEDIS
                                    </td>
                                </tr>
                                <tr data-service="skrining-pertumbuhan" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Skrining Pertumbuhan - Balita dan Anak Prasekolah</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningPertumbuhan">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="perkembangan-3-6-tahun" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Perkembangan (3-6 Tahun)</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalPerkembangan3_6Tahun">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kpsp" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Kuesioner Pra Skrining Perkembangan (KPSP)</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKPSP">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-telinga-mata" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Skrining Telinga dan Mata - Balita dan Anak Prasekolah</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningTelingaMata">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-gigi" class="pertanyaan-balita-diatas-satu" style="display: none;">
                                    <td class="align-middle">Skrining Gigi - Balita dan Anak Prasekolah</td>
                                    <td class="text-center align-middle"><span
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

        <!-- Tabel Pemeriksaan -->
        <div class="section mb-4" id="tabel-pemeriksaan-dewasa">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title m-0 font-weight-bold text-primary">Pemeriksaan Mandiri</h5>
                <span class="badge badge-primary px-3 py-2 rounded-pill">Usia Dewasa</span>
            </div>

            <div class="card border-0 rounded-lg shadow-sm">
                <div class="card-header bg-light p-3">
                    <h6 class="mb-0">
                        <button
                            class="btn btn-link btn-block text-left text-dark text-decoration-none d-flex justify-content-between align-items-center pl-2 font-weight-bold"
                            type="button">
                            <span><i class="fas fa-chevron-down mr-2"></i>Jumlah Pemeriksaan (8/8)</span>
                        </button>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Layanan</th>
                                    <th class="text-center border-0" width="120">Status</th>
                                    <th class="text-center border-0" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-service="demografi">
                                    <td class="align-middle">Data Demografi</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalDemografi">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kanker-usus">
                                    <td class="align-middle">Faktor Resiko Kanker Usus</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKankerUsus">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="faktor-resiko-tb">
                                    <td class="align-middle">Faktor Resiko TB - Dewasa&Lansia</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalFaktorResikoTB">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="hati">
                                    <td class="align-middle">Hati</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalHati">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kanker-leher-rahim">
                                    <td class="align-middle">Kanker Leher Rahim</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKankerLeherRahim">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kesehatan-jiwa">
                                    <td class="align-middle">Kesehatan Jiwa</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKesehatanJiwa">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="kanker-paru">
                                    <td class="align-middle">Penapisan Resiko Kanker Paru</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalKankerParu">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="perilaku-merokok">
                                    <td class="align-middle">Perilaku Merokok</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalPerilakuMerokok">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="tekanan-darah">
                                    <td class="align-middle">Tekanan Darah & Gula Darah</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalTekananDarah">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="aktivitas-fisik">
                                    <td class="align-middle">Tingkat Aktivitas Fisik</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalAktivitasFisik">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="tuberkulosis">
                                    <td class="align-middle">Tuberkulosis</td>
                                    <td class="text-center align-middle"><span
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
                                    <td class="align-middle">1. Antropometri dan Laboratorium</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalAntropometriLab">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-puma">
                                    <td class="align-middle">2. Skrining PUMA</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningPuma">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="penyakit-tropis">
                                    <td class="align-middle">Penyakit Tropis</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalPenyakitTropis">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-indra">
                                    <td class="align-middle">3. Skrining Indra</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningIndra">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="skrining-gigi">
                                    <td class="align-middle">4. Skrining Gigi</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalSkriningGigi">Input Data</button>
                                    </td>
                                </tr>
                                <tr data-service="gangguan-fungsional" style="display: none;" class="lansia-only">
                                    <td class="align-middle">5. Pemeriksaan Gangguan Fungsional/Barthel Index</td>
                                    <td class="text-center align-middle"><span
                                            class="badge badge-secondary rounded-circle p-2 status-check"><i
                                                class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                            data-target="#modalGangguanFungsional">Input Data</button>
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

<!-- KELUHAN LAIN -->
<div class="section mb-4">
    <h5 class="section-title border-bottom pb-2 text-primary">DATA HASIL KUNJUNGAN</h5>
    <div class="form-group">
        <label class="font-weight-semibold">Pemeriksaan Lain yang Pernah Dilakukan</label>
        <textarea class="form-control" name="keluhan_lain" id="keluhan_lain" rows="4"
            placeholder=""></textarea>
        <small class="form-text text-muted">Deskripsikan Kondisi Pasien saat Pemeriksaan</small>
    </div>
</div>

<!-- Tambahkan tombol Selesai di bawah tabel -->
<div class="text-center mt-4 mb-5">
    <button type="button" id="btn-selesai-skrining" class="btn btn-success btn-lg px-5 shadow-sm">
        <i class="fas fa-check-circle mr-2"></i>Selesai
    </button>
</div>

<!-- Modal untuk semua pemeriksaan seperti di skrining-ckg.blade.php -->
<!-- Modal Demografi -->
<div class="modal fade" id="modalDemografi" tabindex="-1" role="dialog" aria-labelledby="modalDemografiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-lg shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalDemografiLabel">Data Demografi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.demografi')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary px-4" onclick="simpanDataForm('demografi')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kanker Usus -->
<div class="modal fade" id="modalKankerUsus" tabindex="-1" role="dialog" aria-labelledby="modalKankerUsusLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKankerUsusLabel">Faktor Resiko Kanker Usus</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.Faktor-Resiko_kankerUsus')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('kanker-usus')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Faktor Resiko TB -->
<div class="modal fade" id="modalFaktorResikoTB" tabindex="-1" role="dialog" aria-labelledby="modalFaktorResikoTBLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFaktorResikoTBLabel">Faktor Resiko TB - Dewasa&Lansia</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.Faktor_Resiko-TB')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('faktor-resiko-tb')"
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

<!-- Modal Penapisan Resiko Kanker Paru -->
<div class="modal fade" id="modalKankerParu" tabindex="-1" role="dialog" aria-labelledby="modalKankerParuLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKankerParuLabel">Penapisan Resiko Kanker Paru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.Penapisan-Resiko-KankerParu')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('kanker-paru')"
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

<!-- Modal Penyakit Tropis -->
<div class="modal fade" id="modalPenyakitTropis" tabindex="-1" role="dialog" aria-labelledby="modalPenyakitTropisLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPenyakitTropisLabel">Penyakit Tropis</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.penyakit-tropis')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('penyakit-tropis')" data-dismiss="modal">Simpan</button>
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

<!-- Modal Skrining Pertumbuhan -->
<div class="modal fade" id="modalSkriningPertumbuhan" tabindex="-1" role="dialog"
    aria-labelledby="modalSkriningPertumbuhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSkriningPertumbuhanLabel">Skrining Pertumbuhan - Balita dan Anak
                    Prasekolah</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.skrining-pertumbuhan')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('skrining-pertumbuhan')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal KPSP -->
<div class="modal fade" id="modalKPSP" tabindex="-1" role="dialog" aria-labelledby="modalKPSPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKPSPLabel">Kuesioner Pra Skrining Perkembangan (KPSP)</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.kpsp')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('kpsp')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Skrining Telinga dan Mata -->
<div class="modal fade" id="modalSkriningTelingaMata" tabindex="-1" role="dialog"
    aria-labelledby="modalSkriningTelingaMataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSkriningTelingaMataLabel">Skrining Telinga dan Mata - Balita dan Anak
                    Prasekolah</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.skrining-telinga-mata')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('telinga-mata')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gangguan Fungsional/Barthel Index -->
<div class="modal fade" id="modalGangguanFungsional" tabindex="-1" role="dialog"
    aria-labelledby="modalGangguanFungsionalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalGangguanFungsionalLabel">Pemeriksaan Gangguan Fungsional/Barthel Index
                    - Lansia</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.gangguan-fungsional')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('gangguan-fungsional')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gejala DM Anak -->
<div class="modal fade" id="modalGejalaDMAnak" tabindex="-1" role="dialog" aria-labelledby="modalGejalaDMAnakLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalGejalaDMAnakLabel">Gejala DM Anak</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.gejala-dm-anak')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('gejala-dm-anak')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Demografi Anak -->
<div class="modal fade" id="modalDemografiAnak" tabindex="-1" role="dialog" aria-labelledby="modalDemografiAnakLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDemografiAnakLabel">Demografi Anak</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.demografi-anak')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('demografi-anak')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Imunisasi Rutin Balita -->
<div class="modal fade" id="modalRiwayatImunisasiBalita" tabindex="-1" role="dialog"
    aria-labelledby="modalRiwayatImunisasiBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalRiwayatImunisasiBalitaLabel">Riwayat Imunisasi Rutin Balita</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('ckg.Riwayat_Imunisasi_Balita')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('riwayat-imunisasi-balita')"
                    data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hepatitis Balita -->
@include('ckg.Hepatitis-Balita')

<!-- Modal Berat Lahir Balita -->
@include('ckg.Berat-LahirBalita')

<!-- Modal Pemeriksaan Jantung Bawaan (PJB) Balita -->
@include('ckg.Pjb')

<!-- Modal Pengambilan Darah Tumit Balita -->
@include('ckg.Darah-TumitBalita')

<!-- Modal Hasil Pemeriksaan SHK, G6PD, HAK Balita -->
@include('ckg.Shk-G6pd-HakBalita')

<!-- Modal Tes Konfirmasi Pemeriksaan SHK, G6PD, HAK Balita -->
@include('ckg.Konfirmasi-Shk-G6pd-HakBalita')

<!-- Modal Edukasi Warna Kulit dan Tinja Bayi -->
@include('ckg.Edukasi-Warna-KulitBalita')

<!-- Modal Perkembangan (3-6 Tahun) -->
@include('ckg.perkembangan-3-6-tahun')

<!-- Modal Talasemia -->
@include('ckg.talasemia')

<!-- Modal Tuberkulosis Bayi & Anak Pra Sekolah -->
@include('ckg.tuberkulosis-bayi-anak')

@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        color: #3a3a3a;
        background-color: #f8f9fc;
        line-height: 1.6;
    }

    /* Card dan container styles */
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    .card {
        border-radius: 10px;
        border: none;
        overflow: hidden;
    }

    .shadow-lg {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .shadow-sm {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05) !important;
    }

    /* Gradient backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
    }

    /* Section styles */
    .section-title {
        color: #2e5cb8;
        font-weight: bold;
        letter-spacing: 0.3px;
    }

    .border-bottom {
        border-bottom: 1px solid #eaecf0 !important;
    }

    /* Form controls */
    .form-control,
    .custom-select {
        border-radius: 6px;
        border-color: #eaecf0;
        padding: 10px 15px;
        height: auto;
        transition: all 0.2s;
    }

    .form-control:focus,
    .custom-select:focus {
        border-color: #2e5cb8;
        box-shadow: 0 0 0 0.2rem rgba(46, 92, 184, 0.15);
    }

    .input-group-append .btn {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-text {
        font-size: 0.8rem;
    }

    /* Button styles */
    .btn {
        border-radius: 6px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2251a7 0%, #15326a 100%);
        transform: translateY(-1px);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1c7430 100%);
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        color: #2e5cb8;
        border-color: #2e5cb8;
    }

    .btn-outline-primary:hover {
        background-color: #2e5cb8;
        border-color: #2e5cb8;
    }

    /* Table styles */
    .table {
        color: #4a5568;
    }

    .table thead th {
        border-top: none;
        font-weight: 600;
        color: #2d3748;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(46, 92, 184, 0.03);
    }

    /* Status badge */
    .badge-secondary {
        background-color: #eaecf0;
        color: #718096;
    }

    .badge-success {
        background-color: #48bb78;
        color: white;
    }

    .badge-primary {
        background-color: #2e5cb8;
        color: white;
    }

    .rounded-pill {
        border-radius: 50rem;
    }

    /* Modal styles */
    .modal-content {
        border: none;
    }

    .modal-header {
        border-bottom: 0;
        padding: 15px 20px;
    }

    .modal-footer {
        border-top: 1px solid #eaecf0;
        padding: 15px 20px;
    }

    /* Form check improvements */
    .form-check {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .form-check-input {
        position: absolute;
        margin-top: 0.3rem;
        margin-left: -1.5rem;
        cursor: pointer;
        z-index: 2;
    }

    .form-check-label {
        cursor: pointer;
        margin-bottom: 0;
        padding: 0.4rem 0;
        display: inline-block;
        position: relative;
        z-index: 1;
        transition: all 0.2s;
    }

    .form-check:hover {
        background-color: rgba(46, 92, 184, 0.05);
        border-radius: 6px;
        transform: translateX(3px);
    }

    .form-check-input:checked+.form-check-label {
        color: #2e5cb8;
        font-weight: 600;
    }

    /* Focus states and accessibility */
    .btn:focus,
    button:focus,
    input:focus,
    select:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(46, 92, 184, 0.15);
    }
    .petugas-entri-group .custom-select {
        flex: 0 0 30%;
        max-width: 30%;
    }
    .petugas-entri-group #petugas_entri.form-control {
        flex: 0 0 70%;
        max-width: 70%;
    }
    .petugas-entri-group .select2-container {
        flex: 0 0 70%;
        max-width: 70%;
    }
    .petugas-entri-group .select2-container .select2-selection--single {
        height: auto;
        min-height: 42px;
        border: 1px solid #eaecf0;
        border-radius: 6px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
    }
    .petugas-entri-group .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding: 0;
        width: 100%;
        font-size: inherit;
    }
    .petugas-entri-group .select2-container .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }
    #modalKaderCrud .select2-container {
        width: 100% !important;
    }
    #modalKaderCrud .select2-container .select2-selection--single {
        min-height: 42px;
        border: 1px solid #eaecf0;
        border-radius: 6px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
    }
    #modalKaderCrud .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0;
    }
    #modalKaderCrud .select2-container .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }

    .swal2-popup.pasien-baru-swal {
        width: min(1400px, 96vw) !important;
        max-width: 1400px !important;
        border-radius: 24px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 28px 80px rgba(15, 23, 42, 0.22) !important;
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .swal2-title.pasien-baru-title {
        margin: 0 !important;
        padding: 26px 34px 10px !important;
        text-align: left !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        color: #16325c !important;
        letter-spacing: 0.01em;
    }

    .swal2-html-container.pasien-baru-html {
        margin: 0 !important;
        padding: 0 34px 26px !important;
        text-align: left !important;
        max-height: calc(92vh - 160px);
        overflow-y: auto;
        overflow-x: visible;
    }

    .swal2-actions.pasien-baru-actions {
        width: 100%;
        margin: 0;
        padding: 18px 34px 28px;
        justify-content: flex-end;
        gap: 12px;
        border-top: 1px solid #e2e8f0;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), #f8fafc);
    }

    .pasien-baru-confirm,
    .pasien-baru-cancel {
        min-width: 150px;
        border-radius: 12px !important;
        padding: 11px 20px !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    .pasien-baru-confirm {
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a) !important;
        color: #fff !important;
        border: none !important;
    }

    .pasien-baru-cancel {
        background: #eef2f7 !important;
        color: #334155 !important;
        border: 1px solid #dbe3ef !important;
    }

    .pasien-baru-modal {
        color: #334155;
    }

    .pasien-baru-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 22px 24px;
        margin-bottom: 20px;
        border-radius: 20px;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.14), transparent 34%),
            linear-gradient(135deg, #eff6ff 0%, #f8fafc 52%, #eef2ff 100%);
        border: 1px solid rgba(59, 130, 246, 0.12);
    }

    .pasien-baru-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        margin-bottom: 12px;
        border-radius: 999px;
        background: rgba(29, 78, 216, 0.08);
        color: #1d4ed8;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .pasien-baru-hero h6 {
        margin: 0 0 6px;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .pasien-baru-hero p {
        margin: 0;
        font-size: 0.92rem;
        color: #475569;
        line-height: 1.6;
    }

    .pasien-baru-badge {
        padding: 10px 14px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, 0.24);
        color: #1e3a8a;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .pasien-baru-section {
        margin-bottom: 18px;
        padding: 18px 18px 6px;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #fbfdff);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
    }

    .pasien-baru-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        font-size: 0.84rem;
        font-weight: 800;
        color: #1e3a8a;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .pasien-baru-section-title small {
        font-size: 0.76rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: normal;
        text-transform: none;
    }

    .pasien-baru-modal .form-group {
        margin-bottom: 1rem;
    }

    .pasien-baru-modal label {
        font-size: 0.79rem;
        font-weight: 800;
        color: #334155;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .pasien-baru-modal .form-control {
        min-height: 48px;
        border-radius: 12px;
        border: 1px solid #d8e1ec;
        background: #f8fafc;
        padding: 11px 14px;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .pasien-baru-modal textarea.form-control {
        min-height: 96px;
    }

    .pasien-baru-modal .form-control:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.22rem rgba(59, 130, 246, 0.14);
    }

    .pasien-baru-modal .row {
        margin-right: -10px;
        margin-left: -10px;
    }

    .pasien-baru-modal .row > [class*="col-"] {
        padding-right: 10px;
        padding-left: 10px;
    }

    .pasien-baru-modal .popup-search-dropdown {
        border-radius: 12px !important;
        border-color: #d8e1ec !important;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12) !important;
    }

    @media (min-width: 1200px) {
        .swal2-popup.pasien-baru-swal {
            width: min(1520px, 97vw) !important;
            max-width: 1520px !important;
        }
    }

    @media (max-width: 768px) {
        .swal2-title.pasien-baru-title {
            padding: 20px 18px 8px !important;
            font-size: 1.2rem !important;
        }

        .swal2-html-container.pasien-baru-html {
            padding: 0 18px 18px !important;
            max-height: calc(92vh - 145px);
        }

        .swal2-actions.pasien-baru-actions {
            padding: 14px 18px 18px;
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .pasien-baru-confirm,
        .pasien-baru-cancel {
            width: 100%;
        }

        .pasien-baru-hero {
            padding: 18px;
            border-radius: 16px;
            flex-direction: column;
        }

        .pasien-baru-section {
            padding: 16px 14px 4px;
        }
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Wait for window to fully load including all scripts
    window.addEventListener('load', function() {
        // Double check jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is still not loaded after window load!');
            return;
        }
        
        // Initialize form when everything is ready
        initializeForm();
    });
    
    function initializeForm() {
        // Konfigurasi AJAX global untuk menambahkan CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function setupPetugasEntri() {
            var tipe = $('#petugas_entri_tipe').val();
            var $input = $('#petugas_entri');
            var $select = $('#petugas_entri_select');
            var $manageKaderButton = $('#btn-manage-kader');
            $manageKaderButton.toggle(tipe === 'Kader');
            var useSelect = (tipe === 'Pegawai Pusk' || tipe === 'Kader');
            if (useSelect) {
                var isKader = tipe === 'Kader';
                $input.hide();
                $select.show();
                if ($select.data('select2')) {
                    $select.off('change.petugasEntri');
                    $select.select2('destroy');
                }
                $select.empty();

                $select.select2({
                    placeholder: isKader ? 'Cari kader...' : 'Cari pegawai...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: isKader ? "{{ route('kader') }}" : "{{ route('pegawai.nik') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    },
                    minimumInputLength: 2,
                    language: {
                        errorLoading: function () { return 'Gagal memuat hasil'; },
                        inputTooShort: function() { return 'Ketik minimal 2 karakter'; },
                        searching: function() { return 'Mencari...'; },
                        noResults: function() { return 'Tidak ada hasil'; }
                    }
                });

                $select.on('change.petugasEntri', function() {
                    var data = $select.select2('data');
                    if (data && data.length > 0) {
                        $input.val(isKader ? (data[0].text || '') : (data[0].id || ''));
                    } else {
                        $input.val('');
                    }
                });
            } else {
                if ($select.data('select2')) {
                    $select.off('change.petugasEntri');
                    $select.val(null).trigger('change');
                    $select.select2('destroy');
                }
                $select.hide();
                $input.show();
            }
        }
        setupPetugasEntri();
        $('#petugas_entri_tipe').on('change', setupPetugasEntri);

        var kaderListUrl = "{{ route('kader.list') }}";
        var kaderStoreUrl = "{{ route('kader.store') }}";
        var kaderBaseUrl = "{{ url('/kader') }}";
        var kaderPosyanduSearchUrl = "{{ route('posyandu.search') }}";
        var kaderKelurahanSearchUrl = "{{ route('wilayah.search.kelurahan') }}";
        var kaderCache = {};

        function escapeHtml(value) {
            return $('<div>').text(value === null || value === undefined ? '' : value).html();
        }

        function setKaderSaveButtonLabel() {
            var isEditMode = $('#kader_id').val() !== '';
            var label = isEditMode ? '<i class="fas fa-save mr-1"></i>Perbarui Kader' : '<i class="fas fa-save mr-1"></i>Simpan Kader';
            $('#btn-save-kader').html(label);
            $('#btn-reset-kader-form').toggle(isEditMode);
        }

        function initKaderMasterSelects() {
            var $modal = $('#modalKaderCrud');
            var $posyandu = $('#kader_kode_posyandu');
            var $kelurahan = $('#kader_kd_kel');

            if ($posyandu.data('select2')) {
                $posyandu.select2('destroy');
            }
            if ($kelurahan.data('select2')) {
                $kelurahan.select2('destroy');
            }

            $posyandu.select2({
                placeholder: 'Cari nama posyandu...',
                allowClear: true,
                width: '100%',
                dropdownParent: $modal,
                ajax: {
                    url: kaderPosyanduSearchUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term || '' };
                    },
                    processResults: function(data) {
                        var results = (data || []).map(function(item) {
                            var nama = item.nama_posyandu || '';
                            var desa = item.desa ? (' (' + item.desa + ')') : '';
                            return {
                                id: item.kode_posyandu || '',
                                text: nama + desa
                            };
                        });
                        return { results: results };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                language: {
                    errorLoading: function() { return 'Gagal memuat hasil'; },
                    inputTooShort: function() { return 'Ketik minimal 2 karakter'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Tidak ada hasil'; }
                }
            });

            $kelurahan.select2({
                placeholder: 'Cari nama kelurahan...',
                allowClear: true,
                width: '100%',
                dropdownParent: $modal,
                ajax: {
                    url: kaderKelurahanSearchUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term || '' };
                    },
                    processResults: function(data) {
                        var results = (data || []).map(function(item) {
                            var kode = item.kd_kel || item.id || '';
                            var nama = item.nm_kel || item.nama || '';
                            return {
                                id: kode,
                                text: nama
                            };
                        });
                        return { results: results };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                language: {
                    errorLoading: function() { return 'Gagal memuat hasil'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Tidak ada hasil'; }
                }
            });
        }

        function setSelect2Value($select, id, text) {
            if (id === null || id === undefined || String(id) === '') {
                $select.val(null).trigger('change');
                return;
            }

            var selectedId = String(id);
            var selectedText = text && String(text).trim() !== '' ? String(text) : selectedId;
            var exists = $select.find('option').filter(function() {
                return String($(this).val()) === selectedId;
            }).length > 0;

            if (!exists) {
                var option = new Option(selectedText, selectedId, true, true);
                $select.append(option);
            }

            $select.val(selectedId).trigger('change');
        }

        function resetKaderForm() {
            $('#kader_id').val('');
            $('#kader_nama').val('');
            setSelect2Value($('#kader_kode_posyandu'), '', '');
            setSelect2Value($('#kader_kd_kel'), '', '');
            $('#kader_status').val('1');
            setKaderSaveButtonLabel();
        }

        function renderKaderRows(rows) {
            var $tbody = $('#kader-table-body');
            kaderCache = {};

            if (!rows || rows.length === 0) {
                $tbody.html('<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data kader</td></tr>');
                return;
            }

            var html = rows.map(function(item) {
                var rowId = String(item.id);
                var isActive = String(item.status) === '1';
                kaderCache[rowId] = item;

                return '<tr>' +
                    '<td>' + escapeHtml(item.nama_kader) + '</td>' +
                    '<td>' + escapeHtml(item.nama_posyandu || item.kode_posyandu || '-') + '</td>' +
                    '<td>' + escapeHtml(item.nama_kelurahan || item.nm_kel || item.kd_kel || '-') + '</td>' +
                    '<td>' + (isActive
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-secondary">Nonaktif</span>') + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-outline-warning btn-sm btn-edit-kader mr-1" data-id="' + rowId + '">Edit</button>' +
                        '<button type="button" class="btn btn-outline-danger btn-sm btn-delete-kader" data-id="' + rowId + '">Hapus</button>' +
                    '</td>' +
                '</tr>';
            }).join('');

            $tbody.html(html);
        }

        function loadKaderList() {
            $('#kader-table-body').html('<tr><td colspan="5" class="text-center text-muted py-3">Memuat data kader...</td></tr>');

            return $.ajax({
                url: kaderListUrl,
                type: 'GET',
                dataType: 'json'
            }).done(function(response) {
                renderKaderRows((response && response.data) ? response.data : []);
            }).fail(function(xhr) {
                var errorMessage = 'Gagal memuat daftar kader';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#kader-table-body').html('<tr><td colspan="5" class="text-center text-danger py-3">' + escapeHtml(errorMessage) + '</td></tr>');
            });
        }

        $('#btn-manage-kader').on('click', function() {
            resetKaderForm();
            $('#modalKaderCrud').modal('show');
            loadKaderList();
        });

        $('#btn-refresh-kader').on('click', function() {
            loadKaderList();
        });

        $('#btn-reset-kader-form').on('click', function() {
            resetKaderForm();
        });

        $('#modalKaderCrud').on('hidden.bs.modal', function() {
            resetKaderForm();
        });

        $('#modalKaderCrud').on('shown.bs.modal', function() {
            initKaderMasterSelects();
        });

        $(document).on('click', '.btn-edit-kader', function() {
            var rowId = String($(this).data('id'));
            var item = kaderCache[rowId];
            if (!item) {
                return;
            }

            $('#kader_id').val(item.id);
            $('#kader_nama').val(item.nama_kader || '');
            setSelect2Value($('#kader_kode_posyandu'), item.kode_posyandu || '', item.nama_posyandu || item.kode_posyandu || '');
            setSelect2Value($('#kader_kd_kel'), item.kd_kel || '', item.nama_kelurahan || item.nm_kel || item.kd_kel || '');
            $('#kader_status').val(String(item.status || '1'));
            setKaderSaveButtonLabel();
            $('#kader_nama').trigger('focus');
        });

        $(document).on('click', '.btn-delete-kader', function() {
            var rowId = String($(this).data('id'));
            var item = kaderCache[rowId];
            if (!item) {
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Hapus data kader?',
                html: 'Data kader <b>' + escapeHtml(item.nama_kader) + '</b> akan dihapus.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: kaderBaseUrl + '/' + item.id,
                    type: 'DELETE',
                    dataType: 'json'
                }).done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data kader berhasil dihapus',
                        timer: 1400,
                        showConfirmButton: false
                    });

                    if (String($('#kader_id').val()) === String(item.id)) {
                        resetKaderForm();
                    }

                    loadKaderList();
                }).fail(function(xhr) {
                    var errorMessage = 'Gagal menghapus data kader';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage
                    });
                });
            });
        });

        $('#btn-save-kader').on('click', function() {
            var rowId = $('#kader_id').val();
            var payload = {
                nama_kader: ($('#kader_nama').val() || '').trim(),
                kode_posyandu: ($('#kader_kode_posyandu').val() || '').trim(),
                kd_kel: ($('#kader_kd_kel').val() || '').trim(),
                status: ($('#kader_status').val() || '1').trim()
            };

            if (!payload.nama_kader || !payload.kode_posyandu || !payload.kd_kel) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data belum lengkap',
                    text: 'Nama kader, nama posyandu, dan nama kelurahan wajib diisi.'
                });
                return;
            }

            var isEditMode = rowId !== '';
            var requestUrl = isEditMode ? (kaderBaseUrl + '/' + rowId) : kaderStoreUrl;
            var requestMethod = isEditMode ? 'PUT' : 'POST';
            var $saveButton = $(this);

            $saveButton.prop('disabled', true);

            $.ajax({
                url: requestUrl,
                type: requestMethod,
                dataType: 'json',
                data: payload
            }).done(function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Data kader berhasil disimpan',
                    timer: 1400,
                    showConfirmButton: false
                });

                resetKaderForm();
                loadKaderList();

                if ($('#petugas_entri_tipe').val() === 'Kader') {
                    $('#petugas_entri').val('');
                    if ($('#petugas_entri_select').data('select2')) {
                        $('#petugas_entri_select').val(null).trigger('change');
                    }
                }
            }).fail(function(xhr) {
                var errorMessage = 'Gagal menyimpan data kader';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors)
                        .map(function(messages) { return messages.join(', '); })
                        .join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }).always(function() {
                $saveButton.prop('disabled', false);
                setKaderSaveButtonLabel();
            });
        });
        
        // Fungsi untuk memeriksa apakah pasien lansia (usia > 60 tahun)
        function checkLansia() {
            var umurTahun = parseInt($('#umur_tahun').val());
            if (umurTahun && umurTahun >= 60) {
                $('.lansia-only').show();
            } else {
                $('.lansia-only').hide();
                // Reset status badge jika tersembunyi
                $('tr[data-service="gangguan-fungsional"] .status-check').removeClass('badge-success').addClass('badge-secondary');
            }
        }
        
        // Panggil fungsi saat halaman dimuat dan saat tanggal lahir berubah
        $('#tanggal_lahir').on('change', function() {
            setTimeout(function() {
                checkLansia();
            }, 300); // tunggu sebentar sampai umur_tahun terisi
        });
        
        // Panggil juga saat data pasien dimuat dari server
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url.includes('get-by-nik') || settings.url.includes('cek-nik')) {
                setTimeout(function() {
                    checkLansia();
                    checkDataWali();
                }, 300);
            }
        });
        
        // Tambahkan event handler untuk tombol Selesai
        $('#btn-selesai-skrining').on('click', function() {
            var nik = $('#nik').val();
            var keluhanLain = $('#keluhan_lain').val();
            
            if (!nik) {
                Swal.fire({
                    icon: 'error',
                    title: 'NIK diperlukan',
                    text: 'Harap isi NIK terlebih dahulu sebelum menyelesaikan skrining',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Jika ada keluhan lain, simpan terlebih dahulu
            if (keluhanLain && keluhanLain.trim() !== '') {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menyimpan keluhan lain...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Simpan keluhan lain ke database
                $.ajax({
                    url: "{{ route('api.skrining.keluhan-lain') }}",
                    type: "POST",
                    data: {
                        nik: nik,
                        keluhan_lain: keluhanLain,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log('Keluhan lain berhasil disimpan:', response);
                        // Tampilkan pesan selesai setelah berhasil menyimpan
                        showCompletionMessage();
                    },
                    error: function(xhr, status, error) {
                        console.error('Gagal menyimpan keluhan lain:', error);
                        
                        var errorMessage = 'Terjadi kesalahan saat menyimpan keluhan lain';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan keluhan lain',
                            html: errorMessage,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                // Jika tidak ada keluhan lain, langsung tampilkan pesan selesai
                showCompletionMessage();
            }
            
            function showCompletionMessage() {
                Swal.fire({
                    icon: 'success',
                    title: 'Skrining Kesehatan Selesai',
                    html: 'Terimakasih sudah melakukan Skrining Kesehatan di UPT Puskesmas Kerjo, Informasi dan Tindak Lanjut Skrining Silahkan Hubungi Petugas Kami ....!!',
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Refresh halaman
                        window.location.reload();
                    }
                });
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
            if (years < 0 || isNaN(years)) years = 0;
            
            // Simpan nilai tahun di hidden field untuk database
            $('#umur_tahun').val(years);
            
            // Tampilkan umur dalam tahun saja
            return years + ' Tahun';
        }
        
        // Event handler untuk tanggal lahir - hitung umur otomatis
        $('#tanggal_lahir').on('change', function() {
            var tanggalLahir = $(this).val();
            var umur = hitungUmur(tanggalLahir);
            $('#umur').val(umur);
            
            // Tampilkan/sembunyikan data wali berdasarkan umur
            checkDataWali();
            
            // Update umur di modal skrining pertumbuhan jika sedang terbuka
            if ($('#modalSkriningPertumbuhan').hasClass('show')) {
                localStorage.setItem('pasien_tanggal_lahir', tanggalLahir);
                setTimeout(function() {
                    if (typeof window.ambilUmurDariFormUtama === 'function') {
                        window.ambilUmurDariFormUtama();
                        if (typeof window.hitungRumusPertumbuhan === 'function') {
                            window.hitungRumusPertumbuhan();
                        }
                    }
                }, 100);
            }
        });
        
        // Fungsi untuk mengecek apakah perlu menampilkan data wali dan tabel pemeriksaan anak
        function checkDataWali() {
            var umurTahun = parseInt($('#umur_tahun').val()) || 0;

            if (umurTahun < 6) {
                // Tampilkan section data wali dan tabel pemeriksaan anak untuk anak di bawah 6 tahun
                $('#data-wali').show();
                $('#tabel-pemeriksaan-anak').show();
                $('#tabel-pemeriksaan-dewasa').hide();
                $('#nik_wali, #nama_wali, #tanggal_lahir_wali, #jenis_kelamin_wali').attr('required', true);
            } else {
                // Sembunyikan section data wali dan tabel pemeriksaan anak untuk usia 6 tahun ke atas
                $('#data-wali').hide();
                $('#tabel-pemeriksaan-anak').hide();
                $('#tabel-pemeriksaan-dewasa').show();
                $('#nik_wali, #nama_wali, #tanggal_lahir_wali, #jenis_kelamin_wali').removeAttr('required');
                // Clear data wali
                $('#nik_wali, #nama_wali, #tanggal_lahir_wali, #jenis_kelamin_wali').val('');
            }

            // Tampilkan pertanyaan khusus bayi/balita < 1 tahun
            if (umurTahun < 1) {
                $('.pertanyaan-bayi-bawah-satu').show();
            } else {
                $('.pertanyaan-bayi-bawah-satu').hide();
            }

            // Tampilkan pertanyaan khusus balita > 1 tahun dan < 6 tahun
            if (umurTahun > 1 && umurTahun < 6) {
                $('.pertanyaan-balita-diatas-satu').show();
            } else {
                $('.pertanyaan-balita-diatas-satu').hide();
            }
        }
        
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
            if (window.previousSkriningData && !window.previousDataPopulated) {
                populateFormWithExistingData(window.previousSkriningData);
            }
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
        
        // Event listener untuk modal skrining pertumbuhan
        $('#modalSkriningPertumbuhan').on('shown.bs.modal', function() {
            console.log('=== Modal Skrining Pertumbuhan dibuka ===');
            // Ambil tanggal lahir dari form utama
            var tanggalLahir = $('#tanggal_lahir').val();
            console.log('Tanggal lahir dari form utama:', tanggalLahir);
            
            // Simpan ke localStorage agar bisa diakses dari dalam modal
            if (tanggalLahir) {
                localStorage.setItem('pasien_tanggal_lahir', tanggalLahir);
                console.log('Tanggal lahir disimpan ke localStorage:', tanggalLahir);
            } else {
                console.log('Tanggal lahir kosong, tidak disimpan ke localStorage');
            }
            
            // Trigger fungsi ambil umur di dalam modal
            setTimeout(function() {
                console.log('Timeout selesai, akan memanggil fungsi ambilUmurDariFormUtama');
                if (typeof window.ambilUmurDariFormUtama === 'function') {
                    console.log('Fungsi ambilUmurDariFormUtama ditemukan, memanggil...');
                    window.ambilUmurDariFormUtama();
                    // Hitung ulang rumus setelah umur diupdate
                    if (typeof window.hitungRumusPertumbuhan === 'function') {
                        console.log('Fungsi hitungRumusPertumbuhan ditemukan, memanggil...');
                        window.hitungRumusPertumbuhan();
                    } else {
                        console.log('Fungsi hitungRumusPertumbuhan tidak ditemukan');
                    }
                } else {
                    console.log('Fungsi ambilUmurDariFormUtama tidak ditemukan');
                }
            }, 500);
        });
        
        // Bersihkan localStorage saat modal ditutup
        $('#modalSkriningPertumbuhan').on('hidden.bs.modal', function() {
            localStorage.removeItem('pasien_tanggal_lahir');
        });

        $('#modalKankerLeherRahim').on('shown.bs.modal', function() {
            var nik = $('#nik').val();
            var existing = window.previousSkriningData || null;
            if (existing && existing.hubungan_intim) {
                $('input[name="hubungan_intim"][value="' + existing.hubungan_intim + '"]').prop('checked', true);
                return;
            }
            if (!nik) return;
            $.ajax({
                url: "{{ route('api.skrining.cek-nik') }}",
                type: "POST",
                data: { nik: nik },
                dataType: "json",
                success: function(response) {
                    var data = null;
                    if (response.status === 'warning' && response.data) data = response.data;
                    else if (response.status === 'info' && response.data) data = response.data;
                    if (!data || !data.hubungan_intim) return;
                    $('input[name="hubungan_intim"][value="' + data.hubungan_intim + '"]').prop('checked', true);
                }
            });
        });
        
        $('#modalAntropometriLab').on('shown.bs.modal', function() {
            var nik = $('#nik').val();
            var existing = window.previousSkriningData || null;
            if (existing) {
                if (existing.riwayat_dm) {
                    $('input[name="riwayat_dm"][value="' + existing.riwayat_dm + '"]').prop('checked', true);
                } else {
                    $('input[name="riwayat_dm"]').prop('checked', false);
                }
                if (typeof toggleLamaRiwayatDmDewasa === 'function') {
                    toggleLamaRiwayatDmDewasa(existing.riwayat_dm || '');
                }
                if (existing.riwayat_dm === 'Ya' && existing.lama_riwayat_dm_dewasa !== undefined && existing.lama_riwayat_dm_dewasa !== null) {
                    $('input[name="lama_riwayat_dm_dewasa"]').val(existing.lama_riwayat_dm_dewasa);
                }
                if (existing.riwayat_ht) {
                    $('input[name="riwayat_ht"][value="' + existing.riwayat_ht + '"]').prop('checked', true);
                } else {
                    $('input[name="riwayat_ht"]').prop('checked', false);
                }
                if (typeof toggleLamaRiwayatHtDewasa === 'function') {
                    toggleLamaRiwayatHtDewasa(existing.riwayat_ht || '');
                }
                if (existing.riwayat_ht === 'Ya' && existing.lama_riwayat_ht_dewasa !== undefined && existing.lama_riwayat_ht_dewasa !== null) {
                    $('input[name="lama_riwayat_ht_dewasa"]').val(existing.lama_riwayat_ht_dewasa);
                }
                if (existing.tinggi_badan) $('input[name="tinggi_badan"]').val(existing.tinggi_badan);
                if (existing.berat_badan) $('input[name="berat_badan"]').val(existing.berat_badan);
                if (existing.lingkar_perut) $('input[name="lingkar_perut"]').val(existing.lingkar_perut);
                if (existing.tekanan_sistolik) $('input[name="tekanan_sistolik"]').val(existing.tekanan_sistolik);
                if (existing.tekanan_diastolik) $('input[name="tekanan_diastolik"]').val(existing.tekanan_diastolik);
                if (existing.gds !== undefined && existing.gds !== null) $('input[name="gds"]').val(existing.gds);
                if (existing.gdp !== undefined && existing.gdp !== null) $('input[name="gdp"]').val(existing.gdp);
                if (existing.kolesterol_lab !== undefined && existing.kolesterol_lab !== null) $('input[name="kolesterol_lab"]').val(existing.kolesterol_lab);
                if (existing.trigliserida !== undefined && existing.trigliserida !== null) $('input[name="trigliserida"]').val(existing.trigliserida);
                return;
            }
            if (!nik) return;
            $.ajax({
                url: "{{ route('api.skrining.cek-nik') }}",
                type: "POST",
                data: { nik: nik },
                dataType: "json",
                success: function(response) {
                    var data = null;
                    if (response.status === 'warning' && response.data) data = response.data;
                    else if (response.status === 'info' && response.data) data = response.data;
                    if (!data) return;
                    if (data.riwayat_dm) {
                        $('input[name="riwayat_dm"][value="' + data.riwayat_dm + '"]').prop('checked', true);
                    } else {
                        $('input[name="riwayat_dm"]').prop('checked', false);
                    }
                    if (typeof toggleLamaRiwayatDmDewasa === 'function') {
                        toggleLamaRiwayatDmDewasa(data.riwayat_dm || '');
                    }
                    if (data.riwayat_dm === 'Ya' && data.lama_riwayat_dm_dewasa !== undefined && data.lama_riwayat_dm_dewasa !== null) {
                        $('input[name="lama_riwayat_dm_dewasa"]').val(data.lama_riwayat_dm_dewasa);
                    }
                    if (data.riwayat_ht) {
                        $('input[name="riwayat_ht"][value="' + data.riwayat_ht + '"]').prop('checked', true);
                    } else {
                        $('input[name="riwayat_ht"]').prop('checked', false);
                    }
                    if (typeof toggleLamaRiwayatHtDewasa === 'function') {
                        toggleLamaRiwayatHtDewasa(data.riwayat_ht || '');
                    }
                    if (data.riwayat_ht === 'Ya' && data.lama_riwayat_ht_dewasa !== undefined && data.lama_riwayat_ht_dewasa !== null) {
                        $('input[name="lama_riwayat_ht_dewasa"]').val(data.lama_riwayat_ht_dewasa);
                    }
                    if (data.tinggi_badan) $('input[name="tinggi_badan"]').val(data.tinggi_badan);
                    if (data.berat_badan) $('input[name="berat_badan"]').val(data.berat_badan);
                    if (data.lingkar_perut) $('input[name="lingkar_perut"]').val(data.lingkar_perut);
                    if (data.tekanan_sistolik) $('input[name="tekanan_sistolik"]').val(data.tekanan_sistolik);
                    if (data.tekanan_diastolik) $('input[name="tekanan_diastolik"]').val(data.tekanan_diastolik);
                    if (data.gds !== undefined && data.gds !== null) $('input[name="gds"]').val(data.gds);
                    if (data.gdp !== undefined && data.gdp !== null) $('input[name="gdp"]').val(data.gdp);
                    if (data.kolesterol_lab !== undefined && data.kolesterol_lab !== null) $('input[name="kolesterol"]').val(data.kolesterol_lab);
                    if (data.trigliserida !== undefined && data.trigliserida !== null) $('input[name="trigliserida"]').val(data.trigliserida);
                }
            });
        });

        $('#modalSkriningIndra').on('shown.bs.modal', function() {
            var nik = $('#nik').val();
            var existing = window.previousSkriningData || null;
            if (existing) {
                var pendengaranVal = existing.pendengaran;
                if (pendengaranVal === 'Gangguan pendengaran') pendengaranVal = 'Curiga gangguan pendengaran';
                var penglihatanVal = existing.penglihatan;
                if (penglihatanVal === 'Normal') penglihatanVal = 'Normal (visus 6/6 - 6/12)';
                if (penglihatanVal === 'Menggunakan Kacamata') penglihatanVal = 'Curiga gangguan penglihatan (visus <6/12)';
                var pupilVal = existing.pupil || existing.selaput_mata;
                if (pupilVal === 'Curiga kelainan mata') pupilVal = 'Curiga Katarak';

                if (existing.hasil_serumen) $('input[name="hasil_serumen"][value="' + existing.hasil_serumen + '"]').prop('checked', true);
                if (existing.hasil_infeksi_telinga) $('input[name="hasil_infeksi_telinga"][value="' + existing.hasil_infeksi_telinga + '"]').prop('checked', true);
                if (pendengaranVal) $('input[name="pendengaran"][value="' + pendengaranVal + '"]').prop('checked', true);
                if (penglihatanVal) $('input[name="penglihatan"]').filter(function() { return this.value === penglihatanVal; }).prop('checked', true);
                if (pupilVal) $('input[name="pupil"][value="' + pupilVal + '"]').prop('checked', true);
                return;
            }
            if (!nik) return;
            $.ajax({
                url: "{{ route('api.skrining.cek-nik') }}",
                type: "POST",
                data: { nik: nik },
                dataType: "json",
                success: function(response) {
                    var data = null;
                    if (response.status === 'warning' && response.data) data = response.data;
                    else if (response.status === 'info' && response.data) data = response.data;
                    if (!data) return;
                    var pendengaranVal = data.pendengaran;
                    if (pendengaranVal === 'Gangguan pendengaran') pendengaranVal = 'Curiga gangguan pendengaran';
                    var penglihatanVal = data.penglihatan;
                    if (penglihatanVal === 'Normal') penglihatanVal = 'Normal (visus 6/6 - 6/12)';
                    if (penglihatanVal === 'Menggunakan Kacamata') penglihatanVal = 'Curiga gangguan penglihatan (visus <6/12)';
                    var pupilVal = data.pupil || data.selaput_mata;
                    if (pupilVal === 'Curiga kelainan mata') pupilVal = 'Curiga Katarak';

                    if (data.hasil_serumen) $('input[name="hasil_serumen"][value="' + data.hasil_serumen + '"]').prop('checked', true);
                    if (data.hasil_infeksi_telinga) $('input[name="hasil_infeksi_telinga"][value="' + data.hasil_infeksi_telinga + '"]').prop('checked', true);
                    if (pendengaranVal) $('input[name="pendengaran"][value="' + pendengaranVal + '"]').prop('checked', true);
                    if (penglihatanVal) $('input[name="penglihatan"]').filter(function() { return this.value === penglihatanVal; }).prop('checked', true);
                    if (pupilVal) $('input[name="pupil"][value="' + pupilVal + '"]').prop('checked', true);
                }
            });
        });

        $('#modalTuberkulosis').on('shown.bs.modal', function() {
            var nik = $('#nik').val();
            var existing = window.previousSkriningData || null;
            if (existing) {
                if (existing.batuk) $('input[name="batuk_berdahak"][value="' + existing.batuk + '"]').prop('checked', true);
                if (existing.demam) $('input[name="demam"][value="' + existing.demam + '"]').prop('checked', true);
                return;
            }
            if (!nik) return;
            $.ajax({
                url: "{{ route('api.skrining.cek-nik') }}",
                type: "POST",
                data: { nik: nik },
                dataType: "json",
                success: function(response) {
                    var data = null;
                    if (response.status === 'warning' && response.data) data = response.data;
                    else if (response.status === 'info' && response.data) data = response.data;
                    if (!data) return;
                    if (data.batuk) $('input[name="batuk_berdahak"][value="' + data.batuk + '"]').prop('checked', true);
                    if (data.demam) $('input[name="demam"][value="' + data.demam + '"]').prop('checked', true);
                }
            });
        });
        
        $('#modalGejalaDMAnak, #modalDemografiAnak, #modalHepatitisBalita, #modalBeratLahirBalita, #modalPjbBalita, #modalDarahTumitBalita, #modalShkG6pdHakBalita, #modalKonfirmasiShkG6pdHakBalita, #modalEdukasiWarnaKulitBalita, #modalPerkembangan3_6Tahun, #modalTalasemia, #modalTuberkulosisBayiAnak, #modalKPSP, #modalSkriningTelingaMata, #modalSkriningGigi').on('shown.bs.modal', function() {
            if (window.previousSkriningData && !window.previousDataPopulated) {
                populateFormWithExistingData(window.previousSkriningData);
            }
        });
        
        // Fungsi untuk mengisi form dengan data skrining yang sudah ada
        function populateFormWithExistingData(data) {
            console.log('Mengisi form dengan data:', data);
            window.previousDataPopulated = true;
            
            // Data identitas dasar
            $('#nama_lengkap').val(data.nama_lengkap || '');
            $('#tanggal_lahir').val(formatDateForInput(data.tanggal_lahir) || '').trigger('change');
            $('#jenis_kelamin').val(data.jenis_kelamin || '').trigger('change');
            $('#no_handphone').val(data.no_handphone || '');
            $('#petugas_entri').val(data.petugas_entri || '');
            if (data.status_petugas) $('#status_petugas').val(data.status_petugas);
            if (data.kode_posyandu) {
                $('#kode_posyandu').val(data.kode_posyandu);
                $.ajax({
                    url: "{{ route('posyandu.get-by-kode') }}",
                    type: "GET",
                    data: { kode_posyandu: data.kode_posyandu },
                    dataType: "json",
                    success: function(res) {
                        if (res && res.status === 'success' && res.data) {
                            $('#nama_posyandu').val(res.data.nama_posyandu || '');
                        }
                    }
                });
                if (data.nm_kel) {
                    $('#kelurahan').val(data.nm_kel || '');
                }
            } else {
                var nikValue = $('#nik').val() || data.nik || '';
                if (data.nm_kel) {
                    $('#kelurahan').val(data.nm_kel || '');
                }
                if (nikValue) {
                    $.ajax({
                        url: "{{ route('pasien.get-by-nik') }}",
                        type: "GET",
                        data: { nik: nikValue },
                        dataType: "json",
                        success: function(rp) {
                            if (rp && rp.status === 'success' && rp.data) {
                                var p = rp.data;
                                if (!$('#kelurahan').val()) {
                                    $('#kelurahan').val(p.nm_kel || '');
                                }
                                $('#nama_posyandu').val(p.nama_posyandu || '');
                                $('#kode_posyandu').val(p.kode_posyandu || p.data_posyandu || '');
                            }
                        }
                    });
                }
            }
            
            var umur = parseInt(data.umur) || 0;
            $('#umur_tahun').val(umur);
            $('#umur').val(umur + ' Tahun');
            if (umur < 6) {
                $('#nik_wali').val(data.nik_wali || '');
                $('#nama_wali').val(data.nama_wali || '');
                $('#tanggal_lahir_wali').val(formatDateForInput(data.tanggal_lahir_wali) || '');
                $('#jenis_kelamin_wali').val(data.jenis_kelamin_wali || '');
                $('#data-wali').show();
            } else {
                $('#data-wali').hide();
                $('#nik_wali, #nama_wali, #tanggal_lahir_wali, #jenis_kelamin_wali').val('');
            }
            checkDataWali();
            
            // Tampilkan tabel pemeriksaan yang sesuai berdasarkan umur
            if (umur < 18) {
                $('#tabel-pemeriksaan-anak').show();
                $('#tabel-pemeriksaan-dewasa').hide();
                console.log('Menampilkan tabel anak untuk umur:', umur);
            } else {
                $('#tabel-pemeriksaan-dewasa').show();
                $('#tabel-pemeriksaan-anak').hide();
                console.log('Menampilkan tabel dewasa untuk umur:', umur);
            }
            
            // Data tekanan darah
            if (data.riwayat_hipertensi || data.riwayat_diabetes) {
                if (data.riwayat_hipertensi) $('input[name="riwayat_hipertensi"][value="' + data.riwayat_hipertensi + '"]').prop('checked', true);
                if (data.riwayat_diabetes) $('input[name="riwayat_diabetes"][value="' + data.riwayat_diabetes + '"]').prop('checked', true);
                console.log('Updating badge for tekanan-darah');
                $('#tabel-pemeriksaan-dewasa tr[data-service="tekanan-darah"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data perilaku merokok
            if (data.status_merokok || data.lama_merokok || data.jumlah_rokok || data.paparan_asap) {
                if (data.status_merokok) $('input[name="status_merokok"][value="' + data.status_merokok + '"]').prop('checked', true);
                if (data.lama_merokok) $('#lama_merokok').val(data.lama_merokok);
                if (data.jumlah_rokok) $('#jumlah_rokok').val(data.jumlah_rokok);
                if (data.paparan_asap) $('input[name="paparan_asap"][value="' + data.paparan_asap + '"]').prop('checked', true);
                console.log('Updating badge for perilaku-merokok');
                $('#tabel-pemeriksaan-dewasa tr[data-service="perilaku-merokok"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data aktivitas fisik
            if (data.frekuensi_olahraga || data.durasi_olahraga) {
                if (data.frekuensi_olahraga) $('input[name="frekuensi_olahraga"][value="' + data.frekuensi_olahraga + '"]').prop('checked', true);
                if (data.durasi_olahraga) $('#durasi_olahraga').val(data.durasi_olahraga);
                console.log('Updating badge for aktivitas-fisik');
                $('#tabel-pemeriksaan-dewasa tr[data-service="aktivitas-fisik"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data konsumsi buah dan sayur
            if (data.konsumsi_buah || data.konsumsi_sayur) {
                if (data.konsumsi_buah) $('#konsumsi_buah').val(data.konsumsi_buah);
                if (data.konsumsi_sayur) $('#konsumsi_sayur').val(data.konsumsi_sayur);
                // Update status badge untuk konsumsi buah sayur jika ada data-service yang sesuai
            }
            
            // Data keluhan lain
            if (data.keluhan_lain) {
                $('#keluhan_lain').val(data.keluhan_lain);
                // Update status badge untuk keluhan lain jika ada data-service yang sesuai
            }
            
            // Data gangguan fungsional (Barthel Index)
            if (data.bab) {
                $('#bab').val(data.bab || '');
                $('#bak').val(data.bak || '');
                $('#membersihkan_diri').val(data.membersihkan_diri || '');
                $('#penggunaan_jamban').val(data.penggunaan_jamban || '');
                $('#makan_minum').val(data.makan_minum || '');
                $('#berubah_sikap').val(data.berubah_sikap || '');
                $('#berpindah').val(data.berpindah || '');
                $('#memakai_baju').val(data.memakai_baju || '');
                $('#naik_tangga').val(data.naik_tangga || '');
                $('#mandi').val(data.mandi || '');
                $('#tabel-pemeriksaan-dewasa tr[data-service="gangguan-fungsional"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data skrining PUMA
            if (data.riwayat_merokok || data.napas_pendek || data.dahak || data.batuk || data.spirometri) {
                if (data.riwayat_merokok) $('input[name="riwayat_merokok"][value="' + data.riwayat_merokok + '"]').prop('checked', true);
                if (data.napas_pendek) $('input[name="napas_pendek"][value="' + data.napas_pendek + '"]').prop('checked', true);
                if (data.dahak) $('input[name="dahak"][value="' + data.dahak + '"]').prop('checked', true);
                if (data.batuk) $('input[name="batuk_puma"][value="' + data.batuk + '"]').prop('checked', true);
                if (data.spirometri) $('input[name="spirometri"][value="' + data.spirometri + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-dewasa tr[data-service="skrining-puma"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data skrining pertumbuhan anak (jika ada)
            if (data.berat_badan_balita || data.tinggi_badan_balita || data.berat_badan || data.tinggi_badan || data.posisi_ukur || data.lingkar_kepala || data.status_gizi_bb_u || data.status_gizi_pb_u || data.status_gizi_bb_pb || data.hasil_imt_u || data.status_lingkar_kepala) {
                // Prioritaskan kolom balita, fallback ke kolom umum bila data lama
                if (data.berat_badan_balita) $('#berat_badan').val(data.berat_badan_balita);
                else if (data.berat_badan) $('#berat_badan').val(data.berat_badan);

                if (data.tinggi_badan_balita) $('#tinggi_badan').val(data.tinggi_badan_balita);
                else if (data.tinggi_badan) $('#tinggi_badan').val(data.tinggi_badan);

                if (data.posisi_ukur) $('#posisi_ukur').val(data.posisi_ukur);
                if (data.lingkar_kepala) $('#lingkar_kepala').val(data.lingkar_kepala);
                if (data.status_gizi_bb_u) $('#status_gizi_bb_u').val(data.status_gizi_bb_u);
                if (data.status_gizi_pb_u) $('#status_gizi_pb_u').val(data.status_gizi_pb_u);
                if (data.status_gizi_bb_pb) $('#status_gizi_bb_pb').val(data.status_gizi_bb_pb);
                if (data.hasil_imt_u) $('#hasil_imt_u').val(data.hasil_imt_u);
                if (data.status_lingkar_kepala) $('#status_lingkar_kepala').val(data.status_lingkar_kepala);
                $('#tabel-pemeriksaan-anak tr[data-service="skrining-pertumbuhan"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data KPSP (jika ada)
            if (data.hasil_kpsp) {
                $('select[name="hasil_kpsp"]').val(data.hasil_kpsp);
                $('#tabel-pemeriksaan-anak tr[data-service="kpsp"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Telinga Mata (jika ada)
            if (
                data.hasil_tes_dengar || data.hasil_tes_lihat ||
                data.hasil_serumen || data.hasil_infeksi_telinga || data.selaput_mata
            ) {
                if (data.hasil_tes_dengar) $('select[name="hasil_tes_dengar"]').val(data.hasil_tes_dengar);
                if (data.hasil_tes_lihat) $('select[name="hasil_tes_lihat"]').val(data.hasil_tes_lihat);
                if (data.hasil_serumen) $('select[name="hasil_serumen"]').val(data.hasil_serumen);
                if (data.hasil_infeksi_telinga) $('select[name="hasil_infeksi_telinga"]').val(data.hasil_infeksi_telinga);
                if (data.selaput_mata) $('select[name="selaput_mata"]').val(data.selaput_mata);
                $('#tabel-pemeriksaan-anak tr[data-service="skrining-telinga-mata"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Gejala DM Anak (jika ada)
            if (data.pernah_dm_oleh_dokter || data.lama_anak_dm || data.sering_lapar || data.sering_haus || data.berat_turun || data.riwayat_diabetes_ortu) {
                if (data.pernah_dm_oleh_dokter) {
                    $('input[name="pernah_dm_oleh_dokter"][value="' + data.pernah_dm_oleh_dokter + '"]').prop('checked', true);
                    // Panggil fungsi toggle untuk menampilkan section yang sesuai
                    if (typeof togglePertanyaanGejalaDmAnak === 'function') {
                        togglePertanyaanGejalaDmAnak(data.pernah_dm_oleh_dokter);
                    }
                }
                if (data.lama_anak_dm) {
                    $('input[name="lama_anak_dm"]').val(data.lama_anak_dm);
                }
                if (data.sering_lapar) $('input[name="sering_lapar"][value="' + data.sering_lapar + '"]').prop('checked', true);
                if (data.sering_haus) $('input[name="sering_haus"][value="' + data.sering_haus + '"]').prop('checked', true);
                if (data.berat_turun) $('input[name="berat_turun"][value="' + data.berat_turun + '"]').prop('checked', true);
                if (data.riwayat_diabetes_ortu) $('input[name="riwayat_diabetes_ortu"][value="' + data.riwayat_diabetes_ortu + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="gejala-dm-anak"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Riwayat Imunisasi Rutin Balita (jika ada)
            if (data.imunisasi_inti || data.imunisasi_lanjutan || data.imunisasi_lanjutan_1 || data.imunisasi_lanjutan_18) {
                if (data.imunisasi_inti) {
                    $('select[name="imunisasi_inti"]').val(data.imunisasi_inti);
                    if (typeof toggleImunisasiBalitaInti === 'function') {
                        toggleImunisasiBalitaInti(data.imunisasi_inti);
                    }
                }
                if (data.imunisasi_lanjutan) {
                    $('select[name="imunisasi_lanjutan"]').val(data.imunisasi_lanjutan);
                    if (typeof toggleImunisasiBalitaLanjutan === 'function') {
                        toggleImunisasiBalitaLanjutan(data.imunisasi_lanjutan);
                    }
                }
                for (let i = 1; i <= 18; i++) {
                    var key = 'imunisasi_lanjutan_' + i;
                    if (data[key]) $('select[name="' + key + '"]').val(data[key]);
                }
                $('#tabel-pemeriksaan-anak tr[data-service="riwayat-imunisasi-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Hepatitis Balita (khusus < 1 tahun)
            if (data.imunisasi_lanjutan_1) {
                $('input[name="hepatitis_balita"][value="' + data.imunisasi_lanjutan_1 + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="hepatitis-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Berat Lahir Balita (khusus < 1 tahun)
            if (
                (data.berat_lahir !== null && data.berat_lahir !== undefined && data.berat_lahir !== '') ||
                (data.berat_badan_balita !== null && data.berat_badan_balita !== undefined && data.berat_badan_balita !== '')
            ) {
                if (data.berat_lahir !== null && data.berat_lahir !== undefined && data.berat_lahir !== '') {
                    $('input[name="berat_lahir"]').val(data.berat_lahir);
                }
                if (data.berat_badan_balita !== null && data.berat_badan_balita !== undefined && data.berat_badan_balita !== '') {
                    $('input[name="berat_badan_balita"]').val(data.berat_badan_balita);
                }
                $('#tabel-pemeriksaan-anak tr[data-service="berat-lahir-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Pemeriksaan Jantung Bawaan (PJB) Balita (khusus < 1 tahun)
            if (
                (data.pjb_tangan_kanan !== null && data.pjb_tangan_kanan !== undefined && data.pjb_tangan_kanan !== '') ||
                (data.pjb_kaki !== null && data.pjb_kaki !== undefined && data.pjb_kaki !== '')
            ) {
                if (data.pjb_tangan_kanan !== null && data.pjb_tangan_kanan !== undefined && data.pjb_tangan_kanan !== '') {
                    $('input[name="pjb_tangan_kanan"]').val(data.pjb_tangan_kanan);
                }
                if (data.pjb_kaki !== null && data.pjb_kaki !== undefined && data.pjb_kaki !== '') {
                    $('input[name="pjb_kaki"]').val(data.pjb_kaki);
                }
                $('#tabel-pemeriksaan-anak tr[data-service="pjb-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Pengambilan Darah Tumit Balita (khusus < 1 tahun)
            if (data.darah_tumit) {
                $('input[name="darah_tumit"][value="' + data.darah_tumit + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="darah-tumit-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Hasil Pemeriksaan SHK, G6PD, HAK Balita (khusus < 1 tahun)
            var g6pdResult = (data.G6PD !== null && data.G6PD !== undefined && data.G6PD !== '') ? data.G6PD : data.g6pd;
            if (data.shk || g6pdResult || data.hak) {
                if (data.shk) $('input[name="shk"][value="' + data.shk + '"]').prop('checked', true);
                if (g6pdResult) $('input[name="g6pd"][value="' + g6pdResult + '"]').prop('checked', true);
                if (data.hak) $('input[name="hak"][value="' + data.hak + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="shk-g6pd-hak-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Tes Konfirmasi Pemeriksaan SHK, G6PD, HAK Balita (khusus < 1 tahun)
            if (data.konfirmasi_shk || data.konfirmasi_g6pd || data.konfirmasi_hak) {
                if (data.konfirmasi_shk) $('input[name="konfirmasi_shk"][value="' + data.konfirmasi_shk + '"]').prop('checked', true);
                if (data.konfirmasi_g6pd) $('input[name="konfirmasi_g6pd"][value="' + data.konfirmasi_g6pd + '"]').prop('checked', true);
                if (data.konfirmasi_hak) $('input[name="konfirmasi_hak"][value="' + data.konfirmasi_hak + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="konfirmasi-shk-g6pd-hak-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }

            // Data Edukasi Warna Kulit dan Tinja Bayi (khusus < 1 tahun)
            if (data.edukasi_warna_kulit || data.hasil_kreamer) {
                if (data.edukasi_warna_kulit) {
                    $('input[name="edukasi_warna_kulit"][value="' + data.edukasi_warna_kulit + '"]').prop('checked', true);
                }
                if (data.hasil_kreamer) {
                    $('input[name="hasil_kreamer"][value="' + data.hasil_kreamer + '"]').prop('checked', true);
                }
                $('#tabel-pemeriksaan-anak tr[data-service="edukasi-warna-kulit-balita"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Demografi Anak (jika ada)
            if (data.status_disabilitas_anak) {
                if (data.status_disabilitas_anak) $('input[name="status_disabilitas"][value="' + data.status_disabilitas_anak + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="demografi-anak"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Perkembangan 3-6 Tahun (jika ada)
            if (data.gangguan_emosi || data.hiperaktif) {
                if (data.gangguan_emosi) $('input[name="gangguan_emosi"][value="' + data.gangguan_emosi + '"]').prop('checked', true);
                if (data.hiperaktif) $('input[name="hiperaktif"][value="' + data.hiperaktif + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="perkembangan-3-6-tahun"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            if (data.pembawa_sifat || data.riwayat_keluarga || data.riwayat_keluarga_talasemia) {
                if (data.pembawa_sifat) $('input[name="pembawa_sifat"][value="' + data.pembawa_sifat + '"]').prop('checked', true);
                var rk = data.riwayat_keluarga || data.riwayat_keluarga_talasemia;
                if (rk) $('input[name="riwayat_keluarga"][value="' + rk + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="talasemia"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            if (data.batuk_lama || data.berat_turun_tbc || data.berat_tidak_naik || data.nafsu_makan_berkurang || data.kontak_tbc) {
                if (data.batuk_lama) $('input[name="batuk_lama"][value="' + data.batuk_lama + '"]').prop('checked', true);
                if (data.berat_turun_tbc) $('input[name="berat_turun_tbc"][value="' + data.berat_turun_tbc + '"]').prop('checked', true);
                if (data.berat_tidak_naik) $('input[name="berat_tidak_naik"][value="' + data.berat_tidak_naik + '"]').prop('checked', true);
                if (data.nafsu_makan_berkurang) $('input[name="nafsu_makan_berkurang"][value="' + data.nafsu_makan_berkurang + '"]').prop('checked', true);
                if (data.kontak_tbc) $('input[name="kontak_tbc"][value="' + data.kontak_tbc + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-anak tr[data-service="tuberkulosis-bayi-anak"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Hati (jika ada)
            if (data.riwayat_hepatitis || data.riwayat_kuning || data.riwayat_transfusi || data.riwayat_tattoo || data.riwayat_tindik || data.narkoba_suntik || data.odhiv || data.kolesterol) {
                if (data.riwayat_hepatitis) $('input[name="riwayat_hepatitis"][value="' + data.riwayat_hepatitis + '"]').prop('checked', true);
                if (data.riwayat_kuning) $('input[name="riwayat_kuning"][value="' + data.riwayat_kuning + '"]').prop('checked', true);
                if (data.riwayat_transfusi) $('input[name="riwayat_transfusi"][value="' + data.riwayat_transfusi + '"]').prop('checked', true);
                if (data.riwayat_tattoo) $('input[name="riwayat_tattoo"][value="' + data.riwayat_tattoo + '"]').prop('checked', true);
                if (data.riwayat_tindik) $('input[name="riwayat_tindik"][value="' + data.riwayat_tindik + '"]').prop('checked', true);
                if (data.narkoba_suntik) $('input[name="narkoba_suntik"][value="' + data.narkoba_suntik + '"]').prop('checked', true);
                if (data.odhiv) $('input[name="odhiv"][value="' + data.odhiv + '"]').prop('checked', true);
                if (data.kolesterol) $('input[name="kolesterol"][value="' + data.kolesterol + '"]').prop('checked', true);
                console.log('Updating badge for hati');
                $('#tabel-pemeriksaan-dewasa tr[data-service="hati"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            if (data.hubungan_intim) {
                $('input[name="hubungan_intim"][value="' + data.hubungan_intim + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-dewasa tr[data-service="kanker-leher-rahim"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Kesehatan Jiwa (jika ada)
            if (data.minat || data.sedih || data.cemas || data.khawatir) {
                if (data.minat) $('input[name="minat"][value="' + data.minat + '"]').prop('checked', true);
                if (data.sedih) $('input[name="sedih"][value="' + data.sedih + '"]').prop('checked', true);
                if (data.cemas) $('input[name="cemas"][value="' + data.cemas + '"]').prop('checked', true);
                if (data.khawatir) $('input[name="khawatir"][value="' + data.khawatir + '"]').prop('checked', true);
                console.log('Updating badge for kesehatan-jiwa');
                $('#tabel-pemeriksaan-dewasa tr[data-service="kesehatan-jiwa"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            if (data.batuk || data.demam) {
                if (data.batuk) $('input[name="batuk_berdahak"][value="' + data.batuk + '"]').prop('checked', true);
                if (data.demam) $('input[name="demam"][value="' + data.demam + '"]').prop('checked', true);
                $('#tabel-pemeriksaan-dewasa tr[data-service="tuberkulosis"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            if (data.riwayat_dm || data.lama_riwayat_dm_dewasa || data.riwayat_ht || data.lama_riwayat_ht_dewasa || data.tinggi_badan || data.berat_badan || data.lingkar_perut || data.tekanan_sistolik || data.tekanan_diastolik || data.gds || data.gdp || data.kolesterol_lab || data.trigliserida) {
                if (data.riwayat_dm) {
                    $('input[name="riwayat_dm"][value="' + data.riwayat_dm + '"]').prop('checked', true);
                }
                if (typeof toggleLamaRiwayatDmDewasa === 'function') {
                    toggleLamaRiwayatDmDewasa(data.riwayat_dm || '');
                }
                if (data.riwayat_dm === 'Ya' && data.lama_riwayat_dm_dewasa !== undefined && data.lama_riwayat_dm_dewasa !== null) {
                    $('input[name="lama_riwayat_dm_dewasa"]').val(data.lama_riwayat_dm_dewasa);
                }
                if (data.riwayat_ht) $('input[name="riwayat_ht"][value="' + data.riwayat_ht + '"]').prop('checked', true);
                if (typeof toggleLamaRiwayatHtDewasa === 'function') {
                    toggleLamaRiwayatHtDewasa(data.riwayat_ht || '');
                }
                if (data.riwayat_ht === 'Ya' && data.lama_riwayat_ht_dewasa !== undefined && data.lama_riwayat_ht_dewasa !== null) {
                    $('input[name="lama_riwayat_ht_dewasa"]').val(data.lama_riwayat_ht_dewasa);
                }
                if (data.tinggi_badan) $('input[name="tinggi_badan"]').val(data.tinggi_badan);
                if (data.berat_badan) $('input[name="berat_badan"]').val(data.berat_badan);
                if (data.lingkar_perut) $('input[name="lingkar_perut"]').val(data.lingkar_perut);
                if (data.tekanan_sistolik) $('input[name="tekanan_sistolik"]').val(data.tekanan_sistolik);
                if (data.tekanan_diastolik) $('input[name="tekanan_diastolik"]').val(data.tekanan_diastolik);
                if (data.gds !== undefined && data.gds !== null) $('input[name="gds"]').val(data.gds);
                if (data.gdp !== undefined && data.gdp !== null) $('input[name="gdp"]').val(data.gdp);
                if (data.kolesterol_lab !== undefined && data.kolesterol_lab !== null) $('input[name="kolesterol"]').val(data.kolesterol_lab);
                if (data.trigliserida !== undefined && data.trigliserida !== null) $('input[name="trigliserida"]').val(data.trigliserida);
                $('#tabel-pemeriksaan-dewasa tr[data-service="antropometri-lab"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Skrining Indra (jika ada)
            if (data.hasil_serumen || data.hasil_infeksi_telinga || data.pendengaran || data.penglihatan || data.pupil || data.selaput_mata) {
                var pendengaranVal = data.pendengaran;
                if (pendengaranVal === 'Gangguan pendengaran') pendengaranVal = 'Curiga gangguan pendengaran';
                var penglihatanVal = data.penglihatan;
                if (penglihatanVal === 'Normal') penglihatanVal = 'Normal (visus 6/6 - 6/12)';
                if (penglihatanVal === 'Menggunakan Kacamata') penglihatanVal = 'Curiga gangguan penglihatan (visus <6/12)';
                var pupilVal = data.pupil || data.selaput_mata;
                if (pupilVal === 'Curiga kelainan mata') pupilVal = 'Curiga Katarak';

                if (data.hasil_serumen) $('input[name="hasil_serumen"][value="' + data.hasil_serumen + '"]').prop('checked', true);
                if (data.hasil_infeksi_telinga) $('input[name="hasil_infeksi_telinga"][value="' + data.hasil_infeksi_telinga + '"]').prop('checked', true);
                if (pendengaranVal) $('input[name="pendengaran"][value="' + pendengaranVal + '"]').prop('checked', true);
                if (penglihatanVal) $('input[name="penglihatan"]').filter(function() { return this.value === penglihatanVal; }).prop('checked', true);
                if (pupilVal) $('input[name="pupil"][value="' + pupilVal + '"]').prop('checked', true);
                console.log('Updating badge for skrining-indra');
                $('#tabel-pemeriksaan-dewasa tr[data-service="skrining-indra"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Skrining Gigi Dewasa (jika ada)
            if (data.karies || data.hilang || data.goyang || data.status || data.jumlah_karies) {
                if (data.karies) $('input[name="karies"][value="' + data.karies + '"]').prop('checked', true);
                if (data.hilang) $('input[name="hilang"][value="' + data.hilang + '"]').prop('checked', true);
                if (data.goyang) $('input[name="goyang"][value="' + data.goyang + '"]').prop('checked', true);
                if (data.status) $('input[name="status"][value="' + data.status + '"]').prop('checked', true);
                if (data.jumlah_karies) $('input[name="jumlah_karies"][value="' + data.jumlah_karies + '"]').prop('checked', true);
                console.log('Updating badge for skrining-gigi dewasa');
                // Update badge untuk skrining gigi dewasa (di tabel dewasa)
                $('#tabel-pemeriksaan-dewasa tr[data-service="skrining-gigi"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Skrining Gigi Anak (jika ada)
            if (data.karies || data.hilang || data.goyang || data.status || data.jumlah_karies) {
                if (data.karies) $('input[name="karies"][value="' + data.karies + '"]').prop('checked', true);
                if (data.hilang) $('input[name="hilang"][value="' + data.hilang + '"]').prop('checked', true);
                if (data.goyang) $('input[name="goyang"][value="' + data.goyang + '"]').prop('checked', true);
                if (data.status) $('input[name="status"][value="' + data.status + '"]').prop('checked', true);
                if (data.jumlah_karies) $('input[name="jumlah_karies"][value="' + data.jumlah_karies + '"]').prop('checked', true);
                console.log('Updating badge for skrining-gigi anak');
                // Update badge untuk skrining gigi anak (di tabel anak)
                $('#tabel-pemeriksaan-anak tr[data-service="skrining-gigi"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            }
            
            // Data Demografi Dewasa (jika ada)
            console.log('Checking demografi dewasa data:', {
                status_perkawinan: data.status_perkawinan,
                status_hamil: data.status_hamil,
                status_disabilitas: data.status_disabilitas
            });
            
            if (data.status_perkawinan || data.status_hamil || data.status_disabilitas) {
                console.log('Populating demografi dewasa form fields');
                if (data.status_perkawinan) $('input[name="status_perkawinan"][value="' + data.status_perkawinan + '"]').prop('checked', true);
                if (data.status_hamil) $('input[name="status_hamil"][value="' + data.status_hamil + '"]').prop('checked', true);
                if (data.status_disabilitas) $('input[name="status_disabilitas"][value="' + data.status_disabilitas + '"]').prop('checked', true);
                
                var demografiBadge = $('#tabel-pemeriksaan-dewasa tr[data-service="demografi"] .status-check');
                console.log('Demografi badge element found:', demografiBadge.length);
                demografiBadge.removeClass('badge-secondary').addClass('badge-success');
                console.log('Demografi badge updated to success');
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Data Berhasil Dimuat',
                text: 'Data skrining yang sudah ada telah dimuat ke form. Anda dapat mengedit dan menyimpan perubahan.',
                timer: 3000,
                showConfirmButton: false
            });
        }
        
        // Fungsi untuk mereset form
        function resetForm() {
            $('#nik').val('');
            $('#nama_lengkap').val('');
            $('#tanggal_lahir').val('');
            $('#jenis_kelamin').val('');
            $('#no_handphone').val('');
            $('#umur').val('');
            $('#umur_tahun').val('');
            $('#btn-ambil-data-sebelumnya').hide();
            window.previousSkriningData = null;
            window.previousDataPopulated = false;
            
            // Reset semua status badge
            $('.status-check').removeClass('badge-success').addClass('badge-secondary');
            
            // Sembunyikan semua tabel pemeriksaan
            $('#data-wali').hide();
            $('#tabel-pemeriksaan-anak').hide();
            $('#tabel-pemeriksaan-dewasa').hide();
            
            // Reset semua input form
            $('form')[0].reset();
        }
        
        function escapePopupValue(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function fillPopupPasienForm(data) {
            if (!data) return;

            $('#popup_no_ktp').val(data.no_ktp || '');
            $('#popup_no_tlp').val(data.no_tlp || '');
            $('#popup_nm_pasien').val(data.nm_pasien || '');
            $('#popup_nm_ibu').val(data.nm_ibu || '');
            $('#popup_jk').val(data.jk || '');
            $('#popup_tgl_lahir').val(formatDateForInput(data.tgl_lahir));
            $('#popup_tmp_lahir').val(data.tmp_lahir || '');
            $('#popup_gol_darah').val(data.gol_darah || '-');
            $('#popup_stts_nikah').val(data.stts_nikah || 'MENIKAH');
            $('#popup_agama').val(data.agama || 'ISLAM');
            $('#popup_pnd').val(data.pnd || '-');
            $('#popup_status').val(data.status || 'Kepala Keluarga');
            $('#popup_keluarga').val(data.keluarga || 'DIRI SENDIRI');
            $('#popup_namakeluarga').val(data.namakeluarga || '');
            $('#popup_no_kk').val(data.no_kk || '');
            $('#popup_data_posyandu').val(data.data_posyandu || '');
            $('#popup_alamat').val(data.alamat || '');
            $('#popup_kd_prop').val(data.kd_prop || '');
            $('#popup_kd_kab').val(data.kd_kab || '');
            $('#popup_kd_kec').val(data.kd_kec || '');
            $('#popup_kd_kel').val(data.kd_kel || '');
            $('#popup_kd_pj').val(data.kd_pj || '');
            $('#popup_no_peserta').val(data.no_peserta || '000');
            $('#popup_perusahaan_pasien').val(data.perusahaan_pasien || '-');
            $('#popup_pekerjaanpj').val(data.pekerjaanpj || 'Swasta');
            $('#popup_alamatpj').val(data.alamatpj || '');
            $('#popup_kelurahanpj').val(data.kelurahanpj || '');
            $('#popup_kecamatanpj').val(data.kecamatanpj || '');
            $('#popup_kabupatenpj').val(data.kabupatenpj || '');
            $('#popup_propinsipj').val(data.propinsipj || '');
            $('#popup_email').val(data.email || 'puskesmaskerjo@gmail.com');
            $('#popup_pekerjaan').val(data.pekerjaan || 'Swasta');
            $('#popup_nip').val(data.nip || '');
            $('#popup_suku_bangsa').val(data.suku_bangsa || 'JAWA');
            $('#popup_bahasa_pasien').val(data.bahasa_pasien || 'JAWA');
            $('#popup_cacat_fisik').val(data.cacat_fisik || 'TIDAK ADA');
        }

        // Fungsi untuk menampilkan popup input/edit pasien dan mengisi form utama
        function showPopupInputPasienBaru(nik, options) {
            options = options || {};
            var mode = options.mode === 'edit' ? 'edit' : 'create';
            var pasienData = options.data || {};
            var noRkmMedis = pasienData.no_rkm_medis || options.no_rkm_medis || '';
            nik = (nik || pasienData.no_ktp || '').trim();
            var nikReadonlyAttr = (mode === 'create' && nik) ? 'readonly' : '';
            var popupTitle = mode === 'edit' ? 'Edit Data Pasien' : 'Input Data Pasien';
            var popupKicker = mode === 'edit' ? 'Edit Pasien' : 'Registrasi Pasien Baru';
            var popupHeadline = mode === 'edit' ? 'Perbarui data pasien yang sudah tersimpan' : 'Lengkapi identitas pasien dengan data yang valid';
            var popupBadge = mode === 'edit' ? ('No. RM ' + escapePopupValue(noRkmMedis || '-')) : 'Mode Input Cepat';
            var confirmLabel = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan';
            var requestUrl = mode === 'edit'
                ? "{{ url('/pasien/update-skrining') }}/" + encodeURIComponent(noRkmMedis)
                : "{{ route('pasien.store-skrining') }}";

            Swal.fire({
                title: popupTitle,
                width: '96vw',
                customClass: {
                    popup: 'pasien-baru-swal',
                    title: 'pasien-baru-title',
                    htmlContainer: 'pasien-baru-html',
                    actions: 'pasien-baru-actions',
                    confirmButton: 'btn pasien-baru-confirm',
                    cancelButton: 'btn pasien-baru-cancel'
                },
                buttonsStyling: false,
                html:
                    '<div class="pasien-baru-modal text-left">' +
                    '<div class="pasien-baru-hero">' +
                    '<div>' +
                    '<div class="pasien-baru-kicker">' + popupKicker + '</div>' +
                    '<h6>' + popupHeadline + '</h6>' +
                    '</div>' +
                    '<div class="pasien-baru-badge">' + popupBadge + '</div>' +
                    '</div>' +
                    '<div class="pasien-baru-section">' +
                    '<div class="pasien-baru-section-title">Data Pokok <small>Identitas dasar pasien</small></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>NIK</label><input id="popup_no_ktp" type="text" class="form-control" maxlength="20" value="' + escapePopupValue(nik) + '" ' + nikReadonlyAttr + '></div></div><div class="col-md-6"><div class="form-group"><label>No Handphone</label><input id="popup_no_tlp" type="text" class="form-control" maxlength="40" placeholder="08xxxxxxxxxx"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Nama Lengkap</label><input id="popup_nm_pasien" type="text" class="form-control" maxlength="40"></div></div><div class="col-md-6"><div class="form-group"><label>Nama Ibu</label><input id="popup_nm_ibu" type="text" class="form-control" maxlength="40"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Jenis Kelamin</label><select id="popup_jk" class="form-control"><option value="">Pilih...</option><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div></div><div class="col-md-6"><div class="form-group"><label>Tanggal Lahir</label><input id="popup_tgl_lahir" type="date" class="form-control"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Tempat Lahir</label><input id="popup_tmp_lahir" type="text" class="form-control" maxlength="15"></div></div><div class="col-md-6"><div class="form-group"><label>Golongan Darah</label><select id="popup_gol_darah" class="form-control"><option value="">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="O">O</option><option value="AB">AB</option><option value="-" selected>-</option></select></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Status Nikah</label><select id="popup_stts_nikah" class="form-control"><option value="">Pilih...</option><option value="BELUM MENIKAH">BELUM MENIKAH</option><option value="MENIKAH" selected>MENIKAH</option><option value="JANDA">JANDA</option><option value="DUDHA">DUDHA</option><option value="JOMBLO">JOMBLO</option></select></div></div><div class="col-md-6"><div class="form-group"><label>Agama</label><select id="popup_agama" class="form-control"><option value="">Pilih...</option><option value="ISLAM" selected>ISLAM</option><option value="KRISTEN">KRISTEN</option><option value="HINDU">HINDU</option><option value="BUDHA">BUDHA</option><option value="KONGHUCU">KONGHUCU</option><option value="ALIRAN KEPERCAYAAN">ALIRAN KEPERCAYAAN</option></select></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Pendidikan</label><select id="popup_pnd" class="form-control"><option value="">Pilih...</option><option value="TS">TS</option><option value="TK">TK</option><option value="SD">SD</option><option value="SMP" selected>SMP</option><option value="SMA">SMA</option><option value="SLTA/SEDERAJAT">SLTA/SEDERAJAT</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option><option value="-">-</option></select></div></div><div class="col-md-6"><div class="form-group"><label>Status Keluarga</label><select id="popup_status" class="form-control"><option value="">Pilih...</option><option value="Kepala Keluarga">Kepala Keluarga</option><option value="Suami" selected>Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Menantu">Menantu</option><option value="Orang tua">Orang tua</option><option value="Mertua">Mertua</option><option value="Pembantu">Pembantu</option><option value="Famili Lain">Famili Lain</option><option value="Lainnya">Lainnya</option></select></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Hubungan Keluarga</label><select id="popup_keluarga" class="form-control"><option value="">Pilih...</option><option value="AYAH">AYAH</option><option value="IBU">IBU</option><option value="ISTRI">ISTRI</option><option value="SUAMI" selected>SUAMI</option><option value="SAUDARA">SAUDARA</option><option value="ANAK">ANAK</option><option value="DIRI SENDIRI">DIRI SENDIRI</option><option value="LAIN-LAIN">LAIN-LAIN</option></select></div></div><div class="col-md-6"><div class="form-group"><label>Nama Keluarga</label><input id="popup_namakeluarga" type="text" class="form-control" maxlength="50"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>No KK</label><input id="popup_no_kk" type="text" class="form-control" maxlength="20"></div></div><div class="col-md-6"><div class="form-group"><label>Kode Posyandu</label><input id="popup_data_posyandu" type="text" class="form-control" maxlength="70" list="posyanduOptions" placeholder="Ketik nama/kode posyandu"></div></div></div>' +
                    '</div>' +
                    '<datalist id="posyanduOptions"></datalist>' +
                    '<div class="pasien-baru-section">' +
                    '<div class="pasien-baru-section-title">Kode Wilayah <small>Alamat dan area domisili</small></div>' +
                    '<div class="form-group"><label>Alamat</label><textarea id="popup_alamat" class="form-control" rows="2" maxlength="200"></textarea></div>' +
                    '<div class="row"><div class="col-md-3"><div class="form-group"><label>Kode Provinsi</label><input id="popup_kd_prop" type="text" class="form-control" list="propinsiOptions" placeholder="Ketik nama/kode provinsi"></div></div><div class="col-md-3"><div class="form-group"><label>Kode Kabupaten</label><input id="popup_kd_kab" type="text" class="form-control" list="kabupatenOptions" placeholder="Ketik nama/kode kabupaten"></div></div><div class="col-md-3"><div class="form-group"><label>Kode Kecamatan</label><input id="popup_kd_kec" type="text" class="form-control" list="kecamatanOptions" placeholder="Ketik nama/kode kecamatan"></div></div><div class="col-md-3"><div class="form-group"><label>Kode Kelurahan</label><input id="popup_kd_kel" type="text" class="form-control" list="kelurahanOptions" placeholder="Ketik nama/kode kelurahan"></div></div></div>' +
                    '</div>' +
                    '<datalist id="propinsiOptions"></datalist><datalist id="kabupatenOptions"></datalist><datalist id="kecamatanOptions"></datalist><datalist id="kelurahanOptions"></datalist>' +
                    '<div class="pasien-baru-section">' +
                    '<div class="pasien-baru-section-title">Penanggung <small>Alamat penanggung dapat disamakan</small></div>' +
                    '<div class="d-flex justify-content-end mb-3"><label class="mb-0 d-flex align-items-center" style="font-size:0.8rem; text-transform:none; letter-spacing:normal;"><input id="popup_penanggung_check" type="checkbox" class="mr-2" style="transform: scale(0.9);">Samakan alamat penanggung dengan pasien</label></div>' +
                    '<div class="row"><div class="col-md-4"><div class="form-group"><label>Kode Penanggung</label><input id="popup_kd_pj" type="text" class="form-control" maxlength="3" list="penjabOptions" placeholder="Ketik nama/kode penanggung"></div></div><div class="col-md-4"><div class="form-group"><label>No Peserta</label><input id="popup_no_peserta" type="text" class="form-control" maxlength="25" value="000"></div></div><div class="col-md-4"><div class="form-group"><label>Perusahaan Pasien</label><input id="popup_perusahaan_pasien" type="text" class="form-control" maxlength="8" list="perusahaanOptions" placeholder="Ketik nama/kode perusahaan" value="-"></div></div></div>' +
                    '<datalist id="penjabOptions"></datalist><datalist id="perusahaanOptions"></datalist>' +
                    '<div class="row"><div class="col-md-4"><div class="form-group"><label>Pekerjaan PJ</label><input id="popup_pekerjaanpj" type="text" class="form-control" maxlength="35" value="Swasta"></div></div><div class="col-md-8"><div class="form-group"><label>Alamat PJ</label><input id="popup_alamatpj" type="text" class="form-control" maxlength="100"></div></div></div>' +
                    '<div class="row"><div class="col-md-4"><div class="form-group"><label>Kelurahan PJ</label><input id="popup_kelurahanpj" type="text" class="form-control" maxlength="60"></div></div><div class="col-md-4"><div class="form-group"><label>Kecamatan PJ</label><input id="popup_kecamatanpj" type="text" class="form-control" maxlength="60"></div></div><div class="col-md-4"><div class="form-group"><label>Kabupaten PJ</label><input id="popup_kabupatenpj" type="text" class="form-control" maxlength="60"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Provinsi PJ</label><input id="popup_propinsipj" type="text" class="form-control" maxlength="30"></div></div><div class="col-md-6"><div class="form-group"><label>Email</label><input id="popup_email" type="email" class="form-control" maxlength="50" value="puskesmaskerjo@gmail.com"></div></div></div>' +
                    '</div>' +
                    '<div class="pasien-baru-section">' +
                    '<div class="pasien-baru-section-title">Tambahan <small>Referensi dan metadata pasien</small></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Pekerjaan</label><input id="popup_pekerjaan" type="text" class="form-control" maxlength="60" value="Swasta"></div></div><div class="col-md-6"><div class="form-group"><label>NIP</label><input id="popup_nip" type="text" class="form-control" maxlength="30"></div></div></div>' +
                    '<div class="row"><div class="col-md-6"><div class="form-group"><label>Suku Bangsa (kode)</label><input id="popup_suku_bangsa" type="text" class="form-control" list="sukuBangsaOptions" placeholder="Ketik nama suku" value="JAWA"></div></div><div class="col-md-6"><div class="form-group"><label>Bahasa Pasien (kode)</label><input id="popup_bahasa_pasien" type="text" class="form-control" list="bahasaOptions" placeholder="Ketik nama bahasa" value="JAWA"></div></div></div>' +
                    '<div class="form-group"><label>Cacat Fisik (kode)</label><input id="popup_cacat_fisik" type="text" class="form-control" list="cacatOptions" placeholder="Ketik nama cacat fisik" value="TIDAK ADA"></div>' +
                    '</div>' +
                    '<datalist id="sukuBangsaOptions"></datalist><datalist id="bahasaOptions"></datalist><datalist id="cacatOptions"></datalist>' +
                    '</div>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: confirmLabel,
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                didOpen: function () {
                    function attachSearch(inputId, datalistId, url, buildParams, formatItem) {
                        var input = document.getElementById(inputId);
                        var fetchTimer;
                        if (!input) return;
                        var minLen = formatItem && typeof formatItem.minLen === 'number' ? formatItem.minLen : 2;
                        input.removeAttribute('list');
                        var group = input.closest('.form-group') || input.parentElement;
                        if (group) { group.style.position = 'relative'; }
                        var containerId = datalistId + '_dropdown';
                        var container = document.getElementById(containerId);
                        if (!container) {
                            container = document.createElement('div');
                            container.id = containerId;
                            container.className = 'popup-search-dropdown';
                            container.style.position = 'absolute';
                            container.style.left = '0';
                            container.style.right = '0';
                            container.style.top = '100%';
                            container.style.marginTop = '2px';
                            container.style.background = '#fff';
                            container.style.border = '1px solid #dee2e6';
                            container.style.borderRadius = '0.25rem';
                            container.style.boxShadow = '0 0.25rem 0.5rem rgba(0,0,0,0.1)';
                            container.style.zIndex = '1061';
                            container.style.maxHeight = '200px';
                            container.style.overflowY = 'auto';
                            container.style.display = 'none';
                            (group || input.parentElement).appendChild(container);
                        }
                        function render(items) {
                            var html = '';
                            items.forEach(function(item){
                                var val = formatItem.value(item);
                                var lab = formatItem.label(item);
                                html += '<div class="dropdown-item" data-value="' + val + '">' + lab + '</div>';
                            });
                            container.innerHTML = html;
                            container.style.display = items.length ? 'block' : 'none';
                            var nodes = container.querySelectorAll('.dropdown-item');
                            [].forEach.call(nodes, function(node){
                                node.addEventListener('mousedown', function(e){ e.preventDefault(); });
                                node.addEventListener('click', function(){
                                    input.value = node.getAttribute('data-value') || '';
                                    var event = document.createEvent('Event');
                                    event.initEvent('change', true, true);
                                    input.dispatchEvent(event);
                                    container.style.display = 'none';
                                });
                            });
                        }
                        function doFetch(q) {
                            clearTimeout(fetchTimer);
                            if (q.length < minLen) { container.style.display = 'none'; container.innerHTML = ''; return; }
                            fetchTimer = setTimeout(function() {
                                var params = buildParams ? buildParams(q) : { q: q };
                                $.ajax({ url: url, type: "GET", data: params, dataType: "json", success: function(list) {
                                    var items = [];
                                    if (Array.isArray(list)) {
                                        items = list;
                                    } else if (list && list.data && Array.isArray(list.data)) {
                                        items = list.data;
                                    }
                                    render(items);
                                }});
                            }, 200);
                        }
                        input.addEventListener('input', function() { doFetch(input.value.trim()); });
                        input.addEventListener('focus', function() { var q = input.value.trim(); if (q.length >= minLen) doFetch(q); });
                        input.addEventListener('blur', function(){ setTimeout(function(){ container.style.display = 'none'; }, 150); });
                    }

                    var kelEl = document.getElementById('kelurahan');
                    var desa = kelEl ? kelEl.value : '';
                    attachSearch('popup_data_posyandu', 'posyanduOptions', "{{ route('posyandu.search') }}", function(q){ return { q: q, desa: desa }; }, { value: function(i){ return i.kode_posyandu || ''; }, label: function(i){ return (i.nama_posyandu || '') + (i.desa ? ' ('+i.desa+')' : ''); } });
                    attachSearch('popup_kd_prop', 'propinsiOptions', "{{ route('wilayah.search.propinsi') }}", function(q){ return { q: q }; }, { value: function(i){ return i.kd_prop || i.id || ''; }, label: function(i){ return i.nm_prop || i.nama || ''; } });
                    attachSearch('popup_kd_kab', 'kabupatenOptions', "{{ route('wilayah.search.kabupaten') }}", function(q){ return { q: q, kd_prop: document.getElementById('popup_kd_prop').value || '' }; }, { value: function(i){ return i.kd_kab || i.id || ''; }, label: function(i){ return i.nm_kab || i.nama || ''; }, minLen: 0 });
                    attachSearch('popup_kd_kec', 'kecamatanOptions', "{{ route('wilayah.search.kecamatan') }}", function(q){ return { q: q, kd_kab: document.getElementById('popup_kd_kab').value || '' }; }, { value: function(i){ return i.kd_kec || i.id || ''; }, label: function(i){ return i.nm_kec || i.nama || ''; }, minLen: 0 });
                    attachSearch('popup_kd_kel', 'kelurahanOptions', "{{ route('wilayah.search.kelurahan') }}", function(q){ return { q: q, kd_kec: document.getElementById('popup_kd_kec').value || '' }; }, { value: function(i){ return i.kd_kel || i.id || ''; }, label: function(i){ return i.nm_kel || i.nama || ''; }, minLen: 0 });
                    attachSearch('popup_kd_pj', 'penjabOptions', "{{ route('penjab.search') }}", function(q){ return { q: q }; }, { value: function(i){ return i.kd_pj || ''; }, label: function(i){ return i.png_jawab || ''; } });
                    attachSearch('popup_perusahaan_pasien', 'perusahaanOptions', "{{ route('perusahaan.search') }}", function(q){ return { q: q }; }, { value: function(i){ return i.kode_perusahaan || ''; }, label: function(i){ return (i.nama_perusahaan || '') + (i.kota ? ' ('+i.kota+')' : ''); } });
                    attachSearch('popup_suku_bangsa', 'sukuBangsaOptions', "{{ route('ref.search.suku-bangsa') }}", function(q){ return { q: q }; }, { value: function(i){ return i.id || ''; }, label: function(i){ return i.nama_suku_bangsa || ''; } });
                    attachSearch('popup_bahasa_pasien', 'bahasaOptions', "{{ route('ref.search.bahasa-pasien') }}", function(q){ return { q: q }; }, { value: function(i){ return i.id || ''; }, label: function(i){ return i.nama_bahasa || ''; } });
                    attachSearch('popup_cacat_fisik', 'cacatOptions', "{{ route('ref.search.cacat-fisik') }}", function(q){ return { q: q }; }, { value: function(i){ return i.id || ''; }, label: function(i){ return i.nama_cacat || ''; } });

                    function resolveHierarchy(params) {
                        $.ajax({ url: "{{ route('wilayah.resolve') }}", type: "GET", data: params, dataType: "json", success: function(res){
                            if (res.kd_prop) document.getElementById('popup_kd_prop').value = res.kd_prop;
                            if (res.kd_kab) document.getElementById('popup_kd_kab').value = res.kd_kab;
                            if (res.kd_kec) document.getElementById('popup_kd_kec').value = res.kd_kec;
                            if (res.kd_kel) document.getElementById('popup_kd_kel').value = res.kd_kel;
                            var kelurahanpj = document.getElementById('popup_kelurahanpj');
                            var kecamatanpj = document.getElementById('popup_kecamatanpj');
                            var kabupatenpj = document.getElementById('popup_kabupatenpj');
                            var propinsipj = document.getElementById('popup_propinsipj');
                            if (kelurahanpj && res.nm_kel) kelurahanpj.value = res.nm_kel;
                            if (kecamatanpj && res.nm_kec) kecamatanpj.value = res.nm_kec;
                            if (kabupatenpj && res.nm_kab) kabupatenpj.value = res.nm_kab;
                            if (propinsipj && res.nm_prop) propinsipj.value = res.nm_prop;
                        }});
                    }

                    var kdKelEl = document.getElementById('popup_kd_kel');
                    var kdKecEl = document.getElementById('popup_kd_kec');
                    var kdKabEl = document.getElementById('popup_kd_kab');
                    var kdPropEl = document.getElementById('popup_kd_prop');
                    if (kdKelEl) kdKelEl.addEventListener('change', function(){ resolveHierarchy({ kd_kel: kdKelEl.value }); });
                    if (kdKecEl) kdKecEl.addEventListener('change', function(){ resolveHierarchy({ kd_kec: kdKecEl.value }); });
                    if (kdKabEl) kdKabEl.addEventListener('change', function(){ resolveHierarchy({ kd_kab: kdKabEl.value }); });
                    if (kdPropEl) kdPropEl.addEventListener('change', function(){ resolveHierarchy({ kd_prop: kdPropEl.value }); });

                    function tryAutofillFromAddress(addr, target) {
                        if (!addr) return;
                        var tokens = addr.split(',');
                        var last = tokens[tokens.length - 1].trim();
                        if (last.length < 3) return;
                        $.ajax({ url: "{{ route('wilayah.search.kelurahan') }}", type: "GET", data: { q: last }, dataType: "json", success: function(list){
                            if (Array.isArray(list) && list.length === 1) {
                                var kd = list[0].kd_kel || list[0].id;
                                document.getElementById('popup_kd_kel').value = kd;
                                resolveHierarchy({ kd_kel: kd });
                            }
                        }});
                    }

                    var alamatEl = document.getElementById('popup_alamat');
                    if (alamatEl) alamatEl.addEventListener('blur', function(){ tryAutofillFromAddress(alamatEl.value); });
                    var alamatPjEl = document.getElementById('popup_alamatpj');
                    if (alamatPjEl) alamatPjEl.addEventListener('blur', function(){ tryAutofillFromAddress(alamatPjEl.value, 'pj'); });

                    var penanggungCheck = document.getElementById('popup_penanggung_check');
                    if (penanggungCheck) penanggungCheck.addEventListener('change', function(){
                        if (penanggungCheck.checked) {
                            var alamatVal = document.getElementById('popup_alamat').value || '';
                            var alamatPj = document.getElementById('popup_alamatpj');
                            if (alamatPj) alamatPj.value = alamatVal;
                            var params = {
                                kd_prop: document.getElementById('popup_kd_prop').value || '',
                                kd_kab: document.getElementById('popup_kd_kab').value || '',
                                kd_kec: document.getElementById('popup_kd_kec').value || '',
                                kd_kel: document.getElementById('popup_kd_kel').value || ''
                            };
                            resolveHierarchy(params);
                        }
                    });

                    if (mode === 'edit') {
                        fillPopupPasienForm(pasienData);
                    }
                },
                preConfirm: function () {
                    if (mode === 'edit' && !noRkmMedis) {
                        Swal.showValidationMessage('No. Rekam Medis pasien tidak ditemukan.');
                        return false;
                    }

                    var no_ktp = document.getElementById('popup_no_ktp').value.trim();
                    var nm_pasien = document.getElementById('popup_nm_pasien').value.trim();
                    var tgl_lahir = document.getElementById('popup_tgl_lahir').value;
                    var jk = document.getElementById('popup_jk').value;
                    var nm_ibu = document.getElementById('popup_nm_ibu').value.trim();
                    if (!no_ktp || !nm_pasien || !tgl_lahir || !jk || !nm_ibu) {
                        Swal.showValidationMessage('Lengkapi NIK, Nama Lengkap, Tanggal Lahir, Jenis Kelamin, dan Nama Ibu');
                        return false;
                    }

                    var payload = {
                        no_ktp: no_ktp,
                        nm_pasien: nm_pasien,
                        jk: jk,
                        tgl_lahir: tgl_lahir,
                        tmp_lahir: document.getElementById('popup_tmp_lahir').value.trim(),
                        nm_ibu: nm_ibu,
                        no_tlp: document.getElementById('popup_no_tlp').value.trim(),
                        alamat: document.getElementById('popup_alamat').value.trim(),
                        gol_darah: document.getElementById('popup_gol_darah').value,
                        stts_nikah: document.getElementById('popup_stts_nikah').value,
                        agama: document.getElementById('popup_agama').value.trim(),
                        pnd: document.getElementById('popup_pnd').value,
                        keluarga: document.getElementById('popup_keluarga').value,
                        namakeluarga: document.getElementById('popup_namakeluarga').value.trim(),
                        no_kk: document.getElementById('popup_no_kk').value.trim(),
                        data_posyandu: document.getElementById('popup_data_posyandu').value.trim(),
                        kd_prop: document.getElementById('popup_kd_prop').value,
                        kd_kab: document.getElementById('popup_kd_kab').value,
                        kd_kec: document.getElementById('popup_kd_kec').value,
                        kd_kel: document.getElementById('popup_kd_kel').value,
                        kd_pj: document.getElementById('popup_kd_pj').value.trim(),
                        no_peserta: document.getElementById('popup_no_peserta').value.trim(),
                        perusahaan_pasien: document.getElementById('popup_perusahaan_pasien').value.trim(),
                        pekerjaanpj: document.getElementById('popup_pekerjaanpj').value.trim(),
                        alamatpj: document.getElementById('popup_alamatpj').value.trim(),
                        kelurahanpj: document.getElementById('popup_kelurahanpj').value.trim(),
                        kecamatanpj: document.getElementById('popup_kecamatanpj').value.trim(),
                        kabupatenpj: document.getElementById('popup_kabupatenpj').value.trim(),
                        propinsipj: document.getElementById('popup_propinsipj').value.trim(),
                        email: document.getElementById('popup_email').value.trim(),
                        pekerjaan: document.getElementById('popup_pekerjaan').value.trim(),
                        nip: document.getElementById('popup_nip').value.trim(),
                        suku_bangsa: document.getElementById('popup_suku_bangsa').value,
                        bahasa_pasien: document.getElementById('popup_bahasa_pasien').value,
                        cacat_fisik: document.getElementById('popup_cacat_fisik').value,
                        status: document.getElementById('popup_status').value
                    };

                    return fetch(requestUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(function(response) {
                        return response.json().catch(function() {
                            return {};
                        }).then(function(result) {
                            if (!response.ok || result.status === 'error') {
                                throw new Error(result.message || (mode === 'edit' ? 'Gagal memperbarui data pasien' : 'Gagal menyimpan data pasien baru'));
                            }

                            return result.data;
                        });
                    })
                    .catch(function(error) {
                        Swal.showValidationMessage(error.message || (mode === 'edit' ? 'Gagal memperbarui data pasien' : 'Gagal menyimpan data pasien baru'));
                    });
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#nik').val(result.value.no_ktp || '');
                    $('#nama_lengkap').val(result.value.nm_pasien || '');
                    $('#tanggal_lahir').val(formatDateForInput(result.value.tgl_lahir)).trigger('change');
                    $('#jenis_kelamin').val(result.value.jk || '').trigger('change');
                    $('#no_handphone').val(result.value.no_tlp || '');
                    $('#kelurahan').val(result.value.nm_kel || '');
                    $('#kode_posyandu').val(result.value.kode_posyandu || result.value.data_posyandu || '');
                    $('#nama_posyandu').val(result.value.nama_posyandu || '');
                    if (!$('#nama_posyandu').val() && $('#kode_posyandu').val()) {
                        $.ajax({
                            url: "{{ route('posyandu.get-by-kode') }}",
                            type: "GET",
                            data: { kode_posyandu: $('#kode_posyandu').val() },
                            dataType: "json",
                            success: function(res2) {
                                if (res2 && res2.status === 'success' && res2.data) {
                                    $('#nama_posyandu').val(res2.data.nama_posyandu || '');
                                }
                            }
                        });
                    }
                    $('#btn-ambil-data-sebelumnya').hide();
                    triggerAutoSaveIdentitas();
                    Swal.fire({
                        icon: 'success',
                        title: mode === 'edit' ? 'Data pasien berhasil diperbarui' : 'Pasien baru berhasil disimpan',
                        text: 'No. Rekam Medis: ' + (result.value.no_rkm_medis || '-') + '. Silakan lanjutkan pengisian skrining.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

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
                        
                        if (response.status === 'warning') {
                            // NIK sudah melakukan skrining pada tahun ini
                            if (response.allow_update && response.data) {
                                // Tampilkan notifikasi dan isi form dengan data yang sudah ada untuk diedit
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Data Skrining Ditemukan',
                                    html: 'NIK ini sudah melakukan skrining pada tahun ini.<br><br>' +
                                          'Data skrining tahun ini akan ditampilkan untuk dapat <strong>diedit atau diperbarui</strong>.',
                                    confirmButtonText: 'Lanjutkan Edit Tahun Ini',
                                    allowOutsideClick: false
                                }).then(() => {
                                    // Isi form dengan data yang sudah ada
                                    populateFormWithExistingData(response.data);
                                });
                            } else {
                                // Tampilkan pesan tidak bisa skrining lagi
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Skrining Sudah Dilakukan',
                                    html: 'NIK ini sudah melakukan skrining pada tahun ini.<br><br>' +
                                          'Skrining kesehatan hanya dapat dilakukan <strong>1 kali dalam 1 tahun</strong>.<br>' +
                                          'Silakan coba lagi tahun depan.',
                                    confirmButtonText: 'Mengerti',
                                    allowOutsideClick: false
                                }).then(() => {
                                    // Reset form setelah user menutup alert
                                    resetForm();
                                });
                            }
                            return; // Stop execution here
                        } else if (response.status === 'info') {
                            var data = response.data;
                            window.previousSkriningData = data;
                            window.previousDataPopulated = false;
                            $('#btn-ambil-data-sebelumnya').show();
                            Swal.fire({
                                icon: 'info',
                                title: 'Data Skrining Sebelumnya Tersedia',
                                text: 'Tekan tombol "Ambil Data Sebelumnya" untuk memuat ke form.',
                                timer: 2500,
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
                                        $('#tanggal_lahir').val(formatDateForInput(pasien.tgl_lahir)).trigger('change');
                                        $('#jenis_kelamin').val(pasien.jk).trigger('change');
                                        $('#no_handphone').val(pasien.no_tlp);
                                        $('#kelurahan').val(pasien.nm_kel || '');
                                        $('#nama_posyandu').val(pasien.nama_posyandu || '');
                                        $('#kode_posyandu').val(pasien.kode_posyandu || pasien.data_posyandu || '');
                                        if (!$('#nama_posyandu').val() && $('#kode_posyandu').val()) {
                                            $.ajax({
                                                url: "{{ route('posyandu.get-by-kode') }}",
                                                type: "GET",
                                                data: { kode_posyandu: $('#kode_posyandu').val() },
                                                dataType: "json",
                                                success: function(res2) {
                                                    if (res2 && res2.status === 'success' && res2.data) {
                                                        $('#nama_posyandu').val(res2.data.nama_posyandu || '');
                                                    }
                                                }
                                            });
                                        }
                                        $('#btn-ambil-data-sebelumnya').hide();
                                        
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Data ditemukan',
                                            text: 'Cek Kembali Data Anda Pastikan No Handphone Aktif dan dapat Menerima Whatapps',
                                            timer: 2500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        // Swal.fire({
                                        //     icon: 'info',
                                        //     title: 'Data tidak ditemukan',
                                        //     text: 'Data pasien tidak ditemukan, silahkan isi form secara manual',
                                        //     timer: 1500,
                                        //     showConfirmButton: false
                                        // }).then(() => {
                                        //     window.location.href = 'https://daftar.faskesku.my.id';
                                        // });
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Data tidak ditemukan',
                                            text: 'Data pasien tidak ditemukan, apakah Anda ingin mendaftarkan pasien baru?',
                                            showCancelButton: true,
                                            confirmButtonText: 'Ya, Daftarkan',
                                            cancelButtonText: 'Tidak',
                                            showConfirmButton: true
                                        }).then(function (result) {
                                            if (result.isConfirmed) {
                                                showPopupInputPasienBaru(nik);
                                            }
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
        
        function formatDateForInput(dateString) {
            if (!dateString) return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) return dateString;
            var m = dateString.match(/^(\d{4}-\d{2}-\d{2})[T\s]/);
            if (m && m[1]) return m[1];
            var normalized = dateString.replace(/(\.\d{3})\d+(Z)?$/, '$1$2');
            try {
                var date = new Date(normalized);
                if (isNaN(date.getTime())) return '';
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                return year + '-' + month + '-' + day;
            } catch (e) {
                return '';
            }
        }

        // Fungsi untuk mencari dan mengisi data wali berdasarkan NIK
        function cariWaliByNIK() {
            var nikWali = $('#nik_wali').val();
            if (nikWali.length > 0) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Mencari data wali...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Cari data wali di database pasien
                $.ajax({
                    url: "{{ route('pasien.get-by-nik') }}",
                    type: "GET",
                    data: {
                        nik: nikWali
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 'success') {
                            var wali = response.data;
                            $('#nama_wali').val(wali.nm_pasien);
                            $('#tanggal_lahir_wali').val(formatDateForInput(wali.tgl_lahir));
                            $('#jenis_kelamin_wali').val(wali.jk);
                            triggerAutoSaveIdentitas();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Data wali ditemukan',
                                text: 'Data wali berhasil diisi otomatis',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Data wali tidak ditemukan',
                                text: 'Data wali tidak ditemukan, silahkan isi form secara manual',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Gagal menghubungi server: ' + error,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK Wali kosong',
                    text: 'Silahkan masukkan NIK wali terlebih dahulu',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
        
        // Event saat tombol cari NIK diklik
                $('#cari-nik').on('click', function() {
                    cariPasienByNIK();
                });

        $('#btn-pasien-baru').on('click', function() {
            showPopupInputPasienBaru($('#nik').val());
        });

        $('#btn-edit-pasien').on('click', function() {
            var nik = ($('#nik').val() || '').trim();

            if (!nik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK kosong',
                    text: 'Masukkan atau cari NIK pasien terlebih dahulu.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            Swal.fire({
                title: 'Mengambil data pasien...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('pasien.get-by-nik') }}",
                type: "GET",
                data: { nik: nik },
                dataType: "json",
                success: function(response) {
                    Swal.close();

                    if (response && response.status === 'success' && response.data) {
                        showPopupInputPasienBaru(nik, {
                            mode: 'edit',
                            data: response.data
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'info',
                        title: 'Data pasien tidak ditemukan',
                        text: 'Pasien dengan NIK tersebut belum terdaftar.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal mengambil data pasien: ' + error,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
        
        // Event saat enter di input NIK
                $('#nik').on('keypress', function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        cariPasienByNIK();
                    }
                });
        
        $('#btn-ambil-data-sebelumnya').on('click', function() {
            if (window.previousSkriningData) {
                populateFormWithExistingData(window.previousSkriningData);
                $('.status-check').removeClass('badge-success').addClass('badge-secondary');
                $(this).hide();
            }
        });
        
        // Event saat tombol cari NIK Wali diklik
        $('#cari-nik-wali').on('click', function() {
            cariWaliByNIK();
        });
        
        // Event saat enter di input NIK Wali
        $('#nik_wali').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                cariWaliByNIK();
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
            checkDataWali();
        }
        
        function simpanIdentitas(options) {
            options = options || {};
            var identitas = ambilPayloadIdentitas();
            return $.ajax({
                url: "{{ route('api.skrining.simpan') }}",
                type: "POST",
                data: identitas,
                dataType: "json",
                global: options.global !== false
            });
        }

        var identitasAutoSaveTimer = null;
        var lastIdentitasSignature = null;
        var identitasAutoSaveInProgress = false;

        function ambilPayloadIdentitas() {
            var umurTahun = parseInt($('#umur_tahun').val(), 10);
            var isAnakDiBawahEnam = !isNaN(umurTahun) && umurTahun < 6;

            return {
                nik: ($('#nik').val() || '').trim(),
                nama_lengkap: ($('#nama_lengkap').val() || '').trim(),
                tanggal_lahir: $('#tanggal_lahir').val(),
                jenis_kelamin: $('#jenis_kelamin').val(),
                no_handphone: ($('#no_handphone').val() || '').trim(),
                umur: $('#umur').val(),
                nik_wali: isAnakDiBawahEnam ? (($('#nik_wali').val() || '').trim() || null) : null,
                nama_wali: isAnakDiBawahEnam ? (($('#nama_wali').val() || '').trim() || null) : null,
                tanggal_lahir_wali: isAnakDiBawahEnam ? ($('#tanggal_lahir_wali').val() || null) : null,
                jenis_kelamin_wali: isAnakDiBawahEnam ? ($('#jenis_kelamin_wali').val() || null) : null
            };
        }

        function identitasSiapAutoSave(payload) {
            return !!(payload.nik && payload.nama_lengkap && payload.tanggal_lahir && payload.jenis_kelamin);
        }

        function triggerAutoSaveIdentitas() {
            if (identitasAutoSaveTimer) {
                clearTimeout(identitasAutoSaveTimer);
            }

            identitasAutoSaveTimer = setTimeout(function() {
                var payload = ambilPayloadIdentitas();
                if (!identitasSiapAutoSave(payload)) return;

                var signature = JSON.stringify(payload);
                if (signature === lastIdentitasSignature) return;
                if (identitasAutoSaveInProgress) return;

                identitasAutoSaveInProgress = true;
                simpanIdentitas({ global: false })
                    .done(function() {
                        lastIdentitasSignature = signature;
                    })
                    .fail(function(xhr) {
                        console.warn('Auto-save identitas gagal', xhr && xhr.responseJSON ? xhr.responseJSON : xhr);
                    })
                    .always(function() {
                        identitasAutoSaveInProgress = false;
                    });
            }, 600);
        }

        $('#nama_lengkap, #no_handphone').on('blur change', function(e) {
            if (!e.originalEvent) return;
            triggerAutoSaveIdentitas();
        });

        $('#tanggal_lahir, #jenis_kelamin').on('change', function(e) {
            if (!e.originalEvent) return;
            triggerAutoSaveIdentitas();
        });

        $('#nik_wali, #nama_wali').on('blur change', function(e) {
            if (!e.originalEvent) return;
            triggerAutoSaveIdentitas();
        });

        $('#tanggal_lahir_wali, #jenis_kelamin_wali').on('change', function(e) {
            if (!e.originalEvent) return;
            triggerAutoSaveIdentitas();
        });

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
                    formData.status_perkawinan = $('input[name="status_perkawinan"]:checked').val() || $('input[name="status_menikah"]:checked').val();
                    formData.status_hamil = $('input[name="status_hamil"]:checked').val();
                    formData.status_disabilitas = $('input[name="status_disabilitas"]:checked').val();
                    formData.kode_posyandu = $('#kode_posyandu').val();
                    formData.petugas_entri = $('#petugas_entri').val();
                    formData.status_petugas = $('#status_petugas').val();
                    break;
                
                case 'demografi-anak':
                    url = "{{ route('api.skrining.demografi-anak') }}";
                    formData.status_disabilitas_anak = $('input[name="status_disabilitas"]:checked').val();
                    formData.pendidikan_anak = $('input[name="pendidikan_anak"]:checked').val();
                    formData.tinggal_dengan_ortu = $('input[name="tinggal_dengan_ortu"]:checked').val();
                    formData.pengasuh_utama = $('input[name="pengasuh_utama"]:checked').val();
                    formData.jumlah_saudara = $('input[name="jumlah_saudara"]:checked').val();
                    formData.alamat_anak = $('textarea[name="alamat_anak"]').val();
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
                    var scope = $('#modalHati');
                    formData.riwayat_hepatitis = scope.find('input[name="riwayat_hepatitis"]:checked').val() || null;
                    formData.riwayat_kuning = scope.find('input[name="riwayat_kuning"]:checked').val() || null;
                    formData.hubungan_intim = scope.find('input[name="hubungan_intim"]:checked').val() || null;
                    formData.riwayat_transfusi = scope.find('input[name="riwayat_transfusi"]:checked').val() || null;
                    formData.riwayat_tindik = scope.find('input[name="riwayat_tindik"]:checked').val() || null;
                    formData.narkoba_suntik = scope.find('input[name="narkoba_suntik"]:checked').val() || null;
                    formData.odhiv = scope.find('input[name="odhiv"]:checked').val() || null;
                    formData.riwayat_tattoo = scope.find('input[name="riwayat_tattoo"]:checked').val() || null;
                    formData.kolesterol = scope.find('input[name="kolesterol"]:checked').val() || null;
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
                
                case 'kanker-usus':
                    url = "{{ route('api.skrining.kanker-usus') }}";
                    formData.kanker_usus_1 = $('input[name="kanker_usus_1"]:checked').val();
                    formData.kanker_usus_2 = $('input[name="kanker_usus_2"]:checked').val();
                    break;
                
                case 'faktor-resiko-tb':
                    url = "{{ route('api.skrining.faktor-resiko-tb') }}";
                    formData.faktor_resiko_tb = $('input[name="faktor_resiko_tb"]:checked').val();
                    break;
                
                case 'kanker-paru':
                    url = "{{ route('api.skrining.kanker-paru') }}";
                    formData.kanker_paru_1 = $('input[name="kanker_paru_1"]:checked').val();
                    formData.kanker_paru_2 = $('input[name="kanker_paru_2"]:checked').val();
                    formData.kanker_paru_3 = $('input[name="kanker_paru_3"]:checked').val();
                    formData.kanker_paru_4 = $('input[name="kanker_paru_4"]:checked').val();
                    break;
                
                case 'aktivitas-fisik':
                    url = "{{ route('api.skrining.aktivitas-fisik') }}";
                    
                    // Q1 (Field differs)
                    formData.frekuensi_olahraga = $('input[name="frekuensi_olahraga"]:checked').val();
                    formData.frekuensi_olahraga_1 = $('input[name="frekuensi_olahraga_1"]').val();
                    formData.frekuensi_olahraga_2 = $('input[name="frekuensi_olahraga_2"]').val();
                    
                    // Q2 - Q6
                    for (let i = 2; i <= 6; i++) {
                        formData['aktivitas_fisik_' + i] = $('input[name="aktivitas_fisik_' + i + '"]:checked').val();
                        formData['aktivitas_fisik_' + i + '_hari'] = $('input[name="aktivitas_fisik_' + i + '_hari"]').val();
                        formData['aktivitas_fisik_' + i + '_menit'] = $('input[name="aktivitas_fisik_' + i + '_menit"]').val();
                    }
                    break;
                
                case 'tuberkulosis':
                    url = "{{ route('api.skrining.tuberkulosis') }}";
                    formData.riwayat_tbc = $('input[name="riwayat_tbc"]:checked').val();
                    formData.jenis_tbc = $('input[name="jenis_tbc"]:checked').val();
                    formData.batuk_berdahak = $('input[name="batuk_berdahak"]:checked').val();
                    formData.demam = $('input[name="demam"]:checked').val();
                    break;
                
                case 'antropometri-lab':
                    url = "{{ route('api.skrining.antropometri-lab') }}";
                    
                    formData.riwayat_dm = $('input[name="riwayat_dm"]:checked').val();
                    formData.lama_riwayat_dm_dewasa = formData.riwayat_dm === 'Ya'
                        ? ($('input[name="lama_riwayat_dm_dewasa"]').val() || null)
                        : null;
                    formData.riwayat_ht = $('input[name="riwayat_ht"]:checked').val();
                    formData.lama_riwayat_ht_dewasa = formData.riwayat_ht === 'Ya'
                        ? ($('input[name="lama_riwayat_ht_dewasa"]').val() || null)
                        : null;

                    // Untuk nilai numerik, pastikan selalu bernilai valid (null atau angka)
                    formData.tinggi_badan = $('input[name="tinggi_badan"]').val() || null;
                    formData.berat_badan = $('input[name="berat_badan"]').val() || null;
                    formData.lingkar_perut = $('input[name="lingkar_perut"]').val() || null;
                    formData.tekanan_sistolik = $('input[name="tekanan_sistolik"]').val() || null;
                    formData.tekanan_diastolik = $('input[name="tekanan_diastolik"]').val() || null;
                    formData.tekanan_sistolik_2 = $('input[name="tekanan_sistolik_2"]').val() || null;
                    formData.tekanan_diastolik_2 = $('input[name="tekanan_diastolik_2"]').val() || null;
                    
                    // Untuk data laboratorium, nilai default 0 jika kosong
                    var gdsVal = $('input[name="gds"]').val();
                    formData.gds = (gdsVal === '' || gdsVal === null || isNaN(parseFloat(gdsVal))) ? 0 : parseFloat(gdsVal);
                    
                    var gdpVal = $('input[name="gdp"]').val();
                    formData.gdp = (gdpVal === '' || gdpVal === null || isNaN(parseFloat(gdpVal))) ? 0 : parseFloat(gdpVal);
                    
                    var kolesterolVal = $('input[name="kolesterol_lab"]').val();
                    formData.kolesterol_lab = (kolesterolVal === '' || kolesterolVal === null || isNaN(parseFloat(kolesterolVal))) ? 0 : parseFloat(kolesterolVal);
                    
                    var trigliseridaVal = $('input[name="trigliserida"]').val();
                    formData.trigliserida = (trigliseridaVal === '' || trigliseridaVal === null || isNaN(parseFloat(trigliseridaVal))) ? 0 : parseFloat(trigliseridaVal);
                    
                    console.log('Data antropometri yang akan dikirim:', formData);
                    break;

                case 'penyakit-tropis':
                    url = "{{ route('api.skrining.penyakit-tropis') }}";
                    formData.frambusia = $('input[name="frambusia"]:checked').val();
                    formData.kusta = $('input[name="kusta"]:checked').val();
                    formData.skabies = $('input[name="skabies"]:checked').val();
                    break;
                
                case 'skrining-indra':
                    url = "{{ route('api.skrining.skrining-indra') }}";
                    formData.hasil_serumen = $('input[name="hasil_serumen"]:checked').val();
                    formData.hasil_infeksi_telinga = $('input[name="hasil_infeksi_telinga"]:checked').val();
                    formData.pendengaran = $('input[name="pendengaran"]:checked').val();
                    formData.penglihatan = $('input[name="penglihatan"]:checked').val();
                    formData.pupil = $('input[name="pupil"]:checked').val();
                    break;
                
                case 'skrining-gigi':
                    url = "{{ route('api.skrining.skrining-gigi') }}";
                    formData.karies = $('input[name="karies"]:checked').val();
                    formData.hilang = $('input[name="hilang"]:checked').val();
                    formData.goyang = $('input[name="goyang"]:checked').val();
                    formData.jumlah_karies = $('input[name="jumlah_karies"]:checked').val();
                    break;
                
                case 'gangguan-fungsional':
                    url = "{{ route('api.skrining.gangguan-fungsional') }}";
                    // Ambil teks label dari setiap radio button yang dipilih di form gangguan fungsional
                    formData.bab = $('input[name="bab"]:checked').next('label').text();
                    formData.bak = $('input[name="bak"]:checked').next('label').text();
                    formData.membersihkan_diri = $('input[name="membersihkan_diri"]:checked').next('label').text();
                    formData.penggunaan_jamban = $('input[name="penggunaan_jamban"]:checked').next('label').text();
                    formData.makan_minum = $('input[name="makan_minum"]:checked').next('label').text();
                    formData.berubah_sikap = $('input[name="berubah_sikap"]:checked').next('label').text();
                    formData.berpindah = $('input[name="berpindah"]:checked').next('label').text();
                    formData.memakai_baju = $('input[name="memakai_baju"]:checked').next('label').text();
                    formData.naik_tangga = $('input[name="naik_tangga"]:checked').next('label').text();
                    formData.mandi = $('input[name="mandi"]:checked').next('label').text();
                    
                    // Hitung total skor dan tingkat ketergantungan untuk disimpan (menggunakan nilai numerik)
                    var totalRaw = 0;
                    var babVal = $('input[name="bab"]:checked').val();
                    var bakVal = $('input[name="bak"]:checked').val();
                    var membersihkanDiriVal = $('input[name="membersihkan_diri"]:checked').val();
                    var penggunaanJambanVal = $('input[name="penggunaan_jamban"]:checked').val();
                    var makanMinumVal = $('input[name="makan_minum"]:checked').val();
                    var berubahSikapVal = $('input[name="berubah_sikap"]:checked').val();
                    var berpindahVal = $('input[name="berpindah"]:checked').val();
                    var memakaiBajuVal = $('input[name="memakai_baju"]:checked').val();
                    var naikTanggaVal = $('input[name="naik_tangga"]:checked').val();
                    var mandiVal = $('input[name="mandi"]:checked').val();
                    
                    if (babVal) totalRaw += parseInt(babVal);
                    if (bakVal) totalRaw += parseInt(bakVal);
                    if (membersihkanDiriVal) totalRaw += parseInt(membersihkanDiriVal);
                    if (penggunaanJambanVal) totalRaw += parseInt(penggunaanJambanVal);
                    if (makanMinumVal) totalRaw += parseInt(makanMinumVal);
                    if (berubahSikapVal) totalRaw += parseInt(berubahSikapVal);
                    if (berpindahVal) totalRaw += parseInt(berpindahVal);
                    if (memakaiBajuVal) totalRaw += parseInt(memakaiBajuVal);
                    if (naikTanggaVal) totalRaw += parseInt(naikTanggaVal);
                    if (mandiVal) totalRaw += parseInt(mandiVal);
                    
                    // Kalikan dengan 5 untuk mendapatkan skor total 100
                    var totalFinal = totalRaw * 5;
                    formData.total_skor_barthel = totalFinal;
                    
                    // Tentukan tingkat ketergantungan berdasarkan skor
                    var tingkatKetergantungan = '';
                    if (totalFinal >= 0 && totalFinal <= 20) {
                        tingkatKetergantungan = 'Ketergantungan Total';
                    } else if (totalFinal >= 21 && totalFinal <= 60) {
                        tingkatKetergantungan = 'Ketergantungan Berat';
                    } else if (totalFinal >= 61 && totalFinal <= 90) {
                        tingkatKetergantungan = 'Ketergantungan Sedang';
                    } else if (totalFinal >= 91 && totalFinal <= 99) {
                        tingkatKetergantungan = 'Ketergantungan Ringan';
                    } else if (totalFinal == 100) {
                        tingkatKetergantungan = 'Mandiri';
                    }
                    formData.tingkat_ketergantungan = tingkatKetergantungan;
                    
                    console.log('Data Barthel Index yang akan dikirim:', formData);
                    break;
                
                case 'skrining-puma':
                    url = "{{ route('api.skrining.skrining-puma') }}";
                    formData.riwayat_merokok = $('input[name="riwayat_merokok"]:checked').val();
                    formData.napas_pendek = $('input[name="napas_pendek"]:checked').val();
                    formData.dahak = $('input[name="dahak"]:checked').val();
                    formData.batuk = $('input[name="batuk_puma"]:checked').val(); // Menggunakan batuk_puma tetapi menyimpan ke field batuk
                    formData.spirometri = $('input[name="spirometri"]:checked').val();
                    break;
                
                case 'gejala-dm-anak':
                    url = "{{ route('api.skrining.gejala-dm-anak') }}";
                    formData.pernah_dm_oleh_dokter = $('input[name="pernah_dm_oleh_dokter"]:checked').val();
                    formData.lama_anak_dm = $('input[name="lama_anak_dm"]').val();
                    formData.sering_lapar = $('input[name="sering_lapar"]:checked').val();
                    formData.sering_haus = $('input[name="sering_haus"]:checked').val();
                    formData.berat_turun = $('input[name="berat_turun"]:checked').val();
                    formData.riwayat_diabetes_ortu = $('input[name="riwayat_diabetes_ortu"]:checked').val();
                    break;

                case 'riwayat-imunisasi-balita':
                    url = "{{ route('api.skrining.riwayat-imunisasi-balita') }}";
                    formData.imunisasi_inti = $('select[name="imunisasi_inti"]').val();
                    formData.imunisasi_lanjutan = $('select[name="imunisasi_lanjutan"]').val();
                    for (let i = 1; i <= 18; i++) {
                        formData['imunisasi_lanjutan_' + i] = $('select[name="imunisasi_lanjutan_' + i + '"]').val();
                    }
                    break;

                case 'hepatitis-balita':
                    url = "{{ route('api.skrining.hepatitis-balita') }}";
                    formData.imunisasi_lanjutan_1 = $('input[name="hepatitis_balita"]:checked').val();
                    break;

                case 'berat-lahir-balita':
                    url = "{{ route('api.skrining.berat-lahir-balita') }}";
                    formData.berat_lahir = $('input[name="berat_lahir"]').val();
                    formData.berat_badan_balita = $('input[name="berat_badan_balita"]').val();
                    break;

                case 'pjb-balita':
                    url = "{{ route('api.skrining.pjb-balita') }}";
                    formData.pjb_tangan_kanan = $('input[name="pjb_tangan_kanan"]').val();
                    formData.pjb_kaki = $('input[name="pjb_kaki"]').val();
                    break;

                case 'darah-tumit-balita':
                    url = "{{ route('api.skrining.darah-tumit-balita') }}";
                    formData.darah_tumit = $('input[name="darah_tumit"]:checked').val();
                    break;

                case 'shk-g6pd-hak-balita':
                    url = "{{ route('api.skrining.shk-g6pd-hak-balita') }}";
                    formData.shk = $('input[name="shk"]:checked').val();
                    formData.g6pd = $('input[name="g6pd"]:checked').val();
                    formData.hak = $('input[name="hak"]:checked').val();
                    break;

                case 'konfirmasi-shk-g6pd-hak-balita':
                    url = "{{ route('api.skrining.konfirmasi-shk-g6pd-hak-balita') }}";
                    formData.konfirmasi_shk = $('input[name="konfirmasi_shk"]:checked').val();
                    formData.konfirmasi_g6pd = $('input[name="konfirmasi_g6pd"]:checked').val();
                    formData.konfirmasi_hak = $('input[name="konfirmasi_hak"]:checked').val();
                    break;

                case 'edukasi-warna-kulit-balita':
                    url = "{{ route('api.skrining.edukasi-warna-kulit-balita') }}";
                    formData.edukasi_warna_kulit = $('input[name="edukasi_warna_kulit"]:checked').val();
                    formData.hasil_kreamer = $('input[name="hasil_kreamer"]:checked').val();
                    break;
                
                case 'perkembangan-3-6-tahun':
                    url = "{{ route('api.skrining.perkembangan-3-6-tahun') }}";
                    formData.gangguan_emosi = $('input[name="gangguan_emosi"]:checked').val();
                    formData.hiperaktif = $('input[name="hiperaktif"]:checked').val();
                    break;
                
                case 'talasemia':
                    url = "{{ route('api.skrining.talasemia') }}";
                    formData.riwayat_keluarga = $('input[name="riwayat_keluarga"]:checked').val();
                    formData.pembawa_sifat = $('input[name="pembawa_sifat"]:checked').val();
                    break;
                
                case 'tuberkulosis-bayi-anak':
                    url = "{{ route('api.skrining.tuberkulosis-bayi-anak') }}";
                    formData.batuk_lama = $('input[name="batuk_lama"]:checked').val();
                    formData.berat_turun_tbc = $('input[name="berat_turun_tbc"]:checked').val();
                    formData.berat_tidak_naik = $('input[name="berat_tidak_naik"]:checked').val();
                    formData.nafsu_makan_berkurang = $('input[name="nafsu_makan_berkurang"]:checked').val();
                    formData.kontak_tbc = $('input[name="kontak_tbc"]:checked').val();
                    break;
                
                case 'skrining-pertumbuhan':
                    url = "{{ route('api.skrining.skrining-pertumbuhan') }}";
                    formData.nik = $('#nik').val();
                    formData.berat_badan = $('#berat_badan').val();
                    formData.tinggi_badan = $('#tinggi_badan').val();
                    formData.posisi_ukur = $('#posisi_ukur').val();
                    formData.status_gizi_bb_u = $('#status_gizi_bb_u').val();
                    formData.status_gizi_pb_u = $('#status_gizi_pb_u').val();
                    formData.status_gizi_bb_pb = $('#status_gizi_bb_pb').val();
                    formData.hasil_imt_u = $('#hasil_imt_u').val();
                    formData.status_lingkar_kepala = $('#status_lingkar_kepala').val();
                    
                    // Validasi client-side untuk field wajib
                    if (!formData.berat_badan || formData.berat_badan === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Berat badan harus diisi',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        return;
                    }
                    
                    if (!formData.tinggi_badan || formData.tinggi_badan === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Tinggi badan harus diisi',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        return;
                    }

                    if (!formData.posisi_ukur || formData.posisi_ukur === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Posisi pengukuran harus dipilih',
                            timer: 3000,
                            showConfirmButton: false
                        });
                        return;
                    }
                    
                    // Debug: log nilai yang diambil
                    console.log('Data skrining pertumbuhan:', formData);
                    break;
                
                case 'kpsp':
                    url = "{{ route('api.skrining.kpsp') }}";
                    formData.nik = $('#nik').val();
                    formData.hasil_kpsp = $('select[name="hasil_kpsp"]').val();
                    break;
                
                case 'telinga-mata':
                    url = "{{ route('api.skrining.telinga-mata') }}";
                    formData.nik = $('#nik').val();
                    formData.hasil_tes_dengar = $('select[name="hasil_tes_dengar"]').val();
                    formData.hasil_tes_lihat = $('select[name="hasil_tes_lihat"]').val();
                    formData.hasil_serumen = $('select[name="hasil_serumen"]').val();
                    formData.hasil_infeksi_telinga = $('select[name="hasil_infeksi_telinga"]').val();
                    formData.selaput_mata = $('select[name="selaput_mata"]').val();
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
            
            // Tambahkan data wali jika anak di bawah 6 tahun
            if (parseInt($('#umur_tahun').val()) < 6) {
                formData.nik_wali = $('#nik_wali').val();
                formData.nama_wali = $('#nama_wali').val();
                formData.tanggal_lahir_wali = $('#tanggal_lahir_wali').val();
                formData.jenis_kelamin_wali = $('#jenis_kelamin_wali').val();
            }

            if (formType === 'demografi-anak' && parseInt($('#umur_tahun').val()) < 6) {
                var missing = [];
                if (!formData.nik_wali) missing.push('NIK Wali');
                if (!formData.nama_wali) missing.push('Nama Wali');
                if (!formData.tanggal_lahir_wali) missing.push('Tanggal Lahir Wali');
                if (!formData.jenis_kelamin_wali) missing.push('Jenis Kelamin Wali');
                if (missing.length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Harap isi: ' + missing.join(', '),
                        timer: 3000,
                        showConfirmButton: false
                    });
                    return;
                }
            }
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan data...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            simpanIdentitas().done(function() {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                    console.log('Data berhasil disimpan:', response);
                    console.log('FormType:', formType);
                    console.log('Selector yang digunakan:', 'tr[data-service="' + formType + '"] .status-check');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Ubah status pemeriksaan menjadi selesai (ikon hijau)
                    // Cek apakah ini untuk tabel dewasa atau anak
                    var selector = '';
                    if (formType === 'demografi' || formType === 'hati' || formType === 'kanker-leher-rahim' || 
                        formType === 'kesehatan-jiwa' || formType === 'perilaku-merokok' || formType === 'tekanan-darah' || 
                        formType === 'aktivitas-fisik' || formType === 'tuberkulosis' || formType === 'antropometri-lab' || 
                        formType === 'skrining-puma' || formType === 'skrining-indra' || formType === 'skrining-gigi' || 
                        formType === 'gangguan-fungsional') {
                        selector = '#tabel-pemeriksaan-dewasa tr[data-service="' + formType + '"] .status-check';
                    } else {
                        selector = '#tabel-pemeriksaan-anak tr[data-service="' + formType + '"] .status-check';
                    }
                    
                    var badgeElement = $(selector);
                    console.log('Badge element found:', badgeElement.length);
                    badgeElement.removeClass('badge-secondary').addClass('badge-success');
                    console.log('Badge class after update:', badgeElement.attr('class'));
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
            }).fail(function(xhr){
                var msg = 'Gagal menyimpan data identitas';
                if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errorMessages = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMessages += value + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi identitas gagal',
                        html: errorMessages,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyimpan identitas',
                        html: xhr.responseJSON.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        html: msg + '<br>Kode: ' + (xhr ? xhr.status : ''),
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Expose simpanDataForm to global scope
        window.simpanDataForm = simpanDataForm;
        
        // Fungsi untuk mengecek dan mengubah status badge berdasarkan isian form
        function updateBadgeStatus() {
            // Cek skrining pertumbuhan
            var beratBadan = $('#berat_badan').val();
            var tinggiBadan = $('#tinggi_badan').val();
            var posisiUkur = $('#posisi_ukur').val();
            if (beratBadan && tinggiBadan && posisiUkur) {
                $('#tabel-pemeriksaan-anak tr[data-service="skrining-pertumbuhan"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            } else {
                $('#tabel-pemeriksaan-anak tr[data-service="skrining-pertumbuhan"] .status-check').removeClass('badge-success').addClass('badge-secondary');
            }
            
            // Cek KPSP
            var hasilKpsp = $('select[name="hasil_kpsp"]').val();
            if (hasilKpsp) {
                $('tr[data-service="kpsp"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            } else {
                $('tr[data-service="kpsp"] .status-check').removeClass('badge-success').addClass('badge-secondary');
            }
            
            // Cek Telinga Mata
            var hasilTesDengar = $('select[name="hasil_tes_dengar"]').val();
            var hasilTesLihat = $('select[name="hasil_tes_lihat"]').val();
            if (hasilTesDengar || hasilTesLihat) {
                $('tr[data-service="skrining-telinga-mata"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            } else {
                $('tr[data-service="skrining-telinga-mata"] .status-check').removeClass('badge-success').addClass('badge-secondary');
            }
            
            // Cek Tekanan Darah
            var sistole = $('#sistole').val();
            var diastole = $('#diastole').val();
            if (sistole && diastole) {
                $('tr[data-service="tekanan-darah"] .status-check').removeClass('badge-secondary').addClass('badge-success');
            } else {
                $('tr[data-service="tekanan-darah"] .status-check').removeClass('badge-success').addClass('badge-secondary');
            }
        }
        
        // Event listener untuk memantau perubahan input
        $(document).on('input change', '#berat_badan, #tinggi_badan, #posisi_ukur, #sistole, #diastole, select[name="hasil_kpsp"], select[name="hasil_tes_dengar"], select[name="hasil_tes_lihat"]', function() {
            updateBadgeStatus();
        });
        
        // Panggil updateBadgeStatus saat halaman dimuat
        $(document).ready(function() {
            updateBadgeStatus();
        });
    }
</script>
@endsection
