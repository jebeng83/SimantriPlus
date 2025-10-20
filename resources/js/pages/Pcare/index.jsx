import React, { useState } from 'react';
import { motion } from 'framer-motion';

const menuItems = [
  {
    key: 'statusPendaftaran',
    title: 'Status Pendaftaran',
    desc: 'Grafik & perbandingan',
    href: '/pcare/status-pendaftaran',
    gradient: 'from-sky-500 to-blue-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 16l4-4 3 3 5-7" />
      </svg>
    )
  },
  {
    key: 'dataPendaftaran',
    title: 'Data Pendaftaran',
    desc: 'Daftar & detail pendaftaran',
    href: '/pcare/data-pendaftaran',
    gradient: 'from-emerald-500 to-teal-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M4 6h16M4 12h10M4 18h7" />
      </svg>
    )
  },
  {
    key: 'dataKunjungan',
    title: 'Data Kunjungan',
    desc: 'Kunjungan pasien PCare',
    href: '/pcare/data-kunjungan',
    gradient: 'from-lime-500 to-green-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M5 12h14" />
        <path d="M12 5v14" />
      </svg>
    )
  },
  {
    key: 'refPoli',
    title: 'Referensi Poli',
    desc: 'Daftar poli FKTP',
    href: '/pcare/ref/poli',
    gradient: 'from-amber-500 to-orange-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M3 12h18" />
        <path d="M12 3v18" />
      </svg>
    )
  },
  {
    key: 'refDokter',
    title: 'Referensi Dokter',
    desc: 'Daftar dokter FKTP',
    href: '/pcare/ref/dokter',
    gradient: 'from-rose-500 to-red-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
        <path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
      </svg>
    )
  },
  {
    key: 'cekPesertaNik',
    title: 'Cek Peserta by NIK',
    desc: 'Cari data peserta BPJS',
    href: '/pcare/data-peserta-by-nik',
    gradient: 'from-indigo-500 to-purple-600',
    icon: (
      <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="4" y="4" width="16" height="12" rx="2" />
        <path d="M8 20h8" />
      </svg>
    )
  }
];

const containerVariants = {
  hidden: { opacity: 0, y: 10 },
  visible: { opacity: 1, y: 0, transition: { staggerChildren: 0.04 } }
};

const cardVariants = {
  hidden: { opacity: 0, y: 16, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 }
};

const tabs = [
  { key: 'referensi', label: 'Referensi Pcare' },
  { key: 'mapping', label: 'Mapping Pcare' },
  { key: 'layanan', label: 'Layanan Pcare' },
];

const cardsByTab = {
  referensi: [
    {
      key: 'refPoli',
      title: 'Referensi Poli',
      desc: 'Daftar poli FKTP',
      href: '/pcare/ref/poli',
      gradient: 'from-amber-500 to-orange-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M3 12h18" />
          <path d="M12 3v18" />
        </svg>
      )
    },
    {
      key: 'refDokter',
      title: 'Referensi Dokter',
      desc: 'Daftar dokter FKTP',
      href: '/pcare/ref/dokter',
      gradient: 'from-rose-500 to-red-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" />
          <path d="M6 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
        </svg>
      )
    },
  ],
  mapping: [
    {
      key: 'mapPoli',
      title: 'Mapping Poli PCare',
      desc: 'Map kode poli RS → PCare',
      href: '#',
      gradient: 'from-cyan-500 to-sky-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h10M4 18h7" />
        </svg>
      )
    },
    {
      key: 'mapDokter',
      title: 'Mapping Dokter PCare',
      desc: 'Map dokter RS → PCare',
      href: '#',
      gradient: 'from-fuchsia-500 to-pink-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
      )
    },
  ],
  layanan: [
    {
      key: 'formPendaftaran',
      title: 'Form Pendaftaran',
      desc: 'Daftarkan peserta ke PCare',
      href: '/pcare/form-pendaftaran',
      gradient: 'from-blue-500 to-indigo-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h10M4 18h7" />
        </svg>
      )
    },
    {
      key: 'statusPendaftaran',
      title: 'Status Pendaftaran',
      desc: 'Lihat status terkini',
      href: '/pcare/status-pendaftaran',
      gradient: 'from-sky-500 to-blue-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 16l4-4 3 3 5-7" />
        </svg>
      )
    },
    {
      key: 'dataPendaftaran',
      title: 'Data Pendaftaran',
      desc: 'Daftar & detail pendaftaran',
      href: '/pcare/data-pendaftaran',
      gradient: 'from-emerald-500 to-teal-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M4 6h16M4 12h10M4 18h7" />
        </svg>
      )
    },
    {
      key: 'cekPesertaNik',
      title: 'Cek Peserta by NIK',
      desc: 'Cari data peserta BPJS',
      href: '/pcare/data-peserta-by-nik',
      gradient: 'from-indigo-500 to-purple-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <rect x="4" y="4" width="16" height="12" rx="2" />
          <path d="M8 20h8" />
        </svg>
      )
    },
    {
      key: 'dataKunjungan',
      title: 'Data Kunjungan',
      desc: 'Kunjungan pasien PCare',
      href: '/pcare/data-kunjungan',
      gradient: 'from-lime-500 to-green-600',
      icon: (
        <svg viewBox="0 0 24 24" className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
      )
    },
  ],
};

export default function PcareMenu() {
  const [activeTab, setActiveTab] = useState('layanan');
  return (
    <div className="w-full">
      {/* Header */}
      <div className="mb-4 flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800 tracking-tight">Menu PCare</h1>
          <p className="text-xs text-slate-500">Akses cepat ke fitur utama PCare BPJS</p>
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
        {cardsByTab[activeTab].map((item) => (
          <motion.a
            href={item.href}
            key={item.key}
            variants={cardVariants}
            whileHover={{ y: -3, scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            className="group relative flex flex-col rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-colors hover:border-slate-300 hover:bg-white hover:shadow-md min-h-[110px]"
          >
            <div className={`flex h-9 w-9 items-center justify-center rounded-md bg-gradient-to-br ${item.gradient} text-white shadow`} >
              {item.icon}
            </div>
            <div className="mt-2">
              <div className="text-slate-800 font-semibold leading-tight">
                {item.title} <span className="text-slate-400">›</span>
              </div>
              <div className="text-slate-500 text-xs">{item.desc}</div>
            </div>
          </motion.a>
        ))}
      </motion.div>

      {/* Footer Note */}
      <div className="mt-4 text-[10px] text-slate-400">Tip: Pilih tab untuk melihat kartu terkait.</div>
    </div>
  );
}