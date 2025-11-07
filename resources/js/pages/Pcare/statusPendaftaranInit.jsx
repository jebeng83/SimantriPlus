/*
 * PCare Status Pendaftaran page initializer (non-React)
 * Moves inline script logic from Blade into a Vite-managed module to avoid
 * HTML entity parsing issues (e.g., "Unexpected token '&'") and to keep JS organized.
 */

export default function initStatusPendaftaran() {
  const $ = window.$ || window.jQuery;
  if (!$) {
    console.error('[PCare Status] jQuery is not available on window. Aborting init.');
    return;
  }

  // Helper: hide fallback summary if React header is active
  const hideFallbackIfReact = () => {
    try {
      if (window.PCARE_STATUS_REACT_READY || typeof window.setPcareSummary === 'function') {
        $('#pcare-summary-fallback').hide().attr('aria-hidden', 'true');
      }
    } catch (e) {
      // Non-fatal
    }
  };

  // Initial checks shortly after DOM is ready
  hideFallbackIfReact();
  setTimeout(hideFallbackIfReact, 300);

  // Initialize DataTable
  const table = $('#tabel-status-pcare').DataTable({
    processing: true,
    responsive: true,
    autoWidth: false,
    deferRender: true,
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
    // Simple DOM to avoid complex inline HTML parsing
    dom: 'lfrtip',
    language: {
      search: 'Cari:',
      lengthMenu: 'Tampilkan _MENU_ data',
      info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
      infoEmpty: 'Tidak ada data',
      zeroRecords: 'Data tidak ditemukan',
      paginate: {
        first: '<i class="fas fa-angle-double-left"></i>',
        previous: '<i class="fas fa-angle-left"></i>',
        next: '<i class="fas fa-angle-right"></i>',
        last: '<i class="fas fa-angle-double-right"></i>'
      }
    },
    initComplete: function () {
      // Add search icon dynamically to avoid HTML inside language config
      const lbl = $('.dataTables_filter label');
      if (lbl.find('i.fas.fa-search').length === 0) {
        lbl.prepend("<i class='fas fa-search mr-1'></i> ");
      }
    },
    data: [],
    columns: [
      { data: null, name: 'index', className: 'text-center', width: '5%', render: (data, type, row, meta) => meta.row + 1 },
      { data: 'tgl_registrasi', name: 'tgl_registrasi', width: '10%', render: function (data) {
          const parts = (data || '').split('-');
          return parts.length === 3 ? `${parts[2]}-${parts[1]}-${parts[0]}` : (data || '-');
        }
      },
      { data: 'no_rawat', name: 'no_rawat', width: '15%' },
      { data: 'no_rkm_medis', name: 'no_rkm_medis', width: '10%' },
      { data: 'nm_pasien', name: 'nm_pasien', width: '20%' },
      { data: null, name: 'poli', width: '15%', render: function (data, type, row) {
          return `${row.nm_poli} (${row.kd_poli})`;
        }
      },
      { data: 'penjamin', name: 'penjamin', width: '10%' },
      { data: 'status_pcare', name: 'status_pcare', className: 'text-center', width: '10%', render: function (data) {
          if (data === 'Terkirim') return '<span class="badge badge-status-terkirim">Terkirim</span>';
          if (data === 'Batal') return '<span class="badge badge-status-batal">Batal</span>';
          return '<span class="badge badge-status-belum">Belum</span>';
        }
      },
      { data: null, name: 'no_kunjungan', width: '18%', render: function (data, type, row) {
          const kunj = row.no_kunjungan || '-';
          const rujuk = row.no_kunjungan_rujuk ? `<div class="text-muted small">Rujukan: ${row.no_kunjungan_rujuk}</div>` : '';
          // Remove duplicate badge and show rujukan only once
          return `${kunj}${rujuk}`;
        }
      },
      { data: 'status_kunjungan_final', name: 'status_kunjungan_final', width: '12%', render: function (data) {
          if (data === 'Rujuk Lanjut') return '<span class="badge badge-info">Rujuk Lanjut</span>';
          if (data === 'Terkirim') return '<span class="badge badge-status-terkirim">Terkirim</span>';
          return data || '-';
        }
      },
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

    window.Swal && window.Swal.fire({
      title: 'Memuat Data',
      html: 'Mohon tunggu...',
      allowOutsideClick: false,
      didOpen: () => window.Swal.showLoading()
    });

    $.getJSON('/api/pcare/pendaftaran/status', { start_date, end_date, status })
      .done(function (resp) {
        window.Swal && window.Swal.close();
        if (!resp.success) {
          return window.Swal && window.Swal.fire({ icon: 'error', title: 'Gagal', text: resp.message || 'Tidak dapat memuat data' });
        }

        // Update fallback summary (non-React)
        const s = resp.summary || {};
        $('#sum-total').text(s.total || 0);
        $('#sum-terkirim').text(s.terkirim || 0);
        $('#sum-kunjungan').text(s.sukses_kunjungan || 0);
        $('#sum-rujukan').text(s.jumlah_rujukan || 0);
        $('#sum-gap-reg-pcare').text(s.gap_reg_vs_pcare || 0);
        $('#sum-gap-pcare-kunjungan').text(s.gap_pcare_vs_kunjungan || 0);
        $('#sum-gap-reg-kunjungan').text(s.gap_reg_vs_kunjungan || 0);
        $('#sum-persentase').text((s.persentase || 0) + '%');
        $('#sum-progress').css('width', (s.persentase || 0) + '%');

        // If React header is present, update it and ensure fallback stays hidden
        if (window.setPcareSummary) {
          hideFallbackIfReact();
          window.setPcareSummary({
            total: s.total || 0,
            terkirim: s.terkirim || 0,
            belum: s.belum || 0,
            batal: s.batal || 0,
            persentase: s.persentase || 0,
            sukses_kunjungan: s.sukses_kunjungan || 0,
            jumlah_rujukan: s.jumlah_rujukan || 0,
            gap_reg_vs_pcare: s.gap_reg_vs_pcare || 0,
            gap_pcare_vs_kunjungan: s.gap_pcare_vs_kunjungan || 0,
            gap_reg_vs_kunjungan: s.gap_reg_vs_kunjungan || 0,
          });
        }

        // Render Kepatuhan per Poli
        const kp = resp.kepatuhan_poli || [];
        const body = $('#kepatuhan-poli-body');
        body.empty();
        kp.sort((a, b) => (b.kepatuhan - a.kepatuhan));
        kp.forEach((item) => {
          const pct = typeof item.kepatuhan === 'number' ? item.kepatuhan : 0;
          const cls = pct >= 90 ? 'badge-kepatuhan-high' : (pct >= 60 ? 'badge-kepatuhan-mid' : 'badge-kepatuhan-low');
          body.append(`
            <tr>
              <td>${item.nm_poli} (${item.kd_poli})</td>
              <td class="text-right">${item.total_registrasi}</td>
              <td class="text-right">${item.sukses_kunjungan}</td>
              <td class="text-right">${item.jumlah_rujukan}</td>
              <td class="text-right">${item.realisasi}</td>
              <td class="text-right"><span class="badge ${cls}">${pct}%</span></td>
            </tr>
          `);
        });

        // Reload table data
        table.clear();
        table.rows.add(resp.data);
        table.draw();
      })
      .fail(function () {
        window.Swal && window.Swal.close();
        window.Swal && window.Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memuat data' });
      });
  }

  // Initial load
  loadData();

  // Filter submit
  $('#filter-form').on('submit', function (e) { e.preventDefault(); loadData(); });

  // Reset filter
  $('#reset-filter').on('click', function () {
    const today = new Date().toISOString().slice(0, 10);
    $('#start_date').val(today);
    $('#end_date').val(today);
    $('#status').val('');
    loadData();
  });

  // Toggle Kepatuhan per Poli (default tertutup)
  $('#toggle-kepatuhan').on('click', function () {
    const body = $('#kepatuhan-body');
    const icon = $(this).find('i');
    body.slideToggle(150, function () {
      if (body.is(':visible')) {
        icon.removeClass('fa-plus').addClass('fa-minus');
      } else {
        icon.removeClass('fa-minus').addClass('fa-plus');
      }
    });
  });

  // Toggle Ringkasan Utama (filter + cards + progress)
  $('#toggle-summary').on('click', function () {
    const body = $('#summary-card-body');
    const icon = $(this).find('i');
    body.slideToggle(150, function () {
      if (body.is(':visible')) {
        icon.removeClass('fa-plus').addClass('fa-minus');
      } else {
        icon.removeClass('fa-minus').addClass('fa-plus');
      }
    });
  });
}