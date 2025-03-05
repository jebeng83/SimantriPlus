<div class="btn-group">
    <button type="button" class="btn btn-sm btn-info btn-view-patient"
        onclick="viewPatient('{{ $pasien->no_rkm_medis }}')" data-toggle="tooltip" title="Lihat Detail">
        <i class="fas fa-eye"></i>
    </button>
    <a href="{{ route('pasien.edit', $pasien->no_rkm_medis) }}" class="btn btn-sm btn-primary" data-toggle="tooltip"
        title="Edit Data" target="_self">
        <i class="fas fa-edit"></i>
    </a>
</div>

<script>
    function viewPatient(noRM) {
        // Tampilkan loading spinner
        Swal.fire({
            title: 'Memuat Data...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Ambil data pasien dari server
        fetch(`/pasien/search?term=${noRM}`)
            .then(response => response.json())
            .then(data => {
                // Tutup loading spinner
                Swal.close();
                
                if (data && data.length > 0) {
                    const pasien = data[0];
                    
                    // Siapkan data pasien untuk modal
                    const patientData = {
                        no_rkm_medis: pasien.no_rkm_medis,
                        nm_pasien: pasien.nm_pasien,
                        no_ktp: pasien.no_ktp || '-',
                        no_tlp: pasien.no_tlp || '-',
                        tgl_lahir: pasien.tgl_lahir || '-',
                        jk: pasien.jk || '-',
                        alamat: pasien.alamat || '-',
                        stts_nikah: pasien.stts_nikah || '-',
                        pekerjaan: pasien.pekerjaan || '-',
                        agama: pasien.agama || '-',
                        umur: pasien.umur || '-',
                        kd_pj: pasien.kd_pj || '-'
                    };
                    
                    // Panggil fungsi untuk menampilkan modal dengan data pasien
                    window.showPatientDetails(patientData);
                } else {
                    // Tampilkan pesan error jika data tidak ditemukan
                    Swal.fire({
                        title: 'Error!',
                        text: 'Data pasien tidak ditemukan',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                // Tutup loading spinner
                Swal.close();
                
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengambil data pasien',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }
</script>