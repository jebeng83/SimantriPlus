<div>
    <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-clipboard" collapsible>
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
        </div>

        <!-- Tambahkan template selector -->
        <div class="mb-3">
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
            </select>
        </div>

        <form id="pemeriksaanForm">
            <div class="row">
                <x-adminlte-textarea name="keluhan" label="Subjek" fgroup-class="col-md-6" rows="4"
                    placeholder="Pasien mengeluh...">
                    {{$pemeriksaan->keluhan ?? ''}}
                </x-adminlte-textarea>
                <x-adminlte-textarea name="pemeriksaan" label="Objek" fgroup-class="col-md-6" rows="4"
                    placeholder="KU : Composmentis, Baik&#10;Thorax : Cor S1-2 intensitas normal, reguler, bising (-)&#10;Pulmo : SDV +/+ ST -/-&#10;Abdomen : Supel, NT(-), peristaltik (+) normal.&#10;EXT : Oedem -/-">
                    {{$pemeriksaan->pemeriksaan ?? ''}}
                </x-adminlte-textarea>
            </div>
            <div class="row">
                <x-adminlte-textarea name="penilaian" label="Asesmen" fgroup-class="col-md-6" rows="4"
                    placeholder="Diagnosis...">
                    {{$pemeriksaan->penilaian ?? ''}}
                </x-adminlte-textarea>
                <x-adminlte-textarea name="instruksi" label="Instruksi" fgroup-class="col-md-6" rows="4"
                    placeholder="Istirahat Cukup, PHBS...">
                    {{$pemeriksaan->instruksi ?? ''}}
                </x-adminlte-textarea>
            </div>
            <div class="row">
                <x-adminlte-textarea name="rtl" label="Plan" fgroup-class="col-md-6" rows="4"
                    placeholder="Edukasi Kesehatan...">
                    {{$pemeriksaan->rtl ?? ''}}
                </x-adminlte-textarea>
                <x-adminlte-textarea name="alergi" label="Alergi" fgroup-class="col-md-6" rows="4"
                    placeholder="Tidak Ada...">
                    {{$alergi ?? ''}}
                </x-adminlte-textarea>
            </div>
            <div class="row">
                <x-adminlte-input name="suhu" label="Suhu Badan (C)" value="{{$pemeriksaan->suhu_tubuh ?? ''}}"
                    fgroup-class="col-md-4" placeholder="36.5" />
                <x-adminlte-input name="berat" label="Berat (Kg)" value="{{$pemeriksaan->berat ?? ''}}"
                    fgroup-class="col-md-4" placeholder="60" />
                <x-adminlte-input name="tinggi" label="Tinggi Badan (Cm)" value="{{$pemeriksaan->tinggi ?? ''}}"
                    fgroup-class="col-md-4" placeholder="165" />
            </div>
            <div class="row">
                <x-adminlte-input name="tensi" label="Tensi" value="{{$pemeriksaan->tensi ?? ''}}"
                    fgroup-class="col-md-4" placeholder="120/80" />
                <x-adminlte-input name="nadi" label="Nadi (per Menit)" value="{{$pemeriksaan->nadi ?? ''}}"
                    fgroup-class="col-md-4" placeholder="80" />
                <x-adminlte-input name="respirasi" label="Respirasi (per Menit)"
                    value="{{$pemeriksaan->respirasi ?? ''}}" fgroup-class="col-md-4" placeholder="20" />
            </div>
            <div class="row">
                <x-adminlte-select-bs name="imun" label="Imun Ke" fgroup-class="col-md-4">
                    <option value="-">-</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </x-adminlte-select-bs>
                <x-adminlte-input name="gcs" label="GCS (E, V, M)" value="{{$pemeriksaan->gcs ?? ''}}"
                    fgroup-class="col-md-4" placeholder="4,5,6" />
                <x-adminlte-select-bs name="kesadaran" label="Kesadaran" fgroup-class="col-md-4">
                    @if(!empty($pemeriksaan->kesadaran))
                    <option @php if($pemeriksaan->kesadaran == 'Compos Mentis') echo 'selected'; @endphp >Compos Mentis
                    </option>
                    <option @php if($pemeriksaan->kesadaran == 'Somnolence') echo 'selected'; @endphp >Somnolence
                    </option>
                    <option @php if($pemeriksaan->kesadaran == 'Sopor') echo 'selected'; @endphp >Sopor</option>
                    <option @php if($pemeriksaan->kesadaran == 'Coma') echo 'selected'; @endphp >Coma</option>
                    @else
                    <option>Compos Mentis</option>
                    <option>Somnolence</option>
                    <option>Sopor</option>
                    <option>Coma</option>
                    @endif
                </x-adminlte-select-bs>
            </div>
            <div class="row justify-content-end">
                <x-adminlte-button id="resetFormButton" class="col-2 mr-1" theme="secondary" label="Reset"
                    icon="fas fa-undo" />
                <x-adminlte-button id="pemeriksaanButton" class="col-2 ml-1" theme="primary" label="Simpan"
                    icon="fas fa-save" />
            </div>
        </form>
    </x-adminlte-card>
</div>

@push('js')
<script id="pemeriksaanjs" src="{{ asset('js/ralan/pemeriksaan.js') }}" data-encryptNoRawat="{{ $encryptNoRawat }}"
    data-token="{{csrf_token()}}"></script>

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
    
    $(document).ready(function() {
        // Mulai update jam
        updateClock();
        
        // Template selector
        $('#template-selector').change(function() {
            var template = $(this).val();
            if (template !== '') {
                applyTemplate(template);
            }
        });
        
        // Reset form button
        $('#resetFormButton').click(function(e) {
            e.preventDefault();
            resetForm();
            Swal.fire({
                text: 'Form telah direset',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
    
    // Fungsi untuk menerapkan template yang dipilih
    function applyTemplate(templateType) {
        // Template untuk pemeriksaan normal
        if (templateType === 'normal') {
            $("textarea[name=keluhan]").val("Pasien melakukan kontrol rutin.");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Kondisi pasien stabil");
            $("textarea[name=instruksi]").val("Istirahat Cukup, PHBS");
            $("textarea[name=rtl]").val("Edukasi Kesehatan");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("80");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk demam
        else if (templateType === 'demam') {
            $("textarea[name=keluhan]").val("Pasien mengeluh demam sejak 2 hari yang lalu. Demam naik turun, kadang menggigil. Nyeri kepala (+), mual (-), muntah (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Demam");
            $("textarea[name=instruksi]").val("Istirahat cukup, kompres, minum banyak air putih");
            $("textarea[name=rtl]").val("Pemberian antipiretik\nObservasi suhu");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("38.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("110/70");
            $("input[name=nadi]").val("90");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk sakit kepala
        else if (templateType === 'sakit-kepala') {
            $("textarea[name=keluhan]").val("Pasien mengeluh sakit kepala berdenyut sejak 1 hari yang lalu. Nyeri skala 6/10. Mual (+), muntah (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Cephalgia");
            $("textarea[name=instruksi]").val("Istirahat yang cukup dalam ruangan yang tenang, hindari cahaya terang");
            $("textarea[name=rtl]").val("Pemberian analgetik dan anti mual");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("130/85");
            $("input[name=nadi]").val("85");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk sesak nafas
        else if (templateType === 'sesak') {
            $("textarea[name=keluhan]").val("Pasien mengeluh sesak napas sejak 4 jam yang lalu. Sesak memberat saat aktivitas. Batuk (+), dahak (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Sesak, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST +/+, Rhonki (+/+), Wheezing (+/+)\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Sesak Napas");
            $("textarea[name=instruksi]").val("Elevasi kepala 30 derajat, hindari aktivitas berat");
            $("textarea[name=rtl]").val("Pemberian bronkodilator\nObservasi respirasi");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("37.2");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("140/90");
            $("input[name=nadi]").val("100");
            $("input[name=respirasi]").val("28");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk nyeri perut
        else if (templateType === 'nyeri-perut') {
            $("textarea[name=keluhan]").val("Pasien mengeluh nyeri perut sejak 1 hari yang lalu. Nyeri terutama di ulu hati/epigastrium. Mual (+), muntah (-), nafsu makan menurun. BAB normal.");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Meringis, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Nyeri tekan epigastrium (+), defans muskular (-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Dispepsia");
            $("textarea[name=instruksi]").val("Hindari makanan pedas, asam, dan bergas. Makan porsi kecil tapi sering.");
            $("textarea[name=rtl]").val("Pemberian antasida dan proton pump inhibitor\nObservasi nyeri perut");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.7");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("88");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk diare
        else if (templateType === 'diare') {
            $("textarea[name=keluhan]").val("Pasien mengeluh diare cair sejak 2 hari yang lalu. BAB 5-6x/hari, konsistensi cair, tidak berdarah. Mual (+), muntah (-), demam (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Bising usus meningkat, nyeri tekan (-), defans muskular (-).\nEXT : Turgor menurun, akral hangat.");
            $("textarea[name=penilaian]").val("Gastroenteritis Akut");
            $("textarea[name=instruksi]").val("Rehidrasi oral, minum air putih minimal 2L/hari, hindari makanan yang merangsang.");
            $("textarea[name=rtl]").val("Pemberian oralit dan probiotik\nObservasi frekuensi BAB");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.9");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("110/70");
            $("input[name=nadi]").val("95");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk hipertensi
        else if (templateType === 'hipertensi') {
            $("textarea[name=keluhan]").val("Pasien mengeluh pusing berputar dan tengkuk terasa berat. Nyeri kepala (+), mual (-), muntah (-), riwayat hipertensi (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("Hipertensi Grade II");
            $("textarea[name=instruksi]").val("Diet rendah garam, hindari stress, istirahat cukup, pantau tekanan darah secara rutin.");
            $("textarea[name=rtl]").val("Pemberian antihipertensi\nKontrol tekanan darah secara rutin");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("170/100");
            $("input[name=nadi]").val("95");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk diabetes mellitus
        else if (templateType === 'diabetes') {
            $("textarea[name=keluhan]").val("Pasien mengeluh badan lemas, sering haus, sering BAK. Nafsu makan (+), penurunan berat badan (+), riwayat DM (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Lemah, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Akral hangat, CRT < 2 detik.");
            $("textarea[name=penilaian]").val("Diabetes Mellitus Tipe 2");
            $("textarea[name=instruksi]").val("Diet rendah gula, olahraga teratur, pantau kadar gula darah secara rutin.");
            $("textarea[name=rtl]").val("Pemeriksaan gula darah\nPemberian OHO/Insulin\nEdukasi diet dan aktivitas fisik");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("130/80");
            $("input[name=nadi]").val("88");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk batuk
        else if (templateType === 'batuk') {
            $("textarea[name=keluhan]").val("Pasien mengeluh batuk sejak 5 hari yang lalu. Batuk berdahak, warna dahak putih. Demam (+) ringan, mual (-), sesak (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Oedem -/-");
            $("textarea[name=penilaian]").val("ISPA");
            $("textarea[name=instruksi]").val("Istirahat cukup, minum air hangat, hindari udara dingin.");
            $("textarea[name=rtl]").val("Pemberian ekspektoran dan mukolitik\nPemberian antibiotik bila perlu");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("37.5");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("85");
            $("input[name=respirasi]").val("22");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk gatal-gatal/alergi
        else if (templateType === 'gatal') {
            $("textarea[name=keluhan]").val("Pasien mengeluh gatal-gatal di seluruh tubuh sejak 2 hari yang lalu. Kemerahan pada kulit (+), bentol-bentol (+). Riwayat alergi makanan/obat (-).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Baik, Composmentis\nThorax : Cor S1-2 intensitas normal, reguler, bising (-)\nPulmo : SDV +/+ ST -/-\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Ruam eritematosa di ekstremitas dan punggung, urtikaria (+).");
            $("textarea[name=penilaian]").val("Dermatitis Alergi");
            $("textarea[name=instruksi]").val("Hindari garukan pada kulit, gunakan pakaian longgar dan berbahan katun, hindari pemicu alergi.");
            $("textarea[name=rtl]").val("Pemberian antihistamin\nPemberian salep kortikosteroid topikal bila perlu");
            $("textarea[name=alergi]").val("Dalam investigasi");
            $("input[name=suhu]").val("36.7");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("120/80");
            $("input[name=nadi]").val("82");
            $("input[name=respirasi]").val("20");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        // Template untuk penyakit jantung
        else if (templateType === 'jantung') {
            $("textarea[name=keluhan]").val("Pasien mengeluh nyeri dada seperti tertekan benda berat sejak 6 jam yang lalu. Nyeri menjalar ke lengan kiri, sesak napas (+), keringat dingin (+), mual (+).");
            $("textarea[name=pemeriksaan]").val("Keadaan Umum : Tampak Cemas, Composmentis\nThorax : Cor S1-2 ireguler, bising (+) grade 2/6 di apex\nPulmo : SDV +/+ ST -/-, Ronkhi basah halus +/+\nAbdomen : Supel, NT(-), peristaltik (+) normal.\nEXT : Akral dingin, CRT > 2 detik.");
            $("textarea[name=penilaian]").val("Angina Pektoris");
            $("textarea[name=instruksi]").val("Istirahat total, hindari aktivitas berat, diet rendah garam dan lemak.");
            $("textarea[name=rtl]").val("EKG 12 lead\nPemeriksaan enzim jantung\nKonsultasi spesialis jantung\nPemberian anti angina");
            $("textarea[name=alergi]").val("Tidak Ada");
            $("input[name=suhu]").val("36.8");
            $("input[name=berat]").val("");
            $("input[name=tinggi]").val("");
            $("input[name=tensi]").val("150/95");
            $("input[name=nadi]").val("110");
            $("input[name=respirasi]").val("26");
            $("input[name=gcs]").val("456");
            $("select[name=kesadaran]").val("Compos Mentis").change();
            $("select[name=imun]").val("-").change();
        }
        
        Swal.fire({
            text: 'Template berhasil diterapkan',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }
    
    // Fungsi untuk reset form
    function resetForm() {
        $("textarea[name=keluhan]").val("");
        $("textarea[name=pemeriksaan]").val("");
        $("textarea[name=penilaian]").val("");
        $("textarea[name=instruksi]").val("");
        $("textarea[name=rtl]").val("");
        $("textarea[name=alergi]").val("");
        $("input[name=suhu]").val("");
        $("input[name=berat]").val("");
        $("input[name=tinggi]").val("");
        $("input[name=tensi]").val("");
        $("input[name=nadi]").val("");
        $("input[name=respirasi]").val("");
        $("input[name=gcs]").val("");
        $("select[name=kesadaran]").val("Compos Mentis").change();
        $("select[name=imun]").val("-").change();
    }
</script>
@endpush