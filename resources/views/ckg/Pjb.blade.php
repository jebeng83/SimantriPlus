<!-- Modal Pemeriksaan Jantung Bawaan (PJB) Balita -->
<div class="modal fade" id="modalPjbBalita" tabindex="-1" role="dialog" aria-labelledby="modalPjbBalitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPjbBalitaLabel">
                    <i class="fas fa-heartbeat mr-2"></i>Pemeriksaan Jantung Bawaan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formPjbBalita">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">
                                    1. Pemeriksaan PJB - Tangan kanan (Oksimeter) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="pjb_tangan_kanan" placeholder="Contoh: 99" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">
                                    2. Pemeriksaan PJB - Kaki <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="pjb_kaki" placeholder="Contoh: 99" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="simpanDataForm('pjb-balita')" data-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
