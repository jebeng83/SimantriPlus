<form id="kankerLeherRahimForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah pernah melakukan hubungan intim/seksual? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="mb-2">
                  <input type="radio" id="hubungan_intim_ya" name="hubungan_intim" value="Ya" required>
                  <label for="hubungan_intim_ya" style="cursor: pointer; margin-left: 5px;">Ya</label>
               </div>
               <div class="mb-2">
                  <input type="radio" id="hubungan_intim_tidak" name="hubungan_intim" value="Tidak" required>
                  <label for="hubungan_intim_tidak" style="cursor: pointer; margin-left: 5px;">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>

<style>
   /* Gaya tambahan untuk memastikan radio button berfungsi dengan baik */
   input[type="radio"] {
      cursor: pointer;
   }

   label {
      cursor: pointer;
   }
</style>