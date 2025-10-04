import React from 'react';
import { motion } from 'framer-motion';

export default function RegisterHeader({ totalPasien = 0, belumPeriksa = 0, date = '' }) {
  return (
    <motion.div
      className="registrasi-header"
      initial={{ opacity: 0, y: -12 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, ease: 'easeOut' }}
    >
      <div className="header-content">
        <motion.div
          className="title-section"
          initial={{ opacity: 0, x: -10 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.1 }}
        >
          <h1 className="registrasi-title">Registrasi Pasien Hari Ini</h1>
          <p className="subtitle">{date}</p>
        </motion.div>

        <div className="stats-section">
          <motion.div
            className="stat-card"
            whileHover={{ scale: 1.03 }}
            transition={{ type: 'spring', stiffness: 300, damping: 20 }}
          >
            <div className="stat-icon">
              <i className="fas fa-users"></i>
            </div>
            <div className="stat-info">
              <div className="stat-number">{totalPasien}</div>
              <div className="stat-label">Total Pasien</div>
            </div>
          </motion.div>

          <motion.div
            className="stat-card"
            whileHover={{ scale: 1.03 }}
            transition={{ type: 'spring', stiffness: 300, damping: 20 }}
          >
            <div className="stat-icon pending">
              <i className="fas fa-clock"></i>
            </div>
            <div className="stat-info">
              <div className="stat-number">{belumPeriksa}</div>
              <div className="stat-label">Belum Periksa</div>
            </div>
          </motion.div>
        </div>

        <motion.button
          className="registrasi-btn registrasi-btn-primary btn-register"
          data-toggle="modal"
          data-target="#modalPendaftaran"
          whileHover={{ scale: 1.02 }}
          whileTap={{ scale: 0.98 }}
          transition={{ type: 'spring', stiffness: 300, damping: 20 }}
        >
          <i className="fas fa-user-plus registrasi-btn-icon"></i>
          Register Baru
        </motion.button>
      </div>
    </motion.div>
  );
}