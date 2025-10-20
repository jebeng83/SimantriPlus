import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import HelloMotion from './components/HelloMotion.jsx';
import RegisterHeader from './components/RegisterHeader.jsx';
import PcareStatusHeader from './pages/PcareStatusHeader.jsx';
import IlpMenu from './pages/ilp/index.jsx';
import PcareMenu from './pages/Pcare/index.jsx';
import MobileJknHome from './pages/Mobile-Jkn/home.jsx';
import EppbgmMenu from './pages/Eppbgm/index.jsx';
import LaporanMenu from './pages/Laporan/index.jsx';
import RegPeriksaPage from './pages/reg_periksa/index.jsx';
import '../css/app.css'

// Mount React app only if the root element exists
const rootElement = document.getElementById('react-root');
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<HelloMotion />);
}

// Mount motion-enhanced Register header if present
const registerRoot = document.getElementById('register-react-root');
if (registerRoot) {
  const root = createRoot(registerRoot);
  const { totalPasien = '0', belumPeriksa = '0', date = '' } = registerRoot.dataset;
  root.render(
    <RegisterHeader
      totalPasien={parseInt(totalPasien, 10) || 0}
      belumPeriksa={parseInt(belumPeriksa, 10) || 0}
      date={date}
    />
  );
}

// Mount PCare Status header with motion
const pcareRoot = document.getElementById('pcare-status-react-root');
if (pcareRoot) {
  const root = createRoot(pcareRoot);
  root.render(<PcareStatusHeader />);
}

// Mount ILP Menu if present
const ilpMenuRoot = document.getElementById('ilp-menu-root');
if (ilpMenuRoot) {
  const root = createRoot(ilpMenuRoot);
  root.render(<IlpMenu />);
}

// Mount PCare Menu if present
const pcareMenuRoot = document.getElementById('pcare-menu-root');
if (pcareMenuRoot) {
  const root = createRoot(pcareMenuRoot);
  root.render(<PcareMenu />);
}

// Mount Mobile JKN Antrol menu if present
const mobileJknHomeRoot = document.getElementById('mobile-jkn-home-root');
if (mobileJknHomeRoot) {
  const root = createRoot(mobileJknHomeRoot);
  root.render(<MobileJknHome />);
}

// Mount ePPBGM Menu if present
const eppbgmMenuRoot = document.getElementById('eppbgm-menu-root');
if (eppbgmMenuRoot) {
  const root = createRoot(eppbgmMenuRoot);
  root.render(<EppbgmMenu />);
}

// Mount Laporan Menu if present
const laporanMenuRoot = document.getElementById('laporan-menu-root');
if (laporanMenuRoot) {
  const root = createRoot(laporanMenuRoot);
  root.render(<LaporanMenu />);
}

// Mount Reg Periksa Page if present
const regPeriksaRoot = document.getElementById('reg-periksa-root');
if (regPeriksaRoot) {
  const root = createRoot(regPeriksaRoot);
  root.render(<RegPeriksaPage />);
}