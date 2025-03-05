<div>
    <div wire:ignore.self id="change-card" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Ganti No Kartu</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent='simpan'>
                        <x-ui.input label="No. Kartu" id="no_card" type="text" model='no_card' />
                        <div class="d-flex flex-row">
                            <div class="ml-auto">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                                <button class="btn btn-secondary" data-dismiss="modal" type="button">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
