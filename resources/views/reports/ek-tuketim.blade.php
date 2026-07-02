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
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
        top: -200px; left: -150px; border-radius: 50%; opacity: 0.6; filter: blur(60px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }

    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }
    .filter-card { position: relative; z-index: 1000 !important; overflow: visible !important; }

    .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-slate-900); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
    .section-title i { padding: 10px; background: #eff6ff; border-radius: 12px; color: #3b82f6; }

    .form-group-pro { margin-bottom: 20px; }
    .form-group-pro label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-slate-900); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.03em; }
    .form-control-pro {
        width: 100%; padding: 12px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        font-size: 0.95rem; color: var(--text-slate-900); font-weight: 500; transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    select.form-control-pro { -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg width="12" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L13 1" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'); background-repeat: no-repeat; background-position: right 16px center; background-size: 10px; padding-right: 40px; }

    .btn-pro {
        padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }

    .premium-actions-grid {
        display: flex; gap: 16px; align-items: center;
    }
    .btn-premium-action {
        position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        height: 52px; padding: 0 24px; border-radius: 14px; font-family: var(--font-primary); font-size: 1rem;
        font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; border: none;
        overflow: hidden; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1; color: #fff !important; text-decoration: none !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
    }
    .btn-premium-action i { font-size: 1.25rem; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-premium-action:hover { transform: translateY(-4px); }
    .btn-premium-action:hover i { transform: scale(1.15) rotate(-5deg); }
    .btn-premium-action::before {
        content: ''; position: absolute; inset: 0; z-index: -1;
        background: linear-gradient(to top, rgba(255,255,255,0.15), transparent);
        opacity: 0; transition: opacity 0.4s;
    }
    .btn-premium-action:hover::before { opacity: 1; }

    .btn-premium-filter { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid rgba(255,255,255,0.1); }
    .btn-premium-filter:hover { box-shadow: 0 15px 30px -5px rgba(30, 41, 59, 0.8); }
    .btn-premium-pdf { background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); border: 1px solid rgba(255,255,255,0.1); }
    .btn-premium-pdf:hover { box-shadow: 0 15px 30px -5px rgba(220, 38, 38, 0.6); }
    .btn-premium-excel { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); border: 1px solid rgba(255,255,255,0.1); }
    .btn-premium-excel:hover { box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.6); }

    .adv-active-dot { width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block; animation: pulse-dot 1.5s infinite; border: 2px solid #fff; position: absolute; top: -2px; right: -2px; }
    @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:.7;} }

    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px; }
    .stat-box {
        background: #fff; border-radius: 24px; padding: 24px; display: flex; align-items: center; gap: 18px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; transition: transform 0.3s;
    }
    .stat-box:hover { transform: translateY(-5px); }
    .stat-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .stat-icon.blue { background: #eff6ff; color: #2563eb; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-icon.purple { background: #f5f3ff; color: #7c3aed; }
    .stat-val { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2; letter-spacing: -0.02em; }
    .stat-lbl { font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

    .tbl-wrap { overflow-x: auto; border-radius: 20px; background: #fff; box-shadow: inset 0 0 0 1px #e2e8f0; margin-top: 10px; }
    .tbl { width: 100%; min-width: 900px; border-collapse: separate; border-spacing: 0; }
    .tbl th { background: #f8fafc; padding: 16px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 16px 20px; font-size: 0.9rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s; }
    .tbl tr:hover td { background: #f8fafc; }

    .badge-tesisat { font-weight: 700; color: #2563eb; font-family: monospace; }
    .badge-date { display: inline-block; padding: 4px 10px; background: #f1f5f9; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.78rem; }
    .badge-donem { display: inline-block; padding: 4px 10px; background: #eff6ff; color: #2563eb; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }

    .btn-detay {
        padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 0.78rem;
        background: #eff6ff; color: #2563eb; border: none; cursor: pointer;
        transition: all 0.2s; text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-detay:hover { background: #2563eb; color: #fff; }

    #yearlyExportOverlay {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        align-items:center; justify-content:center; flex-direction:column;
    }
    #yearlyExportOverlay.active { display:flex; }
    .yearly-loader-box {
        background:rgba(255,255,255,0.97); border-radius:24px; padding:40px 50px;
        text-align:center; box-shadow:0 30px 80px rgba(0,0,0,0.25);
        animation:fadeScaleIn .35s ease; min-width:320px;
    }
    @keyframes fadeScaleIn { from{opacity:0;transform:scale(.92);} to{opacity:1;transform:scale(1);} }
    .yearly-spinner {
        width:64px; height:64px; border:5px solid #e2e8f0;
        border-top-color:#2563eb; border-radius:50%;
        animation:yearlySpin .85s linear infinite; margin:0 auto 20px;
    }
    @keyframes yearlySpin { to{transform:rotate(360deg);} }
    .yearly-loader-title { font-size:1.15rem; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .yearly-loader-sub   { font-size:.85rem; color:#64748b; font-weight:500; }
    .yearly-progress     { width:220px; height:6px; background:#e2e8f0; border-radius:99px; margin:16px auto 0; overflow:hidden; }
    .yearly-progress-bar {
        height:100%; width:0%;
        background:linear-gradient(90deg,#2563eb,#4f46e5); border-radius:99px;
        animation:yearlyProgressFill 30s ease-out forwards;
    }
    @keyframes yearlyProgressFill { 0%{width:0%;} 40%{width:55%;} 70%{width:78%;} 90%{width:90%;} 100%{width:95%;} }
    .yearly-overlay-close {
        margin-top:18px; padding:7px 20px; border-radius:10px; border:1px solid #e2e8f0;
        background:#f8fafc; color:#64748b; font-weight:700; font-size:.83rem;
        cursor:pointer; display:none;
    }
    .yearly-overlay-close:hover { background:#f1f5f9; color:#0f172a; }
    .yearly-open-btn {
        display:none; margin-top:18px; padding:12px 28px; border-radius:13px;
        background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff;
        font-weight:800; font-size:.95rem; border:none; cursor:pointer;
        box-shadow:0 8px 20px -5px rgba(37,99,235,.35); transition:all .2s;
    }
    .yearly-open-btn:hover { transform:translateY(-2px); box-shadow:0 12px 24px -5px rgba(37,99,235,.45); }
    .yearly-success-icon {
        display:none; width:64px; height:64px; border-radius:50%;
        background:#dcfce7; color:#16a34a; font-size:1.8rem;
        align-items:center; justify-content:center; margin:0 auto 16px;
    }

    @media (max-width: 768px) {
        .page-hero { padding: 3rem 1rem 6rem 1rem; }
        .hero-container { flex-direction: column; align-items: flex-start; gap: 20px; }
        .main-container { padding: 0 1rem; margin-top: -4rem; }
        .glass-card { padding: 20px; }
    }

    /* ===== EK TÜKETİM DETAY MODAL ===== */
    #ektDetayModal {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        align-items:center; justify-content:center; padding:20px;
        opacity:0; transition:opacity .35s ease;
    }
    #ektDetayModal.active { opacity:1; }
    #ektDetayModal .ekt-card {
        background:rgba(255,255,255,0.97); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
        border:1px solid rgba(255,255,255,0.7); border-radius:32px; width:100%; max-width:860px;
        box-shadow:0 40px 100px rgba(0,0,0,0.25); overflow:hidden;
        transform:scale(.95) translateY(10px); transition:all .35s cubic-bezier(.22,1,.36,1); max-height:90vh; display:flex; flex-direction:column;
    }
    #ektDetayModal.active .ekt-card { transform:scale(1) translateY(0); }
    .ekt-header {
        background:linear-gradient(125deg,#0f172a 0%,#1e1b4b 100%);
        padding:28px 32px; display:flex; align-items:center; justify-content:space-between;
        border-bottom:1px solid rgba(255,255,255,0.08); flex-shrink:0;
    }
    .ekt-header-left { display:flex; align-items:center; gap:18px; }
    .ekt-header-icon {
        width:48px; height:48px; border-radius:16px;
        background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3);
        display:flex; align-items:center; justify-content:center; color:#60a5fa; font-size:1.2rem; flex-shrink:0;
    }
    .ekt-eyebrow { font-size:.75rem; font-weight:700; color:#60a5fa; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
    .ekt-title-row { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .ekt-fatura-badge { font-size:1rem; font-weight:800; color:#fff; letter-spacing:-.01em; }
    .ekt-sep { color:#475569; font-size:1.1rem; font-weight:300; }
    .ekt-donem-pill {
        display:inline-flex; align-items:center; padding:5px 14px; background:rgba(96,165,250,0.15);
        border:1px solid rgba(96,165,250,0.3); border-radius:20px; color:#93c5fd;
        font-size:.82rem; font-weight:700;
    }
    .ekt-header-right { display:flex; align-items:center; gap:14px; }
    .ekt-close-btn {
        width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.08);
        border:1px solid rgba(255,255,255,0.12); color:#94a3b8; cursor:pointer;
        display:flex; align-items:center; justify-content:center; transition:all .2s; font-size:1rem;
    }
    .ekt-close-btn:hover { background:rgba(255,255,255,0.15); color:#fff; }

    .ekt-body { padding:24px 32px; overflow-y:auto; flex:1; }
    .ekt-section-title {
        font-size:.85rem; font-weight:800; color:#0f172a; margin-bottom:16px;
        display:flex; align-items:center; gap:10px; letter-spacing:-.01em;
    }
    .ekt-section-title i { padding:8px; background:#eff6ff; border-radius:10px; color:#3b82f6; font-size:.8rem; }

    .ekt-tbl-wrap { overflow-x:auto; border-radius:16px; background:#fff; box-shadow:inset 0 0 0 1px #e2e8f0; margin-top:6px; }
    .ekt-tbl { width:100%; min-width:700px; border-collapse:separate; border-spacing:0; }
    .ekt-tbl th { background:#f8fafc; padding:12px 16px; font-size:.72rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e2e8f0; }
    .ekt-tbl td { padding:12px 16px; font-size:.85rem; color:#1e293b; border-bottom:1px solid #f1f5f9; background:#fff; }
    .ekt-tbl tr:hover td { background:#f8fafc; }

    .ekt-loading { text-align:center; padding:60px 20px; }
    .ekt-spinner {
        width:48px; height:48px; border:4px solid #e2e8f0; border-top-color:#2563eb;
        border-radius:50%; animation:ektSpin .8s linear infinite; margin:0 auto 16px;
    }
    @keyframes ektSpin { to { transform:rotate(360deg); } }
    #ektModalBodyContent { min-height:200px; }
    .ekt-empty { text-align:center; padding:60px 20px; color:#94a3b8; }
    .ekt-empty i { font-size:2.5rem; display:block; margin-bottom:12px; color:#cbd5e1; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero" style="padding-bottom: 7rem;">
        <div class="hero-container" style="flex-wrap: wrap; gap: 20px;">
            <div class="hero-title-group" style="flex-grow: 1;">
                <h1 class="hero-title">Ek Tüketim Raporu</h1>
                <p class="hero-subtitle">Ek tüketim kalemine sahip faturaların detaylı listesi</p>
                @php
                    $hasDonemFilter = request()->anyFilled(['start_period', 'end_period']);
                @endphp
                @if($hasDonemFilter)
                <div style="margin-top:12px; padding:6px 14px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:12px; display:inline-flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <i class="fas fa-calendar-alt" style="color:#60a5fa; font-size:0.9rem;"></i>
                    <span style="font-size:.8rem;font-weight:700;color:#e2e8f0;">Dönem:</span>
                    <span style="background:rgba(59,130,246,0.15); color:#93c5fd; padding:2px 8px; font-size:0.7rem; border-radius:4px;">
                        {{ request('start_period', '...') }}{{ request('end_period') ? ' - '.request('end_period') : '' }}
                    </span>
                </div>
                @endif
            </div>

            <div id="ekTuketimFilterForm" data-action="{{ route('reports.ek-tuketim') }}" class="premium-actions-grid">
                <button type="button" class="btn-premium-action btn-premium-filter" data-toggle="modal" data-target="#ektFilterModal">
                    <i class="fas fa-sliders-h"></i> FİLTRELE
                    @if($hasDonemFilter)<span class="adv-active-dot"></span>@endif
                </button>

                <button type="button" id="ek-tuketim-export-pdf" class="btn-premium-action btn-premium-pdf">
                    <i class="fas fa-file-pdf"></i> PDF İndir
                </button>

                <button type="button" id="ek-tuketim-export-excel" class="btn-premium-action btn-premium-excel">
                    <i class="fas fa-file-excel"></i> Excel İndir
                </button>
            </div>
        </div>
    </div>

    <div class="main-container" style="margin-top: -3.5rem;">
        <div id="reportResultsContainer">
            @if($results->count() > 0)
                @include('reports.partials.ek_tuketim_table', compact('results', 'totalKWH', 'totalAmount', 'totalIlaveToplam', 'totalIlaveTutar'))
            @else
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#eff6ff;color:#3b82f6;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">📊</div>
                    <h4 style="font-weight:800;color:var(--text-slate-900);">Ek Tüketim Raporu</h4>
                    <p style="color:var(--text-slate-500);max-width:500px;margin:0 auto;">Ek tüketim kalemine sahip faturaları görüntülemek için dönem aralığı seçin.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══ FİLTRE MODALI ═══ --}}
<div class="modal fade" id="ektFilterModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;" role="document">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.25); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:30px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.35rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(96,165,250,0.2);border-radius:12px;margin-right:12px;color:#60a5fa;"><i class="fas fa-sliders-h"></i></div>
                        Dönem Filtreleme
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Görüntülemek istediğiniz dönem aralığını seçin.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:40px 35px;">
                <div class="row" style="background: rgba(241, 245, 249, 0.5); padding: 20px; border-radius: 16px;">
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-alt" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Başlangıç Dönemi
                        </label>
                        <select id="modal_start_period" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Seçiniz</option>
                            @foreach($donemler as $d)
                                <option value="{{ $d }}" {{ request('start_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-check" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bitiş Dönemi
                        </label>
                        <select id="modal_end_period" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Seçiniz</option>
                            @foreach($donemler as $d)
                                <option value="{{ $d }}" {{ request('end_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(248, 250, 252, 0.8); border-top: 1px solid rgba(226, 232, 240, 0.8); padding: 25px 35px; display:flex; justify-content:space-between; align-items: center; border-bottom-left-radius: 28px; border-bottom-right-radius: 28px;">
                <button type="button" class="btn-pro btn-outline-pro" id="ektClearBtn" style="border-radius: 12px; font-weight: 700; transition: all 0.2s;"><i class="fas fa-eraser"></i> Temizle</button>
                <button type="button" class="btn-pro btn-primary-pro" id="ektApplyBtn" style="border-radius: 12px; font-weight: 700; padding-left: 28px; padding-right: 28px; transition: all 0.2s;"><i class="fas fa-check"></i> Sonuçları Getir</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ EXPORT YÜKLENİYOR OVERLAY ═══ --}}
<div id="yearlyExportOverlay">
    <div class="yearly-loader-box">
        <div id="yearlySuccessIcon" class="yearly-success-icon"><i class="fas fa-check"></i></div>
        <div id="yearlySpinner" class="yearly-spinner"></div>
        <h4 id="yearlyLoaderTitle" class="yearly-loader-title">Rapor Hazırlanıyor</h4>
        <p id="yearlyLoaderSub" class="yearly-loader-sub">Veriler derleniyor, lütfen bekleyin...</p>
        <div id="yearlyProgressWrap" class="yearly-progress">
            <div id="yearlyProgressBar" class="yearly-progress-bar"></div>
        </div>
        <button type="button" id="yearlyOpenBtn" class="yearly-open-btn"><i class="fas fa-download"></i> Dosyayı İndir</button>
        <button type="button" id="yearlyOverlayClose" class="yearly-overlay-close">Kapat</button>
    </div>
</div>

{{-- ═══ Ek Tüketim Detay Modal ═══ --}}
<div id="ektDetayModal">
    <div class="ekt-card">
        <div class="ekt-header">
            <div class="ekt-header-left">
                <div class="ekt-header-icon"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <div class="ekt-eyebrow">Ek Tüketim Geçmişi</div>
                    <div class="ekt-title-row">
                        <span id="ekt-tesisat" class="ekt-fatura-badge">—</span>
                        <span class="ekt-sep">|</span>
                        <span class="ekt-donem-pill">Son 1 Yıl</span>
                    </div>
                </div>
            </div>
            <div class="ekt-header-right">
                <button onclick="closeEktDetay()" class="ekt-close-btn"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="ekt-body">
            <div class="ekt-section-title"><i class="fas fa-table"></i> DÖNEM BAZINDA EK TÜKETİM RAPORU</div>
            <div id="ektModalBodyContent">
                <div class="ekt-loading">
                    <div class="ekt-spinner"></div>
                    <div style="font-weight:700;color:#0f172a;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let lastDownloadUrl = '';
let lastExportType = '';

function showOverlay(type) {
    $('#yearlyExportOverlay').addClass('active');
    const $bar = $('#yearlyProgressBar');
    $bar.removeClass('yearly-progress-bar'); void $bar[0].offsetWidth; $bar.addClass('yearly-progress-bar');
    $('#yearlySuccessIcon').hide();
    $('#yearlySpinner').show();
    $('#yearlyProgressWrap').show();
    $('#yearlyOpenBtn').hide();
    $('#yearlyOverlayClose').hide();
    setTimeout(() => { $('#yearlyOverlayClose').show(); }, 10000);
}
function hideOverlay() {
    $('#yearlyExportOverlay').removeClass('active');
    if (lastDownloadUrl) { URL.revokeObjectURL(lastDownloadUrl); lastDownloadUrl = ''; }
}

function buildFormData() {
    const params = new URLSearchParams();
    const sp = $('#modal_start_period').val();
    const ep = $('#modal_end_period').val();
    if (sp) params.set('start_period', sp);
    if (ep) params.set('end_period', ep);
    return params;
}

async function handleExport(type) {
    lastExportType = type;
    const params = buildFormData();
    params.append('export', type);
    const form = document.getElementById('ekTuketimFilterForm');
    const actionUrl = form ? form.getAttribute('data-action') : "{{ route('reports.ek-tuketim') }}";
    const url = `${actionUrl}?${params.toString()}`;

    if (type === 'pdf') {
        window.open(url, '_blank');
        return;
    }

    lastDownloadUrl = '';
    showOverlay(type);
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error('Rapor oluşturulamadı');
        const blob = await response.blob();
        lastDownloadUrl = window.URL.createObjectURL(blob);
        $('#yearlySpinner').hide();
        $('#yearlyProgressWrap').hide();
        $('#yearlySuccessIcon').css('display', 'flex');
        $('#yearlyLoaderTitle').text('İndirme Hazır!');
        $('#yearlyLoaderSub').text('Excel dosyanız başarıyla oluşturuldu.');
        $('#yearlyOpenBtn').html('<i class="fas fa-file-excel"></i> Excel Dosyasını İndir').show();
        $('#yearlyOverlayClose').show();
    } catch (e) {
        $('#yearlyLoaderTitle').text('Hata Oluştu');
        $('#yearlyLoaderSub').text('Rapor oluşturulurken bir hata meydana geldi.');
        $('#yearlySpinner').hide();
        $('#yearlyProgressWrap').hide();
        $('#yearlyOverlayClose').show();
    }
}

window.closeEktDetay = function() {
    var modal = document.getElementById('ektDetayModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    setTimeout(function() { modal.style.display = 'none'; }, 280);
};

function openEktDetay(tesisatNo) {
    var modal = document.getElementById('ektDetayModal');
    document.getElementById('ekt-tesisat').textContent = 'Tesisat No: ' + tesisatNo;
    document.getElementById('ektModalBodyContent').innerHTML =
        '<div class="ekt-loading"><div class="ekt-spinner"></div><div style="font-weight:700;color:#0f172a;">Yükleniyor...</div></div>';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(function() { modal.classList.add('active'); }, 10);

    $.ajax({
        url: '{{ url("/raporlar/ek-tuketim/son-1-yil") }}/' + tesisatNo,
        type: 'GET',
        success: function(res) {
            if (!res.success || !res.records.length) {
                document.getElementById('ektModalBodyContent').innerHTML =
                    '<div class="ekt-empty"><i class="fas fa-inbox"></i><div style="font-weight:700;color:#64748b;">Son 1 yılda fatura bulunamadı.</div></div>';
                return;
            }
            var html = '<div class="ekt-tbl-wrap"><table class="ekt-tbl"><thead><tr>' +
                '<th>Dönem</th><th style="text-align:center;">İlk Okuma</th><th style="text-align:center;">Son Okuma</th><th style="text-align:right;">T1 (kWh)</th><th style="text-align:right;">T2 (kWh)</th><th style="text-align:right;">T3 (kWh)</th><th style="text-align:right;">Toplam (kWh)</th><th style="text-align:right;">T1 İlave (kWh)</th><th style="text-align:right;">T2 İlave (kWh)</th><th style="text-align:right;">T3 İlave (kWh)</th><th style="text-align:right;">Top. İlave (kWh)</th>' +
                '</tr></thead><tbody>';
            var total1 = 0, total2 = 0, total3 = 0, totalToplam = 0;
            var totalT1I = 0, totalT2I = 0, totalT3I = 0, totalIlaveToplam = 0;
            res.records.forEach(function(r) {
                total1 += r.t1; total2 += r.t2; total3 += r.t3;
                totalToplam += r.toplam_tuketim;
                totalT1I += r.t1_ilave; totalT2I += r.t2_ilave; totalT3I += r.t3_ilave;
                totalIlaveToplam = totalT1I + totalT2I + totalT3I;
                html += '<tr>' +
                    '<td><span style="display:inline-block;padding:3px 10px;background:#eff6ff;color:#2563eb;border-radius:8px;font-weight:700;font-size:.78rem;">' + r.donem + '</span></td>' +
                    '<td style="text-align:center;font-weight:600;">' + (r.ilk_okuma || '—') + '</td>' +
                    '<td style="text-align:center;font-weight:600;">' + (r.son_okuma || '—') + '</td>' +
                    '<td style="text-align:right;font-weight:600;">' + r.t1.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:600;">' + r.t2.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:600;">' + r.t3.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:700;">' + r.toplam_tuketim.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:600;color:#7c3aed;">' + r.t1_ilave.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:600;color:#7c3aed;">' + r.t2_ilave.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:600;color:#7c3aed;">' + r.t3_ilave.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '<td style="text-align:right;font-weight:700;color:#0f172a;">' + (r.t1_ilave + r.t2_ilave + r.t3_ilave).toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                    '</tr>';
            });
            html += '</tbody><tfoot><tr style="background:#f1f5f9;font-weight:800;">' +
                '<td style="font-size:.85rem;letter-spacing:.03em;">GENEL TOPLAM</td>' +
                '<td style="text-align:center;">—</td>' +
                '<td style="text-align:center;">—</td>' +
                '<td style="text-align:right;">' + total1.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + total2.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + total3.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + totalToplam.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + totalT1I.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + totalT2I.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;">' + totalT3I.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '<td style="text-align:right;color:#0f172a;">' + totalIlaveToplam.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td>' +
                '</tr></tfoot></table></div>';
            document.getElementById('ektModalBodyContent').innerHTML = html;
        },
        error: function() {
            document.getElementById('ektModalBodyContent').innerHTML =
                '<div class="ekt-empty"><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i><div style="font-weight:700;color:#dc2626;">Veri alınırken hata oluştu.</div></div>';
        }
    });
}

$(document).ready(function() {
    function autoSwapPeriods() {
        var start = $('#modal_start_period').val();
        var end   = $('#modal_end_period').val();
        if (start && end && start > end) {
            $('#modal_start_period').val(end);
            $('#modal_end_period').val(start);
            $('#modal_start_period, #modal_end_period').css({'border-color':'#f59e0b','transition':'border-color 0s'});
            setTimeout(function(){ $('#modal_start_period, #modal_end_period').css({'border-color':'','transition':'border-color 0.4s'}); }, 700);
        }
    }
    $('#modal_start_period').on('change', autoSwapPeriods);
    $('#modal_end_period').on('change', autoSwapPeriods);

    $(document).on('click', '#reportResultsContainer .pagination a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var $container = $('#reportResultsContainer');
        $container.css('opacity', '0.5');
        $.ajax({
            url: href,
            success: function(html) {
                $container.html(html).css('opacity', '1');
                $('html, body').animate({ scrollTop: $container.offset().top - 100 }, 500);
            }
        });
    });

    $(document).on('click', '.ek-tuketim-detay-btn', function() {
        var tesisat = $(this).data('tesisat');
        if (tesisat) openEktDetay(tesisat);
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') closeEktDetay();
    });

    $('#ek-tuketim-export-pdf').click(() => handleExport('pdf'));
    $('#ek-tuketim-export-excel').click(() => handleExport('excel'));

    $('#yearlyOpenBtn').click(function() {
        if (lastExportType === 'pdf') {
            window.open(lastDownloadUrl, '_blank');
        } else {
            const a = document.createElement('a');
            a.href = lastDownloadUrl;
            a.download = 'EkTuketim_Raporu.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        hideOverlay();
    });

    $('#yearlyOverlayClose').click(hideOverlay);

    $('#ektClearBtn').click(function() {
        $('#modal_start_period').val('');
        $('#modal_end_period').val('');
    });

    $('#ektApplyBtn').on('click', function() {
        var hasDonem = !!$('#modal_start_period').val() || !!$('#modal_end_period').val();
        if (!hasDonem) {
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen dönem aralığı seçiniz.', confirmButtonText: 'Tamam'});
            return;
        }
        const params = buildFormData();
        const actionUrl = $('#ekTuketimFilterForm').attr('data-action') || "{{ route('reports.ek-tuketim') }}";
        window.location.href = actionUrl + "?" + params.toString();
    });
});
</script>
@endpush
