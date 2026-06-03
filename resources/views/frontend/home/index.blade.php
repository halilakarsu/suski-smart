@extends('frontend.layouts.app')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Poppins', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --bg-main: #f4f6f9;
        --card-bg: rgba(255, 255, 255, 0.95);
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }

    .dash {
        background-color: var(--bg-main) !important;
        min-height: 100vh;
        padding-bottom: 4rem;
    }

    .dashboard-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 4rem 2rem 10rem 2rem; margin-top: -20px;
        color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .dashboard-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, transparent 70%);
        top: -200px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none; z-index: 1;
    }

    .dashboard-hero::after {
        content: ''; position: absolute; width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
        bottom: -100px; right: 5%; border-radius: 50%; opacity: 0.6; filter: blur(50px);
        animation: pulseDelay 10s infinite alternate; pointer-events: none; z-index: 1;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(1.2); opacity: 0.9; } }
    @keyframes pulseDelay { 0% { transform: scale(1) translate(0, 0); } 100% { transform: scale(1.3) translate(-20px, -20px); } }

    .hero-content {
        position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;
    }

    .hero-title {
        font-family: var(--font-primary); font-size: clamp(1.5rem, 3vw, 2.4rem); font-weight: 500; letter-spacing: -0.04em;
        margin-bottom: 0.5rem; background: linear-gradient(to right, #ffffff, #93c5fd);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    .dashboard-container {
        width: 100%; max-width: 1500px; margin: -6rem auto 0 auto;
        padding: 0 2rem; position: relative; z-index: 20;
    }

    .stats-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 40px;
    }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface-glass); backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 20px;
        padding: 20px; box-shadow: var(--shadow-elevated);
        position: relative; overflow: hidden; transition: all 0.4s;
        display: flex !important; flex-direction: column; text-decoration: none !important;
        min-height: 130px;
    }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.12); border-color: white; }
    .stat-card::before {
        content: ''; position: absolute; right: -15%; top: -15%; width: 100px; height: 100px;
        border-radius: 50%; opacity: 0.12; filter: blur(20px); transition: all 0.5s;
    }
    .stat-c1::before { background: #3b82f6; }
    .stat-c2::before { background: #7c3aed; }
    .stat-c3::before { background: #10b981; }


    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; z-index: 2; }
    .stat-title { font-size: 0.75rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-icon {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .ic1 { background: linear-gradient(135deg, #60a5fa, #2563eb); }
    .ic2 { background: linear-gradient(135deg, #a78bfa, #7c3aed); }
    .ic3 { background: linear-gradient(135deg, #34d399, #10b981); }


    .stat-value { font-size: 1.9rem; font-weight: 800; color: var(--text-slate-900); z-index: 2; margin-top: auto; line-height: 1; }
    .stat-desc { font-size: 0.75rem; color: var(--text-slate-500); font-weight: 600; margin-top: 4px; z-index: 2; }

    .widgets-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    @media (max-width: 1024px) { .widgets-grid { grid-template-columns: 1fr; } }

    .glass-card {
        background: var(--card-bg); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); border: 1px solid rgba(226, 232, 240, 0.6); margin-bottom: 24px;
    }

    .section-title { font-size: 0.9rem; font-weight: 800; color: var(--text-slate-900); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .section-title i { color: #3b82f6; }

    .wlink {
        font-size: 0.72rem; font-weight: 600; color: #3b82f6; text-decoration: none;
        background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.18);
        padding: 4px 11px; border-radius: 20px; transition: all 0.18s;
    }
    .wlink:hover { background: #2563eb; color: #fff; border-color: #2563eb; text-decoration: none; }

    .rtable { width: 100%; border-collapse: collapse; }
    .rtable th {
        font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em;
        color: #94a3b8; padding: 0.85rem 1.4rem; border-bottom: 1px solid #f1f5f9;
        background: #f8fafc; text-align: left;
    }
    .rtable th:not(:first-child) { text-align: right; }
    .rtable td {
        padding: 0.85rem 1.4rem; border-bottom: 1px solid #f1f5f9;
        font-size: 0.83rem; font-weight: 500; color: #475569;
    }
    .rtable td:not(:first-child) { text-align: right; }
    .rtable tbody tr:last-child td { border-bottom: none; }
    .rtable tbody tr:hover td { background: #f8fafc; }

    .rdot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .rname { display: flex; align-items: center; gap: 0.65rem; font-weight: 600; color: #0f172a; }
    .rtag {
        font-size: 0.72rem; font-weight: 700; color: #2563eb;
        background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.16);
        padding: 3px 9px; border-radius: 20px; display: inline-block;
    }

    .pbar-wrap { display: flex; align-items: center; gap: 0.75rem; }
    .pbar-bg { flex: 1; height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
    .pbar-fill { height: 100%; border-radius: 3px; transition: width 0.6s ease; }

    .empty-box { text-align: center; padding: 2.5rem 1rem; color: #94a3b8; }
    .empty-box i { font-size: 2.5rem; opacity: 0.35; margin-bottom: 0.75rem; display: block; }
    .empty-box p { font-size: 0.82rem; font-weight: 500; margin: 0; }

    * { box-sizing: border-box; }
    img, canvas { max-width: 100%; }
</style>

<div class="dash p-0">

  <div class="dashboard-hero">
    <div class="hero-content">
      <div>
        <h1 class="hero-title">SMART ŞUSKİ PROJESİ</h1>
        <p style="color: rgba(255,255,255,0.65); font-size: 1.1rem; font-weight: 400; letter-spacing: 0.03em; backdrop-filter: blur(4px); display: inline-block; padding: 0.4rem 1.2rem; border-radius: 100px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);"><i class="fas fa-bolt" style="margin-right: 8px; color: #fbbf24;"></i> Elektrik Faturaları · Yönetim ve Takip Sistemi</p>
      </div>
    </div>
  </div>

  <div class="dashboard-container">

    <div class="stats-grid">
      <div class="stat-card stat-c1">
        <div class="stat-header">
          <div class="stat-title">Toplam Abone</div>
          <div class="stat-icon ic1"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-value">{{ number_format($abone['toplam'] ?? 0) }}</div>
        <div class="stat-desc">Sisteme kayıtlı tüm aboneler</div>
      </div>

      <div class="stat-card stat-c2">
        <div class="stat-header">
          <div class="stat-title">Aktif Bölge</div>
          <div class="stat-icon ic2"><i class="fas fa-map-marked-alt"></i></div>
        </div>
        <div class="stat-value">{{ number_format($abone['bolge'] ?? 0) }}</div>
        <div class="stat-desc">İstatistik yapılan bölge sayısı</div>
      </div>

      <div class="stat-card stat-c3">
        <div class="stat-header">
          <div class="stat-title">Ödenen Faturalar</div>
          <div class="stat-icon ic3"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="stat-value">{{ number_format($odenen) }}</div>
        <div class="stat-desc">Kesinleşmiş ödeme kayıtları</div>
      </div>

    </div>

    <div class="glass-card">
      <div class="section-title"><i class="fas fa-map-marker-alt"></i> Yerleşim Yeri Abone Dağılımı</div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border-radius: 20px; padding: 20px; border: 1px solid rgba(59,130,246,0.15);">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #60a5fa, #2563eb); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; box-shadow: 0 4px 8px rgba(37,99,235,0.2);">
              <i class="fas fa-building"></i>
            </div>
            <div>
              <div style="font-size: 0.85rem; font-weight: 800; color: #1e3a5f;">MERKEZ</div>
              <div style="font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">{{ number_format($merkezDagilim->sum('adet')) }} Abone</div>
            </div>
          </div>
          <table style="width:100%; border-collapse: collapse;">
            <thead>
              <tr>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:left;">Bölge</th>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:right;">Abone</th>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:right;">%</th>
              </tr>
            </thead>
            <tbody>
              @php $totalMerkez = $merkezDagilim->sum('adet'); $mColors = ['#2563eb','#7c3aed','#0891b2','#059669','#d97706','#dc2626','#db2777','#4f46e5']; @endphp
              @forelse($merkezDagilim as $i => $b)
              @php $pct = $totalMerkez > 0 ? round(($b->adet / $totalMerkez) * 100, 1) : 0; $c = $mColors[$i % count($mColors)]; @endphp
              <tr>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15);">
                  <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.78rem; font-weight:600; color:#0f172a;">
                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $c }}; flex-shrink:0;"></div>
                    {{ $b->BOLGE_ADI }}
                  </div>
                </td>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15); text-align:right; font-size:0.78rem; font-weight:700; color:#1e3a5f;">{{ number_format($b->adet) }}</td>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15); text-align:right; font-size:0.72rem; font-weight:600; color:#64748b;">%{{ $pct }}</td>
              </tr>
              @empty
              <tr><td colspan="3" style="padding:1.5rem; text-align:center; color:#94a3b8; font-size:0.8rem;">Merkez verisi bulunamadı</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div style="background: linear-gradient(135deg, #fefce8, #fef9c3); border-radius: 20px; padding: 20px; border: 1px solid rgba(234,179,8,0.15);">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #fbbf24, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; box-shadow: 0 4px 8px rgba(217,119,6,0.2);">
              <i class="fas fa-home"></i>
            </div>
            <div>
              <div style="font-size: 0.85rem; font-weight: 800; color: #78350f;">KÖY</div>
              <div style="font-size: 0.7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">{{ number_format($koyDagilim->sum('adet')) }} Abone</div>
            </div>
          </div>
          <table style="width:100%; border-collapse: collapse;">
            <thead>
              <tr>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:left;">Bölge</th>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:right;">Abone</th>
                <th style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.09em; color:#94a3b8; padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.3); text-align:right;">%</th>
              </tr>
            </thead>
            <tbody>
              @php $totalKoy = $koyDagilim->sum('adet'); $kColors = ['#d97706','#ca8a04','#65a30d','#0d9488','#1d4ed8','#7c3aed','#b91c1c','#be185d']; @endphp
              @forelse($koyDagilim as $i => $b)
              @php $pct = $totalKoy > 0 ? round(($b->adet / $totalKoy) * 100, 1) : 0; $c = $kColors[$i % count($kColors)]; @endphp
              <tr>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15);">
                  <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.78rem; font-weight:600; color:#0f172a;">
                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $c }}; flex-shrink:0;"></div>
                    {{ $b->BOLGE_ADI }}
                  </div>
                </td>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15); text-align:right; font-size:0.78rem; font-weight:700; color:#78350f;">{{ number_format($b->adet) }}</td>
                <td style="padding:0.6rem 0.8rem; border-bottom:1px solid rgba(148,163,184,0.15); text-align:right; font-size:0.72rem; font-weight:600; color:#64748b;">%{{ $pct }}</td>
              </tr>
              @empty
              <tr><td colspan="3" style="padding:1.5rem; text-align:center; color:#94a3b8; font-size:0.8rem;">Köy verisi bulunamadı</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
