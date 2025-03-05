@extends('adminlte::page')

@section('title', 'Pelayanan ILP')

@section('content_header')
<div class="d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Pelayanan ILP</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pelayanan ILP</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <p class="text-muted m-0"><i class="fas fa-user-md mr-1"></i> {{$nm_dokter}}</p>
      <p class="text-muted m-0"><i class="fas fa-calendar-day mr-1"></i> {{ date('d F Y') }}</p>
   </div>
</div>
@stop

@section('content')
<div class="preloader">
   <div class="loader">
      <div class="spinner-border text-primary" role="status">
         <span class="sr-only">Loading...</span>
      </div>
      <p class="mt-2">Memuat data...</p>
   </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible animate__animated animate__fadeInDown">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   <h5><i class="icon fas fa-check"></i> Sukses!</h5>
   {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible animate__animated animate__fadeInDown">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
   <h5><i class="icon fas fa-ban"></i> Error!</h5>
   {{ session('error') }}
</div>
@endif

<div class="row mb-3">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h6 class="text-primary mb-0 font-weight-bold"><i class="fas fa-filter mr-2"></i>Filter & Pencarian</h6>
               <a href="#" class="btn btn-sm btn-outline-secondary" id="resetAllFilters">
                  <i class="fas fa-sync mr-1"></i> Reset Filter
               </a>
            </div>
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group mb-md-0">
                     <label class="small text-muted mb-1"><i class="fas fa-search mr-1"></i> Cari Pasien</label>
                     <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" name="search"
                           placeholder="No RM / Nama Pasien" value="{{ $search ?? '' }}">
                        <div class="search-loading">
                           <i class="fas fa-spinner fa-spin"></i>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group mb-md-0">
                     <label class="small text-muted mb-1"><i class="fas fa-clinic-medical mr-1"></i> Posyandu</label>
                     <div class="input-group">
                        <select class="form-control select2-posyandu" id="filter_posyandu" name="filter_posyandu"
                           data-placeholder="Pilih Posyandu">
                           <option value="">Semua Posyandu</option>
                           @foreach($data_posyandu as $posyandu)
                           <option value="{{ $posyandu->nama_posyandu }}" {{ isset($filter_posyandu) &&
                              $filter_posyandu==$posyandu->nama_posyandu ? 'selected' : '' }}>{{
                              $posyandu->nama_posyandu
                              }}</option>
                           @endforeach
                        </select>
                        <div class="posyandu-loading">
                           <i class="fas fa-spinner fa-spin"></i>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="form-group mb-0">
                     <label class="small text-muted mb-1"><i class="fas fa-calendar-alt mr-1"></i> Rentang
                        Tanggal</label>
                     <div class="d-flex">
                        <div class="input-group mr-2">
                           <div class="input-group-prepend">
                              <span class="input-group-text">Dari</span>
                           </div>
                           <input type="date" name="dari" id="dari" class="form-control" value="{{ $dari ?? '' }}">
                        </div>
                        <div class="input-group">
                           <div class="input-group-prepend">
                              <span class="input-group-text">Sampai</span>
                           </div>
                           <input type="date" name="sampai" id="sampai" class="form-control"
                              value="{{ $sampai ?? '' }}">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="filter-badges mt-2">
               <!-- Badge filter aktif akan ditampilkan di sini -->
            </div>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-md-12">
      <div class="card shadow animate__animated animate__fadeIn">
         <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
               <h5 class="card-title mb-0 text-primary">
                  <i class="fas fa-list-alt mr-2"></i> Daftar Pemeriksaan ILP
               </h5>
               <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-primary" id="toggleColumns">
                     <i class="fas fa-columns mr-1"></i> Tampilkan Semua Kolom
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="resetColumns">
                     <i class="fas fa-undo mr-1"></i> Reset Kolom
                  </button>
                  <a href="{{ route('ilp.pendaftaran') }}" class="btn btn-sm btn-primary ml-2">
                     <i class="fas fa-plus mr-1"></i> Tambah Data
                  </a>
               </div>
            </div>
         </div>
         <div class="card-body">
            <div class="table-responsive">
               <table id="table1" class="table table-striped table-hover">
                  <thead>
                     <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="10%">No RM</th>
                        <th width="20%">Nama Pasien</th>
                        <th width="10%">BB (kg)</th>
                        <th width="10%">TB (cm)</th>
                        <th width="10%">IMT</th>
                        <th width="10%">TD</th>
                        <th width="10%">Status</th>
                        <th width="5%" class="text-center">Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($pemeriksaan as $p)
                     <tr data-posyandu="{{ $p['data_posyandu'] ?? '' }}">
                        <td>{{ $p['no'] }}</td>
                        <td>{{ $p['tanggal'] }}</td>
                        <td>{{ $p['no_rm'] }}</td>
                        <td>{{ $p['nama_pasien'] }}</td>
                        <td>{{ $p['berat_badan'] ?? '-' }}</td>
                        <td>{{ $p['tinggi_badan'] ?? '-' }}</td>
                        <td>{{ $p['imt'] ?? '-' }}</td>
                        <td>{{ $p['td'] ?? '-' }}</td>
                        <td>
                           @if($p['status'] == 'Menunggu')
                           <span class="badge badge-warning">{{ $p['status'] }}</span>
                           @elseif($p['status'] == 'Dalam Proses')
                           <span class="badge badge-info">{{ $p['status'] }}</span>
                           @elseif($p['status'] == 'Selesai')
                           <span class="badge badge-success">{{ $p['status'] }}</span>
                           @else
                           <span class="badge badge-secondary">{{ $p['status'] }}</span>
                           @endif
                        </td>
                        <td class="text-center">
                           <div class="dropdown">
                              <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                 id="dropdownMenuButton{{ $p['id'] }}" data-toggle="dropdown" aria-haspopup="true"
                                 aria-expanded="false">
                                 <i class="fas fa-ellipsis-v"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="dropdownMenuButton{{ $p['id'] }}">
                                 <button type="button" class="dropdown-item" data-toggle="modal"
                                    data-target="#modalDetail{{ $p['id'] }}">
                                    <i class="fas fa-eye text-primary mr-2"></i> Detail
                                 </button>
                                 <button type="button" class="dropdown-item" data-toggle="modal"
                                    data-target="#modalHasil{{ $p['id'] }}">
                                    <i class="fas fa-edit text-success mr-2"></i> Edit
                                 </button>
                                 <a href="{{ route('ilp.cetak', $p['id']) }}" target="_blank" class="dropdown-item">
                                    <i class="fas fa-print text-info mr-2"></i> Cetak
                                 </a>
                                 <button type="button" class="dropdown-item send-wa-btn" data-id="{{ $p['id'] }}"
                                    data-no-hp="{{ $p['no_tlp'] ?? '' }}" data-nama="{{ $p['nama_pasien'] }}"
                                    data-no-rm="{{ $p['no_rm'] }}">
                                    <i class="fab fa-whatsapp text-success mr-2"></i> Kirim WA
                                 </button>
                              </div>
                           </div>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

@foreach($pemeriksaan as $p)
{{-- Modal Detail Pemeriksaan --}}
<div class="modal fade" id="modalDetail{{ $p['id'] }}" tabindex="-1" role="dialog"
   aria-labelledby="modalDetailLabel{{ $p['id'] }}" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-detail" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalDetailLabel{{ $p['id'] }}">
               <i class="fas fa-info-circle mr-2"></i> Detail Pemeriksaan ILP
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <!-- Informasi Utama -->
            <div class="alert alert-light border mb-4">
               <div class="d-flex justify-content-between align-items-center">
                  <div>
                     <h6 class="font-weight-bold mb-1">{{ $p['nama_pasien'] }}</h6>
                     <p class="mb-0 text-muted small">
                        <i class="fas fa-id-card mr-1"></i> No RM: {{ $p['no_rm'] }} |
                        <i class="fas fa-calendar-day mr-1"></i> Tanggal: {{ $p['tanggal'] }}
                        @if(isset($p['no_tlp']) && !empty($p['no_tlp']))
                        | <i class="fas fa-phone mr-1"></i> No. HP: {{ $p['no_tlp'] }}
                        @endif
                     </p>
                  </div>
                  <div class="text-right">
                     <h5>
                        @if($p['status'] == 'Menunggu')
                        <span class="badge badge-warning">{{ $p['status'] }}</span>
                        @elseif($p['status'] == 'Dalam Proses')
                        <span class="badge badge-info">{{ $p['status'] }}</span>
                        @elseif($p['status'] == 'Selesai')
                        <span class="badge badge-success">{{ $p['status'] }}</span>
                        @else
                        <span class="badge badge-secondary">{{ $p['status'] }}</span>
                        @endif
                     </h5>
                     <p class="mb-0 text-muted small">
                        <i class="fas fa-clinic-medical mr-1"></i> Posyandu: {{ $p['data_posyandu'] ?? $p['posyandu'] ??
                        '-' }}
                     </p>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-md-6">
                  <div class="card shadow-sm mb-3">
                     <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                           <i class="fas fa-user mr-1"></i> Informasi Pasien
                        </h6>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">No KTP</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['nik'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Tanggal Lahir</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['tgl_lahir'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Usia</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['usia'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Jenis Kelamin</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['jenis_kelamin'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Status Nikah</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['status_nikah'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Alamat</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['alamat'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Posyandu</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['data_posyandu'] ?? $p['posyandu'] ?? '-' }}
                                    </h5>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Tanggal Pelayanan</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['tanggal'] ?? '-' }}</h5>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="col-md-6">
                  <div class="card shadow-sm mb-3">
                     <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                           <i class="fas fa-heartbeat mr-1"></i> Hasil Pemeriksaan
                        </h6>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Berat Badan</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['berat_badan'] ?? '-' }} <small>kg</small>
                                    </h5>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Tinggi Badan</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['tinggi_badan'] ?? '-' }} <small>cm</small>
                                    </h5>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">IMT</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['imt'] ?? '-' }}
                                       @php
                                       $imt = floatval($p['imt'] ?? 0);

                                       $imtCategory = '';
                                       $imtColor = '';

                                       if ($imt < 18.5) { $imtCategory='Kurus' ; $imtColor='info' ; } elseif ($imt>=
                                          18.5 && $imt < 25) { $imtCategory='Normal' ; $imtColor='success' ; } elseif
                                             ($imt>= 25 && $imt < 30) { $imtCategory='Kelebihan Berat Badan' ;
                                                $imtColor='warning' ; } elseif ($imt>= 30) {
                                                $imtCategory = 'Obesitas';
                                                $imtColor = 'danger';
                                                }
                                                @endphp

                                                @if($imtCategory)
                                                <span class="badge badge-{{ $imtColor }} ml-2">{{ $imtCategory }}</span>
                                                @endif
                                    </h5>
                                    <p class="mb-0 small text-muted">
                                       <span class="text-info">Kurus: &lt;18.5</span> |
                                       <span class="text-success">Normal: 18.5-24.9</span> |
                                       <span class="text-warning">Kelebihan BB: 25-29.9</span> |
                                       <span class="text-danger">Obesitas: ≥30</span>
                                    </p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Tekanan Darah</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['td'] ?? '-' }}
                                       @php
                                       $tdParts = explode('/', $p['td'] ?? '0/0');
                                       $sistole = intval(trim($tdParts[0] ?? 0));
                                       $diastole = intval(trim($tdParts[1] ?? 0));

                                       $tdCategory = '';
                                       $tdColor = '';

                                       if ($sistole < 120 && $diastole < 80) { $tdCategory='Normal' ; $tdColor='success'
                                          ; } elseif (($sistole>= 120 && $sistole <= 139) || ($diastole>= 80 &&
                                             $diastole <= 89)) { $tdCategory='Pra-hipertensi' ; $tdColor='warning' ; }
                                                elseif (($sistole>= 140 && $sistole <= 159) || ($diastole>= 90 &&
                                                   $diastole <= 99)) { $tdCategory='Hipertensi tingkat 1' ;
                                                      $tdColor='danger' ; } elseif ($sistole>= 160 || $diastole >= 100)
                                                      {
                                                      $tdCategory = 'Hipertensi tingkat 2';
                                                      $tdColor = 'danger';
                                                      } elseif ($sistole > 140 && $diastole < 90) {
                                                         $tdCategory='Hipertensi Sistolik Terisolasi' ;
                                                         $tdColor='danger' ; } @endphp @if($tdCategory) <span
                                                         class="badge badge-{{ $tdColor }} ml-2">{{ $tdCategory
                                                         }}</span>
                                                         @endif
                                    </h5>
                                    <p class="mb-0 small text-muted">
                                       <span class="text-success">Normal: &lt;120 dan &lt;80</span> |
                                       <span class="text-warning">Pra-hipertensi: 120-139 atau 80-89</span> |
                                       <span class="text-danger">Hipertensi 1: 140-159 atau 90-99</span> |
                                       <span class="text-danger">Hipertensi 2: &gt;160 atau &gt;100</span> |
                                       <span class="text-danger">Hipertensi Sistolik Terisolasi: &gt;140 dan
                                          &lt;90</span>
                                    </p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Tambahkan bagian Hasil Laboratorium di modal detail -->
            <div class="row">
               <div class="col-md-12">
                  <div class="card shadow-sm mb-3">
                     <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                           <i class="fas fa-flask mr-1"></i> Hasil Pemeriksaan Laboratorium
                        </h6>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Hemoglobin</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['hemoglobin'] ?? '-' }}
                                       <small>g/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 12-16 g/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Kolesterol Total</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['kolesterol'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: < 200 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Asam Urat</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['asam_urat'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 3.5-7.2 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Gula Darah Puasa</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['gula_darah_puasa'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 70-100 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">SGOT</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['sgot'] ?? '-' }}
                                       <small>U/L</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 5-40 U/L</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">SGPT</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['sgpt'] ?? '-' }}
                                       <small>U/L</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 5-35 U/L</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Trigliserida</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['trigliserida'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: < 150 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light mb-3">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">HDL</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['hdl'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: > 40 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-3">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">LDL</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['ldl'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: < 100 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Ureum</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['ureum'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 15-43 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">Kreatinin</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['kreatinin'] ?? '-' }}
                                       <small>mg/dL</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: 0.7-1.2 mg/dL</p>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="card bg-light">
                                 <div class="card-body py-2 px-3">
                                    <p class="mb-0 small text-muted">HbA1c</p>
                                    <h5 class="mb-0 font-weight-bold">{{ $p['hba1c'] ?? '-' }}
                                       <small>%</small>
                                    </h5>
                                    <p class="mb-0 small text-muted">Normal: < 5.7%</p>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Tambahkan bagian Pemeriksaan Lainnya di modal detail -->
            <div class="row">
               <div class="col-md-12">
                  <div class="card shadow-sm mb-3">
                     <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                           <i class="fas fa-stethoscope mr-1"></i> Pemeriksaan Lainnya
                        </h6>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="card mb-3 pemeriksaan-card">
                                 <div class="card-header py-2 px-3 bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                       <i class="fas fa-lungs mr-1"></i> Pemeriksaan Paru
                                    </h6>
                                 </div>
                                 <div class="card-body py-2 px-3">
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Suara Napas</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['suara_napas'] ?? 'Vesikuler' }}
                                       </div>
                                    </div>
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Ronkhi</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['ronkhi'] ?? 'Tidak ada' }}</div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-5 text-muted">Wheezing</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['wheezing'] ?? 'Tidak ada' }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card mb-3 pemeriksaan-card">
                                 <div class="card-header py-2 px-3 bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                       <i class="fas fa-heartbeat mr-1"></i> Pemeriksaan Jantung
                                    </h6>
                                 </div>
                                 <div class="card-body py-2 px-3">
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Suara Jantung</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['suara_jantung'] ?? 'Normal' }}
                                       </div>
                                    </div>
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Irama</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['irama_jantung'] ?? 'Teratur' }}
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-5 text-muted">Murmur</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['murmur'] ?? 'Tidak ada' }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="card mb-3 pemeriksaan-card">
                                 <div class="card-header py-2 px-3 bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                       <i class="fas fa-brain mr-1"></i> Pemeriksaan Neurologis
                                    </h6>
                                 </div>
                                 <div class="card-body py-2 px-3">
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Kesadaran</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['kesadaran'] ?? 'Compos Mentis' }}
                                       </div>
                                    </div>
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Refleks</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['refleks'] ?? 'Normal' }}</div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-5 text-muted">GCS</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['gcs'] ?? 'E4V5M6 (15)' }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="card mb-3 pemeriksaan-card">
                                 <div class="card-header py-2 px-3 bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                       <i class="fas fa-eye mr-1"></i> Pemeriksaan Mata
                                    </h6>
                                 </div>
                                 <div class="card-body py-2 px-3">
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Visus Mata Kanan</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['visus_od'] ?? '6/6' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                       <div class="col-md-5 text-muted">Visus Mata Kiri</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['visus_os'] ?? '6/6' }}</div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-5 text-muted">Buta Warna</div>
                                       <div class="col-md-7 font-weight-bold">{{ $p['buta_warna'] ?? 'Negatif' }}</div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Bagian Kesimpulan -->
            <div class="row">
               <div class="col-md-12">
                  <div class="card shadow-sm mb-3">
                     <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold text-primary">
                           <i class="fas fa-file-alt mr-1"></i> Kesimpulan
                        </h6>
                     </div>
                     <div class="card-body p-3">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="kesimpulan">Kesimpulan</label>
                                 <textarea class="form-control" name="kesimpulan" id="kesimpulan" rows="3"
                                    placeholder="Masukkan kesimpulan...">{{ $p['skilas'] }}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
               <i class="fas fa-times mr-1"></i> Tutup
            </button>
            <a href="{{ route('ilp.cetak', $p['id']) }}" target="_blank" class="btn btn-info">
               <i class="fas fa-print mr-1"></i> Cetak
            </a>
            <button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="modal"
               data-target="#modalHasil{{ $p['id'] }}">
               <i class="fas fa-edit mr-1"></i> Edit Data
            </button>
            <button type="button" class="btn btn-success send-wa-btn" data-id="{{ $p['id'] }}"
               data-no-hp="{{ $p['no_tlp'] ?? '' }}" data-nama="{{ $p['nama_pasien'] }}" data-no-rm="{{ $p['no_rm'] }}">
               <i class="fab fa-whatsapp mr-1"></i> Kirim WA
            </button>
         </div>
      </div>
   </div>
</div>

{{-- Modal Input Hasil Pemeriksaan --}}
<div class="modal fade" id="modalHasil{{ $p['id'] }}" tabindex="-1" role="dialog"
   aria-labelledby="modalHasilLabel{{ $p['id'] }}" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-light">
            <h5 class="modal-title text-success" id="modalHasilLabel{{ $p['id'] }}">
               <i class="fas fa-edit mr-2"></i> Edit Hasil Pemeriksaan ILP
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form action="{{ route('ilp.update', $p['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
               <div class="card shadow-sm mb-3">
                  <div class="card-header bg-light py-2">
                     <h6 class="m-0 font-weight-bold text-success">Informasi Pasien</h6>
                  </div>
                  <div class="card-body p-3">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="row mb-2">
                              <div class="col-md-4 text-muted">No Rawat</div>
                              <div class="col-md-8 font-weight-bold">{{ $p['no_rawat'] }}</div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-4 text-muted">No RM</div>
                              <div class="col-md-8 font-weight-bold">{{ $p['no_rm'] }}</div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-4 text-muted">Nama</div>
                              <div class="col-md-8 font-weight-bold">{{ $p['nama_pasien'] }}</div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label for="status_pemeriksaan">Status Pemeriksaan</label>
                              <select class="form-control" name="status_pemeriksaan" id="status_pemeriksaan">
                                 <option value="Menunggu" {{ $p['status']=='Menunggu' ? 'selected' : '' }}>
                                    Menunggu
                                 </option>
                                 <option value="Dalam Proses" {{ $p['status']=='Dalam Proses' ? 'selected' : '' }}>Dalam
                                    Proses</option>
                                 <option value="Selesai" {{ $p['status']=='Selesai' ? 'selected' : '' }}>
                                    Selesai
                                 </option>
                                 <option value="Sudah Diambil" {{ $p['status']=='Sudah Diambil' ? 'selected' : '' }}>
                                    Sudah Diambil</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card shadow-sm mb-3">
                  <div class="card-header bg-light py-2">
                     <h6 class="m-0 font-weight-bold text-success">Hasil Pemeriksaan</h6>
                  </div>
                  <div class="card-body p-3">
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="berat_badan">Berat Badan (kg)</label>
                              <input type="text" class="form-control" name="berat_badan" id="berat_badan"
                                 value="{{ $p['berat_badan'] }}">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="tinggi_badan">Tinggi Badan (cm)</label>
                              <input type="text" class="form-control" name="tinggi_badan" id="tinggi_badan"
                                 value="{{ $p['tinggi_badan'] }}">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="tekanan_darah">Tekanan Darah</label>
                              <div class="input-group">
                                 <input type="text" class="form-control" name="tekanan_darah" id="tekanan_darah"
                                    value="{{ $p['td'] }}" placeholder="120/80">
                                 <div class="input-group-append">
                                    <button type="button" class="btn btn-info" data-toggle="popover"
                                       data-placement="top" title="Interpretasi Tekanan Darah"
                                       data-content="<strong>Normal:</strong> <120/80 mmHg<br><strong>Pra-hipertensi:</strong> 120-139/80-89 mmHg<br><strong>Hipertensi tingkat 1:</strong> 140-159/90-99 mmHg<br><strong>Hipertensi tingkat 2:</strong> ≥160/≥100 mmHg<br><strong>Hipertensi Sistolik Terisolasi:</strong> >140/<90 mmHg">
                                       <i class="fas fa-info-circle"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row mb-3">
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="imt">Indeks Massa Tubuh (IMT)</label>
                              <div class="input-group">
                                 <input type="text" class="form-control" name="imt" id="imt" value="{{ $p['imt'] }}"
                                    readonly>
                                 <div class="input-group-append">
                                    <button type="button" class="btn btn-info" data-toggle="popover"
                                       data-placement="top" title="Interpretasi IMT"
                                       data-content="<strong>Kurus:</strong> <18.5<br><strong>Normal:</strong> 18.5-24.9<br><strong>Kelebihan Berat Badan:</strong> 25-29.9<br><strong>Obesitas:</strong> ≥30">
                                       <i class="fas fa-info-circle"></i>
                                    </button>
                                 </div>
                              </div>
                              <small class="form-text text-muted">IMT dihitung otomatis dari berat dan
                                 tinggi
                                 badan</small>
                           </div>
                        </div>
                        <div class="col-md-8">
                           <div class="form-group mb-0">
                              <label for="kesimpulan">Kesimpulan</label>
                              <textarea class="form-control" name="kesimpulan" id="kesimpulan" rows="3"
                                 placeholder="Masukkan kesimpulan...">{{ $p['skilas'] }}</textarea>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Hasil Laboratorium -->
               <div class="card shadow-sm mb-3">
                  <div class="card-header bg-light py-2">
                     <h6 class="m-0 font-weight-bold text-success">Hasil Pemeriksaan Laboratorium</h6>
                  </div>
                  <div class="card-body p-3">
                     <div class="row">
                        <div class="col-md-3">
                           <div class="card bg-light mb-3">
                              <div class="card-body py-2 px-3">
                                 <p class="mb-0 small text-muted">Hemoglobin</p>
                                 <h5 class="mb-0 font-weight-bold">{{ $p['hemoglobin'] ?? '-' }}
                                    <small>g/dL</small>
                                 </h5>
                                 <p class="mb-0 small text-muted">Normal: 12-16 g/dL</p>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="card bg-light mb-3">
                              <div class="card-body py-2 px-3">
                                 <p class="mb-0 small text-muted">Kolesterol Total</p>
                                 <h5 class="mb-0 font-weight-bold">{{ $p['kolesterol'] ?? '-' }}
                                    <small>mg/dL</small>
                                 </h5>
                                 <p class="mb-0 small text-muted">Normal: < 200 mg/dL</p>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="card bg-light mb-3">
                              <div class="card-body py-2 px-3">
                                 <p class="mb-0 small text-muted">Asam Urat</p>
                                 <h5 class="mb-0 font-weight-bold">{{ $p['asam_urat'] ?? '-' }}
                                    <small>mg/dL</small>
                                 </h5>
                                 <p class="mb-0 small text-muted">Normal: 3.5-7.2 mg/dL</p>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="card bg-light mb-3">
                              <div class="card-body py-2 px-3">
                                 <p class="mb-0 small text-muted">Gula Darah Puasa</p>
                                 <h5 class="mb-0 font-weight-bold">{{ $p['gula_darah_puasa'] ?? '-' }}
                                    <small>mg/dL</small>
                                 </h5>
                                 <p class="mb-0 small text-muted">Normal: 70-100 mg/dL</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="card mb-3">
                        <div class="card-header py-2 px-3 bg-light">
                           <h6 class="mb-0 font-weight-bold text-success">
                              <i class="fas fa-lungs mr-1"></i> Pemeriksaan Paru
                           </h6>
                        </div>
                        <div class="card-body py-2 px-3">
                           <div class="form-group">
                              <label for="suara_napas">Suara Napas</label>
                              <select class="form-control" name="suara_napas" id="suara_napas">
                                 <option value="Vesikuler" {{ ($p['suara_napas'] ?? '' )=='Vesikuler' ? 'selected' : ''
                                    }}>Vesikuler</option>
                                 <option value="Bronkial" {{ ($p['suara_napas'] ?? '' )=='Bronkial' ? 'selected' : ''
                                    }}>Bronkial</option>
                                 <option value="Bronchovesikuler" {{ ($p['suara_napas'] ?? '' )=='Bronchovesikuler'
                                    ? 'selected' : '' }}>Bronchovesikuler
                                 </option>
                              </select>
                           </div>
                           <div class="form-group">
                              <label for="ronkhi">Ronkhi</label>
                              <select class="form-control" name="ronkhi" id="ronkhi">
                                 <option value="Tidak ada" {{ ($p['ronkhi'] ?? '' )=='Tidak ada' ? 'selected' : '' }}>
                                    Tidak ada</option>
                                 <option value="Ada" {{ ($p['ronkhi'] ?? '' )=='Ada' ? 'selected' : '' }}>
                                    Ada</option>
                              </select>
                           </div>
                           <div class="form-group mb-0">
                              <label for="wheezing">Wheezing</label>
                              <select class="form-control" name="wheezing" id="wheezing">
                                 <option value="Tidak ada" {{ ($p['wheezing'] ?? '' )=='Tidak ada' ? 'selected' : '' }}>
                                    Tidak ada</option>
                                 <option value="Ada" {{ ($p['wheezing'] ?? '' )=='Ada' ? 'selected' : '' }}>
                                    Ada</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="card mb-3">
                        <div class="card-header py-2 px-3 bg-light">
                           <h6 class="mb-0 font-weight-bold text-success">
                              <i class="fas fa-heartbeat mr-1"></i> Pemeriksaan Jantung
                           </h6>
                        </div>
                        <div class="card-body py-2 px-3">
                           <div class="form-group">
                              <label for="suara_jantung">Suara Jantung</label>
                              <select class="form-control" name="suara_jantung" id="suara_jantung">
                                 <option value="Normal" {{ ($p['suara_jantung'] ?? '' )=='Normal' ? 'selected' : '' }}>
                                    Normal</option>
                                 <option value="Abnormal" {{ ($p['suara_jantung'] ?? '' )=='Abnormal' ? 'selected' : ''
                                    }}>Abnormal</option>
                              </select>
                           </div>
                           <div class="form-group">
                              <label for="irama_jantung">Irama</label>
                              <select class="form-control" name="irama_jantung" id="irama_jantung">
                                 <option value="Teratur" {{ ($p['irama_jantung'] ?? '' )=='Teratur' ? 'selected' : ''
                                    }}>Teratur</option>
                                 <option value="Tidak Teratur" {{ ($p['irama_jantung'] ?? '' )=='Tidak Teratur'
                                    ? 'selected' : '' }}>Tidak Teratur</option>
                              </select>
                           </div>
                           <div class="form-group mb-0">
                              <label for="murmur">Murmur</label>
                              <select class="form-control" name="murmur" id="murmur">
                                 <option value="Tidak ada" {{ ($p['murmur'] ?? '' )=='Tidak ada' ? 'selected' : '' }}>
                                    Tidak ada</option>
                                 <option value="Ada" {{ ($p['murmur'] ?? '' )=='Ada' ? 'selected' : '' }}>
                                    Ada</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="card mb-3 pemeriksaan-card">
                        <div class="card-header py-2 px-3 bg-light">
                           <h6 class="mb-0 font-weight-bold text-primary">
                              <i class="fas fa-brain mr-1"></i> Pemeriksaan Neurologis
                           </h6>
                        </div>
                        <div class="card-body py-2 px-3">
                           <div class="row mb-2">
                              <div class="col-md-5 text-muted">Kesadaran</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['kesadaran'] ?? 'Compos Mentis' }}</div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-5 text-muted">Refleks</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['refleks'] ?? 'Normal' }}</div>
                           </div>
                           <div class="row">
                              <div class="col-md-5 text-muted">GCS</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['gcs'] ?? 'E4V5M6 (15)' }}</div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="card mb-3 pemeriksaan-card">
                        <div class="card-header py-2 px-3 bg-light">
                           <h6 class="mb-0 font-weight-bold text-primary">
                              <i class="fas fa-eye mr-1"></i> Pemeriksaan Mata
                           </h6>
                        </div>
                        <div class="card-body py-2 px-3">
                           <div class="row mb-2">
                              <div class="col-md-5 text-muted">Visus Mata Kanan</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['visus_od'] ?? '6/6' }}</div>
                           </div>
                           <div class="row mb-2">
                              <div class="col-md-5 text-muted">Visus Mata Kiri</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['visus_os'] ?? '6/6' }}</div>
                           </div>
                           <div class="row">
                              <div class="col-md-5 text-muted">Buta Warna</div>
                              <div class="col-md-7 font-weight-bold">{{ $p['buta_warna'] ?? 'Negatif' }}</div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  <i class="fas fa-times mr-1"></i> Batal
               </button>
               <button type="submit" class="btn btn-success">
                  <i class="fas fa-save mr-1"></i> Simpan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
@endforeach
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="icon" type="image/png" href="{{ asset('epasien/YASKI.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
   /* Preloader */
   .preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.9);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: all 0.5s ease;
   }

   .preloader .loader {
      text-align: center;
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
   }

   .preloader .spinner-border {
      width: 3rem;
      height: 3rem;
   }

   .preloader p {
      color: #4a5568;
      font-weight: 500;
      margin-top: 1rem;
   }

   .preloader.fade-out {
      opacity: 0;
      visibility: hidden;
   }

   /* Styling umum */
   body {
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      background-color: #f8f9fa;
   }

   .card {
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      border: none;
      overflow: hidden;
      margin-bottom: 1rem;
   }

   .card-header {
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 1rem 1.25rem;
      background-color: #fff;
   }

   .card-title {
      font-weight: 600;
      margin-bottom: 0;
   }

   .card-body {
      padding: 1.25rem;
   }

   .text-primary {
      color: #4361ee !important;
   }

   .text-success {
      color: #10b981 !important;
   }

   .bg-primary {
      background-color: #4361ee !important;
   }

   .bg-success {
      background-color: #10b981 !important;
   }

   .btn-primary {
      background-color: #4361ee;
      border-color: #4361ee;
   }

   .btn-primary:hover {
      background-color: #3a56d4;
      border-color: #3a56d4;
   }

   .btn-success {
      background-color: #10b981;
      border-color: #10b981;
   }

   .btn-success:hover {
      background-color: #0ea271;
      border-color: #0ea271;
   }

   .btn-outline-primary {
      color: #4361ee;
      border-color: #4361ee;
   }

   .btn-outline-primary:hover {
      background-color: #4361ee;
      border-color: #4361ee;
   }

   .btn-outline-secondary {
      color: #6c757d;
      border-color: #6c757d;
   }

   .btn-outline-secondary:hover {
      background-color: #6c757d;
      border-color: #6c757d;
      color: #fff;
   }

   .btn-sm {
      padding: 0.25rem 0.75rem;
      font-size: 0.875rem;
   }

   /* Form controls */
   .form-control {
      height: calc(2.25rem + 2px);
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      font-size: 0.95rem;
      padding: 0.375rem 0.75rem;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
   }

   .form-control:focus {
      border-color: #4361ee;
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
   }

   .input-group-text {
      background-color: #f8f9fa;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
   }

   /* Select2 */
   .select2-container--default .select2-selection--single {
      height: calc(2.25rem + 2px);
      border-radius: 6px;
      border: 1px solid #e2e8f0;
   }

   .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: calc(2.25rem + 2px);
      padding-left: 0.75rem;
      color: #495057;
   }

   .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: calc(2.25rem + 2px);
      right: 0.5rem;
   }

   .select2-dropdown {
      border-color: #e2e8f0;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 9999;
   }

   .select2-container--open .select2-dropdown {
      margin-top: 3px;
   }

   .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #4361ee;
   }

   .select2-container--default .select2-search--dropdown .select2-search__field {
      border-radius: 4px;
      border: 1px solid #e2e8f0;
      padding: 6px 10px;
   }

   .select2-container--default .select2-search--dropdown .select2-search__field:focus {
      outline: none;
      border-color: #4361ee;
   }

   .select2-results__option {
      padding: 8px 12px;
   }

   /* Table */
   .table {
      color: #333;
   }

   .table th {
      font-weight: 600;
      border-top: none;
      border-bottom: 2px solid #e2e8f0;
      padding: 0.75rem;
      background-color: #f8f9fa;
   }

   .table td {
      padding: 0.75rem;
      vertical-align: middle;
      border-top: 1px solid #e2e8f0;
   }

   .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.02);
   }

   .table-hover tbody tr:hover {
      background-color: rgba(0, 0, 0, 0.04);
   }

   /* Badges */
   .badge {
      padding: 0.35em 0.65em;
      font-weight: 500;
      border-radius: 4px;
   }

   .badge-warning {
      background-color: #fef3c7;
      color: #92400e;
   }

   .badge-info {
      background-color: #e0f2fe;
      color: #0369a1;
   }

   .badge-success {
      background-color: #dcfce7;
      color: #166534;
   }

   .badge-secondary {
      background-color: #f1f5f9;
      color: #475569;
   }

   /* Dropdown */
   .dropdown-menu {
      border: none;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 0.5rem 0;
   }

   .dropdown-item {
      padding: 0.5rem 1.25rem;
      color: #333;
   }

   .dropdown-item:hover,
   .dropdown-item:focus {
      background-color: #f8f9fa;
      color: #16181b;
   }

   /* Breadcrumb */
   .breadcrumb {
      padding: 0;
      margin: 0;
      background-color: transparent;
      font-size: 0.875rem;
   }

   .breadcrumb-item+.breadcrumb-item::before {
      content: "›";
      color: #6c757d;
   }

   .breadcrumb-item.active {
      color: #6c757d;
   }

   /* Loading indicators */
   .search-loading,
   .posyandu-loading {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      display: none;
      color: #4361ee;
      font-size: 0.875rem;
      z-index: 10;
   }

   .posyandu-loading {
      right: 30px;
   }

   /* Filter badges */
   .filter-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
   }

   .filter-badges .badge {
      display: flex;
      align-items: center;
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
   }

   .filter-badges .badge a {
      margin-left: 0.5rem;
      color: inherit;
      opacity: 0.7;
   }

   .filter-badges .badge a:hover {
      opacity: 1;
      text-decoration: none;
   }

   /* Autocomplete */
   .ui-autocomplete {
      max-height: 300px;
      overflow-y: auto;
      border-radius: 6px;
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 0.5rem 0;
      z-index: 9999 !important;
   }

   .ui-menu-item {
      padding: 0;
   }

   .ui-menu-item-wrapper {
      padding: 0.5rem 1rem;
      color: #333;
   }

   .ui-menu-item-wrapper.ui-state-active {
      background-color: #4361ee;
      color: white;
      border: none;
      margin: 0;
   }

   .highlight-match {
      font-weight: 600;
      background-color: #fef9c3;
      color: #854d0e;
      padding: 0 2px;
      border-radius: 2px;
   }

   /* Tombol Tampilkan Semua Kolom */
   #toggleColumns {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      font-weight: 500;
   }

   #toggleColumns:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
   }

   #toggleColumns:active {
      transform: translateY(0);
   }

   #toggleColumns::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      opacity: 0;
      transition: opacity 0.3s ease;
   }

   #toggleColumns:hover::after {
      opacity: 1;
   }

   /* Perbaikan untuk modal dan dropdown */
   .modal-open .select2-container {
      z-index: 1040;
   }

   .dropdown-menu {
      z-index: 1050;
   }

   .dropdown-toggle::after {
      margin-left: 0.5em;
   }

   /* Responsive */
   @media (max-width: 768px) {
      .card-body {
         padding: 1rem;
      }

      .btn-sm {
         padding: 0.25rem 0.5rem;
         font-size: 0.75rem;
      }

      .table th,
      .table td {
         padding: 0.5rem;
      }
   }

   /* Perbaikan tampilan modal */
   .modal-body .card {
      border: 1px solid rgba(0, 0, 0, .125);
      margin-bottom: 1rem;
   }

   .modal-body .card-header {
      background-color: #f8f9fa;
      padding: 0.5rem 1rem;
   }

   .modal-body .card-body {
      padding: 0.75rem 1rem;
   }

   /* Perbaikan tampilan teks */
   .modal-body .text-muted {
      font-size: 0.9rem;
   }

   .modal-body .font-weight-bold {
      font-size: 0.95rem;
   }

   /* Perbaikan tampilan ikon */
   .modal-body .fas {
      width: 16px;
      text-align: center;
      margin-right: 5px;
   }

   /* Perbaikan tampilan badge */
   .badge {
      font-weight: 500;
      padding: 0.35em 0.65em;
   }

   /* Perbaikan tampilan preloader */
   .preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      z-index: 9999;
      display: flex;
      justify-content: center;
      align-items: center;
   }

   .loader {
      text-align: center;
   }

   /* Perbaikan tampilan pemeriksaan lainnya */
   .pemeriksaan-card {
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
      transition: all 0.3s ease;
   }

   .pemeriksaan-card:hover {
      box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
   }

   .pemeriksaan-card .card-header {
      border-bottom: 1px solid rgba(0, 0, 0, .05);
      background-color: #f8f9fa;
   }

   .pemeriksaan-card .card-header h6 {
      font-size: 1rem;
      color: #4e73df;
   }

   .pemeriksaan-card .card-body {
      padding: 1rem;
   }

   .pemeriksaan-card .row {
      margin-bottom: 0.5rem;
   }

   .pemeriksaan-card .row:last-child {
      margin-bottom: 0;
   }

   .pemeriksaan-card .text-muted {
      font-size: 0.85rem;
   }

   .pemeriksaan-card .font-weight-bold {
      font-size: 0.9rem;
   }

   /* Perbaikan tampilan modal detail */
   .modal-detail .modal-body {
      padding: 1.25rem;
   }

   .modal-detail .card-header {
      padding: 0.75rem 1rem;
   }

   .modal-detail .card-body {
      padding: 1rem;
   }

   /* Perbaikan tampilan pemeriksaan lainnya yang terduplikasi */
   .pemeriksaan-section {
      margin-bottom: 1.5rem;
   }

   .pemeriksaan-section .card-header {
      background-color: #f1f5fb;
      border-bottom: 1px solid rgba(0, 0, 0, .05);
   }

   .pemeriksaan-section .card-header h6 {
      font-size: 1.1rem;
      color: #3a57e8;
   }

   .pemeriksaan-section .card-body {
      padding: 1.25rem;
   }

   /* Perbaikan tampilan kolom */
   .pemeriksaan-item {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
      border-bottom: 1px solid rgba(0, 0, 0, .05);
   }

   .pemeriksaan-item:last-child {
      border-bottom: none;
   }

   .pemeriksaan-item .label {
      color: #6c757d;
      font-size: 0.9rem;
   }

   .pemeriksaan-item .value {
      font-weight: 600;
      font-size: 0.95rem;
      color: #212529;
   }

   /* Perbaikan untuk masalah aria-hidden */
   .dropdown-wrapper[aria-hidden="true"] {
      display: block !important;
   }

   /* Perbaikan untuk select2 */
   .select2-container--default .select2-selection--single:focus {
      outline: none;
      border-color: #4361ee;
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
   }

   .select2-container--default .select2-selection--single .select2-selection__rendered:focus {
      outline: none;
   }

   /* Perbaikan untuk modal */
   .modal-backdrop {
      z-index: 1040;
   }

   .modal {
      z-index: 1050;
   }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
   // Definisi bahasa Indonesia untuk DataTables
const indonesianLanguage = {
    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
    "infoThousands": ".",
    "lengthMenu": "Tampilkan _MENU_ entri",
    "loadingRecords": "Sedang memuat...",
    "processing": "Sedang memproses...",
    "search": "Cari:",
    "zeroRecords": "Tidak ditemukan data yang sesuai",
    "thousands": ".",
    "paginate": {
        "first": "Pertama",
        "last": "Terakhir",
        "next": "Selanjutnya",
        "previous": "Sebelumnya"
    },
    "aria": {
        "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
        "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
    },
    "autoFill": {
        "cancel": "Batalkan",
        "fill": "Isi semua sel dengan <i>%d<\/i>",
        "fillHorizontal": "Isi sel secara horizontal",
        "fillVertical": "Isi sel secara vertikal"
    },
    "buttons": {
        "collection": "Koleksi <span class='ui-button-icon-primary ui-icon ui-icon-triangle-1-s'\/>",
        "colvis": "Visibilitas Kolom",
        "colvisRestore": "Kembalikan visibilitas",
        "copy": "Salin",
        "copySuccess": {
            "1": "1 baris disalin ke papan klip",
            "_": "%d baris disalin ke papan klip"
        },
        "copyTitle": "Salin ke Papan klip",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Tampilkan semua baris",
            "_": "Tampilkan %d baris"
        },
        "pdf": "PDF",
        "print": "Cetak",
        "copyKeys": "Tekan ctrl atau u2318 + C untuk menyalin tabel ke papan klip.<br \/><br \/>Untuk membatalkan, klik pesan ini atau tekan esc."
    },
    "searchBuilder": {
        "add": "Tambah Kondisi",
        "button": {
            "0": "Cari Builder",
            "_": "Cari Builder (%d)"
        },
        "clearAll": "Bersihkan Semua",
        "condition": "Kondisi",
        "data": "Data",
        "deleteTitle": "Hapus filter",
        "leftTitle": "Ke Kiri",
        "logicAnd": "Dan",
        "logicOr": "Atau",
        "rightTitle": "Ke Kanan",
        "title": {
            "0": "Cari Builder",
            "_": "Cari Builder (%d)"
        },
        "value": "Nilai",
        "conditions": {
            "date": {
                "after": "Setelah",
                "before": "Sebelum",
                "between": "Diantara",
                "empty": "Kosong",
                "equals": "Sama dengan",
                "not": "Tidak sama",
                "notBetween": "Tidak diantara",
                "notEmpty": "Tidak kosong"
            },
            "number": {
                "between": "Diantara",
                "empty": "Kosong",
                "equals": "Sama dengan",
                "gt": "Lebih besar dari",
                "gte": "Lebih besar atau sama dengan",
                "lt": "Lebih kecil dari",
                "lte": "Lebih kecil atau sama dengan",
                "not": "Tidak sama",
                "notBetween": "Tidak diantara",
                "notEmpty": "Tidak kosong"
            },
            "string": {
                "contains": "Berisi",
                "empty": "Kosong",
                "endsWith": "Diakhiri dengan",
                "equals": "Sama Dengan",
                "not": "Tidak sama",
                "notEmpty": "Tidak kosong",
                "startsWith": "Diawali dengan"
            },
            "array": {
                "equals": "Sama dengan",
                "empty": "Kosong",
                "contains": "Berisi",
                "not": "Tidak",
                "notEmpty": "Tidak kosong",
                "without": "Tanpa"
            }
        }
    },
    "searchPanes": {
        "clearMessage": "Bersihkan Semua",
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "title": "Filter Aktif - %d",
        "collapse": {
            "0": "Panel Pencarian",
            "_": "Panel Pencarian (%d)"
        },
        "emptyPanes": "Tidak Ada Panel Pencarian",
        "loadMessage": "Memuat Panel Pencarian"
    },
    "select": {
        "cells": {
            "1": "1 sel terpilih",
            "_": "%d sel terpilih"
        },
        "columns": {
            "1": "1 kolom terpilih",
            "_": "%d kolom terpilih"
        },
        "rows": {
            "1": "1 baris terpilih",
            "_": "%d baris terpilih"
        }
    },
    "datetime": {
        "previous": "Sebelumnya",
        "next": "Selanjutnya",
        "hours": "Jam",
        "minutes": "Menit",
        "seconds": "Detik",
        "unknown": "-",
        "amPm": [
            "am",
            "pm"
        ],
        "weekdays": [
            "Min",
            "Sen",
            "Sel",
            "Rab",
            "Kam",
            "Jum",
            "Sab"
        ],
        "months": [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ]
    },
    "editor": {
        "close": "Tutup",
        "create": {
            "button": "Tambah",
            "submit": "Tambah",
            "title": "Tambah data baru"
        },
        "edit": {
            "button": "Ubah",
            "submit": "Perbarui",
            "title": "Ubah data"
        },
        "error": {
            "system": "Terjadi kesalahan sistem"
        },
        "multi": {
            "info": "Item yang dipilih berisi nilai yang berbeda untuk input ini. Untuk mengedit dan mengatur semua item untuk input ini ke nilai yang sama, klik atau ketuk di sini, jika tidak maka mereka akan mempertahankan nilai masing-masing.",
            "noMulti": "Input ini dapat diubah satu per satu, tetapi bukan sebagai bagian dari grup.",
            "restore": "Batalkan perubahan",
            "title": "Beberapa nilai"
        },
        "remove": {
            "button": "Hapus",
            "confirm": {
                "_": "Apakah Anda yakin untuk menghapus %d baris?",
                "1": "Apakah Anda yakin untuk menghapus 1 baris?"
            },
            "submit": "Hapus",
            "title": "Hapus data"
        }
    }
};

$(document).ready(function() {
    // Sembunyikan preloader setelah halaman dimuat
    $('.preloader').fadeOut(500);
    
    // Inisialisasi Select2 untuk filter posyandu
    $('.select2-posyandu').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Inisialisasi DataTable
    var table = $('#table1').DataTable({
        responsive: true,
        language: indonesianLanguage,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Semua"]
        ],
        columnDefs: [
            { responsivePriority: 1, targets: [0, 1, 2, 3, 9] },
            { responsivePriority: 2, targets: [4, 5, 6, 7, 8] }
        ]
    });
    
    // Auto searching untuk input pencarian
    $('#searchInput').on('keyup', function() {
        const searchValue = $(this).val();
        
        // Tampilkan indikator loading
        $('.search-loading').fadeIn(200);
        
        // Terapkan pencarian
        table.search(searchValue).draw();
        
        // Sembunyikan indikator loading setelah pencarian selesai
        setTimeout(function() {
            $('.search-loading').fadeOut(200);
        }, 300);
    });
    
    // Auto filtering untuk posyandu
    $('#filter_posyandu').on('change', function() {
        const posyanduValue = $(this).val();
        
        // Tampilkan indikator loading
        $('.posyandu-loading').fadeIn(200);
        
        // Hapus filter posyandu yang ada sebelumnya
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
            return !(fn.name && fn.name === 'posyanduFilter');
        });
        
        if (posyanduValue) {
            // Tambahkan filter baru untuk posyandu
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Tambahkan properti name untuk identifikasi
                this.name = 'posyanduFilter';
                
                // Ambil nilai posyandu dari atribut data-posyandu pada baris
                const rowPosyandu = $(table.row(dataIndex).node()).data('posyandu');
                
                // Jika tidak ada filter atau nilai posyandu cocok, tampilkan baris
                return !posyanduValue || rowPosyandu === posyanduValue;
            });
            
            // Tambahkan badge filter posyandu
            $('.filter-badges').append(
                '<span class="badge badge-info posyandu-filter-badge">' +
                '<i class="fas fa-clinic-medical mr-1"></i> Posyandu: ' + posyanduValue +
                ' <a href="#" class="text-white ml-2 clear-posyandu-filter"><i class="fas fa-times-circle"></i></a>' +
                '</span>'
            );
        } else {
            // Jika filter dihapus, hapus badge
            $('.posyandu-filter-badge').remove();
        }
        
        // Redraw tabel dengan filter baru
        table.draw();
        
        // Sembunyikan indikator loading
        $('.posyandu-loading').fadeOut(200);
    });

    // Fungsi untuk memformat tanggal dari YYYY-MM-DD ke DD-MM-YYYY
    function formatDateDisplay(dateString) {
        if (!dateString) return '';
        
        try {
            const parts = dateString.split('-');
            if (parts.length !== 3) return dateString;
            
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        } catch (e) {
            console.error('Error formatting date:', e);
            return dateString;
        }
    }
    
    // Fungsi untuk memformat tanggal dari DD-MM-YYYY ke YYYY-MM-DD
    function formatDateValue(dateString) {
        if (!dateString) return '';
        
        try {
            const parts = dateString.split('-');
            if (parts.length !== 3) return dateString;
            
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        } catch (e) {
            console.error('Error formatting date:', e);
            return dateString;
        }
    }

    // Auto searching untuk tanggal
    $('#dari, #sampai').on('change', function() {
        const dariValue = $('#dari').val();
        const sampaiValue = $('#sampai').val();
        
        console.log('Filter tanggal diubah - dari:', dariValue, 'sampai:', sampaiValue);
        
        // Tampilkan indikator loading
        $('.search-loading').fadeIn(200);
        
        // Hapus semua filter tanggal yang ada sebelumnya
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
            return !(fn.name && fn.name === 'dateFilter');
        });
        
        if (dariValue || sampaiValue) {
            // Tambahkan filter baru untuk tanggal
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Tambahkan properti name untuk identifikasi
                this.name = 'dateFilter';
                
                // Kolom tanggal adalah kolom 1 (indeks 0-based)
                const tanggalStr = data[1];
                if (!tanggalStr) return true;
                
                try {
                    // Format tanggal di tabel adalah DD-MM-YYYY
                    const dateParts = tanggalStr.split('-');
                    if (dateParts.length !== 3) return true;
                    
                    // Konversi tanggal tabel ke objek Date
                    // Format: DD-MM-YYYY -> YYYY-MM-DD untuk objek Date
                    const day = parseInt(dateParts[0], 10);
                    const month = parseInt(dateParts[1], 10) - 1; // Bulan dalam JavaScript dimulai dari 0
                    const year = parseInt(dateParts[2], 10);
                    
                    const rowDate = new Date(year, month, day);
                    
                    // Konversi tanggal filter ke objek Date
                    let dariDate = null;
                    let sampaiDate = null;
                    
                    if (dariValue) {
                        // Format input date adalah YYYY-MM-DD
                        dariDate = new Date(dariValue + 'T00:00:00');
                        // Set waktu ke 00:00:00
                        dariDate.setHours(0, 0, 0, 0);
                    }
                    
                    if (sampaiValue) {
                        // Format input date adalah YYYY-MM-DD
                        sampaiDate = new Date(sampaiValue + 'T00:00:00');
                        // Set waktu ke 23:59:59
                        sampaiDate.setHours(23, 59, 59, 999);
                    }
                    
                    // Set waktu rowDate ke 12:00:00 untuk menghindari masalah zona waktu
                    rowDate.setHours(12, 0, 0, 0);
                    
                    // Bandingkan tanggal
                    let result = true;
                    
                    if (dariDate && sampaiDate) {
                        result = rowDate >= dariDate && rowDate <= sampaiDate;
                    } else if (dariDate) {
                        result = rowDate >= dariDate;
                    } else if (sampaiDate) {
                        result = rowDate <= sampaiDate;
                    }
                    
                    console.log('Comparing dates:', {
                        rowDateStr: tanggalStr,
                        rowDate: rowDate.toISOString(),
                        dariValue: dariValue,
                        sampaiValue: sampaiValue,
                        dariDate: dariDate ? dariDate.toISOString() : null,
                        sampaiDate: sampaiDate ? sampaiDate.toISOString() : null,
                        result: result
                    });
                    
                    return result;
                } catch (e) {
                    console.error('Error filtering date:', e);
                    return true;
                }
            });
            
            // Tambahkan badge filter tanggal
            updateDateFilterBadges(dariValue, sampaiValue);
        } else {
            // Jika kedua input kosong, hapus badge
            removeDateFilterBadges();
        }
        
        // Redraw tabel dengan filter baru
        table.draw();
        
        // Sembunyikan indikator loading
        $('.search-loading').fadeOut(200);
    });

    // Fungsi untuk memperbarui badge filter tanggal
    function updateDateFilterBadges(dariValue, sampaiValue) {
        // Hapus badge tanggal yang ada
        removeDateFilterBadges();
        
        // Tambahkan badge baru jika ada filter aktif
        if (dariValue || sampaiValue) {
            let badgeText = '<i class="fas fa-calendar-alt mr-1"></i> Tanggal: ';
            
            if (dariValue && sampaiValue) {
                badgeText += formatDateDisplay(dariValue) + ' s/d ' + formatDateDisplay(sampaiValue);
            } else if (dariValue) {
                badgeText += 'Dari ' + formatDateDisplay(dariValue);
            } else if (sampaiValue) {
                badgeText += 'Sampai ' + formatDateDisplay(sampaiValue);
            }
            
            $('.filter-badges').append(
                '<span class="badge badge-info date-filter-badge">' +
                badgeText +
                ' <a href="#" class="text-white ml-2 clear-date-filter"><i class="fas fa-times-circle"></i></a>' +
                '</span>'
            );
        }
    }

    // Fungsi untuk menghapus badge filter tanggal
    function removeDateFilterBadges() {
        $('.date-filter-badge').remove();
    }

    // Hapus filter posyandu saat klik ikon hapus pada badge
    $(document).on('click', '.clear-posyandu-filter', function(e) {
        e.preventDefault();
        
        // Reset nilai select posyandu
        $('.select2-posyandu').val('').trigger('change');
        
        // Hapus filter posyandu
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
            return !(fn.name && fn.name === 'posyanduFilter');
        });
        
        // Redraw tabel
        table.draw();
        
        // Hapus badge
        $('.posyandu-filter-badge').remove();
    });

    // Hapus filter tanggal saat klik ikon hapus pada badge
    $(document).on('click', '.clear-date-filter', function(e) {
        e.preventDefault();
        
        // Reset nilai input tanggal
        $('#dari').val('');
        $('#sampai').val('');
        
        // Hapus filter tanggal
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
            return !(fn.name && fn.name === 'dateFilter');
        });
        
        // Redraw tabel
        table.draw();
        
        // Hapus badge
        removeDateFilterBadges();
    });

    // Reset semua filter
    $('#resetAllFilters').on('click', function(e) {
        e.preventDefault();
        
        console.log('Reset semua filter');
        
        // Tampilkan indikator loading
        $('.search-loading, .posyandu-loading').fadeIn(200);
        
        // Reset filter posyandu
        $('.select2-posyandu').val('').trigger('change');
        
        // Reset filter tanggal
        $('#dari').val('');
        $('#sampai').val('');
        
        // Reset filter pencarian
        $('#searchInput').val('');
        
        // Hapus semua filter kustom dari DataTable
        $.fn.dataTable.ext.search = [];
        
        // Reset pencarian dan redraw tabel
        table.search('').columns().search('').draw();
        
        // Hapus semua badge
        $('.filter-badges').empty();
        
        // Sembunyikan indikator loading
        $('.search-loading, .posyandu-loading').fadeOut(200);
    });
    
    // Toggle kolom tambahan
    $('#toggleColumns').on('click', function() {
        const allColumns = table.columns();
        const allVisible = allColumns.visible().reduce((a, b) => a && b, true);
        
        if (allVisible) {
            // Sembunyikan beberapa kolom
            table.columns([4, 5, 6, 7]).visible(false);
            $(this).html('<i class="fas fa-columns mr-1"></i> Tampilkan Semua Kolom');
        } else {
            // Tampilkan semua kolom
            allColumns.visible(true);
            $(this).html('<i class="fas fa-columns mr-1"></i> Tampilkan Kolom Default');
        }
    });
    
    // Reset kolom ke tampilan default
    $('#resetColumns').on('click', function() {
        // Tampilkan semua kolom
        table.columns().visible(true);
        $('#toggleColumns').html('<i class="fas fa-columns mr-1"></i> Tampilkan Semua Kolom');
    });
    
    // Hitung IMT otomatis saat berat badan atau tinggi badan diubah
    $('input[name="berat_badan"], input[name="tinggi_badan"]').on('input', function() {
        const bb = parseFloat($('input[name="berat_badan"]').val()) || 0;
        const tb = parseFloat($('input[name="tinggi_badan"]').val()) || 0;
        
        if (bb > 0 && tb > 0) {
            // Konversi tinggi badan dari cm ke m
            const tbMeter = tb / 100;
            // Hitung IMT
            const imt = (bb / (tbMeter * tbMeter)).toFixed(2);
            // Tampilkan hasil
            $('input[name="imt"]').val(imt);
        } else {
            $('input[name="imt"]').val('');
        }
    });
});
</script>

<script>
   // Inisialisasi popover untuk tombol info tekanan darah
$(document).ready(function() {
    // Aktifkan semua popover
    $('[data-toggle="popover"]').popover({
        html: true,
        trigger: 'hover',
        container: 'body'
    });
    
    // Tambahkan event handler untuk modal
    $('.modal').on('shown.bs.modal', function() {
        // Reinisialisasi popover dalam modal
        $(this).find('[data-toggle="popover"]').popover({
            html: true,
            trigger: 'hover',
            container: 'body'
        });
    });
    
    // Perbaikan untuk modal yang hang
    $('.modal').on('hidden.bs.modal', function() {
        // Hapus backdrop yang mungkin tertinggal
        $('.modal-backdrop').remove();
        // Pastikan body tidak memiliki class modal-open
        $('body').removeClass('modal-open');
        // Hapus inline style pada body
        $('body').removeAttr('style');
    });
});
</script>

<script>
   // Fungsi untuk mengirim WhatsApp
$(document).on('click', '.send-wa-btn', function() {
    const noHP = $(this).data('no-hp');
    const nama = $(this).data('nama');
    const noRM = $(this).data('no-rm');
    const patientId = $(this).data('id');
    
    if (!noHP || noHP === '') {
        Swal.fire({
            title: 'Nomor HP Tidak Tersedia',
            text: 'Pasien tidak memiliki nomor HP yang terdaftar. Silakan update data pasien terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Format nomor HP (hapus 0 di depan dan tambahkan 62)
    let formattedHP = noHP.replace(/^0/, '62');
    if (!formattedHP.startsWith('62')) {
        formattedHP = '62' + formattedHP;
    }
    
    // Tampilkan modal untuk memilih jenis pesan
    Swal.fire({
        title: 'Kirim Hasil Pemeriksaan',
        html: `
            <div class="text-left mb-4">
                <p class="mb-2">Kirim hasil pemeriksaan ke:</p>
                <p class="font-weight-bold mb-0">${nama} (${noRM})</p>
                <p class="text-muted">No. HP: ${noHP}</p>
            </div>
            <div class="form-group text-left">
                <label for="messageType">Pilih Jenis Pesan:</label>
                <select class="form-control" id="messageType">
                    <option value="text">Ringkasan Hasil (Text)</option>
                    <option value="pdf">File PDF Hasil Pemeriksaan</option>
                </select>
            </div>
            <div class="form-group text-left" id="customMessageContainer">
                <label for="customMessage">Pesan Tambahan (Opsional):</label>
                <input type="text" class="form-control" id="customMessage" placeholder="Masukkan pesan tambahan di sini...">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            return {
                messageType: document.getElementById('messageType').value,
                customMessage: document.getElementById('customMessage').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { messageType, customMessage } = result.value;
            
            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyiapkan pesan WhatsApp',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kirim permintaan ke endpoint baru
            $.ajax({
                url: '{{ route("ilp.send-whatsapp") }}',
                type: 'POST',
                data: {
                    id: patientId,
                    phone: formattedHP,
                    type: messageType,
                    message: customMessage,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Buka WhatsApp dengan pesan yang sudah disiapkan
                        window.open(response.whatsapp_url, '_blank');
                        Swal.close();
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: response.message || 'Gagal mengirim pesan',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengirim pesan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        }
    });
});
</script>
@stop