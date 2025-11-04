import React, { useEffect, useState } from 'react';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import AppLayout from '../../Layouts/AppLayout.jsx';

const PageHeader = () => (
  <div className="relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-lg">
    <div className="relative z-10">
      <h1 className="text-2xl font-bold tracking-tight">Industri Farmasi</h1>
      <p className="mt-1 opacity-90">Kelola data industri farmasi (pabrik/industri) secara cepat dan mudah.</p>
    </div>
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 0.2 }}
      className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white"
    />
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 0.15 }}
      className="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-white"
    />
  </div>
);

const Input = ({ label, id, type = 'text', value, onChange, maxLength, placeholder, disabled, error }) => (
  <div className="space-y-1">
    <label htmlFor={id} className="block text-sm font-medium text-gray-700">{label}</label>
    <input
      id={id}
      type={type}
      value={value}
      onChange={onChange}
      maxLength={maxLength}
      placeholder={placeholder}
      disabled={disabled}
      className={`w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 disabled:bg-gray-100 disabled:text-gray-500 ${error ? 'border-red-400 focus:ring-red-300' : 'border-gray-300 focus:ring-indigo-300'}`}
    />
    {error && <p className="text-xs text-red-600">{error}</p>}
  </div>
);

const ConfirmDelete = ({ open, onClose, onConfirm, item }) => (
  <AnimatePresence>
    {open && (
      <motion.div
        className="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
      >
        <motion.div
          initial={{ y: 50, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          exit={{ y: 50, opacity: 0 }}
          transition={{ type: 'spring', stiffness: 260, damping: 20 }}
          className="mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
        >
          <h3 className="text-lg font-semibold">Hapus Industri</h3>
          <p className="mt-2 text-sm text-gray-600">
            Anda yakin ingin menghapus <span className="font-medium text-gray-900">{item?.nama_industri}</span> ({item?.kode_industri})?
          </p>
          <div className="mt-6 flex justify-end gap-3">
            <button onClick={onClose} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
              Batal
            </button>
            <button onClick={onConfirm} className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
              Hapus
            </button>
          </div>
        </motion.div>
      </motion.div>
    )}
  </AnimatePresence>
);

export default function IndustriFarmasiPage() {
  const { props } = usePage();
  const { items, filters, flash, nextCode } = props;

  const [query, setQuery] = useState(filters?.q || '');
  const [perPage, setPerPage] = useState(filters?.perPage || 10);
  const [modalOpen, setModalOpen] = useState(false);
  const [isEdit, setIsEdit] = useState(false);
  const [selected, setSelected] = useState(null);
  const [confirmOpen, setConfirmOpen] = useState(false);

  const {
    data,
    setData,
    post,
    put,
    processing,
    errors,
    reset
  } = useForm({
    kode_industri: '',
    nama_industri: '',
    alamat: '',
    kota: '',
    no_telp: ''
  });

  useEffect(() => {
    if (flash?.success) {
      const t = setTimeout(() => {}, 2500);
      return () => clearTimeout(t);
    }
  }, [flash]);

  const openCreate = () => {
    reset();
    setIsEdit(false);
    setSelected(null);
    // Prefill kode_industri with nextCode from server (format I0000, auto-increment)
    setData('kode_industri', (nextCode || '').toUpperCase());
    setModalOpen(true);
  };

  const openEdit = (item) => {
    console.log('openEdit called with item:', item);
    console.log('item.kode_industri:', item.kode_industri);
    setIsEdit(true);
    setSelected(item);
    setData({
      kode_industri: item.kode_industri || '',
      nama_industri: item.nama_industri || '',
      alamat: item.alamat || '',
      kota: item.kota || '',
      no_telp: item.no_telp || ''
    });
    setModalOpen(true);
  };

  const closeModal = () => {
    setModalOpen(false);
  };

  const submitForm = (e) => {
    e.preventDefault();
    if (isEdit && selected) {
      // Pastikan kode_industri ada dan tidak kosong
      if (!selected?.kode_industri) {
        alert('Kode industri kosong. Pilih data yang benar sebelum menyimpan perubahan.');
        return;
      }
      
      // Buat URL dengan format yang benar - tanpa encoding berlebihan
      const kodeIndustri = selected.kode_industri.trim();
      const updateUrl = `/farmasi/industri-farmasi/${kodeIndustri}`;

      // Named route Ziggy untuk memastikan URL yang tepat dengan parameter
      const namedUpdateUrl = route('farmasi.industri-farmasi.update', { kode_industri: kodeIndustri });

      console.log('PUT Request Details:');
      console.log('- Kode Industri:', kodeIndustri);
      console.log('- Update URL (manual):', updateUrl);
      console.log('- Update URL (named route):', namedUpdateUrl);
      console.log('- Data:', data);

      // Gunakan method spoofing via POST dengan forceFormData agar Laravel mengenali _method=PUT
      router.post(namedUpdateUrl, { ...data, _method: 'PUT' }, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          console.log('Update berhasil dengan method spoofing (POST + _method=PUT)');
          setModalOpen(false);
        },
        onError: (errors) => {
          console.error('Update gagal:', errors);
        }
      });
    } else {
      // Create: gunakan named route untuk konsistensi
      post(route('farmasi.industri-farmasi.store'), {
        preserveScroll: true,
        onSuccess: () => setModalOpen(false)
      });
    }
  };

  const confirmDelete = (item) => {
    setSelected(item);
    setConfirmOpen(true);
  };

  const performDelete = () => {
    if (!selected) return;
    // Guard kode_industri & generate URL yang benar
    if (!selected?.kode_industri) {
      alert('Kode industri kosong. Pilih data yang benar sebelum menghapus.');
      return;
    }
    router.delete(route('farmasi.industri-farmasi.destroy', { kode_industri: selected.kode_industri }) , {
      preserveScroll: true,
      onSuccess: () => setConfirmOpen(false)
    });
  };

  const onSearch = (e) => {
    e.preventDefault();
    router.get(route('farmasi.industri-farmasi.index'), { q: query, perPage }, { preserveState: true, replace: true });
  };

  const onChangePerPage = (e) => {
    const val = Number(e.target.value);
    setPerPage(val);
    router.get(route('farmasi.industri-farmasi.index'), { q: query, perPage: val }, { preserveState: true, replace: true });
  };

  return (
    <div>
      <Head title="Industri Farmasi" />
      <div className="space-y-6">
        <PageHeader />

        {flash?.success && (
          <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} className="rounded-lg bg-green-50 p-3 text-sm text-green-800">
            {flash.success}
          </motion.div>
        )}

        <div className="rounded-xl bg-white p-4 shadow-sm">
          {modalOpen && (
            <div className="mb-5 rounded-lg border border-indigo-200 bg-indigo-50/40 p-4">
              <div className="flex items-start justify-between">
                <div>
                  <h3 className="text-lg font-semibold">{isEdit ? 'Edit Industri' : 'Tambah Industri'}</h3>
                  <p className="text-sm text-gray-600">Form berada di dalam layout utama (inline).</p>
                </div>
                <button onClick={closeModal} className="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Tutup">✕</button>
              </div>

              <form onSubmit={submitForm} className="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <Input
                  label="Kode Industri"
                  id="kode_industri"
                  value={data.kode_industri}
                  onChange={(e) => setData('kode_industri', e.target.value.toUpperCase())}
                  maxLength={5}
                  placeholder="Mis. IND01"
                  disabled={isEdit}
                  error={errors?.kode_industri}
                />
                <Input
                  label="Nama Industri"
                  id="nama_industri"
                  value={data.nama_industri}
                  onChange={(e) => setData('nama_industri', e.target.value)}
                  maxLength={50}
                  placeholder="Nama perusahaan"
                  error={errors?.nama_industri}
                />
                <Input
                  label="Alamat"
                  id="alamat"
                  value={data.alamat}
                  onChange={(e) => setData('alamat', e.target.value)}
                  maxLength={50}
                  placeholder="Alamat perusahaan"
                  error={errors?.alamat}
                />
                <Input
                  label="Kota"
                  id="kota"
                  value={data.kota}
                  onChange={(e) => setData('kota', e.target.value)}
                  maxLength={20}
                  placeholder="Kota"
                  error={errors?.kota}
                />
                <Input
                  label="No. Telp"
                  id="no_telp"
                  value={data.no_telp}
                  onChange={(e) => setData('no_telp', e.target.value)}
                  maxLength={20}
                  placeholder="Nomor telepon"
                  error={errors?.no_telp}
                />

                <div className="md:col-span-2 mt-2 flex justify-end gap-3">
                  <button type="button" onClick={closeModal} className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                  </button>
                  <button type="submit" disabled={processing} className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                    {isEdit ? 'Simpan Perubahan' : 'Simpan'}
                  </button>
                </div>
              </form>
            </div>
          )}
          <div className="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <form onSubmit={onSearch} className="flex w-full max-w-xl items-center gap-2">
              <div className="flex-1">
                <label className="block text-xs font-medium text-gray-600">Pencarian</label>
                <input
                  type="text"
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder="Cari kode/nama/alamat/kota/no telp..."
                  className="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-300"
                />
              </div>
              <button type="submit" className="mt-6 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Cari
              </button>
            </form>
            <div className="flex items-center gap-3">
              <div>
                <label className="block text-xs font-medium text-gray-600">Baris per halaman</label>
                <select value={perPage} onChange={onChangePerPage} className="mt-1 rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-300">
                  {[10, 20, 50].map(n => <option key={n} value={n}>{n}</option>)}
                </select>
              </div>
              <button onClick={openCreate} className="mt-6 rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">
                + Tambah Industri
              </button>
            </div>
          </div>

          <div className="mt-4 overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead>
                <tr className="bg-gray-50">
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Kode</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Nama Industri</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Alamat</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Kota</th>
                  <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">No. Telp</th>
                  <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {items?.data?.length ? items.data.map((item) => (
                  <tr key={item.kode_industri} className="hover:bg-gray-50">
                    <td className="px-4 py-2 text-sm font-mono">{item.kode_industri}</td>
                    <td className="px-4 py-2 text-sm">{item.nama_industri}</td>
                    <td className="px-4 py-2 text-sm">{item.alamat}</td>
                    <td className="px-4 py-2 text-sm">{item.kota}</td>
                    <td className="px-4 py-2 text-sm">{item.no_telp}</td>
                    <td className="px-4 py-2 text-right">
                      <div className="flex justify-end gap-2">
                        <button onClick={() => openEdit(item)} className="rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">
                          Edit
                        </button>
                        <button onClick={() => confirmDelete(item)} className="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                          Hapus
                        </button>
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
                  <Link
                    key={idx}
                    href={link.url || '#'}
                    preserveState
                    replace
                    className={`rounded-lg px-3 py-1.5 text-xs ${link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border'} ${!link.url ? 'pointer-events-none opacity-50' : ''}`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                  />
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Form inline di dalam layout utama, modal overlay dihapus */}

        <ConfirmDelete open={confirmOpen} onClose={() => setConfirmOpen(false)} onConfirm={performDelete} item={selected} />
      </div>
    </div>
  );
}

// Pastikan halaman berada di dalam AppLayout utama
IndustriFarmasiPage.layout = (page) => <AppLayout title="Farmasi" children={page} />;

