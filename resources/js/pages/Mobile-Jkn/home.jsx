import React from 'react';
import { motion } from 'framer-motion';

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { staggerChildren: 0.08, duration: 0.35 }
  }
};

const cardVariants = {
  hidden: { opacity: 0, scale: 0.96 },
  visible: { opacity: 1, scale: 1 },
};

const icons = {
  plus: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <path d="M12 5v14M5 12h14" />
    </svg>
  ),
  hospital: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <rect x="4" y="3" width="16" height="18" rx="2" />
      <path d="M12 7v6M9 10h6" />
    </svg>
  ),
  doctor: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <circle cx="12" cy="7" r="4" />
      <path d="M5 21c0-4 3-7 7-7s7 3 7 7" />
      <path d="M8 21h8" />
    </svg>
  ),
  clock: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <circle cx="12" cy="12" r="9" />
      <path d="M12 7v6l4 2" />
    </svg>
  ),
  id: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <rect x="3" y="5" width="18" height="14" rx="2" />
      <circle cx="8" cy="12" r="3" />
      <path d="M14 10h6M14 14h6" />
    </svg>
  ),
  book: (
    <svg viewBox="0 0 24 24" className="h-7 w-7" fill="none" stroke="currentColor" strokeWidth="2">
      <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
      <path d="M6.5 2H20v15H6.5A2.5 2.5 0 0 0 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2z" />
    </svg>
  )
};

const items = [
  {
    title: 'Pendaftaran Antrean',
    description: 'Registrasi kunjungan',
    href: '/antrol-bpjs/pendaftaran-mobile-jkn',
    gradient: 'from-blue-500 to-sky-500',
    icon: icons.plus,
  },
  {
    title: 'Referensi Poli HFIS',
    description: 'Daftar poli tersedia',
    href: '/antrol-bpjs/referensi-poli-hfis',
    gradient: 'from-emerald-500 to-green-500',
    icon: icons.hospital,
  },
  {
    title: 'Referensi Dokter HFIS',
    description: 'Jadwal & kapasitas',
    href: '/antrol-bpjs/referensi-dokter-hfis',
    gradient: 'from-orange-500 to-amber-500',
    icon: icons.doctor,
  },
  {
    title: 'Sisa Antrean',
    description: 'Pantau status pelayanan',
    href: '/antrol-bpjs/pendaftaran-mobile-jkn?tab=status',
    gradient: 'from-indigo-500 to-violet-500',
    icon: icons.clock,
  },
  {
    title: 'Cek Peserta',
    description: 'Cari peserta BPJS',
    href: '/antrol-bpjs/pendaftaran-mobile-jkn?tab=peserta',
    gradient: 'from-pink-500 to-rose-500',
    icon: icons.id,
  },
  {
    title: 'Panduan & Debug',
    description: 'Tips & simulasi',
    href: '/antrol-bpjs/pendaftaran-mobile-jkn?debug=skrining',
    gradient: 'from-slate-600 to-gray-600',
    icon: icons.book,
  },
];

function Card({ item }) {
  return (
    <motion.a
      variants={cardVariants}
      whileHover={{ scale: 1.02, y: -2 }}
      whileTap={{ scale: 0.98 }}
      href={item.href}
      className="group relative flex flex-col rounded-xl border border-slate-200 bg-white/70 p-3 shadow-sm backdrop-blur-sm transition-colors hover:border-slate-300 hover:bg-white"
    >
      <div className="absolute -top-3 -left-3 h-16 w-16 rounded-xl bg-gradient-to-br opacity-90 blur-xl group-hover:opacity-100 group-hover:blur-2 transition" style={{ backgroundImage: `linear-gradient(to bottom right, var(--tw-gradient-stops))` }}></div>
      <div className={`flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br ${item.gradient} text-white shadow-md`}> 
        {item.icon}
      </div>
      <div className="mt-3">
        <div className="text-slate-800 font-semibold leading-tight">
          {item.title} <span className="text-slate-400">›</span>
        </div>
        <div className="text-slate-500 text-sm">{item.description}</div>
      </div>
    </motion.a>
  );
}

export default function MobileJknHome() {
  return (
    <div className="min-h-[calc(100vh-130px)] w-full">
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Menu Antrol BPJS</h1>
          <p className="text-slate-500">Akses cepat ke fitur Antrol BPJS</p>
        </div>
        <div className="text-xs text-slate-400">v1.0</div>
      </div>

      <motion.div
        variants={containerVariants}
        initial="hidden"
        animate="visible"
        className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4"
      >
        {items.map((item, idx) => (
          <Card key={idx} item={item} />
        ))}
      </motion.div>

      <p className="text-xs text-slate-400 mt-2">Tip: Klik kartu untuk membuka halaman terkait.</p>
    </div>
  );
}