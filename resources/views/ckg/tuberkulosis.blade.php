<form id="tuberkulosisForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah batuk berdahak ≥ 2 minggu berturut-turut? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="batuk_ya" name="batuk_berdahak" value="Ya" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="batuk_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="batuk_tidak" name="batuk_berdahak" value="Tidak" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="batuk_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Apakah demam tinggi ≥ 2 minggu berturut-turut? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="demam_ya" name="demam" value="Ya" class="custom-control-input" required>
                  <label class="custom-control-label" for="demam_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="demam_tidak" name="demam" value="Tidak" class="custom-control-input" required>
                  <label class="custom-control-label" for="demam_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>