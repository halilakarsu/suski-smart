@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Subscriber Detail */
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

    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px;
        color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; font-weight: 500; }

    /* Main Container */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }

    .card-title-pro { font-size: 1.1rem; font-weight: 800; color: var(--text-slate-900); display: flex; align-items: center; gap: 12px; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .card-title-pro i { color: #3b82f6; }

    .info-list { display: grid; gap: 20px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 0.75rem; font-weight: 800; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 1.05rem; font-weight: 700; color: var(--text-slate-900); }
    .info-value.mono { font-family: monospace; color: #2563eb; font-size: 1.2rem; }

    .badge-status { padding: 6px 14px; border-radius: 10px; font-weight: 800; font-size: 0.75rem; }
    .status-active { background: #f0fdf4; color: #16a34a; }
    .status-passive { background: #fef2f2; color: #dc2626; }

    /* History Timeline */
    .timeline-pro { position: relative; padding-left: 30px; }
    .timeline-pro::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-item { position: relative; margin-bottom: 25px; }
    .timeline-item::before { content: ''; position: absolute; left: -25px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #3b82f6; border: 3px solid #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .timeline-content { background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #f1f5f9; }

    .btn-pro {
        padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-outline-pro { background: rgba(255,255,255,0.1); color: white !important; border: 1px solid rgba(255,255,255,0.2); }
    .btn-outline-pro:hover { background: rgba(255,255,255,0.2); }

    /* ═══ Geçmiş Modal Stilleri ═══ */
    #endeksGecmisModal {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.7); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
        align-items:center; justify-content:center; padding:20px;
        opacity:0; transition:opacity 0.25s ease;
    }
    #endeksGecmisModal.active { opacity:1; }
    #endeksGecmisModal.active .emd-card { transform:scale(1) translateY(0) !important; opacity:1 !important; }

    .emd-card {
        transform: scale(0.95) translateY(20px);
        opacity: 0;
        transition: all 0.3s ease;
        max-width: 860px;
        width: 100%;
        max-height: 90vh;
        margin: 0 15px;
        background: #fff;
        border-radius: 28px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(0,0,0,0.3);
    }

    .egm-main-table {
        width:100%; border-collapse:collapse; font-size:0.82rem;
    }
    .egm-main-table thead tr { background:#f8fafc; position:sticky; top:0; z-index:10; }
    .egm-main-table thead th {
        padding:11px 14px; font-size:0.68rem; font-weight:800; color:#64748b;
        text-transform:uppercase; letter-spacing:0.07em; border-bottom:2px solid #e2e8f0; white-space:nowrap;
    }
    .egm-period-header-row td {
        padding:9px 16px !important;
        background:linear-gradient(125deg,#0f172a 0%,#1e1b4b 100%); border-bottom:none;
    }
    .egm-period-label { font-size:0.88rem; font-weight:800; color:#fff; display:inline-flex; align-items:center; gap:7px; }
    .egm-period-label i { opacity:0.8; }
    .egm-period-meta {
        font-size:0.75rem; color:#c4b5fd; background:rgba(255,255,255,0.1);
        border:1px solid rgba(255,255,255,0.15); padding:3px 10px; border-radius:20px; margin-left:15px; vertical-align:middle;
    }
    .egm-period-meta strong { color:#ede9fe; }
    .egm-period-tutar {
        font-size:0.88rem; font-weight:800; color:#a7f3d0; display:inline-flex; align-items:center; gap:6px;
        background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3);
        padding:4px 14px; border-radius:20px;
    }
    .egm-data-row td { padding:7px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    .egm-data-row:hover td { background:#fafbff; }
    .egm-row-ana td { background:#f5f3ff; }
    .egm-row-ana:hover td { background:#ede9fe; }
    .egm-row-reaktif td { background:#fffbf5; }
    .egm-row-reaktif:hover td { background:#fef3c7; }
    .egm-row-sep td { border-bottom:3px solid #c4b5fd !important; }
    .egm-indicator-cell { white-space:nowrap; }
    .egm-indicator-sub { font-size:0.7rem; color:#94a3b8; font-weight:500; margin-left:6px; }
    .egm-num-cell { text-align:right; font-family:'Courier New',monospace; font-size:0.8rem; color:#334155; font-weight:600; white-space:nowrap; }
    .egm-tuketim-cell { color:#059669 !important; font-weight:700 !important; }
    .egm-badge {
        display:inline-flex; align-items:center; justify-content:center;
        font-size:0.72rem; font-weight:900; padding:2px 8px; border-radius:6px; min-width:28px; text-align:center;
    }
    .egm-badge-t0 { background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; }
    .egm-badge-t1 { background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe; }
    .egm-badge-t2 { background:#f3e8ff; color:#6d28d9; border:1px solid #ddd6fe; }
    .egm-badge-t3 { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
    .egm-badge-ri { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .egm-badge-rc { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    
    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 16px 24px;
        border-radius: 16px;
        font-family: var(--font-primary);
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .main-action-btn {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white !important;
    }
    .main-action-btn:hover {
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }
    .action-btn i { font-size: 1.2rem; }
    .map-action-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white !important;
    }
    .map-action-btn:hover {
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Abone Detayı: {{ $abone->ABONE_TESIS_NO }}</h1>
                <p class="hero-subtitle">Abone profili, sayaç geçmişi ve güncelleme tarihçesi.</p>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="{{ route('aboneler.index') }}" class="btn-pro btn-outline-pro">
                    <i class="fas fa-arrow-left"></i> Listeye Dön
                </a>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Prominent Action Buttons -->
        <div style="display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap;">
            @if($sonDonem)
            <button type="button" id="aboneHistoryBtn" data-tesisat="{{ $abone->ABONE_TESIS_NO }}" data-donem="{{ $sonDonem }}" class="action-btn main-action-btn">
                <i class="fas fa-history"></i> Son 1 Yıllık Veriyi Görüntüle
            </button>
            @endif
        </div>

        <div class="row">
            <div class="col-md-4">
                <!-- PRIMARY INFO -->
                <div class="glass-card">
                    <h5 class="card-title-pro"><i class="fas fa-id-card"></i> Kimlik Bilgileri</h5>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Tesisat No</span>
                            <span class="info-value mono">{{ $abone->ABONE_TESIS_NO }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ünvan</span>
                            <span class="info-value">{{ $abone->UNVAN ?? '—' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bölge Koordinasyonu</span>
                            <span class="info-value">{{ $abone->bolge->bolge_adi ?? ($abone->BOLGE_ADI ?? 'Tanımsız') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sistem Durumu</span>
                            <div>
                                @if($abone->is_active)
                                    <span class="badge-status status-active">AKTİF</span>
                                @else
                                    <span class="badge-status status-passive">PASİF</span>
                                    @if($abone->passive_reason)
                                        <div class="mt-3 p-3" style="background: #fff5f5; border-left: 4px solid #fecaca; border-radius: 8px;">
                                            <span class="info-label" style="color: #dc2626; font-size: 0.7rem;">Pasiflik Nedeni:</span>
                                            <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #991b1b;">{{ $abone->passive_reason }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY INFO -->
                <div class="glass-card">
                    <h5 class="card-title-pro"><i class="fas fa-tags"></i> Kategori & Tarife</h5>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Abone Grubu</span>
                            <span class="info-value">{{ $abone->abone_grubu ?? '—' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tarife</span>
                            <span class="info-value">{{ $abone->tarife ?? '—' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bağlantı Grubu</span>
                            <span class="info-value">{{ $abone->baglanti_grubu ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- ADDRESS & NOTES -->
                <div class="glass-card">
                    <h5 class="card-title-pro"><i class="fas fa-map-marked-alt"></i> Konum ve Notlar</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="info-label">Açık Adres</label>
                            <p style="font-weight: 600; color: var(--text-slate-900); line-height: 1.6;">{{ $abone->ADRES ?? 'Adres bilgisi girilmemiş.' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="info-label">Özel Notlar</label>
                            <p style="background: #f8fafc; padding: 15px; border-radius: 12px; font-style: italic; color: #64748b;">{{ $abone->notlar ?? 'Abone hakkında herhangi bir not bulunmuyor.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- METER HISTORY -->
                <div class="glass-card">
                    <h5 class="card-title-pro"><i class="fas fa-tachometer-alt"></i> Sayaç Değişim Geçmişi</h5>
                    <div class="timeline-pro">
                        @forelse($farkliSayaclar as $sayac)
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong style="font-family: monospace; font-size: 1.1rem; color: #2563eb;">{{ $sayac->no }}</strong>
                                        <span style="font-size: 0.8rem; font-weight: 700; color: #94a3b8;"><i class="fas fa-calendar-alt mr-1"></i> {{ $sayac->tarih }}</span>
                                    </div>
                                    @if($loop->first)
                                        <span class="badge badge-success mt-2" style="font-size: 0.6rem; border-radius: 4px;">GÜNCEL SAYAÇ</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">Kayıtlı sayaç geçmişi bulunamadı.</p>
                        @endforelse
                    </div>
                </div>


            </div>
        </div>

        {{-- ═══ Son 1 Yıl Tüketim Grafiği ═══ --}}
        <div class="glass-card" style="margin-top:30px;">
            <h5 class="card-title-pro"><i class="fas fa-chart-bar"></i> Son 1 Yıl Tüketim (kWh)</h5>
            <div style="position:relative; height:280px;">
                <canvas id="tuketimChart"></canvas>
            </div>
        </div>

        {{-- ═══ Uydu Görüntüsü ═══ --}}
        <div class="glass-card" style="margin-top:30px;">
            <h5 class="card-title-pro"><i class="fas fa-satellite"></i> Uydu Görüntüsü</h5>
            <div style="border-radius:16px; overflow:hidden; height:400px;">
                <iframe
                    width="100%" height="100%" style="border:0;"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps?q={{ urlencode(($abone->ADRES ?? '') . ', ' . ($abone->BOLGE_ADI ?? '')) }}&t=k&z=15&output=embed"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

    </div>
</div>

{{-- ═══ Premium Endeks Geçmiş 6 Ay Modal ═══ --}}
<div id="endeksGecmisModal">
    <div class="emd-card">
        <div class="emd-header" style="background:linear-gradient(125deg,#0f172a 0%,#1e1b4b 100%); padding:20px 28px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div class="emd-header-left" style="display:flex; align-items:center; gap:14px;">
                <div class="emd-header-icon" style="width:42px; height:42px; border-radius:12px; background:rgba(167,139,250,0.15); border:1px solid rgba(167,139,250,0.3); display:flex; align-items:center; justify-content:center; color:#a78bfa;"><i class="fas fa-history"></i></div>
                <div>
                    <div class="emd-eyebrow" style="font-size:.65rem; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:.12em; margin-bottom:3px;">Endeks Geçmişi</div>
                    <div class="emd-title-row" style="display:flex; align-items:center; gap:10px;">
                        <span id="egm-header-tesisat" class="emd-fatura-badge" style="font-size:.9rem; font-weight:800; color:#fff;">Tesisat No: —</span>
                        <span class="emd-sep" style="color:rgba(255,255,255,0.2);">|</span>
                        <span class="emd-donem-pill" style="background:rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); color:#c084fc; padding:2px 12px; border-radius:20px; font-size:.72rem; font-weight:700;">Son 1 Yıl Verisi</span>
                    </div>
                </div>
            </div>
            <div class="emd-header-right">
                <button onclick="closeEndeksGecmis()" class="emd-close-btn" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.06); color:rgba(255,255,255,0.6); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.9rem;"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="emd-body" style="flex:1; overflow-y:auto; background:#f8fafc; padding:0;">
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
                    <tbody id="egm-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function emdFmt(n) {
    var num = parseFloat(n) || 0;
    return num.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2});
}

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
        html += '<tr class="egm-period-header-row"><td colspan="2"><span class="egm-period-label"><i class="far fa-calendar-alt"></i> ' + formatEgmDonem(rec.donem) + '</span><span class="egm-period-meta">Çarpan: <strong>x' + rec.carpan + '</strong></span></td><td colspan="3" style="text-align:right;"><span class="egm-period-tutar"><i class="fas fa-lira-sign"></i> ' + emdFmt(rec.tutar) + ' Fatura Tutarı</span></td></tr>';
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
            html += '<tr class="egm-data-row ' + rowCls + ' ' + borderCls + '"><td class="egm-indicator-cell"><span class="' + lblClass + '">' + item.label + '</span> <span class="egm-indicator-sub">' + item.sublabel + '</span></td><td class="egm-num-cell">' + emdFmt(item.data.ilk) + '</td><td class="egm-num-cell">' + emdFmt(item.data.son) + '</td><td class="egm-num-cell" style="font-weight:800;color:' + farkColor + ';">' + emdFmt(item.data.fark) + '</td><td class="egm-num-cell egm-tuketim-cell">' + tuketimText + '</td></tr>';
        });
    });
    tbody.innerHTML = html;
    var modal = document.getElementById('endeksGecmisModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(function() { modal.classList.add('active'); }, 10);
}

window.closeEndeksGecmis = function() {
    var modal = document.getElementById('endeksGecmisModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    setTimeout(function() { modal.style.display = 'none'; }, 280);
};

$(document).ready(function() {
    $('#aboneHistoryBtn').on('click', function() {
        var tesisat = $(this).data('tesisat');
        var donem = $(this).data('donem');
        if (!tesisat) { console.warn('[Abone] tesisat_no bulunamadı'); return; }

        console.debug('[Abone] Son 1 yıl yükleniyor:', tesisat, donem);

        Swal.fire({ title: 'Yükleniyor...', text: 'Son 1 yılın endeks verileri getiriliyor.', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        $.ajax({
            url: '/raporlar/endeks/gecmis-1-yil/' + encodeURIComponent(tesisat),
            type: 'GET',
            data: { donem: donem || '' },
            success: function(res) {
                Swal.close();
                console.debug('[Abone] API yanıtı:', res);
                if (res.success && res.records && res.records.length > 0) {
                    openEndeksGecmis(res.tesisat_no, res.records);
                } else {
                    Swal.fire({ icon: 'info', title: 'Kayıt Bulunamadı', text: 'Bu tesisat numarasına ait geçmiş dönem kaydı bulunamadı.', confirmButtonText: 'Tamam' });
                }
            },
            error: function(xhr) {
                Swal.close();
                console.error('[Abone] API hatası:', xhr);
                Swal.fire({ icon: 'error', title: 'Hata', text: 'Geçmiş veriler alınırken bir hata oluştu.', confirmButtonText: 'Tamam' });
            }
        });
    });

    var ctx = document.getElementById('tuketimChart');
    if (!ctx) return;

    var labels = @json($chartLabels);
    var values = @json($sonYilTuketim->pluck('fatura_edilecek_toplam_tuketim_kwh'));
    var tutarlar = @json($sonYilTuketim->pluck('tutar_toplam'));

    if (!values.length) {
        ctx.parentElement.innerHTML = '<div class="text-center py-5 text-muted fw-bold">Bu abone için tüketim verisi bulunamadı.</div>';
        return;
    }

    const tutarLabelPlugin = {
        id: 'tutarLabelPlugin',
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
            chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                meta.data.forEach(function(bar, index) {
                    var tutar = chart.data.tutarlar[index];
                    if (tutar && tutar > 0) {
                        var text = tutar.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ₺';
                        ctx.save();
                        ctx.fillStyle = '#475569';
                        ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(text, bar.x, bar.y - 6);
                        ctx.restore();
                    }
                });
            });
        }
    };

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            tutarlar: tutarlar,
            datasets: [{
                label: 'Tüketim (kWh)',
                data: values,
                backgroundColor: '#72b2dd', // Resimdeki gibi açık mavi tonu
                borderWidth: 0,
                barPercentage: 0.6,
            }]
        },
        options: {
            layout: { padding: { top: 25 } },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#333333',
                    bodyColor: '#333333',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    titleFont: { weight: '700', size: 13 },
                    bodyFont: { weight: '600', size: 12 },
                    padding: 12,
                    cornerRadius: 4,
                    callbacks: {
                        label: function(ctx) {
                            var tuk = ctx.parsed.y.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' kWh';
                            var ttr = ctx.chart.data.tutarlar[ctx.dataIndex];
                            if (ttr > 0) {
                                tuk += ' (Fatura: ' + ttr.toLocaleString('tr-TR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ₺)';
                            }
                            return tuk;
                        }
                    }
                },
                datalabels: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e5e7eb', drawBorder: false },
                    ticks: {
                        color: '#6b7280',
                        font: { size: 13 },
                        padding: 10,
                        callback: function(v) { return v.toLocaleString('tr-TR'); }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: '#6b7280',
                        font: { size: 13 }
                    }
                }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        },
        plugins: [tutarLabelPlugin]
    });
});
</script>
@endpush
@endsection
