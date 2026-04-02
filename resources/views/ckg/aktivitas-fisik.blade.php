<form id="aktivitasFisikForm">
   <div class="card mb-3">
      <div class="card-body">
         
         <!-- 1. Rumah Tangga -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">1. Apakah Anda melakukan aktivitas fisik sedang pada kegiatan rumah tangga/domestik seperti membersihkan rumah/lingkungan (menyapu, menata perabotan), mencuci baju manual, memasak, mengasuh anak, atau mengangkat beban dengan berat < 20 kg? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="frekuensi_olahraga_ya" name="frekuensi_olahraga" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(1, true)">
                  <label class="custom-control-label" for="frekuensi_olahraga_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="frekuensi_olahraga_tidak" name="frekuensi_olahraga" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(1, false)">
                  <label class="custom-control-label" for="frekuensi_olahraga_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_1" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="frekuensi_olahraga_1" id="frekuensi_olahraga_1" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="frekuensi_olahraga_2" id="frekuensi_olahraga_2" placeholder="Menit" min="1">
                </div>
            </div>
         </div>

         <!-- 2. Tempat Kerja Sedang -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">2. Apakah Anda melakukan aktivitas fisik sedang pada tempat kerja seperti pekerjaan dengan mengangkat beban, memberi makan ternak, berkebun dan membersihkan kendaraan (motor/mobil/perahu)? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_2_ya" name="aktivitas_fisik_2" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(2, true)">
                  <label class="custom-control-label" for="aktivitas_fisik_2_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_2_tidak" name="aktivitas_fisik_2" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(2, false)">
                  <label class="custom-control-label" for="aktivitas_fisik_2_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_2" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_2_hari" id="aktivitas_fisik_2_hari" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_2_menit" id="aktivitas_fisik_2_menit" placeholder="Menit" min="1">
                </div>
            </div>
         </div>

         <!-- 3. Perjalanan -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">3. Apakah Anda melakukan aktivitas fisik sedang dalam perjalanan seperti berjalan kaki atau bersepeda ke ladang, sawah, pasar dan tempat kerja? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_3_ya" name="aktivitas_fisik_3" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(3, true)">
                  <label class="custom-control-label" for="aktivitas_fisik_3_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_3_tidak" name="aktivitas_fisik_3" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(3, false)">
                  <label class="custom-control-label" for="aktivitas_fisik_3_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_3" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_3_hari" id="aktivitas_fisik_3_hari" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_3_menit" id="aktivitas_fisik_3_menit" placeholder="Menit" min="1">
                </div>
            </div>
         </div>

         <!-- 4. Olahraga Sedang -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">4. Apakah Anda melakukan olahraga intensitas sedang seperti latihan beban < 20 kg, senam aerobic, yoga, bermain bola, bersepeda dan berenang (santai)? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_4_ya" name="aktivitas_fisik_4" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(4, true)">
                  <label class="custom-control-label" for="aktivitas_fisik_4_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_4_tidak" name="aktivitas_fisik_4" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(4, false)">
                  <label class="custom-control-label" for="aktivitas_fisik_4_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_4" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_4_hari" id="aktivitas_fisik_4_hari" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_4_menit" id="aktivitas_fisik_4_menit" placeholder="Menit" min="1">
                </div>
            </div>
         </div>

         <!-- 5. Tempat Kerja Berat -->
         <div class="form-group border-bottom pb-3">
            <label class="font-weight-bold">5. Apakah Anda melakukan aktivitas fisik intensitas berat di tempat kerja seperti mengangkat/memikul beban berat ≥20 kg, mencangkul, menggali, memanen, memanjat pohon, menebang pohon, mengayuh becak, menarik jaring, mendorong atau menarik (mesin pemotong rumput/gerobak/perahu/kendaraan)? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_5_ya" name="aktivitas_fisik_5" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(5, true)">
                  <label class="custom-control-label" for="aktivitas_fisik_5_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_5_tidak" name="aktivitas_fisik_5" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(5, false)">
                  <label class="custom-control-label" for="aktivitas_fisik_5_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_5" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_5_hari" id="aktivitas_fisik_5_hari" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_5_menit" id="aktivitas_fisik_5_menit" placeholder="Menit" min="1">
                </div>
            </div>
         </div>
         
         <!-- 6. Olahraga Berat -->
         <div class="form-group">
            <label class="font-weight-bold">6. Apakah Anda melakukan olahraga intensitas berat seperti bersepeda cepat (>16 km/jam), jalan cepat (>7 km/jam), lari, sepak bola, futsal, bulutangkis, tenis, basket dan lompat tali? <span class="text-danger">*</span></label>
            <div class="mt-2">
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_6_ya" name="aktivitas_fisik_6" value="Ya" class="custom-control-input" required onchange="togglePertanyaanLanjutan(6, true)">
                  <label class="custom-control-label" for="aktivitas_fisik_6_ya">Ya</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="aktivitas_fisik_6_tidak" name="aktivitas_fisik_6" value="Tidak" class="custom-control-input" required onchange="togglePertanyaanLanjutan(6, false)">
                  <label class="custom-control-label" for="aktivitas_fisik_6_tidak">Tidak</label>
               </div>
            </div>
            
            <div id="pertanyaan_lanjutan_6" style="display: none;" class="mt-3 pl-4 border-left">
                <div class="form-group">
                   <label>Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_6_hari" id="aktivitas_fisik_6_hari" placeholder="Hari (1-7)" min="1" max="7">
                </div>
                <div class="form-group">
                   <label>Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut? <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="aktivitas_fisik_6_menit" id="aktivitas_fisik_6_menit" placeholder="Menit" min="1">
                </div>
            </div>
         </div>

      </div>
   </div>
   
   <script>
       function togglePertanyaanLanjutan(index, isYes) {
           var container = document.getElementById('pertanyaan_lanjutan_' + index);
           // Handle field names: for index 1 it's frekuensi_olahraga, for others it's aktivitas_fisik_index
           var prefix = (index === 1) ? 'frekuensi_olahraga' : 'aktivitas_fisik_' + index;
           
           // Field suffixes are inconsistent. 
           // For index 1: fields are frekuensi_olahraga_1 and frekuensi_olahraga_2
           // For index 2-6: fields are aktivitas_fisik_X_hari and aktivitas_fisik_X_menit
           
           var inputHariId = (index === 1) ? 'frekuensi_olahraga_1' : 'aktivitas_fisik_' + index + '_hari';
           var inputMenitId = (index === 1) ? 'frekuensi_olahraga_2' : 'aktivitas_fisik_' + index + '_menit';
           
           var inputHari = document.getElementById(inputHariId);
           var inputMenit = document.getElementById(inputMenitId);
           
           if (isYes) {
               container.style.display = 'block';
               inputHari.setAttribute('required', 'required');
               inputMenit.setAttribute('required', 'required');
           } else {
               container.style.display = 'none';
               inputHari.removeAttribute('required');
               inputMenit.removeAttribute('required');
               inputHari.value = '';
               inputMenit.value = '';
           }
       }
   </script>
</form>