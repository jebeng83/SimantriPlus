@extends('adminlte::page')

@section('title', 'Demografis Pasien')

@section('content_header')
<h1>Demografis Pasien</h1>
@stop

@section('content')
<div id="demografi-pasien-app">
   <!-- Loading state while React component loads -->
   <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
      <div class="text-center">
         <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
         </div>
         <p class="mt-2">Loading Demographic Analysis...</p>
      </div>
   </div>
</div>
@stop

@section('css')
<!-- Ant Design CSS -->
<link href="https://unpkg.com/antd@4.24.15/dist/antd.min.css" rel="stylesheet">
<!-- ApexCharts CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.css">
<!-- Moment.js CSS if needed -->
<style>
   /* Ensure the container takes full width */
   #demografi-pasien-app {
      width: 100%;
      min-height: 100vh;
   }

   /* Override AdminLTE styles if needed */
   .content-wrapper {
      background: #f4f6f9;
   }

   /* Custom loading state */
   .demographic-loading {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      border-radius: 16px;
      padding: 40px;
      text-align: center;
   }
</style>
@stop

@section('js')
<!-- React and ReactDOM -->
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>

<!-- Moment.js -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<!-- Ant Design -->
<script src="https://unpkg.com/antd@4.24.15/dist/antd.min.js"></script>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>

<!-- Babel Standalone for JSX -->
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- Our React Component -->
<script type="text/babel">
   const { useState, useEffect } = React;
        const { 
            Card, Row, Col, Select, Button, Space, Typography, Spin, message, 
            Statistic, Divider, DatePicker, Badge 
        } = antd;
        
        // Create icon components using emoji since Ant Design icons are not available via CDN
        const createIcon = (emoji) => React.createElement('span', { style: { fontSize: '16px' } }, emoji);
        
        const { Title, Text } = Typography;
        const { Option } = Select;
        const { RangePicker } = DatePicker;

        const DemografiPasien = () => {
            const [loading, setLoading] = useState(false);
            const [chartLoading, setChartLoading] = useState(false);
            const [filters, setFilters] = useState({
                kd_kab: null,
                kd_kec: null,
                kd_kel: null,
                dateRange: [moment().subtract(1, 'month'), moment()]
            });
            
            // Data states
            const [kabupatenList, setKabupatenList] = useState([]);
            const [kecamatanList, setKecamatanList] = useState([]);
            const [kelurahanList, setKelurahanList] = useState([]);
            const [demographicData, setDemographicData] = useState(null);
            const [summary, setSummary] = useState({
                totalPatients: 0,
                uniquePatients: 0,
                kabupatenCount: 0,
                kecamatanCount: 0,
                kelurahanCount: 0
            });

            // Chart instances
            const [charts, setCharts] = useState({
                kabupatenChart: null,
                kecamatanChart: null,
                kelurahanChart: null,
                genderChart: null
            });

            useEffect(() => {
                loadInitialData();
                return () => {
                    // Cleanup charts
                    Object.values(charts).forEach(chart => {
                        if (chart) chart.destroy();
                    });
                };
            }, []);

            const loadInitialData = async () => {
                setLoading(true);
                try {
                    await Promise.all([
                        loadKabupaten(),
                        loadAllKecamatan(),
                        loadAllKelurahan(),
                        loadDemographicData()
                    ]);
                } catch (error) {
                    message.error('Gagal memuat data awal');
                    console.error('Error loading initial data:', error);
                } finally {
                    setLoading(false);
                }
            };

            const loadKabupaten = async () => {
                try {
                    // Try database endpoint first
                    const response = await fetch('/ranap/laporan/grafik/kabupaten-db');
                    const result = await response.json();
                    
                    if (result && Array.isArray(result) && result.length > 0) {
                        setKabupatenList(result);
                        return;
                    }
                    
                    // Fallback to web route
                    const fallbackResponse = await fetch('/kabupaten');
                    const fallbackResult = await fallbackResponse.json();
                    
                    if (fallbackResult && Array.isArray(fallbackResult)) {
                        setKabupatenList(fallbackResult);
                    } else if (fallbackResult.status === 'success' && Array.isArray(fallbackResult.data)) {
                        setKabupatenList(fallbackResult.data);
                    } else {
                        console.warn('No kabupaten data available');
                        setKabupatenList([]);
                    }
                } catch (error) {
                    console.error('Error loading kabupaten:', error);
                    setKabupatenList([]);
                    // Don't show error message as this might be expected in some cases
                }
            };

            // Load all kecamatan data independently
            const loadAllKecamatan = async () => {
                try {
                    const response = await fetch('/ranap/laporan/grafik/kecamatan-all');
                    const result = await response.json();
                    
                    if (result && Array.isArray(result)) {
                        setKecamatanList(result);
                    } else if (result.status === 'success' && Array.isArray(result.data)) {
                        setKecamatanList(result.data);
                    } else {
                        console.warn('Kecamatan data not available');
                        setKecamatanList([]);
                    }
                } catch (error) {
                    console.error('Error loading kecamatan:', error);
                    setKecamatanList([]);
                }
            };

            // Load all kelurahan data independently
            const loadAllKelurahan = async () => {
                try {
                    const response = await fetch('/ranap/laporan/grafik/kelurahan-all');
                    const result = await response.json();
                    
                    if (result && Array.isArray(result)) {
                        setKelurahanList(result);
                    } else if (result.status === 'success' && Array.isArray(result.data)) {
                        setKelurahanList(result.data);
                    } else {
                        console.warn('Kelurahan data not available');
                        setKelurahanList([]);
                    }
                } catch (error) {
                    console.error('Error loading kelurahan:', error);
                    setKelurahanList([]);
                }
            };

            const loadDemographicData = async () => {
                setChartLoading(true);
                try {
                    const params = new URLSearchParams();
                    
                    if (filters.kd_kab) params.append('kd_kab', filters.kd_kab);
                    if (filters.kd_kec) params.append('kd_kec', filters.kd_kec);
                    if (filters.kd_kel) params.append('kd_kel', filters.kd_kel);
                    if (filters.dateRange && filters.dateRange[0] && filters.dateRange[1]) {
                        params.append('start_date', filters.dateRange[0].format('YYYY-MM-DD'));
                        params.append('end_date', filters.dateRange[1].format('YYYY-MM-DD'));
                    }

                    const response = await fetch(`/ranap/laporan/grafik/demografi?${params.toString()}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        setDemographicData(data.charts);
                        setSummary(data.summary);
                        
                        // Render charts
                        setTimeout(() => {
                            renderCharts(data.charts);
                        }, 100);
                    } else {
                        message.error(data.message || 'Gagal memuat data');
                    }
                    
                } catch (error) {
                    message.error('Gagal memuat data demografis');
                    console.error('Error loading demographic data:', error);
                } finally {
                    setChartLoading(false);
                }
            };

            const renderCharts = (data) => {
                // Destroy existing charts
                Object.values(charts).forEach(chart => {
                    if (chart) chart.destroy();
                });

                // Validate data structure
                if (!data || typeof data !== 'object') {
                    console.warn('Invalid chart data structure');
                    return;
                }

                // Render Kabupaten Chart with validation
                if (data.kabupaten && window.ApexCharts && 
                    Array.isArray(data.kabupaten.data) && data.kabupaten.data.length > 0 &&
                    Array.isArray(data.kabupaten.labels) && data.kabupaten.labels.length > 0) {
                    
                    const chartElement = document.querySelector("#kabupaten-chart");
                    if (chartElement) {
                        const kabupatenChart = new ApexCharts(chartElement, {
                            series: data.kabupaten.data.map(val => Number(val) || 0),
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: data.kabupaten.labels,
                            colors: ['#1890ff', '#52c41a', '#faad14', '#f5222d', '#722ed1', '#13c2c2', '#eb2f96', '#fa8c16'],
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }],
                            title: {
                                text: 'Distribusi per Kabupaten',
                                align: 'center'
                            },
                            noData: {
                                text: 'Tidak ada data kabupaten'
                            }
                        });
                        kabupatenChart.render();
                        setCharts(prev => ({ ...prev, kabupatenChart }));
                    }
                } else {
                    // Show empty state for kabupaten chart
                    const chartElement = document.querySelector("#kabupaten-chart");
                    if (chartElement) {
                        chartElement.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">📊<br/>Tidak ada data kabupaten</div>';
                    }
                }

                // Render Kecamatan Chart with validation
                if (data.kecamatan && window.ApexCharts && 
                    Array.isArray(data.kecamatan.data) && data.kecamatan.data.length > 0 &&
                    Array.isArray(data.kecamatan.labels) && data.kecamatan.labels.length > 0) {
                    
                    const chartElement = document.querySelector("#kecamatan-chart");
                    if (chartElement) {
                        const kecamatanChart = new ApexCharts(chartElement, {
                            series: [{
                                data: data.kecamatan.data.map(val => Number(val) || 0)
                            }],
                            chart: {
                                type: 'bar',
                                height: 300
                            },
                            xaxis: {
                                categories: data.kecamatan.labels,
                                labels: {
                                    rotate: -45
                                }
                            },
                            colors: ['#52c41a'],
                            title: {
                                text: 'Distribusi per Kecamatan',
                                align: 'center'
                            },
                            noData: {
                                text: 'Tidak ada data kecamatan'
                            }
                        });
                        kecamatanChart.render();
                        setCharts(prev => ({ ...prev, kecamatanChart }));
                    }
                } else {
                    // Show empty state for kecamatan chart
                    const chartElement = document.querySelector("#kecamatan-chart");
                    if (chartElement) {
                        chartElement.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">📊<br/>Tidak ada data kecamatan</div>';
                    }
                }

                // Render Kelurahan Chart with validation
                if (data.kelurahan && window.ApexCharts && 
                    Array.isArray(data.kelurahan.data) && data.kelurahan.data.length > 0 &&
                    Array.isArray(data.kelurahan.labels) && data.kelurahan.labels.length > 0) {
                    
                    const chartElement = document.querySelector("#kelurahan-chart");
                    if (chartElement) {
                        const kelurahanChart = new ApexCharts(chartElement, {
                            series: [{
                                data: data.kelurahan.data.map(val => Number(val) || 0)
                            }],
                            chart: {
                                type: 'bar',
                                height: 300
                            },
                            xaxis: {
                                categories: data.kelurahan.labels,
                                labels: {
                                    rotate: -45
                                }
                            },
                            colors: ['#faad14'],
                            title: {
                                text: 'Distribusi per Kelurahan',
                                align: 'center'
                            },
                            noData: {
                                text: 'Tidak ada data kelurahan'
                            }
                        });
                        kelurahanChart.render();
                        setCharts(prev => ({ ...prev, kelurahanChart }));
                    }
                } else {
                    // Show empty state for kelurahan chart
                    const chartElement = document.querySelector("#kelurahan-chart");
                    if (chartElement) {
                        chartElement.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">📊<br/>Tidak ada data kelurahan</div>';
                    }
                }

                // Render Gender Chart with validation
                if (data.gender && window.ApexCharts && 
                    Array.isArray(data.gender.data) && data.gender.data.length > 0 &&
                    Array.isArray(data.gender.labels) && data.gender.labels.length > 0) {
                    
                    const chartElement = document.querySelector("#gender-chart");
                    if (chartElement) {
                        const genderChart = new ApexCharts(chartElement, {
                            series: data.gender.data.map(val => Number(val) || 0),
                            chart: {
                                type: 'donut',
                                height: 300
                            },
                            labels: data.gender.labels,
                            colors: ['#1890ff', '#eb2f96'],
                            title: {
                                text: 'Distribusi Jenis Kelamin',
                                align: 'center'
                            },
                            noData: {
                                text: 'Tidak ada data jenis kelamin'
                            }
                        });
                        genderChart.render();
                        setCharts(prev => ({ ...prev, genderChart }));
                    }
                } else {
                    // Show empty state for gender chart
                    const chartElement = document.querySelector("#gender-chart");
                    if (chartElement) {
                        chartElement.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">👫<br/>Tidak ada data jenis kelamin</div>';
                    }
                }
            };

            const handleFilterChange = (key, value) => {
                setFilters(prev => ({
                    ...prev,
                    [key]: value
                }));
            };

            const handleApplyFilter = () => {
                loadDemographicData();
            };

            const handleResetFilter = () => {
                setFilters({
                    kd_kab: null,
                    kd_kec: null,
                    kd_kel: null,
                    dateRange: [moment().subtract(1, 'month'), moment()]
                });
                setTimeout(() => {
                    loadDemographicData();
                }, 100);
            };

            if (loading) {
                return React.createElement('div', {
                    style: { textAlign: 'center', padding: '50px' },
                    className: 'demographic-loading'
                }, [
                    React.createElement(Spin, { key: 'spinner', size: 'large' }),
                    React.createElement('div', {
                        key: 'text',
                        style: { marginTop: 16 }
                    }, [
                        React.createElement(Text, { key: 'loading-text' }, 'Memuat data demografis...')
                    ])
                ]);
            }

            return React.createElement('div', {
                style: {
                    padding: '24px',
                    background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
                    minHeight: '100vh'
                }
            }, [
                // Header
                React.createElement('div', {
                    key: 'header',
                    style: { marginBottom: '24px' }
                }, [
                    React.createElement(Title, {
                        key: 'title',
                        level: 3,
                        style: {
                            color: '#1f2937',
                            fontWeight: 700,
                            marginBottom: '8px'
                        }
                    }, [
                        createIcon('👥'),
                        ' Demografis Pasien'
                    ]),
                    React.createElement(Text, {
                        key: 'subtitle',
                        type: 'secondary',
                        style: {
                            color: '#6b7280',
                            fontSize: '16px'
                        }
                    }, 'Analisis distribusi pasien berdasarkan wilayah geografis dan demografi')
                ]),

                // Filters
                React.createElement(Card, {
                    key: 'filters',
                    title: React.createElement(Space, {}, [
                        createIcon('🔍'),
                        'Filter Data'
                    ]),
                    style: {
                        marginBottom: '24px',
                        borderRadius: '16px',
                        border: 'none',
                        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)'
                    }
                }, [
                    React.createElement(Row, {
                        key: 'filter-row',
                        gutter: [16, 16]
                    }, [
                        React.createElement(Col, {
                            key: 'date-col',
                            xs: 24,
                            sm: 12,
                            md: 6
                        }, [
                            React.createElement(Text, {
                                key: 'date-label',
                                strong: true
                            }, 'Rentang Tanggal:'),
                            React.createElement(RangePicker, {
                                key: 'date-picker',
                                value: filters.dateRange,
                                onChange: (dates) => handleFilterChange('dateRange', dates),
                                style: { width: '100%', marginTop: '4px' },
                                format: 'DD/MM/YYYY'
                            })
                        ]),
                        React.createElement(Col, {
                            key: 'kab-col',
                            xs: 24,
                            sm: 12,
                            md: 6
                        }, [
                            React.createElement(Text, {
                                key: 'kab-label',
                                strong: true
                            }, 'Kabupaten:'),
                            React.createElement(Select, {
                                key: 'kab-select',
                                placeholder: 'Pilih Kabupaten',
                                value: filters.kd_kab,
                                onChange: (value) => handleFilterChange('kd_kab', value),
                                style: { width: '100%', marginTop: '4px' },
                                allowClear: true,
                                showSearch: true,
                                optionFilterProp: 'children'
                            }, (kabupatenList || []).map(item => 
                                React.createElement(Option, {
                                    key: item.kd_kab,
                                    value: item.kd_kab
                                }, item.nm_kab)
                            ))
                        ]),
                        React.createElement(Col, {
                            key: 'kec-col',
                            xs: 24,
                            sm: 12,
                            md: 6
                        }, [
                            React.createElement(Text, {
                                key: 'kec-label',
                                strong: true
                            }, 'Kecamatan:'),
                            React.createElement(Select, {
                                key: 'kec-select',
                                placeholder: 'Pilih Kecamatan',
                                value: filters.kd_kec,
                                onChange: (value) => handleFilterChange('kd_kec', value),
                                style: { width: '100%', marginTop: '4px' },
                                allowClear: true,
                                showSearch: true,
                                optionFilterProp: 'children'
                            }, (kecamatanList || []).map(item => 
                                React.createElement(Option, {
                                    key: item.kd_kec,
                                    value: item.kd_kec
                                }, item.nm_kec)
                            ))
                        ]),
                        React.createElement(Col, {
                            key: 'kel-col',
                            xs: 24,
                            sm: 12,
                            md: 6
                        }, [
                            React.createElement(Text, {
                                key: 'kel-label',
                                strong: true
                            }, 'Kelurahan:'),
                            React.createElement(Select, {
                                key: 'kel-select',
                                placeholder: 'Pilih Kelurahan',
                                value: filters.kd_kel,
                                onChange: (value) => handleFilterChange('kd_kel', value),
                                style: { width: '100%', marginTop: '4px' },
                                allowClear: true,
                                showSearch: true,
                                optionFilterProp: 'children'
                            }, (kelurahanList || []).map(item => 
                                React.createElement(Option, {
                                    key: item.kd_kel,
                                    value: item.kd_kel
                                }, item.nm_kel)
                            ))
                        ])
                    ]),
                    React.createElement(Divider, { key: 'divider' }),
                    React.createElement(Space, { key: 'actions' }, [
                        React.createElement(Button, {
                            key: 'apply',
                            type: 'primary',
                            icon: createIcon('🔍'),
                            onClick: handleApplyFilter,
                            loading: chartLoading
                        }, 'Terapkan Filter'),
                        React.createElement(Button, {
                            key: 'reset',
                            icon: createIcon('🔄'),
                            onClick: handleResetFilter
                        }, 'Reset')
                    ])
                ]),

                // Summary Statistics
                React.createElement(Row, {
                    key: 'summary',
                    gutter: [16, 16],
                    style: { marginBottom: '24px' }
                }, [
                    React.createElement(Col, {
                        key: 'total-patients',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Total Kunjungan',
                                value: summary.totalPatients,
                                prefix: createIcon('👥'),
                                valueStyle: { color: '#1890ff' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'unique-patients',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Pasien Unik',
                                value: summary.uniquePatients,
                                prefix: createIcon('👤'),
                                valueStyle: { color: '#52c41a' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'kabupaten',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Kabupaten',
                                value: summary.kabupatenCount,
                                prefix: createIcon('🏛️'),
                                valueStyle: { color: '#faad14' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'kecamatan',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Kecamatan',
                                value: summary.kecamatanCount,
                                prefix: createIcon('🏘️'),
                                valueStyle: { color: '#f5222d' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'kelurahan',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Kelurahan',
                                value: summary.kelurahanCount,
                                prefix: createIcon('🏠'),
                                valueStyle: { color: '#722ed1' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'status',
                        xs: 12,
                        sm: 8,
                        md: 4
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            style: {
                                borderRadius: '12px',
                                border: 'none',
                                boxShadow: '0 2px 12px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'stat',
                                title: 'Status',
                                value: chartLoading ? 'Loading...' : 'Ready',
                                prefix: createIcon('📊'),
                                valueStyle: { color: chartLoading ? '#faad14' : '#13c2c2' }
                            })
                        ])
                    ])
                ]),

                // Charts
                React.createElement(Row, {
                    key: 'charts',
                    gutter: [16, 16]
                }, [
                    React.createElement(Col, {
                        key: 'kabupaten-chart',
                        xs: 24,
                        lg: 12
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            title: React.createElement(Space, {}, [
                                createIcon('📊'),
                                'Distribusi per Kabupaten'
                            ]),
                            loading: chartLoading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)',
                                marginBottom: '16px'
                            }
                        }, [
                            React.createElement('div', {
                                key: 'chart',
                                id: 'kabupaten-chart',
                                style: { minHeight: '300px', padding: '16px' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'gender-chart',
                        xs: 24,
                        lg: 12
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            title: React.createElement(Space, {}, [
                                createIcon('👫'),
                                'Distribusi Jenis Kelamin'
                            ]),
                            loading: chartLoading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)',
                                marginBottom: '16px'
                            }
                        }, [
                            React.createElement('div', {
                                key: 'chart',
                                id: 'gender-chart',
                                style: { minHeight: '300px', padding: '16px' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'kecamatan-chart',
                        xs: 24,
                        lg: 12
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            title: React.createElement(Space, {}, [
                                createIcon('📊'),
                                'Distribusi per Kecamatan'
                            ]),
                            loading: chartLoading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)',
                                marginBottom: '16px'
                            }
                        }, [
                            React.createElement('div', {
                                key: 'chart',
                                id: 'kecamatan-chart',
                                style: { minHeight: '300px', padding: '16px' }
                            })
                        ])
                    ]),
                    React.createElement(Col, {
                        key: 'kelurahan-chart',
                        xs: 24,
                        lg: 12
                    }, [
                        React.createElement(Card, {
                            key: 'card',
                            title: React.createElement(Space, {}, [
                                createIcon('📊'),
                                'Distribusi per Kelurahan'
                            ]),
                            loading: chartLoading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)',
                                marginBottom: '16px'
                            }
                        }, [
                            React.createElement('div', {
                                key: 'chart',
                                id: 'kelurahan-chart',
                                style: { minHeight: '300px', padding: '16px' }
                            })
                        ])
                    ])
                ])
            ]);
        };

        // Render the component
        ReactDOM.render(
            React.createElement(DemografiPasien),
            document.getElementById('demografi-pasien-app')
        );
    </script>
@stop