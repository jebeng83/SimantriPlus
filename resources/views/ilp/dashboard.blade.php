@extends('adminlte::page')

@section('title', 'ILP Dashboard')

@section('css')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
   <div>
      <h1 class="m-0 text-dark"><i class="fas fa-chart-line mr-2"></i>Dashboard ILP</h1>
      <h5 class="text-muted"><i class="fas fa-user-md mr-1"></i>Selamat Datang, {{$nm_dokter}}</h5>
   </div>
   <div class="text-right">
      <h5 id="tanggalHari" class="text-muted"></h5>
      <h5 id="jamDigital" class="text-muted"></h5>
   </div>
</div>
@endsection

@section('content')

<!-- Filter Card -->
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-lg border-0 rounded-lg filter-card">
         <div class="card-header bg-gradient-primary text-white py-3">
            <h3 class="card-title m-0 d-flex align-items-center">
               <i class="fas fa-filter mr-2"></i>Filter Data Sasaran Posyandu
            </h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body py-4">
            <form id="filterForm" method="GET" action="{{ route('ilp.dashboard') }}">
               <div class="row">
                  <div class="col-md-12 mb-3">
                     <div class="d-flex align-items-center flex-wrap flex-md-nowrap">
                        <div class="input-group-prepend mr-2 mb-2 mb-md-0">
                           <span class="btn btn-primary rounded-circle pulse-animation"
                              style="height: 50px; width: 50px;">
                              <i class="fas fa-home"></i>
                           </span>
                        </div>

                        <div class="flex-grow-1 mr-3 mb-2 mb-md-0">
                           <label for="desa" class="text-muted small mb-1"><i
                                 class="fas fa-city mr-1"></i>Desa/Kelurahan</label>
                           <select class="form-control select2-custom" id="desa" name="desa"
                              data-placeholder="Pilih Desa/Kelurahan">
                              <option value="">Semua Desa/Kelurahan</option>
                              @foreach($daftar_desa as $desa)
                              <option value="{{ $desa }}" {{ request('desa')==$desa ? 'selected' : '' }}>
                                 {{ $desa }}
                              </option>
                              @endforeach
                           </select>
                        </div>

                        <div class="flex-grow-1 mr-3 mb-2 mb-md-0">
                           <label for="posyandu" class="text-muted small mb-1"><i
                                 class="fas fa-map-marker-alt mr-1"></i>Lokasi Posyandu</label>
                           <select class="form-control select2-custom" id="posyandu" name="posyandu"
                              data-placeholder="Pilih Posyandu">
                              <option value="">Semua Posyandu</option>
                              @foreach($daftar_posyandu as $posyandu)
                              <option value="{{ $posyandu }}" {{ request('posyandu')==$posyandu ? 'selected' : '' }}>
                                 {{ $posyandu }}
                              </option>
                              @endforeach
                           </select>
                        </div>

                        <div class="button-group d-flex align-items-end">
                           <button type="submit" class="btn btn-primary px-4 btn-filter">
                              <i class="fas fa-filter mr-1"></i> Filter Data
                           </button>
                           <button id="resetFilter" type="button" class="btn btn-light px-4 btn-reset ml-2">
                              <i class="fas fa-undo mr-1"></i> Reset
                           </button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Info Box Scrollable -->
<div class="row mb-4">
   <div class="col-12">
      <div class="card elevation-3">
         <div class="card-header bg-gradient-light">
            <h3 class="card-title"><i class="fas fa-users mr-2"></i>Data Sasaran Posyandu</h3>
            <div class="card-tools">
               <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
               </button>
            </div>
         </div>
         <div class="card-body p-0">
            <div class="d-flex flex-nowrap overflow-auto py-3 px-3" style="scrollbar-width: thin;">
               <!-- Balita -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-primary border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-primary rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-baby fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Balita</span>
                        <span class="mini-info-box-number text-primary font-weight-bold" style="font-size: 1.5rem;">{{
                           $balita }}</span>
                        <span class="mini-info-box-desc text-muted"><small>0-5 tahun</small></span>
                     </div>
                  </div>
               </div>

               <!-- Pra Sekolah -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-success border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-success rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-child fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Pra Sekolah</span>
                        <span class="mini-info-box-number text-success font-weight-bold" style="font-size: 1.5rem;">{{
                           $pra_sekolah }}</span>
                        <span class="mini-info-box-desc text-muted"><small>6-9 tahun</small></span>
                     </div>
                  </div>
               </div>

               <!-- Remaja -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-info border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-info rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Remaja</span>
                        <span class="mini-info-box-number text-info font-weight-bold" style="font-size: 1.5rem;">{{
                           $remaja }}</span>
                        <span class="mini-info-box-desc text-muted"><small>10-18 tahun</small></span>
                     </div>
                  </div>
               </div>

               <!-- Usia Produktif -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-warning border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-warning rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-user-tie fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Produktif</span>
                        <span class="mini-info-box-number text-warning font-weight-bold" style="font-size: 1.5rem;">{{
                           $produktif }}</span>
                        <span class="mini-info-box-desc text-muted"><small>19-59 tahun</small></span>
                     </div>
                  </div>
               </div>

               <!-- Lansia -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-danger border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-danger rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-user-plus fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Lansia</span>
                        <span class="mini-info-box-number text-danger font-weight-bold" style="font-size: 1.5rem;">{{
                           $lansia }}</span>
                        <span class="mini-info-box-desc text-muted"><small>>60 tahun</small></span>
                     </div>
                  </div>
               </div>

               <!-- Total -->
               <div class="mini-info-box bg-white shadow-sm mx-2 rounded-lg border-left border-secondary border-left-4"
                  style="min-width: 200px; height: 100px; flex: 1 0 auto; max-width: 250px;">
                  <div class="d-flex align-items-center h-100 p-3">
                     <div
                        class="mini-info-box-icon bg-secondary rounded-circle p-3 mr-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-2x text-white"></i>
                     </div>
                     <div class="mini-info-box-content">
                        <span class="mini-info-box-text text-muted font-weight-bold">Total</span>
                        <span class="mini-info-box-number text-secondary font-weight-bold" style="font-size: 1.5rem;">{{
                           $balita + $pra_sekolah + $remaja + $produktif + $lansia
                           }}</span>
                        <span class="mini-info-box-desc text-muted"><small>Semua Usia</small></span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="text-center pb-2">
               <small class="text-muted"><i class="fas fa-arrow-left mr-1"></i> Geser untuk melihat lebih banyak <i
                     class="fas fa-arrow-right ml-1"></i></small>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-md-12">
      <x-adminlte-card title="Statistik Sasaran Posyandu" theme="info" theme-mode="outline" icon="fas fa-chart-bar"
         class="elevation-3">
         <div class="chart-container" style="position: relative; height:50vh;">
            <canvas id="chartPemeriksaan"></canvas>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Grafik Kunjungan Posyandu -->
<div class="row">
   <div class="col-md-12">
      <x-adminlte-card title="Statistik Kunjungan Posyandu Berdasarkan Umur" theme="primary" theme-mode="outline"
         icon="fas fa-chart-line" class="elevation-3" id="grafikKunjungan">
         <div class="mb-3">
            <form id="periodeForm" method="GET" action="{{ route('ilp.dashboard') }}" class="d-flex align-items-center">
               <!-- Menyimpan nilai filter posyandu yang sedang aktif -->
               <input type="hidden" name="posyandu" value="{{ request('posyandu') }}">
               <input type="hidden" name="desa" value="{{ request('desa') }}">
               <input type="hidden" name="scroll_to" value="grafikKunjungan">

               <div class="mr-2">
                  <label class="mb-0 mr-2"><i class="fas fa-calendar-alt mr-1"></i> Periode:</label>
               </div>
               <div class="btn-group" role="group">
                  <button type="button" data-periode="minggu"
                     class="btn-periode btn {{ $periode_filter == 'minggu' ? 'btn-primary' : 'btn-outline-primary' }}">
                     <i class="fas fa-calendar-week mr-1"></i> Mingguan
                  </button>
                  <button type="button" data-periode="bulan"
                     class="btn-periode btn {{ $periode_filter == 'bulan' ? 'btn-primary' : 'btn-outline-primary' }}">
                     <i class="fas fa-calendar-alt mr-1"></i> Bulanan
                  </button>
                  <button type="button" data-periode="tahun"
                     class="btn-periode btn {{ $periode_filter == 'tahun' ? 'btn-primary' : 'btn-outline-primary' }}">
                     <i class="fas fa-calendar mr-1"></i> Tahunan
                  </button>
               </div>
            </form>
         </div>
         <div class="chart-container" style="position: relative; height:50vh;">
            <canvas id="chartKunjunganByUmur"></canvas>
         </div>
         <div class="text-center mt-3">
            <small class="text-muted">
               <i class="fas fa-info-circle mr-1"></i>
               @if($periode_filter == 'minggu')
               Data kunjungan 12 minggu terakhir dari tabel ilp_dewasa
               @elseif($periode_filter == 'tahun')
               Data kunjungan 5 tahun terakhir dari tabel ilp_dewasa
               @else
               Data kunjungan 6 bulan terakhir dari tabel ilp_dewasa
               @endif
            </small>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Tabel Ringkasan Kunjungan Posyandu -->
<div class="row">
   <div class="col-md-12">
      <x-adminlte-card title="Ringkasan Kunjungan Posyandu {{ ucfirst($periode_filter) }}an" theme="primary"
         theme-mode="outline" icon="fas fa-table" class="elevation-3" id="tabelRingkasan">
         <div class="table-responsive">
            <table class="table table-bordered table-hover">
               <thead class="thead-light">
                  <tr>
                     <th>Periode</th>
                     <th>Balita (0-5)</th>
                     <th>Pra Sekolah (6-9)</th>
                     <th>Remaja (10-18)</th>
                     <th>Produktif (19-59)</th>
                     <th>Lansia (>60)</th>
                     <th>Total</th>
                  </tr>
               </thead>
               <tbody>
                  @php
                  $total_balita = 0;
                  $total_pra_sekolah = 0;
                  $total_remaja = 0;
                  $total_produktif = 0;
                  $total_lansia = 0;
                  $total_semua = 0;
                  @endphp

                  @if(isset($kunjungan_posyandu['labels']) && count($kunjungan_posyandu['labels']) > 0)
                  @foreach($kunjungan_posyandu['labels'] as $index => $label)
                  @php
                  $balita_val = $kunjungan_posyandu['balita'][$index] ?? 0;
                  $pra_sekolah_val = $kunjungan_posyandu['pra_sekolah'][$index] ?? 0;
                  $remaja_val = $kunjungan_posyandu['remaja'][$index] ?? 0;
                  $produktif_val = $kunjungan_posyandu['produktif'][$index] ?? 0;
                  $lansia_val = $kunjungan_posyandu['lansia'][$index] ?? 0;
                  $total_bulan = $balita_val + $pra_sekolah_val + $remaja_val + $produktif_val + $lansia_val;

                  $total_balita += $balita_val;
                  $total_pra_sekolah += $pra_sekolah_val;
                  $total_remaja += $remaja_val;
                  $total_produktif += $produktif_val;
                  $total_lansia += $lansia_val;
                  $total_semua += $total_bulan;
                  @endphp
                  <tr>
                     <td>{{ $label }}</td>
                     <td>{{ $balita_val }}</td>
                     <td>{{ $pra_sekolah_val }}</td>
                     <td>{{ $remaja_val }}</td>
                     <td>{{ $produktif_val }}</td>
                     <td>{{ $lansia_val }}</td>
                     <td><strong>{{ $total_bulan }}</strong></td>
                  </tr>
                  @endforeach
                  @else
                  <tr>
                     <td colspan="7" class="text-center">Tidak ada data kunjungan posyandu dalam periode yang dipilih
                     </td>
                  </tr>
                  @endif
               </tbody>
               <tfoot class="bg-light font-weight-bold">
                  <tr>
                     <td>Total</td>
                     <td>{{ $total_balita }}</td>
                     <td>{{ $total_pra_sekolah }}</td>
                     <td>{{ $total_remaja }}</td>
                     <td>{{ $total_produktif }}</td>
                     <td>{{ $total_lansia }}</td>
                     <td>{{ $total_semua }}</td>
                  </tr>
               </tfoot>
            </table>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Grafik Persentase Kunjungan Posyandu -->
<div class="row">
   <div class="col-md-6">
      <x-adminlte-card title="Persentase Kunjungan Posyandu {{ ucfirst($periode_filter) }}an" theme="success"
         theme-mode="outline" icon="fas fa-chart-pie" class="elevation-3" id="grafikPersentase">
         <div class="chart-container" style="position: relative; height:40vh;">
            <canvas id="donutKunjunganPosyandu"></canvas>
         </div>
         <div class="text-center mt-3">
            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Persentase kunjungan berdasarkan
               kelompok
               umur</small>
         </div>
      </x-adminlte-card>
   </div>
   <div class="col-md-6">
      <x-adminlte-card title="Distribusi Kelompok Umur" theme="info" theme-mode="outline" icon="fas fa-chart-pie"
         class="elevation-3">
         <div class="chart-container" style="position: relative; height:40vh;">
            <canvas id="pieChart"></canvas>
         </div>
      </x-adminlte-card>
   </div>
</div>

<div class="row">
   <div class="col-md-6">
      <x-adminlte-card title="Ringkasan Data" theme="success" theme-mode="outline" icon="fas fa-list-alt"
         class="elevation-3">
         <div class="table-responsive">
            <table class="table table-bordered table-hover">
               <thead class="thead-light">
                  <tr>
                     <th>Kelompok Umur</th>
                     <th>Jumlah</th>
                     <th>Persentase</th>
                  </tr>
               </thead>
               <tbody>
                  @php
                  $total = $balita + $pra_sekolah + $remaja + $produktif + $lansia;
                  $persentase_balita = $total > 0 ? round(($balita / $total) * 100, 2) : 0;
                  $persentase_pra_sekolah = $total > 0 ? round(($pra_sekolah / $total) * 100, 2) : 0;
                  $persentase_remaja = $total > 0 ? round(($remaja / $total) * 100, 2) : 0;
                  $persentase_produktif = $total > 0 ? round(($produktif / $total) * 100, 2) : 0;
                  $persentase_lansia = $total > 0 ? round(($lansia / $total) * 100, 2) : 0;
                  @endphp
                  <tr>
                     <td><i class="fas fa-baby text-primary mr-2"></i>Balita (0-5 tahun)</td>
                     <td>{{ $balita }}</td>
                     <td>
                        <div class="progress">
                           <div class="progress-bar bg-primary" role="progressbar"
                              style="width: {{ $persentase_balita }}" aria-valuenow="{{ $persentase_balita }}"
                              aria-valuemin="0" aria-valuemax="100">{{ $persentase_balita }}</div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td><i class="fas fa-child text-success mr-2"></i>Pra Sekolah (6-9 tahun)</td>
                     <td>{{ $pra_sekolah }}</td>
                     <td>
                        <div class="progress">
                           <div class="progress-bar bg-success" role="progressbar"
                              style="width: {{ $persentase_pra_sekolah }}" aria-valuenow="{{ $persentase_pra_sekolah }}"
                              aria-valuemin="0" aria-valuemax="100">{{
                              $persentase_pra_sekolah }}</div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td><i class="fas fa-user text-info mr-2"></i>Remaja (10-18 tahun)</td>
                     <td>{{ $remaja }}</td>
                     <td>
                        <div class="progress">
                           <div class="progress-bar bg-info" role="progressbar" style="width: {{ $persentase_remaja }}"
                              aria-valuenow="{{ $persentase_remaja }}" aria-valuemin="0" aria-valuemax="100">{{
                              $persentase_remaja }}</div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td><i class="fas fa-user-tie text-warning mr-2"></i>Usia Produktif (19-59 tahun)</td>
                     <td>{{ $produktif }}</td>
                     <td>
                        <div class="progress">
                           <div class="progress-bar bg-warning" role="progressbar"
                              style="width: {{ $persentase_produktif }}" aria-valuenow="{{ $persentase_produktif }}"
                              aria-valuemin="0" aria-valuemax="100">{{ $persentase_produktif }}</div>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td><i class="fas fa-user-plus text-danger mr-2"></i>Lansia (>60 tahun)</td>
                     <td>{{ $lansia }}</td>
                     <td>
                        <div class="progress">
                           <div class="progress-bar bg-danger" role="progressbar"
                              style="width: {{ $persentase_lansia }}" aria-valuenow="{{ $persentase_lansia }}"
                              aria-valuemin="0" aria-valuemax="100">{{ $persentase_lansia }}</div>
                        </div>
                     </td>
                  </tr>
                  <tr class="bg-light font-weight-bold">
                     <td><i class="fas fa-users mr-2"></i>Total</td>
                     <td>{{ $total }}</td>
                     <td>100</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Grafik Kunjungan Berdasarkan Posyandu -->
<div class="row">
   <div class="col-md-12">
      <x-adminlte-card title="Statistik Kunjungan Berdasarkan Posyandu" theme="success" theme-mode="outline"
         icon="fas fa-chart-bar" class="elevation-3" id="grafikKunjunganPosyandu">
         <div class="mb-3">
            <form id="periodeFormPosyandu" method="GET" action="{{ route('ilp.dashboard') }}"
               class="d-flex align-items-center">
               <!-- Menyimpan nilai filter posyandu yang sedang aktif -->
               <input type="hidden" name="posyandu" value="{{ request('posyandu') }}">
               <input type="hidden" name="desa" value="{{ request('desa') }}">
               <input type="hidden" name="scroll_to" value="grafikKunjunganPosyandu">

               <div class="mr-2">
                  <label class="mb-0 mr-2"><i class="fas fa-calendar-alt mr-1"></i> Periode:</label>
               </div>
               <div class="btn-group" role="group">
                  <button type="button" data-periode="minggu"
                     class="btn-periode btn {{ $periode_filter == 'minggu' ? 'btn-success' : 'btn-outline-success' }}">
                     <i class="fas fa-calendar-week mr-1"></i> Mingguan
                  </button>
                  <button type="button" data-periode="bulan"
                     class="btn-periode btn {{ $periode_filter == 'bulan' ? 'btn-success' : 'btn-outline-success' }}">
                     <i class="fas fa-calendar-alt mr-1"></i> Bulanan
                  </button>
                  <button type="button" data-periode="tahun"
                     class="btn-periode btn {{ $periode_filter == 'tahun' ? 'btn-success' : 'btn-outline-success' }}">
                     <i class="fas fa-calendar mr-1"></i> Tahunan
                  </button>
               </div>
            </form>
         </div>
         <div class="chart-container" style="position: relative; height:50vh;">
            <canvas id="chartKunjunganByPosyandu"></canvas>
         </div>
         <div class="text-center mt-3">
            <small class="text-muted">
               <i class="fas fa-info-circle mr-1"></i>
               @if($periode_filter == 'minggu')
               Data kunjungan 12 minggu terakhir dari tabel ilp_dewasa
               @elseif($periode_filter == 'tahun')
               Data kunjungan 5 tahun terakhir dari tabel ilp_dewasa
               @else
               Data kunjungan 6 bulan terakhir dari tabel ilp_dewasa
               @endif
            </small>
         </div>
      </x-adminlte-card>
   </div>
</div>

<!-- Dashboard Faktor Risiko -->
<div class="row">
   <div class="col-md-12">
      <x-adminlte-card title="Faktor Risiko Berdasarkan Hasil Pemeriksaan" theme="danger" theme-mode="outline"
         icon="fas fa-heartbeat" class="elevation-3" id="hasilPemeriksaan">

         <!-- Hasil Pemeriksaan Terakhir -->
         <div class="row mb-4">
            <div class="col-md-6">
               <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                  <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                     <h5 class="font-weight-bold text-info mb-1">
                        <i class="fas fa-weight mr-2"></i>Indeks Massa Tubuh (IMT)
                     </h5>
                     <p class="text-muted small mb-0">Hasil pemeriksaan terakhir</p>
                  </div>
                  <div class="card-body d-flex align-items-center pt-2">
                     <div
                        class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 80px; height: 80px; flex: 0 0 auto;">
                        <span class="font-weight-bold" style="font-size: 1.5rem;">
                           {{ $faktor_risiko['last_check']['imt'] ?? '-' }}
                        </span>
                     </div>
                     <div>
                        <h6 class="mb-1 font-weight-bold">Kategori:</h6>
                        <span
                           class="badge badge-pill badge-{{ $faktor_risiko['last_check']['imt_class'] ?? 'secondary' }} px-3 py-2">
                           <i class="fas fa-chart-line mr-1"></i>
                           {{ $faktor_risiko['last_check']['imt_category'] ?? 'Tidak ada data' }}
                        </span>
                        <p class="text-muted small mt-2 mb-0">
                           <i class="fas fa-info-circle mr-1"></i>
                           IMT normal: 18.5 - 24.9
                        </p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                  <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                     <h5 class="font-weight-bold text-danger mb-1">
                        <i class="fas fa-heartbeat mr-2"></i>Tekanan Darah
                     </h5>
                     <p class="text-muted small mb-0">Hasil pemeriksaan terakhir</p>
                  </div>
                  <div class="card-body d-flex align-items-center pt-2">
                     <div
                        class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 80px; height: 80px; flex: 0 0 auto;">
                        <span class="font-weight-bold" style="font-size: 1.2rem;">
                           {{ $faktor_risiko['last_check']['td'] ?? '-' }}
                        </span>
                     </div>
                     <div>
                        <h6 class="mb-1 font-weight-bold">Kategori:</h6>
                        <span
                           class="badge badge-pill badge-{{ $faktor_risiko['last_check']['td_class'] ?? 'secondary' }} px-3 py-2">
                           <i class="fas fa-chart-line mr-1"></i>
                           {{ $faktor_risiko['last_check']['td_category'] ?? 'Tidak ada data' }}
                        </span>
                        <p class="text-muted small mt-2 mb-0">
                           <i class="fas fa-info-circle mr-1"></i>
                           TD normal: < 120/80 mmHg </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Grafik dan Tabel Distribusi -->
         <div class="row">
            <!-- Grafik IMT -->
            <div class="col-md-6">
               <div class="card">
                  <div class="card-header bg-info">
                     <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Distribusi IMT
                     </h3>
                  </div>
                  <div class="card-body">
                     <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="chartIMT"></canvas>
                     </div>
                  </div>
               </div>

               <!-- Tabel Distribusi IMT -->
               <div class="card mt-3">
                  <div class="card-header bg-info">
                     <h3 class="card-title">
                        <i class="fas fa-table mr-1"></i>
                        Tabel Distribusi IMT
                     </h3>
                  </div>
                  <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="table table-striped" id="tabelIMT">
                           <thead>
                              <tr>
                                 <th>Kategori</th>
                                 <th>Jumlah</th>
                                 <th>Persentase</th>
                              </tr>
                           </thead>
                           <tbody>
                              @php
                              $total_imt = $faktor_risiko['total'] > 0 ? $faktor_risiko['total'] :
                              1;
                              $persentase_kurus = round(($faktor_risiko['imt']['kurus'] /
                              $total_imt) * 100, 2);
                              $persentase_normal = round(($faktor_risiko['imt']['normal'] /
                              $total_imt) * 100, 2);
                              $persentase_kelebihan_bb =
                              round(($faktor_risiko['imt']['kelebihan_bb'] / $total_imt) *
                              100, 2);
                              $persentase_obesitas = round(($faktor_risiko['imt']['obesitas'] /
                              $total_imt) * 100, 2);
                              @endphp
                              <tr>
                                 <td>Kurus (< 18.5)</td>
                                 <td>{{ $faktor_risiko['imt']['kurus'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-info" role="progressbar"
                                          style="width: {{ $persentase_kurus }}" aria-valuenow="{{ $persentase_kurus }}"
                                          aria-valuemin="0" aria-valuemax="100">
                                          {{ $persentase_kurus }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Normal (18.5 - 24.9)</td>
                                 <td>{{ $faktor_risiko['imt']['normal'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-success" role="progressbar"
                                          style="width: {{ $persentase_normal }}"
                                          aria-valuenow="{{ $persentase_normal }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_normal }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Kelebihan BB (25 - 29.9)</td>
                                 <td>{{ $faktor_risiko['imt']['kelebihan_bb'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-warning" role="progressbar"
                                          style="width: {{ $persentase_kelebihan_bb }}"
                                          aria-valuenow="{{ $persentase_kelebihan_bb }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_kelebihan_bb }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Obesitas (≥ 30)</td>
                                 <td>{{ $faktor_risiko['imt']['obesitas'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-danger" role="progressbar"
                                          style="width: {{ $persentase_obesitas }}"
                                          aria-valuenow="{{ $persentase_obesitas }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_obesitas }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                           <tfoot>
                              <tr class="bg-light font-weight-bold">
                                 <td>Total</td>
                                 <td>{{ $faktor_risiko['total'] }}</td>
                                 <td>100</td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Grafik Tekanan Darah -->
            <div class="col-md-6">
               <div class="card">
                  <div class="card-header bg-danger">
                     <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Distribusi Tekanan Darah
                     </h3>
                  </div>
                  <div class="card-body">
                     <div class="chart-container" style="position: relative; height:250px;">
                        <canvas id="chartTD"></canvas>
                     </div>
                  </div>
               </div>

               <!-- Tabel Distribusi Tekanan Darah -->
               <div class="card mt-3">
                  <div class="card-header bg-danger">
                     <h3 class="card-title">
                        <i class="fas fa-table mr-1"></i>
                        Tabel Distribusi Tekanan Darah
                     </h3>
                  </div>
                  <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="table table-striped" id="tabelTD">
                           <thead>
                              <tr>
                                 <th>Kategori</th>
                                 <th>Jumlah</th>
                                 <th>Persentase</th>
                              </tr>
                           </thead>
                           <tbody>
                              @php
                              $total_td = $faktor_risiko['total'] > 0 ? $faktor_risiko['total'] :
                              1;
                              $persentase_normal_td = round(($faktor_risiko['td']['normal'] /
                              $total_td) * 100, 2);
                              $persentase_pra_hipertensi =
                              round(($faktor_risiko['td']['pra_hipertensi'] / $total_td) *
                              100, 2);
                              $persentase_hipertensi_1 =
                              round(($faktor_risiko['td']['hipertensi_1'] / $total_td) * 100,
                              2);
                              $persentase_hipertensi_2 =
                              round(($faktor_risiko['td']['hipertensi_2'] / $total_td) * 100,
                              2);
                              $persentase_hipertensi_sistolik =
                              round(($faktor_risiko['td']['hipertensi_sistolik'] /
                              $total_td) * 100, 2);
                              @endphp
                              <tr>
                                 <td>Normal (< 120/80)</td>
                                 <td>{{ $faktor_risiko['td']['normal'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-success" role="progressbar"
                                          style="width: {{ $persentase_normal_td }}"
                                          aria-valuenow="{{ $persentase_normal_td }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_normal_td }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Pra-hipertensi (120-139/80-89)</td>
                                 <td>{{ $faktor_risiko['td']['pra_hipertensi'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-warning" role="progressbar"
                                          style="width: {{ $persentase_pra_hipertensi }}"
                                          aria-valuenow="{{ $persentase_pra_hipertensi }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_pra_hipertensi }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Hipertensi 1 (140-159/90-99)</td>
                                 <td>{{ $faktor_risiko['td']['hipertensi_1'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-danger" role="progressbar"
                                          style="width: {{ $persentase_hipertensi_1 }}"
                                          aria-valuenow="{{ $persentase_hipertensi_1 }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_hipertensi_1 }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Hipertensi 2 (≥ 160/100)</td>
                                 <td>{{ $faktor_risiko['td']['hipertensi_2'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-danger" role="progressbar"
                                          style="width: {{ $persentase_hipertensi_2 }}"
                                          aria-valuenow="{{ $persentase_hipertensi_2 }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_hipertensi_2 }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                              <tr>
                                 <td>Hipertensi Sistolik (> 140/< 90)</td>
                                 <td>{{ $faktor_risiko['td']['hipertensi_sistolik'] }}</td>
                                 <td>
                                    <div class="progress">
                                       <div class="progress-bar bg-purple" role="progressbar"
                                          style="width: {{ $persentase_hipertensi_sistolik }}"
                                          aria-valuenow="{{ $persentase_hipertensi_sistolik }}" aria-valuemin="0"
                                          aria-valuemax="100">
                                          {{ $persentase_hipertensi_sistolik }}
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                           <tfoot>
                              <tr class="bg-light font-weight-bold">
                                 <td>Total</td>
                                 <td>{{ $faktor_risiko['total'] }}</td>
                                 <td>100</td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </x-adminlte-card>
   </div>
</div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)
@section('plugins.Select2', true)
@section('plugins.Chartjs', true)

@section('js')
<script>
   // Simpan referensi chart dalam variabel global
   let charts = {};

   $(function() {
   // Inisialisasi Select2
   $('.select2-custom').select2({
      theme: 'bootstrap4',
      width: '100%'
   });

   // Fungsi untuk inisialisasi chart
   function initChart(canvasId, config) {
      const ctx = document.getElementById(canvasId).getContext('2d');
      // Simpan instance chart
      charts[canvasId] = new Chart(ctx, config);
      return charts[canvasId];
   }

   // Fungsi untuk update data chart
   function updateChartData(chartId, newData) {
      const chart = charts[chartId];
      if (chart) {
         chart.data.labels = newData.labels;
         chart.data.datasets = newData.datasets;
         chart.update();
      }
   }

   // Reset filter
   $('#resetFilter').on('click', function() {
      $('#desa').val('').trigger('change');
      $('#posyandu').val('').trigger('change');
      window.location.href = "{{ route('ilp.dashboard') }}";
   });

   // Update posyandu berdasarkan desa yang dipilih
   $('#desa').on('change', function() {
      const desa = $(this).val();
      
      // Jika desa kosong, tampilkan semua posyandu
      if (!desa) {
         $('#posyandu').val('').trigger('change');
         return;
      }
      
      // Ambil daftar posyandu berdasarkan desa
      $.ajax({
         url: "{{ route('ilp.dashboard') }}",
         type: "GET",
         data: {
            get_posyandu_by_desa: true,
            desa: desa,
            ajax: true
         },
         dataType: "json",
         success: function(response) {
            // Kosongkan dropdown posyandu
            $('#posyandu').empty();
            
            // Tambahkan opsi default
            $('#posyandu').append('<option value="">Semua Posyandu</option>');
            
            // Tambahkan opsi posyandu
            if (response.daftar_posyandu && response.daftar_posyandu.length > 0) {
               response.daftar_posyandu.forEach(function(posyandu) {
                  $('#posyandu').append('<option value="' + posyandu + '">' + posyandu + '</option>');
               });
            }
            
            // Refresh Select2
            $('#posyandu').trigger('change');
         },
         error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data posyandu. Silakan coba lagi.');
         }
      });
   });

   // Inisialisasi jam digital
   function updateJam() {
      const now = new Date();
      const options = { 
         weekday: 'long', 
         year: 'numeric', 
         month: 'long', 
         day: 'numeric' 
      };
      const jam = now.getHours().toString().padStart(2, '0');
      const menit = now.getMinutes().toString().padStart(2, '0');
      const detik = now.getSeconds().toString().padStart(2, '0');
      
      $('#tanggalHari').html('<i class="fas fa-calendar-alt mr-1"></i>' + now.toLocaleDateString('id-ID', options));
      $('#jamDigital').html('<i class="fas fa-clock mr-1"></i>' + jam + ':' + menit + ':' + detik);
   }
   
   // Update jam setiap detik
   updateJam();
   setInterval(updateJam, 1000);

   // Inisialisasi grafik pemeriksaan
   const pemeriksaanConfig = {
      type: 'bar',
      data: {
         labels: ['Balita (0-5)', 'Pra Sekolah (6-9)', 'Remaja (10-18)', 'Produktif (19-59)', 'Lansia (>60)'],
         datasets: [{
            label: 'Jumlah Sasaran',
            data: [
               {{ $balita }},
               {{ $pra_sekolah }},
               {{ $remaja }},
               {{ $produktif }},
               {{ $lansia }}
            ],
            backgroundColor: [
               'rgba(23, 162, 184, 0.8)', // info
               'rgba(40, 167, 69, 0.8)', // success
               'rgba(0, 123, 255, 0.8)', // primary
               'rgba(255, 193, 7, 0.8)', // warning
               'rgba(220, 53, 69, 0.8)'  // danger
            ],
            borderColor: [
               'rgba(23, 162, 184, 1)',
               'rgba(40, 167, 69, 1)',
               'rgba(0, 123, 255, 1)',
               'rgba(255, 193, 7, 1)',
               'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            y: {
               beginAtZero: true,
               ticks: {
                  precision: 0
               }
            }
         },
         plugins: {
            legend: {
               display: false
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     const label = context.dataset.label || '';
                     const value = context.raw || 0;
                     const total = {{ $balita + $pra_sekolah + $remaja + $produktif + $lansia }};
                     const percentage = Math.round((value / total) * 100);
                     return `${label}: ${value} (${percentage}%)`;
                  }
               }
            }
         }
      }
   };
   const pemeriksaanChart = initChart('chartPemeriksaan', pemeriksaanConfig);

   // Inisialisasi grafik pie untuk distribusi kelompok umur
   const pieConfig = {
      type: 'pie',
      data: {
         labels: ['Balita (0-5)', 'Pra Sekolah (6-9)', 'Remaja (10-18)', 'Produktif (19-59)', 'Lansia (>60)'],
         datasets: [{
            data: [
               {{ $balita }},
               {{ $pra_sekolah }},
               {{ $remaja }},
               {{ $produktif }},
               {{ $lansia }}
            ],
            backgroundColor: [
               'rgba(23, 162, 184, 0.8)', // info
               'rgba(40, 167, 69, 0.8)', // success
               'rgba(0, 123, 255, 0.8)', // primary
               'rgba(255, 193, 7, 0.8)', // warning
               'rgba(220, 53, 69, 0.8)'  // danger
            ],
            borderColor: [
               'rgba(23, 162, 184, 1)',
               'rgba(40, 167, 69, 1)',
               'rgba(0, 123, 255, 1)',
               'rgba(255, 193, 7, 1)',
               'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: {
               position: 'bottom'
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     const label = context.label || '';
                     const value = context.raw || 0;
                     const total = {{ $balita + $pra_sekolah + $remaja + $produktif + $lansia }};
                     const percentage = Math.round((value / total) * 100);
                     return `${label}: ${value} (${percentage}%)`;
                  }
               }
            }
         }
      }
   };
   const pieChart = initChart('pieChart', pieConfig);

   // Inisialisasi grafik donut untuk persentase kunjungan
   const donutConfig = {
      type: 'doughnut',
      data: {
         labels: ['Balita (0-5)', 'Pra Sekolah (6-9)', 'Remaja (10-18)', 'Produktif (19-59)', 'Lansia (>60)'],
         datasets: [{
            data: [
               {{ array_sum($kunjungan_posyandu['balita'] ?? []) }},
               {{ array_sum($kunjungan_posyandu['pra_sekolah'] ?? []) }},
               {{ array_sum($kunjungan_posyandu['remaja'] ?? []) }},
               {{ array_sum($kunjungan_posyandu['produktif'] ?? []) }},
               {{ array_sum($kunjungan_posyandu['lansia'] ?? []) }}
            ],
            backgroundColor: [
               'rgba(23, 162, 184, 0.8)', // info
               'rgba(40, 167, 69, 0.8)', // success
               'rgba(0, 123, 255, 0.8)', // primary
               'rgba(255, 193, 7, 0.8)', // warning
               'rgba(220, 53, 69, 0.8)'  // danger
            ],
            borderColor: [
               'rgba(23, 162, 184, 1)',
               'rgba(40, 167, 69, 1)',
               'rgba(0, 123, 255, 1)',
               'rgba(255, 193, 7, 1)',
               'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: {
               position: 'bottom'
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     const label = context.label || '';
                     const value = context.raw || 0;
                     const total = context.dataset.data.reduce((a, b) => a + b, 0);
                     const percentage = Math.round((value / total) * 100);
                     return `${label}: ${value} (${percentage}%)`;
                  }
               }
            }
         }
      }
   };
   const donutChart = initChart('donutKunjunganPosyandu', donutConfig);

   // Inisialisasi grafik kunjungan berdasarkan umur
   const kunjunganConfig = {
      type: 'line',
      data: {
         labels: {!! json_encode($kunjungan_posyandu['labels'] ?? []) !!},
         datasets: [
            {
               label: 'Balita (0-5)',
               data: {!! json_encode($kunjungan_posyandu['balita'] ?? []) !!},
               backgroundColor: 'rgba(23, 162, 184, 0.2)',
               borderColor: 'rgba(23, 162, 184, 1)',
               borderWidth: 2,
               tension: 0.4
            },
            {
               label: 'Pra Sekolah (6-9)',
               data: {!! json_encode($kunjungan_posyandu['pra_sekolah'] ?? []) !!},
               backgroundColor: 'rgba(40, 167, 69, 0.2)',
               borderColor: 'rgba(40, 167, 69, 1)',
               borderWidth: 2,
               tension: 0.4
            },
            {
               label: 'Remaja (10-18)',
               data: {!! json_encode($kunjungan_posyandu['remaja'] ?? []) !!},
               backgroundColor: 'rgba(0, 123, 255, 0.2)',
               borderColor: 'rgba(0, 123, 255, 1)',
               borderWidth: 2,
               tension: 0.4
            },
            {
               label: 'Produktif (19-59)',
               data: {!! json_encode($kunjungan_posyandu['produktif'] ?? []) !!},
               backgroundColor: 'rgba(255, 193, 7, 0.2)',
               borderColor: 'rgba(255, 193, 7, 1)',
               borderWidth: 2,
               tension: 0.4
            },
            {
               label: 'Lansia (>60)',
               data: {!! json_encode($kunjungan_posyandu['lansia'] ?? []) !!},
               backgroundColor: 'rgba(220, 53, 69, 0.2)',
               borderColor: 'rgba(220, 53, 69, 1)',
               borderWidth: 2,
               tension: 0.4
            }
         ]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            y: {
               beginAtZero: true,
               ticks: {
                  precision: 0
               }
            }
         },
         plugins: {
            legend: {
               position: 'bottom'
            }
         }
      }
   };
   const kunjunganChart = initChart('chartKunjunganByUmur', kunjunganConfig);

   // Inisialisasi grafik kunjungan berdasarkan posyandu
   const posyanduConfig = {
      type: 'bar',
      data: {
         labels: {!! json_encode($kunjungan_by_posyandu['labels'] ?? []) !!},
         datasets: [
            {
               label: 'Jumlah Kunjungan',
               data: {!! json_encode($kunjungan_by_posyandu['data'] ?? []) !!},
               backgroundColor: 'rgba(40, 167, 69, 0.7)',
               borderColor: 'rgba(40, 167, 69, 1)',
               borderWidth: 1,
               borderRadius: 5,
               barThickness: 25,
               maxBarThickness: 40
            }
         ]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         scales: {
            y: {
               beginAtZero: true,
               ticks: {
                  precision: 0
               }
            }
         },
         plugins: {
            legend: {
               position: 'bottom'
            }
         }
      }
   };
   const posyanduChart = initChart('chartKunjunganByPosyandu', posyanduConfig);

   // Variabel untuk melacak request AJAX yang sedang berjalan
   let ajaxRequestInProgress = false;
   let lastClickedPeriode = null;
   let activeRequests = {}; // Untuk melacak request aktif per form

   // Fungsi untuk filter periode
   $('.btn-periode').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const periode = $(this).data('periode');
      const form = $(this).closest('form');
      const formId = form.attr('id');
      const currentBtn = $(this);
      
      // Cek apakah tombol sudah aktif
      if (currentBtn.hasClass('active')) {
         console.log('Tombol sudah aktif, abaikan klik');
         return false;
      }
      
      // Cek apakah ada request yang sedang berjalan untuk form ini
      if (activeRequests[formId]) {
         console.log('Request untuk form ini sedang berjalan, abaikan klik');
         return false;
      }
      
      // Set flag bahwa request sedang berjalan untuk form ini
      activeRequests[formId] = true;
      
      // Tambahkan kelas loading dan efek visual
      form.find('.btn-periode').removeClass('active');
      currentBtn.addClass('active');
      
      // Animasi tombol
      form.find('.btn-periode').prop('disabled', true);
      
      // Tambahkan efek loading pada container grafik
      const chartContainer = $(this).closest('.card').find('.chart-container');
      chartContainer.addClass('loading-state');
      
      // Hapus loader lama jika ada
      chartContainer.find('.chart-loader').remove();
      
      // Tambahkan loader baru
      chartContainer.append('<div class="chart-loader"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>');
      
      // Tambahkan input hidden untuk periode
      if (form.find('input[name="periode"]').length) {
         form.find('input[name="periode"]').val(periode);
      } else {
         form.append('<input type="hidden" name="periode" value="' + periode + '">');
      }
      
      // Gunakan AJAX untuk memuat data tanpa refresh halaman
      $.ajax({
         url: form.attr('action'),
         type: 'GET',
         data: form.serialize() + '&ajax=true',
         dataType: 'json',
         timeout: 30000, // Timeout setelah 30 detik
         success: function(response) {
            // Update UI dengan data baru
            if (response && response.success) {
               try {
                  // Update grafik dengan animasi
                  if (response.data) {
                     if (response.data.chartKunjunganByUmur && formId === 'periodeForm') {
                        updateChartData('chartKunjunganByUmur', response.data.chartKunjunganByUmur);
                     }
                     
                     if (response.data.chartKunjunganByPosyandu && formId === 'periodeFormPosyandu') {
                        updateChartData('chartKunjunganByPosyandu', response.data.chartKunjunganByPosyandu);
                     }
                  }
                  
                  // Update informasi periode
                  const infoText = chartContainer.siblings('.text-center').find('small');
                  let newText = '';
                  
                  if (periode === 'minggu') {
                     newText = '<i class="fas fa-info-circle mr-1"></i> Data kunjungan 12 minggu terakhir dari tabel ilp_dewasa';
                  } else if (periode === 'tahun') {
                     newText = '<i class="fas fa-info-circle mr-1"></i> Data kunjungan 5 tahun terakhir dari tabel ilp_dewasa';
                  } else {
                     newText = '<i class="fas fa-info-circle mr-1"></i> Data kunjungan 6 bulan terakhir dari tabel ilp_dewasa';
                  }
                  
                  infoText.fadeOut(300, function() {
                     $(this).html(newText).fadeIn(300);
                  });
                  
                  // Update tombol dengan animasi
                  form.find('.btn-periode').each(function() {
                     const btnPeriode = $(this);
                     const btnData = btnPeriode.data('periode');
                     
                     if (btnData === periode) {
                        if (btnPeriode.hasClass('btn-outline-primary')) {
                           btnPeriode.removeClass('btn-outline-primary').addClass('btn-primary');
                        } else if (btnPeriode.hasClass('btn-outline-success')) {
                           btnPeriode.removeClass('btn-outline-success').addClass('btn-success');
                        }
                     } else {
                        if (btnPeriode.hasClass('btn-primary')) {
                           btnPeriode.removeClass('btn-primary').addClass('btn-outline-primary');
                        } else if (btnPeriode.hasClass('btn-success')) {
                           btnPeriode.removeClass('btn-success').addClass('btn-outline-success');
                        }
                     }
                  });
               } catch (error) {
                  console.error('Error saat memperbarui UI:', error);
                  alert('Terjadi kesalahan saat memperbarui tampilan. Silakan refresh halaman.');
               }
            } else {
               // Jika gagal, tampilkan pesan error
               console.error('Response error:', response);
               alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
            }
         },
         error: function(xhr, status, error) {
            // Tampilkan pesan error yang lebih informatif
            console.error("AJAX Error:", error);
            console.error("Status:", status);
            console.error("Response:", xhr.responseText);
            alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
         },
         complete: function() {
            // Reset flag bahwa request sudah selesai untuk form ini
            setTimeout(function() {
               activeRequests[formId] = false;
               
               // Hapus kelas loading
               form.find('.btn-periode').prop('disabled', false);
               chartContainer.removeClass('loading-state');
               chartContainer.find('.chart-loader').fadeOut(300, function() {
                  $(this).remove();
               });
            }, 800); // Tambahkan delay untuk mencegah klik berulang
         }
      });
      
      // Mencegah form submit normal
      return false;
   });

   // Inisialisasi grafik IMT
   const imtConfig = {
      type: 'doughnut',
      data: {
         labels: ['Kurus (< 18.5)', 'Normal (18.5 - 24.9)', 'Kelebihan BB (25 - 29.9)', 'Obesitas (≥ 30)'],
         datasets: [{
            data: [
               {{ $faktor_risiko['imt']['kurus'] }},
               {{ $faktor_risiko['imt']['normal'] }},
               {{ $faktor_risiko['imt']['kelebihan_bb'] }},
               {{ $faktor_risiko['imt']['obesitas'] }}
            ],
            backgroundColor: [
               '#17a2b8', // info
               '#28a745', // success
               '#ffc107', // warning
               '#dc3545'  // danger
            ],
            borderWidth: 1
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: {
               position: 'bottom',
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     const label = context.label || '';
                     const value = context.raw || 0;
                     const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                     const percentage = Math.round((value / total) * 100);
                     return `${label}: ${value} (${percentage}%)`;
                  }
               }
            }
         }
      }
   };
   const imtChart = initChart('chartIMT', imtConfig);

   // Inisialisasi grafik Tekanan Darah
   const tdConfig = {
      type: 'doughnut',
      data: {
         labels: [
            'Normal (< 120/80)', 
            'Pra-hipertensi (120-139/80-89)', 
            'Hipertensi 1 (140-159/90-99)', 
            'Hipertensi 2 (≥ 160/100)',
            'Hipertensi Sistolik (> 140/< 90)'
         ],
         datasets: [{
            data: [
               {{ $faktor_risiko['td']['normal'] }},
               {{ $faktor_risiko['td']['pra_hipertensi'] }},
               {{ $faktor_risiko['td']['hipertensi_1'] }},
               {{ $faktor_risiko['td']['hipertensi_2'] }},
               {{ $faktor_risiko['td']['hipertensi_sistolik'] }}
            ],
            backgroundColor: [
               '#28a745', // success
               '#ffc107', // warning
               '#fd7e14', // orange
               '#dc3545', // danger
               '#6f42c1'  // purple
            ],
            borderWidth: 1
         }]
      },
      options: {
         responsive: true,
         maintainAspectRatio: false,
         plugins: {
            legend: {
               position: 'bottom',
            },
            tooltip: {
               callbacks: {
                  label: function(context) {
                     const label = context.label || '';
                     const value = context.raw || 0;
                     const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                     const percentage = Math.round((value / total) * 100);
                     return `${label}: ${value} (${percentage}%)`;
                  }
               }
            }
         }
      }
   };
   const tdChart = initChart('chartTD', tdConfig);

   // Tambahkan CSS untuk loading state dan animasi (disederhanakan)
   $('<style>')
      .prop('type', 'text/css')
      .html(`
         .loading-state {
            position: relative;
            opacity: 0.7;
         }
         .chart-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.9);
            padding: 15px 25px;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 10;
            font-weight: bold;
            color: #007bff;
         }
         .btn-periode {
            transition: all 0.2s ease;
         }
         .btn-periode:hover {
            transform: scale(1.03);
         }
         .btn-periode:active {
            transform: scale(0.97);
         }
         
         /* Animasi untuk tombol filter dan reset */
         .btn-filter, .btn-reset {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
         }
         
         .btn-filter:hover, .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
         }
         
         .btn-filter:active, .btn-reset:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
         }
         
         .btn-filter::after, .btn-reset::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
         }
         
         .btn-filter:focus:not(:active)::after, .btn-reset:focus:not(:active)::after {
            animation: ripple 1s ease-out;
         }
         
         .select2-custom {
            transition: all 0.3s ease;
         }
         
         .select2-container--bootstrap4 .select2-selection--single:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
         }
         
         /* Animasi ripple untuk tombol */
         @keyframes ripple {
            0% {
               transform: scale(0, 0);
               opacity: 0.5;
            }
            20% {
               transform: scale(25, 25);
               opacity: 0.5;
            }
            100% {
               opacity: 0;
               transform: scale(40, 40);
            }
         }
         
         /* Animasi loading untuk tombol filter */
         .btn-filter.loading {
            pointer-events: none;
            position: relative;
            color: transparent !important;
         }
         
         .btn-filter.loading::after {
            content: '';
            position: absolute;
            width: 1rem;
            height: 1rem;
            top: calc(50% - 0.5rem);
            left: calc(50% - 0.5rem);
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            opacity: 1;
         }
         
         @keyframes spin {
            to { transform: rotate(360deg); }
         }
         
         /* Animasi untuk card filter */
         .filter-card {
            transition: all 0.3s ease;
         }
         
         .filter-card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
         }
         
         /* Animasi pulse untuk ikon */
         .pulse-animation {
            animation: pulse 2s infinite;
         }
         
         @keyframes pulse {
            0% {
               transform: scale(0.95);
               box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
            }
            
            70% {
               transform: scale(1);
               box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }
            
            100% {
               transform: scale(0.95);
               box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
         }
      `)
      .appendTo('head');
      
   // Tambahkan efek loading pada tombol filter
   $('#filterForm').on('submit', function() {
      const filterBtn = $(this).find('.btn-filter');
      const resetBtn = $(this).find('.btn-reset');
      
      // Simpan teks asli tombol
      filterBtn.data('original-text', filterBtn.html());
      
      // Tambahkan kelas loading
      filterBtn.addClass('loading');
      resetBtn.prop('disabled', true);
      
      // Tambahkan efek loading pada card
      $('.card').addClass('loading-state');
      
      // Kembalikan true untuk melanjutkan submit form
      return true;
   });
   
   // Tambahkan efek ripple pada tombol reset
   $('#resetFilter').on('click', function(e) {
      const btn = $(this);
      
      // Tambahkan kelas loading
      btn.addClass('loading');
      $('.btn-filter').prop('disabled', true);
      
      // Animasi reset select2
      $('#desa, #posyandu').val('').trigger('change');
      
      // Tambahkan delay sebelum redirect
      setTimeout(function() {
         window.location.href = "{{ route('ilp.dashboard') }}";
      }, 300);
      
      return false;
   });
});
</script>
@endsection