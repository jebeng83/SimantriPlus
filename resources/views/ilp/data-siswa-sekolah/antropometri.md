# Ringkasan Antropometri Fields (IMT)

## Alur Data
- Query dasar dibangun di method presentasi untuk periode yang dipilih, join ke sekolah/kelas/pasien, dan menerapkan filter request (tanggal, sekolah, jenis, kelas).
- Hasil agregasi untuk dropdown kategori dikemas dalam `kategoriAnalisa` dan dioper ke view `PresentasiCkgSekolah.blade.php` melalui atribut data `presentasiData`.

## Perhitungan IMT (enum)
- IMT di `skrining_siswa_sd.imt` disimpan sebagai enum teks: `Gizi Buruk`, `Gizi Kurang`, `Gizi Baik`, `Berisiko gizi lebih`, `Gizi Lebih`, `Obesitas`.
- Controller menghitung jumlah baris untuk tiap label enum, tanpa ambang angka dan tanpa distinct siswa.
- Lokasi kode:
  - Bangun items IMT dan summary: `app/Http/Controllers/ILP/DashboardCkgSekolahController.php:906–923`
  - Persen risiko IMT (non-`Gizi Baik`): `app/Http/Controllers/ILP/DashboardCkgSekolahController.php:952–965`

## Render di View
- Dropdown kategori membaca `kategoriAnalisa` dan menampilkan grafik.
- Khusus `antropometri_fields`, grafik memakai seri `Jumlah` dari `countData` (bukan Ya/Tidak).
- Sumbu-Y disetel ke skala integer berdasarkan nilai maksimum agar angka tampil sebagai 1,2,3,...
- Lokasi kode:
  - Seri `Jumlah` dan y-axis integer: `resources/views/ilp/data-siswa-sekolah/PresentasiCkgSekolah.blade.php:411–417`
  - Label ramah pengguna untuk IMT: `resources/views/ilp/data-siswa-sekolah/PresentasiCkgSekolah.blade.php:381–389`
  - Injeksi data ke view: `resources/views/ilp/data-siswa-sekolah/PresentasiCkgSekolah.blade.php:260–269`

## Diagnostik ketika grafik hanya menampilkan 1 bar
- Biasanya karena data terfilter (tanggal/sekolah/kelas) sehingga hanya ada satu baris IMT dengan nilai `Gizi Baik` pada periode tersebut.
- Pastikan periode cukup lebar atau filter dikosongkan untuk melihat distribusi penuh.
- Validasi cepat:
  - Jalankan query hitung enum langsung pada database sesuai filter yang aktif untuk memastikan jumlah tiap kategori.

## Catatan Penggunaan
- Grafik menampilkan angka sesuai filter halaman. Untuk menyamakan dengan uji terminal, gunakan periode dan filter yang sama.
