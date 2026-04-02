<form id="tuberkulosisForm">
    <!-- Pertanyaan 1: Riwayat Kontak -->
    <div class="card mb-3">
       <div class="card-body">
          <div class="form-group">
             <label class="font-weight-bold">1. Apakah anda ada kontak dengan pasien Tuberkulosis (TBC)? <span class="text-danger">*</span></label>
             <div class="mt-2">
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="riwayat_tbc_serumah" name="riwayat_tbc" value="Riwayat kontak serumah" class="custom-control-input" required onchange="toggleJenisTBC(true)">
                   <label class="custom-control-label" for="riwayat_tbc_serumah">Riwayat kontak serumah</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="riwayat_tbc_erat" name="riwayat_tbc" value="Riwayat kontak erat" class="custom-control-input" required onchange="toggleJenisTBC(true)">
                   <label class="custom-control-label" for="riwayat_tbc_erat">Riwayat kontak erat</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="riwayat_tbc_tidak_ada" name="riwayat_tbc" value="Tidak ada" class="custom-control-input" required onchange="toggleJenisTBC(false)">
                   <label class="custom-control-label" for="riwayat_tbc_tidak_ada">Tidak ada</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="riwayat_tbc_tidak_diketahui" name="riwayat_tbc" value="Tidak diketahui" class="custom-control-input" required onchange="toggleJenisTBC(false)">
                   <label class="custom-control-label" for="riwayat_tbc_tidak_diketahui">Tidak diketahui</label>
                </div>
             </div>
             
             <!-- Pertanyaan 1.1: Jenis TBC -->
             <div id="container_jenis_tbc" style="display: none;" class="mt-3 pl-4 border-left">
                <label class="font-weight-bold">1.1 Apakah jenis TBC dari pasien Tuberkulosis (TBC) yang berkontak dengan anda? <span class="text-danger">*</span></label>
                <div class="mt-2">
                    <div class="custom-control custom-radio mb-2">
                       <input type="radio" id="jenis_tbc_bakteriologis" name="jenis_tbc" value="Bakteriologis" class="custom-control-input">
                       <label class="custom-control-label" for="jenis_tbc_bakteriologis">Bakteriologis</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                       <input type="radio" id="jenis_tbc_klinis" name="jenis_tbc" value="Klinis" class="custom-control-input">
                       <label class="custom-control-label" for="jenis_tbc_klinis">Klinis</label>
                    </div>
                </div>
             </div>
          </div>
       </div>
    </div>

    <!-- Pertanyaan 2 (Lama): Batuk -->
    <div class="card mb-3">
       <div class="card-body">
          <div class="form-group">
             <label class="font-weight-bold">2. Apakah Anda pernah atau sedang mengalami batuk yang tidak sembuh-sembuh selama lebih dari 2 minggu? <span class="text-danger">*</span></label>
             <div class="mt-2">
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="batuk_ya" name="batuk_berdahak" value="Ya" class="custom-control-input" required>
                   <label class="custom-control-label" for="batuk_ya">Ya</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="batuk_tidak" name="batuk_berdahak" value="Tidak" class="custom-control-input" required>
                   <label class="custom-control-label" for="batuk_tidak">Tidak</label>
                </div>
             </div>
          </div>
       </div>
    </div>
 
    <!-- Pertanyaan 3 (Lama): Demam -->
    <div class="card mb-3">
       <div class="card-body">
          <div class="form-group">
             <label class="font-weight-bold">3. Apakah Anda tinggal serumah atau sering bertemu dengan orang yang menderita Tuberkulosis (TBC) atau batuk berkepanjangan? <span class="text-danger">*</span></label>
             <div class="mt-2">
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="demam_ya" name="demam" value="Ya" class="custom-control-input" required>
                   <label class="custom-control-label" for="demam_ya">Ya</label>
                </div>
                <div class="custom-control custom-radio mb-2">
                   <input type="radio" id="demam_tidak" name="demam" value="Tidak" class="custom-control-input" required>
                   <label class="custom-control-label" for="demam_tidak">Tidak</label>
                </div>
             </div>
          </div>
       </div>
    </div>
 
    <script>
        function toggleJenisTBC(show) {
            var container = document.getElementById('container_jenis_tbc');
            var inputs = container.querySelectorAll('input[name="jenis_tbc"]');
            
            if (show) {
                container.style.display = 'block';
                inputs.forEach(function(input) { input.required = true; });
            } else {
                container.style.display = 'none';
                inputs.forEach(function(input) { 
                    input.required = false; 
                    input.checked = false;
                });
            }
        }
    </script>
 </form>