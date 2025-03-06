<div>
   <!-- Form Pendaftaran -->
   <div class="row">
      <div class="col-md-12">
         <x-adminlte-card title="Form Pendaftaran Kunjungan ILP-Posyandu" theme="primary" theme-mode="filled"
            class="border-0 rounded-lg">
            <div class="card-tools position-absolute" style="right: 1rem; top: 0.75rem; z-index: 10;">
               <button type="button" class="btn-toggle-form" id="toggleFormButton" wire:click="toggleFormPendaftaran"
                  wire:loading.attr="disabled" wire:target="toggleFormPendaftaran"
                  aria-expanded="{{ $showFormPendaftaran ? 'true' : 'false' }}" aria-controls="formPendaftaran"
                  title="{{ $showFormPendaftaran ? 'Sembunyikan Form' : 'Tampilkan Form' }}">
                  <i class="fas {{ $showFormPendaftaran ? 'fa-minus' : 'fa-plus' }}"
                     wire:loading.class="fa-spinner fa-spin" wire:target="toggleFormPendaftaran" aria-hidden="true"></i>
                  <span class="sr-only">{{ $showFormPendaftaran ? 'Sembunyikan Form' : 'Tampilkan Form' }}</span>
               </button>
            </div>

            <div id="formPendaftaran" class="form-toggle-animation {{ $showFormPendaftaran ? '' : 'collapsed' }}"
               aria-hidden="{{ !$showFormPendaftaran ? 'true' : 'false' }}"
               tabindex="{{ $showFormPendaftaran ? '0' : '-1' }}">
               <div class="row">
                  <div class="col-lg-6">
                     <div class="card border-0 mb-3">
                        <div class="card-header bg-primary text-white">
                           <h5 class="mb-0"><i class="fas fa-user-plus mr-2" aria-hidden="true"></i>Informasi Pasien
                           </h5>
                        </div>
                        <div class="card-body">
                           <div class="form-group">
                              <label for="search_pasien" class="font-weight-bold">
                                 <i class="fas fa-search mr-1" aria-hidden="true"></i> Nomor RM / KTP / Nama Pasien
                              </label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">
                                       <i class="fas fa-user-plus" aria-hidden="true"></i>
                                    </span>
                                 </div>
                                 <input type="text" class="form-control" id="search_pasien"
                                    placeholder="Masukkan Nomor RM / KTP / Nama Pasien" wire:model.defer="search_term"
                                    aria-label="Cari pasien" autocomplete="off">
                                 <div class="input-group-append">
                                    <button class="btn btn-danger" type="button" wire:click="searchPasienByTerm"
                                       aria-label="Cari pasien">
                                       <i class="fas fa-search mr-1" aria-hidden="true"></i> Cari
                                    </button>
                                 </div>
                              </div>
                              <small class="form-text text-muted mt-2">
                                 Cari : <strong>Nomor RM</strong> (mis: 000542), <strong>No KTP</strong> (mis:
                                 3313xxxxxxxxxx1)
                              </small>
                              <input type="hidden" id="no_rkm_medis" wire:model.defer="no_rkm_medis">
                           </div>

                           <div class="form-group">
                              <label for="nm_pasien" class="font-weight-bold"><i class="fas fa-user mr-1"></i> Nama
                                 Pasien</label>
                              <input type="text" class="form-control bg-light" id="nm_pasien" placeholder="Nama Pasien"
                                 wire:model.defer="nm_pasien" disabled>
                           </div>

                           <div class="form-group">
                              <label for="status_pasien" class="font-weight-bold"><i class="fas fa-user-tag mr-1"></i>
                                 Status Pasien</label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i
                                          class="fas fa-user-clock"></i></span>
                                 </div>
                                 <div class="form-control p-0 overflow-hidden">
                                    <div
                                       class="status-pasien status-{{ strtolower($status_pasien ?? '') }} w-100 h-100 m-0 d-flex align-items-center px-2">
                                       {{ $status_pasien }}
                                    </div>
                                 </div>
                                 <div class="input-group-append">
                                    <span class="input-group-text bg-secondary text-white">{{ $umur_tahun ?? '-' }}
                                       tahun</span>
                                 </div>
                              </div>
                              <small class="form-text text-muted mt-2">
                                 Umur: Balita (0-5), PraSekolah (6-9), Remaja (10-17), Produktif (18-59), Lansia (≥60)
                              </small>
                           </div>
                        </div>
                     </div>

                     <div class="card border-0 mb-3">
                        <div class="card-header bg-success text-white">
                           <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Informasi Kunjungan</h5>
                        </div>
                        <div class="card-body">
                           <div class="form-group">
                              <label for="tgl_registrasi" class="font-weight-bold"><i
                                    class="fas fa-calendar-alt mr-1"></i> Tanggal Registrasi</label>
                              <input type="datetime-local" class="form-control" id="tgl_registrasi"
                                 wire:model.defer="tgl_registrasi" required>
                           </div>

                           <div class="form-group">
                              <label for="dokter" class="font-weight-bold"><i class="fas fa-user-md mr-1"></i> Bidan /
                                 Perawat</label>
                              <select class="form-control" id="dokter" wire:model.defer="dokter" required>
                                 <option value="">Pilih Bidan / Perawat</option>
                                 @foreach($dokters as $d)
                                 <option value="{{ $d->kd_dokter }}">{{ $d->nama }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-6">
                     <div class="card border-0 mb-3">
                        <div class="card-header bg-info text-white">
                           <h5 class="mb-0"><i class="fas fa-user-friends mr-2"></i>Informasi Penanggung Jawab</h5>
                        </div>
                        <div class="card-body">
                           <div class="form-group">
                              <label for="pj" class="font-weight-bold"><i class="fas fa-user-friends mr-1"></i>
                                 Penanggung Jawab</label>
                              <input type="text" class="form-control" id="pj" placeholder="Penanggung Jawab"
                                 wire:model.defer="pj" required>
                           </div>

                           <div class="form-group">
                              <label for="hubungan_pj" class="font-weight-bold"><i
                                    class="fas fa-people-arrows mr-1"></i> Hubungan Penanggung Jawab</label>
                              <input type="text" class="form-control" id="hubungan_pj"
                                 placeholder="Hubungan Penanggung Jawab" wire:model.defer="hubungan_pj" required>
                           </div>

                           <div class="form-group mb-0">
                              <label for="alamat_pj" class="font-weight-bold"><i class="fas fa-map-marker-alt mr-1"></i>
                                 Alamat Penanggung Jawab</label>
                              <textarea class="form-control" id="alamat_pj" placeholder="Alamat Penanggung Jawab"
                                 wire:model.defer="alamat_pj" required rows="3"></textarea>
                              <small class="form-text text-muted mt-2">
                                 Harap isi bidang ini.
                              </small>
                           </div>
                        </div>
                     </div>

                     <div class="card border-0 mb-3">
                        <div class="card-header bg-warning text-white">
                           <h5 class="mb-0"><i class="fas fa-hospital-user mr-2"></i>Informasi Posyandu</h5>
                        </div>
                        <div class="card-body">
                           <div class="form-group">
                              <label for="kd_poli" class="font-weight-bold"><i class="fas fa-hospital mr-1"></i>
                                 PUSTU</label>
                              <select class="form-control" id="kd_poli" wire:model.defer="kd_poli" required>
                                 <option value="">Pilih PUSTU</option>
                                 @foreach($poliklinik as $p)
                                 <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
                                 @endforeach
                              </select>
                           </div>

                           <div class="form-group mb-0">
                              <label for="data_posyandu" class="font-weight-bold"><i
                                    class="fas fa-hospital-user mr-1"></i> Data Posyandu</label>
                              <select class="form-control" id="data_posyandu" wire:model.defer="data_posyandu" required>
                                 <option value="">Pilih Posyandu</option>
                                 @foreach($listPosyandu as $p)
                                 <option value="{{ $p['nama_posyandu'] }}">{{ $p['nama_posyandu'] }}</option>
                                 @endforeach
                              </select>
                           </div>

                           <div class="form-group mt-4 mb-0">
                              <div class="d-flex justify-content-center">
                                 <button type="button" class="btn btn-danger btn-lg w-50 mr-2" wire:click="closeModal">
                                    <i class="fas fa-times mr-1"></i> Batal
                                 </button>
                                 <button type="button" class="btn btn-success btn-lg w-50" wire:click="simpan">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </x-adminlte-card>
      </div>
   </div>

   <!-- Tabel Pendaftaran -->
   <div class="row mt-3">
      <div class="col-md-12">
         <x-adminlte-card title="" theme="light" theme-mode="outline" class="border-0 rounded-lg">
            <!-- Panel Filter -->
            <div class="row mb-3">
               <div class="col-md-12">
                  <div class="card border-0">
                     <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>Data Pendaftaran Hari Ini</h5>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-6">
                              <!-- Filter Kiri -->
                              <div class="card border-0 mb-3">
                                 <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>Filter Data</h6>
                                 </div>
                                 <div class="card-body p-3">
                                    <div class="form-group mb-3">
                                       <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" id="filterPoliPustu"
                                             wire:model="filterPoliPustu" wire:change="$refresh">
                                          <label class="custom-control-label font-weight-bold" for="filterPoliPustu">
                                             <i class="fas fa-clinic-medical mr-1"></i> Tampilkan hanya Poliklinik PUSTU
                                          </label>
                                       </div>
                                    </div>
                                    <div class="form-group mb-3">
                                       <button class="btn btn-danger btn-block" wire:click="resetFilters">
                                          <i class="fas fa-undo mr-1"></i> Reset Semua Filter
                                       </button>
                                    </div>
                                    <div class="form-group mb-0">
                                       <label for="selectedPosyandu" class="font-weight-bold"><i
                                             class="fas fa-hospital-user mr-1"></i> Pilih Nama Posyandu</label>
                                       <div class="input-group">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text bg-primary text-white"><i
                                                   class="fas fa-search"></i></span>
                                          </div>
                                          <select class="form-control" id="selectedPosyandu"
                                             wire:model="selectedPosyandu" wire:change="$refresh">
                                             <option value="">Semua Posyandu</option>
                                             @foreach($listPosyandu as $posyandu)
                                             <option value="{{ $posyandu['nama_posyandu'] }}">{{
                                                $posyandu['nama_posyandu'] }}
                                             </option>
                                             @endforeach
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <!-- Filter Kanan -->
                              <div class="card border-0 mb-3">
                                 <div class="card-header bg-success text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-user-clock mr-2"></i>Filter Status Pasien</h6>
                                 </div>
                                 <div class="card-body p-3">
                                    <div class="form-group mb-3">
                                       <label for="filterStatus" class="font-weight-bold"><i
                                             class="fas fa-user-clock mr-1"></i> Status Pasien</label>
                                       <div class="input-group">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text bg-success text-white"><i
                                                   class="fas fa-filter"></i></span>
                                          </div>
                                          <select class="form-control" id="filterStatus" wire:model="filterStatus"
                                             wire:change="$refresh">
                                             <option value="">Semua Status</option>
                                             <option value="Belum">Menunggu</option>
                                             <option value="Sudah">Selesai</option>
                                          </select>
                                          <div class="input-group-append">
                                             <button class="btn btn-outline-secondary" wire:click="resetStatusFilter">
                                                <i class="fas fa-undo mr-1"></i> Reset
                                             </button>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="form-group mb-0">
                                       <label for="searchTerm" class="font-weight-bold"><i
                                             class="fas fa-search mr-1"></i>
                                          Cari Pasien</label>
                                       <div class="input-group">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text bg-success text-white"><i
                                                   class="fas fa-search"></i></span>
                                          </div>
                                          <input type="text" class="form-control" id="searchTerm"
                                             placeholder="Cari berdasarkan Nama, NIK, atau No. RM"
                                             wire:model.debounce.300ms="searchTerm" wire:keydown.enter="$refresh">
                                          <div class="input-group-append">
                                             <button class="btn btn-success search-btn" wire:click="$refresh">
                                                <i class="fas fa-search mr-1"></i> Cari
                                             </button>
                                             <button class="btn btn-outline-secondary" wire:click="resetSearch">
                                                <i class="fas fa-times mr-1"></i> Reset
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- End Panel Filter -->

            <!-- Tabel Pendaftaran -->
            <div class="table-responsive">
               <div id="table-pendaftaran_wrapper" class="dataTables_wrapper dt-bootstrap4">
                  <!-- Konten wrapper DataTable tetap ada tapi tanpa length control -->
               </div>
               <table id="table-pendaftaran" class="table table-striped table-hover">
                  <thead class="bg-primary text-white">
                     <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="15%">No Rawat</th>
                        <th width="10%">No RM</th>
                        <th>Nama Pasien</th>
                        <th>Dokter</th>
                        <th>Poliklinik</th>
                        <th>Posyandu</th>
                        <th>Penjamin</th>
                        <th class="text-center" width="8%">Status</th>
                        <th class="text-center" width="10%">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($pendaftaran as $key => $item)
                     <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>
                           <a href="{{ route('ilp.dewasa.form', ['noRawat' => $item->no_rawat]) }}"
                              class="text-primary font-weight-bold" title="Buka Form ILP">
                              {{ $item->no_rawat }}
                              <i class="fas fa-external-link-alt ml-1 small"></i>
                           </a>
                        </td>
                        <td>{{ $item->no_rkm_medis }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->nm_dokter }}</td>
                        <td>{{ $item->nm_poli }}</td>
                        <td>{{ $item->data_posyandu ?? '-' }}</td>
                        <td>{{ $item->png_jawab }}</td>
                        <td class="text-center">
                           @if($item->stts == 'Belum')
                           <span class="badge badge-warning">Menunggu</span>
                           @elseif($item->stts == 'Sudah')
                           <span class="badge badge-success">Selesai</span>
                           @else
                           <span class="badge badge-info">{{ $item->stts }}</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="btn-group">
                              <button type="button" class="btn btn-primary btn-sm"
                                 wire:click="bukaModalPendaftaran('{{ $item->no_rawat }}')">
                                 <i class="fas fa-edit"></i>
                              </button>
                              <button type="button" class="btn btn-danger btn-sm">
                                 <i class="fas fa-trash"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </x-adminlte-card>
      </div>
   </div>

   <!-- Modal Form Pendaftaran -->
   <div class="modal fade" id="modalPendaftaran" tabindex="-1" role="dialog" aria-labelledby="modalPendaftaranTitle"
      aria-hidden="true" wire:ignore.self>
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header bg-primary text-white">
               <h5 class="modal-title" id="modalPendaftaranTitle"><i class="fas fa-user-plus mr-2"></i>Form
                  Pendaftaran
               </h5>
               <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="card border-0 mb-3">
                        <div class="card-header bg-primary text-white py-2">
                           <h6 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Informasi Pasien</h6>
                        </div>
                        <div class="card-body p-3">
                           <div class="form-group">
                              <label for="modal_search_pasien" class="font-weight-bold"><i
                                    class="fas fa-search mr-1"></i> Nomor RM / KTP / Nama Pasien</label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i
                                          class="fas fa-user-plus"></i></span>
                                 </div>
                                 <input type="text" class="form-control" id="modal_search_pasien"
                                    placeholder="Masukkan Nomor RM / KTP / Nama Pasien" wire:model.defer="search_term">
                                 <div class="input-group-append">
                                    <button class="btn btn-danger" type="button" wire:click="searchPasienByTerm">
                                       <i class="fas fa-search mr-1"></i> Cari
                                    </button>
                                 </div>
                              </div>
                              <small class="form-text text-muted mt-2">
                                 Search : <strong>Nomor RM</strong> (mis: 000542), <strong>No KTP</strong>
                                 (mis:
                                 3313xxxxxxxxxx1)
                              </small>
                              <input type="hidden" id="modal_no_rkm_medis" wire:model.defer="no_rkm_medis">
                           </div>

                           <div class="form-group">
                              <label for="modal_nm_pasien" class="font-weight-bold"><i class="fas fa-user mr-1"></i>
                                 Nama Pasien</label>
                              <input type="text" class="form-control bg-light" id="modal_nm_pasien"
                                 placeholder="Nama Pasien" wire:model.defer="nm_pasien" disabled>
                           </div>

                           <div class="form-group mb-0">
                              <label for="modal_status_pasien" class="font-weight-bold"><i
                                    class="fas fa-user-tag mr-1"></i> Status Pasien</label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i
                                          class="fas fa-user-clock"></i></span>
                                 </div>
                                 <div class="form-control p-0 overflow-hidden">
                                    <div
                                       class="status-pasien status-{{ strtolower($status_pasien ?? '') }} w-100 h-100 m-0 d-flex align-items-center px-2">
                                       {{ $status_pasien }}
                                    </div>
                                 </div>
                                 <div class="input-group-append">
                                    <span class="input-group-text bg-secondary text-white">{{ $umur_tahun ??
                                       '-' }}
                                       tahun</span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="card border-0 mb-3">
                        <div class="card-header bg-success text-white py-2">
                           <h6 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Informasi Kunjungan</h6>
                        </div>
                        <div class="card-body p-3">
                           <div class="form-group">
                              <label for="modal_tgl_registrasi" class="font-weight-bold"><i
                                    class="fas fa-calendar-alt mr-1"></i> Tanggal Registrasi</label>
                              <input type="datetime-local" class="form-control" id="modal_tgl_registrasi"
                                 wire:model.defer="tgl_registrasi" required>
                           </div>

                           <div class="form-group mb-0">
                              <label for="modal_dokter" class="font-weight-bold"><i class="fas fa-user-md mr-1"></i>
                                 Dokter</label>
                              <select class="form-control" id="modal_dokter" wire:model.defer="dokter" required>
                                 <option value="">Pilih Dokter</option>
                                 @foreach($dokters as $d)
                                 <option value="{{ $d->kd_dokter }}">{{ $d->nama }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="card border-0 mb-3">
                        <div class="card-header bg-info text-white py-2">
                           <h6 class="mb-0"><i class="fas fa-user-friends mr-2"></i>Informasi Penanggung Jawab
                           </h6>
                        </div>
                        <div class="card-body p-3">
                           <div class="form-group">
                              <label for="modal_pj" class="font-weight-bold"><i class="fas fa-user-friends mr-1"></i>
                                 Penanggung Jawab</label>
                              <input type="text" class="form-control" id="modal_pj" placeholder="Penanggung Jawab"
                                 wire:model.defer="pj" required>
                           </div>

                           <div class="form-group">
                              <label for="modal_hubungan_pj" class="font-weight-bold"><i
                                    class="fas fa-people-arrows mr-1"></i> Hubungan Penanggung Jawab</label>
                              <input type="text" class="form-control" id="modal_hubungan_pj"
                                 placeholder="Hubungan Penanggung Jawab" wire:model.defer="hubungan_pj" required>
                           </div>

                           <div class="form-group mb-0">
                              <label for="modal_alamat_pj" class="font-weight-bold"><i
                                    class="fas fa-map-marker-alt mr-1"></i> Alamat Penanggung Jawab</label>
                              <textarea class="form-control" id="modal_alamat_pj" placeholder="Alamat Penanggung Jawab"
                                 wire:model.defer="alamat_pj" required rows="3"></textarea>
                           </div>
                        </div>
                     </div>

                     <div class="card border-0 mb-3">
                        <div class="card-header bg-warning text-white py-2">
                           <h6 class="mb-0"><i class="fas fa-hospital-user mr-2"></i>Informasi Tambahan</h6>
                        </div>
                        <div class="card-body p-3">
                           <div class="form-group">
                              <label for="modal_kd_poli" class="font-weight-bold"><i class="fas fa-hospital mr-1"></i>
                                 Poliklinik</label>
                              <select class="form-control" id="modal_kd_poli" wire:model.defer="kd_poli" required>
                                 <option value="">Pilih Poliklinik</option>
                                 @foreach($poliklinik as $p)
                                 <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
                                 @endforeach
                              </select>
                           </div>

                           <div class="form-group mb-0">
                              <label for="modal_data_posyandu" class="font-weight-bold"><i
                                    class="fas fa-hospital-user mr-1"></i> Data Posyandu</label>
                              <select class="form-control" id="modal_data_posyandu" wire:model.defer="data_posyandu"
                                 required>
                                 <option value="">Pilih Posyandu</option>
                                 @foreach($listPosyandu as $p)
                                 <option value="{{ $p['nama_posyandu'] }}">{{ $p['nama_posyandu'] }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer bg-light">
               <button type="button" class="btn btn-danger" wire:click="closeModal" data-dismiss="modal">
                  <i class="fas fa-times mr-1"></i> Batal
               </button>
               <button type="button" class="btn btn-success" wire:click="simpan">
                  <i class="fas fa-save mr-1"></i> Simpan
               </button>
            </div>
         </div>
      </div>
   </div>

   @push('css')
   <style>
      /* Warna dan tema utama */
      :root {
         --primary-color: #2c3e50;
         --secondary-color: #3498db;
         --accent-color: #e74c3c;
         --success-color: #27ae60;
         --warning-color: #f39c12;
         --info-color: #3498db;
         --light-bg: #f8f9fa;
         --dark-text: #2c3e50;
         --light-text: #ecf0f1;
         --border-radius: 4px;
         --box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);

         /* Warna untuk status pasien */
         --balita-color: #219653;
         --prasekolah-color: #2D7DD2;
         --remaja-color: #8E44AD;
         --produktif-color: #D35400;
         --lansia-color: #C0392B;
      }

      /* Reset dan basic styles */
      .card {
         border-radius: var(--border-radius);
         box-shadow: var(--box-shadow);
         transition: all 0.2s ease;
         overflow: hidden;
      }

      .card-header {
         padding: 0.75rem 1.25rem;
         border-bottom: none;
      }

      .card-body {
         padding: 1rem 1.25rem;
      }

      /* Form elements styling */
      .form-control {
         border-radius: var(--border-radius);
         padding: 0.5rem 0.75rem;
         border: 1px solid #dee2e6;
         transition: all 0.2s ease;
         font-size: 0.9rem;
         height: auto;
      }

      .form-control:focus {
         border-color: var(--secondary-color);
         box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
      }

      /* Button styling */
      .btn {
         border-radius: var(--border-radius);
         padding: 0.375rem 0.75rem;
         font-weight: 500;
         transition: all 0.2s ease;
         font-size: 0.9rem;
      }

      .btn-primary {
         background-color: var(--primary-color);
         border-color: var(--primary-color);
      }

      .btn-success {
         background-color: var(--success-color);
         border-color: var(--success-color);
      }

      .btn-danger {
         background-color: var(--accent-color);
         border-color: var(--accent-color);
      }

      /* Styling untuk status pasien */
      .status-pasien {
         font-weight: 500;
         color: white;
         height: 100%;
      }

      .status-balita {
         background-color: var(--balita-color);
      }

      .status-prasekolah {
         background-color: var(--prasekolah-color);
      }

      .status-remaja {
         background-color: var(--remaja-color);
      }

      .status-produktif {
         background-color: var(--produktif-color);
      }

      .status-lansia {
         background-color: var(--lansia-color);
      }

      /* Form toggle animation */
      .form-toggle-animation {
         transition: max-height 0.3s ease-in-out, opacity 0.2s ease-in-out;
         overflow: hidden;
         max-height: 2000px;
         opacity: 1;
      }

      .form-toggle-animation.collapsed {
         max-height: 0;
         opacity: 0;
         margin: 0;
         padding: 0;
      }

      /* Toggle button */
      .btn-toggle-form {
         background-color: rgba(255, 255, 255, 0.2);
         border: 1px solid rgba(255, 255, 255, 0.4);
         color: var(--light-text);
         width: 24px;
         height: 24px;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.2s ease;
         cursor: pointer;
         position: relative;
         z-index: 20;
         padding: 0;
         margin: 0;
      }

      /* Table styling */
      .table {
         margin-bottom: 0;
      }

      .table th {
         border: none;
         padding: 0.75rem;
         vertical-align: middle;
         font-weight: 500;
         font-size: 0.85rem;
      }

      .table td {
         padding: 0.75rem;
         vertical-align: middle;
         border-top: 1px solid rgba(0, 0, 0, 0.05);
         font-size: 0.9rem;
      }

      /* Badge styling */
      .badge {
         padding: 0.4rem 0.6rem;
         border-radius: 3px;
         font-weight: 500;
         font-size: 0.75rem;
      }

      /* Modal styling */
      .modal-content {
         border-radius: var(--border-radius);
         border: none;
         overflow: hidden;
      }

      .modal-header {
         border-bottom: none;
         padding: 1rem 1.25rem;
      }

      .modal-body {
         padding: 1rem 1.25rem;
      }

      .modal-footer {
         padding: 1rem 1.25rem;
         border-top: 1px solid #eee;
      }

      /* Utility classes */
      .rounded-lg {
         border-radius: 6px !important;
      }

      /* Pencarian pasien styling */
      #searchTerm {
         transition: all 0.3s ease;
      }

      #searchTerm:focus {
         box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
         border-color: var(--success-color);
      }

      .search-btn {
         transition: all 0.3s ease;
      }

      .search-btn:hover {
         background-color: #219653;
         transform: translateY(-1px);
      }

      .search-btn:active {
         transform: translateY(0);
      }

      /* Animasi loading untuk pencarian */
      @keyframes pulse {
         0% {
            opacity: 1;
         }

         50% {
            opacity: 0.6;
         }

         100% {
            opacity: 1;
         }
      }

      .searching {
         animation: pulse 1.5s infinite ease-in-out;
      }
   </style>
   @endpush

   @push('scripts')
   <script>
      document.addEventListener('livewire:load', function () {
            // Fungsi untuk menyembunyikan sebagian nomor KTP
            function maskKtp(ktp) {
                if (!ktp || ktp === '-') return '-';
                var ktpLength = ktp.length;
                if (ktpLength <= 5) return ktp;
                
                var firstFour = ktp.substring(0, 4);
                var lastOne = ktp.substring(ktpLength - 1);
                var masked = 'x'.repeat(ktpLength - 5);
                
                return firstFour + masked + lastOne;
            }
            
            // Inisialisasi Select2 untuk dropdown
            $('.form-control-lg').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Pilih...',
                allowClear: true
            });
            
            // Tunggu hingga dokumen sepenuhnya dimuat
            $(document).ready(function() {
                console.log('Document ready - Initializing DataTable');
                
                // Hapus instance DataTable yang mungkin sudah ada
                if ($.fn.DataTable.isDataTable('#table-pendaftaran')) {
                    $('#table-pendaftaran').DataTable().destroy();
                    console.log('Existing DataTable destroyed');
                }
                
                // Inisialisasi DataTable dengan konfigurasi yang lebih sederhana
                var dataTable = $('#table-pendaftaran').DataTable({
                    "paging": true,
                    "pageLength": 10,
                    "lengthChange": false,
                    "ordering": true,
                    "info": false,
                    "searching": false,
                    "language": {
                        "paginate": {
                            "first": '<i class="fas fa-angle-double-left"></i>',
                            "previous": '<i class="fas fa-angle-left"></i>',
                            "next": '<i class="fas fa-angle-right"></i>',
                            "last": '<i class="fas fa-angle-double-right"></i>'
                        }
                    }
                });
                
                // Tambahkan efek visual saat pencarian
                const searchInput = document.getElementById('searchTerm');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const tableBody = document.querySelector('#table-pendaftaran tbody');
                        if (this.value.length > 0) {
                            tableBody.classList.add('searching');
                        } else {
                            tableBody.classList.remove('searching');
                        }
                    });
                }
                
                // Re-initialize datatable ketika konten diperbarui karena filter
                Livewire.hook('message.sent', (message, component) => {
                    if (message.updateQueue && message.updateQueue.some(u => u.method === '$refresh')) {
                        const tableBody = document.querySelector('#table-pendaftaran tbody');
                        if (tableBody) tableBody.classList.add('searching');
                    }
                });
                
                Livewire.hook('message.processed', (message, component) => {
                    if (message.updateQueue && message.updateQueue.some(u => u.payload.name === 'pendaftaran')) {
                        console.log('Pendaftaran data updated, reinitializing DataTable');
                        
                        // Remove searching animation
                        const tableBody = document.querySelector('#table-pendaftaran tbody');
                        if (tableBody) tableBody.classList.remove('searching');
                        
                        // Destroy existing DataTable
                        if ($.fn.DataTable.isDataTable('#table-pendaftaran')) {
                            $('#table-pendaftaran').DataTable().destroy();
                        }
                        
                        // Reinitialize DataTable
                        dataTable = $('#table-pendaftaran').DataTable({
                            "paging": true,
                            "pageLength": 10,
                            "lengthChange": false,
                            "ordering": true,
                            "info": false,
                            "searching": false,
                            "language": {
                                "paginate": {
                                    "first": '<i class="fas fa-angle-double-left"></i>',
                                    "previous": '<i class="fas fa-angle-left"></i>',
                                    "next": '<i class="fas fa-angle-right"></i>',
                                    "last": '<i class="fas fa-angle-double-right"></i>'
                                }
                            }
                        });
                    }
                });
                
                // Event handler untuk modal dan toggle form
                Livewire.on('openModalPendaftaran', function () {
                    $('#modalPendaftaran').modal('show');
                });
                
                Livewire.on('closeModalPendaftaran', function () {
                    $('#modalPendaftaran').modal('hide');
                });

                $('#toggleFormButton').on('click', function(e) {
                    $(this).prop('disabled', true);
                    setTimeout(() => {
                        $(this).prop('disabled', false);
                    }, 1000);
                });
                
                Livewire.on('toggleFormPendaftaran', function(showForm) {
                    const $icon = $('.btn-toggle-form i');
                    if (showForm) {
                        $icon.removeClass('fa-plus fa-spinner fa-spin').addClass('fa-minus');
                    } else {
                        $icon.removeClass('fa-minus fa-spinner fa-spin').addClass('fa-plus');
                    }
                    
                    $('#toggleFormButton').attr('data-state', showForm ? 'expanded' : 'collapsed');
                    
                    const formContent = document.querySelector('.form-toggle-animation');
                    if (formContent) {
                        if (showForm) {
                            formContent.classList.remove('collapsed');
                        } else {
                            formContent.classList.add('collapsed');
                        }
                    }
                });
            });
        });
   </script>
   @endpush
</div>