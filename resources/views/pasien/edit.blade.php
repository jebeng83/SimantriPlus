@extends('adminlte::page')

@section('title', 'Edit Pasien')

@section('content_header')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<h1>Edit Data Pasien</h1>
@stop

@section('content')
<x-adminlte-card>
    <form action="{{ route('pasien.update', $pasien->no_rkm_medis) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- <div class="form-group">
            <label for="no_rkm_medis">No Rekam Medis</label>
            <input type="text" class="form-control" id="no_rkm_medis" name="no_rkm_medis"
                value="{{ $pasien->no_rkm_medis }}" disabled>
            @error('no_rkm_medis')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div> --}}
        <div class="form-group">
            <label for="no_ktp">KTP</label>
            <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="{{ $pasien->no_ktp }}">
            @error('no_ktp')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_kk">No. KK</label>
            <input type="text" class="form-control" id="no_kk" name="no_kk" value="{{ $pasien->no_kk }}">
            @error('no_kk')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="nm_pasien">Nama</label>
            <input type="text" class="form-control" id="nm_pasien" name="nm_pasien" value="{{ $pasien->nm_pasien }}">
            @error('nm_pasien')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="tgl_lahir">Tgl Lahir</label>
            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ $pasien->tgl_lahir }}">
            @error('tgl_lahir')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select type="text" class="form-control" id="status" name="status">
                <option value="Kepala Keluarga" @if($pasien->status == 'Kepala Keluarga') selected @endif>Kepala
                    Keluarga</option>
                <option value="Suami" @if($pasien->status == 'Suami') selected @endif>Suami</option>
                <option value="Istri" @if($pasien->status == 'Istri') selected @endif>Istri</option>
                <option value="Anak" @if($pasien->status == 'Anak') selected @endif>Anak</option>
                <option value="Menantu" @if($pasien->status == 'Menantu') selected @endif>Menantu</option>
                <option value="Orang tua" @if($pasien->status == 'Orang tua') selected @endif>Orang tua</option>
                <option value="Mertua" @if($pasien->status == 'Mertua') selected @endif>Mertua</option>
                <option value="Pembantu" @if($pasien->status == 'Pembantu') selected @endif>Pembantu</option>
                <option value="Famili Lain" @if($pasien->status == 'Famili Lain') selected @endif>Famili Lain</option>
                <option value="Lainnya" @if($pasien->status == 'Lainnya') selected @endif>Lainnya</option>
            </select>
            @error('status')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="stts_nikah">Status Nikah</label>
            <select type="text" class="form-control" id="stts_nikah" name="stts_nikah">
                <option value="MENIKAH" @if($pasien->stts_nikah == 'MENIKAH') selected @endif>MENIKAH</option>
                <option value="BELUM MENIKAH" @if($pasien->stts_nikah == 'BELUM MENIKAH') selected @endif>BELUM MENIKAH
                </option>
                <option value="JANDA" @if($pasien->stts_nikah == 'JANDA') selected @endif>JANDA</option>
                <option value="DUDHA" @if($pasien->stts_nikah == 'DUDHA') selected @endif>DUDHA</option>
                <option value="JOMBLO" @if($pasien->stts_nikah == 'JOMBLO') selected @endif>JOMBLO</option>
            </select>
            @error('stts_nikah')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_peserta">No. Peserta</label>
            <input type="text" class="form-control" id="no_peserta" name="no_peserta" value="{{ $pasien->no_peserta }}">
            @error('no_peserta')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_tlp">No. Telp</label>
            <input type="text" class="form-control" id="no_tlp" name="no_tlp" value="{{ $pasien->no_tlp }}">
            @error('no_tlp')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea type="text" class="form-control" id="alamat" name="alamat"
                rows="4">{{ $pasien->alamat }}</textarea>
            @error('alamat')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="data_posyandu">Posyandu</label>
            <select type="text" class="form-control" id="data_posyandu" name="data_posyandu">
                @foreach($posyandu as $pos)
                <option value="{{ $pos->nama_posyandu }}" @if($pasien->data_posyandu == $pos->nama_posyandu) selected
                    @endif>{{ $pos->nama_posyandu }}</option>
                @endforeach
            </select>
            @error('data_posyandu')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</x-adminlte-card>
@stop

@section('plugins.TempusDominusBs4', true)

@section('css')
<style>
    .form-group label {
        font-weight: 600;
        color: #333;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
        transform: translateY(-2px);
    }

    .btn-secondary {
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Refresh data dari server
        const no_rkm_medis = '{{ $pasien->no_rkm_medis }}';
        
        // AJAX request untuk mendapatkan data terbaru pasien
        $.ajax({
            url: '/pasien/' + no_rkm_medis,
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function(data) {
                // Isi formulir dengan data terbaru
                $('#no_ktp').val(data.no_ktp);
                $('#no_kk').val(data.no_kk);
                $('#nm_pasien').val(data.nm_pasien);
                $('#tgl_lahir').val(data.tgl_lahir);
                $('#status').val(data.status);
                $('#stts_nikah').val(data.stts_nikah);
                $('#no_peserta').val(data.no_peserta);
                $('#no_tlp').val(data.no_tlp);
                $('#alamat').val(data.alamat);
                $('#data_posyandu').val(data.data_posyandu);
                
                console.log('Data pasien berhasil disegarkan dari database');
            },
            error: function(xhr) {
                console.error('Gagal memperbarui data: ' + xhr.responseText);
            }
        });
        
        // Animasi untuk form
        $('.form-group').each(function(index) {
            $(this).css('opacity', 0);
            $(this).animate({
                opacity: 1
            }, 300 * (index + 1));
        });
        
        // Validasi form
        $('form').on('submit', function(e) {
            let valid = true;
            
            // Validasi nama
            if ($('#nm_pasien').val().trim() === '') {
                $('#nm_pasien').addClass('is-invalid');
                valid = false;
            } else {
                $('#nm_pasien').removeClass('is-invalid');
            }
            
            // Validasi KTP
            if ($('#no_ktp').val().trim() === '') {
                $('#no_ktp').addClass('is-invalid');
                valid = false;
            } else {
                $('#no_ktp').removeClass('is-invalid');
            }
            
            // Validasi tanggal lahir
            if ($('#tgl_lahir').val().trim() === '') {
                $('#tgl_lahir').addClass('is-invalid');
                valid = false;
            } else {
                $('#tgl_lahir').removeClass('is-invalid');
            }
            
            if (!valid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
@stop