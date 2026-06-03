@extends('frontend.layouts.app')
@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Reporting */
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

    /* Hero Section */
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

    /* Main Content */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px; overflow: visible;
    }
    .filter-card { position: relative; z-index: 1000 !important; overflow: visible !important; }

    .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-slate-900); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
    .section-title i { padding: 10px; background: #eff6ff; border-radius: 12px; color: #3b82f6; }

    /* Form Elements */
    .form-group-pro { margin-bottom: 20px; }
    .form-group-pro label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-slate-900); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.03em; }
    .form-control-pro {
        width: 100%; padding: 12px 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        font-size: 0.95rem; color: var(--text-slate-900); font-weight: 500; transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    select.form-control-pro { -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg width="12" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L13 1" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'); background-repeat: no-repeat; background-position: right 16px center; background-size: 10px; padding-right: 40px; }

    /* Buttons */
    .btn-pro {
        padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }
    .btn-success-pro { background: linear-gradient(135deg, #059669, #10b981); color: white !important; }

    /* Multi-select */
    .custom-multi-select { position: relative; width: 100%; z-index: 1000; }
    .custom-multi-select .dropdown-toggle {
        text-align: left; background: #fff; border: 1.5px solid #e2e8f0;
        padding: 12px 16px; border-radius: 12px; font-size: .92rem; color: var(--text-slate-900);
        display: flex; justify-content: space-between; align-items: center; width: 100%;
        transition: all 0.2s; font-weight: 500;
    }
    .custom-multi-select .dropdown-toggle span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: calc(100% - 20px);
    }
    .custom-multi-select .dropdown-toggle::after { display: none !important; }
    .custom-multi-select .dropdown-menu {
        width: 100%; border-radius: 16px; border: 1.5px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15); padding: 10px; max-height: 260px; overflow-y: auto; margin-top: 6px;
        background: #fff; z-index: 99999 !important; position: absolute !important;
    }
    .custom-multi-select .form-check { padding: 7px 10px; margin-bottom: 1px; border-radius: 9px; transition: background 0.15s; display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .custom-multi-select .form-check:hover { background: #eff6ff; }
    .custom-multi-select .form-check.checked-row { background: #eff6ff; }
    .custom-multi-select .form-check-input { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .custom-multi-select .cb-box { width: 20px; height: 20px; min-width: 20px; border-radius: 5px; border: 2px solid #cbd5e1; background: #fff; display: flex; align-items: center; justify-content: center; transition: all 0.18s; flex-shrink: 0; }
    .custom-multi-select .cb-box svg { width: 11px; height: 11px; stroke: #fff; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; fill: none; opacity: 0; transition: opacity 0.15s; }
    .custom-multi-select .form-check-input:checked ~ .cb-box { background: #2563eb; border-color: #2563eb; }
    .custom-multi-select .form-check-input:checked ~ .cb-box svg { opacity: 1; }
    .custom-multi-select .select-all-wrap { border-bottom: 1.5px solid #e2e8f0; margin-bottom: 6px; padding-bottom: 6px; }

    /* ===== ADVANCED MODAL BUTTON ===== */
    .btn-advanced-pro { position:relative; padding:12px 18px; border-radius:14px; font-weight:700; font-size:.9rem; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:all .3s; border:1.5px solid #c7d2fe; cursor:pointer; background:linear-gradient(135deg,#eff6ff,#f5f3ff); color:#4f46e5; box-shadow:0 4px 12px rgba(79,70,229,.1); }
    .btn-advanced-pro:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(79,70,229,.2); border-color:#818cf8; }
    .adv-active-dot { width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block; animation:pulse-dot 1.5s infinite; }
    @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:.7;} }
    .adv-badge { display:inline-flex; align-items:center; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:20px; font-size:.75rem; font-weight:700; padding:3px 10px; }

    /* Stats Row Premium */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px; }
    .stat-box { 
        background: #fff; border-radius: 24px; padding: 24px; display: flex; align-items: center; gap: 18px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; transition: transform 0.3s;
    }
    .stat-box:hover { transform: translateY(-5px); }
    .stat-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
    .stat-icon.purple { background: #f5f3ff; color: #7c3aed; }
    .stat-icon.blue { background: #eff6ff; color: #2563eb; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-val { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2; letter-spacing: -0.02em; }
    .stat-lbl { font-size: 0.8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

    /* Stats & Table */
    .tbl-wrap { overflow-x: auto; border-radius: 20px; background: #fff; box-shadow: inset 0 0 0 1px #e2e8f0; margin-top: 10px; }
    .tbl { width: 100%; min-width: 1100px; border-collapse: separate; border-spacing: 0; }
    .tbl th { background: #f8fafc; padding: 16px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 16px 20px; font-size: 0.9rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s; }
    .tbl tr:hover td { background: #f8fafc; }

    /* ===== EXPORT LOADING OVERLAY ===== */
    #anExportOverlay {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        align-items:center; justify-content:center; flex-direction:column;
    }
    #anExportOverlay.active { display:flex; }
    .an-loader-box {
        background:rgba(255,255,255,0.97); border-radius:24px; padding:40px 50px;
        text-align:center; box-shadow:0 30px 80px rgba(0,0,0,0.25);
        animation:fadeScaleIn .35s ease; min-width:320px;
    }
    @keyframes fadeScaleIn { from{opacity:0;transform:scale(.92);} to{opacity:1;transform:scale(1);} }
    .an-spinner {
        width:64px; height:64px; border:5px solid #e2e8f0;
        border-top-color:#2563eb; border-radius:50%;
        animation:anSpin .85s linear infinite; margin:0 auto 20px;
    }
    @keyframes anSpin { to{transform:rotate(360deg);} }
    .an-loader-title { font-size:1.15rem; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .an-loader-sub   { font-size:.85rem; color:#64748b; font-weight:500; }
    .an-progress     { width:220px; height:6px; background:#e2e8f0; border-radius:99px; margin:16px auto 0; overflow:hidden; }
    .an-progress-bar {
        height:100%; width:0%;
        background:linear-gradient(90deg,#2563eb,#4f46e5); border-radius:99px;
        animation:anProgressFill 30s ease-out forwards;
    }
    @keyframes anProgressFill { 0%{width:0%;} 40%{width:55%;} 70%{width:78%;} 90%{width:90%;} 100%{width:95%;} }
    .an-overlay-close {
        margin-top:18px; padding:7px 20px; border-radius:10px; border:1px solid #e2e8f0;
        background:#f8fafc; color:#64748b; font-weight:700; font-size:.83rem;
        cursor:pointer; display:none;
    }
    .an-overlay-close:hover { background:#f1f5f9; color:#0f172a; }
    .an-open-btn {
        display:none; margin-top:18px; padding:12px 28px; border-radius:13px;
        background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff;
        font-weight:800; font-size:.95rem; border:none; cursor:pointer;
        box-shadow:0 8px 20px -5px rgba(37,99,235,.35); transition:all .2s;
    }
    .an-open-btn:hover { transform:translateY(-2px); box-shadow:0 12px 24px -5px rgba(37,99,235,.45); }
    .an-success-icon {
        display:none; width:64px; height:64px; border-radius:50%;
        background:#dcfce7; color:#16a34a; font-size:1.8rem;
        align-items:center; justify-content:center; margin:0 auto 16px;
    }

    /* Results loading modal */
    .an-results-modal { padding: 8px 4px 2px; text-align: center; }
    .an-results-icon {
        width: 70px; height: 70px; border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 18px; color: #2563eb; font-size: 1.8rem;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        animation: anResultsPulse 1.7s ease-in-out infinite;
    }
    @keyframes anResultsPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37,99,235,.22); }
        50% { transform: scale(1.06); box-shadow: 0 0 0 14px rgba(37,99,235,0); }
    }
    .an-results-title { font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
    .an-results-sub { font-size: .86rem; color: #64748b; font-weight: 500; margin-bottom: 18px; }
    .an-results-progress { height: 7px; width: 100%; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
    .an-results-progress span {
        display: block; height: 100%; width: 45%;
        background: linear-gradient(90deg, #2563eb, #4f46e5, #10b981);
        border-radius: 999px;
        animation: anResultsProgress 1.2s ease-in-out infinite;
    }
    @keyframes anResultsProgress {
        0% { transform: translateX(-110%); }
        100% { transform: translateX(230%); }
    }
    .an-results-dots span {
        display: inline-block; width: 7px; height: 7px; margin: 16px 3px 0;
        border-radius: 50%; background: #2563eb;
        animation: anResultsDots 1.1s ease-in-out infinite;
    }
    .an-results-dots span:nth-child(2) { animation-delay: .16s; }
    .an-results-dots span:nth-child(3) { animation-delay: .32s; }
    @keyframes anResultsDots {
        0%, 80%, 100% { transform: scale(.7); opacity: .45; }
        40% { transform: scale(1.15); opacity: 1; }
    }

    /* Detail Modal (Anomaly) */
    #detMdl { position:fixed; inset:0; background:rgba(15,23,42,0.85); z-index:9999; display:none; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(10px); }
    .det-box { background:#fff; border-radius:32px; width:100%; max-width:1000px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 40px 80px rgba(0,0,0,0.35); overflow:hidden; }
    
    @media (max-width: 768px) {
        .page-hero { padding: 3rem 1rem 6rem 1rem; }
        .hero-container { flex-direction: column; align-items: flex-start; gap: 20px; }
        .main-container { padding: 0 1rem; margin-top: -4rem; }
        .glass-card { padding: 20px; }
        .modal-dialog { max-width: 95% !important; margin: 10px auto; }
    }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Anomali Tespit Raporu</h1>
                <p class="hero-subtitle">Dönem bazında normalin dışındaki tüketim ve teknik sapmaları aşağıda raporlayabilirsiniz.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn-advanced-pro" data-toggle="modal" data-target="#anAdvModal" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
                    <i class="fas fa-sliders-h"></i> Detaylı Filtre
                    @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','yerlesim_tipi','fatura_no']))<span class="adv-active-dot" style="background:#fca5a5;"></span>@endif
                </button>
                <div class="dropdown" id="anExportBtnContainer" style="display: none;">
                    <button type="button" class="btn-pro btn-outline-pro dropdown-toggle ml-2" data-toggle="dropdown" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
                        <i class="fas fa-file-export"></i> Dışa Aktar
                    </button>
                    <div class="dropdown-menu dropdown-menu-pro dropdown-menu-right" style="border-radius:12px;">
                        <button type="button" data-type="pdf" class="dropdown-item dropdown-item-pro btn-export-trigger"><i class="fas fa-file-pdf text-danger"></i> PDF Raporu Al</button>
                        <button type="button" data-type="excel" class="dropdown-item dropdown-item-pro btn-export-trigger"><i class="fas fa-file-excel text-success"></i> Excel Raporu Al</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        {{-- FİLTRE KARTI --}}
        <div class="glass-card filter-card">
            <h5 class="section-title"><i class="fas fa-filter"></i> Anomali Tespit Bilgileri</h5>

            @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','yerlesim_tipi']))
            <div style="margin-bottom:16px;padding:10px 16px;background:linear-gradient(135deg,rgba(37,99,235,.07),rgba(79,70,229,.07));border:1.5px solid rgba(37,99,235,.2);border-radius:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <i class="fas fa-sliders-h" style="color:#2563eb;"></i>
                <span style="font-size:.83rem;font-weight:700;color:#1d4ed8;">Aktif Filtreler:</span>
                @if(request('tarife')) <span class="adv-badge">Tarife: {{ count(request('tarife')) }} seçili</span> @endif
                @if(request('baglanti_grubu')) <span class="adv-badge">{{ request('baglanti_grubu') }}</span> @endif
                @if(request('yerlesim_tipi')) <span class="adv-badge">{{ ucfirst(request('yerlesim_tipi')) }}</span> @endif
                @if(request('tesisat_no')) <span class="adv-badge">Tesisat: {{ request('tesisat_no') }}</span> @endif
            </div>
            @endif

            <form action="{{ route('reports.anomali') }}" method="GET" id="anomaliFilterForm">
                <div id="anAdvHidden"></div>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="fas fa-map-marker-alt me-2"></i> Bölge Seçimi</label>
                            <div class="dropdown custom-multi-select">
                                <button class="dropdown-toggle" type="button" id="AnHeroBolgeDropdown" data-toggle="dropdown" style="height: 47px;">
                                    <span id="AnHeroBolgeLabel">Bölge Seçin...</span>
                                    <i class="fas fa-chevron-down" style="font-size:.75rem;color:#94a3b8;"></i>
                                </button>
                                <div class="dropdown-menu" onclick="event.stopPropagation();">
                                    <div class="form-check select-all-wrap" id="selectAllAnHeroBolgeRow">
                                        <input class="form-check-input" type="checkbox" id="selectAllAnHeroBolge">
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label fw-bold" for="selectAllAnHeroBolge">Tümünü Seç</label>
                                    </div>
                                    @php $bolgeList = \App\Models\Bolgeler::orderBy('bolge_adi')->get(); @endphp
                                    @foreach($bolgeList as $bolge)
                                        <div class="form-check an-hero-bolge-row" onclick="toggleCheckbox(this)">
                                            <input class="form-check-input an-hero-bolge-cb" type="checkbox" name="bolge_kodu[]" value="{{ $bolge->bolge_kodu }}" id="herobolge_{{ $loop->index }}"
                                                {{ (!request()->has('bolge_kodu') || (is_array(request('bolge_kodu')) && in_array($bolge->bolge_kodu, request('bolge_kodu')))) ? 'checked' : '' }}>
                                            <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                            <label class="form-check-label" for="herobolge_{{ $loop->index }}">{{ $bolge->bolge_adi }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="far fa-calendar-alt me-2"></i> Dönem Seçimi</label>
                            <select name="donem" id="hero_donem" class="form-control-pro" style="height: 47px;">
                                <option value="">Tümü</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}" {{ request('donem') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn-pro btn-primary-pro w-100 justify-content-center" style="height: 47px; background: linear-gradient(135deg, #2563eb, #4f46e5); font-weight: 800;"><i class="fas fa-search"></i> Sonuçları Getir</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SONUÇLAR KONTEYNERI --}}
        <div id="reportResultsContainer">
            @if(request()->filled('donem'))
                @include('reports.partials.anomali_table', ['faturalar' => $faturalar, 'bolgeMap' => $bolgeMap, 'totals' => $totals ?? null])
            @else
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#eff6ff;color:#3b82f6;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">🤖</div>
                    <h4 style="font-weight:800;color:var(--text-slate-900);">Anomali Tespit Motoru</h4>
                    <p style="color:var(--text-slate-500);max-width:500px;margin:0 auto;">Dönem bazında akıllı anomali taramasını başlatmak için kriterleri belirleyin.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- GELİŞMİŞ FİLTRE MODALI --}}
<div class="modal fade" id="anAdvModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 65%;" role="document">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.25); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:30px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.35rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(96,165,250,0.2);border-radius:12px;margin-right:12px;color:#60a5fa;"><i class="fas fa-sliders-h"></i></div>
                        Gelişmiş Filtreleme
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Anomali taramasını daha spesifik kriterlere göre daraltın.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:45px 35px; min-height: 55vh; display: flex; flex-direction: column; justify-content: space-around;">
                
                <!-- ROW 1: Bölgeler & Tesisat No -->
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-map-marker-alt" style="color:#3b82f6; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bölgeler
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="ModalAnBolgeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px;">
                                <span id="ModalAnBolgeLabel">Bölge Seçin...</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllModalAnBolgeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllModalAnBolge">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllModalAnBolge">Tümünü Seç</label>
                                </div>
                                @foreach($bolgeList as $bolge)
                                    <div class="form-check modal-an-bolge-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input modal-an-bolge-cb" type="checkbox" value="{{ $bolge->bolge_kodu }}" id="modalanbolge_{{ $loop->index }}"
                                            {{ (!request()->has('bolge_kodu') || (is_array(request('bolge_kodu')) && in_array($bolge->bolge_kodu, request('bolge_kodu')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="modalanbolge_{{ $loop->index }}">{{ $bolge->bolge_adi }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-hashtag" style="color:#ea580c; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Tesisat No
                        </label>
                        <input type="text" id="an_tesisat" class="form-control-pro" value="{{ request('tesisat_no') }}" placeholder="Örn: 123456" style="padding: 12px 18px; border-radius: 12px; font-family: monospace; font-size: 1.05rem; height: 47px;">
                    </div>
                </div>

                <!-- ROW 2: Fatura No & Dönem -->
                <div class="row" style="background: rgba(241, 245, 249, 0.5); padding: 15px; border-radius: 16px; margin-bottom: 25px;">
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-file-invoice" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Fatura No
                        </label>
                        <input type="text" id="an_fatura" class="form-control-pro" value="{{ request('fatura_no') }}" placeholder="Örn: ABC123" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                    </div>
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-alt" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Analiz Dönemi
                        </label>
                        <select id="modal_donem" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Seçiniz...</option>
                            @foreach($donemler as $d)
                                <option value="{{ $d }}" {{ request('donem') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- ROW 3: Diğerleri -->
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-plug" style="color:#059669; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bağlantı Grubu
                        </label>
                        <select id="an_baglanti" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            <option value="AG" {{ request('baglanti_grubu')=='AG'?'selected':'' }}>AG – Alçak Gerilim</option>
                            <option value="OG" {{ request('baglanti_grubu')=='OG'?'selected':'' }}>OG – Orta Gerilim</option>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-city" style="color:#9333ea; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Yerleşim Türü
                        </label>
                        <select id="an_yerlesim" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            <option value="merkez" {{ request('yerlesim_tipi')=='merkez'?'selected':'' }}>Merkez</option>
                            <option value="koy" {{ request('yerlesim_tipi')=='koy'?'selected':'' }}>Köy</option>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-tags" style="color:#dc2626; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Tarife Grubu
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="AnTarifeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px; height: 47px;">
                                <span id="AnTarifeLabel">Tüm Tarifeler</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllAnTarifeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllAnTarife">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllAnTarife">Tümünü Seç</label>
                                </div>
                                @foreach($tarifeler as $t)
                                    <div class="form-check an-tarife-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input an-tarife-cb" type="checkbox" value="{{ $t->tarife }}" id="antarife_{{ $loop->index }}"
                                            {{ (!request()->has('tarife') || (is_array(request('tarife')) && in_array($t->tarife, request('tarife')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="antarife_{{ $loop->index }}">{{ $t->abone_grubu ?: $t->tarife }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background: rgba(248, 250, 252, 0.8); border-top: 1px solid rgba(226, 232, 240, 0.8); padding: 25px 35px; display:flex; justify-content:space-between; align-items: center; border-bottom-left-radius: 28px; border-bottom-right-radius: 28px;">
                <button type="button" class="btn-pro btn-outline-pro" id="anClearBtn" style="border-radius: 12px; font-weight: 700; transition: all 0.2s;"><i class="fas fa-eraser"></i> Filtreleri Temizle</button>
                <div class="d-flex gap-3">
                    <button type="button" class="btn-pro btn-primary-pro" id="anApplyBtn" style="border-radius: 12px; font-weight: 700; padding-left: 28px; padding-right: 28px; transition: all 0.2s;"><i class="fas fa-check"></i> Sonuçları Getir</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- EXPORT LOADING OVERLAY --}}
<div id="anExportOverlay">
    <div class="an-loader-box">
        <div class="an-success-icon" id="anSuccessIcon"><i class="fas fa-check"></i></div>
        <div class="an-spinner" id="anSpinner"></div>
        <div class="an-loader-title" id="anLoaderTitle">Analiz Raporu Hazırlanıyor…</div>
        <div class="an-loader-sub" id="anLoaderSub">Lütfen bekleyin, anomali dökümü oluşturuluyor.</div>
        <div class="an-progress" id="anProgressWrap"><div class="an-progress-bar" id="anProgressBar"></div></div>
        <button class="an-open-btn" id="anOpenBtn"></button>
        <button class="an-overlay-close" id="anOverlayClose"><i class="fas fa-times"></i> Kapat</button>
    </div>
</div>

{{-- DETAIL MODAL (ANOMALI) --}}
<div id="detMdl">
    <div class="det-box">
        <div style="padding:25px 35px;background:var(--primary-gradient);display:flex;justify-content:space-between;align-items:center;color:#fff;">
            <div>
                <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.8;font-weight:800;margin-bottom:4px;">Anomali Detay Analizi</div>
                <h2 id="detTit" style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;">-</h2>
            </div>
            <button onclick="closeDetail()" style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div style="flex:1;overflow-y:auto;padding:35px;background:#f8fafc;">
            <div id="detGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let lastDownloadUrl = '';
let lastExportType = '';

function showAnOverlay(type) {
    const $bar = $('#anProgressBar');
    $bar.removeClass('an-progress-bar'); void $bar[0].offsetWidth; $bar.addClass('an-progress-bar');
    $('#anSuccessIcon').hide();
    $('#anSpinner').show();
    $('#anProgressWrap').show();
    $('#anOpenBtn').hide();
    $('#anOverlayClose').hide();
    $('#anExportOverlay').addClass('active');
    setTimeout(() => { $('#anOverlayClose').show(); }, 10000);
}

function hideAnOverlay() {
    $('#anExportOverlay').removeClass('active');
    if (lastDownloadUrl) { URL.revokeObjectURL(lastDownloadUrl); lastDownloadUrl = ''; }
}

function showAnReady(type, blobUrl) {
    lastDownloadUrl = blobUrl;
    lastExportType = type;
    $('#anSpinner').hide();
    $('#anProgressWrap').hide();
    $('#anSuccessIcon').css('display','flex');
    $('#anLoaderTitle').text('İndirme Hazır!');
    $('#anLoaderSub').text('Dosyanız başarıyla oluşturuldu.');
    var label = type === 'pdf' ? '<i class="fas fa-file-pdf"></i> PDF Dosyasını Aç' : '<i class="fas fa-file-excel"></i> Excel Dosyasını Aç';
    $('#anOpenBtn').html(label).show();
    $('#anOverlayClose').show();
}

async function handleExport(type) {
    showAnOverlay(type);
    const form = document.getElementById('anomaliFilterForm');
    const formData = new FormData(form);
    formData.append('export', type);
    
    // Add hidden modal inputs
    formData.append('tesisat_no', $('#an_tesisat').val());
    formData.append('fatura_no', $('#an_fatura').val());
    formData.append('baglanti_grubu', $('#an_baglanti').val());
    formData.append('yerlesim_tipi', $('#an_yerlesim').val());
    formData.append('donem', $('#modal_donem').val() || $('#hero_donem').val());
    
    if ($('.modal-an-bolge-cb:checked').length > 0) {
        formData.delete('bolge_kodu[]');
        $('.modal-an-bolge-cb:checked').each(function() { formData.append('bolge_kodu[]', $(this).val()); });
    }
    $('.an-tarife-cb:checked').each(function() { formData.append('tarife[]', $(this).val()); });

    const params = new URLSearchParams(formData);
    try {
        const response = await fetch(`${form.action}?${params.toString()}`);
        if (!response.ok) throw new Error('Rapor oluşturulamadı');
        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        showAnReady(type, blobUrl);
    } catch (e) {
        $('#anLoaderTitle').text('Hata Oluştu');
        $('#anLoaderSub').text('Rapor oluşturulurken bir hata meydana geldi.');
        $('#anSpinner').hide();
        $('#anProgressWrap').hide();
        $('#anOverlayClose').show();
    }
}

let rData = {!! $faturalar->keyBy('id')->toJson() !!};

function normalizeAnomaliResponse(response) {
    if (typeof response === 'object' && response.html !== undefined) {
        rData = Object.assign({}, rData, response.detail_data || {});
        return response.html;
    }

    return response;
}

function showDetail(id) {
    const d = rData[id]; if (!d) return;
    document.getElementById('detTit').innerText = d.fatura_no + ' (' + (d.abone_tesis_no || d.tesisat_no) + ')';
    let h = '';
    let pld = d.payload; if (typeof pld === 'string') { try { pld = JSON.parse(pld); } catch(e) { pld = {}; } }
    const anomaliler = pld && pld._tuketim_anomalileri ? pld._tuketim_anomalileri : [];
    
    if (anomaliler.length > 0) {
        h += `<div style="grid-column: 1 / -1; margin-bottom: 20px;">
                <div style="background: #fff; border-radius: 20px; border: 1px solid #fecaca; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #b91c1c; margin-bottom: 15px;"><i class="fas fa-robot"></i> Sistem Analiz Raporu</h4>
                    <div style="display: flex; flex-direction: column; gap: 10px;">`;
        anomaliler.forEach(ano => {
            let m = typeof ano === 'object' ? (ano.mesaj || '') : ano;
            let det = typeof ano === 'object' ? (ano.detay || '') : '';
            h += `<div style="padding: 12px; background: #fef2f2; border-radius: 12px; border: 1px solid #fee2e2;">
                    <div style="font-weight: 800; color: #b91c1c; font-size: 0.85rem;">${m}</div>
                    <div style="font-size: 0.8rem; color: #7f1d1d; margin-top: 4px;">${det}</div>
                  </div>`;
        });
        h += `</div></div></div>`;
    }
    
    const fields = [
        {k:'Tesisat No',v:d.abone_tesis_no||d.tesisat_no},
        {k:'Fatura No',v:d.fatura_no},
        {k:'Dönem',v:d.donem},
        {k:'Tüketim',v:number_format(d.fatura_edilecek_toplam_tuketim_kwh, 2) + ' kWh'},
        {k:'Toplam Tutar',v:'₺' + number_format(d.tutar_toplam, 2)},
        {k:'İlk Okuma',v:d.ilk_okuma},
        {k:'Son Okuma',v:d.son_okuma}
    ];
    fields.forEach(f => {
        h += `<div style="background:#fff;padding:15px;border-radius:16px;border:1px solid #f1f5f9;">
                <div style="font-size:0.7rem;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">${f.k}</div>
                <div style="font-weight:700;color:#1e293b;">${f.v||'–'}</div>
              </div>`;
    });
    
    document.getElementById('detGrid').innerHTML = h;
    document.getElementById('detMdl').style.display = 'flex';
}
function closeDetail() { document.getElementById('detMdl').style.display = 'none'; }
function number_format(n, d) { return parseFloat(n||0).toLocaleString('tr-TR', {minimumFractionDigits:d, maximumFractionDigits:d}); }

function openItirazModal(id) {
    Swal.fire({
        title: 'Faturaya İtiraz Et',
        input: 'textarea',
        inputLabel: 'İtiraz Nedeni',
        inputPlaceholder: 'Neden itiraz ediyorsunuz?',
        showCancelButton: true,
        confirmButtonText: 'İtiraz Et',
        confirmButtonColor: '#dc2626'
    }).then(res => {
        if (res.isConfirmed && res.value) {
            $.post(`{{ url('fatura/anomali-itiraz') }}/${id}`, { _token: '{{ csrf_token() }}', itiraz_aciklamasi: res.value }, () => {
                Swal.fire('Başarılı', 'İtiraz kaydedildi.', 'success').then(() => $('#anomaliFilterForm').submit());
            });
        }
    });
}

function openAnomaliKaydetModal(id) {
    Swal.fire({
        title: 'Hatayı Kaydet',
        input: 'textarea',
        inputLabel: 'İşlem Notu',
        inputPlaceholder: 'İsterseniz bu anomali için kısa bir not yazın.',
        showCancelButton: true,
        confirmButtonText: 'Kaydet',
        cancelButtonText: 'Vazgeç',
        confirmButtonColor: '#059669'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.post(`{{ url('raporlar/anomali') }}/${id}/kaydet`, {
            _token: '{{ csrf_token() }}',
            islem_notu: res.value || ''
        }).done(() => {
            Swal.fire('Kaydedildi', 'Anormal fatura kayıt listesine taşındı.', 'success').then(() => $('#anomaliFilterForm').submit());
        }).fail(() => {
            Swal.fire('Hata', 'Anomali kaydedilirken bir sorun oluştu.', 'error');
        });
    });
}

function ignoreAnomali(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Hatayı Görmezden Gel',
        text: 'Bu fatura bundan sonra anomali kontrolünde hatalı olarak görünmeyecek.',
        input: 'textarea',
        inputLabel: 'İşlem Notu',
        inputPlaceholder: 'İsterseniz neden görmezden gelindiğini yazın.',
        showCancelButton: true,
        confirmButtonText: 'Görmezden Gel',
        cancelButtonText: 'Vazgeç',
        confirmButtonColor: '#64748b'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.post(`{{ url('raporlar/anomali') }}/${id}/gormezden-gel`, {
            _token: '{{ csrf_token() }}',
            islem_notu: res.value || ''
        }).done(() => {
            Swal.fire('Tamam', 'Bu kayıt artık anomali sonuçlarında gösterilmeyecek.', 'success').then(() => $('#anomaliFilterForm').submit());
        }).fail(() => {
            Swal.fire('Hata', 'Kayıt görmezden gelinirken bir sorun oluştu.', 'error');
        });
    });
}

function toggleCheckbox(row) {
    if (window.event && (window.event.target.tagName === 'INPUT' || window.event.target.tagName === 'LABEL')) return;
    const cb = row.querySelector('input[type="checkbox"]');
    cb.checked = !cb.checked;
    cb.dispatchEvent(new Event('change', { bubbles: true }));
}

$(document).ready(function() {
    function initMS(saId, cbClass, lblId, ph, allTxt, cntTxt) {
        const $sa=$('#'+saId),$cbs=$('.'+cbClass),$lbl=$('#'+lblId),$saRow=$('#'+saId+'Row');
        function upLbl(){
            const n=$cbs.filter(':checked').length;
            $lbl.text(n===0?ph:n===$cbs.length?allTxt:n+' '+cntTxt);
            $sa.prop('checked',n===$cbs.length && n > 0);
            $cbs.each(function(){$(this).closest('.form-check').toggleClass('checked-row',$(this).is(':checked'));});
        }
        if($saRow.length){$saRow.on('click',function(e){if(e.target.tagName!=='INPUT' && e.target.tagName!=='LABEL')$sa.prop('checked',!$sa.prop('checked')).trigger('change');});}
        $sa.on('change',function(){
            $cbs.prop('checked',$(this).is(':checked')).trigger('change');
            upLbl();
        });
        $cbs.on('change',function(){upLbl();});
        upLbl();
    }
    
    let isSyncing = false;
    initMS('selectAllAnHeroBolge','an-hero-bolge-cb','AnHeroBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllModalAnBolge','modal-an-bolge-cb','ModalAnBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllAnTarife','an-tarife-cb','AnTarifeLabel','Tarife Seçin...','Tüm Tarifeler Seçili','Tarife Seçili');

    // Sync Bolge Selection
    $('.an-hero-bolge-cb').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.modal-an-bolge-cb[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });
    $('.modal-an-bolge-cb').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.an-hero-bolge-cb[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });

    // Sync Period
    $('#hero_donem').on('change', function() { $('#modal_donem').val($(this).val()); });
    $('#modal_donem').on('change', function() { $('#hero_donem').val($(this).val()); });

    $('.btn-export-trigger').click(function(e) {
        e.preventDefault();
        handleExport($(this).data('type'));
    });

    $('#anOpenBtn').click(function() {
        if (!lastDownloadUrl) return;
        if (lastExportType === 'pdf') {
            window.open(lastDownloadUrl, '_blank');
        } else {
            const a = document.createElement('a');
            a.href = lastDownloadUrl;
            a.download = 'Anomali_Raporu.xlsx';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
        }
        hideAnOverlay();
    });

    $('#anOverlayClose').click(hideAnOverlay);

    $('#anClearBtn').click(function() {
        $('.modal-an-bolge-cb').prop('checked', false).trigger('change');
        $('.an-tarife-cb').prop('checked', false).trigger('change');
        $('#an_tesisat').val('');
        $('#an_fatura').val('');
        $('#modal_donem').val('');
        $('#an_baglanti').val('');
        $('#an_yerlesim').val('');
    });

    function showAnResultsLoading() {
        Swal.fire({
            title: '',
            html: `
                <div class="an-results-modal">
                    <div class="an-results-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="an-results-title">Sonuçlar Getiriliyor</div>
                    <div class="an-results-sub">Anomali kriterlerine göre kayıtlar analiz ediliyor, lütfen bekleyin.</div>
                    <div class="an-results-progress"><span></span></div>
                    <div class="an-results-dots"><span></span><span></span><span></span></div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: { popup: 'an-results-swal' }
        });
    }

    function showAnResultsReady() {
        return Swal.fire({
            icon: 'success',
            title: 'Tamam',
            text: 'Sonuçlar başarıyla getirildi.',
            confirmButtonText: 'Tamam',
            allowOutsideClick: false
        });
    }

    $('#anomaliFilterForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $container = $('#reportResultsContainer');
        
        let formData = $form.serialize();
        formData += '&tesisat_no=' + $('#an_tesisat').val() +
                    '&fatura_no=' + $('#an_fatura').val() +
                    '&baglanti_grubu=' + $('#an_baglanti').val() +
                    '&yerlesim_tipi=' + $('#an_yerlesim').val() +
                    '&donem=' + ($('#hero_donem').val() || $('#modal_donem').val());
        
        if ($('.modal-an-bolge-cb:checked').length > 0) {
            formData = formData.replace(/bolge_kodu%5B%5D=[^&]*&?/g, '');
            $('.modal-an-bolge-cb:checked').each(function() { formData += '&bolge_kodu[]=' + $(this).val(); });
        }
        
        if (!formData.includes('tarife')) {
            $('.an-tarife-cb:checked').each(function() { formData += '&tarife[]=' + $(this).val(); });
        }

        const hasBolge = $('.an-hero-bolge-cb:checked, .modal-an-bolge-cb:checked').length > 0;
        const hasDonem = !!($('#hero_donem').val() || $('#modal_donem').val());
        const hasTarife = $('.an-tarife-cb:checked').length > 0;
        const hasTesisat = !!$('#an_tesisat').val().trim();
        const hasFatura = !!$('#an_fatura').val().trim();
        const hasBaglanti = !!$('#an_baglanti').val();
        const hasYerlesim = !!$('#an_yerlesim').val();

        if (!hasBolge && !hasDonem && !hasTarife && !hasTesisat && !hasFatura && !hasBaglanti && !hasYerlesim) {
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen sonuçları getirmeden önce en az bir filtreleme seçeneği seçiniz.', confirmButtonText: 'Tamam'});
            return;
        }

        $container.css({'opacity': '0.35', 'pointer-events': 'none'});
        showAnResultsLoading();
        $.ajax({
            url: $form.attr('action'),
            data: formData,
            success: function(response) {
                const html = normalizeAnomaliResponse(response);
                showAnResultsReady().then(function() {
                    $container.html(html).css({'opacity': '1', 'pointer-events': ''});
                    $('#anExportBtnContainer').fadeIn();
                    $('html, body').animate({ scrollTop: $container.offset().top - 100 }, 500);
                });
            },
            error: function() {
                $container.css({'opacity': '1', 'pointer-events': ''});
                Swal.fire({icon: 'error', title: 'Hata', text: 'Analiz yüklenirken bir hata oluştu.', confirmButtonText: 'Tamam'});
            }
        });
    });

    $('#anApplyBtn').click(() => { $('#anAdvModal').modal('hide'); $('#anomaliFilterForm').submit(); });
    
    $(document).on('click', '#reportResultsContainer .pagination a', function(e) {
        e.preventDefault();
        const url = new URL($(this).attr('href'));
        const page = url.searchParams.get('page');
        const $form = $('#anomaliFilterForm');
        let formData = $form.serialize() + '&page=' + page;
        
        formData += '&tesisat_no=' + $('#an_tesisat').val() +
                    '&fatura_no=' + $('#an_fatura').val() +
                    '&baglanti_grubu=' + $('#an_baglanti').val() +
                    '&yerlesim_tipi=' + $('#an_yerlesim').val() +
                    '&donem=' + ($('#hero_donem').val() || $('#modal_donem').val());

        $('#reportResultsContainer').css('opacity', '0.5');
        $.ajax({
            url: $form.attr('action'),
            data: formData,
            success: function(response) {
                const html = normalizeAnomaliResponse(response);
                $('#reportResultsContainer').html(html).css('opacity', '1');
                $('html, body').animate({ scrollTop: $('#reportResultsContainer').offset().top - 100 }, 500);
            }
        });
    });
});
</script>
@endpush
@endsection
