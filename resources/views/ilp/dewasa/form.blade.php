@extends('adminlte::page')

@section('title', 'Form ILP')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1>Form ILP </h1>
   <a href="{{ route('ilp.pendaftaran') }}" class="btn btn-secondary btn-sm">
      <i class="fas fa-arrow-left mr-1"></i> Kembali
   </a>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header bg-primary text-white">
            <h3 class="card-title">Data Pasien</h3>
         </div>
         <div class="card-body">
            <!-- Data Pasien -->
            <div class="row mb-3">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>No. Rekam Medis:</label>
                     <input type="text" class="form-control" value="{{ $pasien->no_rkm_medis ?? '' }}" readonly>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Nama Pasien:</label>
                     <input type="text" class="form-control" value="{{ $pasien->nm_pasien ?? '' }}" readonly>
                  </div>
               </div>
            </div>

            <div class="row mb-3">
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Tempat/Tgl. Lahir:</label>
                     <div class="input-group">
                        <input type="text" class="form-control" value="{{ $pasien->tmp_lahir ?? '' }}" readonly>
                        <input type="text" class="form-control"
                           value="{{ isset($pasien->tgl_lahir) ? date('d/m/Y', strtotime($pasien->tgl_lahir)) : '' }}"
                           readonly>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>Status Nikah:</label>
                     <input type="text" class="form-control" value="{{ $pasien->stts_nikah ?? '' }}" readonly>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label>No. KTP:</label>
                     <input type="text" class="form-control" value="{{ $pasien->no_ktp ?? '' }}" readonly>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Komponen Livewire -->
      <div class="card mt-4">
         <div class="card-header bg-primary text-white">
            <h3 class="card-title">Data Pemeriksaan</h3>
         </div>
         <div class="card-body">
            @livewire('component.ilp-dewasa.form', ['noRawat' => $noRawat])
         </div>
      </div>
   </div>
</div>
@stop

@section('js')
<script>
   document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('ilpDewasaSaved', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data ILP Dewasa berhasil disimpan',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('ralan.pasien') }}";
                }
            });
        });

        window.livewire.on('ilpDewasaDeleted', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data ILP Dewasa berhasil dihapus',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('ralan.pasien') }}";
                }
            });
        });

        window.livewire.on('hapusIlpDewasa', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ILP Dewasa akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('hapusRalanIlpDewasa');
                }
            });
        });
    });
</script>
@stop