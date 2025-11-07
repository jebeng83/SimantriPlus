import React, { useEffect, useMemo, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";

// Tambah konstanta STATUS_OPTIONS di bagian atas agar bisa digunakan di dalam komponen
const STATUS_OPTIONS = ["Belum", "Tunda", "Sudah", "Batal"];

function formatDate(iso) {
  try {
    const d = new Date(iso);
    return d.toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" });
  } catch {
    return iso;
  }
}

export default function DisplayKegiatanUkm({ monthlyUrl = "/api/jadwal-ukm/monthly" }) {
  const [month, setMonth] = useState(() => {
    const now = new Date();
    const def = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}`;
    try {
      const params = new URLSearchParams(window.location.search);
      const q = params.get("month");
      if (q && /^\d{4}-\d{2}$/.test(q)) return q;
    } catch {}
    return def;
  });
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  // State untuk popup update status
  const [showStatusModal, setShowStatusModal] = useState(false);
  const [selectedItem, setSelectedItem] = useState(null); // { id, petugas_nama, status, parent: { nama_kegiatan, tanggal } }
  const [newStatus, setNewStatus] = useState("Belum");
  const [savingStatus, setSavingStatus] = useState(false);
  const [statusError, setStatusError] = useState("");

  async function loadData(m) {
    setLoading(true);
    setError("");
    try {
      const url = `${monthlyUrl}?month=${encodeURIComponent(m || month)}&t=${Date.now()}`;
      const res = await fetch(url, { headers: { Accept: "application/json" } });
      const ct = (res.headers.get("content-type") || "").toLowerCase();
      // Deteksi kondisi tidak login atau bukan JSON
      if (!ct.includes("application/json")) {
        const text = await res.text().catch(() => "");
        console.warn("[DisplayKegiatanUkm] Non-JSON response", { status: res.status, redirected: res.redirected, ct, url });
        const msg = res.status === 401 || (text && text.includes("login"))
          ? "Endpoint dilindungi. Silakan login untuk melihat jadwal bulanan."
          : "Gagal memuat data (respons bukan JSON).";
        setRows([]);
        setError(msg);
        console.log('[DisplayKegiatanUkm] Fetched monthly', { month: m || month, url, count: 0, start: undefined, end: undefined });
      } else {
        const j = await res.json().catch(() => ({}));
        const items = Array.isArray(j?.data) ? j.data : Array.isArray(j) ? j : [];
        console.log('[DisplayKegiatanUkm] Fetched monthly', { month: m || month, url, count: items.length, start: j?.start, end: j?.end });
        setRows(items);
      }
    } catch (e) {
      console.warn("Gagal memuat data jadwal bulanan:", e);
      setError("Gagal memuat data jadwal bulanan. Coba lagi nanti.");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadData(month);
  }, [month]);

  // Gabungkan kegiatan dengan kode yang sama dalam satu tanggal dan simpan daftar entri (dengan id) per petugas
  function aggregateByKode(items) {
    const map = new Map();
    for (const it of items) {
      const kodeKey = it?.kode ?? null;
      const key = kodeKey ? String(kodeKey) : `__no_kode__${it?.id ?? Math.random().toString(36).slice(2)}`;
      if (!map.has(key)) {
        map.set(key, {
          id: `${it.tanggal?.slice(0,10) || ''}-${key}`,
          tanggal: it.tanggal,
          kode: it.kode,
          nama_kegiatan: it.nama_kegiatan || it.kegiatan || it.kode || "-",
          kd_kel: it.kd_kel,
          nm_kel: it.nm_kel,
          // simpan keterangan dari entri pertama yang ditemukan
          Keterangan: it.Keterangan || "",
          // daftar entri per petugas agar bisa update status satu-per-satu
          items: [],
        });
      }
      const agg = map.get(key);
      agg.items.push({
        id: it.id ?? it.kd_jadwal ?? `${key}-auto-${agg.items.length}`,
        petugas_nama: it.petugas_nama || it.nama || it.nip || "-",
        nip: it.nip,
        status: it.status || "Belum",
        Keterangan: it.Keterangan || "",
        kd_kel: it.kd_kel,
      });
      // sinkronkan nm_kel/Keterangan jika belum terisi
      if (!agg.nm_kel && it.nm_kel) agg.nm_kel = it.nm_kel;
      if (!agg.Keterangan && it.Keterangan) agg.Keterangan = it.Keterangan;
    }
    return Array.from(map.values());
  }

  const groups = useMemo(() => {
    const g = new Map();
    for (const r of rows || []) {
      const raw = r.tanggal || "-";
      const dayKey = String(raw).slice(0, 10); // normalize YYYY-MM-DD only
      if (!g.has(dayKey)) g.set(dayKey, []);
      g.get(dayKey).push(r);
    }
    return Array.from(g.entries()).sort((a, b) => {
      try {
        return new Date(a[0]) - new Date(b[0]);
      } catch (e) {
        return String(a[0]).localeCompare(String(b[0]));
      }
    });
  }, [rows]);

  function openStatusModal(rec, parent) {
    setSelectedItem({ ...rec, parent });
    setNewStatus(rec?.status || "Belum");
    setStatusError("");
    setShowStatusModal(true);
  }
  function closeStatusModal() {
    setShowStatusModal(false);
    setSelectedItem(null);
    setStatusError("");
  }
  async function saveStatusUpdate() {
    if (!selectedItem?.id) return;
    setSavingStatus(true);
    setStatusError("");
    try {
      const csrfToken = getCsrfToken();
      const res = await fetch(`/api/jadwal-ukm/${encodeURIComponent(selectedItem.id)}` , {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ status: newStatus }),
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.error) {
        throw new Error(json?.message || json?.detail || "Gagal mengupdate status");
      }
      // Update state lokal agar badge berubah tanpa reload penuh
      setRows((prev) => prev.map((r) => String(r.id) === String(selectedItem.id) ? { ...r, status: newStatus } : r));
      closeStatusModal();
    } catch (e) {
      console.error("saveStatusUpdate error", e);
      setStatusError(e?.message || "Gagal mengupdate status");
    } finally {
      setSavingStatus(false);
    }
  }

  return (
    <div className="min-h-screen w-full p-4 md:p-6">
      <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
          <h1 className="text-2xl font-bold text-emerald-600">Display Kegiatan UKM</h1>
          <p className="text-gray-600">Menampilkan jadwal kegiatan UKM per tanggal pada bulan terpilih.</p>
        </div>
        <div className="flex items-center gap-3">
          <label className="text-sm text-gray-700">Pilih Bulan</label>
          <input
            type="month"
            value={month}
            onChange={(e) => setMonth(e.target.value)}
            className="rounded border border-gray-300 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
          <button
            onClick={() => loadData(month)}
            className="rounded bg-emerald-600 text-white px-3 py-2 text-sm hover:bg-emerald-700 shadow"
          >
            Refresh
          </button>
        </div>
      </div>

      {loading && (
        <div className="flex items-center gap-3 text-emerald-700">
          <svg className="animate-spin h-5 w-5" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
          </svg>
          Memuat data...
        </div>
      )}
      {!!error && (
        <div className="rounded border border-red-300 bg-red-50 text-red-700 px-3 py-2 mb-4">{error}</div>
      )}

      <AnimatePresence>
        {!loading && groups.length === 0 && (
          <motion.div key="empty"
            initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
            className="rounded border border-gray-200 bg-white px-4 py-6 text-gray-600">
            Tidak ada data untuk bulan ini.
          </motion.div>
        )}

        <div key="grid" className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {groups.map(([tanggal, items]) => {
            const aggItems = aggregateByKode(items);
            return (
              <motion.div key={`day-${tanggal}`}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                className="rounded-lg border border-gray-200 bg-white shadow-sm"
              >
                <div className="px-4 py-3 border-b bg-emerald-50 border-emerald-200">
                  <div className="font-semibold text-emerald-700">{formatDate(tanggal)}</div>
                  <div className="text-xs text-emerald-800">{aggItems.length} kegiatan</div>
                </div>
                <ul className="divide-y">
                  {aggItems.map((it, idx) => (
                    <li key={makeAggKey(tanggal, it, idx)} className="px-4 py-3">
                      <div className="flex items-start justify-between">
                        <div>
                          <div className={`font-medium ${isBelumStatus(it) ? 'text-red-600' : 'text-gray-800'}`}>
                            {it.nama_kegiatan || it.kegiatan || it.kode || "-"}
                          </div>
                          <div className="text-sm text-gray-600">
                            Desa/Kelurahan: <span className="font-medium">{it.nm_kel || it.kd_kel || "-"}</span>
                          </div>
                          {/* Tampilkan per-petugas dengan badge status yang bisa di-klik */}
                          {(it.items && it.items.length > 0) ? (
                            it.items.map((rec, i) => (
                              <div key={makePetKey(tanggal, it, rec, i)} className="mt-1 flex items-center justify-between gap-2">
                                <div className="text-sm text-gray-600">
                                  Petugas: <span className="font-medium">{rec.petugas_nama}</span>
                                </div>
                                <button
                                  type="button"
                                  onClick={() => openStatusModal(rec, { nama_kegiatan: it.nama_kegiatan || it.kegiatan || it.kode || "-", tanggal })}
                                  className={`ml-3 inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ${statusClasses(rec.status)} hover:opacity-80 transition-opacity`}
                                >
                                  {String(rec.status || 'Belum').toUpperCase()}
                                </button>
                              </div>
                            ))
                          ) : (
                            <div className="text-sm text-gray-600">
                              Petugas: <span className="font-medium">{it.petugas_nama || it.nama || it.nip || "-"}</span>
                            </div>
                          )}
                          {it.Keterangan && (
                            <div className="text-sm text-gray-600">Keterangan: {it.Keterangan}</div>
                          )}
                        </div>
                      </div>
                    </li>
                  ))}
                </ul>
              </motion.div>
            );
          })}
        </div>
      </AnimatePresence>

      {/* Modal Update Status */}
      <AnimatePresence>
        {showStatusModal && selectedItem && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
          >
            <motion.div
              initial={{ scale: 0.98, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.98, opacity: 0 }}
              className="w-[92vw] max-w-md rounded-lg bg-white shadow-lg border border-gray-200"
            >
              <div className="px-4 py-3 border-b">
                <div className="font-semibold">Ubah Status Jadwal</div>
                <div className="text-xs text-gray-600">{selectedItem?.parent?.nama_kegiatan} • {formatDate(selectedItem?.parent?.tanggal)}</div>
              </div>
              <div className="p-4 space-y-3">
                <div className="text-sm text-gray-700">Petugas: <span className="font-medium">{selectedItem?.petugas_nama}</span></div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-gray-700">Pilih Status</label>
                  <div className="grid grid-cols-2 gap-2">
                    {STATUS_OPTIONS.map((opt) => (
                      <label key={opt} className={`inline-flex items-center gap-2 rounded border px-3 py-2 cursor-pointer ${newStatus === opt ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:bg-gray-50'}`}>
                        <input type="radio" name="status" value={opt} checked={newStatus === opt} onChange={(e) => setNewStatus(e.target.value)} />
                        <span className="text-sm">{opt}</span>
                      </label>
                    ))}
                  </div>
                </div>
                {!!statusError && (
                  <div className="rounded border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm">{statusError}</div>
                )}
              </div>
              <div className="px-4 py-3 border-t flex items-center justify-end gap-2">
                <button type="button" onClick={closeStatusModal} className="px-4 py-2 rounded border text-sm hover:bg-gray-50">Batal</button>
                <button type="button" disabled={savingStatus} onClick={saveStatusUpdate} className="px-4 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700 disabled:opacity-60">
                  {savingStatus ? 'Menyimpan...' : 'Simpan'}
                </button>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

function getCsrfToken() {
  try {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
  } catch {
    return '';
  }
}
function statusClasses(st) {
  const s = String(st || '').toLowerCase();
  if (s === 'sudah') return 'bg-emerald-100 text-emerald-700';
  if (s === 'tunda') return 'bg-amber-100 text-amber-700';
  if (s === 'batal') return 'bg-rose-100 text-rose-700';
  return 'bg-gray-100 text-gray-700';
}

// (dihapus) STATUS_OPTIONS dideklarasikan di bagian atas file untuk menghindari duplikasi.

// Helper untuk membuat key yang selalu unik dan tidak kosong
function makeAggKey(day, aggItem, idx) {
  try {
    const id = aggItem?.id;
    const idStr = id != null ? String(id) : '';
    const hasId = idStr.trim() !== '';
    const kode = aggItem?.kode;
    const kodeStr = kode != null && String(kode).trim() !== '' ? String(kode).trim() : '__no_kode__';
    // Sertakan idx agar benar-benar unik meski ada id duplikat tak terduga
    return hasId ? `agg-${idStr}-${idx}` : `agg-${String(day)}-${kodeStr}-${idx}`;
  } catch {
    return `agg-${String(day)}-__unknown__-${idx}`;
  }
}

function makePetKey(day, aggItem, rec, i) {
  try {
    const recId = rec?.id;
    const recIdStr = recId != null ? String(recId) : '';
    const hasRecId = recIdStr.trim() !== '';
    const kode = aggItem?.kode;
    const kodeStr = (kode != null && String(kode).trim() !== '') ? String(kode).trim() : '__no_kode__';
    // Sertakan day/kode/i untuk menjamin keunikan
    return hasRecId ? `pet-${String(day)}-${kodeStr}-${recIdStr}-${i}` : `pet-${String(day)}-${kodeStr}-${i}`;
  } catch {
    return `pet-${String(day)}-__unknown__-${i}`;
  }
}

// Helper untuk menentukan apakah sebuah kegiatan memiliki status "Belum"
function isBelumStatus(aggItem) {
  try {
    const s = String(aggItem?.status || '').toLowerCase();
    if (s) return s === 'belum';
    if (Array.isArray(aggItem?.items)) {
      return aggItem.items.some((x) => String(x?.status || '').toLowerCase() === 'belum');
    }
    return false;
  } catch {
    return false;
  }
}