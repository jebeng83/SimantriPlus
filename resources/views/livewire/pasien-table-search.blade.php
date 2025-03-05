<div>
    <div class="search-panel mb-4 fade-in-up">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>
                    Pencarian Lanjutan
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="search">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Pasien</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Nama pasien..."
                                        wire:model.defer="searchName">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>No. Rekam Medis</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="No. RM..."
                                        wire:model.defer="searchRM">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Alamat</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Alamat..."
                                        wire:model.defer="searchAddress">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-default" wire:click="resetSearch">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary search-button">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div id="searchResultInfo" class="text-muted" style="{{ $resultCount > 0 ? '' : 'display: none;' }}">
            <i class="fas fa-info-circle"></i> <span>{{ $resultCount }}</span> data ditemukan
        </div>
    </div>

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
                @forelse($results as $pasien)
                <tr class="patient-row" data-id="{{ $pasien->no_rkm_medis }}" style="cursor: pointer;">
                    <td>{{ $pasien->no_rkm_medis }}</td>
                    <td>{{ $pasien->nm_pasien }}</td>
                    <td>{{ $pasien->no_ktp }}</td>
                    <td>{{ $pasien->no_kk }}</td>
                    <td>{{ $pasien->no_peserta }}</td>
                    <td>{{ $pasien->no_tlp }}</td>
                    <td>{{ $pasien->tgl_lahir }}</td>
                    <td>{{ $pasien->alamat }}</td>
                    <td>{{ $pasien->stts_nikah }}</td>
                    <td>{{ $pasien->status }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info btn-view-patient"
                                onclick="viewPatient('{{ $pasien->no_rkm_medis }}')" data-toggle="tooltip"
                                title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="{{ route('pasien.edit', $pasien->no_rkm_medis) }}" class="btn btn-sm btn-primary"
                                data-toggle="tooltip" title="Edit Data" target="_self">
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
</div>