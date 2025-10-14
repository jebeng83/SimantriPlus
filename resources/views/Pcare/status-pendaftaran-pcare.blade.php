@extends('adminlte::page')

@section('title', 'Status Pendaftaran PCare BPJS')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-chart-bar text-primary"></i> Status Pendaftaran PCare BPJS</h1>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Status Pendaftaran PCare</li>
   </ol>
  </div>
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">Perbandingan Total Registrasi vs Sukses Terkirim ke PCare</h3>
      </div>
      <div class="card-body">
        <!-- Filter -->
        <form id="filter-form" class="form-inline mb-3">
          <div class="form-group mr-2">
            <label for="start_date" class="mr-2">Tanggal Mulai:</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
          </div>
          <div class="form-group mr-2">
            <label for="end_date" class="mr-2">Tanggal Selesai:</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
          </div>
          <div class="form-group mr-2">
            <label for="status" class="mr-2">Status PCare:</label>
            <select class="form-control" id="status" name="status">
              <option value="">Semua</option>
              <option value="Terkirim">Terkirim</option>
              <option value="Belum">Belum</option>
              <option value="Batal">Batal</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary mr-2">
            <i class="fas fa-search"></i> Tampilkan
          </button>
          <button type="button" id="reset-filter" class="btn btn-secondary">
            <i class="fas fa-sync"></i> Reset
          </button>
        </form>

        <!-- Ringkasan & Progress (Fallback non-React) -->
        <div class="mb-3" id="pcare-summary-fallback">
          <div class="row">
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#0ea5e9;color:#fff"><i class="fas fa-users"></i></div>
                  <div>
                    <div class="text-muted small">Total Reg (BPJ/NON/PBI)</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-total">0</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#22c55e;color:#fff"><i class="fas fa-paper-plane"></i></div>
                  <div>
                    <div class="text-muted small">Sukses Pendaftaran PCare</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-terkirim">0</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#10b981;color:#fff"><i class="fas fa-notes-medical"></i></div>
                  <div>
                    <div class="text-muted small">Sukses Kunjungan PCare</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-kunjungan">0</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#f59e0b;color:#fff"><i class="fas fa-arrows-alt-h"></i></div>
                  <div>
                    <div class="text-muted small">Gap Reg vs Pendaftaran</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-gap-reg-pcare">0</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#f97316;color:#fff"><i class="fas fa-exchange-alt"></i></div>
                  <div>
                    <div class="text-muted small">Gap Pendaftaran vs Kunjungan</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-gap-pcare-kunjungan">0</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                  <div class="mr-3 rounded d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#fb7185;color:#fff"><i class="fas fa-balance-scale"></i></div>
                  <div>
                    <div class="text-muted small">Gap Reg vs Kunjungan</div>
                    <div class="h5 mb-0 font-weight-bold" id="sum-gap-reg-kunjungan">0</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted">Persentase Sukses Pendaftaran</span>
              <span class="font-weight-bold" id="sum-persentase">0%</span>
            </div>
            <div class="progress" style="height:10px">
              <div class="progress-bar bg-success" id="sum-progress" role="progressbar" style="width:0%"></div>
            </div>
          </div>
        </div>

        <!-- React mount (jika build React aktif, akan override fallback di atas) -->
        <div id="pcare-status-react-root" class="mb-3"></div>

        <!-- Tabel Data -->
        <div class="table-responsive">
          <table id="tabel-status-pcare" class="table table-striped table-hover w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Rawat</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Poli</th>
                <th>Penjamin</th>
                <th>Status PCare</th>
                <th>No Kunjungan</th>
                <th>Status Kunjungan</th>
                <th>Keluhan</th>
                <th>Tinggi</th>
                <th>Berat</th>
                <th>Lingkar Perut</th>
                <th>Tensi</th>
                <th>Nadi</th>
                <th>Respirasi</th>
                <th>Suhu</th>
                <th>Instruksi</th>
                <th>Diagnosa</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('epasien/plugins/jquery-datatable/extensions/responsive/css/responsive.dataTables.min.css') }}">
<style>
  .badge-status-terkirim { background-color: #28a745; color: #fff; }
  .badge-status-belum { background-color: #ffc107; color: #212529; }
  .badge-status-batal { background-color: #dc3545; color: #fff; }
  /* Tabel lebih elegan */
  #tabel-status-pcare thead th {
    background: #f1f5f9;
    color: #374151;
    font-weight: 700;
    border-bottom: 1px solid #e5e7eb;
  }
  #tabel-status-pcare tbody tr:hover { background: #f9fafb; }
  .dataTables_wrapper .dataTables_filter input { border-radius: 6px; }
  .dataTables_wrapper .dataTables_length select { border-radius: 6px; }
</style>
@stop

@section('js')
<script src="{{ asset('epasien/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/extensions/responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(function() {
    const table = $('#tabel-status-pcare').DataTable({
      processing: true,
      responsive: true,
      autoWidth: false,
      deferRender: true,
      pageLength: 25,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'Semua']],
      dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-5"i><"col-md-7"p>>',
      language: {
        search: "<i class='fas fa-search mr-1'></i> Cari:",
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data',
        zeroRecords: '<div class="text-center text-muted py-3"><i class="fas fa-search fa-2x mb-2"></i><br>Data tidak ditemukan</div>',
        paginate: {
          first: '<i class="fas fa-angle-double-left"></i>',
          previous: '<i class="fas fa-angle-left"></i>',
          next: '<i class="fas fa-angle-right"></i>',
          last: '<i class="fas fa-angle-double-right"></i>'
        }
      },
      data: [],
      columns: [
        { data: null, name: 'index', className: 'text-center', width: '5%', render: (data, type, row, meta) => meta.row + 1 },
        { data: 'tgl_registrasi', name: 'tgl_registrasi', width: '10%', render: function(data) {
            const parts = (data || '').split('-');
            return parts.length === 3 ? `${parts[2]}-${parts[1]}-${parts[0]}` : (data || '-');
          }
        },
        { data: 'no_rawat', name: 'no_rawat', width: '15%' },
        { data: 'no_rkm_medis', name: 'no_rkm_medis', width: '10%' },
        { data: 'nm_pasien', name: 'nm_pasien', width: '20%' },
        { data: null, name: 'poli', width: '15%', render: function(data, type, row){
            return `${row.nm_poli} (${row.kd_poli})`;
          }
        },
        { data: 'penjamin', name: 'penjamin', width: '10%' },
        { data: 'status_pcare', name: 'status_pcare', className: 'text-center', width: '10%', render: function(data){
            if (data === 'Terkirim') return '<span class="badge badge-status-terkirim">Terkirim</span>';
            if (data === 'Batal') return '<span class="badge badge-status-batal">Batal</span>';
            return '<span class="badge badge-status-belum">Belum</span>';
          }
        },
        { data: 'no_kunjungan', name: 'no_kunjungan', width: '15%' },
        { data: 'status_kunjungan', name: 'status_kunjungan', width: '10%' },
        { data: 'keluhan', name: 'keluhan', width: '20%' },
        { data: 'tinggi', name: 'tinggi', width: '7%', className: 'text-right' },
        { data: 'berat', name: 'berat', width: '7%', className: 'text-right' },
        { data: 'lingkar_perut', name: 'lingkar_perut', width: '8%', className: 'text-right' },
        { data: 'tensi', name: 'tensi', width: '8%' },
        { data: 'nadi', name: 'nadi', width: '7%', className: 'text-right' },
        { data: 'respirasi', name: 'respirasi', width: '7%', className: 'text-right' },
        { data: 'suhu_tubuh', name: 'suhu_tubuh', width: '7%', className: 'text-right' },
        { data: 'instruksi', name: 'instruksi', width: '15%' },
        { data: 'kode_diagnosa', name: 'kode_diagnosa', width: '12%' }
      ],
      order: [[1, 'desc']]
    });

    function loadData() {
      const start_date = $('#start_date').val();
      const end_date = $('#end_date').val();
      const status = $('#status').val();

      Swal.fire({
        title: 'Memuat Data',
        html: 'Mohon tunggu...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      $.getJSON('/api/pcare/pendaftaran/status', { start_date, end_date, status })
        .done(function(resp){
          Swal.close();
          if (!resp.success) {
            return Swal.fire({ icon: 'error', title: 'Gagal', text: resp.message || 'Tidak dapat memuat data' });
          }

          // Update summary fallback (non-React)
          const s = resp.summary || {};
          $('#sum-total').text(s.total || 0);
          $('#sum-terkirim').text(s.terkirim || 0);
          $('#sum-kunjungan').text(s.sukses_kunjungan || 0);
          $('#sum-gap-reg-pcare').text(s.gap_reg_vs_pcare || 0);
          $('#sum-gap-pcare-kunjungan').text(s.gap_pcare_vs_kunjungan || 0);
          $('#sum-gap-reg-kunjungan').text(s.gap_reg_vs_kunjungan || 0);
          $('#sum-persentase').text((s.persentase || 0) + '%');
          $('#sum-progress').css('width', (s.persentase || 0) + '%');

          // Update summary via React component (Framer Motion) jika tersedia
          if (window.setPcareSummary) {
            window.setPcareSummary({
              total: s.total || 0,
              terkirim: s.terkirim || 0,
              belum: s.belum || 0,
              batal: s.batal || 0,
              persentase: s.persentase || 0,
              sukses_kunjungan: s.sukses_kunjungan || 0,
              gap_reg_vs_pcare: s.gap_reg_vs_pcare || 0,
              gap_pcare_vs_kunjungan: s.gap_pcare_vs_kunjungan || 0,
              gap_reg_vs_kunjungan: s.gap_reg_vs_kunjungan || 0,
            });
          }

          // Reload table data
          table.clear();
          table.rows.add(resp.data);
          table.draw();
        })
        .fail(function(){
          Swal.close();
          Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memuat data' });
        });
    }

    // Initial load
    loadData();

    // Filter submit
    $('#filter-form').on('submit', function(e){ e.preventDefault(); loadData(); });

    // Reset filter
    $('#reset-filter').on('click', function(){
      const today = new Date().toISOString().slice(0,10);
      $('#start_date').val(today);
      $('#end_date').val(today);
      $('#status').val('');
      loadData();
    });
  });
</script>
@stop