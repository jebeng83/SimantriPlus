<div>
    <form wire:submit.prevent='simpan'>
        <div class="form-group">
            <label for="fungsional">Diagnosis Fungsional</label>
            <input id="fungsional" wire:model.defer='fungsional' class="form-control @error('fungsional') is-invalid @enderror" type="text" name="fungsional" id="fungsional">
            @error('fungsional')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="medis">Diagnosis Medis</label>
            <input id="medis" wire:model.defer=medis class="form-control @error('medis') is-invalid @enderror" type="text" name="medis" id="medis">
            @error('medis')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <h5>INSTRUMEN UJI FUNGSI / PROSEDUR KFR</h5>
        <div class="form-group">
            <label for="hasil">Hasil Yang Didapat</label>
            <input id="hasil" wire:model.defer='hasil' class="form-control @error('hasil') is-invalid @enderror" type="text" name="hasil" id="hasil">
            @error('hasil')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="kesimpulan">Kesimpulan</label>
            <input id="kesimpulan" wire:model.defer='kesimpulan' class="form-control @error('kesimpulan') is-invalid @enderror" type="text" name="kesimpulan">
            @error('kesimpulan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="rekomendasi">Rekomendasi</label>
            <input id="rekomendasi" wire:model.defer='rekomendasi' class="form-control @error('rekomendasi') is-invalid @enderror" type="text" name="rekomendasi">
            @error('rekomendasi')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex flex-row mx-auto">
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
    <div class="table-responsive mt-3">
        <table wire:init='loadDatas' class="table table-light">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Diagnosis Fungsional</th>
                    <th>Diagnosis Medis</th>
                    <th>Hasil Yang Didapat</th>
                    <th>Kesimpulan</th>
                    <th>Rekomendasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datas as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->diagnosis_fungsional }}</td>
                    <td>{{ $data->diagnosis_medis }}</td>
                    <td>{{ $data->hasil_didapat }}</td>
                    <td>{{ $data->kesimpulan }}</td>
                    <td>{{ $data->rekomedasi }}</td>
                    <td>
                        {{-- <button class="btn btn-sm btn-primary" wire:click='edit({{ $data->id }})'>Edit</button> --}}
                        <button class="btn btn-sm btn-danger" wire:click='confirmDelete("{{ $data->no_rawat }}")'>Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
