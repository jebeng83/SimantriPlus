<div @if($isCollapsed) class="card card-info collapsed-card" @elseif($isMaximized) class="card card-info card-maximized" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-flask mr-1"></i> Pemeriksaan </h3>
        <div class="card-tools">
            <button type="button" wire:click="expanded" class="btn btn-tool" data-card-widget="maximize" >
                <i wire:ignore class="fas fa-lg fa-expand"></i>     
            </button>
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent='simpanPemeriksaan'>
            <div class="row">
                <div class="form-group col-md-6">
                  <label for="">Subjek</label>
                  <textarea wire:model.defer='keluhan' class="form-control" name="" id="" rows="3"></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="">Objek</label>
                    <textarea wire:model.defer='pemeriksaan' class="form-control" name="" id="" rows="3"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                  <label for="">Asesmen</label>
                  <textarea wire:model.defer='penilaian' class="form-control" name="" id="" rows="3"></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="">Instruksi</label>
                    <textarea wire:model.defer='instruksi' class="form-control" name="" id="" rows="3"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                  <label for="">Plan</label>
                  <textarea wire:model.defer='rtl' class="form-control" name="" id="" rows="3"></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="">Alergi</label>
                    <textarea wire:model.defer='alergi' class="form-control" name="" id="" rows="3"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="">Suhu (C)</label>
                    <input type="text" wire:model.defer='suhu' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group col-md-4">
                    <label for="">Berat (Kg)</label>
                    <input type="text" wire:model.defer='berat' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group col-md-4">
                    <label for="">Tinggi (Cm)</label>
                    <input type="text" wire:model.defer='tinggi' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="">Tensi</label>
                    <input type="text" wire:model.defer='tensi' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group col-md-4">
                    <label for="">Nadi (per Menit)</label>
                    <input type="text" wire:model.defer='nadi' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group col-md-4">
                    <label for="">Respirasi</label>
                    <input type="text" wire:model.defer='respirasi' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                  <label for="">Imun Ke</label>
                  <select class="form-control" wire:model.defer='imun' name="" id="">
                    <option value="-">-</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="">GCS (E, V, M)</label>
                    <input type="text" wire:model.defer='gcs' class="form-control" name="" id="" aria-describedby="helpId" placeholder="">
                </div>
                <div class="form-group col-md-4">
                    <label for="">Kesadaran</label>
                    <select class="form-control" wire:model.defer='kesadaran' name="" id="">
                        <option>Compos Mentis</option>
                        <option>Somnolence</option>
                        <option>Sopor</option>
                        <option>Coma</option>
                    </select>
                </div>
            </div>
            <div class="d-flex flex-row-reverse">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@section('js')
    <script>
        window.addEventListener('swal:pemeriksaan', function(e) {
            Swal.fire(e.detail);
        });
    </script>
@endsection
