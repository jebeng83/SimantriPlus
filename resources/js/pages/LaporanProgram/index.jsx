import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Statistic, Progress, Badge, Space, Typography, Button, Tooltip } from 'antd';
import {
    FileTextOutlined,
    BarChartOutlined,
    UserOutlined,
    CalendarOutlined,
    TrophyOutlined,
    HeartOutlined,
    MedicineBoxOutlined,
    TeamOutlined,
    RiseOutlined,
    DownloadOutlined,
    EyeOutlined,
    PrinterOutlined
} from '@ant-design/icons';
import './LaporanProgram.css';

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
            icon: <UserOutlined />,
            color: '#1890ff',
            gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            count: '1,247',
            increase: '+8.2%',
            action: 'view_patients'
        },
        {
            title: 'Laporan Program Kesehatan',
            description: 'Overview seluruh program kesehatan yang sedang berjalan',
            icon: <HeartOutlined />,
            color: '#52c41a',
            gradient: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            count: '12',
            increase: '+15.3%',
            action: 'view_health_programs'
        },
        {
            title: 'Laporan Kinerja Medis',
            description: 'Evaluasi kinerja tim medis dan pelayanan kesehatan',
            icon: <TrophyOutlined />,
            color: '#faad14',
            gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            count: '95.2%',
            increase: '+2.1%',
            action: 'view_performance'
        },
        {
            title: 'Laporan Farmasi',
            description: 'Monitoring persediaan dan penggunaan obat-obatan',
            icon: <MedicineBoxOutlined />,
            color: '#eb2f96',
            gradient: 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
            count: '89.7%',
            increase: '+5.4%',
            action: 'view_pharmacy'
        },
        {
            title: 'Laporan Tim Medis',
            description: 'Data kehadiran dan jadwal tim medis',
            icon: <TeamOutlined />,
            color: '#722ed1',
            gradient: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
            count: '156',
            increase: '+3.7%',
            action: 'view_medical_team'
        },
        {
            title: 'Laporan Bulanan',
            description: 'Ringkasan laporan aktivitas bulanan',
            icon: <CalendarOutlined />,
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
            prefix: <UserOutlined />
        },
        {
            title: 'Program Aktif',
            value: 12,
            suffix: 'program',
            valueStyle: { color: '#1890ff' },
            prefix: <HeartOutlined />
        },
        {
            title: 'Tingkat Kepuasan',
            value: 95.2,
            suffix: '%',
            valueStyle: { color: '#cf1322' },
            prefix: <TrophyOutlined />
        },
        {
            title: 'Kapasitas Terisi',
            value: 78,
            suffix: '%',
            valueStyle: { color: '#722ed1' },
            prefix: <RiseOutlined />
        }
    ];

    const handleCardAction = (action) => {
        console.log(`Action: ${action}`);
        // Implement navigation or action logic here
    };

    return (
        <div className="laporan-program-dashboard">
            {/* Header Section */}
            <div className="dashboard-header">
                <div className="header-content">
                    <Title level={2} className="dashboard-title">
                        <BarChartOutlined className="title-icon" />
                        Dashboard Laporan Program
                    </Title>
                    <Text className="dashboard-subtitle">
                        Pantau dan kelola seluruh laporan program rumah sakit
                    </Text>
                </div>
                <div className="header-actions">
                    <Button type="primary" icon={<DownloadOutlined />} size="large">
                        Export All
                    </Button>
                </div>
            </div>

            {/* Quick Stats */}
            <Row gutter={[24, 24]} className="quick-stats-section">
                {quickStats.map((stat, index) => (
                    <Col xs={24} sm={12} lg={6} key={index}>
                        <Card className="stat-card" loading={loading}>
                            <Statistic
                                title={stat.title}
                                value={stat.value}
                                suffix={stat.suffix}
                                valueStyle={stat.valueStyle}
                                prefix={stat.prefix}
                            />
                        </Card>
                    </Col>
                ))}
            </Row>

            {/* Progress Overview */}
            <Row gutter={[24, 24]} className="progress-section">
                <Col span={24}>
                    <Card className="progress-overview-card">
                        <Title level={4}>Progress Program Kesehatan</Title>
                        <Row gutter={[16, 16]}>
                            <Col xs={24} md={8}>
                                <div className="progress-item">
                                    <Text>Program Screening PKG</Text>
                                    <Progress 
                                        percent={85} 
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
                                    <Text>Program Vaksinasi</Text>
                                    <Progress 
                                        percent={92} 
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
                                    <Text>Program Kesehatan Lansia</Text>
                                    <Progress 
                                        percent={78} 
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

            {/* Main Program Cards */}
            <Row gutter={[24, 24]} className="program-cards-section">
                {programCards.map((program, index) => (
                    <Col xs={24} sm={12} lg={8} key={index}>
                        <Card
                            className="program-card"
                            loading={loading}
                            hoverable
                            cover={
                                <div 
                                    className="card-cover" 
                                    style={{ background: program.gradient }}
                                >
                                    <div className="card-icon">
                                        {program.icon}
                                    </div>
                                    <div className="card-stats">
                                        <div className="card-count">{program.count}</div>
                                        <Badge 
                                            count={program.increase} 
                                            style={{ 
                                                backgroundColor: 'rgba(255,255,255,0.2)',
                                                color: 'white',
                                                border: '1px solid rgba(255,255,255,0.3)'
                                            }} 
                                        />
                                    </div>
                                </div>
                            }
                            actions={[
                                <Tooltip title="Lihat Detail">
                                    <EyeOutlined 
                                        key="view" 
                                        onClick={() => handleCardAction(program.action)}
                                    />
                                </Tooltip>,
                                <Tooltip title="Download">
                                    <DownloadOutlined key="download" />
                                </Tooltip>,
                                <Tooltip title="Print">
                                    <PrinterOutlined key="print" />
                                </Tooltip>
                            ]}
                        >
                            <Meta
                                title={
                                    <Space>
                                        <span style={{ color: program.color }}>
                                            {program.title}
                                        </span>
                                    </Space>
                                }
                                description={program.description}
                            />
                        </Card>
                    </Col>
                ))}
            </Row>

            {/* Recent Activity */}
            <Row gutter={[24, 24]} className="recent-activity-section">
                <Col span={24}>
                    <Card className="recent-activity-card">
                        <Title level={4}>Aktivitas Terbaru</Title>
                        <div className="activity-list">
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <FileTextOutlined style={{ color: '#1890ff' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Laporan Bulanan November telah dibuat</Text>
                                    <br />
                                    <Text type="secondary">2 jam yang lalu</Text>
                                </div>
                            </div>
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <UserOutlined style={{ color: '#52c41a' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Data pasien rawat inap diperbarui</Text>
                                    <br />
                                    <Text type="secondary">4 jam yang lalu</Text>
                                </div>
                            </div>
                            <div className="activity-item">
                                <div className="activity-icon">
                                    <TrophyOutlined style={{ color: '#faad14' }} />
                                </div>
                                <div className="activity-content">
                                    <Text strong>Evaluasi kinerja medis selesai</Text>
                                    <br />
                                    <Text type="secondary">1 hari yang lalu</Text>
                                </div>
                            </div>
                        </div>
                    </Card>
                </Col>
            </Row>
        </div>
    );
};

export default LaporanProgram;