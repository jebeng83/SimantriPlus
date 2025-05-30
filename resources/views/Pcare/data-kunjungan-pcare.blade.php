@extends('adminlte::page')

@section('title', 'Data Kunjungan PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Data Kunjungan PCare</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Kunjungan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success" id="btnKirimUlangTerpilih">
                        <i class="fas fa-sync-alt"></i> Kirim Ulang Terpilih
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelKunjungan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll"></th>
                                <th>No. Rawat</th>
                                <th>No. Kunjungan</th>
                                <th>Tgl Daftar</th>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>No. Kartu</th>
                                <th>Poli</th>
                                <th>Keluhan</th>
                                <th>Kesadaran</th>
                                <th>Sistole</th>
                                <th>Diastole</th>
                                <th>BB</th>
                                <th>TB</th>
                                <th>Resp Rate</th>
                                <th>Heart Rate</th>
                                <th>Lingkar Perut</th>
                                <th>Terapi</th>
                                <th>Status Pulang</th>
                                <th>Tgl Pulang</th>
                                <th>Dokter</th>
                                <th>Diagnosa 1</th>
                                <th>Diagnosa 2</th>
                                <th>Diagnosa 3</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/datatables/css/dataTables.bootstrap4.min.css">
<style>
    .table-responsive {
        overflow-x: auto;
    }

    #tabelKunjungan th,
    #tabelKunjungan td {
        white-space: nowrap;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi DataTable
    var table = $('#tabelKunjungan').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: "{{ route('pcare.data-kunjungan') }}",
            type: 'GET'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="checkItem" value="' + row.no_rawat + '">';
                }
            },
            { data: 'no_rawat', name: 'pku.no_rawat' },
            { data: 'noKunjungan', name: 'pku.noKunjungan' },
            { data: 'tglDaftar', name: 'rp.tgl_registrasi' },
            { data: 'no_rkm_medis', name: 'pku.no_rkm_medis' },
            { data: 'nm_pasien', name: 'p.nm_pasien' },
            { data: 'noKartu', name: 'pku.noKartu' },
            { 
                data: null,
                name: 'pku.nmPoli',
                render: function(data, type, row) {
                    return row.kdPoli + ' - ' + row.nmPoli;
                }
            },
            { data: 'keluhan', name: 'pku.keluhan' },
            { 
                data: null,
                name: 'pku.nmSadar',
                render: function(data, type, row) {
                    return row.kdSadar + ' - ' + row.nmSadar;
                }
            },
            { data: 'sistole', name: 'pku.sistole' },
            { data: 'diastole', name: 'pku.diastole' },
            { data: 'beratBadan', name: 'pku.beratBadan' },
            { data: 'tinggiBadan', name: 'pku.tinggiBadan' },
            { data: 'respRate', name: 'pku.respRate' },
            { data: 'heartRate', name: 'pku.heartRate' },
            { data: 'lingkarPerut', name: 'pku.lingkarPerut' },
            { data: 'terapi', name: 'pku.terapi' },
            { 
                data: null,
                name: 'pku.nmStatusPulang',
                render: function(data, type, row) {
                    return row.kdStatusPulang + ' - ' + row.nmStatusPulang;
                }
            },
            { data: 'tglPulang', name: 'pku.tglPulang' },
            { 
                data: null,
                name: 'pku.nmDokter',
                render: function(data, type, row) {
                    return row.kdDokter + ' - ' + row.nmDokter;
                }
            },
            { 
                data: null,
                name: 'pku.nmDiag1',
                render: function(data, type, row) {
                    return row.kdDiag1 + ' - ' + row.nmDiag1;
                }
            },
            { 
                data: null,
                name: 'pku.nmDiag2',
                render: function(data, type, row) {
                    return row.kdDiag2 ? (row.kdDiag2 + ' - ' + row.nmDiag2) : '-';
                }
            },
            { 
                data: null,
                name: 'pku.nmDiag3',
                render: function(data, type, row) {
                    return row.kdDiag3 ? (row.kdDiag3 + ' - ' + row.nmDiag3) : '-';
                }
            },
            { data: 'status', name: 'pku.status' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[3, 'desc']],
        language: {
            "sEmptyTable":     "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing":     "Sedang memproses...",
            "sLengthMenu":     "Tampilkan _MENU_ entri",
            "sZeroRecords":    "Tidak ditemukan data yang sesuai",
            "sInfo":           "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty":      "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered":   "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix":    "",
            "sSearch":         "Cari:",
            "sUrl":           "",
            "oPaginate": {
                "sFirst":    "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext":     "Selanjutnya",
                "sLast":     "Terakhir"
            }
        }
    });

    // Handle checkbox "Check All"
    $('#checkAll').on('click', function() {
        $('.checkItem').prop('checked', $(this).prop('checked'));
    });

    // Handle tombol Kirim Ulang individual
    $('#tabelKunjungan').on('click', '.btn-kirim-ulang', function() {
        var noRawat = $(this).data('id');
        kirimUlangKunjungan(noRawat);
    });

    // Handle tombol Kirim Ulang Terpilih
    $('#btnKirimUlangTerpilih').on('click', function() {
        var selectedRows = [];
        $('.checkItem:checked').each(function() {
            selectedRows.push($(this).val());
        });

        if (selectedRows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu data untuk dikirim ulang'
            });
            return;
        }

        kirimUlangBatch(selectedRows);
    });

    // Fungsi untuk kirim ulang satu data
    function kirimUlangKunjungan(noRawat) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengirim ulang data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Ulang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/pcare/kunjungan/kirim-ulang/' + noRawat,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                                text: response.message || 'Data berhasil dikirim ulang'
                        });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengirim ulang data'
                            });
                        }
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat mengirim data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }

    // Fungsi untuk kirim ulang batch
    function kirimUlangBatch(noRawatList) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengirim ulang ' + noRawatList.length + ' data?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Ulang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/pcare/kunjungan/kirim-ulang-batch',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        no_rawat: noRawatList
                    },
                    success: function(response) {
                        if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                                text: response.message || 'Data batch berhasil dikirim ulang'
                        });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengirim ulang data batch'
                            });
                        }
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat mengirim data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    }
});
</script>
@stop