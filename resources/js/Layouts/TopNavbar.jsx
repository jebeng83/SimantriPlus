import React, { useMemo, useState } from 'react';
import { motion } from 'framer-motion';

// Utility to normalize AdminLTE menu item to a simple shape
function normalizeItem(item) {
  if (!item || typeof item !== 'object') return null;
  const text = item.text || item.header || item.title || '';
  const url = item.url || item.href || item.route || '#';
  const icon = item.icon || item.icon_class || 'fas fa-circle';
  const target = item.target || (item.href_target || null);
  const submenu = Array.isArray(item.submenu) ? item.submenu : [];
  const active = !!item.active;
  return { text, url, icon, target, submenu, active };
}

function LeftItems({ items }) {
  const normalized = useMemo(() => items.map(normalizeItem).filter(Boolean), [items]);
  return (
    <ul className="flex items-center gap-1 md:gap-2">
      {normalized.map((it, idx) => (
        <li key={idx}>
          {it.submenu && it.submenu.length > 0 ? (
            <DropdownItem item={it} />
          ) : (
            <NavItem item={it} />
          )}
        </li>
      ))}
    </ul>
  );
}

function RightItems({ items }) {
  const normalized = useMemo(() => items.map(normalizeItem).filter(Boolean), [items]);
  return (
    <ul className="flex items-center gap-1 md:gap-2">
      {normalized.map((it, idx) => (
        <li key={idx}>
          {it.submenu && it.submenu.length > 0 ? (
            <DropdownItem item={it} alignRight />
          ) : (
            <NavItem item={it} />
          )}
        </li>
      ))}
    </ul>
  );
}

function IconFA({ name, className = '' }) {
  const cls = `${name} ${className}`.trim();
  return <i className={cls} aria-hidden="true" />;
}

function NavItem({ item }) {
  const { text, url, target, icon, active } = item;
  return (
    <motion.a
      href={url || '#'}
      target={target || undefined}
      style={{ textDecoration: 'none' }}
      className={`group inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors border-b border-transparent hover:border-red-500 hover:border-b-2 focus:border-red-500 focus:border-b-2 active:border-red-500 active:border-b-2 ${
        active ? 'text-white' : 'text-slate-200 hover:text-white'
      }`}
      initial={{ opacity: 0, y: -4 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.18 }}
    >
      <IconFA name={icon} className="mr-2 text-slate-300 group-hover:text-white" />
      <span className="hidden sm:inline">{text}</span>
    </motion.a>
  );
}

function DropdownItem({ item, alignRight = false }) {
  const { text, icon, submenu = [] } = item;
  const [open, setOpen] = useState(false);
  const normalizedSub = useMemo(() => submenu.map(normalizeItem).filter(Boolean), [submenu]);
  return (
    <div className="relative">
      <motion.button
        type="button"
        className="group inline-flex items-center rounded-md px-3 py-2 text-sm font-medium text-slate-200 hover:text-white"
        onClick={() => setOpen((v) => !v)}
        initial={{ opacity: 0, y: -4 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.18 }}
      >
        <IconFA name={icon} className="mr-2 text-slate-300 group-hover:text-white" />
        <span className="hidden sm:inline">{text}</span>
        <IconFA name={open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'} className="ml-2 text-slate-400" />
      </motion.button>
      {open && (
        <motion.ul
          className={`absolute z-50 min-w-[12rem] rounded-md border border-slate-700/30 bg-slate-900/80 backdrop-blur p-2 shadow-xl ${
            alignRight ? 'right-0' : 'left-0'
          }`}
          initial={{ opacity: 0, y: -6 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.2 }}
        >
          {normalizedSub.map((sub, idx) => (
            <li key={idx}>
              <a
                href={sub.url || '#'}
                target={sub.target || undefined}
                style={{ textDecoration: 'none' }}
                className="flex items-center gap-2 rounded px-3 py-2 text-sm text-slate-200 hover:bg-slate-800/80 hover:text-white border-b border-transparent hover:border-red-500 hover:border-b-2 focus:border-red-500 focus:border-b-2 active:border-red-500 active:border-b-2"
              >
                <IconFA name={sub.icon} className="text-slate-300" />
                <span>{sub.text}</span>
              </a>
            </li>
          ))}
        </motion.ul>
      )}
    </div>
  );
}

export default function TopNavbar({ left = [], right = [], brand = null }) {
  return (
    <motion.div
      className="flex w-full items-center justify-between gap-2"
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ duration: 0.15 }}
    >
      {/* Left section: brand + PushMenu toggler + left items */}
      <div className="flex items-center gap-2 md:gap-3">
        {/* AdminLTE Pushmenu toggle (opens left sidebar on small screens) */}
        <button
          type="button"
          className="inline-flex items-center justify-center rounded-md px-2 py-2 text-slate-200 hover:text-white"
          data-widget="pushmenu"
          aria-label="Toggle sidebar"
          title="Toggle sidebar"
        >
          <IconFA name="fas fa-bars" />
        </button>
        <LeftItems items={left} />
      </div>

      {/* Right section */}
      <div className="flex items-center gap-2 md:gap-3">
        <RightItems items={right} />
      </div>
    </motion.div>
  );
}