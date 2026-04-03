<form id="perilakuMerokokForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Apakah Anda merokok dalam setahun terakhir ini? <span
                  class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="merokok_ya" name="status_merokok" value="Ya" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="merokok_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="merokok_tidak" name="status_merokok" value="Tidak"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="merokok_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div id="divPertanyaanTambahan" style="display: none;">
      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">2. Sudah berapa tahun Anda merokok? <span
                     class="text-danger">*</span></label>
               <input type="number" class="form-control" name="lama_merokok" min="0">
            </div>
         </div>
      </div>

      <div class="card mb-3">
         <div class="card-body">
            <div class="form-group">
               <label class="font-weight-bold">3. Biasanya, berapa batang rokok yang Anda hisap dalam sehari? <span
                     class="text-danger">*</span></label>
               <input type="number" class="form-control" name="jumlah_rokok" min="1">
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3" id="divPaparanAsap">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">4. Apakah Anda terpapar asap rokok atau menghirup asap rokok dari orang lain
               dalam sebulan terakhir? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="paparan_ya" name="paparan_asap" value="Ya" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="paparan_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="paparan_tidak" name="paparan_asap" value="Tidak" class="custom-control-input"
                     required>
                  <label class="custom-control-label" for="paparan_tidak">Tidak</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>

<script>
   // Tampilkan pertanyaan tambahan berdasarkan pilihan status merokok
   document.querySelectorAll('input[name="status_merokok"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
         if (this.value === 'Ya') {
            document.getElementById('divPertanyaanTambahan').style.display = 'block';
            
            // Tambahkan required pada input lama_merokok dan jumlah_rokok
            document.querySelector('input[name="lama_merokok"]').required = true;
            document.querySelector('input[name="jumlah_rokok"]').required = true;
         } else {
            document.getElementById('divPertanyaanTambahan').style.display = 'none';
            
            // Hapus required pada input lama_merokok dan jumlah_rokok
            document.querySelector('input[name="lama_merokok"]').required = false;
            document.querySelector('input[name="jumlah_rokok"]').required = false;
         }
      });
   });
</script>