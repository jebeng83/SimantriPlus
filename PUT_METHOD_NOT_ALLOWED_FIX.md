Perbaikan 405 Method Not Allowed untuk PUT (Industri Farmasi)

Ringkasan
- Masalah: Request update (PUT) untuk resource Industri Farmasi mengembalikan 405 Method Not Allowed.
- Penyebab umum: URL tanpa parameter, nama route Ziggy tidak lengkap, atau Laravel tidak mendeteksi metode PUT dari frontend.
- Solusi: Gunakan named route Ziggy yang benar dengan parameter, kirim request menggunakan method spoofing (POST + _method=PUT) dengan FormData, dan bersihkan cache Laravel.

Gejala
- Network menunjukkan request ke /farmasi/industri-farmasi (tanpa parameter) atau response 405 untuk PUT.
- Konsol Ziggy sebelumnya mengeluh: "route 'industri-farmasi.update' is not in the route list" (nama tidak lengkap dalam group farmasi).

Definisi Route yang Benar
- Nama: farmasi.industri-farmasi.update
- URI: PUT /farmasi/industri-farmasi/{kode_industri}

Verifikasi Cepat
1) List route terkait industri-farmasi:
   php artisan route:list --name=industri-farmasi

   Pastikan ada baris: PUT farmasi/industri-farmasi/{kode_industri}  farmasi.industri-farmasi.update

2) Regenerasi Ziggy (jika frontend menggunakan Ziggy):
   php artisan ziggy:generate

3) Bersihkan cache agar definisi terkini aktif:
   php artisan route:clear && php artisan config:clear && php artisan cache:clear && php artisan view:clear

Perbaikan di Frontend (Inertia + Ziggy)
- File: resources/js/Pages/farmasi/industrifarmasi.jsx
- Gunakan named route Ziggy dengan parameter, dan method spoofing via POST + _method=PUT dengan FormData.

Contoh implementasi submit update:

```jsx
// Pastikan kodeIndustri berisi nilai seperti "I0001"
const namedUpdateUrl = route('farmasi.industri-farmasi.update', { kode_industri: kodeIndustri });

// Kirim sebagai FormData agar Laravel mengenali _method=PUT
router.post(namedUpdateUrl, { ...data, _method: 'PUT' }, {
  forceFormData: true,
  preserveScroll: true,
  preserveState: false,
  onSuccess: () => {
    console.log('Update berhasil dengan method spoofing (POST + _method=PUT)');
    setModalOpen(false);
  },
  onError: (errors) => {
    console.error('Update gagal:', errors);
  }
});
```

Catatan:
- Jangan kirim ke base URL tanpa parameter ("/farmasi/industri-farmasi"). Gunakan URL berparameter: "/farmasi/industri-farmasi/I0001".
- Jika tetap ingin menggunakan PUT langsung, pastikan URL berparameter dan server tidak memblokir JSON PUT. Namun pendekatan aman dan konsisten di Laravel adalah method spoofing dengan FormData.

Contoh Controller (untuk referensi)
```php
// app/Http/Controllers/IndustriFarmasiController.php
public function update(Request $request, string $kode_industri)
{
    $validated = $request->validate([
        'nama_industri' => 'required|string',
        'alamat' => 'nullable|string',
        'kota' => 'required|string',
        'no_telp' => 'nullable|string',
    ]);

    $model = IndustriFarmasi::findOrFail($kode_industri);
    $model->update($validated);

    return back()->with('success', 'Data industri berhasil diperbarui');
}
```

Pengujian
1) Uji via DevTools Network saat mengubah kota menjadi "SOLO" untuk kode "I0001":
   - Request: POST ke /farmasi/industri-farmasi/I0001
   - Body: FormData berisi field data dan _method=PUT
   - Response: 200/302 tanpa 405

2) Uji via curl (langsung ke endpoint):
   - PUT langsung (opsi):
     curl -i -X PUT http://127.0.0.1:8000/farmasi/industri-farmasi/I0001 \
       -H "Accept: application/json" -H "Content-Type: application/json" \
       --data '{"nama_industri":"arasia","alamat":"","kota":"SOLO","no_telp":""}'

   - Spoofing via POST:
     curl -i -X POST http://127.0.0.1:8000/farmasi/industri-farmasi/I0001 \
       -H "Accept: application/json" \
       -F "nama_industri=arasia" -F "alamat=" -F "kota=SOLO" -F "no_telp=" -F "_method=PUT"

3) Verifikasi data di terminal (opsional):
   php -r "require 'vendor/autoload.php';\nuse App\\Models\\IndustriFarmasi;\n$m=IndustriFarmasi::find('I0001');\necho json_encode($m ? ['kode'=>$m->kode_industri,'kota'=>$m->kota] : null),"\n";"

Troubleshooting Jika Masih 405
- Pastikan namedUpdateUrl menghasilkan /farmasi/industri-farmasi/I0001.
- Pastikan selected.kode_industri terisi sebelum submit.
- Hard reload browser (Ctrl+Shift+R) setelah perubahan frontend.
- Pastikan CSRF token termuat (Inertia/Laravel default sudah menyertakan di form). Jika membuat request manual, sertakan X-CSRF-TOKEN.
- Cek middleware dan group route (prefix "farmasi") agar tidak ada konflik definisi.

Checklist Cepat
- [ ] route:list menunjukkan PUT farmasi/industri-farmasi/{kode_industri}
- [ ] Ziggy menggunakan nama farmasi.industri-farmasi.update dengan parameter {kode_industri}
- [ ] Frontend mengirim POST + _method=PUT dengan forceFormData
- [ ] Network menampilkan URL berparameter, bukan base route
- [ ] Cache Laravel dibersihkan

Dengan langkah-langkah di atas, error 405 untuk PUT pada Industri Farmasi akan teratasi dan alur edit berfungsi dengan benar.