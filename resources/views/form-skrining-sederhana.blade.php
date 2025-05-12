@extends('adminlte::page')

@section('title', 'Form Skrining Kesehatan Sederhana')

@section('content_header')
<h1>Form Skrining Kesehatan Sederhana</h1>
@endsection

@section('content')
<!-- Form Skrining Kesehatan Sederhana -->
<div class="card">
   <div class="card-header bg-primary text-white">
      <h4 class="mb-0">FORM SKRINING KESEHATAN</h4>
   </div>
   <div class="card-body">
      <form id="formSkriningKesehatan" method="post" action="{{ route('skrining.store') }}">
         @csrf

         <!-- DATA IDENTITAS PASIEN -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">DATA IDENTITAS PASIEN</h5>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>NIK</label>
                     <div class="input-group">
                        <input type="text" class="form-control" name="nik" id="nik" required>
                        <div class="input-group-append">
                           <span class="input-group-text bg-primary text-white" id="cari-nik" style="cursor: pointer;">
                              <i class="fas fa-search"></i> Cari
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <label>Nama Lengkap</label>
                     <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" required>
                  </div>
                  <div class="form-group">
                     <label>Tanggal Lahir</label>
                     <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
                  </div>
                  <div class="form-group">
                     <label>Jenis Kelamin</label>
                     <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                     </select>
                  </div>
                  <div class="form-group" id="pertanyaan_hamil" style="display:none;">
                     <label>Apakah Sedang Hamil?</label>
                     <select class="form-control" name="sedang_hamil">
                        <option value="">-- Pilih --</option>
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>No. Telepon</label>
                     <input type="text" class="form-control" name="no_telepon" id="no_telepon">
                  </div>
                  <div class="form-group">
                     <label>Status Perkawinan</label>
                     <select class="form-control" name="status_perkawinan" id="status_perkawinan">
                        <option value="">-- Pilih --</option>
                        <option value="Belum Menikah">Belum Menikah</option>
                        <option value="Menikah">Menikah</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>Apakah Anda Penyandang Disabilitas</label>
                     <select class="form-control" name="status_disabilitas" id="status_disabilitas">
                        <option value="">-- Pilih --</option>
                        <option value="Non Disabilitas">Non Disabilitas</option>
                        <option value="Penyandang Disabilitas">Penyandang Disabilitas</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>

         <!-- SKRINING MANDIRI -->
         <div class="section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h5 class="section-title m-0 font-weight-bold">Pemeriksaan Mandiri</h5>
               <small class="text-muted">Versi 1.0</small>
            </div>

            <div class="accordion" id="accordionSkriningMandiri">
               <div class="card">
                  <div class="card-header bg-light p-2" id="headingJumlahPemeriksaan">
                     <h6 class="mb-0">
                        <button
                           class="btn btn-link btn-block text-left text-dark text-decoration-none d-flex justify-content-between align-items-center pl-2"
                           type="button" data-toggle="collapse" data-target="#collapseJumlahPemeriksaan"
                           aria-expanded="true" aria-controls="collapseJumlahPemeriksaan">
                           <span><i class="fas fa-chevron-down mr-2"></i>Jumlah Pemeriksaan (8/8)</span>
                        </button>
                     </h6>
                  </div>
                  <div id="collapseJumlahPemeriksaan" class="collapse show" aria-labelledby="headingJumlahPemeriksaan"
                     data-parent="#accordionSkriningMandiri">
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
                                          data-toggle="modal" data-target="#modalDemografi">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="hati">
                                    <td>Hati</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalHati">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="kanker-leher-rahim">
                                    <td>Kanker Leher Rahim</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalKankerLeherRahim">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="kesehatan-jiwa">
                                    <td>Kesehatan Jiwa</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalKesehatanJiwa">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="perilaku-merokok">
                                    <td>Perilaku Merokok</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalPerilakuMerokok">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="tekanan-darah">
                                    <td>Tekanan Darah & Gula Darah</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalTekananDarah">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="aktivitas-fisik">
                                    <td>Tingkat Aktivitas Fisik</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalAktivitasFisik">Input Data</button>
                                    </td>
                                 </tr>
                                 <tr data-service="tuberkulosis">
                                    <td>Tuberkulosis</td>
                                    <td class="text-center"><span
                                          class="badge badge-secondary rounded-circle p-2 status-check"><i
                                             class="fas fa-check"></i></span></td>
                                    <td class="text-center">
                                       <button type="button" class="btn btn-outline-primary btn-sm btn-modal-trigger"
                                          data-toggle="modal" data-target="#modalTuberkulosis">Input Data</button>
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

         <!-- RIWAYAT DAN FAKTOR RISIKO -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">RIWAYAT DAN FAKTOR RISIKO</h5>

            <!-- Riwayat Penyakit -->
            <div class="mb-3">
               <label class="font-weight-bold">Riwayat Penyakit Pribadi:</label>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_pribadi[]" value="hipertensi">
                  <label class="form-check-label">Hipertensi</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_pribadi[]" value="diabetes">
                  <label class="form-check-label">Diabetes Melitus</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_pribadi[]" value="jantung">
                  <label class="form-check-label">Penyakit Jantung</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_pribadi[]" value="stroke">
                  <label class="form-check-label">Stroke</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_pribadi[]" value="kanker">
                  <label class="form-check-label">Kanker</label>
               </div>
            </div>

            <!-- Riwayat Keluarga -->
            <div class="mb-3">
               <label class="font-weight-bold">Riwayat Penyakit Keluarga:</label>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_keluarga[]" value="hipertensi">
                  <label class="form-check-label">Hipertensi</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_keluarga[]" value="diabetes">
                  <label class="form-check-label">Diabetes Melitus</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_keluarga[]" value="jantung">
                  <label class="form-check-label">Penyakit Jantung</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_keluarga[]" value="stroke">
                  <label class="form-check-label">Stroke</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="riwayat_keluarga[]" value="kanker">
                  <label class="form-check-label">Kanker</label>
               </div>
            </div>

            <!-- Faktor Risiko -->
            <div class="row mb-3">
               <div class="col-md-6">
                  <label class="font-weight-bold">Status Merokok:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="status_merokok" value="tidak_merokok">
                     <label class="form-check-label">Tidak Merokok</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="status_merokok" value="pernah_merokok">
                     <label class="form-check-label">Pernah Merokok, Berhenti < 10 tahun</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="status_merokok" value="merokok_aktif">
                     <label class="form-check-label">Perokok Aktif</label>
                  </div>
                  <div class="form-group mt-2" id="divJumlahRokok" style="display:none">
                     <label>Jumlah batang rokok/hari:</label>
                     <input type="number" class="form-control" name="jumlah_rokok">
                  </div>
               </div>
               <div class="col-md-6">
                  <label class="font-weight-bold">Aktivitas Fisik:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="aktivitas_fisik" value="cukup">
                     <label class="form-check-label">Cukup (≥30 menit/hari atau ≥150 menit/minggu)</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="aktivitas_fisik" value="kurang">
                     <label class="form-check-label">Kurang (<30 menit/hari atau <150 menit/minggu)</label>
                  </div>
               </div>
            </div>

            <!-- Konsumsi -->
            <div class="row mb-3">
               <div class="col-md-6">
                  <label class="font-weight-bold">Konsumsi Makanan:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="konsumsi[]" value="gula_berlebih">
                     <label class="form-check-label">Gula >4 sendok makan/hari</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="konsumsi[]" value="garam_berlebih">
                     <label class="form-check-label">Garam >1 sendok teh/hari</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="konsumsi[]" value="minyak_berlebih">
                     <label class="form-check-label">Minyak >5 sendok makan/hari</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="konsumsi[]" value="kurang_buah_sayur">
                     <label class="form-check-label">Sayur dan buah <5 porsi/hari</label>
                  </div>
               </div>
               <div class="col-md-6">
                  <label class="font-weight-bold">Konsumsi Alkohol:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="konsumsi_alkohol" value="tidak">
                     <label class="form-check-label">Tidak</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="konsumsi_alkohol" value="kadang">
                     <label class="form-check-label">Ya, tidak setiap hari</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="radio" name="konsumsi_alkohol" value="rutin">
                     <label class="form-check-label">Ya, setiap hari</label>
                  </div>
               </div>
            </div>
         </div>

         <!-- PEMERIKSAAN FISIK -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">PEMERIKSAAN FISIK</h5>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Berat Badan (kg)</label>
                     <input type="number" step="0.1" class="form-control" name="berat_badan" id="berat_badan">
                  </div>
                  <div class="form-group">
                     <label>Tinggi Badan (cm)</label>
                     <input type="number" step="0.1" class="form-control" name="tinggi_badan" id="tinggi_badan">
                  </div>
                  <div class="form-group">
                     <label>IMT (kg/m²)</label>
                     <input type="text" class="form-control" name="imt" id="imt" readonly>
                     <small id="kategoriBMI" class="form-text"></small>
                  </div>
                  <div class="form-group">
                     <label>Lingkar Pinggang (cm)</label>
                     <input type="number" step="0.1" class="form-control" name="lingkar_pinggang">
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Tekanan Darah Sistolik (mmHg)</label>
                     <input type="number" class="form-control" name="tekanan_sistolik" id="tekanan_sistolik">
                  </div>
                  <div class="form-group">
                     <label>Tekanan Darah Diastolik (mmHg)</label>
                     <input type="number" class="form-control" name="tekanan_diastolik" id="tekanan_diastolik">
                  </div>
                  <div class="form-group">
                     <label>Interpretasi Tekanan Darah</label>
                     <input type="text" class="form-control" name="interpretasi_td" id="interpretasi_td" readonly>
                  </div>
                  <div class="form-group">
                     <label>Denyut Nadi (per menit)</label>
                     <input type="number" class="form-control" name="denyut_nadi">
                  </div>
               </div>
            </div>
         </div>

         <!-- SKRINING DETEKSI DINI -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">SKRINING DETEKSI DINI</h5>

            <!-- Penglihatan -->
            <div class="mb-3">
               <label class="font-weight-bold">Deteksi Dini Gangguan Penglihatan:</label>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Visus Mata Kanan</label>
                        <select class="form-control" name="visus_mata_kanan">
                           <option value="">-- Pilih --</option>
                           <option value="6/6">6/6 (Normal)</option>
                           <option value="6/9">6/9 (Ringan)</option>
                           <option value="6/18">6/18 (Sedang)</option>
                           <option value="6/60">6/60 (Berat)</option>
                           <option value="3/60">3/60 (Sangat Berat)</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Visus Mata Kiri</label>
                        <select class="form-control" name="visus_mata_kiri">
                           <option value="">-- Pilih --</option>
                           <option value="6/6">6/6 (Normal)</option>
                           <option value="6/9">6/9 (Ringan)</option>
                           <option value="6/18">6/18 (Sedang)</option>
                           <option value="6/60">6/60 (Berat)</option>
                           <option value="3/60">3/60 (Sangat Berat)</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="katarak" value="1">
                  <label class="form-check-label">Suspek Katarak</label>
               </div>
            </div>

            <!-- Pendengaran -->
            <div class="mb-3">
               <label class="font-weight-bold">Deteksi Dini Gangguan Pendengaran:</label>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Tes Berbisik Telinga Kanan</label>
                        <select class="form-control" name="pendengaran_kanan">
                           <option value="">-- Pilih --</option>
                           <option value="normal">Normal</option>
                           <option value="gangguan">Gangguan pendengaran</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Tes Berbisik Telinga Kiri</label>
                        <select class="form-control" name="pendengaran_kiri">
                           <option value="">-- Pilih --</option>
                           <option value="normal">Normal</option>
                           <option value="gangguan">Gangguan pendengaran</option>
                        </select>
                     </div>
                  </div>
               </div>
            </div>

            <!-- PPOK -->
            <div class="mb-3">
               <label class="font-weight-bold">Deteksi Dini PPOK (Khusus usia > 40 tahun dan merokok):</label>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="gejala_ppok[]" value="napas_pendek">
                  <label class="form-check-label">Napas pendek saat aktivitas</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="gejala_ppok[]" value="dahak">
                  <label class="form-check-label">Dahak dari paru atau kesulitan mengeluarkan dahak</label>
               </div>
               <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="gejala_ppok[]" value="batuk">
                  <label class="form-check-label">Batuk diluar kondisi selesma/flu</label>
               </div>
            </div>

            <!-- Kanker -->
            <div class="mb-3">
               <label class="font-weight-bold">Deteksi Dini Kanker:</label>

               <!-- Kanker Serviks (khusus wanita) -->
               <div class="kanker-wanita" style="display:none">
                  <label>Deteksi Dini Kanker Serviks:</label>
                  <div class="form-group">
                     <select class="form-control" name="pemeriksaan_iva">
                        <option value="">-- Status Pemeriksaan IVA --</option>
                        <option value="belum">Belum dilakukan</option>
                        <option value="negatif">Dilakukan - Hasil Negatif</option>
                        <option value="positif">Dilakukan - Hasil Positif</option>
                     </select>
                  </div>

                  <div class="form-group">
                     <label>Deteksi Dini Kanker Payudara:</label>
                     <select class="form-control" name="pemeriksaan_sadanis">
                        <option value="">-- Status Pemeriksaan SADANIS --</option>
                        <option value="normal">Normal</option>
                        <option value="benjolan">Ditemukan benjolan</option>
                        <option value="suspek">Suspek kanker</option>
                     </select>
                  </div>
               </div>

               <!-- Kanker Paru -->
               <div class="form-group">
                  <label>Faktor Risiko Kanker Paru:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="faktor_risiko_kanker_paru[]"
                        value="paparan_kerja">
                     <label class="form-check-label">Riwayat tempat kerja mengandung zat karsinogenik</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="faktor_risiko_kanker_paru[]"
                        value="lingkungan_berisiko">
                     <label class="form-check-label">Lingkungan tempat tinggal berpotensi tinggi</label>
                  </div>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="faktor_risiko_kanker_paru[]"
                        value="rumah_tidak_sehat">
                     <label class="form-check-label">Lingkungan dalam rumah yang tidak sehat</label>
                  </div>
               </div>

               <!-- Kanker Kolorektal -->
               <div class="form-group">
                  <label>Faktor Risiko Kanker Kolorektal:</label>
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="faktor_risiko_kolorektal" value="1">
                     <label class="form-check-label">Riwayat keluarga kanker kolorektal generasi pertama</label>
                  </div>
               </div>
            </div>

            <!-- Pemeriksaan Lab (opsional) -->
            <div class="mb-3">
               <label class="font-weight-bold">Data Pemeriksaan Laboratorium (opsional):</label>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Gula Darah Sewaktu (mg/dL)</label>
                        <input type="number" class="form-control" name="gula_darah_sewaktu">
                     </div>
                     <div class="form-group">
                        <label>Asam Urat (mg/dL)</label>
                        <input type="number" step="0.1" class="form-control" name="asam_urat">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Kolesterol Total (mg/dL)</label>
                        <input type="number" class="form-control" name="kolesterol_total">
                     </div>
                     <div class="form-group">
                        <label>Hemoglobin (g/dL)</label>
                        <input type="number" step="0.1" class="form-control" name="hemoglobin">
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- KESIMPULAN DAN SARAN -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">KESIMPULAN DAN SARAN</h5>
            <div class="form-group">
               <label>Diagnosis</label>
               <textarea class="form-control" name="diagnosis" rows="2"></textarea>
            </div>
            <div class="form-group">
               <label>Edukasi dan Saran</label>
               <textarea class="form-control" name="saran" rows="3"></textarea>
            </div>
            <div class="form-group">
               <label>Rencana Tindak Lanjut</label>
               <select class="form-control" name="tindak_lanjut">
                  <option value="">-- Pilih --</option>
                  <option value="edukasi">Edukasi dan Monitoring</option>
                  <option value="kontrol">Kontrol Ulang</option>
                  <option value="rujuk">Rujuk ke Faskes Lanjutan</option>
               </select>
            </div>
         </div>

         <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-5">SIMPAN</button>
            <button type="reset" class="btn btn-secondary px-5 ml-2">RESET</button>
         </div>
      </form>
   </div>
</div>
@endsection

@section('css')
<style>
   .section-title {
      color: #0069d9;
      font-weight: bold;
   }

   .form-check {
      margin-bottom: 0.5rem;
   }

   .form-check-label {
      font-weight: normal;
   }

   .font-weight-bold {
      font-weight: bold;
      margin-bottom: 0.5rem;
      display: block;
   }
</style>
@endsection

@section('js')
<script>
   $(document).ready(function() {
        // Menampilkan/menyembunyikan input jumlah rokok
        $('input[name="status_merokok"]').change(function() {
            if ($(this).val() == 'merokok_aktif' || $(this).val() == 'pernah_merokok') {
                $('#divJumlahRokok').show();
            } else {
                $('#divJumlahRokok').hide();
            }
        });

        // Menampilkan/menyembunyikan pemeriksaan khusus wanita
        $('#jenis_kelamin').change(function() {
            if ($(this).val() == 'P') {
                $('.kanker-wanita').show();
                $('#pertanyaan_hamil').show();
            } else {
                $('.kanker-wanita').hide();
                $('#pertanyaan_hamil').hide();
            }
        });

        // Menghitung IMT
        function hitungIMT() {
            var beratBadan = parseFloat($('#berat_badan').val());
            var tinggiBadan = parseFloat($('#tinggi_badan').val());
            
            if (beratBadan > 0 && tinggiBadan > 0) {
                // Konversi cm ke m
                var tinggiBadanMeter = tinggiBadan / 100;
                // Hitung IMT
                var imt = beratBadan / (tinggiBadanMeter * tinggiBadanMeter);
                $('#imt').val(imt.toFixed(1));
                
                // Kategori IMT
                var kategori = '';
                if (imt < 18.5) {
                    kategori = 'Berat badan kurang';
                } else if (imt >= 18.5 && imt < 25) {
                    kategori = 'Berat badan normal';
                } else if (imt >= 25 && imt < 30) {
                    kategori = 'Berat badan berlebih (Pre-obesitas)';
                } else if (imt >= 30) {
                    kategori = 'Obesitas';
                }
                $('#kategoriBMI').text(kategori);
            }
        }

        $('#berat_badan, #tinggi_badan').keyup(hitungIMT);
        
        // Interpretasi Tekanan Darah
        function interpretasiTD() {
            var sistolik = parseInt($('#tekanan_sistolik').val());
            var diastolik = parseInt($('#tekanan_diastolik').val());
            
            if (sistolik > 0 && diastolik > 0) {
                var interpretasi = '';
                if (sistolik < 120 && diastolik < 80) {
                    interpretasi = 'Normal';
                } else if ((sistolik >= 120 && sistolik <= 139) || (diastolik >= 80 && diastolik <= 89)) {
                    interpretasi = 'Prehipertensi';
                } else if ((sistolik >= 140 && sistolik <= 159) || (diastolik >= 90 && diastolik <= 99)) {
                    interpretasi = 'Hipertensi derajat 1';
                } else if (sistolik >= 160 || diastolik >= 100) {
                    interpretasi = 'Hipertensi derajat 2';
                }
                $('#interpretasi_td').val(interpretasi);
            }
        }

        $('#tekanan_sistolik, #tekanan_diastolik').keyup(interpretasiTD);
        
        // Tambahkan notifikasi sukses jika ada
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
            });
        @endif
    });
</script>
@endsection

{{-- Modal untuk semua skrining dipindahkan ke form-skrining-minimal.blade.php
@include('ckg.demografi')
@include('ckg.hati')
@include('ckg.kanker-leher-rahim')
@include('ckg.kesehatan-jiwa')
@include('ckg.perilaku-merokok')
@include('ckg.tekanan-darah')
@include('ckg.aktivitas-fisik')
@include('ckg.tuberkulosis')
--}}