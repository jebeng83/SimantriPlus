<div class="btn-group">
    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Menu
    </button>
    <div class="dropdown-menu">
        <a type="button" wire:click="$emit('bukaModalPendaftaran', '{{$row->no_rawat}}')" class="dropdown-item">Ubah</a>
        <a type="button" wire:click="hapus('{{$row->no_rawat}}')" class="dropdown-item">Hapus</a>
    </div>
</div>