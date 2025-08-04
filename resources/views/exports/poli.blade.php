<!DOCTYPE html>
<html>

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Referensi Poli PCare BPJS</title>
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

      .text-center {
         text-align: center;
      }

      .badge-success {
         background-color: #28a745;
         color: white;
         padding: 2px 6px;
         border-radius: 3px;
         font-size: 10px;
      }

      .badge-secondary {
         background-color: #6c757d;
         color: white;
         padding: 2px 6px;
         border-radius: 3px;
         font-size: 10px;
      }

      .badge-warning {
         background-color: #ffc107;
         color: #212529;
         padding: 2px 6px;
         border-radius: 3px;
         font-size: 10px;
      }
   </style>
</head>

<body>
   <div class="header">
      <div class="title">REFERENSI POLI PCARE BPJS</div>
      <div class="subtitle">Tanggal Export: {{ date('d/m/Y H:i:s') }}</div>
   </div>

   <table>
      <thead>
         <tr>
            <th style="width: 50px">No</th>
            <th style="width: 120px">Kode Poli</th>
            <th>Nama Poli</th>
            <th style="width: 120px">Status Poli Sakit</th>
         </tr>
      </thead>
      <tbody>
         @if(isset($data['list']) && is_array($data['list']))
            @foreach($data['list'] as $index => $item)
            <tr>
               <td class="text-center">{{ $index + 1 }}</td>
               <td class="text-center">{{ $item['kdPoli'] ?? '-' }}</td>
               <td>{{ $item['nmPoli'] ?? '-' }}</td>
               <td class="text-center">
                  @if(isset($item['poliSakit']))
                     @if($item['poliSakit'] === true)
                        <span class="badge-success">Ya</span>
                     @elseif($item['poliSakit'] === false)
                        <span class="badge-secondary">Tidak</span>
                     @else
                        <span class="badge-warning">-</span>
                     @endif
                  @else
                     <span class="badge-warning">-</span>
                  @endif
               </td>
            </tr>
            @endforeach
         @else
            <tr>
               <td colspan="4" class="text-center">Tidak ada data yang tersedia</td>
            </tr>
         @endif
      </tbody>
   </table>

   <div class="footer">
      <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
      <p>Total Data: {{ isset($data['list']) && is_array($data['list']) ? count($data['list']) : 0 }} poli</p>
   </div>
</body>

</html>