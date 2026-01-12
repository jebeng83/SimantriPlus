import React from 'react';
import { motion } from 'framer-motion';

const menuItems = [
  {
    key: 'dashboard',
    title: 'Dashboard ILP',
    desc: 'Ringkasan data & analitik',
    href: '/ilp/dashboard',
    gradient: 'from-indigo-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 13a9 9 0 1118 0" className="opacity-50" />
        <path d="M12 7v6l4 2" />
      </svg>
    )
  },
  {
    key: 'pendaftaran',
    title: 'Pendaftaran',
    desc: 'Registrasi kunjungan',
    href: '/ilp/pendaftaran',
    gradient: 'from-emerald-500 to-teal-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16M4 12h10M4 18h7" />
      </svg>
    )
  },
  {
    key: 'pelayanan',
    title: 'Pelayanan',
    desc: 'Input & kelola layanan',
    href: '/ilp/pelayanan',
    gradient: 'from-fuchsia-500 to-pink-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M9 12l2 2 4-4" />
        <rect x="3" y="3" width="18" height="18" rx="4" ry="4" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'faktorResiko',
    title: 'Faktor Risiko',
    desc: 'Pantau risiko ILP',
    href: '/ilp/faktor-resiko',
    gradient: 'from-orange-500 to-rose-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M12 8v8" />
        <path d="M8 12h8" />
      </svg>
    )
  },
  {
    key: 'pendaftaranCkg',
    title: 'Pendaftaran CKG',
    desc: 'Entry & monitoring CKG',
    href: '/ilp/pendaftaran-ckg',
    gradient: 'from-cyan-500 to-sky-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M5 12h14" />
        <path d="M12 5v14" />
      </svg>
    )
  },
  {
    key: 'sasaranCkg',
    title: 'Sasaran CKG',
    desc: 'Target & tindak lanjut',
    href: '/ilp/sasaran-ckg',
    gradient: 'from-violet-500 to-purple-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <circle cx="12" cy="12" r="3" />
        <path d="M12 2v3M12 19v3M2 12h3M19 12h3" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'dashboardCkg',
    title: 'Dashboard CKG',
    desc: 'Ringkasan CKG',
    href: '/ilp/dashboard-ckg',
    gradient: 'from-sky-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 10h18" />
        <path d="M12 3v18" />
      </svg>
    )
  },
  {
    key: 'dashboardPws',
    title: 'Dashboard Analisa CKG',
    desc: 'Analisis PWS & tren',
    href: '/ilp/dashboard-pws',
    gradient: 'from-rose-500 to-red-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 16l6-6 4 4 6-8" />
      </svg>
    )
  },
  {
    key: 'dataSiswaSekolah',
    title: 'Data Siswa Sekolah',
    desc: 'Manajemen data siswa',
    href: '/ilp/data-siswa-sekolah',
    gradient: 'from-lime-500 to-green-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16" />
        <path d="M4 12h16" />
        <path d="M4 18h16" />
      </svg>
    )
  },
  {
    key: 'dashboardSekolah',
    title: 'Dashboard Sekolah',
    desc: 'Analitik & ringkasan',
    href: '/ilp/dashboard-sekolah',
    gradient: 'from-amber-500 to-yellow-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 12h18" />
        <path d="M12 3v18" />
      </svg>
    )
  },
  {
    key: 'analisaCkgSekolah',
    title: 'Analisa CKG Sekolah',
    desc: 'Analitik CKG per sekolah',
    href: '/ilp/analisa-ckg-sekolah',
    gradient: 'from-teal-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 19h16" />
        <path d="M7 15v4" />
        <path d="M12 11v8" />
        <path d="M17 6v13" />
      </svg>
    )
  },
  {
    key: 'presentasiCkg',
    title: 'Presentasi CKG',
    desc: 'Pemaparan eksekutif',
    href: '/ilp/presentasi-ckg-sekolah',
    gradient: 'from-indigo-500 to-purple-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 4h18v12H3z" />
        <path d="M12 16v4" />
        <path d="M8 20h8" />
      </svg>
    )
  }
];

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: { opacity: 1, y: 0, transition: { staggerChildren: 0.05 } }
};

const cardVariants = {
  hidden: { opacity: 0, y: 20, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 }
};

export default function IlpMenu() {
  const handleNavigate = (href) => {
    // Paksa navigasi penuh untuk menghindari intersepsi oleh library lain
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
          <h1 className="text-3xl font-bold tracking-tight bg-gradient-to-r from-violet-600 via-sky-600 to-emerald-600 bg-clip-text text-transparent">Menu ILP</h1>
          <p className="text-sm text-slate-600">Akses cepat ke fitur utama ILP</p>
        </div>
        <div className="flex items-center gap-2">
          <span className="inline-flex items-center rounded-full bg-gradient-to-r from-slate-100 to-slate-200 px-3 py-1 text-xs font-medium text-slate-700 shadow-sm border border-slate-200/60">
            v1.0
          </span>
        </div>
      </div>

      {/* Cards Row */}
      <motion.div
        className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4"
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
            onClick={(e) => { e.preventDefault(); handleNavigate(item.href); }}
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
      <div className="mt-6 text-[11px] text-slate-400">
        Tip: Klik kartu untuk membuka halaman terkait.
      </div>
    </div>
  );
}
