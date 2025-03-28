@extends('adminlte::page')

@section('title', 'Detail Data Ibu Hamil')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-female mr-2"></i>Detail Data Ibu Hamil</h1>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('anc.data-ibu-hamil.index') }}">Data Ibu Hamil</a></li>
      <li class="breadcrumb-item active">Detail Data Ibu Hamil</li>
   </ol>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-12">
      <!-- Informasi Umum Card -->
      <div class="card card-primary card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-info-circle mr-2"></i>Informasi Umum
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">ID Hamil</th>
                        <td width="65%">: {{ $dataIbuHamil->id_hamil }}</td>
                     </tr>
                     <tr>
                        <th>NIK</th>
                        <td>: {{ $dataIbuHamil->nik ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Nomor Rekam Medis</th>
                        <td>: {{ $dataIbuHamil->no_rkm_medis }}</td>
                     </tr>
                     <tr>
                        <th>Nomor KK</th>
                        <td>: {{ $dataIbuHamil->nomor_kk ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Nama</th>
                        <td>: {{ $dataIbuHamil->nama }}</td>
                     </tr>
                     <tr>
                        <th>Tanggal Lahir</th>
                        <td>: {{ $dataIbuHamil->tgl_lahir ? $dataIbuHamil->tgl_lahir->format('d-m-Y') : '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Kehamilan ke</th>
                        <td width="65%">: {{ $dataIbuHamil->kehamilan_ke }}</td>
                     </tr>
                     <tr>
                        <th>Status</th>
                        <td>: <span
                              class="badge {{ $dataIbuHamil->status == 'Hamil' ? 'bg-primary' : ($dataIbuHamil->status == 'Melahirkan' ? 'bg-success' : 'bg-danger') }}">{{
                              $dataIbuHamil->status }}</span></td>
                     </tr>
                     <tr>
                        <th>HPHT</th>
                        <td>: {{ $dataIbuHamil->hari_pertama_haid ? $dataIbuHamil->hari_pertama_haid->format('d-m-Y') :
                           '-' }}</td>
                     </tr>
                     <tr>
                        <th>HPL</th>
                        <td>: {{ $dataIbuHamil->hari_perkiraan_lahir ?
                           $dataIbuHamil->hari_perkiraan_lahir->format('d-m-Y') : '-' }}</td>
                     </tr>
                     <tr>
                        <th>Usia Kehamilan</th>
                        <td>:
                           @php
                           if ($dataIbuHamil->hari_perkiraan_lahir) {
                           $hpl = \Carbon\Carbon::parse($dataIbuHamil->hari_perkiraan_lahir);
                           $today = \Carbon\Carbon::now();

                           if ($today->lt($hpl)) {
                           $diffInDays = $today->diffInDays($hpl);
                           $weeks = floor((280 - $diffInDays) / 7);
                           $days = (280 - $diffInDays) % 7;
                           echo $weeks . ' minggu ' . $days . ' hari';
                           } else {
                           echo 'Sudah lewat HPL';
                           }
                           } else {
                           echo '-';
                           }
                           @endphp
                        </td>
                     </tr>
                     <tr>
                        <th>Waktu Pendaftaran</th>
                        <td>: {{ $dataIbuHamil->created_at ? $dataIbuHamil->created_at->format('d-m-Y H:i:s') : '-' }}
                        </td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Data Kesehatan Card -->
      <div class="card card-success card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-heartbeat mr-2"></i>Data Kesehatan
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="45%">Berat Badan Sebelum Hamil</th>
                        <td width="55%">: {{ $dataIbuHamil->berat_badan_sebelum_hamil ?? '-' }} kg</td>
                     </tr>
                     <tr>
                        <th>Tinggi Badan</th>
                        <td>: {{ $dataIbuHamil->tinggi_badan ?? '-' }} cm</td>
                     </tr>
                     <tr>
                        <th>LILA</th>
                        <td>: {{ $dataIbuHamil->lila ?? '-' }} cm</td>
                     </tr>
                     <tr>
                        <th>IMT Sebelum Hamil</th>
                        <td>: {{ $dataIbuHamil->imt_sebelum_hamil ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Status Gizi</th>
                        <td>: {{ $dataIbuHamil->status_gizi ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Jumlah Janin</th>
                        <td>: {{ $dataIbuHamil->jumlah_janin ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Golongan Darah</th>
                        <td width="65%">: {{ $dataIbuHamil->golongan_darah ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Rhesus</th>
                        <td>: {{ $dataIbuHamil->rhesus ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Jarak Kehamilan</th>
                        <td>:
                           @if($dataIbuHamil->jarak_kehamilan_tahun || $dataIbuHamil->jarak_kehamilan_bulan)
                           {{ $dataIbuHamil->jarak_kehamilan_tahun ? $dataIbuHamil->jarak_kehamilan_tahun . ' tahun ' :
                           '' }}
                           {{ $dataIbuHamil->jarak_kehamilan_bulan ? $dataIbuHamil->jarak_kehamilan_bulan . ' bulan' :
                           '' }}
                           @else
                           -
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <th>Riwayat Penyakit</th>
                        <td>: {{ $dataIbuHamil->riwayat_penyakit ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Riwayat Alergi</th>
                        <td>: {{ $dataIbuHamil->riwayat_alergi ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Usia Ibu</th>
                        <td>: {{ $dataIbuHamil->usia_ibu ?? '-' }} tahun</td>
                     </tr>
                     <tr>
                        <th>Jumlah Anak Lahir Hidup</th>
                        <td>: {{ $dataIbuHamil->jumlah_anak_hidup ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Riwayat Keguguran</th>
                        <td>: {{ $dataIbuHamil->riwayat_keguguran ?? '0' }}</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Data Administrasi Card -->
      <div class="card card-warning card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-file-alt mr-2"></i>Data Administrasi
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="40%">Kepemilikan Buku KIA</th>
                        <td width="60%">: {{ $dataIbuHamil->kepemilikan_buku_kia ? 'Ya' : 'Tidak' }}</td>
                     </tr>
                     <tr>
                        <th>Jaminan Kesehatan</th>
                        <td>: {{ $dataIbuHamil->jaminan_kesehatan ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>No. Jaminan Kesehatan</th>
                        <td>: {{ $dataIbuHamil->no_jaminan_kesehatan ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Faskes TK 1</th>
                        <td width="65%">: {{ $dataIbuHamil->faskes_tk1 ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Faskes Rujukan</th>
                        <td>: {{ $dataIbuHamil->faskes_rujukan ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Data Suami Card -->
      <div class="card card-danger card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-user mr-2"></i>Data Suami
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Nama Suami</th>
                        <td width="65%">: {{ $dataIbuHamil->nama_suami ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>NIK Suami</th>
                        <td>: {{ $dataIbuHamil->nik_suami ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Pendidikan</th>
                        <td width="65%">: {{ $dataIbuHamil->pendidikan ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Pekerjaan</th>
                        <td>: {{ $dataIbuHamil->pekerjaan ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Telp/HP Suami</th>
                        <td>: {{ $dataIbuHamil->telp_suami ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <!-- Data Alamat Card -->
      <div class="card card-secondary card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-map-marker-alt mr-2"></i>Data Alamat
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Provinsi</th>
                        <td width="65%">:
                           @php
                           // Mengambil data provinsi dari file assets/propinsi.iyem
                           $provinsiNama = '-';
                           $path = public_path('assets/propinsi.iyem');
                           if (file_exists($path)) {
                           $content = file_get_contents($path);
                           $propinsiList = json_decode($content, true);

                           if (isset($propinsiList['propinsi'])) {
                           foreach ($propinsiList['propinsi'] as $prov) {
                           if ($prov['id'] == $dataIbuHamil->provinsi) {
                           $provinsiNama = $prov['nama'];
                           break;
                           }
                           }
                           }
                           }
                           echo $provinsiNama;
                           @endphp
                        </td>
                     </tr>
                     <tr>
                        <th>Kabupaten</th>
                        <td>:
                           @php
                           // Mengambil data kabupaten dari file assets/kabupaten.iyem
                           $kabupatenNama = '-';
                           $path = public_path('assets/kabupaten.iyem');
                           if (file_exists($path)) {
                           $content = file_get_contents($path);
                           $kabupatenList = json_decode($content, true);

                           if (isset($kabupatenList['kabupaten'])) {
                           foreach ($kabupatenList['kabupaten'] as $kab) {
                           if ($kab['id'] == $dataIbuHamil->kabupaten && $kab['id_propinsi'] == $dataIbuHamil->provinsi)
                           {
                           $kabupatenNama = $kab['nama'];
                           break;
                           }
                           }
                           }
                           }
                           echo $kabupatenNama;
                           @endphp
                        </td>
                     </tr>
                     <tr>
                        <th>Kecamatan</th>
                        <td>:
                           @php
                           // Mengambil data kecamatan dari file assets/kecamatan.iyem
                           $kecamatanNama = '-';
                           $path = public_path('assets/kecamatan.iyem');
                           if (file_exists($path)) {
                           $content = file_get_contents($path);
                           $kecamatanList = json_decode($content, true);

                           if (isset($kecamatanList['kecamatan'])) {
                           foreach ($kecamatanList['kecamatan'] as $kec) {
                           if ($kec['id'] == $dataIbuHamil->kecamatan && $kec['id_kabupaten'] ==
                           $dataIbuHamil->kabupaten) {
                           $kecamatanNama = $kec['nama'];
                           break;
                           }
                           }
                           }
                           }
                           echo $kecamatanNama;
                           @endphp
                        </td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-borderless">
                     <tr>
                        <th width="35%">Desa/Kelurahan</th>
                        <td width="65%">:
                           @php
                           // Mengambil data kelurahan dari file assets/kelurahan.iyem
                           $kelurahanNama = '-';
                           $path = public_path('assets/kelurahan.iyem');
                           if (file_exists($path)) {
                           $content = file_get_contents($path);
                           $kelurahanList = json_decode($content, true);

                           if (isset($kelurahanList['kelurahan'])) {
                           foreach ($kelurahanList['kelurahan'] as $kel) {
                           if ($kel['id'] == $dataIbuHamil->desa && $kel['id_kecamatan'] == $dataIbuHamil->kecamatan) {
                           $kelurahanNama = $kel['nama'];
                           break;
                           }
                           }
                           }
                           }
                           echo $kelurahanNama != '-' ? $kelurahanNama : $dataIbuHamil->desa;
                           @endphp
                        </td>
                     </tr>
                     <tr>
                        <th>Puskesmas</th>
                        <td>: {{ $dataIbuHamil->puskesmas ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Posyandu</th>
                        <td>: {{ $dataIbuHamil->data_posyandu ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Alamat</th>
                        <td>: {{ $dataIbuHamil->alamat_lengkap ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>RT/RW</th>
                        <td>: {{ $dataIbuHamil->rt ?? '-' }}/{{ $dataIbuHamil->rw ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <div class="form-group row">
         <div class="col-sm-12 text-center">
            <a href="{{ route('anc.data-ibu-hamil.index') }}" class="btn btn-secondary btn-lg mr-2">
               <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('anc.data-ibu-hamil.edit', $dataIbuHamil->id_hamil) }}" class="btn btn-primary btn-lg">
               <i class="fas fa-edit mr-2"></i>Edit Data
            </a>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<style>
   .card-header {
      padding: 0.75rem 1.25rem;
   }

   .card-title {
      margin-bottom: 0;
   }

   .table-borderless th,
   .table-borderless td {
      padding: 0.5rem;
   }

   .badge {
      font-size: 90%;
      padding: 0.4em 0.7em;
   }
</style>
@stop