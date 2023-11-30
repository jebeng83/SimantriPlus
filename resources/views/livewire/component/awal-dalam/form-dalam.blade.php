<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_dalam" model='tanggal_dalam' />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="informasi">Anamnesis</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="anamnesis" model='anamnesis'>
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="hubungan" type="text" model='hubungan' />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama" model='keluhan_utama' />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps" model='rps' />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Pengobatan" id="rpo" model='rpo' />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd" model='rpd' />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.input label="Riwayat Alergi" id="alergi" type="text" model='alergi' />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input label="Status Nutrisi" id="nutrisi" type="text" model='nutrisi' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="TD (mmHg)" id="td" type="text" model='td' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="Nadi (x/menit)" id="nadi" type="text" model='nadi' />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.input label="Suhu (C)" id="suhu" type="text" model='suhu' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="RR (x/menit)" id="rr" type="text" model='rr' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="BB (Kg)" id="bb" type="text" model='bb' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="GCS (E,V,M)" id="gcs" type="text" model='gcs' />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="Kondisi Umum" id="kondisi" model='kondisi' />
            </div>
        </div>
        <h6 class="mt-3 text-bold">III. STATUS KELAINAN</h6>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="kepala">Kepala</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="kepala" model='kepala'>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="keterangan_kepala" type="text" model='keterangan_kepala' />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="abdomen">Abdomen</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="abdomen" model='abdomen'>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="keterangan_abdomen" type="text" model='keterangan_abdomen' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="ekstrimis">Ekstrimis</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="ekstremitas" model='ekstremitas'>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="keterangan_ekstremitas" type="text" model='keterangan_ekstremitas' />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="thoraks">Thoraks</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="thoraks" model='thoraks'>
                                <option value="Normal">Normal</option>
                                <option value="Abnormal">Abnormal</option>
                                <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="keterangan_thoraks" type="text" model='keterangan_thoraks' />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="Lainnya" id="lainnya" model='lainnya' />
            </div>
        </div>
        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab" model='lab' />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Radiologi" id="rad" model='rad' />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Penunjang lainnya" id="penunjanglain" model='penunjanglain' />
            </div>
        </div>
        <h6 class="mt-3 text-bold">V. DIAGNOSIS/ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Assesmen Kerja" id="diagnosis" model='diagnosis' />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Assesmen Banding" id="diagnosis2" model='diagnosis2' />
            </div>
        </div>
        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Permasalahan" id="permasalahan" model='permasalahan' />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Terapi/Pengobatan" id="terapi" model='terapi' />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="Tindakan/Rencana Tindakan" id="tindakan" model='tindakan' />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="VII. EDUKASI" id="edukasi" model='edukasi' />
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-md-6 mt-3">
                <button type="reset" wire:click='confirmHapus' class="btn btn-danger btn-block">Hapus</button>
            </div>
            <div class="col-6 col-md-6 mt-3">
                <button type="submit" class="btn btn-primary btn-block">{{ $editMode ? 'Ubah' : 'Simpan' }}</button>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    $(".btn-awal-dalam").on('click', function(){
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-dalam").modal('show');
    });
</script>
@endpush
