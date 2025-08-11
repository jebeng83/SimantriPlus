<div class="btn-group action-dropdown">
    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
        Menu
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <h6 class="dropdown-header">Status Antrean</h6>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 1)" class="dropdown-item">
            <i class="fas fa-check-circle mr-2 text-success"></i> Hadir
        </a>
        <a type="button" wire:click="updateStatusAntreanBPJS('{{$row->no_rawat}}', 2)" class="dropdown-item">
            <i class="fas fa-times-circle mr-2 text-danger"></i> Tidak Hadir
        </a>
        <div class="dropdown-divider"></div>
        <h6 class="dropdown-header">Aksi Antrean</h6>
        <a type="button" onclick="batalAntrean('{{$row->no_rawat}}', '{{$row->pasien->nm_pasien ?? 'Pasien'}}')" class="dropdown-item text-warning">
            <i class="fas fa-ban mr-2"></i> Batal Antrean
        </a>
    </div>
</div>

<script>
function batalAntrean(noRawat, namaPasien) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Batal Antrean BPJS',
            html: `<p>Anda akan membatalkan antrean untuk:</p><strong>${namaPasien}</strong><br><br><label for="alasan" style="font-weight: bold;">Alasan Pembatalan:</label>`,
            input: 'textarea',
            inputPlaceholder: 'Masukkan alasan pembatalan antrean...',
            inputAttributes: {
                'id': 'alasan',
                'rows': 3,
                'style': 'width: 100%; margin-top: 10px;'
            },
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Alasan pembatalan harus diisi!';
                }
                if (value.length < 10) {
                    return 'Alasan pembatalan minimal 10 karakter!';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            icon: 'warning',
            customClass: {
                popup: 'swal-wide'
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Panggil method Livewire untuk batal antrean
                @this.call('batalAntreanBPJS', noRawat, result.value.trim());
                
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang membatalkan antrean BPJS',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    } else {
        // Fallback jika SweetAlert tidak tersedia
        const alasan = prompt('Masukkan alasan pembatalan antrean:');
        if (alasan && alasan.trim() !== '') {
            @this.call('batalAntreanBPJS', noRawat, alasan.trim());
        }
    }
}
</script>

<style>
.swal-wide {
    width: 600px !important;
}
</style>