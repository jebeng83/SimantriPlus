# Catatan: Dashboard Analisa CKG Siswa Sekolah

## Tujuan
- Menyusun rencana dashboard analisa hasil CKG anak sekolah berbasis data `skrining_siswa_sd` untuk monitoring kesehatan siswa, temuan, dan tindak lanjut.

## Struktur Folder Terkait
- `resources/views/ilp/data-siswa-sekolah/index.blade.php` menampilkan daftar siswa dengan filter sekolah/kelas/status (resources/views/ilp/data-siswa-sekolah/index.blade.php:49)
- `resources/views/ilp/data-siswa-sekolah/show.blade.php` detail satu siswa, termasuk identitas pasien, sekolah, kelas, dan kontak orang tua (resources/views/ilp/data-siswa-sekolah/show.blade.php:28)
- `resources/views/ilp/data-siswa-sekolah/create.blade.php` form tambah siswa baru (resources/views/ilp/data-siswa-sekolah/create.blade.php:40)
- `resources/views/ilp/data-siswa-sekolah/edit.blade.php` form edit data siswa (resources/views/ilp/data-siswa-sekolah/edit.blade.php:32)
- `resources/views/ilp/data-siswa-sekolah/dashboard.blade.php` dashboard sekolah umum (jumlah siswa, distribusi umur, status) (resources/views/ilp/data-siswa-sekolah/dashboard.blade.php:110)

## Rute & Controller Terkait
- Rute resource data siswa: `Route::resource('data-siswa-sekolah', DataSiswaSekolahController::class)` (routes/web.php:379)
- Rute dashboard sekolah: `Route::get('/dashboard-sekolah', DashboardSekolahController::class.'@index')` (routes/web.php:387)
- Controller dashboard menghitung metrik agregat per sekolah/kelas/jenis: `app/Http/Controllers/ILP/DashboardSekolahController.php` (app/Http/Controllers/ILP/DashboardSekolahController.php:1)

## Model & Relasi Data
- Model siswa sekolah: `App\Models\DataSiswaSekolah` memiliki relasi `hasMany` ke skrining SD (app/Models/DataSiswaSekolah.php:72)
- Model skrining SD: `App\Models\SkriningSiswaSD` `belongsTo` siswa sekolah (app/Models/SkriningSiswaSD.php:55)
- Scope bermanfaat pada skrining: `Normal`, `PerluPerhatian`, `Rujuk` (app/Models/SkriningSiswaSD.php:71)

## Skema Tabel `skrining_siswa_sd`
- Migrasi: `database/migrations/2025_01_15_100004_create_skrining_siswa_sd_table.php` (database/migrations/2025_01_15_100004_create_skrining_siswa_sd_table.php:12)
- Kolom inti:
  - `siswa_id` (FK ke `data_siswa_sekolah`)
  - `tanggal_skrining`, `petugas_skrining`
  - Antropometri: `berat_badan`, `tinggi_badan`, `imt`, `status_gizi`
  - Vitals: `tekanan_darah`, `denyut_nadi`, `suhu_tubuh`
  - Mata: `visus_od`, `visus_os`, `kelainan_mata`
  - Telinga: `pendengaran_kanan`, `pendengaran_kiri`, `kelainan_telinga`
  - Gigi: `gigi_karies`, `gigi_hilang`, `kelainan_gigi`
  - Riwayat: `riwayat_penyakit`, `riwayat_alergi`, `obat_dikonsumsi`
  - Imunisasi: `status_imunisasi` (JSON)
  - Kesimpulan: `kesimpulan`, `tindak_lanjut`, `status_skrining` (`Normal`, `Perlu Perhatian`, `Rujuk`)

## Indikator Dashboard CKG (Disarankan)
- Cakupan skrining:
  - Jumlah skrining per periode (minggu/bulan/semester)
  - Persentase siswa pernah diskrining vs total siswa aktif
- Status hasil skrining:
  - Distribusi `status_skrining` (Normal/Perlu Perhatian/Rujuk) keseluruhan, per sekolah, per kelas
  - Tren perubahan status per waktu
- Antropometri & Status gizi:
  - Rata-rata dan distribusi `berat_badan`, `tinggi_badan`, `imt`
  - Proporsi kategori `status_gizi` (mis. kurus, normal, gemuk) sesuai standar yang digunakan
- Pemeriksaan mata:
  - Visus rata-rata (`visus_od`, `visus_os`), jumlah kelainan mata terlapor
- Pemeriksaan telinga:
  - Proporsi gangguan pendengaran kanan/kiri, jumlah kelainan telinga
- Pemeriksaan gigi:
  - Rata-rata `gigi_karies`, `gigi_hilang`, jumlah kelainan gigi
- Imunisasi:
  - Cakupan per antigen dari `status_imunisasi` (JSON), persentase lengkap/tidak lengkap
- Tindak lanjut:
  - Jumlah rujukan, tindak lanjut selesai vs belum, waktu rata-rata tindak lanjut

## Sumber Data & Filter Umum
- Sumber utama: join `skrining_siswa_sd` ke `data_siswa_sekolah` (identitas, sekolah, kelas) dan `pasien` (jk, tgl_lahir, alamat)
- Filter yang konsisten dengan dashboard sekolah saat ini:
  - `sekolah`, `jenis_sekolah`, `kelas`, periode tanggal (`tanggal_skrining`) dan pencarian siswa

## Contoh Query Eloquent
- Total skrining dan status:
  - `SkriningSiswaSD::count()`
  - `SkriningSiswaSD::normal()->count()`
  - `SkriningSiswaSD::perluPerhatian()->count()`
  - `SkriningSiswaSD::rujuk()->count()`
- Agregasi per sekolah:
  - `SkriningSiswaSD::join('data_siswa_sekolah','skrining_siswa_sd.siswa_id','=','data_siswa_sekolah.id')`
    `->join('data_sekolah','data_siswa_sekolah.id_sekolah','=','data_sekolah.id_sekolah')`
    `->select('data_sekolah.nama_sekolah', DB::raw('count(*) as total'), DB::raw('sum(status_skrining="Rujuk") as rujuk'))`
    `->groupBy('data_sekolah.nama_sekolah')`
- Antropometri ringkas:
  - `SkriningSiswaSD::selectRaw('avg(berat_badan) as bb_avg, avg(tinggi_badan) as tb_avg, avg(imt) as imt_avg')->first()`
- Distribusi umur saat skrining (gunakan `pasien.tgl_lahir`): join dengan `data_siswa_sekolah` dan `pasien`, lalu `TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, tanggal_skrining)` dan bucket (lihat pola di app/Http/Controllers/ILP/DashboardSekolahController.php:263)

## Desain Halaman Dashboard CKG
- Lokasi view: `resources/views/ilp/data-siswa-sekolah/dashboard-ckg.blade.php`
- Bagian:
  - Filter (sekolah/kelas/jenis/periode)
  - Kartu ringkasan: total skrining, rujuk, perlu perhatian, normal
  - Grafik batang pie status skrining
  - Grafik garis tren skrining per bulan
  - Tabel agregasi per sekolah dan per kelas dengan kolom: total skrining, rujuk, perlu perhatian, normal
  - Panel antropometri: rata-rata BB/TB/IMT, distribusi status gizi
  - Panel mata/telinga/gigi: ringkasan temuan
  - Panel imunisasi: cakupan per antigen

## Integrasi Controller (Rencana)
- Tambah `ILP\DashboardCkgSekolahController@index` untuk mengambil data agregat dari `SkriningSiswaSD` dengan filter request.
- Reuse pola query di `DashboardSekolahController@index` (app/Http/Controllers/ILP/DashboardSekolahController.php:25) untuk konsistensi filter.
- Rute: `Route::get('/dashboard-ckg-sekolah', [DashboardCkgSekolahController::class,'index'])->name('dashboard-ckg-sekolah')`.

## Validasi & Kualitas Data
- Pastikan relasi `siswa_id` valid dan cascade delete berjalan (database/migrations/2025_01_15_100004_create_skrining_siswa_sd_table.php:15)
- Normalisasi nilai visus dan format tekanan darah agar konsisten.
- Pastikan `status_imunisasi` JSON memiliki skema konsisten untuk perhitungan cakupan.

## Langkah Implementasi Cepat
- Buat controller `DashboardCkgSekolahController` dengan filter `sekolah`, `kelas`, `jenis`, `periode`.
- Implementasikan agregasi status skrining, antropometri, dan panel temuan.
- Buat view `dashboard-ckg.blade.php` mengikuti gaya `dashboard.blade.php` (resources/views/ilp/data-siswa-sekolah/dashboard.blade.php:110).
- Tambah rute `dashboard-ckg-sekolah` dan tautan dari halaman indeks dan dashboard sekolah.

## Catatan Tambahan
- Untuk kinerja, gunakan agregasi `DB::raw` dan indeks pada kolom filter (`siswa_id`, `tanggal_skrining`, `status_skrining`).
- Pertimbangkan caching hasil agregat per periode jika volume data besar.

## Rancangan Presentasi Pimpinan (CKG Sekolah)
- Sasaran audiens: pimpinan Dinkes/RS/sekolah, fokus pada keputusan dan aksi.
- Struktur presentasi:
  - Sampul: judul, periode, unit pelaksana, kontak.
  - Tujuan: alasan skrining CKG, ruang lingkup, metodologi singkat.
  - Ringkasan eksekutif: jumlah siswa terskrining, cakupan rata-rata, proporsi Normal/Perlu/Rujuk.
  - Cakupan per sekolah: bar chart top sekolah, gap analisis sekolah dengan cakupan rendah.
  - Status skrining: donut chart distribusi Normal/Perlu/Rujuk, perubahan tren bulanan.
  - Risiko utama: kategori risiko tertinggi dan persentasenya, implikasi layanan.
  - Antropometri: IMT dan Status Gizi ringkas, kategori dewasa jika tersedia.
  - Rekomendasi: prioritas intervensi 30/60/90 hari, dukungan yang dibutuhkan.
  - Rencana aksi: PIC, milestone, indikator keberhasilan.
  - Lampiran: definisi indikator, sumber data, batasan.
- Pemetaan data ke slide:
  - Siswa terskrining: `distinctSiswa` dari `DashboardCkgSekolahController@index` (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:284)
  - Cakupan per sekolah: `cakupanPerSekolah` (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:185)
  - Status skrining: `totalNormal`, `totalPerlu`, `totalRujuk` (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:77)
  - Risiko utama: `persenResikoKategori` (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:306)
  - Antropometri ringkas: `ringkasanPemeriksaan.antropometri` (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:531)
- Implementasi halaman presentasi:
  - View: `resources/views/ilp/data-siswa-sekolah/PresentasiCkgSekolah.blade.php` berisi ringkasan eksekutif, grafik cakupan, status, risiko, antropometri.
  - Controller: tambah method `presentasi` yang menyajikan subset data untuk presentasi (app/Http/Controllers/ILP/DashboardCkgSekolahController.php:593).
  - Rute: `Route::get('/presentasi-ckg-sekolah', [DashboardCkgSekolahController::class,'presentasi'])->name('presentasi-ckg-sekolah')` (routes/web.php:391).
