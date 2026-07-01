@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --bg-main: #f4f6f9;
        --card-bg: rgba(255, 255, 255, 0.95);
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }

    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; margin-top: -70px !important; }

    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }
    .page-hero::before {
        content: ''; position: absolute; width: 800px; height: 800px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, transparent 70%);
        top: -300px; right: -200px; border-radius: 50%; opacity: 0.5; filter: blur(80px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    .page-hero::after {
        content: ''; position: absolute; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
        bottom: -200px; left: -100px; border-radius: 50%; opacity: 0.4; filter: blur(60px);
        animation: pulseSlow2 12s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.15); opacity: 0.7; } }
    @keyframes pulseSlow2 { 0% { transform: scale(1); opacity: 0.3; } 100% { transform: scale(1.2); opacity: 0.6; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; }
    .hero-title-group h1 {
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }

    .dashboard-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface-glass); backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 20px;
        padding: 24px; box-shadow: var(--shadow-elevated);
        position: relative; overflow: hidden; transition: all 0.4s;
        display: flex !important; flex-direction: column; text-decoration: none !important;
        min-height: 120px; cursor: pointer;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.15); border-color: white; }
    .stat-card::before {
        content: ''; position: absolute; right: -10%; top: -10%; width: 120px; height: 120px;
        border-radius: 50%; opacity: 0.15; filter: blur(25px); transition: all 0.5s;
    }
    .stat-card:hover::before { transform: scale(2); opacity: 0.25; }
    .stat-c1::before { background: #3b82f6; }
    .stat-c2::before { background: #059669; }
    .stat-c3::before { background: #0891b2; }
    .stat-c4::before { background: #ca8a04; }
    .stat-c5::before { background: #dc2626; }
    .stat-c6::before { background: #ea580c; }
    .stat-c7::before { background: #7c3aed; }
    .stat-c8::before { background: #b91c1c; }

    .stat-header { display: flex; justify-content: space-between; align-items: center; z-index: 2; }
    .stat-title { font-size: 0.75rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-icon {
        width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: white; flex-shrink: 0;
    }
    .ic1 { background: linear-gradient(135deg, #60a5fa, #2563eb); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
    .ic2 { background: linear-gradient(135deg, #34d399, #059669); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3); }
    .ic3 { background: linear-gradient(135deg, #67e8f9, #0891b2); box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3); }
    .ic4 { background: linear-gradient(135deg, #fde047, #ca8a04); box-shadow: 0 4px 12px rgba(202, 138, 4, 0.3); }
    .ic5 { background: linear-gradient(135deg, #f87171, #dc2626); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); }
    .ic6 { background: linear-gradient(135deg, #fdba74, #ea580c); box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3); }
    .ic7 { background: linear-gradient(135deg, #a78bfa, #7c3aed); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3); }
    .ic8 { background: linear-gradient(135deg, #fca5a5, #b91c1c); box-shadow: 0 4px 12px rgba(185, 28, 28, 0.3); }

    .stat-value { font-size: 1.1rem; font-weight: 800; color: var(--text-slate-900); z-index: 2; line-height: 1.2; margin-top: 14px; letter-spacing: -0.02em; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero" style="padding-bottom: 7rem;">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Elektrik Abone Raporları</h1>
                <p class="hero-subtitle">Tüm raporlara hızlı erişim için aşağıdaki kartlardan birini seçin</p>
            </div>
        </div>
    </div>

    <div class="dashboard-container" style="margin-top: -3.5rem;">
        <div class="stats-grid">
            <a href="{{ route('reports.periodical') }}" class="stat-card stat-c1">
                <div class="stat-header">
                    <div class="stat-title">Dönemsel Tüketim</div>
                    <div class="stat-icon ic1"><i class="fas fa-calendar-alt"></i></div>
                </div>
                <div class="stat-value">Dönem Bazında Rapor</div>
            </a>

            <a href="{{ route('reports.yearly') }}" class="stat-card stat-c2">
                <div class="stat-header">
                    <div class="stat-title">Yıllık Özet</div>
                    <div class="stat-icon ic2"><i class="fas fa-calendar"></i></div>
                </div>
                <div class="stat-value">Yıl Bazında Rapor</div>
            </a>

            <a href="{{ route('reports.detailed') }}" class="stat-card stat-c3">
                <div class="stat-header">
                    <div class="stat-title">Fatura Detayı</div>
                    <div class="stat-icon ic3"><i class="fas fa-file-invoice"></i></div>
                </div>
                <div class="stat-value">Detaylı Fatura Analizi</div>
            </a>

            <a href="{{ route('reports.tuketim') }}" class="stat-card stat-c4">
                <div class="stat-header">
                    <div class="stat-title">Pivot Analiz</div>
                    <div class="stat-icon ic4"><i class="fas fa-chart-pie"></i></div>
                </div>
                <div class="stat-value">Tüketim Dönem Raporu</div>
            </a>

            <a href="{{ route('reports.koy-merkez') }}" class="stat-card stat-c5">
                <div class="stat-header">
                    <div class="stat-title">Karşılaştırma</div>
                    <div class="stat-icon ic5"><i class="fas fa-map-marked-alt"></i></div>
                </div>
                <div class="stat-value">Köy / Merkez Raporu</div>
            </a>

            <a href="{{ route('reports.ek-tuketim') }}" class="stat-card stat-c6">
                <div class="stat-header">
                    <div class="stat-title">İlave Tüketim</div>
                    <div class="stat-icon ic6"><i class="fas fa-plus-circle"></i></div>
                </div>
                <div class="stat-value">Ek Tüketim Raporu</div>
            </a>

            <a href="{{ route('reports.endeks') }}" class="stat-card stat-c7">
                <div class="stat-header">
                    <div class="stat-title">Endeks Anomali</div>
                    <div class="stat-icon ic7"><i class="fas fa-search"></i></div>
                </div>
                <div class="stat-value">Analiz ve Denetim</div>
            </a>

            <a href="{{ route('reports.anormal-faturalar') }}" class="stat-card stat-c8">
                <div class="stat-header">
                    <div class="stat-title">Olağan Dışı</div>
                    <div class="stat-icon ic8"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
                <div class="stat-value">Anormal Faturalar</div>
            </a>
        </div>
    </div>
</div>
@endsection
