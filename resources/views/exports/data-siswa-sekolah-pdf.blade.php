<!DOCTYPE html>
<html>

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Data Siswa Sekolah</title>
   <style>
      body {
         font-family: Arial, sans-serif;
         font-size: 10px;
         margin: 0;
         padding: 20px;
      }

      .header {
         text-align: center;
         margin-bottom: 20px;
         border-bottom: 2px solid #333;
         padding-bottom: 10px;
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

      .filter-info {
         margin-bottom: 15px;
         background-color: #f8f9fa;
         padding: 10px;
         border: 1px solid #dee2e6;
      }

      .filter-info table {
         width: 100%;
         border-collapse: collapse;
      }

      .filter-info td {
         padding: 3px 5px;
         border: none;
         font-size: 10px;
      }

      .filter-info .label {
         font-weight: bold;
         width: 15%;
      }

      table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 20px;
      }

      th,
      td {
         border: 1px solid #000000;
         padding: 4px;
         text-align: left;
         vertical-align: top;
      }

      th {
         background-color: #f2f2f2;
         font-weight: bold;
         text-align: center;
         font-size: 9px;
      }

      td {
         font-size: 8px;
      }

      .footer {
         margin-top: 20px;
         font-size: 10px;
         text-align: right;
      }

      .text-center {
         text-align: center;
      }

      .text-right {
         text-align: right;
      }

      .badge {
         display: inline-block;
         padding: 2px 6px;
         font-size: 7px;
         font-weight: bold;
         border-radius: 3px;
      }

      .badge-success {
         background-color: #d4edda;
         color: #155724;
      }

      .badge-warning {
         background-color: #fff3cd;
         color: #856404;
      }

      .badge-danger {
         background-color: #f8d7da;
         color: #721c24;
      }

      .badge-primary {
         background-color: #d1ecf1;
         color: #0c5460;
      }

      .badge-info {
         background-color: #d1ecf1;
         color: #0c5460;
      }

      .badge-secondary {
         background-color: #e2e3e5;
         color: #383d41;
      }

      .summary {
         margin-bottom: 15px;
         padding: 10px;
         background-color: #e9ecef;
         border: 1px solid #ced4da;
      }

      .summary .total {
         font-weight: bold;
         font-size: 12px;
      }
   </style>
</head>

<body>
   <div class="header">
      <div class="title">{{ $hospital_info['name'] }}</div>
      <div class="subtitle">{{ $hospital_info['address'] }}</div>
      @if(isset($hospital_info['phone']) && $hospital_info['phone'])
      <div style="font-size: 12px; margin-bottom: 5px;">Telp: {{ $hospital_info['phone'] }}</div>
      @endif
      @if(isset($hospital_info['email']) && $hospital_info['email'])
      <div style="font-size: 12px; margin-bottom: 10px;">Email: {{ $hospital_info['email'] }}</div>
      @endif
      <div style="font-size: 14px; font-weight: bold; margin-top: 10px;">DATA SISWA SEKOLAH</div>
   </div>

   <div class="filter-info">
      <table>
         <tr>
            <td class="label">Tanggal Cetak:</td>
            <td>{{ $tanggal_cetak }}</td>
            <td class="label">Total Data:</td>
            <td class="total">{{ $total_data }} siswa</td>
         </tr>
         <tr>
            <td class="label">Filter Sekolah:</td>
            <td>{{ $filters['sekolah'] }}</td>
            <td class="label">Filter Kelas:</td>
            <td>{{ $filters['kelas'] }}</td>
         </tr>
         <tr>
            <td class="label">Filter Status:</td>
            <td>{{ $filters['status'] }}</td>
            <td class="label">Pencarian:</td>
            <td>{{ $filters['search'] ?: 'Tidak ada' }}</td>
         </tr>
      </table>
   </div>

   <table>
      <thead>
         <tr>
            <th style="width: 3%;">No</th>
            <th style="width: 8%;">No KTP</th>
            <th style="width: 12%;">Nama Siswa</th>
            <th style="width: 8%;">NISN</th>
            <th style="width: 4%;">JK</th>
            <th style="width: 8%;">TTL</th>
            <th style="width: 5%;">Umur</th>
            <th style="width: 12%;">Sekolah</th>
            <th style="width: 6%;">Kelas</th>
            <th style="width: 10%;">Nama Ortu</th>
            <th style="width: 8%;">NIK Ortu</th>
            <th style="width: 8%;">No WhatsApp</th>
            <th style="width: 8%;">Disabilitas</th>
            <th style="width: 6%;">Status</th>
         </tr>
      </thead>
      <tbody>
         @forelse($data as $siswa)
         <tr>
            <td class="text-center">{{ $siswa['no'] }}</td>
            <td>{{ $siswa['no_ktp'] }}</td>
            <td><strong>{{ $siswa['nama_siswa'] }}</strong></td>
            <td>{{ $siswa['nisn'] }}</td>
            <td class="text-center">
               @if($siswa['jenis_kelamin'] == 'Laki-laki')
               <span class="badge badge-primary">L</span>
               @else
               <span class="badge badge-warning">P</span>
               @endif
            </td>
            <td>{{ $siswa['tempat_lahir'] }}, {{ $siswa['tanggal_lahir'] }}</td>
            <td class="text-center">{{ $siswa['umur'] }}</td>
            <td>
               <strong>{{ $siswa['nama_sekolah'] }}</strong><br>
               <small>{{ $siswa['jenis_sekolah'] }}</small>
            </td>
            <td class="text-center">{{ $siswa['kelas'] }}</td>
            <td>{{ $siswa['nama_ortu'] }}</td>
            <td>{{ $siswa['nik_ortu'] }}</td>
            <td>{{ $siswa['no_whatsapp'] }}</td>
            <td>
               @if($siswa['jenis_disabilitas'] == 'Non Disabilitas')
               <span class="badge badge-success">{{ $siswa['jenis_disabilitas'] }}</span>
               @else
               <span class="badge badge-warning">{{ $siswa['jenis_disabilitas'] }}</span>
               @endif
            </td>
            <td>
               @if($siswa['status'] == 'Aktif')
               <span class="badge badge-success">{{ $siswa['status'] }}</span>
               @elseif($siswa['status'] == 'Lulus')
               <span class="badge badge-primary">{{ $siswa['status'] }}</span>
               @elseif($siswa['status'] == 'Pindah')
               <span class="badge badge-warning">{{ $siswa['status'] }}</span>
               @else
               <span class="badge badge-danger">{{ $siswa['status'] }}</span>
               @endif
            </td>
         </tr>
         @empty
         <tr>
            <td colspan="14" class="text-center">Tidak ada data siswa</td>
         </tr>
         @endforelse
      </tbody>
   </table>

   <div class="footer">
      <p>Dicetak pada: {{ $tanggal_cetak }}</p>
      <p>Total: {{ $total_data }} siswa</p>
   </div>
</body>

</html>