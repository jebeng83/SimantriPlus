<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.input-datetime label="Tanggal" id="tanggal_tht" model="tanggal_tht" />
            </div>
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="informasi">Anamnesis</label>
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
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Keluhan Utama" id="keluhan_utama" model="keluhan_utama" />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Sekarang" id="rps" model="rps" />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penggunaan Obat" id="rpo" model="rpo" />
            </div>
            <div class="col-6 col-md-6">
                <x-ui.textarea label="Riwayat Penyakit Dahulu" id="rpd" model="rpd" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.input label="Riwayat Alergi" id="alergi" model="alergi" />
            </div>
        </div>

        <h6 class="mt-3 text-bold">II. PEMERIKSAAN FISIK</h6>
        <div class="row">
            <div class="col-6 col-md-4">
                <x-ui.input label="TD (mmHg)" id="td" model="td" />
            </div>
            <div class="col-6 col-md-4">
                <x-ui.input label="TB (cm)" id="tb" model="tb" />
            </div>
            <div class="col-6 col-md-4">
                <x-ui.input label="BB (Kg)" id="bb" model="bb" />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <x-ui.input label="Suhu (C)" id="suhu" model="suhu" />
            </div>
            <div class="col-6 col-md-4">
                <x-ui.input label="Nadi (x/menit)" id="nadi" model="nadi" />
            </div>
            <div class="col-6 col-md-4">
                <x-ui.input label="RR (x/menit)" id="rr" model="rr" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.input label="Nyeri" id="nyeri" model="nyeri" />
            </div>
            <div class="col-md-6">
                <x-ui.input label="Status Nutrisi" id="nutrisi" model="nutrisi" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="Kondisi Umum" id="kondisi" model="kondisi" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">III. STATUS LOKALIS</h6>
        <div class="row">
            <div class="col-12 col-md-12">
                <img class="img-fluid" src="{{ asset('assets/medis/medis-tht.png') }}" alt="medis-tht">
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea label="Keterangan" id="ket_lokalis" model="ket_lokalis" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">IV. PEMERIKSAAN PENUNJANG</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Laboratorium" id="lab" model="lab" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Radiologi" id="rad" model="rad" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Tes Pendengaran" id="tes_pendengaran" model="tes_pendengaran" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Penunjang Lainnya" id="penunjang" model="penunjang" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">V. DIAGNOSIS/ASESMEN</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Asesment Kerja" id="diagnosis" model="diagnosis" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Asesment Banding" id="diagnosisbanding" model="diagnosisbanding" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">VI. PERMASALAHAN & TATALAKSANA</h6>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Permasalahan" id="permasalahan" model="permasalahan" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Terapi/Pengobatan" id="terapi" model="terapi" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <x-ui.textarea label="Tindakan/Rencana Tindakan" id="tindakan" model="tindakan" />
            </div>
            <div class="col-md-6">
                <x-ui.textarea label="Tatalaksana Lainnya" id="tatalaksana" model="tatalaksana" />
            </div>
        </div>
        <h6 class="mt-3 text-bold">VII. EDUKASI</h6>
        <div class="row">
            <div class="col-12 col-md-12">
                <x-ui.textarea id="edukasi" model="edukasi" />
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
    $(".btn-awal-tht").on('click', function(){
        var id = $(this).attr('id');
        @this.set('no_rawat', id);
        $("#modal-awal-medis-tht").modal('show');
    });
</script>
@endpush
