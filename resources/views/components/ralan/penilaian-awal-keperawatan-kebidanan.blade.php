<div>
    @if(!empty($data))
    <x-adminlte-card theme="dark" title="Penilaian Awal Keperawatan Kebidanan & Kandungan" collapsible="collapsed" maximizable>
        <x-adminlte-card theme="dark" title="PEMERIKSAAN KEBIDANAN" theme-mode="outline">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 25%">TFU</td>
                    <td style="width: 25%">: {{$data->tfu}}</td>
                    <td style="width: 25%">TBJ</td>
                    <td style="width: 25%">: {{$data->tbj}}</td>
                </tr>
                <tr>
                    <td>Letak</td>
                    <td>: {{$data->letak}}</td>
                    <td>Presensi</td>
                    <td>: {{$data->presensi}}</td>
                </tr>
                <tr>
                    <td>Penurunan</td>
                    <td>: {{$data->penurunan}}</td>
                    <td>Kontraksi/HIS</td>
                    <td>: {{$data->his}}x/10</td>
                </tr>
                <tr>
                    <td>Kekuatan</td>
                    <td>: {{$data->kekuatan}}</td>
                    <td>Lamanya</td>
                    <td>: {{$data->lamanya}}detik</td>
                </tr>
                <tr>
                    <td>Gerak janin x/30 menit, DJJ</td>
                    <td>: {{$data->bjj}}/mnt {{$data->ket_bjj}}</td>
                    <td>Portio</td>
                    <td>: {{$data->portio}}</td>
                </tr>
                <tr>
                    <td>Pembukaan Serviks</td>
                    <td>: {{$data->serviks}} cm</td>
                    <td>ketuban</td>
                    <td>: {{$data->ketuban}} kep/bok</td>
                </tr>
                <tr>
                    <td>Hodge</td>
                    <td colspan="3">: {{$data->hodge}}</td>
                </tr>
                <tr>
                    <td colspan="4" style="font-weight: bold">Pemeriksaan Penunjang</td>
                </tr>
                <tr>
                    <td>Inspekulo</td>
                    <td>: {{$data->inspekulo}}</td>
                    <td>Hasil</td>
                    <td>: {{$data->ket_inspekulo}}</td>
                </tr>
                <tr>
                    <td>CTG</td>
                    <td>: {{$data->ctg}}</td>
                    <td>Hasil</td>
                    <td>: {{$data->ket_ctg}}</td>
                </tr>
                <tr>
                    <td>Laboratorium</td>
                    <td>: {{$data->lab}}</td>
                    <td>Hasil</td>
                    <td>: {{$data->ket_lab}}</td>
                </tr>
                <tr>
                    <td>USG</td>
                    <td>: {{$data->usg}}</td>
                    <td>Hasil</td>
                    <td>: {{$data->ket_usg}}</td>
                </tr>
                <tr>
                    <td>Lakmus</td>
                    <td>: {{$data->lakmus}}</td>
                    <td>Hasil</td>
                    <td>: {{$data->ket_lakmus}}</td>
                </tr>
                <tr>
                    <td colspan="2">Pemeriksaan Panggul</td>
                    <td colspan="2">: {{$data->panggul}}</td>
                </tr>
            </table>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="RIWAYAT KESEHATAN" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 50%">Keluhan Utama</td>
                            <td colspan="3">
                                : {{$data->keluhan_utama}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Riwayat Menstruasi :</td>
                        </tr>
                        <tr>
                            <td>Umur Menarche</td>
                            <td>: {{$data->umur}} tahun</td>
                        </tr>
                        <tr>
                            <td>Lamanya</td>
                            <td>: {{$data->lama}} hari</td>
                        </tr>
                        <tr>
                            <td>Banyaknya</td>
                            <td>: {{$data->banyaknya}} pembalut</td>
                        </tr>
                        <tr>
                            <td>Haid Terakhir</td>
                            <td>: {{$data->haid}} </td>
                        </tr>
                        <tr>
                            <td>Siklus</td>
                            <td>: {{$data->siklus}} hari, {{$data->ket_siklus}}</td>
                        </tr>
                        <tr>
                            <td>Masalah yang dirasakan saat menstruasi</td>
                            <td>: {{$data->ket_siklus1}} </td>
                        </tr>
                        <tr>
                            <td colspan="2">Riwayat perkawinan</td>
                        </tr>
                        <tr>
                            <td>Status menikah</td>
                            <td>: {{$data->status}}  {{$data->kali}} kali</td>
                        </tr>
                        <tr>
                            <td>Usia perkawinan ke 1</td>
                            <td>: {{$data->usia1}} tahun, Status : {{$data->usia1}}</td>
                        </tr>
                        <tr>
                            <td>Usia Perkawinan 2</td>
                            <td>: {{$data->usia2}} tahun, Status : {{$data->ket2}} </td>
                        </tr>
                        <tr>
                            <td>Usia Perkawinan Ke 3</td>
                            <td>: {{$data->usia3}} tahun, Status : {{$data->ket3}} </td>
                        </tr>
                        <tr>
                            <td colspan="2">Riwayat Kehamilan Tetap</td>
                        </tr>
                        <tr>
                            <td>HPHT</td>
                            <td>: {{$data->hpht}} </td>
                        </tr>
                        <tr>
                            <td>Usia Hamil</td>
                            <td>: {{$data->usia_kehamilan}} bln/mgg</td>
                        </tr>
                        <tr>
                            <td>TP</td>
                            <td>: {{$data->tp}}</td>
                        </tr>
                        <tr>
                            <td>Riwayat Imunisasi</td>
                            <td>: {{$data->imunisasi}} </td>
                        </tr>
                        <tr>
                            <td>Imunisasi</td>
                            <td>: {{$data->imunisasi}} kali  {{$data->ket_imunisasi}} </td>
                        </tr>
                        <tr>
                            <td>G</td>
                            <td>: {{$data->g}} </td>
                        </tr>
                        <tr>
                            <td>P</td>
                            <td>: {{$data->p}} </td>
                        </tr>
                        <tr>
                            <td>A</td>
                            <td>: {{$data->a}} </td>
                        </tr>
                        <tr>
                            <td>Hidup</td>
                            <td>: {{$data->hidup}} </td>
                        </tr>
                        <tr>
                            <td>Riwayat KB</td>
                            <td>: {{$data->kb}}  {{$data->ket_kb}} </td>
                        </tr>
                        <tr>
                            <td>Lamanya</td>
                            <td>:  </td>
                        </tr>
                        <tr>
                            <td>Komplikasi</td>
                            <td>: {{$data->komplikasi}}  {{$data->ket_komplikasi}} </td>
                        </tr>
                        <tr>
                            <td>Kapan berhenti KB</td>
                            <td>: {{$data->berhenti}} </td>
                        </tr>
                        <tr>
                            <td>Alasan</td>
                            <td>: {{$data->alasan}} </td>
                        </tr>
                        <tr>
                            <td>Riwayat Genekologi</td>
                            <td>: {{$data->ginekologi}}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Riwayat Kebiasaan :</td>
                        </tr>
                        <tr>
                            <td>Obat/Vitamin</td>
                            <td>: {{$data->kebiasaan}}  {{$data->ket_kebiasaan}} </td>
                        </tr>
                        <tr>
                            <td>Merokok</td>
                            <td>: {{$data->kebiasaan1}}  {{$data->ket_kebiasaan1}} batang/hari</td>
                        </tr>
                        <tr>
                            <td>Alkohol</td>
                            <td>: {{$data->kebiasaan2}}  {{$data->ket_kebiasaan2}} gelas/hari</td>
                        </tr>
                        <tr>
                            <td>Obat Tidur/Narkoba</td>
                            <td>: {{$data->kebiasaan3}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="FUNGSIONAL" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 50%">Alat Bantu</td>
                            <td tyle="width: 50%">
                                : {{$data->alat_bantu}}  {{$data->ket_alat_bantu}}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%">Prothesa</td>
                            <td tyle="width: 50%">
                                : {{$data->prothesa}}  {{$data->ket_pro}}
                            </td>
                        </tr>
                        <tr>
                            <td>Cacat fisik</td>
                            <td>: NORMAL </td>
                        </tr>
                        <tr>
                            <td>Aktivitas kehidupan Sehari-hari (ADL)</td>
                            <td>: {{$data->adl}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="RIWAYAT PSIKO-SOSIAL, SPIRITUAL DAN BUDAYA" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 50%">Status Psikologis</td>
                            <td tyle="width: 50%">
                                : {{$data->status_psiko}}  {{$data->ket_psiko}}
                            </td>
                        </tr>
                        <tr>
                            <td>Bahasa yang digunakan sehari-hari</td>
                            <td>: INDONESIA</td>
                        </tr>
                        <tr>
                            <td colspan="2">Status Sosial dan Ekonomi</td>
                        </tr>
                        <tr>
                            <td>a. Hubungan pasien dan keluarga</td>
                            <td>: {{$data->hub_keluarga}} </td>
                        </tr>
                        <tr>
                            <td>b. Tinggal dengan</td>
                            <td>: {{$data->tinggal_dengan}} </td>
                        </tr>
                        <tr>
                            <td>c. Ekonomi</td>
                            <td>: {{$data->ekonomi}} </td>
                        </tr>
                        <tr>
                            <td>Kepercayaan / Budaya / Nilai-nilai khusus yang perlu diperhatikan</td>
                            <td>: {{$data->budaya}}  {{$data->ket_budaya}} </td>
                        </tr>
                        <tr>
                            <td>Edukasi diberikan kepada</td>
                            <td>: {{$data->edukasi}}  {{$data->ket_edukasi}} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
        <x-adminlte-card theme="dark" title="PENILAIAN RESIKO JATUH" theme-mode="outline">
            <div class="table-responsive">
                <table class="table table-light">
                    <tbody>
                        <tr>
                            <td colspan="2">a. Cara Berjalan :</td>
                        </tr>
                        <tr>
                            <td>1. Tidak seimbang / sempoyongan / limbung</td>
                            <td>: {{$data->berjalan_a}} </td>
                        </tr>
                        <tr>
                            <td>2. Jalan dengan menggunakan alat bantu (kruk, tripot, kursi roda, orang lain)</td>
                            <td>: {{$data->berjalan_b}} </td>
                        </tr>
                        <tr>
                            <td>b. Menopang saat akan duduk, tampak memegang pinggiran kursi atau meja / benda lain sebagai penopang</td>
                            <td>: {{$data->berjalan_c}} </td>
                        </tr>
                        <tr>
                            <td>Hasil</td>
                            <td>: {{$data->hasil}} </td>
                        </tr>
                        <tr>
                            <td>Dilaporkan kepada dokter ?</td>
                            <td>: {{$data->lapor}}  {{$data->ket_lapor}}  jam dilaporkan</td>
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
                            <td style="font-weight: bold" colspan="2">{{$data->nyeri}}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Penyebab</td>
                            <td>:  </td>
                        </tr>
                        <tr>
                            <td>Kualitas</td>
                            <td>: {{$data->quality}}  {{$data->ket_quality}} </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold" colspan="2">Wilayah :</td>
                        </tr>
                        <tr>
                            <td>Lokasi</td>
                            <td>: {{$data->lokasi}} </td>
                        </tr>
                        <tr>
                            <td>Menyebar</td>
                            <td>: {{$data->menyebar}} </td>
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
                            <td>Nyeri hilang bila</td>
                            <td>: {{$data->nyeri_hilang}}  {{$data->ket_nyeri}} </td>
                        </tr>
                        <tr>
                            <td>Diberitahukan pada dokter ?  {{$data->pada_dokter}} </td>
                            <td>: {{$data->ket_dokter}} </td>
                        </tr>
                        <tr>
                            <td>Masalah Kebidanan</td>
                            <td>: {{$data->masalah}} </td>
                        </tr>
                        <tr>
                            <td>Tindakan</td>
                            <td>: {{$data->tindakan}} </td>
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