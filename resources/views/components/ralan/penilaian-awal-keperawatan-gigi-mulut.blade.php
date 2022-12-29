<div>
    @if(!empty($data))
    <x-adminlte-card theme="dark" title="Penilaian Awal Keperawatan Gigi dan Mulut" collapsible="collapsed" maximizable>
        <x-adminlte-card theme="dark" title="RIWAYAT KESEHATAN" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">1. Keluhan Utama</td>
                    <td tyle="width: 50%">
                        : {{ $data->keluhan_utama }}
                    </td>
                </tr>
                <tr>
                    <td>2. Riwayat Penyakit</td>
                    <td>
                        : {{$data->ket_riwayat_penyakit}}
                    </td>
                </tr>
                <tr>
                    <td>3. Riwayat Perawatan Gigi</td>
                    <td>
                        : {{$data->ket_riwayat_perawatan_gigi}}
                    </td>
                </tr>
                <tr>
                    <td>4. Riwayat Alergi</td>
                    <td>
                        : {{$data->alergi}}
                    </td>
                </tr>
                <tr>
                    <td>5. Kebiasaan Lain</td>
                    <td>
                        : {{$data->ket_kebiasaan_lain}}
                    </td>
                </tr>
                <tr>
                    <td>6. Kebiasaan Sikat Gigi</td>
                    <td>
                        : {{$data->kebiasaan_sikat_gigi}}
                    </td>
                </tr>
                <tr>
                    <td>7. Obat Yang Diminum Saat Ini</td>
                    <td>
                        : {{$data->obat_yang_diminum_saatini}}
                    </td>
                </tr>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="FUNGSIONAL" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">Alat Bantu</td>
                    <td tyle="width: 50%">
                        : {{$data->ket_alat_bantu}}
                    </td>
                </tr>
                <tr>
                    <td>Prothesa</td>
                    <td>
                        : {{$data->ket_pro}}
                    </td>
                </tr>
                <tr>
                    <td>Cacat Fisik</td>
                    <td>
                        : 
                    </td>
                </tr>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="RIWAYAT PSIKO-SOSIAL, SPIRITUAL DAN BUDAYA" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 50%">Status Psikologis</td>
                            <td tyle="width: 50%">
                                : {{$data->ket_psiko}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%">Bahasa yang digunakan sehari-hari</td>
                            <td tyle="width: 50%">
                                :
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="font-weight: bold">Status Sosial dan Ekonomi:</td>
                        </tr>
                        <tr>
                            <td>a. Hubungan pasien dengan anggota keluarga</td>
                            <td>: {{$data->hub_keluarga}}</td>
                        </tr>
                        <tr>
                            <td>b. Tinggal dengan</td>
                            <td>: {{$data->ket_tinggal}}</td>
                        </tr>
                        <tr>
                            <td>a. Ekonomi</td>
                            <td>: {{$data->ekonomi}}</td>
                        </tr>
                        <tr>
                            <td>Kepercayaan / Budaya / Nilai-nilai khusus yang perlu diperhatikan</td>
                            <td>: {{$data->ket_budaya}}</td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>: </td>
                        </tr>
                        <tr>
                            <td>Edukasi diberikan kepada</td>
                            <td>: {{$data->ket_edukasi}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PENILAIAN RESIKO JATUH" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="2" style="font-weight: bold">a. Cara Berjalan :</td>
                        </tr>
                        <tr>
                            <td style="width: 50%">1. Tidak seimbang / sempoyongan / limbung</td>
                            <td>: {{$data->berjalan_a}}</td>
                        </tr>
                        <tr>
                            <td>2. Jalan dengan menggunakan alat bantu (kruk, tripot, kursi roda, orang lain)</td>
                            <td>: {{$data->berjalan_b}}</td>
                        </tr>
                        <tr>
                            <td>b. Menopang saat akan duduk, tampak memegang pinggiran kursi atau meja / benda lain sebagai penopang:</td>
                            <td>: {{$data->berjalan_c}}</td>
                        </tr>
                        <tr>
                            <td>Hasil</td>
                            <td>: {{$data->hasil}}</td>
                        </tr>
                        <tr>
                            <td>Dilaporkan kepada dokter ?</td>
                            <td>: {{$data->lapor}}</td>
                        </tr>
                        <tr>
                            <td>Jam dilaporkan</td>
                            <td>: {{$data->ket_lapor}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PENILAIAN TINGKAT NYERI" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="2" style="font-weight: bold">{{$data->nyeri}}</td>
                        </tr>
                        <tr>
                            <td style="width: 50%">Skala Nyeri</td>
                            <td>:{{$data->skala_nyeri}}</td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>:{{$data->lokasi}}</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td>: {{$data->durasi}}</td>
                        </tr>
                        <tr>
                            <td>Frekuensi</td>
                            <td>: {{$data->frekuensi}}</td>
                        </tr>
                        <tr>
                            <td>Nyeri Hilang bila {{$data->nyeri_hilang}}</td>
                            <td>:  {{$data->ket_nyeri}}</td>
                        </tr>
                        <tr>
                            <td>Diberitahukan pada dokter ?</td>
                            <td>: {{$data->pada_dokter}}</td>
                        </tr>
                        <tr>
                            <td>Jam diberitahukan</td>
                            <td>:</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PENILAIAN INTRAORAL" theme-mode="outline">
            <div class="teble-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td style="width: 50%">Kebersihan Mulut</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Mukosa Mulut</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Karies</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Karang Gigi</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Gingiva</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Palatum</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Masalah Keperawatan</td>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td>Rencana Keperawatan</td>
                        <td>:</td>
                    </tr>
                </table>
            </div>
        </x-adminlte-card>
    </x-adminlte-card>
    @endif
</div>