<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Laporan Antrian Poliklinik</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         line-height: 1.5;
         margin: 0;
         padding: 20px;
         color: #333;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
         border-bottom: 2px solid #333;
         padding-bottom: 10px;
      }

      .logo {
         max-height: 80px;
         margin-bottom: 10px;
      }

      h1 {
         font-size: 18px;
         margin: 0;
         text-transform: uppercase;
      }

      h2 {
         font-size: 16px;
         margin: 5px 0;
      }

      .info {
         margin-bottom: 20px;
      }

      .info-row {
         display: flex;
         margin-bottom: 5px;
      }

      .info-label {
         width: 120px;
         font-weight: bold;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 20px;
      }

      th,
      td {
         border: 1px solid #ddd;
         padding: 8px;
         text-align: left;
      }

      th {
         background-color: #f2f2f2;
         font-weight: bold;
      }

      tr:nth-child(even) {
         background-color: #f9f9f9;
      }

      .text-center {
         text-align: center;
      }

      .footer {
         margin-top: 30px;
         display: flex;
         justify-content: flex-end;
      }

      .signature {
         text-align: center;
         width: 200px;
      }

      .signature-line {
         margin-top: 50px;
         border-top: 1px solid #333;
         padding-top: 5px;
      }

      @media print {
         body {
            padding: 0;
            padding-top: 10px;
         }

         .no-print {
            display: none;
         }
      }
   </style>
</head>

<body>
   <div class="header">
      <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
      <h1>E-DOKTER - RSUD GUNUNG TUA</h1>
      <h2>Laporan Antrian Poliklinik</h2>
   </div>

   <div class="info">
      <div class="info-row">
         <div class="info-label">Tanggal</div>
         <div>: {{ $tanggal }}</div>
      </div>
      <div class="info-row">
         <div class="info-label">Poliklinik</div>
         <div>: {{ $namaPoli }}</div>
      </div>
      <div class="info-row">
         <div class="info-label">Dicetak pada</div>
         <div>: {{ date('d-m-Y H:i:s') }}</div>
      </div>
   </div>

   <table>
      <thead>
         <tr>
            <th class="text-center" width="5%">No.</th>
            <th class="text-center" width="10%">No. Antrian</th>
            <th class="text-center" width="15%">No. Rawat</th>
            <th width="25%">Nama Pasien</th>
            <th class="text-center" width="10%">No. RM</th>
            <th width="20%">Poliklinik</th>
            <th width="15%">Dokter</th>
            <th class="text-center" width="10%">Status</th>
         </tr>
      </thead>
      <tbody>
         @if(count($antrian) > 0)
         @foreach($antrian as $key => $item)
         <tr>
            <td class="text-center">{{ $key + 1 }}</td>
            <td class="text-center">{{ $item->no_reg }}</td>
            <td class="text-center">{{ $item->no_rawat }}</td>
            <td>{{ $item->nm_pasien }}</td>
            <td class="text-center">{{ $item->no_rkm_medis }}</td>
            <td>{{ $item->nm_poli }}</td>
            <td>{{ $item->nm_dokter }}</td>
            <td class="text-center">{{ $item->stts }}</td>
         </tr>
         @endforeach
         @else
         <tr>
            <td colspan="8" class="text-center">Tidak ada data antrian yang tersedia</td>
         </tr>
         @endif
      </tbody>
   </table>

   <div class="footer">
      <div class="signature">
         <div>Gunung Tua, {{ date('d-m-Y') }}</div>
         <div>Petugas,</div>
         <div class="signature-line">
            {{ auth()->user()->name ?? 'Administrator' }}
         </div>
      </div>
   </div>

   <div class="no-print" style="margin-top: 20px; text-align: center;">
      <button onclick="window.print()"
         style="padding: 8px 16px; cursor: pointer; background: #4e73df; color: white; border: none; border-radius: 4px;">
         Cetak Laporan
      </button>
      <button onclick="window.close()"
         style="padding: 8px 16px; cursor: pointer; background: #e74a3b; color: white; border: none; border-radius: 4px; margin-left: 10px;">
         Tutup
      </button>
   </div>
</body>

</html>