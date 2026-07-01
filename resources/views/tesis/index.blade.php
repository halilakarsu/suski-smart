@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }
    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
        top: -200px; left: -150px; border-radius: 50%; opacity: 0.6; filter: blur(60px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04); margin-bottom: 30px;
    }
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: rgba(255,255,255,0.9); border-radius: 20px; padding: 24px; box-shadow: 0 8px 24px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); display: flex; align-items: center; gap: 16px; }
    .stat-icon { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff; flex-shrink: 0; }
    .stat-info h4 { font-size: 0.85rem; color: #64748b; font-weight: 600; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-info .value { font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .chart-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .chart-grid { grid-template-columns: 1fr; } }
    .table-mini { width: 100%; border-collapse: collapse; }
    .table-mini td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
    .table-mini tr:last-child td { border-bottom: none; }
    .bar-bg { height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #8b5cf6, #6d28d9); }
    .quick-link { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 14px; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; }
    .quick-link:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
</style>
<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Tesis Bilgi Sistemi</h1>
                <p class="hero-subtitle">Tesis, arıza kaydı ve araç yönetim paneli</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);"><i class="fas fa-building"></i></div>
                <div class="stat-info">
                    <h4>Toplam Tesis</h4>
                    <div class="value">{{ $tesisSayisi }}</div>
                    <small style="color:#64748b;">{{ $aktifTesis }} aktif, {{ $pasifTesis }} pasif</small>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><i class="fas fa-tools"></i></div>
                <div class="stat-info">
                    <h4>Arıza Kaydı</h4>
                    <div class="value">{{ number_format($arizaSayisi) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-truck"></i></div>
                <div class="stat-info">
                    <h4>Araç</h4>
                    <div class="value">{{ $aracSayisi }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);"><i class="fas fa-list"></i></div>
                <div class="stat-info">
                    <h4>Hızlı Erişim</h4>
                    <div style="display:flex;gap:8px;margin-top:4px;flex-wrap:wrap;">
                        <a href="{{ route('tesis-bilgi-sistemi.tesisler') }}" class="quick-link" style="background:#f3e8ff;color:#6d28d9;">Tesisler</a>
                        <a href="{{ route('tesis-bilgi-sistemi.arizalar') }}" class="quick-link" style="background:#fef3c7;color:#d97706;">Arızalar</a>
                        <a href="{{ route('tesis-bilgi-sistemi.araclar') }}" class="quick-link" style="background:#d1fae5;color:#059669;">Araçlar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="glass-card">
                <h5 style="font-weight:700;margin-bottom:16px;color:#0f172a;"><i class="fas fa-chart-pie" style="color:#8b5cf6;"></i> En Çok Arıza Türleri</h5>
                <table class="table-mini">
                    @foreach($arizaTurleri as $a)
                    <tr>
                        <td style="font-weight:600;">{{ $a->ariza_turu }}</td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($a->toplam) }}</td>
                        <td style="width:40%;">
                            <div class="bar-bg">
                                <div class="bar-fill" style="width: {{ $arizaTurleri->max('toplam') > 0 ? ($a->toplam / $arizaTurleri->max('toplam') * 100) : 0 }}%;"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="glass-card">
                <h5 style="font-weight:700;margin-bottom:16px;color:#0f172a;"><i class="fas fa-map-marker-alt" style="color:#f59e0b;"></i> İlçelere Göre Arıza Dağılımı</h5>
                <table class="table-mini">
                    @foreach($ilceAriza as $i)
                    <tr>
                        <td style="font-weight:600;">{{ $i->ilce }}</td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($i->toplam) }}</td>
                        <td style="width:40%;">
                            <div class="bar-bg">
                                <div class="bar-fill" style="width: {{ $ilceAriza->max('toplam') > 0 ? ($i->toplam / $ilceAriza->max('toplam') * 100) : 0 }}%; background:linear-gradient(90deg,#f59e0b,#d97706);"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
