import React, { useState } from 'react';
import { motion } from 'framer-motion';

const tabs = [
  { key: 'program', label: 'Laporan Program' },
  { key: 'grafik', label: 'Grafik Analisa' },
  { key: 'umum', label: 'Laporan Umum' },
];

const cardsByTab = {
  program: [
    {
      key: 'program-dashboard',
      title: 'Dashboard Program',
      desc: 'Ringkasan semua program kesehatan',
      href: '/ranap/laporan/program',
      gradient: 'from-indigo-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h10M4 18h7" />
        </svg>
      ),
    },
    {
      key: 'pasien-ranap',
      title: 'Pasien Ranap',
      desc: 'Data pasien rawat inap',
      href: '/ranap/laporan/program',
      gradient: 'from-emerald-500 to-teal-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
          <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8" className="opacity-50" />
        </svg>
      ),
    },
    {
      key: 'kinerja-medis',
      title: 'Kinerja Medis',
      desc: 'Evaluasi dan KPI tim medis',
      href: '/ranap/laporan/program',
      gradient: 'from-rose-500 to-red-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 18l6-6 4 4 6-8" />
        </svg>
      ),
    },
    {
      key: 'farmasi',
      title: 'Laporan Farmasi',
      desc: 'Stok & penggunaan obat',
      href: '/ranap/laporan/program',
      gradient: 'from-amber-500 to-yellow-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M7 11h10M7 15h10" />
          <rect x="4" y="5" width="16" height="14" rx="2" />
        </svg>
      ),
    },
    {
      key: 'tim-medis',
      title: 'Tim Medis',
      desc: 'Kehadiran & jadwal tim',
      href: '/ranap/laporan/program',
      gradient: 'from-fuchsia-500 to-pink-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <circle cx="8" cy="8" r="3" />
          <circle cx="16" cy="8" r="3" />
          <path d="M4 21c0-3.314 2.686-6 6-6" className="opacity-50" />
          <path d="M14 15c3.314 0 6 2.686 6 6" className="opacity-50" />
        </svg>
      ),
    },
    {
      key: 'bulanan',
      title: 'Laporan Bulanan',
      desc: 'Ringkasan aktivitas bulanan',
      href: '/ranap/laporan/program',
      gradient: 'from-sky-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h12M4 18h8" />
        </svg>
      ),
    },
  ],
  grafik: [
    {
      key: 'grafik-dashboard',
      title: 'Grafik & Analisa',
      desc: 'Dashboard analitik lengkap',
      href: '/ranap/laporan/grafik',
      gradient: 'from-indigo-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 19h16M7 16v-6M12 16V8M17 16v-4" />
        </svg>
      ),
    },
    {
      key: 'demografi',
      title: 'Demografi Pasien',
      desc: 'Analisa usia, gender, lokasi',
      href: '/ranap/laporan/demografi-pasien',
      gradient: 'from-emerald-500 to-teal-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
          <path d="M3 21c0-5 4-9 9-9s9 4 9 9" className="opacity-50" />
        </svg>
      ),
    },
    {
      key: 'top-penyakit',
      title: 'Top 10 Penyakit',
      desc: 'Analisis penyakit terbanyak',
      href: '/ranap/laporan/top-penyakit',
      gradient: 'from-rose-500 to-red-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 14h6v6H4zM10 10h6v10h-6zM16 6h6v14h-6z" />
        </svg>
      ),
    },
    {
      key: 'realtime',
      title: 'Realtime Dashboard',
      desc: 'Monitoring data live',
      href: '/ranap/laporan/grafik',
      gradient: 'from-amber-500 to-yellow-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 12h6l3-6 3 10 4-8" />
        </svg>
      ),
    },
    {
      key: 'heatmap',
      title: 'Heatmap Aktivitas',
      desc: 'Pola aktivitas RS',
      href: '/ranap/laporan/grafik',
      gradient: 'from-fuchsia-500 to-pink-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 4h16v16H4z" />
          <path d="M8 8h4v4H8z" />
        </svg>
      ),
    },
    {
      key: 'predictive',
      title: 'Predictive Analytics',
      desc: 'Perkiraan tren & kapasitas',
      href: '/ranap/laporan/grafik',
      gradient: 'from-sky-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 18l6-6 4 4 6-8" />
        </svg>
      ),
    },
  ],
  umum: [
    {
      key: 'antrian-poliklinik',
      title: 'Antrian Poliklinik',
      desc: 'Cetak & export laporan antrian',
      href: '/laporan/antrian-poliklinik',
      gradient: 'from-indigo-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h10M4 18h7" />
        </svg>
      ),
    },
    {
      key: 'lap-operasi',
      title: 'Lap. Operasi',
      desc: 'Template & ringkasan',
      href: '#',
      gradient: 'from-emerald-500 to-teal-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M7 11h10M7 15h10" />
          <rect x="4" y="5" width="16" height="14" rx="2" />
        </svg>
      ),
    },
    {
      key: 'resume-pasien',
      title: 'Resume Pasien',
      desc: 'Ringkasan perawatan',
      href: '#',
      gradient: 'from-rose-500 to-red-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
          <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8" className="opacity-50" />
        </svg>
      ),
    },
    {
      key: 'export',
      title: 'Export Laporan',
      desc: 'PDF & Excel',
      href: '#',
      gradient: 'from-amber-500 to-yellow-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M12 5v14M5 12h14" />
        </svg>
      ),
    },
    {
      key: 'audit',
      title: 'Audit & Log',
      desc: 'Jejak aktivitas sistem',
      href: '#',
      gradient: 'from-fuchsia-500 to-pink-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h12M4 18h8" />
        </svg>
      ),
    },
    {
      key: 'lainnya',
      title: 'Menu Lainnya',
      desc: 'Segera hadir',
      href: '#',
      gradient: 'from-sky-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M9 5l7 7-7 7" />
        </svg>
      ),
    },
  ],
};

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: { opacity: 1, y: 0, transition: { staggerChildren: 0.05 } },
};

const cardVariants = {
  hidden: { opacity: 0, y: 20, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 },
};

export default function LaporanMenu() {
  const [activeTab, setActiveTab] = useState('program');

  const items = cardsByTab[activeTab] || [];

  return (
    <div className="w-full">
      {/* Header */}
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-semibold text-slate-800 tracking-tight">Menu Laporan</h1>
          <p className="text-sm text-slate-500">Akses cepat ke seluruh laporan: Program, Grafik, dan Umum</p>
        </div>
        <div className="flex items-center gap-2">
          <span className="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">v1.0</span>
        </div>
      </div>

      {/* Tabs */}
      <div className="mb-4 flex flex-wrap gap-2">
        {tabs.map((t) => (
          <button
            key={t.key}
            onClick={() => setActiveTab(t.key)}
            className={`px-4 py-2 rounded-full border transition-colors text-sm ${
              activeTab === t.key
                ? 'bg-slate-800 text-white border-slate-800'
                : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'
            }`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {/* Cards Row (1 row, up to 6 cols) */}
      <motion.div
        key={activeTab}
        className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        {items.map((item) => (
          <motion.a
            href={item.href}
            key={item.key}
            variants={cardVariants}
            whileHover={{ y: -4, scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            className="group relative block w-full min-h-[120px] overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm hover:shadow-md backdrop-blur-sm transition-colors hover:border-slate-300"
          >
            {/* Accent gradient bar */}
            <div className={`absolute inset-x-0 top-0 h-1 z-10 rounded-t-xl bg-gradient-to-r ${item.gradient}`} />

            {/* Icon */}
            <div
              className={`relative z-10 inline-flex items-center justify-center rounded-lg bg-gradient-to-br ${item.gradient} text-white shadow-md`}
              style={{ width: 42, height: 42 }}
            >
              {item.icon}
            </div>

            {/* Texts */}
            <div className="mt-3 relative z-10">
              <div className="flex items-center gap-2">
                <h3 className="text-sm font-semibold text-slate-800">{item.title}</h3>
                <svg
                  viewBox="0 0 24 24"
                  className="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="1.5"
                >
                  <path d="M9 5l7 7-7 7" />
                </svg>
              </div>
              <p className="mt-1 text-xs text-slate-500">{item.desc}</p>
            </div>

            {/* Subtle hover glow */}
            <div
              className={`pointer-events-none absolute inset-0 z-0 rounded-xl opacity-0 group-hover:opacity-20 transition duration-300 bg-gradient-to-r ${item.gradient} blur-md`}
              aria-hidden="true"
            />
          </motion.a>
        ))}
      </motion.div>

      {/* Info */}
      <div className="mt-8">
        <div className="rounded-xl border border-slate-200 bg-white/80 p-4 shadow-sm">
          <h2 className="text-base font-semibold text-slate-800">Panduan Singkat</h2>
          <ul className="mt-2 text-sm text-slate-600 list-disc pl-4 space-y-1">
            <li>Gunakan tab di atas untuk berpindah antar kategori laporan.</li>
            <li>Klik kartu untuk membuka halaman laporan terkait.</li>
            <li>Beberapa menu umum masih dalam tahap pengembangan.</li>
          </ul>
          <p className="mt-3 text-[11px] text-slate-400">Tip: Bookmark halaman favorit Anda untuk akses lebih cepat.</p>
        </div>
      </div>
    </div>
  );
}