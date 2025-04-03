@extends('layouts.app')

@section('content')
<div class="container-fluid">
   <!-- Page Heading -->
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Antrian Poliklinik</h1>
      <div>
         <button class="btn btn-primary" onclick="refreshAntrian()">
            <i class="fas fa-sync-alt"></i> Refresh
         </button>
         <span id="last-update" class="ml-2 text-sm text-gray-600">
            Terakhir diperbarui: {{ now()->format('H:i:s') }}
         </span>
      </div>
   </div>

   <div class="row">
      <div class="col-lg-12">
         <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
               <h6 class="m-0 font-weight-bold text-primary">Daftar Antrian Pasien</h6>
               <div class="dropdown no-arrow">
                  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                     aria-haspopup="true" aria-expanded="false">
                     <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                     aria-labelledby="dropdownMenuLink">
                     <div class="dropdown-header">Pilihan:</div>
                     <a class="dropdown-item" href="#" id="printAntrian">
                        <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                        Cetak Antrian
                     </a>
                     <a class="dropdown-item" href="#" id="exportExcel">
                        <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export Excel
                     </a>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <div class="row mb-4">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="filterPoli">Filter Poliklinik:</label>
                        <select class="form-control" id="filterPoli" onchange="filterAntrian()">
                           <option value="">Semua Poliklinik</option>
                           <!-- Opsi poliklinik akan diisi dari data -->
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="searchInput">Pencarian:</label>
                        <input type="text" class="form-control" id="searchInput"
                           placeholder="Cari berdasarkan No. RM/Nama" onkeyup="filterAntrian()">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="tanggalAntrian">Tanggal:</label>
                        <input type="date" class="form-control" id="tanggalAntrian" value="{{ date('Y-m-d') }}"
                           onchange="filterAntrian()">
                     </div>
                  </div>
               </div>

               <div class="table-responsive">
                  <table class="table table-bordered table-hover" id="antrianTable" width="100%" cellspacing="0">
                     <thead class="thead-dark">
                        <tr>
                           <th class="text-center" width="5%">No.</th>
                           <th class="text-center" width="10%">No. Antrian</th>
                           <th class="text-center" width="15%">No. Rawat</th>
                           <th width="20%">Nama Pasien</th>
                           <th class="text-center" width="15%">No. RM</th>
                           <th width="20%">Poliklinik</th>
                           <th class="text-center" width="10%">Status</th>
                           <th class="text-center" width="5%">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="antrianBody">
                        <!-- Data antrian akan dimuat di sini -->
                     </tbody>
                  </table>
               </div>

               <div id="emptyState" class="text-center py-5 d-none">
                  <img src="{{ asset('img/empty-state.svg') }}" alt="Tidak ada antrian" style="max-width: 150px">
                  <h5 class="mt-3 text-gray-600">Tidak ada antrian yang tersedia</h5>
                  <p class="text-muted">Silakan periksa kembali filter atau refresh halaman</p>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Statistik Antrian -->
   <div class="row">
      <!-- Total Antrian Card -->
      <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
               <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                     <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Antrian</div>
                     <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAntrian">0</div>
                  </div>
                  <div class="col-auto">
                     <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Antrian Menunggu Card -->
      <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
               <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                     <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Menunggu</div>
                     <div class="h5 mb-0 font-weight-bold text-gray-800" id="waitingCount">0</div>
                  </div>
                  <div class="col-auto">
                     <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Antrian Terlayani Card -->
      <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
               <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                     <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Terlayani</div>
                     <div class="h5 mb-0 font-weight-bold text-gray-800" id="servedCount">0</div>
                  </div>
                  <div class="col-auto">
                     <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Rata-rata Waktu Tunggu Card -->
      <div class="col-xl-3 col-md-6 mb-4">
         <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
               <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                     <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Rata-rata Waktu Tunggu</div>
                     <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgWaitTime">0 menit</div>
                  </div>
                  <div class="col-auto">
                     <i class="fas fa-clock fa-2x text-gray-300"></i>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal Detail Pasien -->
<div class="modal fade" id="detailPasienModal" tabindex="-1" role="dialog" aria-labelledby="detailPasienModalLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="detailPasienModalLabel">Detail Pasien</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-4 text-center mb-3">
                  <div class="avatar-container mx-auto"
                     style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                     <i class="fas fa-user-circle fa-7x text-gray-300"></i>
                  </div>
               </div>
               <div class="col-md-8">
                  <h4 id="modalNamaPasien" class="font-weight-bold">-</h4>
                  <div class="row">
                     <div class="col-5">No. Rekam Medis</div>
                     <div class="col-7">: <span id="modalNoRM">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Jenis Kelamin</div>
                     <div class="col-7">: <span id="modalJK">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Tanggal Lahir</div>
                     <div class="col-7">: <span id="modalTglLahir">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Umur</div>
                     <div class="col-7">: <span id="modalUmur">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Alamat</div>
                     <div class="col-7">: <span id="modalAlamat">-</span></div>
                  </div>
               </div>
            </div>

            <hr>

            <div class="row mt-3">
               <div class="col-md-6">
                  <h6 class="font-weight-bold">Informasi Kunjungan</h6>
                  <div class="row">
                     <div class="col-5">No. Rawat</div>
                     <div class="col-7">: <span id="modalNoRawat">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Tanggal Registrasi</div>
                     <div class="col-7">: <span id="modalTglReg">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">No. Antrian</div>
                     <div class="col-7">: <span id="modalNoAntrian">-</span></div>
                  </div>
               </div>
               <div class="col-md-6">
                  <h6 class="font-weight-bold">Informasi Layanan</h6>
                  <div class="row">
                     <div class="col-5">Poliklinik</div>
                     <div class="col-7">: <span id="modalPoli">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Dokter</div>
                     <div class="col-7">: <span id="modalDokter">-</span></div>
                  </div>
                  <div class="row">
                     <div class="col-5">Asuransi</div>
                     <div class="col-7">: <span id="modalAsuransi">-</span></div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" id="btnPanggilPasien">
               <i class="fas fa-bullhorn mr-1"></i> Panggil Pasien
            </button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('scripts')
<script>
   $(document).ready(function() {
        loadAntrianData();
        loadPoliklinikOptions();
        
        // Auto-refresh setiap 30 detik
        setInterval(function() {
            refreshAntrian();
        }, 30000);
    });
    
    function refreshAntrian() {
        loadAntrianData();
        $('#last-update').text('Terakhir diperbarui: ' + new Date().toLocaleTimeString());
    }
    
    function loadPoliklinikOptions() {
        // Fetch data poliklinik dari server
        $.ajax({
            url: '/api/poliklinik',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let options = '<option value="">Semua Poliklinik</option>';
                data.forEach(function(poli) {
                    options += `<option value="${poli.kd_poli}">${poli.nm_poli}</option>`;
                });
                $('#filterPoli').html(options);
            },
            error: function(error) {
                console.error('Error loading poliklinik:', error);
            }
        });
    }
    
    function loadAntrianData() {
        const tanggal = $('#tanggalAntrian').val();
        const kdPoli = $('#filterPoli').val();
        const search = $('#searchInput').val();
        
        $.ajax({
            url: '/api/antrian-poliklinik',
            type: 'GET',
            data: {
                tanggal: tanggal,
                kd_poli: kdPoli,
                search: search
            },
            dataType: 'json',
            success: function(data) {
                renderAntrianTable(data);
                updateStatistics(data);
            },
            error: function(error) {
                console.error('Error loading antrian data:', error);
                showEmptyState(true);
            }
        });
    }
    
    function renderAntrianTable(data) {
        if (data.length === 0) {
            showEmptyState(true);
            return;
        }
        
        showEmptyState(false);
        let html = '';
        
        data.forEach(function(item, index) {
            const statusClass = getStatusClass(item.stts);
            
            html += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center font-weight-bold">${item.no_reg}</td>
                    <td class="text-center">${item.no_rawat}</td>
                    <td>${item.nm_pasien}</td>
                    <td class="text-center">${item.no_rkm_medis}</td>
                    <td>${item.nm_poli}</td>
                    <td class="text-center">
                        <span class="badge badge-pill ${statusClass}">${item.stts}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info" onclick="showDetail('${item.no_rawat}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        $('#antrianBody').html(html);
    }
    
    function getStatusClass(status) {
        switch(status) {
            case 'Belum':
                return 'badge-warning';
            case 'Sudah':
                return 'badge-success';
            case 'Batal':
                return 'badge-danger';
            case 'Dirawat':
                return 'badge-primary';
            default:
                return 'badge-secondary';
        }
    }
    
    function showEmptyState(show) {
        if (show) {
            $('#antrianTable').addClass('d-none');
            $('#emptyState').removeClass('d-none');
        } else {
            $('#antrianTable').removeClass('d-none');
            $('#emptyState').addClass('d-none');
        }
    }
    
    function updateStatistics(data) {
        const totalAntrian = data.length;
        const waiting = data.filter(item => item.stts === 'Belum').length;
        const served = data.filter(item => item.stts === 'Sudah').length;
        
        $('#totalAntrian').text(totalAntrian);
        $('#waitingCount').text(waiting);
        $('#servedCount').text(served);
        
        // Simulasi waktu tunggu rata-rata (dalam implementasi nyata, ini harus dihitung dari data aktual)
        const avgWaitMinutes = totalAntrian > 0 ? Math.round(waiting * 5.5) : 0;
        $('#avgWaitTime').text(avgWaitMinutes + ' menit');
    }
    
    function filterAntrian() {
        loadAntrianData();
    }
    
    function showDetail(noRawat) {
        // Fetch detail pasien dari server
        $.ajax({
            url: '/api/pasien/detail/' + noRawat,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate modal with data
                $('#modalNamaPasien').text(data.nm_pasien);
                $('#modalNoRM').text(data.no_rkm_medis);
                $('#modalJK').text(data.jk === 'L' ? 'Laki-laki' : 'Perempuan');
                $('#modalTglLahir').text(data.tgl_lahir);
                $('#modalUmur').text(data.umur);
                $('#modalAlamat').text(data.alamat);
                
                $('#modalNoRawat').text(data.no_rawat);
                $('#modalTglReg').text(data.tgl_registrasi);
                $('#modalNoAntrian').text(data.no_reg);
                
                $('#modalPoli').text(data.nm_poli);
                $('#modalDokter').text(data.nm_dokter);
                $('#modalAsuransi').text(data.png_jawab);
                
                // Show modal
                $('#detailPasienModal').modal('show');
            },
            error: function(error) {
                console.error('Error loading patient details:', error);
                alert('Gagal memuat data pasien');
            }
        });
    }
    
    // Handle panggil pasien
    $('#btnPanggilPasien').on('click', function() {
        const noRawat = $('#modalNoRawat').text();
        const namaPasien = $('#modalNamaPasien').text();
        const nomorAntrian = $('#modalNoAntrian').text();
        const poli = $('#modalPoli').text();
        
        // Simulasi panggilan dengan notifikasi suara (dalam implementasi nyata, ini bisa menggunakan sistem antrian)
        const message = `Nomor antrian ${nomorAntrian}, atas nama ${namaPasien}, silakan menuju ${poli}`;
        alert(message);
        
        // Update status panggilan ke server
        $.ajax({
            url: '/api/antrian/panggil',
            type: 'POST',
            data: {
                no_rawat: noRawat
            },
            success: function() {
                $('#detailPasienModal').modal('hide');
                refreshAntrian();
            },
            error: function(error) {
                console.error('Error calling patient:', error);
                alert('Gagal memanggil pasien');
            }
        });
    });
    
    // Handle print
    $('#printAntrian').on('click', function() {
        const tanggal = $('#tanggalAntrian').val();
        const kdPoli = $('#filterPoli').val();
        
        window.open(`/laporan/antrian-poliklinik?tanggal=${tanggal}&kd_poli=${kdPoli}`, '_blank');
    });
    
    // Handle export
    $('#exportExcel').on('click', function() {
        const tanggal = $('#tanggalAntrian').val();
        const kdPoli = $('#filterPoli').val();
        
        window.location.href = `/laporan/antrian-poliklinik/export?tanggal=${tanggal}&kd_poli=${kdPoli}`;
    });
</script>
@endsection