<form id="skriningIndraForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apa Hasil Pemeriksaan Telinga Luar (serumen impaksi)? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hasil_serumen_tidak" name="hasil_serumen" value="Tidak ada serumen impaksi"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hasil_serumen_tidak">Tidak ada serumen impaksi</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hasil_serumen_ada" name="hasil_serumen" value="Ada serumen impaksi"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hasil_serumen_ada">Ada serumen impaksi</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Apa Hasil Pemeriksaan Telinga Luar (infeksi telinga)? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hasil_infeksi_telinga_tidak" name="hasil_infeksi_telinga" value="Tidak ada infeksi telinga"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hasil_infeksi_telinga_tidak">Tidak ada infeksi telinga</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="hasil_infeksi_telinga_ada" name="hasil_infeksi_telinga" value="Ada infeksi telinga"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="hasil_infeksi_telinga_ada">Ada infeksi telinga</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">3. Hasil pemeriksaan tajam pendengaran <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pendengaran_normal" name="pendengaran" value="Normal"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pendengaran_normal">Normal</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pendengaran_curiga" name="pendengaran" value="Curiga gangguan pendengaran"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pendengaran_curiga">Curiga gangguan pendengaran</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">4. Apa hasil skrining tajam penglihatan? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="penglihatan_normal" name="penglihatan" value="Normal (visus 6/6 - 6/12)"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="penglihatan_normal">Normal (visus 6/6 - 6/12)</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="penglihatan_curiga" name="penglihatan" value="Curiga gangguan penglihatan (visus <6/12)"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="penglihatan_curiga">Curiga gangguan penglihatan (visus &lt;6/12)</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">5. Hasil pemeriksaan pupil <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pupil_normal" name="pupil" value="Normal"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pupil_normal">Normal</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pupil_curiga_katarak" name="pupil" value="Curiga Katarak"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pupil_curiga_katarak">Curiga Katarak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>
