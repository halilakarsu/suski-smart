@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Disputed Invoices */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --danger-gradient: linear-gradient(135deg, #dc2626, #ef4444);
        --warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
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

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    /* Main Container */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }

    /* KPI Cards */
    .kpi-card-pro {
        background: #fff; border-radius: 24px; padding: 25px; border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 20px; transition: all 0.3s;
    }
    .kpi-card-pro:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
    .kpi-icon-wrap { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }

    /* Table Design */
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
    .table-pro th { color: #94a3b8; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 15px; background: #f8fafc; border: none; }
    .table-pro td { background: #fff; padding: 12px 15px; vertical-align: middle; border: none; font-size: 0.85rem; font-weight: 500; }
    .table-pro tr td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .table-pro tr td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    .table-pro tr:hover td { background: #f8fafc; }

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

    .btn-premium-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff !important;
        box-shadow: 0 12px 24px -6px rgba(16, 185, 129, 0.35);
    }

    .btn-premium-success:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 20px 30px -8px rgba(16, 185, 129, 0.45);
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

    /* Status Badges */
    .status-badge-pro {
        padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">İtiraz Edilen Faturalar</h1>
                <p class="hero-subtitle">Müşteri tarafından itiraz edilmiş ve inceleme aşamasındaki kayıtlar.</p>
            </div>
            <div class="d-flex gap-2">
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- KPI ROW -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="kpi-card-pro">
                    <div class="kpi-icon-wrap" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;"><i class="fas fa-hand-paper"></i></div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $itirazlar->total() }}</div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Toplam İtiraz</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card-pro">
                    <div class="kpi-icon-wrap" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fas fa-clock"></i></div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $itirazlar->getCollection()->where('durum', 'bekliyor')->count() }}</div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">İnceleniyor</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card-pro">
                    <div class="kpi-icon-wrap" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $itirazlar->getCollection()->where('durum', 'kabul_edildi')->count() }}</div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Kabul Edildi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card-pro">
                    <div class="kpi-icon-wrap" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;"><i class="fas fa-lira-sign"></i></div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">₺{{ number_format($itirazlar->getCollection()->sum('genel_toplam'), 0, ',', '.') }}</div>
                        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Toplam Tutar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="glass-card" style="padding: 15px 25px;">
            <form action="{{ route('fatura.itirazlar') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-3">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Dönem</label>
                    <input type="text" name="donem" class="form-control" value="{{ request('donem') }}" style="border-radius: 12px; padding: 10px;" placeholder="2026-03">
                </div>
                <div class="col-md-3">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Tesisat No</label>
                    <input type="text" name="tesisat_no" class="form-control" value="{{ request('tesisat_no') }}" style="border-radius: 12px; padding: 10px;" placeholder="Ara...">
                </div>
                <div class="col-md-2">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block;">Durum</label>
                    <select name="durum" class="form-control" style="border-radius: 12px; padding: 10px;">
                        <option value="">Tümü</option>
                        <option value="bekliyor" {{ request('durum') == 'bekliyor' ? 'selected' : '' }}>İnceleniyor</option>
                        <option value="kabul_edildi" {{ request('durum') == 'kabul_edildi' ? 'selected' : '' }}>Kabul Edildi</option>
                        <option value="reddedildi" {{ request('durum') == 'reddedildi' ? 'selected' : '' }}>Reddedildi</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn-premium btn-premium-primary flex-grow-1"><i class="fas fa-search"></i> Filtrele</button>
                    @if(request()->hasAny(['donem', 'tesisat_no', 'durum']))
                        <a href="{{ route('fatura.itirazlar') }}" class="btn-premium btn-premium-simple"><i class="fas fa-undo"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <!-- MAIN TABLE -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title-pro mb-0"><i class="fas fa-list-alt"></i> İtiraz Kayıtları</h5>
                <span style="font-size: 0.8rem; font-weight: 700; color: #94a3b8;">{{ $itirazlar->total() }} kayıt</span>
            </div>

            <div class="table-responsive">
                <table class="table-pro">
                    <thead>
                        <tr>
                            <th>Dönem</th>
                            <th>Tesisat / Fatura No</th>
                            <th>Bölge</th>
                            <th>İtiraz Nedeni</th>
                            <th>Durum</th>
                            <th style="text-align: right;">Tutar</th>
                            <th style="text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itirazlar as $item)
                            <tr>
                                <td><span style="font-weight: 800; color: #2563eb;">{{ $item->donem }}</span></td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">{{ $item->abone_tesis_no ?? $item->tesisat_no }}</div>
                                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">{{ $item->fatura_no }}</div>
                                </td>
                                <td><span style="font-weight: 600;">{{ $item->ilce }}</span></td>
                                <td style="max-width: 250px;">
                                    <span style="font-size: 0.8rem; color: #64748b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->itiraz_aciklamasi }}">
                                        {{ $item->itiraz_aciklamasi }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->durum == 'bekliyor') <span class="status-badge-pro status-pending"><i class="fas fa-clock"></i> İnceleniyor</span>
                                    @elseif($item->durum == 'kabul_edildi') <span class="status-badge-pro status-approved"><i class="fas fa-check"></i> Kabul</span>
                                    @elseif($item->durum == 'reddedildi') <span class="status-badge-pro status-rejected"><i class="fas fa-times"></i> Reddedildi</span>
                                    @else <span class="status-badge-pro" style="background:#f1f5f9; color:#64748b;">{{ $item->durum }}</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 800; color: #059669;">₺{{ number_format((float)($item->genel_toplam ?? 0), 2, ',', '.') }}</td>
                                <td style="text-align: right;">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn-premium btn-premium-simple" style="padding: 8px 16px; font-size: 0.75rem;" onclick="showDetail({{ $item->id }})"><i class="fas fa-eye"></i> Detay</button>
                                        <button class="btn-premium btn-premium-success" style="padding: 8px 16px; font-size: 0.75rem;" onclick="kaldirItiraz({{ $item->id }}, '{{ $item->fatura_no }}')"><i class="fas fa-undo-alt"></i> Geri Al</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox mb-3" style="font-size: 3rem; color: #e2e8f0; display: block;"></i>
                                    <p style="font-weight: 600; color: #94a3b8;">Henüz itiraz kaydı bulunmuyor.</p>

                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($itirazlar->hasPages())
                <div style="margin-top: 25px;">{{ $itirazlar->links('pagination::bootstrap-4') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- DETAIL MODAL -->
<div id="detMdl" style="position:fixed;inset:0;background:rgba(15,23,42,0.85);z-index:9999;display:none;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(10px);">
    <div style="background:#fff;border-radius:32px;width:100%;max-width:1100px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 80px rgba(0,0,0,0.35);overflow:hidden;">
        <div style="padding:25px 35px;background:var(--primary-gradient);display:flex;justify-content:space-between;align-items:center;color:#fff;">
            <div>
                <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.8;font-weight:800;margin-bottom:4px;">İtiraz Detay Analizi</div>
                <h2 id="detTit" style="margin:0;font-size:1.5rem;font-weight:800;letter-spacing:-.02em;">-</h2>
            </div>
            <button onclick="closeDetail()" style="width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div id="detBdy" style="flex:1;overflow-y:auto;padding:35px;background:#f8fafc;"></div>
    </div>
</div>

<!-- ══ ULTRA PREMIUM GERİ AL MODAL ══ -->
<div id="geriAlModal" style="position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,0.4);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);">
    
    <!-- Animated ambient glows behind the modal -->
    <div style="position:absolute;width:400px;height:400px;background:radial-gradient(circle, rgba(59,130,246,0.3) 0%, transparent 70%);top:50%;left:50%;transform:translate(-80%, -80%);pointer-events:none;filter:blur(40px);animation:pulse-ambient 8s infinite alternate;"></div>
    <div style="position:absolute;width:400px;height:400px;background:radial-gradient(circle, rgba(16,185,129,0.2) 0%, transparent 70%);top:50%;left:50%;transform:translate(-20%, -20%);pointer-events:none;filter:blur(40px);animation:pulse-ambient 10s infinite alternate-reverse;"></div>

    <div style="position:relative;background:rgba(255,255,255,0.85);backdrop-filter:blur(40px);-webkit-backdrop-filter:blur(40px);border-radius:32px;width:100%;max-width:560px;box-shadow:0 50px 100px -20px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,1), inset 0 0 0 1px rgba(255,255,255,0.4);overflow:hidden;animation:modalEnter .5s cubic-bezier(0.16,1,0.3,1);">
        
        {{-- Header Section --}}
        <div style="padding:40px 40px 30px;text-align:center;position:relative;">
            <div style="position:absolute;top:0;left:0;right:0;height:120px;background:linear-gradient(180deg, rgba(248,250,252,0.8) 0%, transparent 100%);pointer-events:none;"></div>
            
            <!-- Icon Container -->
            <div style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:24px;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);box-shadow:0 20px 40px -10px rgba(16,185,129,0.3), inset 0 0 0 1px rgba(255,255,255,0.8);margin-bottom:20px;">
                <div style="position:absolute;inset:0;border-radius:24px;box-shadow:inset 0 2px 4px rgba(255,255,255,0.8);"></div>
                <i class="fas fa-undo-alt" style="font-size:1.6rem;color:#059669;filter:drop-shadow(0 4px 6px rgba(5,150,105,0.2));"></i>
            </div>
            
            <h2 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 8px;letter-spacing:-0.03em;">İtirazı Geri Al</h2>
            <p style="font-size:0.9rem;color:#64748b;margin:0;line-height:1.5;font-weight:500;">
                Bu işlemi onayladığınızda fatura itiraz süreci sonlanacak ve <br><strong>kesinleşmiş faturalar</strong> arasına geri yüklenecektir.
            </p>
        </div>

        {{-- Body Section --}}
        <div style="padding:0 40px;">
            <!-- Target Invoice Chip -->
            <div style="background:linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);border:1px solid #e2e8f0;border-radius:16px;padding:16px 20px;display:flex;align-items:center;gap:16px;margin-bottom:28px;box-shadow:0 4px 12px -4px rgba(0,0,0,0.03);">
                <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#3b82f6;">
                    <i class="fas fa-file-invoice-dollar" style="font-size:1.1rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:2px;">Seçili Fatura</div>
                    <div id="geriAlFaturaNo" style="font-size:0.95rem;font-weight:800;color:#0f172a;font-family:'Plus Jakarta Sans', monospace;letter-spacing:0.02em;"></div>
                </div>
                <div style="margin-left:auto;">
                    <span style="background:rgba(16,185,129,0.1);color:#059669;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:700;border:1px solid rgba(16,185,129,0.2);">İade Edilecek</span>
                </div>
            </div>

            <!-- Input Area -->
            <div style="margin-bottom:30px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">
                    <i class="fas fa-pen-nib" style="color:#94a3b8;"></i> Gerekçe Detayı <span style="color:#ef4444;">*</span>
                </label>
                <div style="position:relative;">
                    <textarea id="geriAlNot" rows="3" placeholder="Bu faturanın itirazını neden geri aldığınızı buraya detaylı olarak yazın..."
                        style="width:100%;background:#fff;border:2px solid #e2e8f0;border-radius:16px;padding:16px 20px;font-size:0.9rem;font-weight:500;color:#1e293b;resize:none;outline:none;transition:all 0.3s cubic-bezier(0.4,0,0.2,1);box-shadow:0 2px 6px rgba(0,0,0,0.02);box-sizing:border-box;"
                        onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 8px 24px -6px rgba(59,130,246,0.15), 0 0 0 4px rgba(59,130,246,0.1)';"
                        onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='0 2px 6px rgba(0,0,0,0.02)';"></textarea>
                </div>
                <div id="geriAlErr" style="display:none;margin-top:10px;font-size:0.8rem;color:#ef4444;font-weight:600;align-items:center;gap:6px;animation:shake 0.4s ease;">
                    <i class="fas fa-exclamation-circle"></i> <span id="geriAlErrTxt"></span>
                </div>
            </div>
        </div>

        {{-- Footer Section --}}
        <div style="background:rgba(248,250,252,0.6);border-top:1px solid rgba(226,232,240,0.8);padding:24px 40px;display:flex;gap:12px;justify-content:flex-end;">
            <button onclick="closeGeriAl()" class="premium-btn premium-btn-cancel">
                Vazgeç
            </button>
            <button id="geriAlConfirmBtn" onclick="geriAlSubmit()" class="premium-btn premium-btn-confirm">
                Onayla ve Geri Yükle
            </button>
        </div>
    </div>
</div>

{{-- Ultra Premium Loading overlay --}}
<div id="geriAlLoading" style="position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;background:rgba(15,23,42,0.5);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);">
    <div style="background:rgba(255,255,255,0.9);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.8);border-radius:28px;padding:50px 60px;text-align:center;box-shadow:0 40px 100px rgba(0,0,0,0.2);">
        <div style="position:relative;width:64px;height:64px;margin:0 auto 24px;">
            <svg viewBox="0 0 100 100" style="width:100%;height:100%;animation:geriAlSpin 1.5s linear infinite;">
                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"></circle>
                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#spinnerGrad)" stroke-width="8" stroke-linecap="round" stroke-dasharray="100 200"></circle>
                <defs>
                    <linearGradient id="spinnerGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6"></stop>
                        <stop offset="100%" stop-color="#10b981"></stop>
                    </linearGradient>
                </defs>
            </svg>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:1.2rem;">
                <i class="fas fa-bolt"></i>
            </div>
        </div>
        <div style="font-weight:800;color:#0f172a;font-size:1.15rem;letter-spacing:-0.02em;">Geri Yükleniyor</div>
        <div style="font-size:0.85rem;color:#64748b;margin-top:8px;font-weight:500;">Fatura güvenle aktarılıyor, lütfen bekleyin...</div>
    </div>
</div>

<style>
/* Animations */
@keyframes modalEnter {
    0% { opacity: 0; transform: scale(0.95) translateY(20px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes pulse-ambient {
    0% { opacity: 0.5; transform: translate(-50%, -50%) scale(0.9); }
    100% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
@keyframes geriAlSpin { to { transform:rotate(360deg); } }

/* Premium Buttons */
.premium-btn {
    padding: 14px 28px;
    border-radius: 14px;
    font-size: 0.9rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.premium-btn-cancel {
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.premium-btn-cancel:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}
.premium-btn-confirm {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    border: none;
    box-shadow: 0 10px 20px -5px rgba(16,185,129,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
}
.premium-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 25px -5px rgba(16,185,129,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
}
.premium-btn-confirm:active {
    transform: translateY(0);
    box-shadow: 0 5px 10px -5px rgba(16,185,129,0.4);
}
</style>

@push('scripts')
<script>
    const rData = {!! $itirazlar instanceof \Illuminate\Pagination\LengthAwarePaginator ? $itirazlar->keyBy('id')->toJson() : '{}' !!};

    function showDetail(id) {
        const d = rData[id];
        if (!d) return;

        const formatDate = (dateStr) => {
            if (!dateStr || dateStr === '—') return '—';
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

        document.getElementById('detTit').innerText = d.fatura_no + ' (' + (d.abone_tesis_no || d.tesisat_no) + ')';
        let h = '';

        // İtiraz Açıklaması
        h += `<div style="margin-bottom:25px;">
                <h4 style="font-size:0.8rem; font-weight:800; color:#b45309; margin-bottom:12px; text-transform:uppercase;"><i class="fas fa-comment-dots"></i> İtiraz Gerekçesi</h4>
                <div style="background:#fffbeb; border-left:5px solid #f59e0b; padding:20px; border-radius:16px; font-size:0.9rem; color:#78350f; line-height:1.6; font-weight:600;">
                    ${d.itiraz_aciklamasi || 'Açıklama girilmemiş.'}
                </div>
              </div>`;

        // Anomali Analiz Raporu (Eğer varsa)
        let pld = d.payload;
        if (typeof pld === 'string') { try { pld = JSON.parse(pld); } catch(e) { pld = {}; } }
        const anomaliler = pld && pld._tuketim_anomalileri ? pld._tuketim_anomalileri : [];
        
        if (anomaliler.length > 0) {
            h += `<div style="margin-bottom: 25px;">
                    <div style="background: linear-gradient(135deg, #fff1f2 0%, #fff 100%); border-radius: 24px; border: 1px solid #fecdd3; padding: 25px; box-shadow: 0 10px 30px rgba(225, 29, 72, 0.05);">
                        <h4 style="font-size: 0.85rem; font-weight: 800; color: #be123c; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-microscope" style="font-size: 1.1rem;"></i> SİSTEMSEL ANOMALİ TESPİT RAPORU
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 12px;">`;
            
            anomaliler.forEach(ano => {
                let kod = typeof ano === 'object' ? (ano.kod || '') : ano;
                let mesaj = typeof ano === 'object' ? (ano.mesaj || '') : ano;
                let detay = typeof ano === 'object' ? (ano.detay || 'Sistem tarafından teknik bir sapma tespit edildi.') : 'Tespit edildi.';
                
                let icon = 'fa-exclamation-triangle', color = '#e11d48';
                if(kod === 'negatif_tuketim') icon = 'fa-arrow-down';
                else if(kod === 'anormal_tuketim') icon = 'fa-chart-line';
                else if(kod === 'reaktif_ceza') icon = 'fa-bolt';

                h += `<div style="background: #fff; border-radius: 16px; padding: 15px; border: 1px solid rgba(225, 29, 72, 0.1); display: flex; gap: 15px;">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: ${color}15; color: ${color}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem;">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 800; color: #0f172a; font-size: 0.85rem; margin-bottom: 4px;">${mesaj}</div>
                            <div style="font-size: 0.8rem; color: #475569; line-height: 1.5; font-weight: 500; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border: 1px solid #f1f5f9;">
                                <strong>Analiz Notu:</strong> ${detay}
                            </div>
                        </div>
                      </div>`;
            });
            h += `</div></div></div>`;
        }

        // Detay Grid
        h += '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">';
        const sections = {
            'Tesisat Bilgileri': { tesisat_no: 'Tesisat No', ilce: 'İlçe', donem: 'Dönem', dagitim: 'Kurum' },
            'Tüketim Verileri': { t1_tuketim: 'T1 Tüketim', t2_tuketim: 'T2 Tüketim', t3_tuketim: 'T3 Tüketim', trafo_kaybi_kwh: 'Trafo Kaybı', fatura_edilecek_toplam_tuketim_kwh: 'Toplam Tüketim' },
            'Finansal Veriler': { birim_fiyat: 'Birim Fiyat', reaktif_tl: 'Reaktif Ceza', kdv: 'KDV', genel_toplam: 'Genel Toplam' }
        };

        Object.entries(sections).forEach(([title, fields]) => {
            h += `<div style="background:#fff; border-radius:20px; border:1px solid #f1f5f9; overflow:hidden;">
                    <div style="background:#f8fafc; padding:12px 20px; font-size:0.7rem; font-weight:800; color:#64748b; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">${title}</div>
                    <div style="padding:10px 0;">`;
            Object.entries(fields).forEach(([f, lbl]) => {
                let val = d[f] || '—';

                if (['tutar', 'tl', 'toplam', 'fiyat', 'kdv'].some(x => f.includes(x)) && val !== '—') {
                    val = '₺' + parseFloat(val).toLocaleString('tr-TR', {minimumFractionDigits:2});
                }
                else if (['tarih', 'okuma', 'son_odeme'].some(x => f.includes(x)) && val !== '—') {
                    val = formatDate(val);
                }

                h += `<div class="d-flex justify-content-between px-3 py-2 border-bottom-dashed" style="font-size:0.85rem;">
                        <span style="color:#94a3b8; font-weight:600;">${lbl}</span>
                        <span style="color:#1e293b; font-weight:700;">${val}</span>
                      </div>`;
            });
            h += `</div></div>`;
        });
        h += '</div>';

        document.getElementById('detBdy').innerHTML = h;
        document.getElementById('detMdl').style.display = 'flex';
    }

    function closeDetail() { document.getElementById('detMdl').style.display = 'none'; }

    // ── Geri Al Premium Modal ──────────────────────────
    let _geriAlId = null;

    function kaldirItiraz(id, faturaNo) {
        _geriAlId = id;
        document.getElementById('geriAlFaturaNo').textContent = faturaNo;
        document.getElementById('geriAlNot').value = '';
        document.getElementById('geriAlErr').style.display = 'none';
        document.getElementById('geriAlModal').style.display = 'flex';
        setTimeout(() => document.getElementById('geriAlNot').focus(), 100);
    }

    function closeGeriAl() {
        document.getElementById('geriAlModal').style.display = 'none';
        _geriAlId = null;
    }

    function geriAlSubmit() {
        const note = document.getElementById('geriAlNot').value.trim();
        const errDiv = document.getElementById('geriAlErr');
        const errTxt = document.getElementById('geriAlErrTxt');

        if (!note || note.length < 3) {
            errTxt.textContent = 'Lütfen en az 3 karakter bir gerekçe girin.';
            errDiv.style.display = 'flex';
            document.getElementById('geriAlNot').focus();
            return;
        }
        errDiv.style.display = 'none';

        document.getElementById('geriAlModal').style.display = 'none';
        document.getElementById('geriAlLoading').style.display = 'flex';

        fetch(`{{ url('fatura/itiraz-kaldir') }}/${_geriAlId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ sonuc_notu: note })
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('geriAlLoading').style.display = 'none';
            if (data.success) {
                // Başarı toast
                const toast = document.createElement('div');
                toast.innerHTML = `<div style="position:fixed;bottom:30px;right:30px;z-index:10002;background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:16px 24px;border-radius:16px;box-shadow:0 16px 40px rgba(16,185,129,0.4);display:flex;align-items:center;gap:12px;font-family:inherit;animation:geriAlSlideIn .4s cubic-bezier(.16,1,.3,1);">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;"><i class="fas fa-check" style="font-size:1rem;"></i></div>
                    <div><div style="font-weight:800;font-size:0.9rem;">İşlem Başarılı</div><div style="font-size:0.76rem;opacity:.85;margin-top:2px;">${data.message || 'Fatura geri yüklendi.'}</div></div>
                </div>`;
                document.body.appendChild(toast);
                setTimeout(() => window.location.reload(), 1800);
            } else {
                showGeriAlError(data.message || 'İşlem sırasında bir hata oluştu.');
            }
        })
        .catch(() => showGeriAlError('Sunucuya ulaşılamadı. Lütfen tekrar deneyin.'));
    }

    function showGeriAlError(msg) {
        document.getElementById('geriAlLoading').style.display = 'none';
        document.getElementById('geriAlModal').style.display = 'flex';
        const errDiv = document.getElementById('geriAlErr');
        document.getElementById('geriAlErrTxt').textContent = msg;
        errDiv.style.display = 'flex';
    }

    // Dışarı tıklayınca kapat
    document.getElementById('geriAlModal').addEventListener('click', function(e) {
        if (e.target === this) closeGeriAl();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeGeriAl();
    });

    window.onclick = function(e) { if (e.target == document.getElementById('detMdl')) closeDetail(); }
</script>
@endpush
@endsection