@extends('adminlte::page')

@section('title', 'Skrining CKG Kesehatan')

@section('content_header')
<h1>Skrining CKG Kesehatan</h1>
@stop

@section('content')
<!-- Form Skrining CKG Kesehatan -->
<div class="card mb-4">
   <div class="card-header bg-primary text-white">
      <h4 class="mb-0">FORM SKRINING KESEHATAN</h4>
      <div class="text-white-50">Version 11.0</div>
   </div>

   <div class="card-body">
      <form id="formSkriningCKG" method="post" action="{{ route('skrining.store') }}">
         @csrf

         <!-- BAGIAN 1: IDENTITAS PASIEN -->
         <div class="section mb-4">
            <h5 class="section-title border-bottom pb-2">IDENTITAS PASIEN</h5>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Nama Lengkap</label>
                     <input type="text" class="form-control" name="nama_lengkap" required>
                  </div>
                  <div class="form-group">
                     <label>Tanggal Lahir</label>
                     <input type="date" class="form-control" name="tanggal_lahir" required>
                  </div>
                  <div class="form-group">
                     <label>NIK</label>
                     <input type="text" class="form-control" name="nik" required>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Jenis Kelamin</label>
                     <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label>No. Telepon</label>
                     <input type="text" class="form-control" name="no_telepon">
                  </div>
                  <div class="form-group">
                     <label>Alamat</label>
                     <textarea class="form-control" name="alamat" rows="2"></textarea>
                  </div>
               </div>
            </div>
         </div>

         <!-- BAGIAN 2: PEMERIKSAAN MANDIRI -->
         <div class="section mb-4">
            <div class="accordion" id="accordionPemeriksaanMandiri">
               <div class="card">
                  <div class="card-header bg-light" id="headingPemeriksaanMandiri">
                     <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse"
                           data-target="#collapsePemeriksaanMandiri" aria-expanded="true"
                           aria-controls="collapsePemeriksaanMandiri">
                           PEMERIKSAAN MANDIRI (8/8)
                        </button>
                     </h5>
                  </div>

                  <div id="collapsePemeriksaanMandiri" class="collapse show" aria-labelledby="headingPemeriksaanMandiri"
                     data-parent="#accordionPemeriksaanMandiri">
                     <div class="card-body p-0">
                        <table class="table table-bordered">
                           <thead class="bg-light">
                              <tr>
                                 <th style="width: 60%">Layanan</th>
                                 <th style="width: 25%">Status</th>
                                 <th style="width: 15%">Aksi</th>
                              </tr>
                           </thead>
                           <tbody>
                              <!-- Demografis -->
                              <tr>
                                 <td>Demografi Dewasa Perempuan</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalDemografi">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Hati -->
                              <tr>
                                 <td>Hati</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalHati">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Kanker Leher Rahim -->
                              <tr>
                                 <td>Kanker Leher Rahim</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalKankerLeherRahim">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Kesehatan Jiwa -->
                              <tr>
                                 <td>Kesehatan Jiwa</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalKesehatanJiwa">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Perilaku Merokok -->
                              <tr>
                                 <td>Perilaku Merokok</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalPerilakuMerokok">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Tekanan Darah & Gula Darah -->
                              <tr>
                                 <td>Tekanan Darah & Gula Darah</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalTekananDarah">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Tingkat Aktivitas Fisik -->
                              <tr>
                                 <td>Tingkat Aktivitas Fisik</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalAktivitasFisik">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>

                              <!-- Tuberkulosis -->
                              <tr>
                                 <td>Tuberkulosis</td>
                                 <td>
                                    <span class="badge badge-success rounded-circle"><i class="fas fa-check"></i></span>
                                 </td>
                                 <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                       data-target="#modalTuberkulosis">
                                       Input Data
                                    </button>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- BAGIAN 3: PEMERIKSAAN OLEH NAKES -->
         <div class="section mb-4">
            <div class="accordion" id="accordionPemeriksaanNakes">
               <div class="card">
                  <div class="card-header bg-light" id="headingPemeriksaanNakes">
                     <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse"
                           data-target="#collapsePemeriksaanNakes" aria-expanded="true"
                           aria-controls="collapsePemeriksaanNakes">
                           PEMERIKSAAN OLEH NAKES (8/21)
                        </button>
                     </h5>
                  </div>

                  <div id="collapsePemeriksaanNakes" class="collapse show" aria-labelledby="headingPemeriksaanNakes"
                     data-parent="#accordionPemeriksaanNakes">
                     <div class="card-body p-0">
                        <!-- Skrining Gizi, Tekanan Darah, dan Gula Darah -->
                        <div class="accordion" id="accordionSkriningGizi">
                           <div class="card border-0">
                              <div class="card-header bg-light" id="headingSkriningGizi">
                                 <h6 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                       data-target="#collapseSkriningGizi" aria-expanded="true"
                                       aria-controls="collapseSkriningGizi">
                                       Skrining Gizi, Tekanan Darah, dan Gula Darah Perempuan ≥ 40 Tahun
                                    </button>
                                 </h6>
                              </div>

                              <div id="collapseSkriningGizi" class="collapse show" aria-labelledby="headingSkriningGizi"
                                 data-parent="#accordionSkriningGizi">
                                 <div class="card-body p-0">
                                    <table class="table table-bordered">
                                       <thead class="bg-light">
                                          <tr>
                                             <th style="width: 50%">Layanan</th>
                                             <th style="width: 20%">Diperiksa</th>
                                             <th style="width: 15%">Status</th>
                                             <th style="width: 15%">Aksi</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <!-- Gizi -->
                                          <tr>
                                             <td>Gizi (BB - TB - Lingkar Perut) Perempuan</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input" id="checkGizi"
                                                      name="check_gizi" checked>
                                                   <label class="custom-control-label" for="checkGizi">Ya</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-light">Selesai diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalGizi">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>

                                          <!-- Tekanan Darah -->
                                          <tr>
                                             <td>Pemeriksaan Tekanan Darah</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input"
                                                      id="checkTekananDarah" name="check_tekanan_darah" checked>
                                                   <label class="custom-control-label"
                                                      for="checkTekananDarah">Ya</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-light">Selesai diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalTekananDarahNakes">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>

                                          <!-- Gula Darah -->
                                          <tr>
                                             <td>Skrining Gula Darah</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input"
                                                      id="checkGulaDarah" name="check_gula_darah" checked>
                                                   <label class="custom-control-label" for="checkGulaDarah">Ya</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-light">Selesai diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalGulaDarah">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <!-- Pemeriksaan PPOK -->
                        <div class="accordion mt-3" id="accordionPPOK">
                           <div class="card border-0">
                              <div class="card-header bg-light" id="headingPPOK">
                                 <h6 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                       data-target="#collapsePPOK" aria-expanded="true" aria-controls="collapsePPOK">
                                       Pemeriksaan PPOK (Skrining PUMA)
                                    </button>
                                 </h6>
                              </div>

                              <div id="collapsePPOK" class="collapse show" aria-labelledby="headingPPOK"
                                 data-parent="#accordionPPOK">
                                 <div class="card-body p-0">
                                    <table class="table table-bordered">
                                       <thead class="bg-light">
                                          <tr>
                                             <th style="width: 50%">Layanan</th>
                                             <th style="width: 20%">Diperiksa</th>
                                             <th style="width: 15%">Status</th>
                                             <th style="width: 15%">Aksi</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <tr>
                                             <td>Pemeriksaan PPOK (Skrining PUMA)</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input" id="checkPPOK"
                                                      name="check_ppok" checked>
                                                   <label class="custom-control-label" for="checkPPOK">Ya</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-light">Selesai diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalPPOK">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <!-- Pemeriksaan Tuberkulosis -->
                        <div class="accordion mt-3" id="accordionTuberkulosis">
                           <div class="card border-0">
                              <div class="card-header bg-light" id="headingTuberkulosis">
                                 <h6 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                       data-target="#collapseTuberkulosis" aria-expanded="true"
                                       aria-controls="collapseTuberkulosis">
                                       Pemeriksaan Tuberkulosis
                                    </button>
                                 </h6>
                              </div>

                              <div id="collapseTuberkulosis" class="collapse show" aria-labelledby="headingTuberkulosis"
                                 data-parent="#accordionTuberkulosis">
                                 <div class="card-body p-0">
                                    <table class="table table-bordered">
                                       <thead class="bg-light">
                                          <tr>
                                             <th style="width: 50%">Layanan</th>
                                             <th style="width: 20%">Diperiksa</th>
                                             <th style="width: 15%">Status</th>
                                             <th style="width: 15%">Aksi</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <tr>
                                             <td>Pemeriksaan Sputum - Tuberkulosis</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input" id="checkTB"
                                                      name="check_tb">
                                                   <label class="custom-control-label" for="checkTB">Tidak</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-secondary">Tidak diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalTB">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <!-- Skrining Laboratorium -->
                        <div class="accordion mt-3" id="accordionLab">
                           <div class="card border-0">
                              <div class="card-header bg-light" id="headingLab">
                                 <h6 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                       data-target="#collapseLab" aria-expanded="true" aria-controls="collapseLab">
                                       Skrining Laboratorium ≥ 40 thn Gula Darah, Fungsi Ginjal, Hati, Profil Lipid
                                    </button>
                                 </h6>
                              </div>

                              <div id="collapseLab" class="collapse show" aria-labelledby="headingLab"
                                 data-parent="#accordionLab">
                                 <div class="card-body p-0">
                                    <table class="table table-bordered">
                                       <thead class="bg-light">
                                          <tr>
                                             <th style="width: 50%">Layanan</th>
                                             <th style="width: 20%">Diperiksa</th>
                                             <th style="width: 15%">Status</th>
                                             <th style="width: 15%">Aksi</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <!-- Labs -->
                                          <tr>
                                             <td>Pemeriksaan Fibrosis/Sirosis Hati</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input" id="checkHati"
                                                      name="check_hati">
                                                   <label class="custom-control-label" for="checkHati">Tidak</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-secondary">Tidak diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalHatiLab">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>

                                          <tr>
                                             <td>Pemeriksaan Fungsi Ginjal (hanya untuk risiko HT DM)</td>
                                             <td>
                                                <div class="custom-control custom-switch">
                                                   <input type="checkbox" class="custom-control-input" id="checkGinjal"
                                                      name="check_ginjal">
                                                   <label class="custom-control-label" for="checkGinjal">Tidak</label>
                                                </div>
                                             </td>
                                             <td>
                                                <span class="badge badge-secondary">Tidak diperiksa</span>
                                             </td>
                                             <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                   data-toggle="modal" data-target="#modalGinjal">
                                                   Input Data
                                                </button>
                                             </td>
                                          </tr>

                                          <!-- Tambahkan bagian lain sesuai dengan gambar -->
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <!-- Bagian lain sesuai gambar -->
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-5">SIMPAN DATA SKRINING</button>
            <button type="reset" class="btn btn-secondary px-5 ml-2">RESET</button>
         </div>
      </form>
   </div>
</div>

<!-- MODALS untuk setiap pemeriksaan -->
<!-- Masing-masing modal akan berisi form pemeriksaan detail sesuai kategori -->

<!-- Modal Demografi -->
<div class="modal fade" id="modalDemografi" tabindex="-1" role="dialog" aria-labelledby="modalDemografiLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalDemografiLabel">Demografi Dewasa Perempuan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <!-- Isi form demografi dewasa perempuan -->
            <div class="form-group">
               <label>Pendidikan Terakhir</label>
               <select class="form-control" name="pendidikan">
                  <option value="">-- Pilih --</option>
                  <option value="SD">SD/Sederajat</option>
                  <option value="SMP">SMP/Sederajat</option>
                  <option value="SMA">SMA/Sederajat</option>
                  <option value="D3">D3</option>
                  <option value="S1">S1</option>
                  <option value="S2">S2</option>
                  <option value="S3">S3</option>
               </select>
            </div>
            <div class="form-group">
               <label>Pekerjaan</label>
               <select class="form-control" name="pekerjaan">
                  <option value="">-- Pilih --</option>
                  <option value="PNS">PNS</option>
                  <option value="Swasta">Karyawan Swasta</option>
                  <option value="Wirausaha">Wirausaha</option>
                  <option value="Petani">Petani</option>
                  <option value="IRT">Ibu Rumah Tangga</option>
                  <option value="Lainnya">Lainnya</option>
               </select>
            </div>
            <div class="form-group">
               <label>Status Pernikahan</label>
               <select class="form-control" name="status_pernikahan">
                  <option value="">-- Pilih --</option>
                  <option value="Belum Menikah">Belum Menikah</option>
                  <option value="Menikah">Menikah</option>
                  <option value="Cerai Hidup">Cerai Hidup</option>
                  <option value="Cerai Mati">Cerai Mati</option>
               </select>
            </div>
            <div class="form-group">
               <label>Pendapatan Rata-rata per Bulan</label>
               <select class="form-control" name="pendapatan">
                  <option value="">-- Pilih --</option>
                  <option value="<1jt">
                     < Rp 1.000.000</option>
                  <option value="1-3jt">Rp 1.000.000 - Rp 3.000.000</option>
                  <option value="3-5jt">Rp 3.000.000 - Rp 5.000.000</option>
                  <option value=">5jt">> Rp 5.000.000</option>
               </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="simpanDataForm('demografi')">Simpan</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal tambahan lainnya sesuai kebutuhan -->

<!-- Modal Hati Lab -->
<div class="modal fade" id="modalHatiLab" tabindex="-1" role="dialog" aria-labelledby="modalHatiLabLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalHatiLabLabel">Pemeriksaan Fibrosis/Sirosis Hati</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            @include('ckg.hati')
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="simpanDataForm('hati')">Simpan</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal Ginjal -->
<div class="modal fade" id="modalGinjal" tabindex="-1" role="dialog" aria-labelledby="modalGinjalLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalGinjalLabel">Pemeriksaan Fungsi Ginjal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <!-- Tambahkan konten form ginjal di sini -->
            <div class="form-group">
               <label>Kadar Kreatinin Serum</label>
               <input type="text" class="form-control" name="kreatinin_serum" placeholder="mg/dL">
            </div>
            <div class="form-group">
               <label>eGFR</label>
               <input type="text" class="form-control" name="egfr" placeholder="mL/min/1.73m²">
            </div>
            <div class="form-group">
               <label>Protein Urin</label>
               <select class="form-control" name="protein_urin">
                  <option value="">-- Pilih --</option>
                  <option value="Negatif">Negatif</option>
                  <option value="Positif 1">Positif 1</option>
                  <option value="Positif 2">Positif 2</option>
                  <option value="Positif 3">Positif 3</option>
               </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="simpanDataForm('ginjal')">Simpan</button>
         </div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {
        // Toggle switch untuk status diperiksa/tidak
        $('.custom-control-input').change(function() {
            var id = $(this).attr('id');
            var isChecked = $(this).prop('checked');
            
            if (isChecked) {
                $('label[for="' + id + '"]').text('Ya');
                $(this).closest('tr').find('.badge').removeClass('badge-secondary').addClass('badge-light').text('Selesai diperiksa');
            } else {
                $('label[for="' + id + '"]').text('Tidak');
                $(this).closest('tr').find('.badge').removeClass('badge-light').addClass('badge-secondary').text('Tidak diperiksa');
            }
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
                case 'hati':
                    url = "{{ route('api.skrining.hati') }}";
                    formData.riwayat_hepatitis = $('input[name="riwayat_hepatitis"]:checked').val();
                    formData.riwayat_kuning = $('input[name="riwayat_kuning"]:checked').val();
                    formData.riwayat_transfusi = $('input[name="riwayat_transfusi"]:checked').val();
                    formData.riwayat_tattoo = $('input[name="riwayat_tattoo"]:checked').val();
                    formData.riwayat_tindik = $('input[name="riwayat_tindik"]:checked').val();
                    formData.narkoba_suntik = $('input[name="narkoba_suntik"]:checked').val();
                    formData.odhiv = $('input[name="odhiv"]:checked').val();
                    formData.kolesterol = $('input[name="kolesterol"]:checked').val();
                    break;
                
                // Tambahkan case lain sesuai kebutuhan
                
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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
        
        // Buat simpanDataForm dapat diakses secara global
        window.simpanDataForm = simpanDataForm;
    });
</script>
@stop

@section('css')
<style>
   .section-title {
      color: #0069d9;
      font-weight: bold;
   }

   .card-header button.btn-link {
      color: #0069d9;
      text-decoration: none;
      font-weight: bold;
   }

   .card-header button.btn-link:hover {
      text-decoration: none;
   }

   .badge {
      padding: 0.4em;
      display: inline-flex;
      align-items: center;
      justify-content: center;
   }

   .badge.rounded-circle {
      width: 25px;
      height: 25px;
   }
</style>
@stop

@section('js')
<script>
   $(document).ready(function() {
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
@stop