<x-adminlte-card title="Permintaan Lab" theme="info" icon="fas fa-lg fa-flask" collapsible="collapsed" maximizable>
    <form id="formPermintaanLab"></form>
    <div class="form-group row">
        <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="klinis" name="klinis" />
        </div>
    </div>
    <div class="form-group row">
        <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="info" name="info" />
        </div>
    </div>
    <div class="form-group row">
        <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
        <div class="col-sm-8">
          <select class="form-control jenis" id="jenis" name="jenis[]" multiple="multiple" ></select>
        </div>
    </div>
    <div class="d-flex flex-row-reverse pb-3">
        <x-adminlte-button id="simpanPermintaanLab" class="ml-1" theme="primary" type="submit" label="Simpan" />
    </div>
    <x-adminlte-callout theme="info" title="Daftar Permintaan Lab">
        <table class="table table-striped">
            <thead class="thead-inverse" style="width: 100%">
                <tr>
                    <th>No. Order</th>
                    <th>Informasi</th>
                    <th>Klinis</th>
                    <th>Pemeriksaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemeriksaan as $row)
                    <tr>
                        <td scope="row">{{$row->noorder}}</td>
                        <td>{{$row->informasi_tambahan}}</td>
                        <td>{{$row->diagnosa_klinis}}</td>
                        <td>
                            @php
                            $pemeriksaan = App\View\Components\Ralan\PermintaanLab::getDetailPemeriksaan($row->noorder);
                            @endphp
                            @foreach($pemeriksaan as $p)
                                <li>{{$p->nm_perawatan}}</li>
                            @endforeach
                        </td>
                        <td><button class="btn btn-danger btn-sm" onclick='hapusPermintaanLab("{{$row->noorder}}", event)'>Hapus</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-adminlte-callout>
</x-adminlte-card>

@push('js')
    <script 
        id="permintaanLab" 
        src="{{ asset('js/ralan/permintaanLab.js') }}" 
        data-encrypNoRawat="{{ $encrypNoRawat }}" 
        data-token="{{ csrf_token() }}">
    </script>
@endpush