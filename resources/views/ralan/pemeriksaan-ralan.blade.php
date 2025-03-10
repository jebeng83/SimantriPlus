@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
<div class="d-flex flex-row justify-content-between">
    <h1>Pemeriksaan Ralan</h1>
    <a name="" id="" class="btn btn-primary" href="{{ url('ralan/pasien') }}" role="button">Daftar Pasien</a>
</div>

@stop

@section('content')
<!-- Debug Info - hidden by default and shown only if no patient data is found -->
<div id="debug-warning-section" style="display: none;">
    <div class="alert alert-warning">
        <h5><i class="icon fas fa-exclamation-triangle"></i> Data Pasien Tidak Ditemukan!</h5>
        <p>Tidak dapat menemukan data pasien dengan parameter yang diberikan. Berikut informasi yang dapat membantu:</p>
        <ul>
            <li><strong>No. Rawat:</strong> {{ $no_rawat ?? 'Tidak tersedia' }}</li>
            <li><strong>No. RM:</strong> {{ $no_rm ?? 'Tidak tersedia' }}</li>
        </ul>

        @if(app()->environment('local', 'development'))
        <div class="mt-3">
            <p><strong>Informasi Debug:</strong></p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Parameter</th>
                        <th>Nilai</th>
                    </tr>
                    <tr>
                        <td>Parameter Asli no_rawat</td>
                        <td>{{ $raw_param['no_rawat_original'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Parameter Asli no_rm</td>
                        <td>{{ $raw_param['no_rm_original'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Panjang no_rawat</td>
                        <td>{{ $param_info['no_rawat_length'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Panjang no_rm</td>
                        <td>{{ $param_info['no_rm_length'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Memiliki karakter khusus?</td>
                        <td>{{ $param_info['has_special_chars'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Base64 Decode dari no_rawat</td>
                        <td>{{ base64_decode($no_rawat) !== false ? base64_decode($no_rawat) : 'Bukan base64 valid' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <p class="mt-3">Silakan kembali ke <a href="{{ url('ralan/pasien') }}" class="alert-link">halaman pasien</a>
            dan coba lagi.</p>
    </div>
</div>

<!-- Loading indicator -->
<div id="loading-indicator" class="text-center p-3">
    <i class="fas fa-spinner fa-spin fa-2x"></i>
    <p>Memuat data pasien...</p>
</div>

<!-- Actual content -->
<div id="patient-content" style="opacity: 0; transition: opacity 0.5s ease;">
    <x-ralan.riwayat :no-rawat="$no_rawat ?? request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ralan.pasien :no-rawat="$no_rawat ?? request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            @if(session()->get('kd_poli') == 'U017')
            <x-adminlte-card title="Uji Fungsi KFR" theme="info" collapsible="collapsed" maximizable>
                <livewire:ralan.uji-fungsi-kfr :noRawat="request()->get('no_rawat')" />
            </x-adminlte-card>
            @endif
            <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
                <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                <livewire:ralan.modal.edit-pemeriksaan />
            </x-adminlte-card>
            @if(session()->get('kd_poli') == 'U0003' || session()->get('kd_poli') == 'U0003')
            <livewire:ralan.odontogram :noRawat=" request()->get('no_rawat')" :noRm="request()->get('no_rm')">
                @endif
                <x-ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" />
                <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-pills"
                    collapsible="collapsed" maximizable>
                    <x-ralan.resep />
                </x-adminlte-card>
                <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed"
                    maximizable>
                    <livewire:ralan.diagnosa :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                </x-adminlte-card>
                <livewire:ralan.resume :no-rawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                <livewire:ralan.catatan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                <x-ralan.rujuk-internal :no-rawat="request()->get('no_rawat')" />
        </div>
    </div>
</div>

@stop

@section('plugins.TempusDominusBs4', true)
@push('js')
<script>
    $(function () {
        $('#pemeriksaan-tab').on('click', function () {
            alert('pemeriksaan');
        })
    })
</script>

<!-- Script untuk mengelola tampilan berdasarkan keberadaan data -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Reference elements
    var warningElement = document.getElementById('debug-warning-section');
    var contentElement = document.getElementById('patient-content');
    var loadingElement = document.getElementById('loading-indicator');
    
    // Function to check if patient data loaded - lebih comprehensive
    function checkPatientDataLoaded() {
        // Check multiple possible elements that indicate patient data
        var patientProfileWidget = document.querySelector('.profile-widget');
        var patientData = document.querySelector('.widget-user-username');
        var noRawatField = document.querySelector('[data-rm]'); // Tombol RM biasanya memiliki data-rm
        var nama = document.querySelector('.widget-user-username');
        var noRawatDisplay = document.querySelector('.btn-no-rawat');
        
        // Jika ada nama pasien atau tombol RM atau widget profil
        if ((nama && nama.textContent.trim().length > 0) || 
            (patientProfileWidget && patientData) || 
            noRawatField || 
            noRawatDisplay) {
            
            console.log("Data pasien berhasil dimuat");
            loadingElement.style.display = 'none';
            contentElement.style.opacity = '1';
            warningElement.style.display = 'none';
            return true;
        } else {
            console.log("Masih memeriksa data pasien...");
            return false;
        }
    }
    
    // First check immediately after DOM content loaded
    if (checkPatientDataLoaded()) {
        // Data loaded immediately
        return;
    }
    
    // Check again after a delay - try multiple times with increasing intervals
    var attempts = 0;
    var maxAttempts = 10; // Meningkatkan jumlah percobaan
    var checkInterval = setInterval(function() {
        attempts++;
        if (checkPatientDataLoaded() || attempts >= maxAttempts) {
            clearInterval(checkInterval);
            
            if (attempts >= maxAttempts && !checkPatientDataLoaded()) {
                // After max attempts, if still no data, show warning
                warningElement.style.display = 'block';
                loadingElement.style.display = 'none';
                contentElement.style.opacity = '0.5'; // Semi-transparant untuk menunjukkan ada masalah
                console.log("Max attempts reached, showing warning");
            }
        }
    }, 500); // Check every 500ms
});
</script>
@endpush