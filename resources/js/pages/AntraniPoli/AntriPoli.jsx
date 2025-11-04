import React, { useEffect, useMemo, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion'

// Helper: fetch JSON
async function getJSON(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch (e) {
    console.error('Fetch error:', url, e);
    return null;
  }
}

function useClock() {
  const [now, setNow] = useState(new Date());
  useEffect(() => {
    const id = setInterval(() => setNow(new Date()), 1000);
    return () => clearInterval(id);
  }, []);
  return now;
}

function formatDateTime(d) {
  try {
    return new Intl.DateTimeFormat('id-ID', {
      dateStyle: 'full',
      timeStyle: 'medium'
    }).format(d);
  } catch (e) {
    return d?.toLocaleString?.() || '';
  }
}

function StatBadge({ label, value, color = 'bg-slate-700' }) {
  return (
    <div className={`px-3 py-1 rounded text-xs ${color} text-white mr-2 mb-2 inline-block`}> 
      <span className="opacity-75 mr-1">{label}:</span>
      <strong>{value}</strong>
    </div>
  );
}

export default function AntriPoli() {
  const now = useClock();
  const [hospital, setHospital] = useState({ name: 'RSUD', kabupaten: '', propinsi: '' });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [called, setCalled] = useState(null);
  const [groups, setGroups] = useState([]);
  const [lastUpdated, setLastUpdated] = useState(null);
  // Ref untuk menandai panggilan yang sudah diumumkan
  const lastSpokenRef = useRef({ key: null, at: 0 });

  async function loadAll() {
    setLoading(true);
    setError('');
    try {
      const [info, disp] = await Promise.all([
        getJSON('/api/setting/hospital-info'),
        getJSON('/api/antri-poli/display')
      ]);
      if (info && !info.error) setHospital(info);
      if (disp && disp.success) {
        setCalled(disp.dipanggil || null);
        setGroups(Array.isArray(disp.groups) ? disp.groups : []);
        setLastUpdated(disp.timestamp || new Date().toISOString());
      }
    } catch (e) {
      setError(e?.message || 'Gagal memuat data');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadAll();
    const id = setInterval(loadAll, 10000); // refresh tiap 10 detik
    return () => clearInterval(id);
  }, []);

  // Panggilan antrian dengan Text-to-Speech (ResponsiveVoice), otomatis saat pasien dipanggil berubah
  useEffect(() => {
    if (!called) return;
    // Key unik untuk satu pemanggilan (agar tidak berulang):
    // gabungan no_rawat/no_reg + poli + dokter
    const keyParts = [
      called?.no_rawat || '',
      called?.no_reg || '',
      called?.kd_poli || called?.nm_poli || '',
      called?.kd_dokter || called?.nm_dokter || ''
    ];
    const key = keyParts.join('|');

    // Jika key sama dengan yang terakhir diumumkan, jangan bicara lagi
    const last = lastSpokenRef.current;
    if (last.key === key) return;

    const nomor = called?.no_reg ? `Nomor Antrian ${called.no_reg}.` : '';
    const namaClean = called?.nm_pasien ? sanitizePatientName(called.nm_pasien) : '';
    const nama = called?.nm_pasien ? ` Atas Nama ${namaClean}.` : '';
    const dokterNameClean = called?.nm_dokter ? sanitizeDoctorName(called.nm_dokter) : '';
    const dokter = (called?.nm_dokter || called?.kd_dokter) ? ` Pasien Dokter ${dokterNameClean || called.kd_dokter}.` : '';
    const poli = (called?.nm_poli || called?.kd_poli) ? ` Silahkan Ke Poli ${called.nm_poli || called.kd_poli}.` : '';
    const text = `${nomor}${nama}${dokter}${poli}`.trim();

    try {
      // Batalkan TTS yang sedang berjalan (hindari overlap)
      if (window.responsiveVoice && typeof window.responsiveVoice.cancel === 'function') {
        window.responsiveVoice.cancel();
      }
      if (window.speechSynthesis && typeof window.speechSynthesis.cancel === 'function') {
        window.speechSynthesis.cancel();
      }

      if (window.responsiveVoice && typeof window.responsiveVoice.speak === 'function') {
        window.responsiveVoice.speak(text, 'Indonesian Female', { rate: 1, pitch: 1 });
        lastSpokenRef.current = { key, at: Date.now() };
      } else if (window.speechSynthesis) {
        const utter = new SpeechSynthesisUtterance(text);
        const voices = window.speechSynthesis.getVoices();
        const idVoice = voices.find(v => /id|indonesian/i.test(v.lang) || /Indonesian/i.test(v.name));
        if (idVoice) utter.voice = idVoice;
        utter.rate = 1; utter.pitch = 1;
        window.speechSynthesis.speak(utter);
        lastSpokenRef.current = { key, at: Date.now() };
      } else {
        console.warn('Text-to-speech tidak tersedia di browser ini.');
        lastSpokenRef.current = { key, at: Date.now() };
      }
    } catch (e) {
      console.warn('Gagal memutar TTS:', e);
    }
  }, [called]);

  const totalPasien = useMemo(() => groups.reduce((acc, g) => acc + (g?.total || 0), 0), [groups]);
  const totalMenunggu = useMemo(() => groups.reduce((acc, g) => acc + (g?.menunggu || 0), 0), [groups]);
  const totalSelesai = useMemo(() => groups.reduce((acc, g) => acc + (g?.selesai || 0), 0), [groups]);
  const totalDipanggil = useMemo(() => groups.reduce((acc, g) => acc + (g?.dipanggil || 0), 0), [groups]);

  return (
    <div className="min-h-screen w-full bg-slate-900 text-white">
      {/* Header */}
      <header className="w-full px-6 py-4 bg-slate-800 shadow flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">{hospital?.name || 'Antri Poli'}</h1>
          <p className="text-sm opacity-75">{hospital?.kabupaten} {hospital?.propinsi ? `- ${hospital.propinsi}` : ''}</p>
        </div>
        <div className="text-right">
          <div className="font-semibold">{formatDateTime(now)}</div>
          {lastUpdated && (<div className="text-xs opacity-70">Update: {new Date(lastUpdated).toLocaleTimeString('id-ID')}</div>)}
        </div>
      </header>

      {/* Summary stats */}
      <section className="px-6 py-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="rounded-lg bg-white text-black p-3 md:p-4 shadow border border-slate-200">
          <div className="text-base md:text-lg font-semibold mb-3">Ringkasan Hari Ini</div>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 md:gap-3">
            <div className="rounded bg-slate-700 text-white p-3">
              <div className="text-xs md:text-sm opacity-75">Total</div>
              <div className="text-xl md:text-2xl font-bold">{totalPasien}</div>
            </div>
            <div className="rounded bg-slate-700 text-white p-3">
              <div className="text-xs md:text-sm opacity-75">Menunggu</div>
              <div className="text-xl md:text-2xl font-bold">{totalMenunggu}</div>
            </div>
            <div className="rounded bg-slate-700 text-white p-3">
              <div className="text-xs md:text-sm opacity-75">Dipanggil</div>
              <div className="text-xl md:text-2xl font-bold">{totalDipanggil}</div>
            </div>
            <div className="rounded bg-slate-700 text-white p-3">
              <div className="text-xs md:text-sm opacity-75">Selesai</div>
              <div className="text-xl md:text-2xl font-bold">{totalSelesai}</div>
            </div>
          </div>
        </div>
        <div className="rounded-lg bg-white text-black p-3 md:p-4 shadow border border-slate-200 md:col-span-3">
          <div className="text-base md:text-lg font-semibold mb-3">Pasien Sedang Dipanggil</div>
          <AnimatePresence initial={false}>
            {called ? (
              <motion.div
                initial={false}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.2, ease: 'easeInOut' }}
                className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3 xl:gap-4 text-base md:text-lg"
              >
                <div className="bg-slate-700 text-white rounded p-2 md:p-3"><div className="text-xs md:text-sm opacity-70">No. Antrian</div><div className="font-extrabold text-3xl md:text-4xl 2xl:text-5xl tracking-wide">{called.no_reg}</div></div>
                <div className="bg-slate-700 text-white rounded p-2 md:p-3"><div className="text-xs md:text-sm opacity-70">Nama</div><div className="font-bold text-lg md:text-2xl 2xl:text-3xl">{called.nm_pasien}</div></div>
                <div className="bg-slate-700 text-white rounded p-2 md:p-3"><div className="text-xs md:text-sm opacity-70">Poli</div><div className="font-bold text-lg md:text-2xl 2xl:text-3xl">{called.nm_poli}</div></div>
                <div className="bg-slate-700 text-white rounded p-2 md:p-3"><div className="text-xs md:text-sm opacity-70">Dokter</div><div className="font-bold text-lg md:text-2xl 2xl:text-3xl">{called.nm_dokter || '-'}</div></div>
              </motion.div>
            ) : (
              <motion.div
                key="no-called"
                initial={false}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.2, ease: 'easeInOut' }}
                className="text-slate-600"
              >
                Belum ada panggilan aktif.
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </section>

      {/* Grid per Poliklinik dan Dokter */}
      <main className="px-6 pb-24">
        {loading && <div className="text-slate-300"></div>}
        {error && <div className="text-red-400">{error}</div>}
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          {groups.map((poli) => (
            <motion.div
              layout
              transition={{ type: 'spring', stiffness: 180, damping: 24 }}
              initial={false}
              key={poli.kd_poli}
              className="rounded-lg bg-white text-black p-4 shadow"
            >
              <div className="flex items-center mb-2">
                <h2 className="text-2xl md:text-3xl font-bold">{poli.nm_poli}</h2>
              </div>
              {/* Hapus ringkasan atas agar tidak double dengan ringkasan per dokter */}
              {/* <div className="flex flex-wrap mb-3">
                <StatBadge label="Total" value={poli.total} color="bg-slate-700" />
                <StatBadge label="Menunggu" value={poli.menunggu} color="bg-yellow-700" />
                <StatBadge label="Dipanggil" value={poli.dipanggil} color="bg-blue-700" />
                <StatBadge label="Selesai" value={poli.selesai} color="bg-green-700" />
              </div> */}

              {Array.isArray(poli.dokters) && poli.dokters.length > 0 ? (
                <div className="space-y-2">
                  {poli.dokters.map((d) => (
                    <motion.div
                      layout
                      transition={{ type: 'spring', stiffness: 180, damping: 24 }}
                      initial={false}
                      key={`${poli.kd_poli}-${d.kd_dokter}`}
                      className="rounded bg-slate-700 text-white p-3"
                    >
                      <div className="grid grid-cols-12 gap-x-3 md:gap-x-4 gap-y-2 items-start">
                        {/* Kiri baris 1: header dokter */}
                        <div className="col-span-12 md:col-span-8 md:row-start-1">
                          <div className="text-sm opacity-75">Dokter</div>
                          <div className="font-semibold text-lg md:text-xl">{d.nm_dokter}</div>
                        </div>
                        {/* Kiri baris 2: ringkasan 2x2 */}
                        <div className="col-span-12 md:col-span-8 md:row-start-2">
                          <div className="mt-0 grid grid-cols-2 gap-2 md:gap-3">
                            <div className="rounded bg-slate-600 text-white p-2 md:p-3">
                              <div className="text-xs md:text-sm opacity-75">Total</div>
                              <div className="text-2xl md:text-3xl font-bold">{d.total}</div>
                            </div>
                            <div className="rounded bg-yellow-800 text-white p-2 md:p-3">
                              <div className="text-xs md:text-sm opacity-75">Menunggu</div>
                              <div className="text-2xl md:text-3xl font-bold">{d.menunggu}</div>
                            </div>
                            <div className="rounded bg-blue-800 text-white p-2 md:p-3">
                              <div className="text-xs md:text-sm opacity-75">Dipanggil</div>
                              <div className="text-2xl md:text-3xl font-bold">{d.dipanggil}</div>
                            </div>
                            <div className="rounded bg-green-800 text-white p-2 md:p-3">
                              <div className="text-xs md:text-sm opacity-75">Selesai</div>
                              <div className="text-2xl md:text-3xl font-bold">{d.selesai}</div>
                            </div>
                          </div>
                        </div>
                        {/* Kanan baris 2: kartu No. Antrian, sejajar dengan ringkasan kiri */}
                        <div className="col-span-12 md:col-span-4 md:row-start-2">
                          <div className="rounded bg-slate-800 text-white p-3 md:p-4 2xl:p-5 h-full min-h-[92px] md:min-h-[132px] 2xl:min-h-[172px] flex flex-col justify-center items-center shadow">
                            <div className="text-sm md:text-base opacity-75 mb-1">No. Antrian</div>
                            <div className="text-5xl md:text-6xl 2xl:text-7xl font-black tracking-wider leading-tight">{d.next_no_reg || '-'} </div>
                          </div>
                        </div>
                      </div>
                    </motion.div>
                  ))}
                </div>
              ) : (
                <div className="text-slate-600">Tidak ada data dokter.</div>
              )}
            </motion.div>
          ))}
        </div>
      </main>

      {/* Footer marquee */}
      <footer className="fixed bottom-0 left-0 right-0 bg-slate-800 py-2">
        <div className="overflow-hidden whitespace-nowrap">
          <div className="animate-marquee inline-block px-6">
            Selamat datang di {hospital?.name}. Pelayanan terbaik untuk Anda. {hospital?.kabupaten ? `Kab. ${hospital.kabupaten}` : ''} {hospital?.propinsi ? `- ${hospital.propinsi}` : ''}
          </div>
        </div>
      </footer>

      {/* Simple marquee animation */}
      <style>
        {`
        .animate-marquee {
          animation: marquee 20s linear infinite;
        }
        @keyframes marquee {
          0% { transform: translateX(0); }
          100% { transform: translateX(-100%); }
        }
        `}
      </style>
    </div>
  );
}

// Helper: remove 'dr.'/'dr'/'drg.' prefix from doctor's name for TTS
function sanitizeDoctorName(name) {
  if (!name) return '';
  return name.replace(/^\s*(drg?|dr)\.?[\s-]*/i, '').trim();
}

// Helper: remove 'Tn.'/'Mr.'/'Ny.'/'An.' prefix from patient name for TTS
function sanitizePatientName(name) {
  if (!name) return '';
  return name.replace(/^\s*(tn|mr|ny|an)\.?[\s-]*/i, '').trim();
}