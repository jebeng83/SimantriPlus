<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-flask mr-1"></i> Permintaan Lab </h3>
        <div class="card-tools">
            {{-- <button type="button" wire:click="expanded" class="btn btn-tool" data-card-widget="maximize" >
                <i wire:ignore class="fas fa-lg fa-expand"></i>     
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="savePermintaanLab">
            <div class="form-group row">
                <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" wire:model.defer="klinis" id="klinis" name="klinis" />
                @error('klinis') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" wire:model.defer="info" id="info" name="info" />
                @error('info') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div wire:ignore class="form-group row">
                <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
                <div class="col-sm-8">
                <select class="form-control jenis" wire:model.defer="jns_pemeriksaan" id="jenis_lab" name="jenis[]" multiple="multiple" ></select>
                </div>
                @error('jns_pemeriksaan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit" > Simpan </button>
            </div>
        </form>
        <div class="callout callout-info">
            <h5> Daftar Permintaan Lab </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-inverse" style="width: 100%">
                        <tr>
                            <th>No. Order</th>
                            <th>Informasi</th>
                            <th>Klinis</th>
                            <th>Pemeriksaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permintaanLab as $item)
                            <tr>
                                <td>{{ $item->noorder }}</td>
                                <td>{{ $item->informasi_tambahan }}</td>
                                <td>{{ $item->diagnosa_klinis }}</td>
                                <td>
                                    @foreach ($this->getDetailPemeriksaan($item->noorder) as $pemeriksaan)
                                        <span class="badge badge-primary">{{ $pemeriksaan->nm_perawatan }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" wire:click="konfirmasiHapus('{{ $item->noorder }}')">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Permintaan Lab Kosong</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>

        window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

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

        function formatData (data) {
            var $data = $(
                '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
            );
            return $data;
        };

        $('#jenis_lab').select2({
            placeholder: 'Pilih Jenis',
            ajax: {
                url: '/api/jns_perawatan_lab',
                dataType: 'json',
                delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3
        });

        $('#jenis_lab').on('change', function (e) {
            let data = $(this).val();
            @this.set('jns_pemeriksaan', data);
        });

        window.livewire.on('select2Lab:reset', () => {
            $('#jenis_lab').val("").trigger('change');
        });

        window.livewire.on('select2Lab', () => {
            $('#jenis_lab').select2({
            placeholder: 'Pilih Jenis',
            ajax: {
                url: '/api/jns_perawatan_lab',
                dataType: 'json',
                delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3
            });
        });

    </script>
@endpush
