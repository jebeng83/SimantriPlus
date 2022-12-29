<div>
    @if(!empty($data))
    <x-adminlte-card theme="dark" title="Penilaian Awal Keperawatan Rawat Jalan" collapsible="collapsed" maximizable>
        <x-adminlte-card theme="dark" title="RIWAYAT KESEHATAN" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">1. Keluhan Utama</td>
                    <td tyle="width: 50%">
                        : {{$data->keluhan_utama}}
                    </td>
                </tr>
                <tr>
                    <td>2. Riwayat Penyakit Keluarga</td>
                    <td>
                        : {{$data->rpk}}
                    </td>
                </tr>
                <tr>
                    <td>3. Riwayat Penyakit Dahulu</td>
                    <td>
                        : {{$data->rpd}}
                    </td>
                </tr>
                <tr>
                    <td>4. Riwayat Pengobatan</td>
                    <td>
                        : {{$data->rpo}}
                    </td>
                </tr>
                <tr>
                    <td>5. Riwayat Alergi</td>
                    <td>
                        : {{$data->alergi}}
                    </td>
                </tr>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="FUNGSIONAL" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">Alat Bantu</td>
                    <td tyle="width: 50%">
                        : {{$data->ket_bantu}}
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
                <tr>
                    <td>Aktifitas Kehidupan Sehari - hari (ADL)</td>
                    <td>
                        : {{$data->adl}}
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
                            <td style="font-weight: bold" colspan="2">Status Sosial dan Ekonomi:</td>
                        </tr>
                        <tr>
                            <td>a. Hubungan pasien dengan anggota keluarga</td>
                            <td>:  {{$data->hub_keluarga}}</td>
                        </tr>
                        <tr>
                            <td>b. Tinggal dengan</td>
                            <td>:  {{$data->tinggal_dengan}}   {{$data->ket_tinggal}} </td>
                        </tr>
                        <tr>
                            <td>a. Ekonomi</td>
                            <td>:  {{$data->ekonomi}} </td>
                        </tr>
                        <tr>
                            <td>Kepercayaan / Budaya / Nilai-nilai khusus yang perlu diperhatikan</td>
                            <td>:  {{$data->ket_budaya}} </td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>:  </td>
                        </tr>
                        <tr>
                            <td>Edukasi diberikan kepada {{$data->edukasi}}</td>
                            <td>:  {{$data->ket_edukasi}} </td>
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
                            <td style="font-weight: bold" colspan="2">a. Cara Berjalan :</td>
                        </tr>
                        <tr>
                            <td style="width: 50%">1. Tidak seimbang / sempoyongan / limbung</td>
                            <td>:  {{$data->berjalan_a}}</td>
                        </tr>
                        <tr>
                            <td>2. Jalan dengan menggunakan alat bantu (kruk, tripot, kursi roda, orang lain)</td>
                            <td>:  {{$data->berjalan_b}}</td>
                        </tr>
                        <tr>
                            <td>b. Menopang saat akan duduk, tampak memegang pinggiran kursi atau meja / benda lain sebagai penopang:</td>
                            <td>:  {{$data->berjalan_c}} </td>
                        </tr>
                        <tr>
                            <td>Hasil</td>
                            <td>:  {{$data->hasil}}</td>
                        </tr>
                        <tr>
                            <td>Dilaporkan kepada dokter ?  {{$data->lapor}}</td>
                            <td>:  {{$data->ket_lapor}}</td>
                        </tr>
                        <tr>
                            <td>Jam dilaporkan</td>
                            <td>: </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="SKRINING GIZI" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-light">
                    <tbody>
                        <tr>
                            <td style="width: 70%">1. Apakah ada penurunan berat badan yang tidak diinginkan selama 6 bulan terakhir?</td>
                            <td style="width: 7%"> {{$data->sg1}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai1}}</td>
                        </tr>
                        <tr>
                            <td style="width: 70%">2. Apakah nafsu makan berkurang karena tidak nafsu makan?</td>
                            <td style="width: 7%"> {{$data->sg2}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai2}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Total Skor</td>
                            <td>: {{$data->total_hasil}}</td>
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
                            <td style="font-weight: bold" colspan="2"> {{$data->nyeri}} </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Penyebab</td>
                            <td>: </td>
                        </tr>
                        <tr>
                            <td>Kualitas</td>
                            <td>: {{$data->ket_quality}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold" colspan="2">Wilayah :</td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>: {{$data->lokasi}}</td>
                        </tr>
                        <tr>
                            <td>Menyebar</td>
                            <td>: {{$data->menyebar}}</td>
                        </tr>
                        <tr>
                            <td>Severity</td>
                            <td>: {{$data->skala_nyeri}} Skala Nyeri</td>
                        </tr>
                        <tr>
                            <td>Waktu / Durasi</td>
                            <td>: {{$data->durasi}}  Menit</td>
                        </tr>
                        <tr>
                            <td>Nyeri hilang bila {{$data->nyeri_hilang}} </td>
                            <td>: {{$data->ket_nyeri}}</td>
                        </tr>
                        <tr>
                            <td>Diberitahukan pada dokter ? {{$data->pada_dokter}}</td>
                            <td>: {{$data->ket_dokter}}</td>
                        </tr>
                        <tr>
                            <td>Jam</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td>Masalah Keperawatan</td>
                            <td>:</td>
                        </tr>
                        <tr>
                            <td>Rencana Keperawatan</td>
                            <td>: {{$data->rencana}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </x-adminlte-card>
    @endif
</div>

@push('css')
    <style>
        .label-asuhan{
            font-weight: bold;
        }
    </style>   
@endpush