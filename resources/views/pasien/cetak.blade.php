<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <title>Data Pasien</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 10px;
         margin: 0;
         padding: 0;
      }

      .header {
         text-align: center;
         margin-bottom: 10px;
         padding: 5px;
         border-bottom: 1px solid #4a7ebb;
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

      table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 10px;
      }

      table,
      th,
      td {
         border: 0.5px solid #ddd;
      }

      th,
      td {
         padding: 4px;
         text-align: left;
         font-size: 9px;
      }

      th {
         background-color: #f5f5f5;
      }

      .footer {
         margin-top: 10px;
         text-align: right;
         font-size: 8px;
         color: #555;
         padding-top: 5px;
      }

      .info-box {
         padding: 5px;
         margin-bottom: 10px;
         font-size: 9px;
      }

      .info-item {
         margin-bottom: 3px;
      }
   </style>
</head>

<body>
   <div class="header">
      <h2>DATA PASIEN</h2>
      <p>Tanggal Cetak: {{ $tanggal }}</p>
   </div>

   <div class="info-box">
      <div class="info-item"><strong>Total Data:</strong> {{ count($pasien) }} pasien (dibatasi 100 data)</div>
      <div class="info-item"><strong>Dicetak Oleh:</strong> {{ $user->name }}</div>
      @if(!empty($filter['name']) || !empty($filter['rm']) || !empty($filter['address']))
      <div class="info-item"><strong>Filter:</strong>
         @if(!empty($filter['name'])) Nama: {{ $filter['name'] }} @endif
         @if(!empty($filter['rm'])) No. RM: {{ $filter['rm'] }} @endif
         @if(!empty($filter['address'])) Alamat: {{ $filter['address'] }} @endif
      </div>
      @endif
   </div>

   <table>
      <thead>
         <tr>
            <th width="5%">No</th>
            <th width="10%">No. RM</th>
            <th width="25%">Nama Pasien</th>
            <th width="15%">No. KTP</th>
            <th width="10%">Tgl Lahir</th>
            <th width="25%">Alamat</th>
            <th width="10%">Status</th>
         </tr>
      </thead>
      <tbody>
         @forelse($pasien as $index => $p)
         <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $p['no_rkm_medis'] }}</td>
            <td>{{ $p['nm_pasien'] }}</td>
            <td>{{ $p['no_ktp'] }}</td>
            <td>{{ $p['tgl_lahir'] }}</td>
            <td>{{ $p['alamat'] }}</td>
            <td>{{ $p['status'] }}</td>
         </tr>
         @empty
         <tr>
            <td colspan="7" style="text-align: center;">Tidak ada data pasien</td>
         </tr>
         @endforelse
      </tbody>
   </table>

   <div class="footer">
      <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
   </div>
</body>

</html>