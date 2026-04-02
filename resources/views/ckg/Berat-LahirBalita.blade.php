<!-- Modal Berat Lahir Balita -->
<div class="modal fade" id="modalBeratLahirBalita" tabindex="-1" role="dialog" aria-labelledby="modalBeratLahirBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBeratLahirBalitaLabel">
                    <i class="fas fa-weight mr-2"></i>Berat Lahir
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formBeratLahirBalita">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">
                                    1. Berat Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="berat_lahir" placeholder="Contoh: 2950" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">
                                    2. Berat Badan Saat Ini (usia &gt;24 jam)
                                </label>
                                <input type="text" class="form-control" name="berat_badan_balita" placeholder="Contoh: 4500">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('berat-lahir-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
