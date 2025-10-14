@extends('adminlte::page')

@section('title', 'Top 10 Penyakit - Analisis Grafik')

@section('content_header')
<h1>
   <i class="fas fa-chart-pie"></i> Top 10 Penyakit
   <small>Analisis Data Penyakit Terbanyak</small>
</h1>
@stop

@section('css')
<style>
   .disease-chart-container {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: calc(100vh - 120px);
      padding: 20px 0;
   }

   .disease-header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
   }

   .filter-card,
   .chart-card,
   .summary-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
   }

   .chart-container {
      min-height: 400px;
      padding: 20px;
   }
</style>
@stop

@section('content')
<div class="disease-chart-container">
   <div class="container-fluid">
      <div class="disease-header">
         <h2 style="color: #667eea; font-weight: 700; margin-bottom: 10px;">
            <i class="fas fa-virus" style="margin-right: 12px;"></i>
            Analisis Top 10 Penyakit
         </h2>
         <p style="color: #6c757d; font-size: 1.1rem;">
            Dashboard analisis data penyakit terbanyak berdasarkan diagnosis pasien
         </p>
      </div>
      <div id="top-penyakit-app"></div>
   </div>
</div>
@stop

@section('js')
<!-- Load all dependencies first, before Babel -->
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
<!-- Try multiple CDNs for moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"
   onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js';"></script>
<script src="https://unpkg.com/antd@4.24.15/dist/antd.min.js"></script>
<link href="https://unpkg.com/antd@4.24.15/dist/antd.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Load Babel last to process the React code -->
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<script type="text/babel">
   // Wait for all dependencies to be available
        const checkDependencies = () => {
            return window.React && window.ReactDOM && window.moment && window.antd && window.ApexCharts;
        };

        const initializeApp = () => {
            if (!checkDependencies()) {
                console.log('Waiting for dependencies...', {
                    React: !!window.React,
                    ReactDOM: !!window.ReactDOM,
                    moment: !!window.moment,
                    antd: !!window.antd,
                    ApexCharts: !!window.ApexCharts
                });
                setTimeout(initializeApp, 100);
                return;
            }

            const { useState, useEffect } = React;
            const { Card, Row, Col, Select, Button, Space, Typography, Spin, message, 
                    Statistic, Divider, DatePicker } = antd;
            const { Title, Text } = Typography;
            const { Option } = Select;
            const { RangePicker } = DatePicker;

            const TopPenyakitAnalysis = () => {
                const [loading, setLoading] = useState(false);
                const [chartLoading, setChartLoading] = useState(false);
                const [filters, setFilters] = useState({
                    start_date: moment().subtract(1, 'month').format('YYYY-MM-DD'),
                    end_date: moment().format('YYYY-MM-DD'),
                    kd_kab: null,
                    kd_kec: null,
                    kd_kel: null,
                    prioritas: null,
                    dateRange: [moment().subtract(1, 'month'), moment()]
                });
            
            const [kabupatenList, setKabupatenList] = useState([]);
            const [kecamatanList, setKecamatanList] = useState([]);
            const [kelurahanList, setKelurahanList] = useState([]);
            const [diseaseData, setDiseaseData] = useState(null);
            const [summary, setSummary] = useState({
                totalDiagnoses: 0,
                uniqueDiseases: 0,
                totalPatients: 0,
                avgCasesPerDisease: 0,
                topDiseasePercentage: 0
            });
            const [charts, setCharts] = useState({});

            useEffect(() => {
                loadInitialData();
            }, []);

            const loadInitialData = async () => {
                setLoading(true);
                try {
                    await Promise.all([
                        loadKabupaten(),
                        loadAllKecamatan(),
                        loadAllKelurahan(),
                        loadTopPenyakitData()
                    ]);
                } catch (error) {
                    message.error('Gagal memuat data awal');
                } finally {
                    setLoading(false);
                }
            };

            const loadKabupaten = async () => {
                try {
                    const response = await fetch('/ranap/laporan/grafik/kabupaten-db');
                    const result = await response.json();
                    setKabupatenList(Array.isArray(result) ? result : []);
                } catch (error) {
                    setKabupatenList([]);
                }
            };

            const loadAllKecamatan = async () => {
                try {
                    const response = await fetch('/ranap/laporan/grafik/kecamatan-all');
                    const result = await response.json();
                    setKecamatanList(Array.isArray(result) ? result : []);
                } catch (error) {
                    setKecamatanList([]);
                }
            };

            const loadAllKelurahan = async () => {
                try {
                    const response = await fetch('/ranap/laporan/grafik/kelurahan-all');
                    const result = await response.json();
                    setKelurahanList(Array.isArray(result) ? result : []);
                } catch (error) {
                    setKelurahanList([]);
                }
            };

            const loadTopPenyakitData = async () => {
                setChartLoading(true);
                try {
                    const params = new URLSearchParams();
                    if (filters.start_date) params.append('start_date', filters.start_date);
                    if (filters.end_date) params.append('end_date', filters.end_date);
                    if (filters.kd_kab) params.append('kd_kab', filters.kd_kab);
                    if (filters.kd_kec) params.append('kd_kec', filters.kd_kec);
                    if (filters.kd_kel) params.append('kd_kel', filters.kd_kel);
                    if (filters.prioritas) params.append('prioritas', filters.prioritas);

                    const response = await fetch(`/ranap/laporan/grafik/top-penyakit?${params.toString()}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        setDiseaseData(data.charts);
                        setSummary(data.summary);
                        setTimeout(() => renderCharts(data.charts), 100);
                    } else {
                        message.error(data.message || 'Gagal memuat data');
                    }
                } catch (error) {
                    message.error('Gagal memuat data penyakit');
                } finally {
                    setChartLoading(false);
                }
            };

            const renderCharts = (data) => {
                // Top Diseases Chart
                if (data.topDiseases && data.topDiseases.data.length > 0) {
                    const chartElement = document.querySelector("#top-diseases-chart");
                    if (chartElement && window.ApexCharts) {
                        const chart = new ApexCharts(chartElement, {
                            series: [{
                                name: 'Jumlah Kasus',
                                data: data.topDiseases.data
                            }],
                            chart: { type: 'bar', height: 400 },
                            xaxis: { 
                                categories: data.topDiseases.labels,
                                labels: { rotate: -45 }
                            },
                            colors: ['#667eea'],
                            title: { text: 'Top 10 Penyakit Terbanyak', align: 'center' },
                            dataLabels: { enabled: true }
                        });
                        chart.render();
                    }
                }

                // Gender Distribution Chart
                if (data.genderDistribution && Object.keys(data.genderDistribution).length > 0) {
                    const firstDisease = Object.keys(data.genderDistribution)[0];
                    const genderData = data.genderDistribution[firstDisease];
                    
                    const chartElement = document.querySelector("#gender-chart");
                    if (chartElement && genderData && window.ApexCharts) {
                        const chart = new ApexCharts(chartElement, {
                            series: genderData.data,
                            chart: { type: 'donut', height: 300 },
                            labels: genderData.labels,
                            colors: ['#1890ff', '#eb2f96'],
                            title: { text: `Distribusi Gender - ${firstDisease}`, align: 'center' }
                        });
                        chart.render();
                    }
                }

                // Monthly Trend Chart
                if (data.monthlyTrend && Object.keys(data.monthlyTrend).length > 0) {
                    const chartElement = document.querySelector("#trend-chart");
                    if (chartElement && window.ApexCharts) {
                        const series = Object.keys(data.monthlyTrend).slice(0, 3).map(disease => ({
                            name: disease,
                            data: data.monthlyTrend[disease].data
                        }));
                        
                        const chart = new ApexCharts(chartElement, {
                            series: series,
                            chart: { type: 'line', height: 350 },
                            xaxis: { categories: Object.values(data.monthlyTrend)[0]?.labels || [] },
                            colors: ['#667eea', '#764ba2', '#28a745'],
                            title: { text: 'Tren Bulanan Top 3 Penyakit', align: 'center' },
                            stroke: { curve: 'smooth', width: 3 }
                        });
                        chart.render();
                    }
                }
            };

            const handleFilterChange = (key, value) => {
                if (key === 'dateRange') {
                    setFilters(prev => ({
                        ...prev,
                        dateRange: value,
                        start_date: value && value[0] ? value[0].format('YYYY-MM-DD') : null,
                        end_date: value && value[1] ? value[1].format('YYYY-MM-DD') : null
                    }));
                } else {
                    setFilters(prev => ({ ...prev, [key]: value }));
                }
            };

            const handleApplyFilter = () => {
                loadTopPenyakitData();
            };

            const handleResetFilter = () => {
                setFilters({
                    start_date: moment().subtract(1, 'month').format('YYYY-MM-DD'),
                    end_date: moment().format('YYYY-MM-DD'),
                    kd_kab: null, kd_kec: null, kd_kel: null, prioritas: null,
                    dateRange: [moment().subtract(1, 'month'), moment()]
                });
                setTimeout(() => loadTopPenyakitData(), 100);
            };

            if (loading) {
                return React.createElement('div', {
                    style: { textAlign: 'center', padding: '50px' }
                }, [
                    React.createElement(Spin, { key: 'spin', size: 'large' }),
                    React.createElement('div', { key: 'text', style: { marginTop: 16 } },
                        React.createElement(Text, {}, 'Memuat data analisis penyakit...')
                    )
                ]);
            }

            return React.createElement('div', {}, [
                // Filters
                React.createElement(Card, {
                    key: 'filter-card',
                    title: 'Filter Data Analisis',
                    className: 'filter-card'
                }, [
                    React.createElement(Row, { key: 'filter-row', gutter: [16, 16] }, [
                        React.createElement(Col, { key: 'date-col', xs: 24, md: 6 }, [
                            React.createElement(Text, { key: 'date-label', strong: true }, 'Rentang Tanggal:'),
                            React.createElement(RangePicker, {
                                key: 'date-picker',
                                value: filters.dateRange,
                                onChange: (dates) => handleFilterChange('dateRange', dates),
                                style: { width: '100%', marginTop: '4px' }
                            })
                        ]),
                        React.createElement(Col, { key: 'kab-col', xs: 24, md: 6 }, [
                            React.createElement(Text, { key: 'kab-label', strong: true }, 'Kabupaten:'),
                            React.createElement(Select, {
                                key: 'kab-select',
                                placeholder: 'Pilih Kabupaten',
                                value: filters.kd_kab,
                                onChange: (value) => handleFilterChange('kd_kab', value),
                                style: { width: '100%', marginTop: '4px' },
                                allowClear: true
                            }, kabupatenList.map(item => 
                                React.createElement(Option, { key: item.kd_kab, value: item.kd_kab }, item.nm_kab)
                            ))
                        ]),
                        React.createElement(Col, { key: 'prioritas-col', xs: 24, md: 6 }, [
                            React.createElement(Text, { key: 'prioritas-label', strong: true }, 'Prioritas:'),
                            React.createElement(Select, {
                                key: 'prioritas-select',
                                placeholder: 'Pilih Prioritas',
                                value: filters.prioritas,
                                onChange: (value) => handleFilterChange('prioritas', value),
                                style: { width: '100%', marginTop: '4px' },
                                allowClear: true
                            }, [
                                React.createElement(Option, { key: '1', value: '1' }, 'Diagnosis Utama'),
                                React.createElement(Option, { key: '2', value: '2' }, 'Diagnosis Sekunder')
                            ])
                        ])
                    ]),
                    React.createElement(Divider, { key: 'divider' }),
                    React.createElement(Space, { key: 'buttons' }, [
                        React.createElement(Button, {
                            key: 'apply-btn', type: 'primary',
                            onClick: handleApplyFilter, loading: chartLoading
                        }, 'Terapkan Filter'),
                        React.createElement(Button, { key: 'reset-btn', onClick: handleResetFilter }, 'Reset')
                    ])
                ]),

                // Summary Statistics
                React.createElement(Row, { key: 'summary-row', gutter: [16, 16], style: { marginBottom: '32px' } }, [
                    React.createElement(Col, { key: 'total-diagnoses', xs: 12, md: 6 },
                        React.createElement(Card, { className: 'summary-card' },
                            React.createElement(Statistic, {
                                title: 'Total Diagnosis',
                                value: summary.totalDiagnoses,
                                valueStyle: { color: '#1890ff' }
                            })
                        )
                    ),
                    React.createElement(Col, { key: 'unique-diseases', xs: 12, md: 6 },
                        React.createElement(Card, { className: 'summary-card' },
                            React.createElement(Statistic, {
                                title: 'Jenis Penyakit',
                                value: summary.uniqueDiseases,
                                valueStyle: { color: '#52c41a' }
                            })
                        )
                    ),
                    React.createElement(Col, { key: 'total-patients', xs: 12, md: 6 },
                        React.createElement(Card, { className: 'summary-card' },
                            React.createElement(Statistic, {
                                title: 'Total Pasien',
                                value: summary.totalPatients,
                                valueStyle: { color: '#faad14' }
                            })
                        )
                    ),
                    React.createElement(Col, { key: 'top-percentage', xs: 12, md: 6 },
                        React.createElement(Card, { className: 'summary-card' },
                            React.createElement(Statistic, {
                                title: 'Penyakit Tertinggi',
                                value: summary.topDiseasePercentage,
                                suffix: '%',
                                valueStyle: { color: '#f5222d' }
                            })
                        )
                    )
                ]),

                // Charts
                React.createElement(Row, { key: 'charts-row', gutter: [16, 16] }, [
                    React.createElement(Col, { key: 'main-chart', xs: 24, lg: 12 },
                        React.createElement(Card, {
                            title: 'Top 10 Penyakit Terbanyak',
                            loading: chartLoading,
                            className: 'chart-card'
                        }, React.createElement('div', { key: 'chart', id: 'top-diseases-chart', className: 'chart-container' }))
                    ),
                    React.createElement(Col, { key: 'gender-chart', xs: 24, lg: 12 },
                        React.createElement(Card, {
                            title: 'Distribusi Gender',
                            loading: chartLoading,
                            className: 'chart-card'
                        }, React.createElement('div', { key: 'chart', id: 'gender-chart', className: 'chart-container' }))
                    ),
                    React.createElement(Col, { key: 'trend-chart', xs: 24 },
                        React.createElement(Card, {
                            title: 'Tren Bulanan',
                            loading: chartLoading,
                            className: 'chart-card'
                        }, React.createElement('div', { key: 'chart', id: 'trend-chart', className: 'chart-container' }))
                    )
                ])
            ]);
        };

        ReactDOM.render(
            React.createElement(TopPenyakitAnalysis),
            document.getElementById('top-penyakit-app')
        );
    };

    // Initialize the app when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeApp);
    } else {
        initializeApp();
    }
    </script>
@stop