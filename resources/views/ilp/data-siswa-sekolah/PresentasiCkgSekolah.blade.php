@extends('adminlte::page')

@section('title', 'Presentasi CKG Sekolah')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <div>
      <h4 class="m-0 font-weight-bold text-primary">Presentasi Analisa CKG Sekolah</h4>
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb bg-transparent p-0 mt-1 mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">ILP</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presentasi CKG Sekolah</li>
         </ol>
      </nav>
   </div>
   <div class="text-right">
      <p class="text-muted m-0"><i class="fas fa-calendar-day mr-1"></i> {{ date('d F Y') }}</p>
   </div>
</div>
@stop

@section('content')
<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
               <h6 class="text-primary mb-0 font-weight-bold"><i class="fas fa-filter mr-2"></i>Filter</h6>
               <div>
                  <a href="{{ route('ilp.analisa-ckg-sekolah') }}" class="btn btn-sm btn-outline-secondary mr-2">
                     <i class="fas fa-chart-bar mr-1"></i> Analisa Detail
                  </a>
                  <a href="{{ route('ilp.presentasi-ckg-sekolah') }}" class="btn btn-sm btn-outline-secondary">
                     <i class="fas fa-sync mr-1"></i> Reset
                  </a>
               </div>
            </div>
            <form method="GET" action="{{ route('ilp.presentasi-ckg-sekolah') }}">
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-school mr-1"></i> Sekolah</label>
                        <select class="form-control" name="sekolah">
                           <option value="">Semua Sekolah</option>
                           @foreach($daftarSekolah as $sekolah)
                           <option value="{{ $sekolah->id_sekolah }}" {{ $sekolahId == $sekolah->id_sekolah ? 'selected' : '' }}>
                              {{ $sekolah->nama_sekolah }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-graduation-cap mr-1"></i> Jenis Sekolah</label>
                        <select class="form-control" name="jenis_sekolah">
                           <option value="">Semua Jenis</option>
                           @foreach($daftarJenisSekolah as $jenis)
                           <option value="{{ $jenis->id }}" {{ $jenisSekolahId == $jenis->id ? 'selected' : '' }}>
                              {{ $jenis->nama }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-chalkboard mr-1"></i> Kelas</label>
                        <select class="form-control" name="kelas">
                           <option value="">Semua Kelas</option>
                           @foreach($daftarKelas as $kelas)
                           <option value="{{ $kelas->id_kelas }}" {{ $kelasId == $kelas->id_kelas ? 'selected' : '' }}>
                              {{ $kelas->kelas }}
                           </option>
                           @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group mb-md-0">
                        <label class="small text-muted mb-1"><i class="fas fa-calendar mr-1"></i> Periode</label>
                        <div class="d-flex align-items-center">
                           <input type="date" class="form-control mr-2" name="tanggal_awal" value="{{ $tanggalAwal ?: (date('Y').'-01-01') }}">
                           <input type="date" class="form-control" name="tanggal_akhir" value="{{ $tanggalAkhir ?: (date('Y').'-12-31') }}">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col-md-12 text-right">
                     <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title m-0">Ringkasan Eksekutif</h3>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-2 mb-3">
                  <div class="info-box">
                     <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Total Siswa</span>
                        <span class="info-box-number">{{ number_format($totalSiswaAll ?? 0) }}</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2 mb-3">
                  <div class="info-box">
                     <span class="info-box-icon bg-info"><i class="fas fa-user-check"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Siswa Terskrining</span>
                        <span class="info-box-number">{{ number_format($distinctSiswa ?? 0) }}</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2 mb-3">
                  @php
                     $avgCoverage = 0; $cntCov = 0;
                     foreach(($cakupanPerSekolah ?? []) as $row){ $avgCoverage += (float)($row['persen'] ?? 0); $cntCov++; }
                     $avgCoverage = $cntCov > 0 ? round($avgCoverage / $cntCov, 2) : 0;
                  @endphp
                  <div class="info-box">
                     <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Rata-rata Cakupan</span>
                        <span class="info-box-number">{{ $avgCoverage }}%</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2 mb-3">
                  <div class="info-box">
                     <span class="info-box-icon bg-secondary"><i class="fas fa-check"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Normal</span>
                        <span class="info-box-number">{{ number_format($totalNormal ?? 0) }}</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2 mb-3">
                  <div class="info-box">
                     <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Perlu Perhatian</span>
                        <span class="info-box-number">{{ number_format($totalPerlu ?? 0) }}</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-2 mb-3">
                  <div class="info-box">
                     <span class="info-box-icon bg-danger"><i class="fas fa-ambulance"></i></span>
                     <div class="info-box-content">
                        <span class="info-box-text">Rujuk</span>
                        <span class="info-box-number">{{ number_format($totalRujuk ?? 0) }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-md-12 mb-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-success text-white">
            <h3 class="card-title m-0">Cakupan Terskrining per Sekolah</h3>
         </div>
         <div class="card-body">
            <div id="chartCakupan"></div>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-warning text-white">
            <h3 class="card-title m-0">Risiko Utama</h3>
         </div>
         <div class="card-body">
            <div id="chartRisiko"></div>
         </div>
      </div>
   </div>
   <div class="col-md-6 mb-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title m-0">Antropometri Ringkas</h3>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <div id="chartImt"></div>
               </div>
               <div class="col-md-6">
                  <div id="chartGizi"></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<div class="row">
   <div class="col-md-12 mb-4">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-violet text-black">
            <h3 class="card-title m-0">Analisa Kategori Lain</h3>
         </div>
         <div class="card-body">
            <div class="form-inline mb-3">
               <label class="mr-2">Kategori:</label>
               <select id="selectKategoriAnalisa" class="form-control form-control-sm"></select>
            </div>
            <div id="chartKategori"></div>
            <div class="mt-3" id="kesimpulanKategori" style="font-size: 0.95rem;"></div>
         </div>
      </div>
   </div>
</div>

<div class="row mb-4">
   <div class="col-md-12">
      <div class="card shadow-sm">
         <div class="card-header bg-gradient-danger text-white">
            <h3 class="card-title m-0">Rekomendasi Tindak Lanjut</h3>
         </div>
         <div class="card-body">
            <ul id="listRekomendasi" class="list-group"></ul>
         </div>
      </div>
   </div>
</div>
<div id="presentasiData"
     data-total-normal="{{ $totalNormal ?? 0 }}"
     data-total-perlu="{{ $totalPerlu ?? 0 }}"
     data-total-rujuk="{{ $totalRujuk ?? 0 }}"
     data-cakupan='@json($cakupanPerSekolah ?? [])'
     data-risiko='@json($persenResikoKategori ?? [])'
     data-summary='@json($ringkasanPemeriksaan ?? [])'
     data-kategori-analisa='@json($kategoriAnalisa ?? [])'
     data-gula-pertanyaan='@json($gulaPerPertanyaan ?? [])'
     data-gula-summary='@json($gulaSummary ?? [])'>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var dataEl = document.getElementById('presentasiData');
  var totalNormal = parseInt((dataEl && dataEl.dataset.totalNormal) ? dataEl.dataset.totalNormal : '0', 10);
  var totalPerlu = parseInt((dataEl && dataEl.dataset.totalPerlu) ? dataEl.dataset.totalPerlu : '0', 10);
  var totalRujuk = parseInt((dataEl && dataEl.dataset.totalRujuk) ? dataEl.dataset.totalRujuk : '0', 10);

  var cakupan = [];
  try {
    cakupan = JSON.parse((dataEl && dataEl.dataset.cakupan) ? dataEl.dataset.cakupan : '[]');
  } catch (e) { cakupan = []; }
  var labels = []; var values = [];
  if (cakupan && cakupan.length > 0) {
    cakupan.sort(function(a,b){ return (b.persen||0) - (a.persen||0); });
    var top = cakupan.slice(0, 8);
    labels = top.map(function(x){ return String(x.nama_sekolah || 'Tanpa Sekolah'); });
    values = top.map(function(x){ return parseFloat(x.persen || 0); });
  }
  new ApexCharts(document.getElementById('chartCakupan'), {
    chart: { type: 'bar', height: 320 },
    series: [{ name: 'Cakupan (%)', data: values }],
    xaxis: { categories: labels },
    dataLabels: { enabled: true, formatter: function(v){ return v + '%'; } },
    plotOptions: { bar: { horizontal: false } },
    tooltip: { y: { formatter: function(v){ return v + '%'; } } },
    legend: { position: 'bottom' }
  }).render();

  var risiko = {};
  try {
    risiko = JSON.parse((dataEl && dataEl.dataset.risiko) ? dataEl.dataset.risiko : '{}');
  } catch (e) { risiko = {}; }
  var rCats = Object.keys(risiko || {});
  rCats.sort(function(a,b){ return (risiko[b].percent||0) - (risiko[a].percent||0); });
  var rTop = rCats.slice(0, 5);
  var rLabels = rTop.map(function(k){ return String(k).replace(/_/g,' '); });
  var rValues = rTop.map(function(k){ return parseFloat(risiko[k].percent || 0); });
  new ApexCharts(document.getElementById('chartRisiko'), {
    chart: { type: 'bar', height: 320 },
    series: [{ name: 'Persentase', data: rValues }],
    xaxis: { categories: rLabels },
    dataLabels: { enabled: true, formatter: function(v){ return v + '%'; } },
    plotOptions: { bar: { horizontal: true } },
    tooltip: { y: { formatter: function(v){ return v + '%'; } } }
  }).render();

  var summary = {};
  try {
    summary = JSON.parse((dataEl && dataEl.dataset.summary) ? dataEl.dataset.summary : '{}');
  } catch (e) { summary = {}; }
  var imt = summary && summary.antropometri && summary.antropometri.imt ? summary.antropometri.imt : { normal: 0, tidak: 0 };
  var gizi = summary && summary.antropometri && summary.antropometri.status_gizi ? summary.antropometri.status_gizi : { normal: 0, tidak: 0 };
  new ApexCharts(document.getElementById('chartImt'), {
    chart: { type: 'donut', height: 240 },
    series: [parseInt(imt.normal||0,10), parseInt(imt.tidak||0,10)],
    labels: ['Normal','Tidak Normal'],
    legend: { position: 'bottom' }
  }).render();
  new ApexCharts(document.getElementById('chartGizi'), {
    chart: { type: 'donut', height: 240 },
    series: [parseInt(gizi.normal||0,10), parseInt(gizi.tidak||0,10)],
    labels: ['Normal','Tidak Normal'],
    legend: { position: 'bottom' }
  }).render();

  var currentKategoriChart = null;

  var kategoriAnalisa = {};
  try { kategoriAnalisa = JSON.parse((dataEl && dataEl.dataset.kategoriAnalisa) ? dataEl.dataset.kategoriAnalisa : '{}'); } catch(e){ kategoriAnalisa = {}; }
  var selectKat = document.getElementById('selectKategoriAnalisa');
  var chartKatEl = document.getElementById('chartKategori');
  var kesKatEl = document.getElementById('kesimpulanKategori');
  var friendlyCat = function(cat){ return String(cat).replace(/_/g,' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); }); };
  var friendlyItem = function(cat, name){
    var map = {
      'gula_darah': {
        'sering_bangun_sd': 'Sering bangun malam',
        'sering_haus_sekolah': 'Sering haus',
        'sering_lapar': 'Sering lapar',
        'berat_turun_sekolah': 'Berat badan turun',
        'sering_ngompol_sekolah': 'Sering ngompol',
        'riwayat_dm_sd': 'Riwayat DM keluarga'
      },
      'gejala_cemas': {
        'gejala_cemas_khawatir': 'Sering khawatir',
        'gejala_cemas_berfikir_lebih': 'Berpikir berlebihan',
        'gejala_cemas_sulit_konsentrasi': 'Sulit konsentrasi'
      },
      'gejala_depresi': {
        'depresi_anak_sedih': 'Sering sedih',
        'depresi_anak_tidaksuka': 'Tidak tertarik',
        'depresi_anak_capek': 'Sering capek'
      },
      'kesehatan_gigi': {
        'tidak_ada': 'Tidak Ada',
        '1': '1',
        '2': '2',
        '3': '3',
        'gt3': '>3'
      },
      'antropometri_fields': {
        'gizi_buruk': 'Gizi Buruk',
        'gizi_kurang': 'Gizi Kurang',
        'gizi_baik': 'Gizi Baik',
        'berisiko_gizi_lebih': 'Berisiko Gizi Lebih',
        'gizi_lebih': 'Gizi Lebih',
        'obesitas': 'Obesitas'
      }
    };
    var m = map[cat] || {};
    return m[name] || String(name).replace(/_/g,' ');
  };
  var renderKategori = function(cat){
    var data = kategoriAnalisa[cat] || null;
    if (!data || !data.items) { if (kesKatEl) kesKatEl.textContent = 'Data tidak tersedia.'; return; }
    var labels = data.items.map(function(it){ return friendlyItem(cat, it.name); });
    var yesData = data.items.map(function(it){ return parseInt(it.yes||0,10); });
    var noData = data.items.map(function(it){ return parseInt(it.no||0,10); });
    var filledData = data.items.map(function(it){ return parseInt(it.filled||0,10); });
    var countData = data.items.map(function(it){ return parseInt((it.count != null ? it.count : it.filled) || 0, 10); });
    var sumYes = yesData.reduce(function(a,b){ return a+b; }, 0);
    var sumNo  = noData.reduce(function(a,b){ return a+b; }, 0);
    var series = [];
    var baseHeight = Math.max(360, labels.length * 28);
    var isHorizontal = labels.length > 12;
    var chartOpts = {
      chart: { type: 'bar', height: baseHeight },
      xaxis: { categories: labels },
      legend: { position: 'bottom' },
      dataLabels: {
        enabled: true,
        formatter: function(v){ return String(parseInt(v,10)); },
        style: { fontSize: '14px', fontWeight: 'bold', colors: ['#ff0000'] }
      },
      plotOptions: { bar: { horizontal: isHorizontal, dataLabels: { position: 'center' } } },
      tooltip: { y: { formatter: function(v){ return String(parseInt(v,10)); } } }
    };
    if (cat === 'mata_telinga' && data.items.length > 0 && data.items[0].dist) {
      var unionVals = {};
      data.items.forEach(function(it){ var d = it.dist || {}; Object.keys(d).forEach(function(k){ unionVals[k] = true; }); });
      var enumVals = Object.keys(unionVals);
      chartOpts.chart.stacked = true;
      series = enumVals.map(function(v){ return { name: v, data: data.items.map(function(it){ var d = it.dist || {}; return parseInt(d[v]||0,10); }) }; });
    } else if (cat === 'antropometri_fields') {
      var maxVal = countData.reduce(function(a,b){ return Math.max(a,b); }, 0);
      var ticks = maxVal <= 15 ? Math.max(1, maxVal) : 10;
      chartOpts.yaxis = { min: 0, max: Math.max(1, maxVal), tickAmount: ticks, labels: { formatter: function(val){ return String(parseInt(Math.round(val), 10)); } } };
      chartOpts.dataLabels = { enabled: true, formatter: function(v){ return String(parseInt(v,10)); }, style: { fontSize: '14px', fontWeight: 'bold', colors: ['#ff0000'] } };
      chartOpts.plotOptions = chartOpts.plotOptions || {}; chartOpts.plotOptions.bar = chartOpts.plotOptions.bar || {}; chartOpts.plotOptions.bar.dataLabels = { position: 'center' };
      chartOpts.tooltip = chartOpts.tooltip || {}; chartOpts.tooltip.y = { formatter: function(v){ return String(parseInt(v,10)); } };
      series = [{ name: 'Jumlah', data: countData }];
    } else if (sumYes + sumNo > 0) {
      chartOpts.chart.stacked = true;
      series = [{ name: 'Ya', data: yesData }, { name: 'Tidak', data: noData }];
    } else {
      series = [{ name: 'Jumlah', data: countData }];
    }
    if (currentKategoriChart && typeof currentKategoriChart.destroy === 'function') {
      try { currentKategoriChart.destroy(); } catch(e) {}
      currentKategoriChart = null;
    }
    while (chartKatEl && chartKatEl.firstChild) { chartKatEl.removeChild(chartKatEl.firstChild); }
    currentKategoriChart = new ApexCharts(chartKatEl, Object.assign({}, chartOpts, { series: series }));
    currentKategoriChart.render();
    if (kesKatEl) {
      var totalDistinct = parseInt((data.summary && data.summary.totalDistinct) || 0, 10);
      var riskCount = parseInt((data.summary && data.summary.riskCount) || 0, 10);
      var percent = parseFloat((data.summary && data.summary.percent) || 0);
      var baseTop = sumYes > 0 ? yesData : filledData;
      var topIdx = baseTop.map(function(v, i){ return { i: i, v: v }; });
      topIdx.sort(function(a,b){ return b.v - a.v; });
      var topNames = topIdx.slice(0,3).map(function(t){ return labels[t.i] || ''; }).filter(Boolean);
      var descMap = {
        'gejala_cemas': 'indikasi kecemasan. Pertimbangkan skrining kesehatan mental, konseling, keterlibatan orang tua, dan rujukan bila perlu.',
        'gejala_depresi': 'indikasi depresi. Lakukan asesmen lanjut, dukungan psikososial, intervensi sekolah, dan rujukan ke layanan kesehatan jiwa.',
        'malaria': 'paparan/gejala malaria. Lakukan pemeriksaan cepat, edukasi pencegahan gigitan nyamuk, dan rujukan sesuai temuan.',
        'tropis_terabaikan': 'indikasi penyakit tropis terabaikan. Lakukan pemeriksaan kulit/infeksi, tata laksana dasar, dan rujukan.',
        'riwayat_imunisasi': 'cakupan imunisasi. Evaluasi kelengkapan imunisasi dan lakukan tindak lanjut imunisasi kejar.',
        'resiko_hepatitis': 'risiko hepatitis. Anjurkan pemeriksaan HBsAg/Anti-HBs, edukasi higienitas, dan rujukan bila perlu.',
        'resiko_tbc': 'risiko TBC. Lakukan skrining gejala, pemeriksaan lanjutan, edukasi, dan rujukan.',
        'merokok': 'paparan rokok. Lakukan konseling, edukasi, dan pembatasan paparan.',
        'reproduksi_putri': 'isu kesehatan reproduksi putri. Edukasi, konseling, dan rujukan sesuai temuan.',
        'reproduksi_putra': 'isu kesehatan reproduksi putra. Edukasi, konseling, dan rujukan sesuai temuan.',
        'kelayakan_kebugaran': 'kelemahan kebugaran. Perkuat aktivitas fisik, kurikulum olahraga, dan pemantauan.',
        'aktivitas_fisik': 'aktivitas fisik kurang/memerlukan penguatan. Tingkatkan intervensi aktivitas fisik.',
        'resiko_hepa_smp_sma': 'risiko hepatitis pada SMP/SMA. Lakukan skrining dan edukasi.',
        'resiko_talasemia': 'indikasi talasemia. Pertimbangkan skrining darah dan rujukan.',
        'pemeriksaan_lab': 'pemeriksaan lab. Lihat panel lab/antropometri untuk interpretasi numerik.',
        'tekanan_darah': 'tekanan darah. Lihat panel tekanan darah untuk kategori normal/tidak.',
        'mata_telinga': 'temuan mata/telinga. Lihat panel distribusi mata/telinga.'
      };
      var addOn = descMap[cat] || 'indikasi risiko. Lanjutkan skrining dan intervensi sesuai temuan.';
      var text = '';
      if (cat === 'antropometri_fields') {
        var idxMap = {};
        labels.forEach(function(n, i){ idxMap[n] = i; });
        var ambil = function(n){ var i = idxMap[n]; return (i != null) ? (countData[i] || 0) : 0; };
        var buruk = ambil('Gizi Buruk');
        var kurang = ambil('Gizi Kurang');
        var baik = ambil('Gizi Baik');
        var risiko = ambil('Berisiko Gizi Lebih');
        var lebih = ambil('Gizi Lebih');
        var obes = ambil('Obesitas');
        text = 'Distribusi IMT: Gizi Buruk ' + buruk + ', Gizi Kurang ' + kurang + ', Gizi Baik ' + baik + ', Berisiko Gizi Lebih ' + risiko + ', Gizi Lebih ' + lebih + ', Obesitas ' + obes + '. Proporsi risiko: ' + percent + '%. Rekomendasi: perkuat edukasi gizi, aktivitas fisik, dan tindak lanjut klinis untuk kasus berisiko/obesitas.';
      } else if (cat === 'kesehatan_gigi') {
        var idxG = {};
        labels.forEach(function(n, i){ idxG[n] = i; });
        var ambilG = function(n){ var i = idxG[n]; return (i != null) ? (countData[i] || 0) : 0; };
        var none = ambilG('Tidak Ada');
        var c1 = ambilG('1');
        var c2 = ambilG('2');
        var c3 = ambilG('3');
        var cgt3 = ambilG('>3');
        var any = c1 + c2 + c3 + cgt3;
        var pctAny = totalDistinct > 0 ? Math.round((any / totalDistinct) * 10000) / 100 : 0;
        text = 'Distribusi Gigi Karies: Tidak Ada ' + none + ', 1 ' + c1 + ', 2 ' + c2 + ', 3 ' + c3 + ', >3 ' + cgt3 + '. Proporsi memiliki karies: ' + pctAny + '%. Rekomendasi: edukasi kebersihan gigi, pemeriksaan gigi berkala, tindak lanjut klinis untuk kasus >3.';
      } else if (cat === 'mata_telinga') {
        var abnormalMap = {
          'gangguan_telingga_kanan': ['Ada indikasi gangguan pendengaran'],
          'gangguan_telingga_kiri': ['Ada indikasi gangguan pendengaran'],
          'serumen_kanan': ['Ada serumen impaksi'],
          'serumen_kiri': ['Ada serumen impaksi'],
          'infeksi_telingga_kanan': ['Ada infeksi telinga'],
          'infeksi_telingga_kiri': ['Ada infeksi telinga'],
          'selaput_mata_kanan': ['Ya'],
          'selaput_mata_kiri': ['Ya'],
          'visus_mata_kanan': ['Visus <6/9'],
          'visus_mata_kiri': ['Visus <6/9'],
          'kacamata': ['Ya']
        };
        var abnormalCounts = data.items.map(function(it){ var badVals = abnormalMap[it.name] || []; var sum = 0; var d = it.dist || {}; badVals.forEach(function(k){ sum += parseInt(d[k]||0,10); }); return sum; });
        var topAbIdx = abnormalCounts.map(function(v,i){ return { i:i, v:v }; });
        topAbIdx.sort(function(a,b){ return b.v - a.v; });
        var topAbNames = topAbIdx.slice(0,3).map(function(t){ return labels[t.i] || ''; }).filter(Boolean);
        var addOnMt = 'temuan mata/telinga. Perkuat rujukan bila ada gangguan pendengaran, infeksi telinga, visus <6/9, atau kebutuhan kacamata.';
        if (totalDistinct > 0) {
          text = 'Kategori Mata Telinga: dari ' + totalDistinct + ' siswa, ' + riskCount + ' siswa (' + percent + '%) memiliki temuan abnormal pada mata/telinga. Indikator paling sering: ' + (topAbNames.join(', ') || '-') + '. Rekomendasi: ' + addOnMt;
        } else {
          text = 'Data tidak tersedia untuk kategori Mata Telinga.';
        }
      } else {
        if (totalDistinct > 0) {
          text = 'Kategori ' + friendlyCat(cat) + ': dari ' + totalDistinct + ' siswa, ' + riskCount + ' siswa (' + percent + '%) memiliki setidaknya satu indikator. Indikator paling sering: ' + (topNames.join(', ') || '-') + '. Rekomendasi: ' + addOn;
        } else {
          text = 'Data tidak tersedia untuk kategori ' + friendlyCat(cat) + '.';
        }
      }
      kesKatEl.textContent = text;
    }
  };
  if (selectKat && kategoriAnalisa) {
    var cats = Object.keys(kategoriAnalisa);
    cats.forEach(function(c){ var opt = document.createElement('option'); opt.value = c; opt.textContent = friendlyCat(c); selectKat.appendChild(opt); });
    var initCat = cats.indexOf('gejala_cemas') >= 0 ? 'gejala_cemas' : cats[0];
    selectKat.value = initCat;
    renderKategori(initCat);
    selectKat.addEventListener('change', function(){ renderKategori(selectKat.value); });
  }

  var list = document.getElementById('listRekomendasi');
  var makeItem = function(title, desc){
    var li = document.createElement('li'); li.className = 'list-group-item';
    var h = document.createElement('div'); h.className = 'font-weight-bold'; h.textContent = title;
    var p = document.createElement('div'); p.textContent = desc;
    li.appendChild(h); li.appendChild(p); list.appendChild(li);
  };
  if (rTop.length > 0) {
    rTop.forEach(function(k){
      var pct = risiko[k] ? risiko[k].percent : 0;
      var title = 'Fokus: ' + String(k).replace(/_/g,' ');
      var desc = 'Persentase risiko ' + pct + '%. Perkuat skrining lanjutan, edukasi, dan rujukan sesuai temuan.';
      makeItem(title, desc);
    });
  } else {
    makeItem('Tidak ada risiko dominan', 'Lanjutkan skrining rutin, edukasi kesehatan, dan pemantauan perkembangan siswa.');
  }
});
</script>
@endpush
