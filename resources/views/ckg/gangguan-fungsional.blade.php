<form id="form-gangguan-fungsional">
   <div class="card border-0 shadow-sm mb-4">
      <div class="card-body p-4">
         <h5 class="mb-4 font-weight-bold text-primary">Pemeriksaan Gangguan Fungsional/Barthel Index</h5>

         <div class="form-group">
            <label class="font-weight-bold mb-2">1. Dapat mengendalikan rangsang buang air besar (BAB)? <span
                  class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bab" id="bab_2" value="2" required
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bab_2">Terkendali teratur (Mandiri)</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bab" id="bab_1" value="1"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bab_1">Kadang-kadang tak terkendali/butuh bantuan (1x /
                     minggu)</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bab" id="bab_0" value="0"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bab_0">Tidak terkendali/tak teratur (Tidak mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">2. Dapat mengendalikan rangsang berkemih/buar air kecil (BAK)? <span
                  class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bak" id="bak_2" value="2" required
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bak_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bak" id="bak_1" value="1"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bak_1">Kadang-kadang tak terkendali/butuh bantuan (1x / 24
                     jam)</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="bak" id="bak_0" value="0"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="bak_0">Tak terkendali atau pakai kateter (Tidak mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">3. Membersihkan diri (seka wajah, sisir rambut, sikat gigi)? <span
                  class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="membersihkan_diri"
                     id="membersihkan_diri_2" value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="membersihkan_diri_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="membersihkan_diri"
                     id="membersihkan_diri_1" value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="membersihkan_diri_1">Perlu bantuan sebagian</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="membersihkan_diri"
                     id="membersihkan_diri_0" value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="membersihkan_diri_0">Butuh pertolongan orang lain (Tidak
                     mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">4. Penggunaan jamban (keluar masuk jamban, melepas/memakai celana,
               membersihkan, menyiram)? <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="penggunaan_jamban"
                     id="penggunaan_jamban_2" value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="penggunaan_jamban_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="penggunaan_jamban"
                     id="penggunaan_jamban_1" value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="penggunaan_jamban_1">Perlu pertolongan pada beberapa kegiatan
                     tetapi dapat mengerjakan sendiri beberapa kegiatan yang lain</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="penggunaan_jamban"
                     id="penggunaan_jamban_0" value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="penggunaan_jamban_0">Tergantung pertolongan orang lain (Tidak
                     mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">5. Makan dan Minum (jika makan harus berupa potongan, dianggap dibantu)
               <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="makan_minum" id="makan_minum_2"
                     value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="makan_minum_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="makan_minum" id="makan_minum_1"
                     value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="makan_minum_1">Perlu ditolong memotong makanan</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="makan_minum" id="makan_minum_0"
                     value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="makan_minum_0">Tidak mampu</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">6. Berubah sikap dari berbaring ke duduk <span
                  class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berubah_sikap" id="berubah_sikap_2"
                     value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berubah_sikap_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berubah_sikap" id="berubah_sikap_1"
                     value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berubah_sikap_1">Bantuan minimal/butuh bantuan</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berubah_sikap" id="berubah_sikap_0"
                     value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berubah_sikap_0">Tidak mampu</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">7. Berpindah/berjalan <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berpindah" id="berpindah_2" value="2"
                     required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berpindah_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berpindah" id="berpindah_1" value="1"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berpindah_1">Butuh bantuan/bantuan minimal/dengan alat
                     bantu</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="berpindah" id="berpindah_0" value="0"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="berpindah_0">Tidak mampu</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">8. Memakai baju <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="memakai_baju" id="memakai_baju_2"
                     value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="memakai_baju_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="memakai_baju" id="memakai_baju_1"
                     value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="memakai_baju_1">Sebagian dibantu (misalnya: mengancing
                     baju)</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="memakai_baju" id="memakai_baju_0"
                     value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="memakai_baju_0">Tergantung orang lain (Tidak mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">9. Naik turun tangga <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="naik_tangga" id="naik_tangga_2"
                     value="2" required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="naik_tangga_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="naik_tangga" id="naik_tangga_1"
                     value="1" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="naik_tangga_1">Butuh pertolongan/bantuan</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="naik_tangga" id="naik_tangga_0"
                     value="0" onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="naik_tangga_0">Tidak mampu</label>
               </div>
            </div>
         </div>

         <div class="form-group">
            <label class="font-weight-bold mb-2">10. Mandi <span class="text-danger">*</span></label>
            <div class="ml-3">
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="mandi" id="mandi_2" value="2"
                     required onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="mandi_2">Mandiri</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="mandi" id="mandi_1" value="1"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="mandi_1">Butuh bantuan sebagian</label>
               </div>
               <div class="form-check mb-2">
                  <input class="form-check-input barthel-input" type="radio" name="mandi" id="mandi_0" value="0"
                     onclick="hitungTotalSkorBarthel()">
                  <label class="form-check-label" for="mandi_0">Tergantung orang lain (Tidak mampu)</label>
               </div>
            </div>
         </div>

         <div class="form-group mt-4">
            <div class="row">
               <div class="col-md-6">
                  <label class="font-weight-bold">Total Skor :</label>
                  <input type="text" class="form-control" id="total_skor_barthel" name="total_skor_barthel" readonly>
               </div>
               <div class="col-md-6">
                  <label class="font-weight-bold">Tingkat Ketergantungan :</label>
                  <input type="text" class="form-control" id="tingkat_ketergantungan" name="tingkat_ketergantungan"
                     readonly>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>

<script>
   // Fungsi untuk menghitung total skor Barthel Index
   function hitungTotalSkorBarthel() {
       console.log("Menghitung total skor Barthel Index");
       var totalRaw = 0;
       var totalFinal = 0;
       
       // Ambil nilai dari setiap pertanyaan yang sudah dijawab menggunakan document.querySelector
       var bab = document.querySelector('input[name="bab"]:checked');
       var bak = document.querySelector('input[name="bak"]:checked');
       var membersihkanDiri = document.querySelector('input[name="membersihkan_diri"]:checked');
       var penggunaanJamban = document.querySelector('input[name="penggunaan_jamban"]:checked');
       var makanMinum = document.querySelector('input[name="makan_minum"]:checked');
       var berubahSikap = document.querySelector('input[name="berubah_sikap"]:checked');
       var berpindah = document.querySelector('input[name="berpindah"]:checked');
       var memakaiBaju = document.querySelector('input[name="memakai_baju"]:checked');
       var naikTangga = document.querySelector('input[name="naik_tangga"]:checked');
       var mandi = document.querySelector('input[name="mandi"]:checked');
       
       // Tambahkan ke total jika ada nilainya (nilai 0, 1, atau 2)
       if (bab) totalRaw += parseInt(bab.value);
       if (bak) totalRaw += parseInt(bak.value);
       if (membersihkanDiri) totalRaw += parseInt(membersihkanDiri.value);
       if (penggunaanJamban) totalRaw += parseInt(penggunaanJamban.value);
       if (makanMinum) totalRaw += parseInt(makanMinum.value);
       if (berubahSikap) totalRaw += parseInt(berubahSikap.value);
       if (berpindah) totalRaw += parseInt(berpindah.value);
       if (memakaiBaju) totalRaw += parseInt(memakaiBaju.value);
       if (naikTangga) totalRaw += parseInt(naikTangga.value);
       if (mandi) totalRaw += parseInt(mandi.value);
       
       // Kalikan dengan 5 untuk mendapatkan skor total 100
       totalFinal = totalRaw * 5;
       
       // Tampilkan total skor
       document.getElementById('total_skor_barthel').value = totalFinal;
       console.log("Total skor raw: " + totalRaw + ", Total Final: " + totalFinal);
       
       // Tentukan tingkat ketergantungan berdasarkan skor
       var tingkatKetergantungan = '';
       if (totalFinal >= 0 && totalFinal <= 20) {
           tingkatKetergantungan = 'Ketergantungan Total';
       } else if (totalFinal >= 21 && totalFinal <= 60) {
           tingkatKetergantungan = 'Ketergantungan Berat';
       } else if (totalFinal >= 61 && totalFinal <= 90) {
           tingkatKetergantungan = 'Ketergantungan Sedang';
       } else if (totalFinal >= 91 && totalFinal <= 99) {
           tingkatKetergantungan = 'Ketergantungan Ringan';
       } else if (totalFinal == 100) {
           tingkatKetergantungan = 'Mandiri';
       }
       
       // Tampilkan tingkat ketergantungan
       document.getElementById('tingkat_ketergantungan').value = tingkatKetergantungan;
       console.log("Tingkat ketergantungan: " + tingkatKetergantungan);
   }

   // Inisialisasi perhitungan awal
   document.addEventListener('DOMContentLoaded', function() {
       console.log("DOM fully loaded");
       hitungTotalSkorBarthel();
   });

   // Event listener untuk modal
   if (typeof jQuery !== 'undefined') {
       jQuery(document).ready(function($) {
           $('#modalGangguanFungsional').on('shown.bs.modal', function() {
               console.log("Modal Gangguan Fungsional ditampilkan");
               hitungTotalSkorBarthel();
           });
       });
   }
</script>