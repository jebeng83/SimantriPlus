<form id="skriningIndraForm">
   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">1. Bagaimana pendengaran Anda? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pendengaran_normal" name="pendengaran" value="Normal"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pendengaran_normal">Normal</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="pendengaran_gangguan" name="pendengaran" value="Gangguan pendengaran"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="pendengaran_gangguan">Gangguan pendengaran</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card mb-3">
      <div class="card-body">
         <div class="form-group">
            <label class="font-weight-bold">2. Bagaimana penglihatan Anda? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="penglihatan_normal" name="penglihatan" value="Normal"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="penglihatan_normal">Normal</label>
               </div>
               <div class="custom-control custom-radio mb-2">
                  <input type="radio" id="penglihatan_kacamata" name="penglihatan" value="Menggunakan Kacamata"
                     class="custom-control-input" required>
                  <label class="custom-control-label" for="penglihatan_kacamata">Menggunakan Kacamata</label>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Kirim</button>
   </div> --}}
</form>