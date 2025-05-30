@if($row->status != 'Sudah Dikunjungi')
<div class="action-buttons">
    <button type="button" class="btn btn-sm btn-info btn-detail" data-id="{{ $row->no_rawat }}" title="Detail">
        <i class="fas fa-eye"></i>
    </button>

    <button type="button" class="btn btn-sm btn-success btn-jadikan-kunjungan" data-id="{{ $row->no_rawat }}"
        data-nokartu="{{ $row->noKartu }}" data-kdpoli="{{ $row->kdPoli }}" data-tgldaftar="{{ $row->tglDaftar }}"
        title="Jadikan Kunjungan">
        <i class="fas fa-hospital-user"></i>
    </button>

    <button type="button" class="btn btn-sm btn-danger btn-delete" data-nokartu="{{ $row->noKartu }}"
        data-tgldaftar="{{ date('d-m-Y', strtotime($row->tglDaftar)) }}" data-nourut="{{ $row->noUrut }}"
        data-kdpoli="{{ $row->kdPoli }}" title="Hapus">
        <i class="fas fa-trash"></i>
    </button>
</div>
@else
<div class="action-buttons">
    <button type="button" class="btn btn-sm btn-info btn-detail" data-id="{{ $row->no_rawat }}" title="Detail">
        <i class="fas fa-eye"></i>
    </button>
    <span class="badge badge-success">Sudah Dikunjungi</span>
</div>
@endif