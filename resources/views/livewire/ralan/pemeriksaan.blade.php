<div>
    <form wire:submit.prevent='simpanPemeriksaan'>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Subjek</label>
                <textarea wire:model.defer='keluhan' class="form-control" name="" id="" rows="2">
                    {{ old('keluhan', $keluhan ?? 'Pasien datang dengan keluhan') }}
                </textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="">Objek</label>
                <textarea wire:model.defer='pemeriksaan' class="form-control" name="" id="" rows="2"> {{ old('pemeriksaan', $pemeriksaan ?? 'KU Baik, Composmentis
Thorax : Cor S1-2 intensitas normal, reguler, bising (-)
Pulmo : SDV +/+ ST -/-
Abdomen : Supel, NT(-), peristaltik (+) normal.
EXT : Oedem -/-') }} </textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Asesmen</label>
                <textarea wire:model.defer='penilaian' class="form-control" name="" id="" rows="2">
                    {{ old('penilaian', $penilaian ?? '-') }}
                </textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="">Instruksi</label>
                <textarea wire:model.defer='instruksi' class="form-control" name="" id="" rows="2">
                    {{ old('instruksi', $instruksi ?? 'Istirahat Cukup, PHBS') }}
                </textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Plan</label>
                <textarea wire:model.defer='rtl' class="form-control" name="" id="" rows="1">
                   {{ old('rtl', $rtl ?? 'Edukasi Kesehatan') }} 
                </textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="">Alergi</label>
                <textarea wire:model.defer='alergi' class="form-control" name="" id="" rows="1">
                    {{ old('alergi', $alergi ?? 'Tidak Ada') }}
                </textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="">Tensi</label>
                <input type="text" wire:model.defer='tensi' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
            <div class="form-group col-md-3">
                <label for="">Berat</label>
                <input type="text" wire:model.defer='berat' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="">
            </div>
            <div class="form-group col-md-3">
                <label for="">Tinggi</label>
                <input type="text" wire:model.defer='tinggi' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="">
            </div>
            <div class="form-group col-md-3">
                <label for="">Lingkar Perut</label>
                <input type="text" wire:model.defer='lingkar' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="" {{ old('lingkar', $lingkar ?? '72') }} >
            </div>
        </div>
        <div class="row">
           
             <div class="form-group col-md-4">
                <label for="">Suhu</label>
                <input type="text" wire:model.defer='suhu' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="" {{ old('suhu', $suhu ?? '36.5') }} >
            </div>
            <div class="form-group col-md-4">
                <label for="">Nadi (per Menit)</label>
                <input type="text" wire:model.defer='nadi' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="" {{ old('nadi', $nadi ?? '82') }} >
            </div>
            <div class="form-group col-md-4">
                <label for="">Respirasi</label>
                <input type="text" wire:model.defer='respirasi' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="" {{ old('respirasi', $respirasi ?? '20') }} >
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="">SPO2</label>
                <input type="text" wire:model.defer='spo2' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="" {{ old('spo2', $spo2 ?? '96') }}  >
            </div>
            <div class="form-group col-md-4">
                <label for="">GCS (E, V, M)</label>
                <input type="text" wire:model.defer='gcs' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="" {{ old('gcs', $gcs ?? '15') }} >
            </div>
            <div class="form-group col-md-4">
                <label for="">Kesadaran</label>
                <select class="form-control" wire:model.defer='kesadaran' name="" id="">
                    @if(!$kesadaran) <option value="{{$kesadaran}}">{{$kesadaran}}</option> @endif
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirium">Delirium</option>
                    <option value="Somnolence">Somnolence</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Coma">Coma</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="">Evaluasi</label>
            <textarea wire:model.defer='evaluasi' class="form-control" name="" id="" rows="1">
                 {{ old('evaluasi', $evaluasi ?? 'Kontrol Ulang Jika belum Ada Perubahan') }}
            </textarea>
        </div>
        <div class="d-flex flex-row-reverse">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
    <h5 class="pt-4">Riwayat Pemeriksaan</h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-inverse" style="width: 100%">
                <tr>
                    <th>PPA</th>
                    <th>Keluhan</th>
                    <th>Pemeriksaan</th>
                    <th>Tensi</th>
                    <th>Nadi</th>
                    <th>Suhu</th>
                    <th>RR</th>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($listPemeriksaan as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->keluhan }}</td>
                    <td>{{ $item->pemeriksaan }}</td>
                    <td>{{ $item->tensi }}</td>
                    <td>{{ $item->nadi }}</td>
                    <td>{{ $item->suhu_tubuh }}</td>
                    <td>{{ $item->respirasi }}</td>
                    <td>
                        <div class="btn-group">
                            <button
                                wire:click='$emit("openModalEditPemeriksaan", "{{$item->no_rawat}}", "{{$item->tgl_perawatan}}","{{$item->jam_rawat}}")'
                                class="btn btn-sm btn-warning">Edit</button>
                            <button
                                wire:click='confirmHapus("{{$item->no_rawat}}", "{{$item->tgl_perawatan}}","{{$item->jam_rawat}}")'
                                class="btn btn-sm btn-danger">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Data Pemeriksaan Kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@section('js')
<script>
    window.addEventListener('swal:pemeriksaan', function(e) {
            Swal.fire(e.detail);
        });
</script>
@endsection