@extends('adminlte::page')

@section('title', $title)

@section('content_header')
<div class="container-fluid">
   <div class="row mb-2">
      <div class="col-sm-6">
         <h1 class="m-0">Sasaran CKG</h1>
      </div>
      <div class="col-sm-6">
         <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Sasaran CKG</li>
         </ol>
      </div>
   </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-header">
               <h3 class="card-title">
                  <i class="fas fa-birthday-cake mr-1"></i>
                  Daftar Pasien Ulang Tahun Hari Ini
               </h3>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table id="tabel-sasaran-ckg" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>No</th>
                           <th>No. Rekam Medis</th>
                           <th>Nama Pasien</th>
                           <th>Tanggal Lahir</th>
                           <th>No. Telepon</th>
                           <th>Alamat PJ</th>
                           <th>Kelurahan PJ</th>
                           <th>Posyandu</th>
                           <th>Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($pasienUlangTahun as $index => $pasien)
                        <tr>
                           <td>{{ $index + 1 }}</td>
                           <td>{{ $pasien->no_rkm_medis }}</td>
                           <td>{{ $pasien->nm_pasien }}</td>
                           <td>{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y') }}</td>
                           <td>{{ $pasien->no_tlp }}</td>
                           <td>{{ $pasien->alamatpj }}</td>
                           <td>{{ $pasien->kelurahanpj }}</td>
                           <td>{{ $pasien->data_posyandu }}</td>
                           <td>
                              <div class="btn-group">
                                 <button type="button" class="btn btn-info btn-sm btn-detail"
                                    data-id="{{ $pasien->no_rkm_medis }}">
                                    <i class="fas fa-eye"></i> Detail
                                 </button>
                                 <a href="{{ route('pasien.edit', $pasien->no_rkm_medis) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                 </a>
                                 <button type="button" class="btn btn-success btn-sm btn-kirim-wa"
                                    data-id="{{ $pasien->no_rkm_medis }}">
                                    <i class="fab fa-whatsapp"></i> Kirim WA
                                 </button>
                              </div>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal Detail Pasien -->
<div class="modal fade" id="modal-detail-pasien" tabindex="-1" role="dialog" aria-labelledby="modalDetailPasienLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header bg-info">
            <h5 class="modal-title" id="modalDetailPasienLabel">Detail Pasien</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label>No. Rekam Medis:</label>
                     <p id="detail-no-rm" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>Nama Pasien:</label>
                     <p id="detail-nama" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>Tanggal Lahir:</label>
                     <p id="detail-tgl-lahir" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>No. Telepon:</label>
                     <p id="detail-no-tlp" class="form-control"></p>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label>Alamat PJ:</label>
                     <p id="detail-alamat" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>Kelurahan PJ:</label>
                     <p id="detail-kelurahan" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>Penanggung Jawab:</label>
                     <p id="detail-pj" class="form-control"></p>
                  </div>
                  <div class="form-group">
                     <label>Posyandu:</label>
                     <p id="detail-posyandu" class="form-control"></p>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>
@endsection

@section('js')
<!-- DataTables & Plugins -->
<link rel="stylesheet"
   href="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css') }}">
<script src="{{ asset('epasien/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
<script src="{{ asset('epasien/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
   document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');
        
        // Pastikan jQuery dan DataTables tersedia
        if (typeof $ === 'undefined') {
            console.error('jQuery tidak tersedia');
            return;
        }
        
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables tidak tersedia');
            return;
        }
        
        console.log('Inisialisasi DataTable');
        
        try {
            // Inisialisasi DataTable
            var table = $('#tabel-sasaran-ckg').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "infoThousands": ".",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "thousands": ".",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "aria": {
                        "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                        "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                    }
                }
            });
            
            console.log('DataTable berhasil diinisialisasi');
        } catch (error) {
            console.error('Error inisialisasi DataTable:', error);
        }

        // Handler untuk tombol Detail
        $('.btn-detail').on('click', function() {
            const id = $(this).data('id');
            console.log('Detail button clicked for ID:', id);
            
            // Gunakan URL absolut dengan base URL
            const baseUrl = window.location.origin;
            const detailUrl = baseUrl + "/ilp/sasaran-ckg/detail/" + id;
            console.log('Detail URL:', detailUrl);
            
            // Menggunakan fetch API
            fetch(detailUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Detail response:', data);
                    // Isi data ke dalam modal
                    $('#detail-no-rm').text(data.no_rkm_medis);
                    $('#detail-nama').text(data.nm_pasien);
                    $('#detail-tgl-lahir').text(formatDate(data.tgl_lahir));
                    $('#detail-no-tlp').text(data.no_tlp);
                    $('#detail-alamat').text(data.alamatpj);
                    $('#detail-kelurahan').text(data.kelurahanpj);
                    $('#detail-pj').text(data.namakeluarga);
                    $('#detail-posyandu').text(data.data_posyandu);
                    
                    // Tampilkan modal
                    $('#modal-detail-pasien').modal('show');
                })
                .catch(error => {
                    console.error('Detail fetch error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal mengambil data pasien: ' + error.message,
                        icon: 'error'
                    });
                });
        });

        // Handler untuk tombol Kirim WA
        $('.btn-kirim-wa').on('click', function() {
            const id = $(this).data('id');
            console.log('Kirim WA button clicked for ID:', id);
            
            // Gunakan URL absolut dengan base URL
            const baseUrl = window.location.origin;
            const waUrl = baseUrl + "/ilp/sasaran-ckg/kirim-wa/" + id;
            console.log('Kirim WA URL:', waUrl);
            
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengirim ucapan selamat ulang tahun via WhatsApp?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Menggunakan fetch API
                    fetch(waUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Kirim WA response:', data);
                            if (data.status === 'success') {
                                // Buka WhatsApp di tab baru
                                window.open(data.url, '_blank');
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Kirim WA fetch error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Gagal mengirim pesan WhatsApp: ' + error.message,
                                icon: 'error'
                            });
                        });
                }
            });
        });
    });

    // Fungsi untuk memformat tanggal
    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }
</script>
@endsection