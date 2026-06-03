@extends('frontend.layouts.app')

@section('content')
    <style>
        /* Ultra-Premium Glassmorphic Import CSS - MATCHING DASHBOARD */
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
            --shadow-glow: 0 0 25px rgba(37, 99, 235, 0.3);
        }

        .content {
            background-color: var(--bg-main) !important;
            min-height: 100vh;
        }

        /* Hero Area */
        .dashboard-hero {
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

        /* Animated background elements */
        .dashboard-hero::before {
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

        .dashboard-hero::after {
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

        .hero-content {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .hero-title {
            font-family: var(--font-primary);
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, #ffffff, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            color: #94a3b8;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Layout Wrapper */
        .dashboard-container {
            width: 100%;
            max-width: 1400px;
            margin: -6rem auto 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 20;
        }

        /* State Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--surface-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow-elevated);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            display: flex !important;
            flex-direction: column;
            text-decoration: none !important;
            min-height: 130px;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.12);
            border-color: rgba(255, 255, 255, 1);
        }

        .stat-card::before {
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
        }

        .stat-c1::before {
            background: #3b82f6;
        }

        .stat-c4::before {
            background: #10b981;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            z-index: 2;
        }

        .stat-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-slate-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ic1 {
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        .ic4 {
            background: linear-gradient(135deg, #34d399, #059669);
        }

        .stat-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--text-slate-900);
            z-index: 2;
            margin-top: auto;
            line-height: 1;
        }

        .stat-desc {
            font-size: 0.75rem;
            color: var(--text-slate-500);
            margin-top: 5px;
            z-index: 2;
            font-weight: 500;
        }

        /* Premium Widgets */
        .premium-widget {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 30px;
            box-shadow: var(--shadow-elevated);
            border: 1px solid rgba(226, 232, 240, 0.6);
            margin-bottom: 30px;
        }

        .widget-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-slate-900);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .widget-title i {
            padding: 10px;
            background: #f1f5f9;
            border-radius: 10px;
            color: #3b82f6;
            font-size: 18px;
        }

        /* Upload Area */
        .drop-zone-premium {
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .drop-zone-premium:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: scale(1.01);
        }

        .upload-icon-box {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #3b82f6;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .btn-premium-pro {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-premium-pro:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
        }

        .btn-premium-pro:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Table */
        .pro-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .pro-table th {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 0 20px 10px 20px;
            text-align: left;
        }

        .pro-table td {
            padding: 18px 20px;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .pro-table tr td:first-child {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            font-weight: 700;
        }

        .pro-table tr td:last-child {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
            text-align: right;
        }

        .pro-table tr:hover td {
            background: #eff6ff;
        }

        .badge-blue {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .action-btn.delete:hover {
            background: #ef4444;
            border-color: #ef4444;
        }

        /* Progress Overlay */
        .prog-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .prog-overlay.active {
            display: flex;
        }

        .prog-card {
            background: white;
            border-radius: 32px;
            width: 100%;
            max-width: 460px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        }

        .prog-bar-track {
            height: 10px;
            background: #f1f5f9;
            border-radius: 5px;
            overflow: hidden;
            margin: 25px 0;
        }

        .prog-bar-fill {
            height: 100%;
            width: 0%;
            background: var(--primary-gradient);
            transition: width 0.4s ease;
            border-radius: 5px;
        }
    </style>

    <div class="content p-0">
        <!-- HERO -->
        <div class="dashboard-hero">
            <div class="hero-content">
                <div>
                    <div class="mb-3">
                        <span
                            style="background: rgba(255,255,255,0.1); padding: 6px 16px; border-radius: 100px; font-size: 0.8rem; font-weight: 600; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); letter-spacing: 1px; color:#a5b4fc;">
                            <i class="fas fa-file-import text-warning mr-1"></i> VERİ ENTEGRASYONU
                        </span>
                    </div>
                    <h1 class="hero-title">Excel Veri Aktarımı</h1>
                    <p class="hero-subtitle">Tedaş tarafında gönderilen faturalarını excel formatında sisteme yükleyin.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('staging.index') }}" class="btn btn-outline-light px-4 py-2"
                        style="border-radius: 12px; font-weight: 700; border-color: rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <i class="fas fa-layer-group mr-2"></i> Bekleme Havuzu
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER -->
        <div class="dashboard-container">

            <div class="stats-grid">
                <a href="{{ route('staging.index') }}" class="stat-card stat-c1">
                    <div class="stat-header">
                        <div class="stat-title">Bekleyen Kayıtlar</div>
                        <div class="stat-icon ic1"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="stat-value">{{ number_format($stagingStats['pending'] ?? 0) }}</div>
                    <div class="stat-desc">Havuzda onay bekleyenler</div>
                </a>

                <div class="stat-card stat-c4">
                    <div class="stat-header">
                        <div class="stat-title">Toplam Dosya</div>
                        <div class="stat-icon ic4"><i class="fas fa-file-excel"></i></div>
                    </div>
                    <div class="stat-value">{{ $gecmisImportlar->total() }}</div>
                    <div class="stat-desc">Yüklenen arşiv dosyası</div>
                </div>
            </div>

            <div class="row">
                <!-- UPLOAD -->
                <div class="col-lg-6">
                    <div class="premium-widget">
                        <h3 class="widget-title"><i class="fas fa-cloud-upload-alt"></i> Yeni Dosya Yükle</h3>

                        <form action="{{ route('import.ajax') }}" method="POST" enctype="multipart/form-data"
                            id="importForm">
                            @csrf
                            <div class="drop-zone-premium" id="dropZone"
                                onclick="document.getElementById('fileInput').click()">
                                <div class="upload-icon-box"><i class="fas fa-file-excel"></i></div>
                                <h4 style="font-weight: 800; color: #1e293b; margin-bottom: 8px;">Excel Dosyasını Buraya
                                    Sürükleyin</h4>
                                <p style="color: #64748b; font-weight: 500;">veya seçmek için <strong>tıklayın</strong></p>
                                <input type="file" name="dosya" id="fileInput" accept=".xlsx,.xls,.csv"
                                    style="display:none;" onchange="handleFileSelect(this)">

                                <div id="fileInfo"
                                    style="display:none; margin-top: 20px; padding: 12px 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb; width: 100%;">
                                    <i class="fas fa-file-alt"></i> <span id="fileName">Dosya.xlsx</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" id="submitBtn" disabled class="btn-premium-pro">
                                    <i class="fas fa-rocket mr-2"></i> İçe Aktarımı Başlat
                                </button>
                            </div>


                        </form>

                        <div
                            style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 18px; border: 1px solid #e2e8f0;">
                            <h5 style="font-size: 0.9rem; font-weight: 800; color: #1e293b; margin-bottom: 12px;"><i
                                    class="fas fa-info-circle text-primary mr-2"></i> Bilgilendirme</h5>
                            <ul style="font-size: 0.8rem; color: #64748b; padding-left: 20px; margin: 0;">
                                <li class="mb-2">Sadece <strong>excel formatındaki dosyaları</strong> desteklenir.</li>
                                <li class="mb-2">Dosya sütunlarının standart şablona uygun olduğundan emin olun.</li>
                                <li>Büyük dosyaların işlenmesi birkaç dakika sürebilir.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- RECENT -->
                <div class="col-lg-6">
                    <div class="premium-widget">
                        <h3 class="widget-title"><i class="fas fa-history"></i> Son Yüklenenler</h3>

                        <div class="table-responsive">
                            <table class="pro-table">
                                <thead>
                                    <tr>
                                        <th>Dönem</th>
                                        <th>Kayıt</th>
                                        
                                        <th>Tarih</th>
                                        <th style="text-align: right;">Eylem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($gecmisImportlar as $log)
                                        <tr>
                                            <td><span class="badge-blue">{{ $log->donem }}</span></td>
                                            <td>
                                                <div style="font-weight: 800; color: #1e293b;">
                                                    {{ number_format($log->toplam_satir ?? 0) }}</div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">
                                                    {{ $log->created_at->format('d.m.Y') }}</div>
                                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                                                    {{ $log->created_at->format('H:i') }}</div>
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button
                                                        onclick="deleteImportLog(
                                                            {{ $log->id }},
                                                            {{ $log->odenen_fatura_sayisi ?? 0 }},
                                                            {{ $log->staging_bekleyen_sayisi ?? 0 }}
                                                        )"
                                                        class="action-btn delete"
                                                        title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <img src="https://illustrations.popsy.co/slate/empty-folder.svg"
                                                    style="width: 120px; opacity: 0.5; margin-bottom: 20px;">
                                                <p style="font-weight: 600; color: #94a3b8;">Henüz bir yükleme yapılmamış.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($gecmisImportlar->hasPages())
                            <div class="d-flex justify-content-end mt-4">
                                {{ $gecmisImportlar->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal (v1 Premium Design) -->

    @push('scripts')
    <style>
/* ══════════════════════════════════════════════════════
   Progress Modal — Version-1 Premium Design
   ══════════════════════════════════════════════════════ */
.pm-backdrop {
  position:fixed;inset:0;z-index:99999;
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
    </style>

    <script>
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fileInput');
        const sb = document.getElementById('submitBtn');
        const fInfo = document.getElementById('fileInfo');
        const fName = document.getElementById('fileName');

        function handleFileSelect(input) {
            if (input.files && input.files.length) {
                fName.textContent = input.files[0].name;
                fInfo.style.display = 'block';
                sb.disabled = false;
            }
        }

        ['dragenter', 'dragover'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.style.borderColor = '#3b82f6'; dz.style.background = '#eff6ff'; }));
        ['dragleave', 'drop'].forEach(e => dz.addEventListener(e, ev => {
            ev.preventDefault(); dz.style.borderColor = '#e2e8f0'; dz.style.background = '#f8fafc';
            if (e === 'drop' && ev.dataTransfer.files.length) { fi.files = ev.dataTransfer.files; handleFileSelect(fi); }
        }));

        /* ── Progress Modal HTML enjekte et ── */
        document.body.insertAdjacentHTML('beforeend', `
        <div id="progModal" class="pm-backdrop">
          <div class="pm-box">
            <div class="pm-strip">
              <div class="pm-strip-fill" id="pmStripFill"></div>
              <div class="pm-strip-dot"  id="pmStripDot"></div>
            </div>
            <div class="pm-body">
              <div class="pm-icon-wrap">
                <div class="pm-icon-ring"></div>
                <div class="pm-icon-ring pm-ring2"></div>
                <div class="pm-icon" id="pmIcon"><i class="fas fa-cloud-upload-alt"></i></div>
              </div>
              <div class="pm-title" id="pmTitle">Dosya Yükleniyor</div>
              <div class="pm-sub"   id="pmSub">Güvenli bağlantı kurularak sunucuya aktarılıyor...</div>
              <div class="pm-track">
                <div class="pm-fill" id="pmFill"><div class="pm-shine"></div></div>
              </div>
              <div class="pm-perc-row">
                <span class="pm-perc" id="pmPerc">0%</span>
                <span class="pm-hint">Lütfen bu sayfayı kapatmayın</span>
              </div>
              <div class="pm-steps">
                <div class="pm-step pm-step-active" id="pmStep1">
                  <div class="pm-step-dot"></div><span>Dosya Aktarımı</span>
                </div>
                <div class="pm-step-line" id="pmLine1"></div>
                <div class="pm-step" id="pmStep2">
                  <div class="pm-step-dot"></div><span>Veri İşleme</span>
                </div>
                <div class="pm-step-line" id="pmLine2"></div>
                <div class="pm-step" id="pmStep3">
                  <div class="pm-step-dot"></div><span>Tamamlandı</span>
                </div>
              </div>
            </div>
          </div>
        </div>`);

        /* Referanslar */
        const progModal   = document.getElementById('progModal');
        const pmFill      = document.getElementById('pmFill');
        const pmStripFill = document.getElementById('pmStripFill');
        const pmStripDot  = document.getElementById('pmStripDot');
        const pmPerc      = document.getElementById('pmPerc');
        const pmIcon      = document.getElementById('pmIcon');
        const pmTitle     = document.getElementById('pmTitle');
        const pmSub       = document.getElementById('pmSub');
        const pmStep1     = document.getElementById('pmStep1');
        const pmStep2     = document.getElementById('pmStep2');
        const pmStep3     = document.getElementById('pmStep3');
        const pmLine1     = document.getElementById('pmLine1');
        const pmLine2     = document.getElementById('pmLine2');

        /* ── Smooth progress animasyonu ── */
        let displayedPct = 0;
        let animFrame    = null;

        function setProgress(targetPct) {
            if (animFrame) cancelAnimationFrame(animFrame);
            function step() {
                const diff  = targetPct - displayedPct;
                const speed = Math.max(0.4, Math.abs(diff) * 0.07);
                if (Math.abs(diff) < 0.3) { displayedPct = targetPct; }
                else                       { displayedPct += speed; }
                const p      = Math.min(100, displayedPct);
                const pRound = Math.round(p);

                pmFill.style.width      = p + '%';
                pmStripFill.style.width = p + '%';
                pmStripDot.style.left   = 'calc(' + p + '% - 6px)';
                pmPerc.textContent      = pRound + '%';

                if (pRound >= 100)     pmPerc.style.color = '#15803d';
                else if (pRound >= 60) pmPerc.style.color = '#059669';
                else                   pmPerc.style.color = '#1a5f8a';

                if (displayedPct < targetPct) animFrame = requestAnimationFrame(step);
            }
            animFrame = requestAnimationFrame(step);
        }

        /* ── Faz geçişleri ── */
        function setPhase(phase) {
            if (phase === 'upload') {
                pmStep1.className = 'pm-step pm-step-active';
                pmStep2.className = 'pm-step';
                pmStep3.className = 'pm-step';
                pmLine1.classList.remove('pm-line-done');
                pmLine2.classList.remove('pm-line-done');
                pmIcon.className    = 'pm-icon';
                pmIcon.innerHTML    = '<i class="fas fa-cloud-upload-alt"></i>';
                pmTitle.textContent = 'Dosya Yükleniyor';
                pmSub.textContent   = 'Güvenli bağlantı kurularak sunucuya aktarılıyor...';
            } else if (phase === 'process') {
                pmStep1.className = 'pm-step pm-step-done';
                pmStep2.className = 'pm-step pm-step-active';
                pmStep3.className = 'pm-step';
                pmLine1.classList.add('pm-line-done');
                pmLine2.classList.remove('pm-line-done');
                pmIcon.className    = 'pm-icon pm-icon-proc';
                pmIcon.innerHTML    = '<i class="fas fa-cog fa-spin"></i>';
                pmTitle.textContent = 'Veriler İşleniyor';
            } else if (phase === 'done') {
                pmStep1.className = 'pm-step pm-step-done';
                pmStep2.className = 'pm-step pm-step-done';
                pmStep3.className = 'pm-step pm-step-active';
                pmLine1.classList.add('pm-line-done');
                pmLine2.classList.add('pm-line-done');
                pmIcon.className    = 'pm-icon pm-icon-done';
                pmIcon.innerHTML    = '<i class="fas fa-check"></i>';
                pmTitle.textContent = 'Tamamlandı!';
                pmSub.textContent   = 'Veriler başarıyla sisteme aktarıldı.';
            }
        }

        const procMessages = [
            "Faturalar tek tek kontrol ediliyor...",
            "Abone bilgileri doğrulanıyor...",
            "Geçmiş verilerle karşılaştırma yapılıyor...",
            "Mükerrer kayıt kontrolü gerçekleştiriliyor...",
            "Finansal tutarlar detaylı inceleniyor...",
            "Veriler sisteme güvenli şekilde işleniyor..."
        ];
        let msgIdx = 0;

        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            sb.disabled = true;
            displayedPct = 0; msgIdx = 0;
            setProgress(0);
            setPhase('upload');
            progModal.classList.add('pm-show');

            const formData = new FormData(this);
            const xhr      = new XMLHttpRequest();
            xhr.open('POST', this.action, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            let pollInterval = null;

            xhr.upload.onprogress = function(ev) {
                if (ev.lengthComputable) {
                    const pct = Math.round((ev.loaded / ev.total) * 60);
                    setProgress(pct);
                    if (pct >= 55 && !pollInterval) {
                        setPhase('process');
                        pollInterval = setInterval(pollProcessing, 1200);
                    }
                }
            };

            function pollProcessing() {
                fetch('{{ route("import.progress") }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const serverPct = Math.min(100, data.yuzde ?? 0);
                        const mapped    = 60 + Math.round(serverPct * 0.38);
                        setProgress(Math.min(98, mapped));
                        pmSub.textContent = procMessages[msgIdx];
                        msgIdx = (msgIdx + 1) % procMessages.length;
                    }
                })
                .catch(() => {});
            }

            function closeProg() {
                progModal.classList.remove('pm-show');
                sb.disabled = false;
            }

            xhr.onload = function() {
                if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            setPhase('done');
                            setProgress(100);
                            setTimeout(() => { closeProg(); location.reload(); }, 1200);
                        } else {
                            closeProg();
                            Swal.fire({ icon: 'error', title: 'Hata', text: data.message || 'Bilinmeyen bir hata oluştu.' });
                        }
                    } catch {
                        closeProg();
                        Swal.fire({ icon: 'error', title: 'Hata', text: 'Bilinmeyen bir sunucu yanıtı alındı.' });
                    }
                } else {
                    closeProg();
                    Swal.fire({ icon: 'error', title: 'Yükleme Hatası', text: 'Sunucu hatası oluştu, lütfen tekrar deneyin.' });
                }
            };

            xhr.onerror = function() {
                if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
                closeProg();
                Swal.fire({ icon: 'error', title: 'Bağlantı Hatası', text: 'İnternet bağlantınızı kontrol edip tekrar deneyin.' });
            };

            xhr.send(formData);
        });

        function deleteImportLog(id, odenenSayisi, stagingSayisi) {
            odenenSayisi = parseInt(odenenSayisi) || 0;
            stagingSayisi = parseInt(stagingSayisi) || 0;

            let title = 'Bu import kaydını silmek istiyor musunuz?';
            let html  = '';
            let icon  = 'warning';

            if (odenenSayisi > 0) {
                icon  = 'error';
                title = '⚠️ Kalıcı Silme Onayı';
                html  = `
                    <div style="text-align:left;font-size:0.9rem;line-height:1.7;">
                        Bu işlem <strong>geri alınamaz!</strong> Aşağıdaki veriler tamamen silinecek:<br><br>
                        ${odenenSayisi > 0 ? `<span style="color:#ef4444;font-weight:700;">🔴 ${odenenSayisi} adet kesinleşmiş fatura</span><br>` : ''}
                        ${stagingSayisi > 0 ? `<span style="color:#f59e0b;font-weight:700;">🟡 ${stagingSayisi} adet bekleyen kayıt (havuz)</span><br>` : ''}
                        <br><span style="color:#64748b;">Ham veriler ve import logu da silinecek.</span>
                    </div>`;
            } else {
                html = `<p style="color:#64748b;">Bu yükleme kaydını ve bağlı tüm ham / havuz verilerini kalıcı olarak sileceksiniz.</p>`;
            }

            Swal.fire({
                title: title,
                html: html,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: odenenSayisi > 0 ? 'Evet, Tümünü Sil!' : 'Evet, Sil!',
                cancelButtonText: 'Vazgeç'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/import/logs/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Silindi!', text: 'Import kaydı ve bağlı tüm veriler başarıyla silindi.', timer: 1500, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Hata', text: data.message || 'Silme işlemi başarısız.' });
                        }
                    }).catch(() => {
                        Swal.fire({ icon: 'error', title: 'Bağlantı Hatası', text: 'Sunucuya ulaşılamadı.' });
                    });
                }
            });
        }
    </script>
    @endpush
@endsection