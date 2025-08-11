<div>
    <form wire:submit.prevent='simpanPemeriksaan'>
        <!-- Tampilkan informasi tanggal dan waktu saat ini -->
        <div class="d-flex justify-content-between mb-3">
            <div>
                <span class="badge badge-info p-2">
                    <i class="fas fa-calendar-day"></i> Tanggal: {{ date('d-m-Y') }}
                </span>
                <span class="badge badge-primary p-2 ml-2">
                    <i class="fas fa-clock"></i> Jam: <span id="current-time">{{ date('H:i:s') }}</span>
                </span>
            </div>
            <div>
                <button type="button" wire:click="kunjunganPcare" class="btn btn-success"><i class="fas fa-hospital"></i> Kunjungan PCare</button>
            </div>
        </div>

        <!-- Tambahkan template selector dengan menambahkan id yang jelas -->
        <div class="mb-3" wire:ignore>
            <label for="template-selector">Template Pemeriksaan:</label>
            <select id="template-selector" class="form-control">
                <option value="">-- Pilih Template --</option>
                <option value="normal">Pemeriksaan Normal</option>
                <option value="demam">Demam</option>
                <option value="sakit-kepala">Sakit Kepala</option>
                <option value="sesak">Sesak Napas</option>
                <option value="nyeri-perut">Nyeri Perut</option>
                <option value="diare">Diare</option>
                <option value="hipertensi">Hipertensi</option>
                <option value="diabetes">Diabetes Mellitus</option>
                <option value="batuk">Batuk</option>
                <option value="gatal">Gatal-gatal/Alergi</option>
                <option value="jantung">Penyakit Jantung</option>
                <option value="ispa">ISPA</option>
                <option value="tb">TB Paru</option>
                <option value="myalgia">Myalgia/Nyeri Otot</option>
                <option value="artritis">Artritis/Nyeri Sendi</option>
                <option value="tifoid">Demam Tifoid</option>
                <option value="isk">Infeksi Saluran Kemih</option>
                <option value="konjungtivitis">Konjungtivitis</option>
                <option value="vertigo">Vertigo</option>
                <option value="anemia">Anemia</option>
                <option value="asam-urat">Asam Urat</option>
                <option value="dispepsia">Dispepsia</option>
                <option value="dermatitis">Dermatitis</option>
                <option value="rhinitis">Rhinitis Alergi</option>
                <option value="bronkitis">Bronkitis</option>
                <option value="gastritis">Gastritis</option>
                <option value="otitis">Otitis Media</option>
                <option value="sinusitis">Sinusitis</option>
                <option value="dislipidemia">Dislipidemia</option>
                <option value="malaria">Malaria</option>
                <option value="dbd">Demam Berdarah</option>
                <option value="pneumonia">Pneumonia</option>
                <option value="gerd">GERD</option>
                <option value="migrain">Migrain</option>
                <option value="hemoroid">Hemoroid</option>
                <option value="konstipasi">Konstipasi</option>
                <option value="faringitis">Faringitis</option>
                <option value="tonsilitis">Tonsilitis</option>
                <option value="disfungsi-ereksi">Disfungsi Ereksi</option>
                <option value="gagal-jantung">Gagal Jantung</option>
                <option value="maag">Maag</option>
                <option value="keputihan">Keputihan/Vaginitis</option>
                <option value="stomatitis">Stomatitis</option>
                <option value="insomnia">Insomnia</option>
                <option value="cva">CVA (Stroke)</option>
                <option value="cacingan">Cacingan</option>
                <option value="kusta">Kusta/Lepra</option>
                <option value="tinea">Tinea/Jamur Kulit</option>
                <option value="scabies">Scabies</option>
                <option value="hipertiroid">Hipertiroid</option>
                <option value="hipotiroid">Hipotiroid</option>
                <option value="epilepsi">Epilepsi</option>
                <option value="kejang-demam">Kejang Demam</option>
                <option value="kencing-manis">Diabetes</option>
            </select>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <label for="keluhan">Subjek</label>
                <textarea wire:model.defer='keluhan' class="form-control" id="keluhan" rows="2"
                    placeholder="Pasien datang dengan keluhan..."></textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="pemeriksaan">Objek</label>
                <textarea wire:model.defer='pemeriksaan' class="form-control" id="pemeriksaan" rows="2"
                    placeholder="KU : Composmentis, Baik&#10;Thorax : Cor S1-2 intensitas normal, reguler, bising (-)&#10;Pulmo : SDV +/+ ST -/-&#10;Abdomen : Supel, NT(-), peristaltik (+) normal.&#10;EXT : Oedem -/-"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Asesmen</label>
                <textarea wire:model.defer='penilaian' class="form-control" name="" id="" rows="2"
                    placeholder="Diagnosis..."></textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="">Instruksi</label>
                <textarea wire:model.defer='instruksi' class="form-control" name="" id="" rows="2"
                    placeholder="Istirahat Cukup, PHBS..."></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="">Plan</label>
                <textarea wire:model.defer='rtl' class="form-control" name="" id="" rows="1"
                    placeholder="Edukasi Kesehatan..."></textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="">Alergi</label>
                <textarea wire:model.defer='alergi' class="form-control" name="" id="" rows="1"
                    placeholder="Tidak Ada..."></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="">Tensi</label>
                <input type="text" wire:model.defer='tensi' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="120/80">
            </div>
            <div class="form-group col-md-3">
                <label for="">Berat</label>
                <input type="text" wire:model.defer='berat' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="60">
            </div>
            <div class="form-group col-md-3">
                <label for="">Tinggi</label>
                <input type="text" wire:model.defer='tinggi' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="165">
            </div>
            <div class="form-group col-md-3">
                <label for="">Lingkar Perut</label>
                <input type="text" wire:model.defer='lingkar' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="72">
            </div>
        </div>
        <div class="row">

            <div class="form-group col-md-4">
                <label for="">Suhu</label>
                <input type="text" wire:model.defer='suhu' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="36.5">
            </div>
            <div class="form-group col-md-4">
                <label for="">Nadi (per Menit)</label>
                <input type="text" wire:model.defer='nadi' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="80">
            </div>
            <div class="form-group col-md-4">
                <label for="">Respirasi</label>
                <input type="text" wire:model.defer='respirasi' class="form-control" name="" id=""
                    aria-describedby="helpId" placeholder="20">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="">SPO2</label>
                <input type="text" wire:model.defer='spo2' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="96">
            </div>
            <div class="form-group col-md-4">
                <label for="">GCS (E, V, M)</label>
                <input type="text" wire:model.defer='gcs' class="form-control" name="" id="" aria-describedby="helpId"
                    placeholder="15">
            </div>
            <div class="form-group col-md-4">
                <label for="">Kesadaran</label>
                <select class="form-control" wire:model.defer='kesadaran' name="" id="">
                    <option value="Compos Mentis">Compos Mentis</option>
                    <option value="Apatis">Apatis</option>
                    <option value="Delirium">Delirium</option>
                    <option value="Somnolence">Somnolence</option>
                    <option value="Sopor">Sopor</option>
                    <option value="Coma">Coma</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="">Evaluasi</label>
            <textarea wire:model.defer='evaluasi' class="form-control" name="" id="" rows="1"
                placeholder="Kontrol Ulang Jika belum Ada Perubahan..."></textarea>
        </div>
        <div class="d-flex flex-row-reverse">
            <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-save"></i> Simpan</button>
            <button type="button" id="resetFormBtn" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</button>
        </div>
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Untuk pasien BPJS, sistem akan otomatis mendaftarkan ke PCare setelah pemeriksaan disimpan.
            </small>
        </div>
    </form>
    <h5 class="pt-4">Riwayat Pemeriksaan</h5>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="thead-inverse" style="width: 100%">
                <tr>
                    <th>PPA</th>
                    <th>Keluhan</th>
                    <th>Pemeriksaan</th>
                    <th>Tensi</th>
                    <th>Nadi</th>
                    <th>Suhu</th>
                    <th>RR</th>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($listPemeriksaan as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->keluhan }}</td>
                    <td>{{ $item->pemeriksaan }}</td>
                    <td>{{ $item->tensi }}</td>
                    <td>{{ $item->nadi }}</td>
                    <td>{{ $item->suhu_tubuh }}</td>
                    <td>{{ $item->respirasi }}</td>
                    <td>
                        <div class="btn-group">
                            <button
                                wire:click='$emit("openModalEditPemeriksaan", "{{$item->no_rawat}}", "{{$item->tgl_perawatan}}","{{$item->jam_rawat}}")'
                                class="btn btn-sm btn-warning">Edit</button>
                            <button
                                wire:click='confirmHapus("{{$item->no_rawat}}", "{{$item->tgl_perawatan}}","{{$item->jam_rawat}}")'
                                class="btn btn-sm btn-danger">Hapus</button>
                            
                            <!-- Dropdown Kehadiran -->
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-check"></i> Kehadiran
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item attendance-option" href="#" 
                                       data-no-rawat="{{$item->no_rawat}}" 
                                       data-tgl="{{$item->tgl_perawatan}}" 
                                       data-jam="{{$item->jam_rawat}}" 
                                       data-status="hadir"
                                       onclick="handleAttendanceClick(this, event)">
                                        <i class="fas fa-check-circle text-success"></i> Hadir
                                    </a>
                                    <a class="dropdown-item attendance-option" href="#" 
                                       data-no-rawat="{{$item->no_rawat}}" 
                                       data-tgl="{{$item->tgl_perawatan}}" 
                                       data-jam="{{$item->jam_rawat}}" 
                                       data-status="tidak-hadir"
                                       onclick="handleAttendanceClick(this, event)">
                                        <i class="fas fa-times-circle text-danger"></i> Tidak Hadir
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Data Pemeriksaan Kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@section('js')
<script>
    // Fungsi untuk mengupdate jam saat ini
    function updateClock() {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var seconds = now.getSeconds().toString().padStart(2, '0');
        $('#current-time').text(hours + ':' + minutes + ':' + seconds);
        setTimeout(updateClock, 1000);
    }
    
    // Fungsi untuk menerapkan template normal
    function applyNormalTemplate() {
        let keluhan = "Pasien melakukan kontrol rutin.";
        let pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
        let penilaian = "Kondisi pasien stabil";
        let instruksi = "Istirahat Cukup, PHBS";
        let rtl = "Edukasi Kesehatan";
        let alergi = "Tidak Ada";
        
        // Set nilai input dan trigger event untuk Livewire
        setLivewireValue('keluhan', keluhan);
        setLivewireValue('pemeriksaan', pemeriksaan);
        setLivewireValue('penilaian', penilaian);
        setLivewireValue('instruksi', instruksi);
        setLivewireValue('rtl', rtl);
        setLivewireValue('alergi', alergi);
        
        // Set nilai default untuk vital sign
        setLivewireValue('suhu', '36.5');
        setLivewireValue('tensi', '120/80');
        setLivewireValue('nadi', '80');
        setLivewireValue('respirasi', '20');
        setLivewireValue('spo2', '96');
        setLivewireValue('gcs', '15');
        setLivewireValue('lingkar', '72');
        setLivewireValue('evaluasi', 'Kontrol Ulang Jika belum Ada Perubahan');
    }
    
    // Fungsi untuk mengatur nilai Livewire dengan benar
    function setLivewireValue(property, value) {
        // Cari elemen dengan wire:model.defer
        const element = document.querySelector(`[wire\\:model\\.defer="${property}"]`);
        if (element) {
            // Set nilai
            element.value = value;
            
            // Trigger input event untuk Livewire
            element.dispatchEvent(new Event('input', { bubbles: true }));
            
            // Untuk memastikan Livewire menangkap perubahan, juga update properti langsung
            if (window.Livewire) {
                // Cari komponen Livewire yang berisi form
                const livewireComponent = window.Livewire.find(
                    element.closest('[wire\\:id]')?.getAttribute('wire:id')
                );
                
                if (livewireComponent) {
                    // Update nilai properti langsung di komponen Livewire
                    livewireComponent.set(property, value);
                }
            }
        }
    }
    
    // Debug function untuk dropdown kehadiran
    function handleAttendanceClick(element, event) {
        event.preventDefault();
        
        // Ambil data dari element
        const noRawat = element.getAttribute('data-no-rawat');
        const tgl = element.getAttribute('data-tgl');
        const jam = element.getAttribute('data-jam');
        const status = element.getAttribute('data-status');
        
        // Debug information
        console.group('🔍 DEBUG: Attendance Dropdown Click');
        console.log('📋 Event Details:', {
            timestamp: new Date().toISOString(),
            element: element,
            event: event
        });
        
        console.log('📊 Data Attributes:', {
            noRawat: noRawat,
            tanggal: tgl,
            jam: jam,
            status: status
        });
        
        console.log('🎯 Element Info:', {
            tagName: element.tagName,
            className: element.className,
            innerHTML: element.innerHTML,
            parentElement: element.parentElement
        });
        
        console.log('🖱️ Event Info:', {
            type: event.type,
            target: event.target,
            currentTarget: event.currentTarget,
            bubbles: event.bubbles,
            cancelable: event.cancelable
        });
        
        // Tampilkan alert dengan informasi debug
        Swal.fire({
            title: '🔍 Debug: Kehadiran Clicked',
            html: `
                <div class="text-left">
                    <h6><i class="fas fa-info-circle"></i> Data Pemeriksaan:</h6>
                    <ul class="list-unstyled ml-3">
                        <li><strong>No Rawat:</strong> ${noRawat}</li>
                        <li><strong>Tanggal:</strong> ${tgl}</li>
                        <li><strong>Jam:</strong> ${jam}</li>
                        <li><strong>Status:</strong> <span class="badge ${status === 'hadir' ? 'badge-success' : 'badge-danger'}">${status}</span></li>
                    </ul>
                    
                    <h6><i class="fas fa-code"></i> Debug Info:</h6>
                    <ul class="list-unstyled ml-3">
                        <li><strong>Element:</strong> ${element.tagName}</li>
                        <li><strong>Classes:</strong> ${element.className}</li>
                        <li><strong>Timestamp:</strong> ${new Date().toLocaleString()}</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb"></i> 
                        <strong>Debug Mode:</strong> Lihat console browser untuk detail lengkap
                    </div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'OK',
            width: '600px',
            customClass: {
                popup: 'debug-alert',
                title: 'debug-title'
            }
        });
        
        // Log ke server (opsional)
        if (window.Livewire) {
            console.log('📡 Sending to Livewire...');
            // Uncomment jika ingin mengirim ke backend
            // @this.call('debugAttendance', noRawat, tgl, jam, status);
        }
        
        console.groupEnd();
        
        // Tutup dropdown setelah klik
        $(element).closest('.dropdown-menu').prev('.dropdown-toggle').dropdown('toggle');
    }
    
    $(document).ready(function() {
        // Mulai update jam
        updateClock();
        
        // Debug: Log ketika dropdown kehadiran dibuka
        $(document).on('show.bs.dropdown', '.btn-group .dropdown-toggle', function(e) {
            if ($(this).text().includes('Kehadiran')) {
                console.log('🔽 Dropdown Kehadiran dibuka:', {
                    timestamp: new Date().toISOString(),
                    element: this,
                    event: e
                });
            }
        });
        
        // Debug: Log ketika dropdown kehadiran ditutup
        $(document).on('hide.bs.dropdown', '.btn-group .dropdown-toggle', function(e) {
            if ($(this).text().includes('Kehadiran')) {
                console.log('🔼 Dropdown Kehadiran ditutup:', {
                    timestamp: new Date().toISOString(),
                    element: this,
                    event: e
                });
            }
        });
        
        // Periksa apakah form kosong dan terapkan template normal jika kosong
        let keluhanValue = $('textarea[wire\\:model\\.defer="keluhan"]').val().trim();
        let pemeriksaanValue = $('textarea[wire\\:model\\.defer="pemeriksaan"]').val().trim();
        
        if (!keluhanValue && !pemeriksaanValue) {
            applyNormalTemplate();
        }
        
        // Script untuk template pemeriksaan
        $('#template-selector').change(function() {
            let template = $(this).val();
            
            if (!template) return;
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menerapkan Template',
                text: 'Harap tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                timer: 1000,
                showConfirmButton: false
            });
            
            // Nilai default template
            let keluhan = '';
            let pemeriksaan = '';
            let penilaian = '';
            let instruksi = '';
            let rtl = '';
            let alergi = '';
            
            // Template Normal
            if (template === 'normal') {
                keluhan = "Pasien melakukan kontrol rutin.";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Kondisi pasien stabil";
                instruksi = "Istirahat Cukup, PHBS";
                rtl = "Edukasi Kesehatan";
                alergi = "Tidak Ada";
                
                // Set nilai input dan trigger event untuk Livewire
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol Ulang Jika belum Ada Perubahan');
            }
            
            // Template Demam
            else if (template === 'demam') {
                keluhan = "Pasien mengeluh demam sejak 2 hari yang lalu. Demam naik turun, kadang menggigil. Nyeri kepala (+), mual (-), muntah (-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Demam";
                instruksi = "Istirahat cukup, kompres, minum banyak air putih";
                rtl = "Pemberian antipiretik\nObservasi suhu";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.5');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '90');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '95');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau jika demam tidak turun');
            }
            
            // Template Sakit Kepala
            else if (template === 'sakit-kepala') {
                keluhan = "Pasien mengeluh sakit kepala berdenyut sejak 1 hari yang lalu. Nyeri skala 6/10. Mual (+), muntah (-), demam (-).";
                pemeriksaan = "Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Cephalgia";
                instruksi = "Istirahat yang cukup dalam ruangan yang tenang, hindari cahaya terang";
                rtl = "Pemberian analgetik dan anti mual";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Batuk
            else if (template === 'batuk') {
                keluhan = "Pasien mengeluh batuk sejak 3 hari yang lalu. Batuk berdahak/kering, nyeri tenggorokan (+/-), hidung tersumbat (+/-), demam (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi -/-, wheezing -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "ISPA";
                instruksi = "Istirahat cukup, minum air hangat, hindari makanan/minuman dingin";
                rtl = "Pemberian antitusif/ekspektoran\nObservasi perkembangan batuk";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.2');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '82');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Sesak Napas
            else if (template === 'sesak') {
                keluhan = "Pasien mengeluh sesak napas sejak 1 hari yang lalu. Sesak dirasakan terutama saat beraktivitas/saat berbaring. Batuk (+/-), nyeri dada (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sesak, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi +/+, wheezing +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Asma/PPOK";
                instruksi = "Istirahat cukup, hindari alergen/pemicu sesak, posisi semi-fowler";
                rtl = "Pemberian bronkodilator\nObservasi sesak napas";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.7');
                setLivewireValue('tensi', '130/90');
                setLivewireValue('nadi', '96');
                setLivewireValue('respirasi', '26');
                setLivewireValue('spo2', '94');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika sesak memberat');
            }
            
            // Template Nyeri Perut
            else if (template === 'nyeri-perut') {
                keluhan = "Pasien mengeluh nyeri perut sejak 1 hari yang lalu. Nyeri terutama di bagian (atas/bawah/kanan/kiri). Mual (+), muntah (+/-), diare (+/-), demam (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Kesakitan, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(+), peristaltik (+) normal/meningkat/menurun.\nEXT : Oedem -/-";
                penilaian = "Gastritis/Dispepsia";
                instruksi = "Diet lunak, hindari makanan pedas/asam, makan sedikit tapi sering";
                rtl = "Pemberian antasida/PPI\nObservasi nyeri perut";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.0');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika nyeri tidak membaik');
            }
            
            // Template Diare
            else if (template === 'diare') {
                keluhan = "Pasien mengeluh diare sejak 2 hari yang lalu. BAB cair 5-7x sehari. Darah (-), lendir (-), mual (+), muntah (-), demam (-), nyeri perut (+).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(+), peristaltik meningkat.\nEXT : Turgor kulit normal, Oedem -/-";
                penilaian = "Gastroenteritis Akut";
                instruksi = "Minum banyak air putih/oralit, hindari makanan pedas/asam/berlemak, cuci tangan sebelum makan";
                rtl = "Pemberian antidiare, antiemetik\nObservasi frekuensi BAB";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.2');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika diare memburuk atau tanda dehidrasi');
            }
            
            // Template Hipertensi
            else if (template === 'hipertensi') {
                keluhan = "Pasien mengeluh sakit kepala, pusing, dan tengkuk terasa berat. Riwayat hipertensi (+), nyeri dada (-), sesak napas (-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Hipertensi";
                instruksi = "Diet rendah garam, aktivitas fisik teratur, hindari stres, pantau tekanan darah secara berkala";
                rtl = "Pemberian antihipertensi\nMonitoring tekanan darah";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '160/100');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '86');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi untuk evaluasi tekanan darah');
            }
            
            // Template Diabetes
            else if (template === 'diabetes') {
                keluhan = "Pasien mengeluh sering haus, sering BAK, dan mudah lelah. Riwayat DM (+), penurunan berat badan (+), penglihatan kabur (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-, Ulkus diabetikum -/-";
                penilaian = "Diabetes Mellitus";
                instruksi = "Diet rendah gula, aktivitas fisik teratur, pantau kadar gula darah secara berkala, perawatan kaki diabetik";
                rtl = "Pemberian OHO/Insulin\nMonitoring gula darah";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '92');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi untuk evaluasi kadar gula darah');
            }
            
            // Template Gatal
            else if (template === 'gatal') {
                keluhan = "Pasien mengeluh gatal-gatal di seluruh/sebagian tubuh sejak 3 hari yang lalu. Kemerahan pada kulit (+), bengkak (-), sesak napas (-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-\nKulit : Eritema (+), papul (+), vesikel (-), skuama (+)";
                penilaian = "Dermatitis/Urtikaria";
                instruksi = "Hindari garuk berlebihan, hindari alergen yang dicurigai, gunakan pakaian dari katun, mandi dengan air hangat";
                rtl = "Pemberian antihistamin, kortikosteroid topikal\nObservasi gatal dan lesi kulit";
                alergi = "Dicurigai terhadap (debu/makanan/obat)";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau segera jika gatal memberat/sesak napas');
            }
            
            // Template Jantung
            else if (template === 'jantung') {
                keluhan = "Pasien mengeluh nyeri dada, sesak napas terutama saat aktivitas, dan mudah lelah. Riwayat penyakit jantung (+), palpitasi (+/-), bengkak pada kaki (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal/melemah, reguler/tidak, bising (+/-)\nPulmo : SDV +/+ ST -/-, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem +/+, Sianosis -/-";
                penilaian = "Penyakit Jantung Koroner/Gagal Jantung";
                instruksi = "Batasi aktivitas fisik berat, diet rendah garam dan lemak, istirahat cukup, hindari stres";
                rtl = "Pemberian terapi jantung sesuai protokol\nMonitoring gejala kardiovaskular";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/90');
                setLivewireValue('nadi', '96');
                setLivewireValue('respirasi', '24');
                setLivewireValue('spo2', '94');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '84');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau segera jika nyeri dada/sesak memberat');
            }
            
            // Template ISPA
            else if (template === 'ispa') {
                keluhan = "Pasien mengeluh batuk, pilek, hidung tersumbat, dan nyeri tenggorokan sejak 4 hari yang lalu. Demam (+/-), sakit kepala (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi -/-, wheezing -/-\nOrofaring : Faring hiperemis, tonsil T1-T1 hiperemis\nRhinore : (+)";
                penilaian = "ISPA (Infeksi Saluran Pernapasan Akut)";
                instruksi = "Istirahat cukup, minum air putih hangat yang banyak, hindari udara dingin, jaga kebersihan lingkungan";
                rtl = "Pemberian simptomatik, ekspektoran, antipiretik jika demam\nObservasi perburukan gejala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.3');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau segera jika gejala memberat');
            }
            
            // Template TB Paru
            else if (template === 'tb') {
                keluhan = "Pasien mengeluh batuk lebih dari 2 minggu, batuk berdahak kadang bercampur darah, demam yang hilang timbul terutama sore hari, keringat malam, berat badan menurun, dan mudah lelah.";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Sedang, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ menurun, Ronkhi basah kasar +/+, suara napas bronkial (+)\nLimfonodi : Pembesaran KGB leher/axilla (-/-)";
                penilaian = "Suspek TB Paru";
                instruksi = "Konsumsi OAT secara teratur, makan makanan bergizi, istirahat cukup, ventilasi rumah yang baik, gunakan masker, buang dahak pada tempatnya";
                rtl = "Pemeriksaan dahak SPS, foto thorax\nPengobatan OAT kategori sesuai indikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.8');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '24');
                setLivewireValue('spo2', '94');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '65');
                setLivewireValue('evaluasi', 'Kontrol 2 minggu sekali untuk pemantauan pengobatan');
            }
            
            // Template Myalgia/Nyeri Otot
            else if (template === 'myalgia') {
                keluhan = "Pasien mengeluh nyeri otot di beberapa bagian tubuh sejak 3 hari yang lalu. Riwayat aktivitas berat (+), demam (-), trauma (-), nyeri sendi (-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nEXT : Nyeri tekan otot (+), Range of Motion normal, krepitasi (-), kemerahan (-)";
                penilaian = "Myalgia";
                instruksi = "Istirahat, hindari aktivitas berat dulu, kompres hangat pada area yang nyeri, hindari mengangkat beban berat";
                rtl = "Pemberian analgetik, antiinflamasi\nObservasi perkembangan nyeri";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika nyeri tidak membaik');
            }
            
            // Template Artritis/Nyeri Sendi
            else if (template === 'artritis') {
                keluhan = "Pasien mengeluh nyeri sendi (lutut/pergelangan kaki/tangan/jari) sejak beberapa hari/minggu yang lalu. Pembengkakan (+/-), kemerahan (+/-), kaku di pagi hari (+/-), keterbatasan gerakan (+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nEXT : Nyeri tekan sendi (+), bengkak (+/-), kemerahan (+/-), panas (-/+), Range of Motion terbatas, krepitasi (+/-)";
                penilaian = "Artritis (Osteoartritis/Gout Arthritis/Artritis Reumatoid)";
                instruksi = "Istirahat, kompres dingin jika akut atau hangat jika kronis, hindari aktivitas yang memberatkan sendi, diet rendah purin (utk gout)";
                rtl = "Pemberian analgetik, antiinflamasi\nPemeriksaan asam urat/faktor reumatoid sesuai indikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '82');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '75');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika nyeri tidak membaik');
            }
            
            // Template Demam Tifoid
            else if (template === 'tifoid') {
                keluhan = "Pasien mengeluh demam naik turun selama >7 hari, nyeri kepala, nyeri perut, mual, nafsu makan menurun. Riwayat konsumsi makanan di luar (+/-), diare (-/+), konstipasi (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan epigastrium/right lower quadrant (+), defans muskular (-), peristaltik (+) menurun.\nLidah : Kotor\nKulit : Rash (-), rose spot (-)";
                penilaian = "Suspek Demam Tifoid";
                instruksi = "Istirahat total, diet tinggi kalori tinggi protein (bubur, telur, daging), minum yang cukup, kebersihan diri dan lingkungan";
                rtl = "Pemeriksaan Widal/Tubex/kultur darah sesuai indikasi\nPemberian antibiotik, simptomatik\nObservasi komplikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.7');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika ada komplikasi');
            }
            
            // Template Infeksi Saluran Kemih
            else if (template === 'isk') {
                keluhan = "Pasien mengeluh nyeri saat BAK, BAK lebih sering, BAK sedikit-sedikit tapi sering, nyeri pinggang, demam (+/-). Terkadang disertai warna urin keruh, bau menyengat, atau kemerahan.";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan-Sedang, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), sukusi ginjal (+), nyeri tekan suprapubik (+)";
                penilaian = "Infeksi Saluran Kemih";
                instruksi = "Minum air putih yang banyak (minimal 2-3 liter/hari), konsumsi obat teratur, hindari menahan BAK, jaga kebersihan area genital";
                rtl = "Pemeriksaan urinalisis\nPemberian antibiotik, analgetik, antipiretik jika demam";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '97');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Konjungtivitis
            else if (template === 'konjungtivitis') {
                keluhan = "Pasien mengeluh mata merah, berair, terasa berpasir/gatal, kadang terasa perih dan silau. Riwayat kontak dengan penderita mata merah (+/-), penggunaan lensa kontak (-/+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nMata : Hiperemis konjungtiva (+), sekret (+/-), edema palpebra (+/-), penglihatan normal, refleks pupil +/+ normal";
                penilaian = "Konjungtivitis";
                instruksi = "Jangan mengucek mata, kompres dengan air matang hangat/dingin, gunakan kacamata hitam jika silau, cuci tangan sebelum dan sesudah meneteskan obat, hindari penggunaan lensa kontak sementara";
                rtl = "Pemberian antibiotik tetes mata/salep, air mata buatan\nHindari penggunaan steroid tanpa indikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Vertigo
            else if (template === 'vertigo') {
                keluhan = "Pasien mengeluh pusing berputar, rasa tidak seimbang, mual (+/-), muntah (-/+). Keluhan memberat saat perubahan posisi, gejala umumnya berlangsung beberapa menit hingga jam.";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nKepala : Nyeri tekan (-), Romberg test (+/-), Dix-Hallpike test (+/-)\nTelinga : Membran timpani utuh, hiperemis (-)\nTanda neurologis fokal (-)";
                penilaian = "Vertigo Perifer (BPPV)";
                instruksi = "Hindari gerakan kepala yang mendadak, hindari mengemudi saat gejala muncul, batasi aktivitas yang membutuhkan keseimbangan, istirahat bila serangan muncul";
                rtl = "Pemberian antivertigo, antiemetik jika mual/muntah\nLatihan Epley jika BPPV";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika gejala memberat');
            }
            
            // Template Anemia
            else if (template === 'anemia') {
                keluhan = "Pasien mengeluh lemas, pucat, mudah lelah, pusing, kadang sesak napas saat aktivitas. Riwayat perdarahan (-/+), diet kurang bergizi (+/-), menstruasi banyak (-/+).";
                pemeriksaan = "Keadaan Umum : Tampak Pucat, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nKonjungtiva : Anemis (+)\nLidah : Atrofi papil (-/+)\nExt : Pucat, kuku pucat (+)";
                penilaian = "Anemia";
                instruksi = "Konsumsi makanan tinggi zat besi (daging merah, hati, bayam, telur), minum suplemen zat besi 1 jam sebelum/2 jam setelah makan, hindari minum teh/kopi bersamaan dengan suplemen";
                rtl = "Pemeriksaan darah lengkap\nPemberian suplemen zat besi, asam folat, vitamin C\nInvestigasi penyebab jika diperlukan";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '100/70');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '70');
                setLivewireValue('evaluasi', 'Kontrol 2 minggu lagi untuk evaluasi kadar Hb');
            }
            
            // Template Asam Urat
            else if (template === 'asam-urat') {
                keluhan = "Pasien mengeluh nyeri sendi (terutama jempol kaki, pergelangan kaki, lutut, siku) yang mendadak, kadang disertai kemerahan, bengkak, dan terasa hangat di daerah sendi yang terkena.";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nEXT : Nyeri tekan sendi (+), bengkak (+), kemerahan (+), panas (+), Range of Motion terbatas\nTofus : (-/+)";
                penilaian = "Gout Arthritis/Hiperurisemia";
                instruksi = "Diet rendah purin (hindari jeroan, seafood, kacang-kacangan, minuman beralkohol), batasi konsumsi daging merah, perbanyak minum air putih, hindari minuman berpemanis";
                rtl = "Pemeriksaan asam urat darah\nPemberian analgetik, antiinflamasi, obat penurun asam urat";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '88');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika nyeri tidak membaik');
            }
            
            // Template Dispepsia
            else if (template === 'dispepsia') {
                keluhan = "Pasien mengeluh nyeri/tidak nyaman di ulu hati, kembung, mual, sendawa, cepat kenyang. Keluhan memberat setelah makan. Riwayat konsumsi makanan pedas/asam/berlemak (+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan epigastrium (+), defans muskular (-), peristaltik (+) normal";
                penilaian = "Dispepsia";
                instruksi = "Makan porsi kecil tapi sering, hindari makanan pedas/asam/berlemak/gorengan, kurangi kopi & minuman berkarbonasi, hindari makan < 3 jam sebelum tidur";
                rtl = "Pemberian antasida, PPI/H2 blocker\nAnjuran modifikasi gaya hidup";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '80');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Dermatitis
            else if (template === 'dermatitis') {
                keluhan = "Pasien mengeluh ruam kemerahan, gatal, kulit kering dan bersisik di area (wajah/tangan/badan/kaki). Riwayat alergi (+/-), penggunaan sabun/kosmetik baru (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nKulit : Eritema (+), papul (+), vesikel (+/-), skuama (+), likenifikasi (+/-), ekskoriasi (+/-)\nTanda infeksi sekunder : (-/+)";
                penilaian = "Dermatitis";
                instruksi = "Hindari menggaruk, jaga kulit tetap lembab dengan pelembab, hindari pemicu alergi yang diketahui, mandi dengan air hangat dan sabun pH netral";
                rtl = "Pemberian steroid topikal, antihistamin oral\nEdukasi perawatan kulit";
                alergi = "Curigai terhadap (deterjen/sabun/kosmetik/makanan tertentu)";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '75');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika lesi kulit tidak membaik');
            }
            
            // Template Rhinitis Alergi
            else if (template === 'rhinitis') {
                keluhan = "Pasien mengeluh hidung tersumbat, bersin-bersin, hidung berair, dan gatal pada hidung/mata/tenggorokan. Keluhan muncul/memburuk saat terpapar debu/serbuk/bulu hewan/udara dingin.";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nHidung : Mukosa hiperemis, edema konka, sekret jernih, arkus faringeal hiperemis\nMata : Konjungtiva hiperemis (-/+)";
                penilaian = "Rhinitis Alergi";
                instruksi = "Hindari alergen yang diketahui, jaga kebersihan rumah, gunakan masker saat beraktivitas di luar/bersih-bersih, cuci hidung dengan larutan salin";
                rtl = "Pemberian antihistamin, dekongestan, nasal spray steroid";
                alergi = "Curigai terhadap (debu/serbuk bunga/bulu hewan/parfum)";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 14 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Bronkitis
            else if (template === 'bronkitis') {
                keluhan = "Pasien mengeluh batuk berdahak >2 minggu, dahak purulen/mukoid, sesak ringan, kadang disertai demam ringan. Riwayat merokok (+/-), paparan polusi (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi +/+, wheezing -/+\nHidung : Sekret (-/+)";
                penilaian = "Bronkitis Akut/Kronik";
                instruksi = "Berhenti merokok, hindari polusi udara, minum air putih yang cukup, istirahat yang cukup, konsumsi makanan bergizi";
                rtl = "Pemberian mukolitik/ekspektoran, bronkodilator jika ada bronkospasme\nObservasi batuk dan produksi dahak";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.2');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '78');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Gastritis
            else if (template === 'gastritis') {
                keluhan = "Pasien mengeluh nyeri perut ulu hati, mual, muntah, perut kembung, nafsu makan menurun. Riwayat konsumsi makanan pedas/asam (+), penggunaan NSAID (+/-), alkohol (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan epigastrium (+/-), peristaltik (+) normal";
                penilaian = "Gastritis";
                instruksi = "Diet lunak, hindari makanan pedas/asam/berlemak, makan porsi kecil tapi sering, hindari minuman berkafein/beralkohol/berkarbonasi, hindari NSAID";
                rtl = "Pemberian antasida, PPI/H2 blocker, antiemetik jika mual/muntah\nObservasi gejala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '76');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Otitis Media
            else if (template === 'otitis') {
                keluhan = "Pasien mengeluh nyeri telinga, pendengaran berkurang, kadang disertai demam, telinga terasa penuh, dan keluar cairan dari telinga. Riwayat ISPA sebelumnya (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nTelinga : Membran timpani hiperemis/perforasi, cairan di kanalis auditorius eksternus (+/-)\nNyeri tekan tragus (+)\nPemeriksaan pendengaran : Rinne (-/+), Weber lateralisasi ke telinga (kanan/kiri/tidak ada)";
                penilaian = "Otitis Media Akut/Kronik";
                instruksi = "Jaga kebersihan telinga, hindari memasukkan benda ke dalam telinga, hindari air masuk ke telinga saat mandi, hindari meniup hidung terlalu keras";
                rtl = "Pemberian antibiotik, analgetik, antipiretik jika demam\nKultus telinga bila ada sekret";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika nyeri tidak membaik');
            }
            
            // Template Sinusitis
            else if (template === 'sinusitis') {
                keluhan = "Pasien mengeluh nyeri di daerah sinus (dahi/pipi), hidung tersumbat, ingus kental berwarna kuning/hijau, nyeri kepala, post nasal drip. Riwayat ISPA sebelumnya (+).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nHidung : Mukosa hiperemis, edema, sekret purulen\nNyeri tekan sinus frontalis/maksilaris (+)\nTenggorokan : Hiperemis, post nasal drip (+)";
                penilaian = "Sinusitis Akut/Kronik";
                instruksi = "Kompres hangat di area sinus yang nyeri, inhalasi uap hangat, minum banyak air, jaga kelembaban ruangan, cuci hidung dengan larutan salin";
                rtl = "Pemberian antibiotik, dekongestan, analgetik\nObservasi perkembangan gejala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.3');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '97');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '75');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Dislipidemia
            else if (template === 'dislipidemia') {
                keluhan = "Pasien melakukan kontrol rutin. Riwayat dislipidemia (+), hipertensi (+/-), DM (+/-). Keluhan nyeri dada (-), sesak (-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-";
                penilaian = "Dislipidemia";
                instruksi = "Diet rendah lemak jenuh dan kolesterol, konsumsi banyak buah dan sayur, olahraga teratur, hindari alkohol dan rokok";
                rtl = "Pemeriksaan profil lipid\nPemberian statin, fibrat sesuai indikasi\nMonitoring faktor risiko kardiovaskular";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '97');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '90');
                setLivewireValue('evaluasi', 'Kontrol 1 bulan lagi untuk evaluasi profil lipid');
            }
            
            // Template Malaria
            else if (template === 'malaria') {
                keluhan = "Pasien mengeluh demam tinggi yang hilang timbul, menggigil, berkeringat, nyeri kepala, nyeri otot, mual, muntah. Riwayat perjalanan ke daerah endemis (+), gigitan nyamuk (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Sedang, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, hepatosplenomegali (+/-), NT(-), peristaltik (+) normal.\nKonjungtiva : Anemis (+/-)";
                penilaian = "Suspek Malaria";
                instruksi = "Istirahat total, minum banyak cairan, makan makanan bergizi, gunakan kelambu saat tidur, gunakan obat anti nyamuk";
                rtl = "Pemeriksaan darah malaria (mikroskopis/RDT)\nPemberian antimalarial sesuai hasil pemeriksaan, antipiretik, antiemetik";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '39.2');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '100');
                setLivewireValue('respirasi', '24');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika demam tidak turun');
            }
            
            // Template Demam Berdarah
            else if (template === 'dbd') {
                keluhan = "Pasien mengeluh demam tinggi mendadak 2-7 hari, nyeri kepala, nyeri otot/sendi, nyeri retroorbital, mual, muntah, nafsu makan menurun. Kadang disertai ruam kemerahan/perdarahan (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Sedang, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, hepatomegali (+/-), NT(+/-), peristaltik (+) normal.\nKulit : Petekie/ekimosis/ruam (+/-)";
                penilaian = "Suspek Dengue/DBD";
                instruksi = "Istirahat total, minum banyak cairan (air putih/jus), makan makanan bergizi, monitoring perdarahan, hindari obat-obatan yang mengganggu fungsi trombosit";
                rtl = "Pemeriksaan darah lengkap, NS1/IgM/IgG Dengue\nManajemen cairan sesuai status hidrasi, antipiretik (hindari aspirin/NSAID)";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.8');
                setLivewireValue('tensi', '100/70');
                setLivewireValue('nadi', '96');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '97');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol setiap hari untuk pemantauan hitung darah lengkap');
            }
            
            // Template Pneumonia
            else if (template === 'pneumonia') {
                keluhan = "Pasien mengeluh demam, batuk berdahak purulen/berdarah, sesak napas, nyeri dada saat batuk/bernapas dalam. Kadang disertai menggigil, keringat malam, nafsu makan menurun.";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Sedang-Berat, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ menurun, Ronkhi basah kasar +/+, suara napas bronkial (+)\nRetraksi otot pernapasan : (+/-)";
                penilaian = "Pneumonia";
                instruksi = "Istirahat total, posisi semi-fowler, minum banyak cairan, makan makanan bergizi, batasi aktivitas fisik";
                rtl = "Pemeriksaan foto thorax, pemeriksaan dahak, darah lengkap\nPemberian antibiotik, bronkodilator, antipiretik, ekspektoran";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.5');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '100');
                setLivewireValue('respirasi', '26');
                setLivewireValue('spo2', '94');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika sesak memberat');
            }
            
            // Template GERD
            else if (template === 'gerd') {
                keluhan = "Pasien mengeluh nyeri/panas di ulu hati (heartburn) yang menjalar ke tenggorokan, regurgitasi asam, sendawa berlebihan. Keluhan memberat setelah makan banyak, berbaring, atau membungkuk.";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan epigastrium (+/-), peristaltik (+) normal\nTenggorokan : Faring hiperemis (+/-)";
                penilaian = "Gastroesophageal Reflux Disease (GERD)";
                instruksi = "Makan porsi kecil tapi sering, hindari makanan berlemak/pedas/asam/gorengan, kurangi kopi/alkohol/coklat/mint, hindari makan 3 jam sebelum tidur, elevasi kepala saat tidur";
                rtl = "Pemberian PPI/H2 blocker, antasida\nAnjuran modifikasi gaya hidup";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '86');
                setLivewireValue('evaluasi', 'Kontrol 14 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Migrain
            else if (template === 'migrain') {
                keluhan = "Pasien mengeluh nyeri kepala berdenyut, umumnya unilateral, intensitas sedang-berat, memburuk dengan aktivitas fisik. Kadang disertai mual, muntah, fotofobia, fonofobia, dan aura visual.";
                pemeriksaan = "Keadaan Umum : Tampak Kesakitan, Composmentis\nKepala : Nyeri tekan (-), kaku kuduk (-), tanda rangsang meningeal (-)\nNeurologis : Refleks fisiologis normal, tanda neurologis fokal (-), nistagmus (-)";
                penilaian = "Migrain";
                instruksi = "Istirahat di ruangan gelap dan tenang saat serangan, identifikasi dan hindari faktor pemicu (makanan tertentu, stres, kurang tidur, siklus menstruasi), tidur yang cukup, olahraga teratur";
                rtl = "Pemberian analgetik (asetaminofen/NSAID), antiemetik jika mual\nPencegahan dengan propranolol/amitriptilin jika serangan sering";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 14 hari lagi atau jika frekuensi serangan meningkat');
            }
            
            // Template Hemoroid
            else if (template === 'hemoroid') {
                keluhan = "Pasien mengeluh BAB berdarah (darah merah segar), nyeri saat BAB, benjolan di anus, rasa tidak tuntas setelah BAB, gatal di sekitar anus. Riwayat konstipasi (+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nAbdomen : Supel, NT(-), peristaltik (+) normal\nAnus : Hemoroid eksterna/interna grade I-IV, prolaps (-/+), trombosis (-/+), fisura ani (-/+)";
                penilaian = "Hemoroid";
                instruksi = "Konsumsi makanan tinggi serat dan banyak minum air putih, hindari mengejan saat BAB, jaga kebersihan area perianal, hindari duduk terlalu lama, olahraga teratur";
                rtl = "Pemberian salep/supositoria anti hemoroid, pelembut feses, analgetik\nManajemen konstipasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '80');
                setLivewireValue('evaluasi', 'Kontrol 14 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Konstipasi
            else if (template === 'konstipasi') {
                keluhan = "Pasien mengeluh BAB keras dan sulit, frekuensi BAB < 3x seminggu, perlu mengejan keras saat BAB, rasa tidak tuntas setelah BAB. Riwayat kurang minum (+), kurang serat (+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nAbdomen : Supel, nyeri tekan (-/+), peristaltik (+) normal/menurun\nAnus : Fisura ani (-/+), hemoroid (-/+)";
                penilaian = "Konstipasi";
                instruksi = "Konsumsi makanan tinggi serat (buah, sayur, biji-bijian), minum air putih yang cukup (8-10 gelas/hari), olahraga teratur, BAB teratur dan jangan ditunda, posisi BAB yang benar";
                rtl = "Pemberian laksatif, pelembut feses\nEdukasi pola makan dan hidup sehat";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '82');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Faringitis
            else if (template === 'faringitis') {
                keluhan = "Pasien mengeluh nyeri tenggorokan, sulit menelan, tenggorokan terasa kering/gatal, batuk, suara serak. Kadang disertai demam, nyeri kepala, dan malaise.";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan, Composmentis\nTenggorokan : Faring hiperemis, eksudat (-/+), pembesaran tonsil (-/+)\nLeher : Nyeri tekan (-/+), pembesaran KGB (-/+)";
                penilaian = "Faringitis Akut";
                instruksi = "Istirahat yang cukup, minum air putih yang banyak, berkumur dengan air hangat yang diberi garam, makan makanan lunak, hindari makanan/minuman yang terlalu panas/dingin/pedas";
                rtl = "Pemberian analgetik, antipiretik jika demam, antibiotik sesuai indikasi\nObservasi perkembangan gejala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika gejala tidak membaik');
            }
            
            // Template Tonsilitis
            else if (template === 'tonsilitis') {
                keluhan = "Pasien mengeluh nyeri tenggorokan hebat, sulit menelan, demam, suara berubah (bindeng), bau mulut, nyeri telinga. Riwayat infeksi saluran napas atas sebelumnya (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Sedang, Composmentis\nTenggorokan : Tonsil T2-T3 hiperemis, eksudat putih/kekuningan (+)\nLeher : Nyeri tekan (+), pembesaran KGB (+)";
                penilaian = "Tonsilitis Akut";
                instruksi = "Istirahat total, minum air putih yang banyak, berkumur dengan air hangat yang diberi garam, makan makanan lunak, hindari makanan/minuman yang terlalu panas/dingin/pedas";
                rtl = "Pemberian antibiotik, analgetik, antipiretik\nObservasi komplikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.2');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '97');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 5 hari lagi atau jika demam/nyeri tidak membaik');
            }
            
            // Template Disfungsi Ereksi
            else if (template === 'disfungsi-ereksi') {
                keluhan = "Pasien mengeluh kesulitan mencapai/mempertahankan ereksi yang cukup untuk berhubungan. Keluhan dirasakan > 3 bulan. Riwayat DM (+/-), hipertensi (+/-), merokok (+/-), stres (+/-), alkohol (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal\nGenitalia : Dalam batas normal";
                penilaian = "Disfungsi Ereksi";
                instruksi = "Kelola faktor risiko (kontrol gula darah/tekanan darah, berhenti merokok, kurangi alkohol), kurangi stres, komunikasi terbuka dengan pasangan, olahraga teratur";
                rtl = "Pemberian PDE5 inhibitor sesuai indikasi\nTerapi faktor risiko yang mendasari\nKonseling psikologis bila diperlukan";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '90');
                setLivewireValue('evaluasi', 'Kontrol 1 bulan lagi untuk evaluasi respon pengobatan');
            }
            
            // Template Gagal Jantung
            else if (template === 'gagal-jantung') {
                keluhan = "Pasien mengeluh sesak napas terutama saat beraktivitas/berbaring, mudah lelah, kaki bengkak, terbangun malam hari karena sesak. Riwayat hipertensi (+/-), penyakit jantung koroner (+/-), DM (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas melemah, reguler/tidak, bising (+/-), gallop (+/-)\nPulmo : SDV +/+ menurun, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal, hepatomegali (+/-)\nExt : Edema pretibial (+/+), akral hangat/dingin";
                penilaian = "Gagal Jantung";
                instruksi = "Batasi aktivitas fisik berat, diet rendah garam dan lemak, istirahat cukup dengan posisi kepala ditinggikan, pantau berat badan, batasi asupan cairan";
                rtl = "Pemberian diuretik, ACE-inhibitor, Beta blocker sesuai indikasi\nMonitoring gejala dan tanda gagal jantung";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '140/90');
                setLivewireValue('nadi', '96');
                setLivewireValue('respirasi', '24');
                setLivewireValue('spo2', '92');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '90');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau segera jika sesak memberat');
            }
            
            // Template Maag
            else if (template === 'maag') {
                keluhan = "Pasien mengeluh nyeri ulu hati, kembung, mual, muntah, cepat kenyang. Keluhan memberat saat perut kosong/stres. Riwayat konsumsi makanan pedas/asam/berlemak (+), minum kopi/alkohol (+/-), stress (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan epigastrium (+), defans muskular (-), peristaltik (+) normal";
                penilaian = "Dispepsia/Gastritis";
                instruksi = "Makan porsi kecil tapi sering, hindari makanan pedas/asam/berlemak/gorengan, kurangi kopi/alkohol/coklat/mint, hindari makan < 3 jam sebelum tidur, kelola stres";
                rtl = "Pemberian antasida, PPI/H2 blocker\nObservasi faktor pemicu";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '78');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Keputihan/Vaginitis
            else if (template === 'keputihan') {
                keluhan = "Pasien mengeluh keputihan abnormal (berwarna putih/kekuningan/kehijauan/keabu-abuan, berbau tidak sedap, gatal, jumlah banyak). Kadang disertai nyeri saat BAK/berhubungan, kemerahan pada area genital.";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nGenitalia Eksterna : Kemerahan (+/-), flour albus (+) warna putih/kuning/hijau/abu-abu, konsistensi kental/encer\nNyeri tekan suprapubik (-/+)";
                penilaian = "Vaginitis (Bacterial Vaginosis/Kandidiasis/Trikomoniasis)";
                instruksi = "Jaga kebersihan area genital, gunakan pakaian dalam dari katun dan tidak ketat, ganti pembalut/pantyliner secara teratur, keringkan area genital setelah BAK/BAB, hindari douching";
                rtl = "Pemberian antifungal/antibiotik oral/topikal sesuai indikasi\nEdukasi kebersihan genital";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '110/70');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '70');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Stomatitis
            else if (template === 'stomatitis') {
                keluhan = "Pasien mengeluh sariawan/luka di mulut, nyeri saat makan/minum/bicara, mulut terasa panas, air liur berlebih. Riwayat trauma (-/+), stres (+/-), kurang vitamin (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nMulut : Ulkus pada mukosa mulut/lidah/gusi, diameter <1 cm, dasar putih/kekuningan, tepi kemerahan, nyeri tekan (+)";
                penilaian = "Stomatitis Aftosa";
                instruksi = "Menjaga kebersihan mulut, sikat gigi dengan sikat lembut, hindari makanan pedas/asam/keras/panas, perbanyak minum air putih, hindari stres";
                rtl = "Pemberian obat kumur antiseptik, gel anestesi lokal, kortikosteroid topikal\nSuplemen vitamin B kompleks";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau jika keluhan tidak membaik');
            }
            
            // Template Insomnia
            else if (template === 'insomnia') {
                keluhan = "Pasien mengeluh sulit memulai/mempertahankan tidur, atau bangun terlalu awal dan tidak bisa tidur kembali. Keluhan dirasakan >1 bulan dan mengganggu aktivitas sehari-hari. Riwayat stres (+), depresi (+/-), cemas (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nTanda Vital : Dalam batas normal\nTanda depresi/ansietas : (+/-)\nPemeriksaan fisik lainnya dalam batas normal";
                penilaian = "Insomnia";
                instruksi = "Tidur dan bangun pada jam yang sama setiap hari, hindari tidur siang terlalu lama, hindari kafein/alkohol/nikotin terutama di malam hari, olahraga teratur (tapi tidak dekat waktu tidur), lingkungan tidur yang nyaman";
                rtl = "Pemberian sedatif-hipnotik dengan dosis minimal sesuai indikasi dan jangka pendek\nTerapi kognitif perilaku untuk insomnia jika tersedia";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '130/80');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '80');
                setLivewireValue('evaluasi', 'Kontrol 14 hari lagi untuk evaluasi kualitas tidur');
            }
            
            // Template CVA (Stroke)
            else if (template === 'cva') {
                keluhan = "Pasien datang dengan riwayat kelemahan/kelumpuhan anggota gerak (kanan/kiri), pelo/sulit bicara, mulut mencong, kesadaran menurun. Riwayat hipertensi (+/-), DM (+/-), dislipidemia (+/-), merokok (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis/Somnolen\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nNeurologis : GCS E(4) V(4-5) M(6), kekuatan motorik (menurun/hemiplegi), refleks patologis (+/-), sensibilitas (menurun/normal)";
                penilaian = "Cerebrovascular Accident (CVA/Stroke)";
                instruksi = "Posisi kepala elevasi 30 derajat, mobilisasi bertahap sesuai kemampuan, latihan fisioterapi, hindari makanan yang sulit ditelan, kontrol faktor risiko (tekanan darah, gula darah, kolesterol)";
                rtl = "Kontrol rutin ke poliklinik saraf\nManajemen faktor risiko\nRehabilitasi medik";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.8');
                setLivewireValue('tensi', '160/100');
                setLivewireValue('nadi', '92');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '13-15');
                setLivewireValue('lingkar', '90');
                setLivewireValue('evaluasi', 'Kontrol 7 hari lagi atau segera jika keluhan memberat');
            }
            
            // Template Cacingan
            else if (template === 'cacingan') {
                keluhan = "Pasien/orangtua mengeluhkan perut sering sakit, nafsu makan menurun, BB tidak naik/menurun, pucat, lemah, dan kadang BAB dengan cacing/telur cacing. Riwayat sanitasi buruk (+/-), kebiasaan tidak cuci tangan (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, nyeri tekan umbilikal (+/-), peristaltik (+) normal\nKonjungtiva : Anemis (+/-)";
                penilaian = "Helminthiasis (Cacingan)";
                instruksi = "Minum obat cacing sesuai dosis, jaga kebersihan tangan dan kuku, selalu cuci tangan sebelum makan dan setelah BAB, gunakan air bersih, makan makanan yang sudah dimasak dengan baik";
                rtl = "Pemberian obat cacing (albendazole/mebendazole)\nSuplemen Fe jika anemia\nEdukasi PHBS";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '100/70');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '65');
                setLivewireValue('evaluasi', 'Kontrol 2 minggu lagi untuk evaluasi keluhan');
            }
            
            // Template Kusta/Lepra
            else if (template === 'kusta') {
                keluhan = "Pasien mengeluh bercak putih/kemerahan di kulit yang mati rasa, kebas pada bagian tertentu, benjolan di wajah/telinga, kelemahan otot, deformitas. Riwayat kontak dengan penderita kusta (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nKulit : Makula hipopigmentasi/eritematosa dengan pinggir tegas, anestesi terhadap suhu/nyeri/raba pada lesi\nPembesaran saraf tepi (+/-) : N. ulnaris/radialis/tibialis posterior/peroneal\nDeformitas (-/+)";
                penilaian = "Morbus Hansen (Kusta/Lepra)";
                instruksi = "Minum obat secara teratur sesuai dosis, segera kontrol jika ada efek samping obat, rawat kulit yang kering dengan pelembab, hindari trauma pada area yang mati rasa, periksa diri secara rutin";
                rtl = "Regimen MDT sesuai tipe (PB/MB)\nPemeriksaan kontak serumah\nPencegahan disabilitas\nDukungan psikososial";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol setiap bulan untuk pengambilan obat dan pemantauan');
            }
            
            // Template Tinea/Jamur Kulit
            else if (template === 'tinea') {
                keluhan = "Pasien mengeluh bercak kemerahan, gatal, bersisik di area (kulit kepala/badan/selangkangan/kaki). Riwayat keringat berlebih (+), lingkungan lembab (+), pemakaian sepatu tertutup lama (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nKulit : Lesi eritematosa, skuama, berbatas tegas, bentuk sirsinat/polisiklik, dengan central healing\nTanda infeksi sekunder (-/+)";
                penilaian = "Dermatomikosis (Tinea)";
                instruksi = "Jaga kebersihan dan kekeringan area yang terinfeksi, keringkan area lipatan setelah mandi, ganti pakaian bila basah, gunakan pakaian yang menyerap keringat, hindari berbagi perlengkapan pribadi";
                rtl = "Pemberian antifungal topikal (mikonazol/ketokonazol/clotrimazole)\nOral antifungal jika luas/tidak respons dengan topikal";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '75');
                setLivewireValue('evaluasi', 'Kontrol 2 minggu lagi atau jika keluhan tidak membaik');
            }
            
            // Template Scabies
            else if (template === 'scabies') {
                keluhan = "Pasien mengeluh gatal-gatal terutama pada malam hari di area sela jari, pergelangan tangan, ketiak, pinggang, pusar, dan alat kelamin. Riwayat kontak dengan penderita scabies (+), sanitasi kurang (+).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nKulit : Papul, vesikel, pustul, ekskoriasi di area predileksi, terowongan (khas scabies) (+/-)\nTanda infeksi sekunder (-/+)";
                penilaian = "Scabies";
                instruksi = "Pengobatan seluruh anggota keluarga secara bersamaan, ganti dan cuci pakaian/sprei/handuk dengan air panas, jemur di bawah sinar matahari, vakum kasur, hindari kontak fisik langsung, jaga kebersihan";
                rtl = "Pemberian permethrin 5% krim/lotion\nAntihistamin oral untuk gatal\nAntibiotik jika ada infeksi sekunder";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '80');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '72');
                setLivewireValue('evaluasi', 'Kontrol 1 minggu lagi untuk evaluasi respon pengobatan');
            }
            
            // Template Hipertiroid
            else if (template === 'hipertiroid') {
                keluhan = "Pasien mengeluh berat badan menurun meski nafsu makan meningkat, mudah berkeringat, tidak tahan panas, tremor, jantung berdebar, gelisah, mata melotot. Riwayat gondok (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Kurus, Composmentis\nThorax : Cor S1-2 intensitas normal, takikardia, bising (-)\nPulmo : SDV +/+ ST -/-\nTiroid : Pembesaran difus/nodular (+)\nMata : Eksoftalmus (+/-), retraksi kelopak (+/-)\nEkstremitas : Tremor halus (+)";
                penilaian = "Hipertiroidisme";
                instruksi = "Diet tinggi kalori, protein, dan karbohidrat, hindari makanan mengandung yodium tinggi, hindari stres, istirahat cukup, hindari kafein dan stimulan lain";
                rtl = "Pemberian antitiroid (PTU/methimazole), beta blocker\nPemeriksaan fungsi tiroid secara berkala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '37.0');
                setLivewireValue('tensi', '140/70');
                setLivewireValue('nadi', '110');
                setLivewireValue('respirasi', '22');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '65');
                setLivewireValue('evaluasi', 'Kontrol setiap 2 minggu hingga eutiroid');
            }
            
            // Template Hipotiroid
            else if (template === 'hipotiroid') {
                keluhan = "Pasien mengeluh mudah lelah, berat badan naik, tidak tahan dingin, kulit kering, rambut rontok, konstipasi, bicara/gerakan lambat, wajah sembab. Riwayat gondok (+/-).";
                pemeriksaan = "Keadaan Umum : Tampak Gemuk, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nTiroid : Pembesaran difus/nodular (+/-)\nWajah : Miksedema (+)\nKulit : Kering, kasar\nEkstremitas : Edema non-pitting (+/-)";
                penilaian = "Hipotiroidisme";
                instruksi = "Minum obat secara teratur di pagi hari dengan perut kosong (30 menit sebelum makan), hindari konsumsi bersamaan dengan kalsium/besi/antasida, kontrol rutin";
                rtl = "Pemberian levotiroksin\nPemeriksaan fungsi tiroid secara berkala";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.2');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '60');
                setLivewireValue('respirasi', '18');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '90');
                setLivewireValue('evaluasi', 'Kontrol 1 bulan lagi untuk evaluasi dosis dan pemeriksaan TSH');
            }
            
            // Template Epilepsi
            else if (template === 'epilepsi') {
                keluhan = "Pasien datang dengan riwayat kejang berulang, kehilangan kesadaran saat kejang, kaku seluruh tubuh, mulut berbusa, kadang inkontinensia BAK/BAB. Riwayat trauma kepala (+/-), infeksi otak (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nKepala : Trauma (-/+)\nNeurologis : Refleks fisiologis normal, tanda rangsang meningeal (-), tanda lateralisasi (-)";
                penilaian = "Epilepsi";
                instruksi = "Minum obat secara teratur, hindari pemicu kejang (kurang tidur, stres, alkohol, cahaya berkedip-kedip), tidur cukup, hindari aktivitas berisiko tinggi (mengemudi, berenang sendiri), pakai gelang identitas medis";
                rtl = "Pemberian antikonvulsan sesuai jenis kejang\nEdukasi keluarga tentang pertolongan saat kejang\nPencegahan trauma saat kejang";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '120/80');
                setLivewireValue('nadi', '84');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '98');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '75');
                setLivewireValue('evaluasi', 'Kontrol 1 bulan lagi atau segera jika kejang muncul kembali');
            }
            
            // Template Kejang Demam
            else if (template === 'kejang-demam') {
                keluhan = "Orangtua melaporkan anak mengalami kejang saat demam, kejang berlangsung beberapa menit, menyeluruh/fokal, disertai mata mendelik, kaku seluruh tubuh, mulut berbusa. Riwayat ISPA/diare sebelumnya (+).";
                pemeriksaan = "Keadaan Umum : Tampak Sakit Ringan-Sedang, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi -/+\nKepala : Ubun-ubun cekung/datar/menonjol\nNeurologis : Refleks fisiologis normal, tanda rangsang meningeal (-), tanda lateralisasi (-)";
                penilaian = "Kejang Demam";
                instruksi = "Kompres hangat saat demam, berikan antipiretik sesuai dosis, posisikan kepala miring saat kejang, jangan memasukkan apapun ke mulut saat kejang, jangan menahan gerakan kejang, catat durasi kejang";
                rtl = "Pemberian antipiretik\nDiazepam rektal untuk dibawa pulang jika kejang >5 menit\nTerapi penyebab demam";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '38.8');
                setLivewireValue('tensi', '90/60');
                setLivewireValue('nadi', '120');
                setLivewireValue('respirasi', '30');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '50');
                setLivewireValue('evaluasi', 'Kontrol 3 hari lagi atau segera jika kejang berulang');
            }
            
            // Template Diabetes
            else if (template === 'kencing-manis') {
                keluhan = "Pasien mengeluh poliuria, polidipsia, polifagia, berat badan menurun, mudah lelah, luka sulit sembuh, gangguan penglihatan. Riwayat DM di keluarga (+/-).";
                pemeriksaan = "Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal\nEkstremitas : Luka (-/+), sensibilitas (-/+)";
                penilaian = "Diabetes Mellitus";
                instruksi = "Diet seimbang rendah gula sederhana, tinggi serat, aktivitas fisik teratur, kontrol gula darah rutin, perawatan kaki diabetik, hindari rokok dan alkohol";
                rtl = "Pemberian OHO/insulin sesuai indikasi\nPemeriksaan gula darah, HbA1c berkala\nScreening komplikasi";
                alergi = "Tidak Ada";
                
                setLivewireValue('keluhan', keluhan);
                setLivewireValue('pemeriksaan', pemeriksaan);
                setLivewireValue('penilaian', penilaian);
                setLivewireValue('instruksi', instruksi);
                setLivewireValue('rtl', rtl);
                setLivewireValue('alergi', alergi);
                
                // Set nilai default untuk vital sign
                setLivewireValue('suhu', '36.5');
                setLivewireValue('tensi', '140/90');
                setLivewireValue('nadi', '88');
                setLivewireValue('respirasi', '20');
                setLivewireValue('spo2', '96');
                setLivewireValue('gcs', '15');
                setLivewireValue('lingkar', '95');
                setLivewireValue('evaluasi', 'Kontrol 1 bulan lagi untuk evaluasi kadar gula darah');
            }
            
            // Tampilkan notifikasi sukses
            setTimeout(function() {
                Swal.fire({
                    title: 'Berhasil',
                    text: 'Template berhasil diterapkan',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }, 1000);
        });
        
        // Reset form button
        $('#resetFormBtn').click(function(e) {
            e.preventDefault();
            
            // Reset semua textarea dan input dengan wire:model.defer
            document.querySelectorAll('[wire\\:model\\.defer]').forEach(element => {
                element.value = '';
                element.dispatchEvent(new Event('input', { bubbles: true }));
                
                // Update Livewire component jika tersedia
                if (window.Livewire) {
                    const livewireComponent = window.Livewire.find(
                        element.closest('[wire\\:id]')?.getAttribute('wire:id')
                    );
                    
                    if (livewireComponent) {
                        const property = element.getAttribute('wire:model.defer');
                        livewireComponent.set(property, '');
                    }
                }
            });
            
            // Reset template selector
            $('#template-selector').val('');
            
            // Tampilkan notifikasi
            Swal.fire({
                title: 'Reset Form',
                text: 'Form telah direset',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false
            });
        });
        
        // Event listener untuk formReset dari Livewire
        window.addEventListener('formReset', function() {
            // Reset template selector
            $('#template-selector').val('');
            
            // Terapkan template normal setelah reset
            setTimeout(function() {
                applyNormalTemplate();
            }, 100);
        });
    });
    
    window.addEventListener('swal:pemeriksaan', function(e) {
        Swal.fire(e.detail);
    });
    
    // Event listener untuk notifikasi toast dari PCare
    window.addEventListener('toast', function(e) {
        const { type, message } = e.detail;
        
        let icon = 'info';
        let title = 'Informasi';
        
        switch(type) {
            case 'success':
                icon = 'success';
                title = 'Berhasil';
                break;
            case 'warning':
                icon = 'warning';
                title = 'Peringatan';
                break;
            case 'error':
                icon = 'error';
                title = 'Error';
                break;
        }
        
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            timer: type === 'success' ? 3000 : 5000,
            showConfirmButton: type === 'error',
            toast: true,
            position: 'top-end',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        });
    });
    
    // Event listener khusus untuk alert PCare dengan redirect
    window.addEventListener('swal:alert', function(e) {
        const { type, title, text, timer, toast, position } = e.detail;
        
        // Jika ini adalah alert sukses kunjungan PCare, tambahkan animasi dan redirect
        if (type === 'success' && text && text.includes('Kunjungan PCare berhasil')) {
            Swal.fire({
                title: title,
                text: text,
                icon: type,
                timer: timer || 3000,
                toast: toast || false,
                position: position || 'center',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showClass: {
                    popup: 'animate__animated animate__bounceIn animate__faster',
                    backdrop: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__bounceOut animate__faster',
                    backdrop: 'animate__animated animate__fadeOut animate__faster'
                },
                customClass: {
                    popup: 'pcare-success-alert',
                    title: 'pcare-success-title',
                    content: 'pcare-success-content'
                },
                didOpen: () => {
                    // Tambahkan efek visual tambahan
                    const popup = Swal.getPopup();
                    popup.style.transform = 'scale(1)';
                    popup.style.transition = 'all 0.3s ease-in-out';
                },
                willClose: () => {
                    // Animasi sebelum redirect
                    const popup = Swal.getPopup();
                    popup.style.transform = 'scale(0.8)';
                    popup.style.opacity = '0';
                }
            }).then(() => {
                // Redirect ke halaman register setelah alert ditutup
                setTimeout(() => {
                    window.location.href = '/register';
                }, 500);
            });
        } else {
            // Alert biasa tanpa redirect
            Swal.fire({
                title: title,
                text: text,
                icon: type,
                timer: timer,
                toast: toast,
                position: position,
                showConfirmButton: type === 'error',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        }
    });
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
.pcare-success-alert {
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3) !important;
    border: 2px solid #28a745 !important;
}

.pcare-success-title {
    color: #28a745 !important;
    font-weight: bold !important;
    font-size: 1.5rem !important;
}

.pcare-success-content {
    color: #155724 !important;
    font-size: 1.1rem !important;
}

.swal2-popup.pcare-success-alert {
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%) !important;
}

.swal2-icon.swal2-success {
    border-color: #28a745 !important;
}

.swal2-icon.swal2-success .swal2-success-ring {
    border-color: #28a745 !important;
}

.swal2-icon.swal2-success .swal2-success-fix {
    background-color: #28a745 !important;
}

/* Debug Alert Styling */
.debug-alert {
    border-radius: 10px !important;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2) !important;
    border: 2px solid #007bff !important;
}

.debug-title {
    color: #007bff !important;
    font-weight: bold !important;
    font-size: 1.3rem !important;
}

.swal2-popup.debug-alert {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
}

/* Dropdown Kehadiran Styling */
.attendance-option {
    transition: all 0.3s ease !important;
}

.attendance-option:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px) !important;
}

.btn-group .dropdown-menu {
    border-radius: 8px !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #dee2e6 !important;
}
</style>
@endpush