import React, { useEffect, useState } from 'react';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import AppLayout from '../../Layouts/AppLayout.jsx';

// Util: kunci harga yang didukung
const PRICE_KEYS = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];

// Safe number parser
const toNumber = (val) => {
  const n = Number(val);
  return Number.isFinite(n) ? n : 0;
};

// Normalisasi nilai untuk input tanggal (type="date") agar sesuai format "yyyy-MM-dd"
// Menghindari nilai seperti "0000-00-00" atau string dengan waktu yang menyebabkan warning browser
const normalizeDateValue = (val) => {
  if (!val) return '';
  // Zero-date dari MySQL tidak valid untuk input date HTML
  if (val === '0000-00-00' || val === '0000-00-00 00:00:00') return '';
  // Jika Date object
  if (val instanceof Date && !isNaN(val)) {
    return val.toISOString().slice(0, 10);
  }
  const str = String(val);
  // Ambil hanya bagian tanggal "YYYY-MM-DD" bila string mengandung waktu
  const m = str.match(/^(\d{4}-\d{2}-\d{2})/);
  if (m) return m[1];
  return '';
};

// Ambil harga dasar dari item sesuai konfigurasi hargadasar
// Preferensi field: h_beli > dasar > h_diskon (jika tersedia)
const pickBasePrice = (item, hargadasar) => {
  if (hargadasar === 'Harga Beli') {
    return toNumber(item?.h_beli ?? item?.dasar);
  }
  // Harga Diskon: fallback ke h_diskon bila ada, jika tidak, gunakan dasar/h_beli
  return toNumber(item?.h_diskon ?? item?.dasar ?? item?.h_beli);
};

// Hitung satu jenis harga jual (mis. ralan, kelas1, dst) berdasarkan konfigurasi set_harga_obat
// Params:
// - item: row databarang minimal berisi { h_beli?, dasar?, kdjns?, kode_brng? }
// - priceKey: salah satu dari PRICE_KEYS (default 'ralan')
// - config: { setharga: 'Umum'|'Per Jenis'|'Per Barang', hargadasar: 'Harga Beli'|'Harga Diskon', ppn: 'Yes'|'No' }
// - percentages: object berisi salah satu sumber persentase sesuai config.setharga
//    { umum?: RowUmum, jenis?: RowJenis, barang?: RowBarang }
//    Di setiap Row expected memiliki keys PRICE_KEYS dengan nilai persen (0-100)
export const calcHargaObat = ({ item, priceKey = 'ralan', config, percentages }) => {
  if (!PRICE_KEYS.includes(priceKey)) {
    console.warn('priceKey tidak dikenal:', priceKey);
    return 0;
  }
  if (!config || !config.setharga || !config.hargadasar) {
    console.warn('config set_harga_obat tidak lengkap');
  }

  const base = pickBasePrice(item, config?.hargadasar);
  let persen = 0;
  switch (config?.setharga) {
    case 'Umum':
      persen = toNumber(percentages?.umum?.[priceKey]);
      break;
    case 'Per Jenis':
      // gunakan persentase berdasarkan jenis (kdjns)
      persen = toNumber(percentages?.jenis?.[priceKey]);
      break;
    case 'Per Barang':
      persen = toNumber(percentages?.barang?.[priceKey]);
      break;
    default:
      persen = 0;
  }

  // harga jual = base * (1 + persen/100)
  let harga = base * (1 + (persen / 100));
  // PPN 11%
  if (config?.ppn === 'Yes') {
    harga *= 1.11;
  }
  return Number(harga.toFixed(2));
};

// Hitung seluruh harga (map semua PRICE_KEYS) sekaligus
export const calcSemuaHargaObat = ({ item, config, percentages }) => {
  const result = {};
  PRICE_KEYS.forEach((k) => {
    result[k] = calcHargaObat({ item, priceKey: k, config, percentages });
  });
  return result;
};

// Contoh helper async untuk mengambil persentase per barang sesuai endpoint yang sudah ada di SetHargaObat.jsx
// Catatan: endpoint global/umum & per-jenis mungkin berbeda; sesuaikan dengan backend Anda.
export async function fetchPersentasePerBarang(kode_brng) {
  if (!kode_brng) return null;
  try {
    const res = await fetch(`/farmasi/set-penjualan-barang/${encodeURIComponent(kode_brng)}`, { headers: { Accept: 'application/json' } });
    const json = await res.json();
    if (json && json.success) return json.data;
  } catch (e) {
    console.error('Gagal mengambil persentase per barang', e);
  }
  return null;
}

const PageHeader = () => (
  <div className="relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-lg">
    <div className="relative z-10">
      <h1 className="text-2xl font-bold tracking-tight">Data Obat</h1>
      {/* <p className="mt-1 opacity-90">Kelola master data obat dari tabel databarang secara cepat dan mudah.</p> */}
    </div>
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 0.2 }} className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white" />
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 0.15 }} className="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-white" />
  </div>
);

const Input = ({ label, id, type = 'text', value, onChange, maxLength, placeholder, disabled, error }) => (
  <div className="space-y-1">
    <label htmlFor={id} className="block text-sm font-medium text-gray-700">{label}</label>
    <input
      id={id}
      type={type}
      value={value}
      onChange={onChange}
      maxLength={maxLength}
      placeholder={placeholder}
      disabled={disabled}
      className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 disabled:bg-gray-100 disabled:text-gray-500 ${error ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}
    />
    {error && <p className="text-xs text-red-600">{error}</p>}
  </div>
);

const ConfirmDelete = ({ open, onClose, onConfirm, item }) => (
  <AnimatePresence>
    {open && (
      <motion.div className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
        <motion.div initial={{ y: 50, opacity: 0 }} animate={{ y: 0, opacity: 1 }} exit={{ y: 50, opacity: 0 }} transition={{ type: 'spring', stiffness: 260, damping: 20 }} className="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 className="text-lg font-semibold">Hapus Obat</h3>
          <p className="mt-2 text-sm text-gray-600">Anda yakin ingin menghapus <span className="font-medium text-gray-900">{item?.nama_brng}</span> ({item?.kode_brng})?</p>
          <div className="mt-6 flex justify-end gap-3">
            <button onClick={onClose} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
            <button onClick={onConfirm} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus</button>
          </div>
        </motion.div>
      </motion.div>
    )}
  </AnimatePresence>
);

// Aksen animasi halus untuk tampilan premium
const AnimatedAccent = () => {
  const [pos, setPos] = useState(0);
  React.useEffect(() => {
    let rafId = 0;
    const tick = () => {
      setPos((prev) => (prev + 0.8) % 200);
      rafId = requestAnimationFrame(tick);
    };
    rafId = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(rafId);
  }, []);
  return (
    <div
      className="h-1 rounded-full"
      style={{
        backgroundImage: 'linear-gradient(90deg, #6366f1, #22d3ee, #a78bfa)',
        backgroundSize: '200% 100%',
        backgroundPosition: `${pos}% 0`,
      }}
    />
  );
};

const ObatModal = ({ open, isEdit, onClose, onSubmit, data, setData, errors, processing, dropdowns, autoHargaAktif, onToggleAutoHarga, onManualRecompute }) => (
  <AnimatePresence>
    {open && (
      <motion.div
        className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center"
        style={{ perspective: 1200 }}
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        transition={{ type: 'spring', stiffness: 110, damping: 20 }}
      >
        <motion.div
          layout
          initial={{ y: 0, opacity: 0, scale: 0.96, rotateX: -12, rotateY: -8 }}
          animate={{ y: 0, opacity: 1, scale: 1, rotateX: 0, rotateY: 0 }}
          exit={{ y: 0, opacity: 0, scale: 0.96, rotateX: 12, rotateY: 8 }}
          transition={{ type: 'spring', stiffness: 140, damping: 16 }}
          whileHover={{ rotateX: -2, rotateY: 2, scale: 1.01, boxShadow: '0 25px 65px rgba(99,102,241,0.22)' }}
          className="mx-auto w-full max-w-3xl rounded-xl bg-white p-6 shadow-2xl max-h-[85vh] overflow-y-auto"
          style={{ transformStyle: 'preserve-3d', transformOrigin: '50% 20%', backfaceVisibility: 'hidden', boxShadow: '0 15px 40px rgba(0,0,0,0.12)' }}
        >
          <div className="sticky top-0 z-10 bg-white/95 backdrop-blur flex items-start justify-between pb-2 border-b">
            <div>
              <h3 className="text-lg font-semibold">{isEdit ? 'Edit Obat' : 'Tambah Obat'}</h3>
              <p className="text-sm text-gray-600">Lengkapi informasi obat di bawah ini.</p>
            </div>
            <button onClick={onClose} className="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Tutup">✕</button>
          </div>
          <AnimatedAccent />

          {/* Toggle auto-hitungan harga + tombol hitung manual */}
          <div className="mt-3 mb-2 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <label className="text-xs font-medium text-gray-700">Auto Hitung Harga</label>
              <input
                type="checkbox"
                checked={!!autoHargaAktif}
                onChange={(e) => onToggleAutoHarga?.(e.target.checked)}
              />
            </div>
            <div className="flex items-center gap-2">
              <motion.button type="button" onClick={onManualRecompute} className="rounded-lg border border-indigo-300 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>
                Hitung Sekarang
              </motion.button>
            </div>
          </div>

          <motion.form layout onSubmit={onSubmit} className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2" initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} transition={{ type: 'spring', stiffness: 160, damping: 18 }}>
            <Input label="Kode Barang" id="kode_brng" value={data.kode_brng} onChange={(e) => setData('kode_brng', e.target.value.toUpperCase())} maxLength={15} placeholder="Mis. A000000000" disabled={isEdit} error={errors?.kode_brng} />
            <Input label="Nama Barang" id="nama_brng" value={data.nama_brng} onChange={(e) => setData('nama_brng', e.target.value)} maxLength={80} placeholder="Nama obat" error={errors?.nama_brng} />
            <div className="space-y-1">
              <label htmlFor="kode_sat" className="block text-sm font-medium text-gray-700">Kode Satuan</label>
              <select id="kode_sat" value={data.kode_sat} onChange={(e) => setData('kode_sat', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kode_sat ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Satuan</option>
                {dropdowns?.kodesatuan?.map((opt) => (<option key={opt.kode_sat} value={opt.kode_sat}>{opt.kode_sat} - {opt.satuan}</option>))}
              </select>
              {errors?.kode_sat && <p className="text-xs text-red-600">{errors.kode_sat}</p>}
            </div>
            <div className="space-y-1">
              <label htmlFor="kode_satbesar" className="block text-sm font-medium text-gray-700">Kode Satuan Besar</label>
              <select id="kode_satbesar" value={data.kode_satbesar} onChange={(e) => setData('kode_satbesar', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kode_satbesar ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Satuan Besar</option>
                {dropdowns?.kodesatuan?.map((opt) => (<option key={opt.kode_sat} value={opt.kode_sat}>{opt.kode_sat} - {opt.satuan}</option>))}
              </select>
              {errors?.kode_satbesar && <p className="text-xs text-red-600">{errors.kode_satbesar}</p>}
            </div>
            <Input label="Harga Dasar" id="dasar" type="number" value={data.dasar} onChange={(e) => setData('dasar', e.target.value)} placeholder="0" error={errors?.dasar} />
            <Input label="Harga Beli" id="h_beli" type="number" value={data.h_beli} onChange={(e) => setData('h_beli', e.target.value)} placeholder="0" error={errors?.h_beli} />
            <Input label="Harga Ralan" id="ralan" type="number" value={data.ralan} onChange={(e) => setData('ralan', e.target.value)} placeholder="0" error={errors?.ralan} />
            <Input label="Harga Kelas 1" id="kelas1" type="number" value={data.kelas1} onChange={(e) => setData('kelas1', e.target.value)} placeholder="0" error={errors?.kelas1} />
            <Input label="Harga Kelas 2" id="kelas2" type="number" value={data.kelas2} onChange={(e) => setData('kelas2', e.target.value)} placeholder="0" error={errors?.kelas2} />
            <Input label="Harga Kelas 3" id="kelas3" type="number" value={data.kelas3} onChange={(e) => setData('kelas3', e.target.value)} placeholder="0" error={errors?.kelas3} />
            <Input label="Harga Utama" id="utama" type="number" value={data.utama} onChange={(e) => setData('utama', e.target.value)} placeholder="0" error={errors?.utama} />
            <Input label="Harga VIP" id="vip" type="number" value={data.vip} onChange={(e) => setData('vip', e.target.value)} placeholder="0" error={errors?.vip} />
            <Input label="Harga VVIP" id="vvip" type="number" value={data.vvip} onChange={(e) => setData('vvip', e.target.value)} placeholder="0" error={errors?.vvip} />
            <Input label="Harga Beli Luar" id="beliluar" type="number" value={data.beliluar} onChange={(e) => setData('beliluar', e.target.value)} placeholder="0" error={errors?.beliluar} />
            <Input label="Harga Jual Bebas" id="jualbebas" type="number" value={data.jualbebas} onChange={(e) => setData('jualbebas', e.target.value)} placeholder="0" error={errors?.jualbebas} />
            <Input label="Harga Karyawan" id="karyawan" type="number" value={data.karyawan} onChange={(e) => setData('karyawan', e.target.value)} placeholder="0" error={errors?.karyawan} />
            <Input label="Isi" id="isi" type="number" value={data.isi} onChange={(e) => setData('isi', e.target.value)} placeholder="1" error={errors?.isi} />
            <Input label="Kapasitas" id="kapasitas" type="number" value={data.kapasitas} onChange={(e) => setData('kapasitas', e.target.value)} placeholder="0" error={errors?.kapasitas} />
            <Input label="Status (0/1)" id="status" value={data.status} onChange={(e) => setData('status', e.target.value)} maxLength={1} placeholder="1" error={errors?.status} />
            <Input label="Letak Barang" id="letak_barang" value={data.letak_barang} onChange={(e) => setData('letak_barang', e.target.value)} maxLength={100} placeholder="Apotek" error={errors?.letak_barang} />
            <Input label="Stok Minimal" id="stokminimal" type="number" value={data.stokminimal} onChange={(e) => setData('stokminimal', e.target.value)} placeholder="" error={errors?.stokminimal} />
            <div className="space-y-1">
              <label htmlFor="kdjns" className="block text-sm font-medium text-gray-700">Jenis Obat (kdjns)</label>
              <select id="kdjns" value={data.kdjns} onChange={(e) => setData('kdjns', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kdjns ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Jenis</option>
                {dropdowns?.jenis?.map((opt) => (<option key={opt.kdjns} value={opt.kdjns}>{opt.kdjns} - {opt.nama}</option>))}
              </select>
              {errors?.kdjns && <p className="text-xs text-red-600">{errors.kdjns}</p>}
            </div>
            <Input label="Tanggal Expire" id="expire" type="date" value={normalizeDateValue(data.expire)} onChange={(e) => setData('expire', e.target.value)} placeholder="" error={errors?.expire} />
            <div className="space-y-1">
              <label htmlFor="kode_industri" className="block text-sm font-medium text-gray-700">Kode Industri</label>
              <select id="kode_industri" value={data.kode_industri} onChange={(e) => setData('kode_industri', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kode_industri ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Industri</option>
                {dropdowns?.industrifarmasi?.map((opt) => (<option key={opt.kode_industri} value={opt.kode_industri}>{opt.kode_industri} - {opt.nama_industri}</option>))}
              </select>
              {errors?.kode_industri && <p className="text-xs text-red-600">{errors.kode_industri}</p>}
            </div>
            <div className="space-y-1">
              <label htmlFor="kode_kategori" className="block text-sm font-medium text-gray-700">Kode Kategori</label>
              <select id="kode_kategori" value={data.kode_kategori} onChange={(e) => setData('kode_kategori', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kode_kategori ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Kategori</option>
                {dropdowns?.kategori_barang?.map((opt) => (<option key={opt.kode} value={opt.kode}>{opt.kode} - {opt.nama}</option>))}
              </select>
              {errors?.kode_kategori && <p className="text-xs text-red-600">{errors.kode_kategori}</p>}
            </div>
            <div className="space-y-1">
              <label htmlFor="kode_golongan" className="block text-sm font-medium text-gray-700">Kode Golongan</label>
              <select id="kode_golongan" value={data.kode_golongan} onChange={(e) => setData('kode_golongan', e.target.value)} className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 ${errors?.kode_golongan ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}>
                <option value="">Pilih Golongan</option>
                {dropdowns?.golongan_barang?.map((opt) => (<option key={opt.kode} value={opt.kode}>{opt.kode} - {opt.nama}</option>))}
              </select>
              {errors?.kode_golongan && <p className="text-xs text-red-600">{errors.kode_golongan}</p>}
            </div>
            <div className="md:col-span-2 mt-2 flex justify-end gap-3">
              <motion.button type="button" onClick={onClose} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>Batal</motion.button>
              <motion.button type="submit" disabled={processing} className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>{isEdit ? 'Simpan Perubahan' : 'Simpan'}</motion.button>
            </div>
          </motion.form>
        </motion.div>
      </motion.div>
    )}
  </AnimatePresence>
);

export default function DataObatPage() {
  const { props } = usePage();
  const { items, filters, flash, nextCode, dropdowns } = props;

  const [query, setQuery] = useState(filters?.q || '');
  const [perPage, setPerPage] = useState(filters?.perPage || 10);
  const [modalOpen, setModalOpen] = useState(false);
  const [isEdit, setIsEdit] = useState(false);
  const [selected, setSelected] = useState(null);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [isSearching, setIsSearching] = useState(false);

  // Animasi elegan untuk panel dan list
  const listVariants = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.03 } },
  };
  const itemVariants = {
    hidden: { opacity: 0, y: 6 },
    show: { opacity: 1, y: 0 },
  };
  const panelVariants = {
    hidden: { opacity: 0, y: 8 },
    show: { opacity: 1, y: 0 },
  };

  const { data, setData, post, processing, errors, reset } = useForm({
    kode_brng: '',
    nama_brng: '',
    kode_sat: '',
    kode_satbesar: '',
    dasar: 0,
    h_beli: '',
    ralan: '',
    kelas1: '',
    kelas2: '',
    kelas3: '',
    utama: '',
    vip: '',
    vvip: '',
    beliluar: '',
    jualbebas: '',
    karyawan: '',
    isi: 1,
    kapasitas: 0,
    status: '1',
    letak_barang: 'Apotek',
    stokminimal: '',
    kdjns: '',
    expire: '',
    kode_industri: '',
    kode_kategori: '',
    kode_golongan: ''
  });

  // Toggle: aktifkan/menonaktifkan auto-hitungan harga jual ketika form dibuka
  const [autoHargaAktif, setAutoHargaAktif] = useState(true);
  const aktifkanAutoHargaJual = () => {
    setAutoHargaAktif(true);
    // Jalankan kalkulasi awal segera setelah diaktifkan
    setTimeout(() => {
      recomputeAllPrices();
    }, 0);
  };
  const nonaktifkanAutoHargaJual = () => setAutoHargaAktif(false);

  // Konfigurasi set_harga_obat + sumber persentase (umum/jenis/barang)
  const [hargaConfig, setHargaConfig] = useState({ setharga: 'Umum', hargadasar: 'Harga Beli', ppn: 'Yes' });
  const [persenUmum, setPersenUmum] = useState(null);
  const [persenJenis, setPersenJenis] = useState(null);
  const [persenBarang, setPersenBarang] = useState(null);

  const defaultPercent = PRICE_KEYS.reduce((acc, k) => { acc[k] = 20; return acc; }, {});

  async function fetchConfigSetHargaObat() {
    try {
      // gunakan endpoint JSON khusus agar tidak menerima halaman Inertia
      const res = await fetch('/farmasi/set-harga-obat/json', { headers: { Accept: 'application/json' } });
      const json = await res.json();
      if (json && json.success && json.data) {
        const d = json.data;
        setHargaConfig({
          setharga: d.setharga || 'Umum',
          hargadasar: d.hargadasar || 'Harga Beli',
          ppn: d.ppn || 'Yes'
        });
      }
    } catch (e) {
      // fallback tetap gunakan default
      console.warn('Gagal mengambil konfigurasi set_harga_obat');
    }
  }

  async function fetchPersentaseUmum() {
    try {
      const res = await fetch('/farmasi/set-penjualan-umum', { headers: { Accept: 'application/json' } });
      const json = await res.json();
      if (json && json.success && json.data) {
        setPersenUmum(json.data);
      }
    } catch (e) {
      console.warn('Gagal mengambil persentase umum');
    }
  }

  async function fetchPersentaseJenis(kdjns) {
    if (!kdjns) return;
    try {
      const res = await fetch(`/farmasi/set-penjualan/${encodeURIComponent(kdjns)}`, { headers: { Accept: 'application/json' } });
      const json = await res.json();
      if (json && json.success && json.data) {
        setPersenJenis(json.data);
      }
    } catch (e) {
      console.warn('Gagal mengambil persentase per jenis');
    }
  }

  async function fetchPersentaseBarang(kode) {
    const row = await fetchPersentasePerBarang(kode);
    if (row) setPersenBarang(row);
  }

  // Recompute semua harga jual ketika base price / config / persentase berubah
  const recomputeAllPrices = () => {
    if (!modalOpen || !autoHargaAktif) return;
    const cfg = hargaConfig || { setharga: 'Umum', hargadasar: 'Harga Beli', ppn: 'Yes' };
    let percentages = {};
    if (cfg.setharga === 'Umum') {
      percentages = { umum: persenUmum || defaultPercent };
    } else if (cfg.setharga === 'Per Jenis') {
      percentages = { jenis: persenJenis || defaultPercent };
    } else if (cfg.setharga === 'Per Barang') {
      percentages = { barang: persenBarang || defaultPercent };
    }

    const item = { h_beli: data.h_beli, dasar: data.dasar, kdjns: data.kdjns, kode_brng: data.kode_brng };
    const all = calcSemuaHargaObat({ item, config: cfg, percentages });
    PRICE_KEYS.forEach((k) => setData(k, all[k]));
  };

  // Fetch config + persentase saat modal dibuka
  useEffect(() => {
    if (!modalOpen) return;
    // pastikan auto-hitungan aktif setiap kali form dibuka
    aktifkanAutoHargaJual();
    fetchConfigSetHargaObat();
    fetchPersentaseUmum();
    if (data.kdjns) fetchPersentaseJenis(data.kdjns);
    if (data.kode_brng) fetchPersentaseBarang(data.kode_brng);
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [modalOpen]);

  // Ambil persentase jenis jika kdjns berubah dan mode Per Jenis
  useEffect(() => {
    if (!modalOpen) return;
    if (hargaConfig?.setharga === 'Per Jenis') {
      fetchPersentaseJenis(data.kdjns);
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.kdjns, hargaConfig?.setharga, modalOpen]);

  // Ambil persentase barang jika kode_brng berubah dan mode Per Barang
  useEffect(() => {
    if (!modalOpen) return;
    if (hargaConfig?.setharga === 'Per Barang') {
      fetchPersentaseBarang(data.kode_brng);
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.kode_brng, hargaConfig?.setharga, modalOpen]);

  // Recompute ketika base price atau config/persentase berubah
  useEffect(() => {
    recomputeAllPrices();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data.h_beli, data.dasar, hargaConfig, persenUmum, persenJenis, persenBarang, modalOpen]);

  useEffect(() => {
    if (flash?.success) {
      const t = setTimeout(() => {}, 2000);
      return () => clearTimeout(t);
    }
  }, [flash]);

  const openCreate = () => {
    reset();
    setIsEdit(false);
    setSelected(null);
    setData('kode_brng', (nextCode || '').toUpperCase());
    setData('dasar', 0);
    setData('isi', 1);
    setData('kapasitas', 0);
    setData('status', '1');
    setData('letak_barang', 'Apotek');
    setData('ralan', 0);
    setData('kelas1', 0);
    setData('kelas2', 0);
    setData('kelas3', 0);
    setData('utama', 0);
    setData('vip', 0);
    setData('vvip', 0);
    setData('beliluar', 0);
    setData('jualbebas', 0);
    setData('karyawan', 0);
    setData('stokminimal', 0);
    setData('kdjns', '');
    setData('expire', '');
    setData('kode_industri', '');
    setData('kode_kategori', '');
    setData('kode_golongan', '');
    setModalOpen(true);
    // aktifkan auto-kalkulasi dan hitung harga awal
    aktifkanAutoHargaJual();
  };

  const openEdit = (item) => {
    setIsEdit(true);
    setSelected(item);
    setData({
      kode_brng: item.kode_brng || '',
      nama_brng: item.nama_brng || '',
      kode_sat: item.kode_sat || '',
      kode_satbesar: item.kode_satbesar || '',
      dasar: item.dasar ?? 0,
      h_beli: item.h_beli ?? '',
      ralan: item.ralan ?? '',
      kelas1: item.kelas1 ?? '',
      kelas2: item.kelas2 ?? '',
      kelas3: item.kelas3 ?? '',
      utama: item.utama ?? '',
      vip: item.vip ?? '',
      vvip: item.vvip ?? '',
      beliluar: item.beliluar ?? '',
      jualbebas: item.jualbebas ?? '',
      karyawan: item.karyawan ?? '',
      isi: item.isi ?? 1,
      kapasitas: item.kapasitas ?? 0,
      status: item.status ?? '1',
      letak_barang: item.letak_barang ?? 'Apotek',
      stokminimal: item.stokminimal ?? '',
      kdjns: item.kdjns ?? '',
      expire: item.expire ?? '',
      kode_industri: item.kode_industri ?? '',
      kode_kategori: item.kode_kategori ?? '',
      kode_golongan: item.kode_golongan ?? ''
    });
    setModalOpen(true);
    // aktifkan auto-kalkulasi setelah data edit di-set
    aktifkanAutoHargaJual();
  };

  const closeModal = () => setModalOpen(false);

  const submitForm = (e) => {
    e.preventDefault();
    if (isEdit && selected) {
      const kodeBrng = selected.kode_brng?.trim();
      if (!kodeBrng) {
        alert('Kode barang kosong. Pilih data yang benar sebelum menyimpan perubahan.');
        return;
      }
      const updateUrl = route('farmasi.data-obat.update', { kode_brng: kodeBrng });
      router.post(updateUrl, { ...data, _method: 'PUT' }, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => setModalOpen(false)
      });
    } else {
      post(route('farmasi.data-obat.store'), {
        preserveScroll: true,
        onSuccess: () => setModalOpen(false)
      });
    }
  };

  const confirmDelete = (item) => {
    setSelected(item);
    setConfirmOpen(true);
  };

  const performDelete = () => {
    if (!selected?.kode_brng) return;
    router.delete(route('farmasi.data-obat.destroy', { kode_brng: selected.kode_brng }), {
      preserveScroll: true,
      onSuccess: () => setConfirmOpen(false)
    });
  };

  const onSearch = (e) => {
    e.preventDefault();
    router.get(route('farmasi.data-obat'), { q: query, perPage }, { preserveState: true, replace: true });
  };

  // Debounce pencarian agar UX mirip Registration/Index.jsx
  useEffect(() => {
    const t = setTimeout(() => {
      setIsSearching(true);
      router.get(route('farmasi.data-obat'), { q: query, perPage }, {
        preserveState: true,
        replace: true,
        onFinish: () => setIsSearching(false)
      });
    }, 400);
    return () => clearTimeout(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [query]);

  const onChangePerPage = (e) => {
    const val = Number(e.target.value);
    setPerPage(val);
    router.get(route('farmasi.data-obat'), { q: query, perPage: val }, { preserveState: true, replace: true });
  };

  // Bulk update harga untuk seluruh data di tabel databarang
  const [bulkUpdating, setBulkUpdating] = useState(false);
  const updateHargaSemua = () => {
    if (!window.confirm('Update semua harga jual di tabel databarang sekarang?')) return;
    setBulkUpdating(true);
    const namedUrl = route('farmasi.data-obat.update-harga-semua');
    router.post(namedUrl, { _method: 'PUT' }, {
      forceFormData: true,
      preserveScroll: true,
      onFinish: () => setBulkUpdating(false)
    });
  };

  return (
    <div>
      <Head title="Data Obat" />
      <div className="space-y-6">
        <PageHeader />

        <ObatModal
          open={modalOpen}
          isEdit={isEdit}
          onClose={closeModal}
          onSubmit={submitForm}
          data={data}
          setData={setData}
          errors={errors}
          processing={processing}
          dropdowns={dropdowns}
          autoHargaAktif={autoHargaAktif}
          onToggleAutoHarga={(checked) => checked ? aktifkanAutoHargaJual() : nonaktifkanAutoHargaJual()}
          onManualRecompute={() => recomputeAllPrices()}
        />

        {flash?.success && (
          <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} className="rounded-lg bg-green-50 p-3 text-sm text-green-800">
            {flash.success}
          </motion.div>
        )}

        {/* Card kontrol pencarian & aksi di bawah header */}
        <motion.div
          className="rounded-xl bg-white p-4 shadow-sm"
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.4, ease: 'easeOut', delay: 0.05 }}
        >
          <div className="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <form onSubmit={onSearch} className="flex w-full max-w-xl items-center gap-2">
              <div className="flex-1">
                <label className="block text-xs font-medium text-gray-600">Pencarian</label>
                <input type="text" value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Cari kode/nama/satuan..." className="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-300" />
              </div>
              <motion.button type="submit" className="mt-6 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" whileHover={{ scale: 1.03, y: -1 }} whileTap={{ scale: 0.97 }}>Cari</motion.button>
            </form>
            <div className="flex items-center gap-3">
              <div>
                <label className="block text-xs font-medium text-gray-600">Baris per halaman</label>
                <select value={perPage} onChange={onChangePerPage} className="mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-300">
                  {[10, 20, 50].map(n => <option key={n} value={n}>{n}</option>)}
                </select>
              </div>
              <motion.button onClick={updateHargaSemua} className="mt-6 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-800 hover:bg-amber-100 disabled:opacity-50" whileHover={{ scale: 1.03, y: -1 }} whileTap={{ scale: 0.97 }} disabled={bulkUpdating}>
                {bulkUpdating ? 'Memperbarui...' : 'Update Harga Semua'}
              </motion.button>
              <motion.button onClick={openCreate} className="mt-6 rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700" whileHover={{ scale: 1.03, y: -1 }} whileTap={{ scale: 0.97 }}>+ Tambah Obat</motion.button>
            </div>
          </div>
        </motion.div>
        {/* Grid konten utama */}
        <motion.div className="grid gap-4 md:grid-cols-2" initial={{ opacity: 0, scale: 0.98 }} animate={{ opacity: 1, scale: 1 }} transition={{ duration: 0.45, ease: 'easeOut', delay: 0.1 }}>
          <motion.div variants={panelVariants} initial="hidden" animate="show" transition={{ duration: 0.45, ease: 'easeOut', delay: 0.12 }} className="rounded-lg ring-1 ring-gray-200 bg-white p-3 shadow-sm">
            <div className="flex items-center justify-between">
              <h3 className="text-sm font-semibold">Daftar Obat</h3>
              {isSearching && (
                <motion.span className="text-xs text-indigo-600" animate={{ opacity: [0.6, 1, 0.6] }} transition={{ duration: 1.2, repeat: Infinity }}>
                  Mencari...
                </motion.span>
              )}
            </div>
              <div className="mt-3 max-h-[480px] overflow-y-auto">
                {items?.data?.length ? (
                  <motion.ul variants={listVariants} initial="hidden" animate="show" className="divide-y divide-gray-100">
                    {items.data.map((item) => (
                      <motion.li variants={itemVariants} key={item.kode_brng} className={`cursor-pointer p-3 transition hover:bg-indigo-50 ${selected?.kode_brng === item.kode_brng ? 'bg-indigo-50 border-l-4 border-indigo-600' : ''}`} onClick={() => setSelected(item)} whileHover={{ scale: 1.01, y: -1 }}>
                        <div className="flex items-center justify-between">
                          <div>
                            <p className="text-sm font-medium text-gray-900">{item.nama_brng}</p>
                            <p className="text-xs font-mono text-gray-600">{item.kode_brng} • {item.kode_sat || '-'}{item.kode_satbesar ? `/${item.kode_satbesar}` : ''}</p>
                          </div>
                          <motion.button onClick={(e) => { e.stopPropagation(); openEdit(item); }} className="rounded-lg border border-indigo-200 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>Edit</motion.button>
                        </div>
                      </motion.li>
                    ))}
                  </motion.ul>
                ) : (
                  <div className="p-6 text-center text-sm text-gray-500">Tidak ada data.</div>
                )}
              </div>
            </motion.div>

            <motion.div variants={panelVariants} initial="hidden" animate="show" transition={{ duration: 0.45, ease: 'easeOut', delay: 0.18 }} className="rounded-lg ring-1 ring-gray-200 bg-white p-4 shadow-sm">
              <h3 className="text-sm font-semibold">Detail Obat</h3>
              {!selected ? (
                <div className="mt-4 rounded-lg border border-dashed p-6 text-center text-sm text-gray-500">Pilih obat di panel kiri untuk melihat detail.</div>
              ) : (
                <motion.div className="mt-3 space-y-4" initial={{ opacity: 0, y: 6 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-base font-semibold text-gray-900">{selected.nama_brng}</p>
                      <p className="text-xs font-mono text-gray-600">{selected.kode_brng}</p>
                    </div>
                    <div className="flex gap-2">
                      <motion.button onClick={() => openEdit(selected)} className="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>Edit</motion.button>
                      <motion.button onClick={() => confirmDelete(selected)} className="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50" whileHover={{ scale: 1.03 }} whileTap={{ scale: 0.97 }}>Hapus</motion.button>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                      <p className="text-xs text-gray-500">Satuan</p>
                      <p className="text-sm">{selected.kode_sat || '-'}{selected.kode_satbesar ? ` / ${selected.kode_satbesar}` : ''}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Jenis (kdjns)</p>
                      <p className="text-sm">{selected.kdjns || '-'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Expire</p>
                      <p className="text-sm">{selected.expire || '-'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Letak Barang</p>
                      <p className="text-sm">{selected.letak_barang || '-'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Industri</p>
                      <p className="text-sm">{selected.kode_industri || '-'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Kategori</p>
                      <p className="text-sm">{selected.kode_kategori || '-'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Golongan</p>
                      <p className="text-sm">{selected.kode_golongan || '-'}</p>
                    </div>
                  </div>

                  <div>
                    <p className="text-xs text-gray-500">Harga</p>
                    <div className="mt-1 grid grid-cols-2 gap-2 text-sm">
                      <div>Dasar: {selected.dasar ?? 0}</div>
                      <div>Beli: {selected.h_beli ?? 0}</div>
                      <div>Ralan: {selected.ralan ?? 0}</div>
                      <div>Kelas 1: {selected.kelas1 ?? 0}</div>
                      <div>Kelas 2: {selected.kelas2 ?? 0}</div>
                      <div>Kelas 3: {selected.kelas3 ?? 0}</div>
                      <div>Utama: {selected.utama ?? 0}</div>
                      <div>VIP: {selected.vip ?? 0}</div>
                      <div>VVIP: {selected.vvip ?? 0}</div>
                      <div>Beli Luar: {selected.beliluar ?? 0}</div>
                      <div>Jual Bebas: {selected.jualbebas ?? 0}</div>
                      <div>Karyawan: {selected.karyawan ?? 0}</div>
                    </div>
                  </div>

                  <div className="grid grid-cols-3 gap-2 text-sm">
                    <div>
                      <p className="text-xs text-gray-500">Isi</p>
                      <p>{selected.isi ?? 1}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Kapasitas</p>
                      <p>{selected.kapasitas ?? 0}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Stok Minimal</p>
                      <p>{selected.stokminimal ?? 0}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Status</p>
                      <p>{selected.status ?? '1'}</p>
                    </div>
                  </div>
                </motion.div>
              )}
            </motion.div>
          </motion.div>

          {items?.links && (
            <div className="mt-4 flex flex-wrap items-center justify-between gap-2">
              <p className="text-xs text-gray-600">Menampilkan {items?.from ?? 0}-{items?.to ?? 0} dari {items?.total ?? 0} data</p>
              <div className="flex flex-wrap gap-2">
                {items.links.map((link, idx) => (
                  <Link key={idx} href={link.url || '#'} preserveState replace className={`rounded-lg px-3 py-1.5 text-xs ${link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border'} ${!link.url ? 'pointer-events-none opacity-50' : ''}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                ))}
              </div>
            </div>
          )}

        <ConfirmDelete open={confirmOpen} onClose={() => setConfirmOpen(false)} onConfirm={performDelete} item={selected} />
      </div>
    </div>
  );
}

DataObatPage.layout = (page) => <AppLayout title="Farmasi" children={page} />;