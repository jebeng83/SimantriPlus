# Panduan Umum Pembuatan Menu Baru (React + Laravel)

Contoh kasus yang digunakan: Jadwal UKM (`resources/js/pages/MatrikKegiatanUkm/JadwalUkm.jsx`).
Dokumen ini memberikan langkah umum yang teruji agar proses penambahan menu baru tidak error dan menghindari halaman blank.

## Arsitektur Singkat
- Halaman Laravel (Blade) menyiapkan elemen root dan menyuntikkan URL API + CSRF via `data-*` attributes.
- `resources/js/app.jsx` akan mendeteksi elemen root dan melakukan dynamic import React page sesuai kebutuhan.
- Controller Laravel menyediakan endpoint API (list, meta, create, update, delete).
- React page membaca `data-*` dari root, melakukan fetch, dan merender UI.

## Prasyarat
- PHP + Composer terpasang dan dapat menjalankan Laravel (`php artisan serve`).
- Node.js + npm terpasang untuk Vite (`npm run dev`).
- Semua path dan nama file case-sensitive (khususnya di macOS/Linux).

## Langkah-Langkah

1) Tentukan Identitas Menu
- Slug URL halaman, mis. `/jadwal-ukm`.
- Nama file React, mis. `resources/js/pages/MatrikKegiatanUkm/JadwalUkm.jsx`.
- ID elemen root di Blade, mis. `jadwal-ukm-react-root`.
- Nama route API: `*.meta`, `*.data`, `*.store`, `*.update`, `*.destroy`.

2) Tambahkan Route Halaman dan API
- Di `routes/web.php` (dalam group `middleware(['web','loginauth'])`), buat route halaman dan API:

```php
// Halaman React
Route::get('/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'page'])
    ->name('jadwal-ukm.page');

// API Endpoints
Route::get('/api/jadwal-ukm/meta', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'meta'])
    ->name('jadwal-ukm.meta');
Route::get('/api/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'data'])
    ->name('jadwal-ukm.data');
Route::post('/api/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'store'])
    ->name('jadwal-ukm.store');
Route::put('/api/jadwal-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'update'])
    ->name('jadwal-ukm.update');
Route::delete('/api/jadwal-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'destroy'])
    ->name('jadwal-ukm.destroy');
```

3) Buat Controller
- Minimal menyediakan `page()` untuk mengembalikan Blade, serta endpoint `meta()`, `data()`, `store()`, `update()`, `destroy()`.
- Contoh pola (disederhanakan):

```php
class MyFeatureController extends Controller {
    protected string $table = 'my_table';
    public function page(Request $r) { return view('react.my_feature'); }
    public function meta(Request $r) { /* return struktur kolom & PK */ }
    public function data(Request $r) { /* return list data */ }
    public function store(Request $r) { /* validasi & insert */ }
    public function update(Request $r, $id) { /* validasi & update */ }
    public function destroy($id) { /* delete */ }
}
```

4) Buat Blade View Halaman
- Lokasi: `resources/views/react/<nama>.blade.php`.
- Pastikan menyertakan Vite dan membuat elemen root dengan ID yang unik.

```blade
@extends('adminlte::page')
@section('title', 'Jadwal UKM')
@section('css')
    @vite('resources/css/app.css')
    <style>#react-root{display:none!important}</style>
@endsection
@section('content')
<div class="container-fluid p-0">
  <div id="jadwal-ukm-react-root"
       data-meta-url="{{ route('jadwal-ukm.meta') }}"
       data-list-url="{{ route('jadwal-ukm.data') }}"
       data-store-url="{{ route('jadwal-ukm.store') }}"
       data-update-url-template="{{ route('jadwal-ukm.update', ['id' => '__ID__']) }}"
       data-delete-url-template="{{ route('jadwal-ukm.destroy', ['id' => '__ID__']) }}"
       data-csrf-token="{{ csrf_token() }}"
  ></div>
</div>
@endsection
@section('js')
<script>window.__vite_plugin_react_preamble_installed__ = true;</script>
@viteReactRefresh
@vite('resources/js/app.jsx')
@endsection
```

5) Tambahkan Mount Block di `resources/js/app.jsx`
- Deteksi root dan lakukan dynamic import komponen React.

```js
const jadwalUkmRoot = document.getElementById('jadwal-ukm-react-root');
if (jadwalUkmRoot) {
  import('./pages/MatrikKegiatanUkm/JadwalUkm.jsx')
    .then(({ default: JadwalUkm }) => {
      const root = createRoot(jadwalUkmRoot);
      root.render(<JadwalUkm />);
    })
    .catch((err) => {
      console.error('Gagal memuat JadwalUkm:', err);
      const root = createRoot(jadwalUkmRoot);
      root.render(null);
    });
}
```

6) Buat File React Page
- Lokasi sesuai import di atas.
- Bacalah `data-*` dari elemen root dan implementasikan UI.

```jsx
export default function JadwalUkm() {
  const root = document.getElementById('jadwal-ukm-react-root');
  const {
    metaUrl,
    listUrl,
    storeUrl,
    updateUrlTemplate,
    deleteUrlTemplate,
    csrfToken,
  } = root?.dataset || {};

  const [rows, setRows] = React.useState([]);
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState('');

  React.useEffect(() => {
    if (!listUrl) { setError('listUrl tidak tersedia'); return; }
    setLoading(true);
    fetch(listUrl)
      .then((r) => r.json())
      .then((json) => setRows(json?.data || []))
      .catch((e) => setError(e?.message || 'Gagal mengambil data'))
      .finally(() => setLoading(false));
  }, [listUrl]);

  if (error) return <div className="alert alert-danger">{error}</div>;
  return (
    <div className="container-fluid">
      {/* render tabel, filter, form, dsb. */}
      {loading ? 'Memuat...' : `Total: ${rows.length}`}
    </div>
  );
}
```

7) Tambahkan Link Menu di Sidebar (Opsional)
- Jika perlu tampil di sidebar statis: edit `resources/views/layouts/sidebar.blade.php`.

```blade
<li class="nav-item {{ request()->is('jadwal-ukm*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('jadwal-ukm.page') }}">
    <i class="fas fa-fw fa-calendar"></i>
    <span>Jadwal UKM</span>
  </a>
</li>
```

8) Jalankan Server untuk Preview
- Terminal 1: `php artisan serve --port=8001`
- Terminal 2: `npm install && npm run dev`
- Buka: `http://127.0.0.1:8001/jadwal-ukm`

## Checklist Anti Error / Halaman Blank
- ID elemen root di Blade sama persis dengan yang dicek di `app.jsx`.
- Path import di `app.jsx` benar dan case-sensitive (`JadwalUkm.jsx` vs `jadwalukm.jsx`).
- Komponen React memiliki `export default` dan mengembalikan satu elemen root (div) yang valid.
- Tidak ada JSX di luar fungsi komponen (hindari menempelkan blok JSX di luar `return`).
- Hooks (`useState`, `useEffect`, `useMemo`) hanya di level atas fungsi komponen (bukan di kondisi/loop).
- Blade menyertakan Vite: `@vite('resources/js/app.jsx')` dan preamble `window.__vite_plugin_react_preamble_installed__ = true;`.
- Semua route yang dipakai di Blade (`route('...')`) sudah didefinisikan di `routes/web.php`.
- Gunakan `console` dan Network tab untuk melihat error import, 404 API, atau CORS.

## Troubleshooting Cepat
- Error "Cannot find module": cek path import dan nama file.
- Halaman blank tanpa error: cek ID root, pastikan mount block dieksekusi (tambahkan `console.log`).
- 405/404 saat fetch: cek method (GET/POST/PUT/DELETE) dan pastikan route sesuai.
- Validasi gagal saat `store/update`: baca pesan validasi dari backend dan sesuaikan form.

## Catatan dari Kasus Jadwal UKM
- Blade `resources/views/react/jadwal_ukm.blade.php` menyuntikkan:
  - `data-meta-url`, `data-list-url`, `data-store-url`, `data-update-url-template`, `data-delete-url-template`, `data-csrf-token`.
- `app.jsx` memount komponen di ID `jadwal-ukm-react-root` dengan dynamic import.
- React page mengimplementasikan filter dan pagination menggunakan `useMemo` + `useEffect` untuk performa dan menjaga UI stabil.
- Pastikan tidak menduplikasi elemen/komponen di luar scope komponen (ini bisa menyebabkan error JSX dan halaman blank).

Dengan mengikuti pola di atas, pembuatan menu baru akan konsisten, minim error, dan mudah dipelihara untuk fitur berikutnya.