@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root { --font-primary:'Plus Jakarta Sans',sans-serif; --primary-gradient:linear-gradient(135deg,#2563eb,#4f46e5); --bg-main:#f4f6f9; --surface-glass:rgba(255,255,255,0.92); --text-900:#0f172a; --text-500:#64748b; --shadow:0 20px 40px -10px rgba(0,0,0,0.08); }
    * { font-family: var(--font-primary); }
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
    .main-wrap { max-width:1400px; margin:-5rem auto 0; padding:0 2rem; position:relative; z-index:20; }
    .glass-card { 
        background:var(--surface-glass); backdrop-filter:blur(20px); -webkit-backdrop-filter: blur(20px);
        border:1px solid rgba(255,255,255,0.7); border-radius:28px; padding:30px; 
        box-shadow:var(--shadow); margin-bottom:28px; overflow: visible;
    }
    .filter-card { position: relative; z-index: 1000 !important; overflow: visible !important; padding: 24px 28px; }
    .section-title { font-size:0.85rem; font-weight:700; color:#64748b; margin-bottom:20px; display:flex; align-items:center; gap:8px; text-transform:uppercase; letter-spacing:0.05em; }
    .section-title i { padding:0; background:none; border-radius:0; color:#94a3b8; font-size:0.8rem; }
    /* Form Elements */
    .form-group-pro { margin-bottom: 0; }
    .form-group-pro label, .form-lbl { display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.06em; }
    .form-group-pro label i { color: #94a3b8; margin-right: 4px; }
    .form-control-pro {
        width: 100%; padding: 0 14px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        font-size: 0.85rem; color: var(--text-900); font-weight: 600; transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08); }
    select.form-control-pro { -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg width="12" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L13 1" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'); background-repeat: no-repeat; background-position: right 14px center; background-size: 9px; padding-right: 36px; }
    .btn-pro { padding:0 18px; border-radius:10px; font-weight:700; font-size:.82rem; display:inline-flex; align-items:center; gap:6px; transition:all .2s; border:none; cursor:pointer; text-decoration:none !important; }
    .btn-primary-pro { background:var(--primary-gradient); color:#fff !important; box-shadow:0 4px 12px -3px rgba(37,99,235,.25); }
    .btn-primary-pro:hover { transform:translateY(-1px); box-shadow:0 6px 16px -3px rgba(37,99,235,.35); }
    .btn-outline-pro { background:#fff; border:1px solid #e2e8f0; color:#64748b; }
    .btn-outline-pro:hover { border-color:#3b82f6; color:#2563eb; background:#f8faff; }
    .btn-success-pro { background:linear-gradient(135deg,#059669,#10b981); color:#fff !important; }
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

    .tbl-wrap { overflow-x:auto; border-radius: 20px; }
    table.tbl { width:100%; border-collapse:separate; border-spacing:0; }
    table.tbl thead th { background:#f8fafc; padding:16px 20px; font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e2e8f0; }
    table.tbl tbody td { padding:16px 20px; font-size:0.9rem; color:#1e293b; border-bottom:1px solid #f1f5f9; background:#fff; transition:background 0.2s; }
    table.tbl tbody tr:hover td { background:#f8fafc; }
    table.tbl tfoot td { background:#f1f5f9 !important; font-weight:800; color:var(--text-900); padding:12px; }
    table.tbl tfoot tr td:first-child { border-radius:10px 0 0 10px; } table.tbl tfoot tr td:last-child { border-radius:0 10px 10px 0; }
    .badge-donem { background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:.73rem; padding:3px 9px; border-radius:20px; }
    .badge-tesisat { background:#f1f5f9; color:#475569; font-weight:600; font-size:.76rem; padding:3px 9px; border-radius:8px; font-family:monospace; }
    .endeks-val { font-weight:700; color:#334155; }
    .dropdown-menu-pro { border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 12px 28px -5px rgba(0,0,0,.1); padding:6px; min-width:190px; }
    .dropdown-item-pro { padding:9px 14px; border-radius:9px; font-weight:600; font-size:.84rem; display:flex; align-items:center; gap:9px; color:var(--text-900); }
    .dropdown-item-pro:hover { background:#f8fafc; }

    /* AJAX Pagination & Status styles */
    .status-badge { padding:4px 10px; border-radius:6px; font-weight:700; font-size:0.75rem; display:inline-flex; align-items:center; gap:5px; }
    .status-badge.success { background:#dcfce7; color:#166534; }
    .status-badge.error { background:#fee2e2; color:#991b1b; }
    .btn-incele { background:#eff6ff; color:#2563eb; font-weight:600; border:1px solid #bfdbfe; border-radius:8px; padding:4px 10px; font-size:0.8rem; cursor:pointer; }
    .detay-panel { display:none; }

    /* ═══════════ PREMIUM ENDEKS DETAY MODAL ═══════════ */
    #endeksDetayModal {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        align-items:center; justify-content:center; padding:20px;
        opacity:0; transition:opacity 0.25s ease;
    }
    #endeksDetayModal.active { opacity:1; }
    .emd-card {
        background:#fff; border-radius:28px; width:100%; max-width:1080px; max-height:90vh;
        display:flex; flex-direction:column; overflow:hidden;
        box-shadow:0 40px 80px -20px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.1);
        transform:scale(0.93) translateY(20px); transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
        opacity:0;
    }
    #endeksDetayModal.active .emd-card { transform:scale(1) translateY(0); opacity:1; }
    /* Header */
    .emd-header {
        background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#1e1b4b 100%);
        padding:16px 24px; display:flex; align-items:center; justify-content:space-between;
        flex-shrink:0; gap:16px;
    }
    .emd-header-left { display:flex; align-items:center; gap:12px; }
    .emd-header-icon {
        width:40px; height:40px; border-radius:12px; background:rgba(96,165,250,0.15);
        border:1px solid rgba(96,165,250,0.3); display:flex; align-items:center; justify-content:center;
        font-size:1.1rem; color:#60a5fa; flex-shrink:0;
    }
    .emd-eyebrow { font-size:0.65rem; font-weight:800; color:#60a5fa; text-transform:uppercase; letter-spacing:0.12em; margin-bottom:5px; }
    .emd-title-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .emd-fatura-badge {
        font-size:1rem; font-weight:800; color:#fff; letter-spacing:-0.02em;
        background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12);
        padding:4px 14px; border-radius:10px;
    }
    .emd-sep { color:rgba(255,255,255,0.2); font-size:1.2rem; }
    .emd-donem-pill {
        font-size:0.8rem; font-weight:700; color:#93c5fd;
        background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3);
        padding:4px 12px; border-radius:20px;
    }
    .emd-header-right { display:flex; align-items:center; gap:12px; flex-shrink:0; }
    .emd-carpan-pill {
        background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);
        border-radius:10px; padding:6px 14px; text-align:center;
    }
    .emd-carpan-pill small { display:block; font-size:0.6rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; }
    .emd-carpan-pill span { font-size:1.05rem; font-weight:900; color:#fff; font-family:monospace; }
    .emd-close-btn {
        width:38px; height:38px; border-radius:10px; border:1px solid rgba(255,255,255,0.15);
        background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.6);
        display:flex; align-items:center; justify-content:center; cursor:pointer;
        font-size:0.95rem; transition:all 0.2s;
    }
    .emd-close-btn:hover { background:rgba(239,68,68,0.2); border-color:rgba(239,68,68,0.4); color:#f87171; }
    /* Body */
    .emd-body { flex:1; overflow-y:auto; padding:20px; background:#f8fafc; display:flex; flex-direction:column; gap:16px; }
    .emd-top-grid { display:grid; grid-template-columns:1fr 1.6fr; gap:16px; }
    @media(max-width:860px) { .emd-top-grid { grid-template-columns:1fr; } }
    /* Section Cards */
    .emd-section-card {
        background:#fff; border-radius:16px; padding:16px;
        border:1px solid #e2e8f0; box-shadow:0 2px 8px rgba(0,0,0,0.03);
    }
    .emd-section-title {
        font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase;
        letter-spacing:0.1em; margin-bottom:12px; display:flex; align-items:center; gap:8px;
    }
    .emd-section-title i { color:#cbd5e1; }
    /* Info list */
    .emd-info-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:6px 0; border-bottom:1px dashed #f1f5f9;
    }
    .emd-info-row:last-child { border-bottom:none; padding-bottom:0; }
    .emd-info-key { font-size:0.78rem; font-weight:600; color:#64748b; }
    .emd-info-val { font-size:0.8rem; font-weight:700; color:#0f172a; text-align:right; max-width:60%; }
    .emd-info-val.mono { font-family:monospace; font-size:0.85rem; }
    /* Endeks Table */
    .emd-endeks-header, .emd-endeks-row {
        display:grid;
        grid-template-columns:50px 1fr 1fr 1fr 1fr 1fr;
        align-items:center; gap:4px;
    }
    .emd-endeks-header {
        font-size:0.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase;
        letter-spacing:0.07em; padding:0 8px 6px; border-bottom:2px solid #f1f5f9;
    }
    .emd-endeks-row {
        padding:6px 8px; border-bottom:1px solid #f8fafc; font-size:0.78rem;
        font-weight:600; color:#334155; transition:background 0.15s;
    }
    .emd-endeks-row:hover { background:#f8fafc; border-radius:8px; }
    .emd-endeks-row.ana {
        background:#eff6ff; border:1px solid #bfdbfe;
        border-radius:10px; margin:4px 0; font-weight:800;
    }
    .emd-endeks-row.reaktif { background:#fef9f0; }
    .emd-tarife-lbl {
        font-size:0.8rem; font-weight:900; color:#1d4ed8;
        background:#dbeafe; padding:2px 6px; border-radius:6px; text-align:center; line-height:1.2;
    }
    .emd-tarife-lbl.sub { font-size:0.65rem; font-weight:600; color:#3b82f6; display:block; }
    .emd-tarife-lbl.ri-lbl { background:#fef2f2; color:#dc2626; }
    .emd-tarife-lbl.rc-lbl { background:#fff6ed; color:#ea580c; }
    .emd-fark-val { font-weight:800; color:#1d4ed8; }
    .emd-fark-val.neg { color:#dc2626; }
    .emd-gercek-val { font-weight:700; color:#059669; font-size:0.75rem; }
    /* Finans */
    .emd-finans-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; }
    .emd-kpi-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:12px;
        padding:12px; display:flex; flex-direction:column; gap:4px;
    }
    .emd-kpi-val {
        font-size:0.95rem; font-weight:900; padding:4px 8px;
        border-radius:8px; letter-spacing:-0.02em; line-height:1;
    }
    .emd-kpi-label { font-size:0.68rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; }
    .emd-genel-card {
        background:linear-gradient(135deg,#059669 0%,#10b981 100%);
        border-radius:16px; padding:16px; color:#fff; position:relative; overflow:hidden;
        box-shadow:0 8px 24px -8px rgba(16,185,129,0.45);
    }
    .emd-genel-card::before {
        content:''; position:absolute; top:-30px; right:-30px;
        width:120px; height:120px; background:rgba(255,255,255,0.12);
        border-radius:50%; filter:blur(20px);
    }
    .emd-genel-label { font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; opacity:0.9; margin-bottom:6px; }
    .emd-genel-amount { font-size:1.8rem; font-weight:900; letter-spacing:-0.04em; line-height:1; }
    .emd-genel-amount small { font-size:1.2rem; opacity:0.8; }
    .emd-genel-sub {
        display:flex; gap:16px; margin-top:12px; padding-top:10px;
        border-top:1px solid rgba(255,255,255,0.2);
        font-size:0.75rem; font-weight:600; opacity:0.85;
    }
    /* Banner */
    .emd-banner {
        flex-shrink:0; padding:12px 24px;
        border-top:1px solid rgba(0,0,0,0.06);
    }
    .emd-banner-inner {
        display:flex; align-items:center; gap:12px; border-radius:12px; padding:10px 16px;
    }
    .emd-banner-inner.success { background:#f0fdf4; border:1px solid #bbf7d0; }
    .emd-banner-inner.error   { background:#fef2f2; border:1px solid #fecdd3; }
    .emd-banner-icon {
        width:38px; height:38px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:1rem; flex-shrink:0;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }
    .emd-banner-title { font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:2px; }
    .emd-banner-msg { font-size:0.92rem; font-weight:700; color:#0f172a; }
    .emd-banner-msg span { font-weight:500; color:#475569; }
    .emd-reaktif-badge {
        margin-left:auto; background:#fee2e2; color:#b91c1c;
        padding:6px 16px; border-radius:20px; font-size:0.75rem;
        font-weight:800; border:1px solid #fca5a5; white-space:nowrap;
        display:flex; align-items:center; gap:6px;
    }
    .loading-results { opacity: 0.5; pointer-events: none; }
    .pagination-wrap .pagination { margin-bottom: 0; justify-content: center; gap: 5px; }
    .pagination-wrap .page-item .page-link { border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 14px; font-weight: 600; }
    .pagination-wrap .page-item.active .page-link { background: var(--primary-gradient); border-color: transparent; color: #fff; }

    /* ===== CUSTOM CHECKBOX MULTISELECT ===== */
    .custom-multi-select { position: relative; width: 100%; z-index: 1000; }
    .custom-multi-select .dropdown-toggle {
        text-align: left; background: #fff; border: 1px solid #e2e8f0;
        padding: 0 14px; border-radius: 10px; font-size: .85rem; color: var(--text-900);
        display: flex; justify-content: space-between; align-items: center; width: 100%;
        transition: all 0.2s; font-weight: 600;
    }
    .custom-multi-select .dropdown-toggle:hover { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.08); }
    .custom-multi-select .dropdown-toggle::after { display: none !important; }
    .custom-multi-select .dropdown-menu {
        width: 100%; border-radius: 16px; border: 1.5px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15); padding: 10px;
        max-height: 260px; overflow-y: auto; margin-top: 6px;
        background: #fff; z-index: 99999 !important;
        position: absolute !important;
    }
    .custom-multi-select .dropdown-menu::-webkit-scrollbar { width: 5px; }
    .custom-multi-select .dropdown-menu::-webkit-scrollbar-track { background: transparent; }
    .custom-multi-select .dropdown-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .custom-multi-select .form-check {
        padding: 7px 10px; margin-bottom: 1px; border-radius: 9px;
        transition: background 0.15s; display: flex; align-items: center; gap: 10px; cursor: pointer;
    }
    .custom-multi-select .form-check:hover { background: #eff6ff; }
    .custom-multi-select .form-check.checked-row { background: #eff6ff; }
    .custom-multi-select .form-check-input { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    
    .cb-box {
        width: 18px; height: 18px; border: 1.5px solid #cbd5e1; border-radius: 5px;
        background: #fff; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; flex-shrink: 0;
    }
    .form-check-input:checked + .cb-box { background: #3b82f6; border-color: #3b82f6; }
    .cb-box svg { width: 12px; height: 12px; fill: none; stroke: white; stroke-width: 3.5; stroke-linecap: round; stroke-linejoin: round; display: none; }
    .form-check-input:checked + .cb-box svg { display: block; }
    .cb-label { font-size: 0.88rem; color: #475569; font-weight: 600; }
    .form-check-input:checked ~ .cb-label { color: #1e293b; }

    .select-all-btn {
        padding: 8px 10px; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
        color: #3b82f6; font-weight: 700; font-size: 0.82rem; cursor: pointer;
    }
    .select-all-btn:hover { background: #f0f7ff; border-radius: 8px; }

    /* ===== ADVANCED FILTER BUTTON ===== */
    .btn-advanced-pro {
        position: relative; padding: 11px 18px; border-radius: 13px; font-weight: 700;
        font-size: .88rem; display: inline-flex; align-items: center; justify-content: center;
        gap: 8px; transition: all .3s; border: 1.5px solid #c7d2fe; cursor: pointer;
        background: linear-gradient(135deg, #eff6ff, #f5f3ff); color: #4f46e5;
        box-shadow: 0 4px 12px rgba(79,70,229,.1);
    }
    .btn-advanced-pro:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79,70,229,.2); border-color: #818cf8; }

    /* Active dot indicator */
    .adv-active-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #ef4444; display: inline-block;
        animation: pulse-dot 1.5s infinite;
    }
    @keyframes pulse-dot {
        0%,100% { transform: scale(1); opacity:1; }
        50% { transform: scale(1.4); opacity:.7; }
    }

    /* Active filter badge */
    .adv-badge {
        display: inline-flex; align-items: center;
        background: #eff6ff; color: #1d4ed8;
        border: 1px solid #bfdbfe; border-radius: 20px;
        font-size: .75rem; font-weight: 700; padding: 3px 10px;
    }

    /* ===== MODAL FILTER GROUPS ===== */
    .modal-filter-group {
        display: flex; align-items: flex-start; gap: 16px;
        padding: 20px 0; border-bottom: 1px solid #e2e8f0;
    }
    .modal-filter-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0; margin-top: 4px;
    }

    /* Radio card group */
    .modal-radio-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .modal-radio-card {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 18px; border-radius: 12px; border: 1.5px solid #e2e8f0;
        background: #fff; cursor: pointer; font-size: .88rem; font-weight: 600;
        color: #64748b; transition: all .2s;
    }
    .modal-radio-card input[type="radio"] { display: none; }
    .modal-radio-card:hover { border-color: #818cf8; color: #4f46e5; background: #f5f3ff; }
    .modal-radio-card.active { border-color: #4f46e5; background: linear-gradient(135deg,#eff6ff,#f5f3ff); color: #4f46e5; box-shadow: 0 4px 12px rgba(79,70,229,.12); }

    /* Modal multiselect inside modal gets higher z-index */
    .modal-ms .dropdown-menu { z-index: 9999999 !important; }

    /* ===== EXPORT LOADING OVERLAY ===== */
    #exportLoadingOverlay {
        display: none;
        position: fixed; inset: 0; z-index: 99999;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center; justify-content: center;
        flex-direction: column; gap: 24px;
    }
    #exportLoadingOverlay.active { display: flex; }
    .export-loader-box {
        background: rgba(255,255,255,0.97);
        border-radius: 24px;
        padding: 40px 50px;
        text-align: center;
        box-shadow: 0 30px 80px rgba(0,0,0,0.25);
        animation: fadeScaleIn .35s ease;
    }
    @keyframes fadeScaleIn {
        from { opacity:0; transform:scale(.92); }
        to   { opacity:1; transform:scale(1); }
    }
    .export-spinner {
        width: 64px; height: 64px;
        border: 5px solid #e2e8f0;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: spin .85s linear infinite;
        margin: 0 auto 20px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .export-loader-title {
        font-size: 1.15rem; font-weight: 800;
        color: #0f172a; margin-bottom: 6px;
    }
    .export-loader-sub {
        font-size: .85rem; color: #64748b; font-weight: 500;
    }
    .export-progress {
        width: 220px; height: 6px;
        background: #e2e8f0; border-radius: 99px;
        margin: 16px auto 0; overflow: hidden;
    }
    .export-progress-bar {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, #2563eb, #4f46e5);
        border-radius: 99px;
        animation: progressFill 25s ease-out forwards;
    }
    @keyframes progressFill {
        0%   { width: 0%; }
        40%  { width: 55%; }
        70%  { width: 78%; }
        90%  { width: 90%; }
        100% { width: 95%; }
    }
    .export-overlay-close {
        margin-top:18px; padding:7px 20px; border-radius:10px;
        border:1px solid #e2e8f0; background:#f8fafc; color:#64748b;
        font-weight:700; font-size:.83rem; cursor:pointer; display:none;
    }
    .export-overlay-close:hover { background:#f1f5f9; color:#0f172a; }
    .export-open-btn {
        display:none; margin-top:18px; padding:12px 28px; border-radius:13px;
        background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff;
        font-weight:800; font-size:.95rem; border:none; cursor:pointer;
        box-shadow:0 8px 20px -5px rgba(37,99,235,.35);
        transition:all .2s;
    }
    .export-open-btn:hover { transform:translateY(-2px); box-shadow:0 12px 24px -5px rgba(37,99,235,.45); }
    .export-success-icon {
        display:none; width:64px; height:64px; border-radius:50%;
        background:#dcfce7; color:#16a34a; font-size:1.8rem;
        align-items:center; justify-content:center; margin:0 auto 16px;
    }

    /* ===== RESULTS LOADING OVERLAY ===== */
    #resultsLoadingOverlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9998;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    #resultsLoadingOverlay.active { display: flex; }
    .results-loader-card {
        background: #fff;
        border-radius: 24px;
        padding: 48px 56px;
        box-shadow: 0 32px 80px rgba(0,0,0,0.22);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        min-width: 320px;
        max-width: 420px;
        text-align: center;
    }
    .results-loader-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #2563eb;
        font-size: 1.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: rl-pulse 1.8s ease-in-out infinite;
    }
    @keyframes rl-pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37,99,235,0.25); }
        50%       { transform: scale(1.08); box-shadow: 0 0 0 12px rgba(37,99,235,0); }
    }
    .results-loader-spinner {
        width: 44px;
        height: 44px;
        border: 4px solid #e2e8f0;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: rl-spin 0.75s linear infinite;
    }
    @keyframes rl-spin { to { transform: rotate(360deg); } }
    .results-loader-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .results-loader-sub {
        font-size: 0.88rem;
        color: #64748b;
        font-weight: 500;
    }
    .results-loader-dots span {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin: 0 3px;
        border-radius: 50%;
        background: #2563eb;
        animation: rl-bounce 1.2s ease-in-out infinite;
    }
    .results-loader-dots span:nth-child(2) { animation-delay: 0.2s; }
    .results-loader-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes rl-bounce {
        0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
        40%           { transform: scale(1.2); opacity: 1; }
    }
    .results-loader-bar {
        width: 100%;
        height: 5px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }
    .results-loader-bar-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #2563eb, #4f46e5);
        border-radius: 99px;
        animation: rl-bar 2.5s ease-in-out infinite;
    }
    @keyframes rl-bar {
        0%   { width: 0%; margin-left: 0; }
        50%  { width: 75%; margin-left: 0; }
        100% { width: 0%; margin-left: 100%; }
    }


    /* ═══════════ PREMIUM ENDEKS GEÇMİŞ 6 AY MODAL ═══════════ */
    #endeksGecmisModal {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        align-items:center; justify-content:center; padding:20px;
        opacity:0; transition:opacity 0.25s ease;
    }
    #endeksGecmisModal.active { opacity:1; }
    #endeksGecmisModal.active .emd-card { transform:scale(1) translateY(0); opacity:1; }

    /* ── Flat-Table Stilleri ──────────────────────────────────────── */
    .egm-main-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
    }
    .egm-main-table thead tr {
        background: #f8fafc;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .egm-main-table thead th {
        padding: 11px 14px;
        font-size: 0.68rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    /* Dönem başlık satırı */
    .egm-period-header-row td { 
        padding: 9px 16px !important;
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        border-bottom: none;
    }
    .egm-period-label {
        font-size: 0.88rem;
        font-weight: 800;
        color: #fff;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .egm-period-label i { opacity: 0.8; }
    .egm-period-meta {
        font-size: 0.75rem;
        color: #c4b5fd;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 3px 10px;
        border-radius: 20px;
        margin-left: 15px;
        vertical-align: middle;
    }
    .egm-period-meta strong { color: #ede9fe; }
    .egm-period-tutar {
        font-size: 0.88rem;
        font-weight: 800;
        color: #a7f3d0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.3);
        padding: 4px 14px;
        border-radius: 20px;
    }

    /* Veri satırları */
    .egm-data-row td {
        padding: 7px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .egm-data-row:hover td { background: #fafbff; }
    .egm-row-ana td { background: #f5f3ff; }
    .egm-row-ana:hover td { background: #ede9fe; }
    .egm-row-reaktif td { background: #fffbf5; }
    .egm-row-reaktif:hover td { background: #fef3c7; }
    .egm-row-sep td { border-bottom: 3px solid #c4b5fd !important; }

    /* Gösterge hücresi */
    .egm-indicator-cell { white-space: nowrap; }
    .egm-indicator-sub {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 500;
        margin-left: 6px;
    }

    /* Sayısal hücreler */
    .egm-num-cell {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        color: #334155;
        font-weight: 600;
        white-space: nowrap;
    }
    .egm-tuketim-cell { color: #059669 !important; font-weight: 700 !important; }

    /* Rozet (badge) stilleri */
    .egm-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 900;
        padding: 2px 8px;
        border-radius: 6px;
        min-width: 28px;
        text-align: center;
    }
    .egm-badge-t0 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .egm-badge-t1 { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .egm-badge-t2 { background: #f3e8ff; color: #6d28d9; border: 1px solid #ddd6fe; }
    .egm-badge-t3 { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .egm-badge-ri { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .egm-badge-rc { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
</style>

{{-- ===== EXPORT LOADING OVERLAY ===== --}}
<div id="exportLoadingOverlay">
    <div class="export-loader-box">
        <div class="export-success-icon" id="exportSuccessIcon"><i class="fas fa-check"></i></div>
        <div class="export-spinner" id="exportSpinner"></div>
        <div class="export-loader-title" id="exportLoaderTitle">Rapor Hazırlanıyor…</div>
        <div class="export-loader-sub" id="exportLoaderSub">Lütfen bekleyin, dosyanız oluşturuluyor.</div>
        <div class="export-progress" id="exportProgressWrap"><div class="export-progress-bar" id="exportProgressBar"></div></div>
        <button class="export-open-btn" id="exportOpenBtn"></button>
        <button class="export-overlay-close" id="exportOverlayClose"><i class="fas fa-times"></i> Kapat</button>
    </div>
</div>

{{-- ===== RESULTS LOADING OVERLAY ===== --}}
<div id="resultsLoadingOverlay">
    <div class="results-loader-card">
        <div class="results-loader-icon">
            <i class="fas fa-tachometer-alt"></i>
        </div>
        <div class="results-loader-spinner"></div>
        <div class="results-loader-title">Endeksler Analiz Ediliyor</div>
        <div class="results-loader-sub" id="resultsLoaderSub">Kayıtlar analiz ediliyor…</div>
        <div class="results-loader-progress" id="resultsLoaderProgress" style="font-size:1.1rem;font-weight:800;color:#f59e0b;margin-top:6px;"></div>
        <div class="results-loader-bar">
            <div class="results-loader-bar-fill" id="resultsLoaderBarFill"></div>
        </div>
    </div>
</div>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Fatura Analiz ve Denetim İşlemleri</h1>
                <p class="hero-subtitle">Endes ve okuma analizleriyle hatalı işlemler tespit ediliyor</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn-pro ml-2" data-toggle="modal" data-target="#pdfAnalizModal" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; font-size: 1.05rem; padding: 10px 20px; font-weight: 800; border-radius: 12px; box-shadow: 0 10px 20px -5px rgba(239,68,68,0.5); transition: all 0.3s;">
                    <i class="fas fa-file-pdf mr-2" style="font-size: 1.2rem;"></i> Pdf-Fatura Analiz
                </button>
                <div class="dropdown" id="exportBtnContainer" style="display: {{ request()->anyFilled(['bolge','start_period','end_period','tesisat_no','tarife','baglanti_grubu','yerlesim_tipi']) ? 'block' : 'none' }};">
                    <button type="button" class="btn-pro dropdown-toggle ml-2" data-toggle="dropdown" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; font-size: 1.05rem; padding: 10px 20px; font-weight: 800; border-radius: 12px; box-shadow: 0 10px 20px -5px rgba(16,185,129,0.5); transition: all 0.3s;">
                        <i class="fas fa-file-export mr-2" style="font-size: 1.2rem;"></i> Dışa Aktar
                    </button>
                    <div class="dropdown-menu dropdown-menu-pro dropdown-menu-right" style="border-radius:12px;">
                        <button type="button" data-type="pdf" class="dropdown-item dropdown-item-pro endeks-export-btn"><i class="fas fa-file-pdf text-danger"></i> PDF Raporu Al</button>
                        <button type="button" data-type="excel" class="dropdown-item dropdown-item-pro endeks-export-btn"><i class="fas fa-file-excel text-success"></i> Excel Raporu Al</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-wrap">

        {{-- FİLTRE --}}
        <div class="glass-card filter-card">
            <h5 class="section-title"><i class="fas fa-filter"></i> Analiz Kriterleri</h5>
            
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

            <form action="{{ route('reports.endeks') }}" method="GET" id="mainFilterForm">
                <input type="hidden" name="tab" id="active_tab" value="{{ request('tab', 'sifir_sayac') }}">
                <div id="advancedHiddenFields"></div>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group-pro">
                            <label><i class="fas fa-map-marker-alt"></i> Bölge Seçimi</label>
                            <div class="dropdown custom-multi-select">
                                <button class="dropdown-toggle" type="button" id="bolgeDropdown" data-toggle="dropdown" style="height:42px;">
                                    <span id="bolgeLabel">Bölge Seçin...</span>
                                    <i class="fas fa-chevron-down" style="font-size:.75rem;color:#94a3b8;"></i>
                                </button>
                                <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                    <div class="form-check select-all-wrap" id="selectAllBolgeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                        <input class="form-check-input" type="checkbox" id="selectAllBolge">
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllBolge">Tümünü Seç</label>
                                    </div>
                                    @foreach($bolgeler as $b)
                                        <div class="form-check bolge-row" onclick="toggleCheckbox(this)">
                                            <input class="form-check-input bolge-checkbox" type="checkbox" name="bolge[]" value="{{ $b }}" id="bolge_{{ $loop->index }}"
                                                {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($b, request('bolge')))) ? 'checked' : '' }}>
                                            <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                            <label class="form-check-label" for="bolge_{{ $loop->index }}">{{ $b }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-pro">
                            <label><i class="far fa-calendar-alt"></i> Dönem Seç</label>
                            <select name="start_period" id="hero_start_period" class="form-control-pro" style="height:42px;">
                                <option value="">Tümü</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}" {{ request('start_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex" style="gap:10px;">
                            <button type="submit" class="btn-pro btn-primary-pro flex-fill justify-content-center" style="height:42px;"><i class="fas fa-search"></i> Sonuçları Getir</button>
                            <button type="button" class="btn-pro btn-outline-pro flex-fill justify-content-center" data-toggle="modal" data-target="#advancedFilterModal" style="height:42px; white-space:nowrap;">
                                <i class="fas fa-sliders-h" style="font-size:0.7rem;"></i> Filtrele
                                @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','yerlesim_tipi','end_period']))<span class="adv-active-dot" style="background:#fca5a5;"></span>@endif
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <div id="reportResultsContainer">
            @if(request()->anyFilled(['bolge','start_period','end_period','yerlesim_tipi','baglanti_grubu','tarife','tesisat_no']))
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#eff6ff;color:#2563eb;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 20px;">
                        <i class="fas fa-hourglass-half fa-spin"></i>
                    </div>
                    <h4 style="font-weight:800;color:var(--text-900);">Analiz Başlatılıyor...</h4>
                    <p style="color:var(--text-500);max-width:500px;margin:0 auto;">Seçtiğiniz kriterlere göre analiz yapılıyor, lütfen bekleyin.</p>
                </div>
            @else
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#eff6ff;color:#2563eb;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 20px;">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h4 style="font-weight:800;color:var(--text-900);">Tüketim & Endeks Analizi</h4>
                    <p style="color:var(--text-500);max-width:500px;margin:0 auto;">Bölge veya dönem filtresi seçerek tüm abonelerin detaylı fatura matematiğini (T0, T1, T2, T3) listeleyebilirsiniz.</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- GELİŞMİŞ FİLTRE MODAL                                        --}}
{{-- ============================================================ --}}
<div class="modal fade" id="advancedFilterModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
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

            {{-- Modal Body --}}
            <div class="modal-body" style="padding:45px 35px; min-height: 55vh; display: flex; flex-direction: column; justify-content: space-around;">

                <!-- ROW 1: Bölgeler & Tesisat No -->
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-map-marker-alt" style="color:#3b82f6; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bölgeler
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="ModalEndeksBolgeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px;">
                                <span id="ModalEndeksBolgeLabel">Bölge Seçin...</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllModalEndeksBolgeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllModalEndeksBolge">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllModalEndeksBolge">Tümünü Seç</label>
                                </div>
                                @foreach($bolgeler as $bolge)
                                    <div class="form-check modal-endeks-bolge-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input modal-endeks-bolge-cb" type="checkbox" value="{{ $bolge }}" id="modalendeksbolge_{{ $loop->index }}"
                                            {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($bolge, request('bolge')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="modalendeksbolge_{{ $loop->index }}">{{ $bolge }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" style="margin-bottom: 25px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-hashtag" style="color:#ea580c; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Abone / Tesisat No
                        </label>
                        <input type="text" id="endeks_tesisat" class="form-control-pro" value="{{ request('tesisat_no') }}" placeholder="Örn: 123456" style="padding: 12px 18px; border-radius: 12px; font-family: monospace; font-size: 1.05rem; height: 47px;">
                    </div>
                </div>

                <!-- ROW 2: Dönem Aralığı Başlangıç & Bitiş -->
                <div class="row" style="background: rgba(241, 245, 249, 0.5); padding: 15px; border-radius: 16px; margin-bottom: 25px;">
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-alt" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Dönem Seç
                        </label>
                        <select id="modal_start_period" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            @foreach($donemler as $donem)
                                <option value="{{ $donem }}" {{ request('start_period') == $donem ? 'selected' : '' }}>{{ $donem }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="far fa-calendar-check" style="color:#64748b; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bitiş Dönemi
                        </label>
                        <select id="modal_end_period" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            @foreach($donemler as $donem)
                                <option value="{{ $donem }}" {{ request('end_period') == $donem ? 'selected' : '' }}>{{ $donem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- ROW 3: 3 Selectbox Yan Yana -->
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-plug" style="color:#059669; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Bağlantı Grubu
                        </label>
                        <select id="endeks_baglanti" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            <option value="AG" {{ request('baglanti_grubu')=='AG'?'selected':'' }}>AG – Alçak Gerilim</option>
                            <option value="OG" {{ request('baglanti_grubu')=='OG'?'selected':'' }}>OG – Orta Gerilim</option>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-city" style="color:#9333ea; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Yerleşim Türü
                        </label>
                        <select id="endeks_yerlesim" class="form-control-pro" style="padding: 12px 18px; border-radius: 12px; height: 47px;">
                            <option value="">Tümü</option>
                            <option value="merkez" {{ request('yerlesim_tipi')=='merkez'?'selected':'' }}>Merkez</option>
                            <option value="koy" {{ request('yerlesim_tipi')=='koy'?'selected':'' }}>Köy</option>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <label style="display:block; font-size:0.82rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-tags" style="color:#dc2626; margin-right:6px; font-size:1rem; vertical-align:middle;"></i> Abone Tarife Grubu
                        </label>
                        <div class="dropdown custom-multi-select modal-ms">
                            <button class="dropdown-toggle" type="button" id="EndeksTarifeDropdown" data-toggle="dropdown" style="padding: 12px 18px; font-size: 0.95rem; border-radius: 12px; height: 47px;">
                                <span id="EndeksTarifeLabel">Tüm Tarifeler</span>
                                <i class="fas fa-chevron-down" style="font-size:0.8rem; color:#94a3b8;"></i>
                            </button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();" style="border-radius: 16px; padding: 12px; border: 1px solid #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                                <div class="form-check select-all-wrap" id="selectAllEndeksTarifeRow" style="padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; margin-bottom: 10px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllEndeksTarife">
                                    <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                    <label class="form-check-label fw-bold" style="color: #0f172a;" for="selectAllEndeksTarife">Tümünü Seç</label>
                                </div>
                                @foreach($tarifeler as $t)
                                    <div class="form-check endeks-tarife-row" onclick="toggleCheckbox(this)">
                                        <input class="form-check-input endeks-tarife-cb" type="checkbox" value="{{ $t->tarife }}" id="endekstarife_{{ $loop->index }}"
                                            {{ (!request()->has('tarife') || (is_array(request('tarife')) && in_array($t->tarife, request('tarife')))) ? 'checked' : '' }}>
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label" for="endekstarife_{{ $loop->index }}">{{ $t->abone_grubu ?: $t->tarife }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer" style="background: rgba(248, 250, 252, 0.8); border-top: 1px solid rgba(226, 232, 240, 0.8); padding: 25px 35px; display:flex; justify-content:space-between; align-items: center; border-bottom-left-radius: 28px; border-bottom-right-radius: 28px;">
                <button type="button" class="btn-pro btn-outline-pro" id="clearAdvancedBtn" style="border-radius: 12px; font-weight: 700; transition: all 0.2s;"><i class="fas fa-eraser"></i> Filtreleri Temizle</button>
                <div class="d-flex gap-3">
                    <button type="button" class="btn-pro btn-primary-pro" id="applyAdvancedBtn" style="border-radius: 12px; font-weight: 700; padding-left: 28px; padding-right: 28px; transition: all 0.2s;"><i class="fas fa-check"></i> Sonuçları Getir</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
@php
    $endeksRowIds = method_exists($results, 'items')
        ? collect($results->items())->pluck('id')->values()
        : collect($results)->pluck('id')->values();
@endphp
<script>
let endeksRowIds = @json($endeksRowIds);

// Satıra tıklanınca checkbox toggle
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
    initMS('selectAllBolge', 'bolge-checkbox', 'bolgeLabel', 'Bölge Seçin...', 'Tüm Bölgeler Seçili', 'Bölge Seçili');
    initMS('selectAllModalEndeksBolge', 'modal-endeks-bolge-cb', 'ModalEndeksBolgeLabel', 'Bölge Seçin...', 'Tüm Bölgeler Seçili', 'Bölge Seçili');
    initMS('selectAllEndeksTarife', 'endeks-tarife-cb', 'EndeksTarifeLabel', 'Tarife Seçin...', 'Tüm Tarifeler Seçili', 'Tarife Seçili');

    const hasAnyFilter = {{ request()->anyFilled(['bolge','start_period','end_period','yerlesim_tipi','baglanti_grubu','tarife','tesisat_no']) ? 'true' : 'false' }};
    if (hasAnyFilter && !window._initialLoadTriggered) {
        window._initialLoadTriggered = true;
        setTimeout(function() {
            $('#mainFilterForm').submit();
        }, 100);
    }

    // Sync Bolge Selection
    $('.bolge-checkbox').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.modal-endeks-bolge-cb[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });
    $('.modal-endeks-bolge-cb').on('change', function() {
        if(isSyncing) return;
        isSyncing = true;
        const val = $(this).val();
        const checked = $(this).is(':checked');
        const $target = $(`.bolge-checkbox[value="${val}"]`);
        if ($target.is(':checked') !== checked) {
            $target.prop('checked', checked).trigger('change');
        }
        isSyncing = false;
    });

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

    $('#hero_start_period').on('change', function() {
        $('#modal_start_period').val($(this).val());
    });

    $('#clearAdvancedBtn').click(function() {
        $('.modal-endeks-bolge-cb').prop('checked', false).trigger('change');
        $('.endeks-tarife-cb').prop('checked', false).trigger('change');
        $('#endeks_tesisat').val('');
        $('#modal_start_period').val('');
        $('#modal_end_period').val('');
        $('#endeks_baglanti').val('');
        $('#endeks_yerlesim').val('');
    });

    $('#applyAdvancedBtn').click(function() {
        // Modal değerlerini forma hidden input olarak enjekte et
        var $h = $('#advancedHiddenFields');
        $h.empty();

        // Yerleşim tipi
        var yerlesim = $('#endeks_yerlesim').val();
        if (yerlesim) $h.append($('<input type="hidden" name="yerlesim_tipi">').val(yerlesim));

        // Bağlantı grubu
        var baglanti = $('#endeks_baglanti').val();
        if (baglanti) $h.append($('<input type="hidden" name="baglanti_grubu">').val(baglanti));

        // Tesisat no
        var tesisat = $('#endeks_tesisat').val();
        if (tesisat) $h.append($('<input type="hidden" name="tesisat_no">').val(tesisat));

        // Dönem
        var sp = $('#modal_start_period').val() || $('#hero_start_period').val();
        var ep = $('#modal_end_period').val();
        if (sp && ep && sp > ep) { var tmp = sp; sp = ep; ep = tmp; }
        if (sp) $h.append($('<input type="hidden" name="start_period">').val(sp));
        if (ep) $h.append($('<input type="hidden" name="end_period">').val(ep));

        // Modal bölge seçimi
        $h.find('input[name="bolge[]"]').remove();
        var $heroCbs = $('.bolge-checkbox:checked');
        var $modalCbs = $('.modal-endeks-bolge-cb:checked');
        var bolgeSource = $modalCbs.length > 0 ? $modalCbs : $heroCbs;
        bolgeSource.each(function() {
            $h.append($('<input type="hidden" name="bolge[]">').val($(this).val()));
        });

        // Tarife
        $('.endeks-tarife-cb:checked').each(function() {
            $h.append($('<input type="hidden" name="tarife[]">').val($(this).val()));
        });

        $('#advancedFilterModal').modal('hide');
        $('#mainFilterForm').submit();
    });

    let _exportBlobUrl = null;
    let _exportType = '';

    function resetOverlay() {
        $('#exportSuccessIcon').hide();
        $('#exportSpinner').show();
        $('#exportProgressWrap').show();
        $('#exportOpenBtn').hide();
        $('#exportOverlayClose').hide();
    }

    function showExportReady(type, blobUrl) {
        _exportBlobUrl = blobUrl;
        _exportType = type;
        $('#exportSpinner').hide();
        $('#exportProgressWrap').hide();
        $('#exportSuccessIcon').css('display', 'flex');
        $('#exportLoaderTitle').text('İndirme Hazır!');
        $('#exportLoaderSub').text('Dosyanız başarıyla oluşturuldu.');
        var label = type === 'pdf' ? '<i class="fas fa-file-pdf"></i> PDF Dosyasını Aç' : '<i class="fas fa-file-excel"></i> Excel Dosyasını Aç';
        $('#exportOpenBtn').html(label).show();
        $('#exportOverlayClose').show();
    }

    function closeExportOverlay() {
        $('#exportLoadingOverlay').removeClass('active');
        if (_exportBlobUrl) { URL.revokeObjectURL(_exportBlobUrl); _exportBlobUrl = null; }
    }

    $('#exportOverlayClose').on('click', closeExportOverlay);

    $('#exportOpenBtn').on('click', function() {
        if (!_exportBlobUrl) return;
        if (_exportType === 'pdf') {
            window.open(_exportBlobUrl, '_blank');
        } else {
            var a = document.createElement('a');
            a.href = _exportBlobUrl;
            a.download = 'Endeks_Raporu.xlsx';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
        }
        closeExportOverlay();
    });

    function hasReportResults() {
        return $('#reportResultsContainer table.tbl tbody tr').length > 0;
    }

    function refreshExportButtonState() {
        $('#exportBtnContainer').toggle(hasReportResults());
    }

    refreshExportButtonState();

    $('.endeks-export-btn').on('click', async function() {
        if (!hasReportResults()) {
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen önce filtre kriterlerini seçin ve raporu oluşturun.', confirmButtonText: 'Tamam'});
            return;
        }

        var type = $(this).data('type');
        var form = $('#mainFilterForm');
        resetOverlay();
        $('#exportLoaderTitle').text(type === 'pdf' ? 'PDF Hazırlanıyor…' : 'Excel Hazırlanıyor…');
        $('#exportLoaderSub').text(type === 'pdf' ? 'Sayfa tasarımı oluşturuluyor…' : 'Veriler Excel formatına aktarılıyor…');
        $('#exportLoadingOverlay').addClass('active');

        var formData = new FormData(form[0]);
        formData.append('export', type);
        formData.append('tesisat_no', $('#endeks_tesisat').val() || '');
        formData.append('baglanti_grubu', $('#endeks_baglanti').val() || '');
        formData.append('yerlesim_tipi', $('#endeks_yerlesim').val() || '');
        
        var heroSp = $('#hero_start_period').val();
        var mSp = $('#modal_start_period').val();
        formData.append('start_period', heroSp || mSp || '');
        formData.append('end_period', $('#modal_end_period').val() || '');

        if ($('.modal-endeks-bolge-cb:checked').length > 0) {
            formData.delete('bolge[]');
            $('.modal-endeks-bolge-cb:checked').each(function() { formData.append('bolge[]', $(this).val()); });
        }
        $('.endeks-tarife-cb:checked').each(function() { formData.append('tarife[]', $(this).val()); });

        var qs = new URLSearchParams(formData);
        
        try {
            const response = await fetch(`${form.attr('action')}?${qs.toString()}`, { credentials: 'same-origin' });
            if (!response.ok) throw new Error('Rapor oluşturulamadı');
            const blob = await response.blob();
            _exportBlobUrl = window.URL.createObjectURL(blob);
            showExportReady(type, _exportBlobUrl);
        } catch (e) {
            Swal.fire({icon: 'error', title: 'Hata', text: 'Dışa aktarma sırasında bir hata oluştu.', confirmButtonText: 'Tamam'});
            closeExportOverlay();
        }
    });

    // ── AJAX Pagination & Results Update ──────────────────────────────
    function showResultsLoader() {
        $('#resultsLoadingOverlay').addClass('active');
        $('body').css('overflow', 'hidden');
    }
    function hideResultsLoader() {
        $('#resultsLoadingOverlay').removeClass('active');
        $('body').css('overflow', '');
    }

    const resultsLoaderMinDuration = 300; // Gereksiz bekleme kaldırıldı (eski: 2000ms)

    function runAfterResultsLoaderDelay(startedAt, callback) {
        const elapsed = Date.now() - startedAt;
        const remaining = Math.max(resultsLoaderMinDuration - elapsed, 0);
        setTimeout(callback, remaining);
    }

    function renderResultsResponse(response) {
        var html = typeof response === 'object' && response.html !== undefined ? response.html : response;
        endeksRowIds = typeof response === 'object' && response.row_ids !== undefined ? response.row_ids : endeksRowIds;
        $('#reportResultsContainer').html(html);
        enhanceEndeksActionButtons();
        refreshExportButtonState();
        $('html, body').animate({ scrollTop: $('#reportResultsContainer').offset().top - 100 }, 500);
    }

    function updateResults(url, showLoader = true) {
        const loaderStartedAt = Date.now();
        if (showLoader) {
            showResultsLoader();
        } else {
            $('#reportResultsContainer').addClass('loading-results');
        }
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (showLoader) {
                    runAfterResultsLoaderDelay(loaderStartedAt, function() {
                        hideResultsLoader();
                        renderResultsResponse(response);
                    });
                } else {
                    $('#reportResultsContainer').removeClass('loading-results');
                    renderResultsResponse(response);
                }
            },
            error: function() {
                if (showLoader) {
                    runAfterResultsLoaderDelay(loaderStartedAt, function() {
                        hideResultsLoader();
                        Swal.fire({icon: 'error', title: 'Hata', text: 'Sonuçlar güncellenirken bir hata oluştu.', confirmButtonText: 'Tamam'});
                    });
                } else {
                    $('#reportResultsContainer').removeClass('loading-results');
                    Swal.fire({icon: 'error', title: 'Hata', text: 'Sonuçlar güncellenirken bir hata oluştu.', confirmButtonText: 'Tamam'});
                }
            }
        });
    }

    $('#mainFilterForm').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        // start_period form serialize'da var (hero_start_period select'ten), manuel de ekleniyor → önce sil
        let formData = $form.serialize();
        formData = formData.replace(/(?:^|&)start_period=[^&]*/g, '').replace(/(?:^|&)end_period=[^&]*/g, '');
        if (formData.startsWith('&')) formData = formData.slice(1);

        const heroStartPeriod = $('#hero_start_period').val();
        var sp = heroStartPeriod || $('#modal_start_period').val() || '';
        var ep = $('#modal_end_period').val() || '';
        if (sp && ep && sp > ep) { var tmp = sp; sp = ep; ep = tmp; }

        formData += '&tesisat_no=' + encodeURIComponent($('#endeks_tesisat').val() || '') +
                    '&baglanti_grubu=' + encodeURIComponent($('#endeks_baglanti').val() || '') +
                    '&yerlesim_tipi=' + encodeURIComponent($('#endeks_yerlesim').val() || '') +
                    '&start_period=' + encodeURIComponent(sp) +
                    '&end_period=' + encodeURIComponent(ep);
        
        if ($('.modal-endeks-bolge-cb:checked').length > 0) {
            formData = formData.replace(/bolge%5B%5D=[^&]*&?/g, '');
            $('.modal-endeks-bolge-cb:checked').each(function() { formData += '&bolge[]=' + encodeURIComponent($(this).val()); });
        }
        
        if (!formData.includes('tarife')) {
            $('.endeks-tarife-cb:checked').each(function() { formData += '&tarife[]=' + encodeURIComponent($(this).val()); });
        }

        const hasBolge = $('.bolge-checkbox:checked, .modal-endeks-bolge-cb:checked').length > 0;
        const hasPeriod = !!sp || !!ep;
        const hasTarife = $('.endeks-tarife-cb:checked').length > 0;
        const hasBaglanti = !!$('#endeks_baglanti').val();
        const hasYerlesim = !!$('#endeks_yerlesim').val();
        const hasTesisat = !!$('#endeks_tesisat').val();

        if (!hasBolge && !hasPeriod && !hasTarife && !hasBaglanti && !hasYerlesim && !hasTesisat) {
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen sonuçları getirmeden önce en az bir filtreleme seçeneği seçiniz.', confirmButtonText: 'Tamam'});
            return;
        }

        // Analiz başlamadan önce cache'i temizle — yeni filtre seçildi
        window._endeksTabCache = null;
        // Default tab sifir_sayac — kullanıcı değiştirmediyse
        if (!$('#active_tab').data('user-changed')) {
            $('#active_tab').val('sifir_sayac');
        }
        $('#active_tab').data('user-changed', false);
        var url = $form.attr('action') + '?' + formData;
        streamResults(url);
    });

    async function streamResults(url) {
        var sep = url.includes('?') ? '&' : '?';
        var fetchUrl = url + sep + 'stream=1';

        showResultsLoader();
        $('#resultsLoaderProgress').text('');
        $('#resultsLoaderBarFill').css('width', '0%');
        $('#resultsLoaderSub').text('Kayıtlar analiz ediliyor…');

        // Güvenlik: 5 dakika sonra stream hâlâ bitmemişse zorla kapat
        let streamCompleted = false;
        const streamTimeout = setTimeout(function() {
            if (!streamCompleted) {
                hideResultsLoader();
                Swal.fire({icon: 'warning', title: 'Zaman Aşımı', text: 'Analiz çok uzun sürdü. Lütfen filtre aralığını daraltın.', confirmButtonText: 'Tamam'});
            }
        }, 300000); // 5 dakika

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            handleStreamEvent(data);
                            // complete gelince döngüyü bitir
                            if (data.type === 'complete') {
                                streamCompleted = true;
                                clearTimeout(streamTimeout);
                                return;
                            }
                        } catch(e) {}
                    }
                }
            }
        } catch(e) {
            hideResultsLoader();
            Swal.fire({icon: 'error', title: 'Hata', text: 'Analiz sırasında bir hata oluştu.', confirmButtonText: 'Tamam'});
        } finally {
            hideResultsLoader();
            streamCompleted = true;
            clearTimeout(streamTimeout);
            // Stream URL'ini tarayıcı geçmişine ekle
            try {
                var streamUrl = new URL(url, window.location.href);
                window.history.pushState({path: streamUrl.toString()}, '', streamUrl.toString());
            } catch(_) {}
        }
    }

    function handleStreamEvent(data) {
        switch (data.type) {
            case 'start':
                $('#resultsLoaderSub').text('0 / ' + data.total + ' kayıt analiz edildi');
                break;
            case 'progress':
                var pct = Math.round((data.processed / data.total) * 100);
                $('#resultsLoaderSub').text(data.processed + ' / ' + data.total + ' kayıt analiz edildi');
                $('#resultsLoaderBarFill').css('width', pct + '%');
                break;
            case 'complete':
                hideResultsLoader();
                // Tüm sekmelerin HTML'ini client-side cache'le
                if (data.allTabHtml) {
                    window._endeksTabCache = {
                        html:    data.allTabHtml,
                        rowIds:  data.allTabRowIds || {}
                    };
                }
                renderResultsResponse(data);
                break;
        }
    }

    // applyAdvancedBtn handler yukarıda tanımlandı

    function enhanceEndeksActionButtons() {
        $('#reportResultsContainer table.tbl tbody tr').each(function(index) {
            var $row = $(this);
            var $detailRow = $row.next('tr');
            var $panel = $detailRow.find('.detay-panel');
            if (!$panel.length) return;

            var data = {};
            try {
                data = JSON.parse($panel.attr('data-json') || '{}');
            } catch(e) { console.error('Endeks JSON parsing error:', e); }

            var rowId = endeksRowIds[Math.floor(index / 2)];
            $row.removeAttr('onclick');
            $row.find('td:last').html(`
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn-incele endeks-detail-btn" title="Detay Görüntüle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn-incele endeks-itiraz-btn" title="İtiraz Et" data-id="${rowId || ''}" style="background:#fef2f2;color:#dc2626;border-color:#fecaca;">
                        <i class="fas fa-hand-paper"></i>
                    </button>
                </div>
            `);
            $detailRow.addClass('d-none');
        });
    }

    enhanceEndeksActionButtons();

    // Intercept Pagination Clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        updateResults(url, false);
    });

    // Intercept Tab Clicks — cache'ten anlık HTML swap, sunucuya istek gönderilmez
    $(document).on('click', '.endeks-tab-btn', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');
        $('#active_tab').val(tab);

        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', tab);
        currentUrl.searchParams.delete('page');
        currentUrl.searchParams.delete('stream');
        window.history.pushState({path: currentUrl.toString()}, '', currentUrl.toString());

        // Önce client-side cache'e bak — analiz zaten yapıldı, yeniden yapma
        if (window._endeksTabCache && window._endeksTabCache.html[tab] !== undefined) {
            var cachedData = {
                html:    window._endeksTabCache.html[tab],
                row_ids: window._endeksTabCache.rowIds[tab] || []
            };
            renderResultsResponse(cachedData);
            return;
        }

        // Cache yoksa (sayfa yenilendi vs.) sunucuya düş — normal AJAX
        updateResults(currentUrl.toString(), false);
    });

    function parseEndeksNumber(value) {
        if (value === undefined || value === null) return 0;
        var normalized = value.toString().trim().replace(/\s/g, '');
        if (normalized.includes(',') && normalized.includes('.')) {
            normalized = normalized.replace(/\./g, '').replace(',', '.');
        } else {
            normalized = normalized.replace(',', '.');
        }
        var parsed = parseFloat(normalized);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function buildEndeksIssueDetails($detailRow) {
        var $panel = $detailRow.find('.detay-panel');
        var carpan = 1;
        var headerSpan = $panel.find('.detay-header span:last').text().trim();
        var carpanMatch = headerSpan.match(/x([0-9.,]+)/);
        if (carpanMatch) carpan = parseEndeksNumber(carpanMatch[1]);

        var rows = [];
        var issues = [];

        // Parse T0, T1, T2, T3 from the endeks table (second table)
        var $endeksTable = $panel.find('table').eq(1);
        if ($endeksTable.length) {
            $endeksTable.find('tbody tr').each(function() {
                var $td = $(this).find('td');
                if ($td.length < 8) return;
                var tip = $td.eq(0).text().trim();
                if (tip === 'Rİ' || tip === 'RC') return;
                var ilk = parseEndeksNumber($td.eq(1).text());
                var son = parseEndeksNumber($td.eq(2).text());
                var fark = parseEndeksNumber($td.eq(3).text());
                var gelen = parseEndeksNumber($td.eq(4).text());
                rows.push({ tip: tip, ilk: ilk, son: son, fark: fark, gelen: gelen });
            });
        }

        var totalRow = rows.find(function(row) { return row.tip === 'T0'; }) || rows[0];
        var negativeRows = rows.filter(function(row) { return row.son < row.ilk; }).map(function(row) { return row.tip; });

        if (negativeRows.length > 0) {
            issues.push({
                title: 'Negatif Endeks Var',
                text: `Son endeks ilk endeksten düşük görünüyor. Sayaç geriye sarmış, sıfırlanmış, pano/sayaç arızası oluşmuş veya ilk-son endeks kolonları ters/yanlış girilmiş olabilir. Kontrol edilen alanlar: ${negativeRows.join(', ')}.`
            });
        }

        if (totalRow && (totalRow.fark === 0 || totalRow.gelen <= 0)) {
            issues.push({
                title: 'Sıfır Endeks / Tüketim Yok',
                text: 'Toplam endeks farkı sıfır ya da toplam tüketim yok. Bu durumda sayaç okunmamış, sayaç/pano arızası nedeniyle tüketim üretilememiş veya okuma verisi eksik aktarılmış olabilir.'
            });
        }

        if (totalRow) {
            var hesaplananToplam = totalRow.fark * carpan;
            if (totalRow.fark !== 0 && Math.abs(totalRow.gelen - hesaplananToplam) > 10) {
                issues.push({
                    title: 'Tutarsız Endeks Boyutu',
                    text: `Toplam endeks farkı ve çarpana göre hesaplanan tüketim (${hesaplananToplam.toLocaleString('tr-TR', {maximumFractionDigits: 2})} kWh) ile faturadaki tüketim (${totalRow.gelen.toLocaleString('tr-TR', {maximumFractionDigits: 2})} kWh) uyuşmuyor. Çarpan, endeks veya tüketim alanlarından biri hatalı aktarılmış olabilir.`
                });
            }
        }

        if (issues.length === 0) {
            return `<div style="margin-top:14px;padding:14px 16px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:12px;color:#166534;font-weight:700;">
                <i class="fas fa-check-circle"></i> Bu kayıtta belirgin bir endeks hatası tespit edilmedi.
            </div>`;
        }

        var items = issues.map(function(issue) {
            return `<div style="padding:13px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;margin-top:10px;">
                <div style="font-weight:800;color:#b91c1c;margin-bottom:5px;"><i class="fas fa-exclamation-triangle"></i> ${issue.title}</div>
                <div style="font-size:.86rem;color:#7f1d1d;line-height:1.45;">${issue.text}</div>
            </div>`;
        }).join('');

        return `<div style="margin-top:16px;">
            <div style="font-size:.82rem;font-weight:800;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px;">Hata Nedeni</div>
            ${items}
        </div>`;
    }

    // ── Premium Endeks Detay Modal ─────────────────────────────────────
    function emdFmt(n) {
        var num = parseFloat(n) || 0;
        return num.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function openEndeksDetay(data) {
        // Header
        document.getElementById('emd-header-tesisat').textContent = 'Tesisat No: ' + data.tesisat;
        document.getElementById('emd-donem').textContent = data.donem;
        document.getElementById('emd-carpan').textContent = 'x' + data.carpan;
        var emdHistBtn = document.getElementById('emd-history-btn');
        if (emdHistBtn) {
            emdHistBtn.setAttribute('data-tesisat', data.tesisat || '');
            emdHistBtn.setAttribute('data-donem', data.donem || '');
        }

        // Temel Bilgiler
        var infos = [
            ['Fatura No',     data.fatura_no, true],
            ['Sayaç Seri No', data.sayac,     false],
            ['Tarife',        data.tarife,    false],
            ['Bağlantı Gr.',  data.baglanti,  false],
            ['İlk Okuma',     data.ilk_okuma, false],
            ['Son Okuma',     data.son_okuma, false],
            ['Adres',         data.adres,     false],
        ];
        document.getElementById('emd-temel-bilgiler').innerHTML = infos.map(function(r) {
            return '<div class="emd-info-row"><span class="emd-info-key">' + r[0] + '</span><span class="emd-info-val' + (r[2] ? ' mono' : '') + '">' + (r[1] || '—') + '</span></div>';
        }).join('');

        // Endeks Hareketleri
        var endeksHtml = '<div class="emd-endeks-header"><span>Tarife</span><span>İlk</span><span>Son</span><span>Fark</span><span>Tüketim</span><span>Çarpanlı</span></div>';
        data.tarifeler.forEach(function(t) {
            var isNeg = t.fark < 0;
            endeksHtml += '<div class="emd-endeks-row' + (t.ana ? ' ana' : '') + '">' +
                '<span><span class="emd-tarife-lbl">' + t.ad + '</span></span>' +
                '<span>' + emdFmt(t.ilk) + '</span>' +
                '<span>' + emdFmt(t.son) + '</span>' +
                '<span class="emd-fark-val' + (isNeg ? ' neg' : '') + '">' + emdFmt(t.fark) + '</span>' +
                '<span>' + emdFmt(t.gelen) + '</span>' +
                '<span class="emd-gercek-val">' + emdFmt(t.gercek) + '</span>' +
            '</div>';
        });
        ['ri','rc'].forEach(function(key) {
            var r = data.reaktif[key];
            if (r.aktif) {
                var cls = key === 'ri' ? 'ri-lbl' : 'rc-lbl';
                endeksHtml += '<div class="emd-endeks-row reaktif">' +
                    '<span><span class="emd-tarife-lbl ' + cls + '">' + key.toUpperCase() + '<small class="emd-tarife-lbl sub">' + r.tip + '</small></span></span>' +
                    '<span>' + emdFmt(r.ilk) + '</span><span>' + emdFmt(r.son) + '</span>' +
                    '<span class="emd-fark-val">' + emdFmt(r.fark) + '</span><span>—</span><span>—</span>' +
                '</div>';
            }
        });

        document.getElementById('emd-endeks-tablo').innerHTML = endeksHtml;

        // Finans KPIs
        var f = data.finans;
        var kpis = [
            {l:'Trafo Kaybı',      v:emdFmt(f.trafo)+' kWh', c:'#92400e', bg:'#fef3c7'},
            {l:'Ek Tüketim',       v:emdFmt(f.ek)+' kWh',    c:'#475569', bg:'#f1f5f9'},
            {l:'Fatura Tüketimi',  v:emdFmt(f.toplam)+' kWh',c:'#059669', bg:'#f0fdf4'},
            {l:'Birim Fiyat',      v:f.birim>0  ? emdFmt(f.birim)+' ₺'   : '—', c:'#1d4ed8', bg:'#eff6ff'},
            {l:'Dağıtım Birim',    v:f.dagitim>0? emdFmt(f.dagitim)+' ₺' : '—', c:'#6d28d9', bg:'#f5f3ff'},
            {l:'KDV',              v:f.kdv>0    ? '₺ '+emdFmt(f.kdv)     : '—', c:'#374151', bg:'#f9fafb'},
        ];
        document.getElementById('emd-finans-kpis').innerHTML = kpis.map(function(k) {
            return '<div class="emd-kpi-card"><div class="emd-kpi-val" style="color:'+k.c+';background:'+k.bg+'">'+k.v+'</div><div class="emd-kpi-label">'+k.l+'</div></div>';
        }).join('');

        document.getElementById('emd-genel-toplam').innerHTML =
            '<div class="emd-genel-label">Genel Toplam Fatura</div>' +
            '<div class="emd-genel-amount"><small>₺</small> ' + emdFmt(f.genel) + '</div>' +
            '<div class="emd-genel-sub">' +
                '<span>Fatura Tutarı: ₺ ' + emdFmt(f.tutar) + '</span>' +
                (f.birim>0 ? '<span>Birim: ' + emdFmt(f.birim) + ' ₺</span>' : '') +
            '</div>';

        // Analysis Banner
        var a = data.analiz;
        var isErr = a.durum === 'HATALI';
        var detayHtml = '';
        if (a.detaylar && a.detaylar.length > 0) {
            detayHtml = '<div style="margin-top:12px;display:flex;flex-direction:column;gap:8px;">' +
                a.detaylar.map(function(d) {
                    return '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:0.8rem;color:#7f1d1d;line-height:1.45;font-weight:500;"><i class="fas fa-exclamation-circle" style="color:#ef4444;margin-right:6px;"></i>' + d + '</div>';
                }).join('') +
            '</div>';
        }
        document.getElementById('emd-analiz-banner').innerHTML =
            '<div class="emd-banner-inner ' + (isErr ? 'error' : 'success') + '" style="flex-wrap:wrap;">' +
                '<div class="emd-banner-icon" style="background:' + a.renk + '">' +
                    '<i class="fas ' + (isErr ? 'fa-exclamation-triangle' : 'fa-check-circle') + '"></i>' +
                '</div>' +
                '<div><div class="emd-banner-title" style="color:' + a.renk + '">' +
                    (isErr ? 'DİKKAT: ANOMALİ TESPİT EDİLDİ' : 'ANALİZ BAŞARILI') +
                '</div><div class="emd-banner-msg">' + a.durum + ' <span>— ' + a.mesaj + '</span></div></div>' +
                (a.ri_var ? '<div class="emd-reaktif-badge"><i class="fas fa-bolt"></i> Reaktif Ceza Mevcut</div>' : '') +
            '</div>' +
            detayHtml;

        var modal = document.getElementById('endeksDetayModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function() { modal.classList.add('active'); }, 10);
    }

    window.closeEndeksDetay = function() {
        var modal = document.getElementById('endeksDetayModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(function() { modal.style.display = 'none'; }, 280);
    };

    window.closeEndeksGecmis = function() {
        var modal = document.getElementById('endeksGecmisModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(function() { modal.style.display = 'none'; }, 280);
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEndeksDetay();
            closeEndeksGecmis();
        }
    });

    $(document).on('click', '.endeks-detail-btn', function() {
        var $row = $(this).closest('tr');
        var $detailRow = $row.next('tr');
        var $panel = $detailRow.find('.detay-panel');
        if (!$panel.length) return;
        try {
            var data = JSON.parse($panel.attr('data-json') || '{}');
            if (!data.fatura_no) return;
            openEndeksDetay(data);
        } catch(e) { console.error('Endeks detay JSON parse hatası:', e); }
    });

    $(document).on('click', '.endeks-history-btn', function() {
        var tesisat = $(this).attr('data-tesisat');
        var donem = $(this).attr('data-donem');
        if (!tesisat) return;

        Swal.fire({
            title: 'Yükleniyor...',
            text: 'Son 6 ayın endeks verileri getiriliyor.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `{{ url('/raporlar/endeks/gecmis-6-ay') }}/${tesisat}`,
            type: 'GET',
            data: { donem: donem },
            success: function(res) {
                Swal.close();
                if (res.success && res.records && res.records.length > 0) {
                    openEndeksGecmis(res.tesisat_no, res.records);
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Kayıt Bulunamadı',
                        text: 'Bu tesisat numarasına ait geçmiş dönem kaydı bulunamadı.',
                        confirmButtonText: 'Tamam'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'Geçmiş veriler alınırken bir hata oluştu.',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });

    function formatEgmDonem(donemStr) {
        if (!donemStr) return donemStr;
        
        var yil = '', ay = '';
        if (donemStr.includes('-')) {
            var parts = donemStr.split('-');
            yil = parts[0];
            ay = parts[1];
        } else if (donemStr.length >= 6) {
            yil = donemStr.substring(0, 4);
            ay = donemStr.substring(4, 6);
        } else {
            return donemStr;
        }

        var aylar = {
            '01': 'Ocak', '02': 'Şubat', '03': 'Mart', '04': 'Nisan',
            '05': 'Mayıs', '06': 'Haziran', '07': 'Temmuz', '08': 'Ağustos',
            '09': 'Eylül', '10': 'Ekim', '11': 'Kasım', '12': 'Aralık'
        };
        return (aylar[ay] || ay) + ' ' + yil;
    }

    function openEndeksGecmis(tesisat, records) {
        document.getElementById('egm-header-tesisat').textContent = 'Tesisat No: ' + tesisat;

        var tbody = document.getElementById('egm-table-body');
        var html = '';

        records.forEach(function(rec, recIdx) {
            var isLast = recIdx === records.length - 1;

            // ── Dönem başlık satırı ──────────────────────────────────
            html += `<tr class="egm-period-header-row">
                <td colspan="2">
                    <span class="egm-period-label"><i class="far fa-calendar-alt"></i> ${formatEgmDonem(rec.donem)}</span>
                    <span class="egm-period-meta">Çarpan: <strong>x${rec.carpan}</strong></span>
                </td>
                <td colspan="3" style="text-align: right;">
                    <span class="egm-period-tutar"><i class="fas fa-lira-sign"></i> ${emdFmt(rec.tutar)} Fatura Tutarı</span>
                </td>
            </tr>`;

            // ── Tarife satırları ─────────────────────────────────────
            var items = [
                { key: 'T1', label: 'T1', sublabel: 'Gündüz',       data: rec.t1, isAna: false, isReaktif: false },
                { key: 'T2', label: 'T2', sublabel: 'Puant',         data: rec.t2, isAna: false, isReaktif: false },
                { key: 'T3', label: 'T3', sublabel: 'Gece',          data: rec.t3, isAna: false, isReaktif: false },
                { key: 'T0', label: 'T0', sublabel: 'Aktif Toplam', data: rec.t0, isAna: true,  isReaktif: false },
                { key: 'RI', label: 'Rİ', sublabel: 'Endüktif',      data: rec.ri, isAna: false, isReaktif: true  },
                { key: 'RC', label: 'RC', sublabel: 'Kapasitif',     data: rec.rc, isAna: false, isReaktif: true  }
            ];

            items.forEach(function(item, itemIdx) {
                var isLastItem = itemIdx === items.length - 1;
                var isNeg = item.data.fark < 0;

                var lblClass = 'egm-badge';
                if      (item.key === 'T0') lblClass += ' egm-badge-t0';
                else if (item.key === 'T1') lblClass += ' egm-badge-t1';
                else if (item.key === 'T2') lblClass += ' egm-badge-t2';
                else if (item.key === 'T3') lblClass += ' egm-badge-t3';
                else if (item.key === 'RI') lblClass += ' egm-badge-ri';
                else if (item.key === 'RC') lblClass += ' egm-badge-rc';

                var tuketimText = item.isReaktif ? '<span style="color:#94a3b8;">—</span>' : emdFmt(item.data.tuketim);
                var farkColor   = isNeg ? '#dc2626' : '#2563eb';

                var rowCls = item.isAna ? 'egm-row-ana' : (item.isReaktif ? 'egm-row-reaktif' : '');
                var borderCls = (isLastItem && !isLast) ? 'egm-row-sep' : '';

                html += `<tr class="egm-data-row ${rowCls} ${borderCls}">
                    <td class="egm-indicator-cell">
                        <span class="${lblClass}">${item.label}</span>
                        <span class="egm-indicator-sub">${item.sublabel}</span>
                    </td>
                    <td class="egm-num-cell">${emdFmt(item.data.ilk)}</td>
                    <td class="egm-num-cell">${emdFmt(item.data.son)}</td>
                    <td class="egm-num-cell" style="font-weight:800; color:${farkColor};">${emdFmt(item.data.fark)}</td>
                    <td class="egm-num-cell egm-tuketim-cell">${tuketimText}</td>
                </tr>`;
            });
        });

        tbody.innerHTML = html;

        var modal = document.getElementById('endeksGecmisModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function() { modal.classList.add('active'); }, 10);
    }

    $(document).on('click', '.endeks-itiraz-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Faturaya İtiraz Et',
            input: 'textarea',
            inputLabel: 'İtiraz Nedeni',
            inputPlaceholder: 'Neden itiraz ediyorsunuz?',
            showCancelButton: true,
            confirmButtonText: 'İtiraz Et',
            cancelButtonText: 'Vazgeç',
            confirmButtonColor: '#dc2626',
            inputValidator: function(value) {
                if (!value || !value.trim()) {
                    return 'Lütfen itiraz nedenini yazınız.';
                }
            }
        }).then(function(res) {
            if (!res.isConfirmed) return;
            $.post(`{{ url('fatura/itiraz') }}/${id}`, {
                _token: '{{ csrf_token() }}',
                itiraz_aciklamasi: res.value
            }).done(function() {
                Swal.fire('Başarılı', 'İtiraz kaydedildi.', 'success').then(function() {
                    $('#mainFilterForm').submit();
                });
            }).fail(function() {
                Swal.fire('Hata', 'İtiraz kaydedilirken bir sorun oluştu.', 'error');
            });
        });
    });

    window.addEventListener('pageshow', function(e) {
        if (e.persisted) closeExportOverlay();
    });

    /* ─── PDF Karşılaştırma (Modal & Professional Progress) ─── */
    var pdfAnalizFiles = [];
    var pdfAnalizFaturaSet = {};
    var pdfFolderSelected = false;

    // Progress Modal Elements
    const pdfProgModal   = document.getElementById('pdfProgModal');
    const pdfPmFill      = document.getElementById('pdfPmFill');
    const pdfPmStripFill = document.getElementById('pdfPmStripFill');
    const pdfPmStripDot  = document.getElementById('pdfPmStripDot');
    const pdfPmPerc      = document.getElementById('pdfPmPerc');
    const pdfPmHint      = document.getElementById('pdfPmHint');
    const pdfPmIcon      = document.getElementById('pdfPmIcon');
    const pdfPmTitle     = document.getElementById('pdfPmTitle');
    const pdfPmSub       = document.getElementById('pdfPmSub');
    const pdfPmStep1     = document.getElementById('pdfPmStep1');
    const pdfPmStep2     = document.getElementById('pdfPmStep2');
    const pdfPmStep3     = document.getElementById('pdfPmStep3');
    const pdfPmLine1     = document.getElementById('pdfPmLine1');
    const pdfPmLine2     = document.getElementById('pdfPmLine2');

    let pdfDisplayedPct = 0;
    let pdfAnimFrame    = null;

    function setPdfProgress(targetPct, textHint) {
        if (pdfAnimFrame) cancelAnimationFrame(pdfAnimFrame);
        function step() {
            const diff  = targetPct - pdfDisplayedPct;
            const speed = Math.max(0.4, Math.abs(diff) * 0.1);
            if (Math.abs(diff) < 0.3) { pdfDisplayedPct = targetPct; }
            else                       { pdfDisplayedPct += speed; }
            const p      = Math.min(100, pdfDisplayedPct);
            const pRound = Math.round(p);

            pdfPmFill.style.width      = p + '%';
            pdfPmStripFill.style.width = p + '%';
            pdfPmStripDot.style.left   = 'calc(' + p + '% - 6px)';
            pdfPmPerc.textContent      = pRound + '%';
            if (textHint) pdfPmHint.textContent = textHint;

            if (pRound >= 100)     pdfPmPerc.style.color = '#15803d';
            else if (pRound >= 60) pdfPmPerc.style.color = '#059669';
            else                   pdfPmPerc.style.color = '#1a5f8a';

            if (pdfDisplayedPct < targetPct) pdfAnimFrame = requestAnimationFrame(step);
        }
        pdfAnimFrame = requestAnimationFrame(step);
    }

    function setPdfPhase(phase) {
        if (phase === 'fetch') {
            pdfPmStep1.className = 'pm-step pm-step-active';
            pdfPmStep2.className = 'pm-step';
            pdfPmStep3.className = 'pm-step';
            pdfPmLine1.classList.remove('pm-line-done');
            pdfPmLine2.classList.remove('pm-line-done');
            pdfPmIcon.className    = 'pm-icon';
            pdfPmIcon.innerHTML    = '<i class="fas fa-cloud-download-alt"></i>';
            pdfPmTitle.textContent = 'Veriler Alınıyor';
            pdfPmSub.textContent   = 'Sistemdeki hamveri kayıtları getiriliyor...';
        } else if (phase === 'match') {
            pdfPmStep1.className = 'pm-step pm-step-done';
            pdfPmStep2.className = 'pm-step pm-step-active';
            pdfPmStep3.className = 'pm-step';
            pdfPmLine1.classList.add('pm-line-done');
            pdfPmLine2.classList.remove('pm-line-done');
            pdfPmIcon.className    = 'pm-icon pm-icon-proc';
            pdfPmIcon.innerHTML    = '<i class="fas fa-cogs fa-spin"></i>';
            pdfPmTitle.textContent = 'PDF\'ler Eşleştiriliyor';
        } else if (phase === 'done') {
            pdfPmStep1.className = 'pm-step pm-step-done';
            pdfPmStep2.className = 'pm-step pm-step-done';
            pdfPmStep3.className = 'pm-step pm-step-active';
            pdfPmLine1.classList.add('pm-line-done');
            pdfPmLine2.classList.add('pm-line-done');
            pdfPmIcon.className    = 'pm-icon pm-icon-done';
            pdfPmIcon.innerHTML    = '<i class="fas fa-check-double"></i>';
            pdfPmTitle.textContent = 'Analiz Tamamlandı!';
            pdfPmSub.textContent   = 'Sonuçlar hazırlanıyor...';
        }
    }

    function pdfMatchFilename(filename, set) {
        var name = filename.replace(/\.pdf$/i, '').trim();
        if (set[name]) return { pdf: filename, invoice: set[name] };
        
        var alphanums = name.match(/[a-zA-Z0-9]+/g);
        if (alphanums) {
            for (var i = 0; i < alphanums.length; i++) {
                if (set[alphanums[i]]) return { pdf: filename, invoice: set[alphanums[i]] };
            }
        }
        return null;
    }

    window.pdfFaturaDetay = function(efksId) {
        var overlay = document.getElementById('pdfFaturaDetayOverlay');
        var body = document.getElementById('fdoBody');
        document.getElementById('fdoTitle').textContent = 'EFKS ID: ' + efksId;
        body.innerHTML = '<div class="fdo-loading"><i class="fas fa-spinner fa-spin"></i><p>Yükleniyor...</p></div>';
        overlay.classList.add('fdo-active');

        fetch('/raporlar/endeks/pdf-karsilastir/fatura-detay/' + encodeURIComponent(efksId))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) {
                    body.innerHTML = '<div class="text-center py-5 text-danger fw-bold" style="font-size:1.1rem;">Kayıt bulunamadı.</div>';
                    return;
                }

                var html = '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:12px;">';
                data.fields.forEach(function(f) {
                    var val = f.value || '—';
                    var escaped = val.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
                    html += '<div style="background:#fff; border-radius:14px; padding:14px 18px; border:1px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,0.03); display:flex; flex-direction:column; gap:4px; transition:all .2s;" onmouseover="this.style.borderColor=\'#93c5fd\';this.style.boxShadow=\'0 4px 12px rgba(59,130,246,0.1)\'" onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.boxShadow=\'0 2px 6px rgba(0,0,0,0.03)\'">' +
                        '<span style="font-size:.6rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; line-height:1.3;">' + f.key.replace(/_/g, ' ') + '</span>' +
                        '<span style="font-size:.88rem; font-weight:700; color:#0f172a; word-break:break-all;">' + escaped + '</span>' +
                        '</div>';
                });
                html += '</div>';
                if (data.fields.length === 0) {
                    html = '<div class="text-center py-5 text-muted fw-bold">Bu kayıt için hamveri bulunamadı.</div>';
                }
                body.innerHTML = html;
            })
            .catch(function() {
                body.innerHTML = '<div class="text-center py-5 text-danger fw-bold">Bir hata oluştu. Lütfen tekrar deneyin.</div>';
            });
    };

    window.pdfFaturaDetayKapat = function() {
        document.getElementById('pdfFaturaDetayOverlay').classList.remove('fdo-active');
    };

    function showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan) {
        document.getElementById('detayOzetEslesen').textContent = eslesenList.length;
        document.getElementById('detayOzetPdfYok').textContent = sistemdeOlan.length;
        document.getElementById('detayOzetSistemYok').textContent = eslesmeyenPdf.length;

        var tblSistem = document.getElementById('detayliAnalizTableSistemVar');
        var tblPdf    = document.getElementById('detayliAnalizTablePdfVar');
        document.getElementById('detaySayacPdfYok').textContent   = sistemdeOlan.length + ' kayıt';
        document.getElementById('detaySayacSistemYok').textContent = eslesmeyenPdf.length + ' dosya';

        var htmlSistem = '';
        if (sistemdeOlan.length === 0) {
            htmlSistem = '<tr><td colspan="3" class="text-center py-4"><span style="color:#16a34a;font-weight:700;"><i class="fas fa-check-circle"></i> Eksik kayıt yok</span></td></tr>';
        } else {
            sistemdeOlan.forEach(function(inv) {
                var idEscaped = inv.id.replace(/'/g, "\\'");
                htmlSistem += '<tr style="border-bottom:1px solid #f1f5f9; transition:background .15s;" onmouseover="this.style.background=\'#fef2f2\'" onmouseout="this.style.background=\'\'">' +
                    '<td style="padding:14px 18px; font-weight:800; color:#334155; font-size:.8rem;">' + inv.id + '</td>' +
                    '<td style="padding:14px 18px; font-weight:700; color:#0f172a; font-size:.85rem;">' + (inv.tutar || '-') + '</td>' +
                    '<td style="padding:14px 18px;"><button class="btn" style="padding:4px 10px; border-radius:8px; background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff; font-weight:700; font-size:.72rem; border:none; cursor:pointer;" onclick="pdfFaturaDetay(\'' + idEscaped + '\')"><i class="fas fa-eye mr-1"></i> Detay</button></td>' +
                    '</tr>';
            });
        }
        tblSistem.innerHTML = htmlSistem;

        var htmlPdf = '';
        if (eslesmeyenPdf.length === 0) {
            htmlPdf = '<tr><td colspan="2" class="text-center py-4"><span style="color:#16a34a;font-weight:700;"><i class="fas fa-check-circle"></i> Eksik dosya yok</span></td></tr>';
        } else {
            eslesmeyenPdf.forEach(function(pdfName) {
                var nameEscaped = pdfName.replace(/'/g, "\\'");
                htmlPdf += '<tr style="border-bottom:1px solid #f1f5f9; transition:background .15s;" onmouseover="this.style.background=\'#fffbeb\'" onmouseout="this.style.background=\'\'">' +
                    '<td style="padding:14px 18px; font-family:monospace; font-size:.8rem; font-weight:700; color:#ea580c; word-break:break-all;">' + nameEscaped + '</td>' +
                    '<td style="padding:14px 18px;"><span style="background:rgba(245,158,11,0.12); color:#d97706; padding:4px 10px; border-radius:8px; font-weight:700; font-size:.72rem; white-space:nowrap;"><i class="fas fa-times-circle mr-1"></i> Eşleşmedi</span></td>' +
                    '</tr>';
            });
        }
        tblPdf.innerHTML = htmlPdf;

        if (!eslesmeyenPdf.length && !sistemdeOlan.length) {
            document.querySelector('#pdfDetayliSonucModal .modal-body .row').innerHTML =
                '<div class="col-12 text-center" style="padding:60px 20px;">' +
                '<div style="display:inline-block; padding:30px 40px; background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(5,150,105,0.05)); border-radius:24px; border:1px solid rgba(16,185,129,0.2);">' +
                '<i class="fas fa-check-circle" style="font-size:3.5rem; color:#10b981; margin-bottom:15px; filter:drop-shadow(0 10px 15px rgba(16,185,129,0.3));"></i>' +
                '<h4 style="color:#059669; font-weight:800; margin:0; font-size:1.4rem;">Kusursuz Eşleşme!</h4>' +
                '<p style="color:#047857; margin-top:8px; font-weight:500; opacity:0.8;">Tüm klasördeki PDF dosyaları sistemdeki faturalarla eksiksiz eşleşti.</p></div></div>';
        }

        $('#pdfAnalizModal').modal('hide');
        setTimeout(function(){
            $('#pdfDetayliSonucModal').modal('show');
        }, 400);
    }

    document.getElementById('pdfFolderInput').addEventListener('change', function(e) {
        var files = e.target.files;
        if (!files.length) return;

        var pdfs = [];
        for (var i = 0; i < files.length; i++) {
            if (files[i].name.toLowerCase().endsWith('.pdf')) {
                pdfs.push(files[i].name);
            }
        }

        if (!pdfs.length) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Seçilen klasörde PDF dosyası bulunamadı.', confirmButtonText: 'Tamam' });
            return;
        }

        pdfAnalizFiles = pdfs;
        pdfFolderSelected = true;
        document.getElementById('pdfAnalizBaslaBtn').disabled = false;

        document.getElementById('pdfAnalizFolderInfo').style.display = 'block';
        document.getElementById('pdfAnalizFileCount').textContent = pdfs.length;
        document.getElementById('pdfDropZone').style.borderColor = '#3b82f6';
        document.getElementById('pdfDropZone').style.background = '#eff6ff';
    });

    document.getElementById('pdfAnalizBaslaBtn').addEventListener('click', function() {
        if (!pdfFolderSelected || !pdfAnalizFiles.length) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen önce Gözat ile bir klasör seçin.', confirmButtonText: 'Tamam' });
            return;
        }

        var donem = document.getElementById('pdfAnalizDonem').value;
        if (!donem) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen önce karşılaştırma dönemi seçin.', confirmButtonText: 'Tamam' });
            return;
        }

        pdfDisplayedPct = 0;
        setPdfProgress(0, '0 / 0');
        setPdfPhase('fetch');
        pdfProgModal.classList.add('pm-show');

        fetch('/raporlar/endeks/pdf-karsilastir/faturalar/' + encodeURIComponent(donem))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error('Veri alınamadı');

                pdfAnalizFaturaSet = data.faturalar; // already object (efks => details)

                setPdfPhase('match');
                pdfPmSub.textContent = pdfAnalizFiles.length + ' adet PDF dosyası analiz ediliyor...';
                
                var eslesenList = [];
                var eslesmeyenPdf = [];
                var toplam = pdfAnalizFiles.length;
                var sira = 0;

                function sonraki() {
                    if (sira >= toplam) {
                        var eslenenSet = {};
                        eslesenList.forEach(function(e) { eslenenSet[e.invoice.id] = true; });
                        
                        var sistemdeOlan = [];
                        Object.keys(data.faturalar).forEach(function(k) {
                            if (!eslenenSet[k]) {
                                sistemdeOlan.push(data.faturalar[k]);
                            }
                        });

                        setPdfPhase('done');
                        setPdfProgress(100, toplam + ' / ' + toplam + ' tamamlandı');
                        
                        setTimeout(function() {
                            pdfProgModal.classList.remove('pm-show');
                            showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan);
                        }, 1200);
                        return;
                    }

                    var chunkEnd = Math.min(sira + 20, toplam);
                    for (; sira < chunkEnd; sira++) {
                        var fname = pdfAnalizFiles[sira];
                        var matched = pdfMatchFilename(fname, pdfAnalizFaturaSet);
                        if (matched) {
                            eslesenList.push(matched);
                        } else {
                            eslesmeyenPdf.push(fname);
                        }
                    }

                    var pct = Math.round((sira / toplam) * 100);
                    setPdfProgress(pct, sira + ' / ' + toplam + ' analiz edildi');
                    setTimeout(sonraki, 20);
                }

                sonraki();
            })
            .catch(function(err) {
                console.error(err);
                pdfProgModal.classList.remove('pm-show');
                Swal.fire({ icon: 'error', title: 'Hata', text: 'Hamveri kayıtları alınırken bir hata oluştu.', confirmButtonText: 'Tamam' });
            });
    });

});
</script>
@endpush

{{-- ═══ Premium Endeks Detay Modal HTML ═══ --}}
<div id="endeksDetayModal">
    <div class="emd-card">
        {{-- Header --}}
        <div class="emd-header">
            <div class="emd-header-left">
                <div class="emd-header-icon"><i class="fas fa-microscope"></i></div>
                <div>
                    <div class="emd-eyebrow">Detaylı Endeks Analizi</div>
                    <div class="emd-title-row">
                        <span id="emd-header-tesisat" class="emd-fatura-badge">Tesisat No: —</span>
                        <span class="emd-sep">|</span>
                        <span id="emd-donem" class="emd-donem-pill">—</span>
                        <button type="button" class="emd-close-btn endeks-history-btn" title="Son 6 Ay" id="emd-history-btn" style="background:rgba(167,139,250,0.15);border-color:rgba(167,139,250,0.3);color:#fff;width:auto;padding:0 14px;gap:6px;font-size:0.8rem;font-weight:700;">
                            <i class="fas fa-history"></i> <span>Son 6 Aylık Veri</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="emd-header-right">
                <div class="emd-carpan-pill">
                    <small>Çarpan</small>
                    <span id="emd-carpan">x1</span>
                </div>
                <button onclick="closeEndeksDetay()" class="emd-close-btn"><i class="fas fa-times"></i></button>
            </div>
        </div>

        {{-- Body --}}
        <div class="emd-body">
            {{-- Row 1: Temel Bilgiler + Endeks Tablosu --}}
            <div class="emd-top-grid">
                <div class="emd-section-card">
                    <div class="emd-section-title"><i class="fas fa-id-card-alt"></i> TEMEL BİLGİLER</div>
                    <div id="emd-temel-bilgiler"></div>
                </div>
                <div class="emd-section-card">
                    <div class="emd-section-title"><i class="fas fa-chart-line"></i> ENDEKS HAREKETLERİ</div>
                    <div id="emd-endeks-tablo"></div>
                </div>
            </div>

            {{-- Row 2: Finans KPIs + Toplam --}}
            <div class="emd-section-card">
                <div class="emd-section-title"><i class="fas fa-lira-sign"></i> FİNANS ÖZETİ</div>
                <div style="display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start;">
                    <div id="emd-finans-kpis" class="emd-finans-grid"></div>
                    <div id="emd-genel-toplam" class="emd-genel-card" style="min-width:220px;"></div>
                </div>
            </div>
        </div>

        {{-- Analysis Banner --}}
        <div id="emd-analiz-banner" class="emd-banner"></div>
    </div>
</div>

{{-- ═══ Premium Endeks Geçmiş 6 Ay Modal HTML ═══ --}}
<div id="endeksGecmisModal">
    <div class="emd-card" style="max-width: 860px;">
        {{-- Header --}}
        <div class="emd-header" style="background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);">
            <div class="emd-header-left">
                <div class="emd-header-icon" style="background:rgba(167,139,250,0.15); border-color:rgba(167,139,250,0.3); color:#a78bfa;"><i class="fas fa-history"></i></div>
                <div>
                    <div class="emd-eyebrow" style="color:#a78bfa;">Endeks Geçmişi</div>
                    <div class="emd-title-row">
                        <span id="egm-header-tesisat" class="emd-fatura-badge">Tesisat No: —</span>
                        <span class="emd-sep">|</span>
                        <span class="emd-donem-pill" style="background:rgba(139,92,246,0.15); border-color:rgba(139,92,246,0.3); color:#c084fc;">Son 6 Ay Verisi</span>
                    </div>
                </div>
            </div>
            <div class="emd-header-right">
                <button onclick="closeEndeksGecmis()" class="emd-close-btn"><i class="fas fa-times"></i></button>
            </div>
        </div>

        {{-- Body --}}
        <div class="emd-body" style="background:#f8fafc; padding:0;">
            <div style="overflow-x:auto;">
                <table class="egm-main-table">
                    <thead>
                        <tr>
                            <th style="width:180px;">Gösterge</th>
                            <th style="text-align:right;">İlk Endeks</th>
                            <th style="text-align:right;">Son Endeks</th>
                            <th style="text-align:right;">Fark</th>
                            <th style="text-align:right;">Tüketim (kWh)</th>
                        </tr>
                    </thead>
                    <tbody id="egm-table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* ══════════════════════════════════════════════════════
   Progress Modal — Version-1 Premium Design
   ══════════════════════════════════════════════════════ */
.pm-backdrop {
  position:fixed;inset:0;z-index:999999;
  background:rgba(5,14,26,.86);
  backdrop-filter:blur(10px) saturate(1.4);
  display:flex;align-items:center;justify-content:center;padding:1rem;
  opacity:0;pointer-events:none;
  transition:opacity .35s cubic-bezier(.4,0,.2,1);
}
.pm-backdrop.pm-show{opacity:1;pointer-events:auto;}

.pm-box{
  background:#fff;border-radius:24px;
  box-shadow:0 40px 100px rgba(0,0,0,.38),0 0 0 1px rgba(255,255,255,.05);
  width:100%;max-width:440px;overflow:hidden;
  transform:translateY(40px) scale(.92);
  transition:transform .42s cubic-bezier(.34,1.38,.64,1);
}
.pm-backdrop.pm-show .pm-box{transform:translateY(0) scale(1);}

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

.pro-table-pdf {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}
.pro-table-pdf th {
    color: #64748b;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    padding: 0 20px 10px 20px;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
}
.pro-table-pdf td {
    padding: 16px 20px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-style: solid none;
    transition: all 0.2s;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}
.pro-table-pdf tr td:first-child {
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
    border-left: 1px solid #f1f5f9;
}
.pro-table-pdf tr td:last-child {
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
    border-right: 1px solid #f1f5f9;
}
.pro-table-pdf tr:hover td {
    background: #eff6ff;
}
.badge-pdf {
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.badge-pdf.success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-pdf.error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.badge-pdf.warning { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }

.drop-zone-premium {
    border: 2px dashed #e2e8f0;
    border-radius: 20px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-align: center;
}
.drop-zone-premium:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: scale(1.02);
}
.drop-zone-premium .upload-icon-box {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #3b82f6;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 16px;
}
</style>

{{-- ═══ Fatura Detay Overlay ═══ --}}
<div id="pdfFaturaDetayOverlay">
    <div class="fdo-card">
        <div class="fdo-header">
            <div class="fdo-header-left">
                <div class="fdo-icon"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <div class="fdo-eyebrow">Fatura Detayı</div>
                    <div class="fdo-title" id="fdoTitle">EFKS ID: —</div>
                </div>
            </div>
            <button onclick="pdfFaturaDetayKapat()" class="fdo-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="fdo-body" id="fdoBody">
            <div class="fdo-loading"><i class="fas fa-spinner fa-spin"></i><p>Yükleniyor...</p></div>
        </div>
    </div>
</div>
<style>
#pdfFaturaDetayOverlay {
    display:none; position:fixed; inset:0; z-index:99999;
    background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
    align-items:center; justify-content:center; padding:30px;
}
#pdfFaturaDetayOverlay.fdo-active { display:flex; }
.fdo-card {
    background:#fff; border-radius:28px; width:100%; max-width:920px; max-height:85vh;
    display:flex; flex-direction:column; overflow:hidden;
    box-shadow:0 40px 80px rgba(0,0,0,0.3); animation:fadeScaleIn .3s ease;
}
.fdo-header {
    background:linear-gradient(135deg,#0f172a,#1e3a5f);
    padding:20px 28px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;
}
.fdo-header-left { display:flex; align-items:center; gap:14px; }
.fdo-icon {
    width:42px; height:42px; border-radius:12px;
    background:rgba(96,165,250,0.15); border:1px solid rgba(96,165,250,0.3);
    display:flex; align-items:center; justify-content:center; color:#60a5fa; font-size:1.1rem;
}
.fdo-eyebrow { font-size:.65rem; font-weight:800; color:#60a5fa; text-transform:uppercase; letter-spacing:.12em; margin-bottom:3px; }
.fdo-title { font-size:1rem; font-weight:800; color:#fff; letter-spacing:-.02em; }
.fdo-close {
    width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.15);
    background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.6);
    display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.9rem;
}
.fdo-close:hover { background:rgba(239,68,68,0.2); border-color:rgba(239,68,68,0.4); color:#f87171; }
.fdo-body { flex:1; overflow-y:auto; padding:24px 28px; background:#f8fafc; }
.fdo-loading { text-align:center; padding:60px 0; }
.fdo-loading i { font-size:2.2rem; color:#3b82f6; }
.fdo-loading p { margin-top:12px; font-weight:700; color:#64748b; }
</style>

{{-- ═══ Pdf-Fatura Analiz Modal HTML ═══ --}}
<div class="modal fade" id="pdfAnalizModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.25); background: rgba(255, 255, 255, 0.98);">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:25px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.35rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(239,68,68,0.2);border-radius:12px;margin-right:12px;color:#f87171;"><i class="fas fa-file-pdf"></i></div>
                        Pdf-Fatura Analiz (Eşleştirme)
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Klasördeki PDF dosyalarını sistemdeki yüklenmiş hamverilerle tek tek eşleştirin.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:35px; max-height:75vh; overflow-y:auto; background:#f4f6f9;">
                
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="form-group-pro">
                            <label><i class="far fa-calendar-alt"></i> Karşılaştırma Dönemi</label>
                            <select id="pdfAnalizDonem" class="form-control-pro" style="height:50px;font-size:1rem;border-radius:14px;">
                                <option value="">Dönem Seçiniz</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top: 20px;">
                            <button type="button" id="pdfAnalizBaslaBtn" class="btn-pro w-100 justify-content-center" style="background: linear-gradient(135deg, #2563eb, #4f46e5); color: #fff; height: 55px; border-radius: 16px; font-size: 1.05rem; box-shadow: 0 10px 25px -5px rgba(37,99,235,0.4);" disabled>
                                <i class="fas fa-play-circle mr-2"></i> Analize Başla
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="drop-zone-premium" id="pdfDropZone" onclick="document.getElementById('pdfFolderInput').click()">
                            <div class="upload-icon-box"><i class="fas fa-folder-open"></i></div>
                            <h5 style="font-weight: 800; color: #1e293b; margin-bottom: 4px;">PDF Klasörünü Seçin</h5>
                            <p style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Tıklayarak içerisinde PDF'lerin bulunduğu klasörü seçin</p>
                            <input type="file" id="pdfFolderInput" webkitdirectory multiple style="display:none;">
                            
                            <div id="pdfAnalizFolderInfo" style="display:none; margin-top: 15px; padding: 10px 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">
                                <i class="fas fa-file-pdf"></i> <span id="pdfAnalizFileCount">0</span> PDF seçildi
                            </div>
                        </div>
                    </div>
                </div>

                </div>

            </div>
        </div>
    </div>
</div>

<div id="pdfProgModal" class="pm-backdrop">
  <div class="pm-box">
    <div class="pm-strip">
      <div class="pm-strip-fill" id="pdfPmStripFill"></div>
      <div class="pm-strip-dot"  id="pdfPmStripDot"></div>
    </div>
    <div class="pm-body">
      <div class="pm-icon-wrap">
        <div class="pm-icon-ring"></div>
        <div class="pm-icon-ring pm-ring2"></div>
        <div class="pm-icon" id="pdfPmIcon"><i class="fas fa-sync-alt fa-spin"></i></div>
      </div>
      <div class="pm-title" id="pdfPmTitle">Analiz Yapılıyor</div>
      <div class="pm-sub"   id="pdfPmSub">Sistem kayıtları alınıyor...</div>
      <div class="pm-track">
        <div class="pm-fill" id="pdfPmFill"><div class="pm-shine"></div></div>
      </div>
      <div class="pm-perc-row">
        <span class="pm-perc" id="pdfPmPerc">0%</span>
        <span class="pm-hint" id="pdfPmHint">0 / 0 dosya</span>
      </div>
      <div class="pm-steps">
        <div class="pm-step pm-step-active" id="pdfPmStep1">
          <div class="pm-step-dot"></div><span>Sistem Verisi</span>
        </div>
        <div class="pm-step-line" id="pdfPmLine1"></div>
        <div class="pm-step" id="pdfPmStep2">
          <div class="pm-step-dot"></div><span>Eşleştirme</span>
        </div>
        <div class="pm-step-line" id="pdfPmLine2"></div>
        <div class="pm-step" id="pdfPmStep3">
          <div class="pm-step-dot"></div><span>Tamamlandı</span>
        </div>
      </div>
    </div>
  </div>
</div>



{{-- ═══ Detaylı Sonuçlar Modalı ═══ --}}
<div class="modal fade" id="pdfDetayliSonucModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.7);">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 86%;">
        <div class="modal-content" style="border-radius:32px; border:1px solid rgba(255,255,255,0.15); overflow:hidden; box-shadow:0 50px 100px -20px rgba(0,0,0,0.5), inset 0 1px 1px rgba(255,255,255,0.3); background: #f8fafc;">
            
            <div class="modal-header" style="background:linear-gradient(135deg, #0f172a, #1e1b4b); border:none; padding:30px 45px; position: relative; overflow:hidden;">
                <!-- Premium Gloss Effect -->
                <div style="position:absolute; top:-50%; left:-50%; width:200%; height:200%; background:radial-gradient(circle at top left, rgba(255,255,255,0.08) 0%, transparent 60%); pointer-events:none;"></div>
                
                <div style="position:relative; z-index:1;">
                    <h5 class="modal-title d-flex align-items-center" style="color:#fff; font-weight:800; font-size:1.6rem; margin:0; letter-spacing:-0.03em; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                        <div style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; background:linear-gradient(135deg, rgba(59,130,246,0.3), rgba(37,99,235,0.1)); border: 1px solid rgba(96,165,250,0.3); border-radius:14px; margin-right:16px; color:#60a5fa; box-shadow: 0 10px 20px -5px rgba(59,130,246,0.4);">
                            <i class="fas fa-layer-group" style="font-size:1.2rem;"></i>
                        </div>
                        Detaylı Uyumsuzluk Raporu
                    </h5>
                    <p style="color:#94a3b8; font-size:0.95rem; margin:10px 0 0 64px; font-weight:500;">Klasördeki dosyalar ile sistem faturalarının eşleşme özetleri ve uyumsuzluk listesi.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.9; font-size:1.8rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.3s; margin-top:-15px; position:relative; z-index:1;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='rotate(90deg)';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.transform='rotate(0deg)';">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:40px 45px; max-height:78vh; overflow-y:auto; background:linear-gradient(to bottom, #f8fafc, #f1f5f9);">
                
                <div class="row mb-5">
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(16,185,129,0.05);"><i class="fas fa-check-circle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Başarıyla Eşleşen</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#10b981; line-height:1;" id="detayOzetEslesen">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#10b981; font-weight:600; opacity:0.8;">Fatura</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(239,68,68,0.05);"><i class="fas fa-times-circle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Sistemde Var, PDF Yok</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#ef4444; line-height:1;" id="detayOzetPdfYok">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#ef4444; font-weight:600; opacity:0.8;">Kayıt</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(245,158,11,0.05);"><i class="fas fa-exclamation-triangle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">PDF Var, Sistemde Yok</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#f59e0b; line-height:1;" id="detayOzetSistemYok">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#f59e0b; font-weight:600; opacity:0.8;">Dosya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Sol Tablo: Sistemde Var, PDF Yok --}}
                    <div class="col-md-6">
                        <div style="background:#fff; border-radius:24px; box-shadow:0 20px 40px -12px rgba(0,0,0,0.06); border:1px solid #fce7f3; overflow:hidden; height:100%;">
                            <div style="padding:16px 20px; border-bottom:2px solid #fce7f3; background:#fffafb; display:flex; align-items:center; gap:10px;">
                                <div style="width:10px; height:10px; border-radius:50%; background:#ef4444; box-shadow:0 0 8px rgba(239,68,68,0.4);"></div>
                                <h6 style="margin:0; font-weight:800; color:#dc2626; font-size:.85rem; letter-spacing:-.01em;">Sistemde Var, PDF Yok</h6>
                                <span style="margin-left:auto; font-size:.7rem; font-weight:700; color:#94a3b8;" id="detaySayacPdfYok">0 kayıt</span>
                            </div>
                            <div class="table-responsive">
                                <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
                                    <thead>
                                        <tr style="background:#fef2f2;">
                                            <th style="padding:12px 18px; color:#991b1b; font-weight:800; font-size:.7rem; text-transform:uppercase; letter-spacing:0.3px;">EFKS ID</th>
                                            <th style="padding:12px 18px; color:#991b1b; font-weight:800; font-size:.7rem; text-transform:uppercase; letter-spacing:0.3px;">Tutar</th>
                                            <th style="padding:12px 18px; color:#991b1b; font-weight:800; font-size:.7rem; text-transform:uppercase; letter-spacing:0.3px;">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detayliAnalizTableSistemVar"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- Sağ Tablo: PDF Var, Sistemde Yok --}}
                    <div class="col-md-6">
                        <div style="background:#fff; border-radius:24px; box-shadow:0 20px 40px -12px rgba(0,0,0,0.06); border:1px solid #fef3c7; overflow:hidden; height:100%;">
                            <div style="padding:16px 20px; border-bottom:2px solid #fef3c7; background:#fefce8; display:flex; align-items:center; gap:10px;">
                                <div style="width:10px; height:10px; border-radius:50%; background:#f59e0b; box-shadow:0 0 8px rgba(245,158,11,0.4);"></div>
                                <h6 style="margin:0; font-weight:800; color:#d97706; font-size:.85rem; letter-spacing:-.01em;">PDF Var, Sistemde Yok</h6>
                                <span style="margin-left:auto; font-size:.7rem; font-weight:700; color:#94a3b8;" id="detaySayacSistemYok">0 dosya</span>
                            </div>
                            <div class="table-responsive">
                                <table style="width:100%; border-collapse:collapse; font-size:.85rem;">
                                    <thead>
                                        <tr style="background:#fffbeb;">
                                            <th style="padding:12px 18px; color:#92400e; font-weight:800; font-size:.7rem; text-transform:uppercase; letter-spacing:0.3px;">PDF Dosya Adı</th>
                                            <th style="padding:12px 18px; color:#92400e; font-weight:800; font-size:.7rem; text-transform:uppercase; letter-spacing:0.3px;">Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detayliAnalizTablePdfVar"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer" style="border-top:1px solid rgba(0,0,0,0.05); padding:25px 45px; background:linear-gradient(to right, #f8fafc, #fff);">
                <button type="button" class="btn" data-dismiss="modal" style="border-radius:16px; font-weight:800; padding:12px 32px; background:#fff; color:#475569; border:2px solid #e2e8f0; font-size:1.05rem; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); transition:all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#0f172a'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.transform='translateY(0)';">Pencereyi Kapat</button>
            </div>
        </div>

    </div>
</div>

@endsection