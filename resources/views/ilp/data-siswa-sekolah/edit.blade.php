@extends('adminlte::page')

@section('title', 'Edit Data Siswa Sekolah')

@section('content_header')
<div class="d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Edit Data Siswa Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ilp.data-siswa-sekolah.index') }}">Data Siswa Sekolah</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <p class="text-muted m-0"><i class="fas fa-calendar-day mr-1"></i> {{ date('d F Y') }}</p>
   </div>
</div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Data Siswa</h3>
                    </div>
                    
                    <form action="{{ route('ilp.data-siswa-sekolah.update', $siswa->id) }}" method="POST" id="editSiswaForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-ban"></i> Terdapat Kesalahan!</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <!-- Informasi Data Tabel yang Di-Join -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                </div>
                            </div>

                            <!-- Data Identitas Siswa -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section-title">
                                        <h5 class="text-primary mb-0"><i class="fas fa-user mr-2"></i> Data Identitas Siswa</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                               id="nik" name="nik" value="{{ old('nik', $siswa->pasien->no_ktp ?? $siswa->nik ?? '') }}" required maxlength="16" 
                                               placeholder="Masukkan 16 digit NIK">
                                        @error('nik')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">NIK harus 16 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nisn">NISN</label>
                                        <input type="text" class="form-control @error('nisn') is-invalid @enderror" 
                                               id="nisn" name="nisn" value="{{ old('nisn', $siswa->nisn) }}">
                                        @error('nisn')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nama_siswa">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" 
                                               id="nama_siswa" name="nama_siswa" value="{{ old('nama_siswa', $siswa->pasien->nm_pasien ?? '') }}" required>
                                        @error('nama_siswa')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" 
                                                id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin', $siswa->pasien->jk ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $siswa->pasien->jk ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                               id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->pasien->tmp_lahir ?? '') }}" required>
                                        @error('tempat_lahir')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                               id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->pasien->tgl_lahir ?? '') }}" required>
                                        @error('tanggal_lahir')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                                  id="alamat" name="alamat" rows="3" required>{{ old('alamat', $siswa->pasien->alamat ?? '') }}</textarea>
                                        @error('alamat')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Data Orang Tua/Wali -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="section-title">
                                        <h5 class="text-info mb-0"><i class="fas fa-users mr-2"></i> Data Orang Tua/Wali</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik_ortu">NIK Orang Tua <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nik_ortu') is-invalid @enderror" 
                                               id="nik_ortu" name="nik_ortu" value="{{ old('nik_ortu', $siswa->nik_ortu) }}" required maxlength="16" 
                                               placeholder="Masukkan 16 digit NIK orang tua">
                                        @error('nik_ortu')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">NIK harus 16 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_ortu">Nama Orang Tua <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama_ortu') is-invalid @enderror" 
                                               id="nama_ortu" name="nama_ortu" value="{{ old('nama_ortu', $siswa->nama_ortu) }}" required>
                                        @error('nama_ortu')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jenis_kelamin_ortu">Jenis Kelamin Orang Tua <span class="text-danger">*</span></label>
                                        <select class="form-control @error('jenis_kelamin_ortu') is-invalid @enderror" 
                                                id="jenis_kelamin_ortu" name="jenis_kelamin_ortu" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin_ortu', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin_ortu', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin_ortu')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggal_lahir_ortu">Tanggal Lahir Orang Tua <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('tanggal_lahir_ortu') is-invalid @enderror" 
                                               id="tanggal_lahir_ortu" name="tanggal_lahir_ortu" 
                                               value="{{ old('tanggal_lahir_ortu', $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d') : '') }}" required>
                                        @error('tanggal_lahir_ortu')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="no_telepon_ortu">No. Telepon Orang Tua</label>
                                        <input type="text" class="form-control @error('no_telepon_ortu') is-invalid @enderror" 
                                               id="no_telepon_ortu" name="no_telepon_ortu" value="{{ old('no_telepon_ortu', $siswa->no_telepon_ortu) }}" 
                                               placeholder="Contoh: 08123456789">
                                        @error('no_telepon_ortu')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Format: 08xxxxxxxxxx atau +62xxxxxxxxxx</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="status">Status Orang Tua</label>
                                        <input type="text" class="form-control @error('status') is-invalid @enderror" 
                                               id="status" name="status" value="{{ old('status', $siswa->status) }}" 
                                               placeholder="Status Orang Tua">
                                        @error('status')
                                             <span class="invalid-feedback">{{ $message }}</span>
                                         @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Data Sekolah -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="section-title">
                                        <h5 class="text-success mb-0"><i class="fas fa-school mr-2"></i> Data Sekolah</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_sekolah">Sekolah <span class="text-danger">*</span></label>
                                        <select class="form-control @error('id_sekolah') is-invalid @enderror" 
                                                id="id_sekolah" name="id_sekolah" required>
                                            <option value="">Pilih Sekolah</option>
                                            @foreach($daftarSekolah as $sekolah)
                                                <option value="{{ $sekolah->id_sekolah }}" 
                                                        {{ old('id_sekolah', $siswa->id_sekolah) == $sekolah->id_sekolah ? 'selected' : '' }}>
                                                    {{ $sekolah->nama_sekolah }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_sekolah')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Sekolah saat ini: Data sekolah tersimpan dengan ID {{ $siswa->id_sekolah }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_kelas">Kelas <span class="text-danger">*</span></label>
                                        <select class="form-control @error('id_kelas') is-invalid @enderror" 
                                                id="id_kelas" name="id_kelas" required>
                                            <option value="">Pilih Kelas</option>
                                            @foreach($daftarKelas as $kelas)
                                                <option value="{{ $kelas->id_kelas }}" 
                                                        {{ old('id_kelas', $siswa->id_kelas) == $kelas->id_kelas ? 'selected' : '' }}>
                                                    {{ $kelas->kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_kelas')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Kelas saat ini: Data kelas tersimpan dengan ID {{ $siswa->id_kelas }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status_siswa">Status Siswa <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status_siswa') is-invalid @enderror" 
                                                id="status_siswa" name="status_siswa" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Aktif" {{ old('status_siswa', $siswa->status_siswa) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Pindah" {{ old('status_siswa', $siswa->status_siswa) == 'Pindah' ? 'selected' : '' }}>Pindah</option>
                                            <option value="Lulus" {{ old('status_siswa', $siswa->status_siswa) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                            <option value="Drop Out" {{ old('status_siswa', $siswa->status_siswa) == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                                        </select>
                                        @error('status_siswa')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Jenis Sekolah dan Kelurahan -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jenis_sekolah">Jenis Sekolah <span class="text-danger">*</span></label>
                                        <select class="form-control @error('jenis_sekolah') is-invalid @enderror" 
                                                id="jenis_sekolah" name="jenis_sekolah" required>
                                            <option value="">Pilih Jenis Sekolah</option>
                                            @foreach($daftarJenisSekolah as $jenisSekolah)
                                                <option value="{{ $jenisSekolah->id }}" 
                                                    {{ old('jenis_sekolah', $siswa->sekolah->id_jenis_sekolah ?? '') == $jenisSekolah->id ? 'selected' : '' }}>
                                                    {{ $jenisSekolah->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jenis_sekolah')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kelurahan">Kelurahan <span class="text-danger">*</span></label>
                                        <select class="form-control @error('kelurahan') is-invalid @enderror" 
                                                id="kelurahan" name="kelurahan" required>
                                            <option value="">Pilih Kelurahan</option>
                                            @foreach($daftarKelurahan as $kelurahan)
                                                <option value="{{ $kelurahan->kd_kel }}" 
                                                    {{ old('kelurahan', $siswa->sekolah->kd_kel ?? '') == $kelurahan->kd_kel ? 'selected' : '' }}>
                                                    {{ $kelurahan->nm_kel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kelurahan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-save mr-2"></i> Update Data Siswa
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-lg" onclick="resetForm()">
                                        <i class="fas fa-undo mr-2"></i> Reset
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('ilp.data-siswa-sekolah.show', $siswa->id) }}" class="btn btn-info btn-lg">
                                        <i class="fas fa-eye mr-2"></i> Lihat Detail
                                    </a>
                                    <a href="{{ route('ilp.data-siswa-sekolah.index') }}" class="btn btn-warning btn-lg">
                                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .animate__animated {
        animation-duration: 0.5s;
    }
    
    .animate__fadeIn {
        animation-name: fadeIn;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .text-danger {
        font-weight: bold;
    }
    
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
    }
    
    .invalid-feedback {
        display: block;
    }
    
    hr {
        border-top: 2px solid #dee2e6;
        margin: 1rem 0;
    }
    
    .btn {
        margin-right: 5px;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .form-text {
        font-size: 0.875rem;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
    }
    
    .breadcrumb {
        font-size: 0.875rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        color: #6c757d;
    }
    
    .section-title {
        border-left: 4px solid #007bff;
        padding-left: 15px;
        margin-bottom: 20px;
    }
    
    .loading-overlay {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background: rgba(255, 255, 255, 0.8);
         display: none;
         justify-content: center;
         align-items: center;
         z-index: 9999;
     }
     
     .btn-lg {
         padding: 0.75rem 1.5rem;
         font-size: 1.1rem;
     }
     
     .form-control:invalid {
         border-color: #dc3545;
     }
     
     .form-control:valid {
         border-color: #28a745;
     }
     
     .tooltip-inner {
         background-color: #007bff;
     }
     
     .tooltip.bs-tooltip-top .arrow::before {
         border-top-color: #007bff;
     }
     
     .auto-save-indicator {
         position: fixed;
         top: 20px;
         right: 20px;
         background: #28a745;
         color: white;
         padding: 8px 16px;
         border-radius: 20px;
         font-size: 0.875rem;
         display: none;
         z-index: 1000;
     }
</style>
@stop

@section('js')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Load kelas berdasarkan sekolah yang dipilih
        $('#id_sekolah').change(function() {
            var sekolahId = $(this).val();
            var kelasSelect = $('#id_kelas');
            
            kelasSelect.html('<option value="">Loading...</option>');
            
            if (sekolahId) {
                $.ajax({
                    url: '{{ route("ilp.get-kelas-by-sekolah") }}',
                    type: 'GET',
                    data: { id_sekolah: sekolahId },
                    success: function(response) {
                        kelasSelect.html('<option value="">Pilih Kelas</option>');
                         $.each(response, function(index, kelas) {
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

        // Enhanced form validation and submission
        $('#editSiswaForm').submit(function(e) {
            e.preventDefault();
            
            // Run validation
            var validation = validateForm();
            
            if (!validation.isValid) {
                // Show validation errors
                var errorHtml = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><h5><i class="icon fas fa-ban"></i> Terdapat Kesalahan!</h5><ul class="mb-0">';
                validation.errors.forEach(function(error) {
                    errorHtml += '<li>' + error + '</li>';
                });
                errorHtml += '</ul></div>';
                
                // Remove existing alerts and add new one
                $('.alert').remove();
                $('.card-body').prepend(errorHtml);
                
                // Scroll to first error
                scrollToFirstError();
                return false;
            }
            
            // Show confirmation dialog with better styling
            Swal.fire({
                title: 'Konfirmasi Update',
                text: 'Apakah Anda yakin ingin mengupdate data siswa ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    showLoading();
                    
                    // Disable submit button
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
                    
                    // Submit form
                    this.submit();
                }
            });
        });
    });

    function resetForm() {
        Swal.fire({
            title: 'Konfirmasi Reset',
            text: 'Apakah Anda yakin ingin mereset form? Semua perubahan akan hilang.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('editSiswaForm').reset();
                // Reset kelas dropdown
                $('#id_kelas').html('<option value="">Pilih Kelas</option>');
                // Clear validation states
                $('.form-control').removeClass('is-invalid');
                $('.custom-invalid-feedback').remove();
                $('.alert').remove();
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Form telah direset.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    // Auto format NIK input
    $('#nik').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
    });

    // Auto format phone number
    $('#no_telepon_ortu').on('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, '');
        
        // Validasi format nomor telepon Indonesia
        var phone = this.value;
        if (phone.length > 0 && !phone.match(/^(\+62|62|0)/)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Nomor telepon harus dimulai dengan 08, 62, atau +62</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    // Loading overlay functions
    function showLoading() {
        if (!$('.loading-overlay').length) {
            $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        }
        $('.loading-overlay').show();
    }
    
    function hideLoading() {
        $('.loading-overlay').hide();
    }
    
    // Enhanced form validation with better UX
    function validateForm() {
        var isValid = true;
        var errors = [];
        
        // Clear previous validation states
        $('.form-control').removeClass('is-invalid');
        $('.custom-invalid-feedback').remove();
        
        // Required fields validation
        var requiredFields = {
            'nik': 'NIK',
            'nama_siswa': 'Nama Siswa',
            'jenis_kelamin': 'Jenis Kelamin',
            'tempat_lahir': 'Tempat Lahir',
            'tanggal_lahir': 'Tanggal Lahir',
            'alamat': 'Alamat',
            'id_sekolah': 'Sekolah',
            'id_kelas': 'Kelas',
            'status': 'Status'
        };
        
        $.each(requiredFields, function(field, label) {
            var value = $('#' + field).val();
            if (!value || value.trim() === '') {
                $('#' + field).addClass('is-invalid');
                if (!$('#' + field).next('.invalid-feedback').length) {
                    $('#' + field).after('<div class="invalid-feedback custom-invalid-feedback">' + label + ' wajib diisi</div>');
                }
                errors.push(label + ' wajib diisi');
                isValid = false;
            }
        });
        
        // NIK validation
        var nik = $('#nik').val();
        if (nik && (nik.length !== 16 || !/^\d{16}$/.test(nik))) {
            $('#nik').addClass('is-invalid');
            if (!$('#nik').next('.invalid-feedback').length) {
                $('#nik').after('<div class="invalid-feedback custom-invalid-feedback">NIK harus 16 digit angka</div>');
            }
            errors.push('NIK harus 16 digit angka');
            isValid = false;
        }
        
        // Date validations
        var tanggalLahir = new Date($('#tanggal_lahir').val());
        var today = new Date();
        
        if (tanggalLahir > today) {
            $('#tanggal_lahir').addClass('is-invalid');
            if (!$('#tanggal_lahir').next('.invalid-feedback').length) {
                $('#tanggal_lahir').after('<div class="invalid-feedback custom-invalid-feedback">Tanggal lahir tidak boleh di masa depan</div>');
            }
            errors.push('Tanggal lahir tidak boleh di masa depan');
            isValid = false;
        }
        
        // Phone number validation
        var phone = $('#no_telepon_ortu').val();
        if (phone && !phone.match(/^(\+62|62|0)\d{8,13}$/)) {
            $('#no_telepon_ortu').addClass('is-invalid');
            if (!$('#no_telepon_ortu').next('.invalid-feedback').length) {
                $('#no_telepon_ortu').after('<div class="invalid-feedback custom-invalid-feedback">Format nomor telepon tidak valid</div>');
            }
            errors.push('Format nomor telepon tidak valid');
            isValid = false;
        }
        
        return { isValid: isValid, errors: errors };
    }
    
    // Real-time validation
    $('.form-control').on('blur', function() {
        validateForm();
    });
    
    // Smooth scroll to first error
    function scrollToFirstError() {
        var firstError = $('.is-invalid').first();
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
            firstError.focus();
         }
     }
     
     // Keyboard shortcuts
     $(document).keydown(function(e) {
         // Ctrl+S to save
         if (e.ctrlKey && e.which === 83) {
             e.preventDefault();
             $('#submitBtn').click();
         }
         
         // Ctrl+R to reset
         if (e.ctrlKey && e.which === 82) {
             e.preventDefault();
             resetForm();
         }
         
         // Escape to go back
         if (e.which === 27) {
             window.location.href = '{{ route("ilp.data-siswa-sekolah.index") }}';
         }
     });
     
     // Auto-save draft functionality (optional)
     var autoSaveTimer;
     $('.form-control').on('input change', function() {
         clearTimeout(autoSaveTimer);
         autoSaveTimer = setTimeout(function() {
             saveDraft();
         }, 3000); // Auto-save after 3 seconds of inactivity
     });
     
     function saveDraft() {
         var formData = $('#editSiswaForm').serialize();
         localStorage.setItem('siswa_edit_draft_{{ $siswa->id }}', formData);
         
         // Show auto-save indicator
         if (!$('.auto-save-indicator').length) {
             $('body').append('<div class="auto-save-indicator"><i class="fas fa-check mr-1"></i>Draft tersimpan</div>');
         }
         $('.auto-save-indicator').fadeIn().delay(2000).fadeOut();
     }
     
     // Load draft on page load
     function loadDraft() {
         var draft = localStorage.getItem('siswa_edit_draft_{{ $siswa->id }}');
         if (draft) {
             // Parse and populate form with draft data if needed
             console.log('Draft tersedia:', draft);
         }
     }
     
     // Initialize tooltips
     $('[data-toggle="tooltip"]').tooltip();
     
     // Add tooltips to form fields
     $('#nis').attr('data-toggle', 'tooltip').attr('title', 'Nomor Induk Siswa');
     $('#nisn').attr('data-toggle', 'tooltip').attr('title', 'Nomor Induk Siswa Nasional');
     $('#nik').attr('data-toggle', 'tooltip').attr('title', 'Nomor Induk Kependudukan (16 digit)');
     
     // Initialize tooltips after adding them
     $('[data-toggle="tooltip"]').tooltip();
     
     // Clear draft after successful submission
     $('#editSiswaForm').on('submit', function() {
         localStorage.removeItem('siswa_edit_draft_{{ $siswa->id }}');
     });
     
     // Load draft on page load
     loadDraft();
 </script>
@stop