<!-- Modal Hepatitis Balita -->
<div class="modal fade" id="modalHepatitisBalita" tabindex="-1" role="dialog" aria-labelledby="modalHepatitisBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalHepatitisBalitaLabel">
                    <i class="fas fa-shield-virus mr-2"></i>Riwayat Imunisasi Hepatitis B
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formHepatitisBalita">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold d-block">
                            1. Apakah anak sudah mendapatkan imunisasi Hepatitis B saat bayi? <span class="text-danger">*</span>
                        </label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="hepatitis_balita" id="hepatitis_balita_sudah" value="Sudah" required>
                                <label class="form-check-label" for="hepatitis_balita_sudah">Sudah</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="hepatitis_balita" id="hepatitis_balita_belum" value="Belum" required>
                                <label class="form-check-label" for="hepatitis_balita_belum">Belum</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('hepatitis-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
