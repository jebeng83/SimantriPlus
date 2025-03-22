<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" wire:click='checkAll' wire:model="selectAll">
                </th>
                <th>Nama Obat</th>
                <th>Tanggal / Jam</th>
                <th>Jumlah</th>
                <th>Aturan Pakai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="body-resep">
            @forelse($resep as $r)
            <tr wire:key='{{$r->kode_brng}}'
                class="cursor-pointer {{ in_array($r->kode_brng, $selectedItem) ? 'table-active' : '' }}">
                <td><input type="checkbox" wire:click="selectResep('{{$r->kode_brng}}')" {{ in_array($r->kode_brng,
                    $selectedItem) ? 'checked' : '' }}></td>
                <td>{{$r->nama_brng}}</td>
                <td>{{$r->tgl_peresepan}} {{$r->jam_peresepan}}</td>
                <td>{{$r->jml}}</td>
                <td>{{$r->aturan_pakai}}</td>
                <td>
                    <button class="btn btn-danger btn-sm"
                        onclick='hapusObat("{{$r->no_resep}}", "{{$r->kode_brng}}", event)'>Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($selectedItem) > 0)
    <div class="mt-2">
        <button class="btn btn-danger btn-sm" wire:click="deleteSelected">Hapus yang dipilih</button>
    </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            confirmButtonText: 'OK'
        });
    });
    
    window.addEventListener('swal:error', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: event.detail.icon,
            confirmButtonText: 'OK'
        });
    });
</script>
@endpush