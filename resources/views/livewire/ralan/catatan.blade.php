<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-receipt mr-1"></i> Catatan Pasien </h3>
        <div class="card-tools">
            {{-- <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="maximize" >
                <i class="fas fa-lg fa-expand"></i>     
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="simpanCatatan">
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea wire:model="catatan" class="form-control" id="catatan" rows="3"></textarea>
                @error('catatan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit" > Simpan </button>
            </div>
        </form>
        <div class="callout callout-info">
            <h5> Daftar Catatan Pasien </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-inverse" style="width: 100%">
                        <tr>
                            <th>No</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listCatatan as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->catatan }}</td>
                                <td>
                                    <button wire:click="hapusCatatan('{{ $item->no_rkm_medis }}')" class="btn btn-sm btn-danger" type="button" > Hapus </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center"> Tidak ada catatan </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
