<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Hasil Pemeriksaan ILP</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 12px;
         line-height: 1.5;
         color: #333;
         margin: 0;
         padding: 20px;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
         border-bottom: 2px solid #4e73df;
         padding-bottom: 10px;
      }

      .header h1 {
         font-size: 18px;
         color: #4e73df;
         margin: 0;
         padding: 0;
      }

      .header p {
         font-size: 12px;
         margin: 5px 0;
      }

      .patient-info {
         background-color: #f8f9fc;
         padding: 10px;
         border-radius: 5px;
         margin-bottom: 20px;
      }

      .patient-info table {
         width: 100%;
      }

      .patient-info td {
         padding: 3px 0;
      }

      .section {
         margin-bottom: 20px;
      }

      .section-title {
         font-size: 14px;
         font-weight: bold;
         color: #4e73df;
         border-bottom: 1px solid #e3e6f0;
         padding-bottom: 5px;
         margin-bottom: 10px;
      }

      table.data {
         width: 100%;
         border-collapse: collapse;
      }

      table.data th,
      table.data td {
         padding: 5px;
         border: 1px solid #e3e6f0;
      }

      table.data th {
         background-color: #f8f9fc;
         text-align: left;
         font-weight: bold;
      }

      .footer {
         margin-top: 30px;
         text-align: right;
         font-size: 12px;
      }

      .footer p {
         margin: 5px 0;
      }

      .col-2 {
         width: 50%;
         float: left;
      }

      .clearfix::after {
         content: "";
         clear: both;
         display: table;
      }
   </style>
</head>

<body>
   <div class="header">
      <h1>HASIL PEMERIKSAAN KESEHATAN ILP</h1>
      <p>Tanggal Pemeriksaan: {{ $tanggal }}</p>
   </div>

   <div class="patient-info">
      <table>
         <tr>
            <td width="15%"><strong>Nama</strong></td>
            <td width="35%">: {{ $pasien->nm_pasien ?? '-' }}</td>
            <td width="15%"><strong>No. RM</strong></td>
            <td width="35%">: {{ $pasien->no_rkm_medis ?? '-' }}</td>
         </tr>
         <tr>
            <td><strong>Tanggal Lahir</strong></td>
            <td>: {{ isset($pasien->tgl_lahir) ? date('d-m-Y', strtotime($pasien->tgl_lahir)) : '-' }}</td>
            <td><strong>Jenis Kelamin</strong></td>
            <td>: {{ $pasien->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
         </tr>
         <tr>
            <td><strong>Alamat</strong></td>
            <td colspan="3">: {{ $pasien->alamat ?? '-' }}</td>
         </tr>
      </table>
   </div>

   <div class="section">
      <div class="section-title">HASIL PEMERIKSAAN FISIK</div>
      <div class="clearfix">
         <div class="col-2">
            <table class="data">
               <tr>
                  <th width="40%">Parameter</th>
                  <th width="30%">Hasil</th>
                  <th width="30%">Nilai Normal</th>
               </tr>
               <tr>
                  <td>Berat Badan</td>
                  <td>{{ $pemeriksaan->berat_badan ?? '-' }} kg</td>
                  <td>-</td>
               </tr>
               <tr>
                  <td>Tinggi Badan</td>
                  <td>{{ $pemeriksaan->tinggi_badan ?? '-' }} cm</td>
                  <td>-</td>
               </tr>
               <tr>
                  <td>IMT</td>
                  <td>{{ $pemeriksaan->imt ?? '-' }}</td>
                  <td>18.5 - 24.9</td>
               </tr>
               <tr>
                  <td>Tekanan Darah</td>
                  <td>{{ $pemeriksaan->td ?? '-' }} mmHg</td>
                  <td>
                     < 120/80 mmHg</td>
               </tr>
            </table>
         </div>
         <div class="col-2">
            <table class="data">
               <tr>
                  <th width="40%">Parameter</th>
                  <th width="30%">Hasil</th>
                  <th width="30%">Nilai Normal</th>
               </tr>
               <tr>
                  <td>Lingkar Perut</td>
                  <td>{{ $pemeriksaan->lp ?? '-' }} cm</td>
                  <td>
                     < 90 cm (P), < 80 cm (W)</td>
               </tr>
               <tr>
                  <td>Merokok</td>
                  <td>{{ $pemeriksaan->merokok ?? '-' }}</td>
                  <td>-</td>
               </tr>
               <tr>
                  <td>Konsumsi Tinggi</td>
                  <td>{{ $pemeriksaan->konsumsi_tinggi ?? '-' }}</td>
                  <td>-</td>
               </tr>
            </table>
         </div>
      </div>
   </div>

   @if(isset($pemeriksaan->gula_darah) || isset($pemeriksaan->kolesterol) || isset($pemeriksaan->asam_urat))
   <div class="section">
      <div class="section-title">HASIL PEMERIKSAAN LABORATORIUM</div>
      <table class="data">
         <tr>
            <th width="30%">Parameter</th>
            <th width="20%">Hasil</th>
            <th width="25%">Nilai Normal</th>
            <th width="25%">Interpretasi</th>
         </tr>
         @if(isset($pemeriksaan->gula_darah))
         <tr>
            <td>Gula Darah</td>
            <td>{{ $pemeriksaan->gula_darah }} mg/dL</td>
            <td>70-140 mg/dL</td>
            <td>
               @php
               $gd = floatval($pemeriksaan->gula_darah);
               if ($gd < 70) $interpretasi='Rendah' ; else if ($gd> 140) $interpretasi = 'Tinggi';
                  else $interpretasi = 'Normal';
                  @endphp
                  {{ $interpretasi }}
            </td>
         </tr>
         @endif
         @if(isset($pemeriksaan->kolesterol))
         <tr>
            <td>Kolesterol Total</td>
            <td>{{ $pemeriksaan->kolesterol }} mg/dL</td>
            <td>
               < 200 mg/dL</td>
            <td>
               @php
               $kol = floatval($pemeriksaan->kolesterol);
               if ($kol < 200) $interpretasi='Normal' ; else if ($kol>= 200 && $kol < 240) $interpretasi='Batas Tinggi'
                     ; else $interpretasi='Tinggi' ; @endphp {{ $interpretasi }} </td>
         </tr>
         @endif
         @if(isset($pemeriksaan->asam_urat))
         <tr>
            <td>Asam Urat</td>
            <td>{{ $pemeriksaan->asam_urat }} mg/dL</td>
            <td>3.5-7.2 mg/dL (P), 2.6-6.0 mg/dL (W)</td>
            <td>
               @php
               $au = floatval($pemeriksaan->asam_urat);
               $jk = $pasien->jk ?? 'L';

               if ($jk == 'L') {
               if ($au < 3.5) $interpretasi='Rendah' ; else if ($au> 7.2) $interpretasi = 'Tinggi';
                  else $interpretasi = 'Normal';
                  } else {
                  if ($au < 2.6) $interpretasi='Rendah' ; else if ($au> 6.0) $interpretasi = 'Tinggi';
                     else $interpretasi = 'Normal';
                     }
                     @endphp
                     {{ $interpretasi }}
            </td>
         </tr>
         @endif
         @if(isset($pemeriksaan->trigliserida))
         <tr>
            <td>Trigliserida</td>
            <td>{{ $pemeriksaan->trigliserida }} mg/dL</td>
            <td>
               < 150 mg/dL</td>
            <td>
               @php
               $tg = floatval($pemeriksaan->trigliserida);
               if ($tg < 150) $interpretasi='Normal' ; else if ($tg>= 150 && $tg < 200) $interpretasi='Batas Tinggi' ;
                     else $interpretasi='Tinggi' ; @endphp {{ $interpretasi }} </td>
         </tr>
         @endif
         @if(isset($pemeriksaan->ureum))
         <tr>
            <td>Ureum</td>
            <td>{{ $pemeriksaan->ureum }} mg/dL</td>
            <td>15-43 mg/dL</td>
            <td>
               @php
               $ureum = floatval($pemeriksaan->ureum);
               if ($ureum < 15) $interpretasi='Rendah' ; else if ($ureum> 43) $interpretasi = 'Tinggi';
                  else $interpretasi = 'Normal';
                  @endphp
                  {{ $interpretasi }}
            </td>
         </tr>
         @endif
         @if(isset($pemeriksaan->kreatinin))
         <tr>
            <td>Kreatinin</td>
            <td>{{ $pemeriksaan->kreatinin }} mg/dL</td>
            <td>0.7-1.2 mg/dL</td>
            <td>
               @php
               $kreatinin = floatval($pemeriksaan->kreatinin);
               if ($kreatinin < 0.7) $interpretasi='Rendah' ; else if ($kreatinin> 1.2) $interpretasi = 'Tinggi';
                  else $interpretasi = 'Normal';
                  @endphp
                  {{ $interpretasi }}
            </td>
         </tr>
         @endif
      </table>
   </div>
   @endif

   <div class="section">
      <div class="section-title">PEMERIKSAAN LAINNYA</div>
      <div class="clearfix">
         <div class="col-2">
            <table class="data">
               <tr>
                  <th colspan="2">Pemeriksaan Mata</th>
               </tr>
               <tr>
                  <td width="40%">Metode</td>
                  <td width="60%">{{ $pemeriksaan->metode_mata ?? '-' }}</td>
               </tr>
               <tr>
                  <td>Hasil</td>
                  <td>{{ $pemeriksaan->hasil_mata ?? '-' }}</td>
               </tr>
            </table>
            <br>
            <table class="data">
               <tr>
                  <th colspan="2">Pemeriksaan Pendengaran</th>
               </tr>
               <tr>
                  <td width="40%">Tes Berbisik</td>
                  <td width="60%">{{ $pemeriksaan->tes_berbisik ?? '-' }}</td>
               </tr>
            </table>
         </div>
         <div class="col-2">
            <table class="data">
               <tr>
                  <th colspan="2">Pemeriksaan Gigi</th>
               </tr>
               <tr>
                  <td width="40%">Kondisi</td>
                  <td width="60%">{{ $pemeriksaan->gigi ?? '-' }}</td>
               </tr>
            </table>
            <br>
            <table class="data">
               <tr>
                  <th colspan="2">Kesehatan Jiwa</th>
               </tr>
               <tr>
                  <td width="40%">Kondisi</td>
                  <td width="60%">{{ $pemeriksaan->kesehatan_jiwa ?? '-' }}</td>
               </tr>
            </table>
         </div>
      </div>
   </div>

   @if(isset($pemeriksaan->skilas) && !empty($pemeriksaan->skilas))
   <div class="section">
      <div class="section-title">KESIMPULAN</div>
      <p>{{ $pemeriksaan->skilas }}</p>
   </div>
   @endif

   <div class="footer">
      <p>Dokter Pemeriksa,</p>
      <br><br><br>
      <p><strong>{{ $pemeriksaan->nama_dokter ?? 'dr. Dokter Pemeriksa' }}</strong></p>
   </div>
</body>

</html>