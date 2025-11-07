# Mengatasi SyntaxError: "Unexpected token '&'" pada halaman Reg Periksa

Dokumen ini merangkum gejala, penyebab umum, langkah diagnostik, dan perbaikan yang telah diterapkan untuk mengatasi error JavaScript:

```
SyntaxError: Unexpected token '&'
```

Error ini biasanya muncul ketika `JSON.parse()` mencoba mem-parsing string yang mengandung HTML entities (mis. `&quot;`, `&amp;`) atau ketika respons server bukan JSON murni, melainkan HTML atau JSON yang ter-escape.

## Ringkasan Gejala di Proyek

- Konsol browser melaporkan `SyntaxError: Unexpected token '&'` pada halaman `/reg-periksa` (sebelumnya terindikasi di sekitar baris 203:33 dan 233:28 dari HTML hasil render).
- Error tetap muncul meskipun skrip navigasi (`navigation-handler.js`) sempat dinonaktifkan.
- Ada beberapa titik di aplikasi yang memanggil `JSON.parse()` atau `response.json()` terhadap konten yang berpotensi bukan JSON murni.

## Penyebab Umum

- `JSON.parse()` diberikan string yang ter-escape HTML (contoh: `{"a": &quot;b&quot;}`) sehingga karakter `&` menjadi token tidak valid.
- Respons Fetch (`response.text()`) diperlakukan sebagai JSON padahal `content-type` bukan `application/json`.
- Nilai `localStorage` yang digunakan library pihak ketiga (contoh: AdminLTE IFrame) rusak atau berisi HTML-escaped JSON.
- Injeksi JSON di Blade yang menghasilkan konten ter-escape di dalam tag `<script>`.

## Diagnostik yang Dilakukan

1. Menambahkan instrumentation di layout utama untuk:
   - Menangkap kegagalan `JSON.parse` dan menampilkan potongan input (200 karakter pertama).
   - Menangkap error global (`window.addEventListener('error', ...)`) untuk mengetahui sumber dan stack error.
2. Menonaktifkan sementara skrip tertentu (navigation handler dan preamble manual Vite React Refresh) untuk isolasi.
3. Memeriksa titik-titik yang melakukan parsing JSON dari:
   - `localStorage` (khususnya `AdminLTE:IFrame:Options`).
   - Tag `<script type="application/json">` yang berisi data menu/topnav.
   - Pemanggilan Fetch di React yang sebelumnya mengandalkan langsung `response.json()`.

## Perbaikan yang Diterapkan

### 1) Sanitasi nilai localStorage AdminLTE IFrame

Sebelum AdminLTE dipanggil, nilai `localStorage` `AdminLTE:IFrame:Options` disanitasi agar tidak menyebabkan `JSON.parse` error.

```html
<script>
(function () {
  try {
    var key = 'AdminLTE:IFrame:Options';
    var raw = localStorage.getItem(key);
    if (!raw) return;
    try {
      JSON.parse(raw);
      return; // OK
    } catch (_) {
      var div = document.createElement('div');
      div.innerHTML = raw; // decode HTML entities
      var decoded = div.textContent || div.innerText || raw;
      try {
        var obj = JSON.parse(decoded);
        localStorage.setItem(key, JSON.stringify(obj));
        console.warn('[AdminLTE:IFrame] Sanitized invalid localStorage value');
      } catch (e2) {
        localStorage.setItem(key, '{}');
        console.warn('[AdminLTE:IFrame] Replaced invalid localStorage value with safe default {}:', e2);
      }
    }
  } catch (e) {}
})();
</script>
```

Letak: `resources/views/vendor/adminlte/master.blade.php`

### 2) Menggunakan helper `safeJson` untuk semua Fetch

Alih-alih memanggil `response.json()` secara langsung, seluruh pemanggilan Fetch di halaman `/reg-periksa` kini melewati helper `safeJson`. Helper ini:
- Mengecek `content-type` respons.
- Jika JSON valid, mengembalikan hasilnya.
- Jika bukan JSON, log peringatan berisi URL, status, `content-type`, dan 120 karakter pertama isi respons.
- Jika parsing gagal, mengembalikan objek kesalahan yang informatif.

Cuplikan helper (disederhanakan):

```js
const safeJson = async (response) => {
  try {
    const ct = response.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { return await response.json(); }
      catch (e) {
        const txt = await response.text();
        return { error: true, message: 'Invalid JSON response', status: response.status, url: response.url, contentType: ct, preview: (txt||'').slice(0,120), raw: txt };
      }
    }
    const text = await response.text();
    const preview = (text || '').slice(0, 120);
    console.warn('[safeJson] Non-JSON received from', response.url, 'status:', response.status, 'ct:', ct, '\nPreview:', preview);
    try { return JSON.parse(text); }
    catch (_) { return { error: true, message: 'Non-JSON response', status: response.status, url: response.url, contentType: ct, preview, raw: text }; }
  } catch (err) {
    return { error: true, message: 'safeJson unexpected error', url: response?.url || '', status: response?.status || 0 };
  }
};
```

Contoh penggunaan pada Fetch:

```js
fetch('/api/antripendaftaran/stats?date=2025-01-01', {
  headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
  cache: 'no-store'
}).then((r) => safeJson(r));
```

Letak: `resources/js/pages/reg_periksa/index.jsx`

Perubahan utama:
- Mengganti semua titik yang sebelumnya memanggil langsung `response.json()` menjadi `.then((r) => safeJson(r))` atau `safeJson(await fetch(...))`.
- Menambahkan header `Accept: 'application/json'` dan `X-Requested-With: 'XMLHttpRequest'`, serta `cache: 'no-store'` untuk mencegah cache dev dan memperjelas intent JSON.
- Mencakup endpoint BPJS (`bpjsAddAntrean`, `bpjsCreateAntrean`) dan antripendaftaran (`antriNext`, `antriStats`, `antriCall`, `antriRecall`), serta loader wilayah (`/propinsi`, `/kabupaten`, `/kecamatan`, `/kelurahan`).

### 3) Vite React Refresh dan AdminLTE

- Preamble manual Vite React Refresh dinonaktifkan sementara; kita mengandalkan `@viteReactRefresh` yang sudah memadai ketika dev server aktif.
- Setelah perbaikan, AdminLTE core JS (`adminlte.min.js`) diaktifkan kembali dan diverifikasi tidak memunculkan error.
- Include plugin AdminLTE (jika ada) tetap dinonaktifkan sementara untuk isolasi; dapat diaktifkan kembali setelah verifikasi final.

Letak: `resources/views/vendor/adminlte/master.blade.php`

## Praktik Terbaik (Best Practices)

1. Embed JSON di Blade dengan aman:
   - Gunakan tag `<script type="application/json">` dan pastikan JSON tidak di-escape HTML.
   - Disarankan menggunakan `@json($data)` atau `{!! json_encode($data, JSON_UNESCAPED_UNICODE) !!}` untuk mencegah entity seperti `&quot;`.
   - Saat membaca kembali di client, gunakan `textContent` dan `JSON.parse`, serta fallback decoding jika diperlukan.

2. Periksa `content-type` sebelum parsing:
   - Hanya panggil `response.json()` jika `content-type` berisi `application/json`.
   - Bila ragu, gunakan pola `safeJson` di atas.

3. Hindari konflik bundle:
   - Jangan memuat `public/js/app.js` (Laravel Mix legacy) bersamaan dengan bundle Vite di mode dev.
   - Pastikan tidak ada Service Worker aktif saat pengembangan (HMR dapat terganggu).

4. Bersihkan data tersimpan yang corrupt:
   - Jika error kembali muncul, hapus `localStorage` key: `AdminLTE:IFrame:Options` (atau biarkan sanitizer memperbaikinya).

## Checklist Debug "Unexpected token '&'"

- [ ] Tangkap error `JSON.parse` (instrumentation) dan cek input preview di konsol.
- [ ] Pastikan semua Fetch memakai `safeJson` + header `Accept: 'application/json'` + `X-Requested-With`.
- [ ] Sanitasi `localStorage` untuk AdminLTE IFrame sebelum memuat `adminlte.min.js`.
- [ ] Tinjau setiap `<script type="application/json">` di Blade: pastikan JSON tidak HTML-escaped.
- [ ] Nonaktifkan sementara skrip non-esensial (navigation handler, plugin) untuk isolasi, lalu aktifkan kembali satu per satu.
- [ ] Verifikasi di `/reg-periksa` bahwa konsol bebas dari error.

## Verifikasi Hasil

- Setelah perbaikan, halaman `/reg-periksa` dimuat tanpa error dan konsol bersih.
- AdminLTE core JS telah diaktifkan kembali dengan aman.
- Jika ada endpoint yang masih mengembalikan HTML/non-JSON, konsol akan menampilkan log `[safeJson] Non-JSON received ...` beserta preview untuk memudahkan perbaikan server-side.

## Jika Error Muncul Kembali

1. Buka konsol browser dan cari log dari `safeJson` untuk melihat endpoint mana yang mengembalikan non-JSON atau JSON tidak valid.
2. Perbaiki endpoint tersebut agar:
   - Mengembalikan JSON murni (tanpa HTML wrapping/escaping).
   - Menetapkan header `Content-Type: application/json` dengan benar.
3. Periksa kembali injeksi JSON di Blade yang mungkin ter-escape.
4. Bersihkan `localStorage` terkait AdminLTE jika perlu.
5. Aktifkan kembali instrumentation sementara untuk menelusuri sumber input yang menyebabkan error.

## Catatan

- Untuk pengembangan, pastikan Service Worker non-aktif agar Vite HMR stabil.
- Setelah masalah benar-benar teratasi, instrumentation `JSON.parse` di layout dapat dihapus untuk mengurangi noise di konsol.

---

Referensi file terkait:
- `resources/views/vendor/adminlte/master.blade.php`
- `resources/js/pages/reg_periksa/index.jsx`
- `resources/js/app.jsx` (memuat aplikasi React secara global dari layout `adminlte::master`)