<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <title>Hasil Pemeriksaan ILP - {{ $nama_pasien }}</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 12px;
         line-height: 1.4;
         color: #333;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
         border-bottom: 2px solid #333;
         padding-bottom: 10px;
      }

      .header h2,
      .header h3 {
         margin: 5px 0;
      }

      .info-container {
         width: 100%;
         margin-bottom: 20px;
      }

      .info-table {
         width: 100%;
         border-collapse: collapse;
      }

      .info-table td {
         padding: 5px;
         vertical-align: top;
      }

      .info-table .label {
         font-weight: bold;
         width: 180px;
      }

      .section {
         margin-bottom: 20px;
      }

      .section-title {
         font-size: 14px;
         font-weight: bold;
         margin-bottom: 10px;
         border-bottom: 1px solid #ccc;
         padding-bottom: 5px;
      }

      .result-table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 20px;
      }

      .result-table th,
      .result-table td {
         padding: 8px;
         border: 1px solid #ddd;
         text-align: left;
      }

      .result-table th {
         background-color: #f2f2f2;
         font-weight: bold;
      }

      .result-table tr:nth-child(even) {
         background-color: #f9f9f9;
      }

      .footer {
         margin-top: 50px;
         text-align: right;
         padding-top: 20px;
      }

      .divider {
         border-top: 1px solid #ddd;
         margin: 20px 0;
      }

      .status {
         display: inline-block;
         padding: 3px 8px;
         border-radius: 3px;
         font-weight: bold;
      }

      .status-menunggu {
         background-color: #ffeeba;
         color: #856404;
      }

      .status-proses {
         background-color: #b8daff;
         color: #004085;
      }

      .status-selesai {
         background-color: #c3e6cb;
         color: #155724;
      }

      .status-diambil {
         background-color: #d6d8db;
         color: #383d41;
      }

      .two-columns {
         display: flex;
         width: 100%;
      }

      .column {
         width: 50%;
      }
   </style>
</head>

<body>
   <div class="header">
      <h2>RAPORT PEMERIKSAAN ILP POSYANDU</h2>
      <h3>POSYANDU - {{ $data_posyandu }}</h3>
   </div>

   <div class="section">
      <div class="section-title">Informasi Pasien</div>
      <table class="info-table">
         <tr>
            <td class="label">No. Rawat</td>
            <td>: {{ $no_rawat }}</td>
            <td class="label">Tanggal Pemeriksaan</td>
            <td>: {{ $tanggal }}</td>
         </tr>
         <tr>
            <td class="label">No. RM</td>
            <td>: {{ $no_rm }}</td>
            <td class="label">Jenis Kelamin</td>
            <td>: {{ $jk }}</td>
         </tr>
         <tr>
            <td class="label">Nama Pasien</td>
            <td>: {{ $nama_pasien }}</td>
            <td class="label">Tanggal Lahir</td>
            <td>: {{ $tgl_lahir }}</td>
         </tr>
         <tr>
            <td class="label">No. KTP</td>
            <td>: {{ $no_ktp }}</td>
            <td class="label">Status Nikah</td>
            <td>: {{ $stts_nikah }}</td>
         </tr>
         <tr>
            <td class="label">No. KK</td>
            <td>: {{ $no_kk }}</td>
            <td class="label">Posyandu</td>
            <td>: {{ $data_posyandu }}</td>
         </tr>
         <tr>
            <td class="label">No. Telepon</td>
            <td>: {{ $no_tlp }}</td>
            <td class="label">Pekerjaan</td>
            <td>: {{ $pekerjaan }}</td>
         </tr>
      </table>
   </div>

   <div class="section">
      <div class="section-title">Riwayat Kesehatan</div>
      <table class="info-table">
         <tr>
            <td class="label">Riwayat Diri Sendiri</td>
            <td>: {{ $riwayat_diri_sendiri }}</td>
         </tr>
         <tr>
            <td class="label">Riwayat Keluarga</td>
            <td>: {{ $riwayat_keluarga }}</td>
         </tr>
         <tr>
            <td class="label">Merokok</td>
            <td>: {{ $merokok }}</td>
         </tr>
         <tr>
            <td class="label">Konsumsi Tinggi</td>
            <td>: {{ $konsumsi_tinggi }}</td>
         </tr>
      </table>
   </div>

   <div class="section">
      <div class="section-title">Hasil Pemeriksaan</div>
      <div class="two-columns">
         <div class="column">
            <table class="result-table">
               <tr>
                  <th>Parameter</th>
                  <th>Hasil</th>
               </tr>
               <tr>
                  <td>Berat Badan (kg)</td>
                  <td>{{ $berat_badan }}</td>
               </tr>
               <tr>
                  <td>Tinggi Badan (cm)</td>
                  <td>{{ $tinggi_badan }}</td>
               </tr>
               <tr>
                  <td>IMT</td>
                  <td>{{ $imt }}</td>
               </tr>
               <tr>
                  <td>Lingkar Perut</td>
                  <td>{{ $lp }}</td>
               </tr>
               <tr>
                  <td>Tekanan Darah</td>
                  <td>{{ $td }}</td>
               </tr>
               <tr>
                  <td>Gula Darah</td>
                  <td>{{ $gula_darah }}</td>
               </tr>
               <tr>
                  <td>Metode Mata</td>
                  <td>{{ $metode_mata }}</td>
               </tr>
               <tr>
                  <td>Hasil Mata</td>
                  <td>{{ $hasil_mata }}</td>
               </tr>
               <tr>
                  <td>Tes Berbisik</td>
                  <td>{{ $tes_berbisik }}</td>
               </tr>
               <tr>
                  <td>Gigi</td>
                  <td>{{ $gigi }}</td>
               </tr>
            </table>
         </div>
         <div class="column">
            <table class="result-table">
               <tr>
                  <th>Parameter</th>
                  <th>Hasil</th>
               </tr>
               <tr>
                  <td>Kesehatan Jiwa</td>
                  <td>{{ $kesehatan_jiwa }}</td>
               </tr>
               <tr>
                  <td>TBC</td>
                  <td>{{ $tbc }}</td>
               </tr>
               <tr>
                  <td>Fungsi Hari</td>
                  <td>{{ $fungsi_hari }}</td>
               </tr>
               <tr>
                  <td>Status TT</td>
                  <td>{{ $status_tt }}</td>
               </tr>
               <tr>
                  <td>Penyakit Lain</td>
                  <td>{{ $penyakit_lain_catin }}</td>
               </tr>
               <tr>
                  <td>Kanker Payudara</td>
                  <td>{{ $kanker_payudara }}</td>
               </tr>
               <tr>
                  <td>IVA Test</td>
                  <td>{{ $iva_test }}</td>
               </tr>
               <tr>
                  <td>Resiko Jantung</td>
                  <td>{{ $resiko_jantung }}</td>
               </tr>
               <tr>
                  <td>GDS</td>
                  <td>{{ $gds }}</td>
               </tr>
            </table>
         </div>
      </div>
   </div>

   <div class="section">
      <div class="section-title">Hasil Laboratorium</div>
      <table class="result-table">
         <tr>
            <th>Parameter</th>
            <th>Hasil</th>
            <th>Parameter</th>
            <th>Hasil</th>
         </tr>
         <tr>
            <td>Asam Urat</td>
            <td>{{ $asam_urat }}</td>
            <td>Ureum</td>
            <td>{{ $ureum }}</td>
         </tr>
         <tr>
            <td>Kolesterol</td>
            <td>{{ $kolesterol }}</td>
            <td>Kreatinin</td>
            <td>{{ $kreatinin }}</td>
         </tr>
         <tr>
            <td>Trigliserida</td>
            <td>{{ $trigliserida }}</td>
            <td>Resiko Kanker Usus</td>
            <td>{{ $resiko_kanker_usus }}</td>
         </tr>
         <tr>
            <td>Charta</td>
            <td>{{ $charta }}</td>
            <td>Skor PUMA</td>
            <td>{{ $skor_puma }}</td>
         </tr>
      </table>
   </div>

   <div class="section">
      <div class="section-title">Kesimpulan</div>
      <p>{{ $skilas }}</p>
   </div>

   <div class="divider"></div>

   <div class="section">
      <div class="section-title">Status Pemeriksaan</div>
      <p>
         @if($status == 'Menunggu')
         <span class="status status-menunggu">{{ $status }}</span>
         @elseif($status == 'Dalam Proses')
         <span class="status status-proses">{{ $status }}</span>
         @elseif($status == 'Selesai')
         <span class="status status-selesai">{{ $status }}</span>
         @else
         <span class="status status-diambil">{{ $status }}</span>
         @endif
      </p>
   </div>

   <div class="footer">
      <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
      <p style="margin-top: 80px;">{{ $nama_dokter }}</p>
      <hr style="width: 200px; margin-left: auto; margin-right: 0;">
      <p>Dokter Pemeriksa</p>
   </div>
</body>

</html>