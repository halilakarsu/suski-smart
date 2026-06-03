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

    /* ===== ADVANCED MODAL BUTTON ===== */
    .btn-advanced-pro { position:relative; padding:12px 18px; border-radius:14px; font-weight:700; font-size:.9rem; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:all .3s; border:1.5px solid #c7d2fe; cursor:pointer; background:linear-gradient(135deg,#eff6ff,#f5f3ff); color:#4f46e5; box-shadow:0 4px 12px rgba(79,70,229,.1); }
    .btn-advanced-pro:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(79,70,229,.2); border-color:#818cf8; }
    .adv-active-dot { width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block; animation:pulse-dot 1.5s infinite; }
    @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:.7;} }
    .adv-badge { display:inline-flex; align-items:center; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:20px; font-size:.75rem; font-weight:700; padding:3px 10px; }

    /* Stats & Table */

    .tbl-wrap { overflow-x: auto; border-radius: 20px; }
    .tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
    .tbl th { background: #f8fafc; padding: 16px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 16px 20px; font-size: 0.9rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s; }
    .tbl tr:hover td { background: #f8fafc; }
    .badge-donem { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; }

    /* ===== EXPORT LOADING OVERLAY ===== */
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
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Köy ve Merkez  Raporu</h1>
                <p class="hero-subtitle">Yerleşim türüne göre tüketim ve tutar karşılaştırmaları</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn-advanced-pro" data-toggle="modal" data-target="#kmAdvModal" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
                    <i class="fas fa-sliders-h"></i> Detaylı Filtre
                    @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','end_period']))<span class="adv-active-dot" style="background:#fca5a5;"></span>@endif
                </button>
                <div class="dropdown" id="koyMerkezExportBtnContainer" style="display: {{ request()->anyFilled(['bolge','start_period','end_period','tesisat_no','tarife','baglanti_grubu']) ? 'block' : 'none' }};">
                    <button type="button" class="btn-pro btn-outline-pro dropdown-toggle" data-toggle="dropdown" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
                        <i class="fas fa-file-export"></i> Dışa Aktar
                    </button>
                    <div class="dropdown-menu dropdown-menu-pro dropdown-menu-right" style="border-radius:12px;">
                        <button type="button" id="koy-merkez-export-pdf" class="dropdown-item dropdown-item-pro"><i class="fas fa-file-pdf text-danger"></i> PDF Raporu Al</button>
                        <button type="button" id="koy-merkez-export-excel" class="dropdown-item dropdown-item-pro"><i class="fas fa-file-excel text-success"></i> Excel Raporu Al</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        {{-- FİLTRE KARTI --}}
        <div class="glass-card filter-card">
            <h5 class="section-title"><i class="fas fa-filter"></i> Köy ve Merkeze Göre Raporlama </h5>

            @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','end_period']))
            <div style="margin-bottom:16px;padding:10px 16px;background:linear-gradient(135deg,rgba(37,99,235,.07),rgba(79,70,229,.07));border:1.5px solid rgba(37,99,235,.2);border-radius:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <i class="fas fa-sliders-h" style="color:#2563eb;"></i>
                <span style="font-size:.83rem;font-weight:700;color:#1d4ed8;">Aktif Filtreler:</span>
                @if(request('tarife')) <span class="adv-badge">Tarife: {{ count(request('tarife')) }} seçili</span> @endif
                @if(request('baglanti_grubu')) <span class="adv-badge">{{ request('baglanti_grubu') }}</span> @endif
                @if(request('tesisat_no')) <span class="adv-badge">Tesisat: {{ request('tesisat_no') }}</span> @endif
            </div>
            @endif

            <form action="{{ route('reports.koy-merkez') }}" method="GET" id="koyMerkezFilterForm">
                <div id="kmAdvHidden"></div>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="fas fa-map-marker-alt me-2"></i> Bölge Seçimi</label>
                            <div class="dropdown custom-multi-select">
                                <button class="dropdown-toggle" type="button" id="KmHeroBolgeDropdown" data-toggle="dropdown" style="height: 47px;">
                                    <span id="KmHeroBolgeLabel">Bölge Seçin...</span>
                                    <i class="fas fa-chevron-down" style="font-size:.75rem;color:#94a3b8;"></i>
                                </button>
                                <div class="dropdown-menu" onclick="event.stopPropagation();">
                                    <div class="form-check select-all-wrap" id="selectAllKmHeroBolgeRow">
                                        <input class="form-check-input" type="checkbox" id="selectAllKmHeroBolge">
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label fw-bold" for="selectAllKmHeroBolge">Tümünü Seç</label>
                                    </div>
                                    @foreach($bolgeler as $bolge)
                                        <div class="form-check km-hero-bolge-row" onclick="toggleCheckbox(this)">
                                            <input class="form-check-input km-hero-bolge-cb" type="checkbox" name="bolge[]" value="{{ $bolge }}" id="herobolge_{{ $loop->index }}"
                                                {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($bolge, request('bolge')))) ? 'checked' : '' }}>
                                            <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                            <label class="form-check-label" for="herobolge_{{ $loop->index }}">{{ $bolge }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="far fa-calendar-alt me-2"></i> Dönem Seçimi</label>
                            <select name="start_period" id="hero_start_period" class="form-control-pro" style="height: 47px;">
                                <option value="">Tümü</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}" {{ request('start_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
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
            @if(request()->anyFilled(['bolge','start_period','end_period', 'baglanti_grubu','tarife']))
                @include('reports.partials.koy_merkez_table', ['results' => $results])
            @else
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#f0fdf4;color:#059669;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">🗺</div>
                    <h4 style="font-weight:800;color:var(--text-slate-900);">Köy / Merkez Raporu</h4>
                    <p style="color:var(--text-slate-500);max-width:500px;margin:0 auto;">Yukarıdan kriterleri seçin ve raporu oluşturun.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- GELİŞMİŞ FİLTRE MODALI --}}
<div class="modal fade" id="kmAdvModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 65%;" role="document">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.25); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:30px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.35rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(96,165,250,0.2);border-radius:12px;margin-right:12px;color:#60a5fa;"><i class="fas fa-sliders-h"></i></div>
                        Gelişmiş Filtreleme
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Rapor detaylarını daha spesifik kriterlere göre daraltın.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:40px 35px;">
                <!-- ROW 1: Bölge & Tesisat No -->
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-map-marker-alt" style="color:#3b82f6; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bölgeler
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="ModalKmBolgeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px;">
                                <span id="ModalKmBolgeLabel">Bölge Seçin...</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllModalKmBolgeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllModalKmBolge">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllModalKmBolge">Tümünü Seç</label>
                                </div>
                                @foreach($bolgeler as $bolge)
                                    <div class="form-check modal-km-bolge-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input modal-km-bolge-cb" type="checkbox" value="{{ $bolge }}" id="modalkmbolge_{{ $loop->index }}"
                                            {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($bolge, request('bolge')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="modalkmbolge_{{ $loop->index }}">{{ $bolge }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-hashtag" style="color:#ea580c; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Abone / Tesisat No
                        </label>
                        <input type="text" id="km_tesisat" class="form-control-pro" value="{{ request('tesisat_no') }}" placeholder="Örn: 123456" style="padding: 12px 18px; border-radius: 12px; font-family: monospace; font-size: 1.05rem; height: 47px;">
                    </div>
                </div>

                <!-- ROW 2: Dönem Aralığı Başlangıç & Bitiş -->
                <div class="row" style="background: rgba(241, 245, 249, 0.5); padding: 15px; border-radius: 16px; margin-bottom: 25px;">
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-alt" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Başlangıç Dönemi
                        </label>
                        <select id="modal_start_period" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
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
                            <option value="">Tümü</option>
                            @foreach($donemler as $d)
                                <option value="{{ $d }}" {{ request('end_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- ROW 3: Bağlantı Grubu & Tarife -->
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-plug" style="color:#059669; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bağlantı Grubu
                        </label>
                        <select id="km_baglanti" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            <option value="AG" {{ request('baglanti_grubu')=='AG'?'selected':'' }}>AG – Alçak Gerilim</option>
                            <option value="OG" {{ request('baglanti_grubu')=='OG'?'selected':'' }}>OG – Orta Gerilim</option>
                        </select>
                    </div>
                    <div class="col-md-6" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-tags" style="color:#dc2626; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Abone Tarife Grubu
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="KmTarifeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px; height: 47px;">
                                <span id="KmTarifeLabel">Tüm Tarifeler</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllKmTarifeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllKmTarife">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllKmTarife">Tümünü Seç</label>
                                </div>
                                @foreach($tarifeler as $t)
                                    <div class="form-check km-tarife-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input km-tarife-cb" type="checkbox" value="{{ $t->tarife }}" id="kmtarife_{{ $loop->index }}"
                                            {{ (!request()->has('tarife') || (is_array(request('tarife')) && in_array($t->tarife, request('tarife')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="kmtarife_{{ $loop->index }}">{{ $t->abone_grubu ?: $t->tarife }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(248, 250, 252, 0.8); border-top: 1px solid rgba(226, 232, 240, 0.8); padding: 25px 35px; display:flex; justify-content:space-between; align-items: center; border-bottom-left-radius: 28px; border-bottom-right-radius: 28px;">
                <button type="button" class="btn-pro btn-outline-pro" id="kmClearBtn" style="border-radius: 12px; font-weight: 700; transition: all 0.2s;"><i class="fas fa-eraser"></i> Filtreleri Temizle</button>
                <div class="d-flex gap-3">
                    <button type="button" class="btn-pro btn-primary-pro" id="kmApplyBtn" style="border-radius: 12px; font-weight: 700; padding-left: 28px; padding-right: 28px; transition: all 0.2s;"><i class="fas fa-check"></i> Sonuçları Getir</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- EXPORT YÜKLENİYOR OVERLAY --}}
<div id="yearlyExportOverlay">
    <div class="yearly-loader-box">
        <div id="yearlySuccessIcon" class="yearly-success-icon"><i class="fas fa-check"></i></div>
        <div id="yearlySpinner" class="yearly-spinner"></div>
        <h4 id="yearlyLoaderTitle" class="yearly-loader-title">Rapor Hazırlanıyor</h4>
        <p id="yearlyLoaderSub" class="yearly-loader-sub">Veriler derleniyor, lütfen bekleyin...</p>
        
        <div id="yearlyProgressWrap" class="yearly-progress">
            <div id="yearlyProgressBar" class="yearly-progress-bar"></div>
        </div>
        
        <button type="button" id="yearlyOpenBtn" class="yearly-open-btn"><i class="fas fa-external-link-alt"></i> Dosyayı Aç</button>
        <button type="button" id="yearlyOverlayClose" class="yearly-overlay-close">Kapat</button>
    </div>
</div>

@push('scripts')
<script>
let lastDownloadUrl = '';
let lastExportType = '';

function showOverlay(type) { 
    const $bar = $('#yearlyProgressBar');
    $bar.removeClass('yearly-progress-bar'); void $bar[0].offsetWidth; $bar.addClass('yearly-progress-bar');
    $('#yearlySuccessIcon').hide();
    $('#yearlySpinner').show();
    $('#yearlyProgressWrap').show();
    $('#yearlyOpenBtn').hide();
    $('#yearlyOverlayClose').hide();
    $('#yearlyExportOverlay').addClass('active');
    setTimeout(() => { $('#yearlyOverlayClose').show(); }, 10000);
}
function hideOverlay() { 
    $('#yearlyExportOverlay').removeClass('active'); 
    if(lastDownloadUrl) { URL.revokeObjectURL(lastDownloadUrl); lastDownloadUrl=''; }
}

function buildFormData() {
    const params = new URLSearchParams();

    // Bölge: modal öncelikli, yoksa hero
    const modalBolge = $('.modal-km-bolge-cb:checked').map(function(){ return $(this).val(); }).get();
    const heroBolge  = $('.km-hero-bolge-cb:checked').map(function(){ return $(this).val(); }).get();
    const bolgeList  = modalBolge.length ? modalBolge : heroBolge;
    bolgeList.forEach(function(v){ params.append('bolge[]', v); });

    // Dönem: modal öncelikli, yoksa hero
    const startPeriod = $('#modal_start_period').val() || $('#hero_start_period').val();
    const endPeriod   = $('#modal_end_period').val();
    if (startPeriod) params.set('start_period', startPeriod);
    if (endPeriod)   params.set('end_period', endPeriod);

    // Tarife
    $('.km-tarife-cb:checked').each(function(){ params.append('tarife[]', $(this).val()); });

    // Modal diğer filtreler
    const tesisatNo   = $('#km_tesisat').val().trim();
    const baglantiGrp = $('#km_baglanti').val();
    if (tesisatNo)   params.set('tesisat_no', tesisatNo);
    if (baglantiGrp) params.set('baglanti_grubu', baglantiGrp);

    return params;
}

async function handleExport(type) {
    lastExportType = type;
    showOverlay(type);
    const form = document.getElementById('koyMerkezFilterForm');
    
    const params = buildFormData();
    params.append('export', type);

    try {
        const response = await fetch(`${form.action}?${params.toString()}`);
        if (!response.ok) throw new Error('Rapor oluşturulamadı');
        const blob = await response.blob();
        lastDownloadUrl = window.URL.createObjectURL(blob);
        
        $('#yearlySpinner').hide();
        $('#yearlyProgressWrap').hide();
        $('#yearlySuccessIcon').css('display','flex');
        $('#yearlyLoaderTitle').text('İndirme Hazır!');
        $('#yearlyLoaderSub').text('Dosyanız başarıyla oluşturuldu.');
        
        var label = type === 'pdf' ? '<i class="fas fa-file-pdf"></i> PDF Dosyasını Aç' : '<i class="fas fa-file-excel"></i> Excel Dosyasını Aç';
        $('#yearlyOpenBtn').html(label).show();
        $('#yearlyOverlayClose').show();
    } catch (e) {
        $('#yearlyLoaderTitle').text('Hata Oluştu');
        $('#yearlyLoaderSub').text('Rapor oluşturulurken bir hata meydana geldi.');
        $('#yearlySpinner').hide();
        $('#yearlyProgressWrap').hide();
        $('#yearlyOverlayClose').show();
    }
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
    initMS('selectAllKmHeroBolge','km-hero-bolge-cb','KmHeroBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllModalKmBolge','modal-km-bolge-cb','ModalKmBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllKmTarife','km-tarife-cb','KmTarifeLabel','Tarife Seçin...','Tüm Tarifeler Seçili','Tarife Seçili');

    // Sync Bolge Selection
    $('.km-hero-bolge-cb').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.modal-km-bolge-cb[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });
    $('.modal-km-bolge-cb').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.km-hero-bolge-cb[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });

    // Dönem Swap ve Sync
    function autoSwapPeriods() {
        var start = $('#modal_start_period').val();
        var end   = $('#modal_end_period').val();
        if (start && end && start > end) { // string comparison for YYYY-MM
            $('#modal_start_period').val(end);
            $('#modal_end_period').val(start);
            $('#modal_start_period, #modal_end_period').css({'border-color':'#f59e0b','transition':'border-color 0s'});
            setTimeout(function(){ $('#modal_start_period, #modal_end_period').css({'border-color':'','transition':'border-color 0.4s'}); }, 700);
        }
    }
    $('#modal_start_period').on('change', autoSwapPeriods);
    $('#modal_end_period').on('change', autoSwapPeriods);

    $('#hero_start_period').on('change', function() {
        $('#modal_start_period').val($(this).val());
    });

    $('#koy-merkez-export-pdf').click(() => handleExport('pdf'));
    $('#koy-merkez-export-excel').click(() => handleExport('excel'));
    $('#yearlyOpenBtn').click(() => { 
        if (lastExportType === 'pdf') {
            window.open(lastDownloadUrl, '_blank');
        } else {
            const a = document.createElement('a');
            a.href = lastDownloadUrl;
            a.download = 'KoyMerkez_Raporu.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        hideOverlay(); 
    });

    $('#yearlyOverlayClose').click(hideOverlay);

    $('#kmClearBtn').click(function() {
        $('.km-hero-bolge-cb').prop('checked', false).trigger('change');
        $('.km-tarife-cb').prop('checked', false).trigger('change');
        $('#km_tesisat').val('');
        $('#hero_start_period').val('');
        $('#modal_start_period').val('');
        $('#modal_end_period').val('');
        $('#km_baglanti').val('');
    });

    $('#koyMerkezFilterForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $container = $('#reportResultsContainer');
        
        const params = buildFormData();

        // ─── Filtre Validasyon ───────────────────────────────────────────────
        const hasBolge   = $('.km-hero-bolge-cb:checked, .modal-km-bolge-cb:checked').length > 0;
        const hasDonem   = !!$('#hero_start_period').val() || !!$('#modal_start_period').val() || !!$('#modal_end_period').val();
        const hasTarife  = $('.km-tarife-cb:checked').length > 0;
        const hasTesisat = !!$('#km_tesisat').val().trim();
        const hasBaglanti= !!$('#km_baglanti').val();
        if (!hasBolge && !hasDonem && !hasTarife && !hasTesisat && !hasBaglanti) {
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen sonuçları getirmeden önce en az bir filtreleme seçeneği seçiniz.', confirmButtonText: 'Tamam'});
            return;
        }
        // ─────────────────────────────────────────────────────────────────────

        $container.css('opacity', '0.5');
        $.ajax({
            url: $form.attr('action'),
            data: params.toString(),
            success: function(html) {
                $container.html(html).css('opacity', '1');
                $('#koyMerkezExportBtnContainer').fadeIn();
                $('html, body').animate({ scrollTop: $container.offset().top - 100 }, 500);
            },
            error: function() {
                $container.css('opacity', '1');
                alert('Rapor yüklenirken bir hata oluştu.');
            }
        });
    });

    $('#kmApplyBtn').click(() => { $('#kmAdvModal').modal('hide'); $('#koyMerkezFilterForm').submit(); });
});
</script>
@endpush
@endsection
