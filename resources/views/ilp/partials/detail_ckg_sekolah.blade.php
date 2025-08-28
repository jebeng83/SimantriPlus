<div class="row">
   <!-- Data Identitas Siswa -->
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-primary">
            <h5 class="card-title"><i class="fas fa-graduation-cap mr-2"></i> Data Identitas Siswa</h5>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <table class="table table-bordered">
                     <tr>
                        <th width="40%">No. Pasien</th>
                        <td>{{ $detail->no_pasien ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>No. KTP</th>
                        <td>{{ $detail->no_ktp ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>No. Peserta BPJS</th>
                        <td id="no-peserta-bpjs-sekolah">{{ $detail->no_peserta ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Nama Siswa</th>
                        <td id="nama-siswa">{{ $detail->nama_siswa ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Tanggal Lahir</th>
                        <td>{{ $detail->tgl_lahir ? date('d-m-Y', strtotime($detail->tgl_lahir)) : '-' }}</td>
                     </tr>
                     <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $detail->jenis_kelamin == 'L' ? 'Laki-laki' : ($detail->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                     </tr>
                     <tr>
                        <th>Jenis Disabilitas</th>
                        <td>{{ $detail->jenis_disabilitas ?? '-' }}</td>
                     </tr>
                     <tr>
                         <th>No. WhatsApp</th>
                         <td>{{ $detail->no_whatsapp ?? '-' }}</td>
                      </tr>
                     <tr>
                        <th>Alamat</th>
                        <td>{{ $detail->alamat_siswa ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kelurahan</th>
                        <td>{{ $detail->kelurahan_siswa ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kecamatan</th>
                        <td>{{ $detail->kecamatan_siswa ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kabupaten</th>
                        <td>{{ $detail->kabupaten_siswa ?? '-' }}</td>
                     </tr>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-bordered">
                     <tr>
                        <th width="40%">Nama Sekolah</th>
                        <td>{{ $detail->nama_sekolah ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Jenis Sekolah</th>
                        <td>{{ $detail->jenis_sekolah ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Kelas</th>
                        <td>{{ $detail->nama_kelas ?? '-' }}</td>
                     </tr>
                     <tr>
                        <th>Tanggal Skrining</th>
                        <td>{{ $detail->tanggal_skrining ? date('d-m-Y', strtotime($detail->tanggal_skrining)) : '-' }}</td>
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
                     <tr>
                        <th>Status</th>
                        <td>
                           <span class="badge {{ $detail->status == '1' ? 'badge-success' : ($detail->status == '2' ? 'badge-info' : 'badge-warning') }}">
                              {{ $detail->status == '1' ? 'Selesai' : ($detail->status == '2' ? 'Sedang Diproses' : 'Menunggu') }}
                           </span>
                        </td>
                     </tr>
                     <tr>
                        <th>Status Skrining</th>
                        <td>
                           <span class="badge 
                              @if($detail->status_skrining == 'Normal') badge-success
                              @elseif($detail->status_skrining == 'Perlu Perhatian') badge-warning
                              @elseif($detail->status_skrining == 'Rujuk') badge-danger
                              @else badge-secondary
                              @endif">
                              {{ $detail->status_skrining ?? 'Belum Diskrining' }}
                           </span>
                        </td>
                     </tr>
                  </table>
                  
                  <!-- Data Identitas Orang Tua Siswa -->
                  <div class="mt-3">
                     <table class="table table-bordered table-sm">
                        <tr>
                           <th width="40%">NIK Orang Tua</th>
                           <td>{{ $detail->nik_ortu ?? '-' }}</td>
                        </tr>
                        <tr>
                           <th>Nama Orang Tua</th>
                           <td>{{ $detail->nama_ortu ?? '-' }}</td>
                        </tr>
                        <tr>
                           <th>Tanggal Lahir</th>
                           <td>{{ $detail->tanggal_lahir_ortu ? date('d-m-Y', strtotime($detail->tanggal_lahir_ortu)) : '-' }}</td>
                        </tr>
                        <tr>
                           <th>Jenis Kelamin</th>
                           <td>{{ $detail->jenis_kelamin_ortu == 'L' ? 'Laki-laki' : ($detail->jenis_kelamin_ortu == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                           <th>Status</th>
                           <td>{{ $detail->status_ortu ?? '-' }}</td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>



   <!-- Data Skrining Siswa SD -->
   @if($detail->tanggal_skrining)
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-info">
            <h5 class="card-title"><i class="fas fa-stethoscope mr-2"></i> Hasil Skrining Kesehatan</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionSkrining">
               
               <!-- Kuesioner Gula Darah (Anak Sekolah) -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKuesionerGulaDarah">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                           data-target="#collapseKuesionerGulaDarah" aria-expanded="true" aria-controls="collapseKuesionerGulaDarah">
                           Kuesioner Gula Darah (Anak Sekolah)
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKuesionerGulaDarah" class="collapse show" aria-labelledby="headingKuesionerGulaDarah"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah anak bapak/ibu sering terbangun pada malam hari untuk buang air kecil atau harus ke toilet lebih dari 2x per malam?</td>
                                    <td class="text-center">
                                       @if($detail->sering_bangun_sd == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->sering_bangun_sd == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah anak bapak/ibu sering merasa haus meskipun sudah banyak minum?</td>
                                    <td class="text-center">
                                       @if($detail->sering_haus_sekolah == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->sering_haus_sekolah == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah anak bapak/ibu sering merasa sangat lapar dan makan lebih banyak dari biasanya?</td>
                                    <td class="text-center">
                                       @if($detail->sering_lapar == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->sering_lapar == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah anak bapak/ibu tetap mengalami penurunan berat badan meskipun nafsu makan meningkat?</td>
                                    <td class="text-center">
                                       @if($detail->berat_turun_sekolah == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->berat_turun_sekolah == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">5</td>
                                    <td>Apakah anak bapak/ibu kembali sering mengompol di malam hari, meskipun sebelumnya sudah bisa mengontrol buang air kecil?</td>
                                    <td class="text-center">
                                       @if($detail->sering_ngompol_sekolah == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->sering_ngompol_sekolah == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">6</td>
                                    <td>Apakah bapak/ibu atau anggota keluarga lainnya (saudara kandung) yang pernah di diagnosis Kencing Manis oleh Dokter?</td>
                                    <td class="text-center">
                                       @if($detail->riwayat_dm_sd == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->riwayat_dm_sd == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Gejala Cemas -->
               <div class="card mb-0">
                  <div class="card-header" id="headingGejalaCemas">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseGejalaCemas" aria-expanded="false" aria-controls="collapseGejalaCemas">
                           Gejala Cemas
                        </button>
                     </h2>
                  </div>
                  <div id="collapseGejalaCemas" class="collapse" aria-labelledby="headingGejalaCemas"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Dalam 2 minggu terakhir, anak sering merasa khawatir atau tidak tenang, tegang, deg-degan dan gelisah terutama terhadap hal-hal negatif atau yang belum tentu terjadi.</td>
                                    <td class="text-center">
                                       @if($detail->gejala_cemas_khawatir == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->gejala_cemas_khawatir == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Dalam 2 minggu terakhir, anak berpikir berlebihan dan tidak bisa mengendalikan diri, terutama terhadap hal-hal negatif atau yang belum tentu terjadi.</td>
                                    <td class="text-center">
                                       @if($detail->gejala_cemas_berfikir_lebih == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->gejala_cemas_berfikir_lebih == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Dalam 2 minggu terakhir, anak sulit tidur dan berkonsentrasi terutama saat memikirkan hal-hal negatif yang belum tentu terjadi.</td>
                                    <td class="text-center">
                                       @if($detail->gejala_cemas_sulit_konsentrasi == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->gejala_cemas_sulit_konsentrasi == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Gejala Depresi -->
               <div class="card mb-0">
                  <div class="card-header" id="headingGejalaDepresi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseGejalaDepresi" aria-expanded="false" aria-controls="collapseGejalaDepresi">
                           Gejala Depresi
                        </button>
                     </h2>
                  </div>
                  <div id="collapseGejalaDepresi" class="collapse" aria-labelledby="headingGejalaDepresi"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Dalam 2 minggu terakhir, anak sering merasa sedih atau tertekan padahal tidak ada penyebab yang jelas.</td>
                                    <td class="text-center">
                                       @if($detail->depresi_anak_sedih == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->depresi_anak_sedih == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Dalam 2 minggu terakhir, anak tidak tertarik lagi dengan kegiatan atau hal-hal yang biasanya dia suka.</td>
                                    <td class="text-center">
                                       @if($detail->depresi_anak_tidaksuka == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->depresi_anak_tidaksuka == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Dalam 2 minggu terakhir, anak merasa sering capek, sulit tidur, dan sulit fokus saat belajar atau melakukan kegiatan.</td>
                                    <td class="text-center">
                                       @if($detail->depresi_anak_capek == 'Ya')
                                          <span class="badge badge-warning">Ya</span>
                                       @elseif($detail->depresi_anak_capek == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Kesehatan Reproduksi Putri -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKesehatanReproduksiPutri">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseKesehatanReproduksiPutri" aria-expanded="false" aria-controls="collapseKesehatanReproduksiPutri">
                           Kesehatan Reproduksi Putri
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKesehatanReproduksiPutri" class="collapse" aria-labelledby="headingKesehatanReproduksiPutri"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah sudah mengalami menstruasi?</td>
                                    <td class="text-center">
                                       @if($detail->menstruasi == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->menstruasi == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Pada usia berapa anda mengalami menstruasi pertama?</td>
                                    <td class="text-center">
                                       @if($detail->haid_pertama)
                                          <span class="badge badge-info">{{ $detail->haid_pertama }} tahun</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah pernah mengalami keputihan?</td>
                                    <td class="text-center">
                                       @if($detail->keputihan == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->keputihan == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah pernah mengalami gatal-gatal di kemaluan?</td>
                                    <td class="text-center">
                                       @if($detail->gatal_kemaluan_puteri == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->gatal_kemaluan_puteri == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Kesehatan Reproduksi Putra -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKesehatanReproduksiPutra">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseKesehatanReproduksiPutra" aria-expanded="false" aria-controls="collapseKesehatanReproduksiPutra">
                           Kesehatan Reproduksi Putra
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKesehatanReproduksiPutra" class="collapse" aria-labelledby="headingKesehatanReproduksiPutra"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah mengalami gatal-gatal di area kemaluan (alat kelamin) atau pernah kencing bernanah/kuning kental seperti susu/nanah?</td>
                                    <td class="text-center">
                                       @if($detail->gatal_kemaluan_putra == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->gatal_kemaluan_putra == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah mengalami nyeri/tidak nyaman saat Buang Air Kecil (BAK) atau Buang Air Besar (BAB)?</td>
                                    <td class="text-center">
                                       @if($detail->nyeri_bak_bab == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->nyeri_bak_bab == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah mengalami luka di anus atau dubur?</td>
                                    <td class="text-center">
                                       @if($detail->luka_penis_dubur == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->luka_penis_dubur == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Faktor Risiko Malaria -->
               <div class="card mb-0">
                  <div class="card-header" id="headingFaktorRisikoMalaria">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseFaktorRisikoMalaria" aria-expanded="false" aria-controls="collapseFaktorRisikoMalaria">
                           Faktor Risiko Malaria
                        </button>
                     </h2>
                  </div>
                  <div id="collapseFaktorRisikoMalaria" class="collapse" aria-labelledby="headingFaktorRisikoMalaria"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah terdapat salah satu atau lebih gejala seperti: demam, sakit kepala, dan menggigil?</td>
                                    <td class="text-center">
                                       @if($detail->malaria_gejala == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->malaria_gejala == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah pernah sakit malaria dan obat tidak habis diminum?</td>
                                    <td class="text-center">
                                       @if($detail->malaria_sakit == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->malaria_sakit == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah ada orang sakit malaria di wilayah tempat tinggal (di rumah atau tetangga sekitar rumah)?</td>
                                    <td class="text-center">
                                       @if($detail->malaria_tempat == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->malaria_tempat == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Tingkat Aktivitas Fisik -->
               <div class="card mb-0">
                  <div class="card-header" id="headingTingkatAktivitasFisik">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseTingkatAktivitasFisik" aria-expanded="false" aria-controls="collapseTingkatAktivitasFisik">
                           Tingkat Aktivitas Fisik
                        </button>
                     </h2>
                  </div>
                  <div id="collapseTingkatAktivitasFisik" class="collapse" aria-labelledby="headingTingkatAktivitasFisik"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Dalam seminggu terakhir, berapa kali Anda melakukan aktivitas fisik/olahraga dalam seminggu?</td>
                                    <td class="text-center">
                                       @if($detail->aktivitas_fisik_jumlah == '0 kali')
                                          <span class="badge badge-secondary">0 kali</span>
                                       @elseif($detail->aktivitas_fisik_jumlah == '1-2 kali')
                                          <span class="badge badge-warning">1-2 kali</span>
                                       @elseif($detail->aktivitas_fisik_jumlah == '3-4 kali')
                                          <span class="badge badge-info">3-4 kali</span>
                                       @elseif($detail->aktivitas_fisik_jumlah == '5 kali atau lebih')
                                          <span class="badge badge-success">5 kali atau lebih</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Rata-rata berapa lama waktu yang Anda habiskan ketika melakukan aktivitas fisik/olahraga?</td>
                                    <td class="text-center">
                                       @if($detail->aktifitas_fisik_waktu == 'Kurang dari 30 menit')
                                          <span class="badge badge-warning">Kurang dari 30 menit</span>
                                       @elseif($detail->aktifitas_fisik_waktu == '30-60 menit')
                                          <span class="badge badge-info">30-60 menit</span>
                                       @elseif($detail->aktifitas_fisik_waktu == 'Lebih dari 60 menit')
                                          <span class="badge badge-success">Lebih dari 60 menit</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Kelayakan Tes Kebugaran -->
               <div class="card mb-0">
                  <div class="card-header" id="headingKelayakanTesKebugaran">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseKelayakanTesKebugaran" aria-expanded="false" aria-controls="collapseKelayakanTesKebugaran">
                           Kelayakan Tes Kebugaran
                        </button>
                     </h2>
                  </div>
                  <div id="collapseKelayakanTesKebugaran" class="collapse" aria-labelledby="headingKelayakanTesKebugaran"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="alert alert-info" role="alert">
                           <i class="fas fa-info-circle"></i> <strong>Petunjuk:</strong> Jawab pertanyaan berikut sesuai dengan kondisi siswa/siswi terkait skrining kebugaran (Kelas 4-6).
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah dokter pernah menyatakan bahwa anak anda memiliki masalah pada tulang dan sendi seperti radang sendi, dan hanya bisa melakukan aktivitas fisik sesuai anjuran dokter?</td>
                                    <td class="text-center">
                                       @if($detail->kebugaran_tulang == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->kebugaran_tulang == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah dokter pernah menyatakan bahwa anak anda memiliki masalah pada jantung dan bahwa anda hanya bisa melakukan aktivitas fisik sesuai anjuran dokter?</td>
                                    <td class="text-center">
                                       @if($detail->kebugaran_jantung == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->kebugaran_jantung == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah anak anda menderita asma atau pernah terserang asma saat melakukan latihan fisik?</td>
                                    <td class="text-center">
                                       @if($detail->kebugaran_asma == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->kebugaran_asma == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah anak anda pernah kehilangan kesadaran, sakit kepala atau pingsan karena aktivitas berat dalam 1 bulan terakhir?</td>
                                    <td class="text-center">
                                       @if($detail->kebugaran_pingsan == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->kebugaran_pingsan == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Penyakit Tropis Terabaikan -->
               <div class="card mb-0">
                  <div class="card-header" id="headingPenyakitTropisTerabaikan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapsePenyakitTropisTerabaikan" aria-expanded="false" aria-controls="collapsePenyakitTropisTerabaikan">
                           Penyakit Tropis Terabaikan
                        </button>
                     </h2>
                  </div>
                  <div id="collapsePenyakitTropisTerabaikan" class="collapse" aria-labelledby="headingPenyakitTropisTerabaikan"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah kulit Anda terdapat bercak putih atau kemerahan yang tidak gatal, mati rasa, tidak sakit dan tidak sembuh dengan obat kulit biasa?</td>
                                    <td class="text-center">
                                       @if($detail->tropis_bercak == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->tropis_bercak == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah kulit Anda ada koreng atau bentol/kudis yang gatal terutama di malam hari walaupun dikasih bedak/lotion?</td>
                                    <td class="text-center">
                                       @if($detail->tropis_koreng == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->tropis_koreng == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Perilaku Merokok - Anak Sekolah -->
               <div class="card mb-0">
                  <div class="card-header" id="headingPerilakuMerokok">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapsePerilakuMerokok" aria-expanded="false" aria-controls="collapsePerilakuMerokok">
                           Perilaku Merokok - Anak Sekolah
                        </button>
                     </h2>
                  </div>
                  <div id="collapsePerilakuMerokok" class="collapse" aria-labelledby="headingPerilakuMerokok"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah Anda merokok dalam setahun terakhir ini?</td>
                                    <td class="text-center">
                                       @if($detail->merokok_aktif_sd == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->merokok_aktif_sd == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">a</td>
                                    <td>Jenis rokok apa yang dikonsumsi?</td>
                                    <td class="text-center">
                                       @if($detail->jenis_rokok_sd == 'Rokok konvensional (rokok putih, filter, kretek, tingwe, dll)')
                                          <span class="badge badge-info">Rokok konvensional (rokok putih, filter, kretek, tingwe, dll)</span>
                                       @elseif($detail->jenis_rokok_sd == 'Rokok elektronik (vape, IQOS, dll)')
                                          <span class="badge badge-warning">Rokok elektronik (vape, IQOS, dll)</span>
                                       @elseif($detail->jenis_rokok_sd == 'Keduanya')
                                          <span class="badge badge-danger">Keduanya</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">b</td>
                                    <td>Biasanya, berapa batang rokok yang Anda hisap dalam sehari?</td>
                                    <td class="text-center">
                                       @if($detail->jumlah_rokok_sd)
                                          <span class="badge badge-primary">{{ $detail->jumlah_rokok_sd }} batang/hari</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">c</td>
                                    <td>Sudah berapa tahun Anda merokok?</td>
                                    <td class="text-center">
                                       @if($detail->lama_rokok_sd)
                                          <span class="badge badge-primary">{{ $detail->lama_rokok_sd }} tahun</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah Anda terpapar asap rokok atau menghirup asap rokok dari orang lain dalam sebulan terakhir?</td>
                                    <td class="text-center">
                                       @if($detail->terpapar_rokok_sd == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->terpapar_rokok_sd == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
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
                  <div id="collapseTalasemia" class="collapse" aria-labelledby="headingTalasemia"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah ada anggota keluarga kandung Anda dinyatakan menderita Talasemia, atau kelainan darah atau pernah menjalani transfusi darah secara rutin?</td>
                                    <td class="text-center">
                                       @if($detail->talasemia_1 == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->talasemia_1 == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah ada anggota keluarga kandung Anda dinyatakan sebagai pembawa sifat talasemia (mereka yang memiliki genetik yang tidak normal sehingga berpotensi menurunkan penyakit Talasemia)?</td>
                                    <td class="text-center">
                                       @if($detail->talasemia_2 == 'Ya')
                                          <span class="badge badge-danger">Ya</span>
                                       @elseif($detail->talasemia_2 == 'Tidak')
                                          <span class="badge badge-success">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Riwayat Imunisasi Rutin -->
               <div class="card mb-0">
                  <div class="card-header" id="headingRiwayatImunisasi">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseRiwayatImunisasi" aria-expanded="false" aria-controls="collapseRiwayatImunisasi">
                           Riwayat Imunisasi Rutin
                        </button>
                     </h2>
                  </div>
                  <div id="collapseRiwayatImunisasi" class="collapse" aria-labelledby="headingRiwayatImunisasi"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi Hepatitis B?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_hepatitis == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_hepatitis == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi BCG?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_bcg == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_bcg == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi OPV1?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_opv1 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_opv1 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi DPT1?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_dpt1 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_dpt1 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">5</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi OPV2?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_opv2 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_opv2 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">6</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi DPT2?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_dpt2 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_dpt2 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">7</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi OPV3?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_opv3 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_opv3 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">8</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi DPT3?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_dpt3 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_dpt3 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">9</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi OPV4?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_opv4 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_opv4 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">10</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi IPV?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_ipv == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_ipv == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">11</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi Campak 1?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_campak1 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_campak1 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">12</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi DPT4?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_dpt4 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_dpt4 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">13</td>
                                    <td>Apakah anak sudah mendapatkan imunisasi Campak 2?</td>
                                    <td class="text-center">
                                       @if($detail->imunisasi_campak2 == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->imunisasi_campak2 == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Faktor Risiko Hepatitis -->
               <div class="card mb-0">
                  <div class="card-header" id="headingFaktorRisikoHepatitis">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseFaktorRisikoHepatitis" aria-expanded="false" aria-controls="collapseFaktorRisikoHepatitis">
                           Faktor Risiko Hepatitis
                        </button>
                     </h2>
                  </div>
                  <div id="collapseFaktorRisikoHepatitis" class="collapse" aria-labelledby="headingFaktorRisikoHepatitis"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah anak pernah menjalani tes untuk Hepatitis B dan mendapatkan hasil positif?</td>
                                    <td class="text-center">
                                       @if($detail->tes_hepatitis_sekolah == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tes_hepatitis_sekolah == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah anak memiliki ibu kandung/saudara sekandung yang menderita Hepatitis B?</td>
                                    <td class="text-center">
                                       @if($detail->keluarga_hepatitis_sekolah == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->keluarga_hepatitis_sekolah == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah anak pernah menerima transfusi darah sebelumnya?</td>
                                    <td class="text-center">
                                       @if($detail->tranfusi_darah_sekolah == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tranfusi_darah_sekolah == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah anak pernah menjalani cuci darah atau hemodialisis?</td>
                                    <td class="text-center">
                                       @if($detail->cucidarah_sekolah == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->cucidarah_sekolah == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Skrining Tuberkulosis Anak -->
               <div class="card mb-0">
                  <div class="card-header" id="headingSkriningTBC">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseSkriningTBC" aria-expanded="false" aria-controls="collapseSkriningTBC">
                           Skrining Tuberkulosis Anak (Kelas 1-8)
                        </button>
                     </h2>
                  </div>
                  <div id="collapseSkriningTBC" class="collapse" aria-labelledby="headingSkriningTBC"
                     data-parent="#accordionSkrining">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="25%" class="text-center">Jawaban</th>
                                 </tr>
                                 <tr>
                                    <td class="text-center">1</td>
                                    <td>Apakah anak batuk terus menerus selama 2 minggu atau lebih?</td>
                                    <td class="text-center">
                                       @if($detail->tbc_batuk_lama == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tbc_batuk_lama == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">2</td>
                                    <td>Apakah berat badan anak menurun dalam 2 bulan terakhir tanpa sebab yang jelas?</td>
                                    <td class="text-center">
                                       @if($detail->tbc_bb_turun == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tbc_bb_turun == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">3</td>
                                    <td>Apakah anak sering demam tanpa sebab yang jelas, terutama pada sore/malam hari?</td>
                                    <td class="text-center">
                                       @if($detail->tbc_demam == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tbc_demam == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">4</td>
                                    <td>Apakah anak sering berkeringat pada malam hari dan mudah lelah?</td>
                                    <td class="text-center">
                                       @if($detail->tbc_lesu == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tbc_lesu == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-center">5</td>
                                    <td>Apakah ada anggota keluarga yang menderita TBC atau sedang dalam pengobatan TBC?</td>
                                    <td class="text-center">
                                       @if($detail->tbc_kontak == 'Ya')
                                          <span class="badge badge-success">Ya</span>
                                       @elseif($detail->tbc_kontak == 'Tidak')
                                          <span class="badge badge-danger">Tidak</span>
                                       @elseif($detail->tbc_kontak == 'Tidak Tahu')
                                          <span class="badge badge-warning">Tidak Tahu</span>
                                       @else
                                          <span class="badge badge-secondary">-</span>
                                       @endif
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               




            </div>
         </div>
      </div>
   </div>
   
   <!-- Hasil Pemeriksaan Kesehatan -->
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-header bg-success">
            <h5 class="card-title"><i class="fas fa-user-md mr-2"></i> Hasil Pemeriksaan Kesehatan</h5>
         </div>
         <div class="card-body p-0">
            <div class="accordion" id="accordionPemeriksaan">
               
               <!-- Hasil Pemeriksaan Kesehatan -->
               <div class="card mb-0">
                  <div class="card-header" id="headingHasilPemeriksaan">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseHasilPemeriksaan" aria-expanded="false" aria-controls="collapseHasilPemeriksaan">
                           Hasil Pemeriksaan Kesehatan
                        </button>
                     </h2>
                  </div>
                  <div id="collapseHasilPemeriksaan" class="collapse" aria-labelledby="headingHasilPemeriksaan"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="50%">Berat Badan</th>
                                    <td>{{ $detail->berat_badan ?? '-' }} kg</td>
                                 </tr>
                                 <tr>
                                    <th>Tinggi Badan</th>
                                    <td>{{ $detail->tinggi_badan ?? '-' }} cm</td>
                                 </tr>
                                 <tr>
                                    <th>IMT/U</th>
                                    <td>{{ $detail->imt ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tekanan Darah Sistole</th>
                                    <td>{{ $detail->sistole ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Tekanan Darah Diastole</th>
                                    <td>{{ $detail->diastole ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="50%">Jumlah Gigi Karies</th>
                                    <td>{{ $detail->gigi_karies ?? '0' }} gigi</td>
                                 </tr>
                                 <tr>
                                    <th>Pemeriksaan GDS</th>
                                    <td>{{ $detail->hasil_gds ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Pemeriksaan HB</th>
                                    <td>{{ $detail->pemeriksaan_hb ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
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
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6">
                              <h6 class="font-weight-bold mb-3">Pemeriksaan Telinga</h6>
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="60%">Gangguan Telinga Kanan</th>
                                    <td>{{ $detail->gangguan_telingga_kanan ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Gangguan Telinga Kiri</th>
                                    <td>{{ $detail->gangguan_telingga_kiri ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Serumen Kanan</th>
                                    <td>{{ $detail->serumen_kanan ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Serumen Kiri</th>
                                    <td>{{ $detail->serumen_kiri ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Infeksi Telinga Kanan</th>
                                    <td>{{ $detail->infeksi_telingga_kanan ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Infeksi Telinga Kiri</th>
                                    <td>{{ $detail->infeksi_telingga_kiri ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                           <div class="col-md-6">
                              <h6 class="font-weight-bold mb-3">Pemeriksaan Mata</h6>
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="60%">Selaput Mata Kanan</th>
                                    <td>{{ $detail->selaput_mata_kanan ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Selaput Mata Kiri</th>
                                    <td>{{ $detail->selaput_mata_kiri ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Visus Mata Kanan</th>
                                    <td>{{ $detail->visus_mata_kanan ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Visus Mata Kiri</th>
                                    <td>{{ $detail->visus_mata_kiri ?? '-' }}</td>
                                 </tr>
                                 <tr>
                                    <th>Menggunakan Kacamata</th>
                                    <td>{{ $detail->kacamata ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <!-- Hasil Pemeriksaan Kebugaran -->
               <div class="card mb-0">
                  <div class="card-header" id="headingHasilKebugaran">
                     <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                           data-target="#collapseHasilKebugaran" aria-expanded="false" aria-controls="collapseHasilKebugaran">
                           Hasil Pemeriksaan Kebugaran
                        </button>
                     </h2>
                  </div>
                  <div id="collapseHasilKebugaran" class="collapse" aria-labelledby="headingHasilKebugaran"
                     data-parent="#accordionPemeriksaan">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-12">
                              <table class="table table-bordered">
                                 <tr>
                                    <th width="30%">Hasil Pemeriksaan Kebugaran Jasmani Anak</th>
                                    <td>{{ $detail->kebugaran_jasmani ?? '-' }}</td>
                                 </tr>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
            </div>
         </div>
      </div>
   </div>
   @else
   <div class="col-md-12 mb-3">
      <div class="card">
         <div class="card-body text-center">
            <div class="alert alert-warning">
               <i class="fas fa-exclamation-triangle mr-2"></i>
               Siswa ini belum melakukan skrining kesehatan.
            </div>
         </div>
      </div>
   </div>
   @endif

</div>

<script>
$(document).ready(function() {
    // Handle petugas entry dropdown change
    $('#id_petugas_entri').on('change', function() {
        var petugasId = $(this).val();
        var pkgId = '{{ $detail->id_pkg ?? "" }}';
        
        if (petugasId && pkgId) {
            // Show loading state
            $(this).prop('disabled', true);
            
            $.ajax({
                url: '{{ route("ilp.pendaftaran-ckg.update-petugas-entry-sekolah") }}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': pkgId,
                    'id_petugas_entri': petugasId
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Petugas entry berhasil diperbarui');
                        // Reload the detail to show updated data
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Gagal memperbarui petugas entry');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating petugas entry:', error);
                    toastr.error('Terjadi kesalahan saat memperbarui petugas entry');
                },
                complete: function() {
                    $('#id_petugas_entri').prop('disabled', false);
                }
            });
        }
    });
});
</script>

<script>
$(document).ready(function() {
    // Initialize accordion behavior
    $('.collapse').on('show.bs.collapse', function () {
        $(this).siblings('.card-header').find('.btn-link').removeClass('collapsed');
    });
    
    $('.collapse').on('hide.bs.collapse', function () {
        $(this).siblings('.card-header').find('.btn-link').addClass('collapsed');
    });
});
</script>