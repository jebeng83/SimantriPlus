import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { router } from '@inertiajs/react';
import { toast } from '@/tools/toast';

export default function PembelianObat() {
    // State untuk form pembelian
    const [formData, setFormData] = useState({
        no_faktur: '',
        tgl_beli: new Date().toISOString().split('T')[0],
        kd_rek: '',
        kode_suplier: '',
        nip: '',
        kd_bangsal: '',
        total1: 0,
        potongan: 0,
        total2: 0,
        ppn: 0,
        tagihan: 0
    });

    // State untuk dropdown data individual
    const [suppliers, setSuppliers] = useState([]);
    const [pegawai, setPegawai] = useState([]);
    const [bangsal, setBangsal] = useState([]);
    const [akunBayar, setAkunBayar] = useState([]);

    // Fungsi untuk mengambil data dropdown
    const fetchDropdownData = async () => {
        try {
            // Fetch suppliers
            const suppliersResponse = await fetch('/api/pembelian/supplier');
            if (suppliersResponse.ok) {
                const suppliersResult = await suppliersResponse.json();
                if (suppliersResult.success) {
                    setSuppliers(suppliersResult.data);
                } else {
                    console.error('Failed to fetch suppliers:', suppliersResult.message);
                }
            } else {
                console.error('Suppliers API request failed:', suppliersResponse.status);
            }

            // Fetch pegawai
            const timestamp = new Date().getTime();
            const pegawaiResponse = await fetch(`/api/pembelian/petugas?t=${timestamp}`);
            if (pegawaiResponse.ok) {
                const pegawaiResult = await pegawaiResponse.json();
                if (pegawaiResult.success) {
                    setPegawai(pegawaiResult.data);
                } else {
                    console.error('Failed to fetch pegawai:', pegawaiResult.message);
                }
            } else {
                console.error('Pegawai API request failed:', pegawaiResponse.status);
            }

            // Fetch bangsal
            const bangsalResponse = await fetch('/api/pembelian/lokasi');
            if (bangsalResponse.ok) {
                const bangsalResult = await bangsalResponse.json();
                if (bangsalResult.success) {
                    setBangsal(bangsalResult.data);
                } else {
                    console.error('Failed to fetch bangsal:', bangsalResult.message);
                }
            } else {
                console.error('Bangsal API request failed:', bangsalResponse.status);
            }

            // Fetch akun bayar
            const akunBayarResponse = await fetch('/api/pembelian/akun-bayar');
            if (akunBayarResponse.ok) {
                const akunBayarResult = await akunBayarResponse.json();
                if (akunBayarResult.success) {
                    setAkunBayar(akunBayarResult.data);
                } else {
                    console.error('Failed to fetch akun bayar:', akunBayarResult.message);
                }
            } else {
                console.error('Akun bayar API request failed:', akunBayarResponse.status);
            }
        } catch (error) {
            console.error('Error fetching dropdown data:', error);
        }
    };

    // State untuk item pembelian
    const [items, setItems] = useState([]);

    // State untuk pencarian item
    const [showSearchModal, setShowSearchModal] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);

    // State untuk total pembelian
    const [totals, setTotals] = useState({
        subtotal: 0,
        grandTotal: 0
    });

    // Fungsi untuk generate nomor faktur
    const generateNoFaktur = async () => {
        try {
            // Tambahkan timestamp untuk mencegah caching
            const timestamp = new Date().getTime();
            const response = await fetch(`/api/pembelian/generate-no-faktur?t=${timestamp}`, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                return data.no_faktur;
            } else {
                console.error('Error generating no faktur:', data.message);
                // Fallback ke format lama jika API gagal
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                return `PB-${year}${month}${day}-${random}`;
            }
        } catch (error) {
            console.error('Error calling generate no faktur API:', error);
            // Fallback ke format lama jika API gagal
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `PB-${year}${month}${day}-${random}`;
        }
    };



    // Load data saat komponen dimount
    useEffect(() => {
        const initializeData = async () => {
            await fetchDropdownData();
            const noFaktur = await generateNoFaktur();
            setFormData(prev => ({
                ...prev,
                no_faktur: noFaktur
            }));
        };
        
        initializeData();
    }, []);

    // Fungsi untuk handle perubahan form
    const handleFormChange = (field, value) => {
        setFormData(prev => ({
            ...prev,
            [field]: value
        }));
    };

    // Fungsi untuk pencarian barang
    const searchBarang = async (term) => {
        if (term.length < 2) {
            setSearchResults([]);
            return;
        }

        try {
            const response = await fetch(`/api/barang/search?q=${encodeURIComponent(term)}`);
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    setSearchResults(result.data);
                } else {
                    console.error('Search failed:', result.message);
                    setSearchResults([]);
                }
            } else {
                console.error('Search request failed:', response.status);
                setSearchResults([]);
            }
        } catch (error) {
            console.error('Error searching barang:', error);
            setSearchResults([]);
        }
    };

    // Fungsi untuk menambah item ke pembelian
    const addItemToPurchase = (barang) => {
        const existingItemIndex = items.findIndex(item => item.kode_brng === barang.kode_brng);
        
        if (existingItemIndex >= 0) {
            // Jika item sudah ada, tambah jumlahnya
            const updatedItems = [...items];
            updatedItems[existingItemIndex].jumlah += 1;
            updatedItems[existingItemIndex].subtotal = updatedItems[existingItemIndex].jumlah * updatedItems[existingItemIndex].h_beli;
            updatedItems[existingItemIndex].total = updatedItems[existingItemIndex].subtotal - updatedItems[existingItemIndex].besardis;
            setItems(updatedItems);
        } else {
            // Jika item baru, tambahkan ke list
            const newItem = {
                kode_brng: barang.kode_brng,
                nama_brng: barang.nama_brng,
                kode_sat: barang.kode_sat || 'TAB',
                jumlah: 1,
                h_beli: barang.h_beli || 0,
                subtotal: barang.h_beli || 0,
                dis: 0,
                besardis: 0,
                total: barang.h_beli || 0,
                no_batch: 'BATCH001',
                kadaluarsa: new Date(Date.now() + 3 * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] // 3 tahun dari sekarang
            };
            setItems([...items, newItem]);
        }
        
        // Tutup modal dan reset pencarian
        setShowSearchModal(false);
        setSearchTerm('');
        setSearchResults([]);
    };

    // Fungsi untuk update item
    const updateItem = (index, field, value) => {
        const updatedItems = [...items];
        updatedItems[index][field] = value;
        
        // Recalculate subtotal dan total
        if (field === 'jumlah' || field === 'h_beli') {
            updatedItems[index].subtotal = updatedItems[index].jumlah * updatedItems[index].h_beli;
        }
        
        if (field === 'dis') {
            updatedItems[index].besardis = (updatedItems[index].h_beli * updatedItems[index].dis) / 100;
        }
        
        updatedItems[index].total = updatedItems[index].subtotal - updatedItems[index].besardis;
        
        setItems(updatedItems);
    };

    // Fungsi untuk hapus item
    const removeItem = (index) => {
        const updatedItems = items.filter((_, i) => i !== index);
        setItems(updatedItems);
    };

    // Fungsi untuk menghitung total
    const calculateTotals = () => {
        const subtotal = items.reduce((sum, item) => sum + (item.total || 0), 0);
        const afterDiscount = subtotal - (formData.potongan || 0);
        const ppnAmount = formData.ppn ? afterDiscount * 0.11 : 0;
        const grandTotal = afterDiscount + ppnAmount;
        
        setTotals({
            subtotal,
            grandTotal
        });
        
        // Update form data
        setFormData(prev => ({
            ...prev,
            total1: subtotal,
            total2: afterDiscount,
            ppn: ppnAmount,
            tagihan: grandTotal
        }));
    };

    // Fungsi untuk menghitung ulang total saat item berubah
    useEffect(() => {
        calculateTotals();
    }, [items]);

    // Fungsi untuk menghitung ulang total saat potongan berubah
    useEffect(() => {
        calculateTotals();
    }, [formData.potongan, formData.ppn]);

    // Fungsi untuk update harga databarang setelah pembelian
    const updateDataBarangPrices = async (purchaseItems) => {
        try {
            let updateSuccess = true;
            
            for (const item of purchaseItems) {
                // Pastikan h_beli adalah harga terbaru dari pembelian
                const hargaBeliBaru = parseFloat(item.h_beli) || 0;
                
                // Hitung harga dasar (harga beli - diskon)
                const hargaDasar = hargaBeliBaru - (parseFloat(item.besardis) || 0);
                
                const updateData = {
                    kode_brng: item.kode_brng,
                    dasar: hargaDasar,
                    h_beli: hargaBeliBaru
                };
                
                // Update databarang dengan harga terbaru
                const response = await fetch(`/api/databarang/update-harga/${item.kode_brng}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updateData)
                });
                
                if (response.ok) {
                    // Update harga jual berdasarkan harga beli/dasar terbaru
                    const hargaJualUpdated = await updateHargaJualFromPembelian(
                        item.kode_brng,
                        hargaBeliBaru,
                        hargaDasar // gunakan harga dasar sebagai base perhitungan bila tersedia
                    );
                    if (!hargaJualUpdated) {
                        updateSuccess = false;
                    }
                } else {
                    console.error(`Failed to update price for ${item.kode_brng}:`, response.statusText);
                    updateSuccess = false;
                }
            }
            
            if (updateSuccess) {
                toast('Harga databarang dan harga jual berhasil diupdate!', 'success');
            } else {
                toast('Beberapa harga gagal diupdate, silakan periksa kembali', 'warning');
            }
        } catch (error) {
            console.error('Error updating databarang prices:', error);
            toast('Gagal mengupdate harga databarang', 'error');
        }
    };

    // Fungsi untuk update harga jual berdasarkan harga beli terbaru dari pembelian
    const updateHargaJualFromPembelian = async (kodeBarang, hargaBeliBaru, baseHargaJual = null) => {
        try {
            // Fetch percentage data untuk perhitungan
            const percentageResponse = await fetch('/api/set-harga-obat');
            const percentageData = await percentageResponse.json();
            
            if (!percentageData.success) {
                throw new Error('Gagal mengambil data persentase');
            }

            const percentages = percentageData.data;
            const harga = parseFloat(hargaBeliBaru) || 0;
            const base = parseFloat(baseHargaJual ?? hargaBeliBaru) || 0;

            // Hitung harga jual berdasarkan persentase keuntungan
            const hargaJualBaru = {
                ralan: Math.round(base + (base * parseFloat(percentages.ralan || 0) / 100)),
                kelas1: Math.round(base + (base * parseFloat(percentages.kelas1 || 0) / 100)),
                kelas2: Math.round(base + (base * parseFloat(percentages.kelas2 || 0) / 100)),
                kelas3: Math.round(base + (base * parseFloat(percentages.kelas3 || 0) / 100)),
                utama: Math.round(base + (base * parseFloat(percentages.utama || 0) / 100)),
                vip: Math.round(base + (base * parseFloat(percentages.vip || 0) / 100)),
                vvip: Math.round(base + (base * parseFloat(percentages.vvip || 0) / 100)),
                beliluar: Math.round(base + (base * parseFloat(percentages.beliluar || 0) / 100)),
                jualbebas: Math.round(base + (base * parseFloat(percentages.jualbebas || 0) / 100)),
                karyawan: Math.round(base + (base * parseFloat(percentages.karyawan || 0) / 100))
            };

            // Update harga jual melalui API
            const updateData = {
                kode_brng: kodeBarang,
                h_beli: harga,
                ...hargaJualBaru
            };

            const response = await fetch(`/api/databarang/update-harga-jual/${kodeBarang}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updateData)
            });
            
            if (response.ok) {
                const result = await response.json();
                return true;
            } else {
                console.error(`Gagal update harga jual untuk ${kodeBarang}:`, response.statusText);
                return false;
            }
        } catch (error) {
            console.error('Error updating harga jual:', error);
            return false;
        }
    };

    // Fungsi untuk update stok gudang barang
    const updateGudangBarangStock = async (purchaseItems, kdBangsal, noFaktur) => {
        try {
            let updateSuccess = true;
            
            for (const item of purchaseItems) {
                const updateData = {
                    kode_brng: item.kode_brng,
                    kd_bangsal: kdBangsal,
                    stok: parseFloat(item.jumlah) || 0,
                    no_batch: item.no_batch || 'BATCH001',
                    no_faktur: noFaktur
                };
                
                // Update stok gudang barang melalui API
                const response = await fetch('/api/gudangbarang/update-stok', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updateData)
                });
                
                if (!response.ok) {
                    console.error(`Failed to update stock for ${item.kode_brng}:`, response.statusText);
                    updateSuccess = false;
                }
            }
            
            if (updateSuccess) {
                toast('Stok gudang barang berhasil diupdate!', 'success');
            } else {
                toast('Beberapa stok gudang gagal diupdate, silakan periksa kembali', 'warning');
            }
            
            return updateSuccess;
        } catch (error) {
            console.error('Error updating gudang barang stock:', error);
            toast('Gagal mengupdate stok gudang barang', 'error');
            return false;
        }
    };

    // Fungsi untuk menyimpan data pembelian
    const handleSubmit = async (e) => {
        e.preventDefault();
        
        // Validasi form
        if (!formData.no_faktur || !formData.tgl_beli || !formData.kode_suplier || 
            !formData.nip || !formData.kd_bangsal || !formData.kd_rek) {
            
            // Tampilkan pesan yang lebih spesifik
            let missingFields = [];
            if (!formData.no_faktur) missingFields.push('No Faktur');
            if (!formData.tgl_beli) missingFields.push('Tanggal Pembelian');
            if (!formData.kode_suplier) missingFields.push('Supplier');
            if (!formData.nip) missingFields.push('Petugas');
            if (!formData.kd_bangsal) missingFields.push('Lokasi');
            if (!formData.kd_rek) missingFields.push('Akun Bayar');
            
            toast(`Mohon lengkapi field: ${missingFields.join(', ')}`, 'error');
            return;
        }
        
        if (items.length === 0) {
            toast('Mohon tambahkan minimal satu item pembelian', 'error');
            return;
        }
        
        // Validasi item
        for (let item of items) {
            if (!item.jumlah || item.jumlah <= 0) {
                toast(`Jumlah untuk item ${item.nama_brng} harus lebih dari 0`, 'error');
                return;
            }
            if (!item.h_beli || item.h_beli <= 0) {
                toast(`Harga beli untuk item ${item.nama_brng} harus lebih dari 0`, 'error');
                return;
            }
        }
        
        try {
            const pembelianData = {
                no_faktur: formData.no_faktur,
                kode_suplier: formData.kode_suplier,
                nip: formData.nip,
                tgl_beli: formData.tgl_beli,
                kd_rek: formData.kd_rek,
                total1: formData.total1,
                potongan: formData.potongan,
                total2: formData.total2,
                ppn: formData.ppn,
                tagihan: formData.tagihan,
                kd_bangsal: formData.kd_bangsal,
                items: items.map(item => ({
                    kode_brng: item.kode_brng,
                    kode_sat: item.kode_sat || 'TAB',
                    no_batch: item.no_batch || 'BATCH001',
                    kadaluarsa: item.kadaluarsa || new Date(Date.now() + 3 * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    jumlah: item.jumlah,
                    harga: item.h_beli,
                    subtotal: item.subtotal,
                    dis: item.dis || 0,
                    besardis: item.besardis || 0,
                    total: item.total
                }))
            };
            
            const response = await fetch('/api/pembelian/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(pembelianData)
            });
            
            if (response.ok) {
                toast('Data pembelian berhasil disimpan!', 'success');
                
                // Siapkan data untuk update harga dengan struktur yang konsisten
                const itemsForUpdate = items.map(item => ({
                    kode_brng: item.kode_brng,
                    h_beli: item.h_beli,
                    dis: item.dis || 0,
                    besardis: item.besardis || 0
                }));
                
                // Update harga databarang setelah pembelian berhasil
                await updateDataBarangPrices(itemsForUpdate);
                
                // Update stok gudang barang setelah pembelian berhasil
                await updateGudangBarangStock(items, formData.kd_bangsal, formData.no_faktur);
                
                // Tunggu sebentar untuk menampilkan toast
                setTimeout(async () => {
                    // Generate nomor faktur baru
                    const newNoFaktur = await generateNoFaktur();
                    
                    // Reset form dengan nomor faktur baru
                    setFormData({
                        no_faktur: newNoFaktur,
                        tgl_beli: new Date().toISOString().split('T')[0],
                        kd_rek: '',
                        kode_suplier: '',
                        nip: '',
                        kd_bangsal: '',
                        total1: 0,
                        potongan: 0,
                        total2: 0,
                        ppn: 0,
                        tagihan: 0
                    });
                    setItems([]);
                    
                    // Tidak perlu refresh halaman, form sudah ter-reset dengan nomor faktur baru
                }, 1500); // Tunggu 1.5 detik untuk menampilkan toast
            } else {
                let errorMessage = 'Terjadi kesalahan';
                try {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const errorData = await response.json();
                        errorMessage = errorData.message || 'Terjadi kesalahan';
                    } else {
                        errorMessage = `Server error: ${response.status} ${response.statusText}`;
                    }
                } catch (parseError) {
                    errorMessage = `Server error: ${response.status} ${response.statusText}`;
                }
                toast('Gagal menyimpan data: ' + errorMessage, 'error');
            }
        } catch (error) {
            console.error('Error saving data:', error);
            toast('Terjadi kesalahan saat menyimpan data', 'error');
        }
    };

    return (
        <AppLayout>
            <Head title="Pembelian Obat" />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Form Pembelian Obat</h2>
                            </div>

                            {/* Form Header */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                {/* No Faktur */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        No Faktur *
                                    </label>
                                    <input
                                        type="text"
                                        value={formData.no_faktur}
                                        onChange={(e) => handleFormChange('no_faktur', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                {/* Tanggal */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Pembelian *
                                    </label>
                                    <input
                                        type="date"
                                        value={formData.tgl_beli}
                                        onChange={(e) => handleFormChange('tgl_beli', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                {/* Akun Bayar */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Akun Bayar *
                                    </label>
                                    <select
                                        value={formData.kd_rek}
                                        onChange={(e) => handleFormChange('kd_rek', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Pilih Akun Bayar</option>
                                        {akunBayar.map((akun, index) => (
                                            <option key={`${akun.kd_rek}-${akun.nama_bayar}-${index}`} value={akun.kd_rek}>
                                                {akun.kd_rek} - {akun.nama_bayar}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Supplier */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Supplier *
                                    </label>
                                    <select
                                        value={formData.kode_suplier}
                                        onChange={(e) => handleFormChange('kode_suplier', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Pilih Supplier</option>
                                        {suppliers.map((supplier) => (
                                            <option key={supplier.kode_suplier} value={supplier.kode_suplier}>
                                                {supplier.kode_suplier} - {supplier.nama_suplier}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Petugas */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Petugas *
                                    </label>
                                    <select
                                        value={formData.nip}
                                        onChange={(e) => handleFormChange('nip', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Pilih Petugas</option>
                                        {pegawai.map((petugas, index) => (
                                            <option key={`${petugas.nip}-${index}`} value={petugas.nip}>
                                                {petugas.nip} - {petugas.nama}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Lokasi */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Lokasi *
                                    </label>
                                    <select
                                        value={formData.kd_bangsal}
                                        onChange={(e) => handleFormChange('kd_bangsal', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Pilih Lokasi</option>
                                        {bangsal.map((lokasi) => (
                                            <option key={lokasi.kd_bangsal} value={lokasi.kd_bangsal}>
                                                {lokasi.kd_bangsal} - {lokasi.nm_bangsal}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            {/* Item Pembelian */}
                            <div className="mb-8">
                                <div className="flex justify-between items-center mb-4">
                                    <h3 className="text-lg font-semibold">Item Pembelian</h3>
                                    <button
                                        onClick={() => setShowSearchModal(true)}
                                        className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors"
                                    >
                                        + Tambah Item
                                    </button>
                                </div>

                                {/* Tabel Item */}
                                <div className="overflow-x-auto">
                                    <table className="min-w-full bg-white border border-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Kode
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Nama Barang
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Kode Sat
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    No Batch
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Kadaluarsa
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Jumlah
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Harga Beli
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Subtotal
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Diskon (%)
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Besar Diskon
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Total
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {items.length === 0 ? (
                                                <tr>
                                                    <td colSpan="12" className="px-4 py-8 text-center text-gray-500">
                                                        Belum ada item yang ditambahkan
                                                    </td>
                                                </tr>
                                            ) : (
                                                items.map((item, index) => (
                                                    <tr key={`${item.kode_brng}-${index}`} className="hover:bg-gray-50">
                                                        <td className="px-4 py-3 text-sm text-gray-900 border-b">
                                                            {item.kode_brng}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-900 border-b">
                                                            {item.nama_brng}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="text"
                                                                value={item.kode_sat || ''}
                                                                onChange={(e) => updateItem(index, 'kode_sat', e.target.value)}
                                                                className="w-20 px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                placeholder="TAB"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="text"
                                                                value={item.no_batch || ''}
                                                                onChange={(e) => updateItem(index, 'no_batch', e.target.value)}
                                                                className="w-24 px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                placeholder="BATCH001"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="date"
                                                                value={item.kadaluarsa || ''}
                                                                onChange={(e) => updateItem(index, 'kadaluarsa', e.target.value)}
                                                                className="w-32 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="number"
                                                                value={item.jumlah}
                                                                onChange={(e) => updateItem(index, 'jumlah', parseInt(e.target.value) || 0)}
                                                                className="w-20 px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                min="1"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="number"
                                                                value={item.h_beli}
                                                                onChange={(e) => updateItem(index, 'h_beli', parseFloat(e.target.value) || 0)}
                                                                className="w-24 px-2 py-1 border border-gray-300 rounded text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                min="0"
                                                                step="0.01"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-900 border-b text-right">
                                                            Rp {(item.subtotal || 0).toLocaleString()}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <input
                                                                type="number"
                                                                value={item.dis}
                                                                onChange={(e) => updateItem(index, 'dis', parseFloat(e.target.value) || 0)}
                                                                className="w-16 px-2 py-1 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                min="0"
                                                                max="100"
                                                                step="0.1"
                                                            />
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-900 border-b text-right">
                                                            Rp {(item.besardis || 0).toLocaleString()}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-900 border-b text-right font-semibold">
                                                            Rp {(item.total || 0).toLocaleString()}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm border-b">
                                                            <button
                                                                onClick={() => removeItem(index)}
                                                                className="text-red-600 hover:text-red-800 font-medium"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                ))
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {/* Total Pembelian */}
                            <div className="bg-gray-50 p-6 rounded-lg mb-6">
                                <h3 className="text-lg font-semibold mb-4">Total Pembelian</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="space-y-4">
                                        <div className="flex justify-between">
                                            <span className="font-medium">Subtotal:</span>
                                            <span className="font-semibold">Rp {totals.subtotal.toLocaleString()}</span>
                                        </div>
                                        
                                        <div className="flex justify-between items-center">
                                            <span className="font-medium">Potongan:</span>
                                            <input
                                                type="number"
                                                value={formData.potongan}
                                                onChange={(e) => handleFormChange('potongan', parseFloat(e.target.value) || 0)}
                                                className="w-32 px-3 py-1 border border-gray-300 rounded text-right focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                min="0"
                                                step="0.01"
                                            />
                                        </div>
                                        
                                        <div className="flex justify-between items-center">
                                            <span className="font-medium">PPN (11%):</span>
                                            <label className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={formData.ppn > 0}
                                                    onChange={(e) => {
                                                        const afterDiscount = totals.subtotal - (formData.potongan || 0);
                                                        const ppnAmount = e.target.checked ? afterDiscount * 0.11 : 0;
                                                        handleFormChange('ppn', ppnAmount);
                                                    }}
                                                    className="mr-2"
                                                />
                                                <span>Rp {(formData.ppn || 0).toLocaleString()}</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div className="flex justify-end items-end">
                                        <div className="text-right">
                                            <div className="text-sm text-gray-600 mb-1">Grand Total</div>
                                            <div className="text-3xl font-bold text-green-600">
                                                Rp {totals.grandTotal.toLocaleString()}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Tombol Aksi */}
                            <div className="flex justify-end space-x-4">
                                <button
                                    onClick={async () => {
                                        const newNoFaktur = await generateNoFaktur();
                                        setFormData({
                                            no_faktur: newNoFaktur,
                                            tgl_beli: new Date().toISOString().split('T')[0],
                                            kd_rek: '',
                                            kode_suplier: '',
                                            nip: '',
                                            kd_bangsal: '',
                                            total1: 0,
                                            potongan: 0,
                                            total2: 0,
                                            ppn: 0,
                                            tagihan: 0
                                        });
                                        setItems([]);
                                        toast('Form berhasil direset dengan nomor faktur baru', 'success');
                                    }}
                                    className="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors"
                                >
                                    Reset
                                </button>
                                <button
                                    onClick={handleSubmit}
                                    className="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors"
                                >
                                    Simpan Pembelian
                                </button>
                            </div>

                            {/* Modal Pencarian Item */}
                            {showSearchModal && (
                                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div className="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[80vh] overflow-hidden">
                                        <div className="flex justify-between items-center mb-4">
                                            <h3 className="text-lg font-semibold">Pencarian Item Barang</h3>
                                            <button
                                                onClick={() => {
                                                    setShowSearchModal(false);
                                                    setSearchTerm('');
                                                    setSearchResults([]);
                                                }}
                                                className="text-gray-500 hover:text-gray-700"
                                            >
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        {/* Search Input */}
                                        <div className="mb-4">
                                            <input
                                                type="text"
                                                value={searchTerm}
                                                onChange={(e) => {
                                                    setSearchTerm(e.target.value);
                                                    searchBarang(e.target.value);
                                                }}
                                                placeholder="Cari nama barang atau kode barang..."
                                                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                autoFocus
                                            />
                                        </div>

                                        {/* Search Results */}
                                        <div className="overflow-y-auto max-h-96">
                                            {searchResults.length === 0 && searchTerm.length >= 2 ? (
                                                <div className="text-center py-8 text-gray-500">
                                                    Tidak ada barang yang ditemukan
                                                </div>
                                            ) : searchResults.length === 0 ? (
                                                <div className="text-center py-8 text-gray-500">
                                                    Ketik minimal 2 karakter untuk mencari barang
                                                </div>
                                            ) : (
                                                <div className="space-y-2">
                                                    {searchResults.map((barang) => (
                                                        <div
                                                            key={barang.kode_brng}
                                                            className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer"
                                                            onClick={() => addItemToPurchase(barang)}
                                                        >
                                                            <div className="flex justify-between items-start">
                                                                <div className="flex-1">
                                                                    <div className="font-medium text-gray-900">
                                                                        {barang.nama_brng}
                                                                    </div>
                                                                    <div className="text-sm text-gray-600">
                                                                        Kode: {barang.kode_brng} | Satuan: {barang.kode_sat}
                                                                    </div>
                                                                    {barang.letak_barang && (
                                                                        <div className="text-sm text-gray-500">
                                                                            Lokasi: {barang.letak_barang}
                                                                        </div>
                                                                    )}
                                                                </div>
                                                                <div className="text-right">
                                                                    <div className="font-semibold text-green-600">
                                                                        Rp {(barang.h_beli || 0).toLocaleString()}
                                                                    </div>
                                                                    <div className="text-sm text-gray-500">
                                                                        Stok: {barang.stokminimal || 0}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>

                                        {/* Modal Footer */}
                                        <div className="mt-6 flex justify-end">
                                            <button
                                                onClick={() => {
                                                    setShowSearchModal(false);
                                                    setSearchTerm('');
                                                    setSearchResults([]);
                                                }}
                                                className="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors"
                                            >
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};