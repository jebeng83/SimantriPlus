<div>
    <div class="d-flex flex-row-reverse">
        <div class="col-12 col-sm-4 col-md-4">
            <input wire:model.debounce.500ms='search' type="text" class="form-control" id="search" name="search" placeholder="Cari template operasi">
        </div>
    </div>
    <div wire:init='loadDatas' class="table-responsive mt-3">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Operasi</th>
                    <th>Diagnosa Pra Bedah</th>
                    <th>Diagnosa Pasca Bedah</th>
                    <th>Jaringan Dieksisi</th>
                    <th>Permintaan PA</th>
                    <th>Laporan Operasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $template->nama_operasi }}</td>
                    <td>{{ $template->diagnosa_preop }}</td>
                    <td>{{ $template->diagnosa_postop }}</td>
                    <td>{{ $template->jaringan_dieksisi }}</td>
                    <td>{{ $template->permintaan_pa }}</td>
                    <td>{{ $template->laporan_operasi }}</td>
                    <td>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <button wire:click='edit("{{$template->no_template}}")' type="button" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></button>
                            <button wire:click='confirmDelete("{{$template->no_template}}")' type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if(!empty($templates))
        <div class="d-flex flex-row">
            <div class="mx-auto">
                {{ $templates->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
