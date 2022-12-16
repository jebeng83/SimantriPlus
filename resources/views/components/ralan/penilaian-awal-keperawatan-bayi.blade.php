<div>
    @if(!empty($data))
    <x-adminlte-card theme="dark" title="Penilaian Awal Keperawatan Rawat Jalan" collapsible="collapsed">
        <x-adminlte-card theme="dark" title="RIWAYAT KESEHATAN DAHULU" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%">Keluhan Utama</td>
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
        <x-adminlte-card theme="dark" title="RIWAYAT TUMBUH KEMBANG DAN PERINATAL CARE" theme-mode="outline">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td style="width: 50%">Riwayat kelahiran</td>
                        <td style="width: 50%">: Anak ke : {{$data->anakke}} dari {{$data->darisaudara}} saudara</td>
                    </tr>
                    <tr>
                        <td>Cara kelahiran</td>
                        <td>: {{$data->caralahir}} {{$data->ket_caralahir}} </td>
                    </tr>
                    <tr>
                        <td>Umur Kelahiran</td>
                        <td>: {{$data->umurkelahiran}} </td>
                    </tr>
                    <tr>
                        <td>Kelainan bawaan</td>
                        <td>: {{$data->kelainanbawaan}} {{$data->ket_kelainan_bawaan}} </td>
                    </tr>
                </tbody>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="RIWAYAT IMUNISASI" theme-mode="outline">
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="RIWAYAT TUMBUH KEMBANG ANAK" theme-mode="outline">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>a. Terungkap, usia</td>
                        <td>: {{$data->usiatengkurap}} </td>
                    </tr>
                    <tr>
                        <td>b. Duduk, usia</td>
                        <td>: {{$data->usiaduduk}} </td>
                    </tr>
                    <tr>
                        <td>c. Berdiri, usia</td>
                        <td>: {{$data->usiaberdiri}} </td>
                    </tr>
                    <tr>
                        <td>d. Gigi pertama, usia</td>
                        <td>: {{$data->usiagigipertama}} </td>
                    </tr>
                    <tr>
                        <td>e. Berjalan, usia</td>
                        <td>: {{$data->usiaberjalan}} </td>
                    </tr>
                    <tr>
                        <td>f. Berbicara, usia</td>
                        <td>: {{$data->usiabicara}} </td>
                    </tr>
                    <tr>
                        <td>g. Mulai bisa membaca, usia</td>
                        <td>: {{$data->usiamenmbaca}} </td>
                    </tr>
                    <tr>
                        <td>h. Mulai bisa menulis, usia</td>
                        <td>: {{$data->usiamenulis}} </td>
                    </tr>
                    <tr>
                        <td>Gangguan perkembangan mental / emosi, bila ada jelaskan</td>
                        <td>: {{$data->gangguanemosi}} </td>
                    </tr>
                </tbody>
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
                        : NORMAL
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
                                : INDONESIA
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
                            <td>b. Pengasuh</td>
                            <td>: {{$data->pengasuh}} </td>
                        </tr>
                        <tr>
                            <td>a. Ekonomi(Ortu)</td>
                            <td>:  {{$data->ekonomi}} </td>
                        </tr>
                        <tr>
                            <td>Kepercayaan / Budaya / Nilai-nilai khusus yang perlu diperhatikan</td>
                            <td>:  {{$data->ket_budaya}} </td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td>: ISLAM </td>
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
                            <td style="font-weight: bold" colspan="2">a. Cara Berjalan (Salah satu atau lebih) :</td>
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
                            <td>: {{$data->ket_lapor}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="SKRINING GIZI (Strong Kid)" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-light">
                    <tbody>
                        <tr>
                            <td style="width: 70%">1. Apakah pasien tampak kurus</td>
                            <td style="width: 7%"> {{$data->sg1}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai1}} </td>
                        </tr>
                        <tr>
                            <td style="width: 70%">2. Apakah terdapat penurunan berat badan selama satu bulan terakhir? (berdasarkan penilaian objektif data berat badan bila ada atau untuk bayi < 1 tahun, berat badan tidak naik selama 3 bulan terakhir)</td>
                            <td style="width: 7%"> {{$data->sg2}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai2}} </td>
                        </tr>
                        <tr>
                            <td style="width: 70%">3. Apakah terdapat salah satu dari kondisi tersebut? Diare > 5 kali/hari seminggu terakhir, Asupan makan berkurang selama 1 minggu terakhir</td>
                            <td style="width: 7%"> {{$data->sg3}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai3}} </td>
                        </tr>
                        <tr>
                            <td style="width: 70%">4. Apakah terdapat penyakit atau keadaan yang menyebapkan pasien beresiko mengalami malnutrisi?</td>
                            <td style="width: 7%"> {{$data->sg4}} </td>
                            <td style="width: 13%">Nilai</td>
                            <td style="width: 10%">: {{$data->nilai4}} </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Total Skor</td>
                            <td>: {{$data->total_hasil}} </td>
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
                            <td>Wajah</td>
                            <td>: {{$data->wajah}} </td>
                            <td> {{$data->nilaiwajah}} </td>
                        </tr>
                        <tr>
                            <td>Menangis</td>
                            <td>: {{$data->kaki}} </td>
                            <td> {{$data->nilaikaki}} </td>
                        </tr>
                        <tr>
                            <td>Kaki</td>
                            <td>: {{$data->kaki}} </td>
                            <td> {{$data->nilaikaki}} </td>
                        </tr>
                        <tr>
                            <td>Bersuara</td>
                            <td>: {{$data->bersuara}} </td>
                            <td> {{$data->nilaibersuara}} </td>
                        </tr>
                        <tr>
                            <td>Aktifitas</td>
                            <td>: {{$data->aktifitas}} </td>
                            <td> {{$data->nilaiaktifitas}} </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: end">Skala nyeri</td>
                            <td>: {{$data->hasilnyeri}} </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold" colspan="2"> {{$data->nyeri}} </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold" colspan="2">Wilayah :</td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>: {{$data->lokasi}}</td>
                        </tr>
                        <tr>
                            <td>Waktu / Durasi</td>
                            <td>: {{$data->durasi}} </td>
                        </tr>
                        <tr>
                            <td>Frekuensi</td>
                            <td>: {{$data->frekuensi}} </td>
                        </tr>
                        <tr>
                            <td>Nyeri hilang bila {{$data->nyeri_hilang}} </td>
                            <td>: {{$data->ket_nyeri}}</td>
                        </tr>
                        <tr>
                            <td>Diberitahukan pada dokter ?</td>
                            <td>:  {{$data->pada_dokter}} </td>
                        </tr>
                        <tr>
                            <td>Jam</td>
                            <td>: {{$data->ket_dokter}} </td>
                        </tr>
                        <tr>
                            <td>Rencana Keperawatan</td>
                            <td>: {{$data->rencana}} </td>
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