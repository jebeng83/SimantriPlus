@extends('adminlte::page')

@section('title', 'Grafik & Analisa')

@section('content_header')
<h1>Grafik & Analisa</h1>
@stop

@section('content')
<div id="grafik-analisa-app">
   <!-- Loading state while React component loads -->
   <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
      <div class="text-center">
         <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
         </div>
         <p class="mt-2">Loading Dashboard...</p>
      </div>
   </div>
</div>
@stop

@section('css')
<!-- Ant Design CSS -->
<link href="https://unpkg.com/antd@4.24.15/dist/antd.min.css" rel="stylesheet">
<style>
   /* Ensure the container takes full width */
   #grafik-analisa-app {
      width: 100%;
      min-height: 100vh;
   }

   /* Override AdminLTE styles if needed */
   .content-wrapper {
      background: #f4f6f9;
   }
</style>
@stop

@section('js')
<!-- React and ReactDOM -->
<script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>

<!-- Ant Design -->
<script src="https://unpkg.com/antd@4.24.15/dist/antd.min.js"></script>

<!-- Babel Standalone for JSX -->
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- Our React Component -->
<script type="text/babel">
   const { useState, useEffect } = React;
        const { Card, Row, Col, Statistic, Badge, Space, Typography, Button, Tooltip, Progress, Divider } = antd;
        
        // Create icon components using emoji since Ant Design icons are not available via CDN
        const createIcon = (emoji) => React.createElement('span', { style: { fontSize: '18px' } }, emoji);
        
        const { Title, Text, Paragraph } = Typography;
        const { Meta } = Card;

        const GrafikAnalisa = () => {
            const [loading, setLoading] = useState(true);
            const [stats, setStats] = useState({
                totalGraphs: 24,
                activeAnalysis: 8,
                completedReports: 156,
                dataAccuracy: 97.8
            });

            useEffect(() => {
                // Simulate loading data
                setTimeout(() => {
                    setLoading(false);
                }, 1000);
            }, []);

            const analyticsCards = [
                {
                    title: 'Analisa Demografis Pasien',
                    description: 'Visualisasi distribusi pasien berdasarkan usia, jenis kelamin, dan lokasi geografis',
                    icon: createIcon('📊'),
                    color: '#1890ff',
                    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    chartType: 'Pie Chart',
                    dataPoints: '2,847',
                    category: 'demographic',
                    status: 'active'
                },
                {
                    title: 'Trend Kunjungan Bulanan',
                    description: 'Analisis pola kunjungan pasien per bulan dengan prediksi trend masa depan',
                    icon: createIcon('📈'),
                    color: '#52c41a',
                    gradient: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
                    chartType: 'Line Chart',
                    dataPoints: '1,456',
                    category: 'trends',
                    status: 'active'
                },
                {
                    title: 'Distribusi Penyakit',
                    description: 'Mapping prevalensi penyakit terbanyak dan analisis epidemiologi',
                    icon: createIcon('📊'),
                    color: '#faad14',
                    gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    chartType: 'Bar Chart',
                    dataPoints: '3,234',
                    category: 'disease',
                    status: 'processing'
                },
                {
                    title: 'Analisa Kinerja Medis',
                    description: 'Evaluasi efektivitas treatment dan tingkat kesembuhan pasien',
                    icon: createIcon('🏥'),
                    color: '#eb2f96',
                    gradient: 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
                    chartType: 'Area Chart',
                    dataPoints: '987',
                    category: 'performance',
                    status: 'completed'
                },
                {
                    title: 'Heatmap Aktivitas Harian',
                    description: 'Pola aktivitas rumah sakit berdasarkan jam operasional dan hari',
                    icon: createIcon('🔥'),
                    color: '#722ed1',
                    gradient: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                    chartType: 'Heatmap',
                    dataPoints: '7,656',
                    category: 'activity',
                    status: 'active'
                },
                {
                    title: 'Dashboard Real-time',
                    description: 'Monitoring live data dengan update otomatis setiap 5 menit',
                    icon: createIcon('⚡'),
                    color: '#13c2c2',
                    gradient: 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                    chartType: 'Multi Chart',
                    dataPoints: 'Live',
                    category: 'realtime',
                    status: 'active'
                },
                {
                    title: 'Analisa Farmasi',
                    description: 'Tracking penggunaan obat, stok, dan efektivitas treatment',
                    icon: createIcon('💊'),
                    color: '#f5222d',
                    gradient: 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                    chartType: 'Mixed Chart',
                    dataPoints: '4,523',
                    category: 'pharmacy',
                    status: 'scheduled'
                },
                {
                    title: 'Predictive Analytics',
                    description: 'Machine learning untuk prediksi capacity planning dan resource allocation',
                    icon: createIcon('🤖'),
                    color: '#fa8c16',
                    gradient: 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                    chartType: 'Prediction Model',
                    dataPoints: '12,890',
                    category: 'prediction',
                    status: 'beta'
                },
                {
                    title: 'Laporan Eksekutif',
                    description: 'Summary dashboard untuk manajemen dengan KPI dan metrics utama',
                    icon: createIcon('📋'),
                    color: '#531dab',
                    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    chartType: 'Executive Dashboard',
                    dataPoints: 'All Data',
                    category: 'executive',
                    status: 'active'
                }
            ];

            const quickMetrics = [
                {
                    title: 'Total Grafik Aktif',
                    value: 24,
                    suffix: 'charts',
                    valueStyle: { color: '#3f8600' },
                    prefix: createIcon('📊')
                },
                {
                    title: 'Analisa Berjalan',
                    value: 8,
                    suffix: 'processes',
                    valueStyle: { color: '#1890ff' },
                    prefix: createIcon('⚙️')
                },
                {
                    title: 'Akurasi Data',
                    value: 97.8,
                    suffix: '%',
                    valueStyle: { color: '#cf1322' },
                    prefix: createIcon('🎯')
                },
                {
                    title: 'Update Terakhir',
                    value: 5,
                    suffix: 'min ago',
                    valueStyle: { color: '#722ed1' },
                    prefix: createIcon('🔄')
                }
            ];

            const getStatusColor = (status) => {
                const colors = {
                    active: '#52c41a',
                    processing: '#faad14',
                    completed: '#1890ff',
                    scheduled: '#722ed1',
                    beta: '#fa8c16'
                };
                return colors[status] || '#d9d9d9';
            };

            const getStatusText = (status) => {
                const texts = {
                    active: 'Aktif',
                    processing: 'Proses',
                    completed: 'Selesai',
                    scheduled: 'Terjadwal',
                    beta: 'Beta'
                };
                return texts[status] || 'Unknown';
            };

            const handleCardAction = (category, action) => {
                console.log(`Action: ${action} for category: ${category}`);
                // Navigasi spesifik berdasarkan kategori
                if (action === 'view') {
                    if (category === 'demographic') {
                        window.location.href = '/ranap/laporan/demografi-pasien';
                        return;
                    }
                    if (category === 'disease') {
                        window.location.href = '/ranap/laporan/top-penyakit';
                        return;
                    }
                }
            };

            return React.createElement('div', {
                style: {
                    padding: '24px',
                    background: 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
                    minHeight: '100vh'
                }
            }, [
                // Header Section
                React.createElement('div', {
                    key: 'header',
                    style: {
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'flex-start',
                        marginBottom: '32px',
                        padding: '24px 0'
                    }
                }, [
                    React.createElement('div', { key: 'header-content' }, [
                        React.createElement(Title, {
                            key: 'title',
                            level: 2,
                            style: {
                                margin: 0,
                                color: '#1f2937',
                                fontWeight: 700,
                                display: 'flex',
                                alignItems: 'center',
                                gap: '12px'
                            }
                        }, [
                            React.createElement('span', { key: 'icon', style: { color: '#3b82f6', fontSize: '28px' } }, '📊'),
                            'Dashboard Grafik & Analisa'
                        ]),
                        React.createElement(Paragraph, {
                            key: 'subtitle',
                            style: {
                                color: '#6b7280',
                                fontSize: '16px',
                                marginTop: '8px',
                                marginBottom: 0
                            }
                        }, 'Pusat kendali untuk semua visualisasi data dan analisis mendalam rumah sakit')
                    ]),
                    React.createElement('div', { key: 'header-actions' }, [
                        React.createElement(Space, { key: 'actions' }, [
                            React.createElement(Button, {
                                key: 'refresh',
                                icon: createIcon('🔄'),
                                size: 'large'
                            }, 'Refresh'),
                            React.createElement(Button, {
                                key: 'export',
                                type: 'primary',
                                icon: createIcon('⬇️'),
                                size: 'large'
                            }, 'Export All')
                        ])
                    ])
                ]),
                
                // Quick Metrics
                React.createElement(Row, {
                    key: 'quick-metrics',
                    gutter: [24, 24],
                    style: { marginBottom: '32px' }
                }, quickMetrics.map((metric, index) => 
                    React.createElement(Col, {
                        key: index,
                        xs: 24,
                        sm: 12,
                        lg: 6
                    }, [
                        React.createElement(Card, {
                            key: 'metric-card',
                            loading: loading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'statistic',
                                title: metric.title,
                                value: metric.value,
                                suffix: metric.suffix,
                                valueStyle: metric.valueStyle,
                                prefix: metric.prefix
                            })
                        ])
                    ])
                )),
                
                // Progress Overview
                React.createElement(Row, {
                    key: 'progress',
                    gutter: [24, 24],
                    style: { marginBottom: '32px' }
                }, [
                    React.createElement(Col, {
                        key: 'progress-col',
                        span: 24
                    }, [
                        React.createElement(Card, {
                            key: 'progress-card',
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Title, {
                                key: 'progress-title',
                                level: 4
                            }, 'Progress Analisa Real-time'),
                            React.createElement(Row, {
                                key: 'progress-row',
                                gutter: [16, 16]
                            }, [
                                React.createElement(Col, {
                                    key: 'col1',
                                    xs: 24,
                                    md: 8
                                }, [
                                    React.createElement('div', {
                                        key: 'progress-item1',
                                        style: { marginBottom: '16px' }
                                    }, [
                                        React.createElement(Text, { key: 'text1' }, 'Data Collection'),
                                        React.createElement(Progress, {
                                            key: 'progress1',
                                            percent: 92,
                                            status: 'active',
                                            strokeColor: {
                                                from: '#108ee9',
                                                to: '#87d068'
                                            }
                                        })
                                    ])
                                ]),
                                React.createElement(Col, {
                                    key: 'col2',
                                    xs: 24,
                                    md: 8
                                }, [
                                    React.createElement('div', {
                                        key: 'progress-item2',
                                        style: { marginBottom: '16px' }
                                    }, [
                                        React.createElement(Text, { key: 'text2' }, 'Processing Analytics'),
                                        React.createElement(Progress, {
                                            key: 'progress2',
                                            percent: 76,
                                            status: 'active',
                                            strokeColor: {
                                                from: '#87d068',
                                                to: '#108ee9'
                                            }
                                        })
                                    ])
                                ]),
                                React.createElement(Col, {
                                    key: 'col3',
                                    xs: 24,
                                    md: 8
                                }, [
                                    React.createElement('div', {
                                        key: 'progress-item3',
                                        style: { marginBottom: '16px' }
                                    }, [
                                        React.createElement(Text, { key: 'text3' }, 'Report Generation'),
                                        React.createElement(Progress, {
                                            key: 'progress3',
                                            percent: 85,
                                            status: 'active',
                                            strokeColor: {
                                                from: '#ff7875',
                                                to: '#ffadd6'
                                            }
                                        })
                                    ])
                                ])
                            ])
                        ])
                    ])
                ]),
                
                // Analytics Cards
                React.createElement(Row, {
                    key: 'analytics-cards',
                    gutter: [24, 24],
                    style: { marginBottom: '32px' }
                }, analyticsCards.map((card, index) => 
                    React.createElement(Col, {
                        key: index,
                        xs: 24,
                        sm: 12,
                        lg: 8
                    }, [
                        React.createElement(Card, {
                            key: 'analytics-card',
                            loading: loading,
                            hoverable: true,
                            onClick: card.category === 'disease' ? () => { try { window.location.href = '/ranap/laporan/top-penyakit'; } catch (e) { document.location.href = '/ranap/laporan/top-penyakit'; } } : undefined,
                            style: {
                                borderRadius: '20px',
                                border: 'none',
                                boxShadow: '0 8px 32px rgba(0, 0, 0, 0.1)',
                                overflow: 'hidden'
                            },
                            cover: React.createElement('div', {
                                style: {
                                    height: '140px',
                                    background: card.gradient,
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'space-between',
                                    padding: '24px',
                                    color: 'white'
                                }
                            }, [
                                React.createElement('div', {
                                    key: 'icon',
                                    style: {
                                        fontSize: '36px',
                                        background: 'rgba(255, 255, 255, 0.2)',
                                        width: '72px',
                                        height: '72px',
                                        borderRadius: '18px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center'
                                    }
                                }, card.icon),
                                React.createElement('div', {
                                    key: 'stats',
                                    style: { textAlign: 'right' }
                                }, [
                                    React.createElement('div', {
                                        key: 'data-points',
                                        style: {
                                            fontSize: '20px',
                                            fontWeight: 'bold',
                                            marginBottom: '8px'
                                        }
                                    }, card.dataPoints),
                                    React.createElement(Badge, {
                                        key: 'badge',
                                        count: getStatusText(card.status),
                                        style: {
                                            backgroundColor: getStatusColor(card.status),
                                            color: 'white'
                                        }
                                    })
                                ])
                            ]),
                            actions: [
                                React.createElement('span', {
                                    key: 'view',
                                    onClick: () => handleCardAction(card.category, 'view'),
                                    style: { cursor: 'pointer' }
                                }, '👁️'),
                                React.createElement('span', {
                                    key: 'download',
                                    onClick: () => handleCardAction(card.category, 'download'),
                                    style: { cursor: 'pointer' }
                                }, '⬇️'),
                                React.createElement('span', {
                                    key: 'print',
                                    onClick: () => handleCardAction(card.category, 'print'),
                                    style: { cursor: 'pointer' }
                                }, '🖨️')
                            ]
                        }, [
                            React.createElement(Meta, {
                                key: 'meta',
                                title: React.createElement(Space, {
                                    direction: 'vertical',
                                    size: 4
                                }, [
                                    (card.category === 'disease'
                                        ? React.createElement('a', {
                                            key: 'title-link',
                                            href: '/ranap/laporan/top-penyakit',
                                            style: { color: card.color, cursor: 'pointer', textDecoration: 'none' },
                                            onClick: (e) => { e.preventDefault(); e.stopPropagation(); try { window.location.href = '/ranap/laporan/top-penyakit'; } catch (err) { document.location.href = '/ranap/laporan/top-penyakit'; } }
                                        }, card.title)
                                        : React.createElement('span', {
                                            key: 'title',
                                            style: { color: card.color }
                                        }, card.title)
                                    ),
                                    React.createElement(Text, {
                                        key: 'type',
                                        type: 'secondary',
                                        style: { fontSize: '12px' }
                                    }, card.chartType)
                                ]),
                                description: card.description
                            })
                        ])
                    ])
                )),
                
                // Recent Activities
                React.createElement(Row, {
                    key: 'activities',
                    gutter: [24, 24]
                }, [
                    React.createElement(Col, {
                        key: 'activities-col',
                        span: 24
                    }, [
                        React.createElement(Card, {
                            key: 'activities-card',
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Title, {
                                key: 'activities-title',
                                level: 4
                            }, 'Aktivitas Analisa Terbaru'),
                            React.createElement('div', {
                                key: 'activities-list',
                                style: { padding: '16px 0' }
                            }, [
                                React.createElement('div', {
                                    key: 'activity1',
                                    style: {
                                        display: 'flex',
                                        alignItems: 'flex-start',
                                        gap: '16px',
                                        padding: '12px 0'
                                    }
                                }, [
                                    React.createElement('div', {
                                        key: 'icon1',
                                        style: {
                                            width: '40px',
                                            height: '40px',
                                            borderRadius: '12px',
                                            background: 'rgba(24, 144, 255, 0.1)',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '18px'
                                        }
                                    }, '📊'),
                                    React.createElement('div', {
                                        key: 'content1',
                                        style: { flex: 1 }
                                    }, [
                                        React.createElement(Text, {
                                            key: 'text1',
                                            strong: true
                                        }, 'Analisa demografis pasien telah diperbarui'),
                                        React.createElement('br', { key: 'br1' }),
                                        React.createElement(Text, {
                                            key: 'time1',
                                            type: 'secondary'
                                        }, '15 menit yang lalu • 2,847 data points')
                                    ]),
                                    React.createElement('div', {
                                        key: 'status1'
                                    }, [
                                        React.createElement(Badge, {
                                            key: 'badge1',
                                            status: 'success',
                                            text: 'Completed'
                                        })
                                    ])
                                ]),
                                React.createElement(Divider, {
                                    key: 'divider1',
                                    style: { margin: '12px 0' }
                                }),
                                React.createElement('div', {
                                    key: 'activity2',
                                    style: {
                                        display: 'flex',
                                        alignItems: 'flex-start',
                                        gap: '16px',
                                        padding: '12px 0'
                                    }
                                }, [
                                    React.createElement('div', {
                                        key: 'icon2',
                                        style: {
                                            width: '40px',
                                            height: '40px',
                                            borderRadius: '12px',
                                            background: 'rgba(82, 196, 26, 0.1)',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '18px'
                                        }
                                    }, '📈'),
                                    React.createElement('div', {
                                        key: 'content2',
                                        style: { flex: 1 }
                                    }, [
                                        React.createElement(Text, {
                                            key: 'text2',
                                            strong: true
                                        }, 'Trend kunjungan Q4 sedang diproses'),
                                        React.createElement('br', { key: 'br2' }),
                                        React.createElement(Text, {
                                            key: 'time2',
                                            type: 'secondary'
                                        }, '1 jam yang lalu • Progress: 76%')
                                    ]),
                                    React.createElement('div', {
                                        key: 'status2'
                                    }, [
                                        React.createElement(Badge, {
                                            key: 'badge2',
                                            status: 'processing',
                                            text: 'Processing'
                                        })
                                    ])
                                ]),
                                React.createElement(Divider, {
                                    key: 'divider2',
                                    style: { margin: '12px 0' }
                                }),
                                React.createElement('div', {
                                    key: 'activity3',
                                    style: {
                                        display: 'flex',
                                        alignItems: 'flex-start',
                                        gap: '16px',
                                        padding: '12px 0'
                                    }
                                }, [
                                    React.createElement('div', {
                                        key: 'icon3',
                                        style: {
                                            width: '40px',
                                            height: '40px',
                                            borderRadius: '12px',
                                            background: 'rgba(114, 46, 209, 0.1)',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '18px'
                                        }
                                    }, '⚡'),
                                    React.createElement('div', {
                                        key: 'content3',
                                        style: { flex: 1 }
                                    }, [
                                        React.createElement(Text, {
                                            key: 'text3',
                                            strong: true
                                        }, 'Dashboard real-time telah dimulai'),
                                        React.createElement('br', { key: 'br3' }),
                                        React.createElement(Text, {
                                            key: 'time3',
                                            type: 'secondary'
                                        }, '3 jam yang lalu • Live monitoring')
                                    ]),
                                    React.createElement('div', {
                                        key: 'status3'
                                    }, [
                                        React.createElement(Badge, {
                                            key: 'badge3',
                                            status: 'success',
                                            text: 'Active'
                                        })
                                    ])
                                ])
                            ])
                        ])
                    ])
                ])
            ]);
        };

        // Render the component
        ReactDOM.render(
            React.createElement(GrafikAnalisa),
            document.getElementById('grafik-analisa-app')
        );
    </script>
@stop