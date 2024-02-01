<div>
    <form wire:submit.prevent='simpan'>
        <x-ui.textarea label="Situation" id="situation" model="situation" live disabled />
        <x-ui.textarea label="Background" id="background" model="background" live disabled />
        <x-ui.textarea label="Assesment" id="assesment" model="assesment" live disabled />
        <x-ui.textarea label="Recommendation" id="recommendation" model="recommendation" live disabled />
        <x-ui.textarea label="Advis" id="advis" model="advis" />
        <x-ui.select2 label="Petugas" id="petugas" ajax="{{ route('pegawai') }}">
            <option value="">Pilih Petugas</option>
        </x-ui.select2>
        <div class="d-flex flex-row justify-content-end">
            <button type="submit" class="btn btn-success">Validasi</button>
        </div>
    </form>
</div>

@push('js')
<script>
    $('#petugas').on('change', function (e) {
        @this.set('petugas', e.target.value);
    });
    Livewire.on('loadSbar', () => {
        $('#petugas').val(null).trigger('change');
    });
</script>
@endpush
