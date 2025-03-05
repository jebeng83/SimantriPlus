<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <title>Data Pasien</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 12px;
         margin: 0;
         padding: 0;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
         padding: 10px;
         border-bottom: 2px solid #4a7ebb;
      }

      .header h2 {
         margin: 5px 0;
         padding: 0;
         color: #2b5797;
      }

      .header p {
         margin: 5px 0;
         color: #555;
      }

      .logo-container {
         text-align: center;
         margin-bottom: 10px;
      }

      .logo {
         max-width: 80px;
         max-height: 80px;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 20px;
      }

      table,
      th,
      td {
         border: 1px solid #ddd;
      }

      th,
      td {
         padding: 8px;
         text-align: left;
         font-size: 11px;
      }

      th {
         background-color: #4a7ebb;
         color: white;
         font-weight: bold;
      }

      tr:nth-child(even) {
         background-color: #f2f2f2;
      }

      .footer {
         margin-top: 30px;
         text-align: right;
         font-size: 11px;
         color: #555;
         border-top: 1px solid #ddd;
         padding-top: 10px;
      }

      .page-break {
         page-break-after: always;
      }

      .info-box {
         background-color: #f9f9f9;
         border: 1px solid #ddd;
         padding: 10px;
         margin-bottom: 20px;
         border-radius: 5px;
      }

      .info-item {
         margin-bottom: 5px;
      }
   </style>
</head>

<body>
   <div class="header">
      <div class="logo-container">
         <!-- Logo bisa ditambahkan di sini -->
         <!-- <img src="{{ public_path('img/logo.png') }}" class="logo" alt="Logo"> -->
      </div>
      <h2>DATA PASIEN</h2>
      <p>Tanggal Cetak: {{ $tanggal }}</p>
   </div>

   <div class="info-box">
      <div class="info-item"><strong>Total Data:</strong> {{ count($pasien) }} pasien</div>
      <div class="info-item"><strong>Dicetak Oleh:</strong> {{ Auth::user()->name }}</div>
      @if(!empty(request('name')) || !empty(request('rm')) || !empty(request('address')))
      <div class="info-item"><strong>Filter:</strong>
         @if(!empty(request('name'))) Nama: {{ request('name') }} @endif
         @if(!empty(request('rm'))) No. RM: {{ request('rm') }} @endif
         @if(!empty(request('address'))) Alamat: {{ request('address') }} @endif
      </div>
      @endif
   </div>

   <table>
      <thead>
         <tr>
            <th width="5%">No</th>
            <th width="10%">No. RM</th>
            <th width="20%">Nama Pasien</th>
            <th width="15%">No. KTP</th>
            <th width="10%">Tgl Lahir</th>
            <th width="20%">Alamat</th>
            <th width="10%">Status</th>
            <th width="10%">No. Telepon</th>
         </tr>
      </thead>
      <tbody>
         @if(count($pasien) > 0)
         @foreach($pasien as $index => $p)
         <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $p->no_rkm_medis }}</td>
            <td>{{ $p->nm_pasien }}</td>
            <td>{{ $p->no_ktp }}</td>
            <td>{{ $p->tgl_lahir }}</td>
            <td>{{ $p->alamat }}</td>
            <td>{{ $p->stts_nikah }}</td>
            <td>{{ $p->no_tlp }}</td>
         </tr>
         @endforeach
         @else
         <tr>
            <td colspan="8" style="text-align: center;">Tidak ada data pasien</td>
         </tr>
         @endif
      </tbody>
   </table>

   <div class="footer">
      <p>Dokumen ini dicetak dari Sistem E-Dokter pada {{ date('d-m-Y H:i:s') }}</p>
      <p>Halaman 1 dari 1</p>
   </div>
</body>

</html>