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
                                    <option value="{{ $pegawai->nik }}" {{ (isset($detail->id_petugas_entri) && $detail->id_petugas_entri != '' ? $detail->id_petugas_entri == $pegawai->nik : session('username') == $pegawai->nik) ? 'selected' : '' }}>
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
            <div class="accordion" id="detail_accordionAnakDibawah6">
               <!-- Gejala DM Anak -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingDMAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseDMAnak" aria-expanded="false" aria-controls="detail_collapseDMAnak">
                           Gejala DM Anak
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseDMAnak" class="collapse" aria-labelledby="detail_headingDMAnak" data-parent="#detail_accordionAnakDibawah6">
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
                  <div class="card-header" id="detail_headingDemografiAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseDemografiAnak" aria-expanded="false" aria-controls="detail_collapseDemografiAnak">
                           Demografi Anak
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseDemografiAnak" class="collapse" aria-labelledby="detail_headingDemografiAnak" data-parent="#detail_accordionAnakDibawah6">
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
                  <div class="card-header" id="detail_headingPerkembangan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapsePerkembangan" aria-expanded="false" aria-controls="detail_collapsePerkembangan">
                           Perkembangan (3-6 Tahun)
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapsePerkembangan" class="collapse" aria-labelledby="detail_headingPerkembangan" data-parent="#detail_accordionAnakDibawah6">
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
                  <div class="card-header" id="detail_headingTalasemia">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTalasemia" aria-expanded="false" aria-controls="detail_collapseTalasemia">
                           Talasemia
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTalasemia" class="collapse" aria-labelledby="detail_headingTalasemia" data-parent="#detail_accordionAnakDibawah6">
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
                  <div class="card-header" id="detail_headingTBCAnak">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTBCAnak" aria-expanded="false" aria-controls="detail_collapseTBCAnak">
                           Tuberkulosis Bayi & Anak Pra Sekolah
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTBCAnak" class="collapse" aria-labelledby="detail_headingTBCAnak" data-parent="#detail_accordionAnakDibawah6">
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
            <div class="accordion" id="detail_accordionPemeriksaan">
               <!-- Demografi Dewasa Perempuan -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingDemografi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#detail_collapseDemografi" aria-expanded="true" aria-controls="detail_collapseDemografi">
                           Data Demografi Dewasa
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseDemografi" class="collapse show" aria-labelledby="detail_headingDemografi"
                     data-parent="#detail_accordionPemeriksaan">
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

               <!-- Faktor Resiko Kanker Usus -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingKankerUsus">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseKankerUsus" aria-expanded="false" aria-controls="detail_collapseKankerUsus">
                           Faktor Resiko Kanker Usus
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseKankerUsus" class="collapse" aria-labelledby="detail_headingKankerUsus"
                     data-parent="#detail_accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah ada anggota keluarga Anda, yang pernah dinyatakan menderita kanker kolorektal atau kanker usus?</th>
                              <td>{{ $detail->kanker_usus_1 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. apakah Anda merokok?</th>
                              <td>{{ $detail->kanker_usus_2 ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Faktor Resiko TB - Dewasa&Lansia -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingTBDewasa">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTBDewasa" aria-expanded="false" aria-controls="detail_collapseTBDewasa">
                           Faktor Resiko TB - Dewasa&Lansia
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTBDewasa" class="collapse" aria-labelledby="detail_headingTBDewasa"
                     data-parent="#detail_accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda pernah atau sedang mengalami batuk yang tidak sembuh-sembuh?</th>
                              <td>{{ $detail->faktor_resiko_tb ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Hati -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingHati">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseHati" aria-expanded="false" aria-controls="detail_collapseHati">
                           Hati
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseHati" class="collapse" aria-labelledby="detail_headingHati"
                     data-parent="#detail_accordionPemeriksaan">
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
                  <div class="card-header" id="detail_headingKanker">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseKanker" aria-expanded="false" aria-controls="detail_collapseKanker">
                           Kanker Leher Rahim
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseKanker" class="collapse" aria-labelledby="detail_headingKanker"
                     data-parent="#detail_accordionPemeriksaan">
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
                  <div class="card-header" id="detail_headingJiwa">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseJiwa" aria-expanded="false" aria-controls="detail_collapseJiwa">
                           Kesehatan Jiwa
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseJiwa" class="collapse" aria-labelledby="detail_headingJiwa"
                     data-parent="#detail_accordionPemeriksaan">
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

               <!-- Penapisan Resiko Kanker Paru -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingKankerParu">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseKankerParu" aria-expanded="false" aria-controls="detail_collapseKankerParu">
                           Penapisan Resiko Kanker Paru
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseKankerParu" class="collapse" aria-labelledby="detail_headingKankerParu"
                     data-parent="#detail_accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda merokok dalam setahun terakhir ini?</th>
                              <td>{{ $detail->kanker_paru_1 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah Anda terpapar atau menghirup asap rokok dari orang lain di rumah, lingkungan atau tempat kerja dalam 1 bulan terakhir?</th>
                              <td>{{ $detail->kanker_paru_2 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah Anda sedang mengalami salah satu atau lebih gejala berikut dan telah diobati tetapi tidak sembuh-sembuh : batuk dalam jangka waktu yang lama / batuk berdarah/ sesak napas/ nyeri dada/ leher bengkak/ terdapat benjolan pada leher?</th>
                              <td>{{ $detail->kanker_paru_4 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>4. Apakah Anda pernah memiliki riwayat penyakit TBC atau PPOK?</th>
                              <td>{{ $detail->kanker_paru_3 ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Perilaku Merokok -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingMerokok">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseMerokok" aria-expanded="false" aria-controls="detail_collapseMerokok">
                           Perilaku Merokok
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseMerokok" class="collapse" aria-labelledby="detail_headingMerokok"
                     data-parent="#detail_accordionPemeriksaan">
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
                  <div class="card-header" id="detail_headingTekanan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTekanan" aria-expanded="false" aria-controls="detail_collapseTekanan">
                           Tekanan Darah & Gula Darah
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTekanan" class="collapse" aria-labelledby="detail_headingTekanan"
                     data-parent="#detail_accordionPemeriksaan">
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
                  <div class="card-header" id="detail_headingAktivitas">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseAktivitas" aria-expanded="false" aria-controls="detail_collapseAktivitas">
                           Tingkat Aktivitas Fisik
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseAktivitas" class="collapse" aria-labelledby="detail_headingAktivitas"
                     data-parent="#detail_accordionPemeriksaan">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah Anda melakukan aktivitas fisik sedang pada kegiatan rumah tangga/domestik seperti membersihkan rumah/lingkungan (menyapu, menata perabotan), mencuci baju manual, memasak, mengasuh anak, atau mengangkat beban dengan berat < 20 kg?</th>
                              <td>{{ $detail->frekuensi_olahraga ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->frekuensi_olahraga_1 ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>3. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->frekuensi_olahraga_2 ?? '-' }} menit</td>
                           </tr>
                           <tr>
                              <th>4. Apakah Anda melakukan aktivitas fisik sedang pada tempat kerja seperti pekerjaan dengan mengangkat beban, memberi makan ternak, berkebun dan membersihkan kendaraan (motor/mobil/perahu)?</th>
                              <td>{{ $detail->aktivitas_fisik_2 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>5. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_2_hari ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>6. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_2_menit ?? '-' }} menit</td>
                           </tr>
                           <tr>
                              <th>7. Apakah Anda melakukan aktivitas fisik sedang dalam perjalanan seperti berjalan kaki atau bersepeda ke ladang, sawah, pasar dan tempat kerja?</th>
                              <td>{{ $detail->aktivitas_fisik_3 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>8. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_3_hari ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>9. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_3_menit ?? '-' }} menit</td>
                           </tr>
                           <tr>
                              <th>10. Apakah Anda melakukan olahraga intensitas sedang seperti latihan beban < 20 kg, senam aerobic, yoga, bermain bola, bersepeda dan berenang (santai)?</th>
                              <td>{{ $detail->aktivitas_fisik_4 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>11. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_4_hari ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>12. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_4_menit ?? '-' }} menit</td>
                           </tr>
                           <tr>
                              <th>13. Apakah Anda melakukan aktivitas fisik intensitas berat di tempat kerja seperti mengangkat/memikul beban berat ≥20 kg, mencangkul, menggali, memanen, memanjat pohon, menebang pohon, mengayuh becak, menarik jaring, mendorong atau menarik (mesin pemotong rumput/gerobak/perahu/kendaraan)?</th>
                              <td>{{ $detail->aktivitas_fisik_5 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>14. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_5_hari ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>15. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_5_menit ?? '-' }} menit</td>
                           </tr>
                           <tr>
                              <th>16. Apakah Anda melakukan olahraga intensitas berat seperti bersepeda cepat (>16 km/jam), jalan cepat (>7 km/jam), lari, sepak bola, futsal, bulutangkis, tenis, basket dan lompat tali?</th>
                              <td>{{ $detail->aktivitas_fisik_6 ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>17. Berapa hari dalam satu minggu Anda melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_6_hari ?? '-' }} hari</td>
                           </tr>
                           <tr>
                              <th>18. Dalam satu hari berapa menit waktu yang digunakan untuk melakukan aktivitas tersebut?</th>
                              <td>{{ $detail->aktivitas_fisik_6_menit ?? '-' }} menit</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Tuberkulosis -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingTB">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTB" aria-expanded="false" aria-controls="detail_collapseTB">
                           Tuberkulosis
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTB" class="collapse" aria-labelledby="detail_headingTB" data-parent="#detail_accordionPemeriksaan">
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
            <div class="accordion" id="detail_accordionPelayananMedis">
               <!-- Skrining Pertumbuhan - Balita dan Anak Prasekolah -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingSkriningPertumbuhan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#detail_collapseSkriningPertumbuhan" aria-expanded="true"
                           aria-controls="detail_collapseSkriningPertumbuhan">
                           Skrining Pertumbuhan - Balita dan Anak Prasekolah
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseSkriningPertumbuhan" class="collapse show" aria-labelledby="detail_headingSkriningPertumbuhan"
                     data-parent="#detail_accordionPelayananMedis">
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
                  <div class="card-header" id="detail_headingKPSP">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseKPSP" aria-expanded="false" aria-controls="detail_collapseKPSP">
                           Hasil Pemeriksaan KPSP
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseKPSP" class="collapse" aria-labelledby="detail_headingKPSP"
                     data-parent="#detail_accordionPelayananMedis">
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
                  <div class="card-header" id="detail_headingSkriningTelingaMata">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseSkriningTelingaMata" aria-expanded="false" aria-controls="detail_collapseSkriningTelingaMata">
                           Skrining Telinga dan Mata
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseSkriningTelingaMata" class="collapse" aria-labelledby="detail_headingSkriningTelingaMata"
                     data-parent="#detail_accordionPelayananMedis">
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
                  <div class="card-header" id="detail_headingSkriningGigiMedis">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseSkriningGigiMedis" aria-expanded="false" aria-controls="detail_collapseSkriningGigiMedis">
                           Skrining Gigi
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseSkriningGigiMedis" class="collapse" aria-labelledby="detail_headingSkriningGigiMedis"
                     data-parent="#detail_accordionPelayananMedis">
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
            <div class="accordion" id="detail_accordionAssesment">
               <!-- Antropometri dan Laboratorium -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingAntropometri">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#detail_collapseAntropometri" aria-expanded="true"
                           aria-controls="detail_collapseAntropometri">
                           Antropometri dan Laboratorium
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseAntropometri" class="collapse show" aria-labelledby="detail_headingAntropometri"
                     data-parent="#detail_accordionAssesment">
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
                                 <tr>
                                    <th>Tekanan Sistolik 2</th>
                                    <td>{{ $detail->tekanan_sistolik_2 ? $detail->tekanan_sistolik_2 . ' mmHg' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tekanan Diastolik</th>
                                    <td>{{ $detail->tekanan_diastolik ? $detail->tekanan_diastolik . ' mmHg' : '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tekanan Diastolik 2</th>
                                    <td>{{ $detail->tekanan_diastolik_2 ? $detail->tekanan_diastolik_2 . ' mmHg' : '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                               <table class="table table-bordered">
                                 <tr>
                                    <th width="40%">GDS</th>
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
                                 <tr>
                                    <th>Riwayat DM</th>
                                    <td>{{ $detail->riwayat_dm ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Riwayat HT</th>
                                    <td>{{ $detail->riwayat_ht ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Skrining PUMA -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingPUMA">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapsePUMA" aria-expanded="false" aria-controls="detail_collapsePUMA">
                           Skrining PUMA
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapsePUMA" class="collapse" aria-labelledby="detail_headingPUMA"
                     data-parent="#detail_accordionAssesment">
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

               <!-- Penyakit Tropis -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingTropis">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseTropis" aria-expanded="false" aria-controls="detail_collapseTropis">
                           Penyakit Tropis
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseTropis" class="collapse" aria-labelledby="detail_headingTropis"
                     data-parent="#detail_accordionAssesment">
                     <div class="card-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="70%">1. Apakah ada papul/nodul/ulkus/krusta papiloma?</th>
                              <td>{{ $detail->frambusia ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>2. Apakah tubuh anda ada bercak kulit putih atau merah yang tidak/kurang rasa/ kebal saat disentuh panas/dingin, tidak gatal/ tidak nyeri?</th>
                              <td>{{ $detail->kusta ?? '-' }}</td>
                           </tr>
                           <tr>
                              <th>3. Apakah ada koreng/ruam/bentol/kudis bergerombol yang gatal terutama di malam hari walaupun sudah diberi bedak atau lotion?</th>
                              <td>{{ $detail->skabies ?? '-' }}</td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>

               <!-- Skrining Indra -->
               <div class="card mb-0">
                  <div class="card-header" id="detail_headingIndra">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseIndra" aria-expanded="false" aria-controls="detail_collapseIndra">
                           Skrining Indra
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseIndra" class="collapse" aria-labelledby="detail_headingIndra"
                     data-parent="#detail_accordionAssesment">
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
                  <div class="card-header" id="detail_headingGigi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseGigi" aria-expanded="false" aria-controls="detail_collapseGigi">
                           Skrining Gigi
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseGigi" class="collapse" aria-labelledby="detail_headingGigi"
                     data-parent="#detail_accordionAssesment">
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
                  <div class="card-header" id="detail_headingBarthelIndex">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#detail_collapseBarthelIndex" aria-expanded="false" aria-controls="detail_collapseBarthelIndex">
                           Pemeriksaan Gangguan Fungsional/Barthel Index
                        </button>
                     </h2>
                  </div>
                  <div id="detail_collapseBarthelIndex" class="collapse" aria-labelledby="detail_headingBarthelIndex"
                     data-parent="#detail_accordionAssesment">
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

<style>
/* Improved accordion styles - better visibility control */
#detail_accordionAnakDibawah6 .collapse,
#detail_accordionPemeriksaan .collapse,
#detail_accordionPelayananMedis .collapse,
#detail_accordionAssesment .collapse {
    display: none;
    height: auto;
    transition: none;
    overflow: visible;
}

#detail_accordionAnakDibawah6 .collapse.show,
#detail_accordionPemeriksaan .collapse.show,
#detail_accordionPelayananMedis .collapse.show,
#detail_accordionAssesment .collapse.show {
    display: block !important;
    height: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Ensure table content is visible */
#detail_accordionAnakDibawah6 .collapse.show .table,
#detail_accordionPemeriksaan .collapse.show .table,
#detail_accordionPelayananMedis .collapse.show .table,
#detail_accordionAssesment .collapse.show .table {
    display: table !important;
    width: 100% !important;
}

/* Ensure table rows and cells are visible */
#detail_accordionAnakDibawah6 .collapse.show .table tr,
#detail_accordionPemeriksaan .collapse.show .table tr,
#detail_accordionPelayananMedis .collapse.show .table tr,
#detail_accordionAssesment .collapse.show .table tr {
    display: table-row !important;
}

#detail_accordionAnakDibawah6 .collapse.show .table td,
#detail_accordionAnakDibawah6 .collapse.show .table th,
#detail_accordionPemeriksaan .collapse.show .table td,
#detail_accordionPemeriksaan .collapse.show .table th,
#detail_accordionPelayananMedis .collapse.show .table td,
#detail_accordionPelayananMedis .collapse.show .table th,
#detail_accordionAssesment .collapse.show .table td,
#detail_accordionAssesment .collapse.show .table th {
    display: table-cell !important;
}
</style>

<script>
$(document).ready(function() {
    // Global accordion state management - ensures only one panel is open across all accordions
    var currentOpenPanel = null;
    var currentOpenButton = null;
    
    // Enhanced accordion implementation with proper collapse behavior
    function initializeAccordion(accordionId) {
        var $accordion = $(accordionId);
        if (!$accordion.length) return;
        
        console.log('Initializing accordion:', accordionId);
        
        // Remove any existing event handlers
        $accordion.off('click.customAccordion');
        
        // Disable Bootstrap's collapse behavior by removing data attributes
        $accordion.find('[data-toggle="collapse"]').each(function() {
            $(this).removeAttr('data-toggle').attr('data-custom-toggle', 'collapse');
        });
        
        // Add click handler for accordion buttons
        $accordion.on('click.customAccordion', '[data-custom-toggle="collapse"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $(this);
            var targetId = $button.attr('data-target');
            var $target = $(targetId);
            
            console.log('Button clicked:', targetId);
            
            if (!$target.length) {
                console.log('Target not found:', targetId);
                return;
            }
            
            // Check if this panel is currently open
            var isCurrentlyOpen = $target.hasClass('show');
            
            // Close any currently open panel (global accordion behavior)
            if (currentOpenPanel && currentOpenPanel.length) {
                console.log('Closing previously open panel:', currentOpenPanel.attr('id'));
                currentOpenPanel.removeClass('show').css('display', 'none');
                if (currentOpenButton && currentOpenButton.length) {
                    currentOpenButton.addClass('collapsed').attr('aria-expanded', 'false');
                }
            }
            
            // Reset global state
            currentOpenPanel = null;
            currentOpenButton = null;
            
            // If the clicked panel wasn't open, open it
            if (!isCurrentlyOpen) {
                console.log('Opening panel:', targetId);
                
                // Show this panel
                $target.addClass('show');
                $button.removeClass('collapsed').attr('aria-expanded', 'true');
                
                // Force display of content
                $target.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });
                
                // Ensure table content is visible
                $target.find('.table').css({
                    'display': 'table',
                    'width': '100%'
                });
                
                $target.find('.table tr').css('display', 'table-row');
                $target.find('.table td, .table th').css('display', 'table-cell');
                
                // Update global state
                currentOpenPanel = $target;
                currentOpenButton = $button;
                
                console.log('Panel opened with content:', targetId);
            } else {
                console.log('Panel was open, now closed:', targetId);
            }
        });
        
        // Ensure panels with 'show' class are properly displayed on load
        $accordion.find('.collapse.show').each(function() {
            var $panel = $(this);
            var panelId = $panel.attr('id');
            var $button = $accordion.find('[data-target="#' + panelId + '"]');
            
            // Close any other open panels first
            if (currentOpenPanel && currentOpenPanel.length && currentOpenPanel.attr('id') !== panelId) {
                currentOpenPanel.removeClass('show').css('display', 'none');
                if (currentOpenButton && currentOpenButton.length) {
                    currentOpenButton.addClass('collapsed').attr('aria-expanded', 'false');
                }
            }
            
            // Set button state
            $button.removeClass('collapsed').attr('aria-expanded', 'true');
            
            // Force display of content
            $panel.css({
                'display': 'block',
                'visibility': 'visible',
                'opacity': '1'
            });
            
            // Ensure table content is visible
            $panel.find('.table').css({
                'display': 'table',
                'width': '100%'
            });
            
            $panel.find('.table tr').css('display', 'table-row');
            $panel.find('.table td, .table th').css('display', 'table-cell');
            
            // Update global state
            currentOpenPanel = $panel;
            currentOpenButton = $button;
            
            console.log('Panel already shown with content:', panelId);
        });
    }
    
    // Initialize all accordions
    initializeAccordion('#detail_accordionAnakDibawah6');
    initializeAccordion('#detail_accordionPemeriksaan');
    initializeAccordion('#detail_accordionPelayananMedis');
    initializeAccordion('#detail_accordionAssesment');
});
</script>
