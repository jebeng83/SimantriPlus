<div>
   <x-adminlte-card title="Isi data partograf" icon="fas fa-file-medical" theme="success" maximizable>
      <style>
         .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
         }

         #partografChart {
            display: block;
            height: 100%;
            width: 100%;
         }
      </style>

      @if(isset($dataIbuHamil) && $dataIbuHamil)
      <div class="partograf-container">
         <div class="row mb-4">
            <div class="col-md-12">
               <div class="alert alert-info">
                  <i class="fas fa-info-circle mr-2"></i>
                  <strong>Partograf:</strong> {{ $dataIbuHamil->nama }} (ID Hamil: {{ $dataIbuHamil->id_hamil }})
               </div>
            </div>
         </div>

         <ul class="nav nav-tabs" id="partografTab" role="tablist">
            <li class="nav-item">
               <a class="nav-link active" id="data-tab" data-toggle="tab" href="#data-content" role="tab"
                  aria-controls="data-content" aria-selected="true">
                  <i class="fas fa-file-medical mr-1"></i> Data Partograf
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="grafik-tab" data-toggle="tab" href="#grafik-content" role="tab"
                  aria-controls="grafik-content" aria-selected="false">
                  <i class="fas fa-chart-line mr-1"></i> Grafik Partograf
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat-content" role="tab"
                  aria-controls="riwayat-content" aria-selected="false">
                  <i class="fas fa-history mr-1"></i> Riwayat
               </a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="catatan-persalinan-tab" data-toggle="tab" href="#catatan-persalinan-content"
                  role="tab" aria-controls="catatan-persalinan-content" aria-selected="false">
                  <i class="fas fa-book-medical mr-1"></i> Catatan Persalinan
               </a>
            </li>
         </ul>

         <div class="tab-content p-3 border border-top-0 rounded-bottom" id="partografTabContent">
            <div class="tab-pane fade show active" id="data-content" role="tabpanel" aria-labelledby="data-tab">
               <form wire:submit.prevent="savePartograf">
                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">1. Informasi Persalinan Awal</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="paritas">Paritas</label>
                                 <select class="form-control" id="paritas" wire:model="partograf.paritas">
                                    <option value="">Pilih Paritas</option>
                                    <option value="Primigravida">Primigravida</option>
                                    <option value="Multigravida">Multigravida</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="onset_persalinan">Onset Persalinan</label>
                                 <select class="form-control" id="onset_persalinan"
                                    wire:model="partograf.onset_persalinan">
                                    <option value="">Spontan</option>
                                    <option value="Spontan">Spontan</option>
                                    <option value="Induksi">Induksi</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="waktu_pecah_ketuban">Waktu Pecah Ketuban</label>
                                 <input type="datetime-local" class="form-control" id="waktu_pecah_ketuban"
                                    wire:model="partograf.waktu_pecah_ketuban">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="faktor_risiko">Faktor Risiko</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="hipertensi"
                                       wire:model="faktorRisiko.hipertensi">
                                    <label class="form-check-label" for="hipertensi">Hipertensi</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="preeklampsia"
                                       wire:model="faktorRisiko.preeklampsia">
                                    <label class="form-check-label" for="preeklampsia">Preeklampsia</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="diabetes"
                                       wire:model="faktorRisiko.diabetes">
                                    <label class="form-check-label" for="diabetes">Diabetes</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">2. Supportive Care</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Pendamping</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" id="pendamping_y" name="pendamping"
                                       value="Y" wire:model="partograf.pendamping" data-field="partograf.pendamping"
                                       @if($partograf['pendamping']=='Y' ) checked @endif>
                                    <label class="form-check-label" for="pendamping_y">Ya</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" id="pendamping_n" name="pendamping"
                                       value="N" wire:model="partograf.pendamping" data-field="partograf.pendamping"
                                       @if($partograf['pendamping']=='N' ) checked @endif>
                                    <label class="form-check-label" for="pendamping_n">Tidak</label>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Mobilitas</label>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" id="mobilitas_y" name="mobilitas"
                                       value="Y" wire:model="partograf.mobilitas" data-field="partograf.mobilitas"
                                       @if($partograf['mobilitas']=='Y' ) checked @endif>
                                    <label class="form-check-label" for="mobilitas_y">Ya</label>
                                 </div>
                                 <div class="form-check">
                                    <input class="form-check-input" type="radio" id="mobilitas_n" name="mobilitas"
                                       value="N" wire:model="partograf.mobilitas" data-field="partograf.mobilitas"
                                       @if($partograf['mobilitas']=='N' ) checked @endif>
                                    <label class="form-check-label" for="mobilitas_n">Tidak</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="manajemen_nyeri">Manajemen Nyeri</label>
                                 <select class="form-control" id="manajemen_nyeri"
                                    wire:model="partograf.manajemen_nyeri">
                                    <option value="">Farmakologis</option>
                                    <option value="Farmakologis">Farmakologis</option>
                                    <option value="Non-Farmakologis">Non-Farmakologis</option>
                                    <option value="Kombinasi">Kombinasi</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="intake_cairan">Intake Cairan</label>
                                 <select class="form-control" id="intake_cairan" wire:model="partograf.intake_cairan">
                                    <option value="">Kombinasi</option>
                                    <option value="Oral">Oral</option>
                                    <option value="Intravena">Intravena</option>
                                    <option value="Kombinasi">Kombinasi</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">3. Informasi Janin</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="denyut_jantung_janin">Denyut Jantung Janin (bpm)</label>
                                 <input type="number" class="form-control" id="denyut_jantung_janin"
                                    wire:model="partograf.denyut_jantung_janin">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="kondisi_cairan_ketuban">Kondisi Cairan Ketuban</label>
                                 <select class="form-control" id="kondisi_cairan_ketuban"
                                    wire:model="partograf.kondisi_cairan_ketuban">
                                    <option value="">Pilih Kondisi</option>
                                    <option value="I">Intact (I)</option>
                                    <option value="C">Clear (C)</option>
                                    <option value="M">Meconium (M)</option>
                                    <option value="B">Blood-stained (B)</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="presentasi_janin">Presentasi Janin</label>
                                 <select class="form-control" id="presentasi_janin"
                                    wire:model="partograf.presentasi_janin">
                                    <option value="">Pilih Presentasi</option>
                                    <option value="A">Occiput Anterior (A)</option>
                                    <option value="P">Occiput Posterior (P)</option>
                                    <option value="T">Occiput Transverse (T)</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="bentuk_kepala_janin">Bentuk Kepala Janin</label>
                                 <select class="form-control" id="bentuk_kepala_janin"
                                    wire:model="partograf.bentuk_kepala_janin">
                                    <option value="">Pilih Bentuk</option>
                                    <option value="0">Normal (0)</option>
                                    <option value="1">Sedikit Molding (+)</option>
                                    <option value="2">Molding Sedang (++)</option>
                                    <option value="3">Molding Berat (+++)</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">4. Informasi Ibu</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="nadi">Nadi (bpm)</label>
                                 <input type="number" class="form-control" id="nadi" wire:model="partograf.nadi">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="tekanan_darah_sistole">Tekanan Darah Sistole (mmHg)</label>
                                 <input type="number" class="form-control" id="tekanan_darah_sistole"
                                    wire:model="partograf.tekanan_darah_sistole">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label for="tekanan_darah_diastole">Tekanan Darah Diastole (mmHg)</label>
                                 <input type="number" class="form-control" id="tekanan_darah_diastole"
                                    wire:model="partograf.tekanan_darah_diastole">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="suhu">Suhu (°C)</label>
                                 <input type="number" step="0.1" class="form-control" id="suhu"
                                    wire:model="partograf.suhu">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="urine_output">Urine Output (ml)</label>
                                 <input type="number" class="form-control" id="urine_output"
                                    wire:model="partograf.urine_output">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">5. Proses Persalinan</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="frekuensi_kontraksi">Frekuensi Kontraksi (per 10 menit)</label>
                                 <input type="number" class="form-control" id="frekuensi_kontraksi"
                                    wire:model="partograf.frekuensi_kontraksi">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="durasi_kontraksi">Durasi Kontraksi (detik)</label>
                                 <input type="number" class="form-control" id="durasi_kontraksi"
                                    wire:model="partograf.durasi_kontraksi">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="dilatasi_serviks">Dilatasi Serviks (cm)</label>
                                 <input type="number" class="form-control" id="dilatasi_serviks"
                                    wire:model="partograf.dilatasi_serviks">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="penurunan_posisi_janin">Penurunan Posisi Janin</label>
                                 <select class="form-control" id="penurunan_posisi_janin"
                                    wire:model="partograf.penurunan_posisi_janin">
                                    <option value="">Pilih Penurunan</option>
                                    <option value="-5">-5</option>
                                    <option value="-4">-4</option>
                                    <option value="-3">-3</option>
                                    <option value="-2">-2</option>
                                    <option value="-1">-1</option>
                                    <option value="0">0</option>
                                    <option value="+1">+1</option>
                                    <option value="+2">+2</option>
                                    <option value="+3">+3</option>
                                    <option value="+4">+4</option>
                                    <option value="+5">+5</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">6. Pengobatan</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="obat_dan_dosis">Obat dan Dosis</label>
                                 <textarea class="form-control" id="obat_dan_dosis" rows="3"
                                    wire:model="partograf.obat_dan_dosis"></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="cairan_infus">Cairan Infus</label>
                                 <textarea class="form-control" id="cairan_infus" rows="2"
                                    wire:model="partograf.cairan_infus"></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="card mb-3">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">7. Perencanaan</h5>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="tindakan_yang_direncanakan">Tindakan yang Direncanakan</label>
                                 <textarea class="form-control" id="tindakan_yang_direncanakan" rows="3"
                                    wire:model="partograf.tindakan_yang_direncanakan"></textarea>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="hasil_tindakan">Hasil Tindakan</label>
                                 <textarea class="form-control" id="hasil_tindakan" rows="3"
                                    wire:model="partograf.hasil_tindakan"></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="keputusan_bersama">Keputusan Bersama</label>
                                 <textarea class="form-control" id="keputusan_bersama" rows="2"
                                    wire:model="partograf.keputusan_bersama"></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-block">
                           <i class="fas fa-save mr-1"></i> Simpan Partograf
                        </button>
                     </div>
                  </div>
               </form>
            </div>

            <div class="tab-pane fade" id="grafik-content" role="tabpanel" aria-labelledby="grafik-tab">
               <div class="row">
                  <div class="col-md-12 mb-3">
                     <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Grafik partograf menunjukkan perkembangan persalinan. Data diambil dari entri partograf
                        terakhir.
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                           <h5 class="mb-0">Grafik Partograf</h5>
                        </div>
                        <div class="card-body text-center">
                           <p class="mb-3">Grafik partograf akan ditampilkan dalam jendela terpisah dengan tampilan
                              layar penuh untuk visualisasi yang lebih baik.</p>
                           <button type="button" class="btn btn-lg btn-primary" id="bukaPartograf" onclick="(function(){
                              console.log('Membuka modal partograf dengan Bootstrap');
                              try {
                                 if (typeof jQuery === 'undefined') {
                                    alert('jQuery tidak tersedia!');
                                    return;
                                 }
                                 if (typeof jQuery.fn.modal === 'undefined') {
                                    alert('Bootstrap modal tidak tersedia!');
                                    return;
                                 }
                                 jQuery('#partografFullscreenModal').modal('show');
                                 jQuery('#partografFullscreenModal').on('shown.bs.modal', function() {
                                    setTimeout(function() {
                                       if (typeof Chart === 'undefined') {
                                          alert('Chart.js tidak tersedia!');
                                          return;
                                       }
                                       console.log('Membuat grafik partograf setelah modal terbuka');
                                       try {
                                          // Reset canvas terlebih dahulu
                                          var container = document.querySelector('#partografFullscreenModal .chart-container');
                                          if (!container) {
                                             console.error('Container chart tidak ditemukan');
                                             return;
                                          }
                                          
                                          var oldCanvas = document.getElementById('partografChartFullscreen');
                                          if (oldCanvas) {
                                             if (window.partografChart) {
                                                window.partografChart.destroy();
                                                window.partografChart = null;
                                             }
                                             container.removeChild(oldCanvas);
                                          }
                                          
                                          var newCanvas = document.createElement('canvas');
                                          newCanvas.id = 'partografChartFullscreen';
                                          container.appendChild(newCanvas);
                                          
                                          // Buat grafik
                                          var canvas = document.getElementById('partografChartFullscreen');
                                          if (!canvas) {
                                             console.error('Canvas tidak ditemukan');
                                             return;
                                          }
                                          
                                          var ctx = canvas.getContext('2d');
                                          if (!ctx) {
                                             console.error('Context tidak dapat diambil');
                                             return;
                                          }
                                          
                                          window.partografChart = new Chart(ctx, {
                                             type: 'line',
                                             data: {
                                                labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                                                datasets: [
                                                   {
                                                      label: 'Dilatasi Serviks',
                                                      data: [2, 3, null, 5, 7, 8, null, null, null, null, null, null, null],
                                                      borderColor: 'rgb(54, 162, 235)',
                                                      backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                                      borderWidth: 3,
                                                      pointRadius: 6,
                                                      pointHoverRadius: 8,
                                                      fill: false,
                                                      tension: 0.1
                                                   },
                                                   {
                                                      label: 'Garis Alert',
                                                      data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, null],
                                                      borderColor: 'rgb(255, 159, 64)',
                                                      borderDash: [5, 5],
                                                      pointRadius: 0,
                                                      fill: false
                                                   },
                                                   {
                                                      label: 'Garis Action',
                                                      data: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, null],
                                                      borderColor: 'rgb(255, 99, 132)',
                                                      borderDash: [5, 5],
                                                      pointRadius: 0,
                                                      fill: false
                                                   }
                                                ]
                                             },
                                             options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                animation: false,
                                                plugins: {
                                                   title: {
                                                      display: true,
                                                      text: 'Grafik Partograf - Dilatasi Serviks'
                                                   }
                                                },
                                                scales: {
                                                   x: {
                                                      title: {
                                                         display: true,
                                                         text: 'Waktu (jam)'
                                                      }
                                                   },
                                                   y: {
                                                      min: 0,
                                                      max: 12,
                                                      title: {
                                                         display: true,
                                                         text: 'Dilatasi Serviks (cm)'
                                                      }
                                                   }
                                                }
                                             }
                                          });
                                          
                                          console.log('Grafik berhasil dibuat');
                                          var statusElement = document.getElementById('chart-status-fullscreen');
                                          if (statusElement) {
                                             statusElement.textContent = 'Status: Grafik berhasil dibuat';
                                          }
                                       } catch (error) {
                                          console.error('Error saat membuat grafik:', error);
                                          alert('Terjadi kesalahan saat membuat grafik: ' + error.message);
                                       }
                                    }, 500);
                                 });
                              } catch (error) {
                                 console.error('Error:', error);
                                 alert('Terjadi kesalahan: ' + error.message);
                              }
                           })();">
                              <i class="fas fa-chart-line mr-2"></i> Tampilkan Grafik Partograf
                           </button>
                           <button type="button" class="btn btn-lg btn-warning mt-2" id="bukaPartografAlt" onclick="(function(){
                              console.log('Membuka modal partograf dengan JS Native');
                              try {
                                 var modal = document.getElementById('partografFullscreenModal');
                                 if (!modal) {
                                    alert('Modal tidak ditemukan!');
                                    return;
                                 }
                                 var backdrop = document.createElement('div');
                                 backdrop.className = 'modal-backdrop-js';
                                 backdrop.id = 'modalBackdropJS';
                                 document.body.appendChild(backdrop);
                                 modal.style.display = 'block';
                                 modal.classList.add('show-js');
                                 document.body.classList.add('modal-open');
                                 document.body.style.overflow = 'hidden';
                                 setTimeout(function() {
                                    if (typeof Chart === 'undefined') {
                                       alert('Chart.js tidak tersedia!');
                                       return;
                                    }
                                    console.log('Membuat grafik partograf setelah modal terbuka');
                                    try {
                                       // Reset canvas terlebih dahulu
                                       var container = document.querySelector('#partografFullscreenModal .chart-container');
                                       if (!container) {
                                          console.error('Container chart tidak ditemukan');
                                          return;
                                       }
                                       
                                       var oldCanvas = document.getElementById('partografChartFullscreen');
                                       if (oldCanvas) {
                                          if (window.partografChart) {
                                             window.partografChart.destroy();
                                             window.partografChart = null;
                                          }
                                          container.removeChild(oldCanvas);
                                       }
                                       
                                       var newCanvas = document.createElement('canvas');
                                       newCanvas.id = 'partografChartFullscreen';
                                       container.appendChild(newCanvas);
                                       
                                       // Buat grafik
                                       var canvas = document.getElementById('partografChartFullscreen');
                                       if (!canvas) {
                                          console.error('Canvas tidak ditemukan');
                                          return;
                                       }
                                       
                                       var ctx = canvas.getContext('2d');
                                       if (!ctx) {
                                          console.error('Context tidak dapat diambil');
                                          return;
                                       }
                                       
                                       window.partografChart = new Chart(ctx, {
                                          type: 'line',
                                          data: {
                                             labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                                             datasets: [
                                                {
                                                   label: 'Dilatasi Serviks',
                                                   data: [2, 3, null, 5, 7, 8, null, null, null, null, null, null, null],
                                                   borderColor: 'rgb(54, 162, 235)',
                                                   backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                                   borderWidth: 3,
                                                   pointRadius: 6,
                                                   pointHoverRadius: 8,
                                                   fill: false,
                                                   tension: 0.1
                                                },
                                                {
                                                   label: 'Garis Alert',
                                                   data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, null],
                                                   borderColor: 'rgb(255, 159, 64)',
                                                   borderDash: [5, 5],
                                                   pointRadius: 0,
                                                   fill: false
                                                },
                                                {
                                                   label: 'Garis Action',
                                                   data: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, null],
                                                   borderColor: 'rgb(255, 99, 132)',
                                                   borderDash: [5, 5],
                                                   pointRadius: 0,
                                                   fill: false
                                                }
                                             ]
                                          },
                                          options: {
                                             responsive: true,
                                             maintainAspectRatio: false,
                                             animation: false,
                                             plugins: {
                                                title: {
                                                   display: true,
                                                   text: 'Grafik Partograf - Dilatasi Serviks'
                                                }
                                             },
                                             scales: {
                                                x: {
                                                   title: {
                                                      display: true,
                                                      text: 'Waktu (jam)'
                                                   }
                                                },
                                                y: {
                                                   min: 0,
                                                   max: 12,
                                                   title: {
                                                      display: true,
                                                      text: 'Dilatasi Serviks (cm)'
                                                   }
                                                }
                                             }
                                          }
                                       });
                                       
                                       console.log('Grafik berhasil dibuat');
                                       var statusElement = document.getElementById('chart-status-fullscreen');
                                       if (statusElement) {
                                          statusElement.textContent = 'Status: Grafik berhasil dibuat';
                                       }
                                    } catch (error) {
                                       console.error('Error saat membuat grafik:', error);
                                       alert('Terjadi kesalahan saat membuat grafik: ' + error.message);
                                    }
                                 }, 500);
                              } catch (error) {
                                 console.error('Error:', error);
                                 alert('Terjadi kesalahan: ' + error.message);
                              }
                           })();" style="display: none;">
                              <i class="fas fa-chart-line mr-2"></i> Tampilkan Grafik (Alternatif)
                           </button>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-12">
                     <button type="button" class="btn btn-info" wire:click="exportPartograf">
                        <i class="fas fa-file-pdf mr-1"></i> Ekspor PDF
                     </button>
                  </div>
               </div>

               <!-- Modal Partograf Fullscreen -->
               <div class="modal fade" id="partografFullscreenModal" tabindex="-1" role="dialog"
                  aria-labelledby="partografFullscreenModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-fullscreen" role="document">
                     <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                           <h5 class="modal-title" id="partografFullscreenModalLabel">
                              <i class="fas fa-chart-line mr-2"></i> Grafik Partograf
                           </h5>
                           <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                        <div class="modal-body">
                           <div class="container-fluid">
                              <div class="row mb-3">
                                 <div class="col-md-12">
                                    <div class="alert alert-info">
                                       <strong>Pasien:</strong> {{ isset($dataIbuHamil) ? $dataIbuHamil->nama : '' }}
                                       {{ isset($dataIbuHamil) ? '(ID Hamil: '.$dataIbuHamil->id_hamil.')' : '' }}
                                       <br>
                                       <strong>Info:</strong> Grafik partograf menunjukkan perkembangan persalinan. Data
                                       diambil dari entri partograf terakhir.
                                    </div>
                                    <div id="debug-log-area" class="alert alert-warning"
                                       style="max-height: 150px; overflow-y: auto; display: none;">
                                       <strong>Debug Log:</strong>
                                       <div id="debug-log-content"></div>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-12">
                                    <div class="chart-container"
                                       style="position: relative; height: 75vh; border: 2px solid #36A2EB; border-radius: 5px; background: white; padding: 20px;">
                                       <canvas id="partografChartFullscreen"></canvas>
                                    </div>
                                    <div class="text-center mt-4">
                                       <div id="chart-status-fullscreen" class="text-muted mb-3">Status: Menunggu
                                          inisialisasi grafik...</div>
                                       <div class="btn-toolbar justify-content-center">
                                          <div class="btn-group mr-2">
                                             <button type="button" class="btn btn-primary" id="buatChart" onclick="(function(){
                                                console.log('Membuat grafik partograf dari tombol internal modal');
                                                if (typeof Chart === 'undefined') {
                                                   alert('Chart.js tidak tersedia!');
                                                   return;
                                                }
                                                try {
                                                   // Reset canvas terlebih dahulu
                                                   var container = document.querySelector('#partografFullscreenModal .chart-container');
                                                   if (!container) {
                                                      console.error('Container chart tidak ditemukan');
                                                      return;
                                                   }
                                                   
                                                   var oldCanvas = document.getElementById('partografChartFullscreen');
                                                   if (oldCanvas) {
                                                      if (window.partografChart) {
                                                         window.partografChart.destroy();
                                                         window.partografChart = null;
                                                      }
                                                      container.removeChild(oldCanvas);
                                                   }
                                                   
                                                   var newCanvas = document.createElement('canvas');
                                                   newCanvas.id = 'partografChartFullscreen';
                                                   container.appendChild(newCanvas);
                                                   
                                                   // Buat grafik
                                                   var canvas = document.getElementById('partografChartFullscreen');
                                                   if (!canvas) {
                                                      console.error('Canvas tidak ditemukan');
                                                      return;
                                                   }
                                                   
                                                   var ctx = canvas.getContext('2d');
                                                   if (!ctx) {
                                                      console.error('Context tidak dapat diambil');
                                                      return;
                                                   }
                                                   
                                                   window.partografChart = new Chart(ctx, {
                                                      type: 'line',
                                                      data: {
                                                         labels: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
                                                         datasets: [
                                                            {
                                                               label: 'Dilatasi Serviks',
                                                               data: [2, 3, null, 5, 7, 8, null, null, null, null, null, null, null],
                                                               borderColor: 'rgb(54, 162, 235)',
                                                               backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                                               borderWidth: 3,
                                                               pointRadius: 6,
                                                               pointHoverRadius: 8,
                                                               fill: false,
                                                               tension: 0.1
                                                            },
                                                            {
                                                               label: 'Garis Alert',
                                                               data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, null],
                                                               borderColor: 'rgb(255, 159, 64)',
                                                               borderDash: [5, 5],
                                                               pointRadius: 0,
                                                               fill: false
                                                            },
                                                            {
                                                               label: 'Garis Action',
                                                               data: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, null],
                                                               borderColor: 'rgb(255, 99, 132)',
                                                               borderDash: [5, 5],
                                                               pointRadius: 0,
                                                               fill: false
                                                            }
                                                         ]
                                                      },
                                                      options: {
                                                         responsive: true,
                                                         maintainAspectRatio: false,
                                                         animation: false,
                                                         plugins: {
                                                            title: {
                                                               display: true,
                                                               text: 'Grafik Partograf - Dilatasi Serviks'
                                                            }
                                                         },
                                                         scales: {
                                                            x: {
                                                               title: {
                                                                  display: true,
                                                                  text: 'Waktu (jam)'
                                                               }
                                                            },
                                                            y: {
                                                               min: 0,
                                                               max: 12,
                                                               title: {
                                                                  display: true,
                                                                  text: 'Dilatasi Serviks (cm)'
                                                               }
                                                            }
                                                         }
                                                      }
                                                   });
                                                   
                                                   console.log('Grafik berhasil dibuat');
                                                   var statusElement = document.getElementById('chart-status-fullscreen');
                                                   if (statusElement) {
                                                      statusElement.textContent = 'Status: Grafik berhasil dibuat';
                                                   }
                                                } catch (error) {
                                                   console.error('Error saat membuat grafik:', error);
                                                   alert('Terjadi kesalahan saat membuat grafik: ' + error.message);
                                                }
                                             })();">
                                                <i class="fas fa-chart-line mr-1"></i> Tampilkan Grafik Partograf
                                             </button>
                                          </div>
                                          <div class="btn-group mr-2">
                                             <button type="button" class="btn btn-info" id="tampilPartografKlasik"
                                                onclick="(function(){
                                                console.log('Menampilkan partograf klasik');
                                                var url = '{{ route('partograf.klasik', ['id_hamil' => isset($dataIbuHamil) ? $dataIbuHamil->id_hamil : 0]) }}';
                                                window.open(url, '_blank', 'width=800,height=600');
                                             })();">
                                                <i class="fas fa-table mr-1"></i> Format Klasik
                                             </button>
                                          </div>
                                          <div class="btn-group mr-2">
                                             <button type="button" class="btn btn-info" id="tampilkanPartografModern"
                                                onclick="(function(){
                                                console.log('Menampilkan partograf modern');
                                                document.getElementById('partograf-klasik').style.display = 'none';
                                                document.getElementById('partograf-modern').style.display = 'block';
                                             })();">
                                                <i class="fas fa-chart-line mr-1"></i> Tampilan Modern
                                             </button>
                                          </div>
                                          <div class="btn-group">
                                             <button type="button" class="btn btn-success" id="eksporChart" onclick="(function(){
                                                console.log('Mengekspor grafik...');
                                                if (!window.partografChart) {
                                                   alert('Tidak ada grafik untuk diekspor!');
                                                   return;
                                                }
                                                try {
                                                   var canvas = document.getElementById('partografChartFullscreen');
                                                   var image = canvas.toDataURL('image/png');
                                                   var link = document.createElement('a');
                                                   link.href = image;
                                                   link.download = 'partograf_' + new Date().toISOString().slice(0, 10) + '.png';
                                                   document.body.appendChild(link);
                                                   link.click();
                                                   document.body.removeChild(link);
                                                } catch (error) {
                                                   console.error('Error:', error);
                                                   alert('Gagal mengekspor grafik: ' + error.message);
                                                }
                                             })();">
                                                <i class="fas fa-file-export mr-1"></i> Ekspor Gambar
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <!-- Tampilan Partograf Modern (Chart.js) -->
                              <div id="partograf-modern" class="mt-4">
                                 <div class="chart-container"
                                    style="position: relative; height: 75vh; border: 2px solid #36A2EB; border-radius: 5px; background: white; padding: 20px;">
                                    <canvas id="partografChartFullscreen"></canvas>
                                 </div>
                              </div>

                              <!-- Tampilan Partograf Klasik (seperti gambar) -->
                              <div id="partograf-klasik" class="mt-4"
                                 style="display: none; background-color: white; padding: 20px;">
                                 <div style="max-width: 1200px; margin: 0 auto;">
                                    <div class="text-center mb-4">
                                       <h3>LEMBAR PARTOGRAF</h3>
                                    </div>

                                    <div class="row mb-3">
                                       <div class="col-md-6">
                                          <table class="table table-bordered">
                                             <tr>
                                                <td style="width: 120px;">No. Register</td>
                                                <td style="width: 200px;">
                                                   <div style="display: flex;">
                                                      @for ($i = 0; $i < 8; $i++) <div
                                                         style="width: 25px; height: 25px; border: 1px solid #000; text-align: center;">
                                                   </div>
                                                   @endfor
                                       </div>
                                       </td>
                                       </tr>
                                       <tr>
                                          <td>No Puskesmas<br>Ketuban pecah</td>
                                          <td>
                                             <div style="display: flex; margin-bottom: 5px;">
                                                @for ($i = 0; $i < 8; $i++) <div
                                                   style="width: 25px; height: 25px; border: 1px solid #000; text-align: center;">
                                             </div>
                                             @endfor
                                    </div>
                                    <div>sejak jam ____________</div>
                                    </td>
                                    </tr>
                                    </table>
                                 </div>
                                 <div class="col-md-6">
                                    <table class="table table-bordered">
                                       <tr>
                                          <td style="width: 120px;">Nama Ibu:</td>
                                          <td>{{ isset($dataIbuHamil) ? $dataIbuHamil->nama : '_________________' }}
                                          </td>
                                       </tr>
                                       <tr>
                                          <td>Tanggal:</td>
                                          <td>
                                             <div class="row">
                                                <div class="col-6">_________________</div>
                                                <div class="col-6">mules sejak jam _________________</div>
                                             </div>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td>Umur:</td>
                                          <td>
                                             <div class="row">
                                                <div class="col-3">_______</div>
                                                <div class="col-3">G: _______</div>
                                                <div class="col-3">P: _______</div>
                                                <div class="col-3">A: _______</div>
                                             </div>
                                          </td>
                                       </tr>
                                    </table>
                                 </div>
                              </div>

                              <!-- Denyut Jantung Janin -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div
                                       style="writing-mode: vertical-rl; transform: rotate(180deg); height: 200px; text-align: center;">
                                       Denyut<br>Jantung<br>Janin<br>(/ menit)
                                    </div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 200px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                       <div style="position: absolute; left: -40px; top: 0;">200</div>
                                       <div style="position: absolute; left: -40px; top: 20px;">190</div>
                                       <div style="position: absolute; left: -40px; top: 40px;">180</div>
                                       <div style="position: absolute; left: -40px; top: 60px;">170</div>
                                       <div style="position: absolute; left: -40px; top: 80px;">160</div>
                                       <div style="position: absolute; left: -40px; top: 100px;">150</div>
                                       <div style="position: absolute; left: -40px; top: 120px;">140</div>
                                       <div style="position: absolute; left: -40px; top: 140px;">130</div>
                                       <div style="position: absolute; left: -40px; top: 160px;">120</div>
                                       <div style="position: absolute; left: -40px; top: 180px;">110</div>
                                       <div style="position: absolute; left: -40px; top: 200px;">100</div>
                                       <div style="position: absolute; left: -40px; top: 220px;">90</div>
                                       <div style="position: absolute; left: -40px; top: 240px;">80</div>

                                       <!-- Data DJJ akan ditampilkan di sini -->
                                    </div>
                                 </div>
                              </div>

                              <!-- Air ketuban dan penyusupan -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Air ketuban<br>Penyusupan</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 40px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                       <!-- Data air ketuban dan penyusupan akan ditampilkan di sini -->
                                    </div>
                                 </div>
                              </div>

                              <!-- Dilatasi Serviks -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div
                                       style="writing-mode: vertical-rl; transform: rotate(180deg); height: 200px; text-align: center;">
                                       pembukaan serviks (cm)<br>Garis waspada & garis bertindak
                                    </div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 200px; position: relative; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                       <!-- Garis Alert dan Action -->
                                       <div
                                          style="position: absolute; width: 100%; height: 1px; background-color: orange; top: 40px; transform: rotate(-15deg); transform-origin: left;">
                                       </div>
                                       <div
                                          style="position: absolute; width: 100%; height: 1px; background-color: red; top: 80px; transform: rotate(-15deg); transform-origin: left;">
                                       </div>

                                       <!-- Label sumbu Y -->
                                       <div style="position: absolute; left: -40px; top: 0;">10</div>
                                       <div style="position: absolute; left: -40px; top: 20px;">9</div>
                                       <div style="position: absolute; left: -40px; top: 40px;">8</div>
                                       <div style="position: absolute; left: -40px; top: 60px;">7</div>
                                       <div style="position: absolute; left: -40px; top: 80px;">6</div>
                                       <div style="position: absolute; left: -40px; top: 100px;">5</div>
                                       <div style="position: absolute; left: -40px; top: 120px;">4</div>
                                       <div style="position: absolute; left: -40px; top: 140px;">3</div>
                                       <div style="position: absolute; left: -40px; top: 160px;">2</div>
                                       <div style="position: absolute; left: -40px; top: 180px;">1</div>

                                       <!-- Label fasa -->
                                       <div style="position: absolute; top: 40px; left: 60px;">L A T E N</div>
                                       <div style="position: absolute; top: 100px; left: 240px;">A K T I F</div>

                                       <!-- Data dilatasi serviks akan ditampilkan di sini -->
                                    </div>

                                    <!-- Label sumbu X -->
                                    <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                                       <div>0</div>
                                       <div>1</div>
                                       <div>2</div>
                                       <div>3</div>
                                       <div>4</div>
                                       <div>5</div>
                                       <div>6</div>
                                       <div>7</div>
                                       <div>8</div>
                                       <div>9</div>
                                       <div>10</div>
                                       <div>11</div>
                                       <div>12</div>
                                    </div>
                                    <div class="text-center">waktu (jam)</div>
                                 </div>
                              </div>

                              <!-- Kontraksi tiap 10 menit -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Kontraksi<br>tiap<br>10mnt</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 80px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                       <!-- Legenda -->
                                       <div style="position: absolute; left: -40px; top: 0px;">5</div>
                                       <div style="position: absolute; left: -40px; top: 20px;">4</div>
                                       <div style="position: absolute; left: -40px; top: 40px;">3</div>
                                       <div style="position: absolute; left: -40px; top: 60px;">2</div>
                                       <div style="position: absolute; left: -40px; top: 80px;">1</div>

                                       <!-- Keterangan -->
                                       <div style="position: absolute; left: -100px; top: 0px;">≤20</div>
                                       <div style="position: absolute; left: -100px; top: 20px;">20-40</div>
                                       <div style="position: absolute; left: -100px; top: 60px;">≥40</div>

                                       <!-- Data kontraksi akan ditampilkan di sini -->
                                    </div>
                                 </div>
                              </div>

                              <!-- Oksitosin, Obat dan Cairan IV -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>oksitosin U/L<br>tetes/menit</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 40px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                    </div>
                                 </div>
                              </div>

                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Obat dan<br>Cairan IV</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 40px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                    </div>
                                 </div>
                              </div>

                              <!-- Tekanan Darah -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div
                                       style="writing-mode: vertical-rl; transform: rotate(180deg); height: 200px; text-align: center;">
                                       <span style="font-size: 8px;">•</span> Nadi<br>
                                       <span style="font-size: 14px; margin-top: 5px;">↕</span> Tekanan Darah
                                    </div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 200px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                       <!-- Legenda tekanan darah -->
                                       <div style="position: absolute; left: -40px; top: 0px;">180</div>
                                       <div style="position: absolute; left: -40px; top: 20px;">170</div>
                                       <div style="position: absolute; left: -40px; top: 40px;">160</div>
                                       <div style="position: absolute; left: -40px; top: 60px;">150</div>
                                       <div style="position: absolute; left: -40px; top: 80px;">140</div>
                                       <div style="position: absolute; left: -40px; top: 100px;">130</div>
                                       <div style="position: absolute; left: -40px; top: 120px;">120</div>
                                       <div style="position: absolute; left: -40px; top: 140px;">110</div>
                                       <div style="position: absolute; left: -40px; top: 160px;">100</div>
                                       <div style="position: absolute; left: -40px; top: 180px;">90</div>
                                       <div style="position: absolute; left: -40px; top: 200px;">80</div>
                                       <div style="position: absolute; left: -40px; top: 220px;">70</div>
                                       <div style="position: absolute; left: -40px; top: 240px;">60</div>

                                       <!-- Data tekanan darah dan nadi akan ditampilkan di sini -->
                                    </div>
                                 </div>
                              </div>

                              <!-- Suhu, Urin -->
                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Suhu</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 40px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                    </div>
                                 </div>
                              </div>

                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Urin</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="row">
                                       <div class="col-6">
                                          <div style="display: flex; align-items: center;">
                                             <div style="width: 80px;">Protein</div>
                                             <div class="partograf-grid"
                                                style="height: 20px; flex-grow: 1; background: repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-6">
                                          <div style="display: flex; align-items: center;">
                                             <div style="width: 80px;">Aseton</div>
                                             <div class="partograf-grid"
                                                style="height: 20px; flex-grow: 1; background: repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <div class="row mb-2">
                                 <div class="col-2">
                                    <div>Volume</div>
                                 </div>
                                 <div class="col-10">
                                    <div class="partograf-grid"
                                       style="height: 40px; background: repeating-linear-gradient(0deg, #ddd, #ddd 1px, transparent 1px, transparent 20px) repeating-linear-gradient(90deg, #ddd, #ddd 1px, transparent 1px, transparent 20px);">
                                    </div>
                                    <div>Minum :</div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                     <button type="button" class="btn btn-warning" id="tutupModalAlt" onclick="(function(){
                              console.log('Menutup modal...');
                              try {
                                 var modal = document.getElementById('partografFullscreenModal');
                                 if (modal) {
                                    modal.style.display = 'none';
                                    modal.classList.remove('show-js');
                                    document.body.classList.remove('modal-open');
                                    document.body.style.overflow = '';
                                 }
                                 var backdrop = document.getElementById('modalBackdropJS');
                                 if (backdrop) {
                                    document.body.removeChild(backdrop);
                                 }
                              } catch (error) {
                                 console.error('Error:', error);
                              }
                           })();">Tutup (Alternatif)</button>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="tab-pane fade" id="riwayat-content" role="tabpanel" aria-labelledby="riwayat-tab">
         <div class="row">
            <div class="col-md-12">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead class="thead-dark">
                        <tr>
                           <th>Tanggal & Jam</th>
                           <th>Dilatasi</th>
                           <th>DJJ</th>
                           <th>Kontraksi</th>
                           <th>Tensi</th>
                           <th>Petugas</th>
                           <th>Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        @if($riwayatPartograf && count($riwayatPartograf) > 0)
                        @foreach($riwayatPartograf as $riwayat)
                        <tr>
                           <td>{{ is_object($riwayat) ? $riwayat->tanggal_partograf :
                              $riwayat['tanggal_partograf'] }}</td>
                           <td>{{ is_object($riwayat) ? $riwayat->dilatasi_serviks : $riwayat['dilatasi_serviks']
                              }} cm</td>
                           <td>{{ is_object($riwayat) ? $riwayat->denyut_jantung_janin :
                              $riwayat['denyut_jantung_janin'] }} bpm</td>
                           <td>{{ is_object($riwayat) ? $riwayat->frekuensi_kontraksi :
                              $riwayat['frekuensi_kontraksi'] }}/10 menit</td>
                           <td>{{ is_object($riwayat) ? $riwayat->tekanan_darah_sistole :
                              $riwayat['tekanan_darah_sistole'] }}/{{ is_object($riwayat) ?
                              $riwayat->tekanan_darah_diastole : $riwayat['tekanan_darah_diastole'] }}</td>
                           <td>{{ is_object($riwayat) ? $riwayat->diperiksa_oleh : $riwayat['diperiksa_oleh'] }}
                           </td>
                           <td>
                              <button class="btn btn-sm btn-info"
                                 wire:click="viewPartograf('{{ is_object($riwayat) ? $riwayat->id_partograf : $riwayat['id_partograf'] }}')">
                                 <i class="fas fa-eye"></i>
                              </button>
                           </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                           <td colspan="7" class="text-center">Belum ada data partograf</td>
                        </tr>
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

      <div class="tab-pane fade" id="catatan-persalinan-content" role="tabpanel"
         aria-labelledby="catatan-persalinan-tab">
         <form id="formCatatanPersalinan" method="javascript:void(0);" onsubmit="event.preventDefault(); return false;"
            class="nosubmit">
            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">1. Kala I</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="kala1_garis_waspada">Partogram melewati garis waspada</label>
                           <div class="d-flex">
                              <!-- Contoh radio button kala1_garis_waspada -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala1_garis_waspada_y"
                                    name="kala1_garis_waspada" value="Ya" {{
                                    $catatanPersalinan['kala1_garis_waspada']=='Ya' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala1_garis_waspada"
                                    data-field="catatanPersalinan.kala1_garis_waspada">
                                 <label class="form-check-label" for="kala1_garis_waspada_y">Ya</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala1_garis_waspada_n"
                                    name="kala1_garis_waspada" value="Tidak" {{
                                    $catatanPersalinan['kala1_garis_waspada']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala1_garis_waspada"
                                    data-field="catatanPersalinan.kala1_garis_waspada">
                                 <label class="form-check-label" for="kala1_garis_waspada_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-8">
                        <div class="form-group">
                           <label for="kala1_masalah_lain">Masalah lain, sebutkan</label>
                           <input type="text" class="form-control" id="kala1_masalah_lain"
                              wire:model.defer="catatanPersalinan.kala1_masalah_lain">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="kala1_penatalaksanaan">Penatalaksanaan masalah Tsb</label>
                           <textarea class="form-control" id="kala1_penatalaksanaan" rows="3"
                              wire:model.defer="catatanPersalinan.kala1_penatalaksanaan"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="kala1_hasil">Hasilnya</label>
                           <textarea class="form-control" id="kala1_hasil" rows="2"
                              wire:model.defer="catatanPersalinan.kala1_hasil"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">2. Kala II</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="kala2_episiotomi">Episiotomi</label>
                           <div class="d-flex">
                              <!-- Radio button Kala II Episiotomi -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala2_episiotomi_y"
                                    name="kala2_episiotomi" value="Ya" {{ $catatanPersalinan['kala2_episiotomi']=='Ya'
                                    ? 'checked' : '' }} wire:model.defer="catatanPersalinan.kala2_episiotomi"
                                    data-field="catatanPersalinan.kala2_episiotomi">
                                 <label class="form-check-label" for="kala2_episiotomi_y">Ya, indikasi</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala2_episiotomi_n"
                                    name="kala2_episiotomi" value="Tidak" {{
                                    $catatanPersalinan['kala2_episiotomi']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala2_episiotomi"
                                    data-field="catatanPersalinan.kala2_episiotomi">
                                 <label class="form-check-label" for="kala2_episiotomi_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-8">
                        <div class="form-group">
                           <label for="kala2_pendamping">Pendamping pada saat persalinan</label>
                           <div class="d-flex flex-wrap">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="checkbox" id="pendamping_bidan"
                                    wire:model.defer="pendampingPersalinan.bidan">
                                 <label class="form-check-label" for="pendamping_bidan">Bidan</label>
                              </div>
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="checkbox" id="pendamping_suami"
                                    wire:model.defer="pendampingPersalinan.suami">
                                 <label class="form-check-label" for="pendamping_suami">Suami</label>
                              </div>
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="checkbox" id="pendamping_keluarga"
                                    wire:model.defer="pendampingPersalinan.keluarga">
                                 <label class="form-check-label" for="pendamping_keluarga">Keluarga</label>
                              </div>
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="checkbox" id="pendamping_teman"
                                    wire:model.defer="pendampingPersalinan.teman">
                                 <label class="form-check-label" for="pendamping_teman">Teman</label>
                              </div>
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="checkbox" id="pendamping_dukun"
                                    wire:model.defer="pendampingPersalinan.dukun">
                                 <label class="form-check-label" for="pendamping_dukun">Dukun</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" id="pendamping_tidak_ada"
                                    wire:model.defer="pendampingPersalinan.tidak_ada">
                                 <label class="form-check-label" for="pendamping_tidak_ada">Tidak Ada</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala2_gawat_janin">Gawat Janin</label>
                           <div class="d-flex">
                              <!-- Radio button Kala II Gawat Janin -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala2_gawat_janin_y"
                                    name="kala2_gawat_janin" value="Ya" {{ $catatanPersalinan['kala2_gawat_janin']=='Ya'
                                    ? 'checked' : '' }} wire:model.defer="catatanPersalinan.kala2_gawat_janin"
                                    data-field="catatanPersalinan.kala2_gawat_janin">
                                 <label class="form-check-label" for="kala2_gawat_janin_y">Ya, tindakan</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala2_gawat_janin_n"
                                    name="kala2_gawat_janin" value="Tidak" {{
                                    $catatanPersalinan['kala2_gawat_janin']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala2_gawat_janin"
                                    data-field="catatanPersalinan.kala2_gawat_janin">
                                 <label class="form-check-label" for="kala2_gawat_janin_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala2_distosia_bahu">Distosia bahu</label>
                           <div class="d-flex">
                              <!-- Radio button Kala II Distosia Bahu -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala2_distosia_bahu_y"
                                    name="kala2_distosia_bahu" value="Ya" {{
                                    $catatanPersalinan['kala2_distosia_bahu']=='Ya' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala2_distosia_bahu"
                                    data-field="catatanPersalinan.kala2_distosia_bahu">
                                 <label class="form-check-label" for="kala2_distosia_bahu_y">Ya, tindakan yang
                                    dilakukan</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala2_distosia_bahu_n"
                                    name="kala2_distosia_bahu" value="Tidak" {{
                                    $catatanPersalinan['kala2_distosia_bahu']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala2_distosia_bahu"
                                    data-field="catatanPersalinan.kala2_distosia_bahu">
                                 <label class="form-check-label" for="kala2_distosia_bahu_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">3. Kala III</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_lama">Lama kala III</label>
                           <input type="text" class="form-control" id="kala3_lama"
                              wire:model.defer="catatanPersalinan.kala3_lama" placeholder="Menit">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_oksitosin">Pemberian Oksitosin 10 U im</label>
                           <div class="d-flex">
                              <!-- Radio button kala3_oksitosin -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala3_oksitosin_y"
                                    name="kala3_oksitosin" value="Ya" {{ $catatanPersalinan['kala3_oksitosin']=='Ya'
                                    ? 'checked' : '' }} wire:model.defer="catatanPersalinan.kala3_oksitosin"
                                    data-field="catatanPersalinan.kala3_oksitosin">
                                 <label class="form-check-label" for="kala3_oksitosin_y">Ya, waktu</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala3_oksitosin_n"
                                    name="kala3_oksitosin" value="Tidak" {{
                                    $catatanPersalinan['kala3_oksitosin']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala3_oksitosin"
                                    data-field="catatanPersalinan.kala3_oksitosin">
                                 <label class="form-check-label" for="kala3_oksitosin_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_oks_2x">Pemberian ulang Oksitosin (2x)</label>
                           <div class="d-flex">
                              <!-- Radio button kala3_oks_2x -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala3_oks_2x_y" name="kala3_oks_2x"
                                    value="Ya" {{ $catatanPersalinan['kala3_oks_2x']=='Ya' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala3_oks_2x"
                                    data-field="catatanPersalinan.kala3_oks_2x">
                                 <label class="form-check-label" for="kala3_oks_2x_y">Ya, alasan</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala3_oks_2x_n" name="kala3_oks_2x"
                                    value="Tidak" {{ $catatanPersalinan['kala3_oks_2x']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala3_oks_2x"
                                    data-field="catatanPersalinan.kala3_oks_2x">
                                 <label class="form-check-label" for="kala3_oks_2x_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_penegangan_tali_pusat">Penegangan tali pusat terkendali</label>
                           <div class="d-flex">
                              <!-- Radio button kala3_penegangan_tali_pusat -->
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala3_penegangan_tali_pusat_y"
                                    name="kala3_penegangan_tali_pusat" value="Ya" {{
                                    $catatanPersalinan['kala3_penegangan_tali_pusat']=='Ya' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala3_penegangan_tali_pusat"
                                    data-field="catatanPersalinan.kala3_penegangan_tali_pusat">
                                 <label class="form-check-label" for="kala3_penegangan_tali_pusat_y">Ya</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala3_penegangan_tali_pusat_n"
                                    name="kala3_penegangan_tali_pusat" value="Tidak" {{
                                    $catatanPersalinan['kala3_penegangan_tali_pusat']=='Tidak' ? 'checked' : '' }}
                                    wire:model.defer="catatanPersalinan.kala3_penegangan_tali_pusat"
                                    data-field="catatanPersalinan.kala3_penegangan_tali_pusat">
                                 <label class="form-check-label" for="kala3_penegangan_tali_pusat_n">Tidak</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_plasenta_lengkap">Plasenta lahir lengkap (intact)</label>
                           <div class="d-flex">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala3_plasenta_lengkap_y"
                                    name="kala3_plasenta_lengkap" value="Ya" {{
                                    $catatanPersalinan['kala3_plasenta_lengkap']=='Ya' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.kala3_plasenta_lengkap', 'Ya')">
                                 <label class="form-check-label" for="kala3_plasenta_lengkap_y">Ya</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala3_plasenta_lengkap_n"
                                    name="kala3_plasenta_lengkap" value="Tidak" {{
                                    $catatanPersalinan['kala3_plasenta_lengkap']=='Tidak' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.kala3_plasenta_lengkap', 'Tidak')">
                                 <label class="form-check-label" for="kala3_plasenta_lengkap_n">Tidak</label>
                              </div>
                           </div>
                           <small class="text-muted">Jika tidak lengkap, tindakan yang dilakukan:</small>
                           <div class="mt-2">
                              <input type="text" class="form-control" placeholder="Tindakan a"
                                 wire:model.defer="tindakanPlasenta.a">
                              <input type="text" class="form-control mt-1" placeholder="Tindakan b"
                                 wire:model.defer="tindakanPlasenta.b">
                              <input type="text" class="form-control mt-1" placeholder="Tindakan c"
                                 wire:model.defer="tindakanPlasenta.c">
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="kala3_plasenta_lebih_30">Plasenta tidak lahir > 30 menit</label>
                           <div class="d-flex">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="kala3_plasenta_lebih_30_y"
                                    name="kala3_plasenta_lebih_30" value="Ya" {{
                                    $catatanPersalinan['kala3_plasenta_lebih_30']=='Ya' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.kala3_plasenta_lebih_30', 'Ya')">
                                 <label class="form-check-label" for="kala3_plasenta_lebih_30_y">Ya, tindakan</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="kala3_plasenta_lebih_30_n"
                                    name="kala3_plasenta_lebih_30" value="Tidak" {{
                                    $catatanPersalinan['kala3_plasenta_lebih_30']=='Tidak' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.kala3_plasenta_lebih_30', 'Tidak')">
                                 <label class="form-check-label" for="kala3_plasenta_lebih_30_n">Tidak</label>
                              </div>
                           </div>
                           <small class="text-muted">Jika ya, tindakan yang dilakukan:</small>
                           <div class="mt-2">
                              <input type="text" class="form-control" placeholder="Tindakan a"
                                 wire:model.defer="tindakanPlasenta30.a">
                              <input type="text" class="form-control mt-1" placeholder="Tindakan b"
                                 wire:model.defer="tindakanPlasenta30.b">
                              <input type="text" class="form-control mt-1" placeholder="Tindakan c"
                                 wire:model.defer="tindakanPlasenta30.c">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">4. Bayi Baru Lahir</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="bayi_berat_badan">Berat badan</label>
                           <div class="input-group">
                              <input type="text" class="form-control" id="bayi_berat_badan"
                                 wire:model.defer="catatanPersalinan.bayi_berat_badan">
                              <div class="input-group-append">
                                 <span class="input-group-text">gram</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="bayi_panjang">Panjang</label>
                           <div class="input-group">
                              <input type="text" class="form-control" id="bayi_panjang"
                                 wire:model.defer="catatanPersalinan.bayi_panjang">
                              <div class="input-group-append">
                                 <span class="input-group-text">cm</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="bayi_jenis_kelamin">Jenis kelamin</label>
                           <div class="d-flex">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="bayi_jenis_kelamin_l"
                                    name="bayi_jenis_kelamin" value="L" {{ $catatanPersalinan['bayi_jenis_kelamin']=='L'
                                    ? 'checked' : '' }} wire:click="$set('catatanPersalinan.bayi_jenis_kelamin', 'L')">
                                 <label class="form-check-label" for="bayi_jenis_kelamin_l">L</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="bayi_jenis_kelamin_p"
                                    name="bayi_jenis_kelamin" value="P" {{ $catatanPersalinan['bayi_jenis_kelamin']=='P'
                                    ? 'checked' : '' }} wire:click="$set('catatanPersalinan.bayi_jenis_kelamin', 'P')">
                                 <label class="form-check-label" for="bayi_jenis_kelamin_p">P</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="bayi_penilaian_bbl">Penilaian bayi baru lahir</label>
                           <div class="d-flex">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="bayi_penilaian_bbl_baik"
                                    name="bayi_penilaian_bbl" value="Baik" {{
                                    $catatanPersalinan['bayi_penilaian_bbl']=='Baik' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.bayi_penilaian_bbl', 'Baik')">
                                 <label class="form-check-label" for="bayi_penilaian_bbl_baik">Baik</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="bayi_penilaian_bbl_ada_penyulit"
                                    name="bayi_penilaian_bbl" value="Ada penyulit" {{
                                    $catatanPersalinan['bayi_penilaian_bbl']=='Ada penyulit' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.bayi_penilaian_bbl', 'Ada penyulit')">
                                 <label class="form-check-label" for="bayi_penilaian_bbl_ada_penyulit">Ada
                                    penyulit</label>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="bayi_pemberian_asi">Pemberian ASI</label>
                           <div class="d-flex">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="bayi_pemberian_asi_y"
                                    name="bayi_pemberian_asi" value="Ya" {{
                                    $catatanPersalinan['bayi_pemberian_asi']=='Ya' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.bayi_pemberian_asi', 'Ya')">
                                 <label class="form-check-label" for="bayi_pemberian_asi_y">Ya, jam setelah bayi
                                    lahir</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="bayi_pemberian_asi_n"
                                    name="bayi_pemberian_asi" value="Tidak" {{
                                    $catatanPersalinan['bayi_pemberian_asi']=='Tidak' ? 'checked' : '' }}
                                    wire:click="$set('catatanPersalinan.bayi_pemberian_asi', 'Tidak')">
                                 <label class="form-check-label" for="bayi_pemberian_asi_n">Tidak, alasan</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="bayi_lahir">Bayi lahir</label>
                           <div class="d-flex flex-wrap">
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="bayi_lahir_normal"
                                    name="kondisiBayi_status" value="Normal" {{ $kondisiBayi['status']=='Normal'
                                    ? 'checked' : '' }} wire:click="$set('kondisiBayi.status', 'Normal')">
                                 <label class="form-check-label" for="bayi_lahir_normal">Normal, tindakan:</label>
                              </div>
                              <div class="form-check mr-3">
                                 <input class="form-check-input" type="radio" id="bayi_lahir_asfiksia"
                                    name="kondisiBayi_status" value="Asfiksia" {{ $kondisiBayi['status']=='Asfiksia'
                                    ? 'checked' : '' }} wire:click="$set('kondisiBayi.status', 'Asfiksia')">
                                 <label class="form-check-label" for="bayi_lahir_asfiksia">Asfiksia
                                    ringan/sedang/biru/lemas, tindakan:</label>
                              </div>
                              <div class="form-check">
                                 <input class="form-check-input" type="radio" id="bayi_lahir_hipoterm"
                                    name="kondisiBayi_status" value="Hipoterm" {{ $kondisiBayi['status']=='Hipoterm'
                                    ? 'checked' : '' }} wire:click="$set('kondisiBayi.status', 'Hipoterm')">
                                 <label class="form-check-label" for="bayi_lahir_hipoterm">Hipoterm, tindakan:</label>
                              </div>
                           </div>
                           <div class="mt-2">
                              <div class="form-check mr-3 d-inline-block">
                                 <input class="form-check-input" type="checkbox" id="tindakan_keringkan"
                                    wire:model.defer="kondisiBayi.keringkan">
                                 <label class="form-check-label" for="tindakan_keringkan">mengeringkan</label>
                              </div>
                              <div class="form-check mr-3 d-inline-block">
                                 <input class="form-check-input" type="checkbox" id="tindakan_hangat"
                                    wire:model.defer="kondisiBayi.hangat">
                                 <label class="form-check-label" for="tindakan_hangat">menghangatkan</label>
                              </div>
                              <div class="form-check mr-3 d-inline-block">
                                 <input class="form-check-input" type="checkbox" id="tindakan_rangsang"
                                    wire:model.defer="kondisiBayi.rangsang">
                                 <label class="form-check-label" for="tindakan_rangsang">rangsang taktil</label>
                              </div>
                              <div class="form-check mr-3 d-inline-block">
                                 <input class="form-check-input" type="checkbox" id="tindakan_bebaskan"
                                    wire:model.defer="kondisiBayi.bebaskan">
                                 <label class="form-check-label" for="tindakan_bebaskan">bebaskan jalan napas</label>
                              </div>
                              <div class="form-check mr-3 d-inline-block">
                                 <input class="form-check-input" type="checkbox" id="tindakan_bungkus"
                                    wire:model.defer="kondisiBayi.bungkus">
                                 <label class="form-check-label" for="tindakan_bungkus">bungkus bayi dan tempatkan di
                                    sisi ibu</label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Akan ditambahkan bagian Kala IV setelah ini -->

            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">5. Kala IV</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="kala4_masalah">Masalah kala IV</label>
                           <textarea class="form-control" id="kala4_masalah" rows="2"
                              wire:model.defer="catatanPersalinan.kala4_masalah"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="kala4_penatalaksanaan">Penatalaksanaan masalah tersebut</label>
                           <textarea class="form-control" id="kala4_penatalaksanaan" rows="2"
                              wire:model.defer="catatanPersalinan.kala4_penatalaksanaan"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="kala4_hasil">Hasilnya</label>
                           <textarea class="form-control" id="kala4_hasil" rows="2"
                              wire:model.defer="catatanPersalinan.kala4_hasil"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="card mb-3">
               <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">6. Pemantauan Kala IV</h5>
               </div>
               <div class="card-body">
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                           <tr>
                              <th>Jam Ke</th>
                              <th>Waktu</th>
                              <th>Tekanan Darah</th>
                              <th>Nadi</th>
                              <th>Tinggi Fundus</th>
                              <th>Kontraksi</th>
                              <th>Kandung Kemih</th>
                              <th>Perdarahan</th>
                              <th>Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($pemantauanKala4 as $index => $pemantauan)
                           <tr>
                              <td>
                                 <input type="number" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.jam_ke">
                              </td>
                              <td>
                                 <input type="time" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.waktu">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.tekanan_darah" placeholder="120/80">
                              </td>
                              <td>
                                 <input type="number" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.nadi">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.tinggi_fundus">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.kontraksi">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.kandung_kemih">
                              </td>
                              <td>
                                 <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="pemantauanKala4.{{ $index }}.perdarahan">
                              </td>
                              <td>
                                 <!-- Tombol hapus baris -->
                                 <button type="button" class="btn btn-sm btn-danger btn-hapus-baris"
                                    wire:click.prevent="hapusBarisKala4({{ $index }})" wire:loading.attr="disabled"
                                    wire:target="hapusBarisKala4({{ $index }})">
                                    <i class="fas fa-trash" wire:loading.class="fa-spinner fa-spin"
                                       wire:target="hapusBarisKala4({{ $index }})"></i>
                                 </button>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                     <!-- Tombol tambah baris -->
                     <button type="button" id="btnTambahBarisKala4" class="btn btn-sm btn-success"
                        wire:click.prevent="tambahBarisKala4" wire:loading.attr="disabled"
                        wire:target="tambahBarisKala4">
                        <i class="fas fa-plus" wire:loading.class="fa-spinner fa-spin"
                           wire:target="tambahBarisKala4"></i>
                        <span wire:loading.remove wire:target="tambahBarisKala4">Tambah Baris</span>
                        <span wire:loading wire:target="tambahBarisKala4">Menambahkan...</span>
                     </button>
                  </div>
               </div>
            </div>

            <!-- Tombol Simpan dan Reset -->
            <div class="form-group">
               <button type="button" id="btnSimpanCatatanPersalinan" class="btn btn-primary"
                  wire:click.prevent="saveCatatanPersalinanForm" wire:loading.attr="disabled"
                  wire:target="saveCatatanPersalinanForm">
                  <i class="fas fa-save mr-1" wire:loading.class="fa-spinner fa-spin"
                     wire:target="saveCatatanPersalinanForm"></i>
                  <span wire:loading.remove wire:target="saveCatatanPersalinanForm">Simpan Catatan Persalinan</span>
                  <span wire:loading wire:target="saveCatatanPersalinanForm">Menyimpan...</span>
               </button>
               <button type="button" id="btnResetCatatanPersalinan" class="btn btn-secondary"
                  wire:click.prevent="resetFormCatatanPersalinan" wire:loading.attr="disabled"
                  wire:target="resetFormCatatanPersalinan">
                  <i class="fas fa-redo mr-1" wire:loading.class="fa-spinner fa-spin"
                     wire:target="resetFormCatatanPersalinan"></i>
                  <span wire:loading.remove wire:target="resetFormCatatanPersalinan">Reset</span>
                  <span wire:loading wire:target="resetFormCatatanPersalinan">Mereset...</span>
               </button>
            </div>
         </form>
      </div>
</div>
</div>
</div>
@else
<div class="alert alert-warning">
   <i class="fas fa-exclamation-triangle mr-2"></i>
   <strong>Perhatian!</strong> Modul partograf hanya dapat diakses jika pasien memiliki data ibu hamil. Silahkan
   daftarkan pasien sebagai ibu hamil terlebih dahulu.
</div>
@endif
</x-adminlte-card>

@push('scripts')
<!-- Load Chart.js secara langsung -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- Load SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
   // Sistem Notifikasi Persisten - Akan tetap muncul setelah refresh
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada notifikasi dari session PHP (punya prioritas lebih tinggi)
        @if (session()->has('partograf_notification'))
            const sessionNotification = {!! session('partograf_notification') !!};
            if (sessionNotification) {
                // Tampilkan notifikasi dari session langsung
                Swal.fire({
                    title: sessionNotification.title,
                    text: sessionNotification.text,
                    icon: sessionNotification.icon,
                    timer: sessionNotification.timer || 3000,
                    timerProgressBar: sessionNotification.timerProgressBar !== false,
                    toast: sessionNotification.toast !== false,
                    position: sessionNotification.position || 'top-end',
                    showConfirmButton: sessionNotification.showConfirmButton !== false
                });
                
                // Hapus notifikasi dari session
                @php
                    session()->forget('partograf_notification');
                @endphp
            }
        @else
            // Cek localStorage jika tidak ada notifikasi session
            const savedNotification = localStorage.getItem('partograf_notification');
            if (savedNotification) {
                // Parse notifikasi tersimpan
                const notification = JSON.parse(savedNotification);
                // Tampilkan notifikasi menggunakan SweetAlert
                Swal.fire({
                    title: notification.title,
                    text: notification.text,
                    icon: notification.icon,
                    timer: notification.timer || 3000,
                    timerProgressBar: notification.timerProgressBar !== false,
                    toast: notification.toast !== false,
                    position: notification.position || 'top-end',
                    showConfirmButton: notification.showConfirmButton !== false
                });
                // Hapus notifikasi dari localStorage setelah ditampilkan
                localStorage.removeItem('partograf_notification');
            }
        @endif
        
        // Fungsi untuk menyimpan notifikasi ke localStorage
        function saveNotification(options) {
            localStorage.setItem('partograf_notification', JSON.stringify(options));
        }
        
        // Event handler untuk menyimpan notifikasi sebelum refresh
        window.addEventListener('catatanPersalinanSaved', function (event) {
            saveNotification({
                title: 'Berhasil!',
                text: 'Catatan persalinan berhasil disimpan',
                icon: 'success',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        window.addEventListener('catatan-persalinan-reset', function (event) {
            saveNotification({
                title: 'Form Direset',
                text: 'Form catatan persalinan telah direset',
                icon: 'info',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        window.addEventListener('pemantauanKala4Ditambahkan', function (event) {
            saveNotification({
                title: 'Berhasil!',
                text: 'Baris pemantauan Kala 4 berhasil ditambahkan',
                icon: 'success',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        window.addEventListener('pemantauanKala4Diperbarui', function (event) {
            saveNotification({
                title: 'Berhasil!',
                text: 'Pemantauan Kala 4 berhasil diperbarui',
                icon: 'success',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        window.addEventListener('errorSave', function (event) {
            saveNotification({
                title: 'Error!',
                text: event.detail?.message || 'Terjadi kesalahan saat menyimpan data',
                icon: 'error',
                timer: 5000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        window.addEventListener('errorResetForm', function (event) {
            saveNotification({
                title: 'Error!',
                text: event.detail?.message || 'Terjadi kesalahan saat mereset form',
                icon: 'error',
                timer: 5000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
        
        // Juga simpan posisi scroll
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('partograf_scroll_position', window.scrollY);
        });
        
        // Kembalikan posisi scroll
        const savedScrollPosition = sessionStorage.getItem('partograf_scroll_position');
        if (savedScrollPosition) {
            setTimeout(() => {
                window.scrollTo({
                    top: parseInt(savedScrollPosition),
                    behavior: 'auto'
                });
            }, 100);
        }
        
        // Sisa script lainnya...
    });
</script>
@endpush
</div>