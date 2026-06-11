@extends('frontend.layouts.app')

@section('content')
    <style>
        /* Ultra-Premium Glassmorphic Design for Finalized Invoices */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --font-primary: 'Plus Jakarta Sans', sans-serif;
            --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
            --bg-main: #f4f6f9;
            --surface-glass: rgba(255, 255, 255, 0.85);
            --text-slate-900: #0f172a;
            --text-slate-500: #64748b;
            --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
        }

        .pg-premium {
            background-color: var(--bg-main) !important;
            min-height: 100vh;
            padding-bottom: 4rem;
        }

        .page-hero {
            background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
            position: relative;
            padding: 5rem 2rem 10rem 2rem;
            margin-top: -20px;
            color: #fff;
            overflow: hidden;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .page-hero::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, transparent 70%);
            top: -200px;
            left: -100px;
            border-radius: 50%;
            opacity: 0.6;
            filter: blur(40px);
            animation: pulseSlow 8s infinite alternate;
            pointer-events: none;
            z-index: 1;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            bottom: -100px;
            right: 5%;
            border-radius: 50%;
            opacity: 0.6;
            filter: blur(50px);
            animation: pulseDelay 10s infinite alternate;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes pulseSlow {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }

            100% {
                transform: scale(1.2);
                opacity: 0.9;
            }
        }

        @keyframes pulseDelay {
            0% {
                transform: scale(1) translate(0, 0);
            }

            100% {
                transform: scale(1.3) translate(-20px, -20px);
            }
        }

        .hero-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-title-group h1 {
            font-family: var(--font-primary);
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            background: linear-gradient(to right, #ffffff, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        /* Main Container */
        .main-container {
            width: 100%;
            max-width: 1400px;
            margin: -5rem auto 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 20;
        }

        /* Glass Card */
        .glass-card {
            background: var(--surface-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            padding: 30px;
            box-shadow: var(--shadow-elevated);
            margin-bottom: 30px;
        }

        /* Stats Card Premium */
        .stat-mini-card {
            background: var(--surface-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--shadow-elevated);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .stat-mini-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.12);
            border-color: rgba(255, 255, 255, 1);
        }

        .stat-mini-card::before {
            content: '';
            position: absolute;
            right: -15%;
            top: -15%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.12;
            filter: blur(20px);
            transition: all 0.5s;
            background: currentColor;
        }

        .stat-icon-pro {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Table Design */
        .table-pro {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-pro th {
            color: #94a3b8;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 15px 20px;
            text-align: left;
            border: none;
        }

        .table-pro td {
            background: #fff;
            padding: 16px 20px;
            vertical-align: middle;
            border: none;
            transition: all 0.2s ease;
        }

        .table-pro tr td:first-child {
            border-top-left-radius: 18px;
            border-bottom-left-radius: 18px;
        }

        .table-pro tr td:last-child {
            border-top-right-radius: 18px;
            border-bottom-right-radius: 18px;
        }

        .table-pro tr:hover td {
            background: #f8fafc;
        }

        /* Buttons - Truly Premium */
        .btn-premium {
            padding: 12px 28px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            z-index: 1;
            text-decoration: none !important;
        }

        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn-premium:hover::before {
            left: 100%;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff !important;
            box-shadow: 0 12px 24px -6px rgba(37, 99, 235, 0.35);
        }

        .btn-premium-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 30px -8px rgba(37, 99, 235, 0.45);
        }

        .btn-premium-outline {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff !important;
        }

        .btn-premium-outline:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px);
        }

        .btn-premium-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff !important;
            box-shadow: 0 12px 24px -6px rgba(16, 185, 129, 0.3);
        }

        .btn-premium-success:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 30px -8px rgba(16, 185, 129, 0.4);
        }

        .btn-premium-simple {
            background: #fff;
            color: #475569 !important;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-premium-simple:hover {
            border-color: #3b82f6;
            color: #3b82f6 !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.15);
        }

        .dropdown-menu-pro {
            display: none;
            position: absolute;
            right: 0;
            top: 110%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid #f1f5f9;
            z-index: 1000;
            min-width: 180px;
            overflow: hidden;
        }

        .dropdown-item-pro {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #1e293b;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: background 0.2s;
        }

        .dropdown-item-pro:hover {
            background: #f8fafc;
        }

        .district-badge {
            background: #eff6ff;
            color: #2563eb;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .account-mono {
            font-family: monospace;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .amount-success {
            color: #16a34a;
            font-weight: 800;
            font-size: 1rem;
        }

        /* Anomaly Styles */
        .anomaly-row {
            background: #fff1f2 !important;
        }

        .anomaly-row:hover td {
            background: #ffe4e6 !important;
        }

        .anomaly-indicator {
            color: #ef4444;
            margin-right: 8px;
            animation: pulseAnomali 2s infinite;
        }

        @keyframes pulseAnomali {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }

        .anomaly-badge {
            background: #ef4444;
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Dönem Kartları */
        .donem-scroll-wrapper {
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .donem-scroll-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .donem-scroll-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }

        .donem-cards-row {
            display: flex;
            gap: 14px;
            width: max-content;
            padding: 4px 2px 8px 2px;
        }

        /* Dönem Kartları - Dashboard KPI Style */
        .donem-card {
            background: var(--surface-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            display: flex !important;
            flex-direction: column;
            text-decoration: none !important;
            min-height: 160px;
            cursor: pointer;
            z-index: 1;
        }

        .donem-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12), 0 0 20px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.3);
            background: #fff;
        }

        .donem-card.active {
            border: 2px solid #2563eb;
            box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25), 0 0 25px rgba(37, 99, 235, 0.15);
            background: #fff;
        }

        .donem-card::before {
            content: '';
            position: absolute;
            right: -15%;
            top: -15%;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.08;
            filter: blur(20px);
            transition: all 0.5s;
            background: #3b82f6;
        }

        .donem-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            z-index: 2;
        }

        .donem-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-slate-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .donem-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        .donem-card.active .donem-icon {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .donem-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-slate-900);
            z-index: 2;
            margin-top: 5px;
            line-height: 1.2;
        }

        .donem-desc {
            font-size: 0.75rem;
            color: var(--text-slate-500);
            margin-top: 8px;
            z-index: 2;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .donem-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            padding-top: 8px;
            margin-top: 8px;
        }

        .donem-stat-label {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }

        .donem-stat-val {
            font-size: 0.8rem;
            color: #334155;
            font-weight: 700;
        }

        .donem-card.active .donem-title {
            color: #2563eb;
        }

        .donem-card.active .donem-value {
            color: #1e3a8a;
        }

        /* Scroll Navigation Arrows */
        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e2e8f0;
            color: #64748b;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.22s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 0.78rem;
            outline: none;
            padding: 0;
        }

        .scroll-arrow:hover {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-color: transparent;
            color: #fff;
            transform: translateY(-50%) scale(1.12);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.28);
        }

        .scroll-arrow-left {
            left: 4px;
        }

        .scroll-arrow-right {
            right: 4px;
        }

        .scroll-host {
            position: relative;
            padding: 0 40px;
        }
    </style>

    @if(session('error'))
        <div
            style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; margin: 20px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-exclamation-circle text-danger" style="font-size: 1.25rem;"></i>
                <div style="color: #991b1b; font-weight: 700; font-size: 0.9rem;">{{ session('error') }}</div>
            </div>
            <button onclick="this.parentElement.style.display='none'"
                style="background:none;border:none;color:#ef4444;cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="pg-premium p-0">
        <div class="page-hero">
            <div class="hero-container">
                <div class="hero-title-group">
                    <h1 class="hero-title">KESİNLEŞEN FATURALAR</h1>
                    <p class="hero-subtitle">Fatura detaylarını görüntüleyerek detaylı analiz yapabilirsiniz.</p>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" id="analysisBtn" class="btn-premium btn-premium-success" style="display:none;"
                        onclick="runAnalysis()">
                        <i class="fas fa-search-dollar"></i> Fatura Analizi Yap
                    </button>
                    <button type="button" class="btn-premium btn-premium-outline"
                        onclick="document.getElementById('filterMdl').style.display='flex'">
                        <i class="fas fa-filter"></i> Filtrele
                    </button>
                </div>
            </div>
        </div>

        <div class="main-container">

            <!-- YIL KARTLARI — Her zaman görünür -->
            <div class="glass-card" style="padding: 15px 25px; margin-bottom: 20px;">
                <div
                    style="display: flex; gap: 8px; justify-content: flex-start; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 5px; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
                    @foreach($yilStats as $ys)
                        @php
                            $isActiveYil = ($selectedYil == $ys->yil);
                        @endphp
                        <a href="javascript:void(0)" class="yil-tab {{ $isActiveYil ? 'active' : '' }}"
                            data-yil="{{ $ys->yil }}" onclick="selectYil('{{ $ys->yil }}')"
                            title="{{ $ys->yil }} yılını {{ $isActiveYil ? 'kapat' : 'seç' }}"
                            style="padding: 8px 20px; border-radius: 99px; font-weight: 700; font-size: 0.95rem; white-space: nowrap; flex-shrink: 0; color: {{ $isActiveYil ? '#fff' : '#64748b' }}; background: {{ $isActiveYil ? 'linear-gradient(135deg, #3b82f6, #4f46e5)' : '#f1f5f9' }}; text-decoration: none !important; transition: all 0.3s; box-shadow: {{ $isActiveYil ? '0 10px 20px -5px rgba(59, 130, 246, 0.4)' : 'none' }}; border: 2px solid transparent;"
                            onmouseover="if(!this.classList.contains('active')){ this.style.color='#3b82f6'; this.style.background='#e0f2fe'; this.style.transform='translateY(-2px)'; }"
                            onmouseout="if(!this.classList.contains('active')){ this.style.color='#64748b'; this.style.background='#f1f5f9'; this.style.transform='translateY(0)'; }">
                            {{ $ys->yil }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- DÖNEM KARTLARI — JS tarafından doldurulur -->
            <div id="donem-section"></div>

            <!-- Yıl seçilmemiş ipucu -->
            <div id="no-year-hint" class="glass-card" style="padding:40px 28px;text-align:center;margin-bottom:20px;">
                <div style="font-size:3rem;color:#cbd5e1;margin-bottom:16px;"><i class="fas fa-hand-pointer"></i></div>
                <div style="font-weight:800;font-size:1.15rem;color:#334155;margin-bottom:8px;">Bir Yıl Seçin</div>
                <div style="color:#64748b;font-size:0.9rem;">Dönemleri ve faturaları görüntülemek için yukarıdaki yıl
                    kartlarından birine tıklayın.</div>
            </div>



        </div>
    </div>

    <!-- PERIOD TABLE MODAL -->
    <div id="periodTableMdl"
        style="position:fixed; inset:0; background:rgba(15,23,42,0.85); z-index:9990; display:none; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(10px);">
        <div
            style="background:#f8fafc; border-radius:32px; width:100%; max-width:1400px; height:90vh; display:flex; flex-direction:column; box-shadow:0 40px 80px rgba(0,0,0,0.35); overflow:hidden;">
            <!-- Header -->
            <div
                style="padding:25px 35px; background:var(--primary-gradient); display:flex; justify-content:space-between; align-items:center; color:#fff;">
                <div>

                    <h2 id="periodTableTit" style="margin:0;font-size:1.6rem;font-weight:800;letter-spacing:-.02em;">-</h2>
                </div>
                <div style="display:flex; align-items:center; gap:15px;">
                    <button class="btn-premium btn-premium-outline" onclick="exportToExcel(this)"
                        style="padding: 10px 20px; border-radius: 12px; font-size: 0.85rem;"><i
                            class="fas fa-file-excel"></i> Excel Çıktısı Al</button>
                    <button class="btn-premium btn-premium-outline" onclick="exportToPDF(this)"
                        style="padding: 10px 20px; border-radius: 12px; font-size: 0.85rem;"><i class="fas fa-file-pdf"></i>
                        PDF Çıktısı Al</button>
                    <button onclick="closePeriodModal()"
                        style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;margin-left:15px;"><i
                            class="fas fa-times"></i></button>
                </div>
            </div>

            <!-- Body -->
            <div style="flex:1; overflow-y:auto; padding:35px;">
                <!-- STATS SUMMARY -->
                <div id="stats-section" class="row mb-4" style="display:none;">
                    <div class="col-md-4">
                        <div class="stat-mini-card">
                            <div class="stat-icon-pro" style="background:#6366f1;"><i class="fas fa-file-invoice"></i></div>
                            <div>
                                <div id="stat-count" style="font-size:1.5rem;font-weight:800;color:#0f172a;">0</div>
                                <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Toplam
                                    Adet</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini-card">
                            <div class="stat-icon-pro" style="background:#10b981;"><i class="fas fa-wallet"></i></div>
                            <div>
                                <div id="stat-tutar" style="font-size:1.5rem;font-weight:800;color:#0f172a;">₺0</div>
                                <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Toplam
                                    Tutar (Net)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini-card">
                            <div class="stat-icon-pro" style="background:#3b82f6;"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div id="stat-tuketim" style="font-size:1.5rem;font-weight:800;color:#0f172a;">0 kWh</div>
                                <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Toplam
                                    Tüketim</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MAIN TABLE -->
                <div id="table-section"></div>
            </div>
        </div>
    </div>

    <!-- FILTER MODAL -->
    <div id="filterMdl"
        style="position:fixed; inset:0; background:rgba(15,23,42,0.8); z-index:9999; display:none; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(8px);">
        <div
            style="background:#fff; border-radius:32px; width:100%; max-width:550px; overflow:hidden; box-shadow:0 30px 60px rgba(0,0,0,0.3);">
            <div
                style="padding:30px 40px; background:var(--primary-gradient); color:#fff; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:1.35rem; font-weight:800;"><i class="fas fa-filter"></i> Detaylı Filtreleme
                </h3>
                <button onclick="document.getElementById('filterMdl').style.display='none'"
                    style="background:none; border:none; color:#fff; cursor:pointer; font-size:1.5rem; opacity:0.8; transition:opacity 0.2s;"
                    onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8"><i
                        class="fas fa-times"></i></button>
            </div>
            <form id="filterForm" onsubmit="applyFilters(event)"
                style="padding:40px; display:flex; flex-direction:column; gap:25px;">
                <div class="row g-4">
                    <div class="col-6">
                        <label
                            style="font-size:0.75rem; font-weight:800; color:var(--text-slate-500); text-transform:uppercase; margin-bottom:10px; display:block;">Yıl</label>
                        <select name="yil" id="filterYil" class="form-control" style="border-radius:12px; padding:12px;"
                            onchange="filterPeriods(this.value)">
                            <option value="">Tüm Yıllar</option>
                            @foreach($yillar as $y)
                                <option value="{{ $y }}" {{ request('yil') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label
                            style="font-size:0.75rem; font-weight:800; color:var(--text-slate-500); text-transform:uppercase; margin-bottom:10px; display:block;">Dönem</label>
                        <select name="donem" id="filterDonem" class="form-control"
                            style="border-radius:12px; padding:12px;">
                            <option value="">Tüm Dönemler</option>
                            @foreach($donemler as $d)
                                <option value="{{ $d }}" data-year="{{ explode('-', $d)[0] }}" {{ request('donem') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label
                            style="font-size:0.75rem; font-weight:800; color:var(--text-slate-500); text-transform:uppercase; margin-bottom:10px; display:block;">Bölge
                            Koordinasyonu</label>
                        <select name="bolge_kodu" class="form-control" style="border-radius:12px; padding:12px;">
                            <option value="">Tüm Bölgeler</option>
                            @foreach($bolgeMap as $kodu => $adi)
                                <option value="{{ $kodu }}" {{ request('bolge_kodu') == $kodu ? 'selected' : '' }}>{{ $adi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label
                            style="font-size:0.75rem; font-weight:800; color:var(--text-slate-500); text-transform:uppercase; margin-bottom:10px; display:block;">Tesisat
                            No</label>
                        <input type="text" name="tesisat_no" value="{{ request('tesisat_no') }}" class="form-control"
                            style="border-radius:12px; padding:12px;" placeholder="Ara...">
                    </div>
                    <div class="col-6">
                        <label
                            style="font-size:0.75rem; font-weight:800; color:var(--text-slate-500); text-transform:uppercase; margin-bottom:10px; display:block;">Fatura
                            No</label>
                        <input type="text" name="fatura_no" value="{{ request('fatura_no') }}" class="form-control"
                            style="border-radius:12px; padding:12px;" placeholder="Ara...">
                    </div>
                </div>
                <button type="submit" class="btn-premium btn-premium-primary"
                    style="width:100%; padding:18px; margin-top:15px; font-size:1rem;">
                    <i class="fas fa-search"></i> Filtreleri Uygula
                </button>
            </form>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div id="detMdl"
        style="position:fixed;inset:0;background:rgba(15,23,42,0.85);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(10px);">
        <div
            style="background:#fff;border-radius:32px;width:100%;max-width:1000px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 80px rgba(0,0,0,0.35);overflow:hidden;">
            <div
                style="padding:25px 35px;background:var(--primary-gradient);display:flex;justify-content:space-between;align-items:center;color:#fff;">
                <div>
                    <div
                        style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.8;font-weight:800;margin-bottom:4px;">
                        Fatura Detay Analizi</div>
                    <h2 id="detTit" style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;">-</h2>
                </div>
                <button onclick="closeDetail()"
                    style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;"><i
                        class="fas fa-times"></i></button>
            </div>
            <div style="flex:1;overflow-y:auto;padding:35px;background:#f8fafc;">
                <div id="anomaliAlert"
                    style="display:none; margin-bottom:25px; background:#fff1f2; border:1px solid #fda4af; border-radius:20px; padding:20px;">
                    <h4
                        style="color:#e11d48; font-size:0.9rem; font-weight:800; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-exclamation-triangle"></i> TESPİT EDİLEN ANOMALİLER
                    </h4>
                    <div id="anomaliList" style="display:flex; flex-direction:column; gap:10px;"></div>
                </div>
                <div id="detGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            /* ── Config ─────────────────────────────────────────────────────────── */
            const AJAX_DONEM = '{{ url("/fatura/odenenler/ajax/donemler") }}';
            const AJAX_TABLO = '{{ url("/fatura/odenenler/ajax/tablo") }}';
            const EXCEL_URL = '{{ route("odenenler.export.excel") }}';
            const PDF_URL = '{{ route("odenenler.export.pdf") }}';
            const bMap = {!! json_encode($bolgeMap) !!};
            const AY = { '01': 'Ocak', '02': 'Şubat', '03': 'Mart', '04': 'Nisan', '05': 'Mayıs', '06': 'Haziran', '07': 'Temmuz', '08': 'Ağustos', '09': 'Eylül', '10': 'Ekim', '11': 'Kasım', '12': 'Aralık' };

            /* ── State ───────────────────────────────────────────────────────────── */
            let curYil = null;
            let curDonem = null;
            let curFilters = {};
            let rData = {}; // for detail modal

            /* ── Helpers ─────────────────────────────────────────────────────────── */
            const fmt = (n, d = 0) => parseFloat(n || 0).toLocaleString('tr-TR', { minimumFractionDigits: d, maximumFractionDigits: d });
            const fmtTL = n => '₺' + fmt(n, 2);

            /* ── Year Selection ──────────────────────────────────────────────────── */
            function selectYil(yil) {
                if (curYil === yil) {
                    curYil = null; curDonem = null; curFilters = {};
                    document.querySelectorAll('.yil-tab').forEach(c => {
                        c.classList.remove('active');
                        c.style.color = '#64748b';
                        c.style.background = '#f1f5f9';
                        c.style.boxShadow = 'none';
                        c.style.transform = 'translateY(0)';
                    });
                    document.getElementById('donem-section').innerHTML = '';
                    document.getElementById('no-year-hint').style.display = 'block';
                    return;
                }
                curYil = yil; curDonem = null; curFilters = {}; // Reset manual filters when selecting a year
                document.querySelectorAll('.yil-tab').forEach(c => {
                    const active = c.dataset.yil === yil;
                    c.classList.toggle('active', active);
                    c.style.color = active ? '#fff' : '#64748b';
                    c.style.background = active ? 'linear-gradient(135deg, #3b82f6, #4f46e5)' : '#f1f5f9';
                    c.style.boxShadow = active ? '0 10px 20px -5px rgba(59, 130, 246, 0.4)' : 'none';
                    c.style.transform = active ? 'translateY(-2px)' : 'translateY(0)';
                });
                // Update filter modal inputs
                const fYil = document.getElementById('filterYil');
                if (fYil) { fYil.value = yil; filterPeriods(yil); }
                const fDonem = document.getElementById('filterDonem');
                if (fDonem) fDonem.value = '';

                document.getElementById('no-year-hint').style.display = 'none';
                const statsSec = document.getElementById('stats-section');
                if (statsSec) statsSec.style.display = 'none';
                const ts = document.getElementById('table-section');
                if (ts) ts.innerHTML = '';

                updateAnalysisBtn();
                loadDonemler(yil);
            }

            async function loadDonemler(yil) {
                const sec = document.getElementById('donem-section');
                sec.innerHTML = `<div class="glass-card" style="padding:30px;text-align:center;margin-bottom:20px;"><i class="fas fa-spinner fa-spin" style="color:#3b82f6;font-size:1.5rem;"></i></div>`;
                try {
                    const res = await fetch(`${AJAX_DONEM}/${yil}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    const data = await res.json();
                    renderDonemCards(data.donemler, yil);
                } catch (e) {
                    console.error('loadDonemler error:', e);
                    sec.innerHTML = `<div class="glass-card" style="padding:20px;color:#ef4444;text-align:center;">Dönemler yüklenemedi.</div>`;
                }
            }

            function renderDonemCards(donemler, yil) {
                if (!donemler || !donemler.length) {
                    document.getElementById('donem-section').innerHTML = `<div class="glass-card" style="padding:30px;text-align:center;">Bu yıla ait dönem bulunamadı.</div>`;
                    return;
                }
                let cards = '';
                donemler.forEach(ds => {
                    const ay = AY[ds.donem.split('-')[1]] || ds.donem.split('-')[1];
                    const active = curDonem === ds.donem;
                    // Alternating colors like dashboard
                    const iconClass = active ? 'linear-gradient(135deg, #2563eb, #1e40af)' : 'linear-gradient(135deg, #60a5fa, #2563eb)';

                    cards += `
                                <div class="col-md-4 mb-3">
                                    <a href="javascript:void(0)" class="donem-card${active ? ' active' : ''}" data-donem="${ds.donem}" onclick="selectDonem('${ds.donem}')">

                                        <div class="donem-value">${ay}</div>
                                        <div class="donem-desc">
                                            <div class="donem-stat-row" style="border:none; margin-top:0; padding:0;">
                                                <span class="donem-stat-label">Toplam Tutar</span>
                                                <span class="donem-stat-val" style="color:#16a34a">₺${fmt(ds.total_tutar, 2)}</span>
                                            </div>
                                            <div class="donem-stat-row">
                                                <span class="donem-stat-label">Fatura Sayısı</span>
                                                <span class="donem-stat-val">${fmt(ds.count)} Adet</span>
                                            </div>
                                            <div class="donem-stat-row">
                                                <span class="donem-stat-label">Tüketim</span>
                                                <span class="donem-stat-val">${fmt(ds.total_tuketim, 2)} kWh</span>
                                            </div>

                                        </div>
                                    </a>
                                </div>`;
                });
                document.getElementById('donem-section').innerHTML = `
                                                                                                                        <div class="glass-card" style="padding:22px 28px;margin-bottom:20px;border:2px solid #dbeafe;">
                                                                                                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                                                                                                <div style="font-size:.85rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#3b82f6;"><i class="fas fa-calendar-alt" style="margin-right:5px;"></i> ${yil} Yılı Dönemleri</div>
                                                                                                                                <div style="font-size:.85rem;font-weight:700;color:#94a3b8;">${donemler.length} Dönem · İncelemek istediğiniz dönemi seçin</div>
                                                                                                                            </div>
                                                                                                                            <div class="row g-3">
                                                                                                                                ${cards}
                                                                                                                            </div>
                                                                                                                        </div>`;
            }

            /* ── Period Selection ────────────────────────────────────────────────── */
            function selectDonem(donem) {
                if (curDonem === donem) {
                    // Eğer tıklanan zaten açıksa bişi yapma
                    document.getElementById('periodTableMdl').style.display = 'flex';
                    return;
                }
                curDonem = donem;
                document.querySelectorAll('[data-donem]').forEach(c => c.classList.toggle('active', c.dataset.donem === donem));
                // Update filter modal
                const fDonem = document.getElementById('filterDonem');
                if (fDonem) fDonem.value = donem;

                // Update Modal Title
                const ayNo = donem.split('-')[1];
                const yilNo = donem.split('-')[0];
                const ayIsim = AY[ayNo] || ayNo;
                document.getElementById('periodTableTit').innerText = ayIsim + ' ' + yilNo + ' Faturaları';

                // Show Modal
                document.getElementById('periodTableMdl').style.display = 'flex';

                // Load Data
                updateAnalysisBtn();
                loadTable(1);
            }

            function closePeriodModal() {
                document.getElementById('periodTableMdl').style.display = 'none';
            }

            /* ── Table Loading ───────────────────────────────────────────────────── */
            async function loadTable(page) {
                const ts = document.getElementById('table-section');
                if (!ts) return;

                ts.innerHTML = `<div class="glass-card" style="text-align:center;padding:60px;"><i class="fas fa-spinner fa-spin" style="font-size:2rem;color:#3b82f6;"></i><div style="color:#64748b;margin-top:12px;font-weight:600;">Faturalar yükleniyor...</div></div>`;
                const p = new URLSearchParams();
                if (curYil) p.set('yil', curYil);
                if (curDonem) p.set('donem', curDonem);

                Object.keys(curFilters).forEach(k => {
                    if (curFilters[k]) p.set(k, curFilters[k]);
                });

                p.set('page', page);
                try {
                    const res = await fetch(`${AJAX_TABLO}?${p.toString()}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                    const data = await res.json();
                    updateStats(data.stats);
                    document.getElementById('stats-section').style.display = 'flex';
                    renderTable(data);
                } catch (e) {
                    console.error('loadTable error:', e);
                    ts.innerHTML = `<div class="glass-card" style="color:#ef4444;text-align:center;padding:40px;">Tablo yüklenemedi.</div>`;
                }
            }

            function updateStats(s) {
                document.getElementById('stats-section').style.display = 'flex';
                document.getElementById('stat-count').textContent = fmt(s.count);
                document.getElementById('stat-tutar').textContent = fmtTL(s.total_tutar);
                document.getElementById('stat-tuketim').textContent = fmt(s.total_tuketim, 2) + ' kWh';
            }

            function renderTable(data) {
                const items = data.data || [];
                if (!items.length) {
                    document.getElementById('table-section').innerHTML = `<div class="glass-card"><div style="text-align:center;padding:60px;background:#f8fafc;border-radius:24px;border:2px dashed #e2e8f0;"><div style="font-size:3rem;color:#cbd5e1;margin-bottom:16px;"><i class="fas fa-calendar-times"></i></div><div style="font-weight:800;font-size:1.25rem;color:#334155;">Bu Dönemde Fatura Yok</div></div></div>`;
                    return;
                }
                items.forEach(f => { rData[f.id] = f; });

                let rows = '';
                items.forEach(f => {
                    const anomaliler = f.payload?._tuketim_anomalileri || [];
                    const hasA = anomaliler.length > 0;
                    rows += `<tr class="${hasA ? 'anomaly-row' : ''}">
                                                                                                                             <td><span class="district-badge">${f.adres || '—'}</span></td>
                                                                                                                            <td><span class="account-mono">${f.abone_tesis_no || f.tesisat_no || ''}</span></td>
                                                                                                                            <td><span class="badge-pro badge-blue">${f.donem || ''}</span></td>
                                                                                                                            <td style="text-align:right;font-weight:700;color:#2563eb;">${fmt(f.fatura_edilecek_toplam_tuketim_kwh, 2)}</td>
                                                                                                                            <td style="text-align:right;"><span class="amount-success">₺${fmt(f.tutar_toplam, 2)}</span></td>
                                                                                                                            <td style="text-align:right;">
                                                                                                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                                                                                                    ${hasA ? `<span class="anomaly-badge">DİKKAT</span>` : ''}
                                                                                                                                    <button class="btn-premium btn-premium-simple" style="padding:8px 16px;font-size:.75rem;" onclick="showDetail(${f.id})"><i class="fas fa-eye"></i> Detay</button>
                                                                                                                                </div>
                                                                                                                            </td></tr>`;
                });

                let pager = '';
                if (data.last_page > 1) {
                    const cp = data.current_page;
                    const lp = data.last_page;
                    const btnStyle = (active) => `padding:8px 14px;border-radius:10px;font-weight:700;cursor:pointer;border:${active ? 'none' : '1px solid #e2e8f0'};background:${active ? 'linear-gradient(135deg,#3b82f6,#6366f1)' : '#fff'};color:${active ? '#fff' : '#475569'};transition:all 0.2s;`;
                    const disabledStyle = `padding:8px 14px;border-radius:10px;font-weight:700;cursor:not-allowed;border:1px solid #f1f5f9;background:#f8fafc;color:#cbd5e1;`;
                    let btns = '';

                    // Prev button
                    btns += cp > 1
                        ? `<button onclick="loadTable(${cp - 1})" style="${btnStyle(false)}"><i class="fas fa-chevron-left" style="font-size:.75rem;"></i></button>`
                        : `<button disabled style="${disabledStyle}"><i class="fas fa-chevron-left" style="font-size:.75rem;"></i></button>`;

                    // First page
                    if (cp > 3) {
                        btns += `<button onclick="loadTable(1)" style="${btnStyle(false)}">1</button>`;
                        if (cp > 4) btns += `<span style="color:#94a3b8;font-weight:700;padding:0 4px;">…</span>`;
                    }

                    // Sliding window: cp-2 to cp+2
                    const wStart = Math.max(1, cp - 2);
                    const wEnd = Math.min(lp, cp + 2);
                    for (let i = wStart; i <= wEnd; i++) {
                        btns += `<button onclick="loadTable(${i})" style="${btnStyle(i === cp)}">${i}</button>`;
                    }

                    // Last page
                    if (cp < lp - 2) {
                        if (cp < lp - 3) btns += `<span style="color:#94a3b8;font-weight:700;padding:0 4px;">…</span>`;
                        btns += `<button onclick="loadTable(${lp})" style="${btnStyle(false)}">${lp}</button>`;
                    }

                    // Next button
                    btns += cp < lp
                        ? `<button onclick="loadTable(${cp + 1})" style="${btnStyle(false)}"><i class="fas fa-chevron-right" style="font-size:.75rem;"></i></button>`
                        : `<button disabled style="${disabledStyle}"><i class="fas fa-chevron-right" style="font-size:.75rem;"></i></button>`;

                    pager = `<div style="margin-top:25px;padding-top:20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                                                                                                                            <div style="font-size:.8rem;color:#94a3b8;font-weight:600;">Sayfa <strong style="color:#334155;">${cp}</strong> / ${lp}</div>
                                                                                                                            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">${btns}</div>
                                                                                                                        </div>`;
                }

                const p = new URLSearchParams();
                if (curYil) p.set('yil', curYil);
                if (curDonem) p.set('donem', curDonem);
                Object.keys(curFilters).forEach(k => { if (curFilters[k]) p.set(k, curFilters[k]); });

                const fName = `Odenen_Faturalar_${(curDonem || curYil || 'Tum').replace(/-/g, '_')}`;

                const exportBtns = `
                                                                                                                        <div class="d-flex align-items-center gap-2">
                                                                                                                            <span style="font-size:.8rem;font-weight:700;color:#94a3b8;margin-right:8px;">${fmt(data.total)} kayıt</span>
                                                                                                                            <a href="${EXCEL_URL}?${p.toString()}" download="${fName}.xlsx" title="Excel olarak indir"
                                                                                                                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:12px;border:1px solid #dcfce7;background:linear-gradient(135deg,#f0fdf4,#dcfce7);color:#15803d;font-weight:700;font-size:.8rem;text-decoration:none;cursor:pointer;transition:all .2s;white-space:nowrap;"
                                                                                                                                onmouseover="this.style.background='linear-gradient(135deg,#10b981,#059669)';this.style.color='#fff';this.style.borderColor='transparent';"
                                                                                                                                onmouseout="this.style.background='linear-gradient(135deg,#f0fdf4,#dcfce7)';this.style.color='#15803d';this.style.borderColor='#dcfce7';">
                                                                                                                                <i class="fas fa-file-excel"></i> Excel
                                                                                                                            </a>
                                                                                                                            <a href="${PDF_URL}?${p.toString()}" download="${fName}.pdf" title="PDF olarak indir"
                                                                                                                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:12px;border:1px solid #fee2e2;background:linear-gradient(135deg,#fff1f2,#fee2e2);color:#dc2626;font-weight:700;font-size:.8rem;text-decoration:none;cursor:pointer;transition:all .2s;white-space:nowrap;"
                                                                                                                                onmouseover="this.style.background='linear-gradient(135deg,#ef4444,#dc2626)';this.style.color='#fff';this.style.borderColor='transparent';"
                                                                                                                                onmouseout="this.style.background='linear-gradient(135deg,#fff1f2,#fee2e2)';this.style.color='#dc2626';this.style.borderColor='#fee2e2';">
                                                                                                                                <i class="fas fa-file-pdf"></i> PDF
                                                                                                                            </a>
                                                                                                                        </div>`;

                document.getElementById('table-section').innerHTML = `<div class="glass-card">

                                                                                                                        <div class="table-responsive"><table class="table-pro"><thead><tr>
                                                                                                                             <th>Adres</th><th>Abone No</th><th>Dönem</th>
                                                                                                                            <th style="text-align:right;">Tüketim (kWh)</th><th style="text-align:right;">Tutar</th><th style="text-align:right;">İşlem</th>
                                                                                                                        </tr></thead><tbody>${rows}</tbody></table></div>${pager}</div>`;
            }

            /* ── Detail Modal ─────────────────────────────────────────────────────── */
            const detayKategoriler = {
                'Temel Bilgiler': [{ k: 'Tesisat No', f: 'tesisat_no' }, { k: 'Abone No', f: 'abone_tesis_no' }, { k: 'Fatura No', f: 'fatura_no' }, { k: 'İlçe', f: 'ilce' }, { k: 'Dönem', f: 'donem' }, { k: 'Adres', f: 'adres' }],
                'Okuma & Endeks': [{ k: 'İlk Okuma', f: 'ilk_okuma' }, { k: 'Son Okuma', f: 'son_okuma' }, { k: 'Çarpan', f: 'carpan' }, { k: 'T0 İlk', f: 't0_ilk_endeks' }, { k: 'T0 Son', f: 'to_son_endeks' }],
                'Tüketim Detayı (kWh)': [{ k: 'T1', f: 't1_tuketim' }, { k: 'T2', f: 't2_tuketim' }, { k: 'T3', f: 't3_tuketim' }, { k: 'Trafo Kaybı', f: 'trafo_kaybi_kwh' }, { k: 'Toplam', f: 'fatura_edilecek_toplam_tuketim_kwh' }],
                'Reaktif Bilgileri': [{ k: 'RI Son Endeks', f: 'ri_son_endeks' }, { k: 'RI Fark', f: 'ri_fark_endeks' }, { k: 'RC Son Endeks', f: 'rc_son_endeks' }, { k: 'RC Fark', f: 'rc_fark_endeks' }, { k: 'Reaktif Bedel', f: 'reaktif_tl' }],
                'Bedel & Tutar': [{ k: 'Birim Fiyat', f: 'birim_fiyat' }, { k: 'Aktif Tük. TL', f: 'aktif_tuketim_tl' }, { k: 'KDV', f: 'kdv' }, { k: 'Genel Toplam', f: 'tutar_toplam' }]
            };
            function showDetail(id) {
                const d = rData[id]; if (!d) return;
                document.getElementById('detTit').innerText = (d.fatura_no || '') + ' (' + (d.abone_tesis_no || d.tesisat_no || '') + ')';
                let h = '';
                for (const [kat, fields] of Object.entries(detayKategoriler)) {
                    h += `<div style="background:#fff;border-radius:20px;border:1px solid #f1f5f9;overflow:hidden;"><div style="background:#f8fafc;padding:12px 20px;font-size:.7rem;font-weight:800;color:#64748b;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">${kat}</div><div style="padding:10px 0;">`;
                    fields.forEach(f => {
                        let v = d[f.f] ?? '–';
                        if (v !== '–') {
                            if (f.f.includes('tutar') || f.f.includes('tl') || f.f === 'kdv') {
                                v = '₺' + parseFloat(v).toLocaleString('tr-TR', { minimumFractionDigits: 2 });
                            } else if (f.f.includes('okuma') || f.f.includes('tarih')) {
                                try {
                                    const dt = new Date(v);
                                    if (!isNaN(dt.getTime())) {
                                        v = dt.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                                    }
                                } catch(e) {}
                            }
                        }
                        h += `<div class="d-flex justify-content-between px-3 py-2" style="font-size:.85rem;border-bottom:1px dashed #f1f5f9;"><span style="color:#94a3b8;font-weight:600;">${f.k}</span><span style="color:#1e293b;font-weight:700;">${v}</span></div>`;
                    });
                    h += `</div></div>`;
                }
                document.getElementById('detGrid').innerHTML = h;
                const anom = d.payload?._tuketim_anomalileri || [];
                const aa = document.getElementById('anomaliAlert');
                if (anom.length) {
                    document.getElementById('anomaliList').innerHTML = anom.map(a => `<div style="background:rgba(255,255,255,.6);padding:12px 18px;border-radius:12px;border-left:4px solid #ef4444;"><div style="font-weight:800;color:#1e293b;font-size:.85rem;">${a.mesaj}</div><div style="color:#64748b;font-size:.75rem;">${a.detay}</div></div>`).join('');
                    aa.style.display = 'block';
                } else aa.style.display = 'none';
                document.getElementById('detMdl').style.display = 'flex';
            }
            function closeDetail() { document.getElementById('detMdl').style.display = 'none'; }

            /* ── Export Functions ────────────────────────────────────────────────── */
            function buildExportParams() {
                const p = new URLSearchParams();
                if (curYil) p.set('yil', curYil);
                if (curDonem) p.set('donem', curDonem);
                Object.keys(curFilters).forEach(k => { if (curFilters[k]) p.set(k, curFilters[k]); });
                return p;
            }

            async function exportToExcel(btn) {
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İndiriliyor...';
                btn.disabled = true;
                const p = buildExportParams();

                try {
                    const response = await fetch(`${EXCEL_URL}?${p.toString()}`);
                    if (!response.ok) throw new Error('Ağ hatası');
                    const blob = await response.blob();

                    let filename = "Odenen_Faturalar.xlsx";
                    const disposition = response.headers.get('content-disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                    }

                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } catch (e) {
                    console.error(e);
                    alert("Excel dosyası indirilirken bir hata oluştu.");
                } finally {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            }

            async function exportToPDF(btn) {
                if (!btn) return;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hazırlanıyor...';
                btn.disabled = true;

                // Yeni sekme aç ve Premium Progress Bar HTML'ini yerleştir
                const newTab = window.open('', '_blank');
                newTab.document.write(`<!DOCTYPE html>
                                <html>
                                <head>
                                    <meta charset="UTF-8">
                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
                                    <style>
                                        body { margin: 0; padding: 0; background: #f8fafc; font-family: 'Segoe UI', sans-serif; }
                                        .pm-backdrop {
                                          position:fixed;inset:0;z-index:99999;
                                          background:rgba(5,14,26,.86);
                                          backdrop-filter:blur(10px) saturate(1.4);
                                          display:flex;align-items:center;justify-content:center;padding:1rem;
                                          opacity:1;pointer-events:auto;
                                        }
                                        .pm-box{
                                          background:#fff;border-radius:24px;
                                          box-shadow:0 40px 100px rgba(0,0,0,.38),0 0 0 1px rgba(255,255,255,.05);
                                          width:100%;max-width:440px;overflow:hidden;
                                        }
                                        .pm-strip{width:100%;height:5px;background:rgba(26,95,138,.1);position:relative;}
                                        .pm-strip-fill{
                                          position:absolute;top:0;left:0;height:100%;width:0%;
                                          background:linear-gradient(90deg,#1a5f8a 0%,#3a9fd6 45%,#22c55e 100%);
                                          background-size:200% 100%;
                                          animation:strip-flow 2.2s linear infinite;
                                          transition:width .45s cubic-bezier(.4,0,.2,1);
                                          border-radius:0 3px 3px 0;
                                        }
                                        @keyframes strip-flow{0%{background-position:0% 0}100%{background-position:200% 0}}
                                        .pm-strip-dot{
                                          position:absolute;top:50%;left:0;
                                          width:12px;height:12px;border-radius:50%;
                                          background:#3a9fd6;transform:translateY(-50%);
                                          box-shadow:0 0 0 4px rgba(58,159,214,.25);
                                          transition:left .45s cubic-bezier(.4,0,.2,1);
                                          animation:dot-pulse 1.8s ease-in-out infinite;
                                        }
                                        @keyframes dot-pulse{
                                          0%,100%{box-shadow:0 0 0 4px rgba(58,159,214,.25);}
                                          50%{box-shadow:0 0 0 9px rgba(58,159,214,.08);}
                                        }
                                        .pm-body{padding:2.25rem 2rem 1.75rem;text-align:center;}
                                        .pm-icon-wrap{
                                          width:90px;height:90px;position:relative;
                                          margin:0 auto 1.5rem;
                                          display:flex;align-items:center;justify-content:center;
                                        }
                                        .pm-icon-ring{
                                          position:absolute;inset:0;border-radius:50%;
                                          border:2px solid rgba(26,95,138,.18);
                                          animation:ring-out 2.2s ease-out infinite;
                                        }
                                        .pm-ring2{animation-delay:1.1s;}
                                        @keyframes ring-out{
                                          0%{transform:scale(.82);opacity:0;}
                                          35%{opacity:1;}
                                          100%{transform:scale(1.6);opacity:0;}
                                        }
                                        .pm-icon{
                                          width:72px;height:72px;border-radius:50%;
                                          background:linear-gradient(135deg,#1a5f8a 0%,#3a9fd6 100%);
                                          display:flex;align-items:center;justify-content:center;
                                          font-size:1.75rem;color:#fff;position:relative;z-index:2;
                                          box-shadow:0 12px 36px rgba(26,95,138,.38);
                                          transition:background .55s ease,box-shadow .55s ease;
                                        }
                                        .pm-icon.pm-icon-proc{
                                          background:linear-gradient(135deg,#059669 0%,#10b981 100%);
                                          box-shadow:0 12px 36px rgba(5,150,105,.38);
                                        }
                                        .pm-icon.pm-icon-done{
                                          background:linear-gradient(135deg,#15803d 0%,#22c55e 100%);
                                          box-shadow:0 12px 36px rgba(21,128,61,.45);
                                          animation:done-pop .4s cubic-bezier(.34,1.56,.64,1) forwards;
                                        }
                                        @keyframes done-pop{0%{transform:scale(.8);}100%{transform:scale(1);}}
                                        .pm-title{font-size:1.25rem;font-weight:800;color:#1a2e3b;margin-bottom:.35rem;}
                                        .pm-sub{font-size:.84rem;color:#4a6a7a;font-weight:500;margin-bottom:1.75rem;min-height:2.2em;}
                                        .pm-track{
                                          background:rgba(26,95,138,.08);border-radius:40px;
                                          height:13px;width:100%;overflow:hidden;
                                          position:relative;margin-bottom:.7rem;
                                        }
                                        .pm-fill{
                                          height:100%;width:0%;
                                          background:linear-gradient(90deg,#1a5f8a 0%,#3a9fd6 50%,#22c55e 100%);
                                          background-size:200% 100%;border-radius:40px;
                                          transition:width .45s cubic-bezier(.4,0,.2,1);
                                          position:relative;overflow:hidden;
                                          animation:fill-flow 2.5s linear infinite;
                                        }
                                        @keyframes fill-flow{0%{background-position:0% 0}100%{background-position:200% 0}}
                                        .pm-shine{
                                          position:absolute;inset:0;
                                          background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.38) 50%,transparent 100%);
                                          transform:translateX(-100%);
                                          animation:shine-sweep 2s ease-in-out infinite;
                                        }
                                        @keyframes shine-sweep{0%{transform:translateX(-100%);}100%{transform:translateX(300%);}}
                                        .pm-perc-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}
                                        .pm-perc{font-size:1.6rem;font-weight:900;color:#1a5f8a;letter-spacing:-1px;transition:color .4s;}
                                        .pm-hint{font-size:.68rem;color:#90aab8;font-weight:500;}
                                        .pm-steps{display:flex;align-items:center;justify-content:center;}
                                        .pm-step{
                                          display:flex;flex-direction:column;align-items:center;
                                          gap:5px;font-size:.65rem;font-weight:600;color:#90aab8;
                                          transition:color .4s;min-width:76px;
                                        }
                                        .pm-step-dot{
                                          width:10px;height:10px;border-radius:50%;
                                          background:#dde8f0;border:2px solid #dde8f0;
                                          transition:all .4s cubic-bezier(.34,1.38,.64,1);
                                        }
                                        .pm-step.pm-step-active{color:#1a5f8a;}
                                        .pm-step.pm-step-active .pm-step-dot{
                                          background:#3a9fd6;border-color:#3a9fd6;
                                          box-shadow:0 0 0 5px rgba(58,159,214,.2);
                                          transform:scale(1.35);
                                        }
                                        .pm-step.pm-step-done{color:#15803d;}
                                        .pm-step.pm-step-done .pm-step-dot{
                                          background:#22c55e;border-color:#22c55e;
                                          box-shadow:0 0 0 5px rgba(34,197,94,.2);
                                        }
                                        .pm-step-line{
                                          flex:1;height:2px;background:#e2ecf3;
                                          margin-bottom:18px;max-width:48px;
                                          position:relative;overflow:hidden;
                                        }
                                        .pm-step-line::after{
                                          content:'';position:absolute;inset:0;
                                          background:linear-gradient(90deg,#3a9fd6,#22c55e);
                                          transform:translateX(-100%);transition:transform .6s ease;
                                        }
                                        .pm-step-line.pm-line-done::after{transform:translateX(0);}
                                    </style>
                                </head>
                                <body>
                                    <div class="pm-backdrop">
                                      <div class="pm-box">
                                        <div class="pm-strip">
                                          <div class="pm-strip-fill" id="pmStripFill"></div>
                                          <div class="pm-strip-dot" id="pmStripDot"></div>
                                        </div>
                                        <div class="pm-body">
                                          <div class="pm-icon-wrap">
                                            <div class="pm-icon-ring"></div>
                                            <div class="pm-icon-ring pm-ring2"></div>
                                            <div class="pm-icon" id="pmIcon"><i class="fas fa-file-pdf"></i></div>
                                          </div>
                                          <div class="pm-title" id="pmTitle">PDF Hazırlanıyor</div>
                                          <div class="pm-sub" id="pmSub">Sunucuda veriler derleniyor...</div>
                                          <div class="pm-track">
                                            <div class="pm-fill" id="pmFill"><div class="pm-shine"></div></div>
                                          </div>
                                          <div class="pm-perc-row">
                                            <span class="pm-perc" id="pmPerc">0%</span>
                                            <span class="pm-hint">Lütfen bu sekmeyi kapatmayın</span>
                                          </div>
                                          <div class="pm-steps">
                                            <div class="pm-step pm-step-active" id="pmStep1">
                                              <div class="pm-step-dot"></div><span>Veri İşleme</span>
                                            </div>
                                            <div class="pm-step-line" id="pmLine1"></div>
                                            <div class="pm-step" id="pmStep2">
                                              <div class="pm-step-dot"></div><span>PDF Çizimi</span>
                                            </div>
                                            <div class="pm-step-line" id="pmLine2"></div>
                                            <div class="pm-step" id="pmStep3">
                                              <div class="pm-step-dot"></div><span>Tamamlandı</span>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                </body>
                                </html>`);

                const pmFill = newTab.document.getElementById('pmFill');
                const pmStripFill = newTab.document.getElementById('pmStripFill');
                const pmStripDot = newTab.document.getElementById('pmStripDot');
                const pmPerc = newTab.document.getElementById('pmPerc');
                const pmIcon = newTab.document.getElementById('pmIcon');
                const pmTitle = newTab.document.getElementById('pmTitle');
                const pmSub = newTab.document.getElementById('pmSub');
                const pmStep1 = newTab.document.getElementById('pmStep1');
                const pmStep2 = newTab.document.getElementById('pmStep2');
                const pmStep3 = newTab.document.getElementById('pmStep3');
                const pmLine1 = newTab.document.getElementById('pmLine1');
                const pmLine2 = newTab.document.getElementById('pmLine2');

                function updateProgressUI(pRound) {
                    if (pmFill) pmFill.style.width = pRound + '%';
                    if (pmStripFill) pmStripFill.style.width = pRound + '%';
                    if (pmStripDot) pmStripDot.style.left = 'calc(' + pRound + '% - 6px)';
                    if (pmPerc) pmPerc.textContent = pRound + '%';

                    if (pRound >= 100) { if (pmPerc) pmPerc.style.color = '#15803d'; }
                    else if (pRound >= 60) { if (pmPerc) pmPerc.style.color = '#059669'; }
                    else { if (pmPerc) pmPerc.style.color = '#1a5f8a'; }
                }

                function setPhase(phase) {
                    if (phase === 'process') {
                        if (pmStep1) pmStep1.className = 'pm-step pm-step-done';
                        if (pmStep2) pmStep2.className = 'pm-step pm-step-active';
                        if (pmStep3) pmStep3.className = 'pm-step';
                        if (pmLine1) pmLine1.classList.add('pm-line-done');
                        if (pmLine2) pmLine2.classList.remove('pm-line-done');
                        if (pmIcon) pmIcon.className = 'pm-icon pm-icon-proc';
                        if (pmIcon) pmIcon.innerHTML = '<i class="fas fa-cog fa-spin"></i>';
                        if (pmTitle) pmTitle.textContent = 'Veriler İşleniyor';
                        if (pmSub) pmSub.textContent = 'PDF dosyası oluşturuluyor...';
                    } else if (phase === 'done') {
                        if (pmStep1) pmStep1.className = 'pm-step pm-step-done';
                        if (pmStep2) pmStep2.className = 'pm-step pm-step-done';
                        if (pmStep3) pmStep3.className = 'pm-step pm-step-active';
                        if (pmLine1) pmLine1.classList.add('pm-line-done');
                        if (pmLine2) pmLine2.classList.add('pm-line-done');
                        if (pmIcon) pmIcon.className = 'pm-icon pm-icon-done';
                        if (pmIcon) pmIcon.innerHTML = '<i class="fas fa-check"></i>';
                        if (pmTitle) pmTitle.textContent = 'İşlem Tamamlandı';
                        if (pmSub) pmSub.textContent = 'Belge hazırlandı, açılıyor...';
                    }
                }

                // 1. Aşama: Akıllı Simülasyon (0'dan 85'e kadar yavaşça artar, sunucu PDF'i çizerken bekleme süresi)
                let simProgress = 0;
                const simInterval = setInterval(() => {
                    if (simProgress < 85) {
                        simProgress += (85 - simProgress) * 0.05; // Gittikçe yavaşlayan doğal artış
                        if (simProgress > 84) simProgress = 85;
                        const val = Math.round(simProgress);
                        updateProgressUI(val);
                        if (val > 40) setPhase('process');
                    }
                }, 300);

                const p = buildExportParams();
                p.set('preview', '1');

                try {
                    const response = await fetch(`${PDF_URL}?${p.toString()}`);
                    if (!response.ok) throw new Error('Ağ hatası');

                    clearInterval(simInterval); // Sunucu işlemi bitti, simülasyonu durdur

                    // 2. Aşama: Gerçek İndirme (Download) İlerlemesi (85'ten 100'e stream ile okuma)
                    const contentLength = response.headers.get('content-length');
                    const total = contentLength ? parseInt(contentLength, 10) : 0;

                    const reader = response.body.getReader();
                    const chunks = [];
                    let loaded = 0;

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;
                        chunks.push(value);
                        loaded += value.length;

                        if (total) {
                            // Kalan %15'lik kısmı gerçek inen byte yüzdesiyle doldur
                            const realPercent = (loaded / total);
                            const finalVal = Math.round(85 + (realPercent * 15));
                            updateProgressUI(finalVal);
                        } else {
                            // Eğer sunucu Content-Length yollamazsa yavaşça %98'e kadar ilerlet
                            let currentVal = parseInt(pmPerc ? pmPerc.innerText.replace('%', '') : '85');
                            if (currentVal < 98) {
                                currentVal += 1;
                                updateProgressUI(currentVal);
                            }
                        }
                    }

                    // Tamamen bitti
                    updateProgressUI(100);
                    setPhase('done');

                    // Kullanıcının %100'ü ve tamamlandı simgesini görmesi için ufak bekleme
                    await new Promise(r => setTimeout(r, 800));

                    const blob = new Blob(chunks, { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);

                    // Açtığımız boş sekmeyi nihai PDF URL'si ile güncelliyoruz
                    newTab.location.href = url;
                } catch (e) {
                    console.error(e);
                    clearInterval(simInterval);
                    newTab.close();
                    alert("PDF dosyası oluşturulurken bir hata oluştu.");
                } finally {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            }

            /* ── Scroll Arrows ───────────────────────────────────────────────────── */
            function scrollCards(id, amt) { const el = document.getElementById(id); if (el) el.scrollBy({ left: amt, behavior: 'smooth' }); }
            function addScrollArrows(id) {
                const wrapper = document.getElementById(id); if (!wrapper) return;
                const host = wrapper.closest('.scroll-host'); if (!host || host.querySelector('.scroll-arrow')) return;
                ['left', 'right'].forEach(dir => {
                    const btn = document.createElement('button');
                    btn.className = `scroll-arrow scroll-arrow-${dir}`; btn.setAttribute('aria-label', dir === 'left' ? 'Sola' : 'Sağa');
                    btn.innerHTML = `<i class="fas fa-chevron-${dir}"></i>`;
                    btn.onclick = () => scrollCards(id, dir === 'left' ? -320 : 320);
                    host.appendChild(btn);
                });
                const upd = () => {
                    const l = host.querySelector('.scroll-arrow-left'); const r = host.querySelector('.scroll-arrow-right');
                    if (l) l.style.opacity = wrapper.scrollLeft > 0 ? '1' : '0.35';
                    if (r) r.style.opacity = (wrapper.scrollLeft + wrapper.clientWidth < wrapper.scrollWidth - 4) ? '1' : '0.35';
                };
                wrapper.addEventListener('scroll', upd); upd();
            }

            /* ── Filter Modal Helpers ────────────────────────────────────────────── */
            function filterPeriods(yil) {
                const sel = document.getElementById('filterDonem'); if (!sel) return;
                let first = null, vis = false;
                Array.from(sel.options).forEach(o => {
                    if (!o.value) return;
                    const show = !yil || o.getAttribute('data-year') === yil;
                    o.style.display = show ? '' : 'none'; o.disabled = !show;
                    if (show && !first) first = o;
                    if (show && o.selected) vis = true;
                });
                if (!vis && sel.value) sel.value = first ? first.value : '';
            }

            async function applyFilters(e) {
                if (e) e.preventDefault();
                const form = document.getElementById('filterForm');
                const formData = new FormData(form);

                // Reset and read all filter values first
                const newYil = formData.get('yil') || null;
                const newDonem = formData.get('donem') || null;
                curFilters = {};
                formData.forEach((value, key) => {
                    if (value && key !== 'yil' && key !== 'donem') curFilters[key] = value;
                });

                // Commit state atomically
                curYil = newYil;
                curDonem = newDonem;

                // Update year card UI
                document.querySelectorAll('.yil-tab').forEach(c => {
                    const active = c.dataset.yil === curYil;
                    c.classList.toggle('active', active);
                    c.style.color = active ? '#fff' : '#64748b';
                    c.style.background = active ? 'linear-gradient(135deg, #3b82f6, #4f46e5)' : '#f1f5f9';
                    c.style.boxShadow = active ? '0 10px 20px -5px rgba(59, 130, 246, 0.4)' : 'none';
                    c.style.transform = active ? 'translateY(-2px)' : 'translateY(0)';
                });

                document.getElementById('no-year-hint').style.display = curYil ? 'none' : 'block';
                document.getElementById('filterMdl').style.display = 'none';
                updateAnalysisBtn();

                if (curYil) {
                    // Load period cards, then highlight selected period and load table
                    await loadDonemler(curYil);
                    if (curDonem) {
                        document.querySelectorAll('[data-donem]').forEach(c =>
                            c.classList.toggle('active', c.dataset.donem === curDonem)
                        );
                    }
                    loadTable(1);
                } else if (Object.keys(curFilters).length > 0) {
                    // No year selected but other filters exist — load table directly
                    loadTable(1);
                }
            }

            async function runAnalysis() {
                const btn = document.getElementById('analysisBtn');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analiz Yapılıyor...';

                const p = new URLSearchParams();
                if (curYil) p.set('yil', curYil);
                if (curDonem) p.set('donem', curDonem);
                Object.keys(curFilters).forEach(k => { if (curFilters[k]) p.set(k, curFilters[k]); });

                try {
                    const res = await fetch('{{ route("fatura.odenenler.analiz") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: p.toString()
                    });
                    const data = await res.json();
                    alert(data.message || 'Analiz tamamlandı.');
                    loadTable(1);
                } catch (e) {
                    console.error('Analysis error:', e);
                    alert('Analiz sırasında bir hata oluştu.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }

            function updateAnalysisBtn() {
                const btn = document.getElementById('analysisBtn');
                if (!btn) return;
                const hasFilter = curYil || curDonem || Object.keys(curFilters).length > 0;
                btn.style.display = hasFilter ? 'inline-flex' : 'none';
            }

            /* ── Global scope aliases for inline onclick handlers ─────────────────── */
            window.selectYil = selectYil;
            window.selectDonem = selectDonem;
            window.closePeriodModal = closePeriodModal;
            window.loadTable = loadTable;
            window.showDetail = showDetail;
            window.closeDetail = closeDetail;
            window.exportToExcel = exportToExcel;
            window.exportToPDF = exportToPDF;
            window.buildExportParams = buildExportParams;

            /* ── Init ────────────────────────────────────────────────────────────── */
            document.addEventListener('DOMContentLoaded', () => {
                const urlY = new URLSearchParams(window.location.search).get('yil');
                const urlD = new URLSearchParams(window.location.search).get('donem');
                if (urlY) {
                    document.querySelectorAll('.yil-tab').forEach(c => {
                        if (c.dataset.yil === urlY) {
                            c.classList.add('active');
                            c.style.color = '#fff';
                            c.style.background = 'linear-gradient(135deg, #3b82f6, #4f46e5)';
                            c.style.boxShadow = '0 10px 20px -5px rgba(59, 130, 246, 0.4)';
                            c.style.transform = 'translateY(-2px)';
                        }
                    });
                    curYil = urlY;
                    document.getElementById('no-year-hint').style.display = 'none';
                    if (urlD) {
                        curDonem = urlD;
                        loadDonemler(urlY).then(() => { curDonem = urlD; loadTable(1); updateAnalysisBtn(); });
                    } else {
                        loadDonemler(urlY);
                        updateAnalysisBtn();
                    }
                }
            });
        </script>
    @endpush
@endsection