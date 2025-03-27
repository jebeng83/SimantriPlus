@extends('adminlte::page')

@section('title', 'Data Ibu Hamil')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-female mr-2"></i>Data Ibu Hamil</h1>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Data Ibu Hamil</li>
   </ol>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-12">
      <!-- Form Card -->
      <div class="card card-primary card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-edit mr-2"></i>{{ isset($dataIbuHamil) ? 'Edit' : 'Form' }} Data Ibu Hamil
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <form id="formDataIbuHamil" method="POST"
               action="{{ isset($dataIbuHamil) ? route('anc.data-ibu-hamil.update', $dataIbuHamil->id_hamil) : route('anc.data-ibu-hamil.store') }}">
               @csrf
               @if(isset($dataIbuHamil))
               @method('PUT')
               @endif
               <!-- Data Wajib -->
               <div class="form-group">
                  <div class="card card-info">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Data Wajib</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="nik">NIK <span class="text-danger">*</span></label>
                                 <div class="input-group">
                                    <input type="text" class="form-control" id="nik" name="nik"
                                       value="{{ isset($dataIbuHamil) ? $dataIbuHamil->nik : '' }}" required>
                                    <div class="input-group-append">
                                       <button type="button" class="btn btn-primary" id="btnCariNIK">
                                          <i class="fas fa-search"></i> Cari
                                       </button>
                                    </div>
                                 </div>
                                 <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="belumMemilikiNIK">
                                    <label class="form-check-label" for="belumMemilikiNIK">
                                       Ceklist disini apabila Ibu Hamil belum memiliki NIK
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label for="kehamilan_ke">Kehamilan ke <span class="text-danger">*</span></label>
                                 <select class="form-control dropdown-simple" id="kehamilan_ke" name="kehamilan_ke"
                                    required>
                                    <option value="">- Pilih -</option>
                                    @for($i = 1; $i <= 10; $i++) <option value="{{ $i }}" {{ isset($dataIbuHamil) &&
                                       $dataIbuHamil->kehamilan_ke == $i ? 'selected' : '' }}>{{ $i }}</option>
                                       @endfor
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label for="status">Status <span class="text-danger">*</span></label>
                                 <select class="form-control dropdown-status" id="status" name="status" required>
                                    <option value="Hamil" {{ isset($dataIbuHamil) && $dataIbuHamil->status == 'Hamil' ?
                                       'selected' : '' }}>Hamil</option>
                                    <option value="Melahirkan" {{ isset($dataIbuHamil) && $dataIbuHamil->status ==
                                       'Melahirkan' ? 'selected' : '' }}>Melahirkan</option>
                                    <option value="Abortus" {{ isset($dataIbuHamil) && $dataIbuHamil->status ==
                                       'Abortus' ? 'selected' : '' }}>Abortus</option>
                                 </select>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="no_rkm_medis">Nomor Rekam Medis <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="no_rkm_medis" name="no_rkm_medis"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->no_rkm_medis : '' }}" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="tgl_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                 <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->tgl_lahir->format('Y-m-d') : '' }}"
                                    required>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="nomor_kk">Nomor KK <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="nomor_kk" name="nomor_kk"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->nomor_kk : '' }}" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="nama">Nama <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="nama" name="nama"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->nama : '' }}" required>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Kesehatan -->
               <div class="form-group">
                  <div class="card card-success">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-heartbeat mr-2"></i>Data Kesehatan</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="berat_badan_sebelum_hamil">Berat Badan Sebelum Hamil (kg)</label>
                                 <input type="number" step="0.01" class="form-control" id="berat_badan_sebelum_hamil"
                                    name="berat_badan_sebelum_hamil"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->berat_badan_sebelum_hamil : '' }}">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="tinggi_badan">Tinggi Badan (cm)</label>
                                 <input type="number" step="0.01" class="form-control" id="tinggi_badan"
                                    name="tinggi_badan"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->tinggi_badan : '' }}">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="lila">LILA (cm)</label>
                                 <input type="number" step="0.01" class="form-control" id="lila" name="lila"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->lila : '' }}">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="imt_sebelum_hamil">IMT Sebelum Hamil</label>
                                 <input type="number" step="0.01" class="form-control" id="imt_sebelum_hamil"
                                    name="imt_sebelum_hamil"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->imt_sebelum_hamil : '' }}" readonly>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="status_gizi">Status Gizi</label>
                                 <input type="text" class="form-control" id="status_gizi" name="status_gizi"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->status_gizi : '' }}" readonly>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label>Jumlah Janin</label>
                                 <div class="d-flex">
                                    <div class="form-check mr-3">
                                       <input class="form-check-input" type="radio" name="jumlah_janin"
                                          value="Tidak Diketahui" id="janin_tidak_diketahui" {{ isset($dataIbuHamil) &&
                                          $dataIbuHamil->jumlah_janin == 'Tidak Diketahui' ? 'checked' : '' }}>
                                       <label class="form-check-label" for="janin_tidak_diketahui">
                                          Tidak Diketahui
                                       </label>
                                    </div>
                                    <div class="form-check mr-3">
                                       <input class="form-check-input" type="radio" name="jumlah_janin" value="Tunggal"
                                          id="janin_tunggal" {{ isset($dataIbuHamil) && $dataIbuHamil->jumlah_janin ==
                                       'Tunggal' ? 'checked' : '' }}>
                                       <label class="form-check-label" for="janin_tunggal">
                                          Tunggal
                                       </label>
                                    </div>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="jumlah_janin" value="Ganda"
                                          id="janin_ganda" {{ isset($dataIbuHamil) && $dataIbuHamil->jumlah_janin ==
                                       'Ganda' ? 'checked' : '' }}>
                                       <label class="form-check-label" for="janin_ganda">
                                          Ganda
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label>Jarak Kehamilan Sebelumnya</label>
                                 <div class="input-group">
                                    <input type="number" class="form-control" name="jarak_kehamilan_tahun"
                                       placeholder="Tahun"
                                       value="{{ isset($dataIbuHamil) ? $dataIbuHamil->jarak_kehamilan_tahun : '' }}">
                                    <input type="number" class="form-control" name="jarak_kehamilan_bulan"
                                       placeholder="Bulan"
                                       value="{{ isset($dataIbuHamil) ? $dataIbuHamil->jarak_kehamilan_bulan : '' }}">
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="hari_pertama_haid">Hari Pertama Haid Terakhir (HPHT)</label>
                                 <input type="date" class="form-control" id="hari_pertama_haid" name="hari_pertama_haid"
                                    value="{{ isset($dataIbuHamil) && $dataIbuHamil->hari_pertama_haid ? $dataIbuHamil->hari_pertama_haid->format('Y-m-d') : '' }}">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="hari_perkiraan_lahir">Hari Perkiraan Lahir (HPL)</label>
                                 <input type="date" class="form-control" id="hari_perkiraan_lahir"
                                    name="hari_perkiraan_lahir"
                                    value="{{ isset($dataIbuHamil) && $dataIbuHamil->hari_perkiraan_lahir ? $dataIbuHamil->hari_perkiraan_lahir->format('Y-m-d') : '' }}">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="golongan_darah">Golongan Darah</label>
                                 <select class="form-control" id="golongan_darah" name="golongan_darah">
                                    <option value="">- Pilih -</option>
                                    <option value="A" {{ isset($dataIbuHamil) && $dataIbuHamil->golongan_darah == 'A' ?
                                       'selected' : '' }}>A</option>
                                    <option value="B" {{ isset($dataIbuHamil) && $dataIbuHamil->golongan_darah == 'B' ?
                                       'selected' : '' }}>B</option>
                                    <option value="AB" {{ isset($dataIbuHamil) && $dataIbuHamil->golongan_darah == 'AB'
                                       ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ isset($dataIbuHamil) && $dataIbuHamil->golongan_darah == 'O' ?
                                       'selected' : '' }}>O</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="rhesus">Rhesus</label>
                                 <select class="form-control" id="rhesus" name="rhesus">
                                    <option value="">- Pilih -</option>
                                    <option value="Positif" {{ isset($dataIbuHamil) && $dataIbuHamil->rhesus ==
                                       'Positif' ? 'selected' : '' }}>Positif (+)</option>
                                    <option value="Negatif" {{ isset($dataIbuHamil) && $dataIbuHamil->rhesus ==
                                       'Negatif' ? 'selected' : '' }}>Negatif (-)</option>
                                 </select>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="riwayat_penyakit">Riwayat Penyakit yang diderita ibu</label>
                                 <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit"
                                    rows="3">{{ isset($dataIbuHamil) ? $dataIbuHamil->riwayat_penyakit : '' }}</textarea>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="riwayat_alergi">Riwayat Alergi</label>
                                 <textarea class="form-control" id="riwayat_alergi" name="riwayat_alergi"
                                    rows="3">{{ isset($dataIbuHamil) ? $dataIbuHamil->riwayat_alergi : '' }}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Administrasi -->
               <div class="form-group">
                  <div class="card card-warning">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>Data Administrasi</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label>Kepemilikan Buku KIA <span class="text-danger">*</span></label>
                                 <div class="d-flex">
                                    <div class="form-check mr-3">
                                       <input class="form-check-input" type="radio" name="kepemilikan_buku_kia"
                                          value="1" id="buku_kia_ya" required {{ isset($dataIbuHamil) &&
                                          $dataIbuHamil->kepemilikan_buku_kia ? 'checked' : '' }}>
                                       <label class="form-check-label" for="buku_kia_ya">
                                          Ya
                                       </label>
                                    </div>
                                    <div class="form-check">
                                       <input class="form-check-input" type="radio" name="kepemilikan_buku_kia"
                                          value="0" id="buku_kia_tidak" required {{ isset($dataIbuHamil) &&
                                          !$dataIbuHamil->kepemilikan_buku_kia ? 'checked' : '' }}>
                                       <label class="form-check-label" for="buku_kia_tidak">
                                          Tidak
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="jaminan_kesehatan">Jaminan Kesehatan</label>
                                 <select class="form-control" id="jaminan_kesehatan" name="jaminan_kesehatan">
                                    <option value="">- Pilih -</option>
                                    <option value="JKN" {{ isset($dataIbuHamil) && $dataIbuHamil->jaminan_kesehatan ==
                                       'JKN' ? 'selected' : '' }}>JKN</option>
                                    <option value="Jamkesda" {{ isset($dataIbuHamil) && $dataIbuHamil->jaminan_kesehatan
                                       == 'Jamkesda' ? 'selected' : '' }}>Jamkesda</option>
                                    <option value="Asuransi Lain" {{ isset($dataIbuHamil) && $dataIbuHamil->
                                       jaminan_kesehatan == 'Asuransi Lain' ? 'selected' : '' }}>Asuransi Lain</option>
                                    <option value="Tidak Ada" {{ isset($dataIbuHamil) && $dataIbuHamil->
                                       jaminan_kesehatan == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="no_jaminan_kesehatan">No. Jaminan Kesehatan</label>
                                 <input type="text" class="form-control" id="no_jaminan_kesehatan"
                                    name="no_jaminan_kesehatan"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->no_jaminan_kesehatan : '' }}">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="faskes_tk1">Faskes TK 1</label>
                                 <input type="text" class="form-control" id="faskes_tk1" name="faskes_tk1"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->faskes_tk1 : '' }}">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="faskes_rujukan">Faskes Rujukan</label>
                                 <input type="text" class="form-control" id="faskes_rujukan" name="faskes_rujukan"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->faskes_rujukan : '' }}">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Pribadi -->
               <div class="form-group">
                  <div class="card card-danger">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user mr-2"></i>Data Suami</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-8">
                              <div class="form-group">
                                 <label for="nama_suami">Nama Suami</label>
                                 <input type="text" class="form-control" id="nama_suami" name="nama_suami"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->nama_suami : '' }}">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="nik_suami">NIK Suami</label>
                                 <input type="text" class="form-control" id="nik_suami" name="nik_suami"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->nik_suami : '' }}">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="pendidikan">Pendidikan</label>
                                 <select class="form-control" id="pendidikan" name="pendidikan">
                                    <option value="">- Pilih -</option>
                                    <option value="SD" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'SD' ?
                                       'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'SMP' ?
                                       'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'SMA' ?
                                       'selected' : '' }}>SMA</option>
                                    <option value="D1" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'D1' ?
                                       'selected' : '' }}>D1</option>
                                    <option value="D2" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'D2' ?
                                       'selected' : '' }}>D2</option>
                                    <option value="D3" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'D3' ?
                                       'selected' : '' }}>D3</option>
                                    <option value="D4" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'D4' ?
                                       'selected' : '' }}>D4</option>
                                    <option value="S1" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'S1' ?
                                       'selected' : '' }}>S1</option>
                                    <option value="S2" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'S2' ?
                                       'selected' : '' }}>S2</option>
                                    <option value="S3" {{ isset($dataIbuHamil) && $dataIbuHamil->pendidikan == 'S3' ?
                                       'selected' : '' }}>S3</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="pekerjaan">Pekerjaan</label>
                                 <input type="text" class="form-control" id="pekerjaan" name="pekerjaan"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->pekerjaan : '' }}">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="telp_suami">Telp/HP Suami</label>
                                 <input type="text" class="form-control" id="telp_suami" name="telp_suami"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->telp_suami : '' }}">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Data Alamat -->
               <div class="form-group">
                  <div class="card card-secondary">
                     <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Data Alamat</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                                       <select class="form-control" id="provinsi" name="provinsi" required>
                                          <option value="">- Pilih Provinsi -</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="kabupaten">Kabupaten/Kota <span class="text-danger">*</span></label>
                                       <select class="form-control" id="kabupaten" name="kabupaten" required>
                                          <option value="">- Pilih Kabupaten/Kota -</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-12">
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="kecamatan">Kecamatan <span class="text-danger">*</span></label>
                                       <select class="form-control" id="kecamatan" name="kecamatan" required>
                                          <option value="">- Pilih Kecamatan -</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="desa">Desa/Kelurahan <span class="text-danger">*</span></label>
                                       <select class="form-control" id="desa" name="desa" required>
                                          <option value="">- Pilih Desa/Kelurahan -</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="puskesmas">Puskesmas <span class="text-danger">*</span></label>
                                 <select class="form-control" id="puskesmas" name="puskesmas" required>
                                    <option value="">- Pilih Puskesmas -</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="data_posyandu">Posyandu <span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="data_posyandu" name="data_posyandu"
                                    required value="{{ isset($dataIbuHamil) ? $dataIbuHamil->data_posyandu : '' }}">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="alamat_lengkap">Alamat Lengkap <span class="text-danger">*</span></label>
                                 <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3"
                                    required>{{ isset($dataIbuHamil) ? $dataIbuHamil->alamat_lengkap : '' }}</textarea>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label for="rt">RT</label>
                                 <input type="text" class="form-control" id="rt" name="rt"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->rt : '' }}">
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label for="rw">RW</label>
                                 <input type="text" class="form-control" id="rw" name="rw"
                                    value="{{ isset($dataIbuHamil) ? $dataIbuHamil->rw : '' }}">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="form-group row">
                  <div class="col-sm-12 text-center">
                     <button type="button" class="btn btn-secondary btn-lg mr-2" onclick="window.history.back()">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                     </button>
                     <button type="reset" class="btn btn-warning btn-lg mr-2">
                        <i class="fas fa-redo mr-2"></i>Reset
                     </button>
                     <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Simpan
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Tabel Data Ibu Hamil -->
<div class="row mt-4">
   <div class="col-12">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-table mr-2"></i>Data Ibu Hamil
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="table-responsive">
               <table id="tabelDataIbuHamil" class="table table-bordered table-striped">
                  <thead>
                     <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Usia Kehamilan</th>
                        <th>HPL</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <!-- Data akan diisi melalui AJAX -->
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet"
   href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<style>
   .card-header {
      padding: 0.75rem 1.25rem;
   }

   .card-title {
      margin-bottom: 0;
   }

   /* Memperbaiki tampilan dropdown */
   select.form-control {
      height: calc(2.25rem + 2px);
      padding: 0.375rem 0.75rem;
      font-size: 1rem;
      appearance: auto;
      -webkit-appearance: auto;
      text-align: center;
      line-height: 1.2;
      padding-top: 0.3rem;
      vertical-align: middle;
      display: flex;
      align-items: center;
      justify-content: center;
   }

   select.form-control option {
      text-align: center;
      padding: 10px;
   }

   /* Dropdown sederhana */
   .dropdown-simple {
      max-width: 100px;
      text-align: center;
      font-weight: 500;
      border-radius: 4px;
      background-color: #f8f9fa;
   }

   /* Dropdown status */
   .dropdown-status {
      width: 100%;
      text-align: center;
      font-weight: 500;
      border-radius: 4px;
      background-color: #e8f4ff;
   }

   .dropdown-status option {
      font-weight: normal;
   }

   /* Memperbaiki tampilan Select2 */
   .select2-container--bootstrap4 .select2-selection--single {
      height: calc(2.25rem + 2px) !important;
      text-align: center !important;
      line-height: 1.2 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
   }

   .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
      text-align: center !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
      line-height: 1.8 !important;
      margin-top: -2px !important;
      vertical-align: middle !important;
   }

   /* DataTable style */
   .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 0.25rem 0.5rem;
      margin-left: 2px;
   }

   .dataTables_wrapper .dataTables_length select {
      min-width: 60px;
   }

   .dataTables_wrapper .dataTables_filter input {
      margin-left: 0.5em;
      display: inline-block;
      width: auto;
   }

   /* Responsif untuk layar kecil */
   @media (max-width: 768px) {

      select.form-control,
      input.form-control,
      .select2-container {
         font-size: 16px;
         /* Mencegah zoom di iOS */
      }

      .dropdown-simple {
         max-width: 80px;
      }

      .dropdown-status {
         max-width: 140px;
      }

      .dataTables_wrapper .dataTables_length,
      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_info,
      .dataTables_wrapper .dataTables_paginate {
         float: none;
         text-align: center;
      }

      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_paginate {
         margin-top: 0.5em;
      }
   }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
   $(document).ready(function() {
        // Inisialisasi Select2 hanya untuk dropdown yang kompleks
        $('#provinsi, #kabupaten, #kecamatan, #desa, #puskesmas').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Tambahkan CSRF token untuk semua AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Definisi terjemahan datatable secara lokal
        var indonesianLanguage = {
            "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "infoPostFix": "",
            "thousands": ".",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "loadingRecords": "Sedang memuat...",
            "processing": "Sedang memproses...",
            "search": "Cari:",
            "zeroRecords": "Tidak ditemukan data yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
            "aria": {
                "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
            }
        };

        // Inisialisasi datatable
        var table = $('#tabelDataIbuHamil').DataTable({
            "language": indonesianLanguage,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('anc.data-ibu-hamil.index') }}",
            "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nik', name: 'nik'},
                {data: 'nama', name: 'nama'},
                {data: 'status', name: 'status'},
                {
                    data: null,
                    name: 'usia_kehamilan',
                    render: function(data, type, row) {
                        if (row.hari_pertama_haid) {
                            var hpht = new Date(row.hari_pertama_haid);
                            var today = new Date();
                            var diffTime = Math.abs(today - hpht);
                            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            var weeks = Math.floor(diffDays / 7);
                            var days = diffDays % 7;
                            return weeks + ' minggu ' + days + ' hari';
                        }
                        return '-';
                    }
                },
                {
                    data: 'hari_perkiraan_lahir',
                    name: 'hari_perkiraan_lahir',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                {data: 'alamat_lengkap', name: 'alamat_lengkap'},
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <a href="{{ url('anc/data-ibu-hamil') }}/${row.id_hamil}/edit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="${row.id_hamil}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                },
            ],
            order: [[5, 'asc']], // Urutkan berdasarkan HPL
        });
        
        // Event handler untuk tombol hapus
        $('#tabelDataIbuHamil').on('click', '.btn-hapus', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('anc/data-ibu-hamil') }}/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire(
                                'Berhasil!',
                                'Data berhasil dihapus.',
                                'success'
                            );
                            table.ajax.reload();
                        },
                        error: function(error) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Hitung IMT
        function hitungIMT() {
            let bb = parseFloat($('#berat_badan_sebelum_hamil').val()) || 0;
            let tb = parseFloat($('#tinggi_badan').val()) || 0;
            
            if (bb > 0 && tb > 0) {
                let tbMeter = tb / 100;
                let imt = bb / (tbMeter * tbMeter);
                $('#imt_sebelum_hamil').val(imt.toFixed(2));
                
                // Set status gizi
                let status = '';
                if (imt < 18.5) status = 'Kurus';
                else if (imt >= 18.5 && imt < 25) status = 'Normal';
                else if (imt >= 25 && imt < 30) status = 'Gemuk';
                else status = 'Obesitas';
                
                $('#status_gizi').val(status);
            }
        }

        // Event listener untuk perubahan BB dan TB
        $('#berat_badan_sebelum_hamil, #tinggi_badan').on('change', hitungIMT);

        // Hitung HPL otomatis
        $('#hari_pertama_haid').on('change', function() {
            let hpht = new Date($(this).val());
            if (hpht) {
                let hpl = new Date(hpht);
                hpl.setDate(hpl.getDate() + 280);
                $('#hari_perkiraan_lahir').val(hpl.toISOString().split('T')[0]);
            }
        });

        // Cari data pasien berdasarkan NIK
        $('#btnCariNIK').on('click', function() {
            let nik = $('#nik').val();
            if (!nik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan masukkan NIK terlebih dahulu'
                });
                return;
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Mencari Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Lakukan pencarian dengan AJAX
            $.ajax({
                url: "{{ route('anc.data-ibu-hamil.get-data-pasien', ['nik' => ':nik']) }}".replace(':nik', nik),
                method: 'GET',
                dataType: 'json',
                cache: false,
                success: function(response) {
                    console.log('Response sukses:', response);
                    Swal.close();
                    
                    if (response.status === 'success') {
                        let data = response.data;
                        
                        // Periksa apakah pasien sudah terdaftar sebagai ibu hamil
                        if (data.ibu_hamil_status) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                html: `Pasien ini sudah terdaftar sebagai ibu hamil dengan status <b>${data.ibu_hamil_status.status}</b>.<br>
                                       ID: <b>${data.ibu_hamil_status.id_hamil}</b><br>
                                       Kehamilan ke: <b>${data.ibu_hamil_status.kehamilan_ke}</b><br>
                                       HPL: <b>${data.ibu_hamil_status.hari_perkiraan_lahir ? new Date(data.ibu_hamil_status.hari_perkiraan_lahir).toLocaleDateString('id-ID') : '-'}</b>`,
                                showCancelButton: true,
                                confirmButtonText: 'Tetap Tambahkan',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Isi form dengan data pasien
                                    fillPatientData(data);
                                }
                            });
                        } else {
                            // Langsung isi form jika pasien belum terdaftar
                            fillPatientData(data);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data pasien ditemukan',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error response:', xhr.responseText);
                    Swal.close();
                    
                    let message = 'Terjadi kesalahan saat mencari data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message
                    });
                }
            });
        });

        // Fungsi untuk mengisi data pasien ke form
        function fillPatientData(data) {
            // Isi data pasien
            $('#nama').val(data.nama || '');
            $('#tgl_lahir').val(data.tgl_lahir || '');
            $('#nomor_kk').val(data.nomor_kk || '');
            $('#no_jaminan_kesehatan').val(data.no_jaminan_kesehatan || '');
            $('#alamat_lengkap').val(data.alamat || '');
            $('#no_rkm_medis').val(data.no_rkm_medis || '');
            
            // Set data_posyandu jika ada
            if (data.data_posyandu) {
                $('#data_posyandu').val(data.data_posyandu);
            } else {
                $('#data_posyandu').val('');
            }
            
            // Set status jika ada
            if (data.status) {
                $('#status').val(data.status);
            }
            
            // Set puskesmas default
            $('#puskesmas').val('KERJO');
            
            // Cek dan isi data wilayah jika ada
            if (data.provinsi && data.provinsi.kode) {
                loadProvinceAndDistricts(data);
            }
        }

        // Handle checkbox belum memiliki NIK
        $('#belumMemilikiNIK').on('change', function() {
            if ($(this).is(':checked')) {
                $('#nik').prop('disabled', true);
                $('#btnCariNIK').prop('disabled', true);
            } else {
                $('#nik').prop('disabled', false);
                $('#btnCariNIK').prop('disabled', false);
            }
        });

        // Submit form
        $('#formDataIbuHamil').on('submit', function(e) {
            e.preventDefault();
            
            // Fungsi untuk mengkonversi string ke integer pada dropdown wilayah
            function parseIntIfPossible(value) {
                if (value && !isNaN(value)) {
                    return parseInt(value);
                }
                return value;
            }
            
            // Konversi nilai string ke integer untuk wilayah
            var provinsiVal = parseIntIfPossible($('#provinsi').val());
            var kabupatenVal = parseIntIfPossible($('#kabupaten').val());
            var kecamatanVal = parseIntIfPossible($('#kecamatan').val());
            var desaVal = parseIntIfPossible($('#desa').val());
            
            // Set nilai yang sudah dikonversi
            $('#provinsi').val(provinsiVal);
            $('#kabupaten').val(kabupatenVal);
            $('#kecamatan').val(kecamatanVal);
            $('#desa').val(desaVal);
            
            var formData = $(this).serialize();
            var url = $(this).attr('action');
            var method = $('input[name="_method"]').val() || 'POST';
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim data
            $.ajax({
                url: url,
                type: method === 'PUT' ? 'POST' : method,
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = "{{ route('anc.data-ibu-hamil.index') }}";
                    });
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    
                    var message = 'Terjadi kesalahan saat menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message
                    });
                    
                    console.error("Error response:", xhr.responseJSON);
                }
            });
        });

        // Load data propinsi
        function loadPropinsi() {
            $.ajax({
                url: "{{ url('/propinsi') }}",
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#provinsi').empty().append('<option value="">- Pilih Provinsi -</option>');
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(i, item) {
                            $('#provinsi').append($('<option>', {
                                value: parseInt(item.kd_prop),
                                text: item.nm_prop
                            }));
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading propinsi:', xhr.responseText);
                }
            });
        }

        // Load data kabupaten berdasarkan propinsi
        $('#provinsi').on('change', function() {
            var kdProp = $(this).val();
            if (kdProp) {
                $.ajax({
                    url: "{{ url('/kabupaten') }}?kd_prop=" + kdProp,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#kabupaten').empty().append('<option value="">- Pilih Kabupaten -</option>');
                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(i, item) {
                                $('#kabupaten').append($('<option>', {
                                    value: parseInt(item.kd_kab),
                                    text: item.nm_kab
                                }));
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading kabupaten:', xhr.responseText);
                    }
                });
            } else {
                $('#kabupaten').empty().append('<option value="">- Pilih Kabupaten -</option>');
            }
            
            // Reset kecamatan dan desa
            $('#kecamatan').empty().append('<option value="">- Pilih Kecamatan -</option>');
            $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
        });

        // Load data kecamatan berdasarkan kabupaten
        $('#kabupaten').on('change', function() {
            var kdProp = $('#provinsi').val();
            var kdKab = $(this).val();
            if (kdProp && kdKab) {
                $.ajax({
                    url: "{{ url('/kecamatan') }}?kd_prop=" + kdProp + "&kd_kab=" + kdKab,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#kecamatan').empty().append('<option value="">- Pilih Kecamatan -</option>');
                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(i, item) {
                                $('#kecamatan').append($('<option>', {
                                    value: parseInt(item.kd_kec),
                                    text: item.nm_kec
                                }));
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading kecamatan:', xhr.responseText);
                    }
                });
            } else {
                $('#kecamatan').empty().append('<option value="">- Pilih Kecamatan -</option>');
            }
            
            // Reset desa
            $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
        });

        // Load data desa berdasarkan kecamatan
        $('#kecamatan').on('change', function() {
            var kdProp = $('#provinsi').val();
            var kdKab = $('#kabupaten').val();
            var kdKec = $(this).val();
            if (kdProp && kdKab && kdKec) {
                $.ajax({
                    url: "{{ url('/kelurahan') }}?kd_prop=" + kdProp + "&kd_kab=" + kdKab + "&kd_kec=" + kdKec,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(i, item) {
                                $('#desa').append($('<option>', {
                                    value: parseInt(item.kd_kel),
                                    text: item.nm_kel
                                }));
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading desa:', xhr.responseText);
                    }
                });
            } else {
                $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
            }
        });

        // Load puskesmas
        $('#puskesmas').append($('<option>', {
            value: 'KERJO',
            text: 'PUSKESMAS KERJO'
        }));

        // Call function when document is ready
        $(document).ready(function() {
            // Load initial data
            loadPropinsi();
            
            // Jika mode edit, load data alamat
            @if(isset($dataIbuHamil))
                setTimeout(function() {
                    // Set puskesmas
                    $('#puskesmas').val('{{ $dataIbuHamil->puskesmas }}');
                    
                    // Isi provinsi dengan AJAX
                    $.ajax({
                        url: "{{ url('/propinsi') }}",
                        method: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            $('#provinsi').empty().append('<option value="">- Pilih Provinsi -</option>');
                            if (res.data && res.data.length > 0) {
                                $.each(res.data, function(i, item) {
                                    $('#provinsi').append($('<option>', {
                                        value: parseInt(item.kd_prop),
                                        text: item.nm_prop
                                    }));
                                });
                                
                                // Set selected and trigger change (pastikan sebagai integer)
                                $('#provinsi').val(parseInt('{{ $dataIbuHamil->provinsi }}')).trigger('change');
                                
                                // Kabupaten load
                                $.ajax({
                                    url: "{{ url('/kabupaten') }}?kd_prop={{ $dataIbuHamil->provinsi }}",
                                    method: 'GET',
                                    dataType: 'json',
                                    success: function(res) {
                                        $('#kabupaten').empty().append('<option value="">- Pilih Kabupaten -</option>');
                                        if (res.data && res.data.length > 0) {
                                            $.each(res.data, function(i, item) {
                                                $('#kabupaten').append($('<option>', {
                                                    value: parseInt(item.kd_kab),
                                                    text: item.nm_kab
                                                }));
                                            });
                                            
                                            // Set selected and trigger change (pastikan sebagai integer)
                                            $('#kabupaten').val(parseInt('{{ $dataIbuHamil->kabupaten }}')).trigger('change');
                                            
                                            // Kecamatan load
                                            $.ajax({
                                                url: "{{ url('/kecamatan') }}?kd_prop={{ $dataIbuHamil->provinsi }}&kd_kab={{ $dataIbuHamil->kabupaten }}",
                                                method: 'GET',
                                                dataType: 'json',
                                                success: function(res) {
                                                    $('#kecamatan').empty().append('<option value="">- Pilih Kecamatan -</option>');
                                                    if (res.data && res.data.length > 0) {
                                                        $.each(res.data, function(i, item) {
                                                            $('#kecamatan').append($('<option>', {
                                                                value: parseInt(item.kd_kec),
                                                                text: item.nm_kec
                                                            }));
                                                        });
                                                        
                                                        // Set selected and trigger change (pastikan sebagai integer)
                                                        $('#kecamatan').val(parseInt('{{ $dataIbuHamil->kecamatan }}')).trigger('change');
                                                        
                                                        // Desa/Kelurahan load
                                                        $.ajax({
                                                            url: "{{ url('/kelurahan') }}?kd_prop={{ $dataIbuHamil->provinsi }}&kd_kab={{ $dataIbuHamil->kabupaten }}&kd_kec={{ $dataIbuHamil->kecamatan }}",
                                                            method: 'GET',
                                                            dataType: 'json',
                                                            success: function(res) {
                                                                $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
                                                                if (res.data && res.data.length > 0) {
                                                                    $.each(res.data, function(i, item) {
                                                                        $('#desa').append($('<option>', {
                                                                            value: parseInt(item.kd_kel),
                                                                            text: item.nm_kel
                                                                        }));
                                                                    });
                                                                    
                                                                    // Set selected (pastikan sebagai integer)
                                                                    $('#desa').val(parseInt('{{ $dataIbuHamil->desa }}'));

                                                                    // Set posyandu jika ada
                                                                    if ('{{ $dataIbuHamil->data_posyandu }}') {
                                                                        $('#data_posyandu').val('{{ $dataIbuHamil->data_posyandu }}');
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    });
                }, 500);
            @else
                // Reset form on page load if not in edit mode
                $('#formDataIbuHamil')[0].reset();
            @endif
        });

        // Fungsi untuk memuat data wilayah berdasarkan data pasien
        function loadProvinceAndDistricts(data) {
            // Isi provinsi dengan AJAX
            $.ajax({
                url: "{{ url('/propinsi') }}",
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#provinsi').empty().append('<option value="">- Pilih Provinsi -</option>');
                    if (res.data && res.data.length > 0) {
                        $.each(res.data, function(i, item) {
                            $('#provinsi').append($('<option>', {
                                value: parseInt(item.kd_prop),
                                text: item.nm_prop
                            }));
                        });
                        
                        // Set selected and trigger change
                        $('#provinsi').val(data.provinsi.kode).trigger('change');
                        
                        // Kabupaten load
                        if (data.kabupaten && data.kabupaten.kode) {
                            $.ajax({
                                url: "{{ url('/kabupaten') }}?kd_prop=" + data.provinsi.kode,
                                method: 'GET',
                                dataType: 'json',
                                success: function(res) {
                                    $('#kabupaten').empty().append('<option value="">- Pilih Kabupaten -</option>');
                                    if (res.data && res.data.length > 0) {
                                        $.each(res.data, function(i, item) {
                                            $('#kabupaten').append($('<option>', {
                                                value: parseInt(item.kd_kab),
                                                text: item.nm_kab
                                            }));
                                        });
                                        
                                        // Set selected and trigger change
                                        $('#kabupaten').val(data.kabupaten.kode).trigger('change');
                                        
                                        // Kecamatan load
                                        if (data.kecamatan && data.kecamatan.kode) {
                                            $.ajax({
                                                url: "{{ url('/kecamatan') }}?kd_prop=" + data.provinsi.kode + "&kd_kab=" + data.kabupaten.kode,
                                                method: 'GET',
                                                dataType: 'json',
                                                success: function(res) {
                                                    $('#kecamatan').empty().append('<option value="">- Pilih Kecamatan -</option>');
                                                    if (res.data && res.data.length > 0) {
                                                        $.each(res.data, function(i, item) {
                                                            $('#kecamatan').append($('<option>', {
                                                                value: parseInt(item.kd_kec),
                                                                text: item.nm_kec
                                                            }));
                                                        });
                                                        
                                                        // Set selected and trigger change
                                                        $('#kecamatan').val(data.kecamatan.kode).trigger('change');
                                                        
                                                        // Desa/Kelurahan load
                                                        if (data.desa && data.desa.kode) {
                                                            $.ajax({
                                                                url: "{{ url('/kelurahan') }}?kd_prop=" + data.provinsi.kode + "&kd_kab=" + data.kabupaten.kode + "&kd_kec=" + data.kecamatan.kode,
                                                                method: 'GET',
                                                                dataType: 'json',
                                                                success: function(res) {
                                                                    $('#desa').empty().append('<option value="">- Pilih Desa/Kelurahan -</option>');
                                                                    if (res.data && res.data.length > 0) {
                                                                        $.each(res.data, function(i, item) {
                                                                            $('#desa').append($('<option>', {
                                                                                value: parseInt(item.kd_kel),
                                                                                text: item.nm_kel
                                                                            }));
                                                                        });
                                                                        
                                                                        // Set selected
                                                                        $('#desa').val(data.desa.kode);
                                                                    }
                                                                },
                                                                error: function(xhr, status, error) {
                                                                    console.error('Error saat mengambil data kelurahan:', error);
                                                                    console.error('Response:', xhr.responseText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error('Error saat mengambil data kecamatan:', error);
                                                    console.error('Response:', xhr.responseText);
                                                }
                                            });
                                        }
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error saat mengambil data kabupaten:', error);
                                    console.error('Response:', xhr.responseText);
                                }
                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data provinsi:', error);
                    console.error('Response:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengambil Data Provinsi',
                        text: 'Terjadi kesalahan saat mengambil data provinsi. Silakan coba lagi nanti.'
                    });
                }
            });
        }
    });
</script>
@stop