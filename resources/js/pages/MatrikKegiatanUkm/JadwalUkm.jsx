import React, { useEffect, useMemo, useState } from "react";
import { motion, AnimatePresence, useReducedMotion } from "framer-motion";
import { CalendarDays, User2, MapPin, ClipboardList, Tag, Loader2, Edit3, Trash } from "lucide-react";
import { toast } from "@/tools/toast";

// Debounce kecil untuk input pencarian dropdown
function useDebouncedValue(value, delay = 200) {
  const [v, setV] = useState(value);
  useEffect(() => {
    const h = setTimeout(() => setV(value), delay);
    return () => clearTimeout(h);
  }, [value, delay]);
  return v;
}

function InputForType({ type, name, value, onChange }) {
  const common = {
    name,
    value: value ?? "",
    onChange: (e) => onChange(name, e.target.value),
    className:
      "w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder:text-gray-400 disabled:bg-gray-100 disabled:text-gray-500",
  };

  if (name === "Keterangan") return <textarea rows={3} {...common} />;
  if (name === "status")
    return (
      <select {...common}>
        <option value="">-- Pilih Status --</option>
        <option value="Belum">Belum</option>
        <option value="Tunda">Tunda</option>
        <option value="Sudah">Sudah</option>
        <option value="Batal">Batal</option>
      </select>
    );

  const lower = (type || "").toLowerCase();
  if (["date"].includes(lower)) return <input type="date" {...common} />;
  if (["datetime", "timestamp"].includes(lower))
    return <input type="datetime-local" {...common} />;
  if (["int", "bigint", "tinyint", "smallint", "mediumint", "decimal", "float", "double"].includes(lower))
    return <input type="number" step="any" {...common} />;
  if (["text", "longtext"].includes(lower)) return <textarea rows={3} {...common} />;
  return <input type="text" {...common} />;
}

export default function JadwalUkm({ metaUrl, listUrl, storeUrl, updateUrlTemplate, deleteUrlTemplate, csrfToken }) {
  const [meta, setMeta] = useState({ columns: [], primary_key: "kd_jadwal" });
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  const [form, setForm] = useState({});
  const [editing, setEditing] = useState(null); // row object being edited
  const [saving, setSaving] = useState(false);
  const [showForm, setShowForm] = useState(true);
  const [page, setPage] = useState(1);
  const [pageSize, setPageSize] = useState(10);
  // Filter state untuk card pencarian
  const [filterStart, setFilterStart] = useState("");
  const [filterEnd, setFilterEnd] = useState("");
  const [filterNipKeyword, setFilterNipKeyword] = useState("");
  const [filterKelKeyword, setFilterKelKeyword] = useState("");
  const [filterStatus, setFilterStatus] = useState("");
  function clearFilters() {
    setFilterStart("");
    setFilterEnd("");
    setFilterNipKeyword("");
    setFilterKelKeyword("");
    setFilterStatus("");
    setPage(1);
  }
  // Gunakan preferensi Reduced Motion agar transisi tetap ringan
  const prefersReducedMotion = useReducedMotion();

  // --- Autocomplete Petugas (nip -> nama) ---
  const [petugasList, setPetugasList] = useState([]);
  const [petugasKeyword, setPetugasKeyword] = useState("");
  const [showPetugasDropdown, setShowPetugasDropdown] = useState(false);
  const [activePetugasIndex, setActivePetugasIndex] = useState(0);
  useEffect(() => {
    const loadPetugas = async () => {
      try {
        const res = await fetch(`/api/pembelian/petugas?t=${Date.now()}`, {
          headers: { Accept: "application/json", "Cache-Control": "no-cache" },
        });
        const data = await res.json().catch(() => ({}));
        let items = [];
        if (Array.isArray(data?.data)) items = data.data;
        else if (Array.isArray(data)) items = data;
        const norm = items.map((x) => ({
          nip: x.nip || x.id || "",
          nama: x.nama || x.name || "",
        }));
        setPetugasList(norm);
      } catch (e) {
        console.warn("Gagal memuat daftar petugas:", e);
      }
    };
    loadPetugas();
  }, []);
  const petugasKeywordDeb = useDebouncedValue(petugasKeyword, 200);
  const petugasSuggestions = useMemo(() => {
    const q = (petugasKeywordDeb || "").toLowerCase().trim();
    if (!q) return petugasList.slice(0, 8);
    return petugasList
      .filter(
        (p) =>
          String(p.nip || "").toLowerCase().includes(q) ||
          String(p.nama || "").toLowerCase().includes(q)
      )
      .slice(0, 8);
  }, [petugasKeywordDeb, petugasList]);
  // Sinkron tampil nama saat nip pada form berubah
  useEffect(() => {
    if (!form?.nip) return;
    const found = petugasList.find((p) => String(p.nip) === String(form.nip));
    if (found) setPetugasKeyword(found.nama);
  }, [form.nip, petugasList]);
  // Jika nama diketik persis, set nip otomatis
  useEffect(() => {
    const key = (petugasKeyword || "").trim().toLowerCase();
    if (!key) return;
    const exact = petugasList.find((p) => String(p.nama || "").toLowerCase() === key);
    if (exact) updateForm("nip", exact.nip);
  }, [petugasKeyword, petugasList]);

  // --- Autocomplete Kegiatan (kode -> nama_kegiatan) ---
  const [kegiatanList, setKegiatanList] = useState([]);
  const [kegiatanKeyword, setKegiatanKeyword] = useState("");
  const [showKegiatanDropdown, setShowKegiatanDropdown] = useState(false);
  const [activeKegiatanIndex, setActiveKegiatanIndex] = useState(0);
  useEffect(() => {
    const loadKegiatan = async () => {
      try {
        const res = await fetch(`/api/kegiatan-ukm?t=${Date.now()}`, {
          headers: { Accept: "application/json", "Cache-Control": "no-cache" },
        });
        const data = await res.json().catch(() => ({}));
        let items = [];
        if (Array.isArray(data?.data)) items = data.data; else if (Array.isArray(data)) items = data;
        const norm = items.map((x) => ({
          kode: x.kode_kegiatan || x.kode || x.id || "",
          nama: x.nama_kegiatan || x.nm_kegiatan || x.nama || x.kegiatan || "",
        }));
        setKegiatanList(norm);
      } catch (e) { console.warn("Gagal memuat daftar kegiatan:", e); }
    };
    loadKegiatan();
  }, []);
  const kegiatanKeywordDeb = useDebouncedValue(kegiatanKeyword, 200);
  const kegiatanSuggestions = useMemo(() => {
    const q = (kegiatanKeywordDeb || "").toLowerCase().trim();
    if (!q) return kegiatanList.slice(0, 8);
    return kegiatanList
      .filter((k) => String(k.kode || "").toLowerCase().includes(q) || String(k.nama || "").toLowerCase().includes(q))
      .slice(0, 8);
  }, [kegiatanKeywordDeb, kegiatanList]);
  useEffect(() => {
    if (!form?.kode) return;
    const found = kegiatanList.find((k) => String(k.kode) === String(form.kode));
    if (found) setKegiatanKeyword(found.nama);
  }, [form.kode, kegiatanList]);
  useEffect(() => {
    const key = (kegiatanKeyword || "").trim().toLowerCase();
    if (!key) return;
    const exact = kegiatanList.find((k) => String(k.nama || "").toLowerCase() === key);
    if (exact) updateForm("kode", exact.kode);
  }, [kegiatanKeyword, kegiatanList]);

  // --- Autocomplete Kelurahan (kd_kel -> nm_kel) ---
  const [kelurahanList, setKelurahanList] = useState([]);
  const [kelurahanKeyword, setKelurahanKeyword] = useState("");
  const [showKelurahanDropdown, setShowKelurahanDropdown] = useState(false);
  const [activeKelurahanIndex, setActiveKelurahanIndex] = useState(0);
  useEffect(() => {
    const loadKelurahan = async () => {
      try {
        const res = await fetch(`/ranap/laporan/grafik/kelurahan-all?t=${Date.now()}`, {
          headers: { Accept: "application/json", "Cache-Control": "no-cache" },
        });
        const data = await res.json().catch(() => ({}));
        let items = [];
        if (Array.isArray(data)) items = data; else if (Array.isArray(data?.data)) items = data.data;
        const norm = items.map((x) => ({ kd_kel: x.kd_kel || x.kode || x.id || "", nm_kel: x.nm_kel || x.nama || "" }));
        setKelurahanList(norm);
      } catch (e) { console.warn("Gagal memuat daftar kelurahan:", e); }
    };
    loadKelurahan();
  }, []);
  const kelurahanKeywordDeb = useDebouncedValue(kelurahanKeyword, 200);
  const kelurahanSuggestions = useMemo(() => {
    const q = (kelurahanKeywordDeb || "").toLowerCase().trim();
    if (!q) return kelurahanList.slice(0, 8);
    return kelurahanList
      .filter((k) => String(k.kd_kel || "").toLowerCase().includes(q) || String(k.nm_kel || "").toLowerCase().includes(q))
      .slice(0, 8);
  }, [kelurahanKeywordDeb, kelurahanList]);
  useEffect(() => {
    if (!form?.kd_kel) return;
    const found = kelurahanList.find((k) => String(k.kd_kel) === String(form.kd_kel));
    if (found) setKelurahanKeyword(found.nm_kel);
  }, [form.kd_kel, kelurahanList]);
  useEffect(() => {
    const key = (kelurahanKeyword || "").trim().toLowerCase();
    if (!key) return;
    const exact = kelurahanList.find((k) => String(k.nm_kel || "").toLowerCase() === key);
    if (exact) updateForm("kd_kel", exact.kd_kel);
  }, [kelurahanKeyword, kelurahanList]);

  const columnNames = useMemo(() => meta.columns?.map((c) => c.name) || [], [meta]);
  const displayColumns = useMemo(() => {
    const pk = meta.primary_key || "kd_jadwal";
    return (meta.columns || [])
      .map((c) => ({ name: c.name, type: (c.type || "").toLowerCase() }))
      .filter((c) => c.name !== pk && !["created_at", "updated_at"].includes(c.name));
  }, [meta]);
  const inputColumns = useMemo(() => {
    const pk = meta.primary_key || "kd_jadwal";
    return displayColumns.filter((c) => c.name !== pk);
  }, [displayColumns, meta]);

  function updateForm(name, value) {
    setForm((f) => ({ ...f, [name]: value }));
    setFieldErrors((prev) => ({ ...prev, [name]: undefined }));
  }

  // Label untuk field tertentu
  function labelFor(name) {
    if (name === "nip") return "Cari Petugas";
    if (name === "kode") return "Cari Kegiatan";
    if (name === "kd_kel") return "Cari Kelurahan/Desa";
    return name;
  }

  // Label untuk header tabel
  function headerLabelFor(name) {
    if (name === "nip") return "Nama Petugas";
    if (name === "kode") return "Nama Kegiatan";
    if (name === "kd_kel") return "Nama Kelurahan";
    return name;
  }

  // Filtering + Pagination client-side
  const filteredRows = useMemo(() => {
    let result = Array.isArray(rows) ? rows : [];
    // Filter tanggal mulai
    if (filterStart) {
      const s = new Date(filterStart);
      result = result.filter((r) => {
        const d = r?.tanggal ? new Date(r.tanggal) : null;
        return d && d >= s;
      });
    }
    // Filter tanggal akhir
    if (filterEnd) {
      const e = new Date(filterEnd);
      result = result.filter((r) => {
        const d = r?.tanggal ? new Date(r.tanggal) : null;
        return d && d <= e;
      });
    }
    // Filter petugas (nip atau nama)
    const qPet = (filterNipKeyword || "").toLowerCase().trim();
    if (qPet) {
      result = result.filter((r) => {
        const nip = String(r?.nip || "").toLowerCase();
        const nama = (petugasList.find((p) => String(p.nip) === String(r?.nip))?.nama || "").toLowerCase();
        return nip.includes(qPet) || nama.includes(qPet);
      });
    }
    // Filter kelurahan/desa (kd_kel atau nm_kel)
    const qKel = (filterKelKeyword || "").toLowerCase().trim();
    if (qKel) {
      result = result.filter((r) => {
        const kd = String(r?.kd_kel || "").toLowerCase();
        const nm = (kelurahanList.find((k) => String(k.kd_kel) === String(r?.kd_kel))?.nm_kel || "").toLowerCase();
        return kd.includes(qKel) || nm.includes(qKel);
      });
    }
    // Filter status
    if (filterStatus) {
      result = result.filter((r) => String(r?.status || "") === String(filterStatus));
    }
    return result;
  }, [rows, filterStart, filterEnd, filterNipKeyword, filterKelKeyword, filterStatus, petugasList, kelurahanList]);

  const totalPages = Math.max(1, Math.ceil((filteredRows?.length || 0) / pageSize));
  const pageRows = useMemo(() => {
    const start = (page - 1) * pageSize;
    const end = start + pageSize;
    return filteredRows.slice(start, end);
  }, [filteredRows, page, pageSize]);
  useEffect(() => {
    const total = Math.max(1, Math.ceil((filteredRows?.length || 0) / pageSize));
    if (page > total) setPage(total);
    if (page < 1) setPage(1);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filteredRows, pageSize]);

  // Render field dengan searchable dropdown untuk nip, kode, kd_kel
  function renderField(c) {
    const name = c.name;
    const err = fieldErrors?.[name];
    const errText = Array.isArray(err) ? err.join(", ") : err;
    if (name === "nip") {
      return (
        <div className="space-y-2 relative">
          <input
            value={petugasKeyword}
            onChange={(e) => {
              setPetugasKeyword(e.target.value);
              setShowPetugasDropdown(true);
              setActivePetugasIndex(0);
            }}
            onFocus={() => setShowPetugasDropdown(true)}
            onBlur={() => setTimeout(() => setShowPetugasDropdown(false), 120)}
            onKeyDown={(e) => {
              if (e.key === "ArrowDown") {
                setShowPetugasDropdown(true);
                setActivePetugasIndex((i) => Math.min(i + 1, Math.max(0, petugasSuggestions.length - 1)));
              } else if (e.key === "ArrowUp") {
                setActivePetugasIndex((i) => Math.max(0, i - 1));
              } else if (e.key === "Enter") {
                const choice = petugasSuggestions[activePetugasIndex];
                if (choice) {
                  updateForm("nip", choice.nip);
                  setPetugasKeyword(choice.nama);
                  setShowPetugasDropdown(false);
                }
              }
            }}
            className="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
            placeholder="Cari Petugas (nama atau nip)"
          />
          <div className="mt-1 text-xs text-gray-600">Terpilih: nama = {petugasKeyword || "-"}{`, nip = ${form.nip || "-"}`}</div>
          {errText && <p className="text-xs text-red-600">{errText}</p>}
          <AnimatePresence>
            {showPetugasDropdown && petugasSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {petugasSuggestions.map((p, i) => (
                  <li key={`${p.nip}-${i}`} className={`px-3 py-2 text-sm cursor-pointer ${i === activePetugasIndex ? "bg-emerald-50" : "hover:bg-gray-50"}`} onMouseDown={(evt) => { evt.preventDefault(); updateForm("nip", p.nip); setPetugasKeyword(p.nama); setShowPetugasDropdown(false); }}>
                    <span className="font-mono mr-2">{p.nip}</span>
                    <span>{p.nama}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
      );
    }
    if (name === "kode") {
      return (
        <div className="space-y-2 relative">
          <input
            value={kegiatanKeyword}
            onChange={(e) => {
              setKegiatanKeyword(e.target.value);
              setShowKegiatanDropdown(true);
              setActiveKegiatanIndex(0);
            }}
            onFocus={() => setShowKegiatanDropdown(true)}
            onBlur={() => setTimeout(() => setShowKegiatanDropdown(false), 120)}
            onKeyDown={(e) => {
              if (e.key === "ArrowDown") {
                setShowKegiatanDropdown(true);
                setActiveKegiatanIndex((i) => Math.min(i + 1, Math.max(0, kegiatanSuggestions.length - 1)));
              } else if (e.key === "ArrowUp") {
                setActiveKegiatanIndex((i) => Math.max(0, i - 1));
              } else if (e.key === "Enter") {
                const choice = kegiatanSuggestions[activeKegiatanIndex];
                if (choice) {
                  updateForm("kode", choice.kode);
                  setKegiatanKeyword(choice.nama);
                  setShowKegiatanDropdown(false);
                }
              }
            }}
            className="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
            placeholder="Cari Kegiatan (nama_kegiatan atau kode)"
          />
          <div className="mt-1 text-xs text-gray-600">Terpilih: nama_kegiatan = {kegiatanKeyword || "-"}{`, kode = ${form.kode || "-"}`}</div>
          {errText && <p className="text-xs text-red-600">{errText}</p>}
          <AnimatePresence>
            {showKegiatanDropdown && kegiatanSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {kegiatanSuggestions.map((k, i) => (
                  <li key={`${k.kode}-${i}`} className={`px-3 py-2 text-sm cursor-pointer ${i === activeKegiatanIndex ? "bg-emerald-50" : "hover:bg-gray-50"}`} onMouseDown={(evt) => { evt.preventDefault(); updateForm("kode", k.kode); setKegiatanKeyword(k.nama); setShowKegiatanDropdown(false); }}>
                    <span className="font-mono mr-2">{k.kode}</span>
                    <span>{k.nama}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
      );
    }
    if (name === "kd_kel") {
      return (
        <div className="space-y-2 relative">
          <input
            value={kelurahanKeyword}
            onChange={(e) => {
              setKelurahanKeyword(e.target.value);
              setShowKelurahanDropdown(true);
              setActiveKelurahanIndex(0);
            }}
            onFocus={() => setShowKelurahanDropdown(true)}
            onBlur={() => setTimeout(() => setShowKelurahanDropdown(false), 120)}
            onKeyDown={(e) => {
              if (e.key === "ArrowDown") {
                setShowKelurahanDropdown(true);
                setActiveKelurahanIndex((i) => Math.min(i + 1, Math.max(0, kelurahanSuggestions.length - 1)));
              } else if (e.key === "ArrowUp") {
                setActiveKelurahanIndex((i) => Math.max(0, i - 1));
              } else if (e.key === "Enter") {
                const choice = kelurahanSuggestions[activeKelurahanIndex];
                if (choice) {
                  updateForm("kd_kel", choice.kd_kel);
                  setKelurahanKeyword(choice.nm_kel);
                  setShowKelurahanDropdown(false);
                }
              }
            }}
            className="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
            placeholder="Cari Kelurahan/Desa (nm_kel atau kd_kel)"
          />
          <div className="mt-1 text-xs text-gray-600">Terpilih: nm_kel = {kelurahanKeyword || "-"}{`, kd_kel = ${form.kd_kel || "-"}`}</div>
          {errText && <p className="text-xs text-red-600">{errText}</p>}
          <AnimatePresence>
            {showKelurahanDropdown && kelurahanSuggestions.length > 0 && (
              <motion.ul initial={{ opacity: 0, y: -4 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -4 }} className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow">
                {kelurahanSuggestions.map((k, i) => (
                  <li key={`${k.kd_kel}-${i}`} className={`px-3 py-2 text-sm cursor-pointer ${i === activeKelurahanIndex ? "bg-emerald-50" : "hover:bg-gray-50"}`} onMouseDown={(evt) => { evt.preventDefault(); updateForm("kd_kel", k.kd_kel); setKelurahanKeyword(k.nm_kel); setShowKelurahanDropdown(false); }}>
                    <span className="font-mono mr-2">{k.kd_kel}</span>
                    <span>{k.nm_kel}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
      );
    }
    return (
      <>
        <InputForType type={c.type} name={name} value={form[name] ?? ""} onChange={updateForm} />
        {errText && <p className="text-xs text-red-600 mt-1">{errText}</p>}
      </>
    );
  }

  function val(id) {
    return id == null ? "" : String(id);
  }
  function urlFor(template, id) {
    return (template || "").replace("__ID__", encodeURIComponent(val(id)));
  }

  async function fetchMeta(signal) {
    try {
      const res = await fetch(metaUrl, { headers: { Accept: "application/json" }, signal });
      const json = await res.json();
      if (json?.error) throw new Error(json?.message || "Gagal mengambil meta");
      setMeta({ columns: json.columns || [], primary_key: json.primary_key || "kd_jadwal" });
    } catch (e) {
      if (e?.name === "AbortError") return;
      console.error("fetchMeta error", e);
      setError(e.message || "Gagal mengambil meta");
    }
  }

  async function fetchList(signal) {
    setLoading(true);
    setError("");
    try {
      const res = await fetch(listUrl, { headers: { Accept: "application/json" }, signal });
      const json = await res.json();
      if (json?.error) throw new Error(json?.message || "Gagal mengambil data");
      setRows(Array.isArray(json.data) ? json.data : []);
    } catch (e) {
      if (e?.name === "AbortError") return;
      console.error("fetchList error", e);
      setError(e.message || "Gagal mengambil data");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    const ac = new AbortController();
    const sig = ac.signal;
    Promise.all([fetchMeta(sig), fetchList(sig)]).catch((e) => console.warn("Initial load aborted or failed:", e?.message || e));
    return () => ac.abort();
  }, []);

  async function handleSubmit(e) {
    e.preventDefault();
    if (editing) { await submitEdit(); return; }
    setSaving(true);
    setError("");
    setFieldErrors({});
    try {
      const payload = {};
      for (const c of inputColumns) {
        if (form.hasOwnProperty(c.name)) payload[c.name] = form[c.name];
      }
      const res = await fetch(storeUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify(payload),
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.error) {
        if (json?.errors && typeof json.errors === "object") setFieldErrors(json.errors);
        throw new Error(json?.message || json?.detail || "Gagal menyimpan");
      }
      setForm({});
      setPetugasKeyword(""); setKegiatanKeyword(""); setKelurahanKeyword("");
      try { await fetchList(); } catch (reloadErr) {
        console.warn("Reload setelah simpan gagal:", reloadErr);
        setError(`Data tersimpan, namun gagal memuat ulang: ${reloadErr?.message || "Tidak diketahui"}`);
      }
    } catch (e) {
      console.error("handleSubmit error", e);
      setError(e.message || "Gagal menyimpan");
    } finally {
      setSaving(false);
    }
  }

  function startEdit(row) {
    setEditing(row);
    const f = {};
    for (const c of inputColumns) {
      f[c.name] = row?.[c.name] ?? "";
    }
    setForm(f);
    setFieldErrors({});
    // sinkronkan keyword dropdown saat edit
    const nipVal = row?.nip;
    if (nipVal != null) {
      const p = petugasList.find((x) => String(x.nip) === String(nipVal));
      setPetugasKeyword(p?.nama || "");
    } else { setPetugasKeyword(""); }
    const kodeVal = row?.kode;
    if (kodeVal != null) {
      const k = kegiatanList.find((x) => String(x.kode) === String(kodeVal));
      setKegiatanKeyword(k?.nama || "");
    } else { setKegiatanKeyword(""); }
    const kdKelVal = row?.kd_kel;
    if (kdKelVal != null) {
      const g = kelurahanList.find((x) => String(x.kd_kel) === String(kdKelVal));
      setKelurahanKeyword(g?.nm_kel || "");
    } else { setKelurahanKeyword(""); }
  }
  function cancelEdit() { setEditing(null); setForm({}); setFieldErrors({}); setPetugasKeyword(""); setKegiatanKeyword(""); setKelurahanKeyword(""); }

  async function submitEdit() {
    if (!editing) return;
    setSaving(true);
    setError("");
    setFieldErrors({});
    try {
      const pk = meta.primary_key || "kd_jadwal";
      const id = editing?.[pk];
      const payload = {};
      for (const c of inputColumns) {
        if (form.hasOwnProperty(c.name)) payload[c.name] = form[c.name];
      }
      const res = await fetch(urlFor(updateUrlTemplate, id), {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrfToken,
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify(payload),
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.error) {
        if (json?.errors && typeof json.errors === "object") setFieldErrors(json.errors);
        throw new Error(json?.message || json?.detail || "Gagal mengubah data");
      }
      setEditing(null);
      setForm({});
      setPetugasKeyword(""); setKegiatanKeyword(""); setKelurahanKeyword("");
      await fetchList();
    } catch (e) {
      console.error("submitEdit error", e);
      setError(e.message || "Gagal mengubah data");
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete(row) {
    if (!row) return;
    const pk = meta.primary_key || "kd_jadwal";
    const id = row?.[pk];
    if (id == null) return;
    // SweetAlert2 confirm jika tersedia, fallback ke confirm()
    let ok = false;
    try {
      if (window?.Swal) {
        const result = await window.Swal.fire({
          title: "Konfirmasi",
          text: `Hapus data dengan ${pk} = ${id}?`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya, hapus",
          cancelButtonText: "Batal",
          reverseButtons: true,
        });
        ok = !!result?.isConfirmed;
      }
    } catch {}
    if (!ok && !confirm(`Hapus data dengan ${pk} = ${id}?`)) return;
    setSaving(true);
    setError("");
    try {
      const res = await fetch(urlFor(deleteUrlTemplate, id), {
        method: "DELETE",
        headers: { Accept: "application/json", "X-CSRF-TOKEN": csrfToken, "X-Requested-With": "XMLHttpRequest" },
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.error) throw new Error(json?.message || json?.detail || "Gagal menghapus data");
      await fetchList();
      try { toast.success("Jadwal berhasil dihapus"); } catch {}
    } catch (e) {
      console.error("handleDelete error", e);
      setError(e.message || "Gagal menghapus data");
      try { toast.error(e.message || "Gagal menghapus data"); } catch {}
    } finally {
      setSaving(false);
    }
  }

  return (
    <motion.div
      initial={{ opacity: 0, y: prefersReducedMotion ? 0 : 4 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: prefersReducedMotion ? 0 : 0.2, ease: "easeOut" }}
      className="p-4 space-y-6"
    >
      

      <AnimatePresence initial={false}>
        {error && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="rounded-lg border border-red-300 bg-red-50 p-3 text-red-700 text-sm"
          >
            {error}
          </motion.div>
        )}
      </AnimatePresence>

      {/* Form Tambah / Edit */}
      <form onSubmit={handleSubmit} className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
        <div className="flex items-center justify-between mb-2">
          <h3 className="text-base font-semibold">Form Jadwal</h3>
          <button type="button" onClick={() => setShowForm((s) => !s)} className="px-3 py-1 text-xs rounded border hover:bg-gray-50 transition-colors duration-150 ease-out">
            {showForm ? "Ciutkan" : "Buka"}
          </button>
        </div>
        <AnimatePresence initial={false}>
          {showForm && (
            <motion.div initial={{ height: 0, opacity: 0 }} animate={{ height: "auto", opacity: 1 }} exit={{ height: 0, opacity: 0 }} transition={{ duration: 0.2 }} className="overflow-hidden">
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {inputColumns.map((c) => (
                  <div key={c.name} className="space-y-1">
                    <label className="block text-xs font-medium text-gray-700">{labelFor(c.name)}</label>
                    {renderField(c)}
                  </div>
                ))}
              </div>
              <div className="mt-4 flex items-center gap-3">
                <button type="submit" className="px-4 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700 disabled:opacity-60 transition-colors duration-150 ease-out" disabled={saving}>
                  {editing ? (saving ? "Menyimpan Perubahan..." : "Simpan Perubahan") : saving ? "Menyimpan..." : "Simpan"}
                </button>
                {editing && (
                  <button type="button" onClick={cancelEdit} className="px-4 py-2 rounded border text-sm hover:bg-gray-50 transition-colors duration-150 ease-out">Batal</button>
                )}
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </form>

      {/* Card Pencarian */}
      <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
        <div className="flex items-center justify-between mb-2">
          <h3 className="text-base font-semibold">Pencarian</h3>
          <button type="button" onClick={clearFilters} className="px-3 py-1 text-xs rounded border hover:bg-gray-50 transition-colors duration-150 ease-out">Reset</button>
        </div>
        <div className="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div className="space-y-1">
              <label className="text-xs text-gray-700">Tanggal mulai</label>
              <input type="date" value={filterStart} onChange={(e)=>{ setFilterStart(e.target.value); setPage(1); }} className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 ease-out" />
            </div>
            <div className="space-y-1">
              <label className="text-xs text-gray-700">Tanggal akhir</label>
              <input type="date" value={filterEnd} onChange={(e)=>{ setFilterEnd(e.target.value); setPage(1); }} className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 ease-out" />
            </div>
            <div className="space-y-1">
              <label className="text-xs text-gray-700">Petugas</label>
              <input type="text" value={filterNipKeyword} onChange={(e)=>{ setFilterNipKeyword(e.target.value); setPage(1); }} placeholder="ketik nama atau nip" className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder:text-gray-400 transition-colors duration-200 ease-out" />
            </div>
            <div className="space-y-1">
              <label className="text-xs text-gray-700">Kelurahan/Desa</label>
              <input type="text" value={filterKelKeyword} onChange={(e)=>{ setFilterKelKeyword(e.target.value); setPage(1); }} placeholder="ketik nm_kel atau kd_kel" className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder:text-gray-400 transition-colors duration-200 ease-out" />
            </div>
            <div className="space-y-1">
              <label className="text-xs text-gray-700">Status</label>
              <select value={filterStatus} onChange={(e)=>{ setFilterStatus(e.target.value); setPage(1); }} className="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 ease-out">
                <option value="">-- Semua --</option>
                <option value="Belum">Belum</option>
                <option value="Tunda">Tunda</option>
                <option value="Sudah">Sudah</option>
                <option value="Batal">Batal</option>
              </select>
            </div>
          </div>
        <p className="mt-2 text-xs text-gray-500">Menampilkan {filteredRows.length} dari {rows.length} data</p>
      </div>

      {/* Tabel Data Modern */}
      <div className="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div className="mb-3 flex items-center justify-between">
          <div className="text-sm text-gray-600">Total data: {filteredRows.length} dari {rows.length}</div>
        </div>
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-gray-700">
                <th className="sticky top-0 z-10 bg-gray-50 px-3 py-2 text-left font-medium">No</th>
                {displayColumns.map((c) => (
                  <th key={c.name} className="sticky top-0 z-10 bg-gray-50 px-3 py-2 text-left font-medium uppercase text-xs tracking-wide border-b border-gray-200">{headerLabelFor(c.name)}</th>
                ))}
                <th className="sticky top-0 z-10 bg-gray-50 px-3 py-2 text-left font-medium uppercase text-xs tracking-wide border-b border-gray-200">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                Array.from({ length: Math.min(pageSize, 5) }).map((_, i) => (
                  <tr key={`skeleton-${i}`} className="border-t animate-pulse">
                    <td className="px-3 py-2"><div className="h-4 w-6 bg-gray-200 rounded"></div></td>
                    {displayColumns.map((c, j) => (
                      <td key={`s-${c.name}-${j}`} className="px-3 py-2"><div className="h-4 w-24 bg-gray-200 rounded"></div></td>
                    ))}
                    <td className="px-3 py-2"><div className="h-6 w-16 bg-gray-200 rounded"></div></td>
                  </tr>
                ))
              ) : (
                pageRows.map((row, idx) => (
                  <tr key={idx} className="border-t hover:bg-gray-50 transition-colors duration-150 ease-out">
                    <td className="px-3 py-2">{(page - 1) * pageSize + idx + 1}</td>
                    {displayColumns.map((c) => {
                      let value = row?.[c.name];
                      if (c.name === "nip" && value != null) {
                        const f = petugasList.find((p) => String(p.nip) === String(value));
                        value = f?.nama ?? value;
                      }
                      if (c.name === "kode" && value != null) {
                        const f = kegiatanList.find((k) => String(k.kode) === String(value));
                        value = f?.nama ?? value;
                      }
                      if (c.name === "kd_kel" && value != null) {
                        const f = kelurahanList.find((k) => String(k.kd_kel) === String(value));
                        value = f?.nm_kel ?? value;
                      }
                      return (
                        <td key={c.name} className="px-3 py-2">
                          <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{String(value ?? "")}</span>
                        </td>
                      );
                    })}
                    <td className="px-3 py-2">
                      <div className="flex items-center gap-2">
                        <button className="px-3 py-1.5 rounded-md bg-white border text-xs hover:bg-gray-50" onClick={() => startEdit(row)}>Edit</button>
                        <button className="px-3 py-1.5 rounded-md bg-rose-600 text-white text-xs hover:bg-rose-700 shadow-sm" onClick={() => handleDelete(row)}>Hapus</button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        {loading && <div className="mt-3 text-xs text-gray-500">Memuat data...</div>}
      </div>
      <div className="mb-2 text-sm text-gray-600 flex items-center justify-between">
        <span>Total data: {filteredRows.length} dari {rows.length}</span>
        <div className="flex items-center gap-2">
          <label className="text-xs text-gray-600">Baris/hal</label>
          <select className="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-150 ease-out" value={pageSize} onChange={(e)=>{ setPageSize(Number(e.target.value)); setPage(1); }}>
            <option value={5}>5</option>
            <option value={10}>10</option>
            <option value={20}>20</option>
            <option value={50}>50</option>
          </select>
          <span className="text-xs text-gray-600">Hal {page} / {totalPages}</span>
          <button className="px-2 py-1 border rounded text-xs hover:bg-gray-50 transition-colors duration-150 ease-out" disabled={page<=1} onClick={()=>setPage((p)=>Math.max(1, p-1))}>Prev</button>
          <button className="px-2 py-1 border rounded text-xs hover:bg-gray-50 transition-colors duration-150 ease-out" disabled={page>=totalPages} onClick={()=>setPage((p)=>Math.min(totalPages, p+1))}>Next</button>
        </div>
      </div>
    </motion.div>
  );
}