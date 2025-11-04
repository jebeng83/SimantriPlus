import React, { useEffect, useMemo, useRef, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const fmtDate = (d = new Date()) => {
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const yy = d.getFullYear();
  return `${dd}-${mm}-${yy}`;
};
const toIsoDate = (display) => {
  try {
    const [dd, mm, yyyy] = (display || '').split('-');
    if (!dd || !mm || !yyyy) return '';
    return `${yyyy}-${mm}-${dd}`;
  } catch { return ''; }
};

export default function PermintaanMedis() {
  const [header, setHeader] = useState({
    noPermintaan: '',
    tanggal: fmtDate(),
    kdGudangTujuan: '',
    nmGudangTujuan: '',
    kdPetugas: '',
    nmPetugas: '',
    asalPermintaan: '',
  });
  const [rows, setRows] = useState([]);
  const [keyword, setKeyword] = useState('');
  const [catalog, setCatalog] = useState([]);
  const [loadingCatalog, setLoadingCatalog] = useState(false);

  // Bangsal (Gudang) autocomplete states
  const [bangsalList, setBangsalList] = useState([]);
  const [showNamaDropdown, setShowNamaDropdown] = useState(false);
  const [activeNamaIndex, setActiveNamaIndex] = useState(0);
  const [showAsalDropdown, setShowAsalDropdown] = useState(false);
  const [activeAsalIndex, setActiveAsalIndex] = useState(0);
  const [showPegawaiDropdown, setShowPegawaiDropdown] = useState(false);
  const [activePegawaiIndex, setActivePegawaiIndex] = useState(0);
  const [pegawaiList, setPegawaiList] = useState([]);

  const noPermintaanRef = useRef(null);
  const nmGudangTujuanRef = useRef(null);
  const nmPetugasRef = useRef(null);
  const asalPermintaanRef = useRef(null);
  const keywordRef = useRef(null);

  // Backward-compat aliases (prevent ReferenceError during HMR if old refs still referenced)
  const kdGudangTujuanRef = nmGudangTujuanRef;
  const kdPetugasRef = nmPetugasRef;
  // Muat katalog obat
  useEffect(() => {
    const load = async () => {
      try {
        setLoadingCatalog(true);
        const res = await fetch('/api/obat-luar');
        const data = await res.json().catch(() => ([]));
        let items = [];
        if (Array.isArray(data)) items = data; else if (Array.isArray(data?.data)) items = data.data;
        const norm = items.map((x) => ({
          kode_brng: x.kode_brng || x.kodeBrng || x.id || '',
          nama_brng: x.nama_brng || x.namaBrng || x.text || '',
          satuan: x.satuan || x.kode_sat || '',
          jenis: x.jenis || x.jenis_obat || x.JenisObat || '',
          kategori: x.kategori || x.kategori_barang || x.Kategori || '',
          golongan: x.golongan || x.golongan_barang || x.Golongan || '',
        }));
        setCatalog(norm);
      } catch (e) {
        console.warn('Gagal memuat katalog obat:', e);
      } finally {
        setLoadingCatalog(false);
      }
    };
    load();
  }, []);

  // Muat daftar bangsal untuk autocomplete
  useEffect(() => {
    const loadBangsal = async () => {
      const ts = Date.now();
      try {
        // Coba sumber utama: pembelian/lokasi
        const resLokasi = await fetch(`/api/pembelian/lokasi?t=${ts}`, {
          headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
        });
        const lokasi = await resLokasi.json().catch(() => ({}));
        if (lokasi?.success && Array.isArray(lokasi.data) && lokasi.data.length > 0) {
          setBangsalList(lokasi.data);
          console.info('[PermintaanMedis] Bangsal list loaded from /api/pembelian/lokasi:', lokasi.data.length);
          return;
        }
      } catch (err) {
        console.warn('Gagal memuat bangsal dari /api/pembelian/lokasi, mencoba sumber cadangan...', err);
      }
      try {
        // Fallback: riwayat-transaksi-gudang/bangsal
        const resBangsal = await fetch(`/api/riwayat-transaksi-gudang/bangsal?t=${ts}`, {
          headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
        });
        const bangsal = await resBangsal.json().catch(() => ({}));
        if (bangsal?.success && Array.isArray(bangsal.data)) {
          setBangsalList(bangsal.data);
          console.info('[PermintaanMedis] Bangsal list loaded from /api/riwayat-transaksi-gudang/bangsal:', bangsal.data.length);
        } else {
          console.warn('[PermintaanMedis] Bangsal API response tidak sesuai/ kosong');
        }
      } catch (err) {
        console.error('Gagal memuat daftar bangsal dari semua sumber:', err);
      }
    };
    loadBangsal();
  }, []);

  // Muat daftar pegawai untuk autocomplete Pegawai (Nama)
  useEffect(() => {
    const loadPegawai = async () => {
      try {
        const ts = Date.now();
        const res = await fetch(`/api/pembelian/petugas?t=${ts}`, {
          headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' }
        });
        const result = await res.json().catch(() => ({}));
        if (result?.success && Array.isArray(result.data)) {
          setPegawaiList(result.data);
          console.info('[PermintaanMedis] Pegawai list loaded:', result.data.length);
        } else {
          console.warn('[PermintaanMedis] Pegawai API response tidak sesuai/ kosong');
        }
      } catch (err) {
        console.error('Gagal memuat daftar pegawai:', err);
      }
    };
    loadPegawai();
  }, []);

  // Pencarian cepat katalog
  const suggestions = useMemo(() => {
    if (!keyword) return [];
    const q = keyword.toLowerCase();
    return catalog.filter((x) => (
      (x.kode_brng || '').toLowerCase().includes(q) ||
      (x.nama_brng || '').toLowerCase().includes(q) ||
      (x.jenis || '').toLowerCase().includes(q) ||
      (x.kategori || '').toLowerCase().includes(q) ||
      (x.golongan || '').toLowerCase().includes(q)
    )).slice(0, 10);
  }, [keyword, catalog]);

  const addRow = (item) => {
    setRows((prev) => ([
      ...prev,
      {
        jumlah: '',
        kodeBarang: item.kode_brng,
        namaBarang: item.nama_brng,
        satuan: item.satuan,
        jenis: item.jenis,
        kategori: item.kategori,
        golongan: item.golongan,
        keterangan: '',
      }
    ]));
    setKeyword('');
  };
  const updateRow = (idx, field, value) => setRows((prev) => prev.map((r, i) => i === idx ? { ...r, [field]: value } : r));
  const removeRow = (idx) => setRows((prev) => prev.filter((_, i) => i !== idx));
  const countValidItems = () => rows.filter((r) => Number(r.jumlah) > 0).length;

  const lookupNamaGudang = (kode) => {
    const found = bangsalList.find((b) => (b.kd_bangsal || '').toLowerCase() === String(kode || '').toLowerCase());
    setHeader((h) => ({ ...h, nmGudangTujuan: found?.nm_bangsal || h.nmGudangTujuan }));
  };
  const handleGudangKeyDown = async (e) => {
    if (e.key === 'PageDown' || e.key === 'PageUp') {
      await lookupNamaGudang(header.kdGudangTujuan);
      if (e.key === 'PageUp') noPermintaanRef.current?.focus(); else nmPetugasRef.current?.focus();
    }
  };

  const kodeSuggestions = useMemo(() => {
    const q = (header.kdGudangTujuan || '').toLowerCase().trim();
    if (!q) return bangsalList.slice(0, 8);
    return bangsalList.filter((b) => (b.kd_bangsal || '').toLowerCase().includes(q)).slice(0, 8);
  }, [header.kdGudangTujuan, bangsalList]);
  const namaSuggestions = useMemo(() => {
    const q = (header.nmGudangTujuan || '').toLowerCase().trim();
    if (!q) return bangsalList.slice(0, 8);
    return bangsalList.filter((b) => (b.nm_bangsal || '').toLowerCase().includes(q)).slice(0, 8);
  }, [header.nmGudangTujuan, bangsalList]);
  const handleNamaKeyDown = (e) => {
    if (e.key === 'ArrowDown') {
      setShowNamaDropdown(true);
      setActiveNamaIndex((i) => Math.min(i + 1, Math.max(0, namaSuggestions.length - 1)));
    } else if (e.key === 'ArrowUp') {
      setActiveNamaIndex((i) => Math.max(0, i - 1));
    } else if (e.key === 'Enter') {
      const choice = namaSuggestions[activeNamaIndex];
      if (choice) {
        setHeader((h) => ({ ...h, kdGudangTujuan: choice.kd_bangsal, nmGudangTujuan: choice.nm_bangsal }));
        setShowNamaDropdown(false);
        asalPermintaanRef.current?.focus();
      }
    }
  };

  // Saran untuk Asal Permintaan (bangsal.nm_bangsal)
  const asalSuggestions = useMemo(() => {
    const q = (header.asalPermintaan || '').toLowerCase().trim();
    if (!q) return bangsalList.slice(0, 8);
    return bangsalList.filter((b) => (b.nm_bangsal || '').toLowerCase().includes(q)).slice(0, 8);
  }, [header.asalPermintaan, bangsalList]);

  // Saran untuk Pegawai (nama)
  const pegawaiSuggestions = useMemo(() => {
    const q = (header.nmPetugas || '').toLowerCase().trim();
    if (!q) return pegawaiList.slice(0, 8);
    return pegawaiList.filter((p) => (p.nama || '').toLowerCase().includes(q)).slice(0, 8);
  }, [header.nmPetugas, pegawaiList]);

  const handleAsalKeyDown = (e) => {
    if (e.key === 'ArrowDown') {
      setShowAsalDropdown(true);
      setActiveAsalIndex((i) => Math.min(i + 1, Math.max(0, asalSuggestions.length - 1)));
    } else if (e.key === 'ArrowUp') {
      setActiveAsalIndex((i) => Math.max(0, i - 1));
    } else if (e.key === 'Enter') {
      const choice = asalSuggestions[activeAsalIndex];
      if (choice) {
        setHeader((h) => ({ ...h, asalPermintaan: choice.nm_bangsal }));
        setShowAsalDropdown(false);
        nmPetugasRef.current?.focus();
      }
    }
  };

  const handlePegawaiKeyDown = (e) => {
    if (e.key === 'ArrowDown') {
      setShowPegawaiDropdown(true);
      setActivePegawaiIndex((i) => Math.min(i + 1, Math.max(0, pegawaiSuggestions.length - 1)));
    } else if (e.key === 'ArrowUp') {
      setActivePegawaiIndex((i) => Math.max(0, i - 1));
    } else if (e.key === 'Enter') {
      const choice = pegawaiSuggestions[activePegawaiIndex];
      if (choice) {
        setHeader((h) => ({ ...h, nmPetugas: choice.nama, kdPetugas: choice.nip }));
        setShowPegawaiDropdown(false);
        keywordRef.current?.focus();
      }
    }
  };

  // Auto nomor berdasarkan tanggal
  useEffect(() => {
    const fetchNextNumber = async () => {
      const iso = toIsoDate(header.tanggal);
      if (!iso) return;
      try {
        const res = await fetch(`/api/permintaan-medis/next-number?tanggal=${encodeURIComponent(iso)}`);
        const data = await res.json();
        if (data && data.nomor) setHeader((h) => ({ ...h, noPermintaan: data.nomor }));
      } catch (e) {
        console.warn('Gagal auto nomor:', e);
      }
    };
    fetchNextNumber();
  }, [header.tanggal]);
  const autoNomor = () => {
    const iso = toIsoDate(header.tanggal);
    if (!iso) return;
    fetch(`/api/permintaan-medis/next-number?tanggal=${encodeURIComponent(iso)}`)
      .then((r) => r.json())
      .then((d) => { if (d && d.nomor) setHeader((h) => ({ ...h, noPermintaan: d.nomor })); })
      .catch((e) => console.warn('Gagal auto nomor:', e));
  };

  const onSave = async () => {
    const jml = countValidItems();
    if (!header.noPermintaan?.trim()) { alert('No.Permintaan tidak boleh kosong'); noPermintaanRef.current?.focus(); return; }
    if (!header.nmGudangTujuan?.trim()) { alert('Ruangan/Depo (Gudang Tujuan) tidak boleh kosong'); nmGudangTujuanRef.current?.focus(); return; }
    if (!header.nmPetugas?.trim()) { alert('Petugas tidak boleh kosong'); nmPetugasRef.current?.focus(); return; }
    if (rows.length === 0) { alert('Maaf, data sudah habis...!!!!'); return; }
    if (jml <= 0) { alert('Maaf, Silahkan masukkan permintaan...!!!!'); return; }
    if (!window.confirm('Eeiiiiiits, udah bener belum data yang mau disimpan..??')) return;
    try {
      const isoTanggal = toIsoDate(header.tanggal);
      const payload = {
        header: { ...header, tanggal: isoTanggal },
        items: rows.filter((r) => Number(r.jumlah) > 0).map((r) => ({
          jumlah: Number(r.jumlah),
          kodeBarang: r.kodeBarang,
          satuan: r.satuan,
          keterangan: r.keterangan?.replaceAll("'", '').replaceAll('"', ''),
        })),
      };
      const res = await fetch('/api/permintaan-medis', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      if (!res.ok) throw new Error('Gagal menyimpan ke server');
      setRows((prev) => prev.map((r) => ({ ...r, jumlah: '', keterangan: '' })));
      autoNomor();
      alert('Permintaan berhasil disimpan');
    } catch (e) {
      console.error(e);
      alert('Terjadi kesalahan saat pemrosesan data, transaksi dibatalkan.\nPeriksa kembali data sebelum melanjutkan menyimpan..!!');
    }
  };

  return (
    <div className="bg-white shadow rounded p-4 space-y-4">
      <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.25 }} className="text-lg font-semibold">::[ Permintaan Obat/Alkes/BHP Medis ]::</motion.div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="space-y-2">
          <label className="block text-sm">No.Permintaan</label>
          <input ref={noPermintaanRef} value={header.noPermintaan} onChange={(e)=>setHeader({ ...header, noPermintaan: e.target.value })} className="w-full border rounded px-3 py-2" />
        </div>
        <div className="space-y-2">
          <label className="block text-sm">Tanggal</label>
          <input value={header.tanggal} onChange={(e)=>setHeader({ ...header, tanggal: e.target.value })} className="w-full border rounded px-3 py-2" />
        </div>
        <div className="space-y-2 relative">
          <label className="block text-sm">Ditujukan Ke (Nama Gudang)</label>
          <input
            ref={nmGudangTujuanRef}
            value={header.nmGudangTujuan}
            onChange={(e)=>{
              const val = e.target.value;
              const found = bangsalList.find((b)=> (b.nm_bangsal||'').toLowerCase() === val.toLowerCase());
              setHeader((h)=>({ ...h, nmGudangTujuan: val, kdGudangTujuan: found?.kd_bangsal ?? h.kdGudangTujuan }));
              setShowNamaDropdown(true);
              setActiveNamaIndex(0);
            }}
            onKeyDown={handleNamaKeyDown}
            onFocus={()=> setShowNamaDropdown(true)}
            onBlur={()=> setTimeout(()=> setShowNamaDropdown(false), 120)}
            className="w-full border rounded px-3 py-2"
          />
          <AnimatePresence>
            {showNamaDropdown && namaSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {namaSuggestions.map((b, i)=> (
                  <li key={b.kd_bangsal} className={`px-3 py-2 text-sm cursor-pointer ${i===activeNamaIndex? 'bg-emerald-50':'hover:bg-gray-50'}`}
                      onMouseDown={(evt)=>{evt.preventDefault(); setHeader((h)=>({ ...h, kdGudangTujuan: b.kd_bangsal, nmGudangTujuan: b.nm_bangsal })); setShowNamaDropdown(false); asalPermintaanRef.current?.focus(); }}>
                    <span className="font-mono mr-2">{b.kd_bangsal}</span>
                    <span>{b.nm_bangsal}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
        <div className="space-y-2 md:row-start-3 md:col-span-2 relative">
          <label className="block text-sm">Pegawai (Nama)</label>
          <input
            ref={nmPetugasRef}
            value={header.nmPetugas}
            onChange={(e)=> {
              const val = e.target.value;
              setHeader((h)=>({ ...h, nmPetugas: val }));
            }}
            onKeyDown={handlePegawaiKeyDown}
            onFocus={()=> setShowPegawaiDropdown(true)}
            onBlur={()=> setTimeout(()=> setShowPegawaiDropdown(false), 120)}
            className="w-full border rounded px-3 py-2"
            placeholder="Cari nama pegawai"
          />
          <AnimatePresence>
            {showPegawaiDropdown && pegawaiSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {pegawaiSuggestions.map((p, i)=> (
                  <li key={p.nip} className={`px-3 py-2 text-sm cursor-pointer ${i===activePegawaiIndex? 'bg-emerald-50':'hover:bg-gray-50'}`}
                      onMouseDown={(evt)=>{evt.preventDefault(); setHeader((h)=>({ ...h, nmPetugas: p.nama, kdPetugas: p.nip })); setShowPegawaiDropdown(false); keywordRef.current?.focus(); }}>
                    <span className="font-mono mr-2">{p.nip}</span>
                    <span>{p.nama}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
        <div className="space-y-2 md:col-span-1 md:col-start-2 md:row-start-2 relative">
          <label className="block text-sm">Asal Permintaan</label>
          <input
            ref={asalPermintaanRef}
            value={header.asalPermintaan}
            onChange={(e)=>{
              const val = e.target.value;
              setHeader((h)=>({ ...h, asalPermintaan: val }));
            }}
            onKeyDown={handleAsalKeyDown}
            onFocus={()=> setShowAsalDropdown(true)}
            onBlur={()=> setTimeout(()=> setShowAsalDropdown(false), 120)}
            className="w-full border rounded px-3 py-2"
            placeholder="Cari gudang asal"
          />
          <AnimatePresence>
            {showAsalDropdown && asalSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {asalSuggestions.map((b, i)=> (
                  <li key={b.kd_bangsal} className={`px-3 py-2 text-sm cursor-pointer ${i===activeAsalIndex? 'bg-emerald-50':'hover:bg-gray-50'}`}
                      onMouseDown={(evt)=>{evt.preventDefault(); setHeader((h)=>({ ...h, asalPermintaan: b.nm_bangsal })); setShowAsalDropdown(false); nmPetugasRef.current?.focus(); }}>
                    <span className="font-mono mr-2">{b.kd_bangsal}</span>
                    <span>{b.nm_bangsal}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
      </div>

      <div className="overflow-auto border rounded">
        <table className="min-w-full text-sm">
          <thead className="bg-gray-50">
            <tr>
              <th className="p-2 w-[6%]">Jml</th>
              <th className="p-2 w-[12%]">KodeBarang</th>
              <th className="p-2">NamaBarang</th>
              <th className="p-2 w-[10%]">Satuan</th>
              <th className="p-2 w-[12%]">Jenis</th>
              <th className="p-2 w-[12%]">Kategori</th>
              <th className="p-2 w-[12%]">Golongan</th>
              <th className="p-2 w-[16%]">Keterangan</th>
              <th className="p-2 w-[6%]" />
            </tr>
          </thead>
          <tbody>
            {rows.length === 0 && (<tr><td colSpan={9} className="p-4 text-center text-gray-500">Belum ada data</td></tr>)}
            {rows.map((r, idx) => (
              <tr key={idx} className="border-t">
                <td className="p-2"><input value={r.jumlah} onChange={(e)=>updateRow(idx,'jumlah',e.target.value)} className="w-16 border rounded px-2 py-1 text-right" /></td>
                <td className="p-2">{r.kodeBarang}</td>
                <td className="p-2">{r.namaBarang}</td>
                <td className="p-2">{r.satuan}</td>
                <td className="p-2">{r.jenis}</td>
                <td className="p-2">{r.kategori}</td>
                <td className="p-2">{r.golongan}</td>
                <td className="p-2"><input value={r.keterangan} onChange={(e)=>updateRow(idx,'keterangan',e.target.value)} className="w-full border rounded px-2 py-1" /></td>
                <td className="p-2 text-center"><button onClick={()=>removeRow(idx)} className="px-2 py-1 text-red-600">Hapus</button></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex items-center gap-3">
        <button onClick={onSave} className="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <div className="flex items-center gap-2">
          <span>Key Word :</span>
          <input ref={keywordRef} value={keyword} onChange={(e)=>setKeyword(e.target.value)} className="border rounded px-3 py-2 w-96" placeholder="Cari kode/nama/jenis/kategori/golongan" />
        </div>
        <button className="px-3 py-2 border rounded" onClick={()=>window.location.href='/farmasi'}>Keluar</button>
      </div>

      <div className="space-y-2">
        {loadingCatalog && <div className="text-sm text-gray-500">Memuat katalog...</div>}
        {!loadingCatalog && keyword && (
          <div className="border rounded p-2">
            {suggestions.length === 0 ? (
              <div className="text-sm text-gray-500">Tidak ada hasil untuk "{keyword}"</div>
            ) : (
              <ul className="divide-y">
                {suggestions.map((s, i) => (
                  <li key={i} className="py-2 flex items-center justify-between">
                    <div>
                      <div className="font-medium">{s.nama_brng}</div>
                      <div className="text-xs text-gray-500">{s.kode_brng} • {s.satuan} • {s.jenis} • {s.kategori} • {s.golongan}</div>
                    </div>
                    <button onClick={()=>addRow(s)} className="px-3 py-1 bg-green-600 text-white rounded">Tambah</button>
                  </li>
                ))}
              </ul>
            )}
          </div>
        )}
      </div>
    </div>
  );
}