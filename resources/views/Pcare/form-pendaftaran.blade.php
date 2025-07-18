@extends('adminlte::page')

@section('title', 'Pendaftaran PCare BPJS')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-clipboard-list text-primary"></i> Pendaftaran PCare BPJS</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Pendaftaran PCare</li>
    </ol>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Form Pendaftaran PCare</h3>
            </div>
            <div class="card-body">
                <!-- Tampilkan tab menu untuk Add dan Delete pendaftaran -->
                <ul class="nav nav-tabs mb-4" id="pcareTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="add-tab" data-toggle="tab" href="#add-content" role="tab"
                            aria-controls="add-content" aria-selected="true">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Pendaftaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="delete-tab" data-toggle="tab" href="#delete-content" role="tab"
                            aria-controls="delete-content" aria-selected="false">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Pendaftaran
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="pcareTabContent">
                    <!-- Tab Tambah Pendaftaran -->
                    <div class="tab-pane fade show active" id="add-content" role="tabpanel" aria-labelledby="add-tab">
                        <form id="form-pendaftaran-pcare" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-0 mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-user-circle mr-2"></i>Data Pasien</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="no_rawat">No. Rawat</label>
                                                <input type="text" class="form-control" id="no_rawat" name="no_rawat"
                                                    required>
                                            </div>

                                            <div class="form-group">
                                                <label for="no_rkm_medis">No. Rekam Medis</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="no_rkm_medis"
                                                        name="no_rkm_medis" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            id="btn-cari-pasien">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="nm_pasien">Nama Pasien</label>
                                                <input type="text" class="form-control" id="nm_pasien" name="nm_pasien"
                                                    readonly>
                                            </div>

                                            <div class="form-group">
                                                <label for="noKartu">No. Kartu BPJS</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="noKartu" name="noKartu"
                                                        required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            id="btn-cek-peserta">
                                                            <i class="fas fa-id-card"></i> Cek Peserta
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="kdProviderPeserta">Kode Provider Peserta</label>
                                                <input type="text" class="form-control" id="kdProviderPeserta"
                                                    name="kdProviderPeserta" required>
                                            </div>

                                            <!-- Detail Peserta BPJS - awalnya tersembunyi -->
                                            <div class="card border-primary mt-3 mb-3" id="detail-peserta-card"
                                                style="display: none;">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0"><i class="fas fa-id-card-alt mr-2"></i>Detail
                                                        Peserta BPJS</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Nomor Kartu</label>
                                                                <p class="form-control-static font-weight-bold"
                                                                    id="det-noKartu">-</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <p class="form-control-static font-weight-bold"
                                                                    id="det-status">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama Lengkap</label>
                                                        <p class="form-control-static font-weight-bold" id="det-nama">-
                                                        </p>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Hubungan Keluarga</label>
                                                                <p class="form-control-static"
                                                                    id="det-hubunganKeluarga">-</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Jenis Kelamin</label>
                                                                <p class="form-control-static" id="det-sex">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Tanggal Lahir</label>
                                                                <p class="form-control-static" id="det-tglLahir">-</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Jenis Peserta</label>
                                                                <p class="form-control-static" id="det-jnsPeserta">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Kelas</label>
                                                                <p class="form-control-static" id="det-kelas">-</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Provider</label>
                                                                <p class="form-control-static" id="det-provider">-</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Informasi Kunjungan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="tglDaftar">Tanggal Pendaftaran</label>
                                                <input type="text" class="form-control datepicker" id="tglDaftar"
                                                    name="tglDaftar" placeholder="dd-mm-yyyy" required>
                                            </div>

                                            <div class="form-group" style="display:none;">
                                                <label for="kdPoli">Poli</label>
                                                <select class="form-control" id="kdPoli" name="kdPoli" required>
                                                    <option value="">Pilih Poli</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="nmPoli">Nama Poli</label>
                                                <input type="text" class="form-control" id="nmPoli" name="nmPoli"
                                                    readonly>
                                                <input type="hidden" id="kdPoli" name="kdPoli" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="keluhan">Keluhan</label>
                                                <textarea class="form-control" id="keluhan" name="keluhan"
                                                    rows="3">Pasien datang dengan keluhan</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="template_pemeriksaan">Template Pemeriksaan</label>
                                                <select class="form-control" id="template_pemeriksaan"
                                                    name="template_pemeriksaan">
                                                    <option value="">-- Pilih Template --</option>
                                                    <optgroup label="Poli Umum">
                                                        <option value="normal">Pemeriksaan Normal</option>
                                                        <option value="artritis">Artritis/Nyeri Sendi</option>
                                                        <option value="asma">Asma Bronkial</option>
                                                        <option value="batuk">Batuk</option>
                                                        <option value="bph">BPH (Benign Prostatic Hyperplasia)</option>
                                                        <option value="bronkitis">Bronkitis</option>
                                                        <option value="common-cold">Common Cold</option>
                                                        <option value="demam">Demam</option>
                                                        <option value="dermatitis-atopik">Dermatitis Atopik</option>
                                                        <option value="dermatitis-kontak">Dermatitis Kontak</option>
                                                        <option value="diabetes">Diabetes Mellitus</option>
                                                        <option value="diare">Diare</option>
                                                        <option value="dislipidemia">Dislipidemia</option>
                                                        <option value="dyspepsia">Dyspepsia</option>
                                                        <option value="epistaksis">Epistaksis</option>
                                                        <option value="faringitis">Faringitis</option>
                                                        <option value="febris">Febris</option>
                                                        <option value="furunkel">Furunkel/Bisul</option>
                                                        <option value="gatal">Gatal-gatal/Alergi</option>
                                                        <option value="gerd">GERD</option>
                                                        <option value="gout">Gout Arthritis</option>
                                                        <option value="hipertensi">Hipertensi</option>
                                                        <option value="ibs">Irritable Bowel Syndrome</option>
                                                        <option value="insomnia">Insomnia</option>
                                                        <option value="ispa">ISPA</option>
                                                        <option value="jantung">Penyakit Jantung</option>
                                                        <option value="kolik">Kolik Abdomen</option>
                                                        <option value="konstipasi">Konstipasi</option>
                                                        <option value="konjungtivitis">Konjungtivitis</option>
                                                        <option value="lbp">Low Back Pain</option>
                                                        <option value="maag">Gastritis</option>
                                                        <option value="malaria">Malaria</option>
                                                        <option value="migrain">Migrain</option>
                                                        <option value="myalgia">Myalgia/Nyeri Otot</option>
                                                        <option value="nyeri-perut">Nyeri Perut</option>
                                                        <option value="osteoartritis">Osteoartritis</option>
                                                        <option value="otitis">Otitis Media</option>
                                                        <option value="pterigium">Pterigium</option>
                                                        <option value="rhinitis">Rhinitis</option>
                                                        <option value="sariawan">Sariawan</option>
                                                        <option value="sakit-kepala">Sakit Kepala</option>
                                                        <option value="scabies">Scabies</option>
                                                        <option value="sesak">Sesak Napas</option>
                                                        <option value="sinusitis">Sinusitis</option>
                                                        <option value="tb">TB Paru</option>
                                                        <option value="tension-headache">Tension Headache</option>
                                                        <option value="tinea">Tinea</option>
                                                        <option value="tonsilitis">Tonsilitis</option>
                                                        <option value="trauma">Trauma/Luka</option>
                                                        <option value="typhoid">Demam Tifoid</option>
                                                        <option value="urtikaria">Urtikaria</option>
                                                        <option value="varicella">Varicella/Cacar Air</option>
                                                        <option value="vertigo">Vertigo</option>
                                                    </optgroup>

                                                    <optgroup label="KIA (Kesehatan Ibu dan Anak)">
                                                        <option value="anc1">ANC Trimester I</option>
                                                        <option value="anc2">ANC Trimester II</option>
                                                        <option value="anc3">ANC Trimester III</option>
                                                        <option value="pnc">PNC (Postnatal Care)</option>
                                                        <option value="kb">Keluarga Berencana</option>
                                                        <option value="imunisasi">Imunisasi</option>
                                                        <option value="tumbuh-kembang">Tumbuh Kembang Anak</option>
                                                        <option value="bayi-sehat">Pemeriksaan Bayi Sehat</option>
                                                        <option value="diare-anak">Diare pada Anak</option>
                                                        <option value="ispa-anak">ISPA pada Anak</option>
                                                        <option value="pneumonia-anak">Pneumonia pada Anak</option>
                                                        <option value="demam-anak">Demam pada Anak</option>
                                                        <option value="mual-hamil">Mual Muntah Kehamilan</option>
                                                        <option value="anemia-hamil">Anemia dalam Kehamilan</option>
                                                        <option value="hipertensi-hamil">Hipertensi dalam Kehamilan
                                                        </option>
                                                        <option value="uti-hamil">Infeksi Saluran Kemih pada Kehamilan
                                                        </option>
                                                        <option value="konstipasi-hamil">Konstipasi pada Kehamilan
                                                        </option>
                                                        <option value="abortus-iminens">Abortus Iminens</option>
                                                        <option value="hiperemesis">Hiperemesis Gravidarum</option>
                                                        <option value="keluhan-asi">Keluhan Menyusui/ASI</option>
                                                        <option value="mastitis">Mastitis</option>
                                                        <option value="infeksi-nifas">Infeksi Nifas</option>
                                                        <option value="stunting">Pemantauan Stunting</option>
                                                        <option value="eksim-anak">Eksim pada Anak</option>
                                                        <option value="campak">Campak</option>
                                                        <option value="difteri">Difteri</option>
                                                        <option value="pertusis">Pertusis</option>
                                                    </optgroup>
                                                    <optgroup label="Penyakit Gigi dan Mulut">
                                                        <option value="gigi-normal">Pemeriksaan Normal Gigi</option>
                                                        <option value="gigi-kontrol">Kontrol Gigi</option>
                                                        <option value="karies">Karies Gigi</option>
                                                        <option value="gingivitis">Gingivitis</option>
                                                        <option value="periodontitis">Periodontitis</option>
                                                        <option value="pulpitis">Pulpitis</option>
                                                        <option value="abses-gigi">Abses Gigi</option>
                                                        <option value="stomatitis">Stomatitis</option>
                                                        <option value="gigi-karies">Karies Gigi</option>
                                                        <option value="gigi-berlubang">Gigi Berlubang</option>
                                                        <option value="gigi-gingivitis">Gingivitis</option>
                                                        <option value="gigi-pulpitis">Pulpitis</option>
                                                        <option value="gigi-abses">Abses Gigi</option>
                                                    </optgroup>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="kdTkp">Tempat Kunjungan</label>
                                                <select class="form-control" id="kdTkp" name="kdTkp" required>
                                                    <option value="10" selected>Rawat Jalan (RJTP)</option>
                                                    <option value="20">Rawat Inap (RITP)</option>
                                                    <option value="50">Promotif Preventif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0"><i class="fas fa-heartbeat mr-2"></i>Pemeriksaan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>Jenis Kunjungan <span class="text-danger">*</span></label>
                                                <div class="mt-2">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="kunjSakit-true" name="kunjSakit"
                                                            value="Kunjungan Sakit" class="custom-control-input"
                                                            checked>
                                                        <label class="custom-control-label"
                                                            for="kunjSakit-true">Kunjungan
                                                            Sakit</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="kunjSakit-false" name="kunjSakit"
                                                            value="Kunjungan Sehat" class="custom-control-input">
                                                        <label class="custom-control-label"
                                                            for="kunjSakit-false">Kunjungan
                                                            Sehat</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sistole">Sistole (mmHg)</label>
                                                        <input type="number" class="form-control" id="sistole"
                                                            name="sistole" value="120">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="diastole">Diastole (mmHg)</label>
                                                        <input type="number" class="form-control" id="diastole"
                                                            name="diastole" value="80">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="beratBadan">Berat Badan (kg)</label>
                                                        <input type="number" class="form-control" id="beratBadan"
                                                            name="beratBadan" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tinggiBadan">Tinggi Badan (cm)</label>
                                                        <input type="number" class="form-control" id="tinggiBadan"
                                                            name="tinggiBadan" value="0">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="respRate">Respiratory Rate</label>
                                                        <input type="number" class="form-control" id="respRate"
                                                            name="respRate" value="20">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="heartRate">Heart Rate</label>
                                                        <input type="number" class="form-control" id="heartRate"
                                                            name="heartRate" value="88">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="lingkar_perut">Lingkar Perut (cm)</label>
                                                        <input type="number" class="form-control" id="lingkar_perut"
                                                            name="lingkar_perut" value="87" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="suhu_tubuh">Suhu Tubuh (°C)</label>
                                                        <input type="text" class="form-control" id="suhu_tubuh"
                                                            name="suhu_tubuh" value="36.5">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Field baru untuk pemeriksaan -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="spo2">SpO2 (%)</label>
                                                        <input type="text" class="form-control" id="spo2" name="spo2"
                                                            value="98">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="gcs">GCS</label>
                                                        <input type="text" class="form-control" id="gcs" name="gcs"
                                                            placeholder="E V M">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="kesadaran">Kesadaran</label>
                                                <select class="form-control" id="kesadaran" name="kesadaran">
                                                    <option value="Compos Mentis" selected>Compos Mentis</option>
                                                    <option value="Somnolence">Somnolence</option>
                                                    <option value="Sopor">Sopor</option>
                                                    <option value="Coma">Coma</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="pemeriksaan">Pemeriksaan</label>
                                                <textarea class="form-control" id="pemeriksaan" name="pemeriksaan"
                                                    rows="3"
                                                    placeholder="Hasil pemeriksaan fisik dan penunjang"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="alergi">Alergi</label>
                                                <textarea class="form-control" id="alergi" name="alergi" rows="2"
                                                    placeholder="Riwayat alergi pasien"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="penilaian">Penilaian</label>
                                                <textarea class="form-control" id="penilaian" name="penilaian" rows="2"
                                                    placeholder="Penilaian klinis"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="rtl">Rencana Tindak Lanjut</label>
                                                <textarea class="form-control" id="rtl" name="rtl" rows="2"
                                                    placeholder="Rencana tindak lanjut pasien"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="instruksi">Instruksi</label>
                                                <textarea class="form-control" id="instruksi" name="instruksi" rows="2"
                                                    placeholder="Instruksi untuk pasien"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="evaluasi">Evaluasi</label>
                                                <textarea class="form-control" id="evaluasi" name="evaluasi" rows="2"
                                                    placeholder="Evaluasi kondisi pasien"></textarea>
                                            </div>

                                            <div class="form-group" style="display:none;">
                                                <label for="rujukBalik">Rujuk Balik</label>
                                                <input type="hidden" class="form-control" id="rujukBalik"
                                                    name="rujukBalik" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="pendaftaranSubmitBtn">
                                    <i class="fas fa-save mr-2"></i>Simpan Pendaftaran
                                </button>
                                <a href="{{ route('ralan.pasien') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Hapus Pendaftaran -->
                    <div class="tab-pane fade" id="delete-content" role="tabpanel" aria-labelledby="delete-tab">
                        <div class="card border-0">
                            <div class="card-body">
                                <form id="form-hapus-pendaftaran">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="del_noKartu">No. Kartu BPJS <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="del_noKartu"
                                                    name="del_noKartu" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="del_tglDaftar">Tanggal Pendaftaran <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control datepicker" id="del_tglDaftar"
                                                    name="del_tglDaftar" placeholder="dd-mm-yyyy" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="del_noUrut">No. Urut Pendaftaran <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="del_noUrut"
                                                    name="del_noUrut" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="del_kdPoli">Kode Poli <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" id="del_kdPoli" name="del_kdPoli" required>
                                                    <option value="">Pilih Poli</option>
                                                    <option value="001">Umum</option>
                                                    <option value="002">Gigi</option>
                                                    <option value="003">KIA</option>
                                                    <option value="004">KB</option>
                                                    <option value="005">IMS</option>
                                                    <option value="006">Psikologi</option>
                                                    <option value="007">Rehabilitasi Medik</option>
                                                    <option value="008">Poli Gizi</option>
                                                    <option value="009">Poli Akupuntur</option>
                                                    <option value="010">Poli Konseling</option>
                                                    <option value="011">Poli DOTS</option>
                                                    <option value="012">UGD</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button type="button" id="btn-hapus-pendaftaran" class="btn btn-danger btn-lg">
                                            <i class="fas fa-trash-alt mr-2"></i>Hapus Pendaftaran
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .card-body {
        padding: 1.5rem;
    }

    .card-header h5 {
        font-weight: 600;
    }

    label {
        font-weight: 600;
    }

    /* Styling untuk detail peserta */
    #detail-peserta-card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    #detail-peserta-card .card-header {
        padding: 0.8rem 1.2rem;
    }

    #detail-peserta-card .card-body {
        padding: 1.2rem;
    }

    .form-control-static {
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 0;
        min-height: 38px;
        display: flex;
        align-items: center;
    }

    .badge-status {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 4px;
    }

    .badge-aktif {
        background-color: #28a745;
        color: white;
    }

    .badge-nonaktif {
        background-color: #dc3545;
        color: white;
    }

    /* Animasi loading */
    .spin {
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .badge-aktif {
        background-color: #28a745;
        color: white;
    }

    .badge-nonaktif {
        background-color: #dc3545;
        color: white;
    }

    #detail-peserta-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #detail-peserta-card .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    #detail-peserta-card .card-body {
        padding: 1.5rem;
    }

    #detail-peserta-card label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    #detail-peserta-card p {
        margin-bottom: 0.5rem;
        padding: 0.375rem 0;
        font-size: 1rem;
    }

    #detail-peserta-card .font-weight-bold {
        font-weight: 700 !important;
        font-size: 1.1rem;
    }

    .badge {
        padding: 0.5em 1em;
        font-size: 85%;
        font-weight: 600;
        border-radius: 0.25rem;
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    $(document).ready(function() {
        // Konfigurasi Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        
        // Inisialisasi datepicker
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            language: 'id'
        });
        
        // Isi tanggal pendaftaran hari ini
        const today = new Date();
        const formattedDate = `${String(today.getDate()).padStart(2, '0')}-${String(today.getMonth() + 1).padStart(2, '0')}-${today.getFullYear()}`;
        $('#tglDaftar').val(formattedDate);
        $('#del_tglDaftar').val(formattedDate);
        
        // Function untuk mengambil data pasien secara otomatis saat halaman dibuka
        // Mirip dengan formWindowOpened di contoh Java
        function initFormOnOpen() {
            // Reset semua data form terlebih dahulu
            $('#form-pendaftaran-pcare')[0].reset();
            $('#detail-peserta-card').hide();
            
            // Set ulang tanggal hari ini setelah reset form
            const today = new Date();
            const formattedDate = `${String(today.getDate()).padStart(2, '0')}-${String(today.getMonth() + 1).padStart(2, '0')}-${today.getFullYear()}`;
            $('#tglDaftar').val(formattedDate);
            $('#del_tglDaftar').val(formattedDate);
            
            // Bersihkan cache lokal untuk mencegah data lama tersimpan
            try {
                // Bersihkan semua data cache yang berhubungan dengan pasien
                sessionStorage.clear(); // Bersihkan semua data session storage
                localStorage.clear();   // Bersihkan semua data local storage
                
                // Hapus semua cookie yang mungkin terkait dengan form
                const cookies = document.cookie.split(";");
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i];
                    const eqPos = cookie.indexOf("=");
                    const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
                    if (name.includes("patient") || name.includes("pcare")) {
                        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
                    }
                }
                
                // Log pembersihan cache
                console.log('Cache dibersihkan: semua data cache dihapus');
            } catch (e) {
                console.error('Error saat membersihkan cache:', e);
            }
            
            // Ambil nomor rekam medis dari URL jika ada
        const noRkmMedisParam = getURLParameter('no_rkm_medis');
            const timestamp = getURLParameter('ts') || (new Date().getTime());
            const clearCache = getURLParameter('clear_cache') || 'false';
            
            console.log('Parameter URL:', { 
                no_rkm_medis: noRkmMedisParam, 
                timestamp: timestamp, 
                clear_cache: clearCache 
            });
            
        if (noRkmMedisParam) {
                // Tampilkan informasi pasien yang akan diambil
                console.log('Memuat data pasien dengan No RM:', noRkmMedisParam);
                toastr.info(`Memuat data pasien dengan No RM: ${noRkmMedisParam}`);
                
                // Reset form terlebih dahulu
                $('#nm_pasien').val('');
                $('#noKartu').val('');
                $('#detail-peserta-card').hide();
                
            $('#no_rkm_medis').val(noRkmMedisParam);
                
                // Gunakan API get-valid-no-rawat untuk mendapatkan no_rawat valid
                $.ajax({
                    url: '/api/get-valid-no-rawat',
                    method: 'POST',
                    data: {
                        no_rkm_medis: noRkmMedisParam,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.no_rawat) {
                            $('#no_rawat').val(response.no_rawat);
                            console.log('Menggunakan no_rawat valid dari database:', response.no_rawat);
                            
                            // Panggil fungsi untuk mengisi data poli otomatis
                            fetchAndSetPoliData(response.no_rawat);
                        } else {
                            // Jika tidak ada no_rawat valid, buat dengan format yang benar (YYYY/MM/DD/xxxxxx)
                            const today = new Date();
                            const year = today.getFullYear();
                            const month = String(today.getMonth() + 1).padStart(2, '0');
                            const day = String(today.getDate()).padStart(2, '0');
                            const rawatId = `${year}/${month}/${day}/000001`;
                            
                            $('#no_rawat').val(rawatId);
                            console.log('No_rawat dari database tidak ditemukan, menggunakan format baru:', rawatId);
                        }
                        
                        // Tunggu sebentar untuk memastikan DOM selesai diinisialisasi
                        setTimeout(function() {
                            fetchPatientData(noRkmMedisParam);
                        }, 300);
                    },
                    error: function() {
                        // Format default jika API gagal
                        const today = new Date();
                        const year = today.getFullYear();
                        const month = String(today.getMonth() + 1).padStart(2, '0');
                        const day = String(today.getDate()).padStart(2, '0');
                        const rawatId = `${year}/${month}/${day}/000001`;
                        
                        $('#no_rawat').val(rawatId);
                        console.log('Gagal mendapatkan no_rawat dari database, menggunakan format default:', rawatId);
                        
                        // Tunggu sebentar untuk memastikan DOM selesai diinisialisasi
                        setTimeout(function() {
                            fetchPatientData(noRkmMedisParam);
                        }, 300);
                    }
                });
        } else {
                console.log('Tidak ada parameter no_rkm_medis di URL');
                toastr.warning('Tidak ada nomor rekam medis yang diberikan');
            }
        }
        
        // Check URL parameter
        function getURLParameter(name) {
            const results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results ? decodeURIComponent(results[1]) : null;
        }
        
        // Panggil fungsi inisialisasi saat halaman dibuka
        initFormOnOpen();
        
        // Otomatis cek peserta saat nomor kartu berubah
        $('#noKartu').on('change', function() {
            const noKartu = $(this).val();
            if (noKartu && noKartu.length > 0) {
                fetchPesertaBPJS(noKartu);
            } else {
                // Reset detail peserta
                $('#detail-peserta-card').slideUp();
            }
        });
        
        // Update nama poli ketika poli dipilih
        $('#kdPoli').change(function() {
            const kdPoli = $(this).val();
            const nmPoli = $("#kdPoli option:selected").data('nm-poli');
            $('#nmPoli').val(nmPoli || '');
        });
        
        // Fungsi untuk mengambil data mapping poli dari API
        function fetchMappingPoli() {
            $.ajax({
                url: '/api/pcare/mapping-poli',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Mapping poli response:', response);
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        // Reset dropdown
                        $('#kdPoli').empty().append('<option value="">Pilih Poli</option>');
                        
                        // Isi dropdown dengan data poli
                        response.data.forEach(function(poli) {
                            $('#kdPoli').append(
                                `<option value="${poli.kd_poli_pcare}" data-nm-poli="${poli.nm_poli_pcare}" data-kd-poli-rs="${poli.kd_poli_rs}">
                                    ${poli.nm_poli_rs} (${poli.nm_poli_pcare})
                                </option>`
                            );
                        });
                        
                        // Trigger change event untuk mengisi nama poli jika ada poli yang dipilih
                        if ($('#kdPoli').val()) {
                            $('#kdPoli').trigger('change');
                        }
                        
                        console.log('Mapping poli loaded successfully');
                    } else {
                        console.error('No mapping poli data available');
                        toastr.warning('Data mapping poli tidak tersedia. Menggunakan data default.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching mapping poli:', error);
                    toastr.error('Gagal mengambil data mapping poli');
                }
            });
        }
        
        // Panggil fungsi untuk mengambil data mapping poli saat halaman dibuka
        fetchMappingPoli();
        
        // Fungsi untuk mengambil dan mengisi data poli berdasarkan no_rawat
        function fetchAndSetPoliData(noRawat) {
            if (!noRawat) return;
            
            console.log('Mengambil data poli berdasarkan no_rawat:', noRawat);
            
            $.ajax({
                url: '/api/get-poli-from-rawat',
                method: 'POST',
                data: {
                    no_rawat: noRawat,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const poliData = response.data;
                        console.log('Data poli ditemukan:', poliData);
                        
                        // Isi field poli
                        $('#kdPoli').val(poliData.kd_poli_pcare);
                        $('#nmPoli').val(poliData.nm_poli_pcare);
                        
                        toastr.success(`Poli berhasil diisi otomatis: ${poliData.nm_poli_pcare}`);
                    } else {
                        console.error('Data poli tidak ditemukan:', response.message);
                        toastr.warning('Data poli tidak ditemukan. Silakan pilih poli secara manual.');
                        
                        // Tampilkan dropdown poli jika data tidak ditemukan
                        $('div:has(#kdPoli)').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error mengambil data poli:', error);
                    toastr.error('Gagal mengambil data poli. Silakan pilih poli secara manual.');
                    
                    // Tampilkan dropdown poli jika terjadi error
                    $('div:has(#kdPoli)').show();
                }
            });
        }
        
        // Fungsi untuk mengambil data pasien dari nomor rekam medis
        function fetchPatientData(noRkmMedis) {
            if (!noRkmMedis) return;
            
            // Reset form terlebih dahulu
            $('#nm_pasien').val('');
            $('#noKartu').val('');
            $('#detail-peserta-card').slideUp();
            
            // Tambahkan timestamp untuk menghindari cache
            const timestamp = new Date().getTime();
            
            // Tampilkan loading
            $('#btn-cari-pasien').html('<i class="fas fa-spinner fa-spin"></i>');
            toastr.info('Mengambil data pasien...');
            console.log('Fetching patient data for:', noRkmMedis, 'timestamp:', timestamp);
            
            // AJAX untuk mengambil data pasien dari API pasien dengan parameter anti-cache
            $.ajax({
                url: `/api/pasien/detail/${noRkmMedis}?_=${timestamp}`, // Tambahkan parameter timestamp untuk mencegah cache
                method: 'GET',
                dataType: 'json',
                cache: false, // Matikan cache untuk request ini
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Request-ID': 'pcare-form-' + timestamp
                },
                success: function(response) {
                    console.log('API response:', response);
                    if (response.status === 'success') {
                        const pasienData = response.data;
                        console.log('Pasien data found:', pasienData);
                        
                        // Verifikasi data pasien yang ditemukan
                        if (pasienData.no_rkm_medis !== noRkmMedis) {
                            console.error('Nomor rekam medis tidak sesuai:', pasienData.no_rkm_medis, 'vs', noRkmMedis);
                            toastr.error(`Nomor rekam medis tidak sesuai: ${pasienData.no_rkm_medis} vs ${noRkmMedis}`);
                            
                            // Jika no_rkm_medis tidak sesuai, mungkin masalah cache. Coba refresh halaman
                            if (confirm(`Data pasien tidak sesuai. Refresh halaman untuk mencoba lagi?`)) {
                                window.location.reload(true); // Force refresh dari server
                            }
                            return;
                        }
                                    
                                    // Isi form dengan data pasien
                        $('#nm_pasien').val(pasienData.nm_pasien || '');
                        
                        // Tampilkan informasi verifikasi
                        toastr.info(`Verifikasi: Data untuk ${pasienData.nm_pasien} (${pasienData.no_rkm_medis}) berhasil dimuat`);
                                    
                                    // Isi nomor BPJS dari no_peserta pasien
                                    if (pasienData.no_peserta) {
                                        $('#noKartu').val(pasienData.no_peserta);
                            console.log('No peserta found:', pasienData.no_peserta);
                                        // Lakukan pengecekan data peserta BPJS otomatis jika no_peserta ada
                                        if (pasienData.no_peserta.length > 0) {
                                            fetchPesertaBPJS(pasienData.no_peserta);
                                        }
                        } else {
                            console.log('No peserta not found in pasien data');
                                    }
                                    
                                    // Jika no_peserta tidak ada, coba cek dengan NIK
                                    if ((!pasienData.no_peserta || pasienData.no_peserta.length === 0) && pasienData.no_ktp) {
                            console.log('Trying with NIK:', pasienData.no_ktp);
                                        fetchPesertaBPJSByNIK(pasienData.no_ktp);
                                    }
                                    
                                    if (pasienData.kd_pj) {
                                        $('#kdProviderPeserta').val(pasienData.kd_pj);
                                    }
                                    
                                    // Notifikasi sukses
                                    toastr.success('Data pasien berhasil dimuat');
                                } else {
                        console.log('Patient data not found with endpoint, showing error');
                        // Reset form karena data tidak ditemukan
                                    $('#nm_pasien').val('');
                                    $('#noKartu').val('');
                                    
                        // Notifikasi error yang lebih informatif
                        toastr.error(`Data pasien dengan No. RM ${noRkmMedis} tidak ditemukan di sistem`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching patient data:', error);
                    console.log('XHR Status:', xhr.status);
                    console.log('XHR Response:', xhr.responseText);
                    
                                // Reset form
                                $('#nm_pasien').val('');
                                $('#noKartu').val('');
                                
                    // Notifikasi error yang lebih informatif
                    toastr.error(`Gagal mengambil data pasien: ${error}. Status: ${xhr.status}`);
                    
                    // Coba sekali lagi dengan pendekatan berbeda jika gagal
                    if (confirm('Gagal mengambil data pasien. Coba lagi?')) {
                        setTimeout(function() {
                            // Gunakan fetch API sebagai alternatif
                            fetch(`/api/pasien/detail/${noRkmMedis}?nocache=${timestamp}`, {
                                headers: {
                                    'Cache-Control': 'no-cache',
                                    'Pragma': 'no-cache'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    $('#nm_pasien').val(data.data.nm_pasien || '');
                                    $('#noKartu').val(data.data.no_peserta || '');
                                
                                // Notifikasi sukses
                                    toastr.success('Data pasien berhasil dimuat (retry)');
                                }
                            })
                            .catch(err => {
                                console.error('Fetch retry failed:', err);
                                toastr.error('Gagal mengambil data pasien setelah mencoba ulang');
                            });
                        }, 500);
                    }
                },
                complete: function() {
                    $('#btn-cari-pasien').html('<i class="fas fa-search"></i>');
                }
            });
        }
        
        // Fungsi untuk mengambil data peserta BPJS berdasarkan no kartu
        function fetchPesertaBPJS(noKartu) {
            if (!noKartu) return;
            
            // Tampilkan loading sedikit
            toastr.info('Mengambil data peserta BPJS...');
            
            // AJAX untuk mengambil data peserta BPJS berdasarkan nomor kartu
            $.ajax({
                url: `/api/pcare/peserta/noka/${noKartu}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.metaData && response.metaData.code === 200) {
                        // Data peserta ditemukan
                        const peserta = response.response;
                        updatePesertaInfo(peserta);
                    } else {
                        console.log('Data peserta tidak ditemukan dengan noka, mencoba NIK');
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengambil data peserta BPJS dengan noka');
                }
            });
        }
        
        // Fungsi untuk mengambil data peserta BPJS berdasarkan NIK
        function fetchPesertaBPJSByNIK(nik) {
            if (!nik) return;
            
            // Tampilkan loading sedikit
            toastr.info('Mengambil data peserta BPJS berdasarkan NIK...');
            
            // AJAX untuk mengambil data peserta BPJS berdasarkan NIK
            $.ajax({
                url: `/api/pcare/peserta/nik/${nik}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.metaData && response.metaData.code === 200) {
                        // Data peserta ditemukan
                        const peserta = response.response;
                        updatePesertaInfo(peserta);
                        
                        // Update nomor kartu dengan yang benar dari peserta
                        $('#noKartu').val(peserta.noKartu);
                    } else {
                        console.log('Data peserta tidak ditemukan dengan NIK');
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengambil data peserta BPJS dengan NIK');
                }
            });
        }
        
        // Fungsi untuk memperbarui informasi peserta di UI
        function updatePesertaInfo(peserta) {
            // Isi detail peserta
            $('#det-noKartu').text(peserta.noKartu || '-');
            $('#det-nama').text(peserta.nama || '-');
            $('#det-hubunganKeluarga').text(peserta.hubunganKeluarga || '-');
            $('#det-sex').text(peserta.sex === 'L' ? 'Laki-laki' : (peserta.sex === 'P' ? 'Perempuan' : '-'));
            $('#det-tglLahir').text(peserta.tglLahir || '-');
            
            // Tampilkan status dengan badge
            if (peserta.ketAktif) {
                const isAktif = peserta.ketAktif === 'AKTIF';
                const badgeClass = isAktif ? 'badge-success' : 'badge-danger';
                $('#det-status').html(`<span class="badge ${badgeClass}">${peserta.ketAktif}</span>`);
            } else {
                $('#det-status').text('-');
            }
            
            // Info jenis peserta
            if (peserta.jnsPeserta && peserta.jnsPeserta.nama) {
                $('#det-jnsPeserta').text(peserta.jnsPeserta.nama);
            } else {
                $('#det-jnsPeserta').text('-');
            }
            
            // Info kelas
            if (peserta.jnsKelas && peserta.jnsKelas.nama) {
                $('#det-kelas').text(peserta.jnsKelas.nama);
            } else {
                $('#det-kelas').text('-');
            }
            
            // Info provider
            if (peserta.kdProviderPst && peserta.kdProviderPst.nmProvider) {
                $('#det-provider').text(peserta.kdProviderPst.nmProvider);
                // Isi field kode provider
                $('#kdProviderPeserta').val(peserta.kdProviderPst.kdProvider);
            } else {
                $('#det-provider').text('-');
            }
            
            // Tampilkan card detail peserta
            $('#detail-peserta-card').slideDown();
            
            // Notifikasi sukses
            toastr.success('Data peserta BPJS berhasil ditemukan');
        }
        
        // Pencarian data pasien
        $('#btn-cari-pasien').click(function() {
            const noRkmMedis = $('#no_rkm_medis').val();
            
            if (!noRkmMedis) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Masukkan Nomor Rekam Medis terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            fetchPatientData(noRkmMedis);
        });
        
        // Cek peserta BPJS (tombol manual)
        $('#btn-cek-peserta').click(function() {
            const noKartu = $('#noKartu').val();
            
            if (!noKartu) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Masukkan Nomor Kartu BPJS terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Tampilkan loading
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            
            // Coba pencarian dengan noKartu (noka)
            $.ajax({
                url: `/api/pcare/peserta/noka/${noKartu}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.metaData && response.metaData.code === 200) {
                        // Data peserta ditemukan
                        const peserta = response.response;
                        updatePesertaInfo(peserta);
                    } else {
                        // Coba pencarian dengan NIK jika noka tidak ditemukan
                        checkByNIK(noKartu);
                    }
                },
                error: function(xhr) {
                    // Coba pencarian dengan NIK jika terjadi error
                    checkByNIK(noKartu);
                },
                complete: function() {
                    // Kembalikan tombol ke kondisi semula
                    $('#btn-cek-peserta').html('<i class="fas fa-id-card"></i> Cek Peserta');
                }
            });
        });
        
        // Fungsi untuk cek data peserta dengan NIK (untuk tombol manual)
        function checkByNIK(inputValue) {
            $.ajax({
                url: `/api/pcare/peserta/nik/${inputValue}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.metaData && response.metaData.code === 200) {
                        // Data peserta ditemukan
                        const peserta = response.response;
                        updatePesertaInfo(peserta);
                        
                        // Update nomor kartu dengan yang benar dari response
                        $('#noKartu').val(peserta.noKartu);
                        
                        // Notifikasi sukses
                        toastr.success('Data peserta BPJS berhasil ditemukan berdasarkan NIK');
                    } else {
                        // Data peserta tidak ditemukan
                        $('#detail-peserta-card').slideUp();
                        
                        // Notifikasi error
                        Swal.fire({
                            title: 'Peserta Tidak Ditemukan',
                            text: response.metaData ? response.metaData.message : 'Data peserta BPJS tidak ditemukan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $('#detail-peserta-card').slideUp();
                    
                    // Notifikasi error
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengambil data peserta BPJS',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
        
        // Handler untuk submit form pendaftaran
        $('#form-pendaftaran-pcare').submit(function(e) {
            e.preventDefault();
            
            // Disable button dan tampilkan loading
            const submitBtn = $('#pendaftaranSubmitBtn');
            const btnText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...').attr('disabled', true);
            
            // Pastikan no_rawat memiliki format yang benar sebelum submit
            let noRawat = $('#no_rawat').val().trim();
            
            // Validasi format no_rawat (yyyy/mm/dd/xxxxxx)
            if (!/^\d{4}\/\d{2}\/\d{2}\/\d+$/.test(noRawat)) {
                console.log('Format no_rawat tidak valid, mencoba memperbaiki format...');
                
                // Cari no_rawat yang valid berdasarkan no_rkm_medis
                const noRkmMedis = $('#no_rkm_medis').val();
                
                $.ajax({
                    url: '/api/get-valid-no-rawat',
                    method: 'POST',
                    data: {
                        no_rkm_medis: noRkmMedis,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.no_rawat) {
                            noRawat = response.no_rawat;
                            console.log('Menggunakan no_rawat dari database:', noRawat);
                            
                            // Update form value
                            $('#no_rawat').val(noRawat);
                            
                            // Lanjutkan submit form dengan no_rawat yang valid
                            submitPcareForm(noRawat);
                        } else {
                            // Jika tidak ada no_rawat valid, gunakan fallback format
                            createFallbackNoRawat();
                        }
                    },
                    error: function() {
                        // Jika API gagal, gunakan fallback format
                        createFallbackNoRawat();
                    }
                });
            } else {
                // No_rawat sudah valid, lanjutkan submit
                submitPcareForm(noRawat);
            }
            
            // Fungsi untuk membuat no_rawat fallback jika tidak ditemukan di database
            function createFallbackNoRawat() {
                // Ambil nomor terakhir dari no_rawat jika ada
                const parts = noRawat.split('/');
                let lastPart = '';
                
                if (parts.length > 0) {
                    lastPart = parts[parts.length - 1];
                }
                
                // Dapatkan tanggal hari ini dalam format yyyy/mm/dd
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                
                // Buat format no_rawat yang benar
                if (!/^\d+$/.test(lastPart)) {
                    // Jika bagian terakhir bukan angka, gunakan default 000001
                    lastPart = '000001';
                }
                
                noRawat = `${year}/${month}/${day}/${lastPart}`;
                console.log('Format no_rawat yang diperbaiki:', noRawat);
                $('#no_rawat').val(noRawat);
                
                // Submit form dengan no_rawat yang sudah diperbaiki
                submitPcareForm(noRawat);
            }
            
            // Fungsi untuk submit form PCare
            function submitPcareForm(noRawat) {
                // Pastikan field wajib terisi
                const nmPoli = $('#nmPoli').val();
                const no_rkm_medis = $('#no_rkm_medis').val();
                const nm_pasien = $('#nm_pasien').val();
                const noKartu = $('#noKartu').val();
                
                if (!noRawat || !noKartu || !no_rkm_medis || !nm_pasien || !nmPoli) {
                    // Log data yang kosong
                    console.error('Field wajib kosong:', {
                        noRawat: noRawat,
                        noKartu: noKartu,
                        no_rkm_medis: no_rkm_medis,
                        nm_pasien: nm_pasien,
                        nmPoli: nmPoli
                    });
                    
                    // Tampilkan pesan kesalahan
                    Swal.fire({
                        title: 'Error!',
                        text: 'Field No Rawat, No Kartu, No RM, Nama Pasien, dan Poli harus diisi!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    
                    // Reset state button
                    const submitBtn = $('#pendaftaranSubmitBtn');
                    submitBtn.html('Simpan').attr('disabled', false);
                    return;
                }
                
                // Fungsi untuk memvalidasi dan memotong data sesuai skema database
                function validateField(value, maxLength, defaultValue = '') {
                    if (value === null || value === undefined) return defaultValue;
                    const strValue = String(value).trim();
                    return strValue.substring(0, maxLength);
                }
                
                // Ambil nilai dari form dan pastikan sesuai batas karakter
                const sistole = validateField($('#sistole').val(), 3, '120');
                const diastole = validateField($('#diastole').val(), 3, '80');
                const beratBadan = validateField($('#beratBadan').val(), 5, '60');
                const tinggiBadan = validateField($('#tinggiBadan').val(), 5, '165');
                const respRate = validateField($('#respRate').val(), 3, '20');
                const lingkarPerut = validateField($('#lingkar_perut').val(), 5, '80');
                const heartRate = validateField($('#heartRate').val(), 3, '80');
                const rujukBalik = validateField($('#rujukBalik').val(), 3, '0');
                const keluhan = validateField($('#keluhan').val(), 400, 'Pasien datang dengan keluhan');
                
                // Validasi enum kunjSakit (harus 'Kunjungan Sakit' atau 'Kunjungan Sehat')
                const kunjSakitVal = $('input[name="kunjSakit"]:checked').val() === "Kunjungan Sakit";
                
                // Validasi kdTkp (harus salah satu dari enum yang valid)
                let kdTkpValue = $('#kdTkp').val() || '10';
                if (!['10', '20', '50'].includes(kdTkpValue)) {
                    kdTkpValue = '10'; // Default ke Rawat Jalan jika tidak valid
                }
                
                // Data untuk PCare BPJS (kirim ke API)
                const formDataPcare = {
                    no_rawat: validateField(noRawat, 17),
                    no_rkm_medis: validateField(no_rkm_medis, 15),
                    nm_pasien: validateField(nm_pasien, 40),
                    nmPoli: validateField(nmPoli, 50),
                    kdProviderPeserta: validateField($('#kdProviderPeserta').val(), 15, '0'),
                    tglDaftar: $('#tglDaftar').val() || moment().format('DD-MM-YYYY'),
                    noKartu: validateField(noKartu, 25),
                    kdPoli: validateField($('#kdPoli').val(), 5, '001'),
                    keluhan: keluhan,
                    kunjSakit: kunjSakitVal,
                    sistole: sistole,
                    diastole: diastole,
                    beratBadan: beratBadan,
                    tinggiBadan: tinggiBadan,
                    respRate: respRate,
                    lingkarPerut: lingkarPerut,
                    heartRate: heartRate,
                    rujukBalik: rujukBalik,
                    kdTkp: kdTkpValue
                };

                // Data untuk pemeriksaan lokal
                const pemeriksaanData = {
                    no_rawat: validateField(noRawat, 17),
                    no_rkm_medis: validateField(no_rkm_medis, 15),
                    tgl_perawatan: moment($('#tglDaftar').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
                    jam_rawat: moment().format('HH:mm:ss'),
                    suhu_tubuh: validateField($('#suhu_tubuh').val(), 5, '36.5'),
                    tensi: validateField(sistole + '/' + diastole, 8, '120/80'),
                    nadi: heartRate,
                    respirasi: respRate,
                    tinggi: tinggiBadan,
                    berat: beratBadan,
                    spo2: validateField($('#spo2').val(), 3, '95'),
                    gcs: validateField($('#gcs').val(), 5, '15'),
                    kesadaran: validateField($('#kesadaran').val(), 13, 'Compos Mentis'),
                    keluhan: keluhan,
                    pemeriksaan: validateField($('#pemeriksaan').val(), 700, ''),
                    alergi: validateField($('#alergi').val(), 50, 'Tidak Ada'),
                    rtl: validateField($('#rtl').val(), 700, ''),
                    penilaian: validateField($('#penilaian').val(), 700, ''),
                    instruksi: validateField($('#instruksi').val(), 700, ''),
                    evaluasi: validateField($('#evaluasi').val(), 700, ''),
                    lingkar_perut: lingkarPerut,
                    nip: validateField('{{ session()->get("username") }}', 20, '')
                };
                
                // Log data yang akan dikirim
                console.log('Data PCare:', formDataPcare);
                console.log('Data Pemeriksaan:', pemeriksaanData);
                
                // Kirim data PCare ke endpoint PCare
                $.ajax({
                    url: '/api/pcare/pendaftaran',
                    method: 'POST',
                    data: formDataPcare,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.metaData && response.metaData.code === 201) {
                            const noUrut = validateField(response.response.message, 5, '');
                            
                            // Jika PCare berhasil, simpan data pemeriksaan
                            savePemeriksaan(pemeriksaanData, noUrut);
                        } else {
                            let errorMsg = 'Terjadi kesalahan saat menyimpan data ke PCare';
                            if (response.metaData && response.metaData.message) {
                                errorMsg = response.metaData.message;
                            }
                            
                            // Reset state button
                            const submitBtn = $('#pendaftaranSubmitBtn');
                            submitBtn.html('Simpan').attr('disabled', false);
                            
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Reset state button
                        const submitBtn = $('#pendaftaranSubmitBtn');
                        submitBtn.html('Simpan').attr('disabled', false);
                        
                        handleAjaxError(xhr, 'Gagal menyimpan data ke PCare');
                    }
                });
            }

            // Fungsi untuk menyimpan data pemeriksaan
            function savePemeriksaan(pemeriksaanData, noUrut) {
                $.ajax({
                    url: '/api/pemeriksaan/save',
                    method: 'POST',
                    data: {
                        ...pemeriksaanData,
                        noUrut: noUrut
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Pendaftaran dan pemeriksaan berhasil disimpan dengan No. Urut: ${noUrut}`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('ralan.pasien') }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Perhatian',
                                text: 'Data PCare berhasil disimpan tetapi gagal menyimpan data pemeriksaan',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr, 'Gagal menyimpan data pemeriksaan');
                    }
                });
            }

            // Fungsi untuk menangani error AJAX
            function handleAjaxError(xhr, defaultMessage) {
                let errorMsg = defaultMessage;
                let validationErrors = '';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    validationErrors = '<ul>';
                    
                    for (const field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            validationErrors += `<li>${errors[field]}</li>`;
                            console.error(`Error pada field ${field}:`, errors[field]);
                        }
                    }
                    
                    validationErrors += '</ul>';
                    errorMsg = 'Terdapat error validasi:';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                console.error('Ajax error:', xhr.responseJSON);
                
                Swal.fire({
                    title: 'Gagal!',
                    html: `${errorMsg} ${validationErrors}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
        
        // Handler untuk hapus pendaftaran
        $('#btn-hapus-pendaftaran').click(function() {
            // Validasi form hapus
            const noKartu = $('#del_noKartu').val();
            const tglDaftar = $('#del_tglDaftar').val();
            const noUrut = $('#del_noUrut').val();
            const kdPoli = $('#del_kdPoli').val();
            
            if (!noKartu || !tglDaftar || !noUrut || !kdPoli) {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Semua field harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Konfirmasi hapus
            Swal.fire({
                title: 'Anda yakin?',
                text: 'Pendaftaran akan dihapus secara permanen dari PCare!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button dan tampilkan loading
                    const deleteBtn = $(this);
                    const btnText = deleteBtn.html();
                    deleteBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...').attr('disabled', true);
                    
                    // Kirim request hapus dengan AJAX
                    $.ajax({
                        url: `/api/pcare/pendaftaran/peserta/${noKartu}/tglDaftar/${tglDaftar}/noUrut/${noUrut}/kdPoli/${kdPoli}`,
                        method: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.metaData && response.metaData.code === 200) {
                                // Notifikasi sukses
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Pendaftaran PCare berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reset form
                                    $('#form-hapus-pendaftaran')[0].reset();
                                });
                            } else {
                                // Tampilkan pesan error dari API
                                let errorMsg = 'Terjadi kesalahan saat menghapus data';
                                if (response.metaData && response.metaData.message) {
                                    errorMsg = response.metaData.message;
                                }
                                
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: errorMsg,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Terjadi kesalahan saat menghapus data';
                            
                            if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                                errorMsg = xhr.responseJSON.metaData.message;
                            }
                            
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        complete: function() {
                            // Kembalikan button ke kondisi semula
                            deleteBtn.html(btnText).attr('disabled', false);
                        }
                    });
                }
            });
        });

        // Function untuk set nilai field
        function setFieldValue(fieldId, value) {
            $('#' + fieldId).val(value);
        }

        // Handler untuk perubahan template
        $('#template_pemeriksaan').change(function() {
            let template = $(this).val();
            
            if (!template) return;
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menerapkan Template',
                text: 'Harap tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                timer: 1000,
                showConfirmButton: false
            });
            
            // Template Normal
            if (template === 'normal') {
                setFieldValue('keluhan', "Pasien melakukan kontrol rutin.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "Kondisi pasien stabil");
                setFieldValue('instruksi', "Istirahat Cukup, PHBS");
                setFieldValue('rtl', "Edukasi Kesehatan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol Ulang Jika belum Ada Perubahan");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }
            
            // Template Demam
            else if (template === 'demam') {
                setFieldValue('keluhan', "Pasien mengeluh demam sejak 2 hari yang lalu. Demam naik turun, kadang menggigil. Nyeri kepala (+), mual (-), muntah (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "Demam");
                setFieldValue('instruksi', "Istirahat cukup, kompres, minum banyak air putih");
                setFieldValue('rtl', "Pemberian antipiretik\nObservasi suhu");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari lagi atau jika demam tidak turun");
                
                // Set vital signs
                setFieldValue('suhu', '38.5');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '90');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '95');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }
            
            // Template Sakit Kepala
            else if (template === 'sakit-kepala') {
                setFieldValue('keluhan', "Pasien mengeluh sakit kepala berdenyut sejak 1 hari yang lalu. Nyeri skala 6/10. Mual (+), muntah (-), demam (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "Cephalgia");
                setFieldValue('instruksi', "Istirahat yang cukup dalam ruangan yang tenang, hindari cahaya terang");
                setFieldValue('rtl', "Pemberian analgetik dan anti mual");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari lagi atau jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }
            
            // Template Batuk
            else if (template === 'batuk') {
                setFieldValue('keluhan', "Pasien mengeluh batuk sejak 3 hari yang lalu. Batuk berdahak/kering, nyeri tenggorokan (+/-), hidung tersumbat (+/-), demam (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi -/-, wheezing -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "ISPA");
                setFieldValue('instruksi', "Istirahat cukup, minum air hangat, hindari makanan/minuman dingin");
                setFieldValue('rtl', "Pemberian antitusif/ekspektoran\nObservasi perkembangan batuk");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari lagi atau jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }
            
            // Template Sesak Napas
            else if (template === 'sesak') {
                setFieldValue('keluhan', "Pasien mengeluh sesak napas sejak 1 hari yang lalu. Sesak dirasakan terutama saat beraktivitas/saat berbaring. Batuk (+/-), nyeri dada (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak Sesak, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi +/+, wheezing +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "Asma/PPOK");
                setFieldValue('instruksi', "Istirahat cukup, hindari alergen/pemicu sesak, posisi semi-fowler");
                setFieldValue('rtl', "Pemberian bronkodilator\nObservasi sesak napas");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari lagi atau segera jika sesak memberat");
                
                // Set vital signs
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '130/90');
                setFieldValue('nadi', '96');
                setFieldValue('respirasi', '26');
                setFieldValue('spo2', '94');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }
            
            // Template Nyeri Perut
            else if (template === 'nyeri-perut') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri perut sejak 1 hari yang lalu. Nyeri terutama di bagian (atas/bawah/kanan/kiri). Mual (+), muntah (+/-), diare (+/-), demam (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak Kesakitan, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(+), peristaltik (+) normal/meningkat/menurun.\nEXT : Oedem -/-");
                setFieldValue('penilaian', "Gastritis/Dispepsia");
                setFieldValue('instruksi', "Diet lunak, hindari makanan pedas/asam, makan sedikit tapi sering");
                setFieldValue('rtl', "Pemberian antasida/PPI\nObservasi nyeri perut");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari lagi atau segera jika nyeri tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '37.0');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '72');
            }

            // Template Hipertensi
            else if (template === 'hipertensi') {
                setFieldValue('keluhan', "Pasien mengeluh pusing, tengkuk terasa berat, dan tekanan darah tinggi. Keluhan lain: nyeri kepala (+/-), jantung berdebar (+/-), sesak napas (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nThorax : Cor S1-2 reguler, tidak ada bising\nPulmo : Vesikuler +/+, tidak ada wheezing/ronkhi\nAbdomen : Supel, tidak ada nyeri tekan\nEXT : Akral hangat, tidak ada edema");
                setFieldValue('penilaian', "Hipertensi Grade I/II");
                setFieldValue('instruksi', "Diet rendah garam, hindari makanan berlemak, olahraga teratur, hindari stress");
                setFieldValue('rtl', "Pemberian antihipertensi\nMonitoring tekanan darah\nEdukasi gaya hidup sehat");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 7 hari atau segera jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '150/95');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Diabetes
            else if (template === 'diabetes') {
                setFieldValue('keluhan', "Pasien mengeluh sering haus, sering BAK, mudah lelah. Keluhan lain: banyak makan (+/-), kesemutan (+/-), luka sukar sembuh (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nEXT : Akral hangat, sensibilitas normal\nPemeriksaan kaki diabetik (-)\nGDS: > 200 mg/dL");
                setFieldValue('penilaian', "Diabetes Melitus Tipe 2");
                setFieldValue('instruksi', "Diet DM, olahraga teratur, kontrol gula darah, perawatan kaki");
                setFieldValue('rtl', "Pemberian OHO\nMonitoring gula darah\nEdukasi diet dan gaya hidup");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 7 hari dengan hasil cek gula darah");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '92');
            }

            // Template ISPA
            else if (template === 'ispa') {
                setFieldValue('keluhan', "Pasien mengeluh batuk pilek sejak 3 hari, hidung tersumbat, tenggorokan gatal. Demam (+/-), nyeri menelan (+/-), suara serak (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nThorax : Cor dalam batas normal\nPulmo : Vesikuler +/+, tidak ada wheezing/ronkhi\nTHT : Faring hiperemis, tonsil T1-T1\nLimfoid : KGB tidak membesar");
                setFieldValue('penilaian', "ISPA");
                setFieldValue('instruksi', "Istirahat cukup, minum air hangat, hindari makanan/minuman dingin");
                setFieldValue('rtl', "Terapi simptomatik\nObservasi gejala pernapasan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '37.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Myalgia
            else if (template === 'myalgia') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri otot seluruh badan, pegal-pegal, lemas. Riwayat aktivitas fisik berlebih (+/-), demam (+/-), trauma (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nMuskuloskeletal : Nyeri tekan otot (+), ROM normal\nNeurologis : Kekuatan otot 5/5, sensibilitas normal\nTanda radang (-)");
                setFieldValue('penilaian', "Myalgia");
                setFieldValue('instruksi', "Istirahat cukup, kompres hangat area nyeri, hindari aktivitas berat");
                setFieldValue('rtl', "Pemberian analgetik\nEdukasi ergonomik");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika nyeri memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Artritis
            else if (template === 'artritis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri sendi, kaku sendi terutama pagi hari, bengkak pada sendi. Riwayat trauma (-), demam (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nMuskuloskeletal : Sendi nyeri tekan (+), bengkak (+)\nROM : Terbatas karena nyeri\nDeformitas : (-)\nTanda radang : Lokal (+)");
                setFieldValue('penilaian', "Osteoartritis/Artritis");
                setFieldValue('instruksi', "Kompres hangat area nyeri, hindari aktivitas berat, latihan ROM");
                setFieldValue('rtl', "Pemberian NSAID\nFisioterapi\nEdukasi perawatan sendi");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 7 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '84');
            }

            // Template Gatal/Alergi
            else if (template === 'gatal') {
                setFieldValue('keluhan', "Pasien mengeluh gatal-gatal di kulit, kemerahan, bentol-bentol. Riwayat alergi makanan/obat/debu (+/-), demam (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nKulit : Eritema (+), urtikaria (+)\nBentol : Diskret/konfluen\nBekas garukan (+/-)\nTanda infeksi sekunder (-)");
                setFieldValue('penilaian', "Dermatitis/Urtikaria");
                setFieldValue('instruksi', "Hindari garuk, identifikasi & hindari pemicu alergi, jaga kebersihan");
                setFieldValue('rtl', "Pemberian antihistamin\nKompres dingin area gatal\nEdukasi pencegahan alergi");
                setFieldValue('alergi', "Dalam observasi");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template TB Paru
            else if (template === 'tb') {
                setFieldValue('keluhan', "Pasien mengeluh batuk >2 minggu, batuk berdahak, keringat malam, penurunan BB. Demam (+), nafsu makan menurun (+), sesak (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak kurus, composmentis\nThorax : Cor dalam batas normal\nPulmo : Vesikuler +/+, ronkhi basah halus (+)\nAbdomen : Supel, hepatosplenomegali (-)\nStatus gizi : Kurang");
                setFieldValue('penilaian', "Suspek TB Paru");
                setFieldValue('instruksi', "Periksa dahak, foto thorax, isolasi droplet, nutrisi adekuat");
                setFieldValue('rtl', "Pemeriksaan BTA sputum\nRujukan rontgen thorax\nEdukasi pencegahan penularan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol dengan hasil pemeriksaan penunjang");
                
                setFieldValue('suhu', '37.8');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Penyakit Jantung
            else if (template === 'jantung') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri dada, jantung berdebar, sesak napas. Nyeri menjalar ke lengan kiri (+/-), keringat dingin (+/-), mual (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis, tampak sesak\nThorax : Cor S1-S2 reguler, murmur (-)\nPulmo : Vesikuler +/+, ronkhi basah halus (+/-)\nEKG : Terlampir\nAkral : Hangat, CRT <2 detik");
                setFieldValue('penilaian', "Angina Pektoris/CAD");
                setFieldValue('instruksi', "Istirahat total, posisi semi-fowler, diet jantung");
                setFieldValue('rtl', "Pemberian vasodilator\nMonitoring vital sign\nRujukan ke Spesialis Jantung");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Rujuk segera ke IGD RS jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '160/95');
                setFieldValue('nadi', '102');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '95');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '88');
            }

            // Template Dyspepsia
            else if (template === 'dyspepsia') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri ulu hati, kembung, mual. Keluhan memberat setelah makan, rasa terbakar di dada (+/-), sendawa (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Nyeri tekan epigastrium (+), defans muskuler (-)\nHepar/Lien : tidak teraba\nBising usus : normal");
                setFieldValue('penilaian', "Dyspepsia");
                setFieldValue('instruksi', "Makan teratur, hindari makanan berlemak/pedas, kurangi kopi, hindari stress");
                setFieldValue('rtl', "Pemberian antasida\nPPI bila perlu\nEdukasi pola makan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Vertigo
            else if (template === 'vertigo') {
                setFieldValue('keluhan', "Pasien mengeluh pusing berputar, mual, muntah. Keluhan memberat saat gerakan kepala, gangguan pendengaran (+/-), tinitus (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nKepala : Nistagmus (+/-)\nRomberg test : (+/-)\nTanda neurologis fokal (-)\nTanda rangsang menings (-)");
                setFieldValue('penilaian', "Vertigo Peripheral/BPPV");
                setFieldValue('instruksi', "Istirahat, hindari gerakan kepala mendadak, latihan Epley bila BPPV");
                setFieldValue('rtl', "Pemberian antivertigo\nObservasi gejala neurologis\nRujukan THT bila perlu");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Low Back Pain
            else if (template === 'lbp') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri punggung bawah, nyeri bertambah saat aktivitas/duduk lama. Riwayat angkat berat (+/-), trauma (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nPunggung : Nyeri tekan region lumbal (+)\nLasegue test : (-)\nStraight leg raising test : (-)\nKekuatan motorik : 5/5\nSensibilitas : normal");
                setFieldValue('penilaian', "Low Back Pain (LBP) Muskuloskeletal");
                setFieldValue('instruksi', "Hindari angkat beban berat, postur yang benar, kompres hangat");
                setFieldValue('rtl', "Pemberian analgetik\nFisioterapi bila perlu\nEdukasi ergonomik");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '84');
            }

            // Template Rhinitis
            else if (template === 'rhinitis') {
                setFieldValue('keluhan', "Pasien mengeluh hidung tersumbat, bersin-bersin, pilek. Riwayat alergi (+), gatal hidung/mata (+), sesak (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nHidung : Mukosa hiperemis, sekret jernih\nFaring : Tidak hiperemis\nTonsil : T1/T1 tidak hiperemis\nAuskultasi paru : vesikuler normal");
                setFieldValue('penilaian', "Rhinitis Alergi");
                setFieldValue('instruksi', "Hindari alergen, jaga kebersihan lingkungan, cuci hidung");
                setFieldValue('rtl', "Pemberian antihistamin\nDekongestant bila perlu\nEdukasi pencegahan alergi");
                setFieldValue('alergi', "Debu/Dingin/dll (dalam observasi)");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '78');
            }

            // Template Faringitis
            else if (template === 'faringitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri tenggorokan, sulit menelan, demam. Batuk (+/-), pilek (+/-), suara serak (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nFaring : Hiperemis (+), pseudomembran (-)\nTonsil : T2/T2 hiperemis\nKGB leher : teraba, lunak, nyeri (-)\nAuskultasi paru : vesikuler normal");
                setFieldValue('penilaian', "Faringitis Akut");
                setFieldValue('instruksi', "Istirahat cukup, minum air hangat, kumur air garam");
                setFieldValue('rtl', "Pemberian antiinflamasi\nAnalgetik\nObservasi tanda bahaya");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '38.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Konjungtivitis
            else if (template === 'konjungtivitis') {
                setFieldValue('keluhan', "Pasien mengeluh mata merah, gatal, berair. Riwayat kontak dengan penderita (+/-), trauma (-), rasa benda asing (+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nMata : Hiperemis konjungtiva (+)\nSecret : serosa/mukoid\nVisus : normal\nPupil : isokor\nRefleks cahaya : normal");
                setFieldValue('penilaian', "Konjungtivitis");
                setFieldValue('instruksi', "Kompres mata dengan air hangat, jaga kebersihan, hindari menggosok mata");
                setFieldValue('rtl', "Pemberian antibiotik tetes mata\nAir mata artifisial\nEdukasi pencegahan penularan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Dermatitis Kontak
            else if (template === 'dermatitis-kontak') {
                setFieldValue('keluhan', "Pasien mengeluh gatal dan kemerahan pada kulit. Riwayat kontak dengan bahan iritan (+), timbul setelah kontak dengan sabun/deterjen/bahan kimia.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nKulit : Eritema (+), vesikel (+/-)\nLesi : terlokalisir di area kontak\nTanda infeksi sekunder (-)\nDermografisme : (+)");
                setFieldValue('penilaian', "Dermatitis Kontak Iritan/Alergi");
                setFieldValue('instruksi', "Hindari bahan pencetus, jaga kebersihan, gunakan pelembab");
                setFieldValue('rtl', "Pemberian kortikosteroid topikal\nAntihistamin oral\nEdukasi pencegahan");
                setFieldValue('alergi', "Dalam observasi");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '81');
            }

            // Template Tinea
            else if (template === 'tinea') {
                setFieldValue('keluhan', "Pasien mengeluh bercak gatal di kulit, bentuk seperti cincin. Riwayat keringat berlebih (+), gatal terutama malam hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nKulit : Lesi numular, tepi aktif meninggi\nSkuama : halus di tepi\nHiperpigmentasi : sentral\nTanda infeksi sekunder (-)");
                setFieldValue('penilaian', "Tinea Korporis/Kruris");
                setFieldValue('instruksi', "Jaga kebersihan, keringkan badan setelah berkeringat, pakaian tidak lembab");
                setFieldValue('rtl', "Pemberian antifungi topikal\nAntihistamin bila gatal\nEdukasi pencegahan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 7 hari atau sampai lesi membaik");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Asma Bronkial
            else if (template === 'asma') {
                setFieldValue('keluhan', "Pasien mengeluh sesak napas, mengi, batuk. Keluhan memberat malam/dini hari, dipicu oleh debu/dingin/aktivitas.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Sesak (+), Composmentis\nThorax : Cor dalam batas normal\nPulmo : Ekspirasi memanjang, wheezing +/+\nOtot bantu napas : Penggunaan minimal/sedang\nSianosis : (-)");
                setFieldValue('penilaian', "Asma Bronkial");
                setFieldValue('instruksi', "Hindari faktor pencetus, posisi semi fowler, teknik napas dalam");
                setFieldValue('rtl', "Nebulisasi\nPemberian bronkodilator\nEdukasi pencegahan");
                setFieldValue('alergi', "Dalam observasi");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika sesak memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '96');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '95');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Bronkitis
            else if (template === 'bronkitis') {
                setFieldValue('keluhan', "Pasien mengeluh batuk berdahak >5 hari, dahak kental/putih/kuning, sesak ringan. Demam (-), nyeri dada (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nThorax : Cor dalam batas normal\nPulmo : Ronkhi basah kasar +/+\nSputum : Mukoid\nSesak : minimal/tidak ada");
                setFieldValue('penilaian', "Bronkitis Akut");
                setFieldValue('instruksi', "Minum air hangat, hindari rokok, istirahat cukup");
                setFieldValue('rtl', "Pemberian mukolitik\nEkspektoran\nEdukasi berhenti merokok");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Diare
            else if (template === 'diare') {
                setFieldValue('keluhan', "Pasien mengeluh BAB cair >3x/hari, mual (+), muntah (+/-). Demam (+/-), nyeri perut (+), lendir/darah (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nMata : Tidak cekung\nMukosa : Lembab\nTurgor : Normal\nAbdomen : Supel, peristaltik meningkat\nDehidrasi : Ringan/Sedang");
                setFieldValue('penilaian', "Gastroenteritis Akut");
                setFieldValue('instruksi', "Minum oralit, cuci tangan, jaga kebersihan makanan");
                setFieldValue('rtl', "Rehidrasi oral\nPemberian zinc\nEdukasi pencegahan diare");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau jika dehidrasi memberat");
                
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '78');
            }

            // Template Gastritis
            else if (template === 'maag') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri ulu hati, mual, kembung. Riwayat makan tidak teratur, stress, konsumsi makanan pedas.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nAbdomen : Nyeri tekan epigastrium (+)\nDefans Muskuler : (-)\nMurphy sign : (-)\nMcBurney : (-)");
                setFieldValue('penilaian', "Gastritis");
                setFieldValue('instruksi', "Makan teratur, hindari makanan pedas/asam, kurangi stress");
                setFieldValue('rtl', "Pemberian antasida\nPPI bila perlu\nEdukasi pola makan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Migrain
            else if (template === 'migrain') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri kepala berdenyut unilateral, mual (+), muntah (+/-). Fotofobia (+), fonofobia (+), aura (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nTanda Rangsang Menings : (-)\nDefisit Neurologis : (-)\nNyeri Tekan Sinus : (-)\nTekanan Darah : Normal");
                setFieldValue('penilaian', "Migrain");
                setFieldValue('instruksi', "Istirahat di ruang gelap & tenang, kompres dingin, tidur cukup");
                setFieldValue('rtl', "Pemberian analgetik\nAnti mual bila perlu\nEdukasi trigger migrain");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika serangan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Otitis Media
            else if (template === 'otitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri telinga, pendengaran berkurang, demam. Riwayat batuk pilek (+), trauma (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nTelinga : Membran timpani hiperemis/bulging\nNyeri Tragus : (+)\nOtore : (-)\nRinoskopi : Mukosa hiperemis");
                setFieldValue('penilaian', "Otitis Media Akut");
                setFieldValue('instruksi', "Jaga telinga tetap kering, hindari masuk air");
                setFieldValue('rtl', "Pemberian antibiotik\nAnalgetik\nRujukan THT bila perlu");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '37.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Sinusitis
            else if (template === 'sinusitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri wajah/pipi, hidung tersumbat, ingus kental. Post ISPA, nyeri kepala (+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nHidung : Mukosa hiperemis, sekret purulen\nNyeri Tekan Sinus : (+)\nOrofaring : PND (+)\nKGB : Tidak membesar");
                setFieldValue('penilaian', "Sinusitis");
                setFieldValue('instruksi', "Kompres hangat area sinus, cuci hidung dengan NaCl");
                setFieldValue('rtl', "Pemberian antibiotik\nDekongestant\nAnalgetik");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Tonsilitis
            else if (template === 'tonsilitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri tenggorokan, sulit menelan, demam. Badan lemas, nafsu makan menurun.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nTonsil : T3/T3 hiperemis, pseudomembran (-)\nUvula : Deviasi (-)\nKGB leher : Membesar, nyeri tekan (+)\nCentor criteria : 3");
                setFieldValue('penilaian', "Tonsilitis Akut");
                setFieldValue('instruksi', "Istirahat cukup, minum air hangat, kumur antiseptik");
                setFieldValue('rtl', "Pemberian antibiotik\nAnalgetik-antipiretik\nObservasi komplikasi");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '38.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Karies Gigi
            else if (template === 'gigi-karies') {
                setFieldValue('keluhan', "Pasien mengeluh gigi berlubang, ngilu saat makan/minum panas atau dingin. Riwayat konsumsi makanan manis tinggi (+), sakit spontan (-), sakit saat digunakan untuk mengunyah (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nStatus Lokalis : Gigi ... terdapat karies superfisial/media/profunda\nNyeri tekan (-)\nNyeri ketuk (-)\nGigi goyang derajat 0\nPerubahan warna gigi (+/-)\nPembesaran KGB rahang (-)\nFaktor risiko : Kebersihan mulut kurang");
                setFieldValue('penilaian', "Karies Gigi (Karies Superfisial/Media/Profunda)");
                setFieldValue('instruksi', "Sikat gigi 2x sehari dengan teknik yang benar\nKurangi konsumsi makanan/minuman manis\nKontrol rutin ke dokter gigi setiap 6 bulan");
                setFieldValue('rtl', "Penambalan gigi dengan GIC/Composite\nAplikasi fluor\nEdukasi kebersihan mulut");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu pasca tindakan atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Gigi Berlubang
            else if (template === 'gigi-berlubang') {
                setFieldValue('keluhan', "Pasien mengeluh gigi berlubang dan nyeri spontan, terutama malam hari. Rasa nyeri menjalar ke kepala/telinga. Gigi terasa ngilu saat makan/minum panas atau dingin.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nStatus Lokalis : Gigi ... terdapat karies profunda mencapai pulpa\nNyeri tekan (+)\nNyeri ketuk (+/-)\nGigi goyang derajat 0\nTerdapat kavitas luas\nPembesaran KGB rahang (+/-)");
                setFieldValue('penilaian', "Gigi Berlubang dengan Pulpitis Ireversibel");
                setFieldValue('instruksi', "Sikat gigi 2x sehari dengan teknik yang benar\nHindari mengunyah pada sisi gigi yang sakit\nKonsumsi analgetik sesuai dosis yang diberikan");
                setFieldValue('rtl', "Tindakan perawatan saluran akar/ekstraksi\nPemberian analgetik dan antibiotik\nRujukan ke dokter gigi spesialis konservasi jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol sesuai jadwal atau jika keluhan memberat");
                
                setFieldValue('suhu', '36.9');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Gingivitis
            else if (template === 'gigi-gingivitis') {
                setFieldValue('keluhan', "Pasien mengeluh gusi berdarah saat menyikat gigi, gusi bengkak dan kemerahan. Bau mulut (+), nyeri pada gusi (+/-), gigi goyang (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nStatus Lokalis : Gingiva hiperemis, edema, mudah berdarah\nKalkulus dan plak (+)\nResesi gingiva (+/-)\nPoket gingiva < 3mm\nGigi goyang derajat 0\nFaktor risiko : Kebersihan mulut kurang, merokok (+/-)");
                setFieldValue('penilaian', "Gingivitis");
                setFieldValue('instruksi', "Sikat gigi 2x sehari dengan teknik yang benar\nGunakan dental floss/benang gigi\nKumur dengan obat kumur antiseptik\nBerhenti merokok");
                setFieldValue('rtl', "Skeling (pembersihan karang gigi)\nAplikasi antiseptik lokal\nPemberian obat kumur antiseptik\nEdukasi kebersihan mulut");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu pasca tindakan");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Kontrol Gigi
            else if (template === 'gigi-kontrol') {
                setFieldValue('keluhan', "Pasien datang untuk kontrol pasca tindakan ... (penambalan/pencabutan/pembersihan karang gigi). Tidak ada keluhan, rasa nyeri (-), bengkak (-), perdarahan (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nStatus Lokalis : Gigi ... telah dilakukan tindakan ... dengan hasil baik\nRestorasi dalam kondisi baik/perlu perbaikan\nGingiva sekitar tampak normal\nNyeri tekan (-)\nNyeri ketuk (-)\nGigi goyang (-)");
                setFieldValue('penilaian', "Kontrol Pasca Tindakan (Tambalan/Ekstraksi/Skeling)");
                setFieldValue('instruksi', "Lanjutkan menjaga kebersihan mulut\nSikat gigi 2x sehari dengan teknik yang benar\nKontrol rutin ke dokter gigi setiap 6 bulan");
                setFieldValue('rtl', "Pemantauan hasil tindakan\nAplikasi fluor jika diperlukan\nEdukasi kebersihan mulut dan pencegahan karies");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 6 bulan untuk pemeriksaan rutin");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Pemeriksaan Normal Gigi
            else if (template === 'gigi-normal') {
                setFieldValue('keluhan', "Pasien datang untuk pemeriksaan gigi rutin. Tidak ada keluhan, hanya ingin memastikan kesehatan gigi dan mulut.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Composmentis\nStatus Lokalis : Gigi dalam kondisi baik\nKebersihan mulut baik\nKaries (-)\nKalkulus dan plak minimal\nGingiva normal, tidak ada tanda radang\nMukosa mulut normal\nOkusi normal");
                setFieldValue('penilaian', "Kesehatan Gigi dan Mulut Baik");
                setFieldValue('instruksi', "Pertahankan kebersihan mulut\nSikat gigi 2x sehari dengan teknik yang benar\nGunakan dental floss secara rutin\nKontrol rutin ke dokter gigi setiap 6 bulan");
                setFieldValue('rtl', "Pembersihan karang gigi jika diperlukan\nAplikasi fluor untuk pencegahan karies\nEdukasi kebersihan mulut");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 6 bulan untuk pemeriksaan rutin");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Pulpitis
            else if (template === 'gigi-pulpitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri gigi hebat, spontan, menyebar ke kepala/telinga, terjadi terutama malam hari. Nyeri bertambah dengan stimulus panas/dingin. Pasien sulit tidur dan makan karena nyeri.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak kesakitan, composmentis\nStatus Lokalis : Gigi ... terdapat karies profunda dengan pulpa terbuka\nNyeri tekan (+)\nNyeri ketuk (+)\nGigi goyang derajat 0/1\nCavitas dalam\nPembesaran KGB rahang (+/-)");
                setFieldValue('penilaian', "Pulpitis Ireversibel");
                setFieldValue('instruksi', "Hindari stimulus panas/dingin\nJangan mengunyah pada sisi gigi yang sakit\nKonsumsi analgetik sesuai dosis");
                setFieldValue('rtl', "Tindakan perawatan saluran akar/ekstraksi\nPemberian analgetik dan antibiotik\nRujukan ke dokter gigi spesialis konservasi jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1-3 hari atau jika keluhan memberat");
                
                setFieldValue('suhu', '37.0');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Abses Gigi
            else if (template === 'gigi-abses') {
                setFieldValue('keluhan', "Pasien mengeluh bengkak di gusi/pipi, nyeri berdenyut, demam, rasa tidak nyaman saat mengunyah. Riwayat sakit gigi sebelumnya (+), trauma (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak kesakitan, composmentis\nStatus Lokalis : Gigi ... terdapat karies profunda dengan nekrosis pulpa\nPembengkakan gingiva/ekstra oral (+)\nFluktuasi (+/-)\nNyeri tekan (+)\nNyeri ketuk (+)\nGigi goyang derajat 1/2\nKelenjar getah bening submandibula membesar dan nyeri");
                setFieldValue('penilaian', "Abses Dentoalveolar");
                setFieldValue('instruksi', "Kompres hangat pada area bengkak\nMinum banyak air putih\nKonsumsi obat sesuai anjuran\nHindari makanan keras");
                setFieldValue('rtl', "Drainase abses jika fluktuasi (+)\nPemberian antibiotik spektrum luas\nAnalgetik-antipiretik\nTindakan ekstraksi/perawatan saluran akar\nRujukan ke dokter gigi spesialis jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1-2 hari untuk evaluasi pembengkakan dan infeksi");
                
                setFieldValue('suhu', '37.8');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template ANC Trimester I
            else if (template === 'anc1') {
                setFieldValue('keluhan', "G...P...A... usia kehamilan ... minggu (trimester I). Keluhan: mual (+/-), muntah (+/-), pusing (+/-), bengkak pada kaki (-), perdarahan (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nTTV : Dalam batas normal\nStatus Gizi : Baik/Kurang\nKonjungtiva : Anemis (-/+)\nUrin : Albumin (-), Glukosa (-)\nAbdomen : TFU belum teraba, Ballotement (+/-)\nDJJ : -\nGameli : -\nGerak janin : -\nTanda pre-eklampsia : -\nPemeriksaan Obstetrik : Leopold I-IV belum dapat dilakukan");
                setFieldValue('penilaian', "G...P...A... Hamil ... minggu, normal/dengan komplikasi ...");
                setFieldValue('instruksi', "Diet bergizi seimbang untuk ibu hamil\nIstirahat cukup\nKonsumsi suplemen Fe, asam folat, kalsium\nMinum air putih minimal 8 gelas sehari");
                setFieldValue('rtl', "Pemeriksaan laboratorium (Hb, golongan darah, urin rutin, gula darah, PITC)\nUSG kehamilan\nTT1/TT2 sesuai skrining\nPendidikan kesehatan tentang tanda bahaya kehamilan\nKontrol kehamilan teratur");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kunjungan ulang 4 minggu lagi atau segera jika ada keluhan");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template ANC Trimester II
            else if (template === 'anc2') {
                setFieldValue('keluhan', "G...P...A... usia kehamilan ... minggu (trimester II). Keluhan: nyeri punggung (+/-), sering BAK (+/-), konstipasi (+/-), bengkak pada kaki (-), perdarahan (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nTTV : Dalam batas normal\nStatus Gizi : Baik/Kurang\nKonjungtiva : Anemis (-/+)\nUrin : Albumin (-), Glukosa (-)\nAbdomen : TFU ... cm, sesuai/tidak sesuai UK\nDJJ : ... x/menit, reguler\nGameli : -/+\nGerak janin : +\nTanda pre-eklampsia : -\nPemeriksaan Obstetrik :\n- Leopold I : Fundus teraba ...\n- Leopold II : Punggung kanan/kiri\n- Leopold III : Presentasi kepala/bokong\n- Leopold IV : Belum masuk PAP");
                setFieldValue('penilaian', "G...P...A... Hamil ... minggu, normal/dengan komplikasi ...");
                setFieldValue('instruksi', "Diet bergizi seimbang untuk ibu hamil\nIstirahat cukup\nLanjutkan suplemen Fe, asam folat, kalsium\nLatihan fisik ringan untuk ibu hamil");
                setFieldValue('rtl', "Pemeriksaan Hb ulang\nUSG kehamilan jika belum\nTT3 sesuai skrining\nPendidikan kesehatan trimester II\nKontrol kehamilan teratur");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kunjungan ulang 4 minggu lagi atau segera jika ada keluhan");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template ANC Trimester III
            else if (template === 'anc3') {
                setFieldValue('keluhan', "G...P...A... usia kehamilan ... minggu (trimester III). Keluhan: sesak (+/-), nyeri pinggang (+/-), susah tidur (+/-), kencang-kencang (+/-), bengkak pada kaki (+/-), perdarahan (-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nTTV : Dalam batas normal\nStatus Gizi : Baik/Kurang\nKonjungtiva : Anemis (-/+)\nUrin : Albumin (-), Glukosa (-)\nAbdomen : TFU ... cm, sesuai/tidak sesuai UK\nDJJ : ... x/menit, reguler\nGameli : -/+\nGerak janin : +, ... x/jam\nTanda pre-eklampsia : -/+\nPemeriksaan Obstetrik :\n- Leopold I : Fundus teraba lunak/keras (kepala/bokong)\n- Leopold II : Punggung kanan/kiri\n- Leopold III : Presentasi kepala/bokong\n- Leopold IV : Sudah/belum masuk PAP ... /5");
                setFieldValue('penilaian', "G...P...A... Hamil ... minggu, normal/dengan komplikasi ...");
                setFieldValue('instruksi', "Diet bergizi seimbang untuk ibu hamil\nIstirahat cukup dengan posisi miring kiri\nLanjutkan suplemen Fe, asam folat, kalsium\nPersiapan persalinan dan kegawatdaruratan\nWaspadai tanda-tanda persalinan");
                setFieldValue('rtl', "Pemeriksaan Hb, urin rutin, HbsAg\nUSG kehamilan\nTT4/TT5 sesuai skrining\nKonseling persiapan persalinan dan KB pasca salin\nKontrol kehamilan lebih sering");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kunjungan ulang 2 minggu lagi (UK < 36 minggu) / 1 minggu lagi (UK > 36 minggu) atau segera jika ada keluhan/tanda persalinan");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '92');
            }

            // Template PNC (Postnatal Care)
            else if (template === 'pnc') {
                setFieldValue('keluhan', "P...A... hari ke-... post partum. Persalinan tanggal ... secara normal/SC di .... Keluhan: nyeri luka jahitan (+/-), darah nifas normal/berlebihan, demam (-), bengkak pada kaki (-), pusing (-), BAB/BAK lancar.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nTTV : Dalam batas normal\nPayudara : Membesar, puting susu menonjol/tidak, ASI +/+\nAbdomen : TFU ... jari di bawah pusat, kontraksi uterus baik, luka operasi baik (jika SC)\nLokhia : Rubra/Serosa/Alba, jumlah normal/berlebihan, bau normal/tidak\nPerineum : Jahitan (+/-), kondisi baik, tidak ada tanda infeksi\nEkstremitas : Tidak ada edema, tidak ada varises");
                setFieldValue('penilaian', "P...A... Post Partum Hari ke-... normal/dengan komplikasi ...");
                setFieldValue('instruksi', "Nutrisi tinggi protein dan cairan\nIstirahat cukup saat bayi tidur\nMenyusui ASI eksklusif\nMenjaga kebersihan diri dan luka\nMobilisasi bertahap");
                setFieldValue('rtl', "Perawatan payudara\nPemberian tablet Fe 30 tablet\nVitamin A dosis tinggi (jika belum)\nPendidikan kesehatan tentang ASI eksklusif\nKonseling KB pasca salin");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kunjungan nifas berikutnya sesuai jadwal (6 hari/2 minggu/6 minggu post partum)");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template KB (Keluarga Berencana)
            else if (template === 'kb') {
                setFieldValue('keluhan', "P...A... datang untuk kontrol/pemasangan KB. Riwayat KB sebelumnya: .... Keluhan: tidak ada/haid tidak teratur/bercak/nyeri kepala/mual/BB naik.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nTTV : Dalam batas normal\nMata : Konjungtiva tidak anemis, sklera tidak ikterik\nLeher : Tidak ada pembesaran KGB/tiroid\nPayudara : Tidak ada benjolan/nyeri tekan\nAbdomen : Tidak ada nyeri tekan, tidak ada massa\nGenital : Inspekulo tidak dilakukan/normal\nEkstremitas : Normal");
                setFieldValue('penilaian', "Akseptor KB baru/lama");
                setFieldValue('instruksi', "Informasi efek samping dan cara mengatasinya\nInformasi kapan harus kembali untuk kontrol\nPastikan cara penggunaan benar (jika pil/suntik)\nJadwal kunjungan ulang");
                setFieldValue('rtl', "Pemasangan/pemberian KB ...\nEdukasi efek samping dan penanganannya\nKunjungan ulang untuk kontrol/suntik ulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 bulan/3 bulan/sesuai jadwal metode KB");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Imunisasi
            else if (template === 'imunisasi') {
                setFieldValue('keluhan', "Anak usia ... bulan/tahun datang untuk imunisasi rutin. Tidak ada keluhan, nafsu makan baik, aktivitas normal, tidak ada riwayat kejang/demam tinggi pasca imunisasi sebelumnya.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Aktif\nTTV : Dalam batas normal\nPertumbuhan : BB ... kg, TB ... cm, LK ... cm (sesuai/tidak sesuai usia)\nThorax : Cor dan pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nKulit : Turgor normal, tidak ada ruam atau tanda infeksi\nStatus Gizi : Baik/Kurang");
                setFieldValue('penilaian', "Tumbuh kembang normal, datang untuk imunisasi ...");
                setFieldValue('instruksi', "Penjelasan tentang kemungkinan efek samping imunisasi\nKompres hangat pada tempat suntikan jika bengkak\nPerhatikan tanda-tanda reaksi alergi berat\nBeri parasetamol jika demam");
                setFieldValue('rtl', "Pemberian imunisasi ... sesuai jadwal\nPendidikan kesehatan tentang gizi seimbang\nPemantauan tumbuh kembang\nJadwal imunisasi berikutnya");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Imunisasi berikutnya sesuai jadwal atau segera jika ada efek samping serius");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template Tumbuh Kembang Anak
            else if (template === 'tumbuh-kembang') {
                setFieldValue('keluhan', "Anak usia ... bulan/tahun datang untuk pemeriksaan tumbuh kembang. Keluhan: perkembangan lambat/terlambat bicara/berjalan/pertumbuhan tidak sesuai usia.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Aktif\nTTV : Dalam batas normal\nAntropometri : BB ... kg, TB ... cm, LK ... cm, LLA ... cm\nBB/U : Gizi baik/kurang/buruk/lebih\nTB/U : Normal/stunting/tinggi\nBB/TB : Normal/kurus/gemuk\nLK/U : Normal/mikrosefali/makrosefali\nPerkembangan (KPSP) : Sesuai/meragukan/penyimpangan\nMotor kasar : Normal/terlambat\nMotor halus : Normal/terlambat\nBicara dan bahasa : Normal/terlambat\nSosialisasi dan kemandirian : Normal/terlambat\nModifikasi Denver II : Normal/suspek/unstable");
                setFieldValue('penilaian', "Tumbuh kembang anak usia ... bulan/tahun, sesuai/tidak sesuai usia");
                setFieldValue('instruksi', "Stimulasi sesuai tahap perkembangan\nPemberian nutrisi seimbang\nMonitoring rutin tumbuh kembang\nPenjelasan kepada orangtua tentang tahapan perkembangan normal");
                setFieldValue('rtl', "Stimulasi perkembangan sesuai usia\nOptimalisasi gizi\nKontrol tumbuh kembang rutin\nRujukan ke spesialis jika ditemukan penyimpangan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol tumbuh kembang 1 bulan/3 bulan sesuai hasil pemeriksaan");
                
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '50');
            }

            // Template Pemeriksaan Bayi Sehat
            else if (template === 'bayi-sehat') {
                setFieldValue('keluhan', "Bayi usia ... bulan datang untuk pemeriksaan rutin. Tidak ada keluhan, minum ASI lancar, BAB/BAK normal, tidur tenang, aktif, tidak rewel.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Aktif\nTTV : Dalam batas normal\nAntropometri : BB ... kg, TB ... cm, LK ... cm\nBB/U : Gizi baik (.... persentil)\nTB/U : Normal (.... persentil)\nBB/TB : Normal (.... persentil)\nKulit : Turgor baik, ikterus (-), sianosis (-)\nKepala : Normocephaly, UUB ... cm, fontanel anterior belum/sudah menutup\nMata : Konjungtiva tidak anemis, sklera tidak ikterik, pupil isokor\nTHT : Dalam batas normal\nThorax : Cor dan pulmo dalam batas normal\nAbdomen : Supel, tidak kembung, hepar/lien tidak teraba\nGenitalia : Normal sesuai usia dan jenis kelamin\nEkstremitas : Gerakan aktif, tidak ada kelainan bentuk");
                setFieldValue('penilaian', "Bayi usia ... bulan dengan tumbuh kembang normal");
                setFieldValue('instruksi', "Lanjutkan ASI eksklusif hingga 6 bulan (jika < 6 bulan)\nPengenalan MPASI bertahap (jika > 6 bulan)\nPerhatikan kebersihan dan keamanan\nStimulasi sesuai perkembangan usia");
                setFieldValue('rtl', "Penimbangan rutin di Posyandu\nImunisasi sesuai jadwal\nSuplemen vitamin A (usia > 6 bulan)\nPendidikan kesehatan tentang stimulasi perkembangan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 bulan lagi untuk pemantauan tumbuh kembang");
                
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '85/55');
                setFieldValue('nadi', '120');
                setFieldValue('respirasi', '30');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '42');
            }

            // Template Sariawan
            else if (template === 'sariawan') {
                setFieldValue('keluhan', "Pasien mengeluh sariawan di rongga mulut sejak beberapa hari yang lalu. Nyeri saat makan dan minum (+), demam (-), kesulitan menelan (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nMulut : Tampak lesi/ulkus di mukosa (bibir/lidah/pipi/gusi) dengan diameter sekitar 2-5 mm, berwarna putih kekuningan dengan tepi kemerahan");
                setFieldValue('penilaian', "Stomatitis Aftosa/Sariawan");
                setFieldValue('instruksi', "Hindari makanan pedas, asam, dan panas. Makan makanan lunak. Menjaga kebersihan mulut.");
                setFieldValue('rtl', "Pemberian obat kumur antiseptik\nPemberian vitamin\nEdukasi perawatan mulut");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 5 hari atau jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Scabies
            else if (template === 'scabies') {
                setFieldValue('keluhan', "Pasien mengeluh gatal pada kulit terutama di malam hari. Keluhan gatal terutama di sela jari, pergelangan tangan, siku, ketiak, dan sekitar pinggang.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nKulit : Tampak papul, vesikel, dan bekas garukan di area predileksi (sela jari, pergelangan tangan, siku, ketiak, pinggang). Terlihat terowongan kecil (kunikuli) di beberapa tempat.");
                setFieldValue('penilaian', "Scabies");
                setFieldValue('instruksi', "Menjaga kebersihan diri dan lingkungan. Mencuci semua pakaian dan sprei dengan air panas. Pengobatan seluruh anggota keluarga secara bersamaan.");
                setFieldValue('rtl', "Pemberian anti scabies topikal\nAntihistamin oral untuk mengurangi gatal\nEdukasi higiene personal dan lingkungan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 7 hari");
                
                // Set vital signs
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '78');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '74');
            }

            // Template Urtikaria
            else if (template === 'urtikaria') {
                setFieldValue('keluhan', "Pasien mengeluh muncul bentol-bentol kemerahan pada kulit yang gatal dan terasa seperti terbakar. Bentol mudah berpindah tempat dan muncul mendadak.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nKulit : Tampak urtika (bentol kemerahan) dengan diameter bervariasi, berbatas tegas, hilang timbul, disertai gatal. Angioedema (-/+).");
                setFieldValue('penilaian', "Urtikaria Akut");
                setFieldValue('instruksi', "Hindari faktor pencetus/alergen. Kompres dingin pada area yang gatal. Hindari menggaruk.");
                setFieldValue('rtl', "Pemberian antihistamin\nIdentifikasi dan eliminasi faktor pencetus\nEdukasi penanganan serangan");
                setFieldValue('alergi', "Dicurigai terhadap: (makanan/obat/bahan kimia/lainnya)");
                setFieldValue('evaluasi', "Kontrol 3-5 hari atau segera jika timbul sesak napas");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Common Cold
            else if (template === 'common-cold') {
                setFieldValue('keluhan', "Pasien mengeluh pilek, hidung tersumbat, dan bersin-bersin sejak 2-3 hari yang lalu. Disertai dengan nyeri tenggorokan ringan, batuk ringan, dan sedikit demam.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nHidung : Mukosa hidung hiperemis, sekret mukoid jernih (+)\nTenggorokan : Faring hiperemis ringan, tonsil dalam batas normal\nTelinga : Dalam batas normal");
                setFieldValue('penilaian', "Common Cold / ISPA Ringan");
                setFieldValue('instruksi', "Istirahat cukup, minum air hangat, hindari makanan/minuman dingin, jaga kelembapan ruangan.");
                setFieldValue('rtl', "Pemberian simptomatik\nEdukasi higiene personal");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3-5 hari jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '74');
            }

            // Template Kolik Abdomen
            else if (template === 'kolik') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri perut yang datang dan hilang tiba-tiba (kolik). Nyeri terutama di regio (atas/tengah/bawah). Mual (+/-), muntah (+/-), perubahan BAB (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak kesakitan saat serangan, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, nyeri tekan (+) di regio (atas/tengah/bawah), defans muskuler (-), Murphy sign (-), Psoas sign (-), Rovsing sign (-)\nGenitalia : Tidak dilakukan pemeriksaan");
                setFieldValue('penilaian', "Kolik Abdomen");
                setFieldValue('instruksi', "Hindari makanan bergas dan sulit dicerna. Kompres hangat pada area nyeri.");
                setFieldValue('rtl', "Pemberian antispasmodic\nAnalgesik jika diperlukan\nObservasi karakter nyeri");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika nyeri memberat");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Pterigium
            else if (template === 'pterigium') {
                setFieldValue('keluhan', "Pasien mengeluh ada pertumbuhan pada bagian putih mata yang meluas ke kornea. Disertai rasa tidak nyaman, kemerahan dan iritasi mata.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nMata : Tampak jaringan fibrovaskular berbentuk segitiga dari konjungtiva bulbi yang tumbuh ke arah kornea. Ukuran sekitar (1-3) mm dari limbus. Hiperemis (+), sekret (-)");
                setFieldValue('penilaian', "Pterigium Grade (I/II/III)");
                setFieldValue('instruksi', "Hindari paparan sinar matahari berlebihan, debu, dan angin. Gunakan kacamata pelindung saat beraktivitas di luar ruangan.");
                setFieldValue('rtl', "Pemberian pelumas mata\nPemberian vasokonstriktor topikal\nRujuk ke dokter spesialis mata jika progresif");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu atau jika keluhan bertambah");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Trauma/Luka
            else if (template === 'trauma') {
                setFieldValue('keluhan', "Pasien datang dengan luka (iris/robek/lecet/tusuk) di area (sebutkan lokasi). Kejadian sekitar (sebutkan waktu) yang lalu akibat (sebutkan penyebab).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nLuka : Tampak luka (iris/robek/lecet/tusuk) di area (sebutkan lokasi) dengan ukuran sekitar (panjang x lebar x dalam) cm. Perdarahan aktif (+/-), tepi luka (rata/tidak rata), debris/benda asing (+/-)\nTanda infeksi : Rubor (-), dolor (+), kalor (-), tumor (-), functio laesa (+/-)");
                setFieldValue('penilaian', "Vulnus (Laceratum/Scissum/Punctum/Excoriasi)");
                setFieldValue('instruksi', "Jaga luka tetap bersih dan kering. Ganti balutan jika basah. Hindari aktivitas berat.");
                setFieldValue('rtl', "Pembersihan luka\nPenutupan luka (jahitan/plester/balut)\nAntibitotik profilaksis\nAnti tetanus serum jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari untuk perawatan luka atau jika tanda infeksi muncul");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template BPH
            else if (template === 'bph') {
                setFieldValue('keluhan', "Pasien laki-laki mengeluh sulit BAK, pancaran urin melemah, sering BAK terutama di malam hari, rasa tidak puas setelah BAK, dan kadang menunggu lama untuk mulai BAK.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan, kandung kemih tidak teraba\nGenitalia : Pemeriksaan prostat pada PR teraba pembesaran prostat grade (I/II/III), konsistensi kenyal padat, permukaan rata, tidak teraba nodul");
                setFieldValue('penilaian', "Benign Prostatic Hyperplasia (BPH)");
                setFieldValue('instruksi', "Batasi konsumsi cairan di malam hari. Hindari alkohol dan kafein. Kosongkan kandung kemih secara teratur. BAK segera saat ada dorongan.");
                setFieldValue('rtl', "Pemberian alfa blocker\nPemeriksaan sisa urin post-miksi jika memungkinkan\nRujukan ke dokter spesialis urologi jika keluhan berat");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu atau segera jika timbul retensi urin");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Dermatitis Atopik
            else if (template === 'dermatitis-atopik') {
                setFieldValue('keluhan', "Pasien mengeluh gatal dan ruam kemerahan pada kulit, terutama di lipatan siku, belakang lutut, wajah, leher, dan tangan. Keluhan bertambah parah di malam hari dan saat stress.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nKulit : Tampak lesi eritematosa, skuama, papul, dan likenifikasi di area (lipatan siku/belakang lutut/wajah/leher/tangan). Ekskoriasi (+) akibat garukan, xerosis (+)");
                setFieldValue('penilaian', "Dermatitis Atopik");
                setFieldValue('instruksi', "Hindari sabun yang keras, gunakan pelembab kulit secara teratur, hindari alergen yang diketahui, hindari pakaian dari bahan yang kasar/wol.");
                setFieldValue('rtl', "Pemberian kortikosteroid topikal\nAntihistamin oral untuk mengurangi gatal\nPelembab kulit\nEdukasi perawatan kulit");
                setFieldValue('alergi', "Riwayat alergi/atopi: (sebutkan jika ada)");
                setFieldValue('evaluasi', "Kontrol 1 minggu");
                
                // Set vital signs
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '78');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Dislipidemia
            else if (template === 'dislipidemia') {
                setFieldValue('keluhan', "Pasien datang untuk kontrol dislipidemia / cek kolesterol. Keluhan tidak ada / kadang pusing, badan terasa berat.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, tidak ada nyeri tekan\nEkstremitas : Akral hangat, tidak ada edema\nHasil lab terakhir (jika ada): Kolesterol total ... mg/dl, LDL ... mg/dl, HDL ... mg/dl, Trigliserida ... mg/dl");
                setFieldValue('penilaian', "Dislipidemia");
                setFieldValue('instruksi', "Diet rendah lemak jenuh dan kolesterol. Olahraga teratur minimal 30 menit 3x seminggu. Hindari makanan tinggi lemak trans.");
                setFieldValue('rtl', "Monitoring profil lipid secara berkala\nPemberian obat penurun lipid jika diperlukan\nEdukasi gaya hidup sehat");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 bulan dengan hasil lab terbaru");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '92');
            }

            // Template Epistaksis
            else if (template === 'epistaksis') {
                setFieldValue('keluhan', "Pasien mengeluh mimisan dari hidung (kanan/kiri) sejak (sebutkan waktu) yang lalu. Darah yang keluar berwarna merah segar, volume (sedikit/sedang/banyak).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nHidung : Tampak darah mengalir dari lubang hidung (kanan/kiri). Septum anterior hiperemis (+). Sumber perdarahan dari (septum anterior/lainnya). Bekuan darah (+/-)");
                setFieldValue('penilaian', "Epistaksis Anterior");
                setFieldValue('instruksi', "Hindari mengorek hidung. Jaga kelembaban hidung. Jika mimisan berulang, duduk tegak, condongkan badan ke depan, tekan kedua lubang hidung dengan jari selama 10-15 menit.");
                setFieldValue('rtl', "Penghentian perdarahan dengan penekanan\nPemberian vasokonstriktor intranasal\nPemasangan tampon anterior jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika perdarahan berulang");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Febris
            else if (template === 'febris') {
                setFieldValue('keluhan', "Pasien mengeluh demam naik turun sejak (sebutkan durasi), suhu tertinggi mencapai ... °C. Disertai menggigil (+/-), berkeringat (+/-), penurunan nafsu makan (+/-), lemas (+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak lemah, Composmentis\nThorax : Cor reguler normokardi. Pulmo: Suara napas vesikuler +/+, rhonki -/-, wheezing -/-\nAbdomen : Supel, nyeri tekan (-), hepatosplenomegali (-/-)\nKulit : Turgor cukup, akral hangat, ruam (-), ikterik (-)");
                setFieldValue('penilaian', "Febris e.c. suspect (infeksi virus/bakteri)");
                setFieldValue('instruksi', "Istirahat cukup, kompres hangat, minum banyak air putih, makan makanan yang mudah dicerna.");
                setFieldValue('rtl', "Pemberian antipiretik\nPemberian cairan adekuat\nObservasi pola demam dan gejala penyerta");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika demam tidak turun > 3 hari");
                
                // Set vital signs
                setFieldValue('suhu', '38.4');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Furunkel/Bisul
            else if (template === 'furunkel') {
                setFieldValue('keluhan', "Pasien mengeluh benjolan merah, nyeri, dan keras di area (sebutkan lokasi) sejak (sebutkan durasi). Benjolan semakin membesar dan terasa nyeri.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nKulit : Tampak nodul eritematosa dengan ukuran sekitar ... cm di area (sebutkan lokasi), nyeri tekan (+), fluktuasi (+/-), pus point (+/-). Tanda inflamasi: rubor (+), kalor (+), dolor (+), tumor (+), functio laesa (+/-)");
                setFieldValue('penilaian', "Furunkulosis");
                setFieldValue('instruksi', "Jaga kebersihan area yang terkena. Hindari memencet bisul. Kompres hangat 3-4 kali sehari selama 15-20 menit.");
                setFieldValue('rtl', "Pemberian antibiotik\nKompres hangat\nInsisi dan drainase jika sudah matang\nPemberian analgesik jika nyeri");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika kondisi memburuk");
                
                // Set vital signs
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '78');
            }

            // Template GERD
            else if (template === 'gerd') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri atau rasa terbakar di dada (heartburn), terutama setelah makan dan saat berbaring. Keluhan lain: regurgitasi asam/makanan ke mulut, kesulitan menelan, rasa pahit/asam di mulut, batuk kronik, dan suara serak.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, nyeri tekan epigastrium (+/-), nyeri tekan kuadran kanan atas (-), murphy sign (-)");
                setFieldValue('penilaian', "Gastroesophageal Reflux Disease (GERD)");
                setFieldValue('instruksi', "Hindari makanan pedas, berlemak, coklat, kopi, alkohol, dan minuman berkarbonasi. Makan porsi kecil tapi sering. Hindari berbaring 2-3 jam setelah makan. Tinggikan kepala saat tidur.");
                setFieldValue('rtl', "Pemberian Proton Pump Inhibitor\nPemberian antasida jika diperlukan\nEdukasi modifikasi gaya hidup");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu atau segera jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Gout Arthritis
            else if (template === 'gout') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri sendi yang timbul mendadak, terutama pada (sendi jempol kaki/pergelangan kaki/lutut). Nyeri disertai bengkak, kemerahan, dan terasa hangat pada sendi yang terkena.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nEkstremitas : Tampak pembengkakan, kemerahan, dan hangat pada sendi (sebutkan lokasi). Nyeri tekan (+), ROM terbatas\nKadar asam urat terakhir: ... mg/dL (jika ada)");
                setFieldValue('penilaian', "Gout Arthritis");
                setFieldValue('instruksi', "Hindari makanan tinggi purin (jeroan, seafood, daging merah, kacang). Batasi konsumsi alkohol. Istirahatkan sendi yang sakit. Elevasi ekstremitas yang terkena.");
                setFieldValue('rtl', "Pemberian NSAID/Kolkisin untuk serangan akut\nPemberian allopurinol untuk menurunkan asam urat\nPemberian diet rendah purin\nPencatatan kadar asam urat secara berkala");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu dengan pemeriksaan asam urat");
                
                // Set vital signs
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '88');
            }

            // Template Irritable Bowel Syndrome
            else if (template === 'ibs') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri perut yang hilang timbul dan berubah pola BAB (diare/konstipasi/bergantian) sejak (sebutkan durasi). Keluhan biasanya membaik setelah BAB. Disertai kembung, perut terasa penuh, dan terkadang mukus dalam feses.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, nyeri tekan ringan dan difus (+), tidak ada massa, bising usus normal/meningkat/menurun, tidak ada tanda peritonitis");
                setFieldValue('penilaian', "Irritable Bowel Syndrome (IBS)");
                setFieldValue('instruksi', "Hindari makanan pencetus (susu, kopi, pedas, berlemak, kol, kacang). Makan teratur dengan porsi kecil tapi sering. Kelola stres dengan baik.");
                setFieldValue('rtl', "Pemberian spasmolitik untuk nyeri\nPemberian probiotik\nEdukasi diet dan manajemen stres\nJurnal makanan untuk identifikasi makanan pencetus");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/70');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '79');
            }

            // Template Insomnia
            else if (template === 'insomnia') {
                setFieldValue('keluhan', "Pasien mengeluh sulit tidur, baik sulit memulai tidur, sering terbangun malam hari, atau bangun terlalu pagi. Keluhan berlangsung sejak (sebutkan durasi) dan mengganggu aktivitas sehari-hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak lelah, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nStatus Psikiatri : Mood eutimik/sedih, afek sesuai, halusinasi (-), delusi (-), orientasi baik, memori baik");
                setFieldValue('penilaian', "Insomnia");
                setFieldValue('instruksi', "Tidur dan bangun pada jam yang sama setiap hari. Hindari kafein dan alkohol terutama sore/malam hari. Hindari tidur siang yang terlalu lama. Lakukan aktivitas menenangkan sebelum tidur. Ciptakan suasana tidur yang nyaman.");
                setFieldValue('rtl', "Edukasi sleep hygiene\nPemberian obat tidur jangka pendek jika diperlukan\nEvaluasi faktor psikologis yang mendasari\nTerapi relaksasi");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '78');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Konstipasi
            else if (template === 'konstipasi') {
                setFieldValue('keluhan', "Pasien mengeluh sulit BAB, frekuensi BAB < 3x seminggu, feses keras, dan perlu mengejan kuat. Disertai rasa tidak tuntas setelah BAB, perut kembung, dan nyeri perut.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : Supel, nyeri tekan (-), distensi (+/-), bising usus normal/menurun, massa feses teraba di kuadran kiri bawah (+/-)");
                setFieldValue('penilaian', "Konstipasi");
                setFieldValue('instruksi', "Tingkatkan konsumsi serat dan cairan. Olahraga teratur. Buang air besar segera saat ada dorongan. Atur jadwal BAB yang teratur.");
                setFieldValue('rtl', "Pemberian laksatif\nEdukasi diet tinggi serat\nPemberian suplemen serat jika diperlukan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '72');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Tension Headache
            else if (template === 'tension-headache') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri kepala seperti diikat atau ditekan di kedua sisi kepala. Nyeri bersifat tumpul, intensitas sedang, dan sering dipicu oleh stres atau kelelahan. Tidak disertai mual, muntah, atau fotofobia.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nNeurologis : Kesadaran compos mentis, GCS 15, pupil bulat isokor 3mm/3mm, refleks cahaya +/+, motorik dan sensorik dalam batas normal, tidak ada tanda rangsang meningeal, tidak ada defisit neurologis fokal");
                setFieldValue('penilaian', "Tension Headache");
                setFieldValue('instruksi', "Istirahat cukup, kelola stres dengan baik, lakukan teknik relaksasi, hindari pemicu nyeri kepala seperti kurang tidur atau posisi duduk yang salah.");
                setFieldValue('rtl', "Pemberian analgesik\nEdukasi manajemen stres\nTerapi relaksasi otot");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau jika keluhan tidak membaik");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '78');
            }

            // Template Osteoartritis
            else if (template === 'osteoartritis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri sendi yang bertambah saat beraktivitas dan membaik saat istirahat. Lokasi nyeri terutama di (lutut/pinggul/tangan). Disertai kaku sendi terutama pagi hari (<30 menit) dan kesulitan melakukan aktivitas sehari-hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nMuskuloskeletal : Tampak pembengkakan sendi (sebutkan lokasi), nyeri tekan (+), krepitasi (+), deformitas (-/+), ROM terbatas. Tidak ada kemerahan atau hangat pada sendi yang terkena.");
                setFieldValue('penilaian', "Osteoartritis");
                setFieldValue('instruksi', "Latihan penguatan otot secara teratur, istirahat saat nyeri hebat, penggunaan alat bantu jalan jika diperlukan, jaga berat badan ideal, kompres hangat pada sendi yang nyeri.");
                setFieldValue('rtl', "Pemberian analgesik\nPemberian OAINS topikal\nTerapi fisik\nEdukasi latihan dan modifikasi aktivitas");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu");
                
                // Set vital signs
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '130/80');
                setFieldValue('nadi', '76');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Malaria
            else if (template === 'malaria') {
                setFieldValue('keluhan', "Pasien mengeluh demam tinggi yang hilang timbul disertai menggigil hebat dan berkeringat. Keluhan lainnya: sakit kepala, nyeri otot, mual, muntah, dan lemas. Riwayat bepergian ke daerah endemis malaria (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, Composmentis\nThorax : Cor reguler takikardi. Pulmo: Suara napas vesikuler +/+, ronkhi -/-, wheezing -/-\nAbdomen : Supel, nyeri tekan (-), hepatosplenomegali (+/-)\nKulit : Turgor cukup, akral hangat/dingin, ikterus (-/+), sianosis (-)\nHasil pemeriksaan RDT Malaria: Positif/Negatif (jika tersedia)");
                setFieldValue('penilaian', "Suspek Malaria");
                setFieldValue('instruksi', "Istirahat cukup, minum banyak air putih, minum obat secara teratur sesuai dosis dan jadwal, kontrol ulang sesuai jadwal yang ditentukan.");
                setFieldValue('rtl', "Pemeriksaan darah tepi/RDT Malaria\nPemberian antimalarial sesuai hasil pemeriksaan dan jenis plasmodium\nPemberian antipiretik\nEdukasi pencegahan malaria");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika kondisi memburuk");
                
                // Set vital signs
                setFieldValue('suhu', '39.0');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '108');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Demam Tifoid
            else if (template === 'typhoid') {
                setFieldValue('keluhan', "Pasien mengeluh demam yang meningkat secara bertahap selama beberapa hari, mencapai puncak di sore/malam hari. Disertai sakit kepala, mual, muntah, nyeri perut, dan nafsu makan menurun. Riwayat makan di luar rumah (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak lemah, Composmentis\nThorax : Cor reguler takikardi ringan. Pulmo: Suara napas vesikuler +/+, ronkhi -/-, wheezing -/-\nAbdomen : Supel, nyeri tekan di regio epigastrium dan kuadran kanan bawah (+), hepatosplenomegali (+/-)\nKulit : Turgor cukup, akral hangat, rose spots (-/+)\nHasil pemeriksaan Tubex/Widal: (jika tersedia)");
                setFieldValue('penilaian', "Suspek Demam Tifoid");
                setFieldValue('instruksi', "Istirahat total, diet lunak, minum banyak air putih, makan sedikit tapi sering, hindari makanan berlemak dan pedas.");
                setFieldValue('rtl', "Pemberian antibiotik\nPemberian antipiretik\nHidasi adekuat\nPemeriksaan Tubex/Widal jika memungkinkan\nEdukasi hygiene dan pencegahan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika kondisi memburuk");
                
                // Set vital signs
                setFieldValue('suhu', '38.8');
                setFieldValue('tensi', '100/70');
                setFieldValue('nadi', '98');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Varicella/Cacar Air
            else if (template === 'varicella') {
                setFieldValue('keluhan', "Pasien mengeluh munculnya bintik merah yang berubah menjadi lepuh berisi cairan, kemudian pecah dan mengering membentuk keropeng. Disertai demam, gatal, dan lemas. Riwayat kontak dengan penderita cacar air (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, Composmentis\nThorax : Cor dan Pulmo dalam batas normal\nKulit : Tampak lesi makula, papul, vesikel, dan keropeng dalam berbagai stadium di wajah, badan, dan ekstremitas. Distribusi lesi sentrifugal.");
                setFieldValue('penilaian', "Varicella/Cacar Air");
                setFieldValue('instruksi', "Istirahat total, hindari menggaruk lesi untuk mencegah infeksi sekunder dan bekas luka, jaga kebersihan diri, isolasi diri sampai semua lesi mengering, gunakan pakaian yang longgar dan lembut.");
                setFieldValue('rtl', "Pemberian antihistamin untuk mengurangi gatal\nPemberian antipiretik (hindari aspirin)\nPemberian antiviral jika < 72 jam onset\nEdukasi pencegahan penyebaran");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau segera jika timbul komplikasi");
                
                // Set vital signs
                setFieldValue('suhu', '38.2');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '76');
            }

            // Template Diare pada Anak
            else if (template === 'diare-anak') {
                setFieldValue('keluhan', "Anak mengalami BAB cair lebih dari 3x sehari sejak (sebutkan waktu) yang lalu. Frekuensi BAB sekitar ... kali/hari. Disertai muntah (+/-), demam (+/-), darah dalam tinja (-/+), dan nafsu makan menurun.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak (sehat/sakit ringan/sakit sedang/sakit berat), kesadaran composmentis\nTurgor kulit : Kembali (cepat/lambat)\nMata : (Normal/cekung/sangat cekung), air mata (ada/tidak ada)\nMulut dan lidah : (Basah/kering)\nRasa haus : (Minum biasa/haus/haus dan minum dengan rakus/tidak bisa minum)\nCapillary refill : < 2 detik\nThorax : Cor reguler normokardi, Pulmo dalam batas normal\nAbdomen : Supel, tidak kembung, bising usus normal/meningkat\nAntropometri : BB ... kg, TB ... cm, LK ... cm");
                setFieldValue('penilaian', "Diare Akut (tanpa/dengan dehidrasi ringan/sedang/berat) pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Teruskan pemberian ASI/makanan sesuai usia, berikan cairan rumah tangga, oralit sesuai anjuran, perhatikan tanda bahaya yang harus segera dibawa ke layanan kesehatan.");
                setFieldValue('rtl', "Rehidrasi dengan oralit sesuai derajat dehidrasi\nSuplemen zinc selama 10-14 hari\nEdukasi orangtua tentang pemberian cairan, meneruskan pemberian makanan, dan tanda bahaya\nJadwalkan kunjungan ulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk anak (sesuaikan dengan usia)
                setFieldValue('suhu', '37.8');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '120');
                setFieldValue('respirasi', '28');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template ISPA pada Anak
            else if (template === 'ispa-anak') {
                setFieldValue('keluhan', "Anak mengalami batuk dan pilek sejak (sebutkan waktu) yang lalu. Disertai demam (+/-), kesulitan bernapas (-/+), nafsu makan menurun, dan rewel.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak (sehat/sakit ringan/sakit sedang/sakit berat), kesadaran composmentis\nHidung : Sekret (serous/purulen), napas cuping hidung (-/+)\nTenggorokan : Faring hiperemis (+/-), tonsil (T1/T2/T3), pembesaran KGB (-/+)\nThorax : Cor dalam batas normal, Pulmo vesikuler +/+, ronkhi -/+, wheezing -/+\nRespirasi : RR ... x/menit, retraksi (-/+), stridor (-/+)\nAntropometri : BB ... kg, TB ... cm, LK ... cm");
                setFieldValue('penilaian', "ISPA (ringan/sedang/berat) pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Istirahat cukup, minum banyak air putih, tetap berikan ASI/makanan sesuai usia, jaga kebersihan dan kelembapan hidung, perhatikan tanda bahaya.");
                setFieldValue('rtl', "Pemberian antipiretik jika demam\nPemberian simptomatik sesuai gejala\nEdukasi orangtua tentang tanda bahaya dan cara pemberian obat\nJadwalkan kunjungan ulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2-3 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk anak (sesuaikan dengan usia)
                setFieldValue('suhu', '37.5');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '110');
                setFieldValue('respirasi', '32');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '50');
            }

            // Template Pneumonia pada Anak
            else if (template === 'pneumonia-anak') {
                setFieldValue('keluhan', "Anak mengalami batuk disertai kesulitan bernapas sejak (sebutkan waktu) yang lalu. Napas cepat (+), tarikan dinding dada ke dalam (+), demam (+), nafsu makan menurun, dan anak tampak lemah.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang/berat, kesadaran composmentis\nSuhu : ... °C\nRespirasi : RR ... x/menit (napas cepat/tidak), retraksi dinding dada (+), napas cuping hidung (+)\nThorax : Cor dalam batas normal, Pulmo suara napas bronkovesikuler, ronkhi basah halus/kasar (+), wheezing (-/+)\nStatus gizi : BB ... kg, TB ... cm, LK ... cm\nCapillary refill time : < 2 detik");
                setFieldValue('penilaian', "Pneumonia (tanpa tanda bahaya/dengan tanda bahaya) pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Istirahat total, minum banyak air putih, makan makanan bergizi dalam porsi kecil tapi sering, jaga suhu ruangan tetap nyaman.");
                setFieldValue('rtl', "Pemberian antibiotik ...\nPemberian bronkodilator jika ada wheezing\nPemberian antipiretik jika demam\nEdukasi tanda bahaya\nJadwalkan kunjungan ulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk anak dengan pneumonia
                setFieldValue('suhu', '38.5');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '130');
                setFieldValue('respirasi', '45');
                setFieldValue('spo2', '94');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template Demam pada Anak
            else if (template === 'demam-anak') {
                setFieldValue('keluhan', "Anak mengalami demam sejak (sebutkan waktu) yang lalu. Suhu tertinggi mencapai ... °C. Disertai batuk/pilek (+/-), nafsu makan menurun, anak tampak lemah dan rewel.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit ringan/sedang, kesadaran composmentis\nSuhu : ... °C\nKulit : Turgor baik, akral hangat, tidak ada ruam\nTHT : Faring hiperemis (+/-), membran timpani normal, tidak ada pembesaran KGB\nThorax : Cor dalam batas normal, Pulmo vesikuler +/+, ronkhi -/-, wheezing -/-\nAbdomen : Supel, tidak ada organomegali\nNeurologis : Tidak ada tanda rangsang meningeal, tidak ada kejang\nAntropometri : BB ... kg, TB ... cm, LK ... cm");
                setFieldValue('penilaian', "Demam (tanpa/dengan) fokus infeksi pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Kompres hangat pada ketiak/lipatan paha, banyak minum air putih, berpakaian tipis dan nyaman, istirahat cukup, tetap berikan ASI/makanan sesuai usia.");
                setFieldValue('rtl', "Pemberian antipiretik\nObservasi demam dan gejala penyerta\nEdukasi orangtua tentang tanda bahaya\nJadwalkan kunjungan ulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika demam ≥ 3 hari atau kondisi memburuk");
                
                // Set vital signs untuk anak demam
                setFieldValue('suhu', '38.2');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '120');
                setFieldValue('respirasi', '30');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '50');
            }

            // Template Eksim pada Anak
            else if (template === 'eksim-anak') {
                setFieldValue('keluhan', "Anak mengalami ruam kemerahan dan gatal pada kulit terutama di area pipi, leher, siku, dan belakang lutut. Keluhan bertambah parah pada malam hari dan saat berkeringat. Riwayat alergi dalam keluarga (+/-).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : Dalam batas normal\nKulit : Tampak lesi eritematosa, papul, dan ekskoriasi di area pipi, leher, siku, dan belakang lutut. Xerosis (+). Tidak ada infeksi sekunder.\nAntropometri : BB ... kg, TB ... cm, LK ... cm");
                setFieldValue('penilaian', "Dermatitis Atopik/Eksim pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Mandi dengan air hangat (tidak panas) dan sabun pH rendah, oleskan pelembab segera setelah mandi, hindari pakaian dari wol/bahan kasar, ganti deterjen dengan mild soap, hindari makanan pencetus (jika diketahui).");
                setFieldValue('rtl', "Pemberian kortikosteroid topikal mild sesuai area yang terkena\nPemberian pelembab hypoallergenic\nPemberian antihistamin oral untuk gatal berat terutama malam hari\nEdukasi perawatan kulit dan faktor pencetus");
                setFieldValue('alergi', "Riwayat atopi keluarga: (sebutkan jika ada)");
                setFieldValue('evaluasi', "Kontrol 1 minggu");
                
                // Set vital signs untuk anak dengan eksim
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template Campak
            else if (template === 'campak') {
                setFieldValue('keluhan', "Anak mengalami demam tinggi sejak (sebutkan waktu) yang lalu, disertai batuk, pilek, mata merah, dan muncul ruam kemerahan pada kulit yang dimulai dari wajah dan menyebar ke tubuh. Riwayat imunisasi campak (sudah/belum).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, kesadaran composmentis\nSuhu : ... °C\nTHT : Konjungtivitis (+), faring hiperemis, koplik spots (+/-)\nThorax : Cor reguler normokardi, Pulmo suara napas vesikuler, ronkhi -/+\nKulit : Tampak ruam makulo-papular eritematosa yang menyatu, distribusi di wajah, leher, badan, ekstremitas\nAntropometri : BB ... kg, TB ... cm, LK ... cm");
                setFieldValue('penilaian', "Campak pada anak usia ... (bulan/tahun)");
                setFieldValue('instruksi', "Istirahat total, perbanyak minum, makan makanan lunak bergizi, menjaga kebersihan mata, menjaga suhu tubuh, dan isolasi untuk mencegah penularan.");
                setFieldValue('rtl', "Pemberian antipiretik\nSuplemen vitamin A\nTetes mata jika konjungtivitis berat\nAntibiotik profilaksis untuk mencegah infeksi sekunder\nEdukasi komplikasi dan tanda bahaya");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk anak dengan campak
                setFieldValue('suhu', '39.0');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '125');
                setFieldValue('respirasi', '32');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template Mual Muntah Kehamilan
            else if (template === 'mual-hamil') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh mual dan muntah terutama di pagi hari. Frekuensi muntah ... kali/hari. Masih bisa makan dan minum sedikit-sedikit. Keluhan mengganggu aktivitas sehari-hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nKonjungtiva : Tidak anemis\nTurgor kulit : Baik\nAbdomen : TFU sesuai usia kehamilan, DJJ ... bpm, pergerakan janin (+)");
                setFieldValue('penilaian', "Emesis Gravidarum pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Makan dalam porsi kecil tapi sering, hindari makanan berminyak/pedas, konsumsi jahe/peppermint, hindari bau yang mencetuskan mual, istirahat cukup, minum banyak air putih.");
                setFieldValue('rtl', "Pemberian antiemetik yang aman untuk kehamilan jika diperlukan\nPemberian vitamin B6\nMonitoring asupan cairan dan gizi\nEdukasi tanda kegawatan yang harus segera ke rumah sakit");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau segera jika muntah memburuk/tidak bisa minum");
                
                // Set vital signs untuk ibu hamil
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '84');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Anemia dalam Kehamilan
            else if (template === 'anemia-hamil') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh lemas, pusing, kadang sesak napas ringan, dan mudah lelah. Nafsu makan baik. Pergerakan janin normal.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak pucat, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nKonjungtiva : Pucat\nLidah dan kuku : Pucat\nThorax : Cor reguler normokardia, murmur (-), Pulmo dalam batas normal\nAbdomen : TFU sesuai usia kehamilan, DJJ ... bpm, pergerakan janin (+)\nPemeriksaan Hb terakhir : ... g/dL");
                setFieldValue('penilaian', "Anemia (ringan/sedang/berat) dalam kehamilan pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Konsumsi makanan tinggi zat besi (daging merah, hati, sayuran hijau), minum tablet besi sesuai anjuran dengan air putih/jus jeruk (bukan teh/kopi), istirahat cukup.");
                setFieldValue('rtl', "Suplementasi tablet besi 2x1 tab\nSuplemen asam folat\nPemeriksaan darah rutin ulangan\nEdukasi pola makan dan cara konsumsi tablet besi\nJadwalkan kontrol kehamilan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu dengan hasil pemeriksaan Hb");
                
                // Set vital signs untuk ibu hamil dengan anemia
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '100/60');
                setFieldValue('nadi', '96');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Hipertensi dalam Kehamilan
            else if (template === 'hipertensi-hamil') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu dengan riwayat tekanan darah tinggi/baru terdeteksi. Keluhan pusing (+/-), pandangan kabur (-/+), nyeri kepala (+/-), bengkak pada kaki (+/-), pergerakan janin normal.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nKonjungtiva : Tidak anemis\nThorax : Cor dan Pulmo dalam batas normal\nAbdomen : TFU sesuai usia kehamilan, DJJ ... bpm, pergerakan janin (+)\nEkstremitas : Edema (-/+), refleks patella normal");
                setFieldValue('penilaian', "Hipertensi (kronik/gestasional/preeklamsia) pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Diet rendah garam, istirahat cukup, hindari stres, batasi aktivitas fisik berat, kenali tanda bahaya kehamilan.");
                setFieldValue('rtl', "Pemberian antihipertensi sesuai indikasi\nMonitoring tekanan darah\nPemeriksaan protein urin\nEvaluasi kesejahteraan janin\nEdukasi tanda preeklamsia berat");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau segera jika ada tanda bahaya");
                
                // Set vital signs untuk ibu hamil dengan hipertensi
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '150/95');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '86');
            }

            // Template Infeksi Saluran Kemih pada Kehamilan
            else if (template === 'isk-hamil' || template === 'uti-hamil') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh nyeri saat BAK, sering BAK, BAK sedikit-sedikit, nyeri pinggang bawah, demam (-/+). Keluhan dirasakan sejak ... hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nNyeri ketok CVA (+/-)\nAbdomen : TFU sesuai usia kehamilan, DJJ ... bpm, pergerakan janin normal\nUrinalisis : Leukosit (+), Nitrit (+/-), Protein (-/+)");
                setFieldValue('penilaian', "Infeksi Saluran Kemih pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Minum banyak air putih (minimal 2L/hari), kosongkan kandung kemih saat terasa ingin BAK, jaga kebersihan area genital.");
                setFieldValue('rtl', "Pemberian antibiotik sesuai hasil kultur urin/empiris\nAnalgetik jika nyeri\nPemeriksaan kultur urin\nEdukasi tanda perburukan\nKontrol ulang dengan hasil urinalisis");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu dengan hasil urinalisis atau segera jika keluhan memberat");
                
                // Set vital signs untuk ibu hamil dengan ISK
                setFieldValue('suhu', '37.2');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '84');
            }

            // Template Konstipasi pada Kehamilan
            else if (template === 'konstipasi-hamil') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh sulit BAB, BAB keras, perut terasa penuh dan tidak nyaman. Keluhan dirasakan sejak ... hari. Frekuensi BAB ... kali per minggu.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nAbdomen : TFU sesuai usia kehamilan, DJJ ... bpm, pergerakan janin normal, bising usus normal, nyeri tekan (-/+)");
                setFieldValue('penilaian', "Konstipasi pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Konsumsi makanan tinggi serat (buah, sayur), minum air putih minimal 2L/hari, olahraga ringan (jalan kaki), hindari menahan BAB, usahakan BAB pada jam yang sama setiap hari.");
                setFieldValue('rtl', "Pemberian laksatif yang aman untuk kehamilan\nAtur pola makan tinggi serat\nEdukasi pencegahan konstipasi\nKontrol dalam ANC rutin");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol pada kunjungan ANC berikutnya");
                
                // Set vital signs untuk ibu hamil dengan konstipasi
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Abortus Iminens
            else if (template === 'abortus-iminens') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh perdarahan pervaginam berwarna merah segar/kecoklatan dalam jumlah sedikit-sedang. Disertai nyeri perut bagian bawah ringan-sedang. Keluhan dirasakan sejak ... hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nKonjungtiva : Tidak anemis\nAbdomen : TFU sesuai/tidak sesuai usia kehamilan, nyeri tekan (-/+)\nGenital : Perdarahan pervaginam (+) jumlah sedikit/sedang, ostium uteri externum tertutup\nDJJ : ... bpm");
                setFieldValue('penilaian', "Abortus Iminens/Perdarahan Kehamilan Muda pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Istirahat total, hindari aktivitas berat, hubungan seksual, dan pengangkatan beban. Segera ke rumah sakit jika perdarahan bertambah banyak atau nyeri semakin hebat.");
                setFieldValue('rtl', "Pemberian progesteron\nTirah baring\nPemeriksaan USG untuk konfirmasi\nRujuk ke SPOG jika perdarahan bertambah banyak\nMonitoring TTV dan perdarahan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika perdarahan bertambah banyak");
                
                // Set vital signs untuk ibu hamil dengan abortus iminens
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '88');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Hiperemesis Gravidarum
            else if (template === 'hiperemesis') {
                setFieldValue('keluhan', "Ibu hamil G...P...A... usia kehamilan ... minggu mengeluh mual muntah hebat sejak ... minggu. Muntah > 10 kali/hari, tidak bisa makan dan minum, lemas, penurunan berat badan ... kg. BAK berkurang dan berwarna pekat.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak lemah, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nMulut : Bibir kering, lidah kotor\nTurgor kulit : Menurun\nAbdomen : TFU sesuai/tidak sesuai usia kehamilan, DJJ ... bpm\nUrin : Warna pekat, ketonuria (+/-)");
                setFieldValue('penilaian', "Hiperemesis Gravidarum (derajat I/II/III) pada G...P...A... UK ... minggu");
                setFieldValue('instruksi', "Makan dalam porsi sangat kecil tapi sering, hindari makanan berminyak/pedas/berbau menyengat, konsumsi makanan tinggi karbohidrat dan rendah lemak.");
                setFieldValue('rtl', "Rehidrasi oral/parenteral\nAntiemetik\nVitamin B kompleks\nRawat inap jika dehidrasi berat, ketonuria (+), atau tidak respon terhadap terapi oral\nEvakuasi psikologis (stressor)");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika muntah tidak terkontrol");
                
                // Set vital signs untuk ibu hamil dengan hiperemesis gravidarum
                setFieldValue('suhu', '36.5');
                setFieldValue('tensi', '100/60');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '75');
            }

            // Template Keluhan Menyusui/ASI
            else if (template === 'keluhan-asi') {
                setFieldValue('keluhan', "Ibu post partum usia ... hari/minggu/bulan, mengeluh produksi ASI sedikit/ASI tidak keluar/puting lecet/nyeri saat menyusui. Bayi rewel, berat badan bayi tidak naik sesuai harapan.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nPayudara : Membesar/tidak, putting menonjol/tidak, lecet pada puting (-/+), pembengkakan (-/+), kemerahan (-/+), nyeri tekan (-/+)\nTeknik menyusui : Benar/salah (jelaskan)\nTransfer ASI : Baik/kurang\nLet down reflex : Baik/kurang");
                setFieldValue('penilaian', "Keluhan ASI: (produksi ASI kurang/puting lecet/kesulitan menyusui) pada ibu menyusui usia ... hari/minggu post partum");
                setFieldValue('instruksi', "Menyusui on demand (8-12 kali/hari), perbaiki teknik menyusui, berikan kedua payudara setiap kali menyusui, oleskan ASI pada puting yang lecet, kompres hangat payudara, pijat payudara, minum cukup air, hindari stress.");
                setFieldValue('rtl', "Edukasi teknik menyusui yang benar\nEvaluasi posisi dan perlekatan bayi saat menyusui\nAjarkan pijat payudara dan kompres hangat\nMonitoring berat badan bayi\nDukungan emosional untuk ibu");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3-5 hari");
                
                // Set vital signs untuk ibu menyusui
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Mastitis
            else if (template === 'mastitis') {
                setFieldValue('keluhan', "Ibu menyusui usia ... minggu/bulan post partum, mengeluh nyeri, bengkak, dan kemerahan pada payudara (kanan/kiri). Disertai demam, menggigil, dan nyeri kepala. Keluhan dirasakan sejak ... hari. Riwayat trauma pada payudara (-/+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nPayudara (kanan/kiri) : Tampak kemerahan, bengkak, nyeri tekan (+), teraba keras, teraba area yang hangat, puting lecet (-/+)\nKelenjar getah bening aksila : Pembesaran (-/+)");
                setFieldValue('penilaian', "Mastitis pada ibu menyusui usia ... minggu/bulan post partum");
                setFieldValue('instruksi', "Tetap menyusui walau terasa nyeri, kompres hangat payudara sebelum menyusui, pijat lembut mengarah ke puting saat menyusui, kosongkan payudara setelah menyusui, istirahat cukup, minum banyak air.");
                setFieldValue('rtl', "Pemberian antibiotik ...\nAnalgesik\nKompres hangat\nPerbaikan teknik menyusui\nEdukasi pentingnya pengosongan payudara");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika keluhan bertambah parah");
                
                // Set vital signs untuk ibu dengan mastitis
                setFieldValue('suhu', '38.5');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '95');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Infeksi Nifas
            else if (template === 'infeksi-nifas') {
                setFieldValue('keluhan', "Ibu post partum usia ... hari, mengeluh demam, lochea berbau, nyeri perut bagian bawah. Persalinan ditolong oleh ... di ... tanggal ... dengan cara normal/SC. Keluhan dirasakan sejak ... hari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, kesadaran composmentis\nTanda vital : TD ... mmHg, HR ... x/menit, RR ... x/menit, Suhu ... °C\nMuka : Tampak pucat/tidak\nAbdomen : Involusio uteri baik/tidak, TFU ... jari di bawah pusat, kontraksi uterus baik/tidak, nyeri tekan (+)\nPerineum : Luka jahitan baik/tidak, kemerahan (-/+), edema (-/+), pus (-/+)\nLochea : Warna ..., bau (+/-), jumlah banyak/sedang/sedikit");
                setFieldValue('penilaian', "Infeksi Nifas (Metritis/Endometritis/Infeksi Luka Perineum) pada ibu post partum usia ... hari");
                setFieldValue('instruksi', "Istirahat total, kebersihan daerah kewanitaan, ganti pembalut sesering mungkin, mandi dengan air hangat 2x sehari, jaga kebersihan luka.");
                setFieldValue('rtl', "Pemberian antibiotik spektrum luas\nAnalgesik dan antipiretik\nPendidikan perawatan luka\nRujuk jika terdapat tanda sepsis\nPemeriksaan laboratorium");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk ibu dengan infeksi nifas
                setFieldValue('suhu', '38.6');
                setFieldValue('tensi', '110/70');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '22');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Pemantauan Stunting
            else if (template === 'stunting') {
                setFieldValue('keluhan', "Anak usia ... bulan/tahun dibawa orangtua untuk pemeriksaan tumbuh kembang. Orangtua khawatir anaknya pendek dibanding anak seusianya. Riwayat ASI ekslusif (+/-), MPASI mulai usia ... bulan. Pola makan: ... kali/hari, menu ...");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nBB : ... kg, BB/U: ... (normal/kurang/buruk)\nTB : ... cm, TB/U: ... (normal/pendek/sangat pendek)\nBB/TB : ... (normal/kurus/sangat kurus/gemuk)\nLK : ... cm, normal/mikrosefali/makrosefali\nPerkembangan (KPSP): Sesuai/Meragukan/Penyimpangan\nUmur tulang : Sesuai/tidak sesuai usia\nFaktor risiko: Riwayat BBLR, kelahiran prematur, penyakit kronis, ekonomi rendah, pendidikan ibu rendah, asupan gizi kurang");
                setFieldValue('penilaian', "Anak usia ... bulan/tahun dengan status gizi: ..., TB/U: ..., (stunting/berisiko stunting/normal)");
                setFieldValue('instruksi', "Berikan makanan tinggi protein (telur, ikan, daging, susu), makanan tinggi kalsium dan zinc, makan teratur 3x sehari dengan 2x cemilan, pastikan anak tidur cukup.");
                setFieldValue('rtl', "Suplementasi vitamin A, zink, dan zat besi sesuai indikasi\nOptimalisasi gizi seimbang\nStimulasi tumbuh kembang sesuai usia\nMonitoring pertumbuhan rutin setiap bulan\nEdukasi pola asuh");
                setFieldValue('alergi', "Tidak Ada/ ... ");
                setFieldValue('evaluasi', "Kontrol 1 bulan untuk pemantauan BB dan TB");
                
                // Set vital signs untuk anak dengan stunting/pemantauan
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '100');
                setFieldValue('respirasi', '24');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '45');
            }

            // Template Difteri
            else if (template === 'difteri') {
                setFieldValue('keluhan', "Anak usia ... tahun mengeluh demam, nyeri tenggorokan, sulit menelan, dan tampak pseudomembran putih keabu-abuan di tenggorokan. Disertai pembesaran kelenjar leher dan bengkak di leher. Riwayat imunisasi DPT lengkap/tidak lengkap/tidak tahu.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang/berat, kesadaran composmentis\nTanda vital : Suhu ... °C, HR ... x/menit, RR ... x/menit, SpO2 ... %\nTenggorokan : Pseudomembran putih keabu-abuan di tonsil/faring/laring\nLeher : Pembengkakan kelenjar getah bening servikal, edema leher (-/+)\nRespirasi : Stridor (-/+), retraksi (-/+), suara napas vesikuler, ronkhi (-/+)");
                setFieldValue('penilaian', "Suspek Difteri pada anak usia ... tahun");
                setFieldValue('instruksi', "Isolasi pasien, istirahat total, minum banyak cairan, diet lunak tinggi kalori.");
                setFieldValue('rtl', "Segera dirujuk ke rumah sakit\nIsolasi\nKoordinasi dengan Dinas Kesehatan\nDeteksi kontak erat\nPengambilan spesimen swab tenggorokan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Segera rujuk ke rumah sakit rujukan");
                
                // Set vital signs untuk anak dengan difteri
                setFieldValue('suhu', '38.5');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '115');
                setFieldValue('respirasi', '28');
                setFieldValue('spo2', '95');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '50');
            }

            // Template Pertusis
            else if (template === 'pertusis') {
                setFieldValue('keluhan', "Anak usia ... bulan/tahun mengalami batuk parokismal (batuk beruntun) yang diikuti tarikan napas berbunyi (whoop), muntah, dan sianosis selama batuk. Keluhan dirasakan sejak ... hari/minggu. Riwayat imunisasi DPT lengkap/tidak lengkap/tidak tahu.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, kesadaran composmentis\nTanda vital : Suhu ... °C, HR ... x/menit, RR ... x/menit saat tenang, SpO2 ... %\nFaring : Hiperemis (-/+)\nThorax : Suara napas vesikuler, ronkhi (-/+), wheezing (-/+)\nKarakteristik batuk : Batuk parokismal dengan whoop, diikuti muntah (+/-), sianosis saat batuk (-/+)");
                setFieldValue('penilaian', "Suspek Pertusis pada anak usia ... bulan/tahun");
                setFieldValue('instruksi', "Isolasi, istirahat cukup, makan makanan lunak dan sedikit tapi sering, jaga kelembapan ruangan, hindari iritan yang memicu batuk.");
                setFieldValue('rtl', "Pemberian antibiotik (azitromisin/eritromisin)\nRujuk jika gejala berat (sianosis, sulit bernapas, kejang)\nIdentifikasi dan profilaksis kontak erat\nLaporkan ke Dinas Kesehatan\nPemeriksaan darah (leukositosis dengan limfositosis)");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3-5 hari atau segera jika sesak napas");
                
                // Set vital signs untuk anak dengan pertusis
                setFieldValue('suhu', '37.0');
                setFieldValue('tensi', '90/60');
                setFieldValue('nadi', '110');
                setFieldValue('respirasi', '30');
                setFieldValue('spo2', '96');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '48');
            }

            // Template Karies Gigi
            else if (template === 'karies') {
                setFieldValue('keluhan', "Pasien mengeluh gigi berlubang, nyeri saat makan/minum manis/asam/dingin/panas, gigi terasa ngilu. Keluhan dirasakan sejak ... hari/minggu. Riwayat perawatan gigi sebelumnya: ...");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : Dalam batas normal\nStatus lokalis : Terdapat kavitas pada gigi ... (sebutkan nomor gigi), kedalaman karies mencapai email/dentin/pulpa, tes perkusi (-/+), tes termis (+/-), tes vitalitas pulpa (+/-)");
                setFieldValue('penilaian', "Karies (superfisialis/media/profunda) pada gigi ...");
                setFieldValue('instruksi', "Sikat gigi dengan benar 2x sehari, gunakan pasta gigi mengandung fluoride, kurangi makanan/minuman manis dan asam, kontrol ke dokter gigi secara rutin 6 bulan sekali.");
                setFieldValue('rtl', "Restorasi gigi dengan GIC/komposit/amalgam\nAplikasi fluoride topikal\nKonseling diet rendah gula\nEdukasi cara menyikat gigi dengan benar");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau segera jika keluhan bertambah");
                
                // Set vital signs normal
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '80');
                setFieldValue('respirasi', '16');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }

            // Template Gingivitis
            else if (template === 'gingivitis') {
                setFieldValue('keluhan', "Pasien mengeluh gusi berdarah saat menyikat gigi, gusi bengkak, kemerahan, dan nyeri. Keluhan dirasakan sejak ... hari/minggu. Riwayat menyikat gigi: ... kali sehari.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : Dalam batas normal\nStatus lokalis : Gingiva tampak edema, hiperemis, mudah berdarah saat probing (+), papila interdental membulat dan hiperemis (+), perdarahan spontan (-/+), kalkulus (-/+), plak (+)");
                setFieldValue('penilaian', "Gingivitis (ringan/sedang/berat)");
                setFieldValue('instruksi', "Sikat gigi dengan lembut 2x sehari dengan teknik yang benar, gunakan sikat gigi dengan bulu lembut, gunakan benang gigi setiap hari, berkumur dengan antiseptik.");
                setFieldValue('rtl', "Scaling dan root planing\nAplikasi antiseptik lokal\nPemberian obat kumur antiseptik\nEdukasi teknik menyikat gigi dan penggunaan benang gigi\nKonseling pencegahan gingivitis");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu setelah scaling");
                
                // Set vital signs normal
                setFieldValue('suhu', '36.8');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '78');
                setFieldValue('respirasi', '16');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '82');
            }

            // Template Periodontitis
            else if (template === 'periodontitis') {
                setFieldValue('keluhan', "Pasien mengeluh gigi goyang, gusi mudah berdarah, bau mulut tidak sedap, gusi turun, dan jarak antar gigi melebar. Keluhan dirasakan sejak ... minggu/bulan. Riwayat merokok: ... batang/hari, selama ... tahun.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : Dalam batas normal\nStatus lokalis : Resesi gingiva (+), poket periodontal ... mm, mobilitas gigi derajat ..., kalkulus (+), plak (+), perdarahan saat probing (+), kehilangan perlekatan ... mm");
                setFieldValue('penilaian', "Periodontitis (ringan/sedang/berat) (lokalisata/generalisata)");
                setFieldValue('instruksi', "Sikat gigi dengan benar 2x sehari, gunakan benang gigi, berkumur dengan obat kumur antiseptik, hindari merokok, kontrol gula darah jika pasien DM.");
                setFieldValue('rtl', "Scaling dan root planing\nPemberian antibiotik sistemik/topikal\nPemberian obat kumur antiseptik\nKuretase poket bila diperlukan\nRujuk ke periodontal spesialis jika berat\nKonseling berhenti merokok jika pasien perokok");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 minggu setelah scaling dan root planing");
                
                // Set vital signs normal
                setFieldValue('suhu', '36.7');
                setFieldValue('tensi', '125/85');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '85');
            }

            // Template Pulpitis
            else if (template === 'pulpitis') {
                setFieldValue('keluhan', "Pasien mengeluh nyeri gigi berdenyut, nyeri spontan, nyeri saat malam hari, nyeri menjalar ke kepala/telinga/rahang, nyeri terus-menerus. Keluhan dirasakan sejak ... hari. Skala nyeri: .../10.");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik, kesadaran composmentis\nTanda vital : Dalam batas normal, suhu ... °C\nStatus lokalis : Terdapat kavitas dalam pada gigi ... (sebutkan nomor gigi), perkusi (+), tes termis dengan air dingin: nyeri (+) dan bertahan lama/hilang setelah stimulus dihilangkan, tes vitalitas pulpa (+/-)");
                setFieldValue('penilaian', "Pulpitis (reversibel/irreversibel) pada gigi ...");
                setFieldValue('instruksi', "Minum obat sesuai resep, hindari mengunyah pada sisi gigi yang sakit, kompres dingin pada pipi area nyeri, jangan biarkan gigi berlubang tanpa perawatan.");
                setFieldValue('rtl', "Pemberian analgesik\nPemberian antibiotik jika ada infeksi\nPulp capping pada pulpitis reversibel\nPerawatan saluran akar pada pulpitis irreversibel\nExtraksi jika gigi tidak dapat dipertahankan");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 3 hari atau segera jika nyeri bertambah hebat/demam tinggi");
                
                // Set vital signs
                setFieldValue('suhu', '37.0');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '85');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '83');
            }

            // Template Abses Gigi
            else if (template === 'abses-gigi') {
                setFieldValue('keluhan', "Pasien mengeluh bengkak di gusi/pipi, nyeri berdenyut dan hebat, gigi terasa tinggi saat menggigit, demam, malaise, dan sulit membuka mulut. Keluhan dirasakan sejak ... hari. Riwayat trauma pada gigi (-/+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Tampak sakit sedang, kesadaran composmentis\nTanda vital : Suhu ... °C, TD ... mmHg, HR ... x/menit\nStatus lokalis : Pembengkakan pada regio ... (ekstra/intraoral), fluktuasi (+/-), nyeri tekan (+), gigi ... karies profunda, perkusi sangat sensitif (+), mobilitas gigi (+/-), limfadenopati servikal (+/-), trismus (-/+)\nPemeriksaan tambahan: Leukositosis (-/+)");
                setFieldValue('penilaian', "Abses dentoalveolar/periodontal pada regio gigi ...");
                setFieldValue('instruksi', "Minum obat sesuai resep, kompres hangat pada area bengkak, minum banyak air putih, istirahat cukup, hindari makanan keras, segera kontrol jika bengkak bertambah besar/sulit membuka mulut/sulit menelan/sesak napas.");
                setFieldValue('rtl', "Pemberian antibiotik spektrum luas\nPemberian analgesik kuat\nDrainase abses (jika fluktuasi +)\nEkstraksi/perawatan saluran akar gigi penyebab\nRujuk ke rumah sakit jika abses luas/tanda sepsis");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 2 hari atau segera jika kondisi memburuk");
                
                // Set vital signs untuk pasien dengan abses
                setFieldValue('suhu', '38.2');
                setFieldValue('tensi', '130/85');
                setFieldValue('nadi', '92');
                setFieldValue('respirasi', '20');
                setFieldValue('spo2', '97');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '84');
            }

            // Template Stomatitis
            else if (template === 'stomatitis') {
                setFieldValue('keluhan', "Pasien mengeluh sariawan/luka di mulut yang nyeri, sulit makan/minum/berbicara, demam (-/+), dan rasa terbakar di mulut. Keluhan dirasakan sejak ... hari. Riwayat stomatitis berulang (-/+), riwayat penyakit autoimun (-/+).");
                setFieldValue('pemeriksaan', "Keadaan Umum : Baik/tampak lelah, kesadaran composmentis\nTanda vital : Suhu ... °C, TD ... mmHg, HR ... x/menit, RR ... x/menit\nStatus lokalis : Ulserasi pada mukosa ... (bibir/lidah/pipi/gingiva/palatum) diameter ... mm, dasar ulkus putih kekuningan, tepi eritematous, nyeri tekan (+), jumlah lesi ... buah, limfadenopati (-/+)");
                setFieldValue('penilaian', "Stomatitis (aftosa/herpetik/traumatik/jamur)");
                setFieldValue('instruksi', "Minum banyak air putih, hindari makanan pedas/asam/keras/panas, sikat gigi dengan sikat lembut, berkumur dengan air garam hangat, hindari stres.");
                setFieldValue('rtl', "Pemberian obat kumur antiseptik\nPemberian pelega nyeri topikal\nPemberian kortikosteroid topikal untuk stomatitis aftosa\nPemberian antivirus untuk stomatitis herpetik\nPemberian antijamur untuk stomatitis kandidiasis\nAnalgesik sistemik jika diperlukan\nEdukasi pencegahan stomatitis berulang");
                setFieldValue('alergi', "Tidak Ada");
                setFieldValue('evaluasi', "Kontrol 1 minggu atau sesuai kebutuhan");
                
                // Set vital signs
                setFieldValue('suhu', '37.0');
                setFieldValue('tensi', '120/80');
                setFieldValue('nadi', '82');
                setFieldValue('respirasi', '18');
                setFieldValue('spo2', '98');
                setFieldValue('gcs', '15');
                setFieldValue('lingkar_perut', '80');
            }
            // Tutup loading setelah template diterapkan
            setTimeout(() => {
                Swal.close();
            }, 1000);
        });
    });
</script>
@stop