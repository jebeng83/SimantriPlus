<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-flask mr-1"></i> Permintaan Lab </h3>
        <div class="card-tools">
            {{-- <button type="button" wire:click="expanded" class="btn btn-tool" data-card-widget="maximize">
                <i wire:ignore class="fas fa-lg fa-expand"></i>
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(config('app.env') === 'local')
        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            No Rawat (Encrypted): {{ $noRawatEncrypted }}<br>
            No Rawat (Decrypted): {{ $noRawat }}<br>
            Jumlah Data: {{ count($permintaanLab) }}<br>
            Waktu: {{ now() }}
        </div>
        @endif

        @if(empty($noRawat))
        <div class="alert alert-warning">
            No Rawat kosong setelah dekripsi. Mohon refresh halaman atau hubungi administrator.
        </div>
        @endif

        <form wire:submit.prevent="savePermintaanLab">
            <div class="form-group row">
                <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" wire:model.defer="klinis" id="klinis" name="klinis" {{
                        old('klinis', $klinis ?? '-' ) }} />
                    @error('klinis') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group row">
                <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" wire:model.defer="info" id="info" name="info" {{ old('info',
                        $info ?? '-' ) }} />
                    @error('info') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div wire:ignore class="form-group row">
                <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
                <div class="col-sm-8">
                    <select class="form-control jenis" wire:model="jns_pemeriksaan" id="jenis_lab" name="jenis[]"
                        multiple="multiple"></select>
                </div>
                @error('jns_pemeriksaan') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- Container untuk template -->
            @if(!empty($selectedTemplate))
            <div class="template-container mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Template Pemeriksaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Pemeriksaan</th>
                                        <th>Nilai Rujukan</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedTemplate as $template)
                                    <tr>
                                        <td>{{ $template->nama_pemeriksaan }}</td>
                                        <td>{{ $template->nilai_rujukan }}</td>
                                        <td>{{ $template->satuan }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit"> Simpan </button>
            </div>
        </form>
        <div class="mt-4">
            <h5 class="mb-3">Daftar Permintaan Lab</h5>

            <div class="table-responsive" wire:poll.30s>
                <!-- Debug info -->
                @if(app()->environment('local'))
                <div class="alert alert-info">
                    <small>
                        <strong>Debug Info:</strong><br>
                        No Rawat: {{ $noRawat }}<br>
                        Jumlah Data: {{ $permintaanLab->count() }}<br>
                        Timestamp: {{ now() }}
                    </small>
                </div>
                @endif

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Tanggal</th>
                            <th>Informasi</th>
                            <th>Klinis</th>
                            <th>Pemeriksaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permintaanLab as $item)
                        <tr wire:key="lab-{{ $item->noorder }}">
                            <td>{{ $item->noorder }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_permintaan)->format('d/m/Y') }} {{
                                $item->jam_permintaan }}</td>
                            <td>{{ $item->informasi_tambahan ?: '-' }}</td>
                            <td>{{ $item->diagnosa_klinis ?: '-' }}</td>
                            <td>
                                @php
                                $pemeriksaanList = $this->getDetailPemeriksaan($item->noorder);
                                @endphp
                                @forelse($pemeriksaanList as $pemeriksaan)
                                <div class="badge badge-info mb-1">{{ $pemeriksaan->nm_perawatan }}</div>
                                @empty
                                <span class="text-muted">Tidak ada pemeriksaan</span>
                                @endforelse
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="konfirmasiHapus('{{ $item->noorder }}')" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="konfirmasiHapus('{{ $item->noorder }}')">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="konfirmasiHapus('{{ $item->noorder }}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-info">
                                    <p class="mb-0">Belum ada permintaan laboratorium</p>
                                    <small class="d-block mt-1">
                                        <i class="fas fa-info-circle"></i> No Rawat: {{ $noRawat }}
                                    </small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    window.addEventListener('swal',function(e){
            Swal.fire(e.detail);
        });

        window.addEventListener('refreshPage', function(e) {
            console.log('Permintaan refresh halaman diterima:', e.detail);
            // Setelah delay 1 detik, refresh halaman untuk memastikan data muncul
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        });

        window.addEventListener('swal:confirm',function(e){
            Swal.fire({
                title: e.detail.title,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: e.detail.confirmButtonText,
                cancelButtonText: e.detail.cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit(e.detail.function, e.detail.params[0]);
                }
            });
        });

        function formatData (data) {
            var $data = $(
                '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
            );
            return $data;
        };

        $('#jenis_lab').select2({
            placeholder: 'Pilih Jenis',
            ajax: {
                url: '/api/jns_perawatan_lab',
                dataType: 'json',
                delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3
        });

        $('#jenis_lab').on('change', function (e) {
            let data = $(this).val();
            @this.set('jns_pemeriksaan', data);
        });

        window.livewire.on('select2Lab:reset', () => {
            $('#jenis_lab').val("").trigger('change');
        });

        window.livewire.on('select2Lab', () => {
            $('#jenis_lab').select2({
            placeholder: 'Pilih Jenis',
            ajax: {
                url: '/api/jns_perawatan_lab',
                dataType: 'json',
                delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                cache: true
                },
                templateResult: formatData,
                minimumInputLength: 3
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Debug untuk memastikan Livewire terload
            if (window.livewire) {
                console.log('Livewire detected and ready');
            }
            
            // Listener untuk event refreshPermintaanLab
            window.livewire.on('refreshPermintaanLab', () => {
                console.log('Event refreshPermintaanLab diterima');
                @this.getPermintaanLab();
            });

            // Listener untuk event dataPermintaanLabUpdated
            window.livewire.on('dataPermintaanLabUpdated', () => {
                console.log('Data permintaan lab telah diperbarui');
            });

            // Auto refresh setiap 30 detik
            setInterval(function() {
                console.log('Auto refresh permintaan lab');
                @this.getPermintaanLab();
            }, 30000);
        });
</script>
@endpush