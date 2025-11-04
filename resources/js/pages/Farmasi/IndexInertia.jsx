import React, { useMemo, useState } from "react";
// Removed framer-motion to prevent runtime errors on Farmasi index

const tabs = ["Master Data", "Transaksi", "Laporan Farmasi"];

const masterCards = [
  { title: "Data Obat", href: "/farmasi/data-obat", icon: "fas fa-pills", color: "from-blue-500 to-indigo-600" },
  { title: "Kategori Obat", href: "/farmasi/kategori-obat", icon: "fas fa-tags", color: "from-fuchsia-500 to-pink-600" },
  { title: "Satuan Barang", href: "/farmasi/satuan-barang", icon: "fas fa-balance-scale", color: "from-yellow-500 to-amber-600" },
  { title: "Jenis Obat", href: "/farmasi/jenis-obat", icon: "fas fa-prescription-bottle", color: "from-red-500 to-rose-600" },
  { title: "Golongan Obat", href: "/farmasi/golongan-obat", icon: "fas fa-layer-group", color: "from-gray-600 to-gray-800" },
  { title: "Set Harga Obat", href: "/farmasi/set-harga-obat", icon: "fas fa-money-bill", color: "from-emerald-500 to-green-600" },
  { title: "Data Suplier", href: "/farmasi/data-suplier", icon: "fas fa-truck", color: "from-green-600 to-teal-600" },
  { title: "Industri Farmasi", href: "/farmasi/industri-farmasi", icon: "fas fa-industry", color: "from-cyan-500 to-blue-600" },
];

const transaksiCards = [
  { title: "Permintaan Medis", href: "/farmasi/permintaan-medis", icon: "fas fa-file-medical", color: "from-green-500 to-emerald-600" },
  { title: "Pembelian Obat", href: "/farmasi/pembelian-obat", icon: "fas fa-shopping-cart", color: "from-yellow-500 to-amber-600" },
  { title: "Penjualan Obat", href: "/farmasi/penjualan-obat", icon: "fas fa-cash-register", color: "from-orange-500 to-amber-600" },
  { title: "Resep Obat", href: "/farmasi/resep-obat", icon: "fas fa-notes-medical", color: "from-red-500 to-rose-600" },
  { title: "Stok Opname", href: "/farmasi/stok-opname", icon: "fas fa-clipboard-check", color: "from-teal-500 to-cyan-600" },
  { title: "Riwayat Transaksi Gudang", href: "/farmasi/riwayat-transaksi-gudang", icon: "fas fa-history", color: "from-slate-500 to-gray-700" },
];

const laporanCards = [
  { title: "Stok Obat", href: "/farmasi/stok-obat", icon: "fas fa-boxes", color: "from-indigo-500 to-violet-600" },
  { title: "Data Opname", href: "/farmasi/data-opname", icon: "fas fa-clipboard-list", color: "from-blue-500 to-sky-600" },
];

const containerVariants = {
  hidden: { opacity: 0, y: 8 },
  show: { opacity: 1, y: 0, transition: { staggerChildren: 0.05, when: "beforeChildren" } },
  exit: { opacity: 0, y: -8 },
};

const itemVariants = {
  hidden: { opacity: 0, scale: 0.98 },
  show: { opacity: 1, scale: 1 },
};

const TabButton = ({ label, active, onClick }) => (
  <button
    onClick={onClick}
    className={`px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
      ${active ? "bg-blue-600 text-white shadow" : "bg-white text-gray-700 hover:bg-gray-100"}
    `}
  >
    {label}
  </button>
);

const Card = ({ title, href, icon, color }) => (
  <a
    href={href}
    className="group block rounded-xl border border-gray-200 bg-white shadow hover:shadow-lg transition-transform hover:-translate-y-0.5"
  >
    <div className="p-4 flex items-center gap-4">
      <div className={`w-12 h-12 rounded-full bg-gradient-to-r ${color} flex items-center justify-center text-white shadow-inner`}> 
        <i className={`${icon}`}></i>
      </div>
      <div className="flex-1">
        <h4 className="text-gray-900 font-semibold">{title}</h4>
        <p className="text-xs text-gray-500">Klik untuk membuka</p>
      </div>
      <div className="text-gray-400 group-hover:text-blue-600 transition-colors">
        <i className="fas fa-arrow-right"></i>
      </div>
    </div>
  </a>
);

export default function FarmasiIndex() {
  const [activeTab, setActiveTab] = useState(tabs[0]);

  const cards = useMemo(() => {
    if (activeTab === "Master Data") return masterCards;
    if (activeTab === "Transaksi") return transaksiCards;
    return laporanCards;
  }, [activeTab]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      {/* Header */}
      <div className="mt-2 mb-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div className="px-6 py-6">
          <h2 className="text-2xl font-bold">Farmasi</h2>
          <p className="opacity-90">Kelola data, transaksi, dan laporan farmasi dengan cepat.</p>
        </div>
      </div>

      {/* Tabs */}
      <div className="mb-4">
        <div className="inline-flex gap-2 p-1 bg-white rounded-lg shadow ring-1 ring-gray-200">
          {tabs.map((t) => (
            <TabButton key={t} label={t} active={activeTab === t} onClick={() => setActiveTab(t)} />
          ))}
        </div>
      </div>

      {/* Tab Content */}
      <div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {cards.map((c) => (
            <Card key={c.href} {...c} />
          ))}
        </div>
      </div>
    </div>
  );
}