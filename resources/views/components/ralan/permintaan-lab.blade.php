<x-adminlte-card title="Permintaan Lab" theme="info" icon="fas fa-lg fa-flask" collapsible="collapsed" maximizable>
    <form id="form-lab">
        <div class="form-group row">
            <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="klinis" name="klinis" />
            </div>
        </div>
        <div class="form-group row">
            <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="info" name="info" />
            </div>
        </div>
        <div class="form-group row">
            <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
            <div class="col-sm-8">
                <select class="form-control jenis" id="jenis" name="jns_pemeriksaan[]" multiple="multiple"></select>
            </div>
        </div>
        <!-- Container untuk template -->
        <div id="template-area" class="mt-3"></div>
        <div class="d-flex flex-row-reverse pb-3">
            <x-adminlte-button id="btn-simpan" class="ml-1" theme="primary" type="submit" label="Simpan" />
        </div>
    </form>
    <x-adminlte-callout theme="info" title="Daftar Permintaan Lab">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-inverse" style="width: 100%">
                    <tr>
                        <th>No. Order</th>
                        <th>Tanggal</th>
                        <th>Informasi</th>
                        <th>Klinis</th>
                        <th>Pemeriksaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="permintaan-lab-table-body">
                    @forelse($pemeriksaan as $row)
                    <tr id="row-{{$row->noorder}}">
                        <td scope="row">{{$row->noorder ?? 'N/A'}}</td>
                        <td>{{$row->tgl_permintaan ?? 'N/A'}} {{$row->jam_permintaan ?? 'N/A'}}</td>
                        <td>{{$row->informasi_tambahan ?? 'N/A'}}</td>
                        <td>{{$row->diagnosa_klinis ?? 'N/A'}}</td>
                        <td>
                            @php
                            try {
                            $detailPemeriksaan =
                            App\View\Components\Ralan\PermintaanLab::getDetailPemeriksaan($row->noorder);
                            } catch(\Exception $e) {
                            $detailPemeriksaan = collect();
                            \Illuminate\Support\Facades\Log::error('Error saat render detailPemeriksaan: ' .
                            $e->getMessage());
                            }
                            @endphp
                            @if($detailPemeriksaan->count() > 0)
                            <ul class="mb-0 pl-3">
                                @foreach($detailPemeriksaan as $p)
                                <li>
                                    {{ $p->nm_perawatan ?? 'Tanpa nama' }}
                                    @if(isset($p->source) && $p->source == 'template')
                                    <span class="badge badge-info ml-1">Template</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-muted">Tidak ada detail pemeriksaan</span>
                            @endif
                        </td>
                        <td><button class="btn btn-danger btn-sm hapus-lab-btn" data-noorder="{{$row->noorder}}"
                                onclick='hapusPermintaanLab("{{$row->noorder}}", event)'>Hapus</button></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-info">
                                <p>Belum ada permintaan laboratorium. Silakan tambahkan permintaan baru.</p>
                                <small class="d-block mt-1">
                                    <strong>Info Diagnostik:</strong><br>
                                    No. Rawat: {{ $encrypNoRawat ?? 'Tidak ada' }}<br>
                                    Timestamp: {{ date('Y-m-d H:i:s') }}
                                </small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-adminlte-callout>
</x-adminlte-card>

@push('js')
<script id="permintaanLab" src="{{ asset('js/ralan/permintaanLab.js') }}" data-encrypNoRawat="{{ $encrypNoRawat }}"
    data-token="{{ csrf_token() }}">
</script>

<!-- Script tambahan untuk reload data tabel -->
<script>
    // Pastikan jQuery tersedia
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menghapus baris secara visual tanpa me-reload halaman
        window.removeRowFromTable = function(noOrder) {
            console.log('Menghapus baris dengan noOrder:', noOrder);
            const row = document.getElementById('row-' + noOrder);
            if (row) {
                // Animasi hapus baris
                row.style.transition = 'all 0.5s ease';
                row.style.opacity = '0';
                row.style.maxHeight = '0';
                
                // Hapus setelah animasi selesai
                setTimeout(function() {
                    row.remove();
                    
                    // Cek jika tabel kosong, tampilkan pesan
                    const tbody = document.getElementById('permintaan-lab-table-body');
                    if (tbody.children.length === 0) {
                        const emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = `
                            <td colspan="6" class="text-center">
                                <div class="alert alert-info">
                                    <p>Belum ada permintaan laboratorium. Silakan tambahkan permintaan baru.</p>
                                    <small class="d-block mt-1">
                                        <strong>Info Diagnostik:</strong><br>
                                        No. Rawat: {{ $encrypNoRawat ?? 'Tidak ada' }}<br>
                                        Timestamp: {{ date('Y-m-d H:i:s') }}
                                    </small>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(emptyRow);
                    }
                }, 500);
            } else {
                console.warn('Baris dengan noOrder ' + noOrder + ' tidak ditemukan');
            }
        };

        // Cek daftar permintaan lab kosong
        const checkEmptyTable = function() {
            const tbody = document.getElementById('permintaan-lab-table-body');
            const tableRows = tbody.querySelectorAll('tr:not(.empty-row)');
            
            console.log('Permintaan lab table rows:', tableRows.length);
            
            // Jika baris tabel kosong (tidak ada data) - tambahkan diagnosa info
            if (tableRows.length === 0) {
                console.log('Tabel permintaan lab kosong, menambahkan diagnostic info');
                
                const emptyRow = document.createElement('tr');
                emptyRow.classList.add('empty-row');
                emptyRow.innerHTML = `
                    <td colspan="6" class="text-center">
                        <div class="alert alert-info">
                            <p>Belum ada permintaan laboratorium. Silakan tambahkan permintaan baru.</p>
                            <small class="d-block mt-1">
                                <strong>Info Diagnostik:</strong><br>
                                No. Rawat: {{ $encrypNoRawat ?? 'Tidak ada' }}<br>
                                No. Rawat Decoded: {{ $noRawat ?? 'Tidak ada' }}<br>
                                Timestamp: {{ date('Y-m-d H:i:s') }}
                            </small>
                            <hr>
                            <a href="#" class="btn btn-sm btn-secondary reload-permintaan">
                                <i class="fas fa-sync-alt"></i> Muat Ulang Data
                            </a>
                        </div>
                    </td>
                `;
                tbody.appendChild(emptyRow);
                
                // Tambahkan event listener untuk reload data
                const reloadBtn = emptyRow.querySelector('.reload-permintaan');
                if (reloadBtn) {
                    reloadBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Hard reload halaman
                        window.location.reload(true);
                    });
                }
            }
        };
        
        // Jalankan fungsi saat halaman dimuat
        checkEmptyTable();
    });
</script>
@endpush