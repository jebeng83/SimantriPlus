<div>
    <div class="form-pendaftaran-container fade-in">
        <form wire:submit.prevent="save">
            <!-- Identitas Pasien -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-user-circle"></i> Data Identitas Pasien
                </div>
                <div class="form-section-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required-field">
                                <label><i class="fas fa-id-card"></i> No.Rekam Medis</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.defer="no_rkm_medis"
                                        placeholder="Masukkan No.Rekam Medis">
                                    <input type="date" class="form-control" wire:model="tgl_daftar" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group required-field">
                                <label><i class="fas fa-user"></i> Nama Pasien</label>
                                <input type="text" class="form-control" wire:model="nm_pasien"
                                    placeholder="Masukkan nama lengkap pasien">
                                @error('nm_pasien') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt"></i> Tempat/Tanggal Lahir</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="tmp_lahir"
                                        placeholder="Tempat Lahir">
                                    <input type="date" class="form-control" wire:model="tgl_lahir">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-birthday-cake"></i> Umur</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="umur_tahun" readonly>
                                    <span class="input-group-text">Tahun</span>
                                    <input type="text" class="form-control" wire:model="umur_bulan" readonly>
                                    <span class="input-group-text">Bulan</span>
                                    <input type="text" class="form-control" wire:model="umur_hari" readonly>
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-pray"></i> Agama</label>
                                <select class="form-control" wire:model="agama">
                                    <option value="ISLAM">ISLAM</option>
                                    <option value="KRISTEN">KRISTEN</option>
                                    <option value="KATOLIK">KATOLIK</option>
                                    <option value="HINDU">HINDU</option>
                                    <option value="BUDHA">BUDHA</option>
                                    <option value="KONGHUCU">KONGHUCU</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-ring"></i> Status Nikah</label>
                                <select class="form-control" wire:model="stts_nikah">
                                    <option value="MENIKAH">MENIKAH</option>
                                    <option value="BELUM MENIKAH">BELUM MENIKAH</option>
                                    <option value="JANDA">JANDA</option>
                                    <option value="DUDHA">DUDHA</option>
                                    <option value="JOMBLO">JOMBLO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-heartbeat"></i> Asuransi</label>
                                @if(!empty($penjab_list))
                                <select class="form-control" wire:model="kd_pj">
                                    @foreach($penjab_list as $penjab)
                                    <option value="{{ $penjab['kd_pj'] }}">{{ $penjab['png_jawab'] }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-building"></i> Instansi</label>
                                <select class="form-control" wire:model="perusahaan_pasien">
                                    @foreach($perusahaan_list as $perusahaan)
                                    <option value="{{ $perusahaan['kode_perusahaan'] }}">{{
                                        $perusahaan['nama_perusahaan']
                                        }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-info-circle"></i> Informasi Tambahan
                </div>
                <div class="form-section-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-venus-mars"></i> Jenis Kelamin</label>
                                <select class="form-control" wire:model="jk">
                                    <option value="L">LAKI-LAKI</option>
                                    <option value="P">PEREMPUAN</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-users"></i> Status</label>
                                <select class="form-control" wire:model="status">
                                    <option value="Kepala Keluarga">Kepala Keluarga</option>
                                    <option value="Suami">Suami</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Menantu">Menantu</option>
                                    <option value="Orang Tua">Orang Tua</option>
                                    <option value="Mertua">Mertua</option>
                                    <option value="Pembantu">Pembantu</option>
                                    <option value="Famili Lain">Famili Lain</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" wire:model="email"
                                    placeholder="email@contoh.com">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-graduation-cap"></i> Pendidikan</label>
                                <select class="form-control" wire:model="pnd">
                                    <option value="-">-</option>
                                    <option value="TS">Tidak Sekolah</option>
                                    <option value="TK">TK</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SLTA/SEDERAJAT">SLTA/SEDERAJAT</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><i class="fas fa-tint"></i> Golongan Darah</label>
                                <select class="form-control" wire:model="gol_darah">
                                    <option value="-">-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> No.Telepon</label>
                                <input type="text" class="form-control" wire:model="no_tlp" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-briefcase"></i> Pekerjaan</label>
                                <select class="form-control" wire:model="pekerjaan">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <option value="Belum/Tidak Bekerja">Belum/Tidak Bekerja</option>
                                    <option value="Pelajar">Pelajar</option>
                                    <option value="Mahasiswa">Mahasiswa</option>
                                    <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                    <option value="TNI">TNI</option>
                                    <option value="POLRI">POLRI</option>
                                    <option value="ASN (Kantor Pemerintah)">ASN (Kantor Pemerintah)</option>
                                    <option value="Pegawai Swasta">Pegawai Swasta</option>
                                    <option value="Wiraswasta/Pekerja Mandiri">Wiraswasta/Pekerja Mandiri</option>
                                    <option value="Pensiunan">Pensiunan</option>
                                    <option value="Pejabat Negara / Pejabat Daerah">Pejabat Negara / Pejabat Daerah</option>
                                    <option value="Pengusaha">Pengusaha</option>
                                    <option value="Dokter">Dokter</option>
                                    <option value="Bidan">Bidan</option>
                                    <option value="Perawat">Perawat</option>
                                    <option value="Apoteker">Apoteker</option>
                                    <option value="Psikolog">Psikolog</option>
                                    <option value="Tenaga Kesehatan Lainnya">Tenaga Kesehatan Lainnya</option>
                                    <option value="Dosen">Dosen</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Peneliti">Peneliti</option>
                                    <option value="Pengacara">Pengacara</option>
                                    <option value="Notaris">Notaris</option>
                                    <option value="Hakim/Jaksa/Tenaga Peradilan Lainnya">Hakim/Jaksa/Tenaga Peradilan Lainnya</option>
                                    <option value="Akuntan">Akuntan</option>
                                    <option value="Insinyur">Insinyur</option>
                                    <option value="Arsitek">Arsitek</option>
                                    <option value="Konsultan">Konsultan</option>
                                    <option value="Wartawan">Wartawan</option>
                                    <option value="Pedagang">Pedagang</option>
                                    <option value="Petani / Pekebun">Petani / Pekebun</option>
                                    <option value="PETANI/PEKEBUN">PETANI/PEKEBUN</option>
                                    <option value="Nelayan / Perikanan">Nelayan / Perikanan</option>
                                    <option value="Peternak">Peternak</option>
                                    <option value="Tokoh Agama">Tokoh Agama</option>
                                    <option value="Juru Masak">Juru Masak</option>
                                    <option value="Pelaut">Pelaut</option>
                                    <option value="Sopir">Sopir</option>
                                    <option value="Pilot">Pilot</option>
                                    <option value="Masinis">Masinis</option>
                                    <option value="Atlet">Atlet</option>
                                    <option value="Pekerja Seni">Pekerja Seni</option>
                                    <option value="Penjahit / Perancang Busana">Penjahit / Perancang Busana</option>
                                    <option value="Karyawan kantor / Pegawai Administratif">Karyawan kantor / Pegawai Administratif</option>
                                    <option value="Teknisi / Mekanik">Teknisi / Mekanik</option>
                                    <option value="Pekerja Pabrik / Buruh">Pekerja Pabrik / Buruh</option>
                                    <option value="Pekerja Konstruksi">Pekerja Konstruksi</option>
                                    <option value="Pekerja Pertukangan">Pekerja Pertukangan</option>
                                    <option value="Pekerja Migran">Pekerja Migran</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-id-card-alt"></i> No.Peserta</label>
                                <input type="text" class="form-control" wire:model="no_peserta"
                                    placeholder="Nomor peserta asuransi">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fas fa-address-card"></i> No.KK</label>
                                <input type="text" class="form-control" wire:model="no_kk"
                                    placeholder="Nomor kartu keluarga">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required-field">
                                <label><i class="fas fa-id-card"></i> No.KTP</label>
                                <input type="text" class="form-control" wire:model="no_ktp" placeholder="Nomor KTP">
                                @error('no_ktp') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-female"></i> Nama Ibu</label>
                                <input type="text" class="form-control" wire:model="nm_ibu"
                                    placeholder="Nama ibu kandung">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-male"></i> Nama Ayah/Penanggung Jawab</label>
                                <input type="text" class="form-control" wire:model="namakeluarga"
                                    placeholder="Nama ayah/penanggung jawab">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user-shield"></i> Status Penanggung Jawab</label>
                                <select class="form-control" wire:model="keluarga">
                                    <option value="AYAH">AYAH</option>
                                    <option value="IBU">IBU</option>
                                    <option value="SUAMI">SUAMI</option>
                                    <option value="ISTRI">ISTRI</option>
                                    <option value="ANAK">ANAK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-briefcase"></i> Pekerjaan</label>
                                <input type="text" class="form-control" wire:model="pekerjaan"
                                    placeholder="Pekerjaan pasien">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-globe-asia"></i> Suku/Bangsa</label>
                                <select class="form-control" wire:model="suku_bangsa">
                                    @foreach($suku_bangsa_list as $suku)
                                    <option value="{{ $suku['id'] }}">{{ $suku['nama_suku_bangsa'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-language"></i> Bahasa</label>
                                <select class="form-control" wire:model="bahasa_pasien">
                                    @foreach($bahasa_list as $bahasa)
                                    <option value="{{ $bahasa['id'] }}">{{ $bahasa['nama_bahasa'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-wheelchair"></i> Cacat Fisik</label>
                                <select class="form-control" wire:model="cacat_fisik">
                                    @foreach($cacat_fisik_list as $cacat)
                                    <option value="{{ $cacat['id'] }}">{{ $cacat['nama_cacat'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-map-marked-alt"></i> Alamat Pasien
                </div>
                <div class="form-section-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-home"></i> Alamat Lengkap</label>
                                <input type="text" class="form-control" wire:model="alamat"
                                    placeholder="Masukkan alamat lengkap">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-map"></i> Propinsi</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.debounce.300ms="search_propinsi"
                                        placeholder="Cari propinsi..." autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="hidden" wire:model="propinsi">
                                </div>

                                @if($showPropinsiDropdown && !empty($filtered_propinsi))
                                <div class="position-absolute w-100 mt-2">
                                    <div class="list-group shadow">
                                        @foreach($filtered_propinsi as $prop)
                                        <a href="#" class="list-group-item list-group-item-action"
                                            wire:click.prevent="selectPropinsi('{{ $prop['kd_prop'] }}', '{{ $prop['nm_prop'] }}')">
                                            {{ $prop['nm_prop'] }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-city"></i> Kabupaten</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.debounce.300ms="search_kabupaten"
                                        placeholder="Cari kabupaten..." autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="hidden" wire:model="kabupaten">
                                </div>

                                @if($showKabupatenDropdown && !empty($filtered_kabupaten))
                                <div class="position-absolute w-100 mt-2">
                                    <div class="list-group shadow">
                                        @foreach($filtered_kabupaten as $kab)
                                        <a href="#" class="list-group-item list-group-item-action"
                                            wire:click.prevent="selectKabupaten('{{ $kab['kd_kab'] }}', '{{ $kab['nm_kab'] }}')">
                                            {{ $kab['nm_kab'] }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-landmark"></i> Kecamatan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.debounce.300ms="search_kecamatan"
                                        placeholder="Cari kecamatan..." autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="hidden" wire:model="kecamatan">
                                </div>

                                @if($showKecamatanDropdown && !empty($filtered_kecamatan))
                                <div class="dropdown-container">
                                    <div class="position-absolute">
                                        <div class="list-group">
                                            @foreach($filtered_kecamatan as $kec)
                                            <a href="#" class="list-group-item list-group-item-action"
                                                wire:click.prevent="selectKecamatan('{{ $kec['kd_kec'] }}', '{{ $kec['nm_kec'] }}')">
                                                <span class="d-block font-weight-medium">{{ $kec['nm_kec'] }}</span>
                                                @if(isset($kec['nm_kab']))
                                                <small class="text-muted">{{ $kec['nm_kab'] }}</small>
                                                @endif
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-street-view"></i> Kelurahan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.debounce.300ms="search_kelurahan"
                                        placeholder="Cari kelurahan..." autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="hidden" wire:model="kelurahan">
                                </div>

                                @if($showKelurahanDropdown && !empty($filtered_kelurahan))
                                <div class="dropdown-container">
                                    <div class="position-absolute">
                                        <div class="list-group">
                                            @foreach($filtered_kelurahan as $kel)
                                            <a href="#" class="list-group-item list-group-item-action"
                                                wire:click.prevent="selectKelurahan('{{ $kel['kd_kel'] }}', '{{ $kel['nm_kel'] }}')">
                                                <span class="d-block font-weight-medium">{{ $kel['nm_kel'] }}</span>
                                                @if(isset($kel['nm_kec']))
                                                <small class="text-muted">{{ $kel['nm_kec'] }}</small>
                                                @endif
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Penanggung Jawab -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-address-book"></i> Alamat Penanggung Jawab
                </div>
                <div class="form-section-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-home"></i> Alamat P.J.</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="alamatpj" {{ $sama_dengan_pasien
                                        ? 'readonly' : '' }} placeholder="Alamat penanggung jawab">
                                    <div class="input-group-append">
                                        <div class="input-group-text custom-checkbox">
                                            <input type="checkbox" wire:model="sama_dengan_pasien"
                                                id="sama_dengan_pasien">
                                            <label for="sama_dengan_pasien" class="mb-0 ml-2">Sama dengan alamat
                                                pasien</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3" {{ $sama_dengan_pasien ? 'style=opacity:0.7' : '' }}>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-street-view"></i> Kelurahan</label>
                                <input type="text" class="form-control" wire:model="kelurahanpj" placeholder="Kelurahan"
                                    {{ $sama_dengan_pasien ? 'readonly' : '' }}>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-city"></i> Kabupaten</label>
                                <input type="text" class="form-control" wire:model="kabupatenpj" placeholder="Kabupaten"
                                    {{ $sama_dengan_pasien ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-landmark"></i> Kecamatan</label>
                                <input type="text" class="form-control" wire:model="kecamatanpj" placeholder="Kecamatan"
                                    {{ $sama_dengan_pasien ? 'readonly' : '' }}>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-map"></i> Propinsi</label>
                                <input type="text" class="form-control" wire:model="propinsipj" placeholder="Propinsi"
                                    {{ $sama_dengan_pasien ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Posyandu -->
            <div class="form-section">
                <div class="form-section-header">
                    <i class="fas fa-hospital-alt"></i> Data Posyandu
                </div>
                <div class="form-section-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><i class="fas fa-clinic-medical"></i> Posyandu Pasien</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.debounce.300ms="search_posyandu"
                                        wire:keydown.escape="resetPosyanduSearch"
                                        wire:keydown.tab="$set('showPosyanduDropdown', false)"
                                        placeholder="Cari posyandu..." autocomplete="off">
                                    <div class="input-group-append">
                                        @if($search_posyandu)
                                        <button class="btn btn-outline-secondary" type="button"
                                            wire:click="resetPosyanduSearch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @else
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        @endif
                                    </div>
                                    <input type="hidden" wire:model="data_posyandu">
                                </div>

                                @if($showPosyanduDropdown && count($filtered_posyandu) > 0)
                                <div class="dropdown-container">
                                    <div class="position-absolute" id="posyandu-dropdown">
                                        <div class="list-group">
                                            @foreach($filtered_posyandu as $posyandu)
                                            <a href="#" class="list-group-item list-group-item-action"
                                                wire:click.prevent="selectPosyandu('{{ $posyandu['kode_posyandu'] }}', '{{ $posyandu['nama_posyandu'] }}')">
                                                <span class="posyandu-name">{{ $posyandu['nama_posyandu'] }}</span>
                                                <span class="posyandu-detail">
                                                    @if(isset($posyandu['desa']))
                                                    {{ $posyandu['desa'] }}
                                                    @endif
                                                    @if(isset($posyandu['nm_kec']))
                                                    - {{ $posyandu['nm_kec'] }}
                                                    @endif
                                                </span>
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @elseif($posyandu_not_found && strlen($search_posyandu) > 2)
                                <div class="position-absolute w-100 mt-2">
                                    <div class="alert alert-warning mb-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Tidak ada posyandu ditemukan</span>
                                            <button type="button" class="close" wire:click="resetPosyanduSearch">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="form-section">
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary px-5" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...
                                </span>
                                <span wire:loading.remove wire:target="save">
                                    <i class="fas fa-save mr-2"></i> Simpan Data Pasien
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @push('css')
    <style>
        :root {
            --primary: #28a745;
            --primary-light: #34ce57;
            --primary-dark: #218838;
            --secondary: #6c757d;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #adb5bd;
            --danger: #dc3545;
            --warning: #ffc107;
            --success: #28a745;
            --info: #17a2b8;
            --shadow: 0 4px 6px rgba(40, 167, 69, 0.1);
            --transition: all 0.3s ease;
        }

        .form-pendaftaran-container {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .form-section {
            border-radius: 8px;
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: none;
            background-color: white;
        }

        .form-section-header {
            background: var(--primary) !important;
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-section-header i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .form-section-body {
            padding: 20px;
            background-color: white;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-group label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group label i {
            margin-right: 5px;
            color: var(--primary) !important;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            height: auto;
            font-size: 0.95rem;
            transition: var(--transition);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2) !important;
        }

        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23718096' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 1 !important;
            position: relative !important;
        }

        /* Perbaikan khusus untuk select pekerjaan */
        select[wire\:model="pekerjaan"] {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            height: auto !important;
            min-height: 38px !important;
            width: 100% !important;
            background-color: #fff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 10px 40px 10px 15px !important;
            font-size: 0.95rem !important;
            line-height: 1.5 !important;
            color: #495057 !important;
        }

        select[wire\:model="pekerjaan"]:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2) !important;
            outline: none !important;
        }

        /* Fallback untuk browser yang tidak mendukung :has() */
        .col-md-3:nth-child(2) .form-group select {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Debug CSS - hapus setelah masalah teratasi */
        select[wire\:model="pekerjaan"] {
            border: 2px solid red !important; /* Untuk debugging - hapus nanti */
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 0 6px 6px 0;
            color: var(--primary) !important;
        }

        .input-group-append .btn {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            padding: 10px 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary) !important;
            border: none !important;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(40, 167, 69, 0.3) !important;
        }

        .btn-outline-secondary {
            border-color: #e2e8f0;
            color: var(--dark);
        }

        .btn-outline-secondary:hover {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .list-group {
            border-radius: 6px;
            overflow: hidden;
        }

        .list-group-item {
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            transition: var(--transition);
        }

        .list-group-item:hover {
            background-color: rgba(40, 167, 69, 0.05) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .list-group-item small {
            display: block;
            font-size: 0.875em;
            color: var(--gray);
            margin-top: 3px;
        }

        .alert {
            margin-bottom: 1rem;
            border: none;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            border-left: 4px solid var(--primary) !important;
            color: var(--primary-dark) !important;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-left: 4px solid var(--danger);
        }

        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }

        .alert .fas {
            margin-right: 0.5rem;
        }

        /* Animasi dan efek smooth */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        /* Perbaikan untuk dropdown */
        .position-absolute {
            z-index: 1050 !important;
            position: absolute !important;
            left: 0 !important;
            right: 0 !important;
            max-height: 300px !important;
            overflow-y: auto !important;
            background: white !important;
            border-radius: 6px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15) !important;
        }

        .list-group {
            max-height: none !important;
            overflow: visible !important;
            border: 1px solid #e2e8f0 !important;
        }

        .list-group-item {
            padding: 12px 15px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            background: white !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .list-group-item:hover {
            background-color: rgba(40, 167, 69, 0.05) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        /* Custom scrollbar untuk dropdown */
        .position-absolute::-webkit-scrollbar {
            width: 8px !important;
        }

        .position-absolute::-webkit-scrollbar-track {
            background: #f1f1f1 !important;
            border-radius: 4px !important;
        }

        .position-absolute::-webkit-scrollbar-thumb {
            background: var(--primary) !important;
        }

        .position-absolute::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark) !important;
        }

        /* Perbaikan untuk form-group yang memiliki dropdown */
        .form-group {
            position: relative !important;
            overflow: visible !important;
        }

        /* Perbaikan khusus untuk form-group yang berisi select pekerjaan */
        .form-group:has(select[wire\:model="pekerjaan"]) {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            overflow: visible !important;
            z-index: auto !important;
        }

        /* Style khusus untuk dropdown posyandu */
        #posyandu-dropdown {
            max-height: 350px !important;
        }

        #posyandu-dropdown .list-group-item {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
        }

        #posyandu-dropdown .posyandu-name {
            font-weight: 500 !important;
            color: var(--dark) !important;
            font-size: 0.95rem !important;
        }

        #posyandu-dropdown .posyandu-detail {
            color: var(--gray) !important;
            font-size: 0.85rem !important;
        }

        /* Memastikan dropdown muncul di atas elemen lain */
        .form-group .position-absolute {
            top: 100% !important;
            margin-top: 2px !important;
        }

        /* Perbaikan untuk container dropdown */
        .dropdown-container {
            position: relative !important;
            width: 100% !important;
        }

        /* Memastikan dropdown tidak terpotong */
        .form-section {
            overflow: visible !important;
        }

        .form-section-body {
            overflow: visible !important;
        }

        /* Highlight untuk field wajib */
        .required-field label::after {
            content: " *";
            color: var(--danger);
        }

        /* Styling untuk checkbox */
        .custom-checkbox {
            display: flex;
            align-items: center;
        }

        .custom-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-section-body {
                padding: 15px;
            }
        }

        /* Header form style */
        .form-pendaftaran-container {
            position: relative;
        }

        .form-pendaftaran-container>.modal-header {
            background: var(--primary) !important;
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            margin-bottom: 1rem;
        }

        .form-pendaftaran-container .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            color: white;
        }

        .form-pendaftaran-container .modal-title i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .form-pendaftaran-container .close {
            color: white;
            opacity: 0.8;
            transition: var(--transition);
            padding: 1rem;
            margin: -1rem;
            font-size: 1.5rem;
        }

        .form-pendaftaran-container .close:hover {
            opacity: 1;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan efek ripple pada tombol
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const x = e.clientX - e.target.getBoundingClientRect().left;
                    const y = e.clientY - e.target.getBoundingClientRect().top;
                    
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Tambahkan ikon pada input date
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                const parent = input.parentElement;
                if (parent.classList.contains('input-group')) {
                    if (!parent.querySelector('.input-group-append')) {
                        const appendDiv = document.createElement('div');
                        appendDiv.classList.add('input-group-append');
                        appendDiv.innerHTML = `<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>`;
                        parent.appendChild(appendDiv);
                    }
                }
            });

            // Animasi fade-in untuk elemen form
            const formSections = document.querySelectorAll('.form-section');
            formSections.forEach((section, index) => {
                section.style.opacity = '0';
                setTimeout(() => {
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 * index);
            });

            // Notifikasi akan hilang setelah 3 detik
            Livewire.on('showNotification', () => {
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            });
        });
    </script>

    <style>
        /* Efek ripple untuk tombol */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
    @endpush
</div><!-- Tambah Pasien Baru -->
@push('css')
<style>
    .modal-header {
        background: var(--primary) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 1.5rem;
    }

    .modal-header .close {
        color: white !important;
        opacity: 0.8;
        transition: var(--transition);
        padding: 1rem;
        margin: -1rem;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .modal-title i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
</style>
@endpush