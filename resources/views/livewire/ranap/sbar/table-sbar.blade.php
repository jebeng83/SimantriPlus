<div wire:init='load'>
    <h5 class="text-bold mt-3">Data SBAR</h5>
    <x-ui.table :headers="$headers" dark>
        @forelse($sbar as $item)
        <tr>
            <td>{{ $item->tanggal }}</td>
            <td>{{ $item->situation }}</td>
            <td>{{ $item->background }}</td>
            <td>{{ $item->assesment }}</td>
            <td>{{ $item->recommendation }}</td>
            <td>{{ $item->advis }}</td>
            <td>{{ $item->nama }}</td>
            <td>
                <button wire:click="$emit('pilihSbar', {{$item->no_sbar}})" type="button" class="btn btn-sm btn-primary">Pilih</button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Data tidak ditemukan</td>
        </tr>
        @endforelse
    </x-ui.table>
</div>
