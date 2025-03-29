<div>
    <div class="search-panel mb-4 fade-in-up">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>
                    Pencarian Pasien
                </h3>
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
                            <label>Nama Pasien</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Nama pasien..."
                                    wire:model="searchName">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>No. Rekam Medis / KTP / Peserta / Telp</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="No. RM..." wire:model="searchRM">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Alamat / Kelurahan / Posyandu</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Alamat..."
                                    wire:model="searchAddress">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-default" wire:click="resetFilters">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="button" class="btn btn-primary search-button" wire:click="search">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pasien->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i> Tidak ada data pasien yang ditemukan dengan kriteria pencarian
        ini
    </div>
    @else
    <div class="alert alert-success">
        <i class="fas fa-check-circle mr-2"></i> Menampilkan <strong>{{ $pasien->count() }}</strong> dari <strong>{{
            $pasien->total() }}</strong> data pasien (halaman {{ $pasien->currentPage() }} dari {{ $pasien->lastPage()
        }})
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>No. RM</th>
                    <th>Nama</th>
                    <th>No. KTP</th>
                    <th>No. KK</th>
                    <th>No. Peserta</th>
                    <th>No. Telp</th>
                    <th>Tgl. Lahir</th>
                    <th>Alamat</th>
                    <th>Status Nikah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pasien as $p)
                <tr class="patient-row"
                    onclick="window.location='{{ route('pasien.edit', $p->no_rkm_medis) }}?t=' + Date.now()"
                    style="cursor: pointer;">
                    <td>{{ $p->no_rkm_medis }}</td>
                    <td>{{ $p->nm_pasien }}</td>
                    <td>{{ $p->no_ktp }}</td>
                    <td>{{ $p->no_kk }}</td>
                    <td>{{ $p->no_peserta }}</td>
                    <td>{{ $p->no_tlp }}</td>
                    <td>{{ $p->tgl_lahir }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>{{ $p->stts_nikah }}</td>
                    <td>{{ $p->status }}</td>
                    <td onclick="event.stopPropagation();">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info btn-view-patient"
                                onclick="viewPatient('{{ $p->no_rkm_medis }}')" data-toggle="tooltip"
                                title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="{{ route('pasien.edit', $p->no_rkm_medis) }}?t={{ time() }}"
                                class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data pasien yang ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $pasien->links() }}
    </div>
</div>