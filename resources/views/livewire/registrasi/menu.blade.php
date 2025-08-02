<div class="btn-group action-dropdown">
    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Menu
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a type="button" wire:click="$emit('bukaModalPendaftaran', '{{$row->no_rawat}}')" class="dropdown-item">
            <i class="fas fa-edit mr-2 text-primary"></i> Ubah
        </a>
        <a type="button" wire:click="hapus('{{$row->no_rawat}}')" class="dropdown-item">
            <i class="fas fa-trash-alt mr-2 text-danger"></i> Hapus
        </a>
        <div class="dropdown-divider"></div>
        @php
        $encryptedNoRawat = Crypt::encryptString($row->no_rawat);
        $encryptedNoRm = Crypt::encryptString($row->no_rm);
        $url = "/ralan/pemeriksaan?no_rawat=" . urlencode($encryptedNoRawat) . "&no_rm=" . urlencode($encryptedNoRm);
        @endphp
        <a href="{{ $url }}" class="dropdown-item">
            <i class="fas fa-stethoscope mr-2"></i> Pemeriksaan
        </a>
        @if(in_array($row->kd_pj, ['BPJ', 'A14', 'A15']))
        <div class="dropdown-divider"></div>
        <h6 class="dropdown-header">Status Antrean BPJS</h6>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 1)" class="dropdown-item">
            <i class="fas fa-check-circle mr-2 text-success"></i> Hadir
        </a>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 2)" class="dropdown-item">
            <i class="fas fa-times-circle mr-2 text-danger"></i> Tidak Hadir
        </a>
        @endif
    </div>
</div>