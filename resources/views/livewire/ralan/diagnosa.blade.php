<div>
    <form id="simpan-diagnosa" wire:submit.prevent='simpan'>
        @csrf
        <input type="hidden" wire:model.defer="diagnosa" id="hidden-diagnosa">
        <input type="hidden" wire:model.defer="prosedur" id="hidden-prosedur">
        <input type="hidden" wire:model.defer="prioritas" id="hidden-prioritas">
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
            <select id="prioritas-select" class="form-control" name="prioritas">
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
        <button id="btn-simpan-diagnosa" class="btn btn-primary btn-block">Simpan</button>
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
                        <button wire:click='confirmDelete("{{$item->kd_penyakit}}","{{$item->prioritas}}","")'
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
    document.addEventListener('livewire:load', function() {
        // Inisialisasi Select2 untuk diagnosa
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

        // Inisialisasi Select2 untuk prosedur dengan default 89.06
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
        
        // Set default prosedur ke 89.06 - Limited consultation
        setTimeout(function() {
            var defaultOption = new Option('89.06 - Limited consultation', '89.06', true, true);
            $('#prosedur-select').append(defaultOption).trigger('change');
            $('#hidden-prosedur').val('89.06');
            @this.set('prosedur', '89.06');
            @this.call('setProsedur', '89.06');
        }, 100);

        // Event handler untuk diagnosa
        $('#diagnosa-select').on('select2:select', function (e) {
            var data = e.params.data;
            $('#hidden-diagnosa').val(data.id);
            @this.set('diagnosa', data.id);
            document.getElementById('hidden-diagnosa').dispatchEvent(new Event('input'));
            @this.call('setDiagnosa', data.id);
        });

        // Event handler untuk prosedur
        $('#prosedur-select').on('select2:select', function (e) {
            var data = e.params.data;
            $('#hidden-prosedur').val(data.id);
            @this.set('prosedur', data.id);
            document.getElementById('hidden-prosedur').dispatchEvent(new Event('input'));
            @this.call('setProsedur', data.id);
        });
        
        // Event handler untuk prioritas
        $('#prioritas-select').on('change', function() {
            var prioritasValue = $(this).val();
            $('#hidden-prioritas').val(prioritasValue);
            @this.set('prioritas', prioritasValue);
            document.getElementById('hidden-prioritas').dispatchEvent(new Event('input'));
            @this.call('setPrioritas', prioritasValue);
        });
        
        // Set prioritas default ke 1 (Diagnosa Ke-1)
        $('#prioritas-select').val('1').trigger('change');
    });
    
    // Validasi sebelum submit form
    document.getElementById('simpan-diagnosa').addEventListener('submit', function(e) {
        var diagnosa = @this.get('diagnosa');
        var prioritas = @this.get('prioritas');
        
        // Final check diagnosa
        if (!diagnosa || diagnosa === '') {
            e.preventDefault();
            alert('Diagnosa harus dipilih!');
            return false;
        }
        
        // Final check prioritas
        if (!prioritas || prioritas === '') {
            e.preventDefault();
            alert('Prioritas harus dipilih!');
            return false;
        }
        
        // Disable button untuk mencegah double submit
        document.getElementById('btn-simpan-diagnosa').disabled = true;
    });
    
    // Reset pilihan setelah berhasil menyimpan
    window.addEventListener('resetSelect2', event => {
        $('#diagnosa-select').val(null).trigger('change');
        $('#hidden-diagnosa').val('');
    });

    window.addEventListener('resetSelect2Prosedur', event => {
        $('#prosedur-select').val(null).trigger('change');
        $('#hidden-prosedur').val('');
    });
    
    // Set prioritas default ke 1 (Diagnosa Ke-1)
    document.addEventListener('DOMContentLoaded', function() {
        $('#prioritas-select').val('1').trigger('change');
        $('#hidden-prioritas').val('1');
        @this.set('prioritas', '1');
        @this.call('setPrioritas', '1');
    });
</script>
@endpush