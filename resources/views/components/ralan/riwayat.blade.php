<div>
    <x-adminlte-modal id="modalRiwayatPemeriksaanRalan" title="Riwayat Pemeriksaan" size="xl" theme="info" v-centered static-backdrop scrollable>
        {{-- <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Klinik</th>
                    <th>Keluhan</th>
                    <th>Pemeriksaan</th>
                    <th>Diagnosa</th>
                    <th>Laboratorium</th>
                    <th>Radiologi</th>
                    <th>Catatan</th>
                    <th>Resume Medis</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{$row->tgl_registrasi}}</td>
                    <td>{{$row->nm_poli}}</td>
                    @php
                    $pemeriksaan = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat,$row->status_lanjut);
                    @endphp
                    <td>{{ $pemeriksaan->keluhan }}</td>
                    <td>
                        <ul style="list-style:none;margin:0;padding:0;">
                            <li>{{$pemeriksaan->pemeriksaan ?? ''}}</li>
                            @isset($pemeriksaan->tinggi)
                            <li>Suhu : {{$pemeriksaan->tinggi}}</li>
                            @endisset
                            @isset($pemeriksaan->berat)
                            <li>Suhu : {{$pemeriksaan->berat}}</li>
                            @endisset
                            @isset($pemeriksaan->suhu_tubuh)
                            <li>Suhu : {{$pemeriksaan->suhu_tubuh}}</li>
                            @endisset
                            @isset($pemeriksaan->tensi)
                            <li>Tensi : {{$pemeriksaan->tensi}}</li>
                            @endisset
                            @isset($pemeriksaan->nadi)
                            <li>Nadi : {{$pemeriksaan->nadi}}</li>
                            @endisset
                            @isset($pemeriksaan->respirasi)
                            <li>RR : {{$pemeriksaan->respirasi}}</li>
                            @endisset
                            @isset($pemeriksaan->alergi)
                            <li><b>Alergi : {{$pemeriksaan->alergi}}</b></li>
                            @endisset
                            @isset($pemeriksaan->rtl)
                            <li>Tindak Lanjut : {{$pemeriksaan->rtl}} mmHg</li>
                            @endisset
                        </ul>
                    </td>
                    <td>
                        <ol style="margin-left:20px;padding:0;">
                            @foreach($diagnosa as $diagnosa)
                                <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                            @endforeach
                        </ol>
                    </td>
                    <td>
                        <div class="container">
                            <div class="row row-cols-auto">
                                @foreach($berkasLab as $l)
                                    <a href="{{ env('URL_BERKAS').$l->lokasi_file }}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
                                        <img src="{{ env('URL_BERKAS').$l->lokasi_file }}" class="img-fluid" style="width: 200px;height:250px">
                                    </a>
                                @endforeach  
                            </div>
                        </div>
                        <ol>
                            @foreach($detailLab as $l)
                                <li>{{$l->Pemeriksaan}} ({{$l->nilai_rujukan}}) = {{$l->nilai}}  {{$l->satuan}}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td>
                        <div class="container">
                            <div class="row row-cols-auto">
                                @foreach($berkasRadiologi as $r)
                                    <a href="{{ env('URL_RADIOLOGI').$l->lokasi_gambar }}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
                                        <img src="{{ env('URL_RADIOLOGI').$l->lokasi_gambar }}" class="img-fluid" style="width: 200px;height:250px">
                                    </a>
                                @endforeach  
                            </div>
                        </div>
                        <ol>
                            @foreach($hasilRadiologi as $r)
                            {{ nl2br(e($r->hasil)) }}
                            @endforeach
                        </ol> 
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table> --}}
    <div class="timeline">
        @foreach($data as $row)
            @php
                $pemeriksaan = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanRalan($row->no_rawat,$row->status_lanjut);
                $diagnosa = App\Http\Controllers\Ralan\PemeriksaanRalanController::getDiagnosa($row->no_rawat);
                $laboratorium = App\Http\Controllers\Ralan\PemeriksaanRalanController::getPemeriksaanLab($row->no_rawat);
                $resume = App\Http\Controllers\Ralan\PemeriksaanRalanController::getResume($row->no_rawat);
                $radiologi = App\Http\Controllers\Ralan\PemeriksaanRalanController::getRadiologi($row->no_rawat);
                $gambarRadiologi = App\Http\Controllers\Ralan\PemeriksaanRalanController::getFotoRadiologi($row->no_rawat);
                $tgl = date_create($row->tgl_registrasi ?? '0000-00-00');
                $date = date_format($tgl,"d M Y");
            @endphp
            
                <div class="time-label">
                    <span @if($loop->first) class="bg-green" @else class="bg-yellow" @endif >{{ $date ?? '' }}</span>
                </div>
                <div>
                    <i class="fas fa-stethoscope bg-blue"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header d-flex justify-content-between"><b>{{$row->no_rawat}}</b>  <b>{{$row->nm_dokter}}</b></h3>
                        <div class="timeline-body">
                            @if(count($pemeriksaan)>0)
                            <x-adminlte-card theme="dark" title="Pemeriksaan" collapsible="collapsed" maximizable>
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
                                                <td colspan="8"><pre>{{ $pemeriksaan->keluhan ?? '' }}</pre></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b>Objek</b></td>
                                                <td colspan="8"><pre>{{ $pemeriksaan->pemeriksaan ?? '' }}</pre></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b>Asesmen</b></td>
                                                <td colspan="8">{{ $pemeriksaan->penilaian ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b>Plan</b></td>
                                                <td colspan="8">{{ $pemeriksaan->rtl ?? '' }}</td>
                                            </tr>
                                        </table>
                                    @endforeach
                                </div>
                            </x-adminlte-card>
                            @endif

                            @if(count($diagnosa)>0)
                            <x-adminlte-card theme="dark" title="Diagnosa" collapsible="collapsed" maximizable>
                                <ul>
                                    @foreach($diagnosa as $diagnosa)
                                        <li>{{$diagnosa->nm_penyakit}} ({{$diagnosa->kd_penyakit}})</li>
                                    @endforeach
                                </ul>
                            </x-adminlte-card>
                            @endisset

                            @isset($resume)
                            <x-adminlte-card theme="dark" title="Resume Medis" collapsible="collapsed" maximizable>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><b>Keluhan Utama</b></td>
                                            <td><pre>{{$resume->keluhan_utama}}</pre></td>
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
                                            <td><pre>{{$resume->obat_pulang}}</pre></td>
                                        </tr>
                                    </table>
                                </div>
                            </x-adminlte-card>
                            @endisset

                            @if(count($radiologi)>0)
                            <x-adminlte-card theme="dark" title="Radiologi" collapsible="collapsed" maximizable>
                                <x-adminlte-card theme="dark" title="Gambar Radiologi" collapsible="collapsed">
                                    <div class="container">
                                        <div class="row row-cols-auto">
                                            @foreach($gambarRadiologi as $gambar)
                                                <a href="{{ env('URL_RADIOLOGI').$gambar->lokasi_gambar }}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
                                                    <img src="{{ env('URL_RADIOLOGI').$gambar->lokasi_gambar }}" class="img-fluid" style="width: 200px;height:250px">
                                                </a>
                                            @endforeach  
                                        </div>
                                    </div>
                                </x-adminlte-card>
                                @foreach($radiologi as $radiologi)
                                    <x-adminlte-card title="{{$radiologi->jam}}" theme="dark" footer-class="bg-dark border-top rounded border-light">
                                        <pre>{{$radiologi->hasil}}</pre>
                                        {{-- <x-slot name="footerSlot">
                                            <x-adminlte-button class="d-flex ml-auto" theme="light" label="Foto"
                                                icon="fas fa-sign-in"/>
                                        </x-slot> --}}
                                    </x-adminlte-card>
                                @endforeach
                            </x-adminlte-card>
                            @endif

                            @if(count($laboratorium)>0)
                            <x-adminlte-card theme="dark" title="Laboratorium" collapsible="collapsed" maximizable>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Pemeriksaan</th>
                                                <th>Hasil</th>
                                                <th>Satuan</th>
                                                <th>Nilai Rujukan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($laboratorium as $lab)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$lab->Pemeriksaan}}</td>
                                                    <td>{{$lab->nilai}}</td>
                                                    <td>{{$lab->satuan}}</td>
                                                    <td>{{$lab->nilai_rujukan}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </x-adminlte-card>
                            @endif

                            <x-ralan.penilaian-awal-keperawatan :no-rawat="$row->no_rawat" />
                            <x-ralan.penilaian-awal-keperawatan-gigi-mulut :no-rawat="$row->no_rawat" />
                            <x-ralan.penilaian-awal-keperawatan-kebidanan :no-rawat="$row->no_rawat" />
                            <x-ralan.penilaian-awal-keperawatan-bayi :no-rawat="$row->no_rawat" />
                        </div>
                    </div>
                </div>
        @endforeach
    </div>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Tutup" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
        });
    </script>
@endpush



