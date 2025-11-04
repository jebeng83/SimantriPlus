import React, { useState } from 'react';
import { motion } from 'framer-motion';

const tabs = [
  { key: 'master', label: 'Master Data' },
  { key: 'layanan', label: 'Layanan' },
  { key: 'laporan', label: 'Laporan Farmasi' }
];

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: { opacity: 1, y: 0, transition: { staggerChildren: 0.05 } }
};

const cardVariants = {
  hidden: { opacity: 0, y: 16, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 }
};

const cardsByTab = {
  master: [
    { key: 'dataObat', title: 'Data Obat', desc: 'Daftar & detail obat', href: '/farmasi/data-obat', gradient: 'from-indigo-500 to-purple-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M7 7h10M7 12h10M7 17h6" /></svg>
    ) },
    { key: 'satuanBarang', title: 'Satuan Barang', desc: 'Kelola satuan', href: '/farmasi/satuan-barang', gradient: 'from-amber-500 to-orange-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><rect x="4" y="4" width="16" height="16" rx="2" /></svg>
    ) },
    { key: 'jenisObat', title: 'Jenis Obat', desc: 'Kelola jenis obat', href: '/farmasi/jenis-obat', gradient: 'from-cyan-500 to-sky-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
    ) },
    { key: 'kategoriObat', title: 'Kategori Obat', desc: 'Kelola kategori', href: '/farmasi/kategori-obat', gradient: 'from-emerald-500 to-teal-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 6h16" /><path d="M4 12h10" /><path d="M4 18h7" /></svg>
    ) },
    { key: 'golonganObat', title: 'Golongan Obat', desc: 'Kelola golongan', href: '/farmasi/golongan-obat', gradient: 'from-rose-500 to-red-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><circle cx="12" cy="12" r="4" /><path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6" /></svg>
    ) },
    { key: 'setHargaObat', title: 'Set Harga Obat', desc: 'Kelola harga', href: '/farmasi/set-harga-obat', gradient: 'from-yellow-500 to-amber-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M12 1v22" /><path d="M8 5h8" /><path d="M6 12h12" /><path d="M8 19h8" /></svg>
    ) },
    { key: 'metodeRacik', title: 'Metode Racik', desc: 'Aturan peracikan', href: '/farmasi/metode-racik', gradient: 'from-fuchsia-500 to-pink-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 4l8 8" /><path d="M12 12l8 8" /></svg>
    ) },
    { key: 'konversiSatuan', title: 'Konversi Satuan', desc: 'Konversi antara satuan', href: '/farmasi/konversi-satuan', gradient: 'from-blue-500 to-indigo-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M6 9l6-6 6 6" /><path d="M6 15l6 6 6-6" /></svg>
    ) },
    { key: 'dataSuplier', title: 'Data Suplier', desc: 'Kelola pemasok', href: '/farmasi/data-suplier', gradient: 'from-teal-500 to-green-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M12 6l7 4-7 4-7-4 7-4z" /><path d="M5 14l7 4 7-4" /></svg>
    ) },
    { key: 'industriFarmasi', title: 'Industri Farmasi', desc: 'Kelola industri', href: '/farmasi/industri-farmasi', gradient: 'from-slate-500 to-slate-700', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><rect x="3" y="8" width="18" height="12" rx="2" /><path d="M7 8V4h4v4" /></svg>
    ) }
  ],
  layanan: [
    { key: 'permintaanMedis', title: 'Permintaan Medis', desc: 'Kelola permintaan', href: '/farmasi/permintaan-medis', gradient: 'from-sky-500 to-blue-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 16l4-4 3 3 5-7" /></svg>
    ) },
    { key: 'resepObat', title: 'Resep Obat', desc: 'Kelola resep', href: '/farmasi/resep-obat', gradient: 'from-rose-500 to-red-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 6h16" /><path d="M4 12h10" /><path d="M4 18h7" /></svg>
    ) },
    { key: 'penjualanObat', title: 'Penjualan Obat', desc: 'Transaksi penjualan', href: '/farmasi/penjualan-obat', gradient: 'from-amber-500 to-orange-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M3 12h18" /><path d="M12 3v18" /></svg>
    ) },
    { key: 'pembelianObat', title: 'Pembelian Obat', desc: 'Transaksi pembelian', href: '/farmasi/pembelian-obat', gradient: 'from-emerald-500 to-teal-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M6 9l6-6 6 6" /><path d="M6 15l6 6 6-6" /></svg>
    ) },
    { key: 'stokObat', title: 'Stok Obat', desc: 'Lihat persediaan', href: '/farmasi/stok-obat', gradient: 'from-lime-500 to-green-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><rect x="4" y="4" width="16" height="16" rx="2" /></svg>
    ) },
    { key: 'stokOpname', title: 'Stok Opname', desc: 'Opname persediaan', href: '/farmasi/stok-opname', gradient: 'from-indigo-500 to-purple-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M9 7h6M9 12h6M9 17h4" /></svg>
    ) },
    { key: 'dataOpname', title: 'Data Opname', desc: 'Riwayat opname', href: '/farmasi/data-opname', gradient: 'from-cyan-500 to-sky-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 6h16" /><path d="M4 12h10" /><path d="M4 18h7" /></svg>
    ) },
    { key: 'dashboard', title: 'Dashboard', desc: 'Ringkasan cepat', href: '/farmasi/dashboard', gradient: 'from-slate-500 to-slate-700', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 12h6V4H4z" /><path d="M14 20h6v-8h-6z" /></svg>
    ) }
  ],
  laporan: [
    { key: 'riwayatTransaksi', title: 'Riwayat Transaksi Gudang', desc: 'Log pergerakan obat', href: '/farmasi/riwayat-transaksi-gudang', gradient: 'from-yellow-500 to-amber-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M4 4h16v16H4z" /><path d="M8 8h8" /><path d="M8 12h8" /><path d="M8 16h8" /></svg>
    ) },
    { key: 'laporanStok', title: 'Laporan Stok', desc: 'Ringkasan stok & nilai', href: '/farmasi/stok-obat', gradient: 'from-lime-500 to-green-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M3 12h18" /><path d="M12 3v18" /></svg>
    ) },
    { key: 'laporanOpname', title: 'Laporan Opname', desc: 'Rekap opname berkala', href: '/farmasi/data-opname', gradient: 'from-indigo-500 to-purple-600', icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5"><path d="M9 7h6M9 12h6M9 17h4" /></svg>
    ) }
  ]
};

function Card({ href, title, desc, gradient, icon }) {
  return (
    <motion.a
      href={href}
      variants={cardVariants}
      whileHover={{ y: -3, scale: 1.02 }}
      whileTap={{ scale: 0.98 }}
      className="group relative flex flex-col rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-colors hover:border-slate-300 hover:bg-white hover:shadow-md min-h-[110px]"
    >
      <div className={`flex h-9 w-9 items-center justify-center rounded-md bg-gradient-to-br ${gradient} text-white shadow`}>
        {icon}
      </div>
      <div className="mt-2">
        <div className="text-slate-800 font-semibold leading-tight">
          {title} <span className="text-slate-400">›</span>
        </div>
        <div className="text-slate-500 text-xs">{desc}</div>
      </div>
    </motion.a>
  );
}

export default function FarmasiIndex() {
  const [activeTab, setActiveTab] = useState('layanan');
  return (
    <div className="w-full">
      {/* Header */}
      <div className="mb-4 flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800 tracking-tight">Menu Farmasi</h1>
          <p className="text-xs text-slate-500">Akses cepat ke master, layanan, dan laporan farmasi</p>
        </div>
        <span className="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-700">v1.0</span>
      </div>

      {/* Tabs */}
      <div className="mb-3 flex flex-wrap gap-2">
        {tabs.map((t) => (
          <button
            key={t.key}
            type="button"
            onClick={() => setActiveTab(t.key)}
            className={`px-3 py-1.5 text-xs rounded-full border transition-colors ${
              activeTab === t.key
                ? 'bg-slate-100 text-slate-800 border-slate-300'
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
            }`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {/* Cards Grid */}
      <motion.div
        className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        {cardsByTab[activeTab].map(({ key, ...itemProps }) => (
          <Card key={key} {...itemProps} />
        ))}
      </motion.div>

      {/* Footer Note */}
      <div className="mt-4 text-[10px] text-slate-400">Tip: Pilih tab untuk melihat kartu terkait.</div>
    </div>
  );
}