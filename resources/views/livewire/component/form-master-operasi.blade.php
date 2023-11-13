<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="nama_operasi">Nama Operasi</label>
                    <input wire:model.defer='nama_operasi' type="text" class="form-control @error('nama_operasi') is-invalid @enderror" data-target="#nama_operasi" />
                    @error('nama_operasi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="diagnosa_preop">Diagnosa Pra Bedah</label>
                    <input wire:model.defer='diagnosa_preop' id="diagnosa_preop" class="form-control @error('diagnosa_preop') is-invalid @enderror" type="text" name="diagnosa_preop" />
                    @error('diagnosa_preop') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="diagnosa_postop">Diagnosa Pasca Bedah</label>
                    <input wire:model.defer='diagnosa_postop' id="diagnosa_postop" class="form-control @error('diagnosa_postop') is-invalid @enderror" type="text" name="diagnosa_postop" />
                    @error('diagnosa_postop') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-6">
                <div class="form-group">
                    <label for="jaringan_dieksisi">Jaringan Dieksisi</label>
                    <input wire:model.defer='jaringan_dieksisi' id="jaringan_dieksisi" class="form-control @error('jaringan_dieksisi') is-invalid @enderror" type="text" name="jaringan_dieksisi">
                    @error('jaringan_dieksisi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-6 col-sm-6">
                <div class="form-group">
                    <label for="permintaan_pa">Permintaan PA</label>
                    <select wire:model.defer='permintaan_pa' id="permintaan_pa" class="form-control @error('permintaan_pa') is-invalid @enderror" type="text" name="permintaan_pa">
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                    @error('permintaan_pa') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                  <label for="laporan_operasi">Laporan Operasi</label>
                  <textarea wire:model.defer='laporan_operasi' class="form-control @error('laporan_operasi') is-invalid @enderror" name="laporan_operasi" id="laporan_operasi" rows="5"></textarea>
                    @error('laporan_operasi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-6">
                <button wire:click='resetInput' type="reset" class="btn btn-block btn-danger">Reset</button>
            </div>
            <div class="col-6 col-sm-6">
                <button type="submit" class="btn btn-block @if($modeEdit) btn-info @else btn-primary @endif">@if($modeEdit) Ubah @else Simpan @endif</button>
            </div>
        </div>
    </form>
    
</div>

@push('js')
<script>
    Livewire.on('edit', e => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    })
</script>
@endpush
