import React from 'react';

export default function JenisSummaryTable({ items = [], onSelect, onDelete }) {
  const columns = [
    { key: 'ralan', label: 'Rawat Jalan' },
    { key: 'kelas1', label: 'Kelas 1' },
    { key: 'kelas2', label: 'Kelas 2' },
    { key: 'kelas3', label: 'Kelas 3' },
    { key: 'utama', label: 'Kelas Utama' },
    { key: 'vip', label: 'VIP' },
    { key: 'vvip', label: 'VVIP' },
    { key: 'beliluar', label: 'Beli Luar' },
    { key: 'jualbebas', label: 'Jual Bebas' },
    { key: 'karyawan', label: 'Karyawan' },
  ];

  const hasData = Array.isArray(items) && items.length > 0;

  return (
    <div className="mt-6 border rounded overflow-hidden">
      <div className="px-4 py-3 bg-gray-50 border-b flex items-center justify-between">
        <h4 className="text-sm font-medium text-gray-700">Ringkasan Pengaturan Per Jenis</h4>
        {!hasData && <span className="text-xs text-gray-500">Belum ada pengaturan per jenis</span>}
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm">
          <thead className="bg-gray-100">
            <tr>
              <th className="px-3 py-2 text-left">Kode Jenis</th>
              {columns.map((c) => (
                <th key={c.key} className="px-3 py-2 text-right">{c.label}</th>
              ))}
              <th className="px-3 py-2 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {hasData ? (
              items.map((row) => (
                <tr key={row.kdjns} className="border-t">
                  <td className="px-3 py-2">{String(row.kdjns || '').toUpperCase()}</td>
                  {columns.map((c) => (
                    <td key={c.key} className="px-3 py-2 text-right">{row[c.key] !== undefined && row[c.key] !== null ? `${row[c.key]}%` : '-'}</td>
                  ))}
                  <td className="px-3 py-2">
                    <div className="flex gap-2">
                      <button type="button" className="px-2 py-1 text-xs rounded bg-indigo-600 text-white" onClick={() => onSelect && onSelect(row)}>Pilih</button>
                      <button type="button" className="px-2 py-1 text-xs rounded bg-red-600 text-white" onClick={() => onDelete && onDelete(row)}>Hapus</button>
                    </div>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td className="px-3 py-2 text-center text-gray-500" colSpan={columns.length + 2}>Tidak ada data</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}