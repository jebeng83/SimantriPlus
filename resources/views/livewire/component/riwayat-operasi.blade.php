<div>
    <div wire:init='loadDatas' class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tgl Operasi</th>
                    <th scope="col">Tgl Selesai</th>
                    <th scope="col">Dokter</th>
                    <th scope="col">Diagnosa Pra Bedah</th>
                    <th scope="col">Diagnosa Pasca Bedah</th>
                    <th scope="col">Jns Operasi</th>
                    <th scope="col">Jns Anestasi</th>
                    <th scope="col">Tindakan Bedah</th>
                    <th scope="col">Obat</th>
                    <th scope="col">Jml Pendarahan</th>
                    <th scope="col">Uraian Bedah</th>
                    <th scope="col">Histopatologi</th>
                    <th scope="col">Macam Jaringan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operasi as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tanggal_operasi }}</td>
                    <td>{{ $item->tanggal_selesai }}</td>
                    <td>{{ $item->nm_dokter }}</td>
                    <td>{{ $item->diagnosa_pra_bedah }}</td>
                    <td>{{ $item->diagnosa_pasca_bedah }}</td>
                    <td>{{ $item->jenis_operasi }}</td>
                    <td>{{ $item->jenis_anestesi }}</td>
                    <td>{{ $item->tindakan_bedah }}</td>
                    <td>{{ $item->obat_anestesi }}</td>
                    <td>{{ $item->jumlah_pendarahan }}</td>
                    <td>{{ $item->uraian_bedah }}</td>
                    <td>{{ $item->histopatologi }}</td>
                    <td>{{ $item->macam_jaringan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
