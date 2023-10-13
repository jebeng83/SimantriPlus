<div @if($isCollapsed) class="card card-info collapsed-card" @else class="card card-info" @endif>
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lg fa-file-medical mr-1"></i> Resume Pasien </h3>
        <div class="card-tools">
            {{-- <button type="button" class="btn btn-tool" wire:click="collapsed" data-card-widget="maximize">
                <i class="fas fa-lg fa-expand"></i>     
            </button> --}}
            <button type="button" wire:click="collapsed" class="btn btn-tool" data-card-widget="collapse">
                <i wire:ignore class="fas fa-lg fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="simpanResume">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="diagnosa-awal">Diagnosa Awal Masuk</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa_awal' id="diagnosa-awal" name="diagnosa-awal" >
                    @error('diagnosa_awal') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label for="alasan">Alasan Masuk Dirawat</label>
                    <input type="text" class="form-control" wire:model.defer='alasan' id="alasan" name="alasan" >
                    @error('alasan') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="fisik">Pemeriksaan Fisik</label>
                    <div class="input-group">
                        <textarea id="fisik" wire:model.defer='fisik' rows="3" class="form-control" type="text" name="fisik"></textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" wire:click="getPemeriksaanFisik" wire:loading.attr='disabled'>
                            <i class="fas fa-paperclip"></i>     
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="keluhan">Keluhan Utama</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.lazy="keluhan" id="keluhan" name="keluhan" ></textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" wire:click="getKeluhanUtama" wire:loading.attr='disabled'>
                            <i class="fas fa-paperclip"></i>     
                            </button>
                        </div>
                    </div>
                    @error('keluhan') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="perawatan">Jalannya Penyakit Selama Perawatan</label>
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='perawatan' id="perawatan" name="perawatan" ></textarea>
                    @error('perawatan') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="radiologi">Pemeriksaan Penunjang Rad Terpenting</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.defer='penunjang' id="radiologi" name="radiologi" ></textarea>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" wire:click='getPemeriksaanRadiologi' wire:loading.attr='disabled'>
                            <i class="fas fa-paperclip"></i>     
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label for="lab">Pemeriksaan Penunjang Lab Terpenting</label>
                    <div class="input-group">
                        <textarea type="text" rows="3" class="form-control" wire:model.defer='lab' id="lab" name="lab" ></textarea>
                        <div class="input-group-append">
                            <button type="button" wire:click='getPemeriksaanLab' class="btn btn-primary" wire:loading.attr='disabled'>
                            <i class="fas fa-paperclip"></i>     
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="operasi">Tindakan / Operasi Selama Perawatan</label>
                <div class="input-group">
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='operasi' id="operasi" name="operasi" ></textarea>
                    <div class="input-group-append">
                        <button type="button" wire:click='getTindakanOperasi' class="btn btn-primary" wire:loading.attr='disabled'>
                        <i class="fas fa-paperclip"></i>     
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="obat">Obat - obatan Selama Perawatan</label>
                <div class="input-group">
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='obat' id="obat" name="obat" ></textarea>
                    <div class="input-group-append">
                        <button type="button" wire:click='getObat' wire:loading.attr='disabled' class="btn btn-primary">
                        <i class="fas fa-paperclip"></i>     
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="diagnosa">Diagnosa Utama</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa' id="diagnosa" name="diagnosa" >
                    @error('diagnosa') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdDiagnosa">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdDiagnosa' id="kdDiagnosa" name="kdDiagnosa" >
                        @error('kdDiagnosa') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="diagnosa1">Diagnosa Sekunder 1</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa1' id="diagnosa1" name="diagnosa1" >
                    @error('diagnosa1') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdDiagnosa1">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdDiagnosa1' id="kdDiagnosa1" name="kdDiagnosa1" >
                        @error('kdDiagnosa1') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="diagnosa2">Diagnosa Sekunder 2</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa2' id="diagnosa2" name="diagnosa2" >
                    @error('diagnosa2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdDiagnosa2">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdDiagnosa2' id="kdDiagnosa2" name="kdDiagnosa2" >
                        @error('kdDiagnosa2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="diagnosa3">Diagnosa Sekunder 3</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa3' id="diagnosa3" name="diagnosa3" >
                    @error('diagnosa3') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdDiagnosa3">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdDiagnosa3' id="kdDiagnosa3" name="kdDiagnosa3" >
                        @error('kdDiagnosa3') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="diagnosa4">Diagnosa Sekunder 4</label>
                    <input type="text" class="form-control" wire:model.defer='diagnosa4' id="diagnosa4" name="diagnosa4" >
                    @error('diagnosa4') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdDiagnosa4">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdDiagnosa4' id="kdDiagnosa4" name="kdDiagnosa4" >
                        @error('kdDiagnosa4') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="prosedur">Prosedur Utama</label>
                    <input type="text" class="form-control" wire:model.defer='prosedur' id="prosedur" name="prosedur" >
                    @error('prosedur') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdProsedur">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdProsedur' id="kdProsedur" name="kdProsedur" >
                        @error('kdProsedur') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="prosedur1">Prosedur Sekunder 1</label>
                    <input type="text" class="form-control" wire:model.defer='prosedur1' id="prosedur1" name="prosedur1" >
                    @error('prosedur1') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdProsedur1">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdProsedur1' id="kdProsedur1" name="kdProsedur1" >
                        @error('kdProsedur1') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="prosedur2">Prosedur Sekunder 2</label>
                    <input type="text" class="form-control" wire:model.defer='prosedur2' id="prosedur2" name="prosedur2" >
                    @error('prosedur2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdProsedur2">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdProsedur2' id="kdProsedur2" name="kdProsedur2" >
                        @error('kdProsedur2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10">
                    <label for="prosedur3">Prosedur Sekunder 3</label>
                    <input type="text" class="form-control" wire:model.defer='prosedur3' id="prosedur3" name="prosedur3" >
                    @error('prosedur3') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label for="kdProsedur3">Kode ICD</label>
                        <input type="text" class="form-control" wire:model.defer='kdProsedur3' id="kdProsedur3" name="kdProsedur3" >
                        @error('kdProsedur3') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="alergi">Alergi Obat</label>
                    <input type="text" class="form-control" wire:model.defer='alergi' id="alergi" name="alergi" >
                    @error('alergi') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="diet">Diet</label>
                <div class="input-group">
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='diet' id="diet" name="diet" ></textarea>
                    <div class="input-group-append">
                        <button type="button" wire:click='getDiet' wire:loading.attr='disabled' class="btn btn-primary">
                        <i class="fas fa-paperclip"></i>     
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="labPending">Hasil Lab yang Belum Selesai (Pending)</label>
                <div class="input-group">
                    <textarea type="text" rows="3" class="form-control" wire:model.defer='labPending' id="labPending" name="labPending" ></textarea>
                    <div class="input-group-append">
                        <button type="button" wire:click='getLabPending' wire:loading.attr='disabled' class="btn btn-primary">
                        <i class="fas fa-paperclip"></i>     
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="instruksi">Instruksi/Anjuran Dan Edukasi (Follow Up)</label>
                <textarea type="text" rows="3" class="form-control" wire:model.defer='instruksi' id="instruksi" name="instruksi" ></textarea>
            </div>
            <div class="form-group">
                <label>Keadaan Pulang</label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" wire:model.defer='keadaanPulang' id="keadaanPulang" name="keadaanPulang">
                            <option value="Membaik">Membaik</option>
                            <option value="Sembuh">Sembuh</option>
                            <option value="Keadaan Khusus">Keadaan Khusus</option>
                            <option value="Meninggal">Meninggal</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control" wire:model.defer='keadaanPulangKet' id="keadaanPulangKet" name="keadaanPulangKet">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Cara Keluar</label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" wire:model.defer='caraKeluar' id="caraKeluar" name="caraKeluar">
                            <option value="Atas Izin Dokter">Atas Izin Dokter</option>
                            <option value="Pindah RS">Pindah RS</option>
                            <option value="Pulang Atas Permintaan Sendiri">Pulang Atas Permintaan Sendiri</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control" wire:model.defer='caraKeluarKet' id="caraKeluarKet" name="caraKeluarKet">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Dilanjutkan</label>
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control" wire:model.defer='dilanjutkan' id="dilanjutkan" name="dilanjutkan">
                            <option value="Kembali Ke RS">Kembali Ke RS</option>
                            <option value="RS Lainnya">RS Lainnya</option>
                            <option value="Dokter Luar">Dokter Luar</option>
                            <option value="Puskesmes">Puskesmes</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control" wire:model.defer='dilanjutkanKet' id="dilanjutkanKet" name="dilanjutkanKet">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="obatPulang">Obat Pulang</label>
                <textarea type="text" rows="3" class="form-control" wire:model.defer='obatPulang' id="obatPulang" name="obatPulang" ></textarea>
            </div>
            <div class="d-flex flex-row-reverse pb-3">
                <button class="btn btn-primary ml-1" type="submit" > Simpan </button>
            </div>
        </form>
        <h5> Daftar Resume Medis </h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-inverse" style="width: 100%">
                    <tr>
                        <th>Keluhan</th>
                        <th>Penunjang</th>
                        <th>Laboratorium</th>
                        <th>Diagnosa</th>
                        <th>Terapi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listResume as $item)
                    <tr>
                        <td>{{ $item->keluhan_utama }}</td>
                        <td>{{ $item->pemeriksaan_penunjang }}</td>
                        <td>{{ $item->hasil_laborat }}</td>
                        <td>{{ $item->diagnosa_utama }}</td>
                        <td>{{ $item->obat_pulang }}</td>
                        <td>
                            {{-- <button class="btn btn-primary btn-sm" wire:click="edit({{ $item->id }})">
                                <i class="fas fa-edit"></i>
                            </button> --}}
                            <button class="btn btn-danger btn-sm" wire:click="hapusResume('{{ $item->no_rawat }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Keluhan Utama -->
    <div class="modal fade" id="keluhanModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="keluhanModalTitle">Keluhan Utama</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                @forelse ($listKeluhan as $item)
                <div class="custom-control custom-checkbox">
                    <input id="keluhanCheck-{{ $item->jam_rawat }}" wire:key='{{ $item->jam_rawat }}' value="{{ $item->keluhan }}" wire:model.defer='checkKeluhan' class="custom-control-input" type="checkbox" name="keluhanCheck[]">
                    <label for="keluhanCheck-{{ $item->jam_rawat }}" class="custom-control-label">
                        {{ $item->keluhan }}
                    </label>
                </div> 
                @empty
                    <h5>Data Keluhan Kosong</h5>
                @endforelse
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahKeluhan' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div> 

    <!-- Modal Pemeriksaan Utama -->
    <div class="modal fade" id="pemeriksaanModal" tabindex="-1" role="dialog" aria-labelledby="pemeriksaanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="keluhanModalTitle">Pemeriksaan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                @forelse ($listPemeriksaan as $item)
                <div class="custom-control custom-checkbox">
                    <input id="pemeriksaanCheck-{{ $item->jam_rawat }}" wire:key='{{ $item->jam_rawat }}' value="{{ $item->pemeriksaan }}" wire:model.defer='checkFisik' class="custom-control-input" type="checkbox" name="pemeriksaanCheck-{{ $item->jam_rawat }}">
                    <label for="pemeriksaanCheck-{{ $item->jam_rawat }}" class="custom-control-label">
                        {{ $item->pemeriksaan }}
                    </label>
                </div> 
                @empty
                    <h5>Data Pemeriksaan Kosong</h5>
                @endforelse
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahPemeriksaanFisik' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div> 
    
    <!-- Modal Penunjang -->
    <div class="modal fade" id="radiologiModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="keluhanModalTitle">Pemeriksaan Radiologi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listRadiologi as $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="radCheck-{{  $item->jam }}" wire:key='{{ $item->jam }}' class="custom-control-input" wire:model.defer='checkRadiologi' value="{{ $item->hasil }}" type="checkbox" name="radCheck[]">
                            <label for="radCheck-{{  $item->jam }}" class="custom-control-label">
                                <pre>{{ $item->hasil }}</pre>
                            </label>
                        </div> 
                    @empty
                        <h5>Data Pemeriksaan Radiologi Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahPemeriksaanRadiologi' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

        <!-- Modal Keluhan Utama -->
    <div class="modal fade" id="keluhanModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="keluhanModalTitle">Keluhan Utama</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                @forelse ($listKeluhan as $item)
                <div class="custom-control custom-checkbox">
                    <input id="keluhanCheck-{{ $item->jam_rawat }}" wire:key='{{ $item->jam_rawat }}' value="{{ $item->keluhan }}" wire:model.defer='checkKeluhan' class="custom-control-input" type="checkbox" name="keluhanCheck[]">
                    <label for="keluhanCheck-{{ $item->jam_rawat }}" class="custom-control-label">
                        {{ $item->keluhan }}
                    </label>
                </div> 
                @empty
                    <h5>Data Keluhan Kosong</h5>
                @endforelse
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahKeluhan' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div> 
    
    <!-- Modal Penunjang -->
    <div class="modal fade" id="radiologiModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="keluhanModalTitle">Pemeriksaan Radiologi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listRadiologi as $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="radCheck-{{  $item->jam }}" wire:key='{{ $item->jam }}' class="custom-control-input" wire:model.defer='checkRadiologi' value="{{ $item->hasil }}" type="checkbox" name="radCheck[]">
                            <label for="radCheck-{{  $item->jam }}" class="custom-control-label">
                                <pre>{{ $item->hasil }}</pre>
                            </label>
                        </div> 
                    @empty
                        <h5>Data Pemeriksaan Radiologi Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahPemeriksaanRadiologi' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Modal Pemeriksaan Lab -->
    <div class="modal fade" id="labModal" tabindex="-1" role="dialog" aria-labelledby="labModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="labModalTitle">Pemeriksaan Laboratorium</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listLab as $i => $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="labCheck-{{ $i }}" wire:key='labCheck-{{ $i }}' class="custom-control-input" wire:model.defer='checkLab' value="{{ $item->Pemeriksaan }} : {{ $item->nilai }}" type="checkbox" name="labCheck[]">
                            <label for="labCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->Pemeriksaan }} : {{ $item->nilai }}
                            </label>
                        </div> 
                    @empty
                        <h5>Data Pemeriksaan Lab Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahPemeriksaanLab' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal Terapi -->
    <div wire:ignore.self class="modal fade" id="tindakanModal" tabindex="-1" role="dialog" aria-labelledby="tindakanModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="tindakanModalTitle">Tindakan Dokter</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listTindakanOperasi as $i => $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="tindakanCheck-{{ $i }}" wire:key='tindakanCheck-{{ $i }}' class="custom-control-input" wire:model.defer='checkTindakanOperasi' value="{{ $item->nm_perawatan }}" type="checkbox" name="tindakanCheck[]">
                            <label for="tindakanCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->nm_perawatan }}
                            </label>
                        </div> 
                    @empty
                        <h5>Data Tindakan Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahTindakanOperasi' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal Obat -->
    <div wire:ignore.self class="modal fade" id="obatModal" tabindex="-1" role="dialog" aria-labelledby="obatModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="obatModalTitle">Obat</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listObat as $i => $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="obatCheck-{{ $i }}" wire:key='obatCheck-{{ $i }}' class="custom-control-input" wire:model.defer='checkObat' value="{{ $item->nama_brng }} : {{ $item->jml }}  {{ $item->kode_sat }}" type="checkbox" name="obatCheck[]">
                            <label for="obatCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->nama_brng }} : {{ $item->jml }}  {{ $item->kode_sat }}
                            </label>
                        </div> 
                    @empty
                        <h5>Data Obat Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahObat' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal Diet -->
    <div wire:ignore.self class="modal fade" id="dietModal" tabindex="-1" role="dialog" aria-labelledby="dietModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="dietModalTitle">Riwyat Diet</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listDiet as $i => $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="dietCheck-{{ $i }}" wire:key='dietCheck-{{ $i }}' class="custom-control-input" wire:model.defer='checkDiet' value="{{ $item->nama_diet }}" type="checkbox" name="dietCheck[]">
                            <label for="dietCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->tanggal }} : {{ $item->waktu }}  {{ $item->nama_diet }}
                            </label>
                        </div> 
                    @empty
                        <h5>Data Diet Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahDiet' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal Pemeriksaan Lab Pending -->
    <div class="modal fade" id="labPendingModal" tabindex="-1" role="dialog" aria-labelledby="labPendingModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="labPendingModalTitle">Pemeriksaan Laboratorium Pending</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @forelse ($listLabPending as $i => $item)  
                        <div class="custom-control custom-checkbox">
                            <input id="labPendingCheck-{{ $i }}" wire:key='labPendingCheck-{{ $i }}' class="custom-control-input" wire:model.defer='checkLabPending' value="{{ $item->Pemeriksaan }}" type="checkbox" name="labPendingCheck[]">
                            <label for="labPendingCheck-{{ $i }}" class="custom-control-label">
                                {{ $item->Pemeriksaan }}
                            </label>
                        </div> 
                    @empty
                        <h5>Data Pemeriksaan Lab Kosong</h5>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" wire:click='tambahPemeriksaanLabPending' class="btn btn-primary">Ok</button>
            </div>
        </div>
        </div>
    </div>

</div>

@push('js')
    <script>
        $('#dietModal').on('hidden.bs.modal', function () {
            @this.listDiet = [];
        });

        $('#obatModal').on('hidden.bs.modal', function () {
            @this.listObat = [];
        });

        $('#tindakanModal').on('hidden.bs.modal', function () {
            @this.listTindakanOperasi = [];
        });

        $('#pemeriksaanModal').on('hidden.bs.modal', function () {
            @this.listPemeriksaan = [];
        });

        $('#keluhanModal').on('hidden.bs.modal', function () {
            @this.listKeluhan = [];
        });

        $('#radiologiModal').on('hidden.bs.modal', function () {
            @this.listRadiologi = [];
        });

        $('#labModal').on('hidden.bs.modal', function () {
            @this.listLab = [];
        });

        $('#labPendingModal').on('hidden.bs.modal', function () {
            @this.listLabPending = [];
        });

        window.livewire.on('closeLabPendingModal', () => {
            $('#labPendingModal').modal('hide');
        });

        window.livewire.on('openLabPendingModal', () => {
            $('#labPendingModal').modal('show');
        });

        window.livewire.on('closeDietModal', () => {
            $('#dietModal').modal('hide');
        });

        window.livewire.on('openDietModal', () => {
            $('#dietModal').modal('show');
        });

        window.addEventListener('closeObatModal', () => {
            $('#obatModal').modal('hide');
        });

        window.addEventListener('openObatModal', () => {
            $('#obatModal').modal('show');
        });

        window.addEventListener('closeTindakanOperasiModal', () => {
            $('#tindakanModal').modal('hide');
        });

        window.addEventListener('openTindakanOperasiModal', () => {
            $('#tindakanModal').modal('show');
        });

        window.livewire.on('getPemeriksaanFisik', () => {
            $('#pemeriksaanModal').modal('show');
        });

        window.livewire.on('closePemeriksaanFisikModal', () => {
            $('#pemeriksaanModal').modal('hide');
        });

        window.livewire.on('getKeluhanUtama',() => {
            $('#keluhanModal').modal('show');
        });

        window.livewire.on('closeKeluhanModal',() => {
            $('#keluhanModal').modal('hide');
        });

        window.livewire.on('getPemeriksaanRadiologi',() => {
            $('#radiologiModal').modal('show');
        });

        window.livewire.on('closePemeriksaanRadiologiModal',() => {
            $('#radiologiModal').modal('hide');
        });

        window.livewire.on('getPemeriksaanLab',() => {
            $('#labModal').modal('show');
        });

        window.livewire.on('closePemeriksaanLabModal',() => {
            $('#labModal').modal('hide');
        });

        window.livewire.on('getTerapi',() => {
            $('#terapiModal').modal('show');
        });

        window.livewire.on('closeTerapiModal',() => {
            $('#terapiModal').modal('hide');
        });
    </script>
@endpush