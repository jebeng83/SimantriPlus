<!-- Modal Pengambilan Darah Tumit Balita -->
<div class="modal fade" id="modalDarahTumitBalita" tabindex="-1" role="dialog" aria-labelledby="modalDarahTumitBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDarahTumitBalitaLabel">
                    <i class="fas fa-tint mr-2"></i>Pengambilan Darah Tumit
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formDarahTumitBalita">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    1. Pengambilan Darah Tumit <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="darah_tumit" id="darah_tumit_ya" value="Ya" required>
                                        <label class="form-check-label" for="darah_tumit_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="darah_tumit" id="darah_tumit_tidak" value="Tidak" required>
                                        <label class="form-check-label" for="darah_tumit_tidak">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('darah-tumit-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
