import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { todayDateString } from '@/tools/datetime';
import { Loader2, Save, Search, Plus, Trash2, X } from 'lucide-react';
import axios from 'axios';
import { toast } from '@/tools/toast';
import { 
    Card, 
    CardHeader, 
    CardTitle, 
    CardContent, 
    Button, 
    Input, 
    Label, 
    Textarea, 
    Table, 
    TableHeader, 
    TableBody, 
    TableRow, 
    TableHead, 
    TableCell, 
    Badge 
} from '@/components/ui';

export default function StokOpname({ auth }) {
    const [formData, setFormData] = useState({
        tanggal: todayDateString(),
        kd_bangsal: '',
        keterangan: ''
    });
    
    const [lokasi, setLokasi] = useState([]);
    const [opnameItems, setOpnameItems] = useState([]);
    const [loading, setLoading] = useState(false);
    // state untuk efek motion ketika halaman dimuat
    const [mounted, setMounted] = useState(false);
    
    // State untuk modal pencarian barang
    const [showSearchModal, setShowSearchModal] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [loadingSearch, setLoadingSearch] = useState(false);

    // State & handlers untuk modal tambah obat
    const [showAddObatModal, setShowAddObatModal] = useState(false);
    const [savingObat, setSavingObat] = useState(false);
    const [obatForm, setObatForm] = useState({
        // Identitas & satuan
        kode_brng: '',
        nama_brng: '',
        kode_sat: '',
        kode_satbesar: 'UNIT',
        // Harga
        dasar: '',
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
        // Lain-lain
        isi: '1',
        kapasitas: '0',
        status: '1',
        letak_barang: 'Apotek',
        stokminimal: '',
        kdjns: '',
        expire: '',
        kode_industri: '',
        kode_kategori: '',
        kode_golongan: '',
    });

    const openAddObatModal = () => setShowAddObatModal(true);
    const closeAddObatModal = () => setShowAddObatModal(false);
    const handleObatChange = (field, value) => {
        setObatForm(prev => ({ ...prev, [field]: value }));
    };

    const submitObatBaru = async () => {
        // Validasi minimal sesuai DataBarangController
        if (!obatForm.kode_brng || !obatForm.nama_brng || !obatForm.dasar) {
            toast('Kode barang, nama barang, dan harga dasar wajib diisi', 'error');
            return;
        }
        try {
            setSavingObat(true);
            const payload = {
                // identitas & satuan
                kode_brng: obatForm.kode_brng,
                nama_brng: obatForm.nama_brng,
                kode_sat: obatForm.kode_sat || undefined,
                kode_satbesar: obatForm.kode_satbesar || 'UNIT',
                // harga
                dasar: parseFloat(obatForm.dasar) || 0,
                h_beli: obatForm.h_beli !== '' ? parseFloat(obatForm.h_beli) : undefined,
                ralan: obatForm.ralan !== '' ? parseFloat(obatForm.ralan) : undefined,
                kelas1: obatForm.kelas1 !== '' ? parseFloat(obatForm.kelas1) : undefined,
                kelas2: obatForm.kelas2 !== '' ? parseFloat(obatForm.kelas2) : undefined,
                kelas3: obatForm.kelas3 !== '' ? parseFloat(obatForm.kelas3) : undefined,
                utama: obatForm.utama !== '' ? parseFloat(obatForm.utama) : undefined,
                vip: obatForm.vip !== '' ? parseFloat(obatForm.vip) : undefined,
                vvip: obatForm.vvip !== '' ? parseFloat(obatForm.vvip) : undefined,
                beliluar: obatForm.beliluar !== '' ? parseFloat(obatForm.beliluar) : undefined,
                jualbebas: obatForm.jualbebas !== '' ? parseFloat(obatForm.jualbebas) : undefined,
                karyawan: obatForm.karyawan !== '' ? parseFloat(obatForm.karyawan) : undefined,
                // lain-lain
                isi: obatForm.isi ? parseInt(obatForm.isi) : 1,
                kapasitas: obatForm.kapasitas ? parseInt(obatForm.kapasitas) : 0,
                status: obatForm.status || '1',
                letak_barang: obatForm.letak_barang || 'Apotek',
                stokminimal: obatForm.stokminimal !== '' ? parseInt(obatForm.stokminimal) : undefined,
                kdjns: obatForm.kdjns || undefined,
                expire: obatForm.expire || undefined,
                kode_industri: obatForm.kode_industri || undefined,
                kode_kategori: obatForm.kode_kategori || undefined,
                kode_golongan: obatForm.kode_golongan || undefined,
            };
            const res = await axios.post('/farmasi/data-obat', payload);
            if (res?.data?.success) {
                toast('Obat berhasil ditambahkan', 'success');
                setShowAddObatModal(false);
                setObatForm({
                    kode_brng: '',
                    nama_brng: '',
                    kode_sat: '',
                    kode_satbesar: 'UNIT',
                    dasar: '',
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
                    isi: '1',
                    kapasitas: '0',
                    status: '1',
                    letak_barang: 'Apotek',
                    stokminimal: '',
                    kdjns: '',
                    expire: '',
                    kode_industri: '',
                    kode_kategori: '',
                    kode_golongan: '',
                });
                // Opsional: tambahkan ke hasil pencarian agar bisa langsung dipilih
                // if (res.data.data) setSearchResults(prev => [res.data.data, ...prev]);
            } else {
                toast(res?.data?.message || 'Gagal menambahkan obat', 'error');
            }
        } catch (error) {
            console.error('Error tambah obat:', error);
            const msg = error?.response?.data?.message || 'Terjadi kesalahan saat menambahkan obat';
            toast(msg, 'error');
        } finally {
            setSavingObat(false);
        }
    };

    // Load data lokasi saat component mount + aktifkan efek motion
    useEffect(() => {
        loadLokasi();
        // aktifkan animasi halus setelah komponen ter-mount
        const t = setTimeout(() => setMounted(true), 50);
        return () => clearTimeout(t);
    }, []);

    const loadLokasi = async () => {
        try {
            const response = await axios.get('/api/opname/lokasi');
            if (response.data.success) {
                setLokasi(response.data.data);
            }
        } catch (error) {
            console.error('Error loading lokasi:', error);
            toast('Gagal memuat data lokasi', 'error');
        }
    };

    // Fungsi untuk pencarian barang
    const searchBarang = async (term) => {
        if (term.length < 2) {
            setSearchResults([]);
            return;
        }

        if (!formData.kd_bangsal) {
            toast('Pilih lokasi terlebih dahulu', 'error');
            return;
        }

        setLoadingSearch(true);
        try {
            const response = await axios.get('/api/opname/data-barang', {
                params: { 
                    kd_bangsal: formData.kd_bangsal,
                    search: term
                }
            });
            
            if (response.data.success) {
                setSearchResults(response.data.data);
            } else {
                setSearchResults([]);
            }
        } catch (error) {
            console.error('Error searching barang:', error);
            setSearchResults([]);
        } finally {
            setLoadingSearch(false);
        }
    };

    // Fungsi untuk menambah item ke opname
    const addItemToOpname = (barang) => {
        const existingItemIndex = opnameItems.findIndex(item => item.kode_brng === barang.kode_brng);
        
        if (existingItemIndex >= 0) {
            toast('Barang sudah ada dalam daftar opname', 'error');
            return;
        }

        const newItem = {
            ...barang,
            real: barang.stok, // Default real = stok sistem
            selisih: 0,
            lebih: 0,
            nomihilang: 0,
            nomilebih: 0,
            totalreal: barang.stok * barang.harga,
            no_batch: barang.no_batch || '',
            no_faktur: barang.no_faktur || ''
        };
        
        setOpnameItems([...opnameItems, newItem]);
        
        // Tutup modal dan reset pencarian
        setShowSearchModal(false);
        setSearchTerm('');
        setSearchResults([]);
        toast(`Barang ${barang.nama_brng} berhasil ditambahkan`, 'success');
    };

    // Fungsi untuk membuka modal pencarian
    const openSearchModal = () => {
        if (!formData.kd_bangsal) {
            toast('Pilih lokasi terlebih dahulu', 'error');
            return;
        }
        setShowSearchModal(true);
    };

    // Fungsi untuk menghapus item dari opname
    const removeItemFromOpname = (index) => {
        const updatedItems = opnameItems.filter((_, i) => i !== index);
        setOpnameItems(updatedItems);
        toast('Item berhasil dihapus dari daftar opname', 'success');
    };

    const handleRealChange = (index, value) => {
        const newItems = [...opnameItems];
        const real = parseFloat(value) || 0;
        const stok = newItems[index].stok;
        const harga = newItems[index].harga;
        
        newItems[index].real = real;
        newItems[index].selisih = real - stok;
        newItems[index].lebih = newItems[index].selisih > 0 ? newItems[index].selisih : 0;
        newItems[index].nomihilang = newItems[index].selisih < 0 ? Math.abs(newItems[index].selisih) * harga : 0;
        newItems[index].nomilebih = newItems[index].selisih > 0 ? newItems[index].selisih * harga : 0;
        newItems[index].totalreal = real * harga;
        
        setOpnameItems(newItems);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        // Validasi seperti contoh Java
        if (!formData.kd_bangsal || formData.kd_bangsal.trim() === '') {
            toast('Lokasi wajib diisi', 'error');
            return;
        }

        if (!formData.keterangan || formData.keterangan.trim() === '') {
            toast('Keterangan wajib diisi', 'error');
            return;
        }

        if (opnameItems.length === 0) {
            toast('Maaf, data kosong..!!!!', 'error');
            return;
        }

        // Cek tiap baris real tidak negatif
        for (let i = 0; i < opnameItems.length; i++) {
            const val = Number(opnameItems[i].real || 0);
            if (val < 0) {
                toast(`${opnameItems[i].kode_brng} ${opnameItems[i].nama_brng} memiliki nilai real < 0`, 'error');
                return;
            }
        }

        // Konfirmasi sebelum simpan
        const ok = window.confirm('Eeiiiiiits, udah bener belum data yang mau disimpan..??');
        if (!ok) return;

        setLoading(true);
        try {
            const payload = {
                tanggal: formData.tanggal,
                kd_bangsal: formData.kd_bangsal,
                keterangan: formData.keterangan,
                items: opnameItems.map(item => ({
                    kode_brng: item.kode_brng,
                    real: item.real,
                    h_beli: item.harga,
                    no_batch: item.no_batch || '',
                    no_faktur: item.no_faktur || ''
                }))
            };

            const response = await axios.post('/api/opname/store', payload);

            if (response.data.success) {
                toast('Data stok opname berhasil disimpan', 'success');
                // Reset form dan data sesuai praktik Java
                setFormData({
                    tanggal: todayDateString(),
                    kd_bangsal: '',
                    keterangan: ''
                });
                setOpnameItems([]);
            }
        } catch (error) {
            console.error('Error saving opname:', error);
            toast(error.response?.data?.message || 'Gagal menyimpan data stok opname', 'error');
        } finally {
            setLoading(false);
        }
    };

    const resetForm = () => {
        setFormData({
            tanggal: todayDateString(),
            kd_bangsal: '',
            keterangan: ''
        });
        setOpnameItems([]);
    };

    // useEffect untuk pencarian barang
    useEffect(() => {
        if (searchTerm && showSearchModal && formData.kd_bangsal) {
            const timeoutId = setTimeout(() => {
                searchBarang(searchTerm);
            }, 500);
            return () => clearTimeout(timeoutId);
        } else if (searchTerm && showSearchModal && !formData.kd_bangsal) {
            setSearchResults([]);
        }
    }, [searchTerm, showSearchModal, formData.kd_bangsal]);

    const formatRupiah = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    };

    const getTotalNominalHilang = () => {
        return opnameItems.reduce((total, item) => total + (item.nomihilang || 0), 0);
    };

    const getTotalNominalLebih = () => {
        return opnameItems.reduce((total, item) => total + (item.nomilebih || 0), 0);
    };

    return (
        <AppLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Stok Opname</h2>}
        >
            <Head title="Stok Opname" />

            <div className="py-6">
                <div className="max-w-full mx-auto px-0">


                    <Card className={`mb-6 transition-all duration-500 ease-out ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'} hover:shadow-lg hover:-translate-y-0.5`}>
                        <CardHeader>
                            <CardTitle>Form Stok Opname</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <Label htmlFor="tanggal">Tanggal Opname</Label>
                                        <Input
                                            id="tanggal"
                                            type="date"
                                            value={formData.tanggal}
                                            onChange={(e) => setFormData({...formData, tanggal: e.target.value})}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <Label htmlFor="lokasi">Lokasi/Bangsal</Label>
                                        <select
                                            id="lokasi"
                                            className="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300 ease-out hover:border-blue-400"
                                            value={formData.kd_bangsal}
                                            onChange={(e) => setFormData({ ...formData, kd_bangsal: e.target.value })}
                                        >
                                            <option value="" disabled>Pilih Lokasi</option>
                                            {lokasi.map((item) => (
                                                <option key={item.kd_bangsal} value={item.kd_bangsal}>
                                                    {item.nm_bangsal}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <Label htmlFor="keterangan">Keterangan</Label>
                                        <Textarea
                                            id="keterangan"
                                            value={formData.keterangan}
                                            onChange={(e) => setFormData({...formData, keterangan: e.target.value})}
                                            rows={2}
                                            placeholder="Keterangan opname..."
                                            className="transition-shadow duration-300 ease-out hover:shadow-sm"
                                        />
                                    </div>
                                </div>
                                <div className="flex gap-2">
                                    <Button 
                                        onClick={openSearchModal}
                                        disabled={!formData.kd_bangsal}
                                        className="transition-transform duration-300 ease-out hover:scale-[1.02]"
                                    >
                                        <Search className="mr-2 h-4 w-4" /> Cari Data Obat
                                    </Button>
                                    <Button 
                                        onClick={() => router.visit('/farmasi/data-opname')}
                                        variant="outline"
                                        className="transition-transform duration-300 ease-out hover:scale-[1.02]"
                                    >
                                        Data Opname
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {opnameItems.length > 0 && (
                        <Card className={`transition-all duration-500 ease-out ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'} hover:shadow-lg`}>
                            <CardHeader className="flex flex-row items-center justify-between">
                                <CardTitle>Data Barang Stok Opname</CardTitle>
                                <div className="flex gap-2">
                                    <Button 
                                        onClick={openAddObatModal}
                                        variant="outline"
                                        className="transition-transform duration-300 ease-out hover:scale-[1.02]"
                                    >
                                        <Plus className="mr-2 h-4 w-4" /> + Obat
                                    </Button>
                                    <Button 
                                        onClick={handleSubmit} 
                                        disabled={loading}
                                        className="transition-transform duration-300 ease-out hover:scale-[1.02]"
                                    >
                                        {loading ? (
                                            <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Menyimpan...</>
                                        ) : (
                                            <><Save className="mr-2 h-4 w-4" /> Simpan Opname</>
                                        )}
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <strong>Tanggal:</strong> {formData.tanggal}
                                    </div>
                                    <div>
                                        <strong>Lokasi:</strong> {lokasi.find(l => l.kd_bangsal === formData.kd_bangsal)?.nm_bangsal}
                                    </div>
                                    <div>
                                        <strong>Keterangan:</strong> {formData.keterangan || '-'}
                                    </div>
                                </div>

                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>No</TableHead>
                                                <TableHead>Kode Barang</TableHead>
                                                <TableHead>Nama Barang</TableHead>
                                                <TableHead>Jenis</TableHead>
                                                <TableHead>Satuan</TableHead>
                                                <TableHead>Harga</TableHead>
                                                <TableHead>Stok</TableHead>
                                                <TableHead className="text-base text-center min-w-[200px]">Real</TableHead>
                                                <TableHead>Selisih</TableHead>
                                                <TableHead>Lebih</TableHead>
                                                <TableHead>Nominal Hilang</TableHead>
                                                <TableHead>Nominal Lebih</TableHead>
                                                <TableHead>No Batch</TableHead>
                                                <TableHead>No Faktur</TableHead>
                                                <TableHead>Aksi</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {opnameItems.map((item, index) => (
                                                <TableRow
                                                    key={index}
                                                    className={`transition-all duration-300 ease-out ${mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'}`}
                                                    style={{ transitionDelay: `${index * 40}ms` }}
                                                >
                                                    <TableCell>{index + 1}</TableCell>
                                                    <TableCell className="font-mono">{item.kode_brng}</TableCell>
                                                    <TableCell>{item.nama_brng}</TableCell>
                                                    <TableCell>{item.jenis}</TableCell>
                                                    <TableCell>{item.satuan}</TableCell>
                                                    <TableCell>{formatRupiah(item.harga)}</TableCell>
                                                    <TableCell>{item.stok}</TableCell>
                                                    <TableCell>
                                                        <Input
                                                            type="number"
                                                            value={item.real}
                                                            onChange={(e) => handleRealChange(index, e.target.value)}
                                                            className="w-48 h-12 text-center font-bold text-xl px-4 py-3 border-2 transition-shadow duration-300 ease-out hover:shadow-sm"
                                                            min="0"
                                                            step="0.01"
                                                        />
                                                    </TableCell>
                                                    <TableCell>
                                                        <Badge variant={item.selisih === 0 ? 'secondary' : item.selisih > 0 ? 'default' : 'destructive'}>
                                                            {item.selisih}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell>{item.lebih}</TableCell>
                                                    <TableCell className="text-red-600">{formatRupiah(item.nomihilang)}</TableCell>
                                                    <TableCell className="text-green-600">{formatRupiah(item.nomilebih)}</TableCell>
                                                    <TableCell>{item.no_batch || '-'}</TableCell>
                                                    <TableCell>{item.no_faktur || '-'}</TableCell>
                                                    <TableCell>
                                                        <Button
                                                            onClick={() => removeItemFromOpname(index)}
                                                            variant="destructive"
                                                            size="sm"
                                                            className="transition-transform duration-300 ease-out hover:scale-[1.02]"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>

                                <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                                    <div className="text-red-600">
                                        <strong>Total Nominal Hilang: {formatRupiah(getTotalNominalHilang())}</strong>
                                    </div>
                                    <div className="text-green-600">
                                        <strong>Total Nominal Lebih: {formatRupiah(getTotalNominalLebih())}</strong>
                                    </div>
                                </div>
                             </CardContent>
                         </Card>
                     )}
                 </div>
            </div>

            {/* Modal Pencarian Barang */}
            {showSearchModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
                    <div className="bg-white rounded-lg p-6 w-full max-w-5xl max-h-[80vh] overflow-hidden transform transition-all duration-300 ease-out scale-100">
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-lg font-semibold">Cari Data Obat</h3>
                            <Button
                                onClick={() => setShowSearchModal(false)}
                                variant="ghost"
                                size="sm"
                                className="transition-transform duration-300 ease-out hover:scale-[1.05]"
                            >
                                <X className="h-4 w-4" />
                            </Button>
                        </div>
                        
                        <div className="mb-4">
                            <Input
                                type="text"
                                placeholder="Cari nama obat atau kode..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full transition-shadow duration-300 ease-out hover:shadow-sm"
                            />
                        </div>

                        <div className="overflow-auto max-h-96">
                            {loadingSearch ? (
                                <div className="flex justify-center items-center py-8">
                                    <Loader2 className="h-6 w-6 animate-spin" />
                                    <span className="ml-2">Mencari data obat...</span>
                                </div>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Kode</TableHead>
                                            <TableHead>Nama Obat</TableHead>
                                            <TableHead>Jenis</TableHead>
                                            <TableHead>Satuan</TableHead>
                                            <TableHead>Harga</TableHead>
                                            <TableHead>Stok</TableHead>
                                            <TableHead>Aksi</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {searchResults.map((item, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-mono">{item.kode_brng}</TableCell>
                                                <TableCell>{item.nama_brng}</TableCell>
                                                <TableCell>{item.jenis}</TableCell>
                                                <TableCell>{item.satuan}</TableCell>
                                                <TableCell>{formatRupiah(item.harga)}</TableCell>
                                                <TableCell>{item.stok}</TableCell>
                                                <TableCell>
                                                    <Button
                                                        onClick={() => addItemToOpname(item)}
                                                        size="sm"
                                                        disabled={opnameItems.some(opnameItem => opnameItem.kode_brng === item.kode_brng)}
                                                    >
                                                        {opnameItems.some(opnameItem => opnameItem.kode_brng === item.kode_brng) ? 'Sudah Ditambah' : 'Tambah'}
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                        {searchResults.length === 0 && searchTerm && !loadingSearch && formData.kd_bangsal && (
                            <TableRow>
                                <TableCell colSpan={7} className="text-center py-4">
                                    Tidak ada data obat yang ditemukan
                                </TableCell>
                            </TableRow>
                        )}
                        {!formData.kd_bangsal && (
                            <TableRow>
                                <TableCell colSpan={7} className="text-center py-4 text-gray-500">
                                    Silakan pilih lokasi terlebih dahulu untuk mencari data obat
                                </TableCell>
                            </TableRow>
                        )}
                        {!searchTerm && formData.kd_bangsal && (
                            <TableRow>
                                <TableCell colSpan={7} className="text-center py-4 text-gray-500">
                                    Ketik nama obat atau kode untuk mencari
                                </TableCell>
                            </TableRow>
                        )}
                                    </TableBody>
                                </Table>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Modal Tambah Obat (ringkas, mengikuti dataobat.jsx) */}
            <AnimatePresence>
                {showAddObatModal && (
                    <motion.div className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm overflow-y-auto p-4 flex items-center justify-center" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
                        <motion.div initial={{ y: 50, opacity: 0 }} animate={{ y: 0, opacity: 1 }} exit={{ y: 50, opacity: 0 }} transition={{ type: 'spring', stiffness: 260, damping: 20 }} className="mx-4 w-full max-w-xl rounded-xl bg-white p-6 shadow-xl">
                            <div className="flex justify-between items-center mb-2">
                                <h3 className="text-lg font-semibold">Tambah Obat</h3>
                                <button onClick={closeAddObatModal} className="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Tutup"><X className="h-4 w-4" /></button>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                {/* Identitas & Satuan */}
                                <div>
                                    <Label htmlFor="kode_brng_modal">Kode Barang</Label>
                                    <Input id="kode_brng_modal" value={obatForm.kode_brng} onChange={(e) => handleObatChange('kode_brng', e.target.value.toUpperCase())} placeholder="Mis. A000000001" />
                                </div>
                                <div>
                                    <Label htmlFor="nama_brng_modal">Nama Barang</Label>
                                    <Input id="nama_brng_modal" value={obatForm.nama_brng} onChange={(e) => handleObatChange('nama_brng', e.target.value)} placeholder="Nama obat" />
                                </div>
                                <div>
                                    <Label htmlFor="kode_sat_modal">Kode Satuan</Label>
                                    <Input id="kode_sat_modal" value={obatForm.kode_sat} onChange={(e) => handleObatChange('kode_sat', e.target.value)} placeholder="Mis. UNIT" />
                                </div>
                                <div>
                                    <Label htmlFor="kode_satbesar_modal">Kode Satuan Besar</Label>
                                    <Input id="kode_satbesar_modal" value={obatForm.kode_satbesar} onChange={(e) => handleObatChange('kode_satbesar', e.target.value)} placeholder="Mis. UNIT" />
                                </div>

                                {/* Harga */}
                                <div>
                                    <Label htmlFor="dasar_modal">Harga Dasar</Label>
                                    <Input id="dasar_modal" type="number" value={obatForm.dasar} onChange={(e) => handleObatChange('dasar', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="h_beli_modal">Harga Beli</Label>
                                    <Input id="h_beli_modal" type="number" value={obatForm.h_beli} onChange={(e) => handleObatChange('h_beli', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="ralan_modal">Harga Ralan</Label>
                                    <Input id="ralan_modal" type="number" value={obatForm.ralan} onChange={(e) => handleObatChange('ralan', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="kelas1_modal">Harga Kelas 1</Label>
                                    <Input id="kelas1_modal" type="number" value={obatForm.kelas1} onChange={(e) => handleObatChange('kelas1', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="kelas2_modal">Harga Kelas 2</Label>
                                    <Input id="kelas2_modal" type="number" value={obatForm.kelas2} onChange={(e) => handleObatChange('kelas2', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="kelas3_modal">Harga Kelas 3</Label>
                                    <Input id="kelas3_modal" type="number" value={obatForm.kelas3} onChange={(e) => handleObatChange('kelas3', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="utama_modal">Harga Utama</Label>
                                    <Input id="utama_modal" type="number" value={obatForm.utama} onChange={(e) => handleObatChange('utama', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="vip_modal">Harga VIP</Label>
                                    <Input id="vip_modal" type="number" value={obatForm.vip} onChange={(e) => handleObatChange('vip', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="vvip_modal">Harga VVIP</Label>
                                    <Input id="vvip_modal" type="number" value={obatForm.vvip} onChange={(e) => handleObatChange('vvip', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="beliluar_modal">Harga Beli Luar</Label>
                                    <Input id="beliluar_modal" type="number" value={obatForm.beliluar} onChange={(e) => handleObatChange('beliluar', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="jualbebas_modal">Harga Jual Bebas</Label>
                                    <Input id="jualbebas_modal" type="number" value={obatForm.jualbebas} onChange={(e) => handleObatChange('jualbebas', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="karyawan_modal">Harga Karyawan</Label>
                                    <Input id="karyawan_modal" type="number" value={obatForm.karyawan} onChange={(e) => handleObatChange('karyawan', e.target.value)} placeholder="0" />
                                </div>

                                {/* Lain-lain */}
                                <div>
                                    <Label htmlFor="isi_modal">Isi</Label>
                                    <Input id="isi_modal" type="number" value={obatForm.isi} onChange={(e) => handleObatChange('isi', e.target.value)} placeholder="1" />
                                </div>
                                <div>
                                    <Label htmlFor="kapasitas_modal">Kapasitas</Label>
                                    <Input id="kapasitas_modal" type="number" value={obatForm.kapasitas} onChange={(e) => handleObatChange('kapasitas', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="status_modal">Status (0/1)</Label>
                                    <Input id="status_modal" value={obatForm.status} onChange={(e) => handleObatChange('status', e.target.value)} placeholder="1" />
                                </div>
                                <div>
                                    <Label htmlFor="letak_barang_modal">Letak Barang</Label>
                                    <Input id="letak_barang_modal" value={obatForm.letak_barang} onChange={(e) => handleObatChange('letak_barang', e.target.value)} placeholder="Apotek" />
                                </div>
                                <div>
                                    <Label htmlFor="stokminimal_modal">Stok Minimal</Label>
                                    <Input id="stokminimal_modal" type="number" value={obatForm.stokminimal} onChange={(e) => handleObatChange('stokminimal', e.target.value)} placeholder="0" />
                                </div>
                                <div>
                                    <Label htmlFor="kdjns_modal">Jenis Obat (kdjns)</Label>
                                    <Input id="kdjns_modal" value={obatForm.kdjns} onChange={(e) => handleObatChange('kdjns', e.target.value)} placeholder="Mis. J001" />
                                </div>
                                <div>
                                    <Label htmlFor="expire_modal">Tanggal Expire</Label>
                                    <Input id="expire_modal" type="date" value={obatForm.expire} onChange={(e) => handleObatChange('expire', e.target.value)} />
                                </div>
                                <div>
                                    <Label htmlFor="kode_industri_modal">Kode Industri</Label>
                                    <Input id="kode_industri_modal" value={obatForm.kode_industri} onChange={(e) => handleObatChange('kode_industri', e.target.value)} placeholder="Mis. I0001" />
                                </div>
                                <div>
                                    <Label htmlFor="kode_kategori_modal">Kode Kategori</Label>
                                    <Input id="kode_kategori_modal" value={obatForm.kode_kategori} onChange={(e) => handleObatChange('kode_kategori', e.target.value)} placeholder="Mis. K001" />
                                </div>
                                <div>
                                    <Label htmlFor="kode_golongan_modal">Kode Golongan</Label>
                                    <Input id="kode_golongan_modal" value={obatForm.kode_golongan} onChange={(e) => handleObatChange('kode_golongan', e.target.value)} placeholder="Mis. G001" />
                                </div>
                            </div>
                            <div className="mt-6 flex justify-end gap-3">
                                <Button variant="outline" onClick={closeAddObatModal}>Batal</Button>
                                <Button onClick={submitObatBaru} disabled={savingObat}>
                                    {savingObat ? (<><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Menyimpan...</>) : (<>Simpan</>)}
                                </Button>
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
        </AppLayout>
    );
}