{{-- Form Demografi Anak --}}
<form id="form-demografi-anak">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah Anda penyandang disabilitas? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="disabilitas_tidak" name="status_disabilitas" value="Non disabilitas"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="disabilitas_tidak">Non disabilitas</label>
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
</form>