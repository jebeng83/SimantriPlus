<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-tooth mr-1"></i> Resep </h3>
        <div class="card-tools">
            {{-- <button type="button" class="btn btn-tool" wire:click="collapsed" data-card-widget="maximize">
                <i class="fas fa-lg fa-expand"></i>     
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="resep-tab" data-toggle="tab" data-target="#resep" type="button" role="tab" aria-controls="resep" aria-selected="true">Resep</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="copyresep-tab" data-toggle="tab" data-target="#copyresep" type="button" role="tab" aria-controls="copyresep" aria-selected="false">Resep Racikan</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="resep" role="tabpanel" aria-labelledby="resep-tab">
                <div class="callout-info">
                    <h5>Input Resep</h5>
                    <form wire:submit.prevent='save'>
                        <div class="containerResep">
                            @for($i=1; $i <= $jmlForm; $i++)
                            <div class="row pb-4">
                                <div wire:ignore class="col-md-5">
                                    <select name="obat[]" class="form-control obat w-100" id="obat" data-placeholder="Pilih Obat">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="jumlah[]" class="form-control" placeholder="Jumlah">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="aturan[]" class="form-control" placeholder="Aturan Pakai">
                                </div>
                            </div>
                            @endfor
                        </div>
                        <div class="d-flex flex-row-reverse">
                            <div class="col-md-2">
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
    
                            <div class="col-md-4">
                                <select id="iter" class="form-control" name="iter">
                                    <option value="-">Pilih jumlah iter</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-1">
                        <button class="btn btn-success" wire:click='tambahForm' role="button">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var poli = '';
            Livewire.hook('component.initialized', (component) => {
                poli = @this.data;
            });
            $('.obat').select2({
                placeholder: 'Pilih Obat',
                ajax: {
                    url: '/api/ralan/'+poli+'/obat'
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: item.nama,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
            window.livewire.on('tambahForm', () => {
                $('.obat').select2({
                    placeholder: 'Pilih Obat',
                    ajax: {
                        url: '/api/ralan/'+poli+'/obat'
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results:  $.map(data, function (item) {
                                    return {
                                        text: item.nama,
                                        id: item.id
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                });
            });
        });
    </script>
@endsection
