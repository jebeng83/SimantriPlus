<form id="skriningPumaForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah anda sedang/mempunyai riwayat merokok? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="riwayat_merokok_ya" name="riwayat_merokok" value="Ya"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="riwayat_merokok_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="riwayat_merokok_tidak" name="riwayat_merokok" value="Tidak"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="riwayat_merokok_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Apakah Anda pernah merasa napas pendek ketika berjalan lebih cepat pada
               jalan yang datar atau pada jalan yang sedikit menanjak? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="napas_pendek_ya" name="napas_pendek" value="Ya"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="napas_pendek_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="napas_pendek_tidak" name="napas_pendek" value="Tidak"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="napas_pendek_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">3. Apakah Anda biasanya mempunyai dahak yang berasal dari paru atau
               kesulitan mengeluarkan dahak saat Anda sedang tidak menderita selesma/flu? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="dahak_ya" name="dahak" value="Ya"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="dahak_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="dahak_tidak" name="dahak" value="Tidak"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="dahak_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">4. Apakah Anda biasanya batuk saat sedang tidak menderita selesma/flu? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="batuk_puma_ya" name="batuk_puma" value="Ya"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="batuk_puma_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="batuk_puma_tidak" name="batuk_puma" value="Tidak"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="batuk_puma_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">5. Apakah Dokter atau tenaga medis lainnya pernah meminta Anda untuk
               melakukan pemeriksaan spirometri atau peak flow meter (meniup ke dalam suatu alat) untuk mengetahui
               fungsi paru? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="spirometri_ya" name="spirometri" value="Ya"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="spirometri_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2 form-check">
                  <input type="radio" id="spirometri_tidak" name="spirometri" value="Tidak"
                     class="custom-control-input form-check-input" required>
                  <label class="custom-control-label form-check-label" for="spirometri_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>