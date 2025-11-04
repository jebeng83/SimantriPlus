# Panduan Lengkap Mengatasi Error "@vitejs/plugin-react can't detect preamble"

Dokumen ini merangkum akar masalah, gejala, arsitektur yang disarankan, langkah perbaikan, dan checklist verifikasi untuk lingkungan Laravel + Vite + React + AdminLTE yang menggunakan Blade.

## Ringkasan Masalah

- Plugin `@vitejs/plugin-react` membutuhkan skrip pendahuluan (preamble) React Fast Refresh sebelum modul React lain dievaluasi saat development.
- Bila `app.jsx` dieksekusi sebelum preamble, atau `app.jsx` dimuat lebih dari sekali pada halaman yang sama, maka muncul error: `@vitejs/plugin-react can't detect preamble` dan React modul bisa gagal import/mount.

## Gejala Umum

- Console menampilkan error seperti:
  - `@vitejs/plugin-react can't detect preamble`
  - `HelloMotion failed to load`, `FarmasiIndex failed to load`, dsb.
- Komponen React tidak termuat atau termuat ganda (double mount), terutama saat navigasi antar halaman AdminLTE.

## Arsitektur yang Disarankan

1. Hanya layout master (misal `adminlte::master`) yang boleh memuat `@vite('resources/js/app.jsx')`. Halaman modul TIDAK perlu memuat app.jsx lagi.
2. Gunakan `@viteReactRefresh` hanya pada environment development (local), dan letakkan di master. Halaman standalone (tanpa AdminLTE) boleh memanggil `@viteReactRefresh` sendiri.
3. Sediakan fallback preamble di master untuk berjaga-jaga jika directive `@viteReactRefresh` tidak mengeksekusi urut.
4. Di `app.jsx`, tambahkan guard ringan (shim) dan fungsi `waitForPreamble()` agar import dinamis aman jika preamble terlambat.
5. Nonaktifkan `fastRefresh` sementara jika urutan eksekusi sering bermasalah di environment dev.

## Perubahan yang Diterapkan di Proyek Ini

- Layout master `resources/views/vendor/adminlte/master.blade.php`:
  - Memanggil `@viteReactRefresh` di dev.
  - Menambahkan fallback untuk mengimpor `/@vite/client` dan `/@react-refresh` dari base dev (`http://127.0.0.1:5174` atau `http://localhost:5174`) saat `public/hot` tidak tersedia.
  - Menginjeksi `@vite(['resources/js/app.jsx'])` SEKALI secara global.
- Halaman AdminLTE (Farmasi, PCare, Mobile JKN, ePPBGM, Laporan, ILP, Reg Periksa, dsb.)
  - Menghapus `@vite('resources/js/app.jsx')` per-halaman untuk mencegah duplikasi.
- Halaman React standalone (mis. `react/antri-poli.blade.php`, `react/antrian-display.blade.php`)
  - Memakai `@viteReactRefresh` (hanya dev) diikuti satu `@vite('resources/js/app.jsx')`.
- `vite.config.js`
  - Menetapkan `fastRefresh: false` di plugin React.
- `resources/js/app.jsx`
  - Menambahkan shim preamble dan `waitForPreamble()` untuk menjaga import dinamis saat dev.

## Potongan Kode Rujukan

### 1) Master Layout (dev preamble + fallback + global app.jsx)

```blade
{{-- resources/views/vendor/adminlte/master.blade.php --}}
@if(app()->environment('local', 'development'))
    @viteReactRefresh
    {{-- Fallback ketika public/hot tidak ada --}}
    <script type="module">
        const candidates = [
            'http://127.0.0.1:5174',
            'http://localhost:5174',
        ];
        let injected = false;
        for (const base of candidates) {
            try {
                await import(base + '/@vite/client');
                await import(base + '/@react-refresh');
                window.__vite_plugin_react_preamble_installed__ = true;
                window.$RefreshReg$ = window.$RefreshReg$ || (() => {});
                window.$RefreshSig$ = window.$RefreshSig$ || ((type) => type);
                injected = true; break;
            } catch (e) { /* abaikan dan coba kandidat berikutnya */ }
        }
    </script>
@endif

@vite(['resources/js/app.jsx'])
```

### 2) Halaman Modul AdminLTE (tanpa `@vite('app.jsx')` per-halaman)

```blade
{{-- Contoh: resources/views/farmasi/satuan-barang.blade.php --}}
@extends('adminlte::page')

@section('css')
    @vite('resources/css/app.css')
    <style>#react-root { display:none !important; }</style>
@endsection

@section('content')
    <div id="farmasi-satuan-barang-root" class="mt-2"></div>
@endsection

@section('js')
    {{-- Tidak ada pemanggilan app.jsx di sini --}}
@endsection
```

### 3) Halaman Standalone (boleh panggil app.jsx sendiri)

```blade
<!doctype html>
<html>
<head>
  <title>Halaman React Standalone</title>
  @if(app()->environment('local', 'development'))
    @viteReactRefresh
  @endif
  @vite('resources/js/app.jsx')
</head>
<body>
  <div id="antri-poli-root"></div>
</body>
</html>
```

### 4) Konfigurasi Vite (disable fast refresh sementara)

```js
// vite.config.js
import react from '@vitejs/plugin-react';
export default defineConfig({
  server: { host: '0.0.0.0', port: 5174, hmr: { host: '127.0.0.1', port: 5174 } },
  plugins: [
    react({ fastRefresh: false })
  ],
});
```

### 5) Guard di app.jsx (shim + waitForPreamble)

```jsx
// resources/js/app.jsx (cuplikan)
const isProdBuild = import.meta?.env?.PROD || false;
if (!isProdBuild && typeof window !== 'undefined') {
  if (!window.__vite_plugin_react_preamble_installed__) {
    window.__vite_plugin_react_preamble_installed__ = true;
    window.$RefreshReg$ ||= () => {};
    window.$RefreshSig$ ||= () => (type) => type;
  }
}

const waitForPreamble = () => {
  if (isProdBuild || window.__vite_plugin_react_preamble_installed__) return Promise.resolve();
  return new Promise((resolve) => {
    let attempts = 0, max = 60;
    const tick = () => {
      if (window.__vite_plugin_react_preamble_installed__) return resolve();
      if (++attempts >= max) { window.__vite_plugin_react_preamble_installed__ = true; return resolve(); }
      setTimeout(tick, 50);
    };
    setTimeout(tick, 0);
  });
};

const importIfPreamble = (path) => waitForPreamble().then(() => import(/* @vite-ignore */ path));
```

## Langkah Perbaikan & Operasional

1. Pastikan hanya master layout yang memuat `app.jsx`.
   - Cari duplikasi: `grep -R "@vite('resources/js/app.jsx')" resources/views`
   - Hapus dari modul yang extend `adminlte::page`.
2. Jalankan dev server Vite dan pastikan port 5174 kosong:
   - Cek: `lsof -i :5174`
   - Bila terpakai, hentikan proses: `kill -9 <PID>`
   - Mulai: `npm run dev`
3. Pastikan server PHP/Laravel berjalan di 8000 (atau sesuai konfigurasi).
4. Bersihkan cache bila perlu:
   - `php artisan optimize:clear`
   - Hard reload di browser (Shift+Reload).
5. Pastikan tidak ada Service Worker aktif saat development (kode di master sudah menonaktifkan dan membersihkan cache).

## Verifikasi

- Buka `http://127.0.0.1:8000/home`:
  - Console harus menampilkan `[app.jsx] loaded`.
  - Tidak ada error `can't detect preamble`.
- Buka halaman modul (Farmasi, PCare, JKN, ePPBGM, Laporan, ILP):
  - Komponen React termuat sesuai elemen rootnya.
  - Sidebar React hanya mount sekali.
- Halaman standalone:
  - `react/antri-poli` dan `react/antrian-display` berfungsi dengan `@viteReactRefresh` saat dev.

## Troubleshooting Lanjutan

- Dev server tidak terdeteksi dari Blade:
  - Pastikan Vite berjalan di `http://localhost:5174/` atau `http://127.0.0.1:5174/`.
  - Sesuaikan `vite.config.js` → `server.hmr.host` dan `server.port`.
- Di belakang proxy / menggunakan HTTPS:
  - Sesuaikan base dev untuk fallback (contoh: `https://dev.local:5174`).
- Versi paket:
  - `vite@7.x`, `@vitejs/plugin-react@5.x`, `laravel-vite-plugin@2.x` telah diuji.

## Prinsip Penting

- Satu halaman → satu pemanggilan `app.jsx` saja (dari master bila memakai AdminLTE).
- `@viteReactRefresh` hanya di development.
- Gunakan fallback preamble hanya sebagai jaring pengaman, bukan pengganti utama.

---

Jika setelah mengikuti panduan ini error masih muncul, catat:
- URL halaman dan layout yang digunakan.
- Log console lengkap.
- Apakah Vite dev server dapat diakses langsung di browser.
Kirimkan informasi tersebut agar analisis lanjutan bisa dilakukan.