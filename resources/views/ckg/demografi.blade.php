<?php
$statusPerkawinan = [
    'Belum Menikah',
    'Menikah',
    'Cerai Mati',
    'Cerai Hidup'
];
?>

<form id="demografiForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Status Perkawinan <span class="text-danger">*</span></label>
            <div class="mt-2">
               @foreach($statusPerkawinan as $status)
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="status_{{ Str::slug($status) }}" name="status_perkawinan"
                     value="{{ $status }}" class="custom-control-input" required>
                  <label class="custom-control-label" for="status_{{ Str::slug($status) }}">{{ $status
                     }}</label>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3" id="pertanyaan_hamil" style="display: none;">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Apakah Anda sedang hamil? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hamil_ya" name="status_hamil" value="Ya" class="custom-control-input">
                  <label class="custom-control-label" for="hamil_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hamil_tidak" name="status_hamil" value="Tidak" class="custom-control-input">
                  <label class="custom-control-label" for="hamil_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">3. Apakah Anda penyandang disabilitas? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="disabilitas_non" name="status_disabilitas" value="Non disabilitas"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="disabilitas_non">Non disabilitas</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="disabilitas_ya" name="status_disabilitas" value="Penyandang disabilitas"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="disabilitas_ya">Penyandang disabilitas</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>

<script>
   window.addEventListener('load', function() {
      if (typeof jQuery === 'undefined') { return; }
      jQuery(function($) {
         function checkJenisKelamin() {
            var jenisKelamin = $('#jenis_kelamin').val();
            if (jenisKelamin === 'P') {
               $('#pertanyaan_hamil').show();
               $('#hamil_ya, #hamil_tidak').prop('required', true);
            } else {
               $('#pertanyaan_hamil').hide();
               $('#hamil_ya, #hamil_tidak').prop('required', false);
               $('input[name="status_hamil"]').prop('checked', false);
            }
         }
         checkJenisKelamin();
         $(document).on('change', '#jenis_kelamin', function() {
            checkJenisKelamin();
         });
      });
   });
</script>
