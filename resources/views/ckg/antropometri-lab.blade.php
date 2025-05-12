<form id="antropometriLabForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">Tinggi Badan (cm) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="tinggi_badan" min="0" max="300" step="0.1" required>
         </div>

         <div class="form-group">
            <label class="font-weight-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="berat_badan" min="0" max="500" step="0.1" required>
         </div>

         <div class="form-group">
            <label class="font-weight-bold">Lingkar Perut (cm) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="lingkar_perut" min="0" max="300" step="0.1" required>
         </div>

         <div class="form-group">
            <label class="font-weight-bold">Tekanan Darah (mmHg) <span class="text-danger">*</span></label>
            <div class="row">
               <div class="col-md-6">
                  <div class="input-group">
                     <input type="number" class="form-control" name="tekanan_sistolik" min="0" max="300"
                        placeholder="Sistolik" required>
                     <div class="input-group-append">
                        <span class="input-group-text">/</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <input type="number" class="form-control" name="tekanan_diastolik" min="0" max="200"
                     placeholder="Diastolik" required>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold">GDS (mg/dL)</label>
            <input type="number" class="form-control" name="gds" min="0" max="1000" step="0.1">
         </div>

         <div class="form-group">
            <label class="font-weight-bold">GDP (mg/dL)</label>
            <input type="number" class="form-control" name="gdp" min="0" max="1000" step="0.1">
         </div>

         <div class="form-group">
            <label class="font-weight-bold">Kolesterol (mg/dL)</label>
            <input type="number" class="form-control" name="kolesterol" min="0" max="1000" step="0.1">
         </div>

         <div class="form-group">
            <label class="font-weight-bold">Trigliserida (mg/dL)</label>
            <input type="number" class="form-control" name="trigliserida" min="0" max="1000" step="0.1">
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>