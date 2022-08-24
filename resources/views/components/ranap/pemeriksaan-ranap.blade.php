<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
        <x-adminlte-callout theme="info" title="Input" >
            
        </x-adminlte-callout>
        <x-adminlte-callout theme="info" title="Riwayat" >
            @php
                $config["responsive"] = true;
                $config['order'] = [[0, 'desc']];
            @endphp
            <x-adminlte-datatable id="tableRiwayatPemeriksaanRanap" :heads="$heads" head-theme="dark" :config="$config" striped hoverable bordered compressed>
                @foreach($riwayat as $row)
                    <tr>
                        <td>{{ $row->tgl_perawatan }}</td>
                        <td>{{ $row->jam_rawat }}</td>
                        <td>{{ $row->keluhan }}</td>
                        <td>{{ $row->pemeriksaan }}</td>
                        <td>{{ $row->penilaian }}</td>
                        <td>{{ $row->suhu_tubuh }}</td>
                        <td>{{ $row->tensi }}</td>
                        <td>{{ $row->nadi }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        </x-adminlte-callout>
    </x-adminlte-card>
</div>