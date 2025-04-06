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
                  <form id="form-pendaftaran-pcare" method="POST" action="{{ route('api.pcare.pendaftaran.store') }}">
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
                                    <input type="text" class="form-control" id="no_rawat" name="no_rawat" required>
                                 </div>

                                 <div class="form-group">
                                    <label for="no_rkm_medis">No. Rekam Medis</label>
                                    <div class="input-group">
                                       <input type="text" class="form-control" id="no_rkm_medis" name="no_rkm_medis"
                                          required>
                                       <div class="input-group-append">
                                          <button class="btn btn-outline-secondary" type="button" id="btn-cari-pasien">
                                             <i class="fas fa-search"></i>
                                          </button>
                                       </div>
                                    </div>
                                 </div>

                                 <div class="form-group">
                                    <label for="nm_pasien">Nama Pasien</label>
                                    <input type="text" class="form-control" id="nm_pasien" name="nm_pasien" readonly>
                                 </div>

                                 <div class="form-group">
                                    <label for="noKartu">No. Kartu BPJS</label>
                                    <div class="input-group">
                                       <input type="text" class="form-control" id="noKartu" name="noKartu" required>
                                       <div class="input-group-append">
                                          <button class="btn btn-outline-primary" type="button" id="btn-cek-peserta">
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
                                       <h5 class="mb-0"><i class="fas fa-id-card-alt mr-2"></i>Detail Peserta BPJS</h5>
                                    </div>
                                    <div class="card-body">
                                       <div class="row">
                                          <div class="col-md-6">
                                             <div class="form-group">
                                                <label>Nomor Kartu</label>
                                                <p class="form-control-static font-weight-bold" id="det-noKartu">-</p>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="form-group">
                                                <label>Status</label>
                                                <p class="form-control-static font-weight-bold" id="det-status">-</p>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="form-group">
                                          <label>Nama Lengkap</label>
                                          <p class="form-control-static font-weight-bold" id="det-nama">-</p>
                                       </div>
                                       <div class="row">
                                          <div class="col-md-6">
                                             <div class="form-group">
                                                <label>Hubungan Keluarga</label>
                                                <p class="form-control-static" id="det-hubunganKeluarga">-</p>
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

                           <div class="card border-0 mb-3">
                              <div class="card-header bg-success text-white">
                                 <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Informasi Kunjungan</h5>
                              </div>
                              <div class="card-body">
                                 <div class="form-group">
                                    <label for="tglDaftar">Tanggal Pendaftaran</label>
                                    <input type="text" class="form-control datepicker" id="tglDaftar" name="tglDaftar"
                                       placeholder="dd-mm-yyyy" required>
                                 </div>

                                 <div class="form-group">
                                    <label for="kdPoli">Poli</label>
                                    <select class="form-control" id="kdPoli" name="kdPoli" required>
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

                                 <div class="form-group">
                                    <label for="nmPoli">Nama Poli</label>
                                    <input type="text" class="form-control" id="nmPoli" name="nmPoli" readonly>
                                 </div>

                                 <div class="form-group">
                                    <label for="keluhan">Keluhan</label>
                                    <textarea class="form-control" id="keluhan" name="keluhan" rows="3"></textarea>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="card border-0 mb-3">
                              <div class="card-header bg-info text-white">
                                 <h5 class="mb-0"><i class="fas fa-heartbeat mr-2"></i>Pemeriksaan</h5>
                              </div>
                              <div class="card-body">
                                 <div class="form-group">
                                    <div class="custom-control custom-radio custom-control-inline">
                                       <input type="radio" id="kunjSakit_true" name="kunjSakit" value="true"
                                          class="custom-control-input" checked>
                                       <label class="custom-control-label" for="kunjSakit_true">Kunjungan Sakit</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                       <input type="radio" id="kunjSakit_false" name="kunjSakit" value="false"
                                          class="custom-control-input">
                                       <label class="custom-control-label" for="kunjSakit_false">Kunjungan Sehat</label>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="sistole">Sistole (mmHg)</label>
                                          <input type="number" class="form-control" id="sistole" name="sistole"
                                             value="0">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="diastole">Diastole (mmHg)</label>
                                          <input type="number" class="form-control" id="diastole" name="diastole"
                                             value="0">
                                       </div>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="beratBadan">Berat Badan (kg)</label>
                                          <input type="number" class="form-control" id="beratBadan" name="beratBadan"
                                             value="0">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="tinggiBadan">Tinggi Badan (cm)</label>
                                          <input type="number" class="form-control" id="tinggiBadan" name="tinggiBadan"
                                             value="0">
                                       </div>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="respRate">Respiratory Rate</label>
                                          <input type="number" class="form-control" id="respRate" name="respRate"
                                             value="0">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label for="heartRate">Heart Rate</label>
                                          <input type="number" class="form-control" id="heartRate" name="heartRate"
                                             value="0">
                                       </div>
                                    </div>
                                 </div>

                                 <div class="form-group">
                                    <label for="lingkarPerut">Lingkar Perut (cm)</label>
                                    <input type="number" class="form-control" id="lingkarPerut" name="lingkarPerut"
                                       value="0">
                                 </div>

                                 <div class="form-group">
                                    <label for="rujukBalik">Rujuk Balik</label>
                                    <input type="number" class="form-control" id="rujukBalik" name="rujukBalik"
                                       value="0">
                                 </div>

                                 <div class="form-group">
                                    <label for="kdTkp">Tempat Kunjungan</label>
                                    <select class="form-control" id="kdTkp" name="kdTkp" required>
                                       <option value="">Pilih Tempat Kunjungan</option>
                                       <option value="10">Rawat Jalan (RJTP)</option>
                                       <option value="20">Rawat Inap (RITP)</option>
                                       <option value="50">Promotif Preventif</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                           <i class="fas fa-save mr-2"></i>Simpan Pendaftaran
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-lg ml-2">
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
                                    <label for="del_noKartu">No. Kartu BPJS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="del_noKartu" name="del_noKartu"
                                       required>
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
                                    <input type="text" class="form-control" id="del_noUrut" name="del_noUrut" required>
                                 </div>

                                 <div class="form-group">
                                    <label for="del_kdPoli">Kode Poli <span class="text-danger">*</span></label>
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
        
        // Otomatis cek peserta saat halaman dibuka jika nomor kartu sudah ada
        function checkPesertaOnLoad() {
            const noKartu = $('#noKartu').val();
            if (noKartu && noKartu.length > 0) {
                // Proses fetch data peserta BPJS secara otomatis
                fetchPesertaBPJS(noKartu);
            }
        }
        
        // Check URL parameter jika ada
        function getURLParameter(name) {
            const results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results ? decodeURIComponent(results[1]) : null;
        }
        
        // Cek apakah ada parameter no_rkm_medis di URL, jika ada ambil datanya
        const noRkmMedisParam = getURLParameter('no_rkm_medis');
        if (noRkmMedisParam) {
            $('#no_rkm_medis').val(noRkmMedisParam);
            fetchPatientData(noRkmMedisParam);
        } else {
            // Cek peserta otomatis jika nomor kartu sudah ada
            checkPesertaOnLoad();
        }
        
        // Event listener untuk otomatis cek peserta saat nomor kartu berubah
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
            const poliMap = {
                '001': 'Umum',
                '002': 'Gigi',
                '003': 'KIA',
                '004': 'KB',
                '005': 'IMS',
                '006': 'Psikologi',
                '007': 'Rehabilitasi Medik',
                '008': 'Poli Gizi',
                '009': 'Poli Akupuntur',
                '010': 'Poli Konseling',
                '011': 'Poli DOTS',
                '012': 'UGD'
            };
            
            const kdPoli = $(this).val();
            $('#nmPoli').val(poliMap[kdPoli] || '');
        });
        
        // Fungsi untuk mengambil data pasien dari nomor rekam medis
        function fetchPatientData(noRkmMedis) {
            if (!noRkmMedis) return;
            
            // Tampilkan loading
            $('#btn-cari-pasien').html('<i class="fas fa-spinner fa-spin"></i>');
            
            // AJAX untuk mengambil data pasien dari API pasien
            $.ajax({
                url: `/api/pasien/detail/${noRkmMedis}`, // Endpoint untuk detail pasien
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' || response.metaData?.code === 200) {
                        const pasienData = response.data || response.response;
                        
                        // Isi form dengan data pasien
                        $('#nm_pasien').val(pasienData.nm_pasien || pasienData.nama || '');
                        
                        // Isi nomor BPJS dari no_peserta pasien
                        if (pasienData.no_peserta) {
                            $('#noKartu').val(pasienData.no_peserta);
                            // Lakukan pengecekan data peserta BPJS otomatis jika no_peserta ada
                            if (pasienData.no_peserta.length > 0) {
                                fetchPesertaBPJS(pasienData.no_peserta);
                            }
                        }
                        
                        // Jika no_peserta tidak ada, coba cek dengan NIK
                        if ((!pasienData.no_peserta || pasienData.no_peserta.length === 0) && pasienData.no_ktp) {
                            fetchPesertaBPJSByNIK(pasienData.no_ktp);
                        }
                        
                        if (pasienData.kd_pj) {
                            $('#kdProviderPeserta').val(pasienData.kd_pj);
                        }
                        
                        // Notifikasi sukses
                        toastr.success('Data pasien berhasil dimuat');
                    } else {
                        // Coba alternative endpoint
                        $.ajax({
                            url: `/pasien/${noRkmMedis}`,
                            method: 'GET',
                            dataType: 'json',
                            success: function(altResponse) {
                                if (altResponse.status === 'success' || altResponse.data) {
                                    const pasienData = altResponse.data;
                                    
                                    // Isi form dengan data pasien
                                    $('#nm_pasien').val(pasienData.nm_pasien || pasienData.nama || '');
                                    
                                    // Isi nomor BPJS dari no_peserta pasien
                                    if (pasienData.no_peserta) {
                                        $('#noKartu').val(pasienData.no_peserta);
                                        // Lakukan pengecekan data peserta BPJS otomatis jika no_peserta ada
                                        if (pasienData.no_peserta.length > 0) {
                                            fetchPesertaBPJS(pasienData.no_peserta);
                                        }
                                    }
                                    
                                    // Jika no_peserta tidak ada, coba cek dengan NIK
                                    if ((!pasienData.no_peserta || pasienData.no_peserta.length === 0) && pasienData.no_ktp) {
                                        fetchPesertaBPJSByNIK(pasienData.no_ktp);
                                    }
                                    
                                    if (pasienData.kd_pj) {
                                        $('#kdProviderPeserta').val(pasienData.kd_pj);
                                    }
                                    
                                    // Notifikasi sukses
                                    toastr.success('Data pasien berhasil dimuat');
                                } else {
                                    // Reset form
                                    $('#nm_pasien').val('');
                                    $('#noKartu').val('');
                                    
                                    // Notifikasi error
                                    toastr.error('Data pasien tidak ditemukan');
                                }
                            },
                            error: function() {
                                // Reset form
                                $('#nm_pasien').val('');
                                $('#noKartu').val('');
                                
                                // Notifikasi error
                                toastr.error('Data pasien tidak ditemukan');
                            }
                        });
                    }
                },
                error: function() {
                    // Coba alternative endpoint
                    $.ajax({
                        url: `/pasien/${noRkmMedis}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(altResponse) {
                            if (altResponse.status === 'success' || altResponse.data) {
                                const pasienData = altResponse.data;
                                
                                // Isi form dengan data pasien
                                $('#nm_pasien').val(pasienData.nm_pasien || pasienData.nama || '');
                                
                                // Isi nomor BPJS dari no_peserta pasien
                                if (pasienData.no_peserta) {
                                    $('#noKartu').val(pasienData.no_peserta);
                                    // Lakukan pengecekan data peserta BPJS otomatis jika no_peserta ada
                                    if (pasienData.no_peserta.length > 0) {
                                        fetchPesertaBPJS(pasienData.no_peserta);
                                    }
                                }
                                
                                // Jika no_peserta tidak ada, coba cek dengan NIK
                                if ((!pasienData.no_peserta || pasienData.no_peserta.length === 0) && pasienData.no_ktp) {
                                    fetchPesertaBPJSByNIK(pasienData.no_ktp);
                                }
                                
                                if (pasienData.kd_pj) {
                                    $('#kdProviderPeserta').val(pasienData.kd_pj);
                                }
                                
                                // Notifikasi sukses
                                toastr.success('Data pasien berhasil dimuat');
                            } else {
                                // Reset form
                                $('#nm_pasien').val('');
                                $('#noKartu').val('');
                                
                                // Notifikasi error
                                toastr.error('Data pasien tidak ditemukan');
                            }
                        },
                        error: function() {
                            toastr.error('Terjadi kesalahan saat mengambil data pasien');
                        }
                    });
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
        
        // Form submit handler
        $('#form-pendaftaran-pcare').submit(function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            // Disable button dan tampilkan loading
            const submitBtn = $(this).find('button[type="submit"]');
            const btnText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...').attr('disabled', true);
            
            // Kirim data dengan AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.metaData && response.metaData.code === 201) {
                        const noUrut = response.response.message;
                        
                        // Notifikasi sukses
                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Pendaftaran PCare berhasil dengan No. Urut: ${noUrut}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect ke halaman detail atau list
                                window.location.href = '/pcare/pendaftaran';
                            }
                        });
                    } else {
                        // Tampilkan pesan error dari API
                        let errorMsg = 'Terjadi kesalahan saat menyimpan data';
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
                    let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                    
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
                    submitBtn.html(btnText).attr('disabled', false);
                }
            });
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
    });
</script>
@stop