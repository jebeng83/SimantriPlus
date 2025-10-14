import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

export default function PcareStatusHeader() {
  const [summary, setSummary] = useState({
    total: 0,
    terkirim: 0,
    belum: 0,
    batal: 0,
    persentase: 0,
    sukses_kunjungan: 0,
    gap_reg_vs_pcare: 0,
    gap_pcare_vs_kunjungan: 0,
    gap_reg_vs_kunjungan: 0,
  });

  // Expose updater globally so Blade script can feed data
  useEffect(() => {
    window.setPcareSummary = (data) => setSummary((prev) => ({ ...prev, ...data }));
    return () => { delete window.setPcareSummary; };
  }, []);

  const Card = ({ icon, label, value, color }) => (
    <motion.div
      className={`card shadow-sm mb-3 border-0`}
      style={{ borderRadius: 10 }}
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.35 }}
      whileHover={{ scale: 1.02 }}
    >
      <div className="card-body d-flex align-items-center">
        <div className={`mr-3 d-flex align-items-center justify-content-center rounded`} style={{
          width: 42, height: 42, background: color, color: '#fff'
        }}>
          <i className={icon}></i>
        </div>
        <div>
          <div className="text-muted small">{label}</div>
          <div className="h5 mb-0 font-weight-bold">{value}</div>
        </div>
      </div>
    </motion.div>
  );

  return (
    <div>
      {/* Baris 1: Total dan Sukses Pendaftaran/Kunjungan */}
      <div className="row">
        <div className="col-md-3"><Card icon="fas fa-users" label="Total Reg (BPJ/NON/PBI)" value={summary.total} color="#0ea5e9" /></div>
        <div className="col-md-3"><Card icon="fas fa-paper-plane" label="Sukses Pendaftaran PCare" value={summary.terkirim} color="#22c55e" /></div>
        <div className="col-md-3"><Card icon="fas fa-notes-medical" label="Sukses Kunjungan PCare" value={summary.sukses_kunjungan} color="#10b981" /></div>
        <div className="col-md-3"><Card icon="fas fa-times-circle" label="Batal Pendaftaran" value={summary.batal} color="#ef4444" /></div>
      </div>

      {/* Baris 2: Gap */}
      <div className="row">
        <div className="col-md-4"><Card icon="fas fa-arrows-alt-h" label="Gap Reg vs Pendaftaran" value={summary.gap_reg_vs_pcare} color="#f59e0b" /></div>
        <div className="col-md-4"><Card icon="fas fa-exchange-alt" label="Gap Pendaftaran vs Kunjungan" value={summary.gap_pcare_vs_kunjungan} color="#f97316" /></div>
        <div className="col-md-4"><Card icon="fas fa-balance-scale" label="Gap Total Reg vs Kunjungan" value={summary.gap_reg_vs_kunjungan} color="#fb7185" /></div>
      </div>

      {/* Progress persentase pendaftaran sukses dari total */}
      <div className="mt-2">
        <div className="d-flex justify-content-between align-items-center mb-1">
          <span className="text-muted">Persentase Sukses Pendaftaran</span>
          <span className="font-weight-bold">{summary.persentase}%</span>
        </div>
        <div className="progress" style={{ height: 10 }}>
          <motion.div
            className="progress-bar bg-success"
            role="progressbar"
            initial={{ width: 0 }}
            animate={{ width: `${summary.persentase}%` }}
            transition={{ duration: 0.5 }}
          />
        </div>
      </div>
    </div>
  );
}