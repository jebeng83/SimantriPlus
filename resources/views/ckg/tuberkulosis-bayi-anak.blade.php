<!-- Modal Tuberkulosis Bayi & Anak Pra Sekolah -->
<div class="modal fade" id="modalTuberkulosisBayiAnak" tabindex="-1" role="dialog" aria-labelledby="modalTuberkulosisBayiAnakLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTuberkulosisBayiAnakLabel">
                    <i class="fas fa-lungs mr-2"></i>Tuberkulosis Bayi & Anak Pra Sekolah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTuberkulosisBayiAnak">
                    <!-- Pertanyaan 1 -->
                    <div class="form-group">
                        <label class="font-weight-bold">1. Apakah anak Anda pernah atau sedang mengalami batuk yang tidak sembuh-sembuh selama lebih dari 2 minggu? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="batuk_lama" id="batuk_lama_ya" value="Ya" required>
                                <label class="form-check-label" for="batuk_lama_ya">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="batuk_lama" id="batuk_lama_tidak" value="Tidak" required>
                                <label class="form-check-label" for="batuk_lama_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <!-- Pertanyaan 2 -->
                    <div class="form-group">
                        <label class="font-weight-bold">2. Apakah berat badan anak Anda turun tanpa alasan yang jelas? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="berat_turun_tbc" id="berat_turun_tbc_ya" value="Ya" required>
                                <label class="form-check-label" for="berat_turun_tbc_ya">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="berat_turun_tbc" id="berat_turun_tbc_tidak" value="Tidak" required>
                                <label class="form-check-label" for="berat_turun_tbc_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <!-- Pertanyaan 3 -->
                    <div class="form-group">
                        <label class="font-weight-bold">3. Apakah berat badan anak Anda tidak naik dalam dua bulan terakhir? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="berat_tidak_naik" id="berat_tidak_naik_ya" value="Ya" required>
                                <label class="form-check-label" for="berat_tidak_naik_ya">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="berat_tidak_naik" id="berat_tidak_naik_tidak" value="Tidak" required>
                                <label class="form-check-label" for="berat_tidak_naik_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <!-- Pertanyaan 4 -->
                    <div class="form-group">
                        <label class="font-weight-bold">4. Apakah anak Anda tidak atau berkurang nafsu makan? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="nafsu_makan_berkurang" id="nafsu_makan_berkurang_ya" value="Ya" required>
                                <label class="form-check-label" for="nafsu_makan_berkurang_ya">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="nafsu_makan_berkurang" id="nafsu_makan_berkurang_tidak" value="Tidak" required>
                                <label class="form-check-label" for="nafsu_makan_berkurang_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <!-- Pertanyaan 5 -->
                    <div class="form-group">
                        <label class="font-weight-bold">5. Apakah Anda tinggal serumah atau sering bertemu dengan orang yang menderita Tuberkulosis (TBC) atau batuk berkepanjangan? <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kontak_tbc" id="kontak_tbc_ya" value="Ya" required>
                                <label class="form-check-label" for="kontak_tbc_ya">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kontak_tbc" id="kontak_tbc_tidak" value="Tidak" required>
                                <label class="form-check-label" for="kontak_tbc_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('tuberkulosis-bayi-anak')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>