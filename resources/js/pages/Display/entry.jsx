import React from 'react';
import { createRoot } from 'react-dom/client';
import LoketDisplay from './index.jsx';

const mountEl = document.getElementById('react-root');
if (mountEl) {
  const root = createRoot(mountEl);
  root.render(<LoketDisplay />);
}