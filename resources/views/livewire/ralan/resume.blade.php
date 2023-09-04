<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-file-medical mr-1"></i> Resume Pasien </h3>
        <div class="card-tools">
            {{-- <button type="button" class="btn btn-tool" wire:click="collapsed" data-card-widget="maximize">
                <i class="fas fa-lg fa-expand"></i>
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="simpanResume">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="keluhan">Keluhan Utama</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.defer="keluhan" id="keluhan"
                            name="keluhan"></textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" wire:click="getKeluhanUtama">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                    </div>
                    {{-- @error('keluhan') <span class="text-danger">{{ $message }}</span> @enderror --}}
                </div>
                <div class="form-group col-md-6">
                    <label for="perawatan">Jalannya Penyakit Selama Perawatan</label>
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='perawatan' id="perawatan"
                        name="perawatan"></textarea>
                    {{-- @error('perawatan') <span class="text-danger">{{ $message }}</span> @enderror --}}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="radiologi">Pemeriksaan Penunjang Positif</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.defer='penunjang' id="radiologi"
                            name="radiologi"></textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" wire:click='getPemeriksaanRadiologi'>
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="lab">Hasil Laboratorium yang Positif</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.defer='lab' id="lab"
                            name="lab"></textarea>
                        <div class="input-group-append">
                            <button type="button" wire:click='getPemeriksaanLab' class="btn btn-primary">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="terapi">Terapi</label>
                <div class="input-group">
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='terapi' id="terapi"
                        name="terapi"></textarea>
                    <div class="input-group-append">
                        <button type="button" wire:click='hapusTerapi' class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="diagnosa">Diagnosa Utama</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa' id="diagnosa" name="diagnosa">
                    {{-- @error('diagnosa') <span class="text-danger">{{ $message }}</span> @enderror --}}
                </div>
                <div class="form-group col-md-4">
                    <label for="prosedur">Prosedur Utama</label>
                    <input type="text" class="form-control" wire:model.defer='prosedur' id="prosedur" name="prosedur">
                </div>
                <div class="form-group col-md-4">
                    <label for="kondisi">Kondisi Pasien Pulang</label>
                    <select type="text" class="form-control" wire:model.defer='kondisi' id="kondisi" name="kondisi">
                        <option value="Hidup">Hidup</option>
                        <option value="Meninggal">Meninggal</option>
                    </select>
                    {{-- @error('kondisi') <span class="text-danger">{{ $message }}</span> @enderror --}}
                </div>
            </div>
            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit"> Simpan </button>
            </div>
        </form>
        <h5> Daftar Resume Medis </h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-inverse" style="width: 100%">
                    <tr>
                        <th>Keluhan</th>
                        <th>Jalannya penyakit</th>
                        <th>Pemeriksaan penunjang positif</th>
                        <th>Diagnosa</th>
                        <th>Terapi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listResume as $item)
                    <tr>
                        <td>{{ $item->keluhan_utama }}</td>
                        <td>{{ $item->jalannya_penyakit }}</td>
                        <td>{{ $item->pemeriksaan_penunjang }}</td>
                        <td>{{ $item->diagnosa_utama }}</td>
                        <td>{{ $item->obat_pulang }}</td>
                        <td>
                            {{-- <button class="btn btn-primary btn-sm" wire:click="edit({{ $item->id }})">
                                <i class="fas fa-edit"></i>
                            </button> --}}
                            <button class="btn btn-danger btn-sm" wire:click="konfirmasiHapus('{{ $item->no_rawat }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Data Resume Kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Keluhan Utama -->
    <div class="modal fade" id="keluhanModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="keluhanModalTitle">Keluhan Utama</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @forelse ($listKeluhan as $item)
                    <div class="custom-control custom-checkbox">
                        <input id="keluhanCheck-{{ $item->jam_rawat }}" wire:key='{{ $item->jam_rawat }}'
                            value="{{ $item->keluhan }}" wire:model.defer='checkKeluhan' class="custom-control-input"
                            type="checkbox" name="keluhanCheck[]">
                        <label for="keluhanCheck-{{ $item->jam_rawat }}" class="custom-control-label">
                            {{ $item->keluhan }}
                        </label>
                    </div>
                    @empty
                    <h5>Data Keluhan Kosong</h5>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" wire:click='tambahKeluhan' class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penunjang -->
    <div class="modal fade" id="radiologiModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="keluhanModalTitle">Pemeriksaan Radiologi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        @forelse ($listRadiologi as $item)
                        <div class="custom-control custom-checkbox">
                            <input id="radCheck-{{  $item->jam }}" wire:key='{{ $item->jam }}'
                                class="custom-control-input" wire:model.defer='checkRadiologi'
                                value="{{ $item->hasil }}" type="checkbox" name="radCheck[]">
                            <label for="radCheck-{{  $item->jam }}" class="custom-control-label">
                                <pre>{{ $item->hasil }}</pre>
                            </label>
                        </div>
                        @empty
                        <h5>Data Pemeriksaan Radiologi Kosong</h5>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" wire:click='tambahPemeriksaanRadiologi' class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pemeriksaan Lab -->
    <div class="modal fade" id="labModal" tabindex="-1" role="dialog" aria-labelledby="labModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="labModalTitle">Pemeriksaan Laboratorium</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        @forelse ($listLab as $i => $item)
                        <div class="custom-control custom-checkbox">
                            <input id="labCheck-{{ $i }}" wire:key='labCheck-{{ $i }}' class="custom-control-input"
                                wire:model.defer='checkLab' value="{{ $item->Pemeriksaan }} : {{ $item->nilai }}"
                                type="checkbox" name="labCheck[]">
                            <label for="labCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->Pemeriksaan }} : {{ $item->nilai }}
                            </label>
                        </div>
                        @empty
                        <h5>Data Pemeriksaan Lab Kosong</h5>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" wire:click='tambahPemeriksaanLab' class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Terapi -->
    <div class="modal fade" id="terapiModal" tabindex="-1" role="dialog" aria-labelledby="terapiModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="terapiModalTitle">Terapi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        @forelse ($listTerapi as $i => $item)
                        <div class="custom-control custom-checkbox">
                            <input id="terapiCheck-{{ $i }}" wire:key='terapiCheck-{{ $i }}'
                                class="custom-control-input" wire:model.defer='checkTerapi'
                                value="{{ $item->nama_brng }} : {{ $item->jml }} {{ $item->kode_sat }}" type="checkbox"
                                name="terapiCheck[]">
                            <label for="terapiCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->nama_brng }} : {{ $item->jml }} {{ $item->kode_sat }}
                            </label>
                        </div>
                        @empty
                        <h5>Data Terapi Kosong</h5>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" wire:click='tambahTerapi' class="btn btn-primary">Ok</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script>
    window.addEventListener('swal:confirm',function(e){
            Swal.fire({
                title: e.detail.title,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: e.detail.confirmButtonText,
                cancelButtonText: e.detail.cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.function, e.detail.params[0]);
                }
            });
        });

        window.livewire.on('getKeluhanUtama',() => {
            $('#keluhanModal').modal('show');
        });

        window.livewire.on('closeKeluhanModal',() => {
            $('#keluhanModal').modal('hide');
        });

        window.livewire.on('getPemeriksaanRadiologi',() => {
            $('#radiologiModal').modal('show');
        });

        window.livewire.on('closePemeriksaanRadiologiModal',() => {
            $('#radiologiModal').modal('hide');
        });

        window.livewire.on('getPemeriksaanLab',() => {
            $('#labModal').modal('show');
        });

        window.livewire.on('closePemeriksaanLabModal',() => {
            $('#labModal').modal('hide');
        });

        window.livewire.on('getTerapi',() => {
            $('#terapiModal').modal('show');
        });

        window.livewire.on('closeTerapiModal',() => {
            $('#terapiModal').modal('hide');
        });
</script>
@endpush