<form id="form-riwayat-imunisasi-balita">
    <div class="card mb-3">
        <div class="card-body">
            <div class="form-group">
                <label class="font-weight-bold">
                    1. Apakah anak pernah memperoleh imunisasi saat usia 0 sd 24 bulan <span class="text-danger">*</span>
                </label>
                <select class="form-control custom-select" name="imunisasi_inti" id="imunisasi_inti" required onchange="toggleImunisasiBalitaInti(this.value)">
                    <option value="">Select...</option>
                    <option value="Ya">Ya</option>
                    <option value="Tidak">Tidak</option>
                </select>
            </div>
        </div>
    </div>

    <div id="imunisasi-inti-ya" style="display:none;">
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">
                        2. Apakah memiliki dan membawa catatan imunisasi anak ( buku KIA atau dokumen catatan lainnya) atau mengingat riwayat imunisasi anak?
                        <span class="text-danger">*</span>
                    </label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan" id="imunisasi_lanjutan" onchange="toggleImunisasiBalitaLanjutan(this.value)">
                        <option value="">Select...</option>
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="imunisasi-lanjutan-ya" style="display:none;">
            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">3. Apakah anak anda sudah pernah menerima imunisasi Hepatitis B pada usia &lt;24 jam? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_1" id="imunisasi_lanjutan_1">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">4. Apakah anak anda sudah pernah menerima imunisasi BCG pada usia &lt;1 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_2" id="imunisasi_lanjutan_2">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">5. Apakah anak anda sudah menerima imunisasi Polio tetes (OPV) dosis ke-1 pada usia 1 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_3" id="imunisasi_lanjutan_3">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">6. Apakah anak anda sudah menerima imunisasi DPT-HB-Hib dosis ke-1 pada usia 2 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_4" id="imunisasi_lanjutan_4">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">7. Apakah anak anda sudah menerima imunisasi Polio Tetes (OPV) dosis ke-2 pada usia 2 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_5" id="imunisasi_lanjutan_5">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">8. Apakah anak anda sudah menerima imunisasi PCV dosis ke-1 pada usia 2 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_6" id="imunisasi_lanjutan_6">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">9. Apakah anak anda sudah pernah menerima imunisasi Rotavirus dosis ke-1 pada usia 2 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_7" id="imunisasi_lanjutan_7">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">10. Apakah anak anda sudah menerima imunisasi DPT-HB-Hib dosis ke-2 pada usia 3 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_8" id="imunisasi_lanjutan_8">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">11. Apakah anak anda sudah menerima imunisasi Polio Tetes (OPV) dosis ke-3 pada usia 3 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_9" id="imunisasi_lanjutan_9">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">12. Apakah anak anda sudah pernah menerima Imunisasi PCV 2 pada usia 3 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_10" id="imunisasi_lanjutan_10">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">13. Apakah anak anda pernah menerima Imunisasi Rotavirus dosis ke-2 pada usia 3 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_11" id="imunisasi_lanjutan_11">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">14. Apakah anak anda sudah menerima imunisasi Polio tetes DPT-HB-Hib dosis ke-3 pada usia 4 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_12" id="imunisasi_lanjutan_12">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">15. Apakah anak anda sudah menerima imunisasi Polio tetes (OPV) dosis ke-4 pada usia 4 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_13" id="imunisasi_lanjutan_13">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">16. Apakah anak anda pernah menerima imunisasi Rotavirus dosis ke-3 pada usia 4 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_14" id="imunisasi_lanjutan_14">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">17. Apakah anak anda sudah menerima imunisasi polio suntik (IPV) dosis ke-1 pada usia 4 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_15" id="imunisasi_lanjutan_15">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">18. Apakah anak anda sudah menerima imunisasi Campak-Rubela dosis ke-1 pada usia 9 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_16" id="imunisasi_lanjutan_16">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">19. Apakah anak anda sudah menerima imunisasi lanjutan DPT-HB-Hib dosis ke-4 pada usia 18-24 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_17" id="imunisasi_lanjutan_17">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">20. Apakah anak anda sudah menerima imunisasi lanjutan campak-rubela dosis ke-2 pada usia 18-24 bulan? <span class="text-danger">*</span></label>
                    <select class="form-control custom-select" name="imunisasi_lanjutan_18" id="imunisasi_lanjutan_18">
                        <option value="">Select...</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>
            </div></div>
        </div>
    </div>
</form>

<script>
    function clearImunisasiSelects(ids) {
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
    }

    function setRequiredImunisasi(ids, required) {
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.required = required;
        });
    }

    function toggleImunisasiBalitaInti(val) {
        var sectionYa = document.getElementById('imunisasi-inti-ya');
        var lanjutanYa = document.getElementById('imunisasi-lanjutan-ya');

        if (val === 'Ya') {
            sectionYa.style.display = 'block';
            // Q2 wajib kalau inti Ya
            setRequiredImunisasi(['imunisasi_lanjutan'], true);
        } else {
            sectionYa.style.display = 'none';
            if (lanjutanYa) lanjutanYa.style.display = 'none';
            setRequiredImunisasi(['imunisasi_lanjutan'], false);

            clearImunisasiSelects(['imunisasi_lanjutan']);
            clearImunisasiSelects([
                'imunisasi_lanjutan_1','imunisasi_lanjutan_2','imunisasi_lanjutan_3','imunisasi_lanjutan_4','imunisasi_lanjutan_5','imunisasi_lanjutan_6',
                'imunisasi_lanjutan_7','imunisasi_lanjutan_8','imunisasi_lanjutan_9','imunisasi_lanjutan_10','imunisasi_lanjutan_11','imunisasi_lanjutan_12',
                'imunisasi_lanjutan_13','imunisasi_lanjutan_14','imunisasi_lanjutan_15','imunisasi_lanjutan_16','imunisasi_lanjutan_17','imunisasi_lanjutan_18'
            ]);
            setRequiredImunisasi([
                'imunisasi_lanjutan_1','imunisasi_lanjutan_2','imunisasi_lanjutan_3','imunisasi_lanjutan_4','imunisasi_lanjutan_5','imunisasi_lanjutan_6',
                'imunisasi_lanjutan_7','imunisasi_lanjutan_8','imunisasi_lanjutan_9','imunisasi_lanjutan_10','imunisasi_lanjutan_11','imunisasi_lanjutan_12',
                'imunisasi_lanjutan_13','imunisasi_lanjutan_14','imunisasi_lanjutan_15','imunisasi_lanjutan_16','imunisasi_lanjutan_17','imunisasi_lanjutan_18'
            ], false);
        }
    }

    function toggleImunisasiBalitaLanjutan(val) {
        var lanjutanYa = document.getElementById('imunisasi-lanjutan-ya');
        var ids = [
            'imunisasi_lanjutan_1','imunisasi_lanjutan_2','imunisasi_lanjutan_3','imunisasi_lanjutan_4','imunisasi_lanjutan_5','imunisasi_lanjutan_6',
            'imunisasi_lanjutan_7','imunisasi_lanjutan_8','imunisasi_lanjutan_9','imunisasi_lanjutan_10','imunisasi_lanjutan_11','imunisasi_lanjutan_12',
            'imunisasi_lanjutan_13','imunisasi_lanjutan_14','imunisasi_lanjutan_15','imunisasi_lanjutan_16','imunisasi_lanjutan_17','imunisasi_lanjutan_18'
        ];

        if (val === 'Ya') {
            lanjutanYa.style.display = 'block';
            setRequiredImunisasi(ids, true);
        } else {
            lanjutanYa.style.display = 'none';
            clearImunisasiSelects(ids);
            setRequiredImunisasi(ids, false);
        }
    }
</script>
