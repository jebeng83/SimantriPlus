<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Dashboard Sekolah</title>
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
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
        }
        .filter-info {
            margin-bottom: 20px;
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
            vertical-align: top;
        }
        .filter-info .label {
            font-weight: bold;
            width: 120px;
        }
        .statistics-cards {
            margin-bottom: 20px;
        }
        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .stats-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stats-card .number {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .stats-card .label {
            font-size: 10px;
            color: #6c757d;
            margin-top: 5px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding: 5px;
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-light { background-color: #f8f9fa; color: black; border: 1px solid #dee2e6; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">{{ $hospital_info['name'] ?? 'RUMAH SAKIT' }}</div>
        @if(isset($hospital_info['address']) && $hospital_info['address'])
        <div style="font-size: 12px; margin-bottom: 5px;">{{ $hospital_info['address'] }}</div>
        @endif
        @if(isset($hospital_info['phone']) && $hospital_info['phone'])
        <div style="font-size: 12px; margin-bottom: 5px;">Telp: {{ $hospital_info['phone'] }}</div>
        @endif
        @if(isset($hospital_info['email']) && $hospital_info['email'])
        <div style="font-size: 12px; margin-bottom: 10px;">Email: {{ $hospital_info['email'] }}</div>
        @endif
        <div style="font-size: 14px; font-weight: bold; margin-top: 10px;">DASHBOARD SEKOLAH</div>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td class="label">Tanggal Cetak:</td>
                <td>{{ $tanggal_cetak }}</td>
                <td class="label">Total Siswa:</td>
                <td>{{ number_format($totalSiswa) }}</td>
            </tr>
            <tr>
                <td class="label">Filter Sekolah:</td>
                <td>{{ $filter_labels['sekolah'] }}</td>
                <td class="label">Filter Jenis Sekolah:</td>
                <td>{{ $filter_labels['jenis_sekolah'] }}</td>
            </tr>
            <tr>
                <td class="label">Filter Kelas:</td>
                <td>{{ $filter_labels['kelas'] }}</td>
                <td class="label"></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="statistics-cards">
        <div class="stats-row">
            <div class="stats-card">
                <div class="number">{{ number_format($totalSiswa) }}</div>
                <div class="label">Total Siswa</div>
            </div>
            <div class="stats-card">
                <div class="number">{{ number_format($siswaLakiLaki) }}</div>
                <div class="label">Siswa Laki-laki</div>
            </div>
            <div class="stats-card">
                <div class="number">{{ number_format($siswaPerempuan) }}</div>
                <div class="label">Siswa Perempuan</div>
            </div>
            <div class="stats-card">
                <div class="number">{{ number_format($siswaAktif) }}</div>
                <div class="label">Siswa Aktif</div>
            </div>
        </div>
    </div>

    <div class="section-title">STATISTIK STATUS SISWA</div>
    <table>
        <tr>
            <th>Status</th>
            <th>Jumlah</th>
            <th>Persentase</th>
        </tr>
        <tr>
            <td>Aktif</td>
            <td class="text-center">{{ number_format($siswaAktif) }}</td>
            <td class="text-center">{{ $totalSiswa > 0 ? number_format(($siswaAktif / $totalSiswa) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Pindah</td>
            <td class="text-center">{{ number_format($siswaPindah) }}</td>
            <td class="text-center">{{ $totalSiswa > 0 ? number_format(($siswaPindah / $totalSiswa) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Lulus</td>
            <td class="text-center">{{ number_format($siswaLulus) }}</td>
            <td class="text-center">{{ $totalSiswa > 0 ? number_format(($siswaLulus / $totalSiswa) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Drop Out</td>
            <td class="text-center">{{ number_format($siswaDropOut) }}</td>
            <td class="text-center">{{ $totalSiswa > 0 ? number_format(($siswaDropOut / $totalSiswa) * 100, 1) : 0 }}%</td>
        </tr>
    </table>

    @if($distribusiUmur->count() > 0)
    <div class="section-title">DISTRIBUSI UMUR</div>
    <table>
        <tr>
            <th>Kelompok Umur</th>
            <th>Jumlah</th>
            <th>Persentase</th>
        </tr>
        @foreach($distribusiUmur as $umur)
        <tr>
            <td>{{ $umur->kelompok_umur }}</td>
            <td class="text-center">{{ number_format($umur->jumlah) }}</td>
            <td class="text-center">{{ $totalSiswa > 0 ? number_format(($umur->jumlah / $totalSiswa) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($statistikSekolah->count() > 0)
    <div class="section-title">STATISTIK PER SEKOLAH</div>
    <table>
        <tr>
            <th>No</th>
            <th>Nama Sekolah</th>
            <th>Jenis Sekolah</th>
            <th>Total Siswa</th>
            <th>Laki-laki</th>
            <th>Perempuan</th>
            <th>Siswa Aktif</th>
            <th>Disabilitas</th>
            <th>Persentase</th>
        </tr>
        @foreach($statistikSekolah as $index => $sekolah)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $sekolah->nama_sekolah }}</td>
            <td class="text-center">
                <span class="badge badge-info">{{ $sekolah->nama }}</span>
            </td>
            <td class="text-center">
                <span class="badge badge-primary">{{ number_format($sekolah->total_siswa) }}</span>
            </td>
            <td class="text-center">{{ number_format($sekolah->siswa_laki) }}</td>
            <td class="text-center">{{ number_format($sekolah->siswa_perempuan) }}</td>
            <td class="text-center">
                <span class="badge badge-success">{{ number_format($sekolah->siswa_aktif) }}</span>
            </td>
            <td class="text-center">
                @if($sekolah->siswa_disabilitas > 0)
                <span class="badge badge-warning">{{ number_format($sekolah->siswa_disabilitas) }}</span>
                @else
                0
                @endif
            </td>
            <td class="text-center">
                <span class="badge badge-light">{{ $totalSiswa > 0 ? number_format(($sekolah->total_siswa / $totalSiswa) * 100, 1) : 0 }}%</span>
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($statistikKelas->count() > 0)
    <div class="section-title">STATISTIK PER KELAS</div>
    <table>
        <tr>
            <th>No</th>
            <th>Kelas</th>
            <th>Sekolah</th>
            <th>Total Siswa</th>
            <th>Laki-laki</th>
            <th>Perempuan</th>
        </tr>
        @foreach($statistikKelas as $index => $kelas)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center">
                <span class="badge badge-primary">{{ $kelas->kelas }}</span>
            </td>
            <td>{{ $kelas->nama_sekolah }}</td>
            <td class="text-center">
                <span class="badge badge-info">{{ number_format($kelas->total_siswa) }}</span>
            </td>
            <td class="text-center">{{ number_format($kelas->siswa_laki) }}</td>
            <td class="text-center">{{ number_format($kelas->siswa_perempuan) }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <div class="footer">
        <div>Dicetak pada: {{ $tanggal_cetak }}</div>
        <div>Sistem Informasi Rumah Sakit</div>
    </div>
</body>
</html>