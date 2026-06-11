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



    /* Stats & Table Responsive Fixes */
    .stats-row { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
        margin-bottom: 30px; 
    }
    
    @media (max-width: 768px) {
        .page-hero { padding: 3rem 1rem 6rem 1rem; }
        .hero-container { flex-direction: column; align-items: flex-start; gap: 20px; }
        .main-container { padding: 0 1rem; margin-top: -4rem; }
        .glass-card { padding: 20px; }
        .modal-dialog { max-width: 95% !important; margin: 10px auto; }
    }

    .tbl-wrap { 
        overflow-x: auto; 
        border-radius: 20px; 
        background: #fff;
        box-shadow: inset 0 0 0 1px #e2e8f0;
        margin-top: 10px;
    }
    .tbl { width: 100%; min-width: 1100px; border-collapse: separate; border-spacing: 0; }
    .tbl th { background: #f8fafc; padding: 16px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .tbl td { padding: 16px 20px; font-size: 0.9rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s; }
    .tbl tr:hover td { background: #f8fafc; }

    /* ===== EXPORT LOADING OVERLAY ===== */
    #detExportOverlay {
        display:none; position:fixed; inset:0; z-index:99999;
        background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        align-items:center; justify-content:center; flex-direction:column;
    }
    #detExportOverlay.active { display:flex; }
    .det-loader-box {
        background:rgba(255,255,255,0.97); border-radius:24px; padding:40px 50px;
        text-align:center; box-shadow:0 30px 80px rgba(0,0,0,0.25);
        animation:fadeScaleIn .35s ease; min-width:320px;
    }
    @keyframes fadeScaleIn { from{opacity:0;transform:scale(.92);} to{opacity:1;transform:scale(1);} }
    .det-spinner {
        width:64px; height:64px; border:5px solid #e2e8f0;
        border-top-color:#2563eb; border-radius:50%;
        animation:detSpin .85s linear infinite; margin:0 auto 20px;
    }
    @keyframes detSpin { to{transform:rotate(360deg);} }
    .det-loader-title { font-size:1.15rem; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .det-loader-sub   { font-size:.85rem; color:#64748b; font-weight:500; }
    .det-progress     { width:220px; height:6px; background:#e2e8f0; border-radius:99px; margin:16px auto 0; overflow:hidden; }
    .det-progress-bar {
        height:100%; width:0%;
        background:linear-gradient(90deg,#2563eb,#4f46e5); border-radius:99px;
        animation:detProgressFill 30s ease-out forwards;
    }
    @keyframes detProgressFill { 0%{width:0%;} 40%{width:55%;} 70%{width:78%;} 90%{width:90%;} 100%{width:95%;} }
    .det-overlay-close {
        margin-top:18px; padding:7px 20px; border-radius:10px; border:1px solid #e2e8f0;
        background:#f8fafc; color:#64748b; font-weight:700; font-size:.83rem;
        cursor:pointer; display:none;
    }
    .det-overlay-close:hover { background:#f1f5f9; color:#0f172a; }
    .det-open-btn {
        display:none; margin-top:18px; padding:12px 28px; border-radius:13px;
        background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff;
        font-weight:800; font-size:.95rem; border:none; cursor:pointer;
        box-shadow:0 8px 20px -5px rgba(37,99,235,.35); transition:all .2s;
    }
    .det-open-btn:hover { transform:translateY(-2px); box-shadow:0 12px 24px -5px rgba(37,99,235,.45); }
    .det-success-icon {
        display:none; width:64px; height:64px; border-radius:50%;
        background:#dcfce7; color:#16a34a; font-size:1.8rem;
        align-items:center; justify-content:center; margin:0 auto 16px;
    }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">{{ request('veri') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}</h1>
                <p class="hero-subtitle">{{ request('veri') === 'tutar' ? 'Dönemlere göre fatura tutarlarını görüntüleyin' : 'Bireysel faturaların tüketim ve maliyet detaylarını inceleyin' }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <div class="dropdown" id="detExportBtnContainer" style="display: {{ request()->anyFilled(['start_period','end_period']) ? 'block' : 'none' }};">
                    <button type="button" class="btn-pro btn-outline-pro dropdown-toggle" data-toggle="dropdown" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
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
            <h5 class="section-title"><i class="fas fa-filter"></i> Filtreleme Kriterleri</h5>


            <form action="{{ route('reports.tuketim') }}" method="GET" id="detailedFilterForm">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="far fa-calendar-alt me-2"></i> Dönem Başlangıç</label>
                            <select name="start_period" id="start_period" class="form-control-pro" style="height: 47px;">
                                <option value="">Seçiniz</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}" {{ request('start_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="far fa-calendar-check me-2"></i> Dönem Bitiş</label>
                            <select name="end_period" id="end_period" class="form-control-pro" style="height: 47px;">
                                <option value="">Seçiniz</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}" {{ request('end_period') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="fas fa-chart-bar me-2"></i> Veri</label>
                            <select name="veri" id="veri" class="form-control-pro" style="height: 47px;">
                                <option value="tuketim" {{ request('veri', 'tuketim') == 'tuketim' ? 'selected' : '' }}>Tüketim (kWh)</option>
                                <option value="tutar" {{ request('veri') == 'tutar' ? 'selected' : '' }}>Tutar (TL)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-pro btn-primary-pro w-100 justify-content-center" style="height: 47px; background: linear-gradient(135deg, #2563eb, #4f46e5); font-weight: 800;"><i class="fas fa-search"></i> Getir</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SONUÇLAR KONTEYNERI --}}
        <div id="reportResultsContainer">
            @if(request()->anyFilled(['start_period','end_period']))
                @include('reports.partials.tuketim_table', ['pivotData' => $pivotData, 'pivotPeriods' => $pivotPeriods, 'totalKWH' => $totalKWH, 'totalAmount' => $totalAmount, 'colTotals' => $colTotals ?? [], 'veri' => $veri ?? 'tuketim'])
            @else
                <div class="glass-card" style="text-align:center;padding:60px 40px;">
                    <div style="width:80px;height:80px;background:#eff6ff;color:#3b82f6;border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px;">📄</div>
                    <h4 style="font-weight:800;color:var(--text-slate-900);">{{ request('veri') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}</h4>
                    <p style="color:var(--text-slate-500);max-width:500px;margin:0 auto;">{{ request('veri') === 'tutar' ? 'Dönemlere ait fatura tutarlarını görüntülemek için filtreleri kullanın.' : 'Dönemlere ait tüketim rakamlarını görüntülemek için filtreleri kullanın.' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>


{{-- EXPORT LOADING OVERLAY --}}
<div id="detExportOverlay">
    <div class="det-loader-box">
        <div class="det-success-icon" id="detSuccessIcon"><i class="fas fa-check"></i></div>
        <div class="det-spinner" id="detSpinner"></div>
        <div class="det-loader-title" id="detLoaderTitle">Rapor Hazırlanıyor…</div>
        <div class="det-loader-sub" id="detLoaderSub">Lütfen bekleyin, detaylı fatura dökümü oluşturuluyor.</div>
        <div class="det-progress" id="detProgressWrap"><div class="det-progress-bar" id="detProgressBar"></div></div>
        <button class="det-open-btn" id="detOpenBtn"></button>
        <button class="det-overlay-close" id="detOverlayClose"><i class="fas fa-times"></i> Kapat</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
let lastDownloadUrl = '';
let lastExportType = '';

function showDetOverlay(type) {
    const $bar = $('#detProgressBar');
    $bar.removeClass('det-progress-bar'); void $bar[0].offsetWidth; $bar.addClass('det-progress-bar');
    $('#detSuccessIcon').hide();
    $('#detSpinner').show();
    $('#detProgressWrap').show();
    $('#detOpenBtn').hide();
    $('#detOverlayClose').hide();
    $('#detExportOverlay').addClass('active');
    setTimeout(() => { $('#detOverlayClose').show(); }, 10000);
}

function hideDetOverlay() {
    $('#detExportOverlay').removeClass('active');
    if (lastDownloadUrl) { URL.revokeObjectURL(lastDownloadUrl); lastDownloadUrl = ''; }
}

function showDetReady(type, blobUrl) {
    lastDownloadUrl = blobUrl;
    lastExportType = type;
    $('#detSpinner').hide();
    $('#detProgressWrap').hide();
    $('#detSuccessIcon').css('display','flex');
    $('#detLoaderTitle').text('İndirme Hazır!');
    $('#detLoaderSub').text('Dosyanız başarıyla oluşturuldu.');
    var label = type === 'pdf' ? '<i class="fas fa-file-pdf"></i> PDF Dosyasını Aç' : '<i class="fas fa-file-excel"></i> Excel Dosyasını Aç';
    $('#detOpenBtn').html(label).show();
    $('#detOverlayClose').show();
}


async function handleExport(type) {
    showDetOverlay(type);
    const form = document.getElementById('detailedFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    
    try {
        const response = await fetch(`${form.action}?${params}&export=${type}`);
        if (!response.ok) throw new Error('Rapor oluşturulamadı');
        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        showDetReady(type, blobUrl);
    } catch (e) {
        $('#detLoaderTitle').text('Hata Oluştu');
        $('#detLoaderSub').text('Rapor oluşturulurken bir hata meydana geldi.');
        $('#detSpinner').hide();
        $('#detProgressWrap').hide();
        $('#detOverlayClose').show();
    }
}


$(document).ready(function() {
    $('.btn-export-trigger').click(function(e) {
        e.preventDefault();
        handleExport($(this).data('type'));
    });

    $('#detOpenBtn').click(function() {
        if (!lastDownloadUrl) return;
        if (lastExportType === 'pdf') {
            window.open(lastDownloadUrl, '_blank');
        } else {
            const a = document.createElement('a');
            a.href = lastDownloadUrl;
            a.download = 'Tuketim_Raporu.xlsx';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
        }
        hideDetOverlay();
    });

    $('#detOverlayClose').click(hideDetOverlay);

    $('#detailedFilterForm').on('submit', function(e) {
        if (!$('#start_period').val() && !$('#end_period').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Uyarı', text: 'Lütfen dönem aralığı seçiniz.', confirmButtonText: 'Tamam'});
            return;
        }
    });

    $(document).on('click', '#reportResultsContainer .pagination a', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const $container = $('#reportResultsContainer');

        $container.css('opacity', '0.5');
        $.ajax({
            url: href,
            success: function(html) {
                $container.html(html).css('opacity', '1');
                $('html, body').animate({ scrollTop: $container.offset().top - 100 }, 500);
            }
        });
    });
});
</script>
@endpush
