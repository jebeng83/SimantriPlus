<!-- Modal Perkembangan (3-6 Tahun) -->
<div class="modal fade" id="modalPerkembangan3_6Tahun" tabindex="-1" role="dialog" aria-labelledby="modalPerkembangan3_6TahunLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalPerkembangan3_6TahunLabel">
               <i class="fas fa-child mr-2"></i>Perkembangan (3-6 Tahun)
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="formPerkembangan3-6Tahun">
               <div class="card mb-3">
                  <div class="card-body">
                     <div class="form-group">
                        <label class="font-weight-bold">1. Apakah anak Anda memiliki satu atau lebih kondisi berikut ini : 1) Sering tantrum (mengganggu aktivitas sehari-hari) ; 2) Mudah menangis atau marah tanpa alasan yang jelas; 3) memukul, menggigit atau mendorong orang lain tanpa alasan yang jelas; 4) Perubahan emosi/perilaku yang menimbulkan gangguan <span class="text-danger">*</span></label>
                        <div class="mt-2">
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="gangguan_emosi_ya" name="gangguan_emosi" value="Ya" class="custom-control-input" required>
                              <label class="custom-control-label" for="gangguan_emosi_ya">Ya</label>
                           </div>
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="gangguan_emosi_tidak" name="gangguan_emosi" value="Tidak" class="custom-control-input" required>
                              <label class="custom-control-label" for="gangguan_emosi_tidak">Tidak</label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="card mb-3">
                  <div class="card-body">
                     <div class="form-group">
                        <label class="font-weight-bold">2. Apakah anak Anda memiliki satu atau lebih kondisi berikut ini : 1) Anak tidak bisa duduk tenang; 2) Anak selalu bergerak tanpa tujuan dan tanpa mengenal lelah; 3) Suasana hati sangat mudah berubah; 4) Emosi anak meledak-ledak/impulsif <span class="text-danger">*</span></label>
                        <div class="mt-2">
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="hiperaktif_ya" name="hiperaktif" value="Ya" class="custom-control-input" required>
                              <label class="custom-control-label" for="hiperaktif_ya">Ya</label>
                           </div>
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="hiperaktif_tidak" name="hiperaktif" value="Tidak" class="custom-control-input" required>
                              <label class="custom-control-label" for="hiperaktif_tidak">Tidak</label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
               <i class="fas fa-times mr-1"></i>Tutup
            </button>
            <button type="button" class="btn btn-primary" onclick="simpanDataForm('perkembangan-3-6-tahun')">
               <i class="fas fa-save mr-1"></i>Simpan Data
            </button>
         </div>
      </div>
   </div>
</div>