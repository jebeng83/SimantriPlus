<div wire:ignore.self id="modal-template-operasi" data-backdrop="false" class="modal fade" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="my-modal-title">Template Lap Operasi</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <input wire:model.debounce.500ms='search' type="text" placeholder="Cari template ...." class="form-control" id="cari_lap_op" id="cari_lap_op">
                    </div>
                </div>
                <div class="list-group">
                    @forelse ($datas as $item)
                        <button wire:click='$emit("pilihTemplateOperasi", "{{ $item->no_template ?? '' }}")' type="button" class="list-group-item list-group-item-action">{{ $item->nama_operasi ?? '' }}</button>
                    @empty
                        
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        window.addEventListener('closeModalTemplateOperasi', function(){
            $('#modal-template-operasi').modal('hide');
        });
    </script>
@endpush
