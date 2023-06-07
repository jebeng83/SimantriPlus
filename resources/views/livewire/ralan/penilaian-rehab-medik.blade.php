<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Anamnesis</label>
                <select class="form-control" wire:model.defer='data.kesadaran' >
                    <option value="">Pilih</option>
                    <option value="Autoanamnesis">Autoanamnesis</option>
                    <option value="Alloanamnesis">Alloanamnesis</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="">Hubungan</label>
                <input class="form-control" wire:model.defer='data.hubungan' />
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="">Keluhan Utama</label>
            <textarea wire:model.defer='data.keluhan_utama' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat Penyakit Sekarang</label>
            <textarea wire:model.defer='data.rps' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Kondisi kemampuan fungsional</label>
            <textarea wire:model.defer='data.kkf' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat Penyakit Dahulu</label>
            <textarea wire:model.defer='data.rpd' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat pengobatan dan rehabilitasi</label>
            <textarea wire:model.defer='data.rpor' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat penyakit dahulu</label>
            <textarea wire:model.defer='data.rpd' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat hobi / pekerjaan</label>
            <textarea wire:model.defer='data.rhp' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat psiko-sosio-ekonomi</label>
            <textarea wire:model.defer='data.rs' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Riwayat keluarga</label>
            <textarea wire:model.defer='data.rkh' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Harapan pasien</label>
            <textarea wire:model.defer='data.harapan_pasien' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <h5>PEMERIKSAAN FISIK</h5>
        <div class="row">
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">GCS</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.gcs"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">TD</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.td"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">HR</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.hr"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">RR</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.rr"  />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">T</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.t"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-5 col-form-label">SpO2</label>
                <div class="col-sm-7">
                <input type="number" class="form-control" wire:model.defer="data.sp02"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">BB</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.bb"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">TB</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.tb"  />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">IMT</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.imt"  />
                </div>
            </div>
            <div class="form-group row col-3">
                <label for="klinis" class="col-sm-4 col-form-label">LK</label>
                <div class="col-sm-8">
                <input type="number" class="form-control" wire:model.defer="data.lk"  />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Postur</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" wire:model.defer="data.postur"  />
                </div>
            </div>
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Ambulasi</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" wire:model.defer="data.ambulasi"  />
                </div>
            </div>
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Ekstrimis dominan</label>
                <div class="col-sm-9">
                    <select class="form-control" wire:model.defer='data.ekstrimitas_dominan' >
                        <option value="">Pilih</option>
                        <option value="Kanan">Kanan</option>
                        <option value="Kiri">Kiri</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="">Kepala / Leher</label>
            <textarea wire:model.defer='data.kepala_leher' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Thorax Paru</label>
            <textarea wire:model.defer='data.thorax_paru' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Thorax Jantung</label>
            <textarea wire:model.defer='data.thorax_jantung' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Abdomen</label>
            <textarea wire:model.defer='data.abdomen' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Ekstremitas</label>
            <textarea wire:model.defer='data.ekstremitas' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <h5>Kepala & Leher</h6>
        <div class="form-group col-md-12">
            <label for="">Look (status lokalis)</label>
            <textarea wire:model.defer='data.kep_look' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Feel (status lokalis)</label>
            <textarea wire:model.defer='data.kep_feel' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">ROM & MMT leher</label>
            <textarea wire:model.defer='data.kep_rom_mmt' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Special Test</label>
            <textarea wire:model.defer='data.kep_special_test' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Saraf cranialis</label>
            <textarea wire:model.defer='data.kep_saraf_cranialis' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Sensorik</label>
            <textarea wire:model.defer='data.kep_sensorik' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <h5>Trunk</h5>
        <div class="form-group col-md-12">
            <label for="">Look (status lokalis)</label>
            <textarea wire:model.defer='data.trunk_look' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Feel (status lokalis)</label>
            <textarea wire:model.defer='data.trunk_feel' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">ROM & MMT leher</label>
            <textarea wire:model.defer='data.trunk_rom_mmt' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Special test</label>
            <textarea wire:model.defer='data.trunk_special_test' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Sensorik</label>
            <textarea wire:model.defer='data.trunk_sensorik' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <h5>AGA</h5>
        <div class="form-group col-md-12">
            <label for="">Look (status lokalis)</label>
            <textarea wire:model.defer='data.aga_look' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Feel (status lokalis)</label>
            <textarea wire:model.defer='data.aga_feel' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">ROM & MMT</label>
            <select wire:model.defer='data.aga_room_mmt' class="form-control" name="">
                <option value="">Pilih</option>
                <option value="Shoulder">Shoulder</option>
                <option value="Elbow">Elbow</option>
                <option value="Wrist">Wrist</option>
                <option value="Finger">Finger</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="">Special test</label>
            <textarea wire:model.defer='data.aga_special_test' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Sensorik</label>
            <textarea wire:model.defer='data.aga_sensorik' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">DTR</label>
            <textarea wire:model.defer='data.aga_dtr' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Spastisitas</label>
            <textarea wire:model.defer='data.aga_spastisitas' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Reflek patologis</label>
            <textarea wire:model.defer='data.aga_reflek_patologis' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <h5>AGB</h5>
        <div class="form-group col-md-12">
            <label for="">Look (status lokalis)</label>
            <textarea wire:model.defer='data.agb_look' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Feel (status lokalis)</label>
            <textarea wire:model.defer='data.agb_feel' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">ROM & MMT</label>
            <select wire:model.defer='data.agb_room_mmt' class="form-control" name="">
                <option value="">Pilih</option>
                <option value="Shoulder">Shoulder</option>
                <option value="Elbow">Elbow</option>
                <option value="Wrist">Wrist</option>
                <option value="Finger">Finger</option>
            </select>
        </div>
        <div class="form-group col-md-12">
            <label for="">Special test</label>
            <textarea wire:model.defer='data.agb_special_test' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Sensorik</label>
            <textarea wire:model.defer='data.agb_sensorik' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">DTR</label>
            <textarea wire:model.defer='data.agb_dtr' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Spastisitas</label>
            <textarea wire:model.defer='data.agb_spastisitas' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Reflek patologis</label>
            <textarea wire:model.defer='data.agb_reflek_patologis' class="form-control" name="" id="" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Pemeriksaan Fisik Khusus Lain</label>
            <textarea wire:model.defer='data.pemeriksaan_fisik' class="form-control" name="" id="" rows="2"></textarea>
        </div>
        <div class="row">
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Basic ADL</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" wire:model.defer="data.basic_adl"  />
                </div>
            </div>
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Kognisi</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-6">
                            <select wire:model.defer='data.kognisi' class="form-control">
                                <option value="">Pilih</option>
                                <option value="MOCA">MOCA</option>
                                <option value="MMSE">MMSE</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" wire:model.defer='data.kognisi_ket' placeholder="Keterangan" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row col-12">
                <label for="klinis" class="col-sm-3 col-form-label">Count test</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" wire:model.defer="data.count_test"  />
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" wire:model.defer='data.chest_expansion' placeholder="keterangan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12">
            <label for="">Balance</label>
            <textarea wire:model.defer='data.balance' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Transfer</label>
            <textarea wire:model.defer='data.transfer' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Coordination</label>
            <textarea wire:model.defer='data.coordination' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Pemeriksaan funsional lainnya</label>
            <textarea wire:model.defer='data.fungsional_lainnya' class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Pemeriksaan Penunjang</label>
            <textarea wire:model.defer='data.pemeriksaan_penunjang' class="form-control" rows="2"></textarea>
        </div>
        <h6>DIAGNOSIS</h6>
        <div class="form-group col-md-12">
            <label for="">Klinis</label>
            <textarea wire:model.defer='data.diag_klinis' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Anatomis</label>
            <textarea wire:model.defer='data.diag_anatomis' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Etiologis</label>
            <textarea wire:model.defer='data.diag_etiologis' class="form-control" rows="1"></textarea>
        </div>
        <h5>ICF</h5>
        <div class="form-group col-md-12">
            <label for="">Body function</label>
            <textarea wire:model.defer='data.icf_function' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Body structure</label>
            <textarea wire:model.defer='data.icf_structure' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Actifity limitation & participation restriction</label>
            <textarea wire:model.defer='data.icf_activity' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Environmental factor</label>
            <textarea wire:model.defer='data.icf_enviromental' class="form-control" rows="1"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Personal Factor</label>
            <textarea wire:model.defer='data.icf_personal_factor' class="form-control" rows="1"></textarea>
        </div>
        <h5>PROGNOSIS</h5>
        <div class="row">
            <div class="form-group row col-6">
                <label for="klinis" class="col-sm-4 col-form-label">Ad vitam</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" wire:model.defer="data.progno_vitam"  />
                </div>
            </div>
            <div class="form-group row col-6">
                <label for="klinis" class="col-sm-4 col-form-label">Ad sanactionam</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" wire:model.defer="data.progno_sanactionam"  />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group row col-6">
                <label for="" class="col-sm-4 col-form-label">Ad functionam</label>
                <div class="col-sm-8">
                <select class="form-control" wire:model.defer='data.progno_functionam'>
                    <option value="Transfer">Transfer</option>
                    <option value="Ambulasi">Ambulasi</option>
                    <option value="ADL lainnya">ADL lainnya</option>
                </select>
                </div>
            </div>
            <div class="col-6">
                <input type="text" class="form-control" wire:model.defer="data.progno_ket"  />
            </div>
        </div>
        <h5>GOAL</h5>
        <div class="form-group col-md-12">
            <label for="">Jangka pendek</label>
            <textarea wire:model.defer='data.goal_pendek' class="form-control" rows="2"></textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="">Jangka panjang</label>
            <textarea wire:model.defer='data.goal_panjang' class="form-control" rows="2"></textarea>
        </div>
        <div class="row">
            <div class="form-group col-md-5">
                <label for="">Daftar Masalah</label>
                <textarea wire:model.defer='data.masalah' class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group col-md-7">
                <label for="">Manajemen (Rencana Diagnosis, Terapi, dan Monitoring)</label>
                <textarea wire:model.defer='data.rencana' class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button class="btn btn-sm">Simpan</button>
        </div>
    </form>
</div>
