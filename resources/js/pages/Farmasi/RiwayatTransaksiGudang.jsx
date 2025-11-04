import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card';
import Button from '@/components/ui/Button';
import Input from '@/components/ui/Input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/Select';
import Badge from '@/components/ui/Badge';
import { Calendar, Download, Search, Filter, Eye } from 'lucide-react';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

const RiwayatTransaksiGudang = () => {
    const [riwayat, setRiwayat] = useState([]);
    const [bangsal, setBangsal] = useState([]);
    const [loading, setLoading] = useState(false);
    const [filters, setFilters] = useState({
        tanggal_mulai: '',
        tanggal_selesai: '',
        kode_brng: '',
        kd_bangsal: '',
        jenis_transaksi: '',
        tipe_transaksi: ''
    });
    const [pagination, setPagination] = useState({
        current_page: 1,
        last_page: 1,
        per_page: 50,
        total: 0
    });
    const [selectedDetail, setSelectedDetail] = useState(null);
    const [showDetailModal, setShowDetailModal] = useState(false);

    const jenisTransaksiOptions = [
        { value: 'opname', label: 'Stok Opname' },
        { value: 'pembelian', label: 'Pembelian' },
        { value: 'penjualan', label: 'Penjualan' },
        { value: 'mutasi', label: 'Mutasi' },
        { value: 'retur', label: 'Retur' },
        { value: 'adjustment', label: 'Adjustment' }
    ];

    const tipeTransaksiOptions = [
        { value: 'INSERT', label: 'Insert' },
        { value: 'UPDATE', label: 'Update' },
        { value: 'DELETE', label: 'Delete' }
    ];

    useEffect(() => {
        fetchBangsal();
        fetchRiwayat();
    }, []);

    const fetchBangsal = async () => {
        try {
            const response = await fetch('/api/riwayat-transaksi-gudang/bangsal');
            const result = await response.json();
            if (result.success) {
                setBangsal(result.data);
            }
        } catch (error) {
            console.error('Error fetching bangsal:', error);
        }
    };

    const fetchRiwayat = async (page = 1) => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                page: page.toString(),
                ...Object.fromEntries(
                    Object.entries(filters).filter(([_, value]) => value !== '')
                )
            });

            const response = await fetch(`/api/riwayat-transaksi-gudang?${params}`);
            const result = await response.json();
            
            if (result.success) {
                setRiwayat(result.data.data);
                setPagination({
                    current_page: result.data.current_page,
                    last_page: result.data.last_page,
                    per_page: result.data.per_page,
                    total: result.data.total
                });
            }
        } catch (error) {
            console.error('Error fetching riwayat:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (key, value) => {
        setFilters(prev => ({
            ...prev,
            [key]: value
        }));
    };

    const handleSearch = () => {
        fetchRiwayat(1);
    };

    const handleReset = () => {
        setFilters({
            tanggal_mulai: '',
            tanggal_selesai: '',
            kode_brng: '',
            kd_bangsal: '',
            jenis_transaksi: '',
            tipe_transaksi: ''
        });
        setTimeout(() => fetchRiwayat(1), 100);
    };

    const handleExport = () => {
        const params = new URLSearchParams(
            Object.fromEntries(
                Object.entries(filters).filter(([_, value]) => value !== '')
            )
        );
        window.open(`/api/riwayat-transaksi-gudang/export?${params}`, '_blank');
    };

    const handleViewDetail = async (id) => {
        try {
            const response = await fetch(`/api/riwayat-transaksi-gudang/${id}`);
            const result = await response.json();
            if (result.success) {
                setSelectedDetail(result.data);
                setShowDetailModal(true);
            }
        } catch (error) {
            console.error('Error fetching detail:', error);
        }
    };

    const getBadgeVariant = (tipe) => {
        switch (tipe) {
            case 'INSERT': return 'default';
            case 'UPDATE': return 'secondary';
            case 'DELETE': return 'destructive';
            default: return 'outline';
        }
    };

    const formatCurrency = (value) => {
        return new Intl.NumberFormat('id-ID').format(value || 0);
    };

    return (
        <AppLayout>
            <Head title="Riwayat Transaksi Gudang Barang" />
            
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Riwayat Transaksi Gudang Barang</h1>
                        <p className="text-gray-600">Audit trail semua transaksi yang mempengaruhi stok gudang</p>
                    </div>
                    <Button onClick={handleExport} className="flex items-center gap-2">
                        <Download className="h-4 w-4" />
                        Export CSV
                    </Button>
                </div>

                {/* Filter Section */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Filter className="h-5 w-5" />
                            Filter Data
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1">Tanggal Mulai</label>
                                <Input
                                    type="date"
                                    value={filters.tanggal_mulai}
                                    onChange={(e) => handleFilterChange('tanggal_mulai', e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Tanggal Selesai</label>
                                <Input
                                    type="date"
                                    value={filters.tanggal_selesai}
                                    onChange={(e) => handleFilterChange('tanggal_selesai', e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Kode Barang</label>
                                <Input
                                    placeholder="Cari kode barang..."
                                    value={filters.kode_brng}
                                    onChange={(e) => handleFilterChange('kode_brng', e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Bangsal</label>
                                <Select value={filters.kd_bangsal} onValueChange={(value) => handleFilterChange('kd_bangsal', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih bangsal" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Semua Bangsal</SelectItem>
                                        {bangsal.map((item) => (
                                            <SelectItem key={item.kd_bangsal} value={item.kd_bangsal}>
                                                {item.nm_bangsal}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Jenis Transaksi</label>
                                <Select value={filters.jenis_transaksi} onValueChange={(value) => handleFilterChange('jenis_transaksi', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih jenis" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Semua Jenis</SelectItem>
                                        {jenisTransaksiOptions.map((item) => (
                                            <SelectItem key={item.value} value={item.value}>
                                                {item.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Tipe Transaksi</label>
                                <Select value={filters.tipe_transaksi} onValueChange={(value) => handleFilterChange('tipe_transaksi', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih tipe" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Semua Tipe</SelectItem>
                                        {tipeTransaksiOptions.map((item) => (
                                            <SelectItem key={item.value} value={item.value}>
                                                {item.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div className="flex gap-2 mt-4">
                            <Button onClick={handleSearch} className="flex items-center gap-2">
                                <Search className="h-4 w-4" />
                                Cari
                            </Button>
                            <Button variant="outline" onClick={handleReset}>
                                Reset
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Data Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Data Riwayat Transaksi</CardTitle>
                        <p className="text-sm text-gray-600">
                            Menampilkan {riwayat.length} dari {pagination.total} data
                        </p>
                    </CardHeader>
                    <CardContent>
                        {loading ? (
                            <div className="flex justify-center py-8">
                                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full border-collapse border border-gray-200">
                                    <thead>
                                        <tr className="bg-stone-700 text-white">
                                            <th className="border border-gray-200 px-4 py-2 text-center">Tanggal</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Kode Barang</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Nama Barang</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Bangsal</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Jenis</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Jenis Transaksi</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Stok Sebelum</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Stok Sesudah</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Selisih</th>
                                            <th className="border border-gray-200 px-4 py-2 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {riwayat.length === 0 ? (
                                            <tr>
                                                <td colSpan="10" className="border border-gray-200 px-4 py-8 text-center text-gray-500">
                                                    Tidak ada data riwayat transaksi
                                                </td>
                                            </tr>
                                        ) : (
                                            riwayat.map((item) => {
                                                const selisih = item.stok_sesudah - item.stok_sebelum;
                                                return (
                                                    <tr key={item.id} className="hover:bg-gray-50">
                                                        <td className="border border-gray-200 px-4 py-2">
                                                            {format(new Date(item.created_at), 'dd/MM/yyyy HH:mm', { locale: id })}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2 font-mono">
                                                            {item.kode_brng}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2">
                                                            {item.data_barang?.nama_brng || '-'}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2">
                                                            {item.bangsal?.nm_bangsal || '-'}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2">
                                                            <Badge variant="outline">
                                                                {item.jenis_transaksi}
                                                            </Badge>
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2">
                                                            <Badge variant={getBadgeVariant(item.sumber_transaksi)}>
                                                                {item.sumber_transaksi}
                                                            </Badge>
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2 text-right">
                                                            {formatCurrency(item.stok_sebelum)}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2 text-right">
                                                            {formatCurrency(item.stok_sesudah)}
                                                        </td>
                                                        <td className={`border border-gray-200 px-4 py-2 text-right font-medium ${
                                                            selisih > 0 ? 'text-green-600' : selisih < 0 ? 'text-red-600' : 'text-gray-600'
                                                        }`}>
                                                            {selisih > 0 ? '+' : ''}{formatCurrency(selisih)}
                                                        </td>
                                                        <td className="border border-gray-200 px-4 py-2 text-center">
                                                            <Button
                                                                size="sm"
                                                                variant="outline"
                                                                onClick={() => handleViewDetail(item.id)}
                                                                className="flex items-center gap-1"
                                                            >
                                                                <Eye className="h-3 w-3" />
                                                                Detail
                                                            </Button>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        )}

                        {/* Pagination */}
                        {pagination.last_page > 1 && (
                            <div className="flex items-center justify-between mt-4">
                                <div className="text-sm text-gray-600">
                                    Halaman {pagination.current_page} dari {pagination.last_page}
                                </div>
                                <div className="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={pagination.current_page === 1}
                                        onClick={() => fetchRiwayat(pagination.current_page - 1)}
                                    >
                                        Sebelumnya
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={pagination.current_page === pagination.last_page}
                                        onClick={() => fetchRiwayat(pagination.current_page + 1)}
                                    >
                                        Selanjutnya
                                    </Button>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Detail Modal */}
            {showDetailModal && selectedDetail && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-semibold">Detail Transaksi</h3>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => setShowDetailModal(false)}
                            >
                                Tutup
                            </Button>
                        </div>
                        
                        <div className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <p className="text-sm">
                                        {format(new Date(selectedDetail.created_at), 'dd MMMM yyyy HH:mm:ss', { locale: id })}
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">User</label>
                                    <p className="text-sm">{selectedDetail.user?.name || '-'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Kode Barang</label>
                                    <p className="text-sm font-mono">{selectedDetail.kode_brng}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nama Barang</label>
                                    <p className="text-sm">{selectedDetail.data_barang?.nama_brng || '-'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Bangsal</label>
                                    <p className="text-sm">{selectedDetail.bangsal?.nm_bangsal || '-'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">No Batch</label>
                                    <p className="text-sm">{selectedDetail.no_batch || '-'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">No Faktur</label>
                                    <p className="text-sm">{selectedDetail.no_faktur || '-'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                                    <Badge variant="outline">{selectedDetail.jenis_transaksi}</Badge>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Tipe Transaksi</label>
                                    <Badge variant={getBadgeVariant(selectedDetail.tipe_transaksi)}>
                                        {selectedDetail.tipe_transaksi}
                                    </Badge>
                                </div>
                            </div>
                            
                            <div className="grid grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Stok Sebelum</label>
                                    <p className="text-lg font-semibold">{formatCurrency(selectedDetail.stok_sebelum)}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Stok Sesudah</label>
                                    <p className="text-lg font-semibold">{formatCurrency(selectedDetail.stok_sesudah)}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Selisih</label>
                                    <p className={`text-lg font-semibold ${
                                        selectedDetail.stok_sesudah - selectedDetail.stok_sebelum > 0 
                                            ? 'text-green-600' 
                                            : selectedDetail.stok_sesudah - selectedDetail.stok_sebelum < 0 
                                            ? 'text-red-600' 
                                            : 'text-gray-600'
                                    }`}>
                                        {selectedDetail.stok_sesudah - selectedDetail.stok_sebelum > 0 ? '+' : ''}
                                        {formatCurrency(selectedDetail.stok_sesudah - selectedDetail.stok_sebelum)}
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Keterangan</label>
                                <p className="text-sm bg-gray-50 p-3 rounded">{selectedDetail.keterangan || '-'}</p>
                            </div>
                            
                            {selectedDetail.data_sebelum && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Data Sebelum</label>
                                    <pre className="text-xs bg-gray-50 p-3 rounded overflow-x-auto">
                                        {JSON.stringify(selectedDetail.data_sebelum, null, 2)}
                                    </pre>
                                </div>
                            )}
                            
                            {selectedDetail.data_sesudah && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Data Sesudah</label>
                                    <pre className="text-xs bg-gray-50 p-3 rounded overflow-x-auto">
                                        {JSON.stringify(selectedDetail.data_sesudah, null, 2)}
                                    </pre>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
};

export default RiwayatTransaksiGudang;