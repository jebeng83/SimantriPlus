import React, { useEffect, useMemo, useState, useRef } from 'react';
import { motion } from 'framer-motion';
import { createPortal } from 'react-dom';
// Audio assets for antrian announcement
import nomorAntrianUrl from '../Display/assets/nomor antrian.mp3';
import menujuUrl from '../Display/assets/menuju/Silahkan ke.mp3';

// CSRF token for Laravel POST
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
// Tambah helper sanitasi untuk path segment
const enc = (v) => encodeURIComponent(String(v ?? '').trim());
// Validasi helpers
const isValidNIK = (s) => /^\d{16}$/.test(String(s).trim());
const isValidNoKartu = (s) => /^\d{13}$/.test(String(s).trim());

// Hitung umur daftar berdasarkan tgl_lahir (YYYY-MM-DD) dan tanggal registrasi (YYYY-MM-DD)
// Menghasilkan number (umurdaftar) dan satuan ('Th' | 'Bl' | 'Hr') sesuai praktik SIMRS:
// - Jika >= 1 tahun: gunakan tahun (Th)
// - Jika < 1 tahun tapi >= 1 bulan: gunakan bulan (Bl)
// - Jika < 1 bulan: gunakan hari (Hr)
const calcUmurDaftar = (tglLahir, tglRegistrasi) => {
  try {
    const dobStr = String(tglLahir || '').trim();
    const regStr = String(tglRegistrasi || '').trim();
    if (!dobStr || !regStr) return { value: 0, unit: 'Th' };

    const [y1, m1, d1] = dobStr.split('-').map((x) => parseInt(x, 10));
    const [y2, m2, d2] = regStr.split('-').map((x) => parseInt(x, 10));
    if ([y1, m1, d1, y2, m2, d2].some((v) => !Number.isFinite(v))) return { value: 0, unit: 'Th' };

    const dob = new Date(y1, (m1 || 1) - 1, d1 || 1);
    const reg = new Date(y2, (m2 || 1) - 1, d2 || 1);

    // Jika tanggal registrasi sebelum tgl lahir, aman-kan
    if (reg < dob) return { value: 0, unit: 'Hr' };

    let years = y2 - y1;
    let months = (m2 || 0) - (m1 || 0);
    let days = (d2 || 0) - (d1 || 0);

    if (days < 0) {
      months -= 1;
      // Jumlah hari pada bulan sebelumnya dari tanggal registrasi
      const prevMonthDate = new Date(y2, (m2 || 1) - 1, 0);
      days += prevMonthDate.getDate();
    }

    if (months < 0) {
      years -= 1;
      months += 12;
    }

    if (years > 0) return { value: years, unit: 'Th' };
    if (months > 0) return { value: months, unit: 'Bl' };
    return { value: Math.max(days, 0), unit: 'Hr' };
  } catch (err) {
    console.error('calcUmurDaftar error:', err);
    return { value: 0, unit: 'Th' };
  }
};

// Helper untuk ambil JSON yang aman (hindari error jika server mengembalikan HTML)
// Sekaligus logging ringkas agar mudah melacak endpoint yang menghasilkan non-JSON
const safeJson = async (response) => {
  try {
    const ct = response.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try {
        return await response.json();
      } catch (e) {
        const url = response.url || '';
        console.error('[safeJson] JSON.parse failed for', url, 'status:', response.status, 'ct:', ct, e);
        // Fallback: coba baca sebagai text agar bisa diinspeksi
        const txt = await response.text();
        return { error: true, message: 'Invalid JSON response', status: response.status, url, contentType: ct, preview: (txt || '').slice(0, 120), raw: txt };
      }
    }
    const text = await response.text();
    const url = response.url || '';
    // Log apabila bukan JSON (seringkali HTML atau HTML-encoded)
    const preview = (text || '').slice(0, 120);
    console.warn('[safeJson] Non-JSON received from', url, 'status:', response.status, 'ct:', ct, '\nPreview:', preview);
    try {
      return JSON.parse(text);
    } catch (_) {
      return { error: true, message: 'Non-JSON response', status: response.status, url, contentType: ct, preview, raw: text };
    }
  } catch (err) {
    // Jika Response sudah digunakan atau error lain, kembalikan objek error yang informatif
    try {
      const url = response && response.url ? response.url : '';
      const status = response && typeof response.status !== 'undefined' ? response.status : 0;
      console.error('[safeJson] Unexpected error handling response', url, err);
      return { error: true, message: 'safeJson unexpected error', url, status };
    } catch (_) {
      return { error: true, message: 'safeJson unexpected error' };
    }
  }
};

// API helpers
const api = {
  poliklinik: async (q = '', limit = 200) => safeJson(await fetch(`/api/poliklinik?q=${encodeURIComponent(q)}&limit=${limit}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  dokterByPoli: async (kdPoli) => safeJson(await fetch(`/ralan/dokter/${enc(kdPoli)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  dokter: async (q = '', limit = 200) => safeJson(await fetch(`/api/dokter?q=${encodeURIComponent(q)}&limit=${limit}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  penjab: async () => safeJson(await fetch('/api/penjab', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  pasienSearch: async (q, limit = 10) => safeJson(await fetch(`/api/pasien?q=${encodeURIComponent(q)}&limit=${limit}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  pasienDetail: async (rm) => safeJson(await fetch(`/api/pasien/detail/${enc(rm)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  pcarePesertaByKartu: async (noKartu) => safeJson(await fetch(`/api/pcare/peserta/noka/${enc(noKartu)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  pcarePesertaByNik: async (nik) => safeJson(await fetch(`/api/pcare/peserta/nik/${enc(nik)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  todayRegs: async (date, kdPoli = '') => safeJson(await fetch(`/api/regperiksa/today?date=${encodeURIComponent(date)}${kdPoli ? `&kd_poli=${encodeURIComponent(kdPoli)}` : ''}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
  generateNoReg: async (kdDokter, tgl, kdPoli = '') => {
    const url = `/regperiksa/generate-noreg/${enc(kdDokter)}/${enc(tgl)}${kdPoli ? `?kd_poli=${encodeURIComponent(kdPoli)}` : ''}`;
    const r = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      cache: 'no-store',
    });
    return safeJson(r);
  },
  generateNoRawat: async (tgl) => {
    const r = await fetch(`/regperiksa/generate-norawat/${enc(tgl)}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      cache: 'no-store',
    });
    return safeJson(r);
  },
  storeReg: (payload) => fetch('/regperiksa/store', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken,
    },
    cache: 'no-store',
    body: JSON.stringify(payload),
  }).then((r) => safeJson(r)),
  deleteReg: (no_rawat) => fetch('/regperiksa/delete', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken,
    },
    cache: 'no-store',
    body: JSON.stringify({ no_rawat }),
  }).then((r) => safeJson(r)),
  // Tambah: kirim antrean ke BPJS (Mobile JKN)
  bpjsAddAntrean: (data) =>
    fetch('/api/antrean/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      cache: 'no-store',
      body: JSON.stringify(data),
    }).then((r) => safeJson(r)),
  // Fallback: buat antrean BPJS dari no_rawat (server akan merakit payload-nya)
  bpjsCreateAntrean: (payload) =>
    fetch('/api/antrean/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      cache: 'no-store',
      body: JSON.stringify(payload),
    }).then((r) => safeJson(r)),
  // Cek Status SRK via endpoint backend (Mobile JKN antrean/add simulasi)
  bpjsSrkStatus: (nomorkartu = '', nik = '') => {
    const params = new URLSearchParams();
    if (String(nomorkartu || '').trim()) params.append('nomorkartu', String(nomorkartu).trim());
    if (String(nik || '').trim()) params.append('nik', String(nik).trim());
    const qs = params.toString();
    return fetch(`/api/bpjs/srk-status${qs ? `?${qs}` : ''}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((r) => safeJson(r));
  },
  // Antri Pendaftaran (tabel antripendaftaran_nomor)
  antriNext: (date) => fetch(`/api/antripendaftaran/next?date=${encodeURIComponent(date)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((r) => safeJson(r)),
  antriStats: (date) => fetch(`/api/antripendaftaran/stats?date=${encodeURIComponent(date)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((r) => safeJson(r)),
  antriCall: (payload) => fetch('/api/antripendaftaran/call', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(payload), cache: 'no-store' }).then((r) => safeJson(r)),
  antriRecall: (payload) => fetch('/api/antripendaftaran/recall', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(payload), cache: 'no-store' }).then((r) => safeJson(r)),
  // Setting Rumah Sakit (untuk header label)
  hospitalInfo: async () => safeJson(await fetch('/api/setting/hospital-info', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })),
};

// UI helpers
const Card = ({ title, children, className = '', headerRight = null }) => (
  <motion.div
    className={`bg-white/95 backdrop-blur-sm shadow-sm hover:shadow-md rounded-2xl border border-slate-200/80 transition-all duration-300 ${className}`}
    initial={{ opacity: 0, y: 8 }}
    animate={{ opacity: 1, y: 0 }}
    whileHover={{ y: -2 }}
    transition={{ type: 'spring', stiffness: 260, damping: 24 }}
    layout
  >
    {title && (
      <div className="px-4 py-2 border-b border-slate-200/80 bg-sky-50 rounded-t-2xl flex items-center justify-between">
        <h5 className="text-slate-800 font-semibold text-[14px]">{title}</h5>
        {headerRight && <div className="ml-2">{headerRight}</div>}
      </div>
    )}
    <div className="p-4">{children}</div>
  </motion.div>
);

const Input = ({ label, ...props }) => (
  <label className="block">
    <span className="text-xs font-medium text-slate-600">{label}</span>
    <input
      {...props}
      className={`mt-1 w-full rounded-xl border border-slate-300/80 bg-white/95 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 shadow-inner transition-all duration-200 ${props.className || ''}`}
    />
  </label>
);

const Select = ({ label, options = [], value, onChange, placeholder = 'Pilih...', disabled = false }) => (
  <label className="block">
    <span className="text-xs font-medium text-slate-600">{label}</span>
    <select
      className={`mt-1 w-full rounded-xl border border-slate-300/80 bg-white/95 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all duration-200 ${disabled ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : ''}`}
      value={String(value ?? '')}
      onChange={(e) => onChange(String(e.target.value ?? '').trim())}
      disabled={disabled}
    >
      <option value="">{placeholder}</option>
      {options.map((opt, idx) => {
        const rawVal = opt.id ?? opt.value ?? opt.kd_dokter ?? opt.kd_poli ?? opt.kd_pj ?? opt.kd ?? opt.code ?? '';
        const val = String(rawVal ?? '');
        const label = opt.text ?? opt.label ?? opt.nm_dokter ?? opt.nm_poli ?? opt.png_jawab ?? (val ? String(val) : 'Tidak diketahui');
        return (
          <option key={val || idx} value={val}>
            {label}
          </option>
        );
      })}
    </select>
  </label>
);

// Filter bar
const FilterBar = ({ date, setDate }) => (
  <Card title="Filter Registrasi">
    <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
      <Input label="Tanggal Registrasi" type="date" value={date} onChange={(e) => setDate(e.target.value)} />
      {/* Poliklinik, Dokter, Cara Bayar dipindah ke Card Pencarian Pasien & Form Registrasi */}
    </div>
    <div className="mt-3 text-xs text-slate-500">Pastikan memilih poli, dokter, dan cara bayar sebelum registrasi.</div>
  </Card>
);

// Patient search + register
const PatientSearchRegister = ({
  date,
  poliklinikOptions = [],
  selectedPoli,
  setSelectedPoli,
  dokterOptions = [],
  selectedDokter,
  setSelectedDokter,
  penjabOptions = [],
  selectedPenjab,
  setSelectedPenjab,
  onRegistered,
  setPatientInfo,
  patientInfo,
  notify,
  refreshBpjs,
  bpjsRefreshKey,
}) => {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedPatient, setSelectedPatient] = useState(null);
  const [noReg, setNoReg] = useState('');
  const [noRawatPreview, setNoRawatPreview] = useState('');
  const [isGeneratingNoReg, setIsGeneratingNoReg] = useState(false);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({ p_jawab: 'PASIEN', almt_pj: '-', hubunganpj: 'DIRI SENDIRI', biaya_reg: 0 });
  const [skriningModal, setSkriningModal] = useState({ open: false, message: '' });
  // Cache detail pasien untuk melengkapi tampilan hasil pencarian (nama, alamat, kelurahan)
  const [resultsDetails, setResultsDetails] = useState({});
  // Cache mapping kelurahan: kd_kel -> nm_kel, dan daftar kelurahan per kecamatan
  const kelLabelCacheRef = useRef(new Map()); // key: kd_kel, value: nm_kel
  const kelListCacheRef = useRef(new Map()); // key: kd_kec, value: Array<{kd_kel,nm_kel}>
  const keluargaOptions = ['AYAH','IBU','ISTRI','SUAMI','SAUDARA','ANAK','DIRI SENDIRI','LAIN-LAIN'].map((v)=>({ value: v, label: v }));

  const openSkriningModal = (message) => {
    setSkriningModal({ open: true, message: message || 'Anda belum melakukan skrining kesehatan. Mohon untuk melakukan skrining kesehatan terlebih dahulu pada menu Skrining Kesehatan.' });
  };
  const closeSkriningModal = () => setSkriningModal({ open: false, message: '' });
  // Util: format tanggal lahir ke DD-MM-YYYY
  const formatDOB_DDMMYYYY = (isoDate) => {
    try {
      if (!isoDate) return '';
      const s = String(isoDate).slice(0, 10);
      const [y, m, d] = s.split('-');
      if (!y || !m || !d) return '';
      return `${d}-${m}-${y}`;
    } catch (e) {
      return '';
    }
  };
  const openSkriningPopup = () => {
    try {
      // Ambil data dari pasien: prioritaskan No Kartu BPJS, fallback ke NIK
      const idNumber = String(
        patientInfo?.no_peserta || patientInfo?.no_ktp || selectedPatient?.no_ktp || ''
      ).trim();
      // Format tanggal lahir ke DD-MM-YYYY
      const dob = formatDOB_DDMMYYYY(patientInfo?.tgl_lahir || '');
  
      // Salin ke clipboard agar petugas tinggal paste di halaman skrining
      const copyPayload = [idNumber, dob].filter(Boolean).join('\n');
      if (copyPayload) {
        if (navigator.clipboard?.writeText) {
          navigator.clipboard
            .writeText(copyPayload)
            .then(() => {
              notify?.({
                type: 'success',
                title: 'Data skrining disalin',
                description: `Nilai yang disalin:\n${copyPayload}\n(Paste ke field NIK/No Kartu BPJS dan Tgl Lahir)`,
              });
            })
            .catch((err) => {
              console.warn('Gagal menyalin ke clipboard:', err);
            });
        }
      }
  
      // Buka halaman skrining BPJS di tab atau jendela baru
      const baseUrl = 'https://webskrining.bpjs-kesehatan.go.id/skrining';
      // Sertakan hash agar bookmarklet dapat membaca dan mengisi otomatis
      const hash = `#id=${encodeURIComponent(idNumber)}&dob=${encodeURIComponent(dob)}`;
      window.open(`${baseUrl}${hash}`, 'skrining', 'width=1200,height=800,noopener,noreferrer');
    } catch (err) {
      console.error('Gagal membuka halaman skrining:', err);
    }
  };

  // Pulihkan fungsi pencarian pasien berdasarkan query
  useEffect(() => {
    let active = true;
    if (query && query.length >= 2) {
      setLoading(true);
      api.pasienSearch(query, 10)
        .then((res) => {
          if (!active) return;
          const rows = Array.isArray(res) ? res : (res?.data || res?.results || []);
          setResults(rows || []);
        })
        .catch((e) => {
          console.error('Gagal mencari pasien:', e);
          if (active) setResults([]);
        })
        .finally(() => active && setLoading(false));
    } else {
      setResults([]);
    }
    return () => {
      active = false;
    };
  }, [query]);

  // Lengkapi hasil pencarian dengan detail pasien (nama lengkap, alamat, kelurahan) bila belum tersedia
  useEffect(() => {
    let cancelled = false;
    const toFetch = [];
    const seen = new Set();
    for (const r of results) {
      const rmRaw = r?.id ?? r?.no_rkm_medis ?? r?.rm ?? '';
      const rm = String(rmRaw || '').trim();
      if (!rm || seen.has(rm)) continue;
      seen.add(rm);
      const hasName = Boolean(r?.nm_pasien || r?.nama || resultsDetails[rm]?.nm_pasien || resultsDetails[rm]?.nama);
      const hasAlamat = Boolean(r?.alamat || r?.alamat_pasien || resultsDetails[rm]?.alamat);
      const hasKelurahan = Boolean(r?.nm_kel || r?.kelurahan || r?.kelurahanpj || resultsDetails[rm]?.kelurahan);
      if (!hasName || !hasAlamat || !hasKelurahan) {
        toFetch.push(rm);
      }
    }
    if (toFetch.length === 0) return;

    // Batasi jumlah dan jalankan SEKUENSIAL dengan jeda kecil untuk menghindari 429 (rate limit)
    const limited = toFetch.slice(0, 3);
    (async () => {
      for (const rm of limited) {
        if (cancelled) break;
        try {
          const res = await api.pasienDetail(rm);
          const d = res?.data || res;
          if (!d || cancelled) continue;
          // Derive nm_kel label from kd_kel/kd_kec with lightweight caching
          const kdKel = d?.kd_kel || '';
          const kdKec = d?.kd_kec || '';
          let kelLabel = d?.kelurahan || d?.nm_kel || '';

          if (!kelLabel && kdKel) {
            const cached = kelLabelCacheRef.current.get(String(kdKel));
            if (cached) {
              kelLabel = cached;
            } else if (kdKec) {
              let list = kelListCacheRef.current.get(String(kdKec));
              if (!list) {
                try {
                  const resp = await fetch(`/kelurahan?kd_kec=${encodeURIComponent(kdKec)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
                    .then((x) => safeJson(x))
                    .catch(() => []);
                  list = Array.isArray(resp) ? resp : [];
                  kelListCacheRef.current.set(String(kdKec), list);
                  for (const k of list) {
                    if (k?.kd_kel) kelLabelCacheRef.current.set(String(k.kd_kel), k?.nm_kel || '');
                  }
                } catch (_) {}
              }
              kelLabel = kelLabelCacheRef.current.get(String(kdKel)) || '';
            }
          }

          setResultsDetails((prev) => ({
            ...prev,
            [rm]: {
              nama: d?.nm_pasien || d?.nama || '',
              nm_pasien: d?.nm_pasien || d?.nama || '',
              no_ktp: d?.no_ktp || '',
              alamat: d?.alamat || '',
              kd_kec: kdKec,
              kd_kel: kdKel,
              kelurahan: kelLabel || '',
            },
          }));
        } catch (err) {
          const is429 = (err?.status === 429) || (err?.response?.status === 429) || (String(err?.message||'').includes('429'));
          const isHtml = String(err?.message||'').includes('<!DOCTYPE');
          if (is429) {
            console.warn(`Rate limited (429) saat memuat detail pasien ${rm}. Akan mencoba lagi secara bertahap.`);
          } else if (isHtml) {
            console.warn(`Respon bukan JSON saat memuat detail pasien ${rm}. Lewati entri ini.`);
          } else {
            console.warn('Gagal memuat detail pasien untuk', rm, err);
          }
        }
        // Jeda antar permintaan untuk menurunkan beban server
        await new Promise((r) => setTimeout(r, 250));
      }
    })();

    return () => { cancelled = true; };
  }, [results]);

  useEffect(() => {
    if (selectedPatient?.id) {
      api.pasienDetail(selectedPatient.id)
        .then((res) => {
          if (res?.data) setPatientInfo(res.data);
        })
        .catch((e) => console.error('Gagal memuat detail pasien:', e));
    }
  }, [selectedPatient, setPatientInfo]);

  // Prefill form dan cara bayar dari informasi pasien (tabel pasien)
  useEffect(() => {
    if (patientInfo) {
      setForm((prev) => ({
        ...prev,
        p_jawab: patientInfo.keluarga ?? prev.p_jawab,
        hubunganpj: patientInfo.keluarga ?? prev.hubunganpj,
        almt_pj: (patientInfo.alamatpj ?? patientInfo.alamat ?? prev.almt_pj),
      }));
      if (patientInfo.kd_pj) {
        setSelectedPenjab((prev) => prev || patientInfo.kd_pj);
      }
    }
  }, [patientInfo]);
  const canGenerate = useMemo(() => !!selectedDokter && !!date, [selectedDokter, date]);
  const canRegister = useMemo(
    () => !!selectedPatient?.id && !!selectedDokter && !!selectedPoli && !!selectedPenjab && !!noReg && !!date,
    [selectedPatient, selectedDokter, selectedPoli, selectedPenjab, noReg, date]
  );

  const handleGenerateNoReg = async () => {
    if (!canGenerate) return;
    setIsGeneratingNoReg(true);
    try {
      const res = await api.generateNoReg(selectedDokter, date, selectedPoli || '');
      if (res?.success) {
        setNoReg(res.no_reg);
        notify?.({ type: 'success', title: 'Nomor registrasi dibuat', description: `No. Registrasi: ${res.no_reg}` });
      } else {
        notify?.({ type: 'error', title: 'Gagal generate No. Registrasi', description: res?.message || 'Terjadi kesalahan saat generate NoReg' });
      }
    } catch (e) {
      console.error(e);
      notify?.({ type: 'error', title: 'Kesalahan jaringan', description: 'Terjadi kesalahan generate NoReg' });
    } finally {
      setIsGeneratingNoReg(false);
    }
  };

  // Setelah simpan, ambil nomor berikutnya dengan retry kecil untuk menghindari efek lag replikasi/caching
  const retryGenerateNoReg = async (lastUsedNoReg = '') => {
    if (!canGenerate) return;
    const lastNum = parseInt(String(lastUsedNoReg || '').replace(/^0+/, ''), 10) || 0;
    const maxAttempts = 5;
    const baseDelay = 250; // ms
    for (let attempt = 1; attempt <= maxAttempts; attempt++) {
      try {
        const res = await api.generateNoReg(selectedDokter, date, selectedPoli || '');
        const nextStr = String(res?.no_reg || '').trim();
        const nextNum = parseInt(nextStr.replace(/^0+/, ''), 10) || 0;
        if (res?.success && nextNum > lastNum) {
          setNoReg(nextStr);
          notify?.({ type: 'success', title: 'Nomor berikutnya siap', description: `No. Registrasi: ${nextStr}` });
          return;
        }
      } catch (err) {
        console.warn('retryGenerateNoReg attempt failed:', err);
      }
      // Jeda dengan incremental backoff
      await new Promise((r) => setTimeout(r, baseDelay * attempt));
    }
    // Jika gagal mendapatkan yang lebih besar, tetap coba ambil satu kali tanpa validasi
    try {
      const res = await api.generateNoReg(selectedDokter, date, selectedPoli || '');
      if (res?.success && res?.no_reg) setNoReg(String(res.no_reg));
    } catch (_) {}
  };

  // Otomatis generate NoReg saat pasien dipilih (dan syarat terpenuhi)
  useEffect(() => {
    if (selectedPatient?.id && canGenerate) {
      handleGenerateNoReg();
    }
  }, [selectedPatient, canGenerate]);

  // Otomatis ambil preview NoRawat berdasarkan tanggal
  useEffect(() => {
    let active = true;
    if (date) {
      api.generateNoRawat(date)
        .then((res) => {
          if (!active) return;
          if (res?.success) {
            setNoRawatPreview(res.no_rawat);
          } else {
            setNoRawatPreview('');
          }
        })
        .catch((e) => {
          console.error('Gagal mengambil preview NoRawat:', e);
          if (active) setNoRawatPreview('');
        });
    }
    return () => {
      active = false;
    };
  }, [date, selectedPatient]);


  const clearForm = () => {
    setQuery('');
    setResults([]);
    setSelectedPatient(null);
    setNoReg('');
    setIsGeneratingNoReg(false);
    setSaving(false);
    setForm({ p_jawab: 'PASIEN', almt_pj: '-', hubunganpj: 'DIRI SENDIRI', biaya_reg: 0 });
    setNoRawatPreview('');
    setPatientInfo(null);
    // Bersihkan filter untuk registrasi berikutnya
    setSelectedPoli('');
    setSelectedDokter('');
    setSelectedPenjab('');
    // juga refresh kartu BPJS (PCare)
    refreshBpjs?.();
  };

  // Tombol reset untuk menyiapkan registrasi berikutnya
  const newBtn = (
    <button
      onClick={() => clearForm()}
      className={`px-4 py-2 rounded-lg text-sm font-medium ${saving || isGeneratingNoReg ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-sky-600 text-white hover:bg-sky-700'}`}
      disabled={saving || isGeneratingNoReg}
    >
      Baru
    </button>
  );

  // Kirim antrean ke BPJS setelah registrasi tersimpan
  const sendBpjsAntrean = async (savedRes) => {
    try {
      if (!savedRes?.bpjs_patient) return; // hanya kirim jika pasien BPJS

      const norm = patientInfo?.no_rkm_medis ?? selectedPatient?.id ?? '';
      const nik = patientInfo?.no_ktp ?? selectedPatient?.no_ktp ?? '';
      const nomorkartu = patientInfo?.no_peserta ?? '';
      const nohp = patientInfo?.no_tlp ?? patientInfo?.no_telp ?? '';

      const angka = parseInt(noReg, 10);

      // Pastikan kode poli dan kode dokter dari tabel mapping_*_pcare
      const kodepoli = String(savedRes?.kodepoli_bpjs || '');
      const kodedokter = Number(savedRes?.kodedokter_bpjs);

      if (!kodepoli || !Number.isFinite(kodedokter) || kodedokter <= 0) {
        notify?.({ type: 'error', title: 'Mapping BPJS belum tersedia', description: 'Kode poli/dokter BPJS tidak ditemukan. Coba fallback otomatis.' });
        try {
          const createRes = await api.bpjsCreateAntrean({ no_rawat: savedRes.no_rawat });
          const meta2 = createRes?.metadata ?? createRes?.metaData;
          if (meta2?.code === 200) {
            const nomor2 = createRes?.response?.nomorantrean;
            notify?.({ type: 'success', title: 'Antrean BPJS (fallback) berhasil', description: nomor2 ? `Nomor antrean: ${nomor2}` : 'Berhasil.' });
          } else {
            notify?.({ type: 'error', title: 'Fallback BPJS gagal', description: meta2?.message || 'Tidak dapat membuat antrean BPJS.' });
          }
        } catch (err2) {
          console.error('Fallback create antrean error:', err2);
        }
        return;
      }

      const dataAdd = {
        nomorkartu: String(nomorkartu || ''),
        nik: String(nik || ''),
        nohp: String(nohp || ''),
        kodepoli,
        namapoli: String(savedRes.namapoli_bpjs || ''),
        norm: String(norm || ''),
        tanggalperiksa: String(date),
        kodedokter,
        namadokter: String(savedRes.namadokter_bpjs || ''),
        jampraktek: String(savedRes.jampraktek || '-'),
        nomorantrean: String(noReg || ''),
        angkaantrean: Number.isFinite(angka) ? angka : Number(String(noReg).replace(/^0+/, '')) || 0,
        keterangan: 'Peserta harap 30 menit lebih awal guna pencatatan administrasi.',
      };

      notify?.({ type: 'info', title: 'Mengirim antrean ke BPJS', description: 'Sedang mengirim data antrean ke BPJS...' });

      const bpjsRes = await api.bpjsAddAntrean(dataAdd);
      const meta = bpjsRes?.metadata ?? bpjsRes?.metaData;
      const code = Number(meta?.code);

      if (code === 200) {
        const nomor = bpjsRes?.response?.nomorantrean || dataAdd.nomorantrean;
        notify?.({ type: 'success', title: 'Antrean BPJS berhasil', description: `Nomor antrean: ${nomor}` });
      } else if (code === 201) {
        const msgRaw = meta?.message || '';
        const msg = msgRaw.toLowerCase();
        const isSkrining = msg.includes('skrining') || msg.includes('srk') || msg.includes('screening');
        if (isSkrining) {
          // Peserta belum melakukan skrining kesehatan
          notify?.({ type: 'warning', title: 'Belum melakukan Skrining Kesehatan', description: msgRaw || 'Peserta belum melakukan SRK (Skrining Kesehatan BPJS).' });
          openSkriningModal(meta?.message);
          // Jangan lanjut fallback karena instruksi harus skrining terlebih dulu
          return;
        }
        // Bila 201 tapi bukan skrining, perlakukan sebagai kegagalan non-SRK dan lanjutkan ke fallback
        const msgTitle = (
          msg.includes('curl error 28') ||
          msg.includes('operation timed out') ||
          msg.includes('timed out') ||
          msg.includes('timeout') ||
          msg.includes('could not resolve host') ||
          msg.includes('failed to connect') ||
          msg.includes('connection')
        ) ? 'Gangguan Koneksi BPJS FKTP' : (
          msg.includes('poli tidak tersedia') ||
          msg.includes('poli tutup') ||
          msg.includes('jadwal tidak tersedia') ||
          msg.includes('jadwal dokter kosong') ||
          msg.includes('ref/poli')
        ) ? 'Pendaftaran Poli Tidak Tersedia' : 'Kuota Penuh';
        notify?.({ type: 'error', title: msgTitle, description: meta?.message || 'Tidak dapat membuat antrean BPJS.' });
        try {
          const createRes = await api.bpjsCreateAntrean({ no_rawat: savedRes.no_rawat });
          const meta2 = createRes?.metadata ?? createRes?.metaData;
          if (meta2?.code === 200) {
            const nomor2 = createRes?.response?.nomorantrean;
            notify?.({ type: 'success', title: 'Antrean BPJS (fallback) berhasil', description: nomor2 ? `Nomor antrean: ${nomor2}` : 'Berhasil.' });
          } else {
            notify?.({ type: 'error', title: 'Fallback BPJS gagal', description: meta2?.message || 'Tidak dapat membuat antrean BPJS.' });
          }
        } catch (err2) {
          console.error('Fallback create antrean error:', err2);
        }
      } else {
        // Jika gagal, coba fallback buat dari no_rawat
        const msg = (meta?.message || '').toLowerCase();
        const msgTitle = (
          msg.includes('curl error 28') ||
          msg.includes('operation timed out') ||
          msg.includes('timed out') ||
          msg.includes('timeout') ||
          msg.includes('could not resolve host') ||
          msg.includes('failed to connect') ||
          msg.includes('connection')
        ) ? 'Gangguan Koneksi BPJS FKTP' : (
          msg.includes('poli tidak tersedia') ||
          msg.includes('poli tutup') ||
          msg.includes('jadwal tidak tersedia') ||
          msg.includes('jadwal dokter kosong') ||
          msg.includes('ref/poli')
        ) ? 'Pendaftaran Poli Tidak Tersedia' : 'BPJS Antrean gagal';
        const description = meta?.message || 'Gagal tambah antrean BPJS';
        notify?.({ type: 'error', title: msgTitle, description });
        try {
          const createRes = await api.bpjsCreateAntrean({ no_rawat: savedRes.no_rawat });
          const meta2 = createRes?.metadata ?? createRes?.metaData;
          if (meta2?.code === 200) {
            const nomor2 = createRes?.response?.nomorantrean;
            notify?.({ type: 'success', title: 'Antrean BPJS (fallback) berhasil', description: nomor2 ? `Nomor antrean: ${nomor2}` : 'Berhasil.' });
          } else {
            notify?.({ type: 'error', title: 'Fallback BPJS gagal', description: meta2?.message || 'Tidak dapat membuat antrean BPJS.' });
          }
        } catch (err2) {
          console.error('Fallback create antrean error:', err2);
        }
      }
    } catch (err) {
      console.error('Error kirim antrean BPJS:', err);
      notify?.({ type: 'error', title: 'Kesalahan jaringan BPJS', description: err?.message || 'Gagal menghubungi layanan BPJS.' });
    }
  };

  // Simpan pendaftaran (dipanggil setelah konfirmasi SRK jika diperlukan)
  const handleSubmitProceed = async () => {
    setSaving(true);
    try {
      const umurInfo = calcUmurDaftar(patientInfo?.tgl_lahir, date);
      const payload = {
        no_reg: noReg,
        kd_dokter: selectedDokter,
        no_rkm_medis: selectedPatient.id,
        kd_poli: selectedPoli,
        kd_pj: selectedPenjab,
        tgl_registrasi: date,
        p_jawab: form.p_jawab,
        almt_pj: form.almt_pj,
        hubunganpj: form.hubunganpj,
        biaya_reg: Number(form.biaya_reg) || 0,
        stts: 'Belum',
        stts_daftar: 'Lama',
        status_lanjut: 'Ralan',
        umurdaftar: umurInfo.value,
        sttsumur: umurInfo.unit,
      };
      const res = await api.storeReg(payload);
      if (res?.success) {
        onRegistered?.(res);
        notify?.({ type: 'success', title: 'Registrasi berhasil', description: `No. Rawat: ${res.no_rawat || '-'}` });
        setNoRawatPreview(res.no_rawat || '');

        // Kirim antrean ke BPJS jika pasien BPJS
        await sendBpjsAntrean(res);

        // Bersihkan form setelah semua proses selesai
        setNoReg('');
        setQuery('');
        setResults([]);
        setSelectedPatient(null);

        // Prefetch nomor registrasi berikutnya agar siap dipakai tanpa hard refresh
        retryGenerateNoReg(payload.no_reg);
      } else {
        notify?.({ type: 'error', title: 'Gagal menyimpan registrasi', description: res?.message || 'Coba lagi beberapa saat.' });
      }
    } catch (e) {
      console.error(e);
      notify?.({ type: 'error', title: 'Kesalahan jaringan', description: 'Terjadi kesalahan saat menyimpan' });
    } finally {
      setSaving(false);
    }
  };

  const handleSubmit = async () => {
    if (!canRegister) {
      notify?.({ type: 'info', title: 'Lengkapi data', description: 'Pilih pasien, poli, dokter, dan cara bayar. NoReg akan dibuat otomatis saat pasien dipilih.' });
      return;
    }
    // Pre-check SRK untuk pasien BPJS (tanpa bergantung pada state di komponen lain)
    const isBPJ = (() => {
      const kd = String(selectedPenjab || patientInfo?.kd_pj || '').trim().toUpperCase();
      const label = String(patientInfo?.png_jawab || '').trim().toLowerCase();
      if (kd === 'BPJ') return true;
      if (label.includes('bpjs')) return true;
      return false;
    })();
    if (isBPJ) {
      try {
        const noka = String(patientInfo?.no_peserta || '');
        const nik = String(patientInfo?.no_ktp || '');
        if (noka || nik) {
          const resSrk = await api.bpjsSrkStatus(noka, nik);
          const status = resSrk?.srk_status;
          const meta = resSrk?.metadata || resSrk?.metaData;
          if (status === 'Belum SRK') {
            openSkriningModal(meta?.message);
            return; // tahan simpan, minta pengguna OK/SRK dulu
          }
        }
      } catch (err) {
        console.warn('SRK precheck error:', err);
        // Jangan blok pendaftaran jika cek SRK gagal; lanjut simpan
      }
    }
    await handleSubmitProceed();
  };

  return (
    <>
      <Card title="Pencarian Pasien & Form Registrasi">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <div>
            <div className="flex items-end gap-2">
              <div className="flex-1">
                <Input
                  label="Cari pasien (Nama / RM / NIK / No BPJS)"
                  placeholder="contoh: Siti / 12345 / 3301xxxx"
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                />
              </div>
              <button
                type="button"
                onClick={() => window.open('/data-pasien/create', '_blank', 'noopener,noreferrer')}
                className="px-3 py-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-800 text-xs whitespace-nowrap"
                aria-label="Buka halaman pasien baru"
                title="Pasien Baru"
              >
                Pasien Baru
              </button>
            </div>
            <div className="mt-3">
              {loading ? (
                <div className="flex items-center gap-2 text-sm text-slate-500">
                  <span className="inline-block h-4 w-4 border-2 border-slate-300 border-t-sky-500 rounded-full animate-spin"></span>
                  Mencari pasien...
                </div>
              ) : results.length > 0 ? (
                <ul className="divide-y divide-slate-200 rounded-lg border border-slate-200">
                  {results.map((r, idx) => {
                    const rmRaw = r.id ?? r.no_rkm_medis ?? r.rm ?? '';
                    const rm = String(rmRaw).trim();
                    const detail = resultsDetails[rm] || {};
                    const name = (r.nm_pasien ?? r.nama ?? detail.nm_pasien ?? detail.nama ?? '').toString().trim();
                    const labelRaw = r.text ?? r.label ?? (rm || name ? `${rm} - ${name}` : 'Tidak diketahui');
                    const label = String(labelRaw).trim();
                    const nik = (r.no_ktp ?? r.nik ?? r.noKTP ?? detail.no_ktp ?? '').toString().trim();
                    const alamat = (r.alamat ?? r.alamat_pasien ?? detail.alamat ?? '').toString().trim();
                    const kelurahanRaw = r.nm_kel ?? r.kelurahan ?? r.kelurahanpj ?? detail.kelurahan ?? '';
                    const kelurahan = String(kelurahanRaw).trim();

                    // Format baris pertama: NIK - Nama (bold)
                    const firstLine = `${nik || '-'} - ${name || '-'}`;

                    // Format baris kedua: No RM{no_rkm_medis} - alamat {alamat} - kelurahan {nm_kel}
                    const secondLine = `No RM: ${rm || '-'} - Alamat: ${alamat || '-'} - Kelurahan: ${kelurahan || '-'}`;

                    return (
                      <motion.li
                        key={rm || label || idx}
                        className="p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-b-0"
                        initial={{ opacity: 0, y: 4 }}
                        animate={{ opacity: 1, y: 0 }}
                        whileHover={{ y: -1 }}
                        whileTap={{ scale: 0.99 }}
                        transition={{ type: 'spring', stiffness: 300, damping: 26 }}
                        onClick={() => {
                          setSelectedPatient({ id: rm, text: label, no_ktp: nik });
                          setQuery(label);
                          setResults([]);
                          // Prefill dari tabel pasien: penanggung jawab, alamat PJ, hubungan PJ, dan kd_pj jika tersedia
                          setForm((prev) => ({
                            ...prev,
                            p_jawab: r.keluarga ?? prev.p_jawab,
                            hubunganpj: r.keluarga ?? prev.hubunganpj,
                            almt_pj: r.alamatpj ?? prev.almt_pj,
                          }));
                          if (r.kd_pj) setSelectedPenjab(r.kd_pj);
                        }}
                      >
                        <div className="font-bold text-slate-800 text-sm mb-1">{firstLine}</div>
                        <div className="text-xs text-slate-600">{secondLine}</div>
                      </motion.li>
                    );
                  })}
                </ul>
              ) : null}
            </div>
            {/* Card Informasi Pasien akan diposisikan pada baris khusus di bawah agar sejajar dengan Form Registrasi */}
          </div>
          <div>
            {/* Filter kolom tengah: Poliklinik dan Cara Bayar sejajar, Dokter di bawah */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3 items-start">
              <Select label="Poliklinik" options={poliklinikOptions} value={selectedPoli} onChange={setSelectedPoli} />
              <Select label="Cara Bayar" options={penjabOptions} value={selectedPenjab} onChange={setSelectedPenjab} />
              <div className="md:col-span-2">
                <Select label="Dokter" options={dokterOptions} value={selectedDokter} onChange={setSelectedDokter} />
              </div>
            </div>
          </div>
          <div>
            <QueueRegisterCard date={date} notify={notify} />
          </div>
        </div>
        {/* Baris khusus untuk menyelaraskan tiga card: Informasi Pasien, Informasi Status BPJS (PCare), dan Form Registrasi */}
        <div className="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
          <div className="h-full space-y-4">
            <PatientInfoCard patientInfo={patientInfo} notify={notify} setPatientInfo={setPatientInfo} selectedPenjab={selectedPenjab} />
            {/* CKG dipindah ke dalam card, di bawah Informasi Pasien */}
            <CKGStatusCard patientInfo={patientInfo} />
          </div>
          <div className="h-full">
            <BPJSStatusCard patientInfo={patientInfo} refreshKey={bpjsRefreshKey} />
          </div>
          <div className="h-full">
            <Card title={`Form Registrasi — ${date ? new Date(date).toLocaleDateString('id-ID') : ''}`} className="h-full">
              <div className="grid grid-cols-1 md:grid-cols-12 gap-3">
                <div className="md:col-span-3">
                  <Input label="No. RM" value={selectedPatient?.id || patientInfo?.no_rkm_medis || ''} readOnly />
                </div>
                <div className="md:col-span-3">
                  <Input label="No. Registrasi" value={noReg} readOnly />
                </div>
                <div className="md:col-span-6">
                  <Input label="No. Rawat (preview)" value={noRawatPreview} readOnly />
                </div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <Input label="Penanggung Jawab" value={form.p_jawab || patientInfo?.keluarga || ''} onChange={(e) => setForm({ ...form, p_jawab: e.target.value })} />
                <Input label="Alamat PJ" value={form.almt_pj} onChange={(e) => setForm({ ...form, almt_pj: e.target.value })} />
                <Select label="Hubungan PJ" options={keluargaOptions} value={form.hubunganpj || patientInfo?.keluarga || ''} onChange={(val) => setForm({ ...form, hubunganpj: val })} />
              </div>
              <div className="mt-3 flex items-center justify-between">
                {newBtn}
                <button
                  className={`px-4 py-2 rounded-lg text-sm font-semibold ${saving || !canRegister ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-emerald-600 text-white hover:bg-emerald-700'}`}
                  onClick={handleSubmit}
                  disabled={saving || !canRegister}
                >
                  {saving ? 'Menyimpan...' : 'Simpan Pendaftaran'}
                </button>
              </div>
            </Card>
          </div>
        </div>
      </Card>

      {/* Modal Skrining Kesehatan BPJS */}
      {skriningModal.open && (
        <div className="fixed inset-0 z-[1000] bg-black/40 backdrop-blur-sm flex items-center justify-center">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md p-4">
            <div className="text-base font-semibold text-slate-800">Skrining Kesehatan BPJS</div>
            <div className="mt-2 text-sm text-slate-700">{skriningModal.message || 'Anda belum melakukan skrining kesehatan. Mohon untuk melakukan skrining kesehatan terlebih dahulu pada menu Skrining Kesehatan.'}</div>
            <div className="mt-4 flex justify-end gap-2">
              <button
                className="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50"
                onClick={() => { closeSkriningModal(); handleSubmitProceed(); }}
              >
                OK
              </button>
              <button
                className="px-4 py-2 rounded-lg bg-sky-600 text-white hover:bg-sky-700"
                onClick={() => { closeSkriningModal(); openSkriningPopup(); }}
              >
                SRK
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

// BPJS status via PCare
const BPJSStatusCard = ({ patientInfo, refreshKey }) => {
  const [mode, setMode] = useState('nik');
  const [value, setValue] = useState('');
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState(null);
  const [source, setSource] = useState(''); // sumber cek: noka/nik
  // SRK state
  const [srkLoading, setSrkLoading] = useState(false);
  const [srkStatus, setSrkStatus] = useState(null);
  const [srkMeta, setSrkMeta] = useState(null);
  const [srkUrl, setSrkUrl] = useState('');

  // reset state saat tombol Baru ditekan
  useEffect(() => {
    setMode('nik');
    setValue('');
    setLoading(false);
    setData(null);
    setSource('');
    setSrkLoading(false);
    setSrkStatus(null);
    setSrkMeta(null);
    setSrkUrl('');
  }, [refreshKey]);

  useEffect(() => {
    if (patientInfo?.no_ktp) {
      setMode('nik');
      setValue(String(patientInfo.no_ktp).trim());
    } else if (patientInfo?.no_peserta) {
      setMode('noka');
      setValue(String(patientInfo.no_peserta).trim());
    }
  }, [patientInfo]);
  const fetchData = async () => {
    if (!value) return;
    // Validasi sebelum call
    if (mode === 'nik' && !isValidNIK(value)) {
      alert('NIK harus 16 digit');
      return;
    }
    if (mode === 'noka' && !isValidNoKartu(value)) {
      alert('No. Kartu BPJS harus 13 digit');
      return;
    }

    setLoading(true);
    try {
      let res;
      let used = mode;
      if (mode === 'noka') {
        res = await api.pcarePesertaByKartu(value);
        const pesertaCheck = res?.response?.peserta || res?.response || res?.peserta;
        if (!pesertaCheck && patientInfo?.no_ktp) {
          const nik = String(patientInfo.no_ktp).trim();
          if (isValidNIK(nik)) {
            res = await api.pcarePesertaByNik(nik);
            used = 'nik';
            setMode('nik');
            setValue(nik);
          }
        }
      } else {
        res = await api.pcarePesertaByNik(value);
        const pesertaCheck = res?.response?.peserta || res?.response || res?.peserta;
        if (!pesertaCheck && patientInfo?.no_peserta) {
          const noka = String(patientInfo.no_peserta).trim();
          if (isValidNoKartu(noka)) {
            res = await api.pcarePesertaByKartu(noka);
            used = 'noka';
            setMode('noka');
            setValue(noka);
          }
        }
      }
      setData(res);
      setSource(used);
    } catch (e) {
      console.error(e);
      alert('Gagal mengambil data BPJS dari PCare');
    } finally {
      setLoading(false);
    }
  };

  // Otomatis cek status saat pasien dipilih atau nilai kartu/NIK berubah
  useEffect(() => {
    const valid = (mode === 'noka' && isValidNoKartu(value)) || (mode === 'nik' && isValidNIK(value));
    if (valid) {
      fetchData();
    }
  }, [value, mode]);

  // Cek SRK otomatis setelah patientInfo tersedia (menggunakan NIK/NoKartu pasien)
  useEffect(() => {
    const nik = String(patientInfo?.no_ktp || '').trim();
    const noka = String(patientInfo?.no_peserta || '').trim();
    if (!nik && !noka) return;
    // validasi ringan untuk menghindari request tidak perlu
    const canNik = nik && isValidNIK(nik);
    const canNoka = noka && isValidNoKartu(noka);
    if (!canNik && !canNoka) return;
    setSrkLoading(true);
    api.bpjsSrkStatus(canNoka ? noka : '', canNik ? nik : '')
      .then((res) => {
        setSrkStatus(res?.srk_status || null);
        setSrkMeta(res?.metadata || null);
        setSrkUrl(res?.url || '');
      })
      .catch((e) => {
        console.warn('SRK status error:', e);
        setSrkStatus(null);
        setSrkMeta(null);
        setSrkUrl('');
      })
      .finally(() => setSrkLoading(false));
  }, [patientInfo?.no_ktp, patientInfo?.no_peserta]);

  // Ambil objek peserta dengan kompatibilitas berbagai bentuk respons
  const peserta = data?.response?.peserta || data?.response || data?.peserta;
  const meta = data?.metaData;
  // Normalisasi nilai boolean BPJS (Y/N, 1/0, true/false) menjadi "Ya/Tidak"
  const yesNo = (val) => {
    if (val === null || typeof val === 'undefined') return null;
    if (typeof val === 'boolean') return val ? 'Ya' : 'Tidak';
    const sRaw = String(val);
    const s = sRaw.trim().toLowerCase();
    // nilai kosong dari BPJS dianggap Tidak
    if (!s) return 'Tidak';
    const positive = ['y', 'ya', 'yes', 'true', '1', 't', 'ht', 'dm', 'hipertensi', 'diabetes', 'prolanis', 'prb'];
    const negative = ['n', 'no', 'false', '0', 'f', 'tidak', 'none', 'null', '-'];
    if (positive.includes(s)) return 'Ya';
    if (negative.includes(s)) return 'Tidak';
    // string non-kosong lain (misal kode program) dari BPJS -> anggap mengikuti program => Ya
    return 'Ya';
  };
  const prol = yesNo(peserta?.pstProl);
  const prb = yesNo(peserta?.pstPrb);

  return (
    <Card title="Informasi Status BPJS (PCare)">
      <div className="flex items-end gap-2">
        <Select label="Mode" options={[{ id: 'nik', text: 'NIK' }, { id: 'noka', text: 'No. Kartu' }]} value={mode} onChange={setMode} />
        <Input label={mode === 'noka' ? 'No. Kartu BPJS' : 'NIK'} value={value} onChange={(e) => setValue(e.target.value)} />
        {/* Tombol cek status dan cek via NIK pasien dihapus sesuai permintaan */}
      </div>
      <div className="mt-3">
        {meta && typeof meta.code !== 'undefined' && (
          <div className="mb-2 text-xs text-slate-500">BPJS Response: code {meta.code} • {meta.message || '-'}</div>
        )}
        {loading ? (
          <div className="flex items-center gap-2 text-sm text-slate-500">
            <span className="inline-block h-4 w-4 border-2 border-slate-300 border-t-sky-500 rounded-full animate-spin"></span>
            Mengambil data BPJS...
          </div>
        ) : peserta ? (
          <div className="text-sm grid grid-cols-2 gap-2">
            <div className="text-slate-600">Nama:</div>
            <div className="font-medium text-slate-800">{peserta?.nama || '-'}</div>
            <div className="text-slate-600">NIK:</div>
            <div className="font-medium text-slate-800">{peserta?.nik || peserta?.noKTP || '-'}</div>
            <div className="text-slate-600">No. Kartu:</div>
            <div className="font-medium text-slate-800">{peserta?.noKartu || '-'}</div>
            <div className="text-slate-600">Status:</div>
            <div className="font-medium text-slate-800">{peserta?.status?.peserta || peserta?.ketAktif || '-'}</div>
            <div className="text-slate-600">Faskes:</div>
            <div className="font-medium text-slate-800">{peserta?.provUmum?.nmProvider || peserta?.kdProviderPst?.nmProvider || '-'}</div>
            <div className="text-slate-600">Kelas:</div>
            <div className="font-medium text-slate-800">{peserta?.kelasTkp?.nama || peserta?.jnsKelas?.nama || '-'}</div>
            {/* Tambahan informasi diminta: jnsPeserta.nama, noHP, pstProl, pstPrb */}
            <div className="text-slate-600">Jenis Peserta:</div>
            <div className="font-medium text-slate-800">{peserta?.jnsPeserta?.nama || peserta?.jenisPeserta?.nama || peserta?.jnsPeserta?.nmJnsPeserta || '-'}</div>
            <div className="text-slate-600">No. HP:</div>
            <div className="font-medium text-slate-800">{peserta?.noHP || peserta?.noHp || patientInfo?.no_tlp || '-'}</div>
            <div className="text-slate-600">Prolanis:</div>
            <div className="font-medium text-slate-800">{prol ?? '-'}</div>
            <div className="text-slate-600">PRB:</div>
            <div className="font-medium text-slate-800">{prb ?? '-'}</div>
          </div>
        ) : (
          <div className="text-xs text-slate-500">{meta && meta.code && meta.code !== 200 ? `BPJS/PCare: ${meta.message || 'Permintaan tidak valid'} (code ${meta.code})` : 'Masukkan NIK atau No. Kartu untuk cek status BPJS.'}</div>
        )}

        {/* SRK Status Section */}
        <div className="mt-3 pt-3 border-t border-slate-200">
          <div className="text-sm font-semibold text-slate-800 mb-1">Skrining Kesehatan (Mobile JKN)</div>
          {srkLoading ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <span className="inline-block h-4 w-4 border-2 border-slate-300 border-t-sky-500 rounded-full animate-spin"></span>
              Mengecek status skrining...
            </div>
          ) : srkStatus ? (
            <div className="text-sm">
              <span className={`px-2 py-1 rounded ${srkStatus === 'Belum SRK' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'}`}>{srkStatus}</span>
              {srkMeta && (
                <div className="mt-1 text-xs text-slate-500">Mobile JKN Response: code {srkMeta.code ?? '-'} • {srkMeta.message ?? '-'}</div>
              )}
              
              {/* URL Mobile JKN disembunyikan sesuai permintaan */}
              
            </div>
          ) : (
            <div className="text-xs text-slate-500">Belum dapat menentukan status SRK. Pastikan NIK atau No. Kartu pasien valid.</div>
          )}
        </div>
      </div>
    </Card>
  );
};

// CKG status card
const CKGStatusCard = ({ patientInfo }) => {
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState(null);
  const year = new Date().getFullYear();

  useEffect(() => {
    const rm = patientInfo?.no_rkm_medis;
    if (!rm) { setData(null); return; }
    setLoading(true);
    fetch(`/api/ckg/status/${enc(rm)}?year=${year}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
      .then((r) => safeJson(r))
      .then((res) => setData(res))
      .catch((e) => { console.error('CKG status error:', e); setData({ success: false, message: 'Gagal memuat status CKG' }); })
      .finally(() => setLoading(false));
  }, [patientInfo?.no_rkm_medis]);

  const latest = data?.latest;
  const has = data?.has_skrining;
  const count = data?.count || 0;
  const displayYear = data?.year || year;
  const statusPhrase = has ? `Sudah CKG tahun ${displayYear}` : `Belum CKG tahun ${displayYear}`;
  const formatDate = (s) => {
    if (!s) return '-';
    try { return new Date(s).toLocaleDateString('id-ID'); } catch (e) { return String(s); }
  };

  return (
    <Card title={`Informasi Status CKG (${year})`}>
      {!patientInfo ? (
        <div className="text-xs text-slate-500">Belum ada pasien dipilih.</div>
      ) : loading ? (
        <div className="flex items-center gap-2 text-sm text-slate-500">
          <span className="inline-block h-4 w-4 border-2 border-slate-300 border-t-sky-500 rounded-full animate-spin"></span>
          Memuat status CKG...
        </div>
      ) : data?.success ? (
        <>
          <div className="text-sm grid grid-cols-2 gap-2">
            <div className="text-slate-600">Status:</div>
            <div className={`font-medium ${has ? 'text-emerald-700' : 'text-rose-700'}`}>{statusPhrase}</div>
          </div>
          {has && (
            <div className="text-sm grid grid-cols-2 gap-2">
              <div className="text-slate-600">Tanggal terakhir:</div>
              <div className="font-medium text-slate-800">{formatDate(latest?.tanggal_skrining)}</div>
              <div className="text-slate-600">Jumlah entri tahun ini:</div>
              <div className="font-medium text-slate-800">{count}</div>
              <div className="text-slate-600">Kunjungan Sehat:</div>
              <div className="font-medium text-slate-800">{latest?.kunjungan_sehat === '1' ? 'Ya' : 'Tidak'}</div>
            </div>
          )}
        </>
      ) : (
        <div className="text-xs text-rose-600">{data?.message || 'Gagal memuat status CKG.'}</div>
      )}
    </Card>
  );
};

// Patient info card
const PatientInfoCard = ({ patientInfo, notify, setPatientInfo, selectedPenjab }) => {
  const [collapsed, setCollapsed] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [loadingEdit, setLoadingEdit] = useState(false);
  const [savingEdit, setSavingEdit] = useState(false);
  const [penjabOptions, setPenjabOptions] = useState([]);
  const [posyanduOptions, setPosyanduOptions] = useState([]);
  const [posyanduAll, setPosyanduAll] = useState([]);
  const [propinsiOptions, setPropinsiOptions] = useState([]);
  const [kabupatenOptions, setKabupatenOptions] = useState([]);
  const [kecamatanOptions, setKecamatanOptions] = useState([]);
  const [kelurahanOptions, setKelurahanOptions] = useState([]);
  const [copyAlamatPJ, setCopyAlamatPJ] = useState(true);
  const [form, setForm] = useState({
    nm_pasien: '',
    no_ktp: '',
    no_kk: '',
    tgl_lahir: '',
    jk: '',
    tmp_lahir: '',
    nm_ibu: '',
    gol_darah: '',
    agama: '',
    pnd: '',
    keluarga: '',
    namakeluarga: '',
    status: '',
    stts_nikah: '',
    pekerjaan: '',
    kd_pj: '',
    no_peserta: '',
    no_tlp: '',
    alamat: '',
    kd_prop: '',
    kd_kab: '',
    kd_kec: '',
    kd_kel: '',
    alamatpj: '',
    kelurahanpj: '',
    kecamatanpj: '',
    kabupatenpj: '',
    data_posyandu: '',
  });
  const toggleBtn = (
    <button
      onClick={() => setCollapsed((v) => !v)}
      className="px-2 py-1 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600"
    >
      {collapsed ? 'Buka' : 'Tutup'}
    </button>
  );

  const statusOptions = [
    'Kepala Keluarga','Suami','Istri','Anak','Menantu','Orang tua','Mertua','Pembantu','Famili Lain','Lainnya'
  ].map((v) => ({ value: v, label: v }));
  const nikahOptions = [
    'MENIKAH','BELUM MENIKAH','JANDA','DUDHA','JOMBLO'
  ].map((v) => ({ value: v, label: v }));
  const pekerjaanOptions = [
    'Belum/Tidak Bekerja','Pelajar','Mahasiswa','Ibu Rumah Tangga','TNI','POLRI','ASN (Kantor Pemerintah)','Pegawai Swasta','Wiraswasta/Pekerja Mandiri','Pensiunan','Pejabat Negara / Pejabat Daerah','Pengusaha','Dokter','Bidan','Perawat','Apoteker','Psikolog','Tenaga Kesehatan Lainnya','Dosen','Guru','Peneliti','Pengacara','Notaris','Hakim/Jaksa/Tenaga Peradilan Lainnya','Akuntan','Insinyur','Arsitek','Konsultan','Wartawan','Pedagang','Petani / Pekebun','PETANI/PEKEBUN','Nelayan / Perikanan','Peternak','Tokoh Agama','Juru Masak','Pelaut','Sopir','Pilot','Masinis','Atlet','Pekerja Seni','Penjahit / Perancang Busana','Karyawan kantor / Pegawai Administratif','Teknisi / Mekanik','Pekerja Pabrik / Buruh','Pekerja Konstruksi','Pekerja Pertukangan','Pekerja Migran','Lainnya'
  ].map((v) => ({ value: v, label: v }));
  const jkOptions = ['L','P'].map((v)=>({ value: v, label: v==='L' ? 'Laki-laki' : 'Perempuan' }));
  const golDarahOptions = ['A','B','AB','O','Tidak diketahui'].map((v)=>({ value: v, label: v }));
  const agamaOptions = ['Islam','Kristen','Katholik','Hindu','Buddha','Konghucu','Lainnya'].map((v)=>({ value: v, label: v }));
  const pndOptions = ['Tidak sekolah','SD','SMP','SMA','D1','D2','D3','S1','S2','S3'].map((v)=>({ value: v, label: v }));
  const keluargaOptions = ['AYAH','IBU','ISTRI','SUAMI','SAUDARA','ANAK','DIRI SENDIRI','LAIN-LAIN'].map((v)=>({ value: v, label: v }));

  const openEditModal = async () => {
    if (!patientInfo) return;
    setEditOpen(true);
    setLoadingEdit(true);
    try {
      const r = await fetch(`/pasien/${enc(patientInfo.no_rkm_medis)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store',
      });
      const d = await safeJson(r);
      setForm({
        nm_pasien: d?.nm_pasien || '',
        no_ktp: d?.no_ktp || '',
        no_kk: d?.no_kk || '',
        tgl_lahir: String(d?.tgl_lahir || '').split(' ')[0],
        jk: d?.jk || '',
        tmp_lahir: d?.tmp_lahir || '',
        nm_ibu: d?.nm_ibu || '',
        gol_darah: d?.gol_darah || '',
        agama: d?.agama || '',
        pnd: d?.pnd || '',
        keluarga: d?.keluarga || '',
        namakeluarga: d?.namakeluarga || '',
        status: d?.status || '',
        stts_nikah: d?.stts_nikah || '',
        pekerjaan: d?.pekerjaan || '',
        kd_pj: d?.kd_pj || '',
        no_peserta: d?.no_peserta || '',
        no_tlp: d?.no_tlp || '',
        alamat: d?.alamat || '',
        kd_prop: d?.kd_prop || '',
        kd_kab: d?.kd_kab || '',
        kd_kec: d?.kd_kec || '',
        kd_kel: d?.kd_kel || '',
        alamatpj: d?.alamatpj || '',
        kelurahanpj: d?.kelurahanpj || '',
        kecamatanpj: d?.kecamatanpj || '',
        kabupatenpj: d?.kabupatenpj || '',
        data_posyandu: d?.data_posyandu || '',
      });
      const pj = await fetch('/api/penjab', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x));
      setPenjabOptions(Array.isArray(pj) ? pj : (Array.isArray(pj?.data) ? pj.data : []));
      const pos = await fetch('/api/data-posyandu', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x));
      setPosyanduAll(Array.isArray(pos) ? pos : (Array.isArray(pos?.data) ? pos.data : []));

      // preload wilayah options based on existing codes
      const prop = await fetch('/propinsi', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x)).catch(() => []);
      const propArr = Array.isArray(prop) ? prop : (Array.isArray(prop?.data) ? prop.data : []);
      setPropinsiOptions(propArr.map((p) => ({ value: p.kd_prop, label: p.nm_prop })));
      if (d?.kd_prop) {
        const kab = await fetch(`/kabupaten?kd_prop=${encodeURIComponent(d.kd_prop)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x)).catch(() => []);
        const kabArr = Array.isArray(kab) ? kab : (Array.isArray(kab?.data) ? kab.data : []);
        setKabupatenOptions(kabArr.map((k) => ({ value: k.kd_kab, label: k.nm_kab })));
      } else {
        setKabupatenOptions([]);
      }
      if (d?.kd_kab) {
        const kec = await fetch(`/kecamatan?kd_kab=${encodeURIComponent(d.kd_kab)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x)).catch(() => []);
        const kecArr = Array.isArray(kec) ? kec : (Array.isArray(kec?.data) ? kec.data : []);
        setKecamatanOptions(kecArr.map((k) => ({ value: k.kd_kec, label: k.nm_kec })));
      } else {
        setKecamatanOptions([]);
      }
      if (d?.kd_kec) {
        const kel = await fetch(`/kelurahan?kd_kec=${encodeURIComponent(d.kd_kec)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x)).catch(() => []);
        const kelArr = Array.isArray(kel) ? kel : (Array.isArray(kel?.data) ? kel.data : []);
        setKelurahanOptions(kelArr.map((k) => ({ value: k.kd_kel, label: k.nm_kel })));
      } else {
        setKelurahanOptions([]);
      }

      // filter posyandu by kelurahan name when available
      const nmKelLabel = Array.isArray(kelurahanOptions) ? kelurahanOptions.find((o) => String(o.value) === String(d?.kd_kel))?.label : undefined;
      const posOpts = (Array.isArray(pos) ? pos : [])
        .filter((p) => {
          const desa = String(p?.desa ?? '').trim();
          const nmKel = String(nmKelLabel ?? '').trim();
          return !nmKel || (desa && desa.toLowerCase() === nmKel.toLowerCase());
        })
        .map((p) => ({ value: p.nama_posyandu, label: p.nama_posyandu }));
      setPosyanduOptions(posOpts);
    } catch (e) {
      console.error('openEditModal error', e);
      notify && notify({ type: 'error', title: 'Gagal memuat data pasien', description: e?.message || 'Terjadi kesalahan saat memuat data.' });
      setEditOpen(false);
    } finally {
      setLoadingEdit(false);
    }
  };

  // Dependent wilayah loaders when user changes selection
  useEffect(() => {
    if (!editOpen) return;
    // Load kabupaten when propinsi changes
    if (!form.kd_prop) {
      setKabupatenOptions([]);
      setKecamatanOptions([]);
      setKelurahanOptions([]);
      return;
    }
    fetch(`/kabupaten?kd_prop=${encodeURIComponent(form.kd_prop)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
      .then((r) => safeJson(r))
      .then((kab) => setKabupatenOptions((Array.isArray(kab) ? kab : (Array.isArray(kab?.data) ? kab.data : [])).map((k) => ({ value: k.kd_kab, label: k.nm_kab }))))
      .catch(() => setKabupatenOptions([]));
  }, [form.kd_prop, editOpen]);

  useEffect(() => {
    if (!editOpen) return;
    // Load kecamatan when kabupaten changes
    if (!form.kd_kab) { setKecamatanOptions([]); setKelurahanOptions([]); return; }
    fetch(`/kecamatan?kd_kab=${encodeURIComponent(form.kd_kab)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
      .then((r) => safeJson(r))
      .then((kec) => setKecamatanOptions((Array.isArray(kec) ? kec : (Array.isArray(kec?.data) ? kec.data : [])).map((k) => ({ value: k.kd_kec, label: k.nm_kec }))))
      .catch(() => setKecamatanOptions([]));
  }, [form.kd_kab, editOpen]);

  useEffect(() => {
    if (!editOpen) return;
    // Load kelurahan when kecamatan changes
    if (!form.kd_kec) { setKelurahanOptions([]); return; }
    fetch(`/kelurahan?kd_kec=${encodeURIComponent(form.kd_kec)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
      .then((r) => safeJson(r))
      .then((kel) => setKelurahanOptions(Array.isArray(kel) ? kel.map((k) => ({ value: k.kd_kel, label: k.nm_kel })) : []))
      .catch(() => setKelurahanOptions([]));
  }, [form.kd_kec, editOpen]);

  // Filter posyandu by selected kelurahan (desa)
  useEffect(() => {
    const nmKelLabel = kelurahanOptions.find((o) => String(o.value) === String(form.kd_kel))?.label;
    const opts = (posyanduAll || [])
      .filter((p) => {
        const desa = String(p?.desa ?? '').trim();
        const nmKel = String(nmKelLabel ?? '').trim();
        return !nmKel || (desa && desa.toLowerCase() === nmKel.toLowerCase());
      })
      .map((p) => ({ value: p.nama_posyandu, label: p.nama_posyandu }));
    setPosyanduOptions(opts);
  }, [posyanduAll, kelurahanOptions, form.kd_kel]);

  // Auto-sync PJ fields with patient's fields while checkbox is checked
  useEffect(() => {
    if (!copyAlamatPJ) return;
    const kabLabel = kabupatenOptions.find((o) => String(o.value) === String(form.kd_kab))?.label || '';
    const kecLabel = kecamatanOptions.find((o) => String(o.value) === String(form.kd_kec))?.label || '';
    const kelLabel = kelurahanOptions.find((o) => String(o.value) === String(form.kd_kel))?.label || '';
    setForm((s) => ({
      ...s,
      alamatpj: s.alamat || '',
      kabupatenpj: kabLabel,
      kecamatanpj: kecLabel,
      kelurahanpj: kelLabel,
    }));
  }, [copyAlamatPJ, form.alamat, form.kd_kab, form.kd_kec, form.kd_kel, kabupatenOptions, kecamatanOptions, kelurahanOptions]);

  // Muat data penjab untuk penentuan label Cara Bayar meskipun tidak dalam mode edit
  useEffect(() => {
    let active = true;
    if (!penjabOptions || penjabOptions.length === 0) {
      api.penjab()
        .then((r) => {
          if (!active) return;
          const arr = Array.isArray(r) ? r : (Array.isArray(r?.data) ? r.data : []);
          setPenjabOptions(arr || []);
        })
        .catch(() => { if (active) setPenjabOptions([]); });
    }
    return () => { active = false; };
  }, []);

  const submitEdit = async () => {
    if (!patientInfo) return;
    const rm = String(patientInfo?.no_rkm_medis ?? '').trim();
    if (!rm) {
      notify && notify({ type: 'error', title: 'No. RM tidak tersedia', description: 'Tidak dapat menyimpan, No. Rekam Medis kosong atau tidak valid.' });
      return;
    }
    setSavingEdit(true);
    try {
      const fd = new FormData();
      Object.entries(form || {}).forEach(([k, v]) => fd.append(k, v ?? ''));
      fd.append('_method', 'PUT');
      fd.append('_token', csrfToken);

      const resp = await fetch(`/data-pasien/${enc(rm)}`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        body: fd,
        cache: 'no-store',
      });
      if (!resp.ok) {
        let msg = 'Gagal menyimpan data pasien';
        try { const j = await safeJson(resp); msg = j?.message || msg; } catch {}
        notify && notify({ type: 'error', title: 'Simpan gagal', description: msg });
        return;
      }
      const refreshed = await fetch(`/api/pasien/detail/${enc(rm)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' }).then((x) => safeJson(x));
      if (refreshed?.data) {
        setPatientInfo && setPatientInfo(refreshed.data);
      }
      notify && notify({ type: 'success', title: 'Data pasien diperbarui', description: 'Perubahan tersimpan.' });
      setEditOpen(false);
    } catch (e) {
      console.error('submitEdit error', e);
      notify && notify({ type: 'error', title: 'Error', description: e?.message || 'Gagal mengirim data.' });
    } finally {
      setSavingEdit(false);
    }
  };

  // Hitung label Cara Bayar dari pasien.kd_pj -> penjab.png_jawab
  const caraBayarLabel = useMemo(() => {
    try {
      const kd = String(selectedPenjab || patientInfo?.kd_pj || '').trim();
      if (!kd) return patientInfo?.png_jawab || '-';
      const match = Array.isArray(penjabOptions)
        ? penjabOptions.find((p) => String(p.id ?? p.value ?? p.kd_pj ?? '') === kd)
        : null;
      const label = match?.text ?? match?.label ?? match?.png_jawab ?? '';
      return label || patientInfo?.png_jawab || kd || '-';
    } catch {
      return patientInfo?.png_jawab || '-';
    }
  }, [selectedPenjab, patientInfo?.kd_pj, patientInfo?.png_jawab, penjabOptions]);

  return (
    <Card title="Informasi Pasien" headerRight={toggleBtn}>
      {collapsed ? null : (
        !patientInfo ? (
          <div className="text-xs text-slate-500">Belum ada pasien dipilih.</div>
        ) : (
          <>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-slate-600">No RM:</div>
              <div className="font-medium text-slate-800">{patientInfo.no_rkm_medis}</div>
              <div className="text-slate-600">Nama:</div>
              <div className="font-medium text-slate-800">{patientInfo.nm_pasien}</div>
              <div className="text-slate-600">NIK:</div>
              <div className="font-medium text-slate-800">{patientInfo.no_ktp || '-'}</div>
              <div className="text-slate-600">JK:</div>
              <div className="font-medium text-slate-800">{patientInfo.jk}</div>
              <div className="text-slate-600">Tgl Lahir:</div>
              <div className="font-medium text-slate-800">{patientInfo.tgl_lahir}</div>
              <div className="text-slate-600">No BPJS:</div>
              <div className="font-medium text-slate-800">{patientInfo.no_peserta || '-'}</div>
              <div className="text-slate-600">Telp:</div>
              <div className="font-medium text-slate-800">{patientInfo.no_tlp || '-'}</div>
              <div className="text-slate-600">Alamat:</div>
              <div className="font-medium text-slate-800">{patientInfo.alamat || '-'}</div>
              <div className="text-slate-600">Cara Bayar:</div>
              <div className="font-medium text-slate-800">{caraBayarLabel}</div>
              <div className="text-slate-600">Umur:</div>
              <div className="font-medium text-slate-800">{patientInfo.umur || '-'}</div>
            </div>
            <div className="mt-3 flex items-center gap-2">
              <button onClick={openEditModal} className="px-3 py-2 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-800 text-xs">Ubah Data</button>
            </div>
          </>
        )
      )}
      {editOpen && createPortal(
        <div className="fixed inset-0 z-[99999] flex items-center justify-center pointer-events-auto">
          <div className="absolute inset-0 bg-black/40 z-[99998]" onClick={() => setEditOpen(false)}></div>
          <div className="relative z-[99999] bg-white/95 backdrop-blur-sm shadow-xl rounded-2xl border border-slate-200 w-full max-w-2xl p-4 mx-4 md:mx-0 max-h-[85vh] overflow-auto">
            <div className="flex items-center justify-between border-b border-slate-200 pb-2">
              <div className="text-base font-semibold text-slate-800">Ubah Data Pasien</div>
              <button className="px-2 py-1 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600" onClick={() => setEditOpen(false)}>Tutup</button>
            </div>
            {loadingEdit ? (
              <div className="p-4 text-sm text-slate-600 flex items-center gap-2"><span className="inline-block h-4 w-4 border-2 border-slate-300 border-t-sky-500 rounded-full animate-spin"></span> Memuat data...</div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3 pt-3">
                <Input label="Nama" value={form.nm_pasien} onChange={(e) => setForm((s) => ({ ...s, nm_pasien: e.target.value }))} />
                <Input label="NIK" value={form.no_ktp} onChange={(e) => setForm((s) => ({ ...s, no_ktp: e.target.value }))} />
                <Input label="No. KK" value={form.no_kk} onChange={(e) => setForm((s) => ({ ...s, no_kk: e.target.value }))} />
                <Select label="JK" options={jkOptions} value={form.jk} onChange={(val) => setForm((s) => ({ ...s, jk: val }))} />
                <Input label="Tempat Lahir" value={form.tmp_lahir} onChange={(e) => setForm((s) => ({ ...s, tmp_lahir: e.target.value }))} />
                <Input label="Tgl Lahir" type="date" value={form.tgl_lahir} onChange={(e) => setForm((s) => ({ ...s, tgl_lahir: e.target.value }))} />
                <Select label="Gol. Darah" options={golDarahOptions} value={form.gol_darah} onChange={(val) => setForm((s) => ({ ...s, gol_darah: val }))} />
                <Select label="Agama" options={agamaOptions} value={form.agama} onChange={(val) => setForm((s) => ({ ...s, agama: val }))} />
                <Input label="Nama Ibu" value={form.nm_ibu} onChange={(e) => setForm((s) => ({ ...s, nm_ibu: e.target.value }))} />
                <Select label="Pendidikan" options={pndOptions} value={form.pnd} onChange={(val) => setForm((s) => ({ ...s, pnd: val }))} />
                <Select label="Keluarga" options={keluargaOptions} value={form.keluarga} onChange={(val) => setForm((s) => ({ ...s, keluarga: val }))} />
                <Input label="Nama Keluarga" value={form.namakeluarga} onChange={(e) => setForm((s) => ({ ...s, namakeluarga: e.target.value }))} />
                <Select label="Status" options={statusOptions} value={form.status} onChange={(val) => setForm((s) => ({ ...s, status: val }))} />
                <Select label="Status Nikah" options={nikahOptions} value={form.stts_nikah} onChange={(val) => setForm((s) => ({ ...s, stts_nikah: val }))} />
                <Select label="Pekerjaan" options={pekerjaanOptions} value={form.pekerjaan} onChange={(val) => setForm((s) => ({ ...s, pekerjaan: val }))} />
                <Select label="Penjab" options={penjabOptions} value={form.kd_pj} onChange={(val) => setForm((s) => ({ ...s, kd_pj: val }))} />
                <Input label="No. Peserta BPJS" value={form.no_peserta} onChange={(e) => setForm((s) => ({ ...s, no_peserta: e.target.value }))} />
                <Input label="Telp" value={form.no_tlp} onChange={(e) => setForm((s) => ({ ...s, no_tlp: e.target.value }))} />
                <label className="block md:col-span-2">
                  <span className="text-xs font-medium text-slate-600">Alamat</span>
                  <textarea className="mt-1 w-full rounded-xl border border-slate-300/80 bg-white/95 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 shadow-inner transition-all duration-200" rows={3} value={form.alamat} onChange={(e) => setForm((s) => ({ ...s, alamat: e.target.value }))}></textarea>
                </label>
                <Select label="Propinsi" options={propinsiOptions} value={form.kd_prop} onChange={(val) => setForm((s) => ({ ...s, kd_prop: val, kd_kab: '', kd_kec: '', kd_kel: '' }))} />
                <Select label="Kabupaten" options={kabupatenOptions} value={form.kd_kab} onChange={(val) => setForm((s) => ({ ...s, kd_kab: val, kd_kec: '', kd_kel: '' }))} />
                <Select label="Kecamatan" options={kecamatanOptions} value={form.kd_kec} onChange={(val) => setForm((s) => ({ ...s, kd_kec: val, kd_kel: '' }))} />
                <Select label="Kelurahan" options={kelurahanOptions} value={form.kd_kel} onChange={(val) => setForm((s) => ({ ...s, kd_kel: val }))} />
                <label className="block md:col-span-2">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-medium text-slate-600">Alamat Penanggung Jawab</span>
                    <label className="inline-flex items-center gap-2 text-xs text-slate-600">
                      <input
                        type="checkbox"
                        checked={copyAlamatPJ}
                        onChange={(e) => setCopyAlamatPJ(e.target.checked)}
                      />
                      <span>Samakan dengan Alamat pasien</span>
                    </label>
                  </div>
                  <textarea className="mt-1 w-full rounded-xl border border-slate-300/80 bg-white/95 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 shadow-inner transition-all duration-200" rows={2} value={form.alamatpj} onChange={(e) => setForm((s) => ({ ...s, alamatpj: e.target.value }))}></textarea>
                </label>
                <Input label="Kelurahan PJ" value={form.kelurahanpj} onChange={(e) => setForm((s) => ({ ...s, kelurahanpj: e.target.value }))} />
                <Input label="Kecamatan PJ" value={form.kecamatanpj} onChange={(e) => setForm((s) => ({ ...s, kecamatanpj: e.target.value }))} />
                <Input label="Kabupaten PJ" value={form.kabupatenpj} onChange={(e) => setForm((s) => ({ ...s, kabupatenpj: e.target.value }))} />
                <Select label="Posyandu" options={posyanduOptions} value={form.data_posyandu} onChange={(val) => setForm((s) => ({ ...s, data_posyandu: val }))} />
              </div>
            )}
            <div className="mt-3 flex items-center justify-end gap-2">
              <button className="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs" onClick={() => setEditOpen(false)} disabled={savingEdit}>Batal</button>
              <button className={`px-3 py-2 rounded-lg text-xs ${savingEdit ? 'bg-sky-200 text-slate-600' : 'bg-sky-600 hover:bg-sky-700 text-white'}`} onClick={submitEdit} disabled={savingEdit}>
                {savingEdit ? 'Menyimpan...' : 'Simpan'}
              </button>
            </div>
          </div>
        </div>,
        document.body
       )}
    </Card>
  );
};

// Today registration table
const TodayRegistrationTable = ({ date, kdPoli, refreshKey = 0 }) => {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);
  // Cetak label modal state
  const [labelOpen, setLabelOpen] = useState(false);
  const [labelData, setLabelData] = useState(null);
  const labelRef = useRef(null);

  const load = async () => {
    setLoading(true);
    try {
      const res = await api.todayRegs(date, kdPoli || '');
      setRows(res?.data || []);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, [date, kdPoli, refreshKey]);

  // Format tanggal lahir ke format Indonesia: DD MMMM YYYY
  const formatDateInd = (iso) => {
    try {
      if (!iso) return '';
      const s = String(iso).slice(0, 10);
      const [y, m, d] = s.split('-');
      const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
      const mm = Math.max(1, Math.min(12, parseInt(m || '1', 10)));
      return `${d} ${bulan[mm - 1]} ${y}`;
    } catch (e) {
      return String(iso || '');
    }
  };

  const handleDelete = async (row) => {
    const ok = window.confirm(`Yakin hapus registrasi?\nNo.Reg: ${row.no_reg}\nNo.Rawat: ${row.no_rawat}\nPasien: ${row.nm_pasien}`);
    if (!ok) return;
    try {
      const res = await api.deleteReg(row.no_rawat);
      if (res?.success) {
        await load();
      } else {
        alert(res?.message || 'Gagal menghapus registrasi');
      }
    } catch (e) {
      alert(e?.message || 'Terjadi kesalahan saat menghapus');
    }
  };

  const openLabelForRow = async (r) => {
    const baseData = {
      fasilitas1: '',
      fasilitas2: '',
      fasilitas3: '',
      nama: r?.nm_pasien || '',
      rm: r?.no_rkm_medis || r?.no_rm || r?.norm || '',
      tgl_lahir: '',
      alamat: '',
      poli: r?.nm_poli ? `${r.nm_poli}` : '',
      dokter: r?.nm_dokter || '',
      tanggal: date || new Date().toLocaleDateString('en-CA'),
    };
    // Prefill header dari Setting
    try {
      const info = await api.hospitalInfo();
      if (info && typeof info === 'object') {
        const name = String(info.name ?? info.nama_instansi ?? '').trim();
        const addr = String(info.address ?? info.alamat_instansi ?? '').trim();
        const kab = String(info.kabupaten ?? '').trim();
        const prop = String(info.propinsi ?? '').trim();
        const phone = String(info.phone ?? info.kontak ?? '').trim();
        baseData.fasilitas1 = name ? name.toUpperCase() : '';
        baseData.fasilitas2 = [addr, kab, prop].filter(Boolean).join(', ');
        baseData.fasilitas3 = phone ? `Telp: ${phone}` : '';
      }
    } catch (e) {
      // abaikan error setting
    }
    // Detail pasien
    try {
      const rm = baseData.rm;
      if (rm) {
        const detailRes = await api.pasienDetail(rm);
        const d = detailRes?.data ?? detailRes; // API bisa mengembalikan {status, data} atau objek langsung
        if (d && typeof d === 'object') {
          baseData.tgl_lahir = d?.tgl_lahir || baseData.tgl_lahir;
          baseData.alamat = [d?.alamat, d?.kabupaten, d?.kecamatan, d?.kelurahan].filter(Boolean).join(', ') || baseData.alamat;
          baseData.rm = d?.no_rkm_medis || baseData.rm;
          baseData.nama = d?.nm_pasien || baseData.nama;
        }
      }
    } catch (e) {}
    setLabelData(baseData);
    setLabelOpen(true);
  };

  const printLabel = () => {
    const el = labelRef.current;
    if (!el) return;
    const content = el.outerHTML;
    const css = `
      <style>
        /* Paksa orientasi landscape dan ukuran 6x4cm saat print */
        @page { size: 6cm 4cm; margin: 0; }
        @media print {
          /* Beberapa browser/driver printer membutuhkan deklarasi eksplisit ini */
          @page { size: 6cm 4cm; margin: 0; }
          html, body { width: 6cm; height: 4cm; }
          #printable-label { width: 6cm !important; height: 4cm !important; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; }
      </style>
    `;
    const html = `<!doctype html><html><head><meta charset=\"utf-8\"><title>Cetak Label</title>${css}</head><body>${content}</body></html>`;
    const w = window.open('', 'PRINT', 'width=800,height=600');
    w.document.write(html);
    w.document.close();
    w.focus();
    setTimeout(() => { w.print(); w.close(); setLabelOpen(false); }, 150);
  };

  // Auto-print ketika modal label dibuka
  useEffect(() => {
    if (!labelOpen) return;
    const t = setTimeout(() => {
      try { printLabel(); } catch (e) {}
    }, 250);
    return () => clearTimeout(t);
  }, [labelOpen]);

  return (
    <Card title={`Registrasi Hari Ini (${date})`} headerRight={<div className="text-[11px] text-slate-500">Klik No. Rawat untuk membuka pemeriksaan ralan, atau klik nama pasien untuk cetak label</div>}>
      {loading ? (
        <div className="text-sm text-slate-500">Memuat data...</div>
      ) : (
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="text-slate-600">
                <th className="text-left px-3 py-2">NoReg</th>
                <th className="text-left px-3 py-2">No. Rawat</th>
                <th className="text-left px-3 py-2">Pasien</th>
                <th className="text-left px-3 py-2">Poli</th>
                <th className="text-left px-3 py-2">Dokter</th>
                <th className="text-left px-3 py-2">Cara Bayar</th>
                <th className="text-left px-3 py-2">Jam</th>
                <th className="text-left px-3 py-2">Status</th>
                <th className="text-left px-3 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {rows.length === 0 ? (
                <tr>
                  <td colSpan={9} className="px-3 py-4 text-center text-slate-500">
                    Tidak ada data
                  </td>
                </tr>
              ) : (
                rows.map((r) => (
                  <tr key={r.no_rawat} className="hover:bg-slate-50">
                    <td className="px-3 py-2 font-mono">{r.no_reg}</td>
                    <td
                      className="px-3 py-2 font-mono cursor-pointer"
                      onClick={() => {
                        const url = `/ralan/pemeriksaan?no_rawat=${enc(r.no_rawat)}&no_rm=${enc(r.no_rkm_medis)}`;
                        try { window.location.assign(url); } catch { window.location.href = url; }
                      }}
                      title="Klik untuk buka Pemeriksaan Ralan"
                    >
                      <a
                        className="text-indigo-700 hover:underline btn-no-rawat"
                        href={`/ralan/pemeriksaan?no_rawat=${enc(r.no_rawat)}&no_rm=${enc(r.no_rkm_medis)}`}
                        title="Buka Pemeriksaan Ralan"
                        onClick={(e) => e.stopPropagation()}
                      >
                        {r.no_rawat}
                      </a>
                    </td>
                    <td className="px-3 py-2">
                      <button className="text-sky-700 hover:underline" onClick={() => openLabelForRow(r)} title="Cetak Label 6x4">
                        {r.nm_pasien}
                      </button>
                    </td>
                    <td className="px-3 py-2">{r.nm_poli} ({r.kd_poli})</td>
                    <td className="px-3 py-2">{r.nm_dokter}</td>
                    <td className="px-3 py-2">{r.png_jawab}</td>
                    <td className="px-3 py-2">{r.jam_reg}</td>
                    <td className="px-3 py-2">{r.stts}</td>
                    <td className="px-3 py-2">
                      <button className="px-2 py-1 rounded-md text-xs bg-red-600 hover:bg-red-700 text-white" title="Hapus registrasi" onClick={() => handleDelete(r)}>Hapus</button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      )}
      <div className="mt-3 text-right">
        <button onClick={load} className="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">
          Refresh
        </button>
      </div>

      {labelOpen && createPortal(
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="absolute inset-0 bg-black/40" onClick={() => setLabelOpen(false)}></div>
          <div className="relative bg-white rounded-xl shadow-lg w-[min(92vw,740px)]">
            <div className="px-4 py-2 border-b border-slate-200 flex items-center justify-between">
              <div className="font-semibold text-sm">Cetak Label (6 x 4 cm)</div>
              <div className="flex items-center gap-2">
                <button className="px-2 py-1 rounded-md text-xs bg-slate-100 hover:bg-slate-200 text-slate-700" onClick={() => setLabelOpen(false)}>Tutup</button>
                <button className="px-2 py-1 rounded-md text-xs bg-indigo-600 hover:bg-indigo-700 text-white" onClick={printLabel}>Cetak</button>
              </div>
            </div>
            <div className="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label className="block mb-2">
                  <span className="text-xs text-slate-600">Header 1</span>
                  <input className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.fasilitas1 || ''} onChange={(e)=>setLabelData((d)=>({ ...d, fasilitas1: e.target.value }))} />
                </label>
                <label className="block mb-2">
                  <span className="text-xs text-slate-600">Header 2</span>
                  <input className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.fasilitas2 || ''} onChange={(e)=>setLabelData((d)=>({ ...d, fasilitas2: e.target.value }))} />
                </label>
                <label className="block mb-2">
                  <span className="text-xs text-slate-600">Header 3</span>
                  <input className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.fasilitas3 || ''} onChange={(e)=>setLabelData((d)=>({ ...d, fasilitas3: e.target.value }))} />
                </label>
                <div className="grid grid-cols-2 gap-2 mt-2">
                  <label className="block">
                    <span className="text-xs text-slate-600">Tanggal</span>
                    <input type="date" className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.tanggal || ''} onChange={(e)=>setLabelData((d)=>({ ...d, tanggal: e.target.value }))} />
                  </label>
                  <label className="block">
                    <span className="text-xs text-slate-600">Poli</span>
                    <input className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.poli || ''} onChange={(e)=>setLabelData((d)=>({ ...d, poli: e.target.value }))} />
                  </label>
                  <label className="block col-span-2">
                    <span className="text-xs text-slate-600">Dokter</span>
                    <input className="mt-1 w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" value={labelData?.dokter || ''} onChange={(e)=>setLabelData((d)=>({ ...d, dokter: e.target.value }))} />
                  </label>
                </div>
              </div>
              <div className="flex items-start justify-center">
                <div ref={labelRef} id="printable-label" className="print:shadow-none" style={{ width: '6cm', height: '4cm', padding: '6px', border: '2px solid #0ea5b7', background: 'white' }}>
                  <div style={{ textAlign: 'center', fontWeight: 600, fontSize: '12px', lineHeight: '12px', marginBottom: '0px' }}>{labelData?.fasilitas1}</div>
                  <div style={{ textAlign: 'center', fontSize: '11px', lineHeight: '11px', marginBottom: '0px' }}>{labelData?.fasilitas2}</div>
                  <div style={{ textAlign: 'center', fontSize: '11px', lineHeight: '11px', marginBottom: '0px' }}>{labelData?.fasilitas3}</div>
                  <div style={{ borderTop: '1px solid #0ea5b7', margin: '3px 0 3px' }}></div>
                  <div style={{ marginTop: '4px', padding: '4px' }}>
                    <div style={{ fontSize: '8px', display:'flex' }}><span style={{width:'75px', display:'inline-block'}}>Nama</span><span style={{flex:1, minWidth:0, whiteSpace:'nowrap', overflow:'hidden', textOverflow:'ellipsis'}}>: {labelData?.nama}</span></div>
                    <div style={{ fontSize: '8px' }}><span style={{width:'75px', display:'inline-block'}}>No.RM</span><span>: {labelData?.rm}</span></div>
                    <div style={{ fontSize: '8px' }}><span style={{width:'75px', display:'inline-block'}}>Tgl.Lahir</span><span>: {formatDateInd(labelData?.tgl_lahir)}</span></div>
                    <div style={{ fontSize: '8px' }}><span style={{width:'75px', display:'inline-block'}}>Alamat</span><span>: {labelData?.alamat}</span></div>
                    <div style={{ fontSize: '8px' }}><span style={{width:'75px', display:'inline-block'}}>Poli Dituju</span><span>: {labelData?.poli}</span></div>
                    <div style={{ fontSize: '8px' }}><span style={{width:'75px', display:'inline-block'}}>Dokter</span><span>: {labelData?.dokter}</span></div>
                    <div style={{ fontSize: '5px' }}><span style={{width:'75px', display:'inline-block'}}>Tanggal</span><span>: {labelData?.tanggal}</span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>, document.body)}
    </Card>
  );
};

// Mini cards untuk statistik hari ini (di bawah Registrasi Pasien)
const StatBadge = ({ label, value, loading = false, color = 'bg-indigo-50 border-indigo-200 text-indigo-700' }) => (
  <motion.div
    className={`rounded-lg border ${color} p-3 flex items-center justify-between shadow-sm w-full`}
    initial={{ opacity: 0, y: 6 }}
    animate={{ opacity: 1, y: 0 }}
    transition={{ type: 'spring', stiffness: 260, damping: 22 }}
    title={label}
  >
    <div className="text-xs font-medium pr-2 truncate">{label}</div>
    {loading ? (
      <div className="h-5 w-8 bg-slate-200 rounded animate-pulse" aria-label="loading"></div>
    ) : (
      <div className="text-xl font-bold tabular-nums">{value}</div>
    )}
  </motion.div>
);

const TodayStatsCards = ({ date, kdPoli = '' }) => {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [ckgCount, setCkgCount] = useState(0);

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    // Ringkasan harian selalu mengambil semua poli (abaikan filter kdPoli)
    api.todayRegs(date, '')
      .then((resp) => {
        if (!mounted) return;
        const data = resp?.data || [];
        setRows(data);
        setCkgCount(Number(resp?.ckg_count ?? 0));
        setLoading(false);
      })
      .catch(() => setLoading(false));
    return () => {
      mounted = false;
    };
  }, [date]);

  const { total, kl2, kl3, kl4, kl5, pasienBPJS, pasienUmum } = useMemo(() => {
    const total = rows.length;
    let kl2 = 0, kl3 = 0, kl4 = 0, kl5 = 0;
    let pasienBPJS = 0, pasienUmum = 0;
    rows.forEach((r) => {
      const kdPoli = String(r?.kd_poli || '').toUpperCase();
      if (kdPoli === 'K2') kl2 += 1;
      else if (kdPoli === 'K3') kl3 += 1;
      else if (kdPoli === 'K4') kl4 += 1;
      else if (kdPoli === 'K5') kl5 += 1;

      const kdPj = String(r?.kd_pj || '').toUpperCase();
      const isBPJS = kdPj === 'BPJ' || kdPj === 'PBI';
      const isExcluded = kdPj === 'BPJ' || kdPj === 'PBI' || kdPj === 'NON';
      if (isBPJS) pasienBPJS += 1;
      else if (!isExcluded) pasienUmum += 1;
    });
    return { total, kl2, kl3, kl4, kl5, pasienBPJS, pasienUmum };
  }, [rows]);

  return (
    <Card className="">
      <div className="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-3">
        <StatBadge label="Total Registrasi Hari Ini" value={total} loading={loading} color="bg-amber-50 border-amber-200 text-amber-800" />
        <StatBadge label="Klaster 2" value={kl2} loading={loading} color="bg-sky-50 border-sky-200 text-sky-800" />
        <StatBadge label="Klaster 3" value={kl3} loading={loading} color="bg-sky-50 border-sky-200 text-sky-800" />
        <StatBadge label="Klaster 4" value={kl4} loading={loading} color="bg-sky-50 border-sky-200 text-sky-800" />
        <StatBadge label="Klaster 5" value={kl5} loading={loading} color="bg-sky-50 border-sky-200 text-sky-800" />
        <StatBadge label="Pasien BPJS" value={pasienBPJS} loading={loading} color="bg-emerald-50 border-emerald-200 text-emerald-800" />
        <StatBadge label="Pasien Umum" value={pasienUmum} loading={loading} color="bg-emerald-50 border-emerald-200 text-emerald-800" />
        <StatBadge label="Sudah CKG" value={ckgCount} loading={loading} color="bg-indigo-50 border-indigo-200 text-indigo-800" />
      </div>
    </Card>
  );
};

// Card Antrian Pendaftaran (samping kanan mini cards)
const QueueRegisterCard = ({ date, notify }) => {
  const [nextNumber, setNextNumber] = useState('');
  const [remaining, setRemaining] = useState(0);
  const [loket, setLoket] = useState('LOKET 1');
  const [loading, setLoading] = useState(false);
  const [calling, setCalling] = useState(false);
  const [lastCalledNumber, setLastCalledNumber] = useState('');
  const [recalling, setRecalling] = useState(false);

  // Preload and map audio assets for number pronunciation
  const numberAudioMap = useMemo(() => {
    const mods = import.meta.glob('../Display/assets/nomor/*.mp3', { eager: true, import: 'default' });
    const m = {};
    for (const [path, url] of Object.entries(mods)) {
      const key = String(path.split('/').pop() || '').replace('.mp3', '').toLowerCase();
      m[key] = url;
    }
    return m;
  }, []);

  // Convert an integer into a sequence of audio URLs (Indonesian pronunciation up to 9999)
  const numberToUrls = (val) => {
    const raw = String(val ?? '').trim();
    const digitsOnly = raw.replace(/\D+/g, ''); // ambil hanya angka, contoh "A001" -> "001"
    const n = parseInt(digitsOnly, 10);
    if (!Number.isFinite(n)) return [];
    const m = numberAudioMap;
    const dig = (d) => m[String(d)] || '';

    const speakUnderHundred = (x) => {
      if (x < 0) return [];
      if (x < 10) return [dig(x)];
      if (x === 10) return [m['10'] || dig(1), m['10'] ? '' : m['puluh']].filter(Boolean);
      if (x === 11) return [m['sebelas']].filter(Boolean);
      if (x < 20) {
        // 12..19 => "<digit> belas"
        return [dig(x % 10), m['belas']].filter(Boolean);
      }
      const tens = Math.floor(x / 10);
      const ones = x % 10;
      if (ones === 0) {
        // 20,30,..90 prefer specific file if exists, else "<digit> puluh"
        return [m[String(x)] || dig(tens), m[String(x)] ? '' : m['puluh']].filter(Boolean);
      }
      return [dig(tens), m['puluh'], dig(ones)].filter(Boolean);
    };

    const speakHundred = (x) => {
      if (x < 100) return speakUnderHundred(x);
      const hundreds = Math.floor(x / 100);
      const rest = x % 100;
      const seq = [dig(hundreds), m['ratus']].filter(Boolean);
      if (rest > 0) seq.push(...speakUnderHundred(rest));
      return seq;
    };

    const speakThousand = (x) => {
      if (x < 1000) return speakHundred(x);
      const thousands = Math.floor(x / 1000);
      const rest = x % 1000;
      const seq = [dig(thousands), m['ribu']].filter(Boolean);
      if (rest > 0) seq.push(...speakHundred(rest));
      return seq;
    };

    if (n === 0) return [dig(0)].filter(Boolean);
    return speakThousand(n).filter(Boolean);
  };

  // Play an array of audio URLs sequentially
  const playSequence = (urls = []) => {
    let idx = 0;
    const playNext = () => {
      if (idx >= urls.length) return;
      const src = urls[idx++];
      try {
        const a = new Audio(src);
        a.addEventListener('ended', playNext);
        a.addEventListener('error', playNext);
        a.play().catch(playNext);
      } catch (_) {
        playNext();
      }
    };
    playNext();
  };

  const playBell = (nomorOverride, tujuanOverride) => {
    try {
      const nomorStr = String(nomorOverride ?? lastCalledNumber ?? nextNumber ?? '').trim();
      const sequence = [
        '/display/assets/notifbell.mp3',
        nomorAntrianUrl,
        ...numberToUrls(nomorStr),
        menujuUrl,
      ];
      playSequence(sequence);
    } catch (e) {
      // ignore autoplay errors
    }
  };

  const loadNext = async () => {
    try {
      setLoading(true);
      const res = await api.antriNext(date);
      const nomor = res?.nomor ?? res?.next ?? res?.data?.nomor ?? '';
      const sisa = Number(res?.sisa ?? res?.remaining ?? res?.data?.sisa ?? 0);
      setNextNumber(String(nomor || ''));
      setRemaining(Number.isFinite(sisa) ? sisa : 0);
    } catch (e) {
      console.error('Gagal memuat antrian berikutnya:', e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (date) loadNext();
  }, [date]);

  const panggil = async () => {
    // Hindari panggilan server jika tidak ada nomor berikutnya dan sisa antrian 0
    if (!nextNumber && remaining <= 0) {
      notify?.({ type: 'warning', title: 'Antrian habis', description: 'Tidak ada nomor antrian tersisa.' });
      return;
    }
    try {
      setCalling(true);
      const payload = nextNumber ? { date, loket, nomor: nextNumber } : { date, loket };
      const res = await api.antriCall(payload);
      const nomorDipanggil = res?.nomor ?? nextNumber;
      if (res?.success && nomorDipanggil) {
        setLastCalledNumber(String(nomorDipanggil || ''));
        notify?.({ type: 'success', title: 'Panggilan antrian', description: `Nomor ${nomorDipanggil} dipanggil (${loket}).` });
        playBell(nomorDipanggil);
      } else if (!nomorDipanggil) {
        notify?.({ type: 'error', title: 'Antrian habis', description: 'Tidak ada nomor antrian tersisa.' });
      } else {
        notify?.({ type: 'error', title: 'Gagal memanggil', description: res?.message || 'Terjadi kesalahan.' });
      }
      await loadNext();
    } catch (e) {
      console.error('Gagal memanggil antrian:', e);
      notify?.({ type: 'error', title: 'Kesalahan jaringan', description: 'Gagal memanggil antrian.' });
    } finally {
      setCalling(false);
    }
  };

  const panggilUlang = async () => {
    if (!lastCalledNumber) {
      notify?.({ type: 'warning', title: 'Tidak ada nomor terakhir', description: 'Belum ada nomor yang dipanggil.' });
      return;
    }
    try {
      setRecalling(true);
      let res = await api.antriRecall({ date, loket, nomor: lastCalledNumber });
      if (!res?.success) {
        // Fallback jika endpoint recall belum tersedia
        res = await api.antriCall({ date, loket, nomor: lastCalledNumber, recall: true, repeat: true });
      }
      if (res?.success) {
        notify?.({ type: 'success', title: 'Panggil ulang', description: `Nomor ${lastCalledNumber} dipanggil ulang (${loket}).` });
        playBell(lastCalledNumber);
      } else {
        notify?.({ type: 'error', title: 'Gagal panggil ulang', description: res?.message || 'Terjadi kesalahan.' });
      }
    } catch (e) {
      console.error('Gagal panggil ulang antrian:', e);
      notify?.({ type: 'error', title: 'Kesalahan jaringan', description: 'Gagal panggil ulang antrian.' });
    } finally {
      setRecalling(false);
    }
  };

  return (
    <Card >
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 items-end">
        <div className="rounded-lg border bg-indigo-50 border-indigo-200 text-indigo-800 p-3">
          <div className="text-xs font-medium">Nomor Berikutnya</div>
          <div className="text-xl font-bold mt-1 tabular-nums">{loading ? '…' : (nextNumber || '-')}</div>
        </div>
        <div className="rounded-lg border bg-amber-50 border-amber-200 text-amber-800 p-3">
          <div className="text-xs font-medium">Sisa Antrian</div>
          <div className="text-xl font-bold mt-1 tabular-nums">{loading ? '…' : remaining}</div>
        </div>
        <div className="flex flex-col md:flex-row items-end gap-2">
          <div className="flex-1">
            <Select label="Loket" options={["LOKET 1","LOKET 2","LOKET 3","LOKET 4"].map((v)=>({ value: v, label: v }))} value={loket} onChange={setLoket} />
          </div>
          <button
            className={`w-full md:w-auto px-3 py-2 rounded-md text-xs font-semibold ${(calling || (!nextNumber && remaining <= 0)) ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700'}`}
            onClick={panggil}
            disabled={calling || (!nextNumber && remaining <= 0)}
            title={!nextNumber && remaining <= 0 ? 'Antrian habis' : 'Panggil nomor berikutnya'}
          >
            {calling ? 'Memanggil…' : 'Panggil'}
          </button>
          <button
            className={`w-full md:w-auto px-3 py-2 rounded-md text-xs font-semibold ${(!lastCalledNumber || recalling) ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-amber-600 text-white hover:bg-amber-700'}`}
            onClick={panggilUlang}
            disabled={!lastCalledNumber || recalling}
            title={lastCalledNumber ? `Panggil ulang nomor ${lastCalledNumber}` : 'Belum ada nomor dipanggil'}
          >
            {recalling ? 'Memanggil ulang…' : 'Panggil Ulang'}
          </button>
        </div>
      </div>
      {!!lastCalledNumber && (
        <div className="mt-2 text-xs text-slate-600">Terakhir dipanggil: <span className="font-semibold">{lastCalledNumber}</span></div>
      )}
      <div className="mt-2 text-[10px] text-slate-500">Sumber: antripendaftaran_nomor (status=0 = belum dipanggil).</div>
    </Card>
  );
};

export default function RegPeriksaPage() {
  const getTodayLocalISO = () => {
    try {
      return new Date().toLocaleDateString('en-CA');
    } catch (e) {
      const d = new Date();
      d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
      return d.toISOString().slice(0, 10);
    }
  };
  const today = getTodayLocalISO();
  const [date, setDate] = useState(today);
  const [poliklinik, setPoliklinikState] = useState({ options: [], value: '' });
  const [dokter, setDokterState] = useState({ options: [], value: '' });
  const [penjab, setPenjabState] = useState({ options: [], value: '' });
  const [patientInfo, setPatientInfo] = useState(null);
  const [toasts, setToasts] = useState([]);
  const notify = (msg) => {
    const id = Date.now() + Math.random();
    setToasts((s) => [...s, { id, ...(msg || {}) }]);
    setTimeout(() => {
      setToasts((s) => s.filter((t) => t.id !== id));
    }, 4000);
  };

  // tombol Baru perlu me-refresh kartu BPJS
  const [bpjsRefreshKey, setBpjsRefreshKey] = useState(0);
  const [regRefreshKey, setRegRefreshKey] = useState(0);
  const triggerBpjsRefresh = () => setBpjsRefreshKey((k) => k + 1);

  useEffect(() => {
    api.poliklinik().then((r) => setPoliklinikState((s) => ({ ...s, options: r || [] })));
    api.dokter().then((r) => setDokterState((s) => ({ ...s, options: r || [] })));
    api.penjab().then((r) => setPenjabState((s) => ({ ...s, options: r || [] })));
  }, []);

  useEffect(() => {
    if (poliklinik.value) {
      api.dokterByPoli(poliklinik.value).then((r) => setDokterState((s) => ({ ...s, options: r || [] })));
    } else {
      api.dokter().then((r) => setDokterState((s) => ({ ...s, options: r || [] })));
    }
  }, [poliklinik.value]);

  const setPoliklinik = (val) => setPoliklinikState((s) => ({ ...s, value: val }));
  const setDokter = (val) => setDokterState((s) => ({ ...s, value: val }));
  const setPenjab = (val) => setPenjabState((s) => ({ ...s, value: val }));

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      <div className="w-full px-0 py-2">
        {/* notifikasi pojok kanan atas */}
        <div className="fixed top-3 right-3 z-[2000] space-y-2">
          {toasts.map((t) => (
            <motion.div
              key={t.id}
              initial={{ opacity: 0, y: -8, scale: 0.98 }}
              animate={{ opacity: 1, y: 0, scale: 1 }}
              className={`pointer-events-auto w-80 max-w-[22rem] rounded-xl border shadow-sm p-3 ${t.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : t.type === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-sky-50 border-sky-200 text-sky-800'}`}
            >
              <div className="font-semibold text-sm">{t.title || 'Info'}</div>
              {t.description && <div className="text-xs mt-0.5">{t.description}</div>}
            </motion.div>
          ))}
        </div>
        {/* <motion.h1 initial={{ opacity: 0, y: 6 }} animate={{ opacity: 1, y: 0 }} className="text-xl font-bold text-slate-800 mb-3">
          Menu Registrasi Pasien
        </motion.h1> */}
        <div className="grid grid-cols-1 gap-4">
          {/* Registrasi Pasien - Ringkasan Hari Ini + Antrian Pendaftaran */}
          <div className="grid grid-cols-1 gap-4">
            <TodayStatsCards date={date} kdPoli={poliklinik.value} />
          </div>

          {/* Pencarian Pasien & Form Registrasi (full width) */}
          <PatientSearchRegister
            date={date}
            // opsi dan nilai untuk filter yang dipindahkan
            poliklinikOptions={poliklinik.options}
            selectedPoli={poliklinik.value}
            setSelectedPoli={setPoliklinik}
            dokterOptions={dokter.options}
            selectedDokter={dokter.value}
            setSelectedDokter={setDokter}
            penjabOptions={penjab.options}
            selectedPenjab={penjab.value}
            setSelectedPenjab={setPenjab}
            setPatientInfo={setPatientInfo}
            patientInfo={patientInfo}
            notify={notify}
            refreshBpjs={triggerBpjsRefresh}
            bpjsRefreshKey={bpjsRefreshKey}
            onRegistered={() => setRegRefreshKey((k) => k + 1)}
          />

          <TodayRegistrationTable date={date} kdPoli={poliklinik.value} refreshKey={regRefreshKey} />
        </div>
      </div>
    </div>
  );
}