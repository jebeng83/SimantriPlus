import React, { useMemo, useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';


// Helper: determine if a link is active based on current pathname
const isActive = (url) => {
  if (!url) return false;
  try {
    const current = window.location.pathname.replace(/\/$/, '');
    const target = url.replace(/\/$/, '');
    return current === target || current.startsWith(target + '/');
  } catch (e) {
    return false;
  }
};

const Icon = ({ name, className = '' }) => (
  <i className={`${name ?? 'fas fa-circle'} ${className}`}></i>
);

const MenuItem = ({ item, collapsed, idx }) => {
  const [open, setOpen] = useState(false);
  const active = isActive(item.url);

  // Micro-interactions saat klik & animasi submenu (inspirasi dari variants + stagger)
  const framerTapLink = { whileTap: { scale: 0.99 } };
  const framerTapButton = { whileTap: { scale: 0.99 } };
  const subListVariants = {
    open: { transition: { staggerChildren: 0.045, delayChildren: 0.05 } },
    closed: { transition: { staggerChildren: 0.03, staggerDirection: -1 } },
  };
  const subItemVariants = {
    open: { opacity: 1, x: 0, transition: { type: 'tween', duration: 0.16, ease: [0.22, 1, 0.36, 1] } },
    closed: { opacity: 0, x: -6, transition: { type: 'tween', duration: 0.14, ease: [0.22, 1, 0.36, 1] } },
  };

  // Non-sidebar/search/topnav items tidak ditampilkan di Sidebar
  if (item?.type === 'sidebar-menu-search' || item?.topnav_right) return null;

  // Header section
  if (item?.type === 'header') {
    return (
      <div className="uppercase text-xs tracking-wider text-slate-400 px-3 mt-6 mb-2">
        {item.text}
      </div>
    );
  }

  const prefetch = (url) => {
    try {
      if (!url || url === '#' || typeof url !== 'string') return;
      const pathname = new URL(url, window.location.origin).pathname.replace(/\/$/, '');
      // Hindari prefetch untuk halaman berat/sensitif agar tidak memicu 503 saat hover.
      if (pathname === '/reg-periksa') return;
      const exists = document.querySelector(`link[rel="prefetch"][href="${url}"]`);
      if (exists) return;
      const link = document.createElement('link');
      link.rel = 'prefetch';
      link.href = url;
      link.as = 'document';
      document.head.appendChild(link);
    } catch (e) {}
  };

  // Leaf link (underline merah sepanjang teks & highlight aktif lebih menyala, tanpa hover warna)
  if (!item.submenu || item.submenu.length === 0) {
    return (
      <motion.a
        {...framerTapLink}
        href={item.url || '#'}
        style={{ textDecoration: 'none' }}
        className={`group relative flex items-center gap-3 w-full px-3 py-2 rounded-md text-sm ${
          active
            ? 'bg-slate-800/90 text-white font-semibold ring-1 ring-red-500/25'
            : 'text-slate-300'
        }`}
        onMouseEnter={() => prefetch(item.url)}
        onFocus={() => prefetch(item.url)}
        onClick={(e) => {
          const url = item.url || '#';
          const blank = item.target === '_blank';
          // Biarkan default untuk ctrl/cmd/shift/middle click atau target _blank
          if (!url || url === '#' || blank || e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1) return;
          e.preventDefault();
          // Navigasi langsung tanpa efek gerak/fade-out
          window.location.href = url;
        }}
        // Haluskan perubahan background aktif
        animate={{ backgroundColor: active ? 'rgba(30,41,59,0.90)' : 'rgba(0,0,0,0)' }}
        transition={{ ease: [0.22, 1, 0.36, 1], duration: 0.18 }}
      >
        <motion.div {...framerIcon} transition={{ type: 'spring', stiffness: 220, damping: 20 }}>
          <Icon name={item.icon} className={`w-5 h-5 ${active ? 'text-white' : 'text-slate-400'}`} />
        </motion.div>
        {!collapsed && (
          <span className="flex-1 overflow-hidden">
            <motion.span
              {...framerText(idx)}
              transition={{ type: 'tween', duration: 0.18, ease: [0.22, 1, 0.36, 1] }}
              className={`relative inline-block truncate after:content-[''] after:absolute after:left-0 after:bottom-0 after:h-0 after:border-b-2 ${
                active
                  ? 'after:border-transparent'
                  : 'after:border-transparent group-hover:after:border-red-500 focus:after:border-red-500 active:after:border-red-500'
              }`}
            >
              {item.text}
              {/* underline aktif dengan animasi lebar */}
              <motion.span
                className="absolute left-0 bottom-0 h-[2px] bg-red-500"
                initial={{ width: 0 }}
                animate={{ width: active ? '100%' : 0 }}
                transition={{ type: 'tween', duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
              />
            </motion.span>
          </span>
        )}
        {!collapsed && active && (
          <span className="ml-auto inline-block w-1.5 h-1.5 rounded-full bg-emerald-400" />
        )}
      </motion.a>
    );
  }

  // Parent with submenu (tanpa animasi warna hover; animasi buka/tutup halus + stagger)
  return (
    <div className="px-1">
      <motion.button
        type="button"
        {...framerTapButton}
        onClick={() => setOpen((v) => !v)}
        className={`w-full flex items-center gap-3 px-2.5 py-2 rounded-md text-sm ${
          open ? 'bg-slate-800/60 text-white' : 'text-slate-300'
        }`}
        animate={{ backgroundColor: open ? 'rgba(30,41,59,0.60)' : 'rgba(0,0,0,0)' }}
        transition={{ ease: [0.22, 1, 0.36, 1], duration: 0.18 }}
      >
        <motion.div {...framerIcon} transition={{ type: 'spring', stiffness: 220, damping: 22 }}>
          <Icon name={item.icon} className={`w-5 h-5 ${open ? 'text-white' : 'text-slate-400'}`} />
        </motion.div>
        {!collapsed && (
          <motion.span {...framerText(idx)} transition={{ type: 'tween', duration: 0.18, ease: [0.22, 1, 0.36, 1] }} className="flex-1 text-left truncate">
            {item.text}
          </motion.span>
        )}
        {!collapsed && (
          <i className={`fas fa-chevron-${open ? 'up' : 'down'} text-xs text-slate-400`} />
        )}
      </motion.button>

      <AnimatePresence initial={false}>
        {open && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: 'auto', opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ type: 'tween', duration: 0.16 }}
            className={`mt-1 ${collapsed ? 'hidden' : ''}`}
          >
            <motion.div
              className="border-l border-slate-700/60 ml-4"
              variants={subListVariants}
              initial="closed"
              animate="open"
              exit="closed"
            >
              {item.submenu.map((child, idx) => (
                <motion.div key={idx} variants={subItemVariants}>
                  <MenuItem item={child} collapsed={collapsed} idx={idx} />
                </motion.div>
              ))}
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

// Framer configs ala tutorial (tanpa warna hover)
const framerSidebarBackground = {
  initial: { opacity: 0 },
  animate: { opacity: 1 },
  exit: { opacity: 0, transition: { delay: 0.2 } },
  transition: { duration: 0.3 },
};

const framerSidebarPanel = {
  initial: { x: '-100%' },
  animate: { x: 0 },
  exit: { x: '-100%' },
  transition: { duration: 0.3 },
};

const framerText = (delay) => ({
  initial: { opacity: 0, x: -50 },
  animate: { opacity: 1, x: 0 },
  transition: { delay: 0.5 + delay / 10 },
});

const framerIcon = {
  initial: { scale: 0 },
  animate: { scale: 1 },
  transition: { type: 'spring', stiffness: 260, damping: 20, delay: 1.5 },
};

const flattenLeafMenu = (menu) => {
  const items = [];
  const walk = (arr) => {
    (arr || []).forEach((i) => {
      if (!i) return;
      if (i.type === 'header' || i.type === 'sidebar-menu-search' || i.topnav_right) return;
      if (i.submenu && i.submenu.length) walk(i.submenu);
      else items.push({ title: i.text, url: i.url || '#', icon: i.icon, target: i.target });
    });
  };
  walk(menu);
  return items;
};

export default function Sidebar({ menu = [] }) {
  const [collapsed, setCollapsed] = useState(false);

  useEffect(() => {
    // Deteksi class body sidebar-collapse untuk mengetahui state collapsed
    const observer = new MutationObserver(() => {
      setCollapsed(document.body.classList.contains('sidebar-collapse'));
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    setCollapsed(document.body.classList.contains('sidebar-collapse'));
    return () => observer.disconnect();
  }, []);


  const handleToggle = () => {
    const $ = window.$ || window.jQuery;
    if ($ && $.fn && $.fn.PushMenu) {
      try {
        $('[data-widget="pushmenu"]').PushMenu('toggle');
        return;
      } catch (e) {
        console.warn('PushMenu toggle failed, fallback to body class toggle', e);
      }
    }
    document.body.classList.toggle('sidebar-collapse');
  };

  return (
    <motion.div
      className={`flex flex-col h-full overflow-hidden`}
      animate={{ width: collapsed ? 74 : 256 }}
      initial={false}
      transition={{ type: 'spring', stiffness: 280, damping: 28 }}
    >
      {/* Hapus tombol overlay agar tidak ada dua menu di Sidebar */}
      {/* (Sebelumnya ada tombol "Open Menu" untuk overlay) */}

      {/* Menu utama Sidebar (tetap satu) */}
      <nav className="mt-2 space-y-1">
        {menu.map((item, idx) => (
          <MenuItem key={idx} item={item} collapsed={collapsed} idx={idx} />
        ))}
      </nav>

      {/* Footer small info */}
      {!collapsed && (
        <div className="mt-auto text-xs text-slate-500 px-3 py-2">
          <div className="flex items-center gap-2">
            <i className="fas fa-magic"></i>
            <span>UI powered by MisterMaster</span>
          </div>
        </div>
      )}
    </motion.div>
  );
}
