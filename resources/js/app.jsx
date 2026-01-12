import './bootstrap';
console.log('[app.jsx] loaded');
import React from 'react';
import { createRoot } from 'react-dom/client';
// import HelloMotion from './components/HelloMotion.jsx';
// import RegisterHeader from './components/RegisterHeader.jsx';
// import PcareStatusHeader from './pages/PcareStatusHeader.jsx';
// import IlpMenu from './pages/ilp/index.jsx';
// import PcareMenu from './pages/Pcare/index.jsx';
// import MobileJknHome from './pages/Mobile-Jkn/home.jsx';
// import EppbgmMenu from './pages/Eppbgm/index.jsx';
// import LaporanMenu from './pages/Laporan/index.jsx';
// import RegPeriksaPage from './pages/reg_periksa/index.jsx';
import '../css/app.css'
// import LoketDisplay from './pages/Display/index.jsx'
// import FarmasiIndex from './pages/Farmasi/index.jsx'
// Farmasi pages are dynamically imported in their mount blocks to avoid bundling heavy/optional dependencies.

// Guard: allow dynamic import in production build even if Vite React preamble is not present.
const isProdBuild = (typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.PROD) || false;
const preambleReady = !!window.__vite_plugin_react_preamble_installed__;

// DEV hardening: proactively set the preamble flag very early to avoid
// "@vitejs/plugin-react can't detect preamble" guard throwing during initial evaluation
// when script ordering is racy. This is safe in development; in production the flag is ignored.
if (typeof window !== 'undefined' && !isProdBuild) {
  // If the flag hasn't been set by @viteReactRefresh yet, set it preemptively.
  if (!window.__vite_plugin_react_preamble_installed__) {
    window.__vite_plugin_react_preamble_installed__ = true;
    // Provide no-op refresh stubs so transformed modules won't crash if preamble script is late
    if (!window.$RefreshReg$) window.$RefreshReg$ = () => {};
    if (!window.$RefreshSig$) window.$RefreshSig$ = () => (type) => type;
    // Optional diagnostics to help confirm the fix works
    console.debug('[app.jsx] Dev preamble flag shim applied early');
  }
}

// Wait until Vite React preamble is installed before importing React modules (dev only).
// In some environments, the module evaluation order can cause app.jsx to run before the preamble.
// We poll briefly to ensure the preamble flag is set. As a last resort after timeout, we shim the flag
// so @vitejs/plugin-react doesn't throw. This does not affect production builds.
const waitForPreamble = () => {
  if (isProdBuild || window.__vite_plugin_react_preamble_installed__) return Promise.resolve();
  return new Promise((resolve) => {
    let attempts = 0;
    const maxAttempts = 60; // ~3s total with 50ms interval (more robust on slow starts)
    const tick = () => {
      if (window.__vite_plugin_react_preamble_installed__) {
        resolve();
        return;
      }
      attempts += 1;
      if (attempts >= maxAttempts) {
        // Last resort: shim the flag to avoid plugin-react guard crash
        window.__vite_plugin_react_preamble_installed__ = true;
        resolve();
        return;
      }
      setTimeout(tick, 50);
    };
    setTimeout(tick, 0);
  });
};

// Import helper: ensure preamble is ready before loading the React module in dev
// Use import.meta.glob to allow Vite to statically analyze and bundle dynamic imports.
// This fixes production builds (no more attempts to load raw .jsx files under /build/assets).
const JSX_MODULES = import.meta.glob('./**/*.jsx');
const importIfPreamble = (path) =>
  waitForPreamble().then(() => {
    const loader = JSX_MODULES[path];
    if (!loader) throw new Error(`Module not found for dynamic import: ${path}`);
    return loader();
  });

// Mount React app only if the root element exists
const rootElement = document.getElementById('react-root');
if (rootElement) {
  const root = createRoot(rootElement);
  importIfPreamble('./components/HelloMotion.jsx')
    .then(({ default: HelloMotion }) => {
      root.render(<HelloMotion />);
    })
    .catch((err) => {
      console.info('HelloMotion failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount motion-enhanced Register header if present
const registerRoot = document.getElementById('register-react-root');
if (registerRoot) {
  const root = createRoot(registerRoot);
  const { totalPasien = '0', belumPeriksa = '0', date = '' } = registerRoot.dataset;
  importIfPreamble('./components/RegisterHeader.jsx')
    .then(({ default: RegisterHeader }) => {
      root.render(
        <RegisterHeader
          totalPasien={parseInt(totalPasien, 10) || 0}
          belumPeriksa={parseInt(belumPeriksa, 10) || 0}
          date={date}
        />
      );
    })
    .catch((err) => {
      console.warn('RegisterHeader failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
    });
}

// Mount PCare Status header with motion
const pcareRoot = document.getElementById('pcare-status-react-root');
if (pcareRoot) {
  const root = createRoot(pcareRoot);
  importIfPreamble('./pages/PcareStatusHeader.jsx')
    .then(({ default: PcareStatusHeader }) => {
      // Render React header
      root.render(<PcareStatusHeader />);

      // Tandai bahwa komponen React untuk summary PCare sudah aktif
      // dan sembunyikan fallback non-React agar tidak menampilkan informasi ganda.
      try {
        window.PCARE_STATUS_REACT_READY = true;
        const fb = document.getElementById('pcare-summary-fallback');
        if (fb) {
          fb.style.display = 'none';
          fb.setAttribute('aria-hidden', 'true');
        }
      } catch (e) {
        console.debug('[PCare] Tidak bisa menyembunyikan fallback summary:', e?.message ?? e);
      }
    })
    .catch((err) => {
      console.warn('PcareStatusHeader failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
    });
}

// Initialize PCare Status jQuery/DataTables logic when the table exists on the page.
// We wait until the DataTables plugin is loaded (provided via Blade includes) before running.
const pcareStatusTableEl = document.getElementById('tabel-status-pcare');
if (pcareStatusTableEl) {
  const waitForDataTables = () => new Promise((resolve) => {
    const check = () => {
      const hasDT = !!(window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable);
      if (hasDT) return resolve();
      setTimeout(check, 50);
    };
    check();
  });

  importIfPreamble('./pages/Pcare/statusPendaftaranInit.jsx')
    .then(({ default: initStatusPendaftaran }) => {
      return waitForDataTables().then(() => {
        try {
          initStatusPendaftaran();
        } catch (e) {
          console.error('[PCare Status] Failed to initialize page script:', e?.message ?? e);
        }
      });
    })
    .catch((err) => {
      console.warn('[PCare Status] Failed to load statusPendaftaranInit.jsx', err?.message ?? err);
    });
}

// Mount ILP Menu if present
const ilpMenuRoot = document.getElementById('ilp-menu-root');
if (ilpMenuRoot) {
  const root = createRoot(ilpMenuRoot);
  importIfPreamble('./pages/ilp/index.jsx')
    .then(({ default: IlpMenu }) => {
      root.render(<IlpMenu />);
    })
    .catch((err) => {
      console.info('IlpMenu failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount PCare Menu if present
const pcareMenuRoot = document.getElementById('pcare-menu-root');
if (pcareMenuRoot) {
  const root = createRoot(pcareMenuRoot);
  importIfPreamble('./pages/Pcare/index.jsx')
    .then(({ default: PcareMenu }) => {
      root.render(<PcareMenu />);
    })
    .catch((err) => {
      console.info('PcareMenu failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount Mobile JKN Antrol menu if present
const mobileJknHomeRoot = document.getElementById('mobile-jkn-home-root');
if (mobileJknHomeRoot) {
  const root = createRoot(mobileJknHomeRoot);
  importIfPreamble('./pages/Mobile-Jkn/home.jsx')
    .then(({ default: MobileJknHome }) => {
      root.render(<MobileJknHome />);
    })
    .catch((err) => {
      console.info('MobileJknHome failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount ePPBGM Menu if present
const eppbgmMenuRoot = document.getElementById('eppbgm-menu-root');
if (eppbgmMenuRoot) {
  const root = createRoot(eppbgmMenuRoot);
  importIfPreamble('./pages/Eppbgm/index.jsx')
    .then(({ default: EppbgmMenu }) => {
      root.render(<EppbgmMenu />);
    })
    .catch((err) => {
      console.info('EppbgmMenu failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount Laporan Menu if present
const laporanMenuRoot = document.getElementById('laporan-menu-root');
if (laporanMenuRoot) {
  const root = createRoot(laporanMenuRoot);
  importIfPreamble('./pages/Laporan/index.jsx')
    .then(({ default: LaporanMenu }) => {
      root.render(<LaporanMenu />);
    })
    .catch((err) => {
      console.info('LaporanMenu failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount Reg Periksa Page if present
const regPeriksaRoot = document.getElementById('reg-periksa-root');
if (regPeriksaRoot) {
  const root = createRoot(regPeriksaRoot);
  importIfPreamble('./pages/reg_periksa/index.jsx')
    .then(({ default: RegPeriksaPage }) => {
      root.render(<RegPeriksaPage />);
    })
    .catch((err) => {
      console.warn('RegPeriksaPage failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
    });
}

// Mount Loket Display React page if present
const loketDisplayRoot = document.getElementById('loket-display-root');
if (loketDisplayRoot) {
  const root = createRoot(loketDisplayRoot);
  importIfPreamble('./pages/Display/index.jsx')
    .then(({ default: LoketDisplay }) => {
      root.render(<LoketDisplay />);
    })
    .catch((err) => {
      console.warn('LoketDisplay failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
    });
}

// Mount Farmasi Permintaan Medis page if present
const permintaanMedisRoot = document.getElementById('farmasi-permintaan-root');
if (permintaanMedisRoot) {
  const root = createRoot(permintaanMedisRoot);
  importIfPreamble('./pages/Farmasi/PermintaanMedis.jsx')
    .then(({ default: PermintaanMedis }) => {
      root.render(<PermintaanMedis />);
    })
    .catch((err) => {
      console.info('PermintaanMedis failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount Farmasi Index page if present
const farmasiIndexRoot = document.getElementById('farmasi-index-root');
if (farmasiIndexRoot) {
  console.log('[Farmasi] Mounting Farmasi Index at #farmasi-index-root');
  const root = createRoot(farmasiIndexRoot);
  importIfPreamble('./pages/Farmasi/index.jsx')
    .then(({ default: FarmasiIndex }) => {
      root.render(<FarmasiIndex />);
    })
    .catch((err) => {
      console.info('FarmasiIndex failed to load:', err?.message ?? err);
      root.render(null);
    });
}

// Mount Farmasi Dashboard page if present
const farmasiDashboardRoot = document.getElementById('farmasi-dashboard-root');
if (farmasiDashboardRoot) {
  const root = createRoot(farmasiDashboardRoot);
  importIfPreamble('./pages/Farmasi/Dashboard.jsx')
    .then(({ default: FarmasiDashboard }) => {
      root.render(<FarmasiDashboard />);
    })
    .catch((err) => console.error('Failed to load Farmasi Dashboard', err));
}

// Mount Industri Farmasi page if present
const industriFarmasiRoot = document.getElementById('farmasi-industri-farmasi-root');
if (industriFarmasiRoot) {
  const root = createRoot(industriFarmasiRoot);
  importIfPreamble('./pages/Farmasi/industrifarmasi.jsx')
    .then(({ default: IndustriFarmasi }) => {
      root.render(<IndustriFarmasi />);
    })
    .catch((err) => console.error('Failed to load Industri Farmasi', err));
}

// Mount Data Suplier page if present
const dataSuplierRoot = document.getElementById('farmasi-data-suplier-root');
if (dataSuplierRoot) {
  const root = createRoot(dataSuplierRoot);
  importIfPreamble('./pages/Farmasi/datasuplier.jsx')
    .then(({ default: DataSuplier }) => {
      root.render(<DataSuplier />);
    })
    .catch((err) => console.error('Failed to load Data Suplier', err));
}

// Mount Satuan Barang page if present
const satuanBarangRoot = document.getElementById('farmasi-satuan-barang-root');
if (satuanBarangRoot) {
  console.log('[Farmasi] Mounting SatuanBarang at #farmasi-satuan-barang-root');
  const root = createRoot(satuanBarangRoot);
  importIfPreamble('./pages/Farmasi/SatuanBarang.jsx')
    .then(({ default: SatuanBarang }) => {
      root.render(<SatuanBarang />);
    })
    .catch((err) => console.error('Failed to load Satuan Barang', err));
}

// Mount Metode Racik page if present
const metodeRacikRoot = document.getElementById('farmasi-metode-racik-root');
if (metodeRacikRoot) {
  const root = createRoot(metodeRacikRoot);
  importIfPreamble('./pages/Farmasi/MetodeRacik.jsx')
    .then(({ default: MetodeRacik }) => {
      root.render(<MetodeRacik />);
    })
    .catch((err) => console.error('Failed to load Metode Racik', err));
}

// Mount Konversi Satuan page if present
const konversiSatuanRoot = document.getElementById('farmasi-konversi-satuan-root');
if (konversiSatuanRoot) {
  const root = createRoot(konversiSatuanRoot);
  importIfPreamble('./pages/Farmasi/KonversiSatuan.jsx')
    .then(({ default: KonversiSatuan }) => {
      root.render(<KonversiSatuan />);
    })
    .catch((err) => console.error('Failed to load Konversi Satuan', err));
}

// Mount Jenis Obat page if present
const jenisObatRoot = document.getElementById('farmasi-jenis-obat-root');
if (jenisObatRoot) {
  const root = createRoot(jenisObatRoot);
  importIfPreamble('./pages/Farmasi/JenisObat.jsx')
    .then(({ default: JenisObat }) => {
      root.render(<JenisObat />);
    })
    .catch((err) => console.error('Failed to load Jenis Obat', err));
}

// Mount Kategori Obat page if present
const kategoriObatRoot = document.getElementById('farmasi-kategori-obat-root');
if (kategoriObatRoot) {
  const root = createRoot(kategoriObatRoot);
  importIfPreamble('./pages/Farmasi/KategoriObat.jsx')
    .then(({ default: KategoriObat }) => {
      root.render(<KategoriObat />);
    })
    .catch((err) => console.error('Failed to load Kategori Obat', err));
}

// Mount Golongan Obat page if present
const golonganObatRoot = document.getElementById('farmasi-golongan-obat-root');
if (golonganObatRoot) {
  const root = createRoot(golonganObatRoot);
  importIfPreamble('./pages/Farmasi/GolonganObat.jsx')
    .then(({ default: GolonganObat }) => {
      root.render(<GolonganObat />);
    })
    .catch((err) => console.error('Failed to load Golongan Obat', err));
}

// Mount Set Harga Obat page if present
const setHargaObatRoot = document.getElementById('farmasi-set-harga-obat-root');
if (setHargaObatRoot) {
  const root = createRoot(setHargaObatRoot);
  importIfPreamble('./pages/Farmasi/SetHargaObat.jsx')
    .then(({ default: SetHargaObat }) => {
      root.render(<SetHargaObat />);
    })
    .catch((err) => console.error('Failed to load Set Harga Obat', err));
}

// Mount Data Obat page if present
const dataObatRoot = document.getElementById('farmasi-data-obat-root');
if (dataObatRoot) {
  const root = createRoot(dataObatRoot);
  importIfPreamble('./pages/Farmasi/dataobat_legacy.jsx')
    .then(({ default: DataObat }) => {
      root.render(<DataObat />);
    })
    .catch((err) => console.error('Failed to load Data Obat', err));
}

// Mount Stok Opname page if present
const stokOpnameRoot = document.getElementById('farmasi-stok-opname-root');
if (stokOpnameRoot) {
  const root = createRoot(stokOpnameRoot);
  importIfPreamble('./pages/Farmasi/StokOpname.jsx')
    .then(({ default: StokOpname }) => {
      root.render(<StokOpname />);
    })
    .catch((err) => console.error('Failed to load Stok Opname', err));
}

// Mount Pembelian Obat page if present
const pembelianObatRoot = document.getElementById('farmasi-pembelian-obat-root');
if (pembelianObatRoot) {
  const root = createRoot(pembelianObatRoot);
  importIfPreamble('./pages/Farmasi/PembelianObat.jsx')
    .then(({ default: PembelianObat }) => {
      root.render(<PembelianObat />);
    })
    .catch((err) => console.error('Failed to load Pembelian Obat', err));
}

// Mount Penjualan Obat page if present
const penjualanObatRoot = document.getElementById('farmasi-penjualan-obat-root');
if (penjualanObatRoot) {
  const root = createRoot(penjualanObatRoot);
  importIfPreamble('./pages/Farmasi/PenjualanObat.jsx')
    .then(({ default: PenjualanObat }) => {
      root.render(<PenjualanObat />);
    })
    .catch((err) => console.error('Failed to load Penjualan Obat', err));
}

// Mount Resep Obat page if present
const resepObatRoot = document.getElementById('farmasi-resep-obat-root');
if (resepObatRoot) {
  const root = createRoot(resepObatRoot);
  importIfPreamble('./pages/Farmasi/ResepObat.jsx')
    .then(({ default: ResepObat }) => {
      root.render(<ResepObat />);
    })
    .catch((err) => console.error('Failed to load Resep Obat', err));
}

// Mount Riwayat Transaksi Gudang page if present
const riwayatTransaksiGudangRoot = document.getElementById('farmasi-riwayat-transaksi-gudang-root');
if (riwayatTransaksiGudangRoot) {
  const root = createRoot(riwayatTransaksiGudangRoot);
  importIfPreamble('./pages/Farmasi/RiwayatTransaksiGudang.jsx')
    .then(({ default: RiwayatTransaksiGudang }) => {
      root.render(<RiwayatTransaksiGudang />);
    })
    .catch((err) => console.error('Failed to load Riwayat Transaksi Gudang', err));
}

// Mount Stok Obat page if present
const stokObatRoot = document.getElementById('farmasi-stok-obat-root');
if (stokObatRoot) {
  const root = createRoot(stokObatRoot);
  importIfPreamble('./pages/Farmasi/StokObat.jsx')
    .then(({ default: StokObat }) => {
      root.render(<StokObat />);
    })
    .catch((err) => console.error('Failed to load Stok Obat', err));
}

// Mount Data Opname page if present
const dataOpnameRoot = document.getElementById('farmasi-data-opname-root');
if (dataOpnameRoot) {
  const root = createRoot(dataOpnameRoot);
  importIfPreamble('./pages/Farmasi/DataOpname.jsx')
    .then(({ default: DataOpname }) => {
      root.render(<DataOpname />);
    })
    .catch((err) => console.error('Failed to load Data Opname', err));
}
// Mount React Sidebar if present
const sidebarRoot = document.getElementById('react-sidebar-root');
if (sidebarRoot && sidebarRoot.dataset.reactMounted !== 'true') {
  sidebarRoot.dataset.reactMounted = 'true';
  const root = createRoot(sidebarRoot);
  // Helper: decode HTML entities from attribute strings (e.g., &quot; &amp;)
  const decodeHtmlEntities = (str) => {
    try {
      if (typeof str !== 'string') return str;
      const div = document.createElement('div');
      div.innerHTML = str;
      return div.textContent || div.innerText || str;
    } catch {
      return str;
    }
  };
  // Prefer script tag JSON first to avoid HTML entity pitfalls
  let raw = null;
  const adminMenuScript = document.getElementById('admin-menu-json');
  if (adminMenuScript && adminMenuScript.textContent) {
    try {
      raw = JSON.parse(adminMenuScript.textContent);
    } catch (e) {
      console.warn('[Sidebar] Failed to parse admin-menu-json script content', e);
    }
  }
  // Fallback: window var then data attribute
  if (!raw && window.ADMIN_MENU) {
    raw = window.ADMIN_MENU;
  }
  if (!raw) {
    try {
      const dataAttr = sidebarRoot.getAttribute('data-admin-menu') || '[]';
      const decoded = decodeHtmlEntities(dataAttr);
      raw = JSON.parse(decoded);
    } catch (e) {
      console.warn('[Sidebar] Failed to parse data-admin-menu JSON', e);
      raw = [];
    }
  }
  let menu = [];
  try {
    if (Array.isArray(raw)) menu = raw;
    else if (raw && typeof raw === 'string') menu = JSON.parse(raw);
    else if (raw && typeof raw === 'object') {
      if (Array.isArray(raw.items)) menu = raw.items;
      else menu = Object.values(raw);
    }
  } catch (e) {
    console.warn('Failed to normalize ADMIN_MENU, using empty array', e);
    menu = [];
  }
  importIfPreamble('./Layouts/Sidebar.jsx')
    .then(({ default: Sidebar }) => {
      root.render(<Sidebar menu={menu} />);
      // Hide fallback menu after React renders
      const fallback = document.getElementById('fallback-sidebar-menu');
      if (fallback) fallback.style.display = 'none';
    })
    .catch((err) => {
      console.error('Sidebar failed to load:', err);
      root.render(null);
    });
}

// Mount React TopNavbar if present
const topnavRoot = document.getElementById('react-topnav-root');
if (topnavRoot && topnavRoot.dataset.reactMounted !== 'true') {
  topnavRoot.dataset.reactMounted = 'true';
  const root = createRoot(topnavRoot);
  // Helpers
  const decodeHtmlEntitiesTop = (str) => {
    try {
      if (typeof str !== 'string') return str;
      const div = document.createElement('div');
      div.innerHTML = str;
      return div.textContent || div.innerText || str;
    } catch {
      return str;
    }
  };
  const parseJSONFromScript = (id) => {
    const el = document.getElementById(id);
    if (!el || !el.textContent) return null;
    try {
      return JSON.parse(el.textContent);
    } catch (e) {
      console.warn(`[TopNavbar] Failed to parse ${id} script content`, e);
      return null;
    }
  };
  const parseJSONFromAttr = (attrName) => {
    const val = topnavRoot.getAttribute(attrName);
    if (!val) return null;
    try {
      const decoded = decodeHtmlEntitiesTop(val);
      return JSON.parse(decoded);
    } catch (e) {
      console.warn(`[TopNavbar] Failed to parse ${attrName} attribute`, e);
      return null;
    }
  };

  let left = [];
  let right = [];
  try {
    // Priority: script tags -> window globals -> data attributes
    let rawLeft = parseJSONFromScript('topnav-left-json');
    let rawRight = parseJSONFromScript('topnav-right-json');
    if (!rawLeft && window.TOPNAV_LEFT) rawLeft = window.TOPNAV_LEFT;
    if (!rawRight && window.TOPNAV_RIGHT) rawRight = window.TOPNAV_RIGHT;
    if (!rawLeft) rawLeft = parseJSONFromAttr('data-topnav-left');
    if (!rawRight) rawRight = parseJSONFromAttr('data-topnav-right');

    left = Array.isArray(rawLeft) ? rawLeft : (rawLeft ? Object.values(rawLeft) : []);
    right = Array.isArray(rawRight) ? rawRight : (rawRight ? Object.values(rawRight) : []);
  } catch (e) {
    console.warn('Failed to normalize TOPNAV menus', e);
  }
  importIfPreamble('./Layouts/TopNavbar.jsx')
    .then(({ default: TopNavbar }) => {
      root.render(<TopNavbar left={left} right={right} />);
      const fallback = document.getElementById('topnav-fallback');
      if (fallback) fallback.style.display = 'none';
    })
    .catch((err) => {
      console.error('TopNavbar failed to load:', err);
      root.render(null);
    });
}
// Mount Login Premium page if present
const loginPremiumRoot = document.getElementById('login-premium-react-root');
if (loginPremiumRoot) {
  const root = createRoot(loginPremiumRoot);
  importIfPreamble('./pages/LoginPremium.jsx')
    .then(({ default: LoginPremium }) => {
      root.render(<LoginPremium />);
      // Hide Blade fallback after React mounts
      const fallback = document.getElementById('login-fallback');
      if (fallback) fallback.style.display = 'none';
    })
    .catch((err) => {
      console.warn('LoginPremium failed to load. Keeping Blade fallback visible.', err);
      // Do NOT clear the container; let Blade fallback remain usable
    });
}

// Menu UKM React Page
const ukmMenuRoot = document.getElementById('matrik-kegiatan-ukm-react-root');
if (ukmMenuRoot) {
    importIfPreamble('./pages/MatrikKegiatanUkm/index.jsx').then(({ default: UkmMenu }) => {
        const root = createRoot(ukmMenuRoot);
        root.render(<UkmMenu />);
    });
}

// Kegiatan UKM React Page
const kegiatanUkmRoot = document.getElementById('kegiatan-ukm-react-root');
if (kegiatanUkmRoot) {
    importIfPreamble('./pages/MatrikKegiatanUkm/KegiatanUkm.jsx').then(({ default: KegiatanUkm }) => {
        const el = kegiatanUkmRoot;
        const metaUrl = el?.dataset?.metaUrl || '/api/kegiatan-ukm/meta';
        const listUrl = el?.dataset?.listUrl || '/api/kegiatan-ukm';
        const storeUrl = el?.dataset?.storeUrl || '/api/kegiatan-ukm';
        const updateUrlTemplate = el?.dataset?.updateUrlTemplate || '/api/kegiatan-ukm/__ID__';
        const deleteUrlTemplate = el?.dataset?.deleteUrlTemplate || '/api/kegiatan-ukm/__ID__';
        const csrfToken = el?.dataset?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;

        const root = createRoot(el);
        root.render(
            <KegiatanUkm
                metaUrl={metaUrl}
                listUrl={listUrl}
                storeUrl={storeUrl}
                updateUrlTemplate={updateUrlTemplate}
                deleteUrlTemplate={deleteUrlTemplate}
                csrfToken={csrfToken}
            />
        );
    });
}

// Jadwal UKM React Page
const jadwalUkmRoot = document.getElementById('jadwal-ukm-react-root');
if (jadwalUkmRoot) {
    importIfPreamble('./pages/MatrikKegiatanUkm/JadwalUkm.jsx').then(({ default: JadwalUkm }) => {
        const el = jadwalUkmRoot;
        const metaUrl = el?.dataset?.metaUrl || '/api/jadwal-ukm/meta';
        const listUrl = el?.dataset?.listUrl || '/api/jadwal-ukm';
        const storeUrl = el?.dataset?.storeUrl || '/api/jadwal-ukm';
        const updateUrlTemplate = el?.dataset?.updateUrlTemplate || '/api/jadwal-ukm/__ID__';
        const deleteUrlTemplate = el?.dataset?.deleteUrlTemplate || '/api/jadwal-ukm/__ID__';
        const csrfToken = el?.dataset?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;

        const root = createRoot(el);
        root.render(
            <JadwalUkm
                metaUrl={metaUrl}
                listUrl={listUrl}
                storeUrl={storeUrl}
                updateUrlTemplate={updateUrlTemplate}
                deleteUrlTemplate={deleteUrlTemplate}
                csrfToken={csrfToken}
            />
        );
    });
}

// Display Kegiatan UKM React Page
const displayKegiatanUkmRoot = document.getElementById('display-kegiatan-ukm-react-root');
if (displayKegiatanUkmRoot) {
  importIfPreamble('./pages/MatrikKegiatanUkm/DisplayKegiatanUkm.jsx').then(({ default: DisplayKegiatanUkm }) => {
    const el = displayKegiatanUkmRoot;
    const monthlyUrl = el?.dataset?.monthlyUrl || '/api/jadwal-ukm/monthly';
    const root = createRoot(el);
    root.render(<DisplayKegiatanUkm monthlyUrl={monthlyUrl} />);
  });
}
// Mount Antri Poli page if present
const antriPoliRoot = document.getElementById('antri-poli-root');
if (antriPoliRoot) {
  const root = createRoot(antriPoliRoot);
  importIfPreamble('./pages/AntraniPoli/AntriPoli.jsx')
    .then(({ default: AntriPoli }) => {
      root.render(<AntriPoli />);
    })
    .catch((err) => {
      console.warn('AntriPoli failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
    });
}

// (Kelurahan React filter removed)
