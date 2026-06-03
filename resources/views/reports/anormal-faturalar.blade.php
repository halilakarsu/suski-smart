@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root { --font-primary:'Plus Jakarta Sans',sans-serif; --primary-gradient:linear-gradient(135deg,#2563eb,#4f46e5); --bg-main:#f4f6f9; --surface-glass:rgba(255,255,255,.9); --text-900:#0f172a; --text-500:#64748b; --shadow:0 20px 40px -10px rgba(0,0,0,.08); }
    * { font-family:var(--font-primary); }
    .pg-premium { background:var(--bg-main)!important; min-height:100vh; padding-bottom:4rem; margin-top:-70px!important; }
    .page-hero { background:linear-gradient(125deg,#0f172a 0%,#1e1b4b 100%); position:relative; padding:5rem 2rem 10rem; margin-top:-30px!important; color:#fff; overflow:hidden; border-bottom-left-radius:40px; border-bottom-right-radius:40px; box-shadow:0 20px 50px rgba(0,0,0,.15); }
    .page-hero::before { content:''; position:absolute; width:600px; height:600px; background:radial-gradient(circle,rgba(59,130,246,.3) 0%,transparent 70%); top:-200px; left:-150px; border-radius:50%; opacity:.6; filter:blur(60px); }
    .hero-container { position:relative; z-index:10; width:100%; max-width:1400px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; }
    .hero-title-group h1 { font-size:2.5rem; font-weight:800; background:linear-gradient(to right,#fff,#93c5fd); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:.5rem; }
    .hero-subtitle { color:#94a3b8; font-size:1.1rem; font-weight:500; }
    .main-container { width:100%; max-width:1400px; margin:-5rem auto 0; padding:0 2rem; position:relative; z-index:20; }
    .glass-card { background:var(--surface-glass); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,.7); border-radius:28px; padding:30px; box-shadow:var(--shadow); margin-bottom:30px; overflow:visible; }
    .filter-card { position:relative; z-index:1000!important; overflow:visible!important; }
    .section-title { font-size:1.1rem; font-weight:800; color:var(--text-900); margin-bottom:25px; display:flex; align-items:center; gap:12px; }
    .section-title i { padding:10px; background:#eff6ff; border-radius:12px; color:#3b82f6; }
    .form-group-pro label { display:block; font-size:.85rem; font-weight:700; color:var(--text-900); margin-bottom:8px; text-transform:uppercase; letter-spacing:.03em; }
    .form-control-pro { width:100%; padding:12px 16px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; font-size:.95rem; color:var(--text-900); font-weight:500; outline:none; transition:all .2s; }
    .form-control-pro:focus { border-color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,.1); }
    select.form-control-pro { appearance:none; background-image:url('data:image/svg+xml;charset=US-ASCII,<svg width="12" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L13 1" stroke="%2394a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'); background-repeat:no-repeat; background-position:right 16px center; background-size:10px; padding-right:40px; }
    .btn-pro { padding:12px 24px; border-radius:14px; font-weight:700; font-size:.9rem; display:inline-flex; align-items:center; gap:8px; transition:all .3s; border:none; cursor:pointer; text-decoration:none!important; }
    .btn-primary-pro { background:var(--primary-gradient); color:#fff!important; box-shadow:0 10px 20px -5px rgba(37,99,235,.3); }
    .btn-outline-pro { background:#fff; border:1px solid #e2e8f0; color:var(--text-500); }
    .btn-advanced-pro { position:relative; padding:12px 18px; border-radius:14px; font-weight:700; font-size:.9rem; display:inline-flex; align-items:center; justify-content:center; gap:8px; transition:all .3s; border:1.5px solid #c7d2fe; cursor:pointer; background:linear-gradient(135deg,#eff6ff,#f5f3ff); color:#4f46e5; box-shadow:0 4px 12px rgba(79,70,229,.1); }
    .adv-active-dot { width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block; }
    .adv-badge { display:inline-flex; align-items:center; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:20px; font-size:.75rem; font-weight:700; padding:3px 10px; }
    .custom-multi-select { position:relative; width:100%; z-index:1000; }
    .custom-multi-select .dropdown-toggle { text-align:left; background:#fff; border:1.5px solid #e2e8f0; padding:12px 16px; border-radius:12px; font-size:.92rem; color:var(--text-900); display:flex; justify-content:space-between; align-items:center; width:100%; font-weight:500; }
    .custom-multi-select .dropdown-toggle::after { display:none!important; }
    .custom-multi-select .dropdown-menu { width:100%; border-radius:16px; border:1.5px solid #e2e8f0; box-shadow:0 20px 40px rgba(0,0,0,.15); padding:10px; max-height:260px; overflow-y:auto; margin-top:6px; background:#fff; z-index:99999!important; position:absolute!important; }
    .custom-multi-select .form-check { padding:7px 10px; margin-bottom:1px; border-radius:9px; display:flex; align-items:center; gap:10px; cursor:pointer; }
    .custom-multi-select .form-check:hover, .custom-multi-select .form-check.checked-row { background:#eff6ff; }
    .custom-multi-select .form-check-input { position:absolute; opacity:0; width:0; height:0; pointer-events:none; }
    .cb-box { width:20px; height:20px; min-width:20px; border-radius:5px; border:2px solid #cbd5e1; background:#fff; display:flex; align-items:center; justify-content:center; }
    .cb-box svg { width:11px; height:11px; stroke:#fff; stroke-width:3; stroke-linecap:round; stroke-linejoin:round; fill:none; opacity:0; }
    .form-check-input:checked ~ .cb-box { background:#2563eb; border-color:#2563eb; }
    .form-check-input:checked ~ .cb-box svg { opacity:1; }
    .select-all-wrap { border-bottom:1.5px solid #e2e8f0; margin-bottom:6px; padding-bottom:6px; }
    .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:24px; margin-bottom:30px; }
    .stat-box { background:#fff; border-radius:24px; padding:24px; display:flex; align-items:center; gap:18px; border:1px solid #f1f5f9; box-shadow:0 10px 25px -5px rgba(0,0,0,.05); }
    .stat-icon { width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
    .stat-icon.purple { background:#f5f3ff; color:#7c3aed; } .stat-icon.blue { background:#eff6ff; color:#2563eb; } .stat-icon.green { background:#f0fdf4; color:#16a34a; }
    .stat-val { font-size:1.4rem; font-weight:800; color:#0f172a; line-height:1.2; }
    .stat-lbl { font-size:.8rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-top:2px; }
    .tbl-wrap { overflow-x:auto; border-radius:20px; background:#fff; }
    .tbl { width:100%; min-width:1100px; border-collapse:separate; border-spacing:0; }
    .tbl th { background:#f8fafc; padding:16px 20px; font-size:.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e2e8f0; }
    .tbl td { padding:16px 20px; font-size:.9rem; color:#1e293b; border-bottom:1px solid #f1f5f9; background:#fff; }
    .badge-anomali { display:inline-flex; align-items:center; gap:5px; background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:999px; padding:4px 9px; font-size:.72rem; font-weight:800; margin:2px; }
    @media (max-width:768px) { .page-hero{padding:3rem 1rem 6rem;} .hero-container{flex-direction:column;align-items:flex-start;gap:20px;} .main-container{padding:0 1rem;margin-top:-4rem;} .glass-card{padding:20px;} .modal-dialog{max-width:95%!important;margin:10px auto;} }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Anormal Faturalar</h1>
                <p class="hero-subtitle">Anomali analizinde kaydedilen hatalı faturaları listeleyin ve takip edin.</p>
            </div>
            <button type="button" class="btn-advanced-pro" data-toggle="modal" data-target="#anormalAdvModal" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.3);box-shadow:none;">
                <i class="fas fa-sliders-h"></i> Detaylı Filtre
                @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','fatura_no','yerlesim_tipi','end_period']))<span class="adv-active-dot" style="background:#fca5a5;"></span>@endif
            </button>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card filter-card">
            <h5 class="section-title"><i class="fas fa-filter"></i> Anormal Fatura Filtreleri</h5>

            @if(request()->anyFilled(['tarife','baglanti_grubu','tesisat_no','fatura_no','yerlesim_tipi']))
                <div style="margin-bottom:16px;padding:10px 16px;background:linear-gradient(135deg,rgba(37,99,235,.07),rgba(79,70,229,.07));border:1.5px solid rgba(37,99,235,.2);border-radius:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <i class="fas fa-sliders-h" style="color:#2563eb;"></i>
                    <span style="font-size:.83rem;font-weight:700;color:#1d4ed8;">Aktif Filtreler:</span>
                    @if(request('tarife')) <span class="adv-badge">Tarife: {{ count((array)request('tarife')) }} seçili</span> @endif
                    @if(request('baglanti_grubu')) <span class="adv-badge">{{ request('baglanti_grubu') }}</span> @endif
                    @if(request('yerlesim_tipi')) <span class="adv-badge">{{ ucfirst(request('yerlesim_tipi')) }}</span> @endif
                    @if(request('tesisat_no')) <span class="adv-badge">Tesisat: {{ request('tesisat_no') }}</span> @endif
                    @if(request('fatura_no')) <span class="adv-badge">Fatura: {{ request('fatura_no') }}</span> @endif
                </div>
            @endif

            <form action="{{ route('reports.anormal-faturalar') }}" method="GET" id="anormalFilterForm">
                <div id="anormalAdvHidden"></div>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="fas fa-map-marker-alt me-2"></i> Bölge Seçimi</label>
                            <div class="dropdown custom-multi-select">
                                <button class="dropdown-toggle" type="button" id="AnormalHeroBolgeDropdown" data-toggle="dropdown" style="height:47px;">
                                    <span id="AnormalHeroBolgeLabel">Bölge Seçin...</span>
                                    <i class="fas fa-chevron-down" style="font-size:.75rem;color:#94a3b8;"></i>
                                </button>
                                <div class="dropdown-menu" onclick="event.stopPropagation();">
                                    <div class="form-check select-all-wrap" id="selectAllAnormalHeroBolgeRow">
                                        <input class="form-check-input" type="checkbox" id="selectAllAnormalHeroBolge">
                                        <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                        <label class="form-check-label fw-bold" for="selectAllAnormalHeroBolge">Tümünü Seç</label>
                                    </div>
                                    @foreach($bolgeler as $bolge)
                                        <div class="form-check anormal-hero-bolge-row" onclick="toggleCheckbox(this)">
                                            <input class="form-check-input anormal-hero-bolge-cb" type="checkbox" name="bolge[]" value="{{ $bolge }}" id="anormalherobolge_{{ $loop->index }}" {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($bolge, request('bolge')))) ? 'checked' : '' }}>
                                            <span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span>
                                            <label class="form-check-label" for="anormalherobolge_{{ $loop->index }}">{{ $bolge }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-pro" style="margin-bottom:0;">
                            <label><i class="far fa-calendar-alt me-2"></i> Başlangıç Dönemi</label>
                            <select name="start_period" id="hero_start_period" class="form-control-pro" style="height:47px;">
                                <option value="">Tümü</option>
                                @foreach($donemler as $donem)
                                    <option value="{{ $donem }}" {{ request('start_period') == $donem ? 'selected' : '' }}>{{ $donem }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn-pro btn-primary-pro w-100 justify-content-center" style="height:47px;font-weight:800;"><i class="fas fa-search"></i> Sonuçları Getir</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <div class="stats-row">
                <div class="stat-box"><div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div><div><div class="stat-val">{{ number_format($totals->total_fatura, 0, ',', '.') }}</div><div class="stat-lbl">Kayıtlı Anormal Fatura</div></div></div>
                <div class="stat-box"><div class="stat-icon blue"><i class="fas fa-bolt"></i></div><div><div class="stat-val">{{ number_format($totals->total_tuketim, 2, ',', '.') }}</div><div class="stat-lbl">Toplam Tüketim (kWh)</div></div></div>
                <div class="stat-box"><div class="stat-icon green"><i class="fas fa-lira-sign"></i></div><div><div class="stat-val">{{ number_format($totals->total_tutar, 2, ',', '.') }}</div><div class="stat-lbl">Toplam Tutar</div></div></div>
            </div>

            @if($results->count() > 0)
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Dönem</th><th>Bölge</th><th>Tesisat No</th><th>Fatura No</th><th>Anomali Detayı</th><th style="text-align:right;">Tüketim</th><th style="text-align:right;">Tutar</th><th>Kaydeden</th><th>Kayıt Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $row)
                                <tr>
                                    <td><span class="adv-badge">{{ $row->donem }}</span></td>
                                    <td style="font-weight:700;color:#2563eb;">{{ $row->ilce ?: '-' }}</td>
                                    <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-weight:700;color:#1e293b;">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</code></td>
                                    <td style="font-weight:700;">{{ $row->fatura_no }}</td>
                                    <td>
                                        @forelse((array)$row->anomali_payload as $anomali)
                                            @php $mesaj = is_array($anomali) ? ($anomali['mesaj'] ?? ($anomali['kod'] ?? 'Anomali')) : $anomali; @endphp
                                            <span class="badge-anomali"><i class="fas fa-exclamation-triangle"></i> {{ $mesaj }}</span>
                                        @empty
                                            <span class="badge-anomali"><i class="fas fa-exclamation-triangle"></i> Anomali</span>
                                        @endforelse
                                        @if($row->islem_notu)
                                            <div style="font-size:.78rem;color:#64748b;margin-top:6px;">Not: {{ $row->islem_notu }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align:right;font-weight:800;">{{ number_format((float)$row->fatura_edilecek_toplam_tuketim_kwh, 2, ',', '.') }} kWh</td>
                                    <td style="text-align:right;font-weight:800;color:#059669;">₺{{ number_format((float)$row->tutar_toplam, 2, ',', '.') }}</td>
                                    <td>{{ $row->user->name ?? '-' }}</td>
                                    <td>{{ $row->created_at?->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 d-flex justify-content-center">{{ $results->links('pagination::bootstrap-4') }}</div>
            @else
                <div style="text-align:center;padding:55px 30px;color:#64748b;">
                    <i class="fas fa-inbox fa-3x" style="color:#cbd5e1;margin-bottom:15px;display:block;"></i>
                    Kayıtlı anormal fatura bulunamadı.
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="anormalAdvModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter:blur(8px);background:rgba(15,23,42,.4);">
    <div class="modal-dialog modal-dialog-centered" style="max-width:65%;" role="document">
        <div class="modal-content" style="border-radius:28px;border:1px solid rgba(255,255,255,.2);overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.25);background:rgba(255,255,255,.95);backdrop-filter:blur(20px);">
            <div class="modal-header" style="background:linear-gradient(135deg,rgba(15,23,42,.95),rgba(30,27,75,.95));border:none;padding:30px 35px;">
                <div>
                    <h5 class="modal-title" style="color:#fff;font-weight:800;font-size:1.35rem;margin:0;"><span style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(96,165,250,.2);border-radius:12px;margin-right:12px;color:#60a5fa;"><i class="fas fa-sliders-h"></i></span>Gelişmiş Filtreleme</h5>
                    <p style="color:#94a3b8;font-size:.85rem;margin:8px 0 0 50px;font-weight:500;">Kayıtlı anormal faturaları daha spesifik kriterlere göre daraltın.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;font-size:1.6rem;background:rgba(255,255,255,.1);border:none;cursor:pointer;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><span aria-hidden="true" style="margin-top:-2px;">&times;</span></button>
            </div>
            <div class="modal-body" style="padding:45px 35px;">
                <div class="row">
                    <div class="col-md-6" style="margin-bottom:25px;">
                        <label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-map-marker-alt" style="color:#3b82f6;margin-right:6px;"></i> Bölgeler</label>
                        <div class="dropdown custom-multi-select">
                            <button class="dropdown-toggle" type="button" id="ModalAnormalBolgeDropdown" data-toggle="dropdown" style="padding:12px 18px;font-size:.95rem;border-radius:12px;"><span id="ModalAnormalBolgeLabel">Bölge Seçin...</span><i class="fas fa-chevron-down" style="font-size:.8rem;color:#94a3b8;"></i></button>
                            <div class="dropdown-menu" onclick="event.stopPropagation();">
                                <div class="form-check select-all-wrap" id="selectAllModalAnormalBolgeRow"><input class="form-check-input" type="checkbox" id="selectAllModalAnormalBolge"><span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span><label class="form-check-label fw-bold" for="selectAllModalAnormalBolge">Tümünü Seç</label></div>
                                @foreach($bolgeler as $bolge)
                                    <div class="form-check modal-anormal-bolge-row" onclick="toggleCheckbox(this)"><input class="form-check-input modal-anormal-bolge-cb" type="checkbox" value="{{ $bolge }}" id="modalanormalbolge_{{ $loop->index }}" {{ (!request()->has('bolge') || (is_array(request('bolge')) && in_array($bolge, request('bolge')))) ? 'checked' : '' }}><span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span><label class="form-check-label" for="modalanormalbolge_{{ $loop->index }}">{{ $bolge }}</label></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-bottom:25px;"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-hashtag" style="color:#ea580c;margin-right:6px;"></i> Tesisat No</label><input type="text" id="anormal_tesisat" class="form-control-pro" value="{{ request('tesisat_no') }}" placeholder="Örn: 123456" style="height:47px;font-family:monospace;"></div>
                    <div class="col-md-3" style="margin-bottom:25px;"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-file-invoice" style="color:#dc2626;margin-right:6px;"></i> Fatura No</label><input type="text" id="anormal_fatura" class="form-control-pro" value="{{ request('fatura_no') }}" placeholder="Örn: ABC123" style="height:47px;"></div>
                </div>
                <div class="row" style="background:rgba(241,245,249,.5);padding:15px;border-radius:16px;margin-bottom:25px;">
                    <div class="col-md-6"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="far fa-calendar-alt" style="color:#64748b;margin-right:6px;"></i> Başlangıç Dönemi</label><select id="modal_start_period" class="form-control-pro" style="height:47px;"><option value="">Tümü</option>@foreach($donemler as $donem)<option value="{{ $donem }}" {{ request('start_period') == $donem ? 'selected' : '' }}>{{ $donem }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="far fa-calendar-check" style="color:#64748b;margin-right:6px;"></i> Bitiş Dönemi</label><select id="modal_end_period" class="form-control-pro" style="height:47px;"><option value="">Tümü</option>@foreach($donemler as $donem)<option value="{{ $donem }}" {{ request('end_period') == $donem ? 'selected' : '' }}>{{ $donem }}</option>@endforeach</select></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-plug" style="color:#059669;margin-right:6px;"></i> Bağlantı Grubu</label><select id="anormal_baglanti" class="form-control-pro" style="height:47px;"><option value="">Tümü</option><option value="AG" {{ request('baglanti_grubu')=='AG'?'selected':'' }}>AG</option><option value="OG" {{ request('baglanti_grubu')=='OG'?'selected':'' }}>OG</option></select></div>
                    <div class="col-md-4"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-city" style="color:#9333ea;margin-right:6px;"></i> Yerleşim Türü</label><select id="anormal_yerlesim" class="form-control-pro" style="height:47px;"><option value="">Tümü</option><option value="merkez" {{ request('yerlesim_tipi')=='merkez'?'selected':'' }}>Merkez</option><option value="koy" {{ request('yerlesim_tipi')=='koy'?'selected':'' }}>Köy</option></select></div>
                    <div class="col-md-4"><label style="display:block;font-size:.82rem;font-weight:800;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;"><i class="fas fa-tags" style="color:#dc2626;margin-right:6px;"></i> Tarife</label><div class="dropdown custom-multi-select"><button class="dropdown-toggle" type="button" id="AnormalTarifeDropdown" data-toggle="dropdown" style="height:47px;"><span id="AnormalTarifeLabel">Tarife Seçin...</span><i class="fas fa-chevron-down" style="font-size:.8rem;color:#94a3b8;"></i></button><div class="dropdown-menu" onclick="event.stopPropagation();"><div class="form-check select-all-wrap" id="selectAllAnormalTarifeRow"><input class="form-check-input" type="checkbox" id="selectAllAnormalTarife"><span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span><label class="form-check-label fw-bold" for="selectAllAnormalTarife">Tümünü Seç</label></div>@foreach($tarifeler as $t)<div class="form-check anormal-tarife-row" onclick="toggleCheckbox(this)"><input class="form-check-input anormal-tarife-cb" type="checkbox" value="{{ $t->tarife }}" id="anormaltarife_{{ $loop->index }}" {{ (!request()->has('tarife') || (is_array(request('tarife')) && in_array($t->tarife, request('tarife')))) ? 'checked' : '' }}><span class="cb-box"><svg viewBox="0 0 12 10"><polyline points="1,5 4,9 11,1"/></svg></span><label class="form-check-label" for="anormaltarife_{{ $loop->index }}">{{ $t->abone_grubu ?: $t->tarife }}</label></div>@endforeach</div></div></div>
                </div>
            </div>
            <div class="modal-footer" style="background:rgba(248,250,252,.8);border-top:1px solid rgba(226,232,240,.8);padding:25px 35px;display:flex;justify-content:space-between;">
                <button type="button" class="btn-pro btn-outline-pro" id="anormalClearBtn"><i class="fas fa-eraser"></i> Filtreleri Temizle</button>
                <button type="button" class="btn-pro btn-primary-pro" id="anormalApplyBtn"><i class="fas fa-check"></i> Sonuçları Getir</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
            $sa.prop('checked',n===$cbs.length && n>0);
            $cbs.each(function(){$(this).closest('.form-check').toggleClass('checked-row',$(this).is(':checked'));});
        }
        if($saRow.length){$saRow.on('click',function(e){if(e.target.tagName!=='INPUT' && e.target.tagName!=='LABEL')$sa.prop('checked',!$sa.prop('checked')).trigger('change');});}
        $sa.on('change',function(){ $cbs.prop('checked',$(this).is(':checked')).trigger('change'); upLbl(); });
        $cbs.on('change',function(){upLbl();});
        upLbl();
    }

    initMS('selectAllAnormalHeroBolge','anormal-hero-bolge-cb','AnormalHeroBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllModalAnormalBolge','modal-anormal-bolge-cb','ModalAnormalBolgeLabel','Bölge Seçin...','Tüm Bölgeler Seçili','Bölge Seçili');
    initMS('selectAllAnormalTarife','anormal-tarife-cb','AnormalTarifeLabel','Tarife Seçin...','Tüm Tarifeler Seçili','Tarife Seçili');

    let syncing = false;
    $('.anormal-hero-bolge-cb').on('change', function(){ if(syncing) return; syncing=true; const val=$(this).val(), checked=$(this).is(':checked'); const $target=$(`.modal-anormal-bolge-cb[value="${val}"]`); if($target.is(':checked')!==checked)$target.prop('checked',checked).trigger('change'); syncing=false; });
    $('.modal-anormal-bolge-cb').on('change', function(){ if(syncing) return; syncing=true; const val=$(this).val(), checked=$(this).is(':checked'); const $target=$(`.anormal-hero-bolge-cb[value="${val}"]`); if($target.is(':checked')!==checked)$target.prop('checked',checked).trigger('change'); syncing=false; });
    $('#hero_start_period').on('change', function(){ $('#modal_start_period').val($(this).val()); });

    $('#anormalClearBtn').on('click', function() {
        $('.modal-anormal-bolge-cb,.anormal-hero-bolge-cb,.anormal-tarife-cb').prop('checked', false).trigger('change');
        $('#anormal_tesisat,#anormal_fatura').val('');
        $('#modal_start_period,#modal_end_period,#hero_start_period,#anormal_baglanti,#anormal_yerlesim').val('');
    });

    $('#anormalApplyBtn').on('click', function() {
        const $h = $('#anormalAdvHidden');
        $h.empty();
        $('.modal-anormal-bolge-cb:checked').each(function(){ $h.append($('<input type="hidden" name="bolge[]">').val($(this).val())); });
        $('.anormal-tarife-cb:checked').each(function(){ $h.append($('<input type="hidden" name="tarife[]">').val($(this).val())); });
        const fields = {
            tesisat_no: $('#anormal_tesisat').val(),
            fatura_no: $('#anormal_fatura').val(),
            start_period: $('#modal_start_period').val() || $('#hero_start_period').val(),
            end_period: $('#modal_end_period').val(),
            baglanti_grubu: $('#anormal_baglanti').val(),
            yerlesim_tipi: $('#anormal_yerlesim').val()
        };
        Object.keys(fields).forEach(function(name){ if(fields[name]) $h.append($('<input type="hidden">').attr('name', name).val(fields[name])); });
        $('#anormalAdvModal').modal('hide');
        $('#anormalFilterForm').submit();
    });
});
</script>
@endpush
@endsection
