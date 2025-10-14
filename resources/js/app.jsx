import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import HelloMotion from './components/HelloMotion.jsx';
import RegisterHeader from './components/RegisterHeader.jsx';
import PcareStatusHeader from './pages/PcareStatusHeader.jsx';

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