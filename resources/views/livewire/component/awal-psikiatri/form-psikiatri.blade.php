<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_psikiatri" model="tanggal_psikiatri" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="anamnesis">Anamnesis</label>
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <x-ui.select id="anamnesis" model="anamnesis">
                                <option value="Autoanamnesis">Autoanamnesis</option>
                                <option value="Alloanamnesis">Alloanamnesis</option>
                            </x-ui.select>
                        </div>
                        <div class="col-6 col-md-6">
                            <x-ui.input id="hubungan" model="hubungan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="mt-3 text-bold">I. RIWAYAT KESEHATAN</h6>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama" model="keluhan_utama" />
            </div>
            <div class="col-12 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Fisik & Neurologi" id="rpk" model="rpk" />
            </div>
            <div class="col-12 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd" model="rpd" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-ui.textarea label="Riwayat NAPZA" id="rpo" model="rpo" />
            </div>
            <div class="col-12 col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. STATUS PSIKIATRI</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Penampilan" id="penampilan" model="penampilan" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Gangguan Persepsi" id="gangguan_persepsi" model="gangguan_persepsi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Pembicaraan" id="pembicaraan" model="pembicaraan" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Proses Pikir & Isi Pikir" id="proses_pikir" model="proses_pikir" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Psikomotor" id="psikomotor" model="psikomotor" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Pengendalian Impuls" id="pengendalian_impuls" model="pengendalian_impuls" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Sikap" id="sikap" model="sikap" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Tilikan" id="tilikan" model="tilikan" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Mood / Afek" id="mood" model="mood" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Reality Testing Ability" id="rta" model="rta" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="Fungsi Kognitif" id="fungsi_kognitif" model="fungsi_kognitif" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">III. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.input label="GCS (E,V,M)" id="gcs" model="gcs" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="TB (Cm)" id="tb" model="tb" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="BB (Kg)" id="bb" model="bb" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="TD (mmHg)" id="td" model="td" />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.input label="Nadi (x/menit)" id="nadi" model="nadi" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="RR (x/menit)" id="rr" model="rr" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="Suhu (C)" id="suhu" model="suhu" />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="SpO2 (%)" id="spo" model="spo" />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.select label="Kepala" id="kepala" model='kepala'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Abdomen" id="abdomen" model='abdomen'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Gigi & Mulut" id="gigi" model='gigi'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Gental & Anus" id="gental" model='gental'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.select label="THT" id="tht" model='tht'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Ekstremitas" id="ekstremitas" model='ekstremitas'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Thoraks" id="thoraks" model='thoraks'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Kulit" id="kulit" model='kulit'>
                    <option value="Normal">Normal</option>
                    <option value="Abnormal">Abnormal</option>
                    <option value="Tidak Diperiksa">Tidak Diperiksa</option>
                </x-ui.select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="Keterangan" id="ket_fisik" model='ket_fisik' />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="IV. PEMERIKSAAN PENUNJANG" id="penunjang" model="penunjang" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="V. DIAGNOSIS/ASESMEN" id="diagnosis" model="diagnosis" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-ui.textarea label="VI. TATALAKSANA" id="tata" model="tata" />
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
    $(".btn-awal-psikiatri").on('click', function(){
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-psikiatri").modal('show');
    });
</script>
@endpush
