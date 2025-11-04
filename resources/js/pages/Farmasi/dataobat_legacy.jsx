import React, { useState, useEffect } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import axios from 'axios';
import { toast } from '@/tools/toast';

export default function DataObat() {
    // Fungsi untuk menghitung tanggal kadaluarsa default (hari ini + 3 tahun)
    const getDefaultExpiryDate = () => {
        const today = new Date();
        const expiryDate = new Date(today);
        expiryDate.setFullYear(today.getFullYear() + 3);
        return expiryDate.toISOString().split('T')[0]; // Format YYYY-MM-DD
    };

    const [search, setSearch] = useState('');
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState('create'); // 'create' or 'edit'
    const [selectedItem, setSelectedItem] = useState(null);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({});
    const [dropdownData, setDropdownData] = useState({
        kodesatuan: [],
        jenis: [],
        industrifarmasi: [],
        kategori_barang: [],
        golongan_barang: []
    });
    
    const [percentageData, setPercentageData] = useState({
        ralan: 0,
        kelas1: 0,
        kelas2: 0,
        kelas3: 0,
        utama: 0,
        vip: 0,
        vvip: 0,
        beliluar: 0,
        jualbebas: 0,
        karyawan: 0
    });
    
    const [includePPN, setIncludePPN] = useState(false);
    const [dataBarang, setDataBarang] = useState({ data: [], links: null, from: 0, to: 0, total: 0 });
    
    const [formData, setFormData] = useState({
        kode_brng: '',
        nama_brng: '',
        kode_sat: '',
        kode_satbesar: '',
        letak_barang: 'Apotek',
        dasar: '0',
        h_beli: '',
        ralan: '',
        kelas1: '',
        kelas2: '',
        kelas3: '',
        utama: '',
        vip: '',
        vvip: '',
        beliluar: '',
        jualbebas: '',
        karyawan: '',
        stokminimal: '100',
        kdjns: '',
        isi: '1',
        kapasitas: '1',
        expire: getDefaultExpiryDate(),
        status: '1',
        kode_industri: '',
        kode_kategori: '',
        kode_golongan: ''
    });

    const fetchList = async (query = '') => {
        try {
            setLoading(true);
            const res = await axios.get('/farmasi/data-obat', {
                headers: { Accept: 'application/json' },
                params: { q: query, perPage: 50 }
            });
            const items = res.data?.items || [];
            setDataBarang({
                data: items,
                links: null,
                from: items.length ? 1 : 0,
                to: items.length,
                total: items.length
            });
        } catch (error) {
            console.error('Error fetching Data Obat list:', error);
            toast('Gagal mengambil data obat', 'error');
        } finally {
            setLoading(false);
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        fetchList(search);
    };

    const openCreateModal = async () => {
        setModalType('create');
        setSelectedItem(null);
        
        // Generate auto code first
        let autoCode = '';
        try {
            const codeResponse = await axios.get('/farmasi/data-obat/next-code');
            autoCode = codeResponse?.data?.nextCode || '';
        } catch (error) {
            console.error('Error generating item code:', error);
        }
        
        setFormData({
            kode_brng: autoCode,
            kode_industri: '',
            nama_brng: '',
            kode_satbesar: '',
            isi: '1',
            kode_sat: '',
            kapasitas: '1',
            kdjns: '',
            kode_kategori: '',
            kode_golongan: '',
            dasar: '0',
            h_beli: '',
            ralan: '',
            kelas1: '',
            kelas2: '',
            kelas3: '',
            utama: '',
            vip: '',
            vvip: '',
            beliluar: '',
            jualbebas: '',
            karyawan: '',
            stokminimal: '100',
            expire: getDefaultExpiryDate(),
            status: '1',
            letak_barang: 'Apotek'
        });
        setIncludePPN(false);
        setErrors({});
        setShowModal(true);
    };

    const openEditModal = async (item) => {
        setModalType('edit');
        setSelectedItem(item);
        setFormData({
            kode_brng: item.kode_brng,
            kode_industri: item.kode_industri || '',
            nama_brng: item.nama_brng,
            kode_satbesar: item.kode_satbesar || '',
            isi: item.isi || '',
            kode_sat: item.kode_sat,
            kapasitas: item.kapasitas || '',
            kdjns: item.kdjns || '',
            kode_kategori: item.kode_kategori || '',
            kode_golongan: item.kode_golongan || '',
            dasar: item.dasar || '',
            h_beli: item.h_beli || '',
            ralan: item.ralan || '',
            kelas1: item.kelas1 || '',
            kelas2: item.kelas2 || '',
            kelas3: item.kelas3 || '',
            utama: item.utama || '',
            vip: item.vip || '',
            vvip: item.vvip || '',
            beliluar: item.beliluar || '',
            jualbebas: item.jualbebas || '',
            karyawan: item.karyawan || '',
            stokminimal: item.stokminimal || '',
            expire: item.expire || '',
            status: item.status || '1',
            letak_barang: item.letak_barang || ''
        });
        setIncludePPN(false);
        setErrors({});
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setSelectedItem(null);
        setErrors({});
    };

    // Fungsi untuk menghitung PPN 11%
    const calculatePPN = (hargaBeli) => {
        const harga = parseFloat(hargaBeli) || 0;
        return Math.round(harga * 0.11); // PPN 11%
    };

    const calculatePrices = (hargaBeli, usePPN = null) => {
        const harga = parseFloat(hargaBeli) || 0;
        const shouldIncludePPN = usePPN !== null ? usePPN : includePPN;
        const ppn = shouldIncludePPN ? calculatePPN(harga) : 0;
        
        return {
            ralan: Math.round(harga + (harga * percentageData.ralan / 100) + ppn),
            kelas1: Math.round(harga + (harga * percentageData.kelas1 / 100) + ppn),
            kelas2: Math.round(harga + (harga * percentageData.kelas2 / 100) + ppn),
            kelas3: Math.round(harga + (harga * percentageData.kelas3 / 100) + ppn),
            utama: Math.round(harga + (harga * percentageData.utama / 100) + ppn),
            vip: Math.round(harga + (harga * percentageData.vip / 100) + ppn),
            vvip: Math.round(harga + (harga * percentageData.vvip / 100) + ppn),
            beliluar: Math.round(harga + (harga * percentageData.beliluar / 100) + ppn),
            jualbebas: Math.round(harga + (harga * percentageData.jualbebas / 100) + ppn),
            karyawan: Math.round(harga + (harga * percentageData.karyawan / 100) + ppn)
        };
    };

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        
        // If changing h_beli, calculate all prices automatically
        if (name === 'h_beli' && value) {
            const calculatedPrices = calculatePrices(value);
            setFormData(prev => ({
                ...prev,
                [name]: value,
                ...calculatedPrices
            }));
        } else {
            setFormData(prev => ({
                ...prev,
                [name]: value
            }));
        }
        
        // Clear error when user starts typing
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: null
            }));
        }
    };
    
    // Handle PPN checkbox change
    const handlePPNChange = (e) => {
        const checked = e.target.checked;
        setIncludePPN(checked);
        
        // Recalculate prices if h_beli exists
        if (formData.h_beli) {
            const calculatedPrices = calculatePrices(formData.h_beli, checked);
            setFormData(prev => ({
                ...prev,
                ...calculatedPrices
            }));
        }
    };
    
    // Fetch dropdown data and generate item code when modal opens
    useEffect(() => {
        if (showModal) {
            const fetchData = async () => {
                try {
                    // Fetch dropdown data
                    const dropdownResponse = await axios.get('/farmasi/data-obat/dropdowns');
                    setDropdownData(dropdownResponse.data);
                    
                    // Fetch percentage data from setpenjualanumum
                    const percentageResponse = await axios.get('/api/set-harga-obat');
                    if (percentageResponse.data.success && percentageResponse.data.data) {
                        const data = percentageResponse.data.data;
                        setPercentageData({
                            ralan: parseFloat(data.ralan) || 0,
                            kelas1: parseFloat(data.kelas1) || 0,
                            kelas2: parseFloat(data.kelas2) || 0,
                            kelas3: parseFloat(data.kelas3) || 0,
                            utama: parseFloat(data.utama) || 0,
                            vip: parseFloat(data.vip) || 0,
                            vvip: parseFloat(data.vvip) || 0,
                            beliluar: parseFloat(data.beliluar) || 0,
                            jualbebas: parseFloat(data.jualbebas) || 0,
                            karyawan: parseFloat(data.karyawan) || 0
                        });
                    }
                    
                    // Auto code generation is now handled in openCreateModal
                } catch (error) {
                    console.error('Error fetching data:', error);
                }
            };
            
            fetchData();
        }
    }, [showModal, modalType]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            let response;
            if (modalType === 'create') {
                response = await axios.post('/farmasi/data-obat', formData);
            } else {
                response = await axios.put(`/farmasi/data-obat/${selectedItem.kode_brng}`, formData);
            }

            if ((response?.status >= 200 && response?.status < 400) || response?.data?.success) {
                toast(
                    modalType === 'create' 
                        ? 'Data obat berhasil ditambahkan!' 
                        : 'Data obat berhasil diperbarui!', 
                    'success'
                );
                closeModal();
                fetchList(search);
            }
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
                toast('Terjadi kesalahan saat menyimpan data. Periksa form kembali.', 'error');
            } else {
                toast('Terjadi kesalahan yang tidak terduga.', 'error');
            }
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (item) => {
        if (confirm('Apakah Anda yakin ingin menghapus data obat ini?')) {
            try {
                await axios.delete(`/farmasi/data-obat/${item.kode_brng}`);
                toast('Data obat berhasil dihapus!', 'success');
                fetchList(search);
            } catch (error) {
                console.error('Error deleting item:', error);
                toast('Gagal menghapus data obat.', 'error');
            }
        }
    };

    const formatCurrency = (value) => {
        if (!value) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };
    
    // Function to generate item code automatically
    const generateItemCode = async () => {
        try {
            const response = await axios.get('/farmasi/data-obat/next-code');
            const newCode = response?.data?.nextCode || null;
            if (newCode) {
                setFormData(prev => ({
                    ...prev,
                    kode_brng: newCode
                }));
                return newCode;
            }
            return null;
        } catch (error) {
            console.error('Error generating item code:', error);
            return null;
        }
    };

    // Fungsi untuk update harga jual berdasarkan harga beli terbaru dari pembelian
    const updateHargaJual = async (kodeBarang, hargaBeliBaru) => {
        try {
            // Fetch percentage data untuk perhitungan
            const percentageResponse = await axios.get('/api/set-harga-obat');
            if (!percentageResponse.data.success) {
                throw new Error('Gagal mengambil data persentase');
            }

            const percentages = percentageResponse.data.data;
            const harga = parseFloat(hargaBeliBaru) || 0;

            // Hitung harga jual berdasarkan persentase keuntungan
            const hargaJualBaru = {
                ralan: Math.round(harga + (harga * parseFloat(percentages.ralan || 0) / 100)),
                kelas1: Math.round(harga + (harga * parseFloat(percentages.kelas1 || 0) / 100)),
                kelas2: Math.round(harga + (harga * parseFloat(percentages.kelas2 || 0) / 100)),
                kelas3: Math.round(harga + (harga * parseFloat(percentages.kelas3 || 0) / 100)),
                utama: Math.round(harga + (harga * parseFloat(percentages.utama || 0) / 100)),
                vip: Math.round(harga + (harga * parseFloat(percentages.vip || 0) / 100)),
                vvip: Math.round(harga + (harga * parseFloat(percentages.vvip || 0) / 100)),
                beliluar: Math.round(harga + (harga * parseFloat(percentages.beliluar || 0) / 100)),
                jualbebas: Math.round(harga + (harga * parseFloat(percentages.jualbebas || 0) / 100)),
                karyawan: Math.round(harga + (harga * parseFloat(percentages.karyawan || 0) / 100))
            };

            // Update harga jual melalui API
            const updateData = {
                kode_brng: kodeBarang,
                h_beli: harga,
                ...hargaJualBaru
            };

            const response = await axios.put(`/api/databarang/update-harga-jual/${kodeBarang}`, updateData);
            
            if (response.data.success) {
                console.log(`Harga jual berhasil diupdate untuk ${kodeBarang}:`, hargaJualBaru);
                return true;
            } else {
                console.error(`Gagal update harga jual untuk ${kodeBarang}:`, response.data.message);
                return false;
            }
        } catch (error) {
            console.error('Error updating harga jual:', error);
            return false;
        }
    };

    useEffect(() => {
        document.title = 'Data Obat';
        fetchList('');
    }, []);

    return (
        <AppLayout>

            <div className="space-y-6 -mt-6 -mx-6 p-6">
                {/* Header */}
                <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div className="p-6">
                        <div className="flex justify-between items-center">
                            <div>
                                <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    Data Obat
                                </h2>
                                <p className="text-gray-600 dark:text-gray-400 mt-1">
                                    Kelola data obat dan barang medis
                                </p>
                            </div>
                            <button
                                onClick={openCreateModal}
                                className="bg-gradient-to-r from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300 shadow-md hover:shadow-lg font-medium text-sm whitespace-nowrap transform hover:scale-105"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4">
                                    <path fillRule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clipRule="evenodd" />
                                </svg>
                                <span>Tambah Obat</span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* Search and Filters */}
                <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div className="p-6">
                        <form onSubmit={handleSearch} className="flex gap-4">
                            <div className="flex-1">
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Cari berdasarkan kode, nama obat, atau satuan..."
                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                />
                            </div>
                            <button
                                type="submit"
                                className="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors"
                            >
                                Cari
                            </button>
                            {search && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        setSearch('');
                                        fetchList('');
                                    }}
                                    className="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg transition-colors"
                                >
                                    Reset
                                </button>
                            )}
                        </form>
                    </div>
                </div>

                {/* Data Table */}
                <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead className="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kode
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama Obat
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Satuan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Harga Beli
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Harga Ralan
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Update Terakhir
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                {dataBarang.data.map((item) => (
                                    <tr key={item.kode_brng} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {item.kode_brng}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {item.nama_brng}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {item.kode_sat}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {formatCurrency(item.h_beli)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {formatCurrency(item.ralan)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : '-'}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                item.status === '1' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            }`}>
                                                {item.status === '1' ? 'Aktif' : 'Non-Aktif'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div className="flex space-x-2">
                                                <button
                                                    onClick={() => openEditModal(item)}
                                                    className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(item)}
                                                    className="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {dataBarang.links && (
                        <div className="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                            <div className="flex items-center justify-between">
                                <div className="text-sm text-gray-700 dark:text-gray-300">
                                    Menampilkan {dataBarang.from} sampai {dataBarang.to} dari {dataBarang.total} data
                                </div>
                                <div className="flex space-x-2">
                                    {dataBarang.links.map((link, index) => (
                                        <button
                                            key={index}
                                            onClick={() => {
                                                if (!link.url) return;
                                                try {
                                                    const url = new URL(link.url, window.location.origin);
                                                    const q = url.searchParams.get('q') || '';
                                                    fetchList(q);
                                                } catch (e) {
                                                    fetchList(search);
                                                }
                                            }}
                                            disabled={!link.url}
                                            className={`px-3 py-1 text-sm rounded-md ${
                                                link.active
                                                    ? 'bg-blue-500 text-white'
                                                    : link.url
                                                    ? 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'
                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600'
                                            }`}
                                        >
                                            {link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    {modalType === 'create' ? 'Tambah Data Obat' : 'Edit Data Obat'}
                                </h3>
                                <button
                                    onClick={closeModal}
                                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                >
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    {/* 1. Kode Barang */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Kode Barang *
                                        </label>
                                        <div className="flex">
                                            <input
                                                type="text"
                                                name="kode_brng"
                                                value={formData.kode_brng}
                                                onChange={handleInputChange}
                                                disabled={modalType === 'edit'}
                                                className={`w-full px-3 py-2 border rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white ${
                                                    errors.kode_brng ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'
                                                }`}
                                            />
                                            {modalType === 'create' && (
                                                <button
                                                    type="button"
                                                    onClick={generateItemCode}
                                                    className="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-r-lg transition-colors duration-300"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fillRule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clipRule="evenodd" />
                                                    </svg>
                                                </button>
                                            )}
                                        </div>
                                        {errors.kode_brng && (
                                            <p className="mt-1 text-sm text-red-600">{errors.kode_brng[0]}</p>
                                        )}
                                    </div>

                                    {/* 2. Industri Farmasi */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Industri Farmasi
                                        </label>
                                        <select
                                            name="kode_industri"
                                            value={formData.kode_industri}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="">Pilih Industri</option>
                                            {dropdownData.industrifarmasi && dropdownData.industrifarmasi.map((item) => (
                                                <option key={item.kode_industri} value={item.kode_industri}>
                                                    {item.nama_industri}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* 3. Nama Barang */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Nama Barang *
                                        </label>
                                        <input
                                            type="text"
                                            name="nama_brng"
                                            value={formData.nama_brng}
                                            onChange={handleInputChange}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white ${
                                                errors.nama_brng ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'
                                            }`}
                                        />
                                        {errors.nama_brng && (
                                            <p className="mt-1 text-sm text-red-600">{errors.nama_brng[0]}</p>
                                        )}
                                    </div>

                                    {/* 4. Satuan Besar */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Satuan Besar
                                        </label>
                                        <select
                                            name="kode_satbesar"
                                            value={formData.kode_satbesar}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="">Pilih Satuan Besar</option>
                                            {dropdownData.kodesatuan && dropdownData.kodesatuan.map((item) => (
                                                <option key={item.kode_sat} value={item.kode_sat}>
                                                    {item.satuan}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* 6. Isi */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Isi
                                        </label>
                                        <input
                                            type="number"
                                            name="isi"
                                            value={formData.isi}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            step="0.01"
                                        />
                                    </div>

                                    {/* 7. Satuan Kecil */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Satuan Kecil *
                                        </label>
                                        <select
                                            name="kode_sat"
                                            value={formData.kode_sat}
                                            onChange={handleInputChange}
                                            className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white ${
                                                errors.kode_sat ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'
                                            }`}
                                        >
                                            <option value="">Pilih Satuan</option>
                                            {dropdownData.kodesatuan && dropdownData.kodesatuan.map((item) => (
                                                <option key={item.kode_sat} value={item.kode_sat}>
                                                    {item.satuan}
                                                </option>
                                            ))}
                                        </select>
                                        {errors.kode_sat && (
                                            <p className="mt-1 text-sm text-red-600">{errors.kode_sat[0]}</p>
                                        )}
                                    </div>
                                    
                                    {/* 8. Kapasitas */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Kapasitas
                                        </label>
                                        <input
                                            type="number"
                                            name="kapasitas"
                                            value={formData.kapasitas}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            step="0.01"
                                        />
                                    </div>

                                    {/* 9. Jenis */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Jenis
                                        </label>
                                        <select
                                            name="kdjns"
                                            value={formData.kdjns}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="">Pilih Jenis</option>
                                            {dropdownData.jenis && dropdownData.jenis.map((item) => (
                                                <option key={item.kdjns} value={item.kdjns}>
                                                    {item.nama}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* 10. Kategori */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Kategori Barang
                                        </label>
                                        <select
                                            name="kode_kategori"
                                            value={formData.kode_kategori}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="">Pilih Kategori</option>
                                            {dropdownData.kategori_barang && dropdownData.kategori_barang.map((item) => (
                                                <option key={item.kode} value={item.kode}>
                                                    {item.nama}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* 11. Golongan */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Golongan Barang
                                        </label>
                                        <select
                                            name="kode_golongan"
                                            value={formData.kode_golongan}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="">Pilih Golongan</option>
                                            {dropdownData.golongan_barang && dropdownData.golongan_barang.map((item) => (
                                                <option key={item.kode} value={item.kode}>
                                                    {item.nama}
                                                </option>
                                            ))}
                                        </select>
                                    </div>









                                    {/* Status */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Status
                                        </label>
                                        <select
                                            name="status"
                                            value={formData.status}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                            <option value="1">Aktif</option>
                                            <option value="0">Non-Aktif</option>
                                        </select>
                                    </div>
                                    



                                    {/* Letak Barang */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Letak Barang
                                        </label>
                                        <input
                                            type="text"
                                            name="letak_barang"
                                            value={formData.letak_barang}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            maxLength={100}
                                        />
                                    </div>

                                    {/* Harga Dasar */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Dasar
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="dasar"
                                                value={formData.dasar}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>



                                    {/* Harga Beli */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Beli
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="h_beli"
                                                value={formData.h_beli}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                            {errors.h_beli && (
                                                <p className="mt-1 text-sm text-red-600">{errors.h_beli[0]}</p>
                                            )}
                                        </div>
                                    </div>

                                    {/* Harga Ralan */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ralan
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="ralan"
                                                value={formData.ralan}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Kelas 1 */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Kelas 1
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="kelas1"
                                                value={formData.kelas1}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Kelas 2 */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Kelas 2
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="kelas2"
                                                value={formData.kelas2}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Kelas 3 */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Kelas 3
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="kelas3"
                                                value={formData.kelas3}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Utama/BPJS */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Utama/BPJS
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="utama"
                                                value={formData.utama}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Kelas VIP */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Kelas VIP
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="vip"
                                                value={formData.vip}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Ranap Kelas VVIP */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Ranap Kelas VVIP
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="vvip"
                                                value={formData.vvip}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Apotek Luar */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Apotek Luar
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="beliluar"
                                                value={formData.beliluar}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Jual Obat Bebas */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Jual Obat Bebas
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="jualbebas"
                                                value={formData.jualbebas}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Harga Karyawan */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Harga Karyawan
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp.</span>
                                            </div>
                                            <input
                                                type="number"
                                                name="karyawan"
                                                value={formData.karyawan}
                                                onChange={handleInputChange}
                                                className="w-full pl-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            />
                                        </div>
                                    </div>

                                    {/* Stok Minimal */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Stok Minimal
                                        </label>
                                        <input
                                            type="number"
                                            name="stokminimal"
                                            value={formData.stokminimal}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        />
                                    </div>

                                    {/* Tanggal Kadaluarsa */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Tanggal Kadaluarsa
                                        </label>
                                        <input
                                            type="date"
                                            name="expire"
                                            value={formData.expire}
                                            onChange={handleInputChange}
                                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        />
                                    </div>

                                </div>

                                {/* PPN Checkbox */}
                                <div className="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div className="flex items-center space-x-3">
                                        <input
                                            type="checkbox"
                                            id="includePPN"
                                            checked={includePPN}
                                            onChange={handlePPNChange}
                                            className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                        <label htmlFor="includePPN" className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Sertakan PPN 11% dalam perhitungan harga jual
                                        </label>
                                    </div>
                                    {includePPN && (
                                        <div className="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                            <span className="font-medium">Catatan:</span> Harga jual akan dihitung sebagai: Harga Beli + Keuntungan + PPN 11%
                                        </div>
                                    )}
                                </div>

                                {/* Action Buttons */}
                                <div className="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <button
                                        type="button"
                                        onClick={closeModal}
                                        className="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        {loading ? 'Menyimpan...' : (modalType === 'create' ? 'Simpan' : 'Update')}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
