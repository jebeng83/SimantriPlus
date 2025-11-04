import React, { useEffect, useState } from 'react';

export default function DataBarangSelector({ value, onChange, className = '' }) {
  const [items, setItems] = useState([]);
  const [q, setQ] = useState('');
  const [loading, setLoading] = useState(false);

  const search = (query) => {
    setLoading(true);
    const url = `/data-obat?q=${encodeURIComponent(query || '')}`;
    fetch(url, { headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((json) => {
        setItems(Array.isArray(json?.items) ? json.items : []);
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    search('');
  }, []);

  const handleSelect = (kode) => {
    if (onChange) onChange(kode);
  };

  return (
    <div className={`space-y-2 ${className}`}>
      <div className="flex gap-2">
        <input
          type="text"
          placeholder="Search..."
          value={q}
          onChange={(e) => setQ(e.target.value)}
          className="flex-1 rounded-lg border px-3 py-2 text-sm shadow-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300"
        />
        <button
          type="button"
          onClick={() => search(q)}
          className="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          {loading ? '...' : 'Search'}
        </button>
      </div>
      <select
        value={value || ''}
        onChange={(e) => handleSelect(e.target.value)}
        className="w-full rounded-lg border px-3 py-2 text-sm shadow-sm border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-300"
      >
        <option value="">{loading ? 'Loading...' : 'Pilih barang...'}</option>
        {items.map((it) => (
          <option key={it.kode_brng} value={it.kode_brng}>
            {it.kode_brng} - {it.nama_brng}
          </option>
        ))}
      </select>
    </div>
  );
}