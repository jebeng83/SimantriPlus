<div>
   <!-- Tambahkan CSS Kustom -->
   <style>
      :root {
         --primary: #1e40af;
         /* Biru tua yang lebih elegan */
         --primary-light: #3b82f6;
         /* Biru yang lebih cerah */
         --primary-dark: #1e3a8a;
         /* Biru sangat tua untuk efek hover */
         --secondary: #0f766e;
         /* Hijau kebiruan elegan */
         --light: #f8fafc;
         /* Abu-abu sangat terang */
         --dark: #1e293b;
         /* Abu-abu sangat gelap */
         --gray: #94a3b8;
         /* Abu-abu sedang */
         --danger: #dc2626;
         /* Merah yang tidak terlalu mencolok */
         --warning: #ea580c;
         /* Oranye kemerahan */
         --success: #16a34a;
         /* Hijau yang segar */
         --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
         /* Bayangan yang lebih halus */
         --transition: all 0.25s ease;
         /* Transisi yang sedikit lebih cepat */
         --input-bg: #ffffff;
         --form-bg: #f1f5f9;
         /* Warna latar belakang form yang lebih elegan */
         --border-radius: 8px;
         /* Border radius yang konsisten */
         --card-border: 1px solid rgba(226, 232, 240, 0.8);
         /* Border kartu yang halus */
      }

      .ilp-form-container {
         background-color: var(--form-bg);
         border-radius: var(--border-radius);
         overflow: hidden;
         box-shadow: 0 0 25px rgba(0, 0, 0, 0.04);
         font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
      }

      .ilp-form-section {
         border-radius: var(--border-radius);
         overflow: hidden;
         transition: var(--transition);
         margin-bottom: 1.5rem;
         box-shadow: var(--shadow);
         border: var(--card-border);
         background-color: var(--input-bg);
      }

      .ilp-form-header {
         background: linear-gradient(135deg, var(--primary), var(--primary-light));
         color: white;
         padding: 18px 24px;
         font-weight: 600;
         font-size: 1.1rem;
         display: flex;
         align-items: center;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
         letter-spacing: 0.3px;
      }

      .ilp-form-header i {
         margin-right: 12px;
         font-size: 1.2rem;
      }

      .ilp-form-body {
         padding: 28px;
         background-color: white;
      }

      .section-title {
         font-size: 1rem;
         font-weight: 600;
         color: var(--primary);
         margin-bottom: 1.5rem;
         padding-bottom: 0.8rem;
         border-bottom: 2px solid #e2e8f0;
         display: flex;
         align-items: center;
      }

      .section-title i {
         margin-right: 10px;
         color: var(--primary);
         font-size: 1.1rem;
      }

      .form-group label {
         font-weight: 500;
         color: var(--dark);
         font-size: 0.9rem;
         margin-bottom: 0.5rem;
         display: block;
         letter-spacing: 0.2px;
      }

      .form-control {
         border-radius: var(--border-radius);
         border: 1px solid #e2e8f0;
         padding: 10px 15px;
         height: auto;
         font-size: 0.95rem;
         transition: var(--transition);
         box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
         background-color: var(--input-bg);
      }

      .form-control:focus {
         border-color: var(--primary-light);
         box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
         outline: none;
      }

      .form-control::placeholder {
         color: #cbd5e1;
         font-size: 0.9rem;
      }

      select.form-control {
         background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
         background-repeat: no-repeat;
         background-position: right 15px center;
         background-size: 16px;
         padding-right: 40px;
         -webkit-appearance: none;
         -moz-appearance: none;
         appearance: none;
      }

      .input-group-append .btn {
         border-top-right-radius: var(--border-radius);
         border-bottom-right-radius: var(--border-radius);
         padding: 10px 15px;
      }

      .btn {
         padding: 12px 20px;
         border-radius: var(--border-radius);
         font-weight: 500;
         letter-spacing: 0.3px;
         transition: var(--transition);
         text-transform: uppercase;
         font-size: 0.85rem;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .btn-primary {
         background: linear-gradient(135deg, var(--primary), var(--primary-light));
         border: none;
         box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
      }

      .btn-primary:hover,
      .btn-primary:focus {
         background: linear-gradient(135deg, var(--primary-dark), var(--primary));
         transform: translateY(-2px);
         box-shadow: 0 6px 12px rgba(59, 130, 246, 0.25);
      }

      .btn-danger {
         background: linear-gradient(135deg, var(--danger), #ef4444);
         border: none;
         box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
      }

      .btn-danger:hover,
      .btn-danger:focus {
         background: linear-gradient(135deg, #b91c1c, var(--danger));
         transform: translateY(-2px);
         box-shadow: 0 6px 12px rgba(220, 38, 38, 0.25);
      }

      .btn-warning {
         background: linear-gradient(135deg, var(--warning), #f97316);
         border: none;
         box-shadow: 0 4px 10px rgba(234, 88, 12, 0.2);
         color: white;
      }

      .btn-warning:hover,
      .btn-warning:focus {
         background: linear-gradient(135deg, #c2410c, var(--warning));
         transform: translateY(-2px);
         box-shadow: 0 6px 12px rgba(234, 88, 12, 0.25);
         color: white;
      }

      .btn i {
         margin-right: 8px;
         font-size: 0.9rem;
      }

      .modal-content {
         border-radius: 12px;
         overflow: hidden;
         box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
         border: none;
      }

      .modal-header {
         background: linear-gradient(135deg, var(--primary), var(--primary-light));
         color: white;
         padding: 18px 24px;
         border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }

      .modal-header .close {
         color: white;
         opacity: 0.8;
         text-shadow: none;
      }

      .modal-header .close:hover {
         opacity: 1;
      }

      .modal-body {
         padding: 28px;
      }

      .modal-footer {
         border-top: 1px solid #e2e8f0;
         padding: 15px 24px;
      }

      .table-responsive {
         border-radius: var(--border-radius);
         overflow: hidden;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
      }

      .table {
         margin-bottom: 0;
         border-collapse: separate;
         border-spacing: 0;
      }

      .table thead th {
         background-color: #f8fafc;
         color: var(--dark);
         font-weight: 600;
         padding: 14px 16px;
         border-bottom: 2px solid #e2e8f0;
         font-size: 0.9rem;
         text-transform: uppercase;
         letter-spacing: 0.5px;
      }

      .table tbody td {
         padding: 14px 16px;
         vertical-align: middle;
         border-top: 1px solid #e2e8f0;
         font-size: 0.95rem;
      }

      .table-striped tbody tr:nth-of-type(odd) {
         background-color: #f8fafc;
      }

      .table tbody tr:hover {
         background-color: #f1f5f9;
      }

      .alert {
         border-radius: var(--border-radius);
         padding: 16px;
         margin-bottom: 20px;
         border: none;
      }

      .alert-info {
         background-color: #eff6ff;
         color: #1e40af;
         border-left: 4px solid #3b82f6;
      }

      .alert-secondary {
         background-color: #f1f5f9;
         color: #334155;
         border-left: 4px solid #94a3b8;
      }

      .form-text {
         font-size: 0.85rem;
         margin-top: 4px;
      }

      .text-success {
         color: #16a34a !important;
      }

      .text-muted {
         color: #64748b !important;
      }

      /* Animasi dan efek smooth */
      @keyframes fadeIn {
         from {
            opacity: 0;
            transform: translateY(10px);
         }

         to {
            opacity: 1;
            transform: translateY(0);
         }
      }

      .fade-in {
         animation: fadeIn 0.3s ease-out forwards;
      }

      .animate-pulse {
         animation: pulse 2s infinite;
      }

      @keyframes pulse {
         0% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
         }

         70% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
         }

         100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
         }
      }

      /* Efek ripple untuk tombol */
      .btn {
         position: relative;
         overflow: hidden;
      }

      .ripple {
         position: absolute;
         background: rgba(255, 255, 255, 0.3);
         border-radius: 50%;
         transform: scale(0);
         animation: ripple 0.6s linear;
         pointer-events: none;
         width: 100px;
         height: 100px;
         margin-left: -50px;
         margin-top: -50px;
      }

      @keyframes ripple {
         to {
            transform: scale(4);
            opacity: 0;
         }
      }

      /* Media queries untuk responsivitas */
      @media (max-width: 768px) {
         .ilp-form-body {
            padding: 20px;
         }

         .btn {
            padding: 10px 15px;
            font-size: 0.8rem;
         }

         .section-title {
            font-size: 0.95rem;
         }
      }
   </style>

   <div class="ilp-form-container fade-in">
      <form wire:submit.prevent="simpan">
         @csrf
         <input type="hidden" name="no_rawat" value="{{ $noRawat }}">

         <!-- Form Pemeriksaan -->
         <div class="ilp-form-section">
            <div class="ilp-form-header">
               <i class="fas fa-user-md"></i> Form Pemeriksaan ILP
            </div>
            <div class="ilp-form-body">
               <!-- Data Identitas -->
               <div class="section-title">
                  <i class="fas fa-id-card mr-2"></i> Data Identitas
               </div>
               <div class="row mb-4">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label><i class="fas fa-id-card-alt mr-1"></i> No. KTP:</label>
                        <input type="text" wire:model="no_ktp" class="form-control" placeholder="Masukkan nomor KTP">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label><i class="fas fa-hospital mr-1"></i> Data Posyandu:</label>
                        <div class="input-group">
                           <input type="text" wire:model.defer="data_posyandu" class="form-control" readonly
                              placeholder="Pilih data posyandu">
                           <div class="input-group-append">
                              <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                                 data-target="#posyanduModal">
                                 <i class="fas fa-search"></i>
                              </button>
                           </div>
                        </div>
                        @if($data_posyandu)
                        <small class="form-text text-success">
                           <i class="fas fa-check-circle"></i> Data posyandu dipilih: {{ $data_posyandu }}
                        </small>
                        @endif
                     </div>
                  </div>
               </div>

               <!-- Riwayat Penyakit -->
               <div class="section-title">
                  <i class="fas fa-notes-medical mr-2"></i> Riwayat Penyakit
               </div>
               <div class="row mb-4">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label><i class="fas fa-user-injured mr-1"></i> Riwayat Penyakit Diri Sendiri:</label>
                        <select wire:model="riwayat_diri_sendiri" class="form-control">
                           <option value="Normal">Normal</option>
                           <option value="Hipertensi">Hipertensi</option>
                           <option value="Diabetes militus">Diabetes Militus</option>
                           <option value="Stroke">Stroke</option>
                           <option value="Jantung">Jantung</option>
                           <option value="Asma">Asma</option>
                           <option value="Kanker">Kanker</option>
                           <option value="Kolesterol">Kolesterol</option>
                           <option value="Hepatitis">Hepatitis</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label><i class="fas fa-users mr-1"></i> Riwayat Penyakit Keluarga:</label>
                        <select wire:model="riwayat_keluarga" class="form-control">
                           <option value="Normal">Normal</option>
                           <option value="Hipertensi">Hipertensi</option>
                           <option value="Diabetes militus">Diabetes Militus</option>
                           <option value="Stroke">Stroke</option>
                           <option value="Jantung">Jantung</option>
                           <option value="Asma">Asma</option>
                           <option value="Kanker">Kanker</option>
                           <option value="Kolesterol">Kolesterol</option>
                           <option value="Hepatitis">Hepatitis</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Faktor Risiko -->
               <div class="section-title">
                  <i class="fas fa-exclamation-triangle mr-2"></i> Faktor Risiko
               </div>
               <div class="row mb-4">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label><i class="fas fa-smoking mr-1"></i> Merokok:</label>
                        <select wire:model="merokok" class="form-control">
                           <option value="Tidak">Tidak</option>
                           <option value="Ya">Ya</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-8">
                     <div class="form-group">
                        <label><i class="fas fa-utensils mr-1"></i> Konsumsi Tinggi:</label>
                        <input type="text" wire:model="konsumsi_tinggi" class="form-control"
                           placeholder="Contoh: Garam, Gula, Lemak">
                     </div>
                  </div>
               </div>

               <!-- Pengukuran -->
               <div class="section-title">
                  <i class="fas fa-weight mr-2"></i> Pengukuran Antropometri & Vital Sign
               </div>
               <div class="row mb-4">
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-weight mr-1"></i> BB (kg):</label>
                        <input type="text" wire:model="berat_badan" wire:change="hitungIMT" class="form-control"
                           placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-ruler-vertical mr-1"></i> TB (cm):</label>
                        <input type="text" wire:model="tinggi_badan" wire:change="hitungIMT" class="form-control"
                           placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-calculator mr-1"></i> IMT:</label>
                        <input type="text" wire:model="imt" class="form-control" readonly>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-tape mr-1"></i> LP (cm):</label>
                        <input type="text" wire:model="lp" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-heartbeat mr-1"></i> TD (mmHg):</label>
                        <input type="text" wire:model="td" class="form-control" placeholder="120/80">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-tint mr-1"></i> Gula Darah:</label>
                        <input type="text" wire:model="gula_darah" class="form-control" placeholder="0.0">
                     </div>
                  </div>
               </div>

               <!-- Pemeriksaan Fisik -->
               <div class="section-title">
                  <i class="fas fa-stethoscope mr-2"></i> Pemeriksaan Fisik
               </div>
               <div class="row mb-4">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-eye mr-1"></i> Metode Mata:</label>
                        <select wire:model="metode_mata" class="form-control">
                           <option value="Snelen Card">Snelen Card</option>
                           <option value="hitungjari">Hitung Jari</option>
                           <option value="visus">Visus</option>
                           <option value="pinhole">Pinhole</option>
                           <option value="snelen card">Snelen Card</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-eye-dropper mr-1"></i> Hasil Mata:</label>
                        <select wire:model="hasil_mata" class="form-control">
                           <option value="normal">Normal</option>
                           <option value="tidak normal">Tidak Normal</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-deaf mr-1"></i> Tes Berbisik:</label>
                        <select wire:model="tes_berbisik" class="form-control">
                           <option value="normal">Normal</option>
                           <option value="tidak normal">Tidak Normal</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-tooth mr-1"></i> Gigi:</label>
                        <select wire:model="gigi" class="form-control">
                           <option value="normal">Normal</option>
                           <option value="caries">Caries</option>
                           <option value="jaringan Periodental">Jaringan Periodental</option>
                           <option value="goyang">Goyang</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Pemeriksaan Lanjutan -->
               <div class="section-title">
                  <i class="fas fa-microscope mr-2"></i> Pemeriksaan Lanjutan
               </div>
               <div class="row mb-4">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label><i class="fas fa-brain mr-1"></i> Kesehatan Jiwa:</label>
                        <select wire:model="kesehatan_jiwa" class="form-control">
                           <option value="normal">Normal</option>
                           <option value="gangguan emosional">Gangguan Emosional</option>
                           <option value="gangguan perilaku">Gangguan Perilaku</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label><i class="fas fa-lungs mr-1"></i> TBC:</label>
                        <input type="text" wire:model="tbc" class="form-control" placeholder="Hasil tes TBC">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label><i class="fas fa-liver mr-1"></i> Fungsi Hati:</label>
                        <select wire:model="fungsi_hari" class="form-control">
                           <option value="Normal">Normal</option>
                           <option value="Hepatitis B">Hepatitis B</option>
                           <option value="Hepatitis C">Hepatitis C</option>
                           <option value="Sirosis">Sirosis</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Pemeriksaan Khusus -->
               <div class="section-title">
                  <i class="fas fa-clipboard-list mr-2"></i> Pemeriksaan Khusus
               </div>
               <div class="row mb-4">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-syringe mr-1"></i> Status TT:</label>
                        <select wire:model="status_tt" class="form-control">
                           <option value="-">-</option>
                           <option value="1">1</option>
                           <option value="2">2</option>
                           <option value="3">3</option>
                           <option value="4">4</option>
                           <option value="5">5</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-virus mr-1"></i> Penyakit Lain Catin:</label>
                        <select wire:model="penyakit_lain_catin" class="form-control">
                           <option value="Normal">Normal</option>
                           <option value="Anemia">Anemia</option>
                           <option value="HIV">HIV</option>
                           <option value="Sifilis">Sifilis</option>
                           <option value="Napza">Napza</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-venus mr-1"></i> Kanker Payudara:</label>
                        <select wire:model="kanker_payudara" class="form-control">
                           <option value="Normal">Normal</option>
                           <option value="ada benjolan">Ada Benjolan</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-check-circle mr-1"></i> IVA Test:</label>
                        <select wire:model="iva_test" class="form-control">
                           <option value="Negatif">Negatif</option>
                           <option value="Positif">Positif</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Pemeriksaan Laboratorium -->
               <div class="section-title">
                  <i class="fas fa-flask mr-2"></i> Pemeriksaan Laboratorium
               </div>
               <div class="row mb-4">
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-tint mr-1"></i> GDS:</label>
                        <input type="text" wire:model="gds" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-vial mr-1"></i> Asam Urat:</label>
                        <input type="text" wire:model="asam_urat" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-vial mr-1"></i> Kolesterol:</label>
                        <input type="text" wire:model="kolesterol" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-vial mr-1"></i> Trigliserida:</label>
                        <input type="text" wire:model="trigliserida" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-vial mr-1"></i> Ureum:</label>
                        <input type="text" wire:model="ureum" class="form-control" placeholder="0.0">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label><i class="fas fa-vial mr-1"></i> Kreatinin:</label>
                        <input type="text" wire:model="kreatinin" class="form-control" placeholder="0.0">
                     </div>
                  </div>
               </div>

               <!-- Risiko Penyakit -->
               <div class="section-title">
                  <i class="fas fa-heartbeat mr-2"></i> Risiko Penyakit
               </div>
               <div class="row mb-4">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-heart mr-1"></i> Risiko Jantung:</label>
                        <select wire:model="resiko_jantung" class="form-control">
                           <option value="Tidak">Tidak</option>
                           <option value="Ya">Ya</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-chart-pie mr-1"></i> Charta:</label>
                        <select wire:model="charta" class="form-control">
                           <option value="<10%">&lt;10%</option>
                           <option value="10% - 20%">10% - 20%</option>
                           <option value="20% - 30%">20% - 30%</option>
                           <option value="30% - 40%">30% - 40%</option>
                           <option value="> 40%">&gt; 40%</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-ribbon mr-1"></i> Risiko Kanker Usus:</label>
                        <select wire:model="resiko_kanker_usus" class="form-control">
                           <option value="Tidak">Tidak</option>
                           <option value="Ya">Ya</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label><i class="fas fa-clipboard-check mr-1"></i> Skor PUMA:</label>
                        <select wire:model="skor_puma" class="form-control">
                           <option value="< 6">&lt; 6</option>
                           <option value="> 6">&gt; 6</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Catatan -->
               <div class="section-title">
                  <i class="fas fa-comment-medical mr-2"></i> Catatan Pemeriksa
               </div>
               <div class="row mb-4">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label><i class="fas fa-clipboard mr-1"></i> Catatan:</label>
                        <textarea wire:model="skilas" class="form-control" rows="3"
                           placeholder="Tambahkan catatan pemeriksaan di sini..."></textarea>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Tombol Submit -->
         <div class="row mt-4 mb-5 fade-in">
            <div class="col-md-12 d-flex">
               <button type="submit" class="btn btn-primary mr-2">
                  <i class="fas fa-save"></i> Simpan
               </button>
               <button type="button" class="btn btn-danger mr-2"
                  onclick="window.location.href='{{ route('ralan.pasien') }}'">
                  <i class="fas fa-times"></i> Batal
               </button>
               @if($ilpDewasa)
               <button type="button" class="btn btn-warning" wire:click="hapusIlpDewasa">
                  <i class="fas fa-trash"></i> Hapus
               </button>
               @endif
            </div>
         </div>
      </form>

      <!-- Modal Pencarian Posyandu -->
      <div class="modal fade" id="posyanduModal" tabindex="-1" role="dialog" aria-labelledby="posyanduModalLabel"
         aria-hidden="true" wire:ignore.self>
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="posyanduModalLabel"><i class="fas fa-hospital mr-2"></i> Pencarian Data
                     Posyandu</h5>
                  <button type="button" class="close btn-close-header-posyandu" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label><i class="fas fa-search mr-1"></i> Cari Posyandu:</label>
                     <div class="input-group">
                        <input type="text" wire:model.debounce.500ms="searchPosyandu" class="form-control"
                           placeholder="Ketik nama posyandu atau desa (min. 2 huruf)">
                        <div class="input-group-append">
                           <span class="input-group-text bg-primary text-white">
                              <div wire:loading wire:target="searchPosyandu">
                                 <i class="fas fa-spinner fa-spin"></i>
                              </div>
                              <div wire:loading.remove wire:target="searchPosyandu">
                                 <i class="fas fa-search"></i>
                              </div>
                           </span>
                        </div>
                     </div>
                     <small class="form-text text-muted">Masukkan minimal 2 huruf untuk mencari</small>
                  </div>

                  <div wire:loading wire:target="updatedSearchPosyandu" class="alert alert-info">
                     <i class="fas fa-spinner fa-spin mr-2"></i> Sedang mencari data posyandu...
                  </div>

                  @if(count($posyanduList) > 0)
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                           <tr>
                              <th>Kode</th>
                              <th>Nama Posyandu</th>
                              <th>Alamat</th>
                              <th>Desa</th>
                              <th>Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($posyanduList as $posyandu)
                           <tr>
                              <td>{{ $posyandu->kode_posyandu ?? '-' }}</td>
                              <td><strong>{{ $posyandu->nama_posyandu ?? '-' }}</strong></td>
                              <td>{{ $posyandu->alamat ?? '-' }}</td>
                              <td>{{ $posyandu->desa ?? '-' }}</td>
                              <td>
                                 <button type="button" class="btn btn-sm btn-primary"
                                    wire:click="selectPosyandu('{{ $posyandu->nama_posyandu ?? '-' }}', '{{ $posyandu->alamat ?? '-' }}', '{{ $posyandu->desa ?? '-' }}')"
                                    wire:loading.attr="disabled">
                                    <i class="fas fa-check"></i> Pilih
                                 </button>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
                  @elseif(strlen($searchPosyandu) >= 2 && !count($posyanduList) && !$errors->any())
                  <div class="alert alert-info">
                     <i class="fas fa-info-circle mr-1"></i> Data posyandu tidak ditemukan. Silakan coba kata kunci
                     lain.
                  </div>
                  @elseif(strlen($searchPosyandu) < 2) <div class="alert alert-secondary">
                     <i class="fas fa-search mr-1"></i> Ketik minimal 2 huruf untuk mencari data posyandu.
               </div>
               @endif
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary btn-close-posyandu" data-dismiss="modal">Tutup</button>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

@section('js')
<script>
   $(document).ready(function() {
    // Hitung IMT otomatis
    $('input[name="berat_badan"], input[name="tinggi_badan"]').on('change', function() {
        var bb = $('input[name="berat_badan"]').val();
        var tb = $('input[name="tinggi_badan"]').val() / 100; // Konversi ke meter
        
        if (bb && tb) {
            var imt = (bb / (tb * tb)).toFixed(2);
            $('input[name="imt"]').val(imt);
        }
    });

    // Event listener untuk menutup modal dari Livewire
    window.addEventListener('closeModal', event => {
        $('#' + event.detail.modalId).modal('hide');
        
        // Pastikan modal benar-benar ditutup
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }, 200);
    });
    
    // Animasi smooth untuk elemen form
    $('.ilp-form-section, .btn').addClass('fade-in');
    
    // Efek hover pada baris tabel
    $('.table tbody tr').hover(
        function() {
            $(this).css('background-color', 'rgba(59, 130, 246, 0.05)');
        },
        function() {
            $(this).css('background-color', '');
        }
    );
    
    // Tambahkan efek ripple pada tombol
    $('.btn').on('click', function(e) {
        var x = e.pageX - $(this).offset().left;
        var y = e.pageY - $(this).offset().top;
        
        var ripple = $('<span class="ripple"></span>');
        ripple.css({
            left: x + 'px',
            top: y + 'px'
        });
        
        $(this).append(ripple);
        
        setTimeout(function() {
            ripple.remove();
        }, 600);
    });

    // Perbaikan untuk modal posyandu
    $('#posyanduModal').on('hidden.bs.modal', function (e) {
        // Pastikan modal benar-benar ditutup
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        
        // Reset scroll jika diperlukan
        $(document.body).css('overflow', '');
    });
    
    // Handler tambahan untuk tombol tutup
    $('.modal .btn-secondary[data-dismiss="modal"]').on('click', function() {
        setTimeout(function() {
            if ($('.modal-backdrop').length > 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }
        }, 300);
    });

    // Handler khusus untuk tombol tutup posyandu
    $('.btn-close-posyandu').on('click', function(e) {
        e.preventDefault();
        $('#posyanduModal').modal('hide');
        
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $(document.body).css('overflow', '');
        }, 300);
    });

    // Handler untuk tombol X di header modal posyandu
    $('.btn-close-header-posyandu').on('click', function(e) {
        e.preventDefault();
        $('#posyanduModal').modal('hide');
        
        setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $(document.body).css('overflow', '');
        }, 300);
    });

    // Tambahkan event listener khusus untuk modal posyandu
    $('#posyanduModal').on('shown.bs.modal', function() {
        $(this).find('input[wire\\:model\\.debounce\\.500ms="searchPosyandu"]').focus();
    });
    
    // Event listener untuk refresh komponen Livewire
    window.Livewire.on('refreshComponent', function() {
        // Data posyandu dipilih, perbarui tampilan
        setTimeout(function() {
            // Pastikan backdrop modal benar-benar dihapus
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }, 300);
    });
    
    // Animasi fokus input saat diklik
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('input-focused');
    }).on('blur', function() {
        $(this).parent().removeClass('input-focused');
    });
    
    // Efek hover untuk tombol
    $('.btn').hover(
        function() {
            $(this).css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
    
    // Perbaikan z-index untuk modal
    $('#posyanduModal').css('z-index', '1050');
    $('.modal-backdrop').css('z-index', '1040');
});
</script>

<style>
   /* Efek fokus pada input */
   .input-focused {
      transition: all 0.2s ease;
   }

   .input-focused label {
      color: var(--primary);
      font-weight: 600;
   }

   /* Efek pada card saat hover */
   .ilp-form-section:hover {
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
   }

   /* Perbaikan tampilan textarea */
   textarea.form-control {
      min-height: 100px;
      line-height: 1.5;
   }

   /* Perbaikan tampilan pada mobile */
   @media (max-width: 576px) {
      .ilp-form-header {
         padding: 15px 20px;
         font-size: 1rem;
      }

      .ilp-form-body {
         padding: 20px 15px;
      }

      .row>div[class^="col-"] {
         margin-bottom: 15px;
      }

      .btn {
         width: 100%;
         margin-bottom: 10px;
         justify-content: center;
      }

      .col-md-12.d-flex {
         flex-direction: column;
      }
   }
</style>
@endsection