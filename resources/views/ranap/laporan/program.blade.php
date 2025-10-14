@extends('adminlte::page')

@section('title', 'Laporan Program')

@section('content_header')
<h1>Laporan Program</h1>
@stop

@section('content')
<div id="laporan-program-app">
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
   #laporan-program-app {
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
        const { Card, Row, Col, Statistic, Progress, Badge, Space, Typography, Button, Tooltip } = antd;
        
        // Create icon components using emoji since Ant Design icons are not available via CDN
        const createIcon = (emoji) => React.createElement('span', { style: { fontSize: '16px' } }, emoji);
        
        const { Title, Text } = Typography;
        const { Meta } = Card;

        const LaporanProgram = () => {
            const [loading, setLoading] = useState(true);
            const [stats, setStats] = useState({
                totalPatients: 1247,
                totalPrograms: 12,
                completionRate: 85,
                monthlyIncrease: 12.5
            });

            useEffect(() => {
                // Simulate loading data
                setTimeout(() => {
                    setLoading(false);
                }, 1000);
            }, []);

            const programCards = [
                {
                    title: 'Laporan Pasien Rawat Inap',
                    description: 'Data komprehensif pasien yang sedang menjalani rawat inap',
                    icon: createIcon('👤'),
                    color: '#1890ff',
                    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    count: '1,247',
                    increase: '+8.2%',
                    action: 'view_patients'
                },
                {
                    title: 'Laporan Program Kesehatan',
                    description: 'Overview seluruh program kesehatan yang sedang berjalan',
                    icon: createIcon('❤️'),
                    color: '#52c41a',
                    gradient: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
                    count: '12',
                    increase: '+15.3%',
                    action: 'view_health_programs'
                },
                {
                    title: 'Laporan Kinerja Medis',
                    description: 'Evaluasi kinerja tim medis dan pelayanan kesehatan',
                    icon: createIcon('🏆'),
                    color: '#faad14',
                    gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    count: '95.2%',
                    increase: '+2.1%',
                    action: 'view_performance'
                },
                {
                    title: 'Laporan Farmasi',
                    description: 'Monitoring persediaan dan penggunaan obat-obatan',
                    icon: createIcon('💊'),
                    color: '#eb2f96',
                    gradient: 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
                    count: '89.7%',
                    increase: '+5.4%',
                    action: 'view_pharmacy'
                },
                {
                    title: 'Laporan Tim Medis',
                    description: 'Data kehadiran dan jadwal tim medis',
                    icon: createIcon('👥'),
                    color: '#722ed1',
                    gradient: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                    count: '156',
                    increase: '+3.7%',
                    action: 'view_medical_team'
                },
                {
                    title: 'Laporan Bulanan',
                    description: 'Ringkasan laporan aktivitas bulanan',
                    icon: createIcon('📅'),
                    color: '#13c2c2',
                    gradient: 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                    count: 'Nov 2024',
                    increase: 'Ready',
                    action: 'view_monthly'
                }
            ];

            const quickStats = [
                {
                    title: 'Total Pasien Hari Ini',
                    value: 89,
                    suffix: 'orang',
                    valueStyle: { color: '#3f8600' },
                    prefix: createIcon('👤')
                },
                {
                    title: 'Program Aktif',
                    value: 12,
                    suffix: 'program',
                    valueStyle: { color: '#1890ff' },
                    prefix: createIcon('❤️')
                },
                {
                    title: 'Tingkat Kepuasan',
                    value: 95.2,
                    suffix: '%',
                    valueStyle: { color: '#cf1322' },
                    prefix: createIcon('🏆')
                },
                {
                    title: 'Kapasitas Terisi',
                    value: 78,
                    suffix: '%',
                    valueStyle: { color: '#722ed1' },
                    prefix: createIcon('📈')
                }
            ];

            const handleCardAction = (action) => {
                console.log(`Action: ${action}`);
                // Implement navigation or action logic here
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
                            'Dashboard Laporan Program'
                        ]),
                        React.createElement(Text, {
                            key: 'subtitle',
                            style: {
                                color: '#6b7280',
                                fontSize: '16px',
                                marginTop: '8px',
                                display: 'block'
                            }
                        }, 'Pantau dan kelola seluruh laporan program rumah sakit')
                    ]),
                    React.createElement('div', { key: 'header-actions' }, [
                        React.createElement(Button, {
                            key: 'export-btn',
                            type: 'primary',
                            icon: createIcon('⬇️'),
                            size: 'large'
                        }, 'Export All')
                    ])
                ]),
                
                // Quick Stats
                React.createElement(Row, {
                    key: 'quick-stats',
                    gutter: [24, 24],
                    style: { marginBottom: '32px' }
                }, quickStats.map((stat, index) => 
                    React.createElement(Col, {
                        key: index,
                        xs: 24,
                        sm: 12,
                        lg: 6
                    }, [
                        React.createElement(Card, {
                            key: 'stat-card',
                            loading: loading,
                            style: {
                                borderRadius: '16px',
                                border: 'none',
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.08)'
                            }
                        }, [
                            React.createElement(Statistic, {
                                key: 'statistic',
                                title: stat.title,
                                value: stat.value,
                                suffix: stat.suffix,
                                valueStyle: stat.valueStyle,
                                prefix: stat.prefix
                            })
                        ])
                    ])
                )),
                
                // Program Cards
                React.createElement(Row, {
                    key: 'program-cards',
                    gutter: [24, 24]
                }, programCards.map((program, index) => 
                    React.createElement(Col, {
                        key: index,
                        xs: 24,
                        sm: 12,
                        lg: 8
                    }, [
                        React.createElement(Card, {
                            key: 'program-card',
                            loading: loading,
                            hoverable: true,
                            style: {
                                borderRadius: '20px',
                                border: 'none',
                                boxShadow: '0 8px 32px rgba(0, 0, 0, 0.1)',
                                overflow: 'hidden'
                            },
                            cover: React.createElement('div', {
                                style: {
                                    height: '120px',
                                    background: program.gradient,
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
                                        fontSize: '32px',
                                        background: 'rgba(255, 255, 255, 0.2)',
                                        width: '64px',
                                        height: '64px',
                                        borderRadius: '16px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center'
                                    }
                                }, program.icon),
                                React.createElement('div', {
                                    key: 'stats',
                                    style: { textAlign: 'right' }
                                }, [
                                    React.createElement('div', {
                                        key: 'count',
                                        style: {
                                            fontSize: '24px',
                                            fontWeight: 'bold',
                                            marginBottom: '8px'
                                        }
                                    }, program.count),
                                    React.createElement(Badge, {
                                        key: 'badge',
                                        count: program.increase,
                                        style: {
                                            backgroundColor: 'rgba(255,255,255,0.2)',
                                            color: 'white',
                                            border: '1px solid rgba(255,255,255,0.3)'
                                        }
                                    })
                                ])
                            ]),
                            actions: [
                                React.createElement('span', {
                                    key: 'view',
                                    onClick: () => handleCardAction(program.action),
                                    style: { cursor: 'pointer' }
                                }, '👁️'),
                                React.createElement('span', { key: 'download' }, '⬇️'),
                                React.createElement('span', { key: 'print' }, '🖨️')
                            ]
                        }, [
                            React.createElement(Meta, {
                                key: 'meta',
                                title: React.createElement(Space, {}, [
                                    React.createElement('span', {
                                        key: 'title',
                                        style: { color: program.color }
                                    }, program.title)
                                ]),
                                description: program.description
                            })
                        ])
                    ])
                ))
            ]);
        };

        // Render the component
        ReactDOM.render(
            React.createElement(LaporanProgram),
            document.getElementById('laporan-program-app')
        );
    </script>
@stop