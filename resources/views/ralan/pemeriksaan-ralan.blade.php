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
            <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
                <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                <livewire:ralan.modal.edit-pemeriksaan />
            </x-adminlte-card>
            @if(session()->get('kd_poli') == 'U0003' || session()->get('kd_poli') == 'U0003')
            <livewire:ralan.odontogram :noRawat=" request()->get('no_rawat')" :noRm="request()->get('no_rm')">
                @endif

                <!-- Komponen-komponen yang dipindahkan setelah Pemeriksaan ANC -->
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

                @if(session()->get('kd_poli') == 'U0007')
                <x-adminlte-card title="Pemeriksaan ANC" theme="info" icon="fas fa-lg fa-baby" collapsible="collapsed"
                    maximizable>
                    <livewire:ralan.pemeriksaan-anc :noRawat="request()->get('no_rawat')"
                        :noRm="request()->get('no_rm')" />
                </x-adminlte-card>
                @endif
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

<!-- Script untuk debug service worker -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Service Worker exists
        if ('serviceWorker' in navigator) {
            console.log('Service Worker tersedia di browser ini');
            
            // Log semua service worker yang terdaftar
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                console.log('Service Workers terdaftar:', registrations.length);
                
                // Debug service worker yang bermasalah
                if (registrations.length > 0) {
                    registrations.forEach(function(registration) {
                        console.log('Service Worker scope:', registration.scope);
                        console.log('Service Worker state:', registration.active ? registration.active.state : 'tidak aktif');
                    });
                }
            });

            // Handle unload event untuk membersihkan service worker jika perlu
            window.addEventListener('beforeunload', function() {
                // Jika halaman mengalami error, pertimbangkan untuk unregister service worker
                if (window.hasServiceWorkerError) {
                    navigator.serviceWorker.getRegistrations().then(function(registrations) {
                        for(let registration of registrations) {
                            registration.unregister();
                            console.log('Service Worker dinonaktifkan karena error');
                        }
                    });
                }
            });
        }
        
        // Detect ANC card click
        var ancCard = document.querySelector('[title="Pemeriksaan ANC"]');
        if (ancCard) {
            ancCard.addEventListener('click', function() {
                console.log('ANC card clicked');
            });
        }
    });
    
    // Tangkap error fetching yang mungkin terkait service worker
    window.addEventListener('error', function(event) {
        if (event.message && event.message.includes('Failed to fetch')) {
            console.error('Fetch error terdeteksi:', event);
            window.hasServiceWorkerError = true;
            
            if (confirm('Terjadi error saat memuat data. Muat ulang halaman?')) {
                // Tambahkan parameter untuk menonaktifkan service worker
                window.location.reload(true);
            }
        }
    });
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

<!-- Script untuk memeriksa duplikasi elemen -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk memeriksa duplikasi heading di kartu ANC
        function checkANCCardDuplication() {
            var ancCard = document.querySelector('[title="Pemeriksaan ANC"]');
            if (ancCard) {
                var ancHeadings = ancCard.querySelectorAll('h3.card-title, .card-header');
                if (ancHeadings.length > 1) {
                    console.warn('Duplikasi elemen di kartu Pemeriksaan ANC:', ancHeadings.length, 'headings ditemukan');
                } else {
                    console.log('Kartu Pemeriksaan ANC normal:', ancHeadings.length, 'heading ditemukan');
                }
            }
        }
        
        // Jalankan pengecekan saat DOM loaded
        checkANCCardDuplication();
        
        // Jalankan pengecekan lagi saat card di-expand
        var ancCardToggle = document.querySelector('[title="Pemeriksaan ANC"] .btn-tool[data-card-widget="collapse"]');
        if (ancCardToggle) {
            ancCardToggle.addEventListener('click', function() {
                setTimeout(checkANCCardDuplication, 500); // Delay untuk memastikan DOM sudah diupdate
            });
        }
    });
</script>
@endpush
