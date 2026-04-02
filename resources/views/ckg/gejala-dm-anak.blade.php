<form id="form-gejala-dm-anak">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">
               1. Apakah Anak Anda pernah dinyatakan diabetes atau kencing manis oleh Dokter? 
               <span class="text-danger">*</span>
            </label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pernah_dm_ya" name="pernah_dm_oleh_dokter" value="Ya"
                     class="custom-control-input" required onchange="togglePertanyaanGejalaDmAnak(this.value)">
                  <label class="custom-control-label" for="pernah_dm_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pernah_dm_tidak" name="pernah_dm_oleh_dokter" value="Tidak"
                     class="custom-control-input" required onchange="togglePertanyaanGejalaDmAnak(this.value)">
                  <label class="custom-control-label" for="pernah_dm_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Jika jawab Ya -->
   <div class="card mb-3" id="lanjutan-dm-ya" style="display: none;">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">
               2. Sudah Berapa Bulan Anak Anda Didiagnosis Diabetes Melitus Oleh Dokter? 
               <small class="d-block text-muted">
                  Isi total bulan sejak didiagnosis dokter hingga saat ini, misal didiagnosis 1 tahun yang lalu = 12, dst
               </small>
            </label>
            <input type="text" class="form-control" name="lama_anak_dm" id="lama_anak_dm" maxlength="4"
               placeholder="Contoh: 12">
         </div>
      </div>
   </div>

   <!-- Jika jawab Tidak -->
   <div id="lanjutan-dm-tidak" style="display: none;">
      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">
                  2. Apakah anak bapak/ibu sering merasa sangat lapar dan makan lebih banyak dari biasanya? 
                  <span class="text-danger">*</span>
               </label>
               <div class="mt-2">
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="sering_lapar_ya" name="sering_lapar" value="Ya"
                        class="custom-control-input">
                     <label class="custom-control-label" for="sering_lapar_ya">Ya</label>
                  </div>
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="sering_lapar_tidak" name="sering_lapar" value="Tidak"
                        class="custom-control-input">
                     <label class="custom-control-label" for="sering_lapar_tidak">Tidak</label>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">
                  3. Apakah anak bapak/ibu sering merasa haus meskipun sudah banyak minum? 
                  <span class="text-danger">*</span>
               </label>
               <div class="mt-2">
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="sering_haus_ya" name="sering_haus" value="Ya" class="custom-control-input">
                     <label class="custom-control-label" for="sering_haus_ya">Ya</label>
                  </div>
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="sering_haus_tidak" name="sering_haus" value="Tidak"
                        class="custom-control-input">
                     <label class="custom-control-label" for="sering_haus_tidak">Tidak</label>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">
                  4. Apakah anak bapak/ibu tetap mengalami penurunan berat badan meskipun nafsu makan meningkat? 
                  <span class="text-danger">*</span>
               </label>
               <div class="mt-2">
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="berat_turun_ya" name="berat_turun" value="Ya" class="custom-control-input">
                     <label class="custom-control-label" for="berat_turun_ya">Ya</label>
                  </div>
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="berat_turun_tidak" name="berat_turun" value="Tidak"
                        class="custom-control-input">
                     <label class="custom-control-label" for="berat_turun_tidak">Tidak</label>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">
                  5. Apakah bapak/ibu atau anggota keluarga lainnya (saudara kandung) yang pernah di diagnosis Kencing Manis oleh Dokter? 
                  <span class="text-danger">*</span>
               </label>
               <div class="mt-2">
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="riwayat_ortu_ya" name="riwayat_diabetes_ortu" value="Ya"
                        class="custom-control-input">
                     <label class="custom-control-label" for="riwayat_ortu_ya">Ya</label>
                  </div>
                  <div class="custom-control custom-radio mb-2">
                     <input type="radio" id="riwayat_ortu_tidak" name="riwayat_diabetes_ortu" value="Tidak"
                        class="custom-control-input">
                     <label class="custom-control-label" for="riwayat_ortu_tidak">Tidak</label>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<script>
   function togglePertanyaanGejalaDmAnak(jawaban) {
      var yaSection = document.getElementById('lanjutan-dm-ya');
      var tidakSection = document.getElementById('lanjutan-dm-tidak');
      var lamaInput = document.getElementById('lama_anak_dm');

      if (jawaban === 'Ya') {
         yaSection.style.display = 'block';
         tidakSection.style.display = 'none';

         if (lamaInput) {
            lamaInput.required = true;
         }

         // Bersihkan pilihan jika sebelumnya memilih "Tidak"
         ['sering_lapar', 'sering_haus', 'berat_turun', 'riwayat_diabetes_ortu'].forEach(function (name) {
            var radios = document.querySelectorAll('input[name="' + name + '"]');
            radios.forEach(function (r) { r.checked = false; });
         });
      } else if (jawaban === 'Tidak') {
         yaSection.style.display = 'none';
         tidakSection.style.display = 'block';

         if (lamaInput) {
            lamaInput.required = false;
            lamaInput.value = '';
         }
      } else {
         yaSection.style.display = 'none';
         tidakSection.style.display = 'none';

         if (lamaInput) {
            lamaInput.required = false;
            lamaInput.value = '';
         }
      }
   }
</script>