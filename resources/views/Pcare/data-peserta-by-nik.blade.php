@extends('adminlte::page')

@section('title', 'Data Peserta BPJS by NIK')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pencarian Data Peserta BPJS Berdasarkan NIK</h3>
                </div>
                <div class="card-body">
                    <!-- Form Pencarian -->
                    <form id="searchForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nik">NIK Peserta:</label>
                                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukkan NIK Peserta" maxlength="16" required>
                                    <small class="form-text text-muted">Masukkan 16 digit NIK peserta BPJS</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block" id="btnCari">
                                        <i class="fas fa-search"></i> Cari Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Loading Indicator -->
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Mencari data peserta...</p>
                    </div>

                    <!-- Hasil Pencarian -->
                    <div id="resultContainer" style="display: none;">
                        <hr>
                        <h5>Data Peserta BPJS</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>No. Kartu</strong></td>
                                        <td id="noKartu">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama</strong></td>
                                        <td id="nama">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK</strong></td>
                                        <td id="noKTP">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Kelamin</strong></td>
                                        <td id="sex">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Lahir</strong></td>
                                        <td id="tglLahir">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hubungan Keluarga</strong></td>
                                        <td id="hubunganKeluarga">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. HP</strong></td>
                                        <td id="noHP">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Golongan Darah</strong></td>
                                        <td id="golDarah">-</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Jenis Peserta</strong></td>
                                        <td id="jnsPeserta">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kelas</strong></td>
                                        <td id="jnsKelas">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status</strong></td>
                                        <td id="ketAktif">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Mulai Aktif</strong></td>
                                        <td id="tglMulaiAktif">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Akhir Berlaku</strong></td>
                                        <td id="tglAkhirBerlaku">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Provider PST</strong></td>
                                        <td id="kdProviderPst">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Provider Gigi</strong></td>
                                        <td id="kdProviderGigi">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tunggakan</strong></td>
                                        <td id="tunggakan">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Asuransi Info -->
                        <div class="row mt-3" id="asuransiInfo" style="display: none;">
                            <div class="col-12">
                                <h6>Informasi Asuransi</h6>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Kode Asuransi</strong></td>
                                        <td id="kdAsuransi">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Asuransi</strong></td>
                                        <td id="nmAsuransi">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>No. Asuransi</strong></td>
                                        <td id="noAsuransi">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorContainer" class="alert alert-danger" style="display: none;">
                        <h6>Error:</h6>
                        <p id="errorMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('=== JQUERY AND DOM READY ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('SweetAlert2 available:', typeof Swal !== 'undefined');
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        const nik = $('#nik').val().trim();
        
        // Validasi NIK
        if (nik.length !== 16) {
            alert('NIK harus 16 digit');
            return;
        }
        
        if (!/^\d+$/.test(nik)) {
            alert('NIK hanya boleh berisi angka');
            return;
        }
        
        searchPeserta(nik);
    });
    
    // Format input NIK (hanya angka)
    $('#nik').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

function searchPeserta(nik) {
    // Hide previous results
    $('#resultContainer').hide();
    $('#errorContainer').hide();
    $('#loading').show();
    $('#btnCari').prop('disabled', true);
    
    // AJAX call to search participant data
    $.ajax({
        url: '/api/pcare/peserta/nik/' + nik,
        method: 'GET',
        dataType: 'json',
        headers: {
            'Content-Type': 'application/json; charset=utf-8',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('=== AJAX SUCCESS CALLBACK ===');
            console.log('Full API Response:', response);
            console.log('Response type:', typeof response);
            console.log('MetaData:', response.metaData);
            console.log('MetaData code:', response.metaData ? response.metaData.code : 'undefined');
            console.log('Response data:', response.response);
            
            $('#loading').hide();
            $('#btnCari').prop('disabled', false);
            
            if (response.metaData && response.metaData.code === 200) {
                console.log('=== CONDITION MET: Calling displayPesertaData ===');
                displayPesertaData(response.response);
                console.log('=== displayPesertaData called ===');
            } else {
                console.log('=== CONDITION NOT MET ===');
                console.log('MetaData exists:', !!response.metaData);
                console.log('Code is 200:', response.metaData ? response.metaData.code === 200 : false);
                showError('Data peserta tidak ditemukan atau terjadi kesalahan.');
            }
        },
        error: function(xhr, status, error) {
            $('#loading').hide();
            $('#btnCari').prop('disabled', false);
            
            let errorMsg = 'Terjadi kesalahan saat mengambil data.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMsg = 'Data peserta dengan NIK tersebut tidak ditemukan.';
            } else if (xhr.status === 500) {
                errorMsg = 'Terjadi kesalahan server. Silakan coba lagi.';
            }
            
            showError(errorMsg);
        }
    });
}

function displayPesertaData(data) {
    console.log('=== DISPLAY PESERTA DATA FUNCTION CALLED ===');
    console.log('Data received:', data);
    console.log('Data type:', typeof data);
    console.log('Data keys:', data ? Object.keys(data) : 'data is null/undefined');
    console.log('noKartu value:', data ? data.noKartu : 'undefined');
    console.log('nama value:', data ? data.nama : 'undefined');
    // Fill basic data
    $('#noKartu').text(data.noKartu || '-');
    $('#nama').text(data.nama || '-');
    $('#noKTP').text(data.noKTP || '-');
    $('#sex').text(data.sex === 'L' ? 'Laki-laki' : (data.sex === 'P' ? 'Perempuan' : '-'));
    $('#tglLahir').text(data.tglLahir || '-');
    $('#hubunganKeluarga').text(data.hubunganKeluarga || '-');
    $('#noHP').text(data.noHP || '-');
    $('#golDarah').text(data.golDarah || '-');
    
    // Fill membership data
    $('#jnsPeserta').text(data.jnsPeserta ? `${data.jnsPeserta.nama} (${data.jnsPeserta.kode})` : '-');
    $('#jnsKelas').text(data.jnsKelas ? `${data.jnsKelas.nama} (${data.jnsKelas.kode})` : '-');
    $('#ketAktif').text(data.ketAktif || '-');
    $('#tglMulaiAktif').text(data.tglMulaiAktif || '-');
    $('#tglAkhirBerlaku').text(data.tglAkhirBerlaku || '-');
    
    // Provider info
    $('#kdProviderPst').text(data.kdProviderPst ? `${data.kdProviderPst.nmProvider} (${data.kdProviderPst.kdProvider})` : '-');
    $('#kdProviderGigi').text(data.kdProviderGigi && data.kdProviderGigi.nmProvider ? `${data.kdProviderGigi.nmProvider} (${data.kdProviderGigi.kdProvider})` : '-');
    
    $('#tunggakan').text(data.tunggakan || '0');
    
    // Asuransi info
    if (data.asuransi && (data.asuransi.kdAsuransi || data.asuransi.nmAsuransi || data.asuransi.noAsuransi)) {
        $('#kdAsuransi').text(data.asuransi.kdAsuransi || '-');
        $('#nmAsuransi').text(data.asuransi.nmAsuransi || '-');
        $('#noAsuransi').text(data.asuransi.noAsuransi || '-');
        $('#asuransiInfo').show();
    } else {
        $('#asuransiInfo').hide();
    }
    
    console.log('=== SHOWING RESULT CONTAINER ===');
    $('#resultContainer').show();
    console.log('Result container display:', $('#resultContainer').css('display'));
    console.log('=== DISPLAY PESERTA DATA FUNCTION COMPLETED ===');
}

function showError(message) {
    console.log('=== SHOWING ERROR ===', message);
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    } else {
        $('#errorMessage').text(message);
        $('#errorContainer').show();
        alert('Error: ' + message);
    }
}
</script>
@endsection