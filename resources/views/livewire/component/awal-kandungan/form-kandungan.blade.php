<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_kandungan" model='tanggal_kandungan' />
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
                <x-ui.textarea label="Riwayat Penyakit Keluarga" id="rpk" model='rpk' />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd" model='rpd' />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Pengobatan" id="rpo" model='rpo' />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.input label="Riwayat Alergi" id="alergi" type="text" model='alergi' />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.select label="Keadaan Umum" id="keadaan" model='keadaan'>
                    <option value="Sehat">Sehat</option>
                    <option value="Sakit Ringan">Sakit Ringan</option>
                    <option value="Sakit Sedang">Sakit Sedang</option>
                    <option value="Sakit Berat">Sakit Berat</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Kesadaran" id="kesadaran" model='kesadaran'>
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Somnolen">Somnolen</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Koma">Koma</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="GCS (E,V,M)" id="gcs" type="text" model='gcs' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="TB (Cm)" id="tb" type="text" model='tb' />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.input label="BB (Kg)" id="bb" type="text" model='bb' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="TD (mmHg)" id="td" type="text" model='td' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="Nadi (x/menit)" id="nadi" type="text" model='nadi' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="RR (x/menit)" id="rr" type="text" model='rr' />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <x-ui.input label="Suhu (C)" id="suhu" type="text" model='suhu' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="SpO2 (%)" id="spo" type="text" model='spo' />
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
                <x-ui.select label="Gental & Anus" id="genital" model='genital'>
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
        <h6 class="mt-3 text-bold">III. STATUS OBSTETRI/GINEKOLOGI</h6>
        <div class="row">
            <div class="col-6 col-md-2">
                <x-ui.input label="TFU (Cm)" id="tfu" type="text" model='tfu' />
            </div>
            <div class="col-6 col-md-2">
                <x-ui.input label="TBJ (gram)" id="tbj" type="text" model='tbj' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.input label="His (x/10 Menit)" id="his" type="text" model='his' />
            </div>
            <div class="col-6 col-md-3">
                <x-ui.select label="Kontraksi" id="kontraksi" model='kontraksi'>
                    <option value="Ada">Ada</option>
                    <option value="Tidak">Tidak</option>
                </x-ui.select>
            </div>
            <div class="col-6 col-md-2">
                <x-ui.input label="DJJ (Dpm)" id="djj" type="text" model='djj' />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Inspeksi" id="inspeksi" model='inspeksi' />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="VT" id="vt" model='vt' />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Inspekulo" id="inspekulo" model='inspekulo' />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="RT" id="rt" model='rt' />
            </div>
        </div>
        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-4">
                <x-ui.textarea label="Ultrasonografi" id="ultra" model='ultra' />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Kardiotografi" id="kardio" model='kardio' />
            </div>
            <div class="col-md-4">
                <x-ui.textarea label="Laboratorium" id="lab" model='lab' />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="V. DIAGNOSIS/ASESMEN" id="diagnosis" model='diagnosis' />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="VI. TATALAKSANA" id="tata" model='tata' />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="VII. KONSUL/RUJUK" id="konsul" model='konsul' rows='5' />
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
    $(".btn-awal-kandungan").on('click', function(){
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-kandungan").modal('show');
    });
</script>
@endpush
