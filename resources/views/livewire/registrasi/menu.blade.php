<div class="btn-group action-dropdown">
    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Menu
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <h6 class="dropdown-header">Status Antrean</h6>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 1)" class="dropdown-item">
            <i class="fas fa-check-circle mr-2 text-success"></i> Hadir
        </a>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 2)" class="dropdown-item">
            <i class="fas fa-times-circle mr-2 text-danger"></i> Tidak Hadir
        </a>
    </div>
</div>