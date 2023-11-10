<div>
    <form wire:submit.prevent='simpan'>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tanggal_operasi">Tanggal Operasi</label>
                    <div wire:ignore.self class="input-group date" id="tanggal_operasi" data-target-input="nearest">
                        <input wire:model.defer='tanggal_operasi' type="text" class="form-control datetimepicker-input" data-target="#tanggal_operasi" @if($modeEdit) disabled @endif/>
                        <div class="input-group-append" data-target="#tanggal_operasi" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                    @error('tanggal_operasi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <div wire:ignore.self class="input-group date" id="tanggal_selesai" data-target-input="nearest">
                        <input wire:model.defer='tanggal_selesai' type="text" class="form-control datetimepicker-input" data-target="#tanggal_selesai" @if($modeEdit) disabled @endif/>
                        <div class="input-group-append" data-target="#tanggal_selesai" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                    @error('tanggal_selesai') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="diagnosa_pra_bedah">Diagnosa Pra Bedah</label>
                    <textarea wire:model.defer='diagnosa_pra_bedah' rows="3" id="diagnosa_pra_bedah" class="form-control @error('diagnosa_pra_bedah') is-invalid @enderror" type="text" name="diagnosa_pra_bedah"></textarea>
                    @error('diagnosa_pra_bedah') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="diagnosa_pasca_bedah">Diagnosa Pasca Bedah</label>
                    <textarea wire:model.defer='diagnosa_pasca_bedah' id="diagnosa_pasca_bedah" class="form-control @error('diagnosa_pasca_bedah') is-invalid @enderror" type="text" name="diagnosa_pasca_bedah" rows="3"></textarea>
                    @error('diagnosa_pasca_bedah') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                  <label for="uraian_bedah">Uraian Bedah</label>
                  <textarea wire:model.defer='uraian_bedah' class="form-control @error('uraian_bedah') is-invalid @enderror" name="uraian_bedah" id="uraian_bedah" rows="3"></textarea>
                    @error('uraian_bedah') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                  <label for="tindakan_bedah">Tindakan Bedah</label>
                  <textarea wire:model.defer='tindakan_bedah' class="form-control @error('tindakan_bedah') is-invalid @enderror" name="tindakan_bedah" id="tindakan_bedah" rows="3"></textarea>
                    @error('tindakan_bedah') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4 col-md-4">
                <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#modal-template-operasi">
                    Template
                </button>
            </div>
            <div class="col-4 col-sm-4">
                <button wire:click='resetInput' type="reset" class="btn btn-block btn-danger">Reset</button>
            </div>
            <div class="col-4 col-sm-4">
                <button type="submit" class="btn btn-block @if($modeEdit) btn-info @else btn-primary @endif">@if($modeEdit) Ubah @else Simpan @endif</button>
            </div>
        </div>
    </form>
    <h5 class="mt-3">Data Laporan Operasi</h5>
    <div wire:init='getData' class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tgl Operasi</th>
                    <th scope="col">Tgl Selesai</th>
                    <th scope="col">Diagnosa Pra Bedah</th>
                    <th scope="col">Diagnosa Pasca Bedah</th>
                    <th scope="col">Uraian Bedah</th>
                    <th scope="col">Tindakan Bedah</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tanggal_operasi }}</td>
                    <td>{{ $item->tanggal_selesai }}</td>
                    <td>{{ $item->diagnosa_pra_bedah }}</td>
                    <td>{{ $item->diagnosa_pasca_bedah }}</td>
                    <td>{{ $item->uraian_bedah }}</td>
                    <td>{{ $item->tindakan_bedah }}</td>
                    <td>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <button wire:click='edit("{{$item->tanggal_operasi}}","{{$item->tanggal_selesai}}")' type="button" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                            <button wire:click='confirmHapus("{{$item->tanggal_operasi}}","{{$item->tanggal_selesai}}")' type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Data masih kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('js')
    <script>
        $(function () {
            $('#tanggal_operasi').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                // defaultDate: moment(),
                allowInputToggle: true,
                icons: {
                    time: "fa fa-clock",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                }
            });
            $('#tanggal_selesai').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                // defaultDate: moment(),
                allowInputToggle: true,
                icons: {
                    time: "fa fa-clock",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-screenshot",
                    clear: "fa fa-trash",
                    close: "fa fa-remove"
                }
            });

            
            $('#tanggal_operasi').on('change.datetimepicker', function(e) {
                console.log(e.date);
                if(!e.date){
                    @this.set('tanggal_operasi', '', true);
                }else{
                    @this.set('tanggal_operasi', e.date.format('YYYY-MM-DD HH:mm:ss'), true);
                    console.log(@this.tanggal_operasi);
                }
            });

            $('#tanggal_selesai').on('change.datetimepicker', function(e) {
                if(!e.date){
                    @this.set('tanggal_selesai', '', true);
                }else{
                    @this.set('tanggal_selesai', e.date.format('YYYY-MM-DD HH:mm:ss'), true);
                }
            });

            Livewire.on('resetInput', e => {
                $('#tanggal_operasi').datetimepicker('clear');
                $('#tanggal_selesai').datetimepicker('clear');
            });
        });
    </script>
@endpush
