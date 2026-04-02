<!-- Modal Tes Konfirmasi Pemeriksaan SHK, G6PD, HAK Balita -->
<div class="modal fade" id="modalKonfirmasiShkG6pdHakBalita" tabindex="-1" role="dialog" aria-labelledby="modalKonfirmasiShkG6pdHakBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalKonfirmasiShkG6pdHakBalitaLabel">
                    <i class="fas fa-vial mr-2"></i>Tes Konfirmasi Pemeriksaan SHK, G6PD, HAK
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formKonfirmasiShkG6pdHakBalita">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    1. Apakah dilakukan tes konfirmasi SHK (hanya untuk TSH &gt;20) <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_shk" id="konfirmasi_shk_ya" value="Ya" required>
                                        <label class="form-check-label" for="konfirmasi_shk_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_shk" id="konfirmasi_shk_tidak" value="Tidak" required>
                                        <label class="form-check-label" for="konfirmasi_shk_tidak">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    2. Apakah dilakukan tes konfirmasi Defisiensi G6PD? <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_g6pd" id="konfirmasi_g6pd_ya" value="Ya" required>
                                        <label class="form-check-label" for="konfirmasi_g6pd_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_g6pd" id="konfirmasi_g6pd_tidak" value="Tidak" required>
                                        <label class="form-check-label" for="konfirmasi_g6pd_tidak">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    3. Apakah dilakukan tes konfirmasi skrining HAK? <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_hak" id="konfirmasi_hak_ya" value="Ya" required>
                                        <label class="form-check-label" for="konfirmasi_hak_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="konfirmasi_hak" id="konfirmasi_hak_tidak" value="Tidak" required>
                                        <label class="form-check-label" for="konfirmasi_hak_tidak">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('konfirmasi-shk-g6pd-hak-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
