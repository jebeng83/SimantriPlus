import React from 'react';
import { motion } from 'framer-motion';

const menuItems = [
  {
    key: 'kegiatan',
    title: 'Kegiatan UKM',
    desc: 'Kelola data kegiatan UKM',
    href: '/kegiatan-ukm',
    gradient: 'from-emerald-500 to-teal-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16M4 12h10M4 18h7" />
      </svg>
    ),
  },
  {
    key: 'jadwal',
    title: 'Jadwal UKM',
    desc: 'Kelola jadwal kegiatan UKM',
    href: '/jadwal-ukm',
    gradient: 'from-indigo-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 5h18M3 10h18M3 15h18M3 20h18" />
      </svg>
    ),
  },
  {
    key: 'matrix',
    title: 'Matrix Kegiatan',
    desc: 'Analisa & matriks kegiatan',
    href: '/matrix-kegiatan-ukm',
    gradient: 'from-rose-500 to-red-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 16l6-6 4 4 6-8" />
      </svg>
    ),
  },
  // Tambahan kartu baru: Display Jadwal UKM
  {
    key: 'display-jadwal',
    title: 'Display Jadwal UKM',
    desc: 'Lihat jadwal UKM per hari dalam satu bulan',
    href: '/display-kegiatan-ukm',
    gradient: 'from-cyan-500 to-sky-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="3" y="4" width="18" height="14" rx="2" ry="2" />
        <path d="M7 8h6M7 12h10" />
      </svg>
    ),
  },
];

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: { opacity: 1, y: 0, transition: { staggerChildren: 0.05 } },
};

const cardVariants = {
  hidden: { opacity: 0, y: 20, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 },
};

export default function UkmMenu() {
  const handleNavigate = (href) => {
    window.location.assign(href);
  };

  return (
    <div className="relative w-full min-h-[calc(100vh-140px)]">
      {/* Decorative background */}
      <div className="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div className="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-gradient-to-br from-fuchsia-300 via-pink-300 to-orange-300 opacity-30 blur-3xl" />
        <div className="absolute -bottom-28 -right-28 w-[28rem] h-[28rem] rounded-full bg-gradient-to-tr from-sky-300 via-cyan-300 to-emerald-300 opacity-30 blur-[100px]" />
        <div className="absolute top-1/3 left-1/2 -translate-x-1/2 w-[45rem] h-40 bg-gradient-to-r from-indigo-200 via-violet-200 to-pink-200 opacity-40 blur-2xl rounded-full" />
      </div>

      {/* Header */}
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight bg-gradient-to-r from-violet-600 via-sky-600 to-emerald-600 bg-clip-text text-transparent">Menu UKM</h1>
          <p className="text-sm text-slate-600">Akses cepat ke fitur UKM</p>
        </div>
        <div className="flex items-center gap-2">
          <span className="inline-flex items-center rounded-full bg-gradient-to-r from-slate-100 to-slate-200 px-3 py-1 text-xs font-medium text-slate-700 shadow-sm border border-slate-200/60">
            v1.0
          </span>
        </div>
      </div>

      {/* Cards Row */}
      <motion.div
        className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-4"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        {menuItems.map((item) => (
          <motion.a
            href={item.href}
            key={item.key}
            variants={cardVariants}
            whileHover={{ y: -3, scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            onClick={(e) => {
              e.preventDefault();
              handleNavigate(item.href);
            }}
            className="group relative flex flex-col rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-colors hover:border-slate-300 hover:bg-white hover:shadow-md min-h-[120px]"
          >
            <div className={`flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br ${item.gradient} text-white shadow-md`}>
              {item.icon}
            </div>
            <div className="mt-3">
              <div className="text-slate-800 font-semibold leading-tight">
                {item.title} <span className="text-slate-400">›</span>
              </div>
              <div className="text-slate-500 text-sm">{item.desc}</div>
            </div>
          </motion.a>
        ))}
      </motion.div>

      {/* Footer Note */}
      <div className="mt-6 text-[11px] text-slate-400">Tip: Klik kartu untuk membuka halaman terkait.</div>
    </div>
  );
}