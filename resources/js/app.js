import './bootstrap';
import React from 'react';
import ReactDOM from 'react-dom';
import LaporanProgram from './pages/LaporanProgram/index.jsx';

// Make React and components available globally
window.React = React;
window.ReactDOM = ReactDOM;
window.LaporanProgram = LaporanProgram;

// Auto-initialize React components on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Laporan Program component if container exists
    const laporanProgramContainer = document.getElementById('laporan-program-app');
    if (laporanProgramContainer) {
        ReactDOM.render(
            React.createElement(LaporanProgram),
            laporanProgramContainer
        );
    }
});
