<!DOCTYPE html>
<html>

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Data Pendaftaran PCare BPJS</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 11px;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
      }

      .title {
         font-size: 16px;
         font-weight: bold;
         margin-bottom: 5px;
      }

      .subtitle {
         font-size: 14px;
         margin-bottom: 15px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
      }

      th,
      td {
         border: 1px solid #000000;
         padding: 5px;
      }

      th {
         background-color: #f2f2f2;
         font-weight: bold;
         text-align: center;
      }

      .footer {
         margin-top: 20px;
         font-size: 10px;
         text-align: right;
      }

      .filter-info {
         margin-bottom: 10px;
      }

      .page-number {
         position: absolute;
         bottom: 0;
         width: 100%;
         text-align: center;
         font-size: 10px;
      }

      .text-center {
         text-align: center;
      }

      .text-right {
         text-align: right;
      }
   </style>
</head>

<body>
   <div class="header">
      <div class="title">DATA PENDAFTARAN PCARE BPJS</div>
      <div class="subtitle">e-Dokter RSUD GUNUNG TUA</div>
   </div>

   <div class="filter-info">
      <table style="border: none; width: 100%; margin-bottom: 10px;">
         <tr>
            <td style="border: none; width: 15%;">Tanggal Cetak</td>
            <td style="border: none; width: 2%;">:</td>
            <td style="border: none;">{{ $tanggal_cetak }}</td>
            <td style="border: none; width: 15%;">Filter Tanggal</td>
            <td style="border: none; width: 2%;">:</td>
            <td style="border: none;">{{ $tanggal }}</td>
         </tr>
         <tr>
            <td style="border: none;">Filter Status</td>
            <td style="border: none;">:</td>
            <td style="border: none;">{{ $status }}</td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
            <td style="border: none;"></td>
         </tr>
      </table>
   </div>

   <table>
      <thead>
         <tr>
            <th style="width: 5%;">No.</th>
            <th style="width: 10%;">No. Rawat</th>
            <th style="width: 8%;">Tgl Daftar</th>
            <th style="width: 8%;">No. RM</th>
            <th style="width: 15%;">Nama Pasien</th>
            <th style="width: 12%;">No. BPJS</th>
            <th style="width: 10%;">Poli</th>
            <th style="width: 7%;">Kunjungan Sakit</th>
            <th style="width: 10%;">TKP</th>
            <th style="width: 5%;">No. Urut</th>
            <th style="width: 10%;">Status</th>
         </tr>
      </thead>
      <tbody>
         @if(count($data) > 0)
         @foreach($data as $key => $row)
         <tr>
            <td class="text-center">{{ $key + 1 }}</td>
            <td>{{ $row->no_rawat }}</td>
            <td>{{ $row->tglDaftar_formatted ?? $row->tglDaftar }}</td>
            <td>{{ $row->no_rkm_medis }}</td>
            <td>{{ $row->nm_pasien }}</td>
            <td>{{ $row->noKartu }}</td>
            <td>{{ $row->nmPoli }}</td>
            <td class="text-center">{{ $row->kunjSakit_formatted ?? ($row->kunjSakit === 'true' ? 'Ya' : 'Tidak') }}
            </td>
            <td>{{ $row->tkp_formatted ?? $row->kdTkp }}</td>
            <td class="text-center">{{ $row->noUrut }}</td>
            <td>{{ $row->status }}</td>
         </tr>
         @endforeach
         @else
         <tr>
            <td colspan="11" class="text-center">Tidak ada data yang tersedia</td>
         </tr>
         @endif
      </tbody>
   </table>

   <div class="footer">
      Dicetak pada: {{ $tanggal_cetak }}
   </div>

   <div class="page-number">
      Halaman <span class="page">{{ "{PAGE_NUM}" }}</span> dari <span class="topage">{{ "{PAGE_COUNT}" }}</span>
   </div>
</body>

</html>