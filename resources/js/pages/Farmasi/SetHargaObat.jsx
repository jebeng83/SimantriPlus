import React, { useState, useEffect, Suspense, lazy } from 'react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import InputError from '@/components/InputError';
import InputLabel from '@/components/InputLabel';
import TextInput from '@/components/TextInput';
import JenisSelector from '@/components/JenisSelector';
import DataBarangSelector from '@/components/DataBarangSelector';
// Local helper components MUST be defined at module scope to avoid re-mounting on each render.
function PercentageField({ id, label, value, onChange, error }) {
    return (
        <div>
            <InputLabel htmlFor={id} value={`${label} (%)`} />
            <div className="relative">
                <TextInput
                    id={id}
                    type="number"
                    min="0"
                    step="0.01"
                    inputMode="decimal"
                    className="mt-1 block w-full pr-10"
                    value={value ?? ''}
                    onChange={onChange}
                />
                <span className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">%</span>
            </div>
            <InputError message={error} className="mt-2" />
        </div>
    );
}

function JenisRow({ label, id, value, onChange, error }) {
    return (
        <div className="flex items-center justify-between gap-6 py-2">
            <label htmlFor={id} className="text-sm text-gray-700">{label} (%)</label>
            <div className="relative w-44">
                <TextInput id={id} type="number" min="0" step="0.01" inputMode="decimal" value={value ?? ''} onChange={onChange} className="w-full pr-10" />
                <span className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                {error && <InputError message={error} className="mt-1" />}
            </div>
        </div>
    );
}
// toast will be dynamically imported on demand to reduce initial bundle size

// Lazy-load summary tables to reduce initial bundle
const UmumSummaryTable = lazy(() => import('@/components/UmumSummaryTable'));
const JenisSummaryTable = lazy(() => import('@/components/JenisSummaryTable'));
const BarangSummaryTable = lazy(() => import('@/components/BarangSummaryTable'));

export default function SetHargaObat({ auth, hargaObat, schema, penjualanUmum, penjualanPerJenis, penjualanPerBarang, error }) {
    // Fields: settings + numeric percentages derived from DESCRIBE
    const numericFieldsKnown = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
    const settingsFieldsKnown = ['setharga','hargadasar','ppn'];
    const schemaFields = Array.isArray(schema) ? schema.map((c) => c.Field) : null;
    const numericFields = (schemaFields && schemaFields.length > 0)
        ? schemaFields.filter((f) => numericFieldsKnown.includes(f))
        : numericFieldsKnown;
    const settingsFields = (schemaFields && schemaFields.length > 0)
        ? schemaFields.filter((f) => settingsFieldsKnown.includes(f))
        : settingsFieldsKnown;

    const initialData = {
        // Settings defaults
        setharga: (hargaObat && hargaObat.setharga) || 'Umum',
        hargadasar: (hargaObat && hargaObat.hargadasar) || 'Harga Beli',
        ppn: (hargaObat && hargaObat.ppn) || 'Yes',
        // Numeric defaults
        ...numericFields.reduce((acc, k) => {
            // Prioritaskan data dari setpenjualanumum bila tersedia
            if (penjualanUmum && Object.prototype.hasOwnProperty.call(penjualanUmum, k)) {
                acc[k] = penjualanUmum[k];
            } else if (hargaObat && Object.prototype.hasOwnProperty.call(hargaObat, k)) {
                acc[k] = hargaObat[k];
            } else {
                acc[k] = 20;
            }
            return acc;
        }, {}),
        // Per-jenis key
        kdjns: '',
        // Per-barang key
        kode_brng: ''
    };

    const { data, setData, post, processing, errors, reset } = useForm(initialData);

    // Tabs + Global setter (incremental change)
    const [activeTab, setActiveTab] = useState('pengaturan');
    // Collapse state untuk konten tab Jenis & Barang (default tertutup)
    const [jenisCollapsed, setJenisCollapsed] = useState(true);
    const [barangCollapsed, setBarangCollapsed] = useState(true);
    // Collapse state untuk tab Pengaturan Harga Umum (default tertutup)
    const [umumCollapsed, setUmumCollapsed] = useState(true);

    // Local data sources when not mounted via Inertia
    const [umumData, setUmumData] = useState(penjualanUmum || null);
    const [perJenisData, setPerJenisData] = useState(Array.isArray(penjualanPerJenis) ? penjualanPerJenis : []);
    const [perBarangData, setPerBarangData] = useState(Array.isArray(penjualanPerBarang) ? penjualanPerBarang : []);

    // Fetch initial data for summary tables and to prefill numeric fields from setpenjualanumum
    useEffect(() => {
        // General settings
        fetch('/set-penjualan-umum', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((json) => {
                if (json && json.success) {
                    setUmumData(json.data || null);
                    const keys = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
                    keys.forEach((k) => {
                        if (json.data && typeof json.data[k] !== 'undefined') {
                            setData(k, json.data[k]);
                        }
                    });
                }
            })
            .catch(() => {});

        // Per-jenis list
        fetch('/set-penjualan/json', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((json) => {
                if (json && json.success && Array.isArray(json.data)) {
                    setPerJenisData(json.data);
                }
            })
            .catch(() => {});

        // Per-barang list
        fetch('/set-penjualan-barang/json', { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((json) => {
                if (json && json.success && Array.isArray(json.data)) {
                    setPerBarangData(json.data);
                }
            })
            .catch(() => {});
    }, []);

    // Set document.title since we are not inside an Inertia app context
    useEffect(() => {
        document.title = 'Pengaturan Harga Obat';
    }, []);

    useEffect(() => {
        if (error) {
            import('@/tools/toast').then(({ toast }) => toast.error(error)).catch(() => {
                console.error('Gagal memuat modul toast');
            });
        }
    }, [error]);

    // Simpan tab "Pengaturan Harga Umum" ke tabel setpenjualanumum
    const handleSubmitUmum = (e) => {
        e.preventDefault();
        const toNumber = (val) => {
            const n = Number(val);
            return Number.isFinite(n) ? n : 0;
        };
        const payload = {
            ralan: toNumber(data.ralan),
            kelas1: toNumber(data.kelas1),
            kelas2: toNumber(data.kelas2),
            kelas3: toNumber(data.kelas3),
            utama: toNumber(data.utama),
            vip: toNumber(data.vip),
            vvip: toNumber(data.vvip),
            beliluar: toNumber(data.beliluar),
            jualbebas: toNumber(data.jualbebas),
            karyawan: toNumber(data.karyawan),
        };
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/farmasi/set-penjualan-umum', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Gagal menyimpan pengaturan harga umum');
                return r.text();
            })
            .then(() => {
                import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan harga umum berhasil disimpan')).catch(() => {});
            })
            .catch((err) => {
                console.error(err);
                import('@/tools/toast').then(({ toast }) => toast.error('Terjadi kesalahan saat menyimpan pengaturan harga umum')).catch(() => {});
            });
    };

    // Simpan khusus dari tab "Pengaturan Harga" tanpa form submit
    const handleSavePengaturan = () => {
        // Backend mewajibkan semua field numeric + enum. Pastikan dikirim lengkap.
        const toNumber = (val) => {
            const n = Number(val);
            return Number.isFinite(n) ? n : 0;
        };
        const payload = {
            setharga: data.setharga || 'Umum',
            hargadasar: data.hargadasar || 'Harga Beli',
            ppn: data.ppn || 'Yes',
            ralan: toNumber(data.ralan),
            kelas1: toNumber(data.kelas1),
            kelas2: toNumber(data.kelas2),
            kelas3: toNumber(data.kelas3),
            utama: toNumber(data.utama),
            vip: toNumber(data.vip),
            vvip: toNumber(data.vvip),
            beliluar: toNumber(data.beliluar),
            jualbebas: toNumber(data.jualbebas),
            karyawan: toNumber(data.karyawan),
        };

        const csrf = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
        fetch('/farmasi/set-harga-obat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Gagal menyimpan pengaturan');
                return r.text();
            })
            .then(() => {
                import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan harga obat berhasil disimpan')).catch(() => {});
            })
            .catch((err) => {
                console.error(err);
                import('@/tools/toast').then(({ toast }) => toast.error('Terjadi kesalahan saat menyimpan pengaturan')).catch(() => {});
            });
    };

    // Simpan pengaturan per-jenis ke tabel setpenjualan
    const handleSubmitJenis = (e) => {
        e.preventDefault();
        const toNumber = (val) => {
            const n = Number(val);
            return Number.isFinite(n) ? n : 0;
        };
        const kode = (data.kdjns || '').toUpperCase().trim();
        if (!kode || kode.length !== 4) {
            import('@/tools/toast').then(({ toast }) => toast.error('Kode jenis harus 4 karakter (mis. J001)')).catch(() => {});
            return;
        }
        const payload = {
            kdjns: kode, // join ke jenis.kdjns (char(4))
            ralan: toNumber(data.ralan),
            kelas1: toNumber(data.kelas1),
            kelas2: toNumber(data.kelas2),
            kelas3: toNumber(data.kelas3),
            utama: toNumber(data.utama),
            vip: toNumber(data.vip),
            vvip: toNumber(data.vvip),
            beliluar: toNumber(data.beliluar),
            jualbebas: toNumber(data.jualbebas),
            karyawan: toNumber(data.karyawan),
        };
        const csrf = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
        fetch('/farmasi/set-penjualan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Gagal menyimpan pengaturan per jenis');
                return r.text();
            })
            .then(() => {
                import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan per jenis berhasil disimpan')).catch(() => {});
            })
            .catch((errors) => {
                console.error(errors);
                import('@/tools/toast').then(({ toast }) => toast.error('Gagal menyimpan pengaturan per jenis')).catch(() => {});
            });
    };

    // Simpan pengaturan per-barang dengan mengupdate persentase di tabel databarang
    const handleSubmitBarang = (e) => {
        e.preventDefault();
        const toNumber = (val) => {
            const n = Number(val);
            return Number.isFinite(n) ? n : 0;
        };
        const kode = (data.kode_brng || '').toUpperCase().trim();
        if (!kode) {
            import('@/tools/toast').then(({ toast }) => toast.error('Kode barang harus diisi')).catch(() => {});
            return;
        }
        const payload = {
            kode_brng: kode,
            ralan: toNumber(data.ralan),
            kelas1: toNumber(data.kelas1),
            kelas2: toNumber(data.kelas2),
            kelas3: toNumber(data.kelas3),
            utama: toNumber(data.utama),
            vip: toNumber(data.vip),
            vvip: toNumber(data.vvip),
            beliluar: toNumber(data.beliluar),
            jualbebas: toNumber(data.jualbebas),
            karyawan: toNumber(data.karyawan),
        };
        const csrf = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
        fetch('/set-penjualan-barang', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Gagal menyimpan pengaturan per barang');
                return r.text();
            })
            .then(() => {
                import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan per barang berhasil disimpan')).catch(() => {});
            })
            .catch((errors) => {
                console.error(errors);
                import('@/tools/toast').then(({ toast }) => toast.error('Gagal menyimpan pengaturan per barang')).catch(() => {});
            });
    };

    // Auto-fill: ketika kode_brng dipilih, ambil pengaturan tersimpan dari setpenjualanperbarang
    useEffect(() => {
        const kode = (data.kode_brng || '').toUpperCase().trim();
        if (!kode) return;
        if (activeTab !== 'barang') return;
        const url = `/set-penjualan-barang/${encodeURIComponent(kode)}`;
        fetch(url, { headers: { Accept: 'application/json' }})
            .then((r) => r.json())
            .then((json) => {
                if (json && json.success && json.data) {
                    const row = json.data;
                    const keys = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
                    keys.forEach((k) => {
                        if (row[k] !== undefined && row[k] !== null) {
                            setData(k, row[k]);
                        }
                    });
                    // otomatis buka form jika sedang collapsed
                    if (barangCollapsed) setBarangCollapsed(false);
                }
            })
            .catch(() => {})
        ;
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [data.kode_brng, activeTab]);

    return (
        <AppLayout>
             <div className="rounded-xl bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-lg">
                 <h2 className="font-semibold text-xl leading-tight">Pengaturan Harga Obat</h2>
                 <p className="mt-1 text-sm opacity-90">Membuat tampilan lebih profesional dan elegan dengan tab menu.</p>
             </div>
 
             <div className="py-12">
                {/* Perbesar card agar memenuhi ruang kosong kiri/kanan */}
                <div className="mx-auto max-w-none px-0 sm:px-0 lg:px-0">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg w-full">
                        <div className="p-6 text-gray-900">
                            {/* Tab menu */}
                            <nav role="tablist" aria-label="Pengaturan harga" className="border-b border-gray-200 mb-6">
                                <ul className="flex flex-wrap gap-2">
                                    {[
                                        { key: 'pengaturan', label: 'Pengaturan Harga' },
                                        { key: 'umum', label: 'Pengaturan Harga Umum' },
                                        { key: 'jenis', label: 'Pengaturan Harga Per Jenis Barang' },
                                        { key: 'barang', label: 'Pengaturan Harga Per Barang' },
                                    ].map((t) => (
                                        <li key={t.key}>
                                            <button
                                                type="button"
                                                role="tab"
                                                aria-selected={activeTab === t.key}
                                                onClick={() => setActiveTab(t.key)}
                                                className={`px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 ${
                                                    activeTab === t.key
                                                        ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm'
                                                        : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 border border-transparent'
                                                }`}
                                            >
                                                {t.label}
                                            </button>
                                        </li>
                                    ))}
                                </ul>
                            </nav>

                            {activeTab === 'pengaturan' && (
                                <div className="space-y-4">
                                    {/* Controls sesuai gambar: setharga, hargadasar, ppn */}
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <InputLabel htmlFor="setharga" value="Harga obat yang digunakan" />
                                            <select id="setharga" className="mt-1 block w-full border rounded-md px-3 py-2"
                                                value={data.setharga} onChange={(e) => setData('setharga', e.target.value)}>
                                                <option value="Umum">Umum</option>
                                                <option value="Per Jenis">Per Jenis</option>
                                                <option value="Per Barang">Per Barang</option>
                                            </select>
                                            <InputError message={errors.setharga} className="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel htmlFor="hargadasar" value="Asal harga jual obat" />
                                            <select id="hargadasar" className="mt-1 block w-full border rounded-md px-3 py-2"
                                                value={data.hargadasar} onChange={(e) => setData('hargadasar', e.target.value)}>
                                                <option value="Harga Beli">Harga Beli</option>
                                                <option value="Harga Diskon">Harga Diskon</option>
                                            </select>
                                            <InputError message={errors.hargadasar} className="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel htmlFor="ppn" value="Sertakan PPN Pembelian" />
                                            <select id="ppn" className="mt-1 block w-full border rounded-md px-3 py-2"
                                                value={data.ppn} onChange={(e) => setData('ppn', e.target.value)}>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                            <InputError message={errors.ppn} className="mt-2" />
                                        </div>
                                    </div>

                                    {/* Ringkasan dalam bentuk tabel seperti contoh */}
                                    <div className="mt-4 border rounded overflow-hidden">
                                        <table className="min-w-full text-sm">
                                            <thead className="bg-gray-100">
                                                <tr>
                                                    <th className="px-3 py-2 text-left">Harga obat yang digunakan</th>
                                                    <th className="px-3 py-2 text-left">Asal harga jual obat</th>
                                                    <th className="px-3 py-2 text-left">Sertakan PPN Pembelian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td className="px-3 py-2">{data.setharga}</td>
                                                    <td className="px-3 py-2">{data.hargadasar}</td>
                                                    <td className="px-3 py-2">{data.ppn}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    {/* Global percentage controls dipindah ke tab 'Pengaturan Harga Umum' */}

                                    {/* Tombol simpan pengaturan */}
                                    <div className="flex items-center justify-end mt-4">
                                        <button
                                            type="button"
                                            disabled={processing}
                                            onClick={handleSavePengaturan}
                                            className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                                        </button>
                                    </div>
                                </div>
                            )}

                            {activeTab === 'umum' && (
                            <form onSubmit={handleSubmitUmum}>
                                <div className="mb-6 flex items-center justify-between">
                                    <div>
                                        <h3 className="text-lg font-semibold text-gray-900">Pengaturan Persentase Harga Jual Obat</h3>
                                        <p className="text-sm text-gray-600 mt-2">
                                            Tentukan persentase markup harga jual obat dari harga beli di setiap kategori. Nilai dalam persen (%).
                                        </p>
                                    </div>
                                    <div>
                                        <button
                                            type="button"
                                            aria-expanded={!umumCollapsed}
                                            onClick={() => setUmumCollapsed((v) => !v)}
                                            className="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium border rounded-md bg-white hover:bg-gray-50 text-gray-700"
                                        >
                                            <span>{umumCollapsed ? 'Tampilkan' : 'Ciutkan'}</span>
                                            <span aria-hidden="true">{umumCollapsed ? '▼' : '▲'}</span>
                                        </button>
                                    </div>
                                </div>

                                {/* Helper component untuk field persen (dipindah ke module scope agar tidak re-mount saat onChange) */}
                                {!umumCollapsed && (
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <PercentageField id="ralan" label="Rawat Jalan" value={data.ralan} onChange={(e) => setData('ralan', e.target.value)} error={errors.ralan} />
                                    <PercentageField id="kelas1" label="Kelas 1" value={data.kelas1} onChange={(e) => setData('kelas1', e.target.value)} error={errors.kelas1} />
                                    <PercentageField id="kelas2" label="Kelas 2" value={data.kelas2} onChange={(e) => setData('kelas2', e.target.value)} error={errors.kelas2} />
                                    <PercentageField id="kelas3" label="Kelas 3" value={data.kelas3} onChange={(e) => setData('kelas3', e.target.value)} error={errors.kelas3} />
                                    <PercentageField id="utama" label="Kelas Utama" value={data.utama} onChange={(e) => setData('utama', e.target.value)} error={errors.utama} />
                                    <PercentageField id="vip" label="VIP" value={data.vip} onChange={(e) => setData('vip', e.target.value)} error={errors.vip} />
                                    <PercentageField id="vvip" label="VVIP" value={data.vvip} onChange={(e) => setData('vvip', e.target.value)} error={errors.vvip} />
                                    <PercentageField id="beliluar" label="Beli Luar" value={data.beliluar} onChange={(e) => setData('beliluar', e.target.value)} error={errors.beliluar} />
                                    <PercentageField id="jualbebas" label="Jual Bebas" value={data.jualbebas} onChange={(e) => setData('jualbebas', e.target.value)} error={errors.jualbebas} />
                                    <PercentageField id="karyawan" label="Karyawan" value={data.karyawan} onChange={(e) => setData('karyawan', e.target.value)} error={errors.karyawan} />
                                </div>
                                )}

                                {/* Datatable dipindahkan ke atas tombol Simpan untuk visibilitas lebih baik */}
                                <Suspense
                                    fallback={(
                                        <div className="mt-8 border rounded overflow-hidden">
                                            <div className="px-4 py-3 bg-gray-50 border-b">
                                                <span className="animate-pulse">Memuat data pengaturan harga umum...</span>
                                            </div>
                                        </div>
                                    )}
                                >
                                    <UmumSummaryTable penjualanUmum={umumData} />
                                </Suspense>

                                {!umumCollapsed && (
                                <div className="flex items-center justify-end mt-8">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                    >
                                        {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                                    </button>
                                </div>
                                )}
                            </form>
                            )}

                            {activeTab === 'jenis' && (
                                <form onSubmit={handleSubmitJenis}>
                                    <div className="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">Pengaturan Harga Per Jenis Barang</h3>
                                            <p className="text-sm text-gray-600 mt-2">Masukkan persentase keuntungan untuk tiap kelas perawatan berdasarkan jenis barang/obat.</p>
                                        </div>
                                        <div>
                                            <button
                                                type="button"
                                                aria-expanded={!jenisCollapsed}
                                                onClick={() => setJenisCollapsed((v) => !v)}
                                                className="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium border rounded-md bg-white hover:bg-gray-50 text-gray-700"
                                            >
                                                <span>{jenisCollapsed ? 'Tampilkan' : 'Ciutkan'}</span>
                                                <span aria-hidden="true">{jenisCollapsed ? '▼' : '▲'}</span>
                                            </button>
                                        </div>
                                    </div>

                                    {/* Daftar field persentase per-jenis (komponen dipindah ke module scope) */}
                                    {!jenisCollapsed && (
                                    <div className="divide-y">
                                        <JenisRow id="ralanJenis" label="Keuntungan di Rawat Jalan" value={data.ralan} onChange={(e) => setData('ralan', e.target.value)} error={errors.ralan} />
                                        <JenisRow id="kelas1Jenis" label="Keuntungan di Rawat Inap Kelas 1" value={data.kelas1} onChange={(e) => setData('kelas1', e.target.value)} error={errors.kelas1} />
                                        <JenisRow id="kelas2Jenis" label="Keuntungan di Rawat Inap Kelas 2" value={data.kelas2} onChange={(e) => setData('kelas2', e.target.value)} error={errors.kelas2} />
                                        <JenisRow id="kelas3Jenis" label="Keuntungan di Rawat Inap Kelas 3" value={data.kelas3} onChange={(e) => setData('kelas3', e.target.value)} error={errors.kelas3} />
                                        <JenisRow id="utamaJenis" label="Keuntungan di Rawat Inap Kelas Utama" value={data.utama} onChange={(e) => setData('utama', e.target.value)} error={errors.utama} />
                                        <JenisRow id="vipJenis" label="Keuntungan di Rawat Inap Kelas VIP" value={data.vip} onChange={(e) => setData('vip', e.target.value)} error={errors.vip} />
                                        <JenisRow id="vvipJenis" label="Keuntungan di Rawat Inap Kelas VVIP" value={data.vvip} onChange={(e) => setData('vvip', e.target.value)} error={errors.vvip} />
                                        <JenisRow id="beliluarJenis" label="Keuntungan Jika Beli dari Apotek Lain" value={data.beliluar} onChange={(e) => setData('beliluar', e.target.value)} error={errors.beliluar} />
                                        <JenisRow id="jualbebasJenis" label="Keuntungan di Penjualan Bebas" value={data.jualbebas} onChange={(e) => setData('jualbebas', e.target.value)} error={errors.jualbebas} />
                                        <JenisRow id="karyawanJenis" label="Keuntungan Jika Pasien/Pembeli Karyawan" value={data.karyawan} onChange={(e) => setData('karyawan', e.target.value)} error={errors.karyawan} />
                                    </div>
                                    )}

                                    {/* Pilih Kode Jenis - sekarang menggunakan searchable dropdown dari tabel jenis */}
                                    {!jenisCollapsed && (
                                        <JenisSelector value={data.kdjns} onChange={(val) => setData('kdjns', val)} />
                                    )}

                                    {/* Tabel ringkasan per-jenis (selalu tampil; klik row akan membuka form bila tertutup) */}
                                    <Suspense fallback={<div className="mt-6 text-sm text-gray-500">Memuat ringkasan per-jenis...</div>}>
                                        <JenisSummaryTable
                                            items={perJenisData}
                                            onSelect={(row) => {
                                                if (!row) return;
                                                if (jenisCollapsed) setJenisCollapsed(false);
                                                // Set kode jenis
                                                setData('kdjns', (row.kdjns || '').toUpperCase());
                                                // Mapping semua field numerik
                                                const keys = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
                                                keys.forEach((k) => {
                                                    if (row[k] !== undefined && row[k] !== null) {
                                                        setData(k, row[k]);
                                                    }
                                                });
                                            }}
                                            onDelete={(row) => {
                                                if (!row || !row.kdjns) return;
                                                const kode = String(row.kdjns).toUpperCase();
                                                if (!window.confirm(`Yakin hapus pengaturan untuk jenis ${kode}?`)) return;
                                                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                                fetch(`/farmasi/set-penjualan/${encodeURIComponent(kode)}`, {
                                                    method: 'DELETE',
                                                    headers: {
                                                        Accept: 'application/json',
                                                        'X-CSRF-TOKEN': csrf,
                                                    },
                                                    credentials: 'same-origin',
                                                })
                                                    .then((r) => {
                                                        if (!r.ok) throw new Error('Gagal menghapus pengaturan per jenis');
                                                        return r.text();
                                                    })
                                                    .then(() => {
                                                        import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan per jenis berhasil dihapus')).catch(() => {});
                                                        fetch('/set-penjualan/json', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
                                                            .then((r) => r.json())
                                                            .then((json) => { if (json && Array.isArray(json.data)) setPerJenisData(json.data); })
                                                            .catch(() => {});
                                                    })
                                                    .catch((errors) => {
                                                        console.error(errors);
                                                        import('@/tools/toast').then(({ toast }) => toast.error('Gagal menghapus pengaturan per jenis')).catch(() => {});
                                                    });
                                            }}
                                        />
                                    </Suspense>

                                    {!jenisCollapsed && (
                                        <div className="flex items-center justify-end mt-6">
                                            <button type="submit" disabled={processing} className="px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                                                {processing ? 'Menyimpan...' : 'Simpan Pengaturan Per Jenis'}
                                            </button>
                                        </div>
                                    )}
                                </form>
                            )}

                            {activeTab === 'barang' && (
                                <form onSubmit={handleSubmitBarang}>
                                    <div className="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">Pengaturan Harga Per Barang</h3>
                                            <p className="text-sm text-gray-600 mt-2">Masukkan persentase keuntungan untuk tiap kelas perawatan berdasarkan data barang.</p>
                                        </div>
                                        <div>
                                            <button
                                                type="button"
                                                aria-expanded={!barangCollapsed}
                                                onClick={() => setBarangCollapsed((v) => !v)}
                                                className="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium border rounded-md bg-white hover:bg-gray-50 text-gray-700"
                                            >
                                                <span>{barangCollapsed ? 'Tampilkan' : 'Ciutkan'}</span>
                                                <span aria-hidden="true">{barangCollapsed ? '▼' : '▲'}</span>
                                            </button>
                                        </div>
                                    </div>

                                    {/* Field persentase per-barang (reuse JenisRow for layout) */}
                                    {!barangCollapsed && (
                                    <div className="divide-y">
                                        <JenisRow id="ralanBarang" label="Keuntungan di Rawat Jalan" value={data.ralan} onChange={(e) => setData('ralan', e.target.value)} error={errors.ralan} />
                                        <JenisRow id="kelas1Barang" label="Keuntungan di Rawat Inap Kelas 1" value={data.kelas1} onChange={(e) => setData('kelas1', e.target.value)} error={errors.kelas1} />
                                        <JenisRow id="kelas2Barang" label="Keuntungan di Rawat Inap Kelas 2" value={data.kelas2} onChange={(e) => setData('kelas2', e.target.value)} error={errors.kelas2} />
                                        <JenisRow id="kelas3Barang" label="Keuntungan di Rawat Inap Kelas 3" value={data.kelas3} onChange={(e) => setData('kelas3', e.target.value)} error={errors.kelas3} />
                                        <JenisRow id="utamaBarang" label="Keuntungan di Rawat Inap Kelas Utama" value={data.utama} onChange={(e) => setData('utama', e.target.value)} error={errors.utama} />
                                        <JenisRow id="vipBarang" label="Keuntungan di Rawat Inap Kelas VIP" value={data.vip} onChange={(e) => setData('vip', e.target.value)} error={errors.vip} />
                                        <JenisRow id="vvipBarang" label="Keuntungan di Rawat Inap Kelas VVIP" value={data.vvip} onChange={(e) => setData('vvip', e.target.value)} error={errors.vvip} />
                                        <JenisRow id="beliluarBarang" label="Keuntungan Jika Beli dari Apotek Lain" value={data.beliluar} onChange={(e) => setData('beliluar', e.target.value)} error={errors.beliluar} />
                                        <JenisRow id="jualbebasBarang" label="Keuntungan di Penjualan Bebas" value={data.jualbebas} onChange={(e) => setData('jualbebas', e.target.value)} error={errors.jualbebas} />
                                        <JenisRow id="karyawanBarang" label="Keuntungan Jika Pasien/Pembeli Karyawan" value={data.karyawan} onChange={(e) => setData('karyawan', e.target.value)} error={errors.karyawan} />
                                    </div>
                                    )}

                                    {/* Selector Data Barang (kode_brng) dengan link ke halaman Data Obat */}
                                    {!barangCollapsed && (
                                        <DataBarangSelector value={data.kode_brng} onChange={(val) => setData('kode_brng', val)} />
                                    )}

                                    {!barangCollapsed && (
                                        <div className="flex items-center justify-end mt-6">
                                            <button type="submit" disabled={processing} className="px-5 py-2.5 bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                                                {processing ? 'Menyimpan...' : 'Simpan Pengaturan Per Barang'}
                                            </button>
                                        </div>
                                    )}
                                    {/* Tabel ringkasan per-barang */}
                                    <Suspense fallback={<div className="mt-6 text-sm text-gray-500">Memuat ringkasan per-barang...</div>}>
                                        <BarangSummaryTable
                                            items={perBarangData}
                                            onSelect={(row) => {
                                                if (!row) return;
                                                if (barangCollapsed) setBarangCollapsed(false);
                                                setData('kode_brng', (row.kode_brng || '').toUpperCase());
                                                const keys = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
                                                keys.forEach((k) => {
                                                    if (row[k] !== undefined && row[k] !== null) {
                                                        setData(k, row[k]);
                                                    }
                                                });
                                            }}
                                            onDelete={(row) => {
                                                if (!row || !row.kode_brng) return;
                                                const kode = String(row.kode_brng).toUpperCase();
                                                if (!window.confirm(`Yakin hapus pengaturan untuk barang ${kode}?`)) return;
                                                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                                fetch(`/set-penjualan-barang/${encodeURIComponent(kode)}`, {
                                                    method: 'DELETE',
                                                    headers: {
                                                        Accept: 'application/json',
                                                        'X-CSRF-TOKEN': csrf,
                                                    },
                                                    credentials: 'same-origin',
                                                })
                                                    .then((r) => {
                                                        if (!r.ok) throw new Error('Gagal menghapus pengaturan per barang');
                                                        return r.text();
                                                    })
                                                    .then(() => {
                                                        import('@/tools/toast').then(({ toast }) => toast.success('Pengaturan per barang berhasil dihapus')).catch(() => {});
                                                        fetch('/set-penjualan-barang/json', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
                                                            .then((r) => r.json())
                                                            .then((json) => { if (json && Array.isArray(json.data)) setPerBarangData(json.data); })
                                                            .catch(() => {});
                                                    })
                                                    .catch((errors) => {
                                                        console.error(errors);
                                                        import('@/tools/toast').then(({ toast }) => toast.error('Gagal menghapus pengaturan per barang')).catch(() => {});
                                                    });
                                            }}
                                        />
                                    </Suspense>
                                </form>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}