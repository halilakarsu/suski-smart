@extends('frontend.layouts.app')

@section('content')
    <style>
        /* Ultra-Premium Glassmorphic Design for Subscribers Management */
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

        .pg-premium {
            background-color: var(--bg-main) !important;
            min-height: 100vh;
            padding-bottom: 4rem;
        }

        /* Hero Section */
        .page-hero {
            background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
            position: relative;
            padding: 4rem 2rem 8rem 2rem;
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
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            top: -150px;
            right: -100px;
            border-radius: 50%;
            opacity: 0.5;
            filter: blur(60px);
            animation: pulseSlow 10s infinite alternate;
            pointer-events: none;
        }

        @keyframes pulseSlow {
            0% {
                transform: scale(1);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.1);
                opacity: 0.6;
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

        .hero-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            font-weight: 500;
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

        /* Tabs Design */
        .premium-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .tab-item-pro {
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.9rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: var(--text-slate-500);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            text-decoration: none !important;
        }

        .tab-item-pro:hover {
            background: #f8fafc;
            color: var(--text-slate-900);
        }

        .tab-item-pro.active {
            background: var(--primary-gradient);
            color: white !important;
            border-color: transparent;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
        }

        .tab-count-pro {
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        .tab-item-pro.active .tab-count-pro {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Güncellemeler Dropdown */
        .tab-dropdown-wrap {
            position: relative;
            display: inline-block;
        }

        .tab-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 260px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.12);
            z-index: 999;
            padding: 8px;
            display: none;
            flex-direction: column;
            gap: 4px;
        }

        .tab-dropdown-wrap:hover .tab-dropdown-menu,
        .tab-dropdown-wrap.open .tab-dropdown-menu {
            display: flex;
        }

        .tab-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--text-slate-500);
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .tab-dropdown-item:hover {
            background: #f8fafc;
            color: var(--text-slate-900);
        }

        .tab-dropdown-item.active {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff !important;
        }

        .tab-dropdown-item.active .tab-count-pro {
            background: rgba(255, 255, 255, 0.25);
        }

        .tab-dropdown-item .tab-count-pro {
            background: #f1f5f9;
            color: #64748b;
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
            transform: scale(1.002);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
        }

        /* Badges */
        .badge-pro {
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .badge-orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .badge-slate {
            background: #f1f5f9;
            color: #475569;
        }

        /* Action Buttons */
        .action-btn-pro {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none !important;
        }

        .ab-blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .ab-blue:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-2px);
        }

        .ab-orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .ab-orange:hover {
            background: #ea580c;
            color: #fff;
            transform: translateY(-2px);
        }

        .ab-red {
            background: #fef2f2;
            color: #dc2626;
        }

        .ab-red:hover {
            background: #dc2626;
            color: #fff;
            transform: translateY(-2px);
        }

        .ab-green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .ab-green:hover {
            background: #16a34a;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Main Buttons */
        .btn-pro {
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none !important;
        }

        .btn-primary-pro {
            background: var(--primary-gradient);
            color: white !important;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
        }

        .btn-primary-pro:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4);
        }

        .btn-outline-pro {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: var(--text-slate-500);
        }

        .btn-outline-pro:hover {
            background: #f8fafc;
            color: var(--text-slate-900);
            border-color: #cbd5e1;
        }

        /* Form Elements */
        .form-control-pro {
            width: 100%;
            padding: 12px 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-slate-900);
            transition: all 0.2s;
            outline: none;
        }

        .form-control-pro:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Drawer Styling */
        .drawer-pro {
            position: fixed;
            top: 0;
            right: -500px;
            width: 500px;
            height: 100vh;
            background: #fff;
            box-shadow: -20px 0 60px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .drawer-pro.open {
            right: 0;
        }

        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 9998;
            display: none;
        }

        .drawer-overlay.open {
            display: block;
        }

        .drawer-header {
            padding: 30px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-title {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--text-slate-900);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .drawer-body {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        .drawer-footer {
            padding: 25px 30px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
    </style>

    <div class="pg-premium p-0">
        <!-- HERO SECTION -->
        <div class="page-hero">
            <div class="hero-container">
                <div class="hero-title-group">
                    <h1 class="hero-title">Abone Yönetimi</h1>
                    <p class="hero-subtitle">Sistemdeki tüm abone kayıtlarını detaylı yönetin ve takip edin.</p>
                </div>
                <div>
                    @can('manage_aboneler')
                        <button type="button" onclick="openCreateSidebar()" class="btn-pro btn-primary-pro"
                            style="padding: 14px 28px;">
                            <i class="fas fa-plus-circle"></i> Yeni Abone Kaydı
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        <div class="main-container">
            <!-- TABS AREA -->
            <div class="premium-tabs">
                {{-- Abone Durumu Dropdown --}}
                <div class="tab-dropdown-wrap" id="statusDropdown">
                    <button type="button" class="tab-item-pro {{ in_array(request('tab'), ['all', 'passive', 'total_all', '']) ? 'active' : '' }}" onclick="toggleStatusDropdown()">
                        @if(request('tab') == 'passive')
                            <i class="fas fa-user-slash"></i> Pasif Aboneler
                        @elseif(request('tab') == 'total_all')
                            <i class="fas fa-users-cog"></i> Tüm Aboneler
                        @else
                            <i class="fas fa-users"></i> Aktif Aboneler
                        @endif
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; opacity: 0.7; margin-left: 5px;"></i>
                    </button>
                    <div class="tab-dropdown-menu" style="min-width: 200px;">
                        <a href="{{ route('aboneler.index', ['tab' => 'total_all'] + request()->except('tab', 'page')) }}"
                            class="tab-dropdown-item {{ request('tab') == 'total_all' ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i> Tüm Aboneler (Aktif+Pasif)
                        </a>
                        <a href="{{ route('aboneler.index', ['tab' => 'all'] + request()->except('tab', 'page')) }}"
                            class="tab-dropdown-item {{ (request('tab') == 'all' || !request('tab') || request('tab') == '') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Sadece Aktif Aboneler
                            <span class="tab-count-pro" style="margin-left:auto;">{{ $totalCount }}</span>
                        </a>
                        <a href="{{ route('aboneler.index', ['tab' => 'passive'] + request()->except('tab', 'page')) }}"
                            class="tab-dropdown-item {{ request('tab') == 'passive' ? 'active' : '' }}">
                            <i class="fas fa-user-slash"></i> Sadece Pasif Aboneler
                            <span class="tab-count-pro" style="margin-left:auto;">{{ $passiveCount }}</span>
                        </a>
                    </div>
                </div>


                {{-- Yerleşim Türü Dropdown --}}
                <div class="tab-dropdown-wrap" id="yerlesimDropdown">
                    <button type="button" class="tab-item-pro {{ in_array(request('yerlesim'), ['koy', 'merkez', 'all', '']) ? 'active' : '' }}" onclick="toggleYerlesimDropdown()">
                        @if(request('yerlesim') == 'koy')
                            <i class="fas fa-tree"></i> Köy
                        @elseif(request('yerlesim') == 'merkez')
                            <i class="fas fa-city"></i> Merkez
                        @else
                            <i class="fas fa-layer-group"></i> Yerleşim: Tümü
                        @endif
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem; opacity: 0.7; margin-left: 5px;"></i>
                    </button>
                    <div class="tab-dropdown-menu" style="min-width: 200px;">
                        <a href="{{ route('aboneler.index', ['yerlesim' => 'all'] + request()->except('yerlesim', 'page')) }}"
                            class="tab-dropdown-item {{ (request('yerlesim') == 'all' || !request('yerlesim') || request('yerlesim') == '') ? 'active' : '' }}">
                            <i class="fas fa-layer-group"></i> Tümü
                        </a>
                        <a href="{{ route('aboneler.index', ['yerlesim' => 'merkez'] + request()->except('yerlesim', 'page')) }}"
                            class="tab-dropdown-item {{ request('yerlesim') == 'merkez' ? 'active' : '' }}">
                            <i class="fas fa-city" style="color: #0284c7;"></i> Merkez
                            <span class="tab-count-pro" style="margin-left:auto;">{{ $merkezCount ?? 0 }}</span>
                        </a>
                        <a href="{{ route('aboneler.index', ['yerlesim' => 'koy'] + request()->except('yerlesim', 'page')) }}"
                            class="tab-dropdown-item {{ request('yerlesim') == 'koy' ? 'active' : '' }}">
                            <i class="fas fa-tree" style="color: #16a34a;"></i> Köy
                            <span class="tab-count-pro" style="margin-left:auto;">{{ $koyCount ?? 0 }}</span>
                        </a>
                    </div>
                </div>


            </div>

            <div id="subscriberAjaxContainer">
                <!-- FILTER CARD -->
                <div class="glass-card" style="padding: 20px 30px;">
                <form action="{{ route('aboneler.index') }}" method="GET" id="filterForm" onsubmit="return false;">
                    <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                    <input type="hidden" name="yerlesim" value="{{ request('yerlesim', 'all') }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-0 position-relative">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Akıllı
                                    Arama (Otomatik)</label>
                                <div class="position-relative">
                                    <input type="text" name="search" id="autoSearchInput" class="form-control-pro" value="{{ request('search') }}"
                                        placeholder="Tesisat No, Sayaç Seri No veya Ünvan yazın..." autocomplete="off"
                                        style="padding-left: 45px; height: 60px; font-size: 1.1rem; border-radius: 20px;">
                                    <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.2rem;"></i>
                                    <div id="searchLoader" style="display: none; position: absolute; right: 20px; top: 50%; transform: translateY(-50%);">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if(request('tab') == 'passive')
                <div class="alert alert-warning border-0"
                    style="background: #fffbeb; border-radius: 16px; padding: 15px 25px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-info-circle text-warning" style="font-size: 1.25rem;"></i>
                    <span style="font-weight: 600; color: #92400e;">Pasif aboneler raporlarda filtrelenebilir ancak aktif
                        işlemlere dahil edilmezler.</span>
                </div>
            @endif

            <!-- MAIN CONTENT CARD -->
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title-pro mb-0">
                        <i class="fas fa-table"></i>
                        @if(request('tab') == 'new') Yeni Eklenen Aboneler
                        @elseif(request('tab') == 'passive') Pasif Aboneler

                        @elseif(request('tab') == 'koy') <i class="fas fa-tree" style="color:#16a34a;"></i> Köy Aboneleri
                        @elseif(request('tab') == 'merkez') <i class="fas fa-city" style="color:#0284c7;"></i> Merkez Aboneleri
                        @else Abone Kayıtları @endif
                    </h5>

                    @if(request('tab') == 'passive')
                        <a href="{{ route('aboneler.sync-passive') }}" class="btn-pro btn-primary-pro"
                            style="background: #ea580c; border: none;">
                            <i class="fas fa-sync-alt"></i> Pasif Analizi Başlat
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table-pro">
                        <thead>
                            <tr>
                                @if(in_array(request('tab'), ['new', 'sayac_guncelleme', 'bilgi_guncelleme']))
                                    <th style="width: 40px;"><input type="checkbox" id="selectAll"
                                            style="width:18px; height:18px;"></th>
                                @endif
                                <th style="width: 60px;">#</th>
                                <th>Bölge Koordinasyonu</th>
                                @if($hasBolgeKodu ?? false)
                                <th style="width: 120px;">Bölge Kodu</th> @endif
                                <th style="width: 120px;">Tesisat No</th>
                                <th>Abone Grubu / Tarife</th>
                                <th>Adres / Detay</th>
                                @if(request('tab') == 'passive')
                                    <th>Pasiflik Nedeni</th>
                                @endif
                                <th style="width: 160px; text-align: right;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aboneler as $abone)
                                @php
                                    $iter = $loop->iteration + ($aboneler->currentPage() - 1) * $aboneler->perPage();
                                    $isPassive = ($hasIsActive ?? false) && !$abone->is_active;
                                @endphp
                                <tr id="row-{{ $abone->id }}" style="{{ $isPassive ? 'opacity: 0.6;' : '' }}">
                                    @if(in_array(request('tab'), ['new', 'sayac_guncelleme', 'bilgi_guncelleme']))
                                        <td><input type="checkbox" class="sub-checkbox" value="{{ $abone->id }}"
                                                style="width:18px; height:18px;"></td>
                                    @endif
                                    <td style="font-weight: 700; color: #94a3b8;">{{ str_pad($iter, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td><span class="badge-pro badge-blue"><i class="fas fa-map-marker-alt mr-2"></i>
                                            {{ $abone->bolge->bolge_adi ?? ($abone->BOLGE_ADI ?? 'Tanımsız') }}</span></td>
                                    @if($hasBolgeKodu ?? false)
                                        <td><code
                                                style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; color: #475569;">{{ $abone->BOLGE_KODU ?? '—' }}</code>
                                        </td>
                                    @endif
                                    <td><strong
                                            style="color: #2563eb; font-family: monospace; font-size: 1.1rem;">{{ $abone->ABONE_TESIS_NO }}</strong>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">{{ $abone->abone_grubu ?? '—' }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">{{ $abone->tarife ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; color: var(--text-slate-900);"
                                            title="{{ $abone->ADRES }}">
                                            {{ $abone->ADRES ?? '—' }}
                                        </div>
                                        @if($abone->yerlesim_turu == 'KÖY')
                                            <span
                                                style="display:inline-block; margin-top:4px; background:#f0fdf4; color:#15803d; font-size:0.7rem; font-weight:800; padding:2px 8px; border-radius:6px; letter-spacing:0.04em;">
                                                <i class="fas fa-tree"></i> KÖY
                                            </span>
                                        @elseif($abone->yerlesim_turu == 'MERKEZ')
                                            <span
                                                style="display:inline-block; margin-top:4px; background:#eff6ff; color:#1d4ed8; font-size:0.7rem; font-weight:800; padding:2px 8px; border-radius:6px; letter-spacing:0.04em;">
                                                <i class="fas fa-city"></i> MERKEZ
                                            </span>
                                        @endif
                                    </td>
                                    @if(request('tab') == 'passive')
                                        <td>
                                            @if($abone->passive_reason)
                                                <span class="badge-pro"
                                                    style="background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; padding: 8px 12px; border-radius: 12px; font-size: 0.8rem;">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ $abone->passive_reason }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size: 0.85rem;">Manuel pasife çekildi</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('aboneler.show', $abone->id) }}" class="action-btn-pro ab-blue"
                                                title="Görüntüle"><i class="fas fa-eye"></i></a>
                                            <button type="button" class="action-btn-pro ab-orange" title="Düzenle"
                                                onclick="openEditModal({{ $abone->toJson() }})"><i
                                                    class="fas fa-pen"></i></button>

                                            @if((($hasIsNew ?? false) && $abone->is_new) || (($hasIsUpdated ?? false) && $abone->is_updated))
                                                <button type="button" class="action-btn-pro ab-green" title="Onayla"
                                                    onclick="markAsOld({{ $abone->id }})"><i class="fas fa-check"></i></button>
                                            @endif

                                            @if($hasIsActive ?? false)
                                                @can('manage_aboneler')
                                                    @if($abone->is_active)
                                                        <button type="button" class="action-btn-pro ab-red"
                                                            style="background:#f1f5f9; color:#64748b;" title="Pasif Yap"
                                                            onclick="toggleActive({{ $abone->id }}, false)"><i
                                                                class="fas fa-user-slash"></i></button>
                                                    @else
                                                        <button type="button" class="action-btn-pro ab-green" title="Aktif Yap"
                                                            onclick="toggleActive({{ $abone->id }}, true)"><i
                                                                class="fas fa-user-check"></i></button>
                                                    @endif
                                                @endcan
                                            @endif
                                            <button type="button" class="action-btn-pro ab-red" title="Sil"
                                                onclick="confirmDelete('{{ $abone->id }}')"><i
                                                    class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center" style="padding: 60px;">
                                        <i class="fas fa-folder-open mb-3"
                                            style="font-size: 3rem; color: #e2e8f0; display: block;"></i>
                                        <p style="color: #94a3b8; font-weight: 600;">Aradığınız kriterlerde abone kaydı
                                            bulunamadı.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($aboneler->hasPages())
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                        {{ $aboneler->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- CREATE DRAWER -->
    <div class="drawer-overlay" id="createOverlay" onclick="closeCreateSidebar()"></div>
    <div class="drawer-pro" id="createSidebar">
        <div class="drawer-header">
            <h5 class="drawer-title"><i class="fas fa-user-plus" style="color:#16a34a;"></i> Yeni Abone Kaydı</h5>
            <button type="button" class="btn-pro btn-outline-pro" onclick="closeCreateSidebar()"
                style="padding: 8px; width:36px; height:36px;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('aboneler.store') }}" method="POST">
            @csrf
            <div class="drawer-body">
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius:12px; font-size:0.85rem;">
                        <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @endif

                <div class="form-group mb-4">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Tesisat
                        / Abone No *</label>
                    <input type="text" name="ABONE_TESIS_NO" class="form-control-pro" required
                        placeholder="Eşsiz tesisat numarası">
                </div>
                <div class="form-group mb-4">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Abone
                        Ünvanı</label>
                    <input type="text" name="UNVAN" class="form-control-pro" placeholder="İsim veya Şirket Ünvanı">
                </div>
                <div class="form-group mb-4">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Bölge
                        Koordinasyonu (İlçe) *</label>
                    <select name="BOLGE_ADI" class="form-control-pro" required>
                        <option value="">Seçiniz...</option>
                        @foreach($bolgeler as $b)
                            <option value="{{ $b->bolge_adi }}">{{ $b->bolge_adi }} ({{ $b->bolge_kodu }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Sayaç
                        Seri No</label>
                    <input type="text" name="SAYAC_SERI_NO" class="form-control-pro" placeholder="Seri Numarası">
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label
                            style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Bağlantı
                            Grubu</label>
                        <select name="baglanti_grubu" class="form-control-pro">
                            <option value="">Seçiniz...</option>
                            <option value="AG">AG (Alçak Gerilim)</option>
                            <option value="OG">OG (Orta Gerilim)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label
                            style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Hesap
                            Adı</label>
                        <input type="text" name="hesap_adi" class="form-control-pro" placeholder="Örn: Serbest Tüketici">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label
                            style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Abone
                            Grubu</label>
                        <input type="text" name="abone_grubu" class="form-control-pro" placeholder="Örn: Mesken, Sanayi">
                    </div>
                    <div class="col-md-6">
                        <label
                            style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Tarife</label>
                        <input type="text" name="tarife" class="form-control-pro" placeholder="Tarife kodu">
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Açık
                        Adres</label>
                    <textarea name="ADRES" class="form-control-pro" rows="2" placeholder="Tam adres bilgisi..."></textarea>
                </div>
                <div class="form-group">
                    <label
                        style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Özel
                        Notlar</label>
                    <textarea name="notlar" class="form-control-pro" rows="2"
                        placeholder="Abone hakkında notlar..."></textarea>
                </div>
            </div>
            <div class="drawer-footer">
                <button type="button" class="btn-pro btn-outline-pro" onclick="closeCreateSidebar()">İptal</button>
                <button type="submit" class="btn-pro btn-primary-pro">Kaydı Tamamla</button>
            </div>
        </form>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius:28px; border:none; box-shadow:0 25px 50px rgba(0,0,0,0.15);">
                <div class="modal-header" style="padding:25px 30px; border-bottom:1px solid #f1f5f9;">
                    <h5 class="modal-title" style="font-weight:800; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-user-edit" style="color:#2563eb;"></i> Abone Profili Güncelle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" style="padding:30px;">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Tesisat
                                    No (Sabit)</label>
                                <input type="text" id="m_tesisat" class="form-control-pro" readonly
                                    style="background:#f8fafc; color:#94a3b8;">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Bölge
                                    Koordinasyonu (İlçe)</label>
                                <select name="BOLGE_ADI" id="m_bolge" class="form-control-pro">
                                    <option value="">Seçiniz...</option>
                                    @foreach($bolgeler as $b)
                                        <option value="{{ $b->bolge_adi }}">{{ $b->bolge_adi }} ({{ $b->bolge_kodu }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Sayaç
                                    Seri No</label>
                                <input type="text" name="SAYAC_SERI_NO" id="m_sayac" class="form-control-pro">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Bağlantı
                                    Grubu</label>
                                <select name="baglanti_grubu" id="m_baglanti_grubu" class="form-control-pro">
                                    <option value="">Seçiniz...</option>
                                    <option value="AG">AG (Alçak Gerilim)</option>
                                    <option value="OG">OG (Orta Gerilim)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Hesap
                                    Adı</label>
                                <input type="text" name="hesap_adi" id="m_hesap_adi" class="form-control-pro">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Abone
                                    Grubu</label>
                                <input type="text" name="abone_grubu" id="m_abone_grubu" class="form-control-pro">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Tarife</label>
                                <input type="text" name="tarife" id="m_tarife" class="form-control-pro">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                    <i class="fas fa-map-pin" style="color:#16a34a;"></i> Yerleşim Türü
                                </label>
                                <select name="yerlesim_turu" id="m_yerlesim_turu" class="form-control-pro">
                                    <option value="MERKEZ">Merkez</option>
                                    <option value="KÖY">Köy</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Adres
                                    Bilgisi</label>
                                <textarea name="ADRES" id="m_adres" class="form-control-pro" rows="2"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">Özel
                                    Notlar</label>
                                <textarea name="notlar" id="m_notlar" class="form-control-pro" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding:20px 30px; background:#f8fafc; border-top:1px solid #f1f5f9;">
                        <button type="button" class="btn-pro btn-outline-pro" data-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn-pro btn-primary-pro">Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Güncellemeler dropdown toggle
            function toggleDropdown() {
                const wrap = document.getElementById('guncellemelerDropdown');
                wrap.classList.toggle('open');
            }
            // Abone durumu dropdown toggle
            function toggleStatusDropdown() {
                const wrap = document.getElementById('statusDropdown');
                wrap.classList.toggle('open');
            }

            // Yerleşim türü dropdown toggle
            function toggleYerlesimDropdown() {
                const wrap = document.getElementById('yerlesimDropdown');
                wrap.classList.toggle('open');
            }

            // Dışarı tıklanınca kapat
            document.addEventListener('click', function (e) {
                const statusWrap = document.getElementById('statusDropdown');
                if (statusWrap && !statusWrap.contains(e.target)) {
                    statusWrap.classList.remove('open');
                }

                const yerlesimWrap = document.getElementById('yerlesimDropdown');
                if (yerlesimWrap && !yerlesimWrap.contains(e.target)) {
                    yerlesimWrap.classList.remove('open');
                }

                const guncellemeWrap = document.getElementById('guncellemelerDropdown');
                if (guncellemeWrap && !guncellemeWrap.contains(e.target)) {
                    guncellemeWrap.classList.remove('open');
                }
            });

            function openEditModal(abone) {
                $('#editForm').attr('action', '/aboneler/' + abone.id);
                $('#m_tesisat').val(abone.ABONE_TESIS_NO);
                $('#m_bolge').val(abone.BOLGE_ADI);
                $('#m_sayac').val(abone.SAYAC_SERI_NO);
                $('#m_adres').val(abone.ADRES);
                $('#m_baglanti_grubu').val(abone.baglanti_grubu);
                $('#m_hesap_adi').val(abone.hesap_adi);
                $('#m_abone_grubu').val(abone.abone_grubu);
                $('#m_tarife').val(abone.tarife);
                $('#m_notlar').val(abone.notlar);
                $('#m_yerlesim_turu').val(abone.yerlesim_turu || '');
                $('#editModal').modal('show');
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu aboneyi silmek istediğinizden emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Evet, Sil',
                    cancelButtonText: 'Vazgeç'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/aboneler/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                $('#row-' + id).fadeOut();
                                Swal.fire('Silindi!', 'Kayıt başarıyla silindi.', 'success');
                            }
                        });
                    }
                });
            }

            function openCreateSidebar() {
                $('#createOverlay').addClass('open');
                $('#createSidebar').addClass('open');
            }
            function closeCreateSidebar() {
                $('#createOverlay').removeClass('open');
                $('#createSidebar').removeClass('open');
            }

            function markAsOld(id) {
                Swal.fire({
                    title: 'Onayla',
                    text: 'Bu aboneyi onaylı kayıt olarak işaretlemek istiyor musunuz?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Evet, Onayla'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/aboneler/' + id + '/mark-old', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                    }
                });
            }

            // Bulk actions logic (simplified for premium)
            $(document).ready(function () {
                $('#selectAll').on('change', function () {
                    $('.sub-checkbox').prop('checked', this.checked);
                    updateBulkActions();
                });
                $('.sub-checkbox').on('change', function () {
                    updateBulkActions();
                });

                function updateBulkActions() {
                    const count = $('.sub-checkbox:checked').length;
                    $('#bulkActions').toggle(count > 0);
                    $('#selectedCount').text(count);
                    $('#selectAllBanner').toggle(count > 0 && count < {{ $aboneler->count() }});
                }
            });

            function bulkApprove() {
                const ids = $('.sub-checkbox:checked').map(function () { return this.value; }).get();
                Swal.fire({
                    title: 'Toplu Onay',
                    text: ids.length + ' adet kaydı onaylamak istiyor musunuz?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Evet, Onayla'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("aboneler.mark-selected-old") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ids: ids })
                        }).then(r => r.json()).then(data => {
                            if (data.success) location.reload();
                        });
                    }
                });
            }

            function markAllOld() {
                const tab = '{{ request("tab", "all") }}';
                Swal.fire({
                    title: 'Tümünü Onayla',
                    text: 'Bu sekmedeki tüm kayıtları onaylamak istediğinizden emin misiniz?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Evet, Tümü Onayla'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/aboneler/mark-all-old?tab=' + tab, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(r => r.json()).then(data => {
                            if (data.success) location.reload();
                        });
                    }
                });
            }

            function toggleActive(id, makeActive) {
                fetch('/aboneler/' + id + '/toggle-active', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(r => r.json()).then(data => {
                    if (data.success) location.reload();
                });
            }

            // Auto Search Logic (Event Delegation)
            let searchTimeout = null;
            
            document.addEventListener('input', function(e) {
                if (e.target && e.target.id === 'autoSearchInput') {
                    const loader = document.getElementById('searchLoader');
                    clearTimeout(searchTimeout);
                    if (loader) loader.style.display = 'block';

                    searchTimeout = setTimeout(() => {
                        performSearch();
                    }, 500); // 500ms debounce
                }
            });

            function performSearch() {
                const form = document.getElementById('filterForm');
                const ajaxContainer = document.getElementById('subscriberAjaxContainer');
                const loader = document.getElementById('searchLoader');
                
                if (!form || !ajaxContainer) return;
                
                // Capturing state BEFORE replacement
                const oldInput = document.getElementById('autoSearchInput');
                const inputVal = oldInput ? oldInput.value : '';
                const selectionStart = oldInput ? oldInput.selectionStart : 0;
                const selectionEnd = oldInput ? oldInput.selectionEnd : 0;
                
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                const url = `${form.action}?${params.toString()}`;

                window.history.pushState({ path: url }, '', url);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('subscriberAjaxContainer');
                    
                    if (newContent) {
                        ajaxContainer.innerHTML = newContent.innerHTML;
                        
                        // Restore focus and cursor position
                        const newInput = document.getElementById('autoSearchInput');
                        if (newInput) {
                            newInput.focus();
                            newInput.setSelectionRange(selectionStart, selectionEnd);
                        }
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    if (loader) loader.style.display = 'none';
                });
            }
        </script>
    @endpush
@endsection