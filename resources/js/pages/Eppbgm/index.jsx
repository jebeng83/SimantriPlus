import React from 'react';
import { motion } from 'framer-motion';

const menuItems = [
  {
    key: 'dataIbuHamil',
    title: 'Data Ibu Hamil',
    desc: 'Input & monitoring ibu hamil',
    href: '/anc/data-ibu-hamil',
    gradient: 'from-rose-500 to-red-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
        <path d="M6 22c0-3.314 2.686-6 6-6s6 2.686 6 6" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'partograf',
    title: 'Partograf',
    desc: 'Pencatatan persalinan',
    href: '/anc/partograf',
    gradient: 'from-indigo-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16M4 12h10M4 18h7" />
      </svg>
    )
  },
  {
    key: 'dataBalitaSakit',
    title: 'Data Balita Sakit',
    desc: 'Pencatatan kasus balita',
    href: '/anc/data-balita-sakit',
    gradient: 'from-amber-500 to-yellow-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M12 3l3 3-3 3-3-3 3-3z" />
        <path d="M6 14h12M9 17h6" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'dataRematri',
    title: 'Data Rematri',
    desc: 'Pendataan remaja putri',
    href: '/anc/data-rematri',
    gradient: 'from-fuchsia-500 to-pink-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <circle cx="8" cy="8" r="3" />
        <circle cx="16" cy="8" r="3" />
        <path d="M4 21c0-3.314 2.686-6 6-6" className="opacity-50" />
        <path d="M14 15c3.314 0 6 2.686 6 6" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'dataIbuNifas',
    title: 'Data Ibu Nifas',
    desc: 'Pemantauan masa nifas',
    href: '/anc/data-ibu-nifas',
    gradient: 'from-emerald-500 to-teal-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M12 4a4 4 0 014 4v1a4 4 0 11-8 0V8a4 4 0 014-4z" />
        <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8" className="opacity-50" />
      </svg>
    )
  },
  {
    key: 'panduan',
    title: 'Panduan & Tips',
    desc: 'Cara pakai & troubleshooting',
    href: '#panduan',
    gradient: 'from-sky-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16M4 12h12M4 18h8" />
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

export default function EppbgmMenu() {
  return (
    <div className="w-full">
      {/* Header */}
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-semibold text-slate-800 tracking-tight">Menu ePPBGM</h1>
          <p className="text-sm text-slate-500">Akses cepat ke fitur utama ePPBGM (berbasis ANC)</p>
        </div>
        <div className="flex items-center gap-2">
          <span className="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
            v1.0
          </span>
        </div>
      </div>

      {/* Cards Row: small cards, single row up to 6 cols */}
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

      {/* Panduan Section */}
      <div id="panduan" className="mt-8">
        <div className="rounded-xl border border-slate-200 bg-white/80 p-4 shadow-sm">
          <h2 className="text-base font-semibold text-slate-800">Panduan Singkat ePPBGM</h2>
          <ul className="mt-2 text-sm text-slate-600 list-disc pl-4 space-y-1">
            <li>Gunakan Data Ibu Hamil untuk input dan pemantauan trimester.</li>
            <li>Kelola Partograf untuk pencatatan persalinan terstruktur.</li>
            <li>Catat kasus Balita Sakit untuk tindak lanjut di posyandu.</li>
            <li>Data Rematri untuk pemantauan remaja putri dan intervensi.</li>
            <li>Data Ibu Nifas untuk monitoring masa nifas dan konseling.</li>
          </ul>
          <p className="mt-3 text-[11px] text-slate-400">Tip: Klik kartu di atas untuk membuka halaman terkait.</p>
        </div>
      </div>
    </div>
  );
}