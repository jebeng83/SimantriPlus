<div>
    <x-adminlte-card title="Diagnosa" theme="info" icon="fas fa-lg fa-file-medical" collapsible="collapsed" maximizable>
        <form id="simpan-diagnosa" method="POST" action="{{ route('diagnosa.simpan') }}">
            @csrf
            <input type="hidden" name="noRawat" value="{{ $noRawat }}">
            <input type="hidden" name="noRM" value="{{ $noRm }}">
            <div class="form-group">
                <label for="diagnosa">Diagnosa</label>
                <select id="diagnosa-select" class="form-control" name="diagnosa"></select>
            </div>
            <div class="form-group">
                <label for="tindakan">Tindakan</label>
                <select id="indakan-select" class="form-control" name="tindakan"></select>
            </div>
            <div class="form-group">
                <label for="prioritas">Prioritas</label>
                <select id="prioritas" class="form-control" name="prioritas">
                    <option value="1">Diagnosa Ke-1</option>
                    <option value="2">Diagnosa Ke-2</option>
                    <option value="3">Diagnosa Ke-3</option>
                    <option value="4">Diagnosa Ke-4</option>
                    <option value="5">Diagnosa Ke-5</option>
                    <option value="6">Diagnosa Ke-6</option>
                    <option value="7">Diagnosa Ke-7</option>
                    <option value="8">Diagnosa Ke-8</option>
                    <option value="9">Diagnosa Ke-9</option>
                    <option value="10">Diagnosa Ke-10</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
        </form>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Diagnosa</th>
                        <th>Prioritas</th>
                        <th>Menu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($diagnosa as $item)
                    <tr>
                        <td>{{$item->kd_penyakit}} - {{$item->nm_penyakit}}</td>
                        <td>{{$item->prioritas}}</td>
                        <td>
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-adminlte-card>
</div>

@push('js')
<script>
    $('#diagnosa-select').select2({
        placeholder: 'Pilih Diagnosa',
        ajax: {
            url: "{{ route('diagnosa') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.kd_penyakit,
                            text: item.kd_penyakit+' - '+item.nm_penyakit
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    $('#tindakan-select').select2({
        placeholder: 'Pilih tindakan',
        ajax: {
            url: "{{ route('icd9') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.kode,
                            text: item.kode+' - '+item.deskripsi_pendek
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    $('#simpan-diagnosa').submit(function (e) {
        e.preventDefault();
        
        // Disable button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        // Collect form data
        var formData = $(this).serialize();
        
        // Log the form data for debugging
        console.log('Form data:', formData);
        
        // Perform the AJAX request
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log('Success response:', response);
                
                if (response.status === 'sukses') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.pesan,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Refresh halaman untuk menampilkan data terbaru
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.pesan || 'Terjadi kesalahan saat menyimpan diagnosa',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    
                    // Enable the submit button again
                    submitBtn.prop('disabled', false).html('Simpan');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan diagnosa';
                
                if (xhr.responseJSON && xhr.responseJSON.pesan) {
                    errorMessage = xhr.responseJSON.pesan;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                Swal.fire({
                    title: 'Gagal!',
                    html: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                
                // Enable the submit button again
                submitBtn.prop('disabled', false).html('Simpan');
            }
        });
    });
</script>
@endpush