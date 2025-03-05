<div>
    <!-- Form Pendaftaran -->
    <div class="modal fade show d-block position-static" id="modalPendaftaran" tabindex="-1" role="dialog"
        aria-labelledby="pendaftaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pendaftaranModalLabel">Pendaftaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                        wire:click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="bg-primary text-white p-3 text-center mb-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-plus fa-2x mr-2"></i>
                            <h4 class="mb-0">Formulir Pendaftaran Pasien</h4>
                        </div>
                    </div>

                    <div class="container-fluid px-4 pb-4">
                        <!-- Baris 1: Tanggal dan No. KTP -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Tanggal Registrasi</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </span>
                                    </div>
                                    <input type="datetime-local" class="form-control" wire:model.defer="tgl_registrasi"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Nomor RM / KTP / Nama Pasien</label>
                                <div class="input-group" wire:ignore>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-id-card text-white"></i>
                                        </span>
                                    </div>
                                    <select id="select2-pasien" class="form-control" style="width: 100%;" required>
                                        <option value="">Cari Nomor RM / KTP / Nama Pasien</option>
                                    </select>
                                </div>
                                <input type="hidden" id="no_rkm_medis" wire:model.defer="no_rkm_medis">
                            </div>
                        </div>

                        <!-- Baris 2: Penanggung Jawab dan Hubungan PJ -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Penanggung Jawab</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-user text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Penanggung Jawab"
                                        wire:model.defer="pj" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Hubungan PJ</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-people-arrows text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Hubungan Penanggung Jawab"
                                        wire:model.defer="hubungan_pj" required>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 3: Alamat PJ -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="font-weight-bold text-primary">Alamat PJ</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-map-marker-alt text-white"></i>
                                        </span>
                                    </div>
                                    <textarea class="form-control" placeholder="Alamat Penanggung Jawab"
                                        wire:model.defer="alamat_pj" required rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 4: Status dan Penjab -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Status</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-info-circle text-white"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" wire:model.defer="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Baru">Baru</option>
                                        <option value="Lama">Lama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Penjab</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-dollar-sign text-white"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" wire:model.defer="penjab" required>
                                        <option value="">Pilih Penjab</option>
                                        @foreach($listPenjab as $p)
                                        <option value="{{ $p->kd_pj }}">{{ $p->png_jawab }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 5: Dokter dan Unit -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Dokter</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-user-md text-white"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" wire:model.defer="dokter" required>
                                        <option value="">Cari Nama Dokter</option>
                                        @foreach($dokters as $d)
                                        <option value="{{ $d->kd_dokter }}">{{ $d->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold text-primary">Unit</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary">
                                            <i class="fas fa-hospital text-white"></i>
                                        </span>
                                    </div>
                                    <select class="form-control" wire:model.defer="kd_poli" required>
                                        <option value="">Pilih Unit</option>
                                        @foreach($poliklinik as $p)
                                        <option value="{{ $p->kd_poli }}">{{ $p->nm_poli }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 py-2" wire:click="closeModal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 py-2" wire:click="simpan">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pendaftaran -->
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-card title="Daftar Pendaftaran Hari Ini" theme="success" theme-mode="outline"
                icon="fas fa-clipboard-check">

                <!-- Panel Filter -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Filter Data</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="filterPoliPosyandu" wire:model="filterPoliPosyandu"
                                                    wire:change="$refresh">
                                                <label class="custom-control-label font-weight-bold text-success"
                                                    for="filterPoliPosyandu">
                                                    Tampilkan Posyandu
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="selectedPosyandu" class="font-weight-bold text-success">Pilih
                                                Nama Posyandu</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-success">
                                                        <i class="fas fa-clinic-medical text-white"></i>
                                                    </span>
                                                </div>
                                                <select class="form-control" id="selectedPosyandu"
                                                    wire:model="selectedPosyandu" wire:change="$refresh">
                                                    <option value="">Semua Posyandu</option>
                                                    @foreach($listPosyandu as $posyandu)
                                                    <option value="{{ $posyandu['nama_posyandu'] }}">{{
                                                        $posyandu['nama_posyandu'] }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-success" wire:click="resetFilters">
                                                        <i class="fas fa-undo mr-1"></i> Reset Filter
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
                <!-- End Panel Filter -->

                <div class="table-responsive">
                    <table id="table-pendaftaran" class="table table-striped table-hover">
                        <thead class="bg-gradient-success text-white">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No Rawat</th>
                                <th width="10%">No RM</th>
                                <th width="20%">Nama Pasien</th>
                                <th width="15%">Dokter</th>
                                <th width="10%">Poliklinik</th>
                                <th width="10%">Posyandu</th>
                                <th width="10%">Penjamin</th>
                                <th width="5%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaran as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td>{{ $item->nm_poli }}</td>
                                <td>{{ $item->data_posyandu ?? '-' }}</td>
                                <td>{{ $item->png_jawab }}</td>
                                <td>
                                    @if($item->stts == 'Belum')
                                    <span class="badge badge-warning">Menunggu</span>
                                    @elseif($item->stts == 'Sudah')
                                    <span class="badge badge-success">Selesai</span>
                                    @else
                                    <span class="badge badge-info">{{ $item->stts }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="bukaModalPendaftaran('{{ $item->no_rawat }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
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

    @push('css')
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .form-control {
            border-radius: 0;
        }

        .btn {
            border-radius: 0;
        }

        .modal-content {
            border-radius: 0;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .input-group-text {
            border-radius: 0;
        }

        .modal-header,
        .modal-footer {
            padding: 1rem;
        }
    </style>
    @endpush

    @push('js')
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
            
            initDataTable();
            
            // Re-initialize datatable ketika konten diperbarui
            Livewire.hook('message.processed', (message, component) => {
                initDataTable();
            });
            
            function initDataTable() {
                // Hancurkan instance tabel yang ada jika sudah diinisialisasi
                if ($.fn.DataTable.isDataTable('#table-pendaftaran')) {
                    $('#table-pendaftaran').DataTable().destroy();
                }
                
                // Inisialisasi datatable baru
                $('#table-pendaftaran').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "language": {
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
                        }
                    }
                });
            }
            
            Livewire.on('openModalPendaftaran', function () {
                $('#modalPendaftaran').modal('show');
            });
            
            Livewire.on('closeModalPendaftaran', function () {
                $('#modalPendaftaran').modal('hide');
            });
            
            // Initialize Select2 for pasien search
            $('#select2-pasien').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/pasien/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.log('Select2 mengirim request dengan term:', params.term);
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        console.log('Select2 menerima response:', data);
                        params.page = params.page || 1;
                        
                        // Perbaikan untuk formatting items
                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: false
                },
                placeholder: 'Cari Nomor RM / KTP / Nama Pasien',
                minimumInputLength: 2,
                templateResult: formatPasien,
                templateSelection: formatPasienSelection
            });
            
            function formatPasien (pasien) {
                if (pasien.loading) {
                    return pasien.text;
                }
                
                // Pastikan menggunakan masked_ktp jika tersedia atau buat sendiri
                var maskedKtpValue = pasien.masked_ktp || maskKtp(pasien.no_ktp);
                
                var $container = $(
                    '<div class="d-flex align-items-center p-1">' +
                        '<div class="mr-2">' +
                            '<i class="fas fa-user-circle fa-2x text-primary"></i>' +
                        '</div>' +
                        '<div style="width: 100%">' +
                            '<div class="font-weight-bold">' + pasien.no_rkm_medis + ' - ' + pasien.nm_pasien + '</div>' +
                            '<div class="small text-muted">' +
                                '<i class="fas fa-id-card mr-1"></i> KTP: ' + maskedKtpValue + 
                            '</div>' +
                            '<div class="small text-muted">' +
                                '<i class="fas fa-map-marker-alt mr-1"></i> ' + (pasien.kelurahanpj || '-') +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
                
                return $container;
            }
            
            function formatPasienSelection (pasien) {
                if (!pasien.id) {
                    return pasien.text;
                }
                
                // Pastikan menggunakan masked_ktp jika tersedia
                var maskedKtpValue = pasien.masked_ktp || maskKtp(pasien.no_ktp);
                
                var text = pasien.no_rkm_medis + ' - ' + pasien.nm_pasien;
                if (maskedKtpValue && maskedKtpValue !== '-') {
                    text += ' (' + maskedKtpValue + ')';
                }
                
                return text;
            }
            
            // Handle selecting pasien
            $('#select2-pasien').on('select2:select', function (e) {
                var data = e.params.data;
                Livewire.emit('pasienSelected', data.no_rkm_medis);
                
                // Update hidden field
                $('#no_rkm_medis').val(data.no_rkm_medis);
            });
        });
    </script>
    @endpush
</div>