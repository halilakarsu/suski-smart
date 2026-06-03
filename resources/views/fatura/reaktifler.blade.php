@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Reactive Archive */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --danger-gradient: linear-gradient(135deg, #dc2626, #ef4444);
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }

    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px;
        color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, transparent 70%);
        top: -200px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none; z-index: 1;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(1.2); opacity: 0.9; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    /* Main Container */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Stats Grid */
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
    .stat-c2::before { background: #ef4444; }
    .stat-c3::before { background: #0ea5e9; }

    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; z-index: 2; }
    .stat-title { font-size: 0.75rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-icon {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .ic1 { background: linear-gradient(135deg, #60a5fa, #2563eb); }
    .ic2 { background: linear-gradient(135deg, #f87171, #ef4444); }
    .ic3 { background: linear-gradient(135deg, #38bdf8, #0ea5e9); }

    .stat-value { font-size: 1.9rem; font-weight: 800; color: var(--text-slate-900); z-index: 2; margin-top: auto; line-height: 1; }
    .stat-desc { font-size: 0.75rem; color: var(--text-slate-500); font-weight: 600; margin-top: 4px; z-index: 2; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }

    /* Table Design */
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
    .table-pro th { color: #94a3b8; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 15px; background: #f8fafc; border: none; }
    .table-pro td { background: #fff; padding: 12px 15px; vertical-align: middle; border: none; font-size: 0.85rem; font-weight: 500; }
    .table-pro tr td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .table-pro tr td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    .table-pro tr:hover td { background: #f1f5f9; }

    /* Badge */
    .badge-reaktif-pro { background: #fff1f2; color: #be123c; padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; border: 1px solid #fda4af; }

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
        top: 0; left: -100%;
        width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
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
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Reaktif Arşivi</h1>
                <p class="hero-subtitle">Reaktif ceza tespit edilerek onaylanmış faturaların geçmişi.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('fatura.itirazlar') }}" class="btn-premium btn-premium-outline">
                    <i class="fas fa-exclamation-triangle"></i> İtirazlar
                </a>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- TOP STATS -->
        <div class="stats-grid">
            <div class="stat-card stat-c2">
                <div class="stat-header">
                    <div class="stat-title">İtirazlar</div>
                    <div class="stat-icon ic2"><i class="fas fa-exclamation-circle"></i></div>
                </div>
                <div class="stat-value">{{ number_format($stats['itiraz'] ?? 0) }}</div>
                <div class="stat-desc">İtiraz edilen faturalar</div>
            </div>

            <div class="stat-card stat-c1">
                <div class="stat-header">
                    <div class="stat-title">Toplam Reaktif</div>
                    <div class="stat-icon ic1"><i class="fas fa-bolt"></i></div>
                </div>
                <div class="stat-value">{{ number_format($stats['reaktif'] ?? 0) }}</div>
                <div class="stat-desc">Reaktif cezalı kayıtlar</div>
            </div>

            <div class="stat-card stat-c3">
                <div class="stat-header">
                    <div class="stat-title">Bekleyen</div>
                    <div class="stat-icon ic3"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-value">{{ number_format($stats['bekleyen'] ?? 0) }}</div>
                <div class="stat-desc">Bekleme havuzundaki kayıtlar</div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="glass-card" style="padding: 15px 25px;">
            <form action="{{ route('reaktifler.index') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Dönem</label>
                    <select name="donem" class="form-control" style="border-radius: 12px; padding: 10px;">
                        <option value="">Tüm Dönemler</option>
                        @foreach($donemler as $d)
                            <option value="{{ $d }}" {{ request('donem') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Tesisat No</label>
                    <input type="text" name="tesisat_no" class="form-control" value="{{ request('tesisat_no') }}" style="border-radius: 12px; padding: 10px;" placeholder="Ara...">
                </div>
                <div class="col-md-3">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Fatura No</label>
                    <input type="text" name="fatura_no" class="form-control" value="{{ request('fatura_no') }}" style="border-radius: 12px; padding: 10px;" placeholder="Ara...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn-premium btn-premium-primary flex-grow-1"><i class="fas fa-filter"></i> Filtrele</button>
                    <a href="{{ route('reaktifler.index') }}" class="btn-premium btn-premium-simple"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>

        <!-- MAIN TABLE -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title-pro mb-0"><i class="fas fa-archive"></i> Arşivlenen Reaktif Kayıtları</h5>
                <span style="font-size: 0.8rem; font-weight: 700; color: #94a3b8;">{{ $reaktifler->total() }} kayıt</span>
            </div>

            <div class="table-responsive">
                <table class="table-pro">
                    <thead>
                        <tr>
                            <th>Dönem</th>
                            <th>Tesisat No</th>
                            <th>Fatura No</th>
                            <th>Abone / Firma</th>
                            <th style="text-align: right;">Reaktif Ceza</th>
                            <th style="text-align: right;">Genel Toplam</th>
                            <th style="text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reaktifler as $item)
                            <tr>
                                <td><span style="font-weight: 800; color: #2563eb;">{{ $item->donem }}</span></td>
                                <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700;">{{ $item->tesisat_no }}</code></td>
                                <td style="font-weight: 600;">{{ $item->fatura_no }}</td>
                                <td>
                                    <div style="font-weight: 700; color: #1e293b;">{{ $item->hesap_adi }}</div>
                                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">{{ $item->dagitim }}</div>
                                </td>
                                <td style="text-align: right;"><span class="badge-reaktif-pro">₺{{ number_format($item->reaktif_tl, 2, ',', '.') }}</span></td>
                                <td style="text-align: right; font-weight: 800; color: #16a34a;">₺{{ number_format($item->genel_toplam, 2, ',', '.') }}</td>
                                <td style="text-align: right;">
                                    <button class="btn-premium btn-premium-simple" style="padding: 8px 16px; font-size: 0.75rem;" onclick="showDetail({{ $item->id }})">
                                        <i class="fas fa-eye"></i> Detay
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox mb-3" style="font-size: 3rem; color: #e2e8f0; display: block;"></i>
                                    <p style="font-weight: 600; color: #94a3b8;">Arşivde kayıt bulunamadı.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reaktifler->hasPages())
                <div style="margin-top: 25px;">{{ $reaktifler->links('pagination::bootstrap-4') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- DETAIL MODAL -->
<div id="detMdl" style="position:fixed;inset:0;background:rgba(15,23,42,0.85);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(10px);">
    <div style="background:#fff;border-radius:32px;width:100%;max-width:900px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 80px rgba(0,0,0,0.35);overflow:hidden;">
        <div style="padding:25px 35px;background:var(--primary-gradient);display:flex;justify-content:space-between;align-items:center;color:#fff;">
            <div>
                <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.8;font-weight:800;margin-bottom:4px;">Fatura Arşiv Detayı</div>
                <h2 id="detTit" style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;">-</h2>
            </div>
            <button onclick="closeDetail()" style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div style="flex:1;overflow-y:auto;padding:35px;background:#f8fafc;">
            <div id="detGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:15px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const rData = @json($reaktifler->getCollection()->keyBy('id'));

    function showDetail(id) {
        const item = rData[id];
        if(!item) return;

        const formatDate = (dateStr) => {
            if (!dateStr || dateStr === '–') return '–';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                const d = date.toLocaleDateString('tr-TR');
                const h = date.getHours();
                const m = date.getMinutes();
                if (h === 0 && m === 0) return d;
                const t = date.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
                return d + ' ' + t;
            } catch(e) { return dateStr; }
        };

        document.getElementById('detTit').innerText = item.fatura_no + ' (' + item.tesisat_no + ')';
        let h = '';
        
        const displayData = item.payload || item;
        Object.entries(displayData).forEach(([k, v]) => {
            if (typeof v === 'object' || !v || k.startsWith('_')) return;
            let lbl = k.toString().replace(/_/g, ' ').toUpperCase();
            let val = v;

            // Format Currency
            if(['tutar', 'tl', 'toplam', 'bedel', 'fiyat', 'kdv'].some(x => k.toLowerCase().includes(x))) {
                val = '₺' + parseFloat(val).toLocaleString('tr-TR', {minimumFractionDigits:2});
            }
            // Format Dates
            else if (['tarih', 'okuma', 'son_odeme'].some(x => k.toLowerCase().includes(x))) {
                val = formatDate(val);
            }
            
            h += `<div style="background:#fff;padding:15px;border-radius:16px;border:1px solid #f1f5f9;">
                    <div style="font-size:0.65rem;font-weight:800;color:#94a3b8;margin-bottom:4px;">${lbl}</div>
                    <div style="font-size:0.9rem;font-weight:700;color:#0f172a;">${val}</div>
                  </div>`;
        });

        document.getElementById('detGrid').innerHTML = h;
        document.getElementById('detMdl').style.display = 'flex';
    }

    function closeDetail() { document.getElementById('detMdl').style.display = 'none'; }
    window.onclick = function(e) { if (e.target == document.getElementById('detMdl')) closeDetail(); }
</script>
@endpush
@endsection
