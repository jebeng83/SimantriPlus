<div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="resep-tab" data-toggle="tab" data-target="#resep" type="button"
                role="tab" aria-controls="resep" aria-selected="true">Resep</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="copyresep-tab" data-toggle="tab" data-target="#copyresep" type="button"
                role="tab" aria-controls="copyresep" aria-selected="false">Resep Racikan</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="resep" role="tabpanel" aria-labelledby="resep-tab">
            <div class="callout-info">
                <h5>Input Resep</h5>
                <form wire:submit.prevent='save'>
                    <div class="containerResep">
                        @for($i=1; $i <= $jmlForm; $i++) <div class="row row-{{$i}} pb-2">
                            <div wire:ignore class="col-md-6">
                                <select name="obat[]" class="form-control obat-{{$i}} w-100" id="obat-{{$i}}"
                                    data-placeholder="Pilih Obat">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="aturan[]" class="form-control"
                                            placeholder="Aturan Pakai">
                                        {{-- @if($i > 1)
                                        <div class="input-group-append">
                                            <a class="btn btn-danger" role="button">-</a>
                                        </div>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                    </div>
                    @endfor
            </div>
            <div class="d-flex flex-row-reverse" style="gap: 10px">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button wire:ignore class="btn btn-success" wire:click='tambahForm' type="reset">+</button>
                <button class="btn btn-danger" wire:click='kurangiForm' type="reset">-</button>
                <div wire:ignore.self>
                    <select id="iter" class="form-control" name="iter">
                        <option value="-">Pilih jumlah iter</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

@push('js')
<script>
    var poli = '';

        function formatData (data) {
            var $data = $(
                '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
            );
            return $data;
        };

        window.addEventListener('livewire:load', function () {
            // Safely inject Livewire property into JS to avoid breaking on special characters like '&'
            // Using @json ensures proper quoting and escaping within JavaScript context
            poli = @json($this->poli);
            console.log(poli);
            // Pastikan dropdown Select2 muncul di atas elemen lain (z-index) dan tidak terpotong oleh tab-content
            // dengan menetapkan dropdownParent ke container resep.
            $('.obat-1').select2({
                placeholder: 'Pilih Obat',
                dropdownParent: $('.containerResep'),
                ajax: {
                    url: '/api/ralan/'+poli+'/obat',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                },
                templateResult: formatData,
                width: '100%',
                minimumInputLength: 3
            });
            
            $('#iter').select2({
                placeholder: 'Pilih jumlah iter',
                dropdownParent: $('.containerResep'),
                allowClear: true,
            });
        })
        
        window.livewire.on('tambahForm', (e) => {
            var i = e.jml;
            $('.obat-'+i).select2({
                placeholder: 'Pilih Obat',
                dropdownParent: $('.containerResep'),
                ajax: {
                    url: '/api/ralan/'+poli+'/obat',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                },
                templateResult: formatData,
                width: '100%',
                minimumInputLength: 3
            });
        });
</script>
@endpush

@push('css')
<style>
    /* Tinggikan z-index untuk container dan dropdown Select2 agar tidak tertutup navbar/kartu AdminLTE */
    .select2-container { z-index: 2000 !important; }
    .select2-dropdown { z-index: 2001 !important; }
    /* Hindari dropdown terpotong di dalam tab-content pada halaman resep */
    #myTabContent { overflow: visible !important; }
    /* Pastikan container resep tidak memotong dropdown */
    .containerResep { position: relative; overflow: visible; }
</style>
@endpush