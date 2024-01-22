<div>
    <form id="simpan-diagnosa" wire:submit.prevent='simpan'>
        @csrf
        <div wire:ignore class="form-group">
            <label for="diagnosa">Diagnosa</label>
            <select id="diagnosa-select" class="form-control" name="diagnosa"></select>
            @error('diagnosa') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div wire:ignore class="form-group">
            <label for="prosedur">Prosedur</label>
            <select id="prosedur-select" class="form-control" name="prosedur"></select>
            @error('prosedur') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label for="prioritas">Prioritas</label>
            <select id="prioritas" wire:model.defer='prioritas' class="form-control" name="prioritas">
                <option value="">Pilih Prioritas</option>
                <option value="1">Diagnosa Ke-1</option>
                <option value="2">Diagnosa Ke-2</option>
                <option value="3">Diagnosa Ke-3</option>
                <option value="4">Diagnosa Ke-4</option>
                <option value="5">Diagnosa Ke-5</option>
                <option value="6">Diagnosa Ke-6</option>
                <option value="7">Diagnosa Ke-7</option>
                <option value="8">Diagnosa Ke-8</option>
                <option value="9">Diagnosa Ke-9</option>
                <option value="10">Diagnosa Ke-10</option>
            </select>
            @error('prioritas') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-primary btn-block">Simpan</button>
    </form>
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Diagnosa</th>
                    <th>Prosedur</th>
                    <th>Prioritas</th>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($diagnosas as $item)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->kd_penyakit}} - {{$item->nm_penyakit}}</td>
                    <td>{{$item->deskripsi_pendek}}</td>
                    <td>{{$item->prioritas}}</td>
                    <td>
                        <button
                            wire:click='confirmDelete("{{$item->kd_penyakit}}","{{$item->prioritas}}","{{$item->kode}}")'
                            class="btn btn-danger btn-sm">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('js')
<script>
    $('#diagnosa-select').select2({
        placeholder: 'Pilih Diagnosa',
        ajax: {
            url: "{{ route('diagnosa') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.kd_penyakit,
                            text: item.kd_penyakit+' - '+item.nm_penyakit+' - '+item.ciri_ciri
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    $('#prosedur-select').select2({
        placeholder: 'Pilih prosedur',
        ajax: {
            url: "{{ route('icd9') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.kode,
                            text: item.kode+' - '+item.deskripsi_pendek
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    $('#diagnosa-select').on('select2:select', function (e) {
        var data = e.params.data;
        @this.set('diagnosa', data.id);
    });

    $('#prosedur-select').on('select2:select', function (e) {
        var data = e.params.data;
        @this.set('prosedur', data.id);
    });
    
    window.addEventListener('resetSelect2', event => {
        $('#diagnosa-select').val(null).trigger('change');
    });

    window.addEventListener('resetSelect2Prosedur', event => {
        $('#prosedur-select').val(null).trigger('change');
    });
</script>
@endpush