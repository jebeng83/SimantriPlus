<!-- Modal Hasil Pemeriksaan SHK, G6PD, HAK Balita -->
<div class="modal fade" id="modalShkG6pdHakBalita" tabindex="-1" role="dialog" aria-labelledby="modalShkG6pdHakBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalShkG6pdHakBalitaLabel">
                    <i class="fas fa-vials mr-2"></i>Hasil Pemeriksaan SHK, G6PD, HAK
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formShkG6pdHakBalita">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    1. Skrining Hipotiroid Kongenital - Hasil pemeriksaan laboratorium (m[IU]/L) <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="shk" id="shk_positif" value="Positif" required>
                                        <label class="form-check-label" for="shk_positif">Positif</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="shk" id="shk_negatif" value="Negatif" required>
                                        <label class="form-check-label" for="shk_negatif">Negatif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    2. Skrining Defisiensi G6PD - hasil pemeriksaan laboratorium (U/dL) <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="g6pd" id="g6pd_positif" value="Positif" required>
                                        <label class="form-check-label" for="g6pd_positif">Positif</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="g6pd" id="g6pd_negatif" value="Negatif" required>
                                        <label class="form-check-label" for="g6pd_negatif">Negatif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    3. Skrining Hiperplasia Adrenal Kongenital (HAK) - hasil pemeriksaan laboratorium <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="hak" id="hak_positif" value="Positif" required>
                                        <label class="form-check-label" for="hak_positif">Positif</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="hak" id="hak_negatif" value="Negatif" required>
                                        <label class="form-check-label" for="hak_negatif">Negatif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('shk-g6pd-hak-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
