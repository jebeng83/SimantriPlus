@extends('adminlte::page')

@section('title', 'Analisa CKG Sekolah')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Analisa CKG Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item active" aria-current="page">Analisa CKG Sekolah</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <p class="text-muted m-0"><i class="fas fa-calendar-day mr-1"></i> {{ date('d F Y') }}</p>
   </div>
</div>
@stop

@section('content')
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h6 class="text-primary mb-0 font-weight-bold"><i class="fas fa-filter mr-2"></i>Filter</h6>
               <div>
                  <a href="{{ route('ilp.dashboard-sekolah') }}" class="btn btn-sm btn-outline-secondary mr-2">
                     <i class="fas fa-chart-bar mr-1"></i> Dashboard Sekolah
                  </a>
                  <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-sm btn-outline-primary mr-2">
                     <i class="fas fa-table mr-1"></i> Data Siswa
                  </a>
                  <button type="button" class="btn btn-sm btn-success mr-2" id="exportExcelCkg">
                     <i class="fas fa-file-excel mr-1"></i> Export Excel
                  </button>
                  <a href="{{ route('ilp.analisa-ckg-sekolah') }}" class="btn btn-sm btn-outline-secondary">
                     <i class="fas fa-sync mr-1"></i> Reset
                  </a>
               </div>
            </div>
            <form method="GET" action="{{ route('ilp.analisa-ckg-sekolah') }}">
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-school mr-1"></i> Sekolah</label>
                        <select class="form-control" name="sekolah">
                           <option value="">Semua Sekolah</option>
                           @foreach($daftarSekolah as $sekolah)
                           <option value="{{ $sekolah->id_sekolah }}" {{ $sekolahId == $sekolah->id_sekolah ? 'selected' : '' }}>
                              {{ $sekolah->nama_sekolah }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-graduation-cap mr-1"></i> Jenis Sekolah</label>
                        <select class="form-control" name="jenis_sekolah">
                           <option value="">Semua Jenis</option>
                           @foreach($daftarJenisSekolah as $jenis)
                           <option value="{{ $jenis->id }}" {{ $jenisSekolahId == $jenis->id ? 'selected' : '' }}>
                              {{ $jenis->nama }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-chalkboard mr-1"></i> Kelas</label>
                        <select class="form-control" name="kelas">
                           <option value="">Semua Kelas</option>
                           @foreach($daftarKelas as $kelas)
                           <option value="{{ $kelas->id_kelas }}" {{ $kelasId == $kelas->id_kelas ? 'selected' : '' }}>
                              {{ $kelas->kelas }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-calendar mr-1"></i> Periode</label>
                        <div class="d-flex align-items-center">
                           <input type="date" class="form-control mr-2" name="tanggal_awal" value="{{ $tanggalAwal ?: (date('Y').'-01-01') }}">
                           <input type="date" class="form-control" name="tanggal_akhir" value="{{ $tanggalAkhir ?: (date('Y').'-12-31') }}">
                        </div>
                     </div>
                  </div>
               </div>
               
               <div class="row mt-3">
                  <div class="col-md-12 text-right">
                     <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> Filter
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-info text-white">
            <h3 class="card-title m-0">Trend Kunjungan per Sekolah</h3>
         </div>
         <div class="card-body">
            <div id="chartTrendSekolahTop"></div>
         </div>
      </div>
   </div>
</div>


<div class="row mb-4">
   <div class="col-md-6">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title m-0">Statistik Per Sekolah</h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-striped table-hover mb-0">
                  <thead class="bg-light">
                     <tr>
                        <th>No</th>
                        <th>Sekolah</th>
                        <th>Total</th>
                        <th>Normal</th>
                        <th>Perlu</th>
                        <th>Rujuk</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($statistikSekolah as $i => $row)
                     <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->nama_sekolah }}</td>
                        <td>{{ number_format($row->total) }}</td>
                        <td><span class="badge badge-success">{{ number_format($row->normal) }}</span></td>
                        <td><span class="badge badge-warning">{{ number_format($row->perlu) }}</span></td>
                        <td><span class="badge badge-danger">{{ number_format($row->rujuk) }}</span></td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data</td>
                     </tr>
                     @endforelse
                 </tbody>
              </table>
           </div>
            <div class="table-responsive border-top">
               @php
                  $kat = data_get($ringkasanPemeriksaan,'antropometri.kategori_imt_dewasa',[]);
                  $under = (int)($kat['underweight'] ?? 0);
                  $ideal = (int)($kat['normal'] ?? 0);
                  $over = (int)($kat['overweight'] ?? 0);
                  $obese = (int)($kat['obesitas'] ?? 0);
                  $totalDistinct = (int)($distinctSiswa ?? 0);
                  $pct = function($n,$t){ return $t > 0 ? number_format(($n / $t) * 100, 2) : '0.00'; };
               @endphp
               <table class="table table-sm table-striped mb-0">
                  <thead>
                     <tr>
                        <th>Kategori IMT (Dewasa)</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">% dari Total Siswa</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Underweight (≤ 18,49)</td>
                        <td class="text-right">{{ number_format($under) }}</td>
                        <td class="text-right">{{ $pct($under,$totalDistinct) }}%</td>
                     </tr>
                     <tr>
                        <td>Normal (18,5–24,9)</td>
                        <td class="text-right">{{ number_format($ideal) }}</td>
                        <td class="text-right">{{ $pct($ideal,$totalDistinct) }}%</td>
                     </tr>
                     <tr>
                        <td>Overweight (> 25–27)</td>
                        <td class="text-right">{{ number_format($over) }}</td>
                        <td class="text-right">{{ $pct($over,$totalDistinct) }}%</td>
                     </tr>
                     <tr>
                        <td>Obesitas (> 27)</td>
                        <td class="text-right">{{ number_format($obese) }}</td>
                        <td class="text-right">{{ $pct($obese,$totalDistinct) }}%</td>
                     </tr>
                  </tbody>
               </table>
            </div>
        </div>
      </div>
    </div>
   <div class="col-md-6">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-info text-white">
            <h3 class="card-title m-0">Statistik Per Kelas</h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-striped table-hover mb-0">
                  <thead class="bg-light">
                     <tr>
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Total</th>
                        <th>Normal</th>
                        <th>Perlu</th>
                        <th>Rujuk</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($statistikKelas as $i => $row)
                     <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->kelas }}</td>
                        <td>{{ number_format($row->total) }}</td>
                        <td><span class="badge badge-success">{{ number_format($row->normal) }}</span></td>
                        <td><span class="badge badge-warning">{{ number_format($row->perlu) }}</span></td>
                        <td><span class="badge badge-danger">{{ number_format($row->rujuk) }}</span></td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

@php
   $enumAll = data_get($ringkasanPemeriksaan,'enum_distribusi',[]);
@endphp
@if(!empty($enumAll))
<div class="row mb-4">
   @foreach($enumAll as $field => $dist)
      @if(!empty($dist) && is_array($dist))
      <div class="col-md-4">
         <div class="card shadow-sm">
            <div class="card-header bg-gradient-secondary text-white">
               <h3 class="card-title m-0">{{ ucwords(str_replace('_',' ', $field)) }}</h3>
            </div>
            <div class="card-body p-0">
               <div class="table-responsive">
                  <table class="table table-sm table-striped mb-0">
                     <thead>
                        <tr>
                           <th>Kategori</th>
                           <th class="text-right">Jumlah</th>
                           <th class="text-right">% dari Total Siswa</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($dist as $label => $val)
                        <tr>
                           <td>{{ $label }}</td>
                           <td class="text-right">{{ number_format((int)$val) }}</td>
                           <td class="text-right">{{ $pct((int)$val,$totalDistinct) }}%</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      @endif
   @endforeach
</div>
@endif

<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-success text-white">
            <h3 class="card-title m-0">Ringkasan Antropometri</h3>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-4">
                  <div class="small-box bg-light">
                     <div class="inner">
                        <h3>{{ number_format(optional($antropometri)->bb_avg ?? 0, 2) }}</h3>
                        <p>Rata-rata Berat Badan</p>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="small-box bg-light">
                     <div class="inner">
                        <h3>{{ number_format(optional($antropometri)->tb_avg ?? 0, 2) }}</h3>
                        <p>Rata-rata Tinggi Badan</p>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="small-box bg-light">
                     <div class="inner">
                        <h3>{{ number_format(optional($antropometri)->imt_avg ?? 0, 2) }}</h3>
                        <p>Rata-rata IMT</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

@if(!empty($ringkasanPemeriksaan))
<div class="row mb-4">
   <div class="col-md-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-info text-white">
            <h3 class="card-title m-0">Pemeriksaan Lab</h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-sm table-striped mb-0">
                  <thead>
                     <tr>
                        <th>Indikator</th>
                        <th class="text-right">Normal</th>
                        <th class="text-right">Tidak</th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Gula Darah Sewaktu (≤150)</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_gds.normal',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_gds.tidak',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_gds.normal',0) + data_get($ringkasanPemeriksaan,'lab.hasil_gds.tidak',0)) }}</td>
                     </tr>
                     <tr>
                        <td>Hemoglobin (12–16)</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_hb.normal',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_hb.tidak',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'lab.hasil_hb.normal',0) + data_get($ringkasanPemeriksaan,'lab.hasil_hb.tidak',0)) }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-success text-white">
            <h3 class="card-title m-0">Antropometri</h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-sm table-striped mb-0">
                  <thead>
                     <tr>
                        <th>Indikator</th>
                        <th class="text-right">Normal</th>
                        <th class="text-right">Tidak</th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>IMT (18.5–24.9)</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.imt.normal',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.imt.tidak',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.imt.normal',0) + data_get($ringkasanPemeriksaan,'antropometri.imt.tidak',0)) }}</td>
                     </tr>
                     <tr>
                        <td>Status Gizi = Normal</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.status_gizi.normal',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.status_gizi.tidak',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'antropometri.status_gizi.normal',0) + data_get($ringkasanPemeriksaan,'antropometri.status_gizi.tidak',0)) }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <div class="col-md-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-warning text-white">
            <h3 class="card-title m-0">Tekanan Darah</h3>
         </div>
         <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-sm table-striped mb-0">
                  <thead>
                     <tr>
                        <th>Indikator</th>
                        <th class="text-right">Normal</th>
                        <th class="text-right">Tidak</th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Sistole < 120 dan Diastole < 80</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'tekanan_darah.bp.normal',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'tekanan_darah.bp.tidak',0)) }}</td>
                        <td class="text-right">{{ number_format(data_get($ringkasanPemeriksaan,'tekanan_darah.bp.normal',0) + data_get($ringkasanPemeriksaan,'tekanan_darah.bp.tidak',0)) }}</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endif

@if(!empty($agregasi))
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-secondary text-white">
            <h3 class="card-title m-0">Agregasi Skrining</h3>
         </div>
         <div class="card-body">
            <div class="row">
               @if(!empty($persenResikoKategori))
               <div class="col-md-6">
                  <div class="card mb-3">
                     <div class="card-header bg-light">
                        <strong>Persentase Risiko per Kategori</strong>
                     </div>
                     <div class="card-body p-0">
                        <div class="table-responsive">
                           <table class="table table-sm table-striped mb-0">
                              <thead>
                                 <tr>
                                    <th>Kategori</th>
                                    <th class="text-right">% Risiko</th>
                                    <th class="text-right">Jumlah Risiko</th>
                                    <th class="text-right">Total Siswa</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                    $judulKategori = [
                                       'gula_darah' => 'Gula Darah',
                                       'gejala_cemas' => 'Gejala Cemas',
                                       'gejala_depresi' => 'Gejala Depresi',
                                       'malaria' => 'Malaria',
                                       'tropis_terabaikan' => 'Penyakit Tropis Terabaikan',
                                       'riwayat_imunisasi' => 'Riwayat Imunisasi',
                                       'resiko_hepatitis' => 'Faktor Resiko Hepatitis',
                                       'resiko_tbc' => 'Faktor Resiko TBC',
                                       'antropometri_fields' => 'Antropometri (Isian)',
                                       'tekanan_darah' => 'Tekanan Darah (Isian)',
                                       'mata_telinga' => 'Skrining Mata & Telinga',
                                       'merokok' => 'Perilaku Merokok Anak Sekolah',
                                       'reproduksi_putri' => 'Kesehatan Reproduksi Putri',
                                       'reproduksi_putra' => 'Kesehatan Reproduksi Putra',
                                       'kelayakan_kebugaran' => 'Kelayakan Kebugaran',
                                       'aktivitas_fisik' => 'Aktivitas Fisik Anak',
                                       'resiko_hepa_smp_sma' => 'Faktor Resiko Hepatitis SMP/SMA',
                                       'resiko_talasemia' => 'Faktor Resiko Thalasemia',
                                       'pemeriksaan_lab' => 'Pemeriksaan Lab',
                                       'riwayat_hpv' => 'Riwayat HPV Kelas 9 Putri',
                                       'resiko_mental_health' => 'Faktor Resiko Mental Health'
                                    ];
                                 @endphp
                                 @foreach($persenResikoKategori as $kategori => $val)
                                 <tr>
                                    <td>{{ $judulKategori[$kategori] ?? ucfirst(str_replace('_',' ',$kategori)) }}</td>
                                    <td class="text-right">{{ number_format($val['percent'] ?? 0, 2) }}%</td>
                                    <td class="text-right">{{ number_format($val['riskCount'] ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($val['totalDistinct'] ?? ($distinctSiswa ?? 0)) }}</td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="card mb-3">
                     <div class="card-header bg-light">
                        <strong>Persentase Risiko per Kategori</strong>
                     </div>
                     <div class="card-body">
                        <div id="chartRiskPctTop"></div>
                     </div>
                  </div>
               </div>
               @endif
            </div>
            @php
               $judulKategori = [
                  'gula_darah' => 'Gula Darah',
                  'gejala_cemas' => 'Gejala Cemas',
                  'gejala_depresi' => 'Gejala Depresi',
                  'malaria' => 'Malaria',
                  'tropis_terabaikan' => 'Penyakit Tropis Terabaikan',
                  'riwayat_imunisasi' => 'Riwayat Imunisasi',
                  'resiko_hepatitis' => 'Faktor Resiko Hepatitis',
                  'resiko_tbc' => 'Faktor Resiko TBC',
                  'antropometri_fields' => 'Antropometri (Isian)',
                  'tekanan_darah' => 'Tekanan Darah (Isian)',
                  'mata_telinga' => 'Skrining Mata & Telinga',
                  'merokok' => 'Perilaku Merokok Anak Sekolah',
                  'reproduksi_putri' => 'Kesehatan Reproduksi Putri',
                  'reproduksi_putra' => 'Kesehatan Reproduksi Putra',
                  'kelayakan_kebugaran' => 'Kelayakan Kebugaran',
                  'aktivitas_fisik' => 'Aktivitas Fisik Anak',
                  'resiko_hepa_smp_sma' => 'Faktor Resiko Hepatitis SMP/SMA',
                  'resiko_talasemia' => 'Faktor Resiko Thalasemia',
                  'pemeriksaan_lab' => 'Pemeriksaan Lab',
                  'riwayat_hpv' => 'Riwayat HPV Kelas 9 Putri',
                  'resiko_mental_health' => 'Faktor Resiko Mental Health'
               ];
            @endphp
            <div class="row">
               @foreach($agregasi as $kategori => $items)
               <div class="col-md-6 mb-3">
                  <div class="card">
                     <div class="card-header bg-light">
                        <strong>{{ $judulKategori[$kategori] ?? ucfirst(str_replace('_',' ',$kategori)) }}</strong>
                     </div>
                     <div class="card-body p-0">
                        <div class="table-responsive">
                           <table class="table table-sm table-striped mb-0">
                              <thead>
                                 <tr>
                                    <th>Indikator</th>
                                    <th class="text-right">Ya</th>
                                    <th class="text-right">Tidak</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">% Ya</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @foreach($items as $row)
                                 @php
                                    $yes = (int)($row['yes'] ?? 0);
                                    $no = (int)($row['no'] ?? 0);
                                    $total = $yes + $no;
                                    if ($total === 0) { $total = (int)($row['count'] ?? 0); }
                                    $pct = $total > 0 ? round(($yes / $total) * 100, 2) : 0;
                                 @endphp
                                 <tr>
                                    <td>{{ str_replace('_',' ', $row['name']) }}</td>
                                    <td class="text-right">{{ number_format($yes) }}</td>
                                    <td class="text-right">{{ number_format($no) }}</td>
                                    <td class="text-right">{{ number_format($total) }}</td>
                                    <td class="text-right">{{ number_format($pct, 2) }}%</td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
</div>
@endif

<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title m-0">Grafik Analisa Kesehatan</h3>
         </div>
         <div class="card-body">
            <div class="row" id="chartsContainer"></div>
         </div>
      </div>
   </div>
</div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('exportExcelCkg');
  if (!btn) return;
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    var sekolah = document.querySelector('select[name="sekolah"]')?.value || '';
    var jenis = document.querySelector('select[name="jenis_sekolah"]')?.value || '';
    var kelas = document.querySelector('select[name="kelas"]')?.value || '';
    var tAwal = document.querySelector('input[name="tanggal_awal"]')?.value || '';
    var tAkhir = document.querySelector('input[name="tanggal_akhir"]')?.value || '';
    var url = '{{ route("ilp.analisa-ckg-sekolah.export.excel") }}';
    var params = [];
    if (sekolah) params.push('sekolah=' + encodeURIComponent(sekolah));
    if (jenis) params.push('jenis_sekolah=' + encodeURIComponent(jenis));
    if (kelas) params.push('kelas=' + encodeURIComponent(kelas));
    if (tAwal) params.push('tanggal_awal=' + encodeURIComponent(tAwal));
    if (tAkhir) params.push('tanggal_akhir=' + encodeURIComponent(tAkhir));
    if (params.length > 0) url += '?' + params.join('&');
    var original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengunduh...';
    btn.disabled = true;
    window.location.href = url;
    setTimeout(function(){ btn.innerHTML = original; btn.disabled = false; }, 2000);
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var agg = JSON.parse(`{!! json_encode($agregasi ?? []) !!}`);
  var riskPct = JSON.parse(`{!! json_encode($persenResikoKategori ?? []) !!}`);
  var summary = JSON.parse(`{!! json_encode($ringkasanPemeriksaan ?? []) !!}`);
  var trenSekolah = JSON.parse(`{!! json_encode($trenBulananPerSekolah ?? []) !!}`);
  var topSekolah = JSON.parse(`{!! json_encode($topSekolah ?? []) !!}`);
  var titleMap = {
    'gula_darah': 'Gula Darah',
    'gejala_cemas': 'Gejala Cemas',
    'gejala_depresi': 'Gejala Depresi',
    'malaria': 'Malaria',
    'tropis_terabaikan': 'Penyakit Tropis Terabaikan',
    'riwayat_imunisasi': 'Riwayat Imunisasi',
    'resiko_hepatitis': 'Faktor Resiko Hepatitis',
    'resiko_tbc': 'Faktor Resiko TBC',
    'antropometri_fields': 'Antropometri (Isian)',
    'tekanan_darah': 'Tekanan Darah (Isian)',
    'mata_telinga': 'Skrining Mata & Telinga',
    'merokok': 'Perilaku Merokok Anak Sekolah',
    'reproduksi_putri': 'Kesehatan Reproduksi Putri',
    'reproduksi_putra': 'Kesehatan Reproduksi Putra',
    'kelayakan_kebugaran': 'Kelayakan Kebugaran',
    'aktivitas_fisik': 'Aktivitas Fisik Anak',
    'resiko_hepa_smp_sma': 'Faktor Resiko Hepatitis SMP/SMA',
    'resiko_talasemia': 'Faktor Resiko Thalasemia',
    'pemeriksaan_lab': 'Pemeriksaan Lab',
    'riwayat_hpv': 'Riwayat HPV Kelas 9 Putri',
    'resiko_mental_health': 'Faktor Resiko Mental Health'
  };
  function toStr(x){ return (x === null || x === undefined) ? '' : String(x); }
  function labels(cat){
    return (agg[cat]||[]).map(function(x){
      var n = x && x.name != null ? x.name : '';
      n = toStr(n);
      return n ? n.replace(/_/g,' ') : 'Tidak Diketahui';
    });
  }
  function data(cat){ return (agg[cat]||[]).map(function(x){ return x.count||0; }); }
  function addCard(id,title){
    var wrap = document.getElementById('chartsContainer');
    var col = document.createElement('div');
    col.className = 'col-md-6 mb-3';
    var card = document.createElement('div');
    card.className = 'card';
    var head = document.createElement('div');
    head.className = 'card-header bg-light';
    head.innerHTML = '<strong>'+title+'</strong>';
    var body = document.createElement('div');
    body.className = 'card-body';
    var div = document.createElement('div');
    div.id = id;
    body.appendChild(div);
    card.appendChild(head);
    card.appendChild(body);
    col.appendChild(card);
    wrap.appendChild(col);
    return div;
  }
  var donutEl = addCard('chartKebugaran','Status Kebugaran Jantung');
  var normal = JSON.parse(`{!! json_encode((int)($totalNormal ?? 0)) !!}`);
  var perlu = JSON.parse(`{!! json_encode((int)($totalPerlu ?? 0)) !!}`);
  var rujuk = JSON.parse(`{!! json_encode((int)($totalRujuk ?? 0)) !!}`);
  new ApexCharts(donutEl, {
    chart: { type: 'donut', height: 300 },
    series: [normal, perlu, rujuk],
    labels: ['Normal','Perlu Perhatian','Rujuk'],
    legend: { position: 'bottom' }
  }).render();
  function renderDonutFromSummary(key, label){
    var data = (summary && summary.lab && summary.lab[key]) ? summary.lab[key] : null;
    if (!data) return;
    var el = addCard('chart_'+key, label);
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: [parseInt(data.normal||0,10), parseInt(data.tidak||0,10)],
      labels: ['Normal','Tidak Normal'],
      legend: { position: 'bottom' }
    }).render();
  }
  function renderDonutAntropo(key, label){
    var data = (summary && summary.antropometri && summary.antropometri[key]) ? summary.antropometri[key] : null;
    if (!data) return;
    var el = addCard('chart_'+key, label);
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: [parseInt(data.normal||0,10), parseInt(data.tidak||0,10)],
      labels: ['Normal','Tidak Normal'],
      legend: { position: 'bottom' }
    }).render();
  }
  function renderDonutBp(){
    var data = (summary && summary.tekanan_darah && summary.tekanan_darah.bp) ? summary.tekanan_darah.bp : null;
    if (!data) return;
    var el = addCard('chart_bp', 'Tekanan Darah');
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: [parseInt(data.normal||0,10), parseInt(data.tidak||0,10)],
      labels: ['Normal','Tidak Normal'],
      legend: { position: 'bottom' }
    }).render();
  }
  renderDonutFromSummary('hasil_gds', 'Gula Darah Sewaktu (GDS)');
  renderDonutFromSummary('hasil_hb', 'Hemoglobin (HB)');
  renderDonutAntropo('imt', 'Indeks Massa Tubuh (IMT)');
  renderDonutAntropo('status_gizi', 'Status Gizi');
  (function(){
    var cat = summary && summary.antropometri && summary.antropometri.kategori_imt_dewasa ? summary.antropometri.kategori_imt_dewasa : null;
    if (!cat) return;
    var el = addCard('chart_imt_kategori','Kategori IMT (Dewasa)');
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: [
        parseInt(cat.underweight||0,10),
        parseInt(cat.normal||0,10),
        parseInt(cat.overweight||0,10),
        parseInt(cat.obesitas||0,10)
      ],
      labels: ['Underweight','Normal','Overweight','Obesitas'],
      legend: { position: 'bottom' }
    }).render();
  })();
  (function(){
    var kar = summary && summary.mata_telinga && summary.mata_telinga.gigi_karies_kategori ? summary.mata_telinga.gigi_karies_kategori : null;
    if (!kar) return;
    var el = addCard('chart_gigi_karies','Gigi Karies (Jumlah)');
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: [
        parseInt((kar['1']||0),10),
        parseInt((kar['2']||0),10),
        parseInt((kar['3']||0),10),
        parseInt((kar.gt3||0),10)
      ],
      labels: ['1','2','3','>3'],
      legend: { position: 'bottom' }
    }).render();
  })();
  function renderDonutMata(key, label){
    var data = (summary && summary.mata_telinga && summary.mata_telinga.distribusi && summary.mata_telinga.distribusi[key]) ? summary.mata_telinga.distribusi[key] : null;
    if (!data) return;
    var labels = Object.keys(data);
    var series = labels.map(function(k){ return parseInt(data[k]||0,10); });
    var el = addCard('chart_mata_'+key, label);
    new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      series: series,
      labels: labels,
      legend: { position: 'bottom' }
    }).render();
  }
  renderDonutMata('gangguan_telingga_kanan', 'Gangguan Telinga Kanan');
  renderDonutMata('gangguan_telingga_kiri', 'Gangguan Telinga Kiri');
  renderDonutMata('serumen_kanan', 'Serumen Kanan');
  renderDonutMata('serumen_kiri', 'Serumen Kiri');
  renderDonutMata('infeksi_telingga_kanan', 'Infeksi Telinga Kanan');
  renderDonutMata('infeksi_telingga_kiri', 'Infeksi Telinga Kiri');
  renderDonutMata('selaput_mata_kanan', 'Selaput Mata Kanan');
  renderDonutMata('selaput_mata_kiri', 'Selaput Mata Kiri');
  renderDonutMata('visus_mata_kanan', 'Visus Mata Kanan');
  renderDonutMata('visus_mata_kiri', 'Visus Mata Kiri');
  renderDonutMata('kacamata', 'Kacamata');
  renderDonutBp();
  var cakupan = JSON.parse(`{!! json_encode($cakupanPerSekolah ?? []) !!}`);
  if (cakupan && cakupan.length > 0) {
    var sekolah = cakupan.map(function(x){ var n = toStr(x.nama_sekolah).trim(); return n || 'Tanpa Sekolah'; });
    var totalSiswa = cakupan.map(function(x){ return parseInt(x.total_siswa || 0, 10); });
    var terskrining = cakupan.map(function(x){ return parseInt(x.terskrining || 0, 10); });
    var h = Math.min(600, 240 + Math.max(0, sekolah.length - 6) * 26);
    var elTrendTop = document.getElementById('chartTrendSekolahTop');
    var elTrend = elTrendTop ? elTrendTop : addCard('chartKunjunganSekolah','Kunjungan/Skrining per Sekolah');
    new ApexCharts(elTrend, {
      chart: { type: 'bar', height: h, stacked: false },
      series: [
        { name: 'Total Siswa', data: totalSiswa },
        { name: 'Terskrining', data: terskrining }
      ],
      xaxis: { categories: sekolah },
      legend: { position: 'bottom' }
    }).render();
  }
  Object.keys(agg).forEach(function(cat){
    if (!agg[cat] || agg[cat].length === 0) return;
    var el = addCard('chart_'+cat, titleMap[cat] || cat.replace(/_/g,' '));
    var isHorizontal = agg[cat].length > 8;
    var h = Math.min(600, 240 + Math.max(0, agg[cat].length - 6)*26);
    var yesData = (agg[cat]||[]).map(function(x){ return x.yes || 0; });
    var noData = (agg[cat]||[]).map(function(x){ return x.no || 0; });
    var hasYesNo = yesData.reduce(function(a,b){return a+b;},0) + noData.reduce(function(a,b){return a+b;},0) > 0;
    var options = {
      chart: { type: 'bar', height: h, stacked: hasYesNo },
      xaxis: { categories: labels(cat) },
      plotOptions: { bar: { horizontal: isHorizontal } },
      dataLabels: { enabled: false }
    };
    if (hasYesNo) {
      options.series = [
        { name: 'Ya', data: yesData },
        { name: 'Tidak', data: noData }
      ];
      options.legend = { position: 'bottom' };
    } else {
      options.series = [{ name: 'Jumlah', data: data(cat) }];
      options.legend = { show: false };
    }
    new ApexCharts(el, options).render();
  });

  
  if (riskPct && Object.keys(riskPct).length > 0) {
    var cats = Object.keys(riskPct);
    var labels = cats.map(function(k){ return (titleMap[k] || toStr(k)).replace(/_/g,' '); });
    var values = cats.map(function(k){ var v = (riskPct[k] && riskPct[k].percent) ? riskPct[k].percent : 0; return Math.round(v * 100) / 100; });
    var isHorizontal = cats.length > 8;
    var h = Math.min(600, 240 + Math.max(0, cats.length - 6)*26);
    var topEl = document.getElementById('chartRiskPctTop');
    if (topEl) {
      new ApexCharts(topEl, {
        chart: { type: 'bar', height: h },
        series: [{ name: 'Persen', data: values }],
        xaxis: { categories: labels },
        plotOptions: { bar: { horizontal: isHorizontal } },
        dataLabels: { enabled: true, formatter: function(val){ return val + '%'; } },
        yaxis: { labels: { formatter: function(val){ return val + '%'; } } },
        tooltip: { y: { formatter: function(val){ return val + '%'; } } }
      }).render();
    } else {
      var el = addCard('chartRiskPct','Persentase Risiko per Kategori');
      new ApexCharts(el, {
        chart: { type: 'bar', height: h },
        series: [{ name: 'Persen', data: values }],
        xaxis: { categories: labels },
        plotOptions: { bar: { horizontal: isHorizontal } },
        dataLabels: { enabled: true, formatter: function(val){ return val + '%'; } },
        yaxis: { labels: { formatter: function(val){ return val + '%'; } } },
        tooltip: { y: { formatter: function(val){ return val + '%'; } } }
      }).render();
    }
  }
});
</script>
@endpush
