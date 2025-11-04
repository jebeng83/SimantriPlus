import React, { useState } from 'react';
import { Head, useForm, usePage, router, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import AppLayout from '../../Layouts/AppLayout.jsx';

const PageHeader = () => (
  <div className="relative overflow-hidden rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-600 p-6 text-white shadow-lg">
    <div className="relative z-10">
      <h1 className="text-2xl font-bold tracking-tight">Konversi Satuan</h1>
      <p className="mt-1 opacity-90">::[ Data Konversi Satuan ]::</p>
    </div>
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 0.2 }} className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white" />
    <motion.div initial={{ opacity: 0 }} animate={{ opacity: 0.15 }} className="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-white" />
  </div>
);

export default function KonversiSatuanPage() {
  const { props } = usePage();
  const { items, satuanOptions = [], flash } = props;

  const [isEdit, setIsEdit] = useState(false);
  const [selected, setSelected] = useState(null);

  const { data, setData, post, processing, errors, reset } = useForm({
    kode_sat: '',
    nilai: '',
    sat_konversi: '',
    nilai_konversi: ''
  });

  const swapUnits = () => {
    setData({
      kode_sat: data.sat_konversi || '',
      sat_konversi: data.kode_sat || '',
      nilai: data.nilai_konversi || '',
      nilai_konversi: data.nilai || ''
    });
  };

  const submitForm = (e) => {
    e.preventDefault();
    if (isEdit && selected) {
      const params = {
        kode_sat: selected.kode_sat,
        sat_konversi: selected.sat_konversi,
        nilai: selected.nilai,
        nilai_konversi: selected.nilai_konversi,
      };
      router.post(route('farmasi.konversi-satuan.update', params), { ...data, _method: 'PUT' }, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
          setIsEdit(false);
          setSelected(null);
          reset();
        }
      });
    } else {
      post(route('farmasi.konversi-satuan.store'), {
        preserveScroll: true,
        onSuccess: () => reset()
      });
    }
  };

  const startEdit = (item) => {
    setIsEdit(true);
    setSelected(item);
    setData({
      kode_sat: item.kode_sat,
      nilai: item.nilai,
      sat_konversi: item.sat_konversi,
      nilai_konversi: item.nilai_konversi,
    });
  };

  const deleteItem = (item) => {
    const params = {
      kode_sat: item.kode_sat,
      sat_konversi: item.sat_konversi,
      nilai: item.nilai,
      nilai_konversi: item.nilai_konversi,
    };
    router.delete(route('farmasi.konversi-satuan.destroy', params), {
      preserveScroll: true,
    });
  };

  return (
    <div>
      <Head title="Konversi Satuan" />
      <div className="space-y-6">
        <PageHeader />

        {flash?.success && (
          <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} className="rounded-lg bg-green-50 p-3 text-sm text-green-800">
            {flash.success}
          </motion.div>
        )}

        <div className="rounded-xl bg-white p-4 shadow-sm">
          <form onSubmit={submitForm} className="flex flex-wrap items-center gap-2">
            <input
              type="number"
              value={data.nilai}
              onChange={(e) => setData('nilai', e.target.value)}
              placeholder="Nilai 1"
              className="h-11 w-24 rounded-full border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-sky-300"
            />
            <select
              value={data.kode_sat}
              onChange={(e) => setData('kode_sat', e.target.value.toUpperCase())}
              className="h-11 w-40 rounded-full border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-sky-300"
            >
              <option value="">-</option>
              {satuanOptions.map((s) => (
                <option key={s.kode_sat} value={s.kode_sat}>{s.kode_sat} — {s.satuan}</option>
              ))}
            </select>

            <button type="button" onClick={swapUnits} title="Tukar" className="h-10 w-10 rounded-lg bg-red-600 text-white shadow hover:bg-red-700">
              ⇅
            </button>

            <input
              type="number"
              value={data.nilai_konversi}
              onChange={(e) => setData('nilai_konversi', e.target.value)}
              placeholder="Nilai 2"
              className="h-11 w-24 rounded-full border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-sky-300"
            />
            <select
              value={data.sat_konversi}
              onChange={(e) => setData('sat_konversi', e.target.value.toUpperCase())}
              className="h-11 w-40 rounded-full border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-sky-300"
            >
              <option value="">-</option>
              {satuanOptions.map((s) => (
                <option key={s.kode_sat} value={s.kode_sat}>{s.kode_sat} — {s.satuan}</option>
              ))}
            </select>

            <button type="submit" disabled={processing} className="ml-auto rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
              {isEdit ? 'Simpan Perubahan' : 'Simpan'}
            </button>
          </form>

          {(errors?.kode_sat || errors?.sat_konversi || errors?.nilai || errors?.nilai_konversi) && (
            <div className="mt-2 text-xs text-red-600">
              {errors?.kode_sat && <div>Kode Satuan Asal: {errors.kode_sat}</div>}
              {errors?.sat_konversi && <div>Kode Satuan Konversi: {errors.sat_konversi}</div>}
              {errors?.nilai && <div>Nilai 1: {errors.nilai}</div>}
              {errors?.nilai_konversi && <div>Nilai 2: {errors.nilai_konversi}</div>}
            </div>
          )}

          <div className="mt-5 overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead>
                <tr className="bg-gray-50">
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Nilai 1</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Satuan 1</th>
                  <th className="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-600">=</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Nilai 2</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Satuan 2</th>
                  <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {items?.data?.length ? items.data.map((item) => (
                  <tr key={`${item.kode_sat}-${item.sat_konversi}-${item.nilai}-${item.nilai_konversi}`} className="hover:bg-gray-50">
                    <td className="px-4 py-2 text-sm">{item.nilai}</td>
                    <td className="px-4 py-2 text-sm">{item.kode_sat}</td>
                    <td className="px-4 py-2 text-center text-sm">=</td>
                    <td className="px-4 py-2 text-sm">{item.nilai_konversi}</td>
                    <td className="px-4 py-2 text-sm">{item.sat_konversi}</td>
                    <td className="px-4 py-2 text-right">
                      <div className="flex justify-end gap-2">
                        <button onClick={() => startEdit(item)} className="rounded-lg border border-sky-200 px-3 py-1.5 text-xs font-medium text-sky-700 hover:bg-sky-50">Edit</button>
                        <button onClick={() => deleteItem(item)} className="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Hapus</button>
                      </div>
                    </td>
                  </tr>
                )) : (
                  <tr>
                    <td colSpan={6} className="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {items?.links && (
            <div className="mt-4 flex flex-wrap items-center justify-between gap-2">
              <p className="text-xs text-gray-600">Menampilkan {items?.from ?? 0}-{items?.to ?? 0} dari {items?.total ?? 0} data</p>
              <div className="flex flex-wrap gap-2">
                {items.links.map((link, idx) => (
                  <Link key={idx} href={link.url || '#'} preserveState replace className={`rounded-lg px-3 py-1.5 text-xs ${link.active ? 'bg-sky-600 text-white' : 'bg-white text-gray-700 border'} ${!link.url ? 'pointer-events-none opacity-50' : ''}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

KonversiSatuanPage.layout = (page) => <AppLayout title="Farmasi" children={page} />;