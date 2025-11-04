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
// Simplified: always import modules; preamble hanya dibutuhkan saat dev refresh, tidak diperlukan di produksi
const importIfPreamble = (path) => import(path);

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
  import('./components/RegisterHeader.jsx')
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
  import('./pages/PcareStatusHeader.jsx')
    .then(({ default: PcareStatusHeader }) => {
      root.render(<PcareStatusHeader />);
    })
    .catch((err) => {
      console.warn('PcareStatusHeader failed to load or preamble missing, rendering nothing.', err);
      root.render(null);
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
  import('./pages/reg_periksa/index.jsx')
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
  import('./pages/Display/index.jsx')
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
  import('./pages/Farmasi/Dashboard.jsx')
    .then(({ default: FarmasiDashboard }) => {
      root.render(<FarmasiDashboard />);
    })
    .catch((err) => console.error('Failed to load Farmasi Dashboard', err));
}

// Mount Industri Farmasi page if present
const industriFarmasiRoot = document.getElementById('farmasi-industri-farmasi-root');
if (industriFarmasiRoot) {
  const root = createRoot(industriFarmasiRoot);
  import('./pages/Farmasi/industrifarmasi.jsx')
    .then(({ default: IndustriFarmasi }) => {
      root.render(<IndustriFarmasi />);
    })
    .catch((err) => console.error('Failed to load Industri Farmasi', err));
}

// Mount Data Suplier page if present
const dataSuplierRoot = document.getElementById('farmasi-data-suplier-root');
if (dataSuplierRoot) {
  const root = createRoot(dataSuplierRoot);
  import('./pages/Farmasi/datasuplier.jsx')
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
  import('./pages/Farmasi/SatuanBarang.jsx')
    .then(({ default: SatuanBarang }) => {
      root.render(<SatuanBarang />);
    })
    .catch((err) => console.error('Failed to load Satuan Barang', err));
}

// Mount Metode Racik page if present
const metodeRacikRoot = document.getElementById('farmasi-metode-racik-root');
if (metodeRacikRoot) {
  const root = createRoot(metodeRacikRoot);
  import('./pages/Farmasi/MetodeRacik.jsx')
    .then(({ default: MetodeRacik }) => {
      root.render(<MetodeRacik />);
    })
    .catch((err) => console.error('Failed to load Metode Racik', err));
}

// Mount Konversi Satuan page if present
const konversiSatuanRoot = document.getElementById('farmasi-konversi-satuan-root');
if (konversiSatuanRoot) {
  const root = createRoot(konversiSatuanRoot);
  import('./pages/Farmasi/KonversiSatuan.jsx')
    .then(({ default: KonversiSatuan }) => {
      root.render(<KonversiSatuan />);
    })
    .catch((err) => console.error('Failed to load Konversi Satuan', err));
}

// Mount Jenis Obat page if present
const jenisObatRoot = document.getElementById('farmasi-jenis-obat-root');
if (jenisObatRoot) {
  const root = createRoot(jenisObatRoot);
  import('./pages/Farmasi/JenisObat.jsx')
    .then(({ default: JenisObat }) => {
      root.render(<JenisObat />);
    })
    .catch((err) => console.error('Failed to load Jenis Obat', err));
}

// Mount Kategori Obat page if present
const kategoriObatRoot = document.getElementById('farmasi-kategori-obat-root');
if (kategoriObatRoot) {
  const root = createRoot(kategoriObatRoot);
  import('./pages/Farmasi/KategoriObat.jsx')
    .then(({ default: KategoriObat }) => {
      root.render(<KategoriObat />);
    })
    .catch((err) => console.error('Failed to load Kategori Obat', err));
}

// Mount Golongan Obat page if present
const golonganObatRoot = document.getElementById('farmasi-golongan-obat-root');
if (golonganObatRoot) {
  const root = createRoot(golonganObatRoot);
  import('./pages/Farmasi/GolonganObat.jsx')
    .then(({ default: GolonganObat }) => {
      root.render(<GolonganObat />);
    })
    .catch((err) => console.error('Failed to load Golongan Obat', err));
}

// Mount Set Harga Obat page if present
const setHargaObatRoot = document.getElementById('farmasi-set-harga-obat-root');
if (setHargaObatRoot) {
  const root = createRoot(setHargaObatRoot);
  import('./pages/Farmasi/SetHargaObat.jsx')
    .then(({ default: SetHargaObat }) => {
      root.render(<SetHargaObat />);
    })
    .catch((err) => console.error('Failed to load Set Harga Obat', err));
}

// Mount Data Obat page if present
const dataObatRoot = document.getElementById('farmasi-data-obat-root');
if (dataObatRoot) {
  const root = createRoot(dataObatRoot);
  import('./pages/Farmasi/dataobat_legacy.jsx')
    .then(({ default: DataObat }) => {
      root.render(<DataObat />);
    })
    .catch((err) => console.error('Failed to load Data Obat', err));
}

// Mount Stok Opname page if present
const stokOpnameRoot = document.getElementById('farmasi-stok-opname-root');
if (stokOpnameRoot) {
  const root = createRoot(stokOpnameRoot);
  import('./pages/Farmasi/StokOpname.jsx')
    .then(({ default: StokOpname }) => {
      root.render(<StokOpname />);
    })
    .catch((err) => console.error('Failed to load Stok Opname', err));
}

// Mount Pembelian Obat page if present
const pembelianObatRoot = document.getElementById('farmasi-pembelian-obat-root');
if (pembelianObatRoot) {
  const root = createRoot(pembelianObatRoot);
  import('./pages/Farmasi/PembelianObat.jsx')
    .then(({ default: PembelianObat }) => {
      root.render(<PembelianObat />);
    })
    .catch((err) => console.error('Failed to load Pembelian Obat', err));
}

// Mount Penjualan Obat page if present
const penjualanObatRoot = document.getElementById('farmasi-penjualan-obat-root');
if (penjualanObatRoot) {
  const root = createRoot(penjualanObatRoot);
  import('./pages/Farmasi/PenjualanObat.jsx')
    .then(({ default: PenjualanObat }) => {
      root.render(<PenjualanObat />);
    })
    .catch((err) => console.error('Failed to load Penjualan Obat', err));
}

// Mount Resep Obat page if present
const resepObatRoot = document.getElementById('farmasi-resep-obat-root');
if (resepObatRoot) {
  const root = createRoot(resepObatRoot);
  import('./pages/Farmasi/ResepObat.jsx')
    .then(({ default: ResepObat }) => {
      root.render(<ResepObat />);
    })
    .catch((err) => console.error('Failed to load Resep Obat', err));
}

// Mount Riwayat Transaksi Gudang page if present
const riwayatTransaksiGudangRoot = document.getElementById('farmasi-riwayat-transaksi-gudang-root');
if (riwayatTransaksiGudangRoot) {
  const root = createRoot(riwayatTransaksiGudangRoot);
  import('./pages/Farmasi/RiwayatTransaksiGudang.jsx')
    .then(({ default: RiwayatTransaksiGudang }) => {
      root.render(<RiwayatTransaksiGudang />);
    })
    .catch((err) => console.error('Failed to load Riwayat Transaksi Gudang', err));
}

// Mount Stok Obat page if present
const stokObatRoot = document.getElementById('farmasi-stok-obat-root');
if (stokObatRoot) {
  const root = createRoot(stokObatRoot);
  import('./pages/Farmasi/StokObat.jsx')
    .then(({ default: StokObat }) => {
      root.render(<StokObat />);
    })
    .catch((err) => console.error('Failed to load Stok Obat', err));
}

// Mount Data Opname page if present
const dataOpnameRoot = document.getElementById('farmasi-data-opname-root');
if (dataOpnameRoot) {
  const root = createRoot(dataOpnameRoot);
  import('./pages/Farmasi/DataOpname.jsx')
    .then(({ default: DataOpname }) => {
      root.render(<DataOpname />);
    })
    .catch((err) => console.error('Failed to load Data Opname', err));
}