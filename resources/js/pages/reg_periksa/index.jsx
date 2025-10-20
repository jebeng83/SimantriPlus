import React, { useEffect, useMemo, useState } from 'react';
import { motion } from 'framer-motion';

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

// API helpers
const api = {
  poliklinik: (q = '', limit = 200) => fetch(`/api/poliklinik?q=${encodeURIComponent(q)}&limit=${limit}`).then((r) => r.json()),
  dokterByPoli: (kdPoli) => fetch(`/ralan/dokter/${enc(kdPoli)}`).then((r) => r.json()),
  dokter: (q = '', limit = 200) => fetch(`/api/dokter?q=${encodeURIComponent(q)}&limit=${limit}`).then((r) => r.json()),
  penjab: () => fetch('/api/penjab').then((r) => r.json()),
  pasienSearch: (q, limit = 10) => fetch(`/api/pasien?q=${encodeURIComponent(q)}&limit=${limit}`).then((r) => r.json()),
  pasienDetail: (rm) => fetch(`/api/pasien/detail/${enc(rm)}`).then((r) => r.json()),
  pcarePesertaByKartu: (noKartu) => fetch(`/api/pcare/peserta/noka/${enc(noKartu)}`).then((r) => r.json()),
  pcarePesertaByNik: (nik) => fetch(`/api/pcare/peserta/nik/${enc(nik)}`).then((r) => r.json()),
  todayRegs: (date, kdPoli = '') => fetch(`/api/regperiksa/today?date=${encodeURIComponent(date)}${kdPoli ? `&kd_poli=${encodeURIComponent(kdPoli)}` : ''}`).then((r) => r.json()),
  generateNoReg: (kdDokter, tgl, kdPoli = '') => fetch(`/regperiksa/generate-noreg/${enc(kdDokter)}/${enc(tgl)}${kdPoli ? `?kd_poli=${encodeURIComponent(kdPoli)}` : ''}`).then((r) => r.json()),
  generateNoRawat: (tgl) => fetch(`/regperiksa/generate-norawat/${enc(tgl)}`).then(async (r) => {
    const ct = r.headers.get('content-type') || '';
    if (ct.includes('application/json')) return r.json();
    const text = await r.text();
    return { success: false, message: 'Invalid JSON response', raw: text };
  }),
  storeReg: (payload) => fetch('/regperiksa/store', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(payload) }).then((r) => r.json()),
  // Tambah: kirim antrean ke BPJS (Mobile JKN)
  bpjsAddAntrean: (data) =>
    fetch('/api/antrean/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify(data),
    }).then(async (r) => {
      const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) return r.json();
      const text = await r.text();
      try {
        return JSON.parse(text);
      } catch (err) {
        return { metadata: { code: r.status, message: 'Non-JSON response from server' }, raw: text };
      }
    }),
  // Fallback: buat antrean BPJS dari no_rawat (server akan merakit payload-nya)
  bpjsCreateAntrean: (payload) =>
    fetch('/api/antrean/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify(payload),
    }).then(async (r) => {
      const ct = r.headers.get('content-type') || '';
      if (ct.includes('application/json')) return r.json();
      const text = await r.text();
      try {
        return JSON.parse(text);
      } catch (err) {
        return { metadata: { code: r.status, message: 'Non-JSON response from server' }, raw: text };
      }
    }),
  // Cek Status SRK via endpoint backend (Mobile JKN antrean/add simulasi)
  bpjsSrkStatus: (nomorkartu = '', nik = '') => {
    const params = new URLSearchParams();
    if (String(nomorkartu || '').trim()) params.append('nomorkartu', String(nomorkartu).trim());
    if (String(nik || '').trim()) params.append('nik', String(nik).trim());
    const qs = params.toString();
    return fetch(`/api/bpjs/srk-status${qs ? `?${qs}` : ''}`).then((r) => r.json());
  },
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
      <div className="px-4 py-3 border-b border-slate-200/80 flex items-center justify-between">
        <h3 className="text-slate-800 font-semibold text-base">{title}</h3>
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
        p_jawab: patientInfo.namakeluarga ?? prev.p_jawab,
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
  };

  // Tombol reset untuk menyiapkan registrasi berikutnya
  const newBtn = (
    <button
      onClick={() => clearForm()}
      className="px-2 py-1 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600"
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

      if (meta?.code === 200) {
        const nomor = bpjsRes?.response?.nomorantrean || dataAdd.nomorantrean;
        notify?.({ type: 'success', title: 'Antrean BPJS berhasil', description: `Nomor antrean: ${nomor}` });
      } else if (meta?.code === 201) {
        const msg = (meta?.message || '').toLowerCase();
        const isSkrining = msg.includes('skrining');
        if (isSkrining) {
          // Peserta belum melakukan skrining kesehatan
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

  const handleSubmit = async () => {
    if (!canRegister) {
      notify?.({ type: 'info', title: 'Lengkapi data', description: 'Pilih pasien, poli, dokter, cara bayar, dan generate NoReg.' });
      return;
    }
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

  return (
    <>
      <Card title="Pencarian Pasien & Form Registrasi">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div>
            <Input
              label="Cari pasien (Nama / RM / NIK / No BPJS)"
              placeholder="contoh: Siti / 12345 / 3301xxxx"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
            />
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
                    const name = (r.nm_pasien ?? r.nama ?? '').toString().trim();
                    const labelRaw = r.text ?? r.label ?? (rm || name ? `${rm} - ${name}` : 'Tidak diketahui');
                    const label = String(labelRaw).trim();
                    const nik = (r.no_ktp ?? r.nik ?? r.noKTP ?? '').toString().trim();
                    return (
                      <motion.li
                        key={rm || label || idx}
                        className="p-3 hover:bg-slate-50 cursor-pointer"
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
                            p_jawab: r.namakeluarga ?? prev.p_jawab,
                            hubunganpj: r.keluarga ?? prev.hubunganpj,
                            almt_pj: r.alamatpj ?? prev.almt_pj,
                          }));
                          if (r.kd_pj) setSelectedPenjab(r.kd_pj);
                        }}
                      >
                        <div className="font-medium text-slate-800 text-sm">{label}</div>
                        <div className="text-xs text-slate-500">No RM: {rm || '-'} • NIK: {nik || '-'}</div>
                      </motion.li>
                    );
                  })}
                </ul>
              ) : (
                <div className="text-xs text-slate-500">Masukkan minimal 2 karakter untuk mencari.</div>
              )}
            </div>
            {/* Card Informasi Pasien dipindahkan ke sisi kiri, dalam Card Pencarian & Form Registrasi */}
            <div className="mt-4">
              <PatientInfoCard patientInfo={patientInfo} />
            </div>
          </div>
          <div className="space-y-3">
            {/* Filter yang dipindahkan ke card registrasi */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
              <Select label="Poliklinik" options={poliklinikOptions} value={selectedPoli} onChange={setSelectedPoli} />
              <Select label="Dokter" options={dokterOptions} value={selectedDokter} onChange={setSelectedDokter} />
              <Select label="Cara Bayar" options={penjabOptions} value={selectedPenjab} onChange={setSelectedPenjab} />
            </div>

            {/* Menampilkan data pasien terpilih pada form */}
            <div className="grid grid-cols-2 gap-3">
              <Input label="No. RM (auto)" value={patientInfo?.no_rkm_medis ?? selectedPatient?.id ?? ''} readOnly />
              <Input label="Nama Pasien (auto)" value={patientInfo?.nm_pasien ?? selectedPatient?.text ?? ''} readOnly />
            </div>

            {/* Form Registrasi */}
            <Card title="Form Registrasi" headerRight={newBtn}>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                <Input label="No. Registrasi" value={noReg} readOnly />
                <Input label="Tanggal" type="date" value={date} onChange={(e) => { /* handled outside */ }} readOnly />
                <Input label="No. Rawat (preview)" value={noRawatPreview} readOnly />
              </div>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <Input label="Penanggung Jawab" value={form.p_jawab} onChange={(e) => setForm({ ...form, p_jawab: e.target.value })} />
                <Input label="Alamat PJ" value={form.almt_pj} onChange={(e) => setForm({ ...form, almt_pj: e.target.value })} />
                <Input label="Hubungan PJ" value={form.hubunganpj} onChange={(e) => setForm({ ...form, hubunganpj: e.target.value })} />
              </div>
              <div className="mt-3 flex items-center justify-between">
                <button
                  className={`px-4 py-2 rounded-lg text-sm font-medium ${isGeneratingNoReg ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-sky-600 text-white hover:bg-sky-700'}`}
                  onClick={handleGenerateNoReg}
                  disabled={isGeneratingNoReg || !canGenerate}
                >
                  {isGeneratingNoReg ? 'Membuat NoReg...' : 'Generate NoReg'}
                </button>
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
                onClick={closeSkriningModal}
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
const BPJSStatusCard = ({ patientInfo }) => {
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
    fetch(`/api/ckg/status/${enc(rm)}?year=${year}`)
      .then((r) => r.json())
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
const PatientInfoCard = ({ patientInfo }) => {
  const [collapsed, setCollapsed] = useState(false);
  const toggleBtn = (
    <button
      onClick={() => setCollapsed((v) => !v)}
      className="px-2 py-1 text-xs rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600"
    >
      {collapsed ? 'Buka' : 'Tutup'}
    </button>
  );

  return (
    <Card title="Informasi Pasien" headerRight={toggleBtn}>
      {collapsed ? null : (
        !patientInfo ? (
          <div className="text-xs text-slate-500">Belum ada pasien dipilih.</div>
        ) : (
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
            <div className="font-medium text-slate-800">{patientInfo.png_jawab || '-'}</div>
            <div className="text-slate-600">Umur:</div>
            <div className="font-medium text-slate-800">{patientInfo.umur || '-'}</div>
          </div>
        )
      )}
    </Card>
  );
};

// Today registration table
const TodayRegistrationTable = ({ date, kdPoli }) => {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(false);

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
  }, [date, kdPoli]);

  return (
    <Card title={`Registrasi Hari Ini (${date})`}>
      {loading ? (
        <div className="text-sm text-slate-500">Memuat data...</div>
      ) : (
        <div className="overflow-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="text-slate-600">
                <th className="text-left px-3 py-2">NoReg</th>
                <th className="text-left px-3 py-2">NoRawat</th>
                <th className="text-left px-3 py-2">Pasien</th>
                <th className="text-left px-3 py-2">Poli</th>
                <th className="text-left px-3 py-2">Dokter</th>
                <th className="text-left px-3 py-2">Cara Bayar</th>
                <th className="text-left px-3 py-2">Jam</th>
                <th className="text-left px-3 py-2">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-200">
              {rows.length === 0 ? (
                <tr>
                  <td colSpan={8} className="px-3 py-4 text-center text-slate-500">
                    Tidak ada data
                  </td>
                </tr>
              ) : (
                rows.map((r) => (
                  <tr key={r.no_rawat} className="hover:bg-slate-50">
                    <td className="px-3 py-2 font-mono">{r.no_reg}</td>
                    <td className="px-3 py-2 font-mono">{r.no_rawat}</td>
                    <td className="px-3 py-2">{r.nm_pasien}</td>
                    <td className="px-3 py-2">{r.nm_poli} ({r.kd_poli})</td>
                    <td className="px-3 py-2">{r.nm_dokter}</td>
                    <td className="px-3 py-2">{r.png_jawab}</td>
                    <td className="px-3 py-2">{r.jam_reg}</td>
                    <td className="px-3 py-2">{r.stts}</td>
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
    </Card>
  );
};

export default function RegPeriksaPage() {
  const today = new Date().toISOString().slice(0, 10);
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
        <div className="fixed top-3 right-3 z-50 space-y-2">
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

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div className="lg:col-span-2">
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
              />
            </div>
            <div className="space-y-4">
              <CKGStatusCard patientInfo={patientInfo} />
          <BPJSStatusCard patientInfo={patientInfo} />
            </div>
          </div>
          <TodayRegistrationTable date={date} kdPoli={poliklinik.value} />
        </div>
      </div>
    </div>
  );
}