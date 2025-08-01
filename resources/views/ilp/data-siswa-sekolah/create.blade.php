@extends('adminlte::page')

@section('title', 'Tambah Data Siswa')

@section('content_header')
<div class="d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Tambah Data Siswa Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ilp.data-siswa-sekolah.index') }}">Data Siswa Sekolah</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-secondary">
         <i class="fas fa-arrow-left mr-1"></i> Kembali
      </a>
   </div>
</div>
@stop

@section('content')
@if($errors->any())
<div class="alert alert-danger alert-dismissible animate__animated animate__fadeInDown">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   <h5><i class="icon fas fa-ban"></i> Error!</h5>
   <ul class="mb-0">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
   </ul>
</div>
@endif

<form action="{{ route('ilp.data-siswa-sekolah.store') }}" method="POST" id="formTambahSiswa">
   @csrf

   <!-- Data Identitas -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white">
         <h3 class="card-title m-0">
            <i class="fas fa-user mr-2"></i>Data Identitas Siswa
         </h3>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-6">
               <div class="form-group">
                  <label for="nis">NIS <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis" name="nis"
                     value="{{ old('nis') }}" required>
                  @error('nis')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label for="nisn">NISN</label>
                  <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn" name="nisn"
                     value="{{ old('nisn') }}">
                  @error('nisn')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-8">
               <div class="form-group">
                  <label for="nama_siswa">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" id="nama_siswa"
                     name="nama_siswa" value="{{ old('nama_siswa') }}" required>
                  @error('nama_siswa')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="nik">NIK</label>
                  <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
                     value="{{ old('nik') }}" maxlength="16">
                  @error('nik')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                  <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin"
                     name="jenis_kelamin" required>
                     <option value="">Pilih Jenis Kelamin</option>
                     <option value="L" {{ old('jenis_kelamin')=='L' ? 'selected' : '' }}>Laki-laki</option>
                     <option value="P" {{ old('jenis_kelamin')=='P' ? 'selected' : '' }}>Perempuan</option>
                  </select>
                  @error('jenis_kelamin')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir"
                     name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                  @error('tempat_lahir')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                  <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                     id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                  @error('tanggal_lahir')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-12">
               <div class="form-group">
                  <label for="alamat">Alamat Lengkap <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                     rows="3" required>{{ old('alamat') }}</textarea>
                  @error('alamat')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Data Orang Tua -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-info text-white">
         <h3 class="card-title m-0">
            <i class="fas fa-users mr-2"></i>Data Orang Tua/Wali
         </h3>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label for="nama_ayah">Nama Ayah</label>
                  <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" id="nama_ayah"
                     name="nama_ayah" value="{{ old('nama_ayah') }}">
                  @error('nama_ayah')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <div class="col-md-4">
               <div class="form-group">
                  <label for="no_telepon_ortu">No. Telepon Orang Tua</label>
                  <input type="text" class="form-control @error('no_telepon_ortu') is-invalid @enderror"
                     id="no_telepon_ortu" name="no_telepon_ortu" value="{{ old('no_telepon_ortu') }}">
                  @error('no_telepon_ortu')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Data Sekolah -->
   <div class="card shadow-sm mb-4">
      <div class="card-header bg-success text-white">
         <h3 class="card-title m-0">
            <i class="fas fa-school mr-2"></i>Data Sekolah
         </h3>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-6">
               <div class="form-group">
                  <label for="sekolah_id">Sekolah <span class="text-danger">*</span></label>
                  <select class="form-control @error('sekolah_id') is-invalid @enderror" id="sekolah_id"
                     name="sekolah_id" required>
                     <option value="">Pilih Sekolah</option>
                     @foreach($daftarSekolah as $sekolah)
                     <option value="{{ $sekolah->id }}" {{ old('sekolah_id')==$sekolah->id ? 'selected' : '' }}>
                        {{ $sekolah->nama_sekolah }}
                     </option>
                     @endforeach
                  </select>
                  @error('sekolah_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                  <select class="form-control @error('kelas_id') is-invalid @enderror" id="kelas_id" name="kelas_id"
                     required>
                     <option value="">Pilih Kelas</option>
                     @foreach($daftarKelas as $kelas)
                     <option value="{{ $kelas->id }}" {{ old('kelas_id')==$kelas->id ? 'selected' : '' }}>
                        {{ $kelas->kelas }} - Tingkat {{ $kelas->tingkat }}
                     </option>
                     @endforeach
                  </select>
                  @error('kelas_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>


      </div>
   </div>

   <!-- Action Buttons -->
   <div class="card shadow-sm">
      <div class="card-body">
         <div class="d-flex justify-content-between">
            <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-secondary">
               <i class="fas fa-times mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
               <i class="fas fa-save mr-1"></i> Simpan Data
            </button>
         </div>
      </div>
   </div>
</form>
@stop

@section('css')
<style>
   .animate__animated {
      animation-duration: 0.5s;
   }

   .animate__fadeIn {
      animation-name: fadeIn;
   }

   .animate__fadeInDown {
      animation-name: fadeInDown;
   }

   @keyframes fadeIn {
      from {
         opacity: 0;
      }

      to {
         opacity: 1;
      }
   }

   @keyframes fadeInDown {
      from {
         opacity: 0;
         transform: translate3d(0, -100%, 0);
      }

      to {
         opacity: 1;
         transform: translate3d(0, 0, 0);
      }
   }

   .form-group label {
      font-weight: 600;
      color: #495057;
   }

   .text-danger {
      font-weight: bold;
   }

   .card {
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
   }

   .card-header {
      border-bottom: 1px solid rgba(0, 0, 0, 0.125);
   }
</style>
@stop

@section('js')
<script>
   $(document).ready(function() {
    // Filter kelas berdasarkan sekolah yang dipilih
    $('#sekolah_id').change(function() {
        var sekolahId = $(this).val();
        var kelasSelect = $('#kelas_id');
        
        // Reset kelas dropdown
        kelasSelect.html('<option value="">Loading...</option>');
        
        if (sekolahId) {
            $.ajax({
                url: '{{ route("ilp.get-kelas-by-sekolah") }}',
                type: 'GET',
                data: { id_sekolah: sekolahId },
                success: function(data) {
                    kelasSelect.html('<option value="">Pilih Kelas</option>');
                    $.each(data, function(index, kelas) {
                        kelasSelect.append('<option value="' + kelas.id + '">' + kelas.kelas + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    kelasSelect.html('<option value="">Error loading kelas</option>');
                    alert('Gagal memuat data kelas: ' + error);
                }
            });
        } else {
            kelasSelect.html('<option value="">Pilih Kelas</option>');
        }
    });
    
    // Validasi form sebelum submit
    $('#formTambahSiswa').submit(function(e) {
        var isValid = true;
        var errorMessage = '';
        
        // Validasi NIS
        if ($('#nis').val().trim() === '') {
            isValid = false;
            errorMessage += 'NIS harus diisi\n';
        }
        
        // Validasi nama siswa
        if ($('#nama_siswa').val().trim() === '') {
            isValid = false;
            errorMessage += 'Nama siswa harus diisi\n';
        }
        
        // Validasi jenis kelamin
        if ($('#jenis_kelamin').val() === '') {
            isValid = false;
            errorMessage += 'Jenis kelamin harus dipilih\n';
        }
        
        // Validasi tempat lahir
        if ($('#tempat_lahir').val().trim() === '') {
            isValid = false;
            errorMessage += 'Tempat lahir harus diisi\n';
        }
        
        // Validasi tanggal lahir
        if ($('#tanggal_lahir').val() === '') {
            isValid = false;
            errorMessage += 'Tanggal lahir harus diisi\n';
        }
        
        // Validasi alamat
        if ($('#alamat').val().trim() === '') {
            isValid = false;
            errorMessage += 'Alamat harus diisi\n';
        }
        
        // Validasi sekolah
        if ($('#sekolah_id').val() === '') {
            isValid = false;
            errorMessage += 'Sekolah harus dipilih\n';
        }
        
        // Validasi kelas
        if ($('#kelas_id').val() === '') {
            isValid = false;
            errorMessage += 'Kelas harus dipilih\n';
        }
        

        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi data berikut:\n\n' + errorMessage);
            return false;
        }
        
        // Konfirmasi sebelum menyimpan
        if (!confirm('Apakah Anda yakin ingin menyimpan data siswa ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Format NIK input (hanya angka, maksimal 16 digit)
    $('#nik').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
    });
    
    // Format nomor telepon (hanya angka)
    $('#no_telepon_ortu').on('input', function() {
        this.value = this.value.replace(/[^0-9+\-\s]/g, '');
    });
});
</script>
@stop