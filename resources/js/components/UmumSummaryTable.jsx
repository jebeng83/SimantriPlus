import React from 'react';

export default function UmumSummaryTable({ penjualanUmum }) {
  const rows = [
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

  return (
    <div className="mt-8 border rounded overflow-hidden">
      <div className="px-4 py-3 bg-gray-50 border-b">
        <h4 className="text-sm font-medium text-gray-700">Ringkasan Pengaturan Harga Umum</h4>
      </div>
      <table className="min-w-full text-sm">
        <thead className="bg-gray-100">
          <tr>
            <th className="px-3 py-2 text-left">Kategori</th>
            <th className="px-3 py-2 text-right">Persentase</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((r) => {
            const val = penjualanUmum && typeof penjualanUmum[r.key] !== 'undefined' ? penjualanUmum[r.key] : null;
            return (
              <tr key={r.key} className="border-t">
                <td className="px-3 py-2">{r.label}</td>
                <td className="px-3 py-2 text-right">{val !== null && val !== undefined ? `${val}%` : '-'}</td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}