<div>
    @if(!empty($data))
    <x-adminlte-card theme="dark" title="Penilaian Awal Medis Bayi" collapsible="collapsed">
        <x-adminlte-card theme="dark" title="RIWAYAT KESEHATAN" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">Keluhan Utama</td>
                    <td tyle="width: 50%">
                        : {{ $data->keluhan_utama }}
                    </td>
                </tr>
                <tr>
                    <td>Riwayat Penyakit Sekarang</td>
                    <td>
                        : {{ $data->rps }}
                    </td>
                </tr>
                <tr>
                    <td>Riwayat Penyakit Keluarga</td>
                    <td>
                        : {{ $data->rpk }}
                    </td>
                </tr>
                <tr>
                    <td>Riwayat Penyakit Dahulu</td>
                    <td>
                        : {{ $data->rpd }}
                    </td>
                </tr>
                <tr>
                    <td>Riwayat Penggunaan Obat</td>
                    <td>
                        : {{ $data->rpo }}
                    </td>
                </tr>
                <tr>
                    <td>Riwayat Alergi</td>
                    <td>
                        : {{$data->alergi}}
                    </td>
                </tr>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PEMERIKSAAN FISIK" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 25%">Kepala</td>
                            <td style="width: 25%">: {{ $data->kepala }}</td>
                            <td style="width: 25%">Abdomen</td>
                            <td style="width: 25%">: {{ $data->abdomen }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%">Mata</td>
                            <td style="width: 25%">: {{ $data->mata }}</td>
                            <td style="width: 25%">Genital & Anus</td>
                            <td style="width: 25%">: {{ $data->genital }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%">Gigi & Mulut</td>
                            <td style="width: 25%">: {{ $data->gigi }}</td>
                            <td style="width: 25%">Ekstremitas</td>
                            <td style="width: 25%">: {{ $data->ekstremitas }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%">THT</td>
                            <td style="width: 25%">: {{ $data->tht }}</td>
                            <td style="width: 25%">Kulit</td>
                            <td style="width: 25%">: {{ $data->kulit }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%">Thoraks</td>
                            <td style="width: 25%">: {{ $data->thoraks }}</td>
                            <td colspan="2">{{ $data->ket_fisik }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="STATUS LOKALIS" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 25%">Keterangan</td>
                            <td>: {{ $data->ket_lokalis }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PEMERIKSAAN PENUNJANGAN" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 100%">{{ $data->penunjang }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="DIAGNOSIS / ASSESMENT" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 100%">{{ $data->diagnosis }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="TATALAKSANA" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 100%">{{ $data->tata }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="KONSUL / RUJUK" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 100%">{{ $data->konsul }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </x-adminlte-card>
    @endif
</div>