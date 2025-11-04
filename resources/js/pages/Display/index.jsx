import React, { useEffect, useMemo, useRef, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import bellUrl from './assets/notifbell.mp3';
import nomorAntrianUrl from './assets/nomor antrian.mp3';

// Helper: fetch JSON with simple error handling
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

function classNames(...c) {
  return c.filter(Boolean).join(' ');
}

function formatRelativeTime(d) {
  if (!d) return '-';
  const seconds = Math.floor((Date.now() - d.getTime()) / 1000);
  if (seconds < 5) return 'baru saja';
  if (seconds < 60) return `${seconds} dtk lalu`;
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} mnt lalu`;
  const hours = Math.floor(minutes / 60);
  return `${hours} jam lalu`;
}

export default function LoketDisplay() {
  const now = useClock();

  // Audio & UI controls
  const [soundEnabled, setSoundEnabled] = useState(false); // Audio disabled (handled by reg_periksa)
  const [volume, setVolume] = useState(0.8);
  const audioRef = useRef(null);

  const [audioInitialized, setAudioInitialized] = useState(false); // Audio init disabled for Display
  const [audioPromptVisible, setAudioPromptVisible] = useState(false); // Prompt removed per request
  const pendingAudioPlayRef = useRef(null);
  const playingRef = useRef(false);
  const lastPlayTimeRef = useRef(0);
  const [audioError, setAudioError] = useState('');

  // Audio assets via Vite (glob)
  const nomorAudioMap = import.meta.glob('./assets/nomor/*.mp3', { eager: true, query: '?url', import: 'default' });
  const loketAudioMap = import.meta.glob('./assets/loket/*.mp3', { eager: true, query: '?url', import: 'default' });
  const menujuAudioMap = import.meta.glob('./assets/menuju/*.mp3', { eager: true, query: '?url', import: 'default' });

  const [isFullscreen, setIsFullscreen] = useState(true); // Default to fullscreen
  const [highContrast, setHighContrast] = useState(true); // Default to high contrast
  const [dimMode, setDimMode] = useState(false); // Keep dim mode off by default

  const [lastUpdatedAt, setLastUpdatedAt] = useState(null);
  const [loading, setLoading] = useState(true);
  const [errorCount, setErrorCount] = useState(0);

  const [callout, setCallout] = useState(null);

  useEffect(() => {
    audioRef.current = new Audio();
    audioRef.current.volume = volume;
  }, []);

  useEffect(() => {
    if (audioRef.current) audioRef.current.volume = volume;
  }, [volume]);

  // Debug function to test audio mapping
  function testAudioMapping() {
    console.log('[Audio Debug] Available nomor audio files:', Object.keys(nomorAudioMap));
    console.log('[Audio Debug] Available menuju audio files:', Object.keys(menujuAudioMap));
    console.log('[Audio Debug] Available loket audio files:', Object.keys(loketAudioMap));
    
    // Test number sequence generation
    const testNumbers = [1, 11, 23, 100, 1234];
    testNumbers.forEach(num => {
      const seq = buildNumberAudioSequence(num);
      console.log(`[Audio Debug] Number ${num} sequence:`, seq);
    });
  }

  // Call test function on component mount
  useEffect(() => {
    testAudioMapping();
  }, []);

  useEffect(() => {
    function onFs() {
      setIsFullscreen(!!document.fullscreenElement);
    }
    document.addEventListener('fullscreenchange', onFs);
    return () => document.removeEventListener('fullscreenchange', onFs);
  }, []);

  const toggleSound = () => {
    setSoundEnabled(prev => {
      const next = !prev;
      if (next) {
        initAudio();
      }
      return next;
    });
  };

  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen?.().catch(() => {});
    } else {
      document.exitFullscreen?.();
    }
  };

  function showAudioActivationPrompt() {
    setAudioPromptVisible(true);
  }

  async function initAudio() {
    try {
      // Create and properly initialize audio context with user gesture
      const a = audioRef.current || new Audio();
      audioRef.current = a;
      a.volume = volume;
      a.src = bellUrl;
      
      // Play a short silent audio to unlock audio context (required by browsers)
      try {
        a.muted = true;
        await a.play();
        a.pause();
        a.currentTime = 0;
        a.muted = false;
        console.debug('[Audio] Audio context unlocked with user gesture');
      } catch (playError) {
        console.warn('[Audio] Could not unlock audio context:', playError.message);
      }
      
      // Mark audio as initialized and hide prompt
      setAudioInitialized(true);
      setAudioPromptVisible(false);
      
      console.debug('[Audio] Audio initialized successfully with user interaction');
      
      // Audio playback removed on Display page per request; handled by reg_periksa.

    } catch (e) {
      console.error('Audio initialization failed:', e);
      setAudioInitialized(false);
      setAudioPromptVisible(true);
    }
  }

  function showError(msg) {
    setAudioError(msg || 'Terjadi kesalahan audio');
    setTimeout(() => setAudioError(''), 3000);
  }

  function getNomorUrl(name) {
    const url = nomorAudioMap[`./assets/nomor/${name}`] || null;
    console.debug('[Audio] getNomorUrl:', name, '→', url);
    return url;
  }
  function getLoketUrl(n) {
    const url = loketAudioMap[`./assets/loket/loket ${n}.mp3`] || null;
    console.debug('[Audio] getLoketUrl:', n, '→', url);
    return url;
  }
  function getMenujuUrl() {
    const url = menujuAudioMap['./assets/menuju/Silahkan ke.mp3'] || null;
    console.debug('[Audio] getMenujuUrl:', '→', url);
    return url;
  }

  function buildNumberAudioSequence(num) {
    const sequence = [];
    
    if (num === 0) {
      const url = getNomorUrl('0.mp3');
      if (url) sequence.push(url);
      return sequence;
    }

    if (num < 0 || num > 9999) {
      console.warn('Nomor di luar jangkauan (0-9999):', num);
      return sequence;
    }

    // Ribuan
    if (num >= 1000) {
      const ribuan = Math.floor(num / 1000);
      if (ribuan === 1) {
        const url = getNomorUrl('ribu.mp3');
        if (url) sequence.push(url);
      } else {
        const url = getNomorUrl(`${ribuan}.mp3`);
        if (url) sequence.push(url);
        const ribuUrl = getNomorUrl('ribu.mp3');
        if (ribuUrl) sequence.push(ribuUrl);
      }
      num = num % 1000;
    }

    // Ratusan
    if (num >= 100) {
      const ratusan = Math.floor(num / 100);
      if (ratusan === 1) {
        const url = getNomorUrl('ratus.mp3');
        if (url) sequence.push(url);
      } else {
        const url = getNomorUrl(`${ratusan}.mp3`);
        if (url) sequence.push(url);
        const ratusUrl = getNomorUrl('ratus.mp3');
        if (ratusUrl) sequence.push(ratusUrl);
      }
      num = num % 100;
    }

    // Puluhan dan satuan
    if (num >= 20) {
      const puluhan = Math.floor(num / 10) * 10;
      const url = getNomorUrl(`${puluhan}.mp3`);
      if (url) sequence.push(url);
      
      const satuan = num % 10;
      if (satuan > 0) {
        const satuanUrl = getNomorUrl(`${satuan}.mp3`);
        if (satuanUrl) sequence.push(satuanUrl);
      }
    } else if (num >= 12) {
      // 12-19: angka + "belas"
      const satuan = num - 10;
      const url = getNomorUrl(`${satuan}.mp3`);
      if (url) sequence.push(url);
      const belasUrl = getNomorUrl('belas.mp3');
      if (belasUrl) sequence.push(belasUrl);
    } else if (num === 11) {
      // Sebelas
      const url = getNomorUrl('sebelas.mp3');
      if (url) sequence.push(url);
    } else if (num === 10) {
      // Sepuluh
      const url = getNomorUrl('10.mp3');
      if (url) sequence.push(url);
    } else if (num > 0) {
      // 1-9
      const url = getNomorUrl(`${num}.mp3`);
      if (url) sequence.push(url);
    }

    return sequence;
  }

  async function playAudioFile(url) {
    if (!url) return Promise.resolve();
    return new Promise((resolve) => {
      try {
        const audio = audioRef.current || new Audio();
        audioRef.current = audio;
        // Ensure we fully reset between tracks for reliability across browsers
        try { audio.pause(); } catch (_) {}
        try { audio.currentTime = 0; } catch (_) {}
        audio.volume = volume;
        audio.src = url;
        // Force a load to avoid race conditions on some browsers
        try { audio.load(); } catch (_) {}

        console.debug('[Audio] start', url);

        const onEnded = () => {
          audio.onended = null;
          audio.onerror = null;
          console.debug('[Audio] ended', url);
          resolve();
        };
        const onError = (e) => {
          console.error('[Audio] error', url, e);
          audio.onended = null;
          audio.onerror = null;
          resolve();
        };
        audio.onended = onEnded;
        audio.onerror = onError;

        const pp = audio.play();
        if (pp && typeof pp.catch === 'function') {
          pp.catch((err) => {
            console.warn('[Audio] play blocked', url, err);
            // If autoplay is blocked due to browser policy, log it but don't reset audioInitialized
            // since user has already activated audio through the button
            if (err.name === 'NotAllowedError') {
              console.debug('[Audio] Autoplay blocked by browser policy, but audio was user-activated');
            }
            onError(err);
          });
        }
      } catch (err) {
        console.warn('Audio exception', err);
        resolve();
      }
    });
  }

  async function playAntrianNumber(n) {
    const seq = buildNumberAudioSequence(n);
    console.debug('[Audio] number sequence', n, seq);
    for (const url of seq) { // sequential
      await playAudioFile(url);
    }
  }

  async function playAntrianSound(data) {
    try {
      if (!soundEnabled) {
        console.debug('[Audio] Sound disabled, skipping');
        return;
      }
      if (!audioInitialized) {
        console.debug('[Audio] Audio not initialized, storing for later playback');
        pendingAudioPlayRef.current = data;
        return; // Don't show prompt, just wait for user to activate
      }
      
      // Add debounce to prevent duplicate calls within 2 seconds
      const now = Date.now();
      if (now - lastPlayTimeRef.current < 2000) {
        console.debug('[Audio] Debouncing duplicate call, skipping');
        return;
      }
      lastPlayTimeRef.current = now;
      
      if (playingRef.current) {
        console.debug('[Audio] Already playing, queuing latest');
        pendingAudioPlayRef.current = data; // queue latest
        return;
      }
      playingRef.current = true;

      const num = data?.no_reg ?? data?.nomor_antrian ?? data?.nomor ?? nextNomor;
      console.debug('[Audio] play sequence start for nomor', num, 'data=', data);

      console.debug('[Audio] Playing bell...');
      await playAudioFile(bellUrl);
      
      console.debug('[Audio] Playing nomor antrian...');
      await playAudioFile(nomorAntrianUrl);

      console.debug('[Audio] Playing number sequence...');
      await playAntrianNumber(Number(num));

      console.debug('[Audio] Playing menuju...');
      const menujuUrl = getMenujuUrl();
      if (menujuUrl) await playAudioFile(menujuUrl);

      // Loket audio dinonaktifkan sesuai permintaan
      // if (data?.loket) {
      //   const loketUrl = getLoketUrl(data.loket);
      //   if (loketUrl) await playAudioFile(loketUrl);
      // }

      console.debug('[Audio] Sequence completed');
      playingRef.current = false;
    } catch (error) {
      console.error('[Audio] Error in playAntrianSound:', error);
      playingRef.current = false;
      showError(error?.message || 'Gagal memutar antrian');
    }
  }

  // Header: Instansi
  const [hospital, setHospital] = useState({ name: 'Puskesmas', address: '', kabupaten: '', propinsi: '' });

  // Body: Antri Pendaftaran Loket (from antripendaftaran_nomor)
  const [nextNomor, setNextNomor] = useState(null);
  const [sisa, setSisa] = useState(0);
  const [stats, setStats] = useState({ total: 0, dipanggil: 0 });
  // Tambahan: nomor yang sedang dipanggil (untuk kartu besar & callout)
  const [calledNomor, setCalledNomor] = useState(null);

  // Media
  const [videoSrc, setVideoSrc] = useState(null);
  const [videoList, setVideoList] = useState([]);
  const [videoIndex, setVideoIndex] = useState(0);
  const videoRef = useRef(null);
  const videoTimerRef = useRef(null);
  const DEFAULT_FALLBACK_MS = 150000; // fallback 150 detik untuk memastikan video panjang tidak terpotong
  const FALLBACK_MARGIN_MS = 15000; // buffer 15 detik di atas durasi metadata untuk mencegah pemotongan karena durasi terdeteksi lebih pendek

  // Footer: antrian masing-masing poli (from /api/antrian-display)
  const [displayData, setDisplayData] = useState([]);

  const lastNextRef = useRef(null);
  const [highlightChange, setHighlightChange] = useState(false);
  // Ref untuk mendeteksi perubahan nomor dipanggil
  const lastCalledRef = useRef(null);

  // Fetch hospital info
  useEffect(() => {
    (async () => {
      const info = await getJSON('/api/setting/hospital-info');
      if (info && info.name) setHospital(info);
    })();
  }, []);

  // Fetch media files (video)
  useEffect(() => {
    (async () => {
      const media = await getJSON('/api/media-files');
      if (media) {
        let list = [];
        if (Array.isArray(media.video_urls) && media.video_urls.length > 0) {
          list = media.video_urls;
        } else if (media.video && media.video.length > 0) {
          const path = media.video_path || '';
          list = media.video.map(v => `${path}/${v}`);
        }
        if (list.length > 0) {
          setVideoList(list);
          setVideoIndex(0);
          setVideoSrc(list[0]);
        }
      }
    })();
  }, []);

  // Sinkronisasi src dengan playlist
  useEffect(() => {
    if (videoList.length > 0) {
      const nextSrc = videoList[videoIndex % videoList.length];
      setVideoSrc(nextSrc);
    }
  }, [videoList, videoIndex]);

  // Paksa reload dan play saat src berubah (menghindari berhenti di satu video)
  useEffect(() => {
    const el = videoRef.current;
    // Bersihkan timer fallback sebelumnya
    if (videoTimerRef.current) {
      clearTimeout(videoTimerRef.current);
      videoTimerRef.current = null;
    }
    if (el && videoSrc) {
      try {
        el.load();
        const p = el.play();
        if (p && typeof p.then === 'function') {
          p.catch(() => {
            // Jika autoplay ditolak, tetap lanjut manual setelah 100ms
            setTimeout(() => el.play().catch(() => {}), 100);
          });
        }
      } catch (_) {}
    }
    return () => {
      if (videoTimerRef.current) {
        clearTimeout(videoTimerRef.current);
        videoTimerRef.current = null;
      }
    };
  }, [videoSrc]);

  // Poll loket queue and display data periodically + track loading/error/last update
  useEffect(() => {
    let cancelled = false;
    async function loadAll() {
      setLoading(true);
      let hadError = false;
  
      const next = await getJSON('/api/antripendaftaran/next');
      if (!cancelled && next) {
        setNextNomor(next.nomor ?? null);
        setSisa(next.sisa ?? 0);
      } else {
        hadError = true;
      }
      const st = await getJSON('/api/antripendaftaran/stats');
      if (!cancelled && st) {
        setStats({ total: st.total ?? 0, dipanggil: st.dipanggil ?? 0 });
      } else {
        hadError = true;
      }
      // Ambil nomor yang sedang dipanggil dari antripendaftaran (status=2)
      const cur = await getJSON('/api/antripendaftaran/current');
      if (!cancelled && cur) {
        setCalledNomor(cur.nomor ?? null);
      }
      const disp = await getJSON('/api/antrian-display');
      if (!cancelled && disp && disp.antrian) {
        setDisplayData(disp.antrian || []);
        // Fallback jika belum ada nomor dipanggil dari antripendaftaran
        if (!cur?.nomor) {
          const called = disp?.dipanggil?.no_reg ?? null;
          setCalledNomor(called);
        }
      } else {
        hadError = true;
      }
  
      if (!cancelled) {
        setLoading(false);
        setLastUpdatedAt(new Date());
        setErrorCount(prev => (hadError ? prev + 1 : 0));
      }
    }
    loadAll();
    const id = setInterval(loadAll, 10000); // refresh every 10s
    return () => { cancelled = true; clearInterval(id); };
  }, []);

  // Detect calledNomor change for animation + callout overlay
  useEffect(() => {
    if (calledNomor && lastCalledRef.current !== calledNomor) {
      setHighlightChange(true);
      const t = setTimeout(() => setHighlightChange(false), 1200);
      lastCalledRef.current = calledNomor;

      // Tampilkan callout untuk nomor yang DIPANGGIL
      setCallout({ nomor: calledNomor, ts: Date.now() });
      const t2 = setTimeout(() => setCallout(null), 15000);

      return () => { clearTimeout(t); clearTimeout(t2); };
    }
  }, [calledNomor, soundEnabled]);

  // Group per poli: ambil nomor terkecil (menyerupai "next") dan jumlah menunggu
  const perPoli = useMemo(() => {
    const map = new Map();
    for (const row of displayData) {
      const nm = row.nm_poli || 'Poli';
      const status = row.status || row.stts || 'Menunggu';
      const noReg = parseInt(row.no_reg, 10);
      const entry = map.get(nm) || { nm_poli: nm, next_no: Infinity, waiting: 0 };
      if (status === 'Menunggu' || status === 'Belum') {
        entry.waiting += 1;
        if (!Number.isNaN(noReg)) entry.next_no = Math.min(entry.next_no, noReg);
      }
      map.set(nm, entry);
    }
    return Array.from(map.values())
      .map(e => ({ ...e, next_no: e.next_no === Infinity ? '-' : e.next_no }))
      .sort((a, b) => a.nm_poli.localeCompare(b.nm_poli));
  }, [displayData]);

  const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  const timeStr = now.toLocaleTimeString('id-ID');
  const neonColor = isFullscreen ? '#60a5fa' : '#fb7185';

  return (
    <div className={classNames(
      'min-h-screen w-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white flex flex-col',
      dimMode ? 'brightness-[0.9]' : '',
      highContrast ? 'contrast-125 saturate-125' : ''
    )}>
      {/* Header */}
      <motion.header
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="px-8 py-6 border-b border-white/10 bg-black/40 backdrop-blur flex items-center justify-between"
      >
        <div className="flex items-center gap-4">
          <div className="h-12 w-12 rounded-full bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center">
            <span className="text-emerald-300 font-bold">LK</span>
          </div>
          <div>
            <div className="text-2xl font-semibold tracking-wide">
              {hospital.name || 'Instansi'}
            </div>
            <div className="text-sm text-white/70">
              {hospital.address ? `${hospital.address} — ${hospital.kabupaten}, ${hospital.propinsi}` : 'Display Antrian Loket'}
            </div>
          </div>
        </div>
        <div className="text-right">
          <div className="text-sm text-white/70">{dateStr}</div>
          <div className="text-xl font-mono">{timeStr}</div>
          <div className="mt-2 flex items-center justify-end gap-2">
            <span className="text-xs text-white/60">Update {formatRelativeTime(lastUpdatedAt)}</span>
            {errorCount > 0 && (
              <span className="text-xs px-2 py-1 rounded bg-amber-500/20 border border-amber-400/60 text-amber-200">Gangguan jaringan</span>
            )}
          </div>
        </div>
      </motion.header>

      {/* Fullscreen toggle button */}
      <motion.button
        onClick={toggleFullscreen}
        initial={{ scale: 0.95, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.98 }}
        transition={{ duration: 0.3 }}
        aria-label={isFullscreen ? 'Nonaktifkan Fullscreen' : 'Aktifkan Fullscreen'}
        className="fixed top-24 right-6 z-50 pointer-events-auto rounded-2xl bg-black/40 border border-white/20 px-3 py-3 shadow-lg backdrop-blur"
        title={isFullscreen ? 'Keluar Fullscreen' : 'Masuk Fullscreen'}
      >
        <span className="relative flex items-center justify-center">
          <motion.span
            className="absolute -inset-1 rounded-2xl blur-md"
            style={{ background: isFullscreen ? 'conic-gradient(from 0deg, #22d3ee, #3b82f6, #6366f1, #22d3ee)' : 'conic-gradient(from 0deg, #f43f5e, #f97316, #ef4444, #f43f5e)', opacity: 0.6 }}
            animate={{ rotate: 360 }}
            transition={{ duration: 8, ease: 'linear', repeat: Infinity }}
          />
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" className="relative z-10" style={{ filter: `drop-shadow(0 0 6px ${neonColor})` }}>
            {/* Icon seperti "X" dengan empat panah menghadap ke tengah */}
            <polygon points="12,5 9,9 15,9" fill={neonColor} />
            <polygon points="19,12 15,9 15,15" fill={neonColor} />
            <polygon points="12,19 9,15 15,15" fill={neonColor} />
            <polygon points="5,12 9,9 9,15" fill={neonColor} />
          </svg>
        </span>
      </motion.button>

      {/* Body */}
      <div className="flex-1 grid grid-cols-12 gap-6 p-8">
        {/* Left: Nomor antrian */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.5 }}
          className="col-span-5 bg-black/40 backdrop-blur rounded-2xl p-6 border border-white/10"
        >
          <div className="mb-4 text-center">
            <div className="uppercase text-[clamp(1.5rem,2.8vw,3.25rem)] font-bold text-white/90 tracking-wide">Nomor Dipanggil</div>
          </div>
          {loading ? (
            <div className="w-full h-96 rounded-2xl animate-pulse bg-white/5 border border-white/10" />
          ) : (
            <AnimatePresence mode="wait">
              <motion.div
                key={calledNomor ?? 'none'}
                initial={{ scale: 0.9, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                exit={{ scale: 0.9, opacity: 0 }}
                transition={{ type: 'spring', stiffness: 200, damping: 18 }}
                className={classNames(
                  'w-full min-h-[clamp(18rem,32vh,36rem)] rounded-2xl flex items-center justify-center border',
                  highlightChange ? 'border-emerald-400/60 bg-emerald-500/10 shadow-lg shadow-emerald-500/20' : 'border-white/10 bg-white/5'
                )}
              >
                <span className="text-[clamp(6rem,10vw,22rem)] leading-[0.9] font-extrabold tracking-tight">
                  {calledNomor ?? '-'}
                </span>
              </motion.div>
            </AnimatePresence>
          )}
          <div className="mt-6 grid grid-cols-3 gap-4">
            <div className="bg-white/5 rounded-xl p-4 text-center">
              <div className="text-lg text-white/70">Nomor Berikutnya</div>
              <div className="text-[clamp(2rem,4vw,6rem)] font-extrabold">{nextNomor ?? '-'}</div>
            </div>
            <div className="bg-white/5 rounded-xl p-4 text-center">
              <div className="text-lg text-white/70">Sisa</div>
              <div className="text-[clamp(2rem,4vw,6rem)] font-extrabold">{sisa}</div>
            </div>
            <div className="bg-white/5 rounded-xl p-4 text-center">
              <div className="text-lg text-white/70">Total Hari Ini</div>
              <div className="text-[clamp(2rem,4vw,6rem)] font-extrabold">{stats.total}</div>
            </div>
          </div>
        </motion.div>

        {/* Right: Video */}
        <motion.div
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.5 }}
          className="col-span-7 bg-black/40 backdrop-blur rounded-2xl p-6 border border-white/10"
        >
          <div className="flex items-center justify-between mb-4">
            <div className="text-lg font-medium text-white/80">Informasi & Video</div>
            <div className="text-xs text-white/60">Auto play • Loop • Mute</div>
          </div>
          <div className="w-full aspect-video bg-white/5 rounded-xl overflow-hidden border border-white/10">
            {videoSrc ? (
              <video
                key={videoIndex}
                ref={videoRef}
                src={videoSrc}
                className="w-full h-full object-cover"
                autoPlay
                muted
                playsInline
                preload="auto"
                onLoadedData={(e) => {
                  const el = e.currentTarget;
                  el.play().catch(() => {});
                }}
                onLoadedMetadata={(e) => {
                  // Fallback: jika event ended tidak terpanggil, jadwalkan perpindahan berdasarkan durasi (+margin)
                  const el = e.currentTarget;
                  if (videoTimerRef.current) {
                    clearTimeout(videoTimerRef.current);
                    videoTimerRef.current = null;
                  }
                  const d = el.duration;
                  if (Number.isFinite(d) && d > 0 && videoList.length > 0) {
                    const fallbackTime = Math.max(DEFAULT_FALLBACK_MS, Math.ceil(d * 1000) + FALLBACK_MARGIN_MS);
                    videoTimerRef.current = setTimeout(() => {
                      setVideoIndex(i => (i + 1) % videoList.length);
                    }, fallbackTime);
                  } else if (videoList.length > 0) {
                    videoTimerRef.current = setTimeout(() => {
                      setVideoIndex(i => (i + 1) % videoList.length);
                    }, DEFAULT_FALLBACK_MS);
                  }
                }}
                onEnded={() => {
                  // Pastikan timer fallback dibersihkan agar tidak memotong video berikutnya
                  if (videoTimerRef.current) {
                    clearTimeout(videoTimerRef.current);
                    videoTimerRef.current = null;
                  }
                  if (videoList.length > 0) {
                    setVideoIndex(i => (i + 1) % videoList.length);
                  }
                }}
                onError={() => {
                  // Jika ada error pada video ini, bersihkan timer dan lompat ke berikutnya agar playlist tidak macet
                  if (videoTimerRef.current) {
                    clearTimeout(videoTimerRef.current);
                    videoTimerRef.current = null;
                  }
                  if (videoList.length > 0) {
                    setVideoIndex(i => (i + 1) % videoList.length);
                  }
                }}
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-white/60">
                Tidak ada video. Silakan taruh file .mp4 di public/assets/video
              </div>
            )}
          </div>
        </motion.div>
      </div>

      {/* Callout overlay */}
      <AnimatePresence>
        {callout && (
          <motion.div
            key={callout.ts}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.25 }}
            className="fixed inset-0 z-50 flex items-center justify-center pointer-events-none"
          >
            <motion.div
              initial={{ opacity: 0, scale: 0.92, y: 20, filter: 'blur(8px)' }}
              animate={{ opacity: 1, scale: 1, y: 0, filter: 'blur(0px)' }}
              exit={{ opacity: 0, scale: 0.95, y: -20, filter: 'blur(6px)' }}
              transition={{ type: 'spring', stiffness: 220, damping: 22 }}
              className="px-10 py-6 rounded-3xl bg-gradient-to-br from-violet-500/40 via-fuchsia-500/35 to-rose-500/40 border border-rose-300/60 backdrop-blur-xl text-white shadow-2xl"
            >
              <motion.span
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -10 }}
                transition={{ duration: 0.3, delay: 0.1 }}
                className="mr-4 text-[clamp(1.25rem,2vw,2rem)]"
              >
                Nomor
              </motion.span>
              <motion.span
                 initial={{ scale: 0.9, opacity: 0 }}
                 animate={{ scale: [1, 1.04, 1], opacity: 1 }}
                 exit={{ scale: 0.95, opacity: 0 }}
                 transition={{ duration: 1.8, ease: 'easeInOut', repeat: Infinity, delay: 0.15 }}
                 className="text-[clamp(6rem,10vw,18rem)] leading-[0.9] font-extrabold"
               >
                 {callout.nomor}
               </motion.span>
              <motion.span
                initial={{ opacity: 0, x: 10 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: 10 }}
                transition={{ duration: 0.3, delay: 0.2 }}
                className="ml-4 text-[clamp(1rem,1.8vw,1.5rem)] text-white/80"
              >
                Silakan ke Meja Pendaftaran
              </motion.span>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Audio activation prompt removed per request */}

      {/* Footer: per poli */}
      <motion.footer
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="px-8 py-4 bg-black/60 border-t border-white/10"
      >
        {/* Per-poli footer card hidden per request */}
        {/* Removed: Nomor Antrian per Poli grid and placeholder message */}

        {/* Marquee informasi puskesmas */}
        <div className="mt-4 overflow-hidden">
          <motion.div
            initial={{ x: '0%' }}
            animate={{ x: ['0%', '-100%'] }}
            transition={{ repeat: Infinity, duration: 20, ease: 'linear' }}
            className="whitespace-nowrap text-white/80 text-[clamp(1rem,1.8vw,1.75rem)] font-semibold"
          >
            <span className="mr-8">{hospital.name} • {hospital.address}</span>
            <span className="mr-8">Kab. {hospital.kabupaten}, Prov. {hospital.propinsi}</span>
            <span className="mr-8">Informasi Loket: Mohon siapkan berkas sebelum dipanggil</span>
            <span className="mr-8">Terima kasih atas kedisiplinan Anda</span>
            {/* Duplicate content to make the marquee seamless */}
            <span className="mr-8">{hospital.name} • {hospital.address}</span>
            <span className="mr-8">Kab. {hospital.kabupaten}, Prov. {hospital.propinsi}</span>
            <span className="mr-8">Informasi Loket: Mohon siapkan berkas sebelum dipanggil</span>
            <span className="mr-8">Terima kasih atas kedisiplinan Anda</span>
          </motion.div>
        </div>
      </motion.footer>
    </div>
  );
}