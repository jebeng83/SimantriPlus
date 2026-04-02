<form id="antropometriLabForm">
   <div class="card mb-3">
      <div class="card-body">
         <!-- 1. Riwayat DM -->
         <div class="form-group border-bottom pb-3">
             <label class="font-weight-bold">1. Apakah Anda pernah dinyatakan diabetes atau kencing manis oleh Dokter? <span class="text-danger">*</span></label>
             <div class="mt-2">
                 <div class="custom-control custom-radio custom-control-inline">
                     <input type="radio" id="riwayat_dm_ya" name="riwayat_dm" value="Ya" class="custom-control-input" required onchange="toggleLamaRiwayatDmDewasa(this.value)">
                     <label class="custom-control-label" for="riwayat_dm_ya">Ya</label>
                 </div>
                 <div class="custom-control custom-radio custom-control-inline">
                     <input type="radio" id="riwayat_dm_tidak" name="riwayat_dm" value="Tidak" class="custom-control-input" required onchange="toggleLamaRiwayatDmDewasa(this.value)">
                     <label class="custom-control-label" for="riwayat_dm_tidak">Tidak</label>
                 </div>
             </div>
         </div>

         <!-- 2. Lama Riwayat DM Dewasa -->
         <div class="form-group border-bottom pb-3" id="lama-riwayat-dm-wrapper" style="display: none;">
            <label class="font-weight-bold">
               2. Sudah Berapa Bulan Anda Didiagnosis Diabetes Melitus Oleh Dokter? Isi Total Bulan Sejak didiagnosis dokter hingga saat ini, misal didiagnosis 1 tahun yang lalu = 12, dst <span class="text-danger">*</span>
            </label>
            <input type="number" class="form-control mt-2" name="lama_riwayat_dm_dewasa" id="lama_riwayat_dm_dewasa"
               min="0" step="1" placeholder="Isi Total Bulan">
         </div>

         <!-- 3. Riwayat HT -->
         <div class="form-group border-bottom pb-3">
             <label class="font-weight-bold">3. Apakah Anda pernah dinyatakan tekanan darah tinggi? <span class="text-danger">*</span></label>
             <div class="mt-2">
                 <div class="custom-control custom-radio custom-control-inline">
                     <input type="radio" id="riwayat_ht_ya" name="riwayat_ht" value="Ya" class="custom-control-input" required onchange="toggleLamaRiwayatHtDewasa(this.value)">
                     <label class="custom-control-label" for="riwayat_ht_ya">Ya</label>
                 </div>
                 <div class="custom-control custom-radio custom-control-inline">
                     <input type="radio" id="riwayat_ht_tidak" name="riwayat_ht" value="Tidak" class="custom-control-input" required onchange="toggleLamaRiwayatHtDewasa(this.value)">
                     <label class="custom-control-label" for="riwayat_ht_tidak">Tidak</label>
                 </div>
             </div>
         </div>

         <!-- 4. Lama Riwayat HT Dewasa -->
         <div class="form-group border-bottom pb-3" id="lama-riwayat-ht-wrapper" style="display: none;">
            <label class="font-weight-bold">
               4. Sudah Berapa Bulan Anda Didiagnosis Hipertensi Oleh Dokter? Isi Total Bulan Sejak didiagnosis dokter hingga saat ini, misal didiagnosis 1 tahun yang lalu = 12, dst <span class="text-danger">*</span>
            </label>
            <input type="number" class="form-control mt-2" name="lama_riwayat_ht_dewasa" id="lama_riwayat_ht_dewasa"
               min="0" step="1" placeholder="Isi jumlah bulan">
         </div>

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

         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">Tekanan Darah 1 (mmHg) <span class="text-danger">*</span></label>
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

         <!-- 3. Tekanan Darah 2 -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">Tekanan Darah 2 (mmHg)</label>
            <div class="row">
               <div class="col-md-6">
                  <div class="input-group">
                     <input type="number" class="form-control" name="tekanan_sistolik_2" min="0" max="300"
                        placeholder="Sistolik">
                     <div class="input-group-append">
                        <span class="input-group-text">/</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <input type="number" class="form-control" name="tekanan_diastolik_2" min="0" max="200"
                     placeholder="Diastolik">
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
            <input type="number" class="form-control" name="kolesterol_lab" min="0" max="1000" step="0.1">
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

<script>
   function toggleLamaRiwayatDmDewasa(jawaban) {
      var wrapper = document.getElementById('lama-riwayat-dm-wrapper');
      var input = document.getElementById('lama_riwayat_dm_dewasa');
      if (!wrapper || !input) return;

      if (jawaban === 'Ya') {
         wrapper.style.display = 'block';
         input.required = true;
      } else {
         wrapper.style.display = 'none';
         input.required = false;
         input.value = '';
      }
   }

   function toggleLamaRiwayatHtDewasa(jawaban) {
      var wrapper = document.getElementById('lama-riwayat-ht-wrapper');
      var input = document.getElementById('lama_riwayat_ht_dewasa');
      if (!wrapper || !input) return;

      if (jawaban === 'Ya') {
         wrapper.style.display = 'block';
         input.required = true;
      } else {
         wrapper.style.display = 'none';
         input.required = false;
         input.value = '';
      }
   }
</script>
