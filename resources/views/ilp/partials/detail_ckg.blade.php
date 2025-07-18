<div class="row">
   <!-- Data Identitas Diri -->
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-primary">
            <h5 class="card-title"><i class="fas fa-id-card mr-2"></i> Data Identitas Diri</h5>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered">
                     <tr>
                        <th width="40%">NIK</th>
                        <td>{{ $detail->nik ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Nama Lengkap</th>
                        <td id="nama-lengkap">{{ $detail->nama_lengkap ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Tanggal Lahir</th>
                        <td>{{ $detail->tanggal_lahir ? date('d-m-Y', strtotime($detail->tanggal_lahir)) : '-' }}</td>
                     </tr>
                     <tr>
                        <th>Umur</th>
                        <td>{{ $detail->umur ? $detail->umur . ' tahun' : '-' }}</td>
                     </tr>
                     <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $detail->jenis_kelamin == 'L' ? 'Laki-laki' : ($detail->jenis_kelamin == 'P' ?
                           'Perempuan' : '-') }}</td>
                     </tr>
                     <tr>
                        <th>Pekerjaan</th>
                        <td>{{ $detail->pekerjaan ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-bordered">
                     <tr>
                        <th width="40%">No. Handphone</th>
                        <td>{{ $detail->no_handphone ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>No. Rekam Medis</th>
                        <td>{{ $detail->no_rkm_medis ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>No. Peserta BPJS</th>
                        <td id="no-peserta-bpjs">{{ $detail->no_peserta ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Tanggal Skrining</th>
                        <td>{{ $detail->tanggal_skrining ? date('d-m-Y', strtotime($detail->tanggal_skrining)) : '-' }}
                        </td>
                     </tr>
                     <tr>
                        <th>Status</th>
                        <td>
                           <span class="badge {{ $detail->status == '1' ? 'badge-success' : 'badge-warning' }}">
                              {{ $detail->status == '1' ? 'Selesai' : 'Menunggu' }}
                           </span>
                        </td>
                     </tr>
                     <tr>
                        <th>Petugas Entry <span class="text-danger">*</span></th>
                        <td>
                           @if(isset($detail->id_petugas_entri) && $detail->petugas_entry_nama)
                    {{ $detail->petugas_entry_nama ?? '-' }}
                           @else
                              <select name="id_petugas_entri" id="id_petugas_entri" class="form-control" required style="border-left: 3px solid #dc3545;">
                                 <option value="">-- Pilih Petugas Entry --</option>
                                 @foreach($pegawai_aktif as $pegawai)
                                    <option value="{{ $pegawai->nik }}" {{ (isset($detail->id_petugas_entri) && $detail->id_petugas_entri == $pegawai->nik) ? 'selected' : '' }}>
                                       {{ $pegawai->nama }}
                                    </option>
                                 @endforeach
                              </select>
                              <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Field ini wajib diisi</small>
                           @endif
                        </td>
                     </tr>
                  </table>
               </div>
            </div>
            <!-- Data Wali untuk usia di bawah 6 tahun -->
            @if(isset($detail->umur) && $detail->umur < 6)
            <div class="row mt-3">
               <div class="col-md-12">
                  <div class="alert alert-info">
                     <h6><i class="fas fa-user-friends mr-2"></i> Data Wali (Usia di bawah 6 tahun)</h6>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <table class="table table-bordered">
                           <tr>
                              <th width="40%">NIK Wali</th>
                              <td>{{ $detail->nik_wali ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>Nama Wali</th>
                              <td>{{ $detail->nama_wali ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                     <div class="col-md-6">
                        <table class="table table-bordered">
                           <tr>
                              <th width="40%">Tanggal Lahir Wali</th>
                              <td>{{ $detail->tanggal_lahir_wali ? date('d-m-Y', strtotime($detail->tanggal_lahir_wali)) : '-' }}</td>
                           </tr>
                           <tr>
                              <th>Jenis Kelamin Wali</th>
                              <td>{{ $detail->jenis_kelamin_wali == 'L' ? 'Laki-laki' : ($detail->jenis_kelamin_wali == 'P' ? 'Perempuan' : '-') }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            @endif
            
            <div class="row mt-3">
               <div class="col-md-12">
                  <table class="table table-bordered">
                     <tr>
                        <th width="20%">Alamat</th>
                        <td>{{ $detail->alamatpj ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kelurahan</th>
                        <td>{{ $detail->kelurahanpj ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kecamatan</th>
                        <td>{{ $detail->kecamatanpj ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kabupaten</th>
                        <td>{{ $detail->kabupatenpj ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Pemeriksaan Anak Usia dibawah 6 Tahun -->
   @if(isset($detail->umur) && $detail->umur < 6)
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-warning">
            <h5 class="card-title"><i class="fas fa-child mr-2"></i> Pemeriksaan Anak Usia dibawah 6 Tahun</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionAnakDibawah6">
               <!-- Gejala DM Anak -->
               <div class="card mb-0">
                  <div class="card-header" id="headingDMAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseDMAnak" aria-expanded="false" aria-controls="collapseDMAnak">
                           Gejala DM Anak
                        </button>
                     </h2>
                  </div>
                  <div id="collapseDMAnak" class="collapse" aria-labelledby="headingDMAnak" data-parent="#accordionAnakDibawah6">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah anak sering merasa lapar?</th>
                              <td>{{ $detail->sering_lapar ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah anak sering merasa haus?</th>
                              <td>{{ $detail->sering_haus ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah anak sering buang air kecil?</th>
                              <td>{{ $detail->sering_pipis ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah anak sering mengompol?</th>
                              <td>{{ $detail->sering_mengompol ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Apakah berat badan anak turun tanpa sebab yang jelas?</th>
                              <td>{{ $detail->berat_turun ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>6. Apakah ada riwayat diabetes pada orang tua?</th>
                              <td>{{ $detail->riwayat_diabetes_ortu ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Demografi Anak -->
               <div class="card mb-0">
                  <div class="card-header" id="headingDemografiAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseDemografiAnak" aria-expanded="false" aria-controls="collapseDemografiAnak">
                           Demografi Anak
                        </button>
                     </h2>
                  </div>
                  <div id="collapseDemografiAnak" class="collapse" aria-labelledby="headingDemografiAnak" data-parent="#accordionAnakDibawah6">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah anak memiliki disabilitas?</th>
                              <td>{{ $detail->status_disabilitas_anak ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Perkembangan (3-6 Tahun) -->
               <div class="card mb-0">
                  <div class="card-header" id="headingPerkembangan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapsePerkembangan" aria-expanded="false" aria-controls="collapsePerkembangan">
                           Perkembangan (3-6 Tahun)
                        </button>
                     </h2>
                  </div>
                  <div id="collapsePerkembangan" class="collapse" aria-labelledby="headingPerkembangan" data-parent="#accordionAnakDibawah6">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah anak mengalami gangguan emosi?</th>
                              <td>{{ $detail->gangguan_emosi ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah anak menunjukkan perilaku hiperaktif?</th>
                              <td>{{ $detail->hiperaktif ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Talasemia -->
               <div class="card mb-0">
                  <div class="card-header" id="headingTalasemia">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseTalasemia" aria-expanded="false" aria-controls="collapseTalasemia">
                           Talasemia
                        </button>
                     </h2>
                  </div>
                  <div id="collapseTalasemia" class="collapse" aria-labelledby="headingTalasemia" data-parent="#accordionAnakDibawah6">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah ada riwayat talasemia dalam keluarga?</th>
                              <td>{{ $detail->riwayat_keluarga ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah anak adalah pembawa sifat talasemia?</th>
                              <td>{{ $detail->pembawa_sifat ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Tuberkulosis Bayi & Anak Pra Sekolah -->
               <div class="card mb-0">
                  <div class="card-header" id="headingTBCAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseTBCAnak" aria-expanded="false" aria-controls="collapseTBCAnak">
                           Tuberkulosis Bayi & Anak Pra Sekolah
                        </button>
                     </h2>
                  </div>
                  <div id="collapseTBCAnak" class="collapse" aria-labelledby="headingTBCAnak" data-parent="#accordionAnakDibawah6">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah anak mengalami batuk lama?</th>
                              <td>{{ $detail->batuk_lama ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah berat badan anak turun?</th>
                              <td>{{ $detail->berat_turun_tbc ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah berat badan anak tidak naik?</th>
                              <td>{{ $detail->berat_tidak_naik ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah nafsu makan anak berkurang?</th>
                              <td>{{ $detail->nafsu_makan_berkurang ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Apakah anak pernah kontak dengan penderita TBC?</th>
                              <td>{{ $detail->kontak_tbc ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endif

   <!-- Pemeriksaan Mandiri - Hanya untuk usia 19 tahun ke atas -->
   @if(isset($detail->umur) && $detail->umur >= 19)
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-info">
            <h5 class="card-title"><i class="fas fa-clipboard-check mr-2"></i> Pemeriksaan Mandiri</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionPemeriksaan">
               <!-- Demografi Dewasa Perempuan -->
               <div class="card mb-0">
                  <div class="card-header" id="headingDemografi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#collapseDemografi" aria-expanded="true" aria-controls="collapseDemografi">
                           Data Demografi Dewasa
                        </button>
                     </h2>
                  </div>
                  <div id="collapseDemografi" class="collapse show" aria-labelledby="headingDemografi"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Status Perkawinan</th>
                              <td>{{ $detail->status_perkawinan ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apabila belum menikah/status cerai, apakah ada rencana menikah dalam kurun waktu 1
                                 tahun ke depan?</th>
                              <td>{{ $detail->rencana_menikah ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda sedang hamil?</th>
                              <td>{{ $detail->status_hamil ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah Anda penyandang disabilitas?</th>
                              <td>{{ $detail->status_disabilitas ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Hati -->
               <div class="card mb-0">
                  <div class="card-header" id="headingHati">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseHati" aria-expanded="false" aria-controls="collapseHati">
                           Hati
                        </button>
                     </h2>
                  </div>
                  <div id="collapseHati" class="collapse" aria-labelledby="headingHati"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda pernah menjalani tes untuk Hepatitis B dan mendapatkan
                                 hasil positif?</th>
                              <td>{{ $detail->riwayat_hepatitis ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda memiliki ibu kandung/saudara sekandung yang menderita Hepatitis B?</th>
                              <td>{{ $detail->riwayat_kuning ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda pernah melakukan hubungan intim / seksual dengan orang yang bukan
                                 pasangan resmi Anda?</th>
                              <td>{{ $detail->hubungan_intim ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah Anda pernah menerima transfusi darah sebelumnya?</th>
                              <td>{{ $detail->riwayat_transfusi ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Apakah Anda pernah menjalani cuci darah atau hemodialisis?</th>
                              <td>{{ $detail->riwayat_tindik ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>6. Apakah Anda pernah menggunakan narkoba, obat terlarang atau bahan adiktif lainnya
                                 dengan cara disuntik?</th>
                              <td>{{ $detail->narkoba_suntik ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>7. Apakah Anda adalah orang dengan HIV (ODHIV)?</th>
                              <td>{{ $detail->odhiv ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>8. Apakah Anda pernah mendapatkan pengobatan Hepatitis C dan tidak sembuh?</th>
                              <td>{{ $detail->riwayat_tattoo ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>9. Apakah Anda pernah didiagnosa atau mendapatkan hasil pemeriksaan kolesterol (lemak
                                 darah) tinggi?</th>
                              <td>{{ $detail->kolesterol ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Kanker Leher Rahim -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKanker">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseKanker" aria-expanded="false" aria-controls="collapseKanker">
                           Kanker Leher Rahim
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKanker" class="collapse" aria-labelledby="headingKanker"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah pernah melakukan hubungan intim/seksual?</th>
                              <td>{{ $detail->hubungan_intim ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Kesehatan Jiwa -->
               <div class="card mb-0">
                  <div class="card-header" id="headingJiwa">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseJiwa" aria-expanded="false" aria-controls="collapseJiwa">
                           Kesehatan Jiwa
                        </button>
                     </h2>
                  </div>
                  <div id="collapseJiwa" class="collapse" aria-labelledby="headingJiwa"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Pernahkah dalam 2 minggu terakhir, Anda merasa tidak memiliki minat
                                 atau kesenangan dalam melakukan sesuatu hal?</th>
                              <td>{{ $detail->minat ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Pernahkah dalam 2 minggu terakhir, Anda merasa murung, sedih, atau putus asa?</th>
                              <td>{{ $detail->sedih ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Dalam 2 minggu terakhir, seberapa sering anda merasa gugup, cemas, atau gelisah?
                              </th>
                              <td>{{ $detail->cemas ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Dalam 2 minggu terakhir, seberapa sering anda tidak mampu mengendalikan rasa
                                 khawatir?</th>
                              <td>{{ $detail->khawatir ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Perilaku Merokok -->
               <div class="card mb-0">
                  <div class="card-header" id="headingMerokok">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseMerokok" aria-expanded="false" aria-controls="collapseMerokok">
                           Perilaku Merokok
                        </button>
                     </h2>
                  </div>
                  <div id="collapseMerokok" class="collapse" aria-labelledby="headingMerokok"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda merokok dalam setahun terakhir ini?</th>
                              <td>{{ $detail->status_merokok ?? '-' }}</td>
                           </tr>
                           @if($detail->status_merokok == 'Ya')
                           <tr>
                              <th>2. Sudah berapa tahun Anda merokok?</th>
                              <td>{{ $detail->lama_merokok ? $detail->lama_merokok . ' tahun' : '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Biasanya, berapa batang rokok yang Anda hisap dalam sehari?</th>
                              <td>{{ $detail->jumlah_rokok ? $detail->jumlah_rokok . ' batang/hari' : '-' }}</td>
                           </tr>
                           @endif
                           <tr>
                              <th>{{ $detail->status_merokok == 'Ya' ? '4' : '2' }}. Apakah Anda terpapar asap rokok
                                 atau menghirup asap rokok dari orang lain dalam sebulan terakhir?</th>
                              <td>{{ $detail->paparan_asap ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Tekanan Darah & Gula Darah -->
               <div class="card mb-0">
                  <div class="card-header" id="headingTekanan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseTekanan" aria-expanded="false" aria-controls="collapseTekanan">
                           Tekanan Darah & Gula Darah
                        </button>
                     </h2>
                  </div>
                  <div id="collapseTekanan" class="collapse" aria-labelledby="headingTekanan"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda pernah didiagnosis hipertensi (tekanan darah tinggi) oleh
                                 dokter atau tenaga kesehatan lainnya?</th>
                              <td>{{ $detail->riwayat_hipertensi ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda pernah didiagnosis diabetes (kencing manis) oleh dokter atau tenaga
                                 kesehatan lainnya?</th>
                              <td>{{ $detail->riwayat_diabetes ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda pernah didiagnosis kolesterol tinggi oleh dokter atau tenaga kesehatan
                                 lainnya?</th>
                              <td>{{ $detail->kolesterol ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Tingkat Aktivitas Fisik -->
               <div class="card mb-0">
                  <div class="card-header" id="headingAktivitas">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseAktivitas" aria-expanded="false" aria-controls="collapseAktivitas">
                           Tingkat Aktivitas Fisik
                        </button>
                     </h2>
                  </div>
                  <div id="collapseAktivitas" class="collapse" aria-labelledby="headingAktivitas"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Berapa kali dalam seminggu Anda berolahraga?</th>
                              <td>{{ $detail->frekuensi_olahraga ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Berapa lama durasi Anda berolahraga dalam sekali sesi?</th>
                              <td>{{ $detail->durasi_olahraga ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Tuberkulosis -->
               <div class="card mb-0">
                  <div class="card-header" id="headingTB">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseTB" aria-expanded="false" aria-controls="collapseTB">
                           Tuberkulosis
                        </button>
                     </h2>
                  </div>
                  <div id="collapseTB" class="collapse" aria-labelledby="headingTB" data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah batuk berdahak ≥ 2 minggu berturut-turut?</th>
                              <td>{{ $detail->batuk ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah demam tinggi ≥ 2 minggu berturut-turut?</th>
                              <td>{{ $detail->demam ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>


            </div>
         </div>
      </div>
   </div>
   @endif

   <!-- Pelayanan Medis - Hanya untuk anak usia dibawah 6 tahun -->
   @if(isset($detail->umur) && $detail->umur < 6)
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-primary">
            <h5 class="card-title"><i class="fas fa-user-md mr-2"></i> Pelayanan Medis</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionPelayananMedis">
               <!-- Skrining Pertumbuhan - Balita dan Anak Prasekolah -->
               <div class="card mb-0">
                  <div class="card-header" id="headingSkriningPertumbuhan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#collapseSkriningPertumbuhan" aria-expanded="true"
                           aria-controls="collapseSkriningPertumbuhan">
                           Skrining Pertumbuhan - Balita dan Anak Prasekolah
                        </button>
                     </h2>
                  </div>
                  <div id="collapseSkriningPertumbuhan" class="collapse show" aria-labelledby="headingSkriningPertumbuhan"
                     data-parent="#accordionPelayananMedis">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="50%">Berat Badan Balita</th>
                                    <td>{{ $detail->berat_badan_balita ? $detail->berat_badan_balita . ' kg' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tinggi Badan Balita</th>
                                    <td>{{ $detail->tinggi_badan_balita ? $detail->tinggi_badan_balita . ' cm' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Status Gizi BB/U</th>
                                    <td>{{ $detail->status_gizi_bb_u ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Status Gizi PB/U</th>
                                    <td>{{ $detail->status_gizi_pb_u ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="50%">Status Gizi BB/PB</th>
                                    <td>{{ $detail->status_gizi_bb_pb ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Hasil IMT/U</th>
                                    <td>{{ $detail->hasil_imt_u ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Status Lingkar Kepala</th>
                                    <td>{{ $detail->status_lingkar_kepala ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Hasil Pemeriksaan KPSP -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKPSP">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseKPSP" aria-expanded="false" aria-controls="collapseKPSP">
                           Hasil Pemeriksaan KPSP
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKPSP" class="collapse" aria-labelledby="headingKPSP"
                     data-parent="#accordionPelayananMedis">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="50%">Hasil KPSP</th>
                              <td>{{ $detail->hasil_kpsp ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Skrining Telinga dan Mata -->
               <div class="card mb-0">
                  <div class="card-header" id="headingSkriningTelingaMata">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseSkriningTelingaMata" aria-expanded="false" aria-controls="collapseSkriningTelingaMata">
                           Skrining Telinga dan Mata
                        </button>
                     </h2>
                  </div>
                  <div id="collapseSkriningTelingaMata" class="collapse" aria-labelledby="headingSkriningTelingaMata"
                     data-parent="#accordionPelayananMedis">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="50%">Hasil Tes Dengar</th>
                              <td>{{ $detail->hasil_tes_dengar ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>Hasil Tes Lihat</th>
                              <td>{{ $detail->hasil_tes_lihat ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Skrining Gigi -->
               <div class="card mb-0">
                  <div class="card-header" id="headingSkriningGigiMedis">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseSkriningGigiMedis" aria-expanded="false" aria-controls="collapseSkriningGigiMedis">
                           Skrining Gigi
                        </button>
                     </h2>
                  </div>
                  <div id="collapseSkriningGigiMedis" class="collapse" aria-labelledby="headingSkriningGigiMedis"
                     data-parent="#accordionPelayananMedis">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="50%">Karies</th>
                              <td>{{ $detail->karies ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>Hilang</th>
                              <td>{{ $detail->hilang ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>Goyang</th>
                              <td>{{ $detail->goyang ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endif

   <!-- Assesment Mandiri - Hanya untuk usia 19 tahun ke atas -->
   @if(isset($detail->umur) && $detail->umur >= 19)
   <div class="col-md-12">
      <div class="card">
         <div class="card-header bg-success">
            <h5 class="card-title"><i class="fas fa-stethoscope mr-2"></i> Assesment Mandiri</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionAssesment">
               <!-- Antropometri dan Laboratorium -->
               <div class="card mb-0">
                  <div class="card-header" id="headingAntropometri">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#collapseAntropometri" aria-expanded="true"
                           aria-controls="collapseAntropometri">
                           Antropometri dan Laboratorium
                        </button>
                     </h2>
                  </div>
                  <div id="collapseAntropometri" class="collapse show" aria-labelledby="headingAntropometri"
                     data-parent="#accordionAssesment">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="40%">Tinggi Badan</th>
                                    <td>{{ $detail->tinggi_badan ? $detail->tinggi_badan . ' cm' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Berat Badan</th>
                                    <td>{{ $detail->berat_badan ? $detail->berat_badan . ' kg' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Lingkar Perut</th>
                                    <td>{{ $detail->lingkar_perut ? $detail->lingkar_perut . ' cm' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tekanan Sistolik</th>
                                    <td>{{ $detail->tekanan_sistolik ? $detail->tekanan_sistolik . ' mmHg' : '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="40%">Tekanan Diastolik</th>
                                    <td>{{ $detail->tekanan_diastolik ? $detail->tekanan_diastolik . ' mmHg' : '-' }}
                                    </td>
                                 </tr>
                                 <tr>
                                    <th>GDS</th>
                                    <td>{{ $detail->gds ? $detail->gds . ' mg/dL' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>GDP</th>
                                    <td>{{ $detail->gdp ? $detail->gdp . ' mg/dL' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Kolesterol</th>
                                    <td>{{ $detail->kolesterol_lab ? $detail->kolesterol_lab . ' mg/dL' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Trigliserida</th>
                                    <td>{{ $detail->trigliserida ? $detail->trigliserida . ' mg/dL' : '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Skrining PUMA -->
               <div class="card mb-0">
                  <div class="card-header" id="headingPUMA">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapsePUMA" aria-expanded="false" aria-controls="collapsePUMA">
                           Skrining PUMA
                        </button>
                     </h2>
                  </div>
                  <div id="collapsePUMA" class="collapse" aria-labelledby="headingPUMA"
                     data-parent="#accordionAssesment">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah anda sedang/mempunyai riwayat merokok?</th>
                              <td>{{ $detail->riwayat_merokok ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda pernah merasa napas pendek ketika berjalan lebih cepat pada jalan yang
                                 datar atau pada jalan yang sedikit menanjak?</th>
                              <td>{{ $detail->napas_pendek ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda biasanya mempunyai dahak yang berasal dari paru atau kesulitan
                                 mengeluarkan dahak saat Anda sedang tidak menderita selesma/flu?</th>
                              <td>{{ $detail->dahak ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah Anda biasanya batuk saat sedang tidak menderita selesma/flu?</th>
                              <td>{{ $detail->batuk ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Apakah Dokter atau tenaga medis lainnya pernah meminta Anda untuk melakukan
                                 pemeriksaan spirometri atau peak flow meter (meniup ke dalam suatu alat) untuk
                                 mengetahui fungsi paru?</th>
                              <td>{{ $detail->spirometri ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Skrining Indra -->
               <div class="card mb-0">
                  <div class="card-header" id="headingIndra">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseIndra" aria-expanded="false" aria-controls="collapseIndra">
                           Skrining Indra
                        </button>
                     </h2>
                  </div>
                  <div id="collapseIndra" class="collapse" aria-labelledby="headingIndra"
                     data-parent="#accordionAssesment">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda memiliki kesulitan dalam mendengar suara?</th>
                              <td>{{ $detail->pendengaran ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda memiliki kesulitan dalam melihat objek?</th>
                              <td>{{ $detail->penglihatan ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Skrining Gigi -->
               <div class="card mb-0">
                  <div class="card-header" id="headingGigi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseGigi" aria-expanded="false" aria-controls="collapseGigi">
                           Skrining Gigi
                        </button>
                     </h2>
                  </div>
                  <div id="collapseGigi" class="collapse" aria-labelledby="headingGigi"
                     data-parent="#accordionAssesment">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda memiliki gigi yang berlubang (karies)?</th>
                              <td>{{ $detail->karies ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda memiliki gigi yang hilang?</th>
                              <td>{{ $detail->hilang ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda memiliki gigi yang goyang?</th>
                              <td>{{ $detail->goyang ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Pemeriksaan Gangguan Fungsional/Barthel Index - Hanya untuk Lansia (>60 tahun) -->
               @if(isset($detail->umur) && $detail->umur > 60)
               <div class="card mb-0">
                  <div class="card-header" id="headingBarthelIndex">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseBarthelIndex" aria-expanded="false" aria-controls="collapseBarthelIndex">
                           Pemeriksaan Gangguan Fungsional/Barthel Index
                        </button>
                     </h2>
                  </div>
                  <div id="collapseBarthelIndex" class="collapse" aria-labelledby="headingBarthelIndex"
                     data-parent="#accordionAssesment">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. BAB (Buang Air Besar)</th>
                              <td>{{ $detail->bab ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. BAK (Buang Air Kecil)</th>
                              <td>{{ $detail->bak ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Membersihkan Diri</th>
                              <td>{{ $detail->membersihkan_diri ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Penggunaan Jamban</th>
                              <td>{{ $detail->penggunaan_jamban ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Makan/Minum</th>
                              <td>{{ $detail->makan_minum ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>6. Berubah Sikap</th>
                              <td>{{ $detail->berubah_sikap ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>7. Berpindah</th>
                              <td>{{ $detail->berpindah ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>8. Memakai Baju</th>
                              <td>{{ $detail->memakai_baju ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>9. Naik Tangga</th>
                              <td>{{ $detail->naik_tangga ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>10. Mandi</th>
                              <td>{{ $detail->mandi ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
               @endif

            </div>
         </div>
      </div>
   </div>
   @endif

   <!-- Keluhan Lain - Untuk semua umur -->
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-warning">
            <h5 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i> Keluhan Lain</h5>
         </div>
         <div class="card-body">
            <table class="table table-bordered">
               <tr>
                  <th width="70%">Keluhan Lain</th>
                  <td>{{ $detail->keluhan_lain ?? '-' }}</td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi daftarKunjunganSehat sudah dipindahkan ke file utama pendaftaran_ckg.blade.php
    
    // Event handler sudah dipindahkan ke file utama pendaftaran_ckg.blade.php
    // untuk menghindari konflik dan duplikasi
});
</script>