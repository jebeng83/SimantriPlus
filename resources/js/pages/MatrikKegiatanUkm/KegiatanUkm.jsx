import React, {
  useEffect,
  useMemo,
  useState,
  useRef,
  useCallback,
} from "react";
import { motion, AnimatePresence } from "framer-motion";
import { toast } from "@/tools/toast";
import { RefreshCcw, Loader2, CalendarDays, Tag, UserCircle2 } from "lucide-react";
// Debounce hook untuk autocomplete dan performa input
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
  // Override input untuk field tertentu
  if (name === "tujuan_kegiatan" || name === "sasaran_kegiatan") {
    return <textarea rows={4} {...common} />;
  }
  if (name === "tahun") {
    const now = new Date().getFullYear();
    const yearOptions = Array.from({ length: 11 }, (_, i) => now - 5 + i);
    return (
      <select
        name={name}
        value={value ?? ""}
        onChange={(e) => onChange(name, e.target.value)}
        className={common.className}
      >
        <option value="">-- Pilih Tahun --</option>
        {yearOptions.map((yr) => (
          <option key={yr} value={yr}>
            {yr}
          </option>
        ))}
      </select>
    );
  }
  const lower = (type || "").toLowerCase();
  if (["date"].includes(lower)) return <input type="date" {...common} />;
  if (["datetime", "timestamp", "datetime2", "datetimeoffset"].includes(lower))
    return <input type="datetime-local" {...common} />;
  if (
    [
      "int",
      "bigint",
      "tinyint",
      "smallint",
      "mediumint",
      "decimal",
      "float",
      "double",
    ].includes(lower)
  )
    return <input type="number" step="any" {...common} />;
  if (["text", "longtext"].includes(lower))
    return <textarea rows={3} {...common} />;
  return <input type="text" {...common} />;
}

export default function KegiatanUkm({
  metaUrl,
  listUrl,
  storeUrl,
  updateUrlTemplate,
  deleteUrlTemplate,
  csrfToken,
}) {
  // Helper: header builder untuk konsistenkan request di dev/prod.
  const isLocalDev = (() => {
    try {
      const hn = (window && window.location && window.location.hostname) || '';
      return hn === '127.0.0.1' || hn === 'localhost';
    } catch {
      return false;
    }
  })();
  const buildHeaders = (extra = {}) => {
    const base = { Accept: 'application/json' };
    // Tambahkan CSRF & X-Requested-With untuk request write
    if (extra.__withCsrf) {
      base['X-CSRF-TOKEN'] = csrfToken;
      base['X-Requested-With'] = 'XMLHttpRequest';
    }
    // Di lingkungan lokal, izinkan bypass LoginAuth untuk uji API
    if (isLocalDev) {
      base['X-API-Testing'] = 'true';
      base['Cache-Control'] = 'no-cache';
    }
    // Gabungkan header lain (kecuali flag khusus)
    const { __withCsrf, ...rest } = extra || {};
    return { ...base, ...rest };
  };
  // Normalisasi URL dari data-attribute Blade (decode &amp; menjadi & dan trim spasi)
  const normalizeUrl = (u) => {
    try {
      return String(u || "").replace(/&amp;/g, "&").trim();
    } catch {
      return u || "";
    }
  };
  const metaUrlNorm = useMemo(() => normalizeUrl(metaUrl), [metaUrl]);
  const listUrlNorm = useMemo(() => normalizeUrl(listUrl), [listUrl]);
  const storeUrlNorm = useMemo(() => normalizeUrl(storeUrl), [storeUrl]);
  const updateUrlTplNorm = useMemo(() => normalizeUrl(updateUrlTemplate), [updateUrlTemplate]);
  const deleteUrlTplNorm = useMemo(() => normalizeUrl(deleteUrlTemplate), [deleteUrlTemplate]);
  const [meta, setMeta] = useState({ columns: [], primary_key: "id" });
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [form, setForm] = useState({});
  const [editing, setEditing] = useState(null); // row object being edited
  const [saving, setSaving] = useState(false);
  const [showTambah, setShowTambah] = useState(true);
  const [reloading, setReloading] = useState(false);

  // Autocomplete Jabatan (kd_jbtn / jabatan)
  const [jabatanList, setJabatanList] = useState([]);
  const [jabatanKeyword, setJabatanKeyword] = useState("");
  const [showJabatanDropdown, setShowJabatanDropdown] = useState(false);
  const [activeJabatanIndex, setActiveJabatanIndex] = useState(0);

  useEffect(() => {
    const loadJabatan = async () => {
      try {
        const ts = Date.now();
        const res = await fetch(`/api/jabatan?t=${ts}`, {
          headers: { Accept: "application/json", "Cache-Control": "no-cache" },
        });
        const data = await res.json().catch(() => ({}));
        let items = [];
        if (Array.isArray(data)) items = data;
        else if (Array.isArray(data?.data)) items = data.data;
        const norm = items.map((x) => ({
          kd_jbtn: x.kd_jbtn || x.kode || x.id || "",
          nm_jbtn: x.nm_jbtn || x.nama || x.name || x.jabatan || "",
        }));
        setJabatanList(norm);
      } catch (e) {
        console.warn("Gagal memuat daftar jabatan:", e);
      }
    };
    loadJabatan();
  }, []);

  const jabatanKeywordDeb = useDebouncedValue(jabatanKeyword, 200);
  const jabatanSuggestions = useMemo(() => {
    const q = (jabatanKeywordDeb || "").toLowerCase().trim();
    if (!q) return jabatanList.slice(0, 8);
    return jabatanList
      .filter(
        (j) =>
          String(j.kd_jbtn || "")
            .toLowerCase()
            .includes(q) ||
          String(j.nm_jbtn || "")
            .toLowerCase()
            .includes(q)
      )
      .slice(0, 8);
  }, [jabatanKeywordDeb, jabatanList]);

  // Sinkronisasi input tampil dengan nm_jbtn saat kd_jbtn pada form berubah
  useEffect(() => {
    if (!form?.kd_jbtn) return;
    const found = jabatanList.find(
      (j) => String(j.kd_jbtn) === String(form.kd_jbtn)
    );
    if (found) setJabatanKeyword(found.nm_jbtn);
  }, [form.kd_jbtn, jabatanList]);

  // Jika nm_jbtn diketik lengkap persis, set kd_jbtn di form otomatis
  useEffect(() => {
    const key = (jabatanKeyword || "").trim().toLowerCase();
    if (!key) return;
    const exact = jabatanList.find(
      (j) => String(j.nm_jbtn || "").toLowerCase() === key
    );
    if (exact) {
      updateForm("kd_jbtn", exact.kd_jbtn);
      if (columnNames.includes("jabatan")) updateForm("jabatan", exact.nm_jbtn);
    }
  }, [jabatanKeyword, jabatanList]);

  // Kode otomatis (deklarasi dipindah di bawah columnNames)

  // formTahun moved below codeFieldName

  // Kode lokal berformat 4 digit (char(4)) dan mengurut dari data yang ada
  function getNextCodeLocal(year) {
    try {
      const filterByYear = (row) => {
        if (!year) return true;
        if (!columnNames.includes("tahun")) return true; // jika tidak ada kolom tahun, abaikan filter
        return String(row?.tahun ?? "") === String(year);
      };
      const codes = (rows || [])
        .filter(filterByYear)
        .map((r) => String(r?.[codeFieldName] ?? "").trim());
      const nums = codes
        .map((s) => (/^\d{1,4}$/.test(s) ? Number(s) : null))
        .filter((n) => typeof n === "number" && !Number.isNaN(n));
      const max = nums.length ? Math.max(...nums) : 0;
      const next = Math.min(max + 1, 9999);
      return String(next).padStart(4, "0");
    } catch (err) {
      console.warn("Gagal hitung kode lokal, fallback 0001:", err);
      return "0001";
    }
  }

  async function fetchNextCode(year) {
    try {
      const res = await fetch(
        `/api/kegiatan-ukm/next-code?tahun=${encodeURIComponent(year || "")}`,
        { headers: { Accept: "application/json" } }
      );
      const j = await res.json().catch(() => ({}));
      if (j?.kode) return j.kode;
      if (j?.data?.kode) return j.data.kode;
    } catch (e) {
      console.warn(
        "Gagal ambil kode otomatis dari server, menggunakan lokal:",
        e
      );
    }
    return getNextCodeLocal(year);
  }

  // moved setAutoCode and init effect below codeFieldName

  function renderField(c) {
    const name = c.name;
    if (name === "kd_jbtn") {
      return (
        <div className="space-y-2 relative">
          <input
            value={jabatanKeyword}
            onChange={(e) => {
              setJabatanKeyword(e.target.value);
              setShowJabatanDropdown(true);
              setActiveJabatanIndex(0);
            }}
            onFocus={() => setShowJabatanDropdown(true)}
            onBlur={() => setTimeout(() => setShowJabatanDropdown(false), 120)}
            onKeyDown={(e) => {
              if (e.key === "ArrowDown") {
                setShowJabatanDropdown(true);
                setActiveJabatanIndex((i) =>
                  Math.min(i + 1, Math.max(0, jabatanSuggestions.length - 1))
                );
              } else if (e.key === "ArrowUp") {
                setActiveJabatanIndex((i) => Math.max(0, i - 1));
              } else if (e.key === "Enter") {
                const choice = jabatanSuggestions[activeJabatanIndex];
                if (choice) {
                  updateForm("kd_jbtn", choice.kd_jbtn);
                  if (columnNames.includes("jabatan"))
                    updateForm("jabatan", choice.nm_jbtn);
                  setJabatanKeyword(choice.nm_jbtn);
                  setShowJabatanDropdown(false);
                }
              }
            }}
            className="w-full px-3 py-2 border rounded focus:outline-none focus:ring"
            placeholder="Cari Jabatan (nm_jbtn atau kd_jbtn)"
          />
          <div className="mt-1 text-xs text-gray-600">
            Terpilih: nm_jbtn ={" "}
            {jabatanKeyword ||
              (columnNames.includes("jabatan") ? form.jabatan || "-" : "-")}
            {`, kd_jbtn = ${form.kd_jbtn || "-"}`}
          </div>
          <AnimatePresence>
            {showJabatanDropdown && jabatanSuggestions.length > 0 && (
              <motion.ul
                initial={{ opacity: 0, y: -4 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -4 }}
                className="absolute z-50 mt-1 w-full max-h-52 overflow-auto rounded border bg-white shadow"
              >
                {jabatanSuggestions.map((j, i) => (
                  <li
                    key={`${j.kd_jbtn}-${i}`}
                    className={`px-3 py-2 text-sm cursor-pointer ${
                      i === activeJabatanIndex
                        ? "bg-emerald-50"
                        : "hover:bg-gray-50"
                    }`}
                    onMouseDown={(evt) => {
                      evt.preventDefault();
                      updateForm("kd_jbtn", j.kd_jbtn);
                      if (columnNames.includes("jabatan"))
                        updateForm("jabatan", j.nm_jbtn);
                      setJabatanKeyword(j.nm_jbtn);
                      setShowJabatanDropdown(false);
                    }}
                  >
                    <span className="font-mono mr-2">{j.kd_jbtn}</span>
                    <span>{j.nm_jbtn}</span>
                  </li>
                ))}
              </motion.ul>
            )}
          </AnimatePresence>
        </div>
      );
    }

    if (name === codeFieldName) {
      return (
        <InputForType
          type={c.type}
          name={name}
          value={form[name] ?? ""}
          onChange={updateForm}
        />
      );
    }

    return (
      <InputForType
        type={c.type}
        name={name}
        value={form[name] ?? ""}
        onChange={updateForm}
      />
    );
  }

  const columnNames = useMemo(
    () => meta.columns?.map((c) => c.name) || [],
    [meta]
  );
  // Deklarasi codeFieldName setelah columnNames untuk menghindari ReferenceError
  const codeFieldName = useMemo(() => {
    if (columnNames.includes("kode_kegiatan")) return "kode_kegiatan";
    if (columnNames.includes("kode")) return "kode";
    return null;
  }, [columnNames]);

  // formTahun dipindahkan ke sini agar aman dari TDZ
  const formTahun = form?.tahun;
  // Ref untuk mencegah inisialisasi kode berulang untuk kombinasi field+tahun yang sama
  const autoInitKeyRef = useRef("");

  // Prefill tahun ke tahun berjalan jika kolom 'tahun' ada dan belum diisi
  useEffect(() => {
    try {
      if (columnNames.includes("tahun") && (formTahun == null || formTahun === "")) {
        const nowYear = new Date().getFullYear();
        setForm((f) => ({ ...f, tahun: nowYear }));
      }
    } catch {}
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [columnNames]);

  // Inisialisasi kode otomatis jika kosong dengan dependency minimal
  useEffect(() => {
    if (!codeFieldName) return;
    const y = formTahun || new Date().getFullYear();
    const key = `${codeFieldName}:${y}`;
    // Jika user sudah mengisi kode secara manual, tandai dan jangan generate ulang
    if (form && form[codeFieldName]) {
      autoInitKeyRef.current = key;
      return;
    }
    // Hindari pengulangan untuk kombinasi yang sama
    if (autoInitKeyRef.current === key) return;
    autoInitKeyRef.current = key;
    let cancelled = false;
    (async () => {
      try {
        const next = await fetchNextCode(y);
        if (!cancelled) setForm((f) => ({ ...f, [codeFieldName]: next }));
      } catch {
        if (!cancelled)
          setForm((f) => ({ ...f, [codeFieldName]: getNextCodeLocal(y) }));
      }
    })();
    return () => {
      cancelled = true;
    };
  }, [codeFieldName, formTahun, rows]);

  // Tombol untuk generate ulang kode
  // const setAutoCode = async () => {
  //   if (!codeFieldName) return;
  //   const y = formTahun || new Date().getFullYear();
  //   const next = await fetchNextCode(y);
  //   setForm((f) => ({ ...f, [codeFieldName]: next }));
  // };
  const displayColumns = useMemo(() => {
    return (meta.columns || [])
      .map((c) => ({ name: c.name, type: (c.type || "").toLowerCase() }))
      .filter((c) => !["created_at", "updated_at"].includes(c.name));
  }, [meta]);

  const inputColumns = useMemo(() => {
    const pk = meta.primary_key || "id";
    return displayColumns.filter((c) => c.name !== pk);
  }, [displayColumns, meta]);

  function val(id) {
    return id == null ? "" : String(id);
  }

  function urlFor(template, id) {
    return (template || "").replace("__ID__", encodeURIComponent(val(id)));
  }

  function updateForm(name, value) {
    setForm((f) => ({ ...f, [name]: value }));
  }

  // Helper: fetch yang aman terhadap lingkungan yang tidak mendukung AbortController
  function safeFetch(url, options = {}, signal) {
    const opts = { ...options };
    try {
      if (
        signal &&
        typeof signal === "object" &&
        // cek properti umum AbortSignal
        ("aborted" in signal || "reason" in signal)
      ) {
        opts.signal = signal;
      }
    } catch {}
    return fetch(url, opts);
  }

  async function fetchMeta(signal) {
    try {
      const res = await safeFetch(
        metaUrlNorm,
        {
          headers: buildHeaders(),
        },
        signal
      );
      const json = await res.json();
      if (json?.error) throw new Error(json?.message || "Gagal mengambil meta");
      setMeta({
        columns: json.columns || [],
        primary_key: json.primary_key || "id",
      });
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
      const res = await safeFetch(
        listUrlNorm,
        {
          headers: buildHeaders(),
        },
        signal
      );
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

  // Soft reload: tidak mengosongkan tabel, hanya menampilkan spinner di tombol Reload
  async function softReload() {
    if (reloading) return;
    setReloading(true);
    setError("");
    try {
      const res = await safeFetch(listUrlNorm, { headers: buildHeaders() });
      const json = await res.json();
      if (json?.error) throw new Error(json?.message || "Gagal memuat ulang");
      setRows(Array.isArray(json.data) ? json.data : []);
    } catch (e) {
      console.error("softReload error", e);
      setError(e.message || "Gagal memuat ulang");
      try { toast.warning(e.message || "Gagal memuat ulang"); } catch {}
    } finally {
      setReloading(false);
    }
  }

  // Hard reload: benar-benar refresh halaman browser
  function hardReload() {
    try {
      setReloading(true);
      // Beri sedikit delay agar spinner terlihat sebelum reload
      setTimeout(() => {
        // Paksa reload penuh; fallback ke set href jika ada masalah
        try {
          window.location.reload();
        } catch (err) {
          try { window.location.href = window.location.href; } catch {}
        }
      }, 50);
    } catch (e) {
      console.warn('hardReload gagal, fallback softReload', e);
      softReload();
    }
  }

  // Click handler: klik biasa => hard reload; klik dengan Ctrl/Shift/Meta => soft reload
  function handleReloadClick(e) {
    if (reloading || loading) return;
    try {
      const useSoft = !!(e && (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey));
      if (useSoft) return softReload();
      return hardReload();
    } catch {
      return hardReload();
    }
  }

  async function confirmDelete(message = "Hapus data ini?") {
    try {
      if (window?.Swal) {
        const result = await window.Swal.fire({
          title: "Konfirmasi",
          text: message,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya, hapus",
          cancelButtonText: "Batal",
          reverseButtons: true,
        });
        return !!result?.isConfirmed;
      }
    } catch {}
    return confirm(message);
  }

  useEffect(() => {
    const ac = new AbortController();
    const sig = ac.signal;
    Promise.all([fetchMeta(sig), fetchList(sig)]).catch((e) =>
      console.warn("Initial load aborted or failed:", e?.message || e)
    );
    return () => ac.abort();
  }, []);

  // Helper: reset form setelah simpan/ubah agar siap input berikutnya
  function resetForm({ keepCollapsed = false } = {}) {
    try {
      // Bersihkan form dan state terkait
      setForm({});
      setEditing(null);
      setError("");
      setJabatanKeyword("");
      setShowJabatanDropdown(false);
      setActiveJabatanIndex(0);
      // Izinkan generate kode otomatis lagi untuk tahun yang sama
      autoInitKeyRef.current = "";
      // Pastikan panel Tambah tetap terbuka agar siap input selanjutnya
      if (!keepCollapsed) setShowTambah(true);
      // Fokuskan ke input pertama di form tambah (jika ada)
      setTimeout(() => {
        const first = document.querySelector(
          "#form-tambah-kegiatan input, #form-tambah-kegiatan select, #form-tambah-kegiatan textarea"
        );
        if (first) first.focus();
      }, 0);
    } catch {}
  }

  async function handleSubmit(e) {
    e.preventDefault();
    // Jika sedang mode edit, arahkan ke submitEdit agar tidak melakukan POST baru
    if (editing) {
      await submitEdit();
      return;
    }
    setSaving(true);
    setError("");
    try {
      const payload = {};
      for (const c of inputColumns) {
        if (form.hasOwnProperty(c.name)) payload[c.name] = form[c.name];
      }
      // Pastikan field kode (mis. kode_kegiatan/kode) ikut terkirim meskipun merupakan primary key
      if (codeFieldName && form.hasOwnProperty(codeFieldName)) {
        payload[codeFieldName] = form[codeFieldName];
      }
      const res = await safeFetch(storeUrlNorm, {
        method: "POST",
        headers: buildHeaders({ __withCsrf: true, "Content-Type": "application/json" }),
        body: JSON.stringify(payload),
      });
      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.error) {
        const msgParts = [];
        msgParts.push(json?.message || "Gagal menyimpan");
        if (json?.detail) msgParts.push(String(json.detail));
        if (Array.isArray(json?.fields) && json.fields.length > 0) {
          msgParts.push(`Kurang: ${json.fields.join(', ')}`);
        }
        throw new Error(msgParts.join(" - "));
      }
      resetForm();
      // Pisahkan reload agar jika gagal, user tetap tahu data tersimpan
      try {
        await fetchList();
        try { toast.success("Kegiatan berhasil ditambahkan"); } catch {}
      } catch (reloadErr) {
        console.warn("Reload setelah simpan gagal:", reloadErr);
        setError(
          `Data tersimpan, namun gagal memuat ulang: ${
            reloadErr?.message || "Tidak diketahui"
          }`
        );
        try { toast.warning("Data tersimpan, namun gagal memuat ulang"); } catch {}
      }
    } catch (e) {
      console.error("handleSubmit error", e);
      setError(e.message || "Gagal menyimpan");
      try { toast.error(e.message || "Gagal menyimpan"); } catch {}
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
    // tampilkan nm_jbtn pada input pencarian jika tersedia
    const kd = row?.kd_jbtn;
    if (kd != null) {
      const found = jabatanList.find((j) => String(j.kd_jbtn) === String(kd));
      setJabatanKeyword(found?.nm_jbtn || "");
    } else {
      setJabatanKeyword("");
    }
  }

  function cancelEdit() {
    resetForm({ keepCollapsed: true });
  }

  async function submitEdit() {
    if (!editing) return;
    setSaving(true);
    setError("");
    try {
      const pk = meta.primary_key || "id";
      const id = editing?.[pk];
      const payload = {};
      for (const c of inputColumns) {
        if (form.hasOwnProperty(c.name)) payload[c.name] = form[c.name];
      }
      const res = await safeFetch(urlFor(updateUrlTplNorm, id), {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (!res.ok || json?.error)
        throw new Error(json?.message || "Gagal mengupdate");
      resetForm();
      await fetchList();
      try { toast.success("Perubahan berhasil disimpan"); } catch {}
    } catch (e) {
      console.error("submitEdit error", e);
      setError(e.message || "Gagal mengupdate");
      try { toast.error(e.message || "Gagal mengupdate"); } catch {}
    } finally {
      setSaving(false);
    }
  }

  async function removeRow(row) {
    if (!row) return;
    const ok = await confirmDelete("Hapus data ini?");
    if (!ok) return;
    setSaving(true);
    setError("");
    try {
      const pk = meta.primary_key || "id";
      const id = row?.[pk];
      const res = await safeFetch(urlFor(deleteUrlTplNorm, id), {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": csrfToken,
        },
      });
      const json = await res.json();
      if (!res.ok || json?.error) {
        const detailMsg = typeof json?.detail === 'string' && json.detail ? `: ${json.detail}` : '';
        throw new Error((json?.message || "Gagal menghapus") + detailMsg);
      }
      await fetchList();
      try { toast.success("Kegiatan berhasil dihapus"); } catch {}
    } catch (e) {
      console.error("removeRow error", e);
      setError(e.message || "Gagal menghapus");
      try { toast.error(e.message || "Gagal menghapus"); } catch {}
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-white">
      <div className="w-full px-0 py-6 lg:py-8">
        <h3 className="text-2xl font-semibold tracking-tight text-gray-900 mb-6">
          :: [ Data Kegiatan Integrasi Layanan Primer ( ILP ) ] ::
        </h3>

        {error && (
          <div className="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {error}
          </div>
        )}

        {/* Form Tambah */}
        {!editing && (
          <motion.div
            className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6"
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ type: "spring", stiffness: 240, damping: 24 }}
          >
            <div className="flex items-center justify-between mb-4">
              <h5 className="text-xl font-semibold tracking-tight text-gray-900">
                Tambah Kegiatan
              </h5>
              <button
                type="button"
                onClick={() => setShowTambah((v) => !v)}
                className="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                aria-expanded={showTambah}
                aria-controls="form-tambah-kegiatan"
                title={showTambah ? "Ciutkan" : "Buka"}
              >
                {showTambah ? "Ciutkan" : "Buka"}
                <span className="ml-1">{showTambah ? "▴" : "▾"}</span>
              </button>
            </div>
            <AnimatePresence initial={false}>
              {showTambah && (
                <motion.div
                  id="form-tambah-kegiatan"
                  initial={{ height: 0, opacity: 0 }}
                  animate={{ height: "auto", opacity: 1 }}
                  exit={{ height: 0, opacity: 0 }}
                  transition={{ duration: 0.25, ease: "easeInOut" }}
                  className="overflow-hidden"
                >
                  <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                      {inputColumns.map((c) => (
                        <div key={c.name}>
                          <label className="block text-sm font-medium text-gray-700 mb-1">
                            {c.name === "kd_jbtn" ? "Pelaksana Program" : c.name}
                          </label>
                          {renderField(c)}
                        </div>
                      ))}
                    </div>
                    <div className="flex gap-2">
                      <button
                        type="submit"
                        disabled={saving}
                        className="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-white font-medium shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                      >
                        {saving ? "Menyimpan..." : "Simpan"}
                      </button>
                    </div>
                  </form>
                </motion.div>
              )}
            </AnimatePresence>
          </motion.div>
        )}

        {/* Tabel Data */}
        <motion.div
          className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm"
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ type: "spring", stiffness: 240, damping: 26 }}
        >
        <div className="flex justify-between items-center mb-3">
          <h5 className="text-xl font-semibold tracking-tight text-gray-900">
            Daftar Kegiatan
          </h5>
          <button
            onClick={handleReloadClick}
            disabled={reloading || loading}
            title="Klik untuk reload halaman. Tahan Ctrl/Shift untuk reload data saja."
            className="inline-flex items-center justify-center rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200 border border-gray-200 disabled:opacity-60"
          >
            {reloading ? (
              <span className="inline-flex items-center gap-2">
                <Loader2 className="h-4 w-4 animate-spin" />
                Memuat...
              </span>
            ) : (
              <span className="inline-flex items-center gap-2">
                <RefreshCcw className="h-4 w-4" />
                Reload Halaman
              </span>
            )}
          </button>
        </div>

          {loading ? (
            <div className="p-3">Memuat data...</div>
          ) : rows.length === 0 ? (
            <div className="p-3 text-gray-600">Belum ada data.</div>
          ) : (
            <div className="overflow-auto">
              <table className="min-w-full text-sm">
                <thead>
                  <tr className="bg-gray-50 text-gray-700">
                    {displayColumns.map((c) => (
                      <th key={c.name} className="sticky top-0 z-10 bg-gray-50 px-3 py-2 text-left font-medium uppercase text-xs tracking-wide border-b border-gray-200">
                        {c.name === "kd_jbtn" ? "Pelaksana Program" : c.name}
                      </th>
                    ))}
                    <th className="sticky top-0 z-10 bg-gray-50 px-3 py-2 text-left font-medium uppercase text-xs tracking-wide border-b border-gray-200">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  {rows.map((row, idx) => (
                    <tr key={row?.[meta.primary_key || "id"] ?? idx} className="border-t hover:bg-gray-50 transition-colors duration-150 ease-out">
                      {displayColumns.map((c) => (
                        <td key={c.name} className="px-3 py-2">
                          {c.name === "kd_jbtn"
                            ? (() => {
                                const kd = row?.[c.name];
                                const found = jabatanList.find((j) => String(j.kd_jbtn) === String(kd));
                                return (
                                  <span className="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                    {String(found?.nm_jbtn ?? kd ?? "")}
                                  </span>
                                );
                              })()
                            : (
                              <span className="inline-flex items-center rounded bg-white px-1 py-0.5 text-xs text-gray-900">
                                {String(row?.[c.name] ?? "")}
                              </span>
                            )}
                        </td>
                      ))}
                      <td className="px-3 py-2">
                        <div className="flex items-center gap-2">
                          <button onClick={() => startEdit(row)} className="px-3 py-1.5 rounded-md bg-amber-500 text-white text-xs hover:bg-amber-600 shadow-sm">Edit</button>
                          <button onClick={() => removeRow(row)} className="px-3 py-1.5 rounded-md bg-rose-600 text-white text-xs hover:bg-rose-700 shadow-sm">Hapus</button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </motion.div>

        {/* Panel Edit */}
        {editing && (
          <motion.div
            className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mt-6"
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ type: "spring", stiffness: 240, damping: 26 }}
          >
            <h2 className="text-xl font-semibold tracking-tight text-gray-900 mb-4">
              Edit Kegiatan
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {inputColumns.map((c) => (
                <div key={c.name}>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    {c.name}
                  </label>
                  {renderField(c)}
                </div>
              ))}
            </div>
            <div className="mt-4 flex gap-2">
              <button
                onClick={submitEdit}
                disabled={saving}
                className="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-white font-medium shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
              >
                {saving ? "Menyimpan..." : "Simpan Perubahan"}
              </button>
              <button
                onClick={cancelEdit}
                className="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200"
              >
                Batal
              </button>
            </div>
          </motion.div>
        )}
      </div>
    </div>
  );
}
