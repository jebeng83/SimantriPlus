@extends('adminlte::page')

@section('title', 'Faktor Resiko ILP')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
   <div>
      <h1 class="m-0 text-dark"><i class="fas fa-flask mr-2"></i>Faktor Resiko ILP</h1>
   </div>
</div>
@stop

@section('content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark"><i class="fas fa-chart-line mr-2"></i>Losss</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
               <li class="breadcrumb-item active">Faktor Resiko ILP</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<section class="content">
   <div class="container-fluid">
      <!-- Filter Section -->
      <div class="card card-primary card-outline">
         <div class="card-header">
            <h3 class="card-title">
               <i class="fas fa-filter mr-1"></i>
               Filter Data
            </h3>
         </div>
         <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('ilp.faktor-resiko') }}">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Desa/Kelurahan:</label>
                        <select class="form-control" name="desa" id="desa">
                           <option value="">Semua Desa</option>
                           @foreach($daftar_desa as $desa)
                           <option value="{{ $desa->desa }}" {{ $desa_filter==$desa->desa ? 'selected' : '' }}>
                              {{ $desa->desa }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Posyandu:</label>
                        <select class="form-control" name="posyandu" id="posyandu">
                           <option value="">Semua Posyandu</option>
                           @foreach($daftar_posyandu as $posyandu)
                           <option value="{{ $posyandu->nama_posyandu }}" {{ $posyandu_filter==$posyandu->nama_posyandu
                              ? 'selected' : '' }}>
                              {{ $posyandu->nama_posyandu }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Periode:</label>
                        <select class="form-control" name="periode" id="periode">
                           <option value="bulan" {{ $periode_filter=='bulan' ? 'selected' : '' }}>6 Bulan Terakhir
                           </option>
                           <option value="minggu" {{ $periode_filter=='minggu' ? 'selected' : '' }}>12 Minggu Terakhir
                           </option>
                           <option value="tahun" {{ $periode_filter=='tahun' ? 'selected' : '' }}>5 Tahun Terakhir
                           </option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Terapkan Filter
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>

      <!-- Hasil Laboratorium Section -->
      <div class="row">
         <div class="col-md-12">
            <div class="card card-primary card-outline">
               <div class="card-header">
                  <h3 class="card-title">
                     <i class="fas fa-flask mr-1"></i>
                     Hasil Pemeriksaan Laboratorium
                  </h3>
               </div>
               <div class="card-body">
                  <div class="row">
                     <!-- Hemoglobin Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Hemoglobin</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="hemoglobinChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong> 12-16 g/dL</li>
                                    <li><strong>Rendah (Anemia):</strong>
                                       < 12 g/dL</li>
                                    <li><strong>Tinggi (Polisitemia):</strong> > 16 g/dL</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Kolesterol Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Kolesterol Total</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="kolesterolChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong>
                                       < 200 mg/dL</li>
                                    <li><strong>Batas Tinggi:</strong> 200-239 mg/dL</li>
                                    <li><strong>Tinggi:</strong> ≥ 240 mg/dL</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row mt-4">
                     <!-- Asam Urat Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Asam Urat</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="asamUratChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong> 3.5-7.2 mg/dL</li>
                                    <li><strong>Rendah:</strong>
                                       < 3.5 mg/dL</li>
                                    <li><strong>Tinggi (Hiperurisemia):</strong> > 7.2 mg/dL</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Gula Darah Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Gula Darah Puasa</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="gulaDarahChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong> 70-100 mg/dL</li>
                                    <li><strong>Prediabetes:</strong> 100-125 mg/dL</li>
                                    <li><strong>Diabetes:</strong> ≥ 126 mg/dL</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row mt-4">
                     <!-- Trigliserida Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Trigliserida</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="trigliseridaChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong>
                                       < 150 mg/dL</li>
                                    <li><strong>Batas Tinggi:</strong> 150-199 mg/dL</li>
                                    <li><strong>Tinggi:</strong> 200-499 mg/dL</li>
                                    <li><strong>Sangat Tinggi:</strong> ≥ 500 mg/dL</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- HbA1c Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">HbA1c</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="hba1cChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong>
                                       < 5.7%</li>
                                    <li><strong>Prediabetes:</strong> 5.7-6.4%</li>
                                    <li><strong>Diabetes:</strong> ≥ 6.5%</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Faktor Resiko Tambahan Section -->
      <div class="row mt-4">
         <div class="col-md-12">
            <div class="card card-primary card-outline">
               <div class="card-header">
                  <h3 class="card-title">
                     <i class="fas fa-exclamation-triangle mr-1"></i>
                     Faktor Resiko Tambahan
                  </h3>
               </div>
               <div class="card-body">
                  <div class="row">
                     <!-- IMT Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Indeks Massa Tubuh (IMT)</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="imtChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Kurus:</strong>
                                       < 18.5</li>
                                    <li><strong>Normal:</strong> 18.5-24.9</li>
                                    <li><strong>Kelebihan BB:</strong> 25-29.9</li>
                                    <li><strong>Obesitas:</strong> ≥ 30</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Tekanan Darah Chart -->
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-header bg-light">
                              <h3 class="card-title">Tekanan Darah</h3>
                              <div class="card-tools">
                                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="card-body">
                              <div class="chart-container">
                                 <canvas id="tekananDarahChart"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                              <div class="mt-3">
                                 <h5>Interpretasi:</h5>
                                 <ul>
                                    <li><strong>Normal:</strong>
                                       < 120 dan < 80</li>
                                    <li><strong>Pra-hipertensi:</strong> 120-139 atau 80-89</li>
                                    <li><strong>Hipertensi 1:</strong> 140-159 atau 90-99</li>
                                    <li><strong>Hipertensi 2:</strong> ≥ 160 atau ≥ 100</li>
                                    <li><strong>Hipertensi Sistolik Terisolasi:</strong> > 140 dan < 90</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   $(function() {
        // Fungsi untuk membuat chart
        function createChart(canvasId, chartData, chartType = 'bar') {
            const ctx = document.getElementById(canvasId).getContext('2d');
            
            return new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Data untuk chart Hemoglobin
        const hemoglobinData = {
            labels: ['Rendah', 'Normal', 'Tinggi'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $hemoglobin['rendah'] ?? 0 }}, {{ $hemoglobin['normal'] ?? 0 }}, {{ $hemoglobin['tinggi'] ?? 0 }}],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 159, 64)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk chart Kolesterol
        const kolesterolData = {
            labels: ['Normal', 'Batas Tinggi', 'Tinggi'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $kolesterol['normal'] ?? 0 }}, {{ $kolesterol['batas_tinggi'] ?? 0 }}, {{ $kolesterol['tinggi'] ?? 0 }}],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk chart Asam Urat
        const asamUratData = {
            labels: ['Rendah', 'Normal', 'Tinggi'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $asam_urat['rendah'] ?? 0 }}, {{ $asam_urat['normal'] ?? 0 }}, {{ $asam_urat['tinggi'] ?? 0 }}],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 159, 64, 0.5)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 159, 64)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk chart Gula Darah
        const gulaDarahData = {
            labels: ['Normal', 'Prediabetes', 'Diabetes'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $gula_darah['normal'] ?? 0 }}, {{ $gula_darah['prediabetes'] ?? 0 }}, {{ $gula_darah['diabetes'] ?? 0 }}],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk chart Trigliserida
        const trigliseridaData = {
            labels: ['Normal', 'Batas Tinggi', 'Tinggi', 'Sangat Tinggi'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [
                    {{ $trigliserida['normal'] ?? 0 }}, 
                    {{ $trigliserida['batas_tinggi'] ?? 0 }}, 
                    {{ $trigliserida['tinggi'] ?? 0 }},
                    {{ $trigliserida['sangat_tinggi'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 99, 132)'
                ],
                borderWidth: 1
            }]
        };

        // Data untuk chart HbA1c
        const hba1cData = {
            labels: ['Normal', 'Prediabetes', 'Diabetes'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $hba1c['normal'] ?? 0 }}, {{ $hba1c['prediabetes'] ?? 0 }}, {{ $hba1c['diabetes'] ?? 0 }}],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)'
                ],
                borderWidth: 1
            }]
        };

        // Membuat chart
        createChart('hemoglobinChart', hemoglobinData, 'pie');
        createChart('kolesterolChart', kolesterolData, 'pie');
        createChart('asamUratChart', asamUratData, 'pie');
        createChart('gulaDarahChart', gulaDarahData, 'pie');
        createChart('trigliseridaChart', trigliseridaData, 'pie');
        createChart('hba1cChart', hba1cData, 'pie');

        // Event handler untuk filter desa
        $('#desa').change(function() {
            const desa = $(this).val();
            
            // Reset posyandu dropdown
            $('#posyandu').empty().append('<option value="">Semua Posyandu</option>');
            
            if (desa) {
                // Ambil data posyandu berdasarkan desa
                $.ajax({
                    url: "{{ route('ilp.get-posyandu') }}",
                    type: "GET",
                    data: { desa: desa },
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#posyandu').append('<option value="' + value.nama_posyandu + '">' + value.nama_posyandu + '</option>');
                        });
                    }
                });
            } else {
                // Jika tidak ada desa yang dipilih, ambil semua posyandu
                $.ajax({
                    url: "{{ route('ilp.get-posyandu') }}",
                    type: "GET",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#posyandu').append('<option value="' + value.nama_posyandu + '">' + value.nama_posyandu + '</option>');
                        });
                    }
                });
            }
        });

        // Grafik IMT
        const imtData = {
            labels: ['Kurus', 'Normal', 'Kelebihan BB', 'Obesitas'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [{{ $imt['kurus'] ?? 0 }}, {{ $imt['normal'] ?? 0 }}, {{ $imt['kelebihan_bb'] ?? 0 }}, {{ $imt['obesitas'] ?? 0 }}],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };
        createChart('imtChart', imtData, 'bar');

        // Grafik Tekanan Darah
        const tekananDarahData = {
            labels: ['Normal', 'Pra-hipertensi', 'Hipertensi 1', 'Hipertensi 2', 'Hipertensi Sistolik Terisolasi'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [
                    {{ $tekanan_darah['normal'] ?? 0 }}, 
                    {{ $tekanan_darah['prahipertensi'] ?? 0 }}, 
                    {{ $tekanan_darah['hipertensi1'] ?? 0 }}, 
                    {{ $tekanan_darah['hipertensi2'] ?? 0 }}, 
                    {{ $tekanan_darah['hst'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };
        createChart('tekananDarahChart', tekananDarahData, 'bar');
    });
</script>
@stop