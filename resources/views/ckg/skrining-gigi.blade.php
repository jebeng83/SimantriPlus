<form id="skriningGigiForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Gigi Karies/Berlubang <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="karies_ya" name="karies" value="Ya" class="custom-control-input" required>
                  <label class="custom-control-label" for="karies_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="karies_tidak" name="karies" value="Tidak" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="karies_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Gigi Hilang <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hilang_ya" name="hilang" value="Ya" class="custom-control-input" required>
                  <label class="custom-control-label" for="hilang_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hilang_tidak" name="hilang" value="Tidak" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="hilang_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">3. Gigi Goyang <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="goyang_ya" name="goyang" value="Ya" class="custom-control-input" required>
                  <label class="custom-control-label" for="goyang_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="goyang_tidak" name="goyang" value="Tidak" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="goyang_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">4. Berapa jumlah gigi karies? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="jumlah_karies_tidak" name="jumlah_karies" value="Tidak ada"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="jumlah_karies_tidak">Tidak ada</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="jumlah_karies_1" name="jumlah_karies" value="1"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="jumlah_karies_1">1</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="jumlah_karies_2" name="jumlah_karies" value="2"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="jumlah_karies_2">2</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="jumlah_karies_3" name="jumlah_karies" value="3"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="jumlah_karies_3">3</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="jumlah_karies_gt3" name="jumlah_karies" value="> 3"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="jumlah_karies_gt3">&gt; 3</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>