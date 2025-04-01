<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Partograf {{ isset($nama) ? '- '.$nama : '' }}</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
   <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
   <style>
      :root {
         --primary-color: #2c6cb8;
         --secondary-color: #f8f9fa;
         --accent-color: #4e88d1;
         --text-color: #333;
         --border-color: #dee2e6;
         --dark-border: #c8cfd6;
         --header-bg: #ebf2fa;
         --grid-highlight: #e7f3ff;
      }

      @media print {
         .no-print {
            display: none !important;
         }

         body {
            margin: 0;
            padding: 0;
            font-size: 10pt;
         }

         .page {
            width: 100%;
            height: 100%;
            border: none !important;
            box-shadow: none !important;
         }

         table,
         th,
         td {
            border: 1px solid #000;
         }

         .print-full-page {
            width: 100%;
            height: 100%;
            page-break-after: always;
         }

         .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
         }
      }

      body {
         font-family: 'Roboto', sans-serif;
         background-color: #f5f7fa;
         margin: 0;
         padding: 0;
         color: var(--text-color);
      }

      .container-fluid {
         max-width: 1200px;
         margin: 0 auto;
         padding: 20px;
      }

      .page {
         background-color: white;
         border-radius: 8px;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
         padding: 25px;
         margin-bottom: 30px;
      }

      .title-section {
         position: relative;
         text-align: center;
         padding-bottom: 15px;
         margin-bottom: 25px;
         border-bottom: 1px solid var(--border-color);
      }

      .main-title {
         font-family: 'Poppins', sans-serif;
         font-size: 24px;
         font-weight: 600;
         color: var(--primary-color);
         margin-bottom: 5px;
      }

      .subtitle {
         font-size: 14px;
         color: #6c757d;
      }

      .card {
         border-radius: 6px;
         border: 1px solid var(--border-color);
         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
         margin-bottom: 20px;
         overflow: hidden;
      }

      .card-header {
         background-color: var(--header-bg);
         padding: 12px 16px;
         border-bottom: 1px solid var(--border-color);
      }

      .card-title {
         margin: 0;
         font-size: 16px;
         font-weight: 600;
         color: var(--primary-color);
         display: flex;
         align-items: center;
      }

      .card-title i {
         margin-right: 8px;
      }

      .card-body {
         padding: 16px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 0;
      }

      table,
      th,
      td {
         border: 1px solid var(--border-color);
      }

      th,
      td {
         padding: 8px;
         text-align: center;
         font-size: 13px;
      }

      th {
         background-color: var(--header-bg);
         font-weight: 500;
      }

      .grid-container {
         display: grid;
         grid-template-columns: repeat(12, 1fr);
         gap: 1px;
      }

      .header-cell {
         background-color: var(--header-bg);
         padding: 6px;
         font-weight: 500;
         font-size: 13px;
         border: 1px solid var(--border-color);
         text-align: center;
      }

      .data-cell {
         border: 1px solid var(--border-color);
         padding: 5px;
         text-align: center;
         min-height: 25px;
      }

      .hour-cell {
         grid-column: span 1;
         background-color: var(--header-bg);
         border: 1px solid var(--border-color);
         text-align: center;
         padding: 5px;
         font-weight: 500;
         font-size: 13px;
      }

      .patient-info-card {
         display: grid;
         grid-template-columns: repeat(2, 1fr);
         gap: 10px;
         margin-bottom: 20px;
      }

      .info-item {
         display: flex;
         border: 1px solid var(--border-color);
         border-radius: 4px;
         overflow: hidden;
      }

      .info-label {
         background-color: var(--header-bg);
         padding: 10px;
         font-weight: 500;
         width: 40%;
         border-right: 1px solid var(--border-color);
         font-size: 13px;
      }

      .info-value {
         padding: 10px;
         width: 60%;
         background-color: white;
         font-size: 13px;
      }

      /* Custom styling for specific sections */
      .fetal-heart-rate-grid {
         border: 1px solid var(--border-color);
         border-radius: 4px;
         overflow: hidden;
      }

      .fhr-header {
         grid-column: span 1;
         background-color: var(--header-bg);
         border: 1px solid var(--border-color);
         padding: 6px;
         font-weight: 500;
         text-align: center;
         font-size: 13px;
      }

      .fhr-value-container {
         grid-column: span 12;
         display: grid;
         grid-template-columns: repeat(12, 1fr);
      }

      .fhr-value {
         border: 1px solid var(--border-color);
         padding: 6px;
         min-height: 26px;
         text-align: center;
         font-size: 13px;
      }

      .btn-toolbar {
         margin-bottom: 20px;
         display: flex;
         gap: 10px;
      }

      .btn-custom {
         display: flex;
         align-items: center;
         gap: 8px;
         padding: 8px 16px;
         border-radius: 4px;
         font-weight: 500;
         transition: all 0.2s;
      }

      .btn-print {
         background-color: var(--primary-color);
         border: none;
         color: white;
      }

      .btn-print:hover {
         background-color: var(--accent-color);
      }

      .btn-close {
         background-color: #f8f9fa;
         border: 1px solid #dee2e6;
         color: #6c757d;
      }

      .btn-close:hover {
         background-color: #e9ecef;
      }

      .signature-section {
         margin-top: 30px;
         text-align: right;
         padding-top: 15px;
         border-top: 1px solid var(--border-color);
      }

      .signature-title {
         font-weight: 500;
         margin-bottom: 50px;
      }

      .signature-name {
         border-top: 1px solid var(--dark-border);
         display: inline-block;
         padding-top: 5px;
         min-width: 200px;
         text-align: center;
      }

      .legend-section {
         background-color: var(--secondary-color);
         border-radius: 4px;
         padding: 12px 16px;
         margin-top: 20px;
         font-size: 13px;
         border: 1px solid var(--border-color);
      }

      .legend-title {
         font-weight: 600;
         margin-bottom: 5px;
         color: var(--primary-color);
         font-size: 14px;
      }

      .cell-highlight {
         background-color: var(--grid-highlight);
      }

      .time-label {
         font-weight: 600;
         color: var(--primary-color);
      }
   </style>
</head>

<body>
   @php
   // Mendapatkan waktu mulai partograf dari variabel yang dikirim atau gunakan waktu saat ini
   $waktu_mulai_partograf = $tanggal_partograf ?? now()->format('Y-m-d H:i:s');
   @endphp

   <div class="container-fluid">
      <div class="btn-toolbar no-print">
         <button onclick="window.print()" class="btn btn-print btn-custom">
            <i class="bi bi-printer"></i> Cetak
         </button>
         <button onclick="window.close()" class="btn btn-close btn-custom">
            <i class="bi bi-x-lg"></i> Tutup
         </button>
      </div>

      <div class="page print-full-page">
         <div class="title-section">
            <div class="main-title">PARTOGRAF</div>
            <div class="subtitle">Monitoring Kemajuan Persalinan</div>
         </div>

         <!-- Informasi Pasien -->
         <div class="card">
            <div class="card-header">
               <h5 class="card-title"><i class="bi bi-person-vcard"></i> Informasi Pasien</h5>
            </div>
            <div class="card-body">
               <div class="patient-info-card">
                  <div class="info-item">
                     <div class="info-label">Nama</div>
                     <div class="info-value">{{ $nama ?? 'N/A' }}</div>
                  </div>
                  <div class="info-item">
                     <div class="info-label">No. Rekam Medis</div>
                     <div class="info-value">{{ $no_rkm_medis ?? 'N/A' }}</div>
                  </div>
                  <div class="info-item">
                     <div class="info-label">Tanggal Partograf</div>
                     <div class="info-value">{{ isset($tanggal_partograf) ?
                        \Carbon\Carbon::parse($tanggal_partograf)->format('d-m-Y H:i') : date('d-m-Y H:i') }}</div>
                  </div>
                  <div class="info-item">
                     <div class="info-label">HPHT</div>
                     <div class="info-value">{{ $hpht ?? 'N/A' }}</div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Grafik Kemajuan Persalinan -->
         <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="card-title"><i class="bi bi-graph-up"></i> Grafik Kemajuan Persalinan</h5>
            </div>
            <div class="card-body">
               <div id="partografChart" style="height: 350px;"></div>
            </div>
         </div>

         <!-- Denyut Jantung Janin -->
         <div class="card">
            <div class="card-header">
               <h5 class="card-title"><i class="bi bi-heart-pulse"></i> Denyut Jantung Janin (DJJ)</h5>
            </div>
            <div class="card-body">
               <div class="fetal-heart-rate-grid">
                  <div class="fhr-header">Jam</div>
                  <div class="fhr-value-container">
                     @for ($i = 0; $i < 16; $i++) <div class="fhr-value time-label">
                        @if(isset($waktu_mulai_partograf))
                        {{ \Carbon\Carbon::parse($waktu_mulai_partograf)->addHours($i)->format('H:i') }}
                        @else
                        {{ $i }}
                        @endif
                  </div>
                  @endfor
               </div>

               <div class="fhr-header">180</div>
               <div class="fhr-value-container">
                  @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
                     @foreach($djjData ?? [] as $djj)
                     @if($djj['jam'] == $i && $djj['nilai'] == 180)
                     <i class="bi bi-x-lg text-danger"></i>
                     @endif
                     @endforeach
               </div>
               @endfor
            </div>

            <div class="fhr-header">170</div>
            <div class="fhr-value-container">
               @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
                  @foreach($djjData ?? [] as $djj)
                  @if($djj['jam'] == $i && $djj['nilai'] == 170)
                  <i class="bi bi-x-lg text-danger"></i>
                  @endif
                  @endforeach
            </div>
            @endfor
         </div>

         <div class="fhr-header">160</div>
         <div class="fhr-value-container">
            @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
               @foreach($djjData ?? [] as $djj)
               @if($djj['jam'] == $i && $djj['nilai'] == 160)
               <i class="bi bi-x-lg text-danger"></i>
               @endif
               @endforeach
         </div>
         @endfor
      </div>

      <div class="fhr-header">150</div>
      <div class="fhr-value-container">
         @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
            @foreach($djjData ?? [] as $djj)
            @if($djj['jam'] == $i && $djj['nilai'] == 150)
            <i class="bi bi-x-lg text-primary"></i>
            @endif
            @endforeach
      </div>
      @endfor
   </div>

   <!-- Baris khusus untuk DJJ 153 -->
   <div class="fhr-header">153</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 153)
         <i class="bi bi-x-lg text-danger"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">140</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value cell-highlight">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 140)
         <i class="bi bi-x-lg text-primary"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">130</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value cell-highlight">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 130)
         <i class="bi bi-x-lg text-primary"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <!-- Baris khusus untuk DJJ 125 -->
   <div class="fhr-header">125</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value cell-highlight">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 125)
         <i class="bi bi-x-lg text-success"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">120</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value cell-highlight">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 120)
         <i class="bi bi-x-lg text-primary"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">110</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 110)
         <i class="bi bi-x-lg text-primary"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">100</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 100)
         <i class="bi bi-x-lg text-danger"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">90</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 90)
         <i class="bi bi-x-lg text-danger"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>

   <div class="fhr-header">80</div>
   <div class="fhr-value-container">
      @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
         @foreach($djjData ?? [] as $djj)
         @if($djj['jam'] == $i && $djj['nilai'] == 80)
         <i class="bi bi-x-lg text-danger"></i>
         @endif
         @endforeach
   </div>
   @endfor
   </div>
   </div>
   </div>
   </div>

   <!-- Dilatasi Serviks -->
   <div class="card">
      <div class="card-header">
         <h5 class="card-title"><i class="bi bi-circle"></i> Dilatasi Serviks (cm)</h5>
      </div>
      <div class="card-body">
         <div class="position-relative">
            <!-- Overlay untuk garis waspada dan bertindak -->
            <div
               style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;">
               <!-- Garis waspada -->
               <div
                  style="position: absolute; top: 0; left: 30%; width: 2px; height: 100%; background-color: #FF9800; z-index: 2;">
                  <span
                     style="position: absolute; top: 10px; left: -30px; transform: rotate(-45deg); color: #FF9800; font-weight: bold;">WASPADA</span>
               </div>
               <!-- Garis bertindak -->
               <div
                  style="position: absolute; top: 0; left: 60%; width: 2px; height: 100%; background-color: #F44336; z-index: 2;">
                  <span
                     style="position: absolute; top: 10px; left: -33px; transform: rotate(-45deg); color: #F44336; font-weight: bold;">BERTINDAK</span>
               </div>
            </div>

            <div class="fetal-heart-rate-grid">
               <div class="fhr-header">Jam</div>
               <div class="fhr-value-container">
                  @for ($i = 0; $i < 16; $i++) <div class="fhr-value time-label">
                     @if(isset($waktu_mulai_partograf))
                     {{ \Carbon\Carbon::parse($waktu_mulai_partograf)->addHours($i)->format('H:i') }}
                     @else
                     {{ $i }}
                     @endif
               </div>
               @endfor
            </div>

            <!-- Label untuk air ketuban/penyusupan -->
            <div class="fhr-header">Air ketuban Penyusupan</div>
            <div class="fhr-value-container">
               @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
                  @foreach($ketubanData ?? [] as $ketuban)
                  @if($ketuban['jam'] == $i)
                  <span>{{ $ketuban['kode'] ?? '' }}</span>
                  @endif
                  @endforeach
            </div>
            @endfor
         </div>
      </div>

      <!-- Baris-baris untuk nilai dilatasi -->
      @for ($nilai = 10; $nilai >= 0; $nilai--)
      <div class="fhr-header">{{ $nilai }}</div>
      <div class="fhr-value-container">
         @for ($i = 0; $i < 16; $i++) <div class="fhr-value">
            @foreach($dilatasiData ?? [] as $dilatasi)
            @if($dilatasi['jam'] == $i && $dilatasi['nilai'] == $nilai)
            <i class="bi bi-x-lg text-success"></i>
            @endif
            @endforeach
      </div>
      @endfor
   </div>
   @endfor
   </div>
   </div>
   </div>

   <!-- Kontraksi per 10 menit -->
   <div class="card">
      <div class="card-header">
         <h5 class="card-title"><i class="bi bi-activity"></i> Kontraksi per 10 menit</h5>
      </div>
      <div class="card-body">
         <div class="fetal-heart-rate-grid">
            <div class="fhr-header">Jam</div>
            <div class="fhr-value-container">
               @for ($i = 0; $i < 16; $i++) <div class="fhr-value time-label">
                  @if(isset($waktu_mulai_partograf))
                  {{ \Carbon\Carbon::parse($waktu_mulai_partograf)->addHours($i)->format('H:i') }}
                  @else
                  {{ $i }}
                  @endif
            </div>
            @endfor
         </div>

         <!-- Baris-baris untuk nilai kontraksi -->
         @for ($nilai = 5; $nilai >= 0; $nilai--)
         <div class="fhr-header">{{ $nilai }}</div>
         <div class="fhr-value-container">
            @for ($i = 0; $i < 16; $i++) <div
               class="fhr-value {{ ($nilai >= 3 && $nilai <= 4) ? 'cell-highlight' : '' }}">
               @foreach($kontraksiData ?? [] as $kontraksi)
               @if($kontraksi['jam'] == $i && $kontraksi['nilai'] == $nilai)
               <span class="badge bg-primary">{{ $kontraksi['durasi'] ?? '' }}</span>
               @endif
               @endforeach
         </div>
         @endfor
      </div>
      @endfor
   </div>
   </div>
   </div>

   <!-- Pengukuran Vital dan Data Lainnya -->
   <div class="card">
      <div class="card-header">
         <h5 class="card-title"><i class="bi bi-clipboard2-pulse"></i> Data Vital dan Catatan</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th>Jam</th>
                  <th>Tekanan Darah</th>
                  <th>Nadi</th>
                  <th>Suhu</th>
                  <th>Cairan Ketuban</th>
                  <th>Urine (ml)</th>
                  <th>Obat &amp; Dosis</th>
               </tr>
            </thead>
            <tbody>
               @for ($i = 0; $i < 16; $i++) <tr>
                  <td class="time-label">
                     @if(isset($waktu_mulai_partograf))
                     {{ \Carbon\Carbon::parse($waktu_mulai_partograf)->addHours($i)->format('H:i') }}
                     @else
                     {{ $i }}
                     @endif
                  </td>
                  <td>
                     @foreach($tensiData ?? [] as $tensi)
                     @if($tensi['jam'] == $i)
                     <span
                        class="badge bg-{{ ($tensi['sistole'] > 140 || $tensi['diastole'] > 90) ? 'danger' : 'success' }}">
                        {{ $tensi['sistole'] ?? '' }}/{{ $tensi['diastole'] ?? '' }}
                     </span>
                     @endif
                     @endforeach
                  </td>
                  <td>
                     @foreach($nadiData ?? [] as $nadi)
                     @if($nadi['jam'] == $i)
                     {{ $nadi['nilai'] ?? '' }}
                     @endif
                     @endforeach
                  </td>
                  <td>
                     @foreach($suhuData ?? [] as $suhu)
                     @if($suhu['jam'] == $i)
                     <span class="badge bg-{{ ($suhu['nilai'] > 37.5) ? 'warning' : 'info' }}">
                        {{ $suhu['nilai'] ?? '' }}
                     </span>
                     @endif
                     @endforeach
                  </td>
                  <td>
                     @foreach($ketubanData ?? [] as $ketuban)
                     @if($ketuban['jam'] == $i)
                     <span class="badge bg-secondary">{{ $ketuban['kode'] ?? '' }}</span>
                     @endif
                     @endforeach
                  </td>
                  <td>
                     @foreach($volumeData ?? [] as $volume)
                     @if($volume['jam'] == $i)
                     {{ $volume['nilai'] ?? '' }}
                     @endif
                     @endforeach
                  </td>
                  <td>
                     @foreach($obatData ?? [] as $obat)
                     @if($obat['jam'] == $i)
                     {{ $obat['detail'] ?? '' }}
                     @endif
                     @endforeach
                  </td>
                  </tr>
                  @endfor
            </tbody>
         </table>
      </div>
   </div>

   <div class="legend-section">
      <div class="legend-title">Keterangan:</div>
      <div class="row">
         <div class="col-md-6">
            <p class="mb-1"><i class="bi bi-droplet-fill text-primary me-1"></i> <strong>Kondisi Cairan
                  Ketuban:</strong>
               I (Selaput Utuh), J (Jernih), M (Mekonium), D (Darah), K (Kering)</p>
            <p class="mb-1"><i class="bi bi-x-lg text-primary me-1"></i> <strong>Tanda pada grafik:</strong>
               Pengukuran parameter</p>
            <p class="mb-1" style="color: #28a745;"><i class="bi bi-circle-fill me-1"></i> <strong>Penurunan
                  Kepala:</strong>
               Posisi kepala janin terhadap spina ischiadica</p>
         </div>
         <div class="col-md-6">
            <p class="mb-1" style="color: #FF9800;"><i class="bi bi-dash me-1"></i> <strong>Garis Waspada:</strong>
               Dimulai dari pembukaan 4 cm, meningkat 1 cm/jam</p>
            <p class="mb-1" style="color: #F44336;"><i class="bi bi-dash me-1"></i> <strong>Garis Bertindak:</strong>
               4 jam di sebelah kanan garis waspada</p>
         </div>
      </div>
   </div>

   <div class="signature-section">
      <div class="signature-title">Petugas Pemeriksa</div>
      <div class="signature-name">(Nama &amp; Tanda Tangan)</div>
   </div>
   </div>
   </div>

   <script>
      document.addEventListener('DOMContentLoaded', function() {
         // Data dari controller
         const grafikData = @json($grafikData ?? null);
         
         if (!grafikData) {
            document.getElementById('partografChart').innerHTML = '<div class="alert alert-warning">Data grafik kemajuan persalinan belum tersedia</div>';
            return;
         }
         
         // Siapkan data untuk chart
         const waktuLabels = grafikData.waktu || [];
         const pembukaanData = grafikData.pembukaan || [];
         const penurunanData = grafikData.penurunan || [];
         
         // Membuat data untuk garis waspada dan bertindak
         let garisWaspadaData = [];
         let garisBertindakData = [];
         
         // Garis waspada dimulai dari 4 cm pada jam ke-0 dan naik 1 cm per jam
         for (let i = 0; i < 16; i++) {
            if (i < 10) { // Batas 10 jam untuk garis waspada
               garisWaspadaData.push(Math.min(4 + i, 10)); // Maksimal 10 cm
            } else {
               garisWaspadaData.push(null); // Tidak ada nilai setelah 10 jam
            }
         }
         
         // Garis bertindak dimulai dari 4 cm pada jam ke-4 (4 jam setelah garis waspada)
         for (let i = 0; i < 16; i++) {
            if (i < 4) {
               garisBertindakData.push(null); // Belum ada nilai di 4 jam pertama
            } else if (i < 14) { // Batas 10 jam untuk garis bertindak setelah mulai
               garisBertindakData.push(Math.min(4 + (i - 4), 10)); // Maksimal 10 cm
            } else {
               garisBertindakData.push(null);
            }
         }
         
         // Konfigurasi chart
         const options = {
            chart: {
               height: 350,
               type: 'line',
               toolbar: {
                  show: true
               },
               fontFamily: 'Roboto, sans-serif',
            },
            stroke: {
               width: [3, 3, 2, 2],
               curve: 'straight',
               dashArray: [0, 0, 0, 0]
            },
            colors: ['#1a73e8', '#28a745', '#FF9800', '#F44336'], // Warna: pembukaan, penurunan (hijau), waspada, bertindak
            series: [
               {
                  name: 'Pembukaan (cm)',
                  data: pembukaanData
               },
               {
                  name: 'Penurunan Kepala',
                  data: penurunanData
               },
               {
                  name: 'Garis Waspada',
                  data: garisWaspadaData
               },
               {
                  name: 'Garis Bertindak',
                  data: garisBertindakData
               }
            ],
            xaxis: {
               categories: waktuLabels,
               title: {
                  text: 'Waktu Pemeriksaan'
               }
            },
            yaxis: [
               {
                  title: {
                     text: 'Pembukaan (cm)',
                     style: {
                        color: '#1a73e8'
                     }
                  },
                  min: 0,
                  max: 10,
                  reversed: false
               },
               {
                  opposite: true,
                  title: {
                     text: 'Penurunan Kepala',
                     style: {
                        color: '#28a745'
                     }
                  },
                  min: 0,
                  max: 5,
                  reversed: true
               }
            ],
            markers: {
               size: 5,
               colors: ['#1a73e8', '#28a745', '#FF9800', '#F44336'], // Warna titik-titik marker sesuai dengan warna garis
               strokeColors: '#fff',
               strokeWidth: 2
            },
            legend: {
               position: 'top',
               labels: {
                  colors: ['#1a73e8', '#28a745', '#FF9800', '#F44336'] // Warna label pada legenda
               }
            },
            grid: {
               borderColor: '#e7e7e7',
               row: {
                  colors: ['#f5f7fa', 'transparent'],
                  opacity: 0.5
               },
            },
            tooltip: {
               y: {
                  formatter: function(val, { seriesIndex }) {
                     if (seriesIndex === 0) {
                        return val + ' cm';
                     } else if (seriesIndex === 1) {
                        return 'Stasiun ' + val;
                     } else {
                        return val + ' cm';
                     }
                  }
               }
            }
         };
         
         // Render chart
         const chart = new ApexCharts(document.getElementById('partografChart'), options);
         chart.render();
      });
   </script>
</body>

</html>