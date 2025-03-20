@props(['noPeserta', 'kodeDokter'])

<div>
    <button type="button" class="btn btn-success btn-block" id="btnIcareBpjs{{ rand() }}"
        onclick="showIcareHistory('{{ $noPeserta ?? '0001441909697' }}', '{{ $kodeDokter ?? '102' }}')">
        <i class="fas fa-history mr-2"></i> i-Care BPJS
    </button>
</div>

<!-- Modal untuk menampilkan riwayat BPJS -->
<div class="modal fade" id="icareHistoryModal" tabindex="-1" role="dialog" aria-labelledby="icareHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="icareHistoryModalLabel">
                    <i class="fas fa-history mr-2"></i> Riwayat Pelayanan BPJS
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="icareLoading" class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-success"></i>
                    <p class="mt-3">Memuat riwayat pelayanan...</p>
                </div>
                <div id="icareContent" style="display: none;">
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle mr-2"></i> Silakan login dengan akun BPJS Anda jika diminta.
                        Riwayat pelayanan akan ditampilkan setelah login berhasil.
                    </div>
                    <div style="height: 600px; width: 100%;">
                        <iframe id="icareFrame" allowfullscreen allow="fullscreen" frameborder="0"
                            style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                </div>
                <div id="icareError" class="alert alert-danger m-3" style="display: none;">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span id="icareErrorMessage">Terjadi kesalahan saat memuat riwayat pelayanan.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnOpenNewTab" style="display: none;">
                    <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function showIcareHistory(noPeserta, kodeDokter) {
        console.log('showIcareHistory called with:', { noPeserta, kodeDokter });
        
        // Validasi nomor peserta
        if (!noPeserta || noPeserta.trim() === '') {
            // Cek apakah SweetAlert tersedia
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Nomor BPJS Tidak Tersedia',
                    text: 'Pasien ini tidak memiliki nomor BPJS yang valid.',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert('Pasien ini tidak memiliki nomor BPJS yang valid');
            }
            return;
        }
        
        // Reset modal
        $('#icareLoading').show();
        $('#icareContent').hide();
        $('#icareError').hide();
        $('#btnOpenNewTab').hide();
        $('#icareFrame').attr('src', '');
        
        // Tampilkan modal
        $('#icareHistoryModal').modal('show');
        
        // Pastikan kodeDokter ada, jika tidak gunakan '102' sebagai default
        if (!kodeDokter || kodeDokter.trim() === '') {
            kodeDokter = '102';
        }
        
        console.log('Sending request to API with data:', { param: noPeserta, kodedokter: kodeDokter });
        
        // Request ke API
        $.ajax({
            url: '/api/icare/validate',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                param: noPeserta,
                kodedokter: kodeDokter
            }),
            success: function(response) {
                console.log('API response:', response);
                $('#icareLoading').hide();
                
                if (response.metaData && response.metaData.code === 200 && response.response && response.response.url) {
                    // Tampilkan iframe dengan URL yang didapat
                    const iframeUrl = response.response.url;
                    console.log('Loading iframe with URL:', iframeUrl);
                    
                    // Simpan URL untuk tombol "Buka di Tab Baru"
                    $('#btnOpenNewTab').show().off('click').on('click', function() {
                        window.open(iframeUrl, '_blank');
                    });
                    
                    // Tampilkan iframe untuk URL yang didapat
                    setTimeout(function() {
                        $('#icareContent').show();
                        $('#icareFrame').attr('src', iframeUrl);
                    }, 500);
                } else {
                    // Tampilkan pesan error
                    $('#icareError').show();
                    
                    // Tampilkan pesan khusus untuk kode 412 (belum terdaftar)
                    if (response.metaData && response.metaData.code === 412) {
                        $('#icareErrorMessage').html(
                            '<strong>Tidak dapat menampilkan riwayat:</strong><br>' +
                            response.metaData.message + '<br><br>' +
                            'Pastikan pasien sudah terdaftar pada fasilitas kesehatan hari ini.'
                        );
                    } else {
                        $('#icareErrorMessage').text(
                            response.metaData && response.metaData.message 
                                ? response.metaData.message 
                                : 'Gagal mendapatkan riwayat pelayanan.'
                        );
                    }
                    
                    // Tampilkan tombol buka tab baru jika ada URL di response
                    if (response.response && response.response.url) {
                        const iframeUrl = response.response.url;
                        $('#btnOpenNewTab').show().off('click').on('click', function() {
                            window.open(iframeUrl, '_blank');
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('API error:', { xhr, status, error });
                $('#icareLoading').hide();
                $('#icareError').show();
                
                // Parse error message
                let errorMessage = 'Terjadi kesalahan saat memuat riwayat pelayanan.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.metaData && response.metaData.message) {
                        errorMessage = response.metaData.message;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
                
                $('#icareErrorMessage').text(errorMessage);
                
                // Tampilkan tombol buka link BPJS langsung
                $('#btnOpenNewTab').show().off('click').on('click', function() {
                    window.open('https://vclaim.bpjs-kesehatan.go.id/icare/', '_blank');
                });
            }
        });
    }
    
    // Tambahkan event listener untuk iframe ketika dokumen siap
    $(document).ready(function() {
        // Handle iframe onload
        $('#icareFrame').on('load', function() {
            console.log('iCare iframe loaded successfully');
            $('#icareLoading').hide();
            $('#icareContent').show();
        });
    });
</script>
@endpush