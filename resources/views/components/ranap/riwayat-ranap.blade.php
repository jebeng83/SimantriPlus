<div>
    <x-adminlte-modal id="modalRiwayatPemeriksaanRanap" title="Riwayat Pemeriksaan" size="xl" theme="info" v-centered
        static-backdrop scrollable>

        <div class="timeline">
            @foreach($data as $row)
            @php
            $pemeriksaan =
            App\Http\Controllers\Ranap\PemeriksaanRanapController::getPemeriksaanRanap($row->no_rawat,$row->status_lanjut);
            $diagnosa = App\Http\Controllers\Ranap\PemeriksaanRanapController::getDiagnosa($row->no_rawat);
            $laboratorium = App\Http\Controllers\Ranap\PemeriksaanRanapController::getPemeriksaanLab($row->no_rawat);
            $resume = App\Http\Controllers\Ranap\PemeriksaanRanapController::getResume($row->no_rawat);
            $radiologi = App\Http\Controllers\Ranap\PemeriksaanRanapController::getRadiologi($row->no_rawat);
            $gambarRadiologi = App\Http\Controllers\Ranap\PemeriksaanRanapController::getFotoRadiologi($row->no_rawat);
            $tgl = date_create($row->tgl_registrasi ?? '0000-00-00');
            $date = date_format($tgl,"d M Y");
            @endphp

            <div class="time-label">
                <span @if($loop->first) class="bg-green" @else class="bg-yellow" @endif >{{ $date ?? '' }}</span>
            </div>
            <div>
                <i class="fas fa-clock bg-blue"></i>
                <div class="timeline-item">
                    <h4 class="timeline-header d-flex justify-content-between"><b>{{$row->no_rawat}}</b>
                        <b>{{$row->nm_dokter}}</b>
                    </h4>
                    <div class="timeline-body">
                        @if(count($pemeriksaan)>0)
                        <x-adminlte-card theme="dark" title="Pemeriksaan" collapsible="collapsed">
                            <div class="table-responsive">
                                @foreach($pemeriksaan as $pemeriksaan)
                                @php
                                $tglPemeriksaan = date_create($pemeriksaan->tgl_perawatan ?? '0000-00-00');
                                $datePemeriksaan = date_format($tglPemeriksaan,"d M Y");
                                @endphp
                                <div class="d-flex justify-content-between">
                                    <h5>{{$datePemeriksaan}}</h5>
                                    <h5>{{$pemeriksaan->jam_rawat}}</h5>
                                </div>
                                <table class="table table-bordered" width="100%">
                                    <tr style="font-weight: bold">
                                        <td>Status</td>
                                        <td>Suhu(C)</td>
                                        <td>Tensi(mmHg)</td>
                                        <td>Nadi(/menit)</td>
                                        <td>RR(/menit)</td>
                                        <td>Tinggi(Cm)</td>
                                        <td>Berat(Kg)</td>
                                        <td>GCS(E,V,M)</td>
                                        <td>Kesadaran</td>
                                    </tr>
                                    <tr>
                                        <td>{{$row->status_lanjut}}</td>
                                        <td>{{ $pemeriksaan->suhu_tubuh ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->tensi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->nadi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->respirasi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->tinggi ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->berat ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->gcs ?? '-' }}</td>
                                        <td>{{ $pemeriksaan->kesadaran ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Alergi</b></td>
                                        <td colspan="8">{{ $pemeriksaan->alergi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Subjek</b></td>
                                        <td colspan="8">
                                            <pre>{{ $pemeriksaan->keluhan ?? '' }}</pre>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Objek</b></td>
                                        <td colspan="8">
                                            <pre>{{ $pemeriksaan->pemeriksaan ?? '' }}</pre>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Asesmen</b></td>
                                        <td colspan="8">{{ $pemeriksaan->penilaian ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Plan</b></td>
                                        <td colspan="8">{{ $pemeriksaan->rtl ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Resume Medis</b></td>
                                        <td colspan="8">
                                            @if(isset($resume))
                                            <ul>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Keluhan Utama :</div>
                                                        <div>
                                                            {{$resume->keluhan_utama}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Diagnosa Utama :</div>
                                                        <div>
                                                            {{$resume->diagnosa_utama}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Prosedur Utama :</div>
                                                        <div>
                                                            {{$resume->prosedur_utama}}
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="d-flex flex-row">
                                                        <div>Obat Pulang :</div>
                                                        <div>
                                                            <pre>{{$resume->obat_pulang}}</pre>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><b>Diagnosa</b></td>
                                        <td colspan="8">
                                            <ol>
                                                @forelse($diagnosa as $diag)
                                                <li>{{$diag->nm_penyakit}} ({{$diag->kd_penyakit}})</li>
                                                @empty
                                                <li>-</li>
                                                @endforelse
                                            </ol>
                                        </td>
                                    </tr>
                                </table>
                                @endforeach
                            </div>
                        </x-adminlte-card>
                        @endif

                        {{-- @if(count($diagnosa)>0)
                        <x-adminlte-card theme="dark" title="Diagnosa" collapsible="collapsed">
                            <ul>
                                @foreach($diagnosa as $diagnosa)
                                <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                                @endforeach
                            </ul>
                        </x-adminlte-card>
                        @endisset

                        @isset($resume)
                        <x-adminlte-card theme="dark" title="Resume Medis" collapsible="collapsed">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><b>Keluhan Utama</b></td>
                                        <td>
                                            <pre>{{$resume->keluhan_utama}}</pre>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Diagnosa Utama</b></td>
                                        <td>{{$resume->diagnosa_utama}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Prosedur Utama</b></td>
                                        <td>{{$resume->prosedur_utama}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Obat Pulang</b></td>
                                        <td>
                                            <pre>{{$resume->obat_pulang}}</pre>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </x-adminlte-card>
                        @endisset --}}

                        @if(count($radiologi)>0)
                        <x-adminlte-card theme="dark" title="Radiologi" collapsible="collapsed">
                            <x-adminlte-card theme="dark" title="Gambar Radiologi" collapsible="collapsed">
                                <div class="container">
                                    <div class="row row-cols-auto">
                                        @foreach($gambarRadiologi as $gambar)
                                        <a href="{{ env('URL_RADIOLOGI').$gambar->lokasi_gambar }}"
                                            data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
                                            <img src="{{ env('URL_RADIOLOGI').$gambar->lokasi_gambar }}"
                                                class="img-fluid" style="width: 200px;height:250px">
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </x-adminlte-card>
                            @foreach($radiologi as $radiologi)
                            <x-adminlte-card title="Hasil Pemeriksaan ke. {{$loop->iteration}}" theme="dark"
                                footer-class="bg-dark border-top rounded border-light">
                                <pre>{{$radiologi->hasil}}</pre>
                                {{-- <x-slot name="footerSlot">
                                    <x-adminlte-button class="d-flex ml-auto" theme="light" label="Foto"
                                        icon="fas fa-sign-in" />
                                </x-slot> --}}
                            </x-adminlte-card>
                            @endforeach
                        </x-adminlte-card>
                        @endif

                        @if(count($laboratorium)>0)
                        <x-adminlte-card theme="dark" title="Laboratorium" collapsible="collapsed">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pemeriksaan</th>
                                            <th>Tgl Periksa</th>
                                            <th>Jam</th>
                                            <th>Hasil</th>
                                            <th>Satuan</th>
                                            <th>Nilai Rujukan</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laboratorium as $lab)
                                        <tr
                                            class="@if($lab->keterangan == 'T' || $lab->keterangan == 'H') bg-danger @endif">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$lab->Pemeriksaan}}</td>
                                            <td>{{$lab->tgl_periksa}}</td>
                                            <td>{{$lab->jam}}</td>
                                            <td>{{$lab->nilai}}</td>
                                            <td>{{$lab->satuan}}</td>
                                            <td>{{$lab->nilai_rujukan}}</td>
                                            <td>{{$lab->keterangan}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </x-adminlte-card>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
</div>

<div id="myModal" class="modal-foto">
    <span class="close">&times;</span>
    <img class="modal-content-foto" id="img01">
    <div id="caption"></div>
</div>

@push('css')
<style>
    #myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    #myImg:hover {
        opacity: 0.7;
    }

    /* The Modal (background) */
    .modal-foto {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.9);
        /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content-foto {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    /* Caption of Modal Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation */
    .modal-content-foto,
    #caption {
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {
            -webkit-transform: scale(0)
        }

        to {
            -webkit-transform: scale(1)
        }
    }

    @keyframes zoom {
        from {
            transform: scale(0)
        }

        to {
            transform: scale(1)
        }
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px) {
        .modal-content-foto {
            width: 100%;
        }
    }
</style>
@endpush

@push('js')
<script>
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
        });

        function openModalImage(id){
            var modal = document.getElementById("myModal");
            var img = document.getElementById(id);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = img.src;
            captionText.innerHTML = img.alt;
        }
</script>
@endpush