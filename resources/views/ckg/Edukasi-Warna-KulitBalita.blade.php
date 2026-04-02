<!-- Modal Edukasi Warna Kulit dan Tinja Bayi -->
<div class="modal fade" id="modalEdukasiWarnaKulitBalita" tabindex="-1" role="dialog" aria-labelledby="modalEdukasiWarnaKulitBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEdukasiWarnaKulitBalitaLabel">
                    <i class="fas fa-child mr-2"></i>Edukasi Warna Kulit dan Tinja Bayi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdukasiWarnaKulitBalita">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    1. Apakah dilakukan edukasi skrining bayi kuning dan warna tinja bayi? <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="edukasi_warna_kulit" id="edukasi_warna_kulit_ya" value="Ya" required>
                                        <label class="form-check-label" for="edukasi_warna_kulit_ya">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="edukasi_warna_kulit" id="edukasi_warna_kulit_tidak" value="Tidak" required>
                                        <label class="form-check-label" for="edukasi_warna_kulit_tidak">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold d-block">
                                    2. Berapa Hasil Kramer pada Bayi Kuning? <span class="text-danger">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="hasil_kreamer" id="hasil_kreamer_normal" value="Normal" required>
                                        <label class="form-check-label" for="hasil_kreamer_normal">Normal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="hasil_kreamer" id="hasil_kreamer_1_3" value="Bayi Kuning Kramer 1-3" required>
                                        <label class="form-check-label" for="hasil_kreamer_1_3">Bayi Kuning Kramer 1-3</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="hasil_kreamer" id="hasil_kreamer_lebih_3" value="Bayi Kuning Kramer >3" required>
                                        <label class="form-check-label" for="hasil_kreamer_lebih_3">Bayi Kuning Kramer &gt;3</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('edukasi-warna-kulit-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
