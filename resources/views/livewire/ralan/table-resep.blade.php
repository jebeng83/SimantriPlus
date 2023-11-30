<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" wire:click='checkAll'>
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
            <tr wire:key='{{$r->kode_brng}}' class="cursor-pointer" wire:onclick="selectResep('{{$r->kode_brng}}')">
                <td><input type="checkbox"></td>
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
                <td colspan="5" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>