import React, { useEffect, useState } from 'react';

export default function JenisSelector({ value, onChange, className = '' }) {
  const [options, setOptions] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    let active = true;
    setLoading(true);
    fetch('/data-obat/dropdowns', { headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((json) => {
        if (!active) return;
        const jenis = Array.isArray(json?.jenis) ? json.jenis : [];
        setOptions(jenis);
      })
      .catch(() => {})
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  return (
    <select
      value={value || ''}
      onChange={(e) => onChange && onChange(e.target.value)}
      className={`rounded-lg border px-3 py-2 text-sm shadow-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300 ${className}`}
    >
      <option value="">{loading ? 'Loading...' : 'Pilih jenis...'}</option>
      {options.map((opt) => (
        <option key={opt.kdjns} value={opt.kdjns}>
          {opt.kdjns} - {opt.nama}
        </option>
      ))}
    </select>
  );
}