@extends('adminlte::page')

@section('title', 'Edit Pasien')

@section('content_header')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<script>
    try {
        var opts = JSON.parse(localStorage.getItem('AdminLTE:IFrame:Options'));
        if (!opts || typeof opts !== 'object') { opts = {}; }
        opts.autoIframeMode = false;
        localStorage.setItem('AdminLTE:IFrame:Options', JSON.stringify(opts));
    } catch (e) {
        localStorage.setItem('AdminLTE:IFrame:Options', '{"autoIframeMode":false}');
    }
</script>
<script>
    (function(){
        function collapseSidebar(){
            try {
                var $ = window.$ || window.jQuery;
                if ($ && $.fn && $.fn.PushMenu) {
                    if (!document.body.classList.contains('sidebar-collapse')) {
                        $('[data-widget="pushmenu"]').PushMenu('collapse');
                    }
                } else {
                    document.body.classList.add('sidebar-collapse');
                }
            } catch (e) {
                document.body.classList.add('sidebar-collapse');
            }
        }

        try {
            if (window.self !== window.top) {
                if (document.readyState === 'complete') {
                    collapseSidebar();
                } else {
                    document.addEventListener('DOMContentLoaded', collapseSidebar);
                    window.addEventListener('load', collapseSidebar);
                    setTimeout(collapseSidebar, 500);
                }
            }
        } catch (e) {
            document.addEventListener('DOMContentLoaded', collapseSidebar);
        }
    })();
</script>
<h1>Edit Data Pasien</h1>
@stop

@section('content')
<x-adminlte-card>
    <form action="{{ route('pasien.update', $pasien->no_rkm_medis) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- <div class="form-group">
            <label for="no_rkm_medis">No Rekam Medis</label>
            <input type="text" class="form-control" id="no_rkm_medis" name="no_rkm_medis"
                value="{{ $pasien->no_rkm_medis }}" disabled>
            @error('no_rkm_medis')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div> --}}
        <div class="form-group">
            <label for="no_ktp">KTP</label>
            <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="{{ $pasien->no_ktp }}">
            @error('no_ktp')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_kk">No. KK</label>
            <input type="text" class="form-control" id="no_kk" name="no_kk" value="{{ $pasien->no_kk }}">
            @error('no_kk')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="nm_pasien">Nama</label>
            <input type="text" class="form-control" id="nm_pasien" name="nm_pasien" value="{{ $pasien->nm_pasien }}">
            @error('nm_pasien')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="tgl_lahir">Tgl Lahir</label>
            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ $pasien->tgl_lahir }}">
            @error('tgl_lahir')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select type="text" class="form-control" id="status" name="status">
                <option value="Kepala Keluarga" @if($pasien->status == 'Kepala Keluarga') selected @endif>Kepala
                    Keluarga</option>
                <option value="Suami" @if($pasien->status == 'Suami') selected @endif>Suami</option>
                <option value="Istri" @if($pasien->status == 'Istri') selected @endif>Istri</option>
                <option value="Anak" @if($pasien->status == 'Anak') selected @endif>Anak</option>
                <option value="Menantu" @if($pasien->status == 'Menantu') selected @endif>Menantu</option>
                <option value="Orang tua" @if($pasien->status == 'Orang tua') selected @endif>Orang tua</option>
                <option value="Mertua" @if($pasien->status == 'Mertua') selected @endif>Mertua</option>
                <option value="Pembantu" @if($pasien->status == 'Pembantu') selected @endif>Pembantu</option>
                <option value="Famili Lain" @if($pasien->status == 'Famili Lain') selected @endif>Famili Lain</option>
                <option value="Lainnya" @if($pasien->status == 'Lainnya') selected @endif>Lainnya</option>
            </select>
            @error('status')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="stts_nikah">Status Nikah</label>
            <select type="text" class="form-control" id="stts_nikah" name="stts_nikah">
                <option value="MENIKAH" @if($pasien->stts_nikah == 'MENIKAH') selected @endif>MENIKAH</option>
                <option value="BELUM MENIKAH" @if($pasien->stts_nikah == 'BELUM MENIKAH') selected @endif>BELUM MENIKAH
                </option>
                <option value="JANDA" @if($pasien->stts_nikah == 'JANDA') selected @endif>JANDA</option>
                <option value="DUDHA" @if($pasien->stts_nikah == 'DUDHA') selected @endif>DUDHA</option>
                <option value="JOMBLO" @if($pasien->stts_nikah == 'JOMBLO') selected @endif>JOMBLO</option>
            </select>
            @error('stts_nikah')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="pekerjaan">Pekerjaan</label>
            <select class="form-control" id="pekerjaan" name="pekerjaan">
                <option value="">-- Pilih Pekerjaan --</option>
                <option value="Belum/Tidak Bekerja" @if($pasien->pekerjaan == 'Belum/Tidak Bekerja') selected @endif>Belum/Tidak Bekerja</option>
                <option value="Pelajar" @if($pasien->pekerjaan == 'Pelajar') selected @endif>Pelajar</option>
                <option value="Mahasiswa" @if($pasien->pekerjaan == 'Mahasiswa') selected @endif>Mahasiswa</option>
                <option value="Ibu Rumah Tangga" @if($pasien->pekerjaan == 'Ibu Rumah Tangga') selected @endif>Ibu Rumah Tangga</option>
                <option value="TNI" @if($pasien->pekerjaan == 'TNI') selected @endif>TNI</option>
                <option value="POLRI" @if($pasien->pekerjaan == 'POLRI') selected @endif>POLRI</option>
                <option value="ASN (Kantor Pemerintah)" @if($pasien->pekerjaan == 'ASN (Kantor Pemerintah)') selected @endif>ASN (Kantor Pemerintah)</option>
                <option value="Pegawai Swasta" @if($pasien->pekerjaan == 'Pegawai Swasta') selected @endif>Pegawai Swasta</option>
                <option value="Wiraswasta/Pekerja Mandiri" @if($pasien->pekerjaan == 'Wiraswasta/Pekerja Mandiri') selected @endif>Wiraswasta/Pekerja Mandiri</option>
                <option value="Pensiunan" @if($pasien->pekerjaan == 'Pensiunan') selected @endif>Pensiunan</option>
                <option value="Pejabat Negara / Pejabat Daerah" @if($pasien->pekerjaan == 'Pejabat Negara / Pejabat Daerah') selected @endif>Pejabat Negara / Pejabat Daerah</option>
                <option value="Pengusaha" @if($pasien->pekerjaan == 'Pengusaha') selected @endif>Pengusaha</option>
                <option value="Dokter" @if($pasien->pekerjaan == 'Dokter') selected @endif>Dokter</option>
                <option value="Bidan" @if($pasien->pekerjaan == 'Bidan') selected @endif>Bidan</option>
                <option value="Perawat" @if($pasien->pekerjaan == 'Perawat') selected @endif>Perawat</option>
                <option value="Apoteker" @if($pasien->pekerjaan == 'Apoteker') selected @endif>Apoteker</option>
                <option value="Psikolog" @if($pasien->pekerjaan == 'Psikolog') selected @endif>Psikolog</option>
                <option value="Tenaga Kesehatan Lainnya" @if($pasien->pekerjaan == 'Tenaga Kesehatan Lainnya') selected @endif>Tenaga Kesehatan Lainnya</option>
                <option value="Dosen" @if($pasien->pekerjaan == 'Dosen') selected @endif>Dosen</option>
                <option value="Guru" @if($pasien->pekerjaan == 'Guru') selected @endif>Guru</option>
                <option value="Peneliti" @if($pasien->pekerjaan == 'Peneliti') selected @endif>Peneliti</option>
                <option value="Pengacara" @if($pasien->pekerjaan == 'Pengacara') selected @endif>Pengacara</option>
                <option value="Notaris" @if($pasien->pekerjaan == 'Notaris') selected @endif>Notaris</option>
                <option value="Hakim/Jaksa/Tenaga Peradilan Lainnya" @if($pasien->pekerjaan == 'Hakim/Jaksa/Tenaga Peradilan Lainnya') selected @endif>Hakim/Jaksa/Tenaga Peradilan Lainnya</option>
                <option value="Akuntan" @if($pasien->pekerjaan == 'Akuntan') selected @endif>Akuntan</option>
                <option value="Insinyur" @if($pasien->pekerjaan == 'Insinyur') selected @endif>Insinyur</option>
                <option value="Arsitek" @if($pasien->pekerjaan == 'Arsitek') selected @endif>Arsitek</option>
                <option value="Konsultan" @if($pasien->pekerjaan == 'Konsultan') selected @endif>Konsultan</option>
                <option value="Wartawan" @if($pasien->pekerjaan == 'Wartawan') selected @endif>Wartawan</option>
                <option value="Pedagang" @if($pasien->pekerjaan == 'Pedagang') selected @endif>Pedagang</option>
                <option value="Petani / Pekebun" @if($pasien->pekerjaan == 'Petani / Pekebun') selected @endif>Petani / Pekebun</option>
                <option value="PETANI/PEKEBUN" @if($pasien->pekerjaan == 'PETANI/PEKEBUN') selected @endif>PETANI/PEKEBUN</option>
                <option value="Nelayan / Perikanan" @if($pasien->pekerjaan == 'Nelayan / Perikanan') selected @endif>Nelayan / Perikanan</option>
                <option value="Peternak" @if($pasien->pekerjaan == 'Peternak') selected @endif>Peternak</option>
                <option value="Tokoh Agama" @if($pasien->pekerjaan == 'Tokoh Agama') selected @endif>Tokoh Agama</option>
                <option value="Juru Masak" @if($pasien->pekerjaan == 'Juru Masak') selected @endif>Juru Masak</option>
                <option value="Pelaut" @if($pasien->pekerjaan == 'Pelaut') selected @endif>Pelaut</option>
                <option value="Sopir" @if($pasien->pekerjaan == 'Sopir') selected @endif>Sopir</option>
                <option value="Pilot" @if($pasien->pekerjaan == 'Pilot') selected @endif>Pilot</option>
                <option value="Masinis" @if($pasien->pekerjaan == 'Masinis') selected @endif>Masinis</option>
                <option value="Atlet" @if($pasien->pekerjaan == 'Atlet') selected @endif>Atlet</option>
                <option value="Pekerja Seni" @if($pasien->pekerjaan == 'Pekerja Seni') selected @endif>Pekerja Seni</option>
                <option value="Penjahit / Perancang Busana" @if($pasien->pekerjaan == 'Penjahit / Perancang Busana') selected @endif>Penjahit / Perancang Busana</option>
                <option value="Karyawan kantor / Pegawai Administratif" @if($pasien->pekerjaan == 'Karyawan kantor / Pegawai Administratif') selected @endif>Karyawan kantor / Pegawai Administratif</option>
                <option value="Teknisi / Mekanik" @if($pasien->pekerjaan == 'Teknisi / Mekanik') selected @endif>Teknisi / Mekanik</option>
                <option value="Pekerja Pabrik / Buruh" @if($pasien->pekerjaan == 'Pekerja Pabrik / Buruh') selected @endif>Pekerja Pabrik / Buruh</option>
                <option value="Pekerja Konstruksi" @if($pasien->pekerjaan == 'Pekerja Konstruksi') selected @endif>Pekerja Konstruksi</option>
                <option value="Pekerja Pertukangan" @if($pasien->pekerjaan == 'Pekerja Pertukangan') selected @endif>Pekerja Pertukangan</option>
                <option value="Pekerja Migran" @if($pasien->pekerjaan == 'Pekerja Migran') selected @endif>Pekerja Migran</option>
                <option value="Lainnya" @if($pasien->pekerjaan == 'Lainnya') selected @endif>Lainnya</option>
            </select>
            @error('pekerjaan')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="kd_pj">Penjab</label>
            <select class="form-control" id="kd_pj" name="kd_pj">
                <option value="">Pilih Penjab</option>
                @foreach($penjab as $pj)
                <option value="{{ $pj->kd_pj }}" @if($pasien->kd_pj == $pj->kd_pj) selected @endif>
                    {{ $pj->png_jawab }}
                </option>
                @endforeach
            </select>
            @error('kd_pj')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_peserta">No. Peserta</label>
            <input type="text" class="form-control" id="no_peserta" name="no_peserta" value="{{ $pasien->no_peserta }}">
            @error('no_peserta')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="no_tlp">No. Telp</label>
            <input type="text" class="form-control" id="no_tlp" name="no_tlp" value="{{ $pasien->no_tlp }}">
            @error('no_tlp')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea type="text" class="form-control" id="alamat" name="alamat"
                rows="4">{{ $pasien->alamat }}</textarea>
            @error('alamat')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="kd_kab">Kabupaten</label>
                    <select class="form-control" id="kd_kab" name="kd_kab">
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    @error('kd_kab')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="kd_kec">Kecamatan</label>
                    <select class="form-control" id="kd_kec" name="kd_kec">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    @error('kd_kec')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="kd_kel">Kelurahan</label>
                    <select class="form-control" id="kd_kel" name="kd_kel">
                        <option value="">Pilih Kelurahan</option>
                    </select>
                    @error('kd_kel')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="data_posyandu">Posyandu</label>
            <select type="text" class="form-control" id="data_posyandu" name="data_posyandu">
                @foreach($posyandu as $pos)
                <option value="{{ $pos->nama_posyandu }}" @if($pasien->data_posyandu == $pos->nama_posyandu) selected
                    @endif>{{ $pos->nama_posyandu }}</option>
                @endforeach
            </select>
            @error('data_posyandu')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="{{ route('pasien.index') }}" class="btn btn-secondary">Kembali</a>

        
    </form>
</x-adminlte-card>
@stop

@section('plugins.TempusDominusBs4', true)

@section('css')
<style>
    .form-group label {
        font-weight: 600;
        color: #333;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
        transform: translateY(-2px);
    }

    .btn-secondary {
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        function setKabOptions(rows, selected) {
            var $sel = $('#kd_kab');
            $sel.empty();
            $sel.append($('<option>').val('').text('Pilih Kabupaten'));
            if (Array.isArray(rows)) {
                rows.forEach(function(r){
                    var opt = $('<option>').val(r.kd_kab).text(r.nm_kab || r.nama || r.kd_kab);
                    $sel.append(opt);
                });
            }
            if (selected) {
                $sel.val(String(selected));
            }
        }

        function setKecOptions(rows, selected) {
            var $sel = $('#kd_kec');
            $sel.empty();
            $sel.append($('<option>').val('').text('Pilih Kecamatan'));
            if (Array.isArray(rows)) {
                rows.forEach(function(r){
                    var opt = $('<option>').val(r.kd_kec).text(r.nm_kec || r.nama || r.kd_kec);
                    $sel.append(opt);
                });
            }
            if (selected) {
                $sel.val(String(selected));
            }
        }

        function setKelOptions(rows, selected) {
            var $sel = $('#kd_kel');
            $sel.empty();
            $sel.append($('<option>').val('').text('Pilih Kelurahan'));
            if (Array.isArray(rows)) {
                rows.forEach(function(r){
                    var opt = $('<option>').val(r.kd_kel).text(r.nm_kel || r.nama || r.kd_kel);
                    $sel.append(opt);
                });
            }
            if (selected) {
                $sel.val(String(selected));
            }
        }

        function loadKabupatenList(kdProp, selectedKdKab) {
            var url = kdProp ? "{{ route('kabupaten') }}" : "/ranap/laporan/grafik/kabupaten-db";
            var params = kdProp ? { kd_prop: kdProp } : {};
            $.ajax({
                url: url,
                type: 'GET',
                data: params,
                dataType: 'json',
                cache: false,
                success: function(rows) {
                    setKabOptions(rows, selectedKdKab);
                },
                error: function() {
                    setKabOptions([], selectedKdKab);
                }
            });
        }

        function loadKecamatanList(kdKab, selectedKdKec) {
            var url = kdKab ? "{{ route('kecamatan') }}" : "/ranap/laporan/grafik/kecamatan-all";
            var params = kdKab ? { kd_kab: kdKab } : {};
            $.ajax({
                url: url,
                type: 'GET',
                data: params,
                dataType: 'json',
                cache: false,
                success: function(rows) {
                    setKecOptions(rows, selectedKdKec);
                },
                error: function() {
                    setKecOptions([], selectedKdKec);
                }
            });
        }

        function loadKelurahanList(kdKec, selectedKdKel) {
            var url = kdKec ? "{{ route('kelurahan') }}" : "/ranap/laporan/grafik/kelurahan-all";
            var params = kdKec ? { kd_kec: kdKec } : {};
            $.ajax({
                url: url,
                type: 'GET',
                data: params,
                dataType: 'json',
                cache: false,
                success: function(rows) {
                    setKelOptions(rows, selectedKdKel);
                },
                error: function() {
                    setKelOptions([], selectedKdKel);
                }
            });
        }

        var kd_prop_initial = "{{ $pasien->kd_prop ?? '' }}";
        var kd_kab_initial = "{{ $pasien->kd_kab ?? '' }}";
        var kd_kec_initial = "{{ $pasien->kd_kec ?? '' }}";
        var kd_kel_initial = "{{ $pasien->kd_kel ?? '' }}";
        loadKabupatenList(kd_prop_initial, kd_kab_initial);
        loadKecamatanList(kd_kab_initial, kd_kec_initial);
        loadKelurahanList(kd_kec_initial, kd_kel_initial);

        $('#kd_kab').on('change', function(){
            var v = $(this).val();
            loadKecamatanList(v, null);
            setKelOptions([], null);
        });

        $('#kd_kec').on('change', function(){
            var v = $(this).val();
            loadKelurahanList(v, null);
        });

        // Refresh data dari server
        const no_rkm_medis = '{{ $pasien->no_rkm_medis }}';
        
        // AJAX request untuk mendapatkan data terbaru pasien
        $.ajax({
            url: '/pasien/' + no_rkm_medis,
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function(data) {
                // Isi formulir dengan data terbaru
                $('#no_ktp').val(data.no_ktp);
                $('#no_kk').val(data.no_kk);
                $('#nm_pasien').val(data.nm_pasien);
                $('#tgl_lahir').val(data.tgl_lahir);
                $('#status').val(data.status);
                $('#stts_nikah').val(data.stts_nikah);
                $('#pekerjaan').val(data.pekerjaan);
                $('#no_peserta').val(data.no_peserta);
                $('#no_tlp').val(data.no_tlp);
                $('#alamat').val(data.alamat);
                $('#data_posyandu').val(data.data_posyandu);
                $('#kd_pj').val(data.kd_pj);
                var kp = data.kd_prop || kd_prop_initial;
                var kb = data.kd_kab || kd_kab_initial;
                var kk = data.kd_kec || kd_kec_initial;
                var kl = data.kd_kel || kd_kel_initial;
                loadKabupatenList(kp, kb);
                loadKecamatanList(kb, kk);
                loadKelurahanList(kk, kl);
                
                
            },
            error: function(xhr) {
                
            }
        });
        
        // Animasi untuk form
        $('.form-group').each(function(index) {
            $(this).css('opacity', 0);
            $(this).animate({
                opacity: 1
            }, 300 * (index + 1));
        });
        
        // Validasi form
        $('form').on('submit', function(e) {
            let valid = true;
            
            // Validasi nama
            if ($('#nm_pasien').val().trim() === '') {
                $('#nm_pasien').addClass('is-invalid');
                valid = false;
            } else {
                $('#nm_pasien').removeClass('is-invalid');
            }
            
            // Validasi KTP
            if ($('#no_ktp').val().trim() === '') {
                $('#no_ktp').addClass('is-invalid');
                valid = false;
            } else {
                $('#no_ktp').removeClass('is-invalid');
            }
            
            // Validasi tanggal lahir
            if ($('#tgl_lahir').val().trim() === '') {
                $('#tgl_lahir').addClass('is-invalid');
                valid = false;
            } else {
                $('#tgl_lahir').removeClass('is-invalid');
            }
            
            if (!valid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }

            
        });
    });
</script>
@stop
