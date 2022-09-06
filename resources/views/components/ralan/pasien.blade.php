<div>
    <x-adminlte-profile-widget name="{{$data->nm_pasien ?? '-'}}" desc="{{$data->no_rkm_medis ?? '-'}}" theme="lightblue"
        src="img/logo/girl.png" 
            layout-type="classic">
        <x-adminlte-profile-row-item icon="fas fa-fw fa-book-medical" title="No Rawat" text="{{$data->no_rawat ?? '-'}}" />
        <x-adminlte-profile-row-item icon="fas fa-fw fa-calendar" title="Tgl Lahir" text="{{$data->tgl_lahir  ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-clock" title="Umur" text="{{$data->umur ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-wallet" title="Cara Bayar" text="{{$data->png_jawab ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-phone" title="No Telp" text="{{$data->no_tlp ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-building" title="Pekerjaan" text="{{$data->pekerjaan ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-id-card" title="No Peserta" text="{{$data->no_peserta ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-map" title="Alamat" text="{{$data->alamat ?? '-'}}"/>
        <x-adminlte-profile-row-item icon="fas fa-fw fa-sticky-note" title="Catatan" text="{{$data->catatan ?? '-'}}"/>
        <span class="nav-link">
            <x-adminlte-button label="Riwayat Pemeriksaan" data-toggle="modal" data-target="#modalRiwayatPemeriksaanRalan" class="bg-primary justify-content-end"/>
        </span>
    </x-adminlte-profile-widget>
</div>