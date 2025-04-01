@extends('layouts.app')

@section('title', 'Partograf Klasik')

@section('content')
<div class="container-fluid">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="card-title">Partograf Klasik</h3>
            </div>
            <div class="card-body">
               <!-- Informasi Pasien -->
               <div class="row mb-4">
                  <div class="col-md-6">
                     <div class="card">
                        <div class="card-header bg-primary text-white">
                           <h5 class="mb-0">Informasi Ibu Hamil</h5>
                        </div>
                        <div class="card-body">
                           <table class="table table-sm table-borderless">
                              <tr>
                                 <th width="150">Nama</th>
                                 <td>: {{ $ibuHamil->nama }}</td>
                              </tr>
                              <tr>
                                 <th>No. RM</th>
                                 <td>: {{ $ibuHamil->no_rkm_medis }}</td>
                              </tr>
                              <tr>
                                 <th>Usia</th>
                                 <td>: {{ $ibuHamil->usia }} tahun</td>
                              </tr>
                              <tr>
                                 <th>Usia Kehamilan</th>
                                 <td>: {{ $ibuHamil->usia_kehamilan }} minggu</td>
                              </tr>
                              <tr>
                                 <th>HPHT</th>
                                 <td>: {{ $ibuHamil->HPHT ? date('d-m-Y', strtotime($ibuHamil->HPHT)) : '-' }}</td>
                              </tr>
                              <tr>
                                 <th>HPL</th>
                                 <td>: {{ $ibuHamil->HPL ? date('d-m-Y', strtotime($ibuHamil->HPL)) : '-' }}</td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="card">
                        <div class="card-header bg-info text-white">
                           <h5 class="mb-0">Informasi Partograf</h5>
                        </div>
                        <div class="card-body">
                           <table class="table table-sm table-borderless">
                              <tr>
                                 <th width="150">ID Partograf</th>
                                 <td>: {{ $partograf->id_partograf }}</td>
                              </tr>
                              <tr>
                                 <th>Tanggal</th>
                                 <td>: {{ date('d-m-Y H:i', strtotime($partograf->tanggal_partograf)) }}</td>
                              </tr>
                              <tr>
                                 <th>Diperiksa Oleh</th>
                                 <td>: {{ $partograf->diperiksa_oleh }}</td>
                              </tr>
                              <tr>
                                 <th>Paritas</th>
                                 <td>: {{ $partograf->paritas ?? '-' }}</td>
                              </tr>
                              <tr>
                                 <th>Onset Persalinan</th>
                                 <td>: {{ $partograf->onset_persalinan ?? '-' }}</td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Grafik Partograf -->
               <div class="row mb-4">
                  <div class="col-md-12">
                     <div class="card">
                        <div class="card-header bg-success text-white">
                           <h5 class="mb-0">Grafik Kemajuan Persalinan</h5>
                        </div>
                        <div class="card-body">
                           <div id="partografChart" style="height: 400px;"></div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Detail Partograf dalam Tab -->
               <div class="row">
                  <div class="col-md-12">
                     <div class="card">
                        <div class="card-header">
                           <ul class="nav nav-tabs card-header-tabs" id="partografTab" role="tablist">
                              <li class="nav-item">
                                 <a class="nav-link active" id="supportive-tab" data-toggle="tab" href="#supportive"
                                    role="tab">Supportive Care</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" id="fetal-tab" data-toggle="tab" href="#fetal" role="tab">Informasi
                                    Janin</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" id="maternal-tab" data-toggle="tab" href="#maternal"
                                    role="tab">Informasi Ibu</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" id="labor-tab" data-toggle="tab" href="#labor" role="tab">Proses
                                    Persalinan</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" id="medication-tab" data-toggle="tab" href="#medication"
                                    role="tab">Pengobatan</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" id="planning-tab" data-toggle="tab" href="#planning"
                                    role="tab">Perencanaan</a>
                              </li>
                           </ul>
                        </div>
                        <div class="card-body">
                           <div class="tab-content" id="partografTabContent">
                              <!-- Tab Supportive Care -->
                              <div class="tab-pane fade show active" id="supportive" role="tabpanel">
                                 <h5 class="mb-3">Supportive Care</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Pendamping</th>
                                       <td>{{ $partograf->pendamping ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Mobilitas</th>
                                       <td>{{ $partograf->mobilitas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Manajemen Nyeri</th>
                                       <td>{{ $partograf->manajemen_nyeri ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Intake Cairan</th>
                                       <td>{{ $partograf->intake_cairan ?? '-' }}</td>
                                    </tr>
                                 </table>
                              </div>

                              <!-- Tab Informasi Janin -->
                              <div class="tab-pane fade" id="fetal" role="tabpanel">
                                 <h5 class="mb-3">Informasi Janin</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Denyut Jantung Janin</th>
                                       <td>{{ $partograf->denyut_jantung_janin ?? '-' }} bpm</td>
                                    </tr>
                                    <tr>
                                       <th>Kondisi Cairan Ketuban</th>
                                       <td>{{ $partograf->kondisi_cairan_ketuban ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Presentasi Janin</th>
                                       <td>{{ $partograf->presentasi_janin ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Bentuk Kepala Janin</th>
                                       <td>{{ $partograf->bentuk_kepala_janin ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Caput Succedaneum</th>
                                       <td>{{ $partograf->caput_succedaneum ?? '-' }}</td>
                                    </tr>
                                 </table>
                              </div>

                              <!-- Tab Informasi Ibu -->
                              <div class="tab-pane fade" id="maternal" role="tabpanel">
                                 <h5 class="mb-3">Informasi Ibu</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Nadi</th>
                                       <td>{{ $partograf->nadi ?? '-' }} bpm</td>
                                    </tr>
                                    <tr>
                                       <th>Tekanan Darah</th>
                                       <td>{{ $partograf->tekanan_darah_sistole ?? '-' }}/{{
                                          $partograf->tekanan_darah_diastole ?? '-' }} mmHg</td>
                                    </tr>
                                    <tr>
                                       <th>Suhu</th>
                                       <td>{{ $partograf->suhu ?? '-' }} °C</td>
                                    </tr>
                                    <tr>
                                       <th>Urine Output</th>
                                       <td>{{ $partograf->urine_output ?? '-' }} ml</td>
                                    </tr>
                                 </table>
                              </div>

                              <!-- Tab Proses Persalinan -->
                              <div class="tab-pane fade" id="labor" role="tabpanel">
                                 <h5 class="mb-3">Proses Persalinan</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Frekuensi Kontraksi</th>
                                       <td>{{ $partograf->frekuensi_kontraksi ?? '-' }} kali per 10 menit</td>
                                    </tr>
                                    <tr>
                                       <th>Durasi Kontraksi</th>
                                       <td>{{ $partograf->durasi_kontraksi ?? '-' }} detik</td>
                                    </tr>
                                    <tr>
                                       <th>Dilatasi Serviks</th>
                                       <td>{{ $partograf->dilatasi_serviks ?? '-' }} cm</td>
                                    </tr>
                                    <tr>
                                       <th>Penurunan Posisi Janin</th>
                                       <td>{{ $partograf->penurunan_posisi_janin ?? '-' }}</td>
                                    </tr>
                                 </table>
                              </div>

                              <!-- Tab Pengobatan -->
                              <div class="tab-pane fade" id="medication" role="tabpanel">
                                 <h5 class="mb-3">Pengobatan</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Obat dan Dosis</th>
                                       <td>{{ $partograf->obat_dan_dosis ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Cairan Infus</th>
                                       <td>{{ $partograf->cairan_infus ?? '-' }}</td>
                                    </tr>
                                 </table>
                              </div>

                              <!-- Tab Perencanaan -->
                              <div class="tab-pane fade" id="planning" role="tabpanel">
                                 <h5 class="mb-3">Perencanaan</h5>
                                 <table class="table table-bordered">
                                    <tr>
                                       <th width="200">Tindakan yang Direncanakan</th>
                                       <td>{{ $partograf->tindakan_yang_direncanakan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Hasil Tindakan</th>
                                       <td>{{ $partograf->hasil_tindakan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                       <th>Keputusan Bersama</th>
                                       <td>{{ $partograf->keputusan_bersama ?? '-' }}</td>
                                    </tr>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const grafikData = @json($grafikData);
        
        if (!grafikData) {
            document.getElementById('partografChart').innerHTML = '<div class="alert alert-warning">Data grafik tidak tersedia</div>';
            return;
        }
        
        // Siapkan data untuk chart
        const waktuLabels = grafikData.waktu || [];
        const pembukaanData = grafikData.pembukaan || [];
        const penurunanData = grafikData.penurunan || [];
        
        // Konfigurasi chart
        const options = {
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: true
                },
            },
            stroke: {
                width: [3, 3],
                curve: 'straight'
            },
            colors: ['#FF4560', '#2E93fA'],
            series: [
                {
                    name: 'Pembukaan (cm)',
                    data: pembukaanData
                },
                {
                    name: 'Penurunan Kepala',
                    data: penurunanData
                }
            ],
            xaxis: {
                categories: waktuLabels,
                title: {
                    text: 'Waktu Pemeriksaan'
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Pembukaan (cm)'
                    },
                    min: 0,
                    max: 10,
                    reversed: false
                },
                {
                    opposite: true,
                    title: {
                        text: 'Penurunan Kepala'
                    },
                    min: 0,
                    max: 5,
                    reversed: true
                }
            ],
            markers: {
                size: 4
            },
            legend: {
                position: 'top'
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            tooltip: {
                y: {
                    formatter: function(val, { seriesIndex }) {
                        return seriesIndex === 0 ? val + ' cm' : 'Stasiun ' + val;
                    }
                }
            }
        };
        
        // Render chart
        const chart = new ApexCharts(document.getElementById('partografChart'), options);
        chart.render();
    });
</script>
@endpush