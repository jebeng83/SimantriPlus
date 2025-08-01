@extends('adminlte::page')

@section('title', 'Detail Data Siswa Sekolah')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Data Siswa Sekolah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ilp.data-siswa-sekolah.index') }}">Data Siswa Sekolah</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Data Siswa -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user"></i> Data Siswa</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>No. RM</strong></td>
                                        <td width="5%">:</td>
                                        <td><span class="badge badge-info">{{ $siswa->no_rkm_medis ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>NISN</strong></td>
                                        <td>:</td>
                                        <td>{{ $siswa->nisn ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Nama Lengkap</strong></td>
                                        <td>:</td>
                                        <td><strong>{{ $siswa->nama_siswa ?? ($siswa->pasien->nm_pasien ?? '-') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK</strong></td>
                                        <td>:</td>
                                        <td><span class="badge badge-info">{{ $siswa->nik ?? ($siswa->pasien->no_ktp ?? '-') }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Kelamin</strong></td>
                                        <td>:</td>
                                        <td>
                                            @php
                                                $jk = $siswa->jenis_kelamin ?? ($siswa->pasien->jk ?? 'L');
                                            @endphp
                                            <span class="badge badge-{{ $jk == 'L' ? 'primary' : 'pink' }}">
                                                {{ $jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tempat, Tanggal Lahir</strong></td>
                                        <td>:</td>
                                        <td>
                                            @php
                                                $tempat_lahir = $siswa->tempat_lahir ?? ($siswa->pasien->tmp_lahir ?? '-');
                                                $tanggal_lahir = $siswa->tanggal_lahir ?? ($siswa->pasien->tgl_lahir ?? null);
                                            @endphp
                                            {{ $tempat_lahir }}, {{ $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->format('d F Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Umur</strong></td>
                                        <td>:</td>
                                        <td>
                                            @php
                                                $tanggal_lahir = $siswa->tanggal_lahir ?? ($siswa->pasien->tgl_lahir ?? null);
                                            @endphp
                                            {{ $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->age . ' tahun' : '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Alamat</strong></td>
                                        <td width="5%">:</td>
                                        <td>{{ $siswa->alamat ?? ($siswa->pasien->alamat ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Orang Tua</strong></td>
                                        <td>:</td>
                                        <td>{{ $siswa->nama_ortu ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. Telepon Ortu</strong></td>
                                        <td>:</td>
                                        <td>
                                            @if($siswa->no_whatsapp && $siswa->no_whatsapp != '-')
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siswa->no_whatsapp) }}" target="_blank" class="text-success">
                                                    <i class="fab fa-whatsapp"></i> {{ $siswa->no_whatsapp }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Masuk</strong></td>
                                        <td>:</td>
                                        <td>{{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d F Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Disabilitas</strong></td>
                                        <td>:</td>
                                        <td>
                                            @php
                                                $jenis_disabilitas = $siswa->jenis_disabilitas ?? 'Non Disabilitas';
                                            @endphp
                                            @if($jenis_disabilitas == 'Non Disabilitas')
                                                <span class="badge badge-success">{{ $jenis_disabilitas }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $jenis_disabilitas }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Siswa</strong></td>
                                        <td>:</td>
                                        <td>
                                            @php
                                                $status = $siswa->status_siswa ?? 'Aktif';
                                            @endphp
                                            @if($status == 'Aktif')
                                                <span class="badge badge-success">{{ $status }}</span>
                                            @elseif($status == 'Lulus')
                                                <span class="badge badge-primary">{{ $status }}</span>
                                            @elseif($status == 'Pindah')
                                                <span class="badge badge-warning">{{ $status }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Sekolah -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-school"></i> Data Sekolah</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>ID Sekolah</strong></td>
                                        <td width="5%">:</td>
                                        <td>{{ $siswa->id_sekolah ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Sekolah</strong></td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kelurahan</strong></td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>ID Kelas</strong></td>
                                        <td width="5%">:</td>
                                        <td>{{ $siswa->id_kelas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Wali Kelas</strong></td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-md-4">
                <!-- Aksi -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cogs"></i> Aksi</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('ilp.data-siswa-sekolah.edit', $siswa->id) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Edit Data Siswa
                            </a>
                            <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                            <button class="btn btn-success btn-block" onclick="printData()">
                                <i class="fas fa-print"></i> Cetak Data
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .info-box {
        margin-bottom: 15px;
    }
    .table-borderless td {
        border: none;
        padding: 0.3rem;
    }
    .badge {
        font-size: 0.9em;
    }
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
</style>
@stop

@section('js')
<script>
    function printData() {
        window.print();
    }

    // Print styles
    const printStyles = `
        <style>
            @media print {
                .content-wrapper, .main-footer, .main-header {
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .card {
                    border: 1px solid #000 !important;
                    box-shadow: none !important;
                }
                .btn, .breadcrumb {
                    display: none !important;
                }
            }
        </style>
    `;
    document.head.insertAdjacentHTML('beforeend', printStyles);
</script>
@stop