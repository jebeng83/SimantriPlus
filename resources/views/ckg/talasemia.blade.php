<!-- Modal Talasemia -->
<div class="modal fade" id="modalTalasemia" tabindex="-1" role="dialog" aria-labelledby="modalTalasemiaLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTalasemiaLabel">
               <i class="fas fa-heartbeat mr-2"></i>Talasemia
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="formTalasemia">
               <div class="card mb-3">
                  <div class="card-body">
                     <div class="form-group">
                        <label class="font-weight-bold">1. Apakah ada anggota keluarga kandung Anda dinyatakan menderita Talasemia, atau kelainan darah atau pernah menjalani transfusi darah secara rutin? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="riwayat_keluarga_ya" name="riwayat_keluarga" value="Ya" class="custom-control-input" required>
                              <label class="custom-control-label" for="riwayat_keluarga_ya">Ya</label>
                           </div>
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="riwayat_keluarga_tidak" name="riwayat_keluarga" value="Tidak" class="custom-control-input" required>
                              <label class="custom-control-label" for="riwayat_keluarga_tidak">Tidak</label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <div class="card mb-3">
                  <div class="card-body">
                     <div class="form-group">
                        <label class="font-weight-bold">2. Apakah ada anggota keluarga kandung Anda dinyatakan sebagai pembawa sifat talasemia (mereka yang memiliki genetik yang tidak normal sehingga berpotensi menurunkan penyakit Talasemia)? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="pembawa_sifat_ya" name="pembawa_sifat" value="Ya" class="custom-control-input" required>
                              <label class="custom-control-label" for="pembawa_sifat_ya">Ya</label>
                           </div>
                           <div class="custom-control custom-radio mb-2">
                              <input type="radio" id="pembawa_sifat_tidak" name="pembawa_sifat" value="Tidak" class="custom-control-input" required>
                              <label class="custom-control-label" for="pembawa_sifat_tidak">Tidak</label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="simpanDataForm('talasemia')">Simpan</button>
         </div>
      </div>
   </div>
</div>