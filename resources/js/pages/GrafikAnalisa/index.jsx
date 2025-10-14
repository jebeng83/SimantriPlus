import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Statistic, Badge, Space, Typography, Button, Tooltip, Progress, Divider } from 'antd';
import {
    BarChartOutlined,
    PieChartOutlined,
    LineChartOutlined,
    AreaChartOutlined,
    DotChartOutlined,
    FundProjectionScreenOutlined,
    TrendingUpOutlined,
    AnalyticsOutlined,
    DashboardOutlined,
    FileImageOutlined,
    DownloadOutlined,
    EyeOutlined,
    PrinterOutlined,
    ReloadOutlined
} from '@ant-design/icons';
import './GrafikAnalisa.css';

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
            icon: <PieChartOutlined />,
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
            icon: <LineChartOutlined />,
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
            icon: <BarChartOutlined />,
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
            icon: <AreaChartOutlined />,
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
            icon: <DotChartOutlined />,
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
            icon: <DashboardOutlined />,
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
            icon: <FundProjectionScreenOutlined />,
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
            icon: <TrendingUpOutlined />,
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
            icon: <AnalyticsOutlined />,
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
            prefix: <BarChartOutlined />
        },
        {
            title: 'Analisa Berjalan',
            value: 8,
            suffix: 'processes',
            valueStyle: { color: '#1890ff' },
            prefix: <AnalyticsOutlined />
        },
        {
            title: 'Akurasi Data',
            value: 97.8,
            suffix: '%',
            valueStyle: { color: '#cf1322' },
            prefix: <DashboardOutlined />
        },
        {
            title: 'Update Terakhir',
            value: 5,
            suffix: 'min ago',
            valueStyle: { color: '#722ed1' },
            prefix: <ReloadOutlined />
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
        
        // Navigate to specific analysis pages
        if (action === 'view') {
            switch (category) {
                case 'demographic':
                    console.log('Navigating to demographic analysis...');
                    try {
                        window.location.href = '/ranap/laporan/demografi-pasien';
                    } catch (error) {
                        console.error('Navigation error:', error);
                    }
                    break;
                case 'disease':
                    console.log('Navigating to top diseases analysis...');
                    console.log('Attempting navigation to: /ranap/laporan/top-penyakit');
                    
                    // Immediate navigation without timeout
                    console.log('Executing immediate navigation...');
                    
                    // Create a temporary form for POST navigation (more reliable)
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '/ranap/laporan/top-penyakit';
                    form.style.display = 'none';
                    document.body.appendChild(form);
                    
                    try {
                        console.log('Submitting form navigation...');
                        form.submit();
                    } catch (formError) {
                        console.error('Form navigation failed:', formError);
                        // Fallback to direct location change
                        document.location = '/ranap/laporan/top-penyakit';
                    }
                    break;
                case 'trends':
                    console.log('Trend analysis - coming soon');
                    break;
                case 'performance':
                    console.log('Performance analysis - coming soon');
                    break;
                default:
                    console.log(`No route defined for category: ${category}`);
            }
        } else {
            console.log(`Action ${action} not implemented for category: ${category}`);
        }
    };

    return (
        <div className="grafik-analisa-dashboard">
            {/* Header Section */}
            <div className="dashboard-header">
                <div className="header-content">
                    <Title level={2} className="dashboard-title">
                        <BarChartOutlined className="title-icon" />
                        Dashboard Grafik & Analisa
                    </Title>
                    <Paragraph className="dashboard-subtitle">
                        Pusat kendali untuk semua visualisasi data dan analisis mendalam rumah sakit
                    </Paragraph>
                </div>
                <div className="header-actions">
                    <Space>
                        <Button icon={<ReloadOutlined />} size="large">
                            Refresh
                        </Button>
                        <Button type="primary" icon={<DownloadOutlined />} size="large">
                            Export All
                        </Button>
                    </Space>
                </div>
            </div>

            {/* Quick Metrics */}
            <Row gutter={[24, 24]} className="quick-metrics-section">
                {quickMetrics.map((metric, index) => (
                    <Col xs={24} sm={12} lg={6} key={index}>
                        <Card className="metric-card" loading={loading}>
                            <Statistic
                                title={metric.title}
                                value={metric.value}
                                suffix={metric.suffix}
                                valueStyle={metric.valueStyle}
                                prefix={metric.prefix}
                            />
                        </Card>
                    </Col>
                ))}
            </Row>

            {/* Analytics Progress Overview */}
            <Row gutter={[24, 24]} className="progress-section">
                <Col span={24}>
                    <Card className="progress-overview-card">
                        <Title level={4}>Progress Analisa Real-time</Title>
                        <Row gutter={[16, 16]}>
                            <Col xs={24} md={8}>
                                <div className="progress-item">
                                    <Text>Data Collection</Text>
                                    <Progress 
                                        percent={92} 
                                        status="active"
                                        strokeColor={{
                                            from: '#108ee9',
                                            to: '#87d068',
                                        }}
                                    />
                                </div>
                            </Col>
                            <Col xs={24} md={8}>
                                <div className="progress-item">
                                    <Text>Processing Analytics</Text>
                                    <Progress 
                                        percent={76} 
                                        status="active"
                                        strokeColor={{
                                            from: '#87d068',
                                            to: '#108ee9',
                                        }}
                                    />
                                </div>
                            </Col>
                            <Col xs={24} md={8}>
                                <div className="progress-item">
                                    <Text>Report Generation</Text>
                                    <Progress 
                                        percent={85} 
                                        status="active"
                                        strokeColor={{
                                            from: '#ff7875',
                                            to: '#ffadd6',
                                        }}
                                    />
                                </div>
                            </Col>
                        </Row>
                    </Card>
                </Col>
            </Row>

            {/* Main Analytics Cards */}
            <Row gutter={[24, 24]} className="analytics-cards-section">
                {analyticsCards.map((card, index) => (
                    <Col xs={24} sm={12} lg={8} key={index}>
                        <Card
                            className="analytics-card"
                            loading={loading}
                            hoverable
                            onClick={card.category === 'disease' ? () => {
                                try {
                                    document.location.href = '/ranap/laporan/top-penyakit';
                                } catch (err) {
                                    window.location.href = '/ranap/laporan/top-penyakit';
                                }
                            } : undefined}
                            cover={
                                <div 
                                    className="card-cover" 
                                    style={{ background: card.gradient }}
                                >
                                    <div className="card-icon">
                                        {card.icon}
                                    </div>
                                    <div className="card-stats">
                                        <div className="card-data-points">{card.dataPoints}</div>
                                        <Badge 
                                            count={getStatusText(card.status)} 
                                            style={{ 
                                                backgroundColor: getStatusColor(card.status),
                                                color: 'white'
                                            }} 
                                        />
                                    </div>
                                </div>
                            }
                            actions={[
                                <Tooltip title="Lihat Grafik" key="view-tooltip">
                                    <span>
                                        <EyeOutlined 
                                            key="view" 
                                            onClick={function(e) {
                                                e && e.preventDefault && e.preventDefault();
                                                e && e.stopPropagation && e.stopPropagation();
                                                console.log('Eye icon clicked for:', card.category);
                                                
                                                // Direct navigation for disease category
                                                if (card.category === 'disease') {
                                                    console.log('Direct navigation to top diseases...');
                                                    document.location.href = '/ranap/laporan/top-penyakit';
                                                } else {
                                                    handleCardAction(card.category, 'view');
                                                }
                                            }}
                                            style={{ cursor: 'pointer', fontSize: '16px', marginRight: '8px' }}
                                        />
                                        {/* Debug link - remove after testing */}
                                        {card.category === 'disease' && (
                                            React.createElement('a', {
                                                href: '/ranap/laporan/top-penyakit',
                                                style: { 
                                                    fontSize: '10px', 
                                                    color: '#1890ff',
                                                    textDecoration: 'none',
                                                    marginLeft: '4px'
                                                },
                                                onClick: function(e) {
                                                    e && e.stopPropagation && e.stopPropagation();
                                                    console.log('Direct link clicked');
                                                }
                                            }, '[Test]')
                                        )}
                                    </span>
                                </Tooltip>,
                                <Tooltip title="Download Data" key="download-tooltip">
                                    <DownloadOutlined 
                                        key="download"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            handleCardAction(card.category, 'download');
                                        }}
                                        style={{ cursor: 'pointer', fontSize: '16px' }}
                                    />
                                </Tooltip>,
                                <Tooltip title="Print Report" key="print-tooltip">
                                    <PrinterOutlined 
                                        key="print"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            handleCardAction(card.category, 'print');
                                        }}
                                        style={{ cursor: 'pointer', fontSize: '16px' }}
                                    />
                                </Tooltip>
                            ]}
                        >
                            <Meta
                                title={
                                    <Space direction="vertical" size={4}>
                                        {card.category === 'disease' ? (
                                            <a
                                                href="/ranap/laporan/top-penyakit"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    e.stopPropagation();
                                                    try {
                                                        document.location.href = '/ranap/laporan/top-penyakit';
                                                    } catch (err) {
                                                        window.location.href = '/ranap/laporan/top-penyakit';
                                                    }
                                                }}
                                                style={{ color: card.color, cursor: 'pointer' }}
                                            >
                                                {card.title}
                                            </a>
                                        ) : (
                                            <span style={{ color: card.color }}>
                                                {card.title}
                                            </span>
                                        )}
                                        <Text type="secondary" style={{ fontSize: '12px' }}>
                                            {card.chartType}
                                        </Text>
                                    </Space>
                                }
                                description={card.description}
                            />
                        </Card>
                    </Col>
                ))}
            </Row>

            {/* Recent Activities */}
            <Row gutter={[24, 24]} className="recent-activities-section">
                <Col span={24}>
                    <Card className="recent-activities-card">
                        <Title level={4}>Aktivitas Analisa Terbaru</Title>
                        <div className="activities-list">
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <BarChartOutlined style={{ color: '#1890ff' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Analisa demografis pasien telah diperbarui</Text>
                                    <br />
                                    <Text type="secondary">15 menit yang lalu • 2,847 data points</Text>
                                </div>
                                <div className="activity-status">
                                    <Badge status="success" text="Completed" />
                                </div>
                            </div>
                            
                            <Divider style={{ margin: '12px 0' }} />
                            
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <LineChartOutlined style={{ color: '#52c41a' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Trend kunjungan Q4 sedang diproses</Text>
                                    <br />
                                    <Text type="secondary">1 jam yang lalu • Progress: 76%</Text>
                                </div>
                                <div className="activity-status">
                                    <Badge status="processing" text="Processing" />
                                </div>
                            </div>
                            
                            <Divider style={{ margin: '12px 0' }} />
                            
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <DashboardOutlined style={{ color: '#722ed1' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Dashboard real-time telah dimulai</Text>
                                    <br />
                                    <Text type="secondary">3 jam yang lalu • Live monitoring</Text>
                                </div>
                                <div className="activity-status">
                                    <Badge status="success" text="Active" />
                                </div>
                            </div>
                        </div>
                    </Card>
                </Col>
            </Row>
        </div>
    );
};

export default GrafikAnalisa;