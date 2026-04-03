<form id="tekananDarahForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah Anda pernah dinyatakan tekanan darah tinggi? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hipertensi_ya" name="riwayat_hipertensi" value="Ya"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hipertensi_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hipertensi_tidak" name="riwayat_hipertensi" value="Tidak"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hipertensi_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Apakah Anda pernah dinyatakan diabetes atau kencing manis? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="diabetes_ya" name="riwayat_diabetes" value="Ya" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="diabetes_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="diabetes_tidak" name="riwayat_diabetes" value="Tidak"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="diabetes_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>